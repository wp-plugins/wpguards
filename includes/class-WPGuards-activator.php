<?php
/**
 * Fired during plugin activation
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/includes
 */

class WPGuards_Activator {

    /**
     * method is fired during plugin activation
     *
     * @since    1.0.0
     */
    public static function activate( $plugin_file ) {

        if ( ! function_exists('curl_version') ) {
            \deactivate_plugins( plugin_basename( $plugin_file ) );
            \wp_die( sprintf( __( '%s plugin requires CURL library. Please contact your host provider.', 'wpguards' ) ), WPGUARDS_NAME );
        }

        if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
            \deactivate_plugins( plugin_basename( $plugin_file ) );
            \wp_die( sprintf( __( '%s plugin requires at least PHP 5.3.0!', 'wpguards' ) ), WPGUARDS_NAME );
        }

        // safety check
        if (is_plugin_active('iwp-client/init.php')) {
            
            deactivate_plugins(plugin_basename(__FILE__ ));
            wp_die( __( 'Sorry, you need to delete IWP Client plugin first', 'wpguards') );

        }

        $settings = get_option('wpguards_settings');
        $publicKey = get_option('iwp_client_public_key');

        // Check IWP settings
        $iwp_mmb_core = new IWP_MMB_Core(); 

        // if API key was provided get IWP client activation key.
        $activationKey = get_option('iwp_client_activate_key');

        // 1.3.7 to 2.0 update
        if ($publicKey != false && isset($settings['crm_apikey']) && !empty($settings['crm_apikey'])) {
            update_option('wpguards_connected', true);
            return;
        }
        
        // If it does not exist, create new one.
        if(!$activationKey || $activationKey == '') {
            $iwp_mmb_core->install();
        }

        // if api key does not exist
        if (!isset($settings['crm_apikey']) || empty($settings['crm_apikey'])) {
            $message = sprintf( __( 'Please insert valid %s API key', 'wpguards'), WPGUARDS_NAME );

            WPGuards_Admin::setGlobalNotice(1000, 'error', $message);
        }

    }

}
