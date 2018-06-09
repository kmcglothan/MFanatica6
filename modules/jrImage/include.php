<?php
/**
 * Jamroom Image Support module
 *
 * copyright 2018 The Jamroom Network
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0.  Please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
 *
 * Jamroom may use modules and skins that are licensed by third party
 * developers, and licensed under a different license  - please
 * reference the individual module or skin license that is included
 * with your installation.
 *
 * This software is provided "as is" and any express or implied
 * warranties, including, but not limited to, the implied warranties
 * of merchantability and fitness for a particular purpose are
 * disclaimed.  In no event shall the Jamroom Network be liable for
 * any direct, indirect, incidental, special, exemplary or
 * consequential damages (including but not limited to, procurement
 * of substitute goods or services; loss of use, data or profits;
 * or business interruption) however caused and on any theory of
 * liability, whether in contract, strict liability, or tort
 * (including negligence or otherwise) arising from the use of this
 * software, even if advised of the possibility of such damage.
 * Some jurisdictions may not allow disclaimers of implied warranties
 * and certain statements in the above disclaimer may not apply to
 * you as regards implied warranties; the other terms and conditions
 * remain enforceable notwithstanding. In some jurisdictions it is
 * not permitted to limit liability and therefore such limitations
 * may not apply to you.
 *
 * @copyright 2012 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrImage_meta
 */
function jrImage_meta()
{
    $_tmp = array(
        'name'        => 'Image Support',
        'url'         => 'image',
        'version'     => '2.0.9',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Core support for displaying, resizing and manipulating images',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2863/image-support',
        'category'    => 'core',
        'license'     => 'mpl',
        'priority'    => 1, // HIGHEST load priority
        'requires'    => 'jrCore:6.1.0b1,jrSystemTools',
        'locked'      => true,
        'activate'    => true
    );
    return $_tmp;
}

/**
 * jrImage_init
 */
function jrImage_init()
{
    // Our image module provides the "image" magic view
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrImage', 'image', 'jrImage_display_image');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrImage', 'default_img', 'jrImage_display_default_image');

    // Register our tools
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrImage', 'cache_reset', array('Reset Image Cache', 'Resets the resized image cache'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrImage', 'limit_image_size', array('Resize Item Images', 'Limit item images to a maximum height or width'));

    // We also provide support for the "image" form field type
    jrCore_register_module_feature('jrCore', 'form_field', 'jrImage', 'image');

    // We will check our own sessions
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrImage', 'image', 'magic_view');
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrImage', 'img');
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrImage', 'default_img');

    // We're going to listen to the "save_media_file" event so we can add image specific fields to the item
    jrCore_register_event_listener('jrCore', 'save_media_file', 'jrImage_save_media_file_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrImage_verify_module_listener');

    // We also provide a "require_image" parameter to the jrCore_db_search_item function
    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrImage_db_search_params_listener');
    jrCore_register_event_listener('jrCore', 'db_update_item', 'jrImage_db_update_item_listener');
    jrCore_register_event_listener('jrCore', 'hourly_maintenance', 'jrImage_hourly_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'upload_saved', 'jrImage_upload_saved_listener');

    // Triggers
    jrCore_register_event_trigger('jrImage', 'img_src', 'Fired before returning image src parameter in jrImage_display');
    jrCore_register_event_trigger('jrImage', 'module_image', 'Fired with img src when showing a module image via the image/img view');
    jrCore_register_event_trigger('jrImage', 'skin_image', 'Fired with img src when showing a skin image via the image/img view');
    jrCore_register_event_trigger('jrImage', 'default_image', 'Fired before displaying the default image');
    jrCore_register_event_trigger('jrImage', 'item_image_info', 'Fired with item information before displaying image');
    jrCore_register_event_trigger('jrImage', 'get_allowed_image_widths', 'Fired with an array of allowed image widths');

    // Register our CSS
    jrCore_register_module_feature('jrCore', 'css', 'jrImage', 'jrImage.css');

    // We provide some dashboard panels
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrImage', 'image cache size', 'jrImage_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrImage', 'cached image count', 'jrImage_dashboard_panels');

    // Cache workers
    jrCore_register_queue_worker('jrImage', 'clear_cache', 'jrImage_clear_cache_worker', 0, 1, 7200, HIGH_PRIORITY_QUEUE);
    jrCore_register_queue_worker('jrImage', 'prune_cache', 'jrImage_prune_cache_worker', 0, 1, 3500, LOW_PRIORITY_QUEUE);
    jrCore_register_queue_worker('jrImage', 'resize_animated', 'jrImage_resize_animated_worker', 0, 1, 900);

    return true;
}

//---------------------------
// QUEUE WORKER
//---------------------------

/**
 * Resize Animated GIF images
 * @param array $_queue The queue entry the worker will receive
 * @return bool
 */
function jrImage_resize_animated_worker($_queue)
{
    jrImage_resize_animated_gif($_queue['input_file'], $_queue['_image'], $_queue['_post'], $_queue['cache_file']);
    return true;
}

/**
 * Clear Image Cache
 * @param array $_queue The queue entry the worker will receive
 * @return bool
 */
function jrImage_clear_cache_worker($_queue)
{
    $cdr = jrCore_get_module_cache_dir('jrImage');
    $dir = $_queue['old_cache_dir'];
    // Check for our old directory
    if (is_dir("{$cdr}/{$dir}")) {
        // Delete existing cache directory
        jrCore_delete_dir_contents("{$cdr}/{$dir}");
        rmdir("{$cdr}/{$dir}");
    }
    return true;
}

/**
 * Prune old entries in the image cache
 * @param array $_queue
 * @return mixed
 */
function jrImage_prune_cache_worker($_queue)
{
    global $_conf;
    $days = (int) $_conf['jrImage_clean_days'];
    if (isset($_queue['clean_days'])) {
        $days = $_queue['clean_days'];
    }
    if ($days > 0) {

        // Our cache directory is multi level:

        // Profile cache directory structure:
        // cache_dir/[0123]  <- first level is privacy
        // cache_dir/[0123]/[0-9]+  <- next level is directory group
        // cache_dir/[0123][/0-9]+/[0-9]+  <- next level is profile_id
        // cache_dir/[0123][/0-9]+/[0-9]+/[module_dir]  <- next level is module directory
        // cache_dir/[0123][/0-9]+/[0-9]+/[module_dir]/[0-9]+  <- final level is the item_id

        // Cache image structure:
        // cache_dir/[module_dir]  <- first level is module
        // cache_dir/[module_dir]/[0-9]+  <- second level is directory group
        // cache_dir/[module_dir]/[0-9]+/[0-9]*  <- last is item_id SYMBOLIC link to item_id directory in profile cache

        $cdr = jrCore_get_module_cache_dir('jrImage') . "/{$_conf['jrImage_active_cache_dir']}";
        if (!is_dir($cdr)) {
            return true;
        }

        $dif = (int) ($days * 86400);
        $old = (time() - $dif);

        $c = 0;
        $s = 0;
        if ($f = opendir($cdr)) {
            while (false !== ($dir1 = readdir($f))) {
                // We only look in the module image cache dirs
                if ($dir1 == '.' || $dir1 == '..' || is_numeric($dir1)) {
                    continue;
                }
                if (is_dir("{$cdr}/{$dir1}")) {
                    // dir1 will be like cache_dir/jrGallery
                    if ($dir2 = @opendir("{$cdr}/{$dir1}")) {
                        while (false !== ($grp = readdir($dir2))) {
                            if ($grp == '.' || $grp == '..') {
                                continue;
                            }
                            // $grp will be the item group directory
                            if ($links = @opendir("{$cdr}/{$dir1}/{$grp}")) {
                                while (false !== ($link = readdir($links))) {
                                    if ($link == '.' || $link == '..') {
                                        continue;
                                    }
                                    if (is_link("{$cdr}/{$dir1}/{$grp}/{$link}")) {
                                        if ($_rt = jrImage_prune_image_symlink_dir("{$cdr}/{$dir1}/{$grp}/{$link}", $old)) {
                                            $c += $_rt[0];
                                            $s += $_rt[1];
                                            // DO NOT set clean_max when running as a queue entry!
                                            if (isset($_queue['clean_max']) && $c > $_queue['clean_max']) {
                                                return $c;
                                            }
                                        }
                                    }
                                }
                                closedir($links);
                            }
                        }
                        closedir($dir2);
                    }
                }
            }
            closedir($f);
        }

        // DO NOT set clean_max when running as a queue entry!
        if (isset($_queue['clean_max']) && $c > $_queue['clean_max']) {
            return $c;
        }

    }

    // We also need to remove any broken symbolic links
    jrImage_delete_broken_cache_symlinks();
    return true;
}

/**
 * Prune images from an image cache directory that have not been accessed in $time
 * @param string $link
 * @param int $time
 * @return array
 */
function jrImage_prune_image_symlink_dir($link, $time)
{
    // We return count of images removed + size
    $_rt = array(0, 0);
    if ($dir = realpath($link)) {
        if ($dir && is_dir($dir)) {
            // We now have the real path to this individual item's cache directory
            if ($f = opendir($dir)) {
                while (false !== ($img = readdir($f))) {
                    if ($img == '.' || $img == '..') {
                        continue;
                    }
                    if ($atime = fileatime("{$dir}/{$img}")) {
                        if ($atime < $time) {
                            $_rt[0]++;
                            $_rt[1] += filesize("{$dir}/{$img}");
                            unlink("{$dir}/{$img}");
                        }
                    }
                }
                closedir($f);
            }
        }
    }
    else {
        unlink($link);
    }
    return $_rt;
}

//---------------------------
// DASHBOARD
//---------------------------

/**
 * User Profiles Dashboard Panels
 * @param $panel
 * @return bool|int
 */
function jrImage_dashboard_panels($panel)
{
    global $_conf;
    // The panel being asked for will come in as $panel
    if ($out = jrCore_is_cached('jrImage', $panel, false, false)) {
        return $out;
    }
    $out = false;
    switch ($panel) {

        case 'image cache size':
            if (function_exists('system')) {
                $dir = jrCore_get_module_cache_dir('jrImage');
                $dir = "{$dir}/{$_conf['jrImage_active_cache_dir']}";
                if (is_dir($dir)) {
                    ob_start();
                    system("/usr/bin/du -sk {$dir} 2>/dev/null", $ret);
                    $out = (int) ob_get_clean();
                    $out *= 1024;

                    $kb = 1024;
                    $mb = 1024 * $kb;
                    $gb = 1024 * $mb;
                    $tb = 1024 * $gb;

                    $mod = '';
                    $siz = 0;
                    if ($out < $kb) {
                        $mod = 'bytes';
                    }
                    elseif ($out < $mb) {
                        $mod = 'kilobytes';
                        $siz = round($out / $kb);
                    }
                    elseif ($out < $gb) {
                        $mod = 'megabytes';
                        $siz = round($out / $mb, 1);
                    }
                    elseif ($out < $tb) {
                        $mod = 'gigabytes';
                        $siz = round($out / $gb, 2);
                    }
                    $out = array(
                        'title' => "{$siz}<br><span>{$mod}</span>"
                    );
                }
            }
            break;

        case 'cached image count':
            if (function_exists('system')) {
                $dir = jrCore_get_module_cache_dir('jrImage');
                $dir = "{$dir}/{$_conf['jrImage_active_cache_dir']}";
                if (is_dir($dir)) {
                    ob_start();
                    system("/usr/bin/find " . escapeshellarg($dir) . " -type f | wc -l 2>/dev/null", $ret);
                    $out = ob_get_clean();
                    $out = array(
                        'title' => jrCore_number_format(intval($out))
                    );
                }
            }
            break;

    }
    if ($out) {
        jrCore_add_to_cache('jrImage', $panel, $out, 30, 0, false, false);
        return $out;
    }
    return false;
}

//-------------------------
// EVENT LISTENERS
//-------------------------

