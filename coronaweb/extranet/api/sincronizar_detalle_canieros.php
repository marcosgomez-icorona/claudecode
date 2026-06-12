<?php
// POST HTTP REQUEST NODE-RED
include 'recepcion_http_request_post.php';

foreach ($request['data'] as $row) {    
    

    // Extraer datos con valores por defecto para evitar null
    $pesada         = $row['pesada']         ?? '';
    $id_caniero     = $row['id_caniero']     ?? '';
    $razon_social   = $row['razon_social']   ?? '';
    $grupo          = $row['grupo']          ?? '';
    $fechaindustrial= $row['fechaindustrial']?? '';
    $fecha_pesada= $row['fecha_pesada']?? '';
    
    // Para solucionar cuando viene como formato T03:00:00.000Z    
    if (!empty($fechaindustrial)) {
        $dt = new DateTime($fechaindustrial);
        $fechaindustrial = $dt->format('d/m/Y');
    } else {
        $fechaindustrial = null; 
    }

    if (!empty($fecha_pesada)) {
        $fp = new DateTime($fecha_pesada);
        $fecha_pesada = $fp->format('d/m/Y');
    } else {
        $fecha_pesada = null; 
    }
    
    $remito         = $row['remito']         ?? '';
    $tipo           = $row['tipo']           ?? '';
    $bruto_tn          = $row['bruto']       ?? 0;
    $trash          = $row['trash']          ?? 0;
    $neto_tn           = $row['netoreal']    ?? 0;
    $brix           = $row['brix']           ?? 0;
    $pol            = $row['pol']            ?? 0;
    $pureza         = $row['pureza']         ?? 0;
    $rendimiento    = $row['rendimiento']    ?? 0;

    
    // Verificar si el CUIT ya existe
    $checkSql = "SELECT pesada,razon_social FROM detalle_canieros WHERE pesada = $pesada LIMIT 1";
    $result = $mysqli->query($checkSql);
    $row_result=$result->fetch_assoc();

    // Si NO existe, lo insertamos
    if ($result && $result->num_rows === 0) {
        $sql = "INSERT INTO detalle_canieros (pesada,id_caniero,razon_social,grupo ,fechaindustrial,fecha_pesada,remito,
                            tipo,bruto_tn,trash ,neto_tn ,brix ,pol ,pureza ,rendimiento)                                     
                VALUES ('$pesada', '$id_caniero','$razon_social','$grupo','$fechaindustrial','$fechaindustrial','$fecha_pesada','$remito','$tipo',
                '$bruto_tn','$trash','$neto_tn','$brix','$pol','$pureza','$rendimiento')";
        $mysqli->query($sql);
    }else{            
            if($row_result['pesada']==$pesada and $row_result['razon_social']<>$razon_social){
                $sql_update = " UPDATE detalle_canieros SET id_caniero = '$id_caniero', razon_social = '$razon_social', grupo = '$grupo', fechaindustrial = '$fechaindustrial', 
                                fecha_pesada = '$fecha_pesada'                    
                                WHERE pesada = '$pesada' ";               
                $mysqli->query($sql_update);
                
            }    
    }
}

$mysqli->close();

echo json_encode(["success" => true, "message" => "Sinc. Detalle de Canieros OK"]);

