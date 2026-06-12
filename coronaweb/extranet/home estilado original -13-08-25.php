<!DOCTYPE html>
	<head>
		<title>Mantenimiento</title>
                
        <link rel="stylesheet" href="ccs/bootstrap.css">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        
        <link rel="stylesheet" href="css/menu.css">
        <link rel="stylesheet" href="css/styles.css">
        <link rel="stylesheet" href="css/jquery-ui.css">
        
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

    <!-- Datapiker para calendario de fechas  
    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <script src="js/bootstrap-datepicker.js"></script>
    -->		
        <style type="text/css">
		    html,body{
            margin: 0; padding: 0;
            Font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }
        .table{
                overflow: scroll;
                font-size: 12px;
                
        }

        .container {
                    padding-left: 0 !important;
                    padding-right: 0 !important;
                    width: 100% !important;
        }
       
		</style>
	</head>
	<body>
    <div id="div_menu" style="display: none;" class="box shadow bg-body rounded row justify-content-center">
        <div class="row justify-content-center"> 
            <div class="box shadow p-1 mb-1 bg-body rounded">
                <div id="header">
                    <nav class="navbar">
                            <ul class="nav-menu nav-center">
                                <li ><a href="">Equipos</a>
                                    <ul>
                                    <li><a href="home.php?menu=ver_equipos&usuario=<?php if(!empty($_GET['usuario'])) echo $_GET['usuario'];?>">Ver Equipos</a></li>                                    
                                    </ul>
                                </li>
                                <li ><a href="">Mantenimiento</a>
                                    <ul>
                                        <li><a href="home.php?menu=ver_ordenes_trabajo&usuario=<?php if(!empty($_GET['usuario'])) echo $_GET['usuario'];?>">Ver Ordenes de Trabajo</a></li>
                                    </ul>
                                </li>
                                <li ><a href="">Deposito</a>
                                    <ul>                                        
                                        <li><a href="home.php?menu=ver_pendientes_entrega&usuario=<?php if(!empty($_GET['usuario'])) echo $_GET['usuario'];?>">Ver Pendientes</a></li>
                                        <li><a href="home.php?menu=ver_componentes_equipos&usuario=<?php if(!empty($_GET['usuario'])) echo $_GET['usuario'];?>">Listado de Insumos y Repuestos de Equipos</a></li>
                                    </ul>
                                </li>
                                <li ><a href="">Vibraciones</a>
                                    <ul>
                                        <li><a href="home.php?menu=analisis_vibraciones&usuario=<?php if(!empty($_GET['usuario'])) echo $_GET['usuario'];?>">Analisis de Vibraciones</a></li>                                    
                                    </ul>
                                </li>
                                <li ><a href="">Alertas</a>
                                    <ul>
                                        <li><a href="home.php?menu=ver_alertas&usuario=<?php if(!empty($_GET['usuario'])) echo $_GET['usuario'];?>">Ver Alertas</a></li>                                    
                                    </ul>
                                </li>
                                <li ><a href="">Sistema</a>
                                    <ul>
                                        <li><a href="home.php?menu=alta_usuario&usuario=<?php if(!empty($_GET['usuario'])) echo $_GET['usuario'];?>">Alta de Usuario</a></li>
                                        <li><a href="home.php?menu=cambiar_clave&usuario=<?php echo $_GET['usuario'];?>">Cambiar clave de Usuario</a></li>
                                        <li><a href="home.php?menu=generacion_masiva_usuarios&usuario=<?php if(!empty($_GET['usuario'])) echo $_GET['usuario'];?>">Generacion Masiva de Usuarios</a></li>
                                    </ul>
                                </li>                                
                                <li><a href="javascript: cerrarSesion();">Salir</a></li>
                            </ul>
                    </nav>
                </div>
            </div>
        </div>
        <div id="div_contenido" style="display: none;" class="box shadow p-3 mb-5 bg-body rounded row justify-content-center">
            
                <div class="card-body">
                    <?php 
                    include_once 'funciones/funciones.php';

                    if(!empty($_GET['usuario'])){
                        $usuario= base64_decode($_GET['usuario']); 
                    }
                    //--------MENU-----------------------//
               
                        //--------Equipos-----------------------//  

                        if(!empty($_GET['menu']) and $_GET['menu']=='ver_equipos'){
                            include 'views/equipos/index.php';
                        } 

                        

                         //----------Mantenimiento-------------//

                        if(!empty($_GET['menu']) and $_GET['menu']=='ver_ordenes_trabajo'){
                            include 'views/ordenes_trabajo/index.php';
                        }
                         
                    
                        //----------Deposito-------------//

                        if(!empty($_GET['menu']) and $_GET['menu']=='ver_componentes_equipos'){
                            include 'views/componentes/componentes_equipos.php';
                        } 

                        if(!empty($_GET['menu']) and $_GET['menu']=='ver_pendientes_entrega'){
                            include 'views/ordenestrabajo_insumos/pendientes_entrega.php';
                        } 

                         //----------Alertas-------------//
                         
                         if(!empty($_GET['menu']) and $_GET['menu']=='ver_alertas'){
                            include 'views/alertas/index.php';
                        } 
                        
                        //----------Vibraciones-------------//

                        if(!empty($_GET['menu']) and $_GET['menu']=='analisis_vibraciones'){
                            include 'views/analisis_vibraciones/index.php';
                        }
                    
                    
                        //----ACCIONES----------------------//
                        
                        //----Amalisis------//
                        if(!empty($_GET['accion']) and $_GET['accion']=='agregar_analisis'){
                            include 'views/analisis_vibraciones/create_busqueda_avanzada.php';
                        }
                        if(!empty($_GET['accion']) and $_GET['accion']=='agregar_analisis_busqueda_comun'){
                            include 'views/analisis_vibraciones/create_busqueda_basica.php';
                        }
                        if(!empty($_GET['accion']) and $_GET['accion']=='busqueda_equipo'){
                            include 'views/analisis_vibraciones/busqueda.php';
                        }
                        
                        if(!empty($_GET['accion']) and $_GET['accion']=='modificar_analisis'){
                            include 'views/analisis_vibraciones/edit.php';
                        }

                        if(!empty($_GET['accion']) and $_GET['accion']=='estado_equipo' and $_GET['menu']=='equipos'){
                            include 'views/equipos/form.php';
                        }

                        if(!empty($_GET['accion']) and $_GET['accion']=='visualizar_detalle_ot'){
                            include 'views/equipos/preview.php';
                        }


                        if(!empty($_GET['accion']) and $_GET['accion']=='bootstrap-select'){
                            include 'views/bootstrap-select/index.php';
                        }
                        
                        //---------------------//

                        
                            //--------Administracion de Sistema-----------------------//

                            if(!empty($_GET['menu']) and $_GET['menu']=='usuarios'){
                                include 'views/usuarios/index.php';
                            }

                            if(!empty($_GET['menu']) and $_GET['menu']=='tipos_usuario'){
                                include 'views/tipos_usuario/index.php';
                            }



                            //-----------------------------------//

                            
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
                        //---------------------//


                        /*
                        //------------POR DEFECTO---------------------//
                        if(empty($_GET['menu']) and empty($_GET['accion'])){
                            include 'views/ordenes_trabajo/index.php';
                        }
                        */
                    ?>
                </div>
             
    </div>
</div> 
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
            window.location.href = "/control_mantenimiento";
    }

           
</script>   
	</body>
</html>