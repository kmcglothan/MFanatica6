<?php
/**
 * Jamroom Comments module
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
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function jrComment_meta()
{
    $_tmp = array(
        'name'        => 'Comments',
        'url'         => 'comment',
        'version'     => '1.9.4',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Adds Users Comments to Profiles and Item Detail pages',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/275/comments',
        'category'    => 'item features',
        'requires'    => 'jrCore:6.0.0',
        'priority'    => 250,
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrComment_init()
{
    global $_conf;

    // Register our DS listeners
    jrCore_register_event_listener('jrCore', 'db_get_item', 'jrComment_db_get_item_listener');
    jrCore_register_event_listener('jrCore', 'db_update_item', 'jrComment_db_update_item_listener');
    jrCore_register_event_listener('jrCore', 'db_delete_item', 'jrComment_db_delete_item_listener');
    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrComment_db_search_params_listener');
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrComment_db_search_items_listener');
    jrCore_register_event_listener('jrCore', 'repair_module', 'jrComment_repair_module_listener');
    jrCore_register_event_listener('jrCore', 'minute_maintenance', 'jrComment_minute_maintenance_listener');

    jrCore_register_event_listener('jrAction', 'action_data', 'jrComment_action_data_listener');
    jrCore_register_event_listener('jrFeed', 'create_rss_feed', 'jrComment_create_rss_feed_listener');
    jrCore_register_event_listener('jrMarket', 'updated_module', 'jrComment_updated_module_listener');
    jrCore_register_event_listener('jrProfile', 'item_detail_view', 'jrComment_item_detail_view_listener');

    // Let the core Action System know we are adding action Support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrComment', 'on');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrComment', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrComment', 'create', 'item_action.tpl');

    // Pulse Key support
    jrCore_register_module_feature('jrProfile', 'pulse_key', 'jrComment', 'profile_jrComment_home_item_count', 'comments');

    // notifications
    $_tmp = array(
        'label' => 5, // 5 = 'new comment posted'
        'help'  => 13 // 13 = 'If a new comment is posted on one of your items, would you like to be notified?'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrComment', 'new_comment', $_tmp);

    if (isset($_conf['jrComment_threading']) && $_conf['jrComment_threading'] == 'on') {
        $_tmp = array(
            'label' => 27, // 27 = new reply to your comment
            'help'  => 28  // 28 = If someone responds to one of your comments would you like to be notified?
        );
        jrCore_register_module_feature('jrUser', 'notification', 'jrComment', 'new_reply', $_tmp);
    }

    // Our submit comment JS/CSS
    jrCore_register_module_feature('jrCore', 'javascript', 'jrComment', 'jrComment.js');
    jrCore_register_module_feature('jrCore', 'css', 'jrComment', 'jrComment.css');

    // We let other modules cancel our add to timeline for a new comment
    jrCore_register_event_trigger('jrComment', 'add_to_timeline', 'Fired before a comment action is added to a profile timeline');
    jrCore_register_event_trigger('jrComment', 'private_item_ids', 'Fired in the jrComment db_search_params listener to get private item ids');

    // We offer a module detail feature for comments
    $_tmp = array(
        'function' => 'jrComment_item_comments_feature',
        'label'    => 'Item Comments',
        'help'     => 'Adds User Comments to Item Detail pages'
    );
    jrCore_register_module_feature('jrCore', 'item_detail_feature', 'jrComment', 'item_comments', $_tmp);

    // Don't show as a profile menu option
    jrCore_register_module_feature('jrProfile', 'profile_menu', 'jrComment', 'exclude', true);

    // Verify DB queue worker
    jrCore_register_queue_worker('jrComment', 'verify_db', 'jrComment_verify_db_worker', 0, 1, 14400);

    // Private ID collector queue worker
    jrCore_register_queue_worker('jrComment', 'private_id', 'jrComment_private_id_worker', 0, 1, 14400);

    // Text field can be searched in the ChangeOwner module
    jrCore_register_module_feature('jrChangeOwner', 'search_field', 'jrComment', 'comment_text');

    return true;
}

//---------------------
// QUEUE WORKERS
//---------------------

/**
 * Verify Comment Database
 * @param array $_queue The queue entry the worker will receive
 * @return bool
 */
