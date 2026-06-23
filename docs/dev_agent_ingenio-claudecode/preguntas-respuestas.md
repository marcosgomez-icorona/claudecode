Arquitecto senior de agentes IA, prompt engineer experto y especialista en desarrollo web full-stack para proyectos empresariales del Ingenio.

Agentes: los 13 originales, revisados y mejorados (más afilados en triggers, sin huecos, con reglas reforzadas de Git/branching)
Skills nuevas: calipso-sql-patterns, n8n-export-safety, php-bootstrap-conventions — conocimiento reutilizable que los agentes puedan invocar
.mcp.json: GitHub (PAT vía env var) + SQL Server (connection string con usuario read-only documentado)
settings.json global con el mapeo a DeepSeek
Slash commands útiles (/new-feature, /sql-review, /deploy-check, etc.)
CLAUDE.md raíz con las reglas duras del Ingenio
Script de instalación renovado + README ejecutivo

revisar primero las skills oficiales de Anthropic para estructurar bien las nuevas skills custom.Ver convenciones de estructura de skillsVer convenciones de estructura de skillsTengo lo que necesito. Voy a construir todo el paquete ahora.Crear estructura base del nuevo paqueteCrear estructura base del nuevo paqueteAhora voy a escribir cada pieza. Empiezo por los agentes mejorados (mantengo nombres y propósito, pero afino triggers, agrego referencias a las skills nuevas, refuerzo reglas Git/branch, y corrijo inconsistencias menores que vi, como el uso de herramientas y el flujo de aprobación).

Preguntas/Respuestas

P: ¿Qué vas a usar como motor del modelo en Claude Code para este Ingenio?
R: DeepSeek u otro proxy compatible con Anthropic API

P: ¿Dónde vivirá este paquete de agentes?
R: Carpeta global ~/.claude (agentes disponibles en todos los proyectos)

P: ¿Qué tan automatizado quieres el control de seguridad (hooks reales que bloqueen acciones, no solo instrucciones en el prompt)?
R: Solo instrucciones en los agentes (como está ahora), confío en el criterio del modelo

P: ¿Quieres que agregue MCP servers reales (filesystem, GitHub, SQL Server/Postgres, etc.) configurados en .mcp.json, o prefieres que el setup se quede solo en agentes + skills sin MCP por ahora?
R: Sí, configura MCPs reales que ya identifico que necesito (GitHub, SQL Server)

P: Para el MCP de GitHub: ¿vas a usar un Personal Access Token clásico (scopes repo + workflow) como ya hablamos, autenticado vía variable de entorno?
R: Sí, PAT clásico vía variable de entorno (GITHUB_TOKEN)

P: Para el MCP de SQL Server (Calipso): ¿cómo es el acceso a la base?
R: Servidor accesible por red local/VPN con usuario SQL (user/password)

P: ¿Quieres que el MCP de SQL Server tenga permisos de SOLO LECTURA forzados a nivel de configuración (usuario de BD read-only), o prefieres que el control de 'no escribir' dependa únicamente del criterio de los agentes como ya está definido?
R: Forzar solo-lectura a nivel de usuario/conexión SQL si es posible


----------
Perfecto. Ya tengo todo lo que necesito. Voy a construir el paquete completo:

Agentes: los 13 originales, revisados y mejorados (más afilados en triggers, sin huecos, con reglas reforzadas de Git/branching)
Skills nuevas: calipso-sql-patterns, n8n-export-safety, php-bootstrap-conventions — conocimiento reutilizable que los agentes puedan invocar
.mcp.json: GitHub (PAT vía env var) + SQL Server (connection string con usuario read-only documentado)
settings.json global con el mapeo a DeepSeek
Slash commands útiles (/new-feature, /sql-review, /deploy-check, etc.)
CLAUDE.md raíz con las reglas duras del Ingenio
Script de instalación renovado + README ejecutivo

Voy a revisar primero las skills oficiales de Anthropic para estructurar bien las nuevas skills custom.Ver convenciones de estructura de skillsVer convenciones de estructura de skillsTengo lo que necesito.
 Voy a construir todo el paquete ahora.Crear estructura base del nuevo paqueteCrear estructura base del nuevo paqueteAhora voy a escribir cada pieza. Empiezo por los agentes mejorados (mantengo nombres y propósito, pero afino triggers, agrego referencias a las skills nuevas, refuerzo reglas Git/branch, y corrijo inconsistencias menores que vi, como el uso de herramientas y el flujo de aprobación).