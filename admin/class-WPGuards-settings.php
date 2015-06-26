<?php
/**
 * Settings page of WPGuards
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/admin
 */

class WPGuards_Settings {

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
     * add_submenu_page/add_menu_page result
     *
     * @since   2.0
     * @access  private
     * @var     string
     */
    private $menuHook;

    /**
     * Forms array
     *
     * @since   2.0
     * @access  private
     * @var     array
     */
    private $forms;

    /**
     * Initialize the class and set its properties.
     * Register forms, add processing and menu actions.
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

        // register forms
        $this->registerForms();

        add_action('admin_init', array($this, 'process'));

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


        if (get_option('wpguards_connected')) {
            // display only settings page if user is not registered yet
            $this->menuHook = add_submenu_page(
                'wpguards',
                __('Settings', 'wpguards'), 
                __('Settings', 'wpguards'), 
                'manage_options', 
                'wpguards_settings', 
                array($this, 'render')
            );
        } else {
            
            $this->menuHook = add_menu_page(
                WPGUARDS_NAME, 
                WPGUARDS_NAME, 
                'manage_options', 
                'wpguards_settings', 
                array($this, 'render'), 
                'dashicons-lock', 
                '3.69'
            );
        }

        add_action('admin_print_scripts-' . $this->menuHook, array($this, 'enqueue_scripts'));
        add_action('admin_print_styles-' . $this->menuHook, array($this, 'enqueue_styles'));

    }

    /**
     * Register forms using array configs
     * 
     * @access public
     * @since  2.0
     * 
     * @return this
     */
    public function registerForms()
    {
        $this->forms = new stdClass;

        // prepare API form
        $this->forms->api = new WPGeeks_Form_Factory( array(
            array(
                'type'       => 'text',
                'label'      => __('Do you have unique API key?', 'wpguards'),
                'id'         => 'APIkey',
                'name'       => 'APIkey',
                'description' => sprintf( __( 'If you are already %1$s member it is possible to generate new API key using <strong><a href="%2$s">Control Panel</a></strong>.', 'wpguards' ), WPGUARDS_NAME, API_URL . '/panel/sites/index' )
            )
        ) );

        // prepare Registrer form
        $registerFormConfig = array(
            array(
                'label'      => __('Username', 'wpguards'),
                'type'       => 'text',
                'id'         => 'username',
                'name'       => 'username',
                'validators' => array(
                    new WPGeeks_Form_Validator_Required
                )
            ),
            array(
                'label'      => __('E-mail address', 'wpguards'),
                'type'       => 'text',
                'id'         => 'email',
                'name'       => 'email',
                'validators' => array(
                    new WPGeeks_Form_Validator_Required,
                    new WPGeeks_Form_Validator_Email
                )
            ),
            array(
                'label'      => __('Password', 'wpguards'),
                'type'       => 'password',
                'name'       => 'password',
                'id'         => 'password',
                'validators' => array(
                    new WPGeeks_Form_Validator_Required
                )
            ),
            array(
                'label'      => __('Repeat password', 'wpguards'),
                'type'       => 'password',
                'name'       => 'passwordRepeat',
                'id'         => 'passwordRepeat',
                'validators' => array(
                    new WPGeeks_Form_Validator_Required
                )
            ),
            array(
                'label'      => __('Country', 'wpguards'),
                'type'       => 'select',
                'name'       => 'country',
                'id'         => 'country',
                'options'    => array(
                    'Poland' => 'Poland'
                ),
                'validators' => array(
                    new WPGeeks_Form_Validator_Required
                )
            ),
            array(
                'label'      => __('Currency', 'wpguards'),
                'type'       => 'select',
                'name'       => 'currency',
                'id'         => 'currency',
                'options'    => array(
                    'USD' => 'USD',
                    'GBP' => 'GBP'
                ),
                'validators' => array(
                    new WPGeeks_Form_Validator_Required
                )
            )
        );

        $this->forms->register = new WPGeeks_Form_Factory($registerFormConfig);

        // preapre settings
        $settingsFormConfig = array(
            array(
                'label'      => __('Send uptime notifications', 'wpguards'),
                'description' => __('Get e-mail alert every time your website is offline', 'wpguards'),
                'type'       => 'switch',
                'value'      => ( isset( $this->options['uptime_notifications'] ) && $this->options['uptime_notifications'] == true ) ? 'true' : 'false',
                'option'     => 'true',
                'id'         => 'uptime_notifications',
                'name'       => 'uptime_notifications'
            )
        );

        $this->forms->settings = new WPGeeks_Form_Factory($settingsFormConfig);

        return $this;
    }

    /**
     * Render settings page
     *
     * @access public
     * @since  2.0
     * 
     * @return void
     */
    public function render()
    {
        include(plugin_dir_path(__FILE__) . 'partials/WPGuards-admin-settings.php');
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

        if ($_GET['page'] != 'wpguards_settings') {
            return;
        }

        if (!$_POST) {
            return;
        }

        // API key was sent and site is not connected yet
        if (!get_option('wpguards_connected', false) && isset($_POST['APIkey'])) {
            $this->install();
        }

        // registration form was sent
        if (!get_option('wpguards_connected', false) && isset($_POST['username'])) {
            $this->register();
        }

        // settings form was sent
        if ( isset( $_POST['wpgurds_save_settings'] ) ) {
            $this->save_settings();
        }

        return;
    }

