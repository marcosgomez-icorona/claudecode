<?php

include_once 'conexiones/conexion.php';
$mysqli=conexion_db();
include_once 'funciones/funciones.php';

//-----------------------//
    if(!empty($_POST['codigoequipo'])){ $codigoequipo=$_POST['codigoequipo'];}else{$codigoequipo='';}    
    if(!empty($_POST['descripcion'])){ $descripcion=$_POST['descripcion'];}else{$descripcion=' ';}
    if(!empty($_POST['descripcionampliada'])){ $descripcionampliada=$_POST['descripcionampliada'];}else{$descripcionampliada=' ';}
    if(!empty($_POST['idtipoequipo'])){ $idtipoequipo=$_POST['idtipoequipo'];}else{$idtipoequipo='NULL';}
    if(!empty($_POST['equiporef'])){ $equiporef=$_POST['equiporef'];}else{$equiporef=' ';}
    if(!empty($_POST['periodoplan'])){ $periodoplan=$_POST['periodoplan'];}else{$periodoplan='NULL';}
    if(!empty($_POST['fechainicioplan'])){ $fechainicioplan=FechaAmd($_POST['fechainicioplan']);}else{$fechainicioplan=' ';}
    if(!empty($_POST['criticidad'])){ $criticidad=$_POST['criticidad'];}else{$criticidad=' ';}

    if(!empty($_POST['accion']) and $_POST['accion']=='agregar_equipo' and !empty($_POST['codigoequipo'])){    
        agregar_equipo($_GET['id_equipo'],$descripcion,$descripcionampliada,$idtipoequipo,$equiporef,$periodoplan,$fechainicioplan,$criticidad);
    }

    if(!empty($_POST['accion']) and $_POST['accion']=='modificar_equipo' and !empty($_GET['id_equipo'])){    
        modificar_equipo($_GET['id_equipo'],$descripcion,$descripcionampliada,$idtipoequipo,$equiporef,$periodoplan,$fechainicioplan,$criticidad);
    }


//-------------------------//
   

$filtro='';

    //SETEO DE VARIABLEAS
    if(!empty($_GET['id_equipo'])){
        $id_equipo=$_GET['id_equipo'];
    }else{
        $id_equipo='-1';
    }

    if(!empty($_POST['id_equipo'])){
        $id_equipo=$_POST['id_equipo'];
    }else{
        $id_equipo='';
    }
    
    if(!empty($_POST['lado'])){
        $lado=$_POST['lado'];
    }else{
        $lado='NULL';
    }
    if(!empty($_POST['mm_s'])){
        $mm_s=$_POST['mm_s'];
    }else{
        $mm_s='NULL';
    }
    if(!empty($_POST['ge'])){
        $ge=$_POST['ge'];
    }else{
        $ge='NULL';
    }
    if(!empty($_POST['temperatura'])){
        $temperatura=$_POST['temperatura'];
    }else{
        $temperatura='NULL';
    }

    if(empty($class_vibracion)){
        $class_vibracion='';
    }
    if(empty($class_vibracion)){
        $class_vibracion='';
    }
    //--------------------------------------------------//
//------------ALTA Y MODIFICACION-----------------------//
/*
if(!empty($_POST) and !empty($_POST['codigoequipo']) and !empty($_POST['descripcion'])){
       
    if($_POST['accion']=='alta'){
       
            $sql_alta_equipos = "   INSERT INTO equipos (codigoequipo,codigonivel,descripcion,descripcionampliada,idtipoequipo,equiporef,criticidad)
            VALUES (".$id_equipo.",'".$lado."',".$mm_s.",".$ge.",".$temperatura.");";
            //echo $sql_alta_equipos;
            $resultado_alta_equipos = $mysqli->query($sql_alta_equipos) or die(mysqli_error($mysqli));

            if(mysqli_error($mysqli)==null){
            echo '<script>alert("Se cargo correctamente...");</script>';
            }
        }
        
    }else{
            if($_POST['accion']=='modificar' and !empty($_GET['id_equipo'])){
                $sql_mod_equipos = "   update equipos SET id_equipo = '".$id_equipo."' ,                                                                                       
                                                    lado = ".$lado." ,mm_s = ".$mm_s.",ge = ".$ge." ,temperatura = ".$temperatura."  
                                                    WHERE id_equipo =".$_GET['id_equipo'].";";
                //echo $sql_mod_equipos;
                $resultado_mod_equipos = $mysqli->query($sql_mod_equipos) or die(mysqli_error($mysqli));
    
                if(mysqli_error($mysqli)==null){
                    echo '<script>alert("Se actualizo correctamente...");</script>';
                }
            }  
    }

    if($_POST['accion']=='alta_vibracion'){
        $sql_alta_analisis_vibraciones = "   insert into analisis_vibraciones (id_equipo  ,lado  ,mm_s  ,ge  ,temperatura )
                                    VALUES (".$id_equipo.",'".$lado."',".$mm_s.",".$ge.",".$temperatura.");";
        //echo $sql_alta_analisis_vibraciones;
        $resultado_alta_analisis_vibraciones = $mysqli->query($sql_alta_analisis_vibraciones) or die(mysqli_error($mysqli));

        if(mysqli_error($mysqli)==null){
            echo '<script>alert("Se cargo el registro de Vibracion correctamente...");</script>';
            $_POST = array();
        }
    }
    //unset($_POST['lado']);
}else{
    if(!empty($_POST['alta'])) echo '<script>alert("Datos incompletos, intente nuevamente...");</script>';
}
*/




