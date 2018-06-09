<?php
/**
 * Jamroom Followers module
 *
 * copyright 2018 The Jamroom Network
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
function jrFollower_meta()
{
    $_tmp = array(
        'name'        => 'Followers',
        'url'         => 'follow',
        'version'     => '1.5.7',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Users can &quot;follow&quot; other users Profiles',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/3600/followers',
        'category'    => 'profiles',
        'requires'    => 'jrCore:6.0.0',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrFollower_init()
{
    // Pulse Key support
    jrCore_register_module_feature('jrProfile', 'pulse_key', 'jrFollower', 'profile_jrFollower_item_count', 'followers');

    // Integrity Check
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrFollower', 'integrity_check', array('Integrity Check', 'Validate and Update follower counts for Profiles'));

    // Register our custom JS
    jrCore_register_module_feature('jrCore', 'javascript', 'jrFollower', 'jrFollower.js');
    jrCore_register_module_feature('jrCore', 'css', 'jrFollower', 'jrFollower.css');

    // Let the core Action System know we are adding actions to followers Support
    jrCore_register_module_feature('jrCore', 'action_support', 'jrFollower', 'create', 'item_action.tpl');

    // follower notifications
    $_tmp = array(
        'label' => 9,  // 'new pending follower'
        'help'  => 23  // 'If you are approving new followers, do you want to be notified when a new follower is waiting to be approved?'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrFollower', 'follower_pending', $_tmp);

    $_tmp = array(
        'label' => 10, // 'new follower'
        'help'  => 24  // 'Do you want to be notified when you get a new follower?'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrFollower', 'new_follower', $_tmp);

    $_tmp = array(
        'label' => 11, // 'follow approved'
        'help'  => 25  // 'Do you want to be notified if your pending follow request for another profile is approved?'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrFollower', 'follow_approved', $_tmp);

    // Skin menu link to 'following'
    $_tmp = array(
        'group' => 'user',
        'label' => 37, // 'Profiles I Follow'
        'url'   => 'following'
    );
    jrCore_register_module_feature('jrCore', 'skin_menu_item', 'jrFollower', 'following', $_tmp);

    // We provide an "include_followed" search param to allow our Action Lists to show out followers
    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrFollower_db_search_params_listener');

    // Add "Share Follows" to User Account
    jrCore_register_event_listener('jrCore', 'form_display', 'jrFollower_form_display_listener');

    // add follower information into the {jrAction_stats} template function
    jrCore_register_event_listener('jrAction', 'action_stats', 'jrFollower_action_stats_listener');

    // Add action on follow approve
    jrCore_register_event_listener('jrAction', 'create', 'jrFollower_action_create_listener');

    // Insert "Followers Require Approval" form field
    jrCore_register_event_listener('jrCore', 'form_display', 'jrFollower_insert_field');

    // Cleanup skin menu item
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrFollower_verify_module_listener');

    jrCore_register_module_feature('jrTips', 'tip', 'jrFollower', 'tip');

    return true;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Adds a "show tips" field to the User Settings
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrFollower_form_display_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_data['form_view'] == 'jrUser/account') {
        $_lng = jrUser_load_lang_strings();
        $_tmp = array(
            'name'          => 'user_jrFollower_share',
            'type'          => 'checkbox',
            'default'       => 'on',
            'validate'      => 'onoff',
            'label'         => $_lng['jrFollower'][38],
            'help'          => $_lng['jrFollower'][39],
            'required'      => false,
            'form_designer' => false // no form designer or we can't turn it off
        );
        jrCore_form_field_create($_tmp);
    }
    return $_data;
}

/**
 * Remove old skin menu item
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrFollower_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    // Bad menu entry
    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $req = "DELETE FROM {$tbl} WHERE menu_module = 'jrFollower'";
    jrCore_db_query($req);

    // Bad form designer entry
    $tbl = jrCore_db_table_name('jrCore', 'form');
    $req = "DELETE FROM {$tbl} WHERE `module` = 'jrProfile' AND `view` = 'settings' AND `name` = 'profile_jrFollower_approve' LIMIT 1";
    jrCore_db_query($req);

    return $_data;
}

/**
 * Insert the "Influences" field into the Profile Settings page
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrFollower_insert_field($_data, $_user, $_conf, $_args, $event)
{
    // Is this the jrProfile/settings form?
    if (isset($_data['form_view']) && $_data['form_view'] == 'jrProfile/settings') {
        $_ln = jrUser_load_lang_strings();
        $_tm = array(
            'name'          => 'profile_jrFollower_approve',
            'label'         => $_ln['jrFollower'][3],
            'help'          => $_ln['jrFollower'][4],
            'sublabel'      => $_ln['jrFollower'][41],
            'type'          => 'checkbox',
            'default'       => 'off',
            'validate'      => 'onoff',
            'required'      => 'on',
            'form_designer' => false
        );
        jrCore_form_field_create($_tm);
    }
    return $_data;
}

/**
 * Updates a created action with proper user and profile info
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrFollower_action_create_listener($_data, $_user, $_conf, $_args, $event)
{
    $_sv = jrCore_get_flag('follower_approved');
    if ($_sv && is_array($_sv) && isset($_args['_item_id'])) {
        $aid = (int) $_args['_item_id'];
        $_cr = array('_user_id' => $_sv['_user_id']);
        unset($_sv['_user_id']);
        jrCore_db_update_item('jrAction', $aid, $_sv, $_cr);
        jrCore_delete_flag('follower_approved');
    }
    return $_data;
}

/**
 * adds the follower stats into the jrAction_stats call that retrieves
 * action information twitter style - i.e. 200 TWEETS | 300 FOLLOWERS | 400 FOLLOWING
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrFollower_action_stats_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['profile_id']) && jrCore_checktype($_args['profile_id'], 'number_nz')) {

        $_data['followers'] = (int) jrCore_db_run_key_function('jrFollower', 'follow_profile_id', $_args['profile_id'], 'count');
        $_data['following'] = 0;

        // Get all profiles users of this profile are following
        $tbl = jrCore_db_table_name('jrProfile', 'profile_link');
        $req = "SELECT `user_id` FROM {$tbl} WHERE profile_id = '{$_args['profile_id']}'";
        $_rt = jrCore_db_query($req, 'user_id');
        if ($_rt && is_array($_rt)) {
            $_data['following'] = (int) jrCore_db_run_key_function('jrFollower', '_user_id', 'IN (' . implode(',', array_keys($_rt)) . ')', 'count');
        }
    }
    return $_data;
}

/**
 * Add support for "include_followed" jrCore_list param for jrAction lists
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrFollower_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    if (!isset($_args['module'])) {
        return $_data;
    }
    switch ($_args['module']) {

        case 'jrAction':

            // include_followed="true"
            if (isset($_data['include_followed']) && ($_data['include_followed'] === true || $_data['include_followed'] == 'true' || $_data['include_followed'] == '1')) {

                // Check for profile_id
                $pid = false;
                if (isset($_data['search']) && is_array($_data['search']) && isset($_data['profile_id'])) {
                    // see if jrProfile has expanded this profile_id - if so remove it
                    foreach ($_data['search'] as $k => $v) {
                        if (strpos(' ' . $v, '_profile_id = ')) {
                            unset($_data['search'][$k]);
                            break;
                        }
                    }
                    $pid = (int) $_data['profile_id'];
                    unset($_data['profile_id']);
                }
                if (!$pid && isset($_data['profile_id'])) {
                    // We did not find it in search..
                    $pid = (int) $_data['profile_id'];
                    unset($_data['profile_id']);
                }
                if ($pid) {
                    // We need to get the profile's users of this profile follow
                    $_us = jrProfile_get_owner_info($pid);
                    if (isset($_us) && is_array($_us)) {
                        $_pr = array($pid);
                        foreach ($_us as $_user) {
                            $_tm = jrFollower_get_profiles_followed($_user['_user_id']);
                            if (isset($_tm) && is_array($_tm)) {
                                $_pr = array_merge($_pr, $_tm);
                            }
                        }
                        if (isset($_pr) && is_array($_pr)) {
                            // Update with new search option
                            $_data['search'][] = "_profile_id in " . implode(',', $_pr);
                        }
                    }
                    // We can turn of quota checking here since "include_followed" is always used on
                    // a profile, and we would not be here if jrAction was not allowed.  This
                    // saves an extra set of _profile_id lookups in our query
                    $_data['quota_check'] = false;
                }
            }
            break;

        case 'jrProfile':
            // followed_by="user_id[,user_id]"
            if (isset($_data['followed_by']) && strlen($_data['followed_by']) > 0) {
                // See if we have an individual user_id or group of user_id's
                $_ui = array();
                if (strpos($_data['followed_by'], ',')) {
                    foreach (explode(',', $_data['followed_by']) as $v) {
                        $v = (int) $v;
                        if (jrCore_checktype($v, 'number_nz')) {
                            $_ui[] = $v;
                        }
                    }
                }
                else {
                    if (jrCore_checktype($_data['followed_by'], 'number_nz')) {
                        $_ui[] = (int) $_data['followed_by'];
                    }
                }
                if (count($_ui) > 0) {
                    $_pr = array();
                    foreach ($_ui as $v) {
                        $_tm = jrFollower_get_profiles_followed($v);
                        if (is_array($_tm)) {
                            foreach ($_tm as $p) {
                                if (!isset($_pr[$p])) {
                                    $_pr[$p] = $p;
                                }
                            }
                        }
                    }
                    if (count($_pr) > 0) {
                        if (!isset($_data['search'])) {
                            $_data['search'] = array();
                        }
                        $_data['search'][] = '_profile_id in ' . implode(',', $_pr);
                    }
                }
            }
            break;

    }
    return $_data;
}

/**
 * Get number of Followers
 * @param array $_conf Global Config
 * @param array $_user User Information
 * @return int Number of unread Private Notes
 */
