const DISPATCH_CONFIG = {
  spreadsheetName: 'DESPACHOS_CORONA_TEST_SPARK.xlsx',
  outputFolderName: 'Outputs',
  sheets: {
    pedidos: 'Pedidos',
    stock: 'Stock',
    clientes: 'Clientes',
    facturacion: 'Facturacion',
    transporte: 'Transporte',
    capacidad: 'Capacidad'
  },
  llm: {
    provider: 'gemini',
    model: 'gemini-1.5-flash',
    apiKeyProperty: 'GEMINI_API_KEY'
  },
  paginationLimit: 10
};

const DISPATCH_STATUS = {
  APTO: 'Apto para programar',
  BLOQUEADO: 'Bloqueado',
  PENDIENTE: 'Pendiente de validacion',
  APROBACION: 'Requiere aprobacion humana'
};

