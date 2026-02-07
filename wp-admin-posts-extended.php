<?php
/**
 * Plugin Name: WP Admin Posts Extended
 * Description: Filtros avanzados y exportación de posts en el admin.
 * Plugin URI: https://github.com/hcardoso81/wp-admin-posts-extended
 * Author: Hernan Cardoso
 * Author URI: https://www.linkedin.com/in/cardosohernan/
 * Version: 0.1.0
 */


defined('ABSPATH') || exit;

define('WPM_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WPM_PLUGIN_VERSION', '1.0.0');

require_once WPM_PLUGIN_PATH . 'bootstrap/admin.php';