/**
 * Enforce minimum image width if configured
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrImage_upload_saved_listener($_data, $_user, $_conf, $_args, $event)
{
    // Make sure if uploaded file is an image, it's a valid image
    if (isset($_data['file_extension']) && strlen($_data['file_extension']) > 0) {
        $ext = strtolower($_data['file_extension']);
        switch ($ext) {
            case 'jpg':
            case 'jpe':
            case 'jpeg':
            case 'jfif':
            case 'jfi':
            case 'png':
            case 'gif':
                $_im = getimagesize($_data['temp_name']);
                if (!$_im || !is_array($_im)) {
                    // This is NOT a valid image
                    $_ln = jrUser_load_lang_strings();
                    return array('error' => $_ln['jrImage'][5]);
                }
                // Are we configured for minimum image width?
                if (!jrUser_is_admin() && isset($_conf['jrImage_minimum_width']) && jrCore_checktype($_conf['jrImage_minimum_width'], 'number_nz')) {
                    if ($_im[0] < $_conf['jrImage_minimum_width']) {
                        // This image is too small
                        $_ln = jrUser_load_lang_strings();
                        return array('error' => str_replace('%1', $_conf['jrImage_minimum_width'], $_ln['jrImage'][6]));
                    }
                }
                switch ($ext) {
                    case 'png':
                    case 'gif':
                        if (isset($_conf['jrImage_convert_to_jpg']) && $_conf['jrImage_convert_to_jpg'] == 'on') {
                            $cnv = true;
                            // If this is a transparent PNG, no conversion...
                            if ($ext == 'png' && jrImage_is_alpha_png($_data['temp_name'])) {
                                $cnv = false;
                            }
                            elseif ($ext == 'gif' && jrImage_is_animated_gif($_data['temp_name'])) {
                                $cnv = false;
                            }
                            if ($cnv) {
                                if ($ext == 'png') {
                                    $src = @imagecreatefrompng($_data['temp_name']);
                                }
                                else {
                                    $src = @imagecreatefromgif($_data['temp_name']);
                                }
                                if ($src) {
                                    // Update to JPG and save new name
                                    imagejpeg($src, $_data['temp_name'], 85);
                                    imagedestroy($src);
                                    jrCore_write_to_file("{$_data['temp_name']}.tmp", str_ireplace(".{$ext}", '.jpg', $_data['file_name']));
                                }
                            }
                        }
                        break;

                    case 'jpg':
                    case 'jpe':
                    case 'jpeg':
                    case 'jfi':
                    case 'jfif':

                        if (function_exists('exif_read_data')) {
                            $_tmp = @exif_read_data($_data['temp_name']);
                            if ($_tmp && is_array($_tmp) && isset($_tmp['Orientation'])) {
                                switch (intval($_tmp['Orientation'])) {
                                    case 0:
                                    case 1:
                                        // No change
                                        $f = false;
                                        $r = 0;
                                        break;
                                    case 2:
                                        // Flip
                                        $f = true;
                                        $r = 0;
                                        break;
                                    case 3:
                                        // Rotate Right 180 Degrees
                                        $f = false;
                                        $r = 180;
                                        break;
                                    case 4:
                                        // Flip and Rotate Right 180 Degrees
                                        $f = true;
                                        $r = 180;
                                        break;
                                    case 5:
                                        // Flip and Rotate Right 90 Degrees
                                        $f = true;
                                        $r = 270;
                                        break;
                                    case 6:
                                        // Rotate Right 90 degrees
                                        $f = false;
                                        $r = 270;
                                        break;
                                    case 7:
                                        // Flip and Rotate Right 270 degrees
                                        $f = true;
                                        $r = 90;
                                        break;
                                    case 8:
                                        // Rotate Right 270 degrees
                                        $f = false;
                                        $r = 90;
                                        break;
                                    default:
                                        $f = false;
                                        $r = 0;
                                        break;
                                }
                                if ($f || $r > 0) {
                                    $src = imagecreatefromjpeg($_data['temp_name']);
                                    $new = false;
                                    if ($r > 0) {
                                        // Rotate
                                        $new = imagerotate($src, $r, 0);
                                    }
                                    if ($f) {
                                        // Flip
                                        if ($new) {
                                            $new = imageflip($new, IMG_FLIP_VERTICAL);
                                        }
                                        else {
                                            $new = imageflip($src, IMG_FLIP_VERTICAL);
                                        }
                                    }
                                    imagejpeg($new, $_data['temp_name'], 85);
                                    imagedestroy($src);
                                    imagedestroy($new);
                                }
                            }
                        }
                        break;
                }
                break;
        }
    }
    return $_data;
}

/**
 * Make sure our cache directory exists
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrImage_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    $cdr = jrCore_get_module_cache_dir('jrImage');
    if (!is_dir("{$cdr}/{$_conf['jrImage_active_cache_dir']}")) {
        @mkdir("{$cdr}/{$_conf['jrImage_active_cache_dir']}", $_conf['jrCore_dir_perms'], true);
    }
    else {
        // Make sure permissions are correct
        @chmod("{$cdr}/{$_conf['jrImage_active_cache_dir']}", $_conf['jrCore_dir_perms']);
    }
    return $_data;
}

/**
 * Keeps image cache cleaned up
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return mixed
 */
function jrImage_hourly_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_conf['jrImage_clean_days']) && jrCore_checktype($_conf['jrImage_clean_days'], 'number_nz')) {
        jrCore_queue_create('jrImage', 'prune_cache', array('time' => time()), 30, null, 1);
    }
    return $_data;
}

/**
 * Adds width/height keys to saved media info
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrImage_save_media_file_listener($_data, $_user, $_conf, $_args, $event)
{
    // See if we are getting an image file upload...
    if (!isset($_data["{$_args['file_name']}_extension"]) || !isset($_args['_file']['tmp_name']) || !is_file($_args['_file']['tmp_name'])) {
        return $_data;
    }

    // Add Width / Height
    switch ($_data["{$_args['file_name']}_extension"]) {
        case 'png':
        case 'gif':
        case 'jpg':
        case 'jpe':
        case 'jpeg':
        case 'jfi':
        case 'jfif':
            $_tmp                                  = getimagesize($_args['_file']['tmp_name']);
            $_data["{$_args['file_name']}_width"]  = (int) $_tmp[0];
            $_data["{$_args['file_name']}_height"] = (int) $_tmp[1];
            break;
    }
    return $_data;
}

/**
 * Adds support for "require_image" to jrCore_db_search_items()
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrImage_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    $done = false;
    // require_image_width="width"
    if (isset($_data['require_image']{0})) {
        if (isset($_data['require_image_width'])) {
            if (!isset($_data['search'])) {
                $_data['search'] = array();
            }
            $_data['search'][] = "{$_data['require_image']}_width >= " . intval($_data['require_image_width']);
            $done              = true;
        }
        // require_image_height="height"
        if (isset($_data['require_image_height'])) {
            if (!isset($_data['search'])) {
                $_data['search'] = array();
            }
            $_data['search'][] = "{$_data['require_image']}_height >= " . intval($_data['require_image_height']);
            $done              = true;
        }
        // require_image="profile_image"
        if (!$done) {
            // We need to add in the SQL "where" clause that the TYPE of image
            // received must be larger than 0 bytes
            if (!isset($_data['search'])) {
                $_data['search'] = array();
            }
            $_data['search'][] = "{$_data['require_image']}_size > 0";
        }
    }
    return $_data;
}

/**
 * Reset profile image cache on privacy change
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrImage_db_update_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['module']) && $_args['module'] == 'jrProfile' && isset($_args['_item_id']) && jrCore_checktype($_args['_item_id'], 'number_nz') && isset($_data['profile_private'])) {
        // Is this profile changing their privacy setting? Reset profile image cache directories
        if ($existing = jrCore_db_get_item_key('jrProfile', $_args['_item_id'], 'profile_private')) {
            if (intval($existing) != intval($_data['profile_private'])) {
                jrImage_delete_profile_image_cache_directory($_args['_item_id']);
            }
        }
    }
    return $_data;
}

//-------------------------
// FUNCTIONS
//-------------------------

/**
 * Prune the local image cache to maintain free space
 * @note requires jrImage_min_disk_free_megabytes $_conf var be set
 * @return bool
 */
function jrImage_maintain_free_disk_space()
{
    global $_conf;
    if (isset($_conf['jrImage_min_disk_free_megabytes']) && jrCore_checktype($_conf['jrImage_min_disk_free_megabytes'], 'number_nz')) {

        // Are we already running?
        $cdr = jrCore_get_module_cache_dir('jrImage');
        $lck = "{$cdr}/prune.lock";
        if (is_file($lck)) {
            // We have an existing lock file in place - is the process still running?
            if (filemtime($lck) < (time() - 900)) {
                // We have been running for over 15 minutes - kill if we can
                if (function_exists('posix_kill')) {
                    $pid = (int) file_get_contents($lck);
                    if ($pid > 0) {
                        posix_kill($pid, SIGTERM);
                    }
                }
                unlink($lck);
            }
            else {
                // Could still be running - check again on next run
                return true;
            }
        }

        // Are we running out of disk space?
        $min_bytes = (int) $_conf['jrImage_min_disk_free_megabytes'];
        $min_bytes = (($min_bytes * 1024) * 1024);
        if ($free = disk_free_space(APP_DIR)) {

            if ($free < $min_bytes) {

                // We have less than 1 Gig free - cleanup
                jrCore_write_to_file($lck, getmypid());

                // How many days are we set to right now for clean?
                $days = (int) $_conf['jrImage_clean_days'];
                if ($days === 0) {
                    // We are disabled for pruning - set to 1 day
                    $days = 1;
                }
                elseif ($days === 1) {
                    // We're already set for 1 day - drop to 12 hours
                    $days = 0.5;
                }
                else {
                    // we are full even though we've been running - reduce by a day
                    $days--;
                }
                $_data = array(
                    'clean_days' => $days,
                    'clean_max'  => 2500
                );
                while (true) {
                    $cnt = jrImage_prune_cache_worker($_data);
                    if ($cnt > 0) {
                        jrCore_logger('MAJ', "deleted " . jrCore_number_format($cnt) . " cached images to free up disk space");
                        break;
                    }
                    // Reduce and try again
                    $_data['clean_days'] = ($_data['clean_days'] * 0.75);
                }
                unlink($lck);

            }
        }
    }
    return true;
}

/**
 * Return true if a file is an image
 * @param string $file
 * @return bool|int|string
 */
function jrImage_is_valid_image_file($file)
{
    if (function_exists('exif_imagetype')) {
        return exif_imagetype($file);
    }
    return (jrImage_is_image_file($file)) ? true : false;
}

/**
 * Return TRUE if file/string is an image file
 * @param $file string Image File name
 * @return string|bool
 */
function jrImage_is_image_file($file)
{
    $ext = jrCore_file_extension($file);
    switch ($ext) {
        case 'png':
        case 'gif':
        case 'jpg':
        case 'jpe':
        case 'jpeg':
        case 'jfif':
            return $ext;
            break;
    }
    return false;
}

/**
 * Returns true if PNG image contains transparency
 * @param $file string full path to PNG file
 * @return bool
 */
function jrImage_is_alpha_png($file)
{
    $ord = ord(file_get_contents($file, null, null, 25, 1));
    if ($ord == 4 || $ord == 6) {
        return true;
    }
    $tmp = file_get_contents($file);
    if (stripos($tmp, 'PLTE') !== false && stripos($tmp, 'tRNS') !== false) {
        return true;
    }
    return false;
}

/**
 * Returns true if a GIF image is an animated GIF
 * @param $file string Full path to GIF file
 * @return bool
 */
function jrImage_is_animated_gif($file)
{
    $file = file_get_contents($file);
    $sloc = 0;
    $scnt = 0;
    while ($scnt < 2) {
        $whr1 = strpos($file, "\x00\x21\xF9\x04", $sloc);
        if (!$whr1) {
            break;
        }
        else {
            $sloc = ($whr1 + 1);
            $whr2 = strpos($file, "\x00\x2C", $sloc);
            if (!$whr2) {
                break;
            }
            else {
                if (($whr1 + 8) == $whr2) {
                    $scnt++;
                }
                $sloc = ($whr2 + 1);
            }
        }
    }
    return ($scnt > 1) ? true : false;
}

/**
 * Image form field display
 * @param $_field array Array of Field parameters
 * @param $_att array Additional HTML parameters
 * @return bool
 */
