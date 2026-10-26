<?php
// Reservar o bloquear un aula para un día puntual (charla, exposición, reunión...).
//   GET                          -> próximas reservas
//   POST {accion:"crear", aula, fecha, inicio, fin, titulo, organizador, forzar}
//   POST {accion:"borrar", id}
require __DIR__ . '/admin_lib.php';
$admin = exigir_admin($pdo);
$uid = (int)$admin['id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $st = $pdo->query(
        'SELECT h.id, h.fecha, h.inicio, h.fin, h.titulo, h.organizador, a.etiqueta AS aula, e.nombre AS edificio
           FROM horarios h
           JOIN aulas a ON a.id = h.aula_id
           JOIN edificios e ON e.id = a.edificio_id
          WHERE h.comision_id IS NULL AND h.fecha >= CURDATE()
       ORDER BY h.fecha, h.inicio, h.id'
    );
    $salida = [];
    foreach ($st->fetchAll() as $r) {
        $salida[] = [
            'id' => (int)$r['id'], 'cuando' => fecha_larga($r['fecha']) . ' · ' . hhmm($r['inicio']) . ' a ' . hhmm($r['fin']),
            'lugar' => $r['edificio'] . ' ' . $r['aula'], 'titulo' => $r['titulo'], 'organizador' => $r['organizador'],
        ];
    }
    echo json_encode($salida, JSON_UNESCAPED_UNICODE);
    exit;
}

$in = leer_json();
$accion = isset($in['accion']) ? $in['accion'] : '';

if ($accion === 'crear') {
    $aulaId = isset($in['aula']) ? (string)$in['aula'] : '';
    $fecha = isset($in['fecha']) ? (string)$in['fecha'] : '';
    $ini = isset($in['inicio']) ? (string)$in['inicio'] : '';
    $fin = isset($in['fin']) ? (string)$in['fin'] : '';
    $titulo = cortar(trim(isset($in['titulo']) ? (string)$in['titulo'] : ''), 150);
    $org = cortar(trim(isset($in['organizador']) ? (string)$in['organizador'] : ''), 120);
    if ($titulo === '') responder_error(400, 'Escribí un título para la reserva.');
    if (!fecha_valida($fecha)) responder_error(400, 'Elegí una fecha.');
    if ($fecha < date('Y-m-d')) responder_error(400, 'Esa fecha ya pasó.');
    $dia = dia_de_fecha($fecha);
    validar_franja($dia, $ini, $fin);
    $a = aula_datos($pdo, $aulaId);

    $hay = conflictos($pdo, $aulaId, $dia, $fecha, $ini . ':00', $fin . ':00', [], null);
    if ($hay && empty($in['forzar'])) responder_conflictos($hay);

    $pdo->prepare('INSERT INTO horarios (aula_id, dia, inicio, fin, comision_id, titulo, organizador, fecha) VALUES (?, ?, ?, ?, NULL, ?, ?, ?)')
        ->execute([$aulaId, $dia, $ini . ':00', $fin . ':00', $titulo, $org !== '' ? $org : null, $fecha]);
    registrar_cambio($pdo, $uid, 'reservar', 'Reservó ' . $a['edificio'] . ' ' . $a['etiqueta'] . ' el ' . fecha_larga($fecha) . ' de ' . $ini . ' a ' . $fin . ': ' . $titulo . '.');
    echo json_encode(['ok' => true]);
    exit;
}

if ($accion === 'borrar') {
    $id = isset($in['id']) ? (int)$in['id'] : 0;
    if ($id <= 0) responder_error(400, 'Datos inválidos.');
    $st = $pdo->prepare('SELECT titulo FROM horarios WHERE id = ? AND fecha IS NOT NULL AND comision_id IS NULL');
    $st->execute([$id]);
    $r = $st->fetch();
    if (!$r) responder_error(404, 'Esa reserva ya no existe.');
    $pdo->prepare('DELETE FROM horarios WHERE id = ? AND fecha IS NOT NULL AND comision_id IS NULL')->execute([$id]);
    registrar_cambio($pdo, $uid, 'borrar_reserva', 'Borró la reserva: ' . $r['titulo'] . '.');
    echo json_encode(['ok' => true]);
    exit;
}

responder_error(400, 'Acción desconocida.');
