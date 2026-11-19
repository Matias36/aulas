<?php
require_once __DIR__ . '/no_cache.php';
// Panel de administrador: buscar clases y cambiarlas, cancelarlas o eliminarlas.
//   GET ?q=texto        -> clases que coinciden
//   GET ?excepciones=1  -> cambios por fecha que todavía no pasaron
//   POST {accion: cambiar | excepcion | quitar_excepcion | eliminar, ...}
require __DIR__ . '/admin_lib.php';
$admin = exigir_admin($pdo);
$uid = (int)$admin['id'];

// ---------- Consultas ----------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['excepciones'])) {
        $st = $pdo->query(
            'SELECT x.id, x.fecha, x.tipo, x.inicio AS xi, x.fin AS xf,
                    h.id AS horario_id, h.aula_id, h.dia, h.inicio, h.fin, h.titulo,
                    c.numero, m.id AS materia_id, m.nombre AS materia,
                    a2.etiqueta AS aula_nueva, e2.nombre AS edificio_nuevo
               FROM excepciones x
               JOIN horarios h ON h.id = x.horario_id
          LEFT JOIN comisiones c ON c.id = h.comision_id
          LEFT JOIN materias m ON m.id = c.materia_id
          LEFT JOIN aulas a2 ON a2.id = x.aula_id
          LEFT JOIN edificios e2 ON e2.id = a2.edificio_id
              WHERE x.fecha >= CURDATE()
           ORDER BY x.fecha, h.inicio, h.id'
        );
        $grupos = [];
        foreach ($st->fetchAll() as $r) {
            $clave = $r['fecha'] . '|' . $r['aula_id'] . '|' . $r['inicio'] . '|' . ($r['materia_id'] !== null ? $r['materia_id'] : 'a' . $r['horario_id']);
            if (!isset($grupos[$clave])) {
                if ($r['tipo'] === 'cancelada') {
                    $detalle = 'Cancelada';
                } else {
                    $detalle = 'Pasa a ' . $r['edificio_nuevo'] . ' ' . $r['aula_nueva'];
                    if ($r['xi'] !== null) $detalle .= ' · ' . hhmm($r['xi']) . ' a ' . hhmm($r['xf']);
                }
                $grupos[$clave] = [
                    'ids' => [], 'numeros' => [], 'cuando' => fecha_larga($r['fecha']),
                    'nombre' => $r['materia'] !== null ? $r['materia'] : $r['titulo'], 'detalle' => $detalle,
                ];
            }
            $grupos[$clave]['ids'][] = (int)$r['id'];
            if ($r['numero'] !== null) $grupos[$clave]['numeros'][] = $r['numero'];
        }
        $salida = [];
        foreach ($grupos as $g) {
            $texto = $g['nombre'] . ($g['numeros'] ? ' · ' . texto_comisiones($g['numeros']) : '');
            $salida[] = ['ids' => $g['ids'], 'cuando' => $g['cuando'], 'texto' => $texto, 'detalle' => $g['detalle']];
        }
        echo json_encode($salida, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $q = isset($_GET['q']) ? trim(preg_replace('/\s+/u', ' ', (string)$_GET['q'])) : '';
    if (!preg_match('/^.{2,60}$/u', $q)) { echo '[]'; exit; }
    $periodo = periodo_vigente($pdo);
    $bloques = bloques_que_coinciden(
        juntar_en_bloques(filas_regulares($pdo, $periodo[0], $periodo[1]), carreras_por_comision($pdo)), $q
    );
    $salida = [];
    foreach (array_slice($bloques, 0, 40) as $b) {
        list($docente, $comision, $carrera) = textos_bloque($b['docentes'], $b['numeros'], $b['carreras']);
        $salida[] = [
            'ids' => $b['ids'], 'materia' => $b['materia'], 'dia' => $b['dia'], 'inicio' => $b['inicio'], 'fin' => $b['fin'],
            'aulaId' => $b['fila']['aula_id'], 'aula' => $b['fila']['aula'],
            'edificioId' => $b['fila']['edificio_id'], 'edificio' => $b['fila']['edificio'],
            'docente' => $docente, 'comision' => $comision, 'carrera' => $carrera,
        ];
    }
    echo json_encode($salida, JSON_UNESCAPED_UNICODE);
    exit;
}

// ---------- Cambios ----------
$in = leer_json();
$accion = isset($in['accion']) ? $in['accion'] : '';
$avisar = !isset($in['avisar']) || !empty($in['avisar']);
$forzar = !empty($in['forzar']);
$motivo = cortar(trim(isset($in['motivo']) ? (string)$in['motivo'] : ''), 150);
function con_motivo($msg, $motivo) { return $motivo !== '' ? $msg . ' Motivo: ' . $motivo . '.' : $msg; }
function marcas_de($ids) { return implode(',', array_fill(0, count($ids), '?')); }

