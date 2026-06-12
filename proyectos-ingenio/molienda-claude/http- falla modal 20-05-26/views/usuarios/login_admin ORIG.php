<?php 
    include_once 'controller/usuarios.php';
    if(!empty($_POST)){
        login_admin($_POST['usuario'],$_POST['password']);
    }	
?>
<div class="row justify-content-center">
    <div class="col-lg-3">
        <div class="card shadow-lg border-0 rounded-lg mt-5">
        <div class="card-header">
                <div class="row">
                    <div class="col-2">
                        <h3 class="text-center font-weight-light my-4"><img src="assets/img/Logo-Ing La Corona.png" width="50px" alt=""></h3>
                    </div>
                    <div class="col-10">
                        <h3 class="text-center font-weight-light my-4"></h3>
                    </div>
                </div>
                
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label  for="usuario">Usuario</label><input class="form-control py-4" id="usuario" name="usuario" type="text" placeholder="Ingrese el usuario" />
                    </div>
                    <div class="form-group">
                        <label  for="password">Contraseña</label><input class="form-control py-4" id="password" name="password" type="password" placeholder="Ingrese la clave" />
                    </div>                    
                    <div class="form-group ">
                        <button type="submit" class="btn btn-secondary form-control">Aceptar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
