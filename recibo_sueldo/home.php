<!DOCTYPE html>
	<head>
		<title>Liquidaciones y Recibos</title>
        
        <link rel="stylesheet" href="css/bootstrap.min.css">
        
        <link rel="stylesheet" href="css/menu.css">
        <link rel="stylesheet" href="css/styles.css">
        <link rel="stylesheet" href="css/jquery-ui.css">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="js/jquery.min.js"></script>  
        <script src="js/bootstrap.js" ></script>
        <script src="js/jquery.js"></script>
        <script src="js/jquery-ui.js"></script>
        
      <script src="js/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
      <script src="js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    
    <!-- Busquedor dentro de un Select  -->
    <link rel="stylesheet" href="css/bootstrap-select.min.css">        
    <script src="js/bootstrap-select.min.js"></script>
    <script>
    window.onload = function() {
        if (verificarSesion()) {
            // El usuario está autenticado, realizar acciones necesarias

            //SE HABILIA LA VISUALIZACION DEL MENU Y EL CONTENIDO
            var div_menu = document.getElementById("div_menu");            
            div_menu.style.display = "inline";
            var div_contenido = document.getElementById("div_contenido");                        
            div_contenido.style.display = "inline";
            
            //console.log("Usuario autenticado");
        } else {
            // El usuario no está autenticado, realizar acciones necesarias
            // Seleccionar el div por su id
            window.location.href = 'index.php';
            alert('Debe loguearse para entrar al Sistema....')
            close();  
        }
    };

    function verificarSesion() {
        // Obtener todas las cookies
        const cookies = document.cookie.split("; ");

        // Buscar la cookie de sesión
        for (const cookie of cookies) {
            const [nombre, valor] = cookie.split("=");
            if (nombre === "sesion") {
                // Verificar el valor de la cookie (puede ser un token u otro identificador)
                return valor === "token_unico"; // Reemplaza "token_unico" con tu lógica de autenticación
            }
        }

        return false; // La cookie de sesión no está presente
    }

    function cerrarSesion() {
            // Borrar la cookie estableciendo su fecha de caducidad en el pasado
            document.cookie = "sesion=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";

            // Redirigir al usuario a la página de inicio de sesión o a otra página
            window.location.href = "index.php";
    }
</script>
    <style type="text/css">
		html,body{
            margin: 3px; padding: 3px;
            Font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }
        .table{
                overflow: scroll;
                font-size: 12px;
                
        }

        .container {
                    padding-left: 2% !important;
                    padding-right: 2% !important;
                    width: 100% !important;
        }
       
	</style>
	</head>
	<body>
        <div id="div_menu" style="display: none;" class="box shadow bg-body rounded row justify-content-center">
            <div class="container">
                <div  class="row justify-content-center"> 
                    <div class="col-4">
                        <img src="assets/img/Logo-Ing La Corona.png" width="80px" alt="">
                    </div>
                    <div class="col-8">
                        <div id="header">
                            <nav class="navbar">
                                    <ul class="nav-menu nav-center m-2">
                                        <li ><a href="">Liquidaciones</a>
                                            <ul>
                                                <li><a href="home.php?menu=ver_liquidaciones">Ver Liquidaciones</a></li>                                    
                                            </ul>
                                        </li>                                
                                        <li ><a href="">Personal</a>
                                            <ul>
                                                <li><a href="home.php?menu=ver_personal">Listado del Personal</a></li>
                                                
                                            </ul>
                                        </li>  
                                        <li ><a href="">Mantenimiento</a>
                                            <ul>
                                                <li><a href="home.php?menu=alta_usuario">Alta de Usuario</a></li>
                                                <li><a href="home.php?menu=cambiar_clave&usuario=<?php echo $_GET['usuario'];?>">Cambiar clave de Usuario</a></li>
                                                <li><a href="home.php?menu=generacion_masiva_usuarios">Generacion Masiva de Usuarios</a></li>
                                            </ul>
                                        </li>                                
                                        <li><a href="javascript: cerrarSesion();">Salir</a></li>
                                    </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
        <div id="div_contenido" style="display: none;" class="box shadow p-3 mb-5 bg-body rounded row justify-content-center">
                
                    <?php 
                    //--------MENU-----------------------//
               
                        //--------liquidaciones-----------------------//  

                        if(!empty($_GET['menu']) and $_GET['menu']=='ver_liquidaciones'){
                            include 'views/liquidaciones/index.php';
                        } 

                        if(!empty($_GET['menu']) and $_GET['menu']=='agregar_liquidacion'){
                            include 'views/liquidaciones/create.php';
                        }

                        if(!empty($_GET['accion']) and $_GET['accion']=='eliminar_liquidacion'){
                            include 'controller/liquidaciones.php';
                        }

                         //----------Recibos-------------//

                        if(!empty($_GET['menu']) and $_GET['menu']=='ver_recibos'){
                            include 'views/recibos/index.php';
                        }

                        if(!empty($_GET['menu']) and $_GET['menu']=='validacion_recibo_empleado'){
                            include 'views/usuarios/login.php';
                        }

                        if(!empty($_GET['accion']) and $_GET['accion']=='ver_recibos_empleado'){
                            include 'views/recibos/form.php';
                        }

                       //----------Personal-------------//

                        if(!empty($_GET['menu']) and $_GET['menu']=='ver_personal'){
                            include 'views/personal/index.php';
                        }
                        if(!empty($_GET['menu']) and $_GET['menu']=='agregar_persona'){
                            include 'views/personal/create.php';
                        }
                        if(!empty($_GET['menu']) and $_GET['menu']=='modificar_persona'){
                            include 'views/personal/edit.php';
                        }

                        //-------------USUARIOS----------//

                        if(!empty($_GET['menu']) and $_GET['menu']=='alta_usuario'){
                            include 'views/usuarios/create.php';
                        }                        

                        if(!empty($_GET['menu']) and $_GET['menu']=='cambiar_clave'){
                            include 'views/usuarios/cambiar_clave.php';
                        }
                        if(!empty($_GET['menu']) and $_GET['menu']=='generacion_masiva_usuarios'){
                            //include 'views/usuarios/generacion_masiva_usuarios.php';
                        }

                        
                        //----ACCIONES----------------------//
                        
                        /*
                        if(!empty($_GET['accion']) and $_GET['accion']=='agregar_analisis'){
                            include 'views/analisis_vibraciones/create_busqueda_avanzada.php';
                        }
                        */
                        //---------------------//

                        
                            //--------Administracion de Sistema-----------------------//

                            if(!empty($_GET['menu']) and $_GET['menu']=='usuarios'){
                                include 'views/usuarios/index.php';
                            }

                            if(!empty($_GET['menu']) and $_GET['menu']=='tipos_usuario'){
                                include 'views/tipos_usuario/index.php';
                            }



                            //-----------------------------------//

                            
                        //----TIPOS DE USUARIO------//
                        if(!empty($_GET['accion']) and $_GET['accion']=='agregar_tipo_usuario'){
                            include 'views/tipos_usuario/create.php';
                        }
                        if(!empty($_GET['accion']) and $_GET['accion']=='modificar_tipo_usuario'){
                            include 'views/tipos_usuario/edit.php';
                        }

                        //---------------------//



                        //------------POR DEFECTO---------------------//
                        if(empty($_GET['menu']) and empty($_GET['accion'])){
                            include 'views/liquidaciones/index.php';
                        }
                
                    ?>
        </div>       
       
</body>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</html>