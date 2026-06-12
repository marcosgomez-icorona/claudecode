<?php // home.php

// Título de la página para el header.php
$page_title = 'Extranet - Acceso a Clientes y Proveedores';

// Incluir la cabecera común
include_once 'views/includes/header.php';

// Inclusiones de PHP que ya tenías
include_once 'funciones/funciones.php';
include 'controller/usuarios.php'; // Debería ser un controlador o servicio en un MVC

// Lógica de usuario (sin cambios, pero recuerda la nota de seguridad)
if (!empty($_GET['usuario'])) {
    $usuario = base64_decode($_GET['usuario']);
    $tipo_usuario = tipo_usuario($usuario); // Función definida en funciones/funciones.php
} else {
    $tipo_usuario = '';
}

// ------ Contenido Principal de la Aplicación ------
// Esta sección `main-container` ya está en el header.php. Solo la abrimos/cerramos.
// Si tu contenido a incluir ya tiene un div.container, deberías ajustar el header.php
// para que no incluya el .container y lo manejes en cada vista.
// Para este ejemplo, asumo que el `main-container` del header es lo que envuelve todo el contenido.
?>
<style>
    /* ESTILO DEL SUBMENU DE PROVEEDORES Y CAÑEROS */
    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .card-hover:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        background-color: #f8f9fa; /* opcional para efecto suave */
    }

    .card-hover h4 {
        transition: color 0.3s;
    }

    .card-hover:hover h4 {
        color: #0d6efd; /* cambia el texto al azul de Bootstrap */
    }

    /* Márgenes laterales de 2px en todo el ancho */
    .full-width-container {
        padding-left: 2px;
        padding-right: 2px;
    }
</style>

<div class="container-fluid my-1 full-width-container">
    <div class="p-4 bg-light shadow rounded">
    <?php
    // --- Lógica de enrutamiento (PHP) ---
    // Esta sección incluye las vistas basadas en los parámetros GET.
    // Es una forma básica de enrutamiento; en un framework MVC, esto lo manejaría un Router central.

    // CAÑEROS
    if (!empty($_GET['menu']) && $_GET['menu'] == 'canieros') {
        //include 'views/canieros/index.php';
        include 'views/canieros/index.php';
    }
    else if (!empty($_GET['accion']) && $_GET['accion'] == 'ver_detalle_cania') {
        include 'views/canieros/detalle_canieros.php';
    }
    else if (!empty($_GET['accion']) && $_GET['accion'] == 'ver_cta_cte_cania') {
        include 'views/canieros/cta_cte_canieros.php';
    }
    else if (!empty($_GET['accion']) && $_GET['accion'] == 'ver_oc_azucar') {
        include 'views/canieros/oc_azucar.php';
    }
    else if (!empty($_GET['accion']) && $_GET['accion'] == 'ver_op_azucar') {
        include 'views/canieros/op_azucar.php';
    }

    // PROVEEDORES
    else if (!empty($_GET['menu']) && $_GET['menu'] == 'proveedores') {
        include 'views/proveedores/index.php';
    }
    else if (!empty($_GET['accion']) && $_GET['accion'] == 'ver_oc') {
        include 'views/proveedores/ordenes_compra.php';
    }
    else if (!empty($_GET['accion']) && $_GET['accion'] == 'ver_detalle_oc') {
        include 'views/proveedores/detalle_oc.php';
    }
    else if (!empty($_GET['accion']) && $_GET['accion'] == 'ver_certidicaciones') {
        include 'views/proveedores/certificacion_servicios.php';
    }
    else if (!empty($_GET['accion']) && $_GET['accion'] == 'ver_detalle_cert_serv') {
        include 'views/proveedores/detalle_certificado_servicio.php';
    }
    else if (!empty($_GET['accion']) && $_GET['accion'] == 'ver_facturas') {
        include 'views/proveedores/facturas_pendientes.php';
    }
    else if (!empty($_GET['accion']) && $_GET['accion'] == 'ver_cta_cte') {
        include 'views/proveedores/cta_cte.php';
    }
    else if (!empty($_GET['accion']) && $_GET['accion'] == 'ver_op') {
        include 'views/proveedores/ordenes_pago.php';
    }
    else if (!empty($_GET['accion']) && $_GET['accion'] == 'ver_detalle_op') {
        include 'views/proveedores/detalle_orden_pago.php';
    }
    else if (!empty($_GET['accion']) && $_GET['accion'] == 'ver_certificdo_retencion') {
        include 'views/proveedores/certificado_retencion.php';
    }
    
    // admin
    if (!empty($_GET['menu']) && $_GET['menu'] == 'admin') {
        include 'views/admin/index.php';
    }
    if (!empty($_GET['accion']) && $_GET['accion'] == 'carga_masiva_proveedores') {
        include 'views/carga_masiva/carga_proveedores.php';
    }

    // USUARIOS
    else if (!empty($_GET['menu']) && $_GET['menu'] == 'usuarios') {
        include 'views/usuarios/index.php';
    }
    else if (!empty($_GET['menu']) && $_GET['menu'] == 'tipos_usuario') {
        include 'views/tipos_usuario/index.php';
    }
    else if (!empty($_GET['menu']) && $_GET['menu'] == 'alta_usuario') {
        include 'views/usuarios/create.php';
    }
        //CAMBIAR PASS
    if (!empty($_GET['accion']) && $_GET['accion'] == 'cambiar_pass') {
        include 'views/usuarios/cambiar_clave.php';
    }
    
    else {
        // Contenido por defecto si no hay parámetros o si los parámetros no coinciden
        //echo '<div class="alert alert-info text-center" role="alert">Seleccione una opción del menú o cargue el contenido por defecto aquí.</div>';
        // include 'views/ordenes_trabajo/index.php'; // Si este es el contenido por defecto
    }
    ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script> 

    <script>
        function exportarExcel(reporte, event) {
            if (event) event.preventDefault(); // evita el submit del formulario

            var tabla = document.getElementById(reporte);
            var html = tabla.outerHTML.replace(/ /g, '%20');

            var nombreArchivo = 'export.xls';

            var enlace = document.createElement('a');
            enlace.href = 'data:application/vnd.ms-excel;charset=utf-8,' + html;
            enlace.download = nombreArchivo;

            document.body.appendChild(enlace);
            enlace.click();
            document.body.removeChild(enlace);
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
                a.download = 'archivo.pdf';  // nombre que se usará si se descarga
                a.target = '_blank';        // abrir en nueva pestaña
                a.click();

                // Limpieza del blob
                setTimeout(() => URL.revokeObjectURL(blobUrl), 1000);
                })
                .catch(error => {
                alert('Error al descargar: ' + error.message);
                });
    }

    function ReportePDF(reporte) {
        const elemento = document.getElementById(reporte);

        // Opciones para que conserve formato
        const opciones = {
            margin: 10, // Márgenes en mm
            filename: 'reporte.pdf',
            image: { type: 'jpeg', quality: 1 }, // Calidad de imagen
            html2canvas: { scale: 2, useCORS: true }, // Más resolución
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Genera el PDF conservando estilos
        html2pdf().set(opciones).from(elemento).save();
    }
</script>
</div> 
</div>
