<?php
function lista_tipos_convenios(){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();

    $sql_tipos_convenios =" SELECT id_tipo_convenio, tipo_convenio FROM tipos_convenio ORDER BY tipo_convenio ASC;";
    $resultado_tipos_convenios = $mysqli->query($sql_tipos_convenios) or die($mysqli->error);
    return $resultado_tipos_convenios;
}
?>