<script>
	function imprimirElemento(elemento) {
		var ventana = window.open('', 'PRINT', 'height=400,width=600');
		ventana.document.write('<html><head><title>' + document.title + '</title>');
		ventana.document.write('<link rel="stylesheet" href="imprimir.css">'); //Cargamos otra hoja, no la normal
		ventana.document.write('</head><body >');
		ventana.document.write(elemento.innerHTML);
		ventana.document.write('</body></html>');
		ventana.document.close();
		ventana.focus();
		ventana.onload = function() {
			ventana.print();
			ventana.close();
		};
		return true;
	}

	/*
    //AJAX para refrescar datos de Modal sin que se cierre
    function AJAXRefrezcaModal(horaSeleccionada = '') {
        // Abrir el modal manualmente si no está abierto
        const modal = new bootstrap.Modal(document.getElementById('analisisazucarModal'));
        if (!document.querySelector('.modal.show')) modal.show();

        // Contenedor donde inyectaremos el PHP
        const contenedor = document.getElementById('contenidoModalAnalisis');

        // Usamos fetch para llamar a analisis_azucar.php
        fetch('home.php?menu=analisis_azucar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'hora=' + encodeURIComponent(horaSeleccionada)
        })
        .then(response => response.text())
        .then(data => {
            contenedor.innerHTML = data;
            
            // RE-VINCULAR el evento change al nuevo selector cargado
            document.getElementById('hora').addEventListener('change', function() {
                cargarAnalisis(this.value); // Se llama a sí misma con la nueva hora
            });
        });
    }
	
	<div class="modal fade" id="analisisazucarModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Análisis de Azúcar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div id="contenidoModalAnalisis" class="modal-body">
          <div class="text-center">Cargando datos...</div>
        </div>
      </div>
    </div>
  </div>
 */

</script>

<?php
//Define Zona Horaria
date_default_timezone_set('America/Argentina/Buenos_Aires');

//QUITA COMILLAS DOBLES PARA EVITAR PROBLEMAS DE CARGA
function quitar_comillas($dato){
	$dato= str_replace ( '"', ' ', $dato);
    $dato= str_replace ( "'", ' ', $dato);

	return $dato;
}

//Funcion para formato de fecha
      function fechaDma($amd)
      {
        return substr($amd, 8, 2)."/".substr($amd, 5, 2)."/".                           substr($amd, 0, 4);
      }
	  function fechaAmd($dma)
      {
        return substr($dma, 6, 4)."-".substr($dma, 3, 2)."-".substr($dma, 0, 2);
      }


	  function limpiarCerosIzquierda($numero) {
		return ltrim($numero, '0');
	}

	function rango_hora($hora) {
		// Normalizar a HH:MM (acepta HH:MM o HH:MM:SS)
		$hora = substr(trim($hora), 0, 5);
		if (!preg_match('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/', $hora)) {
			return $hora;
		}
		
		// Convertir a timestamp y sumar 1 hora
		$timestamp_actual = strtotime($hora);
		$timestamp_siguiente = strtotime('+1 hour', $timestamp_actual);
		
		// Formatear las horas
		$hora_inicio = date('H:i', $timestamp_actual);
		$hora_fin = date('H:i', $timestamp_siguiente);
		
		return $hora_inicio . ' a ' . $hora_fin;
	}
		
// FUNCIONES DE CONVERSION DE NUMEROS A LETRAS.
 
function centavos()
{
	global $importe_parcial;
 
	$importe_parcial = number_format($importe_parcial, 2, ".", "") * 100;
 
	if ($importe_parcial > 0)
		$num_letra = " con ".decena_centavos($importe_parcial);
	else
		$num_letra = "";
 
	return $num_letra;
}
 
