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
function jrPayment_plugin_stripe_meta($_post)
{
    return array(
        'title'       => 'Stripe',
        'description' => 'Stripe is a free to signup payment processor that charges 2.9% + 0.30 per transaction.',
        'url'         => 'https://stripe.com',
        'admin'       => 'https://dashboard.stripe.com'
    );
}

/**
 * Plugin Config
 * @param $_post array $_post
 * @return bool
 */
function jrPayment_plugin_stripe_config($_post)
{
    global $_conf;
    jrCore_page_notice('success', "Set the Stripe Webhook Endpoint to: <b>{$_conf['jrCore_base_url']}/{$_post['module_url']}/webhook/stripe</b><br>in your Stripe Dashboard -> Developers -> Webhooks section.", false);

    // Publish API Key
    $_tmp = array(
        'name'     => 'publish_key',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'printable',
        'label'    => 'Publish API Key',
        'help'     => 'This is your Stripe Publishable API Key<br><br>The Publishable API Key can be found in your Account Settings in the Stripe Dashboard.'
    );
    jrCore_form_field_create($_tmp);

    // Secret API Key
    $_tmp = array(
        'name'     => 'secret_key',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'printable',
        'label'    => 'Secret API Key',
        'help'     => 'This is your Stripe Secret API Key<br><br>The Secret API Key can be found in your Account Settings in the Stripe Dashboard.'
    );
    jrCore_form_field_create($_tmp);

    // Store Currency
    $_cur = array(
        'AUD' => 'AUD - Australian Dollars',
        'GBP' => 'GBP - British Pounds Sterling',
        'CAD' => 'CAD - Canadian Dollars',
        'DKK' => 'DKK - Danish Kroner',
        'EUR' => 'EUR - Euros',
        'NOK' => 'NOK - Norwegian Kroner',
        'SEK' => 'SEK - Swedish Kronor',
        'USD' => 'USD - U.S. Dollars'
    );
    asort($_cur);
    $_tmp = array(
        'name'     => 'store_currency',
        'type'     => 'select',
        'options'  => $_cur,
        'default'  => 'USD',
        'validate' => 'core_string',
        'label'    => 'store currency',
        'help'     => 'Select the currency you want to use on the site'
    );
    jrCore_form_field_create($_tmp);

    // Webhook Signing Secret
    $_tmp = array(
        'name'     => 'signing_secret',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'printable',
        'label'    => 'Webhook Signing Secret',
        'sublabel' => 'see note in help',
        'help'     => 'This is the Webhook Signing Secret that can be enabled for Webhook security in the Developers -> Webhooks section of the Stripe Dashboard.<br><br><b>Important:</b> Do Not enter a value here unless you have enabled the Signing Secret for the Webhook endpoint.',
    );
    jrCore_form_field_create($_tmp);

    // Require Address
    $_tmp = array(
        'name'     => 'address',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'label'    => 'Require Address',
        'help'     => 'If this option is checked, the user will be required to enter an address during checkout'
    );
    jrCore_form_field_create($_tmp);
    return true;
}

/**
 * Initialize the Stripe PHP API
 * @return bool
 */
function jrPayment_plugin_stripe_init_api()
{
    $config = jrPayment_get_plugin_config('stripe');
    require_once APP_DIR . '/modules/jrPayment/contrib/stripe/init.php';
    \Stripe\Stripe::setApiKey($config['secret_key']);
    return true;
}

/**
 * Transaction view URL
 * @param array $_txn
 * @return string
 */
function jrPayment_plugin_stripe_get_transaction_url($_txn)
{
    $url = 'test/';
    if ($_raw = json_decode($_txn['txn_raw'], true)) {
        if (isset($_raw['livemode']) && $_raw['livemode'] == '1') {
            $url = '';
        }
    }
    return "https://dashboard.stripe.com/{$url}payments/" . $_txn['txn_id'];
}

