<!DOCTYPE html>
	<head>
		<title>Mantenimiento</title>

        <link rel="stylesheet" href="ccs/bootstrap.css">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        
        <link rel="stylesheet" href="css/menu.css">
        <link rel="stylesheet" href="css/styles.css">
        <link rel="stylesheet" href="css/jquery-ui.css">
        
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
            margin: 0; padding: 0;
            Font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }
        .table{
                overflow: scroll;
                font-size: 12px;
                
        }

        .container {
                    padding-left: 0 !important;
                    padding-right: 0 !important;
                    width: 100% !important;
        }
        footer {
                    position: absolute;
                    bottom: 0;
                    width: 100%;
                    /*height: 1px;*/
                    
        }
        
		</style>
	</head>
	<body>
        <div class="row justify-content-center"> 
            
                <?php 
                    
                    //---------Certificaciones de Servicio-----------//
                    
                    //Planilla Liquidacion Final
                    if(!empty($_GET['accion']) and $_GET['accion']=='descargar_form_liq_final'){
                        include 'assets/doc/Planilla Liquidacion Final.pdf';
                    }
                    
               
                ?>
        </div>
	</body>
</html>