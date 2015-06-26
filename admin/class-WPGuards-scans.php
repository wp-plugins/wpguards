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

        $basicData = get_transient('wpguards_checkConnection');

        if (!isset($basicData->planID)) {
            return $this;
        }

        if ($basicData->planID == '2') {
            return $this;
        }

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
            // transient expires after 4 hours
            $transientTime = 14400;
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
            add_action('admin_print_scripts-' . $menuHook, array($this, 'enqueueScripts'));
        }

    }

    public function render()
    {
        $scanTransient     = get_transient('wpguards_scans');
        $nextScanTransient = get_transient('wpguards_nextScan');

        if (!empty($scanTransient)) {

            $positive = false;

            foreach ($scanTransient as $scan) {
                if ($scan->positives > 0) {
                    $positive = true;
                }
            } 
        } 

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

    /**
     * Register the JavaScript for the WPGuards scans.
     *
     * @since   2.0
     * @return  void
     */
    public function enqueueScripts() {

        wp_enqueue_script('wpguards_scans', plugin_dir_url(__FILE__) . 'js/WPGuards-scans.js', array('jquery'), false, true);

    }

}
