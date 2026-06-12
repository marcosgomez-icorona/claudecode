<?php

    include_once 'conexiones/conexion.php';
    include_once 'funciones/funciones.php';

    $filtro='';

    $mysqli=conexion_db();
    //
    if($_GET['usuario']){
        $usuario= base64_decode($_GET['usuario']);
        $sql = " select cuit, nombre FROM proveedores where cuit='".$usuario."'; ";    
        $proveedor = $mysqli->query($sql) or die(mysqli_error($mysqli)); 
        $row = $proveedor->fetch_assoc();
        $nom_prov = !empty($row['nombre']) ? $row['nombre'] : '';
        $where="WHERE TRIM(REPLACE(REPLACE(REPLACE(proveedor, CHAR(13), ''), CHAR(10), ''), CHAR(9), '')) =TRIM('".$nom_prov."')";
    }else{
        $usuario='';
        $where='Were 1=0';
    }
    

    // Asegúrate de que la conexión $mysqli esté disponible aquí,
// ya sea porque este archivo la inicializa o porque se incluye después de la inicialización.
// Si $mysqli NO está disponible aquí, tendrías que pasarla como argumento a la función,
// o inicializarla (include_once 'conexiones/conexion.php'; $mysqli = conexion_db();)
// si este archivo es un punto de entrada independiente de datos.

/**
 * Obtiene los ítems de una orden de compra específica.
 * @param mysqli $mysqli La conexión a la base de datos.
 * @param string $id El número de orden de compra.
 * @return array Un array de arrays asociativos con los ítems de la OC.
 */

 include_once 'conexiones/conexion.php';
    include_once 'funciones/funciones.php';

    $filtro='';

    $mysqli=conexion_db();
    //
    if($_GET['usuario']){
        $usuario= base64_decode($_GET['usuario']);
        $sql = " select cuit, canieros.razon_social nombre FROM canieros where cuit='".$usuario."' ORDER BY razon_social DESC LIMIT 1; ";    
        //echo $sql;
        $nombre = $mysqli->query($sql) or die(mysqli_error($mysqli)); 
        $row = $nombre->fetch_assoc();
        //$where="WHERE TRIM(REPLACE(REPLACE(REPLACE(proveedor, CHAR(13), ''), CHAR(10), ''), CHAR(9), '')) =TRIM('".$row['nombre']."')";
    }else{
        $usuario='';
        $where='Were 1=0';
    }
 
    
 function obtiene_ctas_caniero($cuit): ?array{
    include_once 'conexiones/conexion.php';
    include_once 'funciones/funciones.php';

    $mysqli = conexion_db_molienda();
    if ($mysqli->connect_errno) {
        error_log("Error al conectar con la base de datos: " . $mysqli->connect_error);
        return null; // Retorna null si la conexión falla
    }
    
    $sql= " SELECT canieros.razon_social,canieros.cuit,canieros.grupo
            FROM canieros INNER JOIN detalle_canieros ON canieros.razon_social = detalle_canieros.razon_social
            WHERE cuit = '$cuit' and activo='S' 
            group by razon_social order by cuit asc;";    
    $stmt = $mysqli->prepare($sql);
    //echo $sql;
    if (!$stmt) {
        error_log("Error al preparar la consulta" . $mysqli->error);
        $mysqli->close(); // Cerrar conexión en caso de error
        return null; // Devuelve null en caso de error
    }

    $stmt->execute();
    $result = $stmt->get_result();
    
    $canieros_rows = [];
    while ($row = $result->fetch_assoc()) {
        $canieros_rows[] = $row;
    }
    
    $result->free();
    $stmt->close();
    $mysqli->close(); // Cerrar la conexión aquí si la abres dentro de la función

    return $canieros_rows; // <-- Devuelve el array asociativo directamente (o null si no hay resultados)

 }   
 function getDetalleCania(string $caniero, string $desde, string $hasta): ?array {
    include_once 'conexiones/conexion.php';
    include_once 'funciones/funciones.php';

    $mysqli = conexion_db_molienda();
    if ($mysqli->connect_errno) {
        error_log("Error al conectar con la base de datos: " . $mysqli->connect_error);
        return null; // Retorna null si la conexión falla
    }
    
    /*PERIODO*/
    if(!empty($desde) and !empty($hasta)){
        $periodo=" and STR_TO_DATE(fechaindustrial, '%d/%m/%Y') between '$desde' and '$hasta'"; 
    }else{
        $periodo= " and STR_TO_DATE(fechaindustrial, '%d/%m/%Y')= ( SELECT MAX(STR_TO_DATE(dc.fechaindustrial, '%d/%m/%Y')) FROM detalle_canieros dc 
                                                                    INNER JOIN canieros c ON dc.razon_social = c.razon_social where  c.razon_social='$caniero')";
    }
        
    $sql= "SELECT  pesada, detalle_canieros.id_caniero, detalle_canieros.razon_social, detalle_canieros.grupo, fechaindustrial, remito, tipo, bruto_tn, trash, neto_tn, brix, 
            ultima_actualizacion, pol, pureza, rendimiento
            FROM detalle_canieros 
            INNER JOIN canieros ON detalle_canieros.razon_social = canieros.razon_social
            WHERE detalle_canieros.razon_social = '$caniero' $periodo
            ORDER BY STR_TO_DATE(fechaindustrial, '%d/%m/%Y') DESC;";
    //echo $sql;
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        error_log("Error al preparar la consulta" . $mysqli->error);
        $mysqli->close(); // Cerrar conexión en caso de error
        return null; // Devuelve null en caso de error
    }

    //$stmt->bind_param("siss", $caniero,$zafra,$desde,$hasta);
    //echo $caniero." - ".$zafra." - ".$desde." - ".$hasta;
    //echo $stmt;
    $stmt->execute();
    $result = $stmt->get_result();

    //$detalle_cania_row = $result->fetch_assoc(); // <-- Aquí obtienes UNA SOLA FILA
    $detalle_cania_rows = [];
    while ($row = $result->fetch_assoc()) {
        $detalle_cania_rows[] = $row;
    }
    
    $result->free();
    $stmt->close();
    $mysqli->close(); // Cerrar la conexión aquí si la abres dentro de la función

    return $detalle_cania_rows; // <-- Devuelve el array asociativo directamente (o null si no hay resultados)
}

