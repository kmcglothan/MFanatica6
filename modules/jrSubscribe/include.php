<?php
/**
 * Jamroom Subscriptions module
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
function jrSubscribe_meta()
{
    return array(
        'name'        => 'Subscriptions',
        'url'         => 'subscribe',
        'version'     => '1.0.4',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Adds support for creating and selling recurring subscriptions',
        'category'    => 'ecommerce',
        'requires'    => 'jrPayment',
        'license'     => 'jcl'
    );
}

/**
 * init
 */
function jrSubscribe_init()
{
    jrCore_register_module_feature('jrCore', 'css', 'jrSubscribe', 'jrSubscribe.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrSubscribe', 'jrSubscribe.js');

    jrCore_register_module_feature('jrCore', 'tool_view', 'jrSubscribe', 'browse', array('Browse Plans', 'Browse and Configure available subscription plans'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrSubscribe', 'subscribers', array('Browse Subscribers', 'Browse and Modify Profile subscriptions'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrSubscribe', 'import', array('Import Subscribers', 'Import existing subscriptions from FoxyCart'));

    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrSubscribe', 'browse', 'Plans');
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrSubscribe', 'subscribers', 'Subscribers');

    // Allow admin to customize plan forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrSubscribe', 'plan_create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrSubscribe', 'plan_modify');

    jrCore_register_event_listener('jrCore', 'hourly_maintenance', 'jrSubscribe_hourly_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrSubscribe_daily_maintenance_listener');

    jrCore_register_event_listener('jrUser', 'account_tabs', 'jrSubscribe_account_tabs_listener');

    jrCore_register_event_listener('jrPayment', 'plugin_function', 'jrSubscribe_plugin_function_listener');
    jrCore_register_event_listener('jrPayment', 'purchase_entry', 'jrSubscribe_purchase_entry_listener');
    jrCore_register_event_listener('jrPayment', 'payment_entry', 'jrSubscribe_payment_entry_listener');
    jrCore_register_event_listener('jrPayment', 'txn_entry', 'jrSubscribe_txn_entry_listener');
    jrCore_register_event_listener('jrPayment', 'txn_detail_entry', 'jrSubscribe_txn_detail_entry_listener');
    jrCore_register_event_listener('jrPayment', 'system_reset_options', 'jrSubscribe_system_reset_options_listener');
    jrCore_register_event_listener('jrPayment', 'system_reset_save', 'jrSubscribe_system_reset_save_listener');
    jrCore_register_event_listener('jrPayment', 'payment_success_page', 'jrSubscribe_payment_success_page_listener');

    jrCore_register_event_trigger('jrSubscribe', 'subscription_created', 'Fired when a new subscription is created');
    jrCore_register_event_trigger('jrSubscribe', 'subscription_extended', 'Fired when an existing subscription is extended');
    jrCore_register_event_trigger('jrSubscribe', 'subscription_canceled', 'Fired when an existing subscription is canceled');
    jrCore_register_event_trigger('jrSubscribe', 'subscription_deleted', 'Fired when an existing subscription is deleted');
    jrCore_register_event_trigger('jrSubscribe', 'subscription_plan_created', 'Fired when a new subscription plan is created');
    jrCore_register_event_trigger('jrSubscribe', 'subscription_plan_updated', 'Fired when an existing subscription plan is updated');
    jrCore_register_event_trigger('jrSubscribe', 'subscribe_success_page', 'Fired when a user views the subscription success page');

    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrSubscribe', 'subscribers');

    // Link to "Your Subscriptions"
    $_tmp = array(
        'group'    => 'user',
        'label'    => 1,
        'url'      => 'active_subscription',
        'function' => 'jrSubscribe_skin_menu_item_function'
    );
    jrCore_register_module_feature('jrCore', 'skin_menu_item', 'jrSubscribe', 'active_subscription', $_tmp);

    // Subscriptions
    $_tmp = array(
        'label'       => 2,
        'quota_check' => false
    );
    jrCore_register_module_feature('jrUser', 'account_tab', 'jrSubscribe', 'active_subscription', $_tmp);

    $_tmp = array(
        'label' => 'Subscription Created',
        'help'  => 'When a new subscription is created would you like to be notified?',
        'group' => 'admin'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrSubscribe', 'subscription_created', $_tmp);

    $_tmp = array(
        'label' => 'Subscription Extended',
        'help'  => 'When an existing subscription is extended would you like to be notified?',
        'group' => 'admin'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrSubscribe', 'subscription_extended', $_tmp);

    $_tmp = array(
        'label' => 'Subscription Canceled',
        'help'  => 'When an existing subscription is canceled would you like to be notified?',
        'group' => 'admin'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrSubscribe', 'subscription_canceled', $_tmp);

    // We provide some newsletter recipient options
    jrCore_register_event_listener('jrNewsLetter', 'newsletter_filters', 'jrSubscribe_newsletter_filters_listener');

    // System reset listener
    jrCore_register_event_listener('jrDeveloper', 'reset_system', 'jrSubscribe_reset_system_listener');

    // We provide some dashboard panels
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrSubscribe', 'active subscriptions', 'jrSubscribe_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrSubscribe', 'free subscriptions', 'jrSubscribe_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrSubscribe', 'new subscriptions - 1 day', 'jrSubscribe_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrSubscribe', 'new subscriptions - 7 days', 'jrSubscribe_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrSubscribe', 'new subscriptions - 30 days', 'jrSubscribe_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrSubscribe', 'subscription income', 'jrSubscribe_dashboard_panels');

    // Graph Support
    $_tmp = array(
        'title'    => 'Daily Subscribers',
        'function' => 'jrSubscribe_graph_active_daily_subscribers',
        'group'    => 'admin'
    );
    jrCore_register_module_feature('jrGraph', 'graph_config', 'jrSubscribe', 'active_daily_subscribers', $_tmp);

    $_tmp = array(
        'title'    => 'Daily Subscription Value',
        'function' => 'jrSubscribe_graph_daily_subscription_value',
        'group'    => 'admin'
    );
    jrCore_register_module_feature('jrGraph', 'graph_config', 'jrSubscribe', 'daily_subscription_value', $_tmp);

    // Daily maintenance queue worker
    jrCore_register_queue_worker('jrSubscribe', 'daily_maintenance', 'jrSubscribe_daily_maintenance_worker', 1, 1, 7200, LOW_PRIORITY_QUEUE);
    jrCore_register_queue_worker('jrSubscribe', 'hourly_maintenance', 'jrSubscribe_hourly_maintenance_worker', 1, 1, 3500, LOW_PRIORITY_QUEUE);

    // Plugin Tasks worker
    jrCore_register_queue_worker('jrSubscribe', 'subscription_tasks', 'jrSubscribe_subscription_tasks_worker', 0, 1, 86400, NORMAL_PRIORITY_QUEUE);

    return true;
}

//---------------------------------------
// QUEUE WORKER
//---------------------------------------

/**
 * Daily Maintenance
 * @param array $_queue
 * @return bool
 */
function jrSubscribe_daily_maintenance_worker($_queue)
{
    // Process expired subscriptions
    $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
    $req = "SELECT *, UNIX_TIMESTAMP() AS tnow FROM {$tbl} WHERE sub_expires < UNIX_TIMESTAMP() AND sub_status IN('active','canceled','unpaid')";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        // We have found subscriptions that we can _potentially_ cancel
        // but it depends on the grace period configured in the profile subscription
        $_pl = array();
        foreach ($_rt as $_sub) {
            $eml = false;
            $pid = (int) $_sub['sub_profile_id'];
            $lid = (int) $_sub['sub_plan_id'];
            if (!isset($_pl[$lid])) {
                $_pl[$lid] = jrCore_db_get_item('jrSubscribe', $lid, true);
            }
            // Make sure we have a minimum of 1 day grace period to account for gateway notification
            if (!isset($_pl[$lid]['sub_grace_period']) || !jrCore_checktype($_pl[$lid]['sub_grace_period'], 'number_nz')) {
                $_pl[$lid]['sub_grace_period'] = 1;
            }
            if ($_sub['sub_status'] == 'canceled') {
                // No grace period for canceled - cancel now
                jrSubscribe_delete_subscription($pid);
                $eml = true;
            }
            else {
                // Figure out how far we are until we expire
                $mod = (int) ($_pl[$lid]['sub_grace_period'] * 86400);
                if (($_sub['sub_expires'] + $mod) < $_sub['tnow']) {
                    // We have exceeded the grace period allowed - cancel
                    jrSubscribe_delete_subscription($pid);
                    $eml = true;
                }
            }
            if ($eml) {
                jrCore_logger('INF', "Subscribe: subscription for profile_id {$pid} has been canceled due to expiration", $_sub);
                if ($_pr = jrCore_db_get_item('jrProfile', $pid, true)) {
                    jrSubscribe_notify_user($_pr['_user_id'], $_sub['sub_plan_id'], 'subscription_canceled');
                }
            }
        }
    }

    // Extend expiring FREE subscriptions by 30 days
    $add = (30 * 86400);
    $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
    $req = "UPDATE {$tbl} SET sub_expires = (UNIX_TIMESTAMP() + {$add}) WHERE sub_expires < UNIX_TIMESTAMP() AND sub_status = 'free'";
    jrCore_db_query($req);

    // Save subscription statistics
    $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
    $req = "SELECT sub_status, COUNT(sub_id) AS i FROM {$tbl} GROUP BY sub_status";
    $_rt = jrCore_db_query($req, 'sub_status', false, 'i');
    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $s => $i) {
            jrCore_create_stat_entry('jrSubscribe', 'daily_subscription_stat', "{$s}_count", 0, 0, '127.0.0.1', true, $i);
        }
    }

    // Total value of all active subs
    $req = "SELECT SUM(sub_amount) AS total_sub_amount FROM {$tbl} WHERE sub_status = 'active'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        jrCore_create_stat_entry('jrSubscribe', 'daily_subscription_stat', 'active_total', 0, 0, '127.0.0.1', true, $_rt['total_sub_amount']);
    }

    // Average value of active subscriptions
    $req = "SELECT ROUND(SUM(sub_amount) / COUNT(sub_id), 0) AS avg_sub_amount FROM {$tbl} WHERE sub_status = 'active'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        jrCore_create_stat_entry('jrSubscribe', 'daily_subscription_stat', 'active_average', 0, 0, '127.0.0.1', true, $_rt['avg_sub_amount']);
    }

    // Average daily subscription length
    $req = "SELECT ROUND(AVG(UNIX_TIMESTAMP() - sub_created), 0) AS avg_sub_length FROM {$tbl} WHERE sub_status = 'active'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        jrCore_create_stat_entry('jrSubscribe', 'daily_subscription_stat', 'active_length', 0, 0, '127.0.0.1', true, $_rt['avg_sub_length']);
    }

    // Run plugin maintenance
    jrSubscribe_run_plugin_function('daily_maintenance');
    return true;
}

