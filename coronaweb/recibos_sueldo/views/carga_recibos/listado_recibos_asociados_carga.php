<?php  
include_once 'controller/recibos.php';
include_once 'controller/usuarios.php';
include_once 'funciones/funciones.php';

$periodo = $_GET['periodo'] ?? '';
$quincena = $_GET['quincena'] ?? '';
$tipo_liquidacion = $_GET['tipo_liquidacion'] ?? '';
$detalle = $_GET['detalle'] ?? '';
$busqueda = $periodo.$quincena.$tipo_liquidacion.$detalle;

$resultado_cargas = listado_cargas_asociadas_recibo($busqueda);
?>

<div class="container">
  <div class="text-end">
        <a href="home.php?menu=listado_cargas" class="btn btn-secondary mt-3">
            <img src="assets/ico/ico_regresar.png" height="24" width="24" alt="Regresar" class="me-1">
            Regresar
        </a>
</div>
  <!-- Título -->
  <div class="row">
    <div class="col-12">
      <h1 class="display-4 text-center text-muted">Listado de Recibos</h1>
    </div>
  </div>
  <!-- Tarjeta con tabla -->
  <div class="row">
    <div class="col-12">
      <div class="card m-1">        
        <div class="card-body m-2">
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead class="bg-secondary text-white">
                <tr>
                  <th>Nombre</th>
                  <th>Periodo</th>                  
                  <th>Recibo</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $resultado_cargas->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['nombre'] ?? '') ?></td>                    
                    <td><?= htmlspecialchars($row['periodo'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['recibo'] ?? '') ?></td> 
                    <td>
                      <a class="btn btn-outline-info btn-sm hover-shadow-sm transition-ease" href="#" onclick="descargarPDF('<?php echo 'assets/recibos/'.$row['recibo'] ?? ''; ?>')">Descargar</a>
                    </td>                   
                  </tr>
                <?php endwhile; ?>
                <?php if ($resultado_cargas->num_rows === 0): ?>
                  <tr>
                    <td colspan="6" class="text-center text-muted">No se encontraron resultados.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div> <!-- /.table-responsive -->
        </div> <!-- /.card-body -->
      </div> <!-- /.card -->
    </div> <!-- /.col -->
  </div> <!-- /.row -->
</div> <!-- /.container -->
