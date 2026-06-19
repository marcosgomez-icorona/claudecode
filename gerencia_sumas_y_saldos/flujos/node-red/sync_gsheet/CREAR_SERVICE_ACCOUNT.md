# Crear Google Service Account — Paso a paso

## Objetivo

Crear una cuenta de servicio en Google Cloud que permita a Node-RED escribir en Google Sheets sin intervención humana.

---

## Paso 1: Entrar a Google Cloud Console

1. Abrí https://console.cloud.google.com
2. Iniciá sesión con la cuenta de Google que usen en el ingenio (o creá una nueva tipo `ingeniolacorona@gmail.com`)
3. Si es la primera vez, aceptá los términos

---

## Paso 2: Crear proyecto

1. Arriba a la izquierda, clic en el selector de proyecto (al lado del logo de Google Cloud)
   ![select project](https://storage.googleapis.com/gweb-cloud-console/customers/select-project.png)

2. Clic en **NEW PROJECT** (botón azul arriba a la derecha del popup)

3. Completá:
   - **Project name**: `Ingenio La Corona`
   - **Location**: Dejalo como está (No organization)
   - Clic en **CREATE**

4. Esperá unos segundos a que se cree. Vas a ver una notificación verde cuando esté listo.

5. Seleccionalo desde el selector de proyectos (arriba).

---

## Paso 3: Habilitar Google Sheets API

1. En la barra de búsqueda superior, escribí: `Google Sheets API`
2. Clic en el resultado **Google Sheets API** (ícono verde con un papel)
3. Clic en el botón azul **ENABLE**
4. Esperá a que diga "API enabled"

---

## Paso 4: Crear la Service Account

1. En la barra de búsqueda superior, escribí: `Service Accounts`
2. Clic en el resultado **Service Accounts** (ícono de llave)
3. Clic en **+ CREATE SERVICE ACCOUNT** (botón azul arriba)

### Paso 4a — Nombre

4. Completá:
   - **Service account name**: `node-red-sheets-sync`
   - **Service account ID**: se autocompleta solo, dejalo
   - **Description**: `Escritura automatizada de Sumas y Saldos desde Node-RED a Google Sheets`
5. Clic en **CREATE AND CONTINUE**

### Paso 4b — Rol

6. En el campo **Select a role**, escribí: `Editor`
7. Seleccioná **Editor** (acceso completo a Google Sheets)
   - Alternativa más restrictiva: buscar `Sheets` y elegir **Google Sheets API > Sheets Admin** si preferís limitarlo solo a Sheets
8. Clic en **CONTINUE**

### Paso 4c — Opcional (saltear)

9. El tercer paso "Grant users access" es opcional. Dejalo vacío.
10. Clic en **DONE**

---

## Paso 5: Crear la clave JSON

1. Ya estás en la lista de Service Accounts. Buscá `node-red-sheets-sync`
2. Clic en el email de la service account (la fila, no el checkbox)
3. Arriba, pestaña **KEYS**
4. Clic en **ADD KEY** → **Create new key**
5. Elegí **JSON** (ya debería estar seleccionado)
6. Clic en **CREATE**
7. **Se descarga un archivo `.json` automáticamente.** Guardalo bien. Este archivo contiene las credenciales privadas.
8. Clic en **CLOSE**

---

## Paso 6: Obtener el email de la Service Account

1. Volvé a la lista de Service Accounts
2. Copiá el **Email** de `node-red-sheets-sync`
   - Tiene este formato: `node-red-sheets-sync@ingenio-la-corona.iam.gserviceaccount.com`

**Este email es el que vas a usar en el Paso 4 del SETUP.md para compartir la Google Sheet.**

---

## Paso 7: Crear la Google Sheet

1. Abrí https://sheets.new
2. En la fila 1 (la primera), poné estos encabezados:

| A | B | C | D | E | F | G | H | I |
|---|---|---|---|---|---|---|---|---|
| CODIGO | CUENTA | RUBRO | DEBE_PERIODO | HABER_PERIODO | SALDO_PERIODO | PERIODO | FECHA_SYNC | SYNC_UUID |

3. Seleccioná la fila 1 entera y ponela en **Negrita** (Ctrl+B)
4. Menú: **Ver → Congelar → 1 fila**
5. Renombrá la hoja: doble clic en "Sheet1" (abajo) y poné `SumasSaldos`
6. Renombrá el archivo: clic en "Untitled spreadsheet" (arriba izquierda) y poné `SumasSaldos_Corona`

---

## Paso 8: Compartir la Sheet

1. Botón verde **Compartir** (arriba a la derecha)
2. Pegá el **email de la service account** que copiaste en el Paso 6
3. Asegurate que el rol diga **Editor**
4. Desmarcá "Notify people" (no hace falta)
5. Clic en **Share**

---

## Paso 9: Obtener el Sheet ID

1. Mirá la URL de tu Google Sheet:
   ```
   https://docs.google.com/spreadsheets/d/1UuVIH2O38XK0TfPMGHk0HG_ixGLtLk6WoBKh4YSrDm4/edit
                                    ══════════════════════════════════════════
                                    ESTE es el Sheet ID
   ```
2. Copiá el string largo entre `/d/` y `/edit`. Ese es el **Sheet ID**.

---

## Resumen: lo que necesitás pasarle a Node-RED

| Dato | Dónde va | Origen |
|---|---|---|
| **JSON completo** de la clave | Nodo **gauth** → campo `key` | Archivo descargado en Paso 5 |
| **Sheet ID** | Nodo **GSheet** → campo `sheet` | URL de la sheet (Paso 9) |
| **Rango** | Nodo **GSheet** → campo `cells` | `SumasSaldos!A:I` |
| **Operación** | Nodo **GSheet** → campo `method` | `append` |

---

## Verificación rápida

Antes de importar el flow en Node-RED, confirmá que:

- [ donde] La Google Sheet existe y tiene los 9 encabezados en la fila 1
- [ ] La service account tiene acceso de **Editor** a la sheet
- [ ] Tenés el archivo JSON de la clave a mano (abrilo con bloc de notas, deberías ver `"type": "service_account"`)
- [ ] Copiaste el Sheet ID de la URL
