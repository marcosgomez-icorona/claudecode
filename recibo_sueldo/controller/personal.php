<?php

if(!empty($_POST['accion']) and $_POST['accion']=='alta_persona' ){
    agregar_persona($_POST['legajo'],$_POST['dni'],$_POST['nombre'],$_POST['tipo_convenio'],$_POST['tipo_liquidacion']);
}

if(!empty($_POST['accion']) and $_POST['accion']=='modificar_persona' ){
    modficar_datos_persona($_GET['id_personal'],$_POST['legajo'],$_POST['dni'],$_POST['nombre'],$_POST['tipo_convenio'],$_POST['tipo_liquidacion']);
}

//--------------FUNCIONES--------------------------//

function listado_personal($busqueda){

    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();
    
    if(!empty($busqueda)){
        $where="WHERE `personal`.nombre LIKE '%$busqueda%' OR personal.legajo LIKE '%$busqueda%' OR personal.dni LIKE '%$busqueda%' OR tipo_convenio LIKE '%$busqueda%' OR tipo_liquidacion LIKE '%$busqueda%'";
    }else{
        $where='';
    }

    $sql_personal ="SELECT id_personal, dni, legajo, nombre, tipo_convenio, tipo_liquidacion FROM personal $where ORDER BY legajo ASC;";
    $resultado_personal = $mysqli->query($sql_personal) or die($mysqli->error);
    return $resultado_personal;
}

function agregar_persona($legajo,$dni,$nombre,$tipo_convenio,$tipo_liquidacion){
    include_once 'conexiones/conexion.php';
    if(!empty($legajo) and !empty($dni) and !empty($nombre) and !empty($tipo_convenio) and !empty($tipo_liquidacion)){
        $sql_modificar_persona = "INSERT INTO personal (legajo,dni,nombre,tipo_convenio,tipo_liquidacion) VALUES ('$legajo','$dni','$nombre','$tipo_convenio','$tipo_liquidacion') ; ";        
        //echo $sql_modificar_persona;
        $mysqli->query($sql_modificar_persona) or die(mysqli_error($mysqli)); 

        if(mysqli_error($mysqli)==null){
            echo '<script>alert("Se genero correctamente...");</script>';
         }
    }else{
        echo '<script>alert("Datos incompletlos");</script>';
        exit;
    }
    
}
function busca_persona($id_personal){

    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();
    
    $sql_datos_persona ="SELECT id_personal, dni, legajo, nombre, tipo_convenio, tipo_liquidacion FROM personal WHERE id_personal=$id_personal;";
    $resultado_datos_persona = $mysqli->query($sql_datos_persona) or die($mysqli->error);
    $datos_persona=$resultado_datos_persona->fetch_assoc();
    return $datos_persona;
}

function modficar_datos_persona($id_personal,$legajo,$dni,$nombre,$tipo_convenio,$tipo_liquidacion){
    include_once 'conexiones/conexion.php';
    if(!empty($id_personal)){
        $sql_modificar_persona = "  update personal SET dni = '$dni',legajo = $legajo,nombre = '$nombre' ,tipo_convenio = '$tipo_convenio' ,tipo_liquidacion = '$tipo_liquidacion'
        WHERE id_personal = $id_personal ; ";        
        //echo $sql_modificar_persona;
        $mysqli->query($sql_modificar_persona) or die(mysqli_error($mysqli)); 

        if(mysqli_error($mysqli)==null){
            echo '<script>alert("Se Modifico correctamente...");</script>';
         }
    }
    
}
?>