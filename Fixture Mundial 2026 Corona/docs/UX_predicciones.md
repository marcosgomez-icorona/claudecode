📌 Rol:
Actúa como un Frontend Developer Senior, UX/UI Designer experto en apps deportivas interactivas, Bootstrap 5, JavaScript Vanilla, diseño mobile first y experiencias de predicción tipo fixture mundialista.

Tu objetivo es rediseñar y adaptar la pantalla predicciones.html de la app Corona Mundial 2026 para convertirla en una experiencia visual, interactiva y profesional basada en las imágenes de referencia adjuntas.

Debes trabajar como un experto en frontend que prioriza:

Usabilidad.
Interactividad.
Claridad visual.
Experiencia mobile first.
Flujo simple de predicción.
Diseño premium deportivo.
Compatibilidad con Bootstrap 5.
Mantenimiento de la lógica existente.
⚡ Acción:
Rediseña predicciones.html para implementar un flujo interactivo en 3 pantallas o estados principales, inspirado en las imágenes adjuntas.

El flujo debe funcionar así:

Pantalla 1 — Vista general de grupos

El usuario accede primero a una pantalla con todos los grupos del Mundial.

Debe mostrar:

Banner superior con contador regresivo al primer partido.
Título: FIFA World Cup 2026 o Corona Mundial 2026.
Texto guía breve: “Elegí un grupo y armá tu predicción de cómo van a quedar las posiciones.”
Barra de progreso de grupos completados.
Grid responsive con 12 cards, una por grupo: A, B, C, D, E, F, G, H, I, J, K y L.
Cada card debe mostrar:
Letra del grupo.
Estado: “Pendiente” o “✓ Predicho”.
Lista de equipos ordenados del 1º al 4º.
Banderas o placeholders visuales.
Color distintivo por grupo.
Borde verde si el grupo ya fue predicho.
Hover interactivo.
Al hacer clic en una card de grupo, se debe navegar a la Pantalla 2.
Pantalla 2 — Predicción del grupo seleccionado

Cuando el usuario selecciona un grupo, debe acceder a una pantalla específica del grupo.

Debe mostrar:

Botón “← Volver”.
Encabezado con letra y nombre del grupo, por ejemplo: Grupo A.
Texto guía: “Tocá una celda y predecí el resultado de cada partido.”
Indicador de progreso: “6 / 6 partidos”.
Barra de progreso horizontal.
Opción destacada:
“Predicción manual — tocá cada celda”.
Botón “Predicción automática”.
Matriz visual de partidos tipo fixture:
Equipos en columnas y filas.
Diagonal bloqueada con patrón rayado.
Celdas editables para cada partido.
Celdas con resultados tipo 2 - 0, 1 - 1, 0 - 0.
Colores de celda según resultado:
Verde para victoria local o resultado favorable.
Rojo para derrota.
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

No usar:

React.
Vue.
Angular.
Tailwind.
Librerías pesadas.
Frameworks adicionales.
Referencias visuales adjuntas
Imagen 1 — Pantalla general de grupos

La primera referencia muestra una vista general con:

Banner superior azul/verde con contador.
Título centrado.
Barra de progreso.
Cards de grupos en grilla.
Cada card incluye posiciones 1º a 4º.
Etiqueta “✓ Predicho”.
Bordes verdes y fondos suaves.
Diseño limpio, interactivo y deportivo.

Esta debe ser la base visual de la Pantalla 1.

Imagen 2 — Pantalla de predicción de grupo

La segunda referencia muestra:

Botón volver.
Header del grupo.
Barra de progreso.
Selector de modo manual/automático.
Matriz de resultados.
Tabla resultante.
Leyenda de clasificación.
Botones de cancelar y guardar.

Esta debe ser la base visual de la Pantalla 2.

Imagen 3 — Predicción de partido

La tercera referencia muestra una card/modal compacto con:

Título.
Dos equipos enfrentados.
Banderas.
Controles − y +.
Marcador grande.
Botones Borrar, Cancelar y Guardar.

Esta debe ser la base visual de la Pantalla 3.

Objetivo UX

La experiencia debe sentirse como una app deportiva moderna, clara y entretenida.

El usuario debe entender rápidamente:

