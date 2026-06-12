# QUESTIONS - Preguntas necesarias

## Fuente de datos

1. El Sumas y Saldos se exporta hoy desde Calipso en Excel, CSV o ambos?
2. El archivo tiene siempre las mismas columnas?
3. Se puede compartir un ejemplo anonimizado de 2 o 3 periodos?
4. La fecha de corte viene dentro del archivo o la carga el usuario?
5. El reporte se emite por empresa, unidad operativa, ejercicio, periodo o todo consolidado?

## Modelo contable

1. Cuales son las columnas minimas: cuenta, descripcion, debe, haber, saldo deudor, saldo acreedor, saldo final?
2. Existe rubro, grupo, nivel de cuenta o plan jerarquico en el reporte?
3. Que cuentas son criticas para Administracion/Gerencia?
4. Hay cuentas que pueden variar mucho naturalmente por zafra, pagos, cierres o ajustes?
5. Como se definen saldos esperados, vencidos o "raros"?

## Alertas

1. Que variacion monetaria debe disparar alerta?
2. Que variacion porcentual debe disparar alerta?
3. Que cuentas deben revisarse aunque el importe sea bajo?
4. Que alertas deben ser solo informativas?
5. Quien valida y ajusta los umbrales durante el piloto?

## Operacion

1. Quien cargara el archivo?
2. Con que frecuencia: diaria, semanal, mensual o por cierre?
3. Quien recibe el informe ejecutivo?
4. El informe debe enviarse por Gmail, guardarse en Drive, descargarse localmente o todo lo anterior?
5. Se requiere aprobacion humana antes de distribuir informes?

## Tecnologia

1. Preferimos MVP en PHP por cercania al stack actual o Node.js por reutilizacion con MCP?
2. Base intermedia: MySQL local, PostgreSQL o SQLite para prototipo?
3. Donde correra el sistema: VM interna, PC de Sistemas, servidor web existente?
4. Hay autenticacion existente para reutilizar?
5. El MCP Calipso ya puede conectarse contra TEST/copia para consultas readonly?
