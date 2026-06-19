# Propuesta: Skills Oficiales y Confiables para Desarrollo Web, UI/UX y Git

**Fecha:** 2026-06-19
**Contexto:** Potenciar proyectos de Ingenio La Corona (dashboards portable, conciliación bancaria, facturas, sumas y saldos, molienda web, etc.)

---

## Resumen Ejecutivo

Actualmente el proyecto cuenta con **14 skills custom** desarrollados internamente para el stack Corona. Existe un ecosistema de plugins oficiales de Anthropic y skills comunitarios de alta calidad que pueden complementar y potenciar estos skills sin reemplazarlos. Se recomienda la instalación de **6 plugins oficiales Anthropic** y **2 skills comunitarios** probados.

---

## 1. Diagnóstico: Skills Actuales vs. Brechas

### Lo que ya tenemos (bien cubierto)

| Skill Local | Propósito |
|-------------|-----------|
| `dashboard-portable-corona` | Arquitectura de dashboards Corona |
| `html-css-avanzado` | Desarrollo HTML/CSS con paleta Corona |
| `javascript-moderno` | JS vanilla para frontend y Node-RED |
| `performance-web` | Optimización de dashboards |
| `testing-web` | Smoke tests de API |
| `ui-ux-reviewer` | Checklist de usabilidad gerencial |
| `frontend-bootstrap-reviewer` | Checklist Bootstrap + Corona |
| `git-workflow-ingenio` | Convenciones Git del ingenio |

### Brechas detectadas

| Área | Brecha |
|------|--------|
| **Diseño visual distintivo** | Los dashboards son funcionales pero genéricos visualmente |
| **Sistemas de diseño** | No hay un design system formal ni tokens |
| **Revisión automatizada de PRs** | No hay revisión multi-agente de cambios |
| **Workflow de features** | No hay un proceso estructurado feature→implementación→review |
| **Calidad web (perf, a11y, SEO)** | No hay auditoría automática de calidad web |
| **Testing visual/browser** | No hay captura de pantalla ni testing cross-browser |
| **Motion/animation** | No hay skills para animación |

---

## 2. Skills Recomendados — Priorizados

### PRIORIDAD ALTA — Impacto inmediato en proyectos activos

#### A. `anthropic/frontend-design` (Plugin Oficial Anthropic)

**Qué hace:** Guía a Claude para generar interfaces con identidad visual distintiva — tipografía intencional, paletas coherentes, layout con propósito, animación contextual. Evita la estética genérica de IA.

**Por qué para Corona:**
- Los dashboards activos (Sumas y Saldos, Conciliación, Facturas) se beneficiarían de identidad visual más pulida
- Trabaja con HTML/CSS vanilla — compatible con nuestro stack
- Es el skill oficial más valorado de Anthropic para frontend
- Sin dependencias, sin build tooling

**Instalación:**
```bash
claude plugin add anthropic/frontend-design
```

**Uso en proyectos Corona:**
- Refactor visual de dashboards existentes
- Nuevos paneles con identidad desde el inicio
- Consistencia entre proyectos (misma paleta, tipografía, espaciado)

**Riesgo:** Bajo. Es un skill de guía (no modifica nada automáticamente). Se activa solo cuando se lo invoca.

---

#### B. `anthropic/commit-commands` (Plugin Oficial Anthropic)

**Qué hace:** Comandos directos para commit, push y PR con mensajes semánticos convencionales.

**Por qué para Corona:**
- Estandariza aún más el formato de commits (ya tenemos convención, esto la refuerza)
- Reduce fricción: "commit these changes" → todo automatizado
- Incluye `clean_gone` para limpiar branches locales huérfanas

**Instalación:**
```bash
claude plugin add anthropic/commit-commands
```

**Riesgo:** Muy bajo. Solo cuando se invoca explícitamente.

---

#### C. `anthropic/code-review` (Plugin Oficial Anthropic)

**Qué hace:** Lanza 5 agentes especializados en paralelo para revisar un PR, con scoring de confianza y comentarios automáticos en GitHub.

