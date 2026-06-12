function buildDispatchMarkdownReport_(results, data, runUuid, startedAt) {
  const limit = DISPATCH_CONFIG.paginationLimit;
  const aptos = filterByStatus_(results, DISPATCH_STATUS.APTO);
  const bloqueados = filterByStatus_(results, DISPATCH_STATUS.BLOQUEADO);
  const pendientes = filterByStatus_(results, DISPATCH_STATUS.PENDIENTE);
  const aprobacion = filterByStatus_(results, DISPATCH_STATUS.APROBACION);

  const shown = Math.min(results.length, limit);
  const pendingToShow = Math.max(results.length - limit, 0);

  let md = '';
  md += '# Propuesta de despachos - Ingenio La Corona\n\n';
  md += 'Run UUID: `' + runUuid + '`\n\n';

  md += '## 1. Resumen ejecutivo logistico\n\n';
  md += '- Total analizados: ' + results.length + '\n';
  md += '- Apto: ' + aptos.length + '\n';
  md += '- Bloqueado: ' + bloqueados.length + '\n';
  md += '- Pendiente validacion: ' + pendientes.length + '\n';
  md += '- Requiere aprobacion: ' + aprobacion.length + '\n';
  md += '- Mostrados en este lote: ' + shown + '\n';
  md += '- Pendientes de mostrar: ' + pendingToShow + '\n';
  md += '- Capacidad estimada: segun hoja `' + DISPATCH_CONFIG.sheets.capacidad + '`\n';
  md += '- Fuentes principales: Sheets/Drive, estado administrativo, facturacion, transporte y capacidad\n\n';
  md += 'Principales riesgos:\n\n';
  md += buildRiskTable_(results);

  md += '\n## 2. Fuentes consultadas\n\n';
  md += '| Fuente | Ubicacion/Filtro | Tipo de acceso | Estado | Observaciones |\n';
  md += '| --- | --- | --- | --- | --- |\n';
  md += '| Pedidos | ' + DISPATCH_CONFIG.sheets.pedidos + ' | Lectura | OK | ' + data.pedidos.length + ' registros |\n';
  md += '| Stock | ' + DISPATCH_CONFIG.sheets.stock + ' | Lectura | OK | ' + data.stock.length + ' registros |\n';
  md += '| Clientes | ' + DISPATCH_CONFIG.sheets.clientes + ' | Lectura | OK | ' + data.clientes.length + ' registros |\n';
  md += '| Facturacion | ' + DISPATCH_CONFIG.sheets.facturacion + ' | Lectura | OK | ' + data.facturacion.length + ' registros |\n';
  md += '| Transporte | ' + DISPATCH_CONFIG.sheets.transporte + ' | Lectura | OK | ' + data.transporte.length + ' registros |\n';
  md += '| Capacidad | ' + DISPATCH_CONFIG.sheets.capacidad + ' | Lectura | OK | ' + data.capacidad.length + ' registros |\n\n';

  md += '## 3. Entregas aptas para programar\n\n';
  md += buildAptosTable_(aptos.slice(0, limit));

  md += '\n## 4. Entregas bloqueadas\n\n';
  md += buildBloqueadosTable_(bloqueados.slice(0, limit));

  md += '\n## 5. Entregas pendientes de validacion\n\n';
  md += buildPendientesTable_(pendientes.slice(0, limit));

  md += '\n## 6. Entregas que requieren aprobacion humana\n\n';
  md += buildAprobacionTable_(aprobacion.slice(0, limit));

  md += '\n## 7. Propuesta de calendario diario/semanal\n\n';
  md += buildCalendarTable_(results.slice(0, limit));

  md += '\n## 8. Alertas y riesgos\n\n';
  md += buildRiskTable_(results);

  md += '\n## 9. Acciones recomendadas por responsable\n\n';
  md += buildActionsByOwner_(results);

  md += '\n## 10. Decisiones que requieren aprobacion humana\n\n';
  md += buildHumanDecisions_(results);

  md += '\n## 11. Borrador interno opcional\n\n';
  md += 'Solicito revision y aprobacion de la propuesta de despachos generada en el run `' + runUuid + '`. Verificar bloqueos, pendientes documentales, capacidad operativa y autorizaciones comerciales antes de confirmar entregas.\n\n';

  md += '## 12. Supuestos y datos faltantes\n\n';
  md += '- No se modificaron datos de origen.\n';
  md += '- Los registros sin fuente relacionada fueron clasificados como pendientes de validacion.\n';
  md += '- Las entregas de alcohol requieren aprobacion humana por control documental/fiscal especial.\n\n';

  md += '## 13. Continuacion por lotes\n\n';
  if (pendingToShow > 0) {
    md += 'Quedan ' + pendingToShow + ' pedidos pendientes de mostrar. Deseas que procese y muestre el siguiente lote?\n';
  } else {
    md += 'No quedan pedidos pendientes de mostrar.\n';
  }

  return md;
}

