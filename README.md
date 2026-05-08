# WP Admin Posts Extended

Plugin personalizado para WordPress que mejora el listado administrativo de posts con filtros avanzados y exportacion a Excel.

## Funcionalidades

- Filtro multiple por etiquetas con Select2.
- Filtro por autor integrado al listado de posts.
- Compatibilidad con filtros nativos de WordPress: categoria, fecha y busqueda.
- Exportacion de los resultados filtrados a `.xlsx`.
- Columna de estado de LinkedIn en el Excel con soporte para `Pendiente`, `Publicado`, `Publicado manualmente` y `Programado para publicar`.
- Campo editorial `Fuente` mediante ACF para clasificar posts como `Nota original` o `Comunicado de prensa`.

## Arquitectura

El plugin mantiene una separacion por capas:

- `domain/`: criterios de busqueda y contratos.
- `infrastructure/wordpress/`: parseo del request, modificacion de queries y repositorios WordPress.
- `admin/`: controladores, vistas y assets del panel administrativo.
- `bootstrap/`: registro de dependencias, hooks y field groups.

## Verificacion

```bash
php -l wp-admin-posts-extended.php
php -l bootstrap/admin.php
php -l domain/PostCriteria.php
php -l infrastructure/wordpress/Request.php
php -l infrastructure/wordpress/AdminQueryModifier.php
php -l infrastructure/wordpress/WpPostRepository.php
php -l admin/AdminAssets.php
php -l admin/AdminFiltersController.php
php -l admin/AdminExportController.php
```
