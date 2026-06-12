<?php

/*
 * Configuracion Supabase REST API.
 * No requiere pdo_pgsql ni cambios en php.ini.
 */
$supabaseConfig = [
    'url' => 'https://ingenio-supabase.srv878399.hstgr.cloud',
    'schema' => 'production',
    'anon_key' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJyb2xlIjoiYW5vbiIsImlzcyI6InN1cGFiYXNlIiwiaWF0IjoxNzc1MTg1MjAwLCJleHAiOjE5MzI5NTE2MDB9.oRVMWDSlF_AHFlCuGDypgcOuKraw9Qs2RPtVI2ug0Co',
    'resources' => ['v_dia_industrial_hxh'],
    'timestamp_column' => 'periodo',
];

$rows = [];
$error = '';
$resourceUsed = '';
$timestampColumn = $supabaseConfig['timestamp_column'];
$fecha = isset($_GET['fecha']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha'])
    ? $_GET['fecha']
    : date('Y-m-d');
$fechaDesde = $fecha . ' 00:00:00';
$fechaHasta = date('Y-m-d', strtotime($fecha . ' +1 day')) . ' 00:00:00';

function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function supabaseRequest(string $url, string $anonKey, string $schema): array
{
    $headers = [
        'apikey: ' . $anonKey,
        'Authorization: Bearer ' . $anonKey,
        'Accept: application/json',
        'Accept-Profile: ' . $schema,
    ];

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 20,
        ]);

        $body = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            throw new RuntimeException('Error cURL: ' . $curlError);
        }

        return [$httpCode, $body];
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", $headers),
            'timeout' => 20,
            'ignore_errors' => true,
        ],
    ]);

    $body = file_get_contents($url, false, $context);
    if ($body === false) {
        throw new RuntimeException('No se pudo ejecutar la consulta HTTP a Supabase.');
    }

    $httpCode = 0;
    if (!empty($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $match)) {
        $httpCode = (int)$match[1];
    }

    return [$httpCode, $body];
}

function decodeSupabaseError(string $body): string
{
    $data = json_decode($body, true);
    if (!is_array($data)) {
        return $body;
    }

    return $data['message'] ?? $data['error_description'] ?? $data['hint'] ?? $body;
}

function firstValue(array $row, array $keys)
{
    foreach ($keys as $key) {
        if (array_key_exists($key, $row) && $row[$key] !== null && $row[$key] !== '') {
            return $row[$key];
        }
    }

    return null;
}

function fmtNumber($value, int $decimals = 0): string
{
    if ($value === null || $value === '') {
        return '-';
    }

    return number_format((float)$value, $decimals, ',', '.');
}

function fmtTonsFromKg($kg, int $decimals = 2): string
{
    if ($kg === null || $kg === '') {
        return '-';
    }

    return fmtNumber(((float)$kg) / 1000, $decimals);
}

function avgValue(array $rows, array $keys)
{
    $sum = 0;
    $count = 0;

    foreach ($rows as $row) {
        $value = firstValue($row, $keys);
        if ($value !== null && is_numeric($value)) {
            $sum += (float)$value;
            $count++;
        }
    }

    return $count > 0 ? $sum / $count : null;
}

function sumValue(array $rows, array $keys)
{
    $sum = 0;
    $found = false;

    foreach ($rows as $row) {
        $value = firstValue($row, $keys);
        if ($value !== null && is_numeric($value)) {
            $sum += (float)$value;
            $found = true;
        }
    }

    return $found ? $sum : null;
}

function maxValue(array $rows, array $keys)
{
    $max = null;

    foreach ($rows as $row) {
        $value = firstValue($row, $keys);
        if ($value !== null && is_numeric($value)) {
            $value = (float)$value;
            $max = $max === null ? $value : max($max, $value);
        }
    }

    return $max;
}

function hourRangeLabel(array $row): string
{
    $hora = firstValue($row, ['hora']);
    if (!$hora) {
        return (string)(firstValue($row, ['etiqueta']) ?? '-');
    }

    $end = strtotime((string)$hora);
    if ($end === false) {
        return (string)(firstValue($row, ['etiqueta']) ?? $hora);
    }

    $start = strtotime('-1 hour', $end);
    return date('H:i', $start) . '-' . date('H:i', $end);
}

