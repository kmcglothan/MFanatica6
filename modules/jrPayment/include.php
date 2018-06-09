<?php
/**
 * Jamroom Payment Support module
 *
 * copyright 2018 Jamroom Team
 *
 * This Jamroom file is LICENSED SOFTWARE, and cannot be redistributed.
 *
 * This Source Code is subject to the terms of the Jamroom Network
 * Commercial License -  please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * Jamroom Team
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function jrPayment_meta()
{

    $_tmp = array(
        'name'        => 'Payment Support',
        'url'         => 'payment',
        'version'     => '1.0.7',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Provides shopping cart and plugin support for payment gateways',
        'category'    => 'ecommerce',
        'license'     => 'jcl',
        'requires'    => 'jrCore:6.1.0b2',
        'priority'    => 80
    );
    return $_tmp;
}

/**
 * init
 */
function jrPayment_init()
{
    global $_mods;

    // JS & CSS
    jrCore_register_module_feature('jrCore', 'css', 'jrPayment', 'jrPayment.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrPayment', 'jrPayment.js');

    // Quota support
    $_tmp = array(
        'label' => 'Allow Sales',
        'help'  => 'If checked, users in this quota will be able to sell items in modules that support sales.'
    );
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrPayment', 'on', $_tmp);

    // Custom ACP views
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrPayment', 'plugin_browser', array('Plugin Config', 'Browse and Configure available payment plugins'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrPayment', 'report', array('Monthly Report', 'Overview of monthly Income and Expenses'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrPayment', 'payments', array('Payment Browser', 'Browse balance affecting payments'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrPayment', 'transactions', array('Event Browser', 'Browse all incoming payment webhook events'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrPayment', 'payout', array('Profile Payout', 'Send a Payout payment to profiles that have a balance'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrPayment', 'system_reset', array('Reset Test Data', 'Delete existing data in the Payments System'));
    if (isset($_mods['jrFoxyCart'])) {
        jrCore_register_module_feature('jrCore', 'tool_view', 'jrPayment', 'import', array('Import Payments', 'Import existing payment transactions from the FoxyCart module'));
    }
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrPayment', 'payments', 'Payments');

    // Our default master view
    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrPayment', 'payments');

    // Core item buttons
    $_tmp = array(
        'title'  => 'add to cart button',
        'icon'   => 'cart',
        'active' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'item_list_button', 'jrPayment', 'jrPayment_item_cart_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrPayment', 'jrPayment_item_cart_button', $_tmp);
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrPayment', 'webhook');

    // Our event triggers
    jrCore_register_event_trigger('jrPayment', 'cart_entry', 'Fired for each entry when viewing the cart');
    jrCore_register_event_trigger('jrPayment', 'cart_add_item', 'Fired when an item is added to the cart');
    jrCore_register_event_trigger('jrPayment', 'cart_update_item', 'Fired when an item is updated in the cart');
    jrCore_register_event_trigger('jrPayment', 'cart_remove_item', 'Fired when an item is removed from the cart');
    jrCore_register_event_trigger('jrPayment', 'cart_checkout_row', 'Fired with checkot row info when displaying cart');
    jrCore_register_event_trigger('jrPayment', 'cart_checkout', 'Fired with cart contents when checking out');
    jrCore_register_event_trigger('jrPayment', 'cart_shipping', 'Fired for each entry in cart to get shipping and handling');
    jrCore_register_event_trigger('jrPayment', 'cart_shipping_alt', 'Fired for each entry in cart to get shipping and handling (3rd party)');
    jrCore_register_event_trigger('jrPayment', 'cart_update_quantity', 'Fired to get available quantity when updating quantity in cart');
    jrCore_register_event_trigger('jrPayment', 'add_to_cart_onclick', 'Fired when creating add to cart button to get onclick handler');
    jrCore_register_event_trigger('jrPayment', 'get_item_price_field', 'Fired to get name of form field for item price');
    jrCore_register_event_trigger('jrPayment', 'purchase_item', 'Fired when an item is successfully purchased');
    jrCore_register_event_trigger('jrPayment', 'purchase_entry', 'Fired for each entry when viewing user purchases');
    jrCore_register_event_trigger('jrPayment', 'payment_entry', 'Fired for each entry when viewing the payment browser');
    jrCore_register_event_trigger('jrPayment', 'txn_entry', 'Fired for each entry when viewing items in the transaction browser');
    jrCore_register_event_trigger('jrPayment', 'txn_detail_entry', 'Fired for each entry when viewing items in transaction detail');
    jrCore_register_event_trigger('jrPayment', 'refund_item', 'Fired when an item is successfully refunded');
    jrCore_register_event_trigger('jrPayment', 'register_entry', 'Fired when a new item is added to the register');
    jrCore_register_event_trigger('jrPayment', 'sale_entry', 'Fired for each entry when viewing profile sales');
    jrCore_register_event_trigger('jrPayment', 'customer_entry', 'Fired for each entry when viewing profile customers');
    jrCore_register_event_trigger('jrPayment', 'product_entry', 'Fired for each entry when viewing profile products');
    jrCore_register_event_trigger('jrPayment', 'webhook_event', 'Fired for every event received as a webhook');
    jrCore_register_event_trigger('jrPayment', 'webhook_parsed', 'Fired for every event received as a webhook with parsed transaction');
    jrCore_register_event_trigger('jrPayment', 'plugin_function', 'Fired when a plugin function is executed');
    jrCore_register_event_trigger('jrPayment', 'system_reset_save', 'Fired with selected reset options');
    jrCore_register_event_trigger('jrPayment', 'system_reset_options', 'Fired to gather additional system reset options');
    jrCore_register_event_trigger('jrPayment', 'payment_success_page', 'Fired when user views the payment success page');
    jrCore_register_event_trigger('jrPayment', 'vault_item_created', 'Fired when an item is sold and added to the system vault');
    jrCore_register_event_trigger('jrPayment', 'vault_item_updated', 'Fired when an item is sold and updated in the system vault');

    // Our event listeners
    jrCore_register_event_listener('jrCore', 'form_display', 'jrPayment_form_display_listener');
    jrCore_register_event_listener('jrCore', 'view_results', 'jrPayment_view_results_listener');
    jrCore_register_event_listener('jrCore', 'parsed_template', 'jrPayment_parsed_template_listener');
    jrCore_register_event_listener('jrCore', 'save_media_file', 'jrPayment_save_media_file_listener');
    jrCore_register_event_listener('jrCore', 'db_delete_item', 'jrPayment_db_delete_item_listener');
    jrCore_register_event_listener('jrCore', 'hourly_maintenance', 'jrPayment_hourly_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrPayment_daily_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrPayment_verify_module_listener');
    jrCore_register_event_listener('jrUser', 'login_success', 'jrPayment_login_success_listener');
    jrCore_register_event_listener('jrUser', 'site_privacy_check', 'jrPayment_site_privacy_check_listener');
    jrCore_register_event_listener('jrDeveloper', 'reset_system', 'jrPayment_reset_system_listener');
    jrCore_register_event_listener('jrPayment', 'payment_entry', 'jrPayment_payment_entry_listener');
    jrCore_register_event_listener('jrPayment', 'txn_detail_entry', 'jrPayment_txn_detail_entry_listener');

    // Purchases
    $_tmp = array(
        'label' => 15,
        'field' => 'quota_jrPayment_show_purchases'
    );
    jrCore_register_module_feature('jrUser', 'account_tab', 'jrPayment', 'purchases', $_tmp);

    // Profile Tabs
    jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrPayment', 'summary', 40);
    jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrPayment', 'payments', 23);
    jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrPayment', 'customers', 24);
    jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrPayment', 'products', 25);

    // "payments" section only shows to profile owners
    jrCore_register_module_feature('jrProfile', 'profile_menu', 'jrPayment', 'owner_only', true);

    // Let our active plugin add items to the page if needed
    jrPayment_run_plugin_function('page_elements');

    $_tmp = array(
        'label' => 'Purchase Receipt',
        'help'  => 'If you purchase an item how would you like to receive the purchase receipt?'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrPayment', 'purchase_receipt', $_tmp);

    $_tmp = array(
        'label' => 'Sold Item',
        'help'  => 'If a user buys an item from your profile would you like to be notified?'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrPayment', 'profile_sold_item', $_tmp);

    $_tmp = array(
        'group' => 'admin',
        'label' => 'Site Sold Items',
        'help'  => 'When a user purchase items from your site do you want to be notified?',
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrPayment', 'system_sold_items', $_tmp);

    // Link to "Your Purchases"
    $_tmp = array(
        'group' => 'user',
        'label' => 31,
        'url'   => 'purchases',
        'field' => 'quota_jrPayment_show_purchases'
    );
    jrCore_register_module_feature('jrCore', 'skin_menu_item', 'jrPayment', 'purchases', $_tmp);

    // Graph Support
    $_tmp = array(
        'title'    => 'Monthly Report',
        'function' => 'jrPayment_graph_monthly_report'
    );
    jrCore_register_module_feature('jrGraph', 'graph_config', 'jrPayment', 'monthly_report', $_tmp);
    $_tmp = array(
        'title'    => 'Monthly Report',
        'function' => 'jrPayment_graph_profile_monthly_report'
    );
    jrCore_register_module_feature('jrGraph', 'graph_config', 'jrPayment', 'profile_monthly_report', $_tmp);

    // We provide some newsletter recipient options
    jrCore_register_event_listener('jrNewsLetter', 'newsletter_filters', 'jrPayment_newsletter_filters_listener');

    // We provide some dashboard panels
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrPayment', 'monthly income', 'jrPayment_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrPayment', 'monthly expense', 'jrPayment_dashboard_panels');

    // Our queue worker
    jrCore_register_queue_worker('jrPayment', 'payment_tasks', 'jrPayment_payment_tasks_worker', 0, 2, 86400, NORMAL_PRIORITY_QUEUE);

    return true;
}

//---------------------------------------
// QUEUE WORKER
//---------------------------------------

/**
 * Worker that processes Payment tasks
 * @param $_queue array Queue entry
 * @return bool
 */
function jrPayment_payment_tasks_worker($_queue)
{
    if (!isset($_queue['plugin']) || strlen($_queue['plugin']) === 0) {
        jrCore_logger('CRI', 'Payments: payment_tasks queue entry received without plugin!', $_queue);
        return true;
    }
    if (!isset($_queue['function']) || strlen($_queue['function']) === 0) {
        jrCore_logger('CRI', 'Payments: payment_tasks queue entry received without function', $_queue);
        return true;
    }
    jrPayment_set_active_plugin($_queue['plugin']);
    jrPayment_run_plugin_function($_queue['function'], $_queue);
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
function jrPayment_dashboard_panels($panel)
{
    // The panel being asked for will come in as $panel
    $out = false;
    switch ($panel) {

        case 'monthly income':
            $off = date_offset_get(new DateTime);
            $now = strftime('%Y%m');
            $tbl = jrCore_db_table_name('jrPayment', 'register');
            $req = "SELECT SUM((r_quantity * r_amount) + r_shipping) AS i FROM {$tbl} WHERE FROM_UNIXTIME(r_created + {$off}, '%Y%m') = '{$now}'";
            $_rt = jrCore_db_query($req, 'SINGLE');
            $out = array(
                'title' => ($_rt && is_array($_rt)) ? jrPayment_get_currency_code() . jrPayment_currency_format($_rt['i']) : jrPayment_get_currency_code() . 0
            );
            break;

        case 'monthly expense':
            $off = date_offset_get(new DateTime);
            $now = strftime('%Y%m');
            $tbl = jrCore_db_table_name('jrPayment', 'register');
            $req = "SELECT SUM(r_fee) AS i FROM {$tbl} WHERE FROM_UNIXTIME(r_created + {$off}, '%Y%m') = '{$now}'";
            $_rt = jrCore_db_query($req, 'SINGLE');
            $out = array(
                'title' => ($_rt && is_array($_rt)) ? jrPayment_get_currency_code() . jrPayment_currency_format($_rt['i']) : jrPayment_get_currency_code() . 0
            );
            break;

    }
    return ($out) ? $out : false;
}

//---------------------------------------
// NEWSLETTER RECIPIENTS
//---------------------------------------

/**
 * Get newsletter recipient email addresses
 * @param $id string Recipient function ID
 * @return array|bool
 */
function jrPayment_newsletter_filter($id)
{
    $_id = false;
    switch ($id) {
        case 'purchase_users':
            $tbl = jrCore_db_table_name('jrPayment', 'register');
            $req = "SELECT r_purchase_user_id AS u FROM {$tbl} GROUP BY r_purchase_user_id";
            $_id = jrCore_db_query($req, 'u', false, 'u');
            break;
    }
    if ($_id && is_array($_id) && count($_id) > 0) {
        return jrMailer_get_email_array_from_ids($_id);
    }
    return false;
}

//------------------------------------
//  GRAPH CONFIG
//------------------------------------

/**
 * Monthly Report Graph (system wide)
 * @param $module string Module
 * @param $name string Name of Graph to create
 * @param $_args array Passed in Parameters
 * @return array
 */
function jrPayment_graph_monthly_report($module, $name, $_args)
{
    $_rs = array(
        'precision' => 2,
        'yaxis' => array(
            'tickFormatter' =>  "function(v,a) { return '$' + v; }"
        ),
        '_sets' => array(
            0 => array(
                'label'       => 'Gross Income',
                'date_format' => '%Y%m',
                'minTickSize' => "[1, 'month']",
                'type'        => 'line',
                'pointRadius' => 4,
                '_data'       => array(),
                'color'       => '#0066CC'
            ),
            1 => array(
                'label'       => 'Expense (includes profile payments)',
                'date_format' => '%Y%m',
                'minTickSize' => "[1, 'month']",
                'type'        => 'line',
                'pointRadius' => 2,
                '_data'       => array(),
                'color'       => '#FFCC00'
            ),
            2 => array(
                'label'       => 'Gateway Fees',
                'date_format' => '%Y%m',
                'minTickSize' => "[1, 'month']",
                'type'        => 'line',
                'pointRadius' => 1,
                '_data'       => array(),
                'color'       => '#CC6600'
            ),
            3 => array(
                'label'       => 'Net Income',
                'date_format' => '%Y%m',
                'minTickSize' => "[1, 'month']",
                'type'        => 'line',
                'pointRadius' => 3,
                '_data'       => array(),
                'color'       => '#339900'
            )
        )
    );
    $off = date_offset_get(new DateTime);
    $tbl = jrCore_db_table_name('jrPayment', 'register');
    $req = "SELECT r_seller_profile_id AS p,
               FROM_UNIXTIME(r_created + {$off}, '%Y%m') AS c,
               SUM((r_quantity * r_amount) + r_shipping + r_tax) AS i,
               SUM(r_fee) AS e,
               SUM(r_expense) AS s,
               SUM(r_gateway_fee) AS f
              FROM {$tbl} GROUP BY p, c ORDER BY c ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $v) {
            $yr = substr($v['c'], 0, 4);
            $mn = substr($v['c'], 4, 2);
            $tm = (string) gmmktime(1, 1, 1, $mn, 15, $yr) * 1000;
            if (!isset($_rs['_sets'][0]['_data'][$tm])) {
                $_rs['_sets'][0]['_data'][$tm] = 0;
                $_rs['_sets'][1]['_data'][$tm] = 0;
                $_rs['_sets'][2]['_data'][$tm] = 0;
                $_rs['_sets'][3]['_data'][$tm] = 0;
            }
            $_rs['_sets'][0]['_data'][$tm] += $v['i'];
            $_rs['_sets'][2]['_data'][$tm] += $v['f'];
            if ($v['p'] > 0) {
                $_rs['_sets'][1]['_data'][$tm] += ($v['i'] - $v['e']);
                $_rs['_sets'][3]['_data'][$tm] += ($v['e'] + $v['f']);
            }
            else {
                $_rs['_sets'][1]['_data'][$tm] += $v['s'];
                $_rs['_sets'][3]['_data'][$tm] += ($v['i'] - ($v['s'] + $v['f']));
            }
        }
        foreach ($_rs['_sets'] as $k => $d) {
            foreach ($d['_data'] as $c => $v) {
                $_rs['_sets'][$k]['_data'][$c] = number_format(($v / 100), 2, '.', '');
            }
        }
        $_rs = jrPayment_fill_gaps_in_graph_data($_rs);
    }
    return $_rs;
}

/**
 * Monthly Report Graph (profile)
 * @param $module string Module
 * @param $name string Name of Graph to create
 * @param $_args array Passed in Parameters
 * @return array
 */
function jrPayment_graph_profile_monthly_report($module, $name, $_args)
{
    global $_user;
    $_ln = jrUser_load_lang_strings();
    $_rs = array(
        '_sets' => array(
            0 => array(
                'label'       => $_ln['jrPayment'][41],
                'date_format' => '%Y%m',
                'minTickSize' => "[1, 'month']",
                'type'        => 'line',
                'pointRadius' => 3,
                '_data'       => array(),
                'color'       => '#0066CC'
            ),
            1 => array(
                'label'       => $_ln['jrPayment'][42],
                'date_format' => '%Y%m',
                'minTickSize' => "[1, 'month']",
                'type'        => 'line',
                'pointRadius' => 1,
                '_data'       => array(),
                'color'       => '#FFCC00'
            ),
            2 => array(
                'label'       => $_ln['jrPayment'][43],
                'date_format' => '%Y%m',
                'minTickSize' => "[1, 'month']",
                'type'        => 'line',
                'pointRadius' => 2,
                '_data'       => array(),
                'color'       => '#339900'
            )
        )
    );
    $tax = 0;
    if (!empty($_user['quota_jrPayment_include_tax']) && $_user['quota_jrPayment_include_tax'] == 'on') {
        $tax = 'r_tax';
    }
    $shp = 0;
    if (!empty($_user['quota_jrPayment_include_shipping']) && $_user['quota_jrPayment_include_shipping'] == 'on') {
        $shp = 'r_shipping';
    }
    $off = date_offset_get(new DateTime);
    $tbl = jrCore_db_table_name('jrPayment', 'register');
    $req = "SELECT FROM_UNIXTIME(r_created + {$off}, '%Y%m') AS c,
              SUM(((r_quantity * r_amount) + {$shp} + {$tax}) - r_refunded_amount) AS i,
              SUM(r_fee) AS e
              FROM {$tbl} WHERE r_seller_profile_id = '{$_user['user_active_profile_id']}' GROUP BY c ORDER BY c ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $v) {
            $yr = substr($v['c'], 0, 4);
            $mn = substr($v['c'], 4, 2);
            $tm = gmmktime(1, 1, 1, $mn, 15, $yr) * 1000;
            $tm = "{$tm}";
            if (!isset($_rs['_sets'][0]['_data'][$tm])) {
                $_rs['_sets'][0]['_data'][$tm] = 0;
                $_rs['_sets'][1]['_data'][$tm] = 0;
                $_rs['_sets'][2]['_data'][$tm] = 0;
            }
            $_rs['_sets'][0]['_data'][$tm] += $v['i'];
            $_rs['_sets'][1]['_data'][$tm] += $v['e'];
            $_rs['_sets'][2]['_data'][$tm] += ($v['i'] - $v['e']);
        }
        foreach ($_rs['_sets'] as $k => $d) {
            foreach ($d['_data'] as $c => $v) {
                $_rs['_sets'][$k]['_data'][$c] = number_format(($v / 100), 2, '.', '');
            }
        }
        $_rs = jrPayment_fill_gaps_in_graph_data($_rs);
    }
    return $_rs;
}

//--------------------------------
// EVENT LISTENERS
//--------------------------------

/**
 * Format manual transactions
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrPayment_payment_entry_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_mods;
    $_data[2]['title'] = ucwords($_args['r_tag']) . '<br><small>' . $_mods['jrPayment']['module_name'] . '</small>';
    if ($_args['r_purchase_user_id'] == 0 && !empty($_args['r_item_data']) && isset($_args['r_item_data']['txn_user_email'])) {
        $_data[3]['title'] = $_args['r_item_data']['txn_user_email'];
    }
    return $_data;
}

/**
 * Format manual transactions
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrPayment_txn_detail_entry_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_mods;
    $_data[2]['title'] = ucwords($_args['r_tag']) . '<br><small>' . $_mods['jrPayment']['module_name'] . ' &bull; register entry</small>';
    if ($_args['r_amount'] > 0 && $_args['r_expense'] > 0) {
        $_data[4]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_args['r_amount']) . '<br><small>-' . jrPayment_get_currency_code() . jrPayment_currency_format($_args['r_expense']) . '</small>';
    }
    elseif ($_args['r_amount'] > 0) {
        $_data[4]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_args['r_amount']);
    }
    elseif ($_args['r_expense'] > 0) {
        $_data[4]['title'] = '-' . jrPayment_get_currency_code() . jrPayment_currency_format($_args['r_expense']);
    }
    else {
        $_data[4]['title'] = 'unknown';
    }
    return $_data;
}

/**
 * Verify Module (integrity check)
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrPayment_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    $_sc = array(
        'search'         => array(
            'profile_foxycart_payout_email like %@%'
        ),
        'return_keys'    => array('_item_id', 'profile_foxycart_payout_email', 'profile_jrPayment_payout_email'),
        'skip_triggers'  => true,
        'ignore_missing' => true,
        'privacy_check'  => false,
        'limit'          => 1000
    );
    $_sc = jrCore_db_search_items('jrProfile', $_sc);
    if ($_sc && is_array($_sc) && isset($_sc['_items'])) {
        $_up = array();
        foreach ($_sc['_items'] as $_s) {
            if (!isset($_s['profile_jrPayment_payout_email']) || strlen($_s['profile_jrPayment_payout_email']) === 0) {
                $pid       = (int) $_s['_item_id'];
                $_up[$pid] = array('profile_jrPayment_payout_email' => $_s['profile_foxycart_payout_email']);
            }
        }
        if (count($_up) > 0) {
            jrCore_db_update_multiple_items('jrProfile', $_up);
            jrCore_logger('INF', "Payments: updated " . jrCore_number_format(count($_up)) . " profiles with missing payout email", $_up);
        }
    }
    return $_data;
}

/**
 * Hourly Maintenance for plugins
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrPayment_hourly_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    jrPayment_run_plugin_function('hourly_maintenance');

    // Cleanup really old carts for logged out users
    $old = (30 * 86400);
    $tbl = jrCore_db_table_name('jrPayment', 'cart');
    $req = "SELECT cart_id FROM {$tbl} WHERE cart_updated < (UNIX_TIMESTAMP() - {$old}) AND cart_user_id = 0";
    $_rt = jrCore_db_query($req, 'cart_id');
    if ($_rt && is_array($_rt)) {
        $req = "DELETE FROM {$tbl} WHERE cart_id IN(" . implode(',', array_keys($_rt)) . ')';
        jrCore_db_query($req);
    }

    return $_data;
}

/**
 * Daily Maintenance for plugins
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrPayment_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    // Save stats
    $beg = strtotime('midnight', (time() - 86400));
    $end = strtotime('midnight', time());

    $tbl = jrCore_db_table_name('jrPayment', 'register');
    $req = "SELECT COUNT(r_id) AS c, SUM(r_amount) AS i FROM {$tbl} WHERE r_created >= {$beg} AND r_created < {$end}";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        jrCore_create_stat_entry('jrPayment', 'daily_register', "payment_count", 0, 0, '127.0.0.1', true, $_rt['c']);
        jrCore_create_stat_entry('jrPayment', 'daily_register', "daily_income", 0, 0, '127.0.0.1', true, $_rt['i']);
    }

    jrPayment_run_plugin_function('daily_maintenance');
    return $_data;
}

/**
 * Watch for media file updates for the Vault
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrPayment_db_delete_item_listener($_data, $_user, $_conf, $_args, $event)
{
    jrPayment_mark_vault_item_as_deleted($_args['module'], $_args['_item_id']);
    return $_data;
}

/**
 * Watch for media file updates for the Vault
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrPayment_save_media_file_listener($_data, $_user, $_conf, $_args, $event)
{
    // Are we updating a vault item?
    if ($_vl = jrPayment_get_vault_item($_args['module'], $_args['unique_id'])) {

        // This is a vault item
        $update = false;

        // Check for good size
        if (isset($_args['_file']['tmp_name']) && is_file($_args['_file']['tmp_name']) && filesize($_args['_file']['tmp_name']) > 512) {
            // It's not an empty file - update
            $update = true;
        }
        if ($update) {
            jrPayment_add_file_to_vault($_args['module'], $_args['file_name'], $_vl);
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
function jrPayment_newsletter_filters_listener($_data, $_user, $_conf, $_args, $event)
{
    $_data['jrPayment'] = array(
        'purchase_users' => "Payments: Users who have purchased an item"
    );
    return $_data;
}

/**
 * Allow incoming webhooks even if private
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrPayment_site_privacy_check_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['module']) && $_args['module'] == 'jrPayment' && isset($_args['option'])) {
        switch ($_args['option']) {
            case 'webhook':
                $_data['allow_private_site_view'] = true;
                break;
        }
    }
    return $_data;
}

/**
 * Adds a "price" field to item forms that support it
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrPayment_form_display_listener($_data, $_user, $_conf, $_args, $event)
{
    if (!isset($_user['quota_jrPayment_allowed']) || $_user['quota_jrPayment_allowed'] != 'on') {
        // this user's quota does not allow sales
        return $_data;
    }

    // Add in payout email address to Profile Settings
    if (isset($_data['form_view']) && $_data['form_view'] == 'jrProfile/settings') {
        // Add in Payout Email Address
        if ($_user['quota_jrPayment_payout_percent'] > 0) {
            $_lng = jrUser_load_lang_strings();
            $_tmp = array(
                'name'          => 'profile_jrPayment_payout_email',
                'label'         => $_lng['jrPayment'][32],
                'type'          => 'text',
                'validate'      => 'email',
                'help'          => $_lng['jrPayment'][33],
                'required'      => false,
                'form_designer' => false
            );
            jrCore_form_field_create($_tmp);
        }
        return $_data;
    }

    // Is this a create/update form?
    if ($field = jrPayment_get_item_price_field($_data['form_params']['module'], $_data['form_view'])) {
        $_lng = jrUser_load_lang_strings();
        $_tmp = array(
            'name'          => $field,
            'type'          => 'text',
            'default'       => '',
            'validate'      => 'price',
            'min'           => '0.01',
            'label'         => $_lng['jrPayment'][1],
            'help'          => $_lng['jrPayment'][2],
            'form_designer' => false // No form designer or it can't be turned off
        );
        jrCore_form_field_create($_tmp);
    }

    return $_data;
}

/**
 * Add view cart button to main menu
 * @param $_data string Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return string
 */
function jrPayment_parsed_template_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_conf['jrPayment_show_cart']) && $_conf['jrPayment_show_cart'] == 'on' && !jrCore_get_flag('jrpayment_cart_added')) {
        if (isset($_args['template']) && strpos(' ' . $_args['template'], 'menu') && strpos($_data, '</nav>')) {
            // This could be the main menu template
            $_user['icon_size']  = jrCore_get_skin_icon_size(32);
            $_user['item_count'] = jrPayment_get_user_cart_item_count();
            $html                = jrCore_parse_template('view_cart_button.tpl', $_user, 'jrPayment');
            $_data               = jrPayment_insert_cart_template($_data, $html);
            jrCore_set_flag('jrpayment_cart_added', 1);
        }
    }
    return $_data;
}

