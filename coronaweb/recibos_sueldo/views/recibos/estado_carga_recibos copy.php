<?php
include_once 'controller/recibos.php';
include_once 'controller/usuarios.php';

$result = estado_carga_recibos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de Carga de Recibos</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS para funcionalidades avanzadas de tabla -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .header h2 {
            margin: 0;
            font-weight: 600;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        .estado-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 500;
            font-size: 0.85em;
        }
        
        .estado-completo {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .estado-parcial {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        
        .estado-pendiente {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 10px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><i class="fas fa-file-invoice"></i> Estado de Carga de Recibos</h2>
            <p class="mb-0 mt-2">Sistema de control de liquidaciones cargadas</p>
        </div>
        
        <div class="table-container">
            <table id="tablaRecibos" class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th><i class="fas fa-calendar"></i> Año</th>
                        <th><i class="fas fa-calendar-alt"></i> Mes</th>
                        <th><i class="fas fa-tag"></i> Tipo de Liquidación</th>
                        <th><i class="fas fa-chart-bar"></i> Cant. Cargados</th>
                        <!-- 
                        <th><i class="fas fa-info-circle"></i> Estado</th>
                         -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($result as $row):
                            // Definir el estado basado en la cantidad cargada
                            $cant_cargados = $row['cant_cargados'];
                            $estado = '';
                            $estado_class = '';
                            
                            // Aquí puedes personalizar los criterios de estado según tus necesidades
                            if ($cant_cargados >= 100) {
                                $estado = 'Completo';
                                $estado_class = 'estado-completo';
                            } elseif ($cant_cargados >= 50) {
                                $estado = 'Parcial';
                                $estado_class = 'estado-parcial';
                            } else {
                                $estado = 'Pendiente';
                                $estado_class = 'estado-pendiente';
                            }
                            
                            // Convertir número de mes a nombre del mes (opcional)
                            $mes_num = str_pad($row['MES'], 2, '0', STR_PAD_LEFT);
                            $nombres_meses = [
                                '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
                                '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
                                '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
                                '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
                            ];
                            $nombre_mes = isset($nombres_meses[$mes_num]) ? $nombres_meses[$mes_num] : $row['MES'];
                            
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['ANIO']) . "</td>";
                            echo "<td>" . htmlspecialchars($nombre_mes) . "</td>";
                            echo "<td>" . htmlspecialchars($row['tipo_liquidacion']) . "</td>";
                            echo "<td class='text-center fw-bold'>" . number_format($row['cant_cargados']) . "</td>";
                           // echo "<td><span class='estado-badge " . $estado_class . "'>" . htmlspecialchars($estado) . "</span></td>";
                            echo "</tr>";
                         endforeach;
                    ?>
                </tbody>
            </table>
        </div>        
        
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inicializar DataTable con opciones personalizadas
            $('#tablaRecibos').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 10,
                responsive: true,
                order: [[0, 'desc'], [1, 'desc'], [2, 'asc']], // Ordenar por Año desc, Mes desc, Tipo Liquidación asc
                columnDefs: [
                    { targets: [3], className: 'text-center' },
                    { targets: [4], className: 'text-center' }
                ]
            });
        });
    </script>
</body>
</html>