//VER ESTADO DE EQUIPO
if(!empty($_GET['id_equipo'])){
                
    $sql_equipo = "    select id_equipo, codigoequipo,equiporef, codigonivel, equipos.descripcion , descripcionampliada, tipoequipos.idtipoequipo, centrocosto_id, periodoplan, fechainicioplan, criticidad,
                        tipoequipos.codigo, tipoequipos.descripcion 'tipo'
                        FROM equipos left join tipoequipos on equipos.idtipoequipo = tipoequipos.idtipoequipo
                        where equipos.id_equipo=".$_GET['id_equipo'].";";
    //echo $sql_equipo;
    $resultado_equipo = $mysqli->query($sql_equipo) or die(mysqli_error($mysqli));   
    $row_resultado_equipo = $resultado_equipo->fetch_assoc(); 

    if(!empty($row_resultado_equipo['codigoequipo'])){
        $codigoequipo=$row_resultado_equipo['codigoequipo'];

        $sql_equipos_asociados = "  select id_asociado, codigo_motor, codigo_bomba, codigo_sist_electrico, codigo_reductor1, codigo_reductor2, codigo_reductor3, codigo_turbina, codigo_agitador, codigo_motor2,
                                    codigo_motor3, codigo_ventilador, codigo_generador, codigo_excitatriz_ppal, codigo_excitatriz_sec
                                    FROM equipos_asociados
                                    where codigo_motor = '".$codigoequipo."' OR codigo_bomba = '".$codigoequipo."' OR codigo_sist_electrico = '".$codigoequipo."' OR 
                                    codigo_reductor1 = '".$codigoequipo."' OR codigo_reductor2 = '".$codigoequipo."' OR codigo_reductor3 = '".$codigoequipo."' OR codigo_turbina = '".$codigoequipo."' OR codigo_agitador = '".$codigoequipo."' OR
                                    codigo_motor2 = '".$codigoequipo."' OR codigo_motor3 = '".$codigoequipo."' OR codigo_ventilador = '".$codigoequipo."' OR codigo_generador = '".$codigoequipo."' OR codigo_excitatriz_ppal = '".$codigoequipo."' OR
                                    codigo_excitatriz_sec = '".$codigoequipo."';";
        //echo $sql_equipos_asociados;
        $resultado_equipos_asociados = $mysqli->query($sql_equipos_asociados) or die(mysqli_error($mysqli));   
        $row_resultado_equipos_asociados = $resultado_equipos_asociados->fetch_assoc();
        if(!empty($row_resultado_equipos_asociados)){

            $codigo_motor=$row_resultado_equipos_asociados['codigo_motor'];
            $sql_codigo_equipos = "  select id_equipo, descripcion
                                        FROM equipos where codigoequipo = '".$codigo_motor."'  ";
            $resultado_codigo_equipos = $mysqli->query($sql_codigo_equipos) or die(mysqli_error($mysqli));   
            $row_resultado_codigo_equipos = $resultado_codigo_equipos->fetch_assoc();
            $id_equipo_motor=$row_resultado_codigo_equipos['id_equipo'];
            $nombre_equipo_motor=$row_resultado_codigo_equipos['descripcion'];

            //
            $codigo_bomba=$row_resultado_equipos_asociados['codigo_bomba'];
            $sql_codigo_equipos = "  select id_equipo, descripcion
                                        FROM equipos where codigoequipo = '".$codigo_bomba."'  ";
            $resultado_codigo_equipos = $mysqli->query($sql_codigo_equipos) or die(mysqli_error($mysqli));   
            $row_resultado_codigo_equipos = $resultado_codigo_equipos->fetch_assoc();
            $id_equipo_bomba=$row_resultado_codigo_equipos['id_equipo'];
            $nombre_equipo_bomba=$row_resultado_codigo_equipos['descripcion'];

            //
            $codigo_sist_electrico=$row_resultado_equipos_asociados['codigo_sist_electrico'];
            $sql_codigo_equipos = "  select id_equipo, descripcion
                                        FROM equipos where codigoequipo = '".$codigo_sist_electrico."'  ";
            $resultado_codigo_equipos = $mysqli->query($sql_codigo_equipos) or die(mysqli_error($mysqli));   
            $row_resultado_codigo_equipos = $resultado_codigo_equipos->fetch_assoc();
            $id_equipo_sist_electrico=$row_resultado_codigo_equipos['id_equipo'];
            $nombre_equipo_sist_electrico=$row_resultado_codigo_equipos['descripcion'];

            //
            $codigo_reductor1=$row_resultado_equipos_asociados['codigo_reductor1'];
            $sql_codigo_equipos = "  select id_equipo, descripcion
                                        FROM equipos where codigoequipo = '".$codigo_reductor1."'  ";
            $resultado_codigo_equipos = $mysqli->query($sql_codigo_equipos) or die(mysqli_error($mysqli));   
            $row_resultado_codigo_equipos = $resultado_codigo_equipos->fetch_assoc();
            $id_equipo_reductor1=$row_resultado_codigo_equipos['id_equipo'];
            $nombre_equipo_reductor1=$row_resultado_codigo_equipos['descripcion'];

            //
            $codigo_reductor2=$row_resultado_equipos_asociados['codigo_reductor2'];
            $sql_codigo_equipos = "  select id_equipo, descripcion
                                        FROM equipos where codigoequipo = '".$codigo_reductor2."'  ";
            $resultado_codigo_equipos = $mysqli->query($sql_codigo_equipos) or die(mysqli_error($mysqli));   
            $row_resultado_codigo_equipos = $resultado_codigo_equipos->fetch_assoc();
            $id_equipo_reductor2=$row_resultado_codigo_equipos['id_equipo'];
            $nombre_equipo_reductor2=$row_resultado_codigo_equipos['descripcion'];

            //

            //
            $codigo_reductor3=$row_resultado_equipos_asociados['codigo_reductor3'];
            $sql_codigo_equipos = "  select id_equipo, descripcion
                                        FROM equipos where codigoequipo = '".$codigo_reductor3."'  ";
            $resultado_codigo_equipos = $mysqli->query($sql_codigo_equipos) or die(mysqli_error($mysqli));   
            $row_resultado_codigo_equipos = $resultado_codigo_equipos->fetch_assoc();
            $id_equipo_reductor3=$row_resultado_codigo_equipos['id_equipo'];
            $nombre_equipo_reductor3=$row_resultado_codigo_equipos['descripcion'];

            //

            $codigo_turbina=$row_resultado_equipos_asociados['codigo_turbina'];
            $sql_codigo_equipos = "  select id_equipo, descripcion
                                        FROM equipos where codigoequipo = '".$codigo_turbina."'  ";
            $resultado_codigo_equipos = $mysqli->query($sql_codigo_equipos) or die(mysqli_error($mysqli));   
            $row_resultado_codigo_equipos = $resultado_codigo_equipos->fetch_assoc();
            $id_equipo_turbina=$row_resultado_codigo_equipos['id_equipo'];
            $nombre_equipo_turbina=$row_resultado_codigo_equipos['descripcion'];

            //

            //
            $codigo_agitador=$row_resultado_equipos_asociados['codigo_agitador'];
            $sql_codigo_equipos = "  select id_equipo, descripcion
                                        FROM equipos where codigoequipo = '".$codigo_agitador."'  ";
            $resultado_codigo_equipos = $mysqli->query($sql_codigo_equipos) or die(mysqli_error($mysqli));   
            $row_resultado_codigo_equipos = $resultado_codigo_equipos->fetch_assoc();
            $id_equipo_agitador=$row_resultado_codigo_equipos['id_equipo'];
            $nombre_equipo_agitador=$row_resultado_codigo_equipos['descripcion'];

            //

            //
            $codigo_excitatriz_ppal=$row_resultado_equipos_asociados['codigo_excitatriz_ppal'];
            $sql_codigo_equipos = "  select id_equipo, descripcion
                                        FROM equipos where codigoequipo = '".$codigo_excitatriz_ppal."'  ";
            $resultado_codigo_equipos = $mysqli->query($sql_codigo_equipos) or die(mysqli_error($mysqli));   
            $row_resultado_codigo_equipos = $resultado_codigo_equipos->fetch_assoc();
            $id_equipo_excitatriz_ppal=$row_resultado_codigo_equipos['id_equipo'];
            $nombre_equipo_excitatriz_ppal=$row_resultado_codigo_equipos['descripcion'];

            //

            //
            $codigo_excitatriz_sec=$row_resultado_equipos_asociados['codigo_excitatriz_sec'];
            $sql_codigo_equipos = "  select id_equipo, descripcion
                                        FROM equipos where codigoequipo = '".$codigo_excitatriz_sec."'  ";
            $resultado_codigo_equipos = $mysqli->query($sql_codigo_equipos) or die(mysqli_error($mysqli));   
            $row_resultado_codigo_equipos = $resultado_codigo_equipos->fetch_assoc();
            $id_equipo_excitatriz_sec=$row_resultado_codigo_equipos['id_equipo'];
            $nombre_equipo_excitatriz_sec=$row_resultado_codigo_equipos['descripcion'];

            //

            //
            $codigo_generador=$row_resultado_equipos_asociados['codigo_generador'];
            $sql_codigo_equipos = "  select id_equipo, descripcion
                                        FROM equipos where codigoequipo = '".$codigo_generador."'  ";
            $resultado_codigo_equipos = $mysqli->query($sql_codigo_equipos) or die(mysqli_error($mysqli));   
            $row_resultado_codigo_equipos = $resultado_codigo_equipos->fetch_assoc();
            $id_equipo_generador=$row_resultado_codigo_equipos['id_equipo'];
            $nombre_equipo_generador=$row_resultado_codigo_equipos['descripcion'];

            //

            //
            $codigo_motor2=$row_resultado_equipos_asociados['codigo_motor2'];
            $sql_codigo_equipos = "  select id_equipo, descripcion
                                        FROM equipos where codigoequipo = '".$codigo_motor2."'  ";
            $resultado_codigo_equipos = $mysqli->query($sql_codigo_equipos) or die(mysqli_error($mysqli));   
            $row_resultado_codigo_equipos = $resultado_codigo_equipos->fetch_assoc();
            $id_equipo_motor2=$row_resultado_codigo_equipos['id_equipo'];
            $nombre_equipo_motor2=$row_resultado_codigo_equipos['descripcion'];

            //

            //
            $codigo_motor3=$row_resultado_equipos_asociados['codigo_motor3'];
            $sql_codigo_equipos = "  select id_equipo, descripcion
                                        FROM equipos where codigoequipo = '".$codigo_motor3."'  ";
            $resultado_codigo_equipos = $mysqli->query($sql_codigo_equipos) or die(mysqli_error($mysqli));   
            $row_resultado_codigo_equipos = $resultado_codigo_equipos->fetch_assoc();
            $id_equipo_motor3=$row_resultado_codigo_equipos['id_equipo'];
            $nombre_equipo_motor3=$row_resultado_codigo_equipos['descripcion'];

            //

            //
            $codigo_ventilador=$row_resultado_equipos_asociados['codigo_ventilador'];
            $sql_codigo_equipos = "  select id_equipo, descripcion
                                        FROM equipos where codigoequipo = '".$codigo_ventilador."'  ";
            $resultado_codigo_equipos = $mysqli->query($sql_codigo_equipos) or die(mysqli_error($mysqli));   
            $row_resultado_codigo_equipos = $resultado_codigo_equipos->fetch_assoc();
            $id_equipo_ventilador=$row_resultado_codigo_equipos['id_equipo'];
            $nombre_equipo_ventilador=$row_resultado_codigo_equipos['descripcion'];

            //

        }
    }else{
        $codigoequipo=' ';
    }

    $sql_equipo_imagen = "  select id_adjunto, idequipo, archivo, defecto
                            FROM adjuntos
                            where idequipo=".$_GET['id_equipo'].";";                            
                            //echo $sql_equipo_imagen;
    $resultado_equipo_imagen = $mysqli->query($sql_equipo_imagen) or die($mysqli->error);
    $row_resultado_equipo_imagen = $resultado_equipo_imagen->fetch_assoc();

    $sql_equipo_ficha_tecnica = "   select id_ficha_tecnica, idequipo, tipoequipo, patron, referencia_tecnica,valor
                                    FROM fichatecnica where idequipo=".$_GET['id_equipo'].";";                            
                            //echo $sql_equipo_ficha_tecnica;
    $resultado_equipo_ficha_tecnica = $mysqli->query($sql_equipo_ficha_tecnica) or die($mysqli->error);
    $row_resultado_equipo_ficha_tecnica = $resultado_equipo_ficha_tecnica->fetch_assoc();

    $sql_alertas = " select id_alerta, estado, falla_termica, corriente, temperatura, fecha
                    FROM alertas where id_equipo=".$_GET['id_equipo'].";";
    //echo $sql_alertas;
    $resultado_alertas = $mysqli->query($sql_alertas) or die(mysqli_error($mysqli));   
    $row_resultado_alertas = $resultado_alertas->fetch_assoc();
    
    
}