/**
 * Hourly Maintenance
 * @param array $_queue
 * @return bool
 */
function jrSubscribe_hourly_maintenance_worker($_queue)
{
    global $_conf;
    // Send notification reminders for upcoming subscription payments
    if (isset($_conf['jrSubscribe_upcoming_notify']) && jrCore_checktype($_conf['jrSubscribe_upcoming_notify'], 'number_nz')) {
        $old = (intval($_conf['jrSubscribe_upcoming_notify']) * 86400);
        $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
        $req = "SELECT * FROM {$tbl} WHERE sub_expires > UNIX_TIMESTAMP() AND sub_expires < (UNIX_TIMESTAMP() + {$old}) AND sub_status = 'active'";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt)) {
            foreach ($_rt as $_sub) {
                $pid = (int) $_sub['sub_profile_id'];
                $_md = jrSubscribe_get_sub_meta_data($pid, $_sub);
                if (!$_md || !is_array($_md)) {
                    $_md = array();
                }
                if (!isset($_md['upcoming_notice_sent'])) {
                    if ($_pr = jrCore_db_get_item('jrProfile', $pid, true)) {
                        jrSubscribe_notify_user($_pr['_user_id'], $_sub['sub_plan_id'], 'subscription_upcoming_payment');
                        jrSubscribe_save_sub_metadata_key($pid, 'upcoming_notice_sent', 1);
                        jrCore_logger('INF', "Subscribe: notified profile_id {$pid} about upcoming subscription payment", $_sub);
                    }
                }
            }
        }

    }

    // Email CANCELED users that their subscription is about to end
    if (isset($_conf['jrSubscribe_cancel_notify']) && jrCore_checktype($_conf['jrSubscribe_cancel_notify'], 'number_nz')) {
        $old = (intval($_conf['jrSubscribe_cancel_notify']) * 86400);
        $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
        $req = "SELECT * FROM {$tbl} WHERE sub_expires > UNIX_TIMESTAMP() AND sub_expires < (UNIX_TIMESTAMP() + {$old}) AND sub_status = 'canceled'";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt)) {
            foreach ($_rt as $_sub) {
                $pid = (int) $_sub['sub_profile_id'];
                $_md = jrSubscribe_get_sub_meta_data($pid, $_sub);
                if (!$_md || !is_array($_md)) {
                    $_md = array();
                }
                if (!isset($_md['cancel_notice_sent'])) {
                    if ($_pr = jrCore_db_get_item('jrProfile', $pid, true)) {
                        jrSubscribe_notify_user($_pr['_user_id'], $_sub['sub_plan_id'], 'subscription_ending');
                        jrSubscribe_save_sub_metadata_key($pid, 'cancel_notice_sent', 1);
                        jrCore_logger('INF', "Subscribe: notified profile_id {$pid} about pending subscription cancelation", $_sub);
                    }
                }
            }
        }
    }
    return true;
}

/**
 * Worker that processes Subscription tasks
 * @param $_queue array Queue entry
 * @return bool
 */
function jrSubscribe_subscription_tasks_worker($_queue)
{
    if (!isset($_queue['plugin']) || strlen($_queue['plugin']) === 0) {
        jrCore_logger('CRI', 'Subscribe: subscription_tasks queue entry received without plugin!', $_queue);
        return true;
    }
    if (!isset($_queue['function']) || strlen($_queue['function']) === 0) {
        jrCore_logger('CRI', 'Subscribe: subscription_tasks queue entry received without function', $_queue);
        return true;
    }
    jrPayment_set_active_plugin($_queue['plugin']);
    jrSubscribe_run_plugin_function($_queue['function'], $_queue);
    return true;
}

//---------------------------------------
// DASHBOARD PANELS
//---------------------------------------

/**
 * Dashboard panels
 * @param string $panel panel title
 * @return array|bool
 */
function jrSubscribe_dashboard_panels($panel)
{
    // The panel being asked for will come in as $panel
    $out = false;
    switch ($panel) {

        case 'active subscriptions':
            $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
            $req = "SELECT COUNT(sub_id) AS i FROM {$tbl} WHERE sub_status = 'active'";
            $_rt = jrCore_db_query($req, 'SINGLE');
            $out = array(
                'graph' => 'active_daily_subscribers',
                'title' => ($_rt && is_array($_rt)) ? jrCore_number_format($_rt['i']) : 0
            );
            break;

        case 'free subscriptions':
            $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
            $req = "SELECT COUNT(sub_id) AS i FROM {$tbl} WHERE sub_status = 'free'";
            $_rt = jrCore_db_query($req, 'SINGLE');
            $out = array(
                'title' => ($_rt && is_array($_rt)) ? jrCore_number_format($_rt['i']) : 0
            );
            break;

        case 'new subscriptions - 1 day':
            $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
            $req = "SELECT COUNT(sub_id) AS i FROM {$tbl} WHERE sub_status = 'active' AND sub_created > (UNIX_TIMESTAMP() - 86400)";
            $_rt = jrCore_db_query($req, 'SINGLE');
            $out = array(
                'title' => ($_rt && is_array($_rt)) ? jrCore_number_format($_rt['i']) : 0
            );
            break;

        case 'new subscriptions - 7 days':
            $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
            $req = "SELECT COUNT(sub_id) AS i FROM {$tbl} WHERE sub_status = 'active' AND sub_created > (UNIX_TIMESTAMP() - (7 * 86400))";
            $_rt = jrCore_db_query($req, 'SINGLE');
            $out = array(
                'title' => ($_rt && is_array($_rt)) ? jrCore_number_format($_rt['i']) : 0
            );
            break;

        case 'new subscriptions - 30 days':
            $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
            $req = "SELECT COUNT(sub_id) AS i FROM {$tbl} WHERE sub_status = 'active' AND sub_created > (UNIX_TIMESTAMP() - (30 * 86400))";
            $_rt = jrCore_db_query($req, 'SINGLE');
            $out = array(
                'title' => ($_rt && is_array($_rt)) ? jrCore_number_format($_rt['i']) : 0
            );
            break;

        case 'subscription income':
            $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
            $req = "SELECT SUM(sub_amount) AS i FROM {$tbl} WHERE sub_status != 'inactive'";
            $_rt = jrCore_db_query($req, 'SINGLE');
            $out = array(
                'graph' => 'daily_subscription_value',
                'title' => ($_rt && is_array($_rt)) ? jrPayment_get_currency_code() . jrPayment_currency_format($_rt['i']) : jrPayment_get_currency_code() . 0
            );
            break;

    }
    return ($out) ? $out : false;
}

