// assets/js/dataService.js
// Simple data service abstraction. In production it will call the Node‑RED endpoint.
// For now we just expose the mock data.
import { mock } from './mockData.js';

/**
 * Obtiene los datos del tablero.
 * En futuro podrá recibir un endpoint vía CONFIG y hacer fetch.
 */
export async function getDashboardData() {
  // TODO: if (CONFIG.API_ENDPOINT) { fetch(...); }
  return Promise.resolve(mock);
}