function jrComment_verify_db_worker($_queue)
{
    // Add comment_thread_id to items that do not have it
    $_rt = jrCore_db_get_items_missing_key('jrComment', 'comment_thread_id');
    if ($_rt && is_array($_rt)) {
        $_up = array();
        while (true) {
            foreach ($_rt as $k => $id) {
                $_up[$id] = array('comment_thread_id' => $id);
                if ($k > 0 && ($k % 1000) === 0 && count($_up) > 0) {
                    jrCore_db_update_multiple_items('jrComment', $_up);
                    $_up = array();
                }
            }
            if (count($_up) > 0) {
                jrCore_db_update_multiple_items('jrComment', $_up);
            }
            break;
        }
        jrCore_logger('INF', "added correct comment_thread_id key to " . jrCore_number_format(count($_rt)) . " comments");
    }
    return true;
}

/**
 * Populate Private ID table
 * @param array $_queue The queue entry the worker will receive
 * @return bool
 */
function jrComment_private_id_worker($_queue)
{
    global $_mods;
    @ini_set('memory_limit', '512M');
    if (isset($_queue['truncate']) && $_queue['truncate'] === true) {
        $tbl = jrCore_db_table_name('jrComment', 'private_id');
        jrCore_db_query("TRUNCATE TABLE {$tbl}");
    }
    foreach ($_mods as $mod => $_inf) {
        // Note: function ensures the module is a DS module + supports private items
        jrComment_save_private_comment_ids_for_module($mod);
    }
    return true;
}

//---------------------
// ITEM FEATURES
//---------------------

/**
 * Return a comment form field entry and existing comments
 * @param string $module Module item belongs to
 * @param array $_item Item info (from DS)
 * @param array $params Smarty function parameters
 * @param array $smarty current Smarty object
 * @return string
 */
function jrComment_item_comments_feature($module, $_item, $params, $smarty)
{
    global $_conf;
    // See if we are enabled in this quota
    if (isset($_item['quota_jrComment_show_detail']) && $_item['quota_jrComment_show_detail'] == 'off') {
        return '';
    }
    if (isset($params['template']) && $params['template'] != '') {
        $params['tpl_dir'] = $_conf['jrCore_active_skin'];
    }
    else {
        $params['template'] = "comment_form.tpl";
        $params['tpl_dir']  = 'jrComment';
    }
    if (!isset($params['style']) || strlen($params['style']) === 0) {
        $params['style'] = '';
    }
    if (!isset($params['class']) || strlen($params['class']) === 0) {
        $params['class'] = '';
    }
    $_tmp = array(
        'jrComment' => array(
            'module'     => $module,
            'profile_id' => $_item['_profile_id'],
            'item_id'    => $_item['_item_id'],
            'unique_id'  => "{$module}_{$_item['_profile_id']}_{$_item['_item_id']}",
            'pagebreak'  => (isset($params['pagebreak']) && jrCore_checktype($params['pagebreak'], 'number_nz')) ? intval($params['pagebreak']) : $_conf['jrComment_pagebreak']
        ),
        '_item'     => $_item
    );
    foreach ($params as $k => $v) {
        $_tmp['jrComment'][$k] = $v;
    }
    // Check for order_by
    if (!isset($_tmp['jrComment']['comment_order_by'])) {
        $_tmp['jrComment']['comment_order_by'] = (isset($_conf['jrComment_direction'])) ? $_conf['jrComment_direction'] : 'numerical_desc';
    }
    // Flag so we can properly change order in db_search_params listener
    return jrCore_parse_template($params['template'], $_tmp, $params['tpl_dir']);
}

