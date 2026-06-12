# PROJECT_CONTEXT.md - molienda-claude

## 1. Objetivo funcional del sistema

Sistema web interno para monitoreo y consulta de datos de molienda e indicadores de fabrica del Ingenio La Corona.

Por la estructura detectada, el sistema muestra informacion operativa vinculada a:

- Molienda online.
- Indicadores de fabrica.
- Analisis de azucar.
- Resumen de fabrica.
- Monitoreo de fabrica.
- Gestion o consulta de usuarios.
- Consultas relacionadas con canieros, ordenes/operaciones de azucar y datos historicos.

Tambien convive con flujos Node-RED y scripts SQL asociados a la adquisicion, simulacion o carga de datos desde OPC/Kepserver, SQL Server/MySQL u otras fuentes operativas.

## 2. Tecnologias usadas

Tecnologias detectadas por estructura y archivos:

- PHP procedural.
- JavaScript.
- Bootstrap.
- jQuery.
- Chart.js.
- Select2.
- Date Range Picker / Moment.js.
- DataTables con Bootstrap.
- MySQL/MariaDB mediante `mysqli`.
- Node-RED para flujos de integracion y adquisicion de datos.
- SQL mediante scripts `.sql`.

No se detecto framework PHP formal en la estructura revisada.

## 3. Estructura de carpetas detectada

Raiz del proyecto:

- `molienda/`: aplicacion web principal PHP.
- `http- falla modal 20-05-26/`: copia o variante de la aplicacion, aparentemente usada para diagnostico o respaldo de una falla puntual.
- `Flujos Node Red/`: flujos Node-RED relacionados con OPC, SQL Server, MySQL, indicadores y molienda en tiempo real.
- `SQL/`: scripts de estructura, datos, configuracion y consultas de base de datos.
- `imagenes/`: capturas o imagenes de referencia del sistema.
- `info y datos/`: archivos de soporte, planillas y datos de Kepserver/OPC.
- `n8n/`: carpeta reservada o asociada a automatizaciones n8n.
- `AGENTS.md`: reglas locales del proyecto.
- `analizar_insert.py`: utilidad Python de analisis.
- `molienda-19-05-2026.tar.gz`: paquete comprimido o respaldo del sistema.

Estructura principal dentro de `molienda/`:

- `index.php`: entrada principal que incluye `home.php`.
- `home.php`: contenedor principal y ruteo por parametros `menu`/`accion`.
- `view.php`: entrada alternativa o de prueba para `molienda_online_test.php`.
- `conexiones/`: conexion a base de datos.
- `controller/`: logica PHP por dominio.
- `views/`: pantallas PHP.
- `api/`: endpoints PHP de integracion.
- `funciones/`: funciones PHP auxiliares.
- `js/`: librerias y scripts JavaScript.
- `css/`: Bootstrap, estilos propios y librerias CSS.
- `assets/`: imagenes, iconos, documentos, adjuntos y archivos asociados a usuarios.
- `fonts/`: fuentes usadas por Bootstrap/Glyphicons.

Subcarpetas relevantes de `views/`:

- `views/includes/`: cabecera comun.
- `views/molienda_online/`: pantallas de molienda, campo, detalle de cania, paradas y monitoreo.
- `views/indicadores_fabrica/`: indicadores de caldera, destileria, fabricacion, trapiche, usina, resumen y analisis de azucar.
- `views/usuarios/`: login, registro, alta, edicion, cambio de clave y administracion de usuarios.
- `views/otros/`: pantallas varias, incluyendo canieros y azucar.

## 4. Puntos de entrada principales

Puntos de entrada PHP detectados:

- `molienda/index.php`: incluye `home.php`; probable entrada web principal.
- `molienda/home.php`: rutea contenido segun `$_GET['menu']` y `$_GET['accion']`.
- `molienda/view.php`: entrada alternativa para pruebas de molienda online.
- `molienda/api/recepcion_http_request_post.php`: endpoint API para recepcion HTTP POST.
- `molienda/api/sincronizar_canieros.php`: endpoint o proceso de sincronizacion de canieros.

Rutas probables segun `home.php`:

