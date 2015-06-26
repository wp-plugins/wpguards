<?php
/**
 * Home page of WPGuards
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/admin
 */

class WPGuards_Home {

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

        // add submenu/menu page
        add_action('admin_menu', array($this, 'addOptionPage'));

        return $this;

    }

    public function getCalendarData()
    {
        $backups    = get_transient('wpguards_backups');
        $nextBackup = get_transient('wpguards_nextBackup');
        $scans      = get_transient('wpguards_scans');
        $nextScan   = get_transient('wpguards_nextScan');

        $calendarData = array();

        if ($nextBackup != false) {
            $calendarData[] = array(
                'start'     => date('Y-m-d', $nextBackup),
                'title'     => __('Scheduled', 'wpguards'),
                'className' => 'backup scheduled'
            );
        }

        if ($nextScan != false) {
            $calendarData[] = array(
                'start'     => date('Y-m-d', $nextScan),
                'title'     => __('Scheduled', 'wpguards'),
                'className' => 'scan scheduled'
            );
        }

        if (is_object($backups) && !empty($backups)) {
            foreach ($backups as $backup) {
                $calendarData[] = array(
                    'start'     => date('Y-m-d', $backup->time),
                    'title'     => __('Full backup', 'wpguards'),
                    'className' => 'backup',
                    'url'       => admin_url('admin.php?page=wpguards_backups')
                );
            }
        }

        $basicData = get_transient('wpguards_checkConnection');

        // limit scans info to trial, standard and pro
        if (is_array($scans) && !empty($scans) && in_array($basicData->planID, array('1', '3', '4'))) {
            foreach ($scans as $scan) {
                $calendarData[] = array(
                    'start'     => $scan->date,
                    'title'     => __('Malware scan', 'wpguards'),
                    'className' => ($scan->positives == '0') ? 'scan' : 'scan red',
                    'url'       => admin_url('admin.php?page=wpguards_scans')
                );
            }
        }

        return $calendarData;
    }

    public function getUptimeData()
    {
        $basicData = get_transient('wpguards_checkConnection');

        if (!isset($basicData->uptime)) {
            return;
        }

        return array(
            array(
                'value' => intval($basicData->uptime),
                'color' => '#7ad03a',
                'label' => __('online', 'wpguards')
            ),
            array(
                'value' => intval(100-$basicData->uptime),
                'color' => '#dd3d36',
                'label' => __('offline', 'wpguards')
            ),
        );
    }

    /**
     * Adds submenu/menu page and prints scripts and styles
     * 
     * @access public
     * @return void
     */
    public function addOptionPage()
    {

        if (get_option('wpguards_connected')) {
            
            // display only settings page if user is not registered yet
            add_menu_page(
                WPGUARDS_NAME, 
                WPGUARDS_NAME, 
                'manage_options', 
                'wpguards', 
                array($this, 'render'), 
                'dashicons-lock', 
                '3.69'
            );

            $menuHook = add_submenu_page(
                'wpguards',
                __('Home', 'wpguards'), 
                __('Home', 'wpguards'),
                'manage_options', 
                'wpguards', 
                array($this, 'render')
            );

            add_action('admin_print_scripts-' . $menuHook, array($this, 'enqueueScripts'));
            add_action('admin_print_styles-' . $menuHook, array($this, 'enqueueStyles'));
        }

    }

    public function render()
    {
        $basicData = get_transient('wpguards_checkConnection');
        $user      = wp_get_current_user();
        
        include(plugin_dir_path(__FILE__) . 'partials/WPGuards-admin-home.php');
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

    /**
     * Register the JavaScript for the WPGuards index.
     *
     * @since   2.0
     * @return  void
     */
    public function enqueueScripts() {

        // avatar
        wp_enqueue_script('wpguards_pnglib', plugin_dir_url(__FILE__) . 'js/jquery.pnglib.min.js', array('jquery'));
        wp_enqueue_script('wpguards_identicon', plugin_dir_url(__FILE__) . 'js/jquery.identicon.js', array('jquery', 'wpguards_pnglib'));

        // chart
        wp_enqueue_script('wpguards_chart', plugin_dir_url(__FILE__) . 'js/Chart.min.js');

        // calendar
        wp_enqueue_script('wpguards_moment', plugin_dir_url(__FILE__) . 'js/moment.min.js', array('jquery'));
        wp_enqueue_script('wpguards_fullcalendar', plugin_dir_url(__FILE__) . 'js/fullcalendar.min.js', array('jquery', 'wpguards_moment'));

        // page scripts
        wp_enqueue_script('wpguards_home', plugin_dir_url(__FILE__) . 'js/WPGuards-home.js', array('jquery', 'wpguards_fullcalendar', 'wpguards_chart'), false, true);

        $localizeData = array(
            'calendar' => $this->getCalendarData(),
            'uptime'   => $this->getUptimeData(),
            'avatar'   => sha1(home_url())
        );
        wp_localize_script('wpguards_fullcalendar', 'WPGuards', $localizeData);

    }

}
