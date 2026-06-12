# Layout esperado - Sumas y Saldos

## Estado

Pendiente de validar con archivo real exportado desde Calipso.

## Columnas minimas candidatas

| Campo normalizado | Obligatorio | Tipo esperado | Descripcion | Estado |
| --- | --- | --- | --- | --- |
| codigo_cuenta | Si | texto | Codigo de cuenta contable | Pendiente validar |
| nombre_cuenta | Si | texto | Descripcion de la cuenta | Pendiente validar |
| debe | Condicional | decimal | Movimiento debe del periodo/corte | Pendiente validar |
| haber | Condicional | decimal | Movimiento haber del periodo/corte | Pendiente validar |
| saldo_deudor | Condicional | decimal | Saldo deudor informado | Pendiente validar |
| saldo_acreedor | Condicional | decimal | Saldo acreedor informado | Pendiente validar |
| saldo_final | Condicional | decimal | Saldo neto calculado o informado | Pendiente validar |
| rubro | No | texto | Rubro, grupo o clasificacion | Pendiente validar |
| nivel | No | entero | Nivel jerarquico de la cuenta | Pendiente validar |
| moneda | No | texto | Moneda del saldo | Pendiente validar |

## Reglas de normalizacion candidatas

- Convertir importes con separador local a decimal estandar.
- Quitar espacios iniciales/finales en codigo y nombre de cuenta.
- Mantener codigo de cuenta como texto para no perder ceros iniciales.
- Calcular `saldo_final` si el archivo solo informa debe/haber o deudor/acreedor.
- Registrar importes originales y normalizados cuando haya conversion.

## Validaciones minimas

- El archivo debe tener al menos codigo y descripcion de cuenta.
- Debe existir una forma confiable de obtener saldo final.
- No debe haber codigos de cuenta vacios en filas de movimiento.
- Las filas de totales deben identificarse para no duplicar analisis.
- El total general debe poder reconciliarse o quedar marcado como no disponible.

## Pendiente con Administracion

1. Confirmar nombre exacto del reporte en Calipso.
2. Confirmar columnas reales.
3. Confirmar si incluye cuentas sin movimiento.
4. Confirmar si incluye apertura por centro de costos, unidad operativa o moneda.
5. Confirmar si el reporte incluye totales/subtotales que deban ignorarse.
