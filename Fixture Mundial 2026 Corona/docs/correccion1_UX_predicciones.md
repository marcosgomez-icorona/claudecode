📌 Rol:
Actúa como un Frontend Developer Senior, UX/UI Designer experto en apps deportivas interactivas, Bootstrap 5, JavaScript Vanilla, integración frontend-backend, validación de usuarios registrados y experiencias de predicción tipo fixture mundialista.

Tu objetivo es perfeccionar predicciones.html de la app Corona Mundial 2026, manteniendo la interfaz interactiva ya lograda en 3 pantallas, pero agregando una validación obligatoria de participante por email antes de permitir guardar predicciones.

Debes trabajar como un experto en frontend que prioriza:

Usabilidad.
Interactividad.
Validación correcta de usuario.
Vinculación de predicciones al email registrado.
Persistencia compatible con ranking.
Claridad visual.
Experiencia mobile first.
Mantenimiento del estilo visual y de fondo actual del proyecto.
No romper lo nuevo que ya está bien implementado.
⚡ Acción:
Ajusta y perfecciona predicciones.html para que el flujo de predicciones funcione correctamente en 3 pantallas, manteniendo el diseño visual ya logrado y agregando una capa obligatoria de identificación del participante registrado.

El flujo final debe funcionar así:

Paso previo obligatorio — Validación de email registrado

Antes de permitir que el usuario elija grupos o guarde predicciones, la pantalla debe solicitar el email del participante.

Debe permitir:

Ingresar el email registrado.
Validar que el email exista en la hoja/base de participantes.
Recuperar los datos del participante asociado:
participante_id
nombre_apellido
email
area
Guardar temporalmente el participante activo en el estado local del frontend.
Vincular toda predicción posterior con ese email y/o participante_id.
Mostrar el nombre del participante activo en la interfaz.
Bloquear el flujo si el email no está registrado.

El usuario no debe poder guardar predicciones sin un email validado.

Pantalla 1 — Vista general de grupos

Después de validar el email, el usuario accede a una pantalla con todos los grupos del Mundial.

Debe mostrar:

Banner superior con contador regresivo al primer partido.
Mantener el estilo de fondo actual del proyecto.
Título: FIFA World Cup 2026 o Corona Mundial 2026.
Texto guía breve: “Elegí un grupo y armá tu predicción de cómo van a quedar las posiciones.”
Identificación visible del participante:
“Prediciendo como: [Nombre y apellido]”
“Área: [Área]”
Opción discreta para cambiar email.
Barra de progreso de grupos completados.
Grid responsive con 12 cards, una por grupo: A, B, C, D, E, F, G, H, I, J, K y L.
Cada card debe mostrar:
Letra del grupo.
Estado: “Pendiente” o “✓ Predicho”.
Lista de equipos ordenados del 1º al 4º.
Banderas o placeholders visuales.
Color distintivo por grupo.
Borde verde si el grupo ya fue predicho por el email validado.
Hover interactivo.
Al hacer clic en una card de grupo, se debe navegar a la Pantalla 2.

Importante: el estado “✓ Predicho” debe depender de las predicciones guardadas para ese participante/email, no de un estado global.

Pantalla 2 — Predicción del grupo seleccionado

Cuando el usuario selecciona un grupo, debe acceder a una pantalla específica del grupo.

Debe mostrar:

