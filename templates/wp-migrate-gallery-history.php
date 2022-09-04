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
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <div id="icon-themes" class="icon32"></div>  
    <h1>ZIP Archive</h1>
    
   <?php
    $history = new WP_Migrate_Gallery_History();
    $history->get_archive_list(WPMG_UPLOADS);

    isset($_POST['ajax_nonce']) && isset($_POST['action']) && $history->delete_history(WPMG_UPLOADS);
    ?>

    <!--NEED THE settings_errors below so that the errors/success messages are shown after submission - wasn't working once we started using add_menu_page and stopped using add_options_page so needed this-->
    <div class="panel">
        <div class="panel-content">
            <?php if($history->files) : ?>
                <form name="form1" method="post" action="">
                    <?php wp_nonce_field('form-settings'); ?>
                    <table class="wp-list-table widefat" cellspacing="0">
                        <thead>
                        <tr>
                            <th scope="col" class="manage-column"></th>
                            <th scope="col" class="manage-column">File</th>
                            <th scope="col" class="manage-column">Created</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php $history->generate_table(); ?>
                            <tr><td colspan="3"></td></tr>
                        </tbody>
                    </table>

                    <div id="error_wrap"></div>
                    <div id="submit-wrap">
                        <?php wp_nonce_field( 'delete_archive_history', 'ajax_nonce' ); ?>
                        <input type="hidden" name="action" value="delete_archive_history" />
                        <button id="delete_archive_history" type="submit" class="button button-primary button-lg"><?php _e( 'Delete All', WPMG_NAME ); ?>
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/svg/icon-arrow.svg'; ?>">
                        </button>

                        <div id="loader"></div>
                    </div>
                </form>
            <?php else : ?>
                <h2>No files</h2>
            <?php endif; ?>
        </div>
    </div>
</div>
