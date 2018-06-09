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
 * @copyright 2003 - 2016 Talldude Networks, LLC.
 */
// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Plugin Meta
 * @param $_post array $_post
 * @return array
 */
function jrPayment_plugin_paypal_meta($_post)
{
    return array(
        'title'       => 'PayPal',
        'description' => 'PayPal is a free to signup payment processor that charges 2.9% + 0.30 per transaction.',
        'url'         => 'https://paypal.com',
        'admin'       => 'https://www.paypal.com/signin'
    );
}

/**
 * Plugin Config
 * @param $_post array $_post
 * @return bool
 */
function jrPayment_plugin_paypal_config($_post)
{
    global $_conf;
    jrCore_page_notice('success', "Set the IPN Notification URL to: <b>{$_conf['jrCore_base_url']}/{$_post['module_url']}/webhook/paypal</b><br>in your PayPal -> Profile -> Profile and Settings -> My Selling Tools -> Instant Payment Notification section.", false);

    // Live / Test
    $_tmp = array(
        'name'     => 'live',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'label'    => 'live transactions',
        'help'     => 'Check this option to enable live system transactions. Unchecked will use the PayPal sandbox for testing'
    );
    jrCore_form_field_create($_tmp);

    // PayPal email
    $_tmp = array(
        'name'     => 'email',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'email',
        'label'    => 'paypal email address',
        'help'     => 'Enter the email address for your PayPal account'
    );
    jrCore_form_field_create($_tmp);

    // Merchant Account ID
    $_tmp = array(
        'name'     => 'account_id',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'paypal merchant ID',
        'help'     => 'Enter your PayPal Merchant ID that can be found in the Profile -> My Business Info settings once logged in to PayPal',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Store Currency
    $_cur = array(
        'AUD' => 'AUD - Australian Dollar',
        'BRL' => 'BRL - Brazilian Real',
        'CAD' => 'CAD - Canadian Dollar',
        'CZK' => 'CZK - Czech Koruna',
        'DKK' => 'DKK - Danish Krone',
        'EUR' => 'EUR - Euro',
        'HKD' => 'HKD - Hong Kong Dollar',
        'ILS' => 'ILS - Israeli New Sheqel',
        'MXN' => 'MXN - Mexican Peso',
        'NOK' => 'NOK - Norwegian Krone',
        'NZD' => 'NZD - New Zealand Dollar',
        'PHP' => 'PHP - Philippine Peso',
        'PLN' => 'PLN - Polish Zloty',
        'GBP' => 'GBP - Pound Sterling',
        'RUB' => 'RUB - Russian Ruble',
        'SGD' => 'SGD - Singapore Dollar',
        'SEK' => 'SEK - Swedish Krona',
        'CHF' => 'CHF - Swiss Franc',
        'THB' => 'THB - Thai Baht',
        'USD' => 'USD - U.S. Dollar'
    );
    natcasesort($_cur);
    $_tmp = array(
        'name'     => 'currency',
        'type'     => 'select',
        'options'  => $_cur,
        'default'  => 'USD',
        'validate' => 'core_string',
        'label'    => 'payment currency',
        'help'     => 'Select the currency you want to use on the site'
    );
    jrCore_form_field_create($_tmp);
    return true;
}

/**
 * Support for additional detail buttons
 * @param array $_txn
 * @param array $_entries
 * @return mixed
 */
function jrPayment_plugin_paypal_txn_detail_buttons($_txn, $_entries)
{
    if ($_txn && is_array($_txn) && $_txn['txn_type'] != 'information' && isset($_txn['txn_id']) && is_array($_entries) && count($_entries) > 0) {
        // Have we refunded this transaction?
        $title = 'refund';
        foreach ($_entries as $_e) {
            if (isset($_e['r_refunded_time']) && $_e['r_refunded_time'] > 0) {
                $title = 'REFUNDED';
                break;
            }
        }
        $config = jrPayment_get_plugin_config('paypal');
        $url    = 'www.sandbox.paypal.com';
        if (isset($config['live']) && $config['live'] == 'on') {
            $url = 'www.paypal.com';
        }
        return array(
            jrCore_page_button('refund', $title, "window.open('https://{$url}/activity/payment/{$_txn['txn_id']}')")
        );
    }
    return false;
}

/**
 * Transaction view URL
 * @param array $_txn
 * @return string
 */
function jrPayment_plugin_paypal_get_transaction_url($_txn)
{
    $config = jrPayment_get_plugin_config('paypal');
    $url    = 'www.sandbox.paypal.com';
    if (isset($config['live']) && $config['live'] == 'on') {
        $url = 'www.paypal.com';
    }
    return (strlen($_txn['txn_id']) >= 16) ? "https://{$url}/activity/payment/{$_txn['txn_id']}" : false;
}

/**
 * Checkout onclick handler
 * @param $cart_hash string MD5 cart hash
 * @param $total int Total cart amount (in cents)
 * @param $_cart array Cart contents
 * @return string
 */
function jrPayment_plugin_paypal_checkout_onclick($cart_hash, $total, $_cart)
{
    global $_conf, $_post;
    return "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/checkout/{$cart_hash}/paypal')";
}

/**
 * Get our checkout URL
 * @param $amount int Total amount in cents
 * @param $_cart array cart items
 * @return string
 */
function jrPayment_plugin_paypal_checkout_url($amount, $_cart)
{
    global $_conf;
    $config = jrPayment_get_plugin_config('paypal');
    $cur    = 'USD';
    if (isset($config['currency']{1})) {
        $cur = $config['currency'];
    }
    $url    = jrCore_get_module_url('jrPayment');
    $_ln    = jrUser_load_lang_strings();
    $amount = ($amount / 100);
    $hashid = "cart:{$_cart['cart_id']}:{$_cart['cart_hash']}";

    $_pr = array(
        'business'      => urlencode($config['email']),
        'item_name'     => urlencode($_ln['jrPayment'][14]),
        'custom'        => $hashid,
        'amount'        => urlencode($amount),
        'currency_code' => $cur,
        'return'        => urlencode("{$_conf['jrCore_base_url']}/{$url}/success/cart"),
        'notify_url'    => urlencode("{$_conf['jrCore_base_url']}/{$url}/webhook/paypal"),
        'no_note'       => '0'
    );
    $url = 'www.sandbox.paypal.com';
    if (isset($config['live']) && $config['live'] == 'on') {
        $url = 'www.paypal.com';
    }
    // https://www.sandbox.paypal.com/cgi-bin/webscr
    $url = "https://{$url}/cgi-bin/webscr?cmd=_xclick";
    foreach ($_pr as $k => $v) {
        $url .= "&{$k}={$v}";
    }
    return $url;
}

/**
 * Get formatted currency
 * @param $amount
 * @return float
 */
function jrPayment_plugin_paypal_currency_format($amount)
{
    return jrCore_number_format($amount / 100, 2);
}

/**
 * Get active currency
 * @return string
 */
function jrPayment_plugin_paypal_get_currency_code()
{
    $config = jrPayment_get_plugin_config('paypal');
    return $config['currency'];
}

/**
 * Parse incoming transaction
 * @param $_post array
 * @return mixed
 */
function jrPayment_plugin_paypal_webhook_parse($_post)
{
    // Validate incoming transaction
    $config = jrPayment_get_plugin_config('paypal');
    $url    = 'www.sandbox.paypal.com';
    if (isset($config['live']) && $config['live'] == 'on') {
        $url = 'www.paypal.com';
    }
    $url = "https://{$url}/cgi-bin/webscr?cmd=_notify-validate";
    foreach ($_post as $k => $v) {
        switch ($k) {
            case '_uri':
            case 'module':
            case 'module_url':
            case 'option':
                continue 2;
                break;
        }
        $url .= "&{$k}=" . urlencode($v);
    }
    $tmp = jrCore_load_url($url, null, 'GET', 443);
    if (!$tmp || !strstr($tmp, 'VERIFIED')) {
        // We are not a valid IPN
        jrCore_logger('CRI', "Payments: invalid paypal notification received in webhook - notify-validate failed", $_post);
        header('HTTP/1.0 200 OK');
        exit;
    }

    // Transaction Info
    $_dt = array();
    if (isset($_post['txn_id'])) {
        $_dt['txn_id'] = $_post['txn_id'];
    }
    elseif (isset($_post['ipn_track_id'])) {
        $_dt['txn_id'] = $_post['ipn_track_id'];
    }

    // Is this a REFUND transaction?
    $_dt['txn_type']   = 'payment';
    $_dt['txn_status'] = 'active';
    if (isset($_post['payment_status'])) {

        switch (strtolower($_post['payment_status'])) {
            case 'refunded':
                $_dt['txn_type'] = 'refund';
                break;
            case 'pending':
                // A pending eCheck or bank transfer needs to be marked as INFORMATIONAL
                $_dt['txn_status'] = 'pending';
                break;
        }
    }

    if (isset($_post['payment_date'])) {
        $_dt['txn_date'] = strtotime($_post['payment_date']);
    }
    elseif (isset($_post['subscr_date'])) {
        $_dt['txn_date'] = strtotime($_post['subscr_date']);
    }
    else {
        $_dt['txn_date'] = time();
    }
    $_dt['txn_tax'] = 0;
    if (isset($_post['tax'])) {
        $_dt['txn_tax'] = jrPayment_price_to_cents($_post['tax']);
    }
    $_dt['txn_shipping'] = 0;
    if (isset($_post['shipping'])) {
        $_dt['txn_shipping'] = jrPayment_price_to_cents($_post['shipping']);
    }

    if (isset($_post['mc_gross'])) {
        $_dt['txn_total'] = jrPayment_price_to_cents($_post['mc_gross']);
    }
    elseif (isset($_post['mc_amount3'])) {
        $_dt['txn_total'] = jrPayment_price_to_cents($_post['mc_amount3']);
    }
    elseif (isset($_post['payment_gross'])) {
        $_dt['txn_total'] = jrPayment_price_to_cents($_post['payment_gross']);
    }

    // Gateway processing fee
    $_dt['txn_gateway_fee'] = 0;
    if (isset($_post['mc_fee'])) {
        $_dt['txn_gateway_fee'] = jrPayment_price_to_cents($_post['mc_fee']);
    }
    if (isset($_post['payer_email'])) {
        $_dt['txn_user_email'] = trim($_post['payer_email']);
    }

    // Do we have a cart transaction?
    if (isset($_post['custom']) && strpos($_post['custom'], 'cart:') === 0) {
        list(, $cart_id, $cart_hash) = explode(':', $_post['custom'], 3);
        $_dt['txn_cart_id']   = trim($cart_id);
        $_dt['txn_cart_hash'] = trim($cart_hash);
    }

    // Extra Fields
    $_fields = array(
        'address_name'    => 'txn_address_line1',
        'address_street'  => 'txn_address_line2',
        'address_city'    => 'txn_address_city',
        'address_state'   => 'txn_address_state',
        'address_country' => 'txn_address_country',
        'address_zip'     => 'txn_address_zip'
    );
    foreach ($_fields as $val => $key) {
        if (isset($_post[$val]) && strlen($_post[$val]) > 0) {
            $_dt[$key] = $_post[$val];
        }
    }
    asort($_post);
    $_dt['txn_raw'] = $_post;
    return $_dt;
}

/**
 * Process incoming transaction
 * @param $_txn array Transaction
 * @return array
 */
function jrPayment_plugin_paypal_webhook_process($_txn)
{
    // Get our raw incoming transaction
    if ($_raw = $_txn['txn_raw']) {

        // Do we have a PENDING payment?
        if (isset($_raw['payment_status'])) {

            switch (strtolower($_raw['payment_status'])) {

                case 'completed':

                    // Invoice Payment
                    // [txn_type] => invoice_payment
                    switch ($_raw['txn_type']) {

                        case 'send_money':
                        case 'invoice_payment':
                        // Is this a payment received at the business email?
                        // [receiver_email] => email@example.com
                        // [business] => email@example.com
                        $email = false;
                        if (!empty($_raw['receiver_email'])) {
                            $email = $_raw['receiver_email'];
                        }
                        elseif (!empty($_raw['business'])) {
                            $email = $_raw['business'];
                        }
                        if ($email && jrCore_checktype($email, 'email')) {
                            if ($config = jrPayment_get_plugin_config('paypal')) {
                                if ($email == $config['email']) {
                                    // We have in incoming Invoice being paid to the business
                                    // Was the invoice paid by someone with an account on the system?
                                    $uid = 0;
                                    if ($_us = jrCore_db_get_item_by_key('jrUser', 'user_email', $_txn['txn_user_email'])) {
                                        $uid = (int) $_us['_user_id'];
                                    }
                                    $_raw['_item_id']      = 0; // no item
                                    $_raw['_profile_id']   = 0; // No profile is seller
                                    $_raw['cart_expense']  = 0;
                                    $_raw['cart_amount']   = $_txn['txn_total'];
                                    $_raw['cart_shipping'] = $_txn['txn_shipping'];
                                    $_raw['cart_quantity'] = 1;
                                    $_raw['cart_module']   = 'jrPayment';
                                    $_raw['cart_field']    = 'invoice';
                                    jrPayment_record_sale_in_register($_txn['txn_item_id'], $_txn['txn_id'], $uid, $_raw, str_replace('_' , ' ', $_raw['txn_type']), $_txn['txn_gateway_fee'], $_txn['txn_tax']);
                                }
                            }
                        }
                        break;

                    }
                    break;

                case 'pending':

                    // This is an eCheck or bank transfer
                    break;

                case 'refunded':

                    // Check for refunded cart items
                    if (isset($_raw['custom']) && strpos(' ' . $_raw['custom'], 'cart:')) {

                        // We had a refund - remove the items from the user's access
                        $tid = $_raw['parent_txn_id'];
                        if ($_rt = jrPayment_get_register_entries_by_gateway_id($tid)) {

                            jrCore_logger('MAJ', "Payments: paypal refund transaction received for txn_id: {$tid}", $_raw);

                            // How much did we originally pay for these items?
                            $total = 0;
                            foreach ($_rt as $_e) {
                                if ($_e['r_refunded_time'] > 0) {
                                    // already refunded
                                    continue;
                                }
                                $total += $_e['r_amount'];
                            }

                            // Is the amount being refunded DIFFERENT than the payment?  If so, we probably
                            // have a partial refund - see if we can figure out WHAT to refund
                            if ($_txn['txn_total'] != $total) {
                                // We have a difference - see if we can find a specific cart entry that
                                // matches our total refunded, and if it is the ONLY match, refund it
                                $_found = array();
                                foreach ($_rt as $_e) {
                                    if ($_e['r_refunded_time'] > 0) {
                                        // already refunded
                                        continue;
                                    }
                                    if ($_e['r_amount'] == $_txn['txn_total']) {
                                        $_found[] = $_e;
                                    }
                                }
                                if (count($_found) === 1) {
                                    // We found the single transaction that was refunded - process
                                    jrPayment_refund_item_by_id($_found[0]['r_id']);
                                }
                                else {
                                    // We cannot know WHAT was refunded - must be manual
                                    $_found = (count($_found) > 0) ? $_found : 'no matches';
                                    jrCore_logger('CRI', "Payments: unable to determine refunded item - manual refund required", array('possible matches' => $_found, '_raw' => $_raw));
                                }
                            }
                            else {
                                // The entire purchase has been refunded - refund EACH item in the transaction
                                foreach ($_rt as $_e) {
                                    jrPayment_refund_item_by_id($_e['r_id']);
                                }
                            }
                        }
                        else {
                            jrCore_logger('CRI', "Payments: unable to find register entries for refund txn_id: {$tid}", $_raw);
                        }
                        // Remove the cart id so there is no attempt to process the cart
                        unset($_txn['txn_cart_id']);
                    }
                    break;
            }
        }
    }
    return $_txn;
}

/**
 * get title of a transaction
 * @param array $_txn
 * @return string
 */
function jrPayment_plugin_paypal_webhook_transaction_title($_txn)
{
    if (isset($_txn['txn_raw']['txn_type'])) {
        return $_txn['txn_raw']['txn_type'];
    }
    elseif (isset($_txn['txn_raw']['payment_type'])) {
        return $_txn['txn_raw']['payment_type'];
    }
    return 'webhook';
}
