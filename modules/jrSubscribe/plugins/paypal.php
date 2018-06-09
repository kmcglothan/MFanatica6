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
function jrSubscribe_plugin_paypal_metadata()
{
    return array(
        'delete_api_support' => 0,
        'expire_notify'      => 0,
        'prorate_sub_change' => 0
    );
}

/**
 * URL to create a subscription
 * @param $_sub array subscription info
 * @param string $action
 * @return string
 */
function jrSubscribe_plugin_paypal_build_subscription_url($_sub, $action = 'create')
{
    global $_user, $_conf;
    $config = jrPayment_get_plugin_config('paypal');
    $cur    = 'USD';
    if (isset($config['currency']{1})) {
        $cur = $config['currency'];
    }
    $srl = jrCore_get_module_url('jrSubscribe');
    $url = jrCore_get_module_url('jrPayment');

    list($number, $interval) = explode(':', $_sub['sub_duration']);
    $_pr = array(
        'business'      => urlencode($config['email']),
        'item_name'     => urlencode($_sub['sub_title']),
        'currency_code' => $cur,
        'custom'        => urlencode("{$_sub['_item_id']}:{$_user['user_active_profile_id']}:{$_user['_user_id']}"),
        'src'           => 1,
        'sra'           => 1,
        'a3'            => $_sub['sub_item_price'],
        'p3'            => intval($number),
        't3'            => strtoupper($interval),
        'return'        => urlencode("{$_conf['jrCore_base_url']}/{$srl}/success"),
        'notify_url'    => urlencode("{$_conf['jrCore_base_url']}/{$url}/webhook/paypal")
    );

    // Does this subscription have a trial period?
    if ($action == 'create' && isset($_sub['sub_trial']) && $_sub['sub_trial'] != '0') {
        if (!jrSubscribe_profile_used_trial($_user['user_active_profile_id'], $_sub['_item_id'])) {
            list($number, $interval) = explode(':', $_sub['sub_trial']);
            $_pr['a1'] = 0;
            $_pr['p1'] = intval($number);
            $_pr['t1'] = strtoupper($interval);
        }
    }
    $url = 'www.sandbox.paypal.com';
    if (isset($config['live']) && $config['live'] == 'on') {
        $url = 'www.paypal.com';
    }
    $url = "https://{$url}/cgi-bin/webscr?cmd=_xclick-subscriptions";
    foreach ($_pr as $k => $v) {
        $url .= "&{$k}={$v}";
    }
    return $url;
}

/**
 * Process subscription checkout
 * @param $_post array posted params
 * @param $_user array current User
 * @param $_conf array Global Conf
 */
function jrSubscribe_plugin_view_paypal_build_subscription($_post, $_user, $_conf)
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
    if ($url = jrSubscribe_plugin_paypal_build_subscription_url($_pl, 'create')) {
        jrCore_set_cookie('jr_sub_action', 'create', 1);
        jrCore_location($url);
    }
    jrCore_set_form_notice('error', 'error building subscription URL - please try again');
    jrCore_location('referrer');
}

/**
 * URL to modify an existing subscription
 * @note This function is only called when the user DOWNGRADES to a cheaper subscription
 * @param array $_new New plan info
 * @param array $_old Old plan info
 * @param array $_sub current subscription info
 * @return string
 */
