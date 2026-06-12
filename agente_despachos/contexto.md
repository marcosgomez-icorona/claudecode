# Contexto del proyecto - Agente de Despachos Corona

## 1. Objetivo

Desarrollar un agente operativo auditable para asistir la planificacion de despachos del Ingenio La Corona, comenzando por azucar como MVP 0.1.

El agente debe leer fuentes autorizadas, normalizar datos, aplicar reglas deterministicas y generar propuestas de despacho como borradores para revision humana. No debe confirmar despachos, modificar fuentes, escribir en Calipso ni ejecutar comunicaciones externas sin aprobacion explicita.

El objetivo practico es reducir errores y tiempos de coordinacion entre Comercial, Administracion, Cobranzas, Facturacion, Logistica, Deposito/Despacho y Sistemas, manteniendo trazabilidad completa de cada ejecucion.

## 2. Supuestos y contexto

- Empresa: Ingenio La Corona.
- Proyecto: `agente-despachos-corona`.
- Alcance inicial: despachos de azucar.
- Alcohol queda contemplado en el modelo de datos, pero fuera del alcance funcional del MVP 0.1 hasta validar fuentes especificas.
- Las fuentes iniciales se encuentran en Google Drive/Sheets o archivos Excel compartidos.
- Las planillas fuente son datos de entrada, no deben ser modificadas por el agente.
- Calipso se considera fuente futura de consulta solo lectura mediante MCP controlado.
- Toda propuesta generada es un borrador operativo hasta aprobacion humana.
- Si faltan datos o hay conflictos entre fuentes, el pedido debe clasificarse como `PENDIENTE_VALIDACION` o `REQUIERE_APROBACION_HUMANA`, nunca como aprobado automaticamente.

Fuentes iniciales detectadas:

- `Oscar Despachos2026 05.06.2026.xlsx`
- `COBRANZA SEMANAL ZAFRA 26.xlsx`
- `ARCHIVO GESTION AZUCAR INDUSTRIA ZAFRA 2026 29.05.26.xlsx`
- `CRONOGRAMA DE CARGAS AL INICIO ZAFRA 26 AL 07.06.2026.xlsx`
- Fuente historica o de prueba previamente mencionada: `DESPACHOS_CORONA_TEST_SPARK.xlsx`

Estas fuentes deben documentarse antes de asumir columnas definitivas. Ningun campo queda como definitivo hasta inspeccionar la estructura real de cada archivo.

## 3. Diseno propuesto

El proyecto se organiza con un principio obligatorio:

**La IA redacta y explica. El codigo valida y clasifica. La persona aprueba.**

Separacion de responsabilidades:

- Ingesta: obtiene archivos, hojas y rangos autorizados.
- Normalizacion: transforma columnas variables en un esquema interno estable.
- Motor de reglas: clasifica pedidos con condiciones deterministicas.
- Generacion de outputs: produce JSON, Markdown, Google Sheet de salida y logs.
- IA: redacta resumen, explica motivos y prepara borradores internos.
- Aprobacion humana: decide si una propuesta se ejecuta, se posterga o se rechaza.

El MVP debe ser simple, auditable y operativo. No debe intentar resolver todas las integraciones en la primera version.

Alternativas de implementacion:

| Enfoque | Uso recomendado | Ventaja | Riesgo |
| --- | --- | --- | --- |
| Simple | Apps Script + Sheets | Rapido para prototipo | Menor observabilidad y pruebas limitadas |
| Intermedio | Node.js + Drive/Sheets + outputs versionados | Mejor trazabilidad y testing | Requiere backend mantenible |
| Robusto | Node-RED + backend + Drive + MCP futuro | Escalable y auditable | Mayor esfuerzo inicial |

Decision recomendada: avanzar con enfoque intermedio, dejando documentado el camino hacia Node-RED y MCP Calipso solo lectura.

## 4. Arquitectura tecnica

Flujo general:

```text
Google Drive/Sheets
  -> Ingesta controlada
  -> Normalizador de planillas
  -> Backend Node.js/JavaScript
  -> Motor de reglas deterministico
  -> Outputs JSON/Markdown/Sheets
  -> IA para redaccion asistida
  -> Revision y aprobacion humana
  -> MCP Calipso futuro solo lectura
```

Responsabilidad por componente:

| Componente | Responsabilidad | Restricciones |
| --- | --- | --- |
| Google Drive/Sheets | Alojar fuentes y salidas | No modificar fuentes originales |
| Node-RED | Orquestar ejecuciones, sincronizacion y alertas internas | No decidir ni aprobar despachos |
| Backend Node.js | Normalizar, validar, clasificar y generar outputs | No usar SQL libre ni credenciales embebidas |
| Motor de reglas | Aplicar condiciones deterministicas | Debe ser testeable y auditable |
| IA | Redactar explicaciones y borradores | No clasifica sola, no inventa datos |
| Aprobacion humana | Validar decisiones criticas | Obligatoria antes de ejecucion operativa |
| MCP Calipso futuro | Consultas controladas de solo lectura | Sin escritura, sin SQL libre |

## 5. Flujo paso a paso

1. Registrar inicio de ejecucion con `run_uuid` y timestamp.
2. Leer fuentes autorizadas desde Drive/Sheets o carpeta de inputs.
3. Registrar nombre, version/fecha, hash o identificador de cada fuente usada.
4. Validar presencia de hojas y columnas minimas conocidas.
5. Normalizar datos hacia entidades internas.
6. Detectar datos faltantes, duplicados o inconsistentes.
7. Cruzar pedidos con cronograma, cobranza, stock, transporte, facturacion y documentacion.
8. Aplicar reglas deterministicas.
9. Clasificar cada pedido.
10. Generar propuesta diaria o semanal como borrador.
11. Generar salida tecnica JSON y salida ejecutiva Markdown/Sheets.
12. Registrar logs de auditoria.
13. Preparar resumen redactado por IA solo si la clasificacion deterministica ya existe.
14. Dejar la decision final pendiente de aprobacion humana.

## 6. Clasificaciones operativas

| Clasificacion | Significado | Condiciones tipicas | Accion recomendada | Responsable sugerido | Requiere aprobacion humana |
| --- | --- | --- | --- | --- | --- |
| `APTO_PARA_PROGRAMAR` | El pedido no presenta bloqueos detectados y puede proponerse para agenda | Stock suficiente, cliente habilitado, transporte disponible, capacidad disponible, documentacion y facturacion sin bloqueo | Incluir en borrador de programacion | Logistica/Despacho | Si, para confirmar ejecucion |
| `BLOQUEADO` | Existe una condicion que impide programar | Cliente bloqueado, deuda vencida bloqueante, stock insuficiente, documentacion critica faltante, facturacion no emitible | Derivar bloqueo al area responsable | Area responsable del bloqueo | Si, para excepciones |
| `PENDIENTE_VALIDACION` | Faltan datos o no se puede validar con seguridad | Fecha ausente, columna no reconocida, fuente faltante, cobranza desactualizada, datos contradictorios | Solicitar validacion o completar fuente | Sistemas/Datos o area fuente | Si |
| `REQUIERE_APROBACION_HUMANA` | El pedido podria avanzar solo con decision explicita | Deuda con prioridad comercial, exceso de capacidad, cambio de fecha, conflicto entre fuentes, excepcion comercial | Elevar decision con motivos y riesgos | Comercial/Gerencia/Administracion | Si |

## 7. Modelo de datos operativo

Los campos siguientes son sugeridos para el esquema interno. No representan columnas definitivas de las planillas fuente.

### Pedido

Obligatorios:

- `pedido_id`
- `cliente_id` o `cliente_nombre`
- `producto_tipo`
- `cantidad`
- `unidad`

Opcionales:

