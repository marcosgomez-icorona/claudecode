<?php
include_once 'conexiones/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    switch ($accion) {
        case 'alta_persona':
            agregar_persona(
                $_POST['legajo'] ?? '',
                $_POST['dni'] ?? '',
                $_POST['nombre'] ?? '',
                $_POST['tipo_convenio'] ?? '',
                $_POST['tipo_liquidacion'] ?? ''
            );
            break;

        case 'modificar_persona':
            modificar_datos_persona(
                $_GET['id_personal'] ?? null,
                $_POST['legajo'] ?? '',
                $_POST['dni'] ?? '',
                $_POST['nombre'] ?? '',
                $_POST['tipo_convenio'] ?? '',
                $_POST['tipo_liquidacion'] ?? ''
            );
            break;
    }
}

// --------------- FUNCIONES ---------------- //

function listado_personal($busqueda) {
    $mysqli = conexion_db();

    $sql = "SELECT id_personal, dni, legajo, nombre, tipo_convenio, tipo_liquidacion
            FROM personal";

    $params = [];
    $types = '';

    if (!empty($busqueda)) {
        $sql .= " WHERE nombre LIKE ? OR legajo LIKE ? OR dni LIKE ? OR tipo_convenio LIKE ? OR tipo_liquidacion LIKE ?";
        $busqueda = '%' . $busqueda . '%';
        $params = [$busqueda, $busqueda, $busqueda, $busqueda, $busqueda];
        $types = 'sssss';
    }

    $sql .= " ORDER BY legajo ASC";

    $stmt = $mysqli->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

function agregar_persona($legajo, $dni, $nombre, $tipo_convenio, $tipo_liquidacion) {
    $mysqli = conexion_db();

    if (!$legajo || !$dni || !$nombre || !$tipo_convenio || !$tipo_liquidacion) {
        echo '<script>alert("Datos incompletos");</script>';
        return;
    }

    $stmt = $mysqli->prepare("INSERT INTO personal (legajo, dni, nombre, tipo_convenio, tipo_liquidacion) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $legajo, $dni, $nombre, $tipo_convenio, $tipo_liquidacion);

    if ($stmt->execute()) {
        echo '<script>alert("Se generó correctamente...");</script>';
    } else {
        echo '<script>alert("Error al insertar: ' . $stmt->error . '");</script>';
    }
    $stmt->close();
}

function busca_persona($id_personal) {
    $mysqli = conexion_db();

    $stmt = $mysqli->prepare("SELECT id_personal, dni, legajo, nombre, tipo_convenio, tipo_liquidacion FROM personal WHERE id_personal = ?");
    $stmt->bind_param("i", $id_personal);
    $stmt->execute();

    $resultado = $stmt->get_result();
    return $resultado->fetch_assoc();
}

function modificar_datos_persona($id_personal, $legajo, $dni, $nombre, $tipo_convenio, $tipo_liquidacion) {
    $mysqli = conexion_db();

    if (empty($id_personal)) {
        echo '<script>alert("ID inválido");</script>';
        return;
    }

    $stmt = $mysqli->prepare("UPDATE personal SET dni = ?, legajo = ?, nombre = ?, tipo_convenio = ?, tipo_liquidacion = ? WHERE id_personal = ?");
    $stmt->bind_param("sssssi", $dni, $legajo, $nombre, $tipo_convenio, $tipo_liquidacion, $id_personal);

    if ($stmt->execute()) {
        echo '<script>alert("Se modificó correctamente...");</script>';
    } else {
        echo '<script>alert("Error al modificar: ' . $stmt->error . '");</script>';
    }

    $stmt->close();
}
?>
