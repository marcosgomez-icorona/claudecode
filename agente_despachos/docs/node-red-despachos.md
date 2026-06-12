# Node-RED — API Despachos Pendientes de Facturación

## Instalación

### 1. Importar el flow

1. Abrir Node-RED (`http://localhost:1880`)
2. Menú → Import → Clipboard
3. Copiar contenido de `flows/flow_despachos_pendientes.json`
4. Pegar y hacer clic en "Import"
5. Hacer clic en "Deploy"

### 2. Configurar conexión SQL Server

El nodo `dp_mssqlcn_0002` (MSSQL-CN) debe configurarse con las credenciales de Calipso:

| Parámetro | Valor |
|-----------|-------|
| Server | `192.168.0.177` (o IP del servidor SQL) |
| Port | `1433` |
| Database | `CORONA` |
| TDS Version | `7_3_A` |
| Use UTC | `false` |

Las credenciales se guardan cifradas en `credentials.json` de Node-RED.

### 3. Servir el frontend estático (opcional)

Para que `GET /despachos-pendientes` sirva el frontend, configurar en `settings.js`:

```javascript
httpStatic: '/ruta/a/agente_despachos',
```

O copiar la carpeta `agente_despachos/` al `userDir` de Node-RED.

### 4. Verificar instalación

```bash
curl http://localhost:1880/api/despachos/health
```

Respuesta esperada:
```json
{"status":"ok","service":"despachos-pendientes","timestamp":"...","version":"0.1.0"}
```

## Endpoints

### `GET /api/despachos/pendientes?days=30`

Lista remitos sin facturar de los últimos N días.

**Parámetros:**
- `days` (opcional, default 30): días hacia atrás (1-365)

**Respuesta:**
```json
{
  "metadata": { "runUuid": "...", "timestamp": "...", "source": "CORONA.pr_ezi_remitos", "filterDays": 30 },
  "resumen": { "totalRemitos": 156, "totalClientes": 34, "totalBolsas": 89450, "totalToneladas": 2847.5, "totalImporte": 42586000, "productos": {...} },
  "data": [
    {
      "remito": "0008-00005615",
      "fecha": "2026-06-10T11:29:00",
      "cliente": "VERAMOR DE MARMOL S.R.L.",
      "cuit": "30707690034",
      "producto": "AZUCAR COMUN TIPO A",
      "cantidad": 600,
      "unidad": "Bolsa",
      "precio": 620,
      "totalItem": 372000,
      "transportista": "AGÜERO ABEL EDGARDO",
      ...
    }
  ]
}
```

### `GET /api/despachos/pendientes/:remito`

Detalle completo de un remito (incluye todos los items).

### `POST /api/despachos/pendientes/:remito/facturar`

Vincula un remito con una factura. Requiere body JSON:
```json
{ "factura": "000100001500" }
```

### `GET /api/despachos/resumen?days=30`

KPIs y métricas del período.

### `GET /api/despachos/health`

Health check del servicio.

### `GET /despachos-pendientes`

Sirve el frontend HTML (requiere `httpStatic` configurado).

## Seguridad

- El endpoint `POST .../facturar` es destructivo (UPDATE en SQL Server)
- Considerar agregar autenticación vía HTTP Node-RED (settings.js)
- Las credenciales SQL se guardan cifradas en Node-RED
- En producción, validar que solo usuarios autorizados puedan facturar

## Fuentes de datos

- `CORONA.dbo.pr_ezi_remitos` — Tabla de remitos
- `CORONA.dbo.pr_ezi_remitos_items` — Items por remito