function jrFollower_pending_count($_conf, $_user)
{
    $pid = jrUser_get_profile_home_key('_profile_id');
    $tmp = jrCore_is_cached('jrFollower', "follower_count_{$pid}");
    if (is_numeric($tmp)) {
        if ($tmp > 0) {
            return $tmp;
        }
        return true;
    }
    $_sc = array(
        'search'         => array(
            "follow_profile_id = {$pid}",
            "follow_active = 0"
        ),
        'limit'          => 250,
        'skip_triggers'  => true,
        'privacy_check'  => false,
        'ignore_pending' => true,
        'return_count'   => true
    );
    $tot = (int) jrCore_db_search_items('jrFollower', $_sc);
    jrCore_add_to_cache('jrFollower', "follower_count_{$pid}", $tot);
    if ($tot > 0) {
        return $tot;
    }
    return true;
}

/**
 * Returns an array of profiles a given user_id follows
 * @param $user_id string User ID
 * @return mixed Array of profile IDs or false if none
 */
function jrFollower_get_profiles_followed($user_id)
{
    $_rt = jrCore_get_flag("jrfollower_get_profiles_followed_{$user_id}");
    if (!$_rt) {
        // DO NOT USE jrCore_db_search_items here!
        $tbl = jrCore_db_table_name('jrFollower', 'item_key');
        $req = "SELECT a.`value` AS i FROM {$tbl} a
                  JOIN {$tbl} b ON (b.`_item_id` = a.`_item_id` AND b.`key` = '_user_id')
                  JOIN {$tbl} c ON (c.`_item_id` = a.`_item_id` AND c.`key` = 'follow_active')
                 WHERE a.`key` = 'follow_profile_id'
                   AND b.`value` = '" . intval($user_id) . "'
                   AND c.`value` = '1'";
        $_rt = jrCore_db_query($req, 'i', false, 'i');
        if ($_rt && is_array($_rt)) {
            jrCore_set_flag("jrfollower_get_profiles_followed_{$user_id}", array_keys($_rt));
        }
        else {
            jrCore_set_flag("jrfollower_get_profiles_followed_{$user_id}", 'no_profiles');
        }
    }
    if ($_rt == 'no_profiles') {
        return false;
    }
    return $_rt;
}

