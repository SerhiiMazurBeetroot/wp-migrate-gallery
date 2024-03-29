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
  <h1>Import Gallery</h1>

  <?php
    $gallery_dir    = stripslashes( get_option('gallery_dir') );
    $gallery_path   = $gallery_dir ? WP_CONTENT_DIR . '/' . $gallery_dir : false;
    $tax_slug       = stripslashes( get_option('tax_slug') );
    $single_title   = stripslashes( get_option('single_title') );
    $cpt_slug       = stripslashes( get_option('cpt_slug') );
    $acf_exists     = in_array( 'advanced-custom-fields-pro/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
    $cpt_exists     = post_type_exists( $cpt_slug );
    $tax_exists     = taxonomy_exists( $tax_slug );
    $gallery_exists = is_dir($gallery_path) ? true : false;
    $all_checked    = $gallery_dir && $tax_exists && $single_title && $cpt_slug && $acf_exists && $cpt_exists && $gallery_exists;
    $active_tab     = isset( $_GET['tab'] ) ? $_GET['tab'] : 'import';
  ?>

  <div class="panel">
    <div class="panel-header">
      <h2>1. Steps:</h2>
    </div>

    <ul class="panel-content">
      <li>1.1. Install ACF PRO</li>
      <li>1.2. Create CPT Gallery</li>
      <li>1.3. Create  Gallery Taxomy</li>
      <li>1.4. Copy Gallery media files to project. This directory should have the following structure:</br></br>
        <img src="<?php echo plugin_dir_url( __DIR__ ) . '/assets/img/tree.png' ?>" alt="Gallery Tree"></br></br>
      </li>
      <li>1.5. Synchronized ACF JSON Custom Fields
        <?php if($acf_exists) { ?>
          <a href="/wp-admin/edit.php?post_type=acf-field-group&post_status=sync">here</a>
        <?php } ?>
      </li>
      <li>1.6. Fill in the information from the next point</li>
    </ul>
  </div>

  <!--NEED THE settings_errors below so that the errors/success messages are shown after submission - wasn't working once we started using add_menu_page and stopped using add_options_page so needed this-->
  <div class="panel">
    <div class="panel-header">
        <h2>2. Please fill in the following information:</h2>
    </div>

    <div class="panel-content">
        <?php settings_errors(); ?>
        <form method="POST" action="options.php">
            <?php
                settings_fields( 'wp_migrate_gallery_import_settings' );
                do_settings_sections( 'wp_migrate_gallery_import_settings' );
                submit_button('Save');
            ?>
        </form>
    </div>
  </div>


  <div class="panel">
      <div class="panel-header">
          <h2>3. Checking before continue:</h2>
      </div>

      <div class="panel-content">
          <form method="POST" action="/wp-admin/admin.php?page=<?php echo WPMG_NAME; ?>&tab=<?php echo $active_tab; ?>">
              
              <table class="form-table" role="presentation">
                  <tbody>
                      <tr>
                          <th>1. ACF PRO plugin exists</th>
                          <td>
                              <?php if($acf_exists) {
                                  echo '<input name="acf" disabled checked="checked" type="checkbox">';
                              } else {
                                  echo '<input name="acf" disabled type="checkbox">';
                              } ?>
                          </td>
                      </tr>

                      <tr>
                          <th>2. CPT Gallery exists</th>
                          <td>
                              <?php if($cpt_exists) { ?>
                                  <input name="cpt" disabled checked="checked" type="checkbox">
                              <?php } else { ?>
                                  <input name="cpt" disabled type="checkbox">
                              <?php } ?>
                          </td>
                      </tr>

                      <tr>
                          <th>3. Gallery Taxonomy exists</th>
                          <td>
                              <?php if($tax_exists) { ?>
                                  <input name="cpt" disabled checked="checked" type="checkbox">
                              <?php } else { ?>
                                  <input name="cpt" disabled type="checkbox">
                              <?php } ?>
                          </td>
                      </tr>

                      <tr>
                          <th>4. Gallery DIR exists</th>
                          <td>
                              <?php if( is_dir($gallery_path) ) { ?>
                              <input name="cpt" disabled checked="checked" type="checkbox">
                              <?php } else { ?>
                                  <input name="cpt" disabled type="checkbox">
                              <?php } ?>
                          </td>
                      </tr>

                  </tbody>
              </table>

              <?php if( $all_checked ) { ?>
                  <div id="error_wrap"></div>
                  <!--Submit Button-->
                  <div id="submit-wrap">
                      <?php wp_nonce_field( 'process_import_gallery', 'ajax_nonce' ); ?>
                      <input type="hidden" name="action" value="process_import_gallery" />
                      <button id="import_submit" type="submit" class="button button-primary button-lg"><?php _e( 'Run Import', WPMG_NAME ); ?>
                          <img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/svg/icon-arrow.svg'; ?>">
                      </button>

                      <div id="loader"></div>
                  </div>

              <?php } ?>
          </form>
      </div>
  </div>
</div>
