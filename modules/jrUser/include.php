<?php
/**
 * Jamroom Users module
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

// For Crawler-Detect
use Jaybizzle\CrawlerDetect\CrawlerDetect;

/**
 * meta
 */
function jrUser_meta()
{
    $_tmp = array(
        'name'        => 'Users',
        'url'         => 'user',
        'version'     => '2.3.1',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Core support for User Accounts, Sessions and Languages',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/945/users',
        'category'    => 'users',
        'requires'    => 'jrCore:6.1.6b7',
        'license'     => 'mpl',
        'priority'    => 1, // HIGHEST load priority
        'locked'      => true,
        'activate'    => true
    );
    return $_tmp;
}

/**
 * init
 */
function jrUser_init()
{
    // Register the module's javascript
    jrCore_register_module_feature('jrCore', 'javascript', 'jrUser', 'jrUser.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrUser', 'jrUser_admin.js', 'admin');

    // register our triggers
    jrCore_register_event_trigger('jrUser', 'signup_validate', 'Fired when a user submits account data for a new account');
    jrCore_register_event_trigger('jrUser', 'signup_created', 'Fired when a user successfully signs up for a new account');
    jrCore_register_event_trigger('jrUser', 'signup_activated', 'Fired when a user successfully validates their account');
    jrCore_register_event_trigger('jrUser', 'login_success', 'Fired when a user successfully logs in');
    jrCore_register_event_trigger('jrUser', 'logout', 'Fired when a user logs out (before session destroyed)');
    jrCore_register_event_trigger('jrUser', 'session_init', 'Fired when session handler is initialized');
    jrCore_register_event_trigger('jrUser', 'session_started', 'Fired when a session is created');
    jrCore_register_event_trigger('jrUser', 'user_updated', 'Fired when a User Account is updated');
    jrCore_register_event_trigger('jrUser', 'account_tabs', 'Fired when the Tabs are created in the User Account');
    jrCore_register_event_trigger('jrUser', 'delete_user', 'Fired when a User Account is deleted');
    jrCore_register_event_trigger('jrUser', 'notify_user', 'Fired when a User is sent a notification');
    jrCore_register_event_trigger('jrUser', 'hourly_notification', 'Fired hourly for timed module notifications');

    // If the tracer module is installed, we have a few events for it
    jrCore_register_module_feature('jrTrace', 'trace_event', 'jrUser', 'signup_activated', 'A new user activates their account');
    jrCore_register_module_feature('jrTrace', 'trace_event', 'jrUser', 'login_success', 'User logs into the system');

    // core event listeners
    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrUser_db_search_params_listener');
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrUser_db_search_items_listener');
    jrCore_register_event_listener('jrCore', 'form_field_create', 'jrUser_form_field_create_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrUser_verify_module_listener');
    jrCore_register_event_listener('jrCore', 'repair_module', 'jrUser_repair_module_listener');
    jrCore_register_event_listener('jrCore', 'template_variables', 'jrUser_template_variables_listener');
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrUser_daily_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'hourly_maintenance', 'jrUser_hourly_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'minute_maintenance', 'jrUser_minute_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'form_validate_exit', 'jrUser_form_validate_exit_listener');
    jrCore_register_event_listener('jrCore', 'email_addresses', 'jrUser_email_addresses_listener');

    // Admin notifications on new signup
    jrCore_register_event_listener('jrUser', 'signup_activated', 'jrUser_signup_activated_listener');

    // Listen for force User SSL
    jrCore_register_event_listener('jrCore', 'view_results', 'jrUser_view_results_listener');

    // Add login to timeline
    jrCore_register_event_listener('jrUser', 'login_success', 'jrUser_login_success_listener');

    // Listen for site pages and check site against site privacy setting
    jrCore_register_event_listener('jrUser', 'session_started', 'jrUser_session_started_listener');

    // System reset listener
    jrCore_register_event_listener('jrDeveloper', 'reset_system', 'jrUser_reset_system_listener');

    // Sync user sessions when profile info is changed
    jrCore_register_event_listener('jrProfile', 'profile_updated', 'jrUser_profile_updated_listener');

    // User tool views
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrUser', 'create', array('Create a New User', 'Create a new User Account'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrUser', 'create_language', array('Create a Language', 'Create a new Language by cloning an existing Language'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrUser', 'delete_language', array('Delete a Language', 'Delete a language that is no longer used'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrUser', 'export_language', array('Export Language Strings', 'Export Language strings to an export file'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrUser', 'import_language', array('Import Language Strings', 'Import Language strings from an export file'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrUser', 'reset_language', array('Reset Language Strings', 'Reset language strings for a module or skin'));

    // We provide our own data browser
    jrCore_register_module_feature('jrCore', 'data_browser', 'jrUser', 'jrUser_data_browser');

    // Register our account tabs..
    jrCore_register_module_feature('jrUser', 'account_tab', 'jrUser', 'account', 42);
    jrCore_register_module_feature('jrUser', 'account_tab', 'jrUser', 'notifications', 64);

    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrUser', 'account');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrUser', 'signup');

    // User Account
    $_tmp = array(
        'group' => 'user',
        'label' => 116,
        'url'   => 'account',
        'order' => 1
    );
    jrCore_register_module_feature('jrCore', 'skin_menu_item', 'jrUser', 'account', $_tmp);

    // User Logout
    $_tmp = array(
        'group' => 'user',
        'label' => 117,
        'url'   => 'logout',
        'order' => 100
    );
    jrCore_register_module_feature('jrCore', 'skin_menu_item', 'jrUser', 'logout', $_tmp);

    // Admin Notifications
    $_tmp = array(
        'label' => 'new account notify',
        'help'  => 'Do you want to be notified when a new User Account is created?',
        'group' => 'admin'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrUser', 'signup_notify', $_tmp);

    // register our custom CSS
    jrCore_register_module_feature('jrCore', 'css', 'jrUser', 'jrUser.css');

    // We provide some dashboard panels
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrUser', 'total user accounts', 'jrUser_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrUser', 'daily active users', 'jrUser_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrUser', 'monthly active users', 'jrUser_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrUser', 'users and visitors online', 'jrUser_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrUser', 'users online', 'jrUser_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrUser', 'visitors online', 'jrUser_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrUser', 'bots online', 'jrUser_dashboard_panels');

    // Site Builder widgets
    jrCore_register_module_feature('jrSiteBuilder', 'widget', 'jrUser', 'widget_login', 'User Login and Signup');

    // Graph Support
    $_tmp = array(
        'title'    => 'Daily Active Users',
        'function' => 'jrUser_graph_daily_active_users',
        'group'    => 'admin'
    );
    jrCore_register_module_feature('jrGraph', 'graph_config', 'jrUser', 'daily_active_users', $_tmp);

    // Register our session plugins
    jrCore_register_system_plugin('jrUser', 'session', 'mysql', 'User Session (default)');

    // Action support
    jrCore_register_module_feature('jrCore', 'action_support', 'jrUser', 'signup', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrUser', 'login', 'item_action.tpl');

    return true;
}

//------------------------------------
// WIDGETS
//------------------------------------

/**
 * Display CONFIG screen for Widget
 * @param $_post array Post info
 * @param $_user array User array
 * @param $_conf array Global Config
 * @param $_wg array Widget info
 * @return bool
 */
function jrUser_widget_login_config($_post, $_user, $_conf, $_wg)
{
    $_opt = array(
        'login'  => "User Log In Form",
        'signup' => "User Sign Up Form",
    );
    // Widget Content
    $_tmp = array(
        'name'     => 'widget_type',
        'label'    => 'Type',
        'help'     => 'Add a log in box or a sign up box to the page.',
        'type'     => 'radio',
        'options'  => $_opt,
        'default'  => 'login',
        'required' => true,
        'layout'   => 'vertical',
        'value'    => (isset($_wg['widget_data']['type'])) ? $_wg['widget_data']['type'] : '',
        'validate' => 'onoff',
    );
    jrCore_form_field_create($_tmp);

    $_qta = jrProfile_get_signup_quotas();
    $_opt = array();
    if (isset($_qta) && is_array($_qta)) {
        foreach ($_qta as $qid => $qname) {
            $_opt[$qid] = "(quota) {$qname}";
        }
    }
    $_tmp = array(
        'name'     => 'signup_quota',
        'label'    => 'Signup Quotas',
        'sublabel' => 'more than 1 allowed',
        'help'     => 'You have more than 1 signup quota active, select which options are available from this location. Use shift+click to select many, or ctrl+click to select individually',
        'type'     => 'select_multiple',
        'options'  => $_opt,
        'value'    => (isset($_wg['widget_data']['signup_quota'])) ? $_wg['widget_data']['signup_quota'] : '',
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    return true;
}

/**
 * Get Widget results from posted Config data
 * @param $_post array Post info
 * @return array
 */
function jrUser_widget_login_config_save($_post)
{
    // set the group to logged out only by default.
    if (jrCore_checktype($_post['widget_id'], 'number_nz')) {
        $tbl = jrCore_db_table_name('jrSiteBuilder', 'widget');
        $req = "UPDATE {$tbl} SET widget_groups = 'visitor' WHERE widget_id = '{$_post['widget_id']}' LIMIT 1";
        jrCore_db_query($req);
    }

    if ($_post['widget_type'] == 'signup' && empty($_post['signup_quota'])) {
        // error
        jrCore_set_form_notice('error', 'There must be at least 1 quota selected if you want to add a signup widget to the page.');
        jrCore_form_result();
    }

    $_data = array(
        'type'         => $_post['widget_type'],
        'signup_quota' => $_post['signup_quota']
    );
    return array('widget_data' => $_data);
}

/**
 * Widget DISPLAY
 * @param $_widget array Page Widget info
 * @return string
 */
function jrUser_widget_login_display($_widget)
{
    if (!jrUser_is_logged_in()) {
        if ($_widget['widget_data']['type'] == 'signup') {
            $_quotas = array();
            $_q      = jrProfile_get_signup_quotas();
            foreach ($_q as $quota_id => $quota_name) {
                if (in_array($quota_id, $_widget['widget_data']['signup_quota'])) {
                    $_quotas[$quota_id] = $quota_name;
                }
            }
            jrCore_set_flag('jrprofile_get_signup_quotas', $_quotas);

        }
        $out = jrCore_capture_module_view_function('jrUser', $_widget['widget_data']['type'], array('_1' => 'widget'));
        jrCore_delete_flag('jrprofile_get_signup_quotas');
        return $out;
    }
    elseif (jrUser_is_admin()) {
        return ucfirst($_widget['widget_data']['type']) . " box shows here to logged out users";
    }
    return '';
}

//------------------------------------
// GRAPHS
//------------------------------------

/**
 * Daily Active Users
 * @param $module string Module
 * @param $name string Name of Graph to create
 * @param $_args array Passed in Parameters
 * @return array
 */
function jrUser_graph_daily_active_users($module, $name, $_args)
{
    $_rs = array(
        '_sets' => array(
            0 => array(
                'label'       => "Daily Active Users",
                'date_format' => '%m/%d/%Y',
                'minTickSize' => "[1, 'day']",
                'type'        => 'line',
                'pointRadius' => 3,
                '_data'       => array()
            )
        )
    );
    // Get our data
    $tbl = jrCore_db_table_name('jrUser', 'stat');
    $req = "SELECT stat_date AS c, stat_value AS v FROM {$tbl} WHERE stat_key = 'daily_active_users' ORDER BY stat_id DESC LIMIT {$_args['days']}";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        $_rt = array_reverse($_rt);
        foreach ($_rt as $v) {
            $yr = substr($v['c'], 0, 4);
            $mn = substr($v['c'], 4, 2);
            $dy = substr($v['c'], 6, 2);
            $tm = mktime(0, 0, 0, $mn, $dy, $yr);
            $tm = "{$tm}";
            if (!isset($_rs['_sets'][0]['_data'][$tm])) {
                $_rs['_sets'][0]['_data'][$tm] = 0;
            }
            $_rs['_sets'][0]['_data'][$tm] += $v['v'];
        }
    }
    return $_rs;
}

//------------------------------------
// DASHBOARD
//------------------------------------

/**
 * User Accounts Dashboard Panels
 * @param $panel
 * @return bool|int
 */
function jrUser_dashboard_panels($panel)
{
    global $_conf;
    // The panel being asked for will come in as $panel
    $out = false;
    switch ($panel) {

        case 'total user accounts':
            $out = array('title' => jrCore_number_format(jrCore_db_get_datastore_item_count('jrUser')));
            break;

        case 'users and visitors online':
            $num = jrUser_session_online_user_count(900, 'combined');
            if ($num == 0) {
                $num = 1;  // We always have the dashboard viewing user online
            }
            $out = array('title' => jrCore_number_format($num));
            break;

        case 'users online':
            $num = jrUser_session_online_user_count(900, 'user');
            if ($num == 0) {
                $num = 1;  // We always have the dashboard viewing user online
            }
            $out = array('title' => jrCore_number_format($num));
            break;

        case 'visitors online':
            $num = jrUser_session_online_user_count(900, 'visitor');
            if ($num == 0) {
                $num = 1;  // We always have the dashboard viewing user online
            }
            $out = array('title' => jrCore_number_format($num));
            break;

        case 'bots online':
            $num = 0;
            if (!isset($_conf['jrUser_bot_sessions']) || $_conf['jrUser_bot_sessions'] == 'on') {
                $num = jrUser_session_online_user_count(900, 'bot');
            }
            $out = array('title' => jrCore_number_format($num));
            break;

        case 'daily active users':
            $beg = strtotime('midnight', time());
            $num = jrCore_db_run_key_function('jrUser', 'user_last_login', "> {$beg}", 'COUNT');
            $out = array(
                'title' => jrCore_number_format($num),
                'graph' => 'daily_active_users'
            );
            break;

        case 'monthly active users':
            $old = (time() - (30 * 86400));
            $num = jrCore_db_run_key_function('jrUser', 'user_last_login', "> {$old}", 'COUNT');
            $out = array(
                'title' => jrCore_number_format($num),
                'graph' => 'daily_active_users'
            );
            break;

    }
    return ($out) ? $out : false;
}

//------------------------------------
// EVENT LISTENERS
//------------------------------------

/**
 * System Reset listener
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrUser_reset_system_listener($_data, $_user, $_conf, $_args, $event)
{
    $_tb = array(
        'cookie',
        'forgot',
        'url',
        'pw_attempt',
        'device',
        'stat',
        'suppressed'
    );
    foreach ($_tb as $table) {
        $tbl = jrCore_db_table_name('jrUser', $table);
        jrCore_db_query("TRUNCATE TABLE {$tbl}");
        jrCore_db_query("OPTIMIZE TABLE {$tbl}");
    }
    return $_data;
}

/**
 * Sync user sessions when profile info is changed by an admin
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrUser_profile_updated_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (jrUser_is_admin() && isset($_data['_profile_id']) && jrCore_checktype($_data['_profile_id'], 'number_nz')) {
        // Are we changing quota_id's ?
        if (isset($_post['profile_quota_id']) && $_post['profile_quota_id'] != $_data['profile_quota_id']) {
            // We are changing quota_id's for a profile - find users and sync sessions
            if ($_us = jrProfile_get_owner_info($_data['_profile_id'])) {
                foreach ($_us as $u) {
                    if (isset($u['_user_id']) && $u['_user_id'] != $_user['_user_id']) {
                        jrUser_set_session_sync_for_user_id($u['_user_id'], 'on');
                    }
                }
            }
        }

    }
    return $_data;
}

/**
 * Make sure none of the addresses being sent are suppressed
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrUser_email_addresses_listener($_data, $_user, $_conf, $_args, $event)
{
    // Are any of these addresses NOT in our system?
    $_em = array();
    foreach ($_data as $k => $email) {
        if (!is_array($_args) || !isset($_args[$email])) {
            // We do not have an account for this user - see if it is suppressed
            $_em[] = jrCore_db_escape($email);
        }
    }
    if (count($_em) > 0) {
        $tbl = jrCore_db_table_name('jrUser', 'suppressed');
        $req = "SELECT * FROM {$tbl} WHERE email_address IN('" . implode("','", $_em) . "')";
        $_em = jrCore_db_query($req, 'email_address', false, 'email_address');
        if ($_em && is_array($_em)) {
            foreach ($_data as $k => $v) {
                if (isset($_em[$v])) {
                    unset($_data[$k]);
                }
            }
        }
    }
    return $_data;
}

/**
 * Validate the quota login_page field
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrUser_form_validate_exit_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_data['module'] == 'jrUser' && isset($_data['login_page'])) {
        $_data['login_page'] = strtolower(trim($_data['login_page']));
        if ($_data['login_page'] != '' && $_data['login_page'] != 'profile' && $_data['login_page'] != 'index' && !jrCore_checktype($_data['login_page'], 'url')) {
            jrCore_set_form_notice('notice', "Invalid Login Page entry");
            jrCore_form_field_hilight('login_page');
            jrCore_form_result();
        }
    }
    return $_data;
}

/**
 * Hourly Notifications
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrUser_hourly_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    // Hourly Notifications
    // Messages MUST be individually formatted as an array with ALL keys:
    // array(
    //    'event'   => 'event_name' (must be registered as a notification option)
    //    'module'  => ModuleDir
    //    'user_id' => UserID to send notification to
    //    'subject' => Subject of Notification
    //    'message' => Message of Notification
    // )
    $_temp = jrCore_trigger_event('jrUser', 'hourly_notification', array());
    if ($_temp && is_array($_temp) && count($_temp) > 0) {
        foreach ($_temp as $_n) {
            if (isset($_n['module']) && jrCore_module_is_active($_n['module'])) {
                if (isset($_n['user_id']) && jrCore_checktype($_n['user_id'], 'number_nz')) {
                    if (isset($_n['event']{1}) && isset($_n['subject']{1}) && isset($_n['message']{1})) {
                        jrUser_notify($_n['user_id'], 0, $_n['module'], $_n['event'], $_n['subject'], $_n['message']);
                    }
                }
            }
        }
    }

    // Cleanup old password attempts
    jrUser_delete_old_password_attempts();

    // Cleanup old FORGOT requests
    $tbl = jrCore_db_table_name('jrUser', 'forgot');
    $req = "DELETE FROM {$tbl} WHERE forgot_time < (UNIX_TIMESTAMP() - 86400)";
    jrCore_db_query($req);

    return $_data;
}

/**
 * Cleanup items
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrUser_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    // Fix up form designer fields
    $tbl = jrCore_db_table_name('jrCore', 'form');
    $req = "UPDATE {$tbl} SET `options` = 'jrUser_get_languages' WHERE `module` = 'jrUser' AND `name` = 'user_language'";
    jrCore_db_query($req);
    $req = "UPDATE {$tbl} SET `options` = 'jrProfile_get_signup_quotas', `group` = 'all' WHERE `module` = 'jrUser' AND `view` = 'signup' AND `name` = 'quota_id'";
    jrCore_db_query($req);

    // Fields in our signup form must always be set to "all"
    $req = "UPDATE {$tbl} SET `group` = 'all' WHERE `module` = 'jrUser' AND `view` = 'signup' AND `name` IN('user_passwd1', 'user_passwd2', 'user_name', 'user_email')";
    jrCore_db_query($req);

    // Remove any user passwords wrongly saved
    jrCore_db_delete_key_from_all_items('jrUser', 'user_passwd1');
    jrCore_db_delete_key_from_all_items('jrUser', 'user_passwd2');

    return $_data;
}

/**
 * Repair Database entries
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrUser_repair_module_listener($_data, $_user, $_conf, $_args, $event)
{
    // Delete keys that should never be saved
    jrCore_db_delete_key_from_all_items('jrUser', 'user_passwd1');
    jrCore_db_delete_key_from_all_items('jrUser', 'user_passwd2');
    jrCore_db_delete_key_from_all_items('jrUser', 'user_id');
    jrCore_db_delete_key_from_all_items('jrUser', 'user_linked_profiles');

    // Bad user accounts that only have single keys - not complete accounts
    $_id = jrCore_db_get_items_missing_key('jrUser', 'user_name');
    if ($_id && is_array($_id) && count($_id) > 0) {
        foreach ($_id as $k => $id) {
            if ($id == 0) {
                unset($_id[$k]);
            }
        }
        if (count($_id) > 0) {
            jrCore_db_delete_multiple_items('jrUser', $_id);
        }
    }

    // Users missing user_validate key
    $_id = jrCore_db_get_items_missing_key('jrUser', 'user_validate');
    if ($_id && is_array($_id) && count($_id) > 0) {
        $_up = array();
        foreach ($_id as $id) {
            $_up[$id] = array('user_validate' => md5(microtime() . mt_rand(0, 999999)));
        }
        if (count($_up) > 0) {
            jrCore_db_update_multiple_items('jrUser', $_up);
            jrCore_logger('INF', "updated " . count($_up) . " user accounts missing user_validate key");
        }
    }

    return $_data;
}

/**
 * Optionally display Sign In page to non-logged in users dependent upon site privacy setting
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrUser_session_started_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if ($_conf['jrCore_maintenance_mode'] != 'on' && !jrCore_is_ajax_request() && isset($_conf['jrUser_site_privacy']) && jrCore_checktype($_conf['jrUser_site_privacy'], 'number_nz') && $_conf['jrUser_site_privacy'] > 1 && !jrUser_is_logged_in()) {

        if (!isset($_post['module_url']) && isset($_conf['jrUser_site_privacy']) && $_conf['jrUser_site_privacy'] == 2) {
            return $_data;
        }
        elseif (isset($_post['option'])) {
            // See if we have requested an allowed module/view
            switch ($_post['option']) {
                case 'webhook':
                case 'login':
                case 'login_save':
                case 'forgot':
                case 'forgot_save':
                case 'logout':
                case 'form_validate':
                case 'signup':
                case 'signup_save':
                case 'activate':
                case 'new_password':
                case 'new_password_save':
                case 'unsubscribe':
                case 'unsubscribe_save':
                case 'unsubscribe_confirm':
                    return $_data;
                    break;
                default:
                    // Let other modules know we are going to block for site privacy config
                    $_data = jrCore_trigger_event('jrUser', 'site_privacy_check', $_data, $_post);
                    if (isset($_data['allow_private_site_view']) && $_data['allow_private_site_view'] === true) {
                        // A module listener has allowed this view
                        return $_data;
                    }
                    jrUser_session_require_login();
                    break;
            }
        }
        // redirect user to login
        elseif (isset($_conf['jrUser_site_privacy']) && $_conf['jrUser_site_privacy'] == '2') {
            if (isset($_post['module_url']) && !isset($_urls["{$_post['module_url']}"])) {
                jrUser_session_require_login();
            }
        }

        // See if we have any signup quotas
        $_data['show_signup'] = 'no';
        if (isset($_conf['jrUser_signup_on']) && $_conf['jrUser_signup_on'] == 'on') {
            $_qt = jrProfile_get_signup_quotas();
            if ($_qt && is_array($_qt) && count($_qt) > 0) {
                $_data['show_signup'] = 'yes';
            }
        }
        jrCore_page_title($_conf['jrCore_system_name']);
        $out = jrCore_parse_template('meta.tpl', array());
        $out .= jrCore_parse_template('index.tpl', $_data, 'jrUser');
        jrCore_db_close();
        header('Connection: close');
        header("Content-Type: text/html; charset=utf-8");
        header('Content-Length: ' . strlen($out));
        echo $out;
        exit;
    }
    return $_data;
}

/**
 * Don't show index.tpl as module index
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrUser_template_variables_listener($_data, $_user, $_conf, $_args, $event)
{
    // Random Session Cleanup
    if (isset($_data['module']) && $_data['module'] == 'jrUser' && isset($_data['jr_template']) && $_data['jr_template'] == 'index.tpl' && !jrCore_get_flag('jruser_show_index')) {
        jrCore_page_not_found();
    }
    return $_data;
}

/**
 * Keep session table clean
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrUser_minute_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_conf;

    // Session cleanup - Max 15 minutes for bots
    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "SELECT session_id AS s FROM {$tbl} WHERE session_user_name LIKE 'bot:' AND session_updated < (UNIX_TIMESTAMP() - 900)";
    $_rt = jrCore_db_query($req, 's', false, 's');
    if ($_rt && is_array($_rt)) {
        $req = "DELETE FROM {$tbl} WHERE session_id IN('" . implode("','", $_rt) . "')";
        jrCore_db_query($req);
    }

    // Delete non-logged in user sessions once form expiration has hit
    $hrs = 4;
    if (isset($_conf['jrCore_form_session_expire_hours']) && jrCore_checktype($_conf['jrCore_form_session_expire_hours'], 'number_nz')) {
        $hrs = (int) $_conf['jrCore_form_session_expire_hours'];
    }
    $req = "SELECT session_id AS s FROM {$tbl} WHERE session_user_id = 0 AND session_updated < (UNIX_TIMESTAMP() - ({$hrs} * 3600))";
    $_rt = jrCore_db_query($req, 's', false, 's');
    if ($_rt && is_array($_rt)) {
        $req = "DELETE FROM {$tbl} WHERE session_id IN('" . implode("','", $_rt) . "')";
        jrCore_db_query($req);
    }

    // session_expire_min for all logged in users
    $req = "SELECT session_id AS s FROM {$tbl} WHERE session_updated < (UNIX_TIMESTAMP() - ({$_conf['jrUser_session_expire_min']} * 60))";
    $_rt = jrCore_db_query($req, 's', false, 's');
    if ($_rt && is_array($_rt)) {
        $req = "DELETE FROM {$tbl} WHERE session_id IN('" . implode("','", $_rt) . "')";
        jrCore_db_query($req);
    }

    return $_data;
}

/**
 * Rewrite non-SSL URLs to SSL
 * @param $_data mixed information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrUser_view_results_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrUser_is_logged_in() && isset($_conf['jrUser_force_ssl']) && $_conf['jrUser_force_ssl'] == 'on' && (strpos($_conf['jrCore_base_url'], 'https:') === 0 || !stripos($_conf['jrCore_base_url'], $_SERVER['HTTP_HOST']))) {
        // See if there are NON-SSL local URLS embedded in our SSL content
        $url = str_replace('https://', 'http://', $_conf['jrCore_base_url']);
        if (strpos($_data, $url)) {
            // OK - there is a non-SSL local URL in our content - we need to replace
            // it everywhere EXCEPT inside any code bbcode blocks
            if (strpos($_data, 'CodeMirror.fromTextArea')) {
                // We have code blocks
                $_new = array();
                foreach (explode("\n", $_data) as $line) {
                    if (strpos(' ' . $line, 'CodeMirror.fromTextArea')) {
                        // Don't mess with it
                        $_new[] = $line;
                    }
                    else {
                        $_new[] = str_replace($url, str_replace('http://', 'https://', $_conf['jrCore_base_url']), $line);
                    }
                }
                $_data = implode("\n", $_new);
                unset($_new);
            }
            else {
                $_data = str_replace($url, str_replace('http://', 'https://', $_conf['jrCore_base_url']), $_data);
            }
        }
    }
    return $_data;
}

/**
 * Keeps remember me cookie entries cleaned up
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrUser_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    // Old Remember Me cookies
    if (!isset($_conf['jrUser_autologin']) || !jrCore_checktype($_conf['jrUser_autologin'], 'number_nz')) {
        $_conf['jrUser_autologin'] = 2;
    }
    switch ($_conf['jrUser_autologin']) {
        case '1':
            $old = 0;
            break;
        case '2':
            $old = 14;
            break;
        case '3':
            $old = 10000;
            break;
        default:
            $old = (int) $_conf['jrUser_autologin'];
            break;
    }

    $tbl = jrCore_db_table_name('jrUser', 'cookie');
    $req = "SELECT cookie_id FROM {$tbl} WHERE cookie_time < (UNIX_TIMESTAMP() - ({$old} * 86400))";
    $_id = jrCore_db_query($req, 'cookie_id', false, 'cookie_id');
    if ($_id && is_array($_id)) {
        $req = "DELETE FROM {$tbl} WHERE cookie_id IN(" . implode(',', $_id) . ')';
        jrCore_db_query($req);
    }

    // Old Brute Force entries
    jrCore_clean_temp('jrUser', 7200);

    //---------------------------------
    // clean up user_unsubscribe keys
    //---------------------------------
    $limit = time() - 86400; // 24 hours
    $_sp   = array(
        'search'         => array(
            "_updated < $limit",
            "user_unsubscribe > 0"
        ),
        'limit'          => 50,
        'ignore_pending' => true,
        'privacy_check'  => false,
        'skip_triggers'  => true
    );
    $_ex   = jrCore_db_search_items('jrUser', $_sp);
    if (is_array($_ex) && isset($_ex['_items'])) {
        $_clear = array();
        foreach ($_ex['_items'] as $_u) {
            $_clear[$_u['_user_id']] = array(
                'user_unsubscribe' => ''
            );
        }
        jrCore_db_update_multiple_items('jrUser', $_clear);
    }

    //---------------------------------
    // insert stats
    //---------------------------------

    // Daily active users
    $end = strtotime('midnight', time());
    $beg = strtotime('midnight', (time() - 86400));
    $_sc = array(
        'search'         => array(
            "user_last_login between {$beg},{$end}"
        ),
        'return_count'   => true,
        'skip_triggers'  => true,
        'privacy_check'  => false,
        'ignore_pending' => true,
        'limit'          => 1000000
    );
    $num = jrCore_db_search_items('jrUser', $_sc);
    jrUser_save_daily_stat('daily_active_users', $num);

    // Monthly Active Users
    $beg = strtotime('midnight', (time() - (30 * 86400)));
    $_sc = array(
        'search'         => array(
            "user_last_login between {$beg},{$end}"
        ),
        'return_count'   => true,
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'privacy_check'  => false,
        'limit'          => 1000000
    );
    $num = jrCore_db_search_items('jrUser', $_sc);
    jrUser_save_daily_stat('monthly_active_users', $num);

    return $_data;
}

/**
 * Make sure our signup form fields are always required
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrUser_signup_activated_listener($_data, $_user, $_conf, $_args, $event)
{
    // Get profile info for the user being activated
    $_pr = jrCore_db_get_item('jrProfile', $_data['_profile_id']);
    if (jrCore_module_is_active('jrAction') && isset($_pr['quota_jrUser_add_to_timeline']) && $_pr['quota_jrUser_add_to_timeline'] == 'on') {
        // Add to Actions...
        $time  = time();
        $_tmp  = array(
            'action_pending' => 0,
            'action_item_id' => $_data['_user_id'],
            'action_module'  => 'jrUser',
            'action_mode'    => 'signup'
        );
        $_core = array(
            '_profile_id' => $_data['_profile_id'],
            '_user_id'    => $_data['_user_id'],
            '_created'    => $time,
            '_updated'    => $time,
        );
        jrCore_db_create_item('jrAction', $_tmp, $_core);
    }

    // We have a new account - notify admins
    if (isset($_conf['jrUser_signup_notify']) && $_conf['jrUser_signup_notify'] == 'on') {

        // Get profile info for the user being activated
        if ($_pr && is_array($_pr)) {
            $_qt = jrProfile_get_quota($_pr['profile_quota_id']);
            if ($_qt && is_array($_qt) && isset($_qt['quota_jrUser_signup_method']) && $_qt['quota_jrUser_signup_method'] == 'admin') {
                // We do NOT need to notify the admins again about this new user - since
                // they just manually validated this user account
                return $_data;
            }
        }

        // Fall through - notify admins of new signup
        $_ad = jrUser_get_admin_user_ids();
        if (is_array($_ad)) {
            $_rp                    = $_data;
            $_rp['system_name']     = $_conf['jrCore_system_name'];
            $_rp['ip_address']      = jrCore_get_ip();
            $new_profile_url        = jrCore_db_get_item_key('jrProfile', $_rp['_profile_id'], 'profile_url');
            $_rp['new_profile_url'] = "{$_conf['jrCore_base_url']}/" . rawurldecode($new_profile_url);
            list($sub, $msg) = jrCore_parse_email_templates('jrUser', 'notify_signup', $_rp);
            foreach ($_ad as $uid) {
                jrUser_notify($uid, 0, 'jrUser', 'signup_notify', $sub, $msg);
            }
        }
    }
    return $_data;
}

/**
 * Make sure our signup form fields are always required
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrUser_form_field_create_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['form_name'] == 'jrUser_signup' && isset($_data['name'])) {
        switch ($_data['name']) {

            // Force these to be required so the site owner can't accidently let them be optional
            case 'user_name':
            case 'user_email':
            case 'user_passwd1':
            case 'user_passwd2':
                $_data['required'] = true;
                break;

            // Our quota_id field is a DYNAMIC field - if there is only 1 signup quota_id
            // then we switch the field type to "hidden" from select
            case 'quota_id':
                $_qt = jrProfile_get_signup_quotas();
                if ($_qt && count($_qt) === 1) {
                    $_qt   = array_keys($_qt);
                    $_data = array(
                        'name'  => 'quota_id',
                        'type'  => 'hidden',
                        'value' => reset($_qt)
                    );
                }
                break;

        }
    }
    return $_data;
}

/**
 * Adds support for "user_id" parameter to jrCore_list
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrUser_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    // user_id=(id)[,id][,id][,..]
    if (isset($_data['user_id'])) {
        if (jrCore_checktype($_data['user_id'], 'number_nz')) {
            if (!isset($_data['search'])) {
                $_data['search'] = array();
            }
            $_data['search'][] = "_user_id = " . intval($_data['user_id']);
        }
        elseif (strpos($_data['user_id'], ',')) {
            $_tmp = explode(',', $_data['user_id']);
            if ($_tmp && is_array($_tmp)) {
                $_pi = array();
                foreach ($_tmp as $pid) {
                    if (is_numeric($pid)) {
                        $_pi[] = (int) $pid;
                    }
                }
                if ($_pi && is_array($_pi) && count($_pi) > 0) {
                    if (!isset($_data['search'])) {
                        $_data['search'] = array();
                    }
                    $_data['search'][] = "_user_id in " . implode(',', $_pi);
                }
            }
        }
    }
    return $_data;
}

/**
 * Add user info to return DS items
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrUser_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] != 'jrUser' && $_args['module'] != 'jrProfile' && isset($_data['_items'][0]) && isset($_data['_items'][0]['_user_id'])) {

        // See if we do NOT include User keys in our results
        if (isset($_args['exclude_jrUser_keys']) && $_args['exclude_jrUser_keys'] === true) {
            return $_data;
        }

        // See if only specific keys are being requested - if none of them are user keys
        // then we do not need to go back to the DB to get any user info
        if (isset($_args['return_keys']) && is_array($_args['return_keys']) && count($_args['return_keys']) > 0) {
            $found = false;
            foreach ($_args['return_keys'] as $key) {
                if (strpos($key, 'user_') === 0) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                return $_data;
            }
            unset($found);
        }

        // Add User keys into the data
        $_us = array();
        foreach ($_data['_items'] as $v) {
            if (isset($v['_user_id']) && jrCore_checktype($v['_user_id'], 'number_nz') && !isset($v['user_group'])) {
                $_us[] = (int) $v['_user_id'];
            }
        }
        if ($_us && is_array($_us) && count($_us) > 0) {
            $_rt = jrCore_db_get_multiple_items('jrUser', $_us);
            if ($_rt && is_array($_rt)) {
                // We've found user info - go though and setup by _user_id
                $_pr = array();
                $_up = array();
                foreach ($_rt as $v) {
                    $_pr["{$v['_user_id']}"] = $v;
                    $_up["{$v['_user_id']}"] = array($v['_created'], $v['_updated']);
                    unset($_pr["{$v['_user_id']}"]['_created']);
                    unset($_pr["{$v['_user_id']}"]['_updated']);
                    unset($_pr["{$v['_user_id']}"]['_item_id']);
                    unset($_pr["{$v['_user_id']}"]['_profile_id']);
                }
                // Add to results
                foreach ($_data['_items'] as $k => $v) {
                    if (isset($_pr["{$v['_user_id']}"]) && is_array($_pr["{$v['_user_id']}"])) {
                        $_data['_items'][$k] = array_merge($v, $_pr["{$v['_user_id']}"]);
                        unset($_data['_items'][$k]['user_password']);
                        $_data['_items'][$k]['user_created'] = $_up["{$v['_user_id']}"][0];
                        $_data['_items'][$k]['user_updated'] = $_up["{$v['_user_id']}"][1];
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * login_success listener to optionally add it to the timeline
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrUser_login_success_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_module_is_active('jrAction') && isset($_data['quota_jrUser_add_to_timeline']) && $_data['quota_jrUser_add_to_timeline'] == 'on') {
        // Add to Actions...
        jrCore_run_module_function('jrAction_save', 'login', 'jrUser', $_data['_user_id'], $_data);
    }
    return $_data;
}

//------------------------------------
// USER FUNCTIONS
//------------------------------------

/**
 * Get a hash for a password
 * @param string $password
 * @return bool|string
 */
function jrUser_get_password_hash($password)
{
    if (!class_exists('PasswordHash')) {
        require_once APP_DIR . '/modules/jrUser/contrib/phpass/PasswordHash.php';
    }
    $iter = jrCore_get_advanced_setting('jrUser', 'password_iterations', 12);
    $hash = new PasswordHash($iter, false);
    return $hash->HashPassword($password);
}

/**
 * Verify a given string matches the hashed password
 * @param string $string String to check if it is correct
 * @param string $password_hash Existing password hash to check against
 * @return bool
 */
function jrUser_verify_password_hash($string, $password_hash)
{
    if (!class_exists('PasswordHash')) {
        require_once APP_DIR . '/modules/jrUser/contrib/phpass/PasswordHash.php';
    }
    $iter = jrCore_get_advanced_setting('jrUser', 'password_iterations', 12);
    $hash = new PasswordHash($iter, false);
    return $hash->CheckPassword($string, $password_hash);
}

/**
 * Delete entries in the forgot table for a user_id
 * @param int $user_id
 * @return mixed
 */
function jrUser_delete_forgot_password_entries($user_id)
{
    $uid = (int) $user_id;
    $tbl = jrCore_db_table_name('jrUser', 'forgot');
    $req = "DELETE FROM {$tbl} WHERE forgot_user_id = {$uid}";
    return jrCore_db_query($req);
}

/**
 * Suppress an email address so we don't send to it
 * @param string $email
 * @return mixed
 */
function jrUser_suppress_email_address($email)
{
    $eml = jrCore_db_escape($email);
    $tbl = jrCore_db_table_name('jrUser', 'suppressed');
    $req = "INSERT IGNORE INTO {$tbl} (email_address) VALUES ('{$eml}')";
    return jrCore_db_query($req, 'COUNT');
}

/**
 * Check if this is a new device for a user and notify
 * @param $user_id int User ID
 * @return mixed
 */
function jrUser_notify_if_new_device($user_id)
{
    $uid = (int) $user_id;
    $tbl = jrCore_db_table_name('jrUser', 'device');
    if ($did = jrCore_get_cookie('jruser_device')) {
        // See if the device ID is valid
        $req = "SELECT notified FROM {$tbl} WHERE device_id = '" . jrCore_db_escape($did) . "' AND user_id = '{$uid}' LIMIT 1";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if ($_rt && is_array($_rt) && isset($_rt['notified']) && $_rt['notified'] == '1') {
            // We are not new...
            jrCore_set_cookie('jruser_device', $did, 365);
            return false;
        }
    }
    // If this is the FIRST time a user is in the device list,
    // save the device but don't send them any email
    $did = md5(microtime());
    $uip = jrCore_db_escape(jrCore_get_ip());
    $req = "SELECT user_id FROM {$tbl} WHERE user_id = '{$uid}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        // We already have entries - see if this is a new one
        $req = "INSERT INTO {$tbl} (user_id, device_id, ip_address, notified) VALUES ('{$uid}', '{$did}', '{$uip}', 1) ON DUPLICATE KEY UPDATE notified = 1";
        $cnt = jrCore_db_query($req, 'COUNT');
        if ($cnt && $cnt === 1) {

            // It was a NEW device (1 = "inserted", 2 = "updated")
            jrCore_set_cookie('jruser_device', $did, 365);

            // Send out notification email
            $_us = jrCore_db_get_item('jrUser', $uid);
            if ($_us && is_array($_us) && isset($_us['user_email'])) {
                list($sub, $msg) = jrCore_parse_email_templates('jrUser', 'new_device', $_us);
                jrCore_send_email($_us['user_email'], $sub, $msg);
            }
            return true;
        }
    }
    else {
        // New
        $req = "INSERT INTO {$tbl} (user_id, device_id, ip_address, notified) VALUES ('{$uid}', '{$did}', '{$uip}', 1)";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (!$cnt || $cnt !== 1) {
            jrCore_logger('MAJ', "unable to save unique device ID to device table - check debug log");
        }
    }
    jrCore_set_cookie('jruser_device', $did, 365);
    return false;
}

