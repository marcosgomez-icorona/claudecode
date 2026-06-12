<?php

    include_once 'conexiones/conexion.php';
    include_once 'funciones/funciones.php';

    $filtro='';

    $mysqli=conexion_db();
    //
    if(isset($_GET['usuario'])){
        $usuario= base64_decode($_GET['usuario']);
        $sql_proveedor = " select cuit, nombre FROM proveedores where cuit='".$usuario."'; ";    
        $proveedor = $mysqli->query($sql_proveedor) or die(mysqli_error($mysqli)); 
        $row_proveedor = $proveedor->fetch_assoc();
        $where="WHERE TRIM(REPLACE(REPLACE(REPLACE(proveedor, CHAR(13), ''), CHAR(10), ''), CHAR(9), '')) =TRIM('".$row_proveedor['nombre']."')";
    }else{

        if($_GET['proveedor']){//SE CUMPLE CUANDO SE ENVIA POR WEBHOOK 
           
            $sql_proveedor = " select cuit, nombre FROM proveedores WHERE TRIM(REPLACE(REPLACE(REPLACE(nombre, CHAR(13), ''), CHAR(10), ''), CHAR(9), '')) =TRIM('".$_GET['proveedor']."'); ";    
            //echo $sql_proveedor;
            $proveedor = $mysqli->query($sql_proveedor) or die(mysqli_error($mysqli)); 
            $row_proveedor = $proveedor->fetch_assoc();
            $where="WHERE TRIM(REPLACE(REPLACE(REPLACE(proveedor, CHAR(13), ''), CHAR(10), ''), CHAR(9), '')) =TRIM('".$row_proveedor['nombre']."')";
        }
        
    }
    
    $sql_ordenes_compra = " SELECT nro_oc, fecha_emision,ROUND(total,2) 'total', detalle, plazo_entrega, condicion_pago, proveedor, ultima_actualizacion
                            FROM ordenes_compra
                            $where
                            ORDER BY fecha_emision DESC, nro_oc DESC; ";
    //echo $sql_ordenes_compra;
    $resultado_ordenes_compra = $mysqli->query($sql_ordenes_compra) or die(mysqli_error($mysqli));
  

    // Asegúrate de que la conexión $mysqli esté disponible aquí,
// ya sea porque este archivo la inicializa o porque se incluye después de la inicialización.
// Si $mysqli NO está disponible aquí, tendrías que pasarla como argumento a la función,
// o inicializarla (include_once 'conexiones/conexion.php'; $mysqli = conexion_db();)
// si este archivo es un punto de entrada independiente de datos.

/**
 * Obtiene los ítems de una orden de compra específica.
 * @param mysqli $mysqli La conexión a la base de datos.
 * @param string $nro_oc El número de orden de compra.
 * @return array Un array de arrays asociativos con los ítems de la OC.
 */


 function getDatosOC(string $nro_oc): ?array { // <-- Cambiado a devolver ?array (puede ser null)
    include_once 'conexiones/conexion.php';
    include_once 'funciones/funciones.php'; // Para cualquier función auxiliar

    $mysqli = conexion_db();
    if ($mysqli->connect_errno) {
        error_log("Error al conectar con la base de datos: " . $mysqli->connect_error);
        return null; // Retorna null si la conexión falla
    }

    // Corregida la consulta SQL
    $sql = "SELECT id,nro_oc, fecha_emision, detalle, plazo_entrega, condicion_pago, total, proveedor,originante, ultima_actualizacion 
            FROM ordenes_compra          
            WHERE nro_oc = ?"; // Eliminada la 'S' al final

    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        error_log("Error al preparar la consulta de OC: " . $mysqli->error);
        $mysqli->close(); // Cerrar conexión en caso de error
        return null; // Devuelve null en caso de error
    }

    $stmt->bind_param("s", $nro_oc);
    $stmt->execute();
    $result = $stmt->get_result();

    $oc_data_row = $result->fetch_assoc(); // <-- Aquí obtienes UNA SOLA FILA
    
    $result->free();
    $stmt->close();
    $mysqli->close(); // Cerrar la conexión aquí si la abres dentro de la función

    return $oc_data_row; // <-- Devuelve el array asociativo directamente (o null si no hay resultados)
}

function getDetalleOcItems(string $nro_oc): array {
    include_once 'conexiones/conexion.php';
    include_once 'funciones/funciones.php';

    $mysqli=conexion_db();
    
    // Es crucial usar consultas preparadas para evitar inyección SQL
    $sql = "SELECT numero_item, codigo, articulo, cantidad, uni_med, pu, total_item, fecha_entrega
            FROM detalle_oc
            WHERE nro_oc = ?
            ORDER BY numero_item ASC";
    //echo $sql;
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        // Manejar el error si la preparación de la consulta falla
        error_log("Error al preparar la consulta de ítems de OC: " . $mysqli->error);
        return []; // Devuelve un array vacío en caso de error
    }

    // "s" indica que el parámetro $nro_oc es una cadena (string)
    $stmt->bind_param("s", $nro_oc);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    $result->free(); // Liberar el resultado
    $stmt->close();  // Cerrar el statement

    return $items;
}

function getDatosOriginante(string $originante): ?array { // <-- Cambiado a devolver ?array (puede ser null)
    include_once 'conexiones/conexion.php';
    include_once 'funciones/funciones.php'; // Para cualquier función auxiliar

    $mysqli = conexion_db();
    if ($mysqli->connect_errno) {
        error_log("Error al conectar con la base de datos: " . $mysqli->connect_error);
        return null; // Retorna null si la conexión falla
    }

    // Corregida la consulta SQL
    $sql = "SELECT cuit, nombre, domicilio FROM proveedores WHERE nombre = ? "; 

    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        error_log("Error al preparar la consulta de OC: " . $mysqli->error);
        $mysqli->close(); // Cerrar conexión en caso de error
        return null; // Devuelve null en caso de error
    }

    $stmt->bind_param("s", $originante);
    $stmt->execute();
    $result = $stmt->get_result();

    $oc_data_row = $result->fetch_assoc(); // <-- Aquí obtienes UNA SOLA FILA
    
    $result->free();
    $stmt->close();
    $mysqli->close(); // Cerrar la conexión aquí si la abres dentro de la función

    return $oc_data_row; // <-- Devuelve el array asociativo directamente (o null si no hay resultados)
}
?>