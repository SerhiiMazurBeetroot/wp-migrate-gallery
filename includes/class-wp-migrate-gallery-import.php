<?php

/**
 * Generate Gallery functionality
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Migrate_Gallery
 * @subpackage Migrate_Gallery/includes
 */

/**
 * Generate Gallery functionality
 *
 *
 * @since      1.0.0
 * @package    Migrate_Gallery
 * @subpackage Migrate_Gallery/includes
 * @author     Serhii Mazur <serhii.mazur@beetroot.se>
 */
class WP_Migrate_Gallery_Import {

    private $galleryDirectory = array();
    private $galleryDirectoryCount = 0;

    /**
     * Gallery constructor
     *
     * @return void
     */
    public function __construct($cpt_slug, $tax_slug, $single_title, $gallery_dir)
    {
        $this->cpt_slug     = $cpt_slug;
        $this->tax_slug     = $tax_slug;
        $this->single_title = $single_title;
        $this->gallery_dir  = WP_CONTENT_DIR . '/' . $gallery_dir;
        $this->get_all_directory_and_files($this->gallery_dir);
        $this->render_gallery_directory();
    }

    public function render_gallery_directory() {
        foreach($this->galleryDirectory as $index => $item) {
            $index++;
            $this->create_gallery_post($item, $index);
        }
    }

    public function get_all_directory_and_files($dir){

        $dh = new DirectoryIterator($dir);  
        
        foreach ($dh as $file_info) {
            if (!$file_info->isDot()) {
                if ($file_info->isDir()) {
                    $this->get_all_directory_and_files("$dir/$file_info");
                } else {
                    if( !in_array($dir, $this->galleryDirectory) ) {
                        $this->galleryDirectory[$this->galleryDirectoryCount] = $dir;
                        $this->galleryDirectoryCount++;
                    }
                }
            }
        }
    }

    public function create_gallery_post($dir, $index) {
        $array_dir = explode("/", $dir);

        // get taxonomy by parent directory
        $term_slug = array_slice($array_dir, -2, 1, true);
        $term_slug = implode("/", $term_slug);

        $term      = get_term_by( 'slug', $term_slug, $this->tax_slug );
        $term_id   = null;

        if (!is_wp_error($term) && !empty($term)) {
            $term_id = $term->term_id;
        }

        // if doesn't exist, create
        if (!$term) {
            // check name
            $term_by_title = term_exists($term_slug);

            if(!$term_by_title) {
                $term = wp_insert_term($term_slug, $this->tax_slug);
            } else {
                $term = wp_insert_term(
                    $term_slug,
                    $this->tax_slug,
                    ['slug' => $term_slug]
                );
            }

            $term_id = $term['term_id'];
        }
        
        $query = get_posts(array(
            'numberposts' => -1,
            'post_type'   => $this->cpt_slug,
            'post_status' => 'publish',
            "title"       => $this->single_title . ' ' . $index
        ));
    
        if ($query) {
            $post_id = $query[0]->ID;

            $update_args = array(
                'ID'         => $post_id,
                'post_title' => $this->single_title . ' ' . $index,
                'post_type'  => $this->cpt_slug,
            );

            wp_update_post($update_args);

        } else {
            $create_args = array(
                'post_title'  => $this->single_title . ' ' . $index,
                'post_status' => 'publish',
                'post_type'   => $this->cpt_slug,
            );

            $post_id = wp_insert_post($create_args);
        }

        // Set taxonomy
        if (!is_wp_error($term) && !empty($term)) {
            wp_set_object_terms($post_id, $term_id, $this->tax_slug);
        }

        $gallery = get_field('gallery', $post_id);
        if (empty($gallery)) {
            $files_post = array();
            $dh = new DirectoryIterator($dir);

            foreach ($dh as $file_info) {

                if (!$file_info->isDot()) {      
                    $file_url        = $dir . "/" . $file_info->getFilename();
                    $ext             = pathinfo($file_url, PATHINFO_EXTENSION);
        
                    if($ext !== 'txt') {
                        // Save images to media
                        $file             = pathinfo($file_info->getFilename(), PATHINFO_FILENAME);
                        $image_name       = $file . '.' . $ext;
                        $upload_dir       = wp_upload_dir();
                        $image_data       = file_get_contents($file_url);
                        $unique_file_name = wp_unique_filename($upload_dir['path'], $image_name);
                        $filename         = basename($unique_file_name);
        
                        if (wp_mkdir_p($upload_dir['path'])) {
                            $file = $upload_dir['path'] . '/' . $filename;
                        } else {
                            $file = $upload_dir['basedir'] . '/' . $filename;
                        }
        
                        file_put_contents($file, $image_data);
            
                        $wp_filetype = wp_check_filetype($filename, null);
            
                        $attachment = array(
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title'     => sanitize_file_name($filename),
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        );
            
                        $attach_id = wp_insert_attachment($attachment, $file);
                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
                        wp_update_attachment_metadata($attach_id, $attach_data);

                        // Save Gallery images to array
                        array_push($files_post, $attach_id);
                    } elseif ($ext === 'txt') {
                        // Save description to field
                        $text_data = file_get_contents($file_url);
                        update_post_meta($post_id, 'description', $text_data);
                    }
                }
            }

            // Save Gallery images to field
            if( !empty($files_post) ) {
                update_post_meta($post_id, 'gallery', $files_post);
            }
        }
    }

}
