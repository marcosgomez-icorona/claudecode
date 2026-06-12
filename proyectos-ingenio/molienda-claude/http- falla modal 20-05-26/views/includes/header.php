<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Molienda de Caña</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Fondo con degradado */
    .panel-container {
      min-height: 100vh;
      padding: 20px;
      background: linear-gradient(135deg, #dbe6f6 0%, #cfd9df 100%);
    }

    /* Cartas con efecto más moderno */
    .card {
      border-radius: 1rem;
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(6px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    }

    /* Títulos */
    h1, h5, .card-title {
      font-weight: 700;
      color: #334e68;
    }

    /* Inputs compactos */
    .form-control {
      border-radius: 0.6rem;
    }
    .form-control-sm {
      padding: 0.1rem 0.50rem;
      font-size: 0.9rem;
    }

    /* Botones personalizados */
    .btn-info {
      background: linear-gradient(135deg, #5bc0de, #2980b9);
      border: none;
    }
    .btn-info:hover {
      background: linear-gradient(135deg, #2980b9, #1c6ea4);
    }

    .btn-success {
      background: linear-gradient(135deg, #28a745, #218838);
      border: none;
    }
    .btn-success:hover {
      background: linear-gradient(135deg, #218838, #1c7430);
    }

    .btn-secondary {
      background: linear-gradient(135deg, #6c757d, #495057);
      border: none;
    }
    .btn-secondary:hover {
      background: linear-gradient(135deg, #616569ff, #343a40);
    }

    /* Tablas */
    .table thead {
      background: #e9f2fa;
    }
    .table-striped > tbody > tr:nth-of-type(odd) {
      background-color: rgba(240, 248, 255, 0.4);
    }

    /* ── Semáforos ───────────────────────────────────────── */
    .semaforo-card {
      transition: background-color 0.35s ease, border-left 0.35s ease;
    }
    .semaforo-dot {
      display: inline-block;
      width: 10px; height: 10px;
      border-radius: 50%;
      background-color: #adb5bd;
      vertical-align: middle;
      margin-left: 4px;
      box-shadow: 0 0 3px rgba(0,0,0,0.25);
      flex-shrink: 0;
    }

    /* ── Indicadores — tarjeta uniforme ──────────────────── */
    .ind-card {
      padding: 0.3rem 0.45rem;
      border-radius: 0.4rem;
      margin-bottom: 0.3rem;
      background: rgba(255,255,255,0.92);
      border-left: 4px solid #adb5bd;
    }
    .ind-title {
      font-size: 14px;
      font-weight: 600;
      color: #334e68;
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 0.18rem;
      line-height: 1.2;
    }
    .ind-row {
      display: flex;
      gap: 0.25rem;
    }
    .ind-field { flex: 1; }
    .ind-caption {
      font-size: 0.6rem;
      color: #6c757d;
      display: block;
      line-height: 1.1;
      margin-bottom: 0.05rem;
    }
    .ind-input {
      font-size: 0.78rem !important;
      font-weight: bold !important;
      padding: 0.05rem 0.2rem !important;
      height: auto !important;
      text-align: center;
    }
    .ind-section {
      flex: 1;
      min-width: 155px;
    }
    .ind-section-title {
      font-size: 18px;
      font-weight: 700;
      color: #334e68;
      border-bottom: 2px solid #dee2e6;
      padding-bottom: 0.15rem;
      margin-bottom: 0.35rem;
    }

    /* ── Dashboard principal ─────────────────────────────── */
    .dash-header {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.3rem 0.6rem;
      background: rgba(255,255,255,0.9);
      border-bottom: 1px solid #dee2e6;
      flex-wrap: wrap;
    }
    .dash-btn-bar {
      display: flex;
      flex-wrap: wrap;
      gap: 0.3rem;
      padding: 0.3rem 0.5rem;
      background: rgba(219,230,246,0.5);
      border-bottom: 1px solid #dee2e6;
    }
    .dash-data-table th, .dash-data-table td {
      padding: 0.22rem 0.5rem;
      font-size: 14px;
      font-weight: 700;
      vertical-align: middle;
    }
    .dash-data-table thead th {
      background: #e9f2fa;
      font-weight: 700;
    }
  </style>
</head>