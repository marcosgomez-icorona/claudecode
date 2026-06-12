# 🚀 START HERE - FASE 2 (2 SEMANAS)

**Creado**: 6 de Junio 2026  
**Timeline**: 14 días  
**Usuario Final**: Asistente de Pago a Proveedores  
**Status**: 🟢 LISTO PARA INICIAR  

---

## 📍 DONDE ESTAMOS AHORA

✅ **FASE 1 Completada**:
- MCP Server operativo con 14 herramientas
- Python validation module completo (700 líneas)
- SQL Server CORONA conectado y probado
- Documentación exhaustiva creada

📍 **HOY Iniciamos FASE 2**:
- Integración Python + Web + n8n
- Timeline: 2 semanas
- Objetivo: Sistema validación facturas end-to-end

---

## 🎯 LO QUE NECESITAS HACER HOY/MAÑANA

### ✋ PAUSA - Lee esto primero

Tenemos 3 documentos nuevos que explican todo:

1. **[PLAN_OPERATIVO_FASE2_2SEMANAS.md](./PLAN_OPERATIVO_FASE2_2SEMANAS.md)**
   - Plan día a día (14 días)
   - Tareas específicas por día
   - Checkpoints y KPIs
   - **EMPEZAR AQUÍ**

2. **[ANALISIS_FACTURAS_VALIDACION_HTML.md](./ANALISIS_FACTURAS_VALIDACION_HTML.md)**
   - Análisis completo de la interfaz web
   - Endpoints API necesarios
   - Estructuras de datos JSON
   - Propuesta de backend

3. **[ANALISIS_N8N_WORKFLOW.md](./ANALISIS_N8N_WORKFLOW.md)**
   - Diagrama del workflow n8n actual
   - Nodos existentes vs nuevos
   - Puntos de integración MCP
   - Cómo modificar el workflow

---

## 📋 CHECKLIST INMEDIATO (HOY)

### ✅ PASO 1: Instalar Python (30 minutos)

```powershell
# 1. Descargar Python 3.11 LTS
# URL: https://www.python.org/downloads/
# Instalar en: C:\claudecode\Python311

# 2. Crear estructura
mkdir C:\claudecode\python

# 3. Crear virtualenv
cd C:\claudecode\python
"C:\claudecode\Python311\python.exe" -m venv py311_validacion

# 4. Activar virtualenv
.\py311_validacion\Scripts\activate

# 5. Instalar dependencias (si las hay)
pip install --upgrade pip

# 6. VALIDAR: Ejecutar validation_engine.py
python "C:\claudecode\automatizacion-facturas\python\validation_engine.py"
```

**Esperado**: Sin errores, output mostrando ejemplo de validación ✅

---

### ✅ PASO 2: Crear Node.js Backend (MAÑANA)

Crear archivo: `C:\claudecode\automatizacion-facturas\backend\api-server.js`

