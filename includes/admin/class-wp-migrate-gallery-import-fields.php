<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/SerhiiMazurBeetroot/
 * @since      1.1.0
 *
 * @package    WP_Migrate_Gallery
 * @subpackage WP_Migrate_Gallery/includes/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Migrate_Gallery
 * @subpackage WP_Migrate_Gallery/includes
 * @author     Serhii Mazur <serhiimazur@beetroot.se>
 */
class WP_Migrate_Gallery_Import_Fields {

	private $plugin_name;

	private $version;

	private $plugin_admin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version           The version of this plugin.
	 */
	public function __construct() {
		$this->plugin_name = WPMG_NAME;
		$this->version     = WPMG_VERSION;
		$this->register_and_build_fields();
		$this->register_page_settings();
		$this->plugin_admin = new WP_Migrate_Gallery_Admin($this->plugin_name, $this->version);
	}

	/**
	 * First, we add_settings_section. This is necessary since all future settings must belong to one.
	 * Second, add_settings_field
	 * Third, register_setting
	 */ 

	private function register_and_build_fields() {
		add_settings_section(
			'wp_migrate_gallery_import_section', 							// ID used to identify this section and with which to register options
			'',  				                                            // Title to be displayed on the administration page
			null,                                                           // Callback used to render the description of the section
			'wp_migrate_gallery_import_settings'                   		    // Page on which to add this section of options
		);

		add_settings_field(
			'cpt_slug',
			'2.1. CPT Gallery Slug',
			[$this,'fn_render_settings_field'],
			'wp_migrate_gallery_import_settings',
			'wp_migrate_gallery_import_section',
			array (
				'type'             => 'input',
				'subtype'          => 'text',
				'id'               => 'cpt_slug',
				'name'             => 'cpt_slug',
				'required'         => 'true',
				'get_options_list' => '',
				'value_type'       =>'normal',
				'wp_data'          => 'option',
				'placeholder'      => 'gallery',
			),
		);

		add_settings_field(
			'single_title',
			'2.2. Title for CPT single page',
			[$this,'fn_render_settings_field'],
			'wp_migrate_gallery_import_settings',
			'wp_migrate_gallery_import_section',
			array (
				'type'             => 'input',
				'subtype'          => 'text',
				'id'               => 'single_title',
				'name'             => 'single_title',
				'required'         => 'true',
				'get_options_list' => '',
				'value_type'       =>'normal',
				'wp_data'          => 'option',
				'placeholder'      => 'Gallery item',
			),
		);

		add_settings_field(
			'tax_slug',
			'2.3. Gallery Taxonomy Slug',
			[$this,'fn_render_settings_field'],
			'wp_migrate_gallery_import_settings',
			'wp_migrate_gallery_import_section',
			array (
				'type'             => 'input',
				'subtype'          => 'text',
				'id'               => 'tax_slug',
				'name'             => 'tax_slug',
				'required'         => 'true',
				'get_options_list' => '',
				'value_type'       =>'normal',
				'wp_data'          => 'option',
				'placeholder'      => 'gallery-categories',
			),
		);

		add_settings_field(
			'gallery_dir',
			'2.4. Gallery DIR: wp-content/',
			[$this,'fn_render_settings_field'],
			'wp_migrate_gallery_import_settings',
			'wp_migrate_gallery_import_section',
			array (
				'type'             => 'input',
				'subtype'          => 'text',
				'id'               => 'gallery_dir',
				'name'             => 'gallery_dir',
				'required'         => 'true',
				'get_options_list' => '',
				'value_type'       =>'normal',
				'wp_data'          => 'option',
				'placeholder'      => 'uploads/dir-gallery',
			),
		);
	}

	private function register_page_settings() {
		$settingsArray = array (
			'single_title',
			'gallery_dir',
			'tax_slug',
			'cpt_slug'
		);
	
		foreach ($settingsArray as $setting) {
			register_setting( 'wp_migrate_gallery_import_settings', $setting);
		}
	}

	public function fn_render_settings_field($args) {
		$this->plugin_admin->render_settings_field($args);
	}
}