Qué grupos faltan predecir.
Qué grupos ya completó.
Cómo entrar a un grupo.
Cómo cargar resultados.
Cómo se calcula la tabla.
Cuándo puede guardar la predicción.
Reglas visuales

Usar un estilo:

Deportivo.
Limpio.
Premium.
Corporativo.
Mobile first.
Interactivo.
Similar a fixture mundialista.
Compatible con la identidad de Corona Mundial 2026.

Paleta recomendada:

:root {
  --corona-red: #C8102E;
  --corona-red-dark: #A0001C;
  --corona-gold: #D4AF37;
  --corona-blue: #0D6EFD;
  --corona-cyan: #2BBBD8;
  --corona-green: #20C997;
  --corona-light: #F5F7FA;
  --corona-white: #FFFFFF;
  --corona-black: #1F1F1F;
  --corona-gray: #495057;
}

Mantener coherencia con el design system previo del proyecto, pero permitir acentos azules, verdes y cyan para mejorar la experiencia tipo fixture.

📤 Salida:

Genera una implementación o instrucciones para Codex con esta estructura:

1. Diagnóstico de la pantalla actual

Analiza brevemente qué debe cambiar en predicciones.html para lograr una experiencia como las imágenes de referencia.

2. Nuevo flujo UX de predicciones

Definir claramente los 3 estados:

groupsView — Vista general de grupos.
groupPredictionView — Predicción del grupo seleccionado.
matchPredictionModal — Modal/card para cargar resultado de partido.
3. Estructura HTML recomendada

Crear o adaptar secciones como:

<section id="groupsView"></section>
<section id="groupPredictionView" class="d-none"></section>
<div id="matchPredictionModal" class="prediction-modal d-none"></div>

La navegación entre pantallas debe hacerse con clases como d-none, sin recargar la página.

4. Componentes UI obligatorios
Vista de grupos

Crear componentes:

.countdown-banner
.prediction-page-title
.groups-progress
.groups-grid
.group-card
.group-badge
.group-status
.team-position-row
Vista de grupo

Crear componentes:

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
Modal de partido

Crear componentes:

.match-prediction-overlay
.match-prediction-card
.match-team
.score-control
.score-button
.score-value
.modal-actions
5. Lógica JavaScript obligatoria

Implementar o adaptar funciones como:

initPredictionsPage()
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
goBackToGroups()
6. Datos de ejemplo para desarrollo

Si no hay backend disponible, usar datos mock temporales para los 12 grupos y sus equipos.

Los datos deben permitir renderizar:

Grupo A a L.
4 equipos por grupo.
6 partidos por grupo.
Estados predicho/pendiente.
Tabla resultante por grupo.

Cuando exista backend real, mantener separación para reemplazar mocks por datos de API sin romper la UI.

7. Reglas de cálculo de tabla

Al guardar resultados del grupo, recalcular:

PJ: partidos jugados.
G: ganados.
E: empatados.
P: perdidos.
GF: goles a favor.
GC: goles en contra.
DG: diferencia de gol.
PTS: puntos.

Puntaje de tabla:

Victoria: 3 puntos.
Empate: 1 punto.
Derrota: 0 puntos.

Ordenar tabla por:

Mayor PTS.
Mayor DG.
Mayor GF.
Orden alfabético si persiste empate.

Clasificación visual:

1º y 2º: clasifica.
3º: mejor 3º.
4º: eliminado.
8. Interacciones obligatorias

La interfaz debe permitir:

Clic en grupo para entrar.
Botón volver para regresar.
Clic en celda para predecir partido.
Incrementar y decrementar goles.
Guardar resultado de partido.
Borrar resultado.
Cancelar edición.
Recalcular tabla automáticamente.
Marcar grupo como completo cuando tenga 6/6 partidos.
Guardar predicción del grupo.
Mantener estado local mientras el usuario edita.
Mostrar feedback visual de éxito/error.
9. Responsive mobile first

Asegurar:

En desktop: grid de 4 columnas para grupos.
En tablet: grid de 2 columnas.
En mobile: grid de 1 columna.
La matriz debe tener scroll horizontal.
Los botones deben ser táctiles.
El modal debe ajustarse al ancho móvil.
La tabla resultante debe ser responsive.
10. Reglas de integración

