<?php
//POST HTTP REQUEST NODE-RED
include_once '../controller/usuarios.php';
include 'recepcion_http_request_post.php';

foreach ($request['data'] as $row) {
    
    $caniero = $row['caniero'] ?? '';
    $cania_bruta = $row['cania_bruta'] ?? '';
    $blsas_dev = $row['bls_dev_v1'] ?? '';
    $ordenes_emitidas_blsas = $row['ord_emitidas_v1'] ?? '';
    $saldo_zafra_ant = $row['saldozafraanterior'] ?? 0;
    $saldo_en_blsas = $row['saldobolsas_v1'] ?? '';
    $ordenes_pendientes_blsas = $row['saldobolsas_v1'] ?? '';
    
    // Verificar si el CUIT ya existe
    $checkSql = "   SELECT caniero, cania_bruta, blsas_dev, ordenes_emitidas_blsas, saldo_zafra_ant, saldo_en_blsas, ultima_actualizacion
                    FROM cta_cte_canieros WHERE caniero = '$caniero' LIMIT 1";
    $result = $mysqli->query($checkSql);
    $row_result=$result->fetch_assoc();

    // Si NO existe, lo insertamos
    if ($result && $result->num_rows === 0) {
        $sql = "INSERT INTO cta_cte_canieros (caniero, cania_bruta, blsas_dev, ordenes_emitidas_blsas,ordenes_pendientes_blsas, saldo_zafra_ant, saldo_en_blsas) 
                VALUES ('$caniero', '$cania_bruta','$blsas_dev','$ordenes_emitidas_blsas','$ordenes_pendientes_blsas','$saldo_zafra_ant','$saldo_en_blsas')";
        $mysqli->query($sql);
    }else{            
            if($row_result['caniero']==$caniero ){
                $sql_update = " update cta_cte_canieros SET caniero='$caniero', cania_bruta='$cania_bruta', blsas_dev='$blsas_dev', 
                                ordenes_emitidas_blsas='$ordenes_emitidas_blsas',ordenes_pendientes_blsas='$ordenes_pendientes_blsas', saldo_zafra_ant='$saldo_zafra_ant', saldo_en_blsas='$saldo_en_blsas'
                                WHERE caniero = '$caniero' ";               
                $mysqli->query($sql_update);
                
            }    
    }
}

$mysqli->close();
echo json_encode(["success" => true, "message" => "Sinc. Cta Cte Canieros OK"]);
