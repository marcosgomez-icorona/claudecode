  <?php
    include 'controller/molienda_online.php';

    $acumulado = obtiene_molienda_acumulada();
    $acumulado_cania = number_format((float)$acumulado['molienda_acumulada'], 0, ',', '.') ?? 0;
    $ultima_actualizacion = fechaDma($acumulado['ultima_actualizacion']) ?? '';

    /*MOLIENDA ACTUAL */    
    $acumulado_molienda_dia = obtiene_acumulado_molienda_dia('hoy');
    $cania_bruta =  number_format((float)$acumulado_molienda_dia['cania_bruta'], 0, ',', '.') ?? 0;
    $trash_ponderado =  number_format((float)$acumulado_molienda_dia['trash_ponderado'], 2, ',', '.') ?? 0;
    $trash_kg =  number_format((float)$acumulado_molienda_dia['trash_kg'], 0, ',', '.');
    $cania_neta =  number_format((float)$acumulado_molienda_dia['cania_neta'], 0, ',', '.');
    $rdto_ponderado =  number_format((float)$acumulado_molienda_dia['rdto_ponderado'], 2, ',', '.') ?? 0;
    $pol_ponderado =  number_format((float)$acumulado_molienda_dia['pol_ponderado'], 2, ',', '.') ?? 0;
    $brix_ponderado =  number_format((float)$acumulado_molienda_dia['brix_ponderado'], 2, ',', '.') ?? 0;
    $pureza_ponderada =  number_format((float)$acumulado_molienda_dia['pureza_ponderada'], 2, ',', '.') ?? 0;

    /*MOLIENDA ULTIMO CIERRE */
    $acumulado_molienda_uc = obtiene_acumulado_molienda_dia('ultimo_cierre');
    $cania_bruta_uc =  number_format((float)$acumulado_molienda_uc['cania_bruta'], 0, ',', '.') ?? 0;
    $trash_ponderado_uc =  number_format((float)$acumulado_molienda_uc['trash_ponderado'], 2, ',', '.') ?? 0;
    //$trash_kg_uc =  number_format((float)$acumulado_molienda_uc['trash_kg'], 0, ',', '.');
    $cania_neta_uc =  number_format((float)$acumulado_molienda_uc['cania_neta'], 0, ',', '.');
    $rdto_ponderado_uc =  number_format((float)$acumulado_molienda_uc['rdto_ponderado'], 2, ',', '.') ?? 0;
    $pol_ponderado_uc =  number_format((float)$acumulado_molienda_uc['pol_ponderado'], 2, ',', '.') ?? 0;
    $brix_ponderado_uc =  number_format((float)$acumulado_molienda_uc['brix_ponderado'], 2, ',', '.') ?? 0;
    $pureza_ponderada_uc =  number_format((float)$acumulado_molienda_uc['pureza_ponderada'], 2, ',', '.') ?? 0;
    
  ?>
  <div class="row">
        <h4 class="text-center my-3">Molienda Online</h4>
  </div>
  <div class="container-fluid panel-container">
    <div class="card m-1">      
      <div class="row">
          <!-- Columna izquierda 70% -->
          <div class="col-lg-7 left-col">
            <div class="row">
              <div class="col-12 text-start">
                <div class="card p-3 ">                  
                  <div class="row bg-light text-start rounded shadow">
                    <div class="col-4 fw-bold">
                      Fecha:<span class="ms-1"><?php echo date('d/m/Y');?></span> 
                    </div>
                    <div class="col-4 " id="hora_molienda_online">
                      Molienda Online
                    </div>
                    <div class="col-4 text-end fw-bold ">
                      Ultima Actualizacion:<span class="ms-1"><?php echo $ultima_actualizacion;?></span>
                    </div>                    
                  </div>
                  <div class="row bg-light text-start rounded shadow">
                    <div class="col-6 text-start">
                      <strong>
                        Molienda Zafra: <span class="ms-1"><?php echo $zafra ?? date('Y');?></span>
                        <span class="ms-1">Acumulado:</span>
                        <span class="ms-1"><?php echo $acumulado_cania;?></span>
                      </strong>                      
                    </div>
                    <div class="col-5 text-center m-1">
                      <button class="btn btn-info btn-sm text-white ms-3" data-bs-toggle="modal" data-bs-target="#indicadoresModal">Indicadores de Fabrica</button>
                    </div>
                  </div>                  
                  <div class="row bg-light text-start rounded shadow">
                    <div class="col m-1 form-control ">
                        <label class="fw-bold ">Caña Bruta</label>
                        <div class="text-start"><input type="text" class="form-control" value="<?php echo $cania_bruta ?? 0;?>"></div>
                        
                    </div>
                    <div class="col m-1 form-control">
                        <label class="fw-bold text-dark">Trash Pon/Kilos</label>                        
                        <div class="text-start"><input type="text" class="form-control" value="<?php echo $trash_ponderado ?? 0;?>"></div>
                        <div class="text-start"><input type="text" class="form-control" value="<?php echo $trash_kg ?? 0;?>"></div>
                    </div>
                    <div class="col m-1 form-control">
                        <label class="fw-bold text-dark">Caña Neta</label>
                        <div class="text-start"><input type="text" class="form-control" value="<?php echo $cania_neta ?? 0;?>"></div>
                    </div>
                    <div class="col m-1 form-control">
                        <label class="fw-bold text-dark">Rto. Ponderado</label>
                        <div class="text-start"><input type="text" class="form-control" value="<?php echo $rdto_ponderado ?? 0;?>"></div>
                    </div>
                    <div class="col m-1 form-control">
                        <label class="fw-bold text-dark">Brix Ponderado</label>
                        <div class="text-start"><input type="text" class="form-control" value="<?php echo $brix_ponderado ?? 0;?>"></div>
                    </div>
                    <div class="col m-1 form-control">
                        <label class="fw-bold text-dark">Pol Ponderado</label>
                        <div class="text-start"><input type="text" class="form-control" value="<?php echo $pol_ponderado ?? 0;?>"></div>
                    </div>
                    <div class="col m-1 form-control">
                        <label class="fw-bold text-dark">Pureza Pond.</label>
                        <div class="text-start"><input type="text" class="form-control" value="<?php echo $pureza_ponderada ?? 0;?>"></div>
                    </div>                                      
                  </div>
                </div>                      
                <div class="card p-3">
                  <h4 class="text-secondary text-center text-justify fw-bold">Ultimo Cierre</h4>
                  <div class="row bg-light text-start rounded shadow">
                    <div class="col m-1 form-control">
                        <label class="fw-bold text-dark">Caña Bruta</label>
                        <div class="text-start"><input type="text" class="form-control" value="<?php echo $cania_bruta_uc ?? 0;?>"></div>
                    </div>
                    <div class="col m-1 form-control">
                        <label class="fw-bold text-dark">Trash Pond.</label>                        
                        <div class="text-start"><input type="text" class="form-control" value="<?php echo $trash_ponderado_uc ?? 0;?>"></div>                        
                    </div>
                    <div class="col m-1 form-control">
                        <label class="fw-bold text-dark">Caña Neta</label>
                        <div class="text-start"><input type="text" class="form-control" value="<?php echo $cania_neta_uc ?? 0;?>"></div>
                    </div>
                    <div class="col m-1 form-control">
                        <label class="fw-bold text-dark">Rto. Ponderado</label>
                        <div class="text-start"><input type="text" class="form-control" value="<?php echo $rdto_ponderado_uc ?? 0;?>"></div>
                    </div>
                    <div class="col m-1 form-control">
                        <label class="fw-bold text-dark">Brix Ponderado</label>
                        <div class="text-start"><input type="text" class="form-control" value="<?php echo $brix_ponderado_uc ?? 0;?>"></div>
                    </div>
                    <div class="col m-1 form-control">
                        <label class="fw-bold text-dark">Pol Ponderado</label>
                        <div class="text-start"><input type="text" class="form-control" value="<?php echo $pol_ponderado_uc ?? 0;?>"></div>
                    </div>
                    <div class="col m-1 form-control">
                        <label class="fw-bold text-dark">Pureza Pond.</label>
                        <div class="text-start"><input type="text" class="form-control" value="<?php echo $pureza_ponderada_uc ?? 0;?>"></div>
                    </div>                                      
                  </div>
                </div>
                <?php include 'detalle_cania.php';?>
              </div>
            </div>            
          </div>
          <!-- Columna derecha 30% -->
          <div class="col-lg-5 right-col">
            <?php include 'datos_molienda_hora.php';?>
            <div class="card p-3 bottom-card">
              <h5 class="card-title">En Canchon</h5>
              <div class="row my-2">
                <div class="col" >
                  <div class="d-flex align-items-center text-start">
                    <span class="ms-1 me-2 fw-bold">Camiones:</span>
                    <input type="text" class="form-control w-auto" 
                          value="<?php echo $cant_camiones ?? 0; ?>">
                  </div>                  
                </div>
                <div class="col">
                  <button class="btn btn-success btn-lg text-white ms-3"></button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 text-start">
            <div class="card p-3 ">
              <div class="row bg-light text-start rounded shadow ">
                <div class="col-1">
                  <button class="btn btn-secondary text-white" data-bs-toggle="modal" data-bs-target="#paradasModal">
                    Paradas
                  </button>
                </div>
                <div class="col m-1">
                  <div class="d-flex align-items-center">
                    <span class="ms-1 me-2 fw-bold">Cantidad:</span>
                    <input type="text" class="form-control w-auto" 
                          value="<?php echo $cantidad ?? 0; ?>">
                  </div>                  
                </div>
                <div class="col" >
                  <div class="d-flex align-items-center">
                    <span class="ms-1 me-2 fw-bold">Ultima Pesada Hr:</span>
                    <input type="text" class="form-control w-auto" 
                          value="<?php echo $hora_ultima_pesada ?? 0; ?>">
                  </div>                  
                </div>
                <div class="col" >
                  <div class="d-flex align-items-center">
                    <span class="ms-1 me-2 fw-bold">Pol Prom. Bagazo:</span>
                    <input type="text" class="form-control w-auto" 
                          value="<?php echo $pol_prom_bagazo ?? 0; ?>">
                  </div>                  
                </div>
                <div class="col" >
                  <div class="d-flex align-items-center">
                    <span class="ms-1 me-2 fw-bold">Pol Prom. Cachaza:</span>
                    <input type="text" class="form-control w-auto" 
                          value="<?php echo $pol_prom_cachaza ?? 0; ?>">
                  </div>                  
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </div>
  
  <!-- Modals -->
  <div class="modal fade" id="paradasModal" tabindex="-1" aria-labelledby="paradasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> 
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="paradasModalLabel">Paradas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <?php include 'paradas.php'; ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="indicadoresModal" tabindex="-1" aria-labelledby="indicadoresModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> 
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="indicadoresModalLabel">Indicadores de Fabrica</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <?php include 'views/indicadores_fabrica/index.php'; ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>


  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
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

    actualizarHora();
    setInterval(actualizarHora, 1000);
  </script>
