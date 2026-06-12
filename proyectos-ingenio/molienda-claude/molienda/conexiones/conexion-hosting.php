<?php

	$mysqli = new mysqli("phpadmin.srv1576197.hstgr.cloud", "admin", "Balitec$", "db_corona","4150");
	$mysqli->set_charset("utf8mb4");

	if ($mysqli -> connect_errno) {
		echo "Error al Conectarse con la Base de Datos: " . $mysqli -> connect_error;
		exit();
	}

	function conexion_db(){
	        $mysqli = new mysqli("phpadmin.srv1576197.hstgr.cloud", "admin", "Balitec$", "db_corona","4150");
		$mysqli->set_charset("utf8mb4");

		if ($mysqli -> connect_errno) {
			echo "Error al Conectarse con la Base de Datos: " . $mysqli -> connect_error;
			exit();
		}

		return $mysqli;
	}

	// Conexión a la base legada (cañeros, OC, OP, proveedores)
	function conexion_db_molienda(){
		$mysqli = new mysqli("phpadmin.srv1576197.hstgr.cloud", "admin", "Balitec$", "db_corona","4150");
		$mysqli->set_charset("utf8mb4");

		if ($mysqli -> connect_errno) {
			error_log("Error conexion db_molienda: " . $mysqli -> connect_error);
			return null;
		}

		return $mysqli;
	}

?>