function getctactecaniero(string $caniero): ?array {

        include_once 'conexiones/conexion.php';
        include_once 'funciones/funciones.php';

        $mysqli = conexion_db_molienda();
        if ($mysqli->connect_errno) {
            error_log("Error al conectar con la base de datos: " . $mysqli->connect_error);
            return null; // Retorna null si la conexión falla
        }

        $sql = "SELECT cuit,caniero,grupo, cania_bruta, blsas_dev, ordenes_emitidas_blsas, saldo_zafra_ant, ordenes_pendientes_blsas, saldo_zafra_ant_pesos, maquila, saldo_en_blsas,ultima_actualizacion
                FROM cta_cte_canieros
                LEFT JOIN canieros ON canieros.razon_social= cta_cte_canieros.caniero
                WHERE caniero = '$caniero'
                GROUP BY caniero
                ORDER BY cuit ASC;";
        //echo $sql;
        $stmt = $mysqli->prepare($sql);

        if (!$stmt) {
            error_log("Error al preparar la consulta" . $mysqli->error);
            $mysqli->close(); // Cerrar conexión en caso de error
            return null; // Devuelve null en caso de error
        }

        //$stmt->bind_param("siss", $caniero,$zafra,$desde,$hasta);
        //echo $caniero." - ".$zafra." - ".$desde." - ".$hasta;
        //echo $stmt;
        $stmt->execute();
        $result = $stmt->get_result();

        //$detalle_cania_row = $result->fetch_assoc(); // <-- Aquí obtienes UNA SOLA FILA
        $cta_cte_cania_rows = [];
        while ($row = $result->fetch_assoc()) {
            $cta_cte_cania_rows[] = $row;
        }
        
        $result->free();
        $stmt->close();
        $mysqli->close(); // Cerrar la conexión aquí si la abres dentro de la función

        return $cta_cte_cania_rows; // <-- Devuelve el array asociativo directamente (o null si no hay resultados) 
}
function OCAzucarCaniero(string $caniero): ?array{

    include_once 'conexiones/conexion.php';
    include_once 'funciones/funciones.php';

    $mysqli = conexion_db_molienda();
    if ($mysqli->connect_errno) {
        error_log("Error al conectar con la base de datos: " . $mysqli->connect_error);
        return null; // Retorna null si la conexión falla
    }

    $sql = "SELECT nro_oc, fecha_emision, detalle, plazo_entrega, condicion_pago, total, proveedor
            FROM ordenes_compra
            INNER JOIN canieros ON ordenes_compra.proveedor = canieros.razon_social
            where proveedor = '$caniero' ORDER BY fecha_emision DESC;";
    //echo $sql;
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        error_log("Error al preparar la consulta" . $mysqli->error);
        $mysqli->close(); // Cerrar conexión en caso de error
        return null; // Devuelve null en caso de error
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    //$detalle_cania_row = $result->fetch_assoc(); // <-- Aquí obtienes UNA SOLA FILA
    $oc_azucar_rows = [];
    while ($row = $result->fetch_assoc()) {
        $oc_azucar_rows[] = $row;
    }
    
    $result->free();
    $stmt->close();
    $mysqli->close(); // Cerrar la conexión aquí si la abres dentro de la función

    return $oc_azucar_rows; // <-- Devuelve el array asociativo directamente (o null si no hay resultados) 
}
function OPAzucarCaniero(string $cuit): ?array{

    include_once 'conexiones/conexion.php';
    include_once 'funciones/funciones.php';

    $mysqli = conexion_db_molienda();
    if ($mysqli->connect_errno) {
        error_log("Error al conectar con la base de datos: " . $mysqli->connect_error);
        return null; // Retorna null si la conexión falla
    }

    $sql = "SELECT cuit,proveedor,nro_op, fecha, descr_item, importe, total, moneda, egreso_valor, id_egresovalores,  orden_pago.ultima_actualizacion, cotizacion, RetGan, RetIIBB, RetIVA
            FROM orden_pago
            INNER JOIN proveedores ON orden_pago.proveedor= proveedores.nombre
            where cuit = '$cuit' and total>0
            ORDER BY fecha DESC;";
    //echo $sql;
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        error_log("Error al preparar la consulta" . $mysqli->error);
        $mysqli->close(); // Cerrar conexión en caso de error
        return null; // Devuelve null en caso de error
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    //$detalle_cania_row = $result->fetch_assoc(); // <-- Aquí obtienes UNA SOLA FILA
    $oc_azucar_rows = [];
    while ($row = $result->fetch_assoc()) {
        $oc_azucar_rows[] = $row;
    }
    
    $result->free();
    $stmt->close();
    $mysqli->close(); // Cerrar la conexión aquí si la abres dentro de la función

    return $oc_azucar_rows; // <-- Devuelve el array asociativo directamente (o null si no hay resultados) 
}
?>