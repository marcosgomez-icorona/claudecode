<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Subir Recibos PDF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container ">
  <h3 class="mb-4">Subir Recibos en PDF</h3>
  <div class="row m-2">
    <div class="col-6">
      <div class="box shadow p-3 mb-5 bg-body rounded row justify-content-center">
        <form action="home.php?accion=subir_recibos&usuario=<?php echo $_GET['usuario'] ?? '' ;?>" method="post" enctype="multipart/form-data" class="mb-4">
              <div class="mb-3">
                <label for="archivos" class="form-label">Seleccionar archivos PDF:</label>
                <input class="form-control" type="file" name="archivos[]" id="archivos" accept=".pdf" multiple required>
              </div>
              <button type="submit" class="btn btn-primary">Subir Recibos</button>
        </form>        
      </div>
    </div>
  </div>
</div>
</body>
</html>
