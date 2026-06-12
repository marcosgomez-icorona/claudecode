function classifyDispatchOrders_(data, runUuid) {
  return data.pedidos.map(function(pedido) {
    const context = buildOrderContext_(pedido, data);
    const checks = runOrderChecks_(context);
    const status = resolveOrderStatus_(checks);

    return {
      runUuid: runUuid,
      pedido: pedido,
      context: context,
      checks: checks,
      clasificacion: status,
      motivos: checks.filter(function(check) { return !check.ok; }).map(function(check) { return check.message; }),
      riesgos: checks.filter(function(check) { return check.risk; }).map(function(check) { return check.risk; }),
      fechaRecomendada: context.fechaSolicitada || '',
      requiereAprobacion: status === DISPATCH_STATUS.APROBACION
    };
  });
}

function buildOrderContext_(pedido, data) {
  const pedidoId = String(pedido.pedido_id || pedido.pedido || '').trim();
  const clienteId = String(pedido.cliente_id || pedido.cliente || '').trim();
  const productoTipo = String(pedido.producto_tipo || pedido.tipo_producto || 'azucar').toLowerCase().trim();
  const producto = String(pedido.producto_descripcion || pedido.producto || '').toLowerCase().trim();
  const cantidad = Number(pedido.cantidad || 0);
  const fechaSolicitada = pedido.fecha_solicitada || pedido.fecha || '';

  const cliente = findFirst_(data.clientes, function(item) {
    return sameText_(item.cliente_id || item.cliente, clienteId);
  });

  const stock = findFirst_(data.stock, function(item) {
    return sameText_(item.producto_tipo || item.tipo_producto, productoTipo) &&
      sameText_(item.producto_descripcion || item.producto, producto);
  });

  const facturacion = findFirst_(data.facturacion, function(item) {
    return sameText_(item.pedido_id || item.pedido, pedidoId);
  });

  const transporte = findFirst_(data.transporte, function(item) {
    const estado = String(item.estado || '').toLowerCase();
    const habilitado = String(item.tipo_producto_habilitado || item.producto_tipo || '').toLowerCase();
    return estado !== 'no disponible' && (!habilitado || habilitado === productoTipo);
  });

  const capacidad = findFirst_(data.capacidad, function(item) {
    return sameText_(item.producto_tipo || item.tipo_producto, productoTipo) &&
      sameText_(item.fecha, fechaSolicitada);
  });

  return {
    pedidoId: pedidoId,
    clienteId: clienteId,
    productoTipo: productoTipo,
    producto: producto,
    cantidad: cantidad,
    fechaSolicitada: fechaSolicitada,
    cliente: cliente,
    stock: stock,
    facturacion: facturacion,
    transporte: transporte,
    capacidad: capacidad
  };
}

