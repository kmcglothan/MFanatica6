<?php
/**
 * Jamroom 5 MailChimp User Sync module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
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
function jrMailChimp_meta()
{
    $_tmp = array(
        'name'        => 'MailChimp User Sync',
        'url'         => 'mailchimp',
        'version'     => '1.0.2',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Synchronize user account information with a MailChimp List',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/3010/mailchimp-user-sync',
        'category'    => 'communication',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrMailChimp_init()
{
    // Custom tab
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrMailChimp', 'list_fields', 'Merge Tag Browser');

    // Tools
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrMailChimp', 'list_fields', array('Merge Tag Browser', 'Configure Linked MailChimp Merge Tags'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrMailChimp', 'sync', array('Sync User Accounts', 'Synchronize Existing User Accounts with the configured MailChimp List'));

    // Listeners
    jrCore_register_event_listener('jrUser', 'signup_activated', 'jrMailChimp_signup_activated_listener');
    jrCore_register_event_listener('jrUser', 'user_updated', 'jrMailChimp_user_updated_listener');
    jrCore_register_event_listener('jrUser', 'delete_user', 'jrMailChimp_delete_user_listener');
    jrCore_register_event_listener('jrCore', 'db_update_item', 'jrMailChimp_db_update_item_listener');

    // Our Queue worker for list maintenance
    jrCore_register_queue_worker('jrMailChimp', 'list_maintenance', 'jrMailChimp_list_maintenance_worker', 0, 1, 7200);

    return true;
}

//-------------------
// QUEUE WORKER
//-------------------
function jrMailChimp_list_maintenance_worker($_queue)
{
    require_once APP_DIR . '/modules/jrMailChimp/contrib/mailchimp-api/MailChimp.php';
    $mcm = new \Drewm\MailChimp($_queue['api_key']);
    switch ($_queue['task']) {

        // Add them to the list...
        case 'subscribe':
            $_us = array(
                'id'                => $_queue['list_id'],
                'email'             => array('email' => $_queue['_user']['user_email']),
                'double_optin'      => false,
                'update_existing'   => true,
                'replace_interests' => false,
                'send_welcome'      => false
            );
            if ($_mt = jrMailChimp_get_user_merge_vars($_queue['_user'])) {
                $_us['merge_vars'] = $_mt;
            }
            $res = $mcm->call('lists/subscribe', $_us);
            if (!$res || !is_array($res) || !isset($res['euid'])) {
                $_rp = array(
                    '_user'    => $_queue['_user'],
                    'response' => $res
                );
                jrCore_logger('CRI', "error adding user account to MailChimp list", $_rp);
            }
            return true;
            break;

        // Remove from list
        case 'unsubscribe':
            $_us = array(
                'id'            => $_queue['list_id'],
                'email'         => array('email' => $_queue['_user']['user_email']),
                'delete_member' => true,
                'send_goodbye'  => false,
                'send_notify'   => false
            );
            $mcm->call('lists/unsubscribe', $_us);
            return true;
            break;

        // Update existing user in list
        case 'update':
            $_us = array(
                'id'                => $_queue['list_id'],
                'email'             => array('email' => $_queue['_user']['user_email']),
                'replace_interests' => false
            );
            if ($_mt = jrMailChimp_get_user_merge_vars($_queue['_user'])) {
                $_mt['EMAIL']      = $_queue['new_user_email'];
                $_us['merge_vars'] = $_mt;
            }
            else {
                $_mt['EMAIL'] = $_queue['new_user_email'];
            }
            $_us['merge_vars'] = $_mt;
            $res               = $mcm->call('lists/update-member', $_us);
            if (!$res || !is_array($res) || !isset($res['euid'])) {
                $_rp = array(
                    '_user'    => $_queue['_user'],
                    'response' => $res
                );
                jrCore_logger('CRI', "error updating user account in MailChimp list", $_rp);
            }
            return true;
            break;
    }

    jrCore_logger('CRI', "unknown list_maintenance_worker queue task: {$_queue['task']}", $_queue);
    return true;
}

//-------------------
// EVENT LISTENERS
//-------------------

/**
 * Listens for users changing Notification preferences
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrMailChimp_db_update_item_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (isset($_args['module']) && $_args['module'] == 'jrUser' && isset($_post['option']) && $_post['option'] == 'notifications_save' && jrMailChimp_is_active()) {
        // Check preferences
        $_usr = jrCore_db_get_item('jrUser', $_args['_item_id'], true, true);
        $_kys = array(
            'user_notification_disabled'                 => 'on',
            'user_jrNewsLetter_newsletter_notifications' => 'off'
        );
        $task = 'subscribe';
        foreach ($_kys as $key => $mode) {
            if (isset($_data[$key]) && $_data[$key] == $mode) {
                $task = 'unsubscribe';
            }
        }
        if (isset($_usr['user_email']) && jrCore_checktype($_usr['user_email'], 'email')) {
            // Check for Mailgun un subscribe
            if (isset($_usr['user_unsubscribed']) && $_usr['user_unsubscribed'] == 'on') {
                $task = 'unsubscribe';
            }
            $_tm = array(
                'task'    => $task,
                'api_key' => $_conf['jrMailChimp_api_key'],
                'list_id' => $_conf['jrMailChimp_list_id'],
                '_user'   => $_usr
            );
            jrCore_queue_create('jrMailChimp', 'list_maintenance', $_tm);
        }
    }
    return $_data;
}

/**
 * Listens for new user activation so it can sync user data
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrMailChimp_signup_activated_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrMailChimp_is_active()) {
        if (isset($_data['user_email']) && jrCore_checktype($_data['user_email'], 'email')) {
            // Add this user to the linked MailChimp List
            $_tm = array(
                'task'    => 'subscribe',
                'api_key' => $_conf['jrMailChimp_api_key'],
                'list_id' => $_conf['jrMailChimp_list_id'],
                '_user'   => $_data
            );
            jrCore_queue_create('jrMailChimp', 'list_maintenance', $_tm);
        }
    }
    return $_data;
}

/**
 * Watch for users changing email addresses
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrMailChimp_user_updated_listener($_data, $_user, $_conf, $_args, $event)
{
    // See if this user is changing email addresses
    if (jrMailChimp_is_active()) {
        if (isset($_args['user_email']) && $_args['user_email'] != $_data['user_email']) {
            // Update this user's email
            $_tm = array(
                'task'           => 'update',
                'api_key'        => $_conf['jrMailChimp_api_key'],
                'list_id'        => $_conf['jrMailChimp_list_id'],
                'new_user_email' => $_args['user_email'],
                '_user'          => $_data
            );
            jrCore_queue_create('jrMailChimp', 'list_maintenance', $_tm);
        }
    }
    return $_data;
}

/**
 * Delete a user from our list when they delete their account
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrMailChimp_delete_user_listener($_data, $_user, $_conf, $_args, $event)
{
    // See if this user is changing email addresses
    if (jrMailChimp_is_active()) {
        if (jrCore_checktype($_data['user_email'], 'email')) {
            // Remove this user from the linked MailChimp List
            $_tm = array(
                'task'    => 'unsubscribe',
                'api_key' => $_conf['jrMailChimp_api_key'],
                'list_id' => $_conf['jrMailChimp_list_id'],
                '_user'   => array('user_email' => $_data['user_email'])
            );
            jrCore_queue_create('jrMailChimp', 'list_maintenance', $_tm);
        }
    }
    return $_data;
}

//-------------------
// FUNCTIONS
//-------------------

/**
 * Check if everything is configured and active
 * @return bool
 */