- `fecha_solicitada`
- `prioridad_comercial`
- `estado_pedido`
- `observaciones`
- `origen_fuente`

Observaciones:

- `producto_tipo` debe admitir `azucar` y `alcohol`, aunque el MVP solo procese azucar.
- Si no se puede identificar el pedido, generar identificador interno trazable y marcar validacion pendiente.

### Cliente

Obligatorios:

- `cliente_id` o `cliente_nombre`
- `estado_admin`

Opcionales:

- `condicion_comercial`
- `bloqueo_admin`
- `requiere_autorizacion`
- `observaciones`

### Cronograma

Obligatorios:

- `fecha`
- `capacidad_disponible` o `capacidad_maxima`

Opcionales:

- `turno`
- `horario`
- `capacidad_programada`
- `restricciones_operativas`

### Cobranza

Obligatorios:

- `cliente_id` o `cliente_nombre`
- `estado_cobranza`

Opcionales:

- `deuda_vencida`
- `monto_deuda`
- `fecha_actualizacion`
- `autorizacion_comercial`

### Stock

Obligatorios:

- `producto_tipo`
- `producto_descripcion`
- `stock_disponible`
- `unidad`

Opcionales:

- `ubicacion`
- `lote`
- `fecha_actualizacion`

### Transporte

Obligatorios:

- `estado_transporte`
- `capacidad`
- `unidad`

Opcionales:

- `transportista`
- `chofer`
- `patente`
- `fecha_disponible`
- `producto_habilitado`

### Facturacion

Obligatorios:

- `pedido_id` o referencia equivalente
- `estado_facturacion`

Opcionales:

- `puede_facturarse`
- `motivo_pendiente`
- `documento_asociado`

### Documentacion

Obligatorios:

- `cliente_id` o `pedido_id`
- `estado_documentacion`

Opcionales:

- `documento_faltante`
- `vencimiento`
- `observaciones`

### Resultado del agente

Obligatorios:

- `run_uuid`
- `timestamp`
- `pedido_ref`
- `clasificacion`
- `motivos`
- `accion_recomendada`
- `responsable_sugerido`
- `fuentes_usadas`

Opcionales:

- `riesgos`
- `fecha_recomendada`
- `requiere_aprobacion`
- `conflictos_detectados`

### Auditoria de ejecucion

Obligatorios:

- `run_uuid`
- `timestamp_inicio`
- `timestamp_fin`
- `fuentes_usadas`
- `cantidad_registros_leidos`
- `cantidad_resultados`
- `estado_ejecucion`

Opcionales:

- `errores`
- `warnings`
- `hash_fuentes`
- `usuario_ejecutor`
- `version_reglas`

## 8. Reglas deterministicas iniciales

| Regla | Condicion | Clasificacion resultante | Motivo | Accion recomendada | Responsable sugerido |
| --- | --- | --- | --- | --- | --- |
| Stock insuficiente | `cantidad` > `stock_disponible` validado | `BLOQUEADO` | No hay stock suficiente para cubrir el pedido | Reprogramar, fraccionar o validar stock real | Deposito/Despacho |
| Cliente bloqueado | `bloqueo_admin` verdadero o estado equivalente | `BLOQUEADO` | Cliente con bloqueo administrativo | Solicitar desbloqueo o autorizacion formal | Administracion |
| Deuda vencida | Deuda vencida bloqueante sin autorizacion | `BLOQUEADO` | Riesgo financiero o condicion comercial incumplida | Gestionar cobranza o excepcion aprobada | Cobranzas/Comercial |
| Transporte faltante | No existe transporte disponible compatible | `BLOQUEADO` o `PENDIENTE_VALIDACION` | No se puede ejecutar la carga | Confirmar transporte o reasignar fecha | Logistica/Transporte |
| Fecha de carga faltante | Pedido sin fecha solicitada ni fecha programable | `PENDIENTE_VALIDACION` | Falta dato operativo minimo | Confirmar fecha objetivo | Comercial/Logistica |
| Exceso de capacidad diaria | La propuesta supera capacidad del dia | `REQUIERE_APROBACION_HUMANA` | Sobrecarga operativa | Repriorizar o mover pedidos | Logistica/Gerencia |
| Conflicto entre fuentes | Dos fuentes informan estados incompatibles | `PENDIENTE_VALIDACION` | No hay base confiable para decidir | Validar fuente maestra y registrar correccion | Sistemas/Datos |
| Pedido sin documentacion | Documentacion critica incompleta o vencida | `BLOQUEADO` | Riesgo administrativo/legal | Completar documentacion | Administracion/Cliente |
| Facturacion pendiente | Factura no emitible o pendiente por causa administrativa | `BLOQUEADO` o `PENDIENTE_VALIDACION` | No se puede liberar despacho con seguridad | Resolver condicion de facturacion | Facturacion |
| Autorizacion comercial requerida | Pedido excepcional por prioridad, deuda, cupo o condicion especial | `REQUIERE_APROBACION_HUMANA` | Requiere decision de negocio | Elevar aprobacion con riesgos | Comercial/Gerencia |

