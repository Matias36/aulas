<?php
require_once __DIR__ . '/no_cache.php';
// Funciones del panel de administrador. Todos los api/admin_*.php pasan por acá.
require_once __DIR__ . '/sesion_lib.php';
require_once __DIR__ . '/lib_horarios.php';
require_once __DIR__ . '/lib_notificaciones.php';

const DIAS_LARGO = ['', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'];

// Solo entra un administrador con sesión abierta (se verifica acá, en el servidor)
function exigir_admin($pdo) {
    $u = usuario_actual($pdo);
    if (!$u) responder_error(401, 'Sin sesión.');
    if ($u['rol'] !== 'administrador') responder_error(403, 'No tenés permiso para esto.');
    return $u;
}

function cortar($s, $n) {
    return preg_match('/^.{0,' . (int)$n . '}/su', (string)$s, $m) ? $m[0] : '';
}

function hora_valida($s) { return is_string($s) && preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $s) === 1; }
function fecha_valida($s) {
    if (!is_string($s) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) return false;
    $d = DateTime::createFromFormat('Y-m-d', $s);
    return $d !== false && $d->format('Y-m-d') === $s;
}
function dia_de_fecha($f) { $d = new DateTime($f); return (int)$d->format('N'); }
function fecha_corta($f) { return substr($f, 8, 2) . '/' . substr($f, 5, 2); }
function fecha_larga($f) { return DIAS_LARGO[dia_de_fecha($f)] . ' ' . fecha_corta($f); }
function enlace_aula($aulaId, $edificioId, $dia) {
    return 'aula.html?id=' . rawurlencode($aulaId) . '&e=' . rawurlencode($edificioId) . '&d=' . (int)$dia;
}

function ids_pedidos($in) {
    $ids = [];
    if (isset($in['ids']) && is_array($in['ids'])) {
        foreach ($in['ids'] as $i) { $n = (int)$i; if ($n > 0) $ids[$n] = true; }
    }
    $ids = array_keys($ids);
    if (!$ids || count($ids) > 30) responder_error(400, 'Datos inválidos.');
    return $ids;
}

// La universidad abre de 07:00 a 23:00 (sábados hasta las 17:00, domingos cerrada)
function validar_franja($dia, $ini, $fin) {
    if ($dia < 1 || $dia > 6) responder_error(400, 'El día tiene que ser de lunes a sábado.');
    if (!hora_valida($ini) || !hora_valida($fin)) responder_error(400, 'Revisá las horas.');
    if ($fin <= $ini) responder_error(400, 'La hora de fin tiene que ser posterior a la de inicio.');
    $cierre = ($dia === 6) ? '17:00' : '23:00';
    if ($ini < '07:00' || $fin > $cierre) responder_error(400, 'Ese día la universidad abre de 07:00 a ' . $cierre . '.');
}

function aula_datos($pdo, $id) {
    $st = $pdo->prepare('SELECT a.id, a.etiqueta, a.edificio_id, e.nombre AS edificio FROM aulas a JOIN edificios e ON e.id = a.edificio_id WHERE a.id = ?');
    $st->execute([$id]);
    $a = $st->fetch();
    if (!$a) responder_error(400, 'Elegí un aula.');
    return $a;
}

// Las filas indicadas tienen que existir, ser del horario regular y formar un solo bloque (mismo aula, día y hora)
function cargar_bloque($pdo, $ids) {
    $marcas = implode(',', array_fill(0, count($ids), '?'));
    $st = $pdo->prepare(
        'SELECT h.id, h.aula_id, h.dia, h.inicio, h.fin, h.comision_id, h.titulo,
                a.etiqueta AS aula, a.edificio_id, e.nombre AS edificio, m.id AS materia_id, m.nombre AS materia
           FROM horarios h
           JOIN aulas a ON a.id = h.aula_id
           JOIN edificios e ON e.id = a.edificio_id
      LEFT JOIN comisiones c ON c.id = h.comision_id
      LEFT JOIN materias m ON m.id = c.materia_id
          WHERE h.fecha IS NULL AND h.id IN (' . $marcas . ')'
    );
    $st->execute($ids);
    $filas = $st->fetchAll();
    if (count($filas) !== count($ids)) responder_error(404, 'La clase ya no existe. Volvé a buscarla.');
    $p = $filas[0];
    foreach ($filas as $f) {
        if ($f['aula_id'] !== $p['aula_id'] || (int)$f['dia'] !== (int)$p['dia'] || $f['inicio'] !== $p['inicio'] || $f['fin'] !== $p['fin']) {
            responder_error(400, 'Datos inconsistentes. Volvé a buscar la clase.');
        }
    }
    $p['nombre'] = $p['materia'] !== null ? $p['materia'] : $p['titulo'];
    $p['ids'] = $ids;
    return $p;
}

