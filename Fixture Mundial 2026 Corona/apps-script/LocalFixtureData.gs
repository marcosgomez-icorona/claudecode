function getLocalOfficialFixtureData() {
  const teams = [
    team('MEX', 'Mexico', 'Mexico', 'A'),
    team('RSA', 'South Africa', 'Sudafrica', 'A'),
    team('KOR', 'Korea Republic', 'Corea del Sur', 'A'),
    team('CZE', 'Czech Republic', 'Republica Checa', 'A'),
    team('CAN', 'Canada', 'Canada', 'B'),
    team('BIH', 'Bosnia & Herzegovina', 'Bosnia y Herzegovina', 'B'),
    team('QAT', 'Qatar', 'Catar', 'B'),
    team('SUI', 'Switzerland', 'Suiza', 'B'),
    team('BRA', 'Brazil', 'Brasil', 'C'),
    team('MAR', 'Morocco', 'Marruecos', 'C'),
    team('HAI', 'Haiti', 'Haiti', 'C'),
    team('SCO', 'Scotland', 'Escocia', 'C'),
    team('USA', 'USA', 'Estados Unidos', 'D'),
    team('PAR', 'Paraguay', 'Paraguay', 'D'),
    team('AUS', 'Australia', 'Australia', 'D'),
    team('TUR', 'Turkey', 'Turquia', 'D'),
    team('GER', 'Germany', 'Alemania', 'E'),
    team('CUW', 'Curacao', 'Curazao', 'E'),
    team('CIV', 'Ivory Coast', 'Costa de Marfil', 'E'),
    team('ECU', 'Ecuador', 'Ecuador', 'E'),
    team('NED', 'Netherlands', 'Paises Bajos', 'F'),
    team('JPN', 'Japan', 'Japon', 'F'),
    team('SWE', 'Sweden', 'Suecia', 'F'),
    team('TUN', 'Tunisia', 'Tunez', 'F'),
    team('BEL', 'Belgium', 'Belgica', 'G'),
    team('EGY', 'Egypt', 'Egipto', 'G'),
    team('IRN', 'Iran', 'Iran', 'G'),
    team('NZL', 'New Zealand', 'Nueva Zelanda', 'G'),
    team('ESP', 'Spain', 'Espana', 'H'),
    team('CPV', 'Cape Verde', 'Cabo Verde', 'H'),
    team('KSA', 'Saudi Arabia', 'Arabia Saudita', 'H'),
    team('URU', 'Uruguay', 'Uruguay', 'H'),
    team('FRA', 'France', 'Francia', 'I'),
    team('SEN', 'Senegal', 'Senegal', 'I'),
    team('IRQ', 'Iraq', 'Iraq', 'I'),
    team('NOR', 'Norway', 'Noruega', 'I'),
    team('ARG', 'Argentina', 'Argentina', 'J'),
    team('ALG', 'Algeria', 'Argelia', 'J'),
    team('AUT', 'Austria', 'Austria', 'J'),
    team('JOR', 'Jordan', 'Jordania', 'J'),
    team('POR', 'Portugal', 'Portugal', 'K'),
    team('COD', 'DR Congo', 'RD Congo', 'K'),
    team('UZB', 'Uzbekistan', 'Uzbekistan', 'K'),
    team('COL', 'Colombia', 'Colombia', 'K'),
    team('ENG', 'England', 'Inglaterra', 'L'),
    team('CRO', 'Croatia', 'Croacia', 'L'),
    team('GHA', 'Ghana', 'Ghana', 'L'),
    team('PAN', 'Panama', 'Panama', 'L')
  ];

  const matches = [
    match('wc2026_001', 'A', 'MEX', 'RSA', '2026-06-11', '20:00', 'Mexico City'),
    match('wc2026_002', 'A', 'KOR', 'CZE', '2026-06-12', '03:00', 'Zapopan'),
    match('wc2026_003', 'B', 'CAN', 'BIH', '2026-06-12', '20:00', 'Toronto'),
    match('wc2026_004', 'D', 'USA', 'PAR', '2026-06-13', '02:00', 'Los Angeles'),
    match('wc2026_005', 'B', 'QAT', 'SUI', '2026-06-13', '20:00', 'Santa Clara'),
    match('wc2026_006', 'C', 'BRA', 'MAR', '2026-06-13', '23:00', 'New Jersey'),
    match('wc2026_007', 'C', 'HAI', 'SCO', '2026-06-14', '02:00', 'Foxborough'),
    match('wc2026_008', 'D', 'AUS', 'TUR', '2026-06-14', '05:00', 'Vancouver'),
    match('wc2026_009', 'E', 'GER', 'CUW', '2026-06-14', '18:00', 'Houston'),
    match('wc2026_010', 'F', 'NED', 'JPN', '2026-06-14', '21:00', 'Arlington'),
    match('wc2026_011', 'E', 'CIV', 'ECU', '2026-06-15', '00:00', 'Philadelphia'),
    match('wc2026_012', 'F', 'SWE', 'TUN', '2026-06-15', '03:00', 'Guadalupe'),
    match('wc2026_013', 'H', 'ESP', 'CPV', '2026-06-15', '17:00', 'Atlanta'),
    match('wc2026_014', 'G', 'BEL', 'EGY', '2026-06-15', '20:00', 'Seattle'),
    match('wc2026_015', 'H', 'KSA', 'URU', '2026-06-15', '23:00', 'Miami'),
    match('wc2026_016', 'G', 'IRN', 'NZL', '2026-06-16', '02:00', 'Los Angeles'),
    match('wc2026_017', 'I', 'FRA', 'SEN', '2026-06-16', '20:00', 'New Jersey'),
    match('wc2026_018', 'I', 'IRQ', 'NOR', '2026-06-16', '23:00', 'Foxborough'),
    match('wc2026_019', 'J', 'ARG', 'ALG', '2026-06-17', '02:00', 'Kansas City'),
    match('wc2026_020', 'J', 'AUT', 'JOR', '2026-06-17', '05:00', 'Santa Clara'),
    match('wc2026_021', 'K', 'POR', 'COD', '2026-06-17', '18:00', 'Houston'),
    match('wc2026_022', 'L', 'ENG', 'CRO', '2026-06-17', '21:00', 'Arlington'),
    match('wc2026_023', 'L', 'GHA', 'PAN', '2026-06-18', '00:00', 'Toronto'),
    match('wc2026_024', 'K', 'UZB', 'COL', '2026-06-18', '03:00', 'Mexico City'),
    match('wc2026_025', 'A', 'CZE', 'RSA', '2026-06-18', '17:00', 'Atlanta'),
    match('wc2026_026', 'B', 'SUI', 'BIH', '2026-06-18', '20:00', 'Los Angeles'),
    match('wc2026_027', 'B', 'CAN', 'QAT', '2026-06-18', '23:00', 'Vancouver'),
    match('wc2026_028', 'A', 'MEX', 'KOR', '2026-06-19', '02:00', 'Zapopan'),
    match('wc2026_029', 'D', 'USA', 'AUS', '2026-06-19', '20:00', 'Seattle'),
    match('wc2026_030', 'C', 'SCO', 'MAR', '2026-06-19', '23:00', 'Foxborough'),
    match('wc2026_031', 'C', 'BRA', 'HAI', '2026-06-20', '01:30', 'Philadelphia'),
    match('wc2026_032', 'D', 'TUR', 'PAR', '2026-06-20', '04:00', 'Santa Clara'),
    match('wc2026_033', 'F', 'NED', 'SWE', '2026-06-20', '18:00', 'Houston'),
    match('wc2026_034', 'E', 'GER', 'CIV', '2026-06-20', '21:00', 'Toronto'),
    match('wc2026_035', 'E', 'ECU', 'CUW', '2026-06-21', '01:00', 'Kansas City'),
    match('wc2026_036', 'F', 'TUN', 'JPN', '2026-06-21', '05:00', 'Guadalupe'),
    match('wc2026_037', 'H', 'ESP', 'KSA', '2026-06-21', '17:00', 'Atlanta'),
    match('wc2026_038', 'G', 'BEL', 'IRN', '2026-06-21', '20:00', 'Los Angeles'),
    match('wc2026_039', 'H', 'URU', 'CPV', '2026-06-21', '23:00', 'Miami'),
    match('wc2026_040', 'G', 'NZL', 'EGY', '2026-06-22', '02:00', 'Vancouver'),
    match('wc2026_041', 'J', 'ARG', 'AUT', '2026-06-22', '18:00', 'Arlington'),
    match('wc2026_042', 'I', 'FRA', 'IRQ', '2026-06-22', '22:00', 'Philadelphia'),
    match('wc2026_043', 'I', 'NOR', 'SEN', '2026-06-23', '01:00', 'Toronto'),
    match('wc2026_044', 'J', 'JOR', 'ALG', '2026-06-23', '04:00', 'Santa Clara'),
    match('wc2026_045', 'K', 'POR', 'UZB', '2026-06-23', '18:00', 'Houston'),
    match('wc2026_046', 'L', 'ENG', 'GHA', '2026-06-23', '21:00', 'Foxborough'),
    match('wc2026_047', 'L', 'PAN', 'CRO', '2026-06-24', '00:00', 'Foxborough'),
    match('wc2026_048', 'K', 'COL', 'COD', '2026-06-24', '03:00', 'Zapopan'),
    match('wc2026_049', 'B', 'SUI', 'CAN', '2026-06-24', '20:00', 'Vancouver'),
    match('wc2026_050', 'B', 'BIH', 'QAT', '2026-06-24', '20:00', 'Seattle'),
    match('wc2026_051', 'C', 'MAR', 'HAI', '2026-06-24', '23:00', 'Atlanta'),
    match('wc2026_052', 'C', 'SCO', 'BRA', '2026-06-24', '23:00', 'Miami'),
    match('wc2026_053', 'A', 'RSA', 'KOR', '2026-06-25', '02:00', 'Guadalupe'),
    match('wc2026_054', 'A', 'CZE', 'MEX', '2026-06-25', '02:00', 'Mexico City'),
    match('wc2026_055', 'E', 'CUW', 'CIV', '2026-06-25', '21:00', 'Philadelphia'),
    match('wc2026_056', 'E', 'ECU', 'GER', '2026-06-25', '21:00', 'New Jersey'),
    match('wc2026_057', 'F', 'TUN', 'NED', '2026-06-26', '00:00', 'Kansas City'),
    match('wc2026_058', 'F', 'JPN', 'SWE', '2026-06-26', '00:00', 'Arlington'),
    match('wc2026_059', 'D', 'TUR', 'USA', '2026-06-26', '03:00', 'Los Angeles'),
    match('wc2026_060', 'D', 'PAR', 'AUS', '2026-06-26', '03:00', 'Santa Clara'),
    match('wc2026_061', 'I', 'NOR', 'FRA', '2026-06-26', '20:00', 'Foxborough'),
    match('wc2026_062', 'I', 'SEN', 'IRQ', '2026-06-26', '20:00', 'Toronto'),
    match('wc2026_063', 'H', 'CPV', 'KSA', '2026-06-27', '01:00', 'Houston'),
    match('wc2026_064', 'H', 'URU', 'ESP', '2026-06-27', '01:00', 'Zapopan'),
    match('wc2026_065', 'G', 'NZL', 'BEL', '2026-06-27', '04:00', 'Vancouver'),
    match('wc2026_066', 'G', 'EGY', 'IRN', '2026-06-27', '04:00', 'Seattle'),
    match('wc2026_067', 'L', 'PAN', 'ENG', '2026-06-27', '22:00', 'New Jersey'),
    match('wc2026_068', 'L', 'CRO', 'GHA', '2026-06-27', '22:00', 'Philadelphia'),
    match('wc2026_069', 'K', 'COL', 'POR', '2026-06-28', '00:30', 'Miami'),
    match('wc2026_070', 'K', 'COD', 'UZB', '2026-06-28', '00:30', 'Atlanta'),
    match('wc2026_071', 'J', 'ALG', 'AUT', '2026-06-28', '03:00', 'Kansas City'),
    match('wc2026_072', 'J', 'JOR', 'ARG', '2026-06-28', '03:00', 'Arlington'),
    knockout('wc2026_073', 'Dieciseisavos de final', '2A', '2B', '2026-06-28', '20:00', 'Los Angeles'),
    knockout('wc2026_074', 'Dieciseisavos de final', '1E', '3A/B/C/D/F', '2026-06-29', '21:30', 'Foxborough'),
    knockout('wc2026_075', 'Dieciseisavos de final', '1F', '2C', '2026-06-30', '02:00', 'Guadalupe'),
    knockout('wc2026_076', 'Dieciseisavos de final', '1C', '2F', '2026-06-29', '18:00', 'Houston'),
    knockout('wc2026_077', 'Dieciseisavos de final', '1I', '3C/D/F/G/H', '2026-06-30', '22:00', 'New Jersey'),
    knockout('wc2026_078', 'Dieciseisavos de final', '2E', '2I', '2026-06-30', '18:00', 'Arlington'),
    knockout('wc2026_079', 'Dieciseisavos de final', '1A', '3C/E/F/H/I', '2026-07-01', '02:00', 'Mexico City'),
    knockout('wc2026_080', 'Dieciseisavos de final', '1L', '3E/H/I/J/K', '2026-07-01', '17:00', 'Atlanta'),
    knockout('wc2026_081', 'Dieciseisavos de final', '1D', '3B/E/F/I/J', '2026-07-02', '01:00', 'Santa Clara'),
    knockout('wc2026_082', 'Dieciseisavos de final', '1G', '3A/E/H/I/J', '2026-07-01', '21:00', 'Seattle'),
    knockout('wc2026_083', 'Dieciseisavos de final', '2K', '2L', '2026-07-03', '00:00', 'Toronto'),
    knockout('wc2026_084', 'Dieciseisavos de final', '1H', '2J', '2026-07-02', '20:00', 'Los Angeles'),
    knockout('wc2026_085', 'Dieciseisavos de final', '1B', '3E/F/G/I/J', '2026-07-03', '04:00', 'Vancouver'),
    knockout('wc2026_086', 'Dieciseisavos de final', '1J', '2H', '2026-07-03', '23:00', 'Miami'),
    knockout('wc2026_087', 'Dieciseisavos de final', '1K', '3D/E/I/J/L', '2026-07-04', '02:30', 'Kansas City'),
    knockout('wc2026_088', 'Dieciseisavos de final', '2D', '2G', '2026-07-03', '19:00', 'Arlington'),
    knockout('wc2026_089', 'Octavos de final', 'Ganador M74', 'Ganador M77', '2026-07-04', '22:00', 'Philadelphia'),
    knockout('wc2026_090', 'Octavos de final', 'Ganador M73', 'Ganador M75', '2026-07-04', '18:00', 'Houston'),
    knockout('wc2026_091', 'Octavos de final', 'Ganador M76', 'Ganador M78', '2026-07-05', '21:00', 'New Jersey'),
    knockout('wc2026_092', 'Octavos de final', 'Ganador M79', 'Ganador M80', '2026-07-06', '01:00', 'Mexico City'),
    knockout('wc2026_093', 'Octavos de final', 'Ganador M83', 'Ganador M84', '2026-07-06', '20:00', 'Arlington'),
    knockout('wc2026_094', 'Octavos de final', 'Ganador M81', 'Ganador M82', '2026-07-07', '01:00', 'Seattle'),
    knockout('wc2026_095', 'Octavos de final', 'Ganador M86', 'Ganador M88', '2026-07-07', '17:00', 'Atlanta'),
    knockout('wc2026_096', 'Octavos de final', 'Ganador M85', 'Ganador M87', '2026-07-07', '21:00', 'Vancouver'),
    knockout('wc2026_097', 'Cuartos de final', 'Ganador M89', 'Ganador M90', '2026-07-09', '21:00', 'Foxborough'),
    knockout('wc2026_098', 'Cuartos de final', 'Ganador M93', 'Ganador M94', '2026-07-10', '20:00', 'Los Angeles'),
    knockout('wc2026_099', 'Cuartos de final', 'Ganador M91', 'Ganador M92', '2026-07-11', '22:00', 'Miami'),
    knockout('wc2026_100', 'Cuartos de final', 'Ganador M95', 'Ganador M96', '2026-07-12', '02:00', 'Kansas City'),
    knockout('wc2026_101', 'Semifinal', 'Ganador M97', 'Ganador M98', '2026-07-14', '20:00', 'Arlington'),
    knockout('wc2026_102', 'Semifinal', 'Ganador M99', 'Ganador M100', '2026-07-15', '20:00', 'Atlanta'),
    knockout('wc2026_103', 'Tercer puesto', 'Perdedor M101', 'Perdedor M102', '2026-07-18', '22:00', 'Miami'),
    knockout('wc2026_104', 'Final', 'Ganador M101', 'Ganador M102', '2026-07-19', '20:00', 'New Jersey')
  ];

  return {
    provider: 'api-propia-corona-seed',
    source: 'FIFA schedule page verified with public schedule references',
    language: 'es',
    updated_at: nowIso(),
    teams: teams,
    matches: matches
  };
}

