<?php
//POST HTTP REQUEST NODE-RED
include_once '../controller/usuarios.php';
include 'recepcion_http_request_post.php';

foreach ($request['data'] as $row) {
    $id_caniero = $row['id_caniero'] ?? '';
    $razon_social = $row['razon_social'] ?? '';
    $cuit = $row['cuit'] ?? '';
    $grupo = $row['grupo'] ?? '';
    
    // Verificar si el CUIT ya existe
    $checkSql = "SELECT id_caniero, razon_social, cuit, grupo FROM canieros WHERE id_caniero = '$id_caniero' LIMIT 1";
    $result = $mysqli->query($checkSql);
    $row_result=$result->fetch_assoc();

    // Si NO existe, lo insertamos
    if ($result && $result->num_rows === 0) {
        $sql = "INSERT INTO canieros (id_caniero, razon_social, cuit, grupo) VALUES ('$id_caniero', '$razon_social','$cuit','$grupo')";
        $mysqli->query($sql);

        // Chequeo usuario
        $checkusuario = $mysqli->prepare("SELECT * FROM usuarios WHERE usuario = ? LIMIT 1");
        $checkusuario->bind_param("s", $CUIT);
        $checkusuario->execute();
        $result = $checkusuario->get_result();
        if ($result && $result->num_rows === 0) {
            alta_usuario($cuit, $cuit, $razon_social,'CANIERO');
        }
        //GENERO NUEVO USUARIO 
        
    }else{            
            if($row_result['razon_social']==$razon_social and $row_result['cuit']<>$cuit ){
                $sql_update = "update canieros SET cuit = '$cuit', grupo = '$grupo' WHERE id_caniero = '$id_caniero' ";               
                $mysqli->query($sql_update);

                //GENERO NUEVO USUARIO CON EL NUEVO CUIT
                alta_usuario($cuit, $cuit, $razon_social,'CANIERO');
            }    
    }
}

$mysqli->close();
echo json_encode(["success" => true, "message" => "Sinc. Canieros OK"]);