Regla de prioridad:

- Si una regla bloqueante y una regla de aprobacion aplican al mismo pedido, prevalece `BLOQUEADO` salvo que exista autorizacion humana explicita documentada.
- Si faltan datos para evaluar una regla critica, prevalece `PENDIENTE_VALIDACION`.
- La IA no puede cambiar una clasificacion deterministica. Solo puede redactar una explicacion.

## 9. Diseno de outputs

Toda salida debe incluir:

- `run_uuid`
- Timestamp de ejecucion
- Fuentes usadas
- Estado de ejecucion
- Clasificacion por pedido
- Motivos
- Riesgos
- Acciones sugeridas
- Responsable sugerido
- Indicacion de aprobacion humana requerida

### Propuesta diaria de despacho

Contenido minimo:

- Fecha propuesta
- Pedidos aptos para programar
- Capacidad estimada usada
- Transporte sugerido si existe fuente validada
- Bloqueos del dia
- Riesgos y decisiones pendientes

### Propuesta semanal

Contenido minimo:

- Distribucion por dia
- Capacidad estimada por jornada
- Pedidos postergados
- Pedidos que requieren validacion
- Riesgos acumulados de stock, cobranza, transporte y facturacion

### Reporte ejecutivo

Contenido minimo:

- Total de pedidos analizados
- Totales por clasificacion
- Principales bloqueos
- Impacto operativo
- Decisiones requeridas
- Recomendacion de proximos pasos

### JSON tecnico

Contenido minimo:

- Metadata de ejecucion
- Fuentes usadas
- Registros normalizados
- Resultados por pedido
- Warnings y errores
- Version de reglas

### Google Sheet de salida

Pestanas recomendadas:

- `Resumen`
- `Resultados`
- `Aptos`
- `Bloqueados`
- `Pendientes Validacion`
- `Requieren Aprobacion`
- `Auditoria`

### Log de auditoria

Contenido minimo:

- `run_uuid`
- Inicio y fin
- Usuario o proceso ejecutor
- Fuentes leidas
- Cantidad de registros procesados
- Reglas aplicadas
- Errores y advertencias
- Ruta de outputs generados

## 10. Riesgos y controles

| Riesgo | Impacto | Control |
| --- | --- | --- |
| Datos incompletos | Clasificacion incorrecta | Marcar `PENDIENTE_VALIDACION` y registrar dato faltante |
| Columnas variables en Excel | Falla de ingesta | Schema versionado y normalizador por fuente |
| IA inventa datos | Decision operativa indebida | IA solo redacta sobre datos estructurados |
| Despacho confirmado automaticamente | Riesgo operativo y comercial alto | Aprobacion humana obligatoria |
| Error de stock | Despacho no ejecutable | Stock debe provenir de fuente validada y registrar fecha |
| Cobranza desactualizada | Riesgo financiero | Mostrar fecha de actualizacion y responsable |
| Conflictos entre fuentes | Decision no confiable | Marcar conflicto y bloquear decision automatica |
| Dependencia de planillas manuales | Baja robustez | Documentar fuentes, hashes y versionado |
| Integracion futura Calipso | Riesgo ERP | MCP solo lectura, herramientas parametrizadas |
| Adopcion por usuarios | Bajo uso real | Outputs claros por responsable y MVP rapido |

