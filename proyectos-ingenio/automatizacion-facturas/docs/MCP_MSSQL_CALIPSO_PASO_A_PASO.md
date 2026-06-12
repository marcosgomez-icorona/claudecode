# Guia paso a paso - MCP MSSQL Calipso readonly

## Objetivo

Dejar funcionando un Node.js MCP Server para consultar SQL Server Calipso en modo readonly, sobre base TEST o copia restaurada, para entender la logica de registracion de facturas sin escribir directo en el ERP.

## Regla de trabajo

Avanzar por checkpoints. No pasar al siguiente bloque hasta que el responsable confirme que el punto anterior quedo verificado.

## Supuestos y contexto

- Base objetivo inicial: `CALIPSO_TEST` o copia restaurada.
- SQL Server compatible con 2008 R2.
- Usuario SQL dedicado readonly.
- Sin credenciales reales en git.
- No produccion salvo autorizacion explicita.
- El MCP solo sirve para analisis, no para registrar facturas.

## Checkpoint 1 - Confirmar entorno

Confirmar:

- Nombre o IP del SQL Server.
- Puerto SQL Server, normalmente `1433`.
- Nombre exacto de la base TEST.
- Schema inicial permitido, normalmente `dbo`.
- Desde que PC/VM se ejecutara Codex o el MCP.

Respuesta esperada:

```text
CONFIRMO CHECKPOINT 1
Servidor: ...
Base test: ...
Schema: ...
Equipo donde corre MCP: ...
```

## Checkpoint 2 - Crear usuario readonly SQL Server

Editar y ejecutar:

```text
sql/11_usuario_mcp_calipso_sqlserver_readonly.sql
```

Ajustar antes:

- `[CALIPSO_TEST]`
- `usr_mcp_calipso_readonly`
- password segura local

Validar:

```sql
USE [CALIPSO_TEST];

SELECT name, type_desc
FROM sys.database_principals
WHERE name = 'usr_mcp_calipso_readonly';

EXECUTE AS USER = 'usr_mcp_calipso_readonly';
SELECT TOP 1 name FROM sys.tables;
REVERT;
```

Respuesta esperada:

```text
CONFIRMO CHECKPOINT 2
Usuario readonly creado y SELECT TOP 1 funciona.
```

## Checkpoint 3 - Instalar Node.js si falta

En esta sesion `node` no esta disponible en PATH. Instalar Node.js LTS en el equipo donde correra el MCP.

Validar:

```powershell
node -v
npm -v
```

Respuesta esperada:

```text
CONFIRMO CHECKPOINT 3
node -v: ...
npm -v: ...
```

## Checkpoint 4 - Instalar dependencias del MCP

Ejecutar:

```powershell
cd C:\claudecode\proyectos-ingenio\automatizacion-facturas\mcp-calipso-sqlserver
npm install
```

Validar que se cree `node_modules` y no haya errores.

Respuesta esperada:

```text
CONFIRMO CHECKPOINT 4
npm install finalizo sin errores.
```

## Checkpoint 5 - Configurar .env local

Copiar:

```powershell
Copy-Item .env.example .env
```

Editar `.env`:

```env
MSSQL_SERVER=IP_O_SERVIDOR
MSSQL_PORT=1433
MSSQL_DATABASE=CALIPSO_TEST
MSSQL_USER=usr_mcp_calipso_readonly
MSSQL_PASSWORD=PASSWORD_LOCAL
MSSQL_ENCRYPT=false
MSSQL_TRUST_SERVER_CERTIFICATE=true
MSSQL_REQUEST_TIMEOUT_MS=30000
MSSQL_MAX_ROWS=200
MSSQL_ALLOWED_SCHEMAS=dbo
MSSQL_LOG_DIR=./logs
```

No compartir el contenido real del `.env`.

Respuesta esperada:

```text
CONFIRMO CHECKPOINT 5
.env creado localmente.
```

## Checkpoint 6 - Validar arranque del servidor MCP

Ejecutar:

```powershell
npm start
```

El proceso debe quedar esperando por stdio. Si falla, copiar solo el error tecnico sin password.

Respuesta esperada:

```text
CONFIRMO CHECKPOINT 6
npm start no muestra errores de arranque.
```

## Checkpoint 7 - Configurar Codex MCP

Agregar el servidor MCP a la configuracion local de Codex, adaptando rutas y variables:

```json
{
  "mcpServers": {
    "calipso-sqlserver": {
      "command": "node",
      "args": [
        "C:/claudecode/proyectos-ingenio/automatizacion-facturas/mcp-calipso-sqlserver/src/server.js"
      ],
      "env": {
        "MSSQL_SERVER": "IP_O_SERVIDOR",
        "MSSQL_PORT": "1433",
        "MSSQL_DATABASE": "CALIPSO_TEST",
        "MSSQL_USER": "usr_mcp_calipso_readonly",
        "MSSQL_PASSWORD": "PASSWORD_LOCAL",
        "MSSQL_ENCRYPT": "false",
        "MSSQL_TRUST_SERVER_CERTIFICATE": "true",
        "MSSQL_MAX_ROWS": "200",
        "MSSQL_ALLOWED_SCHEMAS": "dbo"
      }
    }
  }
}
```

Respuesta esperada:

```text
CONFIRMO CHECKPOINT 7
MCP agregado a la configuracion de Codex.
```

## Checkpoint 8 - Pruebas funcionales

Desde Codex, probar herramientas en este orden:

1. `healthcheck`
2. `list_tables`
3. `search_columns` con `fact`
4. `search_columns` con `prove`
5. `find_invoice_logic_candidates`

No ejecutar consultas amplias sobre tablas grandes. Empezar siempre por metadata.

Respuesta esperada:

```text
CONFIRMO CHECKPOINT 8
healthcheck/list_tables/search_columns funcionan.
```

## Checkpoint 9 - Analisis de registracion de facturas

Con el MCP operativo, reconstruir casos reales:

- factura con OC;
- factura con recepcion/remito;
- factura de servicio;
- factura con impuestos/percepciones;
- factura anulada o revertida.

Para cada caso documentar:

- tabla cabecera;
- tabla de items;
- proveedor;
- documento origen;
- documento destino;
- asiento contable;
- impuestos;
- estado/anulacion;
- IDs o claves de relacion.

## Riesgos y controles

- Riesgo: interpretar mal Calipso mirando tablas aisladas.
  Control: usar casos completos y trazabilidad documental.
- Riesgo: impacto en produccion.
  Control: base TEST y usuario readonly.
- Riesgo: consulta pesada.
  Control: `TOP`, `MSSQL_MAX_ROWS`, metadata primero.
- Riesgo: credenciales expuestas.
  Control: `.env` local y `.gitignore`.

## Entregables concretos al finalizar

- MCP operativo.
- Usuario SQL readonly validado.
- Log de auditoria generado.
- Primer mapa de tablas candidatas.
- Documento posterior de logica OC -> recepcion -> factura -> CxP -> asiento.
- Skill `skill-mcp-mssql` disponible para futuras sesiones.
