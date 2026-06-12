# Seguimiento Inteligente de Sumas y Saldos

Proyecto para construir un asistente administrativo-contable que controle periodicamente el Sumas y Saldos exportado desde Calipso, detecte variaciones relevantes, saldos criticos, cuentas sin movimiento, diferencias entre snapshots y posibles inconsistencias para Administracion y Gerencia.

## Estado inicial

- Carpeta creada como espacio propio del proyecto.
- MVP recomendado: carga manual de Excel/CSV exportado desde Calipso.
- Integracion MCP Calipso: diferida hasta validar reglas contables y operar primero contra TEST/copia readonly.

## Principios

- No escribir en Calipso.
- No generar SQL libre contra ERP.
- Mantener compatibilidad SQL Server 2008 R2.
- Trazabilidad por snapshot, archivo fuente, usuario, fecha y UUID.
- Validacion funcional con Administracion antes de automatizar alertas.
- Separacion test/prod obligatoria.

## Documentos

- `PROJECT_CONTEXT.md`: alcance, arquitectura y decisiones iniciales.
- `FASE_0_RELEVAMIENTO.md`: plan operativo de la primera etapa.
- `LAYOUT_SUMAS_SALDOS.md`: layout esperado y pendientes de validacion.
- `ALERT_RULES.md`: reglas candidatas de alertas.
- `DATA_MODEL.md`: modelo de datos inicial.
- `DECISION_LOG.md`: decisiones tecnicas y funcionales.
- `MCP_CALIPSO_ANALISIS.md`: analisis readonly de vistas Calipso y consultas candidatas.
- `SNAPSHOT_MVP_SPEC.md`: definicion del primer snapshot MCP a confirmar.
- `database/schema_mysql.sql`: schema MySQL intermedio del MVP.
- `BACKLOG.md`: epicas, historias, tareas y criterios de aceptacion.
- `QUESTIONS.md`: preguntas necesarias para cerrar el MVP.
- `AGENTS.md`: reglas especificas para Codex dentro de este proyecto.
