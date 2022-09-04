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
 * @subpackage WP_Migrate_Gallery/includes/admin
 * @author     Serhii Mazur <serhiimazur@beetroot.se>
 */
class WP_Migrate_Gallery_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->load_dependencies();
	}

	private function load_dependencies() {
		require_once WPMG_PATH . 'includes/admin/class-wp-migrate-gallery-import.php';
		require_once WPMG_PATH . 'includes/admin/class-wp-migrate-gallery-import-fields.php';
		require_once WPMG_PATH . 'includes/admin/class-wp-migrate-gallery-parser.php';
		require_once WPMG_PATH . 'includes/admin/class-wp-migrate-gallery-parser-fields.php';
		require_once WPMG_PATH . 'includes/admin/class-wp-migrate-gallery-history.php';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.1.0
	 */
	public function enqueue_styles( $hook ) {
		if( 'toplevel_page_wp-migrate-gallery' === $hook ) {
			wp_enqueue_style( $this->plugin_name, WPMG_URL . 'assets/css/wp-migrate-gallery-admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.1.0
	 */
	public function enqueue_scripts( $hook ) {
		if( 'toplevel_page_wp-migrate-gallery' === $hook ) {
			wp_enqueue_script( $this->plugin_name, WPMG_URL . 'assets/js/wp-migrate-gallery-admin.js', array( 'jquery' ), $this->version, false );

			wp_localize_script( $this->plugin_name, 'wpmg_vars', array(
				'endpoint' 		=> WPMG_AJAX::get_endpoint(),
				'ajax_nonce' 	=> wp_create_nonce( 'ajax_nonce' ),
			) );
		}
	}

	/**
	 * Register any menu pages used by the plugin.
	 * @since  1.1.0
	 * @access public
	 */
	public function menu_pages() {
		add_menu_page( $this->plugin_name, 'Migrate Gallery', 'administrator', $this->plugin_name, array( $this, 'menu_pages_callback' ), 'dashicons-carrot', 81 );
	}

	/**
	 * The callback for creating a new menu page.
	 * @access public
	 */
	public function menu_pages_callback() {
		ob_start();

        include_once WPMG_PATH . '/templates/wp-migrate-gallery-dashboard.php';

        $output = ob_get_clean();

        echo $output;
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

	public function register_import_fields() {
		new WP_Migrate_Gallery_Import_Fields();
	}

	public function register_parser_fields() {
		new WP_Migrate_Gallery_Parser_Fields();
	}

	public function render_settings_field($args) {
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