// Qué choques tendría una clase en ese aula, día y horario. $fecha = null: horario de siempre; con fecha: ese día puntual.
// Devuelve textos del tipo "Morfología I COM 2 (08:00–13:00)". Una clase de la misma materia en el mismo horario no es choque (comisiones juntas).
function conflictos($pdo, $aulaId, $dia, $fecha, $ini, $fin, $excluirIds, $materiaId) {
    $periodo = periodo_vigente($pdo, $fecha);
    $anio = $periodo[0];
    $cuat = $periodo[1];
    $ocupado = [];

    $excepcionadas = [];
    if ($fecha !== null) {
        $st = $pdo->prepare('SELECT horario_id FROM excepciones WHERE fecha = ?');
        $st->execute([$fecha]);
        foreach ($st->fetchAll() as $r) $excepcionadas[(int)$r['horario_id']] = true;
    }

    // Clases de siempre en esa aula y día
    $st = $pdo->prepare(
        'SELECT h.id, h.inicio, h.fin, h.titulo, c.numero, m.id AS materia_id, m.nombre AS materia
           FROM horarios h
      LEFT JOIN comisiones c ON c.id = h.comision_id
      LEFT JOIN materias m ON m.id = c.materia_id
          WHERE h.fecha IS NULL AND h.aula_id = ? AND h.dia = ?
            AND (h.comision_id IS NULL OR (c.anio = ? AND c.cuatrimestre IN (?, 3)))'
    );
    $st->execute([$aulaId, $dia, $anio, $cuat]);
    foreach ($st->fetchAll() as $r) {
        if (isset($excepcionadas[(int)$r['id']])) continue;
        $ocupado[] = ['id' => (int)$r['id'], 'materia_id' => $r['materia_id'], 'inicio' => $r['inicio'], 'fin' => $r['fin'],
                      'texto' => $r['materia'] !== null ? $r['materia'] . ' ' . $r['numero'] : $r['titulo']];
    }

    if ($fecha !== null) {
        // Clases que ese día se mudan hacia esta aula
        $st = $pdo->prepare(
            "SELECT h.id, COALESCE(x.inicio, h.inicio) AS inicio, COALESCE(x.fin, h.fin) AS fin, h.titulo, c.numero,
                    m.id AS materia_id, m.nombre AS materia
               FROM excepciones x
               JOIN horarios h ON h.id = x.horario_id
          LEFT JOIN comisiones c ON c.id = h.comision_id
          LEFT JOIN materias m ON m.id = c.materia_id
              WHERE x.fecha = ? AND x.tipo = 'movida' AND x.aula_id = ?"
        );
        $st->execute([$fecha, $aulaId]);
        foreach ($st->fetchAll() as $r) {
            $ocupado[] = ['id' => (int)$r['id'], 'materia_id' => $r['materia_id'], 'inicio' => $r['inicio'], 'fin' => $r['fin'],
                          'texto' => $r['materia'] !== null ? $r['materia'] . ' ' . $r['numero'] : $r['titulo']];
        }
        // Reservas de ese día
        $st = $pdo->prepare('SELECT id, inicio, fin, titulo FROM horarios WHERE fecha = ? AND aula_id = ?');
        $st->execute([$fecha, $aulaId]);
        foreach ($st->fetchAll() as $r) {
            $ocupado[] = ['id' => (int)$r['id'], 'materia_id' => null, 'inicio' => $r['inicio'], 'fin' => $r['fin'], 'texto' => 'Reserva: ' . $r['titulo']];
        }
    }

    $hay = [];
    foreach ($ocupado as $o) {
        if (in_array($o['id'], $excluirIds, true)) continue;
        if ($materiaId !== null && $o['materia_id'] !== null && (int)$o['materia_id'] === (int)$materiaId && $o['inicio'] === $ini && $o['fin'] === $fin) continue;
        if ($o['inicio'] < $fin && $ini < $o['fin']) $hay[] = $o['texto'] . ' (' . hhmm($o['inicio']) . ' a ' . hhmm($o['fin']) . ')';
    }
    return array_values(array_unique($hay));
}

function responder_conflictos($lista) {
    http_response_code(409);
    echo json_encode(['error' => 'Hay clases en ese horario.', 'conflictos' => array_values($lista)], JSON_UNESCAPED_UNICODE);
    exit;
}

function registrar_cambio($pdo, $uid, $accion, $detalle) {
    try {
        $pdo->prepare('INSERT INTO registro_cambios (usuario_id, accion, detalle) VALUES (?, ?, ?)')
            ->execute([$uid, $accion, cortar($detalle, 480)]);
    } catch (PDOException $e) {
        error_log('UNM Aulas: no se pudo registrar el cambio: ' . $e->getMessage());
    }
}

// Bloques cuya materia o docentes contienen todas las palabras buscadas (sin tildes ni mayúsculas)
function bloques_que_coinciden($bloques, $q) {
    $palabras = array_values(array_filter(explode(' ', plano($q))));
    if (!$palabras) return [];
    $hallados = [];
    foreach ($bloques as $b) {
        $todo = plano($b['materia']) . ' ' . plano(implode(' ', $b['docentes']));
        $ok = true;
        foreach ($palabras as $p) { if (strpos($todo, $p) === false) { $ok = false; break; } }
        if ($ok) $hallados[] = $b;
    }
    usort($hallados, function ($x, $y) {
        return strcmp(plano($x['materia']) . $x['dia'] . $x['inicio'], plano($y['materia']) . $y['dia'] . $y['inicio']);
    });
    return $hallados;
}
