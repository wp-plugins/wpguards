<?php

defined('ABSPATH') OR exit; //prevent from direct access

/**
* WPGuards Tickets Table class
*/
class WPGBackups {

    public $backups;
    public $plan;
    public $time;

    function __construct() {

    }

    /**
     * Output backups in simple view
     * @return void
     */
    public function simple_backups() {
        global $WPGuards;
        
        $this->backups = $this->getBackups();

        if ( empty($this->backups) ) {
            
            echo __('You haven\'t any backups yet','wpguards');
            return;

        }

        krsort($this->backups);

        echo '<table id="simple_backups" cellspacing="0">';

            ?>
            <thead>
                <tr>
                    <th scope="col" id="backup"><?php _e('Backup','wpguards'); ?></th>
                    <th scope="col" id="size"><?php _e('Total size','wpguards'); ?></th>
                    <th scope="col" id="created"><?php _e('Created','wpguards'); ?></th>
                </tr>
            </thead>

            <tbody>
                <?php
                foreach ($this->backups as $backup) {

                    $name = $this->getBackupNameByPath( $backup['server']['file_path'] );

                    echo '<tr>';

                        echo '<td class="name"><abbr title="'.$name.'">'.ucfirst($backup['name']).'</abbr></td>';
                        echo '<td class="size">'.$backup['size'].'</td>';
                        echo '<td class="time">'.wpg_format_time($backup['time']).'</td>';

                    echo '</tr>';
                    
                }
                ?>
            </tbody>
            <?

        echo '</table>';

    }

    /**
     * Donwload and prepare backups
     * @return void
     */
    public function prepare_backups() {
        global $WPGuards;
		
        $this->plan = $WPGuards->WPGConnection->getSitePlan();
        $this->backups = $this->getBackups();
        $this->time = $WPGuards->WPGConnection->getNextBackupTime();
		
        krsort($this->backups);

    }

    /**
     * Display sidebar on the Backups page
     * @return void
     */
    public function display_backups_sidebar() {

    	if ( !empty($this->backups) ) :
    	?>
    	<div class="postbox informations">
            <h3><span><?php _e('Next backup','wpguards'); ?></span></h3>
            <div class="inside">

            	<?php $this->display_next_backup_info(); ?>

            </div>
        </div>

        <?php endif ; ?>

        <div class="postbox informations">
            <h3><span><?php _e('Informations','wpguards'); ?></span></h3>
            <div class="inside">

            	<?php $this->display_backups_info(); ?>

            </div>
        </div>
        <?php
    }

    /**
     * Diplay when next backup will be made
     * @return void
     */
    public function display_next_backup_info() {

		echo '<div id="next_backup">';

			/* translators: Context - In 3 days, 2 hour, 1 minute regarding when the next backup will be made */
			_e('In','wpguards');

            echo ' <acronym title="'.wpg_format_time($this->time).'">';
                echo seconds2human( $this->time - time() );
            echo '</acronym>';

        echo '</div>';

    }

    /**
     * Display informations about backups
     * @return void
     */
    public function display_backups_info() {
        global $WPGuards;

        echo '<div id="backup-warning">';
            _e('On cheap servers there might be a problem with restoring backups due to timeouts. In this case recommended is to download backup to your hard drive and upload it manually to the FTP.','wpguards');
        echo '</div>';

        switch ($this->plan) {

            case 'basic':
                _e('Currently you are using free version of WPGuards. Backups are made on a weekly basis and they are kept locally.','wpguards');
                echo '<br />';
                printf( __('%sUpgarde your version and do a backup <strong>every day!</strong>%s','wpguards'), '<a href="http://api.wpguards.com/login">', '</a>' );
                break;

            case 'standard':
                _e('Currently you are using standard version of WPGuards. Backups are made on a weekly basis and they are kept on the external Dropbox drive.','wpguards');
                echo '<br />';
                printf( __('%sUpgarde your version and do a backup <strong>every day!</strong>%s','wpguards'), '<a href="http://api.wpguards.com/login">', '</a>' );
                break;

            case 'professional':
                _e('Currently you are using premium version of WPGuards. Backups are made on a daily basis and they are kept on the external Dropbox drive.','wpguards');
                break;

        }

    }

