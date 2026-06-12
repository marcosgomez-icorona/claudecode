# Reglas iniciales de alertas

## Estado

Reglas candidatas para discutir con Administracion. No deben considerarse definitivas hasta validar con datos reales.

## Severidades

| Severidad | Uso |
| --- | --- |
| INFO | Cambio esperado o dato para seguimiento |
| MEDIA | Requiere revision administrativa |
| ALTA | Puede afectar informe gerencial o cierre |
| CRITICA | Requiere revision inmediata antes de distribuir informe |

## Reglas candidatas MVP

### ALTAS_CUENTAS

Detecta cuentas que aparecen en el snapshot actual y no estaban en el anterior.

- Severidad inicial: MEDIA.
- Requiere validar si el alta es esperada.

### BAJAS_CUENTAS

Detecta cuentas que estaban en el snapshot anterior y no aparecen en el actual.

- Severidad inicial: ALTA.
- Puede indicar cambio de layout, filtro distinto o cierre parcial.

### VARIACION_ABSOLUTA_ALTA

Detecta cuentas con variacion absoluta superior a un umbral.

- Umbral inicial sugerido: pendiente definir.
- Severidad inicial: MEDIA o ALTA segun cuenta.

### VARIACION_PORCENTUAL_ALTA

Detecta cuentas con variacion porcentual superior a un umbral.

- Umbral inicial sugerido: pendiente definir.
- Control: ignorar porcentaje cuando el saldo anterior sea cero o muy bajo.

### SALDO_SIGNO_INESPERADO

Detecta cuentas cuyo saldo queda deudor/acreedor contrario a lo esperado.

- Requiere catalogo de comportamiento esperado por cuenta.
- Severidad inicial: ALTA.

### CUENTA_CRITICA_CON_MOVIMIENTO

Detecta movimiento en cuentas marcadas como criticas.

- Requiere listado de cuentas criticas.
- Severidad inicial: ALTA.

### CUENTA_SIN_MOVIMIENTO

Detecta cuentas relevantes sin movimiento durante un periodo esperado.

- Requiere definir cuentas a observar.
- Severidad inicial: INFO o MEDIA.

## Estructura JSON sugerida

```json
{
  "rule_code": "VARIACION_ABSOLUTA_ALTA",
  "severity": "ALTA",
  "account_code": "pendiente",
  "account_name": "pendiente",
  "current_balance": 0,
  "previous_balance": 0,
  "absolute_delta": 0,
  "percent_delta": null,
  "message": "Variacion superior al umbral configurado",
  "requires_human_review": true
}
```

## Pendiente

- Definir umbrales por importe.
- Definir umbrales por porcentaje.
- Definir cuentas criticas.
- Definir cuentas que admiten alta variacion por zafra o cierres.
- Definir responsables de revision.
