# 🏦 Análisis Experto — Conciliación Bancaria MVP (Galicia)

## Revisión del flujo Node-RED

### 1. 🔴 ISSUE CRÍTICO: APPEND duplica datos

**Problema:** Los nodos `Append Conciliacion`, `Append Pendientes` y `Append Resumen` usan método `append`. Cada vez que se ejecuta la conciliación, los resultados se **agregan al final** sin limpiar los anteriores.

**Solución:** Reemplazar por `clear` + `append` en secuencia:

```
Función "Previo a escribir → Clear rango"
  ↓
GSheet (method: clear, cells: "Conciliacion!A2:H")
  ↓
GSheet (method: append, cells: "Conciliacion!A2:H")
```

**Archivo afectado:** `flow_conciliacion_bancaria-13-06-26.json` — nodos: `e106c2fee4486106`, `6c41a11e8fb05c80`, `2f3fcd2d11518072`

### 2. 🟡 ISSUE IMPORTANTE: Query Calipso hardcodeado

**Problema:** El query MSSQL tiene `'202602'` hardcodeado como período.

**Solución:** Hacerlo dinámico desde el inject node:

```javascript
// En el nodo "▶ Sync Calipso"
msg._periodo = '2026-02';  // ← Configurable
msg._periodoNum = msg._periodo.replace('-', '').substring(0, 6);  // → "202602"

// En el query:
// BEFORE: AND SUBSTRING(...) = '202602'
// AFTER:  AND SUBSTRING(...) = '${msg._periodoNum}'
```

**Archivo:** `flow_conciliacion_bancaria-13-06-26.json` 

### 3. 🟢 MEJORA: Query Calipso más completo

**Problema actual:** El query solo trae movimientos donde la cuenta bancaria aparece como REFERENCIA. Puede perderse transacciones donde el banco es solo DEBE o solo HABER.

**Query recomendado:**

```sql
SELECT DISTINCT
  SUBSTRING(t.FECHAAPLICACION, 1, 8) AS FECHA,
  t.NUMERODOCUMENTO AS ASIENTO,
  t.DETALLE,
  v1.IMPORTE AS DEBE,
  v2.IMPORTE AS HABER,
  (COALESCE(v1.IMPORTE, 0) - COALESCE(v2.IMPORTE, 0)) AS NETO,
  t.NOMBREDESTINATARIO AS PROVEEDOR,
  ec.CODIGO AS CUENTA_CODIGO
FROM V_TRCONTABLE_ t
INNER JOIN V_ITEMCONTABLE_ ic ON t.ITEMSTRANSACCION_ID = ic.BO_PLACE_ID
LEFT JOIN V_VALOR_ v1 ON ic.DEBE_ID = v1.ID
LEFT JOIN V_VALOR_ v2 ON ic.HABER_ID = v2.ID
LEFT JOIN V_EZI_CUENTAS ec ON ic.REFERENCIA_ID = ec.ID
WHERE (
  ec.CODIGO LIKE '01.01.01.02.04%'  -- Cuenta Galicia
  OR t.DETALLE LIKE '%Galicia%'       -- Detalle mencione el banco
  OR t.DETALLE LIKE '%Banco%'
)
AND SUBSTRING(t.FECHAAPLICACION, 1, 6) = '${msg._periodoNum}'
AND t.ESTADO = 'C'
ORDER BY t.FECHAAPLICACION ASC;
```

**Ventaja:** Captura movimientos donde el banco se menciona en el detalle pero la cuenta contable es otra (ej: transferencias intermedias).

### 4. 🟢 MEJORA: Matching multi-criterio

**Problema actual:** Solo match por importe + fecha (±3 días). Esto genera falsos positivos cuando hay múltiples movimientos del mismo importe.

**Solución:** Agregar matching por referencia cruzada:

```javascript
// Orden de matching (de más preciso a menos preciso):
// 1. Match por NRO_COMPROBANTE del banco vs ASIENTO de Calipso
// 2. Match por importe EXACTO + misma fecha
// 3. Match por importe +/- 1% + fecha +/- 1 día
// 4. Match por importe +/- 1% + fecha +/- 3 días
// 5. Sin match → PENDIENTE

function buscarMatch(movBanco, calipsoRows) {
  for (let nivel = 1; nivel <= 4; nivel++) {
    for (let ci = 0; ci < calipsoRows.length; ci++) {
      if (calipsoUsados[ci]) continue;
      const cm = calipsoRows[ci];
      const score = matchScore(movBanco, cm, nivel);
      if (score >= nivel) return { idx: ci, calidad: nivel, calImporte: parseFloat(cm[5]) };
    }
  }
  return null; // Sin match
}
```

