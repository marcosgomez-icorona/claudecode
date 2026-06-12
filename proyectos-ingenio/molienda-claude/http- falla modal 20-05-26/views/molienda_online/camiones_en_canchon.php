<?php ?>
<div class="table-responsive">
  <table class="table table-sm table-bordered table-striped align-middle mb-0 dash-data-table">
    <thead class="table-light text-center">
      <tr>
        <th>Hora</th>
        <th>Cantidad</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($canchon ?? [] as $item): ?>
      <tr>
        <td><?php echo htmlspecialchars($item['hora']     ?? ''); ?></td>
        <td><?php echo htmlspecialchars($item['cantidad'] ?? ''); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