function jrImage_form_field_image_display($_field, $_att = null)
{
    global $_conf, $_user;
    $htm = '';
    if (!isset($_field['value']) || !is_array($_field['value'])) {
        if (isset($_field['value']) && $_field['value'] === false) {
            // weird..
        }
        else {
            // If we are doing an update - we need the full item - the 'jrcore_form_create_values'
            // flag is set in the jrCore_form_create call with the "values" key/array
            $_field['value'] = jrCore_get_flag('jrcore_form_create_values');
        }
    }

    // Check if JSON
    if (isset($_field['options']) && !is_array($_field['options']) && strlen($_field['options']) > 0) {
        $_tmp = explode("\n", $_field['options']);
        if ($_tmp && is_array($_tmp)) {
            foreach ($_tmp as $v) {
                $v = trim($v);
                if (strpos($v, '|')) {
                    list($k, $v) = explode('|', $v, 2);
                }
                else {
                    $k = $v;
                }
                $_field[$k] = $v;
            }
        }
    }

    // We need to show existing images to the user
    $_ln = jrUser_load_lang_strings();
    if (!isset($_field['no_image']) && isset($_field['value']) && is_array($_field['value']) && isset($_field['value']['_item_id']) && jrCore_checktype($_field['value']['_item_id'], 'number_nz')) {

        // See if we are allowing 1 or multiple images to be uploaded for this item
        $_im = array();
        if ((isset($_field['multiple']) && $_field['multiple']) || (isset($_field['multiple']) && jrCore_checktype($_field['multiple'], 'number_nz') && $_field['multiple'] > 1)) {
            // Get image fields
            foreach ($_field['value'] as $k => $v) {
                if (strpos($k, "{$_field['name']}_") === 0 && strpos($k, '_size') && jrCore_checktype($v, 'number_nz')) {
                    $_im[] = array(
                        'field'  => str_replace('_size', '', $k),
                        'unique' => (isset($_field['value']["{$_field['name']}_time"])) ? intval($_field['value']["{$_field['name']}_time"]) : time()
                    );
                }
            }
        }
        else {
            if (isset($_field['value']['_item_id']) && jrCore_checktype($_field['value']['_item_id'], 'number_nz') && isset($_field['value']["{$_field['name']}_size"]) && jrCore_checktype($_field['value']["{$_field['name']}_size"], 'number_nz')) {
                $_im[] = array(
                    'field'  => $_field['name'],
                    'unique' => $_field['value']["{$_field['name']}_time"]
                );
            }
        }
        if (count($_im) > 0) {
            $_fm = jrCore_form_get_session();
            if (!isset($_field['image_module'])) {
                $mod = $_fm['form_params']['module'];
                $iid = (int) $_field['value']['_item_id'];
            }
            else {
                $mod = $_field['image_module'];
                switch ($mod) {
                    case 'jrProfile':
                        $iid = (int) $_field['value']['_profile_id'];
                        break;
                    case 'jrUser':
                        $iid = (int) $_field['value']['_user_id'];
                        break;
                    default:
                        $iid = (int) $_field['value']['_item_id'];
                        break;
                }
            }
            $mrl = jrCore_get_module_url($mod);
            $iur = jrCore_get_module_url('jrImage');
            $siz = 'larger';

            $plg = jrCore_get_active_media_system();
            $fnc = "_{$plg}_media_get_image_url";
            foreach ($_im as $k => $_inf) {

                // Create our image URL
                if (function_exists($fnc)) {
                    // [module] => jrVideo
                    // [type] => video_image
                    // [item_id] => 175
                    // [size] => small
                    // [crop] => auto
                    // [alt] => testing video
                    // [title] => testing video
                    // [class] => iloutline iindex
                    // [width] =>
                    // [height] =>
                    $_params = array(
                        'module'  => $mod,
                        'type'    => $_inf['field'],
                        'item_id' => $iid,
                        'size'    => $siz,
                        '_v'      => $_inf['unique']
                    );
                    $img_url = $fnc($_params);
                    if (!$img_url) {
                        // Default to local
                        $img_url = "{$_conf['jrCore_base_url']}/{$mrl}/image/{$_inf['field']}/{$iid}/{$siz}/_v={$_inf['unique']}";
                    }
                }
                else {
                    $img_url = "{$_conf['jrCore_base_url']}/{$mrl}/image/{$_inf['field']}/{$iid}/{$siz}/_v={$_inf['unique']}";
                }

                $url = "<img src=\"{$img_url}\" width=\"160\" alt=\"" . addslashes($_field['label']) . "\">";
                if (isset($_field['image_delete']) && $_field['image_delete'] !== false) {
                    $img = jrCore_get_sprite_html('close', 16);
                    $htm .= "<div class=\"image_update_display\" onmouseover=\"$('#d{$_inf['field']}').show()\" onmouseout=\"$('#d{$_inf['field']}').hide()\">{$url}";
                    $htm .= "<div id=\"d{$_inf['field']}\" class=\"image_delete\" onclick=\"jrCore_confirm('','" . addslashes($_ln['jrImage'][3]) . "',function() { jrCore_window_location('{$_conf['jrCore_base_url']}/{$iur}/delete/{$mod}/{$_inf['field']}/{$iid}') })\">{$img}</div>";
                }
                else {
                    $htm .= "<div class=\"image_update_display\">{$url}";
                }
                $htm .= '</div>';
            }
            $htm .= '<div style="clear:both"></div>';
        }
    }
    if (!isset($_field['text']) && isset($_ln['jrImage'][4])) {
        $_field['text'] = $_ln['jrImage'][4];
    }
    $_field['html']     = $htm;
    $_field['type']     = 'image';
    $_field['template'] = 'form_field_elements.tpl';

    // We have a file upload - we need to turn on the progress meter if enabled
    $_field['multiple'] = (isset($_field['multiple'])) ? $_field['multiple'] : false;

    // Allowed image file types
    if (isset($_field['allowed'])) {
        $allowed = trim($_field['allowed']);
    }
    else {
        // Make sure we have some quota defaults
        if (!isset($_user['quota_jrImage_allowed_image_types']) || strlen($_user['quota_jrImage_allowed_image_types']) < 3) {
            $_user['quota_jrImage_allowed_image_types'] = 'png,gif,jpg,jpeg';
        }
        $allowed = trim($_user['quota_jrImage_allowed_image_types']);
    }

    // Max allowed upload
    if (isset($_field['max']) && jrCore_checktype($_field['max'], 'number_nz')) {
        $max = (int) $_field['max'];
    }
    else {
        if (!isset($_user['quota_jrImage_max_image_size']) || !jrCore_checktype($_user['quota_jrImage_max_image_size'], 'number_nz')) {
            $_user['quota_jrImage_max_image_size'] = 2097152;
        }
        $max = $_user['quota_jrImage_max_image_size'];
    }
    $max    = jrCore_get_max_allowed_upload($max);
    $_field = jrCore_enable_meter_support($_field, $allowed, $max, $_field['multiple']);

    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * Defines Form Designer field options
 * @return array
 */
function jrImage_form_field_image_form_designer_options()
{
    return array(
        'options_help'        => 'enter options ONE PER LINE, in the following format: <strong>multiple|true</strong>  OR <strong>multiple|5</strong> OR <strong>allowed|jpg,png,gif</strong>',
        'disable_validation'  => true,
        'disable_default'     => true,
        'disable_min_and_max' => true
    );
}

/**
 * Additional form field HTML attributes that can be passed in via the form
 */
function jrImage_form_field_image_attributes()
{
    return array('disabled', 'readonly', 'maxlength', 'onfocus', 'onblur', 'onselect', 'onkeypress');
}

/**
 * Check to be sure validation is on if field is required
 * @param $_field array Array of Field Parameters
 * @param $_post array Posted Data for checking
 * @return array
 */
function jrImage_form_field_image_params($_field, $_post)
{
    if (!isset($_field['validate'])) {
        $_field['validate'] = 'not_empty';
    }
    if (!isset($_field['error_msg'])) {
        $_lang               = jrUser_load_lang_strings();
        $_field['error_msg'] = $_lang['jrImage'][1];
    }
    return $_field;
}

/**
 * Checks to see if we received data on our post in the form validator
 * @param $_field array Array of Field Parameters
 * @param $_post array Posted Data for checking
 * @return bool
 */
function jrImage_form_field_image_is_empty($_field, $_post)
{
    global $_user;
    // Make sure we got a File..
    $tmp = jrCore_is_uploaded_media_file($_field['module'], $_field['name'], $_user['user_active_profile_id']);
    if (!$tmp) {
        return true;
    }
    // Okay looks good
    return false;
}

/**
 * Verify we get an uploaded file if one is required in the form
 * @param $_field array Field Information
 * @param $_post array Parsed $_REQUEST
 * @param $e_msg string Error message for field if in error
 * @return mixed
 */
function jrImage_form_field_image_validate($_field, $_post, $e_msg)
{
    global $_user;
    // Make sure we got a File..
    $tmp = jrCore_is_uploaded_media_file($_field['module'], $_field['name'], $_user['user_active_profile_id']);
    if (!$tmp) {
        if (!$_field['required']) {
            // file does not exist, but is not required
            return $_post;
        }
        jrCore_set_form_notice('error', $e_msg);
        return false;
    }
    // Okay looks good
    return $_post;
}

/**
 * Return an array of allowed image widths
 * @return array Returns array of allowed image sizes
 */
function jrImage_get_allowed_image_widths()
{
    global $_conf;
    $_sz = array(
        '24'       => 24,
        'xxsmall'  => 24,
        '40'       => 40,
        'xsmall'   => 40,
        '56'       => 56,
        '72'       => 72,
        'small'    => 72,
        '96'       => 96,
        'icon96'   => 96,
        '128'      => 128,
        'icon'     => 128,
        '196'      => 196,
        'medium'   => 196,
        '256'      => 256,
        'large'    => 256,
        '320'      => 320,
        'larger'   => 320,
        '384'      => 384,
        'xlarge'   => 384,
        '512'      => 512,
        'xxlarge'  => 512,
        '800'      => 800,
        'xxxlarge' => 800,
        '1280'     => 1280
    );
    if (!isset($_conf['jrImage_block_original_size']) || $_conf['jrImage_block_original_size'] == 'off') {
        $_sz['original'] = 'original';
    }

    // Support by advanced key
    if (isset($_conf['jrImage_custom_image_sizes']) && strlen($_conf['jrImage_custom_image_sizes']) > 0) {
        foreach (explode(',', $_conf['jrImage_custom_image_sizes']) as $s) {
            $s       = (int) $s;
            $_sz[$s] = $s;
        }
    }

    // Support by Event
    return jrCore_trigger_event('jrImage', 'get_allowed_image_widths', $_sz);
}

/**
 * Return an Image SRC URL
 * @param $module string Module name
 * @param $field string DS field
 * @param $item_id int Item ID
 * @param $size string Size
 * @param $_args array Additional params
 * @return string
 */
function jrImage_get_image_src($module, $field, $item_id, $size, $_args = null)
{
    $params = array();
    if (is_array($_args)) {
        $params = $_args;
    }
    $params['module']  = $module;
    $params['type']    = $field;
    $params['item_id'] = (int) $item_id;
    $params['size']    = $size;
    $smarty            = new stdClass();
    return smarty_function_jrImage_display($params, $smarty);
}

/**
 * Get the active image cache directory
 * @return string
 */
function jrImage_get_active_cache_directory()
{
    global $_conf;
    return jrCore_get_module_cache_dir('jrImage') . '/' . $_conf['jrImage_active_cache_dir'];
}

/**
 * Get an image cache key
 * @param array $_post
 * @return string
 */
function jrImage_get_cache_key($_post)
{
    return md5($_post['_uri']);
}

/**
 * Clean broken cache symlinks
 * @return bool
 */
function jrImage_delete_broken_cache_symlinks()
{
    if ($cdr = jrImage_get_active_cache_directory()) {
        ob_start();
        system('/usr/bin/find ' . escapeshellarg($cdr) . ' -type l ! -exec test -e {} \; -print 2>/dev/null');
        $res = ob_get_clean();
        if ($res && strlen($res) > 2) {
            if ($res = explode("\n", $res)) {
                foreach ($res as $link) {
                    if (strpos($link, APP_DIR) === 0 && strlen(realpath($link)) === 0) {
                        unlink($link);
                    }
                }
            }
        }
        return true;
    }
    return false;
}

/**
 * Get path to a cached image key if it exists
 * @param string $module
 * @param int $item_id
 * @param string $key
 * @return bool|string
 */
function jrImage_is_cached_image_key($module, $item_id, $key)
{
    $grp = _jrCore_local_media_get_directory_group($item_id);
    $img = jrImage_get_active_cache_directory() . "/{$module}/{$grp}/{$item_id}/{$key}";
    return (is_file($img)) ? $img : false;
}

/**
 * Create the symlink cache directory for an image
 * @param int $profile_id
 * @param int $privacy
 * @param string $module
 * @param int $item_id
 * @return string
 */
function jrImage_create_image_cache_symlink($profile_id, $privacy, $module, $item_id)
{
    global $_conf;
    $iid = (int) $item_id;
    $grp = _jrCore_local_media_get_directory_group($iid);
    $cdr = jrImage_get_active_cache_directory();
    $dir = "{$cdr}/{$module}/{$grp}";
    if (!is_dir($dir)) {
        @mkdir($dir, $_conf['jrCore_dir_perms'], true);
    }
    else {
        chmod($dir, $_conf['jrCore_dir_perms']);
    }
    if (!is_link("{$dir}/{$iid}")) {
        // Create symlink
        $pid = (int) $profile_id;
        $gr2 = _jrCore_local_media_get_directory_group($pid);
        $prd = "{$privacy}/{$gr2}/{$pid}/{$module}/{$iid}";
        if (!is_dir("{$cdr}/{$prd}")) {
            mkdir("{$cdr}/{$prd}", $_conf['jrCore_dir_perms'], true);
        }
        else {
            chmod("{$cdr}/{$prd}", $_conf['jrCore_dir_perms']);
        }
        $old = getcwd();
        chdir($cdr);
        symlink("../../{$prd}", "{$module}/{$grp}/{$iid}");
        chdir($old);
    }
    return "{$dir}/{$iid}";
}

/**
 * Get a profile's image cache directory
 * @param int $profile_id
 * @param string $privacy public|private|shared
 * @param string $module
 * @param int $item_id
 * @return string
 */
function jrImage_create_profile_image_cache_directory($profile_id, $privacy, $module = null, $item_id = 0)
{
    global $_conf;
    $pid = (int) $profile_id;
    $grp = _jrCore_local_media_get_directory_group($pid);
    if (is_null($module)) {
        $dir = jrImage_get_active_cache_directory() . "/{$privacy}/{$grp}/{$pid}";
    }
    else {
        if ($item_id > 0) {
            $dir = jrImage_get_active_cache_directory() . "/{$privacy}/{$grp}/{$pid}/{$module}/{$item_id}";
        }
        else {
            $dir = jrImage_get_active_cache_directory() . "/{$privacy}/{$grp}/{$pid}/{$module}";
        }
    }
    if (!is_dir($dir)) {
        @mkdir($dir, $_conf['jrCore_dir_perms'], true);
    }
    return $dir;
}

/**
 * Verify that a profile's cache directory is in the correct privacy group
 * @param int $profile_id
 * @return bool
 */
function jrImage_delete_profile_image_cache_directory($profile_id)
{
    $cdr = jrImage_get_active_cache_directory();
    if (is_dir($cdr)) {
        $pid = (int) $profile_id;
        $grp = _jrCore_local_media_get_directory_group($pid);
        foreach (array(0, 1, 2, 3) as $p) {
            if (is_dir("{$cdr}/{$p}/{$grp}/{$pid}")) {
                jrCore_delete_dir_contents("{$cdr}/{$p}/{$grp}/{$pid}");
                rmdir("{$cdr}/{$p}/{$grp}/{$pid}");
            }
        }
        return true;
    }
    return false;
}

/**
 * Get profile information from image cache path
 * @param string $img
 * @return array
 */
function jrImage_get_profile_info_from_path($img)
{
    $_st = stat($img);
    $cdr = jrImage_get_active_cache_directory();
    $_im = str_replace("{$cdr}/", '', realpath($img));
    $_im = explode('/', $_im);
    // [0] => 1 - profile_private
    // [1] => 1 - directory group
    // [2] => 1 - profile_id
    // [3] => jrGallery - module
    // [4] => 149 - item_id
    // [5] => 1e8c82840d9b6efcf98ee445298be925 - cached image
    $tmp = jrCore_mime_type($img);
    $_tm = array(
        '_item_id'        => $_im[3],
        '_profile_id'     => $_im[1],
        'profile_private' => $_im[0],
        'image_time'      => $_st['mtime'],
        'image_name'      => basename($img),
        'image_size'      => $_st['size'],
        'image_type'      => $tmp,
        'image_extension' => jrCore_file_extension_from_mime_type($tmp)
    );
    return $_tm;
}

/**
 * Send a 304 Not Modified response for an image
 * @param int $timestamp
 * @param string $img
 * @return bool
 */
function jrImage_send_not_modified($timestamp, $img)
{
    $ifs = false;
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']{1})) {
        $ifs = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
    }
    elseif (function_exists('getenv')) {
        $ifs = getenv('HTTP_IF_MODIFIED_SINCE');
    }
    if ($ifs && strtotime($ifs) == $timestamp) {
        jrCore_set_custom_header("Last-Modified: " . gmdate('D, d M Y H:i:s \G\M\T', $timestamp));
        jrCore_set_custom_header('Content-Disposition: inline; filename="' . basename($img) . '"');
        jrCore_set_custom_header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400000));
        jrCore_set_custom_header('HTTP/1.1 304 Not Modified');
        jrCore_send_response_and_detach(null, true);
        exit;
    }
    return false;
}

