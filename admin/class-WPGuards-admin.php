<?php
/**
 * The dashboard-specific functionality of the plugin.
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/admin
 */

class WPGuards_Admin {

    /**
     * WPGuards instance
     *
     * @since   2.0
     * @access  private
     * @var     WPGuards
     */
    private $WPGuards;

    /**
     * Initialize the class and set its properties.
     *
     * @since  2.0
     * @param  WPGuards  $WPGuards  WPGuards instance.
     * 
     * @return this
     */
    public function __construct(WPGuards $WPGuards) {

        $this->WPGuards = $WPGuards;

        $this->checkConnection();

        add_action('wp_head', array($this, 'initUptimeMonitor'));
        add_action('login_footer', array($this, 'addLoginLink'));

        return $this;

    }

    /**
     * Add extra meta tag to monitor website uptime
     * 
     * @access  public
     * 
     * @return  void
     */
    public function initUptimeMonitor()
    {
        echo '<meta name="WPGuards" content="uptime" data-details="23ncu2bX3">';
    }

    /**
     * Add extra link below login box to get help
     * 
     * @access  public
     * 
     * @return  void
     */
    public function addLoginLink()
    {
        include(plugin_dir_path(__FILE__) . 'partials/WPGuards-admin-login.php');
    }

    /**
     * Method should fire every 8 hours. 
     * It updates WPguards settings.
     * 
     * @access  public
     * 
     * @return  void
     */
    public function checkConnection()
    {
        if (!get_option('wpguards_connected', false)) {
            return;
        }

        if (get_transient('wpguards_checkConnection') !== false) {
            return;
        }

        $response = WPGuards_Curl::fetch('site/checkConnection');

        // set transient data
        if ($response->status == 'success') {
            set_transient('wpguards_checkConnection', $response->data, 3600);
        } else {
            self::setNotice('1083', 'error', $response->message);
        }

        // set notices
        if (isset($response->data->notice) && is_array($response->data->notice) && ! empty($response->data->notice)) {
            foreach ($response->data->notice as $notice) {
                self::setGlobalNotice($notice[0], $notice[1], $notice[2]);
            }
        }

        // unset notices
        if (isset($response->data->unsetNotice) && is_array($response->data->unsetNotice) && ! empty($response->data->unsetNotice)) {
            foreach ($response->data->unsetNotice as $notice) {
                self::unsetGlobalNotice($notice);
            }
        }
    }

    /**
     * Method renders all admin notices. Both global and flash messages.
     * 
     * @access  public
     * 
     * @return  void
     */
    public static function getNotices()
    {
        $notices = get_option('wpguards_notices');

        // any global notices?
        if (is_array($notices) && !empty($notices)) {

            foreach ($notices as $notice) {

                if ($notice['type'] == 'success') {
                    $notice['type'] = 'updated';
                }
                
                echo '<div class="' . $notice['type'] .'"><p>' . $notice['message'] . '</p></div>';
            }

            // after dispalying delete notice
            delete_option('wpguards_notices');

        }

        $notices = get_option('wpguards_global_notice');

        // any admin notices?
        if (is_array($notices) && !empty($notices)) {

            foreach ($notices as $notice) {

                if ($notice['type'] == 'success') {
                    $notice['type'] = 'updated';
                }

                echo '<div class="' . $notice['type'] .'"><p>' . $notice['message'] . '</p></div>';
            }
        }
    }

    /**
     * Method sets global notice info
     * 
     * @since   2.0
     * @access  public
     * @static
     * 
     * @param   string  $code     error code. Keep in mind that using same 
     *                            code can overwrite previous notice.
     * @param   string  $type     error type.
     * @param   string  $message  error message.
     * 
     * @return  void
     */
     public static function setGlobalNotice($code, $type, $message)
     {
        
        $notices = get_option('wpguards_global_notice');

        $notice  = array(
            'type'    => $type,
            'message' => __($message, 'wpguards')
        );

        $notices[$code] = $notice;

        update_option('wpguards_global_notice', $notices); 
        
     }
    
    /**
     * Method unsets global notice
     * 
     * @since   2.0
     * @access  public
     * @static
     * 
     * @param   string  $code  error code specified in setGlobalNotice
     * 
     * @return  void
     */
     public static function unsetGlobalNotice($code)
     {
        
        $notices = get_option('wpguards_global_notice');
        unset($notices[$code]);
        update_option('wpguards_global_notice', $notices); 

    }
     
    /**
     * Method sets notice
     * 
     * @since   2.0
     * @access  public
     * @static
     * 
     * @param   string  $code     error code. Keep in mind that using same 
     *                            code can overwrite previous notice.
     * @param   string  $type     error type.
     * @param   string  $message  error message.
     * 
     * @return  void
     */
    public static function setNotice($code, $type, $message)
    {

        $notices = get_option('wpguards_notices');

        $notice  = array(
            'type'    => $type,
            'message' => __($message, 'wpguards')
        );

        $notices[$code] = $notice;

        update_option('wpguards_notices', $notices);

    }

}
