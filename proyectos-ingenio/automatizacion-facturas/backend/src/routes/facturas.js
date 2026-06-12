/**
 * facturas.js — Endpoints de facturas (staging CALIPSO)
 * GET  /api/facturas/pendientes
 * GET  /api/facturas/:id
 * GET  /api/facturas/:id/items
 * GET  /api/resumen
 * POST /api/facturas/:id/aprobar
 * POST /api/facturas/:id/rechazar
 */

import { Router } from 'express';
import { query } from '../db/sqlserver.js';
import { log } from '../logger.js';

export const facturasRouter = Router();

// ---------------------------------------------------------------------------
// GET /api/facturas/pendientes
// ---------------------------------------------------------------------------
facturasRouter.get('/pendientes', async (_req, res) => {
  try {
    const rows = await query(`
      SELECT
        s.ID                         AS id,
        s.TIPO_OPERACION             AS tipo_operacion,
        s.ESTADO_PROCESO             AS estado_proceso,
        s.FECHA_CARGA                AS fecha_carga,
        s.USUARIO_CARGA              AS usuario_carga,
        s.APROBADO_POR               AS aprobado_por,
        s.ORIGEN                     AS origen,
        s.LETRA                      AS letra,
        s.NUMERODOCUMENTO            AS numerodocumento,
        s.FECHA_EMISION              AS fecha_emision,
        s.FECHA_VENCIMIENTO          AS fecha_vencimiento,
        s.PROVEEDOR_CUIT             AS proveedor_cuit,
        s.PROVEEDOR_NOMBRE           AS proveedor_nombre,
        s.PROVEEDOR_CODIGO           AS proveedor_codigo,
        s.REFERENCIA                 AS referencia,
        s.NETO                       AS neto,
        s.IVA_21                     AS iva_21,
        s.IVA_105                    AS iva_105,
        s.PERCEPCIONES               AS percepciones,
        s.OTROS_IMPUESTOS            AS otros_impuestos,
        s.TOTAL                      AS total,
        s.CAE                        AS cae,
        s.FECHA_VTO_CAE              AS fecha_vto_cae,
        s.PDF_FILENAME               AS pdf_filename,
        s.EMAIL_ORIGEN               AS email_origen,
        s.EMAIL_ASUNTO               AS email_asunto,
        s.COTIZACION                 AS cotizacion,
        m.NOMBRE                     AS moneda,
        -- Semáforo CAE
        CASE
          WHEN s.CAE IS NULL OR s.CAE = '' THEN 'SIN_CAE'
          WHEN s.FECHA_VTO_CAE < CONVERT(varchar(8), GETDATE(), 112) THEN 'CAE_VENCIDO'
          ELSE 'CAE_OK'
        END                           AS estado_cae,
        -- Dias en cola
        DATEDIFF(DAY,
          CONVERT(datetime, SUBSTRING(s.FECHA_CARGA, 1, 8), 112),
          GETDATE()
        )                             AS dias_en_cola
      FROM UD_EZI_STAGING_FACTURAS s
      LEFT JOIN MONEDA m ON m.ID = s.MONEDA_ID
      WHERE s.ESTADO_PROCESO IN ('PENDIENTE', 'EN_REVISION')
      ORDER BY s.FECHA_CARGA ASC
    `);

    res.json(rows);
  } catch (err) {
    log.error(`pendientes: ${err.message}`);
    res.status(500).json({ error: err.message });
  }
});