// --- Cambiar aula, día u horario para siempre ---
if ($accion === 'cambiar') {
    $ids = ids_pedidos($in);
    $p = cargar_bloque($pdo, $ids);
    $aulaId = isset($in['aula']) ? (string)$in['aula'] : '';
    $dia = isset($in['dia']) ? (int)$in['dia'] : 0;
    $ini = isset($in['inicio']) ? (string)$in['inicio'] : '';
    $fin = isset($in['fin']) ? (string)$in['fin'] : '';
    validar_franja($dia, $ini, $fin);
    $a = aula_datos($pdo, $aulaId);
    $iniS = $ini . ':00';
    $finS = $fin . ':00';

    $cambios = [];
    if ($aulaId !== $p['aula_id']) $cambios[] = 'ahora se cursa en ' . $a['edificio'] . ' ' . $a['etiqueta'];
    if ((int)$p['dia'] !== $dia || $iniS !== $p['inicio'] || $finS !== $p['fin']) $cambios[] = 'ahora es el ' . DIAS_LARGO[$dia] . ' de ' . $ini . ' a ' . $fin;
    if (!$cambios) responder_error(400, 'No cambiaste nada.');

    $hay = conflictos($pdo, $aulaId, $dia, null, $iniS, $finS, $ids, $p['materia_id']);
    if ($hay && !$forzar) responder_conflictos($hay);

    $marcas = marcas_de($ids);
    $pdo->prepare('UPDATE horarios SET aula_id = ?, dia = ?, inicio = ?, fin = ? WHERE fecha IS NULL AND id IN (' . $marcas . ')')
        ->execute(array_merge([$aulaId, $dia, $iniS, $finS], $ids));
    if ((int)$p['dia'] !== $dia) {   // si cambió de día, los cambios por fecha que tenía ya no corresponden
        $pdo->prepare('DELETE FROM excepciones WHERE horario_id IN (' . $marcas . ')')->execute($ids);
    }

    $msg = con_motivo('Cambió ' . $p['nombre'] . ': ' . implode(' y ', $cambios) . '.', $motivo);
    $n = $avisar ? notificar_seguidores($pdo, $ids, cortar($msg, 300), enlace_aula($aulaId, $a['edificio_id'], $dia)) : 0;
    registrar_cambio($pdo, $uid, 'cambiar', $p['nombre'] . ' (' . DIAS_LARGO[(int)$p['dia']] . ' ' . hhmm($p['inicio']) . '): ' . implode(' y ', $cambios) . '. Avisados: ' . $n . '.');
    echo json_encode(['ok' => true, 'avisados' => $n]);
    exit;
}

// --- Cancelar o mudar la clase solo para una fecha ---
if ($accion === 'excepcion') {
    $ids = ids_pedidos($in);
    $p = cargar_bloque($pdo, $ids);
    $dia = (int)$p['dia'];
    $fecha = isset($in['fecha']) ? (string)$in['fecha'] : '';
    if (!fecha_valida($fecha)) responder_error(400, 'Elegí una fecha.');
    if ($fecha < date('Y-m-d')) responder_error(400, 'Esa fecha ya pasó.');
    if (dia_de_fecha($fecha) !== $dia) {
        responder_error(400, 'Esa fecha cae un ' . DIAS_LARGO[dia_de_fecha($fecha)] . ' y esta clase es los ' . DIAS_LARGO[$dia] . '.');
    }
    $tipo = isset($in['tipo']) ? $in['tipo'] : '';
    if (!in_array($tipo, ['cancelada', 'movida'], true)) responder_error(400, 'Datos inválidos.');
    $cuando = DIAS_LARGO[$dia] . ' ' . fecha_corta($fecha);

    $aulaNueva = null; $iniN = null; $finN = null;
    $enlace = enlace_aula($p['aula_id'], $p['edificio_id'], $dia);
    if ($tipo === 'movida') {
        $aulaId = (isset($in['aula']) && $in['aula'] !== '') ? (string)$in['aula'] : $p['aula_id'];
        $ini = (isset($in['inicio']) && $in['inicio'] !== '') ? (string)$in['inicio'] : substr($p['inicio'], 0, 5);
        $fin = (isset($in['fin']) && $in['fin'] !== '') ? (string)$in['fin'] : substr($p['fin'], 0, 5);
        validar_franja($dia, $ini, $fin);
        $a = aula_datos($pdo, $aulaId);
        $iniS = $ini . ':00';
        $finS = $fin . ':00';
        $mismaAula = ($aulaId === $p['aula_id']);
        $mismaHora = ($iniS === $p['inicio'] && $finS === $p['fin']);
        if ($mismaAula && $mismaHora) responder_error(400, 'No cambiaste nada.');

        $hay = conflictos($pdo, $aulaId, $dia, $fecha, $iniS, $finS, $ids, $p['materia_id']);
        if ($hay && !$forzar) responder_conflictos($hay);

        $aulaNueva = $aulaId;
        if (!$mismaHora) { $iniN = $iniS; $finN = $finS; }
        if (!$mismaAula && !$mismaHora) $texto = 'pasa a ' . $a['edificio'] . ' ' . $a['etiqueta'] . ', de ' . $ini . ' a ' . $fin;
        elseif (!$mismaAula)            $texto = 'pasa a ' . $a['edificio'] . ' ' . $a['etiqueta'];
        else                            $texto = 'cambia de horario: de ' . $ini . ' a ' . $fin;
        $msg = 'El ' . $cuando . ', ' . $p['nombre'] . ' ' . $texto . '.';
        $enlace = enlace_aula($aulaId, $a['edificio_id'], $dia);
    } else {
        $msg = 'Se canceló ' . $p['nombre'] . ' del ' . $cuando . '.';
    }

    $pdo->beginTransaction();
    $ins = $pdo->prepare(
        'INSERT INTO excepciones (horario_id, fecha, tipo, aula_id, inicio, fin, motivo, creado_por)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE tipo = VALUES(tipo), aula_id = VALUES(aula_id), inicio = VALUES(inicio),
                                 fin = VALUES(fin), motivo = VALUES(motivo), creado_por = VALUES(creado_por), creado_en = NOW()'
    );
    foreach ($ids as $id) $ins->execute([$id, $fecha, $tipo, $aulaNueva, $iniN, $finN, $motivo !== '' ? $motivo : null, $uid]);
    $pdo->commit();

    $msg = con_motivo($msg, $motivo);
    $n = $avisar ? notificar_seguidores($pdo, $ids, cortar($msg, 300), $enlace) : 0;
    registrar_cambio($pdo, $uid, $tipo === 'movida' ? 'mover_fecha' : 'cancelar_fecha', $msg . ' Avisados: ' . $n . '.');
    echo json_encode(['ok' => true, 'avisados' => $n]);
    exit;
}