## 11. MCP Calipso futuro seguro

El MCP Calipso solo podra operar en modo consulta controlada. No se permite escritura ni SQL libre generado por IA.

Herramientas futuras permitidas:

| Herramienta | Parametros permitidos | Salida esperada | Restricciones |
| --- | --- | --- | --- |
| Estado de cliente | `cliente_id`, `cliente_nombre` | Estado administrativo resumido | Solo lectura |
| Cuenta corriente | `cliente_id`, rango de fechas | Saldos y vencimientos | Compatible SQL Server 2008 R2 |
| Pedidos pendientes | `cliente_id`, `fecha_desde`, `fecha_hasta` | Pedidos abiertos | Sin modificar pedidos |
| Facturacion | `pedido_id`, `cliente_id` | Estado de factura/remito | Sin emitir comprobantes |
| Remitos | `pedido_id`, `cliente_id` | Remitos asociados | Sin crear ni anular |
| Stock | `producto_id`, `producto_tipo` | Stock disponible consultado | Sin movimientos de stock |
| Orden de compra | `cliente_id`, `oc_ref` | Estado documental | Sin alta ni modificacion |
| Documentacion cliente | `cliente_id` | Estado de documentos | Sin actualizar documentos |

Prohibiciones:

- SQL libre desde prompts.
- Escritura en tablas Calipso.
- Cambios de estado.
- Confirmacion automatica de pedidos, remitos, facturas o movimientos.
- Credenciales reales en codigo.
- Consultas incompatibles con SQL Server 2008 R2.

## 12. Node-RED previsto

Flujos recomendados:

| Flujo | Objetivo | Entrada | Salida |
| --- | --- | --- | --- |
| Sincronizacion Drive | Detectar y copiar fuentes autorizadas | Carpeta Drive/Sheets | Inputs versionados |
| Ejecucion diaria | Lanzar agente con configuracion vigente | Scheduler/manual | `run_uuid` y outputs |
| Generacion de outputs | Publicar resultados | JSON tecnico | Markdown/Sheets/log |
| Aprobacion humana | Enviar propuesta interna para revision | Resultado del agente | Estado aprobado/rechazado/observado |
| Consulta MCP Calipso | Enriquecer datos futuros | Cliente/pedido/producto | Datos solo lectura |
| Logging y auditoria | Registrar trazabilidad | Eventos del flujo | Log centralizado |

## 13. Estructura recomendada del repositorio

```text
AGENTS.md
contexto.md
README.md
docs/
config/
  schema.json
  sources.example.json
src/
  ingest/
  normalize/
  rules/
  output/
  llm/
  audit/
node-red/
mcp/
skills/
data/
  inputs/
  outputs/
  samples/
tests/
```

Reglas para el repositorio:

- `data/inputs/` solo debe contener muestras o archivos de prueba, no fuentes productivas sensibles.
- `config/*.example.json` puede documentar estructura sin credenciales.
- Las credenciales reales deben ir en variables de entorno o secretos externos.
- Cada cambio funcional debe tener prueba minima cuando modifique reglas o normalizacion.

## 14. Roadmap de implementacion

### Fase 0 - Estructura y contexto

- Crear `contexto.md`.
- Definir estructura base del repositorio.
- Documentar fuentes iniciales y restricciones.
- Separar test/prod desde el inicio.

### Fase 1 - Normalizacion de planillas

- Inspeccionar archivos reales.
- Crear `schema.json`.
- Mapear columnas por fuente.
- Detectar faltantes y cambios de formato.

