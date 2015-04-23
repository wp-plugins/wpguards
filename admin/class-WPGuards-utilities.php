<?php
/**
 * Utilities page of WPGuards
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/admin
 */

class WPGuards_Utilities {

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
            $menuHook = add_submenu_page(
                'wpguards',
                __('Utilites', 'wpguards'), 
                __('Utilites', 'wpguards'), 
                'manage_options', 
                'wpguards_utilities', 
                array($this, 'render')
            );

            add_action('admin_print_scripts-' . $menuHook, array($this, 'enqueueScripts'));
            add_action('admin_print_styles-' . $menuHook, array($this, 'enqueueStyles'));
            
        }

    }

    /**
     * Render utilities page
     *
     * @access public
     * @since  2.0
     * 
     * @return void
     */
    public function render()
    {
        $basicData = get_transient('wpguards_checkConnection');

        include(plugin_dir_path(__FILE__) . 'partials/WPGuards-admin-utilites.php');
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

    }
}
