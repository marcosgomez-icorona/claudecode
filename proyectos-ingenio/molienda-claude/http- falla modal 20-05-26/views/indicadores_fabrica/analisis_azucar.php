<?php
  include 'controller/molienda_online.php';
  $hora_actual = date('H').':00';
  $hora = $_POST['hora'] ?? date('H').':00';
  $horas = range(0, 23);

  $fechaindustrial_raw = $_POST['fechaindustrial'] ?? '';
  $fechaindustrial = preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaindustrial_raw) ? $fechaindustrial_raw : date('Y-m-d');

  // Análisis por hora
  $consulta = 'avg_cinta_larga';
  $sql = EjecutarConsulta($consulta, $fechaindustrial, $hora);
  $r = (ObtieneDatosSQL($sql))[0] ?? [];
  $c_larga_avg_color     = $r['color']      ?? '';
  $c_larga_avg_turbidez  = $r['turbidez']   ?? '';
  $c_larga_avg_humedad   = $r['humedad']    ?? '';
  $c_larga_avg_cenizas   = $r['cenizas']    ?? '';
  $c_larga_avg_sedmentos = $r['sedimentos'] ?? '';
  $c_larga_avg_so2       = $r['so2']        ?? '';

  $consulta = 'avg_cinta_corta';
  $sql = EjecutarConsulta($consulta, $fechaindustrial, $hora);
  $r = (ObtieneDatosSQL($sql))[0] ?? [];
  $c_corta_avg_color     = $r['color']      ?? '';
  $c_corta_avg_turbidez  = $r['turbidez']   ?? '';
  $c_corta_avg_humedad   = $r['humedad']    ?? '';
  $c_corta_avg_cenizas   = $r['cenizas']    ?? '';
  $c_corta_avg_sedmentos = $r['sedimentos'] ?? '';
  $c_corta_avg_so2       = $r['so2']        ?? '';

  $consulta = 'avg_embolsado';
  $sql = EjecutarConsulta($consulta, $fechaindustrial, $hora);
  $r = (ObtieneDatosSQL($sql))[0] ?? [];
  $embolsado_avg_color     = $r['color']      ?? '';
  $embolsado_avg_turbidez  = $r['turbidez']   ?? '';
  $embolsado_avg_humedad   = $r['humedad']    ?? '';
  $embolsado_avg_cenizas   = $r['cenizas']    ?? '';
  $embolsado_avg_sedmentos = $r['sedimentos'] ?? '';
  $embolsado_avg_so2       = $r['so2']        ?? '';

  $consulta = 'avg_crudo';
  $sql = EjecutarConsulta($consulta, $fechaindustrial, $hora);
  $r = (ObtieneDatosSQL($sql))[0] ?? [];
  $crudo_avg_color     = $r['color']      ?? '';
  $crudo_avg_turbidez  = $r['turbidez']   ?? '';
  $crudo_avg_humedad   = $r['humedad']    ?? '';
  $crudo_avg_cenizas   = $r['cenizas']    ?? '';
  $crudo_avg_sedmentos = $r['sedimentos'] ?? '';
  $crudo_avg_so2       = $r['so2']        ?? '';

  // Promedios por día
  $consulta = 'avg_cinta_larga_dia';
  $sql = EjecutarConsulta($consulta, $fechaindustrial, '');
  $r = (ObtieneDatosSQL($sql))[0] ?? [];
  $c_larga_avg_color_dia     = $r['color']      ?? '';
  $c_larga_avg_turbidez_dia  = $r['turbidez']   ?? '';
  $c_larga_avg_humedad_dia   = $r['humedad']    ?? '';
  $c_larga_avg_cenizas_dia   = $r['cenizas']    ?? '';
  $c_larga_avg_sedmentos_dia = $r['sedimentos'] ?? '';
  $c_larga_avg_so2_dia       = $r['so2']        ?? '';

  $consulta = 'avg_cinta_corta_dia';
  $sql = EjecutarConsulta($consulta, $fechaindustrial, '');
  $r = (ObtieneDatosSQL($sql))[0] ?? [];
  $c_corta_avg_color_dia     = $r['color']      ?? '';
  $c_corta_avg_turbidez_dia  = $r['turbidez']   ?? '';
  $c_corta_avg_humedad_dia   = $r['humedad']    ?? '';
  $c_corta_avg_cenizas_dia   = $r['cenizas']    ?? '';
  $c_corta_avg_sedmentos_dia = $r['sedimentos'] ?? '';
  $c_corta_avg_so2_dia       = $r['so2']        ?? '';

  $consulta = 'avg_embolsado_dia';
  $sql = EjecutarConsulta($consulta, $fechaindustrial, $hora);
  $r = (ObtieneDatosSQL($sql))[0] ?? [];
  $embolsado_avg_color_dia     = $r['color']      ?? '';
  $embolsado_avg_turbidez_dia  = $r['turbidez']   ?? '';
  $embolsado_avg_humedad_dia   = $r['humedad']    ?? '';
  $embolsado_avg_cenizas_dia   = $r['cenizas']    ?? '';
  $embolsado_avg_sedmentos_dia = $r['sedimentos'] ?? '';
  $embolsado_avg_so2_dia       = $r['so2']        ?? '';

  $consulta = 'avg_crudo_dia';
  $sql = EjecutarConsulta($consulta, $fechaindustrial, $hora);
  $r = (ObtieneDatosSQL($sql))[0] ?? [];
  $crudo_avg_color_dia     = $r['color']      ?? '';
  $crudo_avg_turbidez_dia  = $r['turbidez']   ?? '';
  $crudo_avg_humedad_dia   = $r['humedad']    ?? '';
  $crudo_avg_cenizas_dia   = $r['cenizas']    ?? '';
  $crudo_avg_sedmentos_dia = $r['sedimentos'] ?? '';
  $crudo_avg_so2_dia       = $r['so2']        ?? '';

  // Estado silos
  $consulta = 'estado_silos';
  $sql = EjecutarConsulta($consulta, $fechaindustrial, $hora);
  $row_silos = ObtieneDatosSQL($sql);
