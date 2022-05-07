<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/SerhiiMazurBeetroot/
 * @since             1.0.0
 * @package           WP_Migrate_Gallery
 *
 * @wordpress-plugin
 * Plugin Name:       WP Migrate Gallery
 * Plugin URI:        https://github.com/SerhiiMazurBeetroot/wp-migrate-gallery
 * Description:       This plugin was created to help you migrate your existing gallery from your old site.
 * Version:           1.0.0
 * Author:            Serhii Mazur
 * Author URI:        https://github.com/SerhiiMazurBeetroot/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-migrate-gallery
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PLUGIN_NAME', 'wp-migrate-gallery' );

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_MIGRATE_GALLERY_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-migrate-gallery-activator.php
 */
function activate_wp_migrate_gallery() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-migrate-gallery-activator.php';
	WP_Migrate_Gallery_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-migrate-gallery-deactivator.php
 */
function deactivate_wp_migrate_gallery() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-migrate-gallery-deactivator.php';
	WP_Migrate_Gallery_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_migrate_gallery' );
register_deactivation_hook( __FILE__, 'deactivate_wp_migrate_gallery' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-migrate-gallery.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_migrate_gallery() {

	$plugin = new WP_Migrate_Gallery();
	$plugin->run();

}
run_wp_migrate_gallery();