/**
 * Add items to the cart
 * @return string
 */
function jrPayment_plugin_stripe_cart_elements()
{
    return '<script type="text/javascript" src="https://checkout.stripe.com/checkout.js"></script>';
}

/**
 * Setup Stripe Checkout code
 * @param $cart_hash string MD5 cart hash
 * @param $total int Total cart amount (in cents)
 * @param $_cart array Cart contents
 * @return string
 */
function jrPayment_plugin_stripe_checkout_onclick($cart_hash, $total, $_cart)
{
    global $_user, $_conf;
    $amnt = html_entity_decode(jrPayment_get_currency_code()) . jrPayment_currency_format($total);
    $_cfg = jrPayment_get_plugin_config('stripe');
    $addr = (isset($_cfg['address']) && $_cfg['address'] == 'on') ? 'true' : 'false';
    $curr = (isset($_cfg['store_currency'])) ? $_cfg['store_currency'] : 'USD';
    $iurl = jrCore_get_module_url('jrImage');
    $murl = jrCore_get_module_url('jrPayment');
    $html = "<script type=\"text/javascript\">
    function jrPayment_plugin_stripe_checkout() {
        var datap = {
            cart_hash: '{$cart_hash}',
            cart_id: '{$_cart['cart_id']}'
        };
        var token = function(res) {
            datap.token = res.id;
            datap.email = res.email;
            var purl = '{$_conf['jrCore_base_url']}/{$murl}/plugin_view/stripe/checkout/__ajax=1';
            jrCore_set_csrf_cookie(purl);
            $.ajax({
                type: 'POST',
                data: datap,
                cache: false,
                dataType: 'json',
                url: purl,
                success: function(msg) {
                    if (typeof msg.error !== 'undefined') {
                        alert(msg.error);
                    }
                    else {
                        jrCore_window_location(msg.url);
                    }
                }
            });
        };
        StripeCheckout.open({
            key: '{$_cfg['publish_key']}',
            image: '{$_conf['jrCore_base_url']}/{$iurl}/img/module/jrPayment/cart.png?_v={$cart_hash}',
            amount: {$total},
            billingAddress: {$addr},
            currency: '{$curr}',
            email: '{$_user['user_email']}',
            name: 'Cart Checkout',
            description: '" . count($_cart['_items']) . " items - {$amnt}',
            panelLabel:  'Checkout',
            token: token
        });
        return false;
    }</script>";
    jrCore_page_html($html);
    return 'jrPayment_plugin_stripe_checkout()';
}

/**
 * Add "refund" button to transaction detail page
 * @param array $_txn
 * @param array $_entries
 * @return mixed
 */
function jrPayment_plugin_stripe_txn_detail_buttons($_txn, $_entries)
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
        return array(
            jrCore_page_button('refund', $title, "window.open('https://dashboard.stripe.com/payments/{$_txn['txn_id']}')")
        );
    }
    return false;
}

/**
 * Get formatted currency
 * @param $amount
 * @return float
 */
function jrPayment_plugin_stripe_currency_format($amount)
{
    return jrCore_number_format(intval($amount) / 100, 2);
}

/**
 * Get a currency symbol
 * @return string
 */
function jrPayment_plugin_stripe_get_currency_code()
{
    $config = jrPayment_get_plugin_config('stripe');
    return $config['store_currency'];
}

/**
 * Parse incoming transaction
 * @param $_post array Posted info
 * @param $use_post bool
 * @param $validate bool
 * @return mixed
 */
