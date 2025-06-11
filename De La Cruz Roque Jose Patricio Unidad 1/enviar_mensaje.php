// Inicio del script PHP
<?php
// Inicia la sesión del usuario
session_start();
// Inclusión de archivo externo
require_once 'conexion.php';

// Validar sesión y mensaje
// Verifica si se recibió una solicitud POST
if (!isset($_SESSION['usuario_id']) || !isset($_POST['mensaje'])) {
    http_response_code(400);
    echo "Datos incompletos";
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$mensaje = trim($_POST['mensaje']);

if ($mensaje === "") {
    echo "Mensaje vacío";
    exit;
}

// Debug opcional
// echo "Usuario ID: $usuario_id - Mensaje: $mensaje";

// Conexión a la base de datos
$stmt = $conn->prepare("INSERT INTO chats (usuario_id, asunto, mensaje, fecha) VALUES (?, 'mensaje', ?, NOW())");

if (!$stmt) {
    http_response_code(500);
// Conexión a la base de datos
    echo "Error al preparar la consulta: " . $conn->error;
    exit;
}

$stmt->bind_param("is", $usuario_id, $mensaje);

if (!$stmt->execute()) {
    http_response_code(500);
    echo "Error al ejecutar la consulta: " . $stmt->error;
    exit;
}

echo "ok";
