<?php
// index.php
include_once 'controller/canieros.php'; // evaluar mover lógica a modelo/controlador
$page_title = 'Acceso Cañeros';
include_once 'views/includes/header.php';

$usuario_get = filter_input(INPUT_GET, 'usuario', FILTER_DEFAULT) ?: '';
$cuit_caniero = $usuario_get !== '' ? base64_decode($usuario_get) : '';
$nombre = isset($row['nombre']) ? $row['nombre'] : '';
?>

<style>
    /* Estructura del ítem: ancho ajustado, texto debajo, sin overlay activo */
    .menu-item {
        display: inline-block;
        text-align: center;
        position: relative;
        color: inherit;
    }
    .menu-item .inner-content {
        display: inline-block;
        padding-left: 2.5%;
        padding-right: 2.5%;
        box-sizing: border-box;
    }

    /* Icon wrapper y títulos */
    .menu-item .icon-wrapper {
        position: relative;
        width: 150px;
        margin: 0 auto;
    }
    .overlay-title {
        display: none;
    }
    .normal-title {
        display: block;
    }

    /* Hover / elevación */
    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        background: #fff;
    }
    .card-hover:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        background-color: #f8f9fa;
    }
    .card-hover h4 {
        transition: color 0.3s;
    }
    .card-hover:hover h4 {
        color: #0d6efd;
    }

    /* Contenedor flexible para los íconos */
    .menu-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px; /* separación horizontal cuando están en fila */
        justify-content: center;
    }

    /* >7 pulgadas: fila con separación de 4px */
    @media (min-width: 673px) {
        .menu-grid .menu-col {
            flex: 0 0 auto;
        }
    }

    /* ≤7 pulgadas: vertical */
    @media (max-width: 672px) {
        .menu-grid {
            flex-direction: column;
            gap: 8px; /* un poco más de separación vertical */
            align-items: center;
        }
        .menu-grid .menu-col {
            width: 100%;
            display: flex;
            justify-content: center;
        }
    }
</style>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3 justify-content-end">
    <div class="d-flex justify-content-end">
        <a href="/coronaweb/extranet" class="btn btn-secondary">
            <img src="assets/ico/ico_regresar.png" height="24" width="24" alt="Regresar" class="me-1">
            Salir
        </a>
    </div>
</div>
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3 justify-content-center">
    <div class="d-flex align-items-center">
        <img src="assets/img/Logo-Ing La Corona.png" width="60" class="img-fluid me-2" alt="Logo">
        <h2 class="h2 mb-0">Cañeros</h2>
    </div>
</div>
<hr>
<div class="menu-grid">
    <div class="menu-col">
        <a href="home.php?accion=ver_detalle_cania&usuario=<?php echo htmlspecialchars($usuario_get) ?>&nombre=<?php echo htmlspecialchars($nombre) ?>"
           class="card-hover p-3 menu-item menu-link"
           aria-label="Ordenes de Compra">
            <div class="inner-content">
                <div class="icon-wrapper mb-2">
                    <img src="assets/ico/sub_menu/detalle_canieros.png"
                     class="img-fluid"
                     alt="Detallado de Caña">
                <div class="overlay-title">Detallado de Caña</div>
            </div>
            <h4 class="text-secondary normal-title fs-6 mb-0">Detallado de Caña</h4>
            </div>
        </a>
    </div>
    <div class="menu-col">
        <a href="home.php?accion=ver_cta_cte_cania&usuario=<?php echo htmlspecialchars($usuario_get) ?>&nombre=<?php echo htmlspecialchars($nombre) ?>"
           class="card-hover p-3 menu-item menu-link"
           aria-label="Ordenes de Compra">
            <div class="inner-content">
                <div class="icon-wrapper mb-2">
                    <img src="assets/ico/sub_menu/cta_cte.png"
                     class="img-fluid"
                     alt="Cuenta Corriente">
                <div class="overlay-title">Cuenta Corriente</div>
            </div>
            <h4 class="text-secondary normal-title fs-6 mb-0">Cuenta Corriente</h4>
            </div>
        </a>
    </div>
    <?php //if(OPAzucarCaniero($cuit_caniero)){ ?>
    <div class="menu-col">
        <a href="home.php?accion=ver_op_azucar&usuario=<?php echo htmlspecialchars($usuario_get) ?>&nombre=<?php echo htmlspecialchars($nombre) ?>"
           class="card-hover p-3 menu-item menu-link"
           aria-label="Ordenes de Compra">
            <div class="inner-content">
                <div class="icon-wrapper mb-2">
                    <img src="assets/ico/sub_menu/orden_pago.png"
                     class="img-fluid"
                     alt="Órdenes de Pago">
                <div class="overlay-title">Órdenes de Pago</div>
            </div>
            <h4 class="text-secondary normal-title fs-6 mb-0">Ordenes de Pago</h4>
            </div>
        </a>
    </div>
    <?php //} ?>
</div>

<p class="text-end mt-3 mb-1">
    <a href="home.php?accion=cambiar_pass&usuario=<?php echo htmlspecialchars($usuario_get) ?>&regresar_menu=canieros"
       class="btn btn-sm btn-info">Cambiar Contraseña</a>
</p>
