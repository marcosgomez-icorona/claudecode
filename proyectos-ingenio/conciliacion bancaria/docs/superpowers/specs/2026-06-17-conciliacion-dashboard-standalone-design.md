# Dashboard de Conciliación Bancaria — Standalone HTML + API Failover

**Fecha:** 2026-06-17
**Proyecto:** Conciliación Bancaria — Bioenergía La Corona
**Versión spec:** 1.0

---

## 1. Objetivo

Extraer el dashboard HTML que actualmente se sirve embebido en Node-RED (`GET /conciliacion`) a un archivo `index.html` standalone, portable, que se abre directamente desde el filesystem (`file://`). El HTML consumirá las APIs de Node-RED con failover automático entre un backend cloud (primario) y LAN (secundario).

## 2. Arquitectura

```
┌──────────────────────────────┐
│   index.html (file://)       │
│   ┌────────────────────┐     │
│   │ config.js          │     │
│   │  - BACKENDS = [    │     │
│   │    cloud, LAN      │     │
│   │  ]                 │     │
│   └────────────────────┘     │
│         │                    │
│         │ fetch() con        │
│         │ failover           │
│         ▼                    │
│   ┌────────────────────┐     │
│   │ apiFetch(ep)       │     │
│   │ 1° cloud (5s)      │     │
│   │ 2° LAN (5s)        │     │
│   └────────────────────┘     │
└──────────┬───────────────────┘
           │
     ┌─────┴─────┐
     │           │
     ▼           ▼
  Cloud Node-RED   LAN Node-RED
  :4040            :1880
     │           │
     └─────┬─────┘
           ▼
       Google Sheets
```

## 3. Componentes

### 3.1 `dashboard/index.html`

Extraído del template Node-RED (`conc_dash_tmpl`) con cambios:

- **API base dinámica**: reemplaza `window.location.origin` por función `apiFetch()` con failover
- **Timeout por backend**: 5s por intento (AbortSignal.timeout)
- **Indicador de backend activo**: muestra en UI si está usando cloud o LAN
- **Sin dependencia de servidor**: todo el contenido es estático, las únicas llamadas externas son a las APIs Node-RED y a CDNs (Bootstrap, Chart.js)

### 3.2 `dashboard/config.js`

Archivo separado con la configuración de backends:

```js
const CONFIG = {
  backends: [
    { name: 'Cloud', url: 'http://ingcorona.ddns.net:4040', timeout: 5000 },
    { name: 'LAN',   url: 'http://192.168.0.23:1880',       timeout: 5000 }
  ]
};
```

Separado del HTML para permitir cambios sin tocar el dashboard.

### 3.3 Backend Node-RED (sin cambios)

Los 3 endpoints existentes se mantienen intactos:

| Endpoint | Método | Descripción |
|----------|--------|-------------|
| `/api/conciliacion/resumen` | GET | Resumen ejecutivo por banco |
| `/api/conciliacion/pendientes` | GET | Pendientes con criticidad |
| `/api/conciliacion/detalle` | GET | Detalle completo del cruce |

Los headers CORS `Access-Control-Allow-Origin: *` ya están configurados en cada endpoint.

## 4. Mecanismo de failover

```js
async function apiFetch(endpoint) {
  for (const backend of CONFIG.backends) {
    try {
      const controller = new AbortController();
      const timeout = setTimeout(() => controller.abort(), backend.timeout);
      const res = await fetch(`${backend.url}${endpoint}`, { signal: controller.signal });
      clearTimeout(timeout);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();
      return { data, backend: backend.name };
    } catch (e) {
      console.warn(`Backend ${backend.name} falló:`, e.message);
    }
  }
  throw new Error('Todos los backends fallaron');
}
```

El resultado incluye `backend` para mostrar en la UI cuál está activo.

## 5. Indicador visual de backend activo

Se agrega un badge en el topbar del dashboard que muestra:
- **🟢 Cloud** (verde) si responde `ingcorona.ddns.net:4040`
- **🟡 LAN** (ámbar) si cayó a `192.168.0.23:1880`
- **🔴 Offline** si ambos fallaron

## 6. Cambios respecto al template original

| Aspecto | Original (Node-RED) | Nuevo (standalone) |
|---------|--------------------|--------------------|
| Ubicación | Template embebido en flow | `dashboard/index.html` |
| API URL | `window.location.origin` | `apiFetch()` con failover |
| Backend | 1 (el mismo servidor) | 2 (cloud → LAN) |
| Indicador backend | No | Sí, en topbar |
| Dependencias CDN | Sí (Bootstrap, Chart.js) | Sí (mismas) |
| Mustache template | Sí (pero sin vars) | No necesario |

## 7. Riesgos y mitigaciones

| Riesgo | Mitigación |
|--------|------------|
| CORS desde `file://` no soportado en algunos browsers | `Access-Control-Allow-Origin: *` ya está en los endpoints. Si algún browser bloquea, servir el HTML desde un mini servidor HTTP local |
| CDNs no disponibles sin internet | Las CDNs de Bootstrap/Chart.js están cacheadas por el browser. Si no, el dashboard se ve sin estilos ni gráficos pero funcional |
| Cloud inaccesible | Failover automático a LAN en 5s |
| Ambos backends caídos | Mensaje claro "Offline — no se pudo conectar a ningún backend" |

## 8. Próximos pasos (post-aprobación)

1. Crear carpeta `dashboard/` con `index.html` + `config.js`
2. Agregar indicador visual de backend activo
3. Probar apertura local
4. Opcional: dejar `GET /conciliacion` como respaldo o quitarlo
