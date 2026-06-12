# Design System Corona Mundial 2026

## 1. Resumen

El sistema visual busca una aplicacion corporativa premium para Ingenio La Corona: clara, moderna, motivadora y sobria. La experiencia mezcla dashboard ejecutivo con gamificacion empresarial, evitando cualquier estetica de apuestas o casino. La direccion final se denomina **Mundial Corporativo Premium**.

## 2. Tokens de diseño

Colores:

- Rojo Corona: `#C8102E` para acciones primarias, foco visual y navegacion activa.
- Rojo oscuro: `#A0001C` para hover y estados activos.
- Dorado Mundial: `#D4AF37` solo para podio, premios y elementos destacados.
- Plata: `#C0C0C0` para segundo puesto.
- Bronce: `#CD7F32` para tercer puesto.
- Negro corporativo: `#1F1F1F` para texto principal.
- Gris oscuro: `#495057` para texto secundario.
- Gris claro: `#F5F5F5` para fondos.
- Blanco: `#FFFFFF` para superficies principales.

Tipografia:

- Montserrat por Google Fonts.
- Pesos: 400, 500, 600, 700.
- Titulo: 700.
- Subtitulo: 500/600.
- Texto base minimo: 16px.

Espaciado y forma:

- Cards: radius 12px.
- Botones: pill radius.
- Sombras suaves: `0 8px 22px rgba(31,31,31,.06)` y `0 14px 36px rgba(31,31,31,.09)`.
- Transicion: `0.2s ease`.

## 3. Componentes UI

- Header sticky blanco con logo, sombra suave y altura cercana a 68px.
- Hero claro con gradientes sutiles, logo destacado, CTA principal y contador.
- Card "proximo foco" para orientar la accion del usuario.
- Fondos premium por seccion con imagen atmosferica al 30% aplicada solo en pseudo-elementos.
- Cards ejecutivas con sombra, borde invisible y hover moderado.
- Botones primarios rojos, secundarios outline rojo y destacados dorados.
- Tablas responsive con encabezado rojo y texto blanco.
- Formularios con controles de 46px de alto minimo y focus rojo suave.
- Podio Top 3 con iconos y color dorado/plata/bronce.
- KPI cards con iconos Bootstrap discretos.
- Estados vacios con borde punteado y mensaje claro.
- Microanimacion `micro-rise` para entrada suave de cards, desactivada si el usuario prefiere reducir movimiento.

## 4. Reglas de uso visual

- Usar rojo Corona para acciones y navegacion, no como fondo dominante.
- Usar dorado solo para premios, podio o acciones especiales.
- Usar imagenes adjuntas como atmosfera, nunca como contenido dominante.
- No aplicar opacidad al contenedor de texto; usar `::before` para imagen y `::after` para overlay.
- No usar colores fluorescentes, fondos recargados, animaciones pesadas ni recursos visuales tipo apuestas.
- Mantener tablas y formularios sobrios, priorizando legibilidad.
- No depender solo del color: usar iconos, texto y badges.

## 5. Aplicacion por pantalla

- Inicio: hero motivacional, contador, KPIs y proximos partidos.
- Registro: formulario limpio, jerarquia simple y CTA de alta.
- Predicciones: foco en email, partido, ganador y goles.
- Ranking individual: podio Top 3 y tabla completa.
- Ranking por area: tabla corporativa con icono de edificio y promedio Top 5.
- Reglamento: contenido editable con card clara.
- Premios: contenido destacado con uso medido del dorado.
- Administracion: formularios operativos, tabla de partidos y boton sincronizar fixture.

## 6. Responsive

- Mobile first.
- Header colapsable en pantallas chicas.
- Cards apiladas en mobile.
- Tablas dentro de `.table-responsive`.
- Podio en columnas desktop y apilado en mobile.
- Botones con altura tactil minima de 44px.

## 7. Accesibilidad

- Contraste alto en botones y encabezados.
- Estados `focus-visible` con outline dorado.
- Logo con texto alternativo.
- Navegacion clara y botones grandes.
- Mensajes de estado con texto explicito.

## 8. Validacion visual

Checklist:

- La interfaz se ve corporativa y profesional.
- El header no supera 70px aproximadamente.
- No parece plataforma de apuestas.
- El rojo Corona es el color principal.
- El dorado queda reservado a elementos especiales.
- Las tablas son responsive.
- El Top 3 se diferencia visualmente.
- Mobile mantiene lectura y tactilidad.
