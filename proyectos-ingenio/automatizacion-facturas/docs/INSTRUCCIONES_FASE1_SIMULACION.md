# Fase 1 — Simulación de carga en MySQL
## Ingenio La Corona | Automatización Facturas de Compra

---

## Qué hace esta fase

Valida el circuito completo **sin tocar SQL Server ni Calipso**:

```
TEST n8n → webhook → parsear → INSERT MySQL → email aprobación → click → UPDATE MySQL
```

Cuando esta fase funcione correctamente, se activa la Fase 2 que agrega la llamada al SP de SQL Server.

---

## PRE-REQUISITOS

- MySQL corriendo con `db_automatizaciones` y tabla `staging_facturas`
- Si no ejecutaste el script de ítems todavía:
  ```sql
  -- En MySQL: ejecutar sql/02_agregar_items.sql
  ALTER TABLE staging_facturas
      ADD COLUMN items_json MEDIUMTEXT NULL AFTER email_asunto;
  ```
- n8n accesible (cloud o self-hosted)
- Cuenta SMTP configurada para envío de emails

---

## PASO 1 — Configurar credenciales en n8n

En n8n → **Settings → Credentials → New**:

### MySQL Automatizaciones
| Campo | Valor |
|---|---|
| Credential name | `MySQL Automatizaciones` |
| Host | `192.168.0.23` (IP del servidor on-prem) |
| Database | `db_automatizaciones` |
| User | `usr_automatizacion` |
| Password | `Corona1234$` |
| Port | `3306` |

### SMTP Ingenio La Corona
| Campo | Valor |
|---|---|
| Credential name | `SMTP Ingenio La Corona` |
| Host | servidor SMTP de la empresa |
| Port | `587` (TLS) o `465` (SSL) |
| User | `sectorcompras@ingeniolacorona.com` |
| Password | contraseña de app Gmail |

---

## PASO 2 — Importar los 3 workflows de Fase 1

En n8n → **Workflows → Import from file** (importar en este orden):

1. `n8n/workflow_facturas_entrada_fase1.json`
2. `n8n/workflow_facturas_aprobacion_fase1.json`
3. `n8n/workflow_test_inyectar.json`

---

## PASO 3 — Ajustar URL en los workflows

### En `Facturas — Entrada FASE 1`
Abrir el nodo **"Validar datos locales"** → editar la línea:
```javascript
const base = 'https://n8n.ingeniolacorona.com'; // ← reemplazar con tu URL de n8n
```

### En `TEST — Inyectar factura de prueba`
Abrir el nodo **"POST al webhook entrada"** → verificar que la URL sea:
```
https://[tu-n8n]/webhook/facturas-entrada
```

---

## PASO 4 — Activar workflows

**Activar en este orden** (el de aprobación primero, para que el webhook esté listo):

1. `Facturas — Aprobación FASE 1` → activar → confirmar que el webhook `facturas-aprobar-fase1` quede en verde
2. `Facturas — Entrada FASE 1` → activar → confirmar que el webhook `facturas-entrada` quede en verde
3. `TEST — Inyectar` → **NO activar** (se ejecuta manualmente)

---

## PASO 5 — Ejecutar la prueba

1. Abrir `TEST — Inyectar factura de prueba`
2. Clic en **"Execute Workflow"** (botón ▶ o "Test workflow")
3. Verificar que el nodo `POST al webhook entrada` muestre código `200`

**Datos que se inyectan:**
| Campo | Valor |
|---|---|
| Proveedor | BULONERIA CONCEPCION S.R.L. |
| CUIT | 30711327912 |
| Comprobante | Factura A 0012-00099999 |
| Fecha | 27/04/2026 |
| Neto | $ 100.000 |
| IVA 21% | $ 21.000 |
| Total | $ 121.000 |
| Ítems | 2 líneas (PRODUCTO DE PRUEBA UNO y DOS) |

---

## PASO 6 — Verificar en MySQL

Ejecutar en la consola MySQL del servidor:

```sql
SELECT
    id,
    estado_proceso,
    fecha_carga,
    numerodocumento,
    letra,
    proveedor_cuit,
    neto,
    iva_21,
    total,
    items_json
FROM db_automatizaciones.staging_facturas
ORDER BY fecha_carga DESC
LIMIT 3;
```

**Resultado esperado:**
- `estado_proceso = PENDIENTE`
- `numerodocumento = 001200099999`
- `total = 121000`
- `items_json` con 2 ítems en JSON

---

## PASO 7 — Verificar email de aprobación

Revisar la bandeja de `pdietrich@ingeniolacorona.com`. Debe llegar un email con:
- Asunto: `[FASE 1 TEST] FACTURA PENDIENTE: BULONERIA CONCEPCION S.R.L...`
- Tabla con datos de la factura
- Botones **APROBAR** y **RECHAZAR**
- Nota en verde indicando que es Fase 1

---

## PASO 8 — Probar aprobación

1. Clic en el botón **APROBAR** en el email
2. Debe abrir el navegador con una página verde de confirmación
3. Debe llegar un segundo email de confirmación a pdietrich

---

## PASO 9 — Verificar MySQL post-aprobación

```sql
SELECT
    id,
    estado_proceso,
    fecha_aprobacion,
    aprobado_por,
    numerodocumento,
    total
FROM db_automatizaciones.staging_facturas
WHERE numerodocumento = '001200099999';
```

**Resultado esperado:**
- `estado_proceso = APROBADO`
- `aprobado_por = pdietrich`
- `fecha_aprobacion` con timestamp actual

---

## PASO 10 — Limpiar registro de prueba

```sql
DELETE FROM db_automatizaciones.staging_facturas
WHERE numerodocumento = '001200099999'
  AND proveedor_cuit  = '30711327912';
```

---

## ✅ Checklist Fase 1 completa

- [ ] Credenciales MySQL configuradas en n8n
- [ ] Credenciales SMTP configuradas en n8n
- [ ] 3 workflows importados
- [ ] URLs de n8n ajustadas en los nodos
- [ ] Workflows de entrada y aprobación activados
- [ ] Test ejecutado → registro en MySQL con estado PENDIENTE
- [ ] Email de aprobación recibido en pdietrich
- [ ] Click en APROBAR → página de confirmación
- [ ] MySQL muestra estado APROBADO
- [ ] Registro de prueba limpiado

---

## FASE 2 — Activar integración SQL Server

Una vez que Fase 1 funciona correctamente:

### 1. Configurar credencial SQL Server en n8n
| Campo | Valor |
|---|---|
| Credential name | `SQL Server CORONA` |
| Server | `serverico` |
| Database | `CORONA` |
| User | usuario con acceso a los SPs |
| Password | contraseña |

### 2. Importar y activar workflows de producción
```
n8n/workflow_facturas_entrada.json          ← reemplaza _fase1
n8n/workflow_facturas_aprobacion.json       ← reemplaza _aprobacion_fase1
```

### 3. Desactivar workflows de Fase 1
- Desactivar `Facturas — Entrada FASE 1`
- Desactivar `Facturas — Aprobación FASE 1`

### 4. Ajustar URL del webhook en Node-RED
El flujo de Node-RED ya apunta a `webhook/facturas-entrada` — no requiere cambios.

### 5. Test real
Ejecutar el test de inyección nuevamente. Esta vez al aprobar se llamará al SP de SQL Server y el registro aparecerá en la cola de pdietrich:
```sql
EXEC UD_EZI_SP_STAGING_PENDIENTES @ESTADO = 'APROBADO'
```
