<?php
if (!isset($paradas) || !is_array($paradas)) {
    if (!function_exists('EjecutarConsulta') || !function_exists('ObtieneDatosSQL')) {
        include_once 'controller/molienda_online.php';
    }

    $hora_paradas = $hora ?? ($_POST['hora'] ?? '');
    $fechaindustrial_raw = $fechaindustrial ?? ($_POST['fechaindustrial'] ?? '');
    $fecha_paradas = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$fechaindustrial_raw)
        ? $fechaindustrial_raw
        : date('Y-m-d');

    $sql_paradas = EjecutarConsulta('paradas', $fecha_paradas, $hora_paradas);
    $paradas = $sql_paradas !== '' ? ObtieneDatosSQL($sql_paradas) : [];
}

$paradas = is_array($paradas) ? $paradas : [];
?>
<div class="realtime-table-wrap">
  <table class="realtime-table">
    <thead class="sticky-head">
      <tr>
        <th class="th-blue">Desde</th>
        <th class="th-blue">Hasta</th>
        <th class="th-amber">T Neto</th>
        <th class="th-text th-teal">Origen</th>
        <th class="th-text th-green">Maquina</th>
        <th class="th-text">Motivo</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($paradas)): ?>
      <tr>
        <td class="td-text dash" colspan="6">Sin paradas registradas para la fecha seleccionada.</td>
      </tr>
      <?php else: ?>
      <?php foreach ($paradas as $item): ?>
      <tr>
        <td class="hour"><?php echo htmlspecialchars($item['DESDE']   ?? ''); ?></td>
        <td class="hour"><?php echo htmlspecialchars($item['HASTA']   ?? ''); ?></td>
        <td><?php echo htmlspecialchars($item['T_Neto']  ?? ''); ?></td>
        <td class="td-text"><?php echo htmlspecialchars($item['origen']  ?? ''); ?></td>
        <td class="td-text"><?php echo htmlspecialchars($item['maquina'] ?? ''); ?></td>
        <td class="td-text"><?php echo htmlspecialchars($item['motivo']  ?? ''); ?></td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
