<?php
/**
 * Jamroom Simple Chat module
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
 * @copyright 2016 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function jrChat_meta()
{
    $_tmp = array(
        'name'        => 'Simple Chat',
        'url'         => 'chat',
        'version'     => '1.1.1',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Simple Chat provides a site wide chat feature for logged in users',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2945/combined-audio',
        'requires'    => 'jrCore:6.0.0,jrUser:2.1.0',
        'category'    => 'communication',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrChat_init()
{
    jrCore_register_module_feature('jrCore', 'javascript', 'jrChat', 'jrChat.js');
    jrCore_register_module_feature('jrCore', 'css', 'jrChat', 'jrChat.css');

    $_tmp = array(
        'label' => 'Enable Chat Access',
        'help'  => 'If checked, users with profiles in this quota will have the ability to use chat'
    );
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrChat', 'on', $_tmp);

    jrCore_register_event_listener('jrCore', 'view_results', 'jrChat_view_results_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrChat_verify_module_listener');

    jrCore_register_event_trigger('jrChat', 'create_message', 'Fired with $_post when a new chat message is going to be saved');
    jrCore_register_event_trigger('jrChat', 'delete_message', 'Fired with $_post when a chat message has been deleted');
    return true;
}

//-----------------------
// EVENT LISTENERS
//-----------------------

/**
 * Bring in chat HTML for users that have it enabled
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrChat_view_results_listener($_data, $_user, $_conf, $_args, $event)
{
    if (!jrCore_is_mobile_device() && !jrCore_is_tablet_device() && !jrCore_is_ajax_request() && jrUser_is_logged_in() && jrCore_is_view_request() && !jrCore_get_flag('jrcore_page_meta_header_only') && !jrCore_get_flag('jrchat_added_to_page')) {

        if (jrCore_get_flag('jrprofile_view_is_active') === false) {
            if (jrCore_get_flag('jrcore_page_no_header_or_footer') === true) {
                return $_data;
            }
            if (jrCore_get_flag('jrcore_page_meta_header_only') === true) {
                return $_data;
            }
        }

        // Does this user have quota access to chat?
        $alw = jrUser_get_profile_home_key('quota_jrChat_allowed');
        if ($alw && $alw == 'on') {

            $last = strrpos($_data, '</body>');
            if ($last !== false) {

                $color = 'black';
                $_tmp  = jrCore_get_registered_module_features('jrCore', 'icon_color');
                if (isset($_tmp["{$_conf['jrCore_active_skin']}"])) {
                    $color = array_keys($_tmp["{$_conf['jrCore_active_skin']}"]);
                    $color = reset($color);
                    if ($color !== 'black' && $color !== 'white') {
                        $color = 'black';
                    }
                }
                $_user['icon_size']  = jrCore_get_skin_icon_size();
                $_user['icon_color'] = $color;
                $_user['chat_state'] = jrChat_get_chat_state();
                $_user['chat_width'] = jrChat_get_chat_width() . 'px';
                $_user['_room']      = jrChat_get_active_room_info();
                $_user['file_types'] = jrUser_get_profile_home_key('quota_jrChat_file_types');
                $_user['max_size']   = jrUser_get_profile_home_key('quota_jrChat_max_size');
                $html                = jrCore_parse_template('chat.tpl', $_user, 'jrChat');
                $_data               = substr_replace($_data, $html . '</body>', $last, 7);
                jrCore_set_flag('jrchat_added_to_page', 1);
            }
        }

    }
    return $_data;
}

/**
 * Make sure our chat updates directory exists
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrChat_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    $dir = jrCore_get_media_directory(0, FORCE_LOCAL);
    if (!is_dir("{$dir}/chat")) {
        mkdir("{$dir}/chat", $_conf['jrCore_dir_perms'], true);
    }
    return $_data;
}

//-----------------------
// FUNCTIONS
//-----------------------

/**
 * Update the updated time on a chat room
 * @param $room_id int Room ID
 * @return mixed
 */
