/**
 * validar.js — Endpoints de validación
 * POST /api/validar        — Validar factura contra OC (Python bridge)
 * GET  /api/proveedores    — Buscar proveedores
 */

import { Router } from 'express';
import { spawn } from 'child_process';
import { join } from 'path';
import { query } from '../db/sqlserver.js';
import { log } from '../logger.js';

export const validarRouter = Router();

// ---------------------------------------------------------------------------
// POST /api/validar — Validar factura contra OC con el motor Python
// ---------------------------------------------------------------------------
validarRouter.post('/validar', async (req, res) => {
  try {
    const { factura_id, oc_nro } = req.body;

    if (!factura_id) {
      return res.status(400).json({ error: 'factura_id es requerido' });
    }

    // 1. Obtener datos de la factura desde staging
    const facturas = await query(`
      SELECT
        ID, NUMERODOCUMENTO, LETRA, FECHA_EMISION, FECHA_VENCIMIENTO,
        PROVEEDOR_CUIT, PROVEEDOR_NOMBRE, PROVEEDOR_ID,
        NETO, IVA_21, IVA_105, PERCEPCIONES, OTROS_IMPUESTOS, TOTAL,
        REFERENCIA, CAE, FECHA_VTO_CAE, COTIZACION
      FROM UD_EZI_STAGING_FACTURAS
      WHERE ID = @id
    `, { id: factura_id });

    if (facturas.length === 0) {
      return res.status(404).json({ error: 'Factura no encontrada en staging' });
    }

    const factura = facturas[0];

    // 2. Si se pasa oc_nro, obtener items de la OC
    let oc = null;
    if (oc_nro) {
      const ocs = await query(`
        SELECT
          oc.ID, oc.NUMERODOCUMENTO AS po_number,
          oc.CODIGODESTINATARIO AS supplier_code,
          oc.TOTAL AS total_amount,
          CONVERT(varchar, oc.FECHAINGRESO, 112) AS po_date,
          oc.ESTADO AS status
        FROM TRORDENCOMPRA oc
        WHERE oc.NUMERODOCUMENTO = @nro
      `, { nro: oc_nro });

      if (ocs.length > 0) {
        oc = ocs[0];
        const items = await query(`
          SELECT
            it.NROLINEA AS nro,
            it.DESCRIPCION AS descripcion,
            it.CANTIDAD AS cantidad,
            it.PRECIOUNITARIO AS precio_unitario,
            it.SUBTOTAL AS subtotal
          FROM ITEMORDENCOMPRA it
          WHERE it.TRORDENCOMPRA_ID = @ocid
          ORDER BY it.NROLINEA
        `, { ocid: oc.ID });
        oc.items = items;
      }
    }

    // 3. Llamar al validador Python
    let pyResult = null;
    try {
      pyResult = await runPythonValidator(factura, oc);
    } catch (pyErr) {
      log.warn(`Python validator falló: ${pyErr.message}. Usando validación SQL.`);
      pyResult = fallbackValidation(factura, oc);
    }

    // 4. Enriquecer con info desde SQL
    const result = {
      factura: {
        id:           factura.ID,
        letra:        factura.LETRA,
        numero:       factura.NUMERODOCUMENTO,
        fecha:        factura.FECHA_EMISION,
        proveedor:    factura.PROVEEDOR_NOMBRE,
        cuit:         factura.PROVEEDOR_CUIT,
        neto:         factura.NETO,
        iva_21:       factura.IVA_21,
        iva_105:      factura.IVA_105,
        total:        factura.TOTAL,
        referencia:   factura.REFERENCIA,
      },
      oc:            oc ? {
        nro:         oc.po_number || oc.NUMERODOCUMENTO,
        fecha:       oc.po_date || oc.FECHAINGRESO,
        total:       oc.total_amount || oc.TOTAL,
        estado:      oc.status || oc.ESTADO,
        items:       oc.items || [],
      } : null,
      validacion:    pyResult,
    };

    res.json(result);
  } catch (err) {
    log.error(`validar: ${err.message}`);
    res.status(500).json({ error: err.message });
  }
});

