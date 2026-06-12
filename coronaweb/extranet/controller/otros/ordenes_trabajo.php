<?php

    include_once 'conexiones/conexion.php';
    include_once 'funciones/funciones.php';

    $filtro='';

    $mysqli=conexion_db();
    //PARAMETROS DE BUSQUEDA
    

    if(!empty($_POST['numero'])){
        $numero=$_POST['numero'];
        $filtro= "Where numero='".$numero."' ";
    }else{
        $numero='';
    }

    if(!empty($_POST['tipoorden'])){
        $tipoorden=$_POST['tipoorden'];
        if($filtro==""){
            $filtro= "Where tipoorden='".$tipoorden."' ";
        }else{
            $filtro= $filtro."and tipoorden='".$tipoorden."' ";
        }
    }else{
        $tipoorden='';
    }
    
    if(!empty($_POST['estado'])){
        $estado=$_POST['estado'];
        if($filtro==""){
            $filtro= "Where estado='".$estado."' ";
        }else{
            $filtro= $filtro."and estado='".$estado."' ";
        }
    }else{
        $estado=NULL;
    }

    if(!empty($_POST['tarea'])){
        $tarea=$_POST['tarea'];        
    }else{
        $tarea='';
    }

    if(!empty($_POST['fechainicio']) and !empty($_POST['fechafin'])){
        $fechainicio=fechaAmd($_POST['fechainicio']);  
        $fechafin=fechaAmd($_POST['fechafin']); 
        if($filtro==""){
            $filtro= "Where fechainicio>='".$fechainicio."' AND fechafin<='".$fechafin."'";
        }else{
            $filtro= $filtro." and fechainicio>='".$fechainicio."' AND fechafin<='".$fechafin."'";
        }
    }else{
        if(!empty($_POST['fechainicio'])){
            $fechainicio=fechaAmd($_POST['fechainicio']);  
            if($filtro==""){
                $filtro= "Where fechainicio='".$fechainicio."' ";
            }else{
                $filtro= $filtro."and fechainicio='".$fechainicio."' ";
            }      
        }else{
            $fechainicio='';
        }
    
        if(!empty($_POST['fechafin'])){
            $fechafin=fechaAmd($_POST['fechafin']); 
            if($filtro==""){
                $filtro= "Where fechafin='".$fechafin."' ";
            }else{
                $filtro= $filtro."and fechafin='".$fechafin."' ";
            }         
        }else{
            $fechafin='';
        }
    
    }
    


    if(!empty($_POST['originador'])){
        $originador=$_POST['originador'];
        if($filtro==""){
            $filtro= "Where originador='".$originador."' ";
        }else{
            $filtro= $filtro."and originador='".$originador."' ";
        }
    }else{
        $originador=NULL;
    }
    if(!empty($_POST['ejecutante'])){        
        $ejecutante=$_POST['ejecutante'];
        if($filtro==""){
            $filtro= "Where ejecutante='".$ejecutante."' ";
        }else{
            $filtro= $filtro."and ejecutante='".$ejecutante."' ";
        }
    }else{
        $originador=NULL;
    }
    if(!empty($_POST['prioridad'])){
        $prioridad=$_POST['prioridad'];
        if($filtro==""){
            $filtro= "Where prioridad='".$prioridad."' ";
        }else{
            $filtro= $filtro."and prioridad='".$prioridad."' ";
        }
    }else{
        $prioridad=NULL;
    }
    if(!empty($_POST['periodo'])){
        $periodo=$_POST['periodo'];
        if($filtro==""){
            $filtro= "Where periodo='".$periodo."' ";
        }else{
            $filtro= $filtro."and periodo='".$periodo."' ";
        }
    }else{
        $periodo=NULL;
    }
    if(!empty($_POST['planprogramado'])){
        $planprogramado=$_POST['planprogramado'];        
    }else{
        $planprogramado=0;
    }
    
    if(!empty($_POST['observaciones'])){
        $observaciones=$_POST['observaciones'];        
    }else{
        $observaciones=' ';
    }
    if(!empty($_POST['parada_molienda'])){
        $parada_molienda=$_POST['parada_molienda'];
        if($filtro==""){
            $filtro= "Where parada_molienda='".$parada_molienda."' ";
        }else{
            $filtro= $filtro."and parada_molienda='".$parada_molienda."' ";
        }
    }else{
        $parada_molienda='';
    }
    if(!empty($_POST['avance'])){
        $avance=$_POST['avance'];        
    }else{
        $avance=0;
    }

    if(!empty($_GET['fecha'])){
        $fecha=FechaAmd($_GET['fecha']);
        if($filtro==""){
            $filtro= "Where fecha='%".$fecha."%' ";
        }else{
            $filtro= $filtro." and fecha='%".$fecha."%' ";        
        }
        
    }
        

    if(!empty($_POST['busqueda_generica'])){
        $busqueda_generica=$_POST['busqueda_generica'];
        if($filtro==""){
            $filtro= "Where numero LIKE '%".$busqueda_generica."%' OR equipos.codigoequipo LIKE '%".$busqueda_generica."%' OR equipos.descripcion LIKE '%".$busqueda_generica."%' OR tipoorden LIKE '%".$busqueda_generica."%' OR estado LIKE '%".$busqueda_generica."%' OR tarea LIKE '%".$busqueda_generica."%' OR prioridad LIKE '%".$busqueda_generica."%' OR originador LIKE '%".$busqueda_generica."%' OR ejecutante LIKE '%".$busqueda_generica."%' OR periodo LIKE '%".$busqueda_generica."%'";
        }else{
            $filtro= $filtro." and (numero LIKE '%".$busqueda_generica."%' OR equipos.codigoequipo LIKE '%".$busqueda_generica."%' OR equipos.descripcion LIKE '%".$busqueda_generica."%' OR tipoorden LIKE '%".$busqueda_generica."%' OR estado LIKE '%".$busqueda_generica."%' OR tarea LIKE '%".$busqueda_generica."%' OR prioridad LIKE '%".$busqueda_generica."%' OR originador LIKE '%".$busqueda_generica."%' OR ejecutante LIKE '%".$busqueda_generica."%' OR periodo LIKE '%".$busqueda_generica."%')";
        }
    }else{
        $busqueda_generica=NULL;
    }

    if(empty($filtro)){
        $filtro= "";
        $limit= "LIMIT 20 ";
    }else{$limit=" ";}
    
    //--------------------------------------------------//
    

    $sql_ordenes_trabajo = "  SELECT id_orden_trabajo,equipos.id_equipo,equipos.codigoequipo, equipos.descripcion 'equipo', numero, tipoorden, estado, ordenes_trabajo.criticidad, codigoequiporef, 
                            tarea, fechainicio, fechafin, prioridad,originador, ejecutante, centrodecosto, periodo, planprogramado, observaciones, paradamolienda,
                            TRUNCATE(TIMESTAMPDIFF(MINUTE,ordenes_trabajo.fechainicio, ordenes_trabajo.fechafin)/60,2) 'tiempo_hs',avance
                            FROM ordenes_trabajo 
                            INNER JOIN equipos ON ordenes_trabajo.codigoequipo = equipos.id_equipo
                            ".$filtro."
                            ORDER BY numero DESC ".$limit." ;";
    //echo $sql_ordenes_trabajo;
    $resultado_ordenes_trabajo = $mysqli->query($sql_ordenes_trabajo) or die(mysqli_error($mysqli));   
    
    //TIPO ORDEN
    $sql_tipoorden = "  select id_tipo_orden, descripcion FROM tipo_orden order by id_tipo_orden ASC;";
    //echo $sql_tipoorden;
    $resultado_tipoorden = $mysqli->query($sql_tipoorden) or die(mysqli_error($mysqli));
    
    //Ejecutante
    $sql_ejecutante = "SELECT id_entidad, nombre FROM entidades where tipo='Ejecutante' order by nombre asc;";
    //echo $sql_Ejecutante;
    $resultado_ejecutante = $mysqli->query($sql_ejecutante) or die(mysqli_error($mysqli));

      //Originador
    $sql_originador = "SELECT id_entidad, nombre FROM entidades where tipo='Originador' order by nombre asc;";
    //echo $sql_Originador;
    $resultado_originador = $mysqli->query($sql_originador) or die(mysqli_error($mysqli));

      //Estados OT 
      if(!empty($tipo_usuario) and $tipo_usuario=='SUPERVISOR'){
        $sql_estados = "SELECT id_estado, nombre FROM estados WHERE tipo='OT' ORDER BY nombre ASC;";      
      }else{
        $sql_estados = "SELECT id_estado, nombre FROM estados WHERE tipo='OT' and nombre<>'Verificada'  ORDER BY nombre ASC;";      
      } 
    //echo $tipo_usuario;
      $resultado_estados = $mysqli->query($sql_estados) or die(mysqli_error($mysqli));

    