/**
 * Return an array of admin/master user id's
 */
function jrUser_get_admin_user_ids()
{
    $key = 'jruser_get_admin_user_ids';
    if (!$_us = jrCore_get_flag($key)) {
        if ($_tm = jrCore_db_get_multiple_items_by_key('jrUser', 'user_group', array('master', 'admin'), true)) {
            $_us = array();
            foreach ($_tm as $id) {
                $_us[$id] = $id;
            }
        }
    }
    if ($_us && is_array($_us)) {
        return $_us;
    }
    return false;
}

/**
 * Return an array of master user id's
 */
function jrUser_get_master_user_ids()
{
    $key = 'jruser_get_master_user_ids';
    if (!$_us = jrCore_get_flag($key)) {
        if ($_tm = jrCore_db_get_multiple_items_by_key('jrUser', 'user_group', 'master', true)) {
            $_us = array();
            foreach ($_tm as $id) {
                $_us[$id] = $id;
            }
        }
    }
    if ($_us && is_array($_us)) {
        return $_us;
    }
    return false;
}

/**
 * Returns true if viewing user is linked to the profile_id
 * Master/Admin users will return false!
 * @param $profile_id integer Profile ID to check
 * @return bool
 */
function jrUser_is_linked_to_profile($profile_id)
{
    // validate id
    if (!jrCore_checktype($profile_id, 'number_nz')) {
        return false;
    }
    // User can always see their home profile
    if ($profile_id == jrUser_get_profile_home_key('_profile_id')) {
        return true;
    }
    if (isset($_SESSION['user_linked_profile_ids']) && in_array($profile_id, explode(',', $_SESSION['user_linked_profile_ids']))) {
        // The viewing user is linked to this profile
        return true;
    }
    return false;
}

