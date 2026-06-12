<?php

    include 'conexiones/conexion.php';
    include 'funciones/funciones.php';

    $filtro='Where 1=1 ';

    if(!empty($_GET['id_alerta'])){
        $id_alerta=$_GET['id_alerta'];
    }else{
        $id_alerta='-1';
    }

    if(!empty($_POST['id_equipo'])){
        $id_equipo=$_POST['id_equipo'];
    }else{
        $id_equipo='';
    }

    if(!empty($_POST['desde'])){
        $desde=FechaAmd($_POST['desde']);
        $filtro= $filtro." and fecha >='".$desde."' ";        
    }else{
        $desde='NULL';
    }
    if(!empty($_POST['hasta'])){
        $hasta=FechaAmd($_POST['hasta']);
        $filtro= $filtro." and fecha <='".$hasta."' ";        
    }else{
        $hasta='NULL';
    }
    if(isset($_POST['estado']) AND $_POST['estado']<>''){
        $estado=$_POST['estado'];
        $filtro= $filtro." and estado =".$estado." ";        
    }else{
        $estado='NULL';
    }
    if(isset($_POST['falla_termica']) AND $_POST['falla_termica']<>''){
        $falla_termica=$_POST['falla_termica'];
        $filtro= $filtro." and falla_termica ='".$falla_termica."' ";        
    }else{
        $falla_termica='NULL';
    }
    
    if(!empty($_POST['temperatura'])){
        $temperatura=$_POST['temperatura'];
        if($temperatura=='Normal'){
            $filtro= $filtro." and temperatura < 50 ";        
        }
        if($temperatura=='Elevada'){
            $filtro= $filtro." and temperatura >= 50 AND temperatura < 100 ";        
        }
        if($temperatura=='Critica'){
            $filtro= $filtro." and temperatura >= 100 ";        
        }
    }else{
        $temperatura='NULL';
    }
    
    
//------------ALTA Y MODIFICACION-----------------------//

//-----------------------------------------------------//

if(!empty($_GET['id_alerta'])){
                
    $sql_alertas = "SELECT id_alerta, estado, falla_termica, corriente, temperatura, fecha, `equipos`.id_equipo, `equipos`.codigoequipo, `equipos`.codigonivel, `equipos`.descripcion, `equipos`.descripcionampliada, `equipos`.idtipoequipo, 
                                `equipos`.centrocosto_id, `equipos`.periodoplan, `equipos`.fechainicioplan, `equipos`.criticidad
                                FROM alertas INNER JOIN equipos ON alertas.id_equipo = equipos.id_equipo
                                where id_alerta=".$_GET['id_alerta']." ORDER BY fecha DESC;";
    //echo $sql_alertas;
    $resultado_alertas = $mysqli->query($sql_alertas) or die(mysqli_error($mysqli));   
    $row_resultado_alertas = $resultado_alertas->fetch_assoc(); 
}
//----------------------------------------------------------------------------------------------------------//

//------------BAJA-----------------------//
if(!empty($_GET['accion']) and $_GET['accion']=='baja' and !empty($_GET['id_alerta'])){
    
    $sql_inactalertas = " DELETE FROM alertas WHERE id_alerta =".$_GET['id_alerta'].";";
    //echo $sql_inactalertas;
    $resultado_inactalertas = $mysqli->query($sql_inactalertas) or die(mysqli_error($mysqli));


}
//---------------------------------------//

//---------------LISTADO---------------------//

$sql_alertas =" SELECT id_alerta, estado, falla_termica, corriente, temperatura, fecha, `equipos`.id_equipo, `equipos`.codigoequipo, `equipos`.codigonivel, `equipos`.descripcion 'equipo', `equipos`.descripcionampliada, `equipos`.idtipoequipo, 
                `equipos`.centrocosto_id, `equipos`.periodoplan, `equipos`.fechainicioplan, `equipos`.criticidad
                FROM alertas INNER JOIN equipos ON alertas.id_equipo = equipos.id_equipo
                ".$filtro." ORDER BY alertas.fecha DESC;";
//echo $sql_alertas;
$resultado_alertas = $mysqli->query($sql_alertas) or die($mysqli->error);

//---------------------------------------//

//-----equipos----------//
if(!empty($_GET['buscar_equipos'])){
    $buscar_equipos=$_GET['buscar_equipos'];
    $filtro_equipo="Where descripcion LIKE '%".$buscar_equipos."%' OR codigoequipo LIKE '%".$buscar_equipos."%'";        
}

if(empty($filtro_equipo)){$filtro_equipo='';}

