📊 **Calificación del Prompt: 8.7/10 🚀**

🛠 **Mejoras necesarias:**

* El prompt adjunto está muy bien orientado a **planificación**, pero todavía no está optimizado para **Codex**, porque Codex funciona mejor cuando recibe tareas accionables, verificables y divididas por fases técnicas.
* Falta indicar cómo debe explorar el repositorio, qué archivos debe crear o modificar, cómo validar el resultado y cuándo detenerse.
* Falta una definición clara de **MVP implementable**, no solo planificado.
* Conviene separar el trabajo en **fases de ejecución para Codex**: análisis, scaffolding, frontend, Apps Script, Google Sheets, integración, pruebas y documentación.
* El prompt debe pedir commits o cambios pequeños, validación manual/técnica y reporte final de archivos modificados.
* OpenAI recomienda que los prompts para Codex incluyan pasos de validación, tareas enfocadas y definición clara de “done”. ([Desarrolladores de OpenAI][1])
* También se recomienda usar contexto específico del proyecto y, cuando aplique, instrucciones tipo `AGENTS.md` para guiar el trabajo dentro del repositorio. ([Desarrolladores de OpenAI][2])
* El contenido base del proyecto adjunto ya define alcance, restricciones, funcionalidades MVP, exclusiones y entregables, por lo que puede convertirse en un prompt de ejecución técnica para Codex. 

---

🎯 **Prompt Optimizado con RACS para CODEX — 10/10**

* **📌 Rol:**
  Actúa como un **Senior Full Stack Engineer especializado en MVPs rápidos, Google Apps Script, Google Sheets, HTML5, Bootstrap 5, JavaScript Vanilla y arquitectura web simple de bajo mantenimiento**.

También debes actuar como:

* Arquitecto técnico orientado a ejecución.
* Desarrollador frontend responsive.
* Desarrollador backend con Google Apps Script.
* Especialista en integración con Google Sheets.
* Revisor técnico de calidad.
* Implementador pragmático enfocado en publicar un MVP funcional en 5 a 6 días.

Tu prioridad es construir una solución **simple, mantenible, rápida de publicar y fácil de operar por una sola persona**.

---

* **⚡ Acción:**
  Implementa el MVP web **“Corona Mundial 2026”** para el **Ingenio La Corona**, siguiendo el alcance definido en el documento del proyecto.

Antes de escribir código, realiza una revisión breve del repositorio y propone un plan técnico de ejecución. Luego implementa el proyecto por fases pequeñas, verificables y fáciles de revisar.

Debes construir una aplicación web funcional con:

1. Frontend en **HTML5, Bootstrap 5 y JavaScript Vanilla**.
2. Backend MVP en **Google Apps Script**.
3. Persistencia en **Google Sheets**.
4. Pantallas públicas para home, registro, predicciones, rankings, reglamento y premios.
5. Panel administrativo simple para partidos, resultados y recalcular puntajes.
6. Lógica de puntajes y rankings.
7. Estructura lista para migración futura a **PHP + MySQL**, sin implementarla todavía.

No implementes funcionalidades fuera del MVP.

---

* **🌎 Contexto:**

## Proyecto

