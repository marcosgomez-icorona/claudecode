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

    if(!empty($_POST['numero'])){
        $numero=$_POST['numero'];
        $filtro= "Where numero='".$numero."' ";
    }else{
        $numero='';
    }

    if(!empty($_POST['fechainicio']) and !empty($_POST['fechafin'])){
        $fechainicio=fechaAmd($_POST['fechainicio']);  
        $fechafin=fechaAmd($_POST['fechafin']); 
        if($filtro==""){
            $filtro= "Where fechainicio>='".$fechainicio."' AND fechafin<='".$fechafin."'";
        }else{
            $filtro= $filtro." and fechainicio>='".$fechainicio."' AND fechafin<='".$fechafin."'";
        }
    }else{
        if(!empty($_POST['fechainicio'])){
            $fechainicio=fechaAmd($_POST['fechainicio']);  
            if($filtro==""){
                $filtro= "Where fechainicio='".$fechainicio."' ";
            }else{
                $filtro= $filtro."and fechainicio='".$fechainicio."' ";
            }      
        }else{
            $fechainicio='';
        }
    
        if(!empty($_POST['fechafin'])){
            $fechafin=fechaAmd($_POST['fechafin']); 
            if($filtro==""){
                $filtro= "Where fechafin='".$fechafin."' ";
            }else{
                $filtro= $filtro."and fechafin='".$fechafin."' ";
            }         
        }else{
            $fechafin='';
        }
    
    }

   
   
   /* SALDO ANTERIOR DE FACTURAS ANTERIOR AL 2024 */
   
   $sql_saldo_anterior = "  SELECT SUM(haber) 'saldo_anterior'
                                    FROM facturas_pendiente_pago
                                    $where and YEAR(fecha_ingreso)<2025;";
    //echo $sql_saldo_anterior;
    $resultado_saldo_anterior = $mysqli->query($sql_saldo_anterior) or die(mysqli_error($mysqli));
    $row_saldo_anterior = $resultado_saldo_anterior->fetch_assoc();
    $saldo_anterior=$row_saldo_anterior['saldo_anterior'] ?? 0;
    
    $saldo_acumulado=-$saldo_anterior;
    //echo $saldo_acumulado;
    /* ACTUALIZO EL VALOR DEL SALDO ACUMULADO */
   $sql_cta_cte_act_saldo = "SELECT comprobante, debe, haber, saldo,saldo_acumulado, importe, fecha_ingreso, fecha_vto, tipo_pago, proveedor, ultima_actualizacion
                    FROM cuenta_corriente
                    $where  and YEAR(fecha_ingreso)>=2025
                    ORDER BY fecha_ingreso ASC, comprobante ASC;   ";
    //echo $sql_cta_cte_act_saldo;
    $resultado_cta_cte_act_saldo = $mysqli->query($sql_cta_cte_act_saldo) or die(mysqli_error($mysqli));
    //echo $saldo_acumulado;
    while ($row_act_saldo = $resultado_cta_cte_act_saldo->fetch_assoc()) {
        $proveedor = $row_act_saldo['proveedor'] ?? '';
        $valor = $row_act_saldo['importe'] ?? 0;
        $saldo = $row_act_saldo['saldo'] ?? '';
        $comprobante = $row_act_saldo['comprobante'] ?? '';
        $saldo_acumulado=$saldo_acumulado+$saldo;
        if($saldo_acumulado==-0){$saldo_acumulado=0;}
        //ACTUALIZO SALDO
        //if(empty($row_act_saldo['saldo_acumulado'])){
            $sql = "update cuenta_corriente SET saldo_acumulado='$saldo_acumulado' 
                    WHERE REPLACE(comprobante, ' ', '') = REPLACE('$comprobante', ' ', '') and proveedor='$proveedor'; ";                
            $mysqli->query($sql) or die(mysqli_error($mysqli));
        //}
            
        
        
    }
   /* ORDENO LA CUENTA EN FORMA DESCENDENTE*/
    
    $sql_cta_cte = "SELECT comprobante, debe, haber, saldo,saldo_acumulado, importe, fecha_ingreso, fecha_vto, tipo_pago, proveedor, ultima_actualizacion
                    FROM cuenta_corriente
                    $where and YEAR(fecha_ingreso)>=2025
                    ORDER BY fecha_ingreso DESC,comprobante DESC;   ";
    //echo $sql_cta_cte;
    $resultado_cta_cte = $mysqli->query($sql_cta_cte) or die(mysqli_error($mysqli));   

    ?>