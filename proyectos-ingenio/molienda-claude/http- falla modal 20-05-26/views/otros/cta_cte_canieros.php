<?php

$nom_caniero = !empty($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : 'Desconocido';

// Capturamos todos los resultados en un array primero para poder verificar si hay datos
include 'controller/canieros.php';
$i=0;
$canieros = [];
$cta_cte_caniero_data = [];

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
    <div class="card-header bg-secondary text-white d-flex flex-column flex-sm-row justify-content-between align-items-center">
        <h4 class="mb-2 mb-sm-0">Cta. Cte. Cañeros</h4>
        <div class="text-center text-sm-end">
            <h5 class="fw-bold mb-1"></h5>            
        </div>
    </div>
    <div class="card mb-1 shadow">
      <div class="card-body">
        <form method="GET" action="home.php">
            <input type="hidden" name="accion" value="ver_detalle_cania">
            <input type="hidden" name="usuario" value="<?php echo htmlspecialchars($usuario_get); ?>">
            <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($nom_caniero); ?>">

            <div class="row g-2 align-items-end">               
                
                <div class="col-11 text-end">
                    <button  class="btn btn-sm btn-success text-end" onclick="exportarExcel('cta_cte_cania', event)">Exportar a Excel</button>
                </div> 
            </div>
        </form>
      </div>
    </div>
    <div class="card-body" id="cta_cte_cania"> 
        <?php 
        echo !empty($canieros[0]['grupo']) ? '<h4 class="text-start text-secondary">GRUPO:  '. $canieros[0]['grupo'].'</h4>' :"";

        foreach($canieros as $cuentas):
            $cta_cte_caniero_data = getctactecaniero($cuentas['razon_social']);            
        ?>
        <!--
        <div>
            <p class="text-end text-muted mb-1">
                    Última Actualización:
                    <?php //echo isset($cta_cte_caniero_data[0]['ultima_actualizacion']) ? $cta_cte_caniero_data[0]['ultima_actualizacion'] : 'No disponible'; ?>
            </p>
        </div>
        -->
            <div class="table-responsive" >
                <table  class="table table-striped table-hover table-bordered align-middle">
                    <thead class="table-light text-center">                      
                    <tr><th colspan="8" class="text-start"><?php echo isset($cta_cte_caniero_data[0]['caniero']) ? $cta_cte_caniero_data[0]['caniero'] : '' ?></th></tr>
                    <tr>
                        <th>Caña Bruta Kgs</th>
                        <th>Bolsas Cañero Devengadas</th>
                        <th>Ordenes Emitidas Bolsas</th>
                        <th>Saldo Zafra Anterior</th>
                        <th>Ordenes Pend.a Emitir Bls</th>
                        <th>Saldo Zafra anterior $</th>
                        <th>Maquila $</th>
                        <th>Saldo en Bolsas</th>                        
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($cta_cte_caniero_data)): ?>
                        <?php foreach ($cta_cte_caniero_data as $array): ?>
                        <tr>
                        <td><?php echo isset($array['cania_bruta']) ? htmlspecialchars(number_format((float)$array['cania_bruta'], 0, ',', '.')) : 0; ?></td>                        
                        <td><?php echo isset($array['blsas_dev']) ? htmlspecialchars(number_format((float)$array['blsas_dev'], 0, ',', '.')) : ''; ?></td>                                                
                        <td><?php echo isset($array['ordenes_emitidas_blsas']) ? htmlspecialchars(number_format((float)$array['ordenes_emitidas_blsas'], 0, ',', '.')) : 0; ?></td>                                                                        
                        <td><?php echo isset($array['saldo_zafra_ant']) ? htmlspecialchars(number_format((float)$array['saldo_zafra_ant'], 0, ',', '.')) : 0; ?></td>
                        <td><?php echo isset($array['ordenes_pendientes_blsas']) ? htmlspecialchars(number_format((float)$array['ordenes_pendientes_blsas'], 0, ',', '.')) : 0; ?></td>
                        <td><?php echo isset($array['saldo_zafra_ant_pesos']) ? htmlspecialchars(number_format((float)$array['saldo_zafra_ant_pesos'], 2, ',', '.')) : 0; ?></td>
                        <td><?php echo isset($array['maquila']) ? htmlspecialchars(number_format((float)$array['maquila'], 2, ',', '.') ) : 0; ?></td>
                        <td><?php echo isset($array['saldo_en_blsas']) ? htmlspecialchars(number_format((float)$array['saldo_en_blsas'], 0, ',', '.')) : 0; ?></td>
                        
                        </tr>
                        <?php endforeach; ?>                                                
                    </tbody>
                    <?php endif; ?>
                </table>
            </div>
            <hr><hr>
            <tr><td></td></tr>
             <?php endforeach; ?>   
    </div>
    
</div>

