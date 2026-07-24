# AGENTS.md

Contexto operativo para agentes que trabajen en este repositorio.

## Proyecto

WP Admin Posts Extended es un plugin personalizado para WordPress orientado a mejorar la gestion editorial dentro del panel administrativo.

La version actual del plugin es 2.5.1.

El plugin extiende la pantalla nativa de listado de posts (`edit.php?post_type=post`) agregando:

- Filtro multiple por etiquetas mediante Select2.
- Filtro por autor integrado al listado administrativo de posts.
- Filtro dinamico por año desde 2016 hasta el año actual.
- Compatibilidad con filtros nativos de WordPress, incluyendo categoria, fecha y busqueda.
- Exportacion de los resultados filtrados a un archivo Excel `.xlsx`.
- La fecha exportada se muestra con formato `dd/mm/yyyy`.
- Exportacion del estado de publicacion en LinkedIn como texto, incluyendo pendiente, publicado, publicado manualmente y programado para publicar.
- Campo editorial "Fuente" mediante ACF, con opciones para clasificar el contenido como "Nota original" o "Comunicado de prensa".
- Campo editorial "hasVideo" mediante ACF, con checkbox para marcar posts que contienen videos de YouTube embebidos.
- Columna "Contiene Videos" al final del listado administrativo de posts y en la exportacion Excel.
- Herramienta temporal `tools/backfill-has-video.php` para completar `hasVideo` en posts publicados desde el 1 de enero de 2025 hasta hoy, con simulacion detallada de los posts detectados.

El objetivo principal del proyecto es mantener la logica desacoplada del archivo principal del plugin y organizada por responsabilidades.

## Stack Tecnico

- PHP.
- WordPress Plugin API.
- WordPress Hooks: `plugins_loaded`, `restrict_manage_posts`, `pre_get_posts`, `admin_init`, `admin_enqueue_scripts`, `acf/init`.
- `WP_Query`, `get_posts()` y funciones nativas de WordPress.
- Advanced Custom Fields mediante registro local de field group.
- Composer.
- PhpSpreadsheet para generar archivos Excel `.xlsx`.
- Select2 + jQuery para filtros avanzados en el admin.
- CSS personalizado integrado visualmente con WordPress Admin.

## Arquitectura

El proyecto sigue una arquitectura modular por capas:

- `wp-admin-posts-extended.php`: archivo principal del plugin. Define constantes, carga Composer y delega el bootstrap.
- `bootstrap/admin.php`: registra dependencias, controladores, hooks administrativos y el field group local de ACF.
- `domain/`: capa de dominio. Contiene criterios de busqueda y contratos.
- `infrastructure/wordpress/`: adaptadores concretos para WordPress, incluyendo request parsing, modificacion de queries y repositorios.
- `admin/`: capa administrativa. Controladores, vistas y assets del panel de administracion.

La intencion es que la logica de negocio de filtrado viva en objetos reutilizables, mientras que WordPress quede encapsulado en infraestructura y controladores admin.

## Capas y Responsabilidades

### Domain Layer

`domain/PostCriteria.php`

- Representa los criterios de filtrado disponibles.
- Actualmente soporta etiquetas, autor, categoria, año, fecha y busqueda.
- Debe mantenerse como objeto simple, sin llamadas directas a WordPress.

`domain/PostRepositoryInterface.php`

- Define el contrato para obtener posts a partir de `PostCriteria`.
- Permite desacoplar la exportacion de la implementacion concreta basada en WordPress.

### Infrastructure Layer

`infrastructure/wordpress/Request.php`

- Construye un `PostCriteria` a partir de `$_GET` en el admin.
- Sanitiza entradas como `admin_tag`, `admin_author`, `admin_year`, `m` y `s`.
- Mantener aqui la traduccion entre parametros HTTP de WordPress y objetos del dominio.

`infrastructure/wordpress/AdminQueryModifier.php`

- Aplica un `PostCriteria` sobre la query principal del listado admin.
- Usa `tax_query`, `author`, `cat`, `year`, `m` y `s` sobre `WP_Query`.

`infrastructure/wordpress/WpPostRepository.php`

- Implementacion concreta de `PostRepositoryInterface`.
- Convierte `PostCriteria` en argumentos para `get_posts()`.
- Se usa para obtener los posts que luego se exportan.

### Admin Layer

`admin/AdminFiltersController.php`

- Registra hooks para renderizar filtros, aplicar filtros y mostrar el boton de exportacion.
- Renderiza `admin/views/tag-filter.php`, `admin/views/author-filter.php` y `admin/views/export-button.php`.
- Renderiza `admin/views/year-filter.php` con opciones desde 2016 hasta el año actual.
- Aplica filtros sobre la main query del admin mediante `AdminQueryModifier`.

`admin/AdminExportController.php`

