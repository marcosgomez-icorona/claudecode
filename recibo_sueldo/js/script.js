// script.js
// Aquí es donde manejarías la lógica con JavaScript

/*
document.addEventListener('DOMContentLoaded', function() {
    // Supongamos que tienes un arreglo de recibos
    const recibos = [
      { id: 1, nombre: 'Juan Pérez', monto: 1500.00 },
      { id: 2, nombre: 'María Gómez', monto: 2000.00 },
      // ... más recibos ...
    ];
  
    const recibosContainer = document.getElementById('recibosContainer');
  
    // Itera sobre los recibos y crea elementos para mostrarlos
    recibos.forEach(recibo => {
      const reciboElement = document.createElement('div');
      reciboElement.classList.add('col-md-4', 'recibo');
      reciboElement.innerHTML = `
        <h4>${recibo.nombre}</h4>
        <p>Monto: $${recibo.monto.toFixed(2)}</p>
        <button onclick="mostrarRecibo(${recibo.id})" class="btn btn-primary">Ver Recibo</button>
      `;
      recibosContainer.appendChild(reciboElement);
    });
  });
  
  function mostrarRecibo(reciboId) {
    // Aquí implementarías la lógica para mostrar el recibo específico
    // Puedes abrir un modal, cargar más detalles, etc.
    console.log(`Mostrar recibo con ID ${reciboId}`);
  }
  
  //LEER DATOS DE UN SHEET DE GOOGLE
  // script.js
document.addEventListener('DOMContentLoaded', function() {
    // ...
  
    // Agrega un botón para cargar los datos desde Google Sheets
    const cargarDatosBtn = document.createElement('button');
    cargarDatosBtn.classList.add('btn', 'btn-success', 'mt-3');
    cargarDatosBtn.innerText = 'Cargar Datos desde Google Sheets';
    cargarDatosBtn.addEventListener('click', cargarDatosDesdeGoogleSheets);
    document.body.appendChild(cargarDatosBtn);
  });
  
  function cargarDatosDesdeGoogleSheets() {
    // ID de tu hoja de cálculo de Google Sheets
    const spreadsheetId = '1dMjDKA4OcQkhKqmg5uORmSFp5wr8qUrRMPCZKd08uuI';
  
    // Rango de celdas que deseas leer (por ejemplo, A1:E10)
    const range = 'A1:E1000';
  
    // Clave de API obtenida desde la consola de desarrolladores de Google Cloud
    const apiKey = 'AIzaSyDADGib7Wa7YFyfvWGxTldAOQYkjkAe3kE';
  
    const url = `https://sheets.googleapis.com/v4/spreadsheets/${spreadsheetId}/values/${range}?key=${apiKey}`;
  
    // Realiza una solicitud AJAX para obtener los datos desde Google Sheets
    fetch(url)
      .then(response => response.json())
      .then(data => {
        // Extrae los datos de la respuesta
        const valores = data.values;
  
        // Supongamos que los datos están en el formato [Nombre, Apellido, Cargo, Sueldo, Bruto, Neto]
        // Puedes ajustar esto según la estructura de tu hoja de cálculo
  
        // Aquí puedes hacer algo con los datos, como mostrarlos en la consola
        console.log('Datos obtenidos:', valores);
      })
      .catch(error => console.error('Error al obtener datos desde Google Sheets:', error));
  }
  */