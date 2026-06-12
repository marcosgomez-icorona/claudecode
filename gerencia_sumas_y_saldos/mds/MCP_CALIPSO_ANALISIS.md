# MCP Calipso - Analisis de consulta Sumas y Saldos

## 1. Objetivo

Documentar el avance de analisis realizado con MCP Calipso readonly para encontrar una consulta base confiable de Sumas y Saldos / Mayor contable y definir como usarla dentro del proyecto.

## 2. Supuestos y contexto

- Base consultada: `CORONA`.
- Acceso usado: MCP/helper readonly contra SQL Server.
- Compatibilidad requerida: SQL Server 2008 R2.
- No se ejecutaron escrituras ni cambios en Calipso.
- La consulta provista por Sistemas se tomo como ancla funcional.

## 3. Vistas confirmadas

Las siguientes vistas existen y fueron confirmadas por metadata:

- `V_TRCONTABLE_`
- `V_ITEMCONTABLE_`
- `V_VALOR_`
- `V_EZI_CUENTAS`
- `V_EZI_ASIENTOS_DETALLES`
- `V_CENTROCOSTOS_`
- `V_EZI_ORDENES_COMPRA2`

Tambien se detectaron vistas candidatas de mayores/saldos:

- `V_EZI_CR_MAYORCUENTAS`
- `V_EZI_CR_MAYORCUENTAS2`
- `V_MAYORCUENTARESULT`
- `V_MAYORCUENTARESULT_`
- `V_ITEMMAYORCUENTARESULT`
- `V_ITEMMAYORCUENTARESULT_`
- `V_SUMA`
- `V_SUMA_`

## 4. Hallazgos tecnicos

### Consulta base de movimientos

La consulta provista por Sistemas se apoya correctamente en:

- `V_TRCONTABLE_.ITEMSTRANSACCION_ID = V_ITEMCONTABLE_.BO_PLACE_ID`
- `V_ITEMCONTABLE_.DEBE_ID = V_VALOR_.ID`
- `V_ITEMCONTABLE_.HABER_ID = V_VALOR_.ID`
- `V_ITEMCONTABLE_.REFERENCIA_ID = V_EZI_CUENTAS.ID`
- `V_ITEMCONTABLE_.CENTROCOSTOS_ID = V_CENTROCOSTOS_.ID`
- `V_TRCONTABLE_.ID = V_EZI_ASIENTOS_DETALLES.ID`

El filtro operativo minimo confirmado:

```sql
WHERE SUBSTRING(V_TRCONTABLE_.FECHAAPLICACION, 1, 8) >= 'YYYYMMDD'
  AND SUBSTRING(V_TRCONTABLE_.FECHAAPLICACION, 1, 8) <= 'YYYYMMDD'
  AND V_EZI_CUENTAS.CODIGO BETWEEN '0' AND '9'
  AND V_TRCONTABLE_.ESTADO = 'C'
```

### Rendimiento

- La consulta desde `20230501` con detalle completo produjo timeout.
- La misma logica acotada a junio 2026 respondio correctamente.
- `V_EZI_CR_MAYORCUENTAS2` parece funcionalmente util, pero produjo timeout incluso con fecha exacta, por lo que no se recomienda como fuente operativa directa sin mas pruebas o filtros internos.
- La agregacion mensual por cuenta desde vistas base respondio correctamente y es la mejor candidata para snapshot MVP.

### Volumen mensual observado

Conteo de transacciones confirmadas por periodo en `V_TRCONTABLE_`:

- `202606`: 319
- `202605`: 1553
- `202604`: 2044
- `202603`: 1955
- `202602`: 964
- `202601`: 961
- `202512`: 1660
- `202511`: 1307
- `202510`: 2127
- `202509`: 2132

Conclusion: el volumen mensual es manejable, pero los rangos historicos grandes deben procesarse por periodo o por batch.

## 5. Consulta candidata - Snapshot por cuenta y periodo

Esta consulta genera una base tipo Sumas y Saldos de movimientos del periodo, agrupada por cuenta y rubro.

