<?php

use WPAdminPostsExtended\Admin\AdminFiltersController;
use WPAdminPostsExtended\Admin\AdminExportController;
use WPAdminPostsExtended\Admin\AdminAssets;
use WPAdminPostsExtended\Admin\AdminPostColumnsController;


// Domain
require_once __DIR__ . '/../domain/PostCriteria.php';
require_once __DIR__ . '/../domain/PostRepositoryInterface.php';

// Infrastructure
require_once __DIR__ . '/../infrastructure/wordpress/AdminQueryModifier.php';
require_once __DIR__ . '/../infrastructure/wordpress/Request.php';
require_once __DIR__ . '/../infrastructure/wordpress/WpPostRepository.php';

// Admin
require_once __DIR__ . '/../admin/AdminAssets.php';
require_once __DIR__ . '/../admin/AdminFiltersController.php';
require_once __DIR__ . '/../admin/AdminExportController.php';
require_once __DIR__ . '/../admin/AdminPostColumnsController.php';


/**
 * Registro de controladores admin
 */
add_action('plugins_loaded', function () {
    (new AdminAssets())->register();
    (new AdminFiltersController())->register();
    (new AdminExportController())->register();
    (new AdminPostColumnsController())->register();
});

/**
 * Registro de campos ACF
 */
add_action('acf/init', function () {

    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key' => 'group_wpape_fuente',
        'title' => 'Fuente',
        'fields' => [
            [
                'key' => 'field_wpape_fuente',
                'label' => 'Fuente',
                'name' => 'fuente',
                'type' => 'radio',
                'choices' => [
                    'nota_original' => 'Nota original',
                    'comunicado_prensa' => 'Comunicado de prensa',
                ],
                'default_value' => 'comunicado_prensa',
                'layout' => 'vertical',
                'return_format' => 'value',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ],
            ],
        ],
        'position' => 'side',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
    ]);

    acf_add_local_field_group([
        'key' => 'group_wpape_has_video',
        'title' => 'Contiene Videos',
        'fields' => [
            [
                'key' => 'field_wpape_has_video',
                'label' => 'Contiene Videos',
                'name' => 'hasVideo',
                'type' => 'checkbox',
                'choices' => [
                    '1' => 'Contiene videos',
                ],
                'default_value' => [],
                'layout' => 'vertical',
                'return_format' => 'value',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ],
            ],
        ],
        'position' => 'side',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
    ]);
});
