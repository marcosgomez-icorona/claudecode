# Dashboard Conciliación Bancaria — Standalone HTML Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Extraer el dashboard HTML del template Node-RED a un `index.html` standalone con failover cloud→LAN y configuración externa.

**Architecture:** `index.html` + `config.js` abiertos desde filesystem (`file://`), con `apiFetch()` que prueba backends en orden (cloud → LAN) y muestra badge del backend activo.

**Tech Stack:** HTML5, JavaScript (vanilla, ES6+), Bootstrap 5 (CDN), Chart.js 4 (CDN)

## Global Constraints

- Cero dependencias de servidor — el HTML debe funcionar abierto directo desde filesystem
- API base URL resuelta con failover en JS (no hardcodeada en HTML)
- CORS cubierto por `Access-Control-Allow-Origin: *` existente en Node-RED
- Sin cambios en los flows Node-RED existentes
- Sin modificar el endpoint `GET /conciliacion` original (se conserva como respaldo)

---

### Task 1: Crear `dashboard/config.js`

**Files:**
- Create: `proyectos-ingenio/conciliacion bancaria/dashboard/config.js`

**Interfaces:**
- Consumes: N/A
- Produces: `CONFIG.backends[]` con `{name, url, timeout}`

- [ ] **Step 1: Crear archivo `config.js` con la configuración de backends**

```js
/**
 * Configuración de backends Node-RED para el Dashboard de Conciliación Bancaria
 * 
 * El dashboard intenta conectar en orden: cloud → LAN
 * Si el primario (cloud) falla en <timeout>ms, pasa al secundario (LAN)
 */
const CONFIG = {
  backends: [
    {
      name: 'Cloud',
      url: 'http://ingcorona.ddns.net:4040',
      timeout: 5000
    },
    {
      name: 'LAN',
      url: 'http://192.168.0.23:1880',
      timeout: 5000
    }
  ]
};
```

- [ ] **Step 2: Commit**

```bash
git add "proyectos-ingenio/conciliacion bancaria/dashboard/config.js"
git commit -m "feat: config.js con backends cloud y LAN para dashboard conciliacion"
```

---

### Task 2: Crear `dashboard/index.html`

**Files:**
- Create: `proyectos-ingenio/conciliacion bancaria/dashboard/index.html`

**Interfaces:**
- Consumes: `CONFIG` (de config.js), APIs Node-RED: `/api/conciliacion/resumen`, `/pendientes`, `/detalle`
- Produces: Dashboard visual completo con 5 secciones (resumen, pendientes, detalle, gastos, gráficos)

- [ ] **Step 1: Crear `index.html` con estructura completa extraída del template Node-RED**

El HTML es el mismo template que actualmente sirve Node-RED con estos cambios:

**Cambio 1 — Reemplazar API base:**
```js
// ANTES (Node-RED template):
const API = window.location.origin;

// DESPUÉS (standalone con failover):
let ACTIVE_BACKEND = null;
let ACTIVE_BACKEND_LABEL = '';

async function apiFetch(endpoint) {
  for (const backend of CONFIG.backends) {
    try {
      const controller = new AbortController();
      const id = setTimeout(() => controller.abort(), backend.timeout);
      const res = await fetch(`${backend.url}${endpoint}`, { signal: controller.signal });
      clearTimeout(id);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();
      ACTIVE_BACKEND = backend.url;
      ACTIVE_BACKEND_LABEL = backend.name;
      return data;
    } catch (e) {
      console.warn(`[Conciliacion] Backend ${backend.name} no responde:`, e.message);
    }
  }
  throw new Error(`Todos los backends fallaron — ${endpoint}`);
}
```

**Cambio 2 — Agregar indicador de backend en el topbar:**
```html
<div class="topbar-actions">
  <span class="backend-indicator" id="backend-indicator">🔴 Offline</span>
  <span class="badge-period" id="period-badge">Feb 2026</span>
  <button class="btn btn-primary" onclick="refreshAll()">🔄 Actualizar</button>
</div>
```
Con CSS:
```css
.backend-indicator {
  font-size: 11px;
  font-weight: 500;
  padding: 4px 10px;
  border-radius: 20px;
  background: var(--surface-3);
  color: var(--text-muted);
}
```

**Cambio 3 — Modificar cada fetch para usar `apiFetch()`:**

Cada función de carga cambia de:
```js
// ANTES
async function cargarResumen() {
  var r = await fetch(API + '/api/conciliacion/resumen');
  var d = await r.json();
  ...
}
```
A:
```js
// DESPUÉS
async function cargarResumen() {
  try {
    var d = await apiFetch('/api/conciliacion/resumen');
    ...
  } catch(e) {
    showError('resumen', e);
  }
}
```

