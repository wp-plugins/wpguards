<?php

defined('ABSPATH') OR exit; //prevent from direct access

/**
* WPGuards Install class
*/

class WPGInstall{
	
	
	public $WPGuards;
	
	/**
	 * construct
	 */
	 
	
	public function __construct( $WPGuards ){
		$this->WPGuards = $WPGuards;	

	}
	
	/**
	 * Installs IWP client and configures Dashoard widget
	 * @return void
	 */
	public function install(){
		
		$this->iwp_install();
		$this->dashboard_config();

		// Set first check scan time
		$malware = new WPGMalware();
		$malware->get_next_scan_time();

	}
	
	/**
	 * Installs IWP client
	 * @return void
	 */
	public function iwp_install() {
		global $iwp_mmb_core; 

		if ( $this->WPGuards->WPGAdmin->options['crm_apikey'] != '' ) {

			$activationKey = get_option('iwp_client_activate_key');
			
			//Check is IWP install. 
			if( $activationKey != '') {
				$iwp_mmb_core->install();
			}

		} else {

			WPGAdmin::setGlobalNotice(1000, 'error', sprintf( __( 'Insert WPGuards API key %shere%s', 'wpguards' ), '<a href="admin.php?page=wpguards_settings">', '</a>' ));

		}

	}

	/**
	 * Configures dashboard plugin widget
	 * @return void
	 */
	public function dashboard_config() {

		if ( !get_option('wpguards_widget_options') ) {
			
			$opt = array(
				'display_news' => 1, 
				'display_analytics' => 1, 
				'display_support' => 1, 
				'display_backups' => 1, 
			);

			update_option( 'wpguards_widget_options', $opt );

		}

	}

	/**
	 * On plugin deactivation - remove notices
	 * @return void
	 */
	public function deactivate(){
		delete_option('WPG_admin_notices');
		delete_option('WPG_global_admin_notice');
	}
} 