Si existe lógica actual en predicciones.html, no eliminarla sin revisar.

Codex debe:

Inspeccionar archivos existentes.
Identificar endpoints o funciones actuales.
Mantener nombres importantes si son usados por backend.
No romper guardado existente.
Adaptar la UI al nuevo flujo.
Separar lógica visual de lógica de API.
Documentar cualquier supuesto.
11. Archivos a modificar o crear

Modificar principalmente:

frontend/predicciones.html
frontend/assets/css/styles.css
frontend/assets/js/predicciones.js

Si no existe predicciones.js, crearlo.

Opcionalmente actualizar:

frontend/assets/js/api.js
frontend/assets/js/config.js
docs/predicciones-flujo.md
12. Criterios de aceptación

La implementación estará lista cuando:

Al abrir predicciones.html, se muestre primero la vista de grupos.
Cada grupo se vea como una card interactiva con posiciones 1º a 4º.
Al hacer clic en un grupo, se muestre la matriz de predicción del grupo.
Al tocar una celda, se abra el modal/card de predicción de partido.
El usuario pueda sumar o restar goles.
Al guardar, la celda se actualice.
La tabla resultante se recalcule automáticamente.
El progreso del grupo cambie de 0/6 hasta 6/6.
Un grupo completo quede marcado como ✓ Predicho.
El diseño sea responsive.
No se rompa el guardado existente.
El diseño se parezca claramente a las imágenes de referencia, pero adaptado a la identidad Corona Mundial 2026.
Prompt maestro para usar directamente en Codex

Rediseña frontend/predicciones.html para convertirlo en una experiencia interactiva de predicciones de grupos del Mundial 2026, basada en las imágenes de referencia adjuntas.

La pantalla debe funcionar en 3 estados:

groupsView: vista general de los 12 grupos con cards interactivas, progreso y contador superior.
groupPredictionView: vista de un grupo seleccionado con matriz de partidos, tabla resultante, leyenda y botón guardar.
matchPredictionModal: card/modal para predecir el resultado de un partido con controles − y +.

En la primera pantalla, mostrar un banner superior con countdown, título centrado, barra de progreso y grid responsive de grupos A-L. Cada card debe mostrar 4 posiciones, equipos, banderas/placeholders, estado pendiente o ✓ Predicho, borde verde si está completo y hover interactivo.

Al hacer clic en un grupo, mostrar la segunda pantalla con botón volver, encabezado del grupo, progreso 0/6 a 6/6, barra de progreso, opción de predicción manual, botón de predicción automática, matriz de resultados 4x4 con diagonal bloqueada, celdas editables, colores para victoria/empate/derrota, tabla resultante y acciones cancelar/guardar.

Al hacer clic en una celda editable, abrir una tercera pantalla/modal compacta con título Predecí el resultado, equipo A, equipo B, banderas, marcador grande, botones − y +, y acciones Borrar, Cancelar, Guardar.

Al guardar una predicción de partido, actualizar la celda de la matriz, recalcular PJ, G, E, P, GF, GC, DG y PTS en la tabla, actualizar progreso del grupo y marcar el grupo como completo cuando tenga 6/6 partidos.

Usar HTML5, Bootstrap 5, CSS personalizado y JavaScript Vanilla. No usar React, Vue, Angular, Tailwind ni librerías pesadas. Mantener mobile first, scroll horizontal para matriz y tablas responsive.

Si existe lógica previa de backend o endpoints, no romperla. Inspecciona primero predicciones.html, api.js, config.js y cualquier archivo relacionado. Mantén separación entre UI, estado local y llamadas API. Si no hay backend disponible, usar mocks temporales claramente separados y fáciles de reemplazar.

Crear o actualizar frontend/predicciones.html, frontend/assets/css/styles.css y frontend/assets/js/predicciones.js.

Aplicar una estética deportiva moderna como las imágenes: cards claras, bordes verdes para completado, banner superior azul/verde, grupo con badge de color, matriz limpia, celdas con colores suaves, tabla resultante tipo dashboard y modal compacto. Adaptar todo a la identidad de Corona Mundial 2026.

Al finalizar, reportar archivos modificados, componentes creados, funciones JavaScript implementadas, supuestos, pruebas realizadas y pendientes reales.