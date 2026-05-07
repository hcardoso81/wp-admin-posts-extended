# AGENTS.md

Contexto operativo para agentes que trabajen en este repositorio.

## Proyecto

WP Admin Posts Extended es un plugin personalizado para WordPress orientado a mejorar la gestion editorial dentro del panel administrativo.

El plugin extiende la pantalla nativa de listado de posts (`edit.php?post_type=post`) agregando:

- Filtro multiple por etiquetas mediante Select2.
- Compatibilidad con filtros nativos de WordPress, incluyendo categoria, fecha y busqueda.
- Exportacion de los resultados filtrados a un archivo Excel `.xlsx`.
- Campo editorial "Fuente" mediante ACF, con opciones para clasificar el contenido como "Nota original" o "Comunicado de prensa".

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
- Actualmente soporta etiquetas, categoria, fecha y busqueda.
- Debe mantenerse como objeto simple, sin llamadas directas a WordPress.

`domain/PostRepositoryInterface.php`

- Define el contrato para obtener posts a partir de `PostCriteria`.
- Permite desacoplar la exportacion de la implementacion concreta basada en WordPress.

### Infrastructure Layer

`infrastructure/wordpress/Request.php`

- Construye un `PostCriteria` a partir de `$_GET` en el admin.
- Sanitiza entradas como `admin_tag`, `m` y `s`.
- Mantener aqui la traduccion entre parametros HTTP de WordPress y objetos del dominio.

`infrastructure/wordpress/AdminQueryModifier.php`

- Aplica un `PostCriteria` sobre la query principal del listado admin.
- Usa `tax_query`, `cat`, `m` y `s` sobre `WP_Query`.

`infrastructure/wordpress/WpPostRepository.php`

- Implementacion concreta de `PostRepositoryInterface`.
- Convierte `PostCriteria` en argumentos para `get_posts()`.
- Se usa para obtener los posts que luego se exportan.

### Admin Layer

`admin/AdminFiltersController.php`

- Registra hooks para renderizar filtros, aplicar filtros y mostrar el boton de exportacion.
- Renderiza `admin/views/tag-filter.php` y `admin/views/export-button.php`.
- Aplica filtros sobre la main query del admin mediante `AdminQueryModifier`.

`admin/AdminExportController.php`

- Maneja la exportacion desde `admin_init`.
- Verifica contexto admin, `post_type=post`, accion `export_posts` y permisos `edit_posts`.
- Obtiene posts filtrados usando `WpPostRepository`.
- Genera el `.xlsx` con PhpSpreadsheet.
- Columnas actuales: Fecha, Titulo, Link, Publicado en LinkedIn, Fuente, Categorias, Etiquetas.

`admin/AdminAssets.php`

- Carga Select2 desde CDN y CSS propio en `edit.php`.
- Inicializa Select2 sobre `#filter-by-tag`.

`admin/views/tag-filter.php`

- Renderiza el selector multiple de etiquetas.
- Usa `admin_tag[]` como nombre del parametro GET.
- Preserva seleccion previa usando los slugs elegidos.

`admin/views/export-button.php`

- Renderiza el boton `Exportar EXCEL`.
- El boton envia `export_posts=1` en el mismo formulario del listado.

`admin/assets/css/admin.css`

- Ajustes visuales para integrar Select2 y el boton de exportacion al estilo de WordPress Admin.

## Flujo Funcional

1. WordPress carga el plugin desde `wp-admin-posts-extended.php`.
2. El plugin carga Composer y luego `bootstrap/admin.php`.
3. En `plugins_loaded`, se registran assets, filtros y exportacion.
4. En la pantalla de posts:
   - `restrict_manage_posts` agrega el selector de etiquetas y el boton de exportacion.
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
```

Para cambios funcionales, validar manualmente en WordPress Admin:

- El listado de posts carga sin errores.
- El filtro multiple de etiquetas aparece y mantiene seleccion.
- Los filtros nativos de categoria, fecha y busqueda siguen funcionando.
- La exportacion respeta los filtros aplicados.
- El Excel abre correctamente y contiene fecha, titulo, link, estado de LinkedIn, fuente, categorias y etiquetas.

## Notas Para Futuras Extensiones

- Si se agregan nuevos filtros, extender primero `PostCriteria`, luego `Request`, `AdminQueryModifier` y `WpPostRepository`.
- Si se agregan nuevas columnas al Excel, hacerlo en `AdminExportController::exportExcel()`.
- Si se agregan controles visuales, ubicar el markup en `admin/views/` y los estilos en `admin/assets/css/admin.css`.
- Si se agrega una nueva fuente de datos, crear una implementacion alternativa de `PostRepositoryInterface`.

## Version Para LinkedIn o CV

Desarrolle un plugin personalizado para WordPress que extiende el panel administrativo de posts con filtros avanzados y exportacion a Excel. La solucion incorpora filtrado multiple por etiquetas mediante Select2, compatibilidad con filtros nativos de categoria, fecha y busqueda, y generacion de reportes `.xlsx` usando PhpSpreadsheet. El proyecto fue estructurado con una arquitectura modular por capas, separando dominio, infraestructura y controladores administrativos, lo que mejora la mantenibilidad y escalabilidad del codigo. Tambien integre campos personalizados con ACF para enriquecer la clasificacion editorial del contenido.

## Version Corta

Plugin WordPress personalizado para mejorar la gestion editorial del backend, agregando filtros avanzados por etiquetas, integracion con filtros nativos y exportacion de posts filtrados a Excel. Desarrollado en PHP con WordPress Plugin API, Composer, PhpSpreadsheet, ACF y Select2, bajo una arquitectura modular por capas.
