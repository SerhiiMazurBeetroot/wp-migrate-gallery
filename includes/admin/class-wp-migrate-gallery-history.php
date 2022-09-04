<?php

/**
 * Generate Gallery functionality
 *
 * @link       #
 * @since      1.1.0
 *
 * @package    Migrate_Gallery
 * @subpackage Migrate_Gallery/includes/admin
 */

/**
 * Generate Gallery functionality
 *
 *
 * @since      1.1.0
 * @package    Migrate_Gallery
 * @subpackage Migrate_Gallery/includes
 * @author     Serhii Mazur <serhii.mazur@beetroot.se>
 */
class WP_Migrate_Gallery_History {

    public $files;
    public $instances;

    /**
     * constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->get_archive_list();
    }
      
    public function get_archive_list()
    {
        $files = array();

        if(is_dir(WPMG_UPLOADS)) {
            $dir = new DirectoryIterator(WPMG_UPLOADS);

            foreach ($dir as $fileinfo) {
                if ($fileinfo != "." && $fileinfo != "..") {
                    $ext = pathinfo($fileinfo);
                    if ( isset($ext['extension']) && $ext['extension'] == 'zip') {
                    $files[$fileinfo->getMTime()] = $fileinfo->getFilename();
                    }
                }
            }

            krsort($files);
            $this->files = $files;
        }
    }

    public function generate_table()
    {
        $i = 1;
        $counter = 0;

        if($this->files) {
            foreach($this->files as $file) {
                $filename = WPMG_UPLOADS . $file;

                if(file_exists($filename)) {
                    $this->print_table_row($file, $filename, $counter, $i);
                    $i++;
                    $counter++;
                }
            }
        }

    }

    private function print_table_row($file, $filename, $counter, $i)
    {
        $created_at = date('M j, Y g:i:s A', filemtime($filename));
        $size = filesize($filename);
        $units = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $step = 1024;
        $k = 0;

        while (($size / $step) > 0.9) {
            $size = $size / $step;
            $k++;
        } ?>

        <tr <?php echo ($counter % 2 === 0) ? 'class="alternate"' : '' ?> >
            <td><?php echo $i ?></td>
            <td>
                <a href="<?php echo WP_CONTENT_URL . '/uploads/wpmg_uploads/' . $file ?>"><?php echo $file ?></a>
                <span> &#8211; <?php echo round($size, 2).$units[$k] ?></span>
            </td>
            <td nowrap><?php echo $created_at; ?></td>
        </tr>

    <? 
    }

    public function delete_history($path)
    {
        // Open the source directory to read in files
        $i = new DirectoryIterator($path);

        foreach($i as $f) {
            if($f->isFile()) {
                unlink($f->getRealPath());
            } else if(!$f->isDot() && $f->isDir()) {
                $this->delete_history($f->getRealPath());
            }
        }
        rmdir($path);
    }
}
