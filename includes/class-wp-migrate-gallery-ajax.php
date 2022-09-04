<?php

/**
 * AJAX-specific functionality for the plugin.
 *
 * @link       #
 * @since      1.1.0
 *
 * @package    Migrate_Gallery
 * @subpackage Migrate_Gallery/includes
 */

class WPMG_AJAX {

	public function __construct()
	{
		add_action( 'init', array( $this, 'define_ajax' ), 10 );
		add_action( 'init', array( $this, 'do_wpmg_ajax' ), 20 );
		$this->add_ajax_actions();
	}

	public static function get_endpoint() {
		return esc_url_raw( get_admin_url() . 'tools.php?page='. WPMG_NAME .'&wpmg-ajax=' );
	}

	public function define_ajax() {

		if ( isset( $_GET['wpmg-ajax'] ) && ! empty( $_GET['wpmg-ajax'] ) ) {

			// Define the WordPress "DOING_AJAX" constant.
			if ( ! defined( 'DOING_AJAX' ) ) {
				define( 'DOING_AJAX', true );
			}

			// Prevent notices from breaking AJAX functionality.
			if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
				@ini_set( 'display_errors', 0 );
			}

			// Send the headers.
			send_origin_headers();
			@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
			@header( 'X-Robots-Tag: noindex' );
			send_nosniff_header();
			nocache_headers();

		}
	}

	/**
	 * Check if we're doing AJAX and fire the related action.
	 */
	public function do_wpmg_ajax() {
		global $wp_query;

		if ( isset( $_GET['wpmg-ajax'] ) && ! empty( $_GET['wpmg-ajax'] ) ) {
			$wp_query->set( 'wpmg-ajax', sanitize_text_field( $_GET['wpmg-ajax'] ) );
		}

		if ( $action = $wp_query->get( 'wpmg-ajax' ) ) {
			do_action( 'wpmg_ajax_' . sanitize_text_field( $action ) );
			die();
		}
	}

	/**
	 * Adds any AJAX-related actions.
	 */
	public function add_ajax_actions() {
		$actions = array(
			'process_import_gallery',
			'process_parse_images_all',
			'process_parse_images_by',
		);

		foreach ( $actions as $action ) {
			add_action( 'wpmg_ajax_' . $action, array( $this, $action ) );
		}
	}

	/**
	 * 
	 * 
	 */
	public function process_import_gallery() {
		// Bail if not authorized.
		if ( ! check_admin_referer( 'ajax_nonce', 'ajax_nonce' ) ) {
			return;
		}

		$gallery_dir  = stripslashes( get_option('gallery_dir') );
        $tax_slug     = stripslashes( get_option('tax_slug') );
        $single_title = stripslashes( get_option('single_title') );
        $cpt_slug     = stripslashes( get_option('cpt_slug') );

		new WP_Migrate_Gallery_Import($cpt_slug, $tax_slug, $single_title, $gallery_dir);

		$result = array(
			'alert' 		=> 'Done, please check your Gallery',
			'error' 		=> 'Failed to fetch data',
			'url' 			=> get_admin_url() . 'tools.php?page=' . WPMG_NAME . '&tab=import',
		);

		// Send output as JSON for processing via AJAX.
		echo json_encode( $result );
		exit;
	}

	/**
	 * 
	 */
	public function process_parse_images_all() {
		// Bail if not authorized.
		if ( ! check_admin_referer( 'ajax_nonce', 'ajax_nonce' ) ) {
			return;
		}

		new WP_Migrate_Gallery_Parser("parse_images_all");

		$result = array(
			'alert' 		=> 'Done, please check History tab',
			'error' 		=> 'Failed to fetch data',
			'url' 			=> get_admin_url() . 'tools.php?page=' . WPMG_NAME . '&tab=parser',
		);

		// Send output as JSON for processing via AJAX.
		echo json_encode( $result );
		exit;
	}

	/**
	 * 
	 */
	public function process_parse_images_by() {
		// Bail if not authorized.
		if ( ! check_admin_referer( 'ajax_nonce', 'ajax_nonce' ) ) {
			return;
		}

		new WP_Migrate_Gallery_Parser("parse_images_by_selector");

		$result = array(
			'alert' 		=> 'Done, please check History tab',
			'error' 		=> 'Failed to fetch data',
			'url' 			=> get_admin_url() . 'tools.php?page=' . WPMG_NAME . '&tab=parser',
		);

		// Send output as JSON for processing via AJAX.
		echo json_encode( $result );
		exit;
	}

}

new WPMG_AJAX;
