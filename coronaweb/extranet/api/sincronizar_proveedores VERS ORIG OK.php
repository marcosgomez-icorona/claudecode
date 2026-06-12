<?php
// POST HTTP REQUEST NODE-RED
include_once '../controller/usuarios.php';
include 'recepcion_http_request_post.php';

// Configurar MySQL para usar UTF-8
$mysqli->set_charset("utf8mb4");

foreach ($request['data'] as $row) {
    $NOMBRE    = $row['NOMBRE']    ?? '';
    $CUIT      = $row['CUIT']      ?? '';
    $DOMICILIO = $row['DOMICILIO'] ?? '';
    $MAIL = $row['MAIL'] ?? '';

    // Verificar si el proveedor ya existe
    $checkSql = $mysqli->prepare("SELECT * FROM proveedores WHERE nombre = ? LIMIT 1");
    $checkSql->bind_param("s", $NOMBRE);
    $checkSql->execute();
    $result = $checkSql->get_result();
    $row_result = $result->fetch_assoc();

    // Si NO existe, lo insertamos
    if ($result && $result->num_rows === 0) {
        $stmt = $mysqli->prepare("INSERT INTO proveedores (cuit, nombre, domicilio, mail) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $CUIT, $NOMBRE, $DOMICILIO, $MAIL);
        $stmt->execute();
    } else {
        // Si domicilio está vacío, actualizarlo
        if (empty($row_result['domicilio'])) {
            $stmt = $mysqli->prepare("UPDATE proveedores SET domicilio = ? WHERE cuit = ?");
            $stmt->bind_param("ss", $DOMICILIO, $CUIT);
            $stmt->execute();
        }
        if (empty($row_result['mail'])) {
            $stmt = $mysqli->prepare("UPDATE proveedores SET mail = ? WHERE cuit = ?");
            $stmt->bind_param("ss", $MAIL, $CUIT);
            $stmt->execute();
        }
        
        // Si el nombre coincide pero el CUIT es diferente, actualizar CUIT
        if ($row_result['nombre'] == $NOMBRE && $row_result['cuit'] != $CUIT) {
            $stmt = $mysqli->prepare("UPDATE proveedores SET cuit = ? WHERE nombre = ?");
            $stmt->bind_param("ss", $CUIT, $NOMBRE);
            $stmt->execute();
        }
    }

    // Chequeo usuario
    $checkusuario = $mysqli->prepare("SELECT * FROM usuarios WHERE usuario = ? LIMIT 1");
    $checkusuario->bind_param("s", $CUIT);
    $checkusuario->execute();
    $result = $checkusuario->get_result();

    if ($result && $result->num_rows === 0) {
        alta_usuario($CUIT, $CUIT, $NOMBRE, 'PROVEEDOR');
    }
}

$mysqli->close();
echo json_encode(["success" => true, "message" => "Sinc. Proveedores OK"]);
