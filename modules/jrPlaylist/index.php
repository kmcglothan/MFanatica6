<?php
/**
 * Jamroom Playlists module
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
// view
//------------------------------
function view_jrPlaylist_view($_post, $_user, $_conf)
{
    if (jrUser_is_logged_in()) {
        // This view is for non-logged in users - redirect to their page
        jrCore_location("{$_conf['jrCore_base_url']}/{$_user['profile_url']}");
    }

    // See if our playlist exists..
    $_ln = jrUser_load_lang_strings();
    $_tm = jrCore_get_cookie('playlist');
    $pid = trim($_post['_1']);
    if (!isset($_tm[$pid])) {
        // Bad playlist
        jrCore_notice_page('error', 'Invalid playlist');
    }

    if (isset($_conf['jrUser_signup_on']) && $_conf['jrUser_signup_on'] == 'on') {
        $url    = jrCore_get_module_url('jrUser');
        $button = jrCore_page_button('login', $_ln['jrPlaylist'][45], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/login')");
        $button .= '&nbsp;' . jrCore_page_button('create', $_ln['jrPlaylist'][46], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/signup')");
        jrCore_set_form_notice('notice', "{$_ln['jrPlaylist'][46]}<br><br>{$button}", false);
        jrCore_get_form_notice();
    }

    // Get our songs for our list
    $htm = '</table><table>';
    $_sn = json_decode($_tm[$pid]['playlist_list'], true);
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

                // Show player
                foreach ($_rt['_items'] as $k => $_itm) {
                    $_rt['_items'][$k]['module'] = $mod;
                }
                $_pt['item']['playlist_items'] = $_rt['_items'];
                $htm                           .= jrCore_parse_template('item_embed.tpl', $_pt, 'jrPlaylist');
                unset($_tm['playlist_list']);

                // Show items in playlist
                foreach ($_rt['_items'] as $_item) {
                    $_rp = array(
                        'playlist_item' => $_item
                    );
                    $htm .= jrCore_parse_template('item_playlist.tpl', $_rp, $mod);
                }
                unset($_rt);
            }
        }
    }
    jrCore_page_custom($htm, '<h2>' . $_tm[$pid]['playlist_title'] . '</h2>');
    jrCore_page_display();
}

//------------------------------
// update
//------------------------------
function view_jrPlaylist_update($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrPlaylist');

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 8);
    }
    $_rt = jrCore_db_get_item('jrPlaylist', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', 8);
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Start our create form
    jrCore_page_banner(5);

    // Form init
    $_tmp = array(
        'submit_value' => 5,
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

    // Playlist Title
    $_tmp = array(
        'name'     => 'playlist_title',
        'label'    => 6,
        'help'     => 7,
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
function view_jrPlaylist_update_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrPlaylist');

    // Validate all incoming posted data
    jrCore_form_validate($_post);

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 8);
        jrCore_form_result();
    }

    // Get data
    $_rt = jrCore_db_get_item('jrPlaylist', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 8);
        jrCore_form_result();
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrPlaylist', 'update', $_post);

    // Add in our SEO URL names
    $_sv['playlist_title_url'] = jrCore_url_string($_sv['playlist_title']);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrPlaylist', $_post['id'], $_sv);

    // Save any uploaded media file
    jrCore_save_all_media_files('jrPlaylist', 'update', $_user['user_active_profile_id'], $_post['id']);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$_post['id']}/{$_sv['playlist_title_url']}");
}

//------------------------------
// delete
//------------------------------
function view_jrPlaylist_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrPlaylist');

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 9);
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item('jrPlaylist', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 9);
        jrCore_form_result();
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    // Delete item and any associated files
    jrCore_db_delete_item('jrPlaylist', $_post['id']);
    jrProfile_reset_cache();
    jrCore_form_result('delete_referrer');
}

//------------------------------
// Show playlist drop down
//------------------------------
function view_jrPlaylist_add($_post, $_user, $_conf)
{
    jrCore_validate_location_url();
    // Check for login
    if (isset($_conf['jrPlaylist_require_login']) && $_conf['jrPlaylist_require_login'] == 'on') {
        if (!jrUser_is_logged_in()) {
            $url = jrCore_get_module_url('jrUser');
            jrCore_notice_page('error', 48, "{$_conf['jrCore_base_url']}/{$url}/login", 49, true, false);
        }
    }
    // Make sure we get a good id - /playlist/add/jrAudio/1
    if (!isset($_post['_2']) || !jrCore_checktype($_post['_2'], 'number_nz')) {
        jrCore_notice_page('error', "invalid item_id - please try again", false, false, true, false);
    }

    // show all the playlists belonging to this user.
    if (jrUser_is_logged_in()) {

        jrUser_check_quota_access('jrPlaylist');

        // Check for pagebreak
        $p = 1;
        if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
            $p = (int) $_post['p'];
        }
        $_sp                  = array(
            'search'      => array(
                "_profile_id = " . jrUser_get_profile_home_key('_profile_id')
            ),
            'order_by'    => array('_item_id' => 'desc'),
            'quota_check' => false,
            'pagebreak'   => 6,
            'page'        => $p
        );
        $_rep                 = jrCore_db_search_items('jrPlaylist', $_sp);
        $_rep['item_id']      = $_post['_2'];
        $_rep['playlist_for'] = $_post['_1'];
    }
    else {
        $_tmp = jrCore_get_cookie('playlist');
        if (isset($_tmp)) {
            $_rep['_items'] = $_tmp;
        }
        else {
            $_rep['_items'] = false;
        }
        $_rep['item_id']      = $_post['_2'];
        $_rep['playlist_for'] = $_post['_1'];
        $_rep['session_id']   = session_id();
    }
    return jrCore_parse_template("playlist_add.tpl", $_rep, 'jrPlaylist');
}

//------------------------------
// Create a new playlist
//------------------------------
function view_jrPlaylist_add_save($_post, $_user, $_conf)
{
    global $_mods;
    jrCore_validate_location_url();
    // Check for login
    if (isset($_conf['jrPlaylist_require_login']) && $_conf['jrPlaylist_require_login'] == 'on') {
        if (!jrUser_is_logged_in()) {
            $url = jrCore_get_module_url('jrUser');
            jrCore_notice_page('error', 48, "{$_conf['jrCore_base_url']}/{$url}/login", 49, true, false);
        }
    }
    if (jrUser_is_logged_in()) {
        jrUser_check_quota_access('jrPlaylist');
    }
    $item_id = (int) $_post['item_id'];
    $mod     = 'jrAudio';
    if (isset($_post['playlist_for']) && isset($_mods["{$_post['playlist_for']}"])) {
        $mod = $_post['playlist_for'];
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
                'success_msg' => 'Invalid playlist title'
            );
            echo json_encode($response);
            exit;
        }

        $playlist_list = array(
            $mod => array($item_id => 0)
        );

        //create the new playlist
        $_rt = array(
            'playlist_title'     => $title,
            'playlist_title_url' => jrCore_url_string($title),
            'playlist_list'      => json_encode($playlist_list),
            'playlist_count'     => 1
        );

        if (jrUser_is_logged_in()) {

            // NOTE: Play lists are always stored to the profile of the creating user - even
            // for admin/master users - we override the default profile_id here and make
            // sure we use the home profile_id (instead of user_active_profile_id)
            $_cr = array(
                '_profile_id' => jrUser_get_profile_home_key('_profile_id')
            );
            // $aid will be the INSERT_ID (_item_id) of the created item
            $aid = jrCore_db_create_item('jrPlaylist', $_rt, $_cr);
            if (!$aid) {
                $_debug = array(
                    '$_rt' => $_rt,
                    '$_cr' => $_cr
                );
                jrCore_logger('MAJ', 'Unable to save new playlist. jrCore_db_create_item failed', $_debug );
                $response = array(
                    'success'     => false,
                    'success_msg' => 'Error: Unable to save new playlist - please try again'
                );
            }
            else {
                // Add to Actions...
                jrCore_run_module_function('jrAction_save', 'create', 'jrPlaylist', $aid);
                $response = array(
                    'success'     => true,
                    'success_msg' => 'New playlist created'
                );
                jrUser_reset_cache($_user['_user_id'], 'jrPlaylist');
                jrProfile_reset_cache(jrUser_get_profile_home_key('_profile_id'));
            }
        }
        else {

            // For non-logged in users, we stored in a cookie
            $_tmp = jrCore_get_cookie('playlist');
            if (!$_tmp) {
                $_tmp = array();
            }
            $_tmp["{$_rt['playlist_title_url']}"]             = $_rt;
            $_tmp["{$_rt['playlist_title_url']}"]['_item_id'] = $_rt['playlist_title_url'];
            jrCore_set_cookie('playlist', $_tmp);
            $response = array(
                'success'     => true,
                'success_msg' => 'New playlist created'
            );
        }
    }
    echo json_encode($response);
}

//----------------------------------------------------
// Add a new item to a playlist
//----------------------------------------------------
function view_jrPlaylist_inject_save($_post, $_user, $_conf)
{
    global $_mods;
    jrCore_validate_location_url();

    // Check for login
    if (isset($_conf['jrPlaylist_require_login']) && $_conf['jrPlaylist_require_login'] == 'on') {
        if (!jrUser_is_logged_in()) {
            $url = jrCore_get_module_url('jrUser');
            jrCore_notice_page('error', 48, "{$_conf['jrCore_base_url']}/{$url}/login", 49, true, false);
        }
    }
    if (jrUser_is_logged_in()) {
        jrUser_check_quota_access('jrPlaylist');
    }

    $_ln = jrUser_load_lang_strings();
    if (!isset($_post['item_id']) || !jrCore_checktype($_post['item_id'], 'number_nz')) {
        $_rp = array(
            'success'     => false,
            'success_msg' => 'invalid item_id'
        );
        jrCore_json_response($_rp);
    }
    $iid = (int) $_post['item_id'];

    // success
    if (jrUser_is_logged_in()) {
        // Get existing playlist...
        $pid = (int) $_post['playlist_id'];
        $_rt = jrCore_db_get_item('jrPlaylist', $pid);
        if (!$_rt || !is_array($_rt)) {
            $_rp = array(
                'success'     => false,
                'success_msg' => 'invalid item_id - data not found'
            );
            jrCore_json_response($_rp);
        }
        // Make sure the calling user has permission to edit this item
        if (!jrUser_can_edit_item($_rt)) {
            jrUser_not_authorized();
        }
    }
    else {
        $pid  = trim($_post['playlist_id']);
        $_tmp = jrCore_get_cookie('playlist');
        $_rt  = $_tmp["{$_post['playlist_id']}"];
    }
    $playlist_list = json_decode($_rt['playlist_list'], true);

    $mod = 'jrAudio';
    if (isset($_post['playlist_for']) && isset($_mods["{$_post['playlist_for']}"])) {
        $mod = $_post['playlist_for'];
    }
    $cnt = 0;
    if (!isset($playlist_list[$mod][$iid])) {

        // New entry into playlist
        $sum = 0;
        foreach ($playlist_list as $entries) {
            $sum += count($entries);
        }
        if (!isset($playlist_list[$mod])) {
            $playlist_list[$mod] = array();
        }
        $playlist_list[$mod][$iid] = $sum;
        $cnt                       = 1;
    }

    // Update playlist
    $_sv = array(
        'playlist_title'     => $_rt['playlist_title'],
        'playlist_title_url' => jrCore_url_string($_rt['playlist_title']),
        'playlist_list'      => json_encode($playlist_list),
        'playlist_count'     => (int) ($_rt['playlist_count'] + $cnt)
    );

    if (jrUser_is_logged_in()) {
        jrCore_db_update_item('jrPlaylist', $pid, $_sv);
        jrUser_reset_cache($_user['_user_id'], 'jrPlaylist');
        jrProfile_reset_cache(jrUser_get_profile_home_key('_profile_id'));
    }
    else {
        $_tmp                   = jrCore_get_cookie('playlist');
        $_tmp[$pid]             = $_sv;
        $_tmp[$pid]['_item_id'] = $pid;
        jrCore_set_cookie('playlist', $_tmp);
    }
    $_rp = array(
        'success'     => true,
        'success_msg' => $_ln['jrPlaylist'][50]
    );
    jrCore_json_response($_rp);
}

//----------------------------------------------------
// remove_save
//----------------------------------------------------
function view_jrPlaylist_remove_save($_post, $_user, $_conf)
{
    global $_mods;
    jrCore_validate_location_url();
    // We should get a valid playlist_id and a valid item_id
    if (!isset($_post['playlist_id']) || strlen($_post['playlist_id']) === 0) {
        $_tmp = array(
            'error'     => 1,
            'error_msg' => 'invalid playlist_id'
        );
        jrCore_json_response($_tmp);
    }
    // Validate module
    if (!isset($_post['playlist_for']) || !isset($_mods["{$_post['playlist_for']}"])) {
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

    // Delete this item from the users playlist
    if (jrUser_is_logged_in()) {

        $_rt = jrCore_db_get_item('jrPlaylist', $_post['playlist_id']);
        if (!isset($_rt) || !is_array($_rt)) {
            $_tmp = array(
                'error'     => 1,
                'error_msg' => 'invalid playlist_id - data not found'
            );
            jrCore_json_response($_tmp);
        }
        if (!jrUser_can_edit_item($_rt)) {
            jrUser_not_authorized();
        }

        $_rt['playlist_list'] = json_decode($_rt['playlist_list'], true);
        if (isset($_rt['playlist_list']["{$_post['playlist_for']}"]["{$_post['item_id']}"])) {
            unset($_rt['playlist_list']["{$_post['playlist_for']}"]["{$_post['item_id']}"]);
            $_rt['playlist_count'] = (intval($_rt['playlist_count']) - 1);
        }
        $_dt = array(
            'playlist_list'  => json_encode($_rt['playlist_list']),
            'playlist_count' => $_rt['playlist_count']
        );
        jrCore_db_update_item('jrPlaylist', $_post['playlist_id'], $_dt);
        jrUser_reset_cache($_user['_user_id'], 'jrPlaylist');
    }
    else {
        // Remove this from their cookie
        $_tmp = jrCore_get_cookie('playlist');
        if (!isset($_tmp["{$_post['playlist_id']}"]) || !is_array($_tmp["{$_post['playlist_id']}"])) {
            $_tmp = array(
                'error'     => 1,
                'error_msg' => 'invalid playlist_id - data not found'
            );
            jrCore_json_response($_tmp);
        }
        $_tmp["{$_post['playlist_id']}"]['playlist_list'] = json_decode($_tmp["{$_post['playlist_id']}"]['playlist_list'], true);
        if (isset($_tmp["{$_post['playlist_id']}"]['playlist_list']["{$_post['playlist_for']}"]["{$_post['item_id']}"])) {
            unset($_tmp["{$_post['playlist_id']}"]['playlist_list']["{$_post['playlist_for']}"]["{$_post['item_id']}"]);
            $_tmp["{$_post['playlist_id']}"]['playlist_count'] = (intval($_tmp["{$_post['playlist_id']}"]['playlist_count']) - 1);
        }
        $_tmp["{$_post['playlist_id']}"]['playlist_list'] = json_encode($_tmp["{$_post['playlist_id']}"]['playlist_list']);
        jrCore_set_cookie('playlist', $_tmp);
    }
    $_tmp = array(
        'success' => 1
    );
    jrCore_json_response($_tmp);
}

/**
 * Show just the playlist player by itself for use with embedding into pages via an iframe and jrEmbed
 * @param $_post array posted info
 * @param $_user array Active User info
 * @param $_conf array System Global Config
 * @return string
 */
