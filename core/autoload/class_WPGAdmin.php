<?php

defined('ABSPATH') OR exit; //prevent from direct access

/**
* WPGuards Admin Class
*/
class WPGAdmin {

    public $options;
    public $apikey_changed = false;

    public $user_plan;

    public $ga_client;
    
    /**
     * WPGAdmin Constructor
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        global $WPGuards;

        $this->options = get_option('wpguards_settings');
        $this->user_plan = get_option( 'wpguards_user_plan', 'trial' );
        $this->apikey_changed = get_option( 'wpguards_apikey_changed', false );

        add_action( 'admin_enqueue_scripts', array($this, 'load_wpguards_admin_styles') );

        add_action( 'admin_menu', array($this, 'add_option_pages') );

        add_action( 'admin_init', array($this, 'register_settings_and_fields') );

        add_action( 'admin_init', array($this, 'check_tickets_payment') );

    }

    /**
     * Checks if there is var st from paypal
     * 
     * @return void
     */
    public function check_tickets_payment() {
        global $WPGuards; 

        if (isset($_GET['st'])) {
            
            if ($_GET['st'] == 'Completed' || $_GET['st'] == 'Processing') {
                WPGAdmin::unsetGlobalNotice(66);
                WPGAdmin::setNotice(198, 'updated', 'Thanks for your payment. Please wait while we are handling it (up to 5 minutes)');
            }

            else WPGAdmin::setNotice(198, 'error', 'There is problem with your payment. Please contact support');

        }

    }

    /**
     * Loads admin styles
     * 
     * @access public
     * @return void
     */
    public function load_wpguards_admin_styles() {

        wp_register_style( 'wpguards_admin_css', WPGUARDS_CSS.'admin.css' );
        wp_register_style( 'wpguards_font_awesome_css', WPGUARDS_CSS.'font-awesome.min.css' );

        wp_enqueue_style( 'wpguards_admin_css' );
        wp_enqueue_style( 'wpguards_font_awesome_css' );
        
    }

    /**
     * Loads admin loader script
     * 
     * @access public
     * @return void
     */
    public function load_wpguards_loader_script() {

        wp_register_script( 'wpguards_loader', WPGUARDS_JS.'loader.js', array( 'jquery' ) );
        wp_enqueue_script( 'wpguards_loader' );

    }

    /**
     * Loads admin check backups script
     * 
     * @access public
     * @return void
     */
    public function load_wpguards_check_backup_script() {

        wp_register_script( 'wpguards_restore_backup', WPGUARDS_JS.'backup.js', array( 'jquery' ) );
        wp_enqueue_script( 'wpguards_restore_backup' );

    }

    /**
     * Loads admin malware script
     * 
     * @access public
     * @return void
     */
    public function load_wpguards_malware_script() {

        wp_register_script( 'wpguards_malware', WPGUARDS_JS.'malware.js', array( 'jquery', 'jquery-ui-tabs' ) );
        wp_enqueue_script( 'wpguards_malware' );

    }

    /**
     * Loads admin loader css
     * 
     * @access public
     * @return void
     */
    public function load_wpguards_loader_style() {

        wp_register_style( 'wpguards_loader_css', WPGUARDS_CSS.'loader.css' );
        wp_enqueue_style( 'wpguards_loader_css' );

    }

