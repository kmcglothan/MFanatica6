<?php
/**
 * Jamroom Like It module
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
function jrLike_meta()
{
    $_tmp = array(
        'name'        => 'Like It',
        'url'         => 'like',
        'version'     => '1.5.1',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => "A module to allow 'like' and 'dislike' of module items",
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/1722/like-it',
        'category'    => 'item features',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrLike_init()
{
    // Pulse Key support
    jrCore_register_module_feature('jrProfile', 'pulse_key', 'jrLike', 'profile_jrLike_like_home_item_count', 'likes');
    jrCore_register_module_feature('jrProfile', 'pulse_key', 'jrLike', 'profile_jrLike_dislike_home_item_count', 'dislikes');

    // Core Quota support
    $_opts = array(
        'label' => 'Allowed to Like Items',
        'help'  => 'If checked, users in this quota will be able to like or dislike items.'
    );
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrLike', 'on', $_opts);

    // notifications
    $_tmp = array(
        'label' => 12, // 12 = 'Item liked'
        'help'  => 13  // 13 = 'If one of your items is liked, would you like to be notified?'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrLike', 'new_like', $_tmp);

    // Skin menu link to My Likes
    $_tmp = array(
        'group' => 'user',
        'label' => 10, // 'Items You Like or Dislike'
        'url'   => 'liked_items'
    );
    jrCore_register_module_feature('jrCore', 'skin_menu_item', 'jrLike', 'liked_items', $_tmp);

    // We offer a module detail feature for likes
    $_tmp = array(
        'function' => 'jrLike_item_likes_feature',
        'label'    => 'Item Likes',
        'help'     => 'Adds User Likes and/or Dislikes to Item Detail pages'
    );
    jrCore_register_module_feature('jrCore', 'item_detail_feature', 'jrLike', 'item_likes', $_tmp);

    // Register our JS and CSS
    jrCore_register_module_feature('jrCore', 'css', 'jrLike', 'jrLike.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrLike', 'jrLike.js');

    // Our module provides the "action" magic view
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrLike', 'like_create', 'jrLike_like_create');

    // Support for actions
    jrCore_register_module_feature('jrCore', 'action_support', 'jrLike', 'like', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrLike', 'dislike', 'item_action.tpl');

    // Listeners
    jrCore_register_event_listener('jrAction', 'action_data', 'jrLike_action_data_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrLike_verify_module_listener');
    jrCore_register_event_listener('jrCore', 'empty_recycle_bin', 'jrLike_empty_recycle_bin_listener');
    jrCore_register_event_listener('jrCore', 'expire_recycle_bin', 'jrLike_expire_recycle_bin_listener');
    jrCore_register_event_listener('jrCore', 'db_get_item', 'jrLike_db_get_item_listener');
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrLike_db_search_items_listener');
    jrCore_register_event_listener('jrDeveloper', 'reset_system', 'jrLike_reset_system_listener');

    // Events
    jrCore_register_event_trigger('jrLike', 'item_liked', 'Fired when the an item is liked/disliked');
    jrCore_register_event_trigger('jrLike', 'item_action_info', 'Fired to get item Title and URL for Action entry');

    // Register our rebuild tool
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrLike', 'rebuild_counts', array('Rebuild Counts', 'Rebuilds module like and dislike counts based on the current Like database'));

    jrCore_register_queue_worker('jrLike', 'convert_db', 'jrLike_convert_db_worker', 0, 1, 14400);
    return true;
}

//--------------------------
// EVENT LISTENERS
//--------------------------

/**
 * Add Liked Item URLs to Like/Dislike Action Items
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrLike_action_data_listener($_data, $_user, $_conf, $_args, $event)
{
    // Some modules (like jrForum) allow users to create items on OTHER profiles,
    // yet the action generated URL will be for their HOME profile - this item_action_info
    // trigger allows these specific modules to get us the corrected URL to the item
    if ($_data['action_module'] == 'jrLike' && isset($_data['action_original_module'])) {

        // Has another module already created this for us?
        if (!isset($_data['action_original_item_url'])) {
            // Generate URL the item that was LIKED
            $_data['action_original_item_url'] = "{$_conf['jrCore_base_url']}/{$_data['action_original_profile_url']}/" . jrCore_get_module_url($_data['action_original_module']) . "/{$_data['action_original_item_id']}/{$_data['action_original_title_url']}";
        }

    }
    return $_data;
}

/**
 * Add in action_data info for like actions
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrLike_db_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrAction' && $_data['action_module'] == 'jrLike') {
        $tbl = jrCore_db_table_name('jrLike', 'likes');
        $req = "SELECT * FROM {$tbl} WHERE like_id = " . intval($_data['action_item_id']);
        $_rt = jrCore_db_query($req, 'SINGLE');
        if ($_rt && is_array($_rt)) {
            $_data['action_data'] = $_rt;
        }
    }
    return $_data;
}

/**
 * Add in action_data info for like actions
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrLike_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrAction') {
        $_ids = array();
        foreach ($_data['_items'] as $k => $_item) {
            if ($_item['action_module'] == 'jrLike') {
                $_ids[] = (int) $_item['action_item_id'];
            }
        }
        if (count($_ids) > 0) {
            $tbl = jrCore_db_table_name('jrLike', 'likes');
            $req = "SELECT * FROM {$tbl} WHERE like_id IN(" . implode(',', $_ids) . ')';
            $_rt = jrCore_db_query($req, 'like_id');
            if ($_rt && is_array($_rt)) {
                foreach ($_data['_items'] as $k => $_item) {
                    $_data['_items'][$k]['action_data'] = $_rt["{$_item['action_item_id']}"];
                }
            }
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
function jrLike_reset_system_listener($_data, $_user, $_conf, $_args, $event)
{
    $tbl = jrCore_db_table_name('jrLike', 'likes');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");
    return $_data;
}

/**
 * Convert to new database format
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrLike_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    // Are we still on the old format?
    if (jrCore_db_table_exists('jrLike', 'item')) {
        // Do we have entries?
        if (jrCore_db_number_rows('jrLike', 'item') > 0) {
            jrCore_queue_create('jrLike', 'convert_db', array('time' => time()));
        }
        else {
            // Remove tables no longer needed
            $_tb = array('notified', 'item', 'item_key');
            foreach ($_tb as $nam) {
                if (jrCore_db_table_exists('jrLike', $nam)) {
                    $tbl = jrCore_db_table_name('jrLike', $nam);
                    $req = "DROP TABLE IF EXISTS {$tbl}";
                    jrCore_db_query($req);
                }
            }
            // Make sure we remove the PREFIX in the module table
            $tbl = jrCore_db_table_name('jrCore', 'module');
            $req = "UPDATE {$tbl} SET module_prefix = '' WHERE module_directory = 'jrLike'";
            jrCore_db_query($req);
        }
    }
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
function jrLike_empty_recycle_bin_listener($_data, $_user, $_conf, $_args, $event)
{
    // The recycle bin is being emptied - we need to delete likes
    // for any item that is in the recycle bin
    if (count($_data) > 0) {
        $tbl = jrCore_db_table_name('jrLike', 'likes');
        foreach ($_data as $mod => $_ids) {
            $req = "DELETE FROM {$tbl} WHERE like_module = '" . jrCore_db_escape($mod) . "' AND like_item_id IN(" . implode(',', $_ids) . ")";
            jrCore_db_query($req);
        }
    }
    return $_data;
}

/**
 * Cleanup likes for deleted items
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrLike_expire_recycle_bin_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['_items']) && is_array($_data['_items'])) {
        // The items array has all the items that are being purged from the recycle bin
        $_dl = array();
        foreach ($_data['_items'] as $k => $v) {
            $mod = $v['module'];
            if (!isset($_dl[$mod])) {
                $_dl[$mod] = array();
            }
            $_dl[$mod][] = (int) $v['item_id'];
        }
        if (count($_dl) > 0) {
            $tbl = jrCore_db_table_name('jrLike', 'likes');
            foreach ($_dl as $mod => $_ids) {
                $req = "DELETE FROM {$tbl} WHERE like_module = '" . jrCore_db_escape($mod) . "' AND like_item_id IN(" . implode(',', $_ids) . ')';
                jrCore_db_query($req);
            }
        }
        unset($_dl);
    }
    return $_data;
}

//--------------------------
// QUEUE WORKER
//--------------------------

/**
 * Convert from old DS based storage to new tables
 * @param array $_queue The queue entry the worker will receive
 * @return bool
 */