try {
    $baseUrl = rtrim($supabaseConfig['url'], '/');
    $schema = trim($supabaseConfig['schema'] ?? 'public');
    $anonKey = trim($supabaseConfig['anon_key']);

    if ($baseUrl === '' || $baseUrl === 'TU_SUPABASE_URL') {
        throw new RuntimeException('Falta configurar la URL de Supabase.');
    }

    if ($anonKey === '' || $anonKey === 'TU_SUPABASE_ANON_KEY') {
        throw new RuntimeException('Falta configurar la anon key de Supabase.');
    }

    $lastErrors = [];
    foreach ($supabaseConfig['resources'] as $resource) {
        $query = http_build_query([
            'select' => '*',
            'order' => 'periodo.asc',
        ]);
        $url = $baseUrl . '/rest/v1/' . rawurlencode($resource) . '?' . $query;

        [$httpCode, $body] = supabaseRequest($url, $anonKey, $schema);
        $data = json_decode($body, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            if (!is_array($data)) {
                throw new RuntimeException('Supabase no devolvio un JSON valido para ' . $resource . '.');
            }

            $rows = $data;
            $resourceUsed = $resource;
            break;
        }

        $lastErrors[] = $resource . '.' . $timestampColumn . ' -> HTTP ' . $httpCode . ': ' . decodeSupabaseError($body);
    }

    if ($resourceUsed === '') {
        throw new RuntimeException('No se pudo leer ninguna vista por Supabase API. Intentos: ' . implode(' | ', $lastErrors));
    }
} catch (Throwable $e) {
    error_log('Error conexion_test_supabase API: ' . $e->getMessage());
    $error = $e->getMessage();
}

