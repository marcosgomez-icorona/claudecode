<?php
  //include 'controller/molienda_online.php';
  $consulta ='consumo_x_hora';
  $fechaindustrial = $_POST['fechaindustrial'] ?? '2025-10-14';
  //$sql = EjecutarConsulta($consulta,$fechaindustrial,$hora ?? '');
  //$datos = ObtieneDatosSQL($sql);
  $i = 0;
  $parametros_calidad = ['COLOR','TURBIDEZ','HUMEDAD %','CENIZAS %','SEDIMENTO','SO2'];
  $parametros_silos = ['A','B','C','D'];
  //CALIDAD DE AZUCAR
  $consulta ='avg_cinta_larga';
  $fechaindustrial = $_POST['fechaindustrial'] ?? '2025-10-14';
  $sql = EjecutarConsulta($consulta,$fechaindustrial,$hora ?? '');
  //echo $sql;
  $row = ObtieneDatosSQL($sql);
    
    $c_larga_avg_color = $row[0]['color'] ?? '';
    $c_larga_avg_turbidez = $row[0]['turbidez'] ?? '';
    $c_larga_avg_humedad = $row[0]['humedad'] ?? '';
    $c_larga_avg_cenizas = $row[0]['cenizas'] ?? '';
    $c_larga_avg_sedmentos = $row[0]['sedimentos'] ?? '';
    $c_larga_avg_so2 = $row[0]['so2'] ?? '';

  $consulta ='avg_cinta_corta';
  $fechaindustrial = $_POST['fechaindustrial'] ?? '2025-10-10';
  $sql = EjecutarConsulta($consulta,$fechaindustrial,$hora ?? '');
  //echo $sql;
  $row = ObtieneDatosSQL($sql);
  
    $c_corta_avg_color = $row[0]['color'] ?? '';
    $c_corta_avg_turbidez = $row[0]['turbidez'] ?? '';
    $c_corta_avg_humedad = $row[0]['humedad'] ?? '';
    $c_corta_avg_cenizas = $row[0]['cenizas'] ?? '';
    $c_corta_avg_sedmentos = $row[0]['sedimentos'] ?? '';
    $c_corta_avg_so2 = $row[0]['so2'] ?? '';

    $consulta ='avg_embolsado';
    $fechaindustrial = $_POST['fechaindustrial'] ?? '2025-10-14';
    $sql = EjecutarConsulta($consulta,$fechaindustrial,$hora ?? '');
    //echo $sql;
    $row = ObtieneDatosSQL($sql);
    
      $embolsado_avg_color = $row[0]['color'] ?? '';
      $embolsado_avg_turbidez = $row[0]['turbidez'] ?? '';
      $embolsado_avg_humedad = $row[0]['humedad'] ?? '';
      $embolsado_avg_cenizas = $row[0]['cenizas'] ?? '';
      $embolsado_avg_sedmentos = $row[0]['sedimentos'] ?? '';
      $embolsado_avg_so2 = $row[0]['so2'] ?? '';

    $consulta ='avg_crudo';
    $fechaindustrial = $_POST['fechaindustrial'] ?? '2025-10-14';
    $sql = EjecutarConsulta($consulta,$fechaindustrial,$hora ?? '');
    //echo $sql;
    $row = ObtieneDatosSQL($sql);
      
        $crudo_avg_color = $row[0]['color'] ?? '';
        $crudo_avg_turbidez = $row[0]['turbidez'] ?? '';
        $crudo_avg_humedad = $row[0]['humedad'] ?? '';
        $crudo_avg_cenizas = $row[0]['cenizas'] ?? '';
        $crudo_avg_sedmentos = $row[0]['sedimentos'] ?? '';
        $crudo_avg_so2 = $row[0]['so2'] ?? '';

    $consulta ='estado_silos';
    $fechaindustrial = $_POST['fechaindustrial'] ?? '2025-10-14';
    $sql = EjecutarConsulta($consulta,$fechaindustrial,$hora ?? '');
    //echo $sql;
    $row = ObtieneDatosSQL($sql);
          
            $estado_silos_nombre = $row[0]['nombre'] ?? '';
            $estado_silos_vacio = $row[0]['vacio'] ?? '';
            $estado_silos_calidad = $row[0]['calidad'] ?? '';
            
             
