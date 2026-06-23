# Continuidad 2026-06-23 — Ecosistema de Agentes Semi-Autónomos

## Decisiones técnicas

1. **Agentes como archivos `.claude/agents/*.md`** — formato nativo de Claude Code con frontmatter YAML (`name`, `description`, `tools`, `model`, `color`, `agentMode`). Se auto-descubren al iniciar sesión.

2. **Skills nuevas como `.md` en `~/.claude/skills/`** — mismo formato que las skills de referencia existentes. No son directorios (tipo proyecto) sino archivos planos invocables vía `Skill`.

3. **3 agentes nuevos creados desde cero** — `docs-writer`, `security-reviewer`, `sql-server-calipso-reviewer` estaban referenciados por el coordinador pero sin archivo. Se crearon con sistema de prompts completo.

4. **Slash commands en `settings.json`** — `/new-feature`, `/sql-review`, `/deploy-check` como atajos a los agentes principales.

5. **No se duplicaron agentes con skills existentes** — `codebase-mapper.md` ya existía como skill, ahora también es agente. Son complementarios: la skill es para cargar conocimiento, el agente para delegar trabajo autónomo.

## Archivos creados (16)

| Archivo | Tipo |
|---------|------|
| `.claude/agents/ingenio-dev-coordinator.md` | Agente — coordinador principal |
| `.claude/agents/requirements-analyst.md` | Agente — analista funcional |
| `.claude/agents/codebase-mapper.md` | Agente — mapeo de código |
| `.claude/agents/solution-architect.md` | Agente — arquitecto técnico |
| `.claude/agents/backend-dev.md` | Agente — desarrollador backend |
| `.claude/agents/frontend-dev.md` | Agente — desarrollador frontend |
| `.claude/agents/qa-tester.md` | Agente — QA tester |
| `.claude/agents/deployment-checker.md` | Agente — readiness checker |
| `.claude/agents/n8n-flow-architect.md` | Agente — arquitecto n8n |
| `.claude/agents/node-red-flow-reviewer.md` | Agente — revisor Node-RED |
| `.claude/agents/docs-writer.md` | Agente — documentador (NUEVO) |
| `.claude/agents/security-reviewer.md` | Agente — revisor seguridad (NUEVO) |
| `.claude/agents/sql-server-calipso-reviewer.md` | Agente — revisor SQL (NUEVO) |
| `~/.claude/skills/calipso-sql-patterns.md` | Skill — patrones SQL 2008 R2 |
| `~/.claude/skills/n8n-export-safety.md` | Skill — seguridad export/import |
| `~/.claude/skills/php-bootstrap-conventions.md` | Skill — convenciones web |

## Archivos modificados (1)

| Archivo | Cambio |
|---------|--------|
| `.claude/settings.json` | Agregados 3 `customCommands` (`/new-feature`, `/sql-review`, `/deploy-check`) |

## Flujo de desarrollo semi-autónomo resultante

```
Usuario: "/new-feature Dashboard de stock"
  └► ingenio-dev-coordinator
        ├► Phase 1: codebase-mapper (mapeo read-only)
        ├► Phase 2: requirements-analyst (especificación)
        ├► Phase 3: solution-architect (diseño técnico)
        ├► Phase 4: backend-dev + frontend-dev (implementación)
        ├► Phase 5: security-reviewer + qa-tester (revisión)
        ├► Phase 6: docs-writer (documentación)
        └► Phase 7: deployment-checker (Go/No-Go)
```

## Próximos pasos

1. **[ ] Probar en sesión nueva** — reiniciar Claude Code y verificar que los 13 agentes aparecen disponibles en `Agent`
2. **[ ] Probar `/new-feature`** con una feature simple para validar el flujo completo
3. **[ ] Afinar agentes** — ajustar tools disponibles por agente según necesidad real
4. **[ ] Agregar `sql-server-calipso-reviewer` + `security-reviewer` + `docs-writer` a la sección de skills en CLAUDE.md** si se quiere invocar vía Skill también
5. **[ ] Documentar el ecosistema en README** del proyecto para onboarding de nuevos desarrolladores

## Riesgos identificados

- Los agentes heredan `model: inherit` — el comportamiento depende de qué modelo esté activo en la sesión
- El coordinador tiene `Task` tool pero en Claude Code el tool equivalente es `Agent` — verificar compatibilidad
- Las skills nuevas no están listadas en `CLAUDE.md` skills de referencia — los agentes las referencian por nombre, pero si el Skill tool no las encuentra, podrían ser ignoradas
