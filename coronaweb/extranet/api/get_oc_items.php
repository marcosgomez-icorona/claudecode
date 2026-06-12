<?php
// api/get_oc_items.php

// Asegura que este script no se cachee y que devuelva JSON
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

// Incluir los archivos necesarios
// Asegúrate de que las rutas sean correctas desde la ubicación de este script
include_once '../conexiones/conexion.php'; // Ajusta la ruta si es necesario
include_once '../funciones/funciones.php'; // Si necesitas FechaDma() aquí antes de pasar datos
include_once '../controller/ordenes_compra.php'; // Incluye el archivo con la función getDetalleOcItems

// Conectar a la base de datos
$mysqli = conexion_db();
if ($mysqli->connect_errno) {
    echo json_encode(['error' => 'Error de conexión a la base de datos: ' . $mysqli->connect_error]);
    exit();
}

// Obtener el número de Orden de Compra de la solicitud AJAX
$nro_oc = $_GET['nro_oc'] ?? ''; // Usamos GET ya que es una solicitud de lectura

if (empty($nro_oc)) {
    echo json_encode(['error' => 'Número de Orden de Compra no proporcionado.']);
    exit();
}

// Llamar a la función para obtener los ítems
$items = getDetalleOcItems($nro_oc);

// Cerrar la conexión a la base de datos
$mysqli->close();

// Devolver los datos en formato JSON
echo json_encode(['success' => true, 'items' => $items]);

?>