function jrChat_update_room_time($room_id)
{
    $rid = (int) $room_id;
    $tbl = jrCore_db_table_name('jrChat', 'room');
    $req = "UPDATE {$tbl} SET room_updated = UNIX_TIMESTAMP() WHERE room_id = '{$rid}'";
    return jrCore_db_query($req);
}

/**
 * Save last served message id to a user
 * @param $user_id int User ID
 * @param $last_id int Last Message ID
 * @return bool
 */
function jrChat_set_last_message_id($user_id, $last_id)
{
    $_SESSION['last_message_id'] = $last_id;
    return true;
}

/**
 * Get the last message id sent to a user
 * @param $user_id int User ID
 * @return int
 */
function jrChat_get_last_message_id($user_id)
{
    return (isset($_SESSION['last_message_id'])) ? $_SESSION['last_message_id'] : 0;
}

/**
 * Store the width of the chat window
 * @param $width
 * @return bool
 */
function jrChat_set_chat_width($width)
{
    global $_user;
    if ($width >= 280 && $width <= 640) {
        $w                             = (int) $width;
        $_SESSION['jrchat_chat_width'] = $w;
        $_user['jrchat_chat_width']    = $w;
        jrCore_db_update_item('jrUser', $_user['_user_id'], array('user_chat_width' => $w));
    }
    return true;
}

/**
 * Get a user's chat width
 * @return bool
 */
function jrChat_get_chat_width()
{
    global $_user;
    if (isset($_SESSION['jrchat_chat_width'])) {
        $w = (int) $_SESSION['jrchat_chat_width'];
        if ($w >= 280 && $w <= 640) {
            return $w;
        }
    }
    else {
        if ($w = jrCore_db_get_item_key('jrUser', $_user['_user_id'], 'user_chat_width')) {
            return $w;
        }
    }
    return 400;
}

/**
 * Save the state of the chat pane
 * @param $state string
 * @return bool
 */
function jrChat_set_chat_state($state)
{
    global $_user;
    switch ($state) {
        case 'open':
        case 'closed':
            $_SESSION['jrchat_chat_state'] = $state;
            $_user['jrchat_chat_state']    = $state;
            jrCore_db_update_item('jrUser', $_user['_user_id'], array('user_chat_state' => $state));
            break;
    }
    return true;
}

/**
 * Get a user's chat state
 * @return bool
 */
function jrChat_get_chat_state()
{
    global $_user;
    if (isset($_SESSION['jrchat_chat_state'])) {
        return $_SESSION['jrchat_chat_state'];
    }
    else {
        if ($state = jrCore_db_get_item_key('jrUser', $_user['_user_id'], 'user_chat_state')) {
            return $state;
        }
    }
    return 'closed';
}

/**
 * Get users in a chat room
 * @param $id
 * @return bool|mixed
 */
function jrChat_get_room_users($id)
{
    $rid = (int) $id;
    $tbl = jrCore_db_table_name('jrChat', 'slot');
    $req = "SELECT slot_user_id FROM {$tbl} WHERE slot_room_id = '{$rid}'";
    $_rt = jrCore_db_query($req, 'slot_user_id');
    if ($_rt && is_array($_rt)) {
        $_sp = array(
            'search'         => array(
                '_item_id in ' . implode(',', array_keys($_rt))
            ),
            'order_by'       => array('user_name' => 'asc'),
            'skip_triggers'  => true,
            'ignore_pending' => true,
            'quota_check'    => false,
            'privacy_check'  => false,
            'no_cache'       => true,
            'limit'          => count($_rt)
        );
        $_sp = jrCore_db_search_items('jrUser', $_sp);
        if ($_sp && is_array($_sp) && isset($_sp['_items'])) {
            return $_sp['_items'];
        }
    }
    return false;
}

/**
 * Get title for a Private Room
 * @param $room_id int Room ID
 * @param $user_id int User ID
 * @param $user_name string User name for private chat
 * @return mixed|string
 */