/**
 * Pending Users browser
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 * @return bool
 */
function jrUser_dashboard_pending_users($_post, $_user, $_conf)
{
    // get our items
    $_pr = array(
        'search'                       => array(
            'user_validated = 0'
        ),
        'order_by'                     => array(
            '_created' => 'desc'
        ),
        'include_jrProfile_keys'       => true,
        'include_jrProfile_quota_keys' => true,
        'ignore_pending'               => true,
        'no_cache'                     => true,
        'privacy_check'                => false
    );
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_pr['search'][] = "user_name like {$_post['search_string']} || user_email LIKE {$_post['search_string']}";
    }
    $_us = jrCore_db_search_items('jrUser', $_pr);

    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard/pending/m=jrUser");

    // Start our output
    $dat             = array();
    $dat[1]['title'] = 'id';
    $dat[1]['width'] = '5%';
    $dat[2]['title'] = 'user name';
    $dat[2]['width'] = '35%';
    $dat[3]['title'] = 'email';
    $dat[3]['width'] = '30%';
    $dat[4]['title'] = 'joined';
    $dat[4]['width'] = '10%';
    $dat[5]['title'] = 'activate';
    $dat[5]['width'] = '5%';
    $dat[6]['title'] = 'resend';
    $dat[6]['width'] = '5%';
    $dat[7]['title'] = 'modify';
    $dat[7]['width'] = '5%';
    $dat[8]['title'] = 'delete';
    $dat[8]['width'] = '5%';
    jrCore_page_table_header($dat);

    if (is_array($_us['_items'])) {
        $uurl = jrCore_get_module_url('jrUser');
        $purl = jrCore_get_module_url('jrProfile');
        foreach ($_us['_items'] as $k => $_usr) {
            $dat             = array();
            $dat[1]['title'] = $_usr['_user_id'];
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $_usr['user_name'];
            $dat[3]['title'] = $_usr['user_email'];
            $dat[4]['title'] = jrCore_format_time($_usr['_created']);
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrCore_page_button("a{$k}", 'activate', "if (confirm('Activate this User Account and send them an email?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$uurl}/user_activate/user_id={$_usr['_user_id']}') }");
            if (isset($_usr['quota_jrUser_signup_method']) && $_usr['quota_jrUser_signup_method'] == 'admin') {
                $dat[6]['title'] = jrCore_page_button("r{$k}", 'resend', 'disabled');
            }
            else {
                $dat[6]['title'] = jrCore_page_button("r{$k}", 'resend', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$uurl}/user_resend/user_id={$_usr['_user_id']}')");
            }
            $dat[7]['title'] = jrCore_page_button("m{$k}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$uurl}/account/user_id={$_usr['_user_id']}')");
            $dat[8]['title'] = jrCore_page_button("d{$k}", 'delete', "if(confirm('Are you sure you want to delete this User Account? This will also deleted the Profile associated with this account.')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$purl}/delete_save/id={$_usr['_profile_id']}') }");
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>There are no pending user accounts at this time</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    return true;
}

/**
 * Custom Data Store browser tool
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 * @return bool
 */
function jrUser_data_browser($_post, $_user, $_conf)
{
    $order_dir = 'desc';
    $order_opp = 'asc';
    if (isset($_post['order_dir']) && ($_post['order_dir'] == 'asc' || $_post['order_dir'] == 'numerical_asc')) {
        $order_dir = 'asc';
        $order_opp = 'desc';
    }

    $order_by = '_created';
    if (isset($_post['order_by'])) {
        switch ($_post['order_by']) {
            case '_item_id':
                $order_dir = 'numerical_' . $order_dir;
                $order_opp = 'numerical_' . $order_opp;
                $order_by  = $_post['order_by'];
                break;
            case 'user_last_login':
            case 'user_name':
            case 'user_email':
                $order_by = $_post['order_by'];
                break;
        }
    }

    // get our items
    $_pr = array(
        'pagebreak'      => (isset($_COOKIE['jrcore_pager_rows']) && jrCore_checktype($_COOKIE['jrcore_pager_rows'], 'number_nz')) ? (int) $_COOKIE['jrcore_pager_rows'] : 12,
        'page'           => 1,
        'order_by'       => array($order_by => $order_dir),
        'return_keys'    => array('_created', '_item_id', '_user_id', '_profile_id', 'user_name', 'user_group', 'user_image_time', 'user_email', 'user_last_login', 'user_active', 'user_blocked'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true,
        'quota_check'    => false,
        'privacy_check'  => false
    );
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $_pr['page'] = (int) $_post['p'];
    }
    // See we have a search condition
    $_ex = false;
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_ex = array('search_string' => $_post['search_string']);
        // Check for passing in a specific key name for search
        if (strpos($_post['search_string'], ':')) {
            list($sf, $ss) = explode(':', $_post['search_string'], 2);
            $_post['search_string'] = $ss;
            if (is_numeric($ss)) {
                $_pr['search'] = array("{$sf} = {$ss}");
            }
            else {
                $_pr['search'] = array("{$sf} like {$ss}%");
            }
        }
        else {
            $_pr['search'] = array("% like %{$_post['search_string']}%");
        }
    }
    $_us = jrCore_db_search_items('jrUser', $_pr);

    // Start our output
    $url             = $_conf['jrCore_base_url'] . jrCore_strip_url_params($_post['_uri'], array('order_by', 'order_dir'));
    $dat             = array();
    $dat[1]['title'] = '&nbsp;';
    $dat[1]['width'] = '2%';
    $dat[2]['title'] = 'ID';
    $dat[2]['width'] = '2%';
    $dat[3]['title'] = '<a href="' . $url . '/order_by=user_name/order_dir=' . $order_opp . '">user name</a>';
    $dat[3]['width'] = '23%';
    $dat[4]['title'] = 'linked profile(s)';
    $dat[4]['width'] = '23%';
    $dat[5]['title'] = '<a href="' . $url . '/order_by=user_email/order_dir=' . $order_opp . '">email</a>';
    $dat[5]['width'] = '23%';
    $dat[6]['title'] = '<a href="' . $url . '/order_by=user_last_login/order_dir=' . $order_opp . '">last login</a>';
    $dat[6]['width'] = '12%';
    $dat[7]['title'] = 'modify';
    $dat[7]['width'] = '5%';
    $dat[8]['title'] = 'delete';
    $dat[8]['width'] = '5%';
    $dat[9]['title'] = 'active';
    $dat[9]['width'] = '5%';

    jrCore_page_table_header($dat);

    if (isset($_us['_items']) && is_array($_us['_items'])) {

        // Get profile info for these users
        $_pn = array();
        $_ui = array();
        foreach ($_us['_items'] as $_tmp) {
            $_ui[]                   = (int) $_tmp['_user_id'];
            $_usr[$_tmp['_user_id']] = $_tmp;
        }
        $tbl = jrCore_db_table_name('jrProfile', 'profile_link');
        $req = "SELECT * FROM {$tbl} WHERE user_id IN(" . implode(',', $_ui) . ") ORDER BY profile_id ASC";
        $_ui = jrCore_db_query($req, 'NUMERIC');
        if ($_ui && is_array($_ui)) {

            $_id = array();
            foreach ($_ui as $v) {
                $_id["{$v['profile_id']}"] = $v['profile_id'];
            }

            // get profiles
            $_pr = array(
                'search'         => array(
                    '_profile_id in ' . implode(',', $_id)
                ),
                'return_keys'    => array('_profile_id', 'profile_name', 'profile_url'),
                'skip_triggers'  => true,
                'ignore_pending' => true,
                'privacy_check'  => false,
                'no_cache'       => true,
                'limit'          => 250
            );
            $_pi = jrCore_db_search_items('jrProfile', $_pr);
            if (isset($_pi['_items']) && is_array($_pi['_items'])) {
                $_pn = array();
                $_pr = array();
                foreach ($_pi['_items'] as $_profile) {
                    $_pr["{$_profile['_profile_id']}"] = $_profile;
                }
                foreach ($_ui as $_link) {
                    if (isset($_pr["{$_link['profile_id']}"])) {
                        if (!isset($_pn["{$_link['user_id']}"])) {
                            $_pn["{$_link['user_id']}"] = array();
                        }
                        $_pn["{$_link['user_id']}"][] = "<a href=\"{$_conf['jrCore_base_url']}/{$_pr["{$_link['profile_id']}"]['profile_url']}\">@" . rawurldecode($_pr["{$_link['profile_id']}"]['profile_url']) . "</a>";
                    }
                }
            }
            unset($_pi);
        }

        foreach ($_us['_items'] as $k => $_usr) {
            $dat  = array();
            $lbox = '';
            if (isset($_usr['user_image_time']) && $_usr['user_image_time'] > 0) {
                $lbox = "<a href=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/image/user_image/{$_usr['_user_id']}/xxlarge/_v={$_usr['user_image_time']}\" data-lightbox=\"user-images\" title=\"{$_usr['user_name']}\">";
            }
            $_im             = array(
                'crop'   => 'auto',
                'width'  => 32,
                'height' => 32,
                'alt'    => 'img',
                'title'  => 'img',
                '_v'     => (isset($_usr['user_image_time']) && $_usr['user_image_time'] > 0) ? $_usr['user_image_time'] : false
            );
            $dat[1]['title'] = $lbox . jrImage_get_image_src('jrUser', 'user_image', $_usr['_user_id'], 'xsmall', $_im);
            $dat[2]['title'] = $_usr['_user_id'];
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = '<h3>' . $_usr['user_name'] . '</h3>';
            $dat[3]['class'] = 'word-break';
            $dat[4]['title'] = (isset($_pn["{$_usr['_user_id']}"])) ? implode('<br>', $_pn["{$_usr['_user_id']}"]) : 'Profile Not Found';
            $dat[4]['class'] = 'center word-break';

            if (strpos($_usr['user_email'], '@')) {
                $dat[5]['title'] = $_usr['user_email'];
                $dat[5]['class'] = 'center word-break';
            }
            else {
                $dat[5]['title'] = 'No Email Address';
                $dat[5]['class'] = 'center word-break error';
            }
            $cls = '';
            if (isset($_usr['user_blocked']) && $_usr['user_blocked'] == '1') {
                $dat[6]['title'] = 'BLOCKED';
                $cls             = ' error';
            }
            elseif (isset($_usr['user_active']) && $_usr['user_active'] != '1') {
                if (isset($_usr['user_last_login']) && $_usr['user_last_login'] > 0) {
                    $dat[6]['title'] = 'INACTIVE';
                }
                else {
                    $dat[6]['title'] = 'PENDING';
                    $cls             = ' notice';
                }
            }
            else {
                $dat[6]['title'] = (isset($_usr['user_last_login']) && $_usr['user_last_login'] > 0) ? jrCore_format_time($_usr['user_last_login']) : '-';
                $dat[6]['class'] = 'center';
            }
            $dat[6]['class'] = "center{$cls}";
            $dat[7]['title'] = jrCore_page_button("u-modify-{$k}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/account/user_id={$_usr['_user_id']}')");
            $dat[7]['class'] = "center{$cls}";
            if (!jrUser_is_master() && ($_usr['user_group'] == 'admin' || $_usr['user_group'] == 'master')) {
                $dat[8]['title'] = jrCore_page_button("u-delete-{$k}", 'delete', 'disabled');
                if (isset($_usr['user_blocked']) && $_usr['user_blocked'] == '1') {
                    $dat[9]['title'] = jrCore_page_button("u-block-{$k}", 'allow', 'disabled');
                }
                else {
                    $dat[9]['title'] = jrCore_page_button("u-block-{$k}", 'block', 'disabled');
                }
            }
            else {
                $dat[8]['title'] = jrCore_page_button("u-delete-{$k}", 'delete', "jrUser_delete_user({$_usr['_user_id']},{$_usr['_profile_id']})");

                if (isset($_usr['user_blocked']) && $_usr['user_blocked'] == '1') {
                    $dat[9]['title'] = jrCore_page_button("u-block-{$k}", 'allow', "if(confirm('Un-block this User Account?\\n - The User Account AND Profile will be made active\\n - The user will be able to log in to the site')){jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/block_save/mode=u/id={$_usr['_user_id']}')}");
                }
                else {
                    if ($_usr['_user_id'] == $_user['_user_id']) {
                        $dat[9]['title'] = jrCore_page_button("u-block-{$k}", 'block', 'disabled');
                    }
                    else {
                        $dat[9]['title'] = jrCore_page_button("u-block-{$k}", 'block', "if(confirm('Are you sure you want to BLOCK this User Account?\\n - The User Account AND Profile will be made inactive\\n - If the user is logged in they will be logged out')){jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/block_save/mode=b/id={$_usr['_user_id']}')}");
                    }
                }
            }
            $dat[8]['class'] = "center{$cls}";
            $dat[9]['class'] = "center{$cls}";
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_us, $_ex);
    }
    else {
        $dat = array();
        if (isset($_post['search_string'])) {
            $dat[1]['title'] = '<p>No Results found for your Search Criteria.</p>';
        }
        else {
            $dat[1]['title'] = '<p>No User Accounts found!</p>';
        }
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    // Delete modal
    $html = jrCore_parse_template('user_delete_modal.tpl', $_post, 'jrUser');
    jrCore_page_custom($html);

    return true;
}

/**
 * Creates tab bar in User Account section
 * @param string $active active tab
 * @param array $_active_user - Active User info array
 * @return bool
 */
function jrUser_account_tabs($active = 'account', $_active_user = null)
{
    global $_conf, $_post, $_user;
    if (!is_null($_active_user) && is_array($_active_user)) {
        // We've been given the info to use
        $pid = $_active_user['_profile_id'];
        $uid = $_active_user['_user_id'];
        $_pr = $_active_user;
    }
    else {
        // Default to viewing user's info
        $pid = $_user['_profile_id'];
        $uid = $_user['_user_id'];
        $_pr = $_user;
        if (jrUser_is_admin()) {
            if (isset($_post['profile_id']) && jrCore_checktype($_post['profile_id'], 'number_nz')) {
                $pid = (int) $_post['profile_id'];
                if ($pid != $_user['_profile_id']) {
                    $_pr = jrCore_db_get_item('jrProfile', $pid, true);
                }
            }
            if (isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz')) {
                $uid = (int) $_post['user_id'];
            }
        }
        elseif (jrUser_is_power_user() || jrUser_is_multi_user()) {
            if (isset($_post['profile_id']) && jrCore_checktype($_post['profile_id'], 'number_nz')) {
                if (jrProfile_is_profile_owner($_post['profile_id'])) {
                    $pid = (int) $_post['profile_id'];
                    if ($pid != $_user['_profile_id']) {
                        $_pr = jrCore_db_get_item('jrProfile', $pid, true);
                    }
                }
            }
        }
    }
    $_tbs = array();
    // Check for registered user tabs
    $_tmp = jrCore_get_registered_module_features('jrUser', 'account_tab');
    if ($_tmp && is_array($_tmp)) {

        // For ADMIN users, we always show the user tabs IF there is a user
        // account who's HOME PROFILE is this profile
        if (jrUser_is_admin()) {
            if ($pid != jrUser_get_profile_home_key('_profile_id')) {
                // We are NOT on the admin's home profile - see if profile has a home user
                $_ex = jrCore_db_get_item_by_key('jrUser', '_profile_id', $pid, true);
                if (!$_ex || !is_array($_ex)) {
                    // No user account for this profile
                    unset($_tmp['jrUser']);
                }
            }
        }
        // If this is a POWER USER, modifying a profile that they control that
        // has no attached USER ACCOUNT, then we want to disable the USER TABS
        // while the power user is in this profile.  The USER TABS should only
        // show when the Power user is modifying their HOME PROFILE
        elseif ((jrUser_is_power_user() || jrUser_is_multi_user()) && $pid != jrUser_get_profile_home_key('_profile_id')) {
            unset($_tmp['jrUser']);
        }

        // Make sure tabs from Profile and User modules load up first
        $_tm2 = array();
        if (isset($_tmp['jrProfile'])) {
            $_tm2['jrProfile'] = $_tmp['jrProfile'];
            unset($_tmp['jrProfile']);
        }
        if (isset($_tmp['jrUser'])) {
            $_tm2['jrUser'] = $_tmp['jrUser'];
            unset($_tmp['jrUser']);
        }
        $_tmp = array_merge($_tm2, $_tmp);

        $_lng = jrUser_load_lang_strings();
        foreach ($_tmp as $mod => $_entries) {

            $url = jrCore_get_module_url($mod);
            foreach ($_entries as $view => $label) {

                // $label can come in as an array
                if (is_array($label)) {
                    $_tbs["{$mod}/{$view}"] = array(
                        'label' => (isset($_lng[$mod]["{$label['label']}"])) ? $_lng[$mod]["{$label['label']}"] : $label['label'],
                        'url'   => "{$_conf['jrCore_base_url']}/{$url}/{$view}/profile_id={$pid}/user_id={$uid}"
                    );
                    if (!isset($label['quota_check']) || $label['quota_check'] === true) {
                        // Check for specific field access
                        $fld = "quota_{$mod}_allowed";
                        if (isset($label['field']) && strlen($label['field']) > 0) {
                            $fld = $label['field'];
                        }
                        if (isset($_pr[$fld]) && $_pr[$fld] != 'on') {
                            unset($_tbs["{$mod}/{$view}"]);
                            continue;
                        }
                    }
                }
                else {
                    // Make sure the viewing user has Quota access to this module
                    if ($mod == 'jrUser' || $mod == 'jrProfile') {
                        $allowed = 'on';
                    }
                    else {
                        if (jrUser_is_admin() || jrUser_is_power_user() || jrUser_is_multi_user()) {
                            $allowed = (isset($_pr["quota_{$mod}_allowed"])) ? $_pr["quota_{$mod}_allowed"] : 'off';
                        }
                        else {
                            $allowed = jrUser_get_profile_home_key("quota_{$mod}_allowed");
                        }
                    }
                    if ($allowed && $allowed != 'on') {
                        continue;
                    }
                    $_tbs["{$mod}/{$view}"] = array(
                        'label' => (isset($_lng[$mod][$label])) ? $_lng[$mod][$label] : $label,
                        'url'   => "{$_conf['jrCore_base_url']}/{$url}/{$view}/profile_id={$pid}/user_id={$uid}"
                    );
                }
                if ((isset($_post['module']) && $_post['module'] == $mod && isset($_post['option']) && $_post['option'] == $view) || $active == $view) {
                    $_tbs["{$mod}/{$view}"]['active'] = true;
                }
            }
        }
    }
    $_tbs = jrCore_trigger_event('jrUser', 'account_tabs', $_tbs, array('pid' => $pid, 'uid' => $uid));
    jrCore_page_tab_bar($_tbs);
    return true;
}

/**
 * Register a setting to be shown in the User Account
 * @param $module string Module registering setting for
 * @param $_field array Array of setting information
 * @return bool
 */
function jrUser_register_setting($module, $_field)
{
    if (!isset($_field['name'])) {
        jrCore_set_form_notice('error', "You must provide a valid field name");
        return false;
    }
    $_tmp = jrCore_get_flag('jruser_register_setting');
    if (!$_tmp) {
        $_tmp = array();
    }
    if (!isset($_tmp[$module])) {
        $_tmp[$module] = array();
    }
    $_field['name']  = "user_{$module}_{$_field['name']}";
    $_tmp[$module][] = $_field;
    jrCore_set_flag('jruser_register_setting', $_tmp);
    return true;
}

/**
 * Notify a User about a specific event
 * @param mixed $to_user_id User ID to send notification to (int or array of int)
 * @param int $from_user_id User ID notification is from
 * @param string $module Module that has registered the notification event
 * @param string $event Event Name
 * @param string $subject Subject of notification
 * @param string $message Message of notification
 * @param array $_options Email Options (optional)
 * @return bool
 */
function jrUser_notify($to_user_id, $from_user_id, $module, $event, $subject, $message, $_options = null)
{
    // Make sure we're not recursive
    if (jrCore_get_flag('jruser_notify_is_running')) {
        return true;
    }
    jrCore_set_flag('jruser_notify_is_running', 1);

    // Make sure module has registered
    $_tmp = jrCore_get_registered_module_features('jrUser', 'notification');
    if (!isset($_tmp[$module][$event])) {
        // Module did not register this event
        jrCore_logger('MAJ', "{$module} has not registered the {$event} notification event - not sending."); // log an error to the activity log
        jrCore_delete_flag('jruser_notify_is_running');
        return false;
    }

    // Get User info
    if (!is_array($to_user_id)) {
        $to_user_id = array($to_user_id);
    }
    // Validate
    foreach ($to_user_id as $k => $uid) {
        if (!jrCore_checktype($uid, 'number_nz')) {
            unset($to_user_id[$k]);
        }
    }
    if (count($to_user_id) === 0) {
        // We came out with nothing
        jrCore_delete_flag('jruser_notify_is_running');
        return false;
    }

    // Get user info
    $_rt = jrCore_db_get_multiple_items('jrUser', $to_user_id);
    if (!$_rt || !is_array($_rt)) {
        jrCore_delete_flag('jruser_notify_is_running');
        return false;
    }

    // Prune
    $key = "user_{$module}_{$event}_notifications";
    foreach ($_rt as $k => $_usr) {

        // Check for valid email
        if (!isset($_usr['user_email']) || !jrCore_checktype($_usr['user_email'], 'email')) {
            unset($_rt[$k]);
            continue;
        }

        // See if this user has disabled ALL notifications
        if (isset($_usr['user_notifications_disabled']) && $_usr['user_notifications_disabled'] == 'on') {
            unset($_rt[$k]);
            continue;
        }

        // See if notifications are enabled for this specific event
        if (isset($_usr[$key]) && $_usr[$key] == 'off') {
            unset($_rt[$k]);
            continue;
        }
        elseif (!isset($_usr[$key]) || (isset($_tmp[$module][$event]['email_only']) && $_tmp[$module][$event]['email_only'] === true)) {
            // Not set OR Forced email on this notification event
            $_rt[$k][$key] = 'email';
        }
    }
    if (count($_rt) === 0) {
        // Came out empty
        jrCore_delete_flag('jruser_notify_is_running');
        return true;
    }

    // notify user trigger
    $_args = array(
        'to_user_id'   => $to_user_id,
        'from_user_id' => $from_user_id,
        'module'       => $module,
        'event'        => $event,
        'subject'      => $subject,
        'message'      => $message,
        'registered'   => $_tmp,
        '_options'     => $_options
    );
    $_rt   = jrCore_trigger_event('jrUser', 'notify_user', $_rt, $_args);

    if (isset($_rt['abort']) && $_rt['abort'] == true) {
        // notification cancelled by a listener
        jrCore_delete_flag('jruser_notify_is_running');
        return true;
    }

    // Add in some options
    if (is_null($_options) || !is_array($_options)) {
        $_options = array();
    }
    $_options['mailing_module'] = $module;
    $_options['mailing_event']  = $event;

    // Process
    if ($_rt && is_array($_rt) && count($_rt) > 0) {

        $_sv = array();
        foreach ($_rt as $k => $_usr) {

            if (!isset($_usr['user_validate']) || strlen($_usr['user_validate']) === 0) {
                $_sv["{$_usr['_user_id']}"] = array(
                    'user_validate' => md5(microtime() . mt_rand(0, 999999))
                );
                $_usr['user_validate']      = $_sv["{$_usr['_user_id']}"]['user_validate'];
            }

            // We're sending an email - make sure we add in our preferences notice
            if (isset($_usr[$key]) && $_usr[$key] == 'email') {
                jrCore_send_email($_usr['user_email'], $subject, $message, $_options, array($_usr['user_email'] => $_usr));
            }
            // Send PN if module enabled
            elseif (jrCore_module_is_active('jrPrivateNote')) {
                jrPrivateNote_send_note($_usr['_user_id'], $from_user_id, $subject, $message);
            }
        }
        if (count($_sv) > 0) {
            jrCore_db_update_multiple_items('jrUser', $_sv);
        }
    }
    jrCore_delete_flag('jruser_notify_is_running');
    return true;
}

/**
 * Check if a User's Profile Quota allows Access to a module
 * @param $module string Module Name
 * @return bool
 */
function jrUser_check_quota_access($module = null)
{
    global $_post, $_user;
    if (is_null($module) || strlen($module) === 0) {
        $module = $_post['module'];
    }
    if (jrUser_is_admin()) {
        // Master and Admin users are not bound by the Quota Access
        // however we want to let them know that access is turned off
        // in case they are not aware of that for a profile
        if (isset($_user["quota_{$module}_allowed"]) && $_user["quota_{$module}_allowed"] != 'on') {
            // Disabled - see if we are on a create form
            if (strpos($_post['option'], 'create') === 0 && !strpos($_post['option'], 'save')) {
                jrCore_set_form_notice('notice', 'Quota access to this module is currently disabled');
            }
        }
        // User has access - check that they are not on a CREATE form and have reached max items
        if (isset($_post['option']) && strpos(' ' . $_post['option'], 'create')) {
            if (isset($_user["quota_{$module}_max_items"]) && jrCore_checktype($_user["quota_{$module}_max_items"], 'number_nz') && isset($_user["profile_{$module}_item_count"]) && $_user["profile_{$module}_item_count"] >= $_user["quota_{$module}_max_items"]) {
                jrCore_set_form_notice('notice', 'This profile has reached the max allowed items of this type');
            }
        }
        return true;
    }
    if (isset($_user["quota_{$module}_allowed"]) && $_user["quota_{$module}_allowed"] != 'on') {
        jrUser_not_authorized();
    }
    // User has access - check that they are not on a CREATE form and have reached max items
    if (isset($_post['option']) && strpos(' ' . $_post['option'], 'create')) {
        if (isset($_user["quota_{$module}_max_items"]) && jrCore_checktype($_user["quota_{$module}_max_items"], 'number_nz')) {
            if ($p_cnt = jrCore_db_get_item_key('jrProfile', $_user['user_active_profile_id'], "profile_{$module}_item_count")) {
                if ($p_cnt >= $_user["quota_{$module}_max_items"]) {
                    jrUser_reset_cache($_user['_user_id']);
                    $_lang = jrUser_load_lang_strings();
                    jrCore_set_form_notice('error', $_lang['jrCore'][70]);
                    jrUser_set_session_key('quota_max_items_reached', true);
                }
            }
        }
    }
    return true;
}

/**
 * View a module's language strings
 * @param $type string "module" or "skin"
 * @param $module string Module or Skin directory name
 * @param $_post array array from jrCore_parseUrl()
 * @param $_user array viewing User info
 * @param $_conf array System Config
 * @return mixed
 */
function jrUser_show_module_lang_strings($type, $module, $_post, $_user, $_conf)
{
    global $_mods;
    $_lang = jrUser_load_lang_strings();

    // Generate our output
    if ($type == 'module') {
        $url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/language";
        jrCore_page_admin_tabs($module, 'language');

        $_mds = array();
        foreach ($_mods as $mod_dir => $_info) {
            if (jrCore_module_is_active($mod_dir) && isset($_lang[$mod_dir])) {
                $_mds[] = $mod_dir;
            }
        }
        $subtitle = jrCore_get_module_jumper('mod_select', $_post['module'], "jrCore_window_location('{$_conf['jrCore_base_url']}/'+ $(this).val() + '/admin/language')", $_mds);

        jrCore_page_banner("Language", $subtitle);

        // See if we are disabled
        if (!jrCore_module_is_active($module)) {
            jrCore_set_form_notice('notice', 'This module is currently disabled');
        }
    }
    else {
        $url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/language/skin={$module}";
        jrCore_page_skin_tabs($module, 'language');

        $murl     = jrCore_get_module_url('jrCore');
        $subtitle = '<select name="skin_jumper" class="form_select form_select_item_jumper" onchange="var v=this.options[this.selectedIndex].value; jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/{$murl}/skin_admin/language/skin='+ v)\">";
        $_tmpm    = jrCore_get_skins();
        foreach ($_tmpm as $skin_dir => $_skin) {
            if (is_dir(APP_DIR . "/skins/{$skin_dir}/lang")) {
                $_mta = jrCore_skin_meta_data($skin_dir);
                $name = (isset($_mta['title'])) ? $_mta['title'] : $skin_dir;
                if ($skin_dir == $_post['skin']) {
                    $subtitle .= '<option value="' . $skin_dir . '" selected> ' . $name . "</option>\n";
                }
                else {
                    $subtitle .= '<option value="' . $skin_dir . '"> ' . $name . "</option>\n";
                }
            }
        }
        $subtitle .= '</select>';
        jrCore_page_banner("Language Strings", $subtitle);
    }
    jrCore_get_form_notice();

    // Get the different languages supported
    $tbl = jrCore_db_table_name('jrUser', 'language');
    $req = "SELECT lang_code FROM {$tbl} WHERE lang_module = '" . jrCore_db_escape($module) . "' GROUP BY lang_code ORDER BY lang_code ASC";
    $_qt = jrCore_db_query($req, 'lang_code', false, 'lang_code');
    if (!isset($_post['lang_code']{0})) {
        if (isset($_conf['jrUser_default_language']{0})) {
            $_post['lang_code'] = $_conf['jrUser_default_language'];
        }
        else {
            $_post['lang_code'] = 'en-US';
        }
    }
    if (!isset($_qt["{$_post['lang_code']}"])) {
        $_post['lang_code'] = 'en-US';
    }
    $url .= "/lang_code={$_post['lang_code']}";
    jrCore_page_search('search', $url);

    // Form init
    if (isset($type) && $type == 'module') {
        $_tmp = array(
            'submit_value' => 'save changes',
            'action'       => 'admin_save/language'
        );
    }
    else {
        $_tmp = array(
            'submit_value' => 'save changes',
            'action'       => "skin_admin_save/language/skin={$module}"
        );
    }
    jrCore_form_create($_tmp);

    // Our "select" jumper for installed languages
    $_tmp = array(
        'name'     => 'lang_code',
        'label'    => 'Language',
        'help'     => false,
        'type'     => 'select',
        'options'  => $_qt,
        'value'    => $_post['lang_code'],
        'onchange' => "var l=this.options[this.selectedIndex].value;self.location='{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/language/lang_code='+ l"
    );
    if (isset($type) && $type == 'skin') {
        $curl             = jrCore_get_module_url('jrCore');
        $_tmp['onchange'] = "var l=this.options[this.selectedIndex].value;self.location='{$_conf['jrCore_base_url']}/{$curl}/skin_admin/language/lang_code='+ l";
    }
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'  => 'p',
        'label' => 'page number',
        'help'  => false,
        'type'  => 'hidden',
        'value' => (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) ? $_post['p'] : 1
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'  => 'lang_code',
        'label' => 'Language',
        'help'  => false,
        'type'  => 'hidden',
        'value' => $_post['lang_code']
    );
    jrCore_form_field_create($_tmp);

    // Get this module's language strings out of the database
    $_ex = false;
    $req = "SELECT * FROM {$tbl} WHERE lang_module = '" . jrCore_db_escape($module) . "' AND lang_code = '" . jrCore_db_escape($_post['lang_code']) . "' ";
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_post['search_string'] = trim(urldecode($_post['search_string']));
        $str                    = jrCore_db_escape($_post['search_string']);
        $req                    .= "AND lang_text LIKE '%{$str}%' ";
        $_ex                    = array('search_string' => $_post['search_string']);
    }
    elseif (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {
        $req .= "AND lang_key = " . intval($_post['id']) . ' ';
    }
    $req .= "ORDER BY (lang_key + 0) ASC";
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $_rt = jrCore_db_paged_query($req, $_post['p'], 12, 'NUMERIC');
    if (isset($_rt['_items']) && is_array($_rt['_items'])) {
        foreach ($_rt['_items'] as $_lstr) {
            if (isset($_lstr['lang_key']) && is_numeric($_lstr['lang_key'])) {
                if (!isset($module_lang_header)) {
                    jrCore_page_section_header("Default Language Strings");
                    $module_lang_header = true;
                }
            }
            else {
                if (!isset($custom_lang_header)) {
                    jrCore_page_section_header("Custom Language Strings");
                    $custom_lang_header = true;
                }
            }
            $lid = "lang_{$_lstr['lang_id']}";
            $err = '';
            if (isset($_SESSION['jr_form_field_highlight'][$lid])) {
                unset($_SESSION['jr_form_field_highlight'][$lid]);
                $err = ' field-hilight';
            }
            $html = '<input type="text" class="form_text lang_input' . $err . '" id="l' . $_lstr['lang_id'] . '" name="lang_' . $_lstr['lang_id'] . '" value="' . jrCore_entity_string($_lstr['lang_text']) . '">';
            if ($_lstr['lang_default'] != $_lstr['lang_text']) {
                $html .= ' <input type="button" class="form_button" value="use default" title="default value: ' . jrCore_entity_string($_lstr['lang_default']) . '" onclick="var v=$(this).val();if (v==\'use default\'){$(\'#l' . $_lstr['lang_id'] . '\').val(\'' . addslashes($_lstr['lang_default']) . '\');$(this).val(\'cancel\');} else {$(\'#l' . $_lstr['lang_id'] . '\').val(\'' . addslashes($_lstr['lang_text']) . '\');$(this).val(\'use default\');}">';
            }
            $_tmp = array(
                'type'     => 'page_link_cell',
                'label'    => $_lstr['lang_key'],
                'url'      => $html,
                'module'   => 'jrCore',
                'template' => 'page_link_cell.tpl'
            );
            jrCore_create_page_element('page', $_tmp);
        }
        jrCore_set_flag('jr_html_page_table_header_colspan', 2);
        jrCore_page_table_pager($_rt, $_ex);
    }
    jrCore_page_set_no_header_or_footer();
    return jrCore_page_display(true);
}