function jrLike_convert_db_worker($_queue)
{
    $last_id = 0;
    $total   = 0;
    while (true) {
        // We need to convert our existing DS items to the new DB format
        $_sp = array(
            'search'         => array(
                "_item_id > {$last_id}"
            ),
            'return_keys'    => array('_item_id', '_created', '_user_id', 'like_item_id', 'like_module', 'like_action', 'like_user_ip'),
            'order_by'       => array('_item_id' => 'asc'),
            'skip_triggers'  => true,
            'privacy_check'  => false,
            'ignore_pending' => true,
            'limit'          => 1000
        );
        $_sp = jrCore_db_search_items('jrLike', $_sp);
        if ($_sp && is_array($_sp) && isset($_sp['_items'])) {
            if ($last_id == 0) {
                jrCore_logger('INF', "migration of like database to new format beginning");
            }
            $_in = array();
            foreach ($_sp['_items'] as $_l) {
                if ($_l['_user_id'] > 0) {
                    $_in[] = "('{$_l['_created']}','{$_l['_user_id']}','{$_l['like_item_id']}','{$_l['like_module']}','{$_l['like_action']}')";
                }
                else {
                    $uip = sprintf('%u', ip2long($_l['like_user_ip']));
                    $_in[] = "('{$_l['_created']}','{$uip}','{$_l['like_item_id']}','{$_l['like_module']}','{$_l['like_action']}')";
                }
                $last_id = (int) $_l['_item_id'];
                $total++;
            }
            $tbl = jrCore_db_table_name('jrLike', 'likes');
            $ins = "INSERT INTO {$tbl} (like_created, like_user_id, like_item_id, like_module, like_action) VALUES " . implode(',', $_in) . ' ON DUPLICATE KEY UPDATE like_action = VALUES(like_action)';
            $cnt = jrCore_db_query($ins, 'COUNT');
            if (!$cnt || $cnt === 0) {
                jrCore_logger('CRI', 'an error was encountered migrating like entries to new database format');
                return true;
            }
        }
        else {
            if ($total > 0) {
                jrCore_logger('INF', "successfully migrated " . jrCore_number_format($total) . " like entries to new database format");
                jrCore_db_truncate_datastore('jrLike');
            }
            break;
        }
    }
    return true;
}

