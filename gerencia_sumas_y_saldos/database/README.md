# Database MVP

## Objetivo

Guardar snapshots del Sumas y Saldos extraidos por MCP Calipso readonly en una base intermedia MySQL.

## Archivo principal

- `schema_mysql.sql`: crea la base `gerencia_sumas_y_saldos` y las tablas MVP.

## Aplicacion manual

Ejecutar desde MySQL/MariaDB con un usuario con permisos sobre la base intermedia:

```sql
SOURCE C:/claudecode/gerencia_sumas_y_saldos/database/schema_mysql.sql;
```

O desde consola:

```powershell
mysql -u usuario -p < C:\claudecode\gerencia_sumas_y_saldos\database\schema_mysql.sql
```

## Controles

- Esta base no reemplaza ni modifica Calipso.
- No guardar credenciales en el repositorio.
- Usar un usuario MySQL propio del proyecto con permisos acotados a esta base.
- Respaldar antes de cambios estructurales cuando ya haya snapshots reales.
