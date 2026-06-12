<?php
include_once 'controller/recibos.php';
include_once 'controller/usuarios.php';

$result = estado_carga_recibos();

// Procesar datos para el cuadro resumen
$resumen_mensual = [];

foreach ($result as $row) {
    $anio = $row['ANIO'];
    $mes = $row['MES'];
    $tipo = $row['tipo_liquidacion'];
    $cantidad = $row['cant_cargados'];
    
    // Inicializar el array para el año-mes si no existe
    $key = $anio . '-' . $mes;
    if (!isset($resumen_mensual[$key])) {
        $resumen_mensual[$key] = [
            'anio' => $anio,
            'mes' => $mes,
            'mensuales' => 0,
            'quincenales' => 0
        ];
    }
    
    // Asignar según tipo de liquidación
    if (stripos($tipo, 'mensual') !== false) {
        $resumen_mensual[$key]['mensuales'] = $cantidad;
    } elseif (stripos($tipo, 'quincenal') !== false) {
        $resumen_mensual[$key]['quincenales'] = $cantidad;
    }
}

// Ordenar el resumen por año y mes descendente
usort($resumen_mensual, function($a, $b) {
    if ($a['anio'] == $b['anio']) {
        return $b['mes'] - $a['mes'];
    }
    return $b['anio'] - $a['anio'];
});
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
            max-width: 1400px;
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
            margin-bottom: 40px;
        }
        
        .resumen-container {
            margin-bottom: 40px;
        }
        
        .resumen-title {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .resumen-title h4 {
            margin: 0;
            font-weight: 600;
        }
        
        .estado-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 500;
            font-size: 0.85em;
            display: inline-block;
            min-width: 100px;
            text-align: center;
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
        
        .estado-sin-carga {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            font-weight: bold;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 10px;
            color: #6c757d;
        }
        
        .card-resumen {
            transition: transform 0.2s;
        }
        
        .card-resumen:hover {
            transform: translateY(-5px);
        }
        
        .badge-count {
            font-size: 1.2em;
            padding: 8px 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><i class="fas fa-file-invoice"></i> Estado de Carga de Recibos</h2>
            <p class="mb-0 mt-2">Sistema de control de liquidaciones cargadas</p>
        </div>
        
        <!-- Cuadro Resumen Mensual -->
        <div class="resumen-container">
            <div class="resumen-title">
                <h4><i class="fas fa-chart-line"></i> Resumen de Estado por Mes</h4>
                <p class="mb-0 mt-1">Control de carga de liquidaciones mensuales y quincenales</p>
            </div>
            
            <div class="table-container">
                <table id="tablaResumen" class="table table-bordered table-hover">
                    <thead class="table-success">
                        <tr>
                            <th><i class="fas fa-calendar"></i> Año</th>
                            <th><i class="fas fa-calendar-alt"></i> Mes</th>
                            <th><i class="fas fa-chart-line"></i> Mensuales</th>
                            <th><i class="fas fa-chart-bar"></i> Quincenales</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resumen_mensual as $item): 
                            // Determinar estado para Mensuales (umbral: 120)
                            $mensuales_cant = $item['mensuales'];
                            if ($mensuales_cant >= 120) {
                                $mensuales_estado = 'Completado';
                                $mensuales_class = 'estado-completo';
                            } elseif ($mensuales_cant > 0 && $mensuales_cant < 120) {
                                $mensuales_estado = 'Incompleto';
                                $mensuales_class = 'estado-pendiente';
                            } else {
                                $mensuales_estado = 'Sin carga';
                                $mensuales_class = 'estado-sin-carga';
                            }
                            
                            // Determinar estado para Quincenales (umbral: 200)
                            $quincenales_cant = $item['quincenales'];
                            if ($quincenales_cant >= 200) {
                                $quincenales_estado = 'Completado';
                                $quincenales_class = 'estado-completo';
                            } elseif ($quincenales_cant > 0 && $quincenales_cant < 200) {
                                $quincenales_estado = 'Incompleto';
                                $quincenales_class = 'estado-pendiente';
                            } else {
                                $quincenales_estado = 'Sin carga';
                                $quincenales_class = 'estado-sin-carga';
                            }
                            
                            // Convertir número de mes a nombre
                            $mes_num = str_pad($item['mes'], 2, '0', STR_PAD_LEFT);
                            $nombres_meses = [
                                '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
                                '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
                                '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
                                '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
                            ];
                            $nombre_mes = isset($nombres_meses[$mes_num]) ? $nombres_meses[$mes_num] : $item['mes'];
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo htmlspecialchars($item['anio']); ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($nombre_mes); ?></td>
                            <td class="text-center">
                                <span class="estado-badge <?php echo $mensuales_class; ?>">
                                    <?php echo htmlspecialchars($mensuales_estado); ?>
                                </span>
                                <br>
                                <small class="text-muted">(<?php echo number_format($mensuales_cant); ?> cargados)</small>
                            </td>
                            <td class="text-center">
                                <span class="estado-badge <?php echo $quincenales_class; ?>">
                                    <?php echo htmlspecialchars($quincenales_estado); ?>
                                </span>
                                <br>
                                <small class="text-muted">(<?php echo number_format($quincenales_cant); ?> cargados)</small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($resumen_mensual)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No hay datos disponibles</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Tabla Detallada de Recibos -->
        <div class="table-container">
            <table id="tablaRecibos" class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th><i class="fas fa-calendar"></i> Año</th>
                        <th><i class="fas fa-calendar-alt"></i> Mes</th>
                        <th><i class="fas fa-tag"></i> Tipo de Liquidación</th>
                        <th><i class="fas fa-chart-bar"></i> Cant. Cargados</th>
                        <th><i class="fas fa-info-circle"></i> Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($result as $row):
                            // Definir el estado basado en la cantidad cargada
                            $cant_cargados = $row['cant_cargados'];
                            $estado = '';
                            $estado_class = '';
                            
                            // Personalizar criterios según tipo de liquidación
                            $tipo_liquidacion = strtolower($row['tipo_liquidacion']);
                            if (strpos($tipo_liquidacion, 'mensual') !== false) {
                                // Umbral para mensuales: 120
                                if ($cant_cargados >= 120) {
                                    $estado = 'Completo';
                                    $estado_class = 'estado-completo';
                                } elseif ($cant_cargados > 0) {
                                    $estado = 'Incompleto';
                                    $estado_class = 'estado-pendiente';
                                } else {
                                    $estado = 'Sin carga';
                                    $estado_class = 'estado-sin-carga';
                                }
                            } elseif (strpos($tipo_liquidacion, 'quincenal') !== false) {
                                // Umbral para quincenales: 200
                                if ($cant_cargados >= 200) {
                                    $estado = 'Completo';
                                    $estado_class = 'estado-completo';
                                } elseif ($cant_cargados > 0) {
                                    $estado = 'Incompleto';
                                    $estado_class = 'estado-pendiente';
                                } else {
                                    $estado = 'Sin carga';
                                    $estado_class = 'estado-sin-carga';
                                }
                            } else {
                                // Para otros tipos, usar criterio genérico
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
                            }
                            
                            // Convertir número de mes a nombre del mes
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
                            echo "<td class='text-center'><span class='estado-badge " . $estado_class . "'>" . htmlspecialchars($estado) . "</span></td>";
                            echo "</tr>";
                         endforeach;
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="footer">
            <i class="fas fa-chart-line"></i> Total de registros: <?php echo count($result); ?> | 
            <i class="fas fa-calendar-check"></i> Períodos con datos: <?php echo count($resumen_mensual); ?> |
            <i class="fas fa-sync-alt"></i> Actualizado: <?php echo date('d/m/Y H:i:s'); ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inicializar DataTable para tabla detallada
            $('#tablaRecibos').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 10,
                responsive: true,
                order: [[0, 'desc'], [1, 'desc'], [2, 'asc']],
                columnDefs: [
                    { targets: [3], className: 'text-center' },
                    { targets: [4], className: 'text-center' }
                ]
            });
            
            // Inicializar DataTable para tabla resumen
            $('#tablaResumen').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 12,
                responsive: true,
                order: [[0, 'desc'], [1, 'desc']],
                columnDefs: [
                    { targets: [2, 3], className: 'text-center' }
                ]
            });
        });
    </script>
</body>
</html>