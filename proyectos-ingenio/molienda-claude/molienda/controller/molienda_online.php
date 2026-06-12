<?php

    include_once 'conexiones/conexion.php';
    include_once 'funciones/funciones.php';

    $filtro='';

    $mysqli=conexion_db();
    

    function EjecutarConsulta($consulta, $fecha,$hora){
        $queries = [
            'consumo_x_hora' => "   SELECT m.fechaindustrial, m.hora, m.gas, m.kilos, m.humedad, m.bolsas,
                                        ROUND(AVG(b.polmanual), 3) AS pol_bagazo,
                                        ROUND(AVG(c.polautomatico), 3) AS pol_cachaza
                                    FROM consumos_x_hora m
                                    LEFT JOIN calidadBagazo_xHora b ON b.fechaindustrial = m.fechaindustrial AND b.hora = CAST(m.hora AS TIME)
                                    LEFT JOIN calidadCachaza_xHora c ON c.fechaindustrial = m.fechaindustrial AND c.hora = CAST(m.hora AS TIME)
                                    WHERE m.fechaindustrial = '".$fecha."'
                                    GROUP BY m.fechaindustrial, m.hora, m.gas, m.kilos, m.humedad, m.bolsas
                                    ORDER BY m.hora ASC",

            'paradas' => "  SELECT fechaindustrial, desde AS DESDE, hasta AS HASTA, t_neto AS T_Neto, t_neto_minutos, origen, maquina, motivo
                            FROM paradas_fabrica
                            WHERE fechaindustrial = '".$fecha."'
                            ORDER BY fechaindustrial DESC, desde ASC",
            
            'ultima_pesada' => "SELECT kilos AS ultima_pesada, fechaindustrial, hora
                                FROM consumos_x_hora
                                WHERE fechaindustrial = '".$fecha."'
                                ORDER BY fechaindustrial DESC, hora DESC LIMIT 1",

            'pol_promedio' => " SELECT
                                    base.fechaindustrial,
                                    ROUND(AVG(b.polmanual), 3) AS pol_prom_bagazo,
                                    ROUND(AVG(c.polautomatico), 3) AS pol_prom_cachaza
                                FROM
                                    (SELECT fechaindustrial FROM calidadBagazo_xHora WHERE fechaindustrial = '".$fecha."' GROUP BY fechaindustrial) base
                                    LEFT JOIN calidadBagazo_xHora b ON b.fechaindustrial = base.fechaindustrial
                                    LEFT JOIN calidadCachaza_xHora c ON c.fechaindustrial = base.fechaindustrial
                                GROUP BY base.fechaindustrial;",

            'avg_cinta_larga' => "  SELECT fechaindustrial AS fecha, ROUND(AVG(color),2) AS color, ROUND(AVG(turbidez),2) AS turbidez,
                                        ROUND(AVG(humedad),2) AS humedad, ROUND(AVG(cenizas),2) AS cenizas,
                                        ROUND(AVG(sediment_test),2) AS sedimentos, ROUND(AVG(so2),2) AS so2
                                    FROM calidadAzucar_xHora
                                    WHERE fechaindustrial = '".$fecha."' AND hora = '".$hora."' AND codigoproceso = 'Cinta Larga'
                                    GROUP BY fechaindustrial;",

            'avg_cinta_corta' => "  SELECT fechaindustrial AS fecha, ROUND(AVG(color),2) AS color, ROUND(AVG(turbidez),2) AS turbidez,
                                        ROUND(AVG(humedad),2) AS humedad, ROUND(AVG(cenizas),2) AS cenizas,
                                        ROUND(AVG(sediment_test),2) AS sedimentos, ROUND(AVG(so2),2) AS so2
                                    FROM calidadAzucar_xHora
                                    WHERE fechaindustrial = '".$fecha."' AND hora = '".$hora."' AND codigoproceso = 'Cinta Corta'
                                    GROUP BY fechaindustrial;",

            'avg_embolsado' => "    SELECT fechaindustrial AS fecha, ROUND(AVG(color),2) AS color, ROUND(AVG(turbidez),2) AS turbidez,
                                        ROUND(AVG(humedad),2) AS humedad, ROUND(AVG(cenizas),2) AS cenizas,
                                        ROUND(AVG(sediment_test),2) AS sedimentos, ROUND(AVG(so2),2) AS so2
                                    FROM calidadAzucar_xHora
                                    WHERE fechaindustrial = '".$fecha."' AND hora = '".$hora."' AND codigoproceso = 'Embolsado'
                                    GROUP BY fechaindustrial;",

            'avg_crudo' => "        SELECT fechaindustrial AS fecha, ROUND(AVG(color),2) AS color, ROUND(AVG(turbidez),2) AS turbidez,
                                        ROUND(AVG(humedad),2) AS humedad, ROUND(AVG(cenizas),2) AS cenizas,
                                        ROUND(AVG(sediment_test),2) AS sedimentos, ROUND(AVG(so2),2) AS so2
                                    FROM calidadAzucar_xHora
                                    WHERE fechaindustrial = '".$fecha."' AND hora = '".$hora."' AND codigoproceso = 'Crudo'
                                    GROUP BY fechaindustrial;",

            'estado_silos'=> "      SELECT fecha, hora, nombre, MAX(vacio) AS vacio, MAX(calidad) AS calidad
                                    FROM estado_silos
                                    WHERE fecha = '".$fecha."' AND hora = '".$hora."'
                                    GROUP BY nombre, fecha, hora
                                    ORDER BY fecha DESC, hora DESC LIMIT 1;",

            'avg_cinta_larga_dia' => "  SELECT fechaindustrial AS fecha, ROUND(AVG(color),2) AS color, ROUND(AVG(turbidez),2) AS turbidez,
                                        ROUND(AVG(humedad),2) AS humedad, ROUND(AVG(cenizas),2) AS cenizas,
                                        ROUND(AVG(sediment_test),2) AS sedimentos, ROUND(AVG(so2),2) AS so2
                                    FROM calidadAzucar_xHora
                                    WHERE fechaindustrial = '".$fecha."' AND codigoproceso = 'Cinta Larga'
                                    GROUP BY fechaindustrial;",

            'avg_cinta_corta_dia' => "  SELECT fechaindustrial AS fecha, ROUND(AVG(color),2) AS color, ROUND(AVG(turbidez),2) AS turbidez,
                                        ROUND(AVG(humedad),2) AS humedad, ROUND(AVG(cenizas),2) AS cenizas,
                                        ROUND(AVG(sediment_test),2) AS sedimentos, ROUND(AVG(so2),2) AS so2
                                    FROM calidadAzucar_xHora
                                    WHERE fechaindustrial = '".$fecha."' AND codigoproceso = 'Cinta Corta'
                                    GROUP BY fechaindustrial;",

            'avg_embolsado_dia' => "    SELECT fechaindustrial AS fecha, ROUND(AVG(color),2) AS color, ROUND(AVG(turbidez),2) AS turbidez,
                                        ROUND(AVG(humedad),2) AS humedad, ROUND(AVG(cenizas),2) AS cenizas,
                                        ROUND(AVG(sediment_test),2) AS sedimentos, ROUND(AVG(so2),2) AS so2
                                    FROM calidadAzucar_xHora
                                    WHERE fechaindustrial = '".$fecha."' AND codigoproceso = 'Embolsado'
                                    GROUP BY fechaindustrial;",

            'avg_crudo_dia' => "    SELECT fechaindustrial AS fecha, ROUND(AVG(color),2) AS color, ROUND(AVG(turbidez),2) AS turbidez,
                                        ROUND(AVG(humedad),2) AS humedad, ROUND(AVG(cenizas),2) AS cenizas,
                                        ROUND(AVG(sediment_test),2) AS sedimentos, ROUND(AVG(so2),2) AS so2
                                    FROM calidadAzucar_xHora
                                    WHERE fechaindustrial = '".$fecha."' AND codigoproceso = 'Crudo'
                                    GROUP BY fechaindustrial;",

            'estado_silos_dia'=> "  SELECT fecha, hora, nombre, MAX(vacio) AS vacio, MAX(calidad) AS calidad
                                    FROM estado_silos
                                    WHERE fecha = '".$fecha."'
                                    GROUP BY nombre, fecha, hora
                                    ORDER BY fecha DESC, hora DESC LIMIT 1;",

            'resumen_fabrica_promedios'=> "
                                    SELECT MAX(fechaindustrial) AS fechaindustrial, codigoproceso,
                                        ROUND(AVG(brix_manual),2) AS brix, ROUND(AVG(pol_manual),2) AS pol,
                                        ROUND(AVG(pureza),2) AS pureza, ROUND(AVG(ph_manual),2) AS ph
                                    FROM calidadJugo_xHora
                                    WHERE fechaindustrial = (
                                        SELECT MAX(fechaindustrial) FROM calidadJugo_xHora
                                        WHERE (brix_manual > 0 OR pol_manual > 0 OR pureza > 0 OR ph_manual > 0)
                                    )
                                    AND (brix_manual > 0 OR pol_manual > 0 OR pureza > 0 OR ph_manual > 0)
                                    GROUP BY codigoproceso;",

            'resumen_fabrica_totales'=> "
                                    SELECT MAX(fechaindustrial) AS fechaindustrial, codigoproceso,
                                        ROUND(SUM(kilos),2) AS total
                                    FROM calidadJugo_xHora
                                    WHERE fechaindustrial = (
                                        SELECT MAX(fechaindustrial) FROM calidadJugo_xHora
                                        WHERE (brix_manual > 0 OR pol_manual > 0 OR pureza > 0 OR ph_manual > 0)
                                    )
                                    AND kilos > 0
                                    GROUP BY codigoproceso;",

            'sulfitado'=> "         SELECT
                                        fechaindustrial,
                                        CASE
                                            WHEN codigoproceso = 'Encalado' THEN 'JUGO ENCALADO PH'
                                            WHEN codigoproceso = 'Sulfitado' THEN 'PH'
                                            ELSE codigoproceso
                                        END AS codigoproceso,
                                        ROUND(AVG(phmanual), 2) AS ph,
                                        ROUND(AVG(PPMS02manual), 2) AS PPMS02manual
                                    FROM calidadSulfitado_xHora
                                    WHERE fechaindustrial = (
                                        SELECT MAX(fechaindustrial) FROM calidadSulfitado_xHora WHERE phmanual > 0
                                    )
                                    GROUP BY fechaindustrial,
                                        CASE
                                            WHEN codigoproceso = 'Encalado' THEN 'JUGO ENCALADO PH'
                                            WHEN codigoproceso = 'Sulfitado' THEN 'PH'
                                            ELSE codigoproceso
                                        END;"
        ];
        
        return $queries[$consulta] ?? '';
    }    
