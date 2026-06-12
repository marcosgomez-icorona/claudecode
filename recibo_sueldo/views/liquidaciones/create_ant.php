<?php    
    include_once 'controller/liquidaciones.php';  
    $tipos_convenio= lista_tipos_convenios();

?>
<div class="container">
    <div class="row">
        <p class="h2">Agregar Liquidacion</p>
    </div>
    <div class="row m-2">
        <div class="card m-1">
            <div class="card-header d-flex justify-content-between align-items-center">   
                <div class="row">
                    <div class="col-6">
                        <label for="">Generar Periodo de Liquidacion</label>
                    </div>    
                    <div class="col-4 m-1">
                        <a href="home.php?menu=ver_liquidaciones" class="btn btn-outline-success my-1 my-sm-0 btn-sm">Volver al Listado...</a>
                    </div>
                </div>
            </div> 
            <div class="card-body  bg-light">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row m-2">
                        <div class="col-md-2 mb-1">
                            <label for="periodo">Periodo</label>
                            <select name="periodo" id="periodo" class="form-control">
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
                            </select>
                        </div>
                        <div class="col-md-2 mb-1">
                            <label for="anio">Año</label>
                            <input name="anio" id="anio" class="form-control" value="<?php echo $anio;?>">
                        </div>
                        <div class="col-md-2 mb-1">
                            <label for="tipo_convenio">Tipo de Convenio</label>
                            <select name="tipo_convenio" id="tipo_convenio" class="form-control">
                                <option value="">Seleccionar...</option>
                                <?php
                                while($row_tipos_convenio=$tipos_convenio->fetch_assoc()){
                                    echo '<option value="'.$row_tipos_convenio['tipo_convenio'].'">'.$row_tipos_convenio['tipo_convenio'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-1">
                            <label for="tipo_liq">Tipo de Liquidacion</label>
                            <select name="tipo_liq" id="tipo_liq" class="form-control">
                                <option value="">Seleccionar...</option>
                                <option value="MENSUAL">MENSUAL</option>
                                <option value="QUINCENAL">QUINCENAL</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-1">
                            <label for="tipo_convenio">Quincena</label>
                            <select name="quincena" id="quincena" class="form-control">
                                <option value="">Seleccionar...</option>
                                <option value="PRIMERA QUINCENA">PRIMERA QUINCENA</option>
                                <option value="SEGUNDA QUINCENA">SEGUNDA QUINCENA</option>
                            </select>
                        </div>
                    </div>
                    <div class="row m-2">
                        <input type="hidden" name="accion" value="alta_periodo">
                        <button class="btn btn-primary btn-block" type="submit">Agregar</button>
                    </div>
                </form>
            </div>   
        </div>
    </div>
</div>
      
    

