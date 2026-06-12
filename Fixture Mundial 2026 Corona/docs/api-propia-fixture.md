# API propia de fixture oficial

## Objetivo

Permitir que la app Corona Mundial 2026 consuma una API propia alimentada con datos oficiales y cargue automaticamente partidos, resultados y equipos en Google Sheets.

## Configuracion en Google Sheets

En la hoja `Configuracion` usar estas claves:

| clave | ejemplo | descripcion |
| --- | --- | --- |
| fixture_api_url | https://api.ingeniolacorona.com/worldcup-2026/fixture | Endpoint propio |
| fixture_api_token | token-secreto | Opcional |
| fixture_api_provider | api-propia-corona | Nombre para logs |
| fixture_api_language | es | Idioma solicitado |
| fixture_sync_enabled | true | Habilita sync programada |

## Endpoint esperado

Apps Script llama por GET a:

```text
fixture_api_url?lang=es
```

Si `fixture_api_token` tiene valor, envia:

```text
Authorization: Bearer TOKEN
```

## Modo sin API externa

Si todavia no existe una API externa, el proyecto incluye una fuente propia embebida en Apps Script:

```text
LocalFixtureData.gs
```

Con `fixture_api_url` vacio, la funcion:

```js
syncOfficialFixture({ force: true })
```

usa ese fixture semilla y carga:

- 48 equipos.
- 104 partidos.
- Horarios disponibles.
- Nombres en espanol.
- Fases eliminatorias con placeholders.

La misma Web App tambien puede responder como API propia en:

```text
https://script.google.com/macros/s/ID_DEPLOY/exec?api=fixture
```

Ese endpoint devuelve JSON con `teams` y `matches`.

## Respuesta JSON esperada

```json
{
  "provider": "api-propia-corona",
  "language": "es",
  "updated_at": "2026-06-11T20:00:00-03:00",
  "teams": [
    {
      "code": "ARG",
      "name": "Argentina",
      "name_es": "Argentina",
      "group": "A"
    }
  ],
  "matches": [
    {
      "match_id": "wc2026_001",
      "stage": "Group Stage",
      "stage_es": "Fase de grupos",
      "team_a": {
        "code": "ARG",
        "name": "Argentina",
        "name_es": "Argentina"
      },
      "team_b": {
        "code": "CAN",
        "name": "Canada",
        "name_es": "Canada"
      },
      "start_time": "2026-06-11T21:00:00-03:00",
      "prediction_deadline": "2026-06-11T20:00:00-03:00",
      "status": "scheduled",
      "score_a": null,
      "score_b": null
    }
  ]
}
```

## Estados soportados

| API | Google Sheets |
| --- | --- |
| scheduled | programado |
| live | en_juego |
| finished | finalizado |
| postponed | suspendido |

Tambien acepta estados ya traducidos: `programado`, `en_juego`, `finalizado`, `suspendido`.

## Campos obligatorios por partido

- `match_id`
- `team_a.name_es` o `team_a.name`
- `team_b.name_es` o `team_b.name`
- `start_time`
- `status`

## Resultados

Cuando `status = finished`, la API debe enviar:

```json
{
  "score_a": 2,
  "score_b": 1
}
```

Apps Script guarda o actualiza la hoja `Resultados` y recalcula puntajes/rankings.

## Funciones Apps Script

### Sincronizacion manual

Ejecutar desde Apps Script:

```js
syncOfficialFixture({ force: true })
```

Para hacerlo desde el frontend/admin se usa la accion:

```json
{
  "action": "syncOfficialFixture",
  "payload": {
    "admin_key": "cambiar-esta-clave",
    "force": true
  }
}
```

### Trigger cada 5 minutos

Ejecutar una sola vez:

```js
createFiveMinuteFixtureTrigger()
```

La funcion elimina triggers anteriores de `syncOfficialFixture` antes de crear uno nuevo, para evitar duplicados.

Antes de activar el trigger, configurar:

```text
fixture_sync_enabled = true
```

Si se usa fixture embebido sin `fixture_api_url`, el trigger tambien puede ejecutarse. En ese modo actualiza fixture y horarios, pero no obtendra resultados reales hasta que exista una API propia externa que devuelva partidos `finished` con `score_a` y `score_b`.

### Boton desde admin

El panel `admin.html` incluye el boton:

```text
Sincronizar fixture
```

Ese boton ejecuta:

```json
{
  "action": "syncOfficialFixture",
  "payload": {
    "admin_key": "clave-admin",
    "force": true
  }
}
```

Actualiza `Equipos`, `Partidos`, `Resultados` si existen resultados en la fuente, y recalcula rankings si entraron resultados nuevos.

## Hojas agregadas

### Equipos

Guarda nombres oficiales y nombres en español:

```text
codigo | nombre_api | nombre_es | grupo | fecha_actualizacion
```

### Sincronizaciones

Guarda trazabilidad de cada ejecucion:

```text
sync_id | proveedor | estado | partidos_actualizados | resultados_actualizados | mensaje | fecha_sync
```

## Criterio operativo

- La API propia debe entregar datos ya validados contra fuente oficial.
- La app no consulta FIFA directamente.
- La app no pisa predicciones.
- La app actualiza partidos y resultados.
- Si hay resultados nuevos, recalcula puntajes y rankings.