function ObtieneDatosSQL($sql): array {
        include_once 'conexiones/conexion.php';
        include_once 'funciones/funciones.php';

        $mysqli = conexion_db();
        if ($mysqli->connect_errno) {
            error_log("Error al conectar con la base de datos: " . $mysqli->connect_error);
            return [];
        }

        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            error_log("Error al preparar la consulta: " . $mysqli->error . " | SQL: " . $sql);
            $mysqli->close();
            return [];
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        $result->free();
        $stmt->close();
        $mysqli->close();

        return $rows;
}

function obtiene_molienda_acumulada(): ?array {
    include_once 'conexiones/conexion.php';
    include_once 'funciones/funciones.php'; 

    $mysqli = conexion_db();
    if ($mysqli->connect_errno) {
        error_log("Error al conectar con la base de datos: " . $mysqli->connect_error);
        return null;
    }
    
    
    // Acumula solo días industriales ya cerrados (excluye el día en curso)
    $sql = "SELECT SUM(cania_bruta) AS molienda_acumulada, MAX(fecha_insert) AS ultima_actualizacion
            FROM datos_Cania
            WHERE YEAR(fecha_pesada) = (
                SELECT YEAR(MAX(fecha_pesada)) FROM datos_Cania
            )
            AND fechaindustrial IS NOT NULL
            AND fechaindustrial < CURDATE();";            
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        error_log("Error al preparar la consulta: " . $mysqli->error);
        $mysqli->close();
        return null;
    }

    $stmt->execute();
    $result = $stmt->get_result();
    
    $row = $result->fetch_assoc(); // una sola fila
    // Depuración fuerte:
    //var_dump($row);

    if ($row && !empty($row['molienda_acumulada'])) {
        return $row;
    } else {
        return null;
    }

}


