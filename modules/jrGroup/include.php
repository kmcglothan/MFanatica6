<?php
/**
 * Jamroom Groups module
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

/**
 * meta
 */
function jrGroup_meta()
{
    $_tmp = array(
        'name'        => 'Groups',
        'url'         => 'group',
        'version'     => '1.5.16',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Provides low level support for Profile Groups and Group modules',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2905/groups',
        'license'     => 'jcl',
        'priority'    => 249,
        'category'    => 'profiles'
    );
    return $_tmp;
}

/**
 * init
 */
function jrGroup_init()
{
    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrGroup', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrGroup', 'update');

    // We have fields that can be searched
    jrCore_register_module_feature('jrSearch', 'search_fields', 'jrGroup', 'group_title', 1);

    // Register our custom JS and CSS
    jrCore_register_module_feature('jrCore', 'javascript', 'jrGroup', 'jrGroup.js');
    jrCore_register_module_feature('jrCore', 'css', 'jrGroup', 'jrGroup.css');

    // Core support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrGroup', 'on');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrGroup', 'on');
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrGroup', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrGroup', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrGroup', 'update', 'item_action.tpl');

    jrCore_register_event_listener('jrCore', 'db_create_item', 'jrGroup_db_create_item_listener');
    jrCore_register_event_listener('jrCore', 'db_get_item', 'jrGroup_db_get_item_listener');
    jrCore_register_event_listener('jrCore', 'db_delete_item', 'jrGroup_db_delete_item_listener');
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrGroup_db_search_items_listener');
    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrGroup_db_search_params_listener');
    jrCore_register_event_listener('jrCore', 'db_update_item', 'jrGroup_db_update_item_listener');
    jrCore_register_event_listener('jrChangeOwner', 'owner_changed', 'jrGroup_owner_changed_listener');

    // Recycle Bin
    jrCore_register_event_listener('jrCore', 'empty_recycle_bin', 'jrGroup_empty_recycle_bin_listener');
    jrCore_register_event_listener('jrCore', 'expire_recycle_bin', 'jrGroup_expire_recycle_bin_listener');
    jrCore_register_event_listener('jrCore', 'restore_recycle_bin_item', 'jrGroup_restore_recycle_bin_item_listener');

    // Verify listener
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrGroup_verify_module_listener');

    // System Reset listener
    jrCore_register_event_listener('jrDeveloper', 'reset_system', 'jrGroup_reset_system_listener');

    // Check for private Groups
    jrCore_register_event_listener('jrProfile', 'item_index_view', 'jrGroup_item_index_view_listener');
    jrCore_register_event_listener('jrProfile', 'item_detail_view', 'jrGroup_item_detail_view_listener');

    // Actions
    jrCore_register_event_listener('jrAction', 'action_save', 'jrGroup_action_save_listener');

    // Hide actions on private groups
    jrCore_register_event_listener('jrCore', 'form_display', 'jrGroup_form_display_listener');

    // RSS Format
    jrCore_register_event_listener('jrFeed', 'create_rss_feed', 'jrGroup_create_rss_feed_listener');

    // Hand off private comments
    jrCore_register_event_listener('jrComment', 'private_item_ids', 'jrGroup_private_item_ids_listener');

    // event triggers
    jrCore_register_event_trigger('jrGroup', 'user_config', 'Fired when viewing the group config for a user');
    jrCore_register_event_trigger('jrGroup', 'user_config_defaults', 'Fired when getting a user config so modules can add default values');

    // notifications
    $_tmp = array(
        'label' => 53,
        'help'  => 54
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrGroup', 'pending_application', $_tmp);
    $_tmp = array(
        'label' => 55,
        'help'  => 56
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrGroup', 'user_leaving', $_tmp);
    $_tmp = array(
        'label' => 57,
        'help'  => 58
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrGroup', 'accepted_application', $_tmp);

    // Skin menu link to 'groups you follow'
    $_tmp = array(
        'group' => 'user',
        'label' => 66, // 'Groups You Follow'
        'url'   => 'my_groups'
    );
    jrCore_register_module_feature('jrCore', 'skin_menu_item', 'jrGroup', 'my_groups', $_tmp);

    // Integrity Check worker
    jrCore_register_queue_worker('jrGroup', 'verify_module', 'jrGroup_verify_module_worker', 0, 1, 14400);

    // Register our tools
    if (jrCore_module_is_active('jrGroupDiscuss')) {
        jrCore_register_module_feature('jrCore', 'tool_view', 'jrGroup', 'transfer_discuss', array('Transfer Group Discussion', 'Transfer group discussion(s) to another group'));
    }
    if (jrCore_module_is_active('jrGroupPage')) {
        jrCore_register_module_feature('jrCore', 'tool_view', 'jrGroup', 'transfer_page', array('Transfer Group Page', 'Transfer group page(s) to another group'));
    }

    return true;
}

//----------------------
// QUEUE WORKER
//----------------------

/**
 * Validate group membership
 * @param $_queue
 * @return mixed
 */
function jrGroup_verify_module_worker($_queue)
{
    // Populate member table with existing members
    $_sp = array(
        'search'      => array(
            'group_members like %{%'
        ),
        'return_keys' => array('_item_id', 'group_members'),
        'limit'       => 10000
    );
    $_rt = jrCore_db_search_items('jrGroup', $_sp);
    if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
        $_dl = array();
        $tbl = jrCore_db_table_name('jrGroup', 'member');
        $_mf = jrCore_get_registered_module_features('jrGroup', 'group_support');
        foreach ($_rt['_items'] as $rt) {
            if (isset($rt['group_members']) && strlen($rt['group_members']) > 2) {
                $_gm = json_decode($rt['group_members'], true);
                if ($_gm && is_array($_gm)) {

                    // Get user accounts that still exist for this group
                    $_us = array();
                    $_tm = jrCore_db_get_multiple_items('jrUser', array_keys($_gm), array('_user_id', 'user_name'));
                    if ($_tm && is_array($_tm)) {
                        foreach ($_tm as $_u) {
                            $_us["{$_u['_user_id']}"] = $_u['user_name'];
                        }
                    }

                    foreach ($_gm as $uid => $member) {
                        if (isset($_us[$uid])) {
                            $member_more   = '';
                            $member_status = 1;
                            if ($member['status'] != 'active') {
                                $member_status = 0;
                            }
                            $_member_more = array();
                            if ($_mf && is_array($_mf)) {
                                foreach ($_mf as $mod => $ignore) {
                                    if (isset($member["{$mod}_config_allowed"])) {
                                        $_member_more["{$mod}_config_allowed"] = $member["{$mod}_config_allowed"];
                                    }
                                }
                                if (count($_member_more) > 0) {
                                    $member_more = json_encode($_member_more);
                                }
                            }
                            $member_joined = time();
                            if (isset($member['joined'])) {
                                $member_joined = (int) $member['joined'];
                            }
                            $req = "INSERT IGNORE INTO {$tbl} (member_created,member_user_id,member_group_id,member_status,member_active,member_more) VALUES ('{$member_joined}','{$uid}','{$rt['_item_id']}','{$member_status}',1,'{$member_more}')";
                            jrCore_db_query($req);
                        }
                    }
                }
            }
            $_dl[] = $rt['_item_id'];
        }
        // Remove the group_members keys for the items we have converted
        if (count($_dl) > 0) {
            jrCore_db_delete_key_from_multiple_items('jrGroup', $_dl, array('group_members'));
            jrCore_logger('INF', "converted " . count($_dl) . " Profile Groups to new member structure");
        }
    }

    // Validate that existing membership only includes real users
    $tbl = jrCore_db_table_name('jrGroup', 'member');
    $req = "SELECT member_user_id FROM {$tbl} GROUP by member_user_id";
    $_gm = jrCore_db_query($req, 'member_user_id', false, 'member_user_id');
    if ($_gm && is_array($_gm) && count($_gm) > 0) {
        $_us = jrCore_db_get_multiple_items('jrUser', $_gm, '_user_id');
        if ($_us && is_array($_us)) {
            foreach ($_us as $_u) {
                unset($_gm["{$_u['_user_id']}"]);
            }
            if (count($_gm) > 0) {
                $req = "DELETE FROM {$tbl} WHERE member_user_id IN(" . implode(',', $_gm) . ")";
                jrCore_db_query($req);
            }
        }
    }

    // Make sure group_member_count is correct - used for sorting
    $tbl = jrCore_db_table_name('jrGroup', 'member');
    $req = "SELECT member_group_id AS gid, COUNT(member_id) AS cnt FROM {$tbl} WHERE member_status = 1 AND member_active = 1 GROUP by member_group_id";
    $_gm = jrCore_db_query($req, 'gid', false, 'cnt');
    if ($_gm && is_array($_gm)) {
        $_up = array();
        foreach ($_gm as $gid => $cnt) {
            $_up[$gid] = array('group_member_count' => $cnt);
        }
        if (count($_up) > 0) {
            jrCore_db_update_multiple_items('jrGroup', $_up);
        }
    }

    return true;
}

//----------------------
// EVENT LISTENERS
//----------------------

/**
 * Get private item ids
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroup_private_item_ids_listener($_data, $_user, $_conf, $_args, $event)
{
    $_sc = array(
        'search'              => array(
            'group_private = on'
        ),
        'return_item_id_only' => true,
        'skip_triggers'       => true,
        'quota_check'         => false,
        'privacy_check'       => false,
        'ignore_pending'      => true,
        'order_by'            => false,
        'limit'               => 500000
    );
    $_sc = jrCore_db_search_items('jrGroup', $_sc);
    if ($_sc && is_array($_sc)) {
        $_data['jrGroup'] = $_sc;
    }
    return $_data;
}

/**
 * Owner Changed listener
 * @param $_data array incoming data array of item including original owner profile and user IDs
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array of new owner profile and user IDs
 * @param $event string Event Trigger name
 * @return array
 */
function jrGroup_owner_changed_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['module']) && $_args['module'] == 'jrChangeOwner' && isset($_args['target_module']) && $_args['target_module'] == 'jrGroup') {

        // Remove the original owner from member list and add new owner
        $tbl = jrCore_db_table_name('jrGroup', 'member');
        $req = "DELETE FROM {$tbl} WHERE `member_user_id` = '{$_data['_user_id']}' AND `member_group_id` = '{$_data['_item_id']}' LIMIT 1";

        jrCore_db_query($req);
        $req = "INSERT INTO {$tbl} (member_created, member_user_id, member_group_id, member_status, member_active, member_more)
                VALUES (UNIX_TIMESTAMP(), '{$_args['_user_id']}', '{$_data['_item_id']}', '1', '1', '')
                ON DUPLICATE KEY UPDATE member_created = UNIX_TIMESTAMP()";
        jrCore_db_query($req);
    }
    return $_data;
}

