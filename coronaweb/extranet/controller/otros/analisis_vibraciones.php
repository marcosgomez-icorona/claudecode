<?php

    include 'conexiones/conexion.php';
    include 'funciones/funciones.php';

    $filtro='Where 1=1 ';

    if(!empty($_GET['id_analisis'])){
        $id_analisis=$_GET['id_analisis'];
    }else{
        $id_analisis='-1';
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
    if(!empty($_POST['lado'])){
        $lado=$_POST['lado'];
        $filtro= $filtro." and lado ='".$lado."' ";        
    }else{
        $lado='NULL';
    }
    if(!empty($_POST['mm_s'])){
        $mm_s=$_POST['mm_s'];
        if($mm_s=='Normal'){
            $filtro= $filtro." and mm_s < 1 ";        
        }
        if($mm_s=='Elevado'){
            $filtro= $filtro." and mm_s >= 1 AND mm_s < 3 ";        
        }
        if($mm_s=='Critico'){
            $filtro= $filtro." and mm_s >= 3 ";        
        }
    }else{
        $mm_s='NULL';
    }

    

    if(!empty($_POST['ge'])){
        $ge=$_POST['ge'];
        if($ge=='Normal'){
            $filtro= $filtro." and ge < 1 ";        
        }
        if($ge=='Elevada'){
            $filtro= $filtro." and ge >= 1 AND ge < 3 ";        
        }
        if($ge=='Critica'){
            $filtro= $filtro." and ge >= 3 ";        
        }
    }else{
        $ge='NULL';
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

if(!empty($_POST) and !empty($_POST['id_equipo']) and !empty($_POST['lado'])){
        
    if($_POST['accion']=='alta'){
        $sql_alta_analisis_vibraciones = "   insert into analisis_vibraciones (id_equipo  ,lado  ,mm_s  ,ge  ,temperatura  )
                                    VALUES (".$id_equipo.",'".$lado."',".$mm_s.",".$ge.",".$temperatura.");";
        //echo $sql_alta_analisis_vibraciones;
        $resultado_alta_analisis_vibraciones = $mysqli->query($sql_alta_analisis_vibraciones) or die(mysqli_error($mysqli));

        if(mysqli_error($mysqli)==null){
            echo '<script>alert("Se cargo");</script>';
        }
    }else{
            if($_POST['accion']=='modificar' and !empty($_GET['id_analisis'])){
                $sql_mod_analisis_vibraciones = "   update analisis_vibraciones SET id_equipo = '".$id_equipo."' ,                                                                                       
                                                    lado = '".$lado."' ,mm_s = ".$mm_s.",ge = ".$ge." ,temperatura = ".$temperatura."  
                                                    WHERE id_analisis =".$_GET['id_analisis'].";";
                //echo $sql_mod_analisis_vibraciones;
                $resultado_mod_analisis_vibraciones = $mysqli->query($sql_mod_analisis_vibraciones) or die(mysqli_error($mysqli));
    
                if(mysqli_error($mysqli)==null){
                    echo '<script>alert("Se actualizo correctamente...");</script>';
                }
            }  
    }
    
}else{
    if(!empty($_POST['alta'])) echo '<script>alert("Datos incompletos, intente nuevamente...");</script>';
}

if(!empty($_GET['id_analisis'])){
                
    $sql_analisis_vibraciones = "select id_analisis, equipos.id_equipo, lado, mm_s, ge, temperatura, fecha,equipos.codigoequipo, equipos.descripcion, equipos.criticidad
    FROM analisis_vibraciones inner join equipos on analisis_vibraciones.id_equipo = equipos.id_equipo
                        where id_analisis=".$_GET['id_analisis']." ORDER BY fecha DESC;";
    //echo $sql_analisis_vibraciones;
    $resultado_analisis_vibraciones = $mysqli->query($sql_analisis_vibraciones) or die(mysqli_error($mysqli));   
    $row_resultado_analisis_vibraciones = $resultado_analisis_vibraciones->fetch_assoc(); 
}
//----------------------------------------------------------------------------------------------------------//

//------------BAJA-----------------------//
if(!empty($_GET['accion']) and $_GET['accion']=='baja' and !empty($_GET['id_analisis'])){
    
    $sql_inactanalisis_vibraciones = " DELETE FROM analisis_vibraciones WHERE id_analisis =".$_GET['id_analisis'].";";
    //echo $sql_inactanalisis_vibraciones;
    $resultado_inactanalisis_vibraciones = $mysqli->query($sql_inactanalisis_vibraciones) or die(mysqli_error($mysqli));


}
//---------------------------------------//

//---------------LISTADO---------------------//
if(empty($filtro)){ 
    $filtro='Where 1=1 ';
}
if(!empty($_GET['fecha'])){
    $fecha=FechaAmd($_GET['fecha']);
    $filtro= $filtro." and fecha LIKE '%".$fecha."%' ";        
}
if(!empty($_POST['nombre'])){
    $nombre=$_POST['nombre'];

    $filtro= $filtro." and codigoequipo LIKE '%".$nombre."%' ";        
    //DIVIDO LAS PALABRAS EN UN VECTOR PARA MEJOR FILTRADO DEL LIKE
    $busqueda_separada_x_espacios = preg_split("/[\s,]+/", $nombre);
   
    if(empty($busqueda_separada_x_espacios[1])){
        $filtro= $filtro." OR descripcion LIKE '%".$nombre."%' ";        
    }else{
        
            $filtro= $filtro." OR descripcion LIKE '%".$busqueda_separada_x_espacios[0]."%".$busqueda_separada_x_espacios[1]."%' OR descripcion LIKE '%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[0]."%'";            
    }    
    
    if(!empty($busqueda_separada_x_espacios[2]) and !empty($busqueda_separada_x_espacios[1])){
        $filtro= $filtro." OR descripcion LIKE '%".$busqueda_separada_x_espacios[0]."%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[2]."%' OR descripcion LIKE '%".$busqueda_separada_x_espacios[2]."%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[0]."%'";            
    }
}else{
    $busqueda_separada_x_espacios='';
    
}




$sql_analisis_vibraciones =" select id_analisis, equipos.id_equipo, lado, mm_s, ge, temperatura, fecha,equipos.codigoequipo, equipos.descripcion 'equipo', equipos.criticidad
FROM analisis_vibraciones inner join equipos on analisis_vibraciones.id_equipo = equipos.id_equipo
".$filtro." group by id_analisis order by fecha DESC;";
//echo $sql_analisis_vibraciones;
$resultado_analisis_vibraciones = $mysqli->query($sql_analisis_vibraciones) or die($mysqli->error);

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

    $sql_vibraciones_equipo =" select id_analisis, equipos.id_equipo, lado, mm_s, ge, temperatura, fecha,equipos.codigoequipo, equipos.descripcion 'equipo', equipos.criticidad
    FROM analisis_vibraciones inner join equipos on analisis_vibraciones.id_equipo = equipos.id_equipo
    Where equipos.id_equipo=".$_GET['id_equipo']." order by fecha desc;";
    //echo $sql_analisis_vibraciones;
    $resultado_vibraciones_equipo = $mysqli->query($sql_vibraciones_equipo) or die($mysqli->error);
    $row_resultado_vibraciones_equipo = $resultado_vibraciones_equipo->fetch_assoc();
}

//----------------------------------------------------------------------------------------------------/
function asignar_color_vibracion($parametro,$valor){
    if($parametro=='ge' and !empty($valor)){
        if($valor<1){
            $class_vibracion='class="table-success"';
        }else{
            if($valor>=1 and $valor<3){
                $class_vibracion='class="table-warning"';
            }else{
                if($valor>=3){
                    $class_vibracion='class="table-danger"';
                }
            }
        }
    }else{
        if($parametro=='mm_s' and !empty($valor)){
            if($valor<1){
                $class_vibracion='class="table-success"';
            }else{
                if($valor>=1 and $valor<3){
                    
                    $class_vibracion='class="table-warning"';
                    //echo '<script>alert("'.$class_vibracion.'");</script>';
                }else{
                    if($valor>=3){
                        $class_vibracion='class="table-danger"';
                    }
                }
            }
        }
    }
    if(empty($class_vibracion)){
        $class_vibracion='';
    }
    return $class_vibracion;
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