function obtiene_acumulado_molienda_dia($dia): ?array{ 
    include_once 'conexiones/conexion.php';
    include_once 'funciones/funciones.php'; 

    $mysqli = conexion_db();
    if ($mysqli->connect_errno) {
        error_log("Error al conectar con la base de datos: " . $mysqli->connect_error);
        return null; // Retorna null si la conexión falla
    }
    $fecha_pesada = '';
    if ($dia === 'hoy') {
        $where = "fechaindustrial IS NULL AND YEAR(fecha_pesada) = YEAR(CURDATE())";
    } elseif ($dia === 'ultimo_cierre') {
        $sql_uc = "SELECT MAX(fechaindustrial) AS uc FROM datos_Cania";
        $row_uc  = $mysqli->query($sql_uc)->fetch_assoc();
        $uc      = $row_uc['uc'] ?? '';
        $where   = "fechaindustrial = '$uc'";
    } elseif ($dia === 'zafra') {
        $where = "YEAR(fecha_pesada) = (SELECT YEAR(MAX(fecha_pesada)) FROM datos_Cania)";
    } else {
        $where = "fechaindustrial = '$dia'";
    }

    $sql= " SELECT SUM(cania_bruta) AS cania_bruta, ROUND(SUM(trash * cania_bruta) / SUM(cania_bruta), 4) AS trash_ponderado,
            ROUND(SUM(trashReal * cania_bruta), 0) AS trash_kg, (SUM(cania_bruta) - ROUND(SUM(trashReal * cania_bruta), 0)) AS cania_neta,
            ROUND( SUM(CASE WHEN rendimiento > 0 THEN rendimiento * (cania_bruta - (trashReal * cania_bruta)) ELSE 0 END) / NULLIF(SUM(CASE WHEN rendimiento > 0 THEN (cania_bruta - (trashReal * cania_bruta)) ELSE 0 END), 0) ,4) AS rdto_ponderado,
            ROUND( SUM(CASE WHEN polporciento > 0 THEN polporciento * cania_bruta ELSE 0 END) / NULLIF(SUM(CASE WHEN polporciento > 0 THEN cania_bruta ELSE 0 END), 0), 4) AS pol_ponderado,
            ROUND( SUM(CASE WHEN brixporciento > 0 THEN brixporciento * cania_bruta ELSE 0 END) / NULLIF(SUM(CASE WHEN brixporciento > 0 THEN cania_bruta ELSE 0 END), 0), 4) AS brix_ponderado,
            ROUND( SUM(CASE WHEN pureza > 0 THEN pureza * cania_bruta ELSE 0 END) / NULLIF(SUM(CASE WHEN pureza > 0 THEN cania_bruta ELSE 0 END), 0), 4) AS pureza_ponderada
            FROM datos_Cania WHERE $where; ";    
    $stmt = $mysqli->prepare($sql);
    //echo $sql;
    if (!$stmt) {
        error_log("Error al preparar la consulta" . $mysqli->error);
        $mysqli->close(); // Cerrar conexión en caso de error
        return null; // Devuelve null en caso de error
    }

    $stmt->execute();
    $result = $stmt->get_result();
    
    $row = $result->fetch_assoc(); // una sola fila
    // Depuración fuerte:
    //var_dump($row);

    if (!empty($row)) {
        return $row;
    } else {
        return null;
    }

}