//------------BAJA-----------------------//
if(!empty($_GET['accion']) and $_GET['accion']=='baja' and !empty($_GET['id_equipo'])){
    
    $sql_inactequipos = " DELETE FROM equipos WHERE id_equipo =".$_GET['id_equipo'].";";
    //echo $sql_inactequipos;
    $resultado_inactequipos = $mysqli->query($sql_inactequipos) or die(mysqli_error($mysqli));


}
//---------------------------------------//






if(!empty($_GET['nro_ot'])){

    //-------------------------------------ORDEN DE TRABAJO---------------------------------------------------------------------//
    
    $sql_ot = " select numero, tipoorden, ordenes_trabajo.codigoequipo , estado, ordenes_trabajo.criticidad, id_orden_trabajo, numero, tipoorden,
                estado, ordenes_trabajo.criticidad , codigoequiporef,
                ordenes_trabajo.tarea, fechainicio, fechafin, prioridad, originador, ordenes_trabajo.ejecutante, centrodecosto, periodo, planprogramado, observaciones, ordenes_trabajo.paradamolienda,
                equipos.codigoequipo, equipos.codigonivel, equipos.descripcion,usuario_verificacion,usuario_aprobacion
                FROM ordenes_trabajo 
                inner join equipos on ordenes_trabajo.codigoequipo = equipos.id_equipo
                where ordenes_trabajo.numero='".$_GET['nro_ot']."' 
                ORDER BY ordenes_trabajo.fechainicio DESC;";
                //echo $sql_ot;
                $resultado_ot = $mysqli->query($sql_ot) or die(mysqli_error($mysqli));   
                $row_resultado_ot = $resultado_ot->fetch_assoc();

    //-------------------------------ORDENES TRABAJO TAREAS----------------------------------------------------------------//
    
    $sql_ot_tareas = "  SELECT `ordenes_trabajo`.id_orden_trabajo, `ordenes_trabajo`.numero, `ordenes_trabajo`.tipoorden, 
    `ordenes_trabajo`.codigoequipo, `ordenes_trabajo`.estado, `ordenes_trabajo`.criticidad,
    `ordenes_trabajo`.codigoequiporef, `ordenes_trabajo`.tarea, `ordenes_trabajo`.fechainicio, 
    `ordenes_trabajo`.fechafin, `ordenes_trabajo`.prioridad, `ordenes_trabajo`.originador, `ordenes_trabajo`.ejecutante,
    `ordenes_trabajo`.centrodecosto, `ordenes_trabajo`.periodo, `ordenes_trabajo`.planprogramado, 
    `ordenes_trabajo`.observaciones, `ordenes_trabajo`.paradamolienda,
     ordernestrabajo_tareas.paradamolienda, ordernestrabajo_tareas.tiempo, ordernestrabajo_tareas.cantoperarios,
     ordernestrabajo_tareas.operador, ordernestrabajo_tareas.ejecutante, ordernestrabajo_tareas.descripcion, 
     ordernestrabajo_tareas.tarea,bloqueo
    FROM ordenes_trabajo 
    left join ordernestrabajo_tareas on ordenes_trabajo.id_orden_trabajo = ordernestrabajo_tareas.idordendetrabajo
    where ordenes_trabajo.numero='".$_GET['nro_ot']."' ORDER BY fechainicio DESC;";
    //echo $sql_ot_tareas;
    $resultado_ot_tareas = $mysqli->query($sql_ot_tareas) or die(mysqli_error($mysqli));   
    $row_resultado_ot_tareas = $resultado_ot_tareas->fetch_assoc();

    //---------------------------------ORDENES TRABAJO INSUMOS--------------------------------------------------------------//

    $sql_ot_insumos = "  SELECT `ordenes_trabajo`.id_orden_trabajo, `ordenes_trabajo`.numero, `ordenes_trabajo`.tipoorden, 
    `ordenes_trabajo`.codigoequipo, `ordenes_trabajo`.estado, `ordenes_trabajo`.criticidad,
    `ordenes_trabajo`.codigoequiporef, `ordenes_trabajo`.tarea, `ordenes_trabajo`.fechainicio, 
    `ordenes_trabajo`.fechafin, `ordenes_trabajo`.prioridad, `ordenes_trabajo`.originador, `ordenes_trabajo`.ejecutante,
    `ordenes_trabajo`.centrodecosto, `ordenes_trabajo`.periodo, `ordenes_trabajo`.planprogramado, 
    `ordenes_trabajo`.observaciones, `ordenes_trabajo`.paradamolienda,
     ordenestrabajo_insumos.cantidad, ordenestrabajo_insumos.descripcion, ordenestrabajo_insumos.codigo
    FROM ordenes_trabajo 
    left join ordenestrabajo_insumos on ordenes_trabajo.id_orden_trabajo = ordenestrabajo_insumos.idordendetrabajo
    where ordenes_trabajo.numero='".$_GET['nro_ot']."' ORDER BY fechainicio DESC;";
    //echo $sql_mantenimiento;
    $resultado_ot_insumos = $mysqli->query($sql_ot_insumos) or die(mysqli_error($mysqli));   
    $row_resultado_ot_insumos = $resultado_ot_insumos->fetch_assoc();
}


