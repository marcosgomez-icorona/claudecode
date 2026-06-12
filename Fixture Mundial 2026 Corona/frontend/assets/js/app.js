function formatDateTime(value) {
  if (!value) return '-';
  return new Intl.DateTimeFormat('es-AR', {
    dateStyle: 'short',
    timeStyle: 'short'
  }).format(new Date(value));
}

function setMessage(element, type, message) {
  if (!element) return;
  element.className = `status-message alert alert-${type}`;
  element.textContent = message;
}

function clearMessage(element) {
  if (!element) return;
  element.className = 'status-message';
  element.textContent = '';
}

function renderEmpty(element, message) {
  element.innerHTML = `<div class="empty-state"><i class="bi bi-info-circle me-2"></i>${message}</div>`;
}

function populateAreas(select) {
  if (!select) return;
  select.innerHTML = '<option value="">Seleccionar area</option>' + window.CORONA_CONFIG.areas
    .map((area) => `<option value="${area}">${area}</option>`)
    .join('');
}

function startCountdown(targetDate, container) {
  if (!container) return;
  const target = new Date(targetDate || window.CORONA_CONFIG.mundialStartDate).getTime();

  function tick() {
    const distance = Math.max(0, target - Date.now());
    const days = Math.floor(distance / 86400000);
    const hours = Math.floor((distance % 86400000) / 3600000);
    const minutes = Math.floor((distance % 3600000) / 60000);
    const seconds = Math.floor((distance % 60000) / 1000);

    container.innerHTML = [
      ['Dias', days],
      ['Horas', hours],
      ['Min', minutes],
      ['Seg', seconds]
    ].map(([label, value]) => `<div class="countdown-box"><strong>${value}</strong><span>${label}</span></div>`).join('');
  }

  tick();
  setInterval(tick, 1000);
}

function isEmail(value) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}

const TEAM_COUNTRY_CODES = {
  Argentina: 'AR', Argelia: 'DZ', Alemania: 'DE', Australia: 'AU', Austria: 'AT',
  Belgica: 'BE', Brasil: 'BR', Canada: 'CA', Catar: 'QA', Colombia: 'CO',
  'Corea del Sur': 'KR', Croacia: 'HR', Ecuador: 'EC', Egipto: 'EG', Escocia: 'GB',
  Espana: 'ES', Francia: 'FR', Ghana: 'GH', Haiti: 'HT', Inglaterra: 'GB',
  Iran: 'IR', Iraq: 'IQ', Japon: 'JP', Jordania: 'JO', Marruecos: 'MA',
  Mexico: 'MX', Noruega: 'NO', 'Nueva Zelanda': 'NZ', Panama: 'PA', Paraguay: 'PY',
  'Paises Bajos': 'NL', Portugal: 'PT', 'RD Congo': 'CD', 'Republica Checa': 'CZ',
  Chequia: 'CZ', Senegal: 'SN', Sudafrica: 'ZA', Suecia: 'SE', Suiza: 'CH', Tunez: 'TN',
  Turquia: 'TR', Uruguay: 'UY', Uzbekistan: 'UZ', 'Costa de Marfil': 'CI',
  'Estados Unidos': 'US', 'Arabia Saudita': 'SA', 'Bosnia y Herzegovina': 'BA',
  'Cabo Verde': 'CV', Curazao: 'CW'
};

const TEAM_CODES = {
  Argentina: 'ARG', Argelia: 'ALG', Alemania: 'GER', Australia: 'AUS', Austria: 'AUT',
  Belgica: 'BEL', Brasil: 'BRA', Canada: 'CAN', Catar: 'QAT', Colombia: 'COL',
  'Corea del Sur': 'KOR', Croacia: 'CRO', Ecuador: 'ECU', Egipto: 'EGY', Escocia: 'SCO',
  Espana: 'ESP', Francia: 'FRA', Ghana: 'GHA', Haiti: 'HAI', Inglaterra: 'ENG',
  Iran: 'IRN', Iraq: 'IRQ', Japon: 'JPN', Jordania: 'JOR', Marruecos: 'MAR',
  Mexico: 'MEX', Noruega: 'NOR', 'Nueva Zelanda': 'NZL', Panama: 'PAN', Paraguay: 'PAR',
  'Paises Bajos': 'NED', Portugal: 'POR', 'RD Congo': 'COD', 'Republica Checa': 'CZE',
  Chequia: 'CZE', Senegal: 'SEN', Sudafrica: 'RSA', Suecia: 'SWE', Suiza: 'SUI', Tunez: 'TUN',
  Turquia: 'TUR', Uruguay: 'URU', Uzbekistan: 'UZB', 'Costa de Marfil': 'CIV',
  'Estados Unidos': 'USA', 'Arabia Saudita': 'KSA', 'Bosnia y Herzegovina': 'BIH',
  'Cabo Verde': 'CPV', Curazao: 'CUW'
};

function normalizeTeamName(name) {
  return String(name || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
}

function lookupTeamMap(map, teamName) {
  const direct = map[teamName];
  if (direct) return direct;
  const normalized = normalizeTeamName(teamName);
  const key = Object.keys(map).find((item) => normalizeTeamName(item) === normalized);
  return key ? map[key] : '';
}

function getTeamFlag(teamName) {
  const code = lookupTeamMap(TEAM_COUNTRY_CODES, teamName);
  if (!code) return '🏳️';
  return code.toUpperCase().replace(/./g, (letter) => String.fromCodePoint(127397 + letter.charCodeAt(0)));
}

function getTeamCountryCode(teamName) {
  return lookupTeamMap(TEAM_COUNTRY_CODES, teamName).toLowerCase();
}

function getTeamCode(teamName) {
  return lookupTeamMap(TEAM_CODES, teamName) || normalizeTeamName(teamName).split(/\s+/).map((part) => part[0]).join('').slice(0, 3).toUpperCase();
}

function formatTeamFlag(teamName, options = {}) {
  const countryCode = getTeamCountryCode(teamName);
  const sizeClass = options.size ? ` team-flag-${options.size}` : '';
  const label = `Bandera de ${teamName}`;
  if (!countryCode) {
    return `<span class="team-flag${sizeClass}" aria-label="${label}">🏳️</span>`;
  }
  return `<img class="team-flag team-flag-img${sizeClass}" src="https://flagcdn.com/w40/${countryCode}.png" srcset="https://flagcdn.com/w80/${countryCode}.png 2x" width="24" height="18" alt="${label}" loading="lazy">`;
}

function formatTeamName(teamName, options = {}) {
  const label = options.code ? getTeamCode(teamName) : teamName;
  const nameClass = options.code ? 'team-code' : 'team-name';
  return `<span class="team-with-flag">${formatTeamFlag(teamName, options)}<span class="${nameClass}">${label}</span></span>`;
}

function formatMatchTeams(teamA, teamB, options = {}) {
  return `<span class="match-teams">${formatTeamName(teamA, options)}<span class="match-vs">vs</span>${formatTeamName(teamB, options)}</span>`;
}

window.CoronaUi = {
  formatDateTime,
  setMessage,
  clearMessage,
  renderEmpty,
  populateAreas,
  startCountdown,
  isEmail,
  getTeamFlag,
  getTeamCountryCode,
  getTeamCode,
  formatTeamFlag,
  formatTeamName,
  formatMatchTeams
};
