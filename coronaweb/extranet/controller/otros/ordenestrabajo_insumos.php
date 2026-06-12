<?php

if(!empty($_POST['accion']) and $_POST['accion']=='alta_ordenestrabajo_insumos' and !empty($_GET['nro_ot'])){
    
    //echo '<script>alert("Se cargo OT correctamente...");</script>';
    agregar_repuestos_insumos_ot($_GET['nro_ot'],$_POST['idproducto'],$_POST['codproducto'],$_POST['descripcion'],$_POST['cantidad'],$_POST['costo']);

}

if(!empty($_POST['accion']) and $_POST['accion']=='modificar_ordenestrabajo_insumo' and !empty($_POST['id_ot_insumo']) and $_POST['retirado']<>'S'){    
    
    modificar_ordenestrabajo_insumo(   $_POST['id_ot_insumo'],$_POST['cantidad'],$_POST['costo'],$_POST['retirado']);

}

if(!empty($_POST['eliminar_insumo_asociado'])){
    eliminar_insumo_asociado($_POST['eliminar_insumo_asociado']);
}

if(!empty($_POST['retira_insumo'])){
    modificar_ordenestrabajo_insumo($_POST['retira_insumo'],-1,0,'S');
}

//--------------FUNCIONES--------------------------//
function listado_repuestos_insumos_ot($nro_ot){

    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();
    $sql_ordenestrabajo_insumos ="  SELECT id_ot_insumo, idordendetrabajo, idproducto, codigo, descripcion, cantidad, stock, control,retirado,costo, 
                                            (SELECT SUM(costo) FROM ordenestrabajo_insumos insumos_ot WHERE insumos_ot.idordendetrabajo=".$nro_ot.") 'total' 
                                    FROM ordenestrabajo_insumos WHERE idordendetrabajo=".$nro_ot." order by idordendetrabajo desc;";  
    //echo $sql_ordenestrabajo_insumos;                                                 
    $resultado_ordenestrabajo_insumos = $mysqli->query($sql_ordenestrabajo_insumos) or die($mysqli->error);
    return $resultado_ordenestrabajo_insumos;
}

function listado_pendiente_entrega($busqueda){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();

    if(!empty($busqueda)){
        $where="WHERE retirado='N' and (codigo LIKE '%$busqueda%' OR descripcion LIKE '%$busqueda%' )";
    }else{
        $where=" WHERE retirado='N' ";
    }
    $sql_pendientes_entrega ="  SELECT id_ot_insumo, idordendetrabajo, idproducto, codigo, descripcion, cantidad, stock, control,retirado,costo 
                                    FROM ordenestrabajo_insumos $where order by idordendetrabajo desc;";  
    //echo $sql_pendientes_entrega;                                                 
    $resultado_pendientes_entrega = $mysqli->query($sql_pendientes_entrega) or die($mysqli->error);
    return $resultado_pendientes_entrega;
}

function obtener_equipo_ot($nro_ot){

    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();
    $sql_equipo_ot ="   SELECT id_equipo, equipos.codigoequipo, descripcion 
                        FROM equipos INNER JOIN ordenes_trabajo on equipos.id_equipo = ordenes_trabajo.codigoequipo
                        where ordenes_trabajo.numero=".$nro_ot."; ";
    $resultado_equipo_ot = $mysqli->query($sql_equipo_ot) or die($mysqli->error);
    $equipo=$resultado_equipo_ot->fetch_assoc();
    return $equipo['id_equipo'];
}

function agregar_repuestos_insumos_ot($idordendetrabajo,$idproducto,$codproducto,$descripcion,$cantidad,$costo){
      
        include_once 'conexiones/conexion.php';
        
            $sql_ordenestrabajo_insumos = "  insert into ordenestrabajo_insumos ( idordendetrabajo ,idproducto ,codigo ,descripcion ,cantidad,costo)
                                        VALUE(".$idordendetrabajo." ,'".$idproducto."','".$codproducto."','".$descripcion."',".$cantidad.",".$costo.");";
        
            //echo $sql_ordenestrabajo_insumos;
            $mysqli->query($sql_ordenestrabajo_insumos) or die(mysqli_error($mysqli)); 
            if(mysqli_error($mysqli)==null){
                echo '<script>alert("Se cargo correctamente...");</script>';
            }  
}

function modificar_ordenestrabajo_insumo($id_ot_insumo,$cantidad,$costo,$retirado){
    include_once 'conexiones/conexion.php';    
    $mysqli=conexion_db();
    
    if($retirado<>'S'){
        $sql_agregar_insumo_ot = "  UPDATE ordenestrabajo_insumos SET cantidad = $cantidad, costo = $costo WHERE id_ot_insumo = $id_ot_insumo AND retirado<>'S' ; ";               
        $mysqli->query($sql_agregar_insumo_ot) or die(mysqli_error($mysqli)); 
        if(mysqli_error($mysqli)==null){
           echo '<script>alert("Se Actualizo correctamente...");</script>';
        }  
    }else{
        if($retirado=='S'){
            if($cantidad==-1){
                $sql_entrega= "  UPDATE ordenestrabajo_insumos SET retirado='S' WHERE id_ot_insumo = $id_ot_insumo; ";                               
                $mysqli->query($sql_entrega) or die(mysqli_error($mysqli)); 
                if(mysqli_error($mysqli)==null){
                   echo '<script>alert("Entregado");</script>';
                }  
            }else{
                echo '<script>alert("No se puede modificar porque ya fue retirado...");</script>';
            }
            
         } 
         
    }
    
} 

function eliminar_insumo_asociado($id_ot_insumo){
    include_once 'conexiones/conexion.php';    
    $mysqli=conexion_db();

    //controlo si fue retirado
    $insumo_ot=ver_insumo_ot($id_ot_insumo);
    if($insumo_ot['retirado']==='N'){
        $sql_eliminar_insumo_ot = "  DELETE FROM ordenestrabajo_insumos WHERE id_ot_insumo=$id_ot_insumo ; ";               
        $mysqli->query($sql_eliminar_insumo_ot) or die(mysqli_error($mysqli)); 
    }else{
        if($insumo_ot['retirado']==='S'){
            echo '<script>alert("No se puede eliminar por que ya fue retirado....");</script>';
        }
    }
    
}

function ver_insumo_ot($id_ot_insumo){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();
    
    $sql_insumo_ot ="   select id_ot_insumo, idordendetrabajo, idproducto, codigo, descripcion, cantidad,costo, stock, control, retirado
                        FROM ordenestrabajo_insumos where id_ot_insumo=".$id_ot_insumo.";";
    //echo $sql_insumo_ot;
    $resultado_insumo_ot = $mysqli->query($sql_insumo_ot) or die($mysqli->error);
    $row_resultado_insumo_ot = $resultado_insumo_ot->fetch_assoc();
    return $row_resultado_insumo_ot;
}
 
?>
<script>
    function ver_ordenestrabajo_insumos(nro_ot)
    {
        sel = window.open("view.php?opcion=ver_ordenestrabajo_insumos&nro_ot="+nro_ot, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }

    function modificar_ordenestrabajo_insumo(nro_ot,id_ot_insumo,retirado)
    {
        sel = window.open("view.php?opcion=modificar_ordenestrabajo_insumo&nro_ot="+nro_ot+"&id_ot_insumo="+id_ot_insumo+"&retirado="+retirado, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }
</script>