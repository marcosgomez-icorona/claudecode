<?php
include 'controller/liquidaciones_importadas.php';

// Obtener el tipo de liquidación seleccionado
$tipo_seleccionado = isset($_POST['tipo_liq']) ? $_POST['tipo_liq'] : '';

// Obtener las liquidaciones según la selección
$liq_manuales = get_liq_manuales($tipo_seleccionado);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibos - Original y Duplicado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Configuración de página en impresión */
        @page {
            size: A4 landscape; /* Apaisado */
            margin: 1mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            background-color: #fff;
        }

        .no-print {
            display: block;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .page-break {
                page-break-after: always;
            }
        }

        /* Contenedor de cada hoja */
        .recibo-container {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            width: 350mm;   /* Ancho de A4 horizontal menos márgenes */
            height: 280mm;  /* Alto de A4 horizontal menos márgenes */
            /* border: 1px solid #000; */
            box-sizing: border-box;
        }

        /* Mitad de hoja para cada recibo */
        .recibo-content {
            width: 50%;       /* Cada recibo ocupa exactamente la mitad */
            padding: 8mm;     /* Márgenes internos */
            box-sizing: border-box;
            /* border-right: 0px dashed #000;*/
            overflow: hidden; /* Evita desbordes */
        }

        .recibo-content:last-child {
            border-right: none;
        }
        
        .form-container {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <h1 class="text-center mb-3 no-print">Recibos de Sueldo</h1>
        <p class="text-center mb-4 no-print">Se generarán en formato Original (izquierda) y Duplicado (derecha).</p>
        
        <!-- Formulario de selección -->
        <div class="form-container no-print">
            <form method="POST" action="">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Tipo Liquidación</label>
                        <select name="tipo_liq" class="form-select">
                            <option value="">Seleccionar...</option>
                            <option value="Q_FAB" <?php echo ($tipo_seleccionado == 'Q_FAB') ? 'selected' : ''; ?>>QUINCENAL FABRICA</option>
                            <option value="Q_CAMPO" <?php echo ($tipo_seleccionado == 'Q_CAMPO') ? 'selected' : ''; ?>>QUINCENAL CAMPO</option>
                            <option value="MENSUAL_CONVENIO" <?php echo ($tipo_seleccionado == 'MENSUAL_CONVENIO') ? 'selected' : ''; ?>>MENSUAL DE CONVENIO</option>
                            <option value="MENSUAL_FUERA_CONVENIO" <?php echo ($tipo_seleccionado == 'MENSUAL_FUERA_CONVENIO') ? 'selected' : ''; ?>>MENSUAL FUERA DE CONVENIO</option>
                            <option value="MENSUAL_BIO" <?php echo ($tipo_seleccionado == 'MENSUAL_BIO') ? 'selected' : ''; ?>>MENSUAL BIO ENERGIA</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="cargar" class="btn btn-primary">Cargar</button>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-success btn-print" onclick="window.print()">
                            Imprimir Recibos
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <?php if (!empty($liq_manuales) and !empty($_POST)): 
                $i=1;    
        ?>

            <?php foreach ($liq_manuales as $item): ?>
                <?php //if($i>142){?>                
                <div class="recibo-container page-break">
                 <!-- 
                    <div class="row text-center small">
                        <div class="col-12 text-center">
                            <?php //echo $i;?>
                        </div>                        
                    </div>
                 -->
                    <!-- Original -->
                    <div class="recibo-content">
                        <?php include 'recibo_hoja_original.php'; ?>
                    </div>
                    <!-- Duplicado -->
                    <div class="recibo-content">
                        <?php include 'recibo_hoja_duplicado.php'; ?>
                    </div>
                </div>

            <?php       //}
                $i++; 
                endforeach; 
            ?>
        <?php else: ?>
            <div class="alert alert-warning text-center no-print" role="alert">
                <?php echo ($tipo_seleccionado) ? 'No hay recibos para el tipo seleccionado.' : 'Seleccione un tipo de liquidación y presione Cargar.'; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Establecer la fecha actual en formato argentino
        document.addEventListener('DOMContentLoaded', function() {
            const fechaHoy = new Date().toLocaleDateString('es-AR', {
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric'
            });
            
            document.querySelectorAll('.fecha-hoy').forEach(function(el) {
                el.textContent = fechaHoy;
            });
        });
    </script>
</body>
</html>