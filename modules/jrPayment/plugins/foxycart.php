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
function jrPayment_plugin_foxycart_meta($_post)
{
    return array(
        'title'       => 'FoxyCart',
        'description' => 'FoxyCart is a payment processor that supports over 100 merchant gateways worldwide. Subscription required.',
        'url'         => 'http://www.foxycart.com',
        'admin'       => 'https://admin.foxycart.com/admin'
    );
}

/**
 * Plugin Config
 * @param $_post array $_post
 * @return bool
 */
function jrPayment_plugin_foxycart_config($_post)
{
    global $_conf;

    $_urls = array(
        "Settings &raquo; Receipt Continue URL:<br><b>{$_conf['jrCore_base_url']}/{$_post['module_url']}/success</b>",
        "Advanced &raquo; Datafeed URL:<br><b>{$_conf['jrCore_base_url']}/{$_post['module_url']}/webhook/foxycart</b>",
        "Advanced &raquo; Single Sign On URL:<br><b>{$_conf['jrCore_base_url']}/{$_post['module_url']}/plugin_view/foxycart/sso</b>"
    );
    jrCore_page_notice('success', "Make sure the following URLs are configured in your FoxyCart Control Panel:<br><br>" . implode('<br><br>', $_urls), false);

    // API Key
    $_tmp = array(
        'name'     => 'api_key',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'printable',
        'label'    => 'API Key',
        'help'     => 'This is your FoxyCart API Key - it is required to receive notifications from FoxyCart when an item is sold.<br><br>Your API Key can be found and generated in the FoxyCart control panel under <b>Store</b> &raquo; <b>Advanced</b> &raquo; <b>API Key</b>.'
    );
    jrCore_form_field_create($_tmp);

    // Store Sub Domain
    $_tmp = array(
        'name'     => 'store_sub_domain',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'printable',
        'label'    => 'Store Sub Domain',
        'help'     => 'This is the Store Sub Domain for your FoxyCart store - this can be found in the FoxyCart Dashboard &quot;Currently Selected Store&quot section as <b>Store Sub Domain</b>.'
    );
    jrCore_form_field_create($_tmp);

    // Store Currency
    $_cur = array(
        'ARS' => 'ARS - Argentine Peso',
        'AUD' => 'AUD - Australian Dollars',
        'BDT' => 'BDT - Bangladeshi Taka',
        'BOB' => 'BOB - Bolivian Boliviano',
        'BRL' => 'BRL - Brazilian Real',
        'GBP' => 'GBP - British Pounds Sterling',
        'BGN' => 'BGN - Bulgarian Lev',
        'KHR' => 'KHR - Cambodian Riel',
        'CAD' => 'CAD - Canadian Dollars',
        'CLP' => 'CLP - Chilean Peso',
        'COP' => 'COP - Colombia Peso',
        'CRC' => 'CRC - Costa Rican Colon',
        'HRK' => 'HRK - Croatia Kuna',
        'CZK' => 'CZK - Czech Koruny',
        'DKK' => 'DKK - Danish Kroner',
        'EGP' => 'EGP - Egyptian Pound',
        'EEK' => 'EEK - Estonia Kroon',
        'ETB' => 'ETB - Ethiopian Birr',
        'EUR' => 'EUR - Euros',
        'GTQ' => 'GTQ - Guatamala Quetzal',
        'HNL' => 'HNL - Honduras Lempira',
        'HKD' => 'HKD - Hong Kong Dollars',
        'HUF' => 'HUF - Hungarian Forints',
        'ISK' => 'ISK - Icelandic Króna',
        'INR' => 'INR - Indian Rupee',
        'IDR' => 'IDR - Indonesian Rupiah',
        'ILS' => 'ILS - Israeli Shekel',
        'JPY' => 'JPY - Japanese Yen',
        'KES' => 'KES - Kenyan Shilling',
        'KWD' => 'KWD - Kuwaiti Dinar',
        'LVL' => 'LVL - Latvian Lat',
        'LBP' => 'LBP - Lebanese Pound',
        'LTL' => 'LTL - Lithuanian Litas',
        'MYR' => 'MYR - Malaysian Ringgit',
        'MXN' => 'MXN - Mexican Pesos',
        'TWD' => 'TWD - New Taiwan Dollars',
        'NZD' => 'NZD - New Zealand Dollars',
        'NOK' => 'NOK - Norwegian Kroner',
        'PYG' => 'PYG - Paraguay Guarani',
        'PEN' => 'PEN - Peruvian New Sol',
        'PHP' => 'PHP - Philippine Pesos',
        'PLN' => 'PLN - Polish Zlotys',
        'RON' => 'RON - Romanian New Leu',
        'RUB' => 'RUB - Russian Ruble',
        'SGD' => 'SGD - Singapore Dollars',
        'ZAR' => 'ZAR - South African Rand',
        'KRW' => 'KRW - South Korean Won',
        'SEK' => 'SEK - Swedish Kronor',
        'CHF' => 'CHF - Swiss Francs',
        'THB' => 'THB - Thai Baht',
        'TRY' => 'TRY - Turkish Liras',
        'USD' => 'USD - U.S. Dollars',
        'UAH' => 'UAH - Ukranian Hryvna',
        'UYU' => 'UYU - Uruguayan Peso',
        'VEB' => 'VEB - Venezuelan Bolivar',
        'ZWD' => 'ZWD - Zimbabwe Dollar'
    );
    asort($_cur);
    $_tmp = array(
        'name'     => 'store_currency',
        'type'     => 'select',
        'options'  => $_cur,
        'default'  => 'USD',
        'validate' => 'core_string',
        'label'    => 'store currency',
        'help'     => 'Select the currency you want to use on the site - this must be set the same as the Store Locale setting in your FoxyCart control panel.<br><br><strong>NOTE:</strong> The currency selected here must be supported by the Payment Processor you have select in your FoxyCart control panel - if you have questions about the status of a supported currency, contact FoxyCart for assistance.'
    );
    jrCore_form_field_create($_tmp);
    return true;
}

