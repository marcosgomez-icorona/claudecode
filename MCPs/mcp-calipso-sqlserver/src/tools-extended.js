// ============================================================================
// HERRAMIENTAS EXTENDIDAS PARA FACTURAS, OC Y CONTABILIDAD
// Se integran en server.js antes de: const transport = new StdioServerTransport();
// ============================================================================

server.tool(
  'get_invoices_by_supplier',
  'Obtiene facturas de compra por proveedor o rango de fechas. Herramienta para análisis de facturas.',
  {
    supplier_name: z.string().optional().describe('Nombre parcial del proveedor a buscar'),
    date_from: z.string().optional().describe('Fecha inicial (YYYY-MM-DD)'),
    date_to: z.string().optional().describe('Fecha final (YYYY-MM-DD)'),
    limit: z.number().int().positive().optional().describe('Límite de registros (max 100)')
  },
  async ({ supplier_name, date_from, date_to, limit = 50 }) => {
    const limitRows = Math.min(limit, 100);
    let query = `
      SELECT TOP (${limitRows})
        fc.ID,
        fc.NUMERODOCUMENTO AS invoice_number,
        fc.FECHAACTUAL AS invoice_date,
        fc.TRANSACCION_ID,
        fc.CENTROCOSTOS_ID,
        fc.ORIGINANTE_ID
      FROM FACTURACOMPRA fc
      WHERE 1=1
    `;

    const params = {};

    if (supplier_name) {
      query += ` AND fc.ORIGINANTE_ID IN (
        SELECT ID FROM [entidad] WHERE NOMBRE LIKE @supplier_name
      )`;
      params.supplier_name = `%${supplier_name}%`;
    }

    if (date_from) {
      query += ` AND CAST(fc.FECHAACTUAL AS DATE) >= @date_from`;
      params.date_from = date_from;
    }

    if (date_to) {
      query += ` AND CAST(fc.FECHAACTUAL AS DATE) <= @date_to`;
      params.date_to = date_to;
    }

    query += ` ORDER BY fc.FECHAACTUAL DESC`;

    const result = await executeReadonly(query, (request) => {
      Object.entries(params).forEach(([key, value]) => {
        request.input(key, sql.NVarChar, value);
      });
    });

    return asText({
      ...result,
      message: `Facturas de compra encontradas: ${result.row_count}`
    });
  }
);

server.tool(
  'get_purchase_orders',
  'Obtiene órdenes de compra activas o por rango de fechas.',
  {
    supplier_name: z.string().optional().describe('Nombre del proveedor'),
    status: z.string().optional().describe('Estado de la OC (ej: ABIERTA, CERRADA)'),
    date_from: z.string().optional().describe('Fecha inicial (YYYY-MM-DD)'),
    limit: z.number().int().positive().optional().describe('Límite de registros')
  },
  async ({ supplier_name, status, date_from, limit = 50 }) => {
    const limitRows = Math.min(limit, 100);
    
    let query = `
      SELECT TOP (${limitRows})
        oc.ID,
        oc.NUMERODOCUMENTO AS po_number,
        oc.FECHAACTUAL AS po_date,
        oc.ORIGINANTE_ID,
        oc.TRANSACCION_ID,
        oc.CENTROCOSTOS_ID
      FROM ORDENCOMPRA oc
      WHERE 1=1
    `;

    const params = {};

    if (supplier_name) {
      query += ` AND oc.ORIGINANTE_ID IN (
        SELECT ID FROM [entidad] WHERE NOMBRE LIKE @supplier_name
      )`;
      params.supplier_name = `%${supplier_name}%`;
    }

    if (date_from) {
      query += ` AND CAST(oc.FECHAACTUAL AS DATE) >= @date_from`;
      params.date_from = date_from;
    }

    query += ` ORDER BY oc.FECHAACTUAL DESC`;

    const result = await executeReadonly(query, (request) => {
      Object.entries(params).forEach(([key, value]) => {
        request.input(key, sql.NVarChar, value);
      });
    });

    return asText({
      ...result,
      message: `Órdenes de compra encontradas: ${result.row_count}`
    });
  }
);