function DetalleMolienda(): ?array {
    include_once 'conexiones/conexion.php';
    include_once 'funciones/funciones.php'; 

    $mysqli = conexion_db();
    if ($mysqli->connect_errno) {
        error_log("Error al conectar con la base de datos: " . $mysqli->connect_error);
        return null; // Retorna null si la conexión falla
    }    
     
    $sql= "SELECT numero_pesada, grupo AS GRUPO, caniero, nro_muestra, cania_bruta, trash, trashReal, polporciento AS Polporciento,
            brixporciento AS Brixporciento, pureza AS Pureza, rendimiento AS Rendimiento, rendimientoReal AS RendimientoReal,
            tipo_cania, fecha_pesada, hora_pesada, fecha_salida, hora_salida, nromuestra2, prepesada, usuario, tipo_contrato,
            transporte, fletero, cosechero, finca, nombre_finca, patente, observaciones, tara
            FROM datos_Cania
            -- WHERE fecha_pesada = (SELECT MAX(fecha_pesada) FROM datos_Cania WHERE fecha_pesada IS NOT NULL)
            ORDER BY numero_pesada DESC LIMIT 50;";
    //echo $sql;
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        error_log("Error al preparar la consulta" . $mysqli->error);
        $mysqli->close(); // Cerrar conexión en caso de error
        return null; // Devuelve null en caso de error
    }    
    $stmt->execute();
    $result = $stmt->get_result();

    $detalle_molienda_rows = [];
    while ($row = $result->fetch_assoc()) {
        $detalle_molienda_rows[] = $row;
    }
    
    $result->free();
    $stmt->close();
    $mysqli->close(); // Cerrar la conexión aquí si la abres dentro de la función

    return $detalle_molienda_rows; // <-- Devuelve el array asociativo directamente (o null si no hay resultados)
}