    /**
     * Adds menu and submenu pages
     * 
     * @access public
     * @return void
     */
    public function add_option_pages() {

        add_menu_page(
            'WPGuards',
             __('WPGuards','wpguards'), 
             'manage_options', 
             'wpguards', 
             array($this, 'display_page'), 
             $this->get_menu_icon(), 
             '3.69'
        );

        $home = add_submenu_page(
            'wpguards',
            __( 'Home', 'wpguards' ),
            __( 'Home', 'wpguards' ),
            'manage_options',
            'wpguards',
            array($this, 'display_page')
        );

        if (!empty($this->options['analytics'])) {

            $analytics = add_submenu_page(
                'wpguards',
                __( 'Analytics', 'wpguards' ),
                __( 'Analytics', 'wpguards' ),
                'manage_options',
                'wpguards_analytics',
                array($this, 'display_page')
            );

        }

        $backups = add_submenu_page(
            'wpguards',
            __( 'Backups', 'wpguards' ),
            __( 'Backups', 'wpguards' ),
            'manage_options',
            'wpguards_backups',
            array($this, 'display_page')
        );

        /*$maintenance = add_submenu_page(
            'wpguards',
            __( 'Maintenance', 'wpguards' ),
            __( 'Maintenance', 'wpguards' ),
            'manage_options',
            'wpguards_maintenance',
            array($this, 'display_page')
        );*/

        $malware = add_submenu_page(
            'wpguards',
            __( 'Malware', 'wpguards' ),
            __( 'Malware', 'wpguards' ),
            'manage_options',
            'wpguards_malware',
            array($this, 'display_page')
        );

        $support = add_submenu_page(
            'wpguards',
            __( 'Support', 'wpguards' ),
            __( 'Support', 'wpguards' ),
            'manage_options',
            'wpguards_support',
            array($this, 'display_page')
        ); 

        $diagnostic = add_submenu_page(
            'wpguards',
            __( 'Diagnostic tools', 'wpguards' ),
            __( 'Diagnostic tools', 'wpguards' ),
            'manage_options',
            'wpguards_diagnostic',
            array($this, 'display_page')
        ); 

        $setting = add_submenu_page(
            'wpguards',
            __( 'Settings', 'wpguards' ),
            __( 'Settings', 'wpguards' ),
            'manage_options',
            'wpguards_settings',
            array($this, 'display_page')
        );

        add_action( 'load-'.$setting, array($this, 'load_settings_page'));

        add_action( 'admin_print_scripts-'.$home, array($this, 'load_wpguards_loader_script') );
        add_action( 'admin_print_scripts-'.$support, array($this, 'load_wpguards_loader_script') );
        if (!empty($this->options['analytics'])) add_action( 'admin_print_scripts-'.$analytics, array($this, 'load_wpguards_loader_script') );
        add_action( 'admin_print_scripts-'.$backups, array($this, 'load_wpguards_loader_script') );
        add_action( 'admin_print_scripts-'.$backups, array($this, 'load_wpguards_check_backup_script') );
        add_action( 'admin_print_scripts-'.$malware, array($this, 'load_wpguards_malware_script') );

        add_action( 'admin_print_styles-'.$home, array($this, 'load_wpguards_loader_style') );
        add_action( 'admin_print_styles-'.$support, array($this, 'load_wpguards_loader_style') );
        if (!empty($this->options['analytics'])) add_action( 'admin_print_styles-'.$analytics, array($this, 'load_wpguards_loader_style') );
        add_action( 'admin_print_styles-'.$backups, array($this, 'load_wpguards_loader_style') );
    }

    /**
     * Displays content for particular pages
     * 
     * @access public
     * @return void
     */
    public function display_page() {
    ?>
        <div class="wrap">

            <!-- Display errors -->
            <?php settings_errors(); ?>

            <!-- Display icon -->
            <?php $this->display_screen_icon(); ?>
            
            <h2 class="nav-tab-wrapper">
                <!-- <span class="nav-title"><?php _e( 'WPGuards', 'wpguards' ); ?> -->
                <a href="?page=wpguards" class="nav-tab <?php echo $_GET[ 'page' ] == 'wpguards' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Home', 'wpguards' ); ?></a>
                <?php if (!empty($this->options['analytics'])): ?>
                    <a href="?page=wpguards_analytics" class="nav-tab <?php echo $_GET[ 'page' ] == 'wpguards_analytics' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Analytics', 'wpguards' ); ?></a>
                <?php endif ?>
                <a href="?page=wpguards_backups" class="nav-tab <?php echo $_GET[ 'page' ] == 'wpguards_backups' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Backups', 'wpguards' ); ?></a>
                <!-- <a href="?page=wpguards_maintenance" class="nav-tab <?php echo $_GET[ 'page' ] == 'wpguards_maintenance' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Maintenance', 'wpguards' ); ?></a> -->
                <a href="?page=wpguards_malware" class="nav-tab <?php echo $_GET[ 'page' ] == 'wpguards_malware' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Malware', 'wpguards' ); ?></a>
                <a href="?page=wpguards_support" class="nav-tab <?php echo $_GET[ 'page' ] == 'wpguards_support' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Support', 'wpguards' ); ?></a>
                <a href="?page=wpguards_diagnostic" class="nav-tab <?php echo $_GET[ 'page' ] == 'wpguards_diagnostic' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Diagnostic tools', 'wpguards' ); ?></a>
                <a href="?page=wpguards_settings" class="nav-tab <?php echo $_GET[ 'page' ] == 'wpguards_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Settings', 'wpguards' ); ?></a>

                <?php if ( isset($_GET['ticket']) && isset($_GET['ticket_title']) ) {
                    // echo '<a class="nav-tab nav-tab-active tab-ticket"><i class="icon-ticket"></i> '.urldecode($_GET['ticket_title']).'</a>';
                    echo '<a class="nav-tab nav-tab-active tab-ticket"><i class="icon-ticket"></i></a>';
                } ?>

            </h2>
            

            <?php if ( isset($_GET['ticket']) && isset($_GET['ticket_title']) ) {
                require_once(WPGUARDS_PATH.'admin/templates/tab_ticket.php');
            } else {
                require_once(WPGUARDS_PATH.'admin/templates/tab_'.$_GET['page'].'.php');
            } ?>

            
        </div><!-- .wrap -->
    <?php
    }

