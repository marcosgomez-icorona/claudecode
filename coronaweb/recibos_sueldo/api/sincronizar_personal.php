<?php
//POST HTTP REQUEST NODE-RED
//include_once '../controller/usuarios.php';
include 'recepcion_http_request_post.php';

foreach ($request['data'] as $row) {
    $idempleado = $row['id_empleado'] ?? '';
    $nombre = $row['nombre'] ?? '';
    $dni = $row['dni'] ?? '';
    $legajo = $row['legajo'] ?? '';
    $cod_cat = $row['cod_cat'] ?? '';
    $tipo_convenio = $row['tipo_convenio'] ?? '';
    $puesto = $row['puesto'] ?? '';
    $tipo_liquidacion = $row['tipo_liquidacion'] ?? '';
    $empresa = $row['empresa'] ?? '';
    
    // Verificar si el CUIT ya existe
    $checkSql = "SELECT id_personal, idempleado, dni, legajo, nombre, cod_cat, tipo_convenio, tipo_liquidacion, puesto, empresa
                 FROM personal WHERE idempleado = '$idempleado' LIMIT 1";
    $result = $mysqli->query($checkSql);
    $row_result=$result->fetch_assoc();

    // Si NO existe, lo insertamos
    if ($result && $result->num_rows === 0) {
        //INSERTO EMPLEADO
        $sql = "INSERT INTO personal (idempleado,dni,legajo,nombre,cod_cat,tipo_convenio,tipo_liquidacion,puesto ,empresa) 
                VALUES ('$idempleado', '$dni','$legajo','$nombre','$cod_cat','$tipo_convenio','$tipo_liquidacion','$puesto','$empresa')";
        $mysqli->query($sql);
        
        //GENERO NUEVO USUARIO                
        $nro_legajo = ltrim($legajo, '0');
        $hashed_password = password_hash($dni, PASSWORD_DEFAULT);            
        $sql_usuario = "INSERT INTO usuarios_personal (usuario,password,legajo,tipo) VALUES ('$nro_legajo','$hashed_password','$legajo','EMPLEADO')";                    
        $usuario=$mysqli->query($sql_usuario) or die(mysqli_error($mysqli)); 
    
    }else{            
            if($row_result['legajo']===$legajo and $row_result['cod_cat']<>$cod_cat){
                $sql_update = " UPDATE personal SET cod_cat = '$cod_cat' 
                                WHERE legajo = '$legajo' ";               
                $mysqli->query($sql_update);
                
            }    
            if($row_result['legajo']===$legajo and $row_result['tipo_convenio']<>$tipo_convenio){
                $sql_update = " UPDATE personal SET tipo_convenio = '$tipo_convenio'
                                WHERE legajo = '$legajo' ";               
                $mysqli->query($sql_update);
                
            }    
            if($row_result['legajo']===$legajo and $row_result['tipo_liquidacion']<>$tipo_liquidacion){
                $sql_update = " UPDATE personal SET tipo_liquidacion = '$tipo_liquidacion'
                                WHERE legajo = '$legajo' ";               
                $mysqli->query($sql_update);
                
            }    
    }
}

$mysqli->close();
echo json_encode(["success" => true, "message" => "Sinc. personal OK"]);
