<?php
    $detalle_haberes ='';
    /* ITEMS DE DETALLE DE HABERES */
if(!empty($item)){
    //1001
    if($item['basico_rem'] ?? 0 >0){
        $basico_rem = " <tr>  
                            <td class='text-start'>1001 - Sueldo Mensual </td>
                            <td>Ds. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp".$item['jornales']."</td>
                            <td>".number_format(($item['basico_rem']/30) ?? 0, 0, ',', '.')."</td>
                            <td>".number_format($item['basico_rem'] ?? 0, 0, ',', '.')."</td>
                            <td></td>
                        </tr>       
                ";
        $detalle_haberes .=$basico_rem ?? '';
            
    }
    //1003
    if($item['jornal'] ?? 0 >0){
        $jornal = " <tr>  
                       <td class='text-start'>1003 - Jornal </td>
                        <td>Ds. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['jornales']."</td>
                        <td> ".number_format($item['jornal'] ?? 0, 0, ',', '.')." </td>
                        <td> ".number_format($item['sueldo_basico'] ?? 0, 2, ',', '.')." </td>
                        <td></td>
                    </tr>       
                ";
        $detalle_haberes .=$jornal ?? '';
            
    }
    //1100
    if($item['escalafon_pago'] ?? 0 >0){
        
            $antiguedad = $item['antiguedad'] ?? '';
            $escalafon = $item['escalafon'] ?? '';
        
        $escalafon_pago = " <tr>  
                       <td class='text-start'>1100 - Escalafón </td>
                        <td>an. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$antiguedad."</td>
                        <td> ".number_format($escalafon ?? 0, 2, ',', '.')." </td>
                        <td> ".number_format($item['escalafon_pago'] ?? 0, 2, ',', '.')." </td>                        
                        <td></td>
                    </tr>       
                ";
        $detalle_haberes .=$escalafon_pago ?? '';
            
        }
        //1203
        if($item['no_remunerativo'] ?? 0 >0){
            $no_remunerativo = " <tr>  
                        <td class='text-start'>1203 - Gratif.NO.Remun. ()</td>
                            <td>Un. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1</td>
                            <td> </td>
                            <td></td>
                            <td></td>
                        </tr>       
                    ";
            $detalle_haberes .=$no_remunerativo ?? '';
            
        }
        //1061
        if($item['dias_de_enfermedad_pago'] ?? 0 >0){
            if(!empty($item['basico_rem'])){ 
                $valor_dia = number_format(($item['basico_rem']/30 ?? 0), 2, ',', '.');
            }else{
                $valor_dia = '';
            }
            $jornal = $item['jornal'] ?? ''.($valor_dia);

            $dias_de_enfermedad = " <tr> 
                                        <td class='text-start'>1061 - Dias de Enfermedad </td>
                                        <td>Ds. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['dias_de_enfermedad']."</td>
                                        <td> ".$jornal."</td>
                                        <td> ".number_format($item['dias_de_enfermedad_pago'] ?? 0, 2, ',', '.')." </td>                                                                
                                        <td></td>
                                    </tr>";
            $detalle_haberes .=$dias_de_enfermedad ?? '';
        }
        //1009
        if($item['feriado_trabajado_pago'] ?? 0 >0){
            if(!empty($item['basico_rem'])){ 
                $valor_dia = number_format(($item['basico_rem']/30 ?? 0), 2, ',', '.');
            }else{
                $valor_dia = '';
            }
            $jornal = $item['jornal'] ?? ''.($valor_dia);
            $feriado = "<tr> 
                            <td class='text-start'>1009 - Feriado  </td>                            
                            <td>Ds. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['feriado_trabajado']."</td>
                            <td> ".$jornal." </td>
                            <td> ".number_format($item['feriado_trabajado_pago'] ?? 0, 2, ',', '.')." </td>                                                    
                             <td></td>
                        </tr>" ?? "";
            $detalle_haberes .=$feriado ?? '';
        }
        //1030
        if($item['art_pago'] ?? 0 >0){
            $art = "<tr> 
                        <td class='text-start'>1030 - ART  </td>                            
                        <td>Ds. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['art']."</td>
                        <td> </td>
                        <td> ".number_format($item['art_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$art ?? '';
        }
        //1072
        if($item['retiro_anticipado_pago'] ?? 0 >0){
            
            $retiro_anticipado = "<tr> 
                        <td class='text-start'>1072 - Retiro Anticipado  </td>                            
                        <td>Ds. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['retiro_anticipado']."</td>
                        <td> </td>
                        <td> ".number_format($item['retiro_anticipado_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$retiro_anticipado ?? '';
        }
        
        
        
       
        if($item['lic_s_goce_pago'] ?? 0 >0){
            $lic_s_goce = "<tr> 
                        <td class='text-start'>Lic. Sº/ Goce  </td>                            
                        <td>Ds. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['lic_s_goce']."</td>
                        <td> </td>
                        <td> ".number_format($item['lic_s_goce_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$lic_s_goce ?? '';
        }
        if(!empty($item['donacion_de_sangre_pago'])){
            $donacion_de_sangre = "<tr> 
                        <td class='text-start'>Donacion de Sangre  </td>                            
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['donacion_de_sangre']."</td>
                        <td> </td>
                        <td> ".number_format($item['donacion_de_sangre_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$donacion_de_sangre ?? '';
        }
        //1041
        if($item['altura_pago'] ?? 0 >0){
            if(!empty($item['basico_rem'])){ 
                $valor_dia = number_format(($item['basico_rem']/30 ?? 0), 2, ',', '.');
            }else{
                $valor_dia = '';
            }
            $jornal = $item['jornal'] ?? ''.($valor_dia);
            
            $altura = "<tr> 
                        <td class='text-start'>1041 - Altura </td>                            
                        <td>Ds. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['altura']."</td>
                        <td> ".$jornal."</td>
                        <td> ".number_format($item['altura_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$altura ?? '';
        }
        //1007
        if($item['destajo_pago'] ?? 0 >0){
            if(!empty($item['basico_rem'])){ 
                $valor_dia = number_format(($item['basico_rem']/30 ?? 0), 2, ',', '.');
            }else{
                $valor_dia = '';
            }
            $jornal = $item['jornal'] ?? ''.($valor_dia);

            $destajo = "<tr> 
                        <td class='text-start'>1007 - Destajo </td>                            
                        <td>Ds. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['destajo']."</td>
                        <td>".$jornal." </td>
                        <td> ".number_format($item['destajo_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$destajo ?? '';
        }        
        //1018
        if($item['hs_50_pago'] ?? 0 >0 and $item['nr_mayor_60']>0){
            $hs_50 = "<tr> 
                        <td class='text-start'>1018 - HS 50% </td>                            
                        <td>Hs.  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['hs_50']."</td>
                        <td> </td>
                        <td> ".number_format($item['hs_50_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$hs_50 ?? '';
        }
        //1019
        if($item['hs_100_pago'] ?? 0 >0  and $item['nr_mayor_60']>0){
            $hs_100 = "<tr> 
                        <td class='text-start'>1019 - HS 100% </td>                            
                        <td>Hs.  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['hs_100']."</td>
                        <td> </td>
                        <td> ".number_format($item['hs_100_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$hs_100 ?? '';
        }
        //1022
        if($item['dest_p_bls_67134_pago'] ?? 0 >0){
            $dest_p_bls_67134 = "<tr> 
                        <td class='text-start'>1019 - Dest.P/Bls $ 67.134 </td>                            
                        <td></td>
                        <td> </td>
                        <td> ".number_format($item['dest_p_bls_67134_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$dest_p_bls_67134 ?? '';
        }
        //1023
        if($item['dest_p_bls_52674_pago'] ?? 0 >0){
            $dest_p_bls_52674 = "<tr> 
                        <td class='text-start'>1023 - Dest.P/Bls. $ 52.674 </td>                            
                        <td></td>
                        <td> </td>
                        <td> ".number_format($item['dest_p_bls_52674_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$dest_p_bls_52674 ?? '';
        }
        //1025
        if($item['dest_p_bls_16226_pago'] ?? 0 >0){
            $dest_p_bls_16226 = "<tr> 
                        <td class='text-start'>1025 - Dest.P/Bls. $ 52.674 </td>                            
                        <td>Un. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['dest_p_bls_16226_pago']."</td>
                        <td> </td>
                        <td> ".number_format($item['dest_p_bls_16226_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$dest_p_bls_16226 ?? '';
        }
        //1026
        if($item['dest_p_bls_25053_pago'] ?? 0 >0){
            $dest_p_bls_25053 = "<tr> 
                        <td class='text-start'>1026 - Dest.P/Bls $ 2.5053</td>                            
                        <td>Un. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['dest_p_bls_25053']."</td>
                        <td> </td>
                        <td> ".number_format($item['dest_p_bls_25053_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$dest_p_bls_25053 ?? '';
        }        
        //1118
        if($item['dest_p_bls_31374_pago'] ?? 0 >0){
            $dest_p_bls_31374 = "<tr> 
                        <td class='text-start'>1118 - Dest.P/Bls $3.1374</td>                            
                        <td>Un. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['dest_p_bls_31374']."</td>
                        <td> </td>
                        <td> ".number_format($item['dest_p_bls_31374_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$dest_p_bls_31374 ?? '';
        }
        //1109
        if($item['presentismo_pago'] ?? 0 >0){
            $presentismo = "<tr> 
                        <td class='text-start'>1109 - Pesentismo</td>                            
                        <td>Un. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['presentismo']."</td>
                        <td> </td>
                        <td> ".number_format($item['presentismo_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$presentismo ?? '';
        }
        //1110
        if($item['premio_asistencia'] ?? 0 >0){
            $premio_asist = "<tr> 
                        <td class='text-start'>1110 - Pesentismo Premio Asist.</td>                            
                        <td></td>
                        <td> </td>
                        <td> ".number_format($item['premio_asistencia'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$premio_asist ?? '';
        }
        //1201
        if($item['reint_de_mec'] ?? 0 >0){
            $reint_de_mec = "<tr> 
                        <td class='text-start'>1101- Reint de Mec</td>                            
                        <td>Un. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['reint_de_mec']."</td>
                        <td> </td>
                        <td> </td>
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$reint_de_mec ?? '';
        }

        if(!empty($item['dedicacion_clc'])){
            $dedicacion_clc = "<tr> 
                                    <td class='text-start'>Dedicacion</td>                            
                                    <td>Un. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                    <td> ".number_format($item['dedicacion_clc'] ?? 0, 0, ',', '.')."</td>
                                    <td> ".number_format($item['dedicacion_clc'] ?? 0, 0, ',', '.')."</td>
                                    <td></td>
                                </tr>" ?? "";
            $detalle_haberes .=$dedicacion_clc ?? '';
        }        
        if(!empty($item['importe_concepto_1200'])){
            $importe_concepto_1200 = "<tr> 
                        <td class='text-start'>1200 - Dif + VsX</td>                            
                        <td>Un. </td>
                        <td> ".$item['importe_concepto_1200']."</td>
                        <td> </td>
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$importe_concepto_1200 ?? '';
        }                
        if($item['hs_simples_pago'] ?? 0 >0){
            $hs_simples_pago = "<tr> 
                        <td class='text-start'>Horas Simples</td>                            
                        <td>Hs. </td>
                        <td> ".number_format($item['hs_simples_pago'] ?? 0, 0, ',', '.')." </td>                                                
                        <td> ".number_format($item['hs_simples_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$hs_simples_pago ?? '';
        }
        if($item['lic_c_goce_pago'] ?? 0 >0){
            if(!empty($item['basico_rem'])){ 
                $valor_dia = number_format(($item['basico_rem']/30 ?? 0), 2, ',', '.');
            }else{
                $valor_dia = '';
            }
            $jornal = $item['jornal'] ?? ''.($valor_dia);

            $lic_c_goce_pago = "<tr> 
                        <td class='text-start'>Lic. con goce de Sueldo</td>                            
                        <td>Ds. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['lic_c_goce']."</td>
                        <td>  ".$jornal."</td>
                        <td> ".number_format($item['lic_c_goce_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$lic_c_goce_pago ?? '';
        }
        if($item['dif_de_cat_pago'] ?? 0 >0){
            $lic_c_goce_pago = "<tr> 
                        <td class='text-start'>Dif. De Cat.</td>                            
                        <td></td>
                        <td> </td>
                        <td> ".number_format($item['dif_de_cat_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$lic_c_goce_pago ?? '';
        }
        if($item['reintegro_medicamentos'] ?? 0 >0){
            $reintegro_medicamentos = "<tr> 
                        <td class='text-start'>Reintegro Medicamento</td>                            
                        <td></td>
                        <td> </td>
                        <td> ".number_format($item['reintegro_medicamentos'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$reintegro_medicamentos ?? '';
        }
        if($item['tickey_canasta'] ?? 0 >0){
            $tickey_canasta = "<tr> 
                        <td class='text-start'>Ticket Canasta</td>                            
                        <td></td>
                        <td> </td>
                        <td> ".number_format($item['tickey_canasta'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$tickey_canasta ?? '';
        }
        if($item['dif_vsx'] ?? 0 >0){
            $dif_vsx = "<tr> 
                        <td class='text-start'>Dif. vsx</td>                            
                        <td></td>
                        <td> </td>
                        <td> ".number_format($item['dif_vsx'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$dif_vsx ?? '';
        }
        if($item['adicional_movil'] ?? 0 >0){
            $adicional_movil = "<tr> 
                        <td class='text-start'>Adicional Movil</td>                            
                        <td></td>
                        <td> </td>
                        <td> ".number_format($item['adicional_movil'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$adicional_movil ?? '';
        }
        if($item['material_descartable'] ?? 0 >0){
            $material_descartable = "<tr> 
                        <td class='text-start'>Material Descartable</td>                            
                        <td></td>
                        <td> </td>
                        <td> ".number_format($item['material_descartable'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$material_descartable ?? '';
        }
        if($item['nr_mayor_60'] ?? 0 >0){
            $nr_mayor_60 = "<tr> 
                        <td class='text-start'>Gratif. Remunerativa > 60</td>                            
                        <td></td>
                        <td> ".number_format($item['nr_mayor_60'] ?? 0, 2, ',', '.')." </td>                                                
                        <td> ".number_format($item['nr_mayor_60'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$nr_mayor_60 ?? '';
        }
        if($item['titulo_pago'] ?? 0 >0){
            $nr_mayor_60 = "<tr> 
                        <td class='text-start'>Titulo</td>                            
                        <td></td>
                        <td> ".number_format($item['titulo_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td> ".number_format($item['titulo_pago'] ?? 0, 2, ',', '.')." </td>                                                
                        <td></td>
                    </tr>" ?? "";
            $detalle_haberes .=$nr_mayor_60 ?? '';
        }
        //DEDUCCIONES
        if(!empty($item['ley_19032'])){
            $ley_19032 = "<tr> 
                                    <td class='text-start'>2500 - Ley 19032</td>                            
                                    <td>%  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 3</td>
                                    <td> 3</td>
                                    <td></td>
                                    <td> ".number_format(abs($item['ley_19032']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$ley_19032 ?? '';
        }
        if(!empty($item['jubilacion'])){
            $jubilacion = "<tr> 
                                    <td class='text-start'>2501 - Jubilacion</td>                            
                                    <td>%  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 11</td>
                                    <td> 11</td>
                                    <td></td>
                                    <td> ".number_format(abs($item['jubilacion']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$jubilacion ?? '';
        }
        if(!empty($item['obra_social'])){
            $obra_social = "<tr> 
                                    <td class='text-start'>2502 - Obra Social</td>                            
                                    <td>%  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 3</td>
                                    <td> 3</td>
                                    <td></td>
                                    <td> ".number_format(abs($item['obra_social']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$obra_social ?? '';
        }
        if(!empty($item['sindicato'])){
            $sindicato = "<tr> 
                                    <td class='text-start'>2503 - Cuota Sindical</td>                            
                                    <td>%&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1.5</td>
                                    <td> 1.5</td>
                                    <td></td>
                                    <td> ".number_format(abs($item['sindicato']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$sindicato ?? '';
        }
        if(!empty($item['os_nr'])){
            $os_nr = "<tr> 
                                    <td class='text-start'>O.S. NR</td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['os_nr']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['os_nr']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$os_nr ?? '';
        }
        if(!empty($item['sindicato_nr'])){
            $sindicato_nr = "<tr> 
                                    <td class='text-start'>Sindicato NR</td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['sindicato_nr']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['sindicato_nr']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$sindicato_nr ?? '';
        }
        if(!empty($item['embargos'])){
            $pension_s_rem_pago = "<tr> 
                                    <td class='text-start'>Embargos</td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['embargos']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['embargos']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$pension_s_rem_pago ?? '';
        }
        if(!empty($item['pension_s_rem_pago'])){
            $pension_s_rem_pago = "<tr> 
                                    <td class='text-start'>Pensión s/ Rem</td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['pension_s_rem_pago']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['pension_s_rem_pago']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$pension_s_rem_pago ?? '';
        }
        
        if(!empty($item['pension_s_no_rem_pago'])){
            $pension_s_no_rem_pago = "<tr> 
                                    <td class='text-start'>Pensión s/ No Rem</td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['pension_s_no_rem_pago']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['pension_s_no_rem_pago']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$pension_s_no_rem_pago ?? '';
        } 

        if(!empty($item['pensiones'])){
            $pensiones = "<tr> 
                                    <td class='text-start'>Pensiones</td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['pensiones']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['pensiones']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$pensiones ?? '';
        } 

        if(!empty($item['pensiones_pago'])){
            $pensiones_pago = "<tr> 
                                    <td class='text-start'>Pensiones</td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['pensiones_pago']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['pensiones_pago']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$pensiones_pago ?? '';
        } 

        if(!empty($item['club_azucarera_arg'])){
            $club_azucarera_arg = "<tr> 
                                    <td class='text-start'>Club Azucarera Argentina</td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['club_azucarera_arg']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['club_azucarera_arg']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$club_azucarera_arg ?? '';
        } 
        /*
        if(!empty($item['ajuste_1ra_qna'])){
            $ajuste_1ra_qna = "<tr> 
                                    <td class='text-start'>Ajuste Primera Quincena</td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['ajuste_1ra_qna']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['ajuste_1ra_qna']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$ajuste_1ra_qna ?? '';
        }
        if(!empty($item['ajuste_2da_qna'])){
            $ajuste_2da_qna = "<tr> 
                                    <td class='text-start'>Ajuste Segunda Quincena </td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['ajuste_2da_qna']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['ajuste_2da_qna']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$ajuste_2da_qna ?? '';
        }
        */  
        if(!empty($item['fotia_ayuda_medica'])){
            $fotia_ayuda_medica = "<tr> 
                                    <td class='text-start'>Ayuda Medica FOTIA </td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['fotia_ayuda_medica']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['fotia_ayuda_medica']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$fotia_ayuda_medica ?? '';
        }      
        if(!empty($item['feia_ayuda_medica'])){
            $feia_ayuda_medica = "<tr> 
                                    <td class='text-start'>Ayuda Medica FEIA</td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['feia_ayuda_medica']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['feia_ayuda_medica']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$feia_ayuda_medica ?? '';
        } 
        if(!empty($item['socorro_social_cuota'])){
            $socorro_social_cuota = "<tr> 
                                    <td class='text-start'>Cuota Socorro Social</td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['socorro_social_cuota']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['socorro_social_cuota']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$socorro_social_cuota ?? '';
        } 
        if(!empty($item['socorro_social_ap'])){
            $socorro_social_ap = "<tr> 
                                    <td class='text-start'>Socorro Social Ap</td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['socorro_social_ap']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['socorro_social_ap']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$socorro_social_ap ?? '';
        } 
        if(!empty($item['colecta'])){
            $colecta = "<tr> 
                                    <td class='text-start'>Colecta</td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['colecta']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['colecta']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$colecta ?? '';
        } 
        
        if(!empty($item['colecta_ii'])){
            $colecta_ii = "<tr> 
                                    <td class='text-start'>Colecta 2</td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['colecta_ii']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['colecta_ii']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$colecta_ii ?? '';
        } 
        if(!empty($item['colecta_iii'])){
            $colecta_iii = "<tr> 
                                    <td class='text-start'>Colecta 3</td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['colecta_iii']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['colecta_iii']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$colecta_iii ?? '';
        } 
        if(!empty($item['colecta_iv'])){
            $colecta_iv = "<tr> 
                                    <td class='text-start'>colecta 4</td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['colecta_iv']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['colecta_iv']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$colecta_iv ?? '';
        } 
        if(!empty($item['colecta_v'])){
            $colecta_v = "<tr> 
                                    <td class='text-start'>Colecta 5</td>                            
                                    <td></td>
                                    <td> ".number_format(abs($item['colecta_v']) ?? 0, 2, ',', '.')." </td>                                                            
                                    <td></td>
                                    <td> ".number_format(abs($item['colecta_v']) ?? 0, 2, ',', '.')." </td>                                                            
                                </tr>" ?? "";
            $detalle_haberes .=$colecta_v ?? '';
        } 
        //1065
        if($item['faltas_c_aviso_pago'] ?? 0 >0){
            $faltas_c_aviso = "<tr> 
                        <td class='text-start'>1065 - Faltas con Aviso  </td>                            
                        <td>Ds. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['faltas_c_aviso']."</td>
                        <td> </td>
                        <td> </td>
                        <td> ".number_format($item['faltas_c_aviso_pago'] ?? 0, 2, ',', '.')." </td>                                                
                    </tr>" ?? "";
            $detalle_haberes .=$faltas_c_aviso ?? '';
        }
        //1066
        if($item['faltas_s_aviso_pago'] ?? 0 >0){
            $faltas_s_aviso = "<tr> 
                        <td class='text-start'>1066 - Faltas sin Aviso  </td>                            
                        <td>Ds. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['faltas_s_aviso']."</td>
                        <td> </td>
                        <td> </td>
                        <td> ".number_format($item['faltas_s_aviso_pago'] ?? 0, 2, ',', '.')." </td>                                                
                    </tr>" ?? "";
            $detalle_haberes .=$faltas_s_aviso ?? '';
        }
        //1073
        if($item['llegada_tarde_pago'] ?? 0 >0){
            $llegada_tarde = "<tr> 
                        <td class='text-start'>1073 - Llegada tarde  </td>                            
                        <td>Ds. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['llegada_tarde']."</td>
                        <td> </td>
                        <td> </td>
                        <td> ".number_format($item['llegada_tarde_pago'] ?? 0, 2, ',', '.')." </td>                                                
                    </tr>" ?? "";
            $detalle_haberes .=$llegada_tarde ?? '';
        }
        //1304
        if($item['dias_de_susp_pago'] ?? 0 >0){
            $dias_de_susp = "<tr> 
                        <td class='text-start'>1304 - Dias de Suspencion  </td>                            
                        <td>Ds. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$item['dias_de_susp']."</td>
                        <td> </td>
                        <td> </td>
                        <td> ".number_format($item['dias_de_susp_pago'] ?? 0, 2, ',', '.')." </td>                                                
                    </tr>" ?? "";
            $detalle_haberes .=$dias_de_susp ?? '';
        }
        if($item['anticipos'] ?? 0 >0){
            $anticipos = "<tr> 
                        <td class='text-start'>Anticipo  </td>                            
                        <td></td>
                        <td> </td>
                        <td> </td>
                        <td> ".number_format($item['anticipos'] ?? 0, 2, ',', '.')." </td>                                                
                    </tr>" ?? "";
            $detalle_haberes .=$anticipos ?? '';
        }
    }
    
    /*VISUALIZACION*/        
        echo $detalle_haberes ?? '';
        echo "<p></p>";
        
    
?>
