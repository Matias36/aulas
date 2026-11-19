<?php
require_once __DIR__ . '/no_cache.php';
// Crear avisos. El panel de administrador va a usar estas funciones cada vez que cambie un horario.

// Un aviso para una persona
function notificar_usuario($pdo, $usuarioId, $mensaje, $enlace = null) {
    $pdo->prepare('INSERT INTO notificaciones (usuario_id, mensaje, enlace) VALUES (?, ?, ?)')
        ->execute([(int)$usuarioId, $mensaje, $enlace]);
}

// Un aviso para cada persona que sigue alguno de esos horarios (una sola vez por persona).
// Devuelve cuántas personas fueron avisadas.
function notificar_seguidores($pdo, $horarioIds, $mensaje, $enlace = null) {
    $ids = array_values(array_unique(array_map('intval', $horarioIds)));
    if (!$ids) return 0;
    $marcas = implode(',', array_fill(0, count($ids), '?'));
    $st = $pdo->prepare('SELECT DISTINCT usuario_id FROM seguimientos WHERE horario_id IN (' . $marcas . ')');
    $st->execute($ids);
    $n = 0;
    foreach ($st->fetchAll() as $f) { notificar_usuario($pdo, $f['usuario_id'], $mensaje, $enlace); $n++; }
    return $n;
}
