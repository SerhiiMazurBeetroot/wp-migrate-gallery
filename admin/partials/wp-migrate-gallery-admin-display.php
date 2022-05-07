<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/SerhiiMazurBeetroot/
 * @since      1.0.0
 *
 * @package    WP_Migrate_Gallery
 * @subpackage WP_Migrate_Gallery/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <div id="icon-themes" class="icon32"></div>
    <h1>Welcome to the new WP project, enjoy it ;)</h1>
    
    <p>The options you can:</p>
    <ul>
        <li>
            <a href="/wp-admin/admin.php?page=<?php echo PLUGIN_NAME; ?>-import">1. Generate Gallery from existing images</a>
        </li>
    </ul>

</div>