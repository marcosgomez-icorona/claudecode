# Setup Python para Validación de Facturas

## 📋 Instalación de Python

### Opción 1: Microsoft Store (Recomendado)
```powershell
python  # Esto abrirá Microsoft Store para instalar Python
```

### Opción 2: Descarga directa
1. Ir a https://www.python.org/downloads/
2. Descargar Python 3.11+ (versión más reciente)
3. Durante instalación, marcar: ✅ "Add Python to PATH"
4. Verificar:
```powershell
python --version
pip --version
```

### Opción 3: Anaconda
```powershell
# Descargar de https://www.anaconda.com/download
# Seguir instalador GUI
conda --version
```

---

## 🚀 Uso del Módulo de Validación

### 1. Ejecutar prueba básica (standalone)

```powershell
cd C:\claudecode\proyectos-ingenio\automatizacion-facturas\python
python validation_engine.py
```

**Salida esperada:**
```
============================================================
RESULTADO DE VALIDACIÓN
============================================================
{
  "is_valid": false,
  "timestamp": "2024-06-05T...",
  "validations_passed": [...],
  "warnings": [
    "Precio inusual en 'Producto A': $51.00 vs $50.00..."
  ],
  "errors": [...],
  "suggestions": [...]
}
```

### 2. Importar en CODEX o scripts

```python
from validation_engine import (
    InvoiceValidator,
    Invoice,
    InvoiceItem,
    PurchaseOrder,
    BulkInvoiceProcessor
)

# Crear validador
validator = InvoiceValidator()

# Validar factura contra OC
result = validator.validate_invoice_against_po(invoice, purchase_order)

# Obtener resultado
if result.is_valid:
    print("✅ Factura válida")
else:
    print("❌ Factura rechazada")
    for error in result.errors:
        print(f"  - {error}")
```

### 3. Procesamiento en lote

```python
processor = BulkInvoiceProcessor(validator)

# Procesar múltiples facturas
results = processor.process_invoices(
    invoices=[inv1, inv2, inv3, ...],
    purchase_orders={"po_001": po1, "po_002": po2, ...}
)

# Obtener resumen
print(f"Válidas: {results['valid_invoices']}")
print(f"Rechazadas: {results['rejected_invoices']}")
```

---

## 📊 Funcionalidades Disponibles

### `InvoiceValidator`
```
Métodos principales:
  - validate_invoice_against_po()     Valida factura vs OC
  - calculate_invoice_totals()         Calcula totales desglosados
  - detect_anomalies()                 Detecta variaciones sospechosas
  - generate_accounting_entries()      Genera asientos contables
```

### `ValidationResult`
```
Propiedades:
  - is_valid                          Validación pasó/falló
  - errors                            Errores críticos
  - warnings                          Advertencias
  - suggestions                        Sugerencias de mejora
  - variance_analysis                 Análisis de desviaciones
```

---

## 🔄 Integración con MCP Node.js

### Flujo recomendado:

```
CODEX
  ↓
MCP Node.js (get_invoices_by_supplier, get_purchase_orders)
  ↓
Python validation_engine (valida lógica)
  ↓
Genera asientos contables
  ↓
Retorna a CODEX para registración en CALIPSO
```

### Ejemplo de integración:

```python
#!/usr/bin/env python3
import subprocess
import json
from validation_engine import InvoiceValidator, Invoice, InvoiceItem

# 1. Obtener datos del MCP Node.js
result = subprocess.run([
    "node", "-e", 
    "const mcp = require('./mcp-client'); " +
    "mcp.call('get_invoices_by_supplier', {'supplier_name': 'ACME'})"
], capture_output=True, text=True)

invoice_data = json.loads(result.stdout)

# 2. Crear objetos
invoice = Invoice(**invoice_data)

# 3. Validar
validator = InvoiceValidator()
validation = validator.validate_invoice_against_po(invoice, po)

# 4. Generar asientos
entries = validator.generate_accounting_entries(invoice, "CC_001", "CXP_001")

# 5. Retornar a CODEX
print(json.dumps({
    "validation": validation.to_dict(),
    "accounting_entries": entries
}))
```

---

## 📝 Casos de Uso

### Caso 1: Validación de Factura Simple
```python
# Validar factura recibida por correo contra OC existente
validator = InvoiceValidator()
result = validator.validate_invoice_against_po(invoice, po)

if result.is_valid:
    # Generar asientos contables automáticamente
    entries = validator.generate_accounting_entries(invoice, cc_id, cxp_id)
    # Registrar en CALIPSO
else:
    # Notificar usuario
    print(result.to_json())
```

### Caso 2: Detección de Anomalías
```python
# Comparar con histórico de compras al proveedor
historical_data = get_supplier_purchase_history('ACME')
result = validator.detect_anomalies(invoice, historical_data)

if result.warnings:
    # Requerir revisión adicional
    approve_with_review(invoice, result.warnings)
```

### Caso 3: Procesamiento en Lote
```python
# Procesar correo con múltiples facturas
processor = BulkInvoiceProcessor(validator)
results = processor.process_invoices(invoices, purchase_orders)

# Generar reporte
save_report(f"facturas_procesadas_{date}.json", results)
```

---

## ⚙️ Configuración Avanzada

### Ajustar tolerancias de validación

```python
validator = InvoiceValidator()
validator.price_variance_threshold = 0.10  # 10% en lugar de 5%
validator.quantity_variance_threshold = 0.05  # 5% en lugar de 2%
```

### Customizar asientos contables

```python
# Definir mapeo de cuentas
account_mapping = {
    "PRODUCTO_A": "4101",  # Cuenta de compra
    "PRODUCTO_B": "4102",
    "IVA": "2105"  # Cuenta IVA Crédito
}

# Usar en generación
entries = validator.generate_accounting_entries(
    invoice, 
    cost_center_id="4101",
    account_payable_id="2001"
)
```

---

## 🐛 Troubleshooting

### "ModuleNotFoundError: No module named 'validation_engine'"
```powershell
# Asegurar que estás en el directorio correcto
cd C:\claudecode\proyectos-ingenio\automatizacion-facturas\python

# Verificar que el archivo existe
dir validation_engine.py

# Intentar de nuevo
python validation_engine.py
```

### Errores de encoding
```powershell
# Definir encoding explícitamente
$env:PYTHONIOENCODING = "utf-8"
python validation_engine.py
```

### Problemas de imports
```powershell
# Instalar dependencias (si las hubiera)
pip install -r requirements.txt

# Verificar Python path
python -c "import sys; print(sys.path)"
```

---

## 📞 Próximos Pasos

1. ✅ Módulo de validación creado
2. ⏳ Instalar Python 3.11+
3. ⏳ Crear requirements.txt con dependencias
4. ⏳ Integrar con web interface (facturas_validacion.html)
5. ⏳ Crear bridge MCP Node.js ↔ Python
6. ⏳ Setup de webhooks para n8n