/**
 * Returns an array (user_id => user_name) of users following the given profile_id
 * @param $profile_id
 * @return bool|mixed
 */
function jrFollower_get_users_following($profile_id)
{
    $tb1 = jrCore_db_table_name('jrFollower', 'item_key');
    $tb2 = jrCore_db_table_name('jrUser', 'item_key');
    if (jrCore_db_key_has_index_table('jrUser', 'user_name')) {
        $tb2 = jrCore_db_get_index_table_name('jrUser', 'user_name');
    }
    $req = "SELECT a.`value` AS i, c.`value` as n FROM {$tb1} a
         LEFT JOIN {$tb1} b ON (b.`_item_id` = a.`_item_id` AND b.`key` = 'follow_profile_id')
         LEFT JOIN {$tb2} c ON (c.`_item_id` = a.`_item_id` AND c.`key` = 'user_name')
             WHERE a.`key` = '_user_id' AND b.`value` = '" . intval($profile_id) . "'";
    $_us = jrCore_db_query($req, 'i', false, 'n');
    if ($_us && is_array($_us) && count($_us) > 0) {
        return $_us;
    }
    return false;
}

/**
 * Return follower info if user is a follower
 * @param $user_id string User ID
 * @param $profile_id string Profile ID
 * @return bool
 */
function jrFollower_is_follower($user_id, $profile_id)
{
    // Make sure user is a follower
    $_sc = array(
        'search'                 => array(
            "_user_id = {$user_id}",
            "follow_profile_id = {$profile_id}"
        ),
        'exclude_jrUser_keys'    => true,
        'exclude_jrProfile_keys' => true,
        'privacy_check'          => false,
        'limit'                  => 1
    );
    $_rt = jrCore_db_search_items('jrFollower', $_sc);
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
        return $_rt['_items'][0];
    }
    return false;
}