$cards = [
    //['class' => 'blue', 'icon' => '#', 'label' => 'Molienda', 'value' => fmtTonsFromKg(sumValue($rows, ['molienda_kg']), 1), 'unit' => 't', 'note' => 'acumulado del día'],
    ['class' => 'amber', 'icon' => 'G', 'label' => 'Gas', 'value' => fmtNumber(sumValue($rows, ['gas_consumo']), 0), 'unit' => 'm3', 'note' => 'acumulado del día'],
    ['class' => 'green', 'icon' => 'H', 'label' => 'Hum. Bagazo', 'value' => fmtNumber(avgValue($rows, ['bagazo_humedad']), 1), 'unit' => '%', 'note' => 'promedio del día'],
    ['class' => 'green', 'icon' => 'B', 'label' => 'Bolsas', 'value' => fmtNumber(sumValue($rows, ['bolsas_azucar']), 0), 'unit' => 'bls', 'note' => 'acumulado del día'],
    ['class' => 'blue', 'icon' => 'P', 'label' => 'Pol Bagazo', 'value' => fmtNumber(avgValue($rows, ['bagazo_pol']), 2), 'unit' => '%', 'note' => 'promedio del día'],
    ['class' => 'amber', 'icon' => 'C', 'label' => 'Pol Cachaza', 'value' => fmtNumber(avgValue($rows, ['cachaza_pol']), 2), 'unit' => '%', 'note' => 'promedio del día'],
    ['class' => 'muted', 'icon' => 'A', 'label' => 'Alcohol Ind.', 'value' => fmtNumber(sumValue($rows, ['alcohol_gl']), 0), 'unit' => '', 'note' => 'acumulado del día'],
    ['class' => 'teal', 'icon' => 'U', 'label' => 'Color', 'value' => fmtNumber(avgValue($rows, ['color_azucar']), 0), 'unit' => 'UI', 'note' => 'promedio del día'],    
];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Molienda y Produccion Tiempo Real</title>
  <style>
    :root {
      --bg: #eef3f8;
      --panel: rgba(255, 255, 255, 0.82);
      --line: #d9e2eb;
      --text: #253246;
      --muted: #75839a;
      --blue: #3f73aa;
      --green: #2f8a70;
      --teal: #4a9a8b;
      --amber: #c98427;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      min-height: 100vh;
      background:
        linear-gradient(135deg, rgba(255,255,255,0.9), rgba(222,232,243,0.72)),
        var(--bg);
      color: var(--text);
      font-family: "Inter", "Segoe UI Variable", "Segoe UI", system-ui, -apple-system, BlinkMacSystemFont, Arial, sans-serif;
      font-size: 14px;
      font-weight: 400;
      letter-spacing: 0;
      -webkit-font-smoothing: antialiased;
      text-rendering: geometricPrecision;
    }

    .screen {
      padding: 8px;
    }

    .realtime-panel {
      min-height: calc(100vh - 16px);
      padding: 14px 12px 10px;
      border: 1px solid #ccd8e4;
      border-radius: 22px;
      background:
        radial-gradient(circle at 18% 0%, rgba(255,255,255,0.96), transparent 36%),
        linear-gradient(145deg, rgba(255,255,255,0.88), rgba(245,248,252,0.84));
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.95), 0 10px 28px rgba(50, 75, 100, 0.13);
      overflow: hidden;
    }

    .header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 18px;
    }

    .header-icon {
      width: 32px;
      height: 32px;
      display: grid;
      place-items: center;
      border-radius: 12px;
      background: #f4f8fb;
      border: 1px solid #dbe6ef;
      color: var(--blue);
      font-weight: 800;
    }

    .title {
      margin: 0;
      color: #38699d;
      font-size: 14px;
      line-height: 1.2;
      text-transform: uppercase;
      font-weight: 750;
      letter-spacing: 0;
    }

    .subtitle {
      margin-top: 3px;
      color: #657187;
      font-size: 11px;
      letter-spacing: 2.6px;
      text-transform: uppercase;
      font-weight: 500;
    }

    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
      gap: 6px;
      margin-bottom: 18px;
    }

    .metric {
      min-height: 80px;
      padding: 9px 10px 8px;
      border: 1px solid #d6e0ea;
      border-radius: 16px;
      background: rgba(255,255,255,0.62);
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.9);
    }

    .metric.blue { border-color: #bfd2e7; }
    .metric.green { border-color: #bfded3; }
    .metric.teal { border-color: #bddfd8; }
    .metric.amber { border-color: #e7d0b5; }

    .metric-head {
      display: flex;
      align-items: center;
      gap: 6px;
      color: #7a8498;
      font-size: 11.5px;
      font-weight: 720;
      letter-spacing: 0.9px;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .metric-icon {
      color: inherit;
      font-size: 11px;
      font-weight: 750;
    }

    .metric-value {
      margin-top: 5px;
      color: #51677f;
      font-size: 20px;
      line-height: 1;
      font-weight: 650;
      letter-spacing: 0;
      white-space: nowrap;
      font-variant-numeric: tabular-nums;
    }

    .metric.blue .metric-value { color: var(--blue); }
    .metric.green .metric-value { color: var(--green); }
    .metric.teal .metric-value { color: var(--teal); }
    .metric.amber .metric-value { color: var(--amber); }

    .metric-unit {
      margin-left: 2px;
      color: #5f6b7c;
      font-size: 12px;
      font-weight: 500;
    }

    .metric-note {
      margin-top: 8px;
      color: #7c879c;
      font-size: 12px;
      font-weight: 400;
      white-space: nowrap;
    }

    .table-wrap {
      width: 100%;
      overflow-x: auto;
      padding-bottom: 9px;
    }

    .realtime-table {
      width: 100%;
      min-width: 1040px;
      border-collapse: collapse;
      font-size: 16px;
      font-weight: 400;
    }

    .realtime-table th {
      padding: 7px 12px 10px;
      border-bottom: 1px solid var(--line);
      color: #73809a;
      font-size: 15px;
      letter-spacing: 0.35px;
      text-align: right;
      text-transform: uppercase;
      white-space: nowrap;
      font-weight: 650;
    }

    .realtime-table th:first-child,
    .realtime-table td:first-child {
      text-align: left;
    }

    .realtime-table td {
      padding: 9px 12px;
      border-bottom: 1px solid var(--line);
      color: #223044;
      text-align: right;
      font-variant-numeric: tabular-nums;
      font-weight: 400;
      letter-spacing: 0;
      white-space: nowrap;
    }

    .realtime-table .hour {
      color: #4d5a70;
      font-family: "Cascadia Mono", "Consolas", "SFMono-Regular", "Courier New", monospace;
      font-style: italic;
      font-size: 16px;
      font-weight: 400;
    }

    .th-blue { color: var(--blue) !important; }
    .th-green { color: var(--green) !important; }
    .th-teal { color: var(--teal) !important; }
    .th-amber { color: var(--amber) !important; }
    .dash { color: #97a5b8 !important; }

    .status {
      margin: 10px 0 0;
      padding: 10px 12px;
      border: 1px solid #e0c9c9;
      border-radius: 10px;
      background: #fff4f4;
      color: #8c2f2f;
      font-size: 13px;
    }

    .empty {
      margin: 12px 0 0;
      padding: 12px;
      border: 1px solid #d9e2eb;
      border-radius: 10px;
      background: rgba(255,255,255,0.7);
      color: var(--muted);
      text-align: center;
    }

    .scroll-hint {
      height: 6px;
      margin-top: 9px;
      border-radius: 999px;
      background: #c6ced8;
    }

    @media (max-width: 1180px) {
      .cards { grid-template-columns: repeat(4, minmax(130px, 1fr)); }
    }

    @media (max-width: 640px) {
      .screen { padding: 0; }
      .realtime-panel { min-height: 100vh; border-radius: 0; }
      .cards { grid-template-columns: repeat(2, minmax(130px, 1fr)); }
      .metric-value { font-size: 23px; }
    }
  </style>
</head>
<body>
  <main class="screen">
    <section class="realtime-panel">
      <header class="header">
        <div class="header-icon">▣</div>
        <div>
          <h1 class="title">Molienda y Produccion Tiempo Real</h1>
          <div class="subtitle">Turno en curso · Acumulado hora a hora</div>
        </div>
      </header>

      <div class="cards">
        <?php foreach ($cards as $card): ?>
          <article class="metric <?php echo h($card['class']); ?>">
            <div class="metric-head"><span class="metric-icon"><?php echo h($card['icon']); ?></span><?php echo h($card['label']); ?></div>
            <div class="metric-value">
              <?php echo h($card['value']); ?><span class="metric-unit"><?php echo h($card['unit']); ?></span>
            </div>
            <div class="metric-note"><?php echo h($card['note']); ?></div>
          </article>
        <?php endforeach; ?>
      </div>

      <?php if ($error !== ''): ?>
        <div class="status"><strong>Error:</strong> <?php echo h($error); ?></div>
      <?php elseif (empty($rows)): ?>
        <div class="empty">La consulta ejecuto correctamente, pero no devolvio datos para el turno actual.</div>
      <?php else: ?>
        <div class="table-wrap">
          <table class="realtime-table">
            <thead>
              <tr>
                <th>Periodo</th>
                <th class="th-blue">Molienda (t)</th>
                <th class="th-amber">Gas (m3)</th>
                <th class="th-green">Bolsas</th>
                <th class="th-teal">Color (UI)</th>
                <th class="th-green">Hum. Baz. (%)</th>
                <th class="th-blue">Pol Baz. (%)</th>
                <th class="th-amber">Pol Cach. (%)</th>
                <th>Alcohol In</th>
                <th class="th-muted">Paradas (min)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rows as $row): ?>
                <?php
                  $periodo = firstValue($row, ['periodo']);
                  $moliendaKg = firstValue($row, ['molienda_kg']);
                  $gas = firstValue($row, ['gas_consumo']);
                  $bolsas = firstValue($row, ['bolsas_azucar']);
                  $color = firstValue($row, ['color_azucar']);
                  $humBagazo = firstValue($row, ['bagazo_humedad']);
                  $polBagazo = firstValue($row, ['bagazo_pol']);
                  $polCachaza = firstValue($row, ['cachaza_pol']);
                  $alcohol = firstValue($row, ['alcohol_gl']);
                  $paradas = firstValue($row, ['paradas_minutos']);
                ?>
                <tr>
                  <td class="hour"><?php echo h($periodo); ?></td>
                  <td><?php echo h(fmtTonsFromKg($moliendaKg, 2)); ?></td>
                  <td class="<?php echo $gas === null ? 'dash' : ''; ?>"><?php echo h(fmtNumber($gas, 0)); ?></td>
                  <td class="<?php echo $bolsas === null ? 'dash' : ''; ?>"><?php echo h(fmtNumber($bolsas, 0)); ?></td>
                  <td class="<?php echo $color === null ? 'dash' : ''; ?>"><?php echo h(fmtNumber($color, 0)); ?></td>
                  <td class="<?php echo $humBagazo === null ? 'dash' : ''; ?>"><?php echo h(fmtNumber($humBagazo, 2)); ?></td>
                  <td class="<?php echo $polBagazo === null ? 'dash' : ''; ?>"><?php echo h(fmtNumber($polBagazo, 2)); ?></td>
                  <td class="<?php echo $polCachaza === null ? 'dash' : ''; ?>"><?php echo h(fmtNumber($polCachaza, 2)); ?></td>
                  <td class="<?php echo $alcohol === null ? 'dash' : ''; ?>"><?php echo h(fmtNumber($alcohol, 0)); ?></td>
                  <td class="<?php echo $paradas === null ? 'dash' : ''; ?>"><?php echo h(fmtNumber($paradas, 0)); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="scroll-hint"></div>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
