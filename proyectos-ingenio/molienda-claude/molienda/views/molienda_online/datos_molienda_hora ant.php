<?php
    if (!function_exists('fmt_num')) {
        function fmt_num($v, $d = 0) {
            if ($v === '' || $v === null || $v === '--') return '--';
            if (!is_numeric($v)) return htmlspecialchars((string)$v);
            return number_format((float)$v, $d, ',', '.');
        }
    }

    $consulta = 'consumo_x_hora';
    $fechaindustrial = $_POST['fechaindustrial'] ?? date('Y-m-d');
    $sql = EjecutarConsulta($consulta, $fechaindustrial, $hora ?? '');
    // echo '<script>console.log("'.$sql.'");</script>';
    $datos_raw = ObtieneDatosSQL($sql) ?? [];

    // Indexar datos por hora de cierre (HH:MM)
    $por_hora = [];
    foreach ($datos_raw as $row) {
        $h = substr($row['hora'] ?? '', 0, 5);
        $por_hora[$h] = $row;
    }

    // Slots del día industrial: la hora almacenada es la de CIERRE del período.
    // Día industrial 07:00–07:00: primer slot es 08:00 (período 07–08), último es 07:00 (período 06–07)
    $slots_dia = [
        '08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00',
        '16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00',
        '00:00','01:00','02:00','03:00','04:00','05:00','06:00','07:00'
    ];

    // Hora de cierre del período en curso (Argentina)
    $h_actual    = (int)date('G');   // hora actual 0–23
    $slot_actual = sprintf('%02d:00', ($h_actual + 1) % 24);

    // Para días distintos al actual mostramos todos los slots sin filtro
    $es_hoy = ($fechaindustrial === date('Y-m-d'));

    // Cuando es hoy: mostrar solo hasta el slot actual (inclusive)
    if ($es_hoy) {
        $idx_actual   = array_search($slot_actual, $slots_dia);
        $slots_mostrar = ($idx_actual !== false)
            ? array_slice($slots_dia, 0, $idx_actual + 1)
            : $slots_dia;
    } else {
        $slots_mostrar = $slots_dia;
    }

    // Kilos acumulados en la hora en curso, calculados desde datos_Cania (no de consumos_x_hora que solo tiene cierres)
    $kilos_parciales_actuales = (isset($molienda_en_curso) && $molienda_en_curso > 0)
        ? $molienda_en_curso
        : null;

    $gas_acumulado = 0;
    $total_bolsas  = 0;
    $total_anhidro = 0;