function jrChat_get_private_room_title($room_id, $user_id, $user_name = null)
{
    $rid = (int) $room_id;
    $uid = (int) $user_id;
    if (is_null($user_name)) {
        $tbl = jrCore_db_table_name('jrChat', 'slot');
        $req = "SELECT slot_user_id FROM {$tbl} WHERE slot_room_id = '{$rid}' AND slot_user_id != '{$uid}' LIMIT 1";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if ($_rt && is_array($_rt) && isset($_rt['slot_user_id'])) {
            return jrCore_db_get_item_key('jrUser', $_rt['slot_user_id'], 'user_name');
        }
        $_ln = jrUser_load_lang_strings();
        return $_ln['jrChat'][25];
    }

    // We've been given the OTHER user in chat
    return jrCore_db_get_item_key('jrUser', $user_id, 'user_name') . ' & ' . $user_name;
}

/**
 * Remove a user from a chat room
 * @param $room_id int Room ID
 * @param $user_id int User ID
 * @param $block_only int set to "1" to BLOCK the user from a public room
 * @return bool
 */
function jrChat_remove_user_from_room($room_id, $user_id, $block_only = 0)
{
    $rid = (int) $room_id;
    $uid = (int) $user_id;
    $tbl = jrCore_db_table_name('jrChat', 'slot');
    if ($block_only && $block_only == 1) {
        $req = "UPDATE {$tbl} SET slot_blocked = 1 WHERE slot_room_id = '{$rid}' AND slot_user_id = '{$uid}' LIMIT 1";
    }
    else {
        // Remove the user - they will have to be re-invited
        $req = "DELETE FROM {$tbl} WHERE slot_room_id = '{$rid}' AND slot_user_id = '{$uid}' LIMIT 1";
    }
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Get the active room info for a user
 * @return bool|mixed
 */
function jrChat_get_active_room_info()
{
    if ($rid = jrChat_get_user_active_room_id()) {
        return jrChat_get_room_info_by_id($rid);
    }
    return false;
}

/**
 * Set a user's active room id
 * @param $id int Room ID
 * @return bool
 */
function jrChat_set_user_active_room_id($id)
{
    global $_user;
    $rid = (int) $id;
    if ($rid > 0 && (!isset($_user['user_active_room_id']) || $_user['user_active_room_id'] != $rid)) {
        $_user['user_active_room_id'] = $rid;
        jrCore_db_update_item('jrUser', $_user['_user_id'], array('user_active_room_id' => $rid));
    }
    $_SESSION['user_active_room_id'] = $rid;
    return true;
}

/**
 * Get a user's active room id
 * @return bool
 */
function jrChat_get_user_active_room_id()
{
    global $_conf, $_user;
    if (isset($_user['user_active_room_id'])) {
        return $_user['user_active_room_id'];
    }
    if (isset($_SESSION['user_active_room_id'])) {
        return $_SESSION['user_active_room_id'];
    }
    $rid = jrCore_db_get_item_key('jrUser', $_user['_user_id'], 'user_active_room_id');
    if ($rid && $rid > 0) {
        jrChat_set_user_active_room_id($rid);
        return $rid;
    }
    if (isset($_conf['jrChat_default_room']) && jrCore_checktype($_conf['jrChat_default_room'], 'number_nz')) {
        // We have a default public room - make sure user has a slot
        $rid = (int) $_conf['jrChat_default_room'];
        $_rm = jrChat_get_room_info_by_id($rid);
        if ($_rm && is_array($_rm)) {
            // Make sure user has a slot
            $tbl = jrCore_db_table_name('jrChat', 'slot');
            $req = "INSERT IGNORE INTO {$tbl} (slot_room_id, slot_user_id) VALUES ('{$rid}', '{$_user['_user_id']}')";
            jrCore_db_query($req);
            jrChat_set_user_active_room_id($rid);
            return $rid;
        }
    }
    return 0;
}

/**
 * Get info about a specific chat room by ID
 * @param $id int Room ID
 * @return mixed
 */
function jrChat_get_room_info_by_id($id)
{
    $rid = (int) $id;
    $tbs = jrCore_db_table_name('jrChat', 'slot');
    $tbr = jrCore_db_table_name('jrChat', 'room');
    $req = "SELECT r.* FROM {$tbs} s LEFT JOIN {$tbr} r ON r.room_id = s.slot_room_id WHERE s.slot_room_id = '{$rid}'";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        $_rt[0]['room_user_count'] = jrChat_get_room_user_count($rid);
        $_rp                       = $_rt[0];
        unset($_rt);
        return $_rp;
    }
    return false;
}

