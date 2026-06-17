# Fallback API + Standalone — Dashboard Sumas y Saldos — Plan de Implementación

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Convertir el `index.html` en un archivo standalone con fetch a 2 endpoints Node-RED con fallback automático y mock data como última instancia.

**Architecture:** Se modifica el `index.html` existente (2339 líneas, todo inline). Se reemplaza `CONFIG.nodeRedUrl` por `API_PRIMARY` + `API_FALLBACK`, se agrega `fetchWithFallback()` con `AbortController` para timeout 15s, se refactoriza `loadFromNodeRED()` → `loadFromAPI()` y `verMovimientos()`, y se agrega un indicador visual en el sidebar footer. No se modifica nada en Node-RED.

**Tech Stack:** JavaScript vanilla (ES6+), Fetch API, AbortController

## Global Constraints

- Todo el código va inline en `gerencia_sumas_y_saldos/index.html`
- Compatible con navegadores modernos (Chrome 66+, Firefox 57+, Safari 12.1+, Edge 79+)
- No se agregan dependencias externas
- Los endpoints Node-RED no cambian
- El mock data embebido se mantiene como fallback final

---

## File Structure

| Archivo | Cambio |
|---------|--------|
| `gerencia_sumas_y_saldos/index.html` | Modificaciones localizadas en 7 secciones |

### Secciones a modificar (líneas exactas)

| # | Sección | Líneas | Cambio |
|---|---------|--------|--------|
| 1 | CSS sidebar-footer + sync-dot | 142-167 | Agregar clases `.dot-green`, `.dot-amber`, `.dot-gray` |
| 2 | HTML sidebar-footer | 789-795 | Agregar `#api-status` con dot + label |
| 3 | HTML botón Consultar | 868-871 | Cambiar `loadFromNodeRED()` → `loadFromAPI()` |
| 4 | JS CONFIG | 1438-1449 | Nuevo CONFIG con API_PRIMARY, API_FALLBACK, API_TIMEOUT_MS |
| 5 | JS fetchWithFallback() | después de CONFIG | Nueva función helper |
| 6 | JS loadFromNodeRED() | 1721-1771 | Refactor a loadFromAPI() con fallback |
| 7 | JS verMovimientos() | 1591-1637 | Agregar fetch con fallback |
| 8 | JS processData() | 1805-1808 | Ref: `CONFIG.useNodeRed` eliminado |
| 9 | JS init() | 2320-2335 | Ref: usa loadFromAPI() |

---

### Task 1: Nueva sección CSS para indicador de estado

**Files:**
- Modify: `gerencia_sumas_y_saldos/index.html:142-167`

- [ ] **Step 1: Agregar clases CSS para los colores del dot**

Reemplazar el bloque `.sync-indicator` / `.sync-dot` / `@keyframes pulse` (líneas 150-167) con versiones que soporten 3 estados:

```css
.sync-indicator {
  display: flex;
  align-items: center;
  gap: 6px;
}

.sync-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--corona-green);
  animation: pulse 2s infinite;
}

.sync-dot.dot-amber { background: var(--accent-amber); }
.sync-dot.dot-gray  { background: var(--text-muted); animation: none; }
.sync-dot.dot-green { background: var(--corona-green); }

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.3; }
}
```

- [ ] **Step 2: Verificar**

El archivo se ve bien. No hay comando para testing visual.

- [ ] **Step 3: Commit**

```bash
git add gerencia_sumas_y_saldos/index.html
git commit -m "chore: add CSS classes for API status indicator (dot-green/dot-amber/dot-gray)"
```

---

### Task 2: HTML sidebar — indicador de estado de API

**Files:**
- Modify: `gerencia_sumas_y_saldos/index.html:789-795`

- [ ] **Step 1: Reemplazar sidebar-footer**

Reemplazar desde `<div class="sidebar-footer">` hasta el cierre `</aside>` (líneas 789-796):

