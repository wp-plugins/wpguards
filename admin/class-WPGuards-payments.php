<?php
/**
 * Payments subpage page for WPGuards
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/admin
 */

class WPGuards_Payments {

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
                __('Payments', 'wpguards'), 
                __('Payments', 'wpguards'),
                'manage_options', 
                'wpguards_payments', 
                array($this, 'render')
            );
        
            add_action('admin_print_styles-' . $menuHook, array($this, 'enqueueStyles'));

        }

    }

    public function render()
    {
        $payments = WPGuards_Curl::fetch('payment/getPayments');

        include(plugin_dir_path(__FILE__) . 'partials/WPGuards-admin-payments.php');
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
}

