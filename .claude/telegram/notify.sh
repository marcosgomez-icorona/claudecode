#!/bin/bash
# ═══════════════════════════════════════════════════════════════
# telegram-coordinator — Helpers de notificación estandarizada
# Ingenio La Corona
#
# Uso desde cualquier agente:
#   source .claude/telegram/notify.sh
#   notify_phase_complete 3 "solution-architect" "validador-cuit" "Diseño completado"
#   notify_error "backend-dev" "validador-cuit" "No se pudo conectar a SQL Server"
#   notify_merge "backend/validador-cuit" "feature" "3 archivos, 430 líneas"
#   notify_deploy_go "conciliacion" "Dashboard listo para produccion"
#   notify_approval_required "PR #1" "Listo para merge a main"
# ═══════════════════════════════════════════════════════════════

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SEND_SCRIPT="$SCRIPT_DIR/send.sh"

# ── Funciones de notificación ──

notify_phase_complete() {
  local phase="$1" agent="$2" project="$3" detail="${4:-}"
  local msg="✅ <b>Phase ${phase}/8 completada</b> — ${agent}
📋 Proyecto: ${project}"
  [ -n "$detail" ] && msg+="
📝 ${detail}"
  bash "$SEND_SCRIPT" "$msg" "HTML"
}

notify_error() {
  local agent="$1" project="$2" error="${3:-Error desconocido}"
  local msg="❌ <b>Error en ${agent}</b>
📋 Proyecto: ${project}
⚠️ ${error}

<i>Requiere intervención</i>"
  bash "$SEND_SCRIPT" "$msg" "HTML"
}

notify_merge() {
  local branch="$1" target="$2" stats="${3:-}"
  local msg="🔀 <b>Merge: ${branch} → ${target}</b>"
  [ -n "$stats" ] && msg+="
📊 ${stats}"
  bash "$SEND_SCRIPT" "$msg" "HTML"
}

notify_deploy_go() {
  local project="$1" detail="${2:-}"
  local msg="🟢 <b>GO — ${project}</b>"
  [ -n "$detail" ] && msg+="
✅ ${detail}"
  bash "$SEND_SCRIPT" "$msg" "HTML"
}

notify_deploy_nogo() {
  local project="$1" reason="${2:-}"
  local msg="🔴 <b>NO-GO — ${project}</b>"
  [ -n "$reason" ] && msg+="
❌ ${reason}"
  bash "$SEND_SCRIPT" "$msg" "HTML"
}

notify_approval_required() {
  local item="$1" detail="${2:-}"
  local msg="⚠️ <b>Aprobación requerida</b>: ${item}"
  [ -n "$detail" ] && msg+="
📋 ${detail}
<i>Usá /aprobar o /rechazar</i>"
  bash "$SEND_SCRIPT" "$msg" "HTML"
}

notify_daily_summary() {
  local active_projects="$1" active_agents="$2" blockages="$3"
  local msg="📊 <b>Resumen diario — $(date '+%d-%b %H:%M')</b>

<b>Proyectos activos:</b> ${active_projects}
<b>Agentes en vuelo:</b> ${active_agents}
<b>Bloqueos:</b> ${blockages:-0}"
  bash "$SEND_SCRIPT" "$msg" "HTML"
}

notify_info() {
  local message="$1"
  bash "$SEND_SCRIPT" "ℹ️ ${message}" "HTML"
}

# ── Procesar outbox ──
process_outbox() {
  local outbox_dir="$SCRIPT_DIR/outbox"
  local sent_dir="$SCRIPT_DIR/sent"

  for msg_file in "$outbox_dir"/*.json 2>/dev/null; do
    [ -f "$msg_file" ] || continue

    # Leer mensaje del archivo JSON
    local msg_text=$(python3 -c "
import json
with open('$msg_file') as f:
    d = json.load(f)
print(d.get('message','')[:4096])
" 2>/dev/null)

    if [ -n "$msg_text" ]; then
      bash "$SEND_SCRIPT" "$msg_text" "HTML" && {
        mv "$msg_file" "$sent_dir/"
      }
    fi
  done
}

# Si se ejecuta directamente, procesar outbox
if [ "${BASH_SOURCE[0]}" = "${0}" ]; then
  process_outbox
fi
