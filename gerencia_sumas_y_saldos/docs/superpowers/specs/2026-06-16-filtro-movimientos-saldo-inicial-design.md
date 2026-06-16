# Spec: Filtro en Movimientos + Saldo Inicial — Sumas y Saldos

**Fecha:** 2026-06-16
**Versión:** 1.0.0
**Estado:** Aprobada — lista para implementación

---

## 1. Objetivo

Agregar al dashboard Sumas y Saldos dos funcionalidades:

1. **Filtro de búsqueda local** en el modal de detalle de movimientos por los campos: Fecha, Asiento, Descripción y Proveedor.
2. **Saldo Inicial** visible en los módulos Sumas y Saldos (columna adicional) y Mayor por Cuenta (primera fila destacada), calculado como el acumulado de movimientos anteriores al período, sincronizado al Google Sheet a las 06:00.

---

## 2. Decisiones de diseño

| Decisión | Elección | Motivo |
|----------|----------|--------|
| Fuente saldo inicial | MSSQL Calipso — acumulado pre-período | Precisión contable |
| Entrega al dashboard | Google Sheets (columna K) en sync 06:00 | Coherencia + rendimiento — sin queries en vivo |
| Filtro en modal | Local (navegador) con `oninput` | Simplicidad, datasets manejables por cuenta |
| Visual Sumas y Saldos | Columna "Saldo Inicial" entre Cuenta y Debe Acum. | Claridad, lectura izquierda→derecha natural |
| Visual Mayor | Primera fila destacada "Saldo Inicial al dd/mm/aaaa" | Estándar contable, no rompe el flujo de movimientos |

---

## 3. Arquitectura

```
┌─────────────────────────────────────────────────────────────────┐
│ SYNC DIARIO 06:00 (Node-RED)                                    │
│                                                                 │
│  MSSQL Query A: Sumas y Saldos del período (@startDate-@endDate)│
│  MSSQL Query B: Saldo Inicial acumulado anterior a @startDate   │
│       │                                                         │
│       ▼                                                         │
│  MERGE por CODIGO → array 2D [[A,B,C,D,E,F,G,H,I,J,K], ...]    │
│       │                                                         │
│       ▼                                                         │
│  GSheet Append → SumasSaldos!A:K                                │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ DASHBOARD (on-demand)                                           │
│                                                                 │
│  GET /api/sumas-saldos → GSheet Read SumasSaldos!A2:K           │
│       │                                                         │
│       ▼                                                         │
│  Transform → JSON con saldoInicial en cada cuenta               │
│       │                                                         │
│       ▼                                                         │
│  Frontend → renderSumasSaldos() + renderMayor()                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 4. Cambios detallados

### 4.1 Sync Flow (`flow_sync_sumas_saldos_gsheet_v2.json`)

**Nuevo nodo: Query Saldo Inicial (MSSQL)**

Query SQL compatible SQL Server 2008 R2:

```sql
SELECT 
    V_EZI_CUENTAS.CODIGO,
    ISNULL(SUM(V_VALOR_.IMPORTE), 0) - ISNULL(SUM(V_VALOR_1.IMPORTE), 0) AS SALDO_INICIAL
FROM V_TRCONTABLE_
    INNER JOIN V_ITEMCONTABLE_ ON V_TRCONTABLE_.ITEMSTRANSACCION_ID = V_ITEMCONTABLE_.BO_PLACE_ID
    INNER JOIN V_VALOR_ ON V_ITEMCONTABLE_.DEBE_ID = V_VALOR_.ID
    INNER JOIN V_VALOR_ AS V_VALOR_1 ON V_ITEMCONTABLE_.HABER_ID = V_VALOR_1.ID
    INNER JOIN V_EZI_CUENTAS ON V_ITEMCONTABLE_.REFERENCIA_ID = V_EZI_CUENTAS.ID
WHERE 
    SUBSTRING(V_TRCONTABLE_.FECHAAPLICACION, 1, 8) < @startDate
    AND V_TRCONTABLE_.ESTADO = 'C'
    AND V_EZI_CUENTAS.CODIGO BETWEEN '0' AND '9'
