/**
 * server.js — API Backend Automatización Facturas
 * Ingenio La Corona — 2026
 *
 * Endpoints:
 *   GET  /api/facturas/pendientes
 *   GET  /api/facturas/:id
 *   GET  /api/facturas/:id/items
 *   GET  /api/oc/proveedor/:cuit
 *   GET  /api/oc/:nro/items
 *   GET  /api/constancias/oc/:nro
 *   GET  /api/resumen
 *   POST /api/facturas/:id/aprobar
 *   POST /api/facturas/:id/rechazar
 *   POST /api/validar
 *   GET  /api/health
 */

import 'dotenv/config';
import express from 'express';
import cors from 'cors';
import { log } from './logger.js';
import { facturasRouter } from './routes/facturas.js';
import { validarRouter } from './routes/validar.js';
import { ocRouter } from './routes/ordencompra.js';

const app = express();
const PORT = parseInt(process.env.PORT, 10) || 3000;

// ---------------------------------------------------------------------------
//  Middleware
// ---------------------------------------------------------------------------
app.use(cors());
app.use(express.json({ limit: '2mb' }));

// Log de cada request
app.use((req, _res, next) => {
  log.info(`${req.method} ${req.path} ${req.ip}`);
  next();
});

// ---------------------------------------------------------------------------
//  Rutas
// ---------------------------------------------------------------------------
app.get('/api/health', async (_req, res) => {
  res.json({ status: 'ok', timestamp: new Date().toISOString(), service: 'facturas-backend' });
});

app.use('/api/facturas', facturasRouter);
app.use('/api', validarRouter);
app.use('/api/oc', ocRouter);

// ---------------------------------------------------------------------------
//  Manejo de errores
// ---------------------------------------------------------------------------
// eslint-disable-next-line no-unused-vars
app.use((err, _req, res, _next) => {
  log.error(err.stack || err.message);
  res.status(500).json({ error: err.message || 'Error interno del servidor' });
});

// ---------------------------------------------------------------------------
//  Arranque
// ---------------------------------------------------------------------------
app.listen(PORT, () => {
  log.info(`Backend Facturas escuchando en http://localhost:${PORT}`);
  log.info(`Healthcheck: http://localhost:${PORT}/api/health`);
});
