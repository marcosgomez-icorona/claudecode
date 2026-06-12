/**
 * ordencompra.js — Endpoints de Órdenes de Compra y Constancias
 * GET /api/oc/proveedor/:cuit
 * GET /api/oc/:nro/items
 * GET /api/constancias/oc/:nro
 */

import { Router } from 'express';
import { query } from '../db/sqlserver.js';
import { log } from '../logger.js';

export const ocRouter = Router();

// ---------------------------------------------------------------------------
// GET /api/oc/proveedor/:cuit  — OCs de un proveedor
// ---------------------------------------------------------------------------
ocRouter.get('/proveedor/:cuit', async (req, res) => {
  try {
    const cuit = req.params.cuit.replace(/\D/g, '');

    // Buscar el proveedor por CUIT
    const prov = await query(`
      SELECT ID, CODIGO, DENOMINACION, CUIT
      FROM PROVEEDOR
      WHERE CUIT = @cuit AND ACTIVESTATUS = 0
    `, { cuit });

    if (prov.length === 0) {
      return res.json([]);
    }

    // Obtener OCs (TRORDENCOMPRA) del proveedor
    // Unimos con TRFACTURACOMPRA para detectar cuales ya tienen factura
    const ocs = await query(`
      SELECT
        oc.ID                       AS id,
        oc.NUMERODOCUMENTO          AS nro,
        CONVERT(varchar, oc.FECHAINGRESO, 112) AS fecha,
        oc.TOTAL                    AS total,
        oc.ESTADO                   AS estado,
        -- Flag: ¿ya tiene factura registrada?
        CASE WHEN EXISTS (
          SELECT 1 FROM TRFACTURACOMPRA fc
          WHERE fc.DETALLE = oc.NUMERODOCUMENTO
        ) THEN 1 ELSE 0 END         AS facturada,
        -- ¿Requiere constancia? (empieza con 01-)
        CASE WHEN oc.NUMERODOCUMENTO LIKE '01-%' THEN 1 ELSE 0 END
                                    AS requiere_constancia
      FROM TRORDENCOMPRA oc
      WHERE oc.CODIGODESTINATARIO = @cod
        AND oc.ESTADO IN ('A', 'P', 'C')
      ORDER BY oc.FECHAINGRESO DESC
    `, { cod: prov[0].CODIGO });

    res.json(ocs);
  } catch (err) {
    log.error(`oc proveedor ${req.params.cuit}: ${err.message}`);
    res.status(500).json({ error: err.message });
  }
});

// ---------------------------------------------------------------------------
// GET /api/oc/:nro/items  — Items de una OC
// ---------------------------------------------------------------------------
ocRouter.get('/:nro/items', async (req, res) => {
  try {
    const nro = req.params.nro;

    const items = await query(`
      SELECT
        it.NROLINEA                 AS nro,
        it.DESCRIPCION              AS descripcion,
        it.CANTIDAD                 AS cantidad,
        it.PRECIOUNITARIO           AS precio_unitario,
        it.SUBTOTAL                 AS subtotal
      FROM ITEMORDENCOMPRA it
      JOIN TRORDENCOMPRA oc ON oc.ID = it.TRORDENCOMPRA_ID
      WHERE oc.NUMERODOCUMENTO = @nro
      ORDER BY it.NROLINEA
    `, { nro });

    res.json(items);
  } catch (err) {
    log.error(`oc items ${req.params.nro}: ${err.message}`);
    res.status(500).json({ error: err.message });
  }
});

// ---------------------------------------------------------------------------
// GET /api/constancias/oc/:nro  — Constancias de servicio asociadas a una OC
// ---------------------------------------------------------------------------
ocRouter.get('/constancias/oc/:nro', async (req, res) => {
  try {
    // Las constancias de servicio son OCs de tipo SERVICIO
    // vinculadas a la OC principal (comienzan con mismo prefijo 01-)
    const nro = req.params.nro;

    // Buscar constancias (son TRORDENCOMPRA de tipo ConstServ)
    const constancias = await query(`
      SELECT
        oc.ID                       AS id_calipso,
        oc.NUMERODOCUMENTO          AS nro,
        CONVERT(varchar, oc.FECHAINGRESO, 112) AS fecha,
        oc.DETALLE                  AS detalle,
        oc.TOTAL                    AS total,
        CASE WHEN EXISTS (
          SELECT 1 FROM TRFACTURACOMPRA fc
          WHERE fc.DETALLE = oc.NUMERODOCUMENTO
        ) THEN 1 ELSE 0 END         AS facturada
      FROM TRORDENCOMPRA oc
      WHERE oc.TIPOTRANSACCION_ID IN (
        'E5887DA3-618D-11D5-931E-00E07D9040B9',
        '08D2A275-E50E-47B5-90A6-5E06088DA3CA'
      )
        AND oc.ESTADO = 'C'
        AND oc.NUMERODOCUMENTO LIKE @prefijo + '%'
      ORDER BY oc.FECHAINGRESO DESC
    `, { prefijo: nro.substring(0, 5) });

    res.json(constancias);
  } catch (err) {
    log.error(`constancias ${req.params.nro}: ${err.message}`);
    res.status(500).json({ error: err.message });
  }
});