//---------------------
// EVENT LISTENERS
//---------------------

/**
 * Updated module - ensure private_id table is populated
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrComment_minute_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    // Kick off private comment collector if we're empty
    $tbl = jrCore_db_table_name('jrComment', 'private_id');
    $req = "SELECT COUNT(*) AS c FROM {$tbl}";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt) && isset($_rt['c']) && intval($_rt['c']) === 0) {

        // We have no items in our private_id table - kick off queue entry to gather
        jrCore_queue_create('jrComment', 'private_id', array('truncate' => false));

    }
    return $_data;
}

/**
 * Updated module - ensure private_id table is populated
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrComment_updated_module_listener($_data, $_user, $_conf, $_args, $event)
{
    // Kick off private comment collector on update
    jrCore_queue_create('jrComment', 'private_id', array('now' => time()));
    return $_data;
}

/**
 * Add comment counts to action items
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrComment_action_data_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['action_data'])) {
        // comment counts
        $pfx = jrCore_db_get_prefix($_data['action_module']);
        if (isset($_data['action_data']["{$pfx}_comment_count"])) {
            $_data['action_item_comment_count'] = (int) $_data['action_data']["{$pfx}_comment_count"];
        }
        else {
            $_data['action_item_comment_count'] = 0;
        }
    }

    if (isset($_data['action_original_data'])) {

        // comment counts
        $pfx = jrCore_db_get_prefix($_data['action_original_module']);
        if (isset($_data['action_original_data']["{$pfx}_comment_count"])) {
            $_data['action_original_item_comment_count'] = (int) $_data['action_original_data']["{$pfx}_comment_count"];
        }
        else {
            $_data['action_original_item_comment_count'] = 0;
        }

        // URL to item that was commented on
        if ($_data['action_original_module'] == 'jrComment' && !isset($_data['action_original_title'])) {
            $_it = jrCore_db_get_item($_data['action_original_data']['comment_module'], $_data['action_original_data']['comment_item_id']);
            if ($_it && is_array($_it)) {
                $url = jrCore_get_module_url($_data['action_original_data']['comment_module']);
                if ($pfx = jrCore_db_get_prefix($_data['action_original_data']['comment_module'])) {
                    $_data['action_original_title']     = $_it["{$pfx}_title"];
                    $_data['action_original_title_url'] = $_it["{$pfx}_title_url"];
                    $_data['action_original_item_url']  = "{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$url}/{$_it['_item_id']}/{$_data['action_original_title_url']}#cm{$_data['action_original_data']['_item_id']}";
                }
                $_data['_comment_data'] = $_it;
            }
        }

    }
    return $_data;
}

/**
 * Cleanup DS issues
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrComment_repair_module_listener($_data, $_user, $_conf, $_args, $event)
{
    // Remove DS fields we no longer need
    jrCore_db_delete_key_from_all_items('jrComment', 'comment_ip');
    jrCore_db_delete_key_from_all_items('jrComment', 'comment_profile_url');
    jrCore_db_delete_key_from_all_items('jrComment', 'comment_item_url');

    $num = jrCore_db_get_datastore_item_count('jrComment');
    if ($num > 0) {

        // Verify comments
        jrCore_queue_create('jrComment', 'verify_db', array('count' => $num));

        // Reset and reload private comments
        jrCore_queue_create('jrComment', 'private_id', array('truncate' => true));

    }

    return $_data;
}

/**
 * Format Comment entries for RSS feed
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrComment_create_rss_feed_listener($_data, $_user, $_conf, $_args, $event)
{
    // We override the "description" and format it differently
    if (isset($_args['module']) && $_args['module'] == 'jrComment') {
        $_lg = jrUser_load_lang_strings();
        foreach ($_data as $k => $_itm) {
            $_data[$k]['description'] = "{$_itm['profile_name']} {$_lg['jrComment'][3]} &quot;{$_itm["comment_item_title"]}&quot; - {$_itm["comment_text"]}";
        }
    }
    return $_data;
}

/**
 * Update private_id table when an item is set to private
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrComment_db_update_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($pfx = jrCore_db_get_prefix($_args['module'])) {

        // Does this item have a private key?
        if (isset($_data["{$pfx}_private"])) {

            // It does - get any existing comment_id's for this item
            $pid = (int) jrCore_db_get_item_key($_args['module'], $_args['_item_id'], '_profile_id');
            $tbl = jrCore_db_table_name('jrComment', 'private_id');
            $_rt = jrComment_get_comment_ids_for_item($_args['module'], $_args['_item_id']);
            if ($_rt && is_array($_rt)) {
                if (isset($_data["{$pfx}_private"]) && $_data["{$pfx}_private"] == 'on') {
                    // This item is private - make sure any comments on it are in our private_id table
                    if ($_rt && is_array($_rt)) {
                        $_in = array();
                        foreach ($_rt as $cid) {
                            $_in[] = "({$cid},{$pid})";
                        }
                        if (count($_in) > 0) {
                            $req = "INSERT IGNORE INTO {$tbl} (comment_id, profile_id) VALUES " . implode(',', $_in);
                            jrCore_db_query($req);
                        }
                    }
                }
                else {
                    // This item is NOT private - make sure any entries are removed
                    $req = "DELETE FROM {$tbl} WHERE profile_id = {$pid} AND comment_id IN(" . implode(',', $_rt) . ')';
                    jrCore_db_query($req);
                }
            }

        }
    }
    return $_data;
}

/**
 * Delete comment entries when an item is deleted
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrComment_db_delete_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['module']) && $_args['module'] == 'jrComment' && isset($_args['_item_id']) && jrCore_checktype($_args['_item_id'], 'number_nz')) {

        // Find comments associated with this item and remove
        $_rt = jrComment_get_comment_ids_for_item($_args['module'], $_args['_item_id']);
        if ($_rt && is_array($_rt)) {

            // Delete comments
            jrCore_db_delete_multiple_items('jrComment', $_rt, false);

            // Delete entries in private_id
            $tbl = jrCore_db_table_name('jrComment', 'private_id');
            $req = "DELETE FROM {$tbl} WHERE comment_id IN(" . implode(',', $_rt) . ')';
            jrCore_db_query($req);

        }
    }
    return $_data;
}

/**
 * Add comment_url key to return comment item
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrComment_db_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_conf;
    if ($_args['module'] == 'jrComment' && is_array($_data)) {
        if (isset($_data['profile_url'])) {
            $purl = $_data['profile_url'];
        }
        else {
            $purl = jrCore_db_get_item_key('jrProfile', $_data['_profile_id'], 'profile_url');
        }
        if ($purl) {
            $murl                 = jrCore_get_module_url($_data['comment_module']);
            $_data['comment_url'] = "{$_conf['jrCore_base_url']}/{$purl}/{$murl}/{$_data['comment_item_id']}";
            if (isset($_data['comment_item_title'])) {
                $_data['comment_url'] .= '/' . jrCore_url_string($_data['comment_item_title']);
            }
        }
    }
    return $_data;
}

/**
 * Set comment privacy flag to FALSE on detail view pages
 * If a user has access to view the ITEM, then they should see any comments on the item
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrComment_item_detail_view_listener($_data, $_user, $_conf, $_args, $event)
{
    jrCore_set_flag('jrcomment_disable_privacy_check', 1);
    return $_data;
}

/**
 * Only comments for items that are on active modules
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrComment_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    // On a comment list we only return items for modules that are active
    if ($_args['module'] == 'jrComment') {

        // See if we are already searching for a specific module...
        if (!isset($_conf['jrComment_check_modules']) || $_conf['jrComment_check_modules'] == 'on') {
            $add = true;
            if (isset($_data['search'])) {
                foreach ($_data['search'] as $k => $v) {
                    if (strpos(' ' . $v, 'comment_module')) {
                        // We are already specifying a comment_module - no need to add to it
                        $add = false;
                        break;
                    }
                }
            }
            else {
                $_data['search'] = array();
            }
            if ($add) {
                if ($_tmp = jrCore_get_datastore_modules()) {
                    $_mod = array();
                    foreach ($_tmp as $mod => $pfx) {
                        if (jrCore_module_is_active($mod)) {
                            $_mod[] = $mod;
                        }
                    }
                    if (count($_mod) > 0) {
                        $_data['search'][] = 'comment_module in ' . implode(',', $_mod);
                    }
                }
            }
        }

        // Exclude comments on private items
        if (!jrUser_is_admin() && !jrCore_get_flag('jrcomment_disable_privacy_check') && (!isset($_data['privacy_check']) || $_data['privacy_check'] == true)) {

            $tbl = jrCore_db_table_name('jrComment', 'private_id');
            if (jrUser_is_logged_in()) {

                // Logged in users can see comments on private items they have access to
                $_pr = array();
                $hid = (int) jrUser_get_profile_home_key('_profile_id');
                if ($hid > 0) {
                    $_pr[] = $hid;
                }
                if (isset($_user['user_active_profile_id']) && jrCore_checktype($_user['user_active_profile_id'], 'number_nz') && $_user['user_active_profile_id'] != $hid) {
                    $_pr[] = (int) $_user['user_active_profile_id'];
                }
                // Power/Multi users can always see the profiles they manage
                $_tm = jrProfile_get_user_linked_profiles($_user['_user_id']);
                if ($_tm && is_array($_tm)) {
                    $_pr = array_merge($_pr, array_keys($_tm));
                    unset($_tm);
                }
                if (count($_pr) > 0) {
                    $_data['search'][] = "_item_id not_in (SELECT `comment_id` FROM {$tbl} WHERE `profile_id` NOT IN(" . implode(',', array_unique($_pr, SORT_NUMERIC)) . "))";
                }
            }
            else {
                $_data['search'][] = "_item_id not_in (SELECT `comment_id` FROM {$tbl})";
            }
        }

        // Check for threading
        if (isset($_conf['jrComment_threading']) && $_conf['jrComment_threading'] == 'on' && !isset($_data['ignore_threading'])) {

            // Save a copy of our original params - we will use them down
            // below in the search_items listener to reconstruct our pagination
            jrCore_set_flag('jrcomment_original_db_search_params', $_data);

            $_data['order_by'] = array(
                '_item_id' => (isset($_conf['jrComment_direction'])) ? $_conf['jrComment_direction'] : 'desc'
            );
            $_data['limit']    = 1000;
            if (isset($_data['pagebreak'])) {
                unset($_data['pagebreak']);
            }
            if (isset($_data['pager'])) {
                unset($_data['pager']);
            }

        }

    }
    return $_data;
}

/**
 * Add comment_url key to return comment items
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrComment_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if ($_args['module'] == 'jrComment' && is_array($_data) && isset($_data['_items'])) {

        // Create our comment_url based on the comment info
        $_id = array();
        foreach ($_data['_items'] as $k => $_v) {
            $_id[] = $_v['comment_profile_id'];
        }
        if (count($_id) > 0) {
            $_pr = jrCore_db_get_multiple_items('jrProfile', $_id, array('_profile_id', 'profile_url'));
            if ($_pr && is_array($_pr)) {
                $_ur = array();
                foreach ($_pr as $_p) {
                    $_ur["{$_p['_profile_id']}"] = $_p['profile_url'];
                }
            }
        }

        foreach ($_data['_items'] as $k => $_v) {
            $purl                                       = (isset($_ur["{$_v['comment_profile_id']}"])) ? $_ur["{$_v['comment_profile_id']}"] : '';
            $_data['_items'][$k]['comment_profile_url'] = $purl;
            switch ($_v['comment_module']) {
                case 'jrProfile':
                    $_data['_items'][$k]['comment_url'] = "{$_conf['jrCore_base_url']}";
                    break;
                default:
                    $_data['_items'][$k]['comment_url'] = "{$_conf['jrCore_base_url']}/{$purl}/" . jrCore_get_module_url($_v['comment_module']) . "/{$_v['comment_item_id']}";
                    break;
            }
            if (isset($_v['comment_item_title'])) {
                $_data['_items'][$k]['comment_url'] .= '/' . jrCore_url_string($_v['comment_item_title']);
            }
        }

        // We have ro re-order if we have threaded comments
        if ((isset($_args['jrcore_list_function_call_is_active']) && $_args['jrcore_list_function_call_is_active'] == 1) && (!isset($_args['ignore_threading']) || $_args['ignore_threading'] == false) && (isset($_conf['jrComment_threading']) && $_conf['jrComment_threading'] == 'on')) {

            // With threading ON, we have to do our own pagination AFTER
            // we have properly order our result set
            if ($_temp = jrCore_get_flag('jrcomment_original_db_search_params')) {

                // Next - get comment map created
                $_tmp = array();
                $_new = array(0 => array());
                foreach ($_data['_items'] as $k => $_v) {
                    $lvl = 0;
                    if (isset($_v['comment_thread_level'])) {
                        $lvl = $_v['comment_thread_level'];
                        $pid = $_v['comment_parent_id'];
                        if (!isset($_new[$lvl][$pid])) {
                            $_new[$lvl][$pid] = array();
                        }
                        $_new[$lvl][$pid]["{$_v['_item_id']}"] = $_v['_item_id'];
                    }
                    else {
                        $_new[$lvl]["{$_v['_item_id']}"] = array($_v['_item_id'] => $_v['_item_id']);
                    }
                    $_tmp["{$_v['_item_id']}"] = $_v;
                }

                $_ttt = array();
                jrComment_thread_comments($_new, $_ttt);

                // Get pagination
                if (isset($_temp['pagebreak'])) {

                    // If we are here from a NEW POST we need to make
                    // sure that on refresh they see their new post..
                    $page = 1;
                    if (isset($_temp['page'])) {
                        $page = (int) $_temp['page'];
                    }
                    $slice = true;
                    if (isset($_post['new']) && jrCore_checktype($_post['new'], 'number_nz')) {
                        $idx = array_search($_post['new'], $_ttt);
                        if ($idx > $_temp['pagebreak']) {
                            $page  = ceil($idx / $_temp['pagebreak']);
                            $_ttt  = array_slice($_ttt, 0, ($page * $_temp['pagebreak']));
                            $slice = false;
                        }
                    }
                    if ($slice) {
                        $_ttt = array_slice($_ttt, (($page - 1) * $_temp['pagebreak']), $_temp['pagebreak']);
                    }
                    if (isset($_data['info']['limit'])) {
                        unset($_data['info']['limit']);
                    }
                    $_data['info']['page']          = $page;
                    $_data['info']['pagebreak']     = $_temp['pagebreak'];
                    $_data['info']['page_base_url'] = jrCore_strip_url_params(jrCore_get_current_url(), array('p'));
                    $_data['info']['prev_page']     = ($page > 1) ? ($page - 1) : 0;
                    $_data['info']['this_page']     = $page;
                    $_data['info']['next_page']     = ($page * $_temp['pagebreak']) < $_data['info']['total_items'] ? ($page + 1) : 0;
                    $_data['info']['next_page']     = (ceil($_data['info']['total_items'] / $_temp['pagebreak']) > $page) ? intval($page + 1) : 0;
                    $_data['info']['total_pages']   = ($_data['info']['total_items'] > 0) ? ceil($_data['info']['total_items'] / $_temp['pagebreak']) : 1;
                }
                elseif (isset($_temp['limit'])) {
                    $_ttt = array_slice($_ttt, 0, $_temp['limit']);
                }

                $_out = array();
                foreach ($_ttt as $id) {
                    $_out[] = $_tmp[$id];
                }
                $_data['_items'] = $_out;
            }
        }

    }
    return $_data;
}

//---------------------
// FUNCTIONS
//---------------------

/**
 * Get all comment _item_id's for a module item
 * @param string $module Module
 * @param int $item_id Module Item ID
 * @return array|bool|mixed
 */
