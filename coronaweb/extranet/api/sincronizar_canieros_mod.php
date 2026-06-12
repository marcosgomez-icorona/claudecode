<?php
/*
// Al inicio
error_log("Request received: " . $input);
// Recibir y decodificar JSON
$input = file_get_contents('php://input');
$request = json_decode($input, true);

if ($request === null || !isset($request['data'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}
*/
$input = file_get_contents('php://input');
$request = json_decode($input, true);

// Verificar si viene codificado
if (isset($request['data_b64'])) {
    $decodedData = base64_decode($request['data_b64']);
    $request['data'] = json_decode($decodedData, true);
}

include_once '../controller/usuarios.php';
include 'recepcion_http_request_post.php';

foreach ($request['data'] as $row) {
    $id_caniero = $row['id_caniero'] ?? '';
    $razon_social = $row['razon_social'] ?? '';
    $cuit = $row['cuit'] ?? '';
    $grupo = $row['grupo'] ?? '';
    
    // Usar consultas preparadas para SELECT
    $checkSql = "SELECT id_caniero, razon_social, cuit, grupo FROM canieros WHERE id_caniero = ? LIMIT 1";
    $stmt = $mysqli->prepare($checkSql);
    $stmt->bind_param("s", $id_caniero);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // INSERT con consulta preparada
        $insertSql = "INSERT INTO canieros (id_caniero, razon_social, cuit, grupo) VALUES (?, ?, ?, ?)";
        $stmtInsert = $mysqli->prepare($insertSql);
        $stmtInsert->bind_param("ssss", $id_caniero, $razon_social, $cuit, $grupo);
        $stmtInsert->execute();
        $stmtInsert->close();
    } else {
        $row_result = $result->fetch_assoc();
        if ($row_result['razon_social'] == $razon_social && $row_result['cuit'] != $cuit) {
            // UPDATE con consulta preparada
            $updateSql = "UPDATE canieros SET cuit = ?, grupo = ? WHERE id_caniero = ?";
            $stmtUpdate = $mysqli->prepare($updateSql);
            $stmtUpdate->bind_param("sss", $cuit, $grupo, $id_caniero);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        }
    }
    $stmt->close();
    
    // Chequeo usuario (también con prepared statement)
    $checkusuario = $mysqli->prepare("SELECT 1 FROM usuarios WHERE usuario = ? LIMIT 1");
    $checkusuario->bind_param("s", $cuit);
    $checkusuario->execute();
    $resultUsuario = $checkusuario->get_result();
    
    if ($resultUsuario->num_rows === 0) {
        alta_usuario($cuit, $cuit, $razon_social, 'CANIERO');
    }
    $checkusuario->close();
}

$mysqli->close();

//falla


// Antes de cada operación importante
error_log("Processing ID: " . $id_caniero);

// Si hay error
error_log("Error en query: " . $mysqli->error);

// Respuesta exitosa
header('Content-Type: application/json');
echo json_encode(["success" => true, "message" => "Sinc. Canieros OK"]);
?>