// ---------------------------------------------------------------------------
// GET /api/facturas/:id
// ---------------------------------------------------------------------------
facturasRouter.get('/:id', async (req, res) => {
  try {
    const rows = await query(`
      SELECT
        s.ID                         AS id,
        s.TIPO_OPERACION             AS tipo_operacion,
        s.ESTADO_PROCESO             AS estado_proceso,
        s.FECHA_CARGA                AS fecha_carga,
        s.USUARIO_CARGA              AS usuario_carga,
        s.APROBADO_POR               AS aprobado_por,
        s.ORIGEN                     AS origen,
        s.LETRA                      AS letra,
        s.NUMERODOCUMENTO            AS numerodocumento,
        s.FECHA_EMISION              AS fecha_emision,
        s.FECHA_VENCIMIENTO          AS fecha_vencimiento,
        s.PROVEEDOR_CUIT             AS proveedor_cuit,
        s.PROVEEDOR_NOMBRE           AS proveedor_nombre,
        s.PROVEEDOR_CODIGO           AS proveedor_codigo,
        s.PROVEEDOR_ID               AS proveedor_id,
        s.REFERENCIA                 AS referencia,
        s.NETO                       AS neto,
        s.IVA_21                     AS iva_21,
        s.IVA_105                    AS iva_105,
        s.PERCEPCIONES               AS percepciones,
        s.OTROS_IMPUESTOS            AS otros_impuestos,
        s.TOTAL                      AS total,
        s.MONEDA_ID                  AS moneda_id,
        s.COTIZACION                 AS cotizacion,
        s.CAE                        AS cae,
        s.FECHA_VTO_CAE              AS fecha_vto_cae,
        s.CENTROCOSTOS_NOMBRE        AS centro_costos,
        s.PDF_FILENAME               AS pdf_filename,
        s.PDF_HASH                   AS pdf_hash,
        s.EMAIL_ORIGEN               AS email_origen,
        s.EMAIL_ASUNTO               AS email_asunto,
        s.ERROR_DETALLE              AS error_detalle,
        s.TR_GENERADO_ID             AS tr_generado_id,
        m.NOMBRE                     AS moneda
      FROM UD_EZI_STAGING_FACTURAS s
      LEFT JOIN MONEDA m ON m.ID = s.MONEDA_ID
      WHERE s.ID = @id
    `, { id: req.params.id });

    if (rows.length === 0) {
      return res.status(404).json({ error: 'Factura no encontrada' });
    }
    res.json(rows[0]);
  } catch (err) {
    log.error(`get factura ${req.params.id}: ${err.message}`);
    res.status(500).json({ error: err.message });
  }
});

// ---------------------------------------------------------------------------
// GET /api/facturas/:id/items
// ---------------------------------------------------------------------------
facturasRouter.get('/:id/items', async (req, res) => {
  try {
    // Items se almacenan en STAGING_ITEMS si existe, o se extraen de JSON
    if (tableExists('UD_EZI_STAGING_ITEMS')) {
      const rows = await query(`
        SELECT
          LINEA         AS nro_linea,
          DESCRIPCION   AS descripcion,
          CANTIDAD      AS cantidad,
          UNIDAD        AS unidad,
          PRECIO_UNITARIO AS precio_unitario,
          ALICUOTA_IVA  AS iva_percent,
          SUBTOTAL      AS subtotal
        FROM UD_EZI_STAGING_ITEMS
        WHERE FACTURA_ID = @id
        ORDER BY LINEA
      `, { id: req.params.id });
      return res.json(rows);
    }

    // Fallback: extraer de staging_facturas.items_json
    const f = await query(
      `SELECT ITEMS_JSON FROM UD_EZI_STAGING_FACTURAS WHERE ID = @id`,
      { id: req.params.id }
    );
    if (f.length === 0 || !f[0].ITEMS_JSON) return res.json([]);

    const items = JSON.parse(f[0].ITEMS_JSON);
    res.json(items.map((it, i) => ({
      nro_linea:       i + 1,
      descripcion:     it.descripcion || it.description || '',
      cantidad:        it.cantidad || it.quantity || 0,
      precio_unitario: it.precio_unitario || it.unit_price || 0,
      iva_percent:     it.iva || it.tax_rate || 21,
      subtotal:        it.subtotal || it.total_amount || 0,
    })));
  } catch (err) {
    log.error(`items ${req.params.id}: ${err.message}`);
    res.status(500).json({ error: err.message });
  }
});

