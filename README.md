# WP Admin Posts Extended

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![WordPress](https://img.shields.io/badge/WordPress-21759B?style=for-the-badge&logo=wordpress&logoColor=white)
![Composer](https://img.shields.io/badge/Composer-885630?style=for-the-badge&logo=composer&logoColor=white)
![PhpSpreadsheet](https://img.shields.io/badge/PhpSpreadsheet-217346?style=for-the-badge&logo=microsoftexcel&logoColor=white)
![ACF](https://img.shields.io/badge/ACF-00A651?style=for-the-badge&logo=wordpress&logoColor=white)
![Select2](https://img.shields.io/badge/Select2-1F6FEB?style=for-the-badge&logo=jquery&logoColor=white)

Plugin personalizado para WordPress que mejora la gestion editorial del listado administrativo de posts con filtros avanzados, campos ACF, columnas personalizadas y exportacion a Excel.

## Funcionalidades

- Filtro multiple por etiquetas con Select2.
- Filtro por autor integrado al listado de posts.
- Compatibilidad con filtros nativos de WordPress: categoria, fecha y busqueda.
- Exportacion de los resultados filtrados a `.xlsx`.
- Columna de estado de LinkedIn en el Excel con soporte para `Pendiente`, `Publicado`, `Publicado manualmente` y `Programado para publicar`.
- Campo editorial `Fuente` mediante ACF para clasificar posts como `Nota original` o `Comunicado de prensa`.
- Campo editorial `hasVideo` mediante ACF para marcar posts que contienen videos.
- Columna `Contiene Videos` en el listado administrativo de posts.
- Columna `Contiene Videos` en la exportacion Excel.

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
php -l admin/AdminPostColumnsController.php
```
