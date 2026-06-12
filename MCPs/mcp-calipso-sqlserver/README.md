# MCP Calipso SQL Server Readonly

Servidor MCP Node.js para explorar una base SQL Server de Calipso en modo lectura.

## Objetivo

Permitir que Codex consulte metadata y casos reales de facturas en Calipso sin escribir en tablas del ERP. El uso previsto es una base `TEST` o una copia restaurada, con usuario `readonly`.

## Instalacion

```powershell
cd mcp-calipso-sqlserver
npm install
Copy-Item .env.example .env
```

Editar `.env` con datos locales. No guardar credenciales reales en git.

## Ejecutar

```powershell
npm start
```

## Herramientas MCP

- `healthcheck`: verifica conexion y version SQL Server.
- `list_tables`: lista tablas por schema permitido.
- `search_columns`: busca columnas/tablas por texto.
- `describe_table`: columnas e indices basicos.
- `sample_table`: muestra limitada sin filtros libres.
- `run_readonly_query`: `SELECT` controlado con `TOP` automatico.
- `find_invoice_logic_candidates`: candidatos para reconstruir factura, OC, recepcion, impuestos y asiento.

## Configuracion Codex MCP

Ejemplo conceptual:

```json
{
  "mcpServers": {
    "calipso-sqlserver": {
      "command": "node",
      "args": [
        "C:/claudecode/proyectos-ingenio/automatizacion-facturas/mcp-calipso-sqlserver/src/server.js"
      ],
      "env": {
        "MSSQL_SERVER": "192.168.0.10",
        "MSSQL_PORT": "1433",
        "MSSQL_DATABASE": "CALIPSO_TEST",
        "MSSQL_USER": "usr_mcp_calipso_readonly",
        "MSSQL_PASSWORD": "REEMPLAZAR_EN_LOCAL",
        "MSSQL_ENCRYPT": "false",
        "MSSQL_TRUST_SERVER_CERTIFICATE": "true",
        "MSSQL_MAX_ROWS": "200",
        "MSSQL_ALLOWED_SCHEMAS": "dbo"
      }
    }
  }
}
```

## Controles

- Bloquea `INSERT`, `UPDATE`, `DELETE`, `MERGE`, `DROP`, `ALTER`, `CREATE`, `EXEC` y DDL/DML.
- `run_readonly_query` acepta solo una sentencia `SELECT`.
- Aplica `TOP` automatico si falta limite.
- Registra auditoria JSONL con `trace_id`.
- Permite restringir schemas por `MSSQL_ALLOWED_SCHEMAS`.

## Uso recomendado para Calipso

1. Conectar contra `CALIPSO_TEST`, no produccion.
2. Ejecutar `find_invoice_logic_candidates`.
3. Buscar columnas por terminos: `factura`, `proveedor`, `orden`, `recep`, `asiento`, `iva`.
4. Tomar 3 a 5 casos reales anonimizados.
5. Documentar flujo OC -> recepcion -> factura -> CxP -> asiento.
6. Recien despues disenar middleware de registracion.
7. Consultar `mcp-calipso-sqlserver/facturas-aprobacion-contexto.md` para el análisis del flujo de staging y registro de facturas aprobadas.

## Contextos reutilizables

Los analisis funcionales y tecnicos confirmados deben guardarse dentro de este MCP para que puedan reutilizarlos otros modelos y sesiones.

- `context/facturas-venta-azucar-alcohol.md`: fuentes validadas para remitos/despachos pendientes de facturar de azucar y alcohol, consultas base SQL Server 2008 R2, riesgos, arquitectura MVP y preguntas pendientes.

- `context/plan-middleware-facturacion-venta.md`: plan de interfaz, MySQL middleware, Node-RED, n8n, estados, outbox y controles para automatizacion de facturas de venta.