//---------------------------------------
// GRAPHS
//---------------------------------------

/**
 * Daily Subscribers
 * @param $module string Module
 * @param $name string Name of Graph to create
 * @param $_args array Passed in Parameters
 * @return array
 */
function jrSubscribe_graph_active_daily_subscribers($module, $name, $_args)
{
    return array(
        '_sets' => array(
            0 => array(
                'label'       => 'Subscriber Count',
                'date_format' => '%m/%d/%Y',
                'minTickSize' => "[1, 'day']",
                'type'        => 'line',
                'pointRadius' => 2,
                '_data'       => jrCore_get_graph_stat_values('jrSubscribe', 'daily_subscription_stat', 'active_count')
            )
        )
    );
}

/**
 * Daily Subscription Value
 * @param $module string Module
 * @param $name string Name of Graph to create
 * @param $_args array Passed in Parameters
 * @return array
 */
function jrSubscribe_graph_daily_subscription_value($module, $name, $_args)
{
    if ($_vl = jrCore_get_graph_stat_values('jrSubscribe', 'daily_subscription_stat', 'active_average')) {
        foreach ($_vl as $k => $v) {
            $_vl[$k] = jrPayment_currency_format($v);
        }
    }
    return array(
        '_sets' => array(
            0 => array(
                'label'       => 'Subscriber Value',
                'date_format' => '%m/%d/%Y',
                'minTickSize' => "[1, 'day']",
                'type'        => 'line',
                'pointRadius' => 2,
                '_data'       => $_vl
            )
        )
    );
}

//---------------------------------------
// NEWSLETTER RECIPIENTS
//---------------------------------------

/**
 * Get newsletter recipient email addresses
 * @param $id string Recipient function ID
 * @return array|bool
 */
function jrSubscribe_newsletter_filter($id)
{
    $_id = false;
    switch ($id) {
        case 'subscription_users':
            $tb1 = jrCore_db_table_name('jrProfile', 'profile_link');
            $tb2 = jrCore_db_table_name('jrSubscribe', 'subscription');
            $req = "SELECT l.user_id AS u FROM {$tb2} s LEFT JOIN {$tb1} l ON l.profile_id = s.sub_profile_id";
            $_id = jrCore_db_query($req, 'u', false, 'u');
            break;
    }
    if ($_id && is_array($_id) && count($_id) > 0) {
        return jrMailer_get_email_array_from_ids($_id);
    }
    return false;
}

//---------------------------------------
// EVENT LISTENERS
//---------------------------------------

/**
 * System Reset listener
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrSubscribe_reset_system_listener($_data, $_user, $_conf, $_args, $event)
{
    $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");
    jrCore_db_query("OPTIMIZE TABLE {$tbl}");
    return $_data;
}

/**
 * Insert "subscription" tab into the User account section
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrSubscribe_account_tabs_listener($_data, $_user, $_conf, $_args, $event)
{
    // Does the viewing profile have a subscription?
    if (!$_sub = jrSubscribe_get_profile_subscription($_args['pid'])) {
        // Do we have any subscriptions?
        if (jrSubscribe_get_active_plan_count() > 0) {
            // Change the label to SUBSCRIPTIONS
            $_ln                                               = jrUser_load_lang_strings();
            $_data['jrSubscribe/active_subscription']['label'] = $_ln['jrSubscribe'][28];
        }
        else {
            // No subscriptions - remove
            unset($_data['jrSubscribe/active_subscription']);
        }
    }
    return $_data;
}

/**
 * Daily Maintenance plugin trigger
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrSubscribe_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    // plugins can process daily maintenance
    $_queue = array('time' => time());
    jrCore_queue_create('jrSubscribe', 'daily_maintenance', $_queue, 10, null, 1);
    return $_data;
}

/**
 * Hourly Maintenance plugin trigger
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrSubscribe_hourly_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    // plugins can process daily maintenance
    $_queue = array('time' => time());
    jrCore_queue_create('jrSubscribe', 'hourly_maintenance', $_queue, 90, null, 1);
    return $_data;
}

/**
 * Watch for subscriptions and redirect on success
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrSubscribe_payment_success_page_listener($_data, $_user, $_conf, $_args, $event)
{
    if (!isset($_data['_1']) || $_data['_1'] != 'cart') {
        if ($content = jrSubscribe_get_subscription_cookie()) {
            // We have a user returning from a subscription
            $murl = jrCore_get_module_url('jrSubscribe');
            jrCore_location("{$_conf['jrCore_base_url']}/{$murl}/active_subscription/" . jrCore_url_string($content));
        }
    }
    return $_data;
}

/**
 * NewsLetter filters
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrSubscribe_newsletter_filters_listener($_data, $_user, $_conf, $_args, $event)
{
    $_data['jrSubscribe'] = array(
        'subscription_users' => "Subscriptions: Users who have an active subscription"
    );
    return $_data;
}

/**
 * Watch for subscriptions coming in via the payment webhook
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrSubscribe_plugin_function_listener($_data, $_user, $_conf, $_args, $event)
{
    switch ($_args['function']) {
        case 'webhook_parse':
        case 'webhook_process':
            $plug = jrPayment_get_active_plugin();
            $func = "jrSubscribe_plugin_{$plug}_{$_args['function']}";
            if (!function_exists($func)) {
                require_once APP_DIR . "/modules/jrPayment/plugins/{$plug}.php";
                require_once APP_DIR . "/modules/jrSubscribe/plugins/{$plug}.php";
            }
            if (function_exists($func)) {
                $_data = $func($_data);
            }
            break;
    }
    return $_data;
}

/**
 * Format subscription purchases in user purchases
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrSubscribe_purchase_entry_listener($_data, $_user, $_conf, $_args, $event)
{
    $url               = jrCore_get_module_url('jrSubscribe');
    $_ln               = jrUser_load_lang_strings();
    $_data[1]['title'] = jrCore_get_module_icon_html('jrSubscribe', 48, 'payment-icon');
    if (isset($_args['r_item_data']['sub_title'])) {
        $_data[2]['title'] = "{$_args['r_item_data']['sub_title']}<br><small>{$_ln['jrSubscribe'][29]}</small>";
    }
    $_data[5]['title'] = jrCore_page_button("view-{$_args['r_id']}", $_ln['jrSubscribe'][30], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/active_subscription')");
    if (jrUser_is_admin()) {
        $_data[6]['title'] = '-';
    }
    return $_data;
}

/**
 * Format subscription purchases in payment browser
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrSubscribe_payment_entry_listener($_data, $_user, $_conf, $_args, $event)
{
    $_data[1]['title'] = jrCore_get_module_icon_html('jrSubscribe', 48, 'payment-icon');
    if (isset($_args['r_item_data']['sub_title'])) {
        $_data[2]['title'] = "{$_args['r_item_data']['sub_title']}<br><small>";
        if ($_args['r_amount'] == 0 && isset($_args['r_item_data']['sub_trial']) && $_args['r_item_data']['sub_trial'] != 0) {
            $_data[2]['title'] .= "Subscription Trial Start";
        }
        else {
            $_data[2]['title'] .= "Subscription Payment";
        }
        $_data[2]['title'] .= '</small>';
    }
    if ($_args['r_amount'] == 0 && isset($_args['r_item_data']['sub_trial']) && $_args['r_item_data']['sub_trial'] != 0) {
        $_data[4]['title'] .= '<br><small>trial start</small>';
    }
    return $_data;
}

/**
 * Format subscription purchases in transaction browser
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrSubscribe_txn_entry_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['_txn']['txn_type']) && $_args['_txn']['txn_type'] == 'subscription') {
        if (isset($_args['_txn']['txn_free_trial']) && $_args['_txn']['txn_free_trial'] == 1) {
            $_data[4]['title'] .= '<br><small>trial start</small>';
        }
    }
    jrPayment_set_active_plugin($_args['txn_plugin']);
    if ($_temp = jrSubscribe_run_plugin_function('txn_entry', $_data, $_args)) {
        $_data = $_temp;
    }
    jrPayment_reset_active_plugin();
    return $_data;
}

/**
 * Format subscription purchases in transaction detail
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrSubscribe_txn_detail_entry_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_mods;
    $_data[1]['title'] = jrCore_get_module_icon_html('jrSubscribe', 48, 'payment-icon');
    $_data[2]['title'] = "{$_args['r_item_data']['sub_title']}<br><small>{$_mods['jrSubscribe']['module_name']}</small>";
    if (isset($_args['txn_free_trial']) && $_args['txn_free_trial'] == 1) {
        $_data[4]['title'] .= '<br><small>trial start</small>';
    }
    return $_data;
}

/**
 * Add in system reset options
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrSubscribe_system_reset_options_listener($_data, $_user, $_conf, $_args, $event)
{
    $_data['reset_subscriber_data']  = array('reset subscriber data', 'delete all existing subscriber data', 'on');
    $_data['reset_subscriber_plans'] = array('reset subscriber plans', 'delete all existing subscriber plans', 'off');
    return $_data;
}

/**
 * Add in system reset options
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrSubscribe_system_reset_save_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['reset_subscriber_data']) && $_data['reset_subscriber_data'] == 'on') {

        $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
        jrCore_db_query("TRUNCATE TABLE {$tbl}");

        // Delete any free trial flags from profiles
        $tbl = jrCore_db_table_name('jrProfile', 'item_key');
        $req = "DELETE FROM {$tbl} WHERE `key` LIKE 'profile_jrSubscribe_trial%'";
        jrCore_db_query($req);

    }
    if (isset($_data['reset_subscriber_plans']) && $_data['reset_subscriber_plans'] == 'on') {
        jrCore_db_truncate_datastore('jrSubscribe');
    }
    return $_data;
}

//---------------------
// FUNCTIONS
//---------------------

/**
 * Save a subscription note by key
 * @param int $sub_id
 * @param string $key
 * @param string $note
 * @return bool
 */
