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
 * @copyright 2003 - 2016 Talldude Networks, LLC.
 */
// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Get meta data about this payment plugin
 * @return array
 */
function jrSubscribe_plugin_foxycart_metadata()
{
    return array(
        'delete_api_support' => 1,
        'expire_notify'      => 1,
        'prorate_sub_change' => 0
    );
}

/**
 * Construct Subscription URL
 * @param $action string create|update
 * @param $_plan array
 * @return mixed
 */
function jrSubscribe_plugin_foxycart_build_subscription_url($action, $_plan)
{
    global $_user;
    $_opts = array(
        'code'          => "subscription:{$_plan['_item_id']}",
        'name'          => $_plan['sub_title'],
        'sub_frequency' => str_replace(':', '', $_plan['sub_duration']),
        'price'         => $_plan['sub_item_price'],
        'quantity'      => 1,
        'quantity_max'  => 1
    );

    // See if we have a trial period for this subscription - if so, we need
    // to set the sub start date to a future date to handle the trial
    $days = 0;
    if ($action == 'create') {
        if (isset($_plan['sub_trial']) && $_plan['sub_trial'] != '0') {
            if (!jrSubscribe_profile_used_trial($_user['user_active_profile_id'], $_plan['_item_id'])) {
                // a sub_trial will always be in DAYS
                $days = jrSubscribe_convert_interval_to_days($_plan['sub_trial']);
            }
        }
    }
    else {
        // If we are in CHANGE we may have some credit
        if ($_pl = jrSubscribe_get_profile_subscription($_user['user_active_profile_id'])) {
            // This profile already has a subscription
            if ($_old = jrCore_db_get_item('jrSubscribe', $_pl['sub_plan_id'])) {
                $days = jrSubscribe_get_subscription_credit_days($_plan, $_old, $_pl['sub_expires']);
                if ($days < 2) {
                    $days = 2;  // To handle rounding we use a minimum of 2 days here
                }
            }
        }
    }
    if ($days > 0) {
        $s_date                 = (time() + ($days * 86400));
        $_opts['sub_startdate'] = strftime('%Y%m%d', $s_date);
    }
    $_add = array();
    foreach ($_opts as $k => $v) {
        $u_code = jrPayment_plugin_foxycart_hmac_string($k, $v, $_opts['code']);
        $_add[] = "{$k}{$u_code}=" . urlencode($v);
    }
    $_add[] = 'h:plan_id=' . (int) $_plan['_item_id'];                  // subscription plan
    $_add[] = 'h:user_id=' . (int) $_user['_user_id'];                  // purchasing user_id
    $_add[] = 'h:profile_id=' . (int) $_user['user_active_profile_id']; // purchasing profile_id
    $_add[] = 'empty=true';
    $config = jrPayment_get_plugin_config('foxycart');
    return "https://{$config['store_sub_domain']}.foxycart.com/cart?cart=checkout&" . implode('&', $_add);
}

/**
 * Process subscription checkout
 * @param $_post array posted params
 * @param $_user array current User
 * @param $_conf array Global Conf
 */
function jrSubscribe_plugin_view_foxycart_build_subscription($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid subscription plan ID');
        jrCore_location('referrer');
    }
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid variable subscription price');
        jrCore_location('referrer');
    }
    $min = (isset($_conf['jrSubscribe_minimum_sub_amount'])) ? intval($_conf['jrSubscribe_minimum_sub_amount']) : 100;
    if ($_post['p'] < $min) {
        jrCore_set_form_notice('error', 'invalid variable subscription price - minimum is ' . $min);
        jrCore_location('referrer');
    }
    if (!$_pl = jrCore_db_get_item('jrSubscribe', $_post['id'])) {
        jrCore_set_form_notice('error', 'invalid subscription plan ID (2)');
        jrCore_location('referrer');
    }
    if (isset($_pl['sub_variable']) && $_pl['sub_variable'] == 'on') {
        $_pl['sub_item_price'] = jrPayment_currency_format($_post['p']);
    }
    if ($url = jrSubscribe_plugin_foxycart_build_subscription_url('create', $_pl)) {
        jrCore_set_cookie('jr_sub_action', 'create', 1);
        jrCore_location($url);
    }
    jrCore_set_form_notice('error', 'error building subscription URL - please try again');
    jrCore_location('referrer');
}

/**
 * Create a NEW subscription
 * @param $_sub array subscription info
 * @param string $action
 * @return mixed
 */
function jrSubscribe_plugin_foxycart_subscribe_onclick($_sub, $action = 'create')
{
    global $_conf;
    $config = jrPayment_get_plugin_config('foxycart');
    if ($config && isset($config['store_sub_domain']) && strlen($config['store_sub_domain']) > 0) {

        // Note: If we have a variable priced subscription we have to redirect to build URL
        if (isset($_sub['sub_variable']) && $_sub['sub_variable'] == 'on') {
            $murl = jrCore_get_module_url('jrSubscribe');
            return "var p=jrSubscribe_get_sub_price({$_sub['_item_id']});jrCore_window_location('{$_conf['jrCore_base_url']}/{$murl}/plugin_view/foxycart/build_subscription/id={$_sub['_item_id']}/p=' + p)";
        }

        // We can construct URL based on plan
        $url = jrSubscribe_plugin_foxycart_build_subscription_url($action, $_sub);
        return "jrSubscribe_set_cookie('create');jrCore_window_location('{$url}')";
    }
    return false;
}

