<div class="d-flex flex-wrap gap-3">

  <!-- Presión -->
  <div class="ind-section">
    <div class="ind-section-title">Presión</div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['presion_vapor_directo'] ?? '--'; ?>"
         data-objetivo="19 a 21" data-direction="target">
      <div class="ind-title">Directa (kg/cm2) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['presion_vapor_directo'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="19 a 21" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['presion_vapor_escape'] ?? '--'; ?>" data-objetivo="1.5" data-direction="target">
      <div class="ind-title">Escape (kg/cm2) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['presion_vapor_escape'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="1,5" readonly></div>
      </div>
    </div>
  </div>

  <!-- Consumo de Vapor -->
  <div class="ind-section">
    <div class="ind-section-title">Consumo de Vapor</div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['cv_usina_alta'] ?? '--'; ?>"
         data-objetivo="140" data-direction="target">
      <div class="ind-title">Usina (Tn/h) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['cv_usina_alta'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="140" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['cv_trapiche'] ?? '--'; ?>"
         data-objetivo="" data-direction="target">
      <div class="ind-title">Trapiche (Tn/h) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['cv_trapiche'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>
  </div>

  <!-- Caudal de Vapor producido -->
  <div class="ind-section">
    <div class="ind-section-title">Vapor Producido</div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['caudal_vapor_cald2'] ?? '--'; ?>"
         data-objetivo="40" data-direction="target">
      <div class="ind-title">Caldera 2 (Tn/h) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['caudal_vapor_cald2'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="40" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['caudal_vapor_cald6'] ?? '--'; ?>"
         data-objetivo="100" data-direction="target">
      <div class="ind-title">Caldera 6 (Tn/h) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['caudal_vapor_cald6'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="100" readonly></div>
      </div>
    </div>
  </div>

  <!-- Gas y Total -->
  <div class="ind-section">
    <div class="ind-section-title">Gas / Total</div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $ind['gas_actual'] ?? '--'; ?>"
         data-objetivo="<?php echo $gas_prom_turno_ant ?? '--'; ?>" data-direction="target">
      <div class="ind-title">Consumo Gas (m3) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($ind['gas_actual'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Prom. T. ant.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($gas_prom_turno_ant ?? '--'); ?>" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['vapor_total'] ?? '--'; ?>"
         data-objetivo="" data-direction="target">
      <div class="ind-title">Vapor Total (Tn/h) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['vapor_total'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>
  </div>

</div>