//--------------------------
// FUNCTIONS
//--------------------------

/**
 * Create a new like or dislike for a DS item
 * @param array $_post
 * @param array $_user
 * @param array $_conf
 * @return bool
 */
function jrLike_like_create($_post, $_user, $_conf)
{
    global $_mods;

    // $_post.module - the module being (dis)liked
    // $_post._1     - the '_item_id' of the item being (dis)liked
    // $_post._2     - the action (like or dislike)

    jrCore_validate_location_url();
    $_ln = jrUser_load_lang_strings();

    // Check the module being (dis)liked exists and is enabled
    if (!jrCore_module_is_active($_post['module'])) {
        return jrCore_json_response(array('error' => 'Module is inactive'));
    }
    // Must be a DS module
    if (!jrCore_is_datastore_module($_post['module'])) {
        return jrCore_json_response(array('error' => 'Invalid datastore module'));
    }

    // Check we got a valid item id
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        return jrCore_json_response(array('error' => 'Invalid item id'));
    }

    // Check we got a valid action
    if (!isset($_post['_2']) || ($_post['_2'] != 'like' && $_post['_2'] != 'dislike')) {
        return jrCore_json_response(array('error' => 'Invalid like action - must be one of like/dislike'));
    }

    // Make sure we are liking a valid item
    $_item = jrCore_db_get_item($_post['module'], $_post['_1']);
    if (!$_item || !is_array($_item)) {
        return jrCore_json_response(array('error' => 'Invalid item'));
    }

    if (jrUser_is_logged_in()) {
        // Check Quota
        $allowed  = jrUser_get_profile_home_key('quota_jrLike_allowed');
        $selflike = jrUser_get_profile_home_key('quota_jrLike_allow_self_likings');
        if ($allowed && $allowed == 'off') {
            // Is the user allowed to like their own items?
            if ($selflike && $selflike != 'on') {
                return jrCore_json_response(array('error' => $_ln['jrLike'][18]));
            }
        }
        // Are user allowed to self like?
        if (jrUser_can_edit_item($_item) && $selflike == 'off' && $_item['_user_id'] == $_user['_user_id']) {
            return jrCore_json_response(array('error' => $_ln['jrLike'][19]));
        }
        $uid = (int) $_user['_user_id'];
    }
    else {
        // Check visitor
        if (isset($_conf['jrLike_require_login']) && $_conf['jrLike_require_login'] == 'on') {
            return jrCore_json_response(array('error' => $_ln['jrLike'][20]));
        }
        $uid = sprintf('%u', ip2long(jrCore_get_ip()));
    }

    // Looking good - get some variables that we'll need
    $pfx = jrCore_db_get_prefix($_post['module']);

    // See if this user already has a like
    $tbl = jrCore_db_table_name('jrLike', 'likes');
    $req = "SELECT like_action FROM {$tbl} WHERE like_user_id = {$uid} AND like_item_id = '{$_post['_1']}' AND like_module = '" . jrCore_db_escape($_post['module']) . "' LIMIT 1";
    $_ex = jrCore_db_query($req, 'SINGLE');

    // See if the user is CHANGING their like
    $upv = $_post['_2'];
    if ($_ex && is_array($_ex) && isset($_ex['like_action'])) {
        if ($_ex['like_action'] == 'like' && $_post['_2'] == 'like') {
            $upv = 'neutral';
        }
        if ($_ex['like_action'] == 'dislike' && $_post['_2'] == 'dislike') {
            $upv = 'neutral';
        }
    }

    // Record like
    // Important: the extra jrCore_db_connect() call is here so we can get 0,1,2 back from the MySQL "COUNT" for the ON DUPLICATE KEY UPDATE
    $con = jrCore_db_connect(true, false);
    $req = "INSERT INTO {$tbl} (like_created, like_user_id, like_item_id, like_module, like_action)
            VALUES(UNIX_TIMESTAMP(), {$uid}, '{$_post['_1']}', '" . jrCore_db_escape($_post['module']) . "', '{$upv}')
            ON DUPLICATE KEY UPDATE like_created = UNIX_TIMESTAMP(), like_action = '{$upv}'";
    $cnt = jrCore_db_query($req, 'COUNT', false, null, true, $con);
    jrCore_db_close();

    if (!is_numeric($cnt)) {

        // We encountered a DB Error
        return jrCore_json_response(array('error' => 'an error was encountered saving the like - please try again'));
    }

    elseif ($cnt === 2) {

        // Like already exists and the user has updated
        // Get the user's previous like or dislike and update accordingly
        if (isset($_ex['like_action'])) {
            $upd = false;
            $exv = false;
            if ($_ex['like_action'] == 'like') {
                if ($_post['_2'] == 'like') {
                    // User is changing their "like" to a "neutral"
                    $exv = 'like';     // Existing Value to DECREMENT
                }
                elseif ($_post['_2'] == 'dislike') {
                    // User is changing their "like" to a "dislike"
                    $exv = 'like';     // Existing Value
                    $upd = 'dislike';  // New Value
                }
            }
            elseif ($_ex['like_action'] == 'dislike') {
                if ($_post['_2'] == 'dislike') {
                    // User is changing their "dislike" to a "neutral"
                    $exv = 'dislike';
                }
                elseif ($_post['_2'] == 'like') {
                    // User is changing their "dislike" to a "like"
                    $exv = 'dislike';
                    $upd = 'like';
                }
            }
            elseif ($_ex['like_action'] == 'neutral') {
                $upd = $_post['_2'];
            }

            // We changed our like - maintain item counts
            $ppfx = jrCore_db_get_prefix('jrProfile');
            if ($upd) {
                jrCore_db_increment_key($_post['module'], $_post['_1'], "{$pfx}_{$upd}_count", 1);
                if (jrUser_get_profile_home_key('_profile_id') != $_item['_profile_id']) {
                    jrCore_db_increment_key('jrProfile', $_item['_profile_id'], "{$ppfx}_jrLike_{$upd}_home_item_count", 1);
                }
            }
            if ($exv) {
                jrCore_db_decrement_key($_post['module'], $_post['_1'], "{$pfx}_{$exv}_count", 1);
                if (jrUser_get_profile_home_key('_profile_id') != $_item['_profile_id']) {
                    jrCore_db_decrement_key('jrProfile', $_item['_profile_id'], "{$ppfx}_jrLike_{$exv}_home_item_count", 1);
                }
            }
        }

        // Event trigger
        $_args = array(
            'like_module'  => $_post['module'],
            'like_item_id' => $_post['_1'],
            'like_action'  => $upv,
            'liked_item'   => $_item
        );
        jrCore_trigger_event('jrLike', 'item_liked', $_item, $_args);
    }

    elseif ($cnt === 1) {

        // 1 = new row inserted - first time like or dislike
        jrCore_db_increment_key($_post['module'], $_post['_1'], "{$pfx}_{$_post['_2']}_count", 1);

        // Increment number of likes for profile
        if (jrUser_get_profile_home_key('_profile_id') != $_item['_profile_id']) {
            jrCore_db_increment_key('jrProfile', $_item['_profile_id'], "profile_jrLike_{$_post['_2']}_home_item_count", 1);
        }

        if (jrUser_is_logged_in()) {

            $ttl = false;
            $url = false;
            // Trigger event to get item Title and URL
            $_data = array(
                'item_url'   => '',
                'item_title' => ''
            );
            $_data = jrCore_trigger_event('jrLike', 'item_action_info', $_data, $_item, $_post['module']);
            if (isset($_data['item_title']) && strlen($_data['item_title']) > 0) {
                $ttl = $_data['item_title'];
            }
            if (isset($_data['item_url']) && strlen($_data['item_url']) > 0) {
                $url = $_data['item_url'];
            }
            if (!$ttl || !$url) {
                switch ($_post['module']) {
                    case 'jrProfile':
                        $ttl = $_item['profile_name'];
                        $url = "{$_conf['jrCore_base_url']}/{$_item['profile_url']}";
                        break;
                    case 'jrGuestbook':
                        $ttl = $_item['profile_name'] . ' - ' . $_ln['jrGuestbook']['menu'];
                        $url = "{$_conf['jrCore_base_url']}/{$_item['profile_url']}/{$_post['module_url']}";
                        break;
                    case 'jrUser':
                        $ttl = $_item['user_name'];
                        $url = "{$_conf['jrCore_base_url']}/{$_item['profile_url']}";
                        break;
                    case 'jrAction':
                        $ttl = '';
                        if (isset($_item["{$pfx}_text"])) {
                            $txt = strip_tags($_item["{$pfx}_text"]);
                            if (strlen($txt) > 60) {
                                $txt = substr($txt, 0, 60) . '...';
                            }
                            $ttl = "{$_ln['jrLike'][3]}: {$txt}";
                        }
                        elseif (isset($_item["{$pfx}_item"])) {
                            $ipfx = jrCore_db_get_prefix($_item["{$pfx}_module"]);
                            $ttl  = $_item["{$pfx}_item"]["{$ipfx}_title"];
                        }
                        $url = "{$_conf['jrCore_base_url']}/{$_item['profile_url']}/{$_post['module_url']}/{$_post['_1']}";
                        break;
                    case 'jrComment':
                        $ttl = strip_tags($_item["{$pfx}_text"]);
                        if (strlen($ttl) > 60) {
                            $ttl = substr($ttl, 0, 60) . '...';
                        }
                        $url = "{$_conf['jrCore_base_url']}/{$_item['profile_url']}/{$_post['module_url']}/{$_post['_1']}";
                        break;
                    case 'jrForum':
                        $furl = jrLike_get_forum_url($_item['forum_group_id']);
                        $url  = "{$_conf['jrCore_base_url']}/{$furl}";
                        $ttl  = $_item["{$pfx}_title"];
                        break;
                    default:
                        $ttl = $_item["{$pfx}_title"];
                        $url = "{$_conf['jrCore_base_url']}/{$_item['profile_url']}/{$_post['module_url']}/{$_post['_1']}/{$_item["{$pfx}_title_url"]}";
                        break;
                }
            }

            // Record Action
            if (isset($_conf['jrLike_allow_actions']) && $_conf['jrLike_allow_actions'] == 'on') {
                // Some modules we do NOT record a LIKE to the timeline for
                switch ($_post['module']) {
                    case 'jrFollower':
                        break;
                    default:
                        // We need to get to the inserted ID of what we just did
                        $tbl = jrCore_db_table_name('jrLike', 'likes');
                        $req = "SELECT * FROM {$tbl} WHERE like_user_id = {$uid} AND like_item_id = '{$_post['_1']}' AND like_module = '" . jrCore_db_escape($_post['module']) . "' LIMIT 1";
                        $_rt = jrCore_db_query($req, 'SINGLE');
                        if ($_rt && is_array($_rt)) {
                            $_rt['action_original_module']  = $_post['module'];
                            $_rt['action_original_item_id'] = (int) $_post['_1'];
                            $_rt['quota_jrAction_allowed']  = (isset($_user['quota_jrAction_allowed'])) ? $_user['quota_jrAction_allowed'] : false;
                            jrCore_run_module_function('jrAction_save', $_post['_2'], 'jrLike', $_rt['like_id'], $_rt, false, jrUser_get_profile_home_key('_profile_id'), $_item['_profile_id']);
                        }
                }
            }

            // Notifications
            // They are not sent multiple notifications if the (dis)liker changes his/her mind and reverses the (dis)like
            // They are not sent a notification if its an item they own
            // They are not sent a notification if a non-logged in visitor has (dis)liked their item
            if (jrUser_get_profile_home_key('_profile_id') != $_item['_profile_id']) {
                // Notify
                $_owners = jrProfile_get_owner_info($_item['_profile_id']);
                if ($_owners && is_array($_owners)) {
                    $_info = array(
                        'system_name'    => $_conf['jrCore_system_name'],
                        'like_user_name' => $_user['user_name'],
                        'like_title'     => $ttl,
                        'like_url'       => $url,
                        'like_module'    => $_post['module'],
                        'like_action'    => $_post['_2']
                    );
                    list($sub, $msg) = jrCore_parse_email_templates('jrLike', 'new_like', $_info);
                    foreach ($_owners as $_o) {
                        if ($_o['_user_id'] != $_user['_user_id']) {
                            jrUser_notify($_o['_user_id'], 0, 'jrLike', 'new_like', $sub, $msg);
                        }
                    }
                }
            }
        }

        // Event trigger
        $_args = array(
            'like_module'  => $_post['module'],
            'like_item_id' => $_post['_1'],
            'like_action'  => $upv,
            'liked_item'   => $_item
        );
        jrCore_trigger_event('jrLike', 'item_liked', $_item, $_args);
    }

    // Fall through - success
    $imurl = "{$_conf['jrCore_base_url']}/" . jrCore_get_module_url('jrImage') . "/img/module/jrLike";
    if ($_post['_2'] == 'like') {
        // New like or going neutral from an existing like?
        if (isset($_ex['like_action']) && $_ex['like_action'] == 'like') {
            $l_src = "{$imurl}/like.png";
            $d_src = "{$imurl}/dislike.png";
            $l_ttl = jrCore_entity_string($_ln['jrLike'][4]);
            $d_ttl = jrCore_entity_string($_ln['jrLike'][5]);
        }
        else {
            $l_src = "{$imurl}/liked.png";
            $d_src = "{$imurl}/dislike_greyed.png";
            $l_ttl = jrCore_entity_string($_ln['jrLike'][6]);
            $d_ttl = jrCore_entity_string($_ln['jrLike'][5]);
        }
    }
    elseif ($_post['_2'] == 'dislike') {
        // New dislike or going neutral from an existing dislike?
        if (isset($_ex['like_action']) && $_ex['like_action'] == 'dislike') {
            $l_src = "{$imurl}/like.png";
            $d_src = "{$imurl}/dislike.png";
            $l_ttl = jrCore_entity_string($_ln['jrLike'][4]);
            $d_ttl = jrCore_entity_string($_ln['jrLike'][5]);
        }
        else {
            $l_src = "{$imurl}/like_greyed.png";
            $d_src = "{$imurl}/disliked.png";
            $l_ttl = jrCore_entity_string($_ln['jrLike'][4]);
            $d_ttl = jrCore_entity_string($_ln['jrLike'][7]);
        }
    }
    else {
        // Neutral
        $l_src = "{$imurl}/like.png";
        $d_src = "{$imurl}/dislike.png";
        $l_ttl = jrCore_entity_string($_ln['jrLike'][4]);
        $d_ttl = jrCore_entity_string($_ln['jrLike'][5]);
    }
    list($l_cnt, $d_cnt) = jrLike_get_like_counts($_post['module'], $_post['_1']);
    $_rs = array(
        'OK'    => 1,
        'l_src' => $l_src . "?s={$_conf['jrCore_active_skin']}&_v={$_mods['jrLike']['module_version']}",
        'l_ttl' => $l_ttl,
        'l_cnt' => $l_cnt,
        'd_src' => $d_src . "?s={$_conf['jrCore_active_skin']}&_v={$_mods['jrLike']['module_version']}",
        'd_ttl' => $d_ttl,
        'd_cnt' => $d_cnt
    );
    jrUser_reset_cache($_user['_user_id'], 'jrLike');
    jrProfile_reset_cache($_item['_profile_id']);
    return jrCore_json_response($_rs);
}

