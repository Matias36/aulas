<?php
// SOLO PARA PROBAR la campana. Se abre desde la PC: http://localhost/unm-aulas/api/crear_notificacion_prueba.php?dni=1
// Crea dos avisos de ejemplo para ese DNI. Después se puede borrar este archivo.
require __DIR__ . '/conexion.php';
require __DIR__ . '/lib_notificaciones.php';
header('Content-Type: text/plain; charset=utf-8');

if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit("Solo se puede ejecutar desde la PC del servidor, usando http://localhost/...\n");
}

$dni = isset($_GET['dni']) ? preg_replace('/\D/', '', $_GET['dni']) : '1';
$st = $pdo->prepare('SELECT id FROM usuarios WHERE dni = ?');
$st->execute([$dni]);
$u = $st->fetch();
if (!$u) exit("No existe un usuario con DNI $dni.\n");

notificar_usuario($pdo, $u['id'], 'Prueba: la clase que seguís cambió de aula. Ahora es en el aula 05.', 'aula.html?id=d2-05&e=daract2&d=1');
notificar_usuario($pdo, $u['id'], 'Prueba: las mesas de examen de diciembre empiezan el 07/12.', 'calendario.html');
echo "Listo: se crearon 2 avisos de prueba para el DNI $dni.\n";