Botón “← Volver”.
Mantener el estilo de fondo actual del proyecto.
Encabezado con letra y nombre del grupo, por ejemplo: Grupo A.
Identificación compacta del participante activo.
Texto guía: “Tocá una celda y predecí el resultado de cada partido.”
Indicador de progreso del grupo: “0 / 6 partidos”, “3 / 6 partidos” o “6 / 6 partidos”.
Barra de progreso horizontal.
Opción destacada:
“Predicción manual — tocá cada celda”.
Botón “Predicción automática”, si ya existe o si se desea mantener como funcionalidad visual.
Matriz visual de partidos tipo fixture:
Equipos en columnas y filas.
Diagonal bloqueada con patrón rayado.
Celdas editables para cada partido.
Celdas con resultados tipo 2 - 0, 1 - 1, 0 - 0.
Colores de celda según resultado:
Verde para victoria del equipo de la fila.
Rojo para derrota del equipo de la fila.
Amarillo/naranja para empate.
Gris/rayado para celda no editable.
Scroll horizontal si es necesario en mobile.
Tabla resultante debajo con:
Posición.
Equipo.
PJ.
G.
E.
P.
GF.
GC.
DG.
PTS.
Leyenda:
Clasifica.
Mejor 3º.
Eliminado.
Botones inferiores:
“← Cancelar”.
“Guardar predicción”.
Al tocar una celda editable, debe abrirse la Pantalla 3.

Al guardar la predicción del grupo, debe enviarse al backend con:

participante_id
email
area
grupo_id
predicciones_partidos
tabla_calculada
fecha_guardado
estado
Pantalla 3 — Card/modal de predicción de partido

Cuando el usuario toca una celda, debe abrirse una interfaz compacta para predecir el resultado del partido.

Debe mostrar:

Título: Predecí el resultado.
Equipo A con bandera, nombre y controles.
Equipo B con bandera, nombre y controles.
Separador “VS”.
Controles de goles:
Botón −.
Número grande.
Botón +.
Botones:
“Borrar”.
“Cancelar”.
“Guardar”.
Al guardar:
Actualizar la celda correspondiente en la matriz.
Recalcular automáticamente la tabla resultante.
Actualizar el progreso del grupo.
Mantener la predicción vinculada al participante activo.
Cerrar el modal/card.
Al cancelar:
Cerrar sin modificar.
Al borrar:
Limpiar la predicción de esa celda.
🌎 Contexto:
Proyecto

Nombre: Corona Mundial 2026
Empresa: Ingenio La Corona
Archivo principal a modificar: predicciones.html

Stack obligatorio:

HTML5.
Bootstrap 5.
CSS personalizado.
JavaScript Vanilla.

Archivos esperados:

frontend/predicciones.html
frontend/assets/css/styles.css
frontend/assets/js/predicciones.js
frontend/assets/js/api.js
frontend/assets/js/config.js

No usar:

React.
Vue.
Angular.
Tailwind.
Librerías pesadas.
Frameworks adicionales.
Contexto funcional crítico

La predicción no puede ser anónima.

Cada predicción debe quedar vinculada al participante registrado para que después el sistema pueda:

Calcular puntos.
Vincular puntos al email.
Vincular puntos al área.
Armar ranking individual.
Armar ranking por áreas.
Evitar predicciones duplicadas o anónimas.
Permitir cargar o actualizar predicciones existentes del mismo participante.
Regla crítica de validación

Antes de permitir predicciones, validar email.

Si el email existe:

Habilitar flujo de grupos.
Mostrar datos del participante.
Cargar predicciones previas de ese participante, si existen.
Marcar grupos ya predichos por ese participante.
Permitir editar predicciones si todavía están abiertas.

Si el email no existe:

Mostrar mensaje claro:
“No encontramos un participante registrado con ese email.”
“Verificá el correo o registrate antes de predecir.”
Mostrar botón o enlace:
“Ir a Registro”
No permitir avanzar al flujo de grupos.
Regla de vinculación

Toda predicción guardada debe incluir como mínimo:

{
  "participante_id": "string",
  "nombre_apellido": "string",
  "email": "string",
  "area": "string",
  "grupo_id": "string",
  "predicciones": [],
  "tabla_calculada": [],
  "fecha_guardado": "ISO timestamp",
  "estado": "completa | parcial"
}
Regla de estilo visual

Mantener el estilo visual ya logrado en las pantallas nuevas.

No rediseñar desde cero.

No tocar innecesariamente:

