# Analisis tecnico - Agente de despachos de azucar y alcohol

## 1. Objetivo

Convertir el prompt funcional existente en `info-md/desarrollo_agente_fase1.md` en una base tecnica ejecutable para desarrollar un agente de despachos de azucar y alcohol del Ingenio La Corona.

El agente debe analizar pedidos, stock, estado administrativo/financiero, transporte, facturacion, documentacion y calendario operativo para generar una propuesta de despacho diaria o semanal, siempre como borrador sujeto a aprobacion humana.

## 2. Supuestos y contexto

- El documento actual fue trabajado en Gemini, Gem y Apps Script.
- El error aparece al invocar el modelo de Gemini, no necesariamente en la logica funcional.
- La fuente principal prevista es `DESPACHOS_CORONA_TEST_SPARK.xlsx`, ubicada conceptualmente en Drive.
- La solucion no debe modificar archivos fuente ni confirmar entregas automaticamente.
- La primera version debe funcionar como MVP auditable antes de conectar acciones reales.
- El alcance debe cubrir azucar y alcohol, aunque el prompt actual esta escrito principalmente para azucar.

## 3. Diagnostico del markdown actual

El archivo actual es un buen prompt operativo, pero todavia no es una especificacion tecnica completa.

Fortalezas:

- Define rol, limites, fuentes permitidas y reglas de seguridad.
- Establece clasificacion operativa de pedidos.
- Obliga a declarar datos faltantes y conflictos entre fuentes.
- Define una estructura de salida markdown muy clara.
- Incorpora paginacion por lotes cuando hay mas de 10 pedidos.

Brechas para desarrollo:

- No define esquema de datos de entrada.
- No separa reglas deterministicas de redaccion con IA.
- No define IDs unicos de trazabilidad por pedido/propuesta.
- No define formato de salida estructurado para guardar en Sheets/Drive.
- No incluye manejo de errores de Gemini/API.
- No contempla todavia alcohol como flujo diferenciado.
- No define ambientes test/prod.
- No define logs, auditoria, aprobaciones ni estados de workflow.

## 4. Diseno propuesto

Recomiendo no hacer que Gemini decida todo directamente desde texto libre. La arquitectura debe separar:

1. Ingesta de datos.
2. Normalizacion.
3. Validaciones deterministicas.
4. Clasificacion operativa.
5. Generacion de propuesta.
6. Redaccion asistida por modelo.
7. Aprobacion humana.
8. Registro y auditoria.

La IA debe usarse principalmente para:

- Explicar decisiones.
- Redactar resumen ejecutivo.
- Detectar inconsistencias semanticas.
- Generar borradores internos.
- Ordenar acciones por responsable.

Las reglas criticas deben ejecutarse con codigo:

- Stock suficiente.
- Cliente bloqueado.
- Deuda vencida.
- Documentacion incompleta.
- Transporte disponible.
- Capacidad diaria excedida.
- Conflictos de calendario.
- Facturacion pendiente.
- Pedido sin fecha o sin prioridad.

## 5. Arquitectura tecnica

### Opcion simple - MVP Apps Script

Componentes:

- Google Sheets como fuente normalizada.
- Apps Script para lectura, validacion y generacion de salida.
- Gemini/OpenAI como servicio de redaccion.
- Drive para guardar reportes markdown/html/pdf.
- Gmail/Calendar solo lectura en fase piloto.

Ventajas:

- Rapida de implementar.
- Aprovecha Drive y Sheets.
- Menor infraestructura.

Riesgos:

- Apps Script tiene limites de tiempo, cuotas y errores menos observables.
- Dependencia fuerte del modelo y permisos Google.
- Dificil escalar si se agregan muchas fuentes.

### Opcion intermedia - Apps Script + API interna

Componentes:

- Apps Script solo como interfaz Google.
- API interna en Python/FastAPI o Node.js.
- Motor de reglas en backend.
- Conector LLM intercambiable.
- Logs en archivo/SQLite/PostgreSQL.
- Outputs en Drive/Sheets.

Ventajas:

- Mejor trazabilidad.
- Permite cambiar Gemini por OpenAI u otro modelo.
- Facilita pruebas y versionado.

Riesgos:

- Requiere desplegar y mantener un servicio.

### Opcion robusta - n8n + backend + conectores

Componentes:

- Node-RED/on-prem para datos locales si aparecen fuentes OT/ERP.
- n8n cloud para orquestacion.
- Backend de reglas.
- Google Drive/Gmail/Calendar como conectores.
- Cola de aprobaciones humanas.
- Dashboard operativo.

Ventajas:

- Escalable hacia agentes.
- Auditable.
- Compatible con flujos productivos.

Riesgos:

- Mayor tiempo inicial.

## 6. Modelo de datos minimo

### Pedido

- `pedido_id`
- `cliente_id`
- `cliente_nombre`
- `producto_tipo` = `azucar` | `alcohol`
- `producto_descripcion`
- `cantidad`
- `unidad`
- `fecha_solicitada`
- `prioridad_comercial`
- `estado_pedido`
- `observaciones_comerciales`

### Stock

- `producto_tipo`
- `producto_descripcion`
- `stock_disponible`
- `unidad`
- `ubicacion`
- `fecha_actualizacion`

### Cliente / administracion

- `cliente_id`
- `estado_admin`
- `cuenta_corriente_estado`
- `deuda_vencida`
- `bloqueo_admin`
- `autorizacion_comercial`
- `documentacion_estado`

### Facturacion

- `pedido_id`
- `factura_estado`
- `puede_facturarse`
- `motivo_pendiente`

### Transporte

