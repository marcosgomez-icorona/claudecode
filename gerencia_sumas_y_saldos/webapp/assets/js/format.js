/* FORMAT — Formateo de moneda, fechas, badges, filtros, export, toast */

/* ════════════════════════════════════════════════
   FORMATEO
════════════════════════════════════════════════ */
function fmtMoney(n) {
  if (n == null) return '—';
  return new Intl.NumberFormat('es-AR', { style:'currency', currency:'ARS', maximumFractionDigits:0 }).format(n);
}

function fmtNum(n) {
  if (n == null) return '—';
  return new Intl.NumberFormat('es-AR', { maximumFractionDigits:0 }).format(n);
}

function fmtPct(n) {
  if (n == null) return '—';
  return (n >= 0 ? '+' : '') + n.toFixed(1) + '%';
}

function fmtDate(s) {
  if (!s) return '—';
  const d = new Date(s + 'T00:00:00');
  return d.toLocaleDateString('es-AR', { day:'2-digit', month:'2-digit', year:'numeric' });
}

function estadoBadge(e) {
  const m = { ok:'ok', warn:'warn', crit:'crit', info:'info' };
  const l = { ok:'Normal', warn:'Atención', crit:'Crítico', info:'Info' };
  const k = m[e] || 'neutral';
  return `<span class="badge-status badge-${k}">${l[e] || e}</span>`;
}

function varPill(v) {
  if (v == null) return '<span class="var-pill var-neutral">—</span>';
  const cls = v > 0 ? 'var-up' : v < 0 ? 'var-down' : 'var-neutral';
  return `<span class="var-pill ${cls}">${fmtPct(v)}</span>`;
}

function diasBadge(d) {
  if (d > 0) return `<span class="badge-status badge-crit">+${d} días</span>`;
  if (d >= -7) return `<span class="badge-status badge-warn">${Math.abs(d)} días</span>`;
  return `<span class="badge-status badge-ok">${Math.abs(d)} días</span>`;
}

/* ════════════════════════════════════════════════
   FILTROS / BÚSQUEDA
════════════════════════════════════════════════ */
function filterTable(tbodyId, query) {
  const q = query.toLowerCase();
  document.querySelectorAll('#' + tbodyId + ' tr').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
}

function filterTopTable(query) { filterTable('top-accounts-body', query); }

/* ════════════════════════════════════════════════
   EXPORT CSV
════════════════════════════════════════════════ */
function exportSectionCSV(tbodyId, filename) {
  const rows = [];
  const thead = document.querySelector('#' + tbodyId).closest('table')?.querySelector('thead');
  if (thead) rows.push([...thead.querySelectorAll('th')].map(th => th.textContent.trim()).join(','));
  document.querySelectorAll('#' + tbodyId + ' tr').forEach(row => {
    if (row.style.display === 'none') return;
    const cells = [...row.querySelectorAll('td')].map(td => '"' + td.textContent.trim().replace(/"/g,'""') + '"');
    if (cells.length) rows.push(cells.join(','));
  });
  const blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8;' });
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = filename + '_' + new Date().toISOString().slice(0,10) + '.csv';
  a.click();
}

function exportCSV() {
  exportSectionCSV('ss-body', 'sumas_saldos');
}

/* ════════════════════════════════════════════════
   TOAST
════════════════════════════════════════════════ */
function showToast(msg, type) {
  const toast = document.createElement('div');
  const colors = { ok:'var(--corona-green-dark)', warn:'var(--accent-amber)', err:'var(--accent-red)' };
  toast.style.cssText = `
    position:fixed; bottom:24px; right:24px; z-index:9999;
    background:var(--surface); border:1px solid var(--border);
    border-left:3px solid ${colors[type]||colors.ok};
    border-radius:10px; padding:12px 18px;
    font-size:13px; color:var(--text-primary);
    box-shadow:0 4px 16px rgba(0,0,0,0.12);
    animation:fadeIn 0.2s ease;
    max-width:320px;
  `;
  toast.textContent = msg;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3500);
}
