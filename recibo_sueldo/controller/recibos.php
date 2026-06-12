<?php
$anio='20'.date('y');
        

if(!empty($_POST['accion']) and $_POST['accion']=='alta_periodo' ){
    //agregar_periodo($_POST['periodo'],$_POST['anio']);

}else{
    if(!empty($_POST['accion']) and empty($_POST['periodo']) and empty($_POST['anio'])){
        //echo "<script>alert('Datos Incompletos, intente nuevamente......')</script>";
    }
   
}

//--------------FUNCIONES--------------------------//

function listado_recibos($liq_legajo,$tipo,$busqueda){

    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();
    
    if($tipo=='empleados'){
        $where='WHERE recibos.id_liquidacion="'.$liq_legajo.'"';
    }else{
        if($tipo=='empleado'){
            $where='WHERE personal.legajo="'.$liq_legajo.'"';
        }   
    }
    if(!empty($busqueda)){
        $where="WHERE recibos.id_liquidacion=$liq_legajo AND (`personal`.nombre LIKE '%$busqueda%' OR recibos.legajo LIKE '%$busqueda%')";
    }
    
    $sql_recibos =" SELECT `personal`.id_personal, `personal`.legajo, `personal`.nombre, recibos.recibo,liquidaciones.periodo,liquidaciones.anio, liquidaciones.tipo_liquidacion,
                    liquidaciones.tipo_convenio, liquidaciones.quincena 
                    FROM recibos 
                    INNER JOIN liquidaciones ON recibos.id_liquidacion = liquidaciones.id_liquidacion
                    INNER JOIN personal ON recibos.legajo = personal.legajo    
                    $where
                    order by recibos.legajo ASC, anio DESC, periodo DESC;";                   
    $resultado_recibos = $mysqli->query($sql_recibos) or die($mysqli->error);
    return $resultado_recibos;
}

?>
<script>
    function ver_recibos(periodo)
    {
        sel = window.open("view.php?opcion=ver_recibos&periodo="+periodo, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }

    function modificar_recibos(periodo,periodo)
    {
        sel = window.open("view.php?opcion=modificar_recibos&periodo="+periodo+"&periodo="+periodo, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }
</script>