- `home.php?menu=molienda_online`
- `home.php?menu=indicadores_fabrica`
- `home.php?menu=analisis_azucar`
- `home.php?menu=resumen_fabrica`
- `home.php?menu=molienda_campo`
- `home.php?menu=monitoreo_fabrica`
- `home.php?menu=usuarios`
- `home.php?menu=alta_usuario`
- `home.php?accion=cambiar_pass`

Si no hay parametros, `home.php` carga por defecto `views/molienda_online/index.php`.

## 5. Reglas para futuros cambios

- Leer siempre `AGENTS.md` antes de modificar el proyecto.
- No modificar archivos fuera del alcance solicitado.
- No usar ni commitear credenciales reales.
- No inventar tablas, columnas, endpoints ni flujos que no existan.
- No ejecutar acciones sobre produccion.
- No modificar logica de ERP, PLC, SCADA o bases productivas sin autorizacion explicita.
- Mantener separacion clara entre test y produccion.
- Preservar compatibilidad con el entorno existente: PHP procedural, Bootstrap, JavaScript y MySQL.
- Documentar cambios con una forma concreta de prueba.
- Priorizar cambios pequenos, auditables y reversibles.
- Antes de tocar conexion, APIs o integraciones Node-RED, validar impacto operativo.
- En SQL Server, mantener compatibilidad con SQL Server 2008 R2 cuando aplique.
- No escribir directo sobre ERP Calipso; usar middleware y validaciones cuando el flujo toque ERP.

## 6. Riesgos conocidos

- `molienda/conexiones/conexion.php` contiene credenciales visibles de base de datos. Deben moverse a configuracion segura fuera del codigo antes de publicar o compartir.
- Hay dos copias muy similares de la aplicacion: `molienda/` y `http- falla modal 20-05-26/`. Esto aumenta riesgo de editar la copia incorrecta.
- Existen archivos con sufijos como `ant`, `ORIG`, `viejo`, `copy` y variantes de prueba, lo que puede generar confusion sobre cual version esta vigente.
- Hay multiples versiones de Bootstrap, jQuery y otros assets JS/CSS. Esto puede provocar conflictos de compatibilidad o carga duplicada.
- El ruteo se realiza por includes directos segun parametros GET; requiere cuidado para evitar rutas no controladas o efectos laterales.
- La logica de negocio parece mezclada entre vistas, controladores y funciones auxiliares.
- Hay adjuntos y documentos dentro de `assets/`, incluyendo archivos con nombres operativos. Debe revisarse si corresponden a datos reales o sensibles.
- La carpeta `SQL/` contiene dumps y scripts de datos; revisar sensibilidad antes de versionar o mover.
- `home.php` referencia carpetas como `views/admin/` y `views/tipos_usuario/` que no se observaron en la estructura listada; puede haber rutas rotas o codigo heredado.
- Uso de CDN externo para `html2pdf` en `home.php`; puede fallar en red industrial sin Internet.

## 7. Comandos o pasos para probar localmente

Prueba local inferida para entorno PHP simple:

1. Ubicarse en la carpeta de la aplicacion:

```powershell
cd C:\claudecode\proyectos-ingenio\molienda-claude\molienda
```

2. Levantar servidor PHP local, si PHP esta instalado:

```powershell
php -S 127.0.0.1:8000
```

3. Abrir en navegador:

```text
http://127.0.0.1:8000/index.php
```

4. Probar rutas principales:

```text
http://127.0.0.1:8000/home.php?menu=molienda_online
http://127.0.0.1:8000/home.php?menu=indicadores_fabrica
http://127.0.0.1:8000/home.php?menu=analisis_azucar
http://127.0.0.1:8000/home.php?menu=resumen_fabrica
http://127.0.0.1:8000/home.php?menu=monitoreo_fabrica
```

Notas:

- Estas pruebas pueden requerir acceso a la base MySQL configurada.
- No probar contra base productiva sin autorizacion.
- Si no hay conectividad a la base, validar al menos carga inicial, errores PHP visibles y assets estaticos.
- Para pruebas reales conviene crear configuracion local/test separada antes de ejecutar endpoints `api/`.
