// Inicio del script PHP
<?php
// Inicia la sesión del usuario
session_start();
// Inclusión de archivo externo
require_once 'conexion.php';

if ($_SESSION['email'] !== 'admin@gmail.com') {
    http_response_code(403);
    exit("No autorizado");
}

// Verifica si se recibió una solicitud POST
if (!isset($_POST['mensaje'], $_POST['usuario_id'])) {
    http_response_code(400);
    exit("Datos faltantes");
}

$mensaje = trim($_POST['mensaje']);
$usuario_id = intval($_POST['usuario_id']);

// Conexión a la base de datos
$stmt = $conn->prepare("INSERT INTO chats (usuario_id, asunto, mensaje, fecha) VALUES (?, 'respuesta_admin', ?, NOW())");
$stmt->bind_param("is", $usuario_id, $mensaje);
$stmt->execute();

echo "ok";