/**
 * Delete a chat room
 * @param $id int Room ID
 * @return mixed
 */
function jrChat_delete_room_by_id($id)
{
    $rid = (int) $id;
    $_td = array(
        'room'    => 'room_id',
        'slot'    => 'slot_room_id',
        'message' => 'msg_room_id',
        'archive' => 'msg_room_id',
        'typing'  => 't_room_id'
    );
    foreach ($_td as $tbl => $id) {
        $tbd = jrCore_db_table_name('jrChat', $tbl);
        $req = "DELETE FROM {$tbd} WHERE `{$id}` = '{$rid}'";
        jrCore_db_query($req);
    }
    return true;
}

/**
 * Get the room with the latest activity for a user
 * @param $user_id int User ID
 * @return int
 */
function jrChat_get_last_active_room_for_user($user_id)
{
    $uid = (int) $user_id;
    $tbl = jrCore_db_table_name('jrChat', 'room');
    $tbs = jrCore_db_table_name('jrChat', 'slot');
    $req = "SELECT r.room_id FROM {$tbl} r LEFT JOIN {$tbs} s ON s.slot_room_id = r.room_id WHERE ((slot_user_id = '{$uid}' AND slot_blocked = 0) OR r.room_public = 1) ORDER BY r.room_updated DESC LIMIT 1";
    $_rm = jrCore_db_query($req, 'SINGLE');
    if ($_rm && is_array($_rm) && isset($_rm['room_id'])) {
        return $_rm['room_id'];
    }
    return 0;
}

/**
 * Get the number of users in a chat room
 * @param $rid int Room ID
 * @return int
 */
function jrChat_get_room_user_count($rid)
{
    $rid = (int) $rid;
    $tbl = jrCore_db_table_name('jrChat', 'typing');
    $req = "SELECT COUNT(t_user_id) AS cnt FROM {$tbl} WHERE t_room_id = '{$rid}' AND (UNIX_TIMESTAMP() - t_time) < 1200";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt) && isset($_rt['cnt'])) {
        return (int) $_rt['cnt'];
    }
    return 0;
}

/**
 * Return TRUE if a user has permission to a chat room
 * @param $room_id int Room ID
 * @param $user_id int User ID
 * @return mixed false|public|private
 */
function jrChat_user_can_access_room($room_id, $user_id)
{
    $rid = (int) $room_id;
    $uid = (int) $user_id;
    $tbr = jrCore_db_table_name('jrChat', 'room');
    $tbl = jrCore_db_table_name('jrChat', 'slot');
    $req = "SELECT r.room_id, r.room_public, s.slot_id FROM {$tbr} r LEFT JOIN {$tbl} s ON (s.slot_room_id = r.room_id AND slot_user_id = '{$uid}' AND slot_blocked = 0) WHERE r.room_id = '{$rid}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt) && isset($_rt['slot_id']) && jrCore_checktype($_rt['slot_id'], 'number_nz') && $_rt['room_id'] == $room_id) {
        return $_rt;
    }
    return false;
}

/**
 * Get available chat rooms for a user
 * @param $user_id int User ID
 * @return mixed
 */
function jrChat_get_available_rooms_for_user($user_id)
{
    $uid = (int) $user_id;
    $tbl = jrCore_db_table_name('jrChat', 'slot');
    $req = "SELECT slot_room_id FROM {$tbl} WHERE slot_user_id = '{$uid}'";
    return jrCore_db_query($req, 'slot_room_id', false, 'slot_room_id');
}

