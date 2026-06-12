<?php
//POST HTTP REQUEST NODE-RED
include 'recepcion_http_request_post.php';

// Verificar que exista el campo data y api_key
if (!isset($request['data']) || !isset($request['api_key'])) {
    http_response_code(400);
    echo json_encode(["error" => "JSON inválido o faltan datos"]);
    exit;
}

// Verificar API key
if ($request['api_key'] !== "Corona1234$") {
    http_response_code(401);
    echo json_encode(["error" => "API key inválida"]);
    exit;
}

// Obtener los datos
$datos = $request['data'];

// Si es un objeto único, convertirlo a array para procesamiento uniforme
if (!is_array($datos) || !isset($datos[0])) {
    $datos = [$datos];
}

// Insertar datos
foreach ($datos as $row) {
    $nro_oc = $row['OC'] ?? '';
    $numero_item = $row['NUMEROITEM'] ?? '';
    $codigo = $row['CODIGO'] ?? '';
    $articulo = $row['ARTICULO'] ?? '';
    $cantidad = $row['CANTIDAD'] ?? '';
    $uni_med = $row['UNI_MED'] ?? '';
    $pu = $row['PU'] ?? '';
    $total_item = $row['TOTAL_ITEM'] ?? '';
    $fecha_entrega = $row['fechaentrega'] ?? '';
    
    // Convertir fecha si es necesario
    if (!empty($fecha_entrega)) {
        // Si la fecha ya viene en formato YYYY-MM-DD, la usamos directamente
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_entrega)) {
            // Ya está en formato correcto
        } else {
            // Intentar convertir desde otros formatos
            $dt = DateTime::createFromFormat('d/m/Y', $fecha_entrega);
            if ($dt) {
                $fecha_entrega = $dt->format('Y-m-d');
            } else {
                // Intentar con ISO
                $dt = new DateTime($fecha_entrega);
                if ($dt) {
                    $fecha_entrega = $dt->format('Y-m-d');
                } else {
                    $fecha_entrega = null;
                }
            }
        }
    } else {
        $fecha_entrega = null;
    }
    
    // Verificar si el registro ya existe
    $checkSql = "SELECT 1 FROM detalle_oc WHERE nro_oc = ? AND codigo = ? LIMIT 1";
    $stmt = $mysqli->prepare($checkSql);
    $stmt->bind_param("ss", $nro_oc, $codigo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Si NO existe, lo insertamos (usando prepared statements para seguridad)
    if ($result && $result->num_rows === 0) {
        $sql = "INSERT INTO detalle_oc (nro_oc, numero_item, codigo, articulo, cantidad, uni_med, pu, total_item, fecha_entrega) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sssssssss", $nro_oc, $numero_item, $codigo, $articulo, $cantidad, $uni_med, $pu, $total_item, $fecha_entrega);
        $stmt->execute();
    }
}

$mysqli->close();
echo json_encode(["success" => true, "message" => "Sinc. Detalle OC OK"]);