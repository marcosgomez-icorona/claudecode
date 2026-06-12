<!DOCTYPE html>
	<head>
		<title>Liquidaciones y Recibos</title>
        
        <link rel="stylesheet" href="css/bootstrap.min.css">
        
        <link rel="stylesheet" href="css/menu.css">
        <link rel="stylesheet" href="css/styles.css">
        <link rel="stylesheet" href="css/jquery-ui.css">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="js/jquery.min.js"></script>  
        <script src="js/bootstrap.js" ></script>
        <script src="js/jquery.js"></script>
        <script src="js/jquery-ui.js"></script>
        
      <script src="js/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
      <script src="js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    
    <!-- Busquedor dentro de un Select  -->
    <link rel="stylesheet" href="css/bootstrap-select.min.css">        
    <script src="js/bootstrap-select.min.js"></script>
   
        <style type="text/css">
		    html,body{
            margin: 3px; padding: 3px;
            Font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }
        .table{
                overflow: scroll;
                font-size: 12px;
                
        }

        .container {
                    padding-left: 2% !important;
                    padding-right: 2% !important;
                    width: 110% !important;
        }
       
		</style>
	</head>
	<body>
                    
                <?php 

                    if(!empty($_GET['menu']) and $_GET['menu']=='validacion_recibo_empleado'){
                        include 'views/usuarios/login.php';
                    }

                    if(!empty($_GET['accion']) and $_GET['accion']=='ver_recibos_empleado'){
                        include 'views/recibos/form.php';
                    }

                    if(empty($_GET['menu']) and empty($_GET['accion'])){
                        include 'views/usuarios/login.php';
                    }

                    if(!empty($_GET['menu']) and $_GET['menu']=='cambiar_clave'){
                        include 'views/usuarios/cambiar_clave.php';
                    }
               
                ?>
       
	</body>
</html>