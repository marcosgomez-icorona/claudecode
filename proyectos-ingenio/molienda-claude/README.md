# molienda-claude

Sistema web interno para monitoreo y consulta de molienda e indicadores de fabrica del Ingenio La Corona.

La aplicacion principal esta en la carpeta `molienda/` y esta desarrollada en PHP procedural, JavaScript y Bootstrap.

## 1. Requisitos

Para ejecutar en entorno local se requiere:

- PHP instalado en el equipo de desarrollo.
- Servidor web local compatible con PHP:
  - PHP built-in server para pruebas simples.
  - Apache/IIS/Nginx con PHP para pruebas mas parecidas a un servidor real.
- Navegador web moderno.
- Acceso a una base de datos MySQL/MariaDB de test o local, si se quieren validar pantallas con datos.
- Conexion a Internet si se usan dependencias cargadas por CDN, como Bootstrap o html2pdf.

Importante:

- No usar credenciales reales en documentacion, commits ni configuraciones compartidas.
- No conectar contra produccion para pruebas de desarrollo.
- Antes de probar endpoints o pantallas que consultan datos, validar que la conexion apunte a un entorno seguro.

## 2. Estructura del proyecto

Estructura detectada en la raiz:

- `molienda/`: aplicacion web principal.
- `http- falla modal 20-05-26/`: copia o variante historica/de diagnostico.
- `Flujos Node Red/`: flujos Node-RED vinculados a OPC, SQL Server, MySQL e indicadores.
- `SQL/`: scripts SQL de estructura, datos, configuracion o simulacion.
- `imagenes/`: capturas y referencias visuales.
- `info y datos/`: planillas y archivos de soporte.
- `n8n/`: carpeta asociada a automatizaciones n8n.
- `AGENTS.md`: reglas de trabajo del proyecto.
- `PROJECT_CONTEXT.md`: contexto tecnico y operativo del proyecto.

Estructura principal de `molienda/`:

- `index.php`: entrada principal. Incluye `home.php`.
- `home.php`: ruteo principal por parametros `menu` y `accion`.
- `view.php`: entrada alternativa/de prueba.
- `conexiones/`: conexion a base de datos.
- `controller/`: logica PHP por dominio.
- `views/`: pantallas PHP.
- `api/`: endpoints PHP.
- `funciones/`: funciones auxiliares.
- `js/`: scripts y librerias JavaScript locales.
- `css/`: estilos y librerias CSS locales.
- `assets/`: imagenes, documentos, adjuntos e iconos.
- `fonts/`: fuentes usadas por assets de interfaz.

## 3. Como configurar entorno local

### Opcion rapida con servidor PHP integrado

Desde PowerShell:

```powershell
cd C:\claudecode\proyectos-ingenio\molienda-claude\molienda
php -S 127.0.0.1:8000
```

Luego abrir:

```text
http://127.0.0.1:8000/index.php
```

### Opcion con servidor web local

1. Configurar un sitio local apuntando el document root a:

```text
C:\claudecode\proyectos-ingenio\molienda-claude\molienda
```

2. Habilitar PHP en el servidor web.
3. Configurar la base de datos en un entorno local o de test.
4. Verificar que el sitio cargue desde el navegador.

### Base de datos

El proyecto usa `mysqli` y la conexion esta centralizada en:

```text
molienda/conexiones/conexion.php
```

Para pruebas locales, no usar credenciales productivas. Si se requiere validar con datos, preparar una base local o de test usando scripts revisados previamente desde `SQL/`.

No ejecutar scripts SQL sobre bases productivas sin autorizacion explicita.

## 4. Como probar la pantalla principal

Con el servidor local levantado, abrir:

```text
http://127.0.0.1:8000/index.php
```

Rutas principales inferidas:

```text
http://127.0.0.1:8000/home.php?menu=molienda_online
http://127.0.0.1:8000/home.php?menu=indicadores_fabrica
http://127.0.0.1:8000/home.php?menu=analisis_azucar
http://127.0.0.1:8000/home.php?menu=resumen_fabrica
http://127.0.0.1:8000/home.php?menu=molienda_campo
http://127.0.0.1:8000/home.php?menu=monitoreo_fabrica
```

Validaciones basicas:

- La pantalla principal carga sin errores PHP fatales.
- Se visualiza el panel de molienda online.
- Los estilos Bootstrap se aplican correctamente.
- No aparecen errores de rutas faltantes en includes principales.
- Si no hay base local/test disponible, documentar los errores esperados de conexion y no avanzar contra produccion.

## 5. Problemas comunes

### PHP no esta disponible

Sintoma:

```text
php : The term 'php' is not recognized
```

Accion:

- Instalar PHP o usar un servidor local que ya lo incluya.
- Verificar que `php.exe` este en el `PATH`.

### La pantalla carga sin estilos

Posibles causas:

- Sin Internet para cargar Bootstrap desde CDN.
- Rutas CSS locales incorrectas.
- Servidor iniciado desde una carpeta que no es `molienda/`.

Accion:

- Confirmar que el servidor local apunta a `molienda/`.
- Revisar consola del navegador para detectar archivos 404.

### Error de conexion a base de datos

Posibles causas:

- La configuracion actual apunta a una base no accesible desde el entorno local.
- No existe base local/test.
- Credenciales no configuradas para desarrollo.

Accion:

- No usar produccion para resolver la prueba.
- Preparar una base local o de test.
- Documentar que la pantalla requiere datos para validar completamente.

### Includes rotos

Riesgos conocidos:

- `home.php` referencia `views/admin/index.php` y `views/tipos_usuario/index.php`, pero esas carpetas no fueron detectadas.
- Algunas vistas usan includes relativos como `detalle_cania.php` o `datos_molienda_hora.php`; pueden fallar si se ejecutan fuera del flujo previsto.

Accion:

- Probar desde `index.php` o `home.php`.
- Evitar abrir vistas internas directamente salvo que se este diagnosticando una ruta concreta.

### Dependencias CDN no disponibles

El proyecto carga dependencias externas como Bootstrap y html2pdf por CDN en algunas pantallas.

Accion:

- Validar si el entorno local tiene Internet.
- Si el destino final no tendra Internet, planificar una tarea separada para usar assets locales ya existentes o versionados.

## 6. Checklist antes de pasar cambios a produccion

- Leer `AGENTS.md` y `PROJECT_CONTEXT.md`.
- Confirmar que solo se modificaron archivos dentro del alcance aprobado.
- Verificar que no se agregaron credenciales reales.
- Confirmar que no se conecto contra produccion durante la prueba.
- Validar la pantalla principal en entorno local o test.
- Probar las rutas afectadas por el cambio.
- Revisar consola del navegador por errores JavaScript o assets 404.
- Revisar logs PHP/servidor por warnings o errores.
- Confirmar que no se modificaron flujos Node-RED, SQL, ERP, PLC, SCADA ni base productiva sin autorizacion.
- Documentar procedimiento de prueba y resultado.
- Preparar rollback simple si el cambio afecta pantallas operativas.
