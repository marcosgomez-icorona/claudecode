#!/bin/bash
# Refresca el token Bearer de Node-RED y actualiza .mcp.json
# Uso: bash .claude/scripts/refresh_nodered_token.sh
# El token expira cada 7 días (604800 segundos)

NODE_RED_URL="${NODE_RED_URL:-http://192.168.0.23:1880}"
NODE_RED_USER="${NODE_RED_USER:-admin}"
NODE_RED_PASS="${NODE_RED_PASS:-2105}"
MCP_JSON="/mnt/c/claudecode/.mcp.json"
NOW=$(date -Iseconds)

echo "🔐 Solicitando token Node-RED..."
RESP=$(curl -s -X POST "${NODE_RED_URL}/auth/token" \
  -H "Content-Type: application/json" \
  -d "{\"client_id\":\"node-red-admin\",\"grant_type\":\"password\",\"username\":\"${NODE_RED_USER}\",\"password\":\"${NODE_RED_PASS}\",\"scope\":\"*\"}")

TOKEN=$(echo "$RESP" | python3 -c "import sys,json; print(json.load(sys.stdin).get('access_token',''))" 2>/dev/null)
EXPIRES=$(echo "$RESP" | python3 -c "import sys,json; print(json.load(sys.stdin).get('expires_in',''))" 2>/dev/null)

if [ -z "$TOKEN" ]; then
  echo "❌ Error obteniendo token. Respuesta: $RESP"
  exit 1
fi

# Actualizar .mcp.json con el nuevo token
python3 -c "
import json, sys
with open('${MCP_JSON}', 'r') as f:
    config = json.load(f)
config['mcpServers']['nodered']['env']['NODE_RED_TOKEN'] = '${TOKEN}'
config['mcpServers']['nodered']['env']['NODE_RED_TOKEN_REFRESHED'] = '${NOW}'
with open('${MCP_JSON}', 'w') as f:
    json.dump(config, f, indent=2)
    f.write('\n')
print('✅ Token actualizado en .mcp.json')
print(f'⏰ Expira en {int(${EXPIRES})/86400:.0f} días (${EXPIRES}s)')
print(f'📅 Refrescado: ${NOW}')
"

echo ""
echo "⚠️  Reiniciar la sesión de Claude Code para que el nuevo token tome efecto."
