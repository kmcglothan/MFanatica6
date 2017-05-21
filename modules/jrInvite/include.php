<?php
/**
 * Jamroom Invitations module
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
function jrInvite_meta()
{
    $_tmp = array(
        'name'        => 'Invitations',
        'url'         => 'invite',
        'version'     => '1.0.2',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Users can invite people to their profile and module items',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/3598/invitations',
        'license'     => 'mpl',
        'category'    => 'users'
    );
    return $_tmp;
}

/**
 * init
 */
function jrInvite_init()
{
    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrInvite', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrInvite', 'update');

    // Core support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrInvite', 'on');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrInvite', 'on');
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrInvite', 'on');

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'jrInvite', 'profile_jrInvite_item_count', 1);

    // Bring in our javascript
    jrCore_register_module_feature('jrCore', 'javascript', 'jrInvite', 'jrInvite.js');

    // notifications
    $_tmp = array(
        'label' => 21, // 21 = 'New Invitation'
        'help'  => 46 // 46 = 'If you receive an invitation, would you like to be notified?'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrInvite', 'new_invitation', $_tmp);

    // Skin menu link to Invites
    $_tmp = array(
        'group' => 'master,admin,user',
        'label' => 13,
        'url'   => "browse"
    );
    jrCore_register_module_feature('jrCore', 'skin_menu_item', 'jrInvite', 'invite_link', $_tmp);

    // Support for adding attendees
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrInvite_db_search_items_listener');
    jrCore_register_event_listener('jrCore', 'db_get_item', 'jrInvite_db_get_item_listener');

    // Recycle Bin
    jrCore_register_event_listener('jrCore', 'db_delete_item', 'jrInvite_db_delete_item_listener');
    jrCore_register_event_listener('jrCore', 'empty_recycle_bin', 'jrInvite_empty_recycle_bin_listener');
    jrCore_register_event_listener('jrCore', 'expire_recycle_bin', 'jrInvite_expire_recycle_bin_listener');
    jrCore_register_event_listener('jrCore', 'restore_recycle_bin_item', 'jrInvite_restore_recycle_bin_item_listener');

    // System Reset listener
    jrCore_register_event_listener('jrDeveloper', 'reset_system', 'jrInvite_reset_system_listener');

    return true;
}

//----------------------
// INVITE LISTENERS
//----------------------

