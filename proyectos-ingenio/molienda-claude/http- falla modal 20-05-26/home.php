<?php // home.php

// Título de la página para el header.php
$page_title = 'Molienda Online Web';

// Incluir la cabecera común
include_once 'views/includes/header.php';

?>

<div class="full-width-container">    
    <div class="p-1 bg-light shadow rounded">
    <?php    

    // MOLIENDA
    if (!empty($_GET['menu']) && $_GET['menu'] == 'molienda_online') {
        
        include 'views/molienda_online/index.php';
    }
    else if (!empty($_GET['menu']) && $_GET['menu'] == 'indicadores_fabrica') {
        include 'views/indicadores_fabrica/index.php';
    }else if (!empty($_GET['menu']) && $_GET['menu'] == 'analisis_azucar') {
        include 'views/indicadores_fabrica/analisis_azucar.php';
    }else if (!empty($_GET['menu']) && $_GET['menu'] == 'resumen_fabrica') {
        include 'views/indicadores_fabrica/resumen_fabrica.php';
    }else if (!empty($_GET['menu']) && $_GET['menu'] == 'molienda_campo') {
        include 'views/molienda_online/molienda_campo.php';
    }else if (!empty($_GET['menu']) && $_GET['menu'] == 'monitoreo_fabrica') {
        include 'views/molienda_online/monitoreo_fabrica_page.php';
    }

    // admin
    if (!empty($_GET['menu']) && $_GET['menu'] == 'admin') {
        include 'views/admin/index.php';
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
    if(empty($_GET)){
        include 'views/molienda_online/index.php';
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

    // Función para number_format en JS (para simulación)
            function DarFormatoNro(number, decimals, decPoint, thousandsSep) {
                number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
                var n = !isFinite(+number) ? 0 : +number,
                    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                    sep = (typeof thousandsSep === 'undefined') ? '.' : thousandsSep,
                    dec = (typeof decPoint === 'undefined') ? ',' : decPoint,
                    s = '',
                    toFixedFix = function (n, prec) {
                        var k = Math.pow(10, prec);
                        return '' + (Math.round(n * k) / k).toFixed(prec);
                    };
                s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
                if (s[0].length > 3) {
                    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
                }
                if ((s[1] || '').length < prec) {
                    s[1] = s[1] || '';
                    s[1] += new Array(prec - s[1].length + 1).join('0');
                }
                return s.join(dec);
            }
</script>
</div> 
</div>
