<?php
/**
 * Plugin Name: WP Admin Posts Extended
 * Description: Gestion editorial para WordPress Admin con filtros avanzados, campos ACF, indicador de videos embebidos y exportacion Excel.
 * Plugin URI: https://github.com/hcardoso81/wp-admin-posts-extended
 * Author: Hernan Cardoso
 * Author URI: https://www.linkedin.com/in/cardosohernan/
 * Version: 2.3.1
 */

defined('ABSPATH') || exit;

if (!defined('WPM_PLUGIN_PATH')) {
    define('WPM_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

if (!defined('WPM_PLUGIN_VERSION')) {
    define('WPM_PLUGIN_VERSION', '2.3.1');
}

require_once __DIR__ . '/vendor/autoload.php';
require_once WPM_PLUGIN_PATH . 'bootstrap/admin.php';
