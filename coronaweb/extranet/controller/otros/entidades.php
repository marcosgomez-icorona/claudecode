<?php 

function lista_ejecutantes(){

    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();
    if(!empty($mysqli)){
        $sql_ejecutante = "SELECT id_entidad, nombre FROM entidades where tipo='Ejecutante' order by nombre asc;";
        //echo $sql_Ejecutante;
        $resultado_ejecutante = $mysqli->query($sql_ejecutante) or die(mysqli_error($mysqli));
    
        return $resultado_ejecutante;
    }else{
        echo "<script> alert('No se puede establecer la conexion')</script>";
    }
    
}

function lista_originador(){
    
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();
    $sql_originador = "SELECT id_entidad, nombre FROM entidades where tipo='Originador' order by nombre asc;";
    $resultado_originador = $mysqli->query($sql_originador) or die(mysqli_error($mysqli));
      
      return $resultado_originador;
}

?>