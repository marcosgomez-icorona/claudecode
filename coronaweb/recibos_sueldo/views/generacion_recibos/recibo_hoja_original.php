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
<div class="recibo border border-dark" style="font-family: Arial, sans-serif; font-size:10px; box-sizing:border-box; width:100%;">

    <!-- Encabezado -->
    <div class="row m-2 align-items-start">
        <div class="col-2 text-start">
            <img src="assets/img/Logo-Ing La Corona.png" alt="Logo Empresa" style="max-height:25px; max-width:100%;">
        </div>
        <div class="col-10 text-center">
            <!-- espacio para título -->
        </div>
    </div>

    <!-- Datos principales -->
    <div class="row m-2 align-items-start border-bottom border-dark pb-1">
        <div class="col-6">
            <div><strong><?php echo htmlspecialchars($item['quincena_liquidacion'] ?? ''); ?></strong></div>
            <div>Periodo de Liquidación: <?php echo htmlspecialchars($item['periodo_liquidacion'] ?? ''); ?></div>
        </div>
        <div class="col-6 text-end">
            <?php echo $membrete ?? '';?>
        </div>
    </div>

    <!-- Datos del empleado -->
    <div class="row m-2 border-bottom pb-1">
        <div class="col-6">
            <div>Apell. y Nombre: <?php echo htmlspecialchars($item['empleado'] ?? ''); ?></div>
            <div>C.U.I.L.: <?php echo htmlspecialchars($item['cuil'] ?? ''); ?></div>
            <div>F.Ingreso: <?php echo htmlspecialchars($item['fechaingreso'] ?? ''); ?></div>
            <div>Legajo N°: <?php
                $legDisplay = $item['leg'] ?? '';
                $legDisplay = preg_replace('/^(50|60)/', '', $legDisplay);
                echo htmlspecialchars($legDisplay);
            ?></div>
            <div>Categoría: <?php   if(!empty($item['cat_ii']) and $item['cat_i']<>$item['cat_ii']){
                                        $categoria = $item['cat_i'].'  '.$item['cat_ii'] ?? '';
                                    }else{
                                        $categoria = $item['cat_i'];
                                    }                                      
                                    echo htmlspecialchars($categoria ?? ''); 
                            ?>
            </div>
            <div>Cargo: <?php echo htmlspecialchars($item['cargo'] ?? ''); ?></div>
        </div>
        <div class="col-6">
            <div>Domicilio: <?php echo htmlspecialchars($item['calle'] ?? ''); ?></div>
            <div>Género: <?php echo htmlspecialchars($item['genero'] ?? ''); ?></div>
            <div>Est.Civil: <?php echo $item['estado_civil'] ?? ''; ?></div>
            <div>Fec.Nacim: <?php echo htmlspecialchars($item['fechanacimiento'] ?? ''); ?> &nbsp;&nbsp; Jornal: <?php echo number_format($item['jornal'] ?? 0 .$item['basico_rem'] ?? 0, 0, ',', '.'); ?></div>
            <div>Antigüedad: <?php echo htmlspecialchars($item['antiguedad'] ?? ''); ?></div>
            <div>Mod.Contratación: <?php echo htmlspecialchars($item['modo_contratacion'] ?? ''); ?></div>
        </div>
    </div>

    <!-- Detalle de haberes -->
    <div class="detalle-table m-2">
        <table class="table table-bordered table-sm" style="font-size:9px; width:100%; table-layout:fixed;">
            <thead class="table-light text-dark">
                <tr >
                    <th style="width:40%;">CONCEPTO</th>
                    <th style="width:10%;">Cantidad</th>
                    <th style="width:15%;">V.Unit.</th>
                    <th style="width:15%;">Haberes</th>
                    <th style="width:20%;">Deducciones</th>
                </tr>
            </thead>
            <tbody class="text-end">
                <?php include 'detalle_conceptos.php'; ?>
                <tr>
                    <td></td><td></td>
                    <td><strong>Tot. Remun.</strong></td>
                    <td><strong>Tot. No Remun.</strong></td>
                    <td><strong>Deducciones</strong></td>
                </tr>
                <tr>
                    <td></td><td></td>
                    <td><?php echo number_format($item['bruto_total'] ?? 0, 2, ',', '.');?></td>
                    <td><?php echo number_format($item['no_remunerativo'] ?? 0, 2, ',', '.');?></td>
                    <td>
                        <?php 
                            $deducciones = 0;
                            if(!empty($item['neto'])){
                                $deducciones = ($item['bruto_total'] ?? 0) + ($item['no_remunerativo'] ?? 0) - ($item['neto'] ?? 0);
                            }
                            echo number_format($deducciones, 2, ',', '.');
                        ?>
                    </td>
                </tr>
                <tr style="font-size: 12px;">
                    <td></td><td></td><td></td>
                    <td><strong>NETO</strong></td>
                    <td><strong><?php echo number_format($item['neto_rounded'] ?? 0, 2, ',', '.');?></strong></td>
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
            <div><strong>Son Pesos:</strong> <?php 
                                                    if(!empty($item['neto']) and round($item['neto'])==685000){
                                                        echo 'seiscientos ochenta y cinco mil';
                                                    }else{
                                                        if(!empty($item['neto']) and round($item['neto'])==2000000){
                                                            echo 'dos millones ';
                                                        }else{
                                                            if(!empty($item['neto']) and round($item['neto'])==2600000){
                                                                echo 'dos millones seiscientos mil ';
                                                            }else{
                                                                if(!empty($item['neto']) and round($item['neto'])==2758000){
                                                                    echo 'dos millones setecientos cincuenta y ocho mil ';
                                                                }else{
                                                                    if(!empty($item['neto']) and round($item['neto'])==1059000){
                                                                        echo 'un millo cincuenta y nueve mil ';
                                                                    }else{
                                                                        echo convertir_a_letras(round($item['neto'] ?? 0) ); 
                                                                    }                                                                    
                                                                }
                                                            }                                                            
                                                        }
                                                        
                                                    }
                                                    
                                                    
                                                    
                                            ?>
            </div>
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
