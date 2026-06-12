# 📅 PLAN OPERATIVO - FASE 2 (2 SEMANAS)

**Objetivo**: Integración completa Python + Web + n8n  
**Timeline**: 14 días  
**Usuario Final**: Asistente de Pago a Proveedores  
**Status**: 🚀 INICIANDO

---

## 📋 Semana 1 - Configuración e Integración Python

### Día 1-2: Setup Python
- [ ] Instalar Python 3.11+ en `C:\claudecode\python`
- [ ] Crear ambiente virtual
- [ ] Instalar dependencias del módulo de validación
- [ ] Hacer disponible para todos los proyectos en carpeta

```bash
# Script de instalación
cd C:\claudecode
python -m venv py311_validacion
.\py311_validacion\Scripts\activate
pip install -r automatizacion-facturas\python\requirements.txt
```

### Día 3: Integración Web ↔ Python
- [ ] Leer y analizar `web/facturas_validacion.html`
- [ ] Crear bridge (API endpoint) entre web y Python
- [ ] Crear bridge entre web y MCP Node.js
- [ ] Documentar endpoints disponibles

**Endpoints a crear:**
```
POST /api/validate-invoice
POST /api/validate-bulk
GET /api/suppliers
GET /api/purchase-orders
```

### Día 4-5: Análisis del flujo N8N existente
- [ ] Leer `n8n/Recepcion y Analisis de Factura.json`
- [ ] Mapear nodos del workflow
- [ ] Identificar puntos de integración con MCP
- [ ] Crear documentación del flujo

**Tareas:**
```
1. Exportar estructura del workflow
2. Identificar inputs/outputs
3. Mapear a herramientas MCP disponibles
4. Documentar cambios necesarios
```

---

## 📋 Semana 2 - Integración Completa y Testing

### Día 6-7: Integración completa
- [ ] Configurar web interface con MCP Node.js
- [ ] Configurar web interface con Python validator
- [ ] Modificar n8n workflow para usar MCP + Python
- [ ] Setup de webhooks

**Checklist:**
```
✓ Web → MCP Node.js (consultas SQL)
✓ Web → Python validator (validaciones)
✓ n8n → MCP Node.js (obtener OC/facturas)
✓ n8n → Python validator (validar factura)
✓ n8n → CALIPSO (registrar asientos)
```

### Día 8-10: Testing integral
- [ ] Crear casos de prueba
- [ ] Test de flujo end-to-end
- [ ] Validación de asientos contables
- [ ] Pruebas de volumen

**Casos de prueba:**
```
1. Factura válida vs OC
2. Factura con variación de precio (5%)
3. Factura con item faltante
4. Procesamiento en lote (10 facturas)
5. Facturas con varias OC
6. Manejo de errores
```

### Día 11-14: Refinamiento y documentación
- [ ] Correción de errores encontrados
- [ ] Optimización de performance
- [ ] Capacitación del usuario (Asistente Pagos)
- [ ] Documentación final de procedimientos

---

## 🔧 Tareas Técnicas Inmediatas

### TAREA 1: Instalar Python (Hoy mismo)

**Localización propuesta:**
```
C:\claudecode\python\
  ├── Python311/           (instalación)
  ├── py311_validacion/    (virtualenv)
  └── shared/              (librerías compartidas)
```

**Comandos:**
```powershell
# 1. Descargar e instalar Python 3.11 LTS
# Desde https://www.python.org/downloads/

# 2. Crear estructura de carpetas
mkdir C:\claudecode\python
mkdir C:\claudecode\python\shared

# 3. Crear virtual env
cd C:\claudecode\python
python -m venv py311_validacion

# 4. Activar y instalar
.\py311_validacion\Scripts\activate
pip install --upgrade pip

# 5. Crear requirements.txt
cd C:\claudecode\automatizacion-facturas\python
pip install dataclasses
# (dataclasses está incluido en Python 3.7+)

# 6. Verificar
python validation_engine.py
```

### TAREA 2: Crear Bridge Web → Backend

**Estructura propuesta:**
```javascript
// backend/api-bridge.js (Node.js server)
import express from 'express';
import { spawn } from 'child_process';
import { mcpClient } from './mcp-client.js';

const app = express();
app.use(express.json());

// Endpoint para validar factura
app.post('/api/validate-invoice', async (req, res) => {
  try {
    const { invoiceId, poId } = req.body;
    
    // 1. Obtener datos con MCP
    const invoice = await mcpClient.call('get_invoices_by_supplier', {
      invoice_id: invoiceId
    });
    
    const po = await mcpClient.call('get_purchase_orders', {
      po_id: poId
    });
    
    // 2. Validar con Python
    const validation = await pythonValidator.validate(invoice, po);
    
    // 3. Retornar resultado
    res.json({
      valid: validation.is_valid,
      validation: validation.to_dict()
    });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.listen(3000, () => console.log('API Bridge escuchando en :3000'));
```

### TAREA 3: Analizar `facturas_validacion.html`

**Se necesita identificar:**
- [ ] Campos de entrada (factura, OC, proveedor, etc)
- [ ] Tabla de resultados
- [ ] Botones/acciones disponibles
- [ ] Formateo de datos para envío