/**
 * Create a new message in a chat room
 * @param $room_id int Room ID
 * @param $user_id int User ID
 * @param $message string Message
 * @return bool|mixed
 */
function jrChat_create_message($room_id, $user_id, $message)
{
    $rid = (int) $room_id;
    $uid = (int) $user_id;
    $msg = jrCore_db_escape($message);
    $tbl = jrCore_db_table_name('jrChat', 'message');
    $req = "INSERT INTO {$tbl} (msg_room_id, msg_user_id, msg_created, msg_content) VALUES ('{$rid}', '{$uid}', UNIX_TIMESTAMP(), '{$msg}')";
    $mid = jrCore_db_query($req, 'INSERT_ID');
    if ($mid && $mid > 0) {
        return $mid;
    }
    return false;
}

/**
 * Get messages in a chat room
 * @param $room_id int Room ID
 * @param $before_id int Last Message ID
 * @param $search_string string optional search string
 * @return mixed
 */
function jrChat_get_messages($room_id, $before_id, $search_string = null)
{
    global $_conf, $_user;
    $lim = 50;
    $rid = (int) $room_id;
    $lid = (int) $before_id;
    $add = '';
    if ($lid && $lid > 0) {
        $add .= " AND msg_id < {$lid}";
    }
    if (!is_null($search_string)) {
        $add .= " AND msg_content LIKE '%" . jrCore_db_escape($search_string) . "%'";
    }
    $tbl = jrCore_db_table_name('jrChat', 'message');
    $req = "SELECT msg_id AS i, msg_user_id AS u, msg_room_id AS r, msg_created AS t, msg_content as c FROM {$tbl} WHERE msg_room_id = '{$rid}'{$add} ORDER BY t DESC LIMIT {$lim}";
    $_rt = jrCore_db_query($req, 'i');

    if ($_rt && is_array($_rt)) {
        $_rt = array_reverse($_rt);
        $_ui = array();
        foreach ($_rt as $k => $_m) {
            $_ui[] = (int) $_m['u'];
        }
        if (count($_ui) > 0) {
            $_us = jrCore_db_get_multiple_items('jrUser', $_ui, array('_user_id', '_updated', 'user_name', 'user_email', 'user_image_size'));
            if ($_us && is_array($_us)) {
                $gravatar = false;
                if (jrCore_module_is_active('jrGravatar') && function_exists('jrGravatar_img_src_listener')) {
                    $gravatar = true;
                }
                $_un = array();
                foreach ($_us as $_u) {
                    $_un["{$_u['_user_id']}"] = $_u;
                }
                foreach ($_rt as $k => $_m) {
                    $_rt[$k]['n'] = $_un["{$_m['u']}"]['user_name'];
                    $img          = 1;
                    if ($gravatar) {
                        $_data = array();
                        $_args = array(
                            '_item'    => $_un["{$_m['u']}"],
                            'module'   => 'jrUser',
                            'type'     => 'user_image',
                            'size'     => 'small',
                            'url_only' => true
                        );
                        $_data = jrGravatar_img_src_listener($_data, $_user, $_conf, $_args, 'img_src');
                        if (isset($_data['src']) && strlen($_data['src']) > 0) {
                            $img = $_data['src'];
                        }
                    }
                    $_rt[$k]['m'] = $img;
                }
            }
        }
    }
    return $_rt;
}

/**
 * Get NEW messages in a chat room
 * @param $room_id int Room ID
 * @param $last_id int Last Message ID
 * @return mixed
 */
