// Inicio del script PHP
<?php
// Inclusión de archivo externo
require_once 'conexion.php';
// Inicia la sesión del usuario
session_start();

// Si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $nueva = $_POST['password'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if ($nueva !== $confirmar || strlen($nueva) < 6) {
        $error = "Las contraseñas no coinciden o son demasiado cortas.";
    } else {
        // Verificar token
// Conexión a la base de datos
        $stmt = $conn->prepare("SELECT r.usuario_id FROM recuperaciones r
                                WHERE r.token = ? AND r.utilizado = 0 AND r.fecha_creacion >= NOW() - INTERVAL 1 HOUR");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $error = "El enlace ya expiró o es inválido.";
        } else {
            $usuario_id = $result->fetch_assoc()['usuario_id'];
            $hash = password_hash($nueva, PASSWORD_DEFAULT);

            // Actualizar contraseña
// Conexión a la base de datos
            $stmt = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE id = ?");
            $stmt->bind_param("si", $hash, $usuario_id);
            $stmt->execute();

            // Marcar token como utilizado
// Conexión a la base de datos
            $stmt = $conn->prepare("UPDATE recuperaciones SET utilizado = 1 WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();

            $exito = true;
        }
    }
}

// Si solo estamos accediendo con GET para mostrar el formulario
$token_valido = false;
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];
// Conexión a la base de datos
    $stmt = $conn->prepare("SELECT id FROM recuperaciones WHERE token = ? AND utilizado = 0 AND fecha_creacion >= NOW() - INTERVAL 1 HOUR");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $token_valido = $result->num_rows > 0;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña - Patoservices</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto mt-20 bg-white p-8 rounded shadow text-center">
// Inicio del script PHP
        <?php if (isset($exito) && $exito): ?>
            <h2 class="text-2xl font-bold text-green-600 mb-4">¡Contraseña actualizada!</h2>
            <p class="text-gray-700">Ahora puedes <a href="login.html" class="text-blue-600 font-medium">iniciar sesión</a>.</p>
// Inicio del script PHP
        <?php elseif (isset($error)): ?>
            <h2 class="text-2xl font-bold text-red-600 mb-4">Error</h2>
            <p class="text-gray-700"><?= $error ?></p>
// Inicio del script PHP
        <?php elseif ($token_valido): ?>
            <h2 class="text-xl font-bold mb-6">Nueva Contraseña</h2>
            <form method="POST" class="space-y-4 text-left">
                <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']) ?>">
                <div>
                    <label class="block text-sm font-medium">Nueva contraseña</label>
// Inclusión de archivo externo
                    <input type="password" name="password" required class="w-full border px-3 py-2 rounded mt-1">
                </div>
                <div>
                    <label class="block text-sm font-medium">Confirmar contraseña</label>
// Inclusión de archivo externo
                    <input type="password" name="confirmar" required class="w-full border px-3 py-2 rounded mt-1">
                </div>
                <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Actualizar</button>
            </form>
// Inicio del script PHP
        <?php else: ?>
            <h2 class="text-xl font-bold text-red-600 mb-4">Enlace inválido o expirado</h2>
            <p class="text-gray-600">Solicita una nueva recuperación desde la <a href="recuperar.html" class="text-blue-600 font-medium">página de recuperación</a>.</p>
// Inicio del script PHP
        <?php endif; ?>
    </div>
</body>
</html>
