"""
validation_engine.py
Módulo Python para validación inteligente de facturas contra órdenes de compra.
Diseñado para integración con MCP Node.js y CODEX.

Funcionalidades:
- Validación de facturas vs OC
- Cálculo de totales y impuestos
- Detección de anomalías
- Generación de asientos contables
- Análisis de variaciones de precio
"""

import json
from datetime import datetime, timedelta
from dataclasses import dataclass, asdict
from typing import List, Dict, Optional, Tuple
from decimal import Decimal
import logging

# Configurar logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)


@dataclass
class InvoiceItem:
    """Representa un item de factura"""
    id: str
    description: str
    quantity: float
    unit_price: float
    total_amount: float
    tax_rate: float = 0.21  # IVA Argentina 21%
    tax_amount: float = 0.0

    def __post_init__(self):
        """Calcula el monto de impuesto si no está especificado"""
        if self.tax_amount == 0.0:
            self.tax_amount = self.total_amount * self.tax_rate

    def get_gross_amount(self) -> float:
        """Retorna el total incluyendo impuestos"""
        return self.total_amount + self.tax_amount


@dataclass
class PurchaseOrder:
    """Representa una Orden de Compra"""
    id: str
    po_number: int
    supplier_id: str
    supplier_name: str
    po_date: str
    delivery_date: str
    total_amount: float
    items: List[InvoiceItem]
    status: str = "ABIERTA"  # ABIERTA, PARCIALMENTE_RECIBIDA, RECIBIDA, CANCELADA

    def get_total_with_tax(self) -> float:
        """Calcula total de OC con impuestos"""
        return sum(item.get_gross_amount() for item in self.items)


@dataclass
class Invoice:
    """Representa una Factura de Compra"""
    id: str
    invoice_number: int
    supplier_id: str
    supplier_name: str
    invoice_date: str
    total_amount: float
    items: List[InvoiceItem]
    po_id: Optional[str] = None
    po_number: Optional[int] = None
    status: str = "PENDIENTE"  # PENDIENTE, VALIDADA, RECHAZADA, REGISTRADA

    def get_total_with_tax(self) -> float:
        """Calcula total de factura con impuestos"""
        return sum(item.get_gross_amount() for item in self.items)


class ValidationResult:
    """Resultado de validación de una factura"""

    def __init__(self):
        self.is_valid = True
        self.warnings: List[str] = []
        self.errors: List[str] = []
        self.validations_passed: List[str] = []
        self.suggestions: List[str] = []
        self.variance_analysis: Dict = {}
        self.timestamp = datetime.now().isoformat()

    def add_error(self, message: str) -> None:
        """Agrega un error (validación falla)"""
        self.errors.append(message)
        self.is_valid = False
        logger.error(f"❌ Error: {message}")

    def add_warning(self, message: str) -> None:
        """Agrega una advertencia (validación pasa pero con reservas)"""
        self.warnings.append(message)
        logger.warning(f"⚠️  Advertencia: {message}")

    def add_suggestion(self, message: str) -> None:
        """Agrega una sugerencia de mejora"""
        self.suggestions.append(message)
        logger.info(f"💡 Sugerencia: {message}")

    def add_pass(self, message: str) -> None:
        """Registra validación que pasó"""
        self.validations_passed.append(message)
        logger.info(f"✅ Validación OK: {message}")

    def to_dict(self) -> Dict:
        """Convierte resultado a diccionario JSON"""
        return {
            "is_valid": self.is_valid,
            "timestamp": self.timestamp,
            "validations_passed": self.validations_passed,
            "warnings": self.warnings,
            "errors": self.errors,
            "suggestions": self.suggestions,
            "variance_analysis": self.variance_analysis
        }

    def to_json(self) -> str:
        """Convierte resultado a JSON string"""
        return json.dumps(self.to_dict(), indent=2)