GROUP BY V_EZI_CUENTAS.CODIGO
```

- El parámetro `@startDate` es el primer día del período (ej: `'20260601'`)
- La query se ejecuta en serie después de la query de sumas del período
- El resultado es un array de objetos `{CODIGO, SALDO_INICIAL}`

**Nuevo nodo: Merge Function**

- Recibe `msg._saldosIniciales` (resultado de la query B) y `msg.payload` (resultado de la query A)
- Hace lookup por CODIGO para agregar la columna K a cada fila del período
- Si una cuenta no tiene movimientos previos, `SALDO_INICIAL = 0`

**GSheet Append actualizado**

- El rango pasa de `SumasSaldos!A2:J` a `SumasSaldos!A2:K`
- Cada fila ahora tiene 11 columnas (A-K)

### 4.2 API Flow (`flow_sumas_y_saldos.json`)

**Nodo 3. Transformar datos** — actualizar mapeo de columnas

```javascript
// Antes (9 cols): [0]CODIGO [1]CUENTA [2]RUBRO [3]DEBE [4]HABER [5]SALDO [6]PERIODO [7]FECHA_SYNC [8]UNIDAD_NEGOCIO
// Ahora (10 cols): [9]=UNIDAD_NEGOCIO, [10]=SALDO_INICIAL

sumasSaldos.push({
    codigo: codigo,
    cuenta: cuenta,
    rubro: rubro,
    saldoInicial: parseFloat(r[10]) || 0,   // ← NUEVO
    debeAcum: debe,
    haberAcum: haber,
    saldo: saldo,
    variacion: 0,
    estado: estado,
    unidad: (r[9] || '').toString().trim()
});
```

**GSheet Read** — actualizar rango de `SumasSaldos!A2:I` a `SumasSaldos!A2:K`

**Health check** — actualizar versión y columnas documentadas

### 4.3 Frontend (`index.html`)

#### 4.3.1 Filtro en Modal de Movimientos

Nuevo HTML dentro del `#movimientosModal`, entre el `modal-title` y la tabla:

```html
<div id="movimientos-filtros" style="display:none; padding:12px 16px; border-bottom:1px solid var(--border); background:var(--surface-2);">
  <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
    <div class="param-group">
      <div class="param-label">Fecha</div>
      <input type="text" class="param-input" id="filtro-fecha" oninput="filtrarMovimientos()" 
             placeholder="YYYYMMDD" style="width:110px; font-family:var(--font-mono); font-size:11px;" />
    </div>
    <div class="param-group">
      <div class="param-label">Asiento</div>
      <input type="text" class="param-input" id="filtro-asiento" oninput="filtrarMovimientos()" 
             placeholder="A-00142" style="width:120px; font-family:var(--font-mono); font-size:11px;" />
    </div>
    <div class="param-group">
      <div class="param-label">Descripción</div>
      <input type="text" class="param-input" id="filtro-descripcion" oninput="filtrarMovimientos()" 
             placeholder="Buscar en descripción..." style="width:200px;" />
    </div>
    <div class="param-group">
      <div class="param-label">Proveedor</div>
      <input type="text" class="param-input" id="filtro-proveedor" oninput="filtrarMovimientos()" 
             placeholder="Buscar proveedor..." style="width:180px;" />
    </div>
    <button class="panel-tool-btn" onclick="limpiarFiltrosMovimientos()" 
            style="height:34px; align-self:flex-end;">Limpiar filtros</button>
  </div>
</div>
```

**Nuevas funciones JS:**

