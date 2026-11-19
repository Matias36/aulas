<?php
require_once __DIR__ . '/no_cache.php';
// Lista de carreras para el desplegable de "Crear cuenta"
require __DIR__ . '/conexion.php';

$filas = $pdo->query('SELECT id, nombre FROM carreras ORDER BY nombre')->fetchAll();
$salida = [];
foreach ($filas as $f) $salida[] = ['id' => (int)$f['id'], 'nombre' => $f['nombre']];
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
