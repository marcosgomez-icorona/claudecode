<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once 'conexiones/conexion.php';
$mysqli = conexion_db();

$errores = [];
$subidos = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivos'])) {
    $carpeta_destino = 'assets/recibos/';
    $archivos = $_FILES['archivos'];
    $total = count($archivos['name']);

    echo "<pre>Total archivos: $total\n\n";

    for ($i = 0; $i < $total; $i++) {
        $nombre = $archivos['name'][$i];
        $tmp = $archivos['tmp_name'][$i];
        $error = $archivos['error'][$i];

        if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) {
            $errores[] = "❌ Error en el archivo '$nombre'. Código de error: $error";
            continue;
        }

        $ruta_destino = $carpeta_destino . basename($nombre);

        // Regex mejoradas para tolerar acentos y caracteres especiales
        $regex_quincenal = "/^(\d+)-(\d{6})-([\w\-]+)-([A-Z]+)-(.+?)-(.+?)\.pdf$/u";
        $regex_mensual = "/^(\d+)-(\d{6})-([\w\-]+)-([A-Z]+)-(.+?)\.pdf$/u";

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

        // Mover archivo
        if (!move_uploaded_file($tmp, $ruta_destino)) {
            $errores[] = "❌ No se pudo mover el archivo: $nombre";
            continue;
        }

        // Insertar en base de datos
        $stmt = $mysqli->prepare("INSERT INTO recibos (recibo, usuario, legajo, periodo, cod_cat, tipo_liquidacion, quincena, detalle, fecha_subida) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        if (!$stmt) {
            $errores[] = "❌ Error en prepare(): " . $mysqli->error;
            continue;
        }

        $stmt->bind_param("sissssss",$nombre, $usuario, $legajo, $periodo, $cod_cat, $tipo_liq, $quincena, $detalle);
        if (!$stmt->execute()) {
            $errores[] = "❌ Error al insertar $nombre: " . $stmt->error;
        } else {
            $subidos[] = "✅ $nombre subido y registrado correctamente.";
        }

        $stmt->close();
    }

    // Mostrar resultados
    if (!empty($errores)) {
        echo "<h4>Errores:</h4><ul>";
        foreach ($errores as $e) echo "<li>$e</li>";
        echo "</ul>";
    }

    if (!empty($subidos)) {
        echo "<h4>Archivos subidos correctamente:</h4><ul>";
        foreach ($subidos as $ok) echo "<li>$ok</li>";
        echo "</ul>";
    }

    echo "</pre>";
} else {
    echo "⚠️ No se recibieron archivos.";
}

$mysqli->close();
?>
