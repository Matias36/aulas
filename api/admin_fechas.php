<?php
require_once __DIR__ . '/no_cache.php';
// Fechas del calendario académico.
//   GET                                      -> las que todavía no pasaron
//   POST {accion:"crear", titulo, fecha, fin}   (fin es opcional: si dura varios días)
//   POST {accion:"borrar", id}
require __DIR__ . '/admin_lib.php';
$admin = exigir_admin($pdo);
$uid = (int)$admin['id'];

function texto_fechas($ini, $fin) {
    if ($fin === null || $fin === $ini) return fecha_larga($ini) . '/' . substr($ini, 0, 4);
    return fecha_corta($ini) . ' al ' . fecha_corta($fin) . '/' . substr($fin, 0, 4);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $st = $pdo->query('SELECT id, titulo, fecha_inicio, fecha_fin FROM eventos WHERE COALESCE(fecha_fin, fecha_inicio) >= CURDATE() ORDER BY fecha_inicio, titulo');
    $salida = [];
    foreach ($st->fetchAll() as $r) {
        $salida[] = ['id' => (int)$r['id'], 'titulo' => $r['titulo'], 'cuando' => texto_fechas($r['fecha_inicio'], $r['fecha_fin'])];
    }
    echo json_encode($salida, JSON_UNESCAPED_UNICODE);
    exit;
}

$in = leer_json();
$accion = isset($in['accion']) ? $in['accion'] : '';

if ($accion === 'crear') {
    $titulo = cortar(trim(isset($in['titulo']) ? (string)$in['titulo'] : ''), 200);
    $fecha = isset($in['fecha']) ? (string)$in['fecha'] : '';
    $fin = (isset($in['fin']) && $in['fin'] !== '') ? (string)$in['fin'] : null;
    if ($titulo === '') responder_error(400, 'Escribí un título.');
    if (!fecha_valida($fecha)) responder_error(400, 'Elegí la fecha.');
    if ($fin !== null && (!fecha_valida($fin) || $fin < $fecha)) responder_error(400, 'La fecha de fin tiene que ser igual o posterior a la de inicio.');
    if ($fin === $fecha) $fin = null;
    $pdo->prepare('INSERT INTO eventos (titulo, fecha_inicio, fecha_fin) VALUES (?, ?, ?)')->execute([$titulo, $fecha, $fin]);
    registrar_cambio($pdo, $uid, 'agregar_fecha', 'Agregó al calendario: ' . $titulo . ' (' . texto_fechas($fecha, $fin) . ').');
    echo json_encode(['ok' => true]);
    exit;
}

if ($accion === 'borrar') {
    $id = isset($in['id']) ? (int)$in['id'] : 0;
    if ($id <= 0) responder_error(400, 'Datos inválidos.');
    $st = $pdo->prepare('SELECT titulo FROM eventos WHERE id = ?');
    $st->execute([$id]);
    $r = $st->fetch();
    if (!$r) responder_error(404, 'Esa fecha ya no existe.');
    $pdo->prepare('DELETE FROM eventos WHERE id = ?')->execute([$id]);
    registrar_cambio($pdo, $uid, 'borrar_fecha', 'Quitó del calendario: ' . $r['titulo'] . '.');
    echo json_encode(['ok' => true]);
    exit;
}

responder_error(400, 'Acción desconocida.');
