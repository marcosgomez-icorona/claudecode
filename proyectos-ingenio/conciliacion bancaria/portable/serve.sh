#!/bin/bash
# Serve el dashboard portable de Conciliación Bancaria
# Usa Python HTTP server (no requiere Node-RED para archivos estáticos)
# Los endpoints API requieren Node-RED corriendo

cd "$(dirname "$0")"
PORT=8080
echo "🌐 Sirviendo Conciliación Bancaria en http://localhost:$PORT"
echo "📡 Los datos requieren Node-RED corriendo (Cloud o LAN)"
echo ""
python3 -m http.server $PORT
