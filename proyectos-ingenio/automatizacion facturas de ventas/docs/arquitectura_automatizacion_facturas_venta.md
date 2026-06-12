# Automatizacion de facturas de venta de azucar y alcohol

## 1. Objetivo

Automatizar la mayor parte del proceso de facturacion de ventas de azucar y alcohol, partiendo de remitos/despachos pendientes y pasando por una interfaz de validacion de usuario autorizado antes de generar la factura definitiva en Calipso.

## 2. Supuestos y contexto

- La base consultada por MCP fue `CORONA`, productiva. Se usaron solo consultas `SELECT` readonly.
- Para desarrollo y pruebas se debe usar base TEST/restaurada antes de cualquier escritura o integracion de registracion.
- No se debe escribir directo en tablas del ERP Calipso.
- La fuente inicial de pendientes validada es:
  - Azucar: `dbo.pr_ezi_remitos` + `dbo.pr_ezi_remitos_items`.
  - Alcohol: `dbo.pr_ezi_remitos_alcohol` + `dbo.pr_ezi_remitos_alcohol_items`.
- Regla inicial de pendiente: `factura IS NULL OR LTRIM(RTRIM(factura)) = ''`, excluyendo estados anulados.
- La vista `dbo.V_EZI_REMITOS_AZUCAR_ENVIAR` no es confiable hoy porque falla por dependencia rota a `INTERFACE.dbo.REMITOS_ITEMS_CALIPSO`.

## 3. Diseno propuesto

MVP:
- Consulta readonly de remitos pendientes por producto.
- Agrupacion por cliente, CUIT, orden y tipo de producto.
- Pantalla de validacion con filtros, seleccion de remitos y previsualizacion de factura.
- Boton "Preparar factura" que genera un registro de staging propio, no una factura directa.
- Usuario autorizado confirma y el middleware ejecuta el alta por mecanismo Calipso definido.

Version operativa:
- Reglas de validacion por CUIT, cliente, precio, cantidad, unidad, remito duplicado y orden.
- Auditoria por UUID de operacion.
- Estados: `PENDIENTE`, `EN_REVISION`, `APROBADO`, `ENVIADO_CALIPSO`, `FACTURADO`, `ERROR`.
- Logs tecnicos y log funcional visible para Administracion.

Version escalable:
- Integracion Node-RED para consulta on-prem y API local.
- n8n para notificaciones, aprobaciones y documentacion.
- Generacion automatica de PDF/CAE solo cuando Calipso/AFIP lo permita por flujo autorizado.

## 4. Arquitectura tecnica

- MCP SQL Server readonly: exploracion, diagnostico y consultas de pendientes.
- Middleware local: API interna con usuario autorizado, staging, validaciones y logs.
- SQL Server TEST: staging y procedimientos propios `UD_EZI`/middleware.
- Web interna: interfaz de validacion y aprobacion.
- Node-RED: ejecucion local contra SQL Server y servicios internos.
- n8n: orquestacion documental, avisos y reportes.

## 5. Flujo paso a paso

1. Operador abre pantalla "Facturas de venta pendientes".
2. Sistema consulta `sql/01_despachos_pendientes_facturar_azucar.sql` y `sql/02_despachos_pendientes_facturar_alcohol.sql`.
3. Operador filtra por producto, cliente, fecha, orden o remito.
4. Sistema valida datos minimos: CUIT, cliente, precio, cantidad, unidad, remito sin factura.
5. Operador selecciona uno o varios remitos compatibles.
6. Sistema arma pre-factura y calcula totales.
7. Usuario autorizado aprueba.
8. Middleware genera registro auditado con UUID.
9. Integracion Calipso genera factura definitiva por mecanismo autorizado.
10. Sistema actualiza estado de staging y muestra numero de factura o error.

## 6. Riesgos y controles

- Riesgo: trabajar sobre `CORONA` productiva. Control: solo readonly ahora; desarrollo contra TEST.
- Riesgo: campo `factura` vacio puede no cubrir todos los casos. Control: comparar contra facturas recientes y remitos ya facturados.
- Riesgo: CUIT `XX` o NULL en algunos remitos. Control: bloqueo o validacion manual obligatoria.
- Riesgo: precios cero o atipicos. Control: alerta y aprobacion reforzada.
- Riesgo: vista existente rota. Control: no depender de `V_EZI_REMITOS_AZUCAR_ENVIAR` hasta reparar `INTERFACE`.
- Riesgo: facturacion parcial/multiple remitos por factura. Control: reglas de agrupacion por cliente, tipo, moneda, punto de venta y orden.

## 7. Proxima version

- Confirmar reglas de agrupacion con Administracion.
- Crear staging propio de facturacion de venta.
- Construir pantalla web MVP.
- Agregar endpoint readonly `/api/despachos-pendientes`.
- Agregar endpoint protegido `/api/pre-facturas` para validacion/aprobacion.

## 8. Entregables concretos

- `sql/01_despachos_pendientes_facturar_azucar.sql`
- `sql/02_despachos_pendientes_facturar_alcohol.sql`
- `sql/03_validaciones_fuentes_facturacion.sql`
- `tools/mcp-readonly-query.mjs`

## Preguntas necesarias

1. La factura se debe generar una por remito, una por orden, o se pueden agrupar varios remitos del mismo cliente en una factura?
2. Quienes son usuarios autorizados para aprobar: Administracion, Sistemas, Ventas, Tesoreria?
3. Que condiciones bloquean facturacion: CUIT faltante, precio cero, remito sin orden, remito no importado, alcohol sin analisis?
4. Para alcohol, que campos son obligatorios en factura: GL, litros absolutos, protocolo, nro analisis, neto/bruto?
5. Para azucar, que unidad manda para facturar: bolsas, kg, toneladas, o la unidad del item?
6. El campo `factura` en `pr_ezi_remitos*` se completa automaticamente por Calipso al facturar o por algun proceso externo?
7. Existe base TEST actualizada para probar staging y generacion sin tocar `CORONA`?
8. La interfaz debe integrarse al sistema web existente o puede ser una app interna nueva?