$sql_equipos = " select id_equipo, codigoequipo, codigonivel, descripcion 'equipo', descripcionampliada, idtipoequipo, centrocosto_id, periodoplan, fechainicioplan, criticidad 
FROM equipos ".$filtro_equipo." ORDER BY descripcion ASC;";
//echo $sql_equipos;
$resultado_equipos = $mysqli->query($sql_equipos) or die($mysqli->error);

if(!empty($_GET['id_equipo'])){
    $sql_equipo_seleccionado = "select id_equipo, descripcion 'equipo'
                                FROM equipos where id_equipo=".$_GET['id_equipo'].";";
                                //echo $sql_equipo_seleccionado;
    $resultado_equipo_seleccionado = $mysqli->query($sql_equipo_seleccionado) or die($mysqli->error);
    $row_resultado_equipo_seleccionado = $resultado_equipo_seleccionado->fetch_assoc();

    $sql_alertaes_equipo =" select id_alerta, equipos.id_equipo, lado, mm_s, ge, temperatura, fecha,equipos.codigoequipo, equipos.descripcion 'equipo', equipos.criticidad
    FROM alertas inner join equipos on alertas.id_equipo = equipos.id_equipo
    Where equipos.id_equipo=".$_GET['id_equipo']." order by fecha desc;";
    //echo $sql_alertas;
    $resultado_alertaes_equipo = $mysqli->query($sql_alertaes_equipo) or die($mysqli->error);
    $row_resultado_alertaes_equipo = $resultado_alertaes_equipo->fetch_assoc();
}

//----------------------------------------------------------------------------------------------------/
function asignar_color_alerta($parametro,$valor){
    if($parametro=='ge' and !empty($valor)){
        if($valor<1){
            $class_alerta='class="table-success"';
        }else{
            if($valor>=1 and $valor<3){
                $class_alerta='class="table-warning"';
            }else{
                if($valor>=3){
                    $class_alerta='class="table-danger"';
                }
            }
        }
    }else{
        if($parametro=='mm_s' and !empty($valor)){
            if($valor<1){
                $class_alerta='class="table-success"';
            }else{
                if($valor>=1 and $valor<3){
                    
                    $class_alerta='class="table-warning"';
                    //echo '<script>alert("'.$class_alerta.'");</script>';
                }else{
                    if($valor>=3){
                        $class_alerta='class="table-danger"';
                    }
                }
            }
        }
    }
    if(empty($class_alerta)){
        $class_alerta='';
    }
    return $class_alerta;
}
//-------------------------------------------------------------------------------------------------------------//
?>
<script>
    $(function() {
        $( "#datepicker" ).datepicker({ 
            dateFormat: "dd/mm/yy", 
            autoSize: true, 
            buttonText: "Choose", 
            showButtonPanel: true, 
            closeText: "Cerrar", 
            currentText:"Hoy",
            dayNamesShort:["Dom","Lu","Mar","Mier","Jue","Vier","Sab"],
            dayNamesMin:["Dom","Lu","Mar","Mier","Jue","Vier","Sab"],
            monthNames: ["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"] }
        );
    });
    $(function() {
        $( "#datepicker1" ).datepicker({ 
            dateFormat: "dd/mm/yy", 
            autoSize: true, 
            buttonText: "Choose", 
            showButtonPanel: true, 
            closeText: "Cerrar", 
            currentText:"Hoy",
            dayNamesShort:["Dom","Lu","Mar","Mier","Jue","Vier","Sab"],
            dayNamesMin:["Dom","Lu","Mar","Mier","Jue","Vier","Sab"],
            monthNames: ["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"] }
        );
    });
    $(function() {
        $( "#datepicker2" ).datepicker({ 
            dateFormat: "dd/mm/yy", 
            autoSize: true, 
            buttonText: "Choose", 
            showButtonPanel: true, 
            closeText: "Cerrar", 
            currentText:"Hoy",
            dayNamesShort:["Dom","Lu","Mar","Mier","Jue","Vier","Sab"],
            dayNamesMin:["Dom","Lu","Mar","Mier","Jue","Vier","Sab"],
            monthNames: ["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"] }
        );
    });

    function ver_equipos(buscar_equipos)
    {
        sel = window.open("home.php?accion=busqueda_equipo&buscar_equipos="+buscar_equipos, "popup", "width=450,height=800,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }
    function elegir_equipo(id,codigo,nombre)
    {
        formulario = opener.document.getElementById('form');
        formulario.id_equipo.value = id;
        formulario.equipo.value = codigo+' - '+nombre;
        //formulario.ejecutar.click();
        close();
    }
</script>