//--------------------------------------------------------------------------------------------//

//---------------LISTADO---------------------//
if(empty($filtro)){ 
    $filtro='Where 1=1 ';
}
if(!empty($_GET['fecha'])){
    $fecha=FechaAmd($_GET['fecha']);
    $filtro= $filtro." and fecha LIKE '%".$fecha."%' ";        
}
if(!empty($_GET['nombre'])){
    $nombre=$_GET['nombre'];

    $filtro= $filtro." and codigoequipo LIKE '%".$nombre."%' ";        
    //DIVIDO LAS PALABRAS EN UN VECTOR PARA MEJOR FILTRADO DEL LIKE
    $busqueda_separada_x_espacios = preg_split("/[\s,]+/", $nombre);
   
    if(empty($busqueda_separada_x_espacios[1])){
        $filtro= $filtro." OR descripcion LIKE '%".$nombre."%' ";        
    }else{
        
            $filtro= $filtro." OR descripcion LIKE '%".$busqueda_separada_x_espacios[0]."%".$busqueda_separada_x_espacios[1]."%' OR descripcion LIKE '%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[0]."%'";            
    }

    if(!empty($busqueda_separada_x_espacios[2])){
        $filtro= $filtro." OR descripcion LIKE '%".$busqueda_separada_x_espacios[0]."%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[2]."%' OR descripcion LIKE '%".$busqueda_separada_x_espacios[2]."%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[0]."%'";            
    }

}else{
    $busqueda_separada_x_espacios='';
    
}

