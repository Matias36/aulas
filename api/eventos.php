<?php
require_once __DIR__ . '/no_cache.php';
// Fechas del calendario académico. Cada una tiene "fecha" (inicio) y, si dura varios días, "fin".
require __DIR__ . '/conexion.php';

try {
    $filas = $pdo->query('SELECT id, titulo, fecha_inicio, fecha_fin FROM eventos ORDER BY fecha_inicio, titulo')->fetchAll();
} catch (PDOException $e) {
    responder_error(500, 'Error al leer las fechas.');
}

$salida = [];
foreach ($filas as $f) {
    $salida[] = [
        'id'     => (int)$f['id'],
        'fecha'  => $f['fecha_inicio'],
        'fin'    => $f['fecha_fin'],
        'titulo' => $f['titulo'],
    ];
}
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
