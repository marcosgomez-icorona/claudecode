# Instrucciones para Codex

## Contexto

Proyecto MVP "Corona Mundial 2026" para Ingenio La Corona. La prioridad es entregar una aplicacion simple, mantenible y publicable rapido para 50 a 100 participantes.

## Stack permitido

- HTML5.
- Bootstrap 5 por CDN.
- JavaScript Vanilla.
- Google Apps Script.
- Google Sheets.

No agregar frameworks frontend, build tools ni dependencias salvo necesidad claramente justificada.

## Reglas de simplicidad

- Mantener funciones cortas y legibles.
- Centralizar endpoint en `frontend/assets/js/config.js`.
- Centralizar HTTP en `frontend/assets/js/api.js`.
- No duplicar reglas de negocio criticas entre archivos cuando puedan quedar en servicios.
- Mantener validaciones en frontend y backend.
- Documentar cambios operativos en `README.md` o `docs/`.

## Fuera de MVP

No implementar login complejo, OAuth, roles, emails automaticos, WhatsApp, API FIFA, PWA, notificaciones, estadisticas avanzadas ni migracion PHP/MySQL. Registrar ideas futuras en `VERSION_2.md`.

## Convenciones

- IDs: prefijo + timestamp + random corto. Ejemplo: `par_20260529103000_ab12`.
- Fechas: ISO 8601.
- Hojas: nombres exactos documentados en `docs/modelo-datos.md`.
- Archivos frontend en minuscula y con guion cuando corresponda.
- Servicios Apps Script separados por dominio.

## Validacion

Antes de cerrar cambios, verificar:

- Registro con email unico.
- Predicciones abiertas/cerradas por fecha limite.
- Resultado exacto: 5 puntos sin sumar extra por ganador.
- Ganador o empate correcto: 2 puntos.
- Ranking individual con desempates.
- Ranking por area con promedio Top 5.
- Pantallas responsive.

## Reporte de cambios

Informar archivos creados/modificados, pruebas realizadas, pendientes reales y riesgos de publicacion.
