<div id="contenido"></div>
<script>
  // Ejemplo de código usando Fetch API para actualizar datos
async function ActualizaMoliendaOnline() {
    try {
        // Mostrar indicador de carga
        document.getElementById('contenido').innerHTML = '<div class="loading"></div>';
        
        // Hacer la petición al servidor
        const respuesta = await fetch('view.php?menu=molienda_online');
        
        if (!respuesta.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        
        // Obtener los datos (asumiendo que el PHP devuelve HTML)
        const datos = await respuesta.text();
        
        
        // Insertar los datos en el DOM
        document.getElementById('contenido').innerHTML = datos;
        
        // Actualizar marca de tiempo
       // document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString();
        
    } catch (error) {
        console.error('Error al obtener datos:', error);
        document.getElementById('contenido').innerHTML = 
            '<div class="error">Error al cargar los datos. Intentando de nuevo en 30 segundos...</div>';
    }
}

// Programar la actualización cada 10 segundos
setInterval(ActualizaMoliendaOnline, 60000);

// Llamar inmediatamente para cargar datos al inicio
ActualizaMoliendaOnline();
</script>