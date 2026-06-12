<?php
/* -------------------------------------------------
 *  Carga de datos y sanitización básica
 * -------------------------------------------------*/
include_once 'controller/recibos.php';

$id_liquidacion = isset($_GET['id_liquidacion']) ? (int)$_GET['id_liquidacion'] : -1;
$busqueda       = isset($_POST['busqueda'])      ? trim($_POST['busqueda'])     : '';

$recibos = listado_recibos($id_liquidacion, 'empleados', $busqueda); // ahora es array[]
$primer  = $recibos[0] ?? null;   // Para mostrar cabeceras de período
?>
<div class="container">
  <!-- Título -->
  <div class="row">
    <div class="col-12">
      <h1 class="display-4 text-center text-muted">Detalle de Liquidación</h1>
    </div>
    
  </div>

  <!-- Barra de búsqueda -->
  <div class="row">
    <div class="col-12">
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <form action="home.php?menu=ver_recibos&id_liquidacion=<?= $id_liquidacion ?>" method="POST"
              class="form-inline my-1 my-lg-0 w-100">
          <input class="form-control mr-sm-2 flex-grow-1" type="search" name="busqueda" placeholder="Búsqueda"
                 value="<?= htmlspecialchars($busqueda) ?>" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>
        </form>
      </nav>
    </div>
  </div>

  <!-- Tarjeta con listado -->
  <div class="row">
    <div class="col-12">
      <div class="card m-1">
        <!-- Cabecera: periodo / tipo / quincena -->
        <div class="card-header">
          <div class="row">
            <div class="col-10">
              <?php if ($primer): ?>
              <h2 class="text-secondary m-0">
                PERIODO <?= htmlspecialchars($primer['periodo']) ?>‑<?= htmlspecialchars($primer['anio']) ?>
                / <?= htmlspecialchars($primer['tipo_liquidacion']) ?>
                <?= $primer['quincena'] ? ' / ' . htmlspecialchars($primer['quincena']) : '' ?>
              </h2>
              <?php else: ?>
                <span class="text-danger">Sin resultados</span>
              <?php endif; ?>
            </div>
            <div class="col-1 align-items-center">
              <a href="home.php?menu=ver_liquidaciones" class="btn btn-outline-success btn-sm">Volver al Listado...</a>
            </div>
          </div>          
        </div>

        <!-- Tabla -->
        <?php if ($primer): ?>
          <div class="card-body m-2 p-0">
            <div class="table-responsive">
              <table class="table mb-0">
                <thead class="bg-info text-white">
                  <tr>
                    <th scope="col">Legajo</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($recibos as $r): ?>
                    <?php
                      $url = 'assets/recibos/' .
                             rawurlencode($r['anio'] . '-' . $r['periodo']) . '/' .
                             rawurlencode($r['recibo']);

                      
                    ?>
                    <tr>
                      <td><?= htmlspecialchars($r['legajo']) ?></td>
                      <td><?= htmlspecialchars($r['nombre']) ?></td>
                      <td>
                      <a class="btn btn-warning btn-sm" href="#" onclick="descargarPDF('<?= $url ?>')">
                        Ver Recibo de Sueldo
                      </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div><!-- /.table-responsive -->
          </div><!-- /.card-body -->
        <?php endif; ?>
      </div><!-- /.card -->
    </div><!-- /.col -->
  </div><!-- /.row -->
</div><!-- /.container -->
<script>

</script>
