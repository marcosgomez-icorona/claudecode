# Plan maestro - Automatizacion de facturas de venta

## 1. Objetivo

Automatizar la facturacion de ventas de azucar y alcohol desde remitos/despachos pendientes, reduciendo pasos manuales y manteniendo una validacion humana autorizada antes de enviar la operacion a Calipso.

## 2. Supuestos y contexto

- Fuente readonly validada por MCP contra Calipso `CORONA`:
  - Azucar: `dbo.pr_ezi_remitos` y `dbo.pr_ezi_remitos_items`.
  - Alcohol: `dbo.pr_ezi_remitos_alcohol` y `dbo.pr_ezi_remitos_alcohol_items`.
- Pendiente inicial: campo `factura` NULL/vacio y estado no anulado.
- Middleware en MySQL para staging, auditoria, validaciones y control de estados.
- No escribir directo en tablas Calipso.
- Alta definitiva en Calipso solo por mecanismo autorizado y probado en TEST.
- Interfaz interna para usuarios autorizados.

## 3. Diseno propuesto

### MVP

- Pantalla unica de "Despachos pendientes de facturar".
- Filtros por producto, fecha, cliente, CUIT, orden y remito.
- Seleccion de remitos compatibles.
- Previsualizacion de prefactura.
- Validaciones bloqueantes y advertencias.
- Aprobacion por usuario autorizado.
- Registro en MySQL como staging.
- Envio a Node-RED para preparar operacion.
- Estado visible hasta `FACTURADO` o `ERROR`.

### Operativo

- Control de usuarios/roles.
- Log funcional y tecnico por UUID.
- Agrupacion de remitos segun regla aprobada.
- Control de duplicados.
- Reintentos controlados.
- Exportacion/reporte diario.

### Escalable

- n8n para notificaciones, aprobaciones externas y reportes.
- Integracion documental: PDF, constancia, remito/factura.
- Agente de conciliacion: remito pendiente vs factura generada.

## 4. Arquitectura tecnica

- Web interna:
  - Frontend: tabla densa, filtros, seleccion, panel de validacion.
  - Backend/API: endpoints contra MySQL y Node-RED.
- MySQL middleware:
  - Staging de prefacturas.
  - Items de prefacturas.
  - Validaciones.
  - Auditoria.
  - Outbox para integraciones.
- Node-RED on-prem:
  - Consulta SQL Server Calipso readonly.
  - Sincroniza pendientes hacia MySQL.
  - Procesa prefacturas aprobadas.
  - Llama middleware/servicio Calipso autorizado.
- n8n cloud:
  - Notificaciones.
  - Aprobaciones opcionales.
  - Reportes.
  - Alertas de error.
- Calipso SQL Server:
  - Solo lectura para pendientes.
  - Escritura final solo por mecanismo autorizado.

## 5. Flujo paso a paso

1. Node-RED ejecuta sincronizacion de pendientes cada 5/10 minutos.
2. Node-RED consulta SQL Server usando las queries validadas.
3. Node-RED inserta/actualiza en MySQL `despachos_pendientes`.
4. Usuario entra a la interfaz.
5. Interfaz muestra pendientes desde MySQL.
6. Usuario selecciona remitos.
7. API crea prefactura en estado `BORRADOR`.
8. Motor de validaciones genera errores/advertencias.
9. Usuario corrige o justifica advertencias.
10. Usuario autorizado aprueba.
11. API cambia prefactura a `APROBADO` y crea evento outbox.
12. Node-RED toma outbox pendiente.
13. Node-RED arma payload final para Calipso.
14. Middleware Calipso registra o deja cola lista para registracion humana asistida.
15. Node-RED actualiza estado `FACTURADO` o `ERROR`.
16. n8n notifica resultado y genera reporte.

## 6. Riesgos y controles

- Riesgo: facturar remito incorrecto. Control: seleccion explicita y previsualizacion.
- Riesgo: duplicado. Control: unique por producto/remito y bloqueo si ya hay factura.
- Riesgo: CUIT faltante o `XX`. Control: bloqueo o aprobacion reforzada.
- Riesgo: precio cero. Control: bloqueo salvo rol superior.
- Riesgo: agrupacion incorrecta. Control: reglas por cliente, CUIT, orden, producto, moneda y tipo.
- Riesgo: produccion. Control: TEST obligatorio para alta, readonly en `CORONA`.
- Riesgo: vista rota `V_EZI_REMITOS_AZUCAR_ENVIAR`. Control: no usarla hasta reparar dependencia `INTERFACE`.

## 7. Proxima version

1. Confirmar reglas funcionales de agrupacion.
2. Crear base MySQL middleware.
3. Implementar API local.
4. Implementar interfaz MVP.
5. Importar flujo Node-RED de sincronizacion.
6. Importar workflow n8n de alertas/reporte.
7. Validar en TEST con casos reales anonimizados.
8. Relevar mecanismo oficial de alta Calipso.

## 8. Entregables concretos de esta etapa

- `docs/plan_maestro_facturacion_venta.md`
- `web/README_INTERFAZ_VALIDACION.md`
- `sql/10_mysql_middleware_facturacion_venta.sql`
- `node-red/flow_facturacion_venta_mvp.json`
- `n8n/workflow_facturacion_venta_notificaciones.json`

## Decisiones pendientes

1. Agrupacion: una factura por remito, por orden o por cliente.
2. Roles: quienes pueden aprobar, aprobar con advertencias y reintentar errores.
3. Calipso: mecanismo autorizado para generar factura definitiva.
4. TEST: nombre/base disponible para pruebas.
5. Alcohol: campos obligatorios definitivos.
6. Azucar: unidad final de facturacion.
