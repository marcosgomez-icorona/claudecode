<?php 
$nom_caniero = !empty($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : 'Desconocido';

// Capturamos todos los resultados en un array primero para poder verificar si hay datos
include 'controller/canieros.php';
$i=0;
$canieros = [];
$canieros_data = [];

// Leer parámetros GET de forma segura
$usuario_get = !empty($_GET['usuario']) ? $_GET['usuario'] : '';
$cuit_caniero = !empty($usuario_get) ? base64_decode($usuario_get) : '';

$desde = !empty($_GET['desde']) ? $_GET['desde'] : '';//dia de ayer
$hasta = !empty($_GET['hasta']) ? $_GET['hasta'] : '';
// Llamar a la función con los parámetros correctos
$canieros = obtiene_ctas_caniero($cuit_caniero);

?>

<div class="card shadow-sm mb-4">    
    <div class="text-end">
        <a href="home.php?menu=canieros&usuario=<?php echo $usuario_get; ?>" class="btn btn-secondary mt-3">
            <img src="assets/ico/ico_regresar.png" height="24" width="24" alt="Regresar" class="me-1">
            Regresar al Menú
        </a>
    </div>
    <div class="m-1">
        <img src="assets/img/Logo-Ing La Corona.png" width="60px" class="img-fluid me-2" alt="Logo">
    </div>
    <div>
        <p class="text-end text-muted">
                Última Actualización: 
                <?php echo isset($canieros[0]['ultima_actualizacion']) ? FechaDma($canieros[0]['ultima_actualizacion']) : 'No disponible'; ?>
        </p>
    </div>
    <div class="card-header bg-secondary text-white d-flex flex-column flex-sm-row justify-content-between align-items-center">
        <h4 class="mb-2 mb-sm-0">Detalle de Caña</h4>
        <div class="text-center text-sm-end">
            <h5 class="fw-bold mb-1"></h5>            
        </div>
    </div>
    <div class="card mb-3 shadow">
      <div class="card-body">
        <form method="GET" action="home.php">
            <input type="hidden" name="accion" value="ver_detalle_cania">
            <input type="hidden" name="usuario" value="<?php echo htmlspecialchars($usuario_get); ?>">
            <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($nom_caniero); ?>">

            <div class="row g-2 align-items-end">                
                <div class="col-md-2">
                    <label for="">Desde</label>
                    <input type="date" class="form-control" name="desde" value="<?php if(empty($_GET)) echo date('Y-m-d')-1;?>">
                </div> 
                <div class="col-md-2">
                    <label for="">Hasta</label>
                    <input type="date" class="form-control" name="hasta" value="<?php //if(empty($_GET)) echo date('Y-m-d');?>">
                </div>          
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary">Buscar</button>
                </div> 
                <div class="col-4 text-end">
                    <button class="btn btn-sm btn-success" onclick="exportarExcel('detalle_cania', event)">Exportar a Excel</button>                    
                </div> 
            </div>
        </form>
      </div>
    </div>    
    <?php 
    
    echo !empty($canieros[0]['grupo']) ? '<h4 class="text-start text-secondary">GRUPO:  '. $canieros[0]['grupo'].'</h4>' :"";
    
    foreach ($canieros as $nomcaniero):
        $canieros_data = getDetalleCania($nomcaniero['razon_social'], $desde,$hasta);
        $total_bruto = 0;
        $total_trash = 0;
        $total_neto = 0;
        $total_brix = 0;
        $total_pol = 0;
        $total_pureza = 0;
        $total_rendimiento = 0;
        $i = 0;

        foreach ($canieros_data as $totales):
            $total_bruto += !empty($totales['bruto_tn']) ? $totales['bruto_tn'] : 0;
            $total_trash += !empty($totales['trash']) ? $totales['trash'] : 0;
            $total_neto += !empty($totales['neto_tn']) ? $totales['neto_tn'] : 0;
            $total_brix += !empty($totales['brix']) ? $totales['brix'] : 0;
            $total_pol += !empty($totales['pol']) ? $totales['pol'] : 0;
            $total_pureza += !empty($totales['pureza']) ? $totales['pureza'] : 0;
            $total_rendimiento += !empty($totales['rendimiento']) ? $totales['rendimiento'] : 0;
            $i++;
        endforeach;

        // Promedio de trash en valor absoluto
        $avg_trash = $i > 0 ? round($total_trash / $i,2) : 0;
        $avg_brix = $i > 0 ? round($total_brix / $i,2) : 0;
        $avg_pol = $i > 0 ? round($total_pol / $i,2) : 0;
        $avg_pureza = $i > 0 ? round($total_pureza / $i,2) : 0;
        $avg_rto = $i > 0 ? round($total_rendimiento / $i,2) : 0;
    ?>
    <div class="card-body"> 
            <div class="table-responsive">
                <table id="detalle_cania" class="table table-striped table-hover table-bordered align-middle">
                    <thead class="table-light text-center">                    
                    <tr><th colspan="11" class="text-start"><?php if(!empty($canieros_data))  echo $canieros_data[0]['razon_social']; ?></th>                                                </tr>
                    <h4>
                    <tr>
                        <th colspan="4" class="text-end"><strong>Total :</strong></th>                                                
                        <th><?php echo number_format((float)$total_bruto, 0, ',', '.'); ?></th>
                        <th><?php echo number_format((float)$avg_trash, 2, ',', '.')." %"; ?></th>
                        <th><?php echo number_format((float)$total_neto, 0, ',', '.'); ?></th>
                        <th><?php echo number_format((float)$avg_brix, 2, ',', '.'); ?></th>
                        <th><?php echo number_format((float)$avg_pol, 2, ',', '.'); ?></th>
                        <th><?php echo number_format((float)$avg_pureza, 2, ',', '.'); ?></th>
                        <th><?php echo number_format((float)$avg_rto, 2, ',', '.'); ?></th>
                    </tr>
                    </h4>
                    <tr>
                        <th>Fecha</th>
                        <th>Pesada Nro</th>
                        <th>Remito</th>
                        <th>Caña</th>
                        <th>Bruto</th>
                        <th>Trash Real%</th>
                        <th>Neto Real</th>
                        <th>Brix</th>
                        <th>Pol</th>
                        <th>Pureza</th>
                        <th>Rto Real</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($canieros_data)): ?>
                        <?php foreach ($canieros_data as $array): ?>
                        <tr>
                        <td><?php echo isset($array['fechaindustrial']) ? htmlspecialchars($array['fechaindustrial']) : ''; ?></td>
                        <td><?php echo htmlspecialchars($array['pesada'] ?? ''); ?></td>                            
                        <td><?php echo htmlspecialchars($array['remito'] ?? ''); ?></td>                            
                        <td><?php echo htmlspecialchars($array['tipo'] ?? ''); ?></td>                            

                        <td><?php echo isset($array['bruto_tn']) ? htmlspecialchars(number_format((float)$array['bruto_tn'], 0, ',', '.')) : ''; ?></td>
                        <td><?php echo isset($array['trash']) ? htmlspecialchars(number_format((float)$array['trash'], 2, ',', '.') . ' %') : ''; ?></td>
                        <td><?php echo isset($array['neto_tn']) ? htmlspecialchars(number_format((float)$array['neto_tn'], 0, ',', '.')) : ''; ?></td>
                        <td><?php echo isset($array['brix']) ? htmlspecialchars(number_format((float)$array['brix'], 2, ',', '.')) : ''; ?></td>
                        <td><?php echo isset($array['pol']) ? htmlspecialchars(number_format((float)$array['pol'], 2, ',', '.')) : ''; ?></td>
                        <td><?php echo isset($array['pureza']) ? htmlspecialchars(number_format((float)$array['pureza'], 2, ',', '.')) : ''; ?></td>
                        <td><?php echo isset($array['rendimiento']) ? htmlspecialchars(number_format((float)$array['rendimiento'], 2, ',', '.')) : ''; ?></td>                           
                        </tr>
                        <?php endforeach; ?>                                                
                    </tbody>
                    <?php endif; ?>
                </table>
            </div>
        
    </div>
    <?php endforeach;?>