/**
 * Change to a lower priced subscription
 * @note This function is ONLY run when the user downgrades!
 * @param array $_new New Plan
 * @param array $_old Old Plan
 * @param array $_sub existing subscription
 * @return mixed
 */
function jrSubscribe_plugin_foxycart_subscribe_change_onclick($_new, $_old, $_sub)
{
    global $_user;
    $config = jrPayment_get_plugin_config('foxycart');
    if ($config && isset($config['store_sub_domain']) && strlen($config['store_sub_domain']) > 0) {
        $_opts = array(
            'code'          => "subscription:{$_new['_item_id']}",
            'name'          => $_new['sub_title'],
            'sub_frequency' => str_replace(':', '', $_new['sub_duration']),
            'price'         => $_new['sub_item_price'],
            'quantity'      => 1,
            'quantity_max'  => 1
        );

        // Get the number of CREDIT DAYS - that will be out trail
        $days = jrSubscribe_get_subscription_credit_days($_new, $_old, $_sub['sub_expires']);
        if ($days < 1) {
            $days = 1;  // Must be at least 1 day in the future
        }
        $s_date                 = (time() + ($days * 86400));
        $_opts['sub_startdate'] = strftime('%Y%m%d', $s_date);

        $_add = array();
        foreach ($_opts as $k => $v) {
            $u_code = jrPayment_plugin_foxycart_hmac_string($k, $v, $_opts['code']);
            $_add[] = "{$k}{$u_code}=" . urlencode($v);
        }
        $_add[] = 'h:plan_id=' . (int) $_new['_item_id'];                   // subscription plan
        $_add[] = 'h:user_id=' . (int) $_user['_user_id'];                  // purchasing user_id
        $_add[] = 'h:profile_id=' . (int) $_user['user_active_profile_id']; // purchasing profile_id
        $_add[] = 'empty=true';
        return "jrSubscribe_set_cookie('create');jrCore_window_location('https://{$config['store_sub_domain']}.foxycart.com/cart?cart=checkout&" . implode('&', $_add) . "')";
    }
    return false;
}

/**
 * Update CC info for a subscription
 * @param $_sub array subscription info
 * @return mixed
 */
function jrSubscribe_plugin_foxycart_subscribe_update_onclick($_sub)
{
    $config = jrPayment_get_plugin_config('foxycart');
    if ($config && isset($config['store_sub_domain']) && strlen($config['store_sub_domain']) > 0) {
        if (isset($_sub['sub_data']) && strlen($_sub['sub_data']) > 0) {
            if ($_tmp = json_decode($_sub['sub_data'], true)) {
                if (isset($_tmp['sub_token'])) {
                    // https://yourdomain.foxycart.com/cart?sub_token=SUB_TOKEN_HERE&cart=checkout&sub_cancel=true
                    return "jrSubscribe_set_cookie('update');jrCore_window_location('https://{$config['store_sub_domain']}.foxycart.com/cart?sub_token={$_tmp['sub_token']}&cart=updateinfo')";
                }
            }
        }
    }
    return false;
}

/**
 * Change an existing plan to a NEW plan
 * @param array $_old existing subscription
 * @param array $_new new subscription
 * @param int $credit_days - delay billing until X days from now
 * @return bool
 */
function jrSubscribe_plugin_foxycart_subscription_change_plan($_old, $_new, $credit_days = 0)
{
    $_md = jrSubscribe_get_sub_meta_data($_old['sub_profile_id'], $_old);
    if ($_md && isset($_md['sub_token']) && strlen($_md['sub_token']) > 0) {
        $_dt = array(
            'api_action'           => 'subscription_modify',
            'sub_token'            => $_md['sub_token'],
            'frequency'            => str_replace(':', '', $_new['sub_duration']),
            'transaction_template' => jrSubscribe_plugin_foxycart_get_subscription_template($_new)
        );
        if ($credit_days > 0) {
            // We need to delay the next payment to give time for credit
            $_dt['next_transaction_date'] = strftime('%Y-%m-%d', (time() + ($credit_days * 86400)));
        }
        $_rs = jrPayment_plugin_foxycart_api_request($_dt);
        if (!$_rs || !is_array($_rs) || !isset($_rs['result']) || strtolower($_rs['result']) != 'success') {
            jrCore_logger('CRI', "Subscribe: error updating existing subscription at foxycart", $_rs);
            jrCore_set_form_notice('error', 'error updating existing subscription at foxycart - check activity log');
            return false;
        }
    }
    return true;
}

/**
 * Mark a subscription as inactive at FoxyCart
 * @param array $_sub existing subscription
 * @return bool
 */