function obtiene_indicadores_fabrica(): array {
    include_once 'conexiones/conexion.php';
    $mysqli = conexion_db();

    $nd = '--';
    $result = [
        'molienda_tn_h'   => $nd,
        'pol_cania'       => $nd,
        'humedad_bagazo'  => $nd,
        'gas_actual'      => $nd,
        'ph_sulfitado'    => $nd,
        'ph_encalado'     => $nd,
        'pol_cachaza'     => $nd,
        'sulfitacion_ppm' => $nd,
        'color_azucar'    => $nd,
        'humedad_azucar'  => $nd,
    ];

    if ($mysqli->connect_errno) return $result;

    $queries = [
        'molienda_tn_h'   => "SELECT kilos AS v FROM consumos_x_hora ORDER BY fechaindustrial DESC, hora DESC LIMIT 1",
        'pol_cania'       => "SELECT polporciento AS v FROM datos_Cania ORDER BY fechaindustrial DESC, hora_pesada DESC LIMIT 1",
        'humedad_bagazo'  => "SELECT humedad AS v FROM calidadBagazo_xHora ORDER BY fechaindustrial DESC, hora DESC LIMIT 1",
        'gas_actual'      => "SELECT gas AS v FROM consumos_x_hora ORDER BY fechaindustrial DESC, hora DESC LIMIT 1",
        'ph_sulfitado'    => "SELECT ROUND(AVG(phmanual),2) AS v FROM calidadSulfitado_xHora WHERE codigoproceso = 'Sulfitado' AND fechaindustrial = (SELECT MAX(fechaindustrial) FROM calidadSulfitado_xHora WHERE phmanual > 0 AND codigoproceso = 'Sulfitado')",
        'ph_encalado'     => "SELECT ROUND(AVG(phmanual),2) AS v FROM calidadSulfitado_xHora WHERE codigoproceso = 'Encalado' AND fechaindustrial = (SELECT MAX(fechaindustrial) FROM calidadSulfitado_xHora WHERE phmanual > 0 AND codigoproceso = 'Encalado')",
        'pol_cachaza'     => "SELECT ROUND(AVG(polautomatico),3) AS v FROM calidadCachaza_xHora WHERE fechaindustrial = (SELECT MAX(fechaindustrial) FROM calidadCachaza_xHora WHERE polautomatico > 0)",
        'sulfitacion_ppm' => "SELECT ROUND(AVG(PPMS02manual),1) AS v FROM calidadSulfitado_xHora WHERE codigoproceso = 'Sulfitado' AND fechaindustrial = (SELECT MAX(fechaindustrial) FROM calidadSulfitado_xHora WHERE PPMS02manual > 0)",
        'color_azucar'    => "SELECT ROUND(AVG(color),0) AS v FROM calidadAzucar_xHora WHERE codigoproceso = 'Cinta Larga' AND fechaindustrial = (SELECT MAX(fechaindustrial) FROM calidadAzucar_xHora WHERE color > 0 AND codigoproceso = 'Cinta Larga')",
        'humedad_azucar'  => "SELECT ROUND(AVG(humedad),3) AS v FROM calidadAzucar_xHora WHERE codigoproceso = 'Cinta Larga' AND fechaindustrial = (SELECT MAX(fechaindustrial) FROM calidadAzucar_xHora WHERE humedad > 0 AND codigoproceso = 'Cinta Larga')",
    ];

    foreach ($queries as $key => $sql) {
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) continue;
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if ($row && $row['v'] !== null) $result[$key] = $row['v'];
        $stmt->close();
    }

    $mysqli->close();
    return $result;
}

