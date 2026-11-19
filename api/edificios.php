<?php
require_once __DIR__ . '/no_cache.php';
// Devuelve los edificios con la misma forma que data/edificios.json
require __DIR__ . '/conexion.php';

try {
    $filas = $pdo->query('SELECT id, nombre, foto, color1, color2 FROM edificios ORDER BY orden, nombre')->fetchAll();
} catch (PDOException $e) {
    responder_error(500, 'Error al leer los edificios.');
}

$salida = [];
foreach ($filas as $f) {
    $salida[] = [
        'id'     => $f['id'],
        'nombre' => $f['nombre'],
        'foto'   => $f['foto'],
        'color'  => [$f['color1'], $f['color2']],
    ];
}
echo json_encode($salida, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