function jrSubscribe_plugin_foxycart_delete_subscription($_sub)
{
    $_md = jrSubscribe_get_sub_meta_data($_sub['sub_profile_id'], $_sub);
    if ($_md && isset($_md['sub_token']) && strlen($_md['sub_token']) > 0) {
        $_dt = array(
            'api_action' => 'subscription_modify',
            'sub_token'  => $_md['sub_token'],
            'is_active'  => 0
        );
        $_rs = jrPayment_plugin_foxycart_api_request($_dt);
        if (!$_rs || !is_array($_rs) || !isset($_rs['result']) || strtolower($_rs['result']) != 'success') {
            jrCore_logger('CRI', "Subscribe: error canceling existing subscription at foxycart", $_rs);
            return false;
        }
    }
    return true;
}

/**
 * URL to cancel a subscription
 * @param $_sub array subscription info
 * @return mixed
 */
function jrSubscribe_plugin_foxycart_subscribe_cancel_onclick($_sub)
{
    $config = jrPayment_get_plugin_config('foxycart');
    if ($config && isset($config['store_sub_domain']) && strlen($config['store_sub_domain']) > 0) {
        if (isset($_sub['sub_data']) && strlen($_sub['sub_data']) > 0) {
            if ($_tmp = json_decode($_sub['sub_data'], true)) {
                if (isset($_tmp['sub_token'])) {
                    // https://yourdomain.foxycart.com/cart?sub_token=SUB_TOKEN_HERE&cart=checkout&sub_cancel=true
                    return "jrSubscribe_set_cookie('cancel');jrCore_window_location('https://{$config['store_sub_domain']}.foxycart.com/cart?sub_token={$_tmp['sub_token']}&cart=checkout&sub_cancel=true')";
                }
            }
        }
    }
    return false;
}

/**
 * Get info about an existing subscription
 * @param string $token Subscription token
 * @return mixed
 */
function jrSubscribe_plugin_foxycart_get_subscription_info($token)
{
    $_dt = array(
        'api_action' => 'subscription_get',
        'sub_token'  => $token
    );
    $_rs = jrPayment_plugin_foxycart_api_request($_dt);
    if (!$_rs || !is_array($_rs) || !isset($_rs['result']) || strtolower($_rs['result']) != 'success' || empty($_rs['subscription'])) {
        return false;
    }
    return $_rs['subscription'];
}

/**
 * Check for incoming subscription transactions
 * @param $_txn array incoming transaction
 * @return mixed
 */
function jrSubscribe_plugin_foxycart_webhook_parse($_txn)
{
    global $_conf, $_post;
    // Is this a subscription transaction?
    if (isset($_txn['txn_raw']) && is_array($_txn['txn_raw'])) {
        $_raw = $_txn['txn_raw']['transactions']['transaction'];
        if (isset($_raw['custom_fields']['custom_field'])) {
            $_fl = array();
            foreach ($_raw['custom_fields']['custom_field'] as $n => $f) {
                if (is_array($f)) {
                    $_fl["{$f['custom_field_name']}"] = $f['custom_field_value'];
                }
            }
            if (!isset($_fl['plan_id'])) {
                // This could be a sub that was imported
                if (isset($_fl['profile_id']) && $_fl['profile_id'] > 0) {
                    $pid = (int) $_fl['profile_id'];
                    if ($_sub = jrSubscribe_get_profile_subscription($pid)) {
                        $_fl['plan_id'] = (int) $_sub['sub_plan_id'];
                    }
                }
            }
            if (isset($_fl['plan_id'])) {
                $_txn['txn_type']    = 'subscription';
                $_txn['txn_user_id'] = $_fl['user_id'];
                // Do we have a free trial?
                if (isset($_raw['transaction_details']['transaction_detail']['is_future_line_item']) && $_raw['transaction_details']['transaction_detail']['is_future_line_item'] == 1) {
                    // We are in a free trial
                    $_txn['txn_free_trial'] = 1;
                }
            }
        }
    }
    else {
        // Is this the daily subscription feed?
        if (isset($_post['FoxySubscriptionData']) && strlen($_post['FoxySubscriptionData']) > 0) {
            $_xm = jrPayment_plugin_foxycart_rc4($_conf['jrFoxyCart_api_key'], urldecode($_post['FoxySubscriptionData']));
            $_xm = @simplexml_load_string($_xm, null, LIBXML_NOCDATA);
            $_xm = json_decode(json_encode((array) $_xm), true);
            if ($_xm && is_array($_xm)) {
                // Create queue where we do our actual work
                $_queue = array(
                    'plugin'   => 'foxycart',
                    'function' => 'process_daily_subscription_feed',
                    'xml'      => $_xm
                );
                jrCore_queue_create('jrSubscribe', 'subscription_tasks', $_queue);
            }
            return jrPayment_plugin_foxycart_webhook_response('success');
        }
    }
    return $_txn;
}

/**
 * hourly maintenance
 * @param array $_queue
 * @return bool
 */