Nombre: **Corona Mundial 2026**
Empresa: **Ingenio La Corona**
Participantes estimados: **50 a 100 personas**
Acceso: **Internet pública**
Hosting previsto: **[www.ingeniolacorona.com](http://www.ingeniolacorona.com)**

## Objetivo del MVP

Crear una plataforma web gamificada basada en el Mundial FIFA 2026 para uso interno del personal del Ingenio La Corona.

La plataforma debe permitir:

* Registro de participantes.
* Carga de predicciones.
* Visualización de ranking individual.
* Visualización de ranking por área.
* Administración manual de partidos y resultados.
* Cálculo automático de puntajes.
* Publicación de reglamento y premios.

## Restricción crítica

El MVP debe poder construirse con:

* 1 desarrollador.
* 4 horas diarias.
* Máximo 24 horas efectivas.
* Objetivo de publicación en 5 a 6 días.

Toda funcionalidad que comprometa ese plazo debe quedar fuera del MVP y documentarse como versión 2.0.

## Stack obligatorio

Frontend:

* HTML5.
* Bootstrap 5.
* JavaScript Vanilla.

Backend MVP:

* Google Apps Script.
* Google Sheets.

Backend futuro:

* PHP.
* MySQL.

## Identidad visual

Usar una estética inspirada en:

* Mundial FIFA 2026.
* Industria azucarera.
* Competencia sana.
* Espíritu de equipo.
* Innovación.
* Profesionalismo.

Colores:

* Primario: Rojo Corona.
* Secundarios: blanco, negro y gris claro.
* Acento: dorado estilo Copa Mundial.

Estilo visual:

* Moderno.
* Corporativo.
* Responsive.
* Dashboard ejecutivo.
* Motivador.
* Profesional.
* Fácil de usar.

## Áreas participantes

Usar estas áreas en el sistema:

* Gerencia
* Compras
* Sistemas
* RRHH
* Cuentas Corrientes
* Comercial
* Finanzas
* Control de Calidad
* Campo
* Fabricación
* Mecánica
* Instrumentación
* Eléctrica
* Infraestructura
* Mantenimiento
* Calderas
* Destilería

## Funcionalidades MVP obligatorias

### 1. Home

Debe incluir:

* Logo.
* Banner principal.
* Contador regresivo.
* Próximos partidos.
* Acceso a ranking.
* Acceso a registro.
* Acceso a reglamento.

### 2. Registro

Campos:

* Nombre y apellido.
* Correo electrónico.
* Área.
* Teléfono opcional.

Validaciones:

* Correo único.
* Campos obligatorios.
* Formato básico de email.

Persistencia:

* Google Sheets.

### 3. Predicciones

Permitir cargar:

* Ganador.
* Empate.
* Resultado exacto.

Reglas:

* Las predicciones son modificables hasta una fecha límite configurable.
* Después del cierre, deben quedar bloqueadas.
* Deben guardarse en Google Sheets.

### 4. Administración

Debe permitir:

* Crear partidos.
* Editar partidos.
* Cargar resultados.
* Recalcular puntajes.
* Actualizar rankings.

Debe ser extremadamente simple.

No implementar login complejo. Usar una protección mínima para MVP, por ejemplo:

* Ruta administrativa no pública.
* Clave simple configurable.
* Validación básica del lado backend.

### 5. Ranking Individual

Mostrar:

* Posición.
* Participante.
* Área.
* Puntaje.

Los primeros puestos deben verse como podio.

### 6. Ranking por Área

Calcular:

* Promedio de puntos de los mejores 5 participantes de cada área.

Mostrar:

* Posición.
* Área.
* Puntaje.

### 7. Reglamento

Página editable desde Google Sheets o configuración simple.

### 8. Premios

Página editable desde Google Sheets o configuración simple.

## Sistema de puntajes

Aplicar estas reglas:

* Resultado exacto: 5 puntos.
* Ganador correcto: 2 puntos.
* Empate correcto: 2 puntos.
* Campeón correcto: 20 puntos.
* Subcampeón correcto: 10 puntos.

## Hojas requeridas en Google Sheets

Diseñar y usar estas hojas:

1. Participantes
2. Partidos
3. Predicciones
4. Resultados
5. Puntajes
6. Ranking Individual
7. Ranking Áreas
8. Configuración

Cada hoja debe tener columnas claras, IDs, timestamps y campos suficientes para operar el MVP.

## Exclusiones del MVP

No implementar:

* Login con contraseña.
* Recuperación de contraseña.
* OAuth.
* API FIFA.
* Integraciones externas.
* WhatsApp.
* Emails automáticos.
* Aplicación móvil.
* PWA.
* Notificaciones push.
* Dashboard avanzado.
* Estadísticas avanzadas.
* Auditoría.
* Roles complejos.
* Historial de cambios.
* Comentarios.
* Chat interno.
* Gamificación avanzada.
* Multiidioma.

Si alguna de estas funciones aparece como necesaria, documentarla en `VERSION_2.md`, pero no implementarla.

---

* **📤 Salida:**

Trabaja como Codex dentro del repositorio y entrega una implementación lista para revisión.

## 1. Primer paso obligatorio: exploración del repositorio

Antes de modificar archivos:

1. Inspecciona la estructura actual del proyecto.
2. Identifica si ya existen archivos HTML, CSS, JS, Apps Script o documentación.
3. Detecta convenciones existentes.
4. Informa brevemente qué encontraste.
5. Propón el plan de implementación en fases.

No hagas cambios masivos sin explicar la estrategia.

---

## 2. Estructura esperada del proyecto

Si el repositorio está vacío o no tiene estructura clara, crea una estructura simple similar a esta:

```text
/
├── README.md
├── AGENTS.md
├── VERSION_2.md
├── docs/
│   ├── arquitectura.md
│   ├── modelo-datos.md
│   ├── flujo-operativo.md
│   └── criterios-aceptacion.md
├── frontend/
│   ├── index.html
│   ├── registro.html
│   ├── predicciones.html
│   ├── ranking.html
│   ├── ranking-areas.html
│   ├── reglamento.html
│   ├── premios.html
│   ├── admin.html
│   ├── assets/
│   │   ├── css/
│   │   │   └── styles.css
│   │   ├── js/
│   │   │   ├── config.js
│   │   │   ├── api.js
│   │   │   ├── app.js
│   │   │   ├── registro.js
│   │   │   ├── predicciones.js
│   │   │   ├── ranking.js
│   │   │   └── admin.js
│   │   └── img/
│   │       └── .gitkeep
└── apps-script/
    ├── Code.gs
    ├── SheetsService.gs
    ├── ParticipantsService.gs
    ├── MatchesService.gs
    ├── PredictionsService.gs
    ├── ScoresService.gs
    ├── RankingsService.gs
    ├── AdminService.gs
    └── appsscript.json
```

Puedes ajustar esta estructura si el repositorio ya tiene otra organización, pero mantén simplicidad.

---

## 3. Archivos de documentación obligatorios

Crea o actualiza:

### `README.md`

Debe incluir:

* Descripción del proyecto.
* Stack técnico.
* Cómo configurar Google Sheets.
* Cómo desplegar Google Apps Script.
* Cómo configurar el endpoint del frontend.
* Cómo probar el MVP.
* Alcance MVP.
* Exclusiones MVP.

### `AGENTS.md`

Debe incluir instrucciones para futuros trabajos con Codex:

* Stack permitido.
* Reglas de simplicidad.
* Qué no implementar en MVP.
* Convenciones de nombres.
* Criterios de validación.
* Cómo documentar cambios.

### `VERSION_2.md`

Debe listar mejoras futuras:

* Login seguro.
* MySQL + PHP.
* Roles.
* Auditoría.
* Emails.
* WhatsApp.
* Dashboard avanzado.
* Estadísticas.
* API FIFA.
* PWA.
* Notificaciones.

### `docs/modelo-datos.md`

Debe documentar todas las hojas de Google Sheets con:

* Nombre de hoja.
* Objetivo.
* Columnas.
* Tipo de dato.
* Ejemplo.
* Observaciones.

### `docs/criterios-aceptacion.md`

Debe definir cuándo el MVP está listo para publicar.

---

## 4. Modelo de datos mínimo requerido

Implementa o documenta estas hojas con columnas sugeridas:

### Participantes

* participante_id
* nombre_apellido
* email
* area
* telefono
* fecha_registro
* estado

### Partidos

* partido_id
* fase
* equipo_a
* equipo_b
* fecha_partido
* fecha_limite_prediccion
* estado

### Predicciones

* prediccion_id
* participante_id
* partido_id
* email
* ganador_predicho
* goles_a_predicho
* goles_b_predicho
* es_empate_predicho
* fecha_prediccion
* fecha_actualizacion
* estado

### Resultados

* resultado_id
* partido_id
* goles_a_real
* goles_b_real
* ganador_real
* es_empate_real
* fecha_carga
* cargado_por

### Puntajes

* puntaje_id
* participante_id
* partido_id
* puntos_resultado
* puntos_extra
* puntos_total
* fecha_calculo

### Ranking Individual

* posicion
* participante_id
* nombre_apellido
* area
* puntaje_total
* resultados_exactos
* ganadores_correctos
* fecha_actualizacion

### Ranking Áreas

* posicion
* area
* cantidad_participantes
* puntaje_promedio_top5
* puntaje_total_top5
* fecha_actualizacion

### Configuración

* clave
* valor
* descripcion
* fecha_actualizacion

---

## 5. Reglas técnicas de implementación

Implementa con estas reglas:

1. Usar JavaScript Vanilla, sin frameworks frontend.
2. Usar Bootstrap 5 por CDN, salvo que el repositorio indique otra cosa.
3. Centralizar la URL del endpoint de Apps Script en `frontend/assets/js/config.js`.
4. Centralizar llamadas HTTP en `frontend/assets/js/api.js`.
5. Manejar estados de carga, éxito, error y vacío.
6. Validar formularios en frontend y backend.
7. No confiar solo en validaciones frontend.
8. Usar IDs únicos simples y consistentes.
9. Usar timestamps en operaciones importantes.
10. Mantener funciones pequeñas, legibles y fáciles de modificar.
11. Evitar dependencias innecesarias.
12. No implementar autenticación compleja.
13. No integrar servicios externos.
14. No usar API FIFA.
15. No agregar funcionalidades fuera del alcance.

---

## 6. Reglas de negocio a implementar

Implementa la lógica para:

### Registro

* Validar nombre, email y área.
* Rechazar emails duplicados.
* Guardar participante en Google Sheets.
* Retornar mensajes claros al frontend.

### Predicciones

* Validar participante existente por email o participante_id.
* Validar partido existente.
* Validar que la fecha límite no haya pasado.
* Permitir crear o actualizar predicción antes del cierre.
* Bloquear cambios posteriores al cierre.

### Resultados

* Permitir cargar resultado real de un partido.
* Determinar ganador real o empate.
* Guardar resultado.
* Recalcular puntajes asociados.

### Puntajes

Calcular:

* 5 puntos si el resultado exacto coincide.
* 2 puntos si el ganador coincide.
* 2 puntos si el empate coincide.
* 0 puntos si no coincide.

Evitar doble conteo ambiguo. Si el resultado exacto coincide, asignar 5 puntos como total principal para ese partido, no 5 + 2.

### Ranking Individual

* Sumar puntos por participante.
* Ordenar de mayor a menor puntaje.
* Aplicar desempates simples:

  1. Mayor cantidad de resultados exactos.
  2. Mayor cantidad de ganadores correctos.
  3. Fecha de registro más antigua.
  4. Orden alfabético.

### Ranking por Área

* Agrupar participantes por área.
* Tomar los mejores 5 participantes de cada área.
* Calcular promedio de puntos del Top 5.
* Ordenar de mayor a menor.

---

## 7. Pantallas a implementar

Implementa interfaces simples y responsive para:

### `index.html`

Debe mostrar:

* Logo o placeholder del logo.
* Hero principal.
* Contador regresivo.
* Próximos partidos.
* Botones a Registro, Ranking y Reglamento.
* Estética Corona Mundial 2026.

### `registro.html`

Debe incluir:

* Formulario de registro.
* Select de áreas.
* Validaciones.
* Mensajes de éxito/error.

### `predicciones.html`

Debe incluir:

* Identificación del participante por email.
* Lista de partidos disponibles.
* Formulario para cargar predicción.
* Estado de predicción abierta/cerrada.
* Mensajes claros.

### `ranking.html`

Debe incluir:

* Podio Top 3.
* Tabla de ranking individual.
* Estado vacío.

### `ranking-areas.html`

Debe incluir:

* Tabla de ranking por área.
* Explicación breve del cálculo Top 5.
* Estado vacío.

### `reglamento.html`

Debe mostrar contenido editable desde configuración o fallback estático.

### `premios.html`

Debe mostrar contenido editable desde configuración o fallback estático.

### `admin.html`

Debe incluir:

* Acceso simple con clave básica configurable.
* Crear/editar partidos.
* Cargar resultados.
* Botón para recalcular puntajes.
* Botón para actualizar rankings.
* Mensajes de operación.

---

## 8. Diseño visual requerido

Aplicar un diseño:

* Corporativo.
* Moderno.
* Responsive.
* Con Bootstrap 5.
* Con tarjetas, badges y tablas limpias.
* Con acento dorado para premios, podio y posiciones destacadas.
* Con rojo institucional como color principal.
* Con navegación simple.
* Con estados claros: cargando, error, vacío, éxito.

No usar animaciones complejas. Solo transiciones suaves si no agregan complejidad.

---

## 9. Validación y pruebas

Al terminar cada fase, valida lo implementado.

Si hay entorno disponible, ejecuta pruebas o comandos pertinentes. Si no hay entorno de ejecución, documenta pruebas manuales.

Debes verificar como mínimo:

1. Registro correcto de participante.
2. Rechazo de email duplicado.
3. Carga de partidos desde backend o fallback.
4. Carga o actualización de predicción antes del cierre.
5. Bloqueo de predicción después del cierre.
6. Carga de resultado real.
7. Cálculo correcto de puntajes.
8. Ranking individual ordenado correctamente.
9. Ranking por área usando promedio Top 5.
10. Visualización correcta en móvil y escritorio.

---

## 10. Forma de trabajo para Codex

Trabaja en fases pequeñas:

### Fase 1 — Exploración y plan

* Inspeccionar repositorio.
* Identificar archivos existentes.
* Proponer plan.
* No modificar todavía si el repositorio ya tiene estructura compleja.

### Fase 2 — Documentación base

* Crear `README.md`.
* Crear `AGENTS.md`.
* Crear `VERSION_2.md`.
* Crear docs técnicos.

### Fase 3 — Frontend base

* Crear estructura HTML.
* Crear estilos.
* Crear navegación.
* Crear configuración.
* Crear servicios API frontend.

### Fase 4 — Apps Script backend

* Crear endpoints.
* Crear servicios por dominio.
* Implementar conexión a Google Sheets.
* Implementar validaciones.

### Fase 5 — Integración

* Conectar formularios con backend.
* Conectar rankings.
* Conectar administración.

### Fase 6 — Lógica de puntajes

* Implementar cálculo.
* Implementar rankings.
* Validar reglas de negocio.

### Fase 7 — Revisión final

* Revisar consistencia.
* Documentar pruebas.
* Reportar archivos creados/modificados.
* Indicar pendientes reales.

---

## 11. Condiciones de finalización

Considera la tarea completa solo cuando:

* El frontend básico esté creado.
* El backend Apps Script esté creado.
* El modelo de Google Sheets esté documentado.
* Las reglas de negocio estén implementadas o claramente preparadas.
* El README explique cómo configurar y desplegar.
* El alcance MVP esté respetado.
* Las exclusiones estén documentadas en `VERSION_2.md`.
* Exista una lista clara de pruebas manuales.
* Se informe qué archivos fueron creados o modificados.

---

## 12. Reporte final obligatorio

Al finalizar, entrega un resumen con:

1. Qué se implementó.
2. Qué archivos se crearon o modificaron.
3. Cómo configurar Google Sheets.
4. Cómo desplegar Google Apps Script.
5. Cómo configurar el endpoint en el frontend.
6. Cómo probar el MVP.
7. Qué quedó fuera del MVP y por qué.
8. Riesgos o pendientes reales.

No incluyas bloques extensos de código en la respuesta final. El código debe quedar en los archivos del repositorio.

---

## 13. Criterio principal de éxito

El resultado debe permitir publicar rápidamente un MVP funcional de **Corona Mundial 2026** que:

* Registre participantes.
* Guarde predicciones.
* Administre partidos y resultados.
* Calcule puntajes.
* Muestre ranking individual.
* Muestre ranking por área.
* Tenga estética profesional del Ingenio La Corona.
* Sea simple de mantener.
* No exceda el alcance de 24 horas efectivas de desarrollo.

---

✅ **Prompt listo para usar en Codex.**

[1]: https://developers.openai.com/codex/prompting?utm_source=chatgpt.com "Prompting – Codex | OpenAI Developers"
[2]: https://developers.openai.com/codex/guides/agents-md?utm_source=chatgpt.com "Custom instructions with AGENTS.md – Codex | OpenAI Developers"