function jrPayment_plugin_stripe_webhook_parse($_post, $use_post = false, $validate = true)
{
    if ($use_post) {
        $_tx = $_post;
    }
    else {
        $tmp = @file_get_contents('php://input');
        $_tx = json_decode($tmp, true);

        // Have we enabled Stripe Signatures?
        if (!empty($_SERVER['HTTP_STRIPE_SIGNATURE'])) {
            $config = jrPayment_get_plugin_config('stripe');
            if (!empty($config['signing_secret'])) {

                // Yes - we have
                $_sig = array();
                foreach (explode(',', $_SERVER['HTTP_STRIPE_SIGNATURE']) as $part) {
                    list($key, $val) = explode('=', $part);
                    $key        = trim($key);
                    $_sig[$key] = trim($val);
                }
                $pass = false;
                if (!empty($_sig['t'])) {
                    $sig = hash_hmac('sha256', "{$_sig['t']}.{$tmp}", trim($config['signing_secret']));
                    foreach ($_sig as $k => $v) {
                        if ($v == $sig) {
                            // We've got the correct signature
                            $pass = true;
                            break;
                        }
                    }
                }
                if (!$pass) {
                    jrCore_logger('CRI', "Payments: stripe transaction received in webhook fails signature validation", array('signature' => $_SERVER['HTTP_STRIPE_SIGNATURE'], 'config' => $config, 'transaction' => $_tx));
                    return false;
                }
            }
        }
    }

    if (!isset($_tx['type'])) {
        jrCore_logger('CRI', "Payments: stripe transaction received in webhook without a valid type", $_tx);
        return false;
    }

    // Validate this came from Stripe and is not a spoof
    jrPayment_plugin_stripe_init_api();

    $fee = false;
    if ($validate && !strpos($_tx['id'], '_0000000000000')) {
        try {
            $_ev = \Stripe\Event::retrieve($_tx['id'])->__toArray(true);
        }
        catch (Exception $e) {
            // We had a problem retrieving this one
            jrCore_logger('CRI', "Payments: error retrieving transaction in webhook", array('error' => $e, '_txn' => $_tx));
            return false;
        }
        if (!$_ev || !is_array($_ev)) {
            jrCore_logger('CRI', "Payments: unverified transaction received in webhook", $_tx);
            return false;
        }
        if ($_ev['type'] != $_tx['type']) {
            jrCore_logger('CRI', "Payments: invalid transaction received in webhook - type mismatch", $_tx);
            return false;
        }
    }

    // Next - get the BALANCE transaction so we can include the fee
    if (isset($_tx['data']['object']['balance_transaction'])) {
        $fee = jrPayment_plugin_stripe_get_gateway_fee($_tx['id'], $_tx['data']['object']['balance_transaction']);
    }

    // Cart and Transaction Info
    $_dt = array(
        'txn_status' => 'active'
    );
    if (isset($_tx['data']['object']['id'])) {
        $_dt['txn_id'] = $_tx['data']['object']['id'];
    }

    // Specific transactions will be balance affecting
    $type = 'information';
    switch ($_tx['type']) {
        case 'charge.succeeded':
            $type = 'payment';
            break;
        case 'charge.refunded':
            $type = 'refund';
            break;
    }
    $_dt['txn_type'] = $type;

    if (isset($_tx['created'])) {
        $_dt['txn_date'] = $_tx['created'];
    }
    elseif (isset($_tx['data']['object']['created'])) {
        $_dt['txn_date'] = $_tx['data']['object']['created'];
    }

    if ($type != 'information') {
        if (isset($_tx['data']['object']['amount'])) {
            $_dt['txn_total'] = jrPayment_price_to_cents($_tx['data']['object']['amount']);
        }
        $_dt['txn_shipping'] = 0;
        $_dt['txn_tax']      = 0;
        if ($fee) {
            $_dt['txn_gateway_fee'] = $fee;
        }
        if (isset($_tx['data']['object']['metadata']['cart'])) {
            list($cart_id, $cart_hash) = explode(':', $_tx['data']['object']['metadata']['cart'], 2);
            $_dt['txn_cart_id']   = trim($cart_id);
            $_dt['txn_cart_hash'] = trim($cart_hash);
        }
        if (isset($_tx['data']['object']['description']) && strpos($_tx['data']['object']['description'], 'cart')) {
            list($email,) = explode('-', $_tx['data']['object']['description'], 2);
            $_dt['txn_user_email'] = trim($email);
        }
    }

    // Get email address on transaction
    if (isset($_tx['data']['object']['email']) && strpos($_tx['data']['object']['email'], '@')) {
        $_dt['txn_user_email'] = trim($_tx['data']['object']['email']);
    }
    elseif (isset($_tx['data']['object']['source']['email']) && strpos($_tx['data']['object']['source']['email'], '@')) {
        $_dt['txn_user_email'] = trim($_tx['data']['object']['source']['email']);
    }
    elseif (isset($_tx['data']['object']['name']) && strpos($_tx['data']['object']['name'], '@')) {
        $_dt['txn_user_email'] = trim($_tx['data']['object']['name']);
    }
    elseif (isset($_tx['data']['object']['source']['name']) && strpos($_tx['data']['object']['source']['name'], '@')) {
        $_dt['txn_user_email'] = trim($_tx['data']['object']['source']['name']);
    }
    elseif (isset($_tx['data']['object']['card']['email']) && strpos($_tx['data']['object']['card']['email'], '@')) {
        $_dt['txn_user_email'] = trim($_tx['data']['object']['card']['email']);
    }
    elseif (isset($_tx['data']['object']['card']['name']) && strpos($_tx['data']['object']['card']['name'], '@')) {
        $_dt['txn_user_email'] = trim($_tx['data']['object']['card']['name']);
    }

    // Extra Fields
    $_fields = array(
        'address_line1',
        'address_line2',
        'address_city',
        'address_state',
        'address_country',
        'address_zip'
    );
    foreach ($_fields as $val) {
        if (isset($_tx['data']['object']['card'][$val]) && strlen($_tx['data']['object']['card'][$val]) > 0) {
            $_dt["txn_{$val}"] = $_tx['data']['object']['card'][$val];
        }
        elseif (isset($_tx['data']['object']['source'][$val]) && strlen($_tx['data']['object']['source'][$val]) > 0) {
            $_dt["txn_{$val}"] = $_tx['data']['object']['source'][$val];
        }
    }
    asort($_tx);
    $_dt['txn_raw'] = $_tx;
    return $_dt;
}

