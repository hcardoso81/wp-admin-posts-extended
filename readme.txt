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
|       |-- year-filter.php
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
- Filtro dinamico por año desde 2016 hasta el año actual.
- Compatibilidad con filtros nativos de categoria, fecha y busqueda.
- Exportacion Excel de los posts filtrados.
- Fecha de publicacion en el Excel con formato dd/mm/yyyy.
- Version actual: 2.5.3.
- Exportacion del estado de LinkedIn como texto: Pendiente, Publicado, Publicado manualmente o Programado para publicar.
- Campo editorial Fuente mediante ACF.
- Campo editorial hasVideo mediante ACF para marcar posts con videos de YouTube embebidos.
- Columna compacta de videos al final del listado administrativo, con icono, tooltip y estados visuales ✓/×; tambien se incluye en la exportacion Excel.
- Herramienta temporal tools/backfill-has-video.php para completar hasVideo en posts publicados desde el 1 de enero de 2025 hasta hoy, con simulacion detallada de los posts detectados.

Nota de seguridad:
- Configurar allowlist de IP antes de subir la herramienta.
- Ejecutarla con usuario administrador, primero en modo simulacion para revisar ID, fecha, titulo, estado hasVideo y enlace de edicion de cada post detectado.
- Borrar tools/backfill-has-video.php del servidor despues de usarla.

Comando para generar zip desde Git:

git archive --format=zip --prefix=wp-admin-posts-extended/ --output=wp-admin-posts-extended.zip HEAD
