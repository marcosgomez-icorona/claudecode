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

    /* ACTUALIZO EL VALOR DEL SALDO ACUMULADO */
        $sql_pendiente_act_saldo = "  SELECT comprobante, debe, haber, saldo,saldo_acumulado, importe, fecha_ingreso, fecha_vto, tipo_pago, proveedor, ultima_actualizacion
                                    FROM facturas_pendiente_pago
                                    $where and year(fecha_ingreso)>=2025
                                    ORDER BY fecha_ingreso ASC;";
        //echo $sql_pendiente_act_saldo;
        $resultado_pendiente_act_saldo = $mysqli->query($sql_pendiente_act_saldo) or die(mysqli_error($mysqli));
        $saldo_acumulado=0;
        while ($row_act_saldo = $resultado_pendiente_act_saldo->fetch_assoc()) {
            $proveedor = $row_act_saldo['proveedor'] ?? '';
            $valor = $row_act_saldo['haber'] ?? 0;
            $comprobante = $row_act_saldo['comprobante'] ?? '';
            $saldo_acumulado = $saldo_acumulado-$valor;
            //ACTUALIZO SALDO
            
                $sql = "update facturas_pendiente_pago SET saldo_acumulado='$saldo_acumulado' where comprobante='$comprobante' and proveedor='$proveedor' ";    
                //echo $sql;
                $mysqli->query($sql) or die(mysqli_error($mysqli));
            

        }        
    /* CONTROL DE SALDO CON CTA CTE*/

    
        $sql_facturacion_pendiente = "  SELECT comprobante, debe, haber, saldo, saldo_acumulado, importe, fecha_ingreso, fecha_vto, tipo_pago, proveedor, ultima_actualizacion
                                        FROM facturas_pendiente_pago
                                        $where and year(fecha_ingreso)>=2025
                                        ORDER BY fecha_ingreso DESC, comprobante DESC; ";
        //echo $sql_facturacion_pendiente;
        $resultado_facturacion_pendiente = $mysqli->query($sql_facturacion_pendiente) or die(mysqli_error($mysqli));   
    ?>