/**
 * Add view cart button overlay
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrPayment_view_results_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (!jrCore_is_ajax_request() && ((!isset($_conf['jrCore_maintenance_mode']) || $_conf['jrCore_maintenance_mode'] != 'on') || jrUser_is_logged_in())) {

        if (jrCore_is_view_request() || jrProfile_is_profile_view()) {

            // are we redirecting from a checkout login?
            if ($url = jrCore_get_cookie('checkout_login')) {
                if (!isset($_post['r'])) {
                    $_data = str_replace('ready(function(){', 'ready(function(){ jrPayment_view_cart();', $_data);
                    jrCore_delete_cookie('checkout_login');
                }
            }

        }
    }
    return $_data;
}

/**
 * Update cart when a user logs in
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrPayment_login_success_listener($_data, $_user, $_conf, $_args, $event)
{
    // If a user logs in, make sure any cart they started while logged out is
    // updated with their user_id so it is available to them logged in
    if (isset($_data['_user_id']) && $_data['_user_id'] > 0) {
        if ($val = jrCore_get_cookie('jrpayment_cart_id')) {
            // Bring over their cart
            $uid = (int) $_data['_user_id'];
            $tbl = jrCore_db_table_name('jrPayment', 'cart');
            $req = "UPDATE {$tbl} SET cart_user_id = '{$uid}' WHERE cart_session_id = '" . jrCore_db_escape($val) . "'";
            jrCore_db_query($req);
        }
    }
    return $_data;
}

/**
 * Reset system (via developer tools)
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrPayment_reset_system_listener($_data, $_user, $_conf, $_args, $event)
{
    $_tables = array('register', 'cart', 'cart_item', 'payout', 'vault');
    foreach ($_tables as $table) {
        $tbl = jrCore_db_table_name('jrPayment', $table);
        jrCore_db_query("TRUNCATE {$tbl}");
    }

    // Delete any vault files
    jrPayment_delete_all_vault_files();

    return $_data;
}

//--------------------------------
// ITEM BUTTONS
//--------------------------------

/**
 * Return "add to cart" button for the item
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrPayment_item_cart_button($module, $_item, $_args, $smarty, $test_only = false)
{
    if ($test_only) {
        return true;
    }
    if (!isset($_args['field']) || strlen($_args['field']) === 0) {
        $pfx            = jrCore_db_get_prefix($module);
        $_args['field'] = "{$pfx}_file";
    }
    $_rp = array(
        'module' => $module,
        'item'   => $_item,
        'field'  => $_args['field']
    );
    return smarty_function_jrPayment_add_to_cart_button($_rp, $smarty);
}

//--------------------------------
// FUNCTIONS
//--------------------------------

/**
 * Fill in missing months in graph data
 * @param array $_data
 * @return mixed
 */
