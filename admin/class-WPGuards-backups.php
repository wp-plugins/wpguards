<?php
/**
 * Backups page for WPGuards
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/admin
 */

class WPGuards_Backups {

    /**
     * WPGuards instance
     *
     * @since   2.0
     * @access  private
     * @var     WPGuards
     */
    private $WPGuards;

    /**
     * WPGuards instance
     *
     * @since   2.0
     * @access  private
     * @var     WPGuards
     */
    private $options;

    /**
     * Initialize the class and set its properties.
     *
     * @access public
     * @since  2.0
     * @param  WPGuards  $WPGuards  WPGuards instance.
     * 
     * @return this
     */
    public function __construct(WPGuards $WPGuards) {

        $this->WPGuards = $WPGuards;
        $this->options  = get_option('wpguards_settings');

        $this->setBackupsTransient();

        // handle form
        add_action('admin_init', array($this, 'process'));

        // add submenu/menu page
        add_action('admin_menu', array($this, 'addOptionPage'));
        

        return $this;

    }

    public function setBackupsTransient() 
    {   
        if (!get_option('wpguards_connected', false)) {
            return;
        }

        // by default backups are stored in transient
        $backups = get_transient('wpguards_backups');

        if ($backups !== false) {
            return $backups;
        }  

        $response = WPGuards_Curl::fetch('backup/getBackups');

        if ($response->status == 'success') {
            // transient expires after 4 hours
            $transientTime = 14400;

            if (isset($response->data->backups)) {
                if (is_object($response->data->backups) && !empty($response->data->backups)) {
                    foreach ($response->data->backups as $backup) {
                        
                        if (!isset($backup->location)) {
                            continue;
                        }

                        if ($backup->location != 'dropbox') {
                            continue;
                        }

                        $file = WP_CONTENT_DIR . '/infinitewp/backups/' . $backup->fileName;

                        if (!file_exists($file)) {
                            continue;
                        }

                        // after 4 hours its safe to delete file
                        if ((intval($backup->time) + (4 * 60 * 60)) > time()) {
                            continue;
                        }
                        
                        @unlink($file); 
                        $f = fopen($file, 'w'); 
                        fclose($f);
                    }

                }

                set_transient('wpguards_backups', $response->data->backups, $transientTime);
            }

            if (isset($response->data->nextBackup)) {
                set_transient('wpguards_nextBackup', $response->data->nextBackup, $transientTime);
            }
        }
    }


    /**
     * Process sent forms
     *
     * @access public
     * @since  2.0
     * 
     * @return void
     */
    public function process()
    {
        if (!isset($_GET['page'])) {
            return;
        }

        if ($_GET['page'] != 'wpguards_backups') {
            return;
        }

        if (!$_POST) {
            return;
        }

        if (!isset($_POST['backupID']) || !isset($_POST['action'])) {
            return;
        }

        switch ($_POST['action']) {

            case 'getLink':

                    // prepare request
                    $request = array(
                        'time'     => esc_attr($_POST['time']),
                        'backupID' => esc_attr($_POST['backupID'])
                    );
                    
                    $response = WPGuards_Curl::fetch('backup/getBackupLink', $request);

                    // set human readable response from api
                    WPGuards_Admin::setNotice($response->code, $response->status, __($response->message, 'wpguards'));

                    if ($response->status == 'success') {
                        set_transient('wpguards_backup_' . $_POST['backupID'], $response->data->link, 3600);
                    }

                break;
            case 'restore':

                    // prepare request
                    $request = array(
                        'time'     => esc_attr($_POST['time']),
                        'backupID' => esc_attr($_POST['backupID'])
                    );
                    
                    $response = WPGuards_Curl::fetch('backup/restoreBackup', $request);

                    // set human readable response from api
                    WPGuards_Admin::setNotice($response->code, $response->status, __($response->message, 'wpguards'));
                    
                break;

        }

        return;
    }

    /**
     * Adds submenu/menu page and prints scripts and styles
     * 
     * @access public
     * @return void
     */
    public function addOptionPage() {

        if (get_option('wpguards_connected')) {
            // display only settings page if user is not registered yet

            $menuHook = add_submenu_page(
                'wpguards',
                __('Backups', 'wpguards'), 
                __('Backups', 'wpguards'),
                'manage_options', 
                'wpguards_backups', 
                array($this, 'render')
            );
        
            add_action('admin_print_styles-' . $menuHook, array($this, 'enqueueStyles'));

        }

    }

    public function render()
    {
        $backupTransient     = get_transient('wpguards_backups');
        $nextBackupTransient = get_transient('wpguards_nextBackup');
        $basicData           = get_transient('wpguards_checkConnection');

        include(plugin_dir_path(__FILE__) . 'partials/WPGuards-admin-backups.php');
    }

    /**
     * Register the stylesheets for the WPGuards index.
     *
     * @since   2.0
     * @return  void
     */
    public function enqueueStyles() {

        wp_enqueue_style('wpguards_admin_css', plugin_dir_url(__FILE__) . 'css/WPGuards-admin.css');
        wp_enqueue_style('wpguards_calendar_css', plugin_dir_url(__FILE__) . 'css/WPGuards-calendar.css');

        wp_enqueue_style('wpguards_fullcalendar', plugin_dir_url(__FILE__) . 'css/fullcalendar.min.css');
        wp_enqueue_style('wpguards_fullcalendar_print', plugin_dir_url(__FILE__) . 'css/fullcalendar.print.css');

    }

}