class InvoiceValidator:
    """Motor principal de validación de facturas"""

    def __init__(self):
        self.price_variance_threshold = 0.05  # 5% de variación permitida
        self.quantity_variance_threshold = 0.02  # 2% en cantidad

    def validate_invoice_against_po(
        self, invoice: Invoice, purchase_order: PurchaseOrder
    ) -> ValidationResult:
        """
        Valida una factura contra una orden de compra.

        Comprobaciones:
        - Proveedor coincide
        - Número de items coincide
        - Cantidades coinciden
        - Precios unitarios son razonables
        - Total es correcto
        """
        result = ValidationResult()
        logger.info(f"🔍 Iniciando validación de factura #{invoice.invoice_number} contra OC #{purchase_order.po_number}")

        # 1. Validar que proveedores coincidan
        if invoice.supplier_id != purchase_order.supplier_id:
            result.add_error(f"Proveedor en factura no coincide con OC: {invoice.supplier_name} vs {purchase_order.supplier_name}")
        else:
            result.add_pass("Proveedor coincide")

        # 2. Validar cantidad de items
        if len(invoice.items) != len(purchase_order.items):
            result.add_warning(
                f"Cantidad de items diferente: Factura={len(invoice.items)}, OC={len(purchase_order.items)}"
            )

        # 3. Validar cada item
        variance_summary = {}
        for inv_item in invoice.items:
            po_item = next(
                (item for item in purchase_order.items if item.description == inv_item.description),
                None
            )

            if not po_item:
                result.add_warning(f"Item '{inv_item.description}' en factura no existe en OC")
                continue

            # Verificar cantidad
            qty_variance = abs(inv_item.quantity - po_item.quantity) / po_item.quantity
            if qty_variance > self.quantity_variance_threshold:
                result.add_warning(
                    f"Variación de cantidad en '{inv_item.description}': "
                    f"{inv_item.quantity} vs {po_item.quantity} ({qty_variance*100:.2f}%)"
                )
            else:
                result.add_pass(f"Cantidad OK: {inv_item.description}")

            # Verificar precio unitario
            price_variance = abs(inv_item.unit_price - po_item.unit_price) / po_item.unit_price
            if price_variance > self.price_variance_threshold:
                result.add_error(
                    f"Variación de precio significativa en '{inv_item.description}': "
                    f"${inv_item.unit_price} vs ${po_item.unit_price} ({price_variance*100:.2f}%)"
                )
                variance_summary[inv_item.description] = {
                    "invoice_price": inv_item.unit_price,
                    "po_price": po_item.unit_price,
                    "variance_percent": price_variance * 100
                }
            else:
                result.add_pass(f"Precio unitario OK: {inv_item.description}")

        # 4. Validar total
        po_total = purchase_order.get_total_with_tax()
        inv_total = invoice.get_total_with_tax()
        total_variance = abs(inv_total - po_total) / po_total if po_total > 0 else 0

        if total_variance > 0.01:  # 1% de tolerancia
            result.add_error(
                f"Total de factura no coincide: ${inv_total} vs ${po_total} "
                f"(variación: {total_variance*100:.2f}%)"
            )
        else:
            result.add_pass(f"Total correcto: ${inv_total}")

        # 5. Validar fechas
        po_date = datetime.fromisoformat(purchase_order.po_date)
        inv_date = datetime.fromisoformat(invoice.invoice_date)

        if inv_date < po_date:
            result.add_error("Fecha de factura anterior a fecha de OC")
        elif (inv_date - po_date).days > 60:
            result.add_warning(f"Factura recibida {(inv_date - po_date).days} días después de OC")
        else:
            result.add_pass("Fecha de factura válida")

        # 6. Guardar análisis de varianzas
        result.variance_analysis = variance_summary

        return result

    def calculate_invoice_totals(self, invoice: Invoice) -> Dict:
        """
        Calcula totales de factura desglosados por componentes.
        """
        subtotal = sum(item.total_amount for item in invoice.items)
        total_tax = sum(item.tax_amount for item in invoice.items)
        gross_total = subtotal + total_tax

        return {
            "subtotal": round(subtotal, 2),
            "total_tax_21": round(total_tax, 2),
            "gross_total": round(gross_total, 2),
            "item_count": len(invoice.items),
            "average_item_price": round(subtotal / len(invoice.items), 2) if invoice.items else 0
        }

    def detect_anomalies(self, invoice: Invoice, historical_data: List[Dict]) -> ValidationResult:
        """
        Detecta anomalías comparando con datos históricos del proveedor.

        Anomalías detectadas:
        - Precios inusualmente altos o bajos
        - Cantidad inesperada
        - Desviación de promedio histórico
        """
        result = ValidationResult()
        logger.info(f"🔎 Detectando anomalías en factura #{invoice.invoice_number}")

        if not historical_data:
            result.add_pass("Sin datos históricos para comparación")
            return result

        # Calcular promedios históricos
        historical_prices = {}
        for record in historical_data:
            item_desc = record.get('description')
            if item_desc:
                if item_desc not in historical_prices:
                    historical_prices[item_desc] = []
                historical_prices[item_desc].append(record.get('unit_price', 0))

        # Analizar cada item de la factura
        for item in invoice.items:
            if item.description in historical_prices:
                historical_avg = sum(historical_prices[item.description]) / len(historical_prices[item.description])
                deviation = abs(item.unit_price - historical_avg) / historical_avg

                if deviation > 0.20:  # 20% de desviación
                    result.add_warning(
                        f"Precio inusual en '{item.description}': ${item.unit_price} "
                        f"(promedio histórico: ${historical_avg:.2f}, desviación: {deviation*100:.1f}%)"
                    )
                else:
                    result.add_pass(f"Precio dentro de rangos normales: {item.description}")

        return result

    def generate_accounting_entries(
        self, invoice: Invoice, cost_center_id: str, account_payable_id: str
    ) -> List[Dict]:
        """
        Genera asientos contables para registro en Calipso.

        Estructura del asiento:
        - Debe: Cuenta de gasto/inventario
        - Haber: Cuenta por pagar
        """
        entries = []

        for item in invoice.items:
            entry = {
                "fecha": invoice.invoice_date,
                "descripcion": f"Compra: {item.description} - Factura #{invoice.invoice_number}",
                "cuenta_debe": cost_center_id,  # Cuenta de gasto/inventario
                "monto_debe": round(item.total_amount, 2),
                "cuenta_haber": account_payable_id,
                "monto_haber": round(item.get_gross_amount(), 2),
                "referencia": f"INV-{invoice.invoice_number}-ITEM-{item.id}",
                "tipo_documento": "FACTURA_COMPRA",
                "proveedor_id": invoice.supplier_id
            }
            entries.append(entry)

        # Agregar asiento de impuesto si aplica
        total_tax = sum(item.tax_amount for item in invoice.items)
        if total_tax > 0:
            tax_entry = {
                "fecha": invoice.invoice_date,
                "descripcion": f"IVA Compra - Factura #{invoice.invoice_number}",
                "cuenta_debe": "CUENTA_IVA_CREDITO",  # IVA Crédito Fiscal
                "monto_debe": round(total_tax, 2),
                "cuenta_haber": account_payable_id,
                "monto_haber": round(total_tax, 2),
                "referencia": f"INV-{invoice.invoice_number}-IVA",
                "tipo_documento": "FACTURA_COMPRA",
                "proveedor_id": invoice.supplier_id
            }
            entries.append(tax_entry)

        return entries


