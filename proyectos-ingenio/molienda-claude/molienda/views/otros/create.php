<?php
    //include 'controller/_ordenes_trabajo.php';
?>
<div class="card-body bg-light">
  <div class="row">
    <div class="col-12">
      <p class="m-1" >Fecha <?php echo date('d/m/Y');?></p> 
    </div>  
  </div>
  <div class="row">
    <div class="col-6">                                          
        <p>Equipo:  <?php if(!empty($row_resultado_equipo)){ echo $row_resultado_equipo['descripcionampliada']." - ".$row_resultado_equipo['codigoequipo']; }?></p>
    </div>
    <div class="col-6">
      Tipo Orden
      <select name="tipoorden" id="" class="form-control mb-2">
        <option value="">Seleccionar...</option>  
        <?php                                            
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
        <option value="">Seleccionar...</option>  
        <?php                                            
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
        <option value="">Seleccionar...</option>
        <option value="Baja">Baja</option>
        <option value="Media">Media</option>
        <option value="Alta">Alta</option>        
        <option value="Alta">Urgente</option>        
      </select>
    </div>
    <div class="col-3">
      Fecha Inicio<input name="fechainicio" id="datepicker1" class="form-control" placeholder="" aria-label="Search">
    </div>
    <div class="col-3">
      Fecha Fin<input name="fechafin" id="datepicker2" class="form-control mr-sm-1" placeholder="" aria-label="Search">
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      Tarea
      <input  type="text" name="tarea"  placeholder=""   class="form-control mb-2" /> 
    </div>
  </div>
  <div class="row">
    <div class="col-4">
      Originador
      <select name="originador" id="" class="form-control mb-2">
        <option value="">Seleccionar...</option>  
        <?php                                            
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
        <option value="">Seleccionar...</option>  
        <?php                                            
              while($row_resultado_ejecutante = $resultado_ejecutante->fetch_assoc()){
                if(!empty($row_resultado_ejecutante)){ 
                  echo '<option value='.$row_resultado_ejecutante['nombre'].'>'.$row_resultado_ejecutante['nombre'].'</option>';
                }
              }
        ?>
      </select>
    </div>
    <div class="col-4">
      Periodo
      <select name="periodo" id="" class="form-control mb-2">
        <option value="">Seleccionar...</option>
        <option value="Zafra">Zafra</option>
        <option value="Reparacion">Reparacion</option>
      </select>      
    </div>
  </div>
  <div class="row">
    <div class="col-4">
      Plan<input name="planprogramado" class="form-control mr-sm-1" type="text">
    </div>
    <div class="col-4">
      Parada Molienda
      <select name="paradamolienda" id="" class="form-control mb-2">        
        <option value="No" selected="">No</option>
        <option value="Si">Si</option>
      </select>
    </div>
    <div class="col-4">
      Avance<input name="avance" class="form-control mr-sm-1" type="text">
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      Observaciones
      <textarea name="observaciones" id="" class="form-control mr-sm-1" cols="30" rows="10"></textarea>
    </div>
  </div>
  <div class="row">
    <input  hidden=""  name="accion_ot"  value="alta_orden_trabajo" /> 
  </div>
</div>

