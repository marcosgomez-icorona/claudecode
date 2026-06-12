<?php
  include 'controller/molienda_online.php';
  $consulta ='consumo_x_hora';
  $hora = $_POST['hora'] ?? '';
  $fechaindustrial_raw = $_POST['fechaindustrial'] ?? '';
  $fechaindustrial = preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaindustrial_raw) ? $fechaindustrial_raw : date('Y-m-d');
  $sql = EjecutarConsulta($consulta, $fechaindustrial, $hora);
  $datos = ObtieneDatosSQL($sql);
  $gas_acumulado = 0;
  $total_bolsas =  0; 
  $total_anhidro = 0;
?>
<div class="card p-3 top-card">
              <h5 class="card-title">Produccion, Consumos y Perdidas</h5>
              <div class="row">
                <div class="col-12">
                  <div class="table-responsive small">
                    <table class="table table-striped table-bordered table-sm align-middle mb-0">
                      <thead class="table-light text-center text-dark">
                        <tr class="text-start">
                          <th>Fecha</th>
                          <th>Hora</th>
                          <th>Gas</th>
                          <th>Molienda</th>
                          <th>Humedad</th>
                          <th>Bolsas</th>                                                                            
                          <th>Anhidro</th>
                          <th>Pol Cachaza</th>
                          <th>Pol Bagazo</th>
                          <th>Jugo Destileria</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($datos as $item): ?>
                        <tr>
                            <td class="text-start"><?php echo fechaDma($item['fechaindustrial'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars(rango_hora($item['hora'] ?? '') ); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($item['gas'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($item['kilos'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($item['humedad'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($item['bolsas'] ?? ''); ?></td>                                                       
                            <td class="text-start"><?php echo htmlspecialchars($item['anhidro'] ?? ''); ?></td>                                                       
                            <td class="text-start"><?php echo ''; ?></td>                                                       
                            <td class="text-start"><?php echo ''; ?></td>                                                       
                            <td class="text-start"><?php echo ''; ?></td>                                                                                   
                        </tr>   
                        <?php
                              $gas_acumulado       = $gas_acumulado + ($item['gas']    ?? 0);
                              $total_bolsas        = $total_bolsas  + ($item['bolsas'] ?? 0);
                              $total_anhidro       = 0;
                              $total_jugo_destileria = 0;
                            endforeach; ?>                     
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="row my-2">
                <div class="col" >
                  <div class="row mb-2 align-items-center">
                    <div class="col ms-1 me-2 fw-bold ">Gas acummulado:</div>
                    <div class="col ">
                        <input type="text" class="form-control form-control-sm " 
                            value="<?php echo $gas_acumulado ?? 0; ?>">
                    </div>
                  </div>
                </div>                                                   
                <div class="col" >
                  <div class="row mb-2 align-items-center">
                    <div class="col ms-1 me-2 fw-bold ">Total Bolsas:</div>
                    <div class="col ">
                        <input type="text" class="form-control form-control-sm " 
                            value="<?php echo $total_bolsas ?? 0; ?>">
                    </div>
                  </div>
                </div>
                <div class="col" >
                  <div class="row mb-2 align-items-center">
                    <div class="col ms-1 me-2 fw-bold ">Total Anhidro:</div>
                    <div class="col ">
                        <input type="text" class="form-control form-control-sm " 
                            value="<?php echo $total_anhidro ?? 0; ?>">                            
                    </div>
                  </div>
                </div>
                <div class="col" >
                  <div class="row mb-2 align-items-center">
                    <div class="col ms-1 me-2 fw-bold ">Total Jugo Destileria:</div>
                    <div class="col ">
                        <input type="text" class="form-control form-control-sm " 
                            value="<?php echo $total_jugo_destileria ?? 0; ?>">                            
                    </div>
                  </div>
                </div>
                
              </div>
            </div>