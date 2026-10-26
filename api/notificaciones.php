<?php
// Avisos del alumno (la campana de Inicio).
//   GET                 -> { "noLeidas": n, "items": [ { id, mensaje, enlace, leida, creada } ] }  (los últimos 50)
//   GET  ?modo=contar   -> { "noLeidas": n }   (liviano, para el puntito de la campana)
//   POST {accion:"leer_todas"}  |  POST {accion:"leer", id: n}
require __DIR__ . '/sesion_lib.php';

$u = usuario_actual($pdo);
if (!$u) responder_error(401, 'Sin sesión.');
$uid = (int)$u['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $in = leer_json();
    $accion = isset($in['accion']) ? $in['accion'] : '';
    if ($accion === 'leer_todas') {
        $pdo->prepare('UPDATE notificaciones SET leida = 1 WHERE usuario_id = ? AND leida = 0')->execute([$uid]);
    } elseif ($accion === 'leer') {
        $id = isset($in['id']) ? (int)$in['id'] : 0;
        if ($id <= 0) responder_error(400, 'Datos inválidos.');
        $pdo->prepare('UPDATE notificaciones SET leida = 1 WHERE id = ? AND usuario_id = ?')->execute([$id, $uid]);
    } else {
        responder_error(400, 'Acción desconocida.');
    }
    echo json_encode(['ok' => true]);
    exit;
}

$st = $pdo->prepare('SELECT COUNT(*) AS n FROM notificaciones WHERE usuario_id = ? AND leida = 0');
$st->execute([$uid]);
$noLeidas = (int)$st->fetch()['n'];

if (isset($_GET['modo']) && $_GET['modo'] === 'contar') {
    echo json_encode(['noLeidas' => $noLeidas]);
    exit;
}

$st = $pdo->prepare('SELECT id, mensaje, enlace, leida, creada_en FROM notificaciones WHERE usuario_id = ? ORDER BY creada_en DESC, id DESC LIMIT 50');
$st->execute([$uid]);
$items = [];
foreach ($st->fetchAll() as $f) {
    $items[] = [
        'id' => (int)$f['id'], 'mensaje' => $f['mensaje'], 'enlace' => $f['enlace'],
        'leida' => (int)$f['leida'] === 1, 'creada' => str_replace(' ', 'T', $f['creada_en']),
    ];
}
echo json_encode(['noLeidas' => $noLeidas, 'items' => $items], JSON_UNESCAPED_UNICODE);
