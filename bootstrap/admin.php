<?php

use WPAdminPostsExtended\Admin\AdminFiltersController;

add_action('plugins_loaded', function () {
    if (is_admin()) {
        (new AdminFiltersController())->register();
    }
});