```html
  <div class="sidebar-footer">
    <div class="sync-indicator">
      <div class="sync-dot dot-green" id="api-dot"></div>
      <span id="api-label">Conectando...</span>
    </div>
    <div class="sync-indicator" style="margin-top:4px;">
      <span id="api-sub-label" style="color:var(--text-muted);font-size:10px;"></span>
    </div>
    <div style="margin-top:6px;">v1.0.0 — Zafra 2026</div>
  </div>
</aside>
```

- [ ] **Step 2: Commit**

```bash
git add gerencia_sumas_y_saldos/index.html
git commit -m "feat: add API status indicator to sidebar footer"
```

---

### Task 3: HTML botón Consultar — actualizar función llamada

**Files:**
- Modify: `gerencia_sumas_y_saldos/index.html:868`

- [ ] **Step 1: Cambiar onclick del botón "Consultar Node-RED"**

Reemplazar:
```html
<button class="btn-primary-custom" style="background:var(--corona-green);" onclick="loadFromNodeRED()">
```
por:
```html
<button class="btn-primary-custom" style="background:var(--corona-green);" onclick="loadFromAPI()">
```

- [ ] **Step 2: Commit**

```bash
git add gerencia_sumas_y_saldos/index.html
git commit -m "chore: rename loadFromNodeRED to loadFromAPI in button"
```

---

### Task 4: CONFIG — nuevo esquema de configuración

**Files:**
- Modify: `gerencia_sumas_y_saldos/index.html:1438-1449`

- [ ] **Step 1: Reemplazar el objeto CONFIG**

Reemplazar desde `const CONFIG = {` hasta `};` (líneas 1438-1449):

```javascript
const CONFIG = {
  criticalThreshold: 10_000_000,
  variationThreshold: 30,
  startDate: '20260601',
  endDate: '20260630',
  currency: 'ARS',
  locale: 'es-AR',

  // Endpoints Node-RED con fallback
  API_PRIMARY:   'http://ingcorona.ddns.net:4040',
  API_FALLBACK:  'http://192.168.0.23:1880',
  API_TIMEOUT_MS: 15000,
  endpoints: {
    sumasSaldos:   '/api/sumas-saldos',
    movimientos:   '/api/sumas-saldos/movimientos',
  },
};
```

- [ ] **Step 2: Commit**

```bash
git add gerencia_sumas_y_saldos/index.html
git commit -m "feat: replace nodeRedUrl with API_PRIMARY + API_FALLBACK + endpoints map"
```

---

### Task 5: fetchWithFallback() — helper de fetch con timeout y failover

**Files:**
- Modify: `gerencia_sumas_y_saldos/index.html` (después del CONFIG, antes de STATE)

- [ ] **Step 1: Agregar función fetchWithFallback + updateApiStatus después del CONFIG**

Insertar después de la línea `};` del CONFIG (línea 1449) y antes de `/* ═══════════ ESTADO GLOBAL */` (línea 1451):

```javascript
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
```

- [ ] **Step 2: Commit**

```bash
git add gerencia_sumas_y_saldos/index.html
git commit -m "feat: add fetchWithFallback() with AbortController timeout and updateApiStatus()"
```

---

### Task 6: loadFromNodeRED() → loadFromAPI() — refactor con fallback

**Files:**
- Modify: `gerencia_sumas_y_saldos/index.html:1721-1771`

- [ ] **Step 1: Reemplazar la función loadFromNodeRED()**

Reemplazar desde `async function loadFromNodeRED() {` hasta el cierre `}` antes de `/* ═══════════ CARGAR DATOS DEMO */` (líneas 1721-1771):