function jrSubscribe_plugin_foxycart_process_daily_subscription_feed($_queue)
{
    global $_conf;
    if (isset($_queue['xml']) && is_array($_queue['xml'])) {

        jrCore_logger('INF', "Subscribe: processing daily FoxyCart subscription feed", $_queue['xml']);

        // Our daily subscription feed will include expired or expiring subscriptions
        if (isset($_queue['xml']['subscriptions']['subscription']) && is_array($_queue['xml']['subscriptions']['subscription'])) {

            // If we only have 1 entry it will come in a bit differently
            if (!isset($_queue['xml']['subscriptions']['subscription'][0]) && isset($_queue['xml']['subscriptions']['subscription']['customer_email'])) {
                $_subs = array($_queue['xml']['subscriptions']['subscription']);
            }
            else {
                $_subs = $_queue['xml']['subscriptions']['subscription'];
            }

            foreach ($_subs as $_sub) {

                // [past_due_amount] => 75.00
                // [customer_email] => xxx@verizon.net
                if (isset($_sub['past_due_amount']) && $_sub['past_due_amount'] > 0 && isset($_sub['customer_email']) && strpos($_sub['customer_email'], '@')) {

                    // Must have a local account
                    if ($_us = jrCore_db_get_item_by_key('jrUser', 'user_email', $_sub['customer_email'])) {

                        // We have found the subscriber

                        // Does this profile have a subscription entry?
                        $pid = (int) $_us['_profile_id'];
                        if ($_sb = jrSubscribe_get_subscription_by_profile_id($pid)) {

                            // Check sub status
                            switch ($_sb['sub_status']) {
                                case 'free':     // subscription is manually controlled
                                case 'inactive': // subscription is already inactive
                                    continue 2;
                                    break;
                            }

                        }
                        else {
                            // No subscription found for this profile - likely already removed
                            continue;
                        }

                        // Is this profile on a good plan?
                        if (!$_pl = jrCore_db_get_item('jrSubscribe', $_sb['sub_plan_id'], true)) {
                            jrCore_logger('MIN', "Subscribe: unable to find plan information for profile_id {$pid} (@{$_us['profile_url']})", array('_foxy' => $_sub, '_sub' => $_sb, '_user' => $_us));
                            continue;
                        }

                        // Check for the failure date - we'll give them a little but before we remove the subscription
                        // [first_failed_transaction_date] => 2018-01-13
                        list($fyear, $fmonth, $fday) = explode('-', $_sub['first_failed_transaction_date']);
                        $expired = mktime(23, 23, 59, $fmonth, $fday, $fyear);
                        if ($expired > (time() - 86400)) {
                            // Make sure we've moved fully beyond the expires date
                            continue;
                        }

                        // Have they may any successful payments SINCE - i.e. maybe the sub token changed?
                        $uid = (int) $_us['_user_id'];
                        $lid = (int) $_sb['sub_plan_id'];
                        $tbl = jrCore_db_table_name('jrPayment', 'register');
                        $req = "SELECT * FROM {$tbl} WHERE r_purchase_user_id = {$uid} AND r_module = 'jrSubscribe' AND r_item_id = {$lid} AND r_created > {$expired} ORDER BY r_created DESC LIMIT 1";
                        $_ap = jrCore_db_query($req, 'SINGLE');
                        if ($_ap && is_array($_ap)) {
                            // We have had a successful payment SINCE the cancel - likely change in sub token
                            if (!empty($_sub['sub_token_url'])) {
                                if ($sub_token = jrSubscribe_plugin_foxycart_get_sub_token_from_url($_sub['sub_token_url'])) {
                                    if (strlen($sub_token) > 10) {
                                        if ($_dat = json_decode($_sb['sub_data'], true)) {
                                            if (empty($_dat['sub_token']) || $_dat['sub_token'] != $sub_token) {
                                                // New Sub token - update
                                                jrSubscribe_save_sub_metadata_key($pid, 'sub_token', $sub_token);
                                                jrCore_logger('INF', "Subscribe: updated sub_token for profile_id: {$pid} - token appears to have changed", $_sub);
                                            }
                                        }
                                    }
                                }
                            }
                            // Have paid recently
                            jrCore_logger('MIN', "Subscribe: skipping foxycart feed entry for past due subscriber - profile has made newer payment", array('datafeed' => $_sub, 'existing' => $_sb, 'register' => $_ap));
                            continue;
                        }

                        // In [error_message]: Subscription Transaction Failed: Error: then one of:
                        // There was an error processing your payment: Card has expired
                        // Your payment was declined for the following reason: Your card was declined.
                        // Your payment was declined for the following reason: Your card was declined. Note: Payment of $50.00 made on December 21, 2017, 7:30 am
                        // Your payment was declined for the following reason: Your card has insufficient funds.
                        // Your payment was declined for the following reason: Your card has insufficient funds. Note: Payment of $25.00 made on December 16, 2017, 7:30 am
                        // Your payment was declined for the following reason: Your card number is incorrect.
                        // Your payment was declined for the following reason: Your card number is incorrect. Note: Payment of $10.00 made on October 11, 2017, 7:32 am
                        // There was an unknown gateway error. Due to the nature of this error, you may want to contact us to ensure your card wasn't already charged and/or to process your order manually.

                        // This subscription has expired
                        jrCore_logger('MIN', "Subscribe: deleting expired subscription for @{$_us['profile_url']} for past due amount of: {$_sub['past_due_amount']}", $_sub);

                        // delete sub
                        jrSubscribe_delete_subscription($pid);

                    }
                    else {
                        jrCore_logger('MAJ', "Subscribe: email address received in subscription feed entry does not match a user in the system: {$_sub['customer_email']}", $_sub);
                    }
                }
            }
        }

        // Send notifications of soon to expire Credit Cards
        if (isset($_conf['jrSubscribe_expire_notify']) && $_conf['jrSubscribe_expire_notify'] == 'on') {
            if (isset($_queue['xml']['payment_methods_soon_to_expire']['customer']) && is_array($_queue['xml']['payment_methods_soon_to_expire']['customer'])) {

                // If we only have 1 entry it will come in a bit differently
                if (!isset($_queue['xml']['payment_methods_soon_to_expire']['customer'][0]) && isset($_queue['xml']['payment_methods_soon_to_expire']['customer']['customer_id'])) {
                    $_exp = array($_queue['xml']['payment_methods_soon_to_expire']['customer']);
                }
                else {
                    $_exp = $_queue['xml']['payment_methods_soon_to_expire']['customer'];
                }

                $config = jrPayment_get_plugin_config('foxycart');
                foreach ($_exp as $_ex) {

                    // [customer_id] => 22771266
                    // [customer_first_name] => <first name>
                    // [customer_last_name] => <last name>
                    // [customer_email] => <email>@<domain.com>
                    // [cc_exp_month] => 09
                    // [cc_exp_year] => 2017

                    // Make sure we are not PAST the expiration date
                    if (intval($_ex['cc_exp_year'] . $_ex['cc_exp_month']) < strftime('%Y%m')) {
                        // This expiration has already passed, so we already notified
                        continue;
                    }

                    if ($_us = jrCore_db_get_item_by_key('jrUser', 'user_email', $_ex['customer_email'])) {
                        // What plan is this profile on?
                        if ($_sb = jrSubscribe_get_subscription_by_profile_id($_us['_profile_id'])) {

                            // Do we already notify about this expiration?
                            if (jrSubscriber_get_subscription_note($_sb['sub_id'], "exp{$_ex['cc_exp_year']}{$_ex['cc_exp_month']}")) {
                                // We've already notified about this expiration
                                continue;
                            }

                            // Fall through - let the user know that their subscription payment method is about to expire
                            if ($_tmp = json_decode($_sb['sub_data'], true)) {
                                if (isset($_tmp['sub_token'])) {
                                    $_rp = array(
                                        '_subscription'     => $_sb,
                                        'card_expire_month' => $_ex['cc_exp_month'],
                                        'card_expire_year'  => $_ex['cc_exp_year'],
                                        'update_card_url'   => "https://{$config['store_sub_domain']}.foxycart.com/cart?sub_token={$_tmp['sub_token']}&cart=updateinfo"
                                    );
                                    jrSubscribe_notify_user($_us['_user_id'], $_sb['sub_plan_id'], 'subscription_payment_method_expiring', $_rp);
                                    jrCore_logger('INF', "Subscribe: notified {$_us['user_email']} about pending payment method expiration", $_rp);
                                }
                            }

                            // Save that we've notified on this expiration
                            jrSubscriber_save_subscription_note($_sb['sub_id'], "exp{$_ex['cc_exp_year']}{$_ex['cc_exp_month']}", 1);
                        }
                    }

                }

            }
        }
    }
    return true;
}

