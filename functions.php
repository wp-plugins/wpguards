<?php

defined('ABSPATH') OR exit; //prevent from direct access

add_action('admin_init', 'wpg_backup_request', 10);
/**
 * Handle backup link/restore request
 * @return void 
 */
function wpg_backup_request() {
    global $WPGuards;
    $post = $_POST;

    if ( isset($post['submit_request_backup_link']) && check_admin_referer('request_backup_link','request_backup_link') ) {

        WPGBackups::get_backup_link( $post['file'] );

    }

    if ( isset($post['submit_request_backup_restore']) && check_admin_referer('request_backup_restore','request_backup_restore') ) {

        // add_action('wpg_backup_restore_request', 'wpg_restore_backup', 10);

    }

}

add_action( 'wp_ajax_restore_backup', 'wpg_restore_backup' );
/**
 * Restore the backup
 * @return void 
 */
function wpg_restore_backup() {
    global $WPGuards;

    if( wp_verify_nonce( $_REQUEST['nonce'], 'request_backup_restore' ) ) {
        
        $response = $WPGuards->WPGConnection->restoreBackup(array(
            'file_name' => $_REQUEST['file'],
            'taskName' => $_REQUEST['task'],
            'resultID' => $_REQUEST['result'],
        ));

        if ($response->status == 'success') die('done');
        else die( __('Restoring error','wpguards').': '.$response->errorMsg );
        
    } else {
    
        die('noonce');
        
    } // end if 

}

add_action( 'wp_ajax_remove_scan', 'wpg_remove_scan' );
/**
 * Remove malvare scan
 * @return void
 */
function wpg_remove_scan() {
    global $WPGuards;

    if ( !wp_verify_nonce( $_REQUEST['noonce'], 'scan-remove' ) ) {

        _e('Wrong noonce. Do you really want to do that?', 'wpguards');
        die(); 

    } else {

        $scans = get_option( 'wpg_scans', array() );

        unset($scans[$_REQUEST['id']]);

        update_option( 'wpg_scans', $scans );

        echo 'done';

        die();

    }

    

}

add_action('admin_init', 'wpg_submit_handler', 10);
/**
 * Handles submited ticket forms
 * 
 * @return void
 */
function wpg_submit_handler() {
    global $WPGuards;

    $post = $_POST;

    if ( isset($post['submit-ticket']) && check_admin_referer('wpg_ticket_request','wpg_new_ticket') ) {
        //if ticket was send

        $data = array(
            'subject' => esc_attr($post['subject']),
            'comment' => esc_attr($post['description']),
            'type' => esc_attr($post['type'])
        );

        $response = $WPGuards->WPGConnection->addTicket($data);

        wpg_support_ticket_notices($post, $response);

    } elseif ( isset($post['submit-comment']) && check_admin_referer('wpg_comment_request','wpg_new_comment') ) {
        //if comment was send

        $data = array(
            'ticketId' => esc_attr($post['ticket']), 
            'comment' => esc_attr($post['comment']), 
        );

        $response = $WPGuards->WPGConnection->addTicketComment($data);

        wpg_support_comment_notices($post, $response);

    } elseif ( (isset($post['submit-ticket']) && !check_admin_referer('wpg_ticket_request','wpg_new_ticket')) || (isset($post['submit-comment']) && check_admin_referer('wpg_comment_request','wpg_new_comment')) ) {
        //if nothing was send

        WPGAdmin::setNotice(3, 'error', __('Sorry, your nonce did not verify','wpguards') );

    }

}

/**
 * Checks the API ticket response and handle it
 * 
 * @return void
 */
function wpg_support_ticket_notices($post, $response) {

    $errors = '';

    if ( empty($post['subject']) ) {
        $errors .= __('Subject cannot be blank','wpguards').'<br />';
    } elseif ( empty($post['description']) ) {
        $errors .= __('Description cannot be blank','wpguards').'<br />';
    }

    if ( !empty($errors) ) {

        WPGAdmin::setNotice(5, 'error', $errors );
        return $response->details->base;

    }

    if ($response == 1)
        WPGAdmin::setNotice(4, 'updated', __('Your ticket was send','wpguards') );

    return 1;
}

/**
 * Checks the API comment response and handle it
 * 
 * @return void
 */
function wpg_support_comment_notices($post, $response) {

    $errors = '';

    if ( empty($post['comment']) ) {
        WPGAdmin::setNotice(5, 'error', __('Comment cannot be blank','wpguards') );
        return $response->details->base;
    }

    WPGAdmin::setNotice(7, 'updated', __('Your comment was send','wpguards') );

    return 1;
}


/**
 * Checks if the API key was provided
 * 
 * @return bool
 */