- `transporte_id`
- `tipo_producto_habilitado`
- `capacidad`
- `unidad`
- `fecha_disponible`
- `estado`
- `chofer_o_proveedor`

### Capacidad operativa

- `fecha`
- `producto_tipo`
- `capacidad_maxima`
- `capacidad_programada`
- `unidad`
- `restricciones`

### Resultado

- `run_uuid`
- `pedido_id`
- `clasificacion`
- `motivos`
- `riesgos`
- `accion_recomendada`
- `responsable_sugerido`
- `requiere_aprobacion`
- `fecha_recomendada`
- `fuentes_usadas`

## 7. Flujo paso a paso

1. Leer fuentes desde Drive/Sheets.
2. Validar columnas obligatorias.
3. Normalizar productos: azucar y alcohol.
4. Generar `run_uuid`.
5. Cruzar pedidos con stock.
6. Cruzar pedidos con estado administrativo y cobranza.
7. Cruzar pedidos con facturacion/documentacion.
8. Cruzar pedidos con transporte.
9. Cruzar pedidos con capacidad diaria y calendario operativo.
10. Clasificar cada pedido.
11. Generar propuesta de calendario.
12. Generar resumen ejecutivo y alertas.
13. Guardar salida como borrador en Outputs.
14. Solicitar aprobacion humana.

## 8. Reglas de clasificacion recomendadas

### Apto para programar

Condiciones minimas:

- Stock suficiente.
- Cliente sin bloqueo administrativo.
- Cuenta habilitada o autorizacion comercial explicita.
- Sin deuda vencida bloqueante.
- Transporte disponible.
- Capacidad diaria disponible.
- Documentacion minima completa.
- Facturacion lista o emitible.
- Sin conflicto de calendario.

### Bloqueado

Casos tipicos:

- Cliente bloqueado.
- Deuda vencida bloqueante.
- Stock insuficiente.
- Transporte no disponible.
- Documentacion critica faltante.
- Facturacion no emitible por causa administrativa.

### Pendiente de validacion

Casos tipicos:

- Falta dato.
- Fuente no disponible.
- Fecha solicitada ausente.
- Producto/cantidad no normalizados.
- Estado financiero sin actualizar.

### Requiere aprobacion humana

Casos tipicos:

- Cliente con deuda pero prioridad comercial alta.
- Exceso de capacidad diaria.
- Cambio de fecha sugerida.
- Conflicto entre fuentes.
- Entrega de alcohol con requisito documental/sanitario/fiscal especial.

## 9. Riesgos y controles

| Riesgo | Impacto | Control |
| --- | --- | --- |
| Error al llamar Gemini | Alto | Crear capa `llm_client` con reintentos, logs y fallback |
| IA inventa datos | Alto | Pasar solo datos estructurados y exigir salida con referencias |
| Apps Script excede tiempo | Medio | Procesar por lotes y cachear lecturas |
| Cambios en columnas del Excel | Alto | Validacion de esquema antes de procesar |
| Confusion azucar/alcohol | Alto | Campo obligatorio `producto_tipo` y reglas separadas |
| Accion automatica no autorizada | Alto | Modo solo lectura + aprobacion humana |
| Falta de trazabilidad | Alto | `run_uuid`, timestamp, fuentes y hash de entrada |

## 10. Manejo recomendado del error con Gemini

Puntos a revisar en Apps Script:

- Nombre exacto del modelo. Usar un modelo vigente y disponible para la API configurada.
- API key cargada en `PropertiesService`, no escrita fija en codigo.
- Endpoint correcto de Generative Language API.
- Payload compatible con el modelo.
- Permisos y servicios avanzados habilitados.
- Facturacion/cuotas del proyecto Google.
- Logs completos de status HTTP y cuerpo de error.

Patron recomendado:

```javascript
function callLlm(prompt, context) {
  const provider = 'gemini';
  const runId = Utilities.getUuid();

  try {
    return callGeminiProvider(prompt, context, runId);
  } catch (err) {
    logLlmError(runId, provider, err);
    throw new Error('No se pudo generar la redaccion con IA. La clasificacion deterministica queda disponible para revision manual.');
  }
}
```

El desarrollo no debe depender de que Gemini funcione para clasificar pedidos. Si falla el modelo, igual debe quedar una salida tecnica con clasificacion, motivos y datos faltantes.

## 11. Proxima version

Version 0.1:

- Normalizar planillas.
- Crear motor de reglas deterministico.
- Generar salida markdown sin IA.
- Agregar `run_uuid` y logs.

Version 0.2:

- Integrar Gemini/OpenAI solo para redaccion.
- Agregar manejo de errores y fallback.
- Guardar outputs en Drive.

Version 0.3:

- Conectar Gmail/Calendar en modo solo lectura.
- Agregar aprobacion humana.

Version 0.4:

- Dashboard operativo.
- Separacion test/prod.
- Flujo n8n para aprobaciones y notificaciones internas.

## 12. Entregables concretos

Para avanzar con desarrollo real, los proximos archivos recomendados son:

- `README.md`: objetivo, instalacion y uso.
- `config/schema.json`: columnas esperadas por cada hoja.
- `src/rules/dispatchRules.js`: motor de reglas.
- `src/llm/llmClient.js`: capa de modelo intercambiable.
- `src/output/markdownReport.js`: generador de salida.
- `src/audit/logger.js`: logs y trazabilidad.
- `tests/dispatchRules.test.js`: pruebas de clasificacion.

Si se continua en Apps Script:

- `apps-script/Code.gs`
- `apps-script/Config.gs`
- `apps-script/Rules.gs`
- `apps-script/GeminiClient.gs`
- `apps-script/ReportBuilder.gs`
- `apps-script/appsscript.json`

