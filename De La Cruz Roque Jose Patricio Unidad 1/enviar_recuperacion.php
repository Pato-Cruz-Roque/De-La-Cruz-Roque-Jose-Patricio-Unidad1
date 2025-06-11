// Inicio del script PHP
<?php
// Envío de correo electrónico
use PHPMailer\PHPMailer\PHPMailer;
// Envío de correo electrónico
use PHPMailer\PHPMailer\Exception;

// Inclusión de archivo externo
require 'PHPMailer/src/PHPMailer.php';
// Inclusión de archivo externo
require 'PHPMailer/src/SMTP.php';
// Inclusión de archivo externo
require 'PHPMailer/src/Exception.php';
// Inclusión de archivo externo
require_once 'conexion.php';

// Verifica si se recibió una solicitud POST
if (!isset($_POST['email'])) {
    echo "Correo no recibido.";
    exit;
}

$correo = trim($_POST['email']);

// Verificar si existe el correo
// Conexión a la base de datos
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Este correo no está registrado.";
    exit;
}

$usuario = $result->fetch_assoc();
$usuario_id = $usuario['id'];

// Generar token único
$token = bin2hex(random_bytes(32));

// Guardar token en base de datos
// Conexión a la base de datos
$stmt = $conn->prepare("INSERT INTO recuperaciones (usuario_id, token) VALUES (?, ?)");
$stmt->bind_param("is", $usuario_id, $token);
$stmt->execute();

// Enlace de recuperación
$url = "http://localhost/1%20UNIDAD%20GABRIEL/resetear_contraseña.php?token=$token";

// Configurar y enviar correo
// Envío de correo electrónico
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;

    // tu correo y clave de aplicación aquí:
    $mail->Username = '';
    $mail->Password = ''; // aca clave secreta no publica 

    $mail->setFrom('', 'Patoservices');
    $mail->addAddress($correo);
    $mail->isHTML(true);
    $mail->Subject = 'Restablecer tu contraseña - Patoservices';
    $mail->Body = "
        <h2>Recuperación de contraseña</h2>
        <p>Haz clic en el siguiente enlace para restablecer tu contraseña:</p>
        <a href='$url'>$url</a>
        <p>Este enlace es válido por un tiempo limitado.</p>
    ";

    $mail->send();
    echo "Se ha enviado un correo con el enlace para restablecer tu contraseña.";
} catch (Exception $e) {
    echo "Error al enviar el correo: {$mail->ErrorInfo}";
}