/**
 * Get number of likes and dislikes for an item
 * @param $module string Module
 * @param $item_id int Item ID
 * @return mixed
 */
function jrLike_get_like_counts($module, $item_id)
{
    $tbl = jrCore_db_table_name('jrLike', 'likes');
    $req = "SELECT like_action, COUNT(like_action) AS cnt FROM {$tbl} WHERE like_item_id = " . intval($item_id) . " AND like_module = '" . jrCore_db_escape($module) . "' GROUP BY like_action";
    $_rt = jrCore_db_query($req, 'like_action', false, 'cnt');
    if ($_rt && is_array($_rt)) {
        $l = (isset($_rt['like'])) ? intval($_rt['like']) : 0;
        $d = (isset($_rt['dislike'])) ? intval($_rt['dislike']) : 0;
        return array($l, $d);
    }
    return 0;
}

/**
 * return the correct URL for a forum post
 * @param $item_id int Item ID
 * @return string
 */
function jrLike_get_forum_url($item_id)
{
    if (!jrCore_checktype($item_id, 'number_nz')) {
        return '';
    }
    // Is jrLike module enabled?
    if (!jrCore_module_is_active('jrLike')) {
        return '';
    }
    // Is target module enabled?
    if (!jrCore_module_is_active('jrForum')) {
        return '';
    }

    $murl        = jrCore_get_module_url('jrForum');
    $item        = jrCore_db_get_item('jrForum', $item_id);
    $_cfg        = jrForum_get_config($item['forum_profile_id']);
    $profile_url = jrCore_db_get_item_key('jrProfile', $item['forum_profile_id'], 'profile_url');

    if (isset($item['forum_cat_url']) && strlen($item['forum_cat_url']) > 0 && isset($_cfg['enable_cats']) && $_cfg['enable_cats'] == 'on') {
        $url = "{$profile_url}/{$murl}/{$item['forum_cat_url']}/{$item['_item_id']}/{$item['forum_title_url']}";
    }
    else {
        $url = "{$profile_url}/{$murl}/{$item['_item_id']}/{$item["forum_title_url"]}";
    }
    return $url;
}

