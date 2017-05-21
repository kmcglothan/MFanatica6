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

/**
 * meta
 */
function jrAction_meta()
{
    $_tmp = array(
        'name'        => 'Timeline',
        'url'         => 'timeline',
        'version'     => '2.0.9',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Users can enter updates and log activity to their Timeline',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/1579/timeline',
        'category'    => 'profiles',
        'priority'    => 250, // LOW load priority (we want other listeners to run first)
        'activate'    => true,
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrAction_init()
{
    // Let other modules affect actions
    jrCore_register_event_trigger('jrAction', 'action_save', 'Fired before action is saved to override');
    jrCore_register_event_trigger('jrAction', 'action_stats', 'Fired when the {jrAction_stats} function is called from the templates.');
    jrCore_register_event_trigger('jrAction', 'action_data', 'Fired when getting data for an action entry');

    // register our custom JS/CSS
    jrCore_register_module_feature('jrCore', 'javascript', 'jrAction', 'char_count.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrAction', 'jrAction.js');
    jrCore_register_module_feature('jrCore', 'css', 'jrAction', 'jrAction.css');

    // Core options
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrAction', 'on');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrAction', true);
    jrCore_register_module_feature('jrCore', 'action_support', 'jrAction', 'create', 'item_action_detail.tpl');

    // Quick Share Tabs
    $_tm = array(
        'title' => 3,
        'icon'  => 'pen'
    );
    jrCore_register_module_feature('jrAction', 'quick_share', 'jrAction', 'jrAction_quick_share_status_update', $_tm);

    // Pulse Key support
    jrCore_register_module_feature('jrProfile', 'pulse_key', 'jrAction', 'profile_jrAction_mention_item_count', 'mentions');
    jrCore_register_module_feature('jrProfile', 'pulse_key', 'jrAction', 'profile_jrAction_shared_item_count', 'shares');

    // Add additional search params
    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrAction_db_search_params_listener');
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrAction_db_search_items_listener');

    jrCore_register_event_listener('jrCore', 'db_get_item', 'jrAction_db_get_item_listener');
    jrCore_register_event_listener('jrCore', 'db_delete_item', 'jrAction_db_delete_item_listener');
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrAction_daily_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'exclude_item_index_buttons', 'jrAction_exclude_item_index_buttons_listener');
    jrCore_register_event_listener('jrCore', 'form_display', 'jrAction_form_display_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrAction_verify_module_listener');

    // We want RSS feeds
    jrCore_register_module_feature('jrFeed', 'feed_support', 'jrAction', 'enabled');
    // RSS Feed
    jrCore_register_event_listener('jrFeed', 'create_rss_feed', 'jrAction_create_rss_feed_listener');

    // System reset listener
    jrCore_register_event_listener('jrDeveloper', 'reset_system', 'jrAction_reset_system_listener');

    // notifications
    $_tmp = array(
        'label' => 12, // mentioned in an activity stream
        'help'  => 16  // If your profile name is mentioned in an Activity Stream do you want to be notified?
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrAction', 'mention', $_tmp);

    $_tmp = array(
        'label' => 38, // item is shared
        'help'  => 39  // If an item of yours is shared do you want to be notified?
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrAction', 'share', $_tmp);

    $_tmp = array(
        'wl'    => 'hash_tags',
        'label' => 'Convert # Tags',
        'help'  => 'If active, hash tags written as #tag will be linked up to a tag search.'
    );
    jrCore_register_module_feature('jrCore', 'format_string', 'jrAction', 'jrAction_format_string_convert_hash_tags', $_tmp);

    // Core item buttons
    $_tmp = array(
        'title'  => 'Timeline RSS Feed Button',
        'icon'   => 'rss',
        'active' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'item_index_button', 'jrAction', 'jrAction_item_index_rss_button', $_tmp);

    $_tmp = array(
        'title'  => 'Timeline Mentions Button',
        'icon'   => 'quote',
        'active' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'item_index_button', 'jrAction', 'jrAction_item_index_mentions_button', $_tmp);

    $_tmp = array(
        'title'  => 'Timeline Search Button',
        'icon'   => 'search2',
        'active' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'item_index_button', 'jrAction', 'jrAction_item_index_search_button', $_tmp);

    // We offer a module detail feature to share to timeline
    $_tmp = array(
        'function' => 'jrAction_share_to_timeline_feature',
        'label'    => 'Share To Timeline',
        'help'     => 'Adds a &quot;Share To Timeline&quot; button to Item Detail pages'
    );
    jrCore_register_module_feature('jrCore', 'item_detail_feature', 'jrAction', 'share_to_timeline', $_tmp);

    // Cleanup old action_original keys
    jrCore_register_queue_worker('jrAction', 'action_cleanup', 'jrAction_action_cleanup_worker', 0, 1, 28800);

    // Cleanup Deleted items worker
    jrCore_register_queue_worker('jrAction', 'deleted_item', 'jrAction_deleted_item_worker', 0, 1, 3600);

    jrCore_register_module_feature('jrTips', 'tip', 'jrAction', 'tip');
    return true;
}

//------------------------------------
// QUEUE WORKER
//------------------------------------

/**
 * Cleanup old "action_original" keys
 * @param $_queue array Queue entry
 * @return bool
 */
function jrAction_action_cleanup_worker($_queue)
{
    while (true) {
        $_sp = array(
            'search'        => array(
                'action_original_created > 0'
            ),
            'skip_triggers' => true,
            'privacy_check' => false,
            'quota_check'   => false,
            'limit'         => 250
        );
        $_sp = jrCore_db_search_items('jrAction', $_sp);
        if ($_sp && is_array($_sp) && isset($_sp['_items'])) {
            $_ac = array();
            foreach ($_sp['_items'] as $k => $v) {
                if (isset($v['action_original_item_id'])) {
                    $_ac[] = (int) $v['action_original_item_id'];
                }
            }
            if (count($_ac) > 0) {
                $_up = array();
                $_ac = jrCore_db_get_multiple_items('jrAction', $_ac);
                if ($_ac && is_array($_ac)) {
                    $_tm = array();
                    foreach ($_ac as $_action) {
                        $iid       = (int) $_action['_item_id'];
                        $_tm[$iid] = $_action;
                    }
                    unset($_ac);
                }
                foreach ($_sp['_items'] as $k => $v) {
                    if (isset($v['action_original_item_id'])) {
                        $iid = (int) $v['action_original_item_id'];
                        if (isset($_tm[$iid])) {
                            $uid       = (int) $v['_item_id'];
                            $_up[$uid] = array('action_original_item' => "jrAction:{$uid}:create");
                        }
                    }
                }
                if (count($_up) > 0) {

                    // Update
                    jrCore_db_update_multiple_items('jrAction', $_up);

                    // And remove keys
                    $_dl = array_keys($_up);
                    jrCore_db_delete_key_from_multiple_items('jrAction', $_dl, 'action_original_module');
                    jrCore_db_delete_key_from_multiple_items('jrAction', $_dl, 'action_original_created');
                    jrCore_db_delete_key_from_multiple_items('jrAction', $_dl, 'action_original_item_id');
                    jrCore_db_delete_key_from_multiple_items('jrAction', $_dl, 'action_original_user_id');
                    jrCore_db_delete_key_from_multiple_items('jrAction', $_dl, 'action_original_profile_name');
                    jrCore_db_delete_key_from_multiple_items('jrAction', $_dl, 'action_original_profile_url');
                    jrCore_db_delete_key_from_multiple_items('jrAction', $_dl, 'action_original_profile_id');
                    jrCore_db_delete_key_from_multiple_items('jrAction', $_dl, 'action_original_shared_by');
                }
            }
            if (count($_sp['_items']) < 250) {
                break;
            }
        }
        else {
            break;
        }
    }
    return true;
}

/**
 * Cleanup action_original_keys that are no longer valid
 * @param $_queue array Queue entry
 * @return bool
 */
function jrAction_deleted_item_worker($_queue)
{
    $_rt = array(
        'search'              => array(
            "action_original_item like {$_queue['module']}:{$_queue['_item_id']}:%"
        ),
        'return_item_id_only' => true,
        'skip_triggers'       => true,
        'privacy_check'       => false,
        'quota_check'         => false,
        'limit'               => 10000
    );
    $_rt = jrCore_db_search_items('jrAction', $_rt);
    if ($_rt && is_array($_rt)) {
        jrCore_db_delete_multiple_items('jrAction', $_rt);
    }
    return true;
}

//------------------------------------
// ITEM DETAIL FEATURE
//------------------------------------

/**
 * Create a "Share This with your Followers" button
 * @param string $module Module item belongs to
 * @param array $_item Item info (from DS)
 * @param array $params Smarty function parameters
 * @param array $smarty current Smarty object
 * @return string
 */
function jrAction_share_to_timeline_feature($module, $_item, $params, $smarty)
{
    global $_conf, $_user;
    if (jrUser_is_logged_in()) {
        // Have we already shared this?
        $params['show_item_has_been_shared'] = false;
        if ($aid = jrAction_get_user_share_id($params['module'], $params['item']['_item_id'], $_user['_user_id'])) {

            // User has shared this entry
            $params['show_item_has_been_shared'] = true;

            // Create URL to view it
            $murl               = jrCore_get_module_url('jrAction');
            $purl               = jrUser_get_profile_home_key('profile_url');
            $params['view_url'] = "{$_conf['jrCore_base_url']}/{$purl}/{$murl}/{$aid}";
        }
        return jrCore_parse_template('item_detail_feature.tpl', $params, 'jrAction');
    }
    return '';
}

//------------------------------------
// QUICK SHARE
//------------------------------------

/**
 * Save a new status update
 * @param $_post array Posted info
 * @param $_user array User info
 * @param $_conf array Global Config
 * @return mixed
 */
function jrAction_quick_share_status_update_save($_post, $_user, $_conf)
{
    $pid = jrUser_get_profile_home_key('_profile_id');
    $_rt = array(
        'action_text'   => $_post['action_text'],
        'action_module' => 'jrAction'
    );
    $_cr = array(
        '_profile_id' => $pid
    );
    $aid = jrCore_db_create_item('jrAction', $_rt, $_cr);
    if (!$aid) {
        $_rs = array('error' => 'unable to create new activity entry - please try again');
        jrCore_json_response($_rs);
    }
    // Send out our Action Created trigger
    $_args = array(
        '_item_id'    => $aid,
        '_user_id'    => $_user['_user_id'],
        '_profile_id' => $pid
    );
    jrCore_trigger_event('jrAction', 'create', $_rt, $_args);

    // Notify any users if we mention them...
    jrAction_process_mentions($_post['action_text'], $aid);

    // Save hashes
    jrAction_save_hash_tags($_post['action_text']);

    jrProfile_reset_cache($pid);
    return true;
}

//----------------------
// ITEM BUTTONS
//----------------------

/**
 * Return RSS Button for Activity Timeline
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return mixed
 */
function jrAction_item_index_rss_button($module, $_item, $_args, $smarty, $test_only = false)
{
    global $_conf, $_post;
    if ($module == 'jrAction') {
        if (jrCore_module_is_active('jrFeed')) {
            if ($test_only) {
                return true;
            }
            $furl = jrCore_get_module_url('jrFeed');
            $murl = jrCore_get_module_url('jrAction');
            $_ln  = jrUser_load_lang_strings();
            $_rt  = array(
                'url'  => "{$_conf['jrCore_base_url']}/{$furl}/{$murl}/{$_post['module_url']}",
                'icon' => 'rss',
                'alt'  => $_ln['jrAction'][31]
            );
            return $_rt;
        }
    }
    return false;
}

/**
 * Return Button for "Mentions" section in Activity Timeline
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return mixed
 */
function jrAction_item_index_mentions_button($module, $_item, $_args, $smarty, $test_only = false)
{
    global $_conf, $_post;
    if ($module == 'jrAction') {
        if ($test_only) {
            return true;
        }
        if (isset($_post['_profile_id']) && jrProfile_is_profile_owner($_post['_profile_id'])) {
            $murl = jrCore_get_module_url('jrAction');
            $_ln  = jrUser_load_lang_strings();
            $_rt  = array(
                'url'  => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/{$murl}/mentions",
                'icon' => 'at',
                'alt'  => $_ln['jrAction'][7]
            );
            return $_rt;
        }
    }
    return false;
}

/**
 * Return Button for "Search" section in Activity Timeline
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return mixed
 */
function jrAction_item_index_search_button($module, $_item, $_args, $smarty, $test_only = false)
{
    global $_post;
    if ($module == 'jrAction') {
        if ($test_only) {
            return true;
        }
        if (isset($_post['_profile_id']) && jrProfile_is_profile_owner($_post['_profile_id'])) {
            $_ln = jrUser_load_lang_strings();
            $_rt = array(
                'url'     => '#',
                'onclick' => "$('#action_search').slideToggle(300);return false",
                'icon'    => 'search2',
                'alt'     => $_ln['jrAction'][8]
            );
            return $_rt;
        }
    }
    return false;
}

//----------------------
// STRING FORMATTER
//----------------------

/**
 * Registered core string formatter - Convert # tags
 * @param string $string String to format
 * @param int $quota_id Quota ID for Profile ID
 * @return string
 */
function jrAction_format_string_convert_hash_tags($string, $quota_id = 0)
{
    if (!jrUser_is_logged_in() || !strpos(' ' . $string, '#')) {
        return $string;
    }
    $string = ' ' . $string;
    // We don't want to mess with any embedded Javascript or CSS
    if (stripos($string, '<script')) {
        $out = '';
        $_sv = array();
        foreach (explode('<script', $string) as $k => $part) {
            if (stripos($part, '</script>')) {
                // We have found the actual code portion
                list($beg, $end) = explode('</script>', $part, 2);
                $_sv[$k] = "<script{$beg}</script>";
                $out .= "~~!~~{$k}~~!~~{$end}";
            }
            else {
                $out .= $part;
            }
        }
        $string = $out;
    }
    $string = preg_replace_callback('/(^#| #)([_a-z0-9\-]+)/i',
        function ($_m) {
            global $_conf;
            if (strlen($_m[2]) === 6 && jrCore_checktype($_m[2], 'hex')) {
                // This is a hex color code - don't replace
                return $_m[0];
            }
            $url = jrCore_get_module_url('jrAction');
            return ' <a href="' . $_conf['jrCore_base_url'] . '/' . $url . '/ss=%23' . $_m[2] . '"><span class="hash_link">' . $_m[0] . '</span></a>';
        },
        $string);

    // If we plucked any JS out earlier, stick it back in
    if (isset($_sv) && is_array($_sv) && count($_sv) > 0) {
        foreach ($_sv as $k => $v) {
            $string = str_replace("~~!~~{$k}~~!~~", $v, $string);
        }
    }
    return substr($string, 1);
}

//----------------------
// EVENT LISTENERS
//----------------------

/**
 * System Reset listener
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrAction_reset_system_listener($_data, $_user, $_conf, $_args, $event)
{
    $tbl = jrCore_db_table_name('jrAction', 'hash');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");
    $tbl = jrCore_db_table_name('jrAction', 'share');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");
    return $_data;
}

/**
 * Exclude buttons from our item index
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrAction_exclude_item_index_buttons_listener($_data, $_user, $_conf, $_args, $event)
{
    // We exclude the CREATE button - we have our own form
    $_data['jrCore_item_create_button'] = true;
    return $_data;
}

/**
 * Prune old System generated Time line entries
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrAction_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_conf['jrAction_prune']) && jrCore_checktype($_conf['jrAction_prune'], 'number_nz')) {
        // We are pruning old entries
        $tot = 0;
        $num = 0;
        $log = false;
        $old = (time() - ($_conf['jrAction_prune'] * 86400));
        while (true) {
            $_sc = array(
                'search'              => array(
                    "_created < {$old}",
                    'action_mode like %',
                ),
                'no_cache'            => true,
                'limit'               => 1000,
                'privacy_check'       => false,
                'skip_triggers'       => true,
                'return_item_id_only' => true
            );
            $_sc = jrCore_db_search_items('jrAction', $_sc);
            if ($_sc && is_array($_sc) && count($_sc) > 0) {
                if (!$log) {
                    // Show message on FIRST pruned set
                    jrCore_logger('INF', "pruning timeline entries older than {$_conf['jrAction_prune']} days");
                    $log = true;
                }
                jrCore_db_delete_multiple_items('jrAction', $_sc, false, false);
                $tot += count($_sc);
            }
            else {
                break;
            }
            $num++;
            if ($num > 99) {
                // fail safe - max 100,000 at a time
                break;
            }
        }
        if ($tot > 0) {
            jrCore_logger('INF', "deleted " . jrCore_number_format($tot) . " system timeline entries older than {$_conf['jrAction_prune']} days");
        }
    }
    return $_data;
}

/**
 * Cleanup any bad action events with empty data
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrAction_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    @ini_set('memory_limit', '512M');

    // Cleanup old "action_original" keys
    jrCore_queue_create('jrAction', 'action_cleanup', array('cleanup' => true), 0, null, 1);

    // Fix up Likes
    if (jrCore_module_is_active('jrLike') && jrCore_db_number_rows('jrLike', 'likes') > 0) {

        $tot = 0;
        $del = 0;
        $tmp = false;
        while (true) {
            $_rt = array(
                'search'         => array(
                    'action_module = jrLike',
                    'action_original_item not_like %:%'
                ),
                'return_keys'    => array('_item_id', 'action_item_id'),
                'order_by'       => false,
                'skip_triggers'  => true,
                'privacy_check'  => false,
                'ignore_pending' => true,
                'no_cache'       => true,
                'limit'          => 5000
            );
            $_rt = jrCore_db_search_items('jrAction', $_rt);
            if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

                if (!$tmp) {
                    jrCore_form_modal_notice('update', 'applying changes: converting like action entries to new format - please be patient');
                    $tmp = true;
                }

                $_dl = array();
                $_id = array();
                foreach ($_rt['_items'] as $k => $v) {
                    $_id[] = (int) $v['action_item_id'];
                }

                // Get liked items
                $tbl = jrCore_db_table_name('jrLike', 'likes');
                $req = "SELECT like_id, like_module, like_item_id FROM {$tbl} WHERE like_id IN(" . implode(',', $_id) . ')';
                $_lk = jrCore_db_query($req, 'like_id');
                if ($_lk && is_array($_lk) && count($_lk) > 0) {

                    $_up = array();
                    foreach ($_rt['_items'] as $k => $v) {
                        if (isset($_lk["{$v['action_item_id']}"])) {
                            $mod                     = $_lk["{$v['action_item_id']}"]['like_module'];
                            $iid                     = $_lk["{$v['action_item_id']}"]['like_item_id'];
                            $_up["{$v['_item_id']}"] = array('action_original_item' => "{$mod}:{$iid}:create");
                        }
                        else {
                            // Bad entry - remove
                            $_dl[] = (int) $v['_item_id'];
                        }
                    }
                    $num = count($_up);
                    if ($num > 0) {
                        $tot += $num;
                        jrCore_db_update_multiple_items('jrAction', $_up);
                        jrCore_form_modal_notice('update', 'applying changes: ' . jrCore_number_format($tot) . ' like action entries converted to correct format');
                    }
                }
                else {
                    // These items do NOT exist in the like table - remove
                    foreach ($_rt['_items'] as $k => $v) {
                        $_dl[] = (int) $v['_item_id'];
                    }
                }
                if (count($_dl) > 0) {
                    jrCore_db_delete_multiple_items('jrAction', $_dl);
                    $del += count($_dl);
                }
                if (count($_rt['_items']) < 5000) {
                    // No more items
                    break;
                }
            }
            else {
                // No more items
                if ($tot > 0) {
                    jrCore_form_modal_notice('update', 'applying changes: ' . jrCore_number_format($tot) . ' like action entries converted to correct format');
                }
                break;
            }
        }
    }

    // Fix up Followers
    if (jrCore_module_is_active('jrFollower') && jrCore_db_get_datastore_item_count('jrFollower') > 0) {
        $tot = 0;
        $del = 0;
        $tmp = false;
        while (true) {
            $_rt = array(
                'search'         => array(
                    'action_module = jrFollower',
                    'action_original_item not_like %:%'
                ),
                'return_keys'    => array('_item_id', 'action_item_id'),
                'order_by'       => false,
                'skip_triggers'  => true,
                'privacy_check'  => false,
                'ignore_pending' => true,
                'no_cache'       => true,
                'limit'          => 5000
            );
            $_rt = jrCore_db_search_items('jrAction', $_rt);
            if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

                if (!$tmp) {
                    jrCore_form_modal_notice('update', 'applying changes: converting follower action entries to new format - please be patient');
                    $tmp = true;
                }

                $_dl = array();
                $_id = array();
                foreach ($_rt['_items'] as $k => $v) {
                    $_id[] = (int) $v['action_item_id'];
                }

                $_fi = jrCore_db_get_multiple_items('jrFollower', $_id, array('_item_id', 'follow_profile_id'));
                if ($_fi && is_array($_fi)) {
                    $_pi = array();
                    foreach ($_fi as $_pid) {
                        $iid       = (int) $_pid['_item_id'];
                        $_pi[$iid] = $_pid['follow_profile_id'];
                    }
                    $_up = array();
                    foreach ($_rt['_items'] as $k => $v) {
                        if (isset($_pi["{$v['action_item_id']}"])) {
                            $_up["{$v['_item_id']}"] = array('action_original_item' => "jrProfile:" . $_pi["{$v['action_item_id']}"] . ":create");
                        }
                        else {
                            // Bad entry - remove
                            $_dl[] = (int) $v['_item_id'];
                        }
                    }
                    $num = count($_up);
                    if ($num > 0) {
                        $tot += $num;
                        jrCore_db_update_multiple_items('jrAction', $_up);
                        jrCore_form_modal_notice('update', 'applying changes: ' . jrCore_number_format($tot) . ' follower action entries converted to correct format');
                    }
                    if (count($_dl) > 0) {
                        jrCore_db_delete_multiple_items('jrAction', $_dl);
                        $del += count($_dl);
                    }
                    if (count($_rt['_items']) < 5000) {
                        // No more items
                        break;
                    }
                }
                else {
                    // These items do NOT exist - remove
                    foreach ($_rt['_items'] as $k => $v) {
                        $_dl[] = (int) $v['_item_id'];
                    }
                    jrCore_db_delete_multiple_items('jrAction', $_dl);
                    $del += count($_dl);
                }
            }
            else {
                // No more items
                if ($tot > 0) {
                    jrCore_form_modal_notice('update', 'applying changes: ' . jrCore_number_format($tot) . ' follower action entries converted to correct format');
                }
                break;
            }
        }
    }

    // Fix up Ratings
    if (jrCore_module_is_active('jrRating') && jrCore_db_get_datastore_item_count('jrRating') > 0) {
        $tot = 0;
        $del = 0;
        $tmp = false;
        while (true) {
            $_rt = array(
                'search'         => array(
                    'action_module = jrRating',
                    'action_original_item not_like %:%'
                ),
                'return_keys'    => array('_item_id', 'action_item_id'),
                'order_by'       => false,
                'skip_triggers'  => true,
                'privacy_check'  => false,
                'ignore_pending' => true,
                'no_cache'       => true,
                'limit'          => 5000
            );
            $_rt = jrCore_db_search_items('jrAction', $_rt);
            if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

                if (!$tmp) {
                    jrCore_form_modal_notice('update', 'applying changes: converting rating action entries to new format - please be patient');
                    $tmp = true;
                }

                $_dl = array();
                $_id = array();
                foreach ($_rt['_items'] as $k => $v) {
                    $_id[] = (int) $v['action_item_id'];
                }

                $_fi = jrCore_db_get_multiple_items('jrRating', $_id, array('_item_id', 'rating_module', 'rating_item_id'));
                if ($_fi && is_array($_fi)) {
                    $_pi = array();
                    foreach ($_fi as $_r) {
                        $iid       = (int) $_r['_item_id'];
                        $_pi[$iid] = "{$_r['rating_module']}:{$_r['rating_item_id']}";
                    }
                    $_up = array();
                    foreach ($_rt['_items'] as $k => $v) {
                        if (isset($_pi["{$v['action_item_id']}"])) {
                            $_up["{$v['_item_id']}"] = array('action_original_item' => $_pi["{$v['action_item_id']}"] . ":create");
                        }
                        else {
                            // Bad entry - remove
                            $_dl[] = (int) $v['_item_id'];
                        }
                    }
                    $num = count($_up);
                    if ($num > 0) {
                        $tot += $num;
                        jrCore_db_update_multiple_items('jrAction', $_up);
                        jrCore_form_modal_notice('update', 'applying changes: ' . jrCore_number_format($tot) . ' rating action entries converted to correct format');
                    }
                    if (count($_dl) > 0) {
                        jrCore_db_delete_multiple_items('jrAction', $_dl);
                        $del += count($_dl);
                    }
                    if (count($_rt['_items']) < 5000) {
                        // No more items
                        break;
                    }
                }
                else {
                    // These items do NOT exist - remove
                    foreach ($_rt['_items'] as $k => $v) {
                        $_dl[] = (int) $v['_item_id'];
                    }
                    jrCore_db_delete_multiple_items('jrAction', $_dl);
                    $del += count($_dl);
                }
            }
            else {
                // No more items
                if ($tot > 0) {
                    jrCore_form_modal_notice('update', 'applying changes: ' . jrCore_number_format($tot) . ' rating action entries converted to correct format');
                }
                break;
            }
        }
    }

    // Fix up Comments
    if (jrCore_module_is_active('jrComment') && jrCore_db_get_datastore_item_count('jrComment') > 0) {
        $tot = 0;
        $del = 0;
        $tmp = false;
        while (true) {
            $_rt = array(
                'search'         => array(
                    'action_module = jrComment',
                    'action_original_item not_like %:%'
                ),
                'return_keys'    => array('_item_id', 'action_item_id'),
                'order_by'       => false,
                'skip_triggers'  => true,
                'privacy_check'  => false,
                'ignore_pending' => true,
                'no_cache'       => true,
                'limit'          => 5000
            );
            $_rt = jrCore_db_search_items('jrAction', $_rt);
            if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

                if (!$tmp) {
                    jrCore_form_modal_notice('update', 'applying changes: converting comment action entries to new format - please be patient');
                    $tmp = true;
                }

                $_dl = array();
                $_id = array();
                foreach ($_rt['_items'] as $k => $v) {
                    $_id[] = (int) $v['action_item_id'];
                }

                $_fi = jrCore_db_get_multiple_items('jrComment', $_id, array('_item_id', 'comment_module', 'comment_item_id'));
                if ($_fi && is_array($_fi)) {
                    $_pi = array();
                    foreach ($_fi as $_r) {
                        $iid       = (int) $_r['_item_id'];
                        $_pi[$iid] = "{$_r['comment_module']}:{$_r['comment_item_id']}";
                    }
                    $_up = array();
                    foreach ($_rt['_items'] as $k => $v) {
                        if (isset($_pi["{$v['action_item_id']}"])) {
                            $_up["{$v['_item_id']}"] = array('action_original_item' => $_pi["{$v['action_item_id']}"] . ":create");
                        }
                        else {
                            // Bad entry - remove
                            $_dl[] = (int) $v['_item_id'];
                        }
                    }
                    $num = count($_up);
                    if ($num > 0) {
                        $tot += $num;
                        jrCore_db_update_multiple_items('jrAction', $_up);
                        jrCore_form_modal_notice('update', 'applying changes: ' . jrCore_number_format($tot) . ' comment action entries converted to correct format');
                    }
                    if (count($_dl) > 0) {
                        jrCore_db_delete_multiple_items('jrAction', $_dl);
                        $del += count($_dl);
                    }
                    if (count($_rt['_items']) < 5000) {
                        // No more items
                        break;
                    }
                }
                else {
                    // These items do NOT exist - remove
                    foreach ($_rt['_items'] as $k => $v) {
                        $_dl[] = (int) $v['_item_id'];
                    }
                    jrCore_db_delete_multiple_items('jrAction', $_dl);
                    $del += count($_dl);
                }
            }
            else {
                // No more items
                if ($tot > 0) {
                    jrCore_form_modal_notice('update', 'applying changes: ' . jrCore_number_format($tot) . ' comment action entries converted to correct format');
                }
                break;
            }
        }
    }

    // Remove data keys - no longer used
    jrCore_db_delete_key_from_all_items('jrAction', 'action_data');
    jrCore_db_delete_key_from_all_items('jrAction', 'action_original_data');

    return $_data;
}

/**
 * Save an Action to the Time line
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrAction_form_display_listener($_data, $_user, $_conf, $_args, $event)
{
    // See if this module supports actions
    if (isset($_user['quota_jrAction_allowed']) && $_user['quota_jrAction_allowed'] == 'on') {

        list($mod, $view) = explode('/', $_data['form_view']);
        $_as = jrCore_get_registered_module_features('jrCore', 'action_support');
        if ($_as && isset($_as[$mod][$view]) && jrCore_is_profile_referrer(false)) {

            // Looks like this view is setup for Actions.
            // By default we do NOT record an action unless the user is posting something on a profile they own.
            // However, some modules may want a say in this - they can set the allowed_off_profile flag
            $allowed = false;
            if (is_array($_as[$mod][$view]) && isset($_as[$mod][$view]['allowed_off_profile']) && $_as[$mod][$view]['allowed_off_profile'] == true) {
                $allowed = true;
            }
            else {
                // Is this user creating an item on a profile they OWN?
                $_up = jrProfile_get_user_linked_profiles($_user['_user_id']);
                if ($_up && is_array($_up) && isset($_up["{$_user['user_active_profile_id']}"])) {
                    $allowed = true;
                }
            }
            if ($allowed) {
                if (!isset($_user['quota_jrAction_show_add']) || $_user['quota_jrAction_show_add'] == 'on') {
                    $_lng = jrUser_load_lang_strings();
                    $_tmp = array(
                        'name'          => "jraction_add_to_timeline",
                        'label'         => $_lng['jrAction'][13],
                        'help'          => $_lng['jrAction'][14],
                        'type'          => 'checkbox',
                        'required'      => false,
                        'form_designer' => false
                    );
                    if (strpos(' ' . $view, 'create')) {
                        $_tmp['default'] = 'on';
                    }
                    else {
                        $_tmp['default'] = 'off';
                    }
                    jrCore_form_field_create($_tmp);
                }
                else {
                    $_tmp = array(
                        'name'  => "jraction_add_to_timeline",
                        'type'  => 'hidden',
                        'value' => 'on'
                    );
                    jrCore_form_field_create($_tmp);
                }
            }
        }
    }
    return $_data;
}

/**
 * jrAction_create_rss_feed_listener
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrAction_create_rss_feed_listener($_data, $_user, $_conf, $_args, $event)
{
    // Format latest actions
    if (isset($_args['module']) && $_args['module'] == 'jrAction') {
        foreach ($_data as $k => $_itm) {
            // We set "title", "url" and "description"
            $url = jrCore_get_module_url($_itm['action_module']);
            if (isset($_itm['action_text'])) {
                // This is a manually entered time line update
                $_data[$k]['description'] = jrCore_strip_html($_itm['action_text']);
            }
            else {
                // this a time line entry that has been added by a module
                if ($pfx = jrCore_db_get_prefix($_itm['action_module'])) {
                    if (isset($_itm['action_item']["{$pfx}_title"])) {
                        $_data[$k]['title'] = "@{$_itm['profile_name']} - {$_itm['action_item']["{$pfx}_title"]}";
                        if (isset($_itm['action_item']["{$pfx}_title_url"])) {
                            $_data[$k]['url'] = "{$_conf['jrCore_base_url']}/{$_itm['profile_url']}/{$url}/{$_itm['_item_id']}/" . $_itm['action_item']["{$pfx}_title_url"];
                        }
                        else {
                            $_data[$k]['url'] = "{$_conf['jrCore_base_url']}/{$_itm['profile_url']}/{$url}/{$_itm['_item_id']}";
                        }
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * See if we are including active modules in our search
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrAction_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['module']) && $_args['module'] == 'jrAction') {
        // Make sure only registered actions for enabled modules come back
        if (!isset($_conf['jrAction_check_modules']) || $_conf['jrAction_check_modules'] == 'on') {
            $_ram = jrCore_get_registered_module_features('jrCore', 'action_support');
            if ($_ram) {
                if (!isset($_data['search']) || !is_array($_data['search'])) {
                    $_data['search'] = array();
                }
                $_data['search'][] = "action_module in jrAction," . implode(',', array_keys($_ram));
            }
            unset($_ram);
        }
    }
    return $_data;
}

/**
 * Delete action entries when an item is deleted
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrAction_db_delete_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (!isset($_args['module']) || $_args['module'] == 'jrAction' || !jrCore_checktype($_args['_item_id'], 'number_nz')) {
        return $_data;
    }

    if (!isset($_conf['jrAction_delete_with_item']) || $_conf['jrAction_delete_with_item'] != 'on') {
        return $_data;
    }

    // If an item is deleted, and it has been SHARED, we need to remove the
    // original_item key from the shared Timeline entries or it will show an error
    jrCore_queue_create('jrAction', 'deleted_item', $_args);

    // Find actions associated with this item and remove
    $_sr = array(
        'search'              => array(
            "action_item_id = {$_args['_item_id']}",
            "action_module = {$_args['module']}"
        ),
        'return_item_id_only' => true,
        'skip_triggers'       => true,
        'privacy_check'       => false,
        'ignore_pending'      => true,
        'limit'               => 10
    );
    $_rt = jrCore_db_search_items('jrAction', $_sr);
    if ($_rt && is_array($_rt)) {
        jrCore_db_delete_multiple_items('jrAction', $_rt, false);
    }
    return $_data;
}

/**
 * Add additional data in to returned array
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrAction_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrAction' && isset($_data['_items']) && jrCore_is_view_request()) {

        $_aad = array();
        foreach ($_data['_items'] as $k => $_v) {

            // This check is really only needed if $_conf.jrAction_check_modules is OFF
            if (!jrCore_module_is_active($_v['action_module'])) {
                unset($_data['_items'][$k]);
                continue;
            }

            // Action Data and Action Original Data
            if ($_v['action_module'] != 'jrAction' && jrCore_is_datastore_module($_v['action_module'])) {
                if (!isset($_aad["{$_v['action_module']}"])) {
                    $_aad["{$_v['action_module']}"] = array();
                }
                $iid                                  = (int) $_v['action_item_id'];
                $_aad["{$_v['action_module']}"][$iid] = $iid;
            }
            if (isset($_v['action_original_item']{3})) {
                list($mod, $iid,) = explode(':', $_v['action_original_item'], 3);
                if ($mod == 'jrAction' && $iid == $_v['_item_id']) {
                    // Prevent recursive trigger event
                    continue;
                }
                if (jrCore_is_datastore_module($mod)) {
                    if (!isset($_aad[$mod])) {
                        $_aad[$mod] = array();
                    }
                    $_aad[$mod][$iid] = $iid;
                }
            }
        }

        $_itm = array();
        if (count($_aad) > 0) {
            foreach ($_aad as $m => $ids) {
                if (count($ids) > 1) {
                    $_tmp = array(
                        'search'         => array(
                            '_item_id in ' . implode(',', $ids)
                        ),
                        'order_by'       => false,
                        'privacy_check'  => false,
                        'quota_check'    => false,
                        'ignore_pending' => true,
                        'jrAction_list'  => true,
                        'limit'          => count($ids)
                    );
                    $_tmp = jrCore_db_search_items($m, $_tmp);
                    if ($_tmp && is_array($_tmp) && isset($_tmp['_items'])) {
                        $_itm[$m] = array();
                        foreach ($_tmp['_items'] as $k => $_v) {
                            switch ($m) {
                                case 'jrProfile':
                                    $uid = (int) $_v['_profile_id'];
                                    break;
                                case 'jrUser':
                                    $uid = (int) $_v['_user_id'];
                                    break;
                                default:
                                    $uid = (int) $_v['_item_id'];
                                    break;
                            }
                            $_itm[$m][$uid] = $_v;
                        }
                    }
                }
                else {
                    $uid            = reset($ids);
                    $_itm[$m][$uid] = jrCore_db_get_item($m, $uid);
                }
            }
        }

        $_ram = jrCore_get_registered_module_features('jrCore', 'action_support');

        foreach ($_data['_items'] as $k => $_v) {

            // Action Data
            if (isset($_itm["{$_v['action_module']}"]) && isset($_v['action_item_id'])) {
                $_data['_items'][$k]['action_data'] = $_itm["{$_v['action_module']}"]["{$_v['action_item_id']}"];
            }

            // Shared By Info
            $_data['_items'][$k]['action_shared_by_user'] = 0;
            if (isset($_v['action_shared_by']) && strlen($_v['action_shared_by']) > 0) {
                $_ids                                          = explode(',', $_v['action_shared_by']);
                $_data['_items'][$k]['action_shared_by_ids']   = array_flip($_ids);
                $_data['_items'][$k]['action_shared_by_count'] = count($_ids);
                if (isset($_data['_items'][$k]['action_shared_by_ids']["{$_user['_user_id']}"])) {
                    $_data['_items'][$k]['action_shared_by_user'] = 1;
                }
            }
            else {
                $_data['_items'][$k]['action_shared_by_count'] = 0;
            }

            // Additional Title info
            $pfx = jrCore_db_get_prefix($_v['action_module']);
            if ($pfx && isset($_data['_items'][$k]['action_data'])) {

                // Our item URL
                $_data['_items'][$k]['action_item_url'] = "{$_conf['jrCore_base_url']}/{$_data['_items'][$k]['action_data']['profile_url']}/" . jrCore_get_module_url($_v['action_module']) . "/{$_data['_items'][$k]['action_data']['_item_id']}";

                // Check for "album" action
                if (strpos($_v['action_mode'], '_album')) {
                    $_data['_items'][$k]['album_title']     = $_data['_items'][$k]['action_data']["{$pfx}_album"];
                    $_data['_items'][$k]['album_title_url'] = $_data['_items'][$k]['action_data']["{$pfx}_album_url"];
                }
                // Get action URL
                if (isset($_data['_items'][$k]['action_data']["{$pfx}_title"])) {
                    $_data['_items'][$k]['action_title'] = $_data['_items'][$k]['action_data']["{$pfx}_title"];
                }
                if (isset($_data['_items'][$k]['action_data']["{$pfx}_title_url"])) {
                    $_data['_items'][$k]['action_title_url'] = $_data['_items'][$k]['action_data']["{$pfx}_title_url"];
                    $_data['_items'][$k]['action_item_url'] .= '/' . $_data['_items'][$k]['action_data']["{$pfx}_title_url"];
                }

                // Share Count
                $_data['_items'][$k]['action_data']['action_share_count'] = 0;
                if (isset($_data['_items'][$k]['action_data']["{$pfx}_share_count"])) {
                    $_data['_items'][$k]['action_data']['action_share_count'] = (int) $_data['_items'][$k]['action_data']["{$pfx}_share_count"];
                }
            }

            // Process through template
            if (!isset($_v['action_mode'])) {
                $_data['_items'][$k]['action_mode'] = 'create';
                $_v['action_mode']                  = 'create';
            }

            // Action Original Data
            $url = false;
            $mod = false;
            $iid = false;
            if (isset($_v['action_original_item']{3})) {
                list($mod, $iid,) = explode(':', $_v['action_original_item'], 3);
                $iid = intval($iid);
                $url = jrCore_get_module_url($mod);
                if (isset($_itm[$mod][$iid])) {

                    $_data['_items'][$k]['action_original_module']       = $mod;
                    $_data['_items'][$k]['action_original_item_id']      = $iid;
                    $_data['_items'][$k]['action_original_created']      = $_itm[$mod][$iid]['_created'];
                    $_data['_items'][$k]['action_original_user_id']      = $_itm[$mod][$iid]['_user_id'];
                    $_data['_items'][$k]['action_original_profile_name'] = $_itm[$mod][$iid]['profile_name'];
                    $_data['_items'][$k]['action_original_profile_url']  = $_itm[$mod][$iid]['profile_url'];
                    $_data['_items'][$k]['action_original_profile_id']   = $_itm[$mod][$iid]['_profile_id'];
                    $_data['_items'][$k]['action_original_item_url']     = "{$_conf['jrCore_base_url']}/{$_itm[$mod][$iid]['profile_url']}/{$url}/{$iid}";

                    $pfx = jrCore_db_get_prefix($mod);
                    if ($pfx) {
                        // Get action URL
                        if (isset($_itm[$mod][$iid]["{$pfx}_title"])) {
                            $_data['_items'][$k]['action_original_title'] = $_itm[$mod][$iid]["{$pfx}_title"];
                        }
                        if (isset($_itm[$mod][$iid]["{$pfx}_title_url"])) {
                            $_data['_items'][$k]['action_original_title_url'] = $_itm[$mod][$iid]["{$pfx}_title_url"];
                            $_data['_items'][$k]['action_original_item_url'] .= "/" . $_itm[$mod][$iid]["{$pfx}_title_url"];
                        }
                        // Share Count
                        $_itm[$mod][$iid]['action_share_count'] = 0;
                        if (isset($_itm[$mod][$iid]["{$pfx}_share_count"])) {
                            $_itm[$mod][$iid]['action_share_count'] = $_itm[$mod][$iid]["{$pfx}_share_count"];
                        }
                    }
                    $_data['_items'][$k]['action_original_data'] = $_itm[$mod][$iid];
                }
            }

            // Trigger action_data
            $_data['_items'][$k] = jrCore_trigger_event('jrAction', 'action_data', $_data['_items'][$k]);

            if ($mod) {
                if (is_file(APP_DIR . "/modules/{$mod}/templates/item_action_detail.tpl")) {
                    $_tmp                                        = array();
                    $_tmp['item']                                = $_data['_items'][$k]['action_original_data'];
                    $_tmp['item']['action_data']                 = $_data['_items'][$k]['action_data'];
                    $_data['_items'][$k]['action_original_html'] = jrCore_parse_template('item_action_detail.tpl', $_tmp, $mod);
                }
                elseif (isset($_data['_items'][$k]['action_original_title'])) {
                    $_data['_items'][$k]['action_original_html'] = "{$_conf['jrCore_base_url']}/{$_itm[$mod][$iid]['profile_url']}/{$url}/{$iid}/{$_data['_items'][$k]['action_original_title_url']}";
                    if (jrCore_module_is_active('jrUrlScan') && strpos(' ' . $_data['_items'][$k]['action_original_html'], 'http')) {
                        $_data['_items'][$k]['action_original_html'] = jrUrlScan_replace_urls($_data['_items'][$k]['action_original_html']);
                    }
                }
            }

            // Action Data
            if (isset($_ram["{$_v['action_module']}"]["{$_v['action_mode']}"])) {
                $tpl = $_ram["{$_v['action_module']}"]["{$_v['action_mode']}"];
                if (is_array($tpl)) {
                    $tpl = $tpl['template'];
                }
                $_data['_items'][$k]['action_html'] = jrCore_parse_template($tpl, array('item' => $_data['_items'][$k]), $_v['action_module']);
            }

        }
    }
    return $_data;
}

/**
 * Return action_date JSON decoded
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrAction_db_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if ($_args['module'] == 'jrAction' && is_array($_data) && jrCore_is_view_request()) {

        // Avoid a recursive trigger...
        if (isset($_data['action_module']) && $_data['action_module'] == 'jrAction' && isset($_data['action_original_item']) && strpos($_data['action_original_item'], "jrAction:{$_data['_item_id']}:") === 0) {
            // This is an action on the same action...
            return $_data;
        }

        // Action Data
        if (isset($_data['action_module']) && isset($_data['action_item_id']) && jrCore_is_datastore_module($_data['action_module'])) {
            $_data['action_data'] = jrCore_db_get_item($_data['action_module'], $_data['action_item_id']);
        }

        // Shared By Info
        $_data['action_shared_by_user'] = 0;
        if (isset($_data['action_shared_by']) && strlen($_data['action_shared_by']) > 0) {
            $_ids                            = explode(',', $_data['action_shared_by']);
            $_data['action_shared_by_ids']   = array_flip($_ids);
            $_data['action_shared_by_count'] = count($_ids);
            if (isset($_data['action_shared_by_ids']["{$_user['_user_id']}"])) {
                $_data['action_shared_by_user'] = 1;
            }
            // If we are on the item detail page for this item, get info about who shared
            $url = jrCore_get_module_url('jrAction');
            if (isset($_post['option']) && $_post['option'] == $url && isset($_post['_1']) && jrCore_checktype($_post['_1'], 'number_nz')) {
                $_rt = array(
                    'search'                 => array(
                        '_user_id in ' . implode(',', $_ids)
                    ),
                    'include_jrProfile_keys' => true,
                    'ignore_pending'         => true,
                    'limit'                  => 100
                );
                $_rt = jrCore_db_search_items('jrUser', $_rt);
                if ($_rt && is_array($_rt) && is_array($_rt['_items'])) {
                    $_data['action_shared_by_user_info'] = $_rt['_items'];
                }
                unset($_rt);
            }
        }
        else {
            $_data['action_shared_by_count'] = 0;
        }

        // Additional Title info
        $pfx = jrCore_db_get_prefix($_data['action_module']);
        if ($pfx && isset($_data['action_data'])) {
            // Check for "album" action
            if (strpos($_data['action_mode'], '_album')) {
                $_data['album_title']     = $_data['action_data']["{$pfx}_album"];
                $_data['album_title_url'] = $_data['action_data']["{$pfx}_album_url"];
            }
            // Get action URL
            if (isset($_data['action_data']["{$pfx}_title"])) {
                $_data['action_title'] = $_data['action_data']["{$pfx}_title"];
            }
            if (isset($_data['action_data']["{$pfx}_title_url"])) {
                $_data['action_title_url'] = $_data['action_data']["{$pfx}_title_url"];
            }
        }

        // Process through template
        if (!isset($_data['action_mode'])) {
            $_data['action_mode'] = 'create';
        }

        // Action Original Data
        $_itm = false;
        $mod  = false;
        $iid  = false;
        if (isset($_data['action_original_item']{3})) {
            list($mod, $iid,) = explode(':', $_data['action_original_item'], 3);
            if (jrCore_is_datastore_module($mod)) {
                $_itm = jrCore_db_get_item($mod, $iid);
                if ($_itm && is_array($_itm)) {
                    $_data['action_original_module']       = $mod;
                    $_data['action_original_item_id']      = $iid;
                    $_data['action_original_created']      = $_itm['_created'];
                    $_data['action_original_user_id']      = $_itm['_user_id'];
                    $_data['action_original_profile_name'] = $_itm['profile_name'];
                    $_data['action_original_profile_url']  = $_itm['profile_url'];
                    $_data['action_original_profile_id']   = $_itm['_profile_id'];
                    $_data['action_original_item_url']     = "{$_conf['jrCore_base_url']}/{$_itm['profile_url']}/" . jrCore_get_module_url($mod) . "/{$iid}";

                    $pfx = jrCore_db_get_prefix($mod);
                    if ($pfx) {
                        // Get action URL
                        if (isset($_itm["{$pfx}_title"])) {
                            $_data['action_original_title'] = $_itm["{$pfx}_title"];
                        }
                        if (isset($_itm["{$pfx}_title_url"])) {
                            $_data['action_original_title_url'] = $_itm["{$pfx}_title_url"];
                            $_data['action_original_item_url'] .= '/' . $_itm["{$pfx}_title_url"];
                        }
                    }
                    $_data['action_original_data'] = $_itm;
                }
            }
        }

        // Trigger action_data
        $_data = jrCore_trigger_event('jrAction', 'action_data', $_data);

        if ($mod) {
            if (is_file(APP_DIR . "/modules/{$mod}/templates/item_action_detail.tpl")) {
                $_tmp                          = array();
                $_tmp['item']                  = $_data['action_original_data'];
                $_tmp['item']['action_data']   = $_data['action_data'];
                $_data['action_original_html'] = jrCore_parse_template('item_action_detail.tpl', $_tmp, $mod);
            }
            elseif (is_array($_itm) && isset($_data['action_original_title'])) {
                $ourl                          = jrCore_get_module_url($mod);
                $_data['action_original_html'] = "{$_conf['jrCore_base_url']}/{$_itm['profile_url']}/{$ourl}/{$iid}/{$_data['action_original_title_url']}";
                if (jrCore_module_is_active('jrUrlScan') && strpos(' ' . $_data['action_original_html'], 'http')) {
                    $_data['action_original_html'] = jrUrlScan_replace_urls($_data['action_original_html']);
                }
            }
        }

        // Action Data
        $_ram = jrCore_get_registered_module_features('jrCore', 'action_support');
        if (isset($_ram["{$_data['action_module']}"]["{$_data['action_mode']}"])) {
            $tpl = $_ram["{$_data['action_module']}"]["{$_data['action_mode']}"];
            if (is_array($tpl)) {
                $tpl = $tpl['template'];
            }
            $_data['action_html'] = jrCore_parse_template($tpl, array('item' => $_data), $_data['action_module']);
        }

    }
    return $_data;
}

//----------------------
// FUNCTIONS
//----------------------

/**
 * Return TRUE if a user_id has shared an item
 * @param $module string Module
 * @param $item_id int
 * @param $user_id int
 * @return bool
 */
function jrAction_get_user_share_id($module, $item_id, $user_id)
{
    global $_mods;
    if (isset($_mods[$module])) {
        $iid = (int) $item_id;
        $uid = (int) $user_id;
        $tbl = jrCore_db_table_name('jrAction', 'share');
        $req = "SELECT share_action_id FROM {$tbl} WHERE share_user_id = {$uid} AND share_module = '{$module}' AND share_item_id = {$iid}";
        $_rt = jrCore_db_query($req, 'SINGLE', false, null, false);
        if ($_rt && is_array($_rt)) {
            return $_rt['share_action_id'];
        }
    }
    return false;
}

/**
 * Save a user share to the share table
 * @param $module string Module of shared item
 * @param $item_id int Item_ID of item being shared
 * @param $user_id int User ID sharing
 * @param $action_id int Item_ID of action entry created
 * @return bool
 */
function jrAction_save_user_share($module, $item_id, $user_id, $action_id)
{
    global $_mods;
    if (isset($_mods[$module])) {
        $iid = (int) $item_id;
        $uid = (int) $user_id;
        $aid = (int) $action_id;
        $tbl = jrCore_db_table_name('jrAction', 'share');
        $req = "INSERT IGNORE INTO {$tbl} (share_user_id, share_module, share_item_id, share_time, share_action_id) VALUES ({$uid}, '{$module}', {$iid}, UNIX_TIMESTAMP(), {$aid})";
        $cnt = jrCore_db_query($req, 'COUNT', false, null, false);
        if ($cnt && $cnt > 0) {
            return true;
        }
    }
    return false;
}

/**
 * Get all mentions in a string
 * @param $text string to check for mentions
 * @return bool|mixed
 */
function jrAction_get_all_mentions($text)
{
    if (strpos(' ' . $text, '@')) {
        preg_match_all('/([^A-Za-z0-9\.])(@([_a-z0-9\-]+))/i', "\n{$text}\n", $_tmp);
        if ($_tmp && is_array($_tmp) && isset($_tmp[3])) {
            return $_tmp[3];
        }
    }
    return false;
}

/**
 * Check an Action text for '@' mentions
 * @param $text string Action Text
 * @param $item_id int Item ID for action that was created
 * @return bool
 */
function jrAction_process_mentions($text, $item_id)
{
    global $_user;
    $_tmp = jrAction_get_all_mentions($text);
    if ($_tmp && is_array($_tmp)) {

        // We have mentions - make sure they are good
        $_rt = array(
            'search'         => array(
                'profile_url in ' . implode(',', $_tmp)
            ),
            'return_keys'    => array('_profile_id', 'profile_url'),
            'skip_triggers'  => true,
            'ignore_pending' => true,
            'limit'          => count($_tmp)
        );
        $_rt = jrCore_db_search_items('jrProfile', $_rt);
        if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

            $_up = array();
            $_pr = array();
            foreach ($_rt['_items'] as $_profile) {

                // Don't notify us if we mention ourselves
                if (jrUser_get_profile_home_key('profile_url') == $_profile['profile_url']) {
                    continue;
                }

                // Used below to keep track of mention counts
                $pid                          = (int) $_profile['_profile_id'];
                $_pr[$pid]                    = $pid;
                $_up["action_mention_{$pid}"] = 1;

                // Notifications
                $_owners = jrProfile_get_owner_info($_profile['_profile_id']);
                if ($_owners && is_array($_owners)) {
                    $_rp = array(
                        'action_user' => $_user,
                        'action_url'  => jrCore_get_local_referrer()
                    );
                    list($sub, $msg) = jrCore_parse_email_templates('jrAction', 'mention', $_rp);
                    foreach ($_owners as $_o) {
                        if ($_o['_user_id'] != $_user['_user_id']) {
                            jrUser_notify($_o['_user_id'], 0, 'jrAction', 'mention', $sub, $msg);
                        }
                    }
                }
            }

            // Increment mention counts
            if (count($_pr) > 0) {
                jrCore_db_update_item('jrAction', $item_id, $_up);
                jrCore_db_increment_key('jrProfile', $_pr, 'profile_jrAction_mention_item_count', 1);
            }
        }
    }
    return true;
}

/**
 * Save a new action to the Action DS
 * @param $mode string Mode (create/update/delete/etc)
 * @param $module string Module creating action
 * @param $item_id integer Unique Item ID in module DataStore
 * @param $_data array action_
 * @param $profile_check bool whether to create actions if admin is creating item on another users profile
 * @param $profile_id int By default will use user_active_profile_id - set to alternate
 * @param $feedback_profile_id int If this is a FEEDBACK entry, set to the _profile_id the feedback is for
 * @return bool
 */
function jrAction_save($mode, $module, $item_id, $_data = null, $profile_check = true, $profile_id = 0, $feedback_profile_id = 0)
{
    global $_post, $_user;
    // See if we are turned on for this module
    if (!isset($_user['quota_jrAction_allowed']) || $_user['quota_jrAction_allowed'] != 'on') {
        return true;
    }
    if (isset($_post['jraction_add_to_timeline']) && $_post['jraction_add_to_timeline'] != 'on') {
        return true;
    }
    elseif (isset($_data['jraction_add_to_timeline']) && $_data['jraction_add_to_timeline'] != 'on') {
        return true;
    }
    // Make sure module is active
    if (!jrCore_module_is_active($module)) {
        return true;
    }
    // Make sure we get a valid $item_id...
    if (!jrCore_checktype($item_id, 'number_nz')) {
        return false;
    }
    $pid = (isset($_user['user_active_profile_id'])) ? intval($_user['user_active_profile_id']) : $_user['_profile_id'];
    if (jrCore_checktype($profile_id, 'number_nz')) {
        $pid = (int) $profile_id;
    }

    // If we are an ADMIN USER that is creating something for a profile
    // that is NOT our home profile, we do not record the action.
    $key = jrUser_get_profile_home_key('_profile_id');
    if ($profile_check) {
        if (jrUser_is_admin() && $pid != $key) {
            return true;
        }
    }

    // Store our action...
    $_save = array(
        'action_mode'    => $mode,
        'action_module'  => $module,
        'action_item_id' => (int) $item_id
    );
    if ($feedback_profile_id > 0) {
        $_save['action_feedback'] = (int) $feedback_profile_id;
    }

    // If this is an Item Detail feature creating an action for an item,
    // we need to store the action_original information
    if (isset($_data['action_original_module']) && isset($_data['action_original_item_id'])) {
        $_save['action_original_item'] = "{$_data['action_original_module']}:{$_data['action_original_item_id']}:create";
    }

    // Let other modules cancel our action if needed
    $_save = jrCore_trigger_event('jrAction', 'action_save', $_save, $_post);
    if (isset($_save['jraction_add_to_timeline']) && $_save['jraction_add_to_timeline'] == 'off') {
        // Cancelled by listener
        return true;
    }

    // See if items being created in this module are pending
    if (!jrUser_is_admin()) {
        $_pnd = jrCore_get_registered_module_features('jrCore', 'pending_support');
        if ($_pnd && isset($_pnd[$module]) && isset($_user["quota_{$module}_pending"]) && intval($_user["quota_{$module}_pending"]) > 0) {
            $_save['action_pending']                    = 1;
            $_save['action_pending_linked_item_module'] = $module;
            $_save['action_pending_linked_item_id']     = (int) $item_id;
        }
    }
    $_core = array(
        '_profile_id' => $pid
    );
    if ($aid = jrCore_db_create_item('jrAction', $_save, $_core)) {
        // Send out our Action Created trigger
        $_args = array(
            '_user_id' => $_user['_user_id'],
            '_item_id' => $aid,
        );
        jrCore_trigger_event('jrAction', 'create', $_save, $_args);
        jrProfile_reset_cache($profile_id);
        return $aid;
    }
    return false;
}

/**
 * Get hash tags found in a string
 * @param string $text
 * @param bool $strip_html set to FALSE to not stripe HTML
 * @return bool|mixed
 */
function jrAction_get_hash_tags_from_string($text, $strip_html = true)
{
    if (strpos(' ' . $text, '#')) {
        if ($strip_html) {
            $text = jrCore_strip_html($text);
        }
        preg_match_all('/(^#| #)([_a-z0-9\-]+)/i', $text, $_tmp);
        if ($_tmp && is_array($_tmp) && isset($_tmp[2])) {
            $_out = array();
            foreach ($_tmp[2] as $k => $hash) {
                if (strlen($hash) > 0) {
                    if (strlen($hash) === 6 && jrCore_checktype($hash, 'hex')) {
                        // HTML color code
                        continue;
                    }
                    $_out[] = $hash;
                }
            }
            if (count($_out) > 0) {
                return $_out;
            }
        }
    }
    return false;
}

/**
 * Save any hash tags in timeline text to the hash tag table
 * @param $text string timeline entry text
 * @return int returns number of hashes saved
 */
function jrAction_save_hash_tags($text)
{
    global $_user;
    if ($_tmp = jrAction_get_hash_tags_from_string($text)) {
        $date = strftime('%Y%m%d%H');
        $_ins = array();
        foreach ($_tmp as $hash) {
            if (strlen($hash) < 33) {
                $_ins[] = "('{$_user['user_active_profile_id']}', '" . jrCore_db_escape($hash) . "', '{$date}')";
            }
        }
        if (count($_ins) > 0) {
            $tbl = jrCore_db_table_name('jrAction', 'hash');
            $req = "INSERT IGNORE INTO {$tbl} (hash_profile_id, hash_text, hash_time) VALUES " . implode(',', $_ins);
            $cnt = jrCore_db_query($req, 'COUNT');
            if ($cnt && $cnt > 0) {
                return $cnt;
            }
        }
    }
    return 0;
}

//----------------------
// SMARTY FUNCTIONS
//----------------------

/**
 * Create a Hash list in a template
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrAction_hash_list($params, $smarty)
{
    if (!isset($params['template'])) {
        return jrCore_smarty_missing_error('template');
    }
    if (!isset($params['order_by']{0})) {
        $params['order_by'] = 'count';
    }
    switch (strtolower($params['order_by'])) {
        case 'hash':
        case 'count':
            break;
        default:
            return jrCore_smarty_invalid_error('order_by');
            break;
    }
    if (!isset($params['order_dir']{0})) {
        $params['order_dir'] = 'desc';
    }
    switch (strtolower($params['order_dir'])) {
        case 'asc':
        case 'desc':
            $params['order_dir'] = strtoupper($params['order_dir']);
            break;
        default:
            return jrCore_smarty_invalid_error('order_dir');
            break;
    }
    if (!isset($params['days']) || !jrCore_checktype($params['days'], 'number_nz')) {
        $params['days'] = 7;
    }
    if (!isset($params['limit']) || !jrCore_checktype($params['limit'], 'number_nz')) {
        $params['limit'] = 10;
    }

    $key = md5(json_encode($params));
    if ((!isset($params['no_cache']) || $params['no_cache'] === false) && $out = jrCore_is_cached('jrAction', $key)) {
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], $out);
            return '';
        }
        return $out;
    }

    $old = strftime('%Y%m%d%H', (time() - ($params['days'] * 86400)));
    $tbl = jrCore_db_table_name('jrAction', 'hash');
    $req = "SELECT COUNT(hash_text) AS hash_count, hash_text FROM {$tbl} WHERE hash_time > {$old} GROUP BY hash_text ";
    switch ($params['order_by']) {
        case 'hash':
            $req .= "ORDER BY hash_text {$params['order_dir']} ";
            break;
        case 'count':
            $req .= "ORDER BY hash_count {$params['order_dir']} ";
            break;
    }
    $req .= "LIMIT {$params['limit']}";
    $out = '';
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        $out = jrCore_parse_template($params['template'], array('_items' => $_rt));
    }
    jrCore_add_to_cache('jrAction', $key, $out);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Create an action form in a template
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrAction_form($params, $smarty)
{
    global $_conf, $_mods;
    $out = '';
    if (jrUser_is_logged_in()) {
        $key = jrUser_get_profile_home_key('quota_jrAction_allowed');
        if ($key && $key == 'on') {

            $_rp = array(
                'token'       => jrCore_form_token_create(),
                'version'     => $_mods['jrAction']['module_version'],
                'quick_share' => (!isset($params['quick_share']) || $params['quick_share'] == true) ? 1 : 0,
                'editor'      => (isset($params['editor']) && $params['editor'] == true) ? 1 : 0,
                '_tabs'       => array()
            );

            // Get modules supporting Quick Share
            $_tm = jrCore_get_registered_module_features('jrAction', 'quick_share');
            if ($_tm && is_array($_tm)) {
                $_ln = jrUser_load_lang_strings();
                foreach ($_tm as $mod => $_inf) {
                    if (isset($params['quick_share']) && $params['quick_share'] == false && $mod != 'jrAction') {
                        continue;
                    }
                    $key = jrUser_get_profile_home_key("quota_{$mod}_allowed");
                    if ($key && $key == 'on') {
                        foreach ($_inf as $function => $_config) {
                            if (isset($_config['title']) && jrCore_checktype($_config['title'], 'number_nz')) {
                                $_config['title'] = $_ln[$mod]["{$_config['title']}"];
                            }
                            $_rp['_tabs'][$mod] = array(
                                'icon'     => $_config['icon'],
                                'title'    => $_config['title'],
                                'module'   => $mod,
                                'function' => $function
                            );
                        }
                    }
                }
            }
            $_tm = array('jrAction' => $_rp['_tabs']['jrAction']);
            unset($_rp['_tabs']['jrAction']);
            $_rp['_tabs'] = array_merge($_tm, $_rp['_tabs']);

            if (isset($_conf['jrAction_editor']) && $_conf['jrAction_editor'] == 'on' && (!isset($params['editor']) || $params['editor'] != false)) {
                $tmp                = new stdClass();
                $editor_name        = (isset($params['name'])) ? $params['name'] : 'action_text';
                $_rp['editor_html'] = smarty_function_jrCore_editor_field(array('name' => $editor_name), $tmp);
            }
            $out = jrCore_parse_template('create_entry_form.tpl', $_rp, 'jrAction');
        }
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Convert # tags into links to profiles
 * @param string $text String to convert at tags in
 * @return string
 */
function smarty_modifier_jrAction_convert_hash_tags($text)
{
    return jrAction_format_string_convert_hash_tags($text, 0);
}

/**
 * returns an array of stats for actions 'actions' 'following' 'followers' for a profile id
 * called from the templates like this {jrAction_stats assign="action_stats" profile_id=$_profile_id}
 *
 * Will return an array of stats that can be formatted
 * <ul>
 *    <li>{$action_stats.actions} Tweets</li>
 *    <li>{$action_stats.followers} Following</li>
 *    <li>{$action_stats.following} Followers</li>
 * </ul>
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrAction_stats($params, $smarty)
{
    // Enabled?
    if (!jrCore_module_is_active('jrAction')) {
        return '';
    }
    // Is it allowed in this quota?
    if (!jrProfile_is_allowed_by_quota('jrAction', $smarty)) {
        return '';
    }
    $out = array();
    if (jrCore_checktype($params['profile_id'], 'number_nz')) {
        $out['actions'] = (int) jrCore_db_run_key_function('jrAction', '_profile_id', $params['profile_id'], 'count');
    }

    // Trigger our action_stats event  (jrFollowers adds in 'following' and 'followers')
    $out = jrCore_trigger_event('jrAction', 'action_stats', $out, $params);

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}
