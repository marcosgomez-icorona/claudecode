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
    <title>Recibos - Generación PDF Individual</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- jsPDF para generar PDFs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 13px;
        background-color: #f8f9fa;
        margin: 0;
        padding: 20px;
    }
    
    .form-container {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Contenedor principal para centrar el recibo */
    .recibo-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px;
        box-sizing: border-box;
    }

    /* Estilo para el recibo individual - CENTRADO EN A4 */
    .recibo-individual {
        width: 186mm; /* Ancho útil para A4 (210mm - márgenes) */
        min-height: 267mm; /* Alto útil para A4 (297mm - márgenes) */
        margin: 15mm auto; /* Centrado vertical y horizontal con márgenes */
        background: white;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        padding: 15mm;
        box-sizing: border-box;
        position: relative;
        page-break-inside: avoid;
        break-inside: avoid;
    }

    /* Para cuando se active durante la generación del PDF */
    .recibo-individual.active {
        display: block;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
        border: 2px dashed #007bff;
    }

    /* Media query para impresión */
    @media print {
        body {
            background-color: white;
            padding: 0;
            margin: 0;
        }
        
        .no-print {
            display: none !important;
        }
        
        .recibo-container {
            display: block;
            min-height: auto;
            padding: 0;
        }
        
        .recibo-individual {
            width: 186mm;
            min-height: 267mm;
            margin: 15mm auto;
            padding: 15mm;
            box-shadow: none;
            border: 1px solid #ddd;
            page-break-after: always;
        }
        
        /* Asegurar que cada recibo comience en nueva página */
        .recibo-individual {
            page-break-before: always;
        }
    }

    .recibo-list {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }

    .recibo-item {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background-color 0.2s;
    }

    .recibo-item:hover {
        background-color: #f8f9fa;
    }

    .recibo-item:last-child {
        border-bottom: none;
    }

    .recibo-info h6 {
        margin: 0 0 5px 0;
        color: #333;
    }

    .recibo-info small {
        color: #666;
    }

    .btn-group-recibo {
        display: flex;
        gap: 10px;
    }

    .btn-sm {
        font-size: 12px;
        padding: 5px 12px;
    }

    .loading-spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 8px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .progress-container {
        display: none;
        margin-top: 20px;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        text-align: center;
    }

    .stat-number {
        font-size: 2em;
        font-weight: bold;
        color: #007bff;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #666;
        font-size: 0.9em;
    }

    /* Mejoras para el contenido del recibo */
    .recibo-header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #333;
        padding-bottom: 15px;
    }

    .recibo-header h3 {
        margin: 0 0 5px 0;
        font-size: 18px;
        color: #333;
    }

    .recibo-header p {
        margin: 0;
        color: #666;
        font-size: 13px;
    }

    .recibo-content {
        margin: 15px 0;
    }

    .recibo-table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }

    .recibo-table th,
    .recibo-table td {
        padding: 8px 12px;
        border: 1px solid #ddd;
        text-align: left;
    }

    .recibo-table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    .recibo-totales {
        margin-top: 20px;
        border-top: 2px solid #333;
        padding-top: 15px;
        text-align: right;
        font-weight: bold;
    }

    .recibo-footer {
        margin-top: 30px;
        text-align: center;
        font-size: 12px;
        color: #666;
        border-top: 1px solid #ddd;
        padding-top: 15px;
    }
