<?php  
include_once 'controller/personal.php';
include_once 'controller/usuarios.php';

$busqueda = $_POST['busqueda'] ?? '';
$resultado_personal = listado_personal($busqueda);
?>

<div class="container">
  <!-- Título -->
  <div class="row">
    <div class="col-12">
      <h1 class="display-4 text-center text-muted">Listado de Empleados</h1>
    </div>
  </div>

  <!-- Barra de búsqueda -->
  <div class="row">
    <div class="col-12">
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <form action="home.php?menu=ver_personal" class="form-inline my-1 my-lg-0 w-100" method="POST">
          <input class="form-control mr-sm-2 flex-grow-1" type="search" name="busqueda"
                 placeholder="Buscar por nombre, DNI, legajo..." value="<?= htmlspecialchars($busqueda ?? '') ?>" />
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>
        </form>
      </nav>
    </div>
  </div>

  <!-- Tarjeta con tabla -->
  <div class="row">
    <div class="col-12">
      <div class="card m-1">
        <!--
        <div class="card-header d-flex justify-content-between align-items-center">
          <a href="home.php?menu=agregar_persona" class="btn btn-success btn-sm">Agregar Persona</a>
        </div>
        -->
        <div class="card-body m-2">
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead class="bg-info text-white">
                <tr>
                  <th>Legajo</th>
                  <th>DNI</th>
                  <th>Nombre</th>
                  <th>Tipo de Convenio</th>
                  <th>Tipo de Liquidación</th>
                  <!--
                  <th>Acciones</th>
                  -->
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $resultado_personal->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['legajo'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['dni'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['nombre'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['tipo_convenio'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['tipo_liquidacion'] ?? '') ?></td>
                    <!--                    
                    <td>
                      <div class="btn-group btn-group-sm mb-1" role="group">
                        <?php
                          $legajo_encrip = base64_encode($row['legajo']);
                          $url_ver_recibos = "home.php?accion=ver_recibos_empleado&legajo=" . urlencode($legajo_encrip);
                        ?>
                        <a class="btn btn-info btn-sm" href="<?= $url_ver_recibos ?>">Ver Recibos de Sueldo</a>
                      </div>
                      <div class="btn-group btn-group-sm" role="group">
                        <a class="btn btn-warning btn-sm"
                           href="home.php?menu=modificar_persona&id_personal=<?= htmlspecialchars($row['id_personal'] ?? '') ?>">
                          Modificar
                        </a>
                      </div>
                    </td>
                  -->
                  </tr>
                <?php endwhile; ?>
                <?php if ($resultado_personal->num_rows === 0): ?>
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