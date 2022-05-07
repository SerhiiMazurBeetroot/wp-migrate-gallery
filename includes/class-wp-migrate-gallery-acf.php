<?php

/**
 * ACF Local JSON
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Migrate_Gallery
 * @subpackage Migrate_Gallery/includes
 */

/**
 * ACF Local JSON
 *
 *
 * @since      1.0.0
 * @package    Migrate_Gallery
 * @subpackage Migrate_Gallery/includes
 * @author     Serhii Mazur <serhii.mazur@beetroot.se>
 */

add_filter('acf/settings/save_json', 'lit_acf_json_save_point');
/**
 * Adds plugin directory as new location for ACF Pro to save JSON
 *
 * @since 1.0.0
 *
 * @param $path Original path
 * @return new path as /acf-json/
 */

function lit_acf_json_save_point( $path ) {
    
    // update path
    $path = plugin_dir_path( __FILE__ ) . 'acf-json';
    
    // return
    return $path;
    
}

add_filter('acf/settings/load_json', 'lit_acf_json_load_point', 9999);

/**
 * Adds plugin directory as new location for ACF Pro to load JSON
 *
 * @since 1.0.0
 *
 * @param array $paths Original path
 * @return new path as /acf-json/
 */

function lit_acf_json_load_point( $paths ) {
    
    // remove original path
    unset($paths[0]);
    
    // append path
    $paths[] = plugin_dir_path( __FILE__ ) . 'acf-json';

    // return
    return $paths;
    
}