//-----equipos----------//
$sql_equipos = " select id_equipo, codigoequipo, codigonivel,equiporef, descripcion 'equipo', descripcionampliada, idtipoequipo, centrocosto_id, periodoplan, fechainicioplan, criticidad 
FROM equipos ".$filtro." ORDER BY descripcion ASC;";
//echo $sql_equipos;
$resultado_equipos = $mysqli->query($sql_equipos) or die($mysqli->error);

//--------analisis-----//
$sql_analisis =" select id_analisis, equipos.id_equipo, lado, mm_s, ge, temperatura, mu, fecha,equipos.codigoequipo, equipos.descripcion 'equipo', equipos.criticidad,
equipos.descripcionampliada
FROM analisis_vibraciones inner join equipos on analisis_vibraciones.id_equipo = equipos.id_equipo
".$filtro." group by id_analisis order by fecha DESC;";
//echo $sql_analisis;
$resultado_analisis = $mysqli->query($sql_analisis) or die($mysqli->error);

//---------------------------------------//



if(!empty($_GET['id_equipo'])){
    $sql_equipo_seleccionado = "select id_equipo, descripcion 'equipo'
                                FROM equipos where id_equipo=".$_GET['id_equipo'].";";
                                //echo $sql_equipo_seleccionado;
    $resultado_equipo_seleccionado = $mysqli->query($sql_equipo_seleccionado) or die($mysqli->error);
    $row_resultado_equipo_seleccionado = $resultado_equipo_seleccionado->fetch_assoc();

    $sql_vibraciones_equipo =" select id_analisis, equipos.id_equipo, lado, mm_s, ge, temperatura, mu, fecha,equipos.codigoequipo, equipos.descripcion 'equipo', equipos.criticidad
    FROM analisis_vibraciones inner join equipos on analisis_vibraciones.id_equipo = equipos.id_equipo
    Where equipos.id_equipo=".$_GET['id_equipo']." order by fecha desc;";
    //echo $sql_equipos;
    $resultado_vibraciones_equipo = $mysqli->query($sql_vibraciones_equipo) or die($mysqli->error);
    $row_resultado_vibraciones_equipo = $resultado_vibraciones_equipo->fetch_assoc();
}
//$_POST = [];