server.tool(
  'get_invoice_items',
  'Obtiene los items/líneas de una factura de compra específica.',
  {
    invoice_id: z.string().describe('ID de la factura (UUID)'),
    include_quantities: z.boolean().optional().describe('Incluir cantidades y precios')
  },
  async ({ invoice_id, include_quantities = true }) => {
    assertIdentifier(invoice_id, 'invoice_id');

    let query = `
      SELECT
        ifc.ID AS item_id,
        ifc.NUMEROORDENITEM AS item_number,
        ifc.DESCRIPCION AS description,
        ifc.CANTIDADRECIBIDA AS quantity_received,
        ifc.CANTIDADADICIONAL AS quantity_additional,
        ifc.COSTO AS unit_cost,
        ifc.CUENTACONTABLE_ID
      FROM ITEMFACTURACOMPRA ifc
      WHERE ifc.ID = @invoice_id
      ORDER BY ifc.NUMEROORDENITEM
    `;

    const result = await executeReadonly(query, (request) => {
      request.input('invoice_id', sql.NVarChar, invoice_id);
    });

    return asText({
      ...result,
      message: `Items de factura: ${result.row_count} encontrados`
    });
  }
);

server.tool(
  'search_accounting_data',
  'Busca datos contables, asientos y transacciones relacionadas a facturas.',
  {
    search_term: z.string().min(2).describe('Término de búsqueda (factura, OC, asiento, etc)'),
    date_from: z.string().optional().describe('Fecha inicial'),
    accounting_type: z.enum(['FACTURA', 'ASIENTO', 'TRANSACCION', 'COSTO']).optional()
  },
  async ({ search_term, date_from, accounting_type }) => {
    const maxRows = 200;
    
    let query = `
      SELECT TOP (${maxRows})
        ic.ID,
        ic.DESCRIPCION AS description,
        ic.FECHAACTUAL AS transaction_date,
        ic.TIPOTRANSACCION AS transaction_type,
        ic.IMPORTE AS amount,
        ic.MONEDA AS currency
      FROM ITEMCONTABLE ic
      WHERE UPPER(ic.DESCRIPCION) LIKE @search_term
        OR UPPER(ic.TIPOTRANSACCION) LIKE @search_term
    `;

    const params = {
      search_term: `%${search_term.toUpperCase()}%`
    };

    if (date_from) {
      query += ` AND CAST(ic.FECHAACTUAL AS DATE) >= @date_from`;
      params.date_from = date_from;
    }

    if (accounting_type) {
      query += ` AND ic.TIPOTRANSACCION = @accounting_type`;
      params.accounting_type = accounting_type;
    }

    query += ` ORDER BY ic.FECHAACTUAL DESC`;

    const result = await executeReadonly(query, (request) => {
      Object.entries(params).forEach(([key, value]) => {
        request.input(key, sql.NVarChar, value);
      });
    });

    return asText({
      ...result,
      search_term,
      message: `Registros contables encontrados: ${result.row_count}`
    });
  }
);

server.tool(
  'get_sugar_sales_orders',
  'Obtiene órdenes de venta de azúcar (especialización para ingenio).',
  {
    date_from: z.string().optional().describe('Fecha inicial (YYYY-MM-DD)'),
    date_to: z.string().optional().describe('Fecha final (YYYY-MM-DD)'),
    customer_name: z.string().optional().describe('Nombre del cliente'),
    limit: z.number().optional().describe('Límite de registros')
  },
  async ({ date_from, date_to, customer_name, limit = 50 }) => {
    const limitRows = Math.min(limit, 100);
    
    let query = `
      SELECT TOP (${limitRows})
        v.ID,
        v.NUMERODOCUMENTO AS order_number,
        v.FECHAACTUAL AS order_date,
        v.ORIGINANTE_ID AS customer_id,
        v.CANTIDADTOTAL AS total_quantity,
        v.IMPORTETOTAL AS total_amount
      FROM [V_UD_EZI_ORDEN_VTA_AZ_ITEM] v
      WHERE 1=1
    `;

    const params = {};

    if (date_from) {
      query += ` AND CAST(v.FECHAACTUAL AS DATE) >= @date_from`;
      params.date_from = date_from;
    }

    if (date_to) {
      query += ` AND CAST(v.FECHAACTUAL AS DATE) <= @date_to`;
      params.date_to = date_to;
    }

    if (customer_name) {
      query += ` AND v.NOMBRECLIENT LIKE @customer_name`;
      params.customer_name = `%${customer_name}%`;
    }

    query += ` ORDER BY v.FECHAACTUAL DESC`;

    const result = await executeReadonly(query, (request) => {
      Object.entries(params).forEach(([key, value]) => {
        request.input(key, sql.NVarChar, value);
      });
    });

    return asText({
      ...result,
      message: `Órdenes de venta de azúcar encontradas: ${result.row_count}`
    });
  }
);

