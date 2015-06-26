<?php
/**
 * Fired during plugin deactivation
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/includes
 */

class WPGuards_Deactivator {

    /**
     * method is fired during plugin deactivation
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        $iwp_mmb_core = new IWP_MMB_Core(); 

        $iwp_mmb_core->uninstall();

        delete_option('wpguards_notice');
        delete_option('wpguards_global_notice');

        delete_option('iwp_client_backup_tasks');
        delete_option('iwp_client_user_hit_count');

        //delete_option('wpguards_settings');
        //delete_option('wpguards_connected');

        //delete_option('wpguards_widget_options');
        //delete_option('wpguards_currency');
        //delete_option('wpguards_tickets_stats');
        //delete_option('wpguards_backups_stats');
        //delete_option('wpguards_user_plan');
        //delete_option('wpg_authorized');
        //delete_option('wpguards_apikey_changed');
        //delete_option('wpg_next_scan_timestamp');

        //delete_transient('wpg_check_plan');
    }

}
