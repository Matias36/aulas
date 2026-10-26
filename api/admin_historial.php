<?php
// Últimos cambios hechos desde el panel de administrador
require __DIR__ . '/admin_lib.php';
exigir_admin($pdo);

$st = $pdo->query(
    'SELECT r.accion, r.detalle, r.creado_en, u.nombre, u.apellido
       FROM registro_cambios r LEFT JOIN usuarios u ON u.id = r.usuario_id
   ORDER BY r.id DESC LIMIT 60'
);
$salida = [];
foreach ($st->fetchAll() as $r) {
    $salida[] = [
        'cuando' => substr($r['creado_en'], 8, 2) . '/' . substr($r['creado_en'], 5, 2) . ' ' . substr($r['creado_en'], 11, 5),
        'quien' => trim($r['nombre'] . ' ' . $r['apellido']),
        'detalle' => $r['detalle'],
    ];
}
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
