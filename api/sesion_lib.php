<?php
require_once __DIR__ . '/no_cache.php';
// Funciones compartidas de sesión. Lo incluyen login, registro, logout y sesión.
require_once __DIR__ . '/conexion.php';

const MAX_INTENTOS = 5;    // intentos fallidos seguidos antes de bloquear
const BLOQUEO_MIN  = 10;   // minutos de bloqueo

// Cualquier error inesperado: se anota en el log de Apache y se responde algo genérico
set_exception_handler(function ($e) {
    error_log('UNM Aulas: ' . $e->getMessage());
    responder_error(500, 'Error del servidor. Probá de nuevo en un momento.');
});

ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', '28800');   // 8 horas
session_name('UNMAULAS');
session_cache_limiter('');
session_start();

function solo_post() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: POST');
        responder_error(405, 'Método no permitido.');
    }
}

// Solo se aceptan pedidos JSON (así un formulario de otro sitio no puede enviar datos a la app)
function leer_json() {
    $tipo = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
    if (stripos($tipo, 'application/json') !== 0) responder_error(415, 'Formato no admitido.');
    $d = json_decode(file_get_contents('php://input'), true);
    if (!is_array($d)) responder_error(400, 'Datos inválidos.');
    return $d;
}

function usuario_json($u) {
    return [
        'id'      => (int)$u['id'],
        'nombre'  => trim($u['nombre'] . ' ' . $u['apellido']),
        'carrera' => isset($u['carrera']) ? $u['carrera'] : '',
        'rol'     => $u['rol'],
    ];
}

// Usuario de la sesión actual, o null si no hay sesión válida
function usuario_actual($pdo) {
    if (empty($_SESSION['uid'])) return null;
    $st = $pdo->prepare("SELECT u.id, u.nombre, u.apellido, u.rol, c.nombre AS carrera
                           FROM usuarios u LEFT JOIN carreras c ON c.id = u.carrera_id
                          WHERE u.id = ? AND u.estado = 'activa'");
    $st->execute([$_SESSION['uid']]);
    $u = $st->fetch();
    if (!$u) { $_SESSION = []; return null; }
    return $u;
}
