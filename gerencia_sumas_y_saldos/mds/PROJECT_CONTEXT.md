# PROJECT_CONTEXT - Seguimiento Inteligente de Sumas y Saldos

## 1. Objetivo

Implementar un sistema incremental para analizar el Sumas y Saldos de Calipso, comparar cortes historicos y generar alertas administrativas-contables accionables para Administracion, Sistemas y Gerencia.

Resultado esperado del MVP: cargar un Excel/CSV exportado desde Calipso, normalizar cuentas y saldos, guardar un snapshot historico, comparar contra el snapshot anterior y emitir un informe de variaciones y alertas.

## 2. Supuestos y contexto

- El primer insumo sera un archivo Excel/CSV exportado manualmente desde Calipso.
- El layout definitivo del reporte todavia debe validarse.
- La base del ERP es SQL Server 2008 R2, pero el MVP no consultara Calipso en forma directa.
- Las reglas contables deben validarse con Administracion antes de convertirse en alertas automaticas.
- La integracion futura con MCP Calipso debe ser readonly, controlada y contra TEST o copia restaurada.
- Avance 2026-06-08: se uso MCP Calipso readonly contra base `CORONA` para validar vistas contables. La fuente candidata para snapshot por cuenta es `V_TRCONTABLE_ + V_ITEMCONTABLE_ + V_VALOR_ + V_EZI_CUENTAS`, con rangos de fecha acotados. Ver `MCP_CALIPSO_ANALISIS.md`.

## 3. Diseno propuesto

Enfoque por fases:

1. MVP manual: importacion Excel/CSV, validacion basica, snapshot, comparacion y reporte.
2. Base historica: persistencia de snapshots, auditoria, estados de procesamiento y re-procesamiento controlado.
3. Dashboard operativo: filtros por cuenta, rubro, fecha, criticidad y variacion.
4. Informes automaticos: resumen diario/semanal para Gerencia y Administracion.
5. MCP Calipso readonly: extraccion controlada desde vistas/consultas permitidas.
6. Piloto operativo: validacion con usuarios clave, ajuste de umbrales y reglas.

## 4. Arquitectura tecnica

### MVP recomendado

- Frontend: PHP + Bootstrap o interfaz simple local existente.
- Backend: PHP o Node.js, segun convenga por el entorno donde vaya a correr.
- Base intermedia: MySQL local o PostgreSQL. Si se prioriza compatibilidad actual, MySQL.
- Entrada: Excel/CSV exportado desde Calipso.
- Salida: reporte HTML/PDF/CSV y resumen ejecutivo.

### Componentes

- Importador de archivos.
- Normalizador de columnas.
- Validador de estructura.
- Motor de snapshots.
- Comparador entre cortes.
- Motor de alertas.
- Generador de informes.
- Auditoria de procesamiento.

## 5. Flujo paso a paso

1. Usuario exporta Sumas y Saldos desde Calipso.
2. Usuario carga el archivo al sistema.
3. Sistema valida columnas minimas y formato numerico.
4. Sistema crea un snapshot con UUID, fecha contable, fecha de carga y usuario.
5. Sistema normaliza cuenta, descripcion, debe, haber, saldo deudor, saldo acreedor y rubro si existe.
6. Sistema compara contra el snapshot anterior equivalente.
7. Sistema calcula variaciones absolutas y porcentuales.
8. Sistema aplica reglas de alerta.
9. Sistema genera informe ejecutivo y detalle operativo.
10. Administracion revisa falsos positivos y ajusta reglas.

## 6. Riesgos y controles

- Riesgo: layout variable del Excel/CSV. Control: mapeo configurable y validacion previa.
- Riesgo: falsos positivos contables. Control: reglas en modo observacion durante piloto.
- Riesgo: lectura directa prematura de Calipso. Control: MVP por archivo y MCP readonly solo en fase posterior.
- Riesgo: datos sensibles. Control: no subir archivos reales a servicios externos sin aprobacion.
- Riesgo: cierres contables parciales. Control: identificar fecha de corte, ejercicio, periodo y estado del cierre.

## 7. Proxima version

Definir layout real del archivo exportado, reglas iniciales de alerta y base intermedia. Luego crear el parser y una carga de prueba con 2 o 3 snapshots anonimizados.

Actualizacion: si el usuario confirma que el MVP avanzara directo con MCP readonly, reemplazar el parser Excel/CSV por un extractor readonly parametrizado por periodo y guardar snapshots en base intermedia.

## 8. Entregables concretos

- Documento de alcance MVP.
- Layout esperado del archivo.
- Modelo de datos intermedio.
- Parser Excel/CSV.
- Motor de comparacion de snapshots.
- Catalogo inicial de alertas.
- Reporte ejecutivo inicial.
- Guia operativa minima.
