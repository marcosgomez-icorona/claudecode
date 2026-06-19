@echo off
REM serve.bat — Servidor HTTP local para desarrollo (Windows)
REM Uso: serve.bat [puerto]
REM Abrir en navegador: http://localhost:8080

set PORT=%1
if "%PORT%"=="" set PORT=8080

echo ============================================
echo  Despachos Pendientes — Modo Desarrollo
echo ============================================
echo.
echo   URL: http://localhost:%PORT%
echo.
echo   Sin Node-RED → usa mock data
echo   Con Node-RED → configura config.js
echo.
echo   Ctrl+C para detener
echo ============================================
echo.

python -m http.server %PORT%
