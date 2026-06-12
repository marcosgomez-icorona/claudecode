# Flujo UX de predicciones

## Diagnostico

La pantalla anterior permitia cargar una prediccion por partido mediante formulario lineal. Funcionaba, pero no mostraba el contexto de grupos, progreso ni tabla proyectada.

## Nuevo flujo

La pantalla `predicciones.html` ahora trabaja en tres estados:

- `groupsView`: vista general de grupos A-L.
- `groupPredictionView`: matriz de prediccion para un grupo.
- `matchPredictionModal`: modal compacto para cargar marcador.

## Componentes

- `countdown-banner`
- `groups-grid`
- `group-card`
- `group-badge`
- `group-status`
- `fixture-matrix`
- `match-cell`
- `resulting-table-card`
- `classification-legend`
- `match-prediction-overlay`

## Calculo de tabla

Cada resultado cargado recalcula:

- PJ
- G
- E
- P
- GF
- GC
- DG
- PTS

Orden:

1. Mayor PTS.
2. Mayor DG.
3. Mayor GF.
4. Orden alfabetico.

## Integracion

La UI mantiene estado local mientras el usuario edita. Antes de mostrar grupos valida el email con `CoronaApi.getParticipantByEmail`. Al guardar un grupo completo usa el participante activo y llama a `CoronaApi.savePrediction` para cada uno de los 6 partidos.

Si no hay backend o si el backend no entrega grupos A-L completos, usa datos mock separados para mantener la experiencia visual operativa. Si el backend entrega partidos sin letra de grupo pero con equipos reales, el frontend intenta mapear cada cruce por nombre de equipo para conservar `partido_id` real.

## Supuestos

- El guardado real sigue siendo por partido, usando el endpoint existente.
- Las predicciones previas se leen por email cuando el backend tiene `getPredictionsByParticipant`.
- El email se valida antes de habilitar el flujo para no generar predicciones anonimas.
