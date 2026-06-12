<?php
// Recibos de Sueldo - VISTA MEJORADA con Animaciones y Colores
// Este archivo es un FRAGMENTO HTML y debe ser incluido por una página principal.

// Asegúrate de que $legajo y $recibos_empleado estén definidos antes de incluir esta vista.
// Tu lógica PHP inicial (si esta vista se incluye directamente y necesita estas variables)
// ... (mantenemos la lógica PHP para listado_recibos y nombre_empleado) ...

include_once 'controller/recibos.php';
include_once 'controller/usuarios.php';

if (!empty($_GET['legajo'])) {
    $legajo = base64_decode($_GET['legajo']);    
} else {
    $legajo = -1; // Valor por defecto si no hay legajo
}
$cod_legajo = (int)$legajo;
// Asegúrate de que listado_recibos($legajo, 'empleado', '') devuelve un array.
$recibos_empleado = listado_recibos($cod_legajo, 'empleado', '');


// Acceso seguro al nombre del empleado
$nombre_empleado = $recibos_empleado[0]['nombre'] ?? '';
$recibo = !empty($recibos_empleado[0]['recibo']) ? $recibos_empleado[0]['recibo'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap 5 JS Bundle (incluye Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery (solo si alguna lógica aún lo requiere, evitar si no es necesario) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        html, body {
            margin: 3px;
            padding: 3px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }
        .table {
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
<div class="container-fluid my-3">
    <div class="p-4 bg-light shadow rounded">
        <div class="container my-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center m-2">
                    <img src="assets/img/Logo-Ing La Corona.png" width="60" alt="Logo" class="me-3">
                    <h1 class="text-secondary">Portal de Empleados</h1>
                </div>
                <div>
                    <a href="view.php?menu=cambiar_clave&legajo=<?php echo $_GET['legajo'] ?? ''?>" class="btn btn-outline-info me-2">
                        <i class="fas fa-key me-1"></i> Cambiar Clave
                    </a>
                    <a href="javascript: cerrarSesion();" class="btn btn-secondary">Salir</a>
                </div>
            </div>
            <div class="card-header bg-secondary text-white">
                <div class="row text-center">
                    <div class="h1 "><?php echo $nombre_empleado ?? '';?></div>
                </div>                
                <div class="row text-end">
                    <div class="h4"><?php echo 'Legajo '.$legajo; ?></div>            
                </div>
            </div>            
            
            <div class="card mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Último Recibo de Sueldo</h5>
                </div>
                <div class="card-body d-flex flex-column flex-md-row align-items-start">
                    <?php if (!empty($recibos_empleado[0]['recibo'])):
                        $ultimo_recibo = $recibos_empleado[0];
                        $periodo=$ultimo_recibo['periodo'] ?? '';
                        $quincena = $ultimo_recibo['quincena'] ?? '';
                        $forma_liquidacion = $ultimo_recibo['forma_liquidacion'] ?? '';
                        $anio = substr($periodo, 0, 4); // Toma los primeros 4 caracteres
                        $mes  = substr($periodo, 4, 2); // Toma los siguientes 2 caracteres
                        $archivo = $ultimo_recibo['recibo'] ?? '';
                        $tipo_liq = $ultimo_recibo['tipo_liquidacion'] ?? '';
                        $cat = $ultimo_recibo['cod_cat'] ?? '';
                        $ruta_ultimo_recibo = 'assets/recibos/'.$archivo;                        
                    ?>
                    <p class="h3 m-2">
                     <span class="fw-bold text-secondary">
                        
                        <?php
                            
                            if($tipo_liq<>'SAC'){
                                if($tipo_liq<>'VACACIONES'){
                                    $liquidacion = 'Periodo '.$anio.' / '.$mes.' '.$quincena ?? '';
                                }else{
                                    {
                                        $liquidacion = 'VACACIONES '.$anio;
                                    }
                                }
                                
                            }else{
                                if($cat == 'CAT-1er'){
                                    $liquidacion = '1° SAC '.$anio;
                                }else{
                                    $liquidacion = '2° SAC '.$anio;
                                }
                                
                            }
                                
                             echo $liquidacion;   
                                
                        ?>
                    </span>
                    </p> 
                    <button class="btn btn-success ms-md-3" onclick="abrirModalConformidad('<?php echo $ruta_ultimo_recibo ?? '';?>', '<?php echo $ultimo_recibo['id_recibo'] ?? 0; ?>')">
                        Ver Último Recibo
                    </button>
                    <?php else: ?>
                        <p class="mb-0 text-muted text-center w-100">No hay recibos disponibles para este empleado.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card border-secondary">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historial de Recibos</h5>
                </div>
                <div class="card-body">
            <?php if (!empty($recibos_empleado)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm text-secondary justify-content-center">
                        <thead class="table-secondary">
                            <tr>
                                <th scope="col" class="text-dark">Año</th>
                                <th scope="col" class="text-dark">Mes</th>
                                <th scope="col" class="text-dark">Tipo Liquidación</th>
                                <?php if($forma_liquidacion== 'QUINCENAL' ){
                                        echo '<th scope="col" class="text-dark">Info Adicional</th>';
                                    }
                                ?>                                
                                <th scope="col" class="text-dark">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recibos_empleado as $index => $recibo):
                                // Omitir el primer recibo si ya se mostró en la sección "Último Recibo"
                                    
                                if ($index === 0 && !empty($recibos_empleado[0])) {                                    
                                    continue;
                                    
                                }    
                                                            
                                $nombre_recibo = $recibo['recibo'] ?? '';
                                $ruta_recibo = 'assets/recibos/' . $nombre_recibo;
                                $periodo_hist=$recibo['periodo'] ?? '';
                                $anio = substr($periodo_hist, 0, 4) ?? ''; // Toma los primeros 4 caracteres
                                $mes  = substr($periodo_hist, 4, 2) ?? ''; // Toma los siguientes 2 caracteres
                            ?>
                                <tr>
                                    <td><?php echo $anio; ?></td>
                                    <td><?php echo $mes; ?></td>
                                    <td><?php echo htmlspecialchars($recibo['tipo_liquidacion'] ?? ''); ?></td>
                                    <?php if(!empty($recibo['quincena'])){
                                        echo '<td>'.$recibo['quincena'].'</td>';
                                    }
                                ?> 
                                    <td>
                                        <button class="btn btn-outline-info btn-sm hover-shadow-sm transition-ease" onclick="abrirModalConformidad('<?php echo $ruta_recibo ?? '';?>', '<?php echo $recibo['id_recibo'] ?? 0; ?>')">                                        
                                            Ver Recibo
                                        </button>
                                        <!--<a class="btn btn-outline-info btn-sm hover-shadow-sm transition-ease" href="#" onclick="descargarPDF('<?php //echo $ruta_recibo; ?>')">
                                                <i class="fas fa-file-alt me-1"></i>
                                            </a>
                                        -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($recibos_empleado) === 1 && !empty($recibos_empleado[0])): ?>
                                
                            <?php elseif (empty($recibos_empleado)): ?>
                                
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted text-center">No hay historial de recibos disponible.</p>
            <?php endif; ?>
        </div>
            </div>

            <!-- Modal de Conformidad -->
            <div class="modal fade" id="modalConformidad" tabindex="-1" aria-labelledby="modalConformidadLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title" id="modalConformidadLabel">
                                <i class="fas fa-file-pdf me-2"></i> Confirmación de descarga
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" onclick="confirmarDescarga()">Aceptar y Descargar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    let pdfParaDescargar = '';
    let idReciboParaConfirmar = '';

function abrirModalConformidad(rutaPDF, idRecibo) {
    pdfParaDescargar = rutaPDF;
    idReciboParaConfirmar = idRecibo;
    const modal = new bootstrap.Modal(document.getElementById('modalConformidad'));
    modal.show();
}

function confirmarDescarga() {
    if (pdfParaDescargar && idReciboParaConfirmar) {
        // Enviar conformidad vía POST a recibos.php
        $.ajax({
            url: 'controller/recibos.php',
            type: 'POST',
            data: {
                accion: 'conformidad',
                id: idReciboParaConfirmar
                
            },
            success: function(respuesta) {
                console.log('Conformidad registrada:', respuesta);
                descargarPDF(pdfParaDescargar);
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalConformidad'));
                modal.hide();
            },
            error: function(err) {
                alert('Error al registrar conformidad. :'+err);
            }
        });
    }
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
                const a = document.createElement('a');
                a.href = blobUrl;
                a.download = 'recibo_sueldo.pdf';
                a.target = '_blank';
                a.click();
                setTimeout(() => URL.revokeObjectURL(blobUrl), 1000);
            })
            .catch(error => {
                alert('Error al descargar: ' + error.message);
            });
    }

    function cerrarSesion() {
        document.cookie = "sesion=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
        window.location.href = "index.php";
    }

    window.onload = function () {
        if (!verificarSesion()) {
            alert('Debe loguearse para entrar al Sistema....');
            window.location.href = 'index.php';
        }
    }

    function verificarSesion() {
        const cookies = document.cookie.split('; ');
        for (const cookie of cookies) {
            const [nombre, valor] = cookie.split('=');
            if (nombre === 'sesion' && valor === 'token_unico') return true;
        }
        return false;
    }
</script>

<!-- FontAwesome para los íconos -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>