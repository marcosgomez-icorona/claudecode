<?php
//POST HTTP REQUEST NODE-RED
include 'recepcion_http_request_post.php';

// Insertar datos
foreach ($request['data'] as $row) {
    /*
    const id_egresovalores = row.id_egresovalores;
    const egreso_valor = row.egreso_valor;
    const num_banco = row.num_banco;        
    const tipo_valor = row.TIPOVALOR;    
    const importe_valor = row.importe_valor;
    const total = row.total;
    const fechavencimiento_cheque = row.fechavencimiento_cheque;
    const fechaemision_cheque = row.fechaemision_cheque;
    */
    $id_egresovalores = $row['id_egresovalores'] ?? '';
    $egreso_valor = $row['egreso_valor'] ?? '';    
    $num_banco = $row['num_banco'] ?? '';
    $tipo_valor = $row['TIPOVALOR'] ?? '';    
    $importe_valor = $row['importe_valor'] ?? 0;    
    $total = $row['total'] ?? 0;    
    $fechavencimiento_cheque = $row['fechavencimiento_cheque'] ?? '';  
    // para solucionar cuando viene como formato T03:00:00.000Z    
    if (!empty($fechavencimiento_cheque)) {
        $dt = new DateTime($fechavencimiento_cheque);
        $fechavencimiento_cheque = $dt->format('Y-m-d');
    } else {
        $fechavencimiento_cheque = null; 
    }  
    $fechaemision_cheque = $row['fechaemision_cheque'] ?? '';  
    if (!empty($fechaemision_cheque)) {
        $dt = new DateTime($fechaemision_cheque);
        $fechaemision_cheque = $dt->format('Y-m-d');
    } else {
        $fechaemision_cheque = null; 
    }      
    

    // Verificar si ya existe
    $checkSql = "   SELECT id_egresovalores, egreso_valor, num_banco, tipo_valor, importe_valor, total, fechavencimiento_cheque, fechaemision_cheque, ultima_actualizacion
                    FROM detalle_orden_pago
                    WHERE id_egresovalores = '$id_egresovalores' LIMIT 1";

    $result = $mysqli->query($checkSql);

    // Si NO existe, lo insertamos
    if ($result && $result->num_rows === 0) {
        $sql = "INSERT INTO detalle_orden_pago (id_egresovalores, egreso_valor, num_banco, tipo_valor, importe_valor, total, fechavencimiento_cheque, fechaemision_cheque)
                VALUES ('$id_egresovalores', '$egreso_valor', '$num_banco', '$tipo_valor', '$importe_valor', '$total', '$fechavencimiento_cheque', '$fechaemision_cheque')";
        $mysqli->query($sql);
    }

}

$mysqli->close();
echo json_encode(["success" => true, "message" => "Sinc. Detalle OP OK"]);
