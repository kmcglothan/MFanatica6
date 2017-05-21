<?php
/**
 * Jamroom Timeline module
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//------------------------------
// mention_profiles
//------------------------------
function view_jrAction_mention_profiles($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (isset($_post['q']) && strlen($_post['q']) > 1) {
        $_sc = array(
            'search'                       => array(
                "profile_name like {$_post['q']}%"
            ),
            'exclude_jrUser_keys'          => true,
            'exclude_jrProfile_quota_keys' => true,
            'ignore_pending'               => true,
            'return_keys'                  => array('_profile_id', 'profile_url', 'profile_name')
        );
        $_rt = jrCore_db_search_items('jrProfile', $_sc);
        if ($_rt && isset($_rt['_items']) && is_array($_rt['_items'])) {
            $_rs = array();
            $url = jrCore_get_module_url('jrProfile');
            $nam = jrUser_get_profile_home_key('profile_name');
            foreach ($_rt['_items'] as $_v) {
                if ($_v['profile_name'] != $nam) {
                    $_rs[] = array(
                        'id'     => $_v['_profile_id'],
                        'name'   => "@{$_v['profile_url']}",
                        'avatar' => "{$_conf['jrCore_base_url']}/{$url}/image/profile_image/{$_v['_profile_id']}/xsmall/crop=auto",
                        'type'   => 'contact'
                    );
                }
            }
            jrCore_json_response($_rs);
        }
    }
    jrCore_db_close();
    exit;
}

//------------------------------
// share
//------------------------------
function view_jrAction_share_msg($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrAction');

    if (!isset($_post['_1']) || !jrCore_module_is_active($_post['_1'])) {
        return 'error: invalid module';
    }
    if (!isset($_post['_2']) || !jrCore_checktype($_post['_2'], 'number_nz')) {
        return 'error: invalid item_id';
    }
    $_temp            = $_post;
    $_temp['module']  = $_post['_1'];
    $_temp['item_id'] = (int) $_post['_2'];

    // Does this module have a custom template?
    $_temp['template'] = false;
    if (is_file(APP_DIR . "/modules/{$_post['_1']}/templates/item_share.tpl")) {
        $_temp['template'] = 'item_share.tpl';
    }
    return jrCore_parse_template('item_share_modal.tpl', $_temp, 'jrAction');
}

//------------------------------
// share
//------------------------------
function view_jrAction_share($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrAction');
    jrCore_validate_location_url();

    // Must get a module
    if (!isset($_post['_1']) || !jrCore_module_is_active($_post['_1'])) {
        jrCore_notice_page('error', 'Invalid module received - please try again');
    }
    $mod = $_post['_1'];

    // We should get a valid action ID
    if (!isset($_post['_2']) || !jrCore_checktype($_post['_2'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid action_id received - please try again');
    }
    $iid = (int) $_post['_2'];

    // Are we sharing an item or a timeline entry?
    if ($mod == 'jrAction') {

        // We are sharing a Timeline entry
        $_rt = jrCore_db_get_item('jrAction', $iid);
        if (!$_rt || !is_array($_rt)) {
            jrCore_notice_page('error', 'Invalid item_id received - no data found');
        }

        // Make sure we have not ALREADY shared this
        if ($aid = jrAction_get_user_share_id('jrAction', $iid, $_user['_user_id'])) {
            // We have already shared this item - redirect to the action item detail page
            jrCore_location("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/" . $aid);
        }

        // add in the share text if present
        $txt = '';
        if (isset($_post['share_text']) && strlen($_post['share_text']) > 0) {
            $txt = $_post['share_text'] . "\n";
        }
        $url = jrCore_get_module_url('jrAction');
        $url = "{$_conf['jrCore_base_url']}/{$_rt['profile_url']}/{$url}/{$iid}";

        // Copy it
        $_nw = array(
            'action_text'       => $txt,
            'action_shared_url' => $url,
            'action_module'     => 'jrAction',
            'action_shared'     => (int) $_rt['_profile_id'],
            'action_original_item' => "jrAction:{$iid}:create"
        );

        $aid = jrCore_db_create_item('jrAction', $_nw, array('_profile_id' => jrUser_get_profile_home_key('_profile_id')));
        if (!$aid) {
            jrCore_notice_page('error', 'unable to share action!');
        }

        // Save hashes in share text
        if (isset($_post['share_text']) && strlen($_post['share_text']) > 0) {
            jrAction_save_hash_tags($_post['share_text']);
        }

        // Add user_id to item action_shared_by field
        $_sb = array();
        if (isset($_rt['action_shared_by'])) {
            $_sb = explode(',', $_rt['action_shared_by']);
        }
        if (count($_sb) > 0) {
            foreach ($_sb as $k => $v) {
                if (!is_numeric($v)) {
                    unset($_sb[$k]);
                }
                else {
                    $_sb[$k] = (int) $v;
                }
            }
        }
        $_sb[] = (int) $_user['_user_id'];
        $_up   = array(
            'action_shared_by' => implode(',', $_sb)
        );
        jrCore_db_update_item('jrAction', $_rt['_item_id'], $_up);

        // Save Share
        jrAction_save_user_share('jrAction', $iid, $_user['_user_id'], $aid);

        // Increment shared key for profile of item being shared
        jrCore_db_increment_key('jrProfile', $_rt['_profile_id'], 'profile_jrAction_shared_item_count', 1);

        // Notifications
        $_owners = jrProfile_get_owner_info($_rt['_profile_id']);
        if ($_owners && is_array($_owners)) {
            $_rp = array(
                'action_user' => $_user,
                'action_url'  => jrCore_get_local_referrer()
            );
            list($sub, $msg) = jrCore_parse_email_templates('jrAction', 'share', $_rp);
            foreach ($_owners as $_o) {
                if ($_o['_user_id'] != $_user['_user_id']) {
                    jrUser_notify($_o['_user_id'], 0, 'jrAction', 'share', $sub, $msg);
                }
            }
        }

        // If this is an admin user, redirect back to their home time line
        if (jrUser_is_admin()) {
            $url = jrUser_get_profile_home_key('profile_url');
            jrCore_location("{$_conf['jrCore_base_url']}/{$url}/{$_post['module_url']}");
        }
        $url = jrCore_get_local_referrer();
        // Is this a mapped URL?
        if (strpos($url, $_conf['jrCore_base_url']) !== 0) {
            $url = jrCore_strip_url_params($url, array('p'));
            jrCore_location($url);
        }
        jrCore_location("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}");
    }

    // Fall through - We are sharing an item - we're going to simply create a URL
    // Share to our timeline that can be picked up by the Media URL Scanner
    $_it = jrCore_db_get_item($mod, $iid);
    if (!$_it || !is_array($_it)) {
        jrCore_notice_page('error', 'Invalid item_id received - no data found');
    }

    $txt = '';
    if (isset($_post['share_text']{1})) {
        jrAction_save_hash_tags($_post['share_text']);
        $txt = $_post['share_text'] . "\n";
    }

    $pfx = jrCore_db_get_prefix($mod);
    $ttl = '';
    if (isset($_it["{$pfx}_title_url"])) {
        $ttl = '/' . $_it["{$pfx}_title_url"];
    }
    $url = jrCore_get_module_url($mod);
    $url = "{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$url}/{$iid}{$ttl}";

    $_rt = array(
        'action_text'          => $txt,
        'action_shared_url'    => $url,
        'action_module'        => 'jrAction',
        'action_shared'        => $_it['_profile_id'],
        'action_original_item' => "{$mod}:{$iid}:create"
    );

    $pid = jrUser_get_profile_home_key('_profile_id');
    $_cr = array(
        '_profile_id' => $pid
    );
    $aid = jrCore_db_create_item('jrAction', $_rt, $_cr);
    if (!$aid) {
        jrCore_notice_page('error', 'unable to create new activity entry - please try again');
    }

    // Save hashes
    if (isset($_post['share_text']{1})) {
        jrAction_save_hash_tags($_post['share_text']);
    }

    // Save Share
    jrAction_save_user_share($mod, $iid, $_user['_user_id'], $aid);

    // Send out our Action Created trigger
    $_args = array(
        '_item_id'    => $aid,
        '_user_id'    => $_user['_user_id'],
        '_profile_id' => $pid
    );
    jrCore_trigger_event('jrAction', 'create', $_rt, $_args);

    jrCore_db_increment_key($mod, $iid, "{$pfx}_share_count", 1);

    // Increment shared key for profile of item being shared as long as we're not sharing our own item
    if ($_it['_profile_id'] !== $pid) {
        jrCore_db_increment_key('jrProfile', $_it['_profile_id'], 'profile_jrAction_shared_item_count', 1);
    }

    jrProfile_reset_cache($pid);
    jrCore_location(jrCore_get_local_referrer());
}

//------------------------------
// create
//------------------------------
function view_jrAction_create($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrAction');
    jrCore_page_banner('Activity Update');

    // Form init
    $_tmp = array(
        'submit_value' => 'save',
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    // Activity Update
    $_tmp = array(
        'name'     => 'action_text',
        'label'    => 'Activity Update',
        'help'     => 'Enter an update for your Profile',
        'type'     => 'textarea',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrAction_create_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrAction');
    if (!jrUser_is_admin() && isset($_user['quota_jrAction_can_post']) && $_user['quota_jrAction_can_post'] != 'on') {
        jrUser_not_authorized();
    }

    // Must get a posted function
    if (!isset($_post['jrAction_function'])) {
        $_rs = array('error' => 'invalid action function');
        jrCore_json_response($_rs);
    }

    $fnd = false;
    $_tm = jrCore_get_registered_module_features('jrAction', 'quick_share');
    if ($_tm && is_array($_tm)) {
        foreach ($_tm as $mod => $_inf) {
            foreach ($_inf as $function => $_config) {
                if ($function == $_post['jrAction_function']) {
                    $fnd = $mod;
                    break 2;
                }
            }
        }
    }
    if (!$fnd) {
        // Function is not registered
        $_rs = array('error' => 'invalid action function - function not found');
        jrCore_json_response($_rs);
    }
    if ($_post['jrAction_function'] == 'jrAction_quick_share_status_update') {
        // Is editor turned on?
        if (isset($_post['action_text_editor_contents'])) {
            $_post['action_text'] = $_post['action_text_editor_contents'];
        }
        jrAction_save_hash_tags($_post['action_text']);
    }
    else {
        unset($_post['action_text']);
    }

    // Pass off to save function
    $func = "{$_post['jrAction_function']}_save";
    if (!function_exists($func)) {
        // Save function does not exist
        $_rs = array('error' => 'invalid action function - save function not found');
        jrCore_json_response($_rs);
    }
    if ($temp = $func($_post, $_user, $_conf)) {
        // If we get a missing value from the SAVE function it will start with FIELD
        if (strpos($temp, 'FIELD:') === 0) {
            $_rs = array('field' => trim(substr($temp, 7)));
            jrCore_json_response($_rs);
        }
        // If we get an error from a SAVE function it will start with ERROR:
        elseif (strpos($temp, 'ERROR:') === 0) {
            $_rs = array('error' => substr($temp, 7));
            jrCore_json_response($_rs);
        }
        // Success
        jrProfile_reset_cache();
        jrCore_form_delete_session();
        $_rs = array('success' => 1);
    }
    else {
        $_rs = array('error', 'an internal error has occured - please try again');
    }
    jrCore_json_response($_rs);
}

//------------------------------
// delete
//------------------------------
function view_jrAction_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrAction');
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid item id');
    }
    $_rt = jrCore_db_get_item('jrAction', $_post['id'], SKIP_TRIGGERS);
    if (!$_rt || !is_array($_rt)) {
        jrCore_notice_page('error', 'Invalid item id');
    }
    // Make sure the calling user has permissions to remove this action
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    jrCore_db_delete_item('jrAction', $_post['id']);

    // If we are deleting a SHARED entry, we have to update the original item so it no longer thinks we shared it
    if (isset($_rt['action_original_item']{3})) {
        list($mod, $iid,) = explode(':', $_rt['action_original_item'], 3);
        if ($mod == 'jrAction' && jrCore_checktype($iid, 'number_nz')) {
            $_oi = jrCore_db_get_item('jrAction', $iid, SKIP_TRIGGERS);
            if ($_oi && is_array($_oi) && isset($_oi['action_shared_by'])) {
                $_tm = explode(',', $_oi['action_shared_by']);
                if ($_tm && is_array($_tm)) {
                    foreach ($_tm as $k => $v) {
                        if ($v == $_rt['_user_id']) {
                            unset($_tm[$k]);
                        }
                    }
                    jrCore_db_update_item('jrAction', $iid, array('action_shared_by' => implode(',', $_tm)));
                    jrProfile_reset_cache($_oi['_profile_id'], 'jrAction');
                }
            }
        }
    }
    jrProfile_reset_cache($_rt['_profile_id']);

    // See where we came from
    $url = jrCore_get_local_referrer();
    if (strpos($url, "{$_post['module_url']}/{$_post['id']}")) {
        jrCore_location("{$_conf['jrCore_base_url']}/{$_user['profile_url']}");
    }
    jrCore_location('referrer');
}

//------------------------------
// update
//------------------------------
function view_jrAction_update($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrAction');

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid item id');
    }
    $_rt = jrCore_db_get_item('jrAction', $_post['id'], SKIP_TRIGGERS);
    if (!$_rt || !is_array($_rt)) {
        jrCore_notice_page('error', 'Invalid item - no data found for given id');
    }
    // Can only update action text items
    if (!isset($_rt['action_text'])) {
        jrCore_notice_page('error', 'This Timeline entry was generated by a listening module and cannot be edited');
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Start output
    jrCore_page_banner(27);

    // Form init
    $_tmp = array(
        'submit_value' => 28,
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

    // Action Text
    $_tmp = array(
        'name'     => 'action_text',
        'label'    => 29,
        'help'     => 30,
        'type'     => 'textarea',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrAction_update_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();

    // Validate all incoming posted data
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrAction');

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid item id');
        jrCore_form_result();
    }

    // Get data
    $_rt = jrCore_db_get_item('jrAction', $_post['id'], SKIP_TRIGGERS);
    if (!$_rt || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 'Unable to retrieve item data');
        jrCore_form_result();
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrAction', 'update', $_post);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrAction', $_post['id'], $_sv);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$_post['id']}");
}

//------------------------------
// quick_share_form
//------------------------------
function view_jrAction_quick_share_form($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrAction');
    if (!isset($_post['function'])) {
        $_rs = array('error' => 'invalid quick share function');
        jrCore_json_response($_rs);
    }
    $func = $_post['function'];
    if (!function_exists($func)) {
        $_rs = array('error' => 'invalid quick share function - function does not exist');
        jrCore_json_response($_rs);
    }

    // Get modules supporting Quick Share
    $ttl = '';
    $_tm = jrCore_get_registered_module_features('jrAction', 'quick_share');
    if ($_tm && is_array($_tm)) {
        $_ln = jrUser_load_lang_strings();
        foreach ($_tm as $mod => $_inf) {
            foreach ($_inf as $function => $_config) {
                if ($function == $func) {
                    if (isset($_config['title']) && jrCore_checktype($_config['title'], 'number_nz')) {
                        $_config['title'] = $_ln[$mod]["{$_config['title']}"];
                    }
                    $ttl = $_config['title'];
                    break;
                }
            }
        }
    }
    if (strlen($ttl) > 0) {
        $_rs = array(
            'html'  => $func($_post, $_user, $_conf),
            'title' => $ttl
        );
        jrCore_json_response($_rs, true, false);
    }
    $_rs = array('error' => 'invalid quick share function - function is not registered');
    jrCore_json_response($_rs);
}

//------------------------------
// new_actions_count
//------------------------------
function view_jrAction_new_actions_count($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['last_item_id']) || !jrCore_checktype($_post['last_item_id'], 'number_nz')) {
        $_post['last_item_id'] = 1;
    }
    $lid = (int) $_post['last_item_id'];
    $_rt = array(
        'search'           => array(
            "_item_id > {$lid}"
        ),
        'return_count'     => true,
        'skip_triggers'    => true,
        'include_followed' => true,
        'profile_id'       => jrUser_get_profile_home_key('_profile_id'),
        'limit'            => 10000
    );
    $cnt = jrCore_db_search_items('jrAction', $_rt);
    jrCore_json_response(array('cnt' => $cnt));
}
