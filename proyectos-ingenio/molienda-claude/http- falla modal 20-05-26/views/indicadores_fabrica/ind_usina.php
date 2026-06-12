<div class="d-flex flex-wrap gap-3">

  <!-- Generadores -->
  <div class="ind-section" style="min-width:175px;">
    <div class="ind-section-title">Generadores</div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['potencia_activa_aeg'] ?? '--'; ?>"
         data-objetivo="5000" data-direction="target">
      <div class="ind-title">Potencia AEG (KW/h) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['potencia_activa_aeg'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="5000" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['intensidad_aeg'] ?? '--'; ?>"
         data-objetivo="" data-direction="target">
      <div class="ind-title">Corriente AEG (A) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['intensidad_aeg'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['potencia_activa_tgm'] ?? '--'; ?>" data-objetivo="" data-direction="target">
      <div class="ind-title">Potencia TGM (KW/h) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['potencia_activa_tgm'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['intensidad_tgm'] ?? '--'; ?>" data-objetivo="" data-direction="target">
      <div class="ind-title">Corriente TGM (A) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['intensidad_tgm'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>
  </div>

  <!-- SKODA -->
  <div class="ind-section" style="min-width:175px;">
    <div class="ind-section-title">SKODA</div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['potencia_activa_siemens'] ?? '--'; ?>"
         data-objetivo="" data-direction="target">
      <div class="ind-title">Potencia SKODA 1 (KW/h) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['potencia_activa_siemens'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['intensidad_siemens'] ?? '--'; ?>"
         data-objetivo="" data-direction="target">
      <div class="ind-title">Corriente SKODA 1 (A) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['intensidad_siemens'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['potencia_activa_edet'] ?? '--'; ?>"
         data-objetivo="" data-direction="target">
      <div class="ind-title">Potencia SKODA 2 (KW/h) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['potencia_activa_edet'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['intensidad_edet'] ?? '--'; ?>"
         data-objetivo="" data-direction="target">
      <div class="ind-title">Corriente SKODA 2 (A) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['intensidad_edet'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>
  </div>

  <!-- Consumo Total -->
  <div class="ind-section" style="min-width:175px;">
    <div class="ind-section-title">Consumo Total</div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['potencia_total'] ?? '--'; ?>"
         data-objetivo="5000" data-direction="target">
      <div class="ind-title">Potencia Total (KW/h) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['potencia_total'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="5000" readonly></div>
      </div>
    </div>

    <?php
      $ci_u  = $opc['intensidad_siemens'] ?? '--';
      $ca_u  = $opc['intensidad_aeg']     ?? '--';
      $ct_u  = $opc['intensidad_tgm']     ?? '--';
      $vals_corriente = array_filter([$ci_u, $ca_u, $ct_u], fn($v) => $v !== '--');
      $corriente_total_usina = count($vals_corriente) ? round(array_sum(array_map('floatval', $vals_corriente)), 0) : '--';
    ?>
    <div class="ind-card semaforo-card"
         data-valor="<?php echo $corriente_total_usina; ?>" data-objetivo="" data-direction="target">
      <div class="ind-title">Corriente Total (A) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($corriente_total_usina); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>
  </div>

</div>
