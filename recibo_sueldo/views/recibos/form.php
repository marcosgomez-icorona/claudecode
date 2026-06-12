<?php  
  include_once 'controller/recibos.php';
  include_once 'controller/usuarios.php';
  
  if(!empty($_GET['legajo'])){
    //Decodifica Legajo
    $legajo= base64_decode($_GET['legajo']);    
    //$legajo=$_GET['legajo'];
  }else{
    $legajo=-1;
  }  
  $recibos_empleado=listado_recibos($legajo,'empleado','');    
  $row_recibos_empleado = $recibos_empleado->fetch_assoc();
?>
<div class="container">
  <div class="row">
    <div class="col-2">
      <h3 class="text-left font-weight-light my-4"><img src="assets/img/Logo-Ing La Corona.png" width="50px" alt=""></h3>
    </div>
  </div>
  <div class="row">
    <div class="col-2">      
    </div>
    <div class="col-9">
      <h1 class="display-3 text-secondary">Recibos de Sueldo</h1>
    </div>
  </div>    
  <div class="row m-2">
    <div class="col-12">
      <div class="card m-1">
        <div class="card-header">  
          <div class="row">
            <div class="col-10">
              <label for=""><div class="h2 text-info"> <?php if(!empty($row_recibos_empleado)){ echo $row_recibos_empleado['nombre']; }?></div></label>
            </div>
            <div class="col-2 align-items-center">
              <a href="view.php?menu=cambiar_clave&legajo=<?php echo $_GET['legajo']?>" class="btn btn-outline-secondary btn-sm">Cambiar Clave</a>
            </div>
          </div>
            
            <div class="row m-1 box shadow p-3 mb-5 bg-secondary rounded">
              <div class="col-5">
                <div class="h3 text-white">
                  <label for="">Ultimo Recibo de Sueldo</label>
                </div>
              </div>
              <div class="col-1">
                <div class="h2 text-white">
                  <label for=""><?php if(!empty($row_recibos_empleado)){ echo "<a class='btn btn-success my-1 my-sm-0 btn-sm' href='assets/recibos/".$row_recibos_empleado['anio']."-".$row_recibos_empleado['periodo']."/".$row_recibos_empleado['recibo']."' target='_blank'> Ver Recibo Periodo ".$row_recibos_empleado['periodo'].'-'.$row_recibos_empleado['anio']." </a>"; }?></label>
                </div>
              </div>
            </div>  
        </div>
        <div class="card-body m-2">   
          <div class="row">
            <div class="col-7">
              <p class="h3">Liquidaciones Anteriores</p>
              <div class="box shadow p-3 mb-5 bg-body rounded">
                <div class="table-responsive table-striped">
                  <table class="table">
                    <thead class="p-3 mb-2 bg-info text-white">
                      <tr>                
                        <th scope="col">Periodo</th>                                                                            
                        <th scope="col">Tipo Liquidacion</th>                                                                            
                        <th scope="col">Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        while($row_recibos_empleado = $recibos_empleado->fetch_assoc()){
                      ?>
                      <tr >  
                        <td><label for=""><?php if(!empty($row_recibos_empleado)){ echo $row_recibos_empleado['periodo'].' - '.$row_recibos_empleado['anio']; }?></label></td>                  
                        <td><label for=""><?php if(!empty($row_recibos_empleado)){ echo $row_recibos_empleado['tipo_liquidacion']; }?></label></td>                  
                        <td>
                          <div class="btn-group btn-group-sm" role="group" aria-label="...">
                            <?php 
                                if(!empty($row_recibos_empleado)){ echo "<a class='btn btn-info my-1 my-sm-0 btn-sm' href='assets/recibos/".$row_recibos_empleado['anio']."-".$row_recibos_empleado['periodo']."/".$row_recibos_empleado['recibo']."' target='_blank'> Ver Recibo de Sueldo</a>"; }
                            ?>
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
      </div>
    </div>
</div>