?>
<div class="card p-3 top-card">              
              <div class="row">
                <div class="col-12">
                  <div class="table-responsive small">
                    <table id="calidad" class="table table-striped table-bordered table-sm align-middle mb-0">
                      <thead class="table-light text-center text-dark">
                        <tr class="text-start">
                          <th>Promedios</th>
                          <th>Cinta Corta</th>
                          <th>Cinta Larga</th>
                          <th>Embolsado</th>
                          <th>Crudo</th>                                               
                        </tr>
                      </thead>
                      <tbody>                       
                        <tr>
                            <td class="text-start"> <strong><?php echo 'COLOR' ?></strong></td>
                            <td class="text-start"><?php echo $c_corta_avg_color; ?></td>
                            <td class="text-start"><?php echo $c_larga_avg_color; ?></td>
                            <td class="text-start"><?php echo $embolsado_avg_color; ?></td>
                            <td class="text-start"><?php echo $crudo_avg_color; ?></td>                            
                        </tr> 
                        <tr>
                            <td class="text-start"> <strong><?php echo 'TURBIDEZ' ?></strong></td>
                            <td class="text-start"><?php echo $c_corta_avg_turbidez; ?></td>
                            <td class="text-start"><?php echo $c_larga_avg_turbidez; ?></td>
                            <td class="text-start"><?php echo $embolsado_avg_turbidez; ?></td>
                            <td class="text-start"><?php echo $crudo_avg_turbidez; ?></td>                            
                        </tr> 
                        <tr>
                            <td class="text-start"> <strong><?php echo 'HUMEDAD %' ?></strong></td>
                            <td class="text-start"><?php echo $c_corta_avg_humedad; ?></td>
                            <td class="text-start"><?php echo $c_larga_avg_humedad; ?></td>
                            <td class="text-start"><?php echo $embolsado_avg_humedad; ?></td>
                            <td class="text-start"><?php echo $crudo_avg_humedad; ?></td>                            
                        </tr> 
                        <tr>
                            <td class="text-start"> <strong><?php echo 'CENIZAS %' ?></strong></td>
                            <td class="text-start"><?php echo $c_corta_avg_cenizas; ?></td>
                            <td class="text-start"><?php echo $c_larga_avg_cenizas; ?></td>
                            <td class="text-start"><?php echo $embolsado_avg_cenizas; ?></td>
                            <td class="text-start"><?php echo $crudo_avg_cenizas; ?></td>                            
                        </tr> 
                        <tr>
                            <td class="text-start"> <strong><?php echo 'SEDIMENTO' ?></strong></td>
                            <td class="text-start"><?php echo $c_corta_avg_sedmentos; ?></td>
                            <td class="text-start"><?php echo $c_larga_avg_sedmentos; ?></td>
                            <td class="text-start"><?php echo $embolsado_avg_sedmentos; ?></td>
                            <td class="text-start"><?php echo $crudo_avg_sedmentos; ?></td>                            
                        </tr> 
                        <tr>
                            <td class="text-start"> <strong><?php echo 'SO2' ?></strong></td>
                            <td class="text-start"><?php echo $c_corta_avg_so2; ?></td>
                            <td class="text-start"><?php echo $c_larga_avg_so2; ?></td>
                            <td class="text-start"><?php echo $embolsado_avg_so2; ?></td>
                            <td class="text-start"><?php echo $crudo_avg_so2; ?></td>                            
                        </tr>                        
                      </tbody>
                    </table>
                  </div>
                </div>
              </div> 
              <p></p>  
              <div class="row ">
                <div class="col-6">
                  <div class="table-responsive small">
                    <table class="table table-striped table-bordered table-sm align-middle mb-0">
                      <thead class="table-light text-center text-dark">
                        <tr class="text-start">
                          <th>SILO</th>
                          <th>VACIO</th>
                          <th>CALIDAD</th>                          
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($row as $item): ?>
                        <tr>
                          <td class="text-start"><?php echo $item['nombre'] ?? ''; ?></td>
                          <td class="text-start"><?php echo $item['vacio'] ?? ''; ?></td>
                          <td class="text-start"><?php echo $item['calidad'] ?? ''; ?></td>
                        </tr>   
                        <?php                                                            
                            endforeach; ?>                     
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-6">
                  <div class="table-responsive small">
                    <table class="table table-striped table-bordered table-sm align-middle mb-0">
                      <thead class="table-light text-center text-dark">
                        <tr class="text-start">
                          <th>CAL %</th>
                          <th>SODA %</th>
                          <th>ART</th>                          
                        </tr>
                      </thead>
                      <tbody>
                        <?php for ($q=0;$q<4;$q++): ?>
                        <tr>
                            <td class="text-start"><?php echo ''; ?></td>
                            <td class="text-start"><?php echo ''; ?></td>
                            <td class="text-start"><?php echo ''; ?></td>                            
                        </tr>   
                        <?php                               
                              
                            endfor; ?>                     
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>              
            </div>