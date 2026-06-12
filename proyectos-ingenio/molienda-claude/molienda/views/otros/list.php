<?php
  $ots_equipo=ver_ots_equipo($_GET['id_equipo']);
?>
<form action="" id="form-list-ot" action="home.php?menu=equipos&accion=estado_equipo" method="POST">
  <div class="table-responsive">
                            <table class="table">
                              <thead class="p-3 mb-2 bg-info text-white">
                                  <tr>
                                      <th scope="col">Nro OT</th>
                                      <th scope="col">Tipo</th>
                                      <th scope="col">Estado</th>
                                      <th scope="col">Tarea</th>
                                      <th scope="col">Prioridad</th>
                                      <th scope="col">Originador</th>
                                      <th scope="col">Ejecutante</th>
                                      <th scope="col">Periodo</th>
                                      <th scope="col">Creado</th>
                                      <th scope="col">Acciones</th>
                                      
                                  </tr>
                              </thead>
                              <tbody>
                                <?php
                                      while($row_ots_equipo = $ots_equipo->fetch_assoc()) {
                                ?>
                                  <tr <?php if(!empty($row_ots_equipo)){ echo asignar_color_orden_trabajo($row_ots_equipo['estado']);}?> >
                                      <td><?php if(!empty($row_ots_equipo)){ $nro_ot=$row_ots_equipo['numero']; echo $nro_ot;  }else{$nro_ot='';}?></td>                                      
                                      <td><?php if(!empty($row_ots_equipo)){ echo $row_ots_equipo['tipoorden']; }?></td>
                                      <td><?php if(!empty($row_ots_equipo)){ echo $row_ots_equipo['estado']; }?></td>
                                      <td><?php if(!empty($row_ots_equipo)){ echo $row_ots_equipo['tarea']; }?></td>
                                      <td><?php if(!empty($row_ots_equipo)){ echo $row_ots_equipo['prioridad']; }?></td>
                                      <td><?php if(!empty($row_ots_equipo)){ echo $row_ots_equipo['originador']; }?></td>
                                      <td><?php if(!empty($row_ots_equipo)){ echo $row_ots_equipo['ejecutante']; }?></td>
                                      <td><?php if(!empty($row_ots_equipo)){ echo $row_ots_equipo['periodo']; }?></td>                                      
                                      <td><?php if(!empty($row_ots_equipo)){ echo FechaDma($row_ots_equipo['fechainicio']); }?></td>
                                      <td>
                                        <div class="btn-group btn-group-sm" role="group" aria-label="...">
                                          <a href="javascript: ver_detalle_ot(<?php echo $nro_ot;?>);" class="btn btn-secondary my-1 my-sm-0 btn-sm">Previsualizar</a>
                                          <a href="javascript: modificar_ot(<?php echo $nro_ot;?>);" class="btn btn-warning my-1 my-sm-0 btn-sm">Modificar</a>
                                          <a href="javascript: ver_tareas_ot(<?php echo $nro_ot;?>);" class="btn btn-info my-1 my-sm-0 btn-sm">Tareas</a>                                                                                    
                                          <a href="javascript: ver_ordenestrabajo_insumos(<?php echo $nro_ot;?>);" class="btn btn-info my-1 my-sm-0 btn-sm">Insumos y Repuestos</a>                                                                                    
                                          <a href="javascript: ver_adjunto_ordenes(<?php echo $nro_ot;?>);" class="btn btn-info my-1 my-sm-0 btn-sm">Comentarios y Anexos</a>                                                                                    
                                        </div>  
                                  </tr>
                                  <?php }?>
                              </tbody>
                            </table>
  </div>
    <input type="submit" name="ejecutar" id="ejecutar" hidden="">
</form>
  