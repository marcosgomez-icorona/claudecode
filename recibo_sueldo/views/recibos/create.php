<?php    
    include_once 'controller/liquidaciones.php';    
?>
<div class="card m-2">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div class="col-8">
      <h4>Generar Periodo de Liquidacion</h4>
    </div>    
    <div class="col-4 m-1">
      <a href="view.php?opcion=ver_recibos&legajo=<?php echo $_GET['legajo']?>" class="btn btn-outline-success my-1 my-sm-0 btn-sm">Volver al Listado...</a>
    </div>
  </div>
  <form action="" method="POST" enctype="multipart/form-data">
    <div class="card-body">            
      <div class="row m-2">
        <div class="col-6 m-1">
          Periodo 
          <select name="periodo" id="" class="form-control mb-2">
            <option value="">Seleccionar...</option>
            <option value="1">Enero</option>
            <option value="2">Febrero</option>                      
            <option value="3">Marzo</option>                      
            <option value="4">Abril</option>                      
            <option value="5">Mayo</option>                      
            <option value="6">Junio</option>                      
            <option value="7">Julio</option>                      
            <option value="8">Agosto</option>                      
            <option value="9">Septiembre</option>                      
            <option value="10">Octubre</option>                      
            <option value="11">Noviembre</option>                      
            <option value="12">Diciembre</option>                      
          <select>
        </div>
        <div class="col-2 m-1">
          Año
          <input name="anio"   class="form-control mb-2" value="<?php echo $anio;?>">           
        </div>        
      </div>      
      <div class="row m-2">
        <input  hidden=""  name="accion"  value="alta_periodo" />         
      </div>
    </div>
    <div class="row m-2">
      <button class="btn btn-primary btn-block" type="submit">Agregar</button>
    </div>
  </form>
</div>