/**
 * Display an image for a DataStore item
 * @note Magic View
 * @param $_post array Params from jrCore_parse_url();
 * @param $_user array User information
 * @param $_conf array Global config
 * @example http://www.site.com/[module]/image/[field_name]/[item_id]/[size]
 */
function jrImage_display_image($_post, $_user, $_conf)
{
    // Check for good Item ID
    jrUser_ignore_action();
    if (!isset($_post['_2']) || !is_numeric($_post['_2'])) {
        if (isset($_post['debug'])) {
            jrCore_notice('CRI', 'invalid media_id - must be valid media id');
        }
        jrImage_display_default_image($_post, $_conf);
    }

    // Must get a valid Size
    if (!isset($_post['_3'])) {
        $_post['_3'] = 'icon';
    }
    $_sz = jrImage_get_allowed_image_widths();
    if (!isset($_sz["{$_post['_3']}"])) {
        if (isset($_post['debug'])) {
            jrCore_notice('CRI', "invalid image size - must be one of: " . implode(',', array_flip($_sz)));
        }
        jrImage_display_default_image($_post, $_conf);
    }
    $_post['width'] = $_sz["{$_post['_3']}"];

    // Get cache key
    $key = jrImage_get_cache_key($_post);
    if ($img = jrImage_is_cached_image_key($_post['module'], $_post['_2'], $key)) {

        // This image is cached - we can possibly bypass lookup
        $_rt = jrImage_get_profile_info_from_path($img);
        $prv = (isset($_rt['profile_private'])) ? $_rt['profile_private'] : 1;
        if (isset($_rt['quota_jrProfile_privacy_changes']) && $_rt['quota_jrProfile_privacy_changes'] == 'off') {
            $prv = (int) $_rt['quota_jrProfile_default_privacy'];
        }
        $_im = array(
            'image_time'      => $_rt['image_time'],
            'image_name'      => $_rt['image_name'],
            'image_size'      => $_rt['image_size'],
            'image_type'      => $_rt['image_type'],
            'image_extension' => $_rt['image_extension']
        );

    }
    else {

        // We have to setup the cache structure for this image
        $_rt = jrCore_db_get_item($_post['module'], intval($_post['_2']));
        if (!$_rt || !is_array($_rt)) {
            if (isset($_post['debug'])) {
                jrCore_notice('CRI', 'invalid media data - data for id not found in DataStore');
            }
            jrImage_display_default_image($_post, $_conf);
        }
        $prv = (isset($_rt['profile_private'])) ? $_rt['profile_private'] : 1;
        if (isset($_rt['quota_jrProfile_privacy_changes']) && $_rt['quota_jrProfile_privacy_changes'] == 'off') {
            $prv = (int) $_rt['quota_jrProfile_default_privacy'];
        }
        // We must have a valid image extension to show an image
        if (!isset($_rt["{$_post['_1']}_extension"])) {
            if (isset($_post['debug'])) {
                jrCore_notice('CRI', 'invalid media data - extension for id not found in DataStore');
            }
            jrImage_display_default_image($_post, $_conf, $_rt);
        }
        $_im = array('image_time' => $_rt['_updated']);
        foreach (array('name', 'size', 'type', 'extension', 'width', 'height') as $i) {
            if (isset($_rt["{$_post['_1']}_{$i}"])) {
                $_im["image_{$i}"] = $_rt["{$_post['_1']}_{$i}"];
            }
        }

    }
    $_rt['profile_private'] = $prv;

    // Let other modules see what we are going to do
    $_rt = jrCore_trigger_event('jrImage', 'item_image_info', $_rt);
    if (isset($_rt['profile_private'])) {
        $prv = $_rt['profile_private'];
    }

    // Check for viewer access
    // 0 = Private
    // 1 = Global
    // 2 = Shared
    // 3 = Shared but Visible in Search
    switch ($prv) {
        case '0':
        case '2':
        case '3':
            // Don't change this - this needs to be global here
            global $_user;
            /** @noinspection PhpUnusedLocalVariableInspection */
            $_user = jrUser_session_start(false); // DO NOT REMOVE
            if (!jrUser_is_admin() && !jrProfile_privacy_check($_post['module'], $_rt['_profile_id'], $prv)) {
                // User does not have access to this profile - no image
                if (isset($_post['debug'])) {
                    jrCore_notice('CRI', 'privacy settings prevent access to this image');
                }
                jrImage_display_default_image($_post, $_conf);
            }
            break;
    }

    // Check for cache
    if ($img) {
        // We are cached..
        jrImage_send_not_modified($_im['image_time'], $img);
    }
    else {

        // Ensure master image is local for resize
        jrCore_db_close();
        $dir = jrCore_get_media_directory($_rt['_profile_id'], FORCE_LOCAL);
        $nam = "{$_post['module']}_{$_post['_2']}_{$_post['_1']}.{$_rt["{$_post['_1']}_extension"]}";
        jrCore_confirm_media_file_is_local($_rt['_profile_id'], $nam, "{$dir}/{$nam}");
        if (!is_file("{$dir}/{$nam}")) {
            if (isset($_post['debug'])) {
                jrCore_notice('CRI', 'image file does not exist');
            }
            jrImage_display_default_image($_post, $_conf);
        }
        if (!jrImage_is_valid_image_file("{$dir}/{$nam}")) {
            if (isset($_post['debug'])) {
                jrCore_notice('CRI', 'image file is not a valid image');
            }
            jrImage_display_default_image($_post, $_conf);
        }

        // Make sure our profile cache directory exists
        $prd = jrImage_create_profile_image_cache_directory($_rt['_profile_id'], $prv, $_post['module'], $_post['_2']);
        jrImage_create_image_cache_symlink($_rt['_profile_id'], $prv, $_post['module'], $_post['_2']);

        // Create our resized image and cache it
        $img = "{$prd}/{$key}";
        $img = jrImage_create_image("{$dir}/{$nam}", $_im, $_post, $_conf, $img, $_rt);
        if (!$img || strpos($img, 'ERROR') === 0) {
            if (isset($_post['debug'])) {
                jrCore_notice('CRI', substr($img, 6));
            }
            jrImage_display_default_image($_post, $_conf);
        }

    }

    // On PHP 7 systems session handling will set some default "no cache" headers that we
    // we need to remove.  This _should_ be fixed by setting session_cache_limiter(''), but
    // have found that makes sessions a bit on the flaky side, so we just unset the headers here
    header_remove('Cache-Control');
    header_remove('Expires');
    header_remove('Pragma');

    // Get right mime type - sometimes it can be wrong when PHP is wrong
    switch ($_im['image_extension']) {
        case 'jpg':
        case 'jpe':
        case 'jpeg':
        case 'jfif':
            $mime = "image/jpeg";
            break;
        case 'png':
            $mime = "image/png";
            break;
        case 'gif':
            $mime = "image/gif";
            break;
        default:
            $mime = $_im['image_type'];
            break;
    }

    jrCore_set_custom_header("Last-Modified: " . gmdate('D, d M Y H:i:s \G\M\T', $_im['image_time']));
    jrCore_set_custom_header("Content-Type: {$mime}");
    jrCore_set_custom_header('Content-Disposition: inline; filename="' . $_im['image_name'] . '"');
    jrCore_set_custom_header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400000));
    jrCore_send_response_and_detach(file_get_contents($img), true);
    exit;
}

/**
 * Resize and image, maintaining aspect ratio
 * @param string $input_file Input file to resize
 * @param string $output_file Output file to create
 * @param int $width Width (in pixels) for new image
 * @param int $quality epeg quality
 * @return mixed
 */
