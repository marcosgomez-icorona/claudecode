<div class="indicator-grid">

  <!-- Parámetros principales -->
  <div class="ind-section">
    <div class="ind-section-title">Parámetros</div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $ind['molienda_tn_h'] ?? '--'; ?>"
         data-objetivo="" data-direction="target">
      <div class="ind-title">Molienda (Tn/h) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $ind['molienda_tn_h'] ?? '--'; ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $ind['pol_cania'] ?? '--'; ?>"
         data-objetivo="3" data-direction="target">
      <div class="ind-title">Pol Caña (%) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $ind['pol_cania'] ?? '--'; ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="3" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $ind['humedad_bagazo'] ?? '--'; ?>"
         data-objetivo="" data-direction="less">
      <div class="ind-title">Humedad Bagazo (%) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $ind['humedad_bagazo'] ?? '--'; ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>
  </div>

  <!-- Molino -->
  <div class="ind-section">
    <div class="ind-section-title">Molino</div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['velocidad_molino1'] ?? '--'; ?>"
         data-objetivo="" data-direction="target">
      <div class="ind-title">Veloc. 1er Molino (R.P.M) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['velocidad_molino1'] ?? '--'; ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['presion_molino6_este'] ?? '--'; ?>"
         data-objetivo="" data-direction="target">
      <div class="ind-title">Presión M6 Este (bar) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['presion_molino6_este'] ?? '--'; ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['presion_molino6_oeste'] ?? '--'; ?>"
         data-objetivo="3" data-direction="target">
      <div class="ind-title">Presión M6 Oeste (bar) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['presion_molino6_oeste'] ?? '--'; ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="3" readonly></div>
      </div>
    </div>
  </div>

  <!-- Imbibición -->
  <div class="ind-section">
    <div class="ind-section-title">Imbibición</div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['agua_imbibicion'] ?? '--'; ?>"
         data-objetivo="" data-direction="target">
      <div class="ind-title">Agua de Imbibición <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['agua_imbibicion'] ?? '--'; ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>
  </div>

</div>
