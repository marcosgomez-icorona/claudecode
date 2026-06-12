# Subida a Google Apps Script

Copiar/actualizar estos archivos en el proyecto Apps Script:

1. `Code.gs`
2. `SheetsService.gs`
3. `AdminService.gs`
4. `ParticipantsService.gs`
5. `MatchesService.gs`
6. `PredictionsService.gs`
7. `ScoresService.gs`
8. `RankingsService.gs`
9. `OfficialFixtureService.gs`
10. `LocalFixtureData.gs`
11. `appsscript.json`

Despues de pegar los archivos:

1. Guardar con `Ctrl + S`.
2. Ejecutar `setupSpreadsheet`.
3. Ejecutar `syncOfficialFixture` si se quiere recargar fixture/equipos.
4. Ejecutar una sola vez `createFiveMinuteFixtureTrigger` si se quiere sincronizacion automatica cada 5 minutos.
5. Publicar nueva version:
   - `Implementar > Gestionar implementaciones`
   - Editar implementacion
   - Version: `Nueva version`
   - Implementar

Acciones disponibles desde frontend:

- `registerParticipant`
- `getParticipantByEmail`
- `getPredictionsByParticipant`
- `listMatches`
- `savePrediction`
- `getRankingIndividual`
- `getRankingAreas`
- `adminLogin`
- `saveMatch`
- `saveResult`
- `recalculateScores`
- `syncOfficialFixture`

Validacion esperada:

- `getParticipantByEmail` debe devolver participante por email registrado.
- `getPredictionsByParticipant` debe devolver predicciones vigentes del participante.
- `syncOfficialFixture` debe cargar 48 equipos y 104 partidos.
