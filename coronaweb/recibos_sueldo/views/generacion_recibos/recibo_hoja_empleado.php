 <!DOCTYPE html>
	<head>
		<title>Recibos de Sueldo</title>
        
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
 <style>
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
       
        .card-hover:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            background-color: #f8f9fa; /* opcional para efecto suave */
        }

        .card-hover h4 {
            transition: color 0.3s;
        }

        .card-hover:hover h4 {
            color: #0d6efd; /* cambia el texto al azul de Bootstrap */
        }

        /* Márgenes laterales de 2px en todo el ancho */
        .full-width-container {
            padding-left: 2px;
            padding-right: 2px;
        }
        /* Configuración de página en impresión */
        @page {
            size: A4 portrait;
            margin: 1mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            background-color: #fff;
        }

        .no-print {
            display: block;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .page-break {
                page-break-after: always;
            }
            
            .recibo-pdf {
                width: 100%;
                height: 100%;
                page-break-inside: avoid;
            }
        }

        /* Contenedor de cada hoja */
        .recibo-container {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            width: 90%;
            box-sizing: border-box;
            margin-bottom: 20px;
        }

        /* Mitad de hoja para cada recibo */
        .recibo-content {
            width: 50%;
            padding: 8mm;
            box-sizing: border-box;
            overflow: hidden;
        }

        .recibo-content:last-child {
            border-right: none;
        }
        
        .form-container {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .btn-generate {
            margin-top: 10px;
        }
        
        .alert ul {
            margin-bottom: 0;
        }
        
        /* Estilos para la vista previa del recibo */
        .recibo-pdf {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm;
            margin: 10px auto;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background: white;
        }
    </style>
<?php 
                if(!empty($_POST)){
                    if($_POST['tipo_liq']=='MENSUAL_BIO'){
                        
                        $membrete = '
                                        <div><strong>BIOENERGIA LA CORONA S.A.</strong></div>
                                        <div>C.U.I.T.: 30710964374</div>
                                        <div>INGENIO LA CORONA S/N - Concepción</div>
                                    ';
                        $firma = 'firma_mcorrea.png';
                    }else{
                        
                        $membrete = '
                                        <div><strong>SUCROALCOHOLERA DEL SUR S.A.</strong></div>
                                        <div>C.U.I.T.: 30-71509855-1</div>
                                        <div>INGENIO LA CORONA S/N - Concepción</div>
                                    ';
                        $firma = 'firma_recibo_dsalica.png';
                    }
                }
                
?>
<div style="font-family: Arial, sans-serif; font-size:9px; border:1px solid #000; padding:5px; box-sizing:border-box; width:100%;">

    <!-- Encabezado -->
    <div class="row m-2 align-items-start">
        <div style="display: table-cell; width: 20%; vertical-align: top; text-align: left;">
            <?php if (file_exists($ruta_logo_absoluta)): ?>
                <img src="<?php echo $ruta_logo_absoluta; ?>" alt="Logo Empresa" style="height:25px; width:auto;">
            <?php else: ?>
                <div style="color: red; font-size: 8px;"></div>
            <?php endif; ?>
        </div>        
    </div>
    <!-- Datos principales -->
    
        <div style="display: table-cell; width: 50%; vertical-align: top;">
            <div><strong><?php echo htmlspecialchars($empleado['quincena_liquidacion'] ?? ''); ?></strong></div>
            <div>Periodo de Liquidación: <?php echo htmlspecialchars($empleado['periodo_liquidacion'] ?? ''); ?></div>
        </div>
        <div style="display: table-cell; width: 50%; vertical-align: top; text-align: right;">
            <?php echo date('d/m/Y'); ?>
        </div>
    

    <!-- Datos del empleado -->
     <table style="width:100%; border-collapse: collapse; font-size:8px; table-layout: fixed;">
        <tbody>
            <th>
                <td><div>Apell. y Nombre: <?php echo htmlspecialchars($item['empleado'] ?? ''); ?></div></td>
                <td><div>Domicilio: <?php echo htmlspecialchars($item['calle'] ?? ''); ?></div></td>
            </th>
            <th>
                <td><div>C.U.I.L.: <?php echo htmlspecialchars($item['cuil'] ?? ''); ?></div></td>
                <td><div>Género: <?php echo htmlspecialchars($item['genero'] ?? ''); ?></div></td>
            </th>
            <th>
                <td><div>F.Ingreso: <?php echo htmlspecialchars($item['fechaingreso'] ?? ''); ?></div></td>
                <td><div>Est.Civil: <?php echo htmlspecialchars($item['estado_civil'] ?? ''); ?></div></td>
            </th>
            <th>
                <td><div>Legajo N°: <?php echo htmlspecialchars($item['leg'] ?? ''); ?></div></td>
                <td><div>Fec.Nacim: <?php echo htmlspecialchars($item['fechanacimiento'] ?? ''); ?> &nbsp;&nbsp; Jornal: <?php echo number_format($item['jornal'] ?? 0 .$item['basico_rem'] ?? 0, 0, ',', '.'); ?></div></td>
            </th>
            <th>
                <td><div>Categoría: <?php   if(!empty($item['cat_ii']) and $item['cat_i']<>$item['cat_ii']){
                                        $categoria = $item['cat_i'].'  '.$item['cat_ii'] ?? '';
                                    }else{
                                        $categoria = $item['cat_i'];
                                    }                                      
                                    echo htmlspecialchars($categoria ?? ''); 
                            ?>
                    </div>
                </td>
                <td><div>Antigüedad: <?php echo htmlspecialchars($item['antiguedad'] ?? ''); ?></div></td>
            </th>
            <tr>
                <td><div>Cargo: <?php echo htmlspecialchars($item['cargo'] ?? ''); ?></div></td>
                <td><div>Mod.Contratación: <?php echo htmlspecialchars($item['modo_contratacion'] ?? ''); ?></div></td>
            </tr>
        </tbody>
     </table>     

    <!-- Detalle de haberes -->
    <div style="margin: 5px;">
        <table style="width:100%; border-collapse: collapse; font-size:8px; table-layout: fixed;">
            <thead style="background-color: #f8f9fa;">
                <tr>
                    <th style="width:40%; border:1px solid #000; padding:2px; text-align: left;">CONCEPTO</th>
                    <th style="width:10%; border:1px solid #000; padding:2px; text-align: center;">Cantidad</th>
                    <th style="width:15%; border:1px solid #000; padding:2px; text-align: center;">V.Unit.</th>
                    <th style="width:15%; border:1px solid #000; padding:2px; text-align: center;">Haberes</th>
                    <th style="width:20%; border:1px solid #000; padding:2px; text-align: center;">Deducciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Incluir detalle de conceptos simplificado para PDF
                ob_start();
                include 'detalle_conceptos.php';
                $detalle = ob_get_clean();
                echo limpiarEstilosParaPDF($detalle);
                ?>
                <tr>
                    <td style="border:1px solid #000; padding:2px;"></td>
                    <td style="border:1px solid #000; padding:2px;"></td>
                    <td style="border:1px solid #000; padding:2px; text-align: center;"><strong>Tot.Remun.</strong></td>
                    <td style="border:1px solid #000; padding:2px; text-align: center;"><strong>Tot. NoRemun.</strong></td>
                    <td style="border:1px solid #000; padding:2px; text-align: center;"><strong>Deducciones</strong></td>
                </tr>
                <tr>
                    <td style="border:1px solid #000; padding:2px;"></td>
                    <td style="border:1px solid #000; padding:2px;"></td>
                    <td style="border:1px solid #000; padding:2px; text-align: center;"><?php echo number_format($empleado['bruto_total'] ?? 0, 2, ',', '.');?></td>
                    <td style="border:1px solid #000; padding:2px; text-align: center;"><?php echo number_format($empleado['no_remunerativo'] ?? 0, 2, ',', '.');?></td>
                    <td style="border:1px solid #000; padding:2px; text-align: center;">
                        <?php 
                            $deducciones = 0;
                            if(!empty($empleado['neto'])){
                                $deducciones = ($empleado['bruto_total'] ?? 0) + ($empleado['no_remunerativo'] ?? 0) - ($empleado['neto'] ?? 0);
                            }
                            echo number_format($deducciones, 2, ',', '.');
                        ?>
                    </td>
                </tr>
                <tr style="font-size: 10px;">
                    <td style="border:1px solid #000; padding:2px;"></td>
                    <td style="border:1px solid #000; padding:2px;"></td>
                    <td style="border:1px solid #000; padding:2px;"></td>
                    <td style="border:1px solid #000; padding:2px; text-align: center;"><strong>NETO</strong></td>
                    <td style="border:1px solid #000; padding:2px; text-align: center;"><strong><?php echo number_format($empleado['neto_rounded'] ?? 0, 2, ',', '.');?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Otros datos -->
    <div class="row m-2 border-bottom pb-1" style="font-size: 9px;">
        <div class="col">
            <div>Fec.Ult. Deposito Aportes: <?php echo htmlspecialchars(fechaDma($item['fecha_ultimo_aporte']) ?? ''); ?></div>
            <div>Periodo Ult. Dep.: <?php echo htmlspecialchars($item['periodo_liquidacion'] ?? ''); ?></div>
            <div>Banco: <?php echo htmlspecialchars($item['banco_aporte'] ?? ''); ?></div>
            
        </div>
    </div>

    <div class="row m-2 border-bottom pb-1 ">
        <div class="col">
            <div><strong>Son Pesos:</strong> <?php echo convertir_a_letras($item['neto_rounded']); ?></div>
        </div>
    </div>

    <div class="row m-2" style="font-size: 8px;">
        <div class="col-8">
            <div>Fecha de Pago: <?php echo htmlspecialchars(fechaDma($item['fecha_pago']) ?? ''); ?></div>
            <div>Lugar de Pago: Ingenio la Corona - Concepción</div>
            <div>El presente es duplicado del recibo Original que obra en nuestro poder firmado por el empleado</div>
            <div>Recibo Leyes 17250, 20744 y 21297</div>
        </div>
        <div class="col-4">            
            <img src="assets/img/<?php echo $firma ?? '';?>" alt="">            
        </div>
        
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</html>