</div>

<div class="modal fade" id="modalItems" tabindex="-1" aria-labelledby="modalItemsLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-secondary text-white">
        <h5 class="modal-title" id="modalItemsLabel">Detalle de Items <span id="modalOcNumber"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>        
      </div>
      <div class="modal-body">
        <div id="modalItemsContent">
            <?php include 'detalle_oc.php';?>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-info" id="btnImprimir">Imprimir</button>
      </div>
    </div>
  </div>
</div>
<script>
document.getElementById('btnImprimir').addEventListener('click', function () {
  // Seleccionamos el contenido del modal
  var element = document.getElementById('modalItemsContent');

  // Opciones para el PDF
  var opt = {
    margin:       0.1,
    filename:     'detalle_oc.pdf',
    image:        { type: 'html', quality: 1 },
    html2canvas:  { scale: 2 },
    jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
  };

  // Generamos y descargamos el PDF
  html2pdf().set(opt).from(element).save();
});
</script>
<script>
    document.getElementById('fecha-hoy').textContent = new Date().toLocaleDateString('es-AR');

    // Manejo del modal para cargar ítems
    document.addEventListener('DOMContentLoaded', function () {
        const modalItems = document.getElementById('modalItems');
        modalItems.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Botón que disparó el modal
            const nroOc = button.getAttribute('data-nro-oc'); // Extrae el nro_oc del botón

            const modalTitle = modalItems.querySelector('#modalOcNumber');
            modalTitle.textContent = `(OC ${nroOc})`;

            const modalBodyContent = modalItems.querySelector('#modalItemsContent');
            modalBodyContent.innerHTML = '<p class="text-center text-muted">Cargando ítems...</p>'; // Mostrar cargando

            // Aquí deberías hacer una llamada AJAX para obtener los ítems de esta OC
            // Por ejemplo: fetch('api/get_oc_items.php?nro_oc=' + nroOc)
            // .then(response => response.json())
            // .then(data => {
            //     // Construir la tabla con los datos de 'data'
            //     let itemsHtml = `
            //         <div class="table-responsive mb-3">
            //             <table class="table table-sm table-bordered">
            //                 <thead class="table-light">
            //                     <tr>
            //                         <th>Item</th>
            //                         <th>Artículo / Observación</th>
            //                         <th>Cant.</th>
            //                         <th>Unidad</th>
            //                         <th>Precio</th>
            //                         <th>Total</th>
            //                     </tr>
            //                 </thead>
            //                 <tbody>
            //     `;
            //     data.items.forEach(item => {
            //         itemsHtml += `
            //             <tr>
            //                 <td>${item.item}</td>
            //                 <td>${item.articulo}</td>
            //                 <td>${item.cantidad}</td>
            //                 <td>${item.unidad}</td>
            //                 <td>$${item.precio.toFixed(2)}</td>
            //                 <td>$${item.total.toFixed(2)}</td>
            //             </tr>
            //         `;
            //     });
            //     itemsHtml += `
            //                 </tbody>
            //             </table>
            //         </div>
            //         <div class="row">
            //             <div class="col-md-8"><strong>Observaciones:</strong> ${data.observaciones}</div>
            //             <div class="col-md-4 text-end"><strong>Total OC:</strong> $${data.total_oc.toFixed(2)}</div>
            //         </div>
            //     `;
            //     modalBodyContent.innerHTML = itemsHtml;
            // })
            // .catch(error => {
            //     console.error('Error al cargar los ítems:', error);
            //     modalBodyContent.innerHTML = '<p class="text-center text-danger">Error al cargar los ítems.</p>';
            // });

            // SIMULACIÓN DE DATOS del modal (reemplazar con AJAX real)
            setTimeout(() => {
                const simulatedData = {
                    items: [
                        { item: 1, articulo: `Artículo A (OC ${nroOc})`, cantidad: 10, unidad: 'Un.', precio: 500, total: 5000 },
                        { item: 2, articulo: `Artículo B (OC ${nroOc})`, cantidad: 5, unidad: 'Un.', precio: 300, total: 1500 }
                    ],
                    observaciones: 'Simulación de observaciones para esta OC.',
                    total_oc: 6500
                };
                let itemsHtml = `
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Artículo / Observación</th>
                                    <th>Cant.</th>
                                    <th>Unidad</th>
                                    <th>Precio</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                simulatedData.items.forEach(item => {
                    itemsHtml += `
                        <tr>
                            <td>${htmlspecialchars(item.item)}</td>
                            <td>${htmlspecialchars(item.articulo)}</td>
                            <td>${htmlspecialchars(item.cantidad)}</td>
                            <td>${htmlspecialchars(item.unidad)}</td>
                            <td>$${number_format(item.precio, 2)}</td>
                            <td>$${number_format(item.total, 2)}</td>
                        </tr>
                    `;
                });
                itemsHtml += `
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-8"><strong>Observaciones:</strong> ${htmlspecialchars(simulatedData.observaciones)}</div>
                        <div class="col-md-4 text-end"><strong>Total OC:</strong> $${number_format(simulatedData.total_oc, 2)}</div>
                    </div>
                `;
                modalBodyContent.innerHTML = itemsHtml;
            }, 1000); // Simula un retraso de 1 segundo

            // Función para htmlspecialchars en JS (para simulación)
            function htmlspecialchars(str) {
                return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
            }
            // Función para number_format en JS (para simulación)
            function number_format(number, decimals, decPoint, thousandsSep) {
                number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
                var n = !isFinite(+number) ? 0 : +number,
                    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                    sep = (typeof thousandsSep === 'undefined') ? '.' : thousandsSep,
                    dec = (typeof decPoint === 'undefined') ? ',' : decPoint,
                    s = '',
                    toFixedFix = function (n, prec) {
                        var k = Math.pow(10, prec);
                        return '' + (Math.round(n * k) / k).toFixed(prec);
                    };
                s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
                if (s[0].length > 3) {
                    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
                }
                if ((s[1] || '').length < prec) {
                    s[1] = s[1] || '';
                    s[1] += new Array(prec - s[1].length + 1).join('0');
                }
                return s.join(dec);
            }
        });
    });
</script>