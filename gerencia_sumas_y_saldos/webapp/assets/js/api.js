/* API — fetch con fallback, carga desde API, carga demo */

/* ════════════════════════════════════════════════
   API HELPER — fetch con timeout y fallback
════════════════════════════════════════════════ */
const API_SOURCE = { current: null };

function updateApiStatus(source, errorMsg) {
  var dot = document.getElementById('api-dot');
  var label = document.getElementById('api-label');
  var sub = document.getElementById('api-sub-label');
  if (!dot) return;

  dot.className = 'sync-dot';

  switch (source) {
    case 'primary':
      dot.classList.add('dot-green');
      label.textContent = 'Online — ingcorona.ddns.net:4040';
      label.style.color = 'var(--corona-green-dark)';
      if (sub) sub.textContent = 'Conectado vía internet';
      break;
    case 'fallback':
      dot.classList.add('dot-amber');
      label.textContent = 'Fallback — 192.168.0.23:1880';
      label.style.color = 'var(--accent-amber)';
      if (sub) sub.textContent = 'Conectado vía LAN local';
      break;
    case 'mock':
      dot.classList.add('dot-gray');
      label.textContent = 'Offline — datos demo';
      label.style.color = 'var(--text-muted)';
      if (sub) sub.textContent = errorMsg || 'Sin conexión con Node-RED';
      break;
    default:
      dot.classList.add('dot-gray');
      label.textContent = 'Conectando...';
      label.style.color = 'var(--text-muted)';
      if (sub) sub.textContent = '';
  }
  API_SOURCE.current = source;
}

async function fetchWithFallback(endpoint, params) {
  var urls = [
    { url: CONFIG.API_PRIMARY + endpoint, name: 'primary' },
    { url: CONFIG.API_FALLBACK + endpoint, name: 'fallback' },
  ];

  var lastError = null;
  for (var i = 0; i < urls.length; i++) {
    var u = urls[i];
    var fullUrl = u.url + '?' + new URLSearchParams(params);
    try {
      var controller = new AbortController();
      var timeout = setTimeout(function() { controller.abort(); }, CONFIG.API_TIMEOUT_MS);
      var res = await fetch(fullUrl, { signal: controller.signal });
      clearTimeout(timeout);

      if (!res.ok) {
        var errBody = await res.json().catch(function() { return { error: res.statusText }; });
        throw new Error(errBody.error || res.statusText);
      }

      var data = await res.json();
      updateApiStatus(u.name);
      return { data: data, source: u.name };
    } catch (e) {
      lastError = e;
      console.warn('fetchWithFallback: ' + u.name + ' falló — ' + e.message);
    }
  }

  // Ambos endpoints fallaron
  updateApiStatus('mock', lastError ? lastError.message : 'Sin conexión');
  throw lastError || new Error('Todos los endpoints fallaron');
}

/* ════════════════════════════════════════════════
   CARGAR DESDE API (con fallback)
════════════════════════════════════════════════ */
async function loadFromAPI() {
  var sd = document.getElementById('startDate').value;
  var ed = document.getElementById('endDate').value;
  sd = sd ? sd.replace(/-/g, '') : CONFIG.startDate;
  ed = ed ? ed.replace(/-/g, '') : CONFIG.endDate;
  var ct = document.getElementById('criticalThreshold').value || CONFIG.criticalThreshold;

  // Estados de carga
  document.getElementById('top-accounts-body').innerHTML = '<tr><td colspan="7"><div class="empty-state"><h3>Consultando... 🔄</h3><p>Esperando respuesta del servidor</p></div></td></tr>';
  document.getElementById('ss-body').innerHTML = '<tr><td colspan="9"><div class="empty-state"><h3>Consultando...</h3></div></td></tr>';
  document.getElementById('kpi-saldo-total').textContent = '···';
  document.getElementById('kpi-crit-count').textContent = '···';
  document.getElementById('kpi-alert-count').textContent = '···';
  updateApiStatus(null);

  try {
    var result = await fetchWithFallback(CONFIG.endpoints.sumasSaldos, {
      startDate: sd,
      endDate: ed,
      criticalThreshold: ct
    });
    STATE.data = result.data;
    populateEmpresaFilter();
    renderAll();
    var total = result.data._metadata?.totalCuentas || result.data.sumasSaldos?.length || 0;
    var label = result.source === 'primary' ? 'ingcorona.ddns.net' : '192.168.0.23';
    document.getElementById('kpi-saldo-delta').textContent = 'Actualizado: ' + new Date().toLocaleTimeString('es-AR');
    if (result.source === 'fallback') {
      showToast('Conectado vía fallback LAN (' + total + ' cuentas)', 'warn');
    } else {
      showToast('Datos desde ' + label + ' (' + total + ' cuentas)', 'ok');
    }
  } catch (e) {
    console.warn('loadFromAPI: ambos endpoints fallaron —', e.message);
    document.getElementById('top-accounts-body').innerHTML = '<tr><td colspan="7"><div class="empty-state"><h3 style="color:var(--accent-red);">⚠ Sin conexión</h3><p>No se pudo conectar con Node-RED (primary ni fallback).</p><p style="font-size:11px;margin-top:8px;">Cargando datos demo para visualización offline.</p></div></td></tr>';
    document.getElementById('ss-body').innerHTML = '<tr><td colspan="8"><div class="empty-state"><h3 style="color:var(--accent-red);">Sin datos</h3></div></td></tr>';
    document.getElementById('kpi-saldo-total').textContent = '—';
    document.getElementById('kpi-crit-count').textContent = '—';
    document.getElementById('kpi-alert-count').textContent = '—';
    showToast('Sin conexión. Cargando datos demo.', 'err');
    loadMockData();
  }
}

/* ════════════════════════════════════════════════
   CARGAR DATOS DEMO
════════════════════════════════════════════════ */
function loadMockData() {
  STATE.data = MOCK;
  populateEmpresaFilter();
  renderAll();
  showToast('Datos demo cargados correctamente', 'ok');
}

/* ════════════════════════════════════════════════
   CARGAR DATOS (entry point)
════════════════════════════════════════════════ */
function processData() {
  // Siempre intentar API primero
  loadFromAPI();
}
