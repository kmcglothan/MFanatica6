<?php
/**
 * Jamroom Image Support module
 *
 * copyright 2017 The Jamroom Network
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

//------------------------------
// default_img
//------------------------------
function view_jrImage_default_img($_post, $_user, $_conf)
{
    jrImage_display_default_image($_post, $_conf);
}

//------------------------------
// img
//------------------------------
function view_jrImage_img($_post, $_user, $_conf)
{
    global $_urls;
    // http://site.com/image/img/module/jrAudio/img.png
    // http://site.com/image/img/skin/jrElastic/img.png
    $tag = '';
    if ($_post['_1'] == 'module') {
        if (!jrCore_module_is_active($_post['_2'])) {
            jrCore_notice('Error', 'invalid module');
        }
        $tag = 'mod_';
    }
    elseif ($_post['_1'] != 'skin') {
        // Backwards compatibility check
        if (isset($_urls["{$_post['_1']}"])) {
            $_post['_3'] = $_post['_2'];
            $_post['_2'] = $_urls["{$_post['_1']}"];
            $_post['_1'] = 'module';
        }
        else {
            jrCore_notice('Error', 'invalid module');
        }
    }
    if (!isset($_post['_3']) || strlen($_post['_3']) === 0) {
        jrCore_notice('Error', 'invalid image');
    }
    // See if we have a custom file for this image
    $_im = array();
    if (isset($_conf["jrCore_{$_post['_2']}_custom_images"]{2})) {
        $_im = json_decode($_conf["jrCore_{$_post['_2']}_custom_images"], true);
    }
    if (isset($_im["{$_post['_3']}"]) && (!isset($_im["{$_post['_3']}"][1]) || $_im["{$_post['_3']}"][1] == 'on')) {
        $img = APP_DIR . "/data/media/0/0/{$tag}{$_post['_2']}_{$_post['_3']}";
    }
    elseif ($_post['_1'] == 'module') {
        // Do we have a skin override?
        $ovr = APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/img/{$_post['_2']}_{$_post['_3']}";
        if (is_file($ovr)) {
            $img = $ovr;
        }
        else {
            $img = APP_DIR . "/modules/{$_post['_2']}/img/{$_post['_3']}";
        }
    }
    else {
        $img = APP_DIR . "/skins/{$_post['_2']}/img/{$_post['_3']}";
    }

    // Custom headers added by modules
    $_tmp = jrCore_get_flag('jrcore_set_custom_header');
    if (isset($_tmp) && is_array($_tmp)) {
        foreach ($_tmp as $header) {
            header($header);
        }
    }

    // Let other modules change our images if needed
    $img = jrCore_trigger_event('jrImage', "{$_post['_1']}_image", $img, $_im);
    $tim = @filemtime($img);
    $ifs = (function_exists('getenv')) ? getenv('HTTP_IF_MODIFIED_SINCE') : false;
    if (!$ifs && isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
        $ifs = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
    }
    if ($ifs && strtotime($ifs) == $tim) {
        header("Last-Modified: " . gmdate('r', $tim));
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 8640000));
        header("HTTP/1.1 304 Not Modified");
        exit;
    }
    switch (jrCore_file_extension($_post['_3'])) {
        case 'jpg':
        case 'jpe':
        case 'jpeg':
        case 'jfif':
            header("Content-type: image/jpeg");
            break;
        case 'png':
            header("Content-type: image/png");
            break;
        case 'gif':
            header("Content-type: image/gif");
            break;
        case 'ico':
            header("Content-type: image/x-icon");
            break;
        case 'svg':
            header("Content-type: image/svg+xml");
            break;
        default:
            jrCore_notice('Error', 'invalid image');
            break;
    }
    header("Last-Modified: " . gmdate('r', $tim));
    header('Content-Disposition: inline; filename="' . $_post['_3'] . '"');
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 8640000));
    echo @file_get_contents($img);
    session_write_close();
    exit;
}

//------------------------------
// cache_reset
//------------------------------
function view_jrImage_cache_reset($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrImage');
    $_mta = jrCore_module_meta_data($_post['module']);
    jrCore_page_banner("{$_mta['name']} - Cache Reset");

    // Form init
    $_tmp = array(
        'submit_value'  => 'reset image cache',
        'cancel'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'submit_prompt' => 'Are you sure you want to reset the image cache?'
    );
    jrCore_form_create($_tmp);

    // Cache Reset
    $_tmp = array(
        'name'     => 'image_cache_reset',
        'label'    => 'reset cache',
        'help'     => 'Check this option to reset the resized image cache. Note that this has no impact on the original images uploaded by users.',
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// cache_reset_save
//------------------------------
function view_jrImage_cache_reset_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Setup new cache dir for all new requests
    if (isset($_post['image_cache_reset']) && $_post['image_cache_reset'] == 'on') {

        $cdr = jrCore_get_module_cache_dir('jrImage');
        $old = $_conf['jrImage_active_cache_dir'];
        $dir = substr(md5(microtime()), 0, 5);
        if (!is_dir("{$cdr}/{$dir}")) {
            mkdir("{$cdr}/{$dir}", $_conf['jrCore_dir_perms'], true);
        }
        // Update to new setting
        if (jrCore_set_setting_value('jrImage', 'active_cache_dir', $dir)) {

            jrCore_delete_config_cache();

            // Delete old directory via a Queue Worker
            $_queue = array('cache_dir' => $old);
            jrCore_queue_create('jrImage', 'clear_cache', $_queue, 10);

            jrCore_set_form_notice('success', 'The image cache was successfully reset');
            jrCore_form_delete_session();
        }
        else {
            jrCore_set_form_notice('error', 'an error was encountered saving the new cache directory to the global settings');
        }
    }
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/cache_reset");
}

//------------------------------
// delete
//------------------------------
function view_jrImage_delete($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    // [module_url] => image
    // [module] => jrImage
    // [option] => delete
    // [_1] => jrProfile
    // [_2] => profile_bg_image
    // [_3] => 1
    if (!isset($_post['_1']) || !jrCore_db_get_prefix($_post['_1'])) {
        jrCore_set_form_notice('error', 'Invalid module');
        jrCore_location('referrer');
    }
    if (!isset($_post['_3']) || !jrCore_checktype($_post['_3'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid item_id');
        jrCore_location('referrer');
    }
    // Get info about this item to be sure the requesting user is allowed
    $_rt = jrCore_db_get_item($_post['_1'], $_post['_3'], true);
    if (!isset($_rt) || !is_array($_rt) || !isset($_rt['_profile_id'])) {
        jrCore_set_form_notice('error', 'Invalid item_id (2)');
        jrCore_location('referrer');
    }
    if (!jrUser_is_admin() && !jrProfile_is_profile_owner($_rt['_profile_id'])) {
        jrUser_not_authorized();
    }

    // Remove file
    jrCore_delete_item_media_file($_post['_1'], $_post['_2'], $_rt['_profile_id'], $_post['_3']);

    // If this was a user or profile image, reload session
    switch ($_post['_1']) {

        case 'jrUser':
            jrUser_session_sync($_user['_user_id']);
            jrUser_reset_cache($_user['_user_id'], $_post['_1']);
            break;

        case 'jrProfile':
            jrUser_session_sync($_user['_user_id']);
            jrProfile_reset_cache($_rt['_profile_id'], $_post['_1']);
            break;

    }

    jrProfile_reset_cache($_rt['_profile_id']);
    jrCore_set_form_notice('success', 'The image was successfully deleted');
    jrCore_location('referrer');
}

//------------------------------
// limit_image_size
//------------------------------
function view_jrImage_limit_image_size($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrImage');
    jrCore_page_banner('Resize Item Images', 'Limit item images to a maximum height or width');
    $text = '';
    if (is_dir(APP_DIR . '/modules/jrFoxyCart') || is_dir(APP_DIR . '/modules/jrPayPal')) {
        $text = '<br><b>Note:</b> items that are for sale will not have their image resized.';
    }
    $note = "The Resize Item Images tool searches for item images with a width or height <b>greater</b> than the selected value<br>and if found, reduces them (in proportion) to the new value which can help save server disk space.{$text}<br><br><b>Important!</b> Once images have been resized the original size cannot be recovered!";
    jrCore_page_note($note, false);

    // Form init
    $_tmp = array(
        'submit_value'  => 'Resize Item Images',
        'cancel'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'submit_prompt' => 'Are you sure you want to resize the images?',
        'submit_modal'  => 'update',
        'modal_width'   => 800,
        'modal_height'  => 400,
        'modal_note'    => 'Please be patient while images are being processed'
    );
    jrCore_form_create($_tmp);

    $_opts  = array('jrAudio', 'jrBlog', 'jrEvent', 'jrFile', 'jrGallery', 'jrGroup', 'jrProfile', 'jrStore', 'jrUser', 'jrGroupDiscuss', 'jrGroupPage');
    $_opts2 = array();
    foreach ($_opts as $mod) {
        if (jrCore_module_is_active($mod) && jrCore_db_get_datastore_item_count($mod) > 0) {
            $_opts2[$mod] = $_mods[$mod]['module_name'];
        }
    }
    natcasesort($_opts2);
    $_tmp = array(
        'name'     => 'image_module',
        'label'    => 'Module',
        'help'     => 'Select the module you want to limit the item image size for',
        'options'  => $_opts2,
        'type'     => 'select',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_sz = array();
    foreach (range(256, 2048, 64) as $sz) {
        $_sz[$sz] = "{$sz} pixels";
    }
    $_tmp = array(
        'name'     => 'max_item_size',
        'type'     => 'select',
        'default'  => '2048',
        'options'  => $_sz,
        'label'    => 'Max Item Size',
        'validate' => 'number_nz',
        'help'     => "Select the maximum size (in pixels) for the height or width of the item image:<br><br><b>NOTE:</b> Items that are for sale will not be resized!"
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'dry_run',
        'label'    => 'Test Run',
        'default'  => 'on',
        'type'     => 'checkbox',
        'required' => 'on',
        'validate' => 'onoff',
        'help'     => "If checked, image resizing will not be applied to the images, but the difference between the original and new file sizes will be shown so you can view the potential disk space savings."
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_display();
}

//------------------------------
// limit_image_size_save
//------------------------------
function view_jrImage_limit_image_size_save($_post, &$_user, &$_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_form_validate($_post);

    @ini_set('max_execution_time', 86400); // 24 hours max
    @ini_set('memory_limit', '1024M');

    // Get all module images that need limiting
    $pfx = jrCore_db_get_prefix($_post['image_module']);
    $_s  = array(
        "search"      => array(
            "{$pfx}_image_width > {$_post['max_item_size']} || {$pfx}_image_height > {$_post['max_item_size']}",
            "{$pfx}_image_extension != gif"
        ),
        "order_by"    => array("_created" => "asc"),
        "return_keys" => array(
            "{$pfx}_title",
            "{$pfx}_name",
            "{$pfx}_image_size",
            "{$pfx}_image_width",
            "{$pfx}_image_height",
            "{$pfx}_image_extension",
            "{$pfx}_file_item_price",
            "_item_id",
            "_profile_id",
            "_user_id"
        ),
        "limit"       => jrCore_db_get_datastore_item_count($_post['image_module'])
    );
    $_rt = jrCore_db_search_items($_post['image_module'], $_s);
    if ($_rt && isset($_rt['_items']) && is_array($_rt['_items']) && count($_rt['_items']) > 0) {

        $mod_name  = $_mods["{$_post['image_module']}"]['module_name'];
        $cache_dir = jrCore_get_module_cache_dir('jrImage');

        jrCore_form_modal_notice('update', count($_rt['_items']) . ' total images found for the ' . $mod_name . ' module - analyzing');
        $ctr        = 0;
        $orig_tsize = 0;
        $new_tsize  = 0;
        foreach ($_rt['_items'] as $rt) {
            // If this item is FOR SALE, skip it
            if (isset($rt["{$pfx}_file_item_price"]) && $rt["{$pfx}_file_item_price"] > 0) {
                continue;
            }
            $ctr++;
            // Adjust for profile or user images
            if ($_post['image_module'] == 'jrProfile') {
                $rt['_item_id'] = $rt['_profile_id'];
            }
            elseif ($_post['image_module'] == 'jrUser') {
                $rt['_item_id'] = $rt['_user_id'];
            }
            // Accumulate original file size
            $orig_tsize += $rt["{$pfx}_image_size"];
            // Calculate new width and length
            if ($rt["{$pfx}_image_width"] >= $rt["{$pfx}_image_height"]) {
                $w = $_post['max_item_size'];
                $h = floor($_post['max_item_size'] * $rt["{$pfx}_image_height"] / $rt["{$pfx}_image_width"]);
            }
            else {
                $h = $_post['max_item_size'];
                $w = floor($_post['max_item_size'] * $rt["{$pfx}_image_width"] / $rt["{$pfx}_image_height"]);
            }
            // Source file
            $file = jrCore_get_media_directory($rt['_profile_id']) . "/{$_post['image_module']}_{$rt['_item_id']}_{$pfx}_image.{$rt["{$pfx}_image_extension"]}";
            if ($_post['dry_run'] == 'on') {

                // Its a dry run - just get the new file size
                $target = "{$cache_dir}/resized.{$rt["{$pfx}_image_extension"]}";
                jrImage_resize_image($file, $target, $w);
                $s = filesize($target);
                unlink($target);
                if (($ctr % 50) === 0) {
                    $saved = $orig_tsize - $new_tsize;
                    jrCore_form_modal_notice('update', "{$ctr} images found to resize for a savings of " . jrCore_format_size($saved) . " of disk space");
                }
            }
            else {
                // All good - Do the limiting
                $x = jrImage_resize_image($file, $file, $w);
                if ($x && substr($x, 0, 5) == 'ERROR') {
                    jrCore_form_modal_notice('update', $x);
                    continue;
                }
                $s    = filesize($file);
                $_tmp = array(
                    "{$pfx}_image_size"   => $s,
                    "{$pfx}_image_width"  => $w,
                    "{$pfx}_image_height" => $h,
                );
                jrCore_db_update_item($_post['image_module'], $rt['_item_id'], $_tmp);
                jrCore_form_modal_notice('update', "{$ctr}: successfully resized image: &quot;{$rt["{$pfx}_title"]}&quot;");
            }
            // Accumulate new file size
            $new_tsize += $s;
        }
        // All done - Show results
        jrCore_form_modal_notice('update', "total size of {$ctr} matched {$mod_name} image files BEFORE resizing: " . jrCore_format_size($orig_tsize));
        jrCore_form_modal_notice('update', "total size of {$ctr} matched {$mod_name} image files AFTER resizing: " . jrCore_format_size($new_tsize));
        $saved = $orig_tsize - $new_tsize;
        if ($_post['dry_run'] == 'on') {
            jrCore_form_modal_notice('complete', "Test Run: A total of " . jrCore_format_size($saved) . " of disk space could be saved");
        }
        else {
            jrCore_form_modal_notice('complete', "Success: A total of " . jrCore_format_size($saved) . " of disk space has been saved");
        }
    }
    else {
        jrCore_form_modal_notice('complete', "No images were found that needed to be resized");
    }
    jrCore_form_result("referrer");
}

//------------------------------------------------------------------------
// tinymce 'imagetools' plugin handler
// https://www.tinymce.com/docs/advanced/handle-async-image-uploads
//------------------------------------------------------------------------
function view_jrImage_tinymce_imagetools($_post, $_user, $_conf)
{
    $path = $_FILES['file']['tmp_name'];
    if (is_file($path)) {
        $_rt = array(
            'image_title_url' => jrCore_url_string(basename($_FILES['file']['name']))
        );
        $aid = jrCore_db_create_item('jrImage', $_rt);
        if (!$aid) {
            $_ret = array(
                'success'     => false,
                'success_msg' => 'could not create the database item for the file.'
            );
            jrCore_json_response($_ret);
        }
        jrCore_save_media_file('jrImage', $path, $_user['user_active_profile_id'], $aid, 'image_file');
        $murl = jrCore_get_module_url('jrImage');
        jrCore_json_response(array('location' => "{$_conf['jrCore_base_url']}/{$murl}/image/image_file/{$aid}/original"));
    }
//    jrCore_logger('min', 'tinymce_imagetools failed to save the adjusted file');
}