function wpg_check_apikey() {

    // get the options from global plugin instance
    /*global $WPGuards;
    $opt = $WPGuards->WPGAdmin->options;

    if ( empty($opt['crm_apikey']) || !$WPGuards->WPGConnection->checkApiKey() )
        return false;*/

    return true;
}

/**
 * Displays no API key message
 * 
 * @return void
 */
function wpg_no_apikey() {
?>
	<div class="big-alert red"><?php _e('Please provide your API key in order to access this page','wpguards'); ?></div>
    <div class="small-alert blue"><?php echo '<a href="'.admin_url('admin.php?page=wpguards_settings').'">'.__( 'Go to the settings tab', 'wpguards' ).'</a>'; ?></div>
<?php
}

/**
 * Render icon
 * @param  string $slug icon name
 * @param  string $size additional size param
 * @return void
 */
function wpg_render_icon($slug, $size = 'icon-2x') {

    switch ($slug) {
    	case 'new':
    		$icon =  '<i class="icon-star-empty '.$size.'" title="'.__('new','wpguards').'"></i>';
    		break;

    	case 'open':
    		$icon =  '<i class="icon-circle-blank '.$size.'" title="'.__('open','wpguards').'"></i>';
    		break;

    	case 'pending':
    		$icon =  '<i class="icon-time '.$size.'" title="'.__('pending','wpguards').'"></i>';
    		break;

    	case 'solved':
            $icon =  '<i class="icon-ok '.$size.'" title="'.__('solved','wpguards').'"></i>';
            break;

        case 'unpaid':
            $icon =  '<i class="icon-money '.$size.'" title="'.__('solved - unpaid','wpguards').'"></i>';
            break;

        case 'closed':
    		$icon =  '<i class="icon-ok '.$size.'" title="'.__('closed','wpguards').'"></i>';
    		break;

    	case 'problem':
    		$icon =  '<i class="icon-bug '.$size.'" title="'.__('problem','wpguards').'"></i>';
    		break;

    	case 'incident':
    		$icon =  '<i class="icon-exclamation '.$size.'" title="'.__('incident','wpguards').'"></i>';
    		break;

    	case 'task':
    		$icon =  '<i class="icon-tasks '.$size.'" title="'.__('quote','wpguards').'"></i>';
    		break;

    	case 'question':
    		$icon =  '<i class="icon-question '.$size.'" title="'.__('question','wpguards').'"></i>';
    		break;
    	
    	default:
    		break;
    };

    return $icon;

}

/**
 * Creates ticket link
 * 
 * @return string
 */
function wpg_create_ticket_link($ticket) {

    $title = wp_trim_words( $ticket['subject'], 7 );

    $url = add_query_arg(array(
            'ticket' => $ticket['id'], 
            'ticket_title' => urlencode($title),
            'closed' => ($ticket['status'] == 'closed' || $ticket['paid']) ? 1 : 0
        ));

    $link = '<a href="'.$url.'">'.$ticket['subject'].'</a>';

    return $link;
}

/**
 * Shows notices
 * 
 * @return void
 */
add_action('admin_notices', 'WPG_admin_notices');
function WPG_admin_notices() {

  if ($notices= get_option('WPG_admin_notices')) {
    foreach ($notices as $notice) {
        // echo "<div class='".$notice['type']."'><p>".$notice['message']."</p></div>";
    }
    delete_option('WPG_admin_notices');
  }
  
  if($notices = get_option('WPG_global_admin_notice') ){
    foreach ($notices as $notice) {
        // echo "<div class='".$notice['type']."'><p>".$notice['message']."</p></div>";
    }
  }

}

/**
 * Formats price
 * 
 * @return string price
 */
function wpg_format_price($price) {

    return $price.' '.get_option('wpguards_currency', 'USD');

}

add_action('admin_init', 'wpg_tickets_paid', 10);
/**
 * Caches return after success payment
 * 
 * @return void
 */
function wpg_tickets_paid() {
    global $WPGuards;

    if ( isset($_GET['paid']) )
        WPGAdmin::setNotice(11, 'updated', __('Thanks for payment','wpguards') );

}


add_action('iwp_after_backup', 'wpg_task_after_backup', 10, 4 ); 
/**
 * Sending backup to dropbox
 * @param  string $backupLink backup link
 * @param  array $info
 * @param  string $paths      path to backup file
 * @param  string $taskName   task name
 * @return void
 */
function wpg_task_after_backup($backupLink, $info, $paths, $taskName){
		global $WPGuards; 
		$toSend = array(
				'backup_link' => $backupLink, 
				'backups_info' =>  WPGBackups::getBackups(),
				'current_backup' => $paths,
				'task_name' => $taskName
			);
		if($WPGuards->WPGConnection->whereGoBackup( $toSend ) == 'dropbox'){
			
			iwp_mmb_auto_print('zip_archive_backup');		
			$response = $WPGuards->WPGConnection->sendBackup( $toSend ); 
			
			//Check if backup is sent to remote service, important if You change this you backup will disappear 
			if( $response == 1 ){
				@unlink( $paths['server']['file_path']) ; 
				$deamonFile = fopen($paths['server']['file_path'],'w'); 
				fclose($deamonFile);
			} 
		}
		
}