### Fase 2 - Motor de reglas

- Implementar reglas deterministicas.
- Crear tests por clasificacion.
- Generar resultados por pedido.

### Fase 3 - Reportes

- Crear output JSON tecnico.
- Crear reporte Markdown.
- Crear Google Sheet de salida.
- Crear log de auditoria.

### Fase 4 - IA para redaccion

- Integrar OpenAI/Gemini solo para resumen y explicaciones.
- Implementar fallback si falla el modelo.
- Bloquear cualquier decision basada solo en IA.

### Fase 5 - Node-RED + Drive

- Automatizar ejecucion diaria.
- Versionar inputs y outputs.
- Agregar notificacion interna para aprobacion.

### Fase 6 - MCP Calipso solo lectura

- Definir herramientas parametrizadas.
- Validar consultas SQL Server 2008 R2.
- Agregar controles de acceso y logs.

### Fase 7 - Tablero de aprobacion

- Mostrar propuestas.
- Registrar aprobaciones, rechazos y observaciones.
- Mantener historial por `run_uuid`.

## 15. Backlog priorizado inicial

1. Crear estructura base de carpetas.
2. Documentar fuentes reales y hojas disponibles.
3. Crear `config/schema.json` inicial sin columnas definitivas no validadas.
4. Crear normalizador por fuente con logs de columnas desconocidas.
5. Crear entidades internas: pedido, cliente, cobranza, stock, transporte, facturacion, cronograma.
6. Implementar reglas deterministicas iniciales.
7. Crear tests unitarios de clasificacion.
8. Generar output JSON tecnico con `run_uuid`.
9. Generar reporte Markdown ejecutivo.
10. Crear log de auditoria por ejecucion.

## 16. Prompts iniciales para Codex

### Crear estructura del proyecto

```text
Crea la estructura base del proyecto agente-despachos-corona segun contexto.md. No agregues credenciales ni datos productivos. Inclui README.md, config/schema.json inicial, src/ y tests/.
```

### Documentar planillas base

```text
Inspecciona las planillas disponibles en data/inputs o fuente indicada. Documenta hojas, columnas, tipos aparentes y dudas en docs/fuentes.md. No modifiques los archivos fuente.
```

### Crear schema inicial

```text
Crea config/schema.json con entidades internas para pedido, cliente, cronograma, cobranza, stock, transporte, facturacion, resultado y auditoria. No inventes columnas definitivas de Excel; separa campos internos de mapeos por fuente.
```

### Crear normalizadores iniciales

```text
Implementa normalizadores para las fuentes documentadas. Deben devolver datos estructurados, warnings por columnas faltantes y errores controlados. No deben modificar fuentes.
```

### Crear motor de reglas

```text
Implementa el motor de reglas deterministico para clasificar pedidos en APTO_PARA_PROGRAMAR, BLOQUEADO, PENDIENTE_VALIDACION o REQUIERE_APROBACION_HUMANA. La IA no debe intervenir en la clasificacion.
```

### Crear tests

```text
Crea tests unitarios para las reglas de stock insuficiente, cliente bloqueado, deuda vencida, transporte faltante, fecha faltante, exceso de capacidad, conflicto entre fuentes, documentacion faltante y facturacion pendiente.
```

### Crear outputs

```text
Crea generadores de output JSON y Markdown. Toda salida debe incluir run_uuid, timestamp, fuentes usadas, clasificacion, motivos, riesgos, acciones sugeridas y responsable sugerido.
```

### Crear auditoria

```text
Implementa logger de auditoria por ejecucion con run_uuid, timestamps, fuentes usadas, cantidades procesadas, version de reglas, warnings y errores.
```

### Documentar Node-RED

```text
Crea docs/node-red.md con los flujos previstos para sincronizacion Drive, ejecucion diaria, generacion de outputs, aprobacion humana, consulta futura MCP Calipso y auditoria.
```

### Documentar MCP Calipso

