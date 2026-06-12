<!DOCTYPE html>
	<head>
		<title>Recibos de Sueldo</title>
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
                    width: 110% !important;
        }
       
</style>
</head>
<body>
    <div class="container-fluid my-1 full-width-container">
        <div class="p-4 bg-light shadow rounded">
                <?php 

                    if(!empty($_GET['menu']) and $_GET['menu']=='validacion_recibo_empleado'){
                        include 'views/usuarios/login.php';
                    }

                    if(!empty($_GET['accion']) and $_GET['accion']=='ver_recibos_empleado'){
                        include 'views/recibos/form.php';
                    }

                    if(empty($_GET['menu']) and empty($_GET['accion'])){
                        include 'views/usuarios/login.php';
                    }

                    if(!empty($_GET['menu']) and $_GET['menu']=='cambiar_clave'){
                        include 'views/usuarios/cambiar_clave.php';
                    }
               
                ?>
        </div> 
    </div>       
</body>
</html>
<script>
    window.onload = function() {
        if (verificarSesion()) {
            // El usuario está autenticado, realizar acciones necesarias

            //SE HABILIA LA VISUALIZACION DEL MENU Y EL CONTENIDO                        
            /*
            var elemento = document.getElementById("div_contenido");
            if (elemento) {
                elemento.style.display = "inline";
            } 
           */
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
    function cerrarSesion() {
            // Borrar la cookie estableciendo su fecha de caducidad en el pasado
            document.cookie = "sesion=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";

            // Redirigir al usuario a la página de inicio de sesión o a otra página
            window.location.href = "index.php";
    }
</script>