function jrSubscribe_plugin_paypal_subscribe_change_url($_new, $_old, $_sub)
{
    global $_user, $_conf;
    $config = jrPayment_get_plugin_config('paypal');
    $cur    = 'USD';
    if (isset($config['currency']{1})) {
        $cur = $config['currency'];
    }
    $srl = jrCore_get_module_url('jrSubscribe');
    $url = jrCore_get_module_url('jrPayment');

    list($number, $interval) = explode(':', $_new['sub_duration']);
    $_pr = array(
        'business'      => urlencode($config['email']),
        'item_name'     => urlencode($_new['sub_title']),
        'currency_code' => $cur,
        'custom'        => urlencode("{$_new['_item_id']}:{$_user['user_active_profile_id']}:{$_user['_user_id']}"),
        'modify'        => 2,
        'src'           => 1,
        'sra'           => 1,
        'a3'            => $_new['sub_item_price'],
        'p3'            => intval($number),
        't3'            => strtoupper($interval),
        'return'        => urlencode("{$_conf['jrCore_base_url']}/{$srl}/success"),
        'notify_url'    => urlencode("{$_conf['jrCore_base_url']}/{$url}/webhook/paypal")
    );

    // We have to extend the trial to account for credit
    if ($_sub['sub_status'] == 'active') {
        $days = jrSubscribe_get_subscription_credit_days($_new, $_old, $_sub['sub_expires']);
        if ($days > 0) {
            $_pr['a1'] = 0;
            $_pr['p1'] = $days;
            $_pr['t1'] = 'D';
        }
    }

    $url = 'www.sandbox.paypal.com';
    if (isset($config['live']) && $config['live'] == 'on') {
        $url = 'www.paypal.com';
    }
    $url = "https://{$url}/cgi-bin/webscr?cmd=_xclick-subscriptions";
    foreach ($_pr as $k => $v) {
        $url .= "&{$k}={$v}";
    }
    return $url;
}

/**
 * Create a NEW subscription
 * @param $_sub array subscription info
 * @param string $action
 * @return mixed
 */
function jrSubscribe_plugin_paypal_subscribe_onclick($_sub, $action = 'create')
{
    global $_conf;
    $config = jrPayment_get_plugin_config('paypal');
    if (isset($config['account_id']) && strlen($config['account_id']) > 0) {

        // Note: If we have a variable priced subscription we have to redirect to build URL
        if (isset($_sub['sub_variable']) && $_sub['sub_variable'] == 'on') {
            $murl = jrCore_get_module_url('jrSubscribe');
            return "var p=jrSubscribe_get_sub_price({$_sub['_item_id']});jrCore_window_location('{$_conf['jrCore_base_url']}/{$murl}/plugin_view/paypal/build_subscription/id={$_sub['_item_id']}/p=' + p)";
        }

        // We can construct URL based on plan
        $url = jrSubscribe_plugin_paypal_build_subscription_url($_sub, $action);
        return "jrSubscribe_set_cookie('create');jrCore_window_location('{$url}')";
    }
    return false;
}

/**
 * URL to cancel a subscription
 * @param $_sub array subscription info
 * @return mixed
 */
function jrSubscribe_plugin_paypal_subscribe_cancel_onclick($_sub)
{
    $config = jrPayment_get_plugin_config('paypal');
    if (isset($config['account_id']) && strlen($config['account_id']) > 0) {
        $_pr = array(
            'alias' => $config['account_id']
        );
        $url = 'www.sandbox.paypal.com';
        if (isset($config['live']) && $config['live'] == 'on') {
            $url = 'www.paypal.com';
        }
        $url = "https://{$url}/cgi-bin/webscr?cmd=_subscr-find";
        foreach ($_pr as $k => $v) {
            $url .= "&{$k}={$v}";
        }
        return "jrCore_window_location('{$url}')";
    }
    return false;
}

/**
 * Update a Credit Card with new one
 * @param $_sub array subscription info
 * @return bool
 */
function jrSubscribe_plugin_paypal_subscribe_update_onclick($_sub)
{
    return jrSubscribe_plugin_paypal_subscribe_cancel_onclick($_sub);
}

/**
 * onclick to immediately delete a subscription
 * @param $_sub array subscription info
 * @return mixed
 */
