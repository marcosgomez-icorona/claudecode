<?php
//POST HTTP REQUEST NODE-RED
include_once '../controller/usuarios.php';
include 'recepcion_http_request_post.php';

foreach ($request['data'] as $row) {
    /*
    NOM_CLIENTE AS PROVEEROR,DESCRIPCION AS comprobante,'0' AS DEBE, SALDO_ORIG AS HABER, SALDO, IMPORTE_TOTAL AS IMPORTE_ORIGINAL, TRCOTIZACION,
     FECHAEMISION, FECHAVENCIMIENTO,'' AS TIPO_PAGO
    */
    $proveedor = $row['PROVEEROR'] ?? '';
    $comprobante = $row['comprobante'] ?? '';
    $debe = $row['DEBE'] ?? 0;
    $haber = $row['HABER'] ?? 0;
    $saldo = $row['SALDO'] ?? 0;
    $importe = $row['IMPORTE_ORIGINAL'] ?? 0;
    $fecha_ingreso = $row['FECHAEMISION'] ?? '';
    // para solucionar cuando viene como formato T03:00:00.000Z    
    $fecha = DateTime::createFromFormat('Ymd', $fecha_ingreso);
    $fechaingresoformateada = $fecha->format('Y-m-d');

    $fecha_vto = $row['FECHAVENCIMIENTO'] ?? '';
    $fecha = DateTime::createFromFormat('Ymd', $fecha_vto);
    $fecha_vtoformateada = $fecha->format('Y-m-d');

    $tipo_pago = $row['TIPO_PAGO'] ?? '';
    
    
    // Verificar si el Proveedor ya existe
    $checkSql = "   SELECT comprobante, debe, haber, saldo, importe, fecha_ingreso, fecha_vto, tipo_pago, proveedor
                    FROM facturas_pendiente_pago WHERE proveedor = '$proveedor' and comprobante= '$comprobante'  LIMIT 1";
    $result = $mysqli->query($checkSql);
    $row_result=$result->fetch_assoc();

    // Si NO existe, lo insertamos
    if ($result && $result->num_rows === 0) {
        $sql = "INSERT INTO facturas_pendiente_pago (comprobante, debe, haber, saldo, importe, fecha_ingreso, fecha_vto, tipo_pago, proveedor) 
                VALUES ('$comprobante', '$debe','$haber','$saldo','$importe','$fechaingresoformateada','$fecha_vtoformateada','$tipo_pago','$proveedor')";
        $mysqli->query($sql);
    }else{            
            if($row_result['haber']<>$haber ){
                $sql_update = " update facturas_pendiente_pago SET haber='$haber', saldo='$saldo', importe='$importe'
                                WHERE proveedor = '$proveedor' and comprobante= '$comprobante' ";               
                $mysqli->query($sql_update);
                
            }    
    }
}

$mysqli->close();
echo json_encode(["success" => true, "message" => "Sinc. Fact. Pdte de Pago OK"]);