/**
 * Formats date and time
 * 
 * @return string date and time
 */
function wpg_format_time($time) {
    global $WPGuards;

    return date( "j.n.Y, G:i", $time + $WPGuards->time_offset );
}

/**
 * Gets plugin data
 * 
 * @return array plugin data
 */
function about_plugin() {
    return get_plugin_data( WPGUARDS );
}

/**
 * Generates order link
 * 
 * @return string url
 */
function order_link($plan = false) {

    return 'http://api.wpguards.com/panel';

    if ($plan) {
        return '#'.$plan;
    } else {
        return '#';
    }
    

}

/**
 * Generates order button
 * 
 * @return string anchor
 */
function order_button( $plan, $text, $style='button-primary' ) {

    return '<a href="'.order_link($plan).'" class="button '.$style.'">'.$text.'</a>';

}

/**
 * Changes seconds to d:h:m:s
 * 
 * @return string time
 */
function seconds2human($seconds) {

    $days    = floor($seconds / 86400);
    $hours   = floor(($seconds - ($days * 86400)) / 3600);
    $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60);
    $seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)));

    $string = ($days) ? $days.' '.sprintf(_n('day', 'days', $days, 'wpguards'), $days).', ' : '';
    $string .= ($hours) ? $hours.' '.sprintf(_n('hour', 'hours', $hours, 'wpguards'), $hours).', ' : '';
    $string .= ($minutes) ? $minutes.' '.sprintf(_n('minute', 'minutes', $minutes, 'wpguards'), $minutes).', ' : '';
    $string .= ($seconds) ? $seconds.' '.sprintf(_n('second', 'seconds', $seconds, 'wpguards'), $seconds) : '';

    return $string;
} 

add_action('admin_init', 'wpg_clean_backups', 10);
/**
 * Cleans backups array
 * @return boolean
 */
function wpg_clean_backups() {
    
    if ( !WPG_CLEAN_BACKUPS )
        return false;

    delete_option( 'iwp_client_backup_tasks' );

    foreach ( glob(WPG_BACKUPS_DIR."*.zip") as $file ) {
        unlink($file);
    }

    return true;
}

add_filter('wpg_before_backup', 'wpg_download_file', 10 ,2);
/**
 * Download backup file
 * @param  array $args  arguments
 * @param  array $tasks tasks
 * @return array        downloaded file array
 */
function wpg_download_file($args, $tasks) {
 global $WPGuards;
 //Wery important thing !!!!!!!!!  
  $file_name = WPGBackups::getBackupNameByPath($tasks[$args['task_name']]['task_results'][$args['result_id']]['server']['file_path']); 
  if($WPGuards->WPGConnection->whereIsBackup($file_name) == 'dropbox'){
	  $task  = $tasks[$args['task_name']];
	  $url = $args['backup_url'];
	  $newfname = $task['task_results'][$args['result_id']]['server']['file_path'];;
	  $file = fopen ($url, "rb");
	  if ($file) {
	    $newf = fopen ($newfname, "wb");
	 
	    if ($newf)
	    while(!feof($file)) {
	      fwrite($newf, fread($file, 2048 * 8 ), 2048 * 8 );
	    }
	  }
	
	  if ($file) {
	    fclose($file);
	  }
	
	  if ($newf) {
	    fclose($newf);
	  }
	  
	  unset($args['backup_url']);
	  return $args; 
	 }
  unset($args['backup_url']);
  return $args; 
 }

add_action('wp_head', 'wpg_add_uptimemonitorkeywords');
/**
 * Adds Uptime Monitor mete to check if website is down
 * @return void
 */
function wpg_add_uptimemonitorkeywords() {
    ?>
    <!-- WPGuards Uptime Monitor Info -->
    <meta data-details="23ncu2bX3" />
    <!-- END WPGuards Uptime Monitor Info -->
    <?php
}

/**
 * Prints an array in readable way
 * @param  array $a array
 * @return void
 */
function wpg_pr($a) {
    echo "<pre>";
    print_r($a);
    echo "</pre>";
}

/**
 * Prints array to log file
 * @param  array $a array
 * @return void
 */
function pr2file($a) {
    $file = WPGUARDS_PATH.'/log.txt';
    $current = file_get_contents($file);
    file_put_contents($file, date('m.d.y H:i:s')."\n".print_r($a, true)."\n\n\n".$current);
}

?>