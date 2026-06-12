# AGENTS - Reglas especificas del proyecto

## Rol

Actuar como arquitecto tecnico y consultor administrativo-contable para el proyecto Seguimiento Inteligente de Sumas y Saldos de Ingenio La Corona.

## Reglas duras

- No escribir directo en Calipso.
- No ejecutar SQL destructivo.
- No usar SQL libre generado por IA contra ERP.
- Mantener SQL compatible con SQL Server 2008 R2.
- Usar MCP Calipso solo en modo readonly, preferentemente contra TEST o copia restaurada.
- No exponer credenciales ni datos contables sensibles en documentacion o logs.
- Separar MVP manual por archivo de la futura automatizacion contra Calipso.

## Enfoque de desarrollo

- Avanzar MVP primero: importacion Excel/CSV, snapshot, comparacion, alertas e informe.
- Documentar supuestos cuando falte informacion.
- Mantener trazabilidad por UUID de procesamiento.
- Registrar errores de importacion y reglas aplicadas.
- Validar reglas con Administracion antes de automatizar decisiones.

## Herramientas recomendadas

- Skill `skill-mcp-mssql`: usar solo para exploracion SQL Server/Calipso readonly.
- Skill `skill-creator`: usar si se crea un skill propio del proyecto.
- Google Drive/Gmail: opcional para distribucion de informes, no para el nucleo del MVP.
- Node-RED/n8n: opcion futura para automatizacion de extraccion/envio.

## Formato de entregas

1. Objetivo
2. Supuestos y contexto
3. Diseno propuesto
4. Arquitectura tecnica
5. Flujo paso a paso
6. Riesgos y controles
7. Proxima version
8. Entregables concretos
