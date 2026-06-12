<?php
$consulta = 'resumen_fabrica_promedios';
$fechaindustrial = $_POST['fechaindustrial'] ?? '2025-10-14';
$sql = EjecutarConsulta($consulta, $fechaindustrial, $hora ?? '');
$promedios = ObtieneDatosSQL($sql);

$consulta = 'resumen_fabrica_totales';
$sql = EjecutarConsulta($consulta, $fechaindustrial, $hora ?? '');
$totales = ObtieneDatosSQL($sql);

$consulta = 'sulfitado';
$sql = EjecutarConsulta($consulta, $fechaindustrial, $hora ?? '');
$sulfitado = ObtieneDatosSQL($sql);
?>

<div class="row g-2" style="font-size:13px;">

  <!-- Columna izquierda: promedios -->
  <div class="col-7">
    <h6 class="fw-bold text-secondary mb-1" style="font-size:0.8rem;">Promedios por Proceso</h6>
    <table class="table table-sm table-bordered table-striped align-middle mb-0 dash-data-table">
      <thead class="table-light text-center">
        <tr>
          <th class="text-start">Proceso</th>
          <th>Brix</th>
          <th>Pol</th>
          <th>Pureza</th>
          <th>Ph</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($promedios as $item): ?>
        <tr>
          <td class="text-start fw-semibold"><?php echo $item['codigoproceso'] ?? ''; ?></td>
          <td class="text-center"><?php echo $item['brix']   ?? ''; ?></td>
          <td class="text-center"><?php echo $item['pol']    ?? ''; ?></td>
          <td class="text-center"><?php echo $item['pureza'] ?? ''; ?></td>
          <td class="text-center"><?php echo $item['ph']     ?? ''; ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Columna derecha: totales + sulfitado -->
  <div class="col-5 d-flex flex-column gap-2">

    <!-- Totales -->
    <div>
      <h6 class="fw-bold text-secondary mb-1" style="font-size:0.8rem;">Totales</h6>
      <table class="table table-sm table-bordered table-striped align-middle mb-0 dash-data-table">
        <thead class="table-light">
          <tr>
            <th class="text-start">Proceso</th>
            <th class="text-center">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($totales as $item): ?>
          <tr>
            <td class="text-start fw-semibold"><?php echo $item['codigoproceso'] ?? ''; ?></td>
            <td class="text-center"><?php echo $item['total'] ?? ''; ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Jugo Sulfitado -->
    <div>
      <h6 class="fw-bold text-secondary mb-1" style="font-size:0.8rem;">Jugo Sulfitado</h6>
      <table class="table table-sm table-bordered table-striped align-middle mb-0 dash-data-table">
        <thead class="table-light">
          <tr>
            <th class="text-start">Parámetro</th>
            <th class="text-center">Valor</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($sulfitado as $item): ?>
          <tr>
            <td class="text-start fw-semibold"><?php echo $item['codigoproceso'] ?? ''; ?></td>
            <td class="text-center"><?php echo $item['ph'] ?? ''; ?></td>
          </tr>
          <?php if (($item['PPMS02manual'] ?? 0) > 0): ?>
          <tr>
            <td class="text-start">mg. SO2/1</td>
            <td class="text-center"><?php echo $item['PPMS02manual']; ?></td>
          </tr>
          <?php endif; ?>
          <?php endforeach; ?>
          <tr><td class="text-start">Hlts. MC 1ra</td><td class="text-center">—</td></tr>
          <tr><td class="text-start">Hlts. MC 2da</td><td class="text-center">—</td></tr>
          <tr><td class="text-start">Cal</td><td class="text-center">—</td></tr>
          <tr><td class="text-start">Azufre</td><td class="text-center">—</td></tr>
        </tbody>
      </table>
    </div>

  </div>
</div>
