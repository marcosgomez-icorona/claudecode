<?php 
if(!empty($_POST['accion']) and $_POST['accion']=='asociar_componente_equipo' and !empty($_GET['id_equipo']) and !empty($_POST['idproducto']) and !empty($_POST['codproducto'])){
    asociar_componente_equipo($_GET['id_equipo'],$_POST['idproducto'],$_POST['codproducto'],$_POST['descripcion'],$_POST['cantidad']);
}
if(!empty($_POST['id_componente']) and !empty($_POST['accion']) and $_POST['accion']=='modificar_insumo_asociado'){    
    modificar_componente_equipo($_POST['id_componente'],$_POST['cantidad']);
}


//------------FUNCIONES---------//

function lista_componentes_equipo($id_equipo){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();
    
    if(!empty($mysqli)){
        $sql_componentes_equipo = "SELECT id_componente, idequipo, idproducto, codproducto, descripcion, cantidad, stockminimo
                            FROM componentes WHERE idequipo=".$id_equipo." ORDER BY descripcion ASC;";        
        $resultado_componentes_equipo = $mysqli->query($sql_componentes_equipo) or die(mysqli_error($mysqli));        

        
    }else{
        echo "<script> alert('No se puede establecer la conexion')</script>";
    }

    return $resultado_componentes_equipo;    
}

function listado_componentes($buscar){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();

    if(!empty($mysqli)){
        if(!empty($buscar)){            
            
            //DIVIDO LAS PALABRAS EN UN VECTOR PARA MEJOR FILTRADO DEL LIKE
            $busqueda_separada_x_espacios = preg_split("/[\s,]+/", $buscar);
           
            if(empty($busqueda_separada_x_espacios[1])){
                $where= "WHERE componentes.descripcion LIKE '%".$buscar."%' OR equipos.descripcion LIKE '%".$buscar."%'";        
            }else{
                
                    $where= "WHERE componentes.descripcion LIKE '%".$busqueda_separada_x_espacios[0]."%".$busqueda_separada_x_espacios[1]."%' OR componentes.descripcion LIKE '%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[0]."%' OR equipos.descripcion LIKE '%".$busqueda_separada_x_espacios[0]."%".$busqueda_separada_x_espacios[1]."%' OR equipos.descripcion LIKE '%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[0]."%'";            
            }
        
            if(!empty($busqueda_separada_x_espacios[2])){
                $where= "WHERE componentes.descripcion LIKE '%".$busqueda_separada_x_espacios[0]."%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[2]."%' OR componentes.descripcion LIKE '%".$busqueda_separada_x_espacios[2]."%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[0]."%' OR equipos.descripcion LIKE '%".$busqueda_separada_x_espacios[0]."%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[2]."%' OR equipos.descripcion LIKE '%".$busqueda_separada_x_espacios[2]."%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[0]."%'";            
            }
        
        }else{
            $where='';
            
        }
        $sql_componentes = "SELECT id_componente, idproducto, codproducto, componentes.descripcion 'componente', cantidad,equipos.codigoequipo, equipos.descripcion 'equipo', 
                            equipos.descripcionampliada
                            FROM componentes LEFT JOIN equipos on componentes.idequipo= equipos.id_equipo
                            ".$where." ORDER BY  equipos.codigoequipo asc,componentes.descripcion ASC;"; 
        echo $sql_componentes;                    
        $resultado_componentes = $mysqli->query($sql_componentes) or die(mysqli_error($mysqli));        

        
    }else{
        echo "<script> alert('No se puede establecer la conexion')</script>";
    }

    return $resultado_componentes;    
}

function obtiene_repuesto_insumo($id){

    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();
    if(!empty($mysqli)){
        $sql_componente = "SELECT id_componente, idequipo, idproducto, codproducto, descripcion, cantidad, stockminimo
                            FROM componentes where id_componente=$id;";
        //echo $sql_componentes;
        $resultado_componente = $mysqli->query($sql_componente) or die(mysqli_error($mysqli));
    
        return $resultado_componente;
    }else{
        echo "<script> alert('No se puede establecer la conexion')</script>";
    }
    
}

function lista_productos(){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();
    if(!empty($mysqli)){
        $sql_productos = "  SELECT id_producto, id, codigo, descripcion, stockminimo, cantidad
                            FROM productos ORDER BY codigo ASC;";        
        $resultado_productos = $mysqli->query($sql_productos) or die(mysqli_error($mysqli));        

        
    }else{
        echo "<script> alert('No se puede establecer la conexion')</script>";
    }

    return $resultado_productos; 
}

function asociar_componente_equipo($id_equipo,$idproducto,$codproducto,$descripcion,$cantidad){
    include_once 'conexiones/conexion.php';
       
        $sql_asociar_componente_equipo = "  INSERT INTO componentes (idequipo,idproducto,codproducto,descripcion ,cantidad)
                                            VALUE(".$id_equipo." ,'".$idproducto."','".$codproducto."','".$descripcion."',".$cantidad.");"; 
        //echo $sql_asociar_componente_equipo;
        $mysqli->query($sql_asociar_componente_equipo) or die(mysqli_error($mysqli)); 
        if(mysqli_error($mysqli)==null){
           echo '<script>alert("Se asocio correctamente...");</script>';
        }  
}

function ver_componente_asociado($id_componente){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();
    if(!empty($mysqli)){
        $sql_componente = "  SELECT id_componente, idequipo, idproducto, codproducto, descripcion, cantidad, stockminimo
                            FROM componentes WHERE id_componente=$id_componente ;";        
        $resultado_componente = $mysqli->query($sql_componente) or die(mysqli_error($mysqli));        
        $row_componente = $resultado_componente->fetch_assoc();
        
    }else{
        echo "<script> alert('No se puede establecer la conexion')</script>";
    }

    return $row_componente; 
}

function modificar_componente_equipo($id_componente,$cantidad){
    include_once 'conexiones/conexion.php';
       
        $sql_asociar_componente_equipo = "  UPDATE componentes SET cantidad = ".$cantidad." WHERE id_componente = ".$id_componente." "; 
        //echo $sql_asociar_componente_equipo;
        $mysqli->query($sql_asociar_componente_equipo) or die(mysqli_error($mysqli)); 
        if(mysqli_error($mysqli)==null){
           echo '<script>alert("Listo");</script>';
        }  
}
?>
<script>
    function seleccionar_componente(idProducto,codproducto,descripcion) {
      
      // Asignar los valores a los campos
      document.getElementById("idproducto").value = idProducto;
      document.getElementById("codproducto").value = codproducto;
      document.getElementById("descripcion").value = descripcion;
      
    }

    function componentes_asociados_equipo(id_equipo,codigo,equipo)
    {
        sel = window.open("view.php?opcion=componentes_asociados_equipo&id_equipo="+id_equipo+"&codigo="+codigo+"&equipo="+equipo, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }

    function asociar_componente_equipo(id_equipo,codigo,equipo)
    {
        sel = window.open("view.php?opcion=asociar_componente_equipo&id_equipo="+id_equipo+"&codigo="+codigo+"&equipo="+equipo, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }
    function modificar_componente_equipo(id_componente,cantidad)
    {
        sel = window.open("view.php?opcion=modificar_componente_equipo&id_componente="+id_componente+"&cantidad="+cantidad, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }
  </script>