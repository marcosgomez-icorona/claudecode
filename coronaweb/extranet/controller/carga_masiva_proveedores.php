<?php
// controller/carga_masiva_proveedores.php

include_once 'conexiones/conexion.php';
include_once 'funciones/funciones.php';

// Establecer la conexión a la base de datos al inicio del script.
$mysqli = conexion_db();

// Verificar si la conexión fue exitosa al inicio del script.
if ($mysqli->connect_errno) {
    die("Error al conectar con la base de datos: " . $mysqli->connect_error);
}

// Lógica para obtener datos del proveedor del usuario actual (no directamente relacionada con la carga masiva)
$row = null;
if (!empty($_GET['usuario'])) {
    $usuario = base64_decode($_GET['usuario']);
    $sql_proveedor = "SELECT cuit, nombre FROM proveedores WHERE cuit = ?";
    $stmt_proveedor_actual = $mysqli->prepare($sql_proveedor);

    if ($stmt_proveedor_actual) {
        $stmt_proveedor_actual->bind_param("s", $usuario);
        $stmt_proveedor_actual->execute();
        $resultado_proveedor = $stmt_proveedor_actual->get_result();
        $row = $resultado_proveedor->fetch_assoc();
        $resultado_proveedor->free();
        $stmt_proveedor_actual->close();
    } else {
        error_log("Error al preparar la consulta de proveedor actual: " . $mysqli->error);
    }
}

// Lógica principal para iniciar la carga masiva
if (!empty($_FILES) && !empty($_POST['carga_masiva'])) {
    // Aquí es donde se llamará la función `subir`.
    // Añadimos una depuración antes y después.
    echo "<script>console.log('Iniciando proceso de carga masiva...');</script>";
    subir($mysqli); // Pasa la conexión $mysqli como argumento
    echo "<script>console.log('Proceso de carga masiva finalizado.');</script>";
}

/**
 * Función para subir y procesar el archivo CSV.
 * @param mysqli $mysqli Objeto de conexión a la base de datos.
 */
