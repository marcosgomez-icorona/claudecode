# Modelo de datos Google Sheets

Todas las hojas usan encabezados en la primera fila. Las fechas se guardan en formato ISO 8601.

## Participantes

| Columna | Tipo | Ejemplo | Observaciones |
| --- | --- | --- | --- |
| participante_id | texto | par_20260529103000_ab12 | ID unico |
| nombre_apellido | texto | Ana Perez | Obligatorio |
| email | texto | ana@ingeniolacorona.com | Unico |
| area | texto | Sistemas | Lista controlada |
| telefono | texto | 3815551234 | Opcional |
| fecha_registro | fecha ISO | 2026-05-29T10:30:00.000Z | Automatico |
| estado | texto | activo | activo/inactivo |

## Partidos

| Columna | Tipo | Ejemplo | Observaciones |
| --- | --- | --- | --- |
| partido_id | texto | mat_20260529103000_cd34 | ID unico |
| fase | texto | Grupo | Obligatorio |
| equipo_a | texto | Argentina | Obligatorio |
| equipo_b | texto | Brasil | Obligatorio |
| fecha_partido | fecha ISO | 2026-06-11T21:00:00.000Z | Obligatorio |
| fecha_limite_prediccion | fecha ISO | 2026-06-11T20:00:00.000Z | Bloqueo |
| estado | texto | programado | programado/finalizado |

## Predicciones

| Columna | Tipo | Ejemplo | Observaciones |
| --- | --- | --- | --- |
| prediccion_id | texto | pre_20260529103000_ef56 | ID unico |
| participante_id | texto | par_... | Relacion |
| partido_id | texto | mat_... | Relacion |
| email | texto | ana@ingeniolacorona.com | Busqueda rapida |
| ganador_predicho | texto | Argentina | Argentina/Brasil/Empate |
| goles_a_predicho | numero | 2 | Entero >= 0 |
| goles_b_predicho | numero | 1 | Entero >= 0 |
| es_empate_predicho | booleano | false | Derivado |
| fecha_prediccion | fecha ISO | 2026-05-29T10:30:00.000Z | Alta |
| fecha_actualizacion | fecha ISO | 2026-05-29T10:40:00.000Z | Modificacion |
| estado | texto | vigente | vigente/anulada |

## Resultados

| Columna | Tipo | Ejemplo | Observaciones |
| --- | --- | --- | --- |
| resultado_id | texto | res_20260529103000_gh78 | ID unico |
| partido_id | texto | mat_... | Relacion |
| goles_a_real | numero | 2 | Entero >= 0 |
| goles_b_real | numero | 1 | Entero >= 0 |
| ganador_real | texto | Argentina | Equipo o Empate |
| es_empate_real | booleano | false | Derivado |
| fecha_carga | fecha ISO | 2026-05-29T11:00:00.000Z | Automatico |
| cargado_por | texto | Admin | Simple |

## Puntajes

| Columna | Tipo | Ejemplo | Observaciones |
| --- | --- | --- | --- |
| puntaje_id | texto | sco_20260529103000_ij90 | ID unico |
| participante_id | texto | par_... | Relacion |
| partido_id | texto | mat_... | Relacion |
| puntos_resultado | numero | 5 | 0/2/5 |
| puntos_extra | numero | 0 | Reservado campeon/subcampeon |
| puntos_total | numero | 5 | Suma |
| fecha_calculo | fecha ISO | 2026-05-29T11:05:00.000Z | Automatico |

## Ranking Individual

| Columna | Tipo | Ejemplo | Observaciones |
| --- | --- | --- | --- |
| posicion | numero | 1 | Ranking |
| participante_id | texto | par_... | Relacion |
| nombre_apellido | texto | Ana Perez | Denormalizado |
| area | texto | Sistemas | Denormalizado |
| puntaje_total | numero | 25 | Total |
| resultados_exactos | numero | 3 | Desempate |
| ganadores_correctos | numero | 5 | Desempate |
| fecha_actualizacion | fecha ISO | 2026-05-29T11:05:00.000Z | Automatico |

## Ranking Areas

| Columna | Tipo | Ejemplo | Observaciones |
| --- | --- | --- | --- |
| posicion | numero | 1 | Ranking |
| area | texto | Sistemas | Area |
| cantidad_participantes | numero | 8 | Registrados activos |
| puntaje_promedio_top5 | numero | 18.4 | Promedio mejores 5 |
| puntaje_total_top5 | numero | 92 | Suma mejores 5 |
| fecha_actualizacion | fecha ISO | 2026-05-29T11:05:00.000Z | Automatico |

## Configuracion

| Columna | Tipo | Ejemplo | Observaciones |
| --- | --- | --- | --- |
| clave | texto | admin_clave | Unica |
| valor | texto | cambiar-esta-clave | Configurable |
| descripcion | texto | Clave simple para admin | Operativa |
| fecha_actualizacion | fecha ISO | 2026-05-29T11:05:00.000Z | Automatico |

## Equipos

| Columna | Tipo | Ejemplo | Observaciones |
| --- | --- | --- | --- |
| codigo | texto | ARG | Codigo del equipo |
| nombre_api | texto | Argentina | Nombre recibido |
| nombre_es | texto | Argentina | Nombre guardado en espanol |
| grupo | texto | A | Grupo si aplica |
| fecha_actualizacion | fecha ISO | 2026-06-11T20:00:00.000Z | Ultima sync |

## Sincronizaciones

| Columna | Tipo | Ejemplo | Observaciones |
| --- | --- | --- | --- |
| sync_id | texto | syn_20260611200000_ab12 | ID unico |
| proveedor | texto | api-propia-corona | Origen configurado |
| estado | texto | ok | ok/error/omitido |
| partidos_actualizados | numero | 104 | Cantidad |
| resultados_actualizados | numero | 1 | Cantidad |
| mensaje | texto | Equipos: 48 | Detalle |
| fecha_sync | fecha ISO | 2026-06-11T20:00:00.000Z | Fecha ejecucion |
