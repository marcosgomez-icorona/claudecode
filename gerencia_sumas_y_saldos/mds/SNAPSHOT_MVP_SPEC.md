# Snapshot MVP - Definicion para confirmar

## 1. Objetivo

Definir el alcance exacto del primer snapshot MCP readonly de Sumas y Saldos para construir el extractor sin ambiguedades.

## 2. Supuestos y contexto

- Fuente primaria: MCP Calipso readonly contra base `CORONA`.
- Vistas fuente: `V_TRCONTABLE_`, `V_ITEMCONTABLE_`, `V_VALOR_`, `V_EZI_CUENTAS`.
- El snapshot inicial representa movimientos del periodo, no saldo acumulado historico.
- El procesamiento se hara por rango acotado, recomendado mensual.
- No se ejecutara SQL libre desde la interfaz. El sistema solo permitira parametros controlados.

## 3. Alcance incluido

El primer snapshot incluye:

- Periodo desde/hasta en formato `YYYYMMDD`.
- Cuenta contable.
- Nombre de cuenta.
- Rubro y subrubros disponibles.
- Debe del periodo.
- Haber del periodo.
- Saldo del periodo: `debe - haber`.
- Estado contable confirmado: solo `V_TRCONTABLE_.ESTADO = 'C'`.
- Cuentas cuyo codigo este entre `'0'` y `'9'`.

## 4. Fuera de alcance inicial

No incluye todavia:

- Saldo inicial acumulado.
- Saldo final acumulado a fecha.
- Separacion obligatoria por unidad operativa.
- Separacion obligatoria por centro de costos.
- Drill-down automatico a OC/factura.
- Alertas definitivas.
- Escritura en Calipso.

## 5. Parametros permitidos

| Parametro | Obligatorio | Formato | Regla |
| --- | --- | --- | --- |
| fecha_desde | Si | YYYYMMDD | Debe ser fecha valida |
| fecha_hasta | Si | YYYYMMDD | Debe ser fecha valida y >= fecha_desde |
| modo | Si | mensual/manual | `mensual` recomendado |
| unidad_operativa | No | codigo interno controlado | Pendiente para version 2 |
| cuenta_desde | No | texto controlado | Default `'0'` |
| cuenta_hasta | No | texto controlado | Default `'9'` |

## 6. Columnas de salida normalizadas

| Campo | Origen | Descripcion |
| --- | --- | --- |
| account_code_full | `V_EZI_CUENTAS.CODIGO` | Codigo completo, actualmente incluye descripcion |
| account_code | derivado | Codigo numerico antes de ` - `, si aplica |
| account_name | `V_EZI_CUENTAS.DESCRIPCION` | Nombre de cuenta |
| rubro_code | `V_EZI_CUENTAS.CRubro` | Codigo rubro |
| rubro_name | `V_EZI_CUENTAS.NRubro` | Nombre rubro |
| subrubro1_code | `V_EZI_CUENTAS.CSubrubro1` | Codigo subrubro 1 |
| subrubro1_name | `V_EZI_CUENTAS.NSubrubro1` | Nombre subrubro 1 |
| subrubro2_code | `V_EZI_CUENTAS.CSubrubro2` | Codigo subrubro 2 |
| subrubro2_name | `V_EZI_CUENTAS.NSubrubro2` | Nombre subrubro 2 |
| subrubro3_code | `V_EZI_CUENTAS.CSubrubro3` | Codigo subrubro 3 |
| subrubro3_name | `V_EZI_CUENTAS.NSubrubro3` | Nombre subrubro 3 |
| debit_period | `SUM(V_VALOR_.IMPORTE)` | Debe del periodo |
| credit_period | `SUM(V_VALOR_1.IMPORTE)` | Haber del periodo |
| balance_period | calculado | `debit_period - credit_period` |

## 7. Consulta base aprobable

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
WHERE SUBSTRING(V_TRCONTABLE_.FECHAAPLICACION, 1, 8) >= @fecha_desde
  AND SUBSTRING(V_TRCONTABLE_.FECHAAPLICACION, 1, 8) <= @fecha_hasta
  AND V_EZI_CUENTAS.CODIGO BETWEEN @cuenta_desde AND @cuenta_hasta
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

## 8. Criterios de aceptacion

- El extractor rechaza fechas invalidas.
- El extractor rechaza rangos mayores a 31 dias en la primera version.
- La consulta se ejecuta solo con parametros controlados.
- El resultado guarda cantidad de cuentas y totales de debe/haber/saldo.
- Cada ejecucion queda trazada con UUID.
- El snapshot no modifica Calipso.

## 9. Confirmacion requerida

Para avanzar al Paso 2, confirmar:

1. Snapshot MVP = movimiento mensual del periodo.
2. Saldo inicial/final acumulado queda para version posterior.
3. Unidad operativa queda consolidada en primera version.
4. Base intermedia sera MySQL.
5. Desarrollo inicial sera Node.js por cercania al MCP.