function jrSubscriber_save_subscription_note($sub_id, $key, $note)
{
    $_nt = array();
    $sid = (int) $sub_id;
    $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
    $req = "SELECT sub_note FROM {$tbl} WHERE sub_id = {$sid}";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt) && isset($_rt['sub_note']) && strlen($_rt['sub_note']) > 0) {
        $_nt = json_decode($_rt['sub_note'], true);
    }
    $_nt[$key] = $note;
    $_nt       = jrCore_db_escape(json_encode($_nt));
    $req       = "UPDATE {$tbl} SET sub_updated = UNIX_TIMESTAMP(), sub_note = '{$_nt}' WHERE sub_id = {$sid} LIMIT 1";
    $cnt       = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Get a subscription note for a subscription key
 * @param int $sub_id
 * @param string $key
 * @return bool
 */
function jrSubscriber_get_subscription_note($sub_id, $key)
{
    $sid = (int) $sub_id;
    $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
    $req = "SELECT sub_note FROM {$tbl} WHERE sub_id = {$sid}";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt) && isset($_rt['sub_note']) && strlen($_rt['sub_note']) > 0) {
        $_nt = json_decode($_rt['sub_note'], true);
        if (isset($_nt[$key])) {
            return $_nt[$key];
        }
    }
    return false;
}

/**
 * Get the value of a plugin feature
 * @param string $flag
 * @return bool
 */
function jrSubscriber_get_plugin_feature_flag($flag)
{
    $_fl = jrSubscribe_run_plugin_function('metadata');
    return ($_fl && isset($_fl[$flag])) ? $_fl[$flag] : false;
}

/**
 * Show skin menu link to "Your Subscription"
 * @param array $_conf
 * @param array $_user
 * @return bool
 */
function jrSubscribe_skin_menu_item_function($_conf, $_user)
{
    if ($pid = jrUser_get_profile_home_key('_profile_id')) {
        if ($_sub = jrSubscribe_get_profile_subscription($pid)) {
            return true;
        }
    }
    return false;
}

/**
 * Send a subscription user notification
 * @param int $user_id
 * @param int $plan_id
 * @param string $template
 * @param array $_replace
 * @return bool
 */
function jrSubscribe_notify_user($user_id, $plan_id, $template, $_replace = null)
{
    $uid = (int) $user_id;
    if ($_us = jrCore_db_get_item('jrUser', $uid)) {
        if (isset($_us['user_email']) && strpos($_us['user_email'], '@')) {
            if (is_null($_replace) || !is_array($_replace)) {
                $_replace = array();
            }
            $_replace['_user'] = $_us;
            if ($plan_id > 0) {
                $pid               = (int) $plan_id;
                $_replace['_plan'] = jrCore_db_get_item('jrSubscribe', $pid, true);
            }

            // Some common replacements
            $_replace['payment_module_url']   = jrCore_get_module_url('jrPayment');
            $_replace['subscribe_module_url'] = jrCore_get_module_url('jrSubscribe');

            list($sub, $msg) = jrCore_parse_email_templates('jrSubscribe', $template, $_replace);
            jrCore_send_email($_us['user_email'], $sub, $msg, null, $_us);
            return true;
        }
    }
    return false;
}

/**
 * Check if a profile has used the free trial period for a plan
 * @param int $profile_id
 * @param int $plan_id
 * @return bool
 */
function jrSubscribe_profile_used_trial($profile_id, $plan_id)
{
    $pid = (int) $profile_id;
    $sid = (int) $plan_id;
    if ($_pr = jrCore_db_get_item('jrProfile', $pid, true)) {
        if (isset($_pr["profile_jrSubscribe_trial_{$sid}"])) {
            return true;
        }
    }
    return false;
}

/**
 * Get the expiration DAY for a subscription
 * @param int $time
 * @return string
 */
function jrSubscriber_get_subscription_expire_date($time)
{
    return jrCore_format_time($time, true);
}

/**
 * Set a subscription cookie so we know we came from a subscription
 * @param string $content
 * @return bool
 */
function jrSubscribe_set_subscription_cookie($content = null)
{
    if (is_null($content)) {
        $content = jrCore_get_current_url();
    }
    return jrCore_set_cookie('jr_sub_action', $content, 1);
}

/**
 * Get a sub cookie if we have one
 * @return bool|mixed
 */
function jrSubscribe_get_subscription_cookie()
{
    if ($content = jrCore_get_cookie('jr_sub_action')) {
        jrCore_delete_cookie('jr_sub_action');
        return $content;
    }
    return false;
}

/**
 * Save meta data about a subscription
 * @param int $profile_id
 * @param string $key
 * @param mixed $val
 * @return bool
 */
function jrSubscribe_save_sub_metadata_key($profile_id, $key, $val)
{
    if ($_sub = jrSubscribe_get_profile_subscription($profile_id)) {
        $_meta = array();
        if (isset($_sub['sub_data']) && strlen($_sub['sub_data']) > 0) {
            $_meta = json_decode($_sub['sub_data'], true);
        }
        $_meta['_updated'] = time();
        $_meta[$key]       = $val;
        $_meta             = jrCore_db_escape(json_encode($_meta));
        $tbl               = jrCore_db_table_name('jrSubscribe', 'subscription');
        $req               = "UPDATE {$tbl} SET sub_data = '{$_meta}' WHERE sub_id = {$_sub['sub_id']}";
        $cnt               = jrCore_db_query($req, 'COUNT');
        if ($cnt && $cnt === 1) {
            return true;
        }
    }
    return false;
}

/**
 * Get meta data about a profile subscription
 * @param int $profile_id
 * @param array $_sub subscription
 * @return bool|mixed
 */
function jrSubscribe_get_sub_meta_data($profile_id, $_sub = null)
{
    if (is_null($_sub) || !is_array($_sub)) {
        $_sub = jrSubscribe_get_profile_subscription($profile_id);
    }
    if ($_sub && is_array($_sub)) {
        if (isset($_sub['sub_data']) && strlen($_sub['sub_data']) > 0) {
            return json_decode($_sub['sub_data'], true);
        }
    }
    return false;
}

/**
 * Delete meta data about a subscription
 * @param int $profile_id
 * @param string $key
 * @return bool
 */
function jrSubscribe_delete_sub_metadata_key($profile_id, $key)
{
    if ($_sub = jrSubscribe_get_profile_subscription($profile_id)) {
        $_meta = array();
        if (isset($_sub['sub_data']) && strlen($_sub['sub_data']) > 0) {
            $_meta = json_decode($_sub['sub_data'], true);
        }
        if (isset($_meta[$key])) {
            unset($_meta[$key]);
        }
        $_meta['_updated'] = time();
        $_meta             = jrCore_db_escape(json_encode($_meta));
        $tbl               = jrCore_db_table_name('jrSubscribe', 'subscription');
        $req               = "UPDATE {$tbl} SET sub_data = '{$_meta}' WHERE sub_id = {$_sub['sub_id']}";
        $cnt               = jrCore_db_query($req, 'COUNT');
        if ($cnt && $cnt === 1) {
            return true;
        }
    }
    return false;
}

/**
 * Set the state of a subscription
 * @param int $profile_id
 * @param string $state
 * @return mixed
 */
