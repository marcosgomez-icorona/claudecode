function loadDispatchData_() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();

  return {
    pedidos: readSheetObjects_(ss, DISPATCH_CONFIG.sheets.pedidos),
    stock: readSheetObjects_(ss, DISPATCH_CONFIG.sheets.stock),
    clientes: readSheetObjects_(ss, DISPATCH_CONFIG.sheets.clientes),
    facturacion: readSheetObjects_(ss, DISPATCH_CONFIG.sheets.facturacion),
    transporte: readSheetObjects_(ss, DISPATCH_CONFIG.sheets.transporte),
    capacidad: readSheetObjects_(ss, DISPATCH_CONFIG.sheets.capacidad)
  };
}

function readSheetObjects_(ss, sheetName) {
  const sheet = ss.getSheetByName(sheetName);
  if (!sheet) {
    throw new Error('No existe la hoja requerida: ' + sheetName);
  }

  const values = sheet.getDataRange().getValues();
  if (values.length < 2) {
    return [];
  }

  const headers = values[0].map(function(header) {
    return normalizeKey_(header);
  });

  return values.slice(1)
    .filter(function(row) {
      return row.some(function(value) { return value !== '' && value !== null; });
    })
    .map(function(row) {
      const item = {};
      headers.forEach(function(header, index) {
        item[header] = row[index];
      });
      return item;
    });
}

function normalizeKey_(value) {
  return String(value || '')
    .trim()
    .toLowerCase()
    .replace(/[áàäâ]/g, 'a')
    .replace(/[éèëê]/g, 'e')
    .replace(/[íìïî]/g, 'i')
    .replace(/[óòöô]/g, 'o')
    .replace(/[úùüû]/g, 'u')
    .replace(/ñ/g, 'n')
    .replace(/[^a-z0-9]+/g, '_')
    .replace(/^_+|_+$/g, '');
}

