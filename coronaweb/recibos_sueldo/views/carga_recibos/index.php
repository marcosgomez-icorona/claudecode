  
<!-- Encabezado -->
<div class="text-center my-4">
  <h1 class="display-4 text-center text-muted">Carga de Recibos de Sueldo</h1>  
</div>
<hr>
<div class="container">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4">
      <!-- Formulario -->
      <form id="form-recibos" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="archivos" class="form-label fw-semibold">
            <i class="bi bi-file-earmark-pdf text-danger"></i> Seleccionar archivos PDF:
          </label>
          <input 
            class="form-control" 
            type="file" 
            name="archivos[]" 
            id="archivos" 
            accept=".pdf" 
            multiple 
            required
          >
          <div class="form-text">Puede seleccionar varios archivos manteniendo presionada la tecla Ctrl o Shift.</div>
        </div>

        <!-- Botón -->
        <div class="d-grid">
          <button type="submit" class="btn btn-primary btn-lg" id="btn-subir">
            <i class="bi bi-cloud-upload"></i> Subir Recibos
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Barra de progreso -->
  <div class="mt-4">
    <div class="progress" style="height: 25px;">
      <div 
        id="barra-progreso" 
        class="progress-bar progress-bar-striped progress-bar-animated" 
        role="progressbar" 
        style="width: 0%;" 
        aria-valuenow="0" 
        aria-valuemin="0" 
        aria-valuemax="100">
        0%
      </div>
    </div>
    <div id="status" class="mt-2 text-center text-muted"></div>
  </div>
</div>

<script>
const form = document.getElementById('form-recibos');
const input = document.getElementById('archivos');
const barra = document.getElementById('barra-progreso');
const statusDiv = document.getElementById('status');
const btn = document.getElementById('btn-subir');

function chunkArray(arr, size) {
  const result = [];
  for (let i = 0; i < arr.length; i += size) {
    result.push(arr.slice(i, i + size));
  }
  return result;
}

// Construir URL base dinámica (ajustá si la ruta real difiere)
const usuarioParam = new URLSearchParams(window.location.search).get('usuario') || '';
const uploadUrlBase = `views/carga_recibos/subir_recibos.php?usuario=${encodeURIComponent(usuarioParam)}`;

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  if (!input.files.length) return;

  btn.disabled = true;
  statusDiv.innerHTML = '';
  barra.style.width = '0%';
  barra.textContent = '0%';

  const files = Array.from(input.files);
  const batches = chunkArray(files, 20);
  const totalBatches = batches.length;
  let batchIndex = 0;

  const resultados = { subidos: [], errores: [] };

  for (const batch of batches) {
    batchIndex++;
    const fd = new FormData();
    batch.forEach(f => fd.append('archivos[]', f));

    try {
      const resp = await fetch(uploadUrlBase, {
        method: 'POST',
        body: fd,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const text = await resp.text();

      if (!resp.ok) {
        resultados.errores.push(`❌ Lote ${batchIndex}: HTTP ${resp.status}`);
        statusDiv.innerHTML += `<div style="color: darkred;">❌ Lote ${batchIndex} HTTP ${resp.status}</div><pre>${text.replace(/</g,'&lt;')}</pre>`;
        continue;
      }

      let json;
      try {
        json = JSON.parse(text);
      } catch (parseErr) {
        resultados.errores.push(`❌ Lote ${batchIndex}: respuesta no JSON: ${parseErr.message}`);
        statusDiv.innerHTML += `<div style="color: darkred;">❌ Lote ${batchIndex} parse error: ${parseErr.message}</div><pre>${text.replace(/</g,'&lt;')}</pre>`;
        continue;
      }

      if (Array.isArray(json.subidos)) resultados.subidos.push(...json.subidos);
      if (Array.isArray(json.errores)) resultados.errores.push(...json.errores);

      statusDiv.innerHTML += `<div>✅ Lote ${batchIndex}/${totalBatches} procesado.</div>`;
    } catch (err) {
      resultados.errores.push(`❌ Error de red en lote ${batchIndex}: ${err.message}`);
      statusDiv.innerHTML += `<div style="color: darkred;">❌ Lote ${batchIndex} falló: ${err.message}</div>`;
    }

    const percent = Math.round((batchIndex / totalBatches) * 100);
    barra.style.width = percent + '%';
    barra.textContent = percent + '%';

    // breve pausa opcional para no saturar
    await new Promise(r => setTimeout(r, 100));
  }

  // resumen final
  let resumen = '<hr>';
  if (resultados.subidos.length) {
    resumen += '<div><strong>Subidos:</strong><ul>';
    resultados.subidos.forEach(s => { resumen += `<li>${s}</li>`; });
    resumen += '</ul></div>';
  }
  if (resultados.errores.length) {
    resumen += '<div><strong>Errores:</strong><ul>';
    resultados.errores.forEach(e => { resumen += `<li>${e}</li>`; });
    resumen += '</ul></div>';
  }
  statusDiv.innerHTML += resumen;
  btn.disabled = false;
});
</script>