// ordenes_trabajo
if(!empty($_POST['accion_ot']) and $_POST['accion_ot']=='alta_orden_trabajo'){
    
    agregar_ot( $tipoorden,$_GET['id_equipo'],$estado,'  ',$tarea,$fechainicio,$fechafin,$prioridad,$originador,$ejecutante,
                $periodo,$planprogramado,$observaciones,$parada_molienda,$avance,$usuario);
}

if(!empty($_GET['nro_ot'])){
    if(!empty($_POST['accion']) and $_POST['accion']=='modificar_ot'){
        modificar_ot(   $_GET['nro_ot'],$tipoorden,$estado,$tarea,$fechainicio,$fechafin,$prioridad,$originador,$ejecutante,$periodo,
                        $planprogramado,$observaciones,$parada_molienda,$avance,$usuario);
    }
}

if(!empty($_GET['numero'])){    
        
    //-------------------------------ORDENES TRABAJO TAREAS----------------------------------------------------------------//
    
    $sql_ot_tareas = "  SELECT `ordenes_trabajo`.id_orden_trabajo, `ordenes_trabajo`.numero, `ordenes_trabajo`.tipoorden, 
    `ordenes_trabajo`.codigoordenes_trabajo, `ordenes_trabajo`.estado, `ordenes_trabajo`.criticidad,
    `ordenes_trabajo`.codigoordenes_trabajoref, `ordenes_trabajo`.tarea, `ordenes_trabajo`.fechainicio, 
    `ordenes_trabajo`.fechafin, `ordenes_trabajo`.prioridad, `ordenes_trabajo`.originador, `ordenes_trabajo`.ejecutante,
    `ordenes_trabajo`.centrodecosto, `ordenes_trabajo`.periodo, `ordenes_trabajo`.planprogramado, 
    `ordenes_trabajo`.observaciones, `ordenes_trabajo`.paradamolienda,
     mantenimiento_ordernestrabajo_tareas.paradamolienda, mantenimiento_ordernestrabajo_tareas.tiempo, mantenimiento_ordernestrabajo_tareas.cantoperarios,
     mantenimiento_ordernestrabajo_tareas.operador, mantenimiento_ordernestrabajo_tareas.ejecutante, mantenimiento_ordernestrabajo_tareas.descripcion, 
     mantenimiento_ordernestrabajo_tareas.tarea,bloqueo
    FROM ordenes_trabajo 
    left join mantenimiento_ordernestrabajo_tareas on ordenes_trabajo.id_orden_trabajo = mantenimiento_ordernestrabajo_tareas.idordendetrabajo
    where ordenes_trabajo.numero='".$_GET['numero']."' ORDER BY fechainicio DESC;";
    //echo $sql_ot_tareas;
    $resultado_ot_tareas = $mysqli->query($sql_ot_tareas) or die(mysqli_error($mysqli));   
    //$row_resultado_ot_tareas = $resultado_ot_tareas->fetch_assoc();

    //---------------------------------ORDENES TRABAJO INSUMOS--------------------------------------------------------------//

    $sql_ot_insumos = "  SELECT `ordenes_trabajo`.id_orden_trabajo, `ordenes_trabajo`.numero, `ordenes_trabajo`.tipoorden, 
    `ordenes_trabajo`.codigoordenes_trabajo, `ordenes_trabajo`.estado, `ordenes_trabajo`.criticidad,
    `ordenes_trabajo`.codigoordenes_trabajoref, `ordenes_trabajo`.tarea, `ordenes_trabajo`.fechainicio, 
    `ordenes_trabajo`.fechafin, `ordenes_trabajo`.prioridad, `ordenes_trabajo`.originador, `ordenes_trabajo`.ejecutante,
    `ordenes_trabajo`.centrodecosto, `ordenes_trabajo`.periodo, `ordenes_trabajo`.planprogramado, 
    `ordenes_trabajo`.observaciones, `ordenes_trabajo`.paradamolienda,
     mantenimiento_ordenestrabajo_insumos.cantidad, mantenimiento_ordenestrabajo_insumos.descripcion, mantenimiento_ordenestrabajo_insumos.codigo
    FROM ordenes_trabajo 
    left join mantenimiento_ordenestrabajo_insumos on ordenes_trabajo.id_orden_trabajo = mantenimiento_ordenestrabajo_insumos.idordendetrabajo
    where ordenes_trabajo.numero='".$_GET['numero']."' ORDER BY fechainicio DESC;";
    //echo $sql_mantenimiento;
    $resultado_ot_insumos = $mysqli->query($sql_ot_insumos) or die(mysqli_error($mysqli));   
    //$row_resultado_ot_insumos = $resultado_ot_insumos->fetch_assoc();
}