function jrPayment_fill_gaps_in_graph_data($_data)
{
    foreach ($_data['_sets'] as $k => $_d) {
        $srt = false;
        $old = false;
        $now = (int) strftime('%Y%m');
        foreach ($_d['_data'] as $t => $v) {

            // Get our starting month
            if (!$old) {
                $old = strftime('%Y%m', ($t / 1000));
            }
            while ($old <= $now) {
                $yr = substr($old, 0, 4);
                $mn = substr($old, 4, 2);
                $tm = gmmktime(1, 1, 1, $mn, 15, $yr) * 1000;
                $tm = "{$tm}";
                if (!isset($_data['_sets'][$k]['_data'][$tm])) {
                    $_data['_sets'][$k]['_data'][$tm] = 0;
                    $srt                              = true;
                }
                if ($mn == 12) {
                    // We're rolling over
                    $mn = '01';
                    $yr++;
                }
                else {
                    // We're in the same year
                    $mn = (int) $mn;
                    $mn++;
                    $mn = str_pad($mn, 2, '0', STR_PAD_LEFT);
                }
                $old = (int) "{$yr}{$mn}";
            }
        }
        if ($srt) {
            ksort($_data['_sets'][$k]['_data']);
        }
    }
    return $_data;
}

/**
 * Insert the cart template into the skin main menu
 * @param string $string
 * @param string $html
 * @return mixed
 */