/**
 * Get the Stripe Gateway fee
 * @param $charge_id
 * @param null $balance_txn_id
 * @return int
 */
function jrPayment_plugin_stripe_get_gateway_fee($charge_id, $balance_txn_id = null)
{
    jrPayment_plugin_stripe_init_api();
    if (is_null($balance_txn_id)) {
        // We've only been given the charge_id - get balance_transaction from charge
        try {
            $_bt = \Stripe\Charge::retrieve($charge_id)->__toArray(true);
        }
        catch (Exception $e) {
            $_bt = false;
        }
        if ($_bt && is_array($_bt) && isset($_bt['balance_transaction'])) {
            // Now we can get the balance
            try {
                $_bt = \Stripe\BalanceTransaction::retrieve($_bt['balance_transaction'])->__toArray(true);
            }
            catch (Exception $e) {
                $_bt = false;
            }
        }
    }
    else {
        // We can get our balance_transaction directly
        try {
            $_bt = \Stripe\BalanceTransaction::retrieve($balance_txn_id)->__toArray(true);
        }
        catch (Exception $e) {
            $_bt = false;
        }
    }
    if ($_bt && is_array($_bt) && isset($_bt['fee'])) {
        return intval($_bt['fee']);
    }
    return 0;
}

/**
 * Process incoming transaction
 * @param $_tx array Transaction
 * @return mixed
 */
