<?php
/**
 * Jamroom FoxyCart eCommerce module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function jrFoxyCart_meta()
{

    $_tmp = array(
        'name'        => 'FoxyCart eCommerce',
        'url'         => 'foxycart',
        'version'     => '1.5.3',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Add item sales and subscriptions to your system',
        'category'    => 'ecommerce',
        'license'     => 'jcl',
        'priority'    => 80
    );
    return $_tmp;
}

/**
 * init
 */
function jrFoxyCart_init()
{
    global $_conf;

    // Core support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrFoxyCart', 'off');

    // tool views
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrFoxyCart', 'payout', array('Profile Payout', 'Payout the sales that the profiles have earned'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrFoxyCart', 'txn_details', array('Transaction Details', 'Details about a purchase transaction'));

    // Our vault_download magic view
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrFoxyCart', 'vault_download', 'view_jrFoxyCart_vault_download');

    // We send out a registration trigger so other modules can register FoxyCart support
    jrCore_register_event_trigger('jrFoxyCart', 'txn_received', 'Fired when a new transaction comes in to the webhook');
    jrCore_register_event_trigger('jrFoxyCart', 'add_price_field', 'Fired when a form is viewed to add a price field');
    jrCore_register_event_trigger('jrFoxyCart', 'my_items_row', 'Fired in site.com/foxycart/items to show the customer their purchases.');
    jrCore_register_event_trigger('jrFoxyCart', 'adding_item_to_purchase_history', 'Fires when foxycart returns a new successful transaction. Do any item delivery here.');
    jrCore_register_event_trigger('jrFoxyCart', 'purchase_recorded', 'Fires after a sold item has been recorded into the purchases table.');
    jrCore_register_event_trigger('jrFoxyCart', 'vault_download', 'Fired when a purchased Vault File is downloaded');
    jrCore_register_event_trigger('jrFoxyCart', 'my_earnings_row', 'Fired in site.com/foxycart/earnings to show the seller the items they have sold.');
    jrCore_register_event_trigger('jrFoxyCart', 'cart_url', 'Fired in the {jrFoxyCart_add_url} smarty function when constructing the cart URL - use to add extra options eg: shipping price.');
    jrCore_register_event_trigger('jrFoxyCart', 'return_to_default_quota', 'Fired when a quota on a subscription is returned to the default quota. returns the quota_id of the quota the profile is going to.');
    jrCore_register_event_trigger('jrFoxyCart', 'subscription_webhook', 'Fired when a subscription purchase is received from the FoxyCart webhook.');
    jrCore_register_event_trigger('jrFoxyCart', 'subscription_datafeed', 'Fired when the daily subscription DataFeed is received.');

    // jrFoxyCart provided Traces
    jrCore_register_module_feature('jrTrace', 'trace_event', 'jrFoxyCart', 'vault_download', 'User downloads a Vault File');

    jrCore_register_event_listener('jrCore', 'form_display', 'jrFoxyCart_profile_form_display_listener');
    jrCore_register_event_listener('jrCore', 'form_display', 'jrFoxyCart_add_price_field_listener');
    jrCore_register_event_listener('jrFoxyCart', 'my_items_row', 'jrFoxyCart_my_items_row_listener');
    jrCore_register_event_listener('jrFoxyCart', 'my_earnings_row', 'jrFoxyCart_my_earnings_row_listener');

    //add a row to the system check to make sure the API key has been set.
    jrCore_register_event_listener('jrCore', 'system_check', 'jrFoxyCart_system_check_listener');

    // We use the verify_module event to make sure the vault is setup
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrFoxyCart_verify_module_listener');

    // Create the "My Files" section for the User in their menu
    $_tmp = array(
        'group' => 'user',
        'label' => 16,
        'url'   => 'items',
        'field' => 'quota_jrFoxyCart_show_my_files'
    );
    jrCore_register_module_feature('jrCore', 'skin_menu_item', 'jrFoxyCart', 'view_files', $_tmp);

    // Setup our Subscription Browser
    $_tmp = array(
        'label'       => 34,
        'quota_check' => false
    );
    jrCore_register_module_feature('jrUser', 'account_tab', 'jrFoxyCart', 'subscription_browser', $_tmp);

    // We need to remove the subscriptions tab if there are NO subscriptions available
    jrCore_register_event_listener('jrUser', 'account_tabs', 'jrFoxyCart_account_tabs_listener');

    // My Items
    $_tmp = array(
        'label' => 16,
        'field' => 'quota_jrFoxyCart_show_my_files'
    );
    jrCore_register_module_feature('jrUser', 'account_tab', 'jrFoxyCart', 'items', $_tmp);

    // We have custom JS/CSS that needs to go into each page
    if (strlen($_conf['jrFoxyCart_store_name_url']) > 0 && jrCore_checktype($_conf['jrFoxyCart_store_name_url'], 'url_name')) {
        if (!isset($_conf['jrFoxyCart_api_version']) || $_conf['jrFoxyCart_api_version'] != '2.0') {
            jrCore_register_module_feature('jrCore', 'css', 'jrFoxyCart', '//cdn.foxycart.com/static/scripts/colorbox/1.3.23/style1_fc/colorbox.css?ver=1');
            jrCore_register_module_feature('jrCore', 'javascript', 'jrFoxyCart', '//cdn.foxycart.com/' . $_conf['jrFoxyCart_store_name_url'] . '/foxycart.colorbox.js?ver=2');
        }
        else {
            jrCore_register_module_feature('jrCore', 'javascript', 'jrFoxyCart', '//cdn.foxycart.com/' . $_conf['jrFoxyCart_store_name_url'] . '/loader.js', array('async="async"', 'defer="defer'));
        }
    }

    // We can notify the user when they sell something
    $_tmp = array(
        'label' => 21, // 'new sale'
        'help'  => 23  // 'When you make a sale of anything, do you want to receive notification?';
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrFoxyCart', 'new_sale', $_tmp);

    // We can notify the user when they sell something
    $_tmp = array(
        'label' => 124, // 'Expiring Subscription'
        'help'  => 125, // 'How would you like to be notified if your subscription is about to expire?',
        'group' => 'all'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrFoxyCart', 'subscription_expiring', $_tmp);

    $_tmp = array(
        'label' => 22, // 'Purchase Details.'
        'help'  => 24, // 'When you purchase something, do you want to get further instructions on how to retrieve it?';
        'field' => 'quota_jrFoxyCart_show_my_files'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrFoxyCart', 'new_purchase', $_tmp);

    $_tmp = array(
        'label' => 25, // 'System Sale'
        'help'  => 26, // 'When some profile sells something via foxycart, do you want a notification email?';
        'group' => 'admin'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrFoxyCart', 'system_sale', $_tmp);

    // Register our CSS
    jrCore_register_module_feature('jrCore', 'css', 'jrFoxyCart', 'jrFoxyCart.css');

    // Register our custom JS
    jrCore_register_module_feature('jrCore', 'javascript', 'jrFoxyCart', 'jrFoxyCart.js');
    if (isset($_conf['jrFoxyCart_add_to_cart_popup']) && $_conf['jrFoxyCart_add_to_cart_popup'] == 'off') {
        jrCore_register_module_feature('jrCore', 'javascript', 'jrFoxyCart', 'jrFoxyCart_no_popup.js');
    }

    // Profile tabs for Profile owners
    jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrFoxyCart', 'earnings', 31); // 31 = 'Earnings' (My Sales)
    jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrFoxyCart', 'payouts', 'payout');
    jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrFoxyCart', 'register', 'transactions');
    jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrFoxyCart', 'ledger', 'summary');
    jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrFoxyCart', 'customers', 'customers'); // 32 = 'Sales Insights'
    jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrFoxyCart', 'products', 'products'); // 33 = 'Product Performance'

    // handled expired subscriptions
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrFoxyCart_daily_maintenance_listener');
    // Handle manual subscription end dates
    jrCore_register_event_listener('jrCore', 'form_validate_exit', 'jrFoxyCart_form_validate_exit_listener');

    // Check for webhooks while in privacy mode
    jrCore_register_event_listener('jrUser', 'site_privacy_check', 'jrFoxyCart_site_privacy_check_listener');

    // Our "payments" section only shows to profile owners
    jrCore_register_module_feature('jrProfile', 'profile_menu', 'jrFoxyCart', 'owner_only', true);

    // Core item buttons
    $_tmp = array(
        'title'  => 'add to cart button',
        'icon'   => 'cart',
        'active' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'item_list_button', 'jrFoxyCart', 'jrFoxyCart_item_cart_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrFoxyCart', 'jrFoxyCart_item_cart_button', $_tmp);

    // We provide some dashboard panels
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrFoxyCart', 'sales today', 'jrFoxyCart_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrFoxyCart', 'total sales', 'jrFoxyCart_dashboard_panels');

    // Graph Support
    $_tmp = array(
        'title'    => 'Sales by Day',
        'function' => 'jrFoxyCart_graph_sales_by_day',
        'group'    => 'admin'
    );
    jrCore_register_module_feature('jrGraph', 'graph_config', 'jrFoxyCart', 'sales_by_day', $_tmp);

    return true;
}

//---------------------------------------------------------
// DASHBOARD
//---------------------------------------------------------

/**
 * Dashboard Panels
 * @param $panel
 * @return bool|int
 */
function jrFoxyCart_dashboard_panels($panel)
{
    // The panel being asked for will come in as $panel
    $out = false;
    switch ($panel) {

        case 'total sales':
            $out = array(
                'title' => jrCore_db_number_rows('jrFoxyCart', 'sale')
            );
            break;

        case 'sales today':
            $off = date_offset_get(new DateTime);
            $tbl = jrCore_db_table_name('jrFoxyCart', 'sale');
            $req = "SELECT COUNT(sale_id) AS c FROM {$tbl} WHERE FROM_UNIXTIME((`sale_time` + {$off}), '%Y%m%d') = FROM_UNIXTIME(UNIX_TIMESTAMP(), '%Y%m%d') GROUP BY sale_id";
            $_rt = jrCore_db_query($req, 'SINGLE');
            $out = array(
                'title' => (is_numeric($_rt['c'])) ? intval($_rt['c']) : 0,
                'graph' => 'sales_by_day'
            );
            break;

    }
    return ($out) ? $out : false;
}

//---------------------------------------------------------
// GRAPHS
//---------------------------------------------------------

/**
 * Profiles created by day
 * @param $module string Module
 * @param $name string Name of Graph to create
 * @param $_args array Passed in Parameters
 * @return array
 */
function jrFoxyCart_graph_sales_by_day($module, $name, $_args)
{
    $_rs = array(
        '_sets' => array(
            0 => array(
                'label'       => "Sales",
                'date_format' => '%m/%d/%Y',
                'minTickSize' => "[1, 'day']",
                'type'        => 'line',
                'pointRadius' => 3,
                '_data'       => array(),
            )
        )
    );

    // Get our data
    $old = (time() - (60 * 86400));
    $off = date_offset_get(new DateTime);
    $tbl = jrCore_db_table_name('jrFoxyCart', 'sale');
    $req = "SELECT FROM_UNIXTIME((`sale_time` + {$off}), '%Y%m%d') AS c, COUNT(`sale_id`) AS n FROM {$tbl} WHERE `sale_time` > {$old} GROUP BY c ORDER BY c ASC LIMIT 60";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $v) {
            $yr = substr($v['c'], 0, 4);
            $mn = substr($v['c'], 4, 2);
            $dy = substr($v['c'], 6, 2);
            $tm = (string) mktime(0, 0, 0, $mn, $dy, $yr);
            if (!isset($_rs['_sets'][0]['_data']["{$tm}"])) {
                $_rs['_sets'][0]['_data']["{$tm}"] = 0;
            }
            $_rs['_sets'][0]['_data']["{$tm}"] += $v['n'];
        }
    }
    return $_rs;
}

//---------------------------------------------------------
// ITEM BUTTONS
//---------------------------------------------------------

/**
 * Return "add to cart" button for the item
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrFoxyCart_item_cart_button($module, $_item, $_args, $smarty, $test_only = false)
{
    // Module must be listening for price support
    if ($module != 'jrFoxyCartBundle') {
        $_xtra = array('module' => 'jrFoxyCart');
        $_form = array();
        $_form = jrCore_trigger_event('jrFoxyCart', 'add_price_field', $_form, $_xtra, $module);
        if (!$_form || !is_array($_form) || count($_form) == 0) {
            return false;
        }
    }
    if ($test_only) {
        return true;
    }
    $_rp = array(
        'module' => $module,
        'item'   => $_item,
        'field'  => $_args['field']
    );
    return smarty_function_jrFoxyCart_add_to_cart($_rp, $smarty);
}

//-------------------------------------------------
// EVENT LISTENERS
//-------------------------------------------------

/**
 * Allow Browse/install etc while private
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrFoxyCart_site_privacy_check_listener($_data, $_user, $_conf, $_args, $event)
{
    // Is this one of our views?
    if (isset($_args['module']) && $_args['module'] == 'jrFoxyCart' && isset($_args['option'])) {
        switch ($_args['option']) {
            case 'webhook':
                $_data['allow_private_site_view'] = true;
                break;
        }
    }
    return $_data;
}

/**
 * Remove subscription tab if no subscriptions are available to the user
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrFoxyCart_account_tabs_listener($_data, $_user, $_conf, $_args, $event)
{
    // Remove the subscription browser if there are no possible subscriptions
    // or it has been disabled in the user's quota
    if (isset($_data['jrFoxyCart/subscription_browser'])) {
        if (isset($_user['quota_jrFoxyCart_show_sb']) && $_user['quota_jrFoxyCart_show_sb'] == 'off') {
            // Disabled in Quota
            unset($_data['jrFoxyCart/subscription_browser']);
        }
        else {
            $qid = (int) jrUser_get_profile_home_key('profile_quota_id');
            $tbl = jrCore_db_table_name('jrProfile', 'quota_value');
            $req = "SELECT quota_id FROM {$tbl} WHERE `module` = 'jrFoxyCart' AND `name` = 'subscription_allow' AND `quota_id` != '{$qid}' AND `value` = 'on' LIMIT 1";
            $_rt = jrCore_db_query($req, 'SINGLE');
            if (!$_rt || !is_array($_rt)) {
                // We have no available subscriptions
                unset($_data['jrFoxyCart/subscription_browser']);
            }
        }
    }
    return $_data;
}

/**
 * Move profiles to new quota_id's on subscription expiration
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrFoxyCart_form_validate_exit_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrUser_is_admin() && $_data['module'] == 'jrProfile' && $_data['option'] == 'settings_save' && isset($_data['profile_id']) && jrCore_checktype($_data['profile_id'], 'number_nz')) {

        // See if we are removing any profile quota change
        if (isset($_data['profile_quota_change_id']) && $_data['profile_quota_change_id'] == '0') {

            // Delete any existing manual subscription end dates
            $pid = (int) $_data['profile_id'];
            $tbl = jrCore_db_table_name('jrFoxyCart', 'sub_expire');
            $req = "DELETE FROM {$tbl} WHERE sub_profile_id = '{$pid}'";
            jrCore_db_query($req);

        }
        elseif (isset($_data['profile_quota_change_id']) && jrCore_checktype($_data['profile_quota_change_id'], 'number_nz')) {

            // Delete any existing manual subscription end dates
            $pid = (int) $_data['profile_id'];
            $tbl = jrCore_db_table_name('jrFoxyCart', 'sub_expire');
            $req = "DELETE FROM {$tbl} WHERE sub_profile_id = '{$pid}'";
            jrCore_db_query($req);

            // Add new manual subscription end date
            if (isset($_data['profile_quota_change_date']) && $_data['profile_quota_change_date'] > time()) {
                $qid = (int) $_data['profile_quota_change_id'];
                $dat = strftime('%Y%m%d', $_data['profile_quota_change_date']);
                $req = "INSERT INTO {$tbl} (sub_expire_date, sub_profile_id, sub_new_quota_id) VALUES ('{$dat}', '{$pid}', '{$qid}')";
                jrCore_db_query($req);
            }
        }
    }
    return $_data;
}

/**
 * Move profiles to new quota_id's on subscription expiration
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrFoxyCart_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    $dat = strftime('%Y%m%d');
    $tbl = jrCore_db_table_name('jrFoxyCart', 'sub_expire');
    $req = "SELECT * FROM {$tbl} WHERE sub_expire_date < {$dat}";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        $_qt = array();
        $_dl = array();
        foreach ($_rt as $_exp) {
            // Make sure our quota is still a valid Quota
            $qid = (int) $_exp['sub_new_quota_id'];
            if (!isset($_qt[$qid])) {
                $_qt[$qid] = jrProfile_get_quota($qid);
            }
            if (!is_array($_qt[$qid])) {
                jrCore_logger('CRI', "unable to move profile_id {$_exp['sub_profile_id']} to quota_id: {$qid} - quota no longer exists!");
                continue;
            }
            if ($_pr = jrCore_db_get_item('jrProfile', $_exp['sub_profile_id'], true)) {
                $_data = array('profile_quota_id' => $qid);
                if (jrCore_db_update_item('jrProfile', $_exp['sub_profile_id'], $_data)) {
                    $_dl[] = (int) $_exp['sub_profile_id'];
                    jrCore_logger('INF', "successfully moved profile_id {$_exp['sub_profile_id']} to quota_id: {$qid} - subscription expired");
                }
            }
        }
        if (count($_dl) > 0) {
            // Remove the expired entries
            $req = "DELETE FROM {$tbl} WHERE sub_profile_id IN(" . implode(',', $_dl) . ')';
            jrCore_db_query($req);
        }
    }

    // Next, handle subscription expiration days..
    $_qt = jrProfile_get_quotas();
    if ($_qt && is_array($_qt)) {
        foreach ($_qt as $qid => $qname) {
            $_qi = jrProfile_get_quota($qid);
            if ($_qi && is_array($_qi)) {
                if (isset($_qi['quota_jrFoxyCart_expire_notify_days']) && jrCore_checktype($_qi['quota_jrFoxyCart_expire_notify_days'], 'number_nz')) {
                    $dat = (int) $_qi['quota_jrFoxyCart_expire_notify_days'];
                    $dat = strftime('%Y%m%d', (time() + ($dat * 86400)));
                    $req = "SELECT * FROM {$tbl} WHERE sub_expire_date = {$dat}";
                    $_rt = jrCore_db_query($req, 'NUMERIC');
                    if ($_rt && is_array($_rt)) {
                        foreach ($_rt as $k => $v) {
                            $pid = (int) $v['sub_profile_id'];
                            if ($_pr = jrCore_db_get_item('jrProfile', $pid, true)) {
                                if ($_pr['profile_quota_id'] != $v['sub_new_quota_id']) {
                                    // We still have not changed... notify
                                    $_us = jrProfile_get_owner_info($pid);
                                    if ($_us && is_array($_us)) {
                                        foreach ($_us as $_owner) {
                                            $_rp = array(
                                                '_quota'   => $_qi,
                                                '_profile' => $_pr,
                                                '_subinfo' => $_rt,
                                                '_user'    => $_owner
                                            );
                                            list($sub, $msg) = jrCore_parse_email_templates('jrFoxyCart', 'subscription_expiring', $_rp);
                                            jrUser_notify($_owner['_user_id'], 0, 'jrFoxyCart', 'subscription_expiring', $sub, $msg);
                                        }
                                    }
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
 * Ensures the Vault directory is setup properly
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrFoxyCart_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    $dir = APP_DIR . '/data/media/vault';
    if (!is_dir($dir)) {
        mkdir($dir, $_conf['jrCore_dir_perms']);
        // Make sure vault is protected from browsing
        jrCore_write_to_file("{$dir}/.htaccess", 'Deny from all');
    }

    // Fix up purchase table with subscription pricing info
    $tbl = jrCore_db_table_name('jrFoxyCart', 'purchase');
    $req = "SELECT purchase_id, purchase_data FROM {$tbl} WHERE purchase_module = 'jrFoxyCart' AND purchase_seller_profile_id = '0' AND LENGTH(purchase_price) = 0";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        $_id = array();
        foreach ($_rt as $_rec) {
            if (strpos($_rec['purchase_data'], 'subscription_purchase')) {
                $_tm                           = json_decode($_rec['purchase_data'], true);
                $_id["{$_rec['purchase_id']}"] = jrFoxyCart_currency_format($_tm['product_price']);
            }
        }
        if (count($_id) > 0) {
            $req = "UPDATE {$tbl} SET purchase_price = CASE purchase_id\n";
            foreach ($_id as $id => $price) {
                $req .= "WHEN {$id} THEN '{$price}'\n";
            }
            $req .= "ELSE purchase_price END";
            $cnt = jrCore_db_query($req, 'COUNT');
            if ($cnt > 0) {
                jrCore_logger('INF', "jrFoxyCart: updated " . count($_id) . " subscription purchase entries with pricing info");
            }
        }
    }
    return $_data;
}

/**
 * Adds a "price" field to forms that have requested it
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrFoxyCart_add_price_field_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    // See if this user's quota allows sales
    if (!isset($_user['quota_jrFoxyCart_active']) || $_user['quota_jrFoxyCart_active'] != 'on') {
        // Not active for this quota
        return $_data;
    }
    // First - see if any modules have registered with us via an Event Listener
    // to have a pricing field added into their create/modify item form
    $_xtra = array('module' => 'jrFoxyCart');
    $_form = array();
    $_form = jrCore_trigger_event('jrFoxyCart', 'add_price_field', $_form, $_xtra);
    if ($_form && is_array($_form) && isset($_form["{$_data['form_view']}"])) {
        $pfx = jrCore_db_get_prefix($_data['form_params']['module']);
        if ($pfx && strlen($pfx) > 0) {
            $_lang = jrUser_load_lang_strings();
            // We've been asked to include a price field in this form
            $name = $_form["{$_data['form_view']}"];
            $_tmp = array(
                'name'          => "{$name}_item_price",
                'type'          => 'text',
                'default'       => '',
                'validate'      => 'price',
                'min'           => '0.01',
                'label'         => $_lang['jrFoxyCart'][7],
                'help'          => $_lang['jrFoxyCart'][11],
                'form_designer' => false // No form designer or it can't be turned off
            );
            if (strpos($_post['option'], 'album')) {
                $_tmp['sublabel'] = $_lang['jrFoxyCart'][20];
            }
            jrCore_form_field_create($_tmp);
        }
    }
    return $_data;
}

/**
 * Add some items to the System Check
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrFoxyCart_system_check_listener($_data, $_user, $_conf, $_args, $event)
{
    // Check for FoxyCart API key being set.
    $dat             = array();
    $dat[1]['title'] = 'FoxyCart';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = 'API Key';
    $dat[2]['class'] = 'center';

    // Make sure we can communicate with foxycart
    $_rs      = array(
        'api_action' => 'customer_list'
    );
    if (isset($_conf['jrFoxyCart_store_domain']) && jrCore_checktype($_conf['jrFoxyCart_store_domain'], 'url')) {
        $response = jrFoxyCart_api($_rs);
        if ($response) {
            $dat[3]['title'] = $_args['pass'];
            $dat[4]['title'] = 'FoxyCart API works';
        }
        else {
            $dat[3]['title'] = $_args['fail'];
            $dat[4]['title'] = 'FoxyCart API Key needs to be set. <a href="' . $_conf['jrCore_base_url'] . '/foxycart/admin/global">(Click here)</a>';
        }
    }
    else {
        $dat[3]['title'] = $_args['fail'];
        $dat[4]['title'] = 'FoxyCart is not configured. <a href="' . $_conf['jrCore_base_url'] . '/foxycart/admin/global">(Click here)</a>';
    }
    $dat[3]['class'] = 'center';
    jrCore_page_table_row($dat);
}

//--------------------------------------------------
// functions
//--------------------------------------------------

/**
 * Report an error on the FoxyCart Data Feed
 * @param $msg string Message to show/log
 * @param $_post array POST data to log
 */
function jrFoxyCart_webhook_error($msg, $_post)
{
    // Fall through - Error
    header('HTTP/1.1 400 Bad Request');
    jrCore_logger('CRI', $msg, $_post);
    jrCore_notice('CRI', $msg);
    exit;
}

/**
 * Process a FoxyCart TRANSACTION
 * This will be called for a SUBSCRIPTION START only - all other
 * subscription logic is handled in jrFoxyCart_process_subscription()
 * @param $_xml array of XML data
 * @return bool
 */
function jrFoxyCart_process_transaction($_xml)
{
    global $_conf;

    // Trigger txn_received
    $_xml = jrCore_trigger_event('jrFoxyCart', 'txn_received', $_xml);

    // Did we come out of our trigger with an item?
    if ($_xml && is_array($_xml)) {
        foreach ($_xml as $_txn) {

            // Check that we haven't already recorded this transaction
            $_ex = jrCore_db_get_item_by_key('jrFoxyCart', 'txn_id', $_txn['txn_id']);
            if ($_ex && is_array($_ex)) {
                jrCore_logger('MAJ', "jrFoxyCart_process_transaction: received duplicate transaction: {$_txn['txn_id']}", $_txn);
                return true;
            }

            jrCore_logger('INF', "jrFoxyCart_process_transaction: processing transaction id {$_txn['txn_id']}", $_txn);

            // See if this is a logged in user or a VISITOR
            if (isset($_txn['txn_custom_user_id']) && jrCore_checktype($_txn['txn_custom_user_id'], 'number_nz')) {
                // Purchase was made by a logged in user
                $uid = (int) $_txn['txn_custom_user_id'];
                jrFoxyCart_add_user_txn_fields($uid, $_txn);
            }
            else {
                // This user is NOT logged in - we are going to need to create them an account if they
                // do not already have an account in this system
                if ($uid = jrFoxyCart_create_user_from_txn($_txn)) {
                    // A new user account has been created or exists...
                    jrFoxyCart_add_user_txn_fields($uid, $_txn);
                }
                else {
                    // We could not create an account - error
                    jrCore_logger('CRI', "jrFoxyCart_process_transaction: unable to create user account for purchase", $_txn);
                    return true;
                }
            }
            $_usr = jrCore_db_get_item('jrUser', $uid, false, true);
            if (!$_usr) {
                // We should never get here...
                jrCore_logger('CRI', "jrFoxyCart_process_transaction: uid received does not have a user account", $_txn);
                return true;
            }

            // Save Transaction to DS
            $_sv = $_txn;
            if (is_array($_txn['txn_items'])) {
                $_sv['txn_items'] = json_encode($_txn['txn_items']);
            }
            jrCore_db_create_item('jrFoxyCart', $_sv);

            $murl = jrCore_get_module_url('jrFoxyCart');
            $ship = false;

            // Next - Add to My Files and Notifications
            if (isset($_txn['txn_items']) && is_array($_txn['txn_items']) && count($_txn['txn_items']) > 0) {

                foreach ($_txn['txn_items'] as $_item) {

                    // process the sale
                    switch ($_item['product_code']) {

                        // Subscription
                        case 'subscription_purchase':

                            jrFoxyCart_start_subscription($_item, $uid, $_txn);
                            break;

                        // Item Purchase
                        default:

                            // product_code is like: jrAudio-15
                            list($mod,) = explode('-', $_item['product_code'], 2);
                            jrFoxyCart_process_item_sale($_item, $uid, $_txn);

                            // Notify Owners of this sale
                            jrFoxyCart_notify_of_sale($mod, $_item, $_txn, $_usr);

                            // If this is a jrStore item, let them know about shipping
                            if ($mod == 'jrStore') {
                                $ship = true;
                            }
                            break;
                    }
                }

                // Notify the user of their purchase
                $_rp = array(
                    'system_name' => $_conf['jrCore_system_name'],
                    'murl'        => $murl,
                    '_buyer'      => $_usr,
                    'txn_id'      => $_txn['txn_id'],
                    'ship_notice' => $ship
                );
                list($sub, $msg) = jrCore_parse_email_templates('jrFoxyCart', 'new_purchase', $_rp);
                jrUser_notify($_usr['_user_id'], 0, 'jrFoxyCart', 'new_purchase', $sub, $msg);
            }
        }
    }
    return true;
}

/**
 * Process EXPIRED FoxyCart subscriptions
 * NOTE: This will be called in the Subscription Data Feed (daily)
 * subscription start is handled in jrFoxyCart_process_transaction()
 * @param $_subscriptions array of expired subs
 * @return bool
 */
function jrFoxyCart_process_expired_subscriptions($_subscriptions)
{
    // Get default signup Quota
    $dqi = 0;  // Default Quota ID
    $_qc = array();
    $_df = false;
    $_tm = jrProfile_get_signup_quotas();
    if ($_tm && is_array($_tm)) {
        $dqi = array_keys($_tm);
        $dqi = reset($dqi);
        if ($dqi && jrCore_checktype($dqi, 'number_nz')) {
            $_df = jrProfile_get_quota($dqi);
        }
    }

    $now = (int) strftime('%Y%m%d');
    foreach ($_subscriptions as $_sub) {

        // Process sub
        if (isset($_sub['customer_id']) && strlen($_sub['customer_id']) > 0) {

            // Get user Info
            $_us = jrCore_db_get_item_by_key('jrUser', 'user_foxycart_customer_id', $_sub['customer_id'], true, true);
            if (!$_us) {
                // Bad user account
                jrCore_logger('CRI', "jrFoxyCart_process_expired_subscriptions: uid received does not have a user account", $_sub);
                continue;
            }

            // Get Profile Info
            $_pr = jrCore_db_get_item('jrProfile', $_us['_profile_id'], true, true);
            if (!$_pr) {
                // Bad Profile
                jrCore_logger('CRI', "jrFoxyCart_process_expired_subscriptions: uid received does not have a valid profile", $_sub);
                continue;
            }

            // Get Quota
            $_qt = jrProfile_get_quota($_pr['profile_quota_id']);
            if (!$_qt) {
                // Bad Quota - we can still process this possibly - log it
                jrCore_logger('CRI', "jrFoxyCart_process_expired_subscriptions: profile received does not have a valid quota", $_sub);
                if ($_df) {
                    $_qt = $_df;  // Use default Quota
                }
            }
            if (!$_qt || !is_array($_qt)) {
                // Bad Quota
                jrCore_logger('CRI', "jrFoxyCart_process_expired_subscriptions: profile received does not have a valid quota (2)", $_sub);
                continue;
            }

            // See if we have a failure date
            // [first_failed_transaction_date] => 2015-01-23
            if (isset($_sub['first_failed_transaction_date'])) {
                $end = (int) str_replace('-', '', $_sub['first_failed_transaction_date']);
                if ($end < $now) {
                    // Subscription has expired - move to Quota
                    if (isset($_qt['quota_jrFoxyCart_expire_quota']) && jrCore_checktype($_qt['quota_jrFoxyCart_expire_quota'], 'number_nz')) {
                        // Make sure it is still a valid Quota
                        $qid = (int) $_qt['quota_jrFoxyCart_expire_quota'];
                        if (!isset($_qc[$qid])) {
                            $_qc[$qid] = jrProfile_get_quota($qid);
                            if (!$_qc[$qid] || !is_array($_qc[$qid])) {
                                if ($dqi > 0) {
                                    jrCore_logger('CRI', "jrFoxyCart_process_expired_subscriptions: invalid Expiration Quota in Quota ID {$_pr['profile_quota_id']} - using default signup quota - verify Quota Config", $_qt);
                                    $qid = $dqi;
                                }
                                else {
                                    jrCore_logger('CRI', "jrFoxyCart_process_expired_subscriptions: invalid Expiration Quota in Quota ID {$_pr['profile_quota_id']} - verify Quota Config", $_qt);
                                    continue;
                                }
                            }
                        }
                        if (isset($_pr['profile_quota_id']) && $_pr['profile_quota_id'] != $qid) {
                            $_up = array('profile_quota_id' => $qid);
                            if (!jrCore_db_update_item('jrProfile', $_pr['_profile_id'], $_up)) {
                                jrCore_logger('CRI', "jrFoxyCart_process_expired_subscriptions: unable to move profile_id {$_pr['_profile_id']} to quota_id {$qid} for expired subscription", $_sub);
                            }
                            else {
                                jrCore_logger('INF', "jrFoxyCart_process_expired_subscriptions: moved profile_id {$_pr['_profile_id']} to quota_id {$qid} for expired subscription", $_sub);
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
 * Updates a User account with info from FoxyCart
 * @param $uid int User ID to update
 * @param $_txn array Transaction info
 * @return int
 */
function jrFoxyCart_add_user_txn_fields($uid, $_txn)
{
    // update the user info coming back from foxycart.
    $_temp = array(
        'user_foxycart_customer_id' => $_txn['txn_customer_id']
    );
    foreach ($_txn as $k => $v) {
        if (strpos($k, 'txn_customer_') === 0) {
            switch ($k) {
                case 'txn_customer_id':
                case 'txn_customer_email':
                    break;
                default:
                    $key         = str_replace('txn_customer_', 'user_foxycart_', $k);
                    $_temp[$key] = $v;
                    break;
            }
        }
    }
    return jrCore_db_update_item('jrUser', $uid, $_temp);
}

/**
 * Create a User Account from a FoxyCart transaction
 * @param $_txn array FoxyCart transaction
 * @return mixed
 */
function jrFoxyCart_create_user_from_txn($_txn)
{
    global $_conf;
    $uname = $_txn['txn_customer_first_name'] . ' ' . $_txn['txn_customer_last_name'];
    $email = $_txn['txn_customer_email'];

    // Make sure this email address is not already being used
    if ($_us = jrCore_db_get_item_by_key('jrUser', 'user_email', $email, true)) {
        // This user account already exists - DO NOT create
        return $_us['_user_id'];
    }

    // Make sure we do not already exist
    if ($_us = jrCore_db_get_item_by_key('jrUser', 'user_name', $uname, true)) {
        // OK - we have a user already using the same name
        $uname = $_txn['txn_customer_first_name'] . ' ' . $_txn['txn_customer_last_name'] . ' ' . $_txn['txn_customer_id'];
    }

    // Create new User Account
    $pass = substr(md5(microtime()), 5, 8);
    require_once APP_DIR . '/modules/jrUser/contrib/phpass/PasswordHash.php';
    $iter = jrCore_get_advanced_setting('jrUser', 'password_iterations', 12);
    $hash  = new PasswordHash($iter, false);
    $_data = array(
        'user_name'       => $uname,
        'user_email'      => $email,
        'user_password'   => $hash->HashPassword($pass),
        'user_group'      => 'user',
        'user_language'   => $_conf['jrUser_default_language'],
        'user_active'     => 1,
        'user_validated'  => 1,
        'user_last_login' => time()
    );
    if (isset($_txn['txn_custom_extra']) && jrCore_checktype($_txn['txn_custom_extra'], 'md5')) {
        $_data['user_new_account_key'] = $_txn['txn_custom_extra'];
    }
    $uid = jrCore_db_create_item('jrUser', $_data);
    if (!$uid || !jrCore_checktype($uid, 'number_nz')) {
        jrCore_logger('MAJ', "jrFoxyCart: An error was encountered creating user account for username: {$uname}, email: {$email}");
        return false;
    }

    // update user account with correct _user_id value
    $_temp = array();
    $_core = array(
        '_user_id' => $uid
    );
    // Update account just created with proper user_id...
    jrCore_db_update_item('jrUser', $uid, $_temp, $_core);

    // User account is created - send out trigger so any listening
    // modules can do their work for this new user
    // NOTE: Profile will be created here
    $_data['_user_id'] = $uid;
    jrCore_trigger_event('jrUser', 'signup_created', $_data, $_data);

    // Send them a welcome email
    $_rp = array(
        'system_name'       => $_conf['jrCore_system_name'],
        'jamroom_url'       => $_conf['jrCore_base_url'],
        'user_name'         => $uname,
        'user_pass'         => $pass,
        'user_email'        => $email,
        'user_account_url'  => "{$_conf['jrCore_base_url']}/" . jrCore_get_module_url('jrUser') . "/account",
        'user_my_items_url' => "{$_conf['jrCore_base_url']}/" . jrCore_get_module_url('jrFoxyCart') . "/items"
    );
    list($sub, $msg) = jrCore_parse_email_templates('jrFoxyCart', 'new_user', $_rp);
    if ($email && jrCore_checktype($email, 'email')) {
        jrCore_send_email($email, $sub, $msg);
        jrCore_logger('INF', "jrFoxyCart: new account successfully created for username: {$uname}, email: {$email}");
    }
    else {
        jrCore_logger('MAJ', "jrFoxyCart: new account email address is invalid for username: {$uname}, email: {$email}");
    }
    return $uid;
}

/**
 * Process an Item Sale
 * @param $_item array Subscription info
 * @param $uid int User ID that purchased subscription
 * @param $_txn array FoxyCart transaction
 * @return bool
 */
function jrFoxyCart_process_item_sale($_item, $uid, $_txn)
{
    // [product_name] => ProJam 5 Dark Skin
    // [product_code] => jrNetworkMarket-30
    // [product_quantity] => 1
    // [product_price] => 49
    // [product_field] => market_file
    // Our Item code is Module-ItemId
    list($module, $item_id) = explode('-', $_item['product_code'], 2);
    $module  = trim($module);
    $item_id = (int) $item_id;
    if (!is_dir(APP_DIR . "/modules/{$module}")) {
        jrCore_logger('CRI', "jrFoxyCart: invalid module in product_code received: {$module} - id: {$_txn['txn_id']}", $_txn);
        return false;
    }
    if (!jrCore_checktype($item_id, 'number_nz')) {
        jrCore_logger('CRI', "jrFoxyCart: invalid item_id in product_code received: {$item_id} - id: {$_txn['txn_id']}");
        return false;
    }
    $_it = jrCore_db_get_item($module, $item_id);
    if ($_it && is_array($_it)) {

        // let the module know that an item of theirs has been sold
        $_args = array(
            'user_id'          => $uid,
            'module'           => $module,
            'item_id'          => $item_id,
            '_txn'             => $_txn,
            'product_field'    => $_item['product_field'],
            'product_quantity' => $_item['product_quantity'],
        );
        $_it   = jrCore_trigger_event('jrFoxyCart', 'adding_item_to_purchase_history', $_it, $_args);

        // If we sold a BUNDLE, then the bundle module has already added the individual items to the user's My Files Section.
        if ($module != 'jrFoxyCartBundle') {
            // Add item to user's "My Items" purchase list.
            $pid = jrFoxyCart_add_to_my_items($uid, $module, $_item['product_field'], $_it, $_txn, $_item['product_quantity']);
            // increment the sales count for the sellers profile
            jrCore_db_increment_key('jrProfile', $_it['_profile_id'], 'profile_jrFoxyCart_sales', 1);
            $bid = 0;
        }
        else {
            // Adding the items INSIDE a bundle happens above
            // in the 'adding_item_to_purchase_history' event
            $pid = 0; // purchase_id
            $bid = $item_id; // bundle_id
        }

        $_sr = jrCore_db_get_item('jrProfile', $_it['_profile_id'], true);
        if (!$_sr || !is_array($_sr)) {
            jrCore_logger('CRI', "jrFoxyCart: invalid profile_id in for item: {$_it['_profile_id']} - id: {$_txn['txn_id']}", $_txn);
        }
        $_qt = jrProfile_get_quota($_sr['profile_quota_id']);

        // Log sale of item
        $shipping = 0;
        if (isset($_txn['txn_shipping_country']) && strlen($_txn['txn_shipping_country']) == 2 && isset($_item['product_ships_from']) && strlen($_item['product_ships_from']) == 2) {
            // shipping exists
            if ($_txn['txn_shipping_country'] == $_item['product_ships_from']) {
                $shipping_fees = isset($_item['product_domestic_shipping']) ? $_item['product_domestic_shipping'] : 0.00;
            }
            else {
                $shipping_fees = isset($_item['product_international_shipping']) ? $_item['product_international_shipping'] : 0.00;
            }
            $shipping = jrFoxyCart_currency_format($shipping_fees * $_item['product_quantity']);
        }
        $gross = jrFoxyCart_currency_format(round(($_item['product_price'] * $_item['product_quantity']), 2));
        $net   = jrFoxyCart_currency_format(round($gross * ($_qt['quota_jrFoxyCart_payout_percent'] / 100), 2));
        $fees  = jrFoxyCart_currency_format($gross - $net);

        // record the item sale
        $_tm           = jrCore_db_get_item($module, $item_id, true);
        $_tm['module'] = $module;
        $tbl = jrCore_db_table_name('jrFoxyCart', 'sale');
        $req = "INSERT INTO {$tbl} (sale_purchase_id ,sale_bundle_id, sale_buyer_user_id, sale_time, sale_seller_profile_id, sale_gross, sale_shipping, sale_system_fee, sale_total_net, sale_txn_id, sale_refund_item)
                VALUES ('{$pid}', '{$bid}', '{$uid}', UNIX_TIMESTAMP(), '{$_it['_profile_id']}', '{$gross}', '{$shipping}', '{$fees}', '{$net}', '{$_txn['txn_id']}', '" . jrCore_db_escape(json_encode($_tm)) . "')";
        $pid = jrCore_db_query($req, 'INSERT_ID');
        if (!$pid || !jrCore_checktype($pid, 'number_nz')) {
            jrCore_logger('CRI', "jrFoxyCart: unable to add sale data for transaction id {$_txn['txn_id']}");
            return false;
        }

        // Increment sold count for this item
        $pfx = jrCore_db_get_prefix($module);
        jrCore_db_increment_key($module, $item_id, $pfx . '_sale_count', 1);

        return $_it;
    }
    return false;
}

/**
 * Process a Start Subscription Transaction
 * @param $_item array Subscription info
 * @param $uid int User ID that purchased subscription
 * @param $_txn array FoxyCart transaction
 * @return bool
 */
function jrFoxyCart_start_subscription($_item, $uid, $_txn)
{
    global $_user;

    // [subscription_enddate] => 0000-00-00
    // [subscription_nextdate] => 0000-00-00
    // [subscription_startdate] => 0000-00-00

    // Get this user's info
    $_usr = jrCore_db_get_item('jrUser', $uid, true);
    if (!$_usr || !is_array($_usr)) {
        jrCore_logger('CRI', "jrFoxyCart: invalid user_id received in subscription: {$uid} - user does not exist (txn_id: {$_txn['txn_id']})", $_txn);
        return false;
    }
    // Next, get info about their profile
    if (isset($_txn['txn_custom_profile_id']) && is_numeric($_txn['txn_custom_profile_id']) && $_txn['txn_custom_profile_id'] > 0) {
        $_profile = jrCore_db_get_item('jrProfile', $_txn['txn_custom_profile_id'], true);
    }
    else {
        $_profile = jrCore_db_get_item('jrProfile', $_usr['_profile_id'], true);
    }
    if (!$_profile || !is_array($_profile)) {
        jrCore_logger('CRI', "jrFoxyCart: invalid profile_id received in subscription: {$_usr['_profile_id']} - profile does not exist (txn_id: {$_txn['txn_id']})", $_txn);
        return false;
    }

    // Make sure quota is valid
    $qid = (int) $_item['product_quota_id']; // the new quota_id they are STARTING on
    $_quota = jrProfile_get_quota($qid);
    if (!$_quota || !is_array($_quota)) {
        jrCore_logger('CRI', "jrFoxyCart: invalid quota_id received in subscription: {$qid} - quota does not exist (txn_id: {$_txn['txn_id']})", $_txn);
        return false;
    }

    // Trigger subscription_start
    jrCore_trigger_event('jrFoxyCart', 'subscription_webhook', $_item, array('subscription_action' => 'start'));

    $_quota['quota_id'] = $qid; //(so it gets stored with the purchase).
    if ($_quota['quota_jrFoxyCart_subscription_allow'] == 'on') {

        //------------------------------------------
        // Change the profile to the new quota
        //------------------------------------------
        $_data = array(
            'profile_quota_id' => $qid
        );
        jrCore_db_update_item('jrProfile', $_profile['_profile_id'], $_data);

        // Update Quota Counts for quotas if we are changing
        if ($qid != $_profile['profile_quota_id']) {
            // Update counts in both Quotas
            jrProfile_increment_quota_profile_count($qid);
            jrProfile_decrement_quota_profile_count($_profile['profile_quota_id']);
        }
        // Make sure any manual expiration has been removed...
        $tbe = jrCore_db_table_name('jrFoxyCart', 'sub_expire');
        $req = "DELETE FROM {$tbe} WHERE sub_profile_id = '{$_profile['_profile_id']}'";
        jrCore_db_query($req);

        // See if this is the user updating... if so re-sync session
        if (isset($_user['_user_id']) && $_user['_user_id'] == $uid) {
            jrUser_session_sync($uid);
        }
        jrCore_form_delete_session();
        jrProfile_reset_cache($_profile['_profile_id']);

        // Record subscription purchase
        $tbl = jrCore_db_table_name('jrFoxyCart', 'purchase');
        $req = "INSERT INTO {$tbl} (purchase_created,purchase_user_id,purchase_profile_id,purchase_module,purchase_item_id,purchase_txn_id,purchase_data,purchase_price)
                VALUES (UNIX_TIMESTAMP(),'{$uid}','{$_profile['_profile_id']}','jrFoxyCart','{$qid}','{$_txn['txn_id']}','" . jrCore_db_escape(json_encode($_item)) . "', '" . jrFoxyCart_currency_format($_item['product_price']) . "')";
        $mid = jrCore_db_query($req, 'INSERT_ID');
        if (!$mid || !jrCore_checktype($mid, 'number_nz')) {
            jrCore_logger('CRI', "unable to record subscription change to quota_id ({$qid}) for profile_id: {$_profile['profile_id']} (txn_id: {$_txn['txn_id']})");
            return false;
        }
        jrCore_logger('INF', "jrFoxyCart: added subscription change to quota_id ({$qid}) for profile: {$_profile['profile_name']} (txn_id: {$_txn['txn_id']})");
        return true;
    }
    jrCore_logger('CRI', "jrFoxyCart: quota_id {$qid} does not allow subscriptions! (txn_id: {$_txn['txn_id']})");
    return false;
}

/**
 * Notify item owner and site owners of item sale
 * @param $module string Module for sold item
 * @param $_it array Item Array
 * @param $_txn array Transaction Array
 * @param $_usr array Purchasing user array
 * @return bool
 */
function jrFoxyCart_notify_of_sale($module, &$_it, &$_txn, &$_usr)
{
    global $_conf;
    $pfx = jrCore_db_get_prefix($module);
    // Notify Owners of this sale
    $_owners = jrProfile_get_owner_info($_it['_profile_id']);
    if ($_owners && is_array($_owners)) {
        $_rp = array(
            'system_name' => $_conf['jrCore_system_name'],
            'murl'        => jrCore_get_module_url('jrFoxyCart'),
            '_buyer'      => $_usr,
            'txn_id'      => $_txn['txn_id'],
            'profile_url' => $_it['profile_url']
        );
        if ($pfx && isset($_it["{$pfx}_title"])) {
            $_rp['item_name'] = $_it["{$pfx}_title"];
        }
        list($sub, $msg) = jrCore_parse_email_templates('jrFoxyCart', 'new_sale', $_rp);
        foreach ($_owners as $_o) {
            jrUser_notify($_o['_user_id'], 0, 'jrFoxyCart', 'new_sale', $sub, $msg);
        }
    }

    // Notify Master Admins of sale in system
    $_sp = array(
        'search'        => array(
            "user_group IN master,admin",
        ),
        'limit'         => 100,
        'return_keys'   => array('_user_id', 'user_name'),
        'skip_triggers' => true,
        'privacy_check' => false
    );
    $_rt = jrCore_db_search_items('jrUser', $_sp);
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
        foreach ($_rt['_items'] as $_v) {
            $_v['system_name'] = $_conf['jrCore_system_name'];
            $_v['_buyer']      = $_usr;
            $_v['txn_id']      = $_txn['txn_id'];
            if ($pfx && isset($_it["{$pfx}_title"])) {
                $_v['item_name'] = $_it["{$pfx}_title"];
            }
            elseif (isset($_it['product_name'])) {
                $_v['item_name'] = $_it['product_name'];
            }
            list($sub, $msg) = jrCore_parse_email_templates('jrFoxyCart', 'system_sale', $_v);
            jrUser_notify($_v['_user_id'], 0, 'jrFoxyCart', 'system_sale', $sub, $msg);
        }
    }
    return true;
}

/**
 * Add an Item to User's My Items section
 * @param $user_id int User_ID
 * @param $module string Module for Item
 * @param $field string Item Field
 * @param $_item array Item Info
 * @param $_txn array FoxyCart transaction
 * @param $qty int Quantity
 * @return bool
 */
function jrFoxyCart_add_to_my_items($user_id, $module, $field, $_item, $_txn, $qty = 1)
{
    $tbl = jrCore_db_table_name('jrFoxyCart', 'purchase');
    $mod = jrCore_db_escape($module);
    $fld = jrCore_db_escape($field);

    // Save to payments
    $bid = (isset($_item['product_bundle_id'])) ? $_item['product_bundle_id'] : 0;
    $req = "INSERT INTO {$tbl} (purchase_created,purchase_user_id,purchase_seller_user_id,purchase_seller_profile_id,purchase_module,purchase_field,purchase_item_id,purchase_bundle_id,purchase_txn_id,purchase_data,purchase_qty)
            VALUES (UNIX_TIMESTAMP(),'{$user_id}','{$_item['_user_id']}','{$_item['_profile_id']}','{$mod}','{$fld}','{$_item['_item_id']}','{$bid}','{$_txn['txn_id']}','" . jrCore_db_escape(json_encode($_item)) . "', '{$qty}')";
    $mid = jrCore_db_query($req, 'INSERT_ID');
    if (!$mid || !jrCore_checktype($mid, 'number_nz')) {
        jrCore_logger('CRI', "unable to add item_id {$_item['_item_id']} to my items for user_id {$user_id}");
        return false;
    }
    jrCore_logger('INF', "added {$module}/{$_item['_item_id']} to my items for user_id: {$user_id}");

    $_args = array(
        'user_id'       => $user_id,
        'module'        => $module,
        'item_id'       => $_item['_item_id'],
        '_txn'          => $_txn,
        'product_field' => (isset($_item['product_field'])) ? $_item['product_field'] : '',
        'purchase_id'   => $mid
    );
    jrCore_trigger_event('jrFoxyCart', 'purchase_recorded', $_item, $_args);
    return $mid;
}

/**
 * Returns TRUE if user profile has sold items
 * @param $_conf array Global config
 * @param $_user array Viewing user
 * @return bool
 */
function jrFoxyCart_sales_count($_conf, $_user)
{
    return (isset($_user['profile_jrFoxyCart_sales_count']) && $_user['profile_jrFoxyCart_sales_count'] > 0) ? true : false;
}

/**
 * Get count of items in the cart for a user
 * @param array $_conf Global Config
 * @param array $_user User Information
 * @return int Number of items currently in the cart
 */
function jrFoxyCart_cart_item_count($_conf, $_user)
{
    return (isset($_SESSION['user_jrFoxyCart_cart_item_count']) && $_SESSION['user_jrFoxyCart_cart_item_count'] > 0) ? intval($_SESSION['user_jrFoxyCart_cart_item_count']) : '';
}

/**
 * SHA256 encode string for FoxyCart HMAC validation
 * @param $name string Name of parameter
 * @param $value string Value of parameter
 * @param $code string Module/Item ID
 * @return string
 */
function jrFoxyCart_hmac_string($name, $value, $code)
{
    global $_conf;
    $val = htmlspecialchars($code) . htmlspecialchars($name) . htmlspecialchars($value);
    return urlencode('||') . hash_hmac('sha256', $val, $_conf['jrFoxyCart_api_key']) . ($value === "--OPEN--" ? urlencode("||open") : "");
}

/**
 * Format the site.com/foxycart/items  "My Items" past purchases row so the user can see what they purchased.
 * @param $_data
 * @param $_user
 * @param $_conf
 * @param $_args
 * @param $event
 * @return mixed
 */
function jrFoxyCart_my_items_row_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['purchase_module'] == 'jrFoxyCart') {
        $murl           = jrCore_get_module_url('jrFoxyCart');
        $_purchase_data = json_decode($_args['purchase_data'], true);
        $_data[2]['title'] = 'Subscription: "' . $_purchase_data['product_name'] . '" (' . $_purchase_data['subscription_frequency'] . ') purchased for profile_id ' . $_args['purchase_profile_id'];
        $_data[3]['title'] = jrCore_format_time($_args['purchase_created'], true);
        $_data[3]['class'] = 'center';
        $_data[5]['title'] = jrCore_page_button("a{$_args['_item_id']}", 'subscription history', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$murl}/subscription_history')");
        $_data[5]['class'] = 'center';
    }
    return $_data;
}

/**
 * queries FoxyCart and returns all xml
 * @param $_rs array containing api calls
 * @return object
 * http://docs.foxycart.com/v/1.0/products/subscriptions?s[]=subscription&s[]=list
 * http://docs.foxycart.com/v/1.0/cheat_sheet
 * http://docs.foxycart.com/v/1.0/api
 */
function jrFoxyCart_api($_rs)
{
    global $_conf;
    $_rs['api_token'] = $_conf['jrFoxyCart_api_key'];
    $url              = str_replace('http://', 'https://', $_conf['jrFoxyCart_store_domain']);
    $response         = jrCore_load_url("{$url}/api", $_rs, 'POST', 443, null, null, false);
    if ($response) {
        return @simplexml_load_string($response, null, LIBXML_NOCDATA);
    }
    return false;
}

/**
 * queries FoxyCart and returns an array
 * @param $_rs array containing api calls
 * @return object
 * http://docs.foxycart.com/v/1.0/products/subscriptions?s[]=subscription&s[]=list
 * http://docs.foxycart.com/v/1.0/cheat_sheet
 * http://docs.foxycart.com/v/1.0/api
 */
function jrFoxyCart_cart($_rs)
{
    global $_conf;
    $url      = str_replace('http://', 'https://', $_conf['jrFoxyCart_store_domain']);
    $response = jrCore_load_url("{$url}/cart", $_rs, 'POST', false);

    if ($response) {
        $_rt = json_decode($response, true);
        return $_rt;
    }
    //failed to get a response, debug log it.
    $debug = array(
        'what'      => 'jrFoxyCart_cart() failed for some reason.',
        '$_rs'      => $_rs,
        '$response' => $response,
    );
    jrCore_logger('CRI', 'no response from FoxyCart', $debug);
    return false;
}

/**
 * Listen for the Profile option page to be displayed and add payout email address field
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrFoxyCart_profile_form_display_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    switch ($_data['form_view']) {

        case 'jrProfile/settings':

            // Add in Payout Email Address
            if ($_user['quota_jrFoxyCart_payout_percent'] > 0 && $_user['quota_jrFoxyCart_active'] == 'on' && $_user['quota_jrFoxyCart_allowed'] == 'on') {
                $_tmp = array(
                    'name'          => 'profile_foxycart_payout_email',
                    'label'         => 'Payout Email Address',
                    'type'          => 'text',
                    'validate'      => 'email',
                    'help'          => 'When you make sales, this is the email address that you will be paid out to.',
                    'required'      => false,
                    'form_designer' => false
                );
                jrCore_form_field_create($_tmp);
            }

            // Add in Manual subscription fields if we have subscription Quotas
            if (jrUser_is_admin()) {

                $pid = (int) $_post['profile_id'];
                $tbl = jrCore_db_table_name('jrFoxyCart', 'sub_expire');
                $req = "SELECT * FROM {$tbl} WHERE sub_profile_id = '{$pid}'";
                $_es = jrCore_db_query($req, 'SINGLE');

                $temp = false;
                if (isset($_es['sub_expire_date']) && strlen($_es['sub_expire_date']) === 8) {
                    $y    = substr($_es['sub_expire_date'], 0, 4);
                    $m    = substr($_es['sub_expire_date'], 4, 2);
                    $d    = substr($_es['sub_expire_date'], 6, 2);
                    $temp = strtotime("{$m}/{$d}/{$y}");
                }

                $_tmp = array(
                    'name'          => 'profile_quota_change_date',
                    'label'         => 'Quota Scheduled Change Date',
                    'type'          => 'date',
                    'value'         => $temp,
                    'default'       => false,
                    'validate'      => 'date',
                    'help'          => 'If you would like to set a manual expiration date for this Profiles current Quota, enter the date this profile will be moved to the quota selected in the <strong>Change To Quota</strong> field.<br><br><strong>NOTE:</strong> The date selected here has no effect unless a valid <strong>Change To Quota</strong> quota has been selected.',
                    'required'      => false,
                    'form_designer' => false
                );
                jrCore_form_field_create($_tmp);

                $_qct = array('0' => '- No Quota Change');
                $_eqt = jrProfile_get_quotas();
                if ($_eqt && is_array($_eqt)) {
                    foreach ($_eqt as $k => $v) {
                        $_qct[$k] = $v;
                    }
                }

                $_tmp = array(
                    'name'          => 'profile_quota_change_id',
                    'label'         => 'Change To Quota',
                    'type'          => 'select',
                    'options'       => $_qct,
                    'value'         => (isset($_es['sub_new_quota_id'])) ? intval($_es['sub_new_quota_id']) : 0,
                    'validate'      => 'number_nz',
                    'help'          => 'If you have set a manual expiration date for this profiles current Quota, select the Quota the profile will be moved to on the expiration date.',
                    'required'      => false,
                    'form_designer' => false
                );
                jrCore_form_field_create($_tmp);

            }
            break;
    }
    return $_data;
}

/**
 * Format a currency value with decimal places
 * @param $price
 * @return string
 */
function jrFoxyCart_currency_format($price)
{
    global $_conf;
    $price = trim(str_replace(',', '', $price));
    switch ($_conf['jrFoxyCart_store_currency']) {
        case 'JPY':
            $val = number_format($price, 0);
            break;
        default:
            $val = number_format($price, 2);
            break;
    }
    return $val;
}

/**
 * Get "paid out" amount for a profile
 * @param $profile_id
 * @return int|string
 */
function jrFoxyCart_get_paid_out_amount($profile_id)
{
    $tbl = jrCore_db_table_name('jrFoxyCart', 'sale');
    $req = "SELECT SUM(sale_total_net) AS total FROM {$tbl} WHERE sale_seller_profile_id = '{$profile_id}' AND sale_payed_out = '1'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt) && isset($_rt['total'])) {
        return jrFoxyCart_currency_format($_rt['total']);
    }
    return 0;
}

/**
 * Get "cleared" balance for a profile that can be payed out
 * @param $profile_id
 * @return int|string
 */
function jrFoxyCart_get_cleared_balance($profile_id)
{
    global $_conf;
    $old = intval(time() - ($_conf['jrFoxyCart_payout_clears'] * 86400));
    $tbl = jrCore_db_table_name('jrFoxyCart', 'sale');
    $req = "SELECT SUM(sale_total_net) AS cleared FROM {$tbl} WHERE sale_seller_profile_id = '{$profile_id}' AND sale_time < {$old} AND sale_payed_out != '1'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt) && isset($_rt['cleared'])) {
        return jrFoxyCart_currency_format($_rt['cleared']);
    }
    return 0;
}

/**
 * Get "pending" balance for a profile
 * @param $profile_id
 * @return int|string
 */
function jrFoxyCart_get_pending_balance($profile_id)
{
    global $_conf;
    $old = intval(time() - ($_conf['jrFoxyCart_payout_clears'] * 86400));
    $tbl = jrCore_db_table_name('jrFoxyCart', 'sale');
    $req = "SELECT SUM(sale_total_net) AS pending FROM {$tbl} WHERE sale_seller_profile_id = '{$profile_id}' AND sale_time >= {$old} AND sale_payed_out != '1'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt) && isset($_rt['pending'])) {
        return jrFoxyCart_currency_format($_rt['pending']);
    }
    return 0;
}

/**
 * display the paid out amount on the earnings table
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrFoxyCart_my_earnings_row_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['payout_amount']) && $_args['payout_amount'] > 0) {
        $_data[1]['title'] = 'Paid out to: ' . $_args['payout_email'];
        $_data[3]['title'] = $_args['payout_id'];
        $_data[3]['class'] = 'center';
        $_data[5]['title'] = jrFoxyCart_currency_format($_args['payout_amount']);
        $_data[5]['class'] = 'right';
    }
    return $_data;
}

/**
 * Get subscription token from FoxyCart for a subscriber
 * @param $customer_id
 * @return mixed
 */
function jrFoxyCart_get_subscription_token($customer_id)
{
    // Get info about current subscription
    $key = "get_subscription_token_{$customer_id}";
    if (!$tkn = jrCore_is_cached('jrFoxyCart', $key)) {
        $_rs = array(
            'api_action'         => 'subscription_list',
            'is_active_filter'   => '',
            'customer_id_filter' => $customer_id
        );
        $xml = jrFoxyCart_api($_rs);
        $tkn = (string) $xml->subscriptions->subscription->sub_token;
        jrCore_add_to_cache('jrFoxyCart', $key, $tkn, 300);
    }
    return $tkn;
}

/**
 * Create Add To Cart URL for an Item
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrFoxyCart_add_url($params, $smarty)
{
    global $_conf, $_user;
    if (!isset($params['module']) || strlen($params['module']) === 0) {
        return jrCore_smarty_missing_error('module');
    }
    if (!isset($params['item']) || !is_array($params['item'])) {
        return jrCore_smarty_missing_error('item');
    }
    if (!isset($params['field']) || strlen($params['field']) === 0) {
        return jrCore_smarty_missing_error('field');
    }
    if (!isset($params['item']["{$params['field']}_item_price"])) {
        return '';
    }

    // Create our add to cart URL
    $pfx   = jrCore_db_get_prefix($params['module']);
    $title = (isset($params['item']["{$pfx}_title"])) ? $params['item']["{$pfx}_title"] : $params['item']['_item_id'];
    $m_url = jrCore_get_module_url($params['module']);
    $code  = "{$params['module']}-{$params['item']['_item_id']}";
    $_opts = array(
        'code'  => $code,
        'name'  => trim($title),
        'price' => trim($params['item']["{$params['field']}_item_price"]),
        'url'   => "{$_conf['jrCore_base_url']}/{$params['item']['profile_url']}/{$m_url}/{$params['item']['_item_id']}"
    );

    $_args = array(
        'module'  => $params['module'],
        'item_id' => $params['item']['_item_id'],
        'item'    => $params['item'],
    );
    $_opts = jrCore_trigger_event('jrFoxyCart', 'cart_url', $_opts, $_args);

    if (!isset($params['quantity_max'])) {
        $params['quantity_max'] = 1;
    }
    if (isset($params['quantity_max']) && jrCore_checktype($params['quantity_max'], 'number_nz')) {
        $_opts['quantity_max'] = $params['quantity_max'];
    }

    $_add = array();
    foreach ($_opts as $k => $v) {
        $u_code = jrFoxyCart_hmac_string($k, $v, $_opts['code']);
        $_add[] = "{$k}{$u_code}=" . urlencode($v);
    }
    if (jrUser_is_logged_in()) {
        $_add[] = 'h:user_id=' . (int) $_user['_user_id']; // purchasing user
        $_add[] = 'h:profile_id=' . (int) $_user['user_active_profile_id']; // purchasing user profile_id
    }
    else {
        $_add[] = 'h:user_id=visitor';
        $_add[] = 'h:profile_id=visitor';
    }

    // Add in our field of the item they are buying
    $_add[] = 'h:' . urlencode($code) . '_field=' . urlencode($params['field']);
    $url    = "{$_conf['jrFoxyCart_store_domain']}/cart?" . implode('&amp;', $_add);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $url);
        return '';
    }
    return $url;
}

/**
 * Create an add-to-cart section for an item
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrFoxyCart_add_to_cart($params, $smarty)
{
    if (!isset($params['item']['quota_jrFoxyCart_active']) || $params['item']['quota_jrFoxyCart_active'] == 'off') {
        return '';
    }
    if (!isset($params['module']) || !jrCore_module_is_active($params['module'])) {
        return jrCore_smarty_missing_error('module');
    }
    if (!isset($params['item']) || !is_array($params['item'])) {
        return '';
    }
    if (!isset($params['field']) || strlen($params['field']) === 0) {
        return jrCore_smarty_missing_error('field');
    }
    $price = (isset($params['item']["{$params['field']}_item_price"])) ? $params['item']["{$params['field']}_item_price"] : false;
    if (isset($params['price'])) {
        $price = $params['price'];
    }
    $out = '';
    // If this item has an individual price, show to the left
    if ($price) {
        $out .= '<div class="add_to_cart_section">';
        $out .= '<span class="add_to_cart_price">' . $price . '</span>';
        $out .= smarty_function_jrFoxyCart_add_button($params, $smarty);
        $out .= '</div>';
    }
    // See if this item is part of a bundle
    elseif (isset($params['item']["{$params['field']}_item_bundle"]) && strlen($params['item']["{$params['field']}_item_bundle"]) > 0) {
        $params['ignore_empty_url'] = true;
        $out .= '<div class="add_to_cart_section">';
        $out .= smarty_function_jrFoxyCart_add_button($params, $smarty);
        $out .= '</div>';
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Create an add-to-cart button for an item
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrFoxyCart_add_button($params, $smarty)
{
    global $_post, $_conf;
    $_pm = $params;
    unset($_pm['assign']);
    $url = smarty_function_jrFoxyCart_add_url($_pm, $smarty);
    if (!isset($url{0}) && !isset($params['ignore_empty_url'])) {
        return '';
    }
    if (!isset($params['module']) || !jrCore_module_is_active($params['module'])) {
        return jrCore_smarty_missing_error('module');
    }
    if (!isset($params['item']) || !is_array($params['item'])) {
        return jrCore_smarty_missing_error('item');
    }
    if (!isset($params['field']) || strlen($params['field']) === 0) {
        return jrCore_smarty_missing_error('field');
    }
    $field = $params['field'];
    // We must have a price or be part of a bundle
    if ((!isset($params['item']["{$field}_item_price"]) || strlen($params['item']["{$field}_item_price"]) === 0) && (!isset($params['item']["{$field}_item_bundle"]) || strlen($params['item']["{$field}_item_bundle"]) === 0)) {
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], '');
        }
        return '';
    }

    $_lang = jrUser_load_lang_strings();
    $alt   = $_lang['jrFoxyCart'][2];
    if (isset($params['alt'])) {
        $alt = str_replace('"', '&quot;', $params['alt']);
    }
    $iid     = "{$params['module']}-{$field}-{$params['item']['_item_id']}-" . substr(md5(json_encode($_post)), 0, 8);
    $onclick = (isset($params['onclick']) && strlen($params['onclick']) > 2) ? ' onclick="' . $params['onclick'] . '"' : '';

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
        // See if this item is part of any bundles
        if (!isset($params['no_bundle']) && isset($params['item']["{$field}_item_bundle"]) && strlen($params['item']["{$field}_item_bundle"]) > 0) {
            $out = '<img id="image_' . $iid . '" class="add_to_cart_icon" src="' . $image . '" width="' . $width . '" height="' . $height . '" alt="' . $alt . '" title="' . $alt . '" onclick="jrFoxyCartBundle_display_bundles(\'' . $iid . '\');"><a id="' . $iid . '" href="#"><div id="add_to_cart_success" style="display:none">' . $_lang['jrFoxyCart'][14] . '</div></a>';
            $out .= '<div id="bundle_' . $iid . '" class="bundle_box" style="display:none"><!-- bundles load here --></div>';
        }
        else {
            $out = '<a id="' . $iid . '" href="' . $url . '"' . $onclick . '><img class="add_to_cart_icon" src="' . $image . '" width="' . $width . '" height="' . $height . '" alt="' . $alt . '" title="' . $alt . '"><div id="add_to_cart_success" style="display:none">' . $_lang['jrFoxyCart'][14] . '</div></a>';
        }
    }
    else {
        if (!isset($params['icon'])) {
            $params['icon'] = 'cart';
        }
        // See if this item is part of any bundles
        if (!isset($params['no_bundle']) && isset($params['item']["{$field}_item_bundle"]) && strlen($params['item']["{$field}_item_bundle"]) > 0) {
            $out = "<a id=\"image_" . $iid . "\" onclick=\"jrFoxyCartBundle_display_bundles('" . $iid . "')\">" . jrCore_get_sprite_html($params['icon']) . '</a><a id="' . $iid . '" href="#"><div id="add_to_cart_success" style="display:none">' . $_lang['jrFoxyCart'][14] . '</div></a>';
            $out .= '<div id="bundle_' . $iid . '" class="bundle_box" style="display:none"><!-- bundles load here --></div>';
        }
        else {
            $out = '<a id="' . $iid . '" href="' . $url . '"' . $onclick . '>' . jrCore_get_sprite_html($params['icon']) . '<div id="add_to_cart_success" style="display:none">' . $_lang['jrFoxyCart'][14] . '</div></a>';
        }
    }

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Create download button for a file in a user's My Files
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrFoxyCart_my_items_download_button($params, $smarty)
{
    global $_conf, $_user;
    if (!jrCore_module_is_active($params['module'])) {
        return '';
    }
    if (!isset($params['item_id']) || !jrCore_checktype($params['item_id'], 'number_nz')) {
        return 'jrFoxyCart_my_items_download_button: invalid item_id';
    }
    $out = '';
    $tbl = jrCore_db_table_name('jrFoxyCart', 'purchase');
    $req = "SELECT purchase_item_id, purchase_module,purchase_field
              FROM {$tbl}
             WHERE purchase_module = '{$params['module']}'
               AND purchase_item_id = '{$params['item_id']}'
               AND purchase_user_id = '{$_user['_user_id']}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && $_rt['purchase_item_id'] > 0) {

        $download_url = $_conf['jrCore_base_url'] . '/' . jrCore_get_module_url($_rt['purchase_module']) . '/vault_download/' . $_rt['purchase_field'] . '/' . $_rt['purchase_item_id'];
        $iid          = "{$params['module']}-{$params['item_id']}";

        $width  = 32;
        $height = 32;
        if (isset($params['width']) && jrCore_checktype($params['width'], 'number_nz')) {
            $width = (int) $params['width'];
        }
        if (isset($params['height']) && jrCore_checktype($params['height'], 'number_nz')) {
            $height = (int) $params['height'];
        }

        if (isset($params['image']{0})) {
            $image = "{$_conf['jrCore_base_url']}/skins/{$_conf['jrCore_active_skin']}/img/{$params['image']}";
            $_lang = jrUser_load_lang_strings();
            $alt   = $_lang['jrFoxyCart'][4];
            if (isset($params['alt'])) {
                $alt = str_replace('"', '&quot;', $params['alt']);
            }
            $out = '<a id="' . $iid . '" href="' . $download_url . '"><img src="' . $image . '" width="' . $width . '" height="' . $height . '" alt="' . $alt . '" title="' . $alt . '"></a>';
        }
        else {
            if (!isset($params['icon'])) {
                $params['icon'] = 'download';
            }
            $out = '<a id="' . $iid . '" href="' . $download_url . '">' . jrCore_get_sprite_html($params['icon'], $width) . '</a>';
        }
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Return list of quotas that can be subscribed to
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrFoxyCart_subscribable_quotas($params, $smarty)
{
    //get a list of all the quotas with subscribe allowed.
    $tbl = jrCore_db_table_name('jrProfile', 'quota_value');
    $req = "SELECT quota_id FROM {$tbl} WHERE `name` = 'subscription_allow' AND `value` = 'on' ";
    $_rt = jrCore_db_query($req, 'NUMERIC');

    $_subs_quotas = array();
    foreach ($_rt as $row) {
        $_subs_quotas[$row['quota_id']]             = jrProfile_get_quota($row['quota_id']);
        $_subs_quotas[$row['quota_id']]['quota_id'] = $row['quota_id'];
    }

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $_subs_quotas);
    }
    return '';
}

/**
 * Create Add To Cart URL for an Item
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrFoxyCart_subscribe_url($params, $smarty = null)
{
    global $_conf;
    if (!isset($_conf['jrFoxyCart_store_domain']) || strlen($_conf['jrFoxyCart_store_domain']) === 0) {
        return 'subscription error: store_domain is not set in Global Config';
    }
    if (!isset($params['quota_id']) || strlen($params['quota_id']) === 0) {
        return 'subscription error: quota_id is not available for subscriptions';
    }
    $_qt = jrProfile_get_quota($params['quota_id']);
    if (!isset($_qt['quota_jrFoxyCart_subscription_allow']) || $_qt['quota_jrFoxyCart_subscription_allow'] != 'on') {
        return 'subscription error: quota does not allow subscriptions';
    }
    $url = jrFoxyCart_get_subscription_url($params['quota_id'], $_qt);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $url);
        return '';
    }
    return $url;
}

/**
 * Create a Subscription URL for use with FoxyCart
 * @param $quota_id
 * @param null $_qt
 * @return bool|string
 */
function jrFoxyCart_get_subscription_url($quota_id, $_qt = null)
{
    global $_user, $_conf;
    if (is_null($_qt) || !is_array($_qt)) {
        $_qt = jrProfile_get_quota($quota_id);
        if (!$_qt || !is_array($_qt)) {
            return false;
        }
    }
    $_opts = array(
        'code'          => 'subscription_purchase',
        'quota_id'      => $quota_id,
        'name'          => $_qt['quota_jrProfile_name'],
        'sub_frequency' => $_qt['quota_jrFoxyCart_subscription_length'],
        'price'         => $_qt['quota_jrFoxyCart_subscription_price'],
        'quantity_max'  => 1,
    );
    // See if we have a trial period for this subscription - if so, we need
    // to set the sub_startdate to a future date to handle the trial
    if (isset($_qt['quota_jrFoxyCart_subscription_trial']) && $_qt['quota_jrFoxyCart_subscription_trial'] != '0') {
        $num = intval($_qt['quota_jrFoxyCart_subscription_trial']);
        switch (substr($_qt['quota_jrFoxyCart_subscription_trial'], -1)) {
            case 'd':
                $s_date = (time() + ($num * 86400));
                break;
            case 'w':
                $s_date = (time() + (($num * 7) * 86400));
                break;
            case 'm':
                $s_date = (time() + (($num * 30) * 86400));
                break;
            case 'y':
                $s_date = (time() + (($num * 365) * 86400));
                break;
            default:
                // Invalid subscription trial period
                return 'subscription error: invalid trial period';
                break;
        }
        $_opts['sub_startdate'] = strftime('%Y%m%d', $s_date);
    }
    $_add = array();
    foreach ($_opts as $k => $v) {
        $u_code = jrFoxyCart_hmac_string($k, $v, $_opts['code']);
        $_add[] = "{$k}{$u_code}=" . urlencode($v);
    }
    if (jrUser_is_logged_in()) {
        $_add[] = 'h:user_id=' . (int) $_user['_user_id']; // purchasing user
        $_add[] = 'h:profile_id=' . (int) $_user['user_active_profile_id']; // purchasing user profile_id
    }
    else {
        $_add[] = 'h:user_id=visitor';
        $_add[] = 'h:profile_id=visitor';
    }
    $_add[] = 'empty=true';
//    $_add[] =' show_shipping_tbd=0';
    return "{$_conf['jrFoxyCart_store_domain']}/cart?" . implode('&amp;', $_add);
}

/**
 * Decodes a FoxyCart XML post
 * @param string $txn XML Transaction to decode
 * @return array|bool
 */
function jrFoxyCart_decode_xml_transaction($txn)
{
    global $_conf;
    $xml = jrFoxyCart_rc4($_conf['jrFoxyCart_api_key'], urldecode($txn));
    $xml = @simplexml_load_string($xml, null, LIBXML_NOCDATA);

    $_txn = array();
    $i    = 0;
    foreach ($xml->transactions->transaction as $transaction) {

        // Base TXN info
        $_txn[$i] = array(
            'txn_id'      => (string) $transaction->id,
            'txn_date'    => (string) $transaction->transaction_date,
            'txn_user_id' => (string) $transaction->user_id,
        );

        // Purchase Info
        $_toget = array(
            'product_total',
            'tax_total',
            'shipping_total',
            'order_total'
        );
        foreach ($_toget as $field) {
            if (isset($transaction->$field) && strlen((string) $transaction->$field) > 0) {
                $_txn[$i]["txn_{$field}"] = (string) $transaction->$field;
            }
        }

        // Transaction Customer Information
        $_toget = array(
            'customer_ip',
            'customer_id',
            'customer_first_name',
            'customer_last_name',
            'customer_company',
            'customer_email',
            'customer_address1',
            'customer_address2',
            'customer_city',
            'customer_state',
            'customer_postal_code',
            'customer_country',
            'customer_phone',
            'h:user_id',
            'h:profile_id'
        );
        foreach ($_toget as $field) {
            if (isset($transaction->$field) && strlen((string) $transaction->$field) > 0) {
                $_txn[$i]["txn_{$field}"] = (string) $transaction->$field;
            }
        }

        // Transaction Shipping info
        $_toget = array(
            'shipping_first_name',
            'shipping_last_name',
            'shipping_company',
            'shipping_email',
            'shipping_address1',
            'shipping_address2',
            'shipping_city',
            'shipping_state',
            'shipping_postal_code',
            'shipping_country',
            'shipping_phone'
        );
        foreach ($_toget as $field) {
            if (isset($transaction->$field) && strlen((string) $transaction->$field) > 0) {
                $_txn[$i]["txn_{$field}"] = (string) $transaction->$field;
            }
        }

        // FoxyCart custom fields
        if (!empty($transaction->custom_fields)) {
            foreach ($transaction->custom_fields->custom_field as $field) {
                $key                           = $field->custom_field_name;
                $_txn[$i]["txn_custom_{$key}"] = (string) $field->custom_field_value;
            }
        }

        // Product Info
        $_toget                = array(
            'product_name',
            'product_code',
            'product_quantity',
            'product_price',
            'sub_token_url',
            'subscription_enddate',
            'subscription_frequency',
            'subscription_nextdate',
            'subscription_startdate',
        );
        $_txn[$i]['txn_items'] = array();
        $j                     = 0;
        foreach ($transaction->transaction_details->transaction_detail as $detail) {
            foreach ($_toget as $field) {
                if (isset($detail->$field) && strlen((string) $detail->$field) > 0) {
                    $_txn[$i]['txn_items'][$j]["{$field}"] = (string) $detail->$field;
                }
            }
            //hidden field passed through the system options: https://jamroom.atlassian.net/browse/JR-248
            if (isset($detail->product_code) && strlen((string) $detail->product_code) > 0 && isset($_txn[$i]['txn_custom_' . $detail->product_code . '_field']) && strlen((string) $_txn[$i]['txn_custom_' . $detail->product_code . '_field']) > 0) {
                $_txn[$i]['txn_items'][$j]["product_field"] = (string) $_txn[$i]['txn_custom_' . $detail->product_code . '_field'];
            }
            foreach ($detail->transaction_detail_options->transaction_detail_option as $option) {
                $_txn[$i]['txn_items'][$j]['product_' . $option->product_option_name] = (string) $option->product_option_value;
            }
            $j++;
        }
        $i++;
    }

    return ($_txn[0]['txn_id'] > 0) ? $_txn : false;
}

/**
 * Decodes a FoxyCart XML Subscription Post
 * @param string $txn XML Transaction to decode
 * @return array|bool
 */
function jrFoxyCart_decode_xml_subscription($txn)
{
    global $_conf;
    $xml = jrFoxyCart_rc4($_conf['jrFoxyCart_api_key'], urldecode($txn));
    return json_decode(json_encode(@simplexml_load_string($xml, null, LIBXML_NOCDATA)), true);
}

/*
* Copyright 2011 Michael Cutler <m@cotdp.com>
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
* http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/

/**
 * A PHP implementation of RC4 based on the original C code from
 * the 1994 usenet post:
 *
 * http://groups.google.com/groups?selm=sternCvKL4B.Hyy@netcom.com
 *
 * @param string $key_str the key as a binary string
 * @param string $data_str the data to decrypt/encrypt as a binary string
 * @return string the result of the RC4 as a binary string
 * @author Michael Cutler <m@cotdp.com>
 */
function jrFoxyCart_rc4($key_str, $data_str)
{
    // convert input string(s) to array(s)
    $key  = array();
    $data = array();
    for ($i = 0; $i < strlen($key_str); $i++) {
        $key[] = ord($key_str{$i});
    }
    for ($i = 0; $i < strlen($data_str); $i++) {
        $data[] = ord($data_str{$i});
    }
    // prepare key
    $state  = range(0, 255);
    $len    = count($key);
    $index1 = $index2 = 0;
    for ($counter = 0; $counter < 256; $counter++) {
        $index2          = ($key[$index1] + $state[$counter] + $index2) % 256;
        $tmp             = $state[$counter];
        $state[$counter] = $state[$index2];
        $state[$index2]  = $tmp;
        $index1          = ($index1 + 1) % $len;
    }
    // rc4
    $len = count($data);
    $x   = $y = 0;
    for ($counter = 0; $counter < $len; $counter++) {
        $x         = ($x + 1) % 256;
        $y         = ($state[$x] + $y) % 256;
        $tmp       = $state[$x];
        $state[$x] = $state[$y];
        $state[$y] = $tmp;
        $data[$counter] ^= $state[($state[$x] + $state[$y]) % 256];
    }
    // convert output back to a string
    $data_str = "";
    for ($i = 0; $i < $len; $i++) {
        $data_str .= chr($data[$i]);
    }
    return $data_str;
}
