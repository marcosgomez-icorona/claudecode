<?php
	
	$mysqli = new mysqli("localhost", "root", "", "db_extranet");
	mysqli_set_charset($mysqli,"utf8");

	if ($mysqli -> connect_errno) {
		echo "Error al Conectarse con la Base de Datos: " . $mysqli -> connect_error;
		exit();
	}

	function conexion_db(){
		$mysqli = new mysqli("localhost", "root", "", "db_extranet");
		mysqli_set_charset($mysqli,"utf8");

		if ($mysqli -> connect_errno) {
			echo "Error al Conectarse con la Base de Datos: " . $mysqli -> connect_error;
			exit();
		}

		return $mysqli;
	}
	
?>	
