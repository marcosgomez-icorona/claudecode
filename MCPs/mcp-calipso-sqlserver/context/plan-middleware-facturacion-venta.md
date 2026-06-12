# Plan middleware - Facturacion de venta azucar/alcohol

Fecha: 2026-06-05
Proyecto: `automatizacion facturas de ventas`
Contexto base: `context/facturas-venta-azucar-alcohol.md`

## Objetivo

Construir una solucion con interfaz de usuario autorizado, base MySQL de middleware, Node-RED on-prem y n8n para automatizar la facturacion de venta de azucar y alcohol desde remitos/despachos pendientes.

## Arquitectura decidida para MVP

- SQL Server Calipso:
  - Solo lectura para obtener pendientes.
  - Fuentes:
    - `dbo.pr_ezi_remitos`
    - `dbo.pr_ezi_remitos_items`
    - `dbo.pr_ezi_remitos_alcohol`
    - `dbo.pr_ezi_remitos_alcohol_items`
- MySQL middleware:
  - Base: `facturacion_venta_mw`.
  - Tablas:
    - `usuarios_autorizados`
    - `despachos_pendientes`
    - `prefacturas`
    - `prefactura_items`
    - `prefactura_validaciones`
    - `integracion_outbox`
    - `auditoria_eventos`
- Web interna:
  - Bandeja de pendientes.
  - Detalle de remito.
  - Prefactura.
  - Monitor de integracion.
- Node-RED:
  - Sincroniza pendientes desde Calipso a MySQL.
  - Procesa outbox de prefacturas aprobadas.
  - Llama middleware Calipso autorizado.
- n8n:
  - Notifica errores, aprobaciones y facturas generadas.
  - Emite reportes.

## Entregables creados en repo

- `docs/plan_maestro_facturacion_venta.md`
- `web/README_INTERFAZ_VALIDACION.md`
- `sql/10_mysql_middleware_facturacion_venta.sql`
- `node-red/flow_facturacion_venta_mvp.json`
- `n8n/workflow_facturacion_venta_notificaciones.json`

## Flujo principal

1. Node-RED consulta pendientes de azucar/alcohol en SQL Server.
2. Node-RED normaliza datos y actualiza `despachos_pendientes` en MySQL.
3. Usuario trabaja en interfaz y arma prefactura.
4. API valida y guarda `prefacturas`, `prefactura_items` y `prefactura_validaciones`.
5. Aprobador confirma.
6. API cambia estado a `APROBADO` y crea `integracion_outbox`.
7. Node-RED toma outbox y llama servicio autorizado de Calipso.
8. Resultado vuelve a MySQL.
9. n8n notifica resultado y errores.

## Reglas de control

- Nunca escribir directo en tablas del ERP.
- Alta definitiva solo en TEST hasta autorizacion.
- Cada prefactura tiene UUID.
- Toda aprobacion debe registrar usuario, fecha, observacion y auditoria.
- Remito con `factura` no vacia no se debe prefacturar.
- CUIT NULL, vacio o `XX` debe bloquear o requerir aprobacion superior.
- Precio cero debe bloquear o requerir aprobacion superior.
- Alcohol debe validar GL, litros y nro de analisis si Administracion lo confirma como obligatorio.

## Pendientes funcionales

1. Definir agrupacion: por remito, por orden o por cliente.
2. Confirmar roles reales.
3. Confirmar base TEST y mecanismo de alta Calipso.
4. Confirmar si `importado = 1` es obligatorio.
5. Confirmar campos obligatorios por producto.
