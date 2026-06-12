<?php
//POST HTTP REQUEST NODE-RED
include 'recepcion_http_request_post.php';

// Insertar datos
foreach ($request['data'] as $row) {
   /*
    ID,OC AS nro_oc,CONVERT(date, LEFT(FECHAACTUAL, 8)) AS fecha_emision,DETALLE AS detalle,PLAZOENTREGA AS plazo_entrega,
    formapago AS condicion_pago, total_doc AS total, nom_PROVEEDOR AS proveedor,cod_ORIGINANTE AS originante
   */
    $proveedor = $row['proveedor'] ?? '';
    $originante = $row['originante'] ?? '';
    $nro_oc = $row['nro_oc'] ?? '';
    $fecha_emision = $row['fecha_emision'] ?? '';
    // para solucionar cuando viene como formato T03:00:00.000Z    
    /*
    if (!empty($fecha_emision)) {
        $dt = new DateTime($fecha_emision);
        $fecha_emision = $dt->format('Y-m-d');
    } else {
        $fecha_emision = null; 
    } 
    */
    $fecha = DateTime::createFromFormat('Ymd', $fecha_emision);
    $fechaformateada = $fecha->format('Y-m-d');
    
    $detalle = $row['detalle'] ?? '';
    $plazo_entrega = $row['plazo_entrega'] ?? '';
    $condicion_pago = $row['condicion_pago'] ?? '';
    $total = $row['total'] ?? '';
   
    // Verificar si el CUIT ya existe
    $checkSql = "SELECT nro_oc, fecha_emision, detalle, plazo_entrega, condicion_pago, total, proveedor,originante
                 FROM ordenes_compra WHERE nro_oc = '$nro_oc' LIMIT 1";
    $result = $mysqli->query($checkSql);
    $row=$result->fetch_assoc();

    // Si NO existe, lo insertamos
    if ($result && $result->num_rows === 0) {
        $sql = "INSERT INTO ordenes_compra (nro_oc, fecha_emision, detalle, plazo_entrega, condicion_pago, total, proveedor, originante) 
                VALUES ('$nro_oc', '$fechaformateada', '$detalle', '$plazo_entrega', '$condicion_pago', '$total', '$proveedor', '$originante')";
        $mysqli->query($sql);
        
    }else{
        if($nro_oc === $row['nro_oc'] and ($total <> $row['total'] or $proveedor <> $row['proveedor'] or $originante <> $row['originante'])){
                $sql_update = " update ordenes_compra SET   fecha_emision = '$fechaformateada',detalle = '$detalle', plazo_entrega = '$plazo_entrega',
                                condicion_pago = '$condicion_pago', total = '$total', proveedor = '$proveedor', originante = '$originante'
                                WHERE nro_oc = '$nro_oc' ";                                 
                $mysqli->query($sql_update);
            } 
           
    }

}

$mysqli->close();
echo json_encode(["success" => true, "message" => "Sinc. OC OK"]);
