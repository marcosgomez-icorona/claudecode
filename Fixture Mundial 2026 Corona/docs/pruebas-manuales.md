# Pruebas manuales MVP

## Preparacion

1. Configurar `SPREADSHEET_ID` en `apps-script/Code.gs`.
2. Ejecutar `setupSpreadsheet()` desde Apps Script.
3. Publicar Apps Script como Web App.
4. Configurar `apiBaseUrl` en `frontend/assets/js/config.js`.
5. Abrir `frontend/index.html`.

## Casos obligatorios

### Registro

- Registrar participante con nombre, email y area validos.
- Verificar alta en hoja `Participantes`.
- Repetir el mismo email.
- Resultado esperado: el segundo intento se rechaza.

### Partidos

- Entrar a `admin.html`.
- Ingresar clave `admin_clave`.
- Crear un partido futuro con fecha limite posterior a la hora actual.
- Verificar alta en hoja `Partidos`.

### Predicciones abiertas

- Entrar a `predicciones.html`.
- Usar email registrado.
- Seleccionar partido abierto.
- Cargar ganador y resultado exacto.
- Verificar alta en hoja `Predicciones`.
- Repetir para el mismo partido con otro resultado.
- Resultado esperado: actualiza la prediccion vigente, no duplica.

### Predicciones cerradas

- Cambiar `fecha_limite_prediccion` de un partido a una fecha pasada.
- Intentar guardar prediccion.
- Resultado esperado: operacion bloqueada.

### Resultados y puntajes

- Desde admin, cargar resultado exacto de un partido.
- Verificar hoja `Resultados`.
- Verificar hoja `Puntajes`.
- Caso exacto: 5 puntos.
- Caso ganador correcto no exacto: 2 puntos.
- Caso empate correcto no exacto: 2 puntos.
- Caso incorrecto: 0 puntos.

### Rankings

- Ejecutar recalculo desde admin.
- Verificar `Ranking Individual` ordenado por puntaje, exactos, ganadores, fecha de registro y nombre.
- Verificar `Ranking Areas` con promedio de mejores 5 participantes por area.

### Visual

- Revisar home, registro, predicciones, ranking, ranking por area, reglamento, premios y admin en escritorio.
- Repetir en ancho movil.
- Confirmar que no haya textos superpuestos ni tablas inutilizables.
