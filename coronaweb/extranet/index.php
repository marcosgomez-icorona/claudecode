<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extranet - Acceso a Clientes y Proveedores</title>
    
    <!-- Bootstrap CSS (Versión única, siempre la última estable) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-H1YWFQJh4vvXZVZDbwYJt1F9GcTu9UpROsbGGmGQj4wQ05X1EXQZlKz3Z3UtInHZFwSc7X8HcjqJX4vB5GHk0w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/jquery-ui.css">

    <!-- jQuery y Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery-ui.js"></script>    
    <!--
    <link rel="stylesheet" href="css/bootstrap-select.min.css">
    <script src="js/bootstrap-select.min.js"></script>   
    -->
    
    <!-- Buscador en Select -->
    <!-- CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- JS de jQuery (requerido por Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- JS de Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        body {
            /*background: url('/extranet/assets/img/fondo.png') no-repeat center top;*/
            background-size: cover;
            background-color: #faf7f0; /* Beige claro y suave */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .main-container {
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            padding: 30px;
            max-width: 600px;
            
        }

        .main-container h2 {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            color: #343a40;
            
        }        

    /* ... (tus estilos existentes, incluyendo los del body, .container, etc.) ... */

    /* --- Estilos para el Menú Dashboard Profesional --- */

    .menu-dashboard-item {
        display: block; /* Asegura que el enlace ocupe todo el espacio del contenedor */
        color: inherit; /* Hereda el color del texto para no ser el azul por defecto del enlace */
    }

    /* Estilo de la tarjeta del menú */
    .menu-dashboard-item .card {
        border: 1px solid #dee2e6; /* Borde sutil */
        border-radius: 10px; /* Bordes redondeados */
        overflow: hidden; /* Asegura que el contenido no se salga de los bordes redondeados */
        text-align: center;
        background-color: #ffffff;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); /* Sombra inicial suave */
    }

    /* Efectos al pasar el ratón */
    .transition-ease {
        transition: all 0.3s ease-in-out; /* Transición suave para todos los cambios */
    }

    .hover-shadow-lg:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; /* Sombra más pronunciada al pasar el ratón */
    }

    .hover-scale-up:hover {
        transform: translateY(-5px); /* Pequeño levantamiento al pasar el ratón */
    }

    /* Estilo del círculo del icono */
    .menu-icon-circle {
        width: 80px; /* Tamaño del círculo */
        height: 80px; /* Tamaño del círculo */
        border-radius: 50%; /* ¡Hace el círculo! */
        background-color: #f0f0f0; /* Fondo gris claro para el círculo */
        display: flex; /* Para centrar el ícono dentro */
        justify-content: center; /* Centrar horizontalmente */
        align-items: center; /* Centrar verticalmente */
        font-size: 2.5rem; /* Tamaño del ícono */
        color: #495057; /* Color del ícono (gris oscuro) */
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1); /* Sombra sutil para el círculo */
        transition: all 0.3s ease-in-out; /* Transición para el círculo */
    }
    
    .card-submenu {
        background-color: #f9f9f9; /* Fondo claro */
        border-radius: 1rem; /* Bordes redondeados */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra suave */
        padding: 2rem;
        transition: all 0.3s ease-in-out;
    }

    .card-submenu img {
        transition: filter 0.3s ease;
        border-radius: 0.75rem;
    }

    .card-submenu img:hover {
        filter: brightness(60%);
    }
    </style>
</head>
<body>
    <div class="main-container" style="width: 1024px;" >
        <h2>Acceso a Clientes y Proveedores</h2>
        <?php include 'views/usuarios/login_admin.php'; ?>  
    </div>
</body>
</html>