function obtiene_indicadores_opc(): array {
    include_once 'conexiones/conexion.php';
    $mysqli = conexion_db();

    $nd = '--';
    $result = [
        // Trapiche
        'velocidad_molino1'     => $nd, 'velocidad_molino6'   => $nd,
        'balanza_cinta'         => $nd, 'agua_imbibicion'     => $nd,
        'presion_molino6_este'  => $nd, 'presion_molino6_oeste' => $nd,
        // Fabricacion
        'caudal_jugo_clarif'    => $nd, 'nivel_melado_tratado' => $nd,
        'nivel_melado'          => $nd, 'nivel_decantador1'   => $nd,
        'nivel_decantador2'     => $nd, 'nivel_decantador3'   => $nd,
        'descarga_tachos_1ra'   => $nd, 'descarga_tachos_2da' => $nd,
        'descarga_tachos_3ra'   => $nd,
        // Salon
        'contador_bolsas_dia'   => $nd,
        'silo_a' => $nd, 'silo_b' => $nd, 'silo_c' => $nd, 'silo_e' => $nd,
        // Caldera
        'presion_vapor_directo' => $nd, 'presion_agua_alim'   => $nd,
        'presion_aire'          => $nd,
        'caudal_vapor_cald1'    => $nd, 'caudal_vapor_cald2'  => $nd,
        'caudal_vapor_cald3'    => $nd, 'caudal_vapor_cald6'  => $nd,
        'vapor_total'           => $nd, 'caudal_gas_cald2'    => $nd,
        'caudal_gas_cald6'      => $nd,
        // Usina
        'potencia_activa_siemens'   => $nd, 'potencia_reactiva_siemens' => $nd,
        'frecuencia_siemens'        => $nd, 'intensidad_siemens'        => $nd,
        'potencia_activa_aeg'       => $nd, 'potencia_reactiva_aeg'     => $nd,
        'frecuencia_aeg'            => $nd, 'intensidad_aeg'            => $nd,
        'potencia_total'            => $nd,
        // Consumos vapor
        'cv_trapiche' => $nd, 'cv_usina_alta' => $nd,
        'cv_destileria' => $nd, 'cv_aux_total' => $nd, 'cv_preparacion_cania' => $nd,
        // Destileria
        'caudal_vino' => $nd, 'caudal_alcohol' => $nd,
        'caudal_jugo_dilutor' => $nd, 'caudal_melaza_dilutor' => $nd, 'caudal_agua_dilutor' => $nd,
        // Nuevos Grupo 2
        'nivel_jugo_pesado' => $nd, 'nivel_jugo_clarificado' => $nd,
        'temp_agua_alim' => $nd, 'presion_vapor_escape' => $nd,
        // Nuevos Grupo 3
        'temp_calentador' => $nd, 'presion_vg1' => $nd,
        'nivel_agua_foza' => $nd, 'caudal_vino_160' => $nd,
        'presion_k2' => $nd, 'potencia_activa_edet' => $nd, 'intensidad_edet' => $nd,
        'potencia_activa_tgm' => $nd, 'intensidad_tgm' => $nd,
        // Meta
        'opc_timestamp' => $nd,
    ];

    if ($mysqli->connect_errno) return $result;

    $sql = "SELECT * FROM indicadores_opc ORDER BY timestamp DESC LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) { $mysqli->close(); return $result; }
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();

    if (!$row) return $result;

    foreach ($result as $key => $_) {
        if ($key === 'opc_timestamp') {
            $result['opc_timestamp'] = $row['timestamp'] ?? $nd;
        } elseif (isset($row[$key]) && $row[$key] !== null) {
            $result[$key] = round((float)$row[$key], 2);
        }
    }

    return $result;
}

