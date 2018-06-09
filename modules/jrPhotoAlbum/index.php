<?php
/**
 * Jamroom Photo Albums module
 *
 * copyright 2018 The Jamroom Network
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
// add to photo album via javascript
//------------------------------
function view_jrPhotoAlbum_add($_post, $_user, $_conf)
{
    jrCore_validate_location_url();
    // Check for login
    if (isset($_conf['jrPhotoAlbum_require_login']) && $_conf['jrPhotoAlbum_require_login'] == 'on') {
        if (!jrUser_is_logged_in()) {
            $url = jrCore_get_module_url('jrUser');
            jrCore_notice_page('error', 1, "{$_conf['jrCore_base_url']}/{$url}/login", 2, true, false);
        }
    }
    if (jrUser_is_logged_in()) {
        jrUser_check_quota_access('jrPhotoAlbum');
    }

    // Make sure we get a good id
    // /photoalbum/add/jrAudio/1
    if (!isset($_post['_2']) || !jrCore_checktype($_post['_2'], 'number_nz')) {
        jrCore_notice_page('error', "invalid item_id - please try again", false, false, true, false);
    }

    // show all the photo albums belonging to this user.
    if (jrUser_is_logged_in()) {

        // Check for pagebreak
        $p = 1;
        if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
            $p = (int) $_post['p'];
        }
        $_sp                    = array(
            'search'                       => array(
                "_profile_id = " . jrUser_get_profile_home_key('_profile_id')
            ),
            'order_by'                     => array('photoalbum_display_order' => 'numerical_asc'),
            'exclude_jrProfile_quota_keys' => true,
            'pagebreak'                    => 6,
            'page'                         => $p
        );
        $_rep                   = jrCore_db_search_items('jrPhotoAlbum', $_sp);
        $_rep['item_id']        = $_post['_2'];
        $_rep['photoalbum_for'] = $_post['_1'];
    }
    else {
        $_tmp = jrCore_get_cookie('photoalbum');
        if (isset($_tmp)) {
            $_rep['_items'] = $_tmp;
        }
        else {
            $_rep['_items'] = false;
        }
        $_rep['item_id']        = $_post['_2'];
        $_rep['photoalbum_for'] = $_post['_1'];
        $_rep['session_id']     = session_id();
    }
    // return results
    return jrCore_parse_template("photoalbum_add.tpl", $_rep, 'jrPhotoAlbum');
}

//------------------------------
// add to photo album via javascript
//------------------------------
function view_jrPhotoAlbum_add_save($_post, $_user, $_conf)
{
    global $_mods;

    jrCore_validate_location_url();
    // Check for login
    if (isset($_conf['jrPhotoAlbum_require_login']) && $_conf['jrPhotoAlbum_require_login'] == 'on') {
        if (!jrUser_is_logged_in()) {
            $url = jrCore_get_module_url('jrUser');
            jrCore_notice_page('error', 1, "{$_conf['jrCore_base_url']}/{$url}/login", 2, true, false);
        }
    }
    if (jrUser_is_logged_in()) {
        jrUser_check_quota_access('jrPhotoAlbum');
    }
    $_lang   = jrUser_load_lang_strings();
    $item_id = (int) $_post['item_id'];
    $mod     = 'jrGallery';
    if (isset($_post['photoalbum_for']) && isset($_mods["{$_post['photoalbum_for']}"])) {
        $mod = $_post['photoalbum_for'];
    }
    if ($item_id == 0) {
        //fail
        $response = array(
            'success'     => false,
            'success_msg' => 'Error: there was no id to add to the playlist.'
        );
    }
    else {
        // Strip HTML Tags
        $title = strip_tags(html_entity_decode($_post['title']));
        if (jrCore_run_module_function('jrBanned_is_banned', 'word', $title)) {
            $response = array(
                'error'       => true,
                'success_msg' => $_lang['jrPhotoAlbum'][3]
            );
            echo json_encode($response);
            exit;
        }
        $photoalbum_list = array(
            $mod => array($item_id => 0)
        );
        //create the new photo album
        $_rt = array(
            'photoalbum_title'     => $title,
            'photoalbum_title_url' => jrCore_url_string($title),
            'photoalbum_list'      => json_encode($photoalbum_list),
            'photoalbum_count'     => 1
        );
        if (jrUser_is_logged_in()) {
            // NOTE: Photo albums are always stored to the profile of the creating user - even
            // for admin/master users - we override the default profile_id here and make
            // sure we use the home profile_id (instead of user_active_profile_id)
            $_cr = array(
                '_profile_id' => jrUser_get_profile_home_key('_profile_id')
            );
            // $aid will be the INSERT_ID (_item_id) of the created item
            $aid = jrCore_db_create_item('jrPhotoAlbum', $_rt, $_cr);
            if (!$aid) {
                $response = array(
                    'success'     => false,
                    'success_msg' => 'Error: Could not save to the database'
                );
            }
            else {
                // Add to Actions...
                jrCore_run_module_function('jrAction_save', 'create', 'jrPhotoAlbum', $aid);
                $response = array(
                    'success'     => true,
                    'success_msg' => $_lang['jrPhotoAlbum'][4]
                );
                jrUser_reset_cache($_user['_user_id'], 'jrPhotoAlbum');
                jrProfile_reset_cache(jrUser_get_profile_home_key('_profile_id'), 'jrPhotoAlbum');
            }
        }
        else {
            // For non-logged in users, we stored in a cookie
            $_tmp = jrCore_get_cookie('photoalbum');
            if (!$_tmp) {
                $_tmp = array();
            }
            $_tmp["{$_rt['photoalbum_title_url']}"]             = $_rt;
            $_tmp["{$_rt['photoalbum_title_url']}"]['_item_id'] = $_rt['photoalbum_title_url'];
            jrCore_set_cookie('photoalbum', $_tmp);
            $response = array(
                'success'     => true,
                'success_msg' => $_lang['jrPhotoAlbum'][4]
            );
        }
    }
    echo json_encode($response);
}

//----------------------------------------------------
// update the contents of a photo album via javascript
//----------------------------------------------------
function view_jrPhotoAlbum_inject_save($_post, $_user, $_conf)
{
    global $_mods;

    jrCore_validate_location_url();
    // Check for login
    if (isset($_conf['jrPhotoAlbum_require_login']) && $_conf['jrPhotoAlbum_require_login'] == 'on') {
        if (!jrUser_is_logged_in()) {
            $url = jrCore_get_module_url('jrUser');
            jrCore_notice_page('error', 1, "{$_conf['jrCore_base_url']}/{$url}/login", 2, true, false);
        }
    }
    if (jrUser_is_logged_in()) {
        jrUser_check_quota_access('jrPhotoAlbum');
    }
    $item_id = (int) $_post['item_id'];
    if ($item_id == 0) {
        //fail
        $response = array(
            'success'     => false,
            'success_msg' => 'Error: there item id or the photoalbum id was not numeric.'
        );
    }
    else {
        if (jrUser_is_logged_in()) {
            // Get existing photo album...
            $pid = (int) $_post['photoalbum_id'];
            $_rt = jrCore_db_get_item('jrPhotoAlbum', $pid);
            // Make sure the calling user has permission to edit this item
            if (!jrUser_can_edit_item($_rt)) {
                jrUser_not_authorized();
            }
        }
        else {
            $pid  = trim($_post['photoalbum_id']);
            $_tmp = jrCore_get_cookie('photoalbum');
            $_rt  = $_tmp["{$_post['photoalbum_id']}"];
        }
        $photoalbum_list = json_decode($_rt['photoalbum_list'], true);

        $mod = 'jrGallery';
        if (isset($_post['photoalbum_for']) && isset($_mods["{$_post['photoalbum_for']}"])) {
            $mod = $_post['photoalbum_for'];
        }
        $cnt = 0;
        if (!isset($photoalbum_list[$mod][$item_id])) {
            // New entry into photo album - Increment all item order values so that new entry can go at the beginning
            foreach ($photoalbum_list as $m => $_v) {
                foreach ($_v as $k => $v) {
                    $photoalbum_list[$m][$k]++;
                }
            }
            $photoalbum_list[$mod][$item_id] = 0;
        }
        foreach ($photoalbum_list as $mod => $items) {
            $cnt += count($items);
        }

        $_sv                         = array();
        $_sv['photoalbum_title']     = $_rt['photoalbum_title'];
        $_sv['photoalbum_title_url'] = jrCore_url_string($_rt['photoalbum_title']);
        $_sv['photoalbum_list']      = json_encode($photoalbum_list);
        $_sv['photoalbum_count']     = $cnt;

        if (jrUser_is_logged_in()) {
            jrCore_db_update_item('jrPhotoAlbum', $pid, $_sv);
            jrUser_reset_cache($_user['_user_id'], 'jrPhotoAlbum');
            jrProfile_reset_cache(jrUser_get_profile_home_key('_profile_id'), 'jrPhotoAlbum');
        }
        else {
            $_tmp                   = jrCore_get_cookie('photoalbum');
            $_tmp[$pid]             = $_sv;
            $_tmp[$pid]['_item_id'] = $pid;
            jrCore_set_cookie('photoalbum', $_tmp);
        }
        $_lang    = jrUser_load_lang_strings();
        $response = array(
            'success'     => true,
            'success_msg' => $_lang['jrPhotoAlbum'][5]
        );

    }
    echo json_encode($response);
}

//------------------------------
// update
//------------------------------
function view_jrPhotoAlbum_update($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrPhotoAlbum');

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'Unable to retrieve photo album from the database - please try again');
    }
    $_rt = jrCore_db_get_item('jrPhotoAlbum', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', 'Unable to retrieve photo album from the database - please try again');
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Start our create form
    jrCore_page_banner(6);

    // Form init
    $_tmp = array(
        'cancel' => jrCore_is_profile_referrer(),
        'values' => $_rt
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

    // PhotoAlbum Title
    $_tmp = array(
        'name'     => 'photoalbum_title',
        'label'    => 7,
        'help'     => 8,
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrPhotoAlbum_update_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrPhotoAlbum');

    // Validate all incoming posted data
    jrCore_form_validate($_post);

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid ID - please try again');
        jrCore_form_result();
    }

    // Get data
    $_rt = jrCore_db_get_item('jrPhotoAlbum', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 'Unable to retrieve photo album from the database - please try again');
        jrCore_form_result();
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrPhotoAlbum', 'update', $_post);

    // Add in our SEO URL names
    $_sv['photoalbum_title_url'] = jrCore_url_string($_sv['photoalbum_title']);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrPhotoAlbum', $_post['id'], $_sv);

    // Save any uploaded media file
    jrCore_save_all_media_files('jrPhotoAlbum', 'update', $_user['user_active_profile_id'], $_post['id']);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$_post['id']}/{$_sv['photoalbum_title_url']}");
}

//------------------------------
// delete
//------------------------------
function view_jrPhotoAlbum_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrPhotoAlbum');

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid ID - please try again');
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item('jrPhotoAlbum', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 'Unable to retrieve photo album from the database - please try again');
        jrCore_form_result();
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    // Delete item and any associated files
    jrCore_db_delete_item('jrPhotoAlbum', $_post['id']);
    jrProfile_reset_cache();
    jrCore_form_result('delete_referrer');
}

//------------------------------
// view (for non-logged in users)
//------------------------------
function view_jrPhotoAlbum_view($_post, $_user, $_conf)
{
    if (jrUser_is_logged_in()) {
        // This view is for non-logged in users - redirect to their page
        jrCore_location("{$_conf['jrCore_base_url']}/{$_user['profile_url']}");
    }

    // See if our photo album exists..
    $_tm = jrCore_get_cookie('photoalbum');
    $pid = trim($_post['_1']);
    if (!isset($_tm[$pid])) {
        // Bad photo album
        jrCore_notice_page('error', 'Invalid photo album');
    }

    // Get our photos for our list
    $_rep = array();
    $_sn  = json_decode($_tm[$pid]['photoalbum_list'], true);
    if (isset($_sn) && is_array($_sn)) {
        foreach ($_sn as $mod => $_ids) {
            $_sp = array(
                'search'                       => array(
                    '_item_id in ' . implode(',', array_keys($_ids))
                ),
                'limit'                        => 100,
                'exclude_jrProfile_quota_keys' => true
            );
            $_rt = jrCore_db_search_items($mod, $_sp);
            if (isset($_rt['_items']) && is_array($_rt['_items'])) {
                foreach ($_rt['_items'] as $k => $_itm) {
                    $_rt['_items'][$k]['module'] = $mod;
                }
                $_rep = array_merge($_rep, $_rt['_items']);
                unset($_rt);
            }
        }
    }
    $_rp           = array();
    $_rp['_items'] = $_rep;
    jrCore_page_banner($_tm["{$pid}"]['photoalbum_title']);
    jrCore_page_custom(jrCore_parse_template('item_photoalbum.tpl', $_rp, 'jrPhotoAlbum'));
    jrCore_page_display();
}

//----------------------------------------------------
// remove_save
//----------------------------------------------------
function view_jrPhotoAlbum_remove_save($_post, $_user, $_conf)
{
    global $_mods;

    jrCore_validate_location_url();
    // We should get a valid photoalbum_id and a valid item_id
    if (!isset($_post['photoalbum_id']) || strlen($_post['photoalbum_id']) === 0) {
        $_tmp = array(
            'error'     => 1,
            'error_msg' => 'invalid photoalbum_id'
        );
        jrCore_json_response($_tmp);
    }
    // Validate module
    if (!isset($_post['photoalbum_for']) || !isset($_mods["{$_post['photoalbum_for']}"])) {
        $_tmp = array(
            'error'     => 1,
            'error_msg' => 'invalid module'
        );
        jrCore_json_response($_tmp);
    }
    // Validate item
    if (!isset($_post['item_id']) || !jrCore_checktype($_post['item_id'], 'number_nz')) {
        $_tmp = array(
            'error'     => 1,
            'error_msg' => 'invalid item_id'
        );
        jrCore_json_response($_tmp);
    }

    // Delete this item from the users photoalbum
    if (jrUser_is_logged_in()) {

        $_rt = jrCore_db_get_item('jrPhotoAlbum', $_post['photoalbum_id']);
        if (!isset($_rt) || !is_array($_rt)) {
            $_tmp = array(
                'error'     => 1,
                'error_msg' => 'invalid photoalbum_id - data not found'
            );
            jrCore_json_response($_tmp);
        }
        if (!jrUser_can_edit_item($_rt)) {
            jrUser_not_authorized();
        }

        $_rt['photoalbum_list'] = json_decode($_rt['photoalbum_list'], true);
        if (isset($_rt['photoalbum_list']["{$_post['photoalbum_for']}"]["{$_post['item_id']}"])) {
            unset($_rt['photoalbum_list']["{$_post['photoalbum_for']}"]["{$_post['item_id']}"]);
            $_rt['photoalbum_count'] = (intval($_rt['photoalbum_count']) - 1);
        }
        $_dt = array(
            'photoalbum_list'  => json_encode($_rt['photoalbum_list']),
            'photoalbum_count' => $_rt['photoalbum_count']
        );
        jrCore_db_update_item('jrPhotoAlbum', $_post['photoalbum_id'], $_dt);
        jrUser_reset_cache($_user['_user_id'], 'jrPhotoAlbum');
        jrProfile_reset_cache(jrUser_get_profile_home_key('_profile_id'), 'jrPhotoAlbum');
    }
    else {
        // Remove this from their cookie
        $_tmp = jrCore_get_cookie('photoalbum');
        if (!isset($_tmp["{$_post['photoalbum_id']}"]) || !is_array($_tmp["{$_post['photoalbum_id']}"])) {
            $_tmp = array(
                'error'     => 1,
                'error_msg' => 'invalid photoalbum_id - data not found'
            );
            jrCore_json_response($_tmp);
        }
        $_tmp["{$_post['photoalbum_id']}"]['photoalbum_list'] = json_decode($_tmp["{$_post['photoalbum_id']}"]['photoalbum_list'], true);
        if (isset($_tmp["{$_post['photoalbum_id']}"]['photoalbum_list']["{$_post['photoalbum_for']}"]["{$_post['item_id']}"])) {
            unset($_tmp["{$_post['photoalbum_id']}"]['photoalbum_list']["{$_post['photoalbum_for']}"]["{$_post['item_id']}"]);
            $_tmp["{$_post['photoalbum_id']}"]['photoalbum_count'] = (intval($_tmp["{$_post['photoalbum_id']}"]['photoalbum_count']) - 1);
        }
        $_tmp["{$_post['photoalbum_id']}"]['photoalbum_list'] = json_encode($_tmp["{$_post['photoalbum_id']}"]['photoalbum_list']);
        jrCore_set_cookie('photoalbum', $_tmp);
    }
    $_tmp = array(
        'success' => 1
    );
    jrCore_json_response($_tmp);
}

//-------------------------------------
// update the order of the photo album
//-------------------------------------
function view_jrPhotoAlbum_order_update($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_session_require_login();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        return jrCore_json_response(array('error' => 'invalid photo album id received'));
    }
    if (!isset($_post['photoalbum_order']) || !is_array($_post['photoalbum_order'])) {
        return jrCore_json_response(array('error' => 'invalid photo album order array received'));
    }

    $_pal = jrCore_db_get_item('jrPhotoAlbum', $_post['id']);
    if (!isset($_pal) || !is_array($_pal)) {
        return jrCore_json_response(array('error' => 'invalid photo album - unable to load data'));
    }
    if (!jrUser_can_edit_item($_pal)) {
        return jrCore_json_response(array('error' => 'permission denied'));
    }

    // Update photo album order
    $_list = array();
    $i     = 0;
    foreach ($_post['photoalbum_order'] as $mod_id) {
        list($mod, $id) = explode('-', $mod_id, 2);
        if (!isset($_mods[$mod])) {
            continue;
        }
        $id               = intval($id);
        $_list[$mod][$id] = $i;
        $i++;
    }
    $_data = array(
        'photoalbum_list' => json_encode($_list)
    );
    jrCore_db_update_item('jrPhotoAlbum', $_post['id'], $_data);
    jrUser_reset_cache($_user['_user_id'], 'jrPhotoAlbum');
    jrProfile_reset_cache(jrUser_get_profile_home_key('_profile_id'), 'jrPhotoAlbum');
    return jrCore_json_response(array('success' => 'photo album order updated'));
}
