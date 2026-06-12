<?php // index.php

// Incluir el controlador/proveedor si es ABSOLUTAMENTE necesario aquí para $row.
// Sin embargo, idealmente esta lógica de obtener $row debería estar en home.php o en un modelo,
// ya que index.php debería ser más una página de login o un menú estático.
// Si $row['nombre'] es parte del contexto de autenticación, ese proceso DEBE ser server-side.
// Por ahora, lo dejo para que el código PHP que dependa de ello no se rompa,
// pero es un punto a refactorizar para una autenticación segura.
include_once 'controller/canieros.php'; // Considerar si esto es necesario aquí

// El título de la página para el header.php
$page_title = 'Acceso Cañeros';

// Incluir la cabecera común
include_once 'views/includes/header.php';

// Obtener el usuario y proveedor de la URL si existen (esto podría venir de una página de login)
// La decodificación de base64 aquí es un posible riesgo si no se valida el origen.
$usuario_get = !empty($_GET['usuario']) ? $_GET['usuario'] : '';
$cuit_caniero = !empty($usuario_get) ? base64_decode($usuario_get) : '';

$nombre = isset($row['nombre']) ? $row['nombre'] : ''; // Depende de controller/canieros.php

?>
<h1 class="mb-4 align-items-left">
            <img src="assets/img/Logo-Ing La Corona.png" width="60px" class="img-fluid me-2" alt="Logo">
            Cañeros
</h1> 

<div class="text-end">
        <a href="/coronaweb/extranet" class="btn btn-secondary mt-3">
            <img src="assets/ico/ico_regresar.png" height="24" width="24" alt="Regresar" class="me-1">
            Salir
        </a>
</div>

<div class="row justify-content-center">
            <div class="col-2 mb-1">
                <a href="home.php?accion=ver_detalle_cania&usuario=<?php echo htmlspecialchars($usuario_get) ?>&nombre=<?php echo htmlspecialchars($nombre) ?>" class="d-block text-center text-decoration-none card-hover p-3">
                    <img src="assets/ico/sub_menu/detalle_canieros.png" width="150px" class="img-fluid mb-2" alt="">
                    <h4 class="text-secondary">Detallado de Caña</h4>
                </a>
            </div>            
            <div class="col-2 mb-1">
                <a href="home.php?accion=ver_cta_cte_cania&usuario=<?php echo htmlspecialchars($usuario_get) ?>&nombre=<?php echo htmlspecialchars($nombre) ?>" class="d-block text-center text-decoration-none card-hover p-3">
                        <img src="assets/ico/sub_menu/cta_cte.png" width="150px" class="img-fluid mb-2" alt="">
                        <h4 class="text-secondary">Cuenta Corriente</h4>
                </a>
            </div>
            <?php //if(OCAzucarCaniero($cuit_caniero)){?>
            <!--    
            <div class="col-2 mb-1">
                <a href="home.php?accion=ver_oc_azucar&usuario=<?php //echo htmlspecialchars($usuario_get) ?>&nombre=<?php //echo htmlspecialchars($nombre) ?>" class="d-block text-center text-decoration-none card-hover p-3">
                        <img src="assets/ico/sub_menu/orden_compra.png" width="150px" class="img-fluid mb-2" alt="">
                        <h4 class="text-secondary">OC de Azucar</h4>
                </a>
            </div>   
            <?php //}?>
            -->         
            <?php if(OPAzucarCaniero($cuit_caniero)){?>
            <div class="col-2 mb-1">
                <a href="home.php?accion=ver_op_azucar&usuario=<?php echo htmlspecialchars($usuario_get) ?>&nombre=<?php echo htmlspecialchars($nombre) ?>" class="d-block text-center text-decoration-none card-hover p-3">
                        <img src="assets/ico/sub_menu/orden_pago.png" width="150px" class="img-fluid mb-2" alt="">
                        <h4 class="text-secondary">Ordenes de Pago</h4>
                </a>
            </div>
            <?php }?> 
        </div>   
        <p class="text-end m-2"><a href="home.php?accion=cambiar_pass&usuario=<?php echo htmlspecialchars($usuario_get) ?>&regresar_menu=canieros" class="btn btn-sm btn-info">Cambiar Contraseña</a></p>   

