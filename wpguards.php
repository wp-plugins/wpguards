<?php
/**
 * WPguards 
 * 
 * @link         http://wpguards.com
 * @package      WPGuards
 * @author       wpguards.com
 *
 * @wordpress-plugin
 * Plugin Name:  WPGuards
 * Plugin URI:   http://wpguards.com
 * Description:  WordPress Support and Maintenance on Autopilot
 * Version:      2.0.4
 * Author:       WPGeeks
 * Author URI:   http://wpgeeks.co.uk
 * License:      GPL2
 * Text Domain:  wpguards
 */

if (!defined('ABSPATH')) {
    exit; // prevent from direct access
}

/**
 * Plugin config
 */

define('WPGUARDS_VERSION', '2.0.4');
define('WPGUARDS_NAME', __('WPGuards', 'wpguards'));
define('WPGUARDS_DIR', plugin_dir_path(__FILE__));
define('WPGUARDS_URL', plugin_dir_url(__FILE__));
define('WPGUARDS_DEBUG', false);

if ( defined( 'WPGUARDS_DEBUG' ) && WPGUARDS_DEBUG ) {
	define('API_URL', 'http://api-dev.wpguards.com');
} else {
	define('API_URL', 'https://api.wpguards.com');
}

/**
 * Require plugin important dependencies
 */

require WPGUARDS_DIR . 'dependencies/manager/init.php'; 

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_WPGuards() {
    require_once WPGUARDS_DIR . 'includes/class-WPGuards-activator.php';
    WPGuards_Activator::activate( __FILE__ );
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_WPGuards() {
    require_once WPGUARDS_DIR . 'includes/class-WPGuards-deactivator.php';
    WPGuards_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_WPGuards');
register_deactivation_hook(__FILE__, 'deactivate_WPGuards');

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require WPGUARDS_DIR . 'includes/class-WPGuards.php';

// Begins execution of the plugin.
new WPGuards();