/**
 * Returns an array of active user accounts user_id => $field
 * @param $field string User Field to return - default is "user_name"
 * @return array Returns array of user info
 */
function jrUser_get_users($field = 'user_name')
{
    $_params = array(
        'search'      => array(
            "user_active = 1"
        ),
        'order_by'    => array(
            $field => 'asc'
        ),
        'return_keys' => array(
            '_user_id',
            $field
        ),
        'limit'       => 1000000
    );
    $_rt     = jrCore_db_search_items('jrUser', $_params);
    $_us     = array();
    if (isset($_rt) && is_array($_rt) && isset($_rt['info']['total_items']) && jrCore_checktype($_rt['info']['total_items'], 'number_nz')) {
        foreach ($_rt['_items'] as $_usr) {
            $_us["{$_usr['_user_id']}"] = $_usr[$field];
        }
    }
    return $_us;
}

/**
 * Load a user's active language strings
 * @param $lang string Language to load strings for
 * @param $cache bool set to false to force lang string reload
 * @param $only_active bool set to FALSE to load ALL lang strings (bypass active module and skin check)
 * @return mixed
 */
function jrUser_load_lang_strings($lang = null, $cache = true, $only_active = true)
{
    global $_mods, $_user, $_post, $_conf;

    if ($cache) {
        $_tmp = jrCore_get_flag('jr_lang');
        if ($_tmp) {
            // We've already loaded in this process
            return $_tmp;
        }
    }

    // If we are NOT passed in a specific language, get it
    if (is_null($lang)) {

        // Check for user changing languages
        if (isset($_post['set_user_language']{0})) {
            $lang = basename($_post['set_user_language']);
            // Make sure this is a VALID language
            $_valid = jrUser_get_languages();
            if (isset($_valid[$lang])) {
                // If this user is logged in save preference
                if (jrUser_is_logged_in() && $_user['user_language'] != $lang) {
                    jrCore_db_update_item('jrUser', $_user['_user_id'], array('user_language' => $lang));
                    $_user['user_language'] = $lang;
                    jrUser_session_sync($_user['_user_id']);
                }
                setcookie('jr_lang', $lang, time() + 86400000);
                $_COOKIE['jr_lang'] = $lang;
            }
        }

        // Check for language as set in Account
        elseif (jrUser_is_logged_in() && isset($_user['user_language'])) {
            $lang = $_user['user_language'];
        }

        // Check for cookie
        elseif (isset($_COOKIE['jr_lang']) && strlen($_COOKIE['jr_lang']) > 0) {
            $lang = $_COOKIE['jr_lang'];
        }

        // Check for what browser accepts - don't load for images
        elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']{0}) && jrCore_is_view_request()) {
            // en-US,en;q=0.8
            $_val = jrUser_get_languages();
            $temp = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            if ($temp == 'en') {
                $temp = 'en-US';
            }
            foreach ($_val as $lang_code => $lang_desc) {
                if (strpos($lang_code, $temp) === 0) {
                    $lang = $lang_code;
                    break;
                }
            }
        }

        // Fall through - make sure we use default
        if (is_null($lang) || !isset($lang{0})) {
            $lang = (isset($_conf['jrUser_default_language']{2})) ? $_conf['jrUser_default_language'] : 'en-US';
        }
    }

    // Check for cache
    $ckey = "load_lang_string_{$lang}";
    $_tmp = false;
    if ($cache) {
        $_tmp = jrCore_is_cached('jrUser', $ckey, false);
    }
    if (!$_tmp) {

        // en-US is our default
        $tbl = jrCore_db_table_name('jrUser', 'language');
        if ($lang == 'en-US') {
            $req = "SELECT lang_module AS m, lang_key AS k, lang_text AS t, lang_default AS d FROM {$tbl} WHERE lang_code = 'en-US'";
        }
        else {
            // Get user's language + en-US (for defaults)
            $req = "SELECT lang_module AS m, lang_code AS c, lang_charset AS s, lang_ltr AS l, lang_key AS k, lang_text AS t, lang_default AS d FROM {$tbl} WHERE (lang_code = 'en-US' OR lang_code = '" . jrCore_db_escape($lang) . "')";
        }
        if ($only_active) {
            $req .= " AND lang_module IN('" . implode("','", array_keys($_mods)) . "','{$_conf['jrCore_active_skin']}')";
        }
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if (!$_rt || !is_array($_rt) || !isset($_rt[0]) || !is_array($_rt[0])) {
            jrCore_logger('CRI', "jrUser_load_lang_strings: unable to load any language strings for lang: {$lang}");
            return false;
        }

        // Setup default lang settings
        $_tmp = array(
            '_settings' => array(
                'lang'      => 'en',
                'code'      => 'en-US',
                'charset'   => 'utf-8',
                'direction' => 'ltr'
            )
        );

        foreach ($_rt as $num => $_lang) {
            // Get lang info for this lang
            if (isset($_lang['c']) && $_lang['c'] == $lang && $_tmp['_settings']['code'] == 'en-US') {
                // Looks like we have mixed en-US + other lang - update so the other is primary
                $_tmp['_settings'] = array(
                    'lang'      => strtolower(substr($_lang['c'], 0, 2)),
                    'code'      => $_lang['c'],
                    'charset'   => $_lang['s'],
                    'direction' => $_lang['l']
                );
            }
            if (!isset($_tmp["{$_lang['m']}"]["{$_lang['k']}"])) {
                $_tmp["{$_lang['m']}"]["{$_lang['k']}"] = $_lang['t'];
            }
            elseif (!isset($_lang['c']) || $_lang['c'] != 'en-US') {
                $_tmp["{$_lang['m']}"]["{$_lang['k']}"] = $_lang['t'];
            }
        }
        unset($_rt);

        jrCore_add_to_cache('jrUser', $ckey, $_tmp, 0, 0, false);
    }
    jrCore_set_flag('jr_lang', $_tmp);
    return $_tmp;
}

