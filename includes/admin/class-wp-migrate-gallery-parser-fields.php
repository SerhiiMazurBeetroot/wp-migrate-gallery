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
class WP_Migrate_Gallery_Parser_Fields {

	private $plugin_name;

	private $version;

	private $plugin_admin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
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

	public function register_and_build_fields() {
		add_settings_section(
			'wp_migrate_gallery_parser_section', 						// ID used to identify this section and with which to register options
			'',  														// Title to be displayed on the administration page
			null,   													// Callback used to render the description of the section
			'wp_migrate_gallery_parser_settings'                   		// Page on which to add this section of options
		);

		add_settings_field(
			'site_url',
			'1.1. Site URL <small>*</small>',
			array( $this, 'fn_render_settings_field' ),
			'wp_migrate_gallery_parser_settings',
			'wp_migrate_gallery_parser_section',
			array (
				'type'             => 'input',
				'subtype'          => 'text',
				'id'               => 'site_url',
				'name'             => 'site_url',
				'required'         => 'true',
				'get_options_list' => '',
				'value_type'       => 'normal',
				'wp_data'          => 'option',
				'placeholder'      => 'https://www.google.com',
			),
		);

	    add_settings_field(
			"parser_option",
			"2.2. Parser option",
			[$this,'setting_dropdown_fn'],
			'wp_migrate_gallery_parser_settings',
			'wp_migrate_gallery_parser_section',
		);

		add_settings_field(
			'container_selector',
			'2.3. Container selector <small>*</small>',
			[$this,'fn_render_settings_field'],
			'wp_migrate_gallery_parser_settings',
			'wp_migrate_gallery_parser_section',
			array (
				'type'             => 'input',
				'subtype'          => 'text',
				'id'               => 'container_selector',
				'name'             => 'container_selector',
				'required'         => 'true',
				'get_options_list' => '',
				'value_type'       => 'normal',
				'wp_data'          => 'option',
				'placeholder'      => '.su-tabs-panes .su-tabs-pane',
				'class'      	   => 'wrapper_by_selector',
			),
		);

		add_settings_field(
			'dir_title',
			'2.4. Dir title by attribute <small>(optional)</small>',
			[$this,'fn_render_settings_field'],
			'wp_migrate_gallery_parser_settings',
			'wp_migrate_gallery_parser_section',
			array (
				'type'             => 'input',
				'subtype'          => 'text',
				'id'               => 'dir_title',
				'name'             => 'dir_title',
				'required'         => 'true',
				'get_options_list' => '',
				'value_type'       => 'normal',
				'wp_data'          => 'option',
				'placeholder'      => 'data-title',
				'class'      	   => 'wrapper_by_selector',
			),
		);

		add_settings_field(
			'image_wrapper',
			'2.5. Image wrapper selector <small>*</small>',
			[$this,'fn_render_settings_field'],
			'wp_migrate_gallery_parser_settings',
			'wp_migrate_gallery_parser_section',
			array (
				'type'             => 'input',
				'subtype'          => 'text',
				'id'               => 'image_wrapper',
				'name'             => 'image_wrapper',
				'required'         => 'true',
				'get_options_list' => '',
				'value_type'       => 'normal',
				'wp_data'          => 'option',
				'placeholder'      => '.flex',
				'class'      	   => 'wrapper_by_selector',
			),
		);


		add_settings_field(
			'subdir_title',
			'2.6. Subdirectory title <small>(optional)</small>',
			[$this,'fn_render_settings_field'],
			'wp_migrate_gallery_parser_settings',
			'wp_migrate_gallery_parser_section',
			array (
				'type'             => 'input',
				'subtype'          => 'text',
				'id'               => 'subdir_title',
				'name'             => 'subdir_title',
				'required'         => 'true',
				'get_options_list' => '',
				'value_type'       => 'normal',
				'wp_data'          => 'option',
				'placeholder'      => 'Single',
				'class'      	   => 'wrapper_by_selector',
			),
		);
	}

	// DROP-DOWN-BOX
	function  setting_dropdown_fn() {
        $options       = get_option('parser_option');
		$items = array(
			array(
				"label" => "Parse images all",
				"function" => "parse_images_all",
			),
			array(
				"label" => "Parse images by selector",
				"function" => "parse_images_by",
			),
		);
		echo "<select id='parser_option' name='parser_option'>";
		foreach($items as $item) {
			$selected = ($options == $item["label"]) ? 'selected="selected"' : '';
			echo "<option data-fn='$item[function]' value='$item[label]' $selected>$item[label]</option>";
		}
		echo "</select>";
	}

	public function register_page_settings() {
		$settingsArray = array (
			'site_url',
			'parser_option',
			'container_selector',
			'image_wrapper',
			'dir_title',
			'subdir_title',
		);
	
		foreach ($settingsArray as $setting) {
			register_setting( 'wp_migrate_gallery_parser_settings', $setting);
		}
	}

	public function fn_render_settings_field($args) {
		$this->plugin_admin->render_settings_field($args);
	}
}
