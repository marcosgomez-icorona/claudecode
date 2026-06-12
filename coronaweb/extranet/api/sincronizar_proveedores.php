<?php
include_once '../controller/usuarios.php';
include 'recepcion_http_request_post.php';

// Configuración
define('API_KEY', 'Corona1234$');
define('MAX_TIME', 300); // 5 minutos máximos
set_time_limit(MAX_TIME);

// Activar logs detallados
error_log("[Proveedores] ===== INICIO PETICIÓN =====");
error_log("[Proveedores] Método: " . $_SERVER['REQUEST_METHOD']);
error_log("[Proveedores] Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'no definido'));

$mysqli->set_charset("utf8mb4");

// Headers para respuesta
header('Content-Type: application/json; charset=utf-8');

// Capturar cualquier error de PHP
function handleError($errno, $errstr, $errfile, $errline) {
    error_log("[Proveedores] PHP Error: [$errno] $errstr in $errfile line $errline");
    return false;
}
set_error_handler('handleError');

try {
    // Log del request recibido (sin datos sensibles)
    error_log("[Proveedores] API Key presente: " . (isset($request['api_key']) ? 'sí' : 'no'));
    error_log("[Proveedores] Batch number: " . ($request['batch_number'] ?? 'no definido'));
    error_log("[Proveedores] Total batches: " . ($request['total_batches'] ?? 'no definido'));
    error_log("[Proveedores] Data count: " . (isset($request['data']) ? count($request['data']) : 'no data'));

    // Validar API Key
    if (!isset($request['api_key']) || $request['api_key'] !== API_KEY) {
        error_log("[Proveedores] ERROR: API Key inválida");
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "error" => "API Key inválida"
        ]);
        exit;
    }

    // Validar datos
    if (!isset($request['data']) || !is_array($request['data'])) {
        error_log("[Proveedores] ERROR: Formato inválido - data no es array");
        echo json_encode([
            "success" => false,
            "error" => "Formato inválido: se esperaba un array en 'data'"
        ]);
        exit;
    }

    // Metadata del batch
    $batch_number  = (int)($request['batch_number'] ?? 0);
    $total_batches = (int)($request['total_batches'] ?? 0);
    $datos_procesar = $request['data'];

    error_log("[Proveedores] Procesando lote $batch_number de $total_batches con " . count($datos_procesar) . " registros");

    // Iniciar transacción
    $mysqli->begin_transaction();

    // Contadores
    $stats = [
        'insertados' => 0,
        'actualizados' => 0,
        'sin_cambios' => 0,
        'usuarios_creados' => 0,
        'errores' => []
    ];

    // Preparar statements
    $checkProv = $mysqli->prepare("SELECT cuit, nombre, domicilio, mail FROM proveedores WHERE cuit = ?");
    $insertProv = $mysqli->prepare("INSERT INTO proveedores (cuit, nombre, domicilio, mail) VALUES (?, ?, ?, ?)");
    $updateProv = $mysqli->prepare("UPDATE proveedores SET nombre = ?, domicilio = ?, mail = ? WHERE cuit = ?");
    $checkUsuario = $mysqli->prepare("SELECT usuario FROM usuarios WHERE usuario = ?");
    
    if (!$checkProv || !$insertProv || !$updateProv || !$checkUsuario) {
        throw new Exception("Error preparando statements: " . $mysqli->error);
    }

    // Procesar cada registro
    foreach ($datos_procesar as $index => $row) {
        try {
            // Validar datos mínimos
            $cuit = trim($row['CUIT'] ?? '');
            $nombre = trim($row['NOMBRE'] ?? '');
            
            if (empty($cuit) || empty($nombre)) {
                $stats['errores'][] = [
                    "index" => $index,
                    "error" => "CUIT o Nombre vacío",
                    "data" => $row
                ];
                continue;
            }

            $domicilio = trim($row['CALLE'] ?? '');
            $mail = trim($row['MAIL'] ?? '');

            // Buscar proveedor existente
            $checkProv->bind_param("s", $cuit);
            $checkProv->execute();
            $result = $checkProv->get_result();
            $provExistente = $result->fetch_assoc();

            if (!$provExistente) {
                // INSERT
                $insertProv->bind_param("ssss", $cuit, $nombre, $domicilio, $mail);
                if ($insertProv->execute()) {
                    $stats['insertados']++;
                } else {
                    throw new Exception("Error al insertar: " . $insertProv->error);
                }
            } else {
                // Verificar cambios
                if ($provExistente['nombre'] !== $nombre || 
                    $provExistente['domicilio'] !== $domicilio || 
                    $provExistente['mail'] !== $mail) {
                    
                    $updateProv->bind_param("ssss", $nombre, $domicilio, $mail, $cuit);
                    if ($updateProv->execute()) {
                        $stats['actualizados']++;
                    } else {
                        throw new Exception("Error al actualizar: " . $updateProv->error);
                    }
                } else {
                    $stats['sin_cambios']++;
                }
            }

            // Crear usuario si no existe
            if (!empty($cuit)) {
                $checkUsuario->bind_param("s", $cuit);
                $checkUsuario->execute();
                $resUser = $checkUsuario->get_result();

                if ($resUser->num_rows === 0) {
                    if (function_exists('alta_usuario')) {
                        alta_usuario($cuit, $cuit, $nombre, 'PROVEEDOR');
                        $stats['usuarios_creados']++;
                    }
                }
            }

        } catch (Exception $e) {
            $stats['errores'][] = [
                "cuit" => $row['CUIT'] ?? 'N/A',
                "nombre" => $row['NOMBRE'] ?? 'N/A',
                "error" => $e->getMessage()
            ];
        }
    }

    // Commit
    $mysqli->commit();

    // Cerrar statements
    $checkProv->close();
    $insertProv->close();
    $updateProv->close();
    $checkUsuario->close();

    // Respuesta exitosa
    $respuesta = [
        "success" => true,
        "batch_number" => $batch_number,
        "total_batches" => $total_batches,
        "procesados" => count($datos_procesar),
        "insertados" => $stats['insertados'],
        "actualizados" => $stats['actualizados'],
        "sin_cambios" => $stats['sin_cambios'],
        "usuarios_creados" => $stats['usuarios_creados'],
        "errores" => $stats['errores'],
        "timestamp" => date('Y-m-d H:i:s')
    ];

    error_log("[Proveedores] Lote $batch_number completado: {$stats['insertados']} insertados, {$stats['actualizados']} actualizados");
    
    $json_response = json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    error_log("[Proveedores] Respuesta JSON: " . $json_response);
    echo $json_response;

} catch (Exception $e) {
    // Rollback
    if (isset($mysqli)) {
        $mysqli->rollback();
    }
    
    error_log("[Proveedores] ERROR: " . $e->getMessage());
    
    http_response_code(500);
    $error_response = json_encode([
        "success" => false,
        "error" => "Error: " . $e->getMessage(),
        "batch_number" => $batch_number ?? 0
    ]);
    echo $error_response;
    
} finally {
    if (isset($mysqli)) {
        $mysqli->close();
    }
    error_log("[Proveedores] ===== FIN PETICIÓN =====\n");
}
?>