<?php
    include_once 'controller/entidades.php';
    include_once 'controller/ordenes_trabajo.php';
    
    $row_resultado_ot=ver_ot($_GET['nro_ot']);
?>
<div class="card my-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div class="col-11">
      <h4>Modificar Orden de Trabajo</h4>
    </div>        
    <div class="col-1 m-1">
      <a href="javascript: close()" class="btn btn-outline-success my-1 my-sm-0 btn-sm">Cerrar</a>
    </div>
  </div>
  <div class="card-body bg-light">
    <form id="form-mod-ot" action="" method="POST" enctype="multipart/form-data">
        <div class="row m-2">        
            <p class=" h3 text-secondary">Orden de Trabajo:  <?php if(!empty($_GET['nro_ot'])){ echo $_GET['nro_ot']; }?></p>
        </div>    
        <div class="row">
          <div class="col-6">                                          
              <p> <label for="">Equipo:  <?php if(!empty($row_resultado_ot)){ echo $row_resultado_ot['descripcionampliada']." - ".$row_resultado_ot['codigoequipo']; }?></label></p>
          </div>
          <div class="col-6">
            Tipo Orden
            <select name="tipoorden" id="" class="form-control mb-2">
              <?php if(!empty($row_resultado_ot['tipoorden'])){ 
                      echo '<option value="'.$row_resultado_ot['tipoorden'].'" selected="">'.$row_resultado_ot['tipoorden'].'</option>'; 
                    }else{
                            echo '<option value="">Seleccionar...</option>  ';
                    }                                   
                    while($row_resultado_tipoorden = $resultado_tipoorden->fetch_assoc()){
                      if(!empty($row_resultado_tipoorden)){ 
                        echo '<option value='.$row_resultado_tipoorden['descripcion'].'>'.$row_resultado_tipoorden['descripcion'].'</option>';
                      }
                    }
              ?>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-3">
            Estado
            <select name="estado" id="" class="form-control mb-2">          
              <?php 
                    if(!empty($row_resultado_ot['estado'])){ 
                      echo '<option value="'.$row_resultado_ot['estado'].'" selected="">'.$row_resultado_ot['estado'].'</option>'; 
                    }else{
                            echo '<option value="">Seleccionar...</option>  ';
                    }                                            
                    while($row_resultado_estados = $resultado_estados->fetch_assoc()){
                      if(!empty($row_resultado_estados)){ 
                        echo '<option value='.$row_resultado_estados['nombre'].'>'.$row_resultado_estados['nombre'].'</option>';
                      }
                    }
              ?>
            </select>
          </div>
          <div class="col-3">
            Prioridad
            <select name="prioridad" id="" class="form-control mb-2">
              <?php 
                if(!empty($row_resultado_ot['prioridad'])){ 
                  echo '<option value="'.$row_resultado_ot['prioridad'].'" selected="">'.$row_resultado_ot['prioridad'].'</option>'; 
                }else{
                        echo '<option value="">Seleccionar...</option>  ';
                }
              ?>
              <option value="Baja">Baja</option>
              <option value="Media">Media</option>
              <option value="Alta">Alta</option>        
              <option value="Alta">Urgente</option>        
            </select>
          </div>
          <div class="col-3">
            Fecha Inicio<input name="fechainicio" id="datepicker1" class="form-control" placeholder="" aria-label="Search" value="<?php if(!empty($row_resultado_ot)){ echo FechaDma($row_resultado_ot['fechainicio']); }?>">
          </div>
          <div class="col-3">
            Fecha Fin<input name="fechafin" id="datepicker2" class="form-control mr-sm-1" placeholder="" aria-label="Search" value="<?php if(!empty($row_resultado_ot)){ echo FechaDma($row_resultado_ot['fechafin']); }?>">
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            Tarea
            <input  type="text" name="tarea"  placeholder=""   class="form-control mb-2" value="<?php if(!empty($row_resultado_ot)){ echo $row_resultado_ot['tarea']; }?>"/> 
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            Originador
            <select name="originador" id="" class="form-control mb-2">          
              <?php                                            
                    if(!empty($row_resultado_ot['originador'])){ 
                      echo '<option value="'.$row_resultado_ot['originador'].'" selected="">'.$row_resultado_ot['originador'].'</option>'; 
                    }else{
                            echo '<option value="">Seleccionar...</option>  ';
                    } 

                    while($row_resultado_originador = $resultado_originador->fetch_assoc()){
                      if(!empty($row_resultado_originador)){ 
                        echo '<option value='.$row_resultado_originador['nombre'].'>'.$row_resultado_originador['nombre'].'</option>';
                      }
                    }
              ?>
            </select>
          </div>
          <div class="col-4">
            Ejecutante
            <select name="ejecutante" id="" class="form-control mb-2">
              <?php                                            
                    if(!empty($row_resultado_ot['ejecutante'])){ 
                      //$ejecutante = strval($row_resultado_ot['ejecutante']);
                      echo '<option value="'.$row_resultado_ot['ejecutante'].'" selected="">'.$row_resultado_ot['ejecutante'].'</option>'; 
                    }else{
                            echo '<option value="">Seleccionar...</option>  ';
                    } 

                    while($row_resultado_ejecutante = $resultado_ejecutante->fetch_assoc()){
                      if(!empty($row_resultado_ejecutante)){ 
                        echo '<option value="'.$row_resultado_ejecutante['nombre'].'">'.$row_resultado_ejecutante['nombre'].'</option>';
                      }
                    }
              ?>
            </select>
          </div>
          <div class="col-4">
            Periodo
            <select name="periodo" id="" class="form-control mb-2">
            <?php 
                if(!empty($row_resultado_ot['periodo'])){ 
                  echo '<option value="'.$row_resultado_ot['periodo'].'" selected="">'.$row_resultado_ot['periodo'].'</option>'; 
                }else{
                        echo '<option value="">Seleccionar...</option>  ';
                }
              ?>
              <option value="Zafra">Zafra</option>
              <option value="Reparacion">Reparacion</option>
            </select>      
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            Plan<input name="planprogramado" class="form-control mr-sm-1" type="text" value="<?php if(!empty($row_resultado_ot)){ echo $row_resultado_ot['planprogramado']; }?>">
          </div>
          <div class="col-4">
            Parada Molienda
            <select name="paradamolienda" id="" class="form-control mb-2">  
            <?php 
                if(!empty($row_resultado_ot['paradamolienda'])){ 
                  echo '<option value="'.$row_resultado_ot['paradamolienda'].'" selected="">'.$row_resultado_ot['paradamolienda'].'</option>'; 
                }
              ?>
              <option value="No" selected="">No</option>
              <option value="Si">Si</option>
            </select>
          </div>
          <div class="col-4">
            Avance<input name="avance" class="form-control mr-sm-1" type="text" value="<?php if(!empty($row_resultado_ot)){ echo $row_resultado_ot['avance']; }?>">
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            Observaciones
            <textarea name="observaciones" id="" class="form-control mr-sm-1" cols="30" rows="10"><?php if(!empty($row_resultado_ot)){ echo $row_resultado_ot['observaciones']; }?></textarea>
          </div>
        </div>
        <div class="row">
          <input  hidden=""  name="accion"  value="modificar_ot" />       
        </div>
        <div class="row m-1">
          <button class="btn btn-primary btn-block" type="submit">Modificar</button>
        </div>
      </div>
    </form>
</div>


