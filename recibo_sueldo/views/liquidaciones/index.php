<?php  
  include_once 'controller/liquidaciones.php';
  if(!empty($_POST['busqueda'])){
    $busqueda=$_POST['busqueda'];
  }else{
    $busqueda='';
  }
  $resultado_liquidaciones=listado_liquidaciones($busqueda);    
  /*
  
  $observacion_ot=listado_liquidaciones($periodo)[1];    
  */
?>
<div class="container">
  <div class="row">    
    <div class="col-12">
      <h1 class="display-4 text-center text-muted">Liquidaciones</h1>
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
            <a href="home.php?menu=agregar_liquidacion" class="btn btn-success my-1 my-sm-0 btn-sm">Agregar liquidacion</a>        
          </div>          
        </div>
        <div class="card-body m-2">         
          <div class="table-responsive">
            <table class="table">
              <thead class="p-3 mb-2 bg-info text-white">
                <tr>                
                  <th scope="col">Periodo</th>                                  
                  <th scope="col">Tipo de Liquidacion</th>                
                  <th scope="col">Quincena</th>                
                  <th scope="col">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  while($row_resultado_liquidaciones = $resultado_liquidaciones->fetch_assoc()){
                ?>
                <tr >
                  <td><?php
                          if(!empty($row_resultado_liquidaciones['periodo'])){ echo $row_resultado_liquidaciones['periodo']."-".$row_resultado_liquidaciones['anio']; }
                      ?>
                  </td>                  
                  <td><?php if(!empty($row_resultado_liquidaciones['tipo_liquidacion'])){ echo $row_resultado_liquidaciones['tipo_liquidacion']; }?></td>
                  <td><?php if(!empty($row_resultado_liquidaciones['quincena'])){ echo $row_resultado_liquidaciones['quincena']; }?></td>                                    
                  <td>
                      <div class="btn-group btn-group-sm" role="group" aria-label="...">
                      <?php
                            if(!empty($row_resultado_liquidaciones)){ 
                              echo "<a class='btn btn-warning my-1 my-sm-0 btn-sm' href='home.php?menu=ver_recibos&id_liquidacion=".$row_resultado_liquidaciones['id_liquidacion']."' >Ver Liquidacion</a>"; 
                            }
                        ?>
                        <form method="POST" action="home.php?menu=ver_liquidaciones">
                          <button type="submit" name="eliminar_liq" value="<?php if(!empty($row_resultado_liquidaciones['id_liquidacion'])){ echo $row_resultado_liquidaciones['id_liquidacion']; }?>" Onclick="return confirm('Esta seguro de Eliminar la Liquidacion?');" class="btn btn-danger my-1 my-sm-0 btn-sm">Eliminar</button>
                        </form>
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
