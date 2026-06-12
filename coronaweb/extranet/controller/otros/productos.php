<?php 

function maestro_productos($buscar,$codigo,$producto){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();
    if(!empty($mysqli)){
        if($buscar=='si'){
            if(!empty($codigo)){
                $where="Where codigo=".$codigo." ";
            }else{
                if(!empty($producto)){
                    $where="Where descripcion LIKE '%".$producto."%' ";
                }else{
                    $where='';
                }
            }
        }else{
            $where=' Where 1=0';
        }
        $sql_productos = "  SELECT id_producto, id, codigo, descripcion, stockminimo, cantidad
                            FROM productos ".$where." ORDER BY codigo ASC;"; 
        //echo $sql_productos;       
        $resultado_productos = $mysqli->query($sql_productos) or die(mysqli_error($mysqli));        

        
    }else{
        echo "<script> alert('No se puede establecer la conexion')</script>";
    }

    return $resultado_productos; 
}

function listado_productos($buscar){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();

    if(!empty($mysqli)){
        if(!empty($buscar)){            
            
            //DIVIDO LAS PALABRAS EN UN VECTOR PARA MEJOR FILTRADO DEL LIKE
            $busqueda_separada_x_espacios = preg_split("/[\s,]+/", $buscar);
           
            if(empty($busqueda_separada_x_espacios[1])){
                $where= "WHERE descripcion LIKE '%".$buscar."%' OR codigo LIKE '%".$buscar."%'";        
            }else{
                
                    $where= "WHERE descripcion LIKE '%".$busqueda_separada_x_espacios[0]."%".$busqueda_separada_x_espacios[1]."%' OR descripcion LIKE '%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[0]."%' OR codigo LIKE '%".$busqueda_separada_x_espacios[0]."%".$busqueda_separada_x_espacios[1]."%' OR codigo LIKE '%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[0]."%'";            
            }
        
            if(!empty($busqueda_separada_x_espacios[2])){
                $where= "WHERE descripcion LIKE '%".$busqueda_separada_x_espacios[0]."%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[2]."%' OR descripcion LIKE '%".$busqueda_separada_x_espacios[2]."%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[0]."%' OR codigo LIKE '%".$busqueda_separada_x_espacios[0]."%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[2]."%' OR codigo LIKE '%".$busqueda_separada_x_espacios[2]."%".$busqueda_separada_x_espacios[1]."%".$busqueda_separada_x_espacios[0]."%'";            
            }
        
        }else{
            $where='';
            
        }
        $sql_productos = "  SELECT id_producto, id, codigo, descripcion, rubro, centro_costo, peso, volumen, bienuso, stockminimo, cantidad, kit, stockmaximo, alto, ancho,
                            espesor, stockoptimo
                            FROM productos ".$where." ORDER BY codigo ASC;"; 
        //echo $sql_productos;       
        $resultado_productos = $mysqli->query($sql_productos) or die(mysqli_error($mysqli));        

        
    }else{
        echo "<script> alert('No se puede establecer la conexion')</script>";
    }

    return $resultado_productos; 
}

?>
<script>
    function seleccionar_producto(idProducto,codproducto,descripcion) {
      
      // Asignar los valores a los campos
      document.getElementById("idproducto").value = idProducto;
      document.getElementById("codproducto").value = codproducto;
      document.getElementById("descripcion").value = descripcion;

      // Ocultar Listado de Productos
      var lista = document.getElementById("lista_productos");
        if (lista.style.display === "none") {
            lista.style.display = "block";
        } else {
            lista.style.display = "none";
        }

    }

    
    function asociar_producto_equipo(id_equipo,codigo,equipo)
    {
        sel = window.open("view.php?opcion=asociar_producto_equipo&id_equipo="+id_equipo+"&codigo="+codigo+"&equipo="+equipo, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }
  </script>