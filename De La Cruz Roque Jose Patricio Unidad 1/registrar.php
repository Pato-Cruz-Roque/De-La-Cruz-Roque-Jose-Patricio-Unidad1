// Inicio del script PHP
<?php
// Inclusión de archivo externo
require_once 'conexion.php';

// Validación básica
// Verifica si se recibió una solicitud POST
if (!isset($_POST['nombre'], $_POST['email'], $_POST['telefono'], $_POST['password'])) {
    echo "faltan_datos";
    exit;
}

// Validación de reCAPTCHA
$captcha = $_POST['g-recaptcha-response'] ?? '';

if (!$captcha) {
    echo "recaptcha_faltante";
    exit;
}

$secretKey = '6LfYCFwrAAAAAJb23HRCQnMaQryV7Qpv6phwKPNj'; // ← clave secreta rebridnanda por google recaptcha pero la secretaaaaa
$ip = $_SERVER['REMOTE_ADDR'];
$verifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha&remoteip=$ip";

$response = file_get_contents($verifyUrl);
$responseKeys = json_decode($response, true);

if (!$responseKeys["success"]) {
    echo "recaptcha_invalido";
    exit;
}

// Datos del usuario
$nombre = trim($_POST['nombre']);
$correo = trim($_POST['email']);
$telefono = trim($_POST['telefono']);
$password = $_POST['password'];

// Verificar si el correo ya está registrado
// Conexión a la base de datos
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "correo_existente";
    exit;
}

// Encriptar contraseña
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insertar usuario
// Conexión a la base de datos
$stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, telefono, contrasena) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nombre, $correo, $telefono, $hash);

if ($stmt->execute()) {
    echo "registro_exitoso";
} else {
    echo "error_registro";
}
?>
