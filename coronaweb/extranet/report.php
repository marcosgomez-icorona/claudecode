<!DOCTYPE html>
	<head>
		<title>Mantenimiento</title>

        
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        
        <link rel="stylesheet" href="css/menu.css">
        <link rel="stylesheet" href="css/styles.css">
         <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="js/jquery.min.js"></script>  
        <script src="js/bootstrap.js" ></script>
        <script src="js/jquery.js"></script>
    
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
        /*
			* {
				margin:0px;
				padding:0px;
			}
			
			#header {
				margin:auto;
				width:500px;
				font-family:Arial, Helvetica, sans-serif;
			}
			
			ul, ol {
				list-style:none;
			}
			
			.nav {
				width:500px; 
				margin:0 auto; 
			}

			.nav > li {
				float:left;
			}
			
			.nav li a {
				background-color:#434851;
				color:#fff;
				text-decoration:none;
				padding:11px 15px;
				display:block;
			}
			
			.nav li a:hover {
				background-color:#000;
			}
			
			.nav li ul {
				display:none;
				position:absolute;
				min-width:140px;
			}
			
			.nav li:hover > ul {
				display:block;
			}
			
			.nav li ul li {
				position:relative;
                
			}
			
			.nav li ul li ul {
				right:-100%;               
				
			}

            .nav li ul li ul li ul{
				right:-100%;               
				
			}
			*/
		</style>
	</head>
	<body>
        <div class="row justify-content-center"> 
            
                <?php
				        
                    //--------Mantenimiento-----------------------//
                    
                    if(!empty($_GET['accion']) and $_GET['accion']=='visualizar_detalle_ot'){
                        include 'views/equipos/preview.php';
                    }


                                        
                      //---------------------//

                    
               
                ?>
        </div>
	</body>
</html>