//---------------------------FUNCIONES-------------------------------------------------------------------------//

function agregar_ot($tipoorden,$codigoequipo,$estado,$criticidad,$tarea,$fechainicio,$fechafin,$prioridad,$originador,$ejecutante,$periodo,
                    $planprogramado,$observaciones,$paradamolienda,$avance){
     
        // 
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();

        $numero= nuevo_numero_ot();
    
        $codigoequiporef= obtiene_codigoref($codigoequipo);
        
        $sql_agregar_orden_trabajo = "  insert into ordenes_trabajo (numero ,tipoorden ,codigoequipo ,estado ,criticidad ,codigoequiporef ,tarea ,fechainicio ,fechafin ,prioridad ,originador ,
                                        ejecutante , periodo ,planprogramado ,observaciones ,paradamolienda,avance) 
                                        VALUE(".$numero." ,'".$tipoorden."',".$codigoequipo."  ,'".$estado."'  ,'".$criticidad."' ,".$codigoequiporef.",'".$tarea."',
                                              '".$fechainicio."','".$fechafin."','".$prioridad."','".$originador."','".$ejecutante."','".$periodo."',".$planprogramado.",
                                              '".$observaciones."','".$paradamolienda."',".$avance.");";
        //echo $sql_agregar_orden_trabajo;
        //$resultado_orden_trabajo = 
        $mysqli->query($sql_agregar_orden_trabajo) or die(mysqli_error($mysqli)); 
        if(mysqli_error($mysqli)==null){            
           echo '<script>formulario = opener.document.getElementById("form-list-ot");
                        formulario.ejecutar.click(); 
                        alert("Se genero la OT correctamente...");
                </script>';
        }  
    
}

