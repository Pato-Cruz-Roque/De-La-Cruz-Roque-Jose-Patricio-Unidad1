// Inicio del script PHP
<?php
$host = "localhost";
$usuario = "root";
$clave = "";
$bd = "sistema_web";

// Conexión a la base de datos
$conn = new mysqli($host, $usuario, $clave, $bd);

// Conexión a la base de datos
if ($conn->connect_error) {
// Conexión a la base de datos
    die("Error de conexión: " . $conn->connect_error);
}

// Conexión a la base de datos
$conn->set_charset("utf8");
?>
