<?php
/**
 * Jamroom Simple Chat module
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
 * @copyright 2016 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//------------------------------
// profile (redirect)
//------------------------------
function view_jrChat_profile($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('error', 'invalid user id');
    }
    $_us = jrCore_db_get_item('jrUser', $_post['_1'], true, false);
    if (!$_us || !is_array($_us)) {
        jrCore_notice_page('error', 'invalid user id - data not found');
    }
    $_pr = jrCore_db_get_item('jrProfile', $_us['_profile_id'], true, false);
    if (!$_pr || !is_array($_pr)) {
        jrCore_notice_page('error', 'invalid user id - profile data not found');
    }
    jrCore_location("{$_conf['jrCore_base_url']}/{$_pr['profile_url']}");
}

//------------------------------
// new_message
//------------------------------
function view_jrChat_new_message($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        $_rs = array('error' => 'Chat requires you to be logged in');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['room_id']) || !jrCore_checktype($_post['room_id'], 'number_nz')) {
        $_rs = array('error' => 'invalid room_id');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['unique']) || strlen($_post['unique']) === 0) {
        $_rs = array('error' => 'invalid unique post value');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['message']) || strlen($_post['message']) === 0) {
        $_rs = array('error' => 'invalid message content');
        jrCore_json_response($_rs);
    }
    if (strlen($_post['message']) > 64000) {
        // Too big
        $_rs = array('error' => 'message is too large');
        jrCore_json_response($_rs);
    }

    // Make sure user has access
    $rid = (int) $_post['room_id'];
    if (jrUser_is_admin()) {

        // Admins always have access - we set slot_id to 1 here so they we
        // just fall through and have access (slot_id is not used but just checked)
        $_ac = array('slot_id' => 1);

    }
    else {

        // Regular user - must have access to room
        if (!$_ac = jrChat_user_can_access_room($rid, $_user['_user_id'])) {
            // If this is a PUBLIC ROOM, we can add their slot
            $_rm = jrChat_get_room_info_by_id($rid);
            if (!$_rm || !is_array($_rm)) {
                $_rs = array('error' => 'invalid room_id - no data found');
                jrCore_json_response($_rs);
            }
            if (isset($_rm['room_public']) && $_rm['room_public'] == 1) {
                // This is a PUBLIC room, and we have not claimed a slot yet - setup
                $tbl = jrCore_db_table_name('jrChat', 'slot');
                $req = "INSERT IGNORE INTO {$tbl} (slot_room_id, slot_user_id) VALUES ('{$rid}', '{$_user['_user_id']}')";
                $sid = jrCore_db_query($req, 'INSERT_ID');
                if ($sid && jrCore_checktype($sid, 'number_nz')) {
                    $_ac = array(
                        'room_id'     => $rid,
                        'room_public' => 1,
                        'slot_id'     => $sid
                    );
                }
                else {
                    $_rs = array('error' => 'you do not have permission to access that chat room (500)');
                    jrCore_json_response($_rs);
                }
            }
            else {
                $_rs = array('error' => 'you do not have permission to access that chat room (1)');
                jrCore_json_response($_rs);
            }
        }
    }

    if (isset($_ac['room_public']) && $_ac['room_public'] == 1 && (!isset($_ac['slot_id']) || $_ac['slot_id'] == 0)) {
        // This is a PUBLIC room, and we have not claimed a slot yet - setup
        $tbl = jrCore_db_table_name('jrChat', 'slot');
        $req = "INSERT IGNORE INTO {$tbl} (slot_room_id, slot_user_id) VALUES ('{$rid}', '{$_user['_user_id']}')";
        $sid = jrCore_db_query($req, 'INSERT_ID');
        if ($sid && jrCore_checktype($sid, 'number_nz')) {
            $_ac['slot_id'] = $sid;
        }
    }

    // have we already seen this post?
    if (isset($_SESSION['jrchat_unique_post_id']) && $_SESSION['jrchat_unique_post_id'] == $_post['unique']) {
        // This is a duplicate - do nothing
        $_rs = array('duplicate' => '1');
        jrCore_json_response($_rs);
    }
    $_SESSION['jrchat_unique_post_id'] = $_post['unique'];

    // Let other modules know we're doing a new message
    $_post = jrCore_trigger_event('jrChat', 'create_message', $_post);

    // Set active room
    jrChat_set_user_active_room_id($rid);

    // Cleanup message
    if (stripos($_post['message'], 'code]')) {
        $msg = trim(jrCore_strip_emoji(jrChat_cleanup_message($_post['message'])));
    }
    else {
        $msg = trim(jrCore_entity_string(jrCore_strip_emoji(jrChat_cleanup_message($_post['message']))));
    }

    // Check for actions
    $msg = jrChat_process_action($msg);

    // Save message
    if (strlen($msg) > 0 && $mid = jrChat_create_message($rid, $_user['_user_id'], $msg)) {
        $rid = (int) $_post['room_id'];
        $uid = (int) $_user['_user_id'];
        $tbl = jrCore_db_table_name('jrChat', 'typing');
        $req = "INSERT INTO {$tbl} (t_room_id, t_user_id, t_time) VALUES ('{$rid}', '{$uid}', (UNIX_TIMESTAMP() - 120)) ON DUPLICATE KEY UPDATE t_time = (UNIX_TIMESTAMP() - 120)";
        jrCore_db_query($req);
        jrChat_update_room_time($rid);
        $_rs = array('ok' => '1');
        jrCore_json_response($_rs);
    }
    $_rs = array('error' => 'an error was encountered saving the message - please try again');
    jrCore_json_response($_rs);
}

//------------------------------
// delete_message
//------------------------------
function view_jrChat_delete_message($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        $_rs = array('error' => 'Chat requires you to be logged in');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        $_rs = array('error' => 'invalid message id');
        jrCore_json_response($_rs);
    }
    $mid = (int) $_post['id'];
    $tbl = jrCore_db_table_name('jrChat', 'message');
    $req = "SELECT * FROM {$tbl} WHERE msg_id = '{$mid}'";
    $_ms = jrCore_db_query($req, 'SINGLE');
    if (!$_ms || !is_array($_ms)) {
        $_rs = array('ok' => 1);
        jrCore_json_response($_rs);
    }
    if (jrUser_is_admin()) {
        $req = "DELETE FROM {$tbl} WHERE msg_id = '{$mid}'";
    }
    else {
        $uid = (int) $_user['_user_id'];
        $req = "DELETE FROM {$tbl} WHERE msg_id = '{$mid}' AND msg_user_id = '{$uid}'";
    }
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt > 0) {

        // Get rid of any attached file as well...
        if ($_fl = jrCore_db_get_item_by_key('jrChat', 'chat_message_id', $mid)) {
            jrCore_db_delete_item('jrChat', $_fl['_item_id']);
        }

        // Create new DELETE message
        $rid = (int) $_ms['msg_room_id'];
        $uid = (int) $_ms['msg_user_id'];
        $msg = "~delmsg:{$mid}~{";
        $req = "INSERT INTO {$tbl} (msg_room_id, msg_user_id, msg_created, msg_content) VALUES ('{$rid}', '{$uid}', UNIX_TIMESTAMP(), '{$msg}')";
        jrCore_db_query($req);

        jrCore_trigger_event('jrChat', 'delete_message', $_post);
        $_rs = array('ok' => 1);
    }
    else {
        $_rs = array('error' => 'unable to delete message');
    }
    jrCore_json_response($_rs);
}

//------------------------------
// get_user_rooms
//------------------------------
function view_jrChat_get_user_rooms($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        jrCore_db_close();
        echo 'ERROR: Chat requires you to be logged in';
        exit;
    }
    $uid = (int) $_user['_user_id'];
    $tbl = jrCore_db_table_name('jrChat', 'room');
    $tbs = jrCore_db_table_name('jrChat', 'slot');
    $req = "SELECT r.* FROM {$tbs} s LEFT JOIN {$tbl} r ON r.room_id = s.slot_room_id WHERE (s.slot_user_id = '{$uid}' OR r.room_public = 1) ORDER BY r.room_public DESC, r.room_private ASC, r.room_title ASC";
    $_rm = jrCore_db_query($req, 'room_id');
    if ($_rm && is_array($_rm)) {
        foreach ($_rm as $rid => $_r) {
            if ($_r['room_private'] == 1) {
                if ($_r['room_user_id'] != $uid) {
                    // This is a private room - the TITLE will be the OTHER user in chat
                    $_rm[$rid]['room_title'] = jrChat_get_private_room_title($_r['room_id'], $uid);
                }
                else {
                    $_rm[$rid]['room_title'] = '@' . $_r['room_title'];
                }
            }
        }
        return jrCore_parse_template('rooms.tpl', array('_rooms' => $_rm), 'jrChat');
    }
    return '';
}

//------------------------------
// transcript
//------------------------------
function view_jrChat_transcript($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        jrCore_notice('ERROR', 'Chat requires you to be logged in');
    }
    if (!isset($_post['room_id']) || !jrCore_checktype($_post['room_id'], 'number_nz')) {
        // See if we know the active room id
        if ($id = jrChat_get_user_active_room_id()) {
            $_post['room_id'] = (int) $id;
        }
        else {
            jrCore_notice('ERROR', 'invalid room_id');
        }
    }
    if (!jrUser_is_admin() && !jrChat_user_can_access_room($_post['room_id'], $_user['_user_id'])) {
        jrCore_notice('ERROR', 'you do not have permission to access that chat room');
    }

    // Download all messages
    @ini_set('memory_limit', '512M');
    $rid = (int) $_post['room_id'];
    $tbl = jrCore_db_table_name('jrChat', 'message');
    $req = "SELECT msg_id AS i, msg_user_id AS u, msg_created AS t, msg_content as c FROM {$tbl} WHERE msg_room_id = '{$rid}' ORDER BY t ASC";
    $_rt = jrCore_db_query($req, 'i');

    if ($_rt && is_array($_rt)) {
        $_ui = array();
        foreach ($_rt as $k => $_m) {
            $uid       = (int) $_m['u'];
            $_ui[$uid] = $uid;
        }
        if (count($_ui) > 0) {
            $_us = jrCore_db_get_multiple_items('jrUser', $_ui, array('_user_id', 'user_name'));
            if ($_us && is_array($_us)) {
                $_un = array();
                foreach ($_us as $_u) {
                    $_un["{$_u['_user_id']}"] = $_u['user_name'];
                }
                foreach ($_rt as $k => $_m) {
                    $_rt[$k]['n'] = $_un["{$_m['u']}"];
                }
            }
        }

        // Send out file
        $out = '';
        foreach ($_rt as $k => $_m) {
            if (strpos($_m['c'], '~{')) {
                $_m['c'] = substr($_m['c'], strpos($_m['c'], '~{') + 2);
            }
            $out .= jrCore_format_time($_m['t']) . " {$_m['n']}: " . jrCore_replace_emoji($_m['c']) . "<br>";
        }
        $_rm = jrChat_get_room_info_by_id($rid);
        $cdr = jrCore_get_module_cache_dir('jrChat');
        $nam = "{$cdr}/" . jrCore_url_string($_rm['room_title']) . '-chat-transcript.html';
        jrCore_write_to_file($nam, $out);
        jrCore_send_download_file($nam);
        exit;
    }
    jrCore_notice('ERROR', 'no chat messages found in room');
}

//------------------------------
// messages
//------------------------------
function view_jrChat_messages($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        $_rs = array('error' => 'Chat requires you to be logged in');
        jrCore_json_response($_rs);
    }

    if (!isset($_post['room_id']) || !jrCore_checktype($_post['room_id'], 'number_nz')) {
        // See if we know the active room id
        if ($id = jrChat_get_user_active_room_id()) {
            $_post['room_id'] = (int) $id;
        }
        else {
            $_rs = array('error' => 'invalid room_id (messages)');
            jrCore_json_response($_rs);
        }
    }
    if (!jrUser_is_admin() && !jrChat_user_can_access_room($_post['room_id'], $_user['_user_id'])) {
        $_rs = array('rid' => 0, 'error' => 'you do not have permission to access that chat room (2)');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['before_id']) || !jrCore_checktype($_post['before_id'], 'number_nz')) {
        $_post['before_id'] = 0; // get latest
    }
    jrChat_set_user_active_room_id($_post['room_id']);

    // Get all messages
    $sst = null;
    if (isset($_post['ss']) && strlen($_post['ss']) > 0) {
        $sst = $_post['ss'];
    }
    $_rs = jrChat_get_messages($_post['room_id'], $_post['before_id'], $sst);
    // i = msg_id
    // u = user_id
    // m = image URL (for gravatar)
    // r = room_id
    // t = date created
    // c = content
    // n = user name
    $bid = false;
    $lid = 0;
    if ($_rs && is_array($_rs)) {
        jrCore_set_flag('jrurlscan_expand_url_cards', 1);
        foreach ($_rs as $k => $m) {
            if (!$bid) {
                $bid = (int) $m['i'];
            }
            $_rs[$k]['c'] = jrChat_replace_lightbox_tag(smarty_modifier_jrCore_format_string(jrCore_replace_emoji($m['c']), $_user['profile_quota_id'], null, 'html'));
            if (!is_null($sst)) {
                $_rs[$k]['c'] = jrCore_hilight_string($_rs[$k]['c'], $sst);
            }
            $lid = $m['i'];
        }
    }
    jrChat_set_slot_last_id($_user['_user_id'], $_post['room_id'], $lid);
    $_rp = array(
        'uid' => $_user['_user_id'],
        'new' => $_rs,   // Chat messages
        'bid' => $bid,   // "Before ID" - used by the client when getting older messages
        'lid' => $lid,
        'rid' => $_post['room_id'],
        'cnt' => jrChat_get_room_user_count($_post['room_id']),
        'adm' => (jrUser_is_admin()) ? 1 : 0
    );
    jrCore_json_response($_rp, true, false);
}

//------------------------------
// new_messages
//------------------------------
function view_jrChat_new_messages($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        $_rs = array('error' => 'Chat requires you to be logged in');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['room_id']) || !jrCore_checktype($_post['room_id'], 'number_nz')) {
        $_rs = array('error' => 'invalid room_id');
        jrCore_json_response($_rs);
    }
    if (!jrUser_is_admin() && !jrChat_user_can_access_room($_post['room_id'], $_user['_user_id'])) {
        $_rs = array('rid' => 0, 'error' => 'you do not have permission to access that chat room (3)');
        jrCore_json_response($_rs);
    }
    $rid = (int) $_post['room_id'];
    if (!isset($_post['last_id']) || !jrCore_checktype($_post['last_id'], 'number_nn')) {
        $_rs = array('error' => 'invalid last_id');
        jrCore_json_response($_rs);
    }
    // Get all NEW messages for this user
    $_rs = jrChat_get_new_messages($rid, $_post['last_id']);
    // Each message in $_rs will contain:
    // i = msg_id
    // u = user_id
    // m = image URL (for gravatar)
    // r = room_id
    // t = date created
    // c = content
    // n = user name
    $lid = 0;   // Last ID holder
    $tnm = 0;   // Total NEW messages
    $_nw = array();
    if ($_rs && is_array($_rs)) {
        jrCore_set_flag('jrurlscan_expand_url_cards', 1);
        foreach ($_rs as $room_id => $_m) {
            if ($room_id == $rid) {
                // This is out active room - format messages
                foreach ($_m as $k => $m) {
                    // Format messages for the room we are viewing
                    $_rs[$room_id][$k]['c'] = jrChat_replace_lightbox_tag(smarty_modifier_jrCore_format_string(jrCore_replace_emoji($m['c']), $_user['profile_quota_id'], null, 'html'));
                    $lid                    = (int) $m['i'];
                    if ($lid > $_post['last_id']) {
                        $tnm++;
                    }
                }
            }
            else {
                // We have new messages in another room...
                $_nw[$room_id] = count($_m);
                $tnm           += $_nw[$room_id];
            }
        }
    }
    if ($tnm > 0 && $lid > 0) {
        // We had new messages in this room - update last message id for slot
        jrChat_set_slot_last_id($_user['_user_id'], $_post['room_id'], $lid);
    }
    $_rp = array(
        'cnt'    => jrChat_get_room_user_count($rid),
        'uid'    => $_user['_user_id'],
        'new'    => (isset($_rs[$rid])) ? $_rs[$rid] : '',
        'lid'    => ($lid > 0) ? $lid : $_post['last_id'],
        'live'   => jrChat_get_typing_users($rid),
        'other'  => $_nw,
        'tnm'    => $tnm,
        'notify' => (isset($_user['user_chat_notifications']) && jrCore_checktype($_user['user_chat_notifications'], 'onoff')) ? $_user['user_chat_notifications'] : 'on',
        'sound'  => 'off'
    );

    // See if sounds are enabled.  If they are, we need to keep track of the LAST new message
    // sent out so we only play a new sound message in ONE tab if they have multiple chat windows open
    if ($lid > 0 && isset($_user['user_chat_message_sound']) && $_user['user_chat_message_sound'] == 'on') {
        // This user has new message sounds enabled
        if (jrChat_get_last_message_id($_user['_user_id']) != $lid) {
            $_rp['sound'] = 'on';
        }
        jrChat_set_last_message_id($_user['_user_id'], $lid);
    }

    jrCore_json_response($_rp, true, false);
}

//------------------------------
// user_config
//------------------------------
function view_jrChat_user_config($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        jrCore_db_close();
        echo 'ERROR: Chat requires you to be logged in';
        exit;
    }

    $_ln = jrUser_load_lang_strings();
    jrCore_page_set_no_header_or_footer();
    jrCore_page_section_header($_ln['jrChat'][5]);

    $_usr = jrCore_db_get_item('jrUser', $_user['_user_id'], true, true);

    // Form init
    $_tmp = array(
        'submit_value' => 6,
        'cancel'       => false,
        'onclick'      => 'jrChat_save_user_settings();return false;'
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'     => 'notifications',
        'label'    => 7,
        'help'     => 8,
        'value'    => (isset($_usr['user_chat_notifications'])) ? $_usr['user_chat_notifications'] : 'on',
        'type'     => 'checkbox',
        'required' => true,
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'message_sound',
        'label'    => 50,
        'help'     => 51,
        'value'    => (isset($_usr['user_chat_message_sound'])) ? $_usr['user_chat_message_sound'] : 'off',
        'type'     => 'checkbox',
        'required' => true,
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    $_opt = array(
        'auto'    => $_ln['jrChat'][41],
        'offline' => $_ln['jrChat'][43]
    );
    $_tmp = array(
        'name'     => 'online_status',
        'label'    => 39,
        'help'     => 40,
        'default'  => 'automatic',
        'type'     => 'radio',
        'options'  => $_opt,
        'value'    => (isset($_usr['user_chat_online_status'])) ? $_usr['user_chat_online_status'] : 'auto',
        'layout'   => 'vertical',
        'required' => true,
        'validate' => 'core_string'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// user_config_save
//------------------------------
function view_jrChat_user_config_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    switch ($_post['online_status']) {
        case 'auto':
        case 'online':
        case 'offline':
            break;
        default:
            $_rs = array('error' => 'invalid value for online_status');
            jrCore_json_response($_rs);
            break;
    }
    $_up = array(
        'user_chat_notifications' => (isset($_post['notifications']) && $_post['notifications'] == 'on') ? 'on' : 'off',
        'user_chat_message_sound' => (isset($_post['message_sound']) && $_post['message_sound'] == 'on') ? 'on' : 'off',
        'user_chat_online_status' => (isset($_post['online_status'])) ? $_post['online_status'] : 'auto'
    );
    if (jrCore_db_update_item('jrUser', $_user['_user_id'], $_up)) {
        jrCore_json_response(array('ok' => 1));
    }
    $_rs = array('error' => 'an error was encountered saving the settings - please try again');
    jrCore_json_response($_rs);
}

//------------------------------
// users
//------------------------------
function view_jrChat_users($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        jrCore_db_close();
        echo 'ERROR: Chat requires you to be logged in';
        exit;
    }
    if (!isset($_post['room_id']) || !jrCore_checktype($_post['room_id'], 'number_nz')) {
        jrCore_db_close();
        echo 'ERROR: invalid room_id';
        exit;
    }
    if (!jrUser_is_admin() && !jrChat_user_can_access_room($_post['room_id'], $_user['_user_id'])) {
        jrCore_db_close();
        echo 'ERROR: you do not have permission to access that chat room (4)';
        exit;
    }
    $rid = (int) $_post['room_id'];
    $_rm = jrChat_get_room_info_by_id($rid);
    if (!$_rm || !is_array($_rm)) {
        jrCore_db_close();
        echo 'ERROR: invalid room_id (2)';
        exit;
    }
    $_rp = jrChat_get_room_users($rid);
    if ($_rp && is_array($_rp)) {
        $_ui = array();
        foreach ($_rp as $_u) {
            $_ui[] = (int) $_u['_user_id'];
        }
        if (count($_ui) > 0) {

            // Get online user info
            // $_ss will contain user_id => last update for each user
            // active in the last 3600 seconds (1 hour)
            $_ss = jrUser_session_online_user_ids(3600, $_ui);

            // Are any of them typing?
            $_tt = false;
            if ($_ss && is_array($_ss)) {
                $tbl = jrCore_db_table_name('jrChat', 'typing');
                $req = "SELECT t_user_id AS u, UNIX_TIMESTAMP() - t_time AS t FROM {$tbl} WHERE t_room_id = '{$rid}'";
                $_tt = jrCore_db_query($req, 'u', false, 't');
            }

            // Get users online status
            $_ol = array();
            $_ia = array();
            foreach ($_rp as $k => $_u) {
                $uid = (int) $_u['_user_id'];
                if (isset($_ss[$uid])) {
                    if (!isset($_u['user_chat_online_status'])) {
                        $_u['user_chat_online_status'] = 'auto';
                    }
                    switch ($_u['user_chat_online_status']) {

                        case 'auto':
                        case 'online':
                            // When is the last time they were active in this room?
                            if ($_tt && isset($_tt[$uid]) && $_tt[$uid] > 0) {
                                if ($_tt[$uid] < 1200) {
                                    // This user has recently been active in this room
                                    $_u['user_chat_online_current_status'] = 'online';
                                    $_ol[]                                 = $_u;
                                    unset($_rp[$k]);
                                }
                                elseif ($_tt[$uid] < 3600 || isset($_ss[$uid]) && $_ss[$uid] > (time() - 3600)) {
                                    // This user has recently been active in this room OR on this site
                                    $_u['user_chat_online_current_status'] = 'inactive';
                                    $_ia[]                                 = $_u;
                                    unset($_rp[$k]);
                                }
                                else {
                                    // User is not online
                                    $_rp[$k]['user_chat_online_current_status'] = 'offline';
                                }
                            }
                            elseif (isset($_ss[$uid]) && $_ss[$uid] > (time() - 3600)) {
                                // User is NOT active in the room but is active on the site
                                $_u['user_chat_online_current_status'] = 'inactive';
                                $_ia[]                                 = $_u;
                                unset($_rp[$k]);
                            }
                            else {
                                // User is offline
                                $_rp[$k]['user_chat_online_current_status'] = 'offline';
                            }
                            break;

                        case 'offline':
                            $_rp[$k]['user_chat_online_current_status'] = 'offline';
                            break;

                        case 'inactive':
                            $_u['user_chat_online_current_status'] = 'inactive';
                            $_ia[]                                 = $_u;
                            unset($_rp[$k]);
                            break;

                    }
                }
                else {
                    // No session - offline
                    $_rp[$k]['user_chat_online_current_status'] = 'offline';
                }
            }
            $_rp = array_merge($_ol, $_ia, $_rp);
        }
    }

    $_ln = jrUser_load_lang_strings();
    jrCore_page_set_no_header_or_footer();
    jrCore_page_section_header($_ln['jrChat'][9]);

    $dat = array();
    jrCore_page_table_header($dat);

    if ($_rp && is_array($_rp)) {

        // Get our icon sizes
        $size = jrCore_get_skin_icon_size();
        $pass = jrCore_get_option_image('pass', $_ln['jrChat'][42]);
        $fail = jrCore_get_option_image('fail', $_ln['jrChat'][43]);
        $warn = jrCore_get_option_image('warning', $_ln['jrChat'][44]);

        foreach ($_rp as $k => $_u) {
            $dat             = array();
            $_im             = array(
                'crop'   => 'portrait',
                'width'  => $size,
                'height' => $size,
                'alt'    => 'img',
                'title'  => 'img',
                '_v'     => (isset($_u['user_image_time']) && $_u['user_image_time'] > 0) ? $_u['user_image_time'] : 0
            );
            $dat[1]['title'] = jrImage_get_image_src('jrUser', 'user_image', $_u['_user_id'], 'small', $_im);
            $dat[1]['class'] = '" style="width:2%';
            if ($_rm['room_user_id'] == $_u['_user_id']) {
                $dat[2]['title'] = $_u['user_name'] . ' (room admin)';
            }
            else {
                $dat[2]['title'] = $_u['user_name'];
            }
            $dat[2]['class'] = '" style="width:81%';
            switch ($_u['user_chat_online_current_status']) {
                case 'online':
                    $dat[3]['title'] = $pass;
                    break;
                case 'offline':
                    $dat[3]['title'] = $fail;
                    break;
                case 'inactive':
                    $dat[3]['title'] = $warn;
                    break;
            }
            $dat[3]['class'] = 'center" style="width:15%';
            if ($_u['_user_id'] == $_rm['room_user_id']) {
                // We are the room owner - no remove
                $dat[4]['title'] = jrCore_page_button("r{$k}", 'X', 'disabled');
            }
            else {
                if ($_rm['room_public'] == 1) {
                    // This is a PUBLIC chat room - only admin users can remove (block) a user from a public chat
                    if (jrUser_is_admin()) {
                        $dat[4]['title'] = jrCore_page_button("r{$k}", 'X', "if(confirm('" . addslashes($_ln['jrChat'][10]) . "')) { jrChat_remove_user_from_chat('{$_u['_user_id']}',1) }");
                    }
                    else {
                        $dat[4]['title'] = jrCore_page_button("r{$k}", 'X', 'disabled');
                    }
                }
                else {
                    if ($_u['_user_id'] == $_user['_user_id']) {
                        // We can remove OUR SELF from a chat that we are not the owner of
                        $dat[4]['title'] = jrCore_page_button("r{$k}", 'X', "if(confirm('" . addslashes($_ln['jrChat'][11]) . "')) { jrChat_remove_user_from_chat('{$_u['_user_id']}',0) }");
                    }
                    else {
                        if ($_rm['room_user_id'] == $_user['_user_id']) {
                            $dat[4]['title'] = jrCore_page_button("r{$k}", 'X', "if(confirm('" . addslashes($_ln['jrChat'][12]) . "')) { jrChat_remove_user_from_chat('{$_u['_user_id']}',0) }");
                        }
                        else {
                            // We are not the room admin and can remove no one but ourselves
                            $dat[4]['title'] = jrCore_page_button("r{$k}", 'X', 'disabled');
                        }
                    }
                }
            }
            $dat[4]['class'] = '" style="width:2%';
            jrCore_page_table_row($dat, "user-row user-row{$_u['_user_id']}");
        }
    }
    jrCore_page_table_footer();

    if ($_rm['room_user_id'] == $_user['_user_id'] && $_rm['room_public'] == '0') {

        // We are the chat room admin - add new user form
        jrCore_page_section_header($_ln['jrChat'][13]);

        // Form init
        $_tmp = array(
            'submit_value' => $_ln['jrChat'][13],
            'cancel'       => false,
            'onclick'      => 'jrChat_add_user_to_chat();'
        );
        jrCore_form_create($_tmp);

        $_tmp = array(
            'name'          => 'chat_room_user_id',
            'label'         => $_ln['jrChat'][14],
            'help'          => false,
            'placeholder'   => $_ln['jrChat'][15],
            'value'         => false,
            'type'          => 'live_search',
            'target'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/search_users/chat_room_user_id",
            'required'      => false,
            'validate'      => 'number_nz',
            'form_designer' => false
        );
        jrCore_form_field_create($_tmp);
    }
    jrCore_page_display();
}

//------------------------------
// remove_user
//------------------------------
function view_jrChat_remove_user($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        $_rs = array('error' => 'Chat requires you to be logged in');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['room_id']) || !jrCore_checktype($_post['room_id'], 'number_nz')) {
        $_rs = array('error' => 'invalid room_id');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['user_id']) || !jrCore_checktype($_post['user_id'], 'number_nz')) {
        $_rs = array('error' => 'invalid user_id');
        jrCore_json_response($_rs);
    }
    if (!jrUser_is_admin() && !jrChat_user_can_access_room($_post['room_id'], $_user['_user_id'])) {
        $_rs = array('error' => 'you do not have permission to access this chat room');
        jrCore_json_response($_rs);
    }
    $_rm = jrChat_get_room_info_by_id($_post['room_id']);
    if (!$_rm || !is_array($_rm)) {
        $_rs = array('error' => 'invalid room_id (2)');
        jrCore_json_response($_rs);
    }
    if (!jrUser_is_admin() && $_user['_user_id'] != $_rm['room_user_id']) {
        // We are NOT the room admin
        $_rs = array('error' => 'permission denied - you are not the room admin');
        jrCore_json_response($_rs);
    }
    // Cannot remove room admin
    if ($_rm['room_user_id'] == $_post['user_id']) {
        $_rs = array('error' => 'permission denied - you cannot remove the room admin');
        jrCore_json_response($_rs);
    }
    if (jrChat_remove_user_from_room($_post['room_id'], $_post['user_id'])) {
        $_rs = array('ok' => 1);
        jrCore_json_response($_rs);
    }
    $_rs = array('error' => 'an error was encountered removing the user from the room - please try again');
    jrCore_json_response($_rs);
}

//------------------------------
// get_chats
//------------------------------
function view_jrChat_get_chats($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        jrCore_db_close();
        echo 'ERROR: Chat requires you to be logged in';
        exit;
    }
    if (!isset($_post['last_id']) || !jrCore_checktype($_post['last_id'], 'number_nn')) {
        jrCore_db_close();
        echo 'ERROR: invalid last_id';
        exit;
    }

    $_ln = jrUser_load_lang_strings();
    jrCore_page_set_no_header_or_footer();

    $dat             = array();
    $dat[1]['title'] = '';
    $dat[1]['width'] = '2%';
    $dat[2]['title'] = $_ln['jrChat'][16];
    $dat[2]['width'] = '86%';
    $dat[3]['title'] = $_ln['jrChat'][52];
    $dat[3]['width'] = '10%';
    $dat[4]['title'] = $_ln['jrChat'][18];
    $dat[4]['width'] = '2%';
    jrCore_page_table_header($dat);

    $_cr = array();
    $_pr = array();
    $_nm = array();
    $_rt = jrChat_get_rooms_for_user($_user['_user_id'], $_post['last_id']);
    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $k => $_r) {
            if ($_r['room_private'] == 1) {
                $_pr[] = $_r;
                if ($_r['room_user_id'] != $_user['_user_id']) {
                    $uid       = (int) $_r['room_user_id'];
                    $_nm[$uid] = $uid;
                }
            }
            else {
                $_cr[] = $_r;
            }
        }
    }

    // Chat Rooms
    if (count($_cr) > 0) {
        foreach ($_cr as $k => $_r) {
            $dat = array();
            if (jrUser_is_admin() && $_r['room_public'] == 1) {
                $dat[1]['title'] = jrCore_page_button("room-del-{$k}", 'X', "if(confirm('" . addslashes($_ln['jrChat'][19]) . "')) { jrChat_delete_room_id('{$_r['room_id']}') }");
            }
            elseif ($_r['room_user_id'] == $_user['_user_id']) {
                $dat[1]['title'] = jrCore_page_button("room-del-{$k}", 'X', "if(confirm('" . addslashes($_ln['jrChat'][20]) . "')) { jrChat_delete_room_id('{$_r['room_id']}') }");
            }
            elseif (jrUser_is_admin()) {
                $dat[1]['title'] = jrCore_page_button("room-del-{$k}", 'X', "if(confirm('" . addslashes($_ln['jrChat'][20]) . "')) { jrChat_delete_room_id('{$_r['room_id']}') }");
            }
            else {
                $dat[1]['title'] = jrCore_page_button("room-del-{$k}", 'X', 'disabled');
            }
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = ucwords($_r['room_title']) . '<br><small>';
            if ($_r['room_public'] == 1) {
                $dat[2]['title'] .= $_ln['jrChat'][21];
            }
            else {
                $dat[2]['title'] .= $_ln['jrChat'][24];
            }
            $transcript_url  = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/transcript/room_id={$_r['room_id']}";
            $dat[2]['title'] .= ' &bull; <a href="' . $transcript_url . '">' . $_ln['jrChat'][35] . '</a> &bull; ' . jrCore_format_time($_r['room_updated'], false, 'relative') . '</small>';
            $dat[3]['title'] = '<small>' . jrCore_number_format($_r['room_msg_count']) .'</small>';
            $dat[3]['class'] = 'center';
            if ($_r['room_new_count'] > 0) {
                $dat[3]['class'] = 'center success';
            }
            $dat[4]['title'] = jrCore_page_button("room-enter-{$k}", "&#10148;", "jrChat_load_room_id('{$_r['room_id']}', '{$_r['room_new_count']}')");
            $dat[4]['class'] = 'center';
            jrCore_page_table_row($dat, "room-row room-row{$_r['room_id']}");
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = $_ln['jrChat'][22];
        $dat[1]['class'] = 'p10 center';
        jrCore_page_table_row($dat);
    }

    if (jrUser_is_admin() || jrUser_get_profile_home_key('quota_jrChat_create_rooms') == 'on') {
        $dat = array();
        if (jrUser_is_admin()) {
            // admin users can create OPEN chats
            $dat[1]['title'] = '<input id="jrchat-new-chat-title" type="text" class="form_text" placeholder="' . jrCore_entity_string($_ln['jrChat'][23]) . '" onkeypress="if (event && event.keyCode == 13) { if (this.value.trim().length > 0) { event.preventDefault(); jrChat_create_room(); return false } else { event.preventDefault(); return false } }">';
            $dat[1]['class'] = '" colspan="2';
            $dat[2]['title'] = '<input id="jrchat-new-chat-type" name="private" type="checkbox" class="form_checkbox" checked><br><small>' . $_ln['jrChat'][24] . '</small>';
            $dat[2]['class'] = 'center jrchat-type-checkbox';
            $dat[3]['title'] = jrCore_page_button('create', '&#x2713;', "jrChat_create_room()");
            $dat[3]['class'] = 'center';
        }
        else {
            $dat[1]['title'] = '<input id="jrchat-new-chat-title" type="text" class="form_text" placeholder="' . jrCore_entity_string($_ln['jrChat'][23]) . '" onkeypress="if (event && event.keyCode == 13) { if (this.value.trim().length > 0) { event.preventDefault(); jrChat_create_room(); return false } else { event.preventDefault(); return false } }">';
            $dat[1]['class'] = '" colspan="3';
            $dat[2]['title'] = jrCore_page_button('create', '&#x2713;', "jrChat_create_room()");
            $dat[2]['class'] = 'center';
        }
        jrCore_page_table_row($dat);
    }

    // Existing User -> User chats
    if (count($_pr) > 0) {

        // Do we have any names we need to grab?
        if (count($_nm) > 0) {
            $_tm = jrCore_db_get_multiple_items('jrUser', $_nm);
            if ($_tm && is_array($_tm)) {
                foreach ($_tm as $_u) {
                    $_nm["{$_u['_user_id']}"] = jrCore_str_to_lower($_u['user_name']);
                }
            }
            unset($_tm);
        }

        $don             = false;
        $dat             = array();
        $dat[1]['title'] = '';
        $dat[2]['title'] = $_ln['jrChat'][25];
        $dat[3]['title'] = $_ln['jrChat'][52];
        $dat[4]['title'] = $_ln['jrChat'][18];
        jrCore_page_table_header($dat, null, true);

        foreach ($_pr as $k => $_r) {
            if ($don && $_r['room_msg_count'] == 0) {
                // No need to show any rooms with no messages
                continue;
            }
            $uid = (int) $_r['room_user_id'];
            $dat = array();
            if (jrUser_is_admin() || $_r['room_user_id'] == $_user['_user_id']) {
                $dat[1]['title'] = jrCore_page_button("chat-del-{$k}", 'X', "if(confirm('" . addslashes($_ln['jrChat'][20]) . "')) { jrChat_delete_room_id('{$_r['room_id']}') }");
            }
            else {
                $dat[1]['title'] = jrCore_page_button("chat-del-{$k}", 'X', 'disabled');
            }
            $dat[1]['class'] = 'center';
            if ($uid == $_user['_user_id']) {
                // We CREATED this room - show user we are chatting with
                $dat[2]['title'] = jrCore_str_to_lower($_r['room_title']);
            }
            elseif (isset($_nm[$uid]) && strlen($_nm[$uid]) > 1) {
                // We did NOT create this room - if this is an admin user show BOTH names
                if (jrUser_is_admin()) {
                    if ($_r['room_title'] == $_user['user_name']) {
                        $dat[2]['title'] = $_nm[$uid];
                    }
                    else {
                        // We've hit the first chat that is not with us - add divider
                        if (!$don) {
                            $div             = array();
                            $div[1]['title'] = '';
                            $div[2]['title'] = 'other private user chats';
                            $div[3]['title'] = $_ln['jrChat'][52];
                            $div[4]['title'] = $_ln['jrChat'][18];
                            jrCore_page_table_header($div, null, true);
                            $don = true;
                        }
                        $dat[2]['title'] = $_nm[$uid] . ' & ' . jrCore_str_to_lower($_r['room_title']);
                    }
                }
                else {
                    $dat[2]['title'] = $_nm[$uid];
                }
            }
            else {
                $dat[2]['title'] = $_ln['jrChat'][25];
            }
            $dat[2]['title'] .= '<br><small>';
            if ($_r['room_msg_count'] > 0) {
                $dat[2]['title'] .= jrCore_format_time($_r['room_updated'], false, 'relative');
            }
            else {
                $dat[2]['title'] .= '-';
            }
            $dat[2]['title'] .= '</small>';
            $dat[3]['title'] = '<small>' . jrCore_number_format($_r['room_msg_count']) . '</small>';
            $dat[3]['class'] = 'center';
            if ($_r['room_new_count'] > 0) {
                $dat[3]['class'] = 'center success';
            }
            $dat[4]['title'] = jrCore_page_button("chat-enter-{$k}", "&#10148;", "jrChat_load_room_id('{$_r['room_id']}', '{$_r['room_new_count']}')");
            $dat[4]['class'] = 'center';
            jrCore_page_table_row($dat, "room-row room-row{$_r['room_id']}");
        }
        jrCore_page_table_footer();
    }
    else {
        jrCore_page_table_footer();
    }

    // Create new chat with a user
    if (jrUser_is_admin() || (isset($_user['quota_jrChat_create_chats']) && $_user['quota_jrChat_create_chats'] == 'on')) {

        jrCore_page_section_header($_ln['jrChat'][26]);

        // Form init
        $_tmp = array(
            'submit_value' => $_ln['jrChat'][27],
            'cancel'       => false,
            'onclick'      => "jrChat_start_chat_with_user();"
        );
        jrCore_form_create($_tmp);

        $_tmp = array(
            'name'          => 'chat_user_id',
            'label'         => $_ln['jrChat'][14],
            'help'          => false,
            'placeholder'   => $_ln['jrChat'][15],
            'value'         => false,
            'type'          => 'live_search',
            'target'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/search_users/chat_user_id",
            'required'      => false,
            'validate'      => 'number_nz',
            'form_designer' => false
        );
        jrCore_form_field_create($_tmp);
    }
    jrCore_page_display();
}

//------------------------------
// search_users
//------------------------------
function view_jrChat_search_users($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (strpos($_post['q'], '@') === 0) {
        $_post['q'] = substr($_post['q'], 1);
    }

    $_rt = array(
        'search'         => array(),
        'return_keys'    => array('_user_id', 'user_name'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'privacy_check'  => false,
        'limit'          => 12
    );

    // Are there any quotas that do NOT allow chat?
    $tbl = jrCore_db_table_name('jrProfile', 'quota_value');
    $req = "SELECT quota_id FROM {$tbl} WHERE `module` = 'jrChat' AND `name` = 'allowed' AND `value` = 'on'";
    $_qi = jrCore_db_query($req, 'quota_id');
    if ($_qi && is_array($_qi)) {
        // We have quotas where chat is enabled.  We have to make sure any users we search on are in one of these quotas
        $tbl             = jrCore_db_table_name('jrProfile', 'item_key');
        $_rt['search'][] = "_profile_id in (SELECT `_item_id` FROM {$tbl} WHERE `key` = 'profile_quota_id' AND `value` IN(" . implode(',', array_keys($_qi)) . '))';
    }
    $fld = 'chat_user_id';
    if (isset($_post['_1']) && jrCore_checktype($_post['_1'], 'core_string')) {
        $fld = $_post['_1'];
    }

    // If this is a non admin, and followers only is enforced, make sure all users are followers
    if (!jrUser_is_admin()) {
        if (jrCore_module_is_active('jrFollower') && isset($_user['quota_jrChat_followers_only']) && $_user['quota_jrChat_followers_only'] == 'on') {
            if (!$_ui = jrCore_is_cached('jrChat', $_user['_user_id'])) {
                $_ui = jrFollower_get_users_following(jrUser_get_profile_home_key('_profile_id'));
                if ($_ui && is_array($_ui)) {
                    jrCore_add_to_cache('jrChat', $_user['_user_id'], $_ui);
                }
            }
            if ($_ui && is_array($_ui)) {
                $_rt['search'][] = '_item_id in ' . implode(',', array_keys($_ui));
            }
            else {
                // User has no followers
                return jrCore_live_search_results($fld, array());
            }
        }
    }

    // Narrow to selected search
    $_rt['search'][] = "user_name like {$_post['q']}%";

    $_rt = jrCore_db_search_items('jrUser', $_rt);
    $_sl = array();
    if ($_rt && is_array($_rt) && is_array($_rt['_items'])) {
        foreach ($_rt['_items'] as $_v) {
            $_sl["{$_v['_user_id']}"] = $_v['user_name'];
        }
    }
    return jrCore_live_search_results($fld, $_sl);
}

//------------------------------
// create_room
//------------------------------
function view_jrChat_create_room($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        $_rs = array('error' => 'Chat requires you to be logged in');
        jrCore_json_response($_rs);
    }
    if (!jrUser_is_admin() && jrUser_get_profile_home_key('quota_jrChat_create_rooms') != 'on') {
        $_rs = array('error' => 'permission denied');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['title']) || strlen($_post['title']) === 0) {
        $_rs = array('error' => 'invalid title');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['type']) || !jrCore_checktype($_post['type'], 'onoff')) {
        $_rs = array('error' => 'invalid room type');
        jrCore_json_response($_rs);
    }
    // room_public: 1 = open, public chat room, 0 = invite only
    if (!jrUser_is_admin()) {
        // Private rooms only for non-admins
        $typ = 0;
    }
    else {
        $typ = ($_post['type'] == 'on') ? 0 : 1;
    }
    $ttl = jrCore_strip_html($_post['title']);
    // Create the new room
    $uid = (int) $_user['_user_id'];
    $tbl = jrCore_db_table_name('jrChat', 'room');
    $req = "INSERT INTO {$tbl} (room_user_id, room_created, room_updated, room_title, room_public) VALUES ('{$uid}', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), '" . jrCore_db_escape($ttl) . "', '{$typ}')";
    $rid = jrCore_db_query($req, 'INSERT_ID');
    if ($rid && jrCore_checktype($rid, 'number_nz')) {

        // We created the room - add the creating user's slot
        $tbl = jrCore_db_table_name('jrChat', 'slot');
        $req = "INSERT INTO {$tbl} (slot_room_id, slot_user_id) VALUES ('{$rid}', '{$uid}')";
        $sid = jrCore_db_query($req, 'INSERT_ID');
        if ($sid && jrCore_checktype($sid, 'number_nz')) {
            $_rs = array(
                'rid' => $rid,
                'ttl' => $ttl
            );
        }
        else {
            // Cleanup
            $tbl = jrCore_db_table_name('jrChat', 'room');
            $req = "DELETE FROM {$tbl} WHERE room_id = '{$rid}'";
            jrCore_db_query($req);
            $_rs = array('error' => 'an error was encountered creating the room - please try again');
        }
    }
    else {
        $_rs = array('error' => 'an error was encountered creating the room - please try again');
    }
    jrCore_json_response($_rs);
}

//------------------------------
// add_user_to_chat
//------------------------------
function view_jrChat_add_user_to_chat($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        $_rs = array('error' => 'Chat requires you to be logged in');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['room_id']) || !jrCore_checktype($_post['room_id'], 'number_nz')) {
        $_rs = array('error' => 'invalid room_id');
        jrCore_json_response($_rs);
    }
    $rid = (int) $_post['room_id'];
    if (!isset($_post['user_id']) || !jrCore_checktype($_post['user_id'], 'number_nz')) {
        $_rs = array('error' => 'invalid user_id');
        jrCore_json_response($_rs);
    }
    $iid = (int) $_post['user_id'];

    // Is this user allowed to chat with the passed in user_id?
    if (!jrUser_is_admin()) {
        if (isset($_user['quota_jrChat_followers_only']) && $_user['quota_jrChat_followers_only'] == 'on') {
            if (!jrFollower_is_follower($iid, jrUser_get_profile_home_key('_profile_id'))) {
                $_ln = jrUser_load_lang_strings();
                $_rs = array('error' => $_ln['jrChat'][29]);
                jrCore_json_response($_rs);
            }
        }
    }
    $tbl = jrCore_db_table_name('jrChat', 'slot');
    $req = "INSERT INTO {$tbl} (slot_room_id, slot_user_id) VALUES ('{$rid}', '{$iid}')";
    $sid = jrCore_db_query($req, 'INSERT_ID');
    if ($sid && jrCore_checktype($sid, 'number_nz')) {

        $_ln = jrUser_load_lang_strings();
        $nam = jrCore_db_get_item_key('jrUser', $iid, 'user_name');
        jrChat_create_message($rid, $_user['_user_id'], "&#9734; <b>{$nam}</b> {$_ln['jrChat'][28]} &#9734;");
        $_rs = array(
            'uid'       => $iid,
            'user_name' => $nam
        );
    }
    else {
        $_rs = array('error' => 'an error was encountered adding the user to the room - please try again');
    }
    jrCore_json_response($_rs);
}

//------------------------------
// remove_user_from_chat
//------------------------------
function view_jrChat_remove_user_from_chat($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        $_rs = array('error' => 'Chat requires you to be logged in');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['room_id']) || !jrCore_checktype($_post['room_id'], 'number_nz')) {
        $_rs = array('error' => 'invalid room_id');
        jrCore_json_response($_rs);
    }
    $rid = (int) $_post['room_id'];
    if (!isset($_post['user_id']) || !jrCore_checktype($_post['user_id'], 'number_nz')) {
        $_rs = array('error' => 'invalid user_id');
        jrCore_json_response($_rs);
    }
    $iid = (int) $_post['user_id'];
    $_rm = jrChat_get_room_info_by_id($rid);
    if (!$_rm || !is_array($_rm)) {
        $_rs = array('error' => 'invalid room_id - data not found');
        jrCore_json_response($_rs);
    }
    // Are we trying to remove the room admin?
    if ($_rm['room_user_id'] == $iid) {
        $_rs = array('error' => 'You are the room admin and cannot remove yourself!');
        jrCore_json_response($_rs);
    }

    // Is this user allowed to chat with the passed in user_id?
    if (!jrUser_is_admin() && $_rm['room_user_id'] != $_user['_user_id'] && $iid != $_user['_user_id']) {
        $_rs = array('error' => 'You are not the room admin and cannot remove users');
        jrCore_json_response($_rs);
    }

    // Are we blocking this user?
    $_ln = jrUser_load_lang_strings();
    $blk = (isset($_post['block']) && $_post['block'] == '1') ? 1 : 0;
    if (jrChat_remove_user_from_room($rid, $iid, $blk)) {

        // Add message that user has been removed
        $nam = jrCore_db_get_item_key('jrUser', $iid, 'user_name');
        if ($iid == $_user['_user_id']) {
            jrChat_create_message($rid, $_user['_user_id'], "&#9734; <b>{$nam}</b> {$_ln['jrChat'][30]} &#9734;");
            $_rs = array(
                'uid'       => $iid,
                'user_name' => $nam,
                'self'      => 1
            );
        }
        else {
            jrChat_create_message($rid, $_user['_user_id'], "&#9734; <b>{$nam}</b> {$_ln['jrChat'][31]} &#9734;");
            $_rs = array(
                'uid'       => $iid,
                'user_name' => $nam,
                'self'      => 0
            );
        }
    }
    else {
        $_rs = array('error' => 'an error was encountered removing the user from the room - please try again');
    }
    jrCore_json_response($_rs);
}

//------------------------------
// create_private_room
//------------------------------
function view_jrChat_create_private_room($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        $_rs = array('error' => 'Chat requires you to be logged in');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['user_id']) || !jrCore_checktype($_post['user_id'], 'number_nz')) {
        $_rs = array('error' => 'invalid user_id');
        jrCore_json_response($_rs);
    }
    $iid = (int) $_post['user_id'];

    if (!jrUser_is_admin()) {

        // Is this user allowed to create private chats?
        if (!isset($_user['quota_jrChat_create_chats']) || $_user['quota_jrChat_create_chats'] != 'on') {
            $_ln = jrUser_load_lang_strings();
            $_rs = array('error' => $_ln['jrChat'][47]);
            jrCore_json_response($_rs);
        }

        // Is this user allowed to chat with the passed in user_id?
        if (isset($_user['quota_jrChat_followers_only']) && $_user['quota_jrChat_followers_only'] == 'on') {
            if (!jrFollower_is_follower($iid, jrUser_get_profile_home_key('_profile_id'))) {
                $_ln = jrUser_load_lang_strings();
                $_rs = array('error' => $_ln['jrChat'][29]);
                jrCore_json_response($_rs);
            }
        }
    }
    $ttl = jrCore_db_escape(jrCore_db_get_item_key('jrUser', $iid, 'user_name'));

    // Create the new PRIVATE room
    $uid = (int) $_user['_user_id'];

    // Does the room already exist?  Note that the room could of been created by the other user
    $tbl = jrCore_db_table_name('jrChat', 'room');
    $req = "SELECT room_id FROM {$tbl} WHERE room_user_id = '{$uid}' AND room_title = '{$ttl}' LIMIT 1";
    $_ex = jrCore_db_query($req, 'SINGLE');
    if (!$_ex || !is_array($_ex)) {

        // OK - we did not find a room that was created by us to chat with the other user.
        // Has the other user already created a private chat back to us?
        $req = "SELECT room_id FROM {$tbl} WHERE room_user_id = '{$iid}' AND room_title = '" . jrCore_db_escape($_user['user_name']) . "' LIMIT 1";
        $_ex = jrCore_db_query($req, 'SINGLE');
        if (!$_ex || !is_array($_ex)) {
            $req = "INSERT INTO {$tbl} (room_user_id, room_created, room_updated, room_title, room_private) VALUES ('{$uid}', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), '{$ttl}', 1)";
            $rid = jrCore_db_query($req, 'INSERT_ID');
            $new = true;
        }
        else {
            $rid = $_ex['room_id'];
            $new = false;
        }
    }
    else {
        $rid = $_ex['room_id'];
        $new = false;
    }
    if ($rid && jrCore_checktype($rid, 'number_nz')) {

        // get creating user's slot
        $sid = false;
        $tbl = jrCore_db_table_name('jrChat', 'slot');
        if (!$new) {
            // We did not create a new room - get existing slot
            $req = "SELECT slot_id FROM {$tbl} WHERE slot_room_id = '{$rid}' AND slot_user_id = '{$uid}' LIMIT 1";
            $_si = jrCore_db_query($req, 'SINGLE');
            if ($_si && is_array($_si)) {
                $sid = (int) $_si['slot_id'];
            }
        }
        if ($new) {
            $req = "INSERT INTO {$tbl} (slot_room_id, slot_user_id) VALUES ('{$rid}', '{$uid}'), ('{$rid}', '{$iid}')";
            $sid = jrCore_db_query($req, 'INSERT_ID');
        }
        if ($sid && jrCore_checktype($sid, 'number_nz')) {
            $_rs = array(
                'rid' => $rid,
                'ttl' => $ttl
            );
        }
        else {
            // Cleanup
            if ($new) {
                $tbl = jrCore_db_table_name('jrChat', 'room');
                $req = "DELETE FROM {$tbl} WHERE room_id = '{$rid}'";
                jrCore_db_query($req);
            }
            $_rs = array('error' => 'an error was encountered creating the room - please try again');
        }
    }
    else {
        $_rs = array('error' => 'an error was encountered creating the room - please try again');
    }
    jrCore_json_response($_rs);
}

//------------------------------
// delete_room
//------------------------------
function view_jrChat_delete_room($_post, $_user, $_conf)
{
    jrCore_validate_location_url();
    if (!jrUser_is_logged_in()) {
        $_rs = array('error' => 'Chat requires you to be logged in');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        $_rs = array('error' => 'invalid room id');
        jrCore_json_response($_rs);
    }
    if (!jrUser_is_admin()) {
        if (jrUser_get_profile_home_key('quota_jrChat_create_rooms') != 'on') {
            $_rs = array('error' => 'permission denied');
            jrCore_json_response($_rs);
        }
        if (!jrChat_user_can_access_room($_post['id'], $_user['_user_id'])) {
            $_rs = array('error' => 'permission denied');
            jrCore_json_response($_rs);
        }
    }
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        $_rs = array('error' => 'invalid room id');
        jrCore_json_response($_rs);
    }
    $_rm = jrChat_get_room_info_by_id($_post['id']);
    if (!$_rm || !is_array($_rm)) {
        $_rs = array('error' => 'invalid room id - not found');
        jrCore_json_response($_rs);
    }
    // Is this user the room admin?
    if (!jrUser_is_admin() && $_rm['room_user_id'] != $_user['_user_id']) {
        $_rs = array('error' => 'permission denied');
        jrCore_json_response($_rs);
    }
    if (jrChat_delete_room_by_id($_post['id'])) {
        // Get our LAST ACTIVE room so we can switchback to it if needed
        $_rs = array(
            'ok'  => 1,
            'rid' => jrChat_get_last_active_room_for_user($_user['_user_id'])
        );
        jrCore_json_response($_rs);
    }
    $_rs = array('error' => 'an error was encountered deleting the room - please try again');
    jrCore_json_response($_rs);
}

//------------------------------
// get_room_info
//------------------------------
function view_jrChat_get_room_info($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        $_rs = array('error' => 'Chat requires you to be logged in');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        $_rs = array('error' => 'invalid room id');
        jrCore_json_response($_rs);
    }

    // Make sure this room exists
    $rid = (int) $_post['id'];
    $_rm = jrChat_get_room_info_by_id($rid);
    if (!$_rm || !is_array($_rm)) {
        $_rs = array('error' => 'invalid room_id - no data found');
        jrCore_json_response($_rs);
    }

    // Make sure user has access
    if (!jrUser_is_admin() && !jrChat_user_can_access_room($rid, $_user['_user_id'])) {
        // If this is a PUBLIC ROOM, we can add their slot
        if (isset($_rm['room_public']) && $_rm['room_public'] == 1) {
            // This is a PUBLIC room, and we have not claimed a slot yet - setup
            $tbl = jrCore_db_table_name('jrChat', 'slot');
            $req = "INSERT IGNORE INTO {$tbl} (slot_room_id, slot_user_id) VALUES ('{$rid}', '{$_user['_user_id']}')";
            jrCore_db_query($req);
        }
        else {
            $_rs = array('error' => 'you do not have permission to access that chat room (1)');
            jrCore_json_response($_rs);
        }
    }

    if ($_rm['room_private'] == 1) {
        // This is a private room - the TITLE will be the OTHER user in chat
        if (jrUser_is_admin() && $_rm['room_title'] != $_user['user_name']) {
            $_rm['room_title'] = jrChat_get_private_room_title($rid, $_rm['room_user_id'], $_rm['room_title']);
        }
        else {
            $_rm['room_title'] = jrChat_get_private_room_title($rid, $_user['_user_id']);
        }
    }
    else {
        $_rm['room_title'] = ucwords($_rm['room_title']);
    }
    jrCore_json_response($_rm);
}

//------------------------------
// am_typing
//------------------------------
function view_jrChat_am_typing($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        $_rs = array('error' => 'Chat requires you to be logged in');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['room_id']) || !jrCore_checktype($_post['room_id'], 'number_nz')) {
        $_rs = array('error' => 'invalid room_id');
        jrCore_json_response($_rs);
    }
    $rid = (int) $_post['room_id'];
    $uid = (int) $_user['_user_id'];
    $tbl = jrCore_db_table_name('jrChat', 'typing');
    $req = "INSERT INTO {$tbl} (t_time, t_room_id, t_user_id) VALUES (UNIX_TIMESTAMP(), '{$rid}', '{$uid}') ON DUPLICATE KEY UPDATE t_time = UNIX_TIMESTAMP()";
    jrCore_db_query($req);
    $_rs = array('ok' => 1);
    jrCore_json_response($_rs);
}

//------------------------------
// get_typing_users
//------------------------------
function view_jrChat_get_typing_users($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        $_rs = array('error' => 'Chat requires you to be logged in');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['room_id']) || !jrCore_checktype($_post['room_id'], 'number_nz')) {
        $_rs = array('error' => 'invalid room_id');
        jrCore_json_response($_rs);
    }
    $_rs = array('ok' => 1);
    $_rt = jrChat_get_typing_users($_post['room_id']);
    if ($_rt && is_array($_rt)) {
        $_rs['users'] = $_rt;
    }
    jrCore_json_response($_rs);
}

//------------------------------
// set_chat_width
//------------------------------
function view_jrChat_set_chat_width($_post, $_user, $_conf)
{
    jrCore_validate_location_url();
    if (!jrUser_is_logged_in()) {
        $_rs = array('error' => 'Chat requires you to be logged in');
        jrCore_json_response($_rs);
    }
    jrChat_set_chat_width(intval($_post['width']));
    $_rs = array('ok' => 1);
    jrCore_json_response($_rs);
}

//------------------------------
// set_chat_state
//------------------------------
function view_jrChat_set_chat_state($_post, $_user, $_conf)
{
    jrCore_validate_location_url();
    if (!jrUser_is_logged_in()) {
        $_rs = array('error' => 'Chat requires you to be logged in');
        jrCore_json_response($_rs);
    }
    jrChat_set_chat_state($_post['state']);
    $_rs = array('ok' => 1);
    jrCore_json_response($_rs);
}

//------------------------------
// upload_files
//------------------------------
function view_jrChat_upload_files($_post, $_user, $_conf)
{
    global $_conf;
    jrUser_session_require_login();
    if (!isset($_post['upload_token']) || !jrCore_checktype($_post['upload_token'], 'md5')) {
        jrCore_db_close();
        exit;
    }
    $_files = jrCore_get_uploaded_media_files('jrChat', 'chat_file');
    if ($_files && is_array($_files)) {
        $_sz = jrImage_get_allowed_image_widths();
        $pid = jrUser_get_profile_home_key('_profile_id');
        foreach ($_files as $file) {
            $nam = file_get_contents("{$file}.tmp");
            $siz = filesize($file);
            $cid = jrCore_db_create_item('jrChat', array(), array('_profile_id' => $pid));
            if ($cid && $cid > 0) {
                if (jrCore_save_media_file('jrChat', $file, $pid, $cid)) {
                    $_ln = jrUser_load_lang_strings();
                    if (jrImage_is_image_file($nam)) {
                        $isz = 1280;
                        $_tm = getimagesize($file);
                        if ($_tm && is_array($_tm) && isset($_tm[0]) && $_tm[0] > 0) {
                            foreach ($_sz as $size) {
                                if ($size > $_tm[0]) {
                                    $isz = $size;
                                    break;
                                }
                            }
                        }
                        $msg = "&#9734; <b>{$_user['user_name']}</b> {$_ln['jrChat'][32]} &#9734;<br><a href=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/image/chat_file/{$cid}/{$isz}?_v={$siz}\" data-lightbox=\"chat-images\">{$nam}</a> &bull; " . jrCore_format_size($siz) . " &bull; <a href=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/download/chat_file/{$cid}\">download</a>";
                    }
                    else {
                        $msg = "&#9734; <b>{$_user['user_name']}</b> {$_ln['jrChat'][33]} &#9734;<br>{$nam} &bull; " . jrCore_format_size($siz) . " &bull; <a href=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/download/chat_file/{$cid}\">download</a>";
                    }
                    $mid = jrChat_create_message($_post['room_id'], $_user['_user_id'], $msg);
                    if ($mid && $mid > 0) {
                        // Add our message ID back into our DS so if deleted we can remove the file
                        jrCore_db_update_item('jrChat', $cid, array('chat_message_id' => $mid));
                    }
                }
                unlink($file);
                unlink("{$file}.tmp");
            }
        }
    }
    jrCore_json_response(array('ok' => 1));
}

//------------------------------
// mobile chat
//------------------------------
function view_jrChat_mobile($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    // Does this user have quota access to chat?
    $alw = jrUser_get_profile_home_key('quota_jrChat_allowed');
    if (!$alw || $alw != 'on') {
        jrUser_not_authorized();
    }

    if (jrCore_is_mobile_device()) {
        jrCore_create_page_element('meta', array('viewport' => 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0'));
    }

    $color = 'black';
    $_tmp  = jrCore_get_registered_module_features('jrCore', 'icon_color');
    if (isset($_tmp["{$_conf['jrCore_active_skin']}"])) {
        $color = array_keys($_tmp["{$_conf['jrCore_active_skin']}"]);
        $color = reset($color);
        if ($color !== 'black' && $color !== 'white') {
            $color = 'black';
        }
    }
    $_user['icon_size']   = jrCore_get_skin_icon_size();
    $_user['icon_color']  = $color;
    $_user['chat_state']  = 'open';
    $_user['chat_width']  = '100%';
    $_user['_room']       = jrChat_get_active_room_info();
    $_user['file_types']  = jrUser_get_profile_home_key('quota_jrChat_file_types');
    $_user['max_size']    = jrUser_get_profile_home_key('quota_jrChat_max_size');
    $_user['mobile_view'] = 1;

    $_ln = jrUser_load_lang_strings();
    jrCore_page_title($_ln['jrChat'][46]);
    $html = jrCore_parse_template('meta.tpl', $_user, $_conf['jrCore_active_skin']);
    $html .= "\n<body id=\"jrchat-mobile-body\">\n";
    $html .= jrCore_parse_template('chat.tpl', $_user, 'jrChat');
    $html .= "\n</body>\n</html>";
    echo $html;
    jrCore_db_close();
    exit;
}
