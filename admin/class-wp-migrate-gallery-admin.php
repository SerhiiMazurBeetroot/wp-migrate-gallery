<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/SerhiiMazurBeetroot/
 * @since      1.0.0
 *
 * @package    WP_Migrate_Gallery
 * @subpackage WP_Migrate_Gallery/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Migrate_Gallery
 * @subpackage WP_Migrate_Gallery/admin
 * @author     Serhii Mazur <serhiimazur@beetroot.se>
 */
class WP_Migrate_Gallery_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('admin_menu', array( $this, 'addPluginAdminMenu' ), 9);
		add_action('admin_init', array( $this, 'registerAndBuildFields' ));
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Migrate_Gallery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Migrate_Gallery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-migrate-gallery-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Migrate_Gallery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Migrate_Gallery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-migrate-gallery-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function addPluginAdminMenu() {
		add_menu_page( $this->plugin_name, 'Migrate Gallery', 'administrator', $this->plugin_name, array( $this, 'displayPluginAdminDashboard' ), 'dashicons-carrot', 81 );
		add_submenu_page( $this->plugin_name, 'Import Gallery', 'Import Gallery', 'administrator', $this->plugin_name.'-import', array( $this, 'displayPluginAdminSettings' ) );
	}

	public function displayPluginAdminDashboard() {
		ob_start();

        include_once 'partials/wp-migrate-gallery-admin-display.php';

        $output = ob_get_clean();

        echo $output;
  	}

	public function displayPluginAdminSettings() {
		// set this var to be used in the admin-display view
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';

		if(isset($_GET['error_message'])){
			add_action('admin_notices', array($this,'settingsPageSettingsMessages'));
			do_action( 'admin_notices', $_GET['error_message'] );
		}

		if (empty($_POST)) {
            require_once 'partials/wp-migrate-gallery-admin-import.php';
        } else {
            require_once 'partials/wp-migrate-gallery-admin-import-run.php';
        }
	}

	public function settingsPageSettingsMessages($error_message){
		switch ($error_message) {
			case '1':
				$message = __( 'There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'my-text-domain' );
				$err_code = esc_attr( 'gallery_dir' );
				$setting_field = 'gallery_dir';
				break;
		}
		$type = 'error';
		add_settings_error(
			$setting_field,
			$err_code,
			$message,
			$type
		);
	}

	public function registerAndBuildFields() {
		/**
		 * First, we add_settings_section. This is necessary since all future settings must belong to one.
		 * Second, add_settings_field
		 * Third, register_setting
		 */     
		add_settings_section(
			'wp_migrate_gallery_general_section', 							// ID used to identify this section and with which to register options
			'',  														// Title to be displayed on the administration page
			array( $this, 'wp_migrate_gallery_display_general_account' ),   // Callback used to render the description of the section
			'wp_migrate_gallery_general_settings'                   		// Page on which to add this section of options
		);

		unset($args);

		add_settings_field(
			'cpt_slug',
			'2.1. CPT Gallery Slug',
			array( $this, 'wp_migrate_gallery_render_settings_field' ),
			'wp_migrate_gallery_general_settings',
			'wp_migrate_gallery_general_section',
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

		register_setting(
			'wp_migrate_gallery_general_settings',
			'cpt_slug'
		);

		add_settings_field(
			'single_title',
			'2.2. Title for CPT single page',
			array( $this, 'wp_migrate_gallery_render_settings_field' ),
			'wp_migrate_gallery_general_settings',
			'wp_migrate_gallery_general_section',
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

		register_setting(
			'wp_migrate_gallery_general_settings',
			'single_title'
		);

		add_settings_field(
			'tax_slug',
			'2.3. Gallery Taxonomy Slug',
			array( $this, 'wp_migrate_gallery_render_settings_field' ),
			'wp_migrate_gallery_general_settings',
			'wp_migrate_gallery_general_section',
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

		register_setting(
			'wp_migrate_gallery_general_settings',
			'tax_slug'
		);

		add_settings_field(
			'gallery_dir',
			'2.4. Gallery DIR: wp-content/',
			array( $this, 'wp_migrate_gallery_render_settings_field' ),
			'wp_migrate_gallery_general_settings',
			'wp_migrate_gallery_general_section',
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

		register_setting(
			'wp_migrate_gallery_general_settings',
			'gallery_dir'
		);
	}

	public function wp_migrate_gallery_display_general_account() {
		ob_start();
		include_once 'partials/wp-migrate-gallery-admin-import.php';

        $output = ob_get_clean();
        echo $output;
	}

	public function wp_migrate_gallery_render_settings_field($args) {
		/* EXAMPLE INPUT
		'type'      => 'input',
		'subtype'   => '',
		'id'    => $this->plugin_name.'_example_setting',
		'name'      => $this->plugin_name.'_example_setting',
		'required' => 'required="required"',
		'get_option_list' => "",
		'value_type' = serialized OR normal,
		'wp_data'=>(option or post_meta),
		'post_id' =>
		*/

		if($args['wp_data'] == 'option'){
			$wp_data_value = get_option($args['name']);
		} elseif($args['wp_data'] == 'post_meta'){
			$wp_data_value = get_post_meta($args['post_id'], $args['name'], true );
		}

		switch ($args['type']) {

			case 'input':
				$value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
				if($args['subtype'] != 'checkbox'){
					$prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">'.$args['prepend_value'].'</span>' : '';
					$prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
					$step = (isset($args['step'])) ? 'step="'.$args['step'].'"' : '';
					$min = (isset($args['min'])) ? 'min="'.$args['min'].'"' : '';
					$max = (isset($args['max'])) ? 'max="'.$args['max'].'"' : '';
					$placeholder = (isset($args['placeholder'])) ? $args['placeholder'] : '';

					if(isset($args['disabled'])){
						// hide the actual input bc if it was just a disabled input the info saved in the database would be wrong - bc it would pass empty values and wipe the actual information
						echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'_disabled" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="'.$args['id'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
					} else {
						echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" placeholder="'.$placeholder.'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
					}
					/*<input required="required" '.$disabled.' type="number" step="any" id="migrate-gallery_cost2" name="migrate-gallery_cost2" value="' . esc_attr( $cost ) . '" size="25" /><input type="hidden" id="migrate-gallery_cost" step="any" name="migrate-gallery_cost" value="' . esc_attr( $cost ) . '" />*/

				} else {
					$checked = ($value) ? 'checked' : '';
					echo '<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" name="'.$args['name'].'" size="40" value="1" '.$checked.' />';
				}
				break;
			default:
				# code...
				break;
		}
	}

}
