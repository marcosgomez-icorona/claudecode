<?php
//POST HTTP REQUEST NODE-RED
include 'recepcion_http_request_post.php';

$i=0;
foreach ($request['data'] as $row) {
    /*
    const comprobante = row.COMPROBANTE;    
    const haber = row.haber;    
    const saldo = row.SALDODOCUMENTO;    
    const importe = row.TOTAL;    
    const fecha_ingreso = row.FECHAACTUAL;
    const tipo_pago = row.TIPO;
    const proveedor = row.NOMBREDESTINATARIO;
    */
    $comprobante = $row['COMPROBANTE'] ?? '';
    $proveedor = $row['NOMBREDESTINATARIO'] ?? '';    
    $haber = $row['haber'] ?? 0;
    $debe = $row['debe'] ?? 0;
    $saldo = $row['SALDODOCUMENTO'] ?? 0;
    $fecha_ingreso = $row['FECHAACTUAL'] ?? '';  
    // para solucionar cuando viene como formato T03:00:00.000Z    
    if (!empty($fecha_ingreso)) {
        $dt = new DateTime($fecha_ingreso);
        $fecha_ingreso = $dt->format('Y-m-d');
    } else {
        $fecha_ingreso = null; 
    }  
    $tipo_pago = $row['TIPO'] ?? '';
    $importe = $row['TOTAL'] ?? 0;
    /*
    if($debe>0){
        $importe=$debe;
    }else{
        if($haber>0){
            $importe=$haber ?? 0;
        }
    }    
    */
    
    
    // Verificar si el comprobante ya existe
    $checkSql = "SELECT proveedor,comprobante, debe,haber, saldo,saldo_acumulado, importe, fecha_ingreso, tipo_pago
                 FROM cuenta_corriente WHERE comprobante = '$comprobante' and proveedor = '$proveedor' LIMIT 1";
    //echo $checkSql;                 
    $result = $mysqli->query($checkSql);
    $row_result=$result->fetch_assoc();

    // Si NO existe, lo insertamos
    if ($result && $result->num_rows === 0) {
        $sql = "INSERT INTO cuenta_corriente (comprobante, debe, haber, saldo, importe, fecha_ingreso, tipo_pago, proveedor)
                VALUES ('$comprobante', '$debe', '$haber', '$saldo', '$importe', '$fecha_ingreso', '$tipo_pago', '$proveedor')";
        //echo $sql;
        $mysqli->query($sql);
        $i++;

    }else{            
            if($row_result['importe']<>$importe or $row_result['debe']<>$debe or $row_result['haber']<>$haber){
                $sql_update = " update cuenta_corriente SET debe = '$debe', haber = '$haber',saldo = '$saldo',importe = '$importe' 
                                WHERE comprobante = '$comprobante' and proveedor = '$proveedor' ";                               
                $mysqli->query($sql_update);
            }    
    }
}

$mysqli->close();
echo json_encode(["success" => true, "message" => "Sinc. '$i' nuevos de Cta Cte Proveedores OK"]);