### 5. 🟢 MEJORA: Detección de pares MEP y FIMA

**Regla de negocio:** MEP y FIMA siempre operan en pares (compra + venta / suscripción + rescate). El flujo actual los trata como movimientos independientes.

```javascript
// Detectar pares MEP en pendientes
var paresMEP = [];
for (var pi = 0; pi < pendientes.length; pi++) {
  var p = pendientes[pi];
  if (p.tipo === 'MEP') {
    // Buscar otro MEP de importe similar (+/- 1%) en fecha cercana
    for (var pj = pi + 1; pj < pendientes.length; pj++) {
      var p2 = pendientes[pj];
      if (p2.tipo === 'MEP') {
        var dif = Math.abs(p.importe - p2.importe);
        if (dif / Math.max(p.importe, p2.importe) < 0.01) {
          paresMEP.push({ compra: p, venta: p2 });
          p.criticidad = 'OK';  // Marcar como par identificado
          p2.criticidad = 'OK';
          p.observacion = 'Par MEP identificado — timing normal';
          p2.observacion = 'Par MEP identificado — timing normal';
        }
      }
    }
  }
}
```

### 6. 🟢 MEJORA: Scheduler automático

Agregar un inject node con schedule para ejecución automática:

```
Inject node: "⏰ Sync automático (madrugada)"
  repeat: cron
  crontab: "0 5 * * 1-5"  // 5am lun-vie
  wires:
    - "▶ Sync Calipso (mes actual)"
```

### 7. 🟢 MEJORA: Log de ejecuciones

Agregar un nodo function que escriba un registro de cada ejecución en una hoja "Historial":

```javascript
// Nodo: "Registrar ejecución"
var logEntry = [new Date().toISOString(), 'Galicia', msg._periodo,
  stats.conciliados, stats.soloBanco, stats.soloCalipso,
  stats.pendientes, stats.semaforo, stats.diferencia];
msg.payload = [logEntry];
msg._sheetType = 'historial';
return msg;
```

### 8. 🟡 CONSIDERACIÓN: Umbrales de materialidad

Los umbrales actuales ($100 = cuadrado, $500K = timing) son razonables para Galicia. Validar con tesorería si aplican:
- **Umbral de conciliación** ($100): Parece bajo para Galicia. Tal vez $500-1000 sea más realista.
- **Umbral de timing** ($500K): Ajustar si el volumen mensual supera $100M.

## Recomendación de deploy

### Orden sugerido:

1. **Aplicar fix CRÍTICO** (clear+append) al JSON antes de importar
2. **Configurar período** en el inject node como parámetro
3. **Cargar extracto bancario** de prueba (Galicia, cualquier mes) en la hoja `Galicia_Banco`
4. **Importar flujo corregido** a Node-RED
5. **Verificar Google Auth** — la service account necesita acceso a la Sheet
6. **Ejecutar Sync Calipso** (prueba)
7. **Ejecutar Conciliación** (prueba)
8. **Verificar dashboard** — los 3 endpoints deben responder JSON
9. **Ajustar tolerancias** según resultados reales
10. **Poner scheduler** cuando esté estable

### Verificaciones post-deploy:

| Qué verificar | Cómo | Resultado esperado |
|--------------|------|-------------------|
| Auth Google | Log de Node-RED | "GSheet OK" sin errores 401/403 |
| Conexión MSSQL | Log de Node-RED | Filas devueltas > 0 |
| Sheet IDs | Abrir Google Sheets | 5 hojas con datos escritos |
| Dashboard | GET /api/conciliacion/resumen | JSON con arrray data[] |
| CORS | Dashboard desde Apache | Fetch exitoso (no bloqueo CORS) |

## Riesgos y mitigaciones

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|-------------|---------|------------|
| Query Calipso no trae todos los movimientos | Media | Alto | Verificar contra asientos reales del mes |
| Service Account expira | Baja | Alto | Monitorear logs, renovar cada año |
| Sheet se llena con duplicados | Alta (sin fix) | Medio | Aplicar fix clear+append antes de deploy |
| Extracto bancario con formato distinto | Media | Medio | Validar columnas antes de procesar |
| Tolerancias mal calibradas | Alta (primera vez) | Medio | Ejecutar test con data real, ajustar |
