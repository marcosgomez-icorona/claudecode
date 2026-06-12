<?php
    include_once 'controller/usuarios.php';

    if(!empty($_GET['usuario'])){
      $usuario=base64_decode($_GET['usuario']);
    }
    
    if(!empty($_POST['accion']) and !empty($_POST['usuario']) and !empty($_POST['contraseña']) and !empty($_POST['valida_contraseña'])){
      if($_POST['contraseña']===$_POST['valida_contraseña']){
        cambiar_clave($_POST['usuario'], $_POST['contraseña']);
      }else{
        echo '<script>alert("No coincide la contraseña, intente nuevamente...");</script>';
      }
    }else{
      if(!empty($_POST['accion'])){
        echo '<script>alert("Datos incompletos, intente nuevamente....");</script>';
      }
    }
?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-6">
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <h4>Cambiar clave</h4>
                </div>
              </div>
              <div class="card-body">
                  <form method="POST" enctype="multipart/form-data">
                    <input
                      type="text"
                      name="usuario"
                      placeholder="Usuario"
                      class="form-control mb-2"
                      value="<?php echo $usuario;?>"
                      hidden=""
                    />                    
                    <input
                      type="password"
                      name="contraseña"
                      placeholder="Contraseña"
                      class="form-control mb-2"
                    /> 
                    <input
                      type="password"
                      name="valida_contraseña"
                      placeholder="Valida Contraseña"
                      class="form-control mb-2"
                    />                                         
                    <input
                      hidden=""
                      name="accion"
                      value="alta"
                    /> 
                    <div class="row">
                      <div class="col-6">
                        <button class="btn btn-primary btn-block" type="submit">Aceptar</button>
                      </div>
                      <div class="col-6">
                        <a href="home.php?menu=<?php echo $_GET['regresar_menu']?>&usuario=<?php echo htmlspecialchars($_GET['usuario']) ?>" class="btn btn-secondary btn-block">Cancelar</a>
                      </div>
                    </div>         
                    
                  </form>
                </div>
            </div>
        </div>
    </div>
</div>