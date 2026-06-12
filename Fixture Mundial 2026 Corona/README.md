# Corona Mundial 2026

MVP web interno para Ingenio La Corona orientado a registrar participantes, cargar predicciones del Mundial FIFA 2026, administrar partidos/resultados y publicar rankings individuales y por area.

## Stack tecnico

- Frontend: HTML5, Bootstrap 5 por CDN y JavaScript Vanilla.
- Backend MVP: Google Apps Script publicado como Web App.
- Persistencia: Google Sheets.
- Sincronizacion opcional: API propia alimentada con datos oficiales.
- Backend futuro documentado: PHP + MySQL.

## Alcance MVP

- Registro de participantes con email unico.
- Carga y actualizacion de predicciones hasta fecha limite.
- Administracion simple con clave configurable.
- Alta/edicion de partidos.
- Carga de resultados.
- Recalculo de puntajes.
- Ranking individual y ranking por area.
- Paginas de reglamento y premios con contenido desde configuracion o fallback estatico.

## Configurar Google Sheets

1. Crear una planilla nueva en Google Sheets.
2. Copiar el ID de la URL de la planilla.
3. Crear las hojas documentadas en [docs/modelo-datos.md](docs/modelo-datos.md) o ejecutar `setupSpreadsheet()` desde Apps Script.
4. En la hoja `Configuracion`, definir como minimo:
   - `admin_clave`: clave administrativa simple.
   - `fecha_inicio_mundial`: fecha para contador regresivo, formato ISO.
   - `reglamento_html`: contenido del reglamento.
   - `premios_html`: contenido de premios.

## Desplegar Google Apps Script

1. Crear un proyecto Apps Script asociado o independiente.
2. Copiar los archivos de `apps-script/`.
3. En `Code.gs`, configurar `SPREADSHEET_ID` con el ID de la planilla.
4. Ejecutar `setupSpreadsheet()` una vez para crear encabezados y configuracion inicial.
5. Publicar como Web App:
   - Ejecutar como: usuario propietario.
   - Acceso: cualquier persona con el enlace.
6. Copiar la URL publicada.

## Configurar endpoint frontend

Editar [frontend/assets/js/config.js](frontend/assets/js/config.js) y reemplazar:

```js
apiBaseUrl: 'PEGAR_URL_WEB_APP_APPS_SCRIPT'
```

Para revisar sin backend, dejar el endpoint vacio. El frontend usa datos fallback de lectura, pero los formularios requieren Apps Script para persistir.

## Probar el MVP

Abrir [frontend/index.html](frontend/index.html) en el navegador o servir la carpeta `frontend/` con un servidor estatico.

Pruebas minimas:

1. Registrar un participante.
2. Intentar registrar el mismo email y verificar rechazo.
3. Crear partidos desde admin.
4. Cargar prediccion antes del cierre.
5. Confirmar bloqueo de prediccion despues del cierre.
6. Cargar resultado real.
7. Recalcular puntajes.
8. Ver ranking individual ordenado.
9. Ver ranking por area con promedio Top 5.
10. Revisar responsive en celular y escritorio.

Detalle operativo en [docs/pruebas-manuales.md](docs/pruebas-manuales.md).

## Sincronizar fixture desde API propia

La app incluye un adaptador para consumir una API propia con datos oficiales y cargar Google Sheets.

1. Configurar `fixture_api_url` en la hoja `Configuracion`.
2. Configurar `fixture_api_token` si corresponde.
3. Poner `fixture_sync_enabled = true`.
4. Ejecutar manualmente `syncOfficialFixture({ force: true })`.
5. Si funciona, ejecutar una vez `createFiveMinuteFixtureTrigger()` para sincronizar cada 5 minutos.

Contrato tecnico en [docs/api-propia-fixture.md](docs/api-propia-fixture.md).

Si no hay API externa, dejar `fixture_api_url` vacio. Apps Script usa el fixture semilla de `LocalFixtureData.gs`.

El panel [frontend/admin.html](frontend/admin.html) incluye un boton `Sincronizar fixture` para forzar la sincronizacion sin entrar a Apps Script.

## Exclusiones MVP

Quedan fuera del MVP: login seguro, OAuth, roles complejos, emails, WhatsApp, API FIFA, PWA, notificaciones, auditoria avanzada, dashboard avanzado, estadisticas y migracion PHP/MySQL. Ver [VERSION_2.md](VERSION_2.md).
