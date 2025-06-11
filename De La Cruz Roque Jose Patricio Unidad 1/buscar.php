// Inicio del script PHP
<?php
// Redirección a otra página
header('Content-Type: application/json');

$items = [
  ["nombre" => "Consultoría Estratégica", "url" => "servicios.html"],
  ["nombre" => "Soporte Técnico", "url" => "servicios.html"],
  ["nombre" => "Desarrollo Web", "url" => "servicios.html"],
  ["nombre" => "Software Empresarial", "url" => "productos.html"],
  ["nombre" => "App Móvil", "url" => "productos.html"],
  ["nombre" => "Herramientas Cloud", "url" => "productos.html"]
];

$q = isset($_GET['q']) ? strtolower(trim($_GET['q'])) : '';
$resultados = [];

if ($q !== '') {
  foreach ($items as $item) {
    if (stripos($item['nombre'], $q) !== false) {
      $resultados[] = $item;
    }
  }
}

echo json_encode($resultados);