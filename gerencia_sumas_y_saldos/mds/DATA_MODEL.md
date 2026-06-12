# Modelo de datos inicial

## Objetivo

Definir una base intermedia para guardar snapshots del Sumas y Saldos, filas normalizadas, alertas y auditoria sin tocar Calipso.

Actualizacion: el MVP pasa a usar MCP Calipso readonly como fuente primaria. La carga por archivo queda como fallback futuro.

## Tablas MVP candidatas

### sy_snapshots

Representa cada snapshot generado desde MCP readonly o, en fallback futuro, desde archivo.

| Campo | Tipo sugerido | Descripcion |
| --- | --- | --- |
| id | bigint auto increment | Identificador interno |
| uuid | char(36) | Trazabilidad unica |
| source_type | varchar(30) | MCP_CALIPSO, FILE_UPLOAD |
| source_filename | varchar(255) | Nombre del archivo cargado, si aplica |
| source_hash | char(64) | Hash SHA-256 del archivo o hash logico de consulta |
| period_from | date | Fecha desde del snapshot |
| period_to | date | Fecha hasta del snapshot |
| accounting_date | date | Fecha de corte contable, si aplica |
| fiscal_year | int | Ejercicio si aplica |
| fiscal_period | int | Periodo si aplica |
| requested_by | varchar(100) | Usuario/proceso que genero |
| created_at | datetime | Fecha/hora de generacion |
| status | varchar(30) | PENDING, VALIDATED, ERROR, PROCESSED |
| notes | text | Observaciones |

### sy_snapshot_rows

Filas normalizadas del snapshot.

| Campo | Tipo sugerido | Descripcion |
| --- | --- | --- |
| id | bigint auto increment | Identificador interno |
| snapshot_id | bigint | FK a sy_snapshots |
| row_number | int | Numero de fila resultado |
| account_code_full | varchar(255) | Codigo completo informado por Calipso |
| account_code | varchar(80) | Codigo de cuenta normalizado |
| account_name | varchar(255) | Nombre de cuenta |
| rubro_code | varchar(80) | Codigo rubro |
| rubro_name | varchar(255) | Nombre rubro |
| subrubro1_code | varchar(80) | Codigo subrubro 1 |
| subrubro1_name | varchar(255) | Nombre subrubro 1 |
| subrubro2_code | varchar(80) | Codigo subrubro 2 |
| subrubro2_name | varchar(255) | Nombre subrubro 2 |
| subrubro3_code | varchar(80) | Codigo subrubro 3 |
| subrubro3_name | varchar(255) | Nombre subrubro 3 |
| debit_period | decimal(18,2) | Debe del periodo |
| credit_period | decimal(18,2) | Haber del periodo |
| balance_period | decimal(18,2) | Saldo del periodo |
| opening_balance | decimal(18,2) | Reservado version posterior |
| closing_balance | decimal(18,2) | Reservado version posterior |
| raw_payload | text | JSON/texto con valores originales |

### sy_alerts

Alertas generadas por comparacion o validacion.

| Campo | Tipo sugerido | Descripcion |
| --- | --- | --- |
| id | bigint auto increment | Identificador interno |
| snapshot_id | bigint | Snapshot actual |
| previous_snapshot_id | bigint | Snapshot anterior usado |
| rule_code | varchar(80) | Codigo de regla |
| severity | varchar(20) | INFO, MEDIA, ALTA, CRITICA |
| account_code | varchar(80) | Cuenta asociada |
| account_name | varchar(255) | Nombre de cuenta |
| current_balance | decimal(18,2) | Saldo actual |
| previous_balance | decimal(18,2) | Saldo anterior |
| absolute_delta | decimal(18,2) | Variacion absoluta |
| percent_delta | decimal(18,6) | Variacion porcentual |
| message | text | Explicacion |
| status | varchar(30) | OPEN, REVIEWED, DISMISSED |
| created_at | datetime | Fecha/hora |

### sy_processing_logs

Auditoria tecnica del procesamiento.

| Campo | Tipo sugerido | Descripcion |
| --- | --- | --- |
| id | bigint auto increment | Identificador interno |
| trace_uuid | char(36) | UUID de proceso |
| snapshot_id | bigint | Snapshot relacionado |
| level | varchar(20) | INFO, WARN, ERROR |
| step | varchar(80) | Paso tecnico |
| message | text | Detalle |
| created_at | datetime | Fecha/hora |

## Decisiones pendientes

- Motor de base confirmado para MVP: MySQL.
- Schema inicial creado en `database/schema_mysql.sql`.
- `raw_payload` se guarda como `TEXT` para mantener compatibilidad amplia de MySQL.
- El fallback de archivo queda postergado; el schema conserva campos suficientes para incorporarlo luego.

## Tablas finales MVP

- `sy_snapshots`: cabecera de cada snapshot MCP.
- `sy_snapshot_rows`: saldos/movimientos por cuenta dentro del snapshot.
- `sy_snapshot_comparisons`: cabecera de comparacion entre dos snapshots.
- `sy_comparison_rows`: diferencias por cuenta.
- `sy_alerts`: alertas generadas por reglas.
- `sy_processing_logs`: auditoria tecnica.

## Indices iniciales

- Periodo de snapshot: `period_from`, `period_to`.
- Cuenta: `account_code`, `account_code_full`.
- Comparacion unica: snapshot actual + snapshot anterior.
- Alertas por severidad/estado.
- Logs por `trace_uuid`.
