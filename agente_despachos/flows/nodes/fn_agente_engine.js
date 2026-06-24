/**
 * fn_agente_engine.js — Motor de clasificación del Agente de Despachos
 * Flow: Despachos Pendientes v1.5.0
 *
 * INPUT:  msg.payload  → array de remitos (desde despachos_pendientes_cache)
 *         msg.reglas   → array de reglas (desde despachos_reglas_clasificacion)
 *         msg.runUuid  → UUID de la ejecución
 *
 * OUTPUT: msg.payload  → array de resultados {remito, estado_nuevo, motivo, puntaje, regla}
 *         msg.summary  → resumen {total, aptos, pendientes, requieren_aprob, bloqueados}
 *         msg.runUuid  → se propaga para auditoría
 *
 * REGLAS DE CLASIFICACIÓN (en orden de prioridad):
 *
 *   BLOQUEADO (datos críticos):
 *   - sin cliente, sin CUIT, sin producto
 *   - cantidad nula o cero
 *   - precio nulo, cero, o > $2000
 *
 *   REQUIERE_APROBACION_HUMANA (casos especiales):
 *   - producto de exportación (contiene "EXPO" o "GRADO 4")
 *   - importe total > $400.000
 *   - faltan transportista + es exportación
 *
 *   PENDIENTE_VALIDACION (datos secundarios):
 *   - falta guía
 *   - falta observaciones
 *   - falta destino
 *   - falta chofer
 *   - falta patente
 *
 *   APTO_PARA_PROGRAMAR (default):
 *   - pasó todas las verificaciones
 */

// ─── Helpers ────────────────────────────────────────────────────────────────

function esNulo(val) {
  return val === null || val === undefined;
}

function esVacio(val) {
  return esNulo(val) || (typeof val === 'string' && val.trim() === '');
}

function parseNum(val) {
  if (esNulo(val)) return NaN;
  var n = parseFloat(val);
  return isNaN(n) ? NaN : n;
}

// ─── Reglas embebidas (fallback si no hay reglas en MySQL) ─────────────────

var REGLAS_DEFAULT = [
  // BLOQUEADOS
  { id: null, nombre: 'sin_cliente',       estado: 'BLOQUEADO', campo: 'cliente',    tipo: 'ES_NULO',         motivo: 'Cliente no informado — dato crítico', puntaje: 100, orden: 10 },
  { id: null, nombre: 'sin_cuit',          estado: 'BLOQUEADO', campo: 'cuit',       tipo: 'ES_NULO',         motivo: 'CUIT no informado — dato crítico', puntaje: 100, orden: 11 },
  { id: null, nombre: 'sin_producto',      estado: 'BLOQUEADO', campo: 'producto',   tipo: 'ES_NULO',         motivo: 'Producto no informado — dato crítico', puntaje: 100, orden: 12 },
  { id: null, nombre: 'sin_cantidad',      estado: 'BLOQUEADO', campo: 'cantidad',   tipo: 'ES_NULO',         motivo: 'Cantidad no informada — dato crítico', puntaje: 100, orden: 13 },
  { id: null, nombre: 'cantidad_cero',     estado: 'BLOQUEADO', campo: 'cantidad',   tipo: 'MENOR_QUE', valor: '1', motivo: 'Cantidad = 0 — inconsistente', puntaje: 100, orden: 14 },
  { id: null, nombre: 'precio_nulo',       estado: 'BLOQUEADO', campo: 'precio',     tipo: 'ES_NULO',         motivo: 'Precio no informado — dato crítico', puntaje: 100, orden: 15 },
  { id: null, nombre: 'precio_cero',       estado: 'BLOQUEADO', campo: 'precio',     tipo: 'MENOR_QUE', valor: '1', motivo: 'Precio = 0 — inconsistente', puntaje: 100, orden: 16 },
  { id: null, nombre: 'precio_excesivo',   estado: 'BLOQUEADO', campo: 'precio',     tipo: 'MAYOR_QUE', valor: '2000', motivo: 'Precio > $2000 — posible error de carga', puntaje: 85, orden: 17 },

  // REQUIERE_APROBACION_HUMANA
  { id: null, nombre: 'exportacion',       estado: 'REQUIERE_APROBACION_HUMANA', campo: 'producto',  tipo: 'CONTIENE', valor: 'EXPO', motivo: 'Producto de exportación — revisión obligatoria', puntaje: 95, orden: 30 },
  { id: null, nombre: 'monto_alto',        estado: 'REQUIERE_APROBACION_HUMANA', campo: 'totalItem', tipo: 'MAYOR_QUE', valor: '400000', motivo: 'Importe elevado (>$400K) — requiere autorización', puntaje: 80, orden: 31 },
  { id: null, nombre: 'sin_transportista', estado: 'REQUIERE_APROBACION_HUMANA', campo: 'transportista', tipo: 'ES_NULO', motivo: 'Falta transportista — revisar logística', puntaje: 70, orden: 32 },

  // PENDIENTE_VALIDACION
  { id: null, nombre: 'sin_guia',          estado: 'PENDIENTE_VALIDACION', campo: 'guia',          tipo: 'ES_NULO', motivo: 'Falta número de guía', puntaje: 85, orden: 50 },
  { id: null, nombre: 'sin_observaciones', estado: 'PENDIENTE_VALIDACION', campo: 'observaciones', tipo: 'ES_VACIO', motivo: 'Falta observaciones del remito', puntaje: 70, orden: 51 },
  { id: null, nombre: 'sin_destino',       estado: 'PENDIENTE_VALIDACION', campo: 'destino',       tipo: 'ES_NULO', motivo: 'Falta destino de entrega', puntaje: 80, orden: 52 },
  { id: null, nombre: 'sin_chofer',        estado: 'PENDIENTE_VALIDACION', campo: 'chofer',        tipo: 'ES_NULO', motivo: 'Falta nombre del chofer', puntaje: 65, orden: 53 },
  { id: null, nombre: 'sin_patente',       estado: 'PENDIENTE_VALIDACION', campo: 'patente',       tipo: 'ES_NULO', motivo: 'Falta patente del vehículo', puntaje: 65, orden: 54 }
];