**Propuesta de flujo UI:**
```
1. Usuario carga factura (búsqueda o manual)
2. Selecciona OC asociada
3. Hace clic en "Validar"
4. Muestra resultado de validación
5. Si es válido: botón "Registrar en CALIPSO"
6. Si tiene advertencias: requiere aprobación manual
```

### TAREA 4: Mapear Workflow N8N

**Del archivo: `n8n/Recepcion y Analisis de Factura.json`**

**Estructura esperada:**
```
Gmail Trigger
  ↓
Extract Attachment (PDF)
  ↓
OCR/Parse Factura  ← AQUÍ integrar con Python
  ↓
Get Purchase Order ← AQUÍ usar MCP Node.js
  ↓
Validate Invoice   ← AQUÍ usar Python validator
  ↓
Generate Accounting Entries ← AQUÍ usar Python
  ↓
Register in CALIPSO ← AQUÍ usar MCP Node.js
  ↓
Send Notification Email
```

---

## 📊 Entregables por Semana

### SEMANA 1 Entregables:
```
✅ Python instalado y configurado
✅ Módulo de validación funcionando standalone
✅ Bridge API Node.js - Python operativo
✅ Análisis del workflow N8N documentado
✅ Documento de arquitectura actualizado
```

### SEMANA 2 Entregables:
```
✅ Web interface integrada y funcionando
✅ N8N workflow actualizado y probado
✅ Flujo end-to-end validado
✅ Casos de prueba pasados
✅ Capacitación del usuario completada
✅ Manual de procedimientos finales
```

---

## 🎯 KPIs de Éxito

| KPI | Objetivo |
|---|---|
| Tiempo de validación de factura | < 5 segundos |
| Precisión de validación | 99%+ |
| Procesamiento en lote | 50+ facturas/min |
| Disponibilidad del sistema | 99%+ |
| Error rate | < 0.1% |

---

## 🚨 Riesgos y Mitigación

| Riesgo | Probabilidad | Impacto | Mitigación |
|---|---|---|---|
| Python no se instala correctamente | Media | Alto | Usar Microsoft Store o WSL |
| Integración n8n compleja | Media | Medio | Comenzar con flujo simple |
| Performance bajo en validación | Baja | Medio | Optimizar Python, cache de datos |
| Permisos insuficientes en CALIPSO | Baja | Alto | Verificar user powerbi tiene acceso WRITE |

---

## 📞 Puntos de Control (Checkpoints)

**Checkpoint 1 - Fin Día 2:**
- [ ] Python instalado
- [ ] validation_engine.py ejecutándose sin errores
- **Responsable**: Soporte técnico

**Checkpoint 2 - Fin Día 5:**
- [ ] Bridge API funcional
- [ ] Web interface conectada a MCP
- [ ] N8N workflow documentado
- **Responsable**: Desarrollo

**Checkpoint 3 - Fin Día 10:**
- [ ] Flujo completo e2e probado
- [ ] Casos de prueba 80%+ pasados
- **Responsable**: QA

**Checkpoint 4 - Fin Día 14:**
- [ ] 100% de casos de prueba pasados
- [ ] Usuario capacitado y dando feedback
- [ ] Sistema listo para producción
- **Responsable**: Implementación

---

## 📋 Checklist Final

### Instalación Python
- [ ] Python 3.11+ en C:\claudecode\python
- [ ] Virtual environment creado
- [ ] validation_engine.py probado exitosamente
- [ ] Documentación de setup guardada

### Integración Web
- [ ] facturas_validacion.html analizado
- [ ] Bridge API Node.js → Python
- [ ] Endpoints REST documentados
- [ ] CORS configurado

### Integración n8n
- [ ] Workflow analizado y documentado
- [ ] Nodos MCP integrados
- [ ] Python validator integrado
- [ ] Webhooks configurados

### Testing
- [ ] 10+ casos de prueba definidos
- [ ] Flujo end-to-end funcional
- [ ] Documentación de errores y resoluciones
- [ ] Performance medido

### Capacitación
- [ ] Manual de procedimientos creado
- [ ] Usuario (Asistente Pagos) capacitado
- [ ] Videos de demostración (opcional)
- [ ] FAQ preparado

### Producción
- [ ] Backups configurados
- [ ] Monitoring y alertas activos
- [ ] Plan de rollback documentado
- [ ] Contacto de soporte disponible

---

## 💬 Próximos Pasos Inmediatos

### HOY:
1. Descarga de Python 3.11 LTS desde python.org
2. Instalación en C:\claudecode\python
3. Confirmación de que validation_engine.py funciona

### MAÑANA:
1. Análisis de facturas_validacion.html
2. Inicio de desarrollo del bridge API
3. Lectura del workflow n8n

### ESTA SEMANA:
1. Completar todas las tareas de SEMANA 1
2. Seguir plan detallado arriba

---

## 📞 Contacto Soporte

**MCP Status**: ✅ Operativo  
**Python**: ⏳ En instalación (Responsable: Usuario)  
**Web Interface**: 📋 Pendiente análisis  
**N8N Workflow**: 📋 Pendiente integración  

**Próxima reunion/checkpoint**: Fin de SEMANA 1

---

**Creado**: 6 de Junio de 2026  
**Plan Validado**: Sí  
**Responsable Implementación**: GitHub Copilot + Usuario  
**Timeline**: 14 días (2 semanas)
