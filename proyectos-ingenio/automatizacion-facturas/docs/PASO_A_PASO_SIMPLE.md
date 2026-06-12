# Implementación Fase 1 — Paso a paso
## Automatización Facturas | Ingenio La Corona

---

## ANTES DE EMPEZAR

Tener a mano:
- Acceso a n8n (usuario admin)
- Datos del servidor SMTP para envío de emails
- La carpeta `automatizacion-facturas/` con los archivos del proyecto

---

## PASO 1 — Agregar columna de ítems en MySQL

Conectarse al servidor y ejecutar en MySQL:

```sql
USE db_automatizaciones;

ALTER TABLE staging_facturas
    ADD COLUMN items_json MEDIUMTEXT NULL AFTER email_asunto;
```

✅ Listo cuando no da error.

---

## PASO 2 — Crear credencial MySQL en n8n

1. Abrir n8n → menú izquierdo → **Credentials**
2. Clic en **+ Add credential**
3. Buscar y seleccionar **MySQL**
4. Completar:

| Campo | Valor |
|---|---|
| Name | `MySQL Automatizaciones` |
| Host | `192.168.0.23` |
| Database | `db_automatizaciones` |
| User | `usr_automatizacion` |
| Password | `Corona1234$` |
| Port | `3306` |

5. Clic en **Save** → debe aparecer ✅ Connection tested successfully

---

## PASO 3 — Crear credencial SMTP en n8n

1. n8n → **Credentials → + Add credential**
2. Buscar y seleccionar **SMTP**
3. Completar con los datos del servidor de correo
4. En **Name** escribir exactamente: `SMTP Ingenio La Corona`
5. **Save** → verificar que conecte

---

## PASO 4 — Importar los 3 workflows

Ir a n8n → **Workflows → + New → Import from file**

Importar en este orden:

**1°** → `n8n/workflow_facturas_aprobacion_fase1.json`

**2°** → `n8n/workflow_facturas_entrada_fase1.json`

**3°** → `n8n/workflow_test_inyectar.json`

---

## PASO 5 — Ajustar la URL de n8n

En el workflow **"Facturas — Entrada FASE 1"**:

1. Abrir el nodo **"Validar datos locales"**
2. Buscar esta línea y reemplazar la URL con la de tu n8n:
```javascript
const base = 'https://n8n.ingeniolacorona.com';
```
3. **Save**

En el workflow **"TEST — Inyectar factura de prueba"**:

1. Abrir el nodo **"POST al webhook entrada"**
2. Verificar que la URL sea `https://[tu-n8n]/webhook/facturas-entrada`
3. **Save**

---

## PASO 6 — Activar los workflows

1. Abrir **"Facturas — Aprobación FASE 1"** → toggle arriba a la derecha → **Active**
2. Abrir **"Facturas — Entrada FASE 1"** → toggle → **Active**
3. El workflow de test **NO** se activa (se ejecuta a mano)

---

## PASO 7 — Ejecutar la prueba

1. Abrir el workflow **"TEST — Inyectar factura de prueba"**
2. Clic en **"Execute Workflow"** (botón ▶ arriba a la derecha)
3. Esperar que todos los nodos se pongan en verde

---

## PASO 8 — Verificar el registro en MySQL

Ejecutar en MySQL:

```sql
SELECT estado_proceso, numerodocumento, proveedor_cuit, total, fecha_carga
FROM db_automatizaciones.staging_facturas
ORDER BY fecha_carga DESC
LIMIT 1;
```

Debe mostrar:
```
estado_proceso = PENDIENTE
numerodocumento = 001200099999
proveedor_cuit  = 30711327912
total           = 121000
```

---

## PASO 9 — Verificar el email

Revisar la bandeja de `pdietrich@ingeniolacorona.com`.

Debe llegar un email con asunto **[FASE 1 TEST] FACTURA PENDIENTE...** con los datos de la factura y dos botones: **APROBAR** y **RECHAZAR**.

---

## PASO 10 — Aprobar y verificar

1. Clic en el botón **APROBAR** del email
2. Debe abrir el navegador con una página de confirmación verde
3. Volver a MySQL y verificar:

```sql
SELECT estado_proceso, aprobado_por, fecha_aprobacion
FROM db_automatizaciones.staging_facturas
WHERE numerodocumento = '001200099999';
```

Debe mostrar:
```
estado_proceso  = APROBADO
aprobado_por    = pdietrich
fecha_aprobacion = (fecha y hora actual)
```

---

## ✅ Todo funcionó — limpiar el registro de prueba

```sql
DELETE FROM db_automatizaciones.staging_facturas
WHERE numerodocumento = '001200099999';
```

---

## ❌ Si algo no funciona

| Síntoma | Dónde mirar |
|---|---|
| El nodo MySQL da error de conexión | Verificar que el servidor MySQL acepte conexiones remotas desde n8n |
| No llega el email | Revisar credencial SMTP — probar con **Test connection** |
| El botón APROBAR abre página de error | Verificar que el workflow de aprobación esté activo y la URL de n8n sea correcta |
| n8n muestra error en el nodo "INSERT MySQL" | Verificar que el campo `items_json` exista (Paso 1) |

---

## SIGUIENTE PASO — Fase 2 (SQL Server)

Cuando todo lo anterior esté verificado, avisarle al equipo para activar la integración real con Calipso.
