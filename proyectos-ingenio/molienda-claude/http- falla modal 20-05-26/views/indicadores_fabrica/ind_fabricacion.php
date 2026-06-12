<div class="d-flex flex-wrap gap-3">

  <!-- PH -->
  <div class="ind-section">
    <div class="ind-section-title">PH</div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $ind['ph_sulfitado'] ?? '--'; ?>"
         data-objetivo="" data-direction="target">
      <div class="ind-title">Sulfitado <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($ind['ph_sulfitado'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $ind['ph_encalado'] ?? '--'; ?>"
         data-objetivo="" data-direction="target">
      <div class="ind-title">Encalado <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($ind['ph_encalado'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>
  </div>

  <!-- P. Generales -->
  <div class="ind-section">
    <div class="ind-section-title">P. Generales</div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $ind['pol_cachaza'] ?? '--'; ?>"
         data-objetivo="" data-direction="less">
      <div class="ind-title">Pol Cachaza (%) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($ind['pol_cachaza'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['caudal_jugo_dilutor'] ?? '--'; ?>" data-objetivo="" data-direction="target">
      <div class="ind-title">Jugo Destilería (m3/h) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['caudal_jugo_dilutor'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['presion_vg1'] ?? '--'; ?>"
         data-objetivo="" data-direction="target">
      <div class="ind-title">Presión Vg1 (kg/cm2) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['presion_vg1'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $ind['sulfitacion_ppm'] ?? '--'; ?>"
         data-objetivo="500" data-direction="target">
      <div class="ind-title">Sulfitación (ppm) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($ind['sulfitacion_ppm'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="500" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['caudal_jugo_clarif'] ?? '--'; ?>"
         data-objetivo="250" data-direction="target">
      <div class="ind-title">Caud. Bba Clarif. (m3/h) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['caudal_jugo_clarif'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="250" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $opc['nivel_melado_tratado'] ?? '--'; ?>"
         data-objetivo="" data-direction="target">
      <div class="ind-title">N. Melado Trat. (%) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($opc['nivel_melado_tratado'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
      </div>
    </div>
  </div>

  <!-- Azúcar -->
  <div class="ind-section">
    <div class="ind-section-title">Azúcar</div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $ind['color_azucar'] ?? '--'; ?>"
         data-objetivo="100" data-direction="less">
      <div class="ind-title">Color <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($ind['color_azucar'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="100" readonly></div>
      </div>
    </div>

    <div class="ind-card semaforo-card"
         data-valor="<?php echo $ind['humedad_azucar'] ?? '--'; ?>"
         data-objetivo="0.04" data-direction="less">
      <div class="ind-title">Humedad (%) <span class="semaforo-dot"></span></div>
      <div class="ind-row">
        <div class="ind-field"><span class="ind-caption">Actual</span>
          <input type="text" class="form-control form-control-sm ind-input" value="<?php echo fmt($ind['humedad_azucar'] ?? '--'); ?>" readonly></div>
        <div class="ind-field"><span class="ind-caption">Obj.</span>
          <input type="text" class="form-control form-control-sm ind-input" value="0,04" readonly></div>
      </div>
    </div>
  </div>

</div>