function jrPayment_insert_cart_template($string, $html)
{
    if (strpos(' ' . $string, '<!-- jrPayment_cart_html -->')) {
        $string = str_replace('<!-- jrPayment_cart_html -->', $html, $string);
    }
    else {
        if ($pos = strrpos($string, '</ul>')) {
            $string = substr_replace($string, $html, $pos, 5);
        }
    }
    return $string;
}

/**
 * Find if a given module is supported by the Payments module
 * @param string $module
 * @return bool
 */
function jrPayment_is_supported_payment_module($module)
{
    if ($_tmp = jrCore_get_event_listeners('jrPayment', 'get_item_price_field')) {
        foreach ($_tmp as $func) {
            if (strpos($func, $module . '_') === 0) {
                return true;
            }
        }
    }
    // Backwards compatibility with FoxyCart module
    if ($_tmp = jrCore_get_event_listeners('jrFoxyCart', 'add_price_field')) {
        foreach ($_tmp as $func) {
            if (strpos($func, $module . '_') === 0) {
                return true;
            }
        }
    }
    return false;
}

/**
 * Delete all vault files
 * @return bool
 */
function jrPayment_delete_all_vault_files()
{
    $_vf = jrCore_get_media_files('system', 'jrPayment_vault*');
    if ($_vf && is_array($_vf)) {
        foreach ($_vf as $_file) {
            jrCore_delete_media_file('system', basename($_file['name']));
        }
    }
    return true;
}

/**
 * Get payment types that affect the site balance
 * @param string $type
 * @return bool
 */
function jrPayment_is_valid_transaction_type($type)
{
    $_valid = array('payment', 'refund', 'information');
    return (in_array(strtolower($type), $_valid)) ? true : false;
}

/**
 * Get fields that are REQUIRED for any transaction
 * @return array
 */
function jrPayment_get_required_transaction_fields()
{
    return array(
        'txn_id'       => 'not_empty',
        'txn_type'     => 'not_empty',
        'txn_status'   => 'not_empty',
        'txn_date'     => 'number_nz',
        'txn_total'    => 'number',
        'txn_shipping' => 'number_nn',
        'txn_tax'      => 'number_nn'
    );
}

/**
 * Check if a transaction contains the required fields
 * @param array $_txn
 * @return bool
 */
function jrPayment_transaction_contains_required_fields($_txn)
{
    foreach (jrPayment_get_required_transaction_fields() as $f => $v) {
        if (!isset($_txn[$f])) {
            jrCore_logger('CRI', "Payments: required field missing in transaction: {$f}", $_txn);
            return false;
        }
        if (!jrCore_checktype($_txn[$f], $v)) {
            jrCore_logger('CRI', "Payments: invalid field value in transaction: {$f}", $_txn);
            return false;
        }
    }
    return true;
}

/**
 * Get price field name for forms that want sales
 * @param $module string Module
 * @param $option string View
 * @return string
 */
function jrPayment_get_item_price_field($module, $option)
{
    $_rs = array(
        'module' => $module,
        'option' => $option
    );
    if ($field = jrCore_trigger_event('jrPayment', 'get_item_price_field', $_rs, $_rs, $module)) {
        if (!is_array($field) && strlen($field) > 0) {
            return $field;
        }
    }
    // Backwards compatible for FoxyCart
    $_temp = jrCore_trigger_event('jrFoxyCart', 'add_price_field', $_rs, null, $module);
    if ($_temp && is_array($_temp) && isset($_temp[$option]) && !strpos(' ' . $option, 'album')) {
        return $_temp[$option] . '_item_price';
    }
    return false;
}

/**
 * Make sure a given price is in cents
 * @param $amount mixed int|float
 * @return int
 */
function jrPayment_price_to_cents($amount)
{
    if (strpos(' ' . $amount, '-')) {
        // No negative values
        $amount = str_replace('-', '', $amount);
    }
    if (strpos(' ' . $amount, '.')) {
        $amount = ($amount * 100);
    }
    return intval($amount);
}

/**
 * Format a currency value with decimal places
 * @param $amount int Amount in cents
 * @return string
 */
function jrPayment_currency_format($amount)
{
    if (strpos(' ' . $amount, '.')) {
        $amount = ($amount * 100);
    }
    $amount = trim($amount);
    if (!$amt = jrPayment_run_plugin_function('currency_format', $amount)) {
        $amt = jrCore_number_format($amount / 100, 2);
    }
    return $amt;
}

/**
 * Create a new entry in the sales register
 * @param string $txn_datastore_id Transaction _item_id from DS
 * @param string $gateway_id Transaction ID from gateway
 * @param int $user_id User ID
 * @param array $_item Item purchased
 * @param string $tag optional search tag
 * @param int $gateway_fee
 * @param int $tax
 * @param int $created
 * @return bool|mixed
 */
function jrPayment_record_sale_in_register($txn_datastore_id, $gateway_id, $user_id, $_item, $tag = null, $gateway_fee = 0, $tax = 0, $created = 0)
{
    global $_mods;
    $fee = 0;
    if (isset($_item['_profile_id']) && jrCore_checktype($_item['_profile_id'], 'number_nz')) {
        $key = "jrpayment_record_sale_in_register_{$_item['_profile_id']}";
        if (!$_pr = jrCore_get_flag($key)) {
            $_pr = jrCore_db_get_item('jrProfile', $_item['_profile_id']);
            if ($_pr && is_array($_pr)) {
                $_qt = jrProfile_get_quota($_pr['profile_quota_id']);
                if ($_qt && is_array($_qt)) {
                    $_pr = array_merge($_pr, $_qt);
                }
                jrCore_set_flag($key, $_pr);
            }
        }

        // Figure out System fee
        if (isset($_pr['quota_jrPayment_payout_percent']) && jrCore_checktype($_pr['quota_jrPayment_payout_percent'], 'number_nn')) {
            $prc = (int) (100 - $_pr['quota_jrPayment_payout_percent']);
            $fee = round($_item['cart_amount'] * ($prc / 100));
        }
    }

    // NOTE: We only record the gateway_fee and tax ONCE per $txn_datastore_id
    // since these items are applied per cart
    if (jrCore_get_flag("jrpayment_{$txn_datastore_id}")) {
        // We've already seen this transaction
        $gateway_fee = 0;
        $tax         = 0;
    }
    jrCore_set_flag("jrpayment_{$txn_datastore_id}", 1);

    if (isset($_mods["{$_item['cart_module']}"])) {
        $cod = jrPayment_run_plugin_function('get_currency_code');
        if (!$cod) {
            $cod = 'USD';
        }
        $_fl = array(
            'r_txn_id'            => (int) $txn_datastore_id,
            'r_plugin'            => jrPayment_get_active_plugin(),
            'r_currency'          => strtoupper($cod),
            'r_gateway_id'        => jrCore_db_escape($gateway_id),
            'r_created'           => ($created > 0) ? $created : time(),
            'r_purchase_user_id'  => (int) $user_id,
            'r_seller_profile_id' => (int) $_item['_profile_id'],
            'r_module'            => $_item['cart_module'],
            'r_item_id'           => $_item['_item_id'],
            'r_field'             => str_replace('_item_price', '', $_item['cart_field']),
            'r_quantity'          => $_item['cart_quantity'],
            'r_amount'            => (int) jrPayment_price_to_cents($_item['cart_amount']),
            'r_expense'           => (isset($_item['cart_expense'])) ? intval(jrPayment_price_to_cents($_item['cart_expense'])) : 0,
            'r_shipping'          => (int) jrPayment_price_to_cents($_item['cart_shipping']),
            'r_tax'               => (int) jrPayment_price_to_cents($tax),
            'r_fee'               => $fee,
            'r_gateway_fee'       => $gateway_fee,
            'r_item_data'         => jrCore_db_escape(json_encode($_item)),
            'r_note'              => '',
            'r_tag'               => (!is_null($tag)) ? jrCore_db_escape($tag) : ''
        );
        if ($gateway_fee > 0) {
            $_fl['r_gateway_fee_checked'] = 1;
        }
        $_fl = jrCore_trigger_event('jrPayment', 'register_entry', $_fl);
        $tbl = jrCore_db_table_name('jrPayment', 'register');
        $req = "INSERT INTO {$tbl} (" . implode(',', array_keys($_fl)) . ") VALUES ('" . implode("','", $_fl) . "')";
        $iid = jrCore_db_query($req, 'INSERT_ID');
        if ($iid && $iid > 0) {
            if ($_fl['r_seller_profile_id'] > 0) {
                // We've successfully sold a profile item - make sure it is added to the vault
                if (!jrPayment_add_register_item_to_vault($_fl['r_module'], $_fl['r_item_id'], $_fl['r_field'], $_item)) {
                    jrCore_logger('CRI', "Payments: unable to copy purchased item file to system vault: {$_item['_profile_id']}/{$_fl['r_module']}/{$_fl['r_item_id']}/{$_fl['r_field']}", $_item);
                }
            }
            return $iid;
        }
    }
    return false;
}

/**
 * Make sure a register item is setup in the Vault
 * @param string $module
 * @param int $item_id
 * @param string $field
 * @param array $_data
 * @return bool
 */
function jrPayment_add_register_item_to_vault($module, $item_id, $field, $_data)
{
    $mod = jrCore_db_escape($module);
    $iid = (int) $item_id;
    $dat = jrCore_db_escape(json_encode($_data));
    $tbl = jrCore_db_table_name('jrPayment', 'vault');
    $req = "INSERT INTO {$tbl} (vault_module, vault_item_id, vault_created, vault_updated, vault_data)
            VALUES ('{$mod}', '{$iid}', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), '{$dat}')
            ON DUPLICATE KEY UPDATE vault_updated = UNIX_TIMESTAMP(), vault_data = VALUES(vault_data)";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt > 0) {

        // 0 = failed entry
        // 1 = new entry
        // 2 = entry already exists
        if ($cnt === 1) {
            jrPayment_add_file_to_vault($module, $field, $_data);
        }

        // Other modules need to be able to work with vault items
        $event = ($cnt === 1) ? 'vault_item_created' : 'vault_item_updated';
        $_args = array(
            'module'  => $module,
            'item_id' => $item_id,
            'field'   => $field
        );
        jrCore_trigger_event('jrPayment', $event, $_data, $_args);
        return true;
    }
    return false;
}

