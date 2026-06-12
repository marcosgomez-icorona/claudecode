<?php

/*DETALLE MOLIENDA ACTUAL */    
$detalle_molienda_dia = DetalleMolienda() ?? '';

?>
<div class="card p-3">
                  <div class="table-responsive small">
                    <table class="table table-striped table-bordered table-sm align-middle mb-0">
                      <thead class="table-light text-center text-dark">
                        <tr class="text-start">
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
                          <th>Fecha Pesada</th>
                          <th>Hr Pesada</th>
                          <th>Fecha Salida</th>
                          <th>Hr Salida</th>
                          <th>nromuestra2</th>
                          <th>usuariopesada</th>
                          <th>cantelementos</th>
                          <th>Tipo Contrato</th>
                          <th>Fletero</th>
                          <th>Cosechero</th>
                          <th>Finca</th>
                          <th>Finca Nombre</th>                          
                        </tr>
                      </thead>
                      <tbody>
                        <!-- Datos aquí -->
                         <?php foreach ($detalle_molienda_dia as $detalle): ?>
                         <tr>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['numero_pesada'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['GRUPO'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['caniero'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['nro_muestra'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['cania_bruta'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['trash'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['trashReal'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['Brixporciento'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['Polporciento'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['Pureza'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['Rendimiento'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['tipo_cania'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['fecha_pesada'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['hora_pesada'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['fecha_salida'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['hora_salida'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['nromuestra2'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['cantelementos'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['tipo_contrato'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['fletero'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['cosechero'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['finca'] ?? ''); ?></td>
                            <td class="text-start"><?php echo htmlspecialchars($detalle['nombre_finca'] ?? ''); ?></td>                            
                         </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>