function ver_ot($nro_ot){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();
    
    $sql_ot =" SELECT equipos.codigoequipo, equipos.descripcion, equipos.descripcionampliada, id_orden_trabajo, numero, tipoorden, estado, ordenes_trabajo.criticidad, 
    codigoequiporef, tarea, fechainicio, fechafin, prioridad, originador, ejecutante,centrodecosto, periodo, planprogramado, observaciones, paradamolienda,avance
    FROM ordenes_trabajo INNER JOIN equipos ON ordenes_trabajo.codigoequipo = equipos.id_equipo
    WHERE ordenes_trabajo.numero=".$nro_ot.";";
    //echo $sql_ot;
    $resultado_ot = $mysqli->query($sql_ot) or die($mysqli->error);
    $row_resultado_ot = $resultado_ot->fetch_assoc();
    return $row_resultado_ot;
}


function modificar_ot(  $nro_ot,$tipoorden,$estado,$tarea,$fechainicio,$fechafin,$prioridad,$originador,$ejecutante,$periodo,
                        $planprogramado,$observaciones,$parada_molienda,$avance,$usuario){
     
        include_once 'conexiones/conexion.php';
        include_once 'controller/usuarios.php';
        $mysqli=conexion_db();
        $update_sup_ot='';
        $usuario_aprobacion_ot='';

        if($estado=='Verificada'){
            $ot=ver_ot($nro_ot);
            $estado_ant=$ot['estado'];
            if($estado_ant<>'Verificada' and !empty($_GET['usuario'])){
                $usuario= base64_decode($_GET['usuario']); 
                $nombre_supervisor=nombre_usuario($usuario);
                $update_sup_ot=", usuario_verificacion='".$nombre_supervisor."'";
            }  
        }else{
            if($estado=='Aprobada'){
                $usuario= base64_decode($_GET['usuario']); 
                $nombre_supervisor=nombre_usuario($usuario);
                $usuario_aprobacion_ot=", usuario_aprobacion='".$nombre_supervisor."'";                
            }
        } 
        //echo $usuario_aprobacion_ot;              
        $sql_modificar_orden_trabajo = "  UPDATE ordenes_trabajo SET tipoorden='".$tipoorden."',estado='".$estado."',tarea='".$tarea."',
                                        fechainicio='".$fechainicio."' ,fechafin='".$fechafin."' ,prioridad='".$prioridad."' ,originador='".$originador."' ,
                                        ejecutante='$ejecutante' , periodo='".$periodo."' ,planprogramado=".$planprogramado." ,observaciones='".$observaciones."',
                                        paradamolienda='".$parada_molienda."', avance=".$avance." $update_sup_ot $usuario_aprobacion_ot WHERE numero=".$nro_ot." ;";
        //echo $sql_modificar_orden_trabajo;        
        $mysqli->query($sql_modificar_orden_trabajo) or die(mysqli_error($mysqli)); 
        if(mysqli_error($mysqli)==null){

           echo '<script>
                        formulario = opener.document.getElementById("form-list-ot");
                        formulario.ejecutar.click(); 
                        
                        alert("Se actualizo la OT correctamente...");
                        //close();
                </script>';
           
        }  
    
}

