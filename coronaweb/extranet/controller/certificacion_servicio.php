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
        $where="WHERE TRIM(REPLACE(REPLACE(REPLACE(proveedor, CHAR(13), ''), CHAR(10), ''), CHAR(9), '')) =TRIM('".$row['nombre']."')";
    }else{
        $usuario='';
        $where='Were 1=0';
    }
    if(!empty($_GET['nro_certificado'])){
        $constancia= ltrim($_GET['nro_certificado'], '0'); //QUITO LOS CEROS DE LA IZQUIERDA
        $where='Where nro_certificado='.$_GET['nro_certificado'].' ';

        $sql_certificacion_servicios = "SELECT fechaemision, nro_oc, nro_certificado, total, proveedor, importe_oc, cond_iva, fechaentrega, ultima_actualizacion
                                        FROM certificacion_servicio
                                        WHERE nro_certificado='".$_GET['nro_certificado']."';"; 

        $sql_detalle_constancia_servicio = "SELECT id, constancia, numeroitem, codigo, nom_producto, bonificacion, descuento, cantidad, cantidaddoc, cantidadpendienteoc,
         cumplido, porcentaje, estado, uni_med, pu, total_item, totalr, fechaentrega, representante, id_solicitud, sector, cumplidohastahoy, cumplidohoy, memo, memoitem
        FROM detalle_constancia_servicio
        WHERE constancia='".$_GET['nro_certificado']."'; ";
        //echo $sql_detalle_constancia_servicio;
        $resultado_detalle_constancia_servicio = $mysqli->query($sql_detalle_constancia_servicio) or die(mysqli_error($mysqli)); 

    }else{
        $sql_certificacion_servicios = "SELECT fechaemision, nro_oc, nro_certificado, total, proveedor, ultima_actualizacion
                                    FROM certificacion_servicio
                                    $where
                                    ORDER BY nro_certificado DESC; ";
        $constancia='';
    }
    
    //echo $sql_certificacion_servicios;
    $resultado_certificacion_servicios = $mysqli->query($sql_certificacion_servicios) or die(mysqli_error($mysqli)); 
    
    
        
    

    ?>