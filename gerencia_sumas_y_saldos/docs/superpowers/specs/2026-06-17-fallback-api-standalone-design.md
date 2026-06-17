# Spec: Fallback API + Standalone — Dashboard Sumas y Saldos

**Fecha:** 2026-06-17  
**Versión:** 1.0.0  
**Estado:** Aprobada — lista para implementación

---

## 1. Objetivo

Convertir el `index.html` del dashboard Sumas y Saldos en un **archivo standalone** que pueda abrirse directamente desde el disco y consumir datos desde Node-RED con **fallback automático entre dos endpoints**, y en última instancia recurrir a datos mock embebidos.

---

## 2. Decisiones de diseño

| Decisión | Elección | Motivo |
|----------|----------|--------|
| Estrategia de fetch | Primary → Fallback → Mock (en serie) | Simplicidad, sin latencia extra |
| Primary | `http://ingcorona.ddns.net:4040` (internet) | Acceso remoto desde cualquier red |
| Fallback | `http://192.168.0.23:1880` (LAN) | Alternativa local si falla DDNS |
| Timeout por intento | 15 segundos | Balance entre esperar y no colgar |
| Indicador de estado | Sidebar footer con dot + texto | Visible siempre, no intrusivo |
| Detección de origen | `file://` vs `http://` al cargar | Diferente estrategia de CORS según caso |
| Endpoints exactos | Heredados: `/api/sumas-saldos` y `/api/sumas-saldos/movimientos` | Sin cambios en Node-RED |

---

## 3. Arquitectura

```
index.html (file:// abierto localmente)
│
├── CONFIG
│   ├── API_PRIMARY:   'http://ingcorona.ddns.net:4040'
│   ├── API_FALLBACK:  'http://192.168.0.23:1880'
│   ├── TIMEOUT_MS:    15000
│   └── endpoints:
│       ├── /api/sumas-saldos              (GET ?startDate=&endDate=&criticalThreshold=)
│       └── /api/sumas-saldos/movimientos   (GET ?codigo=)
│
├── loadFromAPI()
│   ├── 1. fetch(PRIMARY + endpoint) con timeout 15s
│   ├── 2. si falla → fetch(FALLBACK + endpoint) con timeout 15s
│   ├── 3. si falla → loadMockData() con toast "Sin conexión - datos demo"
│   └── return { data, source: 'primary'|'fallback'|'mock' }
│
├── Indicador de estado en sidebar
│   ├── dot verde  + "Primary"   (ingcorona.ddns.net:4040)
│   ├── dot amarillo + "Fallback" (192.168.0.23:1880)
│   └── dot gris + "Offline - demo" (mock data)
│
└── MOCK DATA (embebido, sin cambios)
```

### Flujo de carga

```
init()
  → loadFromAPI(PRIMARY)
    → success: renderAll(), source='primary'
    → timeout/error: loadFromAPI(FALLBACK)
      → success: renderAll(), source='fallback'
      → timeout/error: loadMockData(), source='mock', toast advertencia

verMovimientos(cuenta)
  → fetch(PRIMARY + '/movimientos?codigo=')
    → success: render
    → fallback: fetch(FALLBACK + ...)
      → success: render
      → fallback: toast error, modal vacío
```

---

## 4. Cambios detallados

### 4.1 CONFIG (línea ~1438)

**Antes:**
```javascript
const CONFIG = {
  // ...
  nodeRedUrl: (window.location.origin || 'http://192.168.0.23:1880') + '/api/sumas-saldos',
  useNodeRed: true
};
```

**Después:**
```javascript
const CONFIG = {
  // ... umbrales existentes ...
  API_PRIMARY:   'http://ingcorona.ddns.net:4040',
  API_FALLBACK:  'http://192.168.0.23:1880',
  API_TIMEOUT_MS: 15000,
  endpoints: {
    sumasSaldos:   '/api/sumas-saldos',
    movimientos:   '/api/sumas-saldos/movimientos',
  },
};
```

### 4.2 Helper: fetch con timeout y fallback

Nueva función genérica:

