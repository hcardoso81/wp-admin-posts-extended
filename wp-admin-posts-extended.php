<?php
/**
 * Plugin Name: WP Admin Posts Extended
 * Description: Filtros avanzados por etiquetas y autor, compatibilidad con filtros nativos y exportacion Excel con estado de LinkedIn.
 * Plugin URI: https://github.com/hcardoso81/wp-admin-posts-extended
 * Author: Hernan Cardoso
 * Author URI: https://www.linkedin.com/in/cardosohernan/
 * Version: 1.2.0
 */

defined('ABSPATH') || exit;

if (!defined('WPM_PLUGIN_PATH')) {
    define('WPM_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

if (!defined('WPM_PLUGIN_VERSION')) {
    define('WPM_PLUGIN_VERSION', '1.2.0');
}

require_once __DIR__ . '/vendor/autoload.php';
require_once WPM_PLUGIN_PATH . 'bootstrap/admin.php';