function jrComment_get_comment_ids_for_item($module, $item_id)
{
    $iid = (int) $item_id;
    $_rt = array(
        'search'              => array(
            "comment_item_id = {$iid}",
            "comment_module = {$module}"
        ),
        'return_item_id_only' => true,
        'skip_triggers'       => true,
        'ignore_pending'      => true,
        'privacy_check'       => false,
        'order_by'            => false,
        'quota_check'         => false,
        'limit'               => 10000
    );
    $_rt = jrCore_db_search_items('jrComment', $_rt);
    if ($_rt && is_array($_rt)) {
        return $_rt;
    }
    return false;
}

/**
 * Save comment _item_id's that are comments on private items
 * @param $module string
 * @return array|bool
 */
function jrComment_save_private_comment_ids_for_module($module)
{
    global $_post;
    if (jrCore_is_datastore_module($module)) {

        // Send event trigger to module to get items that are private
        $_ids = jrCore_trigger_event('jrComment', 'private_item_ids', array(), $_post, $module);
        if ($_ids && is_array($_ids) && isset($_ids[$module])) {

            // We have some items that are private - we need to get any comments
            // that are made on these items and save the item_id and profile_id
            $_pid = array();
            $_ids = array_chunk($_ids[$module], 1000, true);
            foreach ($_ids as $_ch) {
                $_sp = array(
                    'search'         => array(
                        'comment_item_id in ' . implode(',', $_ch),
                        "comment_module = {$module}"
                    ),
                    'return_keys'    => array('_item_id', '_profile_id'),
                    'skip_triggers'  => true,
                    'privacy_check'  => false,
                    'ignore_pending' => true,
                    'quota_check'    => false,
                    'order_by'       => false,
                    'limit'          => 500000
                );
                $_sp = jrCore_db_search_items('jrComment', $_sp);
                if ($_sp && is_array($_sp) && isset($_sp['_items'])) {
                    $_in = array();
                    foreach ($_sp['_items'] as $v) {
                        $_pid["{$v['_item_id']}"] = $v['_profile_id'];
                        $_in[]                    = "({$v['_item_id']},{$v['_profile_id']})";
                    }
                    if (count($_in) > 0) {
                        // Save to our private_ids table
                        $tbl = jrCore_db_table_name('jrComment', 'private_id');
                        $req = "INSERT IGNORE INTO {$tbl} (comment_id, profile_id) VALUES " . implode(',', $_in);
                        jrCore_db_query($req);
                    }
                }
            }
            return $_pid;
        }
    }
    return false;
}

