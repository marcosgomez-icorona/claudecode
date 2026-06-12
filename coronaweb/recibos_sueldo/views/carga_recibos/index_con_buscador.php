<?php
include 'conexion.php';

// Filtros
$filtro_legajo = $_GET['legajo'] ?? '';
$filtro_periodo = $_GET['periodo'] ?? '';

$sql = "SELECT * FROM recibos WHERE 1";
if (!empty($filtro_legajo)) {
    $sql .= " AND legajo = '" . $mysqli->real_escape_string($filtro_legajo) . "'";
}
if (!empty($filtro_periodo)) {
    $sql .= " AND periodo = '" . $mysqli->real_escape_string($filtro_periodo) . "'";
}
$sql .= " ORDER BY fecha_subida DESC LIMIT 100";
$resultado = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Subir Recibos PDF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h3 class="mb-4">Subir Recibos en PDF</h3>

  <form action="subir_recibos.php" method="post" enctype="multipart/form-data" class="mb-4">
    <div class="mb-3">
      <label for="archivos" class="form-label">Seleccionar archivos PDF:</label>
      <input class="form-control" type="file" name="archivos[]" id="archivos" accept=".pdf" multiple required>
    </div>
    <button type="submit" class="btn btn-primary">Subir Recibos</button>
  </form>

  <?php if (!empty($_GET['msg'])): ?>
    <div class="alert alert-info"><?= htmlspecialchars($_GET['msg']) ?></div>
  <?php endif; ?>

  <hr>

  <h4>Buscar Recibos</h4>
  <form method="get" class="row g-3 mb-3">
    <div class="col-md-4">
      <input type="text" name="legajo" class="form-control" placeholder="Nro. Legajo" value="<?= htmlspecialchars($filtro_legajo) ?>">
    </div>
    <div class="col-md-4">
      <input type="text" name="periodo" class="form-control" placeholder="Periodo (Ej: 202506)" value="<?= htmlspecialchars($filtro_periodo) ?>">
    </div>
    <div class="col-md-4">
      <button class="btn btn-secondary" type="submit">Filtrar</button>
      <button class="btn btn-success ms-2" onclick="exportarExcel()">Exportar a Excel</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-bordered table-hover" id="tabla_recibos">
      <thead class="table-light">
        <tr>
          <th>Archivo</th>
          <th>Legajo</th>
          <th>Periodo</th>
          <th>Tipo</th>
          <th>Fecha Subida</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $resultado->fetch_assoc()): ?>
          <tr>
            <td><a href="assets/recibos/<?= urlencode($row['nombre']) ?>" target="_blank"><?= htmlspecialchars($row['nombre']) ?></a></td>
            <td><?= htmlspecialchars($row['legajo']) ?></td>
            <td><?= htmlspecialchars($row['periodo']) ?></td>
            <td><?= htmlspecialchars($row['tipo_liquidacion']) ?></td>
            <td><?= htmlspecialchars($row['fecha_subida']) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function exportarExcel() {
  let tabla = document.getElementById("tabla_recibos").outerHTML;
  let data_type = 'data:application/vnd.ms-excel';
  let table_html = tabla.replace(/ /g, '%20');
  let a = document.createElement('a');
  a.href = data_type + ', ' + table_html;
  a.download = 'recibos.xls';
  a.click();
}
</script>
</body>
</html>
