<?php
// Inicio de sesión. Recibe JSON: { "dni": "...", "clave": "...", "admin": true|false }
require __DIR__ . '/sesion_lib.php';
solo_post();
$in = leer_json();

$dni   = trim(isset($in['dni']) ? (string)$in['dni'] : '');
$clave = isset($in['clave']) ? (string)$in['clave'] : '';
$admin = !empty($in['admin']);

if ($dni === '' || $clave === '') responder_error(400, 'Completá tu DNI y la contraseña.');
if (!preg_match('/^\d{1,8}$/', $dni)) responder_error(400, 'El DNI solo lleva números.');

$st = $pdo->prepare(
    'SELECT u.id, u.password_hash, u.nombre, u.apellido, u.rol, u.estado, u.intentos_fallidos,
            TIMESTAMPDIFF(SECOND, NOW(), u.bloqueado_hasta) AS seg_bloqueo, c.nombre AS carrera
       FROM usuarios u LEFT JOIN carreras c ON c.id = u.carrera_id
      WHERE u.dni = ?'
);
$st->execute([$dni]);
$u = $st->fetch();

// Cuenta bloqueada por intentos fallidos
if ($u && $u['seg_bloqueo'] !== null && (int)$u['seg_bloqueo'] > 0) {
    $min = (int)ceil($u['seg_bloqueo'] / 60);
    responder_error(429, 'Demasiados intentos fallidos. Probá de nuevo en ' . $min . ' minuto' . ($min === 1 ? '' : 's') . '.');
}

if ($u) {
    $ok = password_verify($clave, $u['password_hash']);
} else {
    password_hash($clave, PASSWORD_DEFAULT);   // mismo tiempo de respuesta si el DNI no existe
    $ok = false;
}

if (!$ok) {
    if ($u) {
        $n = (int)$u['intentos_fallidos'] + 1;
        if ($n >= MAX_INTENTOS) {
            $pdo->prepare('UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = DATE_ADD(NOW(), INTERVAL ? MINUTE) WHERE id = ?')
                ->execute([BLOQUEO_MIN, $u['id']]);
            responder_error(429, 'Demasiados intentos fallidos. Probá de nuevo en ' . BLOQUEO_MIN . ' minutos.');
        }
        $pdo->prepare('UPDATE usuarios SET intentos_fallidos = ? WHERE id = ?')->execute([$n, $u['id']]);
    }
    responder_error(401, 'DNI o contraseña incorrectos.');
}

if ($u['estado'] !== 'activa') responder_error(403, 'Tu cuenta no está habilitada.');
if ($admin && $u['rol'] !== 'administrador') responder_error(403, 'Esta cuenta no tiene acceso de administrador.');
if (!$admin && $u['rol'] === 'administrador') responder_error(403, 'Las cuentas de administrador ingresan desde "Acceso administrador".');

// Entró bien: se limpian los intentos, se renueva el identificador de sesión y se actualiza el hash si hace falta
$pdo->prepare('UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = ?')->execute([$u['id']]);
if (password_needs_rehash($u['password_hash'], PASSWORD_DEFAULT)) {
    $pdo->prepare('UPDATE usuarios SET password_hash = ? WHERE id = ?')->execute([password_hash($clave, PASSWORD_DEFAULT), $u['id']]);
}
session_regenerate_id(true);
$_SESSION['uid'] = (int)$u['id'];

echo json_encode(['usuario' => usuario_json($u)], JSON_UNESCAPED_UNICODE);
