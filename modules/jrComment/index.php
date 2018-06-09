<?php
/**
 * Jamroom Comments module
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
// quote
//------------------------------
function view_jrComment_quote($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        $_rs = array('error' => 'invalid comment id');
        jrCore_json_response($_rs);
    }

    // NOTE: We use jrCore_db_search_items here since it gives us privacy checking!
    $_rt = array(
        'search'                       => array(
            "_item_id = {$_post['_1']}"
        ),
        'exclude_jrProfile_quota_keys' => true,
        'ignore_pending'               => true,
        'quota_check'                  => false,
        'order_by'                     => false,
        'limit'                        => 1
    );
    $_rt = jrCore_db_search_items('jrComment', $_rt);
    if (!$_rt || !is_array($_rt) || !isset($_rt['_items'])) {
        return '';
    }
    $_rt = $_rt['_items'][0];

    // Is this comment on a PRIVATE PROFILE?
    if (!jrUser_is_admin()) {
        $prv = $_rt['profile_private'];
        if ($_rt['comment_profile_id'] != $_rt['_profile_id']) {
            $prv = jrCore_db_get_item_key('jrProfile', $_rt['comment_profile_id'], 'profile_private');
        }
        switch (intval($prv)) {
            case 0:
                // Completely private profile
                if (!jrProfile_is_profile_owner($_rt['comment_profile_id'])) {
                    jrUser_not_authorized();
                }
                break;

            case 2:
            case 3:
                // Followers Only
                if (!jrFollower_is_follower($_user['_user_id'], $_rt['comment_profile_id'])) {
                    jrUser_not_authorized();
                }
                break;
        }

        // Is this a comment on a PRIVATE item that the user does not have access to?
        $_ids = jrCore_trigger_event('jrComment', 'private_item_ids', array(), $_rt, $_rt['comment_module']);
        if ($_ids && is_array($_ids) && isset($_ids["{$_rt['comment_module']}"]) && is_array($_ids["{$_rt['comment_module']}"]) && in_array(intval($_rt['comment_item_id']), $_ids["{$_rt['comment_module']}"])) {
            jrUser_not_authorized();
        }
    }
    if (isset($_conf['jrComment_editor']) && $_conf['jrComment_editor'] == 'on') {
        $val = "[quote=\"{$_rt['user_name']}\"]\n" . trim($_rt['comment_text']) . "\n[/quote]\n";
    }
    else {
        $_rp = array('<p>' => '', '</p>' => '');
        $val = "[quote=\"{$_rt['user_name']}\"]\n" . str_replace(array_keys($_rp), $_rp, trim($_rt['comment_text'])) . "\n[/quote]\n\n";
    }
    return $val;
}

//------------------------------
// view_comments
//------------------------------
function view_jrComment_view_comments($_post, $_user, $_conf)
{
    if (!isset($_post['item_module']) || !jrCore_module_is_active($_post['item_module'])) {
        return 'ERROR: invalid module';
    }
    if (!isset($_post['item_id']) || !jrCore_checktype($_post['item_id'], 'number_nz')) {
        return 'ERROR: invalid item_id';
    }
    $_sp = array(
        'search'   => array(
            "comment_item_ckey = {$_post['item_id']}:{$_post['item_module']}:i"
        ),
        'order_by' => array(
            '_item_id' => (isset($_conf['jrComment_direction'])) ? $_conf['jrComment_direction'] : 'desc'
        )
    );
    if (isset($_conf['jrComment_pagebreak']) && jrCore_checktype($_conf['jrComment_pagebreak'], 'number_nz')) {
        $page = 1;
        if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
            $page = (int) $_post['p'];
        }
        $_sp['pagebreak'] = (int) $_conf['jrComment_pagebreak'];
        $_sp['page']      = $page;
    }
    else {
        $_sp['limit'] = (is_numeric($_post['limit'])) ? intval($_post['limit']) : 1000;
    }
    $_sp['jrcore_list_function_call_is_active'] = 1;   // ensures we handle threading if enabled
    if (isset($_post['order_by']{2}) && strpos($_post['order_by'], ' ')) {
        list($key, $val) = explode(' ', $_post['order_by']);
        $key = trim($key);
        $val = trim($val);
        if (strlen($key) > 0 && strlen($val) > 0) {
            $_sp['order_by'] = array($key => $val);
        }
    }
    $_rt = jrCore_db_search_items('jrComment', $_sp);
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
        $out = '';
        jrCore_set_flag('jrprofile_view_is_active', true);
        if (isset($_post['template']) && jrCore_checktype($_post['template'], 'file_name') && $_post['template'] !== 'undefined') {
            if (file_exists(APP_DIR . '/modules/' . $_conf['jrCore_active_skin']) . '/' . $_post['template']) {
                return $out . jrCore_parse_template($_post['template'], $_rt);
            }
        }
        $out .= jrCore_parse_template('item_list.tpl', $_rt, 'jrComment');
        if (isset($_conf['jrComment_pagebreak']) && jrCore_checktype($_conf['jrComment_pagebreak'], 'number_nz')) {
            $out .= jrCore_parse_template('comment_pager.tpl', $_rt, 'jrComment');
        }
        return $out;
    }
    return 'ERROR: Unable to retrieve comments';
}

//------------------------------
// Save comment to datastore
//------------------------------
function view_jrComment_comment_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();

    if (!isset($_post['comment_module']) || !jrCore_module_is_active($_post['comment_module'])) {
        $_res = array('error' => 'invalid comment module or comment module is not active');
        jrCore_json_response($_res);
    }
    $pfx = jrCore_db_get_prefix($_post['comment_module']);
    if (!$pfx) {
        $_res = array('error' => 'module is not setup with a datastore - unable to save comment');
        jrCore_json_response($_res);
    }
    if (!jrCore_checktype($_post['comment_item_id'], 'number_nz')) {
        $_res = array('error' => 'invalid comment_item_id');
        jrCore_json_response($_res);
    }
    if (!jrCore_checktype($_post['comment_profile_id'], 'number_nz')) {
        $_res = array('error' => 'invalid comment_profile_id');
        jrCore_json_response($_res);
    }

    // Check for valid text
    $_ln = jrUser_load_lang_strings();
    if (!isset($_post['comment_text']) || strlen($_post['comment_text']) === 0) {
        $_res = array('error' => $_ln['jrComment'][10]);
        jrCore_json_response($_res);
    }
    // Check for Wait Timer
    if (!jrUser_is_admin() && isset($_SESSION['jrComment_last_post_timer']) && $_SESSION['jrComment_last_post_timer'] > (time() - ($_conf['jrComment_wait_time'] * 60))) {
        $_res = array('error' => $_ln['jrComment'][9] . $_conf['jrComment_wait_time'] . 'm');
        jrCore_json_response($_res);
    }
    // Check for banned words..
    if ($ban = jrCore_run_module_function('jrBanned_is_banned', 'word', $_post['comment_text'])) {
        $_res = array('error' => "{$_ln['jrCore'][67]} " . strip_tags($ban));
        jrCore_json_response($_res);
    }

    // We need to get the TITLE for this item
    $_it = jrCore_db_get_item($_post['comment_module'], $_post['comment_item_id']);
    if (!$_it || !is_array($_it)) {
        $_res = array('error' => "invalid item_id - please try again");
        jrCore_json_response($_res);
    }
    $_SESSION['jrComment_last_post_timer'] = time();

    $_tmp = array(
        'comment_module'       => $_post['comment_module'],
        'comment_item_id'      => $_post['comment_item_id'],
        'comment_profile_id'   => $_post['comment_profile_id'],
        'comment_item_ckey'    => "{$_post['comment_item_id']}:{$_post['comment_module']}:i",
        'comment_profile_ckey' => "{$_post['comment_profile_id']}:{$_post['comment_module']}:p"
    );
    if (isset($_conf['jrComment_editor']) && $_conf['jrComment_editor'] == 'on' || stripos(' ' . $_post['comment_text'], '[code]')) {
        $_tmp['comment_text'] = trim($_post['comment_text']);
    }
    else {
        $_tmp['comment_text'] = jrCore_strip_html(trim($_post['comment_text']));
    }
    if (isset($_conf['jrComment_threading']) && $_conf['jrComment_threading'] == 'on' && isset($_post['comment_parent_id']) && jrCore_checktype($_post['comment_parent_id'], 'number_nz')) {
        $_rt = jrCore_db_get_item('jrComment', $_post['comment_parent_id'], true);
        if ($_rt && is_array($_rt)) {
            $idx = 1;
            $tid = $_post['comment_parent_id'];
            if (isset($_rt['comment_thread_level']) && jrCore_checktype($_rt['comment_thread_level'], 'number_nz')) {
                $idx = ($_rt['comment_thread_level'] + 1);
                $tid = $_rt['comment_thread_id'];
            }
            $_tmp['comment_thread_id']    = (int) $tid;
            $_tmp['comment_parent_id']    = (int) $_post['comment_parent_id'];
            $_tmp['comment_thread_level'] = (int) $idx;
        }
    }

    // Get our comment title
    $curl = false;
    $murl = jrCore_get_module_url($_post['comment_module']);
    switch ($_post['comment_module']) {
        case 'jrUser':
            $_tmp['comment_item_title'] = $_it['user_name'];
            break;
        case 'jrProfile':
            $_tmp['comment_item_title'] = $_it['profile_name'];
            $curl                       = "{$_conf['jrCore_base_url']}/{$_it['profile_url']}";
            break;
        case 'jrAction':
            $_tmp['comment_item_title'] = (isset($_it["{$pfx}_text"])) ? $_it["{$pfx}_text"] : '';
            $curl                       = "{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$murl}/{$_post['comment_item_id']}/" . jrCore_url_string($_tmp['comment_item_title']);
            break;
        default:
            $_tmp['comment_item_title'] = (isset($_it["{$pfx}_title"])) ? $_it["{$pfx}_title"] : '';
            if (isset($_it["{$pfx}_title_url"])) {
                $curl = "{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$murl}/{$_post['comment_item_id']}/" . $_it["{$pfx}_title_url"];
            }
            else {
                $curl = "{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$murl}/{$_post['comment_item_id']}";
            }
            break;
    }

    // When leaving a comment on another profile, we must use our HOME ID
    $id = jrCore_db_create_item('jrComment', $_tmp, array('_profile_id' => jrUser_get_profile_home_key('_profile_id')));
    if (!$id || !jrCore_checktype($id, 'number_nz')) {
        $_res = array('error' => $_ln['jrComment'][8]);
        jrCore_json_response($_res);
    }

    // If this comment is on a PRIVATE item, store the comment ID here
    if ($pfx = jrCore_db_get_prefix($_post['comment_module'])) {
        if ((isset($_it['group_private']) && $_it['group_private'] == 'on') || (isset($_it["{$pfx}_private"]) && $_it["{$pfx}_private"] == 'on')) {
            jrComment_create_private_id_entry($_it['_profile_id'], $id);
        }
    }

    $_tmp['_item_id'] = $id;

    // Increment number of comments on item
    jrCore_db_increment_key($_post['comment_module'], $_post['comment_item_id'], "{$pfx}_comment_count", 1);

    // Increment number of comments for profile
    if (jrUser_get_profile_home_key('_profile_id') != $_it['_profile_id']) {
        jrCore_db_increment_key('jrProfile', $_post['comment_profile_id'], 'profile_jrComment_home_item_count', 1);
    }

    // Add comment id to $curl
    $curl .= "#cm{$id}";

    // Add to actions - give the module the comment is being left on an opportunity to cancel
    $_it = jrCore_trigger_event('jrComment', 'add_to_timeline', $_it, $_tmp, $_post['comment_module']);
    if (!isset($_it['add_to_timeline']) || $_it['add_to_timeline'] == true) {

        $_sav = array(
            'action_original_module'  => $_post['comment_module'],
            'action_original_item_id' => $_post['comment_item_id']
        );
        $aid  = jrCore_run_module_function('jrAction_save', 'create', 'jrComment', $id, $_sav, false, jrUser_get_profile_home_key('_profile_id'), $_post['comment_profile_id']);
        if ($aid && $aid > 0) {
            // See if this comment is on a PRIVATE item
            $_ids = jrCore_trigger_event('jrComment', 'private_item_ids', array(), $_tmp, $_post['comment_module']);
            if (!$_ids || !is_array($_ids) || !is_array($_ids["{$_post['comment_module']}"]) || !in_array($_post['comment_item_id'], $_ids["{$_post['comment_module']}"])) {
                jrCore_run_module_function('jrAction_process_mentions', $_tmp['comment_text'], $aid);
            }
        }

    }

    // Send user notifications
    $_owners = jrProfile_get_owner_info($_post['comment_profile_id']);
    if ($_owners && is_array($_owners)) {
        $_rp = array(
            'system_name'        => $_conf['jrCore_system_name'],
            'comment_user_name'  => $_user['user_name'],
            'comment_text'       => $_tmp['comment_text'],
            'comment_item_url'   => $curl,
            'comment_item_title' => str_replace('@', '', $_tmp['comment_item_title']),
            'comment_item_id'    => $id
        );
        list($sub, $msg) = jrCore_parse_email_templates('jrComment', 'new_comment', $_rp);
        foreach ($_owners as $_o) {
            // "0" is from_user_id - 0 is the "system user"
            if ($_o['_user_id'] != $_user['_user_id']) {
                jrUser_notify($_o['_user_id'], 0, 'jrComment', 'new_comment', $sub, $msg);
            }
        }
    }

    // if threaded, send notification to parent comment user (if not self)
    if (isset($_rt) && is_array($_rt) && $_rt['_user_id'] != $_user['_user_id']) {
        $_rp = array(
            'system_name'        => $_conf['jrCore_system_name'],
            'comment_user_name'  => $_user['user_name'],
            'comment_text'       => $_tmp['comment_text'],
            'comment_item_url'   => $curl,
            'comment_item_title' => $_tmp['comment_item_title'],
            'comment_item_id'    => $id
        );
        list($sub, $msg) = jrCore_parse_email_templates('jrComment', 'new_reply', $_rp);
        jrUser_notify($_rt['_user_id'], 0, 'jrComment', 'new_reply', $sub, $msg);
    }

    // Reset caches
    jrUser_reset_cache($_user['_user_id']);
    jrProfile_reset_cache(jrUser_get_profile_home_key('_profile_id'));
    jrProfile_reset_cache($_post['comment_profile_id'], $_post['comment_module']);

    // See if pending is on - we need to let the user know their comment is pending review by an admin user
    $_rt = jrCore_db_get_item('jrComment', $id, true);
    if (isset($_rt['comment_pending']) && $_rt['comment_pending'] == '1') {
        $_res = array('success' => $_ln['jrComment'][15]);
    }
    else {
        $_res = array('success' => $_ln['jrComment'][7]);
    }
    $_res['item_id'] = $id;
    $hl              = 'off';
    if ($_conf['jrComment_editor'] == 'on' || $_conf['jrComment_direction'] == 'desc') {
        $hl = 'on';
    }
    $_res['highlight'] = $hl;
    jrCore_json_response($_res);
}

//------------------------------
// delete
//------------------------------
function view_jrComment_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_validate_location_url();

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_form_result('referrer');
    }
    $_rt = jrCore_db_get_item('jrComment', $_post['id']);

    // Make sure the calling user has permission to delete this item
    if (!jrUser_can_edit_item($_rt)) {
        // They don't own this comment - see if we are configured in their
        // quota to allow profile owners to delete comments
        if (!isset($_user['quota_jrComment_profile_delete']) || $_user['quota_jrComment_profile_delete'] != 'on') {
            jrUser_not_authorized();
        }
    }
    // See if this is a LOCKED ITEM
    if (!jrUser_is_admin() && isset($_rt['comment_locked']) && $_rt['comment_locked'] == '1') {
        jrUser_not_authorized();
    }

    // See if SAVE THREADS is on - if it is, we don't delete the comment if it has replies
    if (isset($_conf['jrComment_save_thread']) && $_conf['jrComment_save_thread'] == 'on') {

        // If this comment has replies..
        $_cm = jrCore_db_get_multiple_items_by_key('jrComment', 'comment_parent_id', $_post['id']);
        if ($_cm && is_array($_cm)) {
            $_ln = jrUser_load_lang_strings('en-US');
            $_up = array(
                'comment_text'   => $_ln['jrComment'][19],
                'comment_locked' => 1
            );
            jrCore_db_update_item('jrComment', $_post['id'], $_up);
        }
        else {
            // Delete item
            jrCore_db_delete_item('jrComment', $_post['id']);
            if ($pfx = jrCore_db_get_prefix($_rt['comment_module'])) {
                jrCore_db_decrement_key($_rt['comment_module'], $_rt['comment_item_id'], "{$pfx}_comment_count", 1);
            }
        }
    }
    else {
        // Delete comment and all replies
        // Find any comments that are in our comment thread
        $_pd = array("{$_post['id']}" => $_rt['comment_parent_id']);
        $_dn = array($_post['id'] => 1);
        $i   = 0;
        while (true) {
            $_sp = array(
                'search'        => array(
                    "comment_parent_id in " . implode(',', array_keys($_pd)),
                    "_item_id not_in " . implode(',', array_keys($_dn))
                ),
                'return_keys'   => array('_item_id', 'comment_parent_id'),
                'skip_triggers' => true,
                'limit'         => 1000,
                'no_cache'      => true
            );
            $_sp = jrCore_db_search_items('jrComment', $_sp);
            if ($_sp && is_array($_sp) && isset($_sp['_items']) && is_array($_sp['_items'])) {
                foreach ($_sp['_items'] as $_cm) {
                    $_dn["{$_cm['_item_id']}"] = 1;
                    $_pd["{$_cm['_item_id']}"] = $_cm['comment_parent_id'];
                    $i++;
                }
                if ($i > 25) {
                    // fail safe...
                    break;
                }
            }
            else {
                break;
            }
        }
        if ($_dn && count($_dn) > 0) {
            jrCore_db_delete_multiple_items('jrComment', array_keys($_dn));
            if ($pfx = jrCore_db_get_prefix($_rt['comment_module'])) {
                jrCore_db_decrement_key($_rt['comment_module'], $_rt['comment_item_id'], "{$pfx}_comment_count", count($_dn));
            }
        }
    }

    // Reset Cache
    jrProfile_reset_cache($_rt['comment_profile_id'], 'jrComment');
    if ($_rt['comment_module'] != 'jrComment') {
        jrProfile_reset_cache($_rt['comment_profile_id'], $_rt['comment_module']);
    }
    jrUser_reset_cache($_user['_user_id']);
    jrCore_form_result('referrer');
}

//------------------------------
// update
//------------------------------
function view_jrComment_update($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrComment');

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 20);
    }
    $_rt = jrCore_db_get_item('jrComment', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', 21);
    }
    // Make sure the calling user has permission to edit this item
    if (!jrComment_user_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Save our URL to go back to on completion of editing
    jrCore_create_memory_url("comment_edit_{$_post['id']}");

    // Start output
    jrCore_page_banner(23);

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

    // Comment Text
    if (isset($_conf['jrComment_editor']) && $_conf['jrComment_editor'] == 'on') {
        $_tmp = array(
            'name'     => 'comment_text',
            'label'    => 'Comment Text',
            'help'     => 'Update the comment text to how you want it.',
            'type'     => 'editor',
            'validate' => 'allowed_html',
            'required' => true
        );
        jrCore_form_field_create($_tmp);
    }
    else {
        $_tmp = array(
            'name'     => 'comment_text',
            'label'    => 'Comment Text',
            'help'     => 'Update the comment text to how you want it.',
            'type'     => 'textarea',
            'validate' => 'printable',
            'required' => true
        );
        $rows = substr_count($_rt['comment_text'], "\n");
        $rows += ceil(strlen($_rt['comment_text']) / 75);
        if ($rows > 6) {
            $_tmp['style'] = 'height: unset';
            $_tmp['rows']  = $rows;
        }
        jrCore_form_field_create($_tmp);
    }

    if (jrUser_is_admin() || jrUser_get_profile_home_key('quota_jrComment_attachments') == 'on') {

        // File Attachment
        $_tmp = array(
            'name'          => 'comment_file',
            'label'         => 31,
            'help'          => 32,
            'text'          => 33,
            'type'          => 'file',
            'extensions'    => jrUser_get_profile_home_key('quota_jrComment_allowed_file_types'),
            'value'         => $_rt,
            'order'         => 4,
            'max'           => (isset($_user['quota_jrCore_max_upload_size'])) ? (int) $_user['quota_jrCore_max_upload_size'] : 2097152,
            'multiple'      => true,
            'form_designer' => false
        );
        jrCore_form_field_create($_tmp);
    }
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrComment_update_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();

    // Validate all incoming posted data
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrComment');

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 20);
        jrCore_form_result();
    }

    // Get data
    $_rt = jrCore_db_get_item('jrComment', $_post['id']);
    if (!$_rt || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 20);
        jrCore_form_result();
    }

    // Make sure the calling user has permission to edit this item
    if (!jrComment_user_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrComment', 'update', $_post);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrComment', $_post['id'], $_sv);

    // Save any uploaded media file
    // If this is an ADMIN user modifying a post by another user, we need to make
    // sure the profile_id is set to the proper profile_id
    if (jrUser_is_admin() || jrUser_get_profile_home_key('quota_jrComment_attachments') == 'on') {
        if (jrUser_is_admin() && $_rt['_profile_id'] != jrUser_get_profile_home_key('_profile_id')) {
            jrCore_save_all_media_files('jrComment', 'update', $_rt['_profile_id'], $_post['id'], $_rt);
        }
        else {
            jrCore_save_all_media_files('jrComment', 'update', jrUser_get_profile_home_key('_profile_id'), $_post['id'], $_rt);
        }
    }

    // Get URL we came from
    $url = jrCore_get_memory_url("comment_edit_{$_post['id']}");
    jrCore_delete_memory_url("comment_edit_{$_post['id']}");

    jrCore_form_delete_session();
    jrProfile_reset_cache($_rt['comment_profile_id'], 'jrComment');
    jrProfile_reset_cache($_rt['comment_profile_id'], $_rt['comment_module']);
    jrUser_reset_cache($_user['_user_id']);
    jrCore_form_result("{$url}#c{$_post['id']}");
}