/**
 * Mark a vault item as having been deleted
 * @param string $module
 * @param int $item_id
 * @return bool
 */
function jrPayment_mark_vault_item_as_deleted($module, $item_id)
{
    $mod = jrCore_db_escape($module);
    $iid = (int) $item_id;
    $tbl = jrCore_db_table_name('jrPayment', 'vault');
    $req = "UPDATE {$tbl} SET vault_deleted = UNIX_TIMESTAMP() WHERE vault_module = '{$mod}' AND vault_item_id = {$iid} LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Add or update a file in the vault
 * @param string $module
 * @param string $field
 * @param array $_item
 * @return bool
 */
function jrPayment_add_file_to_vault($module, $field, $_item)
{
    if ($source = jrCore_get_media_file_path($module, $field, $_item)) {
        $ext = $_item["{$field}_extension"];
        if (jrCore_media_file_exists($_item['_profile_id'], "{$source}.original.{$ext}")) {
            // jrAudio_59_audio_file.mp3.original.mp3
            // We have an ORIGINAL file - we always use that for our vault
            $source = "{$source}.original.{$ext}";
        }
        if (jrCore_copy_media_file('system', $source, "jrPayment_vault_{$module}_{$_item['_item_id']}_{$field}.{$ext}")) {
            return "jrPayment_vault_{$module}_{$_item['_item_id']}_{$field}.{$ext}";
        }
    }
    return false;
}

/**
 * Get a vault item by module and item_id
 * @param string $module
 * @param int $item_id
 * @return array|bool|mixed
 */
function jrPayment_get_vault_item($module, $item_id)
{
    $mod = jrCore_db_escape($module);
    $iid = (int) $item_id;
    $tbl = jrCore_db_table_name('jrPayment', 'vault');
    $req = "SELECT * FROM {$tbl} WHERE vault_module = '{$mod}' AND vault_item_id = '{$iid}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        return json_decode($_rt['vault_data'], true);
    }
    return false;
}

/**
 * Get all register entries that match a tag
 * @param string $tag
 * @return bool|mixed
 */
function jrPayment_get_all_register_entries_with_tag($tag)
{
    if ($tag && strlen($tag) > 0) {
        $tbl = jrCore_db_table_name('jrPayment', 'register');
        $req = "SELECT * FROM {$tbl} WHERE r_tag = '" . jrCore_db_escape($tag) . "' ORDER BY r_id DESC";
        return jrCore_db_query($req, 'NUMERIC');
    }
    return false;
}

/**
 * Get DS data for items in the register
 * @param $_items array
 * @return array
 */
function jrPayment_get_data_for_items($_items)
{
    if (is_array($_items)) {
        $_ids = array();
        foreach ($_items as $_v) {
            if (isset($_v['r_module']) && isset($_v['r_item_id'])) {
                if (!isset($_ids["{$_v['r_module']}"])) {
                    $_ids["{$_v['r_module']}"] = array();
                }
                $_ids["{$_v['r_module']}"][] = $_v['r_item_id'];
            }
        }
        if (count($_ids) > 0) {
            $_it = array();
            foreach ($_ids as $mod => $_id) {
                $_rt = array(
                    'search'   => array(
                        '_item_id in ' . implode(',', $_id)
                    ),
                    'order_by' => false,
                    'limit'    => count($_ids)
                );
                $_rt = jrCore_db_search_items($mod, $_rt);
                if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
                    $_it[$mod] = array();
                    foreach ($_rt['_items'] as $v) {
                        $iid             = (int) $v['_item_id'];
                        $_it[$mod][$iid] = $v;
                    }
                }
            }
            foreach ($_items as $k => $_v) {
                $mod = $_v['r_module'];
                $iid = $_v['r_item_id'];
                if (isset($_it[$mod][$iid])) {
                    $_items[$k]['r_item_data'] = $_it[$mod][$iid];
                }
                else {
                    $_items[$k]['r_item_data'] = json_decode($_v['r_item_data'], true);
                }
            }
        }
    }
    return $_items;
}

/**
 * Refund an item by register ID
 * @param $id int Register ID
 * @param null $amount int Amount in cents refunded
 * @return bool
 */
function jrPayment_refund_item_by_id($id, $amount = null)
{
    // Is this still a valid entry?
    $rid = (int) $id;
    if ($_rt = jrPayment_get_register_entry_by_id($rid)) {

        // Are we already refunded?
        if ($_rt['r_refunded_time'] > 0) {
            return true;
        }

        $amt = $_rt['r_amount'];
        // Were we given a different amount?
        if (jrCore_checktype($amount, 'number_nz')) {
            $amt = (int) $amount;
        }
        $tbl = jrCore_db_table_name('jrPayment', 'register');
        // Has this item already been paid out?
        if ($_rt['r_seller_profile_id'] > 0 && isset($_rt['r_payed_out_time']) && $_rt['r_payed_out_time'] > 0) {
            // This item has already been paid out - we need to update the register entry so it is
            // refunded AND we subtract the amount from any future payout
            $fee = ($_rt['r_amount'] + $_rt['r_fee']);
            $not = "refund for register id {$rid} that was already paid out";
            $req = "UPDATE {$tbl} SET r_fee = '{$fee}', r_refunded_time = UNIX_TIMESTAMP(), r_refunded_amount = '{$amt}', r_note = '{$not}' WHERE r_id = '{$rid}'";
            jrCore_db_increment_key('jrProfile', $_rt['r_seller_profile_id'], 'profile_jrPayment_refund_adjust', $fee);
        }
        else {
            $req = "UPDATE {$tbl} SET r_refunded_time = UNIX_TIMESTAMP(), r_refunded_amount = '{$amt}' WHERE r_id = '{$rid}'";
        }
        $cnt = jrCore_db_query($req, 'COUNT');
        if ($cnt && $cnt === 1) {
            jrCore_trigger_event('jrPayment', 'refund_item', $_rt);
            jrCore_logger('MAJ', "Payments: refunded entry ID {$rid} in {$_rt['r_plugin']} transaction {$_rt['r_gateway_id']}", $_rt);
            return true;
        }
    }
    return false;
}

/**
 * Get a register entry by ID
 * @param $id int entry ID
 * @return mixed
 */
function jrPayment_get_register_entry_by_id($id)
{
    $rid = (int) $id;
    $tbl = jrCore_db_table_name('jrPayment', 'register');
    $req = "SELECT * FROM {$tbl} WHERE r_id = '{$rid}'";
    return jrCore_db_query($req, 'SINGLE');
}

/**
 * Get register entries by Gateway ID
 * @param $id string Gateway ID
 * @return mixed
 */
function jrPayment_get_register_entries_by_gateway_id($id)
{
    $rid = jrCore_db_escape($id);
    $tbl = jrCore_db_table_name('jrPayment', 'register');
    $req = "SELECT * FROM {$tbl} WHERE r_gateway_id = '{$rid}' OR r_tag = '{$rid}' ORDER BY r_id ASC";
    return jrCore_db_query($req, 'NUMERIC');
}

/**
 * Get a transaction
 * @param $id int Transaction ID
 * @return bool|mixed
 */
function jrPayment_get_transaction_by_id($id)
{
    $tid = (int) $id;
    if ($_tr = jrCore_db_get_item('jrPayment', $tid, true)) {
        return $_tr;
    }
    return false;
}

//--------------------------------
// CART FUNCTIONS
//--------------------------------

/**
 * Get the unique cart cookie ID for a user
 * @return string
 */
function jrPayment_get_cart_cookie_id()
{
    if ($val = jrCore_get_cookie('jrpayment_cart_id')) {
        return $val;
    }
    $val = jrCore_create_unique_string(16);
    jrCore_set_cookie('jrpayment_cart_id', $val);
    return $val;
}

/**
 * Create a new User cart
 * @return mixed
 */
function jrPayment_create_user_cart()
{
    global $_user;
    $uid = 0;
    if (jrUser_is_logged_in()) {
        $uid = (int) $_user['_user_id'];
    }
    $cid = jrCore_db_escape(jrPayment_get_cart_cookie_id());
    $tbl = jrCore_db_table_name('jrPayment', 'cart');
    $req = "SELECT cart_id FROM {$tbl} WHERE cart_user_id = '{$uid}' AND cart_session_id = '{$cid}' AND cart_status = 0";
    $_ex = jrCore_db_query($req, 'SINGLE');
    if ($_ex && is_array($_ex)) {
        return intval($_ex['cart_id']);
    }

    // We need a NEW cart
    $req = "INSERT INTO {$tbl} (cart_updated, cart_user_id, cart_session_id) VALUES(UNIX_TIMESTAMP(), '{$uid}', '{$cid}')";
    $cid = jrCore_db_query($req, 'INSERT_ID');
    if ($cid && $cid > 0) {
        return $cid;
    }
    return false;
}

/**
 * Get number of items in a user cart
 * @return int
 */
function jrPayment_get_user_cart_item_count()
{
    global $_user;
    $tbl = jrCore_db_table_name('jrPayment', 'cart');
    $tbi = jrCore_db_table_name('jrPayment', 'cart_item');
    $uid = 0;
    if (jrUser_is_logged_in()) {
        $uid = (int) $_user['_user_id'];
    }
    $cid = jrCore_db_escape(jrPayment_get_cart_cookie_id());
    $req = "SELECT i.cart_entry_id FROM {$tbi} i LEFT JOIN {$tbl} c ON(c.cart_id = i.cart_id) WHERE c.cart_user_id = '{$uid}' AND c.cart_session_id = '{$cid}' AND c.cart_status = 0";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        return count($_rt);
    }
    return 0;
}

/**
 * Get a user's cart content
 * @param $complete_check bool set to TRUE to only return a cart that has not completed
 * @return mixed
 */
function jrPayment_get_user_cart($complete_check = true)
{
    global $_user;
    $add = '';
    if ($complete_check) {
        $add = " AND c.cart_status = 0";
    }
    $uid = 0;
    if (jrUser_is_logged_in()) {
        $uid = (int) $_user['_user_id'];
    }
    $cid = jrCore_db_escape(jrPayment_get_cart_cookie_id());
    $_ci = array(
        '_items' => array()
    );
    $eid = 0;
    $tbl = jrCore_db_table_name('jrPayment', 'cart');
    $tbi = jrCore_db_table_name('jrPayment', 'cart_item');
    $req = "SELECT i.*, c.cart_hash FROM {$tbi} i LEFT JOIN {$tbl} c ON(c.cart_id = i.cart_id) WHERE c.cart_user_id = '{$uid}' AND c.cart_session_id = '{$cid}'{$add} ORDER BY i.cart_entry_id ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $_c) {
            $_tm = jrCore_db_get_item($_c['cart_module'], $_c['cart_item_id']);
            if ($_tm && is_array($_tm)) {
                $_ci['_items']["{$_c['cart_entry_id']}"] = array_merge($_c, $_tm);
                if ($eid === 0) {
                    $eid = $_c['cart_entry_id'];
                }
            }
        }
    }
    if (count($_ci['_items']) > 0) {
        $_ci['cart_id']   = $_ci['_items'][$eid]['cart_id'];
        $_ci['cart_hash'] = $_ci['_items'][$eid]['cart_hash'];
        return $_ci;
    }
    return false;
}