/**
 * Installs and Updates language strings for modules and skins.
 * @param $type string one of "module" or "skin"
 * @param $dir string Module or Skin Directory
 * @return bool Returns true
 */
function jrUser_install_lang_strings($type, $dir)
{
    $lang_dir = APP_DIR . "/{$type}s/{$dir}/lang";
    if (!is_dir($lang_dir)) {
        // No lang strings
        return false;
    }
    if ($h = opendir($lang_dir)) {
        $_lng = array();
        while (($file = readdir($h)) !== false) {
            if (jrCore_file_extension($file) == 'php') {
                $lang = array();
                $code = str_replace('.php', '', $file);
                include_once "{$lang_dir}/{$file}"; // $lang will be set here if we have strings...
                if (is_array($lang)) {

                    // we do not delete previously entered lang strings
                    $_ins = array();
                    $_upd = array();
                    $_new = array();
                    // Go through and update any defaults, or insert any lang strings that we haven't installed yet
                    $mod = jrCore_db_escape($dir);
                    $cod = jrCore_db_escape($code);
                    $tbl = jrCore_db_table_name('jrUser', 'language');
                    $req = "SELECT * FROM {$tbl} WHERE lang_module = '{$mod}' AND lang_code = '{$cod}'";
                    $_rt = jrCore_db_query($req, 'lang_key');

                    // okay - check if we have existing lang keys for this module - we need to
                    // go through the $_lang array and prune out any entries that
                    // are not being changed or inserted.
                    foreach ($lang as $lid => $lstr) {
                        if (!isset($_rt[$lid])) {
                            // This is a new string - insert
                            $_ins[$lid] = $lstr;
                        }
                        elseif (isset($_rt[$lid]['lang_default']) && $_rt[$lid]['lang_default'] != $lstr) {
                            // See if it has been changed at all...
                            if (isset($_rt[$lid]['lang_text']) && $_rt[$lid]['lang_text'] == $_rt[$lid]['lang_default']) {
                                // Never been changed - update both
                                $_new[$lid] = $lstr;
                            }
                            else {
                                $_upd[$lid] = $lstr;
                            }
                        }
                    }
                    // Text flow direction
                    $ltr = 'ltr';
                    if (isset($lang['direction']) && $lang['direction'] = 'rtl') {
                        $ltr = 'rtl';
                    }
                    // Insert new entries if we have any
                    if (count($_ins) > 0) {
                        $req = "INSERT INTO {$tbl} (lang_module,lang_code,lang_charset,lang_ltr,lang_key,lang_text,lang_default) VALUES\n";
                        foreach ($_ins as $key => $str) {
                            $req .= "('{$mod}','{$cod}','utf-8','{$ltr}','" . jrCore_db_escape($key) . "','" . jrCore_db_escape($str) . "','" . jrCore_db_escape($str) . "'),";
                        }
                        $req = substr($req, 0, strlen($req) - 1);
                        $cnt = jrCore_db_query($req, 'COUNT');
                        if (isset($cnt) && $cnt > 0) {
                            jrCore_logger('INF', "{$dir} {$type} installed {$cnt} new {$code} language strings");
                        }
                    }
                    // Update existing entries with new default
                    if (count($_upd) > 0) {
                        foreach ($_upd as $key => $str) {
                            $req = "UPDATE {$tbl} SET lang_default = '" . jrCore_db_escape($str) . "' WHERE lang_module = '{$mod}' AND lang_code = '{$cod}' AND lang_key = '{$key}'";
                            jrCore_db_query($req);
                        }
                        unset($_upd);
                    }
                    // Update existing entries with new default AND text
                    if (count($_new) > 0) {
                        foreach ($_new as $key => $str) {
                            $req = "UPDATE {$tbl} SET lang_text = '" . jrCore_db_escape($str) . "', lang_default = '" . jrCore_db_escape($str) . "' WHERE lang_module = '{$mod}' AND lang_code = '{$cod}' AND lang_key = '{$key}'";
                            jrCore_db_query($req);
                        }
                        unset($_new);
                    }
                    // Save for below - cloned languages will not have a lang file
                    $_lng[$code] = jrCore_db_escape($code);
                }
            }
        }
        closedir($h);

        // Lastly, we now need to go through and update any CLONED languages
        // that might need to have new language strings inserted based on en-US
        if (count($_lng) > 0) {
            $tbl = jrCore_db_table_name('jrUser', 'language');
            $req = "SELECT lang_code FROM {$tbl} WHERE lang_code NOT IN('" . implode("','", $_lng) . "') GROUP BY lang_code";
            $_cc = jrCore_db_query($req, 'lang_code');
            if ($_cc && is_array($_cc)) {
                $mod = jrCore_db_escape($dir);
                foreach ($_cc as $code => $ignore) {
                    // Make sure all lang strings for this module are setup in this language
                    $ltr = 'ltr';
                    $cod = jrCore_db_escape($code);
                    // First - get existing lang strings
                    $req = "SELECT * FROM {$tbl} WHERE lang_module = '{$mod}' AND lang_code = '{$cod}'";
                    $_ns = jrCore_db_query($req, 'lang_key');
                    if ($_ns && is_array($_ns)) {
                        $_tm = reset($_ns);
                        if ($_tm && isset($_tm['lang_ltr'])) {
                            $ltr = $_tm['lang_ltr'];
                        }
                    }
                    // Next, get English lang strings
                    $req = "SELECT * FROM {$tbl} WHERE lang_module = '{$mod}' AND lang_code = 'en-US'";
                    $_es = jrCore_db_query($req, 'lang_key');
                    if ($_es && is_array($_es)) {
                        // Go through each English string and make sure it exists in the Cloned language
                        foreach ($_es as $lid => $_inf) {
                            if (!$_ns || !is_array($_ns) || !isset($_ns[$lid])) {
                                $req = "INSERT INTO {$tbl} (lang_module,lang_code,lang_charset,lang_ltr,lang_key,lang_text,lang_default) VALUES ('{$mod}','{$cod}','utf-8','{$ltr}','{$lid}','" . jrCore_db_escape($_inf['lang_text']) . "','" . jrCore_db_escape($_inf['lang_default']) . "')";
                                jrCore_db_query($req);
                            }
                        }
                    }
                }
            }
        }
    }
    return true;
}

/**
 * Return an array of available languages
 * @return array
 */
function jrUser_get_languages()
{
    $tbl = jrCore_db_table_name('jrUser', 'language');
    $req = "SELECT lang_code FROM {$tbl} GROUP BY lang_code ORDER BY lang_code ASC";
    $_rt = jrCore_db_query($req, 'lang_code', false, 'lang_code');
    foreach ($_rt as $k => $v) {
        $_rt[$k] = jrUser_get_lang_name(substr($v, 0, 2)) . ' (' . $k . ')';
    }
    return $_rt;
}

/**
 * Checks to see if user is entering invalid passwords
 * @param $ip string IP Address
 * @param $timeout int Seconds to reset
 * @return int
 */
function jrUser_get_password_attempts($ip, $timeout = 60)
{
    if (jrCore_db_table_exists('jrUser', 'pw_attempt')) {
        $tbl = jrCore_db_table_name('jrUser', 'pw_attempt');
        $req = "SELECT *, UNIX_TIMESTAMP() AS a_now FROM {$tbl} WHERE a_ip = '{$ip}'";
        $_at = jrCore_db_query($req, 'SINGLE', false, null, false);
        if ($_at && is_array($_at)) {
            if ($_at['a_time'] < ($_at['a_now'] - $timeout)) {
                // It has been $timeout seconds since last login attempt - reset
                $req = "UPDATE {$tbl} SET a_time = UNIX_TIMESTAMP(), a_count = 1 WHERE a_ip = '{$ip}'";
                jrCore_db_query($req);
                return 1;
            }
            $req = "UPDATE {$tbl} SET a_time = UNIX_TIMESTAMP(), a_count = (a_count + 1) WHERE a_ip = '{$ip}'";
            jrCore_db_query($req);
            return ($_at['a_count'] + 1);
        }
        $req = "INSERT INTO {$tbl} (a_ip, a_time, a_count) VALUES ('{$ip}', UNIX_TIMESTAMP(), 1) ON DUPLICATE KEY UPDATE a_count = (a_count + 1)";
        jrCore_db_query($req, false, null, false, null, false);
        return 1;
    }
    return 0;
}

/**
 * Removes invalid password key for an IP address
 * @param $ip string IP Address
 * @return mixed
 */
function jrUser_reset_password_attempts($ip)
{
    $tbl = jrCore_db_table_name('jrUser', 'pw_attempt');
    $req = "DELETE FROM {$tbl} WHERE a_ip = '{$ip}'";
    return jrCore_db_query($req, null, false, null, false, null, false);
}

/**
 * Remove old password attempt entries
 * @return mixed
 */
function jrUser_delete_old_password_attempts()
{
    $tbl = jrCore_db_table_name('jrUser', 'pw_attempt');
    $req = "DELETE FROM {$tbl} WHERE a_time < (UNIX_TIMESTAMP() - 3600)";
    return jrCore_db_query($req, 'COUNT', false, null, false, null, false);
}

/**
 * List online users
 * @param $_post array Post Info
 * @param $_user array Viewing User Info
 * @param $_conf array Global Config
 * @return bool
 */
function jrUser_online_users($_post, $_user, $_conf)
{
    // Get all our users...
    $dif = 900;
    if (isset($_post['active_time']) && jrCore_checktype($_post['active_time'], 'number_nz')) {
        $dif = (int) $_post['active_time'];
    }

    // See we have a search condition
    $sst = null;
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $sst = $_post['search_string'];
    }
    $_rt = jrUser_session_online_user_info($dif, $sst);

    $url = jrCore_get_module_url('jrCore');
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$url}/dashboard/online", $sst);

    $dat             = array();
    $dat[1]['title'] = 'user';
    $dat[1]['width'] = '13%;';
    $dat[2]['title'] = 'group';
    $dat[2]['width'] = '8%;';
    $dat[3]['title'] = 'location';
    $dat[3]['width'] = '54%;';
    $dat[4]['title'] = 'IP';
    $dat[4]['width'] = '5%;';
    $dat[5]['title'] = 'updated';
    $dat[5]['width'] = '10%;';
    $dat[6]['title'] = 'log&nbsp;off';
    $dat[6]['width'] = '5%;';

    $burl = false;
    if (jrCore_module_is_active('jrBanned')) {
        $dat[7]['title'] = 'ban&nbsp;IP';
        $dat[7]['width'] = '2%;';
        $dat[8]['title'] = 'account';
        $dat[8]['width'] = '3%;';
        $burl            = jrCore_get_module_url('jrBanned');
    }
    else {
        $dat[8]['title'] = 'account';
        $dat[8]['width'] = '5%;';
    }
    jrCore_page_table_header($dat);

    $curl = jrCore_get_module_url('jrUser');
    $myip = jrCore_get_ip();

    //-------------------------------
    // USERS
    //-------------------------------
    $_dn = array();
    foreach ($_rt as $k => $_us) {

        // Don't show AJAX actions
        if (strpos($_us['session_user_action'], '__ajax')) {
            continue;
        }
        if (!isset($_us['session_user_name']) || strlen($_us['session_user_name']) === 0 || strpos($_us['session_user_name'], 'bot:') === 0) {
            continue;
        }
        if (isset($_dn["{$_us['session_user_name']}"])) {
            continue;
        }
        $_dn["{$_us['session_user_name']}"] = 1;
        $dat                                = array();
        if (strpos($_us['session_user_action'], '?')) {
            $_us['session_user_action'] = substr($_us['session_user_action'], 0, strpos($_us['session_user_action'], '?'));
        }
        $dat[1]['title'] = $_us['session_user_name'];
        $dat[1]['class'] = 'center';
        $dat[2]['title'] = $_us['session_user_group'];
        $dat[2]['class'] = 'center';
        $dat[3]['title'] = isset($_us['session_user_action']{0}) ? jrCore_entity_string($_us['session_user_action']) : '/';
        $dat[3]['class'] = 'word-break';
        $dat[4]['title'] = "<a onclick=\"popwin('{$_conf['jrCore_base_url']}/{$curl}/whois/{$_us['session_user_ip']}','{$_us['session_user_ip']}',900,600,'yes');\">" . $_us['session_user_ip'] . '</a>';
        $dat[4]['class'] = 'center';
        $dat[5]['title'] = jrCore_format_time($_us['session_updated']);
        $dat[5]['class'] = 'center';

        // Show "Ban IP" button if jrBanned is installed
        if ($burl) {

            if ((isset($_us['session_user_group']) && $_us['session_user_group'] == 'master') || $_us['session_user_ip'] == $myip) {
                $dat[6]['title'] = jrCore_page_button("r{$k}", 'log off', 'disabled');
                $dat[7]['title'] = jrCore_page_button("b{$k}", 'ban IP', 'disabled');
            }
            elseif (isset($_us['session_user_id']) && jrCore_checktype($_us['session_user_id'], 'number_nz')) {
                $dat[6]['title'] = jrCore_page_button("r{$k}", 'log off', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$curl}/session_remove_save/{$_us['session_user_id']}')");
                $dat[7]['title'] = jrCore_page_button("b{$k}", 'ban IP', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$burl}/item_save/ban_type=ip/ban_value={$_us['session_user_ip']}')");
            }
            else {
                $dat[6]['title'] = 'n/a';
                $dat[6]['class'] = 'center';
                $dat[7]['title'] = jrCore_page_button("b{$k}", 'ban IP', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$burl}/item_save/ban_type=ip/ban_value={$_us['session_user_ip']}')");
            }
        }
        else {

            if (isset($_us['session_user_group']) && $_us['session_user_group'] == 'master') {
                $dat[6]['title'] = jrCore_page_button("r{$k}", 'log off', 'disabled');
            }
            elseif (isset($_us['session_user_id']) && jrCore_checktype($_us['session_user_id'], 'number_nz')) {
                $dat[6]['title'] = jrCore_page_button("r{$k}", 'log off', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$curl}/session_remove_save/{$_us['session_user_id']}')");
            }
            else {
                $dat[6]['title'] = 'n/a';
                $dat[6]['class'] = 'center';
            }
        }
        $dat[8]['title'] = jrCore_page_button("m{$k}", 'account', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$curl}/account/profile_id={$_us['session_profile_id']}/user_id={$_us['session_user_id']}')");
        jrCore_page_table_row($dat);
    }

    //-------------------------------
    // VISITORS
    //-------------------------------
    foreach ($_rt as $k => $_us) {

        // Don't show AJAX actions
        if (strpos($_us['session_user_action'], '__ajax')) {
            continue;
        }
        if (isset($_us['session_user_name']) && strlen($_us['session_user_name']) > 0) {
            continue;
        }
        $dat             = array();
        $dat[1]['title'] = 'visitor';
        $dat[1]['class'] = 'center';
        $dat[2]['title'] = '-';
        $dat[2]['class'] = 'center';
        $dat[3]['title'] = jrCore_entity_string($_us['session_user_action']);
        $dat[3]['class'] = 'word-break';
        $dat[4]['title'] = "<a onclick=\"popwin('{$_conf['jrCore_base_url']}/{$curl}/whois/{$_us['session_user_ip']}','{$_us['session_user_ip']}',900,600,'yes');\">" . $_us['session_user_ip'] . '</a>';
        $dat[4]['class'] = 'center';
        $dat[5]['title'] = jrCore_format_time($_us['session_updated']);
        $dat[5]['class'] = 'center';
        $dat[6]['title'] = 'n/a';
        $dat[6]['class'] = 'center';
        if ($burl) {
            // Show "Ban IP" button if jrBanned is installed
            if ($_us['session_user_ip'] == $myip) {
                $dat[7]['title'] = jrCore_page_button("b{$k}", 'ban IP', 'disabled');
            }
            else {
                $dat[7]['title'] = jrCore_page_button("b{$k}", 'ban IP', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$burl}/item_save/ban_type=ip/ban_value={$_us['session_user_ip']}')");
            }
        }
        $dat[8]['title'] = '-';
        $dat[8]['class'] = 'center';
        jrCore_page_table_row($dat);
    }

    //-------------------------------
    // BOTS
    //-------------------------------
    if (isset($_post['show_bots']) && $_post['show_bots'] == '1') {
        foreach ($_rt as $k => $_us) {
            // Don't show AJAX actions
            if (strpos($_us['session_user_action'], '__ajax')) {
                continue;
            }
            if (!isset($_us['session_user_name']) || strpos($_us['session_user_name'], 'bot:') !== 0) {
                continue;
            }
            $dat             = array();
            $dat[1]['title'] = jrCore_str_to_lower(str_replace('bot: ', '', $_us['session_user_name']));
            $dat[1]['class'] = 'center error';
            $dat[2]['title'] = '-';
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = jrCore_entity_string($_us['session_user_action']);
            $dat[3]['class'] = 'word-break';
            $dat[4]['title'] = "<a onclick=\"popwin('{$_conf['jrCore_base_url']}/{$curl}/whois/{$_us['session_user_ip']}','{$_us['session_user_ip']}',900,600,'yes');\">" . $_us['session_user_ip'] . '</a>';
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrCore_format_time($_us['session_updated']);
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = 'n/a';
            $dat[6]['class'] = 'center';
            if ($burl) {
                // Show "Ban IP" button if jrBanned is installed
                $dat[7]['title'] = jrCore_page_button("b{$k}", 'ban IP', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$burl}/item_save/ban_type=ip/ban_value={$_us['session_user_ip']}')");
            }
            $dat[8]['title'] = '-';
            $dat[8]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
    }

    jrCore_page_table_footer();
    return true;
}

//------------------------------------
// User Checks
//------------------------------------

/**
 * The jrUser_is_logged_in function will return true if a user is logged
 * in to Jamroom, and false if they are a viewer.
 * @return bool Returns true/false on success/fail
 */
function jrUser_is_logged_in()
{
    if (isset($_SESSION['_user_id']) && $_SESSION['_user_id'] > 0) {
        return true;
    }
    return false;
}

/**
 * The jrUser_is_master function will return true if the user
 * is the Master Admin
 * @return bool Returns true/false on success/fail
 */
function jrUser_is_master()
{
    if (jrUser_is_logged_in() && isset($_SESSION['user_group']) && $_SESSION['user_group'] == 'master') {
        return true;
    }
    return false;
}

/**
 * The jrUser_is_admin function will return true if a user is logged
 * in to Jamroom, and is an Admin User
 * @return bool Returns true/false on success/fail
 */
function jrUser_is_admin()
{
    if (jrUser_is_logged_in() && isset($_SESSION['user_group'])) {
        switch ($_SESSION['user_group']) {
            case 'master':
            case 'admin':
                return true;
                break;
        }
    }
    return false;
}

/**
 * The jrUser_is_power_user function will return true if a user is logged
 * in to Jamroom, and is a Power User (more than 1 profile)
 * @return bool Returns true/false on success/fail
 */
function jrUser_is_power_user()
{
    if (jrUser_is_logged_in() && (jrUser_is_admin() || jrUser_get_profile_home_key('quota_jrUser_power_user') == 'on')) {
        return true;
    }
    return false;
}

/**
 * Check if user manages multiple profiles
 * @return bool Returns true/false on success/fail
 */
function jrUser_is_multi_user()
{
    if (jrUser_is_logged_in() && (jrUser_is_admin() || (isset($_SESSION['user_linked_profile_ids']) && strpos($_SESSION['user_linked_profile_ids'], ',')))) {
        return true;
    }
    return false;
}

/**
 * Checks if the calling user is in the specified Quota
 * @param $quota_id integer Quota ID
 * @return bool
 */
function jrUser_in_quota($quota_id)
{
    if (jrUser_is_logged_in() && isset($_SESSION['profile_quota_id']) && intval($_SESSION['profile_quota_id']) === intval($quota_id)) {
        return true;
    }
    return false;
}

/**
 * The jrUser_master_only function is used to ensure access to a code section is
 * for Master Users only - any other access by anyone else will result
 * in a log message, and they will be logged out.
 * @return null
 */