// --- Quitar un cambio por fecha (vuelve el horario habitual) ---
if ($accion === 'quitar_excepcion') {
    $ids = ids_pedidos($in);
    $st = $pdo->prepare(
        'SELECT x.id, x.fecha, x.horario_id, h.aula_id, h.dia, h.titulo, a.edificio_id, m.nombre AS materia
           FROM excepciones x
           JOIN horarios h ON h.id = x.horario_id
           JOIN aulas a ON a.id = h.aula_id
      LEFT JOIN comisiones c ON c.id = h.comision_id
      LEFT JOIN materias m ON m.id = c.materia_id
          WHERE x.id IN (' . marcas_de($ids) . ')'
    );
    $st->execute($ids);
    $filas = $st->fetchAll();
    if (!$filas) responder_error(404, 'Ese cambio ya no existe.');
    $p = $filas[0];
    $horarioIds = [];
    foreach ($filas as $f) $horarioIds[] = (int)$f['horario_id'];
    $nombre = $p['materia'] !== null ? $p['materia'] : $p['titulo'];

    $pdo->prepare('DELETE FROM excepciones WHERE id IN (' . marcas_de($ids) . ')')->execute($ids);
    $msg = 'Se restableció el horario habitual de ' . $nombre . ' del ' . fecha_larga($p['fecha']) . '.';
    $n = $avisar ? notificar_seguidores($pdo, $horarioIds, cortar($msg, 300), enlace_aula($p['aula_id'], $p['edificio_id'], $p['dia'])) : 0;
    registrar_cambio($pdo, $uid, 'quitar_cambio', $msg . ' Avisados: ' . $n . '.');
    echo json_encode(['ok' => true, 'avisados' => $n]);
    exit;
}

// --- Eliminar la clase para siempre ---
if ($accion === 'eliminar') {
    $ids = ids_pedidos($in);
    $p = cargar_bloque($pdo, $ids);
    $msg = con_motivo('Se dio de baja ' . $p['nombre'] . ' (' . DIAS_LARGO[(int)$p['dia']] . ' de ' . hhmm($p['inicio']) . ' a ' . hhmm($p['fin']) . ').', $motivo);
    $n = $avisar ? notificar_seguidores($pdo, $ids, cortar($msg, 300), null) : 0;   // se avisa antes de borrar: al borrar se pierden los seguidores
    $pdo->prepare('DELETE FROM horarios WHERE fecha IS NULL AND id IN (' . marcas_de($ids) . ')')->execute($ids);
    registrar_cambio($pdo, $uid, 'eliminar', $msg . ' Avisados: ' . $n . '.');
    echo json_encode(['ok' => true, 'avisados' => $n]);
    exit;
}

responder_error(400, 'Acción desconocida.');
