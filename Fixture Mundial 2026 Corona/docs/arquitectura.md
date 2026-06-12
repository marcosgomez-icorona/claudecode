# Arquitectura

## Objetivo

Publicar un MVP de bajo mantenimiento para operar el fixture interno del Mundial 2026 con Google Sheets como base de datos y Apps Script como API.

## Componentes

- Frontend estatico en `frontend/`.
- Web App Apps Script en `apps-script/`.
- Google Sheets como persistencia.

## Flujo tecnico

1. El usuario abre el frontend publico.
2. JavaScript llama al endpoint Apps Script con `action` y payload JSON.
3. Apps Script valida datos, opera sobre Sheets y devuelve JSON.
4. El frontend renderiza estados de carga, exito, error o vacio.

## Publicacion

- Frontend: hosting estatico en `www.ingeniolacorona.com` o carpeta publica equivalente.
- Backend: Google Apps Script Web App.
- Datos: Google Sheets restringido al administrador.

## Preparacion futura PHP + MySQL

La estructura separa frontend, API client y servicios de dominio para reemplazar Apps Script por una API PHP sin rehacer pantallas.