function unidad_centavos($numero)
{
	switch ($numero)
	{
		case 9:
		{
			$num_letra = "nueve centavos";
			break;
		}
		case 8:
		{
			$num_letra = "ocho centavos";
			break;
		}
		case 7:
		{
			$num_letra = "siete centavos";
			break;
		}
		case 6:
		{
			$num_letra = "seis centavos";
			break;
		}
		case 5:
		{
			$num_letra = "cinco centavos";
			break;
		}
		case 4:
		{
			$num_letra = "cuatro centavos";
			break;
		}
		case 3:
		{
			$num_letra = "tres centavos";
			break;
		}
		case 2:
		{
			$num_letra = "dos centavos";
			break;
		}
		case 1:
		{
			$num_letra = "un centavos";
			break;
		}
	}
	return $num_letra;
}
 
function decena_centavos($numero)
{
	if ($numero >= 10)
	{
		if ($numero >= 90 && $numero <= 99)
		{
			  if ($numero == 90)
				  return "noventa centavos";
			  else if ($numero == 91)
				  return "noventa y un centavos";
			  else
				  return "noventa y ".unidad_centavos($numero - 90);
		}
		if ($numero >= 80 && $numero <= 89)
		{
			if ($numero == 80)
				return "ochenta centavos";
			else if ($numero == 81)
				return "ochenta y un centavos";
			else
				return "ochenta y ".unidad_centavos($numero - 80);
		}
		if ($numero >= 70 && $numero <= 79)
		{
			if ($numero == 70)
				return "setenta centavos";
			else if ($numero == 71)
				return "setenta y un centavos";
			else
				return "setenta y ".unidad_centavos($numero - 70);
		}
		if ($numero >= 60 && $numero <= 69)
		{
			if ($numero == 60)
				return "sesenta centavos";
			else if ($numero == 61)
				return "sesenta y un centavos";
			else
				return "sesenta y ".unidad_centavos($numero - 60);
		}
		if ($numero >= 50 && $numero <= 59)
		{
			if ($numero == 50)
				return "cincuenta centavos";
			else if ($numero == 51)
				return "cincuenta y un centavos";
			else
				return "cincuenta y ".unidad_centavos($numero - 50);
		}
		if ($numero >= 40 && $numero <= 49)
		{
			if ($numero == 40)
				return "cuarenta centavos";
			else if ($numero == 41)
				return "cuarenta y un centavos";
			else
				return "cuarenta y ".unidad_centavos($numero - 40);
		}
		if ($numero >= 30 && $numero <= 39)
		{
			if ($numero == 30)
				return "treinta centavos";
			else if ($numero == 91)
				return "treinta y un centavos";
			else
				return "treinta y ".unidad_centavos($numero - 30);
		}
		if ($numero >= 20 && $numero <= 29)
		{
			if ($numero == 20)
				return "veinte centavos";
			else if ($numero == 21)
				return "veintiun centavos";
			else
				return "veinti".unidad_centavos($numero - 20);
		}
		if ($numero >= 10 && $numero <= 19)
		{
			if ($numero == 10)
				return "diez centavos";
			else if ($numero == 11)
				return "once centavos";
			else if ($numero == 12)
				return "doce centavos";
			else if ($numero == 13)
				return "trece centavos";
			else if ($numero == 14)
				return "catorce centavos";
			else if ($numero == 15)
				return "quince centavos";
			else if ($numero == 16)
				return "dieciseis centavos";
			else if ($numero == 17)
				return "diecisiete centavos";
			else if ($numero == 18)
				return "dieciocho centavos";
			else if ($numero == 19)
				return "diecinueve centavos";
		}
	}
	else
		return unidad_centavos($numero);
}
 
function unidad($numero)
{
	switch ($numero)
	{
		case 9:
		{
			$num = "nueve";
			break;
		}
		case 8:
		{
			$num = "ocho";
			break;
		}
		case 7:
		{
			$num = "siete";
			break;
		}
		case 6:
		{
			$num = "seis";
			break;
		}
		case 5:
		{
			$num = "cinco";
			break;
		}
		case 4:
		{
			$num = "cuatro";
			break;
		}
		case 3:
		{
			$num = "tres";
			break;
		}
		case 2:
		{
			$num = "dos";
			break;
		}
		case 1:
		{
			$num = "uno";
			break;
		}
	}
	return $num;
}
 
