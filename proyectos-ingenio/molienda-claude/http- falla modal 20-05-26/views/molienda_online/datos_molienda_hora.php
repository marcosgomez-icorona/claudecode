<?php
  
  $consulta ='consumo_x_hora';
  $fechaindustrial = $_POST['fechaindustrial'] ?? date('Y-m-d');
  $sql = EjecutarConsulta($consulta,$fechaindustrial,$hora ?? '');
  $datos = ObtieneDatosSQL($sql) ?? [];
  $gas_acumulado = 0;
  $total_bolsas =  0;
  $total_anhidro = 0;
?>
<div>
              <div class="d-flex align-items-center flex-wrap gap-2 mb-1 px-1 py-1 rounded" style="background:rgba(108,117,125,0.07); font-size:0.78rem; font-weight:600;">
                <span class="ind-section-title me-1" style="border-bottom:none; margin-bottom:0; padding-bottom:0;">Prom. T. ant.:</span>
                <span class="text-secondary">Gas:</span>
                <span class="text-dark"><?php echo $prom_turno_ant['gas'] ?? '--'; ?></span>
                <span class="text-muted">|</span>
                <span class="text-secondary">Molienda:</span>
                <span class="text-dark"><?php echo $prom_turno_ant['kilos'] ?? '--'; ?></span>
                <span class="text-muted">|</span>
                <span class="text-secondary">Humedad:</span>
                <span class="text-dark"><?php echo $prom_turno_ant['humedad'] ?? '--'; ?></span>
                <span class="text-muted">|</span>
                <span class="text-secondary">Bolsas:</span>
                <span class="text-dark"><?php echo $prom_turno_ant['bolsas'] ?? '--'; ?></span>
                <span class="text-muted">|</span>
                <span class="text-secondary">Pol Baz.:</span>
                <span class="text-dark"><?php echo $prom_turno_ant['pol_bagazo'] ?? '--'; ?></span>
                <span class="text-muted">|</span>
                <span class="text-secondary">Pol Cach.:</span>
                <span class="text-dark"><?php echo $prom_turno_ant['pol_cachaza'] ?? '--'; ?></span>
              </div>
              <h6 class="fw-bold text-secondary mb-1" style="font-size:0.82rem;">Consumo de Gas y Molienda</h6>
              <div class="row">
                <div class="col-12">
                  <div class="table-responsive">
                    <table class="table table-striped table-bordered table-sm align-middle mb-0 dash-data-table">
                      <thead class="table-light text-center text-dark">
                        <tr class="text-start">
                          <th>Fecha</th>
                          <th>Hora</th>
                          <th>Gas M3</th>
                          <th>Molienda</th>
                          <th>Humedad %</th>
                          <th>Bolsas</th>
                          <th>Alc. Ind. M3</th>
                          <th>Pol Bagazo %</th>
                          <th>Pol Cachaza %</th>
                          <th>Jugo Deriv. M3</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($datos as $item): ?>
                        <tr>
                            <td class="text-start"><strong><?php echo substr(fechaDma($item['fechaindustrial']),0,5) ?? ''; ?></strong></td>
                            <td class="text-start"><strong><?php echo htmlspecialchars(rango_hora($item['hora'] ?? '') ); ?></strong></td>
                            <td class="text-start"><strong><?php echo htmlspecialchars($item['gas'] ?? ''); ?></strong></td>
                            <td class="text-start"><strong><?php echo htmlspecialchars($item['kilos'] ?? ''); ?></strong></td>
                            <td class="text-start"><strong><?php echo htmlspecialchars($item['humedad'] ?? ''); ?></strong></td>
                            <td class="text-start"><strong><?php echo htmlspecialchars($item['bolsas'] ?? ''); ?></strong></td>                                                       
                            <td class="text-start"><strong><?php echo htmlspecialchars($item['alc_industrial'] ?? ''); ?></strong></td> 
                            <td class="text-start"><strong><?php echo htmlspecialchars($item['pol_bagazo'] ?? ''); ?></strong></td> 
                            <td class="text-start"><strong><?php echo htmlspecialchars($item['pol_cachaza'] ?? ''); ?></strong></td> 
                            <td class="text-start"><strong><?php echo htmlspecialchars($item['jugo_derivado'] ?? ''); ?></strong></td> 
                        </tr>   
                        <?php
                              $gas_acumulado = $gas_acumulado + ($item['gas']    ?? 0);
                              $total_bolsas  = $total_bolsas  + ($item['bolsas'] ?? 0);
                              $total_anhidro = 0;
                            endforeach; ?>                     
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="d-flex align-items-center gap-3 mt-1" style="font-size:14px; font-weight:700;">
                <div class="d-flex align-items-center gap-1">
                  <span class="text-secondary">Gas acum.:</span>
                  <input type="text" class="form-control form-control-sm" style="width:80px;font-size:14px;font-weight:700;" value="<?php echo $gas_acumulado ?? 0; ?>">
                </div>
                <div class="d-flex align-items-center gap-1">
                  <span class="text-secondary">Bolsas:</span>
                  <input type="text" class="form-control form-control-sm" style="width:80px;font-size:14px;font-weight:700;" value="<?php echo $total_bolsas ?? 0; ?>">
                </div>
                <div class="d-flex align-items-center gap-1">
                  <span class="text-secondary">Anhidro:</span>
                  <input type="text" class="form-control form-control-sm" style="width:80px;font-size:14px;font-weight:700;" value="<?php echo $total_anhidro ?? 0; ?>">
                </div>
              </div>
            </div>