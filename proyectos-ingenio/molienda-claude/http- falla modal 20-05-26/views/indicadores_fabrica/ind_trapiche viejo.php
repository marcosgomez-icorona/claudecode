<div class="card p-3">
  <h5 class="card-title">Trapiche</h5>
  <div class="row bg-light text-start rounded shadow"> 
    <p class="card-title mb-3">Parametros</p>
    <div class="row mb-2 align-items-center">
      <div class="col  ">Molienda:</div>
      <div class="col ">
        <input type="text" class="form-control form-control-sm " 
               value="<?php echo $molienda ?? 0; ?>">
      </div>
    </div>
    <div class="row mb-2 align-items-center">
      <div class="col ">Pol:</div>
      <div class="col ">
        <input type="text" class="form-control form-control-sm " 
               value="<?php echo $pol ?? 0; ?>">
      </div>
    </div>
    <div class="row mb-2 align-items-center">
      <div class="col ">Veloc. 1er Molino:</div>
      <div class="col ">
        <input type="text" class="form-control form-control-sm " 
               value="<?php echo $veloc_1er_molino ?? 0; ?>">
      </div>
    </div>
  </div>
</div>

