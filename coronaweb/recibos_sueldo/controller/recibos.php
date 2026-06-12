<?php
$anio='20'.date('y');
        

if(!empty($_POST['accion']) and $_POST['accion']=='alta_periodo' ){
    //agregar_periodo($_POST['periodo'],$_POST['anio']);

}else{
    if(!empty($_POST['accion']) and empty($_POST['periodo']) and empty($_POST['anio'])){
        //echo "<script>alert('Datos Incompletos, intente nuevamente......')</script>";
    }
   
}

//PARA CONFORMIDAD DEL RECIBO
if (!empty($_POST['accion']) and $_SERVER['REQUEST_METHOD'] === 'POST' and $_POST['accion'] === 'conformidad' ) {
    $id = intval($_POST['id'] ?? 0);
    if ($id > 0) {
        conformidad($id); // Ejecuta la lógica que necesites
        echo json_encode(['status' => 'ok']);
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'ID inválido']);
    }
    exit;
}

//--------------FUNCIONES--------------------------//

function listado_recibos($liq_legajo, $tipo, $busqueda) {
    include_once 'conexiones/conexion.php';
    $mysqli = conexion_db();

    $sql = "SELECT id_recibo,recibo,periodo, r.quincena, r.tipo_liquidacion,r.detalle, r.fecha_subida,p.id_personal, p.idempleado,
            p.dni, ABS(p.legajo) as legajo, p.nombre, r.cod_cat,p.tipo_convenio,p.tipo_liquidacion as forma_liquidacion ,p.tipo_liquidacion AS 'forma_liquidacion', p.puesto, p.empresa,r.conformidad,r.fecha_conformidad
            FROM personal p LEFT JOIN recibos r ON ABS(r.legajo) = ABS(p.legajo)
            where r.legajo=$liq_legajo ";
    
    if(!empty($busqueda)){
        $sql .= $busqueda;
    }
    $sql .= "ORDER BY r.periodo DESC";
    
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

    //echo $sql;
    $stmt->close();
    return $recibos;
}

function estado_carga_recibos() {
    include_once 'conexiones/conexion.php';
    $mysqli = conexion_db();

    $sql = "SELECT SUBSTRING(periodo,1,4) AS ANIO,
                SUBSTRING(periodo,5,7) AS MES, 
                tipo_liquidacion,
                COUNT(legajo) as cant_cargados
            FROM recibos
            GROUP BY periodo, tipo_liquidacion
            ORDER BY SUBSTRING(periodo,1,4) desc,
                SUBSTRING(periodo,5,7) desc, 
                tipo_liquidacion asc";
    
    // Preparar y ejecutar
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        die("Error en prepare: " . $mysqli->error);
    }
    
    // Ejecutar la consulta
    $stmt->execute();
    
    // Obtener el resultado
    $resultado = $stmt->get_result();

    // Retornar resultado como array asociativo
    $estado_carga = [];
    while ($fila = $resultado->fetch_assoc()) {
        $estado_carga[] = $fila;
    }

    // Cerrar recursos
    $stmt->close();
    $mysqli->close();
    
    return $estado_carga;
}

function conformidad($id){
    include_once '../conexiones/conexion.php';    

    $mysqli = conexion_db();
    if ($mysqli->connect_errno) {
        error_log("Error al conectar con la base de datos: " . $mysqli->connect_error);
        return null; // Retorna null si la conexión falla
    }

    $check= "select conformidad FROM recibos where id_recibo= $id; ";    
    $stmt = $mysqli->prepare($check);
    $stmt->execute();
    $result = $stmt->get_result(); 
    $row = $result->fetch_assoc();
    $control_conformdad = $row['conformidad'];
    
    //CONTROLO SI YA REALIZO LA CONFORMIDAD PARA NO CAMBIAR LA FECHA
    if($control_conformdad<>'S'){
        $sql= "update recibos SET conformidad = 'S', fecha_conformidad = NOW() WHERE id_recibo = $id; ";    
        $stmt = $mysqli->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();    
        
    }
   
}

function listado_cargas($busqueda) {
    include_once 'conexiones/conexion.php';    
    $mysqli = conexion_db();

    if (!empty($busqueda)) {

        $where = " WHERE CONCAT(periodo, quincena, tipo_liquidacion, detalle) LIKE '%$busqueda%' OR fecha_subida LIKE '%$busqueda%' ";
        
    }else{
        $where = '';
    }

    $sql = "SELECT CONCAT(periodo,'-', quincena,'-', tipo_liquidacion,'-', detalle) 'carga', fecha_subida,periodo, quincena, tipo_liquidacion, detalle
            FROM recibos
            $where
            GROUP BY periodo, quincena, tipo_liquidacion, detalle
            ORDER BY fecha_subida DESC;";
    //echo $sql;
    $stmt = $mysqli->prepare($sql);    
    $stmt->execute();
    return $stmt->get_result();
}

function listado_cargas_asociadas_recibo($busqueda) {
    include_once 'conexiones/conexion.php';    
    $mysqli = conexion_db();

    if (!empty($busqueda)) {

        $where = "WHERE CONCAT(r.periodo, r.quincena, r.tipo_liquidacion, r.detalle) LIKE '%$busqueda%' ";
        
    }else{
        $where = '';
    }

    $sql = "SELECT r.id_recibo,r.recibo,r.periodo,p.nombre
            FROM personal p INNER JOIN recibos r ON r.legajo = p.legajo
            $where 
            GROUP BY r.id_recibo,r.recibo,r.periodo           
            ORDER BY p.nombre ASC;";
    //echo $sql;
    $stmt = $mysqli->prepare($sql);    
    $stmt->execute();
    return $stmt->get_result();
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