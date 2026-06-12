<?php
$detalle_molienda_dia = DetalleMolienda() ?? [];
?>
<div class="realtime-table-wrap">
  <table class="realtime-table realtime-table-wide">
    <thead class="sticky-head">
      <tr>
        <th class="th-blue">Nro Pesada</th>
        <th>Grupo</th>
        <th class="th-text">Ca&ntilde;ero</th>
        <th>Nro Muestra</th>
        <th class="th-blue">Ca&ntilde;a Bruta</th>
        <th class="th-amber">Trash</th>
        <th class="th-green">Brix %</th>
        <th class="th-green">Pol %</th>
        <th class="th-teal">Pureza</th>
        <th class="th-teal">Rendimiento</th>
        <th>T Ca&ntilde;a</th>
        <th>F. Pesada</th>
        <th>Hr Pesada</th>
        <th>F. Salida</th>
        <th>Hr Salida</th>
        <th>Muestra 2</th>
        <th>Contrato</th>
        <th class="th-text">Fletero</th>
        <th class="th-text">Cosechero</th>
        <th>Finca</th>
        <th class="th-text">Nombre Finca</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($detalle_molienda_dia as $d): ?>
      <tr>
        <td class="hour"><?php echo htmlspecialchars($d['numero_pesada'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['GRUPO']         ?? ''); ?></td>
        <td class="td-text"><?php echo htmlspecialchars($d['caniero']       ?? ''); ?></td>
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
        <td class="td-text"><?php echo htmlspecialchars($d['fletero']       ?? ''); ?></td>
        <td class="td-text"><?php echo htmlspecialchars($d['cosechero']     ?? ''); ?></td>
        <td><?php echo htmlspecialchars($d['finca']         ?? ''); ?></td>
        <td class="td-text"><?php echo htmlspecialchars($d['nombre_finca']  ?? ''); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