function jrSubscribe_plugin_paypal_subscribe_delete_onclick($_sub)
{
    $config = jrPayment_get_plugin_config('paypal');
    if ($_md = jrSubscribe_get_sub_meta_data($_sub['sub_profile_id'], $_sub)) {
        $url = 'www.sandbox.paypal.com';
        if (isset($config['live']) && $config['live'] == 'on') {
            $url = 'www.paypal.com';
        }
        $url = "https://{$url}/cgi-bin/webscr?cmd=_profile-recurring-payments";
        $_pr = array(
            'encrypted_profile_id' => $_md['subscr_id'],
            'mp_id'                => $_md['subscr_id']
        );
        foreach ($_pr as $k => $v) {
            $url .= "&{$k}={$v}";
        }
        return "jrCore_window_location('{$url}')";
    }
    return false;
}

/**
 * Check for incoming subscription transactions
 * @param $_txn array incoming transaction
 * @return mixed
 */
function jrSubscribe_plugin_paypal_webhook_parse($_txn)
{
    if (isset($_txn['txn_raw'])) {

        $_raw = $_txn['txn_raw'];
        switch ($_raw['txn_type']) {

            // These are "informational" transactions - make sure there is no price
            case 'subscr_cancel':
            case 'subscr_failed':
            case 'subscr_eot':
            case 'recurring_payment':
            case 'recurring_payment_expired':
            case 'recurring_payment_failed':
            case 'recurring_payment_profile_cancel':
            case 'recurring_payment_profile_created':
            case 'recurring_payment_skipped':
            case 'recurring_payment_suspended':
            case 'recurring_payment_suspended_due_to_max_failed_payment':
                $_txn['txn_type'] = 'information';
                unset($_txn['txn_total'], $_txn['txn_shipping'], $_txn['txn_tax']);
                break;

            case 'subscr_modify':
                // We will get a modify IPN when the user changes to a NEW subscription
                $_txn['txn_type'] = 'information';
                unset($_txn['txn_total'], $_txn['txn_shipping'], $_txn['txn_tax']);
                break;

            // If we get a subscription signup with a TRIAL, we will not get a
            // payment event so we need to log that here
            case 'subscr_signup':
                if (isset($_raw['period1']) && $_raw['period1'] != 0) {
                    // We have a signup to a subscription with a trial
                    $_txn['txn_type'] = 'subscription';
                    // Break out id's
                    if (isset($_raw['custom']) && strpos($_raw['custom'], ':')) {
                        // custom = plan_id:profile_id:user_id
                        list(, , $user_id) = explode(':', $_raw['custom'], 3);
                        if ($user_id && jrCore_checktype($user_id, 'number_nz')) {
                            $_txn['txn_user_id']    = (int) $user_id;
                            $_txn['txn_user_email'] = $_raw['payer_email'];
                            $_txn['txn_total']      = $_raw['mc_amount1'];
                        }
                    }
                }
                else {
                    // This is only informational
                    $_txn['txn_type'] = 'information';
                    unset($_txn['txn_total'], $_txn['txn_shipping'], $_txn['txn_tax']);
                }
                break;

            // our actual subscription
            case 'subscr_payment':
                $_txn['txn_type'] = 'subscription';
                // Break out id's
                if (isset($_raw['custom']) && strpos($_raw['custom'], ':')) {
                    // custom = plan_id:profile_id:user_id
                    list(, , $user_id) = explode(':', $_raw['custom'], 3);
                    if ($user_id && jrCore_checktype($user_id, 'number_nz')) {
                        $_txn['txn_user_id']    = (int) $user_id;
                        $_txn['txn_user_email'] = $_raw['payer_email'];
                    }
                }
                break;

            default:
                // Check for refunded subscription
                if (isset($_raw['subscr_id']) && strlen($_raw['subscr_id']) > 0 && isset($_raw['payment_status']) && strtolower($_raw['payment_status']) == 'refunded') {
                    // We have a REFUNDED subscription
                    if (isset($_raw['custom']) && strpos($_raw['custom'], ':')) {
                        // custom = plan_id:profile_id:user_id
                        list(, , $user_id) = explode(':', $_raw['custom'], 3);
                        if ($user_id && jrCore_checktype($user_id, 'number_nz')) {
                            $_txn['txn_user_id']    = (int) $user_id;
                            $_txn['txn_user_email'] = $_raw['payer_email'];
                        }
                    }
                }
                break;
        }

    }
    return $_txn;
}