function runOrderChecks_(context) {
  const checks = [];

  checks.push(checkRequired_(context.pedidoId, 'Falta identificador de pedido'));
  checks.push(checkRequired_(context.clienteId, 'Falta identificador de cliente'));
  checks.push(checkRequired_(context.producto, 'Falta producto'));
  checks.push(checkPositiveNumber_(context.cantidad, 'Cantidad invalida o faltante'));
  checks.push(checkRequired_(context.fechaSolicitada, 'Falta fecha solicitada'));

  checks.push(checkExists_(context.stock, 'No se encontro stock para el producto'));
  if (context.stock) {
    const stockDisponible = Number(context.stock.stock_disponible || context.stock.disponible || 0);
    checks.push({
      ok: stockDisponible >= context.cantidad,
      type: stockDisponible >= context.cantidad ? 'ok' : 'blocked',
      message: stockDisponible >= context.cantidad ? 'Stock suficiente' : 'Stock insuficiente',
      risk: stockDisponible >= context.cantidad ? '' : 'No programar sin validar stock'
    });
  }

  checks.push(checkExists_(context.cliente, 'No se encontro estado administrativo del cliente'));
  if (context.cliente) {
    const bloqueado = isTruthyText_(context.cliente.bloqueo_admin || context.cliente.bloqueado);
    const deudaVencida = Number(context.cliente.deuda_vencida || 0);
    const autorizacion = isTruthyText_(context.cliente.autorizacion_comercial || context.cliente.autorizado);

    checks.push({
      ok: !bloqueado,
      type: bloqueado ? 'blocked' : 'ok',
      message: bloqueado ? 'Cliente bloqueado administrativamente' : 'Cliente sin bloqueo administrativo',
      risk: bloqueado ? 'Bloqueo administrativo' : ''
    });

    checks.push({
      ok: deudaVencida <= 0 || autorizacion,
      type: deudaVencida > 0 && autorizacion ? 'approval' : (deudaVencida > 0 ? 'blocked' : 'ok'),
      message: deudaVencida > 0 && autorizacion ? 'Cliente con deuda vencida y autorizacion comercial' : (deudaVencida > 0 ? 'Cliente con deuda vencida bloqueante' : 'Sin deuda vencida bloqueante'),
      risk: deudaVencida > 0 ? 'Riesgo de cobranza' : ''
    });
  }

  checks.push(checkExists_(context.facturacion, 'No se encontro estado de facturacion'));
  if (context.facturacion) {
    const puedeFacturarse = !isFalseText_(context.facturacion.puede_facturarse || context.facturacion.facturable);
    checks.push({
      ok: puedeFacturarse,
      type: puedeFacturarse ? 'ok' : 'blocked',
      message: puedeFacturarse ? 'Facturacion lista o emitible' : 'Facturacion no emitible',
      risk: puedeFacturarse ? '' : 'Riesgo administrativo/fiscal'
    });
  }

  checks.push(checkExists_(context.transporte, 'No se encontro transporte disponible'));

  checks.push(checkExists_(context.capacidad, 'No se encontro capacidad operativa para la fecha'));
  if (context.capacidad) {
    const maxima = Number(context.capacidad.capacidad_maxima || 0);
    const programada = Number(context.capacidad.capacidad_programada || 0);
    const disponible = maxima - programada;
    checks.push({
      ok: disponible >= context.cantidad,
      type: disponible >= context.cantidad ? 'ok' : 'approval',
      message: disponible >= context.cantidad ? 'Capacidad diaria disponible' : 'Capacidad diaria insuficiente o excedida',
      risk: disponible >= context.cantidad ? '' : 'Exceso de capacidad operativa'
    });
  }

  if (context.productoTipo === 'alcohol') {
    checks.push({
      ok: false,
      type: 'approval',
      message: 'Despacho de alcohol requiere validacion documental/fiscal humana',
      risk: 'Control especial para alcohol'
    });
  }

  return checks;
}

function resolveOrderStatus_(checks) {
  const missing = checks.some(function(check) { return check.type === 'missing'; });
  const blocked = checks.some(function(check) { return check.type === 'blocked'; });
  const approval = checks.some(function(check) { return check.type === 'approval'; });

  if (blocked) return DISPATCH_STATUS.BLOQUEADO;
  if (missing) return DISPATCH_STATUS.PENDIENTE;
  if (approval) return DISPATCH_STATUS.APROBACION;
  return DISPATCH_STATUS.APTO;
}

function checkRequired_(value, message) {
  const ok = value !== null && value !== undefined && String(value).trim() !== '';
  return { ok: ok, type: ok ? 'ok' : 'missing', message: ok ? 'Dato presente' : message, risk: ok ? '' : 'Dato faltante' };
}

function checkPositiveNumber_(value, message) {
  const ok = Number(value) > 0;
  return { ok: ok, type: ok ? 'ok' : 'missing', message: ok ? 'Cantidad valida' : message, risk: ok ? '' : 'Dato faltante' };
}

function checkExists_(value, message) {
  const ok = !!value;
  return { ok: ok, type: ok ? 'ok' : 'missing', message: ok ? 'Fuente encontrada' : message, risk: ok ? '' : 'Fuente faltante' };
}

function findFirst_(items, predicate) {
  for (let i = 0; i < items.length; i++) {
    if (predicate(items[i])) return items[i];
  }
  return null;
}

function sameText_(a, b) {
  return String(a || '').trim().toLowerCase() === String(b || '').trim().toLowerCase();
}

function isTruthyText_(value) {
  const text = String(value || '').trim().toLowerCase();
  return ['si', 'sí', 'true', '1', 'x', 'bloqueado'].indexOf(text) >= 0;
}

function isFalseText_(value) {
  const text = String(value || '').trim().toLowerCase();
  return ['no', 'false', '0', 'n'].indexOf(text) >= 0;
}

