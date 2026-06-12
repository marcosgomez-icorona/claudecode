<?php
//POST HTTP REQUEST NODE-RED
include 'recepcion_http_request_post.php';

// Insertar datos
foreach ($request['data'] as $row) {
    /*
    descr_item,importe,[NUMERODOCUMENTO],[FECHAACTUAL],[nom_proveedor],[moneda],[egreso_valor],[total],VALORTOTAL,[comprador],
    id_egresovalores,COTIZACION,importe_cancelado,NroCertifGan, RetGan,NroCertifIIBB, RetIIBB,NroCertifIVA, RetIVA
    */
    $nro_op = $row['NUMERODOCUMENTO'] ?? ''; //
    $fechaactual = $row['FECHAACTUAL'] ?? '';   // 
    // para solucionar cuando viene como formato T03:00:00.000Z    
    $fecha = DateTime::createFromFormat('Ymd', $fechaactual);
    $fechaformateada = $fecha->format('Y-m-d');

    $importe = $row['importe'] ?? 0; //
    $descr_item = $row['descr_item'] ?? '';    //
    $comprador =  $row['comprador'] ?? '';    //
    $total = $row['VALORTOTAL'] ?? 0; //   
    $moneda = $row['moneda'] ?? '';    //
    $egreso_valor = $row['egreso_valor'] ?? '';   // 
    $id_egresovalores = $row['id_egresovalores'] ?? '';  //   
    $cotizacion = $row['COTIZACION'] ?? '';  //  
    $importe_cancelado = $row['importe_cancelado'] ?? ''; //    
    $NroCertifGan = $row['NroCertifGan'] ?? ''; //    
    $NroCertifIIBB = $row['NroCertifIIBB'] ?? ''; //    
    $NroCertifIVA = $row['NroCertifIVA'] ?? ''; //    
    $RetGan = $row['RetGan'] ?? 0;    //
    $RetIIBB = $row['RetIIBB'] ?? 0;  //  
    $RetIVA = $row['RetIVA'] ?? 0;   // 
    $proveedor = $row['nom_proveedor'] ?? ''; //    
    

    // Verificar si ya existe
    $checkSql = "SELECT nro_op, fecha, descr_item, importe, total, moneda, egreso_valor, id_egresovalores, proveedor, comprador, ultima_actualizacion, cotizacion,
                 importe_cancelado,NroCertifGan, RetGan,NroCertifIIBB, RetIIBB,NroCertifIVA, RetIVA
                 FROM orden_pago WHERE descr_item = '$descr_item' LIMIT 1";
    $result = $mysqli->query($checkSql);
    $row_result=$result->fetch_assoc();

    // Si NO existe, lo insertamos
    if ($result && $result->num_rows === 0) {
        $sql = "INSERT INTO orden_pago (nro_op ,fecha,descr_item,importe,total ,proveedor , comprador, moneda ,egreso_valor,id_egresovalores,cotizacion,
                importe_cancelado,NroCertifGan, RetGan,NroCertifIIBB, RetIIBB,NroCertifIVA, RetIVA)
                VALUES ('$nro_op', '$fechaformateada', '$descr_item', '$importe', '$total', '$proveedor', '$comprador', '$moneda', '$egreso_valor', '$id_egresovalores', '$cotizacion',
                        '$importe_cancelado','$NroCertifGan','$RetGan','$NroCertifIIBB','$RetIIBB', '$NroCertifIVA', '$RetIVA')";
        $mysqli->query($sql);
    }else{            
            if($RetGan > 0 or $RetIIBB > 0 or $RetIVA > 0){
                $sql_update = " update orden_pago SET   importe_cancelado = '$importe_cancelado',NroCertifGan = '$NroCertifGan', RetGan = '$RetGan',
                                NroCertifIIBB = '$NroCertifIIBB', RetIIBB = '$RetIIBB', NroCertifIVA = '$NroCertifIVA', RetIVA = '$RetIVA'
                                WHERE nro_op = '$nro_op' ";                                 
                $mysqli->query($sql_update);
            }  
            if($row_result['importe_cancelado']<>$importe_cancelado){
                $sql_update = " update orden_pago SET   importe_cancelado = '$importe_cancelado'
                                WHERE descr_item = '$descr_item' ";                                 
                $mysqli->query($sql_update);   
            }
            if($row_result['total']<>$total){
                $sql_update = " update orden_pago SET total = '$total'
                                WHERE descr_item = '$descr_item' ";                                 
                $mysqli->query($sql_update);   
            }
        
    }

}

$mysqli->close();
echo json_encode(["success" => true, "message" => "Sinc. OP OK"]);
