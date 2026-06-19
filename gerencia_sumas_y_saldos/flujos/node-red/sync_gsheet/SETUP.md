# Sync Sumas y Saldos → Google Sheets — Guía de Configuración

## Paso 1: Instalar el nodo en Node-RED

```bash
# En el servidor (192.168.0.23), como el usuario que corre Node-RED:
cd ~/.node-red
npm install node-red-contrib-google-sheets

# Reiniciar Node-RED
sudo systemctl restart node-red
# o si corre con pm2:
pm2 restart node-red
```

## Paso 2: Crear Service Account y Google Sheet

**Seguí la guía detallada:** [`CREAR_SERVICE_ACCOUNT.md`](CREAR_SERVICE_ACCOUNT.md)

Resumen de lo que vas a obtener al terminar:
- Un archivo **JSON** con la clave privada de la service account
- El node-red-sheets-sync@sync-calipso-corona.iam.gserviceaccount.com de la service account (formato `...@...iam.gserviceaccount.com`)
- Una **Google Sheet** llamada `SumasSaldos_Corona` con 9 encabezados
- El **Sheet ID** (string largo en la URL entre `/d/` y `/edit`)

## Paso 5: Importar el flow en Node-RED

1. Abrir Node-RED: http://192.168.0.23:1880
2. Menú ≡ → Import → pegar el contenido de `flow_sync_sumas_saldos_gsheet_v2.json`
3. **Configurar el nodo gauth** (doble clic en "Google Auth (gauth)"):
   - Pegar TODO el contenido del JSON de la service account descargado
4. **Configurar el nodo GSheet** (doble clic en "Append a SumasSaldos"):
   - Sheet ID: pegar el ID de tu Google Sheet
   - Cells/Range: `SumasSaldos!A:I` (o como se llame la pestaña)
   - Operation: append
5. Hacer **Deploy**

## Paso 6: Probar

1. En Node-RED, clic en el botón del inject "▶ Ejecutar sync ahora"
2. Ver la pestaña de debug para confirmar éxito
3. Abrir la Google Sheet y verificar los datos

---

## Columnas en Google Sheets

| Col | Campo | Tipo | Descripción |
|---|---|---|---|
| A | CODIGO | Texto | Código de cuenta contable |
| B | CUENTA | Texto | Nombre de la cuenta |
| C | RUBRO | Texto | Nombre del rubro |
| D | DEBE_PERIODO | Número | Total debe del período |
| E | HABER_PERIODO | Número | Total haber del período |
| F | SALDO_PERIODO | Número | Debe - Haber |
| G | PERIODO | Texto | Fechas inicio-fin (YYYYMMDD-YYYYMMDD) |
| H | FECHA_SYNC | Texto | Timestamp ISO del sync |
| I | SYNC_UUID | Texto | UUID único del sync |

## Schedule

El sync está configurado para ejecutarse diariamente a las **06:00 AM** (hora del servidor).
Para cambiarlo, editar el nodo "⏰ Schedule diario 06:00" y modificar el crontab.

## Troubleshooting

- **Error de auth**: verificar que el JSON de la service account esté completo en el nodo gauth
- **Sheet no encontrada**: verificar el Sheet ID y que la service account tenga acceso de editor
- **Sin datos**: revisar la pestaña debug de Node-RED para ver warnings del nodo MSSQL