function view_jrPlaylist_standalone($_post, $_user, $_conf)
{
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        return '';
    }
    $pid = (int) $_post['_1'];
    // get this playlist.
    $_rt = jrCore_db_get_item('jrPlaylist', $pid);
    if (!$_rt || !is_array($_rt)) {
        jrCore_notice_page('error', 21);
    }
    $_data = array(
        'item' => $_rt,
    );
    return jrCore_parse_template('standalone_playlist.tpl', $_data, 'jrPlaylist');
}

//---------------------------------------------------------
// Playlist Embed (used for twitter cards etc, just a player)
//---------------------------------------------------------
function view_jrPlaylist_embed($_post, $_user, $_conf)
{
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('notice', 'playlist with that id could not be located');
    }

    $_rt = jrCore_db_get_item('jrPlaylist', $_post['_1']);

    if (!$_rt) {
        jrCore_notice_page('notice', 'playlist with that id could not be found in the datastore');
    }

    $_rep = array(
        'item' => $_rt
    );
    $html = jrCore_parse_template('item_embed.tpl', $_rep, 'jrPlaylist');

    jrCore_page_set_meta_header_only();
    jrCore_page_custom($html);
    jrCore_page_display();

}

//----------------------------------
// update the order of a playlist
//----------------------------------
function view_jrPlaylist_order_update($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_session_require_login();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        return jrCore_json_response(array('error', 'invalid playlist id received'));
    }
    if (!isset($_post['playlist_order']) || !is_array($_post['playlist_order'])) {
        return jrCore_json_response(array('error', 'invalid playlist_order array received'));
    }
    $_pl = jrCore_db_get_item('jrPlaylist', $_post['id']);
    if (!isset($_pl) || !is_array($_pl)) {
        return jrCore_json_response(array('error', 'invalid playlist - unable to load data'));
    }
    if (!jrUser_can_edit_item($_pl)) {
        return jrCore_json_response(array('error', 'permission denied'));
    }
    // Update playlist order
    // [playlist_list] => {"jrAudio":{"8":0,"10":1,"11":2,"12":3,"152":4,"385":8,"383":9,"382":10,"393":11},"jrVideo":{"41":5,"40":6},"jrSoundCloud":{"1":7}}
    $_list = array();
    foreach ($_post['playlist_order'] as $num => $mod_id) {
        list($mod, $id) = explode('-', $mod_id, 2);
        if (!isset($_mods[$mod])) {
            continue;
        }
        $id               = intval($id);
        $_list[$mod][$id] = $num;
    }
    $_data = array(
        'playlist_list' => json_encode($_list)
    );
    jrCore_db_update_item('jrPlaylist', $_post['id'], $_data);
    jrProfile_reset_cache();
    return jrCore_json_response(array('success', 'playlist_order successfully updated'));
}
