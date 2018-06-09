<?php
/**
 * Jamroom Group Discussions module
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

/**
 * meta
 */
function jrGroupDiscuss_meta()
{
    $_tmp = array(
        'name'        => 'Group Discussions',
        'url'         => 'group_discuss',
        'version'     => '1.4.13',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Adds simple discussions to Profile Groups',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/3897/group-discussions',
        'license'     => 'jcl',
        'category'    => 'profiles',
        'priority'    => 250,
        'requires'    => 'jrGroup,jrCore:6.0.0,jrComment,jrAction:2.0.8'
    );
    return $_tmp;
}

/**
 * init
 */
function jrGroupDiscuss_init()
{
    jrCore_register_module_feature('jrCore', 'javascript', 'jrGroupDiscuss', 'jrGroupDiscuss.js');
    jrCore_register_module_feature('jrCore', 'css', 'jrGroupDiscuss', 'jrGroupDiscuss.css');

    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrGroupDiscuss', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrGroupDiscuss', 'update');

    // Core support
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrGroupDiscuss', 'on');
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrGroupDiscuss', 'on');

    jrCore_register_module_feature('jrCore', 'action_support', 'jrGroupDiscuss', 'create', array('template' => 'item_action.tpl', 'allowed_off_profile' => true));
    jrCore_register_module_feature('jrCore', 'action_support', 'jrGroupDiscuss', 'update', array('template' => 'item_action.tpl', 'allowed_off_profile' => true));

    // We have fields that can be searched
    jrCore_register_module_feature('jrSearch', 'search_fields', 'jrGroupDiscuss', 'discuss_title', 1);

    // Exclude us from the Profile Menu
    jrCore_register_module_feature('jrProfile', 'profile_menu', 'jrGroupDiscuss', 'active', 'jrGroup');

    // Register ourselves with the Groups core
    jrCore_register_module_feature('jrGroup', 'group_support', 'jrGroupDiscuss', 'on');

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'jrGroupDiscuss', 'profile_jrGroupDiscuss_item_count', 1);

    // We want RSS feeds
    jrCore_register_module_feature('jrFeed', 'feed_support', 'jrGroupDiscuss', 'enabled');

    // Get correct Liked Item title and URL
    jrCore_register_event_listener('jrAction', 'action_data', 'jrGroupDiscuss_action_data_listener');

    // Create corrected URLs to RSS items
    jrCore_register_event_listener('jrFeed', 'create_rss_feed', 'jrGroupDiscuss_create_rss_feed_listener');

    // We add a "can create pages" config option to the group
    jrCore_register_event_listener('jrGroup', 'user_config', 'jrGroupDiscuss_user_config_listener');
    jrCore_register_event_listener('jrGroup', 'user_config_defaults', 'jrGroupDiscuss_user_config_defaults_listener');

    // Add in "home" profile info for discussions
    jrCore_register_event_listener('jrCore', 'db_get_item', 'jrGroupDiscuss_db_get_item_listener');
    jrCore_register_event_listener('jrCore', 'db_create_item', 'jrGroupDiscuss_db_create_item_listener');
    jrCore_register_event_listener('jrCore', 'db_delete_item', 'jrGroupDiscuss_db_delete_item_listener');
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrGroupDiscuss_db_search_items_listener');

    // System Reset listener
    jrCore_register_event_listener('jrDeveloper', 'reset_system', 'jrGroupDiscuss_reset_system_listener');

    // stop the comments module from ALSO sending an email for group discuss topics.
    jrCore_register_event_listener('jrUser', 'notify_user', 'jrGroupDiscuss_notify_user_listener');

    // add the following state to the group detail page
    jrCore_register_event_listener('jrProfile', 'item_detail_view', 'jrGroupDiscuss_item_detail_view_listener');

    // Change owner listener to make sure new owner is a group member
    jrCore_register_event_listener('jrChangeOwner', 'owner_changed', 'jrGroupDiscuss_owner_changed_listener');

    // Hand off private comments
    jrCore_register_event_listener('jrComment', 'private_item_ids', 'jrGroupDiscuss_private_item_ids_listener');

    // Add Last comment info to Group Discussion Lists
    jrCore_register_event_listener('jrCore', 'template_variables', 'jrGroupDiscuss_template_variables_listener');

    // Core item buttons
    $_tmp = array(
        'title'  => 'RSS feed button',
        'icon'   => 'rss',
        'active' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'item_index_button', 'jrGroupDiscuss', 'jrGroupDiscuss_rss_feed_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrGroupDiscuss', 'jrGroupDiscuss_rss_feed_button', $_tmp);

    $_tmp = array(
        'title'  => 'follow discussion button',
        'icon'   => 'site',
        'active' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrGroupDiscuss', 'jrGroupDiscuss_follow_discussion_button', $_tmp);

    // notifications
    $_tmp = array(
        'label' => 27,
        'help'  => 28
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrGroupDiscuss', 'discussion_updated', $_tmp);

    // notifications
    $_tmp = array(
        'label' => 32, // 'Group Watch'
        'help'  => 33 // 'When you are watching a group for new discussionss how do you want to be notified?';
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrGroupDiscuss', 'group_watch', $_tmp);

    // Register our tools
    if (jrCore_module_is_active('jrForum')) {
        jrCore_register_module_feature('jrCore', 'tool_view', 'jrGroupDiscuss', 'transfer_topic_to_discuss', array('Transfer Forum Topic', 'Transfer forum topic(s) to a group as a group discuss item'));
    }
    if (jrCore_module_is_active('jrDiscussion')) {
        jrCore_register_module_feature('jrCore', 'tool_view', 'jrGroupDiscuss', 'transfer_discuss_to_discussion', array('Transfer to Discussion', 'Transfer group discuss item(s) to a Discussion module category'));
    }
    if (jrCore_module_is_active('jrForum')) {
        jrCore_register_module_feature('jrCore', 'tool_view', 'jrGroupDiscuss', 'transfer_discuss_to_forum', array('Transfer to Forum', 'Transfer group discuss item(s) to a Forum module topic'));
    }

    return true;
}

//----------------------
// BUTTONS
//----------------------

/**
 * Return "create album" button for audio index
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return mixed
 */
function jrGroupDiscuss_rss_feed_button($module, $_item, $_args, $smarty, $test_only = false)
{
    global $_conf, $_post, $_urls;
    if (jrGroup_member_has_access($_item) && jrCore_module_is_active('jrFeed') && $module == 'jrGroupDiscuss') {
        if ($test_only) {
            return true;
        }
        $durl = jrCore_get_module_url('jrGroupDiscuss');
        $furl = jrCore_get_module_url('jrFeed');

        $_rt = array(
            'url'  => "{$_conf['jrCore_base_url']}/{$furl}/{$durl}/limit=100",
            'icon' => 'rss',
            'alt'  => 'RSS'
        );
        // See if we are doing a specific discussion item
        if (isset($_post['option']) && isset($_urls["{$_post['option']}"]) && $_urls["{$_post['option']}"] == 'jrGroupDiscuss' && isset($_post['_1']) && jrCore_checktype($_post['_1'], 'number_nz')) {
            $_rt['url'] .= "/id={$_post['_1']}/discussion={$_post['_2']}";
        }
        elseif (isset($_post['group_id']) && jrCore_checktype($_post['group_id'], 'number_nz')) {
            $_rt['url'] .= "/group_id={$_post['group_id']}";
        }
        return $_rt;
    }
    return false;
}

/**
 * Return "follow discussion" button for discussion detail
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return string
 */
function jrGroupDiscuss_follow_discussion_button($module, $_item, $_args, $smarty, $test_only = false)
{
    if (jrGroup_member_has_access($_item) && $module == 'jrGroupDiscuss' && jrCore_module_is_active('jrComment') && jrUser_is_logged_in()) {
        if ($test_only) {
            return true;
        }
        $_args['icon']    = 'site';
        $_args['item_id'] = $_item['_item_id'];
        if (isset($_item['discussion_user_is_following']) && $_item['discussion_user_is_following'] == '1') {
            $_args['icon'] = 'site-hilighted';
        }
        return smarty_function_jrGroupDiscuss_follow_button($_args, $smarty);
    }
    return false;
}

//----------------------
// EVENT LISTENERS
//----------------------

/**
 * Add Last comment info to Group Discussion Lists
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGroupDiscuss_template_variables_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_conf;
    if (isset($_data['_params']) && isset($_data['_params']['module']) && $_data['_params']['module'] == 'jrGroupDiscuss' && isset($_data['_items']) && isset($_data['_params']['include_latest_replies'])) {
        $_id = array();
        $_tt = array();
        $_pn = array();
        $_pr = array();
        $_rs = array();
        foreach ($_data['_items'] as $k => $_i) {
            $iid       = (int) $_i['_item_id'];
            $_id[$k]   = $iid;
            $_rs[$iid] = $k;
            $_tt[$k]   = $_i['discuss_title_url'];
            $_pr[$iid] = $_i['_group_data']['profile_url'];
            $_pn[$k]   = ($_conf['jrComment_pagebreak'] > 0) ? ceil($_i['discuss_comment_count'] / $_conf['jrComment_pagebreak']) : 1;
        }
        $cnt = count($_id);
        if ($cnt > 0) {
            $_sp = array(
                'search'                       => array(
                    'comment_item_id in ' . implode(',', $_id),
                    'comment_module = jrGroupDiscuss'
                ),
                'order_by'                     => array('_item_id' => 'desc'),
                'privacy_check'                => false,
                'quota_check'                  => false,
                'ignore_pending'               => true,
                'ignore_threading'             => true,
                'exclude_jrUser_keys'          => true,
                'exclude_jrProfile_quota_keys' => true,
                'jrProfile_active_check'       => false,
                'limit'                        => 2500
            );
            $_sp = jrCore_db_search_items('jrComment', $_sp);
            if ($_sp && is_array($_sp) && isset($_sp['_items'])) {
                $url = jrCore_get_module_url('jrGroupDiscuss');
                $_fn = array();
                foreach ($_sp['_items'] as $_i) {
                    $iid = (int) $_i['comment_item_id'];
                    if (!isset($_fn[$iid])) {
                        $uid  = $_rs[$iid];
                        $page = '';
                        if (isset($_pn[$iid]) && $_pn[$iid] > 1) {
                            $page = '/p=' . $page;
                        }
                        $_data['_items'][$uid]['last_comment']             = $_i;
                        $_data['_items'][$uid]['discuss_last_comment_url'] = "{$_conf['jrCore_base_url']}/" . $_pr[$iid] . "/{$url}/{$iid}/" . $_tt[$iid] . "{$page}#cm{$_i['_item_id']}";
                        $_fn[$iid] = 1;
                        if (count($_fn) === $cnt) {
                            // We've filled all slots
                            break;
                        }
                    }
                }
            }
        }

    }
    return $_data;
}

/**
 * Get private discussion id's
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroupDiscuss_private_item_ids_listener($_data, $_user, $_conf, $_args, $event)
{
    // Get private groups
    $_rt = jrGroup_get_private_groups();
    if ($_rt && is_array($_rt)) {
        // We have private groups - find Discussions in those groups
        $_sc = array(
            'search'              => array(
                'discuss_group_id in ' . implode(',', $_rt)
            ),
            'return_item_id_only' => true,
            'skip_triggers'       => true,
            'quota_check'         => false,
            'privacy_check'       => false,
            'ignore_pending'      => true,
            'limit'               => 100000
        );
        $_sc = jrCore_db_search_items('jrGroupDiscuss', $_sc);
        if ($_sc && is_array($_sc)) {
            $_data['jrGroupDiscuss'] = $_sc;
        }
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
function jrGroupDiscuss_reset_system_listener($_data, $_user, $_conf, $_args, $event)
{
    $tbl = jrCore_db_table_name('jrGroupDiscuss', 'follow');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");
    $tbl = jrCore_db_table_name('jrGroupDiscuss', 'follow_group');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");
    return $_data;
}

/**
 * Create action_original_item_url key for action entries
 * @param $_data array incoming data array of item including original owner profile and user IDs
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array of new owner profile and user IDs
 * @param $event string Event Trigger name
 * @return array
 */
function jrGroupDiscuss_action_data_listener($_data, $_user, $_conf, $_args, $event)
{
    // Get correct URL to group items
    $url = jrCore_get_module_url('jrGroupDiscuss');
    if ($_data['action_module'] == 'jrGroupDiscuss' && isset($_data['action_data']['_group_data'])) {
        $_data['action_item_url'] = "{$_conf['jrCore_base_url']}/{$_data['action_data']['_group_data']['profile_url']}/{$url}/{$_data['action_item_id']}/{$_data['action_title_url']}";
        // for backwards compatibility
        $_data['action_data']['group_profile_url'] = $_data['action_data']['_group_data']['profile_url'];
    }
    if (isset($_data['action_original_module']) && $_data['action_original_module'] == 'jrGroupDiscuss' && isset($_data['action_original_data']['_group_data'])) {
        $_data['action_original_item_url'] = "{$_conf['jrCore_base_url']}/{$_data['action_original_data']['_group_data']['profile_url']}/{$url}/{$_data['action_original_item_id']}/{$_data['action_original_title_url']}";
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
function jrGroupDiscuss_owner_changed_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['module']) && $_args['module'] == 'jrChangeOwner' && isset($_args['target_module']) && $_args['target_module'] == 'jrGroupDiscuss' && jrCore_checktype($_data['discuss_group_id'], 'number_nz')) {
        // Get parent group
        $_gt = jrCore_db_get_item('jrGroup', $_data['discuss_group_id']);
        if ($_gt && is_array($_gt)) {
            if (!isset($_gt['group_member']["{$_args['_user_id']}"])) {
                // Make new owner a group member
                $tbl = jrCore_db_table_name('jrGroup', 'member');
                $req = "INSERT INTO {$tbl} (member_created, member_user_id, member_group_id, member_status, member_active, member_more)
                VALUES (UNIX_TIMESTAMP(), '{$_args['_user_id']}', '{$_data['discuss_group_id']}', '1', '1', '')
                ON DUPLICATE KEY UPDATE member_created = UNIX_TIMESTAMP()";
                jrCore_db_query($req);
            }
        }
    }
    return $_data;
}

/**
 * Add "Home" Profile info for discussion creators
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroupDiscuss_db_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if ($_args['module'] == 'jrGroupDiscuss' && is_array($_data)) {

        // Add Home Profile info for creating user
        $_id = array($_data['_user_id']);
        $_ky = array('_profile_id', 'profile_name', 'profile_url');
        $_us = jrCore_db_get_multiple_items('jrProfile', $_id, $_ky);
        if ($_us && is_array($_us) && isset($_us[0])) {
            foreach ($_us[0] as $k => $v) {
                if ($k == '_profile_id') {
                    $_data["home_profile_id"] = $v;
                }
                else {
                    $_data["home_{$k}"] = $v;
                }
            }
        }

        // Check if we are viewing a specific discussion
        if (isset($_post['_profile_id']) && jrUser_is_logged_in() && isset($_post['option']) && jrCore_get_module_url('jrGroupDiscuss') == $_post['option'] && isset($_post['_1']) && jrCore_checktype($_post['_1'], 'number_nz')) {

            // Check if the viewing user is following this discussion
            $iid = intval($_post['_1']);
            $tbl = jrCore_db_table_name('jrGroupDiscuss', 'follow');
            $req = "SELECT follow_notified FROM {$tbl} WHERE follow_id = '{$iid}' AND follow_user_id = '" . intval($_user['_user_id']) . "'";
            $_if = jrCore_db_query($req, 'SINGLE');
            if ($_if && is_array($_if)) {
                if ($_if['follow_notified'] > 0) {
                    // reset follow notified - they have viewed this topic now
                    $tbl = jrCore_db_table_name('jrGroupDiscuss', 'follow');
                    $req = "UPDATE {$tbl} SET follow_notified = '0' WHERE follow_id = '{$iid}' AND follow_user_id = '{$_user['_user_id']}'";
                    jrCore_db_query($req);
                }
                $_data['discussion_user_is_following'] = 1;
            }
            else {
                $_data['discussion_user_is_following'] = 0;
            }
        }

    }
    return $_data;
}

/**
 * Notify discussion "watchers"
 * Increment group discuss counter
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroupDiscuss_db_create_item_listener($_data, $_user, $_conf, $_args, $event)
{
    // Notify discussion "watchers"
    if ($_args['module'] == 'jrComment' && isset($_data['comment_module']) && $_data['comment_module'] == 'jrGroupDiscuss') {

        // We have a new comment on a Group Discussion item - get watchers
        $uid = (int) $_data['comment_item_id'];
        $tbl = jrCore_db_table_name('jrGroupDiscuss', 'follow');

        // Update the posters follow_notified to 0
        $req = "UPDATE {$tbl} SET follow_notified = '0' WHERE follow_id = '{$uid}' AND follow_user_id = '{$_user['_user_id']}'";
        jrCore_db_query($req);

        if (isset($_conf['jrGroupDiscuss_follower_notification']) && $_conf['jrGroupDiscuss_follower_notification'] == 'chatty') {
            $req = "SELECT follow_user_id FROM {$tbl} WHERE follow_id = '{$uid}' AND follow_user_id != '{$_user['_user_id']}'";
        }
        else {
            $req = "SELECT follow_user_id FROM {$tbl} WHERE follow_id = '{$uid}' AND follow_notified = '0' AND follow_user_id != '{$_user['_user_id']}'";
        }
        $_rt = jrCore_db_query($req, 'follow_user_id', false, 'follow_user_id');
        if ($_rt && is_array($_rt)) {

            // Get info about this discussion
            $_rp                    = jrCore_db_get_item('jrGroupDiscuss', $uid);
            $_rp['_user']           = $_user;
            $_rp['discuss_url']     = "{$_conf['jrCore_base_url']}/{$_rp['profile_url']}/" . jrCore_get_module_url('jrGroupDiscuss') . "/{$_rp['_item_id']}/{$_rp['discuss_title_url']}#cm{$_args['_item_id']}";
            $_rp['discuss_message'] = jrCore_strip_html($_data['comment_text']);

            // Notify users
            list($sub, $msg) = jrCore_parse_email_templates('jrGroupDiscuss', 'discussion_updated', $_rp);
            jrUser_notify($_rt, 0, 'jrGroupDiscuss', 'discussion_updated', $sub, $msg);

            // Update that these users have been notified
            $req = "UPDATE {$tbl} SET follow_notified = UNIX_TIMESTAMP() WHERE follow_id = '{$uid}' AND follow_user_id IN(" . implode(',', $_rt) . ')';
            jrCore_db_query($req);
        }
    }

    // Increment group discuss counter
    elseif ($_args['module'] == 'jrGroupDiscuss' && jrCore_checktype($_data['discuss_group_id'], 'number_nz')) {
        jrCore_db_increment_key('jrGroup', $_data['discuss_group_id'], 'group_jrGroupDiscuss_item_count', 1);
    }

    // update the _updated for the Group if a comment is posted to a group module
    if (isset($_args['module']) && $_args['module'] == 'jrComment' && strpos($_data['comment_module'], 'jrGroup') === 0 && isset($_conf['jrGroupDiscuss_recently_active']) && $_conf['jrGroupDiscuss_recently_active'] != 'off') {
        if (jrCore_module_is_active($_data['comment_module']) && jrCore_checktype($_data['comment_item_id'], 'number_nz')) {
            jrCore_db_update_item($_data['comment_module'], $_data['comment_item_id'], array(), array('_updated' => time()));
            if ($_data['comment_module'] == 'jrGroupDiscuss') {
                // It was a group discuss - update parent group as well
                $group_id = jrCore_db_get_item_key('jrGroupDiscuss', $_data['comment_item_id'], 'discuss_group_id');
                if (jrCore_checktype($group_id, 'number_nz')) {
                    jrCore_db_update_item('jrGroup', $group_id, array(), array('_updated' => time()));
                }
            }
        }
    }

    // add the commenting user to following this thread
    if (isset($_data['comment_item_id'])) {
        $fid = (int) $_data['comment_item_id'];
        if ($fid > 0) {
            $uid = (int) $_user['_user_id'];
            $tbl = jrCore_db_table_name('jrGroupDiscuss', 'follow');
            $req = "INSERT INTO {$tbl} (follow_id, follow_user_id, follow_created) VALUES ('{$fid}','{$uid}',UNIX_TIMESTAMP()) ON DUPLICATE KEY UPDATE follow_created = UNIX_TIMESTAMP()";
            jrCore_db_query($req);
            jrCore_delete_all_cache_entries('jrGroupDiscuss', $_user['_user_id']);
        }
    }

    return $_data;
}

/**
 * Decrement group discuss counter
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroupDiscuss_db_delete_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrGroupDiscuss' && jrCore_checktype($_data['discuss_group_id'], 'number_nz')) {
        // Decrement group discuss counter
        jrCore_db_decrement_key('jrGroup', $_data['discuss_group_id'], 'group_jrGroupDiscuss_item_count', 1);
    }
    return $_data;
}

/**
 * Add "Home" Profile info for discussion creators
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroupDiscuss_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrGroupDiscuss' && isset($_data['_items']) && is_array($_data['_items'])) {
        $tbl = jrCore_db_table_name('jrGroupDiscuss', 'follow');
        $req = "SELECT * FROM {$tbl} WHERE follow_user_id = '" . intval($_user['_user_id']) . "'";
        $_if = jrCore_db_query($req, 'follow_id');
        $_id = array();
        foreach ($_data['_items'] as $k => $_v) {
            $_id["{$_v['_user_id']}"] = $_v['_user_id'];
            // following status
            if ($_if && in_array($_v['_item_id'], array_keys($_if))) {
                $_data['_items'][$k]['discuss_user_is_following'] = '1';
            }
            else {
                $_data['_items'][$k]['discuss_user_is_following'] = '0';
            }
        }
        if (count($_id) > 0) {
            $_sp = array(
                'search'         => array(
                    "_user_id in " . implode(',', $_id)
                ),
                'return_keys'    => array('_profile_id', 'profile_name', 'profile_url'),
                'skip_triggers'  => true,
                'ignore_pending' => true,
                'privacy_check'  => false,
                'quota_check'    => false,
                'limit'          => count($_id)
            );
            $_sp = jrCore_db_search_items('jrProfile', $_sp);
            if ($_sp && is_array($_sp) && isset($_sp['_items'])) {
                $_pr = array();
                foreach ($_sp['_items'] as $k => $_v) {
                    $_pr["{$_v['_user_id']}"] = $_v;
                }
                foreach ($_data['_items'] as $k => $_v) {
                    if (isset($_pr["{$_v['_user_id']}"])) {
                        foreach ($_pr["{$_v['_user_id']}"] as $kn => $vl) {
                            $_data['_items'][$k]["home_{$kn}"] = $vl;
                        }
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * Add Group Config option
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroupDiscuss_user_config_listener($_data, $_user, $_conf, $_args, $event)
{
    // $_data = User Info
    // $_args = Group Info
    $val = 'on';
    if (isset($_args['group_member']["{$_data['_user_id']}"]['jrGroupDiscuss_config_allowed']) && jrCore_checktype($_args['group_member']["{$_data['_user_id']}"]['jrGroupDiscuss_config_allowed'], 'onoff')) {
        $val = $_args['group_member']["{$_data['_user_id']}"]['jrGroupDiscuss_config_allowed'];
    }
    $_tmp = array(
        'name'     => 'jrGroupDiscuss_config_allowed',
        'label'    => 'can create discussions',
        'help'     => 'If checked, this User will be allowed to create discussions in this Group',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'value'    => $val,
        'default'  => 'off',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    return $_data;
}

/**
 * Return Default values for config options
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroupDiscuss_user_config_defaults_listener($_data, $_user, $_conf, $_args, $event)
{
    // $_args['_group'] = group info
    // $_args['_user_id'] = User ID
    $val = 'off';
    $uid = (int) $_args['_user_id'];
    if (isset($_args['_group']['group_member'][$uid])) {
        $val = 'on';
    }
    $_data['jrGroupDiscuss_config_allowed'] = $val;
    return $_data;
}

/**
 * listen for the jrUser module's 'notify_user' event
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroupDiscuss_notify_user_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    // Stop the comment module from ALSO sending an email for group discuss topics.
    if (isset($_post['comment_module']) && $_post['comment_module'] == 'jrGroupDiscuss' && $_args['module'] == 'jrComment' && $_args['event'] == 'new_comment') {
        return array('abort' => true);
    }
    return $_data;
}

/**
 * Create the correct URLs to a group discussion post
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroupDiscuss_create_rss_feed_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_conf;
    if ($_args['module'] == 'jrGroupDiscuss' && is_array($_data) && count($_data) > 0) {
        $murl = jrCore_get_module_url('jrGroupDiscuss');
        foreach ($_data as $k => $v) {
            $_data[$k]['link']  = "{$_conf['jrCore_base_url']}/{$v['_group_data']['profile_url']}/{$murl}/{$v['_item_id']}/{$v['discuss_title_url']}";
            $_data[$k]['title'] = "{$v['_group_data']['group_title']}: {$v['discuss_title']} - @{$v['profile_url']}";
        }
    }
    return $_data;
}

//----------------------
// SMARTY FUNCTIONS
//----------------------

/**
 * Creates a "follow this discussion" button so users can be notified when a discussion is updated
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrGroupDiscuss_follow_button($params, $smarty)
{
    global $_conf;
    if (!jrUser_is_logged_in()) {
        return '';
    }
    if (!isset($params['item_id']) || !jrCore_checktype($params['item_id'], 'number_nz')) {
        return jrCore_smarty_invalid_error('item_id');
    }
    $class = null;
    if (isset($params['class']) && jrCore_checktype($params['class'], 'printable')) {
        $class = $params['class']; // icon class
    }
    $color = null;
    if (isset($params['color']) && jrCore_checktype($params['color'], 'printable')) {
        $color = $params['color']; // icon color
    }
    $iid = intval($params['item_id']);
    $_ln = jrUser_load_lang_strings();
    $ttl = $_ln['jrGroupDiscuss'][24];
    $alt = $_ln['jrGroupDiscuss'][24];
    if (isset($params['alt']) && strlen($params['alt']) > 0) {
        $alt = $params['alt'];
        $ttl = $params['alt'];
    }
    $alt = ' alt="' . jrCore_entity_string($alt) . '"';
    $ttl = ' title="' . jrCore_entity_string($ttl) . '"';
    if (isset($params['image'])) {
        $src = "{$_conf['jrCore_base_url']}/skins/{$_conf['jrCore_active_skin']}/img/{$params['image']}";
        $out = '<img id="discussion_follow_button_' . $iid . '" src="' . $src . '"' . $alt . $ttl . ' onclick="jrGroupDiscuss_follow_toggle(\'' . $iid . '\')">';
    }
    else {
        if (!isset($params['icon'])) {
            $params['icon'] = 'site';
        }
        $siz = null;
        if (isset($params['size']) && jrCore_checktype($params['size'], 'number_nz')) {
            $siz = (int) $params['size'];
        }
        $out = "<a id=\"discussion_follow_button_{$iid}\" onclick=\"jrGroupDiscuss_follow_toggle('" . $iid . "')\" " . $ttl . ">" . jrCore_get_sprite_html($params['icon'], $siz, $class, $color) . '</a>';
    }
    $out .= '<div id="discussion_follow_drop_' . $iid . '" class="discussion_follow_box" style="display:none"></div>';
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Creates a "follow this group" button so users can be notified when a new thread is posted in this group discusssion
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrGroupDiscuss_follow_group_button($params, $smarty)
{
    global $_conf;
    if (!jrUser_is_logged_in()) {
        return '';
    }
    if (!isset($params['group_id']) || !jrCore_checktype($params['group_id'], 'number_nz')) {
        return 'jrGroupDiscuss_follow_group_button: group_id parameter required';
    }
    $class = null;
    if (isset($params['class']) && jrCore_checktype($params['class'], 'printable')) {
        $class = $params['class']; // icon class
    }
    $color = null;
    if (isset($params['color']) && jrCore_checktype($params['color'], 'printable')) {
        $color = $params['color']; // icon color
    }

    $gid = intval($params['group_id']);
    $_ln = jrUser_load_lang_strings();
    $ttl = $_ln['jrGroupDiscuss'][29];
    $alt = $_ln['jrGroupDiscuss'][29];
    if (isset($params['alt'])) {
        $alt = $params['alt'];
        $ttl = $params['alt'];
    }
    $alt = ' alt="' . jrCore_entity_string($alt) . '"';
    $ttl = ' title="' . jrCore_entity_string($ttl) . '"';
    if (isset($params['image'])) {
        $src = "{$_conf['jrCore_base_url']}/skins/{$_conf['jrCore_active_skin']}/img/{$params['image']}";
        $out = '<img id="forum_category_follow_button_' . $gid . '" class="discussion_follow_box" src="' . $src . '"' . $alt . $ttl . ' onclick="jrGroupDiscuss_follow_group_toggle(\'' . $gid . '\')">';
    }
    else {
        if (!isset($params['icon'])) {
            $params['icon'] = 'site';
        }
        $siz = null;
        if (isset($params['size']) && jrCore_checktype($params['size'], 'number_nz')) {
            $siz = (int) $params['size'];
        }
        $out = "<a id=\"discussion_follow_group_button_{$gid}\" onclick=\"jrGroupDiscuss_follow_group_toggle('" . $gid . "')\" " . $ttl . ">" . jrCore_get_sprite_html($params['icon'], $siz, $class, $color) . '</a>';
    }
    $out .= '<div id="discussion_follow_group_drop_' . $gid . '" class="discussion_follow_box" style="display:none"><!-- discussion group follow loads here --></div>';
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * @deprecated - do not use
 * @param array
 * @return array
 */
function smarty_modifier_jrGroupDiscuss_add_group_url($_items)
{
    if (is_array($_items) && count($_items) > 0) {
        $_gid = array();
        foreach ($_items as $item) {
            if (jrCore_checktype($item['discuss_group_id'], 'number_nz')) {
                $_gid["{$item['discuss_group_id']}"] = 1;
            }
        }
        if (count($_gid) > 0) {
            $_rt = array(
                'search'                       => array(
                    '_item_id in ' . implode(',', array_keys($_gid))
                ),
                'return_keys'                  => array('profile_url'),
                'ignore_pending'               => true,
                'include_jrProfile_keys'       => true,
                'exclude_jrProfile_quota_keys' => true,
                'limit'                        => count($_gid)
            );
            $_gt = jrCore_db_search_items('jrGroup', $_rt);
            if ($_gt && is_array($_gt['_items']) && count($_gt['_items']) > 0) {
                $_gid = array();
                foreach ($_gt['_items'] as $gt) {
                    $_gid["{$gt['_item_id']}"] = $gt['profile_url'];
                }
                foreach ($_items as $k => $item) {
                    $_items["{$k}"]["group_profile_url"] = $_gid["{$item['discuss_group_id']}"];
                }
            }
        }
    }
    return $_items;
}

/**
 * Check for following state and add to the  Group detail page
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroupDiscuss_item_detail_view_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrUser_is_logged_in() && $_args['module'] == 'jrGroup' && isset($_data['_item_id'])) {

        // get the following users for this group
        $tbl = jrCore_db_table_name('jrGroupDiscuss', 'follow_group');
        $req = "SELECT *  FROM {$tbl} WHERE  follow_group_id = '{$_data['_item_id']}' AND follow_user_id = '{$_user['_user_id']}'";
        $_if = jrCore_db_query($req, 'follow_user_id');
        if ($_if && is_array($_if)) {
            $_data['discuss_user_is_following'] = 1;
        }
        else {
            $_data['discuss_user_is_following'] = 0;
        }
    }
    return $_data;
}

/**
 * Notify anyone watching this group for new discussion that a new discussion has been created
 * @param array $_topic Topic that has been created/updated
 * @param array $_group Group info
 * @param int $discuss_id discussion item_id
 * @return bool
 */
function jrGroupDiscuss_notify_group_watchers($_topic, $_group, $discuss_id)
{
    global $_conf, $_post, $_user;
    if (!jrCore_checktype($_group['_item_id'], 'number_nz')) {
        return false;
    }

    // get the following users for this group
    $tbl = jrCore_db_table_name('jrGroupDiscuss', 'follow_group');
    $req = "SELECT * FROM {$tbl} WHERE follow_group_id = '{$_group['_item_id']}' AND follow_user_id != '{$_user['_user_id']}'";
    $_if = jrCore_db_query($req, 'follow_user_id');

    if ($_if && is_array($_if) && count($_if) > 0) {
        // Get profile_url for profile we posted on
        $murl                        = jrCore_get_module_url('jrGroup');
        $_topic['creator_user_name'] = $_user['user_name'];
        $_topic['group_topic_url']   = "{$_conf['jrCore_base_url']}/{$_group['profile_url']}/{$_post['module_url']}/{$discuss_id}/{$_topic['discuss_title_url']}";
        $_topic['group_url']         = "{$_conf['jrCore_base_url']}/{$_group['profile_url']}/{$murl}/{$_group['_item_id']}/{$_group['group_title_url']}";
        $_topic['_group']            = $_group;
        $_topic['message']           = jrCore_entity_string(jrCore_strip_html(smarty_modifier_jrCore_format_string($_topic['discuss_description'], 0)));

        list($sub, $msg) = jrCore_parse_email_templates('jrGroupDiscuss', 'group_watch', $_topic);
        jrUser_notify(array_keys($_if), 0, 'jrGroupDiscuss', 'group_watch', $sub, $msg);
    }

    return true;
}

/**
 * Notify all group members that a new discussion has been created
 * @param array $_topic Topic that has been created/updated
 * @param array $_group Group info
 * @param int $discuss_id discussion item_id
 * @return bool
 */
function jrGroupDiscuss_notify_group_members($_topic, $_group, $discuss_id)
{
    global $_conf, $_post, $_user;
    if (!jrCore_checktype($_group['_item_id'], 'number_nz')) {
        return false;
    }

    // get all group members
    $tbl = jrCore_db_table_name('jrGroup', 'member');
    $req = "SELECT * FROM {$tbl} WHERE member_group_id = '{$_group['_item_id']}' AND member_user_id != '{$_user['_user_id']}'";
    $_if = jrCore_db_query($req, 'member_user_id');

    if ($_if && is_array($_if) && count($_if) > 0) {
        // Get profile_url for profile we posted on
        $murl                        = jrCore_get_module_url('jrGroup');
        $_topic['creator_user_name'] = $_user['user_name'];
        $_topic['group_topic_url']   = "{$_conf['jrCore_base_url']}/{$_group['profile_url']}/{$_post['module_url']}/{$discuss_id}/{$_topic['discuss_title_url']}";
        $_topic['group_url']         = "{$_conf['jrCore_base_url']}/{$_group['profile_url']}/{$murl}/{$_group['_item_id']}/{$_group['group_title_url']}";
        $_topic['_group']            = $_group;
        $_topic['message']           = jrCore_entity_string(jrCore_strip_html(smarty_modifier_jrCore_format_string($_topic['discuss_description'], 0)));

        list($sub, $msg) = jrCore_parse_email_templates('jrGroupDiscuss', 'group_watch', $_topic);
        jrUser_notify(array_keys($_if), 0, 'jrGroupDiscuss', 'group_watch', $sub, $msg);
    }

    return true;
}
