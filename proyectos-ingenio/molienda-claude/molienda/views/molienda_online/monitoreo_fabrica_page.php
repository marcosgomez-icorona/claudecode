<?php
    include 'controller/molienda_online.php';

    $hora = $_POST['hora'] ?? '';
    $fechaindustrial_raw = $_POST['fechaindustrial'] ?? '';
    $fechaindustrial = preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaindustrial_raw) ? $fechaindustrial_raw : date('Y-m-d');

    $ind = obtiene_indicadores_fabrica();
    $opc = obtiene_indicadores_opc();
    $prom_turno_ant = obtiene_promedios_turno_anterior();
    $gas_prom_turno_ant = obtiene_gas_promedio_turno_anterior();
    $prom_consumo_8hs = obtiene_prom_consumo_8hs();

    // AÑO DE ZAFRA — desde la última fecha_pesada (no depende de fechaindustrial que puede ser NULL al inicio de zafra)
    $anio_zafra_row = ObtieneDatosSQL("SELECT YEAR(MAX(fecha_pesada)) AS anio FROM datos_Cania");
    $anio_zafra = ($anio_zafra_row[0] ?? [])['anio'] ?? date('Y');

    $acumulado = obtiene_molienda_acumulada() ?? [];
    $acumulado_cania = number_format((float)($acumulado['molienda_acumulada'] ?? 0), 0, ',', '.');

    $acumulado_molienda_dia = obtiene_acumulado_molienda_dia('hoy') ?? [];

    //PARADAS
    $consulta = 'paradas';
    $sql = EjecutarConsulta($consulta, $fechaindustrial, $hora);
    $paradas = ObtieneDatosSQL($sql);
    $cant_tiempo_parada = 0;
    foreach ($paradas as $parada):
        $cant_tiempo_parada = $cant_tiempo_parada + (int)($parada['t_neto_minutos'] ?? 0);
    endforeach;
    $cant_tiempo_parada = $cant_tiempo_parada . ' min.';

    $prom_consumo_8hs = obtiene_prom_consumo_8hs();

    // CAMIONES EN CANCHON
    $canchon = obtiene_camiones_canchon();
    $pre_ingreso = obtiene_pre_ingreso_canchon();

    // MOLIENDA EN CURSO (hora actual desde datos_Cania)
    $molienda_en_curso = obtiene_molienda_en_curso();

    // ESTADO MOLIENDA — semáforo
    // Semaforo de estado de molienda oculto temporalmente hasta ajustar obtiene_estado_molienda().

    // PROMEDIOS EN CANCHON
    $row_prom = ObtieneDatosSQL("
        SELECT ROUND(AVG(kilos), 0) AS prom_hora,
               ROUND(SUM(kilos) / NULLIF(COUNT(DISTINCT fechaindustrial), 0), 0) AS prom_dia
        FROM consumos_x_hora
        WHERE YEAR(fechaindustrial) = (SELECT YEAR(MAX(fechaindustrial)) FROM consumos_x_hora) AND kilos > 0");
    $r_prom = $row_prom[0] ?? [];
    $promedio_hora     = $r_prom['prom_hora'] ?? 0;
    $promedio_dia      = $r_prom['prom_dia']  ?? 0;
    $promedio_hora_fmt = number_format((float)$promedio_hora, 0, ',', '.');
    $promedio_dia_fmt  = number_format((float)$promedio_dia,  0, ',', '.');
?>

<!-- Barra superior -->
<div class="dash-header">
  <img src="assets/img/Logo-Ing La Corona.png" alt="Ingenio La Corona" style="height:32px; object-fit:contain;">
  <span class="fw-bold text-secondary" style="font-size:0.95rem;">Monitoreo Fábrica</span>
  <span class="text-muted small">Fecha: <?php echo date('d/m/Y'); ?> &nbsp;|&nbsp; Zafra: <?php echo $anio_zafra; ?></span>
  <span class="text-muted small ms-2">OPC: <?php echo $opc['opc_timestamp'] !== '--' ? date('H:i:s', strtotime($opc['opc_timestamp'])) : '--'; ?></span>
  <span class="ms-auto small fw-semibold text-secondary" id="hora_monitoreo_fabrica"></span>
</div>

<!-- Contenido principal -->
<div class="row g-0 p-1" style="height:calc(100vh - 70px); overflow:hidden;">

  <!-- Columna izquierda: monitoreo fábrica (60%) -->
  <div class="pe-1 d-flex flex-column" style="width:60%; height:100%;">
    <div class="card p-2 flex-grow-1" style="overflow:auto; min-height:0;">
      <?php include 'views/molienda_online/monitoreo_fabrica.php'; ?>
    </div>
  </div>

  <!-- Columna derecha: igual que molienda_online (40%) -->
  <div class="ps-1 d-flex flex-column gap-1" style="width:40%; height:100%;">

    <div class="card p-2 flex-grow-1" style="overflow-y:auto; min-height:0;">
      <?php include 'datos_molienda_hora.php'; ?>
    </div>

    <div class="card p-2 flex-shrink-0">
      <div class="d-flex align-items-center flex-wrap gap-2" style="font-size:0.8rem;">
        <strong class="text-secondary">En Canchón</strong>
        <span class="badge bg-secondary" title="Camiones con pre-ingreso"><?php echo $pre_ingreso; ?> PI</span>
        <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#camionescanchonModal">Evolución x Hora</button>
        <div class="d-flex align-items-center gap-1">
          <span class="fw-semibold text-secondary">Acumulado:</span>
          <input type="text" class="form-control form-control-sm" style="width:95px;" value="<?php echo $acumulado_cania ?? 0; ?>">
        </div>
        <div class="d-flex align-items-center gap-1">
          <span class="fw-semibold text-secondary">Prom/Hora:</span>
          <input type="text" class="form-control form-control-sm" style="width:75px;" value="<?php echo $promedio_hora_fmt; ?>">
        </div>
        <div class="d-flex align-items-center gap-1">
          <span class="fw-semibold text-secondary">Prom/Día:</span>
          <input type="text" class="form-control form-control-sm" style="width:75px;" value="<?php echo $promedio_dia_fmt; ?>">
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Modals -->
<div class="modal fade" id="camionescanchonModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h5 class="modal-title">Evolución x Hora — Canchón</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-2">
        <?php include 'camiones_en_canchon.php'; ?>
      </div>
      <div class="modal-footer py-1">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="indicadoresFabricacionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width:95vw;">
    <div class="modal-content" style="max-height:90vh;">
      <div class="modal-header bg-light sticky-top">
        <h5 class="modal-title text-secondary">Indicadores de Fabricación <span class="badge bg-secondary">#10</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body fw-bold p-2 p-md-3" style="overflow-y:auto; overflow-x:auto;">
        <div class="container">
          <?php include 'views/indicadores_fabrica/ind_fabricacion.php'; ?>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="indicadoresTrapicheModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width:95vw;">
    <div class="modal-content" style="max-height:90vh;">
      <div class="modal-header bg-light sticky-top">
        <h5 class="modal-title text-secondary">Indicadores de Trapiche <span class="badge bg-secondary">#7</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body fw-bold p-2 p-md-3" style="overflow-y:auto; overflow-x:auto;">
        <div class="container">
          <?php include 'views/indicadores_fabrica/ind_trapiche.php'; ?>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="indicadoresCalderaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width:95vw;">
    <div class="modal-content" style="max-height:90vh;">
      <div class="modal-header bg-light sticky-top">
        <h5 class="modal-title text-secondary">Indicadores de Caldera <span class="badge bg-secondary">#8</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body fw-bold p-2 p-md-3" style="overflow-y:auto; overflow-x:auto;">
        <div class="container">
          <?php include 'views/indicadores_fabrica/ind_caldera.php'; ?>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="indicadoresUsinaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width:95vw;">
    <div class="modal-content" style="max-height:90vh;">
      <div class="modal-header bg-light sticky-top">
        <h5 class="modal-title text-secondary">Indicadores de Usina <span class="badge bg-secondary">#10</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body fw-bold p-2 p-md-3" style="overflow-y:auto; overflow-x:auto;">
        <div class="container">
          <?php include 'views/indicadores_fabrica/ind_usina.php'; ?>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="indicadoresDestileriaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width:95vw;">
    <div class="modal-content" style="max-height:90vh;">
      <div class="modal-header bg-light sticky-top">
        <h5 class="modal-title text-secondary">Indicadores de Destileria <span class="badge bg-secondary">#4</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body fw-bold p-2 p-md-3" style="overflow-y:auto; overflow-x:auto;">
        <div class="container">
          <?php include 'views/indicadores_fabrica/ind_destileria.php'; ?>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="analisisazucarModal" tabindex="-1">
  <div class="modal-dialog modal-xl" style="max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h5 class="modal-title">Análisis de Azúcar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div id="contenidoModalAnalisis" class="modal-body p-2">
        <div class="text-center">Cargando datos...</div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="resumenfabricaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl" style="max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h5 class="modal-title">Resumen de Fábrica</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-2">
        <?php include 'views/indicadores_fabrica/resumen_fabrica.php'; ?>
      </div>
      <div class="modal-footer py-1">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function () {
  var COLORES = {
    verde:    { bg:'rgba(25,135,84,0.10)',  borde:'#198754', dot:'#198754' },
    amarillo: { bg:'rgba(230,168,23,0.15)', borde:'#e6a817', dot:'#e6a817' },
    rojo:     { bg:'rgba(220,53,69,0.12)',  borde:'#dc3545', dot:'#dc3545' },
    gris:     { bg:'rgba(108,117,125,0.07)',borde:'#adb5bd', dot:'#adb5bd' }
  };
  function num(s) {
    if (!s || s === '--') return NaN;
    return parseFloat(String(s).replace(',', '.').trim());
  }
  function calcEstado(val, obj, dir) {
    obj = String(obj || '').trim();
    if (!obj || obj === '--') return 'gris';
    var v = num(val);
    if (isNaN(v)) return 'gris';
    var m = obj.match(/^([\d.,]+)\s*(?:a|-)\s*([\d.,]+)$/i);
    if (m) {
      var mn = num(m[1]), mx = num(m[2]);
      if (isNaN(mn) || isNaN(mx)) return 'gris';
      if (v >= mn && v <= mx) return 'verde';
      var mg = ((mn + mx) / 2) * 0.10;
      return (v >= mn - mg && v <= mx + mg) ? 'amarillo' : 'rojo';
    }
    var o = num(obj);
    if (isNaN(o)) return 'gris';
    if (o === 0) return (v === 0) ? 'verde' : 'rojo';
    if (dir === 'less') {
      if (v <= o) return 'verde';
      return ((v - o) / o <= 0.10) ? 'amarillo' : 'rojo';
    }
    var d = Math.abs(v - o) / o;
    return d <= 0.05 ? 'verde' : d <= 0.10 ? 'amarillo' : 'rojo';
  }
  function aplicarSemaforos() {
    document.querySelectorAll('.semaforo-card').forEach(function (card) {
      var estado = calcEstado(
        card.dataset.valor     || '--',
        card.dataset.objetivo  || '',
        card.dataset.direction || 'target'
      );
      var c = COLORES[estado];
      card.style.backgroundColor = c.bg;
      card.style.borderLeft = '4px solid ' + c.borde;
      card.style.borderRadius = '0.5rem';
      var dot = card.querySelector('.semaforo-dot');
      if (dot) {
        dot.style.backgroundColor = c.dot;
        dot.title = estado.charAt(0).toUpperCase() + estado.slice(1);
      }
    });
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', aplicarSemaforos);
  } else {
    aplicarSemaforos();
  }
})();
</script>

<script>
  function cargarAnalisis(horaSeleccionada = '') {
    const modal = new bootstrap.Modal(document.getElementById('analisisazucarModal'));
    if (!document.querySelector('.modal.show')) modal.show();
    const contenedor = document.getElementById('contenidoModalAnalisis');
    fetch('home.php?menu=analisis_azucar', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'hora=' + encodeURIComponent(horaSeleccionada)
    })
    .then(response => response.text())
    .then(data => {
      contenedor.innerHTML = data;
      document.getElementById('hora').addEventListener('change', function() {
        cargarAnalisis(this.value);
      });
    });
  }

  function actualizarHora() {
    const ahora = new Date();
    const hora = ahora.toLocaleTimeString("es-AR", { hour:"2-digit", minute:"2-digit", second:"2-digit" });
    document.getElementById("hora_monitoreo_fabrica").innerHTML =
      `<p><strong>Monitoreo Fábrica <span class="text-primary ms-2">${hora}</span></strong></p>`;
  }
  actualizarHora();
  setInterval(actualizarHora, 1000);

  window.onload = function() {
    setTimeout(function() { location.reload(); }, 300000);
  };
</script>
