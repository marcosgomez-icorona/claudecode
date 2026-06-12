<style>
    body {
        position: relative;
        background: url('/extranet/assets/img/fondo.png') no-repeat top center;
        background-size: cover;
        background-color: #f8f9fa;
        padding-top: 50px;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
    }

    .background-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(144, 238, 144, 0.2), rgba(245, 245, 220, 0.2));
        pointer-events: none;
        z-index: 0;
    }

    .content {
        position: relative;
        z-index: 1;
        text-align: center;
    }
    
</style>

<body>
    <div class="background-overlay"></div>
    
    <div class="content">
        
        <h2>Acceso a Clientes y Proveedores</h2>
        <div class="card-body">
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

                        if(!empty($_GET['menu']) and $_GET['menu']=='ordenes_compra'){
                            include 'views/ordenes_compra/index.php';
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
    </div>
</body>

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