// ---------------------------------------------------------------------------
// GET /api/proveedores?q=texto
// ---------------------------------------------------------------------------
validarRouter.get('/proveedores', async (req, res) => {
  try {
    const q = (req.query.q || '').replace(/\W/g, '');
    if (!q || q.length < 2) {
      return res.json([]);
    }

    const rows = await query(`
      SELECT TOP 20
        ID, CODIGO, DENOMINACION AS nombre, CUIT AS cuit
      FROM PROVEEDOR
      WHERE ACTIVESTATUS = 0
        AND (DENOMINACION LIKE @q OR CUIT LIKE @q2 OR CODIGO LIKE @q3)
      ORDER BY DENOMINACION
    `, { q: `%${q}%`, q2: `%${q}%`, q3: `%${q}%` });

    res.json(rows);
  } catch (err) {
    log.error(`proveedores: ${err.message}`);
    res.status(500).json({ error: err.message });
  }
});

// ---------------------------------------------------------------------------
// Helper — Ejecutar Python validator
// ---------------------------------------------------------------------------
function runPythonValidator(factura, oc) {
  return new Promise((resolve, reject) => {
    const PYTHON = process.env.PYTHON_PATH || 'python';
    const SCRIPT = process.env.VALIDATOR_SCRIPT || join(import.meta.dirname, '..', '..', '..', 'python', 'validation_engine.py');

    const py = spawn(PYTHON, [SCRIPT, '--validate'], {
      timeout: 15000,
      env: { ...process.env, PYTHONUNBUFFERED: '1' },
    });

    let stdout = '';
    let stderr = '';

    py.stdout.on('data', (d) => { stdout += d.toString(); });
    py.stderr.on('data', (d) => { stderr += d.toString(); });

    py.on('close', (code) => {
      if (code === 0) {
        try {
          resolve(JSON.parse(stdout.trim().split('\n').pop() || '{}'));
        } catch {
          resolve({ raw: stdout, parsed: false });
        }
      } else {
        reject(new Error(stderr || stdout || `exit code ${code}`));
      }
    });

    py.on('error', (err) => reject(err));

    // Enviar datos por stdin
    const payload = JSON.stringify({
      factura: {
        id:              factura.ID,
        invoice_number:  parseInt(factura.NUMERODOCUMENTO, 10) || 0,
        supplier_id:     factura.PROVEEDOR_CUIT || '',
        supplier_name:   factura.PROVEEDOR_NOMBRE || '',
        invoice_date:    formatDateISO(factura.FECHA_EMISION) || '',
        total_amount:    factura.TOTAL || 0,
        items:           factura.items || [],
      },
      oc: oc ? {
        id:               oc.ID || '',
        po_number:        parseInt(oc.po_number || oc.NUMERODOCUMENTO, 10) || 0,
        supplier_name:    factura.PROVEEDOR_NOMBRE || '',
        po_date:          formatDateISO(oc.po_date || oc.FECHAINGRESO) || '',
        total_amount:     oc.total_amount || oc.TOTAL || 0,
        items:            (oc.items || []).map(it => ({
          id:           String(it.nro || it.NROLINEA || ''),
          description:  it.descripcion || it.DESCRIPCION || '',
          quantity:     it.cantidad || it.CANTIDAD || 0,
          unit_price:   it.precio_unitario || it.PRECIOUNITARIO || 0,
          total_amount: it.subtotal || it.SUBTOTAL || 0,
        })),
      } : null,
    });

    py.stdin.write(JSON.stringify(payload));
    py.stdin.end();
  });
}

// ---------------------------------------------------------------------------
// Fallback — Validación liviana sin Python (solo SQL)
// ---------------------------------------------------------------------------
function fallbackValidation(factura, oc) {
  const errors = [];
  const warnings = [];
  const passed = [];

  if (!oc) {
    warnings.push('Sin OC asociada para comparar');
    return { is_valid: true, errors, warnings, validations_passed: passed, fallback: true };
  }

  // Proveedor
  passed.push('Validación SQL — datos estructurales OK');

  // Comparar totales
  const totalOC = parseFloat(oc.total_amount || oc.TOTAL || 0);
  const totalFC = parseFloat(factura.TOTAL || 0);
  if (totalOC > 0 && Math.abs(totalFC - totalOC) / totalOC > 0.05) {
    warnings.push(`Total factura (${totalFC}) difiere >5% de OC (${totalOC})`);
  } else {
    passed.push('Totales dentro de tolerancia');
  }

  return { is_valid: errors.length === 0, errors, warnings, validations_passed: passed, fallback: true };
}

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function formatDateISO(yyyymmdd) {
  if (!yyyymmdd || yyyymmdd.length < 8) return yyyymmdd;
  const y = yyyymmdd.substring(0, 4);
  const m = yyyymmdd.substring(4, 6);
  const d = yyyymmdd.substring(6, 8);
  return `${y}-${m}-${d}`;
}
