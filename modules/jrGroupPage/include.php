<?php
/**
 * Jamroom Group Pages module
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
function jrGroupPage_meta()
{
    $_tmp = array(
        'name'        => 'Group Pages',
        'url'         => 'group_page',
        'version'     => '1.2.2',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Adds Page support to Profile Groups',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2904/group-pages',
        'license'     => 'jcl',
        'category'    => 'profiles',
        'requires'    => 'jrGroup,jrCore:6.0.4'
    );
    return $_tmp;
}

/**
 * init
 */
function jrGroupPage_init()
{
    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrGroupPage', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrGroupPage', 'update');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrGroupPage', true);

    // Core support
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrGroupPage', 'on');
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrGroupPage', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrGroupPage', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrGroupPage', 'update', 'item_action.tpl');

    // Exclude us from the Profile Menu
    jrCore_register_module_feature('jrProfile', 'profile_menu', 'jrGroupPage', 'active', 'jrGroup');

    // We have fields that can be searched
    jrCore_register_module_feature('jrSearch', 'search_fields', 'jrGroupPage', 'npage_title', 1);

    // Register ourselves with the Groups core
    jrCore_register_module_feature('jrGroup', 'group_support', 'jrGroupPage', 'on');

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'jrGroupPage', 'profile_jrGroupPage_item_count', 1);

    // Get correct Liked Item title and URL
    jrCore_register_event_listener('jrAction', 'action_data', 'jrGroupPage_action_data_listener');

    // We add a "can create pages" config option to the group
    jrCore_register_event_listener('jrGroup', 'user_config', 'jrGroupPage_user_config_listener');
    jrCore_register_event_listener('jrGroup', 'user_config_defaults', 'jrGroupPage_user_config_defaults_listener');

    // Add in listener for when group owner is changed
    jrCore_register_event_listener('jrCore', 'db_update_item', 'jrGroupPage_db_update_item_listener');

    // Change owner listener to make sure new owner is a group member
    jrCore_register_event_listener('jrChangeOwner', 'owner_changed', 'jrGroupPage_owner_changed_listener');

    // Listeners to increment/decrement the parent group counter
    jrCore_register_event_listener('jrCore', 'db_create_item', 'jrGroupPage_db_create_item_listener');
    jrCore_register_event_listener('jrCore', 'db_delete_item', 'jrGroupPage_db_delete_item_listener');

    // Hand off private comments
    jrCore_register_event_listener('jrComment', 'private_item_ids', 'jrGroupPage_private_item_ids_listener');

    // Register our tools
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrGroupPage', 'group_config', array('Page Group Config', 'Assign existing Pages to specific Profile Groups'));

    return true;
}

//----------------------
// EVENT LISTENERS
//----------------------

/**
 * Increment group discuss counter
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroupPage_db_create_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrGroupPage' && jrCore_checktype($_data['npage_group_id'], 'number_nz')) {
        // Increment group discuss counter
        jrCore_db_increment_key('jrGroup', $_data['npage_group_id'], 'group_jrGroupPage_item_count', 1);
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
function jrGroupPage_db_delete_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrGroupPage' && jrCore_checktype($_data['npage_group_id'], 'number_nz')) {
        // Decrement group discuss counter
        jrCore_db_decrement_key('jrGroup', $_data['npage_group_id'], 'group_jrGroupPage_item_count', 1);
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
function jrGroupPage_private_item_ids_listener($_data, $_user, $_conf, $_args, $event)
{
    // Get private groups
    $_rt = jrGroup_get_private_groups();
    if ($_rt && is_array($_rt)) {
        // We have private groups - find Discussions in those groups
        $_sc = array(
            'search'              => array(
                'npage_group_id in ' . implode(',', $_rt)
            ),
            'return_item_id_only' => true,
            'skip_triggers'       => true,
            'quota_check'         => false,
            'privacy_check'       => false,
            'ignore_pending'      => true,
            'limit'               => 100000
        );
        $_sc = jrCore_db_search_items('jrGroupPage', $_sc);
        if ($_sc && is_array($_sc)) {
            $_data['jrGroupPage'] = $_sc;
        }
    }
    return $_data;
}

/**
 * Get title and URL for LIKE action entries
 * @param $_data array incoming data array of item including original owner profile and user IDs
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array of new owner profile and user IDs
 * @param $event string Event Trigger name
 * @return array
 */