function jrSubscribe_set_subscription_state($profile_id, $state)
{
    $pid = (int) $profile_id;
    $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
    $req = "UPDATE {$tbl} SET sub_status = '" . jrCore_db_escape($state) . "' WHERE sub_profile_id = {$pid}";
    return jrCore_db_query($req, 'COUNT');
}

/**
 * Get a subscription by sub_id
 * @param int $id
 * @return mixed
 */
function jrSubscribe_get_subscription_by_id($id)
{
    $sid = (int) $id;
    $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
    $req = "SELECT * FROM {$tbl} WHERE sub_id = {$sid}";
    return jrCore_db_query($req, 'SINGLE');
}

/**
 * Get a subscription by profile_id
 * @param int $id
 * @return mixed
 */
function jrSubscribe_get_subscription_by_profile_id($id)
{
    $pid = (int) $id;
    $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
    $req = "SELECT * FROM {$tbl} WHERE sub_profile_id = {$pid}";
    return jrCore_db_query($req, 'SINGLE');
}

/**
 * Get the duration of a subscription as a readable string
 * @param string $length
 * @return bool|string
 */
function jrSubscribe_get_sub_duration_string($length)
{
    $_ln = jrUser_load_lang_strings();
    list($num, $tag) = explode(':', $length);
    switch ($tag) {
        case 'd':
            $idx = 3;
            break;
        case 'w':
            $idx = 4;
            break;
        case 'm':
            $idx = 5;
            break;
        case 'y':
            $idx = 6;
            break;
        default:
            return false;
            break;
    }
    if ($num > 1) {
        $idx += 4;
    }
    return "{$num} {$_ln['jrSubscribe'][$idx]}";
}

/**
 * Get number of active subscriptions
 * @return mixed
 */
function jrSubscribe_get_active_plan_count()
{
    return jrCore_db_run_key_function('jrSubscribe', 'sub_active', 'on', 'COUNT');
}

/**
 * Update a single DB field in a subscription
 * @param int $sub_id
 * @param string $field
 * @param mixed $value
 * @return bool
 */
function jrSubscribe_update_subscription_field($sub_id, $field, $value)
{
    $sid = (int) $sub_id;
    $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
    $req = "UPDATE {$tbl} SET `{$field}` = '" . jrCore_db_escape($value) . "', sub_updated = UNIX_TIMESTAMP() WHERE sub_id = {$sid}";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Cancel a subscription for a profile
 * @param int $profile_id
 * @return bool
 */
function jrSubscribe_cancel_subscription($profile_id)
{
    $pid = (int) $profile_id;
    $plg = jrPayment_get_active_plugin();
    $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
    $req = "UPDATE {$tbl} SET sub_status = 'canceled', sub_updated = UNIX_TIMESTAMP() WHERE sub_profile_id = {$pid} AND sub_plugin = '{$plg}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {

        // Trigger event
        jrCore_trigger_event('jrSubscribe', 'subscription_canceled', array('profile_id' => $profile_id));

        // Notify admin users of new subscriber
        $req = "SELECT * FROM {$tbl} WHERE sub_profile_id = {$pid} AND sub_plugin = '{$plg}'";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if ($_rt && is_array($_rt)) {
            $_pr                   = jrCore_db_get_item('jrProfile', $pid);
            $_pl                   = jrCore_db_get_item('jrSubscribe', $_rt['sub_plan_id'], true);
            $_pl['sub_item_price'] = jrPayment_currency_format($_rt['sub_amount']);
            jrSubscribe_notify_admins('canceled', $_pr, $_pl);
        }
        return true;
    }
    return false;
}

/**
 * Delete a profile subscription
 * @param int $profile_id
 * @return bool
 */
function jrSubscribe_delete_subscription($profile_id)
{
    global $_conf;
    $pid = (int) $profile_id;
    $_sb = jrSubscribe_get_subscription_by_profile_id($pid);
    if ($_sb && is_array($_sb)) {
        $sid = (int) $_sb['sub_id'];
        $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
        $req = "UPDATE {$tbl} SET sub_status = 'inactive', sub_updated = UNIX_TIMESTAMP() WHERE sub_id = {$sid}";
        $cnt = jrCore_db_query($req, 'COUNT');
        if ($cnt && $cnt > 0) {
            $_pr = jrCore_db_get_item('jrProfile', $pid, true, true);
            if ($_pr && is_array($_pr)) {
                // good profile - get plan
                if ($_pl = jrCore_db_get_item('jrSubscribe', $_sb['sub_plan_id'])) {
                    if (isset($_pl['sub_eot_quota_id']) && $_pl['sub_eot_quota_id'] >= 0) {

                        // Trigger event
                        $_args = array(
                            '_sub'  => $_sb,
                            '_plan' => $_pl
                        );
                        jrCore_trigger_event('jrSubscribe', 'subscription_deleted', $_pr, $_args);

                        $key = false;
                        $qid = false;
                        if ($_pl['sub_eot_quota_id'] == '0') {
                            // We are moving this profile back to their OLD quota
                            if (isset($_pr['profile_jrSubscribe_eot_quota_id']) && jrCore_checktype($_pr['profile_jrSubscribe_eot_quota_id'], 'number_nz')) {
                                $qid = (int) $_pr['profile_jrSubscribe_eot_quota_id'];
                                $key = true;
                            }
                        }
                        if (!$qid) {
                            if ($_pl['sub_eot_quota_id'] > 0) {
                                $qid = (int) $_pl['sub_eot_quota_id'];
                            }
                            else {
                                // We do not know what quota_id to change to!
                                $qid = (int) $_conf['jrProfile_default_quota_id'];
                                jrCore_logger('CRI', "Subscribe: invalid end of term quota_id in subscription plan {$_sb['sub_plan_id']}", $_pl);
                            }
                        }

                        // Do we need to change or is the profile already on the right quota?
                        if ($qid && $_pr['profile_quota_id'] != $qid) {
                            $_up = array(
                                'profile_quota_id' => $qid
                            );
                            if (jrCore_db_update_item('jrProfile', $pid, $_up)) {
                                if ($key) {
                                    // Remove old EOT quota_id key
                                    jrCore_db_delete_item_key('jrProfile', $pid, 'profile_jrSubscribe_eot_quota_id');
                                }

                                // Update quota counts
                                jrProfile_increment_quota_profile_count($qid);
                                jrProfile_decrement_quota_profile_count($_pr['profile_quota_id']);

                                jrProfile_reset_cache($pid);
                                jrUser_reset_cache($_pr['_user_id']);
                                if ($_us = jrProfile_get_owner_info($pid)) {
                                    foreach ($_us as $u) {
                                        jrUser_set_session_sync_for_user_id($u['_user_id'], 'on');
                                        jrSubscribe_notify_user($u['_user_id'], $_sb['sub_plan_id'], 'subscription_canceled');
                                    }
                                }

                                // See if our plugins need to do any work for deleting the subscription
                                if ($_sb['sub_manual'] == 0) {
                                    jrSubscribe_run_plugin_function('delete_subscription', $_sb);
                                }
                                jrCore_logger('INF', "Subscribe: deactivated subscription for profile @{$_pr['profile_url']} - moved to quota_id {$_up['profile_quota_id']}", $_sb);
                            }
                        }
                        return true;
                    }
                    else {
                        jrCore_logger('CRI', "Subscribe: plan {$_sb['sub_plan_id']} has invalid EOT quota id - unable to move profile_id: {$pid} to new quota_id");
                    }
                }
                else {
                    jrCore_logger('CRI', "Subscribe: plan {$_sb['sub_plan_id']} not found - unable to move profile_id: {$pid} to new quota_id");
                }
            }
        }
        else {
            jrCore_logger('CRI', "Subscribe: error making subscription inactive in database for profile_id: {$pid}");
        }
    }
    return false;
}

/**
 * Creates a new subscription for a profile
 * NOTE: this function does NOT move the profile to the quota
 * @param int $profile_id Profile ID
 * @param int $plan_id Plan ID
 * @param string $duration
 * @param int $amount plan amount (in cents)
 * @param bool $free_trial set to TRUE to indicate sub has started in a free trial
 * @param bool $manual set to TRUE to indicate sub was created manually
 * @param bool $update_expires set to FALSE to not update the expires field
 * @param int $created_time set to EPOCH time for creation of subscription
 * @param int $expires_time set to EPOCH time for expiration of subscription
 * @return bool
 */
