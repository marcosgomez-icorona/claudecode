<?php

    include_once 'conexiones/conexion.php';
    include_once 'funciones/funciones.php';   

    $mysqli=conexion_db();
    //
    if(isset($_GET['usuario'])){
        $usuario= base64_decode($_GET['usuario']);
        $sql_proveedor = " select cuit, nombre, domicilio FROM proveedores where cuit='".$usuario."'; ";    
        $proveedor = $mysqli->query($sql_proveedor) or die(mysqli_error($mysqli)); 
        $row = $proveedor->fetch_assoc();                
    }else{
        $usuario='';
        $where='Were 1=0';
    }    

    function getDatosComprador(string $usuario): ?array { // <-- Cambiado a devolver ?array (puede ser null)
        include_once 'conexiones/conexion.php';
        include_once 'funciones/funciones.php'; // Para cualquier función auxiliar
    
        $cuit= base64_decode($usuario);
        $mysqli = conexion_db();
        if ($mysqli->connect_errno) {
            error_log("Error al conectar con la base de datos: " . $mysqli->connect_error);
            return null; // Retorna null si la conexión falla
        }
    
        // Corregida la consulta SQL
        $sql = "select cuit, nombre as razon_social, domicilio as direccion FROM proveedores where cuit = ?"; // Eliminada la 'S' al final
    
        $stmt = $mysqli->prepare($sql);
    
        if (!$stmt) {
            error_log("Error al preparar la consulta: " . $mysqli->error);
            $mysqli->close(); // Cerrar conexión en caso de error
            return null; // Devuelve null en caso de error
        }
    
        $stmt->bind_param("s", $cuit);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $comprador_data_row = $result->fetch_assoc(); // <-- Aquí obtienes UNA SOLA FILA
        
        $result->free();
        $stmt->close();
        $mysqli->close(); // Cerrar la conexión aquí si la abres dentro de la función
    
        return $comprador_data_row; // <-- Devuelve el array asociativo directamente (o null si no hay resultados)
    }

    function getDatosEmpresa(string $empresa_cod): ?array { // <-- Cambiado a devolver ?array (puede ser null)
        include_once 'conexiones/conexion.php';
        include_once 'funciones/funciones.php'; // Para cualquier función auxiliar
    
        $cuit= base64_decode($empresa_cod);
      
        $mysqli = conexion_db();
        if ($mysqli->connect_errno) {
            error_log("Error al conectar con la base de datos: " . $mysqli->connect_error);
            return null; // Retorna null si la conexión falla
        }
    
        // Corregida la consulta SQL
        $sql = "select cuit, nombre as razon_social, domicilio as direccion FROM proveedores where cuit = '$cuit' ";    
        //echo $sql;
        $stmt = $mysqli->prepare($sql);
    
        if (!$stmt) {
            error_log("Error al preparar la consulta: " . $mysqli->error);
            $mysqli->close(); // Cerrar conexión en caso de error
            return null; // Devuelve null en caso de error
        }
            
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result === false) {
            error_log("Error en get_result(): " . $stmt->error);
            $stmt->close();
            $mysqli->close();
            return null;
        }
        $data_row = $result->fetch_assoc(); // <-- Aquí obtienes UNA SOLA FILA
        
        $result->free();
        $stmt->close();
        $mysqli->close(); // Cerrar la conexión aquí si la abres dentro de la función
    
        return $data_row; // <-- Devuelve el array asociativo directamente (o null si no hay resultados)
    }
?>