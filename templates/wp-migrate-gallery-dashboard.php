<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       #
 * @since      1.1.0
 *
 * @package    WP_Migrate_Gallery
 * @subpackage WP_Migrate_Gallery/templates
 */

// Prevent direct access.
if ( ! defined( 'WPMG_PATH' ) ) exit;

// Determines which tab to display.
$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'import';
?>

<div class="wrap" style="display: grid;">

    <div class="notice-container">
        <h2 class="hidden"></h2>
    </div>

	<div class="header">
		<?php settings_errors(); ?>
	</div>

	<div class="nav-tab-wrapper">
		<ul>
			<li><a href="/wp-admin/admin.php?page=<?php echo WPMG_NAME; ?>&tab=import" class="nav-tab <?php echo $active_tab == 'import' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Import', WPMG_NAME ); ?></a></li>
			<li><a href="/wp-admin/admin.php?page=<?php echo WPMG_NAME; ?>&tab=parser" class="nav-tab <?php echo $active_tab == 'parser' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Parser', WPMG_NAME ); ?></a></li>
			<li><a href="/wp-admin/admin.php?page=<?php echo WPMG_NAME; ?>&tab=history" class="nav-tab <?php echo $active_tab == 'history' ? 'nav-tab-active' : ''; ?>"><?php _e( 'History', WPMG_NAME ); ?></a></li>
		</ul>
	</div>

	<div class="dashboard-wrap">
		<?php
		// Include the correct tab template.
		$page_template = str_replace( '_', '-', sanitize_file_name( $active_tab ) ) . '.php';
		
		if(isset($_GET['error_message'])){
			add_action('admin_notices', array($this,'settingsPageSettingsMessages'));
			do_action( 'admin_notices', $_GET['error_message'] );
		}
		
		if ( file_exists( WPMG_PATH . '/templates/wp-migrate-gallery-' . $page_template ) ) {
			include WPMG_PATH . '/templates/wp-migrate-gallery-' . $page_template;
		} else {
			include WPMG_PATH . '/templates/wp-migrate-gallery-import.php';
		}
		?>
	</div>
</div>
