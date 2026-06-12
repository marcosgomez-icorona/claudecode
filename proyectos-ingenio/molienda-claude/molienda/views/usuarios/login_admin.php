<?php 
    include_once 'controller/usuarios.php';

    
    if(!empty($_POST)){   
        //sync_usuarios_caniero();        
        login_admin($_POST['usuario'],$_POST['password']);
        
    }	
?>
<div class="row justify-content-center">
    <h3 class="text-center font-weight-light my-4"><img src="assets/img/Logo-Ing La Corona.png" width="50px" alt=""></h3>
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

