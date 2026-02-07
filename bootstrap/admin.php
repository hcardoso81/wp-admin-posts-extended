<?php

use WPAdminPostsExtended\Admin\AdminFiltersController;
use WPAdminPostsExtended\Admin\AdminExportController;

// Domain
require_once __DIR__ . '/../domain/PostCriteria.php';

// Infrastructure
require_once __DIR__ . '/../infrastructure/wordpress/AdminQueryModifier.php';
require_once __DIR__ . '/../infrastructure/wordpress/Request.php';

// Admin
require_once __DIR__ . '/../admin/AdminFiltersController.php';
require_once __DIR__ . '/../admin/AdminExportController.php';

add_action('plugins_loaded', function () {
    (new AdminFiltersController())->register();
    (new AdminExportController())->register();
});