/**
 * Support for FoxyCart Single Sign On
 * @param array $_post
 * @param array $_user
 * @param array $_conf
 * @return mixed
 */
function jrPayment_plugin_view_foxycart_sso($_post, $_user, $_conf)
{
    if (jrUser_is_logged_in()) {

        // We need to get this user's foxycart customer ID
        $cid = false;
        if (empty($_user['user_foxycart_customer_id'])) {

            // Create this customer at FoxyCart
            $_rs = array(
                'api_action'             => 'customer_save',
                'customer_email'         => $_user['user_email'],
                'customer_password_hash' => $_user['user_password']
            );
            if ($_rs = jrPayment_plugin_foxycart_api_request($_rs)) {
                if (!empty($_rs['customer_id'])) {
                    $cid = $_rs['customer_id'];
                    jrCore_db_update_item('jrUser', $_user['_user_id'], array('user_foxycart_customer_id' => $cid));
                    $_user['user_foxycart_customer_id'] = $cid;
                }
                else {
                    jrCore_logger('CRI', "invalid response from FoxyCart creating customer_id for user_id: {$_user['_user_id']}", $_rs);
                }
            }
            else {
                jrCore_logger('CRI', "invalid response from FoxyCart creating customer_id for user_id: {$_user['_user_id']} (2)", $_rs);
            }
        }
        else {
            $cid = $_user['user_foxycart_customer_id'];
        }

        // redirect to foxycart
        if ($cid) {
            $cfg = jrPayment_get_plugin_config('foxycart');
            $now = (time() + 3600);
            $tkn = sha1($cid . '|' . $now . '|' . $cfg['api_key']);
            $url = "https://{$cfg['store_sub_domain']}.foxycart.com/checkout?fc_auth_token={$tkn}&fcsid={$_post['fcsid']}&fc_customer_id={$cid}&timestamp={$now}";
            jrCore_location($url);
        }
        jrCore_notice_page('error', 'An error was encountered checking out - please try again');
    }

    // Should _never_ get here, but just in case :0
    $murl = jrCore_get_module_url('jrUser');
    jrCore_location("{$_conf['jrCore_base_url']}/{$murl}/login/r=1");
}