function jrUser_master_only()
{
    jrUser_session_require_login();
    if (isset($_SESSION['user_group']) && $_SESSION['user_group'] == 'master') {
        return true;
    }
    return jrUser_not_authorized();
}

/**
 * The jrUser_admin_only function is used to ensure access to a code section is
 * for Profile Admins only - any other access by anyone else will result in error
 * @return null
 */
function jrUser_admin_only()
{
    jrUser_session_require_login();
    if (isset($_SESSION['user_group'])) {
        switch ($_SESSION['user_group']) {
            case 'master':
            case 'admin':
                return true;
                break;
        }
    }
    return jrUser_not_authorized();
}

/**
 * Exits with a "not authorized" message.
 * @return null
 */
function jrUser_not_authorized()
{
    global $_conf;
    $_ln = jrUser_load_lang_strings();
    if (jrCore_is_ajax_request()) {
        jrCore_notice_page('error', $_ln['jrCore'][41]);
    }
    elseif (jrUser_is_logged_in()) {
        $ref = 'referrer';
        if (strpos(jrCore_get_local_referrer(), '/login')) {
            $ref = $_conf['jrCore_base_url'];
        }
        jrCore_notice_page('error', '<div class="p20">' . $_ln['jrCore'][41] . '</div>', $ref, $_ln['jrCore'][87], false);
    }
    else {
        $murl = jrCore_get_module_url('jrUser');
        jrCore_notice_page('error', '<div class="p20">' . $_ln['jrCore'][41] . '</div>', "{$_conf['jrCore_base_url']}/{$murl}/login", $_ln['jrCore'][87], false);
    }
    exit;
}

/**
 * Returns true of the viewing user is allowed to edit the viewed profile
 * @param $profile_id integer Profile ID
 * @return bool
 */
function jrUser_is_profile_owner($profile_id)
{
    global $_user;
    if (!isset($profile_id) || !jrCore_checktype($profile_id, 'number_nz')) {
        return false;
    }
    if (jrUser_is_admin() || (isset($_user['user_linked_profile_ids']) && in_array($profile_id, explode(',', $_user['user_linked_profile_ids'])))) {
        return true;
    }
    return false;
}

/**
 * Returns true/false if the current user has the proper credentials to edit the given item
 * @param $_item array Array of Item information returned from jrCore_db_get_item()
 * @return bool
 */
function jrUser_can_edit_item($_item)
{
    global $_user;
    if (jrUser_is_admin() || (isset($_user['user_linked_profile_ids']) && isset($_item['_profile_id']) && in_array($_item['_profile_id'], explode(',', $_user['user_linked_profile_ids'])))) {
        return true;
    }
    return false;
}

/**
 * Reset cached pages for a specific user_id
 * @param $uid integer User ID to reset cached pages for
 * @param $module string Module to delete for
 * @return mixed
 */
function jrUser_reset_cache($uid, $module = null)
{
    if (!jrCore_checktype($uid, 'number_nn')) {
        return false;
    }
    return jrCore_delete_all_cache_entries($module, $uid);
}

//------------------------------------
// User Session
//------------------------------------

/**
 * Set a new session value
 * @param string $key
 * @param mixed $value
 * @return bool
 */
function jrUser_set_session_key($key, $value)
{
    global $_user;
    $_user[$key]    = $value;
    $_SESSION[$key] = $value;
    return true;
}

/**
 * Get a previously set session value
 * @param string $key
 * @return bool
 */
function jrUser_get_session_key($key)
{
    return (isset($_SESSION[$key])) ? $_SESSION[$key] : false;
}

/**
 * Return TRUE if a session key exists
 * @param string $key
 * @return bool
 */
function jrUser_session_key_exists($key)
{
    return (isset($_SESSION[$key]));
}

/**
 * Delete a session key if it exists
 * @param string $key
 * @return bool
 */
function jrUser_delete_session_key($key)
{
    global $_user;
    if (isset($_user[$key])) {
        unset($_user[$key]);
    }
    if (isset($_SESSION[$key])) {
        unset($_SESSION[$key]);
    }
    return true;
}

/**
 * End a user session
 * @param array $_user
 * @return bool
 */
function jrUser_end_user_session($_user)
{
    // Send logout trigger
    jrCore_trigger_event('jrUser', 'logout', $_user);

    // Destroy session
    $sid = jrUser_session_destroy();

    // Successful logout
    jrCore_trigger_event('jrUser', 'logout_success', $_user, array('session_id' => $sid));
    return true;
}

/**
 * @ignore
 * jrCore_get_active_cache_system
 * @return string
 */
function jrUser_get_active_session_system()
{
    global $_conf;
    if (isset($_conf['jrUser_active_session_system']{1})) {
        // Make sure it is valid...
        $func = "_{$_conf['jrUser_active_session_system']}_session_open";
        if (function_exists($func)) {
            return $_conf['jrUser_active_session_system'];
        }
    }
    return 'jrUser_mysql';
}

/**
 * jrUser_ignore_action
 */
function jrUser_ignore_action()
{
    jrCore_set_flag('jruser_ignore_action', 1);
    return true;
}

/**
 * Used internally by Jamroom
 * @ignore
 */
function jrUser_unique_install_id()
{
    global $_conf;
    return substr(md5($_conf['jrCore_base_url']), 0, 12);
}

/**
 * Initialize a Jamroom Session
 * @return true
 */
function jrUser_session_init()
{
    global $_conf;
    if (isset($_SESSION)) {
        // We already have a session up...
        return true;
    }
    // Set PHPs garbage collection higher than our own collection -
    // this prevents PHP from stepping in and messing with our sessions
    $exp = isset($_conf['jrUser_session_expire_min']) ? ($_conf['jrUser_session_expire_min'] * 60) : 7200;

    // Trigger event so add on modules can override this if needed
    $res = array('expire_length' => $exp);
    $res = jrCore_trigger_event('jrUser', 'session_init', $res, $res);
    if (!is_array($res)) {
        // Our session support was initialized by a listener
        return true;
    }

    ini_set('session.gc_maxlifetime', ($exp + 7200));
    $act = jrUser_get_active_session_system();
    session_set_save_handler("_{$act}_session_open", "_{$act}_session_close", "_{$act}_session_read", "_{$act}_session_write", "_{$act}_session_destroy", "_{$act}_session_collect");
    session_name('sess' . jrUser_unique_install_id());
    session_start();
    return true;
}

/**
 * End a Jamroom session
 * @return bool Returns true
 */
function jrUser_session_destroy()
{
    jrUser_session_init();
    $_SESSION = array();
    @session_unset();
    @session_destroy();
    unset($_SESSION);
    jrUser_session_delete_login_cookie();
    return true;
}

/**
 * Delete an individual Session ID
 * @param string $sid Session ID to remove
 * @return bool Returns true
 */
function jrUser_session_delete_session_id($sid)
{
    $act = jrUser_get_active_session_system();
    if (!$act) {
        $act = 'jrUser_mysql';
    }
    $fnc = "_{$act}_session_delete_session_id";
    if (function_exists($fnc)) {
        return $fnc($sid);
    }
    return false;
}

/**
 * Remove all sessions for a given User ID
 * @param $user_id mixed User ID or array of User IDs
 * @return bool
 */
function jrUser_session_remove($user_id)
{
    $act = jrUser_get_active_session_system();
    if (!$act) {
        $act = 'jrUser_mysql';
    }
    $fnc = "_{$act}_session_remove";
    if (function_exists($fnc)) {
        return $fnc($user_id);
    }
    return false;
}

/**
 * Remove all sessions for a given User ID except active session
 * @param $user_id int User ID
 * @param $active_sid string Active Session ID
 * @return bool
 */
function jrUser_session_remove_all_other_sessions($user_id, $active_sid)
{
    $act = jrUser_get_active_session_system();
    if (!$act) {
        $act = 'jrUser_mysql';
    }
    $fnc = "_{$act}_session_remove_all_other_sessions";
    if (function_exists($fnc)) {

        // Remove Remember Me cookies
        $uid = (int) $user_id;
        $tbl = jrCore_db_table_name('jrUser', 'cookie');
        $req = "SELECT cookie_id FROM {$tbl} WHERE cookie_user_id = '{$uid}'";
        $_rt = jrCore_db_query($req, 'cookie_id');
        if ($_rt && is_array($_rt)) {
            $req = "DELETE FROM {$tbl} WHERE cookie_id IN('" . implode("','", array_keys($_rt)) . "')";
            jrCore_db_query($req);
        }
        return $fnc($user_id, $active_sid);

    }
    return false;
}

/**
 * Get session information by SID
 * @param $sid string Active Session ID
 * @return bool
 */
function jrUser_session_is_valid_session($sid)
{
    $act = jrUser_get_active_session_system();
    if (!$act) {
        $act = 'jrUser_mysql';
    }
    $fnc = "_{$act}_session_is_valid_session";
    if (function_exists($fnc)) {
        return $fnc($sid);
    }
    return false;
}

/**
 * Get total number of online users
 * @param $length int Max number of seconds with no activity a session is considered "active"
 * @param $type string user|bot to get count by online user type
 * @return int
 */
function jrUser_session_online_user_count($length = 900, $type = 'combined')
{
    $act = jrUser_get_active_session_system();
    if (!$act) {
        $act = 'jrUser_mysql';
    }
    $fnc = "_{$act}_session_online_user_count";
    if (function_exists($fnc)) {
        return $fnc($length, $type);
    }
    return false;
}

/**
 * Get ids of online users
 * @param int $length number of seconds of inactivity before user is "offline"
 * @param array $_ids only check the given IDs
 * @return mixed
 */
function jrUser_session_online_user_ids($length = 900, $_ids = null)
{
    $act = jrUser_get_active_session_system();
    if (!$act) {
        $act = 'jrUser_mysql';
    }
    $fnc = "_{$act}_session_online_user_ids";
    if (function_exists($fnc)) {
        return $fnc($length, $_ids);
    }
    return false;
}

/**
 * Get information about online users
 * @param $length int Max number of seconds with no activity a session is considered "active"
 * @param $search string Optional Search string
 * @return mixed
 */
function jrUser_session_online_user_info($length = 900, $search = null)
{
    $act = jrUser_get_active_session_system();
    if (!$act) {
        $act = 'jrUser_mysql';
    }
    $fnc = "_{$act}_session_online_user_info";
    if (function_exists($fnc)) {
        return $fnc($length, $search);
    }
    return false;
}

/**
 * Set the session_sync flag for a Quota or array of Quotas
 * @param $quota_id mixed Quota ID or array of Quota IDs to set session_sync flag for
 * @param $state string on|off
 * @return bool
 */
function jrUser_set_session_sync_for_quota($quota_id, $state)
{
    $act = jrUser_get_active_session_system();
    if (!$act) {
        $act = 'jrUser_mysql';
    }
    $fnc = "_{$act}_session_sync_for_quota";
    if (function_exists($fnc)) {
        return $fnc($quota_id, $state);
    }
    return false;
}

/**
 * Set the session_sync flag for a User ID
 * @param int $user_id User ID
 * @param $state string on|off
 * @return bool
 */
function jrUser_set_session_sync_for_user_id($user_id, $state)
{
    $act = jrUser_get_active_session_system();
    if (!$act) {
        $act = 'jrUser_mysql';
    }
    $fnc = "_{$act}_session_sync_for_user_id";
    if (function_exists($fnc)) {
        return $fnc($user_id, $state);
    }
    return false;
}

/**
 * redirect a user to the /user/login page if they are not logged in
 * @return mixed returns bool true if user is logged on, redirects to login page if not
 */
function jrUser_session_require_login()
{
    global $_conf;
    if (!jrUser_is_logged_in()) {

        jrUser_session_init();

        // Save where they were trying to go before they needed to log in so we
        // can send them back to that location once logged in.
        jrUser_save_location();

        // Redirect them to login
        $_ln = jrUser_load_lang_strings();
        jrCore_set_form_notice('error', $_ln['jrUser'][108]);
        $url = jrCore_get_module_url('jrUser');
        jrCore_location("{$_conf['jrCore_base_url']}/{$url}/login");
    }
    return true;
}

/**
 * Get Name of unique auto login cookie
 * @return string
 */
function jrUser_session_get_login_cookie_name()
{
    return 'auto' . jrUser_unique_install_id();
}

/**
 * Sets a Browser "Remember Me" Login cookie
 * @param $user_id integer User ID to set cookie for
 * @return bool
 */
function jrUser_session_set_login_cookie($user_id)
{
    global $_conf;
    if (!jrCore_checktype($user_id, 'number_nz')) {
        return false;
    }
    $val = md5(microtime());
    $tbl = jrCore_db_table_name('jrUser', 'cookie');
    $req = "INSERT INTO {$tbl} (cookie_user_id,cookie_time,cookie_value) VALUES ('{$user_id}',UNIX_TIMESTAMP(),'" . sha1($val) . "')";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!$cnt || $cnt !== 1) {
        jrCore_logger('CRI', "jrUser_session_set_login_cookie() unable to set autologin cookie for user_id: {$user_id} - check error log");
    }
    else {
        // Create new cookie
        $tim = (14 * 86400); // Default: 14 days
        if (isset($_conf['jrUser_autologin']) && jrCore_checktype($_conf['jrUser_autologin'], 'number_nz') && $_conf['jrUser_autologin'] != '2') {
            // If we are 3, that is the old value for "permanent"
            if ($_conf['jrUser_autologin'] == '3') {
                // Set it for really far in the future
                $tim = (1000 * 86400);
            }
            else {
                $tim = (intval($_conf['jrUser_autologin']) * 86400);
            }
        }
        $cid = jrUser_session_get_login_cookie_name();
        setcookie($cid, "{$user_id}-{$val}", time() + $tim, '/');
        $_COOKIE[$cid] = "{$user_id}-{$val}";
    }
    return true;
}

/**
 * Delete an auto-login cookie
 * @param string $cid unique cookie ID
 * @return bool
 */
function jrUser_session_delete_login_cookie($cid = null)
{
    if (is_null($cid)) {
        $cid = jrUser_session_get_login_cookie_name();
    }
    if (isset($_COOKIE[$cid])) {

        if (strpos($_COOKIE[$cid], '-')) {
            // DB Cleanup
            list($uid, $md5) = explode('-', $_COOKIE[$cid], 2);
            $uid = (int) $uid;
            if ($uid > 0) {
                $md5 = trim($md5);
                $tbl = jrCore_db_table_name('jrUser', 'cookie');
                $req = "DELETE FROM {$tbl} WHERE cookie_user_id = '{$uid}' AND cookie_value = '" . sha1($md5) . "'";
                jrCore_db_query($req);
            }
        }

        // Remove actual cookie
        setcookie($cid, '', time() - 8640000, '/');
        unset($_COOKIE[$cid]);
    }
    return true;
}

/**
 * Get a User-ID from a valid auto-login cookie
 * @return bool|int
 */
function jrUser_get_user_id_from_login_cookie()
{
    global $_conf;
    // Check for "remember me" cookie being set on login.
    $cid = jrUser_session_get_login_cookie_name();
    if (!isset($_COOKIE[$cid]) || strlen($_COOKIE[$cid]) === 0) {
        return false;
    }
    if (!strpos($_COOKIE[$cid], '-')) {
        // Bad value
        jrUser_session_delete_login_cookie($cid);
        return false;
    }
    // Looks like we have an auto login cookie - process
    list($user_id, $hash_id) = explode('-', $_COOKIE[$cid], 2);
    $user_id = (int) $user_id;
    $hash_id = trim($hash_id);
    if (!$user_id || !jrCore_checktype($user_id, 'number_nz')) {
        // Bad request - although we don't know the user ID, we can't do a cleanup - remove cookie
        jrUser_session_delete_login_cookie($cid);
        return false;
    }
    // Our cookie value comes in as an MD5
    if (!isset($hash_id{30}) || !jrCore_checktype($hash_id, 'md5')) {
        jrUser_session_delete_login_cookie($cid);
        return false;
    }
    // Check for expired cookie
    $val = (14 * 86400); // Default: 14 days
    if (isset($_conf['jrUser_autologin']) && jrCore_checktype($_conf['jrUser_autologin'], 'number_nz')) {
        switch (intval($_conf['jrUser_autologin'])) {
            case 7:
            case 30:
            case 60:
            case 90:
                $val = (intval($_conf['jrUser_autologin']) * 86400);
                break;
            case 3:
                // 3 is "never" so we just set it really far off
                $val = (1000 * 86400);
                break;
        }
    }
    $tbl = jrCore_db_table_name('jrUser', 'cookie');
    $tbi = jrCore_db_table_name('jrUser', 'item');
    $req = "SELECT c.cookie_id FROM {$tbl} c INNER JOIN {$tbi} i ON (i.`_item_id` = c.cookie_user_id) WHERE c.cookie_user_id = '{$user_id}' AND c.cookie_value = '" . sha1($hash_id) . "' AND c.cookie_time > (UNIX_TIMESTAMP() - {$val})";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        // Not found or expired
        jrUser_session_delete_login_cookie($cid);
        return false;
    }
    return intval($user_id);
}

/**
 * Start a User Session
 * @param $option_check bool set to false to skip the option check
 * @return mixed
 */
function jrUser_session_start($option_check = true)
{
    global $_post;

    // Some "options" have no need for a full session load
    if ($option_check && isset($_post['option']) && strlen($_post['option']) > 0) {
        $_tmp = jrCore_get_registered_module_features('jrUser', 'skip_session');
        if (is_array($_tmp)) {
            // Quick check for exact option
            if (isset($_tmp["{$_post['module']}"]["{$_post['option']}"])) {
                return jrUser_get_logged_out_user_session();
            }
            // Fall through for magic view check
            foreach ($_tmp as $mod => $_opts) {
                if (isset($_opts["{$_post['option']}"]) && $_opts["{$_post['option']}"] === 'magic_view') {
                    return jrUser_get_logged_out_user_session();
                }
            }
        }
    }

    jrUser_session_init();

    if (!isset($_SESSION['is_logged_in'])) {

        if ($user_id = jrUser_get_user_id_from_login_cookie()) {

            // We got a good user_id from our login cookie
            // Get associated user and profile data for this user
            if ($_user = jrUser_get_user_session_data($user_id)) {

                // Update last login time for user
                jrCore_db_update_item('jrUser', $user_id, array('user_last_login' => 'UNIX_TIMESTAMP()'), null, false);

                // Trigger "login_success" since this user came back with a cookie
                $_user = jrCore_trigger_event('jrUser', 'login_success', $_user);

                // Save to cache
                $c_key = "{$user_id}-{$_user['_profile_id']}-" . session_id();
                jrCore_add_to_cache('jrUser', $c_key, $_user);

                // Save off profile home keys
                jrUser_save_profile_home_keys();

            }
        }
        else {

            // Unable to get valid user_id from login cookie
            $_user = jrUser_get_logged_out_user_session();

        }

        // Add each key and value to our session
        foreach ($_user as $k => $v) {
            $_SESSION[$k] = $v;
        }

    }

    // Is this user logged in?
    if (isset($_SESSION) && isset($_SESSION['_user_id']) && $_SESSION['_user_id'] > 0) {

        // We are now logged in
        $_SESSION['is_logged_in'] = 'yes';

        // Update last_login_time if greater than 24 hours ago
        if (!isset($_SESSION['user_last_login']) || $_SESSION['user_last_login'] < (time() - 86400)) {
            // Update last login time for user
            jrCore_db_update_item('jrUser', $_SESSION['_user_id'], array('user_last_login' => time()), null, false);
            $_SESSION['user_last_login'] = time();
        }

    }

    // Do we have a session sync?
    if (jrCore_get_flag('user_session_sync') == 1) {
        jrUser_session_sync($_SESSION['_user_id']);
    }

    // Trigger session started event
    return jrCore_trigger_event('jrUser', 'session_started', $_SESSION);
}

/**
 * Refresh an existing user session with the latest User and Profile info
 * @param int $user_id User to synchronize session for
 * @return bool
 */
function jrUser_session_sync($user_id)
{
    // Get latest user info
    if (jrUser_is_logged_in()) {

        $uid = (int) $user_id;
        $pid = (isset($_SESSION['user_active_profile_id'])) ? intval($_SESSION['user_active_profile_id']) : 0;
        if ($_user = jrUser_get_user_session_data($uid, $pid)) {

            $_SESSION = array_merge($_SESSION, $_user);

            // Refresh cached value
            jrCore_add_to_cache('jrUser', "{$uid}-{$pid}-" . session_id(), $_user);

            // reset our skin_cache menu to pick up any new settings
            jrCore_delete_cache('jrCore', "skin_menu_{$uid}");

            return true;

        }
        else {
            global $_user;
            jrUser_end_user_session($_user);
        }
    }
    return false;
}