/**
 * Watch for a successful subscription webhook
 * @param $_txn array incoming transaction
 * @return mixed
 */
function jrSubscribe_plugin_foxycart_webhook_process($_txn)
{
    if (isset($_txn['txn_raw']) && is_array($_txn['txn_raw'])) {

        if (isset($_txn['txn_raw']['transactions']['transaction'])) {

            // Is this a subscription payment?
            $_raw = $_txn['txn_raw']['transactions']['transaction'];
            if (isset($_raw['custom_fields']['custom_field']) && is_array($_raw['custom_fields']['custom_field'])) {
                $_fl = array();
                foreach ($_raw['custom_fields']['custom_field'] as $n => $f) {
                    if (is_array($f) && isset($f['custom_field_name'])) {
                        $_fl["{$f['custom_field_name']}"] = $f['custom_field_value'];
                    }
                }

                // Get the current plan this profile_id is subscribed to, if any
                if (isset($_fl['profile_id']) && $_fl['profile_id'] > 0) {
                    $pid = (int) $_fl['profile_id'];
                    if ($_sub = jrSubscribe_get_profile_subscription($pid)) {
                        $_fl['plan_id'] = (int) $_sub['sub_plan_id'];
                    }
                }
                else {
                    jrCore_logger('CRI', "Subscribe: unable to determine profile_id or plan_id from FoxyCart subscription response", $_txn);
                    return $_txn;
                }

                // Did we get a plan id?
                if (empty($_fl['plan_id'])) {
                    // If we don't have a plan_id, let's see if we can figure it out based on the profile's
                    // existing quota_id and see if we can line that up with a subscription plan
                    if ($_pr = jrCore_db_get_item('jrProfile', $_fl['profile_id'])) {
                        $qid = (int) $_pr['profile_quota_id'];
                        if ($qid && $qid > 0) {
                            $_pl = array(
                                'search'        => array(
                                    "sub_quota_id = {$qid}"
                                ),
                                'skip_triggers' => true,
                                'limit'         => 1
                            );
                            // We've got the profile's quota ID - try to match up to plan
                            if (isset($_raw['transaction_details']['transaction_detail']['subscription_frequency'])) {
                                // We've found the frequency
                                $num = intval($_raw['transaction_details']['transaction_detail']['subscription_frequency']);
                                if ($num > 0) {
                                    $_pl['search'][] = "sub_duration = " . $num . ':' . str_replace($num, '', $_raw['transaction_details']['transaction_detail']['subscription_frequency']);
                                }
                            }
                            $_pl = jrCore_db_search_items('jrSubscribe', $_pl);
                            if ($_pl && is_array($_pl) && isset($_pl['_items'])) {
                                // We have found a subscription that matches
                                $_fl['plan_id'] = (int) $_pl['_items'][0]['_item_id'];
                            }
                        }
                    }
                }

                // We must have a valid plan_id
                if (!isset($_fl['plan_id']) || !jrCore_checktype($_fl['plan_id'], 'number_nz')) {

                    // We could not determine the plan this subscription came in for
                    jrCore_logger('CRI', "Subscribe: unable to determine plan_id from FoxyCart subscription response", $_txn);

                }
                else {

                    $start  = true;
                    $update = false;
                    $cancel = false;

                    // Check our transaction details and see if we are just updating to a new credit card
                    if (isset($_raw['transaction_details']['transaction_detail']) && isset($_raw['transaction_details']['transaction_detail'][1])) {
                        // This is a user just updating their Credit Card info
                        // NOTE: Subscription can ALSO be extended with the updated credit card!
                        $start  = false;
                        $update = true;
                    }

                    // We have an incoming subscription transaction - are we canceling?
                    if (!$update && $_raw['transaction_details']['transaction_detail']['subscription_enddate'] != '0000-00-00') {
                        // We are canceling a subscription
                        $start  = false;
                        $cancel = true;
                    }

                    // Start or Continue a subscription
                    if ($start) {

                        // We have a subscription - is the plan good?
                        $sid = (int) $_fl['plan_id'];
                        if ($_pl = jrCore_db_get_item('jrSubscribe', $sid)) {

                            // Plan is good - does this profile already have an existing active subscription?
                            $new = true;
                            $uid = (int) $_fl['user_id'];
                            $gid = $_txn['txn_id'];
                            $pid = (int) $_fl['profile_id'];
                            $sub = null;
                            if (isset($_raw['transaction_details']['transaction_detail']['sub_token_url'])) {
                                if (!$sub = jrSubscribe_plugin_foxycart_get_sub_token_from_url($_raw['transaction_details']['transaction_detail']['sub_token_url'])) {
                                    jrCore_logger('CRI', "Subscribe: unable to retrieve sub_token from FoxyCart subscription response", $_txn);
                                }
                            }
                            else {
                                jrCore_logger('CRI', "Subscribe: sub_token URL not found in FoxyCart subscription response", $_txn);
                            }
                            $prc = false;
                            $_sb = jrSubscribe_get_profile_subscription($pid);
                            if ($_sb && is_array($_sb)) {
                                if ($_sb['sub_plan_id'] != $sid) {
                                    // We already have a subscription for this profile - this means the
                                    // profile is CHANGING to a different subscription while the old is still active
                                    // We need to DELETE the old subscription, and start the new
                                    jrSubscribe_plugin_foxycart_delete_subscription($_sb);
                                }
                                $new = false;
                                $prc = $_sb['sub_amount'];
                            }
                            if (!$prc) {
                                // New or changed subscription - price is what the plan is at
                                $prc = jrPayment_price_to_cents($_pl['sub_item_price']);
                                if (isset($_pl['sub_variable']) && $_pl['sub_variable'] == 'on') {
                                    if (!empty($_raw['product_total'])) {
                                        $prc = jrPayment_price_to_cents($_raw['product_total']);
                                    }
                                    else {
                                        $prc = jrPayment_price_to_cents($_txn['txn_total']);
                                    }
                                }
                            }
                            if ($new) {
                                // If this is a VARIABLE priced subscription, skip price validation
                                $val = true;
                                if (isset($_pl['sub_variable']) && $_pl['sub_variable'] == 'on') {
                                    $val = false;
                                }
                                // Do we have a free trial with this plan?
                                if ($_txn['txn_total'] == 0) {
                                    // We have an incoming 0 payment - check for trial period
                                    if (isset($_pl['sub_trial']) && $_pl['sub_trial'] != 0) {
                                        // This plan has a TRIAL period - turn off price validation
                                        jrSubscribe_start_subscription($pid, $sid, $prc, $_pl['sub_trial'], false);
                                        jrSubscribe_set_subscription_state($pid, 'trial');
                                    }
                                    else {
                                        jrSubscribe_start_subscription($pid, $sid, $prc, null, $val);
                                    }
                                }
                                else {
                                    jrSubscribe_start_subscription($pid, $sid, $prc, null, $val);
                                }
                                jrSubscribe_save_sub_metadata_key($pid, 'sub_token', $sub);
                            }
                            else {
                                // This is an existing subscription with an incoming payment
                                $created = 0;
                                if (!empty($_raw['transaction_details']['transaction_detail']['subscription_startdate']) && $_raw['transaction_details']['transaction_detail']['subscription_startdate'] != '0000-00-00') {
                                    list($syear, $smonth, $sday) = explode('-', $_raw['transaction_details']['transaction_detail']['subscription_startdate']);
                                    $created = mktime(23, 23, 59, $smonth, $sday, $syear);
                                }
                                jrSubscribe_start_subscription($pid, $sid, $prc, null, false, false, true, true, $created);
                            }
                            $fee = 0;
                            if (isset($_txn['txn_gateway_fee'])) {
                                $fee = (int) $_txn['txn_gateway_fee'];
                            }
                            jrSubscribe_record_sale_in_register($_txn['txn_item_id'], $prc, $gid, $uid, $_pl, $sub, $fee);
                        }
                    }

                    elseif ($update) {
                        // User has UPDATED their Credit Card info at FoxyCart
                        $pid = (int) $_fl['profile_id'];
                        $uid = (int) $_fl['user_id'];
                        if ($_us = jrCore_db_get_item('jrUser', $uid)) {
                            jrCore_logger('INF', "Subscribe: {$_us['user_email']} (profile_id: {$pid}) has updated their credit card", $_txn);
                        }
                        // Did they also pay an outstanding balance?
                        if (isset($_txn['txn_total']) && $_txn['txn_total'] > 0) {
                            // Payment has been made with updated credit card
                            $sid = (int) $_fl['plan_id'];
                            if ($_pl = jrCore_db_get_item('jrSubscribe', $sid)) {
                                $prc = false;
                                $_sb = jrSubscribe_get_profile_subscription($pid);
                                if ($_sb && is_array($_sb)) {
                                    $prc = $_sb['sub_amount'];
                                }
                                if (!$prc) {
                                    // price is what the plan is at
                                    $prc = jrPayment_price_to_cents($_pl['sub_item_price']);
                                }
                                jrSubscribe_start_subscription($pid, $sid, $prc);
                            }
                        }
                    }

                    elseif ($cancel) {

                        // Get this profile's existing subscription
                        $pid = (int) $_fl['profile_id'];
                        if ($_pl = jrSubscribe_get_profile_subscription($pid)) {

                            // Do we have a sub token?
                            $tkn = false;
                            if (!empty($_pl['sub_data'])) {
                                if ($_sd = json_decode($_pl['sub_data'], true)) {
                                    if (!empty($_sd['sub_token'])) {
                                        $tkn = $_sd['sub_token'];
                                    }
                                }
                            }

                            // We have the subscription for the profile - if we can find the sub_token_url and they match, we can cancel
                            $sub = true;
                            if ($tkn) {
                                if (isset($_raw['transaction_details']['transaction_detail']['sub_token_url'])) {
                                    if (!$sub = jrSubscribe_plugin_foxycart_get_sub_token_from_url($_raw['transaction_details']['transaction_detail']['sub_token_url'])) {
                                        jrCore_logger('CRI', "Subscribe: unable to retrieve sub_token from FoxyCart cancel subscription response", $_txn);
                                    }
                                }
                                else {
                                    jrCore_logger('CRI', "Subscribe: sub_token URL not found in FoxyCart cancel subscription response", $_txn);
                                }
                                if ($sub != $tkn) {
                                    // This is not the right subscription
                                    jrCore_logger('CRI', "Subscribe: sub_token found in FoxyCart cancel subscription response does not match sub_token stored in subscription", array('_sub' => $_pl, '_txn' => $_txn));
                                    $sub = false;
                                }
                            }
                            if ($sub) {
                                if (jrSubscribe_cancel_subscription($pid)) {
                                    jrCore_logger('INF', "Subscribe: subscription for profile_id {$pid} has been canceled at end of term: " . jrCore_format_time($_pl['sub_expires']), $_txn);
                                }
                                else {
                                    jrCore_logger('CRI', "Subscribe: error canceling subscription for profile_id {$pid}", $_txn);
                                }
                            }
                        }
                        else {
                            jrCore_logger('CRI', "Subscribe: error canceling subscription for profile_id {$pid} - subscription not found", $_txn);
                        }
                    }

                }
            }
        }

    }
    return $_txn;
}

