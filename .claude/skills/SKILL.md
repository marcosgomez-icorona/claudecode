---
name: automation-diagnostics
description: |
  Diagnóstico y recomendación de automatización para entornos IT/OT industriales. 
  Activar esta skill siempre que el usuario describa un proceso manual, repetitivo, 
  o que involucre transferencia de datos entre sistemas (ERP, PLC, SCADA, SQL, 
  archivos Excel/PDF, correos, reportes). También activar cuando el usuario mencione 
  palabras como "automatizar", "integrar", "conectar sistemas", "proceso manual", 
  "carga de datos", "envío de reportes", "conciliación", "sincronizar", "notificación 
  automática", o cuando describa un flujo de trabajo que hoy se hace a mano.
  Usar también cuando se comparen herramientas de automatización (Node-RED vs n8n, 
  SQL vs API, etc.) o cuando se planifique una nueva integración entre sistemas 
  industriales y administrativos.
---

# Skill: Diagnóstico de Automatización — Ingenio La Corona

## Propósito

Analizar cualquier proceso o tarea que el usuario describa y recomendar la herramienta, 
arquitectura y pasos concretos para automatizarlo, considerando el stack tecnológico 
real del Ingenio La Corona.

---

## Stack de referencia

Antes de recomendar, mapear el pedido contra estas capas:

### Capa OT (Campo / Planta)
- **PLC**: Fatek (FBs series) — lectura de señales, contadores, setpoints
- **SCADA**: Wonderware InTouch — supervisión, historización, alarmas
- **HMI**: Weintek — operación local en campo
- **OPC Server**: KepServerEX — puente OT→IT, tags industriales

### Capa de Integración / Middleware
- **Node-RED**: adquisición local, lógica de flujos, protocolo OPC-UA/DA, MQTT, HTTP
- **n8n** (Hostinger Docker): orquestación cloud, workflows complejos, integraciones SaaS
- **SQL Server**: almacenamiento central, stored procedures, vistas, jobs del Agente SQL

### Capa Administrativa / ERP
- **ERP Calipso**: gestión comercial, compras, finanzas, inventarios, despachos
- **Agente Calipso v2.0**: interfaz AI sobre Calipso (Node.js/Express + Claude API + SQL Server 2022)
- **Power BI / Metabase**: dashboards y reporting gerencial

### Capa de Reportes y Documentos
- **Excel / PDF**: reportes operativos, KPIs, laboratorio, balanzas
- **Email (SMTP)**: envío automático de reportes y OC a proveedores
- **SharePoint / FTP**: distribución de archivos

### Capa de Control de Gestión
- **Departamento de Control**: balanza, materia prima, laboratorios (fábrica/especiales/destilería), inventarios, despachos azúcar/alcohol
- **Sistema de Gestión de Inocuidad**: ISO 22000 / objetivo FSSC 22000 — registros, evidencias, auditorías
- **~60 personas coordinadas**: operarios, analistas, jefes de turno

---

## Proceso de diagnóstico

### Paso 1 — Clasificar el tipo de tarea

| Tipo | Descripción | Señales en el pedido |
|------|-------------|----------------------|
| **Adquisición de datos** | Leer valores desde PLC, SCADA, sensores | "leer de PLC", "tomar dato del proceso", "registrar medición" |
| **ETL / Transformación** | Limpiar, transformar, cargar datos entre sistemas | "pasar de Excel a SQL", "cargar en Calipso", "consolidar planillas" |
| **Notificación / Alerta** | Enviar mensajes según condición | "avisar cuando", "notificar si", "mandar email/WhatsApp" |
| **Generación de reportes** | Crear PDF/Excel con datos del sistema | "reporte diario", "informe de turno", "resumen de producción" |
| **Integración bidireccional** | Sincronizar dos sistemas en ambas direcciones | "que Calipso se actualice con los datos del SCADA" |
| **Aprobación / Workflow** | Procesos que requieren intervención humana | "que el jefe apruebe", "flujo de autorización", "OC pendiente" |
| **Registro / Trazabilidad** | Dejar evidencia auditable (ISO/FSSC) | "registro de laboratorio", "evidencia de inocuidad", "log de proceso" |
| **Cálculo / KPI** | Computar indicadores operativos o financieros | "calcular rendimiento", "balance de masa", "eficiencia de molienda" |

### Paso 2 — Identificar los sistemas involucrados

Preguntar o inferir:
- ¿De dónde vienen los datos? (PLC, SCADA, Excel manual, Calipso, SQL, formulario)
- ¿A dónde van los datos? (SQL, Calipso, Power BI, email, PDF, WhatsApp)
- ¿Con qué frecuencia? (tiempo real, por turno, diario, bajo demanda)
- ¿Quién lo dispara? (evento automático, persona, horario, alarma)

### Paso 3 — Recomendar herramienta principal

Usar esta matriz de decisión:

```
¿Involucra datos de PLC/SCADA en tiempo real?
  → SÍ → Node-RED (con OPC-UA/DA o Modbus)
  → NO → continuar

¿Es una integración entre Calipso y otros sistemas?
  → SÍ → Agente Calipso v2.0 (si ya está desplegado) o Node-RED + SQL
  → NO → continuar

¿Requiere lógica compleja, reintentos, webhooks externos o integraciones SaaS?
  → SÍ → n8n (Hostinger Docker)
  → NO → continuar

¿Es principalmente transformación de datos tabulares o reportes?
  → SÍ → SQL Server (Stored Procedure + Agent Job) + Power BI / Excel
  → NO → continuar

¿Es un documento formal (PDF, Word, planilla estructurada)?
  → SÍ → Node-RED o n8n + script Python/Node.js generador de archivo
  → NO → continuar

¿Es una notificación simple basada en condición?
  → SÍ → Node-RED (para OT) o n8n (para IT/email/WhatsApp)
```

### Paso 4 — Arquitectura recomendada

Siempre presentar:

1. **Diagrama de flujo de datos** (texto ASCII o descripción clara de nodos)
2. **Stack exacto** con versiones/protocolos relevantes
3. **Punto de integración crítico** (dónde puede fallar)
4. **Estimación de complejidad**: Baja / Media / Alta
5. **Tiempo estimado de implementación**: horas / días / semanas

### Paso 5 — Pasos de implementación

Desglosar en fases concretas:
- Fase 1: Prueba de conectividad (verificar que los sistemas se ven)
- Fase 2: Prototipo mínimo (flujo básico sin manejo de errores)
- Fase 3: Manejo de errores y reintentos
- Fase 4: Logging y trazabilidad
- Fase 5: Puesta en producción y monitoreo

---

## Plantillas de respuesta

### Respuesta estándar de diagnóstico

```
## Diagnóstico de Automatización

**Tipo de tarea identificada:** [tipo del Paso 1]
**Sistemas involucrados:** [origen] → [destino]
**Frecuencia:** [tiempo real / periódica / bajo demanda]

---

### Herramienta recomendada: [nombre]
**Por qué:** [razón específica al caso]
**Alternativa:** [si aplica]

---

### Arquitectura propuesta

[Diagrama de flujo]

[SISTEMA ORIGEN] → [MIDDLEWARE] → [SISTEMA DESTINO]
       ↓                               ↓
  [qué dato]                    [qué hace con él]

---

### Pasos de implementación

**Fase 1 — Conectividad** (~X horas)
- [paso concreto]

**Fase 2 — Prototipo** (~X horas)
- [paso concreto]

**Fase 3 — Producción** (~X días)
- [paso concreto]

---

### Riesgos y consideraciones
- [riesgo 1]: [mitigación]
- [riesgo 2]: [mitigación]

---

### Clasificación del proyecto
- **Complejidad:** Baja / Media / Alta
- **Impacto operativo:** [descripción]
- **Categoría:** Proyecto de Automatización Administrativa / Tarea Operativa
```

---

## Proyectos activos de referencia

Al diagnosticar, verificar si el pedido es extensión de algún proyecto ya iniciado:

| Proyecto | Estado | Stack |
|----------|--------|-------|
| Conciliación Bancaria Automática | En levantamiento | Por definir |
| Carga Automática Facturas Proveedores | Arquitectura definida | Node-RED + MySQL + Calipso |
| Envío OC a Proveedores | ✅ En producción | Completado |
| Carga Facturas Venta Azúcar | Definido | Por implementar |
| Dashboard Gerencial | En evaluación | Metabase / Grafana / Power BI |
| Agente Calipso v2.0 | Propuesta completa | Node.js + Claude API + SQL Server |

Si el pedido se relaciona con uno de estos, indicarlo explícitamente y proponer integración o continuidad.

---

## Reglas de clasificación (importante para gestión)

- Si el automatismo **resuelve un proceso administrativo repetitivo** → clasificar como **Proyecto de Automatización Administrativa** (seguimiento formal)
- Si el automatismo **mejora una tarea operativa existente** → clasificar como **Tarea Operativa** (sin seguimiento de proyecto)
- Si involucra **datos de inocuidad o trazabilidad ISO/FSSC** → agregar flag: ⚠️ Requiere validación de integridad de registro

---

## Consideraciones especiales del contexto

- **Destilería**: pérdidas por evaporación en tanques abiertos entre 0.8%–1.5% según graduación alcohólica — incluir en balances automáticos
- **Zafra vs inter-zafra**: algunos procesos son estacionales; indicar si la automatización debe adaptarse al calendario de molienda
- **Red OT/IT segmentada**: validar siempre que el flujo de datos no atraviese segmentos de red sin el gateway apropiado (KepServerEX o Node-RED como broker)
- **SQL Server 2022**: aprovechar funciones de JSON nativo y Always Encrypted para integraciones con Agente Calipso
- **Auditorías ISO/FSSC**: todo registro automatizado debe tener timestamp, usuario responsable, y ser inmutable (no editable post-confirmación)