function jrChat_get_new_messages($room_id, $last_id)
{
    global $_conf, $_user;
    $lim = 50;
    $rid = (int) $room_id;
    $lid = (int) $last_id;

    // In order to see if we have NEW messages, we need to get ALL messages across
    // all rooms that this user has access to
    $add = '';
    if (!jrUser_is_admin()) {
        // This is NOT an admin user - restrict to rooms they have access to
        $_rm = jrChat_get_available_rooms_for_user($_user['_user_id']);
        if (!$_rm || !is_array($_rm)) {
            return false;
        }
        $add = ' AND msg_room_id IN(' . implode(',', $_rm) . ')';
    }
    $tbl = jrCore_db_table_name('jrChat', 'message');
    $req = "SELECT msg_id AS i, msg_user_id AS u, msg_room_id AS r, msg_created AS t, msg_content as c FROM {$tbl} WHERE msg_id > {$lid}{$add} ORDER BY t DESC LIMIT {$lim}";
    $_rt = jrCore_db_query($req, 'i');

    $_nm = array();
    if ($_rt && is_array($_rt)) {
        $_rt = array_reverse($_rt);
        $_ui = array();
        foreach ($_rt as $k => $_m) {
            if ($_m['r'] == $rid) {
                // This message is IN the room we are getting messages for
                $_ui[] = (int) $_m['u'];
            }
            // NEW messages array
            if (!isset($_nm["{$_m['r']}"])) {
                $_nm["{$_m['r']}"] = array();
            }
            $_nm["{$_m['r']}"][] = $_m;
        }
        if (count($_ui) > 0) {
            // We have NEW messages in our active room - get user info for users in this room
            $_us = jrCore_db_get_multiple_items('jrUser', $_ui, array('_user_id', '_updated', 'user_name', 'user_email', 'user_image_size'));
            if ($_us && is_array($_us)) {
                $gravatar = false;
                if (jrCore_module_is_active('jrGravatar') && function_exists('jrGravatar_img_src_listener')) {
                    $gravatar = true;
                }
                $_un = array();
                foreach ($_us as $_u) {
                    $_un["{$_u['_user_id']}"] = $_u;
                }
                foreach ($_nm[$rid] as $k => $_m) {
                    $_nm[$rid][$k]['n'] = $_un["{$_m['u']}"]['user_name'];
                    // Check for gravatar image
                    $img = 1;
                    if ($gravatar) {
                        $_data = array();
                        $_args = array(
                            '_item'    => $_un["{$_m['u']}"],
                            'module'   => 'jrUser',
                            'type'     => 'user_image',
                            'size'     => 'small',
                            'url_only' => true
                        );
                        $_data = jrGravatar_img_src_listener($_data, $_user, $_conf, $_args, 'img_src');
                        if (isset($_data['src']) && strlen($_data['src']) > 0) {
                            $img = $_data['src'];
                        }
                    }
                    $_nm[$rid][$k]['m'] = $img;
                }
            }
        }
    }
    return $_nm;
}

/**
 * Get all the chat rooms a user has access to
 * @param $user_id int User ID
 * @param $last_id int Last ID
 * @return mixed
 */
function jrChat_get_rooms_for_user($user_id, $last_id)
{
    $uid = (int) $user_id;
    $tbl = jrCore_db_table_name('jrChat', 'room');
    $tbs = jrCore_db_table_name('jrChat', 'slot');
    if (jrUser_is_admin()) {
        $req = "SELECT * FROM {$tbl} ORDER BY room_public DESC, room_title ASC";
    }
    else {
        $req = "SELECT r.* FROM {$tbs} s LEFT JOIN {$tbl} r ON r.room_id = s.slot_room_id WHERE (s.slot_user_id = '{$uid}' OR r.room_public = 1) ORDER BY r.room_public DESC, r.room_title ASC";
    }
    $_rm = jrCore_db_query($req, 'room_id');
    if ($_rm && is_array($_rm)) {
        $tbl = jrCore_db_table_name('jrChat', 'message');
        $req = "SELECT msg_room_id, COUNT(msg_id) AS c FROM {$tbl} WHERE msg_id > {$last_id} GROUP BY msg_room_id";
        $_rt = jrCore_db_query($req, 'msg_room_id', false, 'c');
        if ($_rt && is_array($_rt)) {
            foreach ($_rm as $rid => $_i) {
                if (isset($_rt[$rid])) {
                    $_rm[$rid]['room_new_count'] = (int) $_rt[$rid];
                }
                else {
                    $_rm[$rid]['room_new_count'] = 0;
                }
            }
        }
    }
    return $_rm;
}

