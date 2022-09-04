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
    <h1>Image Parser</h1>

    <?php
		$site_url       = stripslashes( get_option('site_url') );
		$parser_option  = stripslashes( get_option('parser_option') );
        $container_selector = stripslashes( get_option('container_selector') );
		$image_wrapper      = stripslashes( get_option('image_wrapper') );
        $all_checked    = false;
        $active_tab     = isset( $_GET['tab'] ) ? $_GET['tab'] : 'parser';

        if($parser_option === "Parse images by selector") {
            $all_checked = $site_url && $container_selector && $image_wrapper;
        } else {
            $all_checked = $site_url ? true : false;
        }
    ?>

    <!--NEED THE settings_errors below so that the errors/success messages are shown after submission - wasn't working once we started using add_menu_page and stopped using add_options_page so needed this-->
    <div class="panel">
        <div class="panel-header">
            <h2>1. Please fill in the following information:</h2>
        </div>

        <div class="panel-content">
            <?php settings_errors(); ?>
            <form method="POST" action="options.php">
                <div class="panel-content-wrap">
                    <?php
                        settings_fields( 'wp_migrate_gallery_parser_settings' );
                        do_settings_sections( 'wp_migrate_gallery_parser_settings' );
                    ?>

                    <div class="wrapper_by_selector">
                        <h3>Example of html:</h3>
                        <img src="<?php echo plugin_dir_url( __DIR__ ) . '/assets/img/parser.png' ?>" alt="Parser example"></br></br>
                    </div>
                </div>
                <?php submit_button('Save'); ?>
            </form>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2>2. Check & click:</h2>
        </div>

        <div class="panel-content">
            <form method="POST" action="/wp-admin/admin.php?page=<?php echo WPMG_NAME; ?>&tab=<?php echo $active_tab; ?>">

                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th>1. Required fields are filled</th>
                            <td>
                                <?php if($site_url) {
                                    echo '<input name="acf" disabled checked="checked" type="checkbox">';
                                } else {
                                    echo '<input name="acf" disabled type="checkbox">';
                                } ?>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <?php if( $all_checked ) { ?>
                    <div id="notice_wrap"></div>
                    <div id="submit-wrap">
                        <?php wp_nonce_field( 'process_parse_images', 'ajax_nonce' ); ?>
                        <input type="hidden" name="action" value="process_parse_images" />
                        <button id="parse_images" type="submit" class="button button-primary button-lg"><?php _e( 'Run Parser', WPMG_NAME ); ?>
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/svg/icon-arrow.svg'; ?>">
                        </button>

                        <div id="loader"></div>
                    </div>
                <?php } ?>
            </form>
        </div>
    </div>

</div>