function jrImage_resize_image($input_file, $output_file, $width, $quality = 80)
{
    global $_conf;
    // Some resize options can use a lot of memory
    @ini_set('memory_limit', '256M');
    $_im = getimagesize($input_file);
    if ($width == 'original') {
        return $input_file;
    }
    switch ($_im['mime']) {
        case 'image/jpeg':
            // Can we use epeg?
            if ($epeg = jrCore_get_tool_path('epeg')) {
                // epeg will not enlarge an image
                if ($_im[0] >= $width) {
                    $bad = false;
                    ob_start();
                    system("{$epeg} --width=" . intval($width) . " --preserve --quality=" . intval($quality) . " {$input_file} {$output_file}.tmp");
                    if ($f = fopen("{$output_file}.tmp", 'r')) {
                        if ($d = fread($f, 512)) {
                            fclose($f);
                            if (strpos($d, 'Thumb::URI')) {
                                // This image did NOT convert correctly
                                $bad = true;
                            }
                        }

                    }
                    ob_end_clean();
                    if (!$bad) {
                        rename("{$output_file}.tmp", $output_file);
                        return $output_file;
                    }
                    unlink("{$output_file}.tmp");
                    unset($bad);
                    // Fall through on a bad image resize from epeg to the PHP-GD resize functions
                }
            }
            $ext = 'jpg';
            $src = imagecreatefromjpeg($input_file);
            break;
        case 'image/png':
            $ext = 'png';
            $src = imagecreatefrompng($input_file);
            break;
        case 'image/gif':
            $ext = 'gif';
            $src = imagecreatefromgif($input_file);
            break;
        default:
            return 'ERROR: invalid image mime type';
            break;
    }

    // make sure we get a valid resource
    if (!is_resource($src)) {
        // See if we can get it via imagecreatefromstring
        if (function_exists('imagecreatefromstring')) {
            $tmp = file_get_contents($input_file);
            $src = imagecreatefromstring($tmp);
            unset($tmp);
        }
        if (!is_resource($src)) {
            return 'ERROR: unable to create image resource from input file';
        }
    }

    // Resize Image
    $src_y_offset = 0;
    $src_x_offset = 0;
    $src_width    = $_im[0];
    $src_height   = $_im[1];

    // maintain aspect ratio of original image
    $height = (int) (($src_height / $src_width) * $width);

    // create resource
    if ($ext != 'gif') {
        $new = imagecreatetruecolor($width, $height);
        if (!$new) {
            imagedestroy($src);
            return 'ERROR: unable to create new resized image resource';
        }
        // Maintain alpha transparency on PNG
        imagealphablending($new, false);
        imagesavealpha($new, true);
    }
    else {
        $new = imagecreate($width, $height);
        if (!$new) {
            imagedestroy($src);
            return 'ERROR: unable to create new resized image resource';
        }
    }
    // resize image
    if ($ext == 'jpg') {
        $qal = 2;
        if ($width <= 40) {
            $qal = 1;
        }
        if (!jrImage_imagecopyresampled($new, $src, 0, 0, $src_x_offset, $src_y_offset, $width, $height, $src_width, $src_height, $qal)) {
            if (!imagecopyresized($new, $src, 0, 0, $src_x_offset, $src_y_offset, $width, $height, $src_width, $src_height)) {
                imagedestroy($src);
                return 'ERROR: unable to create new resized image';
            }
        }
    }
    else {
        if (!imagecopyresampled($new, $src, 0, 0, $src_x_offset, $src_y_offset, $width, $height, $src_width, $src_height)) {
            if (!imagecopyresized($new, $src, 0, 0, $src_x_offset, $src_y_offset, $width, $height, $src_width, $src_height)) {
                imagedestroy($src);
                return 'ERROR: unable to create new resized image';
            }
        }
    }

    // Create new image
    switch ($ext) {
        case 'jpg':
        case 'jpe':
        case 'jpeg':
        case 'jfif':
            imagejpeg($new, $output_file, 85);
            break;
        case 'png':
            imagepng($new, $output_file);
            break;
        case 'gif':
            if (function_exists('imagegif')) {
                imagecolortransparent($new);
                imagegif($new, $output_file);
            }
            else {
                imagepng($new, $output_file);
            }
            break;
    }
    imagedestroy($src);
    chmod($output_file, $_conf['jrCore_file_perms']);
    return $output_file;
}

/**
 * Resize an animated GIF image
 * @param string $input_file Input Animated GIF file to resize
 * @param array $_image Image information
 * @param array $_post
 * @param string $cache_file
 * @return string
 */
function jrImage_resize_animated_gif($input_file, $_image, $_post, $cache_file)
{
    // Make sure our convert binary is good
    if (!$convert = jrCore_get_tool_path('convert', 'jrImage')) {
        return false;
    }
    $cache_lock = "{$cache_file}.lock";
    if (is_file($cache_lock)) {
        // We are in the middle of resizing - how long has it been?
        if (filemtime($cache_lock) < (time() - 180)) {
            // It has been more than 3 minutes - we've bombed
            unlink($cache_lock);
        }
        else {
            return false;
        }
    }
    touch($cache_lock);

    //----------------------------------
    // Resize Image
    //----------------------------------
    $new_width  = $_post['width'];
    $new_height = $_post['width'];
    list($src_width, $src_height,) = getimagesize($input_file);

    //----------------------------------
    // Cropping
    //----------------------------------
    if (isset($_post['crop'])) {
        switch ($_post['crop']) {

            // No crop
            case 'none':
            case 'false':
                // maintain aspect ratio of original image
                $new_height      = (int) (($src_height / $src_width) * $new_width);
                $_post['height'] = $new_height;
                break;


            // With crop set to "auto" we will crop the height OR width
            // depending on original aspect ratio of the image
            // 'auto'
            // 'square'
            default:

                // Check for an aspect ratio crop
                if (strpos($_post['crop'], ':')) {

                    // Our aspect ratio crop will come in like 4x3 or 16x9, etc.
                    list($w, $h) = explode(':', $_post['crop'], 2);
                    $w = (int) $w;
                    $h = (int) $h;
                    if ($w > 0 && $h > 0) {
                        if ($w > $h) {
                            $new_height      = round(($_post['width'] / $w) * $h);
                            $_post['height'] = $new_height;
                        }
                        else {
                            $new_height      = $_post['width'];
                            $new_width       = round(($_post['width'] / $h) * $w);
                            $_post['width']  = $new_width;
                            $_post['height'] = $new_height;
                        }
                    }
                    else {
                        // fall through - maintain aspect ratio of original image
                        $new_height      = (int) (($src_height / $src_width) * $new_width);
                        $_post['height'] = $new_height;
                    }
                }
                else {
                    // Default auto height/width
                    $_post['height'] = $_post['width'];
                }
                break;
        }
    }
    else {
        // maintain aspect ratio of original image
        $new_height      = (int) (($src_height / $src_width) * $new_width);
        $_post['height'] = $new_height;
    }

    // Use convert binary if we can
    system("{$convert} {$input_file} -coalesce {$cache_file}.tmp.gif");
    if (is_file("{$cache_file}.tmp.gif")) {
        system("{$convert} -size {$src_width}x{$src_height} {$cache_file}.tmp.gif -resize {$new_width}x{$new_height}\! {$cache_file}.tmp2.gif");
        if (is_file("{$cache_file}.tmp2.gif")) {
            if (!rename("{$cache_file}.tmp2.gif", $cache_file)) {
                copy("{$cache_file}.tmp2.gif", $cache_file);
                unlink("{$cache_file}.tmp2.gif");
            }
        }
        unlink("{$cache_file}.tmp.gif");
    }
    unlink($cache_lock);
    return $cache_file;
}

/**
 * Create a new image thumbnail from an existing master image
 * @param string $input_file Input file to resize
 * @param array $_image Image information
 * @param array $_post Params from jrCore_parse_url();
 * @param array $_conf Global config
 * @param string $cache_file if given file will be written to
 * @param array $_data full image information
 * @return bool
 */