?>

<div class="container-fluid p-0" style="font-size:13px;">

  <!-- Selector de hora -->
  <div class="d-flex align-items-center gap-2 mb-2 p-1 bg-light rounded">
    <label class="fw-bold mb-0">Hora:</label>
    <select name="hora" id="hora" class="form-select form-select-sm" style="width:auto;">
      <option value="">Seleccionar hora</option>
      <?php foreach ($horas as $h):
        $horaFormateada = str_pad($h, 2, "0", STR_PAD_LEFT) . ":00";
        $esActual = ($horaFormateada == $hora_actual) ? 'selected' : '';
      ?>
        <option value="<?php echo $horaFormateada; ?>" <?php echo $esActual; ?>>
          <?php echo $horaFormateada; ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <!-- Tablas por hora y por día lado a lado -->
  <div class="row g-2 mb-2">

    <div class="col-6">
      <h6 class="fw-bold text-secondary mb-1" style="font-size:0.8rem;">Análisis por Hora</h6>
      <table class="table table-sm table-bordered table-striped align-middle mb-0 dash-data-table">
        <thead class="table-light text-center">
          <tr>
            <th class="text-start">Parámetro</th>
            <th>C. Corta</th>
            <th>C. Larga</th>
            <th>Embolsado</th>
            <th>Crudo</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>COLOR</td>
            <td><?php echo $c_corta_avg_color; ?></td>
            <td><?php echo $c_larga_avg_color; ?></td>
            <td><?php echo $embolsado_avg_color; ?></td>
            <td><?php echo $crudo_avg_color; ?></td></tr>
          <tr><td>TURBIDEZ</td>
            <td><?php echo $c_corta_avg_turbidez; ?></td>
            <td><?php echo $c_larga_avg_turbidez; ?></td>
            <td><?php echo $embolsado_avg_turbidez; ?></td>
            <td><?php echo $crudo_avg_turbidez; ?></td></tr>
          <tr><td>HUMEDAD %</td>
            <td><?php echo $c_corta_avg_humedad; ?></td>
            <td><?php echo $c_larga_avg_humedad; ?></td>
            <td><?php echo $embolsado_avg_humedad; ?></td>
            <td><?php echo $crudo_avg_humedad; ?></td></tr>
          <tr><td>CENIZAS %</td>
            <td><?php echo $c_corta_avg_cenizas; ?></td>
            <td><?php echo $c_larga_avg_cenizas; ?></td>
            <td><?php echo $embolsado_avg_cenizas; ?></td>
            <td><?php echo $crudo_avg_cenizas; ?></td></tr>
          <tr><td>SEDIMENTO</td>
            <td><?php echo $c_corta_avg_sedmentos; ?></td>
            <td><?php echo $c_larga_avg_sedmentos; ?></td>
            <td><?php echo $embolsado_avg_sedmentos; ?></td>
            <td><?php echo $crudo_avg_sedmentos; ?></td></tr>
          <tr><td>SO2</td>
            <td><?php echo $c_corta_avg_so2; ?></td>
            <td><?php echo $c_larga_avg_so2; ?></td>
            <td><?php echo $embolsado_avg_so2; ?></td>
            <td><?php echo $crudo_avg_so2; ?></td></tr>
        </tbody>
      </table>
    </div>

    <div class="col-6">
      <h6 class="fw-bold text-secondary mb-1" style="font-size:0.8rem;">Promedio por Día</h6>
      <table class="table table-sm table-bordered table-striped align-middle mb-0 dash-data-table">
        <thead class="table-light text-center">
          <tr>
            <th class="text-start">Parámetro</th>
            <th>C. Corta</th>
            <th>C. Larga</th>
            <th>Embolsado</th>
            <th>Crudo</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>COLOR</td>
            <td><?php echo $c_corta_avg_color_dia; ?></td>
            <td><?php echo $c_larga_avg_color_dia; ?></td>
            <td><?php echo $embolsado_avg_color_dia; ?></td>
            <td><?php echo $crudo_avg_color_dia; ?></td></tr>
          <tr><td>TURBIDEZ</td>
            <td><?php echo $c_corta_avg_turbidez_dia; ?></td>
            <td><?php echo $c_larga_avg_turbidez_dia; ?></td>
            <td><?php echo $embolsado_avg_turbidez_dia; ?></td>
            <td><?php echo $crudo_avg_turbidez_dia; ?></td></tr>
          <tr><td>HUMEDAD %</td>
            <td><?php echo $c_corta_avg_humedad_dia; ?></td>
            <td><?php echo $c_larga_avg_humedad_dia; ?></td>
            <td><?php echo $embolsado_avg_humedad_dia; ?></td>
            <td><?php echo $crudo_avg_humedad_dia; ?></td></tr>
          <tr><td>CENIZAS %</td>
            <td><?php echo $c_corta_avg_cenizas_dia; ?></td>
            <td><?php echo $c_larga_avg_cenizas_dia; ?></td>
            <td><?php echo $embolsado_avg_cenizas_dia; ?></td>
            <td><?php echo $crudo_avg_cenizas_dia; ?></td></tr>
          <tr><td>SEDIMENTO</td>
            <td><?php echo $c_corta_avg_sedmentos_dia; ?></td>
            <td><?php echo $c_larga_avg_sedmentos_dia; ?></td>
            <td><?php echo $embolsado_avg_sedmentos_dia; ?></td>
            <td><?php echo $crudo_avg_sedmentos_dia; ?></td></tr>
          <tr><td>SO2</td>
            <td><?php echo $c_corta_avg_so2_dia; ?></td>
            <td><?php echo $c_larga_avg_so2_dia; ?></td>
            <td><?php echo $embolsado_avg_so2_dia; ?></td>
            <td><?php echo $crudo_avg_so2_dia; ?></td></tr>
        </tbody>
      </table>
    </div>

  </div>

  <!-- Silos + Cal/Soda -->
  <div class="row g-2">

    <div class="col-6">
      <h6 class="fw-bold text-secondary mb-1" style="font-size:0.8rem;">Estado Silos</h6>
      <table class="table table-sm table-bordered table-striped align-middle mb-0 dash-data-table">
        <thead class="table-light text-center">
          <tr>
            <th>SILO</th>
            <th>VACÍO</th>
            <th>CALIDAD</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($row_silos as $item): ?>
          <tr>
            <td><?php echo $item['nombre'] ?? ''; ?></td>
            <td><?php echo $item['vacio']  ?? ''; ?></td>
            <td><?php echo $item['calidad'] ?? ''; ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="col-6">
      <h6 class="fw-bold text-secondary mb-1" style="font-size:0.8rem;">Cal / Soda / ART</h6>
      <table class="table table-sm table-bordered table-striped align-middle mb-0 dash-data-table">
        <thead class="table-light text-center">
          <tr>
            <th>CAL %</th>
            <th>SODA %</th>
            <th>ART</th>
          </tr>
        </thead>
        <tbody>
          <?php for ($q = 0; $q < 4; $q++): ?>
          <tr>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <?php endfor; ?>
        </tbody>
      </table>
    </div>

  </div>

</div>