function jrSubscribe_create_subscription($profile_id, $plan_id, $duration, $amount, $free_trial = false, $manual = false, $update_expires = true, $created_time = 0, $expires_time = 0)
{
    $pid = (int) $profile_id;
    $sid = (int) $plan_id;
    $amt = (int) $amount;
    if ($expires_time > 0) {
        $exp = (int) $expires_time;
    }
    else {
        if (!$exp = jrSubscribe_get_sub_end_date($duration)) {
            // We must have a valid end date!
            $exp = jrSubscribe_get_sub_end_date('1:m');
            jrCore_logger('CRI', "Subscribe: unable to determine end date for subscription plan_id {$pid} - using 30 days default");
        }
    }
    $plg = jrPayment_get_active_plugin();
    $sts = ($free_trial) ? 'trial' : 'active';
    $man = 0;
    if ($manual) {
        $man = 1;
        $sts = 'free';
    }

    // If we are updating the EXPIRE time for a subscription, we need to get the current
    // subscription expiration time and get the different between now and then and add it to the new expires
    $upe = '';
    if ($update_expires && $expires_time === 0) {
        $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
        $req = "SELECT (sub_expires - UNIX_TIMESTAMP()) AS exp FROM {$tbl} WHERE sub_profile_id = {$pid} AND sub_status != 'inactive' AND sub_expires > UNIX_TIMESTAMP()";
        $_ex = jrCore_db_query($req, 'SINGLE');
        if ($_ex && is_array($_ex) && isset($_ex['exp']) && jrCore_checktype($_ex['exp'], 'number_nz')) {
            $exp += (int) $_ex['exp'];
        }
        $upe = ", sub_expires = {$exp}";
    }

    $crd = 'UNIX_TIMESTAMP()';
    if ($created_time > 0) {
        $crd = (int) $created_time;
    }
    $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
    $req = "INSERT INTO {$tbl} (sub_profile_id, sub_plan_id, sub_status, sub_plugin, sub_amount, sub_created, sub_updated, sub_expires, sub_manual, sub_note)
            VALUES ('{$pid}', '{$sid}', '{$sts}', '{$plg}', '{$amt}', {$crd}, UNIX_TIMESTAMP(), {$exp}, {$man}, '')
            ON DUPLICATE KEY UPDATE sub_plan_id = {$sid}, sub_status = '{$sts}', sub_plugin = '{$plg}', sub_amount = {$amt}, sub_updated = UNIX_TIMESTAMP(){$upe}";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt > 0) {
        return $cnt;
    }
    return false;
}

/**
 * Start or Extend a subscription for a user's profile to a subscription plan
 * @param int $profile_id Profile ID
 * @param int $plan_id Plan ID
 * @param int $price Plan price (in cents)
 * @param string|int $duration
 * @param bool $validate set to FALSE to skip price validation
 * @param bool $manual set to TRUE to indicate this subscription was manually created
 * @param bool $update_expires set to FALSE to prevent sub expires from being updated
 * @param bool $notify_admins set to FALSE to prevent email notification of new subscription
 * @param int $created_time set to EPOCH time subscription was created or 0 for default
 * @param int $expires_time set to EPOCH time to override computed expires time
 * @return bool
 */
function jrSubscribe_start_subscription($profile_id, $plan_id, $price, $duration = null, $validate = true, $manual = false, $update_expires = true, $notify_admins = true, $created_time = 0, $expires_time = 0)
{
    // subscription plan
    $sid = (int) $plan_id;
    $_pl = jrCore_db_get_item('jrSubscribe', $sid, true);
    if (!$_pl || !is_array($_pl)) {
        jrCore_logger('CRI', "Subscribe: plan_id received in start_subscription does not exist", func_get_args());
        return false;
    }

    // subscription quota
    $qid = (int) $_pl['sub_quota_id'];
    $_qt = jrProfile_get_quota($qid);
    if (!$_qt || !is_array($_qt)) {
        jrCore_logger('CRI', "Subscribe: quota_id received in start_subscription does not exist", func_get_args());
        return false;
    }

    // profile info
    $pid = (int) $profile_id;
    $_pr = jrCore_db_get_item('jrProfile', $pid);
    if (!$_pr || !is_array($_pr)) {
        jrCore_logger('CRI', "Subscribe: profile_id received in start_subscription does not exist", func_get_args());
        return false;
    }

    // Does our price match?
    if ($validate) {
        $amount = jrPayment_price_to_cents($_pl['sub_item_price']);
        if (jrPayment_price_to_cents($price) !== $amount) {
            jrCore_logger('CRI', "Subscribe: amount received in start subscription is different than the plan price", array('profile_id' => $profile_id, 'plan_id' => $plan_id, 'price' => $price, '_plan' => $_pl));
            return false;
        }
    }
    else {
        $amount = jrPayment_price_to_cents($price);
    }

    // We are good - start subscription
    if (is_null($duration) || !strpos($duration, ':')) {
        $duration = $_pl['sub_duration'];
    }
    if ($new = jrSubscribe_create_subscription($pid, $sid, $duration, $amount, false, $manual, $update_expires, $created_time, $expires_time)) {

        // Is the profile already in the quota?
        if ($_pr['profile_quota_id'] != $qid) {

            // Update profile to new quota
            $_up = array(
                'profile_quota_id' => $qid
            );
            // If this plan has a FREE trial, we need to mark that we've used it
            if (isset($_pl['sub_trial']) && $_pl['sub_trial'] !== 0) {
                $_up["profile_jrSubscribe_trial_{$_pl['_item_id']}"] = 1;
            }
            // Do we need to save the quota_id we are moving from?
            if (isset($_pl['sub_eot_quota_id']) && $_pl['sub_eot_quota_id'] == '0') {
                $_up['profile_jrSubscribe_eot_quota_id'] = (int) $_pr['profile_quota_id'];
            }

            // Update quota counts
            jrProfile_increment_quota_profile_count($qid);
            jrProfile_decrement_quota_profile_count($_pr['profile_quota_id']);

            if (jrCore_db_update_item('jrProfile', $pid, $_up)) {
                jrCore_logger('INF', "Subscribe: started {$_pl['sub_title']} subscription for profile @{$_pr['profile_url']}", $_pl);
                jrProfile_reset_cache($pid);

                // Get user ties to this profile and set sync flag
                if ($_us = jrProfile_get_owner_info($pid)) {
                    foreach ($_us as $u) {
                        jrUser_set_session_sync_for_user_id($u['_user_id'], 'on');
                    }
                }
            }
        }
        else {
            // Extending or Moving to a new subscription?
            if ($_es = jrSubscribe_get_profile_subscription($pid)) {
                // We already have a subscription to for this profile - update
                jrCore_logger('INF', "Subscribe: updated {$_pl['sub_title']} subscription for profile @{$_pr['profile_url']}", $_pl);
            }
            else {
                // Should never get here...
                jrCore_logger('INF', "Subscribe: started {$_pl['sub_title']} subscription for profile @{$_pr['profile_url']}", $_pl);
                jrProfile_reset_cache($pid);
            }
        }

        $action = 'created';
        if ($new != 1) {
            // This was an updated subscription
            $action = 'extended';
        }

        // Remove notification keys
        jrSubscribe_delete_sub_metadata_key($pid, 'upcoming_notice_sent');
        jrSubscribe_delete_sub_metadata_key($pid, 'cancel_notice_sent');

        $_args = array(
            'amount' => $amount,
            '_plan'  => $_pl,
            '_quota' => $_qt
        );
        jrCore_trigger_event('jrSubscribe', "subscription_{$action}", $_pr, $_args);

        // Notify admins
        if ($notify_admins) {
            $_pl['sub_item_price'] = jrPayment_currency_format($amount);
            jrSubscribe_notify_admins($action, $_pr, $_pl);
        }

        return true;
    }
    return false;
}

/**
 * Get number of days to CREDIT when changing to a cheaper plan
 * @param array $_new
 * @param array $_old
 * @param int $expires
 * @return int
 */
function jrSubscribe_get_subscription_credit_days($_new, $_old, $expires)
{
    // $_new = plan we are changing TO
    // $_old = plan we are changing FROM
    // First - how many days are left on the current subscription?
    $days = (int) ceil(($expires - time()) / 86400);
    $intv = jrSubscribe_convert_interval_to_days($_new['sub_duration']);
    $perc = ($days / $intv);

    // How much $$ does that translate to?
    $cred = ($perc * jrPayment_price_to_cents($_old['sub_item_price']));

    // How much of the NEW plan does that credit amount buy us?
    $nday = jrSubscribe_convert_interval_to_days($_new['sub_duration']);
    $nprc = jrPayment_price_to_cents($_new['sub_item_price']);
    $pday = round($nprc / $nday, 0);

    // Now we know how many days the credit amount buys us in the new plan
    return (int) ceil($cred / $pday);
}

/**
 * Notify admins when a subscription is created or canceled
 * @param string $action created|updated|canceled
 * @param array $_profile profile info
 * @param array $_plan plan info
 */