/**
 * Get total value of a cart
 * @param $_cart array cart items
 * @return int
 */
function jrPayment_get_cart_total($_cart)
{
    global $_conf;
    $tot = 0;
    if (isset($_cart['_items']) && is_array($_cart['_items'])) {
        foreach ($_cart['_items'] as $_e) {
            $tot += $_e['cart_amount'];
        }
    }
    if (isset($_conf['jrPayment_cart_charge']) && $_conf['jrPayment_cart_charge'] > 0) {
        $tot += intval($_conf['jrPayment_cart_charge'] * 100);
    }
    return $tot;
}

/**
 * Delete a user cart
 * @param $cart_id int Cart ID to delete
 * @return bool
 */
function jrPayment_delete_user_cart($cart_id)
{
    $cid = (int) $cart_id;
    $tbl = jrCore_db_table_name('jrPayment', 'cart');
    $req = "DELETE FROM {$tbl} WHERE cart_id = '{$cid}'";
    jrCore_db_query($req);

    $tbl = jrCore_db_table_name('jrPayment', 'cart_item');
    $req = "DELETE FROM {$tbl} WHERE cart_id = '{$cid}'";
    jrCore_db_query($req);

    return true;
}

/**
 * Get a cart by ID
 * @param $id int Cart ID
 * @return mixed
 */
function jrPayment_get_cart_by_id($id)
{
    global $_conf;
    $tbl = jrCore_db_table_name('jrPayment', 'cart');
    $tbi = jrCore_db_table_name('jrPayment', 'cart_item');
    $cid = (int) $id;
    $req = "SELECT i.*, c.cart_user_id FROM {$tbi} i LEFT JOIN {$tbl} c ON(c.cart_id = i.cart_id) WHERE c.cart_id = '{$cid}' ORDER BY i.cart_entry_id ASC";
    $_ci = array(
        '_items' => array()
    );
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $_c) {
            $_tm = jrCore_db_get_item($_c['cart_module'], $_c['cart_item_id']);
            if ($_tm && is_array($_tm)) {
                $pfx = jrCore_db_get_prefix($_c['cart_module']);
                if (isset($_tm["{$pfx}_item_shipping"])) {
                    $_tm['cart_shipping'] = $_tm["{$pfx}_item_shipping"];
                }
                $_ci['_items'][] = array_merge($_c, $_tm);
            }
        }
    }
    if (count($_ci['_items']) > 0) {
        $_ci['cart_id']      = (int) $id;
        $_ci['cart_user_id'] = $_ci['_items'][0]['cart_user_id'];
        if (isset($_conf['jrPayment_cart_charge']) && $_conf['jrPayment_cart_charge'] > 0) {
            $_ci['cart_charge'] = intval($_conf['jrPayment_cart_charge'] * 100);
        }
        return $_ci;
    }
    return false;
}

/**
 * Add an item to the cart
 * @param $cart_id int Cart ID
 * @param $module string Module
 * @param $item_id int Item ID
 * @param $field string Field
 * @return bool|mixed
 */
function jrPayment_add_item_to_cart($cart_id, $module, $item_id, $field)
{
    if (!jrCore_module_is_active($module)) {
        return 'item is not available - please try again later';
    }
    if (!$field || strlen($field) === 0) {
        return 'item is not for sale (missing field)';
    }
    if ($_it = jrCore_db_get_item($module, $item_id, true)) {

        if (!isset($_it[$field])) {
            return 'item is not for sale';
        }
        if (isset($_it[$field]) && jrCore_checktype($_it[$field], 'price')) {

            // We have a good item - add to cart
            $_tm = array(
                'cart_id' => $cart_id,
                'module'  => $module,
                'item_id' => $item_id,
                'field'   => $field
            );
            $_it = jrCore_trigger_event('jrPayment', 'cart_add_item', $_it, $_tm);

            // Does this module support shipping and handling charges?
            $shp = 0;
            $_ss = jrCore_get_registered_module_features('jrPayment', 'shipping_support');
            if (isset($_ss[$module])) {
                $ship = str_replace('_price', '_shipping', $field);
                if (isset($_it[$ship]) && jrCore_checktype($_it[$ship], 'price')) {
                    $shp = jrPayment_price_to_cents($_it[$ship]);
                }
            }

            $cid = (int) $cart_id;
            $iid = (int) $item_id;
            $prc = jrPayment_price_to_cents($_it[$field]);
            $tbl = jrCore_db_table_name('jrPayment', 'cart_item');
            $req = "INSERT INTO {$tbl} (cart_entry_time, cart_id, cart_module, cart_item_id, cart_field, cart_quantity, cart_amount, cart_shipping)
                    VALUES(UNIX_TIMESTAMP(), '{$cid}', '{$module}', '{$iid}', '{$field}', 1, '{$prc}', '{$shp}')
                    ON DUPLICATE KEY UPDATE cart_entry_time = UNIX_TIMESTAMP(), cart_amount = '{$prc}', cart_shipping = '{$shp}'";
            $eid = jrCore_db_query($req, 'INSERT_ID');
            if ($eid && $eid > 0) {
                return $eid;
            }
            return 'error adding item to cart';
        }
        // Fall through - item has not price?
        return false;
    }
    return 'item is no longer available';
}

/**
 * Remove an item from a cart
 * @param $cart_id int Cart ID
 * @param $entry_id int Entry ID in cart
 * @return bool|mixed
 */
function jrPayment_remove_item_from_cart($cart_id, $entry_id)
{
    $cid = (int) $cart_id;
    $eid = (int) $entry_id;
    $tbl = jrCore_db_table_name('jrPayment', 'cart_item');
    $req = "DELETE FROM {$tbl} WHERE cart_id = '{$cid}' and cart_entry_id = '{$eid}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_trigger_event('jrPayment', 'cart_remove_item', array('cart_id' => $cid, 'entry_id' => $eid));
        return true;
    }
    return false;
}

/**
 * Update item quantity in cart
 * @param $cart_id int Cart ID
 * @param $entry_id int Entry ID in cart
 * @param $quantity int Amount to set quantity to
 * @return bool|mixed
 */
function jrPayment_update_cart_item_quantity($cart_id, $entry_id, $quantity)
{
    $cid = (int) $cart_id;
    $eid = (int) $entry_id;
    $qty = (int) $quantity;
    $tbl = jrCore_db_table_name('jrPayment', 'cart_item');
    $req = "UPDATE {$tbl} SET cart_quantity = '{$qty}' WHERE cart_id = '{$cid}' and cart_entry_id = '{$eid}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_trigger_event('jrPayment', 'cart_update_item', array('cart_id' => $cid, 'entry_id' => $eid, 'quantity' => $qty));
        return true;
    }
    return false;
}

/**
 * Create a cart validation hash based on items
 * @param $_cr array cart details
 * @return bool
 */
function jrPayment_save_cart_validation($_cr)
{
    if ($md5 = jrPayment_get_cart_validation_hash($_cr)) {
        $cid = (int) $_cr['cart_id'];
        $tbl = jrCore_db_table_name('jrPayment', 'cart');
        $req = "UPDATE {$tbl} SET cart_updated = UNIX_TIMESTAMP(), cart_hash = '{$md5}' WHERE cart_id = '{$cid}'";
        $cnt = jrCore_db_query($req, 'COUNT');
        if ($cnt && $cnt > 0) {
            return $md5;
        }
    }
    return false;
}

/**
 * Get validation hash for a cart
 * @param $_cr array Cart
 * @return bool|string
 */
function jrPayment_get_cart_validation_hash($_cr)
{
    if (isset($_cr['_items']) && is_array($_cr['_items'])) {
        $_tm = array();
        foreach ($_cr['_items'] as $_i) {
            // For validation, we need the module, item_id, field, quantity, amount
            $_tm[] = "{$_i['cart_module']},{$_i['cart_item_id']},{$_i['cart_field']},{$_i['cart_quantity']},{$_i['cart_amount']}";
        }
        return md5(implode(',', $_tm));
    }
    return false;
}

/**
 * Validate that our cart contents matches what we get back in our transaction
 * @param $_cr array Cart Contents
 * @param $_tx array Transaction
 * @return bool
 */
function jrPayment_validate_cart($_cr, $_tx)
{
    if ($md5 = jrPayment_get_cart_validation_hash($_cr)) {
        if ($md5 == $_tx['txn_cart_hash']) {
            return true;
        }
    }
    return false;
}

/**
 * Reset a cart
 * @param $id int Cart ID
 * @return bool
 */
function jrPayment_reset_cart($id)
{
    $cid = (int) $id;
    $tbl = jrCore_db_table_name('jrPayment', 'cart_item');
    $req = "DELETE FROM {$tbl} WHERE cart_id = '{$cid}'";
    jrCore_db_query($req);
    return true;
}

/**
 * Delete a single cart item
 * @param $id int Cart Entry ID
 * @return bool
 */
function jrPayment_delete_cart_item($id)
{
    $eid = (int) $id;
    $tbl = jrCore_db_table_name('jrPayment', 'cart_item');
    $req = "DELETE FROM {$tbl} WHERE cart_entry_id = '{$eid}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt > 0) {
        return true;
    }
    return true;
}

/**
 * "complete" a cart after payment has been successfully made
 * @param $cart_id int Cart ID
 * @param $status int 1 = cart_complete, 2 = cart_pending
 * @return bool
 */
function jrPayment_set_cart_status($cart_id, $status = 1)
{
    $cst = (int) $status;
    $cid = (int) $cart_id;
    $tbl = jrCore_db_table_name('jrPayment', 'cart');
    $req = "UPDATE {$tbl} SET cart_status = {$cst} WHERE cart_id = {$cid}";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt == 1) {
        return true;
    }
    return false;
}

/**
 * Send a receipt to a buyer
 * @param $user_id int User ID
 * @param $_items array Item info
 * @return bool
 */
