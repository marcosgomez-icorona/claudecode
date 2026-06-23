#!/bin/bash
# ═══════════════════════════════════════════════════════════════
# telegram-coordinator — Enviar notificación a Telegram
# Ingenio La Corona
#
# Uso: ./send.sh "mensaje" [parse_mode]
#   parse_mode: HTML (default) | Markdown
#   Requiere: TELEGRAM_BOT_TOKEN, TELEGRAM_CHAT_ID en entorno
# ═══════════════════════════════════════════════════════════════
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CONFIG_FILE="$SCRIPT_DIR/config.env"

# Cargar configuración si existe
if [ -f "$CONFIG_FILE" ]; then
  source "$CONFIG_FILE"
fi

# Validar credenciales
if [ -z "${TELEGRAM_BOT_TOKEN:-}" ]; then
  echo "❌ TELEGRAM_BOT_TOKEN no está configurado" >&2
  echo "   Editar $CONFIG_FILE y agregar TELEGRAM_BOT_TOKEN=..." >&2
  exit 1
fi

if [ -z "${TELEGRAM_CHAT_ID:-}" ]; then
  echo "❌ TELEGRAM_CHAT_ID no está configurado" >&2
  echo "   Editar $CONFIG_FILE y agregar TELEGRAM_CHAT_ID=..." >&2
  exit 1
fi

MESSAGE="${1:-}"
PARSE_MODE="${2:-HTML}"

if [ -z "$MESSAGE" ]; then
  echo "❌ Uso: $0 \"mensaje a enviar\" [HTML|Markdown]" >&2
  exit 1
fi

# Limitar longitud (Telegram max: 4096 chars)
MAX_LEN=4000
if [ ${#MESSAGE} -gt $MAX_LEN ]; then
  MESSAGE="${MESSAGE:0:$MAX_LEN}..."
fi

# Enviar via Telegram API
RESPONSE=$(curl -s -X POST "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/sendMessage" \
  -H "Content-Type: application/json" \
  -d "$(cat <<EOF
{
  "chat_id": "${TELEGRAM_CHAT_ID}",
  "text": "${MESSAGE}",
  "parse_mode": "${PARSE_MODE}",
  "disable_web_page_preview": true
}
EOF
)")

# Verificar respuesta
OK=$(echo "$RESPONSE" | python3 -c "import sys,json; print(json.load(sys.stdin).get('ok',False))" 2>/dev/null || echo "False")

if [ "$OK" = "True" ]; then
  echo "✅ Enviado: ${MESSAGE:0:80}..."
else
  echo "❌ Error al enviar: $RESPONSE" >&2
  exit 1
fi