**Cambio 4 — Agregar función `updateBackendIndicator()`:**
```js
function updateBackendIndicator() {
  var el = document.getElementById('backend-indicator');
  if (!el) return;
  if (ACTIVE_BACKEND_LABEL === 'Cloud') {
    el.innerHTML = '🟢 Cloud';
    el.style.color = 'var(--corona-green-dark)';
    el.style.background = 'var(--corona-green-light)';
  } else if (ACTIVE_BACKEND_LABEL === 'LAN') {
    el.innerHTML = '🟡 LAN';
    el.style.color = 'var(--accent-amber)';
    el.style.background = 'var(--accent-amber-light)';
  } else {
    el.innerHTML = '🔴 Offline';
    el.style.color = 'var(--accent-red)';
    el.style.background = 'var(--accent-red-light)';
  }
}
```

**Cambio 5 — Agregar función `showError()` para estados offline:**
```js
function showError(section, error) {
  console.error('[Conciliacion] Error en', section, error);
  updateBackendIndicator();
  var bodies = {
    resumen: 'resumen-body',
    pendientes: 'pendientes-body',
    detalle: 'detalle-body',
    gastos: 'gastos-body'
  };
  var id = bodies[section];
  if (id) {
    document.getElementById(id).innerHTML =
      '<tr><td colspan="8"><div class="empty"><h3>⚠ Sin conexión</h3>' +
      '<p>No se pudo conectar a ningún backend Node-RED. Verificá que al menos uno esté activo.</p>' +
      '<button class="btn btn-primary" onclick="refreshAll()">Reintentar</button></div></td></tr>';
  }
}
```

**Cambio 6 — Modificar `refreshAll()` para que llame a `updateBackendIndicator()` después de cargar:**
```js
async function refreshAll() {
  ACTIVE_BACKEND = null;
  ACTIVE_BACKEND_LABEL = '';
  updateBackendIndicator();
  showSection(STATE._current || 'resumen');
}
```

**Cambio 7 — Eliminar la línea `const API = window.location.origin;`** (no aplica en file://)

**Archivo completo `index.html`:**

> El contenido es el mismo del template de Node-RED con los 7 cambios descritos arriba. No copio el HTML completo aquí porque son ~370 líneas de template idénticas; ver flow_dashboard_conciliacion.json líneas 181-182 para el contenido base.

```html
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Conciliación Bancaria | Bioenergía La Corona</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Config de backends -->
  <script src="config.js"></script>
  <style>
    /* ... mismo CSS del template original ... */
    /* + nuevo CSS para .backend-indicator */
  </style>
</head>
<body>
  <!-- ... mismo HTML del template original ... -->
  <!-- solo cambia el topbar-actions: se agrega el span backend-indicator -->
</body>
</html>
```

- [ ] **Step 2: Abrir `index.html` en browser y verificar que carga**

```bash
# Verificar que los archivos existen
ls -la "proyectos-ingenio/conciliacion bancaria/dashboard/"
```

- [ ] **Step 3: Commit**

```bash
git add "proyectos-ingenio/conciliacion bancaria/dashboard/index.html"
git commit -m "feat: index.html standalone con failover cloud/LAN para conciliacion"
```

---

### Task 3: Crear `dashboard/README.md`

**Files:**
- Create: `proyectos-ingenio/conciliacion bancaria/dashboard/README.md`

- [ ] **Step 1: Crear README con instrucciones de uso**

```markdown
# Dashboard de Conciliación Bancaria — Standalone

Dashboard portable para visualizar la conciliación bancaria de Bioenergía La Corona.

## Cómo usar

1. Abrí `index.html` en cualquier browser (Chrome, Edge, Firefox)
2. El dashboard intenta conectar automáticamente al backend Node-RED:
   - **Cloud** (primario): `http://ingcorona.ddns.net:4040`
   - **LAN** (secundario): `http://192.168.0.23:1880`
3. El badge en el topbar indica qué backend está activo:
   - 🟢 Cloud — conectado al servidor cloud
   - 🟡 LAN — conectado al servidor local
   - 🔴 Offline — sin conexión

## Configuración

Para cambiar URLs o timeouts, editá `config.js`.

## Requisitos

- Conexión a internet (para CDNs de Bootstrap/Chart.js) **o** que ya estén en cache del browser
- Al menos uno de los backends Node-RED operativo
- Los CORS headers ya están configurados en los endpoints Node-RED

## APIs consumidas

| Endpoint | Descripción |
|----------|-------------|
| `GET /api/conciliacion/resumen` | Resumen por banco |
| `GET /api/conciliacion/pendientes` | Pendientes con criticidad |
| `GET /api/conciliacion/detalle` | Detalle del cruce |

## Estructura

```
dashboard/
├── index.html    # Dashboard (abrirlo en browser)
├── config.js     # URLs de backends
└── README.md     # Este archivo
```

## Notas

- El endpoint original `GET /conciliacion` en Node-RED se conserva como respaldo
- Los datos se leen siempre de Google Sheets a través de las APIs Node-RED
```

- [ ] **Step 2: Commit**

```bash
git add "proyectos-ingenio/conciliacion bancaria/dashboard/README.md"
git commit -m "docs: README con instrucciones de uso del dashboard standalone"
```

---

### Task 4: Registrar modo de trabajo en memoria del proyecto

Es opcional - guardar en la memoria del proyecto que esta implementación se hizo siguiendo el proceso completo brainstorming → writing-plans → implementación, para que en futuras iteraciones se use el mismo approach.