</style>
</head>
<body>
    <div class="container-fluid" style="max-width: 1200px;">
        <h1 class="text-center mb-4">Generador de Recibos PDF Individual</h1>
        
        <!-- Formulario de selección -->
        <div class="form-container">
            <form method="POST" action="">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Tipo de Liquidación</label>
                        <select name="tipo_liq" class="form-select">
                            <option value="">Seleccionar...</option>
                            <option value="Q_FAB" <?php echo ($tipo_seleccionado == 'Q_FAB') ? 'selected' : ''; ?>>QUINCENAL FÁBRICA</option>
                            <option value="Q_CAMPO" <?php echo ($tipo_seleccionado == 'Q_CAMPO') ? 'selected' : ''; ?>>QUINCENAL CAMPO</option>
                            <option value="MENSUAL_CONVENIO" <?php echo ($tipo_seleccionado == 'MENSUAL_CONVENIO') ? 'selected' : ''; ?>>MENSUAL DE CONVENIO</option>
                            <option value="MENSUAL_FUERA_CONVENIO" <?php echo ($tipo_seleccionado == 'MENSUAL_FUERA_CONVENIO') ? 'selected' : ''; ?>>MENSUAL FUERA DE CONVENIO</option>
                            <option value="MENSUAL_BIO" <?php echo ($tipo_seleccionado == 'MENSUAL_BIO') ? 'selected' : ''; ?>>MENSUAL BIO ENERGÍA</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="cargar" class="btn btn-primary">
                            <i class="fas fa-search"></i> Cargar
                        </button>
                    </div>
                    <?php if (!empty($liq_manuales) && !empty($_POST)): ?>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-success" onclick="generarTodosPDF()">
                            <span class="loading-spinner"></span>
                            <i class="fas fa-download"></i> Descargar Todos (ZIP)
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <?php if (!empty($liq_manuales) && !empty($_POST)): ?>
            
            <!-- Estadísticas -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($liq_manuales); ?></div>
                    <div class="stat-label">Recibos Cargados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="pdf-generados">0</div>
                    <div class="stat-label">PDFs Generados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo strtoupper(str_replace('_', ' ', $tipo_seleccionado)); ?></div>
                    <div class="stat-label">Tipo Seleccionado</div>
                </div>
            </div>

            <!-- Barra de progreso para descarga masiva -->
            <div class="progress-container">
                <div class="d-flex justify-content-between mb-2">
                    <span>Generando PDFs...</span>
                    <span id="progress-text">0%</span>
                </div>
                <div class="progress">
                    <div class="progress-bar" id="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
            </div>

            <!-- Lista de recibos -->
            <div class="recibo-list">
                <?php foreach ($liq_manuales as $index => $item): ?>
                <div class="recibo-item">
                    <div class="recibo-info">
                        <h6>
                            <?php 
                            // Asume que tienes campos como nombre, legajo, etc. en $item
                            echo isset($item['nombre']) ? $item['nombre'] : 'Empleado #' . ($index + 1);
                            ?>
                        </h6>
                        <small>
                            Legajo: <?php echo isset($item['legajo']) ? $item['legajo'] : 'N/A'; ?> | 
                            Período: <?php echo isset($item['periodo']) ? $item['periodo'] : date('m/Y'); ?>
                        </small>
                    </div>
                    <div class="btn-group-recibo">
                        <button class="btn btn-outline-primary btn-sm" onclick="previsualizarRecibo(<?php echo $item['periodo'] ?? $index; ?>)">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="generarPDFIndividual(<?php echo $item['periodo'] ?? $index; ?>, '<?php echo htmlspecialchars($item['legajo'] ?? '', ENT_QUOTES); ?>')">
                            <span class="loading-spinner"></span>
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Contenedores ocultos para cada recibo -->
            <?php foreach ($liq_manuales as $index => $item): ?>
            <div class="recibo-individual" id="recibo-<?php echo $index; ?>"    data-nombre="<?php $nombreRecibo = $item['nombre_recibo'];
                                                                                                $nombreRecibo = preg_replace('/^(50|60)/', '', $nombreRecibo);
                                                                                                echo $nombreRecibo; ?>" >
                <div style="display: flex; justify-content: space-between;">
                    <!-- Original -->
                    <div style="padding-right: 40px;">                        
                        <?php include 'recibo_hoja_original.php'; ?>
                    </div>                    
                </div>
            </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="alert alert-info text-center" role="alert">
                <i class="fas fa-info-circle"></i>
                <?php echo ($tipo_seleccionado) ? 'No hay recibos para el tipo seleccionado.' : 'Seleccione un tipo de liquidación y presione Cargar para comenzar.'; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    
    <script>
        const { jsPDF } = window.jspdf;
        let pdfGenerados = 0;

        // Previsualizar recibo
        function previsualizarRecibo(index) {
            // Ocultar todos los recibos
            document.querySelectorAll('.recibo-individual').forEach(recibo => {
                recibo.classList.remove('active');
            });
            
            // Mostrar el recibo seleccionado
            const reciboSeleccionado = document.getElementById(`recibo-${index}`);
            reciboSeleccionado.classList.add('active');
            
            // Scroll al recibo
            reciboSeleccionado.scrollIntoView({ behavior: 'smooth' });
        }

        // Generar PDF individual
        async function generarPDFIndividual(index, legajo) {
            const button = event.target.closest('button');
            const spinner = button.querySelector('.loading-spinner');
            const originalText = button.innerHTML;
            
            // Mostrar loading
            spinner.style.display = 'inline-block';
            button.disabled = true;
            
            try {
                const recibo = document.getElementById(`recibo-${index}`);
                recibo.classList.add('active');
                
                // Esperar a que se renderice
                await new Promise(resolve => setTimeout(resolve, 100));
                
                // Capturar como imagen
                const canvas = await html2canvas(recibo, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    width: recibo.scrollWidth,
                    height: recibo.scrollHeight
                });
                
                // Crear PDF
                //const pdf = new jsPDF('l', 'mm', 'a4'); // landscape, mm, a4
                const pdf = new jsPDF({
                    orientation: 'portrait',    // Cambiar a vertical
                    unit: 'mm',
                    format: 'a4',
                    compress: false,            // Desactivar compresión para mejor calidad
                    precision: 16               // Mayor precisión en los cálculos
                });

                const imgData = canvas.toDataURL('image/png');
                
                // Calcular dimensiones para que se ajuste a la página
                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = pdf.internal.pageSize.getHeight();
                const imgAspectRatio = canvas.width / canvas.height;
                const pdfAspectRatio = pdfWidth / pdfHeight;
                
                let finalWidth, finalHeight;
                if (imgAspectRatio > pdfAspectRatio) {
                    finalWidth = pdfWidth - 10; // margen de 5mm cada lado
                    finalHeight = finalWidth / imgAspectRatio;
                } else {
                    finalHeight = pdfHeight - 10; // margen de 5mm cada lado
                    finalWidth = finalHeight * imgAspectRatio;
                }
                
                const x = (pdfWidth - finalWidth) / 2;
                const y = (pdfHeight - finalHeight) / 2;
                
                pdf.addImage(imgData, 'PNG', x, y, finalWidth, finalHeight);
                
                // Nombre del archivo — quitar prefijo 50/60 del legajo
                let legajoNombre = String(legajo || '');
                if (legajoNombre.startsWith('50')) legajoNombre = legajoNombre.substring(2);
                else if (legajoNombre.startsWith('60')) legajoNombre = legajoNombre.substring(2);
                const fecha = new Date().toISOString().slice(0, 10);
                const nombreArchivo = legajoNombre
                    ? `${legajoNombre}-recibo_${fecha}.pdf`
                    : `recibo_${index + 1}_${fecha}.pdf`;
                
                // Descargar
                pdf.save(nombreArchivo);
                
                // Actualizar contador
                pdfGenerados++;
                document.getElementById('pdf-generados').textContent = pdfGenerados;
                
                // Ocultar recibo después de generar
                recibo.classList.remove('active');
                
            } catch (error) {
                console.error('Error generando PDF:', error);
                alert('Error al generar el PDF. Por favor intente nuevamente.');
            } finally {
                // Quitar loading
                spinner.style.display = 'none';
                button.disabled = false;
            }
        }

       
