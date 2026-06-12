<?php

 function get_liq_manuales(): ?array { // <-- Cambiado a devolver ?array (puede ser null)
    include 'conexiones/conexion.php';
    include_once 'funciones/funciones.php'; // Para cualquier función auxiliar

    $mysqli = conexion_db();
    if ($mysqli->connect_errno) {
        error_log("Error al conectar con la base de datos: " . $mysqli->connect_error);
        return null; // Retorna null si la conexión falla
    }
    $sql = "SELECT 1=0";

    if(!empty($_POST)){
        $tipo_liquidacion = $_POST['tipo_liq'] ?? '';
        if($tipo_liquidacion=='Q_FAB'){
            $sql="SELECT leg, empleados.cuit_cuil 'cuil',calle , empleados.fechaingreso, empleados.fechanacimiento , liquidaciones_manuales.sector, COALESCE(apellidoynombre,empleado) 'empleado',os_fotia, ospat, edad_mas_60, pension_s_rem, pension_s_no_rem, cat_i, cat_ii,
            dif_de_cat, anos_reconocidos 'antiguedad', empleados.categoria, empleados.tipocontratacion 'modo_contratacion',cargos_y_estado_civil.cargo,cargos_y_estado_civil.estado_civil, escalafon, basico_rem 'jornal',
            basico_no_rem, titulo, jornales, jornales_a_liq, dias_de_enfermedad, feriado, feriado_trabajado, art, retiro_anticipado, hs_simples, faltas_c_aviso, faltas_s_aviso,
            llegada_tarde, dias_de_susp, lic_c_goce, lic_s_goce, altura, destajo, donacion_de_sangre, hs_50, hs_100,
            dest_p_bls_67134, dest_p_bls_52674, dest_p_bls_16226, dest_p_bls_25053, dest_p_bls_31374, presentismo,
            premio_asist,premio_asistencia, reint_de_mec, total_jornales, sueldo_basico, dias_de_enfermedad_pago, feriado_pago, feriado_trabajado_pago,
            art_pago, retiro_anticipado_pago, hs_simples_pago, faltas_c_aviso_pago, faltas_s_aviso_pago, llegada_tarde_pago,
            dias_de_susp_pago, lic_c_goce_pago, lic_s_goce_pago, destajo_pago, altura_pago, donacion_de_sangre_pago, escalafon_pago,
            titulo_pago, presentismo_pago, dedicacion_clc, dif_de_cat_pago, hs_50_pago, hs_100_pago, dest_p_bls_67134_pago, 
            dest_p_bls_52674_pago, dest_p_bls_16226_pago, dest_p_bls_25053_pago, dest_p_bls_31374_pago, importe_concepto_1200,
            ajuste_1ra_qna, ajuste_2da_qna, nr_mayor_60, bruto_total, jubilacion, ley_19032, obra_social, sindicato, adic_de_os,
            club_azucarera_arg, COALESCE(no_remunerativo,0) no_remunerativo, ajuste_1ra_qna_nr, ajuste_2da_qna_nr, os_nr, sindicato_nr, reintegro_medicamentos,
            tickey_canasta, fotia_ayuda_medica, feia_ayuda_medica, socorro_social_cuota, socorro_social_ap, dif_vsx, embargos, 
            pensiones, club_azucarera, fotia_ayuda_econ, colecta, colecta_ii, colecta_iii, anticipos, pension_s_rem_pago, pension_s_no_rem_pago,
            neto,ROUND(neto) neto_redondeado, neto_rounded, sistema, diferencia,periodo_liquidacion,quincena_liquidacion,fecha_ultimo_aporte,banco_aporte,periodo_aporte,fecha_pago,lugar_pago,            
            CONCAT(leg,'-',(CASE 
                                        WHEN periodo_liquidacion = 'Junio 2025' THEN '202506'
                                        WHEN periodo_liquidacion = 'Julio 2025' THEN '202507'
                                        WHEN periodo_liquidacion = 'Agosto 2025' THEN '202508'
                                        WHEN periodo_liquidacion = 'Septiembre 2025' THEN '202509'
                                        WHEN periodo_liquidacion = 'Octubre 2025' THEN '202510'
                                        WHEN periodo_liquidacion = 'Noviembre 2025' THEN '202511'
                                        WHEN periodo_liquidacion = 'Diciembre 2025' THEN '202512'
                                        ELSE '' END),'-','CAT','-','MENSUAL','-',quincena_liquidacion,' ',periodo_liquidacion) as nombre_recibo
            FROM liquidaciones_manuales
            LEFT JOIN empleados ON liquidaciones_manuales.leg= empleados.legajo
            LEFT JOIN cargos_y_estado_civil ON TRIM(empleados.legajo) = TRIM(cargos_y_estado_civil.legajo)
            -- where liquidaciones_manuales.neto > 0  AND leg='9838' AND liquidaciones_manuales.quincena_liquidacion = 'Segunda Quincena' REVISAR PORQUE NO SALE
            where   liquidaciones_manuales.neto_rounded > 0  and liquidaciones_manuales.sector='FAB' AND periodo_liquidacion = 'Agosto 2025' AND quincena_liquidacion = 'Primera Quincena'
                    -- leg = '9838'      
            -- where liquidaciones_manuales.neto_rounded > 0 and liquidaciones_manuales.sector='CAMPO' AND liquidaciones_manuales.quincena_liquidacion = 'Segunda Quincena' 
            ORDER BY empleados.apellidoynombre ASC;"; 
        }elseif($tipo_liquidacion=='Q_CAMPO'){
            $sql="  SELECT leg, empleados.cuit_cuil 'cuil',calle , empleados.fechaingreso, empleados.fechanacimiento , liquidaciones_manuales.sector, COALESCE(apellidoynombre,empleado) 'empleado',os_fotia, ospat, edad_mas_60, pension_s_rem, pension_s_no_rem, cat_i, cat_ii,
                    dif_de_cat, anos_reconocidos 'antiguedad', empleados.categoria, empleados.tipocontratacion 'modo_contratacion',cargos_y_estado_civil.cargo,cargos_y_estado_civil.estado_civil, escalafon, basico_rem 'jornal',
                    basico_no_rem, titulo, jornales, jornales_a_liq, dias_de_enfermedad, feriado, feriado_trabajado, art, retiro_anticipado, hs_simples, faltas_c_aviso, faltas_s_aviso,
                    llegada_tarde, dias_de_susp, lic_c_goce, lic_s_goce, altura, destajo, donacion_de_sangre, hs_50, hs_100,
                    dest_p_bls_67134, dest_p_bls_52674, dest_p_bls_16226, dest_p_bls_25053, dest_p_bls_31374, presentismo,
                    premio_asist,premio_asistencia, reint_de_mec, total_jornales, sueldo_basico, dias_de_enfermedad_pago, feriado_pago, feriado_trabajado_pago,
                    art_pago, retiro_anticipado_pago, hs_simples_pago, faltas_c_aviso_pago, faltas_s_aviso_pago, llegada_tarde_pago,
                    dias_de_susp_pago, lic_c_goce_pago, lic_s_goce_pago, destajo_pago, altura_pago, donacion_de_sangre_pago, escalafon_pago,
                    titulo_pago, presentismo_pago, dedicacion_clc, dif_de_cat_pago, hs_50_pago, hs_100_pago, dest_p_bls_67134_pago, 
                    dest_p_bls_52674_pago, dest_p_bls_16226_pago, dest_p_bls_25053_pago, dest_p_bls_31374_pago, importe_concepto_1200,
                    ajuste_1ra_qna, ajuste_2da_qna, nr_mayor_60, bruto_total, jubilacion, ley_19032, obra_social, sindicato, adic_de_os,
                    club_azucarera_arg, COALESCE(no_remunerativo,0) no_remunerativo, ajuste_1ra_qna_nr, ajuste_2da_qna_nr, os_nr, sindicato_nr, reintegro_medicamentos,
                    tickey_canasta, fotia_ayuda_medica, feia_ayuda_medica, socorro_social_cuota, socorro_social_ap, dif_vsx, embargos, 
                    pensiones, club_azucarera, fotia_ayuda_econ, colecta, colecta_ii, colecta_iii, anticipos, pension_s_rem_pago, pension_s_no_rem_pago,
                    neto, neto_rounded, sistema, diferencia,periodo_liquidacion,quincena_liquidacion,fecha_ultimo_aporte,banco_aporte,periodo_aporte,fecha_pago,lugar_pago,
                    CONCAT(leg,'-',(CASE 
                                        WHEN periodo_liquidacion = 'Junio 2025' THEN '202506'
                                        WHEN periodo_liquidacion = 'Julio 2025' THEN '202507'
                                        WHEN periodo_liquidacion = 'Agosto 2025' THEN '202508'
                                        WHEN periodo_liquidacion = 'Septiembre 2025' THEN '202509'
                                        WHEN periodo_liquidacion = 'Octubre 2025' THEN '202510'
                                        WHEN periodo_liquidacion = 'Noviembre 2025' THEN '202511'
                                        WHEN periodo_liquidacion = 'Diciembre 2025' THEN '202512'
                                        ELSE '' END),'-','CAT','-','MENSUAL','-',quincena_liquidacion,' ',periodo_liquidacion) as nombre_recibo
                    FROM liquidaciones_manuales
                    LEFT JOIN empleados ON liquidaciones_manuales.leg= empleados.legajo
                    LEFT JOIN cargos_y_estado_civil ON TRIM(empleados.legajo) = TRIM(cargos_y_estado_civil.legajo)
                    -- where liquidaciones_manuales.neto > 0  AND leg='9838' AND liquidaciones_manuales.quincena_liquidacion = 'Segunda Quincena' REVISAR PORQUE NO SALE
                    where liquidaciones_manuales.neto_rounded > 0  and liquidaciones_manuales.sector='CAMPO' AND fecha_pago = '2025-08-12' AND periodo_liquidacion = 'Julio 2025'
                    -- where liquidaciones_manuales.neto_rounded > 0 and liquidaciones_manuales.sector='CAMPO' AND liquidaciones_manuales.quincena_liquidacion = 'Segunda Quincena' 
                    ORDER BY empleados.apellidoynombre ASC;"; 
        }elseif($tipo_liquidacion=='MENSUAL_CONVENIO'){
            $sql="  SELECT leg, empleados.cuit_cuil 'cuil',calle , empleados.fechaingreso, empleados.fechanacimiento , COALESCE(apellidoynombre,empleado) 'empleado',
                    anos_reconocidos 'antiguedad', empleados.categoria, empleados.tipocontratacion 'modo_contratacion',cargos_y_estado_civil.cargo,cargos_y_estado_civil.estado_civil, ospat, 
                    edad_mas_60, pension_s_rem, cat_i, cat_ii, dif_cat_, dif_de_cat, anos_reconocidos, escalafon, basico_rem,
                    basico_no_rem, titulo, jornales, jornales_a_liq, dias_de_enfermedad, feriado, feriado_trabajado, art, retiro_anticipado, hs_simples, faltas_c_aviso, llegada_tarde, 
                    dias_de_susp, lic_c_goce, lic_s_goce, altura, destajo, donacion_de_sangre, hs_50, hs_100, dest_p_bls_67134, dest_p_bls_52674, dest_p_bls_16226, dest_p_bls_25053, dest_p_bls_31374,
                    dedicacion, presentismo, premio_asist, reint_de_mec, total_jornales, sueldo_basico, dias_de_enfermedad_pago, feriado_pago, feriado_trabajado_pago, art_pago, retiro_anticipado_pago,
                    hs_simples_pago, faltas_c_aviso_pago, llegada_tarde_pago, dias_de_susp_pago, lic_c_goce_pago, lic_s_goce_pago, destajo_pago, altura_pago, donacion_de_sangre_pago, escalafon_pago,
                    titulo_pago, presentismo_pago, dedicacion_clc, dif_de_cat_pago, hs_50_pago, hs_100_pago, dest_p_bls_67134_pago, dest_p_bls_52674_pago, dest_p_bls_16226_pago, dest_p_bls_25053_pago,
                    dest_p_bls_31374_pago, adicional_movil, importe_concepto_1200, nr_mayor_60, bruto_total, jubilacion, ley_19032, obra_social, sindicato, adic_de_os, club_azucarera_arg, 
                    no_remunerativo, material_descartable, os_nr, sindicato_nr, reintegro_medicamentos, tickey_canasta, ayuda_medica, socorro_social_cuota, socorro_social_ap, dif_vsx, embargos, 
                    pensiones, colecta, colecta_ii, colecta_iii, feia_ayuda, anticipos, pensiones_pago, neto, neto_rounded, sistema, diferencia, periodo_liquidacion, quincena_liquidacion,
                    fecha_ultimo_aporte, banco_aporte, periodo_aporte, fecha_pago, lugar_pago,
                    CONCAT(leg,'-',(CASE 
                                        WHEN periodo_liquidacion = 'Junio 2025' THEN '202506'
                                        WHEN periodo_liquidacion = 'Julio 2025' THEN '202507'
                                        WHEN periodo_liquidacion = 'Agosto 2025' THEN '202508'
                                        WHEN periodo_liquidacion = 'Septiembre 2025' THEN '202509'
                                        WHEN periodo_liquidacion = 'Octubre 2025' THEN '202510'
                                        WHEN periodo_liquidacion = 'Noviembre 2025' THEN '202511'
                                        WHEN periodo_liquidacion = 'Diciembre 2025' THEN '202512'
                                        ELSE '' END),'-','CAT','-','MENSUAL','-',quincena_liquidacion,' ',periodo_liquidacion) as nombre_recibo
                    FROM liquidaciones_mensuales_convenio
                    LEFT JOIN empleados ON leg= empleados.legajo
                    LEFT JOIN cargos_y_estado_civil ON TRIM(empleados.legajo) = TRIM(cargos_y_estado_civil.legajo)
                    where neto_rounded > 0 AND periodo_liquidacion <> 'Junio 2025' -- and leg'9846' 
                    ORDER BY empleados.apellidoynombre ASC;"; 
        }elseif($tipo_liquidacion=='MENSUAL_FUERA_CONVENIO'){
            $sql="SELECT leg, calle , empleados.fechaingreso, empleados.fechanacimiento , COALESCE(apellidoynombre,empleado) 'empleado',
                        anos_reconocidos 'antiguedad', empleados.categoria, empleados.tipocontratacion 'modo_contratacion',cargos_y_estado_civil.cargo,cargos_y_estado_civil.estado_civil,
                        tipo, cuil, ospat, edad_mas_60, pension_s_rem, pension_s_no_rem, cat_i, cat_ii, dif_cat_, dif_de_cat, anos_reconocidos, escalafon, basico_rem, basico_no_rem,
                        g50, jornales, jornales_a_liq, dias_de_enfermedad, feriado, feriado_trabajado, art, retiro_anticipado, hs_simples, faltas_c_aviso, llegada_tarde, dias_de_susp,
                        lic_c_goce, lic_s_goce, altura, destajo, donacion_de_sangre, hs_50, hs_100, dest_p_bls_67134, dest_p_bls_52674, dest_p_bls_16226, dest_p_bls_25053, dedicacion, presentismo, 
                        premio_asist, reint_de_mec, total_jornales, sueldo_basico, dias_de_enfermedad_pago, feriado_pago, feriado_trabajado_pago, art_pago, retiro_anticipado_pago, hs_simples_pago,
                        faltas_c_aviso_pago, llegada_tarde_pago, dias_de_susp_pago, lic_c_goce_pago, lic_s_goce_pago, destajo_pago, altura_pago, donacion_de_sangre_pago, escalafon_pago, titulo_pago,
                        presentismo_pago, dedicacion_clc, dif_de_cat_pago, hs_50_pago, hs_100_pago, dest_p_bls_67134_pago, dest_p_bls_52674_pago, dest_p_bls_16226_pago, dest_p_bls_25053_pago, 
                        importe_concepto_1200, nr_mayor_60, bruto_total, jubilacion, ley_19032, obra_social, sindicato, adic_de_os, club_azucarera_arg, no_remunerativo, material_descartable,
                        os_nr, sindicato_nr, reintegro_medicamentos, tickey_canasta, ayuda_medica, socorro_social_cuota, socorro_social_ap, dif_vsx, embargos, pensiones_base, colecta, colecta_ii,
                        colecta_iii, feia_ayuda, anticipos, pensiones, neto, neto_rounded, sistema, diferencia, periodo_liquidacion, quincena_liquidacion, fecha_ultimo_aporte, banco_aporte,
                        periodo_aporte, fecha_pago, lugar_pago,
                        CONCAT(leg,'-',(CASE 
                                        WHEN periodo_liquidacion = 'Junio 2025' THEN '202506'
                                        WHEN periodo_liquidacion = 'Julio 2025' THEN '202507'
                                        WHEN periodo_liquidacion = 'Agosto 2025' THEN '202508'
                                        WHEN periodo_liquidacion = 'Septiembre 2025' THEN '202509'
                                        WHEN periodo_liquidacion = 'Octubre 2025' THEN '202510'
                                        WHEN periodo_liquidacion = 'Noviembre 2025' THEN '202511'
                                        WHEN periodo_liquidacion = 'Diciembre 2025' THEN '202512'
                                        ELSE '' END),'-','CAT','-','MENSUAL','-',quincena_liquidacion,' ',periodo_liquidacion) as nombre_recibo
                        FROM liquidaciones_mensuales_fuera_de_convenio
                        LEFT JOIN empleados ON leg= empleados.legajo
                        LEFT JOIN cargos_y_estado_civil ON TRIM(empleados.legajo) = TRIM(cargos_y_estado_civil.legajo)
                        where  neto_rounded > 0 AND leg = '940' -- AND periodo_liquidacion <> 'Junio 2025'  AND leg<>'9625' AND leg<>'9006' AND leg<>'992' AND leg<>'993' 
                        ORDER BY empleados.apellidoynombre ASC;"; 
        }elseif($tipo_liquidacion=='MENSUAL_BIO'){
            $sql="SELECT leg, calle , empleados.fechaingreso, empleados.fechanacimiento , COALESCE(apellidoynombre,empleado) 'empleado',
                            anos_reconocidos 'antiguedad', empleados.categoria, empleados.tipocontratacion 'modo_contratacion',cargos_y_estado_civil.cargo,cargos_y_estado_civil.estado_civil,
                            tipo, cuil, ospat, edad_mas_60, pension_s_rem, pension_s_no_rem, cat_i, cat_ii, dif_cat_, dif_de_cat, anos_reconocidos, escalafon, basico_rem, basico_no_rem,
                            g50, jornales, jornales_a_liq, dias_de_enfermedad, feriado, feriado_trabajado, art, retiro_anticipado, hs_simples, faltas_c_aviso, llegada_tarde, dias_de_susp,
                            lic_c_goce, lic_s_goce, altura, destajo, donacion_de_sangre, hs_50, hs_100, dest_p_bls_67134, dest_p_bls_52674, dest_p_bls_16226, dest_p_bls_25053, dedicacion, presentismo, 
                            premio_asist, reint_de_mec, total_jornales, sueldo_basico, dias_de_enfermedad_pago, feriado_pago, feriado_trabajado_pago, art_pago, retiro_anticipado_pago, hs_simples_pago,
                            faltas_c_aviso_pago, llegada_tarde_pago, dias_de_susp_pago, lic_c_goce_pago, lic_s_goce_pago, destajo_pago, altura_pago, donacion_de_sangre_pago, escalafon_pago, titulo_pago,
                            presentismo_pago, dedicacion_clc, dif_de_cat_pago, hs_50_pago, hs_100_pago, dest_p_bls_67134_pago, dest_p_bls_52674_pago, dest_p_bls_16226_pago, dest_p_bls_25053_pago, 
                            importe_concepto_1200, nr_mayor_60, bruto_total, jubilacion, ley_19032, obra_social, sindicato, adic_de_os, club_azucarera_arg, no_remunerativo, material_descartable,
                            os_nr, sindicato_nr, reintegro_medicamentos, tickey_canasta, ayuda_medica, socorro_social_cuota, socorro_social_ap, dif_vsx, embargos, pensiones_base, colecta, colecta_ii,
                            colecta_iii, feia_ayuda, anticipos, pensiones, neto, neto_rounded, sistema, diferencia, periodo_liquidacion, quincena_liquidacion, fecha_ultimo_aporte, banco_aporte,
                            periodo_aporte, fecha_pago, lugar_pago,
                            CONCAT(leg,'-',(CASE 
                                        WHEN periodo_liquidacion = 'Junio 2025' THEN '202506'
                                        WHEN periodo_liquidacion = 'Julio 2025' THEN '202507'
                                        WHEN periodo_liquidacion = 'Agosto 2025' THEN '202508'
                                        WHEN periodo_liquidacion = 'Septiembre 2025' THEN '202509'
                                        WHEN periodo_liquidacion = 'Octubre 2025' THEN '202510'
                                        WHEN periodo_liquidacion = 'Noviembre 2025' THEN '202511'
                                        WHEN periodo_liquidacion = 'Diciembre 2025' THEN '202512'
                                        ELSE '' END),'-','CAT','-','MENSUAL','-',quincena_liquidacion,' ',periodo_liquidacion) as nombre_recibo
                        FROM liquidaciones_mensuales_fuera_de_convenio
                        LEFT JOIN empleados ON leg= empleados.legajo
                        LEFT JOIN cargos_y_estado_civil ON TRIM(empleados.legajo) = TRIM(cargos_y_estado_civil.legajo)
                        where neto_rounded > 0  AND LEG='992' -- (LEG='858' OR LEG='1004' OR LEG='990' OR LEG='970' OR LEG='9053') -- AND periodo_liquidacion = 'Junio 2025'
                        ORDER BY empleados.apellidoynombre ASC, periodo_liquidacion DESC;"; 
        }

    }
   
   
    $stmt = $mysqli->query($sql);
   

    if (!$stmt) {
        error_log("Error al preparar la consulta : " . $mysqli->error);
        $mysqli->close(); // Cerrar conexión en caso de error
        return null; // Devuelve null en caso de error
    }

    //$row = $result->fetch_assoc(); // <-- Aquí obtienes UNA SOLA FILA

    $liq_rows = [];
    while ($row = $stmt->fetch_assoc()) {
        $liq_rows[] = $row;
    }
    
    $stmt->free();    
    $mysqli->close(); // Cerrar la conexión aquí si la abres dentro de la función

    return $liq_rows; // <-- Devuelve el array asociativo directamente (o null si no hay resultados)
}





?>