function obtiene_prom_consumo_8hs(): string {
    include_once 'conexiones/conexion.php';
    $mysqli = conexion_db();
    if ($mysqli->connect_errno) return '--';

    $sql = "SELECT ROUND(AVG(
                COALESCE(potencia_activa_siemens, 0) +
                COALESCE(potencia_activa_aeg, 0) +
                COALESCE(potencia_activa_edet, 0)
            ), 0) AS prom
            FROM indicadores_opc
            WHERE timestamp >= NOW() - INTERVAL 8 HOUR
            AND (potencia_activa_siemens IS NOT NULL
              OR potencia_activa_aeg IS NOT NULL
              OR potencia_activa_edet IS NOT NULL)";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) { $mysqli->close(); return '--'; }
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();

    return ($row && $row['prom'] !== null) ? (string)(int)$row['prom'] : '--';
}

function obtiene_promedios_turno_anterior(): array {
    include_once 'conexiones/conexion.php';
    $mysqli = conexion_db();
    $nd = '--';
    $result = ['gas' => $nd, 'kilos' => $nd, 'humedad' => $nd, 'bolsas' => $nd, 'pol_bagazo' => $nd, 'pol_cachaza' => $nd];
    if ($mysqli->connect_errno) return $result;

    $h = (int)date('G');
    if ($h >= 5 && $h < 13) {
        $fecha = date('Y-m-d', strtotime('-1 day'));
        $where = "m.fechaindustrial = '$fecha' AND (m.hora >= '21:00' OR m.hora < '05:00')";
    } elseif ($h >= 13 && $h < 21) {
        $fecha = date('Y-m-d');
        $where = "m.fechaindustrial = '$fecha' AND m.hora >= '05:00' AND m.hora < '13:00'";
    } else {
        $fecha = ($h >= 21) ? date('Y-m-d') : date('Y-m-d', strtotime('-1 day'));
        $where = "m.fechaindustrial = '$fecha' AND m.hora >= '13:00' AND m.hora < '21:00'";
    }

    $sql = "SELECT ROUND(AVG(m.gas),1) AS gas, ROUND(AVG(m.kilos),0) AS kilos,
                   ROUND(AVG(m.humedad),2) AS humedad, ROUND(AVG(m.bolsas),0) AS bolsas,
                   ROUND(AVG(b.polmanual),3) AS pol_bagazo, ROUND(AVG(c.polautomatico),3) AS pol_cachaza
            FROM consumos_x_hora m
            LEFT JOIN calidadBagazo_xHora b ON b.fechaindustrial = m.fechaindustrial AND b.hora = m.hora
            LEFT JOIN calidadCachaza_xHora c ON c.fechaindustrial = m.fechaindustrial AND c.hora = m.hora
            WHERE $where AND m.gas > 0";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) { $mysqli->close(); return $result; }
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();

    if ($row) {
        foreach ($result as $key => $_) {
            if (isset($row[$key]) && $row[$key] !== null) $result[$key] = $row[$key];
        }
    }
    return $result;
}

function obtiene_camiones_canchon(): array {
    include_once 'conexiones/conexion.php';
    $mysqli = conexion_db();
    if ($mysqli->connect_errno) return [];
    $fecha = date('Y-m-d');
    $stmt = $mysqli->prepare(
        "SELECT hora_inicio AS hora, pesadas_count AS cantidad
         FROM molienda_tiempo_real
         WHERE fecha = ?
         ORDER BY hora_inicio ASC"
    );
    if (!$stmt) { $mysqli->close(); return []; }
    $stmt->bind_param('s', $fecha);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) $rows[] = $row;
    $result->free();
    $stmt->close();
    $mysqli->close();
    return $rows;
}

function obtiene_pre_ingreso_canchon(): int {
    include_once 'conexiones/conexion.php';
    $mysqli = conexion_db();
    if ($mysqli->connect_errno) return 0;
    $stmt = $mysqli->prepare("SELECT pre_ingreso FROM molienda_estado_actual WHERE id = 1");
    if (!$stmt) { $mysqli->close(); return 0; }
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();
    return (int)($row['pre_ingreso'] ?? 0);
}

