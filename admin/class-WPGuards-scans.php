<?php
/**
 * Malware scans page for WPGuards
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/admin
 */

class WPGuards_Scans {

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

        $this->setScansTransient();

        // add submenu/menu page
        add_action('admin_menu', array($this, 'addOptionPage'));
        

        return $this;

    }

    public function setScansTransient() 
    {   
        if (!get_option('wpguards_connected', false)) {
            return;
        }

        // by default scans are stored in transient
        $scans = get_transient('wpguards_scans');

        if ($scans !== false) {
            return $scans;
        }  

        $response = WPGuards_Curl::fetch('scan/getScans');

        if ($response->status == 'success') {
            // transient expires 4 hours after next backup (time to process backup and send to cloud)
            $transientTime = $response->data->nextScan - time() + 14400;
            set_transient('wpguards_scans', $response->data->scans, $transientTime);
            set_transient('wpguards_nextScan', $response->data->nextScan, $transientTime);
        }
    }

    /**
     * Adds submenu/menu page and prints scripts and styles
     * 
     * @access public
     * @return void
     */
    public function addOptionPage() {

        $basicData = get_transient('wpguards_checkConnection');

        if (!isset($basicData->planID)) {
            return;
        }

        if ($basicData->planID == '2') {
            return;
        }

        if (get_option('wpguards_connected')) {
            // display only settings page if user is not registered yet

            $menuHook = add_submenu_page(
                'wpguards',
                __('Scans', 'wpguards'), 
                __('Scans', 'wpguards'),
                'manage_options', 
                'wpguards_scans', 
                array($this, 'render')
            );
        
            add_action('admin_print_styles-' . $menuHook, array($this, 'enqueueStyles'));

        }

    }

    public function render()
    {
        $scanTransient     = get_transient('wpguards_scans');
        $nextScanTransient = get_transient('wpguards_nextScan');

        include(plugin_dir_path(__FILE__) . 'partials/WPGuards-admin-scans.php');
    }

    /**
     * Register the stylesheets for the WPGuards index.
     *
     * @since   2.0
     * @return  void
     */
    public function enqueueStyles() {

        wp_enqueue_style('wpguards_admin_css', plugin_dir_url(__FILE__) . 'css/WPGuards-admin.css');

    }

}
