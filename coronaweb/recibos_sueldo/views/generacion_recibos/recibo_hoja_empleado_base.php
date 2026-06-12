 <style>
        /* Configuración de página en impresión */
        @page {
            size: A4 portrait;
            margin: 1mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
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
    <table style="width:100%; border-collapse: collapse; font-size:8px; table-layout: fixed;">
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
     <div class="row m-2 border-bottom pb-1">    
        <div class="col">
            <div style="display: table-cell; width: 50%; vertical-align: top;">
                    <div>Apell. y Nombre: <?php echo htmlspecialchars($empleado['empleado'] ?? ''); ?></div>
                    <div>C.U.I.L.: <?php echo htmlspecialchars($empleado['cuil'] ?? ''); ?></div>
                    <div>F.Ingreso: <?php echo htmlspecialchars($empleado['fechaingreso'] ?? ''); ?></div>
                    <div>Legajo N°: <?php echo htmlspecialchars($empleado['leg'] ?? ''); ?></div>
                    <div>Categoría: <?php  
                        if(!empty($empleado['cat_ii']) and $empleado['cat_i'] != $empleado['cat_ii']){
                            $categoria = $empleado['cat_i'] . '  ' . $empleado['cat_ii'] ?? '';
                        } else {
                            $categoria = $empleado['cat_i'] ?? '';
                        }                                      
                        echo htmlspecialchars($categoria); 
                    ?></div>
                    <div>Cargo: <?php echo htmlspecialchars($empleado['cargo'] ?? ''); ?></div>
            </div>
        </div>
        <div class="col">
                <div style="display: table-cell; width: 50%; vertical-align: top;">
                    <div>Domicilio: <?php echo htmlspecialchars($empleado['calle'] ?? ''); ?></div>
                    <div>Género: <?php echo htmlspecialchars($empleado['genero'] ?? ''); ?></div>
                    <div>Est.Civil: <?php echo htmlspecialchars($empleado['estado_civil'] ?? ''); ?></div>
                    <div>Fec.Nacim: <?php echo htmlspecialchars($empleado['fechanacimiento'] ?? ''); ?> &nbsp;&nbsp; Jornal: <?php echo number_format($empleado['jornal'] ?? $empleado['basico_rem'] ?? 0, 0, ',', '.'); ?></div>
                    <div>Antigüedad: <?php echo htmlspecialchars($empleado['antiguedad'] ?? ''); ?></div>
                    <div>Mod.Contratación: <?php echo htmlspecialchars($empleado['modo_contratacion'] ?? ''); ?></div>
                </div>
            </div>
        </div>       
        
    

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
    <div style="display: table; width: 100%; margin-bottom: 1px; border-bottom: 1px solid #000; padding-bottom: 3px; font-size: 8px;">
        <div style="display: table-cell; width: 100%; vertical-align: top;">
            <div>Fec.Ult. Deposito Aportes: <?php echo htmlspecialchars(fechaDma($empleado['fecha_ultimo_aporte']) ?? ''); ?></div>
            <div>Periodo Ult. Dep.: <?php echo htmlspecialchars($empleado['periodo_liquidacion'] ?? ''); ?></div>
            <div>Banco: <?php echo htmlspecialchars($empleado['banco_aporte'] ?? ''); ?></div>
        </div>
    </div>

    <div style="display: table; width: 100%; margin-bottom: 1px; border-bottom: 1px solid #000; padding-bottom: 3px;">
        <div style="display: table-cell; width: 100%; vertical-align: top;">
            <div><strong>Son Pesos:</strong> <?php echo convertir_a_letras($empleado['neto_rounded']); ?></div>
        </div>
    </div>

    <div style="display: table; width: 100%; font-size: 7px;">
        <div style="display: table-cell; width: 70%; vertical-align: top;">
            <div>Fecha de Pago: <?php echo htmlspecialchars(fechaDma($empleado['fecha_pago']) ?? ''); ?></div>
            <div>Lugar de Pago: Ingenio la Corona - Concepción</div>
            <div>El presente es duplicado del recibo Original que obra en nuestro poder firmado por el empleado</div>
            <div>Recibo Leyes 17250, 20744 y 21297</div>
        </div>
        <div style="display: table-cell; width: 30%; vertical-align: bottom; text-align: center;">            
            <div style="border-top: 1px solid #000; padding-top: 15px; margin-top: 10px;">Firma empleado</div>
        </div>
    </div>
</div>