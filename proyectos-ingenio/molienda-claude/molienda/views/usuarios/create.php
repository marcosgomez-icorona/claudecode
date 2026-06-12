<?php
    include_once 'controller/usuarios.php';
    
    if(!empty($_POST['accion']) and !empty($_POST['usuario']) and !empty($_POST['contraseña']) and !empty($_POST['valida_contraseña']) and !empty($_POST['tipo'])){
      if($_POST['contraseña']===$_POST['valida_contraseña']){
        alta_usuario($_POST['usuario'], $_POST['contraseña'],$_POST['tipo']);
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
        <div class="col-3">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <div class="col-7 m-1">
                    <h4>Agregar Usuario</h4>
                </div>                
              </div>
              <div class="card-body">
                  <form method="POST" enctype="multipart/form-data">
                    <input
                      type="text"
                      name="usuario"
                      placeholder="Usuario"
                      class="form-control mb-2"
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
                    <select name="tipo" class="form-control mb-2">
                      <option value="">Tipo de Usuario.....</option>
                      <option value="ADMIN">Administrador</option>
                      <option value="OPERADOR">Operador</option>
                    </select>                                       
                    <input
                      hidden=""
                      name="accion"
                      value="alta"
                    />          
                    <button class="btn btn-primary btn-block" type="submit">Agregar</button>
                  </form>
                </div>
            </div>
        </div>
    </div>
</div>