    /**
     * Displays screen icon
     * 
     * @access public
     * @return void
     */
    public function display_screen_icon() {

        if ($this->user_plan == 'basic') {
            screen_icon('wpguards-open');
        } else {
            screen_icon('wpguards-close');
        }
    }

    /**
     * Gets menu icon
     * 
     * @access public
     * @return string icon url
     */
    public function get_menu_icon() {

        if ($this->user_plan == 'basic') {
            return WPGUARDS_IMG.'logo_open_20.png';
        } else {
            return WPGUARDS_IMG.'logo_close_20.png';
        }

    }

    /**
     * Registers settings and fields
     * 
     * @access public
     * @return void
     */
    public function register_settings_and_fields() {

        register_setting('wpguards_settings', 'wpguards_settings', array($this, 'wpguards_sanitize_fields'));                 //third param- cb

        add_settings_section(
            'wpguards_crm',                                                         // ID used to identify this section and with which to register options
            __( 'General Settings', 'wpguards' ),                               // Title to be displayed on the administration page
            array($this, 'section_support_informations_cb'),                        // Callback used to render the description of the section
            'wpguards_settings'                                                     // Page on which to add this section of options
        );

            add_settings_field( 
                'crm_apikey',                                                       // ID used to identify the field
                '<label for="apikey">'.__( 'API key', 'wpguards' ).'</label>',      // The label to the left of the option interface element
                array($this, 'field_apikey_cb'),                                    // The name of the function responsible for rendering the option interface
                'wpguards_settings',                                                // The page on which this option will be displayed
                'wpguards_crm',                                                     // The name of the section to which this field belongs
                false                                                               // The array of arguments to pass to the callback
            );

            /*add_settings_field( 
                'analytics',                                                       // ID used to identify the field
                '<label for="analytics">'.__( 'Analytics Access Code', 'wpguards' ).'</label>',      // The label to the left of the option interface element
                array($this, 'field_analytics_cb'),                                    // The name of the function responsible for rendering the option interface
                'wpguards_settings',                                                // The page on which this option will be displayed
                'wpguards_crm',                                                     // The name of the section to which this field belongs
                false                                                               // The array of arguments to pass to the callback
            );*/

    }

    /**
     * Section output
     * 
     * @access public
     * @return void
     */
    public function section_support_informations_cb() {
        // pr($this->options);        
    }

    /**
     * API key field output
     * 
     * @access public
     * @return void
     */
    public function field_apikey_cb() {
        global $WPGuards; 
        if ($this->options['crm_apikey']) $val = $this->options['crm_apikey'];
        else $val = '';

        echo '<input type="text" class="regular-text" name="wpguards_settings[crm_apikey]" id="apikey" value="'.$val.'" />';

        if (empty($val)) {
            echo '<p class="description">'.sprintf( __( 'If you didn\'t get an API key yet, you can %sregister free account%s to get one.', 'wpguards' ), '<a href="http://api.wpguards.com/register" target="_blank" style="target-new: tab;" >', '</a>' ).'</p>';
        }

    }

    /**
     * Analytics Access key field output
     * 
     * @access public
     * @return void
     */
    public function field_analytics_cb() {
        global $WPGuards; 
        if (!empty($this->options['analytics'])) $val = $this->options['analytics'];
        else $val = '';

        echo '<input type="text" class="regular-text" name="wpguards_settings[analytics]" id="crm_aanalytics" value="'.$val.'" />';

        if (empty($val)) {
            echo '<p class="description">'.sprintf( __( 'By providing Analytics Access Code you\'ll enable integrated Google Analytics Dashboard plugin by Alin Marcu. %sGet Access Code%s.', 'wpguards' ), '<a href="'.$this->ga_client->createAuthUrl().'" target="_blank" style="target-new: tab;" >', '</a>' ).'</p>';
        } else {
            echo '<p class="description">'.__( 'Google Analytics Dashboard plugin by Alin Marcu integrated', 'wpguards' ).'</p>';
        }

    }