/**
 * Get all session data for a User
 * @param int $user_id
 * @param int $profile_id
 * @return array|bool
 */
function jrUser_get_user_session_data($user_id, $profile_id = 0)
{
    $uid = (int) $user_id;
    $_rt = jrCore_db_get_item('jrUser', $uid, true, true);
    if ($_rt && is_array($_rt)) {

        // Add in Profile Info
        if ($profile_id > 0) {
            $pid = (int) $profile_id;
        }
        else {
            $pid = (int) $_rt['_profile_id'];
        }

        // Get profiles we link to
        $_pn = jrProfile_get_user_linked_profiles($uid);
        if (!$_pn || !is_array($_pn)) {
            // Do we have info on the profile? If we do, then it could just be a linking issue
            jrCore_logger('CRI', "no profiles found for user_id {$uid} ({$_rt['user_name']})", $_rt);
            return false;
        }
        if (!isset($_pn[$pid]) && $_rt['user_group'] == 'user') {
            // Bad profile id
            $pid = jrUser_get_profile_home_key('_profile_id');
            if (!isset($_pn[$pid])) {
                // We cannot figure out this user's profile
                jrCore_logger('CRI', "invalid linked profiles found for user_id {$uid} ({$_rt['user_name']})", $_rt);
                return true;
            }
        }
        $_rt['user_linked_profile_ids'] = implode(',', array_keys($_pn));

        // Bring in Profile Info
        // NOTE: We do not skip_triggers here - this way quota data gets added in
        $_tm = jrCore_db_get_item('jrProfile', $pid, false, true);
        if ($_tm && is_array($_tm)) {
            $_rt['profile_created'] = $_tm['_created'];
            $_rt['profile_updated'] = $_tm['_updated'];
            $_rt                    = array_merge($_tm, $_rt);
        }
        $_rt['_item_id']               = $uid;
        $_rt['_user_id']               = $uid;
        $_rt['_profile_id']            = $pid;
        $_rt['user_active_profile_id'] = $pid;
        return $_rt;

    }
    return false;
}

/**
 * Return array of user info used for logged out users
 * @return array
 */
function jrUser_get_logged_out_user_session()
{
    global $_conf;
    return array(
        'profile_quota_id' => 0,
        'user_language'    => $_conf['jrUser_default_language'],
        'is_logged_in'     => 'no'
    );
}

/**
 * Get a user's profile home data
 * @return bool|mixed|string
 */
function jrUser_get_profile_home_data()
{
    global $_user;
    if (isset($_user['_user_id'])) {
        $key = "profile_home_keys_{$_user['_user_id']}";
        if (!$_us = jrCore_get_flag($key)) {
            if (!$_us = jrCore_is_cached('jrUser', $key, true, false)) {
                // We are NOT in cache - setup
                $_us = jrUser_save_profile_home_keys();
            }
            jrCore_set_flag($key, $_us);
        }
        if (is_array($_us)) {
            return $_us;
        }
    }
    return false;
}

/**
 * Get a specific HOME PROFILE Key for a user
 * @param $key string Key to return value for
 * @return mixed string|bool
 */
function jrUser_get_profile_home_key($key)
{
    $_us = jrUser_get_profile_home_data();
    if ($_us && isset($_us[$key])) {
        return $_us[$key];
    }
    return false;
}

/**
 * Save a User's Profile Keys to their home profile container
 * @return bool|mixed
 */
function jrUser_save_profile_home_keys()
{
    if (isset($_SESSION['_user_id'])) {
        $pid = jrCore_db_get_item_key('jrUser', $_SESSION['_user_id'], '_profile_id');
        if ($pid && $pid > 0) {
            $_pr = jrCore_db_get_item('jrProfile', $pid);
            foreach ($_pr as $k => $v) {
                if ($k != '_profile_id' && strpos($k, 'profile_') !== 0 && strpos($k, 'quota_') !== 0) {
                    unset($_pr[$k]);
                }
            }
            $key = "profile_home_keys_{$_SESSION['_user_id']}";
            jrCore_add_to_cache('jrUser', $key, $_pr, 0, $pid, true, false);
            jrCore_set_flag($key, $_pr);
            return $_pr;
        }
    }
    return false;
}

/**
 * Get configured session plugins
 * @return array
 */
function jrUser_get_session_system_plugins()
{
    return jrCore_get_system_plugins('session');
}

/**
 * Save the current location as a saved location
 */
function jrUser_save_location()
{
    // We never "save" an AJAX request, since that does not have a view
    if (!jrCore_is_ajax_request() && jrCore_is_view_request()) {
        jrCore_set_cookie('jruser_save_location', jrCore_get_current_url(), 1);
    }
    return true;
}

/**
 * Returns a saved location if one is set
 */
function jrUser_get_saved_location()
{
    if ($url = jrCore_get_cookie('jruser_save_location')) {
        jrCore_delete_cookie('jruser_save_location');
        if (strlen($url) > 0) {
            return $url;
        }
    }
    return false;
}

/**
 * Save URL location to temp table for a user
 * @param null $url string URL to save
 * @return bool|mixed
 */
function jrUser_save_url_location($url = null)
{
    if (!jrUser_is_logged_in()) {
        return false;
    }
    if (is_null($url)) {
        $url = jrCore_get_current_url();
    }
    $_SESSION['user_memory_url'] = $url;
    return true;
}

/**
 * Get a previously saved URL location for a user
 * @param int $user_id User ID to get URL for
 * @return bool|mixed
 */
function jrUser_get_saved_url_location($user_id = 0)
{
    if (!jrUser_is_logged_in()) {
        return false;
    }
    return (isset($_SESSION['user_memory_url'])) ? $_SESSION['user_memory_url'] : false;
}

/**
 * Delete a previously saved URL location for a user
 * @param int $user_id User ID to get URL for
 * @return bool|mixed
 */
function jrUser_delete_saved_url_location($user_id = 0)
{
    if (isset($_SESSION['user_memory_url'])) {
        unset($_SESSION['user_memory_url']);
    }
    return true;
}

/**
 * Returns the Language Name for a given ISO-639-1 Code
 * @param string $code Language code to return name of
 * @return string
 */
function jrUser_get_lang_name($code)
{
    $_codes = array(
        "aa" => "Afar",
        "ab" => "Abkhazian",
        "ae" => "Avestan",
        "af" => "Afrikaans",
        "ak" => "Akan",
        "am" => "Amharic",
        "an" => "Aragonese",
        "ar" => "Arabic",
        "as" => "Assamese",
        "av" => "Avaric",
        "ay" => "Aymara",
        "az" => "Azerbaijani",
        "ba" => "Bashkir",
        "be" => "Belarusian",
        "bg" => "Bulgarian",
        "bh" => "Bihari",
        "bi" => "Bislama",
        "bm" => "Bambara",
        "bn" => "Bengali",
        "bo" => "Tibetan",
        "br" => "Breton",
        "bs" => "Bosnian",
        "ca" => "Catalan",
        "ce" => "Chechen",
        "ch" => "Chamorro",
        "co" => "Corsican",
        "cr" => "Cree",
        "cs" => "Czech",
        "cu" => "Church Slavic",
        "cv" => "Chuvash",
        "cy" => "Welsh",
        "da" => "Danish",
        "de" => "German",
        "dv" => "Divehi",
        "dz" => "Dzongkha",
        "ee" => "Ewe",
        "el" => "Greek",
        "en" => "English",
        "eo" => "Esperanto",
        "es" => "Spanish",
        "et" => "Estonian",
        "eu" => "Basque",
        "fa" => "Persian",
        "ff" => "Fulah",
        "fi" => "Finnish",
        "fj" => "Fijian",
        "fo" => "Faroese",
        "fr" => "French",
        "fy" => "Western Frisian",
        "ga" => "Irish",
        "gd" => "Scottish Gaelic",
        "gl" => "Galician",
        "gn" => "Guarani",
        "gu" => "Gujarati",
        "gv" => "Manx",
        "ha" => "Hausa",
        "he" => "Hebrew",
        "hi" => "Hindi",
        "ho" => "Hiri Motu",
        "hr" => "Croatian",
        "ht" => "Haitian",
        "hu" => "Hungarian",
        "hy" => "Armenian",
        "hz" => "Herero",
        "ia" => "Interlingua (International Auxiliary Language Association)",
        "id" => "Indonesian",
        "ie" => "Interlingue",
        "ig" => "Igbo",
        "ii" => "Sichuan Yi",
        "ik" => "Inupiaq",
        "io" => "Ido",
        "is" => "Icelandic",
        "it" => "Italian",
        "iu" => "Inuktitut",
        "ja" => "Japanese",
        "jv" => "Javanese",
        "ka" => "Georgian",
        "kg" => "Kongo",
        "ki" => "Kikuyu",
        "kj" => "Kwanyama",
        "kk" => "Kazakh",
        "kl" => "Kalaallisut",
        "km" => "Khmer",
        "kn" => "Kannada",
        "ko" => "Korean",
        "kr" => "Kanuri",
        "ks" => "Kashmiri",
        "ku" => "Kurdish",
        "kv" => "Komi",
        "kw" => "Cornish",
        "ky" => "Kirghiz",
        "la" => "Latin",
        "lb" => "Luxembourgish",
        "lg" => "Ganda",
        "li" => "Limburgish",
        "ln" => "Lingala",
        "lo" => "Lao",
        "lt" => "Lithuanian",
        "lu" => "Luba-Katanga",
        "lv" => "Latvian",
        "mg" => "Malagasy",
        "mh" => "Marshallese",
        "mi" => "Maori",
        "mk" => "Macedonian",
        "ml" => "Malayalam",
        "mn" => "Mongolian",
        "mr" => "Marathi",
        "ms" => "Malay",
        "mt" => "Maltese",
        "my" => "Burmese",
        "na" => "Nauru",
        "nb" => "Norwegian Bokmal",
        "nd" => "North Ndebele",
        "ne" => "Nepali",
        "ng" => "Ndonga",
        "nl" => "Dutch",
        "nn" => "Norwegian Nynorsk",
        "no" => "Norwegian",
        "nr" => "South Ndebele",
        "nv" => "Navajo",
        "ny" => "Chichewa",
        "oc" => "Occitan",
        "oj" => "Ojibwa",
        "om" => "Oromo",
        "or" => "Oriya",
        "os" => "Ossetian",
        "pa" => "Panjabi",
        "pi" => "Pali",
        "pl" => "Polish",
        "ps" => "Pashto",
        "pt" => "Portuguese",
        "qu" => "Quechua",
        "rm" => "Raeto-Romance",
        "rn" => "Kirundi",
        "ro" => "Romanian",
        "ru" => "Russian",
        "rw" => "Kinyarwanda",
        "sa" => "Sanskrit",
        "sc" => "Sardinian",
        "sd" => "Sindhi",
        "se" => "Northern Sami",
        "sg" => "Sango",
        "si" => "Sinhala",
        "sk" => "Slovak",
        "sl" => "Slovenian",
        "sm" => "Samoan",
        "sn" => "Shona",
        "so" => "Somali",
        "sq" => "Albanian",
        "sr" => "Serbian",
        "ss" => "Swati",
        "st" => "Southern Sotho",
        "su" => "Sundanese",
        "sv" => "Swedish",
        "sw" => "Swahili",
        "ta" => "Tamil",
        "te" => "Telugu",
        "tg" => "Tajik",
        "th" => "Thai",
        "ti" => "Tigrinya",
        "tk" => "Turkmen",
        "tl" => "Tagalog",
        "tn" => "Tswana",
        "to" => "Tonga",
        "tr" => "Turkish",
        "ts" => "Tsonga",
        "tt" => "Tatar",
        "tw" => "Twi",
        "ty" => "Tahitian",
        "ug" => "Uighur",
        "uk" => "Ukrainian",
        "ur" => "Urdu",
        "uz" => "Uzbek",
        "ve" => "Venda",
        "vi" => "Vietnamese",
        "vo" => "Volapuk",
        "wa" => "Walloon",
        "wo" => "Wolof",
        "xh" => "Xhosa",
        "yi" => "Yiddish",
        "yo" => "Yoruba",
        "za" => "Zhuang",
        "zh" => "Chinese",
        "zu" => "Zulu"
    );
    if (isset($_codes[$code])) {
        return $_codes[$code];
    }
    return $code;
}

/**
 * Returns name of common web bots
 * @return bool|string
 */
function jrUser_get_bot_name()
{
    if (!class_exists('CrawlerDetect')) {
        require_once APP_DIR . "/modules/jrUser/contrib/crawler-detect/CrawlerDetect.php";
        require_once APP_DIR . "/modules/jrUser/contrib/crawler-detect/Fixtures/AbstractProvider.php";
        require_once APP_DIR . "/modules/jrUser/contrib/crawler-detect/Fixtures/Crawlers.php";
        require_once APP_DIR . "/modules/jrUser/contrib/crawler-detect/Fixtures/Exclusions.php";
        require_once APP_DIR . "/modules/jrUser/contrib/crawler-detect/Fixtures/Headers.php";
    }
    $cd = new CrawlerDetect;
    if ($cd->isCrawler()) {
        return "bot: " . $cd->getMatches();
    }
    return '';
}

/**
 * Save a daily statistic
 * @param $key string Unique key
 * @param $value int Value
 * @return bool
 */
function jrUser_save_daily_stat($key, $value)
{
    $dat = (int) strftime('%Y%m%d');
    $key = jrCore_db_escape($key);
    $val = (int) $value;
    $tbl = jrCore_db_table_name('jrUser', 'stat');
    $req = "INSERT IGNORE INTO {$tbl} (stat_date, stat_key, stat_value) VALUES ({$dat}, '{$key}', {$val})";
    return jrCore_db_query($req, 'COUNT');
}

/**
 * Run a whois lookup Query for an IP Address
 * @param $ip string IP Address
 * @return array|bool
 */
function jrUser_whois_lookup($ip)
{
    $_res = false;
    $_tmp = jrCore_load_url("http://whois.arin.net/rest/ip/{$ip}.txt", null, 'GET', 80, null, null, false, 10, $_SERVER['HTTP_USER_AGENT']);
    if ($_tmp && strlen($_tmp) > 5) {
        $_tmp = explode("\n", $_tmp);
        if ($_tmp && is_array($_tmp)) {
            $_res = array();
            foreach ($_tmp as $line) {
                $line = trim($line);
                if (strpos(' ' . $line, 'ERROR:201')) {
                    // Access denied...
                    return false;
                }
                if (strlen($line) > 0 && strpos($line, ':') && strpos($line, '#') !== 0) {
                    list($key, $val) = explode(':', $line);
                    $key = trim($key);
                    $val = trim($val);
                    if (strlen($val) > 0) {
                        $_res[$key] = trim($val);
                    }
                }
            }
        }
    }
    return $_res;
}

/**
 * Check if a given name is a reserved name
 * @param string $name
 * @returns bool
 */
function jrUser_is_reserved_name($name)
{
    $_names = array(
        'about',
        'access',
        'account',
        'accounts',
        'add',
        'address',
        'adm',
        'admin',
        'administration',
        'adult',
        'advertising',
        'affiliate',
        'affiliates',
        'ajax',
        'analytics',
        'android',
        'anon',
        'anonymous',
        'api',
        'app',
        'apps',
        'archive',
        'atom',
        'auth',
        'authentication',
        'avatar',
        'backup',
        'banner',
        'banners',
        'billing',
        'bin',
        'blog',
        'blogs',
        'board',
        'bot',
        'bots',
        'business',
        'cache',
        'cadastro',
        'calendar',
        'campaign',
        'careers',
        'cgi',
        'chat',
        'client',
        'cliente',
        'code',
        'comercial',
        'compare',
        'compras',
        'config',
        'connect',
        'contact',
        'contest',
        'create',
        'css',
        'dashboard',
        'data',
        'db',
        'delete',
        'demo',
        'design',
        'designer',
        'dev',
        'devel',
        'dir',
        'directory',
        'doc',
        'docs',
        'domain',
        'download',
        'downloads',
        'ecommerce',
        'edit',
        'editor',
        'email',
        'faq',
        'favorite',
        'feed',
        'feedback',
        'file',
        'files',
        'flog',
        'follow',
        'forgot',
        'forgotpass',
        'forgot_pass',
        'forgotpassword',
        'forgot_password',
        'forum',
        'forums',
        'free',
        'ftp',
        'gadget',
        'gadgets',
        'games',
        'group',
        'groups',
        'guest',
        'help',
        'home',
        'homepage',
        'host',
        'hosting',
        'hostname',
        'hpg',
        'html',
        'http',
        'httpd',
        'https',
        'image',
        'images',
        'imap',
        'img',
        'index',
        'indice',
        'info',
        'information',
        'intranet',
        'invite',
        'ipad',
        'iphone',
        'irc',
        'java',
        'javascript',
        'job',
        'jobs',
        'js',
        'knowledgebase',
        'list',
        'lists',
        'log',
        'login',
        'log_in',
        'logon',
        'log_on',
        'logout',
        'log_out',
        'logs',
        'mail',
        'mail1',
        'mail2',
        'mail3',
        'mail4',
        'mail5',
        'mailer',
        'mailing',
        'manager',
        'marketing',
        'master',
        'me',
        'media',
        'message',
        'messenger',
        'microblog',
        'microblogs',
        'mine',
        'mob',
        'mobile',
        'movie',
        'movies',
        'mp3',
        'msg',
        'msn',
        'music',
        'musicas',
        'mx',
        'my',
        'mysql',
        'name',
        'named',
        'net',
        'network',
        'new',
        'news',
        'newsletter',
        'nick',
        'nickname',
        'notes',
        'noticias',
        'ns',
        'ns1',
        'ns2',
        'ns3',
        'ns4',
        'old',
        'online',
        'operator',
        'order',
        'orders',
        'page',
        'pager',
        'pages',
        'panel',
        'pass',
        'passchange',
        'pass_change',
        'password',
        'passwordchange',
        'password_change',
        'perl',
        'photo',
        'photoalbum',
        'photos',
        'php',
        'pic',
        'pics',
        'plugin',
        'plugins',
        'pop',
        'pop3',
        'post',
        'postfix',
        'postmaster',
        'posts',
        'profile',
        'project',
        'projects',
        'promo',
        'pub',
        'public',
        'python',
        'random',
        'register',
        'registration',
        'root',
        'rss',
        'ruby',
        'sale',
        'sales',
        'sample',
        'samples',
        'script',
        'scripts',
        'search',
        'secure',
        'security',
        'send',
        'service',
        'setting',
        'settings',
        'setup',
        'shop',
        'signin',
        'signup',
        'site',
        'sitemap',
        'sites',
        'smtp',
        'soporte',
        'sql',
        'ssh',
        'stage',
        'staging',
        'start',
        'stat',
        'static',
        'stats',
        'status',
        'store',
        'stores',
        'subdomain',
        'subscribe',
        'suporte',
        'support',
        'system',
        'tablet',
        'tablets',
        'talk',
        'task',
        'tasks',
        'tech',
        'telnet',
        'test',
        'test1',
        'test2',
        'test3',
        'teste',
        'tests',
        'theme',
        'themes',
        'tmp',
        'todo',
        'tools',
        'tv',
        'update',
        'upload',
        'url',
        'usage',
        'user',
        'username',
        'usuario',
        'vendas',
        'video',
        'videos',
        'visitor',
        'web',
        'webmail',
        'webmaster',
        'website',
        'websites',
        'win',
        'workshop',
        'ww',
        'wws',
        'www',
        'www1',
        'www2',
        'www3',
        'www4',
        'www5',
        'www6',
        'www7',
        'wwws',
        'wwww',
        'xpg',
        'xxx',
        'you',
        'yourdomain',
        'yourname',
        'yoursite',
        'yourusername'
    );
    if (in_array(jrCore_str_to_lower(trim($name)), $_names)) {
        return true;
    }
    return false;
}

//------------------------------------
// MySQL Session Handler replacement
//------------------------------------

/**
 * @ignore
 * Open a MySQL Session
 * @param $path string
 * @param $name string
 * @return bool
 */
function _jrUser_mysql_session_open($path, $name)
{
    return true;
}

/**
 * @ignore
 * Close a MySQL Session
 * @return bool
 */
function _jrUser_mysql_session_close()
{
    return true;
}

/**
 * @ignore
 * Read an active MySQL Session
 * @param $sid String Current Session ID
 * @return string
 */