/**
 * System Reset listener
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGroup_reset_system_listener($_data, $_user, $_conf, $_args, $event)
{
    $tbl = jrCore_db_table_name('jrGroup', 'member');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");
    return $_data;
}

/**
 * Empty Recycle Bin
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGroup_empty_recycle_bin_listener($_data, $_user, $_conf, $_args, $event)
{
    $tbl = jrCore_db_table_name('jrGroup', 'member');
    $req = "DELETE FROM {$tbl} WHERE member_active = '0'";
    jrCore_db_query($req);
    return $_data;
}

/**
 * Expire Recycle Bin Items
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGroup_expire_recycle_bin_listener($_data, $_user, $_conf, $_args, $event)
{
    if (is_array($_data) && is_array($_data['_items']) && count($_data['_items']) > 0) {
        $_gr = array();
        $_id = array();
        foreach ($_data['_items'] as $k => $_it) {
            switch ($_it['module']) {
                case 'jrGroup':
                    $_gr[] = $_it['item_id'];
                    break;
                case 'jrUser':
                    $_id[] = $_it['item_id'];
                    break;
            }
        }
        $tbl = jrCore_db_table_name('jrGroup', 'member');
        if (count($_gr) > 0) {
            $req = "DELETE FROM {$tbl} WHERE member_group_id IN(" . implode(',', $_gr) . ')';
            jrCore_db_query($req);
        }
        if (count($_id) > 0) {
            $req = "DELETE FROM {$tbl} WHERE member_user_id IN(" . implode(',', $_id) . ')';
            jrCore_db_query($req);
        }
    }
    return $_data;
}

/**
 * Restore Recycle Bin Item
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGroup_restore_recycle_bin_item_listener($_data, $_user, $_conf, $_args, $event)
{
    switch ($_args['module']) {

        case 'jrGroup':
            // A group is being restored - undelete users in group
            $tbl = jrCore_db_table_name('jrGroup', 'member');
            $req = "UPDATE {$tbl} SET member_active = '1' WHERE member_group_id = '" . intval($_args['item_id']) . "'";
            jrCore_db_query($req);
            break;

        case 'jrUser':
            // Restore this user's place in their groups
            $tbl = jrCore_db_table_name('jrGroup', 'member');
            $req = "UPDATE {$tbl} SET member_active = '1' WHERE member_user_id = '" . intval($_args['item_id']) . "'";
            jrCore_db_query($req);
    }
    return $_data;
}

/**
 * Format RSS entries
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGroup_create_rss_feed_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    // We override the "description" and format it differently
    $_mf = jrCore_get_registered_module_features('jrGroup', 'group_support');
    if ($_mf && is_array($_mf) && isset($_mf["{$_args['module']}"])) {
        $pfx = jrCore_db_get_prefix($_args['module']);

        // See if we are redoing all the items (discussion item)
        if (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {

            $_gi = jrCore_db_get_item($_args['module'], $_post['id']);
            if ($_gi && is_array($_gi)) {

                // Make sure this is not part of a private group being accessed by a non-member
                if (!jrUser_is_admin() && isset($_gi['group_private']) && $_gi['group_private'] == 'on') {
                    if (!jrUser_is_logged_in()) {
                        // Must be logged in
                        jrUser_session_require_login();
                    }
                    elseif (!jrGroup_member_has_access($_gi)) {
                        // Not a member
                        $murl = jrCore_get_module_url('jrGroup');
                        jrCore_location("{$_conf['jrCore_base_url']}/{$murl}/private_notice/{$_gi['_item_id']}");
                    }
                }

                $_data   = array();
                $_data[] = array(
                    'title'       => "@{$_gi['profile_url']}: " . jrCore_entity_string($_gi['group_title']) . " - " . jrCore_entity_string(jrCore_strip_html($_gi["{$pfx}_title"])),
                    'link'        => "{$_conf['jrCore_base_url']}/{$_gi['profile_url']}/{$_post['option']}/{$_post['id']}/" . $_gi["{$pfx}_title_url"],
                    'description' => jrCore_entity_string(jrCore_replace_emoji(jrCore_strip_html(smarty_modifier_jrCore_format_string($_gi["{$pfx}_description"], 0)))),
                    'pubdate'     => strftime("%a, %d %b %Y %T %z", $_gi['_created'])
                );
                $_sp     = array(
                    'search'                       => array(
                        "comment_item_id = {$_post['id']}",
                        "comment_module = {$_args['module']}"
                    ),
                    'order_by'                     => array(
                        '_created' => 'asc'
                    ),
                    'exclude_jrUser_keys'          => true,
                    'exclude_jrProfile_quota_keys' => true,
                    'limit'                        => 25
                );
                $_sp     = jrCore_db_search_items('jrComment', $_sp);
                if ($_sp && is_array($_sp) && isset($_sp['_items'])) {
                    $url = jrCore_get_module_url($_args['module']);
                    foreach ($_sp['_items'] as $k => $_itm) {
                        $_data[] = array(
                            'title'       => "@{$_itm['original_profile_name']}: " . jrCore_entity_string(jrCore_strip_html($_itm['comment_text'])),
                            'link'        => "{$_conf['jrCore_base_url']}/{$_gi['profile_url']}/{$url}/{$_gi['_item_id']}/{$_gi["{$pfx}_title_url"]}#cm{$_itm['_item_id']}",
                            'description' => jrCore_entity_string(jrCore_replace_emoji(jrCore_strip_html(smarty_modifier_jrCore_format_string($_itm['comment_text'], 0)))),
                            'pubdate'     => strftime("%a, %d %b %Y %T %z", $_itm['_created']),
                        );
                    }
                }
            }
        }
        else {
            if (!isset($_post["{$pfx}_group_id"]) || !jrCore_checktype($_post["{$pfx}_group_id"], 'number_nz')) {
                jrCore_page_not_found();
            }
            $_gr = jrCore_db_get_item('jrGroup', $_post["{$pfx}_group_id"]);
            if (!jrUser_is_admin() && isset($_gr['group_private']) && $_gr['group_private'] == 'on') {
                if (!jrUser_is_logged_in()) {
                    // Must be logged in
                    jrUser_session_require_login();
                }
                elseif (!jrGroup_member_has_access($_gr)) {
                    // Not a member
                    $murl = jrCore_get_module_url('jrGroup');
                    jrCore_location("{$_conf['jrCore_base_url']}/{$murl}/private_notice/{$_gr['_item_id']}");
                }
            }
            foreach ($_data as $k => $_itm) {
                if ($_itm["{$pfx}_group_id"] != $_post["{$pfx}_group_id"]) {
                    unset($_data[$k]);
                    continue;
                }
                $_data[$k]['title'] = "{$_gr['group_title']} - {$_itm["{$pfx}_title"]}";
            }
        }
    }
    return $_data;
}

/**
 * Remove Action checkbox from create/update forms on Private Groups
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroup_form_display_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (isset($_post['group_id']) && jrCore_checktype($_post['group_id'], 'number_nz')) {
        $mod = $_post['module'];
        $_mf = jrCore_get_registered_module_features('jrGroup', 'group_support');
        if ($_mf && is_array($_mf) && isset($_mf[$mod])) {
            $_gr = jrCore_db_get_item('jrGroup', $_post['group_id'], true);
            if ($_gr && is_array($_gr) && isset($_gr['group_private']) && $_gr['group_private'] == 'on') {
                $_tm = jrCore_get_flag('jrcore_register_module_feature');
                if (isset($_tm['jrCore']['action_support'][$mod])) {
                    unset($_tm['jrCore']['action_support'][$mod]);
                    jrCore_set_flag('jrcore_register_module_feature', $_tm);
                }
            }
        }
    }
    return $_data;
}

/**
 * Prevent actions from being created for comments posted on items associated with a private group
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroup_action_save_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['comment_module']{1})) {
        if ($_args['comment_module'] == 'jrGroup') {
            // If this is a private group, no time line entry
            $_ti = jrCore_db_get_item('jrGroup', $_args['comment_item_id'], true);
            if ($_ti && is_array($_ti) && isset($_ti['group_private']) && $_ti['group_private'] == 'on') {
                $_data['jraction_add_to_timeline'] = 'off';
            }
        }
        else {
            $_mf = jrCore_get_registered_module_features('jrGroup', 'group_support');
            if ($_mf && is_array($_mf) && isset($_mf["{$_args['comment_module']}"])) {
                if ($pfx = jrCore_db_get_prefix($_args['comment_module'])) {
                    $_ti = jrCore_db_get_item($_args['comment_module'], $_args['comment_item_id']);
                    if ($_ti && is_array($_ti) && isset($_ti['group_private']) && $_ti['group_private'] == 'on') {
                        $_data['jraction_add_to_timeline'] = 'off';
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * Check for Private Group membership
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroup_item_index_view_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post, $_urls;
    if (!jrUser_is_admin()) {
        if (isset($_post['option']) && isset($_urls["{$_post['option']}"])) {
            $mod = $_urls["{$_post['option']}"];
            $_mf = jrCore_get_registered_module_features('jrGroup', 'group_support');
            if ($_mf && is_array($_mf) && isset($_mf[$mod])) {
                // We have a Group Module index view - check for privacy
                $_ti = false;
                $chk = true;
                if (isset($_post['group_id']) && jrCore_checktype($_post['group_id'], 'number_nz')) {
                    $_ti = jrCore_db_get_item('jrGroup', $_post['group_id']);
                    if ($_ti && is_array($_ti) && isset($_ti['group_private']) && $_ti['group_private'] == 'off') {
                        $chk = false;
                    }
                }
                if ($chk) {
                    if (!jrUser_is_logged_in()) {
                        // Must be logged in
                        jrUser_session_require_login();
                    }
                    elseif ($_ti && !jrGroup_member_has_access($_ti)) {
                        // Not a member
                        $murl = jrCore_get_module_url('jrGroup');
                        jrCore_location("{$_conf['jrCore_base_url']}/{$murl}/private_notice/{$_data['_item_id']}");
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * Check for Private Group membership
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroup_item_detail_view_listener($_data, $_user, $_conf, $_args, $event)
{
    if (!jrUser_is_admin() && isset($_data['group_private']) && $_data['group_private'] == 'on') {
        if (!jrUser_is_logged_in()) {
            // Must be logged in
            jrUser_session_require_login();
        }
        elseif (!jrGroup_member_has_access($_data)) {
            // Not a member
            $murl = jrCore_get_module_url('jrGroup');
            jrCore_location("{$_conf['jrCore_base_url']}/{$murl}/private_notice/{$_data['_item_id']}");
        }
    }
    return $_data;
}

/**
 * Don't set pending on group "joining" updates
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroup_db_update_item_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (isset($_args['module']) && $_args['module'] == 'jrGroup' && isset($_post['option']) && ($_post['option'] == 'button' || $_post['option'] == 'user_config_save' || $_post['option'] == 'delete_user_save')) {
        // This is an "joining" request - make sure pending is not active on this update
        if (isset($_data['group_pending'])) {
            unset($_data['group_pending']);
        }
    }
    return $_data;
}

/**
 * Integrity Check tasks
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroup_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    // Bad menu entry
    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $req = "DELETE FROM {$tbl} WHERE `menu_module` IN('jrGroup','jrNingGroup') AND `menu_unique` = 'group_members_link'";
    jrCore_db_query($req);

    // Group membership validation
    $_queue = array('module' => 'jrGroup');
    jrCore_queue_create('jrGroup', 'verify_module', $_queue);
    return $_data;
}

/**
 * Enforce privacy on group modules
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroup_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (!jrUser_is_admin()) {

        // If this is a COMMENT listing on a group module, we can skip privacy check
        $_mf = jrCore_get_registered_module_features('jrGroup', 'group_support');
        if (isset($_args['module']) && $_args['module'] == 'jrComment' && jrUser_is_logged_in()) {
            if (isset($_post['item_module']) && ($_post['item_module'] == 'jrGroup' || isset($_mf["{$_post['item_module']}"]))) {
                // This is a view_comment listing on a group module - check that the user has access to the group
                if ($_post['item_module'] == 'jrGroupDiscuss') {
                    $_group = jrCore_db_get_item('jrGroupDiscuss', $_post['item_id']);
                    $gid    = (int) $_group['discuss_group_id'];
                }
                else {
                    $gid = (int) $_post['item_id'];
                }
                $uid = (int) $_user['_user_id'];
                $tbl = jrCore_db_table_name('jrGroup', 'member');
                $req = "SELECT * FROM {$tbl} WHERE member_group_id = {$gid} AND member_user_id = {$uid} AND member_active = 1 LIMIT 1";
                $_rt = jrCore_db_query($req, 'SINGLE');
                if ($_rt && is_array($_rt)) {
                    // User has access to group - allow all comments
                    $_data['privacy_check'] = false;
                }
            }
        }
        else {
            // See if this listing is for a Group module
            if ($_mf && is_array($_mf) && isset($_mf["{$_args['module']}"])) {
                if ($pfx = jrCore_db_get_prefix($_args['module'])) {

                    // We want to NOT SHOW registered items that are part of a private group unless the user is the OWNER or a MEMBER
                    if (jrUser_is_logged_in()) {
                        // Get public groups and groups they own or are members of
                        $tbl  = jrCore_db_table_name('jrGroup', 'member');
                        $req  = "SELECT * FROM {$tbl} WHERE `member_user_id` = '{$_user['_user_id']}'";
                        $_rt  = jrCore_db_query($req, 'NUMERIC');
                        $_gid = array();
                        if ($_rt && is_array($_rt) && count($_rt) > 0) {
                            foreach ($_rt as $rt) {
                                $_gid["{$rt['member_group_id']}"] = $rt['member_group_id'];
                            }
                            $gid = implode(',', $_gid);
                            $_sp = array(
                                'search'              => array(
                                    "group_private = off || _profile_id = {$_user['_profile_id']} || _item_id in {$gid}"
                                ),
                                'skip_triggers'       => true,
                                'return_item_id_only' => true,
                                'limit'               => 100000
                            );
                        }
                        else {
                            $_sp = array(
                                'search'              => array(
                                    "group_private = off || _profile_id = {$_user['_profile_id']}"
                                ),
                                'skip_triggers'       => true,
                                'return_item_id_only' => true,
                                'limit'               => 100000
                            );
                        }
                    }
                    else {
                        // Get public groups only
                        $_sp = array(
                            'search'              => array(
                                'group_private = off'
                            ),
                            'skip_triggers'       => true,
                            'return_item_id_only' => true,
                            'limit'               => 100000
                        );
                    }
                    $_rt = jrCore_db_search_items('jrGroup', $_sp);
                    if ($_rt && is_array($_rt)) {
                        if (!isset($_data['search']) || !is_array($_data['search'])) {
                            $_data['search'] = array();
                        }
                        $_data['search'][] = "{$pfx}_group_id in " . implode(',', $_rt);
                    }
                    else {
                        // There are no groups for them to view - set condition for no match
                        $_data['search'][] = "_item_id < 0";
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * 'Comment Membership' listener
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroup_db_create_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrComment' && ($_data['comment_module'] == 'jrGroup' || $_data['comment_module'] == 'jrGroupDiscuss') && isset($_conf['jrGroup_comment_membership']) && $_conf['jrGroup_comment_membership'] == 'on') {
        // Get group
        if ($_data['comment_module'] == 'jrGroup') {
            $_gr = jrCore_db_get_item('jrGroup', $_data['comment_item_id']);
        }
        else {
            $_gr = jrCore_db_get_item('jrGroup', _jrCore_db_get_item_key('jrGroupDiscuss', $_data['comment_item_id'], 'discuss_group_id'));
        }
        if (!jrGroup_member_has_access($_gr) && $_gr['group_private'] != 'on') {
            // Make the commenter a group member
            $tbl = jrCore_db_table_name('jrGroup', 'member');
            $req = "INSERT INTO {$tbl} (member_created, member_user_id, member_group_id, member_status) VALUES (UNIX_TIMESTAMP(), '{$_data['_user_id']}', '{$_gr['_item_id']}', '1')";
            if (jrCore_db_query($req, 'COUNT')) {
                jrCore_db_increment_key('jrGroup', $_gr['_item_id'], 'group_member_count', 1);
            }
        }
    }
    return $_data;
}

/**
 * Expand Group Members
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroup_db_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_is_view_request()) {
        if (isset($_args['module']) && $_args['module'] == 'jrGroup') {
            // Add in Group Member info - but not if this is an image check
            $_data['group_member']       = jrGroup_get_group_members($_data['_item_id']);
            $_data['group_member_count'] = count($_data['group_member']);
        }
        else {
            // See if this is a registered group module
            $_mf = jrCore_get_registered_module_features('jrGroup', 'group_support');
            if ($_mf && is_array($_mf) && isset($_mf["{$_args['module']}"])) {
                // This is a groups module - add Group info
                if ($pfx = jrCore_db_get_prefix($_args['module'])) {
                    if (isset($_data["{$pfx}_group_id"]) && jrCore_checktype($_data["{$pfx}_group_id"], 'number_nz')) {
                        $_gr = jrCore_db_get_item('jrGroup', $_data["{$pfx}_group_id"]);
                        if ($_gr && is_array($_gr)) {
                            foreach ($_gr as $k => $v) {
                                // Override item with profile info for hosting profile
                                if (strpos($k, 'profile_') === 0) {
                                    if (isset($_data[$k])) {
                                        $_data["original_{$k}"] = $_data[$k];
                                    }
                                    $_data[$k] = $v;
                                }
                                elseif (strpos($k, 'group_') === 0 || strpos($k, 'quota_') === 0 || $k == '_profile_id') {
                                    $_data[$k] = $v;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * Add group info to group modules
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroup_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    // See if this is a registered group module
    $_mf = jrCore_get_registered_module_features('jrGroup', 'group_support');
    if ($_mf && is_array($_mf) && isset($_mf["{$_args['module']}"])) {

        // This is a groups module - add Group info
        $_gi = array();
        if ($pfx = jrCore_db_get_prefix($_args['module'])) {

            foreach ($_data['_items'] as $v) {
                if (isset($v["{$pfx}_group_id"]) && jrCore_checktype($v["{$pfx}_group_id"], 'number_nz')) {
                    $gid       = (int) $v["{$pfx}_group_id"];
                    $_gi[$gid] = $gid;
                }
            }
            if (count($_gi) > 0) {
                $_gr = array(
                    'search'         => array(
                        '_item_id in ' . implode(',', $_gi)
                    ),
                    'order_by'       => false,
                    'privacy_check'  => false,
                    'ignore_pending' => true,
                    'limit'          => count($_gi)
                );
                $_gr = jrCore_db_search_items('jrGroup', $_gr);
                if ($_gr && is_array($_gr) && isset($_gr['_items'])) {
                    $_gx = array();
                    foreach ($_gr['_items'] as $_group) {
                        $gid       = (int) $_group['_item_id'];
                        $_gx[$gid] = $_group;

                    }
                    foreach ($_data['_items'] as $k => $v) {
                        if (isset($v["{$pfx}_group_id"])) {
                            $gid = (int) $v["{$pfx}_group_id"];
                            if (isset($_gx[$gid])) {
                                $_data['_items'][$k]['_group_data'] = $_gx[$gid];
                            }
                        }
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * Delete Group items when a group is deleted
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroup_db_delete_item_listener($_data, $_user, $_conf, $_args, $event)
{
    // A group is being deleted - delete items in group modules
    if ($_args['module'] == 'jrGroup' && jrCore_checktype($_args['_item_id'], 'number_nz')) {
        $_mf = jrCore_get_registered_module_features('jrGroup', 'group_support');
        if ($_mf && is_array($_mf)) {
            foreach ($_mf as $mod => $ignore) {
                if ($pfx = jrCore_db_get_prefix($mod)) {
                    $_sp = array(
                        'search'              => array(
                            "{$pfx}_group_id = {$_args['_item_id']}"
                        ),
                        'return_item_id_only' => true,
                        'skip_triggers'       => true,
                        'limit'               => 25000
                    );
                    $_rt = jrCore_db_search_items($mod, $_sp);
                    if ($_rt && is_array($_rt)) {
                        jrCore_db_delete_multiple_items($mod, $_rt);
                    }
                }
            }
        }
        // flag users in this group as deleted...
        $tbl = jrCore_db_table_name('jrGroup', 'member');
        $req = "UPDATE {$tbl} SET member_active = '0' WHERE `member_group_id` = {$_args['_item_id']}";
        jrCore_db_query($req);
    }
    // A user is being deleted
    elseif ($_args['module'] == 'jrUser' && jrCore_checktype($_data['_user_id'], 'number_nz')) {
        // flag user as deleted...
        $tbl = jrCore_db_table_name('jrGroup', 'member');
        $req = "UPDATE {$tbl} SET member_active = '0' WHERE `member_user_id` = {$_data['_user_id']}";
        jrCore_db_query($req);
    }
    return $_data;
}

//----------------------
// FUNCTIONS
//----------------------

/**
 * Get private groups the viewer cannot see
 * @return array|bool|mixed
 */