/**
 * Watch for a successful subscription IPN
 * @see https://www.paypal.com/us/cgi-bin/webscr?cmd=p/acc/ipn-subscriptions-outside
 * @param $_txn array incoming transaction
 * @return mixed
 */
function jrSubscribe_plugin_paypal_webhook_process($_txn)
{
    // Get our raw incoming transaction
    if ($_raw = $_txn['txn_raw']) {
        if (!isset($_raw['txn_type'])) {
            $_raw['txn_type'] = 'instant';
        }
        switch ($_raw['txn_type']) {

            // Signup to NEW subscription - we only handle this here if we have a free trial
            case 'subscr_signup':
                if (isset($_raw['period1']) && $_raw['period1'] != 0) {
                    // Get custom value (contains subscription Plan ID and Profile ID)
                    if (isset($_raw['custom']) && strpos($_raw['custom'], ':')) {
                        // custom = plan_id:profile_id:user_id
                        list($sid, $pid, $uid) = explode(':', $_raw['custom'], 3);
                        if ($_pl = jrCore_db_get_item('jrSubscribe', $sid)) {

                            // Plan is good - start subscription
                            // If this is a VARIABLE priced subscription, make sure we use the right price
                            $prc = jrPayment_price_to_cents($_pl['sub_item_price']);
                            if (isset($_pl['sub_variable']) && $_pl['sub_variable'] == 'on') {
                                $prc = jrPayment_price_to_cents($_raw['mc_gross']);
                            }

                            jrSubscribe_start_subscription($pid, $sid, $prc, $_pl['sub_trial'], false);
                            jrSubscribe_set_subscription_state($pid, 'trial');
                            jrSubscribe_record_sale_in_register($_txn['txn_item_id'], $_raw['mc_amount1'], $_txn['txn_id'], $uid, $_pl, $_raw['subscr_id'], $_txn['txn_gateway_fee']);

                            // Save our subscription ID
                            jrSubscribe_save_sub_metadata_key($pid, 'subscr_id', $_raw['subscr_id']);

                        }
                        else {
                            jrCore_logger('CRI', "Subscribe: plan_id received in paypal webhook does not exist", array('_txn' => $_txn));
                        }
                    }
                }
                break;

            // Subscription payment has been made
            case 'subscr_payment':
                // Get custom value (contains subscription Plan ID and Profile ID)
                if (isset($_raw['custom']) && strpos($_raw['custom'], ':')) {
                    // custom = plan_id:profile_id:user_id
                    list($sid, $pid, $uid) = explode(':', $_raw['custom'], 3);
                    if ($_pl = jrCore_db_get_item('jrSubscribe', $sid)) {

                        // Plan is good - start subscription
                        // If this is a VARIABLE priced subscription, skip price validation
                        $val = true;
                        $prc = jrPayment_price_to_cents($_pl['sub_item_price']);
                        if (isset($_pl['sub_variable']) && $_pl['sub_variable'] == 'on') {
                            $val = false;
                            $prc = jrPayment_price_to_cents($_raw['mc_gross']);
                        }

                        jrSubscribe_start_subscription($pid, $sid, $prc, null, $val);
                        jrSubscribe_record_sale_in_register($_txn['txn_item_id'], $_raw['mc_gross'], $_txn['txn_id'], $uid, $_pl, $_raw['subscr_id'], $_txn['txn_gateway_fee']);

                        // Save our subscription ID
                        jrSubscribe_save_sub_metadata_key($pid, 'subscr_id', $_raw['subscr_id']);

                    }
                    else {
                        jrCore_logger('CRI', "Subscribe: plan_id received in paypal webhook does not exist", array('_txn' => $_txn));
                    }
                }
                break;

            // Subscription is being modified
            // NOTE: This allows for price change to existing subscription
            case 'subscr_modify':
                // Get custom value (contains subscription Plan ID and Profile ID)
                if (isset($_raw['custom']) && strpos($_raw['custom'], ':')) {
                    // custom = plan_id:profile_id:user_id
                    list($sid, ,) = explode(':', $_raw['custom'], 3);
                    // Update subscription price
                    if (isset($_raw['mc_amount3']) && strlen($_raw['mc_amount3']) > 0) {
                        $amount = jrPayment_price_to_cents($_raw['mc_amount3']);
                        jrSubscribe_update_subscription_field($sid, 'sub_amount', $amount);
                    }
                }
                break;

            // Subscription is being canceled
            case 'subscr_cancel':
                // Get custom value (contains subscription Plan ID and Profile ID)
                if (isset($_raw['custom']) && strpos($_raw['custom'], ':')) {
                    // custom = plan_id:profile_id:user_id
                    list(, $pid,) = explode(':', $_raw['custom'], 3);
                    // Cancel subscription
                    jrSubscribe_cancel_subscription($pid);
                }
                break;

            case 'recurring_payment_suspended':
                // We have PAYMENT for a subscription being suspended - this does
                // not mean we CHANGE the subscription, we just want to note this
                // so the site owner knows they are not collecting payment on it
                if (isset($_raw['custom']) && strpos($_raw['custom'], ':')) {
                    // custom = plan_id:profile_id:user_id
                    list(, $pid,) = explode(':', $_raw['custom'], 3);
                    if ($pid && jrCore_checktype($pid, 'number_nz')) {
                        jrSubscribe_set_subscription_state($pid, 'unpaid');
                    }
                }
                break;

            case 'subscr_eot':
            case 'recurring_payment_suspended_due_to_max_failed_payment':
                // EOT or max failed payments reached - delete subscription
                if (isset($_raw['custom']) && strpos($_raw['custom'], ':')) {
                    // custom = plan_id:profile_id:user_id
                    list(, $pid,) = explode(':', $_raw['custom'], 3);
                    // Delete subscription
                    jrSubscribe_delete_subscription($pid);
                    jrCore_logger('INF', "Subscribe: subscription for profile_id {$pid} has been canceled due to payment failure", $_raw);
                }
                break;

            case 'subscr_failed':
                // A subscription payment has failed - let user know
                if (isset($_raw['custom']) && strpos($_raw['custom'], ':')) {
                    // custom = plan_id:profile_id:user_id
                    list($sid, $pid, $uid) = explode(':', $_raw['custom'], 3);
                    jrSubscribe_set_subscription_state($pid, 'unpaid');
                    jrSubscribe_notify_user($uid, $sid, 'subscription_past_due');
                }
                break;

            default:
                // Check for refunded subscription
                if (isset($_raw['subscr_id']) && strlen($_raw['subscr_id']) > 0 && isset($_raw['payment_status']) && strtolower($_raw['payment_status']) == 'refunded') {
                    // custom = plan_id:profile_id:user_id
                    list(, $pid,) = explode(':', $_raw['custom'], 3);
                    // Get subscription
                    if ($_sub = jrSubscribe_get_profile_subscription($pid)) {

                        // Find and refund register entry
                        if ($_re = jrPayment_get_all_register_entries_with_tag($_raw['subscr_id'])) {
                            // We've got a list of entries - find the one that was refunded, and mark it refunded
                            foreach ($_re as $r) {
                                if (isset($_raw['parent_txn_id']) && $r['r_gateway_id'] == $_raw['parent_txn_id']) {
                                    // This is the one
                                    jrPayment_refund_item_by_id($r['r_id']);
                                    break;
                                }
                            }
                        }

                    }
                }
                break;

        }
    }
    return $_txn;
}
