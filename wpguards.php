<?php
/**
 * Plugin Name: WPGuards
 * Plugin URI: http://wpguards.com
 * Description: WordPress Support and Maintenance on Autopilot
 * Version: 1.2.0
 * Author: WordPressGeeks
 * Author URI: http://wordpressgeeks.co.uk
 * License: GPL2
 * 
 * Text Domain: wpguards
 */


/**
 * Define constants
 */
define( 'WPGUARDS', __FILE__ );
define( 'WPG_CLEAN_BACKUPS', false );
define( 'WPG_BACKUPS_DIR', WP_CONTENT_DIR.'/infinitewp/backups/' );
define( 'WPGUARDS_DIR', plugins_url( '/' , __FILE__ ) );
define( 'WPGUARDS_PATH', plugin_dir_path( __FILE__ ).'/' );
define( 'WPGUARDS_CORE_PATH', plugin_dir_path( __FILE__ ).'core/' );
define( 'WPGUARDS_MODULES_PATH', plugin_dir_path( __FILE__ ).'modules/' );
// assets urls
define( 'WPGUARDS_IMG', WPGUARDS_DIR.'assets/images/' );
define( 'WPGUARDS_JS', WPGUARDS_DIR.'assets/js/' );
define( 'WPGUARDS_CSS', WPGUARDS_DIR.'assets/css/' );
define( 'WPGUARDS_API', 'https://api.wpguards.com/api/v1/' );  


/**
 * Load IWP plugin if it's not active in other instance
 */
include_once(ABSPATH.'wp-admin/includes/plugin.php');
if ( !is_plugin_active('iwp-client/init.php') ) {
	$init_file = WPGUARDS_MODULES_PATH."manager/init.php"; 
	if( file_exists( $init_file ) ) require_once( $init_file );
}

 
/**
 * WPGuards main class
 */
class WPGuards {

	public $time_offset;
	public $connection;
	/**
	 * autoloader() makes all instances for included classes
	 * 
	 * @var /class_name/
	 */

	/**
	 * WPGuards Constructor
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		$this->time_offset = 3600 * get_option('gmt_offset');
		
		//Loading textdomain
		$this->load_plugin_textdomain();
		//Loading plugin functions
		$this->load_functions();
		//Require classes
		$this->require_classes();
		//Autoloader classes
		$this->autoloader();
		//Manual load Class
		$this->loadManualClass();

		// Manage user
		add_action('admin_init', array($this, 'manage_user'));

		// On first authorization make IWP install
		add_action('wpg_authorized', array($this, 'install'));
		
	}

	/**
	 * Load i18n files
	 *
	 * @access public
	 * @return void
	 */
	public function load_plugin_textdomain() {
		
		load_plugin_textdomain( 'wpguards', false, dirname( plugin_basename( __FILE__ ) ).'/languages/' );

	}

	/**
	 * Loads functions
	 *
	 * @access public
	 * @return void
	 */
	public function load_functions() {
		
    	require_once(WPGUARDS_PATH.'functions.php');

	}

	/**
	 * Require all classes
	 *
	 * @access public
	 * @return void
	 */
	public function require_classes() {
		
    	foreach ( glob( WPGUARDS_CORE_PATH."/class_*.php" ) as $file ) {
			require_once($file);
    	}

	}

	/**
	 * Loads manual class
	 * 
	 * @access private
	 * @return void
	 */
	private function loadManualClass(){
		$this->WPGConnection = new WPGConnection( $this );
	}
	
	/**
	 * Autoloader
	 * 
	 * Loads classes and makes instances
	 *
	 * @access public
	 * @return void
	 */
	public function autoloader() {
		
		$classes = array();

		// retrive all class files
		foreach ( glob( WPGUARDS_CORE_PATH."autoload/class_*.php" ) as $file ) {
			$class_name = substr( basename($file, '.php'), 6); // strip class name from class_
			$classes[$class_name]['path'] = $file;
    	}

    	// require files and make instances
    	foreach ($classes as $class => $val) {
    		require_once($val['path']);
    		
    		if (class_exists($class)) {
    			$this->$class = new $class;
    		}
    		
    	}

	}

	/**
	 * manage user plan
	 * @return void
	 */
	public function manage_user() {

		$transient = get_transient( 'wpg_check_plan' );
		if ( $transient === false && $this->WPGAdmin->options['crm_apikey'] != '' ) {
			
			$user_plan = $this->WPGConnection->getSitePlan();

			$old_plan = get_option('wpguards_user_plan', '');

	        if ( empty($old_plan) || $old_plan != $user_plan ) {

	            update_option( 'wpguards_user_plan', $user_plan );
	            // do_action('wpg_authorized');
	            
	        }

	        // Set currency
	        if ( $this->WPGAdmin->options['crm_apikey'] != '' && !get_option('wpguards_currency') ) {
					
				$currency = $this->WPGConnection->getUserCurrency();

				update_option( 'wpguards_currency', $currency );

			}

			set_transient('wpg_check_plan', true, HOUR_IN_SECONDS );
		}

	}
	
	/**
	 * Install activation hook
	 */
	 public function install() {

	 	if ( is_plugin_active('iwp-client/init.php') ) {
			
	 		deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( __( 'Sorry, you need to delete IWP Client plugin first', 'wpguards' ) );

		}
	 	
	 	$install = new WPGInstall( $this ); 
		$install->install();
	 }
	
	/**
	 * Uninstall hooks
	 */
	 public function deactivate(){
	 	$deactivate = new WPGInstall( $this ); 
		$deactivate->deactivate();
	 }
}

$GLOBALS['WPGuards'] = new WPGuards();

if (function_exists('register_activation_hook'))
    register_activation_hook( __FILE__ , array( $GLOBALS['WPGuards'], 'install' )); 

if (function_exists('register_deactivation_hook'))
    register_deactivation_hook(__FILE__, array( $GLOBALS['WPGuards'], 'deactivate' ));