/**
 * Format transactions
 * @param array $_data transaction row
 * @param array $_args transaction data
 * @return mixed
 */
function jrSubscribe_plugin_foxycart_txn_entry($_data, $_args)
{
    if ($_args['txn_plugin'] == 'foxycart' && isset($_args['txn_raw']) && is_array($_args['txn_raw'])) {
        if (isset($_args['txn_raw']['transactions']['transaction'])) {
            $_raw = $_args['txn_raw']['transactions']['transaction'];
            if (isset($_raw['custom_fields']['custom_field']) && is_array($_raw['custom_fields']['custom_field'])) {
                $_fl = array();
                foreach ($_raw['custom_fields']['custom_field'] as $n => $f) {
                    if (is_array($f)) {
                        $_fl["{$f['custom_field_name']}"] = $f['custom_field_value'];
                    }
                }
                if (isset($_fl['plan_id'])) {
                    if (isset($_raw['transaction_details']['transaction_detail']) && isset($_raw['transaction_details']['transaction_detail'][1])) {
                        $_data[2]['title'] = 'foxycart<br><small>subscription.update</small>';
                    }
                    elseif ($_raw['transaction_details']['transaction_detail']['subscription_enddate'] != '0000-00-00') {
                        $_data[2]['title'] = 'foxycart<br><small>subscription.cancel</small>';
                    }
                    elseif ($_args['txn_total'] == 0) {
                        $_data[2]['title'] = 'foxycart<br><small>subscription.trial_start</small>';
                    }
                    else {
                        $_data[2]['title'] = 'foxycart<br><small>subscription.create</small>';
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * Watch for a successful subscription
 * @param $_txn array incoming transaction
 * @return mixed
 */
function jrSubscribe_plugin_foxycart_webhook_event($_txn)
{
    return $_txn;
}

/**
 * Get the sub_token from the subscription URL
 * @param string $url
 * @return bool|string
 */
function jrSubscribe_plugin_foxycart_get_sub_token_from_url($url)
{
    // [sub_token_url] => https://theartsherpa.foxycart.com/cart?sub_token=d51ff3dadc06e126fe8552d385508b68fb76883e81fc3c496bd80942b85a6247
    parse_str(parse_url($url, PHP_URL_QUERY), $_vars);
    if ($_vars && isset($_vars['sub_token'])) {
        return trim($_vars['sub_token']);
    }
    return false;
}

/**
 * Get subscription template with replacements
 * @param array $_rp
 * @return mixed
 */
function jrSubscribe_plugin_foxycart_get_subscription_template($_rp)
{
    $template = <<<EOT
<?xml version="1.0"?>
<transaction_template>
  <shipping_total><![CDATA[0]]></shipping_total>
  <custom_fields>
    <custom_field>
      <custom_field_name><![CDATA[plan_id]]></custom_field_name>
      <custom_field_value><![CDATA[%%_item_id%%]]></custom_field_value>
      <custom_field_is_hidden><![CDATA[1]]></custom_field_is_hidden>
    </custom_field>
    <custom_field>
      <custom_field_name><![CDATA[user_id]]></custom_field_name>
      <custom_field_value><![CDATA[1]]></custom_field_value>
      <custom_field_is_hidden><![CDATA[1]]></custom_field_is_hidden>
    </custom_field>
    <custom_field>
      <custom_field_name><![CDATA[profile_id]]></custom_field_name>
      <custom_field_value><![CDATA[1]]></custom_field_value>
      <custom_field_is_hidden><![CDATA[1]]></custom_field_is_hidden>
    </custom_field>
  </custom_fields>
  <discounts/>
  <transaction_details>
    <transaction_detail>
      <product_name><![CDATA[%%sub_title%%]]></product_name>
      <product_price><![CDATA[%%sub_item_price%%]]></product_price>
      <product_quantity><![CDATA[1]]></product_quantity>
      <quantity_min><![CDATA[0]]></quantity_min>
      <quantity_max><![CDATA[1]]></quantity_max>
      <product_weight><![CDATA[0.000]]></product_weight>
      <product_code><![CDATA[subscription:%%_item_id%%]]></product_code>
      <parent_code><![CDATA[]]></parent_code>
      <image><![CDATA[]]></image>
      <url><![CDATA[]]></url>
      <length><![CDATA[0]]></length>
      <width><![CDATA[0]]></width>
      <height><![CDATA[0]]></height>
      <expires><![CDATA[0]]></expires>
      <shipto><![CDATA[]]></shipto>
      <category_code><![CDATA[DEFAULT]]></category_code>
      <transaction_detail_options/>
    </transaction_detail>
  </transaction_details>
</transaction_template>
EOT;

    foreach ($_rp as $k => $v) {
        $new       = "%%{$k}%%";
        $_rp[$new] = $v;
        unset($_rp[$k]);
    }
    return str_replace(array_keys($_rp), $_rp, $template);
}