    /**
     * Check if start IWP
     */
    public function wpguards_sanitize_fields( $inputs ) {
        
        if ( !empty($this->options['crm_apikey']) && $inputs['crm_apikey'] === $this->options['crm_apikey'] ) $changed = false;
        else $changed = true;

        update_option('wpguards_apikey_changed', $changed);

        $output = array();
        foreach( $inputs as $key => $value ) {
             
            if( isset( $inputs[$key] ) ) $output[$key] = strip_tags( stripslashes( $inputs[ $key ] ) );
             
        }

        /*// end earlier if there isn't analytics access code
        if (empty($inputs['analytics'])) return $output;

        try {

            // Set Google Analytics client
            $this->set_analytics();

            $this->ga_client->authenticate($inputs['analytics']);
            $token = json_decode($this->ga_client->getAccessToken());
            ga_dash_store_token_only($token);
        } catch (Google_AuthException $e) {
            // WPGAdmin::setNotice(2, 'error', __('<strong>Google Analytics error</strong>: ', 'wpguards' ).$e);
            return $output;
        }*/

        return $output;

    }

    /**
     * Check and authorize API key and Analytics
     * @return void
     */
    public function load_settings_page() {
        global $WPGuards;
        
        // Set Google Analytics client
        // $this->set_analytics();

        WPGAdmin::unsetGlobalNotice(1000);
        
        if ( !$this->apikey_changed ) 
            return;

        if( isset($_GET['settings-updated']) && $WPGuards->WPGConnection->checkApiKey() ) {
            
            WPGAdmin::setNotice(1, 'updated', __( 'API Key correct', 'wpguards' ));
            $response= $WPGuards->WPGConnection->remoteInstall();

            // update option for no future remote installations
            update_option('wpguards_apikey_changed', false);

            // if this is first authorization make action
            if (!get_option('wpg_authorized'))
                do_action('wpg_authorized');
            
            if($response == '1') {
                WPGAdmin::setNotice(2, 'updated', __('WPGuards added to remote framework.', 'wpguards' ));
            } else {
                WPGAdmin::setGlobalNotice(print_r($response,1), 'error', $response->error->message);
            };

        }
    }

    /**
     * Set analytics client
     */
    public function set_analytics() {

        require_once WPGUARDS_PATH.'ga/functions.php';

        if (!class_exists('Google_Exception')) require_once WPGUARDS_PATH.'ga/external/Google_Client.php';

        $this->ga_client = new Google_Client();

        require_once WPGUARDS_PATH.'ga/external/contrib/Google_AnalyticsService.php';
    
        $this->ga_client = new Google_Client();
        $this->ga_client->setAccessType('offline');
        $this->ga_client->setApplicationName('Google Analytics Dashboard');
        $this->ga_client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
        
        $this->ga_client->setClientId('347001075646-hkub67g7husu4f9rfh4ta1m713h9l95n.apps.googleusercontent.com');
        $this->ga_client->setClientSecret('Dc58Q4zMNjwiOjYwLDyIwM_3');
        $this->ga_client->setDeveloperKey('AIzaSyBX7FTHAoRI_rOE9kG1dNHL_VwGDK2-Jqw');
        
        $service = new Google_AnalyticsService($this->ga_client);

        if (empty($this->options['analytics'])) return false;

        try {
            $this->ga_client->authenticate($this->options['analytics']);
            $token = json_decode($this->ga_client->getAccessToken());
            ga_dash_store_token_only($token);
        } catch (Google_AuthException $e) {
            // WPGAdmin::setNotice(2, 'error', __('<strong>Google Analytics error</strong>: ', 'wpguards' ).$e);
            return false;
        }

    }

    /**
     * Function setting global notice info
     */
     public function setGlobalNotice($code, $type, $message){
        $notices = get_option('WPG_global_admin_notice');
        $notices[$code] = array('type' => $type, 'message' => __($message, 'wpguards') );
        update_option('WPG_global_admin_notice', $notices ); 
        
     }
     
     /**
     * Function unset notice info
     */
     public function unsetGlobalNotice($code){
        
        $notices = get_option('WPG_global_admin_notice');
        unset($notices[$code]);
        update_option('WPG_global_admin_notice', $notices ); 
    }
     
    /**
     * Function set notice
     */
    public function setNotice($code, $type, $message){
        $notices = get_option('WPG_admin_notices');
        $notices[$code] = array('type' => $type, 'message' => __($message, 'wpguards') );
        update_option('WPG_admin_notices', $notices );
    }
     
    
}

?>