function jrPayment_send_buyer_receipt($user_id, $_items)
{
    $uid = (int) $user_id;
    list($sub, $msg) = jrCore_parse_email_templates('jrPayment', 'purchase_receipt', $_items);
    jrUser_notify($uid, 0, 'jrPayment', 'purchase_receipt', $sub, $msg);
    return true;
}

/**
 * Notify a profile they sold an item
 * @param int $profile_id Profile ID that SOLD the item
 * @param array $_items array of items sold
 * @param array $_buyer Buyer User info
 * @return bool
 */
function jrPayment_notify_profile_of_sold_items($profile_id, $_items, $_buyer)
{
    $pid = (int) $profile_id;
    $_us = jrProfile_get_owner_info($pid);
    if ($_us && is_array($_us)) {
        foreach ($_items['_items'] as $k => $v) {
            $_items['_items'][$k]['cart_module_url'] = jrCore_get_module_url($v['cart_module']);
        }
        foreach ($_us as $_usr) {
            list($sub, $msg) = jrCore_parse_email_templates('jrPayment', 'profile_sold_item', array('_items' => $_items['_items'], '_buyer' => $_buyer));
            jrUser_notify($_usr['_user_id'], 0, 'jrPayment', 'profile_sold_item', $sub, $msg);
        }
    }
    return true;
}

/**
 * Notify site admins that their site sold an item
 * @param array $_cart
 * @param array $_buyer
 * @return bool
 */
function jrPayment_notify_admins_of_sold_items($_cart, $_buyer)
{
    $_us = jrUser_get_admin_user_ids();
    if ($_us && is_array($_us)) {
        foreach ($_cart['_items'] as $k => $v) {
            $_cart['_items'][$k]['cart_module_url'] = jrCore_get_module_url($v['cart_module']);
        }
        foreach ($_us as $uid) {
            list($sub, $msg) = jrCore_parse_email_templates('jrPayment', 'system_sold_items', array('_items' => $_cart['_items'], '_cart' => $_cart, '_buyer' => $_buyer));
            jrUser_notify($uid, 0, 'jrPayment', 'system_sold_items', $sub, $msg);
        }
    }
    return true;
}

/**
 * Notify a buyer that their purchase is pending
 * @param array $_user
 * @param array $_cart
 * @return bool
 */
function jrPayment_notify_buyer_of_pending_purchase($_user, $_cart)
{
    if ($_user && is_array($_user)) {
        list($sub, $msg) = jrCore_parse_email_templates('jrPayment', 'purchase_pending', array('_items' => $_cart['_items'], '_cart' => $_cart, '_buyer' => $_user));
        jrUser_notify($_user['_user_id'], 0, 'jrPayment', 'purchase_receipt', $sub, $msg);
    }
    return true;
}

/**
 * Get name/title of item sold
 * @param array $_item
 * @return string
 */
function jrPayment_get_sold_item_name($_item)
{
    if ($pfx = jrCore_db_get_prefix($_item['cart_module'])) {
        if (isset($_item["{$pfx}_title"])) {
            return $_item["{$pfx}_title"];
        }
        if (isset($_item["{$pfx}_name"])) {
            return $_item["{$pfx}_name"];
        }
    }
    return "unknown item";
}

/**
 * Get the checkout URL for a cart
 * @param $amount int Total cart amount in cents
 * @param $_cart array Cart items
 * @param $plugin string
 * @return bool|mixed
 */
function jrPayment_get_checkout_url($amount, $_cart, $plugin = null)
{
    global $_conf;
    if (!is_null($plugin)) {
        jrPayment_set_active_plugin($plugin);
    }
    if ($url = jrPayment_run_plugin_function('checkout_url', $amount, $_cart)) {
        jrPayment_set_active_plugin($_conf['jrPayment_plugin']);
        if (jrCore_checktype($url, 'url')) {
            return $url;
        }
    }
    jrPayment_set_active_plugin($_conf['jrPayment_plugin']);
    // If we fall through it could be that the user is trying to check out with PayPal
    if (jrPayment_get_active_plugin() != 'paypal' && isset($_conf['jrPayment_show_paypal']) && $_conf['jrPayment_show_paypal'] == 'on') {
        jrPayment_set_active_plugin('paypal');
        if ($url = jrPayment_run_plugin_function('checkout_url', $amount, $_cart)) {
            jrPayment_set_active_plugin($_conf['jrPayment_plugin']);
            return $url;
        }
    }
    return false;
}

//--------------------------------
// PLUGIN FUNCTIONS
//--------------------------------

/**
 * Get configured payment plugins
 * @return mixed
 */
function jrPayment_get_plugins()
{
    if (!$_pl = jrCore_get_flag('jrpayment_get_plugins')) {
        $_pl = glob(APP_DIR . "/modules/jrPayment/plugins/*.php");
        if ($_pl && is_array($_pl)) {
            $_plg = array();
            foreach ($_pl as $file) {
                $plug = str_replace('.php', '', basename($file));
                if ($_mta = jrPayment_get_plugin_meta_data($plug)) {
                    $_plg[$plug] = $_mta['title'];
                }
            }
            if (count($_plg) > 0) {
                jrCore_set_flag('jrpayment_get_plugins', $_plg);
                $_pl = $_plg;
            }
        }
    }
    return is_array($_pl) ? $_pl : false;
}

/**
 * Get the active payment plugin
 * @return bool
 */
function jrPayment_get_active_plugin()
{
    global $_conf;
    if ($plug = jrCore_get_flag('jrpayment_active_plugin')) {
        return $plug;
    }
    if (isset($_conf['jrPayment_plugin']{1})) {
        return $_conf['jrPayment_plugin'];
    }
    return false;
}

/**
 * Set the active payment plugin (overrides $_conf)
 * @param $plug string Plugin
 * @return bool
 */
function jrPayment_set_active_plugin($plug)
{
    return jrCore_set_flag('jrpayment_active_plugin', $plug);
}

/**
 * Reset the active plugin back to the default
 * @return bool
 */
function jrPayment_reset_active_plugin()
{
    global $_conf;
    return jrPayment_set_active_plugin($_conf['jrPayment_plugin']);
}

/**
 * Get a plugin's meta data
 * @param $plug
 * @return bool
 */
function jrPayment_get_plugin_meta_data($plug)
{
    global $_post;
    $func = "jrPayment_plugin_{$plug}_meta";
    if (!function_exists($func)) {
        require_once APP_DIR . "/modules/jrPayment/plugins/{$plug}.php";
    }
    if (function_exists($func)) {
        return $func($_post);
    }
    return false;
}

/**
 * Get a plugin's configuration
 * @param $plug
 * @return bool|mixed
 */
function jrPayment_get_plugin_config($plug)
{
    $key = "jrpayment_plugin_config_{$plug}";
    if (!$_cf = jrCore_get_flag($key)) {
        $plg = jrCore_url_string($plug);
        $tbl = jrCore_db_table_name('jrPayment', 'plugin_config');
        $req = "SELECT config_content FROM {$tbl} WHERE config_plugin = '{$plg}'";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if ($_rt && is_array($_rt) && isset($_rt['config_content']{1})) {
            $_cf = json_decode($_rt['config_content'], true);
            jrCore_set_flag($key, $_cf);
        }
        else {
            return false;
        }
    }
    return $_cf;
}

/**
 * Run a plugin function
 * @param string $func Function to run
 * @return bool|mixed
 */
function jrPayment_run_plugin_function($func)
{
    $plug = jrPayment_get_active_plugin();
    if (!$plug || strlen($plug) === 0) {
        return false;
    }
    $temp = "jrPayment_plugin_{$plug}_{$func}";
    if (!function_exists($temp)) {
        require_once APP_DIR . "/modules/jrPayment/plugins/{$plug}.php";
    }
    $args = array_slice(func_get_args(), 1);
    if (function_exists($temp)) {
        $args = call_user_func_array($temp, $args);
        if (!$args) {
            return false;
        }
    }

    // Trigger for other payment plugins
    foreach (jrPayment_get_plugins() as $pl => $title) {
        if ($pl != $plug) {
            $temp = "jrPayment_plugin_{$pl}_{$func}_event_listener";
            if (!function_exists($temp)) {
                require_once APP_DIR . "/modules/jrPayment/plugins/{$pl}.php";
            }
            if (function_exists($temp)) {
                $args = call_user_func_array($temp, $args);
            }
        }
    }

    // Trigger for other payment modules
    return jrCore_trigger_event('jrPayment', 'plugin_function', $args, array('function' => $func));
}

/**
 * Return true if a plugin function exists
 * @param $func string Function
 * @return bool
 */
function jrPayment_plugin_function_exists($func)
{
    $plug = jrPayment_get_active_plugin();
    if (!$plug || strlen($plug) === 0) {
        return false;
    }
    $func = "jrPayment_plugin_{$plug}_{$func}";
    if (!function_exists($func)) {
        require_once APP_DIR . "/modules/jrPayment/plugins/{$plug}.php";
    }
    if (function_exists($func)) {
        return true;
    }
    return false;
}

/**
 * Check if a plugin is a valid plugin
 * @param $plugin string plugin to check
 * @return bool
 */
function jrPayment_is_valid_plugin($plugin)
{
    return (is_file(APP_DIR . "/modules/jrPayment/plugins/{$plugin}.php")) ? true : false;
}

/**
 * Save transaction to DS
 * @param $_tx array DS keys
 * @param $_cr array "Core" DS keys
 * @return mixed
 */
function jrPayment_create_transaction($_tx, $_cr = null)
{
    // Some fields we don't need to save
    unset($_tx['module'], $_tx['module_url'], $_tx['option'], $_tx['_1'], $_tx['_uri']);
    if (isset($_tx['txn_raw']) && is_array($_tx['txn_raw'])) {
        $_tx['txn_raw'] = json_encode($_tx['txn_raw']);
    }
    if (!isset($_tx['txn_plugin'])) {
        $_tx['txn_plugin'] = jrPayment_get_active_plugin();
    }
    return jrCore_db_create_item('jrPayment', $_tx, $_cr);
}

/**
 * Get the active currency code if configured
 * @return string
 */
function jrPayment_get_currency_code()
{
    if (!$code = jrPayment_run_plugin_function('get_currency_code')) {
        return '&#36;';
    }
    return jrPayment_get_currency_entity($code);
}

/**
 * Get HTML entity for a currency code
 * @param string $currency
 * @return mixed|string
 */
