<div class="d-flex flex-wrap gap-3">

  <!-- Caudal -->
  <div class="ind-section">
    <div class="ind-section-title">Caudal</div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['caudal_vino'] ?? '--'; ?>"
         data-objetivo="5000" data-direction="target">
      <div class="ind-title">Caudal Vino (L/h) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['caudal_vino'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="5000" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['caudal_alcohol'] ?? '--'; ?>"
         data-objetivo="" data-direction="target">
      <div class="ind-title">Caudal Alcohol (L/h) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['caudal_alcohol'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>
  </div>

  <!-- Jugo Dilutor -->
  <div class="ind-section">
    <div class="ind-section-title">Jugo Dilutor</div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['caudal_jugo_dilutor'] ?? '--'; ?>"
         data-objetivo="5000" data-direction="target">
      <div class="ind-title">Caudal (L/h) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['caudal_jugo_dilutor'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="5000" readonly></div>
      </div>
    </div>
  </div>

  <!-- Melaza Dilutor -->
  <div class="ind-section">
    <div class="ind-section-title">Melaza Dilutor</div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['caudal_melaza_dilutor'] ?? '--'; ?>"
         data-objetivo="" data-direction="target">
      <div class="ind-title">Caudal (L/h) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['caudal_melaza_dilutor'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>
  </div>

</div>
