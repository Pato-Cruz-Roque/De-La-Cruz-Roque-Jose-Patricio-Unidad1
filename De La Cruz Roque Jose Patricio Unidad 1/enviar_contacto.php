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

if (
    empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['email']) ||
    empty($_POST['asunto']) || empty($_POST['mensaje'])
) {
    echo "Por favor, completa todos los campos obligatorios.";
    exit;
}

$nombre = htmlspecialchars(trim($_POST['nombre']));
$apellido = htmlspecialchars(trim($_POST['apellido']));
$email = trim($_POST['email']);
// Verifica si se recibió una solicitud POST
$telefono = isset($_POST['telefono']) ? htmlspecialchars(trim($_POST['telefono'])) : '';
$asunto = htmlspecialchars(trim($_POST['asunto']));
$mensaje = htmlspecialchars(trim($_POST['mensaje']));

// Composición del cuerpo del mensaje
$contenido = "
    <h2>Nuevo mensaje de contacto</h2>
    <p><strong>Nombre:</strong> $nombre $apellido</p>
    <p><strong>Correo:</strong> $email</p>
    <p><strong>Teléfono:</strong> $telefono</p>
    <p><strong>Asunto:</strong> $asunto</p>
    <p><strong>Mensaje:</strong><br>$mensaje</p>
";

// Envío de correo electrónico
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = ''; // ←  CORREO
    $mail->Password = '';           // ← CLAVE DE APLICACIÓN
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('', 'Formulario de Contacto');
    $mail->addAddress('', 'Patoservices');
    $mail->addReplyTo($email, "$nombre $apellido");

    $mail->isHTML(true);
    $mail->Subject = "Contacto Web: $asunto";
    $mail->Body    = $contenido;

    $mail->send();
    echo "¡Mensaje enviado correctamente! Te responderemos pronto.";
} catch (Exception $e) {
    echo "Error al enviar el mensaje: " . $mail->ErrorInfo;
}
