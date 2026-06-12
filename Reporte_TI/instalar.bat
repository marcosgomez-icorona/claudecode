@echo off
REM Instala las dependencias de Python necesarias (solo se corre una vez).
echo Instalando dependencias...
python -m pip install --upgrade openpyxl python-pptx
if errorlevel 1 (
  echo.
  echo No se pudo instalar. Verifica que Python este instalado y en el PATH.
  pause
  exit /b 1
)
echo.
echo Listo. Ya puedes usar generar.bat para producir el reporte.
pause