function ver_ots_equipo($id_equipo){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();

    $sql_ots_equipo = "  select numero, tipoorden, ordenes_trabajo.codigoequipo 'cod equipo OT', estado, ordenes_trabajo.criticidad, id_orden_trabajo, numero, tipoorden,
                            estado, ordenes_trabajo.criticidad , codigoequiporef,
                            ordenes_trabajo.tarea, fechainicio, fechafin, prioridad, originador, ordenes_trabajo.ejecutante, centrodecosto, periodo, planprogramado, observaciones, ordenes_trabajo.paradamolienda,
                            equipos.codigoequipo, equipos.codigonivel, equipos.descripcion
                            FROM ordenes_trabajo 
                            inner join equipos on ordenes_trabajo.codigoequipo = equipos.id_equipo
                            where id_equipo=".$id_equipo." 
                            ORDER BY ordenes_trabajo.numero DESC;";
    //echo $sql_ots_equipo;
    $resultado_ots_equipo = $mysqli->query($sql_ots_equipo) or die(mysqli_error($mysqli));   
    return $resultado_ots_equipo;
}

function asignar_color_ordenes_trabajo($estado){

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
//----------------------------------------------------------------------------------------------------/
?>
<script>
    function ver_detalle_ot(nro_ot,usuario_cod)
    {
        sel = window.open("report.php?accion=visualizar_detalle_ot&nro_ot="+nro_ot+"&usuario="+usuario_cod, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }

    function modificar_ot(nro_ot,usuario_cod)
    {
        sel = window.open("view.php?opcion=modificar_ot&nro_ot="+nro_ot+"&usuario="+usuario_cod, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }

    function oculta_contenido(id){
        if (document.getElementById){ //se obtiene el id
            var el = document.getElementById(id); //se define la variable "el" igual a nuestro div
            el.style.display = (el.style.display == 'none') ? 'block' : 'none';
        }
    }

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

</script>