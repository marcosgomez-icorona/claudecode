<?php
    // Indexar pesadas por hora
    $pesadas_por_hora = [];
    foreach ($canchon ?? [] as $item) {
        $pesadas_por_hora[$item['hora']] = (int)$item['cantidad'];
    }

    // Slots del dia industrial desde las 07:00 hasta las 06:00
    $slots_canchon = [
        '07:00','08:00','09:00','10:00','11:00','12:00','13:00',
        '14:00','15:00','16:00','17:00','18:00','19:00','20:00',
        '21:00','22:00','23:00','00:00','01:00','02:00','03:00',
        '04:00','05:00','06:00'
    ];

    // Solo mostrar hasta la hora actual (inclusive) para el dia de hoy
    $h_actual_str = date('H') . ':00';
    $total_camiones = array_sum($pesadas_por_hora);
?>
<div class="realtime-table-wrap">
  <table class="realtime-table">
    <thead class="sticky-head">
      <tr>
        <th class="th-blue">Hora</th>
        <th class="th-green">Cantidad</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $mostrar = false;
      foreach ($slots_canchon as $slot):
          // Empezar a mostrar desde 07:00 siempre
          // Para el dia actual: mostrar hasta la hora actual inclusive
          // Para dias historicos (si se implementa): mostrar todos los que tengan datos
          $cantidad   = $pesadas_por_hora[$slot] ?? 0;
          $tiene_dato = isset($pesadas_por_hora[$slot]);

          // Si hay datos para cualquier hora, mostrar esa hora
          // Si no hay datos y es futura, no mostrar
          $es_futura = false;
          if (!$tiene_dato) {
              // Determinar si la hora ya paso (considerando cruce de medianoche)
              $h_slot = (int)substr($slot, 0, 2);
              $h_now  = (int)date('G');
              // Slots 00-06 pertenecen al dia siguiente del industrial
              $slot_num = ($h_slot >= 7) ? $h_slot : ($h_slot + 24);
              $now_num  = ($h_now  >= 7) ? $h_now  : ($h_now  + 24);
              $es_futura = ($slot_num > $now_num);
          }

          if ($es_futura) continue;
      ?>
      <tr<?php echo $tiene_dato ? '' : ' class="dash"'; ?>>
        <td class="hour"><?php echo htmlspecialchars($slot); ?></td>
        <td><?php echo $tiene_dato ? $cantidad : '-'; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <?php if ($total_camiones > 0): ?>
    <tfoot>
      <tr>
        <td class="td-text">Total</td>
        <td><?php echo $total_camiones; ?></td>
      </tr>
    </tfoot>
    <?php endif; ?>
  </table>
</div>