//-------------------------------------FUNCIONES---------------------------------------------------------------//



function ver_datos_equipo($id_equipo){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();

    $sql_datos_equipo = "   SELECT id_equipo, codigoequipo, codigonivel, equipos.descripcion, descripcionampliada, equipos.idtipoequipo,tipoequipos.descripcion 'tipoequipo', equiporef, centrocosto_id,
                            periodoplan, fechainicioplan, criticidad  
                            FROM equipos LEFT JOIN tipoequipos ON equipos.idtipoequipo = tipoequipos.idtipoequipo WHERE id_equipo=".$id_equipo.";";    
    $resultado_datos_equipo = $mysqli->query($sql_datos_equipo) or die(mysqli_error($mysqli));   
    $row_datos_equipo  = $resultado_datos_equipo->fetch_assoc();

    return $row_datos_equipo; 
}

function agregar_equipo($codigoequipo,$descripcion,$descripcionampliada,$idtipoequipo,$equiporef,$periodoplan,$fechainicioplan,$criticidad){
    include_once 'conexiones/conexion.php';    
    $mysqli=conexion_db();    
    
        $sql_agregar_equipo = " INSERT INTO equipos (codigoequipo,codigonivel,descripcion,descripcionampliada,idtipoequipo,equiporef,criticidad) 
                                VALUE ($codigoequipo,$codigonivel,$descripcion,$descripcionampliada,$idtipoequipo,$equiporef,$periodoplan,$fechainicioplan,$criticidad); ";       
        $mysqli->query($sql_agregar_equipo) or die(mysqli_error($mysqli)); 

        //echo $sql_agregar_equipo;
        if(mysqli_error($mysqli)==null){
           echo '<script>alert("Se Actualizo correctamente...");</script>';
        }      
         
}

