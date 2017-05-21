<?php
/**
 * Jamroom Video module
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
// create_album
//------------------------------
function view_jrVideo_create_album($_post, $_user, $_conf)
{
    // Must be logged in to create a new video file
    jrUser_session_require_login();
    jrCore_check_ffmpeg_install();
    jrUser_check_quota_access('jrVideo');
    jrProfile_check_disk_usage();

    jrCore_page_banner(45);

    // Form init
    $_tmp = array(
        'submit_value' => 45,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    // Video Album
    $_tmp = array(
        'name'     => 'video_album',
        'label'    => 42,
        'help'     => 43,
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Video File
    $_tmp = array(
        'name'     => 'video_file',
        'label'    => 46,
        'help'     => 47,
        'text'     => 48,
        'type'     => 'video',
        'required' => true,
        'multiple' => true
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// create_album_save
//------------------------------
function view_jrVideo_create_album_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrVideo');

    $_files = jrCore_get_uploaded_media_files('jrVideo', 'video_file');
    if (!isset($_files) || !is_array($_files)) {
        jrCore_set_form_notice('error', 'You must upload some video files!');
        jrCore_form_result();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('jrVideo', 'create', $_post);

    // If we have been given a PRICE for the individual video items, we create a sample
    $sample = false;
    if (isset($_rt['video_file_item_price']) && strlen($_rt['video_file_item_price']) > 0) {
        $sample = true;
    }

    foreach ($_files as $n => $file_name) {

        // Grab meta data from this file
        $_tmp = array();

        // What fields from the meta data are we overriding?
        $_def = array(
            'video_album'      => 'no-album',
            'video_title'      => '',
            'video_file_track' => ($n + 1)
        );
        foreach ($_def as $k => $v) {
            if (isset($_rt[$k]) && strlen($_rt[$k]) > 0) {
                $_tmp[$k] = $_rt[$k];
            }
            else {
                $_tmp[$k] = $v;
            }
        }

        // Merge in meta data
        $_met = jrCore_get_media_file_metadata($file_name, 'video_file');
        if (isset($_met) && is_array($_met)) {
            $_tmp = array_merge($_met, $_tmp);
        }

        // Add in any additional custom fields that come in
        foreach ($_rt as $k => $v) {
            if (!isset($_tmp[$k])) {
                $_tmp[$k] = $v;
            }
        }

        // If we do not have a title, use the file name
        if (!isset($_tmp['video_title']) || strlen($_tmp['video_title']) === 0) {
            $tmp                 = trim(file_get_contents("{$file_name}.tmp"));
            $_tmp['video_title'] = substr($tmp, 0, strrpos($tmp, '.'));
            $_tmp['video_title'] = str_replace(array('-', '_'), ' ', $_tmp['video_title']);
        }

        // Add in our SEO URL names if we get them
        foreach (array('video_title', 'video_album') as $k) {
            if (isset($_tmp[$k])) {
                $_tmp["{$k}_url"] = jrCore_url_string($_tmp[$k]);
            }
        }

        // Cleanup any fields that are empty...
        foreach ($_tmp as $k => $v) {
            if (strlen($v) === 0) {
                unset($_tmp[$k]);
            }
        }

        // We don't want to show this video file in lists and on the site if
        // it is being converted - set our active flag to 0 if we're converting
        $_tmp['video_active'] = 'on';
        if (isset($_user['quota_jrVideo_video_conversions']) && $_user['quota_jrVideo_video_conversions'] == 'on') {
            $_tmp['video_active'] = 'off';
        }

        // $aid will be the INSERT_ID (_item_id) of the created item
        $aid = jrCore_db_create_item('jrVideo', $_tmp);
        if (!$aid) {
            jrCore_set_form_notice('error', 'unable to create new video file in DataStore!');
            jrCore_form_result();
        }

        // Now that we have our DataStore Item created, link up the file with it
        // We have to tell jrCore_save_media_file the file we want to link with this item,
        // so we pass in the FULL PATH $_file_name as arg #2 to jrCore_save_media_file
        jrCore_save_media_file('jrVideo', $file_name, $_user['user_active_profile_id'], $aid);

        // Lastly, check if video conversions are enabled.
        // If so, we need to add this item into the conversion queue
        if (isset($_user['quota_jrVideo_video_conversions']) && $_user['quota_jrVideo_video_conversions'] == 'on') {
            $_queue = array(
                'file_name'     => 'video_file',
                'quota_id'      => $_user['profile_quota_id'],
                'profile_id'    => $_user['user_active_profile_id'],
                'item_id'       => $aid,
                'screenshot'    => 1,
                'sample'        => $sample,
                'sample_length' => $_conf['jrVideo_sample_length'],
                'max_workers'   => (isset($_conf['jrVideo_conversion_worker_count'])) ? intval($_conf['jrVideo_conversion_worker_count']) : 1
            );
            jrCore_queue_create('jrVideo', 'video_conversions', $_queue);
        }

        // Add the FIRST VIDEO to our actions...
        if (!isset($action_saved)) {
            // Add to Actions...
            jrCore_run_module_function('jrAction_save', 'create_album', 'jrVideo', $aid);
            $action_saved = true;
        }
    }
    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/albums");
}

//------------------------------
// create
//------------------------------
function view_jrVideo_create($_post, $_user, $_conf)
{
    // Must be logged in to create a new video file
    jrUser_session_require_login();
    jrCore_check_ffmpeg_install();
    jrUser_check_quota_access('jrVideo');
    jrProfile_check_disk_usage();

    // Start our create form
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
    );
    $tmp = jrCore_page_banner_item_jumper('jrVideo', 'video_title', $_sr, 'create', 'update');
    jrCore_page_banner(22, $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 9,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    // Video Title
    $_tmp = array(
        'name'     => 'video_title',
        'label'    => 10,
        'help'     => 11,
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Video Album
    $_tmp = array(
        'name'     => 'video_album',
        'label'    => 31,
        'help'     => 32,
        'type'     => 'select_and_text',
        'validate' => 'printable',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Video File
    $_tmp = array(
        'name'     => 'video_file',
        'label'    => 14,
        'help'     => 15,
        'text'     => 29,
        'type'     => 'video',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrVideo_create_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_form_validate($_post);

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('jrVideo', 'create', $_post);

    // Add in our SEO URL names
    $_rt['video_title_url'] = jrCore_url_string($_rt['video_title']);
    if (isset($_rt['video_album'])) {
        $_rt['video_album_url'] = jrCore_url_string($_rt['video_album']);
    }

    // Get our uploaded media file - we're going to get the meta data from it
    $_fl = jrCore_get_uploaded_media_files('jrVideo', 'video_file');
    if (isset($_fl) && is_array($_fl) && isset($_fl[0])) {
        $_tmp = jrCore_get_media_file_metadata($_fl[0], 'video_file');
        if (isset($_tmp) && is_array($_tmp)) {
            if (!isset($_tmp['video_file_track']) || !jrCore_checktype($_tmp['video_file_track'], 'number_nz')) {
                $_tmp['video_file_track'] = 1;
            }
            $_rt = array_merge($_tmp, $_rt);
        }
        else {
            $_rt['video_file_track'] = 1;
        }
        unset($_tmp);
    }

    // We don't want to show this video file in lists and on the site if
    // it is being converted - set our active flag to 0 if we're converting
    $_rt['video_active'] = 'on';
    if (isset($_user['quota_jrVideo_video_conversions']) && $_user['quota_jrVideo_video_conversions'] == 'on') {
        $_rt['video_active'] = 'off';
    }

    // $aid will be the INSERT_ID (_item_id) of the created item
    $aid = jrCore_db_create_item('jrVideo', $_rt);
    if (!$aid) {
        jrCore_set_form_notice('error', 18);
        jrCore_form_result();
    }
    // Save any uploaded media files added in by our
    jrCore_save_all_media_files('jrVideo', 'create', $_user['user_active_profile_id'], $aid);

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'create', 'jrVideo', $aid);

    // If we have been given a PRICE for this item, we create a sample
    $sample = false;
    if (isset($_rt['video_file_item_price']) && strlen($_rt['video_file_item_price']) > 0) {
        $sample = true;
    }

    // Lastly, check if video conversions are enabled.
    // If so, we need to add this item into the conversion queue
    if (isset($_fl) && is_array($_fl) && isset($_fl[0])) {
        if (isset($_user['quota_jrVideo_video_conversions']) && $_user['quota_jrVideo_video_conversions'] == 'on') {
            $_queue = array(
                'file_name'     => 'video_file',
                'quota_id'      => $_user['profile_quota_id'],
                'profile_id'    => $_user['user_active_profile_id'],
                'item_id'       => $aid,
                'screenshot'    => 1,
                'sample'        => $sample,
                'sample_length' => $_conf['jrVideo_sample_length'],
                'max_workers'   => intval($_conf['jrVideo_conversion_worker_count'])
            );
            jrCore_queue_create('jrVideo', 'video_conversions', $_queue);
        }
    }

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$aid}/{$_rt['video_title_url']}");
}

//------------------------------
// update
//------------------------------
function view_jrVideo_update($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_check_ffmpeg_install();

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 20);
    }
    $_rt = jrCore_db_get_item('jrVideo', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', 21);
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Start output
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
    );
    $tmp = jrCore_page_banner_item_jumper('jrVideo', 'video_title', $_sr, 'create', 'update');
    jrCore_page_banner(23, $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 24,
        'cancel'       => jrCore_is_profile_referrer(),
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // id
    $_tmp = array(
        'name'     => 'id',
        'type'     => 'hidden',
        'value'    => $_post['id'],
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    // Video Title
    $_tmp = array(
        'name'     => 'video_title',
        'label'    => 10,
        'help'     => 11,
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Video Album
    $_tmp = array(
        'name'     => 'video_album',
        'label'    => 31,
        'help'     => 32,
        'type'     => 'select_and_text',
        'validate' => 'printable',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Video File
    $_tmp = array(
        'name'     => 'video_file',
        'label'    => 14,
        'help'     => 15,
        'text'     => 29,
        'type'     => 'video',
        'value'    => $_rt,
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Video Image
    $_tmp = array(
        'name'     => 'video_image',
        'label'    => 16,
        'help'     => 17,
        'text'     => 30,
        'type'     => 'image',
        'value'    => $_rt,
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrVideo_update_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();

    // Validate all incoming posted data
    jrCore_form_validate($_post);

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 20);
        jrCore_form_result();
    }

    // Get data
    $_rt = jrCore_db_get_item('jrVideo', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 20);
        jrCore_form_result();
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrVideo', 'update', $_post);

    // Add in our SEO URL names
    $_sv['video_title_url'] = jrCore_url_string($_sv['video_title']);
    $_sv['video_album_url'] = jrCore_url_string($_sv['video_album']);
    $_sv['video_genre_url'] = jrCore_url_string($_sv['video_genre']);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrVideo', $_post['id'], $_sv);

    // Save any uploaded media files added in by our
    jrCore_save_all_media_files('jrVideo', 'update', $_user['user_active_profile_id'], $_post['id']);

    // If we have been given a PRICE for this item, we create a sample
    $sample = false;
    if (isset($_sv['video_file_item_price']) && strlen($_sv['video_file_item_price']) > 0) {
        $sample = true;
    }

    // Lastly, check if video conversions are enabled.
    // If so, we need to add this item into the conversion queue
    if (jrCore_get_uploaded_media_files('jrVideo', 'video_file') && isset($_user['quota_jrVideo_video_conversions']) && $_user['quota_jrVideo_video_conversions'] == 'on') {
        $_queue = array(
            'file_name'     => 'video_file',
            'quota_id'      => $_user['profile_quota_id'],
            'profile_id'    => $_user['user_active_profile_id'],
            'item_id'       => $_post['id'],
            'sample'        => $sample,
            'sample_length' => $_conf['jrVideo_sample_length'],
            'max_workers'   => intval($_conf['jrVideo_conversion_worker_count'])
        );
        jrCore_queue_create('jrVideo', 'video_conversions', $_queue);
    }

    // See if we are adding or removing a price
    else {

        // If we are ADDING a price, we must create our sample
        $input_file = jrCore_get_media_file_path('jrVideo', 'video_file', $_rt);
        if ($sample && isset($_sv['video_file_item_price']) && strlen($_sv['video_file_item_price']) > 0 && !is_file("{$input_file}.sample.flv")) {
            // Create Samples (both FLV for desktop and M4V for mobile)
            $_queue = array(
                'file_name'     => 'video_file',
                'quota_id'      => $_user['profile_quota_id'],
                'profile_id'    => $_user['user_active_profile_id'],
                'item_id'       => $_post['id'],
                'sample'        => true,
                'sample_length' => $_conf['jrVideo_sample_length'],
                'max_workers'   => intval($_conf['jrVideo_conversion_worker_count'])
            );
            jrCore_queue_create('jrVideo', 'create_video_sample', $_queue);
        }
        // See if we are removing a price - delete sample
        elseif (isset($_rt['video_file_item_price']) && strlen($_rt['video_file_item_price']) > 0 && (!isset($_sv['video_file_item_price']) || strlen($_sv['video_file_item_price']) === 0)) {

            // We're removing a price from the item - delete samples (FLV)
            jrCore_delete_media_file($_user['user_active_profile_id'], "{$input_file}.sample.flv");

            // ... AND M4V
            $input_file = preg_replace("/\\.[^.\\s]{3,4}$/", "", $input_file) . '_mobile.m4v';
            jrCore_delete_media_file($_user['user_active_profile_id'], "{$input_file}.sample.m4v");
        }
    }

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'update', 'jrVideo', $_post['id']);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$_post['id']}/{$_sv['video_title_url']}");
}

//------------------------------
// delete
//------------------------------
function view_jrVideo_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_validate_location_url();

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 20);
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item('jrVideo', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 20);
        jrCore_form_result();
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Delete item and any associated files
    jrCore_db_delete_item('jrVideo', $_post['id']);
    jrCore_queue_delete_by_item_id('jrVideo', $_post['id']);
    jrProfile_reset_cache();
    jrCore_form_result('delete_referrer');
}

//------------------------------
// delete_album
//------------------------------
function view_jrVideo_delete_album($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrVideo');

    // Make sure we get a good id
    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        jrCore_set_form_notice('error', 21);
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item_by_key('jrVideo', 'video_album_url', $_post['_1']);
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 21);
        jrCore_form_result();
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Delete all items that match
    $_id = jrCore_db_get_multiple_items_by_key('jrVideo', 'video_album_url', $_post['_1'], true);
    if (isset($_id) && is_array($_id)) {
        jrCore_db_delete_multiple_items('jrVideo', $_id);
    }

    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}");
}

//----------------------------------
// update the order of an album.
//----------------------------------
function view_jrVideo_order_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['video_file_track']) || !is_array($_post['video_file_track'])) {
        return jrCore_json_response(array('error', 'invalid video_file_track array received'));
    }

    // Get our video files that are being re-ordered and make sure
    // the calling user has access to them
    if (!jrUser_is_admin()) {
        $_rt = jrCore_db_get_multiple_items('jrVideo', $_post['video_file_track']);
        if (!isset($_rt) || !is_array($_rt)) {
            return jrCore_json_response(array('error', 'unable to retrieve video entries from database'));
        }
        foreach ($_rt as $_v) {
            if (!jrUser_can_edit_item($_v)) {
                return jrCore_json_response(array('error', 'permission denied'));
            }
        }
    }
    // Looks good - set album order
    $_up = array();
    foreach ($_post['video_file_track'] as $ord => $vid) {
        $_up[$vid] = array('video_file_track' => $ord);
    }
    jrCore_db_update_multiple_items('jrVideo', $_up);
    jrProfile_reset_cache();
    return jrCore_json_response(array('success', 'video_file_track successfully updated'));
}

//---------------------------------------------
// Video Widget Config Body (loaded via ajax)
//---------------------------------------------
function view_jrVideo_widget_config_body($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }

    $ss      = array();
    $default = true;

    // specific ids
    if (isset($_post['ids']) && $_post['ids'] !== "false" && $_post['ids'] !== "undefined" && $_post['ids'] !== "") {
        $ss[]    = "_item_id IN {$_post['ids']}";
        $default = false;
    }
    // search string
    if (isset($_post['sstr']) && $_post['sstr'] !== "false" && $_post['sstr'] !== "undefined" && $_post['sstr'] !== "") {
        $ss[]    = "video_% LIKE %{$_post['sstr']}%";
        $default = false;
    }
    // profile
    if (isset($_post['profile_url']) && $_post['profile_url'] !== "false" && $_post['profile_url'] !== "undefined" && $_post['profile_url'] !== "") {
        $ss[]    = "profile_url = {$_post['profile_url']}";
        $default = false;
    }
    // album
    if (isset($_post['album_url']) && $_post['album_url'] !== "false" && $_post['album_url'] !== "undefined" && $_post['album_url'] !== "") {
        $album_url = jrCore_url_string($_post['album_url']);
        $ss[]      = "video_album_url = $album_url";
        $default   = false;
    }
    // default list of items
    if ($default) {
        $ss[] = "_profile_id = {$_user['user_active_profile_id']}";
    }

    // Create search params from $_post
    $_sp = array(
        'search'              => $ss,
        'pagebreak'           => 8,
        'page'                => $_post['p'],
        'exclude_jrUser_keys' => true
    );

    $_rt = jrCore_db_search_items('jrVideo', $_sp);
    return jrCore_parse_template('widget_config_body.tpl', $_rt, 'jrVideo');
}

//---------------------------------------------------------
// Video Embed (used for twitter cards etc, just a player)
//---------------------------------------------------------
function view_jrVideo_embed($_post, $_user, $_conf)
{
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('notice', 'video with that id could not be located');
    }

    $_rt = jrCore_db_get_item('jrVideo', $_post['_1']);

    if (!$_rt) {
        jrCore_notice_page('notice', 'video with that id could not be found in the datastore');
    }

    $_rep = array(
        'item' => $_rt
    );
    $html = jrCore_parse_template('item_embed.tpl', $_rep, 'jrVideo');

    jrCore_page_set_meta_header_only();
    jrCore_page_custom($html);
    jrCore_page_display();

}


