<?php
// Conexión a la base de datos. Todos los archivos de api/ la incluyen.
// XAMPP trae el usuario "root" sin contraseña: sirve para el prototipo local.
// (Antes de publicar de verdad, crear un usuario propio con contraseña.)

date_default_timezone_set('America/Argentina/Buenos_Aires');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function responder_error($codigo, $mensaje) {
    http_response_code($codigo);
    echo json_encode(['error' => $mensaje], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=if0_43272434_unm_aulas;charset=utf8mb4',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    responder_error(500, 'No se pudo conectar a la base de datos.');
}