// ---------------------------------------------------------------------------
// POST /api/facturas/:id/aprobar
// ---------------------------------------------------------------------------
facturasRouter.post('/:id/aprobar', async (req, res) => {
  try {
    const { operador, notas } = req.body;
    const op = operador || 'web';

    // Actualizar estado a APROBADO
    const ts = dateToTS();

    const result = await query(`
      UPDATE UD_EZI_STAGING_FACTURAS
      SET ESTADO_PROCESO    = 'APROBADO',
          FECHA_APROBACION  = @ts,
          APROBADO_POR      = @operador
      WHERE ID = @id
        AND ESTADO_PROCESO IN ('PENDIENTE', 'EN_REVISION')

      IF @@ROWCOUNT = 0
        SELECT 'ERROR' AS resultado, 'Registro no encontrado o ya procesado' AS detalle
      ELSE
        SELECT 'OK' AS resultado,
               'Factura aprobada. Pendiente de carga en Calipso.' AS detalle
    `, { id: req.params.id, ts, operador: op });

    log.info(`Factura ${req.params.id} APROBADA por ${op}`);
    res.json(result[0] || { resultado: 'OK' });
  } catch (err) {
    log.error(`aprobar ${req.params.id}: ${err.message}`);
    res.status(500).json({ error: err.message });
  }
});

// ---------------------------------------------------------------------------
// POST /api/facturas/:id/rechazar
// ---------------------------------------------------------------------------
facturasRouter.post('/:id/rechazar', async (req, res) => {
  try {
    const { motivo, operador } = req.body;
    const op = operador || 'web';
    const ts = dateToTS();

    const result = await query(`
      UPDATE UD_EZI_STAGING_FACTURAS
      SET ESTADO_PROCESO    = 'RECHAZADO',
          FECHA_APROBACION  = @ts,
          APROBADO_POR      = @operador,
          ERROR_DETALLE     = @motivo
      WHERE ID = @id
        AND ESTADO_PROCESO IN ('PENDIENTE', 'EN_REVISION')

      IF @@ROWCOUNT = 0
        SELECT 'ERROR' AS resultado, 'Registro no encontrado o ya procesado' AS detalle
      ELSE
        SELECT 'OK' AS resultado,
               'Factura rechazada. Motivo: ' + @motivo AS detalle
    `, { id: req.params.id, ts, operador: op, motivo: motivo || '' });

    log.info(`Factura ${req.params.id} RECHAZADA por ${op}: ${motivo}`);
    res.json(result[0] || { resultado: 'OK' });
  } catch (err) {
    log.error(`rechazar ${req.params.id}: ${err.message}`);
    res.status(500).json({ error: err.message });
  }
});

// ---------------------------------------------------------------------------
// GET /api/resumen
// ---------------------------------------------------------------------------
facturasRouter.get('/resumen', async (_req, res) => {
  try {
    const rows = await query(`
      SELECT
        ESTADO_PROCESO   AS estado,
        COUNT(*)         AS cantidad,
        SUM(TOTAL)       AS total_acumulado,
        MIN(FECHA_CARGA) AS mas_antigua,
        MAX(FECHA_CARGA) AS mas_reciente
      FROM UD_EZI_STAGING_FACTURAS
      GROUP BY ESTADO_PROCESO
      ORDER BY
        CASE ESTADO_PROCESO
          WHEN 'APROBADO'    THEN 1
          WHEN 'PENDIENTE'   THEN 2
          WHEN 'EN_REVISION' THEN 3
          WHEN 'PROCESADO'   THEN 4
          WHEN 'RECHAZADO'   THEN 5
          WHEN 'ERROR'       THEN 6
          ELSE 7
        END
    `);
    res.json(rows);
  } catch (err) {
    log.error(`resumen: ${err.message}`);
    res.status(500).json({ error: err.message });
  }
});

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function dateToTS() {
  const d = new Date();
  const y = String(d.getFullYear());
  const m = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  const h = String(d.getHours()).padStart(2, '0');
  const min = String(d.getMinutes()).padStart(2, '0');
  const s = String(d.getSeconds()).padStart(2, '0');
  const ms = String(d.getMilliseconds()).padStart(3, '0');
  return `${y}${m}${day}${h}${min}${s}${ms}`;
}

let _tableCache = {};
async function tableExists(name) {
  if (name in _tableCache) return _tableCache[name];
  const r = await query(
    `SELECT 1 FROM sys.tables WHERE name = @name`,
    { name }
  );
  _tableCache[name] = r.length > 0;
  return _tableCache[name];
}