```sql
SELECT
    V_EZI_CUENTAS.CODIGO,
    V_EZI_CUENTAS.DESCRIPCION AS CUENTA,
    V_EZI_CUENTAS.CRubro,
    V_EZI_CUENTAS.NRubro,
    V_EZI_CUENTAS.CSubrubro1,
    V_EZI_CUENTAS.NSubrubro1,
    V_EZI_CUENTAS.CSubrubro2,
    V_EZI_CUENTAS.NSubrubro2,
    V_EZI_CUENTAS.CSubrubro3,
    V_EZI_CUENTAS.NSubrubro3,
    SUM(V_VALOR_.IMPORTE) AS DEBE_PERIODO,
    SUM(V_VALOR_1.IMPORTE) AS HABER_PERIODO,
    SUM(V_VALOR_.IMPORTE) - SUM(V_VALOR_1.IMPORTE) AS SALDO_PERIODO
FROM V_TRCONTABLE_
INNER JOIN V_ITEMCONTABLE_
    ON V_TRCONTABLE_.ITEMSTRANSACCION_ID = V_ITEMCONTABLE_.BO_PLACE_ID
INNER JOIN V_VALOR_
    ON V_ITEMCONTABLE_.DEBE_ID = V_VALOR_.ID
INNER JOIN V_VALOR_ AS V_VALOR_1
    ON V_ITEMCONTABLE_.HABER_ID = V_VALOR_1.ID
INNER JOIN V_EZI_CUENTAS
    ON V_ITEMCONTABLE_.REFERENCIA_ID = V_EZI_CUENTAS.ID
WHERE SUBSTRING(V_TRCONTABLE_.FECHAAPLICACION, 1, 8) >= '20260601'
  AND SUBSTRING(V_TRCONTABLE_.FECHAAPLICACION, 1, 8) <= '20260630'
  AND V_EZI_CUENTAS.CODIGO BETWEEN '0' AND '9'
  AND V_TRCONTABLE_.ESTADO = 'C'
GROUP BY
    V_EZI_CUENTAS.CODIGO,
    V_EZI_CUENTAS.DESCRIPCION,
    V_EZI_CUENTAS.CRubro,
    V_EZI_CUENTAS.NRubro,
    V_EZI_CUENTAS.CSubrubro1,
    V_EZI_CUENTAS.NSubrubro1,
    V_EZI_CUENTAS.CSubrubro2,
    V_EZI_CUENTAS.NSubrubro2,
    V_EZI_CUENTAS.CSubrubro3,
    V_EZI_CUENTAS.NSubrubro3
ORDER BY V_EZI_CUENTAS.CODIGO
```

Nota: en implementacion real las fechas deben ser parametros controlados generados por el middleware, no texto libre ingresado por IA.

## 6. Consulta candidata - Mayor detallado acotado

Esta consulta sirve para navegar desde una cuenta/alerta al detalle de movimientos.

