# Skill: php-bootstrap-conventions

**Descripción:** Reglas de estilo y arquitectura para aplicaciones web internas (PHP + JS + Bootstrap) en Ingenio La Corona.

1. **Backend (PHP):**
   - Mantén la lógica de negocio separada de la presentación HTML [13].
   - Toda entrada de usuario (incluso usuarios internos) debe ser validada en el backend de forma independiente [13, 14].
   - Implementa un manejo de errores que falle de forma segura y deje logs operativos sin exponer datos sensibles [13].

2. **Frontend (Bootstrap + JS):**
   - Usa JavaScript "vanilla" donde sea posible. No introduzcas frameworks nuevos (React/Vue/Angular) si la página no los usa [12].
   - Estilo corporativo, limpio y operativo. Usa colores de alerta (rojo/verde/amarillo) de manera consistente para KPIs y estados [15].
   - No añadas dependencias externas vía CDN sin justificación. Usa los assets locales del proyecto [16].
   - Utiliza HTML semántico y diseña pensando en pantallas de control industrial o monitores antiguos [14, 15].