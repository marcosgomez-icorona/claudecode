<?php
//POST HTTP REQUEST NODE-RED
include 'recepcion_http_request_post.php';

$i=0;

/* LIMPIA REGISTROS PARA CARGAR LOS NUEVOS PENDIENTES */
    $clean = "DELETE FROM facturas_pendiente_pago ";    
    $mysqli->query($clean);
    
foreach ($request['data'] as $row) {
    /*
    const comprobante = row.comprobante;    
    const debe = row.DEBE;    
    const haber = row.HABER;    
    const saldo = 0;    
    const importe = row.IMPORTE_ORIGINAL;    
    const fecha_ingreso = row.FECHAEMISION;
    const fecha_vto = row.FECHAVENCIMIENTO;
    const tipo_pago = row.TIPO_PAGO;
    const proveedor = row.PROVEEROR;
    */
    
    $comprobante = $row['comprobante'] ?? 0;
    $proveedor = $row['PROVEEROR'] ?? '';
    $debe = $row['DEBE'] ?? 0;
    $haber = $row['HABER'] ?? 0;    
    $importe = $row['IMPORTE_ORIGINAL'] ?? 0;
    $fecha_ingreso = $row['FECHAEMISION'] ?? '';    
    // para solucionar cuando viene como formato T03:00:00.000Z    
    if (!empty($fecha_ingreso)) {
        $dt = new DateTime($fecha_ingreso);
        $fecha_ingreso = $dt->format('Y-m-d');
    } else {
        $fecha_ingreso = null; 
    }
    $fecha_vto = $row['FECHAVENCIMIENTO'] ?? '';
    if (!empty($fecha_vto)) {
        $dt = new DateTime($fecha_vto);
        $fecha_vto = $dt->format('Y-m-d');
    } else {
        $fecha_vto = null; 
    }
    $tipo_pago = $row['TIPO_PAGO'] ?? 0;    
    
    // Verificar si el comprobante ya existe
    $checkSql = "SELECT comprobante, debe, haber, saldo, importe, saldo_acumulado, fecha_ingreso, fecha_vto, tipo_pago, proveedor, ultima_actualizacion
                 FROM facturas_pendiente_pago WHERE comprobante = '$comprobante' and proveedor = '$proveedor' LIMIT 1";
    //echo $checkSql;                 
    $result = $mysqli->query($checkSql);
    $row_result=$result->fetch_assoc();

    // Si NO existe, lo insertamos
    if ($result && $result->num_rows === 0) {
        $sql = "INSERT INTO facturas_pendiente_pago (comprobante, debe, haber, importe, fecha_ingreso, fecha_vto, tipo_pago, proveedor)
                VALUES ('$comprobante', '$debe', '$haber', '$importe', '$fecha_ingreso', '$fecha_vto', '$tipo_pago', '$proveedor')";
        //echo $sql;
        $mysqli->query($sql);
        $i++;

    }else{            
            if($row_result['importe']<>$importe ){
                $sql_update = " update cuenta_corriente SET  debe = '$debe', haber = '$haber',importe = '$importe' 
                                WHERE comprobante = '$comprobante' and proveedor = '$proveedor' ";                               
                $mysqli->query($sql_update);
            }    
    }
}

$mysqli->close();
echo json_encode(["success" => true, "message" => "Sinc. '$i' nuevos de Pendientes de Pago Proveedores OK"]);