    /**
     * Display backups table
     * @return void
     */
    public function display_backups() {
		global $WPGuards;

        if ( empty($this->backups) ) {
            
            echo '<div class="big-alert red">'.__('There is no backups available yet','wpguards').'</div>';
            echo '<div class="small-alert blue">';
            	_e('Next backup will be made within','wpguards');
	            echo ' <acronym title="'.wpg_format_time($this->time).'">';
	                echo seconds2human( $this->time - time() );
	            echo '</acronym>';
	        echo '</div>';
            return;
            
        }

        $this->display_restoring_infos();

        echo '<table class="wp-list-table widefat fixed backups" cellspacing="0">';

            $this->display_backups_header();

            $this->display_backups_content();

        echo '</table>';

    }

    /**
     * Hidden restoring informations for jQuery
     * @return void
     */
    public function display_restoring_infos() {
        ?>

        <div class="updated" id="request_send" style="display: none;"><p><?php _e('Backup request has been send. Please wait while your backup is being restored.','wpguards'); ?></p></div>

        <div class="updated" id="restored" style="display: none;"><p><?php _e('Backup successfully restored.','wpguards'); ?></p></div>

        <div class="error" id="wrong_noonce" style="display: none;"><p><?php _e('Wrong noonce! Do you really want to do that?','wpguards'); ?></p></div>

        <?
    }

    /**
     * Display backups table header
     * @return [type] [description]
     */
    public function display_backups_header() {
    	
    	?>
    	<thead>
			<tr>
                <th scope="col" id="backup" class="manage-column column-backup"><a><?php _e('Backup','wpguards'); ?></a></th>
                <th scope="col" id="size" class="manage-column column-size"><a><?php _e('Total size','wpguards'); ?></a></th>
                <th scope="col" id="link" class="manage-column column-link"><a><?php _e('Download','wpguards'); ?></a></th>
				<th scope="col" id="restore" class="manage-column column-restore"><a><?php _e('Restore','wpguards'); ?></a></th>
				<th scope="col" id="created" class="manage-column column-created sorted desc"><a><span><?php _e('Created','wpguards'); ?></span><span class="sorting-indicator"></span></a></th>
			</tr>
		</thead>
		<?
    }

    /**
     * Display backup table row
     * @return void
     */
    public function display_backups_content() {
    	global $WPGuards;
        ?>
        <tbody id="the-list" data-wp-lists="list:backup">
            <?php
            
			foreach ($this->backups as $backup) {

                $name = $this->getBackupNameByPath( $backup['server']['file_path'] );

                echo '<tr class="alternate">';

                    echo '<td class="backup column-backup"><abbr title="'.$name.'">'.ucfirst($backup['name']).'</abbr></td>'; //<div class="row-actions"><span class="view"><a href="#">'.__('View','wpguards').'</a></div></td>';
                    echo '<td class="size column-size">'.$backup['size'].'</td>';

                    echo '<td class="link column-link">';

                        $this->download_button($backup, $name);

                    echo '</td>';

                    echo '<td class="restore column-restore" id="restore_cell">';

                        echo '<form method="post" id="backup_restore">';
                            wp_nonce_field('request_backup_restore', 'request_backup_restore');
                            echo '<input type="hidden" id="file" name="file" value="'.$name.'" />';
                            echo '<input type="hidden" id="file_path" name="file_path" value="'.$backup['server']['file_path'].'" />';
                            echo '<input type="hidden" id="file_size" name="file_size" value="'.floatval($backup['size']).'" />';
                            echo '<input type="hidden" id="task" name="task" value="'.$backup['taskName'].'" />';
                            echo '<input type="hidden" id="result" name="result" value="'.$backup['resultID'].'" />';
                            echo '<input type="submit" class="button button-primary" name="submit_request_backup_restore" value="'.__('restore','wpguards').'" />';
                        echo '</form>';

                    echo'</td>';

					echo '<td class="created column-created">'.wpg_format_time($backup['time'] + $WPGuards->time_offset).'</td>';

                echo '</tr>';
                
            }
            ?>
        </tbody>
        <?
       
    }

