<?php 
    include_once 'controller/usuarios.php';
    if(!empty($_POST)){
        login_admin($_POST['legajo'],$_POST['dni']);
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
                        <h3 class="text-center font-weight-light my-4">Visualizar Recibos de Sueldo</h3>
                    </div>
                </div>
                
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label  for="legajo">Legajo</label><input class="form-control py-4" id="legajo" name="legajo" type="text" placeholder="Ingrese el Legajo" />
                    </div>
                    <div class="form-group">
                        <label  for="dni">Clave</label><input class="form-control py-4" id="dni" name="dni" type="password" placeholder="Ingrese su Clave personal" />
                    </div>
                    <div class="form-group ">
                        <button type="submit" class="btn btn-secondary form-control">Aceptar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