```javascript
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
  document.getElementById('upload-label').textContent = 'Consultando...';
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
    renderAll();
    var total = result.data._metadata?.totalCuentas || result.data.sumasSaldos?.length || 0;
    var label = result.source === 'primary' ? 'ingcorona.ddns.net' : '192.168.0.23';
    document.getElementById('upload-label').textContent = label + ': ' + total + ' cuentas';
    document.getElementById('kpi-saldo-delta').textContent = 'Actualizado: ' + new Date().toLocaleTimeString('es-AR');
    if (result.source === 'fallback') {
      showToast('Conectado vía fallback LAN (' + total + ' cuentas)', 'warn');
    } else {
      showToast('Datos desde ' + label + ' (' + total + ' cuentas)', 'ok');
    }
  } catch (e) {
    console.warn('loadFromAPI: ambos endpoints fallaron —', e.message);
    document.getElementById('upload-label').textContent = 'Sin conexión';
    document.getElementById('top-accounts-body').innerHTML = '<tr><td colspan="7"><div class="empty-state"><h3 style="color:var(--accent-red);">⚠ Sin conexión</h3><p>No se pudo conectar con Node-RED (primary ni fallback).</p><p style="font-size:11px;margin-top:8px;">Cargando datos demo para visualización offline.</p></div></td></tr>';
    document.getElementById('ss-body').innerHTML = '<tr><td colspan="8"><div class="empty-state"><h3 style="color:var(--accent-red);">Sin datos</h3></div></td></tr>';
    document.getElementById('kpi-saldo-total').textContent = '—';
    document.getElementById('kpi-crit-count').textContent = '—';
    document.getElementById('kpi-alert-count').textContent = '—';
    showToast('Sin conexión. Cargando datos demo.', 'err');
    loadMockData();
  }
}
```

- [ ] **Step 2: Commit**

```bash
git add gerencia_sumas_y_saldos/index.html
git commit -m "feat: refactor loadFromNodeRED() to loadFromAPI() with fetchWithFallback"
```

---

### Task 7: verMovimientos() — agregar fetch con fallback

**Files:**
- Modify: `gerencia_sumas_y_saldos/index.html:1591-1637`

- [ ] **Step 1: Reemplazar la función verMovimientos()**

Reemplazar desde `function verMovimientos(codigo, cuenta) {` hasta el cierre `}` antes del comentario de FILTROS (líneas 1591-1637):

```javascript
function verMovimientos(codigo, cuenta) {
  var modal = new bootstrap.Modal(document.getElementById('movimientosModal'));
  document.getElementById('modal-title').textContent = 'Movimientos: ' + codigo + ' - ' + (cuenta || '');
  document.getElementById('movimientos-loading').style.display = '';
  document.getElementById('movimientos-content').style.display = 'none';
  document.getElementById('movimientos-filtros').style.display = 'none';
  document.getElementById('movimientos-error').style.display = 'none';
  limpiarFiltrosMovimientos();
  modal.show();

  fetchWithFallback(CONFIG.endpoints.movimientos, { codigo: codigo.trim() })
    .then(function(result){
      var data = result.data;
      document.getElementById('movimientos-loading').style.display = 'none';
      if (!data.success || !data.movimientos || data.movimientos.length === 0) {
        document.getElementById('movimientos-error').style.display = '';
        document.getElementById('movimientos-error').textContent = 'Sin movimientos para esta cuenta.';
        return;
      }
      document.getElementById('movimientos-content').style.display = '';
      document.getElementById('movimientos-filtros').style.display = '';
      var tbody = document.getElementById('movimientos-body');
      tbody.innerHTML = '';
      data.movimientos.forEach(function(m){
        var tr = document.createElement('tr');
        tr.innerHTML = '<td class="code">' + (m.fecha || '') + '</td>' +
          '<td class="code">' + (m.asiento || '') + '</td>' +
          '<td>' + (m.descripcion || '') + '</td>' +
          '<td>' + (m.proveedor || '') + '</td>' +
          '<td class="num">' + (m.debe ? fmtMoney(m.debe) : '—') + '</td>' +
          '<td class="num">' + (m.haber ? fmtMoney(m.haber) : '—') + '</td>' +
          '<td class="num"><strong>' + fmtMoney(m.saldo) + '</strong></td>';
        tbody.appendChild(tr);
      });
      document.getElementById('mov-count').textContent = data.total;
      document.getElementById('mov-total-debe').textContent = fmtMoney(data.totalDebe);
      document.getElementById('mov-total-haber').textContent = fmtMoney(data.totalHaber);
      document.getElementById('mov-saldo').textContent = fmtMoney(data.saldoTotal);
    })
    .catch(function(err){
      document.getElementById('movimientos-loading').style.display = 'none';
      document.getElementById('movimientos-error').style.display = '';
      document.getElementById('movimientos-error').textContent = 'Error: ' + err.message;
    });
}
```

