<?php
  include_once 'controller/molienda_online.php';
  $ind = obtiene_indicadores_fabrica();
  $opc = obtiene_indicadores_opc();
  $gas_prom_turno_ant = obtiene_gas_promedio_turno_anterior();
  // Si está en indicadores_fabrica mostramos dos columnas, si no ocupa todo el ancho
  if(empty($_GET['menu'])) $_GET['menu']= '';
  $colLeft = ($_GET['menu']=='indicadores_fabrica' ) ? 'col-12 col-lg-7' : 'col-12';
  $colRight = 'col-12 col-lg-5';
?>
<style>
  .compact-spacing {
    line-height: 1;
  }
  .compact-spacing .row {
    margin-top: 0.5rem !important;
  }
  
  .compact-spacing h1, 
  .compact-spacing h2, 
  .compact-spacing h3, 
  .compact-spacing h4, 
  .compact-spacing h5, 
  .compact-spacing h6 {
    margin-bottom: 0.3rem;
    line-height: 1.2;
  }
  .compact-spacing p {
    margin-bottom: 0.3rem;
  }
</style>


  <div class="card m-1 p-2">      
    <div class="row g-2"> <!-- Reducido de g-3 a g-2 -->
      <h5>Panel de Fabrica - Sala de Control
        <small class="text-muted ms-2" style="font-size:0.65rem;">
          OPC: <?php echo $opc['opc_timestamp'] !== '--' ? date('H:i:s', strtotime($opc['opc_timestamp'])) : '--'; ?>
        </small>
      </h5>
      <!-- Columna izquierda -->
      <div class="col-12">
        <div class="row "> <!-- Reducido de g-3 a g-2 -->
          <div class="col col-md-6 text-start">
            <?php include 'ind_fabricacion.php';?>
          </div>
          <div class="col col-md-6 text-start">
            <?php include 'ind_caldera.php';?>
          </div>
        </div>  
        <div class="col-12">
        <div class="row "> <!-- Reducido de g-3 a g-2 -->
          <div class="col col-md-6 text-start">
            <?php include 'ind_usina.php';?>
          </div>
          <div class="col col-md-6 text-start">
            <?php include 'ind_destileria.php';?>
          </div>
        </div>       
        <div class="row g-2"> <!-- Reducido de g-3 a g-2 -->
          <div class="col col-md-6 text-start">
            <?php include 'ind_trapiche.php';?>
          </div>
          <div class="col col-md-6 text-start">
            <?php ?>
          </div>
        </div>         
      </div>       
            <!-- Columna derecha -->
      <?php if($_GET['menu']=='indicadores_fabrica'){ ?>
      <div class="col-7">
        <div class="row">
          <div class="col-12">
            <?php include 'views/indicadores_fabrica/datos_fabrica_hora.php';?>
          </div>
        </div>
      </div>
      <?php } ?>

    </div>
  </div>

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

  function aplicar() {
    document.querySelectorAll('.semaforo-card').forEach(function (card) {
      var estado = calcEstado(
        card.dataset.valor     || '--',
        card.dataset.objetivo  || '',
        card.dataset.direction || 'target'
      );
      var c = COLORES[estado];

      // Fondo tenue (inline supera .card del header sin !important)
      card.style.backgroundColor = c.bg;
      // Borde izquierdo de color (Opción A)
      card.style.borderLeft = '4px solid ' + c.borde;
      card.style.borderRadius = '0.5rem';

      // Dot indicador
      var dot = card.querySelector('.semaforo-dot');
      if (dot) {
        dot.style.backgroundColor = c.dot;
        dot.title = estado.charAt(0).toUpperCase() + estado.slice(1);
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', aplicar);
  } else {
    aplicar();
  }
  setTimeout(function () { location.reload(); }, 300000);
})();
</script>
