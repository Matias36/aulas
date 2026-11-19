<?php
require_once __DIR__ . '/no_cache.php';
// Crear cuenta. Recibe JSON: { "dni", "nombre", "apellido", "carrera" (id), "clave" }
require __DIR__ . '/sesion_lib.php';
solo_post();
$in = leer_json();

$dni      = trim(isset($in['dni']) ? (string)$in['dni'] : '');
$nombre   = trim(preg_replace('/\s+/u', ' ', isset($in['nombre'])   ? (string)$in['nombre']   : ''));
$apellido = trim(preg_replace('/\s+/u', ' ', isset($in['apellido']) ? (string)$in['apellido'] : ''));
$carrera  = isset($in['carrera']) ? (int)$in['carrera'] : 0;
$clave    = isset($in['clave']) ? (string)$in['clave'] : '';

if (!preg_match('/^\d{7,8}$/', $dni)) responder_error(400, 'El DNI debe tener 7 u 8 números, sin puntos.');
if ($nombre === '' || $apellido === '') responder_error(400, 'Completá nombre y apellido.');
if (!preg_match('/^.{1,80}$/u', $nombre) || !preg_match('/^.{1,80}$/u', $apellido)) responder_error(400, 'El nombre y el apellido son demasiado largos.');
if (strlen($clave) < 8) responder_error(400, 'La contraseña debe tener al menos 8 caracteres.');
if (strlen($clave) > 72) responder_error(400, 'La contraseña es demasiado larga (máximo 72 caracteres).');

$st = $pdo->prepare('SELECT nombre FROM carreras WHERE id = ?');
$st->execute([$carrera]);
$c = $st->fetch();
if (!$c) responder_error(400, 'Elegí tu carrera.');

$st = $pdo->prepare('SELECT id FROM usuarios WHERE dni = ?');
$st->execute([$dni]);
if ($st->fetch()) responder_error(409, 'Ese DNI ya tiene una cuenta.');

try {
    $pdo->prepare("INSERT INTO usuarios (dni, password_hash, nombre, apellido, carrera_id, rol, estado) VALUES (?, ?, ?, ?, ?, 'alumno', 'activa')")
        ->execute([$dni, password_hash($clave, PASSWORD_DEFAULT), $nombre, $apellido, $carrera]);
} catch (PDOException $e) {
    if (isset($e->errorInfo[1]) && (int)$e->errorInfo[1] === 1062) responder_error(409, 'Ese DNI ya tiene una cuenta.');
    throw $e;
}

session_regenerate_id(true);
$_SESSION['uid'] = (int)$pdo->lastInsertId();

echo json_encode(['usuario' => usuario_json([
    'id' => $_SESSION['uid'], 'nombre' => $nombre, 'apellido' => $apellido, 'carrera' => $c['nombre'], 'rol' => 'alumno',
])], JSON_UNESCAPED_UNICODE);
