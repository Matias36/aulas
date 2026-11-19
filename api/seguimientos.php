<?php
require_once __DIR__ . '/no_cache.php';
// Clases que el alumno decide seguir (campanitas), con su nota personal.
//   GET                 -> "Mis cursos" para Perfil: { "cursos": [ ... ] }
//   GET  ?modo=ids      -> { "ids": [horarios seguidos], "conNota": [los que tienen nota] }
//   POST {accion:"seguir"|"dejar", ids:[...]}
//   POST {accion:"nota", ids:[...], nota:"..."}
require __DIR__ . '/sesion_lib.php';
require __DIR__ . '/lib_horarios.php';

$u = usuario_actual($pdo);
if (!$u) responder_error(401, 'Sin sesión.');
$uid = (int)$u['id'];

function ids_validos($in) {
    $ids = [];
    if (isset($in['ids']) && is_array($in['ids'])) {
        foreach ($in['ids'] as $i) { $n = (int)$i; if ($n > 0) $ids[$n] = true; }
    }
    $ids = array_keys($ids);
    if (!$ids || count($ids) > 20) responder_error(400, 'Datos inválidos.');
    return $ids;
}
function marcas($ids) { return implode(',', array_fill(0, count($ids), '?')); }

// ---------- Cambios ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $in = leer_json();
    $accion = isset($in['accion']) ? $in['accion'] : '';
    $ids = ids_validos($in);

    if ($accion === 'seguir') {
        $st = $pdo->prepare('SELECT id FROM horarios WHERE id IN (' . marcas($ids) . ')');
        $st->execute($ids);
        $ins = $pdo->prepare('INSERT IGNORE INTO seguimientos (usuario_id, horario_id) VALUES (?, ?)');
        foreach ($st->fetchAll() as $f) $ins->execute([$uid, (int)$f['id']]);

    } elseif ($accion === 'dejar') {
        $st = $pdo->prepare('DELETE FROM seguimientos WHERE usuario_id = ? AND horario_id IN (' . marcas($ids) . ')');
        $st->execute(array_merge([$uid], $ids));

    } elseif ($accion === 'nota') {
        $nota = isset($in['nota']) ? (string)$in['nota'] : '';
        if (!preg_match('/^.{0,500}$/suD', $nota)) responder_error(400, 'La nota es demasiado larga (máximo 500 caracteres).');
        $st = $pdo->prepare('UPDATE seguimientos SET nota = ? WHERE usuario_id = ? AND horario_id IN (' . marcas($ids) . ')');
        $st->execute(array_merge([$nota, $uid], $ids));

    } else {
        responder_error(400, 'Acción desconocida.');
    }
    echo json_encode(['ok' => true]);
    exit;
}

// ---------- Solo los ids (para pintar las campanitas) ----------
if (isset($_GET['modo']) && $_GET['modo'] === 'ids') {
    $st = $pdo->prepare("SELECT horario_id, (nota <> '') AS con_nota FROM seguimientos WHERE usuario_id = ?");
    $st->execute([$uid]);
    $ids = [];
    $conNota = [];
    foreach ($st->fetchAll() as $f) {
        $ids[] = (int)$f['horario_id'];
        if ((int)$f['con_nota'] === 1) $conNota[] = (int)$f['horario_id'];
    }
    echo json_encode(['ids' => $ids, 'conNota' => $conNota]);
    exit;
}

// ---------- Mis cursos ----------
list($anio, $cuat) = periodo_vigente($pdo);
$carrerasDe = carreras_por_comision($pdo);

$st = $pdo->prepare(
    'SELECT s.horario_id, s.nota, h.aula_id, h.dia, h.inicio, h.fin, h.comision_id, h.titulo, h.organizador,
            a.etiqueta AS aula, e.nombre AS edificio,
            c.numero, m.id AS materia_id, m.nombre AS materia, d.nombre AS docente
       FROM seguimientos s
       JOIN horarios h ON h.id = s.horario_id
       JOIN aulas a ON a.id = h.aula_id
       JOIN edificios e ON e.id = a.edificio_id
  LEFT JOIN comisiones c ON c.id = h.comision_id
  LEFT JOIN materias   m ON m.id = c.materia_id
  LEFT JOIN docentes   d ON d.id = c.docente_id
      WHERE s.usuario_id = ? AND h.fecha IS NULL
        AND (h.comision_id IS NULL OR (c.anio = ? AND c.cuatrimestre IN (?, 3)))
   ORDER BY h.dia, h.inicio, h.aula_id, c.numero'
);
$st->execute([$uid, $anio, $cuat]);

// Mismo criterio que en las aulas: comisiones de la misma materia, aula, día y hora van en un solo curso
$bloques = [];
foreach ($st->fetchAll() as $f) {
    $esClase = $f['comision_id'] !== null;
    $clave = $esClase
        ? $f['aula_id'] . '|' . $f['dia'] . '|' . $f['inicio'] . '|' . $f['fin'] . '|' . $f['materia_id']
        : 'actividad|' . $f['horario_id'];
    if (!isset($bloques[$clave])) {
        $bloques[$clave] = [
            'dia' => (int)$f['dia'], 'inicio' => hhmm($f['inicio']), 'fin' => hhmm($f['fin']),
            'edificio' => $f['edificio'], 'aula' => $f['aula'],
            'materia' => $esClase ? $f['materia'] : $f['titulo'],
            'docentes' => [], 'numeros' => [], 'carreras' => [], 'ids' => [], 'nota' => '',
        ];
    }
    $b = &$bloques[$clave];
    $b['ids'][] = (int)$f['horario_id'];
    if ($b['nota'] === '' && $f['nota'] !== '') $b['nota'] = $f['nota'];
    if ($esClase) {
        if ($f['docente'] !== null && !in_array($f['docente'], $b['docentes'], true)) $b['docentes'][] = $f['docente'];
        $b['numeros'][] = $f['numero'];
        foreach (($carrerasDe[(int)$f['comision_id']] ?? []) as $cn) {
            if (!in_array($cn, $b['carreras'], true)) $b['carreras'][] = $cn;
        }
    } elseif ($f['organizador'] !== null) {
        $b['docentes'][] = $f['organizador'];
    }
    unset($b);
}

$cursos = [];
foreach ($bloques as $b) {
    list($docente, $comision, $carrera) = textos_bloque($b['docentes'], $b['numeros'], $b['carreras']);
    $cursos[] = [
        'id' => $b['ids'][0], 'ids' => $b['ids'],
        'dia' => $b['dia'], 'inicio' => $b['inicio'], 'fin' => $b['fin'],
        'edificio' => $b['edificio'], 'aula' => $b['aula'],
        'materia' => $b['materia'], 'docente' => $docente,
        'comision' => $comision, 'carrera' => $carrera, 'nota' => $b['nota'],
    ];
}
echo json_encode(['cursos' => $cursos], JSON_UNESCAPED_UNICODE);
