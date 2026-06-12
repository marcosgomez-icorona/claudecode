@echo off
REM Genera el reporte ejecutivo a partir de la plantilla en esta carpeta.
cd /d "%~dp0"
python generar_reporte.py Reporte_TI_Plantilla.xlsx
if errorlevel 1 (
  echo.
  echo Error al generar el reporte. Revisa que la plantilla este completa.
  pause
  exit /b 1
)
echo.
echo Reporte generado en esta misma carpeta.
pause
