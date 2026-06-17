@echo off
REM =====================================================
REM Servidor local para Dashboard Conciliacion Bancaria
REM Abre http://localhost:8080 en el browser
REM =====================================================
cd /d "%~dp0"
echo ============================================
echo  Dashboard Conciliacion Bancaria
echo ============================================
echo.
echo  Abriendo servidor en http://localhost:8080
echo  Cerrar con Ctrl+C
echo.
start http://localhost:8080
python -m http.server 8080
pause
