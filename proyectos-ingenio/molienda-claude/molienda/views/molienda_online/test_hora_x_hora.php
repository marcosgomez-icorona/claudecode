<?php

$paradas = [];
$errorParadas = '';
$fechaindustrial = date('Y-m-d');

function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function cargarParadasMysqli(string $fechaindustrial): array
{
    include_once __DIR__ . '/../../conexiones/conexion.php';

    $mysqli = conexion_db();
    if (!$mysqli || $mysqli->connect_errno) {
        throw new RuntimeException('No se pudo conectar con la base de datos.');
    }

    $sql = "SELECT fechaindustrial, desde AS DESDE, hasta AS HASTA,
                   t_neto AS T_Neto, t_neto_minutos, origen, maquina, motivo
            FROM paradas_fabrica
            WHERE fechaindustrial = ?
            ORDER BY fechaindustrial DESC, desde ASC";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        $error = $mysqli->error;
        $mysqli->close();
        throw new RuntimeException('No se pudo preparar la consulta de paradas: ' . $error);
    }

    $stmt->bind_param('s', $fechaindustrial);
    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();
        $mysqli->close();
        throw new RuntimeException('No se pudo ejecutar la consulta de paradas: ' . $error);
    }

    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $paradas[] = $row;
    }

    $result->free();
    $stmt->close();
    $mysqli->close();

    return $paradas;
}

try {
    $paradas = cargarParadasMysqli($fechaindustrial);
} catch (Throwable $e) {
    error_log('Error test_hora_x_hora mysqli: ' . $e->getMessage());
    $errorParadas = $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Paradas hora x hora</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <main class="container-fluid py-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <div>
        <h1 class="h5 mb-1">Paradas hora x hora</h1>
        <div class="text-muted small">Fuente: paradas_fabrica | Fecha: <?php echo h(date('d/m/Y', strtotime($fechaindustrial))); ?></div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-sm table-bordered table-striped align-middle mb-0">
        <thead class="table-light text-center">
          <tr>
            <th>Desde</th>
            <th>Hasta</th>
            <th>T Neto</th>
            <th class="text-start">Origen</th>
            <th class="text-start">Maquina</th>
            <th class="text-start">Motivo</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($errorParadas !== ''): ?>
          <tr>
            <td colspan="6" class="text-center text-danger"><?php echo h($errorParadas); ?></td>
          </tr>
          <?php elseif (empty($paradas)): ?>
          <tr>
            <td colspan="6" class="text-center text-muted">Sin paradas registradas para la fecha actual.</td>
          </tr>
          <?php else: ?>
          <?php foreach ($paradas as $item): ?>
          <tr>
            <td class="text-center"><?php echo h($item['DESDE'] ?? ''); ?></td>
            <td class="text-center"><?php echo h($item['HASTA'] ?? ''); ?></td>
            <td class="text-center"><?php echo h($item['T_Neto'] ?? ''); ?></td>
            <td class="text-start"><?php echo h($item['origen'] ?? ''); ?></td>
            <td class="text-start"><?php echo h($item['maquina'] ?? ''); ?></td>
            <td class="text-start"><?php echo h($item['motivo'] ?? ''); ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>
