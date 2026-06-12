<?php
//POST HTTP REQUEST NODE-RED
include 'recepcion_http_request_post.php';

//chequeo 
$checkStmt = $mysqli->prepare("SELECT 1 FROM detalle_constancia_servicio WHERE constancia = ? LIMIT 1");

// Insertar datos
$sql = "INSERT IGNORE INTO detalle_constancia_servicio
        (id,constancia,numeroitem,codigo,nom_producto,bonificacion,descuento,cantidad,
         cantidaddoc,cantidadpendienteoc,cumplido,porcentaje,estado,uni_med,pu,total_item,totalr,
         fechaentrega,representante,id_solicitud,sector,cumplidohastahoy,cumplidohoy,memo,memoitem)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $mysqli->prepare($sql);

foreach ($request['data'] as $row) {

    $vals = [
        $row['ID']                  ?? '',
        $row['constancia']          ?? '',
        $row['NUMEROITEM']          ?? '',
        $row['CODIGO']              ?? '',
        $row['NOM_PRODUCTO']        ?? '',
        $row['BONIFICACION']        ?? '',
        $row['DESCUENTO']           ?? '',
        $row['CANTIDAD']            ?? '',
        $row['CANTIDADOC']          ?? '',
        $row['CANTIDADPENDIENTEOC'] ?? '',
        $row['CUMPLIDO']            ?? '',
        $row['PORCENTAJE']          ?? '',
        $row['ESTADO']              ?? '',
        $row['UNI_MED']             ?? '',
        $row['PU']                  ?? '',
        $row['TOTAL_ITEM']          ?? '',
        $row['TOTALTR']             ?? '',
        $row['FECHAENTREGA']        ?? '',
        $row['REPRESENTANTE']       ?? '',
        $row['ID_SOLICITUD']        ?? '',
        $row['SECTOR']              ?? '',
        $row['CUMPLIDOHASTAHOY']    ?? '',
        $row['CUMPLIDOHOY']         ?? '',
        $row['MEMO']                ?? '',
        $row['MEMOITEM']            ?? '',
    ];

    // Verificar si la pesada ya existe
    $checkStmt->bind_param("s", $vals[1]);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows === 0) {
        // No existe, insertar
        // Tipos de datos: 25 columnas => 25 símbolos (s = string, i = int, d = double)
        $stmt->bind_param(str_repeat('s', 25), ...$vals);
        $stmt->execute();
    }
}

$stmt->close();


$mysqli->close();
echo json_encode(["success" => true, "message" => "Sinc Constancias de Serv. OK"]);