function jrImage_create_image($input_file, $_image, $_post, $_conf, $cache_file, $_data = null)
{
    global $_conf;

    // Some resize options can use a lot of memory
    @ini_set('memory_limit', '512M');

    // $_image contains info about the ORIGINAL IMAGE
    // $_post contains our info about the NEW (resizing) image
    switch ($_image['image_extension']) {
        case 'jpg':
        case 'jpe':
        case 'jpeg':
        case 'jfif':
            // For a JPEG image, we have no filters/cropping, and we are
            // maintaining aspect ratio, we can do a really quick resize
            // using the epeg binary
            if (!isset($_post['crop']) && (!isset($_post['_uri']) || !strpos($_post['_uri'], 'filter'))) {
                // We're not cropping or filtering - check if watermarking is on for this quota
                if (!isset($_data['quota_jrImage_watermark']) || $_data['quota_jrImage_watermark'] != 'on') {
                    // Watermarking is not enabled
                    return jrImage_resize_image($input_file, $cache_file, $_post['width']);
                }
                else {
                    // If watermarking is enabled, but it is only for images that are for sale,
                    // then we can skip this image if it is NOT for sale
                    if (isset($_data['quota_jrImage_watermark_sale_only']) && $_data['quota_jrImage_watermark_sale_only'] == 'on') {
                        if (!isset($_data["{$_post['_1']}_item_price"]) || $_data["{$_post['_1']}_item_price"] == 0) {
                            // Watermarking is for for-sale images only, and this one is not for sale
                            return jrImage_resize_image($input_file, $cache_file, $_post['width']);
                        }
                    }
                }
            }
            $ext = 'jpg';
            break;
        case 'png':
        case 'gif':
            if (jrImage_is_animated_gif($input_file)) {

                if (!isset($_conf['jrImage_resize_animated']) || $_conf['jrImage_resize_animated'] != 'on') {
                    // We are not resizing animated GIF images
                    return $input_file;
                }

                // We are resizing - add to queue and return "in progress" image
                $_queue = array(
                    'input_file' => $input_file,
                    '_image'     => $_image,
                    '_post'      => $_post,
                    'cache_file' => $cache_file
                );
                jrCore_queue_create('jrImage', 'resize_animated', $_queue);

                // Return our "in progress" PNG
                if (copy(APP_DIR . "/modules/jrImage/img/working.png", $cache_file)) {
                    return $cache_file;
                }

                // Fall through - error so just show what is uploaded
                return $input_file;

            }
            $ext = 'png';
            break;
        default:
            if (isset($_post['debug'])) {
                jrCore_notice('CRI', "invalid image extension: {$_image['image_extension']}");
            }
            return $input_file;
            break;
    }

    // Prep filters
    $_filter = false;
    if (isset($_post['_uri']) && strpos($_post['_uri'], 'filter')) {
        foreach ($_post as $k => $v) {
            if (strpos($k, 'filter') === 0) {
                if (!isset($_filter)) {
                    $_filter = array();
                }
                if (strpos($v, ',')) {
                    foreach (explode(',', $v) as $vv) {
                        $_filter[] = trim($vv);
                    }
                }
                else {
                    $_filter[] = $v;
                }
            }
        }
    }

    //----------------------------------
    // Load source image
    //----------------------------------
    $image = null;
    switch ($_image['image_extension']) {
        case 'jpg':
        case 'jpe':
        case 'jpeg':
        case 'jfif':
            $image['source']  = @imagecreatefromjpeg($input_file);
            $image['quality'] = 85;
            break;
        case 'png':
            $image['source'] = @imagecreatefrompng($input_file);
            break;
        case 'gif':
            $image['source'] = @imagecreatefromgif($input_file);
            break;
    }
    // make sure we get a valid resource
    if (!is_resource($image['source'])) {
        // See if we can get it via imagecreatefromstring
        if (function_exists('imagecreatefromstring')) {
            $tmp             = file_get_contents($input_file);
            $image['source'] = @imagecreatefromstring($tmp);
            unset($tmp);
        }
        if (!is_resource($image['source'])) {
            return $input_file;
        }
    }

    // interlace
    switch ($_image['image_extension']) {
        case 'jpg':
        case 'jpe':
        case 'jpeg':
        case 'jfif':
            imageinterlace($image['source'], true);
            break;
    }

    //----------------------------------
    // Resize Image
    //----------------------------------
    $hnd_y_offset = 0;
    $hnd_x_offset = 0;
    $src_y_offset = 0;
    $src_x_offset = 0;
    list($src_width, $src_height,) = getimagesize($input_file);
    if ($_post['width'] == 'original') {
        $_post['width'] = $src_width;
    }
    $new_width  = $_post['width'];
    $new_height = $_post['width']; // will be changed below

    //----------------------------------
    // Cropping
    //----------------------------------
    if (isset($_post['crop'])) {
        switch ($_post['crop']) {

            case 'fill':
                // With a "fill" crop, we place black borders on top/sides of
                // image so the image is square, but no cropping/resizing
                // of the image happens.
                if ($src_width > $src_height) {
                    $diff         = ($src_width - $src_height);
                    $diff         = round(($diff / $src_width) * $_post['width']);
                    $hnd_y_offset = round($diff / 2);
                    $new_height   = ($new_width - $diff);
                }
                else {
                    $diff         = ($src_height - $src_width);
                    $diff         = round(($diff / $src_height) * $_post['width']);
                    $hnd_x_offset = round($diff / 2);
                    $new_width    = ($new_height - $diff);
                }
                $_post['height'] = $_post['width'];
                break;

            case 'portrait':
                // With a portrait crop, we expect the images to be of people,
                // with their head in the upper 3rd of the picture
                if ($src_width > $src_height) {
                    $diff         = ($src_width - $src_height);
                    $src_x_offset = round($diff / 2);
                    $src_width    = $src_height;
                }
                else {
                    $diff         = ($src_height - $src_width);
                    $src_y_offset = round($diff / 4);
                    $src_height   = $src_width;
                }
                $_post['height'] = $_post['width'];
                $new_height      = $new_width;
                break;

            // With crop set to "height" we will crop the height to the given
            // size, but maintain the aspect ratio for the width
            // $_post['width'] here is set to allowed size as passed in
            case 'height':
                $_post['height'] = $_post['width'];
                $new_height      = $new_width;
                // Now we figure our width based on ratio of height
                $new_width      = (int) (($src_width / $src_height) * $new_height);
                $_post['width'] = $new_width;
                break;

            case 'width':
                // Now we figure our height based on ratio of width
                $new_height      = (int) (($src_height / $src_width) * $new_width);
                $_post['height'] = $new_height;
                break;

            // No crop
            case 'none':
            case 'false':
                // maintain aspect ratio of original image
                $new_height      = (int) (($src_height / $src_width) * $new_width);
                $_post['height'] = $new_height;
                break;


            // With crop set to "auto" we will crop the height OR width
            // depending on original aspect ratio of the image
            // 'auto'
            // 'square'
            default:

                // Check for an aspect ratio crop
                if (strpos($_post['crop'], ':')) {

                    // Our aspect ratio crop will come in like 4x3 or 16x9, etc.
                    list($w, $h) = explode(':', $_post['crop'], 2);
                    $w = (int) $w;
                    $h = (int) $h;
                    if ($w > 0 && $h > 0) {
                        $ratio = array(0 => $src_width / $src_height, 1 => $w / $h);
                        if ($w > $h) {
                            if ($ratio[0] > $ratio[1]) {
                                $width        = $src_height * ($w / $h);
                                $src_x_offset = ($src_width - $width) / 2;
                                $src_width    = $width;
                            }
                            else {
                                if ($ratio[0] < $ratio[1]) {
                                    $height       = $src_width / ($w / $h);
                                    $src_y_offset = ($src_height - $height) / 2;
                                    $src_height   = $height;
                                }
                            }
                            $new_width       = $_post['width'];
                            $new_height      = round(($_post['width'] / $w) * $h);
                            $_post['height'] = $new_height;
                        }
                        else {
                            if ($ratio[0] > $ratio[1]) {
                                $width        = $src_height * ($w / $h);
                                $src_x_offset = ($src_width - $width) / 2;
                                $src_width    = $width;
                            }
                            else {
                                if ($ratio[0] < $ratio[1]) {
                                    $height       = $src_width / ($w / $h);
                                    $src_y_offset = ($src_height - $height) / 2;
                                    $src_height   = $height;
                                }
                            }
                            $new_height      = $_post['width'];
                            $new_width       = round(($_post['width'] / $h) * $w);
                            $_post['width']  = $new_width;
                            $_post['height'] = $new_height;
                        }
                    }
                    else {
                        // fall through - maintain aspect ratio of original image
                        $new_height      = (int) (($src_height / $src_width) * $new_width);
                        $_post['height'] = $new_height;
                    }
                }
                else {
                    // Default auto height/width
                    if ($src_width > $src_height) {
                        $diff         = ($src_width - $src_height);
                        $src_x_offset = round($diff / 2);
                        $src_width    = $src_height;
                    }
                    else {
                        $diff         = ($src_height - $src_width);
                        $src_y_offset = round($diff / 2);
                        $src_height   = $src_width;
                    }
                    $_post['height'] = $_post['width'];
                    $new_height      = $new_width;
                }
                break;
        }

        // allow for adjustment of the crop offset
        // https://www.jamroom.net/the-jamroom-network/forum/new_posts/50901/image-cropportrait
        $_offsets = explode(':', $_post['crop']);
        if (count($_offsets) == 4) {
            $src_x_offset = (int) $_offsets[2];
            $src_y_offset = (int) $_offsets[3];
        }
    }
    else {
        // maintain aspect ratio of original image
        $new_height      = (int) (($src_height / $src_width) * $new_width);
        $_post['height'] = $new_height;
    }

    //----------------------------------
    // create resource
    //----------------------------------
    if ($_image['image_extension'] != 'gif') {
        $image['handle'] = imagecreatetruecolor($_post['width'], $_post['height']);
        if (!$image['handle']) {
            imagedestroy($image['source']);
            return $input_file;
        }
        imagealphablending($image['handle'], false);
        imagesavealpha($image['handle'], true);
    }
    else {
        $image['handle'] = imagecreate($_post['width'], $_post['height']);
        if (!$image['handle']) {
            imagedestroy($image['source']);
            return $input_file;
        }
    }

    // If crop=fill we need to fill the image in first
    if (isset($_post['crop']) && $_post['crop'] == 'fill') {
        if (empty($_post['fillcolor'])) {
            $r = 0;
            $g = 0;
            $b = 0;
        }
        else {
            @list($r, $g, $b) = explode(':', $_post['fillcolor'], 3);
            if (!isset($r) || !jrCore_checktype($r, 'number_nn')) {
                $r = 0;
            }
            if (!isset($g) || !jrCore_checktype($g, 'number_nn')) {
                $g = 0;
            }
            if (!isset($b) || !jrCore_checktype($b, 'number_nn')) {
                $b = 0;
            }
        }
        $color = imagecolorallocate($image['handle'], $r, $g, $b);
        imagefilledrectangle($image['handle'], 0, 0, $_post['width'], $_post['height'], $color);
    }

    // resize image
    if ($ext == 'jpg') {
        $qal = 2;
        if ($new_width <= 40) {
            $qal = 1;
        }
        if (!jrImage_imagecopyresampled($image['handle'], $image['source'], $hnd_x_offset, $hnd_y_offset, $src_x_offset, $src_y_offset, $new_width, $new_height, $src_width, $src_height, $qal)) {
            if (!imagecopyresized($image['handle'], $image['source'], $hnd_x_offset, $hnd_y_offset, $src_x_offset, $src_y_offset, $new_width, $new_height, $src_width, $src_height)) {
                imagedestroy($image['source']);
                return $input_file;
            }
        }
    }
    else {
        if (!imagecopyresampled($image['handle'], $image['source'], $hnd_x_offset, $hnd_y_offset, $src_x_offset, $src_y_offset, $new_width, $new_height, $src_width, $src_height)) {
            if (!imagecopyresized($image['handle'], $image['source'], $hnd_x_offset, $hnd_y_offset, $src_x_offset, $src_y_offset, $new_width, $new_height, $src_width, $src_height)) {
                imagedestroy($image['source']);
                return $input_file;
            }
        }
    }

    //----------------------------------
    // Watermarking
    //----------------------------------
    // [quota_jrImage_watermark] => on
    // [quota_jrImage_watermark_x_offset] => -5
    // [quota_jrImage_watermark_y_offset] => -5
    // [quota_jrImage_watermark_sale_only] => on
    if (isset($_data['quota_jrImage_watermark']) && $_data['quota_jrImage_watermark'] == 'on') {

        $wmark = true;
        // See if we below our cutoff
        if (isset($_data['quota_jrImage_watermark_cutoff']) && jrCore_checktype($_data['quota_jrImage_watermark_cutoff'], 'number_nz') && $new_width < $_data['quota_jrImage_watermark_cutoff']) {
            $wmark = false;
        }
        // See if it is ONLY for sale items
        elseif (isset($_data['quota_jrImage_watermark_sale_only']) && $_data['quota_jrImage_watermark_sale_only'] == 'on') {
            $wmark = false;
            if (isset($_data["{$_post['_1']}_item_price"]) && $_data["{$_post['_1']}_item_price"] > 0) {
                // This image has a price - watermark
                $wmark = true;
            }
        }
        if ($wmark) {

            // See if we have a custom watermark file
            $mark_file = APP_DIR . '/modules/jrImage/img/watermark.png';
            if (jrCore_media_file_exists(0, 'mod_jrImage_watermark.png')) {
                $mark_file = jrCore_confirm_media_file_is_local(0, 'mod_jrImage_watermark.png');
            }

            $wmark = imagecreatefrompng($mark_file);
            if (is_resource($wmark)) {
                $wtr_x = imagesx($wmark);
                $wtr_y = imagesy($wmark);
                // We will make sure our watermark is no larger than HALF the width of the source
                if ($wtr_x > ($_post['width'] / 2)) {
                    // We need to resize our watermark
                    $tmp_width  = round($_post['width'] / 2);
                    $tmp_height = round(($tmp_width / $wtr_x) * $wtr_y);
                    $tmp_mark   = imagecreatetruecolor($tmp_width, $tmp_height);
                    if (is_resource($tmp_mark)) {
                        imagealphablending($tmp_mark, false);
                        imagesavealpha($tmp_mark, true);
                        imagecopyresampled($tmp_mark, $wmark, 0, 0, 0, 0, $tmp_width, $tmp_height, $wtr_x, $wtr_y);
                        $wmark = $tmp_mark;
                    }
                    $wtr_x = $tmp_width;
                    $wtr_y = $tmp_height;
                    unset($tmp_mark);
                }
                // Positioning
                if (isset($_data['quota_jrImage_watermark_x_offset']) && $_data['quota_jrImage_watermark_x_offset'] >= 0) {
                    $img_x = (int) $_data['quota_jrImage_watermark_x_offset'];
                    if ($_post['width'] < 100) {
                        $img_x = round($img_x / 2);
                    }
                }
                else {
                    $img_x = (($_post['width'] - $wtr_x) - intval($_data['quota_jrImage_watermark_x_offset'] * -1));
                    if ($_post['width'] < 100) {
                        $img_x -= round(intval($_data['quota_jrImage_watermark_x_offset'] * -1) / 2);
                    }
                }
                if (isset($_data['quota_jrImage_watermark_y_offset']) && $_data['quota_jrImage_watermark_y_offset'] >= 0) {
                    $img_y = (int) $_data['quota_jrImage_watermark_y_offset'];
                    if ($_post['width'] < 100) {
                        $img_y = round($img_y / 2);
                    }
                }
                else {
                    $img_y = (($_post['height'] - $wtr_y) - intval($_data['quota_jrImage_watermark_y_offset'] * -1));
                    if ($_post['width'] < 100) {
                        $img_y += round(intval($_data['quota_jrImage_watermark_y_offset'] * -1) / 2);
                    }
                }
                jrImage_imagecopymerge_alpha($image['handle'], $wmark, $img_x, $img_y, 0, 0, $wtr_x, $wtr_y, 100);
            }
            imagedestroy($wmark);
        }
    }

    //----------------------------------
    // Check for filters
    //----------------------------------
    if (is_array($_filter)) {
        // run our filters
        foreach ($_filter as $filt) {
            $_flt = explode(':', $filt);
            $func = "jrImage_filter_{$_flt[0]}";
            if (function_exists($func)) {
                $ftmp = $func($image['handle'], $_flt, $new_height, $new_width);
                if (isset($ftmp) && is_resource($ftmp)) {
                    $image['handle'] = $ftmp;
                    $new_height      = imagesy($image['handle']);
                    $new_width       = imagesx($image['handle']);
                    unset($ftmp);
                    $func = "jrImage_filter_{$_flt[0]}_extension";
                    if (function_exists($func)) {
                        $ext = $func();
                        if ($ext) {
                            $cache_file                = str_replace(".{$_image['image_extension']}", ".{$ext}", $cache_file);
                            $_image['image_extension'] = $ext;
                        }
                    }
                }
            }
        }
    }

    //----------------------------------
    // Create Cached image
    //----------------------------------
    switch (strtolower($_image['image_extension'])) {
        case 'jpg':
        case 'jpe':
        case 'jpeg':
        case 'jfif':
            imagejpeg($image['handle'], $cache_file, $image['quality']);
            break;
        case 'png':
            imagepng($image['handle'], $cache_file);
            break;
        case 'gif':
            if (function_exists('imagegif')) {
                imagecolortransparent($image['handle']);
                imagegif($image['handle'], $cache_file);
            }
            else {
                imagepng($image['handle'], $cache_file);
            }
            break;
    }
    imagedestroy($image['source']);
    chmod($cache_file, $_conf['jrCore_file_perms']);
    return $cache_file;
}

/**
 * Display the "default" image when no image is available
 * @param $_post array incoming $_post
 * @param $_conf array System Config
 * @param $_item array DS Item
 * @return null
 */