async function generarTodosPDF() {
    const button = event.target;
    const spinner = button.querySelector('.loading-spinner');
    const progressContainer = document.querySelector('.progress-container');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    
    const recibos = document.querySelectorAll('.recibo-individual');
    const totalRecibos = recibos.length;
    
    if (totalRecibos === 0) {
        alert('No hay recibos para procesar');
        return;
    }
    
    // Mostrar loading y progress
    if (spinner) spinner.style.display = 'inline-block';
    if (progressContainer) progressContainer.style.display = 'block';
    button.disabled = true;
    
    const zip = new JSZip();
    let procesadosExitosos = 0;
    
    try {
        for (let i = 0; i < totalRecibos; i++) {
            const recibo = recibos[i];
            const nombreRecibo = recibo.getAttribute('data-nombre') || `recibo_${(i + 1).toString().padStart(3, '0')}`;
            
            if (!recibo) {
                console.warn(`No se encontró el recibo en índice: ${i}`);
                continue;
            }
            
            // Mostrar temporalmente el recibo para capturarlo
            const originalDisplay = recibo.style.display;
            recibo.style.display = 'block';
            
            // Esperar renderizado
            await new Promise(resolve => setTimeout(resolve, 500));
            
            try {
                // Capturar como imagen
                const canvas = await html2canvas(recibo, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: false,
                    backgroundColor: '#ffffff',
                    logging: false,
                    onclone: function(clonedDoc) {
                        // Asegurar que el recibo clonado se vea bien
                        const clonedRecibo = clonedDoc.getElementById(recibo.id);
                        if (clonedRecibo) {
                            clonedRecibo.style.display = 'block';
                        }
                    }
                });
                
                // Crear PDF
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4',
                    compress: true
                });
                
                const imgData = canvas.toDataURL('image/jpeg', 0.8);
                
                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = pdf.internal.pageSize.getHeight();
                
                // Calcular dimensiones manteniendo relación de aspecto
                const imgWidth = canvas.width;
                const imgHeight = canvas.height;
                const ratio = Math.min(
                    (pdfWidth - 20) / imgWidth, 
                    (pdfHeight - 20) / imgHeight
                );
                
                const finalWidth = imgWidth * ratio;
                const finalHeight = imgHeight * ratio;
                
                const x = (pdfWidth - finalWidth) / 2;
                const y = (pdfHeight - finalHeight) / 2;
                
                pdf.addImage(imgData, 'PNG', x, y, finalWidth, finalHeight);
                
                // Agregar metadata al PDF
                pdf.setProperties({
                    title: nombreRecibo,
                    subject: 'Recibo de Haberes',
                    author: 'Sistema de Recibos',
                    keywords: 'recibo, sueldo, haberes'
                });
                
                // Agregar al ZIP con el nombre correcto
                const pdfBlob = pdf.output('blob');
                zip.file(`${nombreRecibo}.pdf`, pdfBlob);
                
                procesadosExitosos++;
                
            } catch (error) {
                console.error(`Error procesando recibo ${nombreRecibo}:`, error);
                // Crear un PDF de error para este recibo
                try {
                    const pdfError = new jsPDF();
                    pdfError.setFontSize(16);
                    pdfError.text('Error al generar recibo', 20, 20);
                    pdfError.setFontSize(12);
                    pdfError.text(`Recibo: ${nombreRecibo}`, 20, 30);
                    pdfError.text('No se pudo generar el PDF correctamente', 20, 40);
                    const pdfBlobError = pdfError.output('blob');
                    zip.file(`${nombreRecibo}_ERROR.pdf`, pdfBlobError);
                } catch (e) {
                    console.error('Error creando PDF de error:', e);
                }
            } finally {
                // Restaurar display original
                recibo.style.display = originalDisplay;
            }
            
            // Actualizar progreso
            const progress = Math.round(((i + 1) / totalRecibos) * 100);
            if (progressBar) progressBar.style.width = progress + '%';
            if (progressText) {
                progressText.textContent = `${progress}% (${i + 1}/${totalRecibos}) - ${nombreRecibo}`;
            }
            
            // Pequeña pausa para no saturar el navegador
            await new Promise(resolve => setTimeout(resolve, 1));
        }
        
        if (zip.files.length === 0) {
            throw new Error('No se pudo generar ningún PDF');
        }
        
        // Generar y descargar ZIP
        const content = await zip.generateAsync({
            type: 'blob',
            compression: 'DEFLATE',
            compressionOptions: { level: 6 }
        });
        
        const fecha = new Date().toISOString().slice(0, 10).replace(/-/g, '');
        const nombreZip = `recibos_${fecha}.zip`;
        saveAs(content, nombreZip);
        
        // Actualizar contador total si existe
        const pdfGenerados = document.getElementById('pdf-generados');
        if (pdfGenerados) {
            pdfGenerados.textContent = procesadosExitosos;
        }
        
        if (procesadosExitosos === totalRecibos) {
            alert(`¡${procesadosExitosos} recibos generados exitosamente!`);
        } else {
            alert(`¡${procesadosExitosos} de ${totalRecibos} recibos generados exitosamente! 
Algunos recibos pueden tener errores.`);
        }
        
    } catch (error) {
        console.error('Error generando PDFs:', error);
        alert('Error al generar los PDFs: ' + error.message);
    } finally {
        // Limpiar UI
        if (spinner) spinner.style.display = 'none';
        if (progressContainer) progressContainer.style.display = 'none';
        if (progressBar) progressBar.style.width = '0%';
        if (progressText) progressText.textContent = '0%';
        button.disabled = false;
    }
}

        // Establecer fecha actual
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