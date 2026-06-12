<?php
$anio='20'.date('y');
        

if(!empty($_POST['accion']) and $_POST['accion']=='alta_periodo' ){
    //agregar_periodo($_POST['periodo'],$_POST['anio']);

}else{
    if(!empty($_POST['accion']) and empty($_POST['periodo']) and empty($_POST['anio'])){
        //echo "<script>alert('Datos Incompletos, intente nuevamente......')</script>";
    }
   
}

//--------------FUNCIONES--------------------------//

function listado_recibos($liq_legajo, $tipo, $busqueda) {
    include_once 'conexiones/conexion.php';
    $mysqli = conexion_db();

    $sql = "SELECT p.id_personal, p.legajo, p.nombre, r.recibo, l.periodo, l.anio, l.tipo_liquidacion, l.tipo_convenio, l.quincena
            FROM personal p 
            LEFT JOIN recibos r ON r.legajo = p.legajo";
    
    $params = [];
    $types = '';
    $conditions = [];

    // Condiciones
    if ($tipo === 'empleados') {
        $conditions[] = "r.id_liquidacion = ?";
        $params[] = $liq_legajo;
        $types .= 'i';
    } elseif ($tipo === 'empleado') {
        $conditions[] = "p.legajo = ?";
        $params[] = $liq_legajo;
        $types .= 's';
    }

    if (!empty($busqueda)) {
        $conditions[] = "(p.nombre LIKE ? OR r.legajo LIKE ?)";
        $search_param = '%' . $busqueda . '%';
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= 'ss';
    }

    // Agrega cláusula WHERE si hay condiciones
    if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    $sql .= " ORDER BY r.legajo ASC, l.anio DESC, l.periodo DESC";
    //echo $sql;
    // Preparar y ejecutar
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        die("Error en prepare: " . $mysqli->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        die("Error al ejecutar la consulta: " . $stmt->error);
    }

    $resultado = $stmt->get_result();

    // Retornar resultado como array asociativo
    $recibos = [];
    while ($fila = $resultado->fetch_assoc()) {
        $recibos[] = $fila;
    }

    $stmt->close();
    return $recibos;
}


?>
<script>
    function ver_recibos(periodo)
    {
        sel = window.open("view.php?opcion=ver_recibos&periodo="+periodo, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }

    function modificar_recibos(periodo,periodo)
    {
        sel = window.open("view.php?opcion=modificar_recibos&periodo="+periodo+"&periodo="+periodo, "popup", "width=850,height=1500,menubar=no,scrollbars=no,toolbar=no,location=yes,directories=no,resizable=no,top=150,left=350");
        
    }
</script>