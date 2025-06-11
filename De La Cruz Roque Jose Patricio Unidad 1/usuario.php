// Inicio del script PHP
<?php
// Inicia la sesión del usuario
session_start();

// Redirección a otra página
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        "logeado" => false
    ]);
    exit;
}

echo json_encode([
    "logeado" => true,
    "nombre" => $_SESSION['nombre'],
    "email" => $_SESSION['email']
]);