- [ ] **Step 2: Commit**

```bash
git add gerencia_sumas_y_saldos/index.html
git commit -m "feat: verMovimientos() now uses fetchWithFallback() for failover"
```

---

### Task 8: processData() — eliminar referencia a CONFIG.useNodeRed

**Files:**
- Modify: `gerencia_sumas_y_saldos/index.html:1805-1808`

- [ ] **Step 1: Simplificar processData()**

Reemplazar desde `function processData() {` hasta el cierre `}` (líneas 1805-1817):

```javascript
function processData() {
  // Siempre intentar API primero
  loadFromAPI();
}
```

- [ ] **Step 2: Commit**

```bash
git add gerencia_sumas_y_saldos/index.html
git commit -m "refactor: processData() simplified to always call loadFromAPI()"
```

---

### Task 9: init() — refactor para usar loadFromAPI()

**Files:**
- Modify: `gerencia_sumas_y_saldos/index.html:2320-2335`

- [ ] **Step 1: Reemplazar init()**

Reemplazar desde `(function init() {` hasta el cierre `})();` (líneas 2320-2335):

```javascript
(async function init() {
  document.getElementById('uuid').textContent = crypto.randomUUID().slice(0,8).toUpperCase();
  document.getElementById('date-footer').textContent = new Date().toLocaleDateString('es-AR', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' });
  document.getElementById('badge-period').textContent = 'Conectando...';

  // Fechas por defecto al mes actual
  var now = new Date();
  var y = now.getFullYear();
  var m = String(now.getMonth() + 1).padStart(2, '0');
  var lastDay = new Date(y, now.getMonth() + 1, 0).getDate();
  document.getElementById('startDate').value = y + '-' + m + '-01';
  document.getElementById('endDate').value = y + '-' + m + '-' + String(lastDay).padStart(2, '0');

  // Auto-cargar desde API (con fallback automático a demo)
  await loadFromAPI();
})();
```

- [ ] **Step 2: Commit**

```bash
git add gerencia_sumas_y_saldos/index.html
git commit -m "feat: init() ahora usa loadFromAPI() con await para carga inicial"
```

---

## Self-Review

**1. Spec coverage:**
- CONFIG nuevo con API_PRIMARY/API_FALLBACK → Task 4
- fetchWithFallback() con AbortController → Task 5
- loadFromNodeRED() → loadFromAPI() con fallback → Task 6
- verMovimientos() con fallback → Task 7
- Indicador de estado en sidebar → Task 1 (CSS) + Task 2 (HTML) + Task 5 (JS function)
- Mock data como fallback final → Task 6 (catch en loadFromAPI llama a loadMockData())
- Botón Consultar actualizado → Task 3
- processData() simplificado → Task 8
- init() actualizado → Task 9

**2. Placeholder scan:** Sin TBDs, TODOs, o secciones incompletas. Cada paso tiene código real.

**3. Type consistency:** 
- `fetchWithFallback()` devuelve `{ data, source }` — usado igual en tasks 6 y 7
- `updateApiStatus(source, errorMsg)` — llamado desde fetchWithFallback y loadFromAPI
- `CONFIG.endpoints.sumasSaldos` y `CONFIG.endpoints.movimientos` — consistentes

No se encontraron issues.
