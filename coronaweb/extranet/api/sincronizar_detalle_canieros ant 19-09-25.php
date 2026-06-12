<?php
// POST HTTP REQUEST NODE-RED
include 'recepcion_http_request_post.php';

// Verificar conexión
if (!$mysqli || $mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(["error" => "Fallo en la conexión a la base de datos"]);
    exit;
}

// Preparar las sentencias UNA VEZ fuera del loop
$checkStmt = $mysqli->prepare("SELECT 1 FROM detalle_canieros WHERE pesada = ? LIMIT 1");
$insertStmt = $mysqli->prepare("INSERT INTO detalle_canieros (...) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$updateStmt = $mysqli->prepare("UPDATE detalle_canieros SET id_caniero = ?, razon_social = ?, grupo = ?, fechaindustrial = ? WHERE pesada = ?");

// Verificar que las sentencias se prepararon correctamente
if (!$checkStmt || !$insertStmt || !$updateStmt) {
    http_response_code(500);
    echo json_encode(["error" => "Error preparando sentencias: " . $mysqli->error]);
    exit;
}

// Insertar datos
foreach ($request['data'] as $row) {
    // Extraer datos con valores por defecto para evitar null
    $pesada         = $row['pesada']         ?? '';
    $id_caniero     = $row['id_caniero']     ?? '';
    $razon_social   = $row['razon_social']   ?? '';
    $grupo          = $row['grupo']          ?? '';
    $fechaindustrial= $row['fechaindustrial']?? '';
    // para solucionar cuando viene como formato T03:00:00.000Z    
    if (!empty($fechaindustrial)) {
        $dt = new DateTime($fechaindustrial);
        $fechaindustrial = $dt->format('d/m/Y');
        echo 'Fecha Industrial'.$fechaindustrial;
    } else {
        $fechaindustrial = null; 
    }
    $remito         = $row['remito']         ?? '';
    $tipo           = $row['tipo']           ?? '';
    $bruto          = $row['bruto']          ?? 0;
    $trash          = $row['trash']          ?? 0;
    $neto           = $row['netoreal']       ?? 0;
    $brix           = $row['brix']           ?? 0;
    $pol            = $row['pol']            ?? 0;
    $pureza         = $row['pureza']         ?? 0;
    $rendimiento    = $row['rendimiento']    ?? 0;

    // Verificar si la pesada ya existe
    $checkStmt->bind_param("s", $pesada);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows === 0) {
        // Insertar
        $insertStmt->bind_param("sssssssddddddd", 
            $pesada, $id_caniero, $razon_social, $grupo, $fechaindustrial,
            $remito, $tipo, $bruto, $trash, $neto,
            $brix, $pol, $pureza, $rendimiento
        );
        if (!$insertStmt->execute()) {
            error_log("Error insertando: " . $insertStmt->error);
        }
    } else {
        // Actualizar
        $updateStmt->bind_param("sssss", $id_caniero, $razon_social, $grupo, $fechaindustrial, $pesada);
        if (!$updateStmt->execute()) {
            error_log("Error actualizando: " . $updateStmt->error);
        }
    }
    
    // Limpiar y resetear después de cada iteración
    $checkStmt->free_result();
}

// Cierre
$checkStmt->close();
$insertStmt->close();
$updateStmt->close();
$mysqli->close();

echo json_encode(["success" => true, "message" => "Sinc. Det. Caña OK"]);