function jrGroup_get_private_groups()
{
    $_sc = array(
        'search'              => array(
            'group_private = on'
        ),
        'return_item_id_only' => true,
        'skip_triggers'       => true,
        'quota_check'         => false,
        'privacy_check'       => false,
        'ignore_pending'      => true,
        'limit'               => 100000
    );
    $_sc = jrCore_db_search_items('jrGroup', $_sc);
    if ($_sc && is_array($_sc)) {
        return $_sc;
    }
    return false;
}

/**
 * Get Groups a user belongs to
 * @param $user_id int
 * @return mixed
 */
function jrGroup_get_user_groups($user_id)
{
    $uid = (int) $user_id;
    $tbl = jrCore_db_table_name('jrGroup', 'member');
    $req = "SELECT member_group_id AS m FROM {$tbl} WHERE member_user_id = '{$uid}'";
    $_rt = jrCore_db_query($req, 'm', false, 'm');
    if ($_rt && is_array($_rt)) {
        return array_keys($_rt);
    }
    return false;
}

/**
 * Validate the group_member_count key for a group
 * @param $group_id int
 * @return bool
 */
function jrGroup_validate_group_member_count($group_id)
{
    $cnt = 0;
    $gid = (int) $group_id;
    $tbl = jrCore_db_table_name('jrGroup', 'member');
    $req = "SELECT COUNT(member_id) AS cnt FROM {$tbl} WHERE member_group_id = '{$gid}' AND member_status = 1 AND member_active = 1";
    $_gm = jrCore_db_query($req, 'SINGLE');
    if ($_gm && is_array($_gm) && isset($_gm['cnt'])) {
        $cnt = (int) $_gm['cnt'];
    }
    return jrCore_db_update_item('jrGroup', $gid, array('group_member_count' => $cnt));
}