/**
 * Get all configured pubic rooms
 * @return array
 */
function jrChat_get_public_rooms()
{
    // Only public rooms
    $_rm = array(0 => 'disabled');
    $tbl = jrCore_db_table_name('jrChat', 'room');
    $req = "SELECT room_id AS r, room_title AS t FROM {$tbl} WHERE room_public = 1 ORDER BY room_title ASC";
    $_rt = jrCore_db_query($req, 'r', false, 't');
    if ($_rt && is_array($_rt)) {
        $_rm = $_rm + $_rt;
    }
    return $_rm;
}

/**
 * Get users who are currently typing
 * @param $room_id int Room ID
 * @return mixed
 */
function jrChat_get_typing_users($room_id)
{
    $rid = (int) $room_id;
    $tbl = jrCore_db_table_name('jrChat', 'typing');
    $req = "SELECT t_user_id AS u, (UNIX_TIMESTAMP() - t_time) AS t FROM {$tbl} WHERE t_room_id = '{$rid}'";
    return jrCore_db_query($req, 'u', false, 't');
}

/**
 * Try to cleanup imbalanced BBCode tags in new message
 * @param $msg string message
 * @return string
 */
function jrChat_cleanup_message($msg)
{
    // balance bbcode tags
    if (stripos($msg, '[code]') === 0 && !stripos($msg, '[/code]')) {
        $msg .= '[/code]';
    }
    if (stripos($msg, '[quote]') === 0 && !stripos($msg, '[/quote]')) {
        $msg .= '[/quote]';
    }
    return $msg;
}

//-----------------------
// ACTIONS
//-----------------------

/**
 * Process an action on a message
 * @param $message string
 * @return mixed
 */
function jrChat_process_action($message)
{
    if (strpos($message, ':') === 0) {
        $temp = trim(substr($message, 1, strpos($message, ' ')));
        $func = "jrChat_action_{$temp}";
        if (function_exists($func)) {
            // We have an action - process
            return trim($func($message));
        }
    }
    return $message;
}

/**
 * Page (notify) another user in a chat room
 * @param $message string message that contains action
 * @return string
 */
function jrChat_action_page($message)
{
    global $_user;
    // :page brian ...
    // :page everyone
    $temp = '';
    $name = trim(jrCore_string_field($message, 2));
    if ($name == '!everyone') {
        $temp .= "~page:everyone~{ ";
        $name = 'everyone';
    }
    else {
        // Next, we need to get the USER id for this user
        $_usr = jrCore_db_get_item_by_key('jrUser', 'user_name', $name, true);
        if ($_usr && is_array($_usr)) {
            $temp .= "~page:{$_usr['_user_id']}~{ ";
        }
    }
    $_ln  = jrUser_load_lang_strings();
    $temp = $temp . "&#9734; <b>{$_user['user_name']}</b> {$_ln['jrChat'][34]} <b>{$name}</b> &#9734; " . trim(mb_substr($message, mb_strpos($message, ' ' . $name) + mb_strlen($name) + 1));
    return $temp;
}

/**
 * Get attachment sizes
 */
function jrChat_get_allowed_attachment_sizes()
{
    $_todo = array();
    foreach (array(1, 2, 4, 8, 12, 16, 20, 24, 32, 64, 128, 256) as $mb) {
        $_todo[] = ($mb * 1048576);
    }
    $_out = array();
    $cmax = jrCore_get_max_allowed_upload();
    foreach ($_todo as $size) {
        if ($size <= $cmax) {
            $_out[$size] = jrCore_format_size($size);
        }
    }
    return $_out;
}
