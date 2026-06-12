// assets/js/config.js
// Configuración del tablero de Despachos Pendientes de Facturación

export const CONFIG = {
  // API endpoint de Node-RED
  api: {
    baseUrl: '',  // Vacío = mismo origen. En Node-RED: '/api/despachos'
    endpoints: {
      pendientes: '/api/despachos/pendientes',
      detalle: '/api/despachos/pendientes',
      resumen: '/api/despachos/resumen',
      health: '/api/despachos/health'
    }
  },

  // Filtros por defecto
  defaults: {
    daysBack: 30,
    pageSize: 25
  },

  // Colores por tipo de producto
  productColors: {
    'AZUCAR COMUN TIPO A': '#1D9E75',
    'AZUCAR CRUDO': '#185FA5',
    'AZUCAR GRADO 4 EXPO CHILE': '#BA7517'
  },

  // Etiquetas para UI
  labels: {
    appTitle: 'Despachos Pendientes de Facturación',
    appSubtitle: 'Ingenio La Corona',
    emptyTable: 'No se encontraron despachos pendientes para el filtro seleccionado.',
    loadingMessage: 'Cargando datos...',
    errorMessage: 'Error al cargar los datos. Verificar conexión con Node-RED.'
  }
};
