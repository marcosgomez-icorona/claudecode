<?php
if(!empty($_FILES)){
    
        $fileTmpPath = $_FILES['archivo']['tmp_name'];
        $fileName = $_FILES['archivo']['name'];
        $fileSize = $_FILES['archivo']['size'];
        $fileType = $_FILES['archivo']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $nombre_archivo = $fileName.'_OT_Nro_'.$_GET['nro_ot'];
        $uploadFileDir = 'assets/adjuntos_ot/';
        $dest_path = $uploadFileDir . $nombre_archivo;
        if(move_uploaded_file($fileTmpPath, $dest_path)){
            $message ='File is successfully uploaded.';
        }
        else{
                $message = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
                echo '<script>alert("'.$message.'")</script>';
            }


    if(!empty($_POST['accion']) and $_POST['accion']=='alta_adjunto_ordenes' and !empty($_GET['nro_ot'])){
        
        
            agregar_adjunto($_GET['nro_ot'],$nombre_archivo);

    }

    if(!empty($_POST['accion']) and $_POST['accion']=='modificar_adjunto_ordenes' and !empty($_POST['id_adjunto_orden'])){
        
        modificar_adjunto($_POST['id_adjunto_orden'],$nombre_archivo);

    }
}
//--------------FUNCIONES--------------------------//
function listado_adjuntos($nro_ot){

    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();

    $sql_observacion_ot ="SELECT observaciones FROM ordenes_trabajo where numero=".$nro_ot.";";
    $resultado_observacion_ot = $mysqli->query($sql_observacion_ot) or die($mysqli->error);
    $observacion_ot = $resultado_observacion_ot->fetch_assoc();    

    $sql_adjuntos ="SELECT id_adjunto_orden, idorden, archivo, control FROM adjunto_ordenes
                    where idorden=".$nro_ot." order by id_adjunto_orden desc";
    $resultado_adjuntos = $mysqli->query($sql_adjuntos) or die($mysqli->error);
    return [$resultado_adjuntos, $observacion_ot['observaciones']];
}

function ver_adjunto($id_adjunto_orden){
    include_once 'conexiones/conexion.php';
    $mysqli=conexion_db();

    $sql_adjunto ="SELECT id_adjunto_orden, idorden, archivo, control FROM adjunto_ordenes
                    where id_adjunto_orden=".$id_adjunto_orden.";";
    $resultado_adjunto = $mysqli->query($sql_adjunto) or die($mysqli->error);
    $adjunto = $resultado_adjunto->fetch_assoc();    
    return $adjunto;
}

function agregar_adjunto($idordendetrabajo,$archivo){
     
        // 
        include_once 'conexiones/conexion.php';
        //----------------//
        $sql_agregar_adjunto = "INSERT INTO adjunto_ordenes (idorden  ,archivo)
                                VALUE(".$idordendetrabajo." ,'".$archivo."');";
        //echo $sql_agregar_adjunto;
        //$resultado_adjuntos = 
        $mysqli->query($sql_agregar_adjunto) or die(mysqli_error($mysqli)); 
        if(mysqli_error($mysqli)==null){
           echo '<script>alert("Se genero correctamente...");</script>';
        }  
    
}

function modificar_adjunto($id_adjunto_orden,$archivo){
     
    include_once 'conexiones/conexion.php';
    //----------------//
    $sql_agregar_adjunto = "update adjunto_ordenes SET archivo = '".$archivo."' WHERE id_adjunto_orden = ".$id_adjunto_orden.";";
    $mysqli->query($sql_agregar_adjunto) or die(mysqli_error($mysqli)); 
    if(mysqli_error($mysqli)==null){
       echo '<script>alert("Se actualizo correctamente...");</script>';
    }  

}

?>
<script>
    function ver_adjunto_ordenes(nro_ot)
    {
        sel = window.open("view.php?opcion=ver_adjunto_ordenes&nro_ot="+nro_ot, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }

    function modificar_adjunto_ordenes(nro_ot,id_adjunto_orden)
    {
        sel = window.open("view.php?opcion=modificar_adjunto_ordenes&nro_ot="+nro_ot+"&id_adjunto_orden="+id_adjunto_orden, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }
</script>