function modificar_equipo($id_equipo,$descripcion,$descripcionampliada,$idtipoequipo,$equiporef,$periodoplan,$fechainicioplan,$criticidad){
    include_once 'conexiones/conexion.php';    
    $mysqli=conexion_db();    
    
    
        $sql_modificar_equipo = "  update equipos SET descripcion = '".$descripcion."' ,descripcionampliada = '".$descripcionampliada."',
                                        idtipoequipo = $idtipoequipo ,equiporef = '".$equiporef."',periodoplan = $periodoplan, fechainicioplan = '".$fechainicioplan."',
                                        criticidad = '".$criticidad."' WHERE id_equipo = $id_equipo ; ";       
        $mysqli->query($sql_modificar_equipo) or die(mysqli_error($mysqli)); 

        //echo $sql_modificar_equipo;
        if(mysqli_error($mysqli)==null){
           echo '<script>alert("Se Actualizo correctamente...");</script>';
        }      
         
}

function nuevo_numero_ot(){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();

    $sql_ot_insumos = " select MAX(numero)+1 'nuevo_numero' FROM ordenes_trabajo;";    
    $resultado_ot_insumos = $mysqli->query($sql_ot_insumos) or die(mysqli_error($mysqli));   
    $row_nuevo_numero = $resultado_ot_insumos->fetch_assoc();
    $nuevo_numero_ot= $row_nuevo_numero['nuevo_numero'];
    return $nuevo_numero_ot; 
}

function ver_tipoequipos(){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();

    $sql_tipoequipos = "SELECT idtipoequipo, codigo, descripcion FROM tipoequipos ORDER BY descripcion ASC;";    
    $resultado_tipoequipos = $mysqli->query($sql_tipoequipos) or die(mysqli_error($mysqli));   
    

    return $resultado_tipoequipos; 
}

function listado_equipos($buscar){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();

    if(!empty($buscar)){            
            
        //DIVIDO LAS PALABRAS EN UN VECTOR PARA MEJOR FILTRADO DEL LIKE
        $busqueda_separada_x_espacios = preg_split("/[\s,]+/", $buscar);
       
        if(empty($busqueda_separada_x_espacios[1])){
            $where= "WHERE descripcion LIKE '%".$buscar."%' OR descripcionampliada LIKE '%".$buscar."%' OR codigoequipo LIKE '%".$buscar."%'";        
        }else{
            
                $where= "WHERE descripcion LIKE '%".$busqueda_separada_x_espacios[0]."%".$busqueda_separada_x_espacios[1]."%' OR descripcion LIKE '%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[0]."%' OR descripcionampliada LIKE '%".$busqueda_separada_x_espacios[0]."%".$busqueda_separada_x_espacios[1]."%' OR descripcionampliada LIKE '%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[0]."%'";            
        }
    
        if(!empty($busqueda_separada_x_espacios[2])){
            $where= "WHERE descripcion LIKE '%".$busqueda_separada_x_espacios[0]."%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[2]."%' OR descripcion LIKE '%".$busqueda_separada_x_espacios[2]."%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[0]."%' OR descripcionampliada LIKE '%".$busqueda_separada_x_espacios[0]."%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[2]."%' OR descripcionampliada LIKE '%".$busqueda_separada_x_espacios[2]."%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[0]."%'";            
        }
    
    }else{
        $where='Where 1=0 ';
    }

    $sql_equipo = " SELECT id_equipo, codigoequipo, codigonivel, descripcion, descripcionampliada, idtipoequipo, equiporef, centrocosto_id, periodoplan, fechainicioplan, criticidad
                    FROM equipos  $where ORDER BY codigoequipo ASC; ";  
                    //echo $sql_equipo;  
    $resultado_equipo = $mysqli->query($sql_equipo) or die(mysqli_error($mysqli));   
    
    return $resultado_equipo; 
    
}

