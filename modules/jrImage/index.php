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
    if ($_tmp && is_array($_tmp)) {
        foreach ($_tmp as $header) {
            header($header);
        }
    }

    // Let other modules change our images if needed
    $img = jrCore_trigger_event('jrImage', "{$_post['_1']}_image", $img, $_im);
    $tim = @filemtime($img);
    jrImage_send_not_modified($tim, $img);

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

    if (isset($_SESSION)) {
        session_write_close();
    }

    // Required for the process_exit (shutdown function) to detach properly from the client
    if (function_exists('apache_setenv')) {
        apache_setenv('no-gzip', 1);
    }
    ini_set('zlib.output_compression', 0);
    ini_set('implicit_flush', 1);

    // Send output
    // THE ORDER OF THE FOLLOWING STATEMENTS is critical for it
    // to work properly on mod_php, CGI/FastCGI, and FPM - DO NOT CHANGE!
    @ob_end_clean();
    $img = @file_get_contents($img);
    $len = strlen($img);
    header("Last-Modified: " . gmdate('r', $tim));
    header('Content-Disposition: inline; filename="' . $_post['_3'] . '"');
    header('Content-Length: ' . $len);
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 8640000));
    header('Connection: close');
    ignore_user_abort();
    ob_start();
    echo $img;
    ob_end_flush();
    @flush();

    // PHP-FPM only
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }
    $_tmp = array('img_length' => $len);
    jrCore_trigger_event('jrCore', 'process_done', $_tmp);
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
function view_jrImage_cache_reset_save($_post, $_user, $_conf)
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
            $_queue = array(
                'new_cache_dir' => $dir,
                'old_cache_dir' => $old
            );
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
    $_tmp = array('jrPayment', 'jrFoxyCart', 'jrPayPal');
    foreach ($_tmp as $m) {
        if (jrCore_module_is_active($m)) {
            $text = '<br><b>Note:</b> items that are for sale will not have their image resized.';
            break;
        }
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
        'modal_note'    => 'Please be patient while the images are processed'
    );
    jrCore_form_create($_tmp);

    $_opt = array();
    foreach ($_mods as $m => $i) {
        switch ($m) {
            case 'jrCore':
            case 'jrImage':
            case 'jrInject':
            case 'jrListParams':
            case 'jrSearch':
                break;
            default:
                if (jrCore_is_datastore_module($m)) {
                    $_opt[$m] = $i['module_name'];
                }
                break;
        }
    }
    natcasesort($_opt);
    $_tmp = array(
        'name'     => 'image_module',
        'label'    => 'Module',
        'help'     => 'Select the module you want to limit the item image size for',
        'options'  => $_opt,
        'type'     => 'select',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_sz = array();
    foreach (range(256, 2560, 64) as $sz) {
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
function view_jrImage_limit_image_size_save($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_form_validate($_post);

    @ini_set('max_execution_time', 82800); // 23 hours max
    @ini_set('memory_limit', '1024M');

    $pfx       = jrCore_db_get_prefix($_post['image_module']);
    $lid       = 0;
    $ctr       = 1;
    $checked   = 0;
    $old_tsize = 0;
    $new_tsize = 0;
    $mod_name  = $_mods["{$_post['image_module']}"]['module_name'];
    $cache_dir = jrCore_get_module_cache_dir('jrImage');

    $total = jrCore_db_get_datastore_item_count($_post['image_module']);
    if ($total > 0) {
        jrCore_form_modal_notice('update', 'Checking ' . jrCore_number_format($total) . ' ' . $mod_name . ' items for images...');

        // Get all module item images that need to be resized
        while (true) {
            $_rt = array(
                "search"         => array(
                    "_item_id > {$lid}"
                ),
                'return_keys'    => array(
                    "_item_id",
                    "{$pfx}_title",
                    "{$pfx}_image_size",
                    "{$pfx}_image_width",
                    "{$pfx}_image_height",
                    "{$pfx}_image_extension",
                    "{$pfx}_file_name",
                    "{$pfx}_file_size",
                    "{$pfx}_file_width",
                    "{$pfx}_file_height",
                    "{$pfx}_file_extension",
                    "{$pfx}_file_item_price"
                ),
                'skip_triggers'  => true,
                'ignore_pending' => true,
                'limit'          => 1000
            );
            $_rt = jrCore_db_search_items($_post['image_module'], $_rt);
            if ($_rt && isset($_rt['_items']) && is_array($_rt['_items']) && count($_rt['_items']) > 0) {

                $checked += count($_rt['_items']);
                jrCore_form_modal_notice('update', 'Found ' . jrCore_number_format($checked) . ' ' . $mod_name . ' items - scanning for images to resize');
                foreach ($_rt['_items'] as $rt) {

                    if (isset($rt["{$pfx}_title"])) {
                        $name = $rt["{$pfx}_title"];
                    }
                    else {
                        $name = $rt["{$pfx}_file_name"];
                    }
                    $lid = $rt['_item_id'];

                    // If this item is FOR SALE, skip it
                    if (isset($rt["{$pfx}_file_item_price"]) && $rt["{$pfx}_file_item_price"] > 0) {
                        jrCore_form_modal_notice('update', "{$ctr}: ID {$lid} &quot;{$name}&quot; has an item price - skipping");
                        continue;
                    }

                    $ext = false;
                    $fld = false;
                    if (isset($rt["{$pfx}_image_size"]) && $rt["{$pfx}_image_size"] > 0) {
                        // Image File
                        $ext = $rt["{$pfx}_image_extension"];
                        $fld = "{$pfx}_image";
                    }
                    elseif (isset($rt["{$pfx}_file_size"]) && $rt["{$pfx}_file_size"] > 0) {
                        // Attachment
                        $ext = $rt["{$pfx}_file_extension"];
                        $fld = "{$pfx}_file";
                    }
                    if ($ext) {

                        // See if we have an image file
                        switch ($rt["{$fld}_extension"]) {
                            case 'png':
                            case 'jpg':
                            case 'jpe':
                            case 'jpeg':
                            case 'jfif':

                                // Calculate new width and length
                                if ($rt["{$fld}_width"] >= $rt["{$fld}_height"]) {
                                    $w = $_post['max_item_size'];
                                    // Make sure this image is LARGER than what we have requested
                                    if ($w > $rt["{$fld}_width"]) {
                                        // This image is already smaller
                                        continue 2;
                                    }
                                    $h = floor($_post['max_item_size'] * $rt["{$fld}_height"] / $rt["{$fld}_width"]);
                                }
                                else {
                                    $h = $_post['max_item_size'];
                                    // Make sure this image is LARGER than what we have requested
                                    if ($h > $rt["{$fld}_height"]) {
                                        // This image is already smaller
                                        continue 2;
                                    }
                                    $w = floor($_post['max_item_size'] * $rt["{$fld}_width"] / $rt["{$fld}_height"]);
                                }

                                // Source file
                                $file = jrCore_confirm_media_file_is_local($rt['_profile_id'], "{$_post['image_module']}_{$rt['_item_id']}_{$fld}.{$rt["{$fld}_extension"]}");
                                if ($file) {

                                    $target = "{$cache_dir}/resized_" . jrCore_create_unique_string(10) . ".{$rt["{$fld}_extension"]}";
                                    $result = jrImage_resize_image($file, $target, $w);
                                    if ($result && strpos($result, 'ERROR') === 0) {
                                        jrCore_form_modal_notice('update', "{$ctr}: ID {$lid}: {$result}");
                                        continue;
                                    }
                                    $new_size = filesize($target);
                                    $old_size = filesize($file);
                                    if ($new_size < $old_size) {

                                        $ctr++;
                                        $saved = ($old_size - $new_size);
                                        if ($_post['dry_run'] == 'on') {
                                            // dry run - just report size savings
                                            jrCore_form_modal_notice('update', "{$ctr}: ID {$lid} &quot;{$name}&quot; resized would save <b>" . jrCore_format_size($saved) . "</b> disk space");
                                        }
                                        else {
                                            // For real
                                            jrCore_copy_media_file($rt['_profile_id'], $target, "{$_post['image_module']}_{$rt['_item_id']}_{$fld}.{$rt["{$fld}_extension"]}");
                                            $_tmp = array(
                                                "{$fld}_size"   => $new_size,
                                                "{$fld}_width"  => $w,
                                                "{$fld}_height" => $h,
                                            );
                                            jrCore_db_update_item($_post['image_module'], $rt['_item_id'], $_tmp, null, false);
                                            jrCore_form_modal_notice('update', "{$ctr}: ID {$lid} &quot;{$name}&quot; was successfully resized saving <b>" . jrCore_format_size($saved) . "</b> disk space");
                                        }
                                        $new_tsize += $new_size;
                                        $old_tsize += $old_size;
                                    }
                                    else {
                                        jrCore_form_modal_notice('update', "{$ctr}: ID {$lid} &quot;{$name}&quot; is smaller than the requested size");
                                    }
                                    if (is_file($target)) {
                                        unlink($target);
                                    }
                                }
                                else {
                                    jrCore_form_modal_notice('update', "{$ctr}: ID {$lid} &quot;{$name}&quot; unable to locate media file");
                                }
                                break;
                        }
                    }
                    else {
                        jrCore_form_modal_notice('update', "{$ctr}: ID {$lid} &quot;{$name}&quot; does not have a valid image extension");
                    }
                }
                if (count($_rt['_items']) < 1000) {
                    // We're at the end
                    break;
                }
            }
            else {
                break;
            }
        }

        // All done - Show results
        if ($new_tsize > 0) {
            $ctr--;
            jrCore_form_modal_notice('update', "Total size of {$ctr} matched {$mod_name} image files BEFORE resizing: " . jrCore_format_size($old_tsize));
            jrCore_form_modal_notice('update', "Total size of {$ctr} matched {$mod_name} image files AFTER resizing: " . jrCore_format_size($new_tsize));
            $saved = $old_tsize - $new_tsize;
            if ($_post['dry_run'] == 'on') {
                jrCore_form_modal_notice('complete', "Test Run: A total of " . jrCore_format_size($saved) . " of disk space could be saved");
            }
            else {
                jrCore_form_modal_notice('complete', "Success: A total of " . jrCore_format_size($saved) . " of disk space has been saved");
            }
        }
        else {
            jrCore_form_modal_notice('complete', "There were no image files found to resize for {$mod_name}");
        }
    }
    else {
        jrCore_form_modal_notice('complete', "There were no image files found to resize for {$mod_name}");
    }
    jrCore_form_result('referrer');
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
}

/**
 * Display an image for a recycle bin item
 * @param $_post array Params from jrCore_parse_url();
 * @param $_user array User information
 * @param $_conf array Global config
 * @example http://www.site.com/image/rb_image/[recycle_bin_id]/jrGallery/gallery_image
 */
function view_jrImage_rb_image($_post, $_user, $_conf)
{
    jrUser_is_admin();

    $tbl = jrCore_db_table_name('jrCore', 'recycle');
    $req = "SELECT r_module AS module, r_item_id AS iid, r_profile_id AS pid, r_data FROM {$tbl} WHERE r_id = '{$_post['_1']}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        $_post['_3'] = 'icon';
        jrImage_display_default_image($_post, $_conf);
    }
    $_data = json_decode($_rt['r_data'], true);
    if (!$_data || !is_array($_data)) {
        $_post['_3'] = 'icon';
        jrImage_display_default_image($_post, $_conf);
    }

    $_im = array('image_time' => $_data['_updated']);
    foreach (array('name', 'size', 'type', 'extension', 'width', 'height') as $i) {
        if (isset($_data["{$_post['_3']}_{$i}"])) {
            $_im["image_{$i}"] = $_data["{$_post['_3']}_{$i}"];
        }
    }

    // Ensure master image is local for resize
    jrCore_db_close();
    $dir = jrCore_get_media_directory($_data['_profile_id'], FORCE_LOCAL);
    $nam = "rb_{$_post['_2']}_{$_data['_item_id']}_{$_post['_3']}.{$_data["{$_post['_3']}_extension"]}";
    jrCore_confirm_media_file_is_local($_rt['pid'], $nam, "{$dir}/{$nam}");
    if (!is_file("{$dir}/{$nam}")) {
        $_post['_3'] = 'icon';
        jrImage_display_default_image($_post, $_conf);
    }
    if (!jrImage_is_valid_image_file("{$dir}/{$nam}")) {
        $_post['_3'] = 'icon';
        jrImage_display_default_image($_post, $_conf);
    }
    // Have we already resized this image?

    $ext = jrCore_file_extension($nam);
    $img = "{$dir}/{$nam}";
    if (is_file("{$dir}/{$nam}.{$ext}")) {
        $img = "{$dir}/{$nam}.{$ext}";
    }
    else {
        $_tm = getimagesize($img);
        if ($_tm[0] > 512 || $_tm[1] > 512) {
            if ($new = jrImage_resize_image("{$dir}/{$nam}", "{$dir}/{$nam}.{$ext}", 512, 80)) {
                if (is_file($new)) {
                    $img = $new;
                }
            }

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
