<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once '../../conexiones/conexion.php';
$mysqli = conexion_db();

$usuario = isset($_GET['usuario']) ? intval($_GET['usuario']) : null;

$errores = [];
$subidos = [];

// Buffer para atrapar salidas inesperadas (no se devuelve en producción si está vacío)
ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivos'])) {
    $carpeta_destino = __DIR__ . '/../../assets/recibos/'; // ajustá según la ubicación real
    if (!is_dir($carpeta_destino)) {
        mkdir($carpeta_destino, 0775, true);
    }

    $archivos = $_FILES['archivos'];
    $total = count($archivos['name']);

    $batch_size = 20;
    for ($start = 0; $start < $total; $start += $batch_size) {
        $end = min($start + $batch_size, $total);
        $mysqli->begin_transaction();
        for ($i = $start; $i < $end; $i++) {
            $nombre = $archivos['name'][$i];
            $tmp = $archivos['tmp_name'][$i];
            $error = $archivos['error'][$i];

            if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) {
                $errores[] = "❌ Error en el archivo '$nombre'. Código de error: $error";
                continue;
            }

            $ruta_destino = $carpeta_destino . basename($nombre);

            // Regex para quincenal / mensual
            $regex_quincenal = "/^(\d+)-(\d{6})-([\w\-]+)-([A-Z]+)-(.+?)-(.+?)\.pdf$/u";
            $regex_mensual  = "/^(\d+)-(\d{6})-([\w\-]+)-([A-Z]+)-(.+?)\.pdf$/u";

            if (stripos($nombre, 'QUINCENAL') !== false) {
                if (!preg_match($regex_quincenal, $nombre, $partes)) {
                    $errores[] = "❌ Formato inválido (Quincenal): $nombre";
                    continue;
                }
                list(, $legajo, $periodo, $cod_cat, $tipo_liq, $quincena, $detalle) = $partes;
                $tipo = 'QUINCENAL';
            } else {
                if (!preg_match($regex_mensual, $nombre, $partes)) {
                    $errores[] = "❌ Formato inválido (Mensual): $nombre";
                    continue;
                }
                list(, $legajo, $periodo, $cod_cat, $tipo_liq, $detalle) = $partes;
                $tipo = 'MENSUAL';
                $quincena = null;
            }

            if (!move_uploaded_file($tmp, $ruta_destino)) {
                $errores[] = "❌ No se pudo mover el archivo: $nombre";
                continue;
            }

            $stmt = $mysqli->prepare("
                INSERT INTO recibos 
                    (recibo, usuario, legajo, periodo, cod_cat, tipo_liquidacion, quincena, detalle, fecha_subida) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            if (!$stmt) {
                $errores[] = "❌ Error en prepare() para $nombre: " . $mysqli->error;
                continue;
            }

            $usuario_val = $usuario !== null ? $usuario : 0;
            $stmt->bind_param("sissssss", $nombre, $usuario_val, $legajo, $periodo, $cod_cat, $tipo_liq, $quincena, $detalle);
            if (!$stmt->execute()) {
                $errores[] = "❌ Error al insertar $nombre: " . $stmt->error;
            } else {
                $subidos[] = "✅ $nombre subido y registrado correctamente.";
            }
            $stmt->close();
        }
        $mysqli->commit();
    }
} else {
    $errores[] = "⚠️ No se recibieron archivos.";
}

$debug_output = trim(ob_get_clean());

$response = [
    'subidos' => $subidos,
    'errores' => $errores
];

// opcional: incluir debug sólo si hay contenido (podés quitar en producción)
if ($debug_output !== '') {
    $response['debug_output'] = $debug_output;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
