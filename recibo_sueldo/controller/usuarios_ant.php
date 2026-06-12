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

        $sql_usuario = "SELECT usuario, password,tipo FROM usuarios WHERE usuario='".$usuario."' ";  
        //echo $sql_usuario;
        $resultado_usuario=$mysqli->query($sql_usuario) or die(mysqli_error($mysqli)); 
        $dato_usuario=$resultado_usuario->fetch_assoc();        
        $usuario_admin=$dato_usuario['usuario'];        
        $pass_admin=$dato_usuario['password'];
        $tipo=$dato_usuario['tipo'];
                
            if (($usuario_admin==$usuario) and password_verify($password, $pass_admin)) {
                if($tipo==='ADMIN'){
                    // La contraseña es correcta                                   
                    echo "  <script>
                                iniciarSesion();
                                window.location.href = 'home.php?menu=ver_liquidaciones&usuario=$usuario'
                            </script>";
                }else{
                    if($tipo==='EMPLEADO'){
                        echo "  <script>                   
                                        window.location.href = 'view.php?accion=ver_recibos_empleado&legajo='+$usuario;                                        
                                </script>";
                    }
                }
                
            }else {
                // La contraseña es incorrecta
                echo '<script>alert("Usuario y/o Contraseña no valido...");</script>';
            }
        
        }

    function alta_usuario($usuario, $password) {
        include_once 'conexiones/conexion.php';
        $mysqli=conexion_db();
    
        // Encriptar la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
        $sql_usuario = "INSERT INTO usuarios (usuario,password) VALUES ('$usuario','$hashed_password')";                    
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
            $sql_cambiar_clave = "UPDATE usuarios SET password='$hashed_password' WHERE id_usuario=$id_usuario;";                    
            $mysqli->query($sql_cambiar_clave) or die(mysqli_error($mysqli)); 
        
            if(mysqli_error($mysqli)==null){
               echo "  <script>   
                                alert('Se cambio correctamente...');                     
                                window.location.href = 'view.php?accion=ver_recibos_empleado&legajo='+$usuario;                                        
                        </script>";
             }
        }
        // Encriptar la contraseña
        //
            
        
    }

    function generar_usuarios_personal(){
        include_once 'conexiones/conexion.php';
        $mysqli=conexion_db();
        $cant=0;
        $sql_personal = "SELECT dni, legajo FROM personal";                    
        $personal=$mysqli->query($sql_personal) or die(mysqli_error($mysqli)); 
        
        //GENERO USUARIOS
        while($lista_personal= $personal->fetch_assoc()){
            alta_usuario($lista_personal['legajo'],$lista_personal['dni']);
            $cant++;
        }
        if(mysqli_error($mysqli)==null){
            echo '<script>alert("Se generaron '.$cant.' usuarios  ");</script>';
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