function jrImage_display_default_image($_post, $_conf, $_item = null)
{
    jrCore_db_close();
    jrUser_ignore_action();
    // Make sure we get a valid image width
    $_sz = jrImage_get_allowed_image_widths();
    if (!isset($_sz["{$_post['_3']}"])) {
        jrCore_notice('CRI', "invalid image size - must be one of: " . implode(',', array_flip($_sz)));
    }
    $_post['width']         = $_sz["{$_post['_3']}"];
    $_post['default_image'] = true;

    // Get any custom images
    $_cust      = (isset($_conf["jrCore_{$_post['module']}_custom_images"]{2})) ? json_decode($_conf["jrCore_{$_post['module']}_custom_images"], true) : array();
    $_custjrImg = (isset($_conf["jrCore_jrImage_custom_images"]{2})) ? json_decode($_conf["jrCore_jrImage_custom_images"], true) : array();

    // Check for default image over ride
    $img = APP_DIR . "/modules/jrImage/img/default.png";
    if (is_file(APP_DIR . "/data/media/0/0/mod_{$_post['module']}_default.png") && isset($_cust['default.png'][1]) && $_cust['default.png'][1] != "off") {
        $img = APP_DIR . "/data/media/0/0/mod_{$_post['module']}_default.png";
    }
    elseif (is_file(APP_DIR . "/data/media/0/0/mod_jrImage_default.png") && isset($_custjrImg['default.png'][1]) && $_custjrImg['default.png'][1] != "off") {
        $img = APP_DIR . "/data/media/0/0/mod_jrImage_default.png";
    }
    elseif (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/img/{$_post['module']}_{$_post['_1']}_default.png")) {
        $img = APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/img/{$_post['module']}_{$_post['_1']}_default.png";
    }
    elseif (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/img/{$_post['module']}_default.png")) {
        $img = APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/img/{$_post['module']}_default.png";
    }
    elseif (is_file(APP_DIR . "/modules/{$_post['module']}/img/default.png")) {
        $img = APP_DIR . "/modules/{$_post['module']}/img/default.png";
    }
    elseif (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/img/default.png")) {
        $img = APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/img/default.png";
    }

    // trigger event for any other changes to the default image
    $_data = array(
        'img'   => $img,
        '_item' => $_item
    );
    $_data = jrCore_trigger_event('jrImage', 'default_image', $_data);
    $img   = $_data['img'];
    $_im   = getimagesize($img);

    // get sized/cached image for display
    $_rt = array(
        'image_name'      => 'default.png',
        'image_type'      => 'image/png',
        'image_size'      => filesize($img),
        'image_width'     => $_im[0],
        'image_height'    => $_im[1],
        'image_extension' => 'png'
    );

    $key = json_encode($_rt) . $_conf['jrCore_active_skin'] . $_post['module'] . $_post['_1'] . $_post['_3'];
    if (isset($_post['crop'])) {
        $key .= $_post['crop'];
    }
    if (isset($_post['filter'])) {
        $key .= $_post['filter'];
    }
    $key = md5($key);
    $cdr = jrCore_get_module_cache_dir('jrImage');
    $new = "{$cdr}/default_{$key}.png";
    if (!is_file($new)) {
        // Create our resized image and cache it
        $new = jrImage_create_image($img, $_rt, $_post, $_conf, $new, $_rt);
    }

    jrCore_set_custom_header("Content-Type: {$_rt['image_type']}");
    jrCore_set_custom_header('Content-Disposition: inline; filename="' . $_rt['image_name'] . '"');
    jrCore_set_custom_header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400000));
    jrCore_send_response_and_detach(file_get_contents($new), true);
    exit;
}

/**
 * Get the allowed sizes (in bytes) for use in ACP config
 */
function jrImage_get_allowed_image_sizes()
{
    $_todo = array(
        131072, 262144, 393216, 524288, 655360, 786432, 1048576, 1572864, 2097152, 2621440, 3145728, 3670016, 4194304, 4718592, 5242880, 6291456, 7340032, 8388608, 9437184, 10485760
    );
    $_out  = array();
    $cmax  = jrCore_get_max_allowed_upload();
    foreach ($_todo as $size) {
        if ($size <= $cmax) {
            $_out[$size] = jrCore_format_size($size);
        }
    }
    return $_out;
}

/**
 * Copy image to another image preserving transparency
 * @param $dst_im resource
 * @param $src_im resource
 * @param $dst_x int
 * @param $dst_y int
 * @param $src_x int
 * @param $src_y int
 * @param $src_w int
 * @param $src_h int
 * @param $pct int
 */
function jrImage_imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
{
    // creating a cut resource
    $cut = imagecreatetruecolor($src_w, $src_h);

    // copying relevant section from background to the cut resource
    imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);

    // copying relevant section from watermark to the cut resource
    imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);

    // insert cut resource to destination image
    imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
}

/**
 * Plug-and-Play function replaces much slower imagecopyresampled.
 * Typically from 30 to 60 times faster when reducing high resolution images down to thumbnail size using the default quality setting.
 * Author: Tim Eckel - Date: 09/07/07 - Version: 1.1 - Project: FreeRingers.net - Freely distributable - These comments must remain.
 * Optional "quality" parameter (defaults is 3). Fractional values are allowed, for example 1.5. Must be greater than zero.
 * Between 0 and 1 = Fast, but mosaic results, closer to 0 increases the mosaic effect.
 * 1 = Up to 350 times faster. Poor results, looks very similar to imagecopyresized.
 * 2 = Up to 95 times faster.  Images appear a little sharp, some prefer this over a quality of 3.
 * 3 = Up to 60 times faster.  Will give high quality smooth results very close to imagecopyresampled, just faster.
 * 4 = Up to 25 times faster.  Almost identical to imagecopyresampled for most images.
 * 5 = No speedup. Just uses imagecopyresampled, no advantage over imagecopyresampled.
 * @param resource $dst_image
 * @param resource $src_image
 * @param int $dst_x
 * @param int $dst_y
 * @param int $src_x
 * @param int $src_y
 * @param int $dst_w
 * @param int $dst_h
 * @param int $src_w
 * @param int $src_h
 * @param int $quality
 * @return bool
 */
function jrImage_imagecopyresampled(&$dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, $quality = 2)
{
    if (empty($src_image) || empty($dst_image) || $quality <= 0) {
        return false;
    }
    if ($quality < 5 && (($dst_w * $quality) < $src_w || ($dst_h * $quality) < $src_h)) {
        $temp = imagecreatetruecolor($dst_w * $quality + 1, $dst_h * $quality + 1);
        imagecopyresized($temp, $src_image, 0, 0, $src_x, $src_y, $dst_w * $quality + 1, $dst_h * $quality + 1, $src_w, $src_h);
        imagecopyresampled($dst_image, $temp, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $dst_w * $quality, $dst_h * $quality);
        imagedestroy($temp);
    }
    else {
        imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
    }
    return true;
}

//-------------------------
// IMAGE FILTERS
//-------------------------

/**
 * blur
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_blur($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_GAUSSIAN_BLUR);
    return $handle;
}

/**
 * border
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_border($handle, $_args, $height, $width)
{
    // Border width in pixels is first
    if (!is_numeric($_args[1])) {
        $_args[1] = 1;
    }
    $_args[1] = intval($_args[1]);
    // Make sure we get good values, or default to 127
    foreach (array(2, 3, 4) as $num) {
        if (!isset($_args[$num]) || !is_numeric($_args[$num])) {
            $_args[$num] = 0;
        }
        $_args[$num] = intval($_args[$num]);
    }
    $color = imagecolorallocate($handle, $_args[2], $_args[3], $_args[4]);
    $brd_x = 0;
    $brd_y = 0;
    $img_x = (imagesx($handle) - 1);
    $img_y = (imagesy($handle) - 1);
    for ($i = 0; $i < $_args[1]; $i++) {
        imagerectangle($handle, $brd_x++, $brd_y++, $img_x--, $img_y--, $color);
    }
    return $handle;
}

/**
 * brightness
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_brightness($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_BRIGHTNESS, $_args[1]);
    return $handle;
}

/**
 * colorize
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_colorize($handle, $_args, $height, $width)
{
    // Make sure we get good values, or default to 127
    foreach (array(1, 2, 3, 4) as $num) {
        if (!isset($_args[$num]) || !is_numeric($_args[$num])) {
            $_args[$num] = 127;
        }
    }
    imagefilter($handle, IMG_FILTER_COLORIZE, $_args[1], $_args[2], $_args[3], $_args[4]);
    return $handle;
}

/**
 * contrast
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_contrast($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_CONTRAST, $_args[1]);
    return $handle;
}

/**
 * edgedetect
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_edgedetect($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_EDGEDETECT);
    return $handle;
}

/**
 * emboss
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_emboss($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_EMBOSS);
    return $handle;
}

/**
 * grayscale
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_grayscale($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_GRAYSCALE);
    return $handle;
}

/**
 * negative
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_negative($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_NEGATE);
    return $handle;
}

/**
 * pixelate
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_pixelate($handle, $_args, $height, $width)
{
    if (!isset($_args[1]) || !is_numeric($_args[1])) {
        $_args[1] = 5;
    }
    if (!isset($_args[2])) {
        $_args[2] = true;
    }
    imagefilter($handle, IMG_FILTER_PIXELATE, $_args[1], $_args[2]);
    return $handle;
}

/**
 * rotate
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return mixed
 */
function jrImage_filter_rotate($handle, $_args, $height, $width)
{
    if (!is_numeric($_args[1]) || $_args[1] <= 0 || $_args[1] >= 360) {
        return $handle;
    }
    if (!isset($_args[2])) {
        $_args[2] = 0;
    }
    $handle = imagerotate($handle, $_args[1], intval($_args[2]));
    imagealphablending($handle, true);
    imagesavealpha($handle, true);

    // See if our rotation affected the size of the image - if so, we need to resize
    $new_h = imagesy($handle);
    $new_w = imagesx($handle);
    if ($new_h != $height || $new_w != $width) {
        $new_resource = imagecreatetruecolor($width, $height);
        if (!imagecopyresampled($new_resource, $handle, 0, 0, 0, 0, $width, $height, $new_w, $new_h)) {
            if (!imagecopyresized($new_resource, $handle, 0, 0, 0, 0, $width, $height, $new_w, $new_h)) {
                return 'unable to create new source image from rotate resulting in new image size';
            }
        }
        $handle = $new_resource;
    }
    return $handle;
}

/**
 * sepia
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_sepia($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_GRAYSCALE);
    imagefilter($handle, IMG_FILTER_BRIGHTNESS, -30);
    imagefilter($handle, IMG_FILTER_COLORIZE, 90, 55, 30);
    return $handle;
}

/**
 * sketch
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_sketch($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_MEAN_REMOVAL);
    return $handle;
}

/**
 * smooth
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_smooth($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_SMOOTH, $_args[1]);
    return $handle;
}

/**
 * rounded corners
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return mixed
 */
function jrImage_filter_rounded($handle, $_args, $height, $width)
{
    $_arg = array();
    if (!isset($_args[2]) && is_numeric($_args[1]) && $_args[1] > 1) {
        $_arg[1] = $_arg[2] = $_arg[3] = $_arg[4] = $_args[1];
    }
    elseif (count($_args) === 5) {
        foreach ($_args as $k => $v) {
            if ($k < 1) {
                continue;
            }
            if (!is_numeric($v)) {
                return false;
            }
            $_arg[$k] = (int) $v;
        }
    }
    else {
        return false;
    }
    if ($tp_col = imagecolorallocatealpha($handle, 0, 0, 0, 127)) {
        $_r = $_c = array();
        // radius (ellipse width and height)
        foreach ($_arg as $k => $v) {
            switch ($v) {
                case 2:
                case 4:
                case 6:
                case 8:
                case 10:
                    $_r[$k] = round($v * 3.3);
                    break;
                default:
                    $_r[$k] = round($v * 3);
                    break;
            }
            // Center of ellipse
            $_c[$k] = round($v * .45);
        }
        // Upper Left
        if ($_arg[1] > 0) {
            $x = ($_arg[1] + $_c[1]); // x offset
            $y = ($_arg[1] + $_c[1]); // y offset
            imagearc($handle, $x, $y, $_r[1], $_r[1], 180, 270, $tp_col);
            imagefilltoborder($handle, 0, 0, $tp_col, $tp_col);
        }
        // Lower Left
        if ($_arg[4] > 0) {
            $x = ($_arg[4] + $_c[4]); // x offset
            $y = (($height - 1) - ($_arg[4] + $_c[4])); // y offset
            imagearc($handle, $x, $y, $_r[4], $_r[4], 90, 180, $tp_col);
            imagefilltoborder($handle, 1, ($height - 1), $tp_col, $tp_col);
        }
        // Upper Right
        if ($_arg[2] > 0) {
            $x = (($width - 1) - ($_arg[2] + $_c[2])); // x offset
            $y = ($_arg[2] + $_c[2]); // y offset
            imagearc($handle, $x, $y, $_r[2], $_r[2], 270, 0, $tp_col);
            imagefilltoborder($handle, ($width - 1), 1, $tp_col, $tp_col);
        }
        // Lower Right
        if ($_arg[3] > 0) {
            $x = (($width - 1) - ($_arg[3] + $_c[3])); // x offset
            $y = (($height - 1) - ($_arg[3] + $_c[3])); // x offset
            imagearc($handle, $x, $y, $_r[3], $_r[3], 0, 90, $tp_col);
            imagefilltoborder($handle, ($width - 1), ($height - 1), $tp_col, $tp_col);
        }
        return $handle;
    }
    return false;
}

