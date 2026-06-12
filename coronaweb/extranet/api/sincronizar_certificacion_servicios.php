<?php
//POST HTTP REQUEST NODE-RED
include 'recepcion_http_request_post.php';
$i=0;
// Insertar datos
foreach ($request['data'] as $row) {

    $nro_oc = $row['numeroOC'] ?? '';    
    $fechaemision = $row['FECHAEMISION'] ?? ''; 
    // para solucionar cuando viene como formato T03:00:00.000Z    
    if (!empty($fechaemision)) {
        $dt = new DateTime($fechaemision);
        $fechaemision = $dt->format('Y-m-d');
    } else {
        $fechaemision = null; 
    }
    $fechaentrega = $row['FECHAENTREGA'] ?? '';
    if (!empty($fechaentrega)) {
        $dt = new DateTime($fechaentrega);
        $fechaentrega = $dt->format('Y-m-d');
    } else {
        $fechaentrega = null; // o '', según tu BD
    }    
    $nro_certificado = $row['CONSTANCIA'] ?? '';
    $total = $row['total_doc'] ?? '';
    $importe_oc = $row['totalOC'] ?? '';
    $cond_iva = $row['nom_cond_iva'] ?? '';
    
    $proveedor = $row['nom_proveedor'] ?? '';
    
    // Verificar si el CUIT ya existe
    $checkSql = "SELECT 1 FROM certificacion_servicio WHERE nro_certificado = '$nro_certificado' LIMIT 1";
    $result = $mysqli->query($checkSql);

    // Si NO existe, lo insertamos
    if ($result && $result->num_rows === 0) {
        $sql = "INSERT INTO certificacion_servicio (fechaemision,nro_oc,nro_certificado ,total,importe_oc,cond_iva,fechaentrega ,proveedor)
                VALUES ('$fechaemision', '$nro_oc', '$nro_certificado', '$total', '$importe_oc', '$cond_iva', '$fechaentrega', '$proveedor')";
        //echo '--- Nro Cert = '.$nro_certificado;
        //echo '/ Fecha Emision = '.$fechaemision;
        $mysqli->query($sql);
               
    }
}

$mysqli->close();
echo json_encode(["success" => true, "message" => "Sinc Cert. Serv. OK"]);
