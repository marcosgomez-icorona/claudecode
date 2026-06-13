@echo off
REM ============================================================
REM  Backend Automatización Facturas — Ingenio La Corona
REM  Iniciar en Windows
REM ============================================================

cd /d "C:\claudecode\proyectos-ingenio\automatizacion-facturas\backend"

echo.
echo ==========================================
echo   BACKEND FACTURAS — Iniciando...
echo ==========================================
echo   Puerto: %PORT% (default 3000)
echo   Health: http://localhost:3000/api/health
echo ==========================================
echo.

node --no-warnings src/server.js
pause
