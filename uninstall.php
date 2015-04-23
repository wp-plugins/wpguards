<?php
	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    	exit();

	require_once('modules/manager/helper.class.php');
	require_once('modules/manager/core.class.php');
	
	$iwp_mmb_core = new IWP_MMB_Core();
	$iwp_mmb_core->uninstall();

	delete_option('iwp_client_backup_tasks');
	delete_option('iwp_client_user_hit_count');

	delete_option('WPG_admin_notices');
	delete_option('WPG_global_admin_notice');

	delete_option('wpguards_widget_options');
	delete_option('wpguards_currency');
	delete_option('wpguards_tickets_stats');
	delete_option('wpguards_backups_stats');
	delete_option('wpguards_settings');
	delete_option('wpguards_user_plan');
	delete_option('wpg_authorized');
	delete_option('wpguards_apikey_changed');
	delete_option('wpg_next_scan_timestamp');

	delete_transient('wpg_check_plan');
?>