function buildAptosTable_(items) {
  let md = '| Cliente | Pedido | Producto | Cantidad | Fecha solicitada | Fecha recomendada | Transporte | Estado admin | Prioridad | Observaciones |\n';
  md += '| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |\n';
  items.forEach(function(item) {
    md += '| ' + cell_(item.context.clienteId) + ' | ' + cell_(item.context.pedidoId) + ' | ' + cell_(item.context.producto) + ' | ' + cell_(item.context.cantidad) + ' | ' + cell_(item.context.fechaSolicitada) + ' | ' + cell_(item.fechaRecomendada) + ' | Disponible | OK | ' + cell_(item.pedido.prioridad_comercial || '') + ' | Validado por reglas |\n';
  });
  return md;
}

function buildBloqueadosTable_(items) {
  let md = '| Cliente | Pedido | Motivo bloqueo | Area responsable | Responsable sugerido | Accion recomendada | Urgencia |\n';
  md += '| --- | --- | --- | --- | --- | --- | --- |\n';
  items.forEach(function(item) {
    md += '| ' + cell_(item.context.clienteId) + ' | ' + cell_(item.context.pedidoId) + ' | ' + cell_(item.motivos.join('; ')) + ' | Administracion/Logistica | Responsable de area | Destrabar bloqueo antes de programar | Alta |\n';
  });
  return md;
}

function buildPendientesTable_(items) {
  let md = '| Cliente | Pedido | Dato faltante | Fuente a revisar | Responsable | Accion recomendada |\n';
  md += '| --- | --- | --- | --- | --- | --- |\n';
  items.forEach(function(item) {
    md += '| ' + cell_(item.context.clienteId) + ' | ' + cell_(item.context.pedidoId) + ' | ' + cell_(item.motivos.join('; ')) + ' | Sheets/Drive | Sistemas/Datos | Completar fuente y reprocesar |\n';
  });
  return md;
}

function buildAprobacionTable_(items) {
  let md = '| Cliente | Pedido | Motivo de aprobacion | Decision requerida | Responsable sugerido | Riesgo |\n';
  md += '| --- | --- | --- | --- | --- | --- |\n';
  items.forEach(function(item) {
    md += '| ' + cell_(item.context.clienteId) + ' | ' + cell_(item.context.pedidoId) + ' | ' + cell_(item.motivos.join('; ')) + ' | Aprobar o rechazar programacion | Comercial/Administracion/Logistica | ' + cell_(item.riesgos.join('; ')) + ' |\n';
  });
  return md;
}

function buildCalendarTable_(items) {
  let md = '| Dia | Cliente | Pedido | Cantidad | Transporte | Horario sugerido | Estado | Observaciones |\n';
  md += '| --- | --- | --- | --- | --- | --- | --- | --- |\n';
  items.forEach(function(item) {
    md += '| ' + cell_(item.fechaRecomendada) + ' | ' + cell_(item.context.clienteId) + ' | ' + cell_(item.context.pedidoId) + ' | ' + cell_(item.context.cantidad) + ' | A validar | A definir | ' + cell_(item.clasificacion) + ' | ' + cell_(item.motivos.join('; ')) + ' |\n';
  });
  return md;
}

function buildRiskTable_(items) {
  let md = '| Riesgo | Tipo | Impacto | Probabilidad | Mitigacion sugerida | Responsable |\n';
  md += '| --- | --- | --- | --- | --- | --- |\n';
  const risks = [];
  items.forEach(function(item) {
    item.riesgos.forEach(function(risk) {
      if (risk) risks.push(risk);
    });
  });
  if (risks.length === 0) {
    md += '| Sin riesgos criticos detectados | Operativo | Bajo | Baja | Mantener revision humana | Logistica |\n';
  } else {
    risks.slice(0, 20).forEach(function(risk) {
      md += '| ' + cell_(risk) + ' | Operativo/Administrativo | Alto | Media | Validar antes de confirmar despacho | Responsable de area |\n';
    });
  }
  return md;
}

function buildActionsByOwner_(items) {
  return '- Comercial: revisar prioridades y autorizaciones especiales.\n' +
    '- Administracion/Facturacion: destrabar facturacion y documentacion pendiente.\n' +
    '- Cobranzas: validar deuda vencida y condiciones de pago.\n' +
    '- Logistica/Transporte: confirmar disponibilidad de unidades.\n' +
    '- Deposito/Despacho: validar stock fisico y capacidad diaria.\n' +
    '- Sistemas/Datos: completar fuentes faltantes y revisar consistencia de columnas.\n';
}

function buildHumanDecisions_(items) {
  const decisions = items.filter(function(item) {
    return item.requiereAprobacion || item.clasificacion === DISPATCH_STATUS.BLOQUEADO;
  });
  if (decisions.length === 0) return '- No hay decisiones humanas criticas detectadas.\n';
  return decisions.map(function(item) {
    return '- Pedido ' + item.context.pedidoId + ' / Cliente ' + item.context.clienteId + ': ' + item.clasificacion + ' - ' + item.motivos.join('; ');
  }).join('\n') + '\n';
}

function filterByStatus_(results, status) {
  return results.filter(function(item) {
    return item.clasificacion === status;
  });
}

function cell_(value) {
  return String(value === null || value === undefined ? '' : value).replace(/\|/g, '/');
}

