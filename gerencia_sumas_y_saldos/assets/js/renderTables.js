// renderTables.js – render the accounts table
export function renderAccountsTable(accounts) {
  const tbody = document.getElementById('accountsTable').querySelector('tbody');
  // Clear existing rows
  tbody.innerHTML = '';
  accounts.forEach(acc => {
    const tr = document.createElement('tr');
    const tdAccount = document.createElement('td');
    tdAccount.textContent = acc.cuenta || acc.account || '';
    const tdBalance = document.createElement('td');
    tdBalance.textContent = acc.saldo != null ? Number(acc.saldo).toLocaleString('en-US', {style: 'currency', currency: 'USD'}) : '';
    const tdVar = document.createElement('td');
    tdVar.textContent = acc.variacion != null ? `${acc.variacion}%` : '';
    const tdState = document.createElement('td');
    tdState.textContent = acc.estado || '';
    tr.appendChild(tdAccount);
    tr.appendChild(tdBalance);
    tr.appendChild(tdVar);
    tr.appendChild(tdState);
    tbody.appendChild(tr);
  });
}
