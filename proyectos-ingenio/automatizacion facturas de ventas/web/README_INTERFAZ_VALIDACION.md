# Interfaz de validacion - Facturacion de venta

## Objetivo

Pantalla interna para que un usuario autorizado revise remitos/despachos pendientes, arme una prefactura, valide datos criticos y apruebe el envio al middleware.

## Vistas MVP

### 1. Bandeja de pendientes

Columnas:

- Producto: Azucar / Alcohol.
- Fecha remito.
- Remito.
- Orden.
- Cliente.
- CUIT.
- Descripcion.
- Cantidad.
- Unidad.
- Precio.
- Importe estimado.
- Estado de validacion.
- Alertas.

Filtros:

- Producto.
- Desde / hasta fecha.
- Cliente.
- CUIT.
- Orden.
- Remito.
- Solo con alertas.
- Solo importados.

Acciones:

- Refrescar.
- Seleccionar remitos.
- Crear prefactura.
- Ver detalle.

### 2. Detalle de remito

Debe mostrar:

- Datos de cliente.
- Datos de transporte.
- Items.
- Orden asociada.
- Estado `factura`.
- Datos especificos de alcohol: GL, litros alcohol, analisis, neto/bruto.
- Historial de sincronizacion.

### 3. Prefactura

Debe mostrar:

- UUID de operacion.
- Remitos incluidos.
- Cliente/CUIT.
- Items consolidados.
- Totales estimados.
- Validaciones.
- Observaciones del operador.
- Boton aprobar.
- Boton rechazar/anular.

### 4. Monitor de integracion

Debe mostrar:

- Estado: `BORRADOR`, `EN_VALIDACION`, `APROBADO`, `ENVIADO_CALIPSO`, `FACTURADO`, `ERROR`.
- Fecha/hora.
- Usuario.
- Mensaje tecnico.
- Numero de factura generado.
- Reintentos.

## Roles sugeridos

- `LECTOR`: ve pendientes y estados.
- `OPERADOR`: arma prefacturas.
- `APROBADOR`: aprueba prefacturas sin alertas bloqueantes.
- `SUPERVISOR`: aprueba advertencias justificadas y reintentos.
- `ADMIN`: administra parametros.

## Endpoints API sugeridos

```http
GET /api/despachos-pendientes?producto=AZUCAR&desde=2026-06-01&hasta=2026-06-05
GET /api/despachos-pendientes/:id
POST /api/prefacturas
GET /api/prefacturas/:uuid
POST /api/prefacturas/:uuid/validar
POST /api/prefacturas/:uuid/aprobar
POST /api/prefacturas/:uuid/anular
GET /api/integraciones/:uuid/eventos
```

## Validaciones visibles

Bloqueantes:

- Remito ya facturado.
- CUIT vacio, NULL o `XX`.
- Cliente vacio.
- Cantidad menor o igual a cero.
- Precio menor a cero.
- Producto sin item.

Advertencias:

- Precio cero.
- Remito sin orden.
- `importado` distinto de 1.
- Alcohol sin nro de analisis.
- Alcohol sin GL.
- Datos de transporte incompletos.

## Ergonomia

- Tabla compacta para uso administrativo.
- Seleccion multiple con totales en panel lateral.
- Alertas con iconos y tooltip.
- Acciones principales fijas en barra superior.
- Nada se envia a Calipso sin confirmacion explicita.