// ─── Evaluador de regla individual ─────────────────────────────────────────

function evaluarRegla(regla, item) {
  var campo = regla.campo;
  var tipo  = regla.tipo;
  var valor = regla.valor;
  var valCampo = item[campo];

  // Calcular totalItem si no viene en los datos
  if (campo === 'totalItem' && esNulo(valCampo)) {
    valCampo = parseNum(item.cantidad) * parseNum(item.precio);
    if (isNaN(valCampo)) valCampo = 0;
  }

  switch (tipo) {
    case 'ES_NULO':
      return esNulo(valCampo);

    case 'ES_VACIO':
      return esVacio(valCampo);

    case 'MENOR_QUE': {
      var n = parseNum(valCampo);
      if (isNaN(n)) return false;
      return n < parseNum(valor);
    }

    case 'MAYOR_QUE': {
      var n = parseNum(valCampo);
      if (isNaN(n)) return false;
      return n > parseNum(valor);
    }

    case 'CONTIENE':
      if (esNulo(valCampo)) return false;
      return String(valCampo).toUpperCase().indexOf(String(valor).toUpperCase()) !== -1;

    case 'NO_CONTIENE':
      if (esNulo(valCampo)) return true;
      return String(valCampo).toUpperCase().indexOf(String(valor).toUpperCase()) === -1;

    default:
      return false;
  }
}

// ─── Clasificar un remito individual ────────────────────────────────────────

function clasificarRemito(item, reglas) {
  // 1. Evaluar reglas en orden — primera que dispara gana
  for (var i = 0; i < reglas.length; i++) {
    var regla = reglas[i];
    if (evaluarRegla(regla, item)) {
      return {
        remito: item.remito,
        estado_nuevo: regla.estado,
        estado_anterior: item.clasificacionAgente || null,
        motivo: regla.motivo,
        puntaje: regla.puntaje,
        regla_id: regla.id,
        regla_nombre: regla.nombre
      };
    }
  }

  // 2. Ninguna regla disparó → APTO
  return {
    remito: item.remito,
    estado_nuevo: 'APTO_PARA_PROGRAMAR',
    estado_anterior: item.clasificacionAgente || null,
    motivo: 'Todos los datos verificados — listo para facturar',
    puntaje: 75,
    regla_id: null,
    regla_nombre: 'default_apto'
  };
}

// ─── MAIN ───────────────────────────────────────────────────────────────────

var remitos = msg.payload || [];
var reglas  = msg.reglas || REGLAS_DEFAULT;

// Si no hay reglas de MySQL, usar las embebidas
if (!reglas || reglas.length === 0) {
  reglas = REGLAS_DEFAULT;
}

// Ordenar reglas por `orden`
reglas.sort(function(a, b) { return (a.orden || 0) - (b.orden || 0); });

// Clasificar cada remito
var resultados = [];
for (var i = 0; i < remitos.length; i++) {
  var r = clasificarRemito(remitos[i], reglas);
  resultados.push(r);
}

// Resumen
var summary = {
  total: resultados.length,
  aptos: 0,
  pendientes: 0,
  requieren_aprob: 0,
  bloqueados: 0,
  sin_cambio: 0,
  cambiaron: 0
};

for (var j = 0; j < resultados.length; j++) {
  var res = resultados[j];
  switch (res.estado_nuevo) {
    case 'APTO_PARA_PROGRAMAR':        summary.aptos++;          break;
    case 'PENDIENTE_VALIDACION':       summary.pendientes++;     break;
    case 'REQUIERE_APROBACION_HUMANA': summary.requieren_aprob++; break;
    case 'BLOQUEADO':                  summary.bloqueados++;     break;
  }
  if (res.estado_anterior === res.estado_nuevo) {
    summary.sin_cambio++;
  } else if (res.estado_anterior && res.estado_anterior !== '') {
    summary.cambiaron++;
  }
}

// Preparar payload para el siguiente nodo
msg.payload = resultados;
msg.summary = summary;
msg.reglas_usadas = reglas.length;
msg.reglas_embebidas = (msg.reglas && msg.reglas.length > 0) ? false : true;

return msg;
