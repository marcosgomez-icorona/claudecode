# Flujos Node-RED — Sumas y Saldos

## Fuente canónica

Los flows **desplegados en Node-RED** (`192.168.0.23:1880`) son la fuente canónica.
Las copias en esta carpeta son **referencia para desarrollo y backup**.

## Backups

Para generar un backup actualizado desde Node-RED:

```bash
source MCPs/mcp-nodered/.env
curl -s "http://192.168.0.23:1880/flows" \
  -H "Authorization: Bearer ${NODE_RED_TOKEN}" \
  > gerencia_sumas_y_saldos/flujos/node-red/back-end-sumas-saldos.json
```

## Tabs desplegados (605 nodos en 13 tabs)

| Tab | Propósito |
|-----|-----------|
| Sumas y Saldos - Gerencia | Dashboard API + HTML (72 nodos) |
| Sumas y Saldos - API Snapshots | Endpoints REST snapshots (10 nodos) |
| Sumas y Saldos - Snapshots | Setup MySQL tables (6 nodos) |
| CONCILIACION - Galicia | Conciliación bancaria (34 nodos) |
| CONCILIACION - Dashboard | Dashboard conciliación (17 nodos) |
| Despachos Pendientes | Agente despachos |
| FACTURAS A REGISTRAR | API facturas a registrar |
| Registración de Facturas | Registro en Calipso |
| Facturas API | API unificada de facturas |
| Facturas por Email | Envío de facturas |
| Sync Calipso | Sincronización ERP |
| Facturacion Venta MVP | Facturación de venta |
| Export Clientes | Exportación de clientes |

## ⚠️ Recuperación post-incidente

El 2026-06-23 se restauraron 605 nodos en 13 tabs desde backups locales.
Faltan restaurar ~16 tabs de IoT/PLC/Telegram que requieren backup externo.

Para restaurar completamente:
1. `npm install -g node-red-node-mysql` (si falta el módulo MySQL)
2. Hacer clic en "Deploy" desde la UI de Node-RED
3. Restaurar tabs IoT desde backup de producción
