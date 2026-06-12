<div class="d-flex flex-column gap-2">

  <!-- RESUMEN DE GUARDIA -->
  <div class="ind-section" style="min-width:unset;">
    <div class="ind-section-title">Resumen de Guardia</div>
    <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:0.4rem;">

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $ind['molienda_tn_h'] ?? '--'; ?>"
           data-objetivo="" data-direction="target">
        <div class="ind-title">Molienda (Tn) <span class="semaforo-dot"></span></div>
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
        <div class="ind-title">Pol (%) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $ind['pol_cania'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="3" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $ind['gas_actual'] ?? '--'; ?>"
           data-objetivo="0" data-direction="target">
        <div class="ind-title">Gas (M3) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $ind['gas_actual'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="0" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $cant_tiempo_parada ?? '--'; ?>"
           data-objetivo="" data-direction="target">
        <div class="ind-title">Paradas <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $cant_tiempo_parada ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['velocidad_molino1'] ?? '--'; ?>"
           data-objetivo="" data-direction="target">
        <div class="ind-title">Veloc. 1er Molino (RPM) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['velocidad_molino1'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

    </div>
  </div>

  <!-- PRODUCCIÓN -->
  <div class="ind-section" style="min-width:unset;">
    <div class="ind-section-title">Producción</div>
    <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:0.4rem;">

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['nivel_jugo_pesado'] ?? '--'; ?>" data-objetivo="" data-direction="target">
        <div class="ind-title">Nivel Jugo Pesado <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['nivel_jugo_pesado'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $ind['ph_sulfitado'] ?? '--'; ?>"
           data-objetivo="" data-direction="target">
        <div class="ind-title">PH Sulfitado <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $ind['ph_sulfitado'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $ind['sulfitacion_ppm'] ?? '--'; ?>"
           data-objetivo="500" data-direction="target">
        <div class="ind-title">Sulfitación (PPM) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $ind['sulfitacion_ppm'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="500" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['temp_calentador'] ?? '--'; ?>" data-objetivo="" data-direction="target">
        <div class="ind-title">Últ. Temp. Calentador <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['temp_calentador'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $ind['pol_cachaza'] ?? '--'; ?>"
           data-objetivo="" data-direction="less">
        <div class="ind-title">Pol Cachaza (%) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $ind['pol_cachaza'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['nivel_jugo_clarificado'] ?? '--'; ?>" data-objetivo="" data-direction="target">
        <div class="ind-title">Nivel Jugo Clarificado <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['nivel_jugo_clarificado'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['caudal_jugo_clarif'] ?? '--'; ?>"
           data-objetivo="250" data-direction="target">
        <div class="ind-title">Caud. Bb Clarif. (M3/h) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['caudal_jugo_clarif'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="250" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['caudal_jugo_dilutor'] ?? '--'; ?>"
           data-objetivo="" data-direction="target">
        <div class="ind-title">Jugo Destilería (M3/h) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['caudal_jugo_dilutor'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['nivel_melado_tratado'] ?? '--'; ?>"
           data-objetivo="" data-direction="target">
        <div class="ind-title">N. Melado Trat. (%) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['nivel_melado_tratado'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['nivel_melado'] ?? '--'; ?>" data-objetivo="" data-direction="target">
        <div class="ind-title">Nivel Melado 1/2 <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['nivel_melado'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['descarga_tachos_1ra'] ?? '--'; ?>" data-objetivo="" data-direction="target">
        <div class="ind-title">Nivel Cristalizador 1ra <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['descarga_tachos_1ra'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['contador_bolsas_dia'] ?? '--'; ?>" data-objetivo="" data-direction="target">
        <div class="ind-title">Producción Azúcar Diaria <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['contador_bolsas_dia'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['caudal_alcohol'] ?? '--'; ?>"
           data-objetivo="" data-direction="target">
        <div class="ind-title">Caudal (L/h) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['caudal_alcohol'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $ind['color_azucar'] ?? '--'; ?>"
           data-objetivo="100" data-direction="less">
        <div class="ind-title">Color <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $ind['color_azucar'] ?? '--'; ?>" readonly></div>
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
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $ind['humedad_azucar'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="0,04" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['caudal_vino'] ?? '--'; ?>"
           data-objetivo="" data-direction="target">
        <div class="ind-title">Vino (L/h) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['caudal_vino'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['caudal_vino_160'] ?? '--'; ?>" data-objetivo="" data-direction="target">
        <div class="ind-title">Vino 160 (L/h) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['caudal_vino_160'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <?php
        $estado_k2 = '--';
        $pk2 = $opc['presion_k2'] ?? '--';
        if ($pk2 !== '--') $estado_k2 = ((float)$pk2 > 0) ? 'ON' : 'OFF';
      ?>
      <div class="ind-card semaforo-card"
           data-valor="<?php echo $pk2; ?>" data-objetivo="" data-direction="target">
        <div class="ind-title">Funcionamiento K2 <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Estado</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $estado_k2; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Presión</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $pk2; ?>" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['nivel_agua_foza'] ?? '--'; ?>" data-objetivo="" data-direction="target">
        <div class="ind-title">Nivel Agua de Foza <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['nivel_agua_foza'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['presion_aire'] ?? '--'; ?>" data-objetivo="" data-direction="target">
        <div class="ind-title">Aire <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['presion_aire'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

    </div>
  </div>

  <!-- ENERGÍA -->
  <div class="ind-section" style="min-width:unset;">
    <div class="ind-section-title">Energía</div>
    <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:0.4rem;">

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['vapor_total'] ?? '--'; ?>"
           data-objetivo="" data-direction="target">
        <div class="ind-title">Caudal Total Vapor (Tn/h) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['vapor_total'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['caudal_vapor_cald2'] ?? '--'; ?>"
           data-objetivo="40" data-direction="target">
        <div class="ind-title">Caudal de Vapor Caldera 2 (Tn/h) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['caudal_vapor_cald2'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="40" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['caudal_vapor_cald6'] ?? '--'; ?>"
           data-objetivo="100" data-direction="target">
        <div class="ind-title">Caud. V. Caldera 6 (Tn/h) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['caudal_vapor_cald6'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="100" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['presion_vapor_directo'] ?? '--'; ?>"
           data-objetivo="21" data-direction="target">
        <div class="ind-title">Presión Directa (Kg/cm2) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['presion_vapor_directo'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="21" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['presion_vapor_escape'] ?? '--'; ?>" data-objetivo="1.5" data-direction="target">
        <div class="ind-title">Escape (Kg/cm2) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['presion_vapor_escape'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="1,5" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['presion_vg1'] ?? '--'; ?>" data-objetivo="" data-direction="target">
        <div class="ind-title">Presión Vg1 (Kg/cm2) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['presion_vg1'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['temp_agua_alim'] ?? '--'; ?>" data-objetivo="" data-direction="target">
        <div class="ind-title">Temp. Agua Alimentación <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['temp_agua_alim'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['potencia_total'] ?? '--'; ?>"
           data-objetivo="5000" data-direction="target">
        <div class="ind-title">Potencia Total (Kw/h) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['potencia_total'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="5000" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $ind['gas_actual'] ?? '--'; ?>"
           data-objetivo="0" data-direction="target">
        <div class="ind-title">Gas (M3) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $ind['gas_actual'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="0" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['cv_usina_alta'] ?? '--'; ?>"
           data-objetivo="140" data-direction="target">
        <div class="ind-title">Consumo de Vapor de Usina (Tn/h) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['cv_usina_alta'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="140" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['cv_trapiche'] ?? '--'; ?>"
           data-objetivo="" data-direction="target">
        <div class="ind-title">Cons. V. Trapiche (Tn/h) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['cv_trapiche'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <?php
        $corriente_total = '--';
        $ci = $opc['intensidad_siemens'] ?? '--';
        $ca = $opc['intensidad_aeg'] ?? '--';
        if ($ci !== '--' && $ca !== '--') $corriente_total = round((float)$ci + (float)$ca, 0);
        elseif ($ci !== '--') $corriente_total = $ci;
        elseif ($ca !== '--') $corriente_total = $ca;
      ?>
      <div class="ind-card semaforo-card"
           data-valor="<?php echo $corriente_total; ?>" data-objetivo="" data-direction="target">
        <div class="ind-title">Corriente Total (A) <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $corriente_total; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $opc['presion_agua_alim'] ?? '--'; ?>" data-objetivo="" data-direction="target">
        <div class="ind-title">Presión Agua Alimentación <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Actual</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $opc['presion_agua_alim'] ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

      <div class="ind-card semaforo-card"
           data-valor="<?php echo $prom_consumo_8hs ?? '--'; ?>" data-objetivo="" data-direction="target">
        <div class="ind-title">Prom. Consumo 8hs Turno Ant. <span class="semaforo-dot"></span></div>
        <div class="ind-row">
          <div class="ind-field"><span class="ind-caption">Prom (KW)</span>
            <input type="text" class="form-control form-control-sm ind-input" value="<?php echo $prom_consumo_8hs ?? '--'; ?>" readonly></div>
          <div class="ind-field"><span class="ind-caption">Obj.</span>
            <input type="text" class="form-control form-control-sm ind-input" value="" readonly></div>
        </div>
      </div>

    </div>
  </div>

</div>
