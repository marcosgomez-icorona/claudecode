# Decision Log

## D-001 - Fuente inicial por archivo

Decision: comenzar con exportacion manual Excel/CSV desde Calipso.

Motivo: reduce riesgo, evita dependencia temprana del ERP y permite validar reglas con Administracion.

Estado: reemplazada por D-005 para MVP MCP.

## D-002 - No usar MCP en MVP inicial

Decision: diferir MCP Calipso readonly hasta validar layout, snapshots y alertas.

Motivo: el mayor riesgo inicial es funcional, no tecnico. La conexion directa puede automatizar errores si se usa antes de validar.

Estado: reemplazada por D-005 para MVP MCP.

## D-003 - Base intermedia

Decision: usar base intermedia fuera de Calipso.

Motivo: preservar ERP, auditar cargas y permitir reprocesamiento.

Estado: confirmada. MySQL para MVP.

## D-007 - Alcance del primer snapshot MVP

Decision propuesta: el primer snapshot sera movimiento mensual del periodo, consolidado por cuenta/rubro, sin saldo inicial ni saldo final acumulado.

Motivo: la consulta validada con MCP responde bien por periodo mensual y permite construir rapido comparacion entre snapshots.

Estado: pendiente confirmar.

## D-009 - Schema MySQL MVP

Decision: crear schema MySQL en `database/schema_mysql.sql` con tablas para snapshots, filas, comparaciones, alertas y logs.

Motivo: permite avanzar con extractor MCP readonly sin tocar Calipso y deja trazabilidad completa.

Estado: confirmada.

## D-008 - Unidad operativa en version 1

Decision propuesta: comenzar consolidado sin separar unidad operativa.

Motivo: reduce complejidad inicial; la separacion por unidad puede agregarse luego usando `V_TRCONTABLE_.UNIDADOPERATIVA_ID`.

Estado: pendiente confirmar.

## D-004 - Stack MVP

Decision recomendada: PHP + Bootstrap + MySQL.

Motivo: stack simple, local, compatible con herramientas internas existentes y facil de mantener por Sistemas.

Estado: pendiente confirmar.

## D-005 - Fuente MCP para snapshot contable

Decision propuesta: usar vistas base `V_TRCONTABLE_`, `V_ITEMCONTABLE_`, `V_VALOR_` y `V_EZI_CUENTAS` para generar snapshots por cuenta y periodo.

Motivo: la consulta agregada por cuenta respondio correctamente para junio 2026; las vistas prearmadas `V_EZI_CR_MAYORCUENTAS2` y conteos globales de `V_EZI_ORDENES_COMPRA2` produjeron timeout.

Estado: propuesta.

## D-006 - Estrategia de performance

Decision propuesta: no consultar rangos historicos largos en una sola ejecucion; procesar por periodo o por batch mensual.

Motivo: la consulta detallada desde `20230501` produjo timeout, mientras que el rango mensual acotado respondio.

Estado: propuesta.
