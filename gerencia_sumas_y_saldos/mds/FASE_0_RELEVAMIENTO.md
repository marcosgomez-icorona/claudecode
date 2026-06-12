# FASE 0 - Relevamiento y definicion MVP

## 1. Objetivo

Cerrar la definicion funcional y tecnica minima para construir el MVP del Seguimiento Inteligente de Sumas y Saldos sin depender todavia de una conexion directa a Calipso.

La fase termina cuando existe un archivo ejemplo validado, un layout minimo acordado, reglas iniciales de alerta y una decision tecnica sobre la base intermedia y el stack del MVP.

## 2. Supuestos y contexto

- El Sumas y Saldos se exportara inicialmente desde Calipso en Excel o CSV.
- El archivo sera cargado manualmente por Sistemas o Administracion.
- No se consultara Calipso directo en esta etapa.
- No se escribira nada en Calipso en ninguna etapa.
- Las reglas automaticas se usaran primero como apoyo de analisis, no como decision contable final.

## 3. Diseno propuesto

La primera etapa se divide en cuatro bloques:

1. Relevamiento de fuente: identificar formato real del archivo, columnas, periodicidad y alcance.
2. Definicion de layout: fijar columnas obligatorias, opcionales y reglas de normalizacion.
3. Definicion de alertas: acordar umbrales iniciales y cuentas criticas.
4. Preparacion tecnica: elegir stack MVP y modelo de datos inicial.

## 4. Arquitectura tecnica

### Arquitectura de Fase 0

```text
Calipso
  -> Export manual Excel/CSV
  -> Archivo ejemplo anonimizado
  -> Validacion de layout
  -> Modelo de snapshot
  -> Reglas iniciales
  -> Backlog tecnico MVP
```

### Decision tecnica inicial recomendada

Para arrancar rapido y alineado al entorno actual:

- Backend MVP: PHP.
- UI MVP: Bootstrap.
- Base intermedia: MySQL.
- Parser: primero CSV; Excel si el formato real lo exige.
- Reporte inicial: HTML exportable/imprimible.

Alternativa si se prioriza integracion futura con MCP:

- Backend MVP: Node.js.
- Base intermedia: MySQL.
- Parser: libreria Node para CSV/XLSX.

Recomendacion: empezar con PHP + MySQL si el sistema va a convivir con herramientas web internas existentes. Usar Node.js solo si desde el inicio queremos reutilizar componentes del MCP y preparar automatizaciones mas tecnicas.

## 5. Flujo paso a paso

1. Administracion genera un Sumas y Saldos desde Calipso.
2. Sistemas anonimiza o depura datos sensibles si hace falta.
3. Se guarda el archivo ejemplo en una carpeta local fuera del repositorio o en `data/samples/` si esta anonimizado.
4. Se documentan columnas reales en `LAYOUT_SUMAS_SALDOS.md`.
5. Se marca cada columna como obligatoria, opcional o derivada.
6. Se definen las primeras reglas de alerta en `ALERT_RULES.md`.
7. Se define el modelo de tablas intermedias en `DATA_MODEL.md`.
8. Se actualiza el backlog con tareas de implementacion.

## 6. Riesgos y controles

- Riesgo: elegir mal el layout por usar un solo archivo. Control: pedir al menos dos cortes distintos.
- Riesgo: mezclar Sumas y Saldos de distintos alcances. Control: registrar empresa, periodo, ejercicio y fecha de corte.
- Riesgo: alertas irrelevantes por variaciones normales. Control: clasificar cuentas criticas y cuentas estacionales.
- Riesgo: datos sensibles en muestras. Control: usar ejemplos anonimizados o guardar fuera del repositorio.
- Riesgo: sobredisenar antes de validar. Control: MVP con importacion, snapshot, comparacion y reporte simple.

## 7. Proxima version

Cuando se confirme el layout real, la siguiente version debe crear:

- `data/samples/` para archivos anonimizados.
- `src/` con importador inicial.
- `database/` con schema MySQL.
- `docs/GUIA_OPERATIVA_MVP.md`.

## 8. Entregables concretos de Fase 0

- Archivo ejemplo anonimizado.
- Layout documentado.
- Matriz de decisiones tecnica.
- Reglas iniciales de alerta.
- Modelo de datos inicial.
- Backlog MVP listo para desarrollo.
- Preguntas funcionales respondidas por Administracion/Sistemas.