function decena($numero)
{
	if ($numero >= 90 && $numero <= 99)
	{
		$num_letra = "noventa ";
 
		if ($numero > 90)
			$num_letra = $num_letra."y ".unidad($numero - 90);
	}
	else if ($numero >= 80 && $numero <= 89)
	{
		$num_letra = "ochenta ";
 
		if ($numero > 80)
			$num_letra = $num_letra."y ".unidad($numero - 80);
	}
	else if ($numero >= 70 && $numero <= 79)
	{
			$num_letra = "setenta ";
 
		if ($numero > 70)
			$num_letra = $num_letra."y ".unidad($numero - 70);
	}
	else if ($numero >= 60 && $numero <= 69)
	{
		$num_letra = "sesenta ";
 
		if ($numero > 60)
			$num_letra = $num_letra."y ".unidad($numero - 60);
	}
	else if ($numero >= 50 && $numero <= 59)
	{
		$num_letra = "cincuenta ";
 
		if ($numero > 50)
			$num_letra = $num_letra."y ".unidad($numero - 50);
	}
	else if ($numero >= 40 && $numero <= 49)
	{
		$num_letra = "cuarenta ";
 
		if ($numero > 40)
			$num_letra = $num_letra."y ".unidad($numero - 40);
	}
	else if ($numero >= 30 && $numero <= 39)
	{
		$num_letra = "treinta ";
 
		if ($numero > 30)
			$num_letra = $num_letra."y ".unidad($numero - 30);
	}
	else if ($numero >= 20 && $numero <= 29)
	{
		if ($numero == 20)
			$num_letra = "veinte ";		
		else if ($numero == 21)
			$num_letra = "veintiun";
		else
			$num_letra = "veinti".unidad($numero - 20);
	}
	else if ($numero >= 10 && $numero <= 19)
	{
		switch ($numero)
		{
			case 10:
			{
				$num_letra = "diez ";
				break;
			}
			case 11:
			{
				$num_letra = "once ";
				break;
			}
			case 12:
			{
				$num_letra = "doce ";
				break;
			}
			case 13:
			{
				$num_letra = "trece ";
				break;
			}
			case 14:
			{
				$num_letra = "catorce ";
				break;
			}
			case 15:
			{
				$num_letra = "quince ";
				break;
			}
			case 16:
			{
				$num_letra = "dieciseis ";
				break;
			}
			case 17:
			{
				$num_letra = "diecisiete ";
				break;
			}
			case 18:
			{
				$num_letra = "dieciocho ";
				break;
			}
			case 19:
			{
				$num_letra = "diecinueve ";
				break;
			}
		}
	}
	else
		$num_letra = unidad($numero);
 
	return $num_letra;
}
 
function centena($numero)
{
	if ($numero >= 100)
	{
		if ($numero >= 900 && $numero <= 999)
		{
			$num_letra = "novecientos ";
 
			if ($numero > 900)
				$num_letra = $num_letra.decena($numero - 900);
		}
		else if ($numero >= 800 && $numero <= 899)
		{
			$num_letra = "ochocientos ";
 
			if ($numero > 800)
				$num_letra = $num_letra.decena($numero - 800);
		}
		else if ($numero >= 700 && $numero <= 799)
		{
			$num_letra = "setecientos ";
 
			if ($numero > 700)
				$num_letra = $num_letra.decena($numero - 700);
		}
		else if ($numero >= 600 && $numero <= 699)
		{
			$num_letra = "seiscientos ";
 
			if ($numero > 600)
				$num_letra = $num_letra.decena($numero - 600);
		}
		else if ($numero >= 500 && $numero <= 599)
		{
			$num_letra = "quinientos ";
 
			if ($numero > 500)
				$num_letra = $num_letra.decena($numero - 500);
		}
		else if ($numero >= 400 && $numero <= 499)
		{
			$num_letra = "cuatrocientos ";
 
			if ($numero > 400)
				$num_letra = $num_letra.decena($numero - 400);
		}
		else if ($numero >= 300 && $numero <= 399)
		{
			$num_letra = "trescientos ";
 
			if ($numero > 300)
				$num_letra = $num_letra.decena($numero - 300);
		}
		else if ($numero >= 200 && $numero <= 299)
		{
			$num_letra = "doscientos ";
 
			if ($numero > 200)
				$num_letra = $num_letra.decena($numero - 200);
		}
		else if ($numero >= 100 && $numero <= 199)
		{
			if ($numero == 100)
				$num_letra = "cien ";
			else
				$num_letra = "ciento ".decena($numero - 100);
		}
	}
	else
		$num_letra = decena($numero);
 
	return $num_letra;
}
 
