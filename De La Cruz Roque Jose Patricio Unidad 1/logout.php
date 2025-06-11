// Inicio del script PHP
<?php
// Inicia la sesión del usuario
session_start();
session_unset();
session_destroy();
// Redirección a otra página
header("Location: login.html");
exit;
