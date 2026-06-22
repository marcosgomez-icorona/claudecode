# MCP Node-RED — Ingenio La Corona

MCP server para inspección, backup, validación y deploy controlado de flows Node-RED.

## Paquete

[`@jensrudolph/node-red-mcp-server`](https://github.com/jensrudolph/node-red-mcp-server) v1.3.4 — Node-RED Admin API vía MCP.

## Instalación

```bash
cd MCPs/mcp-nodered
npm install
```

## Configuración

El `.mcp.json` global apunta a esta carpeta local (`cwd`) y pasa las variables de entorno con el token y la URL.

```json
{
  "nodered": {
    "command": "node",
    "args": ["./node_modules/.bin/node-red-mcp", "--read-only"],
    "cwd": "/mnt/c/claudecode/MCPs/mcp-nodered",
    "env": {
      "NODE_RED_URL": "http://192.168.0.23:1880",
      "NODE_RED_TOKEN": "...",
      "MCP_READ_ONLY": "true"
    }
  }
}
```

## Refrescar token

El token Bearer expira **cada 7 días**. Para refrescarlo:

```bash
bash MCPs/mcp-nodered/refresh_token.sh
```

O manualmente:

```bash
curl -s -X POST http://192.168.0.23:1880/auth/token \
  -H "Content-Type: application/json" \
  -d '{"client_id":"node-red-admin","grant_type":"password","username":"admin","password":"2105","scope":"*"}'
```

Luego copiar el `access_token` a `.mcp.json` → `nodered.env.NODE_RED_TOKEN`.

## Modo lectura/escritura

Por defecto arranca en `--read-only`. Para permitir escritura:

1. Quitar `--read-only` de `args` en `.mcp.json`
2. Cambiar `MCP_READ_ONLY` a `false`
3. Cambiar `MCP_ALLOW_FULL_FLOW_WRITES` a `true`

⚠️ El MCP hace **backup automático** antes de cada mutación (`MCP_AUTO_BACKUP=true`).

## Herramientas disponibles

~30 herramientas: listar flows, inspeccionar nodos, buscar, backups, diff, validación, deploy controlado, simulación de function nodes, auditoría de entidades Home Assistant.

Para ver la lista completa: `mcp__nodered__api-help`.
