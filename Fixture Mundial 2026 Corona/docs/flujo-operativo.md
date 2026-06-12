# Flujo operativo

## Preparacion inicial

1. Crear Google Sheet.
2. Cargar Apps Script.
3. Ejecutar `setupSpreadsheet()`.
4. Configurar endpoint en frontend.
5. Cargar partidos desde `admin.html`.

## Operacion diaria

1. Participantes se registran.
2. Participantes cargan o corrigen predicciones antes del cierre.
3. Administrador carga resultados.
4. Administrador recalcula puntajes y rankings.
5. Usuarios consultan rankings.

## Cierre de predicciones

Cada partido tiene `fecha_limite_prediccion`. Apps Script bloquea altas y modificaciones posteriores a esa fecha.

## Recalculo

El recalculo reconstruye hojas de `Puntajes`, `Ranking Individual` y `Ranking Areas` desde participantes, predicciones y resultados.

## Sincronizacion de fixture

El administrador puede forzar la sincronizacion desde `admin.html` con el boton `Sincronizar fixture`.

Para sincronizacion automatica, ejecutar una vez en Apps Script:

```js
createFiveMinuteFixtureTrigger()
```

Con API externa, la sincronizacion puede traer resultados cuando los partidos vengan con estado `finished` y marcadores `score_a` / `score_b`.

Con fixture embebido, se actualizan equipos y partidos, pero no resultados reales.