function cien()
{
	global $importe_parcial;
 
	$parcial = 0; $car = 0;
 
	while (substr($importe_parcial, 0, 1) == 0)
		$importe_parcial = substr($importe_parcial, 1, strlen($importe_parcial) - 1);
 
	if ($importe_parcial >= 1 && $importe_parcial <= 9.99)
		$car = 1;
	else if ($importe_parcial >= 10 && $importe_parcial <= 99.99)
		$car = 2;
	else if ($importe_parcial >= 100 && $importe_parcial <= 999.99)
		$car = 3;
 
	$parcial = substr($importe_parcial, 0, $car);
	$importe_parcial = substr($importe_parcial, $car);
 
	$num_letra = centena($parcial).centavos();
 
	return $num_letra;
}
 
function cien_mil()
{
	global $importe_parcial;
 
	$parcial = 0; $car = 0;
 
	while (substr($importe_parcial, 0, 1) == 0)
		$importe_parcial = substr($importe_parcial, 1, strlen($importe_parcial) - 1);
 
	if ($importe_parcial >= 1000 && $importe_parcial <= 9999.99)
		$car = 1;
	else if ($importe_parcial >= 10000 && $importe_parcial <= 99999.99)
		$car = 2;
	else if ($importe_parcial >= 100000 && $importe_parcial <= 999999.99)
		$car = 3;
 
	$parcial = substr($importe_parcial, 0, $car);
	$importe_parcial = substr($importe_parcial, $car);
 
	if ($parcial > 0)
	{
		if ($parcial == 1)
			$num_letra = "mil ";
		else
			$num_letra = centena($parcial)." mil ";
	}
 
	return $num_letra;
}
 
 
function millon()
{
	global $importe_parcial;
 
	$parcial = 0; $car = 0;
 
	while (substr($importe_parcial, 0, 1) == 0)
		$importe_parcial = substr($importe_parcial, 1, strlen($importe_parcial) - 1);
 
	if ($importe_parcial >= 1000000 && $importe_parcial <= 9999999.99)
		$car = 1;
	else if ($importe_parcial >= 10000000 && $importe_parcial <= 99999999.99)
		$car = 2;
	else if ($importe_parcial >= 100000000 && $importe_parcial <= 999999999.99)
		$car = 3;
 
	$parcial = substr($importe_parcial, 0, $car);
	$importe_parcial = substr($importe_parcial, $car);
 
	if ($parcial == 1)
		$num_letras = "un millón ";
	else
		$num_letras = centena($parcial)." millones ";
 
	return $num_letras;
}
 
function convertir_a_letras($numero)
{
	global $importe_parcial;
    
	if(!isset($num_letras)) $num_letras='';
	
	$importe_parcial = $numero;
 
	if ($numero < 1000000000)
	{
		if ($numero >= 1000000 && $numero <= 999999999.99)
			$num_letras = millon().cien_mil().cien();
		else if ($numero >= 1000 && $numero <= 999999.99)
			$num_letras = cien_mil().cien();
		else if ($numero >= 1 && $numero <= 999.99)
			$num_letras = cien();
		else if ($numero >= 0.01 && $numero <= 0.99)
		{
			if ($numero == 0.01)
				$num_letras = "un centavo";
			else
				$num_letras = convertir_a_letras(($numero * 100)."/100").($numero * 100)."/100 centavos";
		}
	}
	return $num_letras;
}
?>