/**
 * Return the number of profiles a user is following
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrFollower_following_count($params, $smarty)
{
    if (!isset($params['user_id']) || !jrCore_checktype($params['user_id'], 'number_nz')) {
        return 'jrFollower_following_count: user_id required';
    }
    $_sc = array(
        'search'                 => array(
            "_user_id = {$params['user_id']}",
            "follow_active = 1"
        ),
        'return_count'           => true,
        'exclude_jrUser_keys'    => true,
        'exclude_jrProfile_keys' => true,
        'privacy_check'          => false
    );
    $cnt = jrCore_db_search_items('jrFollower', $_sc);
    $num = 0;
    if (isset($cnt) && jrCore_checktype($cnt, 'number_nz')) {
        $num = $cnt;
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $num);
        return '';
    }
    return $num;
}

/**
 * Creates a Follow/Unfollow button for logged in users on a profile
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrFollower_button($params, $smarty)
{
    global $_conf, $_user;
    if (!jrUser_is_logged_in()) {
        return '';
    }
    if (!jrCore_module_is_active('jrFollower')) {
        return '';
    }
    // we must get a profile id
    if (!isset($params['profile_id'])) {
        return jrCore_smarty_missing_error('profile_id');
    }
    if (!jrCore_checktype($params['profile_id'], 'number_nz')) {
        return jrCore_smarty_invalid_error('profile_id');
    }
    // If we are viewing our own profile....
    if (jrUser_get_profile_home_key('_profile_id') == $params['profile_id']) {
        return '';
    }
    $params['profile_id'] = (int) $params['profile_id'];
    $_lang                = jrUser_load_lang_strings();

    // Figure template
    $tpl = 'button_follow.tpl';
    $val = $_lang['jrFollower'][1];
    if ($_rt = jrFollower_is_follower($_user['_user_id'], $params['profile_id'])) {
        // See if we are pending or active...
        if (isset($_rt['follow_active']) && $_rt['follow_active'] != '1') {
            $tpl = 'button_pending.tpl';
            $val = $_lang['jrFollower'][5];
        }
        else {
            $tpl = 'button_following.tpl';
            $val = $_lang['jrFollower'][2];
        }
    }
    $params['value'] = $val;
    if (!isset($params['title'])) {
        $params['title'] = $val;
    }
    if (isset($params['title']) && jrCore_checktype($params['title'], 'number_nz') && isset($_lang["{$_conf['jrCore_active_skin']}"]["{$params['title']}"])) {
        $params['title'] = $_lang["{$_conf['jrCore_active_skin']}"]["{$params['title']}"];
    }
    $params['title'] = jrCore_entity_string($params['title']);

    // process and return
    $out = jrCore_parse_template($tpl, $params, 'jrFollower');
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Returns a list of users that the profile is currently not following as a suggestion list.
 * usage: {jrFollower_who_to_follow profile_id=$_profile_id}
 * @param $params
 * @param $smarty
 * @return bool|string
 */
function smarty_function_jrFollower_who_to_follow($params, &$smarty)
{
    global $_conf;

    // we must get a profile id
    if (!isset($params['profile_id']) || !jrCore_checktype($params['profile_id'], 'number_nz')) {
        return jrCore_smarty_missing_error('profile_id');
    }

    $limit = 5;
    if (isset($params['limit']) && jrCore_checktype($params['limit'], 'number_nz')) {
        $limit = $params['limit'];
    }

    $template = 'who_to_follow.tpl';
    $tpl_dir  = 'jrFollower';
    if (isset($params['template']) && strlen($params['template']) > 0) {
        $template = $params['template'];
        $tpl_dir  = $_conf['jrCore_active_skin'];
    }

    $_sc = array(
        'search'         => array(
            "_profile_id = {$params['profile_id']} "
        ),
        'return_keys'    => array('follow_profile_id'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'privacy_check'  => false,
        'quota_check'    => false,
        'limit'          => 5000
    );
    $_rt = jrCore_db_search_items('jrFollower', $_sc);

    $_ids = array($params['profile_id']);
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
        foreach ($_rt['_items'] as $item) {
            if (isset($item['follow_profile_id'])) {
                $_ids[] = (int) $item['follow_profile_id'];
            }
        }
    }

    $_it = array(
        'search'        => array(
            "_profile_id not_in " . implode(',', $_ids),
            "profile_image_size > 0"
        ),
        'limit'         => $limit,
        'skip_triggers' => true,
        'order_by'      => array('_profile_id' => 'random')
    );
    foreach ($params as $k => $v) {
        if (strpos($k, 'search') === 0) {
            $_it['search'][] = $v;
        }
    }
    $_it = jrCore_db_search_items('jrProfile', $_it);

    $out = '';
    if ($_it && is_array($_it) && isset($_it['_items'])) {
        $out = jrCore_parse_template($template, $_it, $tpl_dir);
    }
    if (!empty($params['assign'])) {
        /** @noinspection PhpUndefinedMethodInspection */
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}