/**
 * Create a new Private ID entry for a profile_id / comment_id
 * @param int $profile_id Profile ID
 * @param int $comment_id Comment ID
 * @return mixed
 */
function jrComment_create_private_id_entry($profile_id, $comment_id)
{
    $cid = (int) $comment_id;
    $pid = (int) $profile_id;
    $tbl = jrCore_db_table_name('jrComment', 'private_id');
    $req = "INSERT IGNORE INTO {$tbl} (comment_id, profile_id) VALUES ({$cid},{$pid})";
    return jrCore_db_query($req);
}

/**
 * Return an ordered array of threaded comments
 * @recursive
 * @param $_items array items to order
 * @param $_result array Result array
 * @param $level int Level to start at
 * @param $only_id int Check for this specific ID
 * @return mixed
 */
function jrComment_thread_comments(&$_items, &$_result, $level = 0, $only_id = 0)
{
    if (isset($_items[$level]) && count($_items[$level]) > 0) {
        if ($only_id > 0) {
            if (isset($_items[$level][$only_id])) {
                foreach ($_items[$level][$only_id] as $id) {
                    $_result[] = $id;
                    jrComment_thread_comments($_items, $_result, ($level + 1), $id);
                }
            }
        }
        else {
            foreach ($_items[$level] as $id => $_ids) {
                $_result[] = $id;
                foreach ($_ids as $sid) {
                    jrComment_thread_comments($_items, $_result, ($level + 1), $sid);
                }
            }
        }
    }
    else {
        $level++;
        if ($level > 10) {
            return $_result;
        }
        return false;
    }
    return $_result;
}