class BulkInvoiceProcessor:
    """Procesa múltiples facturas en lote"""

    def __init__(self, validator: InvoiceValidator):
        self.validator = validator
        self.processing_log = []

    def process_invoices(
        self, invoices: List[Invoice], purchase_orders: Dict[str, PurchaseOrder]
    ) -> Dict:
        """Procesa múltiples facturas y retorna reporte"""
        results = {
            "timestamp": datetime.now().isoformat(),
            "total_invoices": len(invoices),
            "valid_invoices": 0,
            "invoices_with_warnings": 0,
            "rejected_invoices": 0,
            "details": []
        }

        for invoice in invoices:
            po = purchase_orders.get(invoice.po_id) if invoice.po_id else None

            if not po:
                logger.warning(f"OC no encontrada para factura #{invoice.invoice_number}")
                results["rejected_invoices"] += 1
                results["details"].append({
                    "invoice_number": invoice.invoice_number,
                    "status": "RECHAZADA",
                    "reason": "OC no encontrada"
                })
                continue

            validation_result = self.validator.validate_invoice_against_po(invoice, po)
            results["details"].append({
                "invoice_number": invoice.invoice_number,
                "status": "VALIDADA" if validation_result.is_valid else "RECHAZADA",
                "validation": validation_result.to_dict()
            })

            if validation_result.is_valid:
                results["valid_invoices"] += 1
            elif validation_result.warnings:
                results["invoices_with_warnings"] += 1
            else:
                results["rejected_invoices"] += 1

        return results


# Ejemplo de uso
if __name__ == "__main__":
    # Crear items de OC
    po_items = [
        InvoiceItem(
            id="po_item_1",
            description="Producto A",
            quantity=100,
            unit_price=50.00,
            total_amount=5000.00
        ),
        InvoiceItem(
            id="po_item_2",
            description="Producto B",
            quantity=50,
            unit_price=100.00,
            total_amount=5000.00
        )
    ]

    # Crear OC
    po = PurchaseOrder(
        id="po_001",
        po_number=12345,
        supplier_id="prov_001",
        supplier_name="Proveedor XYZ",
        po_date="2024-05-01",
        delivery_date="2024-05-15",
        total_amount=10000.00,
        items=po_items
    )

    # Crear items de factura (con ligera variación)
    invoice_items = [
        InvoiceItem(
            id="inv_item_1",
            description="Producto A",
            quantity=100,
            unit_price=51.00,  # 2% más caro
            total_amount=5100.00
        ),
        InvoiceItem(
            id="inv_item_2",
            description="Producto B",
            quantity=50,
            unit_price=99.00,  # 1% más barato
            total_amount=4950.00
        )
    ]

    # Crear factura
    invoice = Invoice(
        id="inv_001",
        invoice_number=98765,
        supplier_id="prov_001",
        supplier_name="Proveedor XYZ",
        invoice_date="2024-05-10",
        total_amount=10050.00,
        items=invoice_items,
        po_id="po_001",
        po_number=12345
    )

    # Validar
    validator = InvoiceValidator()
    result = validator.validate_invoice_against_po(invoice, po)

    print("\n" + "="*60)
    print("RESULTADO DE VALIDACIÓN")
    print("="*60)
    print(result.to_json())

    # Calcular totales
    totals = validator.calculate_invoice_totals(invoice)
    print("\nTOTALES DE FACTURA:")
    print(json.dumps(totals, indent=2))

    # Generar asientos contables
    entries = validator.generate_accounting_entries(invoice, "CC_001", "CXP_001")
    print("\nASIENTOS CONTABLES GENERADOS:")
    print(json.dumps(entries, indent=2))