function jrMailChimp_is_active()
{
    global $_conf;
    if (isset($_conf['jrMailChimp_active']) && $_conf['jrMailChimp_active'] == 'on' && isset($_conf['jrMailChimp_api_key']) && strlen($_conf['jrMailChimp_api_key']) > 2 && isset($_conf['jrMailChimp_list_id']) && strlen($_conf['jrMailChimp_list_id']) > 0) {
        return true;
    }
    return false;
}

/**
 * Get a specific user's Merge Tags
 * @param $_us array User Info
 * @return array|bool
 */
function jrMailChimp_get_user_merge_vars($_us)
{
    $_ot = array();
    if (!$_mt = jrCore_get_flag('jrmailchimp_merge_vars')) {
        $_mt = array(
            'skip_triggers' => true,
            'limit'         => 1000
        );
        $_mt = jrCore_db_search_items('jrMailChimp', $_mt);
        jrCore_set_flag('jrmailchimp_merge_vars', $_mt);
    }
    if ($_mt && is_array($_mt) && isset($_mt['_items'])) {
        foreach ($_mt['_items'] as $_tag) {
            if (isset($_us["{$_tag['tag_key']}"])) {
                $_ot["{$_tag['tag_merge']}"] = $_us["{$_tag['tag_key']}"];
            }
        }
        if (count($_ot) > 0) {
            return $_ot;
        }
    }
    return false;
}

/**
 * Get existing Merge Tags
 */
function jrMailChimp_get_merge_tags()
{
    global $_conf;
    require_once APP_DIR . '/modules/jrMailChimp/contrib/mailchimp-api/MailChimp.php';
    $_us = array(
        'id' => array($_conf['jrMailChimp_list_id']),
    );
    $mcm = new \Drewm\MailChimp($_conf['jrMailChimp_api_key']);
    $res = $mcm->call('lists/merge-vars', $_us);
    if ($res && is_array($res) && isset($res['success_count']) && isset($res['data'][0]['merge_vars'])) {
        $_mv = array();
        foreach ($res['data'][0]['merge_vars'] as $k => $_inf) {
            $_mv["{$_inf['tag']}"] = "{$_inf['tag']} ({$_inf['name']})";
        }
        if (isset($_mv['EMAIL'])) {
            unset($_mv['EMAIL']);
        }
        if (count($_mv) > 0) {
            return $_mv;
        }
    }
    return false;
}