function jrPayment_get_currency_entity($currency)
{
    $_tmp     = array(
        'AED' => '&#1583;.&#1573;',
        'AFN' => '&#65;&#102;',
        'ALL' => '&#76;&#101;&#107;',
        'ANG' => '&#402;',
        'AOA' => '&#75;&#122;',
        'AWG' => '&#402;',
        'AZN' => '&#1084;&#1072;&#1085;',
        'BAM' => '&#75;&#77;',
        'BDT' => '&#2547;',
        'BGN' => '&#1083;&#1074;',
        'BHD' => '.&#1583;.&#1576;',
        'BIF' => '&#70;&#66;&#117;',
        'BOB' => '&#36;&#98;',
        'BRL' => '&#82;&#36;',
        'BTN' => '&#78;&#117;&#46;',
        'BWP' => '&#80;',
        'BYR' => '&#112;&#46;',
        'BZD' => '&#66;&#90;&#36;',
        'CDF' => '&#70;&#67;',
        'CHF' => '&#67;&#72;&#70;',
        'CNY' => '&#165;',
        'CRC' => '&#8353;',
        'CUP' => '&#8396;',
        'CZK' => '&#75;&#269;',
        'DJF' => '&#70;&#100;&#106;',
        'DKK' => '&#107;&#114;',
        'DOP' => '&#82;&#68;&#36;',
        'DZD' => '&#1583;&#1580;',
        'EGP' => '&#163;',
        'ETB' => '&#66;&#114;',
        'EUR' => '&#8364;',
        'FKP' => '&#163;',
        'GBP' => '&#163;',
        'GEL' => '&#4314;',
        'GHS' => '&#162;',
        'GIP' => '&#163;',
        'GMD' => '&#68;',
        'GNF' => '&#70;&#71;',
        'GTQ' => '&#81;',
        'HNL' => '&#76;',
        'HRK' => '&#107;&#110;',
        'HTG' => '&#71;',
        'HUF' => '&#70;&#116;',
        'IDR' => '&#82;&#112;',
        'ILS' => '&#8362;',
        'INR' => '&#8377;',
        'IQD' => '&#1593;.&#1583;',
        'IRR' => '&#65020;',
        'ISK' => '&#107;&#114;',
        'JEP' => '&#163;',
        'JMD' => '&#74;&#36;',
        'JOD' => '&#74;&#68;',
        'JPY' => '&#165;',
        'KES' => '&#75;&#83;&#104;',
        'KGS' => '&#1083;&#1074;',
        'KHR' => '&#6107;',
        'KMF' => '&#67;&#70;',
        'KPW' => '&#8361;',
        'KRW' => '&#8361;',
        'KWD' => '&#1583;.&#1603;',
        'KZT' => '&#1083;&#1074;',
        'LAK' => '&#8365;',
        'LBP' => '&#163;',
        'LKR' => '&#8360;',
        'LSL' => '&#76;',
        'LTL' => '&#76;&#116;',
        'LVL' => '&#76;&#115;',
        'LYD' => '&#1604;.&#1583;',
        'MAD' => '&#1583;.&#1605;.',
        'MDL' => '&#76;',
        'MGA' => '&#65;&#114;',
        'MKD' => '&#1076;&#1077;&#1085;',
        'MMK' => '&#75;',
        'MNT' => '&#8366;',
        'MOP' => '&#77;&#79;&#80;&#36;',
        'MRO' => '&#85;&#77;',
        'MUR' => '&#8360;',
        'MVR' => '.&#1923;',
        'MWK' => '&#77;&#75;',
        'MYR' => '&#82;&#77;',
        'MZN' => '&#77;&#84;',
        'NGN' => '&#8358;',
        'NIO' => '&#67;&#36;',
        'NOK' => '&#107;&#114;',
        'NPR' => '&#8360;',
        'OMR' => '&#65020;',
        'PAB' => '&#66;&#47;&#46;',
        'PEN' => '&#83;&#47;&#46;',
        'PGK' => '&#75;',
        'PHP' => '&#8369;',
        'PKR' => '&#8360;',
        'PLN' => '&#122;&#322;',
        'PYG' => '&#71;&#115;',
        'QAR' => '&#65020;',
        'RON' => '&#108;&#101;&#105;',
        'RSD' => '&#1044;&#1080;&#1085;&#46;',
        'RUB' => '&#1088;&#1091;&#1073;',
        'RWF' => '&#1585;.&#1587;',
        'SAR' => '&#65020;',
        'SCR' => '&#8360;',
        'SDG' => '&#163;',
        'SEK' => '&#107;&#114;',
        'SHP' => '&#163;',
        'SLL' => '&#76;&#101;',
        'SOS' => '&#83;',
        'STD' => '&#68;&#98;',
        'SYP' => '&#163;',
        'SZL' => '&#76;',
        'THB' => '&#3647;',
        'TJS' => '&#84;&#74;&#83;',
        'TMT' => '&#109;',
        'TND' => '&#1583;.&#1578;',
        'TOP' => '&#84;&#36;',
        'TRY' => '&#8356;',
        'TWD' => '&#78;&#84;&#36;',
        'UAH' => '&#8372;',
        'UGX' => '&#85;&#83;&#104;',
        'UYU' => '&#36;&#85;',
        'UZS' => '&#1083;&#1074;',
        'VEF' => '&#66;&#115;',
        'VND' => '&#8363;',
        'VUV' => '&#86;&#84;',
        'WST' => '&#87;&#83;&#36;',
        'XAF' => '&#70;&#67;&#70;&#65;',
        'XPF' => '&#70;',
        'YER' => '&#65020;',
        'ZAR' => '&#82;',
        'ZMK' => '&#90;&#75;',
        'ZWL' => '&#90;&#36;',
    );
    $currency = strtoupper($currency);
    return (isset($_tmp[$currency])) ? $_tmp[$currency] : '&#36;';
}

//--------------------------------
// SMARTY FUNCTIONS
//--------------------------------

/**
 * Get the active currency symbol
 * @param $params
 * @return string
 */
function smarty_function_jrPayment_get_currency_symbol($params)
{
    return jrPayment_get_currency_code();
}

/**
 * Create an add-to-cart button for an item
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrPayment_add_to_cart_button($params, $smarty)
{
    global $_conf;
    if (!isset($params['module']) || !jrCore_module_is_active($params['module'])) {
        return jrCore_smarty_missing_error('module');
    }
    if (!isset($params['item']) || !is_array($params['item'])) {
        return jrCore_smarty_missing_error('item');
    }
    if (!isset($params['field']) || strlen($params['field']) === 0) {
        return jrCore_smarty_missing_error('field');
    }
    if ((!isset($params['item']['quota_jrPayment_allowed']) || $params['item']['quota_jrPayment_allowed'] == 'off') && (!isset($params['quota_check']) || $params['quota_check'] == true)) {
        // Payment support is not enabled in this quota
        return '';
    }

    // The field of the item being sold
    $field = $params['field'];

    // This is the unique ID of the add to cart
    $uid = 'i' . substr(md5(microtime()), 0, 8);

    // Is our plugin overriding our add to cart?
    $_data = array(
        'unique_id' => $uid,
        'module'    => $params['module'],
        'item'      => $params['item'],
        'field'     => $field,
        'onclick'   => "jrPayment_add_to_cart('{$params['module']}','{$params['item']['_item_id']}','{$field}_item_price')"
    );
    $_data = jrCore_trigger_event('jrPayment', 'add_to_cart_onclick', $_data, $params);
    if (isset($params['onclick'])) {
        // We have extra onclick params
        $_data['onclick'] = rtrim(trim($_data['onclick']), ';') . ';' . $params['onclick'];
    }
    if ($_data['onclick'] == 'hide') {
        // We've been purposefully hidden by a listener
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], '');
        }
        return '';
    }

    // If this item is NOT part of a bundle, and has no price, don't show
    if (!jrCore_module_is_active('jrBundle') || !strpos(' ' . $_data['onclick'], 'jrBundle')) {
        if (!isset($params['item']["{$field}_item_price"]) || !jrCore_checktype($params['item']["{$field}_item_price"], 'price') || $params['item']["{$field}_item_price"] <= 0) {
            if (!empty($params['assign'])) {
                $smarty->assign($params['assign'], '');
            }
            return '';
        }
    }

    $_ln = jrUser_load_lang_strings();
    $alt = $_ln['jrPayment'][3];
    if (isset($params['alt'])) {
        $alt = str_replace('"', '&quot;', $params['alt']);
    }

    if (isset($params['text']) && strlen($params['text']) > 0) {
        $out = '<a id="' . $uid . '" onclick="' . $_data['onclick'] . '">' . $params['text'] . '</a>';
    }
    else {
        $out = '<div class="cart-section"><span class="cart-price">';
        // Check for BUNDLE ONLY
        $pfx = jrCore_db_get_prefix($params['module']);
        if (jrCore_module_is_active('jrBundle') && isset($params['item']["{$pfx}_bundle_only"]) && $params['item']["{$pfx}_bundle_only"] == 'on') {
            // Regardless if this item has a price, it has been marked bundle only
            $out .= $_ln['jrBundle'][37];
        }
        elseif (jrCore_module_is_active('jrBundle') && !isset($params['item']["{$field}_item_price"]) || !jrCore_checktype($params['item']["{$field}_item_price"], 'price') || $params['item']["{$field}_item_price"] <= 0) {
            // This item must be part of a bundle - show bundle only
            $out .= $_ln['jrBundle'][37];
        }
        else {
            $out .= jrPayment_get_currency_code() . jrPayment_currency_format($params['item']["{$field}_item_price"]);
        }
        $out .= '</span><a id="' . $uid . '" onclick="' . $_data['onclick'] . '">';
        if (isset($params['image']{1})) {
            $image = "{$_conf['jrCore_base_url']}/skins/{$_conf['jrCore_active_skin']}/img/{$params['image']}";
            $width = 32;
            if (isset($params['width']) && jrCore_checktype($params['width'], 'number_nz')) {
                $width = (int) $params['width'];
            }
            $height = 32;
            if (isset($params['height']) && jrCore_checktype($params['height'], 'number_nz')) {
                $height = (int) $params['height'];
            }
            $out .= '<img class="cart-icon" src="' . $image . '" width="' . $width . '" height="' . $height . '" alt="' . $alt . '" title="' . $alt . '">';
        }
        else {
            if (!isset($params['icon']{1})) {
                $params['icon'] = 'cart';
            }
            if (!isset($params['size']) || !jrCore_checktype($params['size'], 'number_nz')) {
                $params['size'] = null;
            }
            $out .= jrCore_get_sprite_html($params['icon'], $params['size']);
        }
        $out .= '<div id="cart-success" style="display:none">' . $_ln['jrPayment'][4] . '</div></a></div>';
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