/**
 * Support for additional detail buttons
 * @param array $_txn
 * @param array $_entries
 * @return mixed
 */
function jrPayment_plugin_foxycart_txn_detail_buttons($_txn, $_entries)
{
    return false;
}

/**
 * Transaction view URL
 * @param array $_txn
 * @return mixed
 */
function jrPayment_plugin_foxycart_get_transaction_url($_txn)
{
    return 'https://admin.foxycart.com/admin.php?ThisAction=TransactionHistory';
}

/**
 * Get formatted currency
 * @param $amount
 * @return float
 */
function jrPayment_plugin_foxycart_currency_format($amount)
{
    $config = jrPayment_get_plugin_config('foxycart');
    switch ($config['store_currency']) {
        case 'JPY':
            return $amount;
            break;
    }
    return jrCore_number_format($amount / 100, 2);
}

/**
 * Get a currency symbol
 * @return string
 */
function jrPayment_plugin_foxycart_get_currency_code()
{
    $config = jrPayment_get_plugin_config('foxycart');
    return $config['store_currency'];
}

/**
 * Checkout onclick handler
 * @param $cart_hash string MD5 cart hash
 * @param $total int Total cart amount (in cents)
 * @param $_cart array Cart contents
 * @return string
 */
function jrPayment_plugin_foxycart_checkout_onclick($cart_hash, $total, $_cart)
{
    global $_conf, $_post;
    return "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/checkout/{$cart_hash}/foxycart')";
}

/**
 * Get our checkout URL
 * @param $amount int Total amount in cents
 * @param $_cart array cart items
 * @return string
 */
function jrPayment_plugin_foxycart_checkout_url($amount, $_cart)
{
    $config = jrPayment_get_plugin_config('foxycart');
    if (isset($config['store_sub_domain']) && strlen($config['store_sub_domain']) > 0) {

        $amount = ($amount / 100);
        $hashid = "cart:{$_cart['cart_id']}:{$_cart['cart_hash']}";

        $_lng = jrUser_load_lang_strings();
        $_opt = array(
            'code'     => $_lng['jrPayment'][14],
            'name'     => $_lng['jrPayment'][14],
            'price'    => $amount,
            'quantity' => 1
        );
        $_add = array();
        foreach ($_opt as $k => $v) {
            $u_code = jrPayment_plugin_foxycart_hmac_string($k, $v, $_opt['code']);
            $_add[] = "{$k}{$u_code}=" . urlencode($v);
        }
        $_add[] = 'empty=true';
        $_add[] = "h:id={$hashid}";
        return "https://{$config['store_sub_domain']}.foxycart.com/cart?cart=checkout&" . implode('&', $_add);
    }
    return false;
}

/**
 * Parse incoming transaction
 * @param $_post array
 * @return mixed
 */
