<?php
$detalle_molienda_dia = DetalleMolienda() ?? [];
?>
<div class="table-responsive">
  <table class="table table-striped table-bordered table-sm align-middle mb-0 dash-data-table" >
    <thead class="table-light text-center" style="position:sticky; top:0; z-index:1; font-size:13px; font-weight:normal;">
      <tr>
        <th>Nro Pesada</th>
        <th>Grupo</th>
        <th>Cañero</th>
        <th>Nro Muestra</th>
        <th>Caña Bruta</th>
        <th>Trash</th>
        <th>Brix %</th>
        <th>Pol %</th>
        <th>Pureza</th>
        <th>Rendimiento</th>
        <th>T Caña</th>
        <th>F. Pesada</th>
        <th>Hr Pesada</th>
        <th>F. Salida</th>
        <th>Hr Salida</th>
        <th>Muestra 2</th>
        <th>Contrato</th>
        <th>Fletero</th>
        <th>Cosechero</th>
        <th>Finca</th>
        <th>Nombre Finca</th>
      </tr>
    </thead>
    <tbody class=" table-sm text-secondary">
      <?php foreach ($detalle_molienda_dia as $d): ?>
      <tr>
        <td><?php echo htmlspecialchars($d['numero_pesada'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['GRUPO']         ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['caniero']       ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['nro_muestra']   ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['cania_bruta']   ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['trash']         ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['Brixporciento'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['Polporciento']  ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['Pureza']        ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['Rendimiento']   ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['tipo_cania']    ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['fecha_pesada']  ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['hora_pesada']   ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['fecha_salida']  ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['hora_salida']   ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['nromuestra2']   ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['tipo_contrato'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['fletero']       ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['cosechero']     ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['finca']         ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['nombre_finca']  ?? ''); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