Fondo general del proyecto.
Estilo premium existente.
Cards de grupos.
Matriz de predicciones.
Modal/card de predicción.
Tabla resultante.
Contador superior.
Barra de progreso.
Microinteracciones ya logradas.

Solo ajustar lo necesario para:

Agregar validación de email.
Mostrar participante activo.
Vincular predicciones al email.
Cargar predicciones existentes del usuario.
Guardar correctamente con identidad del participante.
Mantener coherencia visual.
Referencias visuales

La interfaz debe conservar la estética ya lograda basada en:

Pantalla 1: cards de grupos interactivas.
Pantalla 2: matriz de resultados y tabla resultante.
Pantalla 3: modal compacto de marcador.
Fondo visual actual del proyecto.
Estilo deportivo moderno, claro, limpio y mobile first.
📤 Salida:

Genera una implementación o instrucciones para Codex con esta estructura:

1. Diagnóstico del ajuste necesario

Explica que la interfaz visual está casi perfecta, pero debe corregirse el flujo funcional para validar email y vincular predicciones al participante registrado.

2. Nuevo flujo UX completo

Definir estos estados:

emailValidationView — Validación de participante por email.
groupsView — Vista general de grupos.
groupPredictionView — Predicción del grupo seleccionado.
matchPredictionModal — Modal/card para cargar resultado de partido.

El flujo debe ser:

Ingresar email
→ Validar participante registrado
→ Si existe, cargar datos y predicciones previas
→ Mostrar grupos
→ Elegir grupo
→ Cargar resultados del grupo
→ Guardar predicción vinculada al participante
→ Actualizar estado del grupo
→ Participar en ranking
3. Estructura HTML recomendada

Crear o adaptar secciones como:

<section id="emailValidationView"></section>
<section id="groupsView" class="d-none"></section>
<section id="groupPredictionView" class="d-none"></section>
<div id="matchPredictionModal" class="prediction-modal d-none"></div>

La navegación entre pantallas debe hacerse con clases como d-none, sin recargar la página.

4. Componentes UI obligatorios
Validación de email

Crear componentes:

.participant-validation-card
.participant-email-input
.participant-validation-actions
.participant-active-badge
.participant-error-message
.participant-success-message

La card debe integrarse al estilo actual, sin romper el fondo del proyecto.

Debe incluir:

Título: “Ingresá tu email registrado”.
Texto: “Usaremos tu email para vincular tus predicciones al ranking.”
Input de email.
Botón: “Continuar”.
Link: “¿Todavía no estás registrado? Registrate acá.”
Mensajes de estado:
Validando...
Participante encontrado.
Email no registrado.
Error de conexión.
Vista de grupos

Mantener componentes existentes:

.countdown-banner
.prediction-page-title
.groups-progress
.groups-grid
.group-card
.group-badge
.group-status
.team-position-row

Agregar:

.active-participant-summary
.change-participant-link
Vista de grupo

Mantener componentes existentes:

.group-detail-header
.group-progress-bar
.prediction-mode-card
.fixture-matrix-wrapper
.fixture-matrix
.match-cell
.match-cell-win
.match-cell-draw
.match-cell-loss
.match-cell-disabled
.resulting-table-card
.classification-legend
.prediction-actions

Agregar:

.participant-context-mini
Modal de partido

Mantener:

.match-prediction-overlay
.match-prediction-card
.match-team
.score-control
.score-button
.score-value
.modal-actions

No rediseñar visualmente el modal salvo que sea necesario para coherencia.

5. Lógica JavaScript obligatoria

Implementar o adaptar funciones como:

