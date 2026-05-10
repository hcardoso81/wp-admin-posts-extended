wp-admin-posts-extended/
|
|-- admin/
|   |-- AdminFiltersController.php
|   |-- AdminExportController.php
|   |-- AdminAssets.php
|   |-- AdminPostColumnsController.php
|   |
|   |-- views/
|       |-- tag-filter.php
|       |-- author-filter.php
|       |-- export-button.php
|
|-- domain/
|   |-- PostCriteria.php
|   |-- PostRepositoryInterface.php
|
|-- infrastructure/
|   |-- wordpress/
|       |-- WpPostRepository.php
|       |-- AdminQueryModifier.php
|       |-- Request.php
|
|-- bootstrap/
|   |-- admin.php
|
|-- wp-admin-posts-extended.php

Funcionalidades:
- Filtro multiple por etiquetas mediante Select2.
- Filtro por autor mediante dropdown de usuarios con capacidad edit_posts.
- Compatibilidad con filtros nativos de categoria, fecha y busqueda.
- Exportacion Excel de los posts filtrados.
- Exportacion del estado de LinkedIn como texto: Pendiente, Publicado, Publicado manualmente o Programado para publicar.
- Campo editorial Fuente mediante ACF.
- Campo editorial hasVideo mediante ACF para marcar posts con videos de YouTube embebidos.
- Columna Contiene Videos al final del listado administrativo y en la exportacion Excel.
- Herramienta temporal tools/backfill-has-video.php para completar hasVideo en posts historicos publicados hasta el 1 de enero de 2025.

Nota de seguridad:
- Configurar allowlist de IP antes de subir la herramienta.
- Ejecutarla con usuario administrador, primero en modo simulacion.
- Borrar tools/backfill-has-video.php del servidor despues de usarla.

Comando para generar zip desde Git:

git archive --format=zip --prefix=wp-admin-posts-extended/ --output=wp-admin-posts-extended.zip HEAD