```text
Crea docs/mcp-calipso.md con herramientas futuras de solo lectura, parametros permitidos, salidas esperadas, restricciones, y prohibicion expresa de SQL libre y escritura en ERP.
```

## 17. Entregables concretos

Entregables del MVP 0.1:

- `contexto.md` consolidado.
- `README.md` operativo.
- `docs/fuentes.md` con estructura real de planillas.
- `config/schema.json` con entidades internas.
- Normalizadores iniciales.
- Motor de reglas deterministico.
- Tests de reglas.
- Output JSON tecnico.
- Reporte Markdown ejecutivo.
- Log de auditoria.

Criterios de aceptacion:

- El agente procesa fuentes de prueba sin modificar archivos originales.
- Cada ejecucion genera `run_uuid`.
- Cada pedido queda clasificado con motivo y accion recomendada.
- Los datos faltantes no se inventan.
- Los conflictos entre fuentes quedan visibles.
- No hay confirmacion automatica de despachos.
- No hay escritura en Calipso.
- La IA, si se usa, solo redacta sobre resultados ya calculados.

## 18. Proxima version

Version 0.2:

- Integracion IA para redaccion asistida.
- Mejoras de reportes por responsable.
- Outputs en Google Sheets.
- Validacion manual mas ordenada.

Version 0.3:

- Node-RED para ejecucion programada.
- Drive como circuito formal de inputs/outputs.
- Notificaciones internas de aprobacion.

Version 0.4:

- MCP Calipso solo lectura.
- Dashboard de aprobaciones.
- Historial y comparacion entre ejecuciones.

## 19. Modulo web — Despachos Pendientes de Facturacion

A partir del analisis del proyecto y necesidades operativas, se agrego un modulo web para visualizar y gestionar remitos de azucar pendientes de facturacion.

### Stack
- **Frontend**: HTML + CSS + JavaScript puro (ES6 modules), estilo profesional con sidebar, KPIs y tabla filtrable.
- **Middleware**: Node-RED como API REST, conectando a SQL Server CORONA (Calipso).
- **Fuente de datos**: `pr_ezi_remitos` + `pr_ezi_remitos_items` (remitos sin factura).
- **Mock data**: `assets/js/mockData.js` para desarrollo offline.

### Endpoints Node-RED
- `GET /api/despachos/pendientes?days=30` — Lista remitos sin facturar
- `GET /api/despachos/pendientes/:remito` — Detalle completo
- `POST /api/despachos/pendientes/:remito/facturar` — Vincular factura
- `GET /api/despachos/resumen` — KPIs
- `GET /api/despachos/health` — Health check
- `GET /despachos-pendientes` — Frontend HTML (con httpStatic)

### Funcionalidades UI
- KPIs: total remitos, clientes, bolsas, toneladas, importe
- Tabla con filtro por dias (7/15/30/60/90) y busqueda textual
- Modal de detalle con items del remito, datos de cliente, transporte
- Columna de clasificacion del Agente de Despachos (APTO/BLOQUEADO/PENDIENTE/APROBACION)
- Boton para vincular con factura (POST a Node-RED)

### Integracion con Agente de Despachos
- La clasificacion del agente (Apps Script) puede vincularse por `pedido_id`/`cliente`+`producto`
- El frontend muestra la clasificacion en tabla y detalle
- Integracion futura via API desde Google Apps Script

### Archivos entregados
- `index.html` — Frontend principal
- `assets/css/styles.css` — Estilos profesionales
- `assets/js/app.js` — Orquestador
- `assets/js/config.js` — Configuracion
- `assets/js/dataService.js` — Capa de datos
- `assets/js/mockData.js` — Mock datos
- `assets/js/renderTables.js` — Renderizado tablas/modal
- `flows/flow_despachos_pendientes.json` — Flow Node-RED
- `docs/node-red-despachos.md` — Documentacion Node-RED
- `README_despachos_pendientes.md` — Documentacion del modulo