initPredictionsPage()
renderEmailValidationView()
validateParticipantEmail(email)
setActiveParticipant(participant)
clearActiveParticipant()
loadParticipantPredictions(participant)
renderGroupsView()
renderGroupCards()
openGroupPrediction(groupId)
renderGroupPrediction(groupId)
renderFixtureMatrix(groupId)
openMatchPrediction(groupId, teamAId, teamBId)
saveMatchPrediction()
clearMatchPrediction()
cancelMatchPrediction()
recalculateGroupTable(groupId)
updateGroupProgress(groupId)
saveGroupPrediction(groupId)
buildPredictionPayload(groupId)
goBackToGroups()
6. Integración con API o backend

Si existe api.js, agregar o reutilizar métodos como:

api.getParticipantByEmail(email)
api.getPredictionsByParticipant(email)
api.saveGroupPrediction(payload)
api.updateGroupPrediction(payload)

Si el backend usa Google Apps Script, preparar payloads compatibles.

La validación de email debe consultar la fuente real de participantes cuando exista.

Si no existe backend disponible, usar mock temporal claramente separado:

const USE_MOCKS = true;

Pero dejar la estructura lista para reemplazarlo por API real.

7. Payload obligatorio al guardar predicción

La función buildPredictionPayload(groupId) debe devolver una estructura similar a:

{
  "participante_id": "P-001",
  "nombre_apellido": "Nombre Apellido",
  "email": "usuario@empresa.com",
  "area": "Sistemas",
  "grupo_id": "A",
  "grupo_nombre": "Grupo A",
  "predicciones_partidos": [
    {
      "partido_id": "A-01",
      "equipo_a": "México",
      "equipo_b": "Sudáfrica",
      "goles_a": 2,
      "goles_b": 0,
      "ganador_predicho": "México",
      "es_empate_predicho": false
    }
  ],
  "tabla_calculada": [
    {
      "posicion": 1,
      "equipo": "Corea del Sur",
      "pj": 3,
      "g": 2,
      "e": 1,
      "p": 0,
      "gf": 4,
      "gc": 1,
      "dg": 3,
      "pts": 7
    }
  ],
  "estado": "completa",
  "fecha_guardado": "2026-01-01T12:00:00.000Z"
}
8. Reglas de validación

Validar:

Email requerido.
Formato básico de email.
Email existente en participantes.
Participante activo antes de guardar.
Grupo existente.
6 partidos completos antes de marcar grupo como completo.
No guardar predicciones anónimas.
No sobrescribir predicciones de otro participante.
Si existe predicción previa del mismo email y grupo, actualizarla en vez de duplicarla.
9. Reglas de cálculo de tabla

Mantener el cálculo existente:

Victoria: 3 puntos.
Empate: 1 punto.
Derrota: 0 puntos.

Recalcular:

PJ.
G.
E.
P.
GF.
GC.
DG.
PTS.

Ordenar tabla por:

Mayor PTS.
Mayor DG.
Mayor GF.
Orden alfabético.
10. Reglas visuales importantes

Mantener el diseño ya logrado.

No romper:

Fondo premium del proyecto.
Cards de grupos.
Matriz fixture.
Modal de marcador.
Tabla resultante.
Colores de estado.
Responsive mobile first.
Scroll horizontal de la matriz.
Botones existentes.
Microinteracciones.

Agregar la validación de email como una card integrada, usando el mismo lenguaje visual.

La validación debe sentirse como un paso natural, no como una pantalla externa improvisada.

11. Archivos a modificar o crear

Modificar principalmente:

frontend/predicciones.html
frontend/assets/js/predicciones.js
frontend/assets/css/styles.css

Opcionalmente actualizar:

frontend/assets/js/api.js
frontend/assets/js/config.js
docs/predicciones-flujo.md
12. Criterios de aceptación

La implementación estará lista cuando:

