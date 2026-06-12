<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">    
    <title>Extranet - Acceso a Clientes y Proveedores</title>    
    <!-- Bootstrap CSS (Versión única, siempre la última estable) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/jquery-ui.css">

    <!-- jQuery y Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery-ui.js"></script>
    
    <!-- Buscador en Select -->
    <link rel="stylesheet" href="css/bootstrap-select.min.css">
    <script src="js/bootstrap-select.min.js"></script>

    <style>
    *, *::before, *::after {
        box-sizing: border-box;
    }

    html, body {
        width: 100%;
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    .main-container {
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        border-radius: 12px;
        padding: 1rem;
        margin: 1rem auto;
        max-width: 100%;
        width: 100%;
    }

    @media (min-width: 576px) {
        .main-container {
            max-width: 540px;
        }
    }

    @media (min-width: 768px) {
        .main-container {
            max-width: 720px;
        }
    }

    @media (min-width: 992px) {
        .main-container {
            max-width: 960px;
        }
    }

    @media (min-width: 1200px) {
        .main-container {
            max-width: 1024px;
        }
    }
</style>

</head>
<body>
    <div class="main-container">
        <h3 class="text-center font-weight-light my-4"><img src="assets/img/Logo-Ing La Corona.png" width="50px" alt=""></h3>
        <?php 
                    include_once 'funciones/funciones.php';
                    include 'controller/usuarios.php';

                    if(!empty($_GET['usuario'])){
                        $usuario= base64_decode($_GET['usuario']); 
                        $tipo_usuario=tipo_usuario($usuario);
                    }else{
                        $tipo_usuario='';
                    }
                    //--------MENU-----------------------//
               
                        //--------CAÑEROS-----------------------//  

                        if(!empty($_GET['menu']) and $_GET['menu']=='detalle_caniero'){
                            include 'views/canieros/index.php';
                        }
                        
                        //--------PROVEEDORES-----------------------//  

                        if(!empty($_GET['menu']) and $_GET['menu']=='proveedores'){
                            include 'views/proveedores/index.php';
                        }
                        if(!empty($_GET['accion']) and $_GET['accion']=='ver_oc'){
                            include 'views/proveedores/ordenes_compra.php';
                        }
                        if(!empty($_GET['accion']) and $_GET['accion']=='ver_certidicaciones'){
                            include 'views/proveedores/certificacion_servicios.php';
                        }
                        if(!empty($_GET['accion']) and $_GET['accion']=='ver_facturas'){
                            include 'views/proveedores/facturas_pendientes.php';
                        }
                        if(!empty($_GET['accion']) and $_GET['accion']=='ver_cta_cte'){
                            include 'views/proveedores/cta_cte.php';
                        }
                        if(!empty($_GET['accion']) and $_GET['accion']=='ver_op'){
                            include 'views/proveedores/ordenes_pago.php';
                        }
                        
                        //----ACCIONES----------------------//

                        /*
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
                        
                        */

                        /*
                        //------------POR DEFECTO---------------------//
                        if(empty($_GET['menu']) and empty($_GET['accion'])){
                            include 'views/ordenes_trabajo/index.php';
                        }
                        */
                    ?>
    </div>
    
</body>
</html>

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

            //controlo si se accedio al estado del equipo directamente para permitir acceso
            // Obtener los parámetros GET de la URL
            const urlParams = new URLSearchParams(window.location.search);

            // Verificar si los parámetros 'menu' y 'accion' existen y tienen los valores deseados
            const menu = urlParams.get('menu');
            const accion = urlParams.get('accion');
            const id_equipo = urlParams.get('id_equipo');

            if (menu === 'equipos' && accion === 'estado_equipo') {
                // Redirigir a la página control_mantenimiento/home.php
                window.location.href = 'view.php?menu='+menu+'&accion='+accion+'&id_equipo='+id_equipo;
            }else{
                    // El usuario no está autenticado, realizar acciones necesarias            
                    window.location.href = 'index.php';
                    alert('Debe loguearse para entrar al Sistema....')
                    close();  
            }
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