    /**
     * Render backup download link
     * @param  array $backup backup
     * @param  string $name   backup name
     * @return void
     */
    public function download_button($backup, $name) {

        if ( filesize($backup['server']['file_path']) != 0 ) {
            echo '<a href="'.$backup['server']['file_url'].'" class="button button-primary">download</a>';
            return;
        }

        if ( ( $link = get_transient( 'wpg_backup_'.substr($name, -12) ) ) === false ) {
                        
            echo '<form method="post">';
                wp_nonce_field('request_backup_link', 'request_backup_link');
                echo '<input type="hidden" name="file" value="'.$name.'" />';
                echo '<input type="submit" class="button button-secondary" name="submit_request_backup_link" value="'.__('link request','wpguards').'" />';
            echo '</form>';
        
        } else {

            if (empty($link['link'])) {
                _e('Something goes wrong. Please contact support.','wpguards');
                return false;
            }

            echo '<a href="'.$link['link'].'" class="button button-primary">'.__('download','wpguards').'</a>';
            echo '<br />';
            echo '<span>'.sprintf(__('expires in %s', 'wpguards'), seconds2human( $link['expire'] - time() )).'</span>';

        }

    }
	
    /**
     * Gets backups from System
     * @return array backups
     */
	public function getBackups() {
		$backups = get_option("iwp_client_backup_tasks"); 
		//delete_option('iwp_client_backup_tasks');
        /*echo "<pre>z bazy "; 
        print_r($backups);
        echo "</pre>";
		*/ 
        if ( empty($backups) ) return array();

		$backupsItems = array(); 
		foreach((array) $backups as $task_name => $backupScheudle){
				
			if(key_exists('task_results', $backupScheudle)){
				foreach((array)$backupScheudle['task_results'] as $resultID => $backup){
	                $backup['taskName'] = $task_name; 
					$backup['name'] = (key_exists('type', $backupScheudle['task_args'])) ? $backupScheudle['task_args']['type'] : 'manual' ; 
					$backup['resultID'] = $resultID; 
					$backup['task_args'] = $backupScheudle['task_args'];
					if(isset($backup['backup_name']))
						$backupsItems[]=$backup; 
				}
			}

			//return $backupsItems;
		}; 
		return $backupsItems;
	}
	
    /**
     * Retrive backup name by backup path
     * @param  string $path backup path
     * @return string       backup name
     */
	public function getBackupNameByPath($path) {
        $nameArray = explode('/', $path); 
        $last = count($nameArray)-1;
        return $nameArray[$last];
    }

    /**
     * Gets backup link and saves it as a transient
     * @param  string $name backup name
     * @return string       backup link
     */
    public function get_backup_link($name) {
        global $WPGuards;
        
        $link = $WPGuards->WPGConnection->getBackupLink( $name );

        $expire = strtotime( $link['1']->date );

        $expire_in = $expire - time();

        $val = array( 
            'link' => $link['0'],
            'expire' => $expire
        );

        $transient = 'wpg_backup_'.substr($name, -12);

        return set_transient( $transient, $val, $expire_in );
    }

    /**
     * Prepares stats
     * 
     * @return void
     */
    public function prepare_backups_stats() { 

        if (empty($this->backups) || empty($this->time)) $this->prepare_backups();

        $size = array();
        foreach ($this->backups as $backup) {

            $size[] = floatval( $backup['size'] );

        }

        $counter = count($this->backups);

        $stats = array(
            'available_backups' => $counter,
            'average_size' => ($counter == 0) ? __('n/a', 'wpguards') : number_format( array_sum($size) / $counter, 2 ).' MB',
            'time' => $this->time
        );

        update_option( 'wpguards_backups_stats', $stats );

        return $stats;

    }

    /**
     * Restores backup
     * 
     * @return bool
     */
    public function restore_backup($post) { 

        $response = $WPGuards->WPGConnection->restoreBackup(array(
            'file_name' => $post['file'], 
            'taskName' => $post['task'], 
            'resultID' => $post['result'], 
        ));

        if ($response->status == 'success') {

            WPGAdmin::setNotice(32, 'updated', __('Backup succefully restored.','wpguards') );
            return true;

        } else {

            WPGAdmin::setNotice(33, 'error', __('Backup restoring error:','wpguards').' '.$response->errorMsg );
            return false;

        }

    }
    
}