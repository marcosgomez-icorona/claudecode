# Proyecto — Carga Automática de Facturas de Proveedores

## Objetivo
Automatizar recepción, clasificación, extracción, validación y preparación de facturas de proveedores para registración en ERP Calipso. Reducir carga manual de Cuentas a Pagar, minimizar errores, generar trazabilidad completa desde PDF hasta asiento contable.

## Alcance MVP
- Ingesta desde mail (casilla dedicada) y carpeta de red
- OCR + extracción estructurada vía Claude
- Staging en SQL Server 2008 R2
- Validaciones: CUIT, duplicado, CAE AFIP, match OC básico
- Dashboard de revisión humana con semáforo
- Carga supervisada al ERP (no automática en fase 1)

## Fuera de alcance en MVP
- Auto-aprobación sin intervención humana
- Integración con portales de proveedores
- Aprendizaje automático de layouts
- Facturas electrónicas B2B vía webservice directo

## Estados del pipeline
`RECIBIDA → EXTRAIDA → VALIDADA_[VERDE|AMARILLA|ROJA] → APROBADA → CARGADA`

Estados de error: `ERROR_EXTRACCION`, `ERROR_VALIDACION`, `RECHAZADA`

## Reglas críticas
- Hash del PDF + clave única (CUIT+tipo+pto_vta+nro) para evitar duplicados
- Validación CAE contra AFIP obligatoria antes de aprobar
- Diferencias contra OC fuera de tolerancia → semáforo rojo
- Prohibido INSERT directo en tablas Calipso — solo vía pr_ezi o importador supervisado
- UUID de operación atraviesa TODAS las capas y queda en logs

## Estructura de carpetas de facturas (filesystem)
```
/facturas/entrada/           ← llegada
/facturas/pendientes/        ← en proceso
/facturas/AAAA/MM/CUIT/      ← archivadas
/facturas/procesadas/        ← cargadas en ERP
/facturas/error/             ← con problemas
/facturas/rechazadas/        ← descartadas con motivo
```

## Convención de nombres
`AAAAMMDD_CUIT_TIPO_PTOVTA-NROCBTE.pdf`

Ejemplo: `20260315_30712345678_A_00001-00001234.pdf`

## Stack específico del proyecto
- Ingesta: Node-RED (watch folder) + n8n (IMAP mail)
- OCR nativo: pdfplumber / PyMuPDF
- OCR imagen: Tesseract local (definir si migramos a Azure Form Recognizer)
- Extracción: Claude API + skill `extract_invoice_data`
- Staging: SQL Server 2008 R2, DB dedicada `FACTURAS_STAGING`
- Validación AFIP: Python + WS AFIP (wsfev1 consulta comprobante)
- Dashboard: decidir — n8n form vs app Flask/FastAPI local
- Orquestación: n8n

## Estructura del repositorio
```
├── docs/           ← documentación funcional y técnica
├── sql/            ← DDL, stored procedures, scripts de mantenimiento
├── src/
│   ├── ingesta/    ← scripts de captura (mail, carpeta)
│   ├── ocr/        ← pipeline OCR
│   ├── extract/    ← llamadas a Claude y parseo
│   ├── validate/   ← validaciones AFIP, CUIT, duplicados
│   ├── match/      ← matching contra OC y recepción
│   └── load/       ← carga hacia Calipso
├── node-red/       ← flows exportados
├── n8n/            ← workflows exportados
├── tests/          ← tests unitarios y fixtures (PDFs de prueba anonimizados)
└── .claude/
    ├── skills/     ← skills reutilizables
    └── agents/     ← definiciones de agentes
```

## Convenciones de código
- Python 3.11+, type hints obligatorios, black + ruff
- Logs estructurados JSON con campo `uuid_operacion` siempre presente
- Configuración en `.env` (nunca hardcode), ejemplo en `.env.example`
- Nada de secretos en git — `.gitignore` estricto
- Tests con pytest, fixtures con PDFs anonimizados

## Estado actual
- [x] Diseño arquitectónico aprobado
- [ ] DDL base staging
- [ ] Skill `extract_invoice_data` v1
- [ ] Pipeline Python de extracción
- [ ] Stored procedures de validación
- [ ] Conector AFIP CAE
- [ ] Dashboard mínimo
- [ ] Integración Node-RED watch folder
- [ ] Workflow n8n ingesta mail

## Decisiones pendientes
1. ¿OCR 100% on-prem (Tesseract) o híbrido con Azure Form Recognizer?
2. Dashboard: ¿n8n form o app Flask dedicada?
3. Casilla de mail definitiva para ingesta
4. Top 10 proveedores modelo para calibrar extracción

## Referencias
- Ver `/docs/arquitectura.md` para detalle completo
- Ver `/docs/estados.md` para diagrama de estados
- Ver `/sql/README.md` para esquema de staging