Al abrir predicciones.html, primero se solicite el email registrado.
El usuario no pueda avanzar sin un email válido.
Si el email no está registrado, se muestre error claro y link a Registro.
Si el email existe, se carguen nombre, email, área y participante_id.
Se muestren los grupos después de validar el email.
Los grupos predichos se marquen según ese participante/email.
Al elegir un grupo, se mantenga visible el participante activo.
Al guardar una predicción, el payload incluya email y participante_id.
Si el mismo email ya predijo ese grupo, se actualice la predicción anterior.
Las predicciones queden listas para ranking individual y ranking por área.
El fondo y estilo visual actual del proyecto se mantengan.
No se rompa ninguna funcionalidad existente.
La experiencia siga siendo fluida, interactiva y mobile first.
Prompt maestro final para usar directamente en Codex

Perfecciona frontend/predicciones.html sin rediseñar desde cero. La interfaz interactiva de predicciones en 3 pantallas ya está muy bien lograda y debe mantenerse: vista general de grupos, vista de matriz por grupo y modal/card para cargar resultado de partido.

El ajuste crítico es agregar antes del flujo una validación obligatoria de email registrado. Al abrir predicciones.html, primero debe mostrarse una card integrada al estilo actual que pida: “Ingresá tu email registrado”. Ese email debe validarse contra los participantes registrados mediante backend/API si existe, o mediante mock temporal separado si todavía no está disponible.

Si el email existe, cargar participante_id, nombre_apellido, email y area, guardar ese participante como activeParticipant, cargar sus predicciones previas y recién entonces mostrar la vista de grupos. Si el email no existe, mostrar un error claro: “No encontramos un participante registrado con ese email. Verificá el correo o registrate antes de predecir.” Agregar link o botón a Registro.

Ninguna predicción puede ser anónima. Toda predicción guardada debe quedar vinculada al participante activo mediante participante_id, email, nombre_apellido y area, para que luego pueda competir en ranking individual y ranking por áreas.

Mantén intacto el estilo visual nuevo ya logrado: fondo premium del proyecto, cards de grupos, matriz fixture, modal de marcador, tabla resultante, contador superior, barra de progreso, colores de estado, scroll horizontal y responsive mobile first. No cambies fondos ni rediseñes componentes que ya funcionan visualmente. Solo integra la validación de email y la vinculación de datos de forma coherente.

Implementa o ajusta estas funciones en frontend/assets/js/predicciones.js: initPredictionsPage, renderEmailValidationView, validateParticipantEmail, setActiveParticipant, clearActiveParticipant, loadParticipantPredictions, renderGroupsView, openGroupPrediction, openMatchPrediction, saveMatchPrediction, recalculateGroupTable, updateGroupProgress, buildPredictionPayload, saveGroupPrediction y goBackToGroups.

Si existe api.js, reutiliza o agrega métodos como api.getParticipantByEmail(email), api.getPredictionsByParticipant(email), api.saveGroupPrediction(payload) y api.updateGroupPrediction(payload). No rompas endpoints existentes. Si no hay backend disponible, usa USE_MOCKS = true de forma clara y fácil de reemplazar.

El payload de guardado debe incluir obligatoriamente: participante_id, nombre_apellido, email, area, grupo_id, grupo_nombre, predicciones_partidos, tabla_calculada, estado y fecha_guardado. Si el mismo email ya guardó una predicción para ese grupo, actualizarla en vez de duplicarla.

Valida: email requerido, formato de email, existencia del email en participantes, participante activo antes de guardar, grupo existente, partidos completos antes de marcar grupo como completo, no guardar predicciones anónimas y no sobrescribir datos de otro participante.

Modifica principalmente frontend/predicciones.html, frontend/assets/js/predicciones.js y frontend/assets/css/styles.css. Opcionalmente actualiza frontend/assets/js/api.js, frontend/assets/js/config.js y docs/predicciones-flujo.md.

Criterios de aceptación: al abrir la página se pide email; solo emails registrados pueden avanzar; se muestra el participante activo; se cargan grupos y predicciones previas de ese email; el estado ✓ Predicho depende del participante; cada guardado incluye email y participante_id; se actualiza predicción previa si corresponde; el diseño visual y fondo actual se mantienen; no se rompe la experiencia interactiva ni el responsive.