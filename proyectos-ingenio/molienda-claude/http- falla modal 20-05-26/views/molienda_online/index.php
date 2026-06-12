  <?php
    include 'controller/molienda_online.php';

    $hora = $_POST['hora'] ?? '';
    $fechaindustrial_raw = $_POST['fechaindustrial'] ?? '';
    $fechaindustrial = preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaindustrial_raw) ? $fechaindustrial_raw : date('Y-m-d');

    $ind = obtiene_indicadores_fabrica();
    $opc = obtiene_indicadores_opc();
    $gas_prom_turno_ant = obtiene_gas_promedio_turno_anterior();
    $prom_consumo_8hs = obtiene_prom_consumo_8hs();

    $acumulado = obtiene_molienda_acumulada() ?? [];
    $acumulado_cania = number_format((float)($acumulado['molienda_acumulada'] ?? 0), 0, ',', '.');
    $ultima_actualizacion = fechaDma($acumulado['ultima_actualizacion'] ?? '') ?? '';

    /*MOLIENDA ACTUAL */
    $acumulado_molienda_dia = obtiene_acumulado_molienda_dia('hoy') ?? [];
    $cania_bruta     = number_format((float)($acumulado_molienda_dia['cania_bruta']     ?? 0), 0, ',', '.');
    $trash_ponderado = number_format((float)($acumulado_molienda_dia['trash_ponderado'] ?? 0), 2, ',', '.');
    $trash_kg        = number_format((float)($acumulado_molienda_dia['trash_kg']        ?? 0), 0, ',', '.');
    $cania_neta      = number_format((float)($acumulado_molienda_dia['cania_neta']      ?? 0), 0, ',', '.');
    $rdto_ponderado  = number_format((float)($acumulado_molienda_dia['rdto_ponderado']  ?? 0), 2, ',', '.');
    $pol_ponderado   = number_format((float)($acumulado_molienda_dia['pol_ponderado']   ?? 0), 2, ',', '.');
    $brix_ponderado  = number_format((float)($acumulado_molienda_dia['brix_ponderado']  ?? 0), 2, ',', '.');
    $pureza_ponderada = number_format((float)($acumulado_molienda_dia['pureza_ponderada'] ?? 0), 2, ',', '.');

    /*MOLIENDA ULTIMO CIERRE */
    $acumulado_molienda_uc = obtiene_acumulado_molienda_dia('ultimo_cierre') ?? [];
    $cania_bruta_uc     = number_format((float)($acumulado_molienda_uc['cania_bruta']     ?? 0), 0, ',', '.');
    $trash_ponderado_uc = number_format((float)($acumulado_molienda_uc['trash_ponderado'] ?? 0), 2, ',', '.');
    //$trash_kg_uc      = number_format((float)($acumulado_molienda_uc['trash_kg']        ?? 0), 0, ',', '.');
    $cania_neta_uc      = number_format((float)($acumulado_molienda_uc['cania_neta']      ?? 0), 0, ',', '.');
    $rdto_ponderado_uc  = number_format((float)($acumulado_molienda_uc['rdto_ponderado']  ?? 0), 2, ',', '.');
    $pol_ponderado_uc   = number_format((float)($acumulado_molienda_uc['pol_ponderado']   ?? 0), 2, ',', '.');
    $brix_ponderado_uc  = number_format((float)($acumulado_molienda_uc['brix_ponderado']  ?? 0), 2, ',', '.');
    $pureza_ponderada_uc = number_format((float)($acumulado_molienda_uc['pureza_ponderada'] ?? 0), 2, ',', '.');
    
    //PARADAS
    $consulta ='paradas';
    $sql = EjecutarConsulta($consulta, $fechaindustrial, $hora);
    //echo $sql;
    $paradas = ObtieneDatosSQL($sql);
    $cant_tiempo_parada = 0;
    foreach ($paradas as $parada):
          $cant_tiempo_parada += (int)($parada['t_neto_minutos'] ?? 0);
    endforeach;
    $cant_tiempo_parada = $cant_tiempo_parada.' min.';

    
    //ULTIMA PESADA
    $consulta ='ultima_pesada';
    $sql = EjecutarConsulta($consulta, $fechaindustrial, $hora);
    //echo $sql;
    $row = ObtieneDatosSQL($sql);
    if(!empty($row)){
      $hora_ultima_pesada = $row[0]['hora'];
    }else{
      $hora_ultima_pesada = '';
    }   
    
    //POL PROMEDIO BAGAZO Y CACHAZA
    $consulta ='pol_promedio';
    $sql = EjecutarConsulta($consulta, $fechaindustrial, $hora);
    $row = ObtieneDatosSQL($sql);
    if(!empty($row)){
      $pol_prom_bagazo = $row[0]['pol_prom_bagazo'];
      $pol_prom_cachaza = $row[0]['pol_prom_cachaza'];
    }else{
      $pol_prom_bagazo = '';
      $pol_prom_cachaza = '';
    }

    // ACUMULADO ZAFRA
    $acumulado_zafra = obtiene_acumulado_molienda_dia('zafra') ?? [];
    $cania_bruta_acumulado      = number_format((float)($acumulado_zafra['cania_bruta']       ?? 0), 0, ',', '.');
    $trash_ponderado_acumulado  = number_format((float)($acumulado_zafra['trash_ponderado']   ?? 0), 2, ',', '.');
    $cania_neta_acumulado       = number_format((float)($acumulado_zafra['cania_neta']        ?? 0), 0, ',', '.');
    $rdto_ponderado_acumulado   = number_format((float)($acumulado_zafra['rdto_ponderado']    ?? 0), 2, ',', '.');
    $brix_ponderado_acumulado   = number_format((float)($acumulado_zafra['brix_ponderado']    ?? 0), 2, ',', '.');
    $pol_ponderado_acumulado    = number_format((float)($acumulado_zafra['pol_ponderado']     ?? 0), 2, ',', '.');
    $pureza_ponderada_acumulado = number_format((float)($acumulado_zafra['pureza_ponderada']  ?? 0), 2, ',', '.');

    // AÑO DE ZAFRA — desde la última fecha_pesada (no depende de fechaindustrial que puede ser NULL al inicio de zafra)
    $anio_zafra_row = ObtieneDatosSQL("SELECT YEAR(MAX(fecha_pesada)) AS anio FROM datos_Cania");
    $anio_zafra = ($anio_zafra_row[0] ?? [])['anio'] ?? date('Y');

    // PROMEDIOS TURNO ANTERIOR
    $prom_turno_ant = obtiene_promedios_turno_anterior();

    // PROMEDIOS EN CANCHON
    $row_prom = ObtieneDatosSQL("
        SELECT ROUND(AVG(kilos), 0) AS prom_hora,
               ROUND(SUM(kilos) / NULLIF(COUNT(DISTINCT fechaindustrial), 0), 0) AS prom_dia
        FROM consumos_x_hora
        WHERE YEAR(fechaindustrial) = (SELECT YEAR(MAX(fechaindustrial)) FROM consumos_x_hora) AND kilos > 0");
    $r_prom = $row_prom[0] ?? [];
    $promedio_hora = $r_prom['prom_hora'] ?? 0;
    $promedio_dia  = $r_prom['prom_dia']  ?? 0;

    //---------------------//


  ?>

  <!-- Barra superior -->
  <div class="dash-header">
    <img src="assets/img/Logo-Ing La Corona.png" alt="Ingenio La Corona" style="height:32px; object-fit:contain;">
    <span class="fw-bold text-secondary" style="font-size:0.95rem;">Molienda Online</span>
    <span class="text-muted small">Fecha: <?php echo date('d/m/Y'); ?> &nbsp;|&nbsp; Zafra: <?php echo $anio_zafra; ?></span>
    <span class="ms-auto small fw-semibold text-secondary" id="hora_molienda_online"></span>
  </div>

  <!-- Barra de botones -->
  <div class="dash-btn-bar">
    <button class="btn btn-info btn-sm text-white" onclick="abrirModal('indicadoresFabricacionModal')">Ind. Fabricación</button>
    <button class="btn btn-info btn-sm text-white" onclick="abrirModal('indicadoresTrapicheModal')">Ind. Trapiche</button>
    <button class="btn btn-info btn-sm text-white" onclick="abrirModal('indicadoresCalderaModal')">Ind. Caldera</button>
    <button class="btn btn-info btn-sm text-white" onclick="abrirModal('indicadoresUsinaModal')">Ind. Usina</button>
    <button class="btn btn-info btn-sm text-white" onclick="abrirModal('indicadoresDestileriaModal')">Ind. Destilería</button>
    <span class="border-start mx-1"></span>
    <button class="btn btn-secondary btn-sm text-white" onclick="cargarAnalisis()">Análisis Azúcar</button>
    <button class="btn btn-danger btn-sm text-white" onclick="abrirModal('resumenfabricaModal')">Resumen Fábrica</button>
  </div>

  <!-- Contenido principal -->
  <div class="row g-0 p-1" style="height:calc(100vh - 115px); overflow:hidden;">

    <!-- Columna izquierda: datos de molienda -->
    <div class="col-lg-6 pe-1 d-flex flex-column gap-1" style="height:100%;">

      <div class="card p-2 flex-shrink-0">
        <table class="table table-sm table-bordered dash-data-table mb-0">
          <thead>
            <tr>
              <th style="width:38%"></th>
              <th class="text-center">Actual</th>
              <th class="text-center">Últ. Cierre</th>
              <th class="text-center">Acumulado</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="fw-semibold text-secondary">Caña Bruta</td>
              <td class="text-center"><strong><?php echo $cania_bruta ?? 0; ?></strong></td>
              <td class="text-center"><strong><?php echo $cania_bruta_uc ?? 0; ?></strong></td>
              <td class="text-center"><strong><?php echo $cania_bruta_acumulado ?? 0; ?></strong></td>
            </tr>
            <tr>
              <td class="fw-semibold text-secondary">Trash Pond.</td>
              <td class="text-center"><strong><?php echo $trash_ponderado ?? 0; ?></strong></td>
              <td class="text-center"><strong><?php echo $trash_ponderado_uc ?? 0; ?></strong></td>
              <td class="text-center"><strong><?php echo $trash_ponderado_acumulado ?? 0; ?></strong></td>
            </tr>
            <tr>
              <td class="fw-semibold text-secondary">Trash Kg</td>
              <td class="text-center"><strong><?php echo $trash_kg ?? 0; ?></strong></td>
              <td class="text-center">—</td>
              <td class="text-center">—</td>
            </tr>
            <tr>
              <td class="fw-semibold text-secondary">Caña Neta</td>
              <td class="text-center"><strong><?php echo $cania_neta ?? 0; ?></strong></td>
              <td class="text-center"><strong><?php echo $cania_neta_uc ?? 0; ?></strong></td>
              <td class="text-center"><strong><?php echo $cania_neta_acumulado ?? 0; ?></strong></td>
            </tr>
            <tr>
              <td class="fw-semibold text-secondary">Rto. Ponderado</td>
              <td class="text-center"><strong><?php echo $rdto_ponderado ?? 0; ?></strong></td>
              <td class="text-center"><strong><?php echo $rdto_ponderado_uc ?? 0; ?></strong></td>
              <td class="text-center"><strong><?php echo $rdto_ponderado_acumulado ?? 0; ?></strong></td>
            </tr>
            <tr>
              <td class="fw-semibold text-secondary">Brix Ponderado</td>
              <td class="text-center"><strong><?php echo $brix_ponderado ?? 0; ?></strong></td>
              <td class="text-center"><strong><?php echo $brix_ponderado_uc ?? 0; ?></strong></td>
              <td class="text-center"><strong><?php echo $brix_ponderado_acumulado ?? 0; ?></strong></td>
            </tr>
            <tr>
              <td class="fw-semibold text-secondary">Pol Ponderado</td>
              <td class="text-center"><strong><?php echo $pol_ponderado ?? 0; ?></strong></td>
              <td class="text-center"><strong><?php echo $pol_ponderado_uc ?? 0; ?></strong></td>
              <td class="text-center"><strong><?php echo $pol_ponderado_acumulado ?? 0; ?></strong></td>
            </tr>
            <tr>
              <td class="fw-semibold text-secondary">Pureza Pond.</td>
              <td class="text-center"><strong><?php echo $pureza_ponderada ?? 0; ?></strong></td>
              <td class="text-center"><strong><?php echo $pureza_ponderada_uc ?? 0; ?></strong></td>
              <td class="text-center"><strong><?php echo $pureza_ponderada_acumulado ?? 0; ?></strong></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="card p-2 flex-grow-1" style="overflow:auto; min-height:0;">
        <?php include 'views/molienda_online/detalle_cania.php'; ?>
      </div>
      <!-- Paradas + indicadores extra -->
      <div class="card p-2 flex-shrink-0">
        <div class="d-flex align-items-center flex-wrap gap-2" style="font-size:0.8rem;">
          <button class="btn btn-secondary btn-sm" onclick="abrirModal('paradasModal')">Paradas</button>
          <div class="d-flex align-items-center gap-1">
            <span class="fw-semibold text-secondary">Total:</span>
            <input type="text" class="form-control form-control-sm fw-bold" style="width:85px;" value="<?php echo $cant_tiempo_parada ?? 0; ?>">
          </div>
          <div class="d-flex align-items-center gap-1">
            <span class="fw-semibold text-secondary">Últ. Pesada:</span>
            <input type="text" class="form-control form-control-sm fw-bold" style="width:65px;" value="<?php echo $hora_ultima_pesada ?? ''; ?>">
          </div>
          <div class="d-flex align-items-center gap-1">
            <span class="fw-semibold text-secondary">Pol Bagazo:</span>
            <input type="text" class="form-control form-control-sm fw-bold" style="width:60px;" value="<?php echo $pol_prom_bagazo ?? 0; ?>">
          </div>
          <div class="d-flex align-items-center gap-1">
            <span class="fw-semibold text-secondary">Pol Cachaza:</span>
            <input type="text" class="form-control form-control-sm fw-bold" style="width:60px;" value="<?php echo $pol_prom_cachaza ?? 0; ?>">
          </div>
          <div class="d-flex align-items-center gap-1">
            <span class="fw-semibold text-secondary">Agua Imbibición:</span>
            <input type="text" class="form-control form-control-sm fw-bold" style="width:70px;" value="<?php echo $opc['agua_imbibicion'] ?? '--'; ?>">
          </div>
        </div>
      </div>

    </div>

    <!-- Columna derecha: tabla de consumo + canchón -->
    <div class="col-lg-6 ps-1 d-flex flex-column gap-1" style="height:100%;">

      <div class="card p-2 flex-grow-1" style="overflow-y:auto; min-height:0;">
        <?php include 'datos_molienda_hora.php'; ?>
      </div>

      <div class="card p-2 flex-shrink-0">
        <div class="d-flex align-items-center flex-wrap gap-2" style="font-size:0.8rem;">
          <strong class="text-secondary">En Canchón</strong>
          <button class="btn btn-secondary btn-sm" onclick="abrirModal('camionescanchonModal')">Evolución x Hora</button>
          <div class="d-flex align-items-center gap-1">
            <span class="fw-semibold text-secondary">Acumulado:</span>
            <input type="text" class="form-control form-control-sm fw-bold" style="width:95px;" value="<?php echo $acumulado_cania ?? 0; ?>">
          </div>
          <div class="d-flex align-items-center gap-1">
            <span class="fw-semibold text-secondary">Prom/Hora:</span>
            <input type="text" class="form-control form-control-sm fw-bold" style="width:70px;" value="<?php echo $promedio_hora ?? 0; ?>">
          </div>
          <div class="d-flex align-items-center gap-1">
            <span class="fw-semibold text-secondary">Prom/Día:</span>
            <input type="text" class="form-control form-control-sm fw-bold" style="width:70px;" value="<?php echo $promedio_dia ?? 0; ?>">
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Modals -->
  <div class="modal fade" id="paradasModal" tabindex="-1" aria-labelledby="paradasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width:95vw;">
      <div class="modal-content">
        <div class="modal-header py-2">
          <h5 class="modal-title" id="paradasModalLabel">Paradas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body p-2">
          <?php include 'views/molienda_online/paradas.php'; ?>
        </div>
        <div class="modal-footer py-1">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="camionescanchonModal" tabindex="-1" aria-labelledby="camionescanchonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header py-2">
          <h5 class="modal-title" id="camionescanchonModalLabel">Evolución x Hora — Canchón</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body p-2">
          <?php include 'views/molienda_online/camiones_en_canchon.php'; ?>
        </div>
        <div class="modal-footer py-1">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="monitoreoFabricaModal" tabindex="-1" aria-labelledby="monitoreoFabricaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width:95vw;">
      <div class="modal-content" style="max-height:90vh;">
        <div class="modal-header bg-light sticky-top py-2">
          <h5 class="modal-title text-secondary" id="monitoreoFabricaModalLabel">
            Monitoreo Fábrica <span class="badge bg-secondary">OPC</span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body p-2" style="overflow-y:auto; overflow-x:auto;">
          <?php include 'views/molienda_online/monitoreo_fabrica.php'; ?>
        </div>
        <div class="modal-footer bg-light py-1">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="indicadoresFabricacionModal" tabindex="-1" aria-labelledby="indicadoresFabricacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 95vw;"> 
      <div class="modal-content" style="max-height: 90vh;">
        <div class="modal-header bg-light sticky-top"> 
          <h5 class="modal-title text-secondary" id="indicadoresFabricacionModalLabel">
            Indicadores de Fabricación <span class="badge bg-secondary">#10</span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>        
        </div>
        <div class="modal-body fw-bold p-2 p-md-3" style="overflow-y: auto; overflow-x: auto;">
          <div class="container">
            <?php include 'views/indicadores_fabrica/ind_fabricacion.php'; ?>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="indicadoresTrapicheModal" tabindex="-1" aria-labelledby="indicadoresTrapicheModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 95vw;"> 
      <div class="modal-content" style="max-height: 90vh;">
        <div class="modal-header bg-light sticky-top"> 
          <h5 class="modal-title text-secondary" id="indicadoresTrapicheModalLabel">
            Indicadores de Trapiche <span class="badge bg-secondary">#7</span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>        
        </div>
        <div class="modal-body fw-bold p-2 p-md-3" style="overflow-y: auto; overflow-x: auto;">
          <div class="container">
            <?php include 'views/indicadores_fabrica/ind_trapiche.php'; ?>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="indicadoresCalderaModal" tabindex="-1" aria-labelledby="indicadoresCalderaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 95vw;"> 
      <div class="modal-content" style="max-height: 90vh;">
        <div class="modal-header bg-light sticky-top"> 
          <h5 class="modal-title text-secondary" id="indicadoresCalderaModalLabel">
            Indicadores de Caldera <span class="badge bg-secondary">#8</span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>        
        </div>
        <div class="modal-body fw-bold p-2 p-md-3" style="overflow-y: auto; overflow-x: auto;">
          <div class="container">
            <?php include 'views/indicadores_fabrica/ind_caldera.php'; ?>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>  
  
  <div class="modal fade" id="indicadoresUsinaModal" tabindex="-1" aria-labelledby="indicadoresUsinaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 95vw;"> 
      <div class="modal-content" style="max-height: 90vh;">
        <div class="modal-header bg-light sticky-top"> 
          <h5 class="modal-title text-secondary" id="indicadoresUsinaModalLabel">
            Indicadores de Usina <span class="badge bg-secondary">#10</span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>        
        </div>
        <div class="modal-body fw-bold p-2 p-md-3" style="overflow-y: auto; overflow-x: auto;">
          <div class="container">
            <?php include 'views/indicadores_fabrica/ind_usina.php'; ?>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
  
  <div class="modal fade" id="indicadoresDestileriaModal" tabindex="-1" aria-labelledby="indicadoresDestileriaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 95vw;"> 
      <div class="modal-content" style="max-height: 90vh;">
        <div class="modal-header bg-light sticky-top"> 
          <h5 class="modal-title text-secondary" id="indicadoresDestileriaModalLabel">
            Indicadores de Destileria <span class="badge bg-secondary">#4</span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>        
        </div>
        <div class="modal-body fw-bold p-2 p-md-3" style="overflow-y: auto; overflow-x: auto;">
          <div class="container">
            <?php include 'views/indicadores_fabrica/ind_destileria.php'; ?>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
  
  <div class="modal fade" id="analisisazucarModal" tabindex="-1">
    <div class="modal-dialog modal-xl" style="max-width:95vw;">
      <div class="modal-content">
        <div class="modal-header py-2">
          <h5 class="modal-title">Análisis de Azúcar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div id="contenidoModalAnalisis" class="modal-body p-2">
          <div class="text-center">Cargando datos...</div>
        </div>
        <div class="modal-footer py-1">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="resumenfabricaModal" tabindex="-1" aria-labelledby="resumenfabricaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width:95vw;">
      <div class="modal-content">
        <div class="modal-header py-2">
          <h5 class="modal-title" id="resumenfabricaModalLabel">Resumen de Fábrica</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body p-2">
          <?php include 'views/indicadores_fabrica/resumen_fabrica.php'; ?>
        </div>
        <div class="modal-footer py-1">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="detallecaniaModal" tabindex="-1" aria-labelledby="detallecaniaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width:95vw;">
      <div class="modal-content">
        <div class="modal-header py-2">
          <h5 class="modal-title" id="detallecaniaModalLabel">Detalle de Caña</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body p-2">
          <?php include 'views/molienda_online/detalle_cania.php'; ?>
        </div>
        <div class="modal-footer py-1">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  

  <!-- Gestión de modales: Bootstrap API si cargó, fallback manual si no -->
  <script>
  var __modalBackdrop = null;
  var __modalActivo   = null;

  function abrirModal(id) {
    try {
      var el = document.getElementById(id);
      if (!el) { alert('Modal no encontrado: ' + id); return; }

      // Cerrar anterior si hay uno abierto
      if (__modalActivo && __modalActivo !== id) cerrarModal(__modalActivo);

      // Bootstrap 5 API disponible
      if (window.bootstrap && window.bootstrap.Modal) {
        window.bootstrap.Modal.getOrCreateInstance(el).show();
        __modalActivo = id;
        return;
      }

      // Fallback manual
      if (__modalBackdrop) { __modalBackdrop.parentNode && __modalBackdrop.parentNode.removeChild(__modalBackdrop); }
      __modalBackdrop = document.createElement('div');
      __modalBackdrop.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1040;';
      __modalBackdrop.onclick = function() { cerrarModal(id); };
      document.body.appendChild(__modalBackdrop);

      el.style.display    = 'block';
      el.style.position   = 'fixed';
      el.style.top        = '5%';
      el.style.left       = '50%';
      el.style.transform  = 'translateX(-50%)';
      el.style.zIndex     = '1055';
      el.style.maxHeight  = '90vh';
      el.style.overflowY  = 'auto';
      el.style.background = '#fff';
      el.style.borderRadius = '8px';
      el.style.boxShadow  = '0 4px 32px rgba(0,0,0,0.35)';
      document.body.style.overflow = 'hidden';
      __modalActivo = id;

      // Botones cerrar (compatible IE11 — sin NodeList.forEach)
      var btns = el.querySelectorAll('[data-bs-dismiss="modal"]');
      for (var i = 0; i < btns.length; i++) {
        btns[i].onclick = (function(modalId){ return function(){ cerrarModal(modalId); }; })(id);
      }
    } catch(e) {
      alert('Error al abrir modal: ' + e.message);
    }
  }

  function cerrarModal(id) {
    try {
      var el = document.getElementById(id);
      if (!el) return;

      if (window.bootstrap && window.bootstrap.Modal) {
        var m = window.bootstrap.Modal.getInstance(el);
        if (m) { m.hide(); }
      }

      // Limpiar estilos inline del fallback
      el.style.display  = 'none';
      el.style.position = '';
      el.style.top      = '';
      el.style.left     = '';
      el.style.transform = '';
      el.style.zIndex   = '';
      el.style.maxHeight = '';
      el.style.overflowY = '';
      document.body.style.overflow = '';

      if (__modalBackdrop && __modalBackdrop.parentNode) {
        __modalBackdrop.parentNode.removeChild(__modalBackdrop);
      }
      __modalBackdrop = null;
      __modalActivo   = null;
    } catch(e) { /* silencioso */ }
  }
  </script>

  <!-- Bootstrap JS: async para no bloquear el parser mientras descarga del CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" async></script>

  <script>
  (function () {
    var COLORES = {
      verde:    { bg:'rgba(25,135,84,0.10)',  borde:'#198754', dot:'#198754' },
      amarillo: { bg:'rgba(230,168,23,0.15)', borde:'#e6a817', dot:'#e6a817' },
      rojo:     { bg:'rgba(220,53,69,0.12)',  borde:'#dc3545', dot:'#dc3545' },
      gris:     { bg:'rgba(108,117,125,0.07)',borde:'#adb5bd', dot:'#adb5bd' }
    };
    function num(s) {
      if (!s || s === '--') return NaN;
      return parseFloat(String(s).replace(',', '.').trim());
    }
    function calcEstado(val, obj, dir) {
      obj = String(obj || '').trim();
      if (!obj || obj === '--') return 'gris';
      var v = num(val);
      if (isNaN(v)) return 'gris';
      var m = obj.match(/^([\d.,]+)\s*(?:a|-)\s*([\d.,]+)$/i);
      if (m) {
        var mn = num(m[1]), mx = num(m[2]);
        if (isNaN(mn) || isNaN(mx)) return 'gris';
        if (v >= mn && v <= mx) return 'verde';
        var mg = ((mn + mx) / 2) * 0.10;
        return (v >= mn - mg && v <= mx + mg) ? 'amarillo' : 'rojo';
      }
      var o = num(obj);
      if (isNaN(o)) return 'gris';
      if (o === 0) return (v === 0) ? 'verde' : 'rojo';
      if (dir === 'less') {
        if (v <= o) return 'verde';
        return ((v - o) / o <= 0.10) ? 'amarillo' : 'rojo';
      }
      var d = Math.abs(v - o) / o;
      return d <= 0.05 ? 'verde' : d <= 0.10 ? 'amarillo' : 'rojo';
    }
    function aplicarSemaforos() {
      document.querySelectorAll('.semaforo-card').forEach(function (card) {
        var estado = calcEstado(
          card.dataset.valor     || '--',
          card.dataset.objetivo  || '',
          card.dataset.direction || 'target'
        );
        var c = COLORES[estado];
        card.style.backgroundColor = c.bg;
        card.style.borderLeft = '4px solid ' + c.borde;
        card.style.borderRadius = '0.5rem';
        var dot = card.querySelector('.semaforo-dot');
        if (dot) {
          dot.style.backgroundColor = c.dot;
          dot.title = estado.charAt(0).toUpperCase() + estado.slice(1);
        }
      });
    }
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', aplicarSemaforos);
    } else {
      aplicarSemaforos();
    }
  })();
  </script>
 
  <script>
    function cargarAnalisis(horaSeleccionada) {
        horaSeleccionada = horaSeleccionada || '';
        abrirModal('analisisazucarModal');
        var contenedor = document.getElementById('contenidoModalAnalisis');
        if (contenedor) contenedor.innerHTML = '<div class="text-center p-3">Cargando...</div>';
        fetch('home.php?menu=analisis_azucar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'hora=' + encodeURIComponent(horaSeleccionada)
        })
        .then(function(r) { return r.text(); })
        .then(function(data) {
            if (contenedor) {
                contenedor.innerHTML = data;
                var sel = document.getElementById('hora');
                if (sel) sel.addEventListener('change', function() { cargarAnalisis(this.value); });
            }
        });
    }
  </script>
  <script>
  
    window.onload = function() {
      // refrescar 
      setTimeout(function() {
        location.reload();
      }, 300000);
    };
    
    function actualizarHora() {
      const ahora = new Date();

      const hora = ahora.toLocaleTimeString("es-AR", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit"
      });

      document.getElementById("hora_molienda_online").innerHTML = `
        <p><strong>Molienda Online <span class="text-primary ms-2">${hora}</span></strong></p>  
      `;
    }

    function abre_pestania(url){
      window.open(url, '_blank');
    }
    actualizarHora();
    setInterval(actualizarHora, 1000);
  </script>