function jrGroupPage_action_data_listener($_data, $_user, $_conf, $_args, $event)
{
    // Get correct URL to group items
    $url = jrCore_get_module_url('jrGroupPage');
    if ($_data['action_module'] == 'jrGroupPage' && isset($_data['action_data']['_group_data'])) {
        $_data['action_item_url']                  = "{$_conf['jrCore_base_url']}/{$_data['action_data']['_group_data']['profile_url']}/{$url}/{$_data['action_item_id']}/{$_data['action_title_url']}";
        // for backwards compatibility
        $_data['action_data']['group_profile_url'] = $_data['action_data']['_group_data']['profile_url'];
    }
    if (isset($_data['action_original_module']) && $_data['action_original_module'] == 'jrGroupPage' && isset($_data['action_original_data']['_group_data'])) {
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
function jrGroupPage_owner_changed_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['module']) && $_args['module'] == 'jrChangeOwner' && isset($_args['target_module']) && $_args['target_module'] == 'jrGroupPage' && jrCore_checktype($_data['npage_group_id'], 'number_nz')) {
        // Get parent group
        $_gt = jrCore_db_get_item('jrGroup', $_data['npage_group_id']);
        if ($_gt && is_array($_gt)) {
            if (!isset($_gt['group_member']["{$_args['_user_id']}"])) {
                // Make new owner a group member
                $tbl = jrCore_db_table_name('jrGroup', 'member');
                $req = "INSERT INTO {$tbl} (member_created, member_user_id, member_group_id, member_status, member_active, member_more)
                VALUES (UNIX_TIMESTAMP(), '{$_args['_user_id']}', '{$_data['npage_group_id']}', '1', '1', '')
                ON DUPLICATE KEY UPDATE member_created = UNIX_TIMESTAMP()";
                jrCore_db_query($req);
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
function jrGroupPage_user_config_listener($_data, $_user, $_conf, $_args, $event)
{
    // $_data = User Info
    // $_args = Group Info
    $val = 'off';
    if (isset($_args['group_member']["{$_data['_user_id']}"]['jrGroupPage_config_allowed']) && jrCore_checktype($_args['group_member']["{$_data['_user_id']}"]['jrGroupPage_config_allowed'], 'onoff')) {
        $val = $_args['group_member']["{$_data['_user_id']}"]['jrGroupPage_config_allowed'];
    }
    $_tmp = array(
        'name'     => 'jrGroupPage_config_allowed',
        'label'    => 'can create pages',
        'help'     => 'If checked, this User will be allowed to create pages in this Group',
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
function jrGroupPage_user_config_defaults_listener($_data, $_user, $_conf, $_args, $event)
{
    $_data['jrGroupPage_config_allowed'] = 'off';
    return $_data;
}

/**
 * Update any pages with new profile and user IDs if their parent group is having its owner changed
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGroupPage_db_update_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrGroup' && jrCore_checktype($_args['_item_id'], 'number_nz') && isset($_data['_profile_id']) && jrCore_checktype($_data['_profile_id'], 'number_nz') && isset($_data['_user_id']) && jrCore_checktype($_data['_user_id'], 'number_nz')) {
        // Get pages parented by this group
        $_s  = array(
            "search" => array("npage_group_id = {$_args['_item_id']}"),
            "limit"  => 10000
        );
        $_rt = jrCore_db_search_items('jrGroupPage', $_s);
        if (isset($_rt) && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
            $_tmp  = array();
            $_core = array();
            foreach ($_rt['_items'] as $rt) {
                $_tmp["{$rt['_item_id']}"]  = array(
                    'npage_title' => $rt['npage_title']
                );
                $_core["{$rt['_item_id']}"] = array(
                    '_profile_id' => $_data['_profile_id'],
                    '_user_id'    => $_data['_user_id']
                );
            }
            jrCore_db_update_multiple_items('jrGroupPage', $_tmp, $_core);
        }
    }
    return $_data;
}

/**
 * @deprecated - do not use
 * @param array
 * @return array
 */
function smarty_modifier_jrGroupPage_add_group_url($_items)
{
    if (isset($_items) && is_array($_items) && count($_items) > 0) {
        $_gid = array();
        foreach ($_items as $item) {
            if (jrCore_checktype($item['npage_group_id'], 'number_nz')) {
                $_gid["{$item['npage_group_id']}"] = 1;
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
                    $_items["{$k}"]["group_profile_url"] = $_gid["{$item['npage_group_id']}"];
                }
            }
        }
    }
    return $_items;
}
