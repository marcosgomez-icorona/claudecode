#!/bin/bash
# serve.sh — Servidor HTTP local para desarrollo (WSL / Linux)
# Uso: bash serve.sh
# Abrir en navegador: http://localhost:8080

PORT=${1:-8080}
echo "============================================"
echo " Despachos Pendientes — Modo Desarrollo"
echo "============================================"
echo ""
echo "  URL: http://localhost:$PORT"
echo ""
echo "  Sin Node-RED → usa mock data"
echo "  Con Node-RED → configura config.js"
echo ""
echo "  Ctrl+C para detener"
echo "============================================"
echo ""

python3 -m http.server "$PORT"
