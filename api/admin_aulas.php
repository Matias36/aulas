<?php

require_once __DIR__ . '/no_cache.php';
// Lista de aulas para los desplegables del panel (sin los bloques informativos, como la Biblioteca)
require __DIR__ . '/admin_lib.php';
exigir_admin($pdo);

$st = $pdo->query("SELECT a.id, a.etiqueta, a.edificio_id, e.nombre AS edificio
                     FROM aulas a JOIN edificios e ON e.id = a.edificio_id
                    WHERE a.tipo <> 'info'
                    ORDER BY e.orden, a.piso, a.orden");
$salida = [];
foreach ($st->fetchAll() as $f) {
    $salida[] = ['id' => $f['id'], 'etiqueta' => $f['etiqueta'], 'edificioId' => $f['edificio_id'], 'edificio' => $f['edificio']];
}
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
