<?php
    function login($username,$password){
       
        // Verificar si se ha enviado el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar credenciales (ejemplo básico, debes mejorar la seguridad)
            include_once 'conexiones/conexion.php';
            $mysqli=conexion_db();

            $sql_empleado = "SELECT dni, legajo, nombre FROM personal WHERE legajo='".$username."' AND tipo='EMPLEADO'";                    
            $empleado=$mysqli->query($sql_empleado) or die(mysqli_error($mysqli)); 
            $dato_empleado=$empleado->fetch_assoc();


            if ($username === $dato_empleado['legajo']) {
                //$_SESSION['user_id'] = 1; // Establecer una variable de sesión (puedes almacenar más información)
                $legajo=$dato_empleado['legajo'];
                $dni=$dato_empleado['dni'];

                echo "<script>validacion_login('$legajo','$dni','$username','$password')</script>";
                exit();
            } else {
                $error_message = 'Credenciales incorrectas';
                return $error_message;
            }
        }
    }

    function login_admin($usuario,$password){
        include_once 'conexiones/conexion.php';
        $mysqli=conexion_db();

        // Supongamos que ya tenés la conexión $mysqli abierta
        $usuario = trim($_POST['usuario']);
        $password = $_POST['password'];

        // Buscar al usuario
        $stmt = $mysqli->prepare("SELECT usuario, password,legajo,tipo FROM usuarios_personal WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $usuario_admin = $row['usuario'] ?? '';
            $pass_admin = $row['password'] ?? ''; // hash
            $tipo = $row['tipo'] ?? '';
            $legajo= $row['legajo'] ?? '';
            $usuario_codificado = base64_encode($legajo);

            // Comparar usuario (opcional si ya lo buscaste) y verificar contraseña
            if (($usuario_admin === $usuario) && password_verify($password, $pass_admin)) {
                if($tipo==='ADMIN'){
                    // La contraseña es correcta                                   
                    echo "  <script>
                                iniciarSesion();
                                window.location.href = 'home.php?menu=carga_recibos&usuario=$usuario';                                
                            </script>";
                }else{
                    if($tipo==='EMPLEADO'){
                        echo "  <script>  
                                        iniciarSesion();                 
                                        window.location.href = 'view.php?accion=ver_recibos_empleado&legajo=".$usuario_codificado."';                                        
                                </script>";
                    }
                }
            } else {
                echo "Usuario o contraseña incorrectos.";
            }
        } else {
            echo "Usuario no encontrado.";
        }

        $stmt->close();
        
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


    function alta_usuario($usuario, $password) {
        include_once 'conexiones/conexion.php';
        $mysqli=conexion_db();
    
        // Encriptar la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
        $sql_usuario = "INSERT INTO usuarios_personal (usuario,password) VALUES ('$usuario','$hashed_password')";                    
        $usuario=$mysqli->query($sql_usuario) or die(mysqli_error($mysqli)); 
    
        if(mysqli_error($mysqli)==null){
           echo '<script>alert("Se creo correctamente...");</script>';
         }
    }

    function cambiar_clave($usuario, $password) {
        include_once 'conexiones/conexion.php';
        $mysqli=conexion_db();
        $usuario = ltrim($usuario, '0');
        $sql_usuarios_personal = "SELECT id_usuario FROM usuarios_personal WHERE usuario='$usuario'";         
        $usuarios_personal=$mysqli->query($sql_usuarios_personal) or die(mysqli_error($mysqli));
        $usuario_db=$usuarios_personal->fetch_assoc();
        $id_usuario=$usuario_db['id_usuario'];

        if(!empty($id_usuario)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql_cambiar_clave = "UPDATE usuarios_personal SET password='$hashed_password' WHERE id_usuario=$id_usuario;";                    
            $mysqli->query($sql_cambiar_clave) or die(mysqli_error($mysqli)); 
        
            if(mysqli_error($mysqli)==null){
               echo "  <script>   
                                alert('Se cambio correctamente...');                     
                                window.location.href = 'index.php';                                        
                        </script>";
             }
        }else{
            echo "  <script>   
                                alert('No se pudo cambiar la contraseña');                                                     
                    </script>";
        }
        // Encriptar la contraseña
        //
            
        
    }

    function generar_usuarios_personal_personal(){
        include_once 'conexiones/conexion.php';
        $mysqli=conexion_db();
        $cant=0;
        $sql_personal = "SELECT dni, legajo FROM personal";                    
        $personal=$mysqli->query($sql_personal) or die(mysqli_error($mysqli)); 
        
        //GENERO usuarios_personal
        while($lista_personal= $personal->fetch_assoc()){
            alta_usuario($lista_personal['legajo'],$lista_personal['dni']);
            $cant++;
        }
        if(mysqli_error($mysqli)==null){
            echo '<script>alert("Se generaron '.$cant.' usuarios_personal  ");</script>';
         } 
    }
?>
<script>
    
    function validacion_login(legajo,dni,username,password) {
            // Aquí colocas la lógica de autenticación, por ejemplo, verificar credenciales
            //var username = document.getElementById('legajo').value;
            //var password = document.getElementById('dni').value;

            if (username === legajo && password===dni) {
                // Redirigir a la página después del inicio de sesión
                window.location.href = 'view.php?accion=ver_recibos_empleado&legajo='+legajo;
                return false; // Evita que el formulario se envíe
            } else {
                alert('No se pudo acceder, intente nuevamente.... ');                
                window.location.href = 'view.php';
                return false; // Evita que el formulario se envíe
            }
        }
        function iniciarSesion() {
                // Lógica para verificar las credenciales del usuario en el servidor
            
                // Si las credenciales son válidas, establecer una cookie
                document.cookie = "sesion=token_unico; path=/";
        }
    

</script>