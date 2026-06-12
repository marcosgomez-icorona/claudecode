<!DOCTYPE html>
	<head>
		<title>Recibos de Sueldo</title>
        
        <link rel="stylesheet" href="css/bootstrap.min.css">
        
        <link rel="stylesheet" href="css/menu.css">
        <!--<link rel="stylesheet" href="css/styles.css">-->
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
    function descargarPDF(url) {
    fetch(url)
        .then(response => {
        if (!response.ok) {
            throw new Error('No se pudo obtener el PDF');
        }
        return response.blob();
        })
        .then(blob => {
        const blobUrl = URL.createObjectURL(blob);

        // Abrir en nueva pestaña con nombre "recibo.pdf"
        const a = document.createElement('a');
        a.href = blobUrl;
        a.download = 'recibo_sueldo.pdf';  // nombre que se usará si se descarga
        a.target = '_blank';        // abrir en nueva pestaña
        a.click();

        // Limpieza del blob
        setTimeout(() => URL.revokeObjectURL(blobUrl), 1000);
        })
        .catch(error => {
        alert('Error al descargar: ' + error.message);
        });
    }
</script>
<style>
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
        .main-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #d5d7e4 0%, #a3a2a5 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .card-header-custom h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }
        .card-body-custom {
            padding: 40px;
        }
        .info-box {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            border-left: 4px solid #ff6b6b;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #cdced3;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        .file-input-wrapper input[type=file] {
            position: absolute;
            left: -9999px;
        }
        .file-input-label {
            display: block;
            padding: 20px;
            background: linear-gradient(135deg, #b8bac2 0%, #807f81 100%);
            color: white;
            text-align: center;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }
        .file-input-label:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(194, 195, 202, 0.4);
        }
        .file-input-label i {
            font-size: 2rem;
            display: block;
            margin-bottom: 10px;
        }
        .progress-container {
            display: none;
            margin-top: 20px;
        }
        .progress {
            height: 40px;
            border-radius: 20px;
            background: #e9ecef;
            overflow: visible;
        }
        .progress-bar {
            background: linear-gradient(90deg, #a8abb6 0%, #b7b7b8 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            transition: width 0.3s ease;
        }
        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-top: 20px;
            font-weight: 500;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        .badge-custom {
            background: linear-gradient(135deg, #c0c3cc 0%, #aaa9ac 100%);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
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
                                        <li ><a href="">Recibos</a>
                                            <ul>
                                                <li><a href="home.php?menu=individualizar_recibos">Individualizar Recibos PDF</a></li>                                    
                                                <li><a href="home.php?menu=carga_recibos">Cargar Recibos</a></li>                                    
                                                <li><a href="home.php?menu=listado_cargas">Listado de Cargas de Recibos</a></li>
                                                <li><a href="home.php?accion=generacion_recibos">Generacion de Recibos (Import. Marchese)</a></li>                                                
                                                <li><a href="home.php?accion=generacion_pdf_individual">Generacion de Recibos Individuales en PDF</a></li>                                                
                                                <li><a href="home.php?accion=estado_carga">Estado de Carga</a></li>                                                
                                            </ul>
                                        </li>                                
                                        <li ><a href="">Personal</a>
                                            <ul>
                                                <li><a href="home.php?menu=ver_personal">Listado del Personal</a></li>
                                                
                                            </ul>
                                        </li>  
                                        <!--
                                        <li ><a href="">Mantenimiento</a>
                                            <ul>                                                
                                                <li><a href="home.php?menu=alta_usuario">Alta de Usuario</a></li>
                                                <li><a href="home.php?menu=cambiar_clave&usuario=<?php //echo $_GET['usuario'];?>">Cambiar clave de Usuario</a></li>
                                                <li><a href="home.php?menu=generacion_masiva_usuarios">Generacion Masiva de Usuarios</a></li>
                                            </ul>
                                        </li>
                                        -->                                
                                        <li><a href="javascript: cerrarSesion();">Salir</a></li>
                                    </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
        <div class="container-fluid my-1 full-width-container">
            <div class="p-4 ">
                    <?php 
                    //--------MENU-----------------------//
                    if(!empty($_GET['menu']) and $_GET['menu']=='carga_recibos'){
                        include 'views/carga_recibos/index.php';
                    } 
                        

                        

                         //----------Recibos-------------//
                         if(!empty($_GET['accion']) and $_GET['accion']=='ver_recibos_empleado'){
                            include 'views/recibos/form.php';
                        }
                        
                        if(!empty($_GET['menu']) and $_GET['menu']=='ver_recibos'){
                            include 'views/recibos/index.php';
                        }

                        if(!empty($_GET['menu']) and $_GET['menu']=='validacion_recibo_empleado'){
                            include 'views/usuarios/login.php';
                        }
                        
                        if(!empty($_GET['accion']) and $_GET['accion']=='subir_recibos'){
                            include 'views/carga_recibos/subir_recibos.php';
                        }

                        if(!empty($_GET['accion']) and $_GET['accion']=='estado_carga'){
                            include 'views/recibos/estado_carga_recibos.php';
                        }

                        //--------CARGAS y GENERACION-----------------------//  

                        if(!empty($_GET['menu']) and $_GET['menu']=='listado_cargas'){
                            include 'views/carga_recibos/listado_carga.php';
                        }
                        if(!empty($_GET['menu']) and $_GET['menu']=='individualizar_recibos'){
                            include 'views/generacion_recibos/individualizar_recibos_pdf.php';
                        }
                        if(!empty($_GET['accion']) and $_GET['accion']=='listado_recibos_asociados_carga'){
                            include 'views/carga_recibos/listado_recibos_asociados_carga.php';
                        } 

                        if(!empty($_GET['accion']) and $_GET['accion']=='generacion_recibos'){
                            include 'views/generacion_recibos/generar_recibos.php';
                        }

                        if(!empty($_GET['accion']) and $_GET['accion']=='generacion_pdf_individual'){
                            include 'views/generacion_recibos/generar_recibos_pdf.php';
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
        </div>
</body>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</html>