function obtiene_estado_molienda(array $ind, array $opc): string {
    include_once 'conexiones/conexion.php';
    $mysqli = conexion_db();
    if ($mysqli->connect_errno) return 'rojo';
    $stmt = $mysqli->prepare("SELECT ultima_pesada FROM molienda_estado_actual WHERE id = 1");
    if (!$stmt) { $mysqli->close(); return 'rojo'; }
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();
    if (empty($row['ultima_pesada'])) return 'rojo';
    $ultima_ts   = strtotime(date('Y-m-d') . ' ' . $row['ultima_pesada']);
    $minutos_ago = (time() - $ultima_ts) / 60;
    if ($minutos_ago <= 20) return 'verde';
    if ($minutos_ago <= 50) return 'amarillo';
    return 'rojo';
}

function obtiene_molienda_en_curso(): float {
    include_once 'conexiones/conexion.php';
    $mysqli = conexion_db();
    if ($mysqli->connect_errno) return 0.0;
    // Slot actual = hora de cierre del período en curso (HH:00)
    $hora_cierre = sprintf('%02d:00', ((int)date('G') + 1) % 24);
    // Prueba primero consumos_x_hora (tiene datos del día tras el UNION con pr_ezi_movimientos)
    $fecha = date('Y-m-d');
    $stmt = $mysqli->prepare("SELECT kilos FROM consumos_x_hora WHERE fechaindustrial = ? AND hora = ?");
    if (!$stmt) { $mysqli->close(); return 0.0; }
    $stmt->bind_param('ss', $fecha, $hora_cierre);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!empty($row['kilos']) && (float)$row['kilos'] > 0) {
        $mysqli->close();
        return (float)$row['kilos'];
    }
    // Fallback: molienda_tiempo_real (actualizado cada 30 min por Node-RED)
    $hora_inicio = sprintf('%02d:00', (int)date('G'));
    $stmt2 = $mysqli->prepare("SELECT neto_cana_kg FROM molienda_tiempo_real WHERE fecha = ? AND hora_inicio = ?");
    if (!$stmt2) { $mysqli->close(); return 0.0; }
    $stmt2->bind_param('ss', $fecha, $hora_inicio);
    $stmt2->execute();
    $row2 = $stmt2->get_result()->fetch_assoc();
    $stmt2->close();
    $mysqli->close();
    return (float)($row2['neto_cana_kg'] ?? 0);
}

function obtiene_gas_promedio_turno_anterior(): string {
    include_once 'conexiones/conexion.php';
    $mysqli = conexion_db();
    if ($mysqli->connect_errno) return '--';

    $h = (int)date('G'); // hora actual 0-23

    if ($h >= 5 && $h < 13) {
        // Turno actual 1 (05-13) → anterior = Turno 3 (21-05) de ayer
        $fecha = date('Y-m-d', strtotime('-1 day'));
        $sql = "SELECT ROUND(AVG(gas), 1) AS prom FROM consumos_x_hora
                WHERE gas > 0 AND fechaindustrial = '$fecha'
                AND (hora >= '21:00' OR hora < '05:00')";
    } elseif ($h >= 13 && $h < 21) {
        // Turno actual 2 (13-21) → anterior = Turno 1 (05-13) de hoy
        $fecha = date('Y-m-d');
        $sql = "SELECT ROUND(AVG(gas), 1) AS prom FROM consumos_x_hora
                WHERE gas > 0 AND fechaindustrial = '$fecha'
                AND hora >= '05:00' AND hora < '13:00'";
    } else {
        // Turno actual 3 (21-05) → anterior = Turno 2 (13-21)
        // Si hora < 5 (madrugada) el turno 2 fue "ayer"
        $fecha = ($h >= 21) ? date('Y-m-d') : date('Y-m-d', strtotime('-1 day'));
        $sql = "SELECT ROUND(AVG(gas), 1) AS prom FROM consumos_x_hora
                WHERE gas > 0 AND fechaindustrial = '$fecha'
                AND hora >= '13:00' AND hora < '21:00'";
    }

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) { $mysqli->close(); return '--'; }
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();

    return ($row && $row['prom'] !== null) ? (string)$row['prom'] : '--';
}


?>