function subir(mysqli $mysqli){

    $proveedores_nuevos = 0;
    $filas_procesadas = 0;
    $errores_insercion_tmp = []; // Para capturar errores en la tabla temporal
    $errores_insercion_proveedores = []; // Para capturar errores en la tabla proveedores
    $proveedores_existentes_saltados = 0; // Contador de proveedores que ya existían

    if (!empty($_POST['carga_masiva'])) {

        // 1. Verificar el archivo subido
        if (!isset($_FILES['filename']) || $_FILES['filename']['error'] !== UPLOAD_ERR_OK) {
            $error_code = $_FILES['filename']['error'] ?? 'UNKNOWN';
            $error_message = '';
            switch ($error_code) {
                case UPLOAD_ERR_INI_SIZE: $error_message = 'El archivo excede el tamaño máximo permitido por el servidor.'; break;
                case UPLOAD_ERR_FORM_SIZE: $error_message = 'El archivo excede el tamaño máximo permitido por el formulario.'; break;
                case UPLOAD_ERR_PARTIAL: $error_message = 'El archivo se subió parcialmente.'; break;
                case UPLOAD_ERR_NO_FILE: $error_message = 'No se seleccionó ningún archivo para subir.'; break;
                case UPLOAD_ERR_NO_TMP_DIR: $error_message = 'Falta una carpeta temporal.'; break;
                case UPLOAD_ERR_CANT_WRITE: $error_message = 'Fallo al escribir el archivo en disco.'; break;
                case UPLOAD_ERR_EXTENSION: $error_message = 'Una extensión de PHP detuvo la subida del archivo.'; break;
                default: $error_message = 'Error desconocido al subir el archivo.'; break;
            }
            echo "<script language='javascript'>alert('Error al subir el archivo: " . $error_message . " (Código: " . $error_code . ")');</script>";
            return;
        }
        
        // 2. Abrir el archivo CSV temporal
        $handle = fopen($_FILES['filename']['tmp_name'], "r");

        if ($handle === FALSE) {
            echo "<script language='javascript'>alert('Error crítico: No se pudo abrir el archivo CSV temporal.');</script>";
            return;
        }
        
        echo "<script>console.log('CSV abierto correctamente.');</script>";

        // 3. Limpiar la tabla temporal `carga_masiva`
        $mysqli->query('TRUNCATE TABLE carga_masiva;');
        if ($mysqli->errno) {
            echo "<script language='javascript'>alert('Error al limpiar la tabla carga_masiva: " . $mysqli->error . "');</script>";
            fclose($handle);
            return;
        }
        echo "<script>console.log('Tabla carga_masiva truncada.');</script>";

        // 4. Preparar la consulta INSERT para `carga_masiva`
        // Asumiendo que c2 es NOMBRE y c1 es CUIT.
        $insert_tmp_stmt = $mysqli->prepare("INSERT INTO carga_masiva (c2, c1, existe) VALUES (?, ?, 'N')");
        if (!$insert_tmp_stmt) {
            echo "<script language='javascript'>alert('Error al preparar la consulta de inserción en carga_masiva: " . $mysqli->error . "');</script>";
            fclose($handle);
            return;
        }
        echo "<script>console.log('Prepared statement para carga_masiva listo.');</script>";

        // 5. Leer el CSV e insertar en `carga_masiva`
        $fila_csv = 0;
        // ¡CAMBIO AQUÍ: "," en lugar de ";"!
        while (($data = fgetcsv($handle, 1024, ",")) !== FALSE) {
            $fila_csv++; // Incrementa para la fila actual
            if ($fila_csv === 1) { // Si es la PRIMERA fila (fila número 1)
                continue; // Salta al siguiente ciclo del bucle (es decir, ignora el resto del código para esta fila)
            }
        

            // A. Manejo de "Undefined offset": Asegurarse de que el índice exista y trimmar.
            $nombre_csv = isset($data[0]) ? trim($data[0]) : '';
            $cuit_csv = isset($data[1]) ? trim($data[1]) : '';

            // Saltamos filas donde CUIT o Nombre estén vacíos (pueden ser filas incompletas o errores en el CSV)
            if (empty($nombre_csv) || empty($cuit_csv)) {
                error_log("Saltando fila " . $fila_csv . " por datos incompletos (Nombre: '" . $nombre_csv . "', CUIT: '" . $cuit_csv . "')");
                echo "<script>console.log('Saltando fila " . $fila_csv . " de CSV: Nombre o CUIT vacíos.');</script>";
                continue;
            }
            
            $insert_tmp_stmt->bind_param("ss", $nombre_csv, $cuit_csv);
            if (!$insert_tmp_stmt->execute()) {
                $error_msg = "Error al insertar fila " . $fila_csv . " en carga_masiva: " . $insert_tmp_stmt->error . " (Nombre: '" . $nombre_csv . "', CUIT: '" . $cuit_csv . "')";
                error_log($error_msg);
                $errores_insercion_tmp[] = $error_msg;
                echo "<script>console.log('" . $error_msg . "');</script>";
            } else {
                $filas_procesadas++;
            }
        }
        $insert_tmp_stmt->close();
        fclose($handle);
        echo "<script>console.log('Inserción en carga_masiva completada. Filas procesadas: " . $filas_procesadas . ". Errores en carga_masiva: " . count($errores_insercion_tmp) . "');</script>";

        // 6. Procesar los datos de `carga_masiva` e insertar/actualizar en `proveedores`
        $sql_select_carga = 'SELECT c2 AS nombre, c1 AS cuit FROM carga_masiva WHERE c2 != "" AND c1 != "";';
        $carga_res = $mysqli->query($sql_select_carga);

        if (!$carga_res) {
            echo "<script language='javascript'>alert('Error al consultar datos de carga_masiva para procesar: " . $mysqli->error . "');</script>";
            return;
        }
        echo "<script>console.log('Consulta a carga_masiva para procesar, resultados obtenidos.');</script>";


        // Preparar la consulta INSERT para `proveedores` (¡Seguro!)
        $insert_proveedor_stmt = $mysqli->prepare("INSERT INTO proveedores (nombre, cuit) VALUES (?, ?)");
        if (!$insert_proveedor_stmt) {
            echo "<script language='javascript'>alert('Error al preparar la consulta de alta de proveedor: " . $mysqli->error . "');</script>";
            $carga_res->free();
            return;
        }
        echo "<script>console.log('Prepared statement para insertar en proveedores listo.');</script>";


        // Preparar la consulta SELECT para verificar si el proveedor ya existe (¡Seguro!)
        $select_proveedor_stmt = $mysqli->prepare("SELECT cuit FROM proveedores WHERE cuit = ?");
        if (!$select_proveedor_stmt) {
            echo "<script language='javascript'>alert('Error al preparar la consulta de verificación de proveedor existente: " . $mysqli->error . "');</script>";
            $insert_proveedor_stmt->close();
            $carga_res->free();
            return;
        }
        echo "<script>console.log('Prepared statement para verificar existencia de proveedor listo.');</script>";


        while ($row_carga_final = $carga_res->fetch_assoc()) {
            $nombre_proveedor = $row_carga_final['nombre'];
            $cuit_proveedor = $row_carga_final['cuit'];

            echo "<script>console.log('Procesando proveedor desde carga_masiva: Nombre=\"" . $nombre_proveedor . "\", CUIT=\"" . $cuit_proveedor . "\"');</script>";

            // Verificar si el proveedor ya existe
            $select_proveedor_stmt->bind_param("s", $cuit_proveedor);
            $select_proveedor_stmt->execute();
            $select_proveedor_stmt->store_result();
            
            if ($select_proveedor_stmt->num_rows == 0) { // Si no existe, insertar
                echo "<script>console.log('  CUIT " . $cuit_proveedor . " NO encontrado en proveedores. Intentando insertar...');</script>";
                $insert_proveedor_stmt->bind_param("ss", $nombre_proveedor, $cuit_proveedor);
                if ($insert_proveedor_stmt->execute()) {
                    $proveedores_nuevos++;
                    echo "<script>console.log('  INSERTADO exitosamente: " . $cuit_proveedor . "');</script>";
                } else {
                    $error_msg = "Error al insertar nuevo proveedor (" . $cuit_proveedor . "): " . $insert_proveedor_stmt->error;
                    error_log($error_msg);
                    $errores_insercion_proveedores[] = $error_msg;
                    echo "<script>console.log('  " . $error_msg . "');</script>";
                }
            } else {
                $proveedores_existentes_saltados++;
                echo "<script>console.log('  CUIT " . $cuit_proveedor . " YA EXISTE en proveedores. Saltando inserción.');</script>";
            }
            $select_proveedor_stmt->free_result(); // Liberar resultado de SELECT para la siguiente iteración
        }
        
        $select_proveedor_stmt->close();
        $insert_proveedor_stmt->close();
        $carga_res->free();
        echo "<script>console.log('Procesamiento de carga_masiva a proveedores completado.');</script>";

        // 7. Mostrar mensaje de éxito y limpiar la tabla temporal
        $final_alert_message = "Proceso de carga completado.\\n";
        $final_alert_message .= "Filas procesadas del CSV: " . $filas_procesadas . ".\\n";
        $final_alert_message .= "Proveedores nuevos insertados: " . $proveedores_nuevos . ".\\n";
        $final_alert_message .= "Proveedores ya existentes (saltados): " . $proveedores_existentes_saltados . ".\\n";
        if (!empty($errores_insercion_tmp) || !empty($errores_insercion_proveedores)) {
            $final_alert_message .= "¡ADVERTENCIA! Ocurrieron errores durante el proceso. Revise los logs del servidor.\\n";
            $final_alert_message .= "Errores en carga_masiva: " . count($errores_insercion_tmp) . ".\\n";
            $final_alert_message .= "Errores al insertar en proveedores: " . count($errores_insercion_proveedores) . ".";
        }
        echo "<script language='javascript'>alert('" . $final_alert_message . "');</script>";

        // 8. LIMPIEZA DE TABLA TMP (`carga_masiva`)
        $mysqli->query('TRUNCATE TABLE carga_masiva;');
        if ($mysqli->errno) {
            error_log("Error al limpiar la tabla carga_masiva al final: " . $mysqli->error);
            echo "<script>console.log('Error al limpiar carga_masiva al final: " . $mysqli->error . "');</script>";
        }
        echo "<script>console.log('Tabla carga_masiva truncada al final del proceso.');</script>";
        
        // Opcional: Redirigir para evitar reenvío de formulario y limpiar la URL
        // header('Location: ' . $_SERVER['PHP_SELF'] . '?menu=proveedores'); // O a la página de lista de proveedores
        // exit();

    } // Fin if (!empty($_POST['carga_masiva']))
} // Fin function subir()
?>