```javascript
function filtrarMovimientos() {
  var fecha = (document.getElementById('filtro-fecha').value || '').toLowerCase().trim();
  var asiento = (document.getElementById('filtro-asiento').value || '').toLowerCase().trim();
  var descripcion = (document.getElementById('filtro-descripcion').value || '').toLowerCase().trim();
  var proveedor = (document.getElementById('filtro-proveedor').value || '').toLowerCase().trim();
  
  var rows = document.querySelectorAll('#movimientos-body tr');
  var visible = 0;
  rows.forEach(function(row) {
    var text = row.textContent.toLowerCase();
    var match = true;
    if (fecha && text.indexOf(fecha) === -1) match = false;
    if (asiento && text.indexOf(asiento) === -1) match = false;
    if (descripcion && text.indexOf(descripcion) === -1) match = false;
    if (proveedor && text.indexOf(proveedor) === -1) match = false;
    row.style.display = match ? '' : 'none';
    if (match) visible++;
  });
  
  // Mostrar contador de resultados
  var countEl = document.getElementById('mov-count');
  if (countEl && (fecha || asiento || descripcion || proveedor)) {
    countEl.textContent = visible + ' (filtrado)';
    countEl.style.color = 'var(--accent-blue)';
  } else if (countEl) {
    countEl.style.color = '';
  }
}

function limpiarFiltrosMovimientos() {
  document.getElementById('filtro-fecha').value = '';
  document.getElementById('filtro-asiento').value = '';
  document.getElementById('filtro-descripcion').value = '';
  document.getElementById('filtro-proveedor').value = '';
  filtrarMovimientos();
  var countEl = document.getElementById('mov-count');
  if (countEl) countEl.style.color = '';
}
```

**Modificación en `verMovimientos()`:**

- Al cargar exitosamente los datos, mostrar `#movimientos-filtros` (actualmente oculto)
- Al mostrar error o loading, ocultarlo
- El `mov-count` se restaura a `data.total` al limpiar filtros

#### 4.3.2 Columna Saldo Inicial en Sumas y Saldos

**Tabla:** Agregar `<th class="num">Saldo Inicial</th>` en el thead (entre Cuenta y Debe Acum.)

**`renderSumasSaldos()`:**

```javascript
// En cada fila, después de <td>${r.cuenta}</td>:
'<td class="num" style="color:' + (r.saldoInicial >= 0 ? 'var(--text-primary)' : 'var(--accent-red)') + '">' + 
  fmtMoney(r.saldoInicial) + 
'</td>'
```

**colspan:** Actualizar los empty states de `colspan="8"` a `colspan="9"`.

**Totales del footer:** Agregar `Total Saldo Inicial` al footer de totales.

#### 4.3.3 Fila Saldo Inicial en Mayor por Cuenta

**`renderMayor(cuenta)` actualizado:**

```javascript
function renderMayor(cuenta) {
  var items = STATE.data.mayor || [];
  var filtered = cuenta ? items.filter(function(r) { return r.cuenta === cuenta; }) : items;
  var tbody = document.getElementById('mayor-body');
  
  // Obtener saldo inicial de la cuenta desde STATE.data.sumasSaldos
  var saldoInicial = 0;
  if (cuenta) {
    var match = (STATE.data.sumasSaldos || []).find(function(r) { return r.codigo === cuenta; });
    if (match) saldoInicial = match.saldoInicial || 0;
  }
  
  var startDate = (document.getElementById('startDate').value || '20260601').replace(/-/g, '');
  var fechaFormateada = fmtDate(startDate);
  
  var html = '';
  
  // Primera fila: Saldo Inicial (destacada)
  html += '<tr style="background:var(--corona-green-light);">' +
    '<td class="code" style="color:var(--text-muted);">—</td>' +
    '<td class="code" style="color:var(--text-muted);">—</td>' +
    '<td><em style="color:var(--corona-green-dark);">Saldo Inicial al ' + fechaFormateada + '</em></td>' +
    '<td class="num">—</td>' +
    '<td class="num">—</td>' +
    '<td class="num"><strong style="color:' + (saldoInicial >= 0 ? 'var(--corona-green-dark)' : 'var(--accent-red)') + '">' + fmtMoney(saldoInicial) + '</strong></td>' +
    '</tr>';
  
  // Movimientos con saldo acumulado desde el inicial
  var saldoAcc = saldoInicial;
  html += filtered.map(function(r) {
    saldoAcc += (r.debe || 0) - (r.haber || 0);
    return '<tr>' +
      '<td class="code">' + fmtDate(r.fecha) + '</td>' +
      '<td class="code">' + (r.asiento || '') + '</td>' +
      '<td>' + (r.descripcion || '') + '</td>' +
      '<td class="num">' + (r.debe ? fmtMoney(r.debe) : '—') + '</td>' +
      '<td class="num">' + (r.haber ? fmtMoney(r.haber) : '—') + '</td>' +
      '<td class="num"><strong style="color:' + (saldoAcc >= 0 ? 'var(--corona-green-dark)' : 'var(--accent-red)') + '">' + fmtMoney(saldoAcc) + '</strong></td>' +
      '</tr>';
  }).join('');
  
  tbody.innerHTML = html || '<tr><td colspan="6"><div class="empty-state"><h3>Sin movimientos</h3></div></td></tr>';
  
  // Populate select
  var sel = document.getElementById('select-cuenta');
  var cuentas = [...new Set(items.map(function(r) { return r.cuenta; }))];
  sel.innerHTML = '<option value="">Todas las cuentas</option>' + cuentas.map(function(c) { return '<option value="' + c + '">' + c + '</option>'; }).join('');
}
```

