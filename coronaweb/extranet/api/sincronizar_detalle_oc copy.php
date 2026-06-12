<?php
//POST HTTP REQUEST NODE-RED
include 'recepcion_http_request_post.php';
/*
ID, V_EZI_ORDENDECOMPRA0.OC,NUMEROITEM,CODIGO ,NOM_PRODUCTO 'ARTICULO',CANTIDAD,UNI_MED,PU,TOTAL_ITEM,
V_EZI_ORDENDECOMPRA0.fechaentrega,CONVERT(date, LEFT(FECHAACTUAL, 8)) 'fecha_oc'

*/
// Insertar datos
foreach ($request['data'] as $row) {

    $nro_oc = $row['OC'] ?? '';
    $numero_item = $row['NUMEROITEM'] ?? '';
    $codigo = $row['CODIGO'] ?? '';
    $articulo = $row['ARTICULO'] ?? '';
    $cantidad = $row['CANTIDAD'] ?? '';
    $uni_med = $row['UNI_MED'] ?? '';
    $pu = $row['PU'] ?? '';
    $total_item = $row['TOTAL_ITEM'] ?? '';
    $fecha_entrega = $row['fechaentrega'] ?? '';
    // para solucionar cuando viene como formato T03:00:00.000Z    
    /*
    if (!empty($fecha_entrega)) {
        $dt = new DateTime($fecha_entrega);
        $fecha_entrega = $dt->format('Y-m-d');
    } else {
        $fecha_entrega = null; 
    }
    */
    // Verificar si el CUIT ya existe
    $checkSql = "SELECT 1 FROM detalle_oc WHERE nro_oc = '$nro_oc' LIMIT 1";
    $result = $mysqli->query($checkSql);

    // Si NO existe, lo insertamos
    if ($result && $result->num_rows === 0) {
        $sql = "INSERT INTO detalle_oc (nro_oc,numero_item, codigo, articulo, cantidad, uni_med, pu, total_item, fecha_entrega) 
                VALUES ('$nro_oc', '$numero_item', '$codigo', '$articulo', '$cantidad', '$uni_med', '$pu', '$total_item', '$fecha_entrega')";
        $mysqli->query($sql);
    }
}

$mysqli->close();
echo json_encode(["success" => true, "message" => "Sinc. Detalle OC OK"]);