/**
 * Get group member information for a group
 * @param $group_id int Group ID to get members for
 * @return array
 */
function jrGroup_get_group_members($group_id)
{
    global $_post;
    // Expand Group Members
    if (!$_out = jrCore_get_flag("jrGroup_get_group_members_{$group_id}")) {
        $_us = array();
        $tbl = jrCore_db_table_name('jrGroup', 'member');
        $req = "SELECT * FROM {$tbl} WHERE `member_group_id` = '{$group_id}' AND `member_active` = '1' ORDER BY `member_id` ASC";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt) && count($_rt) > 0) {
            foreach ($_rt as $rt) {
                // Expand member_more
                if (strlen($rt['member_more']) > 2) {
                    $_mm = json_decode($rt['member_more'], true);
                    if ($_mm && is_array($_mm) && count($_mm) > 0) {
                        foreach ($_mm as $k => $v) {
                            $rt["{$k}"] = $v;
                        }
                    }
                }
                unset($rt['member_more']);
                $_us["{$rt['member_user_id']}"] = $rt;
            }
        }
        if (count($_us) > 0) {
            $_rt = array(
                'search'                       => array(
                    '_user_id in ' . implode(',', array_keys($_us))
                ),
                'return_keys'                  => array('_user_id', 'user_name', '_profile_id', 'profile_name', 'profile_url', 'profile_quota_id'),
                'ignore_pending'               => true,
                'include_jrProfile_keys'       => true,
                'exclude_jrProfile_quota_keys' => true,
                'limit'                        => count($_us)
            );
            if (isset($_post['ss'])) {
                $_rt['search'][] = 'user_name like %' . $_post['ss'] . '% || profile_name like %' . $_post['ss'] . '%';
                $_rt['no_cache'] = true;
            }
            $_rt = jrCore_db_search_items('jrUser', $_rt);
            if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
                $_out = array();
                foreach ($_rt['_items'] as $v) {
                    $_out["{$v['_user_id']}"] = array_merge($v, $_us["{$v['_user_id']}"]);
                }
            }
            jrCore_set_flag("jrGroup_get_group_members_{$group_id}", $_out);
        }
    }
    return $_out;
}