```sql
SELECT TOP 200
    '2-Mov.del Periodo' AS TIPO,
    V_TRCONTABLE_.NUMERODOCUMENTO,
    V_TRCONTABLE_.NOMBRE,
    SUM(V_VALOR_.IMPORTE) AS DEBE,
    SUM(V_VALOR_1.IMPORTE) AS HABER,
    0 AS SALDO,
    V_TRCONTABLE_.FECHAACTUAL,
    SUBSTRING(V_TRCONTABLE_.FECHAACTUAL, 1, 4) AS ANIO,
    SUBSTRING(V_TRCONTABLE_.FECHAACTUAL, 5, 2) AS MES,
    SUBSTRING(V_TRCONTABLE_.FECHAACTUAL, 7, 2) AS DIA,
    V_TRCONTABLE_.FECHAAPLICACION,
    COALESCE(V_EZI_ASIENTOS_DETALLES.DETALLE, V_TRCONTABLE_.DETALLE) AS DETALLE,
    V_TRCONTABLE_.VALORTOTAL AS TOTAL,
    -SUM(V_VALOR_1.IMPORTE) AS RESTAHABER,
    V_ITEMCONTABLE_.DESCRIPCION,
    V_EZI_CUENTAS.CODIGO,
    V_TRCONTABLE_.NOMBREDESTINATARIO AS PROVEEDOR,
    V_CENTROCOSTOS_.NOMBRE AS CC,
    V_EZI_ASIENTOS_DETALLES.TC
FROM V_TRCONTABLE_
INNER JOIN V_ITEMCONTABLE_
    ON V_TRCONTABLE_.ITEMSTRANSACCION_ID = V_ITEMCONTABLE_.BO_PLACE_ID
INNER JOIN V_VALOR_
    ON V_ITEMCONTABLE_.DEBE_ID = V_VALOR_.ID
INNER JOIN V_VALOR_ AS V_VALOR_1
    ON V_ITEMCONTABLE_.HABER_ID = V_VALOR_1.ID
INNER JOIN V_EZI_CUENTAS
    ON V_ITEMCONTABLE_.REFERENCIA_ID = V_EZI_CUENTAS.ID
LEFT OUTER JOIN V_EZI_ASIENTOS_DETALLES
    ON V_TRCONTABLE_.ID = V_EZI_ASIENTOS_DETALLES.ID
LEFT OUTER JOIN V_CENTROCOSTOS_
    ON V_ITEMCONTABLE_.CENTROCOSTOS_ID = V_CENTROCOSTOS_.ID
WHERE SUBSTRING(V_TRCONTABLE_.FECHAAPLICACION, 1, 8) >= '20260601'
  AND SUBSTRING(V_TRCONTABLE_.FECHAAPLICACION, 1, 8) <= '20260630'
  AND V_EZI_CUENTAS.CODIGO = 'CODIGO_CUENTA_VALIDADO'
  AND V_TRCONTABLE_.ESTADO = 'C'
GROUP BY
    V_TRCONTABLE_.NUMERODOCUMENTO,
    V_TRCONTABLE_.NOMBRE,
    V_TRCONTABLE_.FECHAACTUAL,
    V_TRCONTABLE_.FECHAAPLICACION,
    V_TRCONTABLE_.VALORTOTAL,
    V_ITEMCONTABLE_.DESCRIPCION,
    V_EZI_CUENTAS.CODIGO,
    COALESCE(V_EZI_ASIENTOS_DETALLES.DETALLE, V_TRCONTABLE_.DETALLE),
    V_TRCONTABLE_.NOMBREDESTINATARIO,
    V_CENTROCOSTOS_.NOMBRE,
    V_EZI_ASIENTOS_DETALLES.TC
ORDER BY V_TRCONTABLE_.FECHAAPLICACION DESC
```

## 7. Consulta OC/facturas - observacion

La consulta de referencia:

```sql
WHERE IMPORTEFACTURA IS NOT NULL AND FLAG='Autorizada' OR FLAG='Impresa'
```

Debe corregirse por precedencia logica:

```sql
WHERE IMPORTEFACTURA IS NOT NULL
  AND (FLAG = 'Autorizada' OR FLAG = 'Impresa')
```

o:

```sql
WHERE IMPORTEFACTURA IS NOT NULL
  AND FLAG IN ('Autorizada', 'Impresa')
```

Sin parentesis, SQL Server evalua `AND` antes que `OR`, por lo que podria traer filas `FLAG='Impresa'` aunque `IMPORTEFACTURA` sea NULL.

## 8. Decision tecnica propuesta

Para el proyecto:

- Usar `V_TRCONTABLE_ + V_ITEMCONTABLE_ + V_VALOR_ + V_EZI_CUENTAS` como fuente primaria del snapshot.
- Usar `V_EZI_ASIENTOS_DETALLES` y `V_CENTROCOSTOS_` solo para drill-down / mayor detallado.
- Usar `V_EZI_ORDENES_COMPRA2` como fuente auxiliar de analisis de OC/facturas, bajo filtros de fecha/OC/proveedor.
- No usar `V_EZI_CR_MAYORCUENTAS2` como fuente principal hasta resolver rendimiento.

## 9. Preguntas abiertas

1. El snapshot esperado debe ser movimiento del periodo o saldo acumulado a fecha?
2. Para Sumas y Saldos gerencial, se necesita saldo inicial + debe + haber + saldo final?
3. El corte debe ser mensual, diario o ambos?
4. La unidad operativa debe separarse o consolidarse?
5. El codigo de cuenta incluye descripcion en el mismo campo; se debe separar codigo numerico y nombre?
6. Las cuentas de resultado se deben resetear por ejercicio o acumular por periodo?
7. Para mayores, el drill-down debe abrir por cuenta, proveedor, centro de costo, unidad operativa o documento?
8. `V_EZI_ORDENES_COMPRA2` se usara solo para explicar movimientos de proveedores/facturas o tambien para alertas?
