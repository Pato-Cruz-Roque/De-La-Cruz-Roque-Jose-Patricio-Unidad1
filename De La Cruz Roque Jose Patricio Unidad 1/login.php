// Inicio del script PHP
<?php
// Inicia la sesión del usuario
session_start();
// Inclusión de archivo externo
require_once 'conexion.php';

// Validación de entrada
// Verifica si se recibió una solicitud POST
if (!isset($_POST['email'], $_POST['password'])) {
    http_response_code(400);
    echo "faltan_datos";
    exit;
}

$email = trim($_POST['email']);
$password = $_POST['password'];

// Buscar usuario por correo
// Conexión a la base de datos
$stmt = $conn->prepare("SELECT id, correo, contrasena, nombre FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "usuario_no_encontrado";
    exit;
}

$usuario = $result->fetch_assoc();

// Validar contraseña (usa password_hash() en registro)
if (!password_verify($password, $usuario['contrasena'])) {
    echo "contrasena_incorrecta";
    exit;
}

// Iniciar sesión
$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['email'] = $usuario['correo'];
$_SESSION['nombre'] = $usuario['nombre'];
echo $email === 'admin@gmail.com' ? "admin" : "usuario";
?>
