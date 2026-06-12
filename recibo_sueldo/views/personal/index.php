<?php  
  include_once 'controller/personal.php';
  include_once 'controller/usuarios.php';

  if(!empty($_POST['busqueda'])){
    $busqueda=$_POST['busqueda'];
  }else{
    $busqueda='';
  }
  $resultado_personal=listado_personal($busqueda);    
  /*
  
  $observacion_ot=listado_personal($periodo)[1];    
  */
?>
<div class="container">
  <div class="row">
    <div class="col-12">
      <h1 class="display-4 text-center text-muted">Listado de Empleados</h1>
    </div>
  </div>  
  <div class="row">
    <div class="col-12">      
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <form route="home.php?menu=ver_liquidaciones" class="form-inline my-1 my-lg-0" method="POST">
            <input class="form-control mr-sm-2" type="search" name="busqueda" placeholder="Busqueda" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>  
          </form>
        </nav>     
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card m-1">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div class="col-2">
            <a href="home.php?menu=agregar_persona" class="btn btn-success my-1 my-sm-0 btn-sm">Agregar Persona</a>        
          </div>          
        </div>
        <div class="card-body m-2">         
          <div class="table-responsive">
            <table class="table">
              <thead class="p-3 mb-2 bg-info text-white">
                <tr>                
                  <th scope="col">Legajo</th>                
                  <th scope="col">DNI</th>                
                  <th scope="col">Nombre</th>                
                  <th scope="col">Tipo de Convenio</th>                
                  <th scope="col">Tipo de Liquidacion</th>                
                  <th scope="col">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  while($row_resultado_personal = $resultado_personal->fetch_assoc()){
                ?>
                <tr >                  
                  <td><?php if(!empty($row_resultado_personal)){ echo $row_resultado_personal['legajo']; }?></td>
                  <td><?php if(!empty($row_resultado_personal)){ echo $row_resultado_personal['dni']; }?></td>
                  <td><?php if(!empty($row_resultado_personal)){ echo $row_resultado_personal['nombre']; }?></td>                                    
                  <td><?php if(!empty($row_resultado_personal)){ echo $row_resultado_personal['tipo_convenio']; }?></td>                                    
                  <td><?php if(!empty($row_resultado_personal)){ echo $row_resultado_personal['tipo_liquidacion']; }?></td>                                    
                  <td>
                    <div class="btn-group btn-group-sm" role="group" aria-label="...">
                    <?php
                          if(!empty($row_resultado_personal)){ 
                            $legajo_encrip=base64_encode($row_resultado_personal['legajo']);
                            echo "<a class='btn btn-info my-1 my-sm-0 btn-sm' href='home.php?accion=ver_recibos_empleado&legajo=".$legajo_encrip."' >Ver Recibos de Sueldo</a>"; 
                          }
                      ?>
                    </div>
                    <div class="btn-group btn-group-sm" role="group" aria-label="...">
                      <a href="home.php?menu=modificar_persona&id_personal=<?php echo $row_resultado_personal['id_personal'];?>" class="btn btn-warning my-1 my-sm-0 btn-sm">Modificar</a>                              
                    </div>   
                  </td>
                </tr>
                <?php }?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
</div>
