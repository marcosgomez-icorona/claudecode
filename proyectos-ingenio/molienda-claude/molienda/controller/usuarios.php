<?php

    function login_admin($usuario,$password){
        include_once 'conexiones/conexion.php';
        $mysqli=conexion_db();

        $sql_usuario = "SELECT usuario, password,tipo FROM usuarios WHERE usuario='".$usuario."' ORDER BY tipo ASC ";  
        //echo $sql_usuario;
        $resultado_usuario=$mysqli->query($sql_usuario) or die(mysqli_error($mysqli)); 
        $dato_usuario=$resultado_usuario->fetch_assoc();        
        $usuario_admin=$dato_usuario['usuario'] ?? '';        
        $pass_admin=$dato_usuario['password'] ?? '';        
        $tipo=$dato_usuario['tipo'] ?? '';        
        //Se codifica el el GET en base64
        $usuario_codificado = base64_encode($usuario);
        //echo $tipo;
            if (($usuario_admin==$usuario) and password_verify($password, $pass_admin)) {
                
                if($tipo==='ADMIN'){
                    // La contraseña es correcta                                   
                    echo "  <script>
                                //iniciarSesion();
                                window.location.href = 'home.php?menu=admin&usuario=$usuario_codificado'
                            </script>";
                }else{
                    
                    if($tipo=='CANIERO'){                        
                        // La contraseña es correcta                                                          
                        echo "  <script>
                                    //iniciarSesion();
                                    window.location.href = 'home.php?menu=canieros&usuario=$usuario_codificado'
                                </script>";
                    }                       
                    if($tipo==='PROVEEDOR'){
                        // La contraseña es correcta                                   
                        
                        echo "  <script>
                                    //iniciarSesion();
                                    window.location.href = 'home.php?menu=proveedores&usuario=$usuario_codificado'
                                </script>";
                    }                    
                   
                }
                
            }else {
                // La contraseña es incorrecta
                echo '<script>alert("Usuario y/o Contraseña no valido...");</script>';
            }
        
        }

    function encriptar_valor($texto_original){
        $clave = "mi_clave_secreta"; // Clave secreta para el cifrado

        // Encriptar el texto
        $texto_encriptado = openssl_encrypt($texto_original, 'aes-256-cbc', $clave, 0, '1234567812345678');
        //echo "Texto encriptado: $texto_encriptado <br>";
        return $texto_encriptado;        
    }

    function desencriptar_valor($texto_encriptado){
        $clave = "mi_clave_secreta"; // Clave secreta para el cifrado
        
        // Desencriptar el texto
        $texto_desencriptado = openssl_decrypt($texto_encriptado, 'aes-256-cbc', $clave, 0, '1234567812345678');
        //echo "Texto desencriptado: $texto_desencriptado";
        return $texto_desencriptado;
    }

    
    function alta_usuario($usuario, $password,$nombre,$tipo) {
        include_once '../conexiones/conexion.php';
        $mysqli=conexion_db();
    
        // Encriptar la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
        $sql_usuario = "INSERT INTO usuarios (usuario,password,nombre,tipo) VALUES ('$usuario','$hashed_password','$nombre','$tipo')";                    
        $usuario=$mysqli->query($sql_usuario) or die(mysqli_error($mysqli)); 
    
        if(mysqli_error($mysqli)==null){
           echo '<script>alert("Se creo correctamente...");</script>';
         }
    }

    function cambiar_clave($usuario, $password) {
        include_once 'conexiones/conexion.php';
        $mysqli=conexion_db();
        
        $sql_usuarios = "SELECT id_usuario FROM usuarios WHERE usuario='$usuario'";                    
        $usuarios=$mysqli->query($sql_usuarios) or die(mysqli_error($mysqli));
        $usuario_db=$usuarios->fetch_assoc();
        $id_usuario=$usuario_db['id_usuario'];

        if(!empty($id_usuario)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql_cambiar_clave = "UPDATE usuarios SET password='$hashed_password' WHERE usuario=$usuario;";                    
            $mysqli->query($sql_cambiar_clave) or die(mysqli_error($mysqli)); 
        
            if(mysqli_error($mysqli)==null){
               echo "  <script>   
                                alert('Se cambio correctamente...');                     
                                window.location.href = 'index.php';                                        
                        </script>";
             }
        }
        // Encriptar la contraseña
        //
    }

    function tipo_usuario($usuario){
        include_once 'conexiones/conexion.php';
        $mysqli=conexion_db();
        $sql_usuario = "SELECT tipo FROM usuarios WHERE usuario='$usuario';";         
        $resultado_usuario = $mysqli->query($sql_usuario) or die(mysqli_error($mysqli));
        $row_resultado_usuario = $resultado_usuario->fetch_assoc();
        $tipo_usuario=$row_resultado_usuario['tipo'];
        
        return $tipo_usuario;
    }
    
    function sync_usuarios_caniero() {
        include_once 'conexiones/conexion.php';
        $mysqli=conexion_db();
        
        //$sql_usuarios = "SELECT id_caniero, razon_social nombre, cuit, grupo FROM canieros ORDER BY razon_social ASC;";                    
        $sql_usuarios = "SELECT id_caniero, razon_social, cuit, grupo FROM canieros where razon_social like 'SUCROALCOHOLERA DEL SUR S.A.' ORDER BY razon_social ASC;";                    
        $usuarios=$mysqli->query($sql_usuarios) or die(mysqli_error($mysqli));
        
        while($usuario_db=$usuarios->fetch_assoc()) {
            $usuario=$usuario_db['cuit'];
            $pass=$usuario_db['cuit'];
            $nombre=$usuario_db['nombre'];
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
            $tipo='CANIERO2';
            $sql_seteo_clave = "insert into usuarios (usuario,password,nombre,tipo) VALUES ('$usuario','$hashed_password','$nombre','$tipo');";   
            //echo $sql_seteo_clave;                 
            $mysqli->query($sql_seteo_clave) or die(mysqli_error($mysqli));            
            
        }
        if(mysqli_error($mysqli)==null){
            echo "  <script>   
                             alert('ok');                     
                             window.location.href = 'index.php';                                        
                     </script>";
          }
        // Encriptar la contraseña
        //
    }

    function seteo_masivo_clave() {
        include_once 'conexiones/conexion.php';
        $mysqli=conexion_db();
        
        $sql_usuarios = "select cuit,nombre FROM proveedores ";                    
        $usuarios=$mysqli->query($sql_usuarios) or die(mysqli_error($mysqli));
        
        while($usuario_db=$usuarios->fetch_assoc()) {
            $usuario=$usuario_db['cuit'];
            $pass=$usuario_db['cuit'];
            $nombre=$usuario_db['nombre'];
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
            $tipo='PROVEEDOR';
            $sql_seteo_clave = "insert into usuarios (usuario,password,nombre,tipo) VALUES ('$usuario','$hashed_password','$nombre','$tipo');";   
            //echo $sql_seteo_clave;                 
            $mysqli->query($sql_seteo_clave) or die(mysqli_error($mysqli));            
            
        }
        if(mysqli_error($mysqli)==null){
            echo "  <script>   
                             alert('ok');                     
                             window.location.href = 'index.php';                                        
                     </script>";
          }
        // Encriptar la contraseña
        //
    }

    function nombre_usuario($usuario){
        include_once 'conexiones/conexion.php';
        $mysqli=conexion_db();
        $sql_usuario = "SELECT nombre FROM usuarios WHERE usuario='$usuario';";         
        $resultado_usuario = $mysqli->query($sql_usuario) or die(mysqli_error($mysqli));
        $row_resultado_usuario = $resultado_usuario->fetch_assoc();
        $nombre_usuario=$row_resultado_usuario['nombre'];
        
        return $nombre_usuario;
    }

    function generar_usuarios_personal(){
        include_once 'conexiones/conexion.php';
        $mysqli=conexion_db();
        $cant=0;
        $sql_personal = "SELECT dni, legajo FROM personal";                    
        $personal=$mysqli->query($sql_personal) or die(mysqli_error($mysqli)); 
        
        //GENERO USUARIOS
        while($lista_personal= $personal->fetch_assoc()){
            alta_usuario($lista_personal['legajo'],$lista_personal['dni'],'OPERADOR');
            $cant++;
        }
        if(mysqli_error($mysqli)==null){
            echo '<script>alert("Se generaron '.$cant.' usuarios  ");</script>';
         } 
    }
?>
