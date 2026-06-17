#!/bin/bash
# Servidor local para Dashboard Conciliacion Bancaria
# Abre http://localhost:8080 en el browser
cd "$(dirname "$0")"
echo "============================================"
echo " Dashboard Conciliacion Bancaria"
echo "============================================"
echo ""
echo " Abriendo servidor en http://localhost:8080"
echo " Cerrar con Ctrl+C"
echo ""
xdg-open http://localhost:8080 2>/dev/null || true
python3 -m http.server 8080 || python -m http.server 8080
