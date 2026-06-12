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

$canieros = obtiene_ctas_caniero($cuit_caniero);

$cantidad_ctas=count($canieros) ?? 0;

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
                <!-- Última Actualización: -->
                <?php // echo isset($canieros[0]['ultima_actualizacion']) ? FechaDma($canieros[0]['ultima_actualizacion']) : 'No disponible'; ?>
        </p>
    </div>
    <div class="card-header bg-secondary text-white d-flex flex-column flex-sm-row justify-content-between align-items-center">
        <h4 class="mb-2 mb-sm-0">Ordenes de Pago</h4>
        <div class="text-center text-sm-end">
            <h5 class="fw-bold mb-1"></h5>            
        </div>
    </div>
    <div class="card mb-3 shadow">
      <div class="card-body">
        <form method="GET" action="home.php">
            <input type="hidden" name="accion" value="ver_op_azucar">
            <input type="hidden" name="usuario" value="<?php echo htmlspecialchars($usuario_get); ?>">
            <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($nom_caniero); ?>">

            <div class="row g-2 align-items-end">                                
                <div class="col-12 text-end">
                    <button class="btn btn-sm btn-success" onclick="exportarExcel('op_azucar', event)">Exportar a Excel</button>                    
                </div> 
            </div>
        </form>
      </div>
    </div>    
    <?php 
    
    echo !empty($canieros[0]['grupo']) ? '<h4 class="text-start text-secondary">GRUPO:  '. $canieros[0]['grupo'].'</h4>' :"";
    
    
    $op_azucar_data = OPAzucarCaniero($cuit_caniero);

    ?>
    <div class="card-body"> 
            <div class="table-responsive">
                <table id="op_azucar" class="table table-striped table-hover table-bordered align-middle">
                    <thead class="table-light text-center">                    
                    <tr><th colspan="11" class="text-start"><?php if(!empty($canieros))  echo $canieros[0]['razon_social']; ?></th>                                                </tr>                    
                    <tr>
                        <th>Fecha</th>
                        <th>Nro OC</th>                        
                        <th>Descripcion</th>                        
                        <th>Total</th>                        
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($canieros)): ?>
                        <?php foreach ($op_azucar_data as $array): ?>
                        <tr>
                        <td><?php echo isset($array['fecha']) ? htmlspecialchars(fechaDma($array['fecha'])) : ''; ?></td>
                        <td><?php echo htmlspecialchars($array['nro_op'] ?? ''); ?></td>                                   
                        <td><?php echo htmlspecialchars($array['descr_item'] ?? ''); ?></td>                         
                        <td><?php echo isset($array['total']) ? htmlspecialchars(number_format((float)$array['total'], 0, ',', '.')) : ''; ?></td>                        
                        </tr>
                        <?php endforeach; ?>                                                
                    </tbody>
                    <?php endif; ?>
                </table>
            </div>
    </div>    
</div>
<script>
   
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
        
</script>