<?php

if(!empty($_POST['accion']) and $_POST['accion']=='alta_tarea' and !empty($_GET['nro_ot'])){
    
    //echo '<script>alert("Se cargo OT correctamente...");</script>';
    agregar_tarea_ot(   $_GET['nro_ot'],$_POST['tarea'],$_POST['descripcion'],$_POST['ejecutante'],$_POST['operador'],$_POST['cantoperarios'],
                        $_POST['tiempo'],$_POST['bloqueo'],$_POST['paradamolienda']);

}

if(!empty($_POST['accion']) and $_POST['accion']=='modificar_tarea' and !empty($_GET['idtarea'])){
    
    //echo '<script>alert("Se cargo OT correctamente...");</script>';
    modificar_tarea_ot(   $_GET['idtarea'],$_POST['tarea'],$_POST['descripcion'],$_POST['ejecutante'],$_POST['operador'],$_POST['cantoperarios'],
                        $_POST['tiempo'],$_POST['bloqueo'],$_POST['paradamolienda']);
}

if(!empty($_POST['eliminar_tarea_ot'])){
    eliminar_tarea_ot($_POST['eliminar_tarea_ot']);
}

//--------------FUNCIONES--------------------------//
function listado_tareas_ot($nro_ot){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();

    $sql_tareas_ot =" select id_ot_tareas, idordendetrabajo, tarea, descripcion, ejecutante, operador, cantoperarios, tiempo, bloqueo, control, paradamolienda
    FROM ordernestrabajo_tareas 
    where idordendetrabajo=".$nro_ot." order by idordendetrabajo desc";
    $resultado_tareas_ot = $mysqli->query($sql_tareas_ot) or die($mysqli->error);
    return $resultado_tareas_ot;
}

function agregar_tarea_ot($idordendetrabajo,$tarea,$descripcion,$ejecutante,$operador,$cantoperarios,$tiempo,$bloqueo,$paradamolienda){     
        include_once 'conexiones/conexion.php';
        $mysqli=conexion_db();
       
        $sql_agregar_orden_trabajo = "  insert into ordernestrabajo_tareas (idordendetrabajo ,tarea,descripcion,ejecutante,operador,cantoperarios,tiempo,bloqueo,paradamolienda)
                                        VALUE(".$idordendetrabajo." ,'".$tarea."','".$descripcion."','".$ejecutante."','".$operador."' ,'".$cantoperarios."','".$tiempo."',
                                              '".$bloqueo."','".$paradamolienda."');";
        //echo $sql_agregar_orden_trabajo;
        //$resultado_orden_trabajo = 
        $mysqli->query($sql_agregar_orden_trabajo) or die(mysqli_error($mysqli)); 
        if(mysqli_error($mysqli)==null){
           echo '<script>alert("Se genero correctamente...");</script>';
        }  
    
}

function modificar_tarea_ot($idtarea,$tarea,$descripcion,$ejecutante,$operador,$cantoperarios,$tiempo,$bloqueo,$paradamolienda){
     
    // 
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();

    $sql_agregar_orden_trabajo = "  UPDATE ordernestrabajo_tareas SET tarea='".$tarea."',descripcion='".$descripcion."',ejecutante='".$ejecutante."',operador='".$operador."',
                                    cantoperarios='".$cantoperarios."',tiempo='".$tiempo."',bloqueo='".$bloqueo."',paradamolienda='".$paradamolienda."'
                                    WHERE id_ot_tareas =".$idtarea."; ";
    //echo $sql_agregar_orden_trabajo;
    
    $mysqli->query($sql_agregar_orden_trabajo) or die(mysqli_error($mysqli)); 
    if(mysqli_error($mysqli)==null){
       echo '<script>alert("Se actualizo correctamente...");</script>';
    }  

}

function ver_tarea_ot($idtarea){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();
    
    $sql_tarea_ot =" select id_ot_tareas, idordendetrabajo, tarea, descripcion, ejecutante, operador, cantoperarios, tiempo, bloqueo, control, paradamolienda
    FROM ordernestrabajo_tareas where id_ot_tareas=".$idtarea.";";
    //echo $sql_tarea_ot;
    $resultado_tarea_ot = $mysqli->query($sql_tarea_ot) or die($mysqli->error);
    $row_resultado_tarea_ot = $resultado_tarea_ot->fetch_assoc();
    return $row_resultado_tarea_ot;
}

function eliminar_tarea_ot($id_tarea_ot){
    include_once 'conexiones/conexion.php';    
    $mysqli=conexion_db();

    $sql_eliminar_insumo_ot = "  DELETE FROM ordernestrabajo_tareas WHERE id_ot_tareas = $id_tarea_ot ; ";               
    $mysqli->query($sql_eliminar_insumo_ot) or die(mysqli_error($mysqli)); 
}

?>
<script>
    function ver_tareas_ot(nro_ot)
    {
        sel = window.open("view.php?opcion=ver_tareas_ot&nro_ot="+nro_ot, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }
    function modificar_tarea_ot(nro_ot,idtarea)
    {
        sel = window.open("view.php?opcion=modificar_tarea_ot&nro_ot="+nro_ot+"&idtarea="+idtarea, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }
</script>