<?php ?>
<div class="table-responsive">
  <table class="table table-sm table-bordered table-striped align-middle mb-0 dash-data-table">
    <thead class="table-light text-center">
      <tr>
        <th>Desde</th>
        <th>Hasta</th>
        <th>T Neto</th>
        <th class="text-start">Origen</th>
        <th class="text-start">Máquina</th>
        <th class="text-start">Motivo</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($paradas as $item): ?>
      <tr>
        <td><?php echo htmlspecialchars($item['DESDE']   ?? ''); ?></td>
        <td><?php echo htmlspecialchars($item['HASTA']   ?? ''); ?></td>
        <td><?php echo htmlspecialchars($item['T_Neto']  ?? ''); ?></td>
        <td class="text-start"><?php echo htmlspecialchars($item['origen']  ?? ''); ?></td>
        <td class="text-start"><?php echo htmlspecialchars($item['maquina'] ?? ''); ?></td>
        <td class="text-start"><?php echo htmlspecialchars($item['motivo']  ?? ''); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
