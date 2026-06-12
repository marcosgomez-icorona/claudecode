<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingenio Azucarero</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            margin: 0;
            background-color: #e8f5e9; /* verde claro */
        }

        .navbar {
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .logo {
            height: 60px;
            margin-right: 20px;
        }

        .main-image {
            width: 100%;
            height: auto;
            opacity: 0.9;
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.2);
        }

        .nav-link {
            font-weight: 500;
            color: #2e7d32 !important;
        }

        .nav-link:hover {
            text-decoration: underline;
        }
        .main-image {
            width: 100%;
            height: auto;
            opacity: 0.7; /* Atenuación al 60% */
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.2);
        }

    </style>
</head>
<body>

    <!-- Encabezado con logo e ítems de menú -->
    <nav class="navbar navbar-expand-lg px-4">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">            
                <img src="assets/img/titulo corona.png" alt="Ingenio" class="logo">              
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-start" id="navbarNav">
                <ul class="navbar-nav ms-3">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Historia</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Portal</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Imagen principal -->
    <div class="container-fluid p-0">
        <img src="assets/img/img_ppal.png" alt="Imagen Principal" class="main-image">
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