/**
 * Returns true/false if the current user has the proper credentials to edit the given item
 * @param $_item array Array of Item information returned from jrCore_db_get_item()
 * @return bool
 */
function jrComment_user_can_edit_item($_item)
{
    // is the admin or the comment writer
    if (jrUser_can_edit_item($_item)) {
        return true;
    }

    // the comment module details exist
    if (!isset($_item['comment_module']) || !$_item['comment_item_id']) {
        return false;
    }

    // comment is on this users stuff.
    $_rt = jrCore_db_get_item($_item['comment_module'], $_item['comment_item_id'], true, true);
    if (jrUser_can_edit_item($_rt)) {
        return true;
    }

    return false;
}

//---------------------
// SMARTY
//---------------------

/**
 * Smarty function to show an embedded comment form
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrComment_form($params, $smarty)
{
    global $_conf;
    // Is jrComment module enabled?
    if (!jrCore_module_is_active('jrComment')) {
        return '';
    }
    // Is it allowed in this quota?
    if (!jrProfile_is_allowed_by_quota('jrComment', $smarty)) {
        return '';
    }
    // Check the incoming parameters
    if ($params['module'] == 'jrProfile') {
        $params['profile_id'] = $params['item_id'];
    }
    if (!jrCore_checktype($params['profile_id'], 'number_nz')) {
        return jrCore_smarty_invalid_error('profile_id');
    }
    if (!jrCore_checktype($params['item_id'], 'number_nz')) {
        return jrCore_smarty_invalid_error('item_id');
    }
    if (!jrCore_module_is_active($params['module'])) {
        return jrCore_smarty_invalid_error('module');
    }
    if (isset($params['template']) && $params['template'] != '' && $params['tpl_dir']) {
        //allow other modules to set the tpl_dir.
    }
    elseif (isset($params['template']) && $params['template'] != '') {
        $params['tpl_dir'] = $_conf['jrCore_active_skin'];
    }
    else {
        $params['template'] = "comment_form.tpl";
        $params['tpl_dir']  = 'jrComment';
    }
    if (!isset($params['style']) || strlen($params['style']) === 0) {
        $params['style'] = '';
    }
    if (!isset($params['class']) || strlen($params['class']) === 0) {
        $params['class'] = '';
    }
    $_tmp = array();
    foreach ($params as $k => $v) {
        $_tmp['jrComment'][$k] = $v;
    }
    $_tmp['jrComment']['unique_id'] = "{$params['module']}_{$params['profile_id']}_{$params['item_id']}";
    // Check for order_by
    if (!isset($_tmp['jrComment']['comment_order_by'])) {
        $_tmp['jrComment']['comment_order_by'] = (isset($_conf['jrComment_direction'])) ? $_conf['jrComment_direction'] : 'numerical_desc';
    }
    $_tmp['jrComment']['pagebreak'] = (isset($params['pagebreak']) && jrCore_checktype($params['pagebreak'], 'number_nz')) ? intval($params['pagebreak']) : $_conf['jrComment_pagebreak'];
    // Call the appropriate template and return
    $out = jrCore_parse_template($params['template'], $_tmp, $params['tpl_dir']);
    if (isset($params['assign']) && $params['assign'] != '') {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}
