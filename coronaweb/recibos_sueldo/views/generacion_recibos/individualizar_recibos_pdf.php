<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dividir PDF por Legajos</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- PDF Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .main-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }

        .card-header-custom h1 {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
        }

        .card-body-custom {
            padding: 40px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            padding: 10px 15px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .file-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        #pdfInput {
            display: none;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            border: 2px dashed #667eea;
            border-radius: 8px;
            background: #f8f9ff;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 16px;
            color: #667eea;
            font-weight: 500;
        }

        .file-input-label:hover {
            background: #f0f1ff;
            border-color: #764ba2;
            color: #764ba2;
        }

        .file-input-label i {
            font-size: 2rem;
            margin-right: 10px;
        }

        .progress-container {
            margin-top: 20px;
            display: none;
        }

        .progress {
            height: 25px;
            border-radius: 8px;
            background: #e9ecef;
        }

        .progress-bar {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            font-weight: bold;
            font-size: 12px;
            border-radius: 8px;
            transition: width 0.3s;
        }

        .alert-custom {
            border: none;
            border-radius: 8px;
            margin-top: 20px;
            padding: 15px 20px;
            font-weight: 500;
        }

        .alert-info {
            background: #e7f3ff;
            color: #0066cc;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="main-card">
                    <!-- Header -->
                    <div class="card-header-custom">
                        <i class="bi bi-file-earmark-pdf" style="font-size: 3rem;"></i>
                        <h1 class="mt-3">Dividir PDF por Legajos</h1>
                        <p class="mb-0 mt-2">Sistema automático de separación y personalización de recibos</p>
                    </div>
                    
                    <!-- Body -->
                    <div class="card-body-custom">                        
                        <!-- Form -->
                        <form id="formulario">
                            <div class="row">
                                <!-- Año -->
                                <div class="col-md-6 mb-3">
                                    <label for="anio" class="form-label required-field">Año</label>
                                    <input type="number" class="form-control" id="anio" name="anio" 
                                           value="2025" min="2020" max="2030" required>
                                </div>

                                <!-- Periodo -->
                                <div class="col-md-6 mb-3">
                                    <label for="periodo" class="form-label required-field">Periodo</label>
                                    <select class="form-select" id="periodo" name="periodo" required>
                                        <option value="">Seleccionar mes...</option>
                                        <option value="01">Enero</option>
                                        <option value="02">Febrero</option>
                                        <option value="03">Marzo</option>
                                        <option value="04">Abril</option>
                                        <option value="05">Mayo</option>
                                        <option value="06">Junio</option>
                                        <option value="07">Julio</option>
                                        <option value="08">Agosto</option>
                                        <option value="09">Septiembre</option>
                                        <option value="10">Octubre</option>
                                        <option value="11">Noviembre</option>
                                        <option value="12">Diciembre</option>
                                    </select>
                                </div>

                                <!-- Tipo de Liquidación -->
                                <div class="col-md-6 mb-3">
                                    <label for="tipo_liquidacion" class="form-label required-field">Tipo de Liquidación</label>
                                    <select class="form-select" id="tipo_liquidacion" name="tipo_liquidacion" required>
                                        <option value="">Seleccionar tipo...</option>
                                        <option value="QUINCENAL-Primera Quincena">1ra Quincena</option>
                                        <option value="QUINCENAL-Segunda Quincena">2da Quincena</option>
                                        <option value="MENSUAL">Mensual</option>
                                        <option value="1er-SAC">1er SAC</option>
                                        <option value="2do-SAC">2do SAC</option>
                                        <option value="VACACIONES">Vacaciones</option>
                                    </select>
                                </div>

                                <!-- Tipo de Contratación -->
                                <div class="col-md-6 mb-3">
                                    <label for="tipo_contratacion" class="form-label required-field">Tipo de Contratación</label>
                                    <select class="form-select" id="tipo_contratacion" name="tipo_contratacion" required>
                                        <option value="">Seleccionar tipo...</option>
                                        <option value="MENSUAL_DE_CONVENIO">MENSUAL DE CONVENIO</option>
                                        <option value="MENSUAL_FUERA_DE_CONVENIO">MENSUAL FUERA DE CONVENIO</option>
                                        <option value="QUINCENAL_FABRICA">QUINCENAL FABRICA</option>
                                        <option value="QUINCENAL_CAMPO">QUINCENAL CAMPO</option>                                        
                                    </select>
                                </div>

                                <!-- File Input -->
                                <div class="col-12 mb-3">
                                    <label class="form-label required-field">Archivo PDF</label>
                                    <div class="file-input-wrapper">
                                        <input type="file" id="pdfInput" accept=".pdf">
                                        <label for="pdfInput" class="file-input-label">
                                            <i class="bi bi-cloud-upload"></i>
                                            <span id="fileName">Seleccionar archivo PDF con los recibos</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="progress-container" id="progreso">
                                <div class="progress">
                                    <div class="progress-bar" id="barraFill" role="progressbar" 
                                         style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                        0%
                                    </div>
                                </div>
                            </div>

                            <!-- Status Alert -->
                            <div id="estado" class="alert alert-info alert-custom" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                Complete todos los campos y seleccione el PDF con los recibos
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

        // Actualizar nombre del archivo seleccionado
        document.getElementById('pdfInput').addEventListener('change', async function(event) {
            const file = event.target.files[0];
            if (!file) return;

            document.getElementById('fileName').textContent = file.name;

            // Validar campos
            const anio = document.getElementById('anio').value;
            const periodo = document.getElementById('periodo').value;
            const tipoLiquidacion = document.getElementById('tipo_liquidacion').value;
            const tipoContratacion = document.getElementById('tipo_contratacion').value;

            if (!anio || !periodo || !tipoLiquidacion || !tipoContratacion) {
                mostrarEstado('danger', '❌ Por favor, complete todos los campos');
                event.target.value = '';
                document.getElementById('fileName').textContent = 'Seleccionar archivo PDF con los recibos';
                return;
            }

            try {
                mostrarEstado('info', '<i class="spinner-border spinner-border-sm me-2"></i>Procesando PDF...');
                document.getElementById('progreso').style.display = 'block';
                
                await procesarPDF(file, anio, periodo, tipoLiquidacion, tipoContratacion);
                
                mostrarEstado('success', '✅ ¡Proceso completado! El archivo ZIP se ha descargado correctamente');
                document.getElementById('progreso').style.display = 'none';
            } catch (error) {
                console.error('Error:', error);
                mostrarEstado('danger', '❌ Error: ' + error.message);
                document.getElementById('progreso').style.display = 'none';
            }
        });

        function mostrarEstado(tipo, mensaje) {
            const estado = document.getElementById('estado');
            estado.className = `alert alert-${tipo} alert-custom`;
            estado.innerHTML = mensaje;
        }

        function actualizarProgreso(actual, total) {
            const porcentaje = Math.round((actual / total) * 100);
            const barraFill = document.getElementById('barraFill');
            barraFill.style.width = porcentaje + '%';
            barraFill.setAttribute('aria-valuenow', porcentaje);
            barraFill.textContent = `${actual}/${total} (${porcentaje}%)`;
        }

        /**
         * Calcula el legajo limpio según las reglas:
         * - Si tiene >= 5 dígitos Y comienza con 50 o 60 → quita los 2 primeros
         * - Si tiene < 5 dígitos → mantiene como está (incluso si comienza con 50 o 60)
         * - Si no comienza con 50 o 60 → mantiene como está
         */
        function calcularLegajoLimpio(legajoOriginal) {
            console.log(`📋 Procesando legajo: ${legajoOriginal}`);
            
            // Si tiene menos de 5 dígitos, mantener como está
            if (legajoOriginal.length < 5) {
                console.log(`   → Legajo corto (${legajoOriginal.length} dígitos): se mantiene como ${legajoOriginal}`);
                return legajoOriginal;
            }
            
            // Si tiene 5+ dígitos y comienza con 50 o 60
            if ((legajoOriginal.startsWith('50') || legajoOriginal.startsWith('60'))) {
                const legajoLimpio = legajoOriginal.substring(2);
                console.log(`   → Legajo largo (${legajoOriginal.length} dígitos): ${legajoOriginal} → ${legajoLimpio}`);
                return legajoLimpio;
            }
            
            // En cualquier otro caso, mantener como está
            console.log(`   → Legajo sin prefijo 50/60: se mantiene como ${legajoOriginal}`);
            return legajoOriginal;
        }

        /**
         * Reemplaza el legajo en el PDF modificando su contenido de texto
         * Busca el legajo original en el PDF y lo reemplaza por el nuevo
         */
        async function reemplazarLegajoEnPDF(pdfDoc, legajoOriginal, legajoNuevo) {
            try {
                // Obtener todas las páginas del documento
                const pages = pdfDoc.getPages();
                
                // Iterar sobre cada página
                for (const page of pages) {
                    // Obtener los streams de contenido de la página
                    const { Resources } = page.node.asdict();
                    
                    // Si no hay recursos, pasar a siguiente página
                    if (!Resources) continue;
                    
                    const font = Resources.get('Font');
                    if (!font) continue;
                    
                    // Obtener el stream de contenido
                    const contentStream = page.getContents();
                    if (!contentStream) continue;
                    
                    let content = await contentStream.decode();
                    let contentStr = new TextDecoder().decode(content);
                    
                    // Buscar y reemplazar el legajo original por el nuevo
                    // Manejar espacios posibles dentro del número
                    const legajoPattern = legajoOriginal.split('').join('\\s*');
                    const regex = new RegExp(legajoPattern, 'g');
                    
                    if (regex.test(contentStr)) {
                        console.log(`   ✅ Reemplazando en página: ${legajoOriginal} → ${legajoNuevo}`);
                        contentStr = contentStr.replace(regex, legajoNuevo);
                        
                        // Actualizar el contenido de la página
                        page.setContents(PDFLib.PDFRawStream.of(
                            pdfDoc,
                            new TextEncoder().encode(contentStr)
                        ));
                    }
                }
                
                return pdfDoc;
            } catch (error) {
                console.warn(`⚠️ No se pudo reemplazar en stream: ${error.message}`);
                // No fallar si no se puede hacer el reemplazo en stream
                return pdfDoc;
            }
        }

        async function procesarPDF(file, anio, periodo, tipoLiquidacion, tipoContratacion) {
            const arrayBuffer = await file.arrayBuffer();
            const arrayBufferCopy = arrayBuffer.slice(0);
            
            const pdfJs = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
            const pdfLibDoc = await PDFLib.PDFDocument.load(arrayBufferCopy);
            
            const totalPaginas = pdfJs.numPages;
            const zip = new JSZip();
            let archivosCreados = 0;
            let archivosSinLegajo = 0;

            for (let numPagina = 1; numPagina <= totalPaginas; numPagina++) {
                try {
                    actualizarProgreso(numPagina, totalPaginas);
                    
                    const paginaJs = await pdfJs.getPage(numPagina);
                    const contenido = await paginaJs.getTextContent();
                    
                    let textoCompleto = '';
                    contenido.items.forEach(item => {
                        textoCompleto += item.str + ' ';
                    });

                    console.log(`\n📄 Página ${numPagina}:`);
                    console.log(`   Texto: ${textoCompleto.substring(0, 100)}...`);

                    let legajoEncontrado = null;

                    // ✅ ESTRATEGIA ÚNICA Y PRECISA: Buscar "Legajo" seguido de números
                    // Captura: "Legajo 500979" o "Legajo: 500979" o "Legajo     500979"
                    // El patrón es: Legajo + espacios/dos puntos + números (3-6 dígitos)
                    const matchLegajo = textoCompleto.match(/Legajo\s*:?\s+(\d{3,6})(?:\s|$)/i);
                    
                    if (matchLegajo) {
                        const legajoJunto = matchLegajo[1].trim();
                        console.log(`   ✅ Legajo capturado directamente: "${legajoJunto}"`);
                        
                        // Validar que no sea un número común
                        if (!esNumeroComun(legajoJunto, anio)) {
                            legajoEncontrado = legajoJunto;
                            console.log(`   ✅ Legajo encontrado (PRECISO): ${legajoEncontrado}`);
                        } else {
                            console.log(`   ⚠️ Número capturado pero es común: ${legajoJunto}`);
                        }
                    } else {
                        console.log(`   ❌ No se encontró patrón "Legajo XXX"`);
                    }
                    
                    // ESTRATEGIA 2 (SOLO SI FALLÓ LA PRIMERA): Fallback más conservador
                    if (!legajoEncontrado) {
                        console.log(`   🔍 Intentando estrategia fallback...`);
                        const todosNumeros = textoCompleto.match(/\d+/g) || [];
                        
                        // Filtrar números sospechosos (muy cortos o muy largos)
                        const candidatos = todosNumeros.filter(num => 
                            num.length >= 4 && 
                            num.length <= 6 && 
                            !esNumeroComun(num, anio)
                        );
                        
                        console.log(`   Candidatos filtrados: ${candidatos.join(', ')}`);
                        
                        if (candidatos.length > 0) {
                            // Prioridad: números que comienzan con 5 o 6 (típicos prefijos de legajo)
                            const legajoConPrefijo = candidatos.find(n => n.startsWith('5') || n.startsWith('6'));
                            legajoEncontrado = legajoConPrefijo || candidatos[0];
                            console.log(`   ⚠️ Legajo asignado (fallback): ${legajoEncontrado}`);
                        }
                    }

                    if (legajoEncontrado) {
                        // ✅ PASO 1: Calcular el legajo limpio según las reglas
                        const legajoLimpio = calcularLegajoLimpio(legajoEncontrado);
                        
                        // ✅ PASO 2: Crear nuevo PDF y copiar la página
                        const nuevoPdf = await PDFLib.PDFDocument.create();
                        const [paginaCopiada] = await nuevoPdf.copyPages(pdfLibDoc, [numPagina - 1]);
                        nuevoPdf.addPage(paginaCopiada);
                        
                        // ✅ PASO 3: REEMPLAZAR el legajo en el PDF si es diferente
                        if (legajoEncontrado !== legajoLimpio) {
                            console.log(`   🔄 Reemplazando legajo en PDF...`);
                            await reemplazarLegajoEnPDF(nuevoPdf, legajoEncontrado, legajoLimpio);
                        }
                        
                        // ✅ PASO 4: Guardar el PDF modificado
                        const pdfBytes = await nuevoPdf.save();
                        
                        // ✅ PASO 5: Agregar al ZIP con el nombre correcto
                        const nombreArchivo = `${legajoLimpio}-${anio}${periodo}-CAT-${tipoLiquidacion}-${tipoContratacion}.pdf`;
                        console.log(`   💾 Archivo: ${nombreArchivo}`);
                        zip.file(nombreArchivo, pdfBytes);
                        archivosCreados++;
                    } else {
                        console.log(`   ❌ Sin legajo detectado`);
                        const nombreArchivo = `sin_legajo_pagina_${numPagina}-${anio}${periodo}-CAT-${tipoLiquidacion}-${tipoContratacion}.pdf`;
                        
                        const nuevoPdf = await PDFLib.PDFDocument.create();
                        const [paginaCopiada] = await nuevoPdf.copyPages(pdfLibDoc, [numPagina - 1]);
                        nuevoPdf.addPage(paginaCopiada);
                        const pdfBytes = await nuevoPdf.save();
                        
                        zip.file(nombreArchivo, pdfBytes);
                        archivosSinLegajo++;
                    }

                } catch (errorPagina) {
                    console.error(`❌ Error en página ${numPagina}:`, errorPagina);
                }
            }
            
            console.log(`\n📊 Resumen: ${archivosCreados} con legajo, ${archivosSinLegajo} sin legajo`);
            
            mostrarEstado('info', '<i class="spinner-border spinner-border-sm me-2"></i>Generando archivo ZIP...');
            const zipBlob = await zip.generateAsync({ type: 'blob' });
            
            const url = URL.createObjectURL(zipBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `Recibos_${anio}${periodo}_${tipoLiquidacion}_${tipoContratacion}.zip`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        function esNumeroComun(numero, anio) {
            const numerosComunes = [
                '2020', '2021', '2022', '2023', '2024', '2025', '2026', '2027',
                anio,
                '01', '02', '03', '04', '05', '06', '07', '08', '09',
                '10', '11', '12', '30', '31',
                '1', '2', '3', '4', '5'
            ];
            return numerosComunes.includes(numero);
        }
    </script>
</body>
</html>
