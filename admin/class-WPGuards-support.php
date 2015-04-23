<?php
/**
 * Support page for WPGuards
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/admin
 */

class WPGuards_Support {

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
     * Forms array
     *
     * @since   2.0
     * @access  private
     * @var     array
     */
    private $forms;

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

        // register forms
        $this->registerForms();

        add_action('admin_init', array($this, 'process'));

        // add submenu/menu page
        add_action('admin_menu', array($this, 'addOptionPage'));

        add_action('wp_ajax_getTickets', array($this, 'ajaxGetTickets'));
        add_action('wp_ajax_getReceipt', array($this, 'ajaxGetReceipt'));
        add_action('wp_ajax_getComments', array($this, 'ajaxGetComments'));
        
        return $this;

    }

    public function ajaxGetTickets()
    {
        $response = WPGuards_Curl::fetch('support/getTickets');

        include(plugin_dir_path(__FILE__) . 'partials/WPGuards-admin-support-tickets.php');
        die();
    }

    public function ajaxGetComments()
    {
        $response = WPGuards_Curl::fetch('support/getTicketComments', array('ticketID' => $_REQUEST['ticket']));

        include(plugin_dir_path(__FILE__) . 'partials/WPGuards-admin-support-comments.php');
        die();
    }

    public function ajaxGetReceipt()
    {
        $basicData = get_transient('wpguards_checkConnection');
        $response  = WPGuards_Curl::fetch('support/unpaidTickets');

        if (!empty($response->data)) {

            $total    = 0.00;
            $currency = '';

            foreach ($response->data as $ticketID => $ticketPayment) {
                $total    += $ticketPayment->cost;
                $currency  = $ticketPayment->currency;
            }
        }
        
        include(plugin_dir_path(__FILE__) . 'partials/WPGuards-admin-support-receipt.php');
        die();
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
                __('Support', 'wpguards'), 
                __('Support', 'wpguards'),
                'manage_options', 
                'wpguards_support', 
                array($this, 'render')
            );

            add_action('admin_print_scripts-' . $menuHook, array($this, 'enqueueScripts'));
           add_action('admin_print_styles-' . $menuHook, array($this, 'enqueueStyles'));
            
        }

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

        // prepare new ticket form
        $ticketFormConfig = array(
            array(
                'label'      => __('Type', 'wpguards'),
                'type'       => 'select',
                'name'       => 'type',
                'id'         => 'type',
                'descrption' => 'Which type describes best your situation',
                'options'    => array(
                    'problem'  => 'Problem',
                    'incident' => 'Incident',
                    'question' => 'Question',
                    'task'     => 'Task'
                ),
                'validators' => array(
                    new WPGeeks_Form_Validator_Required
                )
            ),
            array(
                'label'      => __('Subject', 'wpguards'),
                'type'       => 'text',
                'id'         => 'subject',
                'name'       => 'subject',
                'validators' => array(
                    new WPGeeks_Form_Validator_Required
                )
            ),
            array(
                'label'      => __('Description', 'wpguards'),
                'type'       => 'textarea',
                'name'       => 'description',
                'id'         => 'description',
                'validators' => array(
                    new WPGeeks_Form_Validator_Required
                )
            ),
            array(
                'type'       => 'nonce',
                'name'       => 'nonce',
                'validators' => array(
                    new WPGeeks_Form_Validator_Nonce
                )
            )
        );

        $this->forms->ticket = new WPGeeks_Form_Factory($ticketFormConfig);

        return $this;
    }

    public function render()
    {
        include(plugin_dir_path(__FILE__) . 'partials/WPGuards-admin-support.php');
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

        if ($_GET['page'] != 'wpguards_support') {
            return;
        }

        if (!$_POST) {
            return;
        }

        if (isset($_POST['type'])) {

            // only new ticket form has type field

            // set values from $_POST
            $this->forms->ticket->setValues($_POST);
            
            if (!$this->forms->ticket->isValid()) {
                $messages = $this->forms->ticket->getMessages();

                // display error messages on fail
                if (is_array($messages) && !empty($messages)) {
                    foreach ($messages as $message) {
                        WPGuards_Admin::setNotice('0', 'error', __($message, 'wpguards'));
                    }

                    return;
                }
            }

            // prepare request
            $request = array(
                'description' => esc_attr($_POST['description']),
                'type'        => esc_attr($_POST['type']),
                'subject'     => esc_attr($_POST['subject']),
            );
            
            $response = WPGuards_Curl::fetch('support/addTicket', $request);

            WPGuards_Admin::setNotice($response->code, $response->status, __($response->message, 'wpguards'));

        }

        if (isset($_POST['ticketID'])) {
            // only update ticket form has ticketID field
            
            if (empty($_POST['description'])) {
                WPGuards_Admin::setNotice('0', 'error', __('Respond field cannot be empty.', 'wpguards'));
                return;
            }

            $request = array(
                'ticketID'    => esc_attr($_POST['ticketID']),
                'description' => esc_attr($_POST['description'])
            );

            $response = WPGuards_Curl::fetch('support/updateTicket', $request);

            WPGuards_Admin::setNotice($response->code, $response->status, __($response->message, 'wpguards'));

        }

        return;
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
     * Register the JavaScript for the WPGuards index.
     *
     * @since   2.0
     * @return  void
     */
    public function enqueueScripts() {

        // avatar
        wp_enqueue_script('wpguards_pnglib', plugin_dir_url(__FILE__) . 'js/jquery.pnglib.min.js', array('jquery'));
        wp_enqueue_script('wpguards_identicon', plugin_dir_url(__FILE__) . 'js/jquery.identicon.js', array('jquery', 'wpguards_pnglib'));

        // page scripts
        wp_enqueue_script('wpguards_support', plugin_dir_url(__FILE__) . 'js/WPGuards-support.js', array('jquery', 'wpguards_identicon'));

        $localizeData = array(
            'ajaxURL'       => admin_url('admin-ajax.php'),
            'userAvatar'    => sha1(home_url()),
            'supportAvatar' => sha1(API_URL)
        );

        wp_localize_script('wpguards_support', 'WPGuards', $localizeData);

    }

}