/**
 * rounded corners - extension (must be png)
 * @return string
 */
function jrImage_filter_rounded_extension()
{
    return 'png';
}

/**
 * cut corners
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return mixed
 */
function jrImage_filter_cut($handle, $_args, $height, $width)
{
    $_cc = array();
    if (!isset($_args[2]) && is_numeric($_args[1])) {
        $_cc[1] = $_cc[2] = $_cc[3] = $_cc[4] = $_args[1];
    }
    elseif (count($_args) === 5) {
        foreach ($_args as $k => $v) {
            if ($k < 1) {
                continue;
            }
            if (!is_numeric($v)) {
                return false;
            }
            $_cc[$k] = (int) $v;
        }
    }
    else {
        return false;
    }
    if ($tp_col = imagecolorallocatealpha($handle, 0, 0, 0, 127)) {
        // Upper Left
        if ($_cc[1] > 0) {
            imageline($handle, 0, $_cc[1], $_cc[1], 0, $tp_col);
            imagefilltoborder($handle, 0, 0, $tp_col, $tp_col);
        }
        // Lower Left
        if ($_cc[4] > 0) {
            imageline($handle, 0, (($height - 1) - $_cc[4]), $_cc[4], ($height - 1), $tp_col);
            imagefilltoborder($handle, 0, ($height - 1), $tp_col, $tp_col);
        }
        // Upper Right
        if ($_cc[2] > 0) {
            imageline($handle, (($width - 1) - $_cc[2]), 0, ($width - 1), $_cc[2], $tp_col);
            imagefilltoborder($handle, ($width - 1), 0, $tp_col, $tp_col);
        }
        // Lower Right
        if ($_cc[3] > 0) {
            imageline($handle, ($width - 1), (($height - 1) - $_cc[3]), (($width - 1) - $_cc[3]), ($height - 1), $tp_col);
            imagefilltoborder($handle, ($width - 1), ($height - 1), $tp_col, $tp_col);
        }
        return $handle;
    }
    return false;
}

/**
 * cut corners - extension (must be png)
 * @return string
 */
function jrImage_filter_cut_extension()
{
    return 'png';
}

/**
 * reflection
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return mixed
 */
function jrImage_filter_reflection($handle, $_args, $height, $width)
{
    if (!function_exists('imagelayereffect')) {
        return false;
    }
    $rf_h = round($height * ($_args[1] / 100));
    // Buffer to hold our reflection
    if ($buff = imagecreatetruecolor($width, $rf_h)) {

        imagesavealpha($handle, true);
        imagealphablending($handle, false);
        imagesavealpha($buff, true);
        imagealphablending($buff, false);

        // We need to "squish" the reflection into a bit smaller space to give it the
        // appearance that it is coming "forward" of the image
        $rf_h = round($rf_h * .50);

        for ($y = 0; $y < $rf_h; $y++) {
            imagecopy($buff, $handle, 0, $y, 0, ($height - $y - 1), $width, 1);
        }
        $alpha_s = 80;
        $alpha_e = 0;
        $alpha_l = abs($alpha_s - $alpha_e);

        $new = imagecreatetruecolor($width, ($height + $rf_h));
        imagesavealpha($new, true);
        imagealphablending($new, false);

        imagecopy($new, $handle, 0, 0, 0, 0, $width, $height);
        imagecopy($new, $buff, 0, $height, 0, 0, $width, $rf_h);

        imagelayereffect($new, IMG_EFFECT_OVERLAY);

        for ($y = 0; $y <= $rf_h; $y++) {
            //  Get % of reflection height
            $pct   = ($y / $rf_h);
            $alpha = (int) ($alpha_s - ($pct * $alpha_l));
            $alpha = (127 - $alpha);
            imagefilledrectangle($new, 0, ($height + $y), $width, ($height + $y), imagecolorallocatealpha($new, 127, 127, 127, $alpha));
        }
        imagedestroy($buff);
        return $new;
    }
    return false;
}

/**
 * reflection - extension (must be png)
 * @return string
 */
function jrImage_filter_reflection_extension()
{
    return 'png';
}

//-------------------------
// SMARTY
//-------------------------

/**
 * Display a "stacked" image setup
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrImage_stacked_image($params, $smarty)
{
    // Make sure we get what we need
    if (!isset($params['module']{0})) {
        return 'jrImage_stacked_image: module parameter required';
    }
    if (!isset($params['type']{0})) {
        return 'jrImage_stacked_image: image type parameter required';
    }
    if (!isset($params['item_id']) || strlen($params['item_id']) === 0) {
        return 'jrImage_stacked_image: image item_id parameter required';
    }
    if (!isset($params['size']{0})) {
        return 'jrImage_stacked_image: image size parameter required';
    }
    $_sz = jrImage_get_allowed_image_widths();
    if (!isset($_sz["{$params['size']}"])) {
        return 'jrImage_stacked_image: invalid size parameter';
    }

    $bw = 2;
    if (isset($params['border_width'])) {
        $bw = (int) $params['border_width'];
    }
    $bs = 'solid';
    if (isset($params['border_style'])) {
        $bs = $params['border_style'];
    }
    $bc = '#FFF';
    if (isset($params['border_color'])) {
        $bc = $params['border_color'];
    }

    $_md = explode(',', $params['module']);
    $_ty = explode(',', $params['type']);
    $_im = explode(',', $params['item_id']);
    // figure our height and width  of the holding div.
    // You cannot do this in CSS for absolutely positioned elements within a DIV
    $off = round($_sz["{$params['size']}"] / 6);
    $isz = intval(count($_im) - 1);
    $p_h = $_sz["{$params['size']}"] + ($off * $isz) + ((count($_im) + 1) * $bw);
    $p_w = $_sz["{$params['size']}"] + ($off * $isz) + ($bw * 2);
    $out = '<div class="image_stack" style="display:inline-block;position:relative;height:' . $p_h . 'px;width:' . $p_w . 'px">' . "\n";
    foreach ($_im as $k => $iid) {
        if (!jrCore_checktype($iid, 'number_nz')) {
            continue;
        }
        $_tm            = $params;
        $_tm['item_id'] = $iid;
        // See if we were already given a class
        if (isset($_tm['class']{0})) {
            $_tm['class'] .= " image_stack{$k}";
        }
        else {
            $_tm['class'] = "image_stack{$k}";
        }
        $_tm['style'] = "position:absolute;z-index:" . ($k * 10) . ";border-width:{$bw}px;border-style:{$bs};border-color:{$bc}";
        if ($k > 0) {
            $t_off        = round($_sz["{$params['size']}"] / 6) * $k;
            $l_off        = $t_off * 2;
            $_tm['style'] .= ";top:{$t_off}px;left:{$l_off}px";
        }
        // Check for module
        $_tm['module'] = $_md[0];
        if (isset($_md[$k])) {
            $_tm['module'] = $_md[$k];
        }
        $_tm['type'] = $_ty[0];
        if (isset($_ty[$k])) {
            $_tm['type'] = $_ty[$k];
        }
        $_tm['crop'] = 'auto';
        $out         .= smarty_function_jrImage_display($_tm, $smarty) . "\n";
    }
    $out .= '</div>';
    return $out;
}

/**
 * Display an image for a module
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrImage_display($params, $smarty)
{
    global $_conf;
    // Make sure we get what we need
    if (!isset($params['module']{0})) {
        return 'jrImage_display: module parameter required';
    }
    if (!isset($params['type']{0})) {
        return 'jrImage_display: image type parameter required';
    }
    if (!isset($params['item_id']) || !jrCore_checktype($params['item_id'], 'number_nn')) {
        return 'jrImage_display: image item_id parameter required';
    }
    if (!isset($params['size']) || strlen($params['size']) === 0) {
        return 'jrImage_display: image size parameter required';
    }
    $_sz = jrImage_get_allowed_image_widths();
    if (!isset($_sz["{$params['size']}"])) {
        return 'jrImage_display: invalid size parameter';
    }

    // Unique URL tag
    switch ($params['module']) {
        case 'jrUser':
            $key = '_user_id';
            break;
        case 'jrProfile':
            $key = '_profile_id';
            break;
        default:
            $key = '_item_id';
            break;
    }

    // Check for height and width.  If our height or width are passed
    // in as false, we do NOT set them (for CSS media queries).

    // Width
    $wid = '';
    if (isset($params['width']) && jrCore_checktype($params['width'], 'number_nz')) {
        $wid = " width=\"" . $params['width'] . "\"";
    }
    elseif (isset($params['width']) && ($params['width'] === false || $params['width'] == 'false')) {
        $wid = '';
    }
    elseif (jrCore_checktype($_sz["{$params['size']}"], 'number_nz')) {
        $wid = " width=\"" . $_sz["{$params['size']}"] . "\"";
    }

    // Height
    $hgt = '';
    if (isset($params['height']) && jrCore_checktype($params['height'], 'number_nz')) {
        $hgt = " height=\"" . $params['height'] . "\"";
    }
    if (strlen($hgt) === 0 && (isset($params['height']) && $params['height'] !== false)) {
        $hgt = " height=\"" . $_sz["{$params['size']}"] . "\"";
    }

    // Get last time image was updated
    if (!isset($params['_v'])) {
        if (isset($smarty->tpl_vars['item']->value[$key]) && $smarty->tpl_vars['item']->value[$key] == $params['item_id']) {
            if (isset($smarty->tpl_vars['item']->value["{$params['type']}_time"])) {
                $params['_v'] = (int) $smarty->tpl_vars['item']->value["{$params['type']}_time"];
            }
        }
        elseif ($key == '_profile_id' && isset($smarty->tpl_vars['_profile_id']) && $smarty->tpl_vars['_profile_id']->value == $params['item_id']) {
            if (isset($smarty->tpl_vars['profile_image_time']->value)) {
                $params['_v'] = (int) $smarty->tpl_vars['profile_image_time']->value;
            }
        }
        elseif (isset($smarty->tpl_vars['_items']) && is_array($smarty->tpl_vars['_items']->value)) {
            foreach ($smarty->tpl_vars['_items']->value as $v) {
                if (isset($v[$key]) && $v[$key] == $params['item_id'] && isset($v["{$params['type']}_time"])) {
                    $params['_v'] = (int) (int) $v["{$params['type']}_time"];
                    break;
                }
            }
        }
    }

    // Get active media system
    $url = false;
    $plg = jrCore_get_active_media_system();
    $fnc = "_{$plg}_media_get_image_url";
    if (function_exists($fnc)) {
        $url = $fnc($params, $smarty);
    }
    if (!$url) {
        $url = jrCore_get_module_url($params['module']);
        $url = "{$_conf['jrCore_base_url']}/{$url}/image/{$params['type']}/{$params['item_id']}/{$params['size']}";

        // Check for cropping and filters
        if (isset($params['crop'])) {
            $url .= "/crop={$params['crop']}";
        }
        if (isset($params['fillcolor'])) {
            $url .= "/fillcolor={$params['fillcolor']}";
        }
        if (isset($params['filter'])) {
            $url .= "/filter={$params['filter']}";
        }
        if (isset($params['_v']) && strlen($params['_v']) > 0) {
            $url .= '/_v=' . (int) $params['_v'];
        }
    }

    // Additional tags
    if (!isset($params['alt'])) {
        $params['alt'] = '';
    }
    $_chck = array('alt', 'class', 'style', 'id', 'title', 'crossorigin');
    $attrs = '';
    foreach ($_chck as $attr) {
        if (isset($params[$attr])) {
            $attrs .= " {$attr}=\"" . jrCore_entity_string($params[$attr]) . "\"";
        }
    }
    // Our final img source URL
    $_rs = array(
        'src' => "<img src=\"{$url}\"" . $wid . $hgt . $attrs . '>'
    );
    // Trigger display event
    $_rs = jrCore_trigger_event('jrImage', 'img_src', $_rs, $params);
    // Assign?
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $_rs['src']);
        return '';
    }
    return $_rs['src'];
}
