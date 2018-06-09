<?php
/**
 * Jamroom Image Galleries module
 *
 * copyright 2017 The Jamroom Network
 *
 * This Jamroom file is LICENSED SOFTWARE, and cannot be redistributed.
 *
 * This Source Code is subject to the terms of the Jamroom Network
 * Commercial License -  please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
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
// get_signature (hires editing)
//------------------------------
function view_jrGallery_get_signature($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    // signature = md5(api_key+api_secret+timestamp+salt)
    $_rt['timestamp'] = time();
    $_rt['salt']      = md5(microtime());
    $_rt['signature'] = sha1($_conf['jrGallery_api_key'] . $_conf['jrGallery_aviary_key'] . $_rt['timestamp'] . $_rt['salt']);
    jrCore_json_response($_rt);
}

//------------------------------
// original_image
//------------------------------
function view_jrGallery_original_image($_post, $_user, $_conf)
{
    // Valid item id
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('error', "Invalid Item ID");
    }
    // Check for valid edit key
    if (!isset($_post['edit_key']) || !jrCore_checktype($_post['edit_key'], 'number_nz')) {
        jrCore_notice_page('error', "Invalid Edit Key");
    }
    if (!$key = jrCore_get_temp_value('jrGallery', "image_edit_key_{$_post['edit_key']}")) {
        jrCore_notice_page('error', "Invalid Edit Key - not found");
    }
    // Get image
    $_rt = jrCore_db_get_item('jrGallery', $_post['_1']);
    if (!$_rt) {
        jrCore_notice_page('error', 'invalid image - image data not found');
    }
    // Check that file exists
    $nam = "jrGallery_{$_post['_1']}_gallery_image.{$_rt['gallery_image_extension']}";
    // Make sure file is actually there...
    if (!jrCore_media_file_exists($_rt['_profile_id'], $nam)) {
        jrCore_notice_page('error', 'invalid image - image file not found');
    }
    // Get right mime type - sometimes it can be wrong when PHP is wrong
    switch ($_rt['gallery_image_extension']) {
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
        default:
            header("Content-type: " . $_rt['gallery_image_type']);
            break;
    }
    header('Content-Disposition: inline; filename="' . $_rt['gallery_image_name'] . '"');
    jrCore_media_file_download($_rt['_profile_id'], $nam, $_rt['gallery_image_name']);
    session_write_close();
    exit();
}

//------------------------------
// slider_images
//------------------------------
function view_jrGallery_slider_images($_post, $_user, $_conf)
{
    if (!isset($_post['pid']) || !jrCore_checktype($_post['pid'], 'number_nz')) {
        $_rs = array('error' => 'invalid profile_id');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['gallery']) || strlen($_post['gallery']) === 0) {
        $_rs = array('error' => 'invalid gallery');
        jrCore_json_response($_rs);
    }
    $page = 1;
    if (isset($_post['page']) && jrCore_checktype($_post['page'], 'number_nz')) {
        $page = (int) $_post['page'];
    }
    $pagebreak = 12;
    if (isset($_post['pagebreak']) && jrCore_checktype($_post['pagebreak'], 'number_nz')) {
        $pagebreak = (int) $_post['pagebreak'];
    }
    $_sc = array(
        'search'                       => array(
            "_profile_id = {$_post['pid']}",
            "gallery_title_url = {$_post['gallery']}"
        ),
        'order_by'                     => array('gallery_order' => 'numerical_asc'),
        'exclude_jrUser_keys'          => true,
        'exclude_jrProfile_quota_keys' => true,
        'pagebreak'                    => $pagebreak,
        'page'                         => $page
    );
    $_rt = jrCore_db_search_items('jrGallery', $_sc);
    if (!$_rt || !is_array($_rt) || !is_array($_rt['_items'])) {
        $_rs = array('error' => 'no gallery images found');
        jrCore_json_response($_rs);
    }
    $key = md5("{$_post['pid']}-{$_post['gallery']}");
    if (!isset($_SESSION['jrGallery_active_gallery'])) {
        $_SESSION['jrGallery_active_gallery'] = $key;
    }
    elseif ($_SESSION['jrGallery_active_gallery'] != $key) {
        // We've changed galleries - reset
        unset($_SESSION['jrGallery_page_num']);
        $_SESSION['jrGallery_active_gallery'] = $key;
    }
    $_SESSION['jrGallery_page_num'] = $page;
    return jrCore_parse_template('item_slider.tpl', $_rt, 'jrGallery');
}

//------------------------------
// create
//------------------------------
function view_jrGallery_create($_post, $_user, $_conf)
{
    // Must be logged in to create a new gallery file
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGallery');
    jrProfile_check_disk_usage();

    // Start our create form
    jrCore_page_banner(2);

    // Form init
    $_tmp = array(
        'submit_value' => 2,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    // check to see if we have a gallery_title_url that we are adding to
    $title = '';
    if (isset($_post['_1']) && jrCore_checktype($_post['_1'], 'url_name')) {
        // retrieve the title of this gallery that new images are being added to
        $_rt = jrCore_db_get_item_by_key('jrGallery', 'gallery_title_url', $_post['_1']);
        if ($_rt && is_array($_rt)) {
            $title = $_rt['gallery_title'];
        }
    }

    // Gallery Title
    $_tmp = array(
        'name'       => 'gallery_title',
        'label'      => 3,
        'help'       => 4,
        'type'       => 'text',
        'validate'   => 'not_empty',
        'value'      => $title,
        'required'   => false,
        'unique'     => true,
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);

    // Gallery Images
    $_tmp = array(
        'name'     => 'gallery_image',
        'label'    => 1,
        'help'     => 5,
        'text'     => 40,
        'type'     => 'image',
        'multiple' => true,
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrGallery_create_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGallery');
    jrCore_form_validate($_post);

    // For our Gallery Images, we are going to create a UNIQUE DataStore entry
    // for each file that is uploaded
    $_files = jrCore_get_uploaded_media_files('jrGallery', 'gallery_image');
    if (!$_files || !is_array($_files)) {
        jrCore_set_form_notice('error', 6);
        jrCore_form_result();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('jrGallery', 'create', $_post);

    // If we do NOT get a gallery title, we default to profile images...
    if (!isset($_rt['gallery_title']) || strlen($_rt['gallery_title']) === 0) {
        $_ln                  = jrUser_load_lang_strings();
        $_rt['gallery_title'] = $_ln['jrGallery'][10];
    }

    // Add in our Gallery Title (for SEO URL use)
    $_rt['gallery_title_url'] = jrCore_url_string($_rt['gallery_title']);

    $action_saved = 0;
    foreach ($_files as $k => $file_name) {
        $_rt['gallery_order'] = ($k + 1);
        $aid                  = jrCore_db_create_item('jrGallery', $_rt);
        if (!$aid) {
            jrCore_set_form_notice('error', 7);
            jrCore_form_result();
        }
        // Now that we have our DataStore Item created, link up the file with it
        // We have to tell jrCore_save_media_file the file we want to link with this item,
        // so we pass in the FULL PATH $_file_name as arg #2 to jrCore_save_media_file
        jrCore_save_media_file('jrGallery', $file_name, $_user['user_active_profile_id'], $aid);

        // Add our FIRST IMAGE to our actions...
        if ($action_saved === 0) {
            // Add to Actions...
            jrCore_run_module_function('jrAction_save', 'create', 'jrGallery', $aid);
            $action_saved = $aid;
        }
    }
    jrCore_form_delete_session();

    // Is this gallery pending?
    if (isset($_user['quota_jrGallery_pending']) && $_user['quota_jrGallery_pending'] > 0) {
        // This gallery is PENDING - redirect to UPDATE so they can see it
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/update/id={$action_saved}");
    }

    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$_rt['gallery_title_url']}/all");
}

//------------------------------
// update
//------------------------------
function view_jrGallery_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGallery');

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 14);
    }
    $_it = jrCore_db_get_item('jrGallery', $_post['id']);
    if (!$_it) {
        jrCore_notice_page('error', 14);
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_it)) {
        jrUser_not_authorized();
    }

    jrCore_page_banner($_it['gallery_title']);

    // Form init
    $_tmp = array(
        'submit_value' => 16,
        'cancel'       => jrCore_is_profile_referrer(),
        'values'       => $_it
    );
    jrCore_form_create($_tmp);

    // Gallery Title
    $_tmp = array(
        'name'  => 'gallery_existing_title',
        'type'  => 'hidden',
        'value' => $_it['gallery_title_url']
    );
    jrCore_form_field_create($_tmp);

    // Gallery Title
    $_tmp = array(
        'name'     => 'gallery_title',
        'label'    => 3,
        'help'     => 4,
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Get existing images
    $_rt = array(
        'search'   => array(
            "_profile_id = {$_user['user_active_profile_id']}",
            "gallery_title_url = {$_it['gallery_title_url']}"
        ),
        'order_by' => array('gallery_order' => 'numerical_asc'),
        "limit"    => 500
    );
    if (jrUser_is_admin() || jrProfile_is_profile_owner($_it['_profile_id'])) {
        $_rt['ignore_pending'] = true;
    }
    $_rt = jrCore_db_search_items('jrGallery', $_rt);

    $htm = jrCore_parse_template('gallery_update.tpl', $_rt, 'jrGallery');
    jrCore_page_custom($htm, 10);

    // Gallery Images
    $_tmp = array(
        'name'     => 'gallery_image',
        'label'    => 62,
        'help'     => 5,
        'text'     => 40,
        'type'     => 'image',
        'multiple' => true,
        'required' => false,
        'value'    => false,
        'no_image' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrGallery_update_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGallery');
    jrCore_form_validate($_post);

    // For our Gallery Images, we are going to create a UNIQUE DataStore entry
    // for each file that is uploaded
    $existing_title = $_post['gallery_existing_title'];
    unset($_post['gallery_existing_title']);

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_up                      = jrCore_form_get_save_data('jrGallery', 'update', $_post);
    $_up['gallery_title_url'] = jrCore_url_string($_post['gallery_title']);

    // Any _new_ uploaded files
    $_files = jrCore_get_uploaded_media_files('jrGallery', 'gallery_image');

    // Update all existing gallery entries with new title
    $ord = 0;
    $cnt = count($_files);
    $_rt = array(
        'search'         => array(
            "gallery_title_url = {$existing_title}",
            "_profile_id = {$_user['user_active_profile_id']}",
        ),
        'return_keys'    => array('_item_id', '_updated', 'gallery_order'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'limit'          => 500
    );
    $_rt = jrCore_db_search_items('jrGallery', $_rt);
    if (!$_rt || !is_array($_rt) || !isset($_rt['_items'])) {
        jrCore_set_form_notice('error', 12);
        jrCore_form_result();
    }
    $tot = count($_rt['_items']);
    $_dt = array();
    $_cr = array();
    foreach ($_rt['_items'] as $item) {
        // Setup new gallery order
        if (!isset($item['gallery_order'])) {
            // Old one without order - fall to end
            $ord++;
            $_up['gallery_order'] = ($tot + $cnt + $ord);
        }
        else {
            $_up['gallery_order'] = ($item['gallery_order'] + $cnt);
        }
        $_dt["{$item['_item_id']}"] = $_up;
        $_cr["{$item['_item_id']}"] = array('_updated' => $item['_updated']);
    }
    jrCore_db_update_multiple_items('jrGallery', $_dt, $_cr);

    // Get new uploaded files
    if ($_files && is_array($_files)) {

        $_up['gallery_order'] = 0;
        foreach ($_files as $file_name) {
            // $aid will be the INSERT_ID (_item_id) of the created item
            $_up['gallery_order']++;
            $aid = jrCore_db_create_item('jrGallery', $_up);
            if (!$aid) {
                jrCore_set_form_notice('error', 7);
                jrCore_form_result();
            }
            // Now that we have our DataStore Item created, link up the file with it
            // We have to tell jrCore_save_media_file the file we want to link with this item,
            // so we pass in the FULL PATH of $_file_name as arg #2 to jrCore_save_media_file
            jrCore_save_media_file('jrGallery', $file_name, $_user['user_active_profile_id'], $aid);

            // Add our FIRST IMAGE to our actions...
            if (!isset($action_saved)) {
                // Add to Actions...
                jrCore_run_module_function('jrAction_save', 'update', 'jrGallery', $aid);
                $action_saved = true;
            }
        }
    }
    jrCore_form_delete_session();

    // Is this gallery pending?
    if (!jrUser_is_admin() && isset($_user['quota_jrGallery_pending']) && $_user['quota_jrGallery_pending'] > 1) {
        // This gallery is PENDING - redirect to UPDATE so they can see it
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/update/id={$_rt['_items'][0]['_item_id']}");
    }

    jrProfile_reset_cache($_user['user_active_profile_id'], 'jrGallery');
    jrUser_reset_cache($_user['_user_id'], 'jrGallery');
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$_up['gallery_title_url']}/all");
}

//------------------------------
// delete_save
//------------------------------
function view_jrGallery_delete_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrGallery');
    // We should get our gallery_title_url as $_post['_2'] ...
    if (!isset($_post['_2']) || strlen($_post['_2']) === 0) {
        jrCore_notice_page('error', 9);
    }
    // Get all gallery images that are part of this collection
    $search  = urldecode($_post['_2']);
    $search  = urlencode($search);
    $_params = array(
        'search'        => array(
            "_profile_id = {$_user['user_active_profile_id']}",
            "gallery_title_url = {$search}"
        ),
        "order_by"      => array(
            '_item_id' => 'DESC'
        ),
        "limit"         => 500,
        "skip_triggers" => true
    );
    $_rt     = jrCore_db_search_items('jrGallery', $_params);

    if (!jrUser_can_edit_item($_rt['_items'][0])) {
        jrUser_not_authorized();
    }
    // Delete each image
    foreach ($_rt['_items'] as $_g) {
        jrCore_db_delete_item('jrGallery', $_g['_item_id']);
    }
    jrProfile_reset_cache($_user['user_active_profile_id'], 'jrGallery');
    jrUser_reset_cache($_user['_user_id'], 'jrGallery');
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}");
}

//------------------------------
// detail
//------------------------------
function view_jrGallery_detail($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGallery');
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 14);
        jrCore_form_result('referrer');
    }
    $_rt = jrCore_db_get_item('jrGallery', $_post['id']);
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Start our create form
    // Add in a link back to the full gallery update page

    $_lang = jrUser_load_lang_strings();
    $murl  = jrCore_get_module_url('jrGallery');
    $icon  = jrCore_get_icon_html('gear');
    $lnk   = '<a href="' . $_conf['jrCore_base_url'] . '/' . $murl . '/update/id=' . $_post['id'] . '" title="' . $_lang['jrGallery'][11] . '">' . $icon . '</a>';
    jrCore_page_banner(15, $lnk);

    $canc = jrCore_is_profile_referrer("{$_conf['jrCore_base_url']}/{$_post['module_url']}/update/{$_rt['gallery_title_url']}");
    if (strpos(jrCore_get_local_referrer(), '/update/id=')) {
        $canc = 'referrer';
    }

    // Form init
    $_tmp = array(
        'submit_value' => 16,
        'cancel'       => $canc,
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // Gallery ID
    $_tmp = array(
        'name'     => 'id',
        'type'     => 'hidden',
        'validate' => 'number_nz',
        'value'    => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    $no_image = false;
    if (isset($_conf['jrGallery_aviary_key']) && strlen($_conf['jrGallery_aviary_key']) > 0 && (!isset($_user['quota_jrGallery_image_editor']) || $_user['quota_jrGallery_image_editor'] != 'off')) {
        // See if we using HI RES IMAGES
        if (isset($_conf['jrGallery_original']) && $_conf['jrGallery_original'] == 'on' && isset($_conf['jrGallery_api_key']) && strlen($_conf['jrGallery_api_key']) > 0) {
            // signature = md5(api_key+api_secret+timestamp+salt)
            $_rt['timestamp'] = time();
            $_rt['salt']      = md5(microtime());
            $_rt['signature'] = sha1($_conf['jrGallery_api_key'] . $_conf['jrGallery_aviary_key'] . $_rt['timestamp'] . $_rt['salt']);
        }
        $htm = jrCore_parse_template('gallery_manipulate.tpl', $_rt, 'jrGallery');
        jrCore_page_custom($htm, $_lang['jrGallery'][41]);
        $no_image = true;
    }

    // New Image (replace existing)
    $_tmp = array(
        'name'     => 'gallery_image',
        'label'    => 43,
        'help'     => 42,
        'text'     => 60,
        'type'     => 'image',
        'size'     => 'xlarge',
        'value'    => $_rt,
        'required' => false,
        'multiple' => false
    );
    if ($no_image) {
        $_tmp['no_image'] = true;
    }
    jrCore_form_field_create($_tmp);

    // Let's get other galleries this profile has created so we can allow the
    // image to be moved to a new gallery if they want
    // Gallery Title
    $_tmp = array(
        'name'     => 'gallery_title',
        'label'    => 25,
        'help'     => 36,
        'type'     => 'select_and_text',
        'options'  => 'jrGallery_get_gallery_titles',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Gallery Image Title
    $_tmp = array(
        'name'     => 'gallery_image_title',
        'label'    => 46,
        'help'     => 47,
        'type'     => 'text',
        'validate' => 'printable',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Gallery Caption
    $_tmp = array(
        'name'     => 'gallery_caption',
        'label'    => 17,
        'help'     => 18,
        'type'     => 'textarea',
        'validate' => 'printable',
        'required' => false
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// detail_save
//------------------------------
function view_jrGallery_detail_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGallery');
    jrCore_form_validate($_post);

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 14);
        jrCore_form_result();
    }
    // Get data
    $_rt = jrCore_db_get_item('jrGallery', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 14);
        jrCore_form_result();
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv                      = jrCore_form_get_save_data('jrGallery', 'detail', $_post);
    $_sv['gallery_title_url'] = jrCore_url_string($_sv['gallery_title']);
    if (isset($_sv['gallery_image_title']) && strlen($_sv['gallery_image_title']) > 0) {
        $_sv['gallery_image_title_url'] = jrCore_url_string($_sv['gallery_image_title']);
    }
    else {
        unset($_sv['gallery_image_title']);
    }

    // get the just edited remote image file from http://aviary.com if it has been edited using the aviary image editor.
    if (isset($_post['gallery_alt_img']) && jrCore_file_extension($_post['gallery_alt_img']) === 'png') {

        $ftemp = file_get_contents($_post['gallery_alt_img']);
        $fname = 'jrGallery_' . $_post['id'] . '_gallery_image.png';
        if (!jrCore_write_media_file($_rt['_profile_id'], $fname, $ftemp)) {
            jrCore_logger('CRI', "error saving media file: {$_rt['_profile_id']}/{$fname}");
        }
        else {
            // Delete the OLD image file
            if ($_rt['gallery_image_extension'] != 'png') {
                $fname = 'jrGallery_' . $_post['id'] . '_gallery_image.' . $_rt['gallery_image_extension'];
                jrCore_delete_media_file($_rt['_profile_id'], $fname);
            }

            // Next, we need to update the datastore entry with the info from the file
            $cdr = jrCore_get_module_cache_dir('jrGallery');
            $fil = "{$cdr}/{$fname}";
            jrCore_write_to_file($fil, $ftemp);

            // Save Data
            $_tmp = false;
            if ($_rt['gallery_image_extension'] == 'jpg' || $_rt['gallery_image_extension'] == 'jpeg' || $_rt['gallery_image_extension'] == 'jpe' || $_rt['gallery_image_extension'] == 'jfif' || $_rt['gallery_image_extension'] == 'jif') {
                // We were a JPG image, so make sure we go back to JPG
                $src = imagecreatefrompng($fil);
                if ($src) {
                    $new = jrCore_get_media_file_path('jrGallery', 'gallery_image', $_rt);
                    imagejpeg($src, $new, 100);
                    imagedestroy($src);
                    $_tmp                           = getimagesize($new);
                    $_sv["gallery_image_time"]      = 'UNIX_TIMESTAMP()';
                    $_sv["gallery_image_size"]      = filesize($new);
                    $_sv["gallery_image_type"]      = jrCore_mime_type($new);
                    $_sv["gallery_image_extension"] = $_rt['gallery_image_extension'];
                    $_sv["gallery_image_width"]     = (int) $_tmp[0];
                    $_sv["gallery_image_height"]    = (int) $_tmp[1];

                    // Cleanup
                    $fname = 'jrGallery_' . $_post['id'] . '_gallery_image.png';
                    jrCore_delete_media_file($_rt['_profile_id'], $fname);
                }
            }
            if (!$_tmp) {
                $_tmp                           = getimagesize($fil);
                $_sv["gallery_image_name"]      = str_replace('.png', '', $_rt['gallery_image_name']) . '.png';
                $_sv["gallery_image_time"]      = 'UNIX_TIMESTAMP()';
                $_sv["gallery_image_size"]      = filesize($fil);
                $_sv["gallery_image_type"]      = jrCore_mime_type($fil);
                $_sv["gallery_image_extension"] = 'png';
                $_sv["gallery_image_width"]     = (int) $_tmp[0];
                $_sv["gallery_image_height"]    = (int) $_tmp[1];
            }
            unlink($fil);
        }
    }

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrGallery', $_post['id'], $_sv);

    // Save any NEW gallery image (overwriting existing)
    jrCore_save_all_media_files('jrGallery', 'detail', $_user['user_active_profile_id'], $_post['id']);

    jrCore_form_delete_session();
    jrProfile_reset_cache($_user['user_active_profile_id'], 'jrGallery');
    jrUser_reset_cache($_user['_user_id'], 'jrGallery');

    $_rt = array_merge($_rt, $_sv);
    $url = jrGallery_get_gallery_image_url($_rt);
    jrCore_form_result($url);
}

//------------------------------
// delete_image
//------------------------------
function view_jrGallery_delete_image($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();

    jrUser_check_quota_access('jrGallery');
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 14);
        jrCore_form_result('referrer');
    }
    $_rt = jrCore_db_get_item('jrGallery', $_post['id']);
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    jrCore_db_delete_item('jrGallery', $_post['id']);

    // Reset caches
    jrProfile_reset_cache($_user['user_active_profile_id'], 'jrGallery');
    jrUser_reset_cache($_user['_user_id'], 'jrGallery');

    // See if we have images left in the gallery
    if (isset($_rt['gallery_title_url']) && strlen($_rt['gallery_title_url']) > 0) {
        $_sc = array(
            'search'         => array(
                "_profile_id = {$_user['user_active_profile_id']}",
                "gallery_title_url = {$_rt['gallery_title_url']}"
            ),
            'skip_triggers'  => true,
            'ignore_pending' => true,
            'privacy_check'  => false,
            'limit'          => 1
        );
        $_ex = jrCore_db_search_items('jrGallery', $_sc);
        if ($_ex && isset($_ex['_items']) && is_array($_ex['_items'])) {
            // We still have more gallery images
            $url = jrCore_get_local_referrer();
            if (strpos($url, "/{$_rt['_item_id']}/")) {
                // Deleted from detail
                jrCore_form_result("{$_conf['jrCore_base_url']}/{$_rt['profile_url']}/{$_post['module_url']}/{$_rt['gallery_title_url']}/all");
            }
            elseif (strpos($url, "/{$_rt['profile_url']}/{$_post['module_url']}/{$_rt['gallery_title_url']}")) {
                // Deleted from profile
                jrCore_form_result($url);
            }
            jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/update/id={$_ex['_items'][0]['_item_id']}");
        }
    }
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}");
}

//------------------------------
// delete_image_ajax
//------------------------------
function view_jrGallery_delete_image_ajax($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();

    jrUser_check_quota_access('jrGallery');
    $_lang = jrUser_load_lang_strings();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_json_response(array('OK' => 0, 'error' => $_lang['jrGallery'][14]));
    }
    $_rt = jrCore_db_get_item('jrGallery', $_post['id']);
    if (!jrUser_can_edit_item($_rt)) {
        jrCore_json_response(array('OK' => 0, 'error' => 'not authorized'));
    }
    jrCore_db_delete_item('jrGallery', $_post['id']);
    jrProfile_reset_cache();
    jrUser_reset_cache($_user['_user_id'], 'jrGallery');

    // success
    jrCore_json_response(array('OK' => 1));
}

//----------------------------------
// update the order of an gallery
//----------------------------------
function view_jrGallery_order_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['gallery_order']) || !is_array($_post['gallery_order'])) {
        return jrCore_json_response(array('error', 'invalid gallery_order array received'));
    }

    // Get our gallery files that are being re-ordered and make sure
    // the calling user has access to them
    if (!jrUser_is_admin()) {
        $_rt = jrCore_db_get_multiple_items('jrGallery', $_post['gallery_order']);
        if (!$_rt || !is_array($_rt)) {
            return jrCore_json_response(array('error', 'unable to retrieve audio entries from database'));
        }
        foreach ($_rt as $_v) {
            if (!jrUser_can_edit_item($_v)) {
                return jrCore_json_response(array('error', 'permission denied'));
            }
        }
    }
    // Looks good - set album order
    $_up = array();
    foreach ($_post['gallery_order'] as $ord => $iid) {
        $iid       = (int) $iid;
        $_up[$iid] = array('gallery_order' => intval($ord));
    }
    if (count($_up) > 0) {
        jrCore_db_update_multiple_items('jrGallery', $_up);
    }
    jrProfile_reset_cache($_user['user_active_profile_id'], 'jrGallery');
    jrUser_reset_cache($_user['_user_id'], 'jrGallery');
    return jrCore_json_response(array('success', 'gallery_order successfully updated'));
}

//----------------------------------
// parse a given template
// $_post['_1'] - template
// $_post['_2'] - _item_id
// $_post['_3'] - gallery_title_url
//----------------------------------
function view_jrGallery_parse($_post, $_user, $_conf)
{
    if (isset($_post['_2']) && jrCore_checktype($_post['_2'], 'number_nz')) {
        $_tmp = array(
            'item' => jrCore_db_get_item('jrGallery', $_post['_2'])
        );
    }
    elseif (isset($_post['_3']) && strlen($_post['_3']) > 0) {
        $_s   = array(
            "search" => array(
                "gallery_title_url = {$_post['_3']}"
            ),
            "limit"  => 100
        );
        $_tmp = jrCore_db_search_items('jrGallery', $_s);
    }
    else {
        return 'invalid parameters received';
    }
    return jrCore_parse_template("{$_post['_1']}.tpl", $_tmp, 'jrGallery');
}

//---------------------------------------------
// Gallery Widget Config Body
//---------------------------------------------
function view_jrGallery_widget_config_body($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $ss = array();
    // specific ids
    if (isset($_post['ids']) && $_post['ids'] !== "false" && $_post['ids'] !== "undefined" && $_post['ids'] !== "") {
        $ss[] = "_item_id in {$_post['ids']}";
    }
    // search string
    if (isset($_post['sstr']) && $_post['sstr'] !== "false" && $_post['sstr'] !== "undefined" && $_post['sstr'] !== "") {
        if (strpos($_post['sstr'], ':')) {
            list($k, $v) = explode(':', $_post['sstr']);
            $ss[] = "{$k} = {$v}";
        }
        else {
            $ss[] = "gallery_% LIKE %{$_post['sstr']}%";
        }
    }
    $_sp = array(
        'search'                       => $ss,
        'pagebreak'                    => 8,
        'page'                         => $_post['p'],
        'exclude_jrUser_keys'          => true,
        'exclude_jrProfile_quota_keys' => true,
        'order_by'                     => array('_created' => 'numerical_desc'),
    );
    $_rt = jrCore_db_search_items('jrGallery', $_sp);
    return jrCore_parse_template('widget_config_body.tpl', $_rt, 'jrGallery');
}

//------------------------------
// image_title_save
//------------------------------
function view_jrGallery_image_title_save($_post, $_user, $_conf)
{

    jrCore_validate_location_url();
    $_ln = jrUser_load_lang_strings();

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        $_rs = array(
            'OK'    => 0,
            'error' => $_ln['jrGallery'][54] // 'No image id received, so cant update database'
        );
        jrCore_json_response($_rs);
    }

    // Get data
    $_rt = jrCore_db_get_item('jrGallery', $_post['id']);
    if (!$_rt || !is_array($_rt)) {
        // Item does not exist....
        $_rs = array(
            'OK'    => 0,
            'error' => $_ln['jrGallery'][55] // 'item not found in datastore'
        );
        jrCore_json_response($_rs);
    }

    if (!jrUser_can_edit_item($_rt)) {
        $_rs = array(
            'OK' => 0,
            'error', $_ln['jrGallery'][56] // 'permission denied'
        );
        jrCore_json_response($_rs);
    }

    // Make sure we get a good URL
    if (isset($_post['gallery_image_title']) && strlen($_post['gallery_image_title']) > 0) {
        if (!jrCore_checktype($_post['gallery_image_title'], 'printable')) {
            $_rs = array(
                'OK'    => 0,
                'error' => $_ln['jrGallery'][57] // 'not a valid title'
            );
            jrCore_json_response($_rs);
        }
        $_up = array(
            'gallery_image_title'     => $_post['gallery_image_title'],
            'gallery_image_title_url' => jrCore_url_string($_post['gallery_image_title']),
        );
        $_cr = array(
            '_updated' => $_rt['_updated']
        );
        jrCore_db_update_item('jrGallery', $_post['id'], $_up, $_cr);
    }
    else {
        jrCore_db_delete_item_key('jrGallery', $_post['id'], 'gallery_image_title');
        jrCore_db_delete_item_key('jrGallery', $_post['id'], 'gallery_image_title_url');
    }

    $_rt = jrCore_db_get_item('jrGallery', $_post['id']);
    if (isset($_rt['gallery_image_title'])) {
        $title = $_rt['gallery_image_title'];
    }
    else {
        $title = '';
    }
    $_rs = array('OK' => 1, 'gallery_image_title' => $title);
    jrCore_json_response($_rs);
}
