<?php

// Assuming you installed from Composer:
require WPMG_PATH . "vendor/autoload.php";

use PHPHtmlParser\Dom;

ini_set('user_agent', 'My-Application/2.5');


/**
 * Generate Gallery functionality
 *
 *
 * @since      1.1.0
 * @package    Migrate_Gallery
 * @subpackage Migrate_Gallery/includes/admin
 * @author     Serhii Mazur <serhii.mazur@beetroot.se>
 */
class WP_Migrate_Gallery_Parser {

    public function __construct($type)
    {
        $this->sourceUrl    = stripslashes( get_option('site_url') );
        $this->gallery_dir  = WPMG_UPLOADS;
        $this->gallery_temp = $this->gallery_dir . 'temp';

        $this->init_PHPHtmlParser();

        if($type === "parse_images_by_selector") {
            $this->parse_images_by_selector();
        } else if($type === "parse_images_all") {
            $this->parse_images_all();
        }
    }


    public function init_PHPHtmlParser()
    {
        $this->dom = new Dom;
        $this->dom->load($this->sourceUrl);
    }


    public function parse_images_all()
    {
        $store = [];
        $this->wpmg_create_dir($this->gallery_temp);
        $posts = $this->dom->find('img');

        if($posts) {
            foreach ($posts as $key => $post) {
                $src = $post->getAttribute('src');
                $src = $this->wpmg_get_image_url($src);
                $src && array_push($store, $src);
            }
    
            // Save images to temp dir
            foreach( $store as $i => $image_url ) {
                $this->wpmg_save_image($image_url, $this->gallery_temp);
            }
    
            // Compress files to zip
            $this->wpmg_create_zip();
        }
    }


    public function parse_images_by_selector()
    {
        $container_selector = stripslashes( get_option('container_selector') );
		$image_wrapper      = stripslashes( get_option('image_wrapper') );
		$dir_title          = stripslashes( get_option('dir_title') );
		$subdir_title       = stripslashes( get_option('subdir_title') );

        $store = [];
        $this->wpmg_create_dir($this->gallery_temp);
        $posts = $this->dom->find($container_selector);

        foreach ($posts as $key => $post) {
            $has_attr_title = $dir_title ? $post->getAttribute($dir_title) : false;
            $title = $dir_title && $has_attr_title ? $has_attr_title : "DIR-" . $key;
            $store[$key]['DIR'] = $title;

            if($image_wrapper) {
                $images_by_wrapper = $post->find($image_wrapper);

                foreach ($images_by_wrapper as $index => $image_by_wrapper) {
                    $subdir_title = $subdir_title ? $subdir_title : 'Single';
                    $store[$key]['single'][$index]['title'] = $subdir_title . '-' . ++$index;

                    $images = [];
                    $img_count = count($image_by_wrapper->find('img'));

                    for($i = 0; $i < $img_count; $i++) {
                        $src = $image_by_wrapper->find('img')[$i]->getAttribute('src');

                        $src = $this->wpmg_get_image_url($src);
                        $src && $images[$i] = $src;

                    }
                    $store[$key]['single'][$index]['images'] = $images;
                }
            }

        }

        // Save images to temp dir
        foreach( $store as $i => $item ) {
            if(isset($item['single'])) {
                foreach($item['single'] as $patient) {
                    $newDir = $this->gallery_temp .'/'. $item['DIR'].'/'.$patient['title'] .'/';

                    if(isset($patient['images'])) {
                        foreach($patient['images'] as $image_url) {
                            $this->wpmg_save_image($image_url, $newDir);
                        }
                    }
                }

            }
        }

        // Compress files to zip
        $this->wpmg_create_zip();
    }


    private function wpmg_save_image($imageUrl, $newDir)
    {
        $this->wpmg_create_dir($newDir);

        $nameExplodeArray = explode('/', $imageUrl);
        $nameExplodeArray = array_reverse($nameExplodeArray);
        $image_name = $nameExplodeArray[0];

        $opts = array(
            'http' => array(
                'method' => 'GET',
                'header' => "User-Agent: lashaparesha api script\r\n",
            ),
        );

        $context = stream_context_create($opts);
        $image_data = file_get_contents($imageUrl, false, $context);
        $unique_file_name = wp_unique_filename($newDir, $image_name);
        $filename = basename($unique_file_name); // Create image file name
        $file = $newDir.'/'.$filename;

        // Create the image  file on the server
        file_put_contents($file, $image_data);
    }


    public function wpmg_get_image_url($src)
    {
        $is_image = $this->wpmg_is_image_file($src);

        if( $is_image ) {
            // Check if image has full url
            if (strpos($src, $this->sourceUrl) === false) {
                if (strpos($src, 'http') === false) {
                    $src = $this->sourceUrl . $src;
                }
            }

            return $src;
        }
        return false;
    }


    private function wpmg_is_image_file($file) {
        $info = pathinfo($file);
        $extension = isset($info['extension']) ? $info['extension'] : false;

        if($extension) {
            return in_array(strtolower($extension), array("jpg", "jpeg", "gif", "png", "bmp", "webp"));
        } else {
            return false;
        }
    }


    private function wpmg_create_dir($newDir)
    {
        try {
            wp_mkdir_p($newDir);
            chmod($newDir, 0777);
        } catch(ErrorException $ex) {
           // echo "Error: " . $ex->getMessage();
        }
    }

      
    private function wpmg_rmrf($dir)
    {
        foreach (glob($dir) as $file) {
            if (is_dir($file)) {
                $this->wpmg_rmrf("{$file}/*");
                rmdir($file);
            } else {
                unlink($file);
            }
        }
    }


    private function wpmg_create_zip()
    {
        $parts = parse_url($this->sourceUrl);
        $parts = urlencode($parts['host']);
        $parts = str_replace(".", "", $parts);
		$archiveFile = $parts . "-" . time();

		$folder = $this->gallery_temp;
		$filename = $this->gallery_dir . "/" . $archiveFile . '.zip';
		$zip = new ZipArchive();
		$zip->open($filename, ZipArchive::CREATE);
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder), RecursiveIteratorIterator::LEAVES_ONLY);

		foreach ($files as $name => $file)
		{
			// Skip directories (they would be added automatically)
			if (!$file->isDir())
			{
				// Get real and relative path for current file
				$filePath = $file->getRealPath();
				$relativePath = substr($filePath, strlen($folder) + 1);
	  
				// Add current file to archive
				$zip->addFile($filePath, $relativePath);
			}
		}

		$zip->close();
		$this->wpmg_rmrf($folder); // delete temporary files
	}
}