?>
<div>
  <div class="d-flex align-items-center flex-wrap gap-2 mb-1 px-1 py-1 rounded"
       style="background:rgba(108,117,125,0.07); font-size:0.78rem; font-weight:600;">
    <span class="ind-section-title me-1" style="border-bottom:none; margin-bottom:0; padding-bottom:0;">Prom. T. ant.:</span>
    <span class="text-secondary">Gas:</span>
    <span class="text-dark"><?php echo fmt_num($prom_turno_ant['gas'] ?? '--', 1); ?></span>
    <span class="text-muted">|</span>
    <span class="text-secondary">Molienda:</span>
    <span class="text-dark"><?php echo fmt_num($prom_turno_ant['kilos'] ?? '--', 0); ?></span>
    <span class="text-muted">|</span>
    <span class="text-secondary">Humedad:</span>
    <span class="text-dark"><?php echo fmt_num($prom_turno_ant['humedad'] ?? '--', 2); ?></span>
    <span class="text-muted">|</span>
    <span class="text-secondary">Bolsas:</span>
    <span class="text-dark"><?php echo fmt_num($prom_turno_ant['bolsas'] ?? '--', 0); ?></span>
    <span class="text-muted">|</span>
    <span class="text-secondary">Pol Baz.:</span>
    <span class="text-dark"><?php echo fmt_num($prom_turno_ant['pol_bagazo'] ?? '--', 2); ?></span>
    <span class="text-muted">|</span>
    <span class="text-secondary">Pol Cach.:</span>
    <span class="text-dark"><?php echo fmt_num($prom_turno_ant['pol_cachaza'] ?? '--', 2); ?></span>
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
            <?php foreach ($slots_mostrar as $slot):
                $row        = $por_hora[$slot] ?? null;
                $tiene_dato = $row !== null;

                // En el día actual, marcar el slot en curso
                $es_slot_actual = $es_hoy && ($slot === $slot_actual);

                // Si es el slot actual y no hay dato cerrado, mostrar kilos parciales en cursiva
                $kilos_parcial = ($es_slot_actual && !$tiene_dato && $kilos_parciales_actuales !== null)
                    ? $kilos_parciales_actuales
                    : null;

                // Acumular totales solo cuando hay dato cerrado
                if ($tiene_dato) {
                    $gas_acumulado += (float)($row['gas']    ?? 0);
                    $total_bolsas  += (int)  ($row['bolsas'] ?? 0);
                }
            ?>
            <tr<?php echo $es_slot_actual ? ' class="table-info fw-bold"' : ''; ?>>
              <td class="text-start">
                <?php echo $tiene_dato
                    ? substr(fechaDma($row['fechaindustrial'] ?? $fechaindustrial), 0, 5)
                    : substr(fechaDma($fechaindustrial), 0, 5); ?>
              </td>
              <td class="text-start"><?php echo rango_hora($slot); ?></td>
              <td class="text-start"><?php echo $tiene_dato ? fmt_num($row['gas']    ?? 0, 0) : ''; ?></td>
              <td class="text-start"><?php
                  if ($tiene_dato)          echo fmt_num($row['kilos'] ?? 0, 0);
                  elseif ($kilos_parcial !== null) echo '<em>' . fmt_num($kilos_parcial, 0) . '</em>';
              ?></td>
              <td class="text-start"><?php echo $tiene_dato ? fmt_num($row['humedad']     ?? 0, 2) : ''; ?></td>
              <td class="text-start"><?php echo $tiene_dato ? fmt_num($row['bolsas']      ?? 0, 0) : ''; ?></td>
              <td class="text-start"><?php echo $tiene_dato ? fmt_num($row['alc_industrial'] ?? '', 2) : ''; ?></td>
              <td class="text-start"><?php echo $tiene_dato ? fmt_num($row['pol_bagazo']  ?? '', 2) : ''; ?></td>
              <td class="text-start"><?php echo $tiene_dato ? fmt_num($row['pol_cachaza'] ?? '', 2) : ''; ?></td>
              <td class="text-start"><?php echo $tiene_dato ? fmt_num($row['jugo_derivado'] ?? '', 2) : ''; ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="d-flex align-items-center gap-3 mt-1" style="font-size:14px; font-weight:700;">
    <div class="d-flex align-items-center gap-1">
      <span class="text-secondary">Gas acum.:</span>
      <input type="text" class="form-control form-control-sm" style="width:90px;font-size:14px;font-weight:700;"
             value="<?php echo $gas_acumulado ? fmt_num($gas_acumulado, 0) : 0; ?>">
    </div>
    <div class="d-flex align-items-center gap-1">
      <span class="text-secondary">Bolsas:</span>
      <input type="text" class="form-control form-control-sm" style="width:80px;font-size:14px;font-weight:700;"
             value="<?php echo $total_bolsas ? fmt_num($total_bolsas, 0) : 0; ?>">
    </div>
    <div class="d-flex align-items-center gap-1">
      <span class="text-secondary">Anhidro:</span>
      <input type="text" class="form-control form-control-sm" style="width:80px;font-size:14px;font-weight:700;"
             value="<?php echo $total_anhidro ? fmt_num($total_anhidro, 0) : 0; ?>">
    </div>
  </div>
</div>