/**
 * Empty Recycle Bin
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrInvite_empty_recycle_bin_listener($_data, $_user, $_conf, $_args, $event)
{
    $tbl = jrCore_db_table_name('jrInvite', 'invitee');
    $req = "DELETE FROM {$tbl} WHERE invitee_active = '0'";
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
function jrInvite_expire_recycle_bin_listener($_data, $_user, $_conf, $_args, $event)
{
    if (is_array($_data) && is_array($_data['_items']) && count($_data['_items']) > 0) {
        $_iv = array();
        $_id = array();
        foreach ($_data['_items'] as $k => $_it) {
            switch ($_it['module']) {
                case 'jrInvite':
                    $_iv[] = $_it['item_id'];
                    break;
                case 'jrUser':
                    $_id[] = $_it['item_id'];
                    break;
            }
        }
        $tbl = jrCore_db_table_name('jrInvite', 'invitee');
        if (count($_iv) > 0) {
            $req = "DELETE FROM {$tbl} WHERE invitee_invite_id IN(" . implode(',', $_iv) . ')';
            jrCore_db_query($req);
        }
        if (count($_id) > 0) {
            $req = "DELETE FROM {$tbl} WHERE invitee_user_id IN(" . implode(',', $_id) . ')';
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
function jrInvite_restore_recycle_bin_item_listener($_data, $_user, $_conf, $_args, $event)
{
    switch ($_args['module']) {

        case 'jrInvite':
            // An invite is being restored - undelete invitees
            $tbl = jrCore_db_table_name('jrInvite', 'invitee');
            $req = "UPDATE {$tbl} SET invitee_active = '1' WHERE invitee_invite_id = '" . intval($_args['item_id']) . "'";
            jrCore_db_query($req);
            break;

        case 'jrUser':
            // Restore this user's place in their events
            $tbl = jrCore_db_table_name('jrInvite', 'invitee');
            $req = "UPDATE {$tbl} SET invitee_active = '1' WHERE invitee_user_id = '" . intval($_args['item_id']) . "'";
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
function jrInvite_reset_system_listener($_data, $_user, $_conf, $_args, $event)
{
    $tbl = jrCore_db_table_name('jrInvite', 'invitee');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");
    return $_data;
}

/**
 * Add invitees to invite
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrInvite_db_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['module']) && $_args['module'] == 'jrInvite') {
        // Is table there
        if (!jrCore_db_table_exists('jrInvite', 'invitee')) {
            return $_data;
        }
        // Add in invitee info
        $tbl = jrCore_db_table_name('jrInvite', 'invitee');
        $req = "SELECT * FROM {$tbl} WHERE `invitee_invite_id` = {$_data['_item_id']} AND `invitee_active` = '1'";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt) && count($_rt) > 0) {
            $_invitee                      = array();
            $_data['invite_invitee_count'] = 0;
            foreach ($_rt as $rt) {
                $_uid["{$rt['invitee_user_id']}"]                    = $rt['invitee_user_id'];
                $_data['invite_invitee']["{$rt['invitee_user_id']}"] = $rt;
                $_data['invite_invitee_count']++;
            }
        }
        if (isset($_uid) && is_array($_uid) && count($_uid) > 0) {
            $_rt = array(
                'search'                       => array(
                    '_user_id in ' . implode(',', $_uid)
                ),
                'order_by'                     => array(
                    'user_name' => 'asc'
                ),
                'return_keys'                  => array('_user_id', 'user_name', '_profile_id', 'profile_name', 'profile_url', 'profile_quota_id'),
                'ignore_pending'               => true,
                'include_jrProfile_keys'       => true,
                'exclude_jrProfile_quota_keys' => true,
                'limit'                        => count($_uid)
            );
            $_rt = jrCore_db_search_items('jrUser', $_rt);
            if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
                // Add invitees to invite
                $_uid = array();
                foreach ($_rt['_items'] as $_v) {
                    $_uid["{$_v['_user_id']}"] = $_v;
                }
                if (isset($_data['invite_invitee']) && is_array($_data['invite_invitee'])) {
                    foreach ($_data['invite_invitee'] as $i => $_u) {
                        if (isset($_uid["{$_u['invitee_user_id']}"])) {
                            $_data['invite_invitee'][$i] = array_merge($_u, $_uid["{$_u['invitee_user_id']}"]);
                        }
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * Add invitees to invites
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrInvite_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if (is_array($_data) && isset($_data['_items'])) {
        // Expand Invite invitees
        if ($_args['module'] == 'jrInvite') {
            // Is table there
            if (!jrCore_db_table_exists('jrInvite', 'invitee')) {
                return $_data;
            }
            // Get all invite invitees
            $_iid = array();
            foreach ($_data['_items'] as $_v) {
                $_iid[] = $_v['_item_id'];
            }
            $_uid = array();
            $iid  = implode(',', $_iid);
            $tbl  = jrCore_db_table_name('jrInvite', 'invitee');
            $req  = "SELECT * FROM {$tbl} WHERE `invitee_invite_id` IN ({$iid}) AND `invitee_active` = '1'";
            $_rt  = jrCore_db_query($req, 'NUMERIC');
            if ($_rt && is_array($_rt) && count($_rt) > 0) {
                $_attendee = array();
                foreach ($_rt as $rt) {
                    $_invitee["{$rt['invitee_invite_id']}"]["{$rt['invitee_user_id']}"] = $rt;
                    $_uid["{$rt['invitee_user_id']}"]                                   = $rt['invitee_user_id'];
                }
            }
            foreach ($_data['_items'] as $k => $_v) {
                if (isset($_invitee["{$_v['_item_id']}"])) {
                    $_data['_items'][$k]['invite_invitee']       = $_invitee["{$_v['_item_id']}"];
                    $_data['_items'][$k]['invite_invitee_count'] = count($_invitee["{$_v['_item_id']}"]);
                }
            }
            if (count($_uid) > 0) {
                $_rt = array(
                    'search'                       => array(
                        '_user_id in ' . implode(',', $_uid)
                    ),
                    'order_by'                     => array(
                        'user_name' => 'asc'
                    ),
                    'return_keys'                  => array('_user_id', 'user_name', '_profile_id', 'profile_name', 'profile_url', 'profile_quota_id'),
                    'ignore_pending'               => true,
                    'include_jrProfile_keys'       => true,
                    'exclude_jrProfile_quota_keys' => true,
                    'limit'                        => count($_uid)
                );
                $_rt = jrCore_db_search_items('jrUser', $_rt);
                if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
                    // Add invitees to each invite
                    $_uid = array();
                    foreach ($_rt['_items'] as $_v) {
                        $_uid["{$_v['_user_id']}"] = $_v;
                    }
                    foreach ($_data['_items'] as $k => $_v) {
                        if (isset($_v['invite_invitee']) && is_array($_v['invite_invitee'])) {
                            foreach ($_data['_items'][$k]['invite_invitee'] as $i => $_u) {
                                if (isset($_uid["{$_u['invitee_user_id']}"])) {
                                    $_data['_items'][$k]['invite_invitee'][$i] = array_merge($_u, $_uid["{$_u['invitee_user_id']}"]);
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
 * Cleanup invites for deleted items
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrInvite_db_delete_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_checktype($_args['_item_id'], 'number_nz') && isset($_args['module']) && $_args['module'] == 'jrInvite') {
        // flag invitees of this invite as deleted...
        $tbl = jrCore_db_table_name('jrInvite', 'invitee');
        $req = "UPDATE {$tbl} SET invitee_active = '0' WHERE `invitee_invite_id` = {$_args['_item_id']}";
        jrCore_db_query($req);
    }
    elseif (jrCore_checktype($_args['_item_id'], 'number_nz') && isset($_args['module']) && $_args['module'] == 'jrUser') {
        // flag user as deleted...
        $tbl = jrCore_db_table_name('jrInvite', 'invitee');
        $req = "UPDATE {$tbl} SET invitee_active = '0' WHERE `invitee_user_id` = {$_data['_user_id']}";
        jrCore_db_query($req);
    }
    else {
        // We have an item being deleted - remove invites
        $_sc = array(
            'search'         => array(
                "invite_item_id = {$_args['_item_id']}",
                "invite_module = {$_args['module']}"
            ),
            'return_keys'    => array('_item_id'),
            'skip_triggers'  => true,
            'ignore_pending' => true,
            'privacy_check'  => false,
            'limit'          => 10000
        );
        $_rt = jrCore_db_search_items('jrInvite', $_sc);
        if (is_array($_rt['_items'])) {
            // See what Items we are deleting
            $_ids = array();
            foreach ($_rt['_items'] as $v) {
                $_ids[] = $v['_item_id'];
            }
            if (count($_ids) > 0) {
                jrCore_db_delete_multiple_items('jrInvite', $_ids, false);
            }
        }
    }
    return $_data;
}
