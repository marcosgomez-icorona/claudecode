<?php
  
  $consulta ='resumen_fabrica_promedios';
  $fechaindustrial = $_POST['fechaindustrial'] ?? '2025-10-14';
  
  $fechaindustrial = $_POST['fechaindustrial'] ?? '2025-10-14';
  $sql = EjecutarConsulta($consulta,$fechaindustrial,$hora ?? '');
  //echo $sql;
  $promedios = ObtieneDatosSQL($sql);

  $consulta ='resumen_fabrica_totales';
  $fechaindustrial = $_POST['fechaindustrial'] ?? '2025-10-14';
  
  $fechaindustrial = $_POST['fechaindustrial'] ?? '2025-10-14';
  $sql = EjecutarConsulta($consulta,$fechaindustrial,$hora ?? '');
  //echo $sql;
  $row = ObtieneDatosSQL($sql);

  $i = 0;
  $parametros_resumen1 = ['J. MIXTO','J. PRIM. PRES.','J. ULT. PRES.','J. CLARIFIC.','MELADO S/T',
                            'JARABE CLARIF.','MIEL RICA DE 1ra','MIEL PORBRE DE 1ra','MIEL MIXTA DE 2da',
                            'JARABE DE 1ra','MAGMA DE 3ra','AZUCAR DE 3ra','MC 1ra','MC 2da','MC 3ra',
                            'MELAZA','JARABE DE 3ra'];
  
?>
<div class="card p-3 top-card">
              
              <div class="row">
                <div class="col-7">
                  <div class="table-responsive small">
                    <table class="table table-striped table-bordered table-sm align-middle mb-0">
                      <thead class="table-light text-center text-dark">
                        <tr class="text-start">
                          <th>Promedios</th>
                          <th>Brix</th>
                          <th>Pol</th>
                          <th>Pza</th>
                          <th>Ph</th>                                               
                        </tr>
                      </thead>
                      <tbody>
                      <tbody>
                        <?php foreach ($promedios as $item): ?>
                        <tr>
                          <td class="text-start"><?php echo $item['codigoproceso'] ?? ''; ?></td>
                          <td class="text-start"><?php echo $item['brix'] ?? ''; ?></td>
                          <td class="text-start"><?php echo $item['pol'] ?? ''; ?></td>
                          <td class="text-start"><?php echo $item['pureza'] ?? ''; ?></td>
                          <td class="text-start"><?php echo $item['ph'] ?? ''; ?></td>
                        </tr>   
                        <?php                                                            
                            endforeach; ?>                     
                      </tbody>              
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-5">                  
                    <div class="row bg-light text-start rounded shadow">                       
                      <div class="row mb-1 align-items-center">
                        <div class="col  ">Molienda:KG. JGO. MIXTO</div>
                        <div class="col ">
                          <input type="text" class="form-control form-control-sm " 
                                value="<?php echo $molienda ?? 0; ?>">
                        </div>
                      </div>
                      <div class="row mb-2 align-items-center">
                        <div class="col ">MOLIENDA</div>
                        <div class="col ">
                          <input type="text" class="form-control form-control-sm " 
                                value="<?php echo $pol ?? 0; ?>">
                        </div>
                      </div>
                      <div class="row mb-2 align-items-center">
                        <div class="col ">MELAZA CONSUMO</div>
                        <div class="col ">
                          <input type="text" class="form-control form-control-sm " 
                                value="<?php echo $veloc_1er_molino ?? 0; ?>">
                        </div>
                      </div>
                      <div class="row mb-2 align-items-center">
                        <div class="col ">MELAZA FABRICA</div>
                        <div class="col ">
                          <input type="text" class="form-control form-control-sm " 
                                value="<?php echo $veloc_1er_molino ?? 0; ?>">
                        </div>
                      </div>
                      <div class="row mb-2 align-items-center">
                        <div class="col ">AGUA IMBIB.</div>
                        <div class="col ">
                          <input type="text" class="form-control form-control-sm " 
                                value="<?php echo $veloc_1er_molino ?? 0; ?>">
                        </div>
                      </div>
                      <div class="row mb-2 align-items-center">
                        <div class="col ">GAS</div>
                        <div class="col ">
                          <input type="text" class="form-control form-control-sm " 
                                value="<?php echo $veloc_1er_molino ?? 0; ?>">
                        </div>
                      </div>
                      <div class="row mb-2 align-items-center">
                        <div class="col ">GAS GASNOR</div>
                        <div class="col ">
                          <input type="text" class="form-control form-control-sm " 
                                value="<?php echo $veloc_1er_molino ?? 0; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="row bg-light text-start rounded shadow">  
                      <h5 class="card-title">JUGO SULFITADO</h5>
                      <div class="row mb-2 align-items-center">
                        <div class="col  ">PH</div>
                        <div class="col ">
                          <input type="text" class="form-control form-control-sm " 
                                value="<?php echo $molienda ?? 0; ?>">
                        </div>
                      </div>
                      <div class="row mb-2 align-items-center">
                        <div class="col ">mg. SO2/1</div>
                        <div class="col ">
                          <input type="text" class="form-control form-control-sm " 
                                value="<?php echo $pol ?? 0; ?>">
                        </div>
                      </div>
                      <div class="row mb-2 align-items-center">
                        <div class="col ">JUGO ENCALADO PH</div>
                        <div class="col ">
                          <input type="text" class="form-control form-control-sm " 
                                value="<?php echo $veloc_1er_molino ?? 0; ?>">
                        </div>
                      </div>
                      <div class="row mb-2 align-items-center">
                        <div class="col ">HLTS. MASA COCIDA 1ra</div>
                        <div class="col ">
                          <input type="text" class="form-control form-control-sm " 
                                value="<?php echo $veloc_1er_molino ?? 0; ?>">
                        </div>
                      </div>
                      <div class="row mb-2 align-items-center">
                        <div class="col ">HLTS. MASA COCIDA 2da</div>
                        <div class="col ">
                          <input type="text" class="form-control form-control-sm " 
                                value="<?php echo $veloc_1er_molino ?? 0; ?>">
                        </div>
                      </div>
                      <div class="row mb-2 align-items-center">
                        <div class="col ">CAL</div>
                        <div class="col ">
                          <input type="text" class="form-control form-control-sm " 
                                value="<?php echo $veloc_1er_molino ?? 0; ?>">
                        </div>
                      </div>
                      <div class="row mb-2 align-items-center">
                        <div class="col ">AZUFRE</div>
                        <div class="col ">
                          <input type="text" class="form-control form-control-sm " 
                                value="<?php echo $veloc_1er_molino ?? 0; ?>">
                        </div>
                      </div>                      
                      </div>
                    </div>
                </div>
              </div> 
              