```javascript
async function fetchWithFallback(endpoint, params, retries = 1) {
  const urls = [
    CONFIG.API_PRIMARY + endpoint,
    CONFIG.API_FALLBACK + endpoint,
  ];
  for (let attempt = 0; attempt <= retries && attempt < urls.length; attempt++) {
    const url = urls[attempt] + '?' + new URLSearchParams(params);
    try {
      const controller = new AbortController();
      const timeout = setTimeout(() => controller.abort(), CONFIG.API_TIMEOUT_MS);
      const res = await fetch(url, { signal: controller.signal });
      clearTimeout(timeout);
      if (!res.ok) { const e = await res.json().catch(() => ({ error: res.statusText })); throw new Error(e.error || res.statusText); }
      return { data: await res.json(), source: attempt === 0 ? 'primary' : 'fallback' };
    } catch (e) {
      if (attempt >= retries || attempt >= urls.length - 1) throw e;
      // intentar siguiente URL
    }
  }
  throw new Error('Todos los endpoints fallaron');
}
```

### 4.3 loadFromNodeRED() → refactor a loadFromAPI()

Se reemplaza el fetch directo por `fetchWithFallback`:

- Si `source === 'fallback'` → toast informativo (no warn)
- Si ambas fallan → `loadMockData()` como hoy
- El indicador de sidebar se actualiza con el source

### 4.4 verMovimientos() con fallback

Se aplica el mismo patrón: intentar primary primero, fallback segundo, error si ambos fallan.

### 4.5 Indicador de estado en sidebar

En el sidebar se reemplaza o agrega en el footer un indicador:

```html
<div id="api-status" class="sync-indicator" style="margin-top:8px;">
  <span class="sync-dot" id="api-dot"></span>
  <span id="api-label">Conectando...</span>
</div>
```

Colores del dot:
- `var(--corona-green)` → Primary (ingcorona.ddns.net:4040)
- `var(--accent-amber)` → Fallback (192.168.0.23:1880)
- `var(--text-muted)` → Offline (mock data)

### 4.6 init() actualizado

Se mantiene el mismo flujo pero usando `fetchWithFallback`:

```javascript
(async function init() {
  // ... fechas default, UUID ...
  const result = await loadFromAPI();
  if (result) renderAll();
  else loadMockData();
})();
```

### 4.7 CORS

No requiere cambios en Node-RED si ya tiene `Access-Control-Allow-Origin: *`. Si no lo tiene, se agrega un header en el HTTP response node:

```
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, OPTIONS
Access-Control-Allow-Headers: Content-Type
```

---

## 5. Archivos a modificar

| Archivo | Cambio | Riesgo |
|---------|--------|--------|
| `gerencia_sumas_y_saldos/index.html` | CONFIG nuevo, fetchWithFallback(), refactor loadFromNodeRED() y verMovimientos(), sidebar status indicator | Medio — toca flujo de carga completo |
| `(opcional) flow_sumas_y_saldos.json` | Si no tiene CORS, agregar header en HTTP response | Bajo |

---

## 6. Riesgos y mitigaciones

| Riesgo | Mitigación |
|--------|------------|
| CORS bloquea el fetch desde `file://` | Algunos navegadores NO permiten CORS desde `file://`. Se documenta que debe abrirse sirviendo desde un HTTP local (Live Server, Python http.server) o configurar CORS en Node-RED. |
| `ingcorona.ddns.net:4040` no está disponible | El fallback automático a LAN + mock garantiza que el dashboard siempre funcione |
| Timeout de 15s puede sentirse lento si el primary está caído | El usuario ve el estado "Conectando..." y el indicador cambia apenas falla el primero |
| Navegador no soporta `AbortController` | Safari 12.1+, Chrome 66+, Firefox 57+. Compatible con el parque actual (~2020+). |

---

## 7. No incluido (fuera de alcance)

- Autenticación en los endpoints (se asume Node-RED abierto)
- Cache local (Service Workers, localStorage)
- PWA / instalable
- Soporte para Internet Explorer
- Configuración dinámica de endpoints desde UI