function jrSubscribe_notify_admins($action, $_profile, $_plan)
{
    if ($_ids = jrUser_get_admin_user_ids()) {
        $_profile['_plan'] = $_plan;
        list($sub, $msg) = jrCore_parse_email_templates('jrSubscribe', "subscriber_{$action}", $_profile);
        foreach ($_ids as $uid) {
            jrUser_notify($uid, 0, 'jrSubscribe', "subscription_{$action}", $sub, $msg);
        }
    }
}

/**
 * Get active subscription for a profile
 * @param $profile_id int Profile ID
 * @return mixed
 */
function jrSubscribe_get_profile_subscription($profile_id)
{
    $pid = (int) $profile_id;
    $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
    $req = "SELECT * FROM {$tbl} WHERE sub_profile_id = '{$pid}' AND sub_status != 'inactive'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        return $_rt;
    }
    return false;
}

/**
 * Run a plugin function
 * @param $func string Function to run
 * @return bool|mixed
 */
function jrSubscribe_run_plugin_function($func)
{
    $plug = jrPayment_get_active_plugin();
    if (!$plug || strlen($plug) === 0) {
        return false;
    }
    $func = "jrSubscribe_plugin_{$plug}_{$func}";
    if (!function_exists($func)) {
        require_once APP_DIR . "/modules/jrPayment/plugins/{$plug}.php";
        require_once APP_DIR . "/modules/jrSubscribe/plugins/{$plug}.php";
    }
    if (!function_exists($func)) {
        return false;
    }
    return call_user_func_array($func, array_slice(func_get_args(), 1));
}

/**
 * Return true if a plugin function exists
 * @param $func string Function
 * @return bool
 */
function jrSubscribe_plugin_function_exists($func)
{
    $plug = jrPayment_get_active_plugin();
    if (!$plug || strlen($plug) === 0) {
        return false;
    }
    $func = "jrSubscribe_plugin_{$plug}_{$func}";
    if (!function_exists($func)) {
        require_once APP_DIR . "/modules/jrPayment/plugins/{$plug}.php";
        require_once APP_DIR . "/modules/jrSubscribe/plugins/{$plug}.php";
    }
    if (function_exists($func)) {
        return true;
    }
    return false;
}

/**
 * Record a subscription sale in the register
 * @param int $txn_id
 * @param int $amount
 * @param string $gateway_id
 * @param int $user_id
 * @param array $_plan
 * @param string $tag
 * @param int $fee
 * @return bool|mixed
 */
function jrSubscribe_record_sale_in_register($txn_id, $amount, $gateway_id, $user_id, $_plan, $tag = null, $fee = 0)
{
    // Add some items to this plan so it gets recorded correctly in the register
    $_plan['_profile_id']   = 0;
    $_plan['cart_module']   = 'jrSubscribe';
    $_plan['cart_quantity'] = 1;
    $_plan['cart_field']    = 'sub_file_item_price';
    $_plan['cart_amount']   = jrPayment_price_to_cents($amount);
    $_plan['cart_shipping'] = 0;
    return jrPayment_record_sale_in_register($txn_id, $gateway_id, $user_id, $_plan, $tag, $fee);
}

/**
 * Get subscription duration in readable format
 * @param string $duration
 * @param bool $single_number
 * @return string
 */
function jrSubscribe_get_text_duration($duration, $single_number = false)
{
    if (!strpos($duration, ':')) {
        return '-';
    }
    list($number, $type) = explode(':', trim($duration));
    $number = (int) $number;
    switch ($type) {
        case 'd':
            $tag = 'day';
            break;
        case 'w':
            $tag = 'week';
            break;
        case 'm':
            $tag = 'month';
            break;
        case 'y':
            $tag = 'year';
            break;
        default:
            return '-';
            break;
    }
    if ($number > 1) {
        $tag .= 's';
        return $number . ' ' . $tag;
    }
    if ($single_number) {
        return $number . ' ' . $tag;
    }
    return $tag;
}

/**
 * Get all subscription plans
 * @param bool $only_active
 * @param bool $check_quota
 * @return array|bool|mixed
 */
function jrSubscribe_get_all_plans($only_active = false, $check_quota = false)
{
    $_sp = array(
        'order_by'      => array('_item_id' => 'numerical_asc'),
        'no_cache'      => true,
        'privacy_check' => false,
        'skip_triggers' => true,
        'limit'         => 1000
    );
    if ($only_active) {
        $_sp['search'] = array('sub_active = on');
    }
    if ($check_quota) {
        if (!isset($_sp['search'])) {
            $_sp['search'] = array();
        }
        $_sp['search'][] = 'sub_display_quota_id in 0,' . jrUser_get_profile_home_key('profile_quota_id');
    }
    $_sp = jrCore_db_search_items('jrSubscribe', $_sp);
    if ($_sp && is_array($_sp) && isset($_sp['_items'])) {
        return $_sp['_items'];
    }
    return false;
}

/**
 * Delete (or mark as inactive) a subscription plan
 * @param int $plan_id
 * @return bool
 */
function jrSubscribe_delete_plan($plan_id)
{
    $pid = (int) $plan_id;
    // Do we have any subscribers?
    // If we have existing subscribers in this plan we can't delete it, but
    // instead we will mark it as inactive
    $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
    $req = "SELECT sub_id FROM {$tbl} WHERE sub_plan_id = {$pid} LIMIT 1";
    $_ex = jrCore_db_query($req, 'SINGLE');
    if ($_ex && is_array($_ex)) {
        // Update to inactive
        $_up = array('sub_active' => 'off');
        jrCore_db_update_item('jrSubscribe', $pid, $_up);
    }
    else {
        jrCore_db_delete_item('jrSubscribe', $pid);
    }
    return true;
}

/**
 * Get available subscription durations
 * @return array
 */
function jrSubscribe_get_sub_durations()
{
    return array(
        '1:d' => '1 day',
        '2:d' => '2 days',
        '3:d' => '3 days',
        '4:d' => '4 days',
        '5:d' => '5 days',
        '6:d' => '6 days',
        '1:w' => '1 week',
        '2:w' => '2 weeks',
        '3:w' => '3 weeks',
        '1:m' => '1 month',
        '2:m' => '2 months',
        '3:m' => '3 months',
        '4:m' => '4 months',
        '6:m' => '6 months',
        '1:y' => '1 year'
    );
}

/**
 * Get end date for a subscription duration in YYYYMMDD format
 * @param $duration string value from jrSubscribe_get_sub_durations()
 * @return int
 */
function jrSubscribe_get_sub_end_date($duration)
{
    if (!strpos($duration, ':')) {
        // See if we've been given an already converted end date
        return (jrCore_checktype($duration, 'number_nz')) ? $duration : false;
    }
    list($number, $type) = explode(':', trim($duration));
    $number = (int) $number;
    switch ($type) {
        case 'd':
            $end = (time() + ($number * 86400));
            break;
        case 'w':
            $end = (time() + ($number * (7 * 86400)));
            break;
        case 'm':
            $end = strtotime("+{$number} months");
            break;
        case 'y':
            $end = strtotime("+{$number} years");
            break;
        default:
            return false;
            break;
    }
    return $end;
}

/**
 * Get number of free trial days based on num:mod
 * @param string $days
 * @return mixed
 */
function jrSubscribe_convert_interval_to_days($days)
{
    if ($days == 0) {
        return 0;
    }
    list($num, $opt) = explode(':', $days, 2);
    $num = (int) $num;
    if ($num === 0) {
        return 0;
    }
    $var = 1;
    switch ($opt) {
        case 'w':
            $var = 7;
            break;
        case 'm':
            $var = 30;
            break;
        case 'y':
            $var = 365;
            break;
    }
    return ($num * $var);
}

/**
 * Get the amount of credit for an existing subscription
 * @param mixed $price
 * @param string $interval
 * @param int $expires
 * @return int
 */
function jrSubscribe_get_subscription_credit_amount($price, $interval, $expires)
{
    $price = jrPayment_price_to_cents($price);
    if ($price === 0) {
        return 0;
    }

    // What are our intervals?
    $interval = jrSubscribe_convert_interval_to_days($interval);

    // How many days until we expire?
    $days = (int) ceil(($expires - time()) / 86400);
    if ($days > 0) {
        // There are still days on the existing subscription - credit
        return intval(round(($days / $interval) * $price));
    }
    return 0;
}

//---------------------
// SMARTY FUNCTIONS
//---------------------

/**
 * Get button to start a subscription
 * @param array $params
 * @param object $smarty
 * @return bool|mixed|string
 */