```javascript
import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';

dotenv.config({ path: '../.env' });
const app = express();
app.use(cors());
app.use(express.json());

// ENDPOINTS NECESARIOS:
// GET  /api/facturas/pendientes
// GET  /api/facturas/{id}
// GET  /api/facturas/{id}/items
// GET  /api/oc/proveedor/{cuit}
// GET  /api/constancias/oc/{nro}
// POST /api/facturas/{id}/validar
// POST /api/facturas/{id}/aprobar
// POST /api/facturas/{id}/rechazar

const PORT = 3000;
app.listen(PORT, () => {
  console.log(`API Backend escuchando en http://localhost:${PORT}`);
});
```

**Referencias**:
- [ANALISIS_FACTURAS_VALIDACION_HTML.md](./ANALISIS_FACTURAS_VALIDACION_HTML.md) → Endpoint details
- [MCP_EXTENDIDO_GUIA_COMPLETA.md](./MCP_EXTENDIDO_GUIA_COMPLETA.md) → Herramientas disponibles

---

## 🗓️ SEMANA 1 (INSTALACIÓN)

### Día 1-2: SETUP PYTHON
- [ ] Instalar Python 3.11 en C:\claudecode\Python311
- [ ] Crear virtualenv en C:\claudecode\python\py311_validacion
- [ ] Verificar que validation_engine.py ejecuta sin errores
- [ ] **Checkpoint 1**: Python funcionando ✅

### Día 3: ANÁLISIS WEB
- [ ] Leer [ANALISIS_FACTURAS_VALIDACION_HTML.md](./ANALISIS_FACTURAS_VALIDACION_HTML.md)
- [ ] Identificar todos los endpoints que necesita
- [ ] Mapear a herramientas MCP disponibles
- [ ] Crear lista de estructuras JSON

### Día 4-5: ANÁLISIS n8n
- [ ] Leer [ANALISIS_N8N_WORKFLOW.md](./ANALISIS_N8N_WORKFLOW.md)
- [ ] Entender flujo Gmail → PDF → OpenAI → Staging
- [ ] Identificar dónde integrar MCP + Python
- [ ] Documentar cambios necesarios
- [ ] **Checkpoint 2**: Análisis completado ✅

---

## 🗓️ SEMANA 2 (INTEGRACIÓN)

### Día 6-7: CREAR BACKEND
- [ ] Crear estructura Node.js en `backend/`
- [ ] Implementar rutas `/api/facturas/*`
- [ ] Conectar MCP client
- [ ] Crear wrapper para Python validator
- [ ] Testing básico de endpoints

### Día 8-10: TESTING
- [ ] 10+ casos de prueba definidos
- [ ] Flujo end-to-end probado
- [ ] Documentar errores encontrados
- [ ] Optimizaciones de performance
- [ ] **Checkpoint 3**: Flujo funcional ✅

### Día 11-14: REFINAMIENTO
- [ ] Integración web → Backend
- [ ] Integración n8n → Backend  
- [ ] Capacitación usuario
- [ ] Documentación final
- [ ] **Checkpoint 4**: LISTO PRODUCCIÓN ✅

---

## 📚 DOCUMENTOS DISPONIBLES

| Documento | Tamaño | Para Quién |
|---|---|---|
| [PLAN_OPERATIVO_FASE2_2SEMANAS.md](./PLAN_OPERATIVO_FASE2_2SEMANAS.md) | 500+ líneas | PM / Developer |
| [ANALISIS_FACTURAS_VALIDACION_HTML.md](./ANALISIS_FACTURAS_VALIDACION_HTML.md) | 400+ líneas | Frontend / Backend |
| [ANALISIS_N8N_WORKFLOW.md](./ANALISIS_N8N_WORKFLOW.md) | 350+ líneas | Integraciones |
| [MCP_EXTENDIDO_GUIA_COMPLETA.md](./MCP_EXTENDIDO_GUIA_COMPLETA.md) | 450+ líneas | Backend / Developer |
| [SETUP_PYTHON_VALIDACION.md](./SETUP_PYTHON_VALIDACION.md) | 350+ líneas | DevOps / Python |

---

## 🔗 ARQUITECTURA FINAL (Lo que vas a construir)

```
┌─────────────────────────────────────────────────────────┐
│                 USUARIO FINAL                            │
│          (Asistente de Pago a Proveedores)              │
└────────────────────┬────────────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────────────┐
│         WEB INTERFACE (HTML + JavaScript)               │
│  • Carga facturas pendientes                            │
│  • Muestra validación en tiempo real                    │
│  • Botones: Aprobar/Rechazar                            │
└────────────────────┬────────────────────────────────────┘
                     ↓
        ┌────────────┴────────────┐
        ↓                         ↓
    ┌─────────────┐        ┌──────────────┐
    │   API REST  │        │ n8n Workflow │
    │  :3000      │        │   (Auto)     │
    └──────┬──────┘        └──────┬───────┘
           ↓                      ↓
    ┌─────────────────────────────────────┐
    │   Node.js Backend (Express)         │
    │  • Conecta MCP tools                │
    │  • Llama Python validator           │
    │  • Registra en CALIPSO              │
    └──────┬──────────────────┬───────────┘
           ↓                  ↓
    ┌────────────────┐  ┌──────────────────┐
    │  MCP Server    │  │ Python Validator │
    │  (SQL Server)  │  │  (validation_    │
    │  • OC lookup   │  │   engine.py)     │
    │  • Factura get │  │  • Validate      │
    │  • Contabilidad│  │  • Anomalies     │
    └────────┬───────┘  └────────┬─────────┘
             ↓                   ↓
    ┌──────────────────────────────────────┐
    │     CORONA Database (SQL Server)     │
    │  • FACTURACOMPRA                     │
    │  • ORDENCOMPRA                       │
    │  • ITEMCONTABLE                      │
    │  • STAGING_FACTURAS                  │
    └──────────────────────────────────────┘
```

---

## 🎯 CHECKPOINTS CRÍTICOS

### ✅ CHECKPOINT 1 - Fin Día 2
**Qué se valida**: Python instalado y funcionando
**Cómo verificar**:
```powershell
.\py311_validacion\Scripts\activate
python ..\automatizacion-facturas\python\validation_engine.py
# Debe mostrar: resultado de validación de ejemplo SIN ERRORES
```

### ✅ CHECKPOINT 2 - Fin Día 5
**Qué se valida**: Análisis completado
**Qué se debe tener**:
- [ ] HTML web totalmente analizado
- [ ] Endpoints necesarios documentados
- [ ] Workflow n8n entendido completamente
- [ ] Plan de integración claro

### ✅ CHECKPOINT 3 - Fin Día 10
**Qué se valida**: Flujo funcional
**Qué se debe tener**:
- [ ] Backend API parcialmente funcional
- [ ] 5+ endpoints funcionando
- [ ] MCP conectado y probado
- [ ] Python validator integrado
- [ ] Testing básico pasando

### ✅ CHECKPOINT 4 - Fin Día 14
**Qué se valida**: LISTO PRODUCCIÓN
**Qué se debe tener**:
- [ ] Flujo end-to-end completo
- [ ] Web + Backend + MCP + Python + n8n integrados
- [ ] Usuario capacitado
- [ ] Documentación final
- [ ] 99%+ de casos de prueba pasados

---

## 🚨 BLOQUEADORES POTENCIALES

| Bloqueador | Si ocurre | Solución |
|---|---|---|
| Python no instala | Usar Python 3.10 o WSL | Ver SETUP_PYTHON_VALIDACION.md |
| Node.js no encontrado | npm no existe | Usar ruta completa C:\Program Files\nodejs |
| MCP no conecta | Error SQL | Verificar .env con CORONA credentials |
| n8n no tiene MCP node | Feature no existe | Crear webhook bridge con Node.js |
| Performance lenta | Validación tarda > 5s | Implementar caché de OC |

---

## 💬 PREGUNTAS FRECUENTES

**P: ¿Qué versión de Python necesito?**  
R: Python 3.11 LTS (recomendado) o 3.10+

**P: ¿Puedo usar anaconda en lugar de venv?**  
R: Sí, usar `conda create -n fact-validacion python=3.11`

**P: ¿Dónde va el backend Node.js?**  
R: `C:\claudecode\automatizacion-facturas\backend\api-server.js`

**P: ¿Necesito instalar n8n localmente?**  
R: No, usamos el existente. Solo modificamos el workflow.

**P: ¿Cuánto tiempo toma la validación?**  
R: Target < 5 segundos por factura

**P: ¿Se puede procesar facturas en lote?**  
R: Sí, batch processing soporta 50+ facturas/minuto

---

## 📞 CONTACTO & RECURSOS

| Recurso | Ubicación |
|---|---|
| **Plan Operativo** | `docs/PLAN_OPERATIVO_FASE2_2SEMANAS.md` |
| **Análisis HTML** | `docs/ANALISIS_FACTURAS_VALIDACION_HTML.md` |
| **Análisis n8n** | `docs/ANALISIS_N8N_WORKFLOW.md` |
| **MCP Tools** | `docs/MCP_EXTENDIDO_GUIA_COMPLETA.md` |
| **Python Setup** | `docs/SETUP_PYTHON_VALIDACION.md` |
| **Python Code** | `python/validation_engine.py` |
| **MCP Server** | `mcp-calipso-sqlserver/src/server.js` |
| **Web HTML** | `web/facturas_validacion.html` |
| **n8n Workflow** | `n8n/Recepcion y Analisis de Factura.json` |

---

## ✨ PRÓXIMO PASO

👉 **Ahora**: Ve a `docs/PLAN_OPERATIVO_FASE2_2SEMANAS.md` y sigue día a día

---

**¿Dudas o preguntas?** Revisá los documentos de análisis primero. Si aún no está claro, preguntame y lo aclaramos.

**¿Listo?** Arranca con la instalación de Python hoy mismo. ✅

---

*Documento creado por: GitHub Copilot*  
*Última actualización: 6 de Junio 2026*  
*Estado: 🟢 LISTO PARA INICIAR*
