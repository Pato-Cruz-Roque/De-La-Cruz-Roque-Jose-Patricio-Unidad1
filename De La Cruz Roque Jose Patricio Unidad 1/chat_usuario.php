// Inicio del script PHP
<?php
// Inicia la sesión del usuario
session_start();
// Inclusión de archivo externo
require_once 'conexion.php';

if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'admin@gmail.com') {
    http_response_code(403);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

if (!isset($_GET['usuario_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Falta usuario_id"]);
    exit;
}

$usuario_id = intval($_GET['usuario_id']);

// Conexión a la base de datos
$stmt = $conn->prepare("SELECT asunto, mensaje, fecha FROM chats WHERE usuario_id = ? ORDER BY fecha ASC");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$mensajes = [];
while ($row = $result->fetch_assoc()) {
    $mensajes[] = $row;
}

echo json_encode($mensajes);
