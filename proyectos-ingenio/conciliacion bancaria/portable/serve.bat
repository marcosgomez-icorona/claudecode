@echo off
REM Serve el dashboard portable de Conciliacion Bancaria
REM Los endpoints API requieren Node-RED corriendo (Cloud o LAN)
cd /d "%~dp0"
echo Sirviendo Conciliacion Bancaria en http://localhost:8080
echo Los datos requieren Node-RED corriendo
echo.
python -m http.server 8080
pause
