# BACKLOG - Seguimiento Inteligente de Sumas y Saldos

## Epica 1 - Relevamiento funcional

### Historia 1.1
Como Administracion, quiero definir que version del Sumas y Saldos se usara como fuente para que el sistema compare siempre la misma estructura.

Criterios de aceptacion:
- Existe un archivo ejemplo anonimizado.
- Estan identificadas las columnas obligatorias.
- Esta definida la periodicidad de carga.

### Historia 1.2
Como Gerencia, quiero recibir un resumen de variaciones relevantes para detectar desfasajes sin revisar todo el listado.

Criterios de aceptacion:
- El resumen muestra top variaciones.
- Cada alerta tiene motivo y severidad.
- El informe permite ir del resumen al detalle.

## Epica 2 - MVP de importacion

### Historia 2.1
Como usuario administrativo, quiero cargar un Excel/CSV del Sumas y Saldos para generar un snapshot historico.

Criterios de aceptacion:
- El sistema rechaza archivos sin columnas minimas.
- El sistema registra usuario, fecha de carga, fecha contable y hash del archivo.
- El snapshot queda disponible para comparacion.

### Historia 2.2
Como Sistemas, quiero que los errores de importacion queden logueados para diagnosticar problemas de formato.

Criterios de aceptacion:
- Cada procesamiento tiene UUID.
- Errores y advertencias quedan registrados.
- El usuario recibe un mensaje claro sin exponer stack traces.

## Epica 3 - Comparacion y alertas

### Historia 3.1
Como Administracion, quiero comparar el snapshot actual contra el anterior para ver altas, bajas y variaciones de saldos.

Criterios de aceptacion:
- Detecta cuentas nuevas.
- Detecta cuentas ausentes respecto del corte anterior.
- Calcula variacion absoluta y porcentual.

### Historia 3.2
Como Gerencia, quiero clasificar alertas por severidad para priorizar revision.

Criterios de aceptacion:
- Existen niveles INFO, MEDIA, ALTA y CRITICA.
- Cada alerta indica regla aplicada.
- Los umbrales son configurables.

## Epica 4 - Dashboard e informes

### Historia 4.1
Como usuario administrativo, quiero filtrar por cuenta, rubro, severidad y fecha para revisar rapidamente los casos.

Criterios de aceptacion:
- Hay filtros por snapshot y cuenta.
- Hay vista de resumen y detalle.
- Puede exportarse el resultado.

### Historia 4.2
Como Gerencia, quiero un informe ejecutivo periodico con las principales variaciones.

Criterios de aceptacion:
- Informe con resumen, top alertas y anexos.
- Exportable a PDF o enviado por correo en fase posterior.
- Incluye fecha de corte y fuente de datos.

## Epica 5 - Integracion MCP Calipso readonly

### Historia 5.1
Como Sistemas, quiero consultar Calipso en modo readonly desde TEST/copia para reemplazar la exportacion manual cuando el MVP este validado.

Criterios de aceptacion:
- Se usa usuario readonly.
- Solo se ejecutan SELECT controlados.
- No hay SQL libre generado por IA.
- Las consultas son compatibles con SQL Server 2008 R2.

## Orden recomendado

1. Conseguir archivo real anonimizado.
2. Cerrar layout minimo.
3. Definir base intermedia.
4. Implementar importador.
5. Implementar snapshots.
6. Implementar comparador.
7. Implementar reglas iniciales.
8. Crear reporte basico.
9. Pilotear con Administracion.
10. Evaluar MCP readonly.

## Sprint 0 - Primera etapa

Objetivo: dejar cerrado el relevamiento minimo para comenzar desarrollo.

Tareas:
- Obtener archivo ejemplo anonimizado.
- Validar columnas reales contra `LAYOUT_SUMAS_SALDOS.md`.
- Confirmar stack MVP en `DECISION_LOG.md`.
- Definir umbrales iniciales en `ALERT_RULES.md`.
- Confirmar base intermedia y modelo de tablas en `DATA_MODEL.md`.
- Preparar primer set de datos de prueba.

Criterios de aceptacion:
- Hay al menos un archivo ejemplo utilizable.
- El layout esta aprobado por Sistemas y Administracion.
- Existe decision de stack.
- Las reglas iniciales estan marcadas como candidatas o aprobadas.
- El desarrollo del importador puede comenzar sin depender de Calipso directo.