**colspan:** Actualizar empty states de `colspan="6"` a `colspan="6"` (no cambia, son 6 columnas).

#### 4.3.4 MOCK actualizado

Agregar `saldoInicial` a cada registro de `MOCK.sumasSaldos`:

```javascript
{ codigo: '1.1.01', cuenta: 'Caja y Bancos',     saldoInicial: 32900000, debeAcum: 45200000, haberAcum: 12300000, ... },
{ codigo: '1.1.02', cuenta: 'Cuentas a Cobrar',  saldoInicial: 29600000, debeAcum: 38700000, haberAcum:  9100000, ... },
// etc.
```

---

## 5. Archivos a modificar

| Archivo | Cambio | Riesgo |
|---------|--------|--------|
| `sync_gsheet/flow_sync_sumas_saldos_gsheet_v2.json` | + query saldo inicial MSSQL, + merge, columna K | Medio — requiere acceso MSSQL y GSheet |
| `flujos/node-red/flow_sumas_y_saldos.json` | Actualizar GSheet Read rango A2:K, transform (col 10), health | Bajo — cambio acotado |
| `index.html` | + filtros modal, + columna SI Sumas y Saldos, + fila SI Mayor, + MOCK | Bajo — solo frontend |

---

## 6. Riesgos y mitigaciones

| Riesgo | Mitigación |
|--------|------------|
| Query de saldo inicial pesada (muchas cuentas) | Ya está filtrada por `CODIGO BETWEEN '0' AND '9'` y `ESTADO = 'C'`. Si sigue siendo lenta, agregar `TOP 1000` en consulta externa. |
| Desfase entre sheet y MSSQL si el sync falla | El health check del dashboard reporta `FECHA_SYNC`. Si pasaron > 24 h, se muestra advertencia. |
| Cuentas sin movimientos previos (nuevas) | `ISNULL(SUM(...), 0)` → saldoInicial = 0, funciona sin cambios. |
| Modificación de flow Node-RED en producción | Se trabaja sobre el flow del repositorio. El deploy a producción es manual. |

---

## 7. Próximos pasos (post-implementación)

1. Deploy del sync flow actualizado y verificar que la columna K aparezca en el sheet
2. Deploy del flow API actualizado
3. Prueba del dashboard con datos reales
4. Ajuste de estilos si es necesario
5. Documentar nueva columna en `SETUP.md` del sync

---

## 8. No incluido (fuera de alcance)

- Filtro remoto/server-side en el modal (se acordó local)
- Saldo inicial en Libro Diario (no se pidió)
- Export CSV con la nueva columna (se hereda automáticamente del render)
- Cache del saldo inicial fuera del sheet
