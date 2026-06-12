<?php

    include_once 'conexiones/conexion.php';
    include_once 'funciones/funciones.php';

    $filtro='';

    $mysqli=conexion_db();
    //
    if($_GET['usuario']){
        $usuario= base64_decode($_GET['usuario']);
        $sql_proveedor = " select cuit, nombre FROM proveedores where cuit='".$usuario."'; ";    
        $proveedor = $mysqli->query($sql_proveedor) or die(mysqli_error($mysqli)); 
        $row = $proveedor->fetch_assoc();
        $where="WHERE TRIM(REPLACE(REPLACE(REPLACE(proveedor, CHAR(13), ''), CHAR(10), ''), CHAR(9), '')) =TRIM('".$row['nombre']."') AND
                importe_cancelado > 0 and NOT(egreso_valor LIKE 'ANU-%')";
    }else{
        $usuario='';
        $where='Were 1=0';
    }
    
    if(!empty($_GET['id_egresovalores'])){

        $where = "  WHERE id_egresovalores='".$_GET['id_egresovalores']."' ";

        $sql_ordenes_pago = "   SELECT nro_op, fecha, descr_item, importe, total, proveedor, moneda, egreso_valor, id_egresovalores, cotizacion, RetGan, RetIIBB, RetIVA
                            FROM orden_pago
                            $where 
                            GROUP BY descr_item
                            ORDER BY descr_item ASC; ";

        $sql_detalle_orden_pago = " SELECT egreso_valor, num_banco, tipo_valor, importe_valor, total, fechavencimiento_cheque 'vto', fechaemision_cheque 'emision', ultima_actualizacion
                                    FROM detalle_orden_pago
                                    $where 
                                    GROUP BY num_banco
                                    ORDER BY num_banco ASC;";
        //echo $sql_detalle_orden_pago;
        $resultado_detalle_orden_pago = $mysqli->query($sql_detalle_orden_pago) or die(mysqli_error($mysqli));   
    }else{

        $sql_ordenes_pago = "SELECT nro_op, fecha, total, egreso_valor, id_egresovalores, proveedor, ultima_actualizacion 
                            FROM orden_pago  
                            $where 
                            group by nro_op                            
                            ORDER BY fecha DESC; ";
        
                        //echo $sql_ordenes_pago;
    }
    
    $resultado_ordenes_pago = $mysqli->query($sql_ordenes_pago) or die(mysqli_error($mysqli));   

    //CERTIFICADOS DE RETENCION
    function getCertificadoRetencion(string $nro_op,string $tipo): ?array { // <-- Cambiado a devolver ?array (puede ser null)
        include_once 'conexiones/conexion.php';
        include_once 'funciones/funciones.php'; // Para cualquier función auxiliar
        
        $mysqli = conexion_db();
        if ($mysqli->connect_errno) {
            error_log("Error al conectar con la base de datos: " . $mysqli->connect_error);
            return null; // Retorna null si la conexión falla
        }
        if($tipo==='RetIVA'){
            $nombre_impuesto = 'Impuesto al valor Agregado IVA'; 
            $nombre_nro_certificado = 'NroCertifIVA';           
            $regimen = 'Factura M - IVA' ;
        }
        else if ($tipo==='RetIIBB') {
            $nombre_impuesto = 'Impuesto al Ingeso Bruto IIBB';     
            $nombre_nro_certificado = 'NroCertifIIBB';       
            $regimen = 'Factura M - IIBB' ;
        }
        else if ($tipo==='RetGan') {
            $nombre_impuesto = 'Impuesto a las Ganancias';            
            $nombre_nro_certificado = 'NroCertifGan';
            $regimen = 'Factura M - IVA' ;
        }
        // Corregida la consulta SQL
        $sql = "SELECT nro_op, fecha, descr_item, importe, proveedor, comprador,(SELECT MAX(cuit) from proveedores where proveedores.nombre like CONCAT('%',comprador,'%') ) as cuit_comprador,
                (SELECT MAX(domicilio) from proveedores where proveedores.nombre like CONCAT('%',comprador,'%') ) as domicilio_comprador,
                importe_cancelado, $nombre_nro_certificado, $tipo, '$nombre_impuesto' as nombre_impuesto, '$regimen' AS regimen
                FROM orden_pago 
                WHERE orden_pago.nro_op = '$nro_op' AND $tipo > 0; "; 
        //echo $sql;
        $stmt = $mysqli->prepare($sql);
    
        if (!$stmt) {
            error_log("Error al preparar la consulta: " . $mysqli->error);
            $mysqli->close(); // Cerrar conexión en caso de error
            return null; // Devuelve null en caso de error
        }
            
        $stmt->execute();
        $result = $stmt->get_result();
    
        $retenciones = [];
        while ($row = $result->fetch_assoc()) {
            $retenciones[] = $row;
        }
        
        
        $result->free();
        $stmt->close();
        $mysqli->close(); // Cerrar la conexión aquí si la abres dentro de la función
    
        return $retenciones; // <-- Devuelve el array asociativo directamente (o null si no hay resultados)
    }
    ?>