<?php
// ¿Hay una sesión abierta? Devuelve el usuario, o 401 si no.
require __DIR__ . '/sesion_lib.php';

$u = usuario_actual($pdo);
if (!$u) responder_error(401, 'Sin sesión.');
echo json_encode(['usuario' => usuario_json($u)], JSON_UNESCAPED_UNICODE);