function jrPayment_plugin_foxycart_webhook_parse($_post)
{
    if (isset($_post['FoxyData']) && strlen($_post['FoxyData']) > 0) {

        $cfg = jrPayment_get_plugin_config('foxycart');
        $_xm = jrPayment_plugin_foxycart_rc4($cfg['api_key'], urldecode($_post['FoxyData']));
        if (!$_xm || strpos($_xm, '<?xml') !== 0) {
            // He have a problem
            jrCore_logger('CRI', "Payments: error decoding foxycart XML transaction data", $_xm);
            return $_xm;
        }
        $_xm = @simplexml_load_string($_xm, null, LIBXML_NOCDATA);
        $_xm = json_decode(json_encode((array) $_xm), true);

        // Did we have issues?
        if (!$_xm || !is_array($_xm) || !isset($_xm['transactions']) || !is_array($_xm['transactions'])) {
            jrCore_logger('CRI', "Payments: no transactions found in foxycart XML transaction data", $_xm);
            return $_xm;
        }

        // Cart and Transaction Info
        $_tx = array(
            'txn_id'     => $_xm['transactions']['transaction']['id'],
            'txn_date'   => strtotime($_xm['transactions']['transaction']['transaction_date']),
            'txn_type'   => 'payment',
            'txn_status' => 'active'
        );

        // Is this a PENDING transaction?  If so, let the user know that the item will be
        // added to their account once the payment actually clears
        if (isset($_xm['transactions']['transaction']['status'][0]) && strtolower($_xm['transactions']['transaction']['status'][0]) == 'pending') {
            // This is a PENDING transaction - we will pick this up later
            $_tx['txn_status'] = 'pending';
        }

        if (isset($_xm['transactions']['transaction']['order_total'])) {
            $_tx['txn_total'] = (int) ($_xm['transactions']['transaction']['order_total'] * 100);
        }
        $_tx['txn_shipping'] = 0;
        if (isset($_xm['transactions']['transaction']['shipping_total']) && $_xm['transactions']['transaction']['shipping_total'] > 0) {
            $_tx['txn_shipping'] = (int) ($_xm['transactions']['transaction']['shipping_total'] * 100);
        }
        $_tx['txn_tax'] = 0;
        if (isset($_xm['transactions']['transaction']['tax_total']) && $_xm['transactions']['transaction']['tax_total'] > 0) {
            $_tx['txn_tax'] = (int) ($_xm['transactions']['transaction']['tax_total'] * 100);
        }
        $_tx['txn_discount'] = 0;
        if (isset($_xm['transactions']['transaction']['discounts']['discount']) && is_array($_xm['transactions']['transaction']['discounts']['discount'])) {
            $_tx['txn_discount'] += jrPayment_price_to_cents($_xm['transactions']['transaction']['discounts']['discount']['amount'] * 100);
        }

        if (isset($_xm['transactions']['transaction']['custom_fields']['custom_field']['custom_field_value']) && strpos($_xm['transactions']['transaction']['custom_fields']['custom_field']['custom_field_value'], 'cart:') === 0) {
            list(, $cart_id, $cart_hash) = explode(':', $_xm['transactions']['transaction']['custom_fields']['custom_field']['custom_field_value'], 3);
            $_tx['txn_cart_id']   = trim($cart_id);
            $_tx['txn_cart_hash'] = trim($cart_hash);
        }

        if (isset($_xm['transactions']['transaction']['customer_email'])) {
            $_tx['txn_user_email'] = $_xm['transactions']['transaction']['customer_email'];
        }

        $name = array();
        if (isset($_xm['transactions']['transaction']['customer_first_name'])) {
            $name[] = $_xm['transactions']['transaction']['customer_first_name'];
        }
        if (isset($_xm['transactions']['transaction']['customer_last_name'])) {
            $name[] = $_xm['transactions']['transaction']['customer_last_name'];
        }
        if (count($name) > 0) {
            $_tx['txn_customer_name'] = implode(' ', $name);
        }

        // Extra Fields
        $_fields = array(
            'customer_address1'    => 'txn_address_line1',
            'customer_address2'    => 'txn_address_line2',
            'customer_city'        => 'txn_address_city',
            'customer_state'       => 'txn_address_state',
            'customer_postal_code' => 'txn_address_zip',
            'customer_country'     => 'txn_address_country'
        );
        foreach ($_fields as $val => $key) {
            if (isset($_xm['transactions']['transaction'][$val]) && !is_array($_xm['transactions']['transaction'][$val]) && strlen($_xm['transactions']['transaction'][$val]) > 0) {
                $_tx[$key] = $_xm['transactions']['transaction'][$val];
            }
        }

        // We need to get the transaction ID from the actual processor
        // This will be stored as r_tag and used to identify refunds
        if (isset($_xm['transactions']['transaction']['processor_response']) && strpos($_xm['transactions']['transaction']['processor_response'], ':')) {
            list(, $gid) = explode(':', $_xm['transactions']['transaction']['processor_response']);
            if ($gid && strlen($gid) > 0) {
                $gid                       = trim($gid);
                $_tx['txn_gateway_txn_id'] = $gid;

                // See if we can get the gateway fee
                if (stripos(' ' . $_xm['transactions']['transaction']['processor_response'], 'stripe')) {
                    require_once APP_DIR . '/modules/jrPayment/plugins/stripe.php';
                    $_tx['txn_gateway_fee'] = jrPayment_plugin_stripe_get_gateway_fee($gid);
                }
            }
        }

        $_tx['txn_raw'] = $_xm;
        return $_tx;
    }
    return $_post;
}

