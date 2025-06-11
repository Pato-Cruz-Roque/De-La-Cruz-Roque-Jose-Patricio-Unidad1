// Inicio del script PHP
<?php
// Inicia la sesión del usuario
session_start();
// Inclusión de archivo externo
require_once 'conexion.php';

if (!isset($_SESSION['email'])) {
    http_response_code(403);
    exit;
}

$email = $_SESSION['email'];
$isAdmin = $email === 'admin@gmail.com';

if ($isAdmin) {
    // Admin: listar usuarios con sus correos
// Conexión a la base de datos
    $stmt = $conn->prepare("
        SELECT u.id AS usuario_id, u.correo
        FROM chats c
        INNER JOIN usuarios u ON c.usuario_id = u.id
        GROUP BY c.usuario_id
        ORDER BY MAX(c.fecha) DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $usuarios = [];

    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }

    echo json_encode(["admin" => true, "usuarios" => $usuarios]);
} else {
    // Usuario: ver sus propios mensajes
    $usuario_id = $_SESSION['usuario_id'];
// Conexión a la base de datos
    $stmt = $conn->prepare("SELECT asunto, mensaje, fecha FROM chats WHERE usuario_id = ? ORDER BY fecha ASC");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $mensajes = [];

    while ($row = $result->fetch_assoc()) {
        $mensajes[] = $row;
    }

    echo json_encode(["admin" => false, "mensajes" => $mensajes]);
}
