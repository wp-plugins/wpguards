<?php
/**
 * Uptime subpage page for WPGuards
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/admin
 */

class WPGuards_Uptime {

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

        if (get_option('wpguards_connected')) {
            // display only settings page if user is not registered yet

            $menuHook = add_submenu_page(
                'wpguards',
                __('Uptime', 'wpguards'), 
                __('Uptime', 'wpguards'),
                'manage_options', 
                'wpguards_uptime', 
                array($this, 'render')
            );
        
            add_action('admin_print_styles-' . $menuHook, array($this, 'enqueueStyles'));
            add_action('admin_print_scripts-' . $menuHook, array($this, 'enqueueScripts'));

        }

    }

    /**
     * Render subpage view
     *
     * @since   2.0
     * @return  void
     */
    public function render()
    {
        $uptimeMonitor = WPGuards_Curl::fetch('uptime/getMonitor');

        include(plugin_dir_path(__FILE__) . 'partials/WPGuards-admin-uptime.php');
    }

    /**
     * Register the stylesheets for the WPGuards index.
     *
     * @since   2.0
     * @return  void
     */
    public function enqueueStyles()
    {

        wp_enqueue_style('wpguards_admin_css', plugin_dir_url(__FILE__) . 'css/WPGuards-admin.css');

    }

    /**
     * Register the JavaScript for the WPGuards uptime subpage.
     *
     * @since   2.0
     * @return  void
     */
    public function enqueueScripts() {

        $uptimeMonitor = WPGuards_Curl::fetch('uptime/getMonitor');

        // chart
        wp_enqueue_script('wpguards_chart', plugin_dir_url(__FILE__) . 'js/Chart.min.js');

        // page scripts
        wp_enqueue_script('wpguards_uptime', plugin_dir_url(__FILE__) . 'js/WPGuards-uptime.js', array('jquery', 'wpguards_chart'), false, true);

        $localizeData = array(
            'uptime'   => $this->uptimeChart($uptimeMonitor),
            'history'  => $this->uptimeLogChart($uptimeMonitor),
            'response' => $this->responseChart($uptimeMonitor)
        );
        wp_localize_script('wpguards_uptime', 'WPGuards', $localizeData);

    }

    /**
     * Return array with log chart
     *
     * @since   2.0
     * @return  string
     */
    private function uptimeChart($transient)
    {   
        if (!isset($transient->data) || !is_object($transient->data) || empty($transient->data)) {
            return;
        }

        return array(
            array(
                'value' => intval($transient->data->alltimeuptimeratio),
                'color' => '#7ad03a',
                'label' => __('online', 'wpguards')
            ),
            array(
                'value' => 100-intval($transient->data->alltimeuptimeratio),
                'color' => '#dd3d36',
                'label' => __('offline', 'wpguards')
            )
        );
    }

    /**
     * Return array with uptime log chart
     *
     * @since   2.0
     * @return  string
     */
    private function uptimeLogChart($transient)
    {
        if (!isset($transient->data) || !is_object($transient->data) || empty($transient->data)) {
            return;
        }

        if (!is_array($transient->data->log) || empty($transient->data->log)) {
            return;
        } 

        $output      = array();

        $dateRange   = end($transient->data->log);
        $startTime   = strtotime($dateRange->datetime);

        $logs        = array_reverse($transient->data->log);

        for ($i = 1; $i <= count($logs); $i++) {

            if (!isset($logs[$i]->datetime)) {
                $thisTime = time()-$startTime;
            } else {
                $thisTime = (strtotime($logs[$i]->datetime)-$startTime);
            }
            
            $prevTime = (strtotime($logs[$i-1]->datetime)-$startTime);

            $time = round(($thisTime-$prevTime)/60, 2);

            $output[] = array(
                'value' => round(($thisTime-$prevTime)/60, 2),
                'color' => ($logs[$i-1]->type == '1') ? '#dd3d36' : '#7ad03a',
                'label' => ($logs[$i-1]->type == '1') ? 'down' : 'up',
            );
        }
        
        return $output;
    }

    /**
     * Return array with response chart
     *
     * @since   2.0
     * @return  string
     */
    private function responseChart($transient)
    {
        if (!isset($transient->data) || !is_object($transient->data) || empty($transient->data)) {
            return;
        }

        if (!is_array($transient->data->responsetime) || empty($transient->data->responsetime)) {
            return;
        } 

        $output = array(
            'labels'   => array(),
            'datasets' => array(
                array(
                    'label'                => "Response time",
                    'fillColor'            => "rgba(220,220,220,0.2)",
                    'strokeColor'          => "#0074a2",
                    'pointColor'           => "#0074a2",
                    'pointStrokeColor'     => "#fff",
                    'pointHighlightFill'   => "#fff",
                    'pointHighlightStroke' => "#0074a2",
                    'data'                 => array()
                )
            )
        );

        $i = 0;

        foreach($transient->data->responsetime as $response) {
            if ($i == 20) {
                break;
            }

            $output['labels'][]              = $response->datetime;
            $output['datasets'][0]['data'][] = $response->value;

            $i++;
        }

        return $output;
    }

    /**
     * Return class for each type
     *
     * @since   2.0
     * @return  string
     */
    private function getTypeClass($type = 1)
    {
        switch ($type) {
            case 1:
                return 'down';
                break;

            case 2:
                return 'up';
                break;
        }

        return 'undefined';
    }

}