    /**
     * Save settings
     *
     * @access private
     * @since  2.0
     * 
     * @return void
     */
    private function save_settings() {

        // set class property
        if ( isset( $_POST['uptime_notifications'] ) ) {
            $this->options['uptime_notifications'] = $_POST['uptime_notifications'];
        } else {
            $this->options['uptime_notifications'] = false;
        }
        
        $api_response = WPGuards_Curl::fetch( 'site/handleSettings', array(
            'APIkey' => $this->options['crm_apikey'],
            'settings' => $this->options
        ) );

        // send to API
        if ( $api_response->status == 'success' ) {

            // update setting
            update_option( 'wpguards_settings', $this->options );

            // refresh form values
            $this->registerForms();

            WPGuards_Admin::setNotice( '200', 'success', __('Settings successfully saved.', 'wpguards') );

        } else {

            // if API key was invalid add notice
            if ( isset( $api_response->message ) && ! empty( $api_response->message ) ) {
                $message = $api_response->message;
            } else {
                $message = __('There was problem with sending settings to API. Please save them again.', 'wpguards');
            }

            WPGuards_Admin::setNotice( '500', 'error', $message );

        }
        
    }

    /**
     * Install website in API repository
     *
     * @access private
     * @since  2.0
     * 
     * @return this
     */
    private function install() 
    {
        $this->options['crm_apikey'] = $_POST['APIkey'];

        update_option('wpguards_settings', $this->options);
            
        // check if site is ready to install
        $installReady = WPGuards_Curl::fetch('site/installReady', array('APIkey' => $_POST['APIkey']));

        // if site is ready to install
        if ($installReady->status == 'success') {

            // unset global notice
            WPGuards_Admin::unsetGlobalNotice(1000);

            $user = wp_get_current_user();

            // prepare request
            $request = array(
                'APIkey'        => $_POST['APIkey'],
                'URL'           => admin_url(),
                'username'      => $user->user_login,
                'activationKey' => get_option('iwp_client_activate_key')
            );
            
            // and perform action
            $response = WPGuards_Curl::fetch('site/install', $request);

            // set response
            WPGuards_Admin::setNotice($response->status, $response->status, $response->message);

            if ($response->status = 'success') {
                update_option('wpguards_connected', true);
            }

            return;

        } else {

            // if API key was invalid add notice
            if ( isset( $installReady->message ) && ! empty( $installReady->message ) ) {
                $message = $installReady->message;
            } else {
                $message = __('API key is invalid.', 'wpguards');
            }

            WPGuards_Admin::setNotice('10200', 'error', $message);

            return;

        }
        
    }

    /**
     * Register user in API panel
     *
     * @access private
     * @since  2.0
     * 
     * @return this
     */
    private function register()
    {
        // set values from $_POST
        $this->forms->register->setValues($_POST);

        // check if form is valid
        if (!$this->forms->register->isValid()) {

            $messages = $this->forms->register->getMessages();

            // display error messages on fail
            if (is_array($messages) && !empty($messages)) {
                foreach ($messages as $message) {
                    WPGuards_Admin::setNotice('0', 'error', __($message, 'wpguards'));
                }

                return;
            }
        }

        // check if passwords are the same
        if ($_POST['password'] != $_POST['passwordRepeat']) {
            WPGuards_Admin::setNotice('0', 'error', __('Passwords are not the same', 'wpguards'));

            return;
        }

        // prepare request
        $request = array(
            'username' => esc_attr($_POST['username']),
            'email'   => esc_attr($_POST['email']),
            'password' => esc_attr($_POST['password']),
            'passwordRepeat' => esc_attr($_POST['passwordRepeat']),
            'country'  => esc_attr($_POST['country']),
            'currency' => esc_attr($_POST['currency'])
        );
        
        $response = WPGuards_Curl::fetch('user/register', $request);

        // set human readable response from api
        WPGuards_Admin::setNotice($response->status, $response->status, __($response->message, 'wpguards'));

        return;
    }

    /**
     * Register the stylesheets for the WPGuards index.
     *
     * @since   2.0
     * @return  void
     */
    public function enqueue_styles() {

        wp_enqueue_style('wpguards_admin_css', plugin_dir_url(__FILE__) . 'css/WPGuards-admin.css');
        //wp_enqueue_style('wpguards_admin_css', WPGUARDS_CSS . 'admin.css');
        //wp_enqueue_style('wpguards_font_awesome_css', WPGUARDS_CSS . 'font-awesome.min.css');
    }

    /**
     * Register the JavaScript for the WPGuards index.
     *
     * @since   2.0
     * @return  void
     */
    public function enqueue_scripts() {

        //wp_enqueue_script('wpguards_loader', WPGUARDS_JS . 'loader.js', array('jquery'));

    }
}
