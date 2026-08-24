<?php
/**
 * Plugin Name: WP Admin Posts Extended
 * Description: Gestion editorial para WordPress Admin con filtros por etiquetas, autor y ano, campos ACF, videos embebidos, exportacion Excel e indicadores visuales.
 * Plugin URI: https://github.com/hcardoso81/wp-admin-posts-extended
 * Author: Hernan Cardoso
 * Author URI: https://www.linkedin.com/in/cardosohernan/
 * Version: 2.5.3
 */

defined('ABSPATH') || exit;

if (!defined('WPM_PLUGIN_PATH')) {
    define('WPM_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

if (!defined('WPM_PLUGIN_VERSION')) {
    define('WPM_PLUGIN_VERSION', '2.5.3');
}

require_once __DIR__ . '/vendor/autoload.php';
require_once WPM_PLUGIN_PATH . 'bootstrap/admin.php';
