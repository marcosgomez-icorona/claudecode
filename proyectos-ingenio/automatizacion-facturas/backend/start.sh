#!/bin/bash
# ============================================================
#  Backend Automatización Facturas — Ingenio La Corona
#  Iniciar en Linux/WSL
# ============================================================

cd "$(dirname "$0")"

echo ""
echo "=========================================="
echo "  BACKEND FACTURAS — Iniciando..."
echo "=========================================="
echo "  Puerto: ${PORT:-3000}"
echo "  Health: http://localhost:${PORT:-3000}/api/health"
echo "=========================================="
echo ""

node --no-warnings src/server.js
