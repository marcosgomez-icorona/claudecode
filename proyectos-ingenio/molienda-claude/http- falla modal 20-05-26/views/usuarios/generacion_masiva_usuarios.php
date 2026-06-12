<?php 
    include_once 'controller/usuarios.php';
    if(!empty($_POST['generar'])){
        generar_usuarios_personal();
    }	
?>
<div class="row justify-content-center">
    <div class="col-lg-3">
        <div class="card shadow-lg border-0 rounded-lg mt-5">
            <div class="card-header">
                <div class="row">                    
                    <div class="col-12">
                        <h3 class="text-center font-weight-light my-4">Genracion Masiva de Usuarios de Empleados</h3>
                    </div>
                </div>
                
            </div>
            <div class="card-body">
                <form method="POST">                                    
                    <div class="form-group ">
                        <button type="submit" name="generar" value="SI" class="btn btn-secondary form-control">Generar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
