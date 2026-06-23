Referenciada por n8n-flow-architect y node-red-flow-reviewer para evitar desastres en la tecnología operativa (IT/OT)
.
# Skill: n8n-export-safety

**Descripción:** Protocolos de seguridad para el manejo de flujos de automatización en n8n y Node-RED.

1. **Regla de Oro (Backup):** Antes de realizar CUALQUIER importación o sugerir una modificación en un entorno productivo, se **DEBE** exportar y respaldar el flujo original existente [18, 22-24].
2. **Gestión de Credenciales:** Las credenciales (API keys, passwords) NUNCA deben estar incrustadas en el JSON del flujo o en nodos de función. Deben referenciarse mediante el gestor de credenciales de n8n o variables de entorno en Node-RED [23, 25, 26].
3. **Aislamiento de Entornos:** Mantén separaciones estrictas entre flujos de prueba y de producción (no deben compartir credenciales ni conexiones a menos que sea inevitable) [25, 26].
4. **Tecnología Operativa (Node-RED):** Cualquier flujo que interactúe con SCADA, OPC/KepServer o PLCs tiene prohibido realizar acciones de control directas (escribir *setpoints*) sin revisión operativa explícita por seguridad industrial [24, 27].