/**
 * Get a specific group user config option
 * @param $module string Group Module
 * @param $option string Config option
 * @param $_group array Group Info
 * @param $user_id int User ID
 * @return bool
 */
function jrGroup_get_user_config($module, $option, $_group, $user_id)
{
    if (isset($_group['group_member'][$user_id]["{$module}_config_{$option}"])) {
        return $_group['group_member'][$user_id]["{$module}_config_{$option}"];
    }
    $_arg = array(
        '_user_id' => $user_id,
        '_group'   => $_group
    );
    $_def = jrCore_trigger_event('jrGroup', 'user_config_defaults', array(), $_arg);
    if (isset($_def["{$module}_config_{$option}"])) {
        return $_def["{$module}_config_{$option}"];
    }
    return false;
}

/**
 * Check if a user has access to a Group
 * @param $_group
 * @return bool
 */
function jrGroup_member_has_access($_group)
{
    global $_user;
    if (jrUser_is_admin() || jrProfile_is_profile_owner($_group['_profile_id']) || (isset($_group['group_member']["{$_user['_user_id']}"]['member_status']) && $_group['group_member']["{$_user['_user_id']}"]['member_status'] == 1)) {
        return true;
    }
    return false;
}

//----------------------
// SMARTY
//----------------------

/**
 * Creates a Join Group button for logged in users on a profile group detail page
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrGroup_apply_button($params, $smarty)
{
    global $_user;

    // Are we logged in?
    if (!jrUser_is_logged_in()) {
        return '';
    }
    // we must get an item array
    if (!isset($params['item']) || !is_array($params['item'])) {
        return 'jrGroup_apply_button: item array required';
    }
    // Applications allowed?
    if (!isset($params['item']['group_applicants']) || $params['item']['group_applicants'] != 'on') {
        return '';
    }
    // Get Group members
    $tbl = jrCore_db_table_name('jrGroup', 'member');
    $req = "SELECT * FROM {$tbl} WHERE `member_group_id` = '{$params['item']['_item_id']}'";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    $_mb = array();
    if ($_rt && is_array($_rt) && count($_rt) > 0) {
        foreach ($_rt as $rt) {
            $_mb["{$rt['member_user_id']}"] = $rt;
        }
    }

    $_lang = jrUser_load_lang_strings();

    if (isset($_mb["{$_user['_user_id']}"]) && $_mb["{$_user['_user_id']}"]['member_status'] == 0) {
        // Pending - Show pending button with option to cancel
        $params['item']['class']  = 'group_pending';
        $params['item']['value']  = $_lang['jrGroup'][28];
        $params['item']['prompt'] = $_lang['jrGroup'][30];
        $params['item']['action'] = 'cancel';
    }
    elseif (isset($_mb["{$_user['_user_id']}"]) && $_mb["{$_user['_user_id']}"]['member_status'] == 1) {
        // Member - Show member button with option to leave
        $params['item']['class']  = 'group_cancel';
        $params['item']['value']  = $_lang['jrGroup'][27];
        $params['item']['prompt'] = $_lang['jrGroup'][29];
        $params['item']['action'] = 'leave';
    }
    else {
        // Show join group button
        $params['item']['class']  = 'group_join';
        $params['item']['value']  = $_lang['jrGroup'][26];
        $params['item']['action'] = 'join';
        $params['item']['prompt'] = $_lang['jrGroup'][31];
    }

    // process and return
    $out = jrCore_parse_template('button.tpl', $params['item'], 'jrGroup');
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}
