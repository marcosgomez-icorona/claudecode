<?php
$anio='20'.date('y');
        

if(!empty($_POST['accion']) and $_POST['accion']=='alta_periodo' ){
    agregar_periodo($_POST['periodo'],$_POST['anio'],$_POST['tipo_liq'],$_POST['quincena']);

}else{
    if(!empty($_POST['accion']) and empty($_POST['periodo']) and empty($_POST['anio'])){
        echo "<script>alert('Datos Incompletos, intente nuevamente......')</script>";
    }
   
}

if(!empty($_POST['eliminar_liq'])){
    eliminar_liquidacion($_POST['eliminar_liq']);
}
//--------------FUNCIONES--------------------------//
function agregar_periodo($periodo,$anio,$tipo_liq,$quincena){
    
    include_once 'conexiones/conexion.php';
    
    $sql_agregar_liquidacion = "INSERT INTO liquidaciones (fecha,periodo,anio,tipo_liquidacion,quincena) VALUES (NOW(),$periodo,$anio,'$tipo_liq','$quincena');";        
    //echo $sql_agregar_liquidacion;
    $mysqli->query($sql_agregar_liquidacion) or die(mysqli_error($mysqli)); 
    

    $sql_id_liquidacion = "SELECT MAX(id_liquidacion) 'id_nueva_liquidacion' FROM liquidaciones;";        
    $nueva_liquidacion= $mysqli->query($sql_id_liquidacion) or die(mysqli_error($mysqli)); 
    $id_nueva_liquidacion= $nueva_liquidacion->fetch_assoc();
    $id_liquidacion=$id_nueva_liquidacion['id_nueva_liquidacion'];

    if(!empty($quincena)){
        $nombre_recibo=$anio.$periodo."-".$tipo_liq."-".$quincena;
    }else{
        $nombre_recibo=$anio.$periodo."-".$tipo_liq;
    }

    if($tipo_liq=='MENSUAL' OR $tipo_liq=='QUINCENAL'){
        $where="WHERE tipo_liquidacion='$tipo_liq'";
    }else{
        $where=" ";//PARA EL CASO DEL SAC Y VACACIONES SE GENERA LA LIQUIDACION PARA TODOS LOS EMPLEADOS
    }
    
    $sql_agregar_periodo = "INSERT INTO recibos (id_liquidacion,legajo, recibo) SELECT $id_liquidacion AS id_liquidacion,legajo, CONCAT(legajo,'-',tipo_convenio,'-','".$nombre_recibo."','.pdf') AS recibo FROM personal 
                                                                                $where;";     
                                                                                        
    //echo $sql_agregar_periodo;
    $mysqli->query($sql_agregar_periodo) or die(mysqli_error($mysqli)); 
    if(mysqli_error($mysqli)==null){
       echo '<script>alert("Se genero correctamente...");</script>';
    }  

}
function listado_liquidaciones($busqueda){

    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();
    if(!empty($busqueda)){
        $where="WHERE periodo LIKE '%$busqueda%' OR anio LIKE '%$busqueda%' OR tipo_liquidacion LIKE '%$busqueda%' OR quincena LIKE '%$busqueda%' OR CONCAT(periodo,'-',anio) LIKE '%$busqueda%'";
    }else{
        $where=' ';
    }
    $sql_liquidaciones ="SELECT id_liquidacion, fecha, periodo, anio, quincena, tipo_liquidacion, tipo_convenio 
                        FROM liquidaciones 
                        $where ORDER BY anio desc,periodo desc;";    
    $resultado_liquidaciones = $mysqli->query($sql_liquidaciones) or die($mysqli->error);
    return $resultado_liquidaciones;
}

function eliminar_liquidacion($id_liquidacion){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();

    $sql_eliminar_recibos_liq = "DELETE FROM recibos WHERE id_liquidacion=$id_liquidacion ;";        
    //echo $sql_eliminar_recibos_liq;
    $mysqli->query($sql_eliminar_recibos_liq) or die(mysqli_error($mysqli));
    
    $sql_eliminar_liq = "DELETE FROM liquidaciones WHERE id_liquidacion=$id_liquidacion ;";        
    //echo $sql_eliminar_liq;
    $mysqli->query($sql_eliminar_liq) or die(mysqli_error($mysqli));

    if(mysqli_error($mysqli)==null){
       echo '<script>   alert("Se elimino correctamente...")                        
            </script>';
       
       //header('Location: home.php?menu=ver_liquidaciones');
    }
}

function lista_tipos_convenios(){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();

    $sql_tipos_convenios =" SELECT id_tipo_convenio, tipo_convenio FROM tipos_convenio ORDER BY tipo_convenio ASC;";
    $resultado_tipos_convenios = $mysqli->query($sql_tipos_convenios) or die($mysqli->error);
    return $resultado_tipos_convenios;
}

function ver_liquidaciones($periodo,$anio){
    if(!empty($row_resultado_liquidaciones['periodo'])){ echo "<a href='assets/recibos/index.php?periodo=".$row_resultado_liquidaciones['periodo']."&anio=".$row_resultado_liquidaciones['anio']."' target='_blank'>".$row_resultado_liquidaciones['periodo']."-".$row_resultado_liquidaciones['anio']."</a>"; }
}
/*


function ver_recibo($periodo){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();

    $sql_recibo ="SELECT periodo, idorden, archivo, control FROM recibos
                    where periodo=".$periodo.";";
    $resultado_recibo = $mysqli->query($sql_recibo) or die($mysqli->error);
    $recibo = $resultado_recibo->fetch_assoc();    
    return $recibo;
}


function modificar_periodo($periodo,$archivo){
     
    include_once 'conexiones/conexion.php';
    //----------------//
    $sql_agregar_recibo = "update recibos SET archivo = '".$archivo."' WHERE periodo = ".$periodo.";";
    $mysqli->query($sql_agregar_recibo) or die(mysqli_error($mysqli)); 
    if(mysqli_error($mysqli)==null){
       echo '<script>alert("Se actualizo correctamente...");</script>';
    }  

}
*/
?>
<script>
    /*
    function ver_recibos(periodo)
    {
        sel = window.open("view.php?opcion=ver_recibos&periodo="+periodo, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }

    function modificar_recibos(periodo,periodo)
    {
        sel = window.open("view.php?opcion=modificar_recibos&periodo="+periodo+"&periodo="+periodo, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }
    */
</script>