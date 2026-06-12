<?php
//POST HTTP REQUEST NODE-RED
include 'recepcion_http_request_post.php';

foreach ($request['data'] as $row) {
    $proveedor = $row['PROVEEROR'] ?? '';
    $saldo_facturas_pendientes = $row['SALDO_ACUMULADO_ANT'] ?? 0;
    
    // Verificar si el CUIT ya existe
    $checkSql = "SELECT proveedor,saldo_facturas_pendientes from saldos_proveedores WHERE proveedor = '$proveedor' LIMIT 1";
    $result = $mysqli->query($checkSql);
    $row_result=$result->fetch_assoc();

    // Si NO existe, lo insertamos
    if ($result && $result->num_rows === 0) {
        $sql = "insert into saldos_proveedores (proveedor,saldo_facturas_pendientes) VALUES ('$proveedor', '$saldo_facturas_pendientes')";
        $mysqli->query($sql);
    }else{            
            if($row_result['saldo_facturas_pendientes']<>$saldo_facturas_pendientes ){
                $sql_update = "update canieros SET saldo_facturas_pendientes = '$saldo_facturas_pendientes' WHERE proveedor = '$proveedor' ";               
                $mysqli->query($sql_update);

               
            }    
    }
}

$mysqli->close();
echo json_encode(["success" => true, "message" => "Sinc. Canieros OK"]);
