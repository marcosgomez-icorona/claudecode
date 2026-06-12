<?php
//conexion a la DB
include '../conexiones/conexion.php';

//POST HTTP NODE-RED CERTIFICACION DE SERVICIOS

$API_KEY = "Corona1234$";

// Obtener datos crudos del cuerpo
$json = file_get_contents('php://input');

// Guardar log por si hay problemas
//file_put_contents("log_input.txt", $json);

// Decodificar JSON
$request = json_decode($json, true);

// Validar JSON
if (!$request || !isset($request['api_key']) || !isset($request['data'])) {
    http_response_code(400);
    echo json_encode(["error" => "JSON inválido o faltan datos"]);
    exit;
}

// Validar clave
if ($request['api_key'] !== $API_KEY) {
    http_response_code(401);
    echo json_encode(["error" => "Acceso no autorizado", "recibido" => $request['api_key']]);
    exit;
}
    