function _jrUser_mysql_session_read($sid)
{
    global $_conf;
    $exp = ($_conf['jrUser_session_expire_min'] * 60);
    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "SELECT session_sync, session_data FROM {$tbl} WHERE session_id = '" . jrCore_db_escape($sid) . "' AND session_updated > (UNIX_TIMESTAMP() - {$exp})";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        jrCore_set_flag('user_session_sync', $_rt['session_sync']);
        return $_rt['session_data'];
    }
    return '';
}

/**
 * @ignore
 * Write an existing User session
 * @param $sid string Session ID to write to
 * @param $val string Session Value to save (set automatically by PHP)
 * @return bool
 */
function _jrUser_mysql_session_write($sid, $val)
{
    global $_conf, $_post;

    // check for user and profile id's
    if (!isset($_SESSION['_user_id']) || !is_numeric($_SESSION['_user_id'])) {
        $_SESSION['_user_id'] = '0';
    }
    if (!isset($_SESSION['_profile_id']) || !is_numeric($_SESSION['_profile_id'])) {
        $_SESSION['_profile_id'] = '0';
    }

    // Check for bot sessions
    if (!empty($_SESSION['user_name'])) {
        $nam = mb_substr($_SESSION['user_name'], 0, 127);
    }
    else {
        $nam = jrUser_get_bot_name();
        if (strpos($nam, 'bot:') === 0) {
            if (isset($_conf['jrUser_bot_sessions']) && $_conf['jrUser_bot_sessions'] == 'off') {
                // We're not doing sessions for bots
                return true;
            }
        }
    }
    $nam = jrCore_db_escape($nam);

    // Get user action
    $ad1 = '';
    $ad2 = '';
    $ad3 = '';
    $act = jrCore_get_flag('jruser_ignore_action');
    if (!$act) {
        if (!isset($_post['_uri']) || strlen($_post['_uri']) === 0) {
            $_post['_uri'] = '/';
        }
        if (!strpos($_post['_uri'], '__ajax') && !strpos($_post['_uri'], 'icon_css') && !strpos($_post['_uri'], '/image/')) {
            $act = jrCore_db_escape(substr(jrCore_strip_emoji($_post['_uri'], false), 0, 255));
            $ad1 = ',session_user_action';
            $ad2 = ",'{$act}'";
            $ad3 = ",session_user_action = '{$act}'";
        }
    }
    $qid = (isset($_SESSION['profile_quota_id']) && is_numeric($_SESSION['profile_quota_id'])) ? intval($_SESSION['profile_quota_id']) : 0;
    $val = jrCore_db_escape($val);
    $uip = jrCore_db_escape(jrCore_get_ip());

    $grp = 'user';
    if (isset($_SESSION['user_group']) && ($_SESSION['user_group'] == 'master' || $_SESSION['user_group'] == 'admin')) {
        $grp = $_SESSION['user_group'];
    }

    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "INSERT INTO {$tbl} (session_id,session_updated,session_user_id,session_user_name,session_user_group,session_profile_id,session_quota_id,session_user_ip{$ad1},session_data)
            VALUES ('{$sid}',UNIX_TIMESTAMP(),'{$_SESSION['_user_id']}','{$nam}','{$grp}','{$_SESSION['_profile_id']}','{$qid}','{$uip}'{$ad2},'{$val}')
            ON DUPLICATE KEY UPDATE session_updated = UNIX_TIMESTAMP(),session_user_id = '{$_SESSION['_user_id']}',session_user_name = '{$nam}',session_user_group = '{$grp}',
            session_profile_id = '{$_SESSION['_profile_id']}',session_quota_id = '{$qid}',session_user_ip = '{$uip}'{$ad3},session_sync = 0,session_data = VALUES(session_data)";
    jrCore_db_query($req);
    return true;
}

/**
 * @ignore
 * Destroy an active session
 * @param $sid string SessionID
 * @return bool
 */
function _jrUser_mysql_session_destroy($sid)
{
    // Destroy a session
    $sid = jrCore_db_escape($sid);
    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "DELETE FROM {$tbl} WHERE session_id = '{$sid}'";
    jrCore_db_query($req);
    return true;
}

/**
 * @ignore
 * Garbage collection for sessions
 * @param $max integer length of time session can be valid for
 * @return bool
 */
function _jrUser_mysql_session_collect($max)
{
    // GC handled in JR process exit listener
    return true;
}

/**
 * @ignore
 * Delete an individual session ID
 * @param string $sid Session ID to remove
 * @return mixed
 */
function _jrUser_mysql_session_delete_session_id($sid)
{
    $sid = jrCore_db_escape($sid);
    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "DELETE FROM {$tbl} WHERE session_id = '{$sid}'";
    return jrCore_db_query($req, 'COUNT');
}

/**
 * @ignore
 * Remove all sessions for a specific user id
 * @param $user_id mixed User ID or array of User ID's
 * @return mixed
 */
function _jrUser_mysql_session_remove($user_id)
{
    // Remove all session entries for a user id
    $tbl = jrCore_db_table_name('jrUser', 'session');
    if (!is_array($user_id)) {
        $user_id = array(intval($user_id));
    }
    else {
        foreach ($user_id as $k => $uid) {
            if (!jrCore_checktype($uid, 'number_nz')) {
                unset($user_id[$k]);
            }
        }
        if (count($user_id) === 0) {
            return false;
        }
    }
    $req = "SELECT session_id FROM {$tbl} WHERE session_user_id IN(" . implode(',', $user_id) . ')';
    $_rt = jrCore_db_query($req, 'session_id');
    if ($_rt && is_array($_rt)) {
        $req = "DELETE FROM {$tbl} WHERE session_id IN('" . implode("','", array_keys($_rt)) . "')";
        return jrCore_db_query($req, 'COUNT');
    }
    return false;
}

/**
 * @ignore
 * Remove all sessions for a specific user id except active session
 * @param $user_id mixed User ID or array of User ID's
 * @param $active_sid string Active session ID
 * @return mixed
 */
function _jrUser_mysql_session_remove_all_other_sessions($user_id, $active_sid)
{
    // Remove all session entries for a user id
    $sid = jrCore_db_escape($active_sid);
    $uid = (int) $user_id;
    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "SELECT session_id FROM {$tbl} WHERE session_user_id = '{$uid}' AND session_id != '{$sid}'";
    $_rt = jrCore_db_query($req, 'session_id');
    if ($_rt && is_array($_rt)) {
        $req = "DELETE FROM {$tbl} WHERE session_id IN('" . implode("','", array_keys($_rt)) . "')";
        jrCore_db_query($req);
    }
    return true;
}

/**
 * @ignore
 * Check if a given Session ID is a valid Session ID
 * @param $sid string Session ID
 * @return mixed
 */
function _jrUser_mysql_session_is_valid_session($sid)
{
    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "SELECT session_data FROM {$tbl} WHERE session_id = '" . jrCore_db_escape($sid) . "' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    return ($_rt && isset($_rt['session_data']{0})) ? $_rt['session_data'] : false;
}

/**
 * Set the session_sync flag for a Quota ID
 * @param $quota_id mixed Quota ID or array of Quota IDs
 * @param $state string on|off
 * @return mixed
 */
function _jrUser_mysql_session_sync_for_quota($quota_id, $state)
{
    $flg = ($state == 'on') ? 1 : 0;
    $_qi = array();
    if (jrCore_checktype($quota_id, 'number_nz')) {
        $_qi[] = (int) $quota_id;
    }
    elseif (is_array($quota_id)) {
        foreach ($quota_id as $qid) {
            if (jrCore_checktype($qid, 'number_nz')) {
                $_qi[] = (int) $qid;
            }
        }
    }
    if (count($_qi) === 0) {
        return false;
    }
    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "SELECT session_id FROM {$tbl} WHERE session_quota_id IN(" . implode(',', $_qi) . ')';
    $_rt = jrCore_db_query($req, 'session_id');
    if ($_rt && is_array($_rt)) {
        $req = "UPDATE {$tbl} SET session_sync = {$flg} WHERE session_id IN('" . implode("','", array_keys($_rt)) . "')";
        return jrCore_db_query($req);
    }
    return false;
}

/**
 * Set the session_sync flag for a User ID
 * @param int $user_id User ID to session sync flag for
 * @param $state string on|off
 * @return mixed
 */
function _jrUser_mysql_session_sync_for_user_id($user_id, $state)
{
    $flg = ($state == 'on') ? 1 : 0;
    $_us = array();
    if (jrCore_checktype($user_id, 'number_nz')) {
        $_us[] = (int) $user_id;
    }
    elseif (is_array($user_id)) {
        foreach ($user_id as $uid) {
            if (jrCore_checktype($uid, 'number_nz')) {
                $_us[] = (int) $uid;
            }
        }
    }
    if (count($_us) === 0) {
        return false;
    }
    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "SELECT session_id FROM {$tbl} WHERE session_user_id IN(" . implode(',', $_us) . ')';
    $_rt = jrCore_db_query($req, 'session_id');
    if ($_rt && is_array($_rt)) {
        $req = "UPDATE {$tbl} SET session_sync = {$flg} WHERE session_id IN('" . implode("','", array_keys($_rt)) . "')";
        return jrCore_db_query($req);
    }
    return false;
}

/**
 * @ignore
 * Get number of active online users
 * @param $length int Max number of seconds with no activity a session is considered "active"
 * @param $type string type of online user count to get = combined|bot|user|visitor
 * @return int
 */
function _jrUser_mysql_session_online_user_count($length, $type = 'combined')
{
    global $_conf;
    $tbl = jrCore_db_table_name('jrUser', 'session');
    switch ($type) {
        case 'bot':
            if (isset($_conf['jrUser_bot_sessions']) && $_conf['jrUser_bot_sessions'] == 'off') {
                // We are NOT tracking bot sessions - return 0
                return 0;
            }
            $req = "SELECT COUNT(DISTINCT(session_user_ip)) AS online FROM {$tbl} WHERE session_updated > (UNIX_TIMESTAMP() - " . intval($length) . ") AND session_user_name LIKE 'bot:%'";
            break;
        case 'visitor':
            // user_id = 0 and is not a bot
            $req = "SELECT COUNT(DISTINCT(session_user_ip)) AS online FROM {$tbl} WHERE session_updated > (UNIX_TIMESTAMP() - " . intval($length) . ") AND session_user_id = 0 AND session_user_name NOT LIKE 'bot:%'";
            break;
        case 'user':
            // user_id > 0
            $req = "SELECT COUNT(DISTINCT(session_user_ip)) AS online FROM {$tbl} WHERE session_updated > (UNIX_TIMESTAMP() - " . intval($length) . ") AND session_user_id > 0";
            break;
        default:
            // combined user + visitor
            $req = "SELECT COUNT(DISTINCT(session_user_ip)) AS online FROM {$tbl} WHERE session_updated > (UNIX_TIMESTAMP() - " . intval($length) . ") AND session_user_name NOT LIKE 'bot:%'";
            break;
    }
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt) && isset($_rt['online'])) {
        return intval($_rt['online']);
    }
    return 0;
}

/**
 * @ignore
 * Return IDs of active users
 * @param int $length number of seconds of inactivity before user is "offline"
 * @param array $_ids only check the given IDs
 * @return mixed
 */
function _jrUser_mysql_session_online_user_ids($length = 900, $_ids = null)
{
    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "SELECT session_user_id AS i, session_updated AS u FROM {$tbl} WHERE session_updated > (UNIX_TIMESTAMP() - " . intval($length) . ")";
    if (is_array($_ids)) {
        foreach ($_ids as $k => $uid) {
            $_ids[$k] = (int) $uid;
        }
        $req .= " AND session_user_id IN(" . implode(',', $_ids) . ')';
    }
    $_rt = jrCore_db_query($req, 'i', false, 'u');
    if ($_rt && is_array($_rt)) {
        return $_rt;
    }
    return false;
}

/**
 * @ignore
 * Return information about users that are active and online
 * @param $length int Max number of seconds with no activity a session is considered "active"
 * @param $search string Optional Search String
 * @return mixed
 */
function _jrUser_mysql_session_online_user_info($length = 900, $search = null)
{
    $sst = '';
    if (!is_null($search) && strlen($search) > 0) {
        $sst = jrCore_db_escape($search);
        $sst = " AND (session_user_action LIKE '%{$sst}%' OR session_user_name LIKE '%{$sst}%' OR session_user_ip LIKE '%{$sst}%')";
    }
    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "SELECT session_id AS i, session_updated AS u, session_user_id AS uid, session_user_name AS n, session_user_group AS g,
                   session_profile_id AS pid, session_quota_id AS q, session_user_ip AS ip, session_user_action AS a, session_sync AS s FROM {$tbl}
             WHERE session_updated > (UNIX_TIMESTAMP() - " . intval($length) . "){$sst}
             ORDER BY session_updated DESC, session_user_id DESC";
    $_ss = jrCore_db_query($req, 'NUMERIC');
    if ($_ss && is_array($_ss)) {
        $_rt = array();
        foreach ($_ss as $s) {
            $key = "{$s['n']}:{$s['ip']}:{$s['uid']}";
            if (!isset($_rt[$key])) {
                $_rt[$key] = array(
                    'session_id'          => $s['i'],
                    'session_updated'     => $s['u'],
                    'session_user_id'     => $s['uid'],
                    'session_user_name'   => $s['n'],
                    'session_user_group'  => $s['g'],
                    'session_profile_id'  => $s['pid'],
                    'session_quota_id'    => $s['q'],
                    'session_user_ip'     => $s['ip'],
                    'session_user_action' => $s['a'],
                    'session_sync'        => $s['s']
                );
            }
        }
        if (count($_rt) > 0) {
            return array_values($_rt);
        }
    }
    return false;
}

//------------------------------------
// SMARTY functions
//------------------------------------

/**
 * Get a Key from a user's home profile
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrUser_home_profile_key($params, $smarty)
{
    if (!isset($params['key']{0})) {
        return '';
    }
    $tmp = jrUser_get_profile_home_key($params['key']);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $tmp);
        return '';
    }
    return $tmp;
}

/**
 * Get all of a user's profile home data assigned to a variable
 * @param $params array
 * @param $smarty object
 * @return string
 */
function smarty_function_jrUser_get_profile_home_data($params, $smarty)
{
    if (empty($params['assign'])) {
        return jrCore_smarty_missing_error('assign');
    }
    $tmp = jrUser_get_profile_home_data();
    $smarty->assign($params['assign'], $tmp);
    return '';
}

/**
 * Get User's Online Status
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrUser_online_status($params, $smarty)
{
    global $_conf;
    if (isset($params['user_id']) && strlen($params['user_id']) > 0) {
        $type = 'user';
        if (strpos(' ' . $params['user_id'], ',')) {
            $_us = explode(',', $params['user_id']);
            foreach ($_us as $k => $uid) {
                if (!jrCore_checktype($uid, 'number_nz')) {
                    unset($_us[$k]);
                }
            }
        }
        else {
            $_us = array($params['user_id']);
        }
        if (count($_us) === 0) {
            return 'jrUser_online_status: invalid user_id parameter';
        }
        $osid = implode(',', $_us);
    }
    elseif (isset($params['profile_id']) && jrCore_checktype($params['profile_id'], 'number_nz')) {
        $type = 'profile';
        $osid = (int) $params['profile_id'];
    }
    else {
        return 'jrUser_online_status: user_id or profile_id parameter required';
    }
    if (isset($params['template']{0}) && !strpos($params['template'], '.tpl')) {
        $cdr = jrCore_get_module_cache_dir($_conf['jrCore_active_skin']);
        $md5 = md5($params['template']);
        if (!is_file("{$cdr}/{$md5}.tpl")) {
            jrCore_write_to_file("{$cdr}/{$md5}.tpl", $params['template']);
        }
        $params['template'] = $md5;
    }
    else {
        $params['template'] = 'default';
    }
    $_rp            = array(
        'type'      => $type,
        'unique_id' => $osid,
        'template'  => $params['template'],
        'id'        => 'u' . substr(md5(microtime()), 0, 6)
    );
    $_rp['seconds'] = 900; // 15 minutes default
    if (isset($params['seconds']) && jrCore_checktype($params['seconds'], 'number_nz')) {
        $_rp['seconds'] = (int) $params['seconds'];
    }
    $tmp = jrCore_parse_template('online_status.tpl', $_rp, 'jrUser');
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $tmp);
        return '';
    }
    return $tmp;
}

/**
 * Show Users Online
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrUser_whos_online($params, $smarty)
{
    if (!isset($params['template'])) {
        return 'jrUser_whos_online: template parameter required';
    }

    $mod = null;
    if (isset($params['module']) && jrCore_module_is_active($params['module'])) {
        $mod = $params['module'];
    }

    // Check for cache
    $key = json_encode($params);
    if ($tmp = jrCore_is_cached('jrUser', $key)) {
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], $tmp);
            return '';
        }
        return $tmp;
    }

    // Initialize counters
    $_rp = array(
        'all_count'           => 0,
        'visitor_count'       => 0,
        'user_count'          => 0,
        'admin_count'         => 0,
        'master_count'        => 0,
        'logged_in_count'     => 0,
        'not_logged_in_count' => 0
    );

    // Get active session users
    $tmp = '';
    $tim = 900; // default to active in last 15 minutes
    if (isset($params['length']) && jrCore_checktype($params['length'], 'number_nz')) {
        $tim = intval($params['length'] * 60); //  override (in minutes)
    }
    $_su = jrUser_session_online_user_info($tim);
    if ($_su && is_array($_su)) {
        $_id = array();
        foreach ($_su as $_session) {
            if ($_session['session_user_id'] > 0) {
                $_id["{$_session['session_user_id']}"] = $_session;
            }
            else {
                if (!isset($_rp['visitor'])) {
                    $_rp['visitor'] = array();
                }
                $_rp['visitor_count']++;
                $_rp['not_logged_in_count']++;
                $_rp['visitor'][] = $_session;
            }
        }
        if (count($_id) > 0) {
            $_sp = array(
                'search'      => array(
                    '_user_id in ' . implode(',', array_keys($_id)),
                ),
                'return_keys' => array('_created', '_updated', '_profile_id', '_user_id', 'user_name', 'user_language', 'user_group'),
                'limit'       => 10000
            );
            $_su = jrCore_db_search_items('jrUser', $_sp);
            if ($_su && isset($_su['_items'])) {
                $_pi = array();
                $_gr = array();
                foreach ($_su['_items'] as $_usr) {
                    unset($_id["{$_usr['_user_id']}"]['session_uniq']);
                    $_rp['all_count']++;
                    $_rp['logged_in_count']++;
                    $_rp["{$_usr['user_group']}_count"]++;
                    $_rp["{$_usr['user_group']}"]["{$_usr['_user_id']}"] = isset($_id["{$_usr['_user_id']}"]) ? array_merge($_usr, $_id["{$_usr['_user_id']}"]) : $_usr;
                    if (!isset($_pi["{$_usr['_profile_id']}"])) {
                        $_pi["{$_usr['_profile_id']}"] = array();
                    }
                    $_pi["{$_usr['_profile_id']}"]["{$_usr['_user_id']}"] = 1;
                    $_gr["{$_usr['_profile_id']}"]                        = $_usr['user_group'];
                }
                unset($_id);
                if (count($_pi) > 0) {
                    $_sp = array(
                        'search'      => array(
                            '_profile_id in ' . implode(',', array_keys($_pi)),
                        ),
                        'return_keys' => array('_profile_id', 'profile_name', 'profile_url'),
                        'limit'       => 10000
                    );
                    $_su = jrCore_db_search_items('jrProfile', $_sp);
                    if (isset($_su) && isset($_su['_items'])) {
                        foreach ($_su['_items'] as $_pr) {
                            $grp = $_gr["{$_pr['_profile_id']}"];
                            if (isset($_pi["{$_pr['_profile_id']}"]) && is_array($_pi["{$_pr['_profile_id']}"])) {
                                foreach ($_pi["{$_pr['_profile_id']}"] as $uid => $one) {
                                    $_rp[$grp][$uid] = array_merge($_rp[$grp][$uid], $_pr);
                                }
                            }
                        }
                        unset($_pi);
                    }
                }
            }
            $_rp['all_count'] += (int) $_rp['not_logged_in_count'];
            // Parse template and cache results
            $tmp = jrCore_parse_template($params['template'], $_rp, $mod);
            jrCore_add_to_cache('jrUser', $key, $tmp);
        }
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $tmp);
        return '';
    }
    return $tmp;
}
