<?php
// Cerrar sesión (destruye la sesión en el servidor)
require __DIR__ . '/sesion_lib.php';
solo_post();
leer_json();

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();
echo json_encode(['ok' => true]);