function team(code, name, nameEs, group) {
  return { code: code, name: name, name_es: nameEs, group: group };
}

function match(id, group, codeA, codeB, date, ukTime, venue) {
  const teams = getLocalTeamMap();
  return {
    match_id: id,
    stage: 'Group Stage',
    stage_es: 'Fase de grupos',
    group: group,
    team_a: teams[codeA],
    team_b: teams[codeB],
    start_time: ukDateTimeToUtcIso(date, ukTime),
    prediction_deadline: predictionDeadlineIso(date, ukTime),
    status: 'scheduled',
    venue: venue,
    score_a: null,
    score_b: null
  };
}

function knockout(id, stageEs, teamA, teamB, date, ukTime, venue) {
  return {
    match_id: id,
    stage: stageEs,
    stage_es: stageEs,
    group: '',
    team_a: { code: '', name: teamA, name_es: teamA },
    team_b: { code: '', name: teamB, name_es: teamB },
    start_time: ukDateTimeToUtcIso(date, ukTime),
    prediction_deadline: predictionDeadlineIso(date, ukTime),
    status: 'scheduled',
    venue: venue,
    score_a: null,
    score_b: null
  };
}

function getLocalTeamMap() {
  const map = {};
  const data = [
    ['MEX', 'Mexico', 'Mexico'], ['RSA', 'South Africa', 'Sudafrica'], ['KOR', 'Korea Republic', 'Corea del Sur'], ['CZE', 'Czech Republic', 'Republica Checa'],
    ['CAN', 'Canada', 'Canada'], ['BIH', 'Bosnia & Herzegovina', 'Bosnia y Herzegovina'], ['QAT', 'Qatar', 'Catar'], ['SUI', 'Switzerland', 'Suiza'],
    ['BRA', 'Brazil', 'Brasil'], ['MAR', 'Morocco', 'Marruecos'], ['HAI', 'Haiti', 'Haiti'], ['SCO', 'Scotland', 'Escocia'],
    ['USA', 'USA', 'Estados Unidos'], ['PAR', 'Paraguay', 'Paraguay'], ['AUS', 'Australia', 'Australia'], ['TUR', 'Turkey', 'Turquia'],
    ['GER', 'Germany', 'Alemania'], ['CUW', 'Curacao', 'Curazao'], ['CIV', 'Ivory Coast', 'Costa de Marfil'], ['ECU', 'Ecuador', 'Ecuador'],
    ['NED', 'Netherlands', 'Paises Bajos'], ['JPN', 'Japan', 'Japon'], ['SWE', 'Sweden', 'Suecia'], ['TUN', 'Tunisia', 'Tunez'],
    ['BEL', 'Belgium', 'Belgica'], ['EGY', 'Egypt', 'Egipto'], ['IRN', 'Iran', 'Iran'], ['NZL', 'New Zealand', 'Nueva Zelanda'],
    ['ESP', 'Spain', 'Espana'], ['CPV', 'Cape Verde', 'Cabo Verde'], ['KSA', 'Saudi Arabia', 'Arabia Saudita'], ['URU', 'Uruguay', 'Uruguay'],
    ['FRA', 'France', 'Francia'], ['SEN', 'Senegal', 'Senegal'], ['IRQ', 'Iraq', 'Iraq'], ['NOR', 'Norway', 'Noruega'],
    ['ARG', 'Argentina', 'Argentina'], ['ALG', 'Algeria', 'Argelia'], ['AUT', 'Austria', 'Austria'], ['JOR', 'Jordan', 'Jordania'],
    ['POR', 'Portugal', 'Portugal'], ['COD', 'DR Congo', 'RD Congo'], ['UZB', 'Uzbekistan', 'Uzbekistan'], ['COL', 'Colombia', 'Colombia'],
    ['ENG', 'England', 'Inglaterra'], ['CRO', 'Croatia', 'Croacia'], ['GHA', 'Ghana', 'Ghana'], ['PAN', 'Panama', 'Panama']
  ];
  data.forEach(function (row) {
    map[row[0]] = { code: row[0], name: row[1], name_es: row[2] };
  });
  return map;
}

function ukDateTimeToUtcIso(date, time) {
  return new Date(date + 'T' + time + ':00+01:00').toISOString();
}

function predictionDeadlineIso(date, time) {
  const kickoff = new Date(date + 'T' + time + ':00+01:00');
  return new Date(kickoff.getTime() - 60 * 60 * 1000).toISOString();
}
