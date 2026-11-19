<?php
require_once __DIR__ . '/no_cache.php';
// Para cambiar la contraseña del administrador. Se abre desde la PC del servidor:
//   http://localhost/unm-aulas/api/cambiar_clave_admin.php
// Cuando termines, borrá este archivo.
require __DIR__ . '/conexion.php';
header('Content-Type: text/html; charset=utf-8');

if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Solo se puede abrir desde la PC del servidor, usando http://localhost/...');
}

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = preg_replace('/\D/', '', isset($_POST['dni']) ? $_POST['dni'] : '');
    $clave = isset($_POST['clave']) ? (string)$_POST['clave'] : '';
    if (strlen($clave) < 8) {
        $mensaje = 'La contraseña tiene que tener al menos 8 caracteres.';
    } else {
        $st = $pdo->prepare("UPDATE usuarios SET password_hash = ?, intentos_fallidos = 0, bloqueado_hasta = NULL WHERE dni = ? AND rol = 'administrador'");
        $st->execute([password_hash($clave, PASSWORD_DEFAULT), $dni]);
        $mensaje = $st->rowCount() ? 'Listo: la contraseña cambió. Ahora borrá este archivo.' : 'No se encontró un administrador con ese DNI (o la contraseña ya era esa).';
    }
}
?>
<!DOCTYPE html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Cambiar contraseña del administrador</title></head>
<body style="font-family: Arial, sans-serif; max-width: 420px; margin: 40px auto; padding: 0 16px;">
<h1 style="font-size: 22px;">Contraseña del administrador</h1>
<?php if ($mensaje !== '') echo '<p><b>' . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . '</b></p>'; ?>
<form method="post">
  <p><label>DNI del administrador<br><input name="dni" value="2" inputmode="numeric" style="width:100%; padding:8px;"></label></p>
  <p><label>Contraseña nueva (mínimo 8 caracteres)<br><input name="clave" type="password" style="width:100%; padding:8px;"></label></p>
  <p><button type="submit" style="padding:10px 18px;">Cambiar</button></p>
</form>
</body></html>