//--------------------------
// SMARTY FUNCTIONS
//--------------------------

/**
 * Smarty function to return all items a user has liked
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrLike_liked($params, $smarty)
{
    // User ID good?
    if (!isset($params['user_id'])) {
        return jrCore_smarty_missing_error('user_id');
    }
    if (!jrCore_checktype($params['user_id'], 'number_nz')) {
        return jrCore_smarty_invalid_error('user_id');
    }

    // Build liked items query
    $uid = (int) $params['user_id'];
    $tbl = jrCore_db_table_name('jrLike', 'likes');
    $req = "SELECT * FROM {$tbl} WHERE like_user_id = {$uid}";
    if (isset($params['action']) && $params['action'] == 'both') {
        $req .= " AND (like_action = 'like' OR like_action = 'dislike')";
    }
    elseif (isset($params['action']) && $params['action'] == 'dislike') {
        $req .= " AND like_action = 'dislike'";
    }
    else {
        $req .= " AND like_action = 'like'";
    }
    if (isset($params['module']) && strlen($params['module']) > 0) {
        $_mds = explode(',', $params['module']);
        if ($_mds && is_array($_mds)) {
            $_add = array();
            foreach ($_mds as $mod) {
                if (jrCore_module_is_active($mod)) {
                    $_add[] = $mod;
                }
            }
            if (count($_add) > 0) {
                $req .= " AND like_module IN('" . implode("','", $_add) . "')";
            }
        }
    }
    if (isset($params['order_by'])) {
        switch (strtolower($params['order_by'])) {
            case 'module asc':
                $req .= " ORDER BY like_module ASC";
                break;
            case 'module desc':
                $req .= " ORDER BY like_module DESC";
                break;
            case 'action asc':
                $req .= " ORDER BY like_action ASC";
                break;
            case 'action desc':
                $req .= " ORDER BY like_action DESC";
                break;
            case 'created asc':
                $req .= " ORDER BY like_id ASC";
                break;
            default:
                $req .= " ORDER BY like_id DESC";
                break;
        }
    }
    if (isset($params['limit']) && jrCore_checktype($params['limit'], 'number_nz')) {
        $req .= " LIMIT {$params['limit']}";
    }
    else {
        $req .= ' LIMIT 10';
    }

    // Get items
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (!$_rt || !is_array($_rt) || count($_rt) == 0) {
        if (isset($params['assign']) && $params['assign'] != '') {
            $smarty->assign($params['assign'], '');
        }
        return ' ';
    }

    // Sort into DS types and IDs
    $_likes = array();
    foreach ($_rt as $rt) {
        if (!isset($_likes["{$rt['like_module']}"])) {
            $_likes["{$rt['like_module']}"] = array();
        }
        $_likes["{$rt['like_module']}"]["{$rt['like_item_id']}"] = $rt;
    }

    // Get each liked item and add it to original array
    $_rp = array();
    foreach ($_likes as $mod => $_items) {
        $pfx = jrCore_db_get_prefix($mod);
        if ($pfx) {
            $_sp = array(
                'search'                       => array(
                    '_item_id in ' . implode(',', array_keys($_items))
                ),
                'exclude_jrProfile_quota_keys' => true,
                'limit'                        => count($_items)
            );
            // Are we requiring images?
            if (isset($params['require_image']) && $params['require_image'] == true) {
                $_sp['require_image'] = "{$pfx}_image";
            }
            $_sp = jrCore_db_search_items($mod, $_sp);
            if ($_sp && is_array($_sp) && isset($_sp['_items'])) {
                foreach ($_sp['_items'] as $x) {
                    $iid   = (int) $x['_item_id'];
                    $_rp[] = array_merge($_likes[$mod][$iid], $x);
                }
            }
        }
    }
    unset($_likes);

    // Template parameters
    if (!isset($params['template']{0})) {
        $params['template'] = 'liked.tpl';
    }
    if (!isset($params['tpl_dir']{0})) {
        $params['tpl_dir'] = 'jrLike';
    }

    // Output
    $_rp = array(
        '_items' => $_rp,
        'params' => $params
    );
    $out = jrCore_parse_template($params['template'], $_rp, $params['tpl_dir']);
    if (isset($params['assign']) && $params['assign'] != '') {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Smarty function to return a like/dislike button
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrLike_button($params, $smarty)
{
    global $_conf, $_user;

    // Is target module enabled?
    if (!jrCore_module_is_active($params['module'])) {
        return '';
    }
    $params['module_url'] = jrCore_get_module_url($params['module']);

    // Check the incoming parameters
    if (!isset($params['item']) || !is_array($params['item'])) {
        if (isset($params['item_id']) && jrCore_checktype($params['item_id'], 'number_nz')) {
            $params['item'] = jrCore_db_get_item($params['module'], $params['item_id']);
        }
        if (!isset($params['item']) || !is_array($params['item'])) {
            return jrCore_smarty_missing_error('item');
        }
    }
    if (!isset($params['action'])) {
        return jrCore_smarty_missing_error('action');
    }
    if ($params['action'] != 'like' && $params['action'] != 'dislike') {
        return jrCore_smarty_invalid_error('action');
    }
    if (!(($params['action'] == 'like' && ($_conf['jrLike_like_option'] == 'like' || $_conf['jrLike_like_option'] == 'all')) || ($params['action'] == 'dislike' && ($_conf['jrLike_like_option'] == 'dislike' || $_conf['jrLike_like_option'] == 'all')))) {
        return '';
    }
    if (!(isset($params['style']) && $params['style'] != '')) {
        $params['style'] = '';
    }
    if (!(isset($params['class']) && $params['class'] != '')) {
        $params['class'] = '';
    }

    if (isset($params['template']{0}) && isset($params['tpl_dir']{0})) {
        //allow other modules to set the tpl_dir.
    }
    elseif (isset($params['template']{0})) {
        $params['tpl_dir'] = $_conf['jrCore_active_skin'];
    }
    else {
        $params['template'] = "button.tpl";
        $params['tpl_dir']  = 'jrLike';
    }

    // if the like is jrProfile, it needs an item_id.
    if ($params['module'] == 'jrProfile' && jrCore_checktype($params['item']['_profile_id'], 'number_nz') && !isset($params['item']['_item_id'])) {
        $params['item']['_item_id'] = $params['item']['_profile_id'];
    }

    // Get button status
    if (isset($params['nolike']) && $params['nolike'] != false) {

        // Force buttons to disabled state
        $params['like_status']    = 'like_greyed';
        $params['dislike_status'] = 'dislike_greyed';
    }
    elseif (jrUser_is_logged_in() && $_user['quota_jrLike_allow_self_likings'] == 'off' && jrUser_get_profile_home_key('_profile_id') == $params['item']['_profile_id']) {

        // Not allowed to like - force to disabled state
        $params['like_status']    = 'like_greyed';
        $params['dislike_status'] = 'dislike_greyed';

    }
    else {

        $check = true;
        if (jrUser_is_logged_in()) {

            $uid = $_user['_user_id'];
            // See if this user us allowed to (dis)like
            if (isset($_user['quota_jrLike_allowed']) && $_user['quota_jrLike_allowed'] == 'off') {
                // Not allowed to like - are we allowed to like our OWN items?
                if (isset($_user['quota_jrLike_allow_self_likings']) && $_user['quota_jrLike_allow_self_likings'] == 'on' && jrUser_can_edit_item($params['item'])) {
                    // They can like this
                    $params['like_status']    = 'like';
                    $params['dislike_status'] = 'dislike';
                }
                else {
                    // No likes at all
                    $params['like_status']    = 'like_greyed';
                    $params['dislike_status'] = 'dislike_greyed';
                    $check                    = false;
                }
            }

        }
        else {

            $uid = sprintf('%u', ip2long(jrCore_get_ip()));
            if (isset($_conf['jrLike_require_login']) && $_conf['jrLike_require_login'] == 'on') {
                // Not allowed to like - force to disabled state
                $params['like_status']    = 'like_greyed';
                $params['dislike_status'] = 'dislike_greyed';
            }
        }

        if ($check) {
            // Have they already like/disliked?
            $iid = intval($params['item']['_item_id']);
            $key = "jrlike_like_check_cache_{$uid}_{$iid}";
            if (!$_rt = jrCore_get_flag($key)) {
                $tbl = jrCore_db_table_name('jrLike', 'likes');
                $req = "SELECT like_action FROM {$tbl} WHERE like_user_id = {$uid} AND like_item_id = {$iid} AND like_module = '{$params['module']}' LIMIT 1";
                $_rt = jrCore_db_query($req, 'SINGLE');
                if (!$_rt || !is_array($_rt)) {
                    $_rt = 'no_results';
                }
                jrCore_set_flag($key, $_rt);
            }
            if ($_rt && is_array($_rt)) {

                if ($_rt['like_action'] == 'like') {
                    // Already liked this item
                    $params['like_status']    = 'liked';
                    $params['dislike_status'] = 'dislike_greyed';
                }
                elseif ($_rt['like_action'] == 'neutral') {
                    $params['like_status']    = 'like';
                    $params['dislike_status'] = 'dislike';
                }
                else {
                    // Already DIS liked this item
                    $params['like_status']    = 'like_greyed';
                    $params['dislike_status'] = 'disliked';
                }
            }
            else {
                // Free to choose either like or dislike
                $params['like_status']    = 'like';
                $params['dislike_status'] = 'dislike';
            }
        }

    }

    // Get existing count
    $params["{$params['action']}_count"] = 0;
    $pfx                                 = jrCore_db_get_prefix($params['module']);
    if (isset($params['item']["{$pfx}_{$params['action']}_count"]) && $params['item']["{$pfx}_{$params['action']}_count"] > 0) {
        $params["{$params['action']}_count"] = (int) $params['item']["{$pfx}_{$params['action']}_count"];
    }

    // Figure unique ID
    $act = $params['action'];
    $iid = (int) $params['item']['_item_id'];
    $key = "{$params['module']}-{$iid}";
    $_tm = jrCore_get_flag('jrlike-unique-map');
    if (!$_tm) {
        $_tm = array();
    }
    if (isset($_tm[$key])) {
        // If we have seen this ID before - is this a NEW action on this item?
        if (isset($_tm[$key][$act])) {
            // This is a NEW like/dislike set coming in for a previously seen item_id
            $unq             = jrCore_create_unique_string(8);
            $_tm[$key][$act] = $unq;
        }
        else {
            // We already have seen this one - see if we have already done the opposite action
            $opp = ($act == 'like') ? 'dislike' : 'like';
            $unq = $_tm[$key][$opp];
            unset($_tm[$key]);
        }
    }
    else {
        // We have NOT seen a like or dislike for this yet
        $unq       = jrCore_create_unique_string(8);
        $_tm[$key] = array($act => $unq);
    }
    jrCore_set_flag('jrlike-unique-map', $_tm);
    $params['unique_id'] = $unq;
    $out                 = jrCore_parse_template($params['template'], $params, $params['tpl_dir']);

    if (isset($params['assign']) && $params['assign'] != '') {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

//--------------------------
// ITEM FEATURES
//--------------------------

/**
 * Return like and dislike buttons (if enabled)
 * @param string $module Module item belongs to
 * @param array $_item Item info (from DS)
 * @param array $params Smarty function parameters
 * @param array $smarty current Smarty object
 * @return string
 */
function jrLike_item_likes_feature($module, $_item, $params, $smarty)
{
    // See if we are enabled in this quota
    if (!isset($_item['quota_jrLike_show_detail']) || $_item['quota_jrLike_show_detail'] != 'on') {
        return '';
    }
    $smarty           = new stdClass;
    $params['action'] = 'like';
    $out              = smarty_function_jrLike_button($params, $smarty);
    $params['action'] = 'dislike';
    $out              .= smarty_function_jrLike_button($params, $smarty);
    if (strlen($out) > 0) {
        $params['out'] = $out;
        return jrCore_parse_template('detail_buttons.tpl', $params, 'jrLike');
    }
    return '';
}
