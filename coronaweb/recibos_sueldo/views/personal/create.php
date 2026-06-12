<?php    
    include_once 'controller/personal.php';    
?>
<div class="container">
    <div class="row">
        <p class="h2">Agregar Persona</p>
    </div>
    <div class="row m-2">
        <div class="card m-1">
            <div class="card-header d-flex justify-content-between align-items-center">   
                <div class="row">                     
                    <div class="col-4 m-1">
                        <a href="home.php?menu=ver_personal" class="btn btn-outline-success my-1 my-sm-0 btn-sm">Volver al Listado...</a>
                    </div>
                </div>
            </div> 
            <div class="card-body  bg-light">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row m-2">
                        <div class="col-md-2 mb-1">
                            <label for="">DNI</label>
                            <input type="text" class="form-control" name="dni">
                        </div>
                        <div class="col-md-2 mb-1">
                            <label for="">Legajo</label>
                            <input type="text" class="form-control" name="legajo">
                        </div>
                        <div class="col-md-2 mb-1">
                            <label for="">Nombre</label>
                            <input type="text" class="form-control" name="nombre">
                        </div>                        
                        <div class="col-md-2 mb-1">
                            <label for="tipo_convenio">Tipo de Convenio</label>
                            <select name="tipo_convenio" id="tipo_convenio" class="form-control">
                                <option value="">Seleccionar...</option>
                                <option value="FOTIA">FOTIA</option>
                                <option value="FEIA">FEIA</option>
                                <option value="FUERA DE CONVENIO">FUERA DE CONVENIO</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-1">
                            <label for="tipo_liquidacion">Tipo de Liquidacion</label>
                            <select name="tipo_liquidacion" id="tipo_liquidacion" class="form-control">
                                <option value="">Seleccionar...</option>
                                <option value="MENSUAL">MENSUAL</option>
                                <option value="QUINCENAL">QUINCENAL</option>
                            </select>
                        </div>                        
                    </div>
                    <div class="row m-2">
                        <input type="hidden" name="accion" value="alta_persona">
                        <button class="btn btn-primary btn-block" type="submit">Agregar</button>
                    </div>
                </form>
            </div>   
        </div>
    </div>
</div>
      
    