**Por qué para Corona:**
- Complementa nuestro `code-review-ingenio` con revisión multi-agente
- Detecta bugs, violaciones de CLAUDE.md, issues de contexto histórico

**Instalación:**
```bash
claude plugin add anthropic/code-review
```

**Riesgo:** Medio. Requiere integración con GitHub (`gh` CLI). Evaluar si vale la pena vs. el code-review existente.

---

### PRIORIDAD MEDIA — Beneficio a mediano plazo

#### D. `anthropic/feature-dev` (Plugin Oficial Anthropic)

**Qué hace:** Proceso guiado de 7 fases: discovery → codebase exploration → clarification → architecture → implementation → quality review → summary.

**Por qué para Corona:**
- Ya tenemos el patrón brainstorming→writing-plans→subagent-driven-development
- Este plugin formaliza el proceso con agentes especializados
- Útil para features grandes (ej: nuevo módulo de Facturas)

**Instalación:**
```bash
claude plugin add anthropic/feature-dev
```

**Riesgo:** Medio. Puede solaparse con superpowers. Evaluar complementariedad.

---

#### E. `addyosmani/web-quality-skills` (Skill Comunitario — Addy Osmani, Chrome Team)

**Qué hace:** 5 skills para auditoría de Core Web Vitals (LCP, INP, CLS), accesibilidad WCAG 2.1, SEO, buenas prácticas — framework-aware.

**Por qué para Corona:**
- Addy Osmani es Engineering Manager en Google Chrome (altamente confiable)
- Auditoría automática de dashboards antes de mostrar a gerencia
- Cobertura de accesibilidad que hoy no tenemos
- Compatible con HTML/Bootstrap vanilla

**Instalación:**
```bash
npx add-skill addyosmani/web-quality-skills
```

**Riesgo:** Bajo. Skills de auditoría local, sin conexión externa.

---

#### F. `anthropic/pr-review-toolkit` (Plugin Oficial Anthropic)

**Qué hace:** 6 agentes especializados: comment-analyzer, test-analyzer, silent-failure-hunter, type-design-analyzer, code-reviewer, code-simplifier.

**Por qué para Corona:**
- Más granular que `code-review` para revisiones enfocadas
- Útil para revisar cambios grandes en los proyectos

**Instalación:**
```bash
claude plugin add anthropic/pr-review-toolkit
```

**Riesgo:** Bajo. Opcional, complementario a code-review.

---

### PRIORIDAD BAJA — Exploratorio / Futuro

#### G. `nextlevelbuilder/ui-ux-pro-max-skill` (Skill Comunitario — 62.6k ⭐)

**Qué hace:** 240+ estilos, 127 pares de fuentes, 99 guías UX. Detecta el contexto del producto y elige automáticamente un sistema de diseño.

**Por qué para Corona:** Potencial para diseño de landing pages, intranets, etc. Pero requiere evaluación.

**Riesgo:** Dependencia externa, puede ser demasiado genérico.

---

## 3. Mapeo por Proyecto

### Proyecto: Dashboard Sumas y Saldos (PRODUCCIÓN)

| Problema Actual | Skill Recomendado | Beneficio |
|-----------------|-------------------|-----------|
| Estética genérica | `frontend-design` | Identidad visual distintiva |
| Sin validación WCAG | `web-quality-skills` | Accesibilidad para gerencia |
| Sin testing visual | `Playwright MCP` (futuro) | Capturas automáticas |

### Proyecto: Conciliación Bancaria (AJUSTES)

| Problema Actual | Skill Recomendado | Beneficio |
|-----------------|-------------------|-----------|
| UI funcional pero básica | `frontend-design` | Mejora visual del tablero |
| Sin revisión de cambios | `code-review` | Detección temprana de bugs |

### Proyecto: Carga Automática de Facturas (DESARROLLO)

| Problema Actual | Skill Recomendado | Beneficio |
|-----------------|-------------------|-----------|
| Feature complejo | `feature-dev` | Proceso estructurado |
| Múltiples componentes | `pr-review-toolkit` | Revisión granular |
| UI del formulario | `frontend-design` | Experiencia de carga mejorada |

