<?php
    include('controller/login.php');
    
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
              <div class="card-header">
                  <h3 class="text-center font-weight-light my-4">Crear Cuenta</h3></div>
                    <div class="card-body">
                        <form  method="POST" name="form1" id="form1">
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group"><label class="small mb-1" for="inputFirstName">Nombre/s</label><input name="nombre" class="form-control py-4" id="inputFirstName" type="text" placeholder="Ingrese el Nombre" /></div>
                                    </div>                                                
                                </div>
                                <div class="form-group"><label class="small mb-1" for="inputEmailAddress">Email</label><input name="usuario" class="form-control py-4" id="inputEmailAddress" type="email" aria-describedby="emailHelp" placeholder="Ingrese el Mail" /></div>
                                   <div class="form-row">
                                        <div class="col-md-6">
                                            <div class="form-group"><label class="small mb-1" for="inputPassword">Contraseña</label><input name="contraseña" class="form-control py-4" id="inputPassword" type="password" placeholder="Ingrese la Contraseña" /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputConfirmPassword">Confirmar Contraseña</label><input name="valida_contraseña" class="form-control py-4" id="inputConfirmPassword" type="password" placeholder="Confirm password" /></div>
                                                </div>
                                            </div>
                                            <div class="form-group mt-4 mb-0"><input class="btn btn-primary btn-block" type="submit" value="Crear">
                                           </div>
                        </form>
                    </div>
                </div>
              </div>  
            </div>
        </div>
    </div>
</div>