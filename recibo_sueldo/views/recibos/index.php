<?php  
  include_once 'controller/recibos.php';
    
  if(!empty($_GET['id_liquidacion'])){
    $id_liquidacion=$_GET['id_liquidacion'];
  }else{
    $id_liquidacion=-1;
  }
  if(!empty($_POST['busqueda'])){
    $busqueda=$_POST['busqueda'];
  }else{
    $busqueda='';
  }
  $resultado_recibos=listado_recibos($id_liquidacion,'empleados',$busqueda);    
  $row_resultado_recibos = $resultado_recibos->fetch_assoc();
?>
<div class="container">
  <div class="row">
    <div class="col-12">
      <h1 class="display-4 text-center text-muted">Detalle de Liquidacion</h1>
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
        <div class="card-header">   
          <p ><label for=""><div class="h2 text-secondary"> 
                <?php if(!empty($row_resultado_recibos)){ echo  "PERIODO ".$row_resultado_recibos['periodo']."-".$row_resultado_recibos['anio']." / ".$row_resultado_recibos['tipo_liquidacion']." / ".$row_resultado_recibos['quincena']; }?>
            </label>
          </p>  </div>         
        </div>
        <div class="card-body m-2">         
          <div class="table-responsive">
            <table class="table">
              <thead class="p-3 mb-2 bg-info text-white">
                <tr>                
                  <th scope="col">Legajo</th>                
                  <th scope="col">Nombre</th>                                                 
                  <th scope="col">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  do{
                ?>
                <tr >  
                  <td><?php if(!empty($row_resultado_recibos)){ echo $row_resultado_recibos['legajo']; }?></td>
                  <td><?php if(!empty($row_resultado_recibos)){ echo $row_resultado_recibos['nombre']; }?></td>                  
                  <td>
                    <div class="btn-group btn-group-sm" role="group" aria-label="...">
                      <?php 
                          if(!empty($row_resultado_recibos)){ echo "<a class='btn btn-warning my-1 my-sm-0 btn-sm' href='assets/recibos/".$row_resultado_recibos['anio']."-".$row_resultado_recibos['periodo']."/".$row_resultado_recibos['recibo']."' target='_blank'> Ver Recibo de Sueldo </a>"; }
                      ?>
                    </div>
                  </td>                  
                </tr>
                <?php }while($row_resultado_recibos = $resultado_recibos->fetch_assoc());?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