- Maneja la exportacion desde `admin_init`.
- Verifica contexto admin, `post_type=post`, accion `export_posts` y permisos `edit_posts`.
- Obtiene posts filtrados usando `WpPostRepository`.
- Genera el `.xlsx` con PhpSpreadsheet.
- Columnas actuales: Fecha, Titulo, Link, Estado LinkedIn, Fuente, Contiene Videos, Categorias, Etiquetas.
- La columna Estado LinkedIn lee `_linkedin_status` y mantiene compatibilidad con `_linkedin_posted`.

`admin/AdminPostColumnsController.php`

- Registra columnas personalizadas en el listado administrativo de posts.
- Agrega la columna `Contiene Videos` al final del listado.
- Lee el meta `hasVideo` y muestra `Si` o `No`.

`admin/AdminAssets.php`

- Carga Select2 desde CDN y CSS propio en `edit.php`.
- Inicializa Select2 sobre `#filter-by-tag`.

`admin/views/tag-filter.php`

- Renderiza el selector multiple de etiquetas.
- Usa `admin_tag[]` como nombre del parametro GET.
- Preserva seleccion previa usando los slugs elegidos.

`admin/views/author-filter.php`

- Renderiza el selector de autores.
- Usa `admin_author` como nombre del parametro GET.
- Debe usar `capability => edit_posts` en `wp_dropdown_users()` para evitar el argumento obsoleto `who`.
- Preserva seleccion previa usando el ID de usuario elegido.

`admin/views/export-button.php`

- Renderiza el boton `Exportar EXCEL`.
- El boton envia `export_posts=1` en el mismo formulario del listado.

`admin/assets/css/admin.css`

- Ajustes visuales para integrar Select2, selector de autor y botones al estilo de WordPress Admin.
- Mantiene espaciado y wrapping de la barra superior de filtros para evitar controles pegados o superpuestos.

`tools/backfill-has-video.php`

- Herramienta temporal de ejecucion manual para actualizar posts historicos.
- Busca posts publicados desde el 1 de enero de 2025 hasta la fecha actual inclusive.
- Analiza `post_content` para detectar URLs o embeds de YouTube.
- En modo simulacion muestra ID, fecha, titulo, estado actual de `hasVideo`, accion simulada y enlace de edicion de cada post detectado.
- Marca el ACF `hasVideo` con `['1']` cuando encuentra contenido de YouTube.
- Requiere usuario logueado con `manage_options`, nonce y allowlist de IP configurado.
- Guarda la opcion `wpape_has_video_backfill_completed` para evitar ejecuciones repetidas.
- Debe borrarse del servidor despues de ejecutarse correctamente.

## Flujo Funcional

1. WordPress carga el plugin desde `wp-admin-posts-extended.php`.
2. El plugin carga Composer y luego `bootstrap/admin.php`.
3. En `plugins_loaded`, se registran assets, filtros y exportacion.
4. En la pantalla de posts:
   - `restrict_manage_posts` agrega los selectores de etiquetas, autor y año, además del boton de exportacion.
   - `admin_enqueue_scripts` carga Select2 y CSS personalizado.
   - `pre_get_posts` toma los parametros del request, crea un `PostCriteria` y modifica la query principal.
5. Si el usuario presiona `Exportar EXCEL`:
   - `admin_init` detecta `export_posts=1`.
   - Se reconstruye el mismo `PostCriteria` desde el request.
   - `WpPostRepository` obtiene los posts filtrados.
   - `AdminExportController` genera y descarga el archivo `.xlsx`.

## Campo ACF Fuente

El field group local se registra en `bootstrap/admin.php` durante `acf/init`.

Campo:

- Nombre: `fuente`.
- Tipo: radio.
- Opciones:
  - `nota_original`: Nota original.
  - `comunicado_prensa`: Comunicado de prensa.
- Valor por defecto: `comunicado_prensa`.
- Ubicacion: posts (`post_type == post`).

La exportacion lee este meta con `get_post_meta($post->ID, 'fuente', true)` y muestra:

- "Comunicado de prensa" si el valor esta vacio o es `comunicado_prensa`.
- "Nota original" para cualquier otro valor esperado actualmente.

## Campo ACF hasVideo

El field group local se registra en `bootstrap/admin.php` durante `acf/init`.

Campo:

- Nombre: `hasVideo`.
- Tipo: checkbox.
- Label: `Contiene videos de YouTube embebidos`.
- Opcion:
  - `1`: Si.
- Valor por defecto: vacio, interpretado como false.
- Ubicacion: posts (`post_type == post`).

El listado administrativo muestra este valor en la columna `Contiene Videos`, ubicada al final de la tabla.

La exportacion lee este meta con `get_post_meta($post->ID, 'hasVideo', true)` y muestra:

- "Si" cuando el checkbox esta marcado.
- "No" cuando esta vacio o sin marcar.

## Backfill historico hasVideo

La herramienta temporal `tools/backfill-has-video.php` sirve para completar `hasVideo` en posts publicados desde el 1 de enero de 2025 hasta la fecha actual inclusive.

Uso recomendado:

1. Configurar el allowlist de IP dentro del archivo antes de subirlo.
2. Abrir el archivo desde el navegador con un usuario administrador logueado.
3. Ejecutar primero en modo simulacion.
4. Revisar el detalle de posts detectados que muestra la simulacion.
5. Ejecutar sin simulacion solo si el conteo y el detalle son correctos.
6. Borrar el archivo del servidor al finalizar.

La deteccion contempla URLs y embeds de `youtube.com`, `youtu.be` y `youtube-nocookie.com`.

## Convenciones del Proyecto

- Mantener el archivo principal del plugin liviano.
- Registrar hooks desde controladores o bootstrap, no desde archivos de vistas.
- Mantener el dominio sin dependencias directas de WordPress.
- Encapsular acceso a `$_GET` en `Request`.
- Encapsular consultas concretas de WordPress en infraestructura.
- Usar vistas PHP simples para markup del admin.
- Sanitizar todo input que venga del request.
- Escapar output en vistas con funciones de WordPress como `esc_attr()` y `esc_html()`.
- Evitar mezclar generacion de Excel con renderizado de UI.
- Cuando una tarea agregue o cambie funcionalidad del plugin, actualizar tambien `AGENTS.md`, `README.md`, `readme.txt`, la descripcion del encabezado del plugin y la constante/version del plugin en `wp-admin-posts-extended.php`.
- Incrementar la version del plugin en cada cambio funcional, usando versionado semantico segun el alcance del cambio.
- Mantener el README de GitHub profesional y actualizado, incluyendo chips/badges de tecnologias cuando se modifique el stack o se agregue una tecnologia visible.
- Al cerrar cambios de codigo, sugerir un mensaje de commit en formato Conventional Commits.

## Dependencias

Composer gestiona PhpSpreadsheet:

```bash
composer install
```

El plugin requiere `vendor/autoload.php`, por lo que `vendor/` debe existir en ambientes donde se ejecute el plugin, salvo que el despliegue tenga otra estrategia de build.

Select2 se carga desde CDN en `AdminAssets`.

## Verificacion Recomendada

No hay suite automatizada registrada en este repositorio. Para cambios en PHP, verificar al menos sintaxis:

```bash
php -l wp-admin-posts-extended.php
php -l bootstrap/admin.php
php -l domain/PostCriteria.php
php -l domain/PostRepositoryInterface.php
php -l infrastructure/wordpress/Request.php
php -l infrastructure/wordpress/AdminQueryModifier.php
php -l infrastructure/wordpress/WpPostRepository.php
php -l admin/AdminAssets.php
php -l admin/AdminFiltersController.php
php -l admin/AdminExportController.php
php -l admin/AdminPostColumnsController.php
php -l tools/backfill-has-video.php
php -l admin/views/author-filter.php
php -l admin/views/year-filter.php
```

Para cambios funcionales, validar manualmente en WordPress Admin:

- El listado de posts carga sin errores.
- El filtro multiple de etiquetas aparece y mantiene seleccion.
- El filtro por autor aparece, no genera warnings de WordPress y mantiene seleccion.
- Los filtros nativos de categoria, fecha y busqueda siguen funcionando.
- La exportacion respeta los filtros aplicados.
- El Excel abre correctamente y contiene fecha, titulo, link, estado de LinkedIn textual, fuente, contiene videos, categorias y etiquetas.
- La fecha del Excel se visualiza como `dd/mm/yyyy` y el filtro por año limita tanto el listado como la exportacion.

## Notas Para Futuras Extensiones

- Si se agregan nuevos filtros, extender primero `PostCriteria`, luego `Request`, `AdminQueryModifier` y `WpPostRepository`.
- Si se agregan nuevas columnas al Excel, hacerlo en `AdminExportController::exportExcel()`.
- Si se agregan controles visuales, ubicar el markup en `admin/views/` y los estilos en `admin/assets/css/admin.css`.
- Si se agrega una nueva fuente de datos, crear una implementacion alternativa de `PostRepositoryInterface`.

## Version Para LinkedIn o CV

Desarrolle un plugin personalizado para WordPress que extiende el panel administrativo de posts con filtros avanzados y exportacion a Excel. La solucion incorpora filtrado multiple por etiquetas mediante Select2, filtro por autor, compatibilidad con filtros nativos de categoria, fecha y busqueda, y generacion de reportes `.xlsx` usando PhpSpreadsheet. El exportador incluye el estado editorial de LinkedIn como texto, contemplando pendiente, publicado, publicado manualmente y programado para publicar. El proyecto fue estructurado con una arquitectura modular por capas, separando dominio, infraestructura y controladores administrativos, lo que mejora la mantenibilidad y escalabilidad del codigo. Tambien integre campos personalizados con ACF para enriquecer la clasificacion editorial del contenido.

## Version Corta

Plugin WordPress personalizado para mejorar la gestion editorial del backend, agregando filtros avanzados por etiquetas y autor, integracion con filtros nativos, campos ACF editoriales y exportacion de posts filtrados a Excel con estado de LinkedIn y contenido de video. Desarrollado en PHP con WordPress Plugin API, Composer, PhpSpreadsheet, ACF y Select2, bajo una arquitectura modular por capas.