function obtiene_codigoref($id_equipo){
    $sql_obtiene_codigoref='';
    $resultado_obtiene_codigoref=[];
    if(!empty($id_equipo)){
        $id_equipo=$id_equipo;
        include_once 'conexiones/conexion.php';
        $mysqli=conexion_db();

        $sql_obtiene_codigoref = "SELECT eq.id_equipo, eq.codigoequipo, eq.codigonivel,
                            CASE
                            WHEN LOCATE('00.00.00', codigonivel) > 0 THEN
                                (SELECT eqref.id_equipo from equipos eqref where eqref.codigonivel=CONCAT(SUBSTRING(eq.codigonivel, 1, 3), '00.00.00.00'))
                            ELSE       
                                CASE
                                WHEN LOCATE('00.00', codigonivel) > 0 THEN
                                    (SELECT eqref.id_equipo from equipos eqref where eqref.codigonivel=CONCAT(SUBSTRING(eq.codigonivel, 1, 6), '00.00.00'))          
                                ELSE       
                                    CASE
                                    WHEN LOCATE('00', codigonivel) > 0 THEN
                                        (SELECT eqref.id_equipo from equipos eqref where eqref.codigonivel=CONCAT(SUBSTRING(eq.codigonivel, 1, 9), '00.00'))          
                                        
                                    ELSE       
                                        (SELECT eqref.id_equipo from equipos eqref where eqref.codigonivel=CONCAT(SUBSTRING(eq.codigonivel, 1, 12), '00'))                        
                                    END
                                END
                            END AS codigoref
                        FROM equipos eq
                        WHERE eq.id_equipo = ".$id_equipo.";";    
                        
        $resultado_obtiene_codigoref = $mysqli->query($sql_obtiene_codigoref) or die(mysqli_error($mysqli));   
        $row_codigoequiporef = $resultado_obtiene_codigoref->fetch_assoc();
        if(mysqli_error($mysqli)==null){
            $codigoequiporef= $row_codigoequiporef['codigoref'];
        }else{
            $codigoequiporef='';
        }

        
    }else{
        $codigoequiporef='';
    }
    return $codigoequiporef; 
}

function actualizar_equiporef($id_equipo){
    include_once 'conexiones/conexion.php';    
    $mysqli=conexion_db();    
    
    $equiporef=obtiene_codigoref($id_equipo);

    $nombreequiporef=ver_datos_equipo($equiporef);

    $sql_actualizar_equiporef = "  update equipos SET equiporef = '".$nombreequiporef['descripcion']."' WHERE id_equipo = $id_equipo ; ";  
       
    $mysqli->query($sql_actualizar_equiporef) or die(mysqli_error($mysqli));         
    echo "<script>close();</script>";     
}


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

function asignar_color_orden_trabajo($estado){

    if(!empty($estado)){
        switch ($estado) {
            case 'Aprobada':
                $class_ot='class="table-success"';
                break;
            case 'Generada':
                $class_ot='class="table-default"';
                break;
            case 'Programada':
                $class_ot='class="table-primary"';
                break;
            case 'Impresa':
                $class_ot='class="table-secundary"';
                break;
            case 'Finalizada':
                $class_ot='bgcolor="#D79EF1"';
                break;
            case 'Cancelada':
                $class_ot='class="table-danger"';
                break;
        }
    }

    if(empty($class_ot)){
        $class_ot='';
    }
    return $class_ot;
}


//----------------------------------------------------------------------------------------------------//

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

    function ver_detalle_ot(nro_ot)
    {
        sel = window.open("report.php?accion=visualizar_detalle_ot&nro_ot="+nro_ot, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }
    function agregar_equipo()
    {
        sel = window.open("view.php?opcion=agregar_equipo", "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }

    function modificar_equipo(id_equipo)
    {
        sel = window.open("view.php?opcion=modificar_equipo&id_equipo="+id_equipo, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }
    function oculta_contenido(id){
        if (document.getElementById){ //se obtiene el id
            var el = document.getElementById(id); //se define la variable "el" igual a nuestro div
            el.style.display = (el.style.display == 'none') ? 'block' : 'none';
        }
    }

    function seleccionar_equipo(codigoequipo_ref,descripcion_equipo_ref) {
      
      // Asignar los valores a los campos
      document.getElementById("codigoequipo_ref").value = codigoequipo_ref;      
      document.getElementById("descripcion_equipo_ref").value = descripcion_equipo_ref;

      // Ocultar Listado de Productos
    /*
      var lista = document.getElementById("lista_equipos");
        if (lista.style.display === "none") {
            lista.style.display = "block";
        } else {
            lista.style.display = "none";
        }
    */
    }

    window.onload = function(){/*hace que se cargue la función lo que predetermina que div estará oculto hasta llamar a la función nuevamente*/
        //oculta_contenido('prie_preview');/* "contenido_a_mostrar" es el nombre que le dimos al DIV */
    }

</script>