function jrPayment_plugin_stripe_webhook_process($_tx)
{
    $_raw = $_tx['txn_raw'];
    // See what we are doing
    switch ($_raw['type']) {

        // New Customer created
        case 'customer.created':
            jrCore_logger('INF', "Payments: Stripe customer account successfully created for " . $_raw['data']['object']['email'] . " (" . $_raw['data']['object']['description'] . ")", $_raw);
            return false;
            break;

        case 'customer.updated':
            jrCore_logger('INF', "Payments: Stripe customer account successfully updated for " . $_raw['data']['object']['email'] . " (" . $_raw['data']['object']['description'] . ")", $_raw);
            return false;
            break;

        // We've had money deposited
        case 'transfer.paid':
            jrCore_logger('INF', "Payments: Stripe has transferred " . jrPayment_currency_format($_raw['data']['object']['amount']) . " to the active bank account", $_raw);
            return false;
            break;

        case 'transfer.failed':
            jrCore_logger('MAJ', "Payments: Stripe transfer of " . jrPayment_currency_format($_raw['data']['object']['amount']) . " to the bank account has failed", $_raw);
            return false;
            break;

        case 'charge.failed':
            $nam = (isset($_raw['data']['object']['description']) && strpos($_raw['data']['object']['description'], '@')) ? ' for user_email: ' . $_raw['data']['object']['description'] : false;
            if (!$nam) {
                $nam = (isset($_raw['data']['object']['source']['name']) && strpos($_raw['data']['object']['source']['name'], '@')) ? ' for user_email: ' . $_raw['data']['object']['source']['name'] : false;
            }
            if (!$nam) {
                $nam = (isset($_raw['data']['object']['receipt_email']) && strpos($_raw['data']['object']['receipt_email'], '@')) ? ' for user_email: ' . $_raw['data']['object']['receipt_email'] : '';
            }
            jrCore_logger('MAJ', "Payments: Stripe charge failure of " . jrPayment_currency_format($_raw['data']['object']['amount']) . $nam, $_raw);
            return false;
            break;

        case 'charge.succeeded':
            // We had a successful purchase - this is handled by the Payments module directly
            break;

        // These are transaction types we actually DO SOMETHING with
        case 'charge.refunded':

            // We had a refund - remove the items from the user's access
            $tid = $_raw['data']['object']['id'];

            $_rt = jrPayment_get_register_entries_by_gateway_id($tid);
            if (!$_rt || !is_array($_rt)) {
                // If stripe is NOT the active payment processor, it could be under the txn_gateway_txn_id
                if ($_tx = jrCore_db_get_item_by_key('jrPayment', 'txn_gateway_txn_id', $tid)) {
                    // We found the id
                    $_rt = jrPayment_get_register_entries_by_gateway_id($_tx['txn_id']);
                }
            }
            if ($_rt && is_array($_rt)) {

                jrCore_logger('MAJ', "Payments: Stripe refund transaction received for txn_id: {$tid}", $_raw);

                // Is the amount being refunded DIFFERENT than the payment?  If so, we probably
                // have a partial refund - see if we can figure out WHAT to refund
                if ($_raw['data']['object']['amount'] != $_raw['data']['object']['amount_refunded'] && isset($_raw['data']['object']['refunds']['data'])) {
                    // We have a difference
                    $_found = array();
                    foreach ($_raw['data']['object']['refunds']['data'] as $_refund) {
                        // Go through each refund and see if we have a MATCH that has not already been refunded
                        foreach ($_rt as $_e) {
                            if ($_e['r_refunded_time'] > 0) {
                                // Already refunded...
                                continue;
                            }
                            if ($_e['r_amount'] == $_refund['amount']) {
                                // We have found one that it MIGHT be
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
            return false;
            break;

        case 'charge.dispute.created':
            jrCore_logger('MAJ', "Payments: Stripe charge dispute created", $_raw);
            break;

        // NOTE: other txn_type's not specifically watched for here will be logged to the Raw Transaction log

    }
    return $_tx;
}

/**
 * get title of a transaction
 * @param array $_txn
 * @return bool
 */
function jrPayment_plugin_stripe_webhook_transaction_title($_txn)
{
    return (isset($_txn['txn_raw']['type'])) ? $_txn['txn_raw']['type'] : '';
}

/**
 * Process checkout
 * @param $_post array posted params
 * @param $_user array current User
 * @param $_conf array Global Conf
 * @return bool
 */
function jrPayment_plugin_view_stripe_checkout($_post, $_user, $_conf)
{
    jrCore_validate_location_url();
    if (!isset($_post['cart_id']) || !jrCore_checktype($_post['cart_id'], 'number_nz')) {
        $_rs = array('error' => 'Invalid cart id');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['cart_hash']) || !jrCore_checktype($_post['cart_hash'], 'md5')) {
        $_rs = array('error' => 'Invalid cart hash');
        jrCore_json_response($_rs);
    }
    $_cr = jrPayment_get_cart_by_id($_post['cart_id']);
    if (!$_cr || !is_array($_cr)) {
        $_rs = array('error' => 'Invalid cart - not found');
        jrCore_json_response($_rs);
    }

    // Charge it
    jrPayment_plugin_stripe_init_api();

    // Our token is unique for our customer
    $token = (isset($_post['token'])) ? $_post['token'] : '';

    // Create new Stripe customer if needed
    $new_customer = false;
    if (!isset($_user['user_stripe_customer_id']) || strlen($_user['user_stripe_customer_id']) === 0) {

        // Must get a token
        if (!isset($_post['token']) || strlen($_post['token']) === 0) {
            jrCore_logger('CRI', 'Payments: invalid token received in checkout', array('_post' => $_post, '_us' => $_user));
            $_er = array('error' => 'an error was encountered checking out - please try again');
            jrCore_json_response($_er);
        }
        // Create new Customer
        try {
            $customer = \Stripe\Customer::create(array(
                'card'        => $token,
                'email'       => $_user['user_email'],
                'description' => $_user['user_name']
            ));
            /** @noinspection PhpUndefinedFieldInspection */
            $cid = $customer->id;
        }
        catch (Exception $e) {
            jrCore_logger('CRI', 'Payments: error creating customer account with Stripe', array('_us' => $_user, 'error' => $e));
            $_er = array('error' => 'Unable to successfully create the transaction - please try again');
            jrCore_json_response($_er);
            return false;
        }

        if (!$cid) {
            jrCore_logger('CRI', 'Payments: error creating customer account with Stripe', array('_us' => $_user, 'cid' => $cid));
            $_er = array('error' => 'Unable to successfully create the transaction - please try again (2)');
            jrCore_json_response($_er);
            return false;
        }

        // Fall through - Stripe customer created, update user info
        jrCore_db_update_item('jrUser', $_user['_user_id'], array('user_stripe_customer_id' => $cid), null, false);
        $_user['user_stripe_customer_id'] = $cid;
        $new_customer                     = true;
    }

    // Make sure our customer info is updated with new info first
    if (!$new_customer) {
        try {
            $cs = \Stripe\Customer::retrieve($_user['user_stripe_customer_id']);
            /** @noinspection PhpUndefinedFieldInspection */
            $cs->source = $token;
            $cs->save();
        }
        catch (Exception $e) {
            jrCore_logger('MAJ', 'Payments: error retriving customer info from Stripe', array('_post' => $_post, '_us' => $_user, 'error' => $e));
            $_er = array('error' => 'An error was encountered saving the transaction - please try again');
            jrCore_json_response($_er);
        }
    }

    // Run charge
    try {

        $config = jrPayment_get_plugin_config('stripe');
        \Stripe\Charge::create(array(
            'amount'      => jrPayment_get_cart_total($_cr),
            'currency'    => $config['store_currency'],
            'description' => "{$_user['user_email']} - cart",
            'customer'    => $_user['user_stripe_customer_id'],
            'metadata'    => array(
                'cart' => "{$_post['cart_id']}:{$_post['cart_hash']}"
            )
        ));

        // Let the user know the transaction is being processed
        $url = jrCore_get_module_url('jrPayment');
        $_rs = array('url' => "{$_conf['jrCore_base_url']}/{$url}/success");
        return jrCore_json_response($_rs);

    }
    catch (Exception $e) {
        jrCore_logger('MAJ', 'Payments: chart checkout purchase credit card declined', $e);
        $_er = array('error' => 'The purchase was declined - please check the Credit Card info and try again');
        jrCore_json_response($_er);
        return false;
    }

}

/**
 * hourly maintenance
 * @return bool
 */
function jrPayment_plugin_stripe_hourly_maintenance()
{
    // Check for Stripe transactions missing the transaction fee
    $_queue = array(
        'plugin'   => 'stripe',
        'function' => 'get_missing_gateway_fees'
    );
    jrCore_queue_create('jrPayment', 'payment_tasks', $_queue);
    return true;
}

/**
 * hourly maintenance
 * @param array $_queue
 * @return bool
 */
function jrPayment_plugin_stripe_get_missing_gateway_fees($_queue)
{
    $tbl = jrCore_db_table_name('jrPayment', 'register');
    $req = "SELECT r_id, r_txn_id, r_gateway_id FROM {$tbl} WHERE r_plugin = 'stripe' AND r_gateway_fee = 0 AND r_gateway_fee_checked = 0 AND r_gateway_id LIKE 'ch_%' ORDER BY r_id DESC LIMIT 500";
    $_rt = jrCore_db_query($req, 'r_id');
    if ($_rt && is_array($_rt)) {

        jrPayment_plugin_stripe_init_api();

        $cnt = 0;
        foreach ($_rt as $rid => $_r) {
            // Get info about this transaction
            $update = true;
            if ($_tx = jrCore_db_get_item('jrPayment', $_r['r_txn_id'], true)) {
                if (isset($_tx['txn_raw']) && strlen($_tx['txn_raw']) > 0) {
                    if ($_raw = json_decode($_tx['txn_raw'], true)) {
                        if (!isset($_raw['data']['object']['balance_transaction']) && isset($_raw['data']['object']['charge'])) {
                            if ($_tx = jrCore_db_get_item_by_key('jrPayment', 'txn_id', $_raw['data']['object']['charge'])) {
                                $_raw = json_decode($_tx['txn_raw'], true);
                            }
                        }
                        // Do we have a balance transaction value?
                        if ($_raw && is_array($_raw) && isset($_raw['data']['object']['balance_transaction']{1})) {
                            try {
                                $_bt = \Stripe\BalanceTransaction::retrieve($_raw['data']['object']['balance_transaction'])->__toArray(true);
                            }
                            catch (Exception $e) {
                                // Error getting balance transaction info
                                jrCore_logger('CRI', "Payments: error retrieving balance transaction for: {$_raw['data']['object']['balance_transaction']}", $e);
                                $_bt = false;
                            }
                            if ($_bt && is_array($_bt) && isset($_bt['fee'])) {
                                // Update register entry with correct gateway fee
                                $fee = (int) $_bt['fee'];
                                $req = "UPDATE {$tbl} SET r_gateway_fee = {$fee}, r_gateway_fee_checked = 1 WHERE r_id = {$_r['r_id']} LIMIT 1";
                                jrCore_db_query($req);
                                $cnt++;
                            }
                            usleep(500000);
                            continue;
                        }
                    }
                }
            }
            if ($update) {
                // Update this transaction so we do not check again
                $req = "UPDATE {$tbl} SET r_gateway_fee_checked = 1 WHERE r_id = {$_r['r_id']} LIMIT 1";
                jrCore_db_query($req);
                $cnt++;
            }
        }
        if ($cnt > 0) {
            jrCore_logger('INF', "Payments: updated {$cnt} transactions with correct gateway fee");
        }
    }
    return true;
}