### Proyecto: Molienda Web (PHP)

| Problema Actual | Skill Recomendado | Beneficio |
|-----------------|-------------------|-----------|
| UI legacy | `frontend-design` | Refactor visual progresivo |
| Sin métricas web | `web-quality-skills` | Auditoría de performance |

### Todos los proyectos

| Necesidad Común | Skill Recomendado |
|-----------------|-------------------|
| Commits consistentes | `commit-commands` |
| Git workflow | `commit-commands` + `git-workflow-ingenio` |
| Code review local | `code-review-ingenio` (existente) |

---

## 4. Plan de Instalación — Faseado

### Fase 1 (Esta Semana) — Perder cero funcionalidad existente

```bash
# 1. Backup del skillset actual
cp -r /home/soporte/.claude/skills /home/soporte/.claude/skills.backup.$(date +%Y%m%d)

# 2. Instalar plugins oficiales prioritarios
claude plugin add anthropic/frontend-design
claude plugin add anthropic/commit-commands

# 3. Verificar que los skills locales siguen funcionando
claude list skills
```

### Fase 2 (Próxima Semana) — Evaluar

```bash
# 4. Probar en un proyecto existente
claude plugin add anthropic/code-review

# 5. Instalar skill comunitario
npx add-skill addyosmani/web-quality-skills
```

### Fase 3 (Mediano Plazo) — Opcional según necesidad

```bash
# 6. Feature dev + PR toolkit (cuando se necesite)
claude plugin add anthropic/feature-dev
claude plugin add anthropic/pr-review-toolkit
```

---

## 5. Riesgos y Mitigaciones

| Riesgo | Mitigación |
|--------|------------|
| Skills oficiales pueden solaparse con superpowers existentes | Evaluar cada uno en entorno de prueba. Los skills se activan explícitamente |
| Plugin catalog cache puede quedar obsoleto | `claude plugin update` periódico |
| Dependencia de skills comunitarios | Solo los de autores verificados (Addy Osmani = Chrome team, nextlevelbuilder = 62k ⭐) |
| Inflado de contexto con muchos skills | Máximo 5-6 skills activos, rotar según proyecto |
| Conflictos con CLAUDE.md existente | Los skills oficiales respetan CLAUDE.md como prioridad |

---

## 6. Stack Propuesto Final

```
Skills Siempre Activos (Core Corona):
├── dashboard-portable-corona        # Arquitectura de dashboards
├── html-css-avanzado                # Layout Corona
├── javascript-moderno               # JS vanilla + Node-RED
├── ui-ux-reviewer                   # Checklist gerencial
├── frontend-bootstrap-reviewer      # Checklist Bootstrap
├── performance-web                  # Optimización
├── testing-web                      # Smoke tests
├── git-workflow-ingenio             # Convenciones Git
└── (futuro) frontend-design         # Identidad visual

Plugins Oficiales Anthropic:
├── anthropic/frontend-design        # Diseño visual distintivo ← PRIORIDAD
├── anthropic/commit-commands        # Git workflow mejorado ← PRIORIDAD
├── anthropic/code-review            # Code review multi-agente ← EVALUAR
├── anthropic/feature-dev            # Feature workflow ← OPCIONAL
└── anthropic/pr-review-toolkit      # PR granular ← OPCIONAL

Skills Comunitarios (Alta Confiabilidad):
└── addyosmani/web-quality-skills    # Perf, a11y, SEO audit ← EVALUAR
```

---

## 7. Próximos Pasos

1. ✅ Aprobar o ajustar esta propuesta
2. Ejecutar Fase 1 (instalar `frontend-design` + `commit-commands`)
3. Probar en un dashboard existente (ej: Sumas y Saldos)
4. Evaluar resultados y decidir Fase 2
5. Documentar experiencia en MEMORY.md

---

*Propuesta preparada por Claude Code con información del marketplace oficial de Anthropic, análisis de skills existentes de Ingenio La Corona, y referencias del ecosistema Claude Code.*
