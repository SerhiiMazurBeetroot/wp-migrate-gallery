<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    WP_Migrate_Gallery
 * @subpackage WP_Migrate_Gallery/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <div id="icon-themes" class="icon32"></div>  
    <h1>Migrate Gallery Result</h1>

    <?php
        $gallery_dir  = stripslashes( get_option('gallery_dir') );
        $tax_slug     = stripslashes( get_option('tax_slug') );
        $single_title = stripslashes( get_option('single_title') );
        $cpt_slug     = stripslashes( get_option('cpt_slug') );
        $acf_exists   = in_array( 'advanced-custom-fields-pro/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
        $cpt_exists   = post_type_exists( $cpt_slug );
        $tax_exists   = taxonomy_exists( $tax_slug );
    ?>

    <?php if ( !empty( $gallery_dir ) ) {
        new WP_Migrate_Gallery_Import($cpt_slug, $tax_slug, $single_title, $gallery_dir);
    } ?>

    <p>Done, please check your Gallery</p>
</div>
