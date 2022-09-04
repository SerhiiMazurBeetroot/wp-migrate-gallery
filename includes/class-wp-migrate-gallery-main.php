<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/SerhiiMazurBeetroot/
 * @since      1.1.0
 *
 * @package    WP_Migrate_Gallery
 * @subpackage WP_Migrate_Gallery/includes
 */

// Prevent direct access.
if ( ! defined( 'WPMG_PATH' ) ) exit;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.1.0
 * @package    WP_Migrate_Gallery
 * @subpackage WP_Migrate_Gallery/includes
 * @author     Serhii Mazur <serhiimazur@beetroot.se>
 */
class WP_Migrate_Gallery {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      WP_Migrate_Gallery_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.1.0
	 */
	public function __construct() {
		$this->plugin_name = WPMG_NAME;
		$this->version     = WPMG_VERSION;
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WP_Migrate_Gallery_Loader. Orchestrates the hooks of the plugin.
	 * - WP_Migrate_Gallery_i18n. Defines internationalization functionality.
	 * - WP_Migrate_Gallery_Admin. Defines all hooks for the admin area.
	 * - WP_Migrate_Gallery_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	private function load_dependencies() {
		require_once WPMG_PATH . 'includes/class-wp-migrate-gallery-loader.php';
		require_once WPMG_PATH . 'includes/class-wp-migrate-gallery-i18n.php';
		require_once WPMG_PATH . 'includes/dev.php';
		require_once WPMG_PATH . 'includes/admin/class-wp-migrate-gallery-admin.php';
		require_once WPMG_PATH . 'includes/admin/class-wp-migrate-gallery-import.php';
		require_once WPMG_PATH . 'includes/admin/class-wp-migrate-gallery-import-fields.php';
		require_once WPMG_PATH . 'includes/admin/class-wp-migrate-gallery-parser.php';
		require_once WPMG_PATH . 'includes/admin/class-wp-migrate-gallery-parser-fields.php';
		require_once WPMG_PATH . 'includes/class-wp-migrate-gallery-acf.php';
		require_once WPMG_PATH . 'includes/class-wp-migrate-gallery-ajax.php';


		$this->loader = new WP_Migrate_Gallery_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WP_Migrate_Gallery_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new WP_Migrate_Gallery_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		// Initialize the admin class.
		$plugin_admin = new WP_Migrate_Gallery_Admin( $this->get_plugin_name(), $this->get_version() );

		// /// Register the admin pages and scripts.
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'menu_pages' );

		// Other admin actions.
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_import_fields' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_parser_fields' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.1.0
	 * @return    WP_Migrate_Gallery_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