function smarty_function_jrSubscribe_get_subscription_button($params, &$smarty)
{
    global $_conf, $_user;
    if (!isset($params['plan_id'])) {
        return jrCore_smarty_missing_error('plan_id');
    }
    if (!jrCore_checktype($params['plan_id'], 'number_nz')) {
        return jrCore_smarty_invalid_error('plan_id');
    }
    $key = 'jrsubscribe_get_subscription_plans';
    if (!$_pl = jrCore_get_flag($key)) {
        $_pl = jrSubscribe_get_all_plans(true, true);
        jrCore_set_flag($key, $_pl);
    }
    if (!$_pl) {
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], '');
        }
        return '';
    }
    $_p = false;
    foreach ($_pl as $p) {
        if ($p['_item_id'] == $params['plan_id']) {
            $_p = $p;
            break;
        }
    }

    $out = '';
    if ($_p) {

        if (!isset($params['title']) || strlen($params['title']) === 0) {
            $_ln             = jrUser_load_lang_strings();
            $params['title'] = jrCore_entity_string($_ln['jrSubscribe'][33]);
        }
        $ttl = jrCore_entity_string($params['title']);
        $pid = (int) $_p['_item_id'];
        $url = jrCore_get_module_url('jrSubscribe');

        // We found our plan
        // Does this profile already have an active subscription?
        $_as    = array();
        $_old   = array();
        $active = false;
        if (jrUser_is_logged_in()) {
            if (!$_tmp = jrCore_get_flag('active_subscription_flag')) {
                if ($_as = jrSubscribe_get_profile_subscription($_user['user_active_profile_id'])) {
                    $_tmp = array(
                        '_as'  => $_as,
                        '_old' => jrCore_db_get_item('jrSubscribe', $_as['sub_plan_id'])
                    );
                    jrCore_set_flag('active_subscription_flag', $_tmp);
                    $active = (int) $_as['sub_plan_id'];
                }
                else {
                    jrCore_set_flag('active_subscription_flag', 'not_found');
                }
            }
            else {
                if (is_array($_tmp)) {
                    $_as    = $_tmp['_as'];
                    $_old   = $_tmp['_old'];
                    $active = (int) $_as['sub_plan_id'];
                }
            }
        }

        if ($active && $active == $pid) {
            // Already subscribed to this plan
            $url = "{$_conf['jrCore_base_url']}/{$url}/active_subscription";
            $out .= jrCore_page_button("subscribe-plan-{$pid}", $ttl, "jrCore_window_location('{$url}')", array('class' => 'subscribe-plan'));
        }
        elseif ($active && jrSubscriber_get_plugin_feature_flag('prorate_sub_change') !== 1) {

            // Bring in any pre-js
            $out .= jrSubscribe_run_plugin_function('subscribe_javascript', $_p);

            // This plugin does not support prorating - we need to get the correct URL
            $onc = jrSubscribe_run_plugin_function('subscribe_change_onclick', $_p, $_old, $_as);
            if ($onc && strlen($onc) > 0) {
                $out .= jrCore_page_button("subscribe-plan-{$pid}", $ttl, $onc);
            }
            else {
                $url = jrSubscribe_run_plugin_function('subscribe_change_url', $_p, $_old, $_as);
                if ($url && strlen($url) > 0) {
                    $out .= jrCore_page_button("subscribe-plan-{$pid}", $ttl, "jrCore_window_location('{$url}')");
                }
                else {
                    // handle manually
                    $out .= jrCore_page_button("subscribe-plan-{$pid}", $ttl, "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/change/id={$pid}')");
                }
            }
        }
        else {

            // Bring in any pre-js
            $out .= jrSubscribe_run_plugin_function('subscribe_javascript', $_p);

            // Are we doing an onclick or a straight URL?
            $onc = jrSubscribe_run_plugin_function('subscribe_onclick', $_p);
            if ($onc && strlen($onc) > 0) {
                $out .= jrCore_page_button("subscribe-plan-{$pid}", $ttl, $onc, array('class' => 'form_button subscribe-plan'));
            }
            else {
                $url = jrSubscribe_run_plugin_function('subscribe_url', $_p);
                if ($url && strlen($url) > 0) {
                    $out .= jrCore_page_button("subscribe-plan-{$pid}", $ttl, "jrCore_window_location('{$url}')", array('class' => 'form_button subscribe-plan'));
                }
            }
        }
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
    }
    return $out;
}

/**
 * Get URL to start a subscription
 * @param array $params
 * @param object $smarty
 * @return bool|mixed|string
 */
function smarty_function_jrSubscribe_get_subscription_url($params, &$smarty)
{
    global $_conf, $_user;
    if (!isset($params['plan_id'])) {
        return jrCore_smarty_missing_error('plan_id');
    }
    if (!jrCore_checktype($params['plan_id'], 'number_nz')) {
        return jrCore_smarty_invalid_error('plan_id');
    }
    $key = 'jrsubscribe_get_subscription_plans';
    if (!$_pl = jrCore_get_flag($key)) {
        $_pl = jrSubscribe_get_all_plans(true, true);
        jrCore_set_flag($key, $_pl);
    }
    if (!$_pl) {
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], '');
        }
        return '';
    }
    $_p = false;
    foreach ($_pl as $p) {
        if ($p['_item_id'] == $params['plan_id']) {
            $_p = $p;
            break;
        }
    }

    $out = '';
    if ($_p) {

        if (!isset($params['title']) || strlen($params['title']) === 0) {
            $_ln             = jrUser_load_lang_strings();
            $params['title'] = jrCore_entity_string($_ln['jrSubscribe'][33]);
        }
        $ttl = jrCore_entity_string($params['title']);
        $pid = (int) $_p['_item_id'];
        $url = jrCore_get_module_url('jrSubscribe');

        // We found our plan
        // Does this profile already have an active subscription?
        $_as    = array();
        $_old   = array();
        $active = false;
        if (jrUser_is_logged_in()) {
            if (!$_tmp = jrCore_get_flag('active_subscription_flag')) {
                if ($_as = jrSubscribe_get_profile_subscription($_user['user_active_profile_id'])) {
                    $_tmp = array(
                        '_as'  => $_as,
                        '_old' => jrCore_db_get_item('jrSubscribe', $_as['sub_plan_id'])
                    );
                    jrCore_set_flag('active_subscription_flag', $_tmp);
                    $active = (int) $_as['sub_plan_id'];
                }
                else {
                    jrCore_set_flag('active_subscription_flag', 'not_found');
                }
            }
            else {
                if (is_array($_tmp)) {
                    $_as    = $_tmp['_as'];
                    $_old   = $_tmp['_old'];
                    $active = (int) $_as['sub_plan_id'];
                }
            }
        }

        if ($active && $active == $pid) {
            // Already subscribed to this plan
            $url = "{$_conf['jrCore_base_url']}/{$url}/active_subscription";
            $out .= "<a class=\"subscription-url\" onclick=\"jrCore_window_location('{$url}')\">{$ttl}</a>";
        }
        elseif ($active && jrSubscriber_get_plugin_feature_flag('prorate_sub_change') !== 1) {

            // Bring in any pre-js
            $out .= jrSubscribe_run_plugin_function('subscribe_javascript', $_p);

            // This plugin does not support prorating - we need to get the correct URL
            $onc = jrSubscribe_run_plugin_function('subscribe_change_onclick', $_p, $_old, $_as);
            if ($onc && strlen($onc) > 0) {
                $out .= "<a class=\"subscription-url\" onclick=\"{$onc}\">{$ttl}</a>";
            }
            else {
                $url = jrSubscribe_run_plugin_function('subscribe_change_url', $_p, $_old, $_as);
                if ($url && strlen($url) > 0) {
                    $out .= "<a class=\"subscription-url\" onclick=\"jrCore_window_location('{$url}')\">{$ttl}</a>";
                }
                else {
                    // handle manually
                    $out .= "<a class=\"subscription-url\" onclick=\"jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/change/id={$pid}')\">{$ttl}</a>";
                }
            }
        }
        else {

            // Bring in any pre-js
            $out .= jrSubscribe_run_plugin_function('subscribe_javascript', $_p);

            // Are we doing an onclick or a straight URL?
            $onc = jrSubscribe_run_plugin_function('subscribe_onclick', $_p);
            if ($onc && strlen($onc) > 0) {
                $out .= "<a class=\"subscription-url\" onclick=\"{$onc}\">{$ttl}</a>";
            }
            else {
                $url = jrSubscribe_run_plugin_function('subscribe_url', $_p);
                if ($url && strlen($url) > 0) {
                    $out .= "<a class=\"subscription-url\" onclick=\"jrCore_window_location('{$url}')\">{$ttl}</a>";
                }
            }
        }
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
    }
    return $out;
}
