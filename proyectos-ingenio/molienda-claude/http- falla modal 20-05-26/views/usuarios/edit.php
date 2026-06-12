<?php
    include 'controller/usuarios.php';
?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <div class="col-7 m-1">
                    <h4>Modificar Usuario</h4>
                  </div>
                  <div class="col-5 m-1">
                    <a href="home.php?menu=usuarios" class="btn btn-outline-success my-1 my-sm-0 btn-sm">Volver al Listado...</a>
                  </div>
                </div>
                <div class="card-body">
                  <form method="POST" enctype="multipart/form-data">
                    Nombre
                    <input
                      type="text"
                      name="nombre"
                      value="<?php if(!empty($row_resultado_usuario)){ echo $row_resultado_usuario['nombre']; }?>"
                      class="form-control mb-2"
                    />    
                    <label>Tipo</label>
                      <select class="form-control mb-2" name="tipo_usuario" >
                        <option value="">Seleccionar...</option>
                        <?php
                          while ($row_resultado_tipos_usuario = $resultado_tipos_usuario-> fetch_assoc()){  
                        ?>
                                <option value="<?php echo $row_resultado_tipos_usuario['id']?>" 
                                        <?php if(!empty($row_resultado_usuario) and !empty($row_resultado_tipos_usuario) and $row_resultado_tipos_usuario['id']==$row_resultado_usuario['tipo_usuario'] ) echo "Selected='' ";?>
                                        onchange="tipo_usuario.value=<?php if(!empty($row_resultado_tipos_usuario)) echo $row_resultado_tipos_usuario['id'];?>" ><?php if(!empty($row_resultado_tipos_usuario)) echo $row_resultado_tipos_usuario['nombre']?> </option>
                        <?php
                          } ;
                          
                        ?>                    
                        
                      </select> 
                      Reestablecer Contraseña
                    <input
                      type="password"
                      name="contraseña"
                      class="form-control mb-2"
                    /> 
                      Validar Contraseña
                    <input
                      type="password"
                      name="valida_contraseña"
                      class="form-control mb-2"
                    /> 
                      <label>Email</label>                
                    <input
                      type="text"
                      name="email"
                      value="<?php if(!empty($row_resultado_usuario)){ echo $row_resultado_usuario['email']; }?>"
                      class="form-control mb-2"
                    /> 
                    <input
                      hidden=""
                      name="accion"
                      value="modificar"
                    />          
                    <button class="btn btn-primary btn-block" type="submit">Modificar</button>
                  </form>
                </div>
            </div>
        </div>
    </div>
</div>