/**
 * Process a webhook
 * @param array $_txn
 * @return array
 */
function jrPayment_plugin_foxycart_webhook_process($_txn)
{
    // This function must exist - just return $_txn
    return $_txn;
}

/**
 * Send out a response to a webhook
 * @param $state string success|failure
 */
function jrPayment_plugin_foxycart_webhook_response($state)
{
    header('HTTP/1.0 200 OK');
    echo 'foxy';
    exit;
}

/**
 * get title of a transaction
 * @param array $_txn
 * @return string
 */
function jrPayment_plugin_foxycart_webhook_transaction_title($_txn)
{
    return 'webhook';
}

/**
 * Get a FoxyCart's customer info by FoxyCart ID
 * @param int $id
 * @return mixed
 */
function jrPayment_plugin_foxycart_get_customer_info_by_id($id)
{
    $_rs = array(
        'api_action'  => 'customer_get',
        'customer_id' => trim($id)
    );
    if ($_rs = jrPayment_plugin_foxycart_api_request($_rs)) {
        if (!empty($_rs['result'])) {
            if (jrCore_str_to_lower($_rs['result']) == 'success') {
                return $_rs;
            }
            jrCore_logger('MAJ', "Payments: API error retrieving customer information from FoxyCart for customer id: {$id}", $_rs);
        }
    }
    // Not found or error
    return false;
}

/**
 * SHA256 encode string for FoxyCart HMAC validation
 * @param $name string Name of parameter
 * @param $value string Value of parameter
 * @param $code string Module/Item ID
 * @return string
 */
function jrPayment_plugin_foxycart_hmac_string($name, $value, $code)
{
    $config = jrPayment_get_plugin_config('foxycart');
    if (isset($config['api_key']) && strlen($config['api_key']) > 0) {
        $val = htmlspecialchars($code) . htmlspecialchars($name) . htmlspecialchars($value);
        return urlencode('||') . hash_hmac('sha256', $val, $config['api_key']) . ($value === "--OPEN--" ? urlencode("||open") : "");
    }
    return false;
}

/**
 * Send an API request to FoxyCart
 * @param $_rs array containing api key => values
 * @return mixed
 */
function jrPayment_plugin_foxycart_api_request($_rs)
{
    $config = jrPayment_get_plugin_config('foxycart');
    if (isset($config['api_key']) && strlen($config['api_key']) > 0) {
        $_rs['api_token'] = $config['api_key'];
        $url              = "https://{$config['store_sub_domain']}.foxycart.com/api";
        $res              = jrCore_load_url($url, $_rs, 'POST', 443, null, null, false);
        if ($res && strlen($res) > 0) {
            $res = @simplexml_load_string($res, null, LIBXML_NOCDATA);
            return json_decode(json_encode((array) $res), true);
        }
    }
    return false;
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
function jrPayment_plugin_foxycart_rc4($key_str, $data_str)
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
        $x              = ($x + 1) % 256;
        $y              = ($state[$x] + $y) % 256;
        $tmp            = $state[$x];
        $state[$x]      = $state[$y];
        $state[$y]      = $tmp;
        $data[$counter] ^= $state[($state[$x] + $state[$y]) % 256];
    }
    // convert output back to a string
    $data_str = "";
    for ($i = 0; $i < $len; $i++) {
        $data_str .= chr($data[$i]);
    }
    return $data_str;
}
