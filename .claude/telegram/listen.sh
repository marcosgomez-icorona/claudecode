#!/bin/bash
# ═══════════════════════════════════════════════════════════════
# telegram-listener — Polling de mensajes entrantes
# Ingenio La Corona
#
# Ciclo: cada 5 segundos, consulta getUpdates,
# escribe mensajes nuevos en inbox/
# ═══════════════════════════════════════════════════════════════
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CONFIG_FILE="$SCRIPT_DIR/config.env"
STATE_FILE="$SCRIPT_DIR/state.json"
INBOX_DIR="$SCRIPT_DIR/inbox"

# Cargar configuración
[ -f "$CONFIG_FILE" ] && source "$CONFIG_FILE"

if [ -z "${TELEGRAM_BOT_TOKEN:-}" ] || [ -z "${TELEGRAM_CHAT_ID:-}" ]; then
  echo "❌ Configurar TELEGRAM_BOT_TOKEN y TELEGRAM_CHAT_ID en config.env" >&2
  exit 1
fi

# Inicializar estado
if [ ! -f "$STATE_FILE" ]; then
  echo '{"last_update_id":0,"last_message_ts":"","messages_processed":0}' > "$STATE_FILE"
fi

LAST_UPDATE_ID=$(python3 -c "import json; print(json.load(open('$STATE_FILE')).get('last_update_id',0))")

echo "🔍 Telegram listener iniciado — chat_id: $TELEGRAM_CHAT_ID"
echo "   Esperando mensajes... (Ctrl+C para detener)"

IDLE_COUNT=0
MAX_IDLE=360  # 30 minutos de inactividad (360 * 5s = 1800s)

while true; do
  # Obtener updates
  UPDATES=$(curl -s "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/getUpdates?offset=${LAST_UPDATE_ID}&timeout=5&limit=5" 2>/dev/null)

  # Parsear mensajes nuevos
  NEW_MESSAGES=$(echo "$UPDATES" | python3 -c "
import sys, json, os
try:
    data = json.load(sys.stdin)
    updates = data.get('result', [])
    found = 0
    for upd in updates:
        msg = upd.get('message', {})
        chat = msg.get('chat', {})
        chat_id = str(chat.get('id', ''))
        authorized_chat = os.environ.get('TELEGRAM_CHAT_ID', '')
        text = msg.get('text', '')
        msg_id = msg.get('message_id', 0)

        if chat_id != authorized_chat:
            continue

        # Escribir en inbox
        ts = msg.get('date', 0)
        from_user = msg.get('from', {}).get('username', msg.get('from', {}).get('first_name', 'unknown'))
        inbox_file = os.path.join('$INBOX_DIR', f'{msg_id}_{ts}_{from_user}.json')

        inbox_msg = {
            'message_id': msg_id,
            'from': from_user,
            'text': text,
            'timestamp': ts,
            'chat_id': chat_id
        }

        with open(inbox_file, 'w') as f:
            json.dump(inbox_msg, f, ensure_ascii=False)

        found += 1
        # Actualizar último ID
        print(str(upd.get('update_id', 0)), end='')
        break
    if found == 0:
        print('0')
except Exception as e:
    print('0')
" 2>/dev/null)

  # Actualizar last_update_id
  if [ "$NEW_MESSAGES" != "0" ] && [ -n "$NEW_MESSAGES" ]; then
    NEW_ID=$((NEW_MESSAGES + 1))
    python3 -c "
import json
with open('$STATE_FILE','r') as f:
    state = json.load(f)
state['last_update_id'] = $NEW_ID
state['last_message_ts'] = '$(date -Iseconds)'
state['messages_processed'] = state.get('messages_processed',0) + 1
with open('$STATE_FILE','w') as f:
    json.dump(state, f, indent=2)
"
    IDLE_COUNT=0
  else
    IDLE_COUNT=$((IDLE_COUNT + 1))
  fi

  # Auto-detener tras inactividad
  if [ $IDLE_COUNT -ge $MAX_IDLE ]; then
    echo "⏹️ 30 min de inactividad — deteniendo listener"
    break
  fi

  sleep 2
done
