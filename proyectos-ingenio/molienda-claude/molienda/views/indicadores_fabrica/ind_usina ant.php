<div class="card p-3">
  <h5 class="card-title">Usina</h5>
  <div class="row bg-light text-start rounded shadow"> 
    <p class="card-title mb-3">Consumo de Energia</p>
    <div class="row mb-2 align-items-center">
      <div class="col  ">Potencia Total:</div>
      <div class="col">
        <input type="text" class="form-control form-control-sm " 
               value="<?php echo $potencia_total ?? 0; ?>">
      </div>
    </div>
    <div class="row mb-2 align-items-center">
      <div class="col">Corriente Total:</div>
      <div class="col">
        <input type="text" class="form-control form-control-sm " 
               value="<?php echo $corriente_total ?? 0; ?>">
      </div>
    </div>
  </div>
</div>