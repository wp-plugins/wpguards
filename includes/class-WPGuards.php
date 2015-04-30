<?php
/**
 * The file that defines the core plugin class
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/includes
 */

class WPGuards
{

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    2.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $pluginName;

	/**
	 * The current version of the plugin.
	 *
	 * @since    2.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    2.0
	 */
	public function __construct()
	{

		$this->pluginName = 'WPGuards';
		$this->version    = '2.0';

		$this->loadDependencies();
		$this->setLocale();

		new WPGuards_IWP();
		new WPGuards_Admin($this);
		new WPGuards_Home($this);
		new WPGuards_Backups($this);
		new WPGuards_Scans($this);
		new WPGuards_Uptime($this);
		new WPGuards_Support($this);
		new WPGuards_Payments($this);
		new WPGuards_Settings($this);
		new WPGuards_Utilities($this);

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    2.0
	 * @access   private
	 */
	private function loadDependencies()
	{
		
		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-WPGuards-i18n.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-WPGuards-IWP.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-WPGuards-curl.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-WPGuards-settings.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-WPGuards-admin.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-WPGuards-backups.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-WPGuards-payments.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-WPGuards-scans.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-WPGuards-home.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-WPGuards-uptime.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-WPGuards-support.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-WPGuards-utilities.php';

		/**
		 * WPGeeks forms
		 */
		
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/WPGeeks/Form/autoload.php';

		/**
		 * WPGeeks utilities
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/WPGeeks/Utilities/HTML.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WPGuards_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    2.0
	 * @access   private
	 */
	private function setLocale()
	{

		$plugin_i18n = new WPGuards_i18n();
		$plugin_i18n->setDomain($this->getPluginName());

		add_action('plugins_loaded', array($plugin_i18n, 'loadPluginTextdomain'));

	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2.0
	 * @return    string    The name of the plugin.
	 */
	public function getPluginName()
	{
		return $this->pluginName;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2.0
	 * @return    string    The version number of the plugin.
	 */
	public function getVersion()
	{
		return $this->version;
	}

}