server.tool(
  'get_alcohol_sales_data',
  'Obtiene datos de ventas de alcohol (especialización para ingenio).',
  {
    date_from: z.string().optional().describe('Fecha inicial'),
    date_to: z.string().optional().describe('Fecha final'),
    limit: z.number().optional().describe('Límite de registros')
  },
  async ({ date_from, date_to, limit = 50 }) => {
    const limitRows = Math.min(limit, 100);
    
    let query = `
      SELECT TOP (${limitRows})
        v.ID,
        v.NUMERODOCUMENTO AS order_number,
        v.FECHAACTUAL AS order_date,
        v.CANTIDADTOTAL AS quantity,
        v.IMPORTETOTAL AS amount,
        v.UNIDAD AS unit
      FROM [V_UD_EZI_ORDEN_ALCOHOL] v
      WHERE 1=1
    `;

    const params = {};

    if (date_from) {
      query += ` AND CAST(v.FECHAACTUAL AS DATE) >= @date_from`;
      params.date_from = date_from;
    }

    if (date_to) {
      query += ` AND CAST(v.FECHAACTUAL AS DATE) <= @date_to`;
      params.date_to = date_to;
    }

    query += ` ORDER BY v.FECHAACTUAL DESC`;

    const result = await executeReadonly(query, (request) => {
      Object.entries(params).forEach(([key, value]) => {
        request.input(key, sql.NVarChar, value);
      });
    });

    return asText({
      ...result,
      message: `Datos de venta de alcohol encontrados: ${result.row_count}`
    });
  }
);

server.tool(
  'get_accounting_reports',
  'Accede a informes contables y datos de análisis gerencial.',
  {
    report_type: z.enum(['CUENTAS_PAGAR', 'CUENTAS_COBRAR', 'CASHFLOW', 'PRESUPUESTO', 'COSTOS']).describe('Tipo de informe'),
    date_from: z.string().optional().describe('Fecha inicial'),
    date_to: z.string().optional().describe('Fecha final'),
    business_unit: z.string().optional().describe('Unidad de negocio')
  },
  async ({ report_type, date_from, date_to, business_unit }) => {
    let query = '';
    const params = {};

    switch (report_type) {
      case 'CUENTAS_PAGAR':
        query = `
          SELECT TOP 100
            v.ID,
            v.PROVEEDOR AS supplier,
            v.MONTO AS amount,
            v.MONEDA AS currency,
            v.ESTADO AS status,
            v.FECHA_VENCIMIENTO AS due_date
          FROM [V_UD_UOCUENTASPAGAR] v
          WHERE 1=1
        `;
        break;

      case 'CUENTAS_COBRAR':
        query = `
          SELECT TOP 100
            v.ID,
            v.CLIENTE AS customer,
            v.MONTO AS amount,
            v.MONEDA AS currency,
            v.ESTADO AS status,
            v.FECHA_VENCIMIENTO AS due_date
          FROM [V_UD_UOCUENTASCOBRAR] v
          WHERE 1=1
        `;
        break;

      case 'CASHFLOW':
        query = `
          SELECT TOP 100
            v.ID,
            v.DESCRIPCION AS description,
            v.ENTRADA AS inflow,
            v.SALIDA AS outflow,
            v.NETO AS net,
            v.FECHA AS transaction_date
          FROM [V_UD_UOCASHFLOW] v
          WHERE 1=1
        `;
        break;

      case 'COSTOS':
        query = `
          SELECT TOP 100
            v.ID,
            v.CONCEPTO AS concept,
            v.MONTO AS amount,
            v.TIPO AS type,
            v.FECHA AS date
          FROM [ITEMCONTABLE] v
          WHERE UPPER(v.DESCRIPCION) LIKE '%COSTO%'
        `;
        break;

      default:
        throw new Error('Tipo de informe no soportado');
    }

    if (date_from) {
      query += ` AND CAST(v.FECHA AS DATE) >= @date_from`;
      params.date_from = date_from;
    }

    if (date_to) {
      query += ` AND CAST(v.FECHA AS DATE) <= @date_to`;
      params.date_to = date_to;
    }

    query += ` ORDER BY v.FECHA DESC`;

    const result = await executeReadonly(query, (request) => {
      Object.entries(params).forEach(([key, value]) => {
        request.input(key, sql.NVarChar, value);
      });
    });

    return asText({
      ...result,
      report_type,
      message: `Informe ${report_type} generado: ${result.row_count} registros`
    });
  }
);
