<?php  
include_once 'controller/recibos.php';
include_once 'controller/usuarios.php';
include_once 'funciones/funciones.php';

$busqueda = $_POST['busqueda'] ?? '';
$resultado_cargas = listado_cargas($busqueda);
?>

<div class="container">
  <!-- Título -->
  <div class="row">
    <div class="col-12">
      <h1 class="display-4 text-center text-muted">Listado de Cargas</h1>
    </div>
  </div>

  <!-- Barra de búsqueda -->
  <div class="row">
    <div class="col-12">
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <form action="home.php?menu=listado_cargas" class="form-inline my-1 my-lg-0 w-100" method="POST">
          <input class="form-control mr-sm-2 flex-grow-1" type="search" name="busqueda"
                 placeholder="Buscar Carga..." value="<?= htmlspecialchars($busqueda ?? '') ?>" />
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>
        </form>
      </nav>
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
                  <th>Detalle</th>
                  <th>Fecha de Carga</th>                  
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $resultado_cargas->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['carga'] ?? '') ?></td>
                    <td><?= htmlspecialchars(FechaDma($row['fecha_subida']) ?? '') ?></td>
                    <td>
                      <div class="btn-group btn-group-sm mb-1" role="group">
                        <a class="btn btn-info btn-sm" href="home.php?accion=listado_recibos_asociados_carga&periodo=<?= $row['periodo'] ?? '' ?>&quincena=<?= $row['quincena'] ?? '' ?>&tipo_liquidacion=<?= $row['tipo_liquidacion'] ?? '' ?>&detalle=<?= $row['detalle'] ?? '' ?>">Ver Recibos Asociados</a>
                      </div>
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
