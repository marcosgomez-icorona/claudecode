<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Molienda de Caña</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      corePlugins: {
        preflight: false,
      },
      theme: {
        extend: {
          colors: {
            brand: {
              blue: '#3f73aa',
              green: '#2f8a70',
              teal: '#4a9a8b',
              amber: '#c98427',
              muted: '#75839a',
              text: '#253246'
            }
          }
        }
      }
    }
  </script>

  <style type="text/tailwindcss">
    @layer components {
      /* Estilos Globales de Layout (Reemplazando Bootstrap Custom) */
      .panel-container {
        @apply min-h-screen p-5 bg-gradient-to-br from-[#f8fafc] to-[#e2e8f0];
      }
      .full-width-container {
        @apply min-h-screen bg-[#eef3f8];
      }
      .molienda-shell {
        @apply min-h-screen bg-gradient-to-br from-slate-50 via-[#eef3f8] to-slate-200 p-2 shadow-none rounded-none;
      }
      body {
        @apply bg-[#eef3f8] text-brand-text font-sans antialiased;
      }

      /* Cartas modernas (Glassmorphism) */
      .card {
        @apply rounded-2xl bg-white/95 backdrop-blur-md shadow-lg border-0 transition-all duration-300;
      }
      .card:hover {
        @apply -translate-y-[2px] shadow-xl;
      }
      
      h1, h5, .card-title {
        @apply font-bold text-slate-800;
      }

      /* Inputs */
      .form-control {
        @apply rounded-xl border-slate-300 shadow-sm focus:border-brand-blue focus:ring focus:ring-brand-blue/20 transition-all;
      }
      .form-control-sm {
        @apply px-2 py-1 text-sm;
      }

      /* Botones */
      .btn-info {
        @apply bg-gradient-to-br from-[#5bc0de] to-[#2980b9] border-none text-white shadow-md hover:shadow-lg hover:from-[#2980b9] hover:to-[#1c6ea4] transition-all rounded-xl font-semibold;
      }
      .btn-success {
        @apply bg-gradient-to-br from-[#28a745] to-[#218838] border-none text-white shadow-md hover:shadow-lg hover:from-[#218838] hover:to-[#1c7430] transition-all rounded-xl font-semibold;
      }
      .btn-secondary {
        @apply bg-gradient-to-br from-slate-500 to-slate-700 border-none text-white shadow-md hover:shadow-lg hover:from-slate-600 hover:to-slate-800 transition-all rounded-xl font-semibold;
      }
      .btn-danger {
        @apply bg-gradient-to-br from-rose-500 to-red-700 border-none text-white shadow-md hover:shadow-lg hover:from-rose-600 hover:to-red-800 transition-all rounded-xl font-semibold;
      }

      /* Tablas Bootstrap */
      .table thead th {
        @apply bg-slate-100 text-slate-600 font-semibold uppercase tracking-wider text-xs border-b border-slate-200;
      }
      .table-striped > tbody > tr:nth-of-type(odd) {
        @apply bg-slate-50/50;
      }
      .table th, .table td {
        @apply px-3 py-2 text-sm align-middle;
      }

      /* Semáforos */
      .semaforo-card {
        @apply transition-all duration-300;
      }
      .semaforo-dot {
        @apply inline-block w-2.5 h-2.5 rounded-full bg-slate-400 align-middle ml-1 shadow-sm shrink-0;
      }

      /* Indicadores (tarjeta uniforme) */
      .indicator-grid {
        @apply grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2 items-start;
      }
      .ind-card {
        @apply p-2 rounded-2xl mb-2 bg-white/90 backdrop-blur-md border border-slate-200 border-l-4 border-l-slate-400 shadow-sm transition-all duration-300;
      }
      .ind-card:hover {
        @apply shadow-md -translate-y-[1px];
      }
      .ind-title {
        @apply text-sm font-bold text-slate-700 flex items-center justify-between gap-2 mb-1 leading-tight;
      }
      .ind-row {
        @apply grid grid-cols-2 gap-1.5;
      }
      .ind-field { @apply flex-1; }
      .ind-caption {
        @apply text-[10px] text-slate-500 block leading-tight mb-[2px] uppercase font-bold tracking-wide;
      }
      .ind-input {
        @apply text-xs px-2 py-1 h-auto text-center rounded-xl bg-slate-50 border-slate-200 font-bold text-slate-700 tabular-nums;
      }
      .ind-section {
        @apply min-w-[155px] rounded-2xl border border-slate-200 bg-white/55 p-2 shadow-[inset_0_1px_0_rgba(255,255,255,0.85)];
      }
      .ind-section-title {
        @apply text-sm font-bold text-slate-700 border-b border-slate-200 pb-1 mb-2 uppercase tracking-wide;
      }

      /* Dashboard principal */
      .dash-header {
        @apply flex items-center gap-2 p-1.5 bg-white/90 border-b border-slate-200 flex-wrap rounded-t-2xl;
      }
      .dash-btn-bar {
        @apply flex flex-wrap gap-1 p-1.5 bg-blue-50/50 border-b border-slate-200;
      }

      /* ── ESTILOS ESPECÍFICOS DE MOLIENDA HORA (Migrados) ── */
      .realtime-panel {
        @apply h-full p-4 rounded-[22px] border border-slate-200 shadow-[inset_0_1px_0_rgba(255,255,255,0.95),_0_10px_28px_rgba(50,75,100,0.13)] bg-gradient-to-br from-white/90 to-slate-50/80;
      }
      .realtime-header {
        @apply flex items-center gap-3 mb-4;
      }
      .realtime-header-icon {
        @apply flex h-10 w-10 items-center justify-center rounded-2xl bg-brand-blue/10 text-brand-blue font-bold shadow-inner;
      }
      .realtime-title {
        @apply m-0 text-lg font-bold text-slate-800 leading-tight;
      }
      .realtime-subtitle {
        @apply text-xs font-semibold uppercase tracking-wide text-slate-400;
      }
      .metric-cards {
        @apply grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-2 mb-4;
      }
      .metric {
        @apply min-h-[80px] p-2.5 border border-slate-200 rounded-2xl bg-white/60 shadow-[inset_0_1px_0_rgba(255,255,255,0.9)] flex flex-col justify-between;
      }
      .metric.blue { @apply border-brand-blue/30; }
      .metric.green { @apply border-brand-green/30; }
      .metric.teal { @apply border-brand-teal/30; }
      .metric.amber { @apply border-brand-amber/30; }
      .metric.muted { @apply border-brand-muted/30; }
      
      .metric-head {
        @apply flex items-center gap-1.5 text-slate-500 text-[11px] font-bold tracking-wide uppercase whitespace-nowrap;
      }
      .metric-icon {
        @apply inline-flex h-5 w-5 items-center justify-center rounded-full bg-slate-100 text-[10px] text-slate-500;
      }
      .metric-value {
        @apply mt-1 text-slate-700 text-xl font-bold whitespace-nowrap tabular-nums;
      }
      .metric.blue .metric-value { @apply text-brand-blue; }
      .metric.green .metric-value { @apply text-brand-green; }
      .metric.teal .metric-value { @apply text-brand-teal; }
      .metric.amber .metric-value { @apply text-brand-amber; }
      .metric.muted .metric-value { @apply text-brand-muted; }
      
      .metric-unit { @apply ml-0.5 text-slate-500 text-xs font-medium; }
      .metric-note { @apply mt-2 text-slate-400 text-[10px] whitespace-nowrap; }

      .realtime-table-wrap {
        @apply w-full overflow-x-auto pb-2;
      }
      .realtime-table {
        @apply w-full min-w-[1040px] border-collapse text-sm text-slate-700;
      }
      .realtime-table-wide {
        @apply min-w-[1680px];
      }
      .realtime-table th {
        @apply p-2.5 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wide text-right text-xs;
      }
      .realtime-table td {
        @apply p-2.5 border-b border-slate-100 text-right tabular-nums whitespace-nowrap;
      }
      .realtime-table th:first-child, .realtime-table td:first-child { @apply text-left; }
      .realtime-table .td-text, .realtime-table .th-text { @apply text-left; }
      .realtime-table .sticky-head th {
        @apply sticky top-0 z-[1] bg-slate-100;
      }
      .realtime-table .hour { @apply text-slate-600 font-mono italic; }
      .realtime-table tfoot td {
        @apply p-2.5 border-t border-slate-200 bg-slate-50 text-slate-700 font-bold;
      }
      
      .th-blue { @apply text-brand-blue; }
      .th-green { @apply text-brand-green; }
      .th-teal { @apply text-brand-teal; }
      .th-amber { @apply text-brand-amber; }
      .th-muted { @apply text-brand-muted; }
      .dash, .td-dash { @apply text-slate-400; }
      .status {
        @apply rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700;
      }
      .empty {
        @apply rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700;
      }
      .scroll-hint {
        @apply mt-2 h-1 rounded-full bg-gradient-to-r from-brand-blue/25 via-brand-teal/20 to-transparent;
      }
      .dash-data-table {
        @apply overflow-hidden rounded-xl;
      }
      .modal-content {
        @apply rounded-2xl border-0 shadow-2xl;
      }
      .modal-header, .modal-footer {
        @apply border-slate-200;
      }
    }
  </style>
</head>
