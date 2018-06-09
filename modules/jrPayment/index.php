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

define('PAYMENT_CART_COMPLETE', 1);
define('PAYMENT_CART_PENDING', 2);

//------------------------------
// webhook
//------------------------------
function view_jrPayment_webhook($_post, $_user, $_conf)
{
    // http://site.com/payment/webhook/[plugin]
    if (isset($_post['_1']) && jrPayment_is_valid_plugin($_post['_1'])) {
        // This is optional, but if we get the plugin on the webhook we use it -
        // that way if a system changes active payment plugins, previous webhooks
        // for things like refunds will continue to function properly
        jrPayment_set_active_plugin($_post['_1']);
    }

    // Step 1 - Parse and Validate
    // This ensures we have a valid callback to our web hook and the plugin
    // formats it to ensure we have some required pieces of info
    $_tx = jrPayment_run_plugin_function('webhook_parse', $_post);
    if ($_tx && is_array($_tx)) {

        // Give listening modules a chance to process this
        $_tx = jrCore_trigger_event('jrPayment', 'webhook_parsed', $_tx);
        if (isset($_tx['transaction_complete']) && $_tx['transaction_complete'] == 1) {
            // We were handled 100% by a listener
            jrPayment_run_plugin_function('webhook_response', 'success', $_post);
            header('HTTP/1.0 200 OK');
            exit;
        }

        // If we are using the FOXYCART plugin, then we need to watch for REFUND
        // transactions from other gateways, since FOXYCART does not handle refunds
        if ($_conf['jrPayment_plugin'] == 'foxycart' && $_post['_1'] != 'foxycart') {

            // This is a webhook from paypal/stripe, but FoxyCart is active.
            if (isset($_tx['txn_type']) && $_tx['txn_type'] != 'refund') {
                // This is NOT a refund transaction - exit as we handle this direct
                jrPayment_run_plugin_function('webhook_response', 'success', $_post);
                header('HTTP/1.0 200 OK');
                exit;
            }
        }

        // Did we get a valid transaction type?
        if (!isset($_tx['txn_type'])) {
            jrCore_logger('CRI', "Payments: txn_type not found in incoming transaction", array('_tx' => $_tx, '_post' => $_post));
            jrPayment_run_plugin_function('webhook_response', 'failure', $_post);
            header('HTTP/1.0 400 Bad Response');
            exit;
        }

        // Have we already processed this transaction?
        if (isset($_tx['txn_id'])) {
            if ($_ex = jrCore_db_get_item_by_key('jrPayment', 'txn_id', $_tx['txn_id'], true)) {
                // We've already processed this transaction
                jrPayment_run_plugin_function('webhook_response', 'success', $_post);
                header('HTTP/1.0 200 OK');
                exit;
            }
        }

        // Store our transaction in the DS
        $tid = jrPayment_create_transaction($_tx);

        // Save stats
        if (isset($_tx['txn_type'])) {
            jrCore_create_stat_entry('jrPayment', 'txn_count', $_tx['txn_type'], 0, 0, null, false, 1);
            if (isset($_tx['txn_total']) && $_tx['txn_total'] > 0) {
                jrCore_create_stat_entry('jrPayment', 'txn_total', $_tx['txn_type'], 0, 0, null, false, $_tx['txn_total']);
            }
        }

        // Log incoming raw data
        $plug = jrPayment_get_active_plugin();
        if (isset($_tx['txn_type']) && $_tx['txn_type'] != 'information' && isset($_tx['txn_id']) && strlen($_tx['txn_id']) > 0) {
            jrCore_logger('INF', "Payments: processing incoming {$plug} transaction: {$_tx['txn_id']}", $_tx);
        }
        $_tx['txn_plugin']   = $plug;
        $_tx['txn_currency'] = jrPayment_run_plugin_function('get_currency_code');

        // Did we get all required fields for a balance affecting transaction?
        if ($_tx['txn_type'] == 'payment' && !jrPayment_transaction_contains_required_fields($_tx)) {
            jrPayment_run_plugin_function('webhook_response', 'failure', $_post);
            header('HTTP/1.0 400 Bad Response');
            exit;
        }

        // process
        if ($tid && jrCore_checktype($tid, 'number_nz')) {

            // Step 2 - Process
            // Let the plugin process and remove unnecessary events
            $_tx['txn_item_id'] = $tid;
            $_tx                = jrPayment_run_plugin_function('webhook_process', $_tx);
            if ($_tx && is_array($_tx)) {

                // Trigger - allow modules to process their own items if needed
                $_tx = jrCore_trigger_event('jrPayment', 'webhook_event', $_tx);

                // Note: As long as the transaction does NOT have txn_cart_id
                // in it, then it can be for any other module/situation as
                // long as it is handled by the listener (i.e. subscriptions)
                // or in the payment webhook_process function

                //---------------------
                // CART TRANSACTION
                //---------------------
                // If we come out of our event with a txn_cart_id, it means
                // we have a properly formatted CART PURCHASE transaction
                if (isset($_tx['txn_cart_id']) && jrCore_checktype($_tx['txn_cart_id'], 'number_nz')) {

                    // Get cart associated with this transaction
                    $_cr = jrPayment_get_cart_by_id($_tx['txn_cart_id']);
                    if (!$_cr || !is_array($_cr)) {
                        jrCore_logger('CRI', "Payments: cart not found for incoming transaction", $_tx);
                        jrPayment_run_plugin_function('webhook_response', 'success', $_post);
                        header('HTTP/1.0 200 OK');
                        exit;
                    }
                    $_tx['txn_user_id'] = $_cr['cart_user_id'];

                    // Validate that the cart has not been tampered with
                    if (!jrPayment_validate_cart($_cr, $_tx)) {
                        jrCore_logger('CRI', "Payments: cart validation hash mismatch", array('cart' => $_cr, 'transaction' => $_tx));
                        jrPayment_run_plugin_function('webhook_response', 'failure', $_post);
                        header('HTTP/1.0 400 Bad Response');
                        exit;
                    }
                    $_tx['txn_item_count'] = count($_cr['_items']);

                    // Update transaction
                    $_up = array(
                        'txn_user_id'    => $_tx['txn_user_id'],
                        'txn_item_count' => $_tx['txn_item_count']
                    );
                    if (isset($_cr['cart_charge'])) {
                        $_up['txn_charge'] = (int) $_cr['cart_charge'];
                    }
                    jrCore_db_update_item('jrPayment', $tid, $_up);

                    // Cart items?
                    if (isset($_cr['_items']) && count($_cr['_items']) > 0) {

                        // If this is an ACTIVE transaction, we can process it now.
                        // If pending, we have to save for later when payment comes through
                        if ($_tx['txn_status'] == 'active') {

                            // Count sold items
                            jrCore_create_stat_entry('jrPayment', 'item_count', 'cart', 0, 0, null, false, $_tx['txn_item_count']);

                            // Mark this cart as having completed checkout
                            jrPayment_set_cart_status($_tx['txn_cart_id'], PAYMENT_CART_COMPLETE);

                            // Get user info that made this purchase
                            $_us = jrCore_db_get_item('jrUser', $_cr['cart_user_id']);

                            // Record our register entry for each item sold
                            $_pi = array();
                            foreach ($_cr['_items'] as $k => $_i) {

                                $tag = null;
                                if (isset($_tx['txn_gateway_txn_id'])) {
                                    $tag = $_tx['txn_gateway_txn_id'];
                                }
                                $fee = 0;
                                if (isset($_tx['txn_gateway_fee'])) {
                                    $fee = (int) $_tx['txn_gateway_fee'];
                                }
                                $tax = 0;
                                if (isset($_tx['txn_tax'])) {
                                    $tax = (int) $_tx['txn_tax'];
                                }
                                if (!jrPayment_record_sale_in_register($tid, $_tx['txn_id'], $_tx['txn_user_id'], $_i, $tag, $fee, $tax)) {
                                    jrCore_logger('CRI', "Payments: unable to record sale in register", array('_tx' => $_tx, '_cr' => $_cr, '_i' => $_i));
                                    continue;
                                }

                                // Increment sale counter for this item
                                if ($pfx = jrCore_db_get_prefix($_i['cart_module'])) {
                                    jrCore_db_increment_key($_i['cart_module'], $_i['_item_id'], "{$pfx}_sale_count", 1);
                                }
                                // Increment Profile sale count
                                jrCore_db_increment_key('jrProfile', $_i['_profile_id'], 'profile_jrPayment_sale_count', 1);

                                // For each item in the cart we give the selling module a chance to process the purchase of the item.
                                jrCore_trigger_event('jrPayment', 'purchase_item', $_i, $_cr, $_i['cart_module']);

                                $pid = (int) $_i['_profile_id'];
                                if (!isset($_pi[$pid])) {
                                    $_pi[$pid] = array(
                                        '_items' => array()
                                    );
                                }
                                $_cr['_items'][$k]['item_name'] = jrPayment_get_sold_item_name($_i);
                                $_pi[$pid]['_items'][]          = $_cr['_items'][$k];
                            }

                            // Notify profiles of sale
                            if (count($_pi) > 0) {
                                foreach ($_pi as $pid => $_items) {
                                    jrPayment_notify_profile_of_sold_items($pid, $_items, $_us);
                                }
                            }

                            // Send the buyer their receipt
                            jrPayment_send_buyer_receipt($_cr['cart_user_id'], $_cr['_items']);

                            // Notify site admins
                            $_cr['txn_id'] = $tid;
                            jrPayment_notify_admins_of_sold_items($_cr, $_us);

                        }
                        elseif ($_tx['txn_status'] == 'pending') {

                            // For a PENDING payment, we just mark the cart as COMPLETE -
                            // when the actual payment clears, the status will be ACTIVE
                            // and the cart will be processed at that time
                            // Mark this cart as having completed checkout
                            jrPayment_set_cart_status($_tx['txn_cart_id'], PAYMENT_CART_PENDING);

                            // Get user info that made this purchase
                            if ($_us = jrCore_db_get_item('jrUser', $_cr['cart_user_id'])) {

                                // Notify them that their purchase is pending
                                jrPayment_notify_buyer_of_pending_purchase($_us, $_cr);

                            }

                        }
                    }

                }
            }
        }
        else {
            jrCore_logger('CRI', "Payments: error saving transaction id {$_tx['txn_id']} to the datastore", $_tx);
        }
    }

    jrPayment_run_plugin_function('webhook_response', 'success', $_post);
    header('HTTP/1.0 200 OK');
    exit;
}

//-----------------------------------
// report
//-----------------------------------
function view_jrPayment_report($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_admin_tabs('jrPayment');
    jrCore_page_include_admin_menu();
    jrCore_page_banner('monthly report');

    $dat             = array();
    $dat[1]['title'] = 'graph';
    $dat[1]['class'] = 'center';
    jrCore_page_table_header($dat);

    $_rt = false;
    $off = date_offset_get(new DateTime);
    $tbl = jrCore_db_table_name('jrPayment', 'register');
    $req = "SELECT r_seller_profile_id AS pid,
                FROM_UNIXTIME(r_created + {$off}, '%M %Y') AS v,
                FROM_UNIXTIME(r_created + {$off}, '%Y%m') AS c,
                SUM(((r_quantity * r_amount) + r_shipping + r_tax) - r_refunded_amount) AS i,
                SUM(r_fee) AS f,
                SUM(r_expense) AS s,
                SUM(r_gateway_fee) AS e
            FROM {$tbl} GROUP BY c, pid ORDER BY c DESC";
    $_tm = jrCore_db_query($req, 'NUMERIC');
    if ($_tm && is_array($_tm)) {
        $_rt = array();
        foreach ($_tm as $_data) {
            $idx = $_data['c'];
            if (!isset($_rt[$idx])) {
                $_rt[$idx] = array(
                    'i' => 0,
                    'f' => 0,
                    'e' => 0,
                    's' => 0
                );
            }
            $_rt[$idx]['v'] = $_data['v'];
            $_rt[$idx]['c'] = $_data['c'];
            $_rt[$idx]['i'] += $_data['i'];
            $_rt[$idx]['e'] += $_data['e'];
            $_rt[$idx]['s'] += $_data['s'];
            if ($_data['pid'] > 0) {
                $_rt[$idx]['f'] += $_data['f'];
            }
        }
    }
    unset($_tm);

    $smarty          = new stdClass();
    $params          = array(
        'module' => 'jrPayment',
        'name'   => 'monthly_report',
        'width'  => '100%',
        'height' => '300px'
    );
    $dat             = array();
    $dat[1]['title'] = smarty_function_jrGraph_embed($params, $smarty);
    $dat[1]['class'] = 'center';
    jrCore_page_table_row($dat);
    jrCore_page_table_footer();

    $dat             = array();
    $dat[1]['title'] = 'month';
    $dat[1]['width'] = '15%';
    $dat[2]['title'] = 'gross income';
    $dat[2]['width'] = '20%';
    $dat[3]['title'] = 'expense';
    $dat[3]['width'] = '20%';
    $dat[4]['title'] = 'gateway fees';
    $dat[4]['width'] = '20%';
    $dat[5]['title'] = 'net income';
    $dat[5]['width'] = '20%';
    $dat[6]['title'] = 'report';
    $dat[6]['width'] = '5%';
    jrCore_page_table_header($dat);

    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $_m) {
            $dat             = array();
            $dat[1]['title'] = $_m['v'];
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_m['i']);
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_m['f'] + $_m['s']);
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_m['e']);
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_m['i'] - $_m['f'] - $_m['e'] - $_m['s']);
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = jrCore_page_button("month-{$_m['c']}", 'report', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/report_detail/date={$_m['c']}')");
            $dat[6]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button('referrer');
    jrCore_page_display();
}

//-----------------------------------
// report_detail
//-----------------------------------
function view_jrPayment_report_detail($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_admin_tabs('jrPayment');
    jrCore_page_include_admin_menu();

    $date = strftime('%Y%m');
    $time = time();
    $frmt = '%Y%m';
    if (isset($_post['date'])) {
        if (strlen($_post['date']) === 6) {
            $date = $_post['date'];
            $time = gmmktime(1, 1, 1, substr($date, 4, 2), 15, substr($date, 0, 4));
        }
        elseif (strlen($_post['date']) === 4) {
            $date = $_post['date'];
            $time = gmmktime(1, 1, 1, 1, 15, substr($date, 0, 4));
            $frmt = '%Y';
        }
    }
    $button = jrCore_page_button('back', 'monthly report', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/report')");
    jrCore_page_banner("monthly report - " . strftime('%B, %Y', $time), $button);

    $_rt = array();
    $off = date_offset_get(new DateTime);
    $tbl = jrCore_db_table_name('jrPayment', 'register');
    $req = "SELECT r_seller_profile_id AS p,
              SUM((r_quantity * r_amount) + r_shipping + r_tax) AS i,
              SUM(r_fee) AS e,
              SUM(r_gateway_fee) AS f,
              SUM(r_expense) AS s,
              IF(r_field = 'transaction', r_tag, r_module) AS m
            FROM {$tbl} WHERE FROM_UNIXTIME(r_created + {$off}, '{$frmt}') = '{$date}' GROUP BY m, p ORDER BY i DESC, s DESC";
    $_tm = jrCore_db_query($req, 'NUMERIC');

    // Get our totals
    $inc = 0;
    $exp = 0;
    $fee = 0;
    if ($_tm && is_array($_tm)) {
        foreach ($_tm as $v) {
            $idx = $v['m'];
            if (!isset($_rt[$idx])) {
                $_rt[$idx] = array(
                    'i' => 0,
                    'e' => 0,
                    'f' => 0,
                    's' => 0,
                    'm' => $idx
                );
            }
            $_rt[$idx]['i'] += $v['i'];
            $_rt[$idx]['f'] += $v['f'];
            if ($v['p'] > 0) {
                $_rt[$idx]['e'] += ($v['e'] + $v['s']);
                $exp            += ($v['e'] + $v['s']);
            }
            else {
                $_rt[$idx]['e'] += $v['s'];
                $exp            += $v['s'];
            }
            $inc += $v['i'];
            $fee += $v['f'];
        }
    }
    unset($_tm);

    $dat             = array();
    $dat[1]['title'] = 'gross income';
    $dat[1]['width'] = '25%';
    $dat[2]['title'] = 'expense';
    $dat[2]['width'] = '25%';
    $dat[3]['title'] = 'gateway fees';
    $dat[3]['width'] = '25%';
    $dat[4]['title'] = 'net income';
    $dat[4]['width'] = '25%';
    jrCore_page_table_header($dat);

    $dat             = array();
    $dat[1]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($inc);
    $dat[1]['class'] = 'payment-bignum bignum bignum4';
    $dat[2]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($exp);
    $dat[2]['class'] = 'payment-bignum bignum bignum1';
    $dat[3]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($fee);
    $dat[3]['class'] = 'payment-bignum bignum bignum1';
    $dat[4]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($inc - $exp - $fee);
    $dat[4]['class'] = 'payment-bignum bignum bignum3';
    jrCore_page_table_row($dat);
    jrCore_page_table_footer();

    if ($_rt && is_array($_rt)) {
        $dat             = array();
        $dat[0]['title'] = 'category';
        $dat[0]['width'] = '28%';
        $dat[1]['title'] = 'gross';
        $dat[1]['width'] = '18%';
        $dat[2]['title'] = 'expense';
        $dat[2]['width'] = '18%';
        $dat[3]['title'] = 'gateway fees';
        $dat[3]['width'] = '18%';
        $dat[4]['title'] = 'income';
        $dat[4]['width'] = '18%';
        jrCore_page_table_header($dat);

        foreach ($_rt as $v) {
            if (isset($_mods["{$v['m']}"])) {
                $dat[0]['title'] = ucwords($_mods["{$v['m']}"]['module_name']);
            }
            else {
                $dat[0]['title'] = ucwords($v['m']);
            }
            $dat[0]['class'] = 'center';
            $dat[1]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($v['i']);
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($v['e']);
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($v['f']);
            $dat[3]['class'] = 'center';

            $total           = ($v['i'] - $v['e'] - $v['f']);
            $dat[4]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($total);
            if ($total > 0) {
                $dat[4]['class'] = 'center success';
            }
            elseif (intval($total) === 0) {
                $dat[4]['class'] = 'center';
            }
            else {
                $dat[4]['class'] = 'center error';
            }
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_footer();
    }
    jrCore_page_cancel_button('referrer');
    jrCore_page_display();
}

//-----------------------------------
// payments (master)
//-----------------------------------
function view_jrPayment_payments($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_admin_tabs('jrPayment', 'payments');
    jrCore_page_include_admin_menu();

    $button = jrCore_page_button('new', 'create payment', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/create_register_entry')");
    $button .= jrCore_page_button('all', 'event browser', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/transactions/type=all')");
    jrCore_page_banner('Payment Browser', $button);
    jrCore_get_form_notice();
    jrCore_page_search('search', jrCore_get_current_url());

    // get all purchases
    $tbl = jrCore_db_table_name('jrPayment', 'register');
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $sst = jrCore_db_escape($_post['search_string']);
        $req = "SELECT * FROM {$tbl} WHERE (r_module LIKE '%{$sst}%' OR r_field LIKE '%{$sst}%' OR r_gateway_id LIKE '%{$sst}%' OR r_item_data LIKE '%{$sst}%') ORDER BY r_created DESC";
    }
    else {
        $_ad = array();
        if (isset($_post['profile_id']) && jrCore_checktype($_post['profile_id'], 'number_nz')) {
            $_ad[] = 'r_seller_profile_id = ' . intval($_post['profile_id']);
        }
        if (isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz')) {
            $_ad[] = 'r_purchase_user_id = ' . intval($_post['user_id']);
        }
        if (isset($_post['refunded']) && $_post['refunded'] == '1') {
            $_ad[] = 'r_refunded_time > 0';
        }
        if (count($_ad) > 0) {
            $req = "SELECT * FROM {$tbl} WHERE (" . implode(' AND ', $_ad) . ") ORDER BY r_created DESC";
        }
        else {
            $req = "SELECT * FROM {$tbl} ORDER BY r_created DESC";
        }
    }
    $page = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $page = (int) $_post['p'];
    }
    $_rt = jrCore_db_paged_query($req, $page, 12, 'NUMERIC');

    // Show items
    $dat[1]['title'] = '';
    $dat[1]['width'] = '2%';
    $dat[2]['title'] = 'item';
    $dat[2]['width'] = '40%';
    $dat[3]['title'] = 'user';
    $dat[3]['width'] = '25%';
    $dat[4]['title'] = 'amount';
    $dat[4]['width'] = '10%';
    $dat[5]['title'] = 'date / TXN';
    $dat[5]['width'] = '20%';
    $dat[6]['title'] = 'detail';
    $dat[6]['width'] = '3%';
    jrCore_page_table_header($dat);

    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

        $_id = array();
        foreach ($_rt['_items'] as $k => $_r) {
            if (isset($_r['r_purchase_user_id']) && jrCore_checktype($_r['r_purchase_user_id'], 'number_nz')) {
                $uid       = (int) $_r['r_purchase_user_id'];
                $_id[$uid] = $uid;
            }
        }
        if (count($_id) > 0) {
            $_ui = array(
                'search'                       => array('_item_id in ' . implode(',', $_id)),
                'return_keys'                  => array('_item_id', '_user_id', 'user_name', 'user_email', 'user_image_time', 'profile_url', 'profile_image_time'),
                'include_jrProfile_keys'       => true,
                'exclude_jrProfile_quota_keys' => true,
                'ignore_pending'               => true,
                'privacy_check'                => false,
                'limit'                        => count($_id)
            );
            $_ui = jrCore_db_search_items('jrUser', $_ui);
            if ($_ui && is_array($_ui) && isset($_ui['_items'])) {
                $_id = array();
                foreach ($_ui['_items'] as $_u) {
                    $uid       = (int) $_u['_user_id'];
                    $_id[$uid] = $_u;
                }
            }
        }
        unset($_ui);

        $_rt['_items'] = jrPayment_get_data_for_items($_rt['_items']);
        foreach ($_rt['_items'] as $k => $_r) {

            $pfx = jrCore_db_get_prefix($_r['r_module']);
            $_it = $_r['r_item_data'];
            if (isset($_it['profile_url'])) {
                $ttl         = (isset($_it["{$pfx}_title"])) ? $_it["{$pfx}_title"] : '(unknown)';
                $_im         = array(
                    'crop'   => 'auto',
                    'alt'    => $ttl,
                    'title'  => $ttl,
                    'width'  => 48,
                    'height' => 48,
                    'class'  => 'module_icon payment-icon',
                    '_v'     => (isset($_r['r_item_data']["{$pfx}_image_time"]) && $_r['r_item_data']["{$pfx}_image_time"] > 0) ? $_r['r_item_data']["{$pfx}_image_time"] : $_r['r_created']
                );
                $dat         = array();
                $mod         = (isset($_mods["{$_r['r_module']}"])) ? $_r['r_module'] : 'jrPayment';
                $profile_url = $_conf['jrCore_base_url'] . '/' . $_it['profile_url'];
                $item_url    = $_conf['jrCore_base_url'] . '/' . $_it['profile_url'] . '/' . jrCore_get_module_url($mod) . '/' . $_r['r_item_id'];
                if (isset($_r['r_item_data']["{$pfx}_image_time"]) && $_r['r_item_data']["{$pfx}_image_time"] > 0) {
                    $dat[1]['title'] = jrImage_get_image_src($_r['r_module'], "{$pfx}_image", $_r['r_item_id'], 'small', $_im);
                }
                else {
                    $dat[1]['title'] = jrCore_get_module_icon_html($mod, 48, 'payment-icon');
                }
                $dat[2]['title'] = '<a href="' . $item_url . '">' . $ttl . '</a><br><small>' . $_mods[$mod]['module_name'] . ' &bull; <a href="' . $profile_url . '">@' . $_it['profile_url'] . '</small></a>';
            }
            else {
                $mod             = (isset($_mods["{$_r['r_module']}"])) ? $_r['r_module'] : 'jrPayment';
                $dat[1]['title'] = jrCore_get_module_icon_html($mod, 48, 'payment-icon');
                $dat[2]['title'] = $_mods[$mod]['module_name'];
            }

            $uid = (int) $_r['r_purchase_user_id'];
            if (isset($_id[$uid])) {
                $dat[3]['title'] = "{$_id[$uid]['user_name']}<br><a href=\"{$_conf['jrCore_base_url']}/{$_id[$uid]['profile_url']}\" target=\"_blank\"><small>@{$_id[$uid]['profile_url']}</small></a>";
            }
            else {
                $dat[3]['title'] = jrCore_page_button("tr-assign-{$k}", 'assign', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/txn_assign/{$_r['r_txn_id']}')");
            }
            $dat[3]['class'] = 'center';

            if (isset($_r['r_refunded_time']) && $_r['r_refunded_time'] > 0) {
                $dat[4]['title'] = '<strike>' . jrPayment_get_currency_code() . jrPayment_currency_format($_r['r_amount']) . '</strike>';
                $dat[4]['title'] .= '<br><small><b>refunded</b></small>';
            }
            elseif ($_r['r_field'] == 'transaction' && $_r['r_expense'] > 0 && $_r['r_amount'] == 0) {
                $dat[4]['title'] = '-' . jrPayment_get_currency_code() . jrPayment_currency_format($_r['r_expense']);
            }
            else {
                $dat[4]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_r['r_amount']);
            }
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrCore_format_time($_r['r_created']) . '<br><small>' . $_r['r_gateway_id'] . '</small>';
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = jrCore_page_button("txn-detail-{$k}", 'detail', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/txn_detail/{$_r['r_txn_id']}')");
            $dat[6]['class'] = 'center';

            // Trigger so individual modules can customize their row if needed
            $dat = jrCore_trigger_event('jrPayment', 'payment_entry', $dat, $_r, $_r['r_module']);
            if ($dat && is_array($dat) && count($dat) > 0) {
                jrCore_page_table_row($dat);
            }

        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat = array();
        if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
            $dat[1]['title'] = 'No transactions match your search criteria';
        }
        else {
            $dat[1]['title'] = 'No payments have been recorded yet';
        }
        $dat[1]['class'] = 'center p10';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button('referrer');
    jrCore_page_display();
}

//-----------------------------------
// purchases (user)
//-----------------------------------
function view_jrPayment_purchases($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (jrUser_is_admin() && isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz')) {
        $_us = jrCore_db_get_item('jrUser', $_post['user_id']);
        if (!isset($_us) || !is_array($_us)) {
            if (jrUser_is_master()) {
                jrCore_page_include_admin_menu();
            }
            jrUser_account_tabs('items');
            jrCore_notice_page('error', 'invalid id - please pass in a valid user_id');
        }
        if ($_us['user_name'] != $_user['user_name']) {
            jrCore_set_form_notice('notice', "You are viewing the purchases for the user <strong>{$_us['user_name']}</strong>", false);
        }
    }
    else {
        $_us = $_user;
    }

    if (jrUser_is_admin()) {
        jrUser_account_tabs('purchases', $_us);
    }
    else {
        jrUser_account_tabs('purchases');
    }

    // Get language strings
    $_ln = jrUser_load_lang_strings();

    $button = '';
    // List all items
    if (isset($_post['item']) && strpos($_post['item'], ':')) {
        if (jrUser_is_admin() && isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz')) {
            $button .= jrCore_page_button('all-purchases', $_ln['jrPayment'][55], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/purchases/user_id={$_post['user_id']}')");
        }
        else {
            $button .= jrCore_page_button('all-purchases', $_ln['jrPayment'][55], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/purchases')");
        }
    }
    $button .= jrCore_page_button('view-profile', $_us['profile_name'], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_us['profile_url']}')");
    jrCore_page_banner(15, $button);
    jrCore_get_form_notice();

    // get all purchases for this user
    $tbl = jrCore_db_table_name('jrPayment', 'register');
    if (isset($_post['item']) && strpos($_post['item'], ':')) {
        list($mod, $iid) = explode(':', $_post['item']);
        $iid = (int) $iid;
        $req = "SELECT * FROM {$tbl} WHERE r_purchase_user_id = '{$_us['_user_id']}' AND r_hidden = 0 AND r_module = '" . jrCore_db_escape($mod) . "' AND r_item_id = {$iid} ORDER BY r_id DESC";
    }
    else {
        $req = "SELECT * FROM {$tbl} WHERE r_purchase_user_id = '{$_us['_user_id']}' AND r_hidden = 0 ORDER BY r_id DESC";
    }
    $page = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $page = (int) $_post['p'];
    }
    $_rt = jrCore_db_paged_query($req, $page, 12, 'NUMERIC');

    // Show items
    if (jrUser_is_admin()) {
        $dat[0]['title'] = '<input type="checkbox" class="form_checkbox" onclick="$(\'.register-checkbox\').prop(\'checked\',$(this).prop(\'checked\'));">';
        $dat[0]['width'] = '1%';
        $dat[1]['title'] = '';
        $dat[1]['width'] = '1%';
    }
    else {
        $dat[1]['title'] = '';
        $dat[1]['width'] = '2%';
    }
    $dat[2]['title'] = $_ln['jrPayment'][7];
    $dat[2]['width'] = '68%';
    $dat[3]['title'] = $_ln['jrPayment'][16];
    $dat[3]['width'] = '15%';
    $dat[4]['title'] = $_ln['jrPayment'][9];
    $dat[4]['width'] = '10%';
    $dat[5]['title'] = $_ln['jrPayment'][36];
    $dat[5]['width'] = '5%';
    if (jrUser_is_admin()) {
        $dat[6]['title'] = 'admin';
        $dat[6]['width'] = '5%';
        $dat[2]['width'] = '62%';
    }
    jrCore_page_table_header($dat);

    if ($_rt && is_array($_rt) && isset($_rt['_items']) && is_array($_rt['_items'])) {

        $_rt['_items'] = jrPayment_get_data_for_items($_rt['_items']);

        foreach ($_rt['_items'] as $k => $_r) {
            $dat = array();
            $pfx = jrCore_db_get_prefix($_r['r_module']);
            $_it = $_r['r_item_data'];

            if (jrUser_is_admin()) {
                $dat[0]['title'] = '<input type="checkbox" class="form_checkbox register-checkbox" name="' . $_r['r_id'] . '">';
                $dat[0]['class'] = 'center';
            }

            $_im             = array(
                'crop'   => 'auto',
                'alt'    => $_it["{$pfx}_title"],
                'title'  => $_it["{$pfx}_title"],
                'width'  => 48,
                'height' => 48,
                '_v'     => (isset($_it["{$pfx}_image_time"]) && $_it["{$pfx}_image_time"] > 0) ? $_it["{$pfx}_image_time"] : $_r['r_created']
            );
            $dat[1]['title'] = jrImage_get_image_src($_r['r_module'], "{$pfx}_image", $_r['r_item_id'], 'small', $_im);

            if (isset($_it['profile_url'])) {
                $url             = "{$_conf['jrCore_base_url']}/{$_it['profile_url']}/" . jrCore_get_module_url($_r['r_module']) . "/{$_it['_item_id']}/{$_it["{$pfx}_title_url"]}";
                $dat[2]['title'] = '<a href="' . $url . '">' . $_it["{$pfx}_title"] . '</a><br><a href="' . $_conf['jrCore_base_url'] . '/' . $_it['profile_url'] . '"><small>@' . $_it['profile_url'] . '</small></a>';
            }
            else {
                $dat[2]['title'] = $_it["{$pfx}_title"];
            }
            $dat[3]['title'] = jrCore_format_time($_r['r_created']);
            $dat[3]['class'] = 'center';
            if ($_r['r_refunded_time'] > 0) {
                $dat[4]['title'] = '<strike>' . jrPayment_get_currency_entity($_r['r_currency']) . jrPayment_currency_format($_r['r_amount']) . '</strike>';
                $dat[4]['title'] .= '<br><b><small>' . $_ln['jrPayment'][34] . '</small></b>';
                $dat[5]['title'] = jrCore_page_button("r-download-{$k}", $_ln['jrPayment'][37], 'disabled');
            }
            else {
                $dat[4]['title'] = jrPayment_get_currency_entity($_r['r_currency']) . jrPayment_currency_format($_r['r_amount']);
                $dat[5]['title'] = jrCore_page_button("r-download-{$k}", $_ln['jrPayment'][37], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/download/{$_r['r_id']}')");
            }
            $dat[4]['class'] = 'center';
            $dat[5]['class'] = 'center';
            if (jrUser_is_admin()) {
                if ($_r['r_refunded_time'] > 0) {
                    $dat[6]['title'] = jrCore_page_button("r-refunded-{$k}", 'refunded', 'disabled');
                }
                else {
                    $dat[6]['title'] = jrCore_page_button("r-refunded-{$k}", 'refunded', "jrCore_confirm('Mark as Refunded?','Mark this entry as refunded and block download?', function() { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/refund_register_id/{$_r['r_id']}') })");
                }
                $dat[6]['class'] = 'center';
            }

            // Trigger so individual modules can customize their row if needed
            $dat = jrCore_trigger_event('jrPayment', 'purchase_entry', $dat, $_r, $_r['r_module']);
            if ($dat && is_array($dat) && count($dat) > 0) {
                jrCore_page_table_row($dat);
            }

        }
        if (jrUser_is_admin()) {
            $sjs             = "var v = $('input:checkbox.register-checkbox:checked').map(function(){ return this.name; }).get().join(',')";
            $tmp             = jrCore_page_button("delete", 'delete checked', "jrCore_confirm('Delete Purchases?','The deleted items will be removed and can no longer be downloaded.',function() { {$sjs};jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/register_entry_delete/id='+ v )} )");
            $dat             = array();
            $dat[1]['title'] = $tmp;
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = $_ln['jrPayment'][18];
        $dat[1]['class'] = 'center p10';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_display();
}

//-----------------------------------
// register_entry_delete
//-----------------------------------
function view_jrPayment_register_entry_delete($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();
    if (!isset($_post['id']) || strlen($_post['id']) === 0) {
        jrCore_set_form_notice('error', 'invalid transaction id');
        jrCore_location('referrer');
    }
    if ($_id = explode(',', $_post['id'])) {
        foreach ($_id as $k => $v) {
            if (!jrCore_checktype($v, 'number_nz')) {
                jrCore_set_form_notice('error', 'invalid transaction id (2)');
                jrCore_location('referrer');
            }
            $_id[$k] = (int) $v;
        }
        $tbl = jrCore_db_table_name('jrPayment', 'register');
        $req = "DELETE FROM {$tbl} WHERE r_id IN(" . implode(',', $_id) . ')';
        $cnt = jrCore_db_query($req, 'COUNT');
        if ($cnt && $cnt > 0) {
            jrCore_set_form_notice('success', 'The selected purchases were successfully deleted');
        }
        else {
            jrCore_set_form_notice('error', 'An error was encountered deleting the purchases - please try again');
        }
        jrCore_location('referrer');
    }
    jrCore_set_form_notice('error', 'invalid transaction id (3)');
    jrCore_location('referrer');
}

//-----------------------------------
// refund_register_id
//-----------------------------------
function view_jrPayment_refund_register_id($_post, $_user, $_conf)
{
    jrUser_admin_only();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid transaction id');
        jrCore_location('referrer');
    }
    jrPayment_refund_item_by_id($_post['_1']);
    jrCore_set_form_notice('success', 'The item was successfully marked as refunded and can no longer be downloaded');
    jrCore_location('referrer');
}

//-----------------------------------
// txn_assign
//-----------------------------------
function view_jrPayment_txn_assign($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid transaction id');
        jrCore_location('referrer');
    }
    $_tr = jrCore_db_get_item('jrPayment', $_post['_1'], true);
    if (!$_tr || !is_array($_tr)) {
        jrCore_set_form_notice('error', 'invalid transaction id - data not found');
        jrCore_location('referrer');
    }

    jrCore_page_banner('Assign Transcation to User');

    // Form init
    $_tmp = array(
        'submit_value' => 'assign user to transaction',
        'cancel'       => 'referrer'
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'  => 'txn_id',
        'type'  => 'hidden',
        'value' => $_post['_1']
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'  => 'return_url',
        'type'  => 'hidden',
        'value' => jrCore_get_local_referrer()
    );
    jrCore_form_field_create($_tmp);

    // Show User Picker...
    $_tmp = array(
        'name'          => 'user_id',
        'label'         => 'select user',
        'help'          => 'What User Account should this transaction be assigned to?',
        'type'          => 'live_search',
        'target'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/get_transaction_users",
        'required'      => false,
        'placeholder'   => 'search',
        'validate'      => false,
        'form_designer' => false
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// get_transaction_users
//------------------------------
function view_jrPayment_get_transaction_users($_post, $_user, $_conf)
{
    jrUser_master_only();
    $_sc = array(
        'search'         => array(
            "user_name like {$_post['q']}%"
        ),
        'return_keys'    => array('_user_id', 'user_name'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'privacy_check'  => false,
        'limit'          => 12
    );
    $_rt = jrCore_db_search_items('jrUser', $_sc);
    $_sl = array();
    if ($_rt && is_array($_rt) && is_array($_rt['_items'])) {
        foreach ($_rt['_items'] as $_v) {
            $_sl["{$_v['_user_id']}"] = $_v['user_name'];
        }
    }
    return jrCore_live_search_results('user_id', $_sl);
}

//-----------------------------------
// txn_assign_save
//-----------------------------------
function view_jrPayment_txn_assign_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    if (!isset($_post['txn_id']) || !jrCore_checktype($_post['txn_id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid transaction id');
        jrCore_form_result();
    }
    $tid = (int) $_post['txn_id'];
    if (!isset($_post['user_id']) || !jrCore_checktype($_post['user_id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid user_id');
        jrCore_form_result();
    }
    $uid = (int) $_post['user_id'];
    $_tr = jrCore_db_get_item('jrPayment', $tid, true);
    if (!$_tr || !is_array($_tr)) {
        jrCore_set_form_notice('error', 'invalid transaction id - data not found');
        jrCore_form_result();
    }

    $_up = array(
        'txn_user_id' => $uid
    );
    if (jrCore_db_update_item('jrPayment', $tid, $_up, null, false)) {
        // Update transaction in register
        $tbl = jrCore_db_table_name('jrPayment', 'register');
        $req = "UPDATE {$tbl} SET r_purchase_user_id = {$uid} WHERE r_txn_id = {$tid} LIMIT 1";
        $cnt = jrCore_db_query($req, 'COUNT');
        if ($cnt && $cnt === 1) {
            jrCore_form_delete_session();
            jrCore_location($_post['return_url']);
        }
    }
    jrCore_set_form_notice('error', 'An error was encountered assigning the user_id to the transaction');
    jrCore_form_result();
}

//-----------------------------------
// txn_detail
//-----------------------------------
function view_jrPayment_txn_detail($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_session_require_login();
    jrUser_admin_only();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid transaction id');
        jrCore_location('referrer');
    }
    $_tr = jrCore_db_get_item('jrPayment', $_post['_1'], true);
    if (!$_tr || !is_array($_tr)) {
        jrCore_set_form_notice('error', 'invalid transaction id - data not found');
        jrCore_location('referrer');
    }
    ksort($_tr);
    $url = jrCore_get_local_referrer();
    if (jrUser_is_master() && (strpos($url, '/payments') || strpos($url, '/transactions') || strpos($url, 'modify_register') || strpos($url, 'browser_item_update'))) {
        jrCore_page_admin_tabs('jrPayment', 'payments');
        jrCore_page_include_admin_menu();
        $add = '';
        if (strpos($url, '/p=')) {
            // We're coming from a page beyond one in the transaction browser
            $page = substr($url, strpos($url, '/p=') + 1);
            list(, $page) = explode('=', $page);
            $page = (int) $page;
            if ($page > 1) {
                $add = "/p={$page}";
            }
        }
        $cancel = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/payments{$add}";
    }
    else {
        $cancel = 'referrer';
        if (jrUser_is_admin() && isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz')) {
            $_us = jrCore_db_get_item('jrUser', $_post['user_id']);
            if (!isset($_us) || !is_array($_us)) {
                if (jrUser_is_master()) {
                    jrCore_page_include_admin_menu();
                }
                jrUser_account_tabs('items');
                jrCore_notice_page('error', 'invalid id - please pass in a valid user_id');
            }
            if ($_us['user_name'] != $_user['user_name']) {
                jrCore_set_form_notice('notice', "You are viewing the purchases for the user <strong>{$_us['user_name']}</strong>", false);
            }
        }
        else {
            $_us = $_user;
        }

        if (jrUser_is_admin()) {
            jrUser_account_tabs('purchases', $_us);
        }
        else {
            jrUser_account_tabs('purchases');
        }
    }

    // Get all register entries for this transaction
    $_re = false;
    if (isset($_tr['txn_id']) && strlen($_tr['txn_id']) > 0) {
        $_re = jrPayment_get_register_entries_by_gateway_id($_tr['txn_id']);
    }

    // Get custom transaction buttons
    $_buttons = array(
        jrCore_page_button('delete', 'delete', "jrCore_confirm('Delete Transaction?', 'Do you really want to delete this transaction?', function() { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/txn_delete/{$_tr['_item_id']}') });")
    );
    if (!empty($_tr['txn_expense'])) {
        $_buttons[] = jrCore_page_button('modify', 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/modify_register_entry/{$_tr['_item_id']}')");
    }
    elseif (jrUser_is_master()) {
        $_buttons[] = jrCore_page_button('modify', 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/browser_item_update/id={$_tr['_item_id']}')");
    }

    if (isset($_tr['txn_plugin'])) {
        jrPayment_set_active_plugin($_tr['txn_plugin']);
        if ($url = jrPayment_run_plugin_function('get_transaction_url', $_tr)) {
            $_buttons[] = jrCore_page_button('view', 'view @ ' . $_tr['txn_plugin'], "window.open('{$url}')");
        }
        $_buttons[] = jrCore_page_button('raw', 'raw transaction detail', "popwin('{$_conf['jrCore_base_url']}/{$_post['module_url']}/raw_transaction_detail/{$_tr['_item_id']}','debug',900,600,'yes');");
        if ($_re && count($_re) > 0) {
            if ($_tmp = jrPayment_run_plugin_function('txn_detail_buttons', $_tr, $_re)) {
                if (is_array($_tmp)) {
                    $_buttons = array_merge($_tmp, $_buttons);
                }
            }
        }
    }
    jrPayment_set_active_plugin($_conf['jrPayment_plugin']);

    // Show raw Transaction
    jrCore_page_banner('transaction detail', implode('', $_buttons));

    if (isset($_tr['txn_user_id']) && jrCore_checktype($_tr['txn_user_id'], 'number_nz')) {

        $dat             = array();
        $dat[1]['title'] = 'User';
        $dat[1]['width'] = '10%';
        $dat[2]['title'] = 'Details';
        $dat[2]['width'] = '90%';

        jrCore_page_table_header($dat);
        $uid = (int) $_tr['txn_user_id'];
        $_us = jrCore_db_get_item('jrUser', $uid);

        $dat             = array();
        $_im             = array(
            'crop'   => 'auto',
            'alt'    => (isset($_us['user_name'])) ? $_us['user_name'] : 'user image',
            'title'  => (isset($_us['user_name'])) ? $_us['user_name'] : 'user image',
            'width'  => 160,
            'height' => 160,
            '_v'     => (isset($_us['user_image_time']) && $_us['user_image_time'] > 0) ? $_us['user_image_time'] : $_us['_created']
        );
        $dat[1]['title'] = jrImage_get_image_src('jrUser', "user_image", $uid, 'large', $_im);
        if (count($_us) > 0) {
            $num = '';
            if (isset($_re) && is_array($_re)) {
                $num = '<br>' . count($_re) . ' items';
            }
            $dat[2]['title'] = $_us['user_name'] . "<br><a href=\"{$_conf['jrCore_base_url']}/{$_us['profile_url']}\">@{$_us['profile_url']}</a><br>" . jrCore_format_time($_re[0]['r_created']) . $num;
        }
        else {
            $dat[2]['title'] = '(visitor)';
        }
        $dat[2]['class'] = 'p10';
        jrCore_page_table_row($dat);

        $dat             = array();
        $dat[1]['title'] = 'Key';
        $dat[2]['title'] = 'Value';
        jrCore_page_table_header($dat, null, true);

    }
    else {

        $dat             = array();
        $dat[1]['title'] = 'Key';
        $dat[1]['width'] = '10%';
        $dat[2]['title'] = 'Value';
        $dat[2]['width'] = '90%';
        jrCore_page_table_header($dat);

    }

    foreach ($_tr as $k => $v) {
        $dat             = array();
        $dat[1]['title'] = $k;
        switch ($k) {
            case '_item_id':
            case '_user_id':
            case '_profile_id':
            case 'txn_raw':
            case 'txn_note':   // We handle this one separately below
                continue 2;
            case '_created':
            case '_updated':
            case 'txn_date':
                $dat[2]['title'] = jrCore_format_time($v);
                break;
            case 'txn_gateway_fee':
            case 'txn_shipping':
            case 'txn_total':
            case 'txn_amount':
            case 'txn_expense':
            case 'txn_fee':
            case 'txn_tax':
            case 'txn_charge':
                $dat[2]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($v);
                break;
            default:
                $dat[2]['title'] = $v;
                break;
        }
        jrCore_page_table_row($dat);
    }
    if (!empty($_tr['txn_note'])) {
        $dat[1]['title'] = 'txn_note';
        $dat[2]['title'] = nl2br(trim($_tr['txn_note']));
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    if ($_tr['txn_type'] != 'information' && $_re && is_array($_re)) {

        // Show individual items
        $dat[1]['title'] = '';
        $dat[1]['width'] = '2%';
        $dat[2]['title'] = 'item';
        $dat[2]['width'] = '68%';
        $dat[3]['title'] = 'date';
        $dat[3]['width'] = '15%';
        $dat[4]['title'] = 'amount';
        $dat[4]['width'] = '15%';
        jrCore_page_table_header($dat);

        $_re = jrPayment_get_data_for_items($_re);

        foreach ($_re as $k => $_r) {

            $_it = $_r['r_item_data'];
            $pfx = jrCore_db_get_prefix($_r['r_module']);
            $dat = array();
            if ($_r['r_module'] != 'jrPayment') {
                $_im             = array(
                    'crop'   => 'auto',
                    'alt'    => $_it["{$pfx}_title"],
                    'title'  => $_it["{$pfx}_title"],
                    'width'  => 48,
                    'height' => 48,
                    '_v'     => (isset($_it["{$pfx}_image_time"]) && $_it["{$pfx}_image_time"] > 0) ? $_it["{$pfx}_image_time"] : $_r['r_created']
                );
                $dat[1]['title'] = jrImage_get_image_src($_r['r_module'], "{$pfx}_image", $_r['r_item_id'], 'icon', $_im);
            }
            else {
                $dat[1]['title'] = jrCore_get_module_icon_html('jrPayment', 48);
            }
            $dat[2]['title'] = '<a href="' . $_conf['jrCore_base_url'] . '/' . $_it['profile_url'] . '/' . jrCore_get_module_url($_r['r_module']) . '">' . $_mods["{$_r['r_module']}"]['module_name'] . '</a> - ' . $_it["{$pfx}_title"] . '<br><small><a href="' . $_conf['jrCore_base_url'] . '/' . $_it['profile_url'] . '">@' . $_it['profile_url'] . '</a></small>';
            $dat[3]['title'] = jrCore_format_time($_r['r_created']);
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = jrPayment_get_currency_entity($_r['r_currency']) . jrPayment_currency_format($_r['r_amount']);
            if (isset($_r['r_refunded']) && $_r['r_refunded'] == 1) {
                $dat[4]['title'] .= '<br><small>refunded</small>';
                $dat[4]['class'] = 'error center';
            }
            else {
                $dat[4]['class'] = 'center';
            }

            // Trigger so individual modules can customize their row if needed
            $dat = jrCore_trigger_event('jrPayment', 'txn_detail_entry', $dat, array_merge($_tr, $_r), $_r['r_module']);
            if ($dat && is_array($dat) && count($dat) > 0) {
                jrCore_page_table_row($dat);
            }
        }
        jrCore_page_table_footer();
    }

    jrCore_page_cancel_button($cancel);
    jrCore_page_display();
}

//-----------------------------------
// txn_delete
//-----------------------------------
function view_jrPayment_txn_delete($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid transaction id');
        jrCore_location('referrer');
    }

    // Delete the transaction from the datastore
    $tid = (int) $_post['_1'];
    jrCore_db_delete_item('jrPayment', $tid);

    // Delete the entry from the register
    $tbl = jrCore_db_table_name('jrPayment', 'register');
    $req = "DELETE FROM {$tbl} WHERE r_txn_id = {$tid} LIMIT 1";
    jrCore_db_query($req);

    jrCore_set_form_notice('success', "The transaction was successfully deleted");
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/payments");
}

//-----------------------------------
// download
//-----------------------------------
function view_jrPayment_download($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid register id');
        jrCore_location('referrer');
    }

    // Make sure this user has purchased this vault file
    $_rg = jrPayment_get_register_entry_by_id($_post['_1']);
    if (!jrUser_is_admin() && (!$_rg || !is_array($_rg) || $_rg['r_purchase_user_id'] != $_user['_user_id'])) {
        jrCore_notice('error', 'It does not appear you have purchased this file - exiting');
    }

    // Has this item been refunded?
    if ($_rg['r_refunded_time'] > 0) {
        jrCore_notice('error', 'It does not appear you have purchased this file - exiting');
    }

    $alt = false;
    $_it = jrCore_db_get_item($_rg['r_module'], $_rg['r_item_id']);
    if (!$_it || !is_array($_it)) {
        // Looks like this item may have been deleted - let's see if
        // we have saved this item to the system vault
        if (!$_it = jrPayment_get_vault_item($_rg['r_module'], $_rg['r_item_id'])) {
            jrCore_notice('error', 'file not found');
        }
        $alt = true;
    }

    // When we get a VAULT download, we're going to be sending the
    // user the ORIGINAL file - not the down sampled copy.
    $fld = $_rg['r_field'];
    if (isset($_it["{$fld}_original_extension"])) {
        // jrVideo_38_video_file.mov.original.mov
        $ext = $_it["{$fld}_original_extension"];
        $nam = "{$_rg['r_module']}_{$_rg['r_item_id']}_{$fld}.{$ext}.original.{$ext}";
    }
    else {
        // We don't have an "original" - i.e. no conversion was done
        $nam = "{$_rg['r_module']}_{$_rg['r_item_id']}_{$fld}." . $_it["{$fld}_extension"];
    }

    if ($alt) {
        // This is a deleted file that has been saved in the vault
        $nam = "jrPayment_vault_{$_rg['r_module']}_{$_rg['r_item_id']}_{$fld}." . $_it["{$fld}_extension"];
    }

    $pfx = jrCore_db_get_prefix($_rg['r_module']);
    if (isset($_it["{$pfx}_title_url"])) {
        $ttl = $_it["{$pfx}_title_url"];
    }
    elseif (isset($_it["{$pfx}_title"])) {
        $ttl = jrCore_url_string($_it["{$pfx}_title_url"]);
    }
    elseif (isset($_it["{$fld}_original_name"])) {
        $ttl = jrCore_url_string($_it["{$fld}_original_name"]);
    }
    elseif (isset($_it["{$fld}_name"])) {
        $ttl = jrCore_url_string($_it["{$fld}_name"]);
    }
    else {
        $ttl = $nam;
    }
    $ttl = pathinfo($ttl, PATHINFO_FILENAME) . '.' . $_it["{$fld}_extension"];

    // "vault_download" event trigger
    $_args = array(
        'module'     => $_rg['r_module'],
        'file_name'  => $fld,
        'vault_file' => $nam,
        'vault_name' => $ttl,
        '_register'  => $_rg
    );
    $_it   = jrCore_trigger_event('jrPayment', 'vault_download', $_it, $_args);
    if (isset($_it['vault_file'])) {
        $nam = $_it['vault_file'];
    }
    if (isset($_it['vault_name'])) {
        $ttl = $_it['vault_name'];
    }
    if ($alt) {
        if (!jrCore_media_file_exists('system', $nam)) {
            jrCore_notice('CRI', 'Invalid media id - no file found: ' . $nam);
        }
    }

    // Increment our counter
    jrCore_db_increment_key($_rg['r_module'], $_it['_item_id'], "{$fld}_vault_download_count", 1);

    // Download the file to the client
    if ($alt) {
        jrCore_media_file_download('system', $nam, $ttl);
    }
    else {
        jrCore_media_file_download($_it['_profile_id'], $nam, $ttl);
    }
    session_write_close();
    exit();
}

//------------------------------
// raw_transaction_detail
//------------------------------
function view_jrPayment_raw_transaction_detail($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_set_meta_header_only();
    $button = jrCore_page_button('close', 'close', 'self.close();');
    jrCore_page_banner("raw transaction detail", $button);
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('error', 'invalid transaction id');
    }
    $_rt = jrCore_db_get_item('jrPayment', $_post['_1']);
    if (!$_rt || !is_array($_rt)) {
        jrCore_notice_page('error', 'invalid transaction id - not found in db');
    }
    if (isset($_rt['txn_raw'])) {
        $_rt = json_decode($_rt['txn_raw'], true);
    }

    $dat             = array();
    $dat[1]['title'] = 'Value';
    $dat[1]['width'] = '100%';
    jrCore_page_table_header($dat);

    $dat             = array();
    $dat[1]['title'] = '<div class="fixed-width">' . str_replace(',', ', ', jrCore_entity_string(print_r($_rt, true))) . '</div>';
    jrCore_page_table_row($dat);
    jrCore_page_table_footer();
    jrCore_page_close_button();
    jrCore_page_display();
}

//-----------------------------------
// transactions (master)
//-----------------------------------
function view_jrPayment_transactions($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_admin_tabs('jrPayment');
    jrCore_page_include_admin_menu();

    $url = jrCore_strip_url_params(jrCore_get_current_url(), array('type', 'p'));
    $btn = '<select name="payment_type" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $url . "/type='+ $(this).val())\">\n";
    if (isset($_post['type']) && $_post['type'] == 'all') {
        $btn .= "<option value=\"payments\"> balance affecting events</option>\n";
        $btn .= "<option value=\"all\" selected=\"selected\"> all events</option>\n";
    }
    else {
        $btn .= "<option value=\"payments\" selected=\"selected\"> balance affecting events</option>\n";
        $btn .= "<option value=\"all\"> all events</option>\n";
    }
    $btn .= '</select>';
    jrCore_page_banner('Event Browser', $btn);

    jrCore_get_form_notice();
    jrCore_page_search('search', jrCore_get_current_url());

    // Show items
    $dat[1]['title'] = '';
    $dat[1]['width'] = '2%';
    $dat[2]['title'] = 'event';
    $dat[2]['width'] = '50%';
    $dat[3]['title'] = 'type';
    $dat[3]['width'] = '10%';
    $dat[4]['title'] = 'amount';
    $dat[4]['width'] = '8%';
    $dat[5]['title'] = 'date / ID';
    $dat[5]['width'] = '22%';
    $dat[6]['title'] = 'detail';
    $dat[6]['width'] = '4%';
    $dat[7]['title'] = 'TX';
    $dat[7]['width'] = '4%';
    jrCore_page_table_header($dat);

    $page = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $page = (int) $_post['p'];
    }

    $_rt = array(
        'order_by'      => array('_created' => 'desc'),
        'skip_triggers' => true,
        'page'          => $page,
        'pagebreak'     => (isset($_COOKIE['jrcore_pager_rows']) && jrCore_checktype($_COOKIE['jrcore_pager_rows'], 'number_nz')) ? (int) $_COOKIE['jrcore_pager_rows'] : 12,
        'no_cache'      => true
    );
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_rt['search'] = array(
            "txn_% like %{$_post['search_string']}%"
        );
    }
    if (!isset($_post['type']) || $_post['type'] != 'all') {
        if (!isset($_rt['search'])) {
            $_rt['search'] = array();
        }
        $_rt['search'][] = 'txn_total >= 0';
    }
    $_rt = jrCore_db_search_items('jrPayment', $_rt);
    if ($_rt && is_array($_rt) && isset($_rt['_items']) && is_array($_rt['_items'])) {

        foreach ($_rt['_items'] as $k => $_t) {

            if (!isset($_t['txn_plugin'])) {
                $_t['txn_plugin'] = jrPayment_get_active_plugin();
            }

            $dat[1]['title'] = "<img src=\"{$_conf['jrCore_base_url']}/modules/jrPayment/img/{$_t['txn_plugin']}.png\" width=\"40\" height=\"40\" alt=\"system\">";
            if (isset($_t['txn_raw'])) {
                $_t['txn_raw'] = json_decode($_t['txn_raw'], true);
            }
            $dat[2]['title'] = $_t['txn_plugin'] . '<br>';
            jrPayment_set_active_plugin($_t['txn_plugin']);
            if ($title = jrPayment_run_plugin_function('webhook_transaction_title', $_t)) {
                $dat[2]['title'] .= '<small>' . $title . '</small>';
            }
            jrPayment_set_active_plugin($_conf['jrPayment_plugin']);

            $cur = jrPayment_get_currency_code();
            if (isset($_t['txn_currency'])) {
                $cur = jrPayment_get_currency_entity($_t['txn_currency']);
            }

            $dat[3]['title'] = $_t['txn_type'];
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = (isset($_t['txn_total'])) ? $cur . jrPayment_currency_format($_t['txn_total']) : '&nbsp;';
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrCore_format_time($_t['_created']);
            if (isset($_t['txn_id']) && strlen($_t['txn_id']) > 0) {
                $dat[5]['title'] .= '<br><small>' . $_t['txn_id'] . '</small>';
            }
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = jrCore_page_button("txn-detail-{$k}", 'detail', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/txn_detail/{$_t['_item_id']}')");
            $dat[6]['class'] = 'center';
            $dat[7]['title'] = jrCore_page_button("txn-raw-{$k}", 'TX', "popwin('{$_conf['jrCore_base_url']}/{$_post['module_url']}/raw_transaction_detail/{$_t['_item_id']}','debug',900,600,'yes');");
            $dat[7]['class'] = 'center';

            $dat = jrCore_trigger_event('jrPayment', 'txn_entry', $dat, $_t);
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = 'no transactions have been recorded yet';
        $dat[1]['class'] = 'center p10';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button('referrer');
    jrCore_page_display();
}

//-----------------------------------
// transaction (for user)
//-----------------------------------
function view_jrPayment_transaction($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid transaction id');
        jrCore_location('referrer');
    }
    if (!$_tr = jrPayment_get_transaction_by_id($_post['_1'])) {
        jrCore_set_form_notice('error', 'invalid transaction id');
        jrCore_location('referrer');
    }
    if (!jrUser_is_admin() && $_tr['r_seller_profile_id'] != $_user['user_active_profile_id']) {
        jrUser_not_authorized();
    }
    $_ln = jrUser_load_lang_strings();

    jrCore_page_banner(22);
    jrCore_get_form_notice();

    $dat             = array();
    $dat[1]['title'] = $_ln['jrPayment'][38];
    $dat[1]['width'] = '20%';
    $dat[2]['title'] = $_ln['jrPayment'][39];
    $dat[2]['width'] = '80%';
    jrCore_page_table_header($dat);

    foreach ($_tr as $k => $v) {
        if (strpos($k, '_') !== 0) {
            switch ($k) {
                case 'txn_raw':
                case 'txn_cart_id':
                case 'txn_cart_hash':
                    break;
                default:
                    if (!jrUser_is_admin() && strpos(' ' . $k, 'email')) {
                        continue 2;
                    }
                    $dat[1]['title'] = $k;
                    if ((strpos($k, 'date') || strpos($k, 'time')) && jrCore_checktype($v, 'number_nz')) {
                        $dat[2]['title'] = jrCore_format_time($v);
                    }
                    else {
                        $dat[2]['title'] = $v;
                    }
                    jrCore_page_table_row($dat);
            }
        }
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button('referrer');
    jrCore_page_display();
}

//------------------------------
// cart
//------------------------------
function view_jrPayment_cart($_post, $_user, $_conf)
{
    $_ln = jrUser_load_lang_strings();
    $_cr = jrPayment_get_user_cart();

    jrCore_page_set_no_header_or_footer();

    $button = jrCore_page_button('continue', $_ln['jrPayment'][5], 'jrPayment_close_cart()');
    jrCore_page_banner($_ln['jrPayment'][6], $button);
    jrCore_get_form_notice();

    $dat             = array();
    $dat[1]['title'] = '&nbsp;';
    $dat[1]['width'] = '5%';
    $dat[2]['title'] = $_ln['jrPayment'][7];
    $dat[2]['width'] = '61%';
    $dat[3]['title'] = $_ln['jrPayment'][8];
    $dat[3]['width'] = '17%';
    $dat[4]['title'] = $_ln['jrPayment'][9];
    $dat[4]['width'] = '17%';
    jrCore_page_table_header($dat);

    if ($_cr && is_array($_cr) && isset($_cr['_items'])) {

        // Do we have modules that need quantity support?
        $_qs = jrCore_get_registered_module_features('jrPayment', 'quantity_support');

        // Save cart validation - will be used in webhook to validate cart content
        $md5 = jrPayment_save_cart_validation($_cr);

        $k   = 0;
        $tot = 0;
        $shp = 0;
        $sym = jrPayment_get_currency_code();
        foreach ($_cr['_items'] as $eid => $_i) {

            $pfx = jrCore_db_get_prefix($_i['cart_module']);
            $fld = str_replace('_', ' ', str_replace('_item_price', '', $_i['cart_field']));
            $dat = array();
            if (isset($_i["{$pfx}_image_time"])) {
                $_im             = array(
                    'crop'   => 'auto',
                    'width'  => 40,
                    'height' => 40,
                    'alt'    => 'img',
                    'title'  => 'img',
                    'class'  => 'module_icon payment-icon',
                    '_v'     => (isset($_i["{$pfx}_image_time"]) && $_i["{$pfx}_image_time"] > 0) ? $_i["{$pfx}_image_time"] : 1
                );
                $dat[1]['title'] = jrImage_get_image_src($_i['cart_module'], "{$pfx}_image", $_i['cart_item_id'], 'icon', $_im);
            }
            else {
                $dat[1]['title'] = jrCore_get_module_icon_html($_i['cart_module'], 40, 'payment-icon');
            }
            $dat[2]['title'] = $_i["{$pfx}_title"];
            if (isset($_i['profile_url'])) {
                $dat[2]['title'] .= '<br><small><a href="' . $_conf['jrCore_base_url'] . '/' . $_i['profile_url'] . '">@' . $_i['profile_url'] . '</a> &bull; ' . $fld . '</small>';
            }
            $dat[2]['class'] = 'p5';

            if (isset($_qs["{$_i['cart_module']}"]) && $_qs["{$_i['cart_module']}"] == true) {
                $dat[3]['title'] = '<input id="q' . $eid . '" type="text" value="' . $_i['cart_quantity'] . '" class="cart-qty" data-eid="' . $eid . '" onkeypress="if (event && event.keyCode == 13 && this.value.length > 0) { jrPayment_update_quantity(' . $eid . '); }">';
                $dat[3]['title'] .= '<br><input type="button" class="cart-remove-button" value="update" onclick="jrPayment_update_quantity(' . $eid . ')">';
            }
            else {
                $dat[3]['title'] = $_i['cart_quantity'];
                $dat[3]['title'] .= '<br><input type="button" class="cart-remove-button" value="remove" onclick="jrPayment_remove_item(\'' . $eid . '\')">';
            }
            $dat[3]['class'] = 'center';

            $tot             += ($_i['cart_amount'] * $_i['cart_quantity']);
            $dat[4]['title'] = $sym . '<span id="entry-total-' . $eid . '">' . jrPayment_currency_format($_i['cart_amount'] * $_i['cart_quantity']) . '</span>';
            $dat[4]['class'] = 'center';

            // Get shipping
            // allow for 3rd party S&H events
            $ish = 0;
            $tmp = jrCore_trigger_event('jrPayment', 'cart_shipping_alt', $dat, $_i);
            if ($tmp && jrCore_checktype($tmp, 'number_nz')) {
                $shp += (int) $tmp;
                $ish = (int) $tmp;
            }
            else {
                // Trigger individual module
                $tmp = jrCore_trigger_event('jrPayment', 'cart_shipping', $dat, $_i, $_i['cart_module']);
                if ($tmp && jrCore_checktype($tmp, 'number_nz')) {
                    $shp += (int) ($_i['cart_quantity'] * $tmp);
                    $ish = (int) $tmp;
                }
            }
            if ($ish > 0) {
                $dat[4]['title'] .= "<br><span class=\"cart-shipping\">+{$sym}" . jrPayment_currency_format($_i['cart_quantity'] * $ish) . ' ' . $_ln['jrPayment'][10] . '</span>';
            }
            if ($shp > 0) {
                $tot += $shp;
            }

            // Get entry
            $dat = jrCore_trigger_event('jrPayment', 'cart_entry', $dat, $_i, $_i['cart_module']);
            jrCore_page_table_row($dat, 'cart-item-row');
            $k++;
        }

        // Service Charge
        if (isset($_conf['jrPayment_cart_charge']) && $_conf['jrPayment_cart_charge'] > 0) {
            $dat             = array();
            $dat[1]['title'] = '&nbsp;';
            $dat[1]['class'] = '" style="text-align:right" colspan="2';
            $dat[2]['title'] = "{$_ln['jrPayment'][35]}";
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $sym . '<span id="cart-charge-total">' . jrPayment_currency_format($_conf['jrPayment_cart_charge']) . '</span>';
            $dat[3]['class'] = 'center p5';
            jrCore_page_table_row($dat);
            $tot += ($_conf['jrPayment_cart_charge'] * 100);
        }

        $dat             = array();
        $dat[1]['title'] = '&nbsp;';
        $dat[1]['class'] = '" style="text-align:right" colspan="2';
        $dat[2]['title'] = $_ln['jrPayment'][11];
        $dat[2]['class'] = 'center';
        $dat[3]['title'] = "<b>{$sym}" . '<span id="cart-checkout-total">' . jrPayment_currency_format($tot) . '</span></b>';
        $dat[3]['class'] = 'center p5';
        jrCore_page_table_row($dat);

        if (!jrUser_is_logged_in()) {
            $onc = 'jrPayment_checkout_login()';
        }
        else {
            // Our plugin can give us a DIRECT URL (so we don't redirect)
            if (!$onc = jrPayment_run_plugin_function('checkout_onclick', $md5, $tot, $_cr)) {
                $onc = "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/checkout/{$md5}')";
            }
        }

        $dat             = array();
        $dat[1]['title'] = '&nbsp';
        if (isset($_conf['jrPayment_show_clear']) && $_conf['jrPayment_show_clear'] == 'on') {
            $dat[1]['title'] = jrCore_page_button('reset', $_ln['jrPayment'][26], 'jrPayment_reset_cart()');
        }
        $dat[1]['class'] = 'left" colspan="3';
        $dat[2]['title'] = jrCore_page_button('checkout', $_ln['jrPayment'][12], $onc, array('class' => 'form_checkout_button form_button'));
        $dat[2]['class'] = 'p10 center jrpayment_checkout_cell';
        if (jrPayment_get_active_plugin() != 'paypal' && jrUser_is_logged_in() && isset($_conf['jrPayment_show_paypal']) && $_conf['jrPayment_show_paypal'] == 'on') {
            jrPayment_set_active_plugin('paypal');
            if ($pnc = jrPayment_run_plugin_function('checkout_onclick', $md5, $tot, $_cr)) {
                $dat[2]['title'] .= "<br><span>{$_ln['jrPayment'][56]}</span><br><img src=\"{$_conf['jrCore_base_url']}/image/img/module/jrPayment/paypal_co.png\" onclick=\"{$pnc}\">";
            }
            jrPayment_set_active_plugin($_conf['jrPayment_plugin']);
        }
        $dat = jrCore_trigger_event('jrPayment', 'cart_checkout_row', $dat, $_cr);
        jrCore_page_table_row($dat, 'payment-checkout-cell');

    }
    else {
        $dat             = array();
        $dat[1]['title'] = $_ln['jrPayment'][13];
        $dat[1]['class'] = 'center p10';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    // Do we have any cart javascript elements?
    $js = jrPayment_run_plugin_function('cart_elements');
    if (!$js) {
        $js = '';
    }

    return '<div id="cart-holder">' . jrCore_page_display(true) . $js . '</div>';
}

//------------------------------
// cart_reset
//------------------------------
function view_jrPayment_cart_reset($_post, $_user, $_conf)
{
    jrCore_validate_location_url();
    $_cr = jrPayment_get_user_cart();
    if ($_cr && is_array($_cr)) {
        jrPayment_reset_cart($_cr['cart_id']);
    }
    if (jrCore_is_ajax_request()) {
        $_rs = array('ok' => 1);
        jrCore_json_response($_rs);
    }
    jrCore_location('referrer');
}

//------------------------------
// remove_item_from_cart
//------------------------------
function view_jrPayment_remove_item_from_cart($_post, $_user, $_conf)
{
    jrCore_validate_location_url();
    $_cr = jrPayment_get_user_cart();
    if ($_cr && is_array($_cr)) {
        if (jrPayment_remove_item_from_cart($_cr['cart_id'], $_post['id'])) {
            $_rs = array('ok' => 1);
            jrCore_json_response($_rs);
        }
        $_rs = array('error' => 'invalid cart entry id');
        jrCore_json_response($_rs);
    }
    $_rs = array('error' => 'invalid cart');
    jrCore_json_response($_rs);
}

//------------------------------
// add_item_to_cart
//------------------------------
function view_jrPayment_add_item_to_cart($_post, $_user, $_conf)
{
    $_cr = jrPayment_get_user_cart();
    if ($_cr && is_array($_cr)) {
        $cid = (int) $_cr['cart_id'];
    }
    else {
        $cid = jrPayment_create_user_cart();
    }
    if (!$cid || !jrCore_checktype($cid, 'number_nz')) {
        $_rs = array('error' => 'invalid cart id');
        jrCore_json_response($_rs);
    }
    $eid = jrPayment_add_item_to_cart($cid, $_post['cart_module'], $_post['cart_item_id'], $_post['cart_field']);
    if ($eid && jrCore_checktype($eid, 'number_nz')) {
        $num = jrPayment_get_user_cart_item_count();
        $_rs = array('entry_id' => $eid, 'item_count' => $num);
    }
    else {
        $_rs = array('error' => $eid);
    }
    jrCore_json_response($_rs);
}

//------------------------------
// checkout
//------------------------------
function view_jrPayment_checkout($_post, $_user, $_conf)
{
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'md5')) {
        jrCore_notice_page('error', 'invalid checkout id - please try again');
    }
    $_cr = jrPayment_get_user_cart();
    if ($_cr && is_array($_cr) && isset($_cr['_items'])) {

        if (!isset($_cr['cart_hash']) || $_cr['cart_hash'] != $_post['_1']) {
            jrCore_notice_page('error', 'invalid cart validation - please try again');
        }

        $amt = 0;
        foreach ($_cr['_items'] as $k => $_i) {
            $amt += ($_i['cart_amount'] * $_i['cart_quantity']);
        }
        if (isset($_conf['jrPayment_cart_charge']) && $_conf['jrPayment_cart_charge'] > 0) {
            $amt += intval($_conf['jrPayment_cart_charge'] * 100);
        }
        $plg = null;
        if (isset($_post['_2']) && strlen($_post['_2']) > 0 && jrPayment_is_valid_plugin($_post['_2'])) {
            $plg = $_post['_2'];
        }
        $url = jrPayment_get_checkout_url($amt, $_cr, $plg);
        if ($url && jrCore_checktype($url, 'url')) {
            $_ur = array(
                'checkout_url' => $url
            );
            $_ur = jrCore_trigger_event('jrPayment', 'cart_checkout', $_ur, $_cr);
            if (isset($_ur['checkout_url']) && jrCore_checktype($_ur['checkout_url'], 'url')) {
                jrCore_location($url);
            }
            jrCore_notice_page('error', 'invalid checkout URL:<br>' . $url, null, null, false);
        }
        jrCore_notice_page('error', 'error building checkout URL for payment processor - please try again');
    }
    jrCore_notice_page('error', 'no cart items found to checkout with - please try again');
}

//------------------------------
// checkout_login
//------------------------------
function view_jrPayment_checkout_login($_post, $_user, $_conf)
{
    jrCore_validate_location_url();
    $url = jrCore_get_local_referrer();
    if (isset($_post['url']) && strlen($_post['url']) > 0) {
        $url = $_post['url'];
    }
    jrCore_set_cookie('checkout_login', $url, 1);
    jrCore_create_memory_url('checkout_login', $url);
    $url = jrCore_get_module_url('jrUser');
    $_rs = array(
        'url' => "{$_conf['jrCore_base_url']}/{$url}/login/r=checkout_login"
    );
    jrCore_json_response($_rs);
}

//------------------------------
// plugin_browser
//------------------------------
function view_jrPayment_plugin_browser($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_admin_tabs('jrPayment');
    jrCore_page_include_admin_menu();
    jrCore_page_banner('Payment Processor Plugins');
    jrCore_get_form_notice();

    $dat             = array();
    $dat[1]['title'] = 'icon';
    $dat[1]['width'] = '1%';
    $dat[2]['title'] = 'payment processor';
    $dat[2]['width'] = '87%';
    $dat[3]['title'] = 'active';
    $dat[3]['width'] = '8%';
    $dat[4]['title'] = 'settings';
    $dat[4]['width'] = '4%';
    jrCore_page_table_header($dat);

    $_pl = jrPayment_get_plugins();
    $act = jrCore_get_option_image('pass');
    $ina = jrCore_get_option_image('fail');
    $url = jrCore_get_module_url('jrImage');
    foreach ($_pl as $plug => $title) {
        $_mt             = jrPayment_get_plugin_meta_data($plug);
        $dat             = array();
        $dat[1]['title'] = "<img src=\"{$_conf['jrCore_base_url']}/{$url}/img/module/jrPayment/{$plug}.png\" width=\"72\" height=\"72\" title=\"{$title}\">";
        $dat[1]['class'] = 'p10 center';
        $dat[2]['title'] = '<h2>' . $title . '</h2>';
        if (isset($_mt['description'])) {
            $dat[2]['title'] .= '<br>' . $_mt['description'];
        }
        if (isset($_mt['url'])) {
            $dat[2]['title'] .= '<br><a href="' . $_mt['url'] . '" target="_blank">' . $_mt['url'] . '</a>';
        }

        $dat[2]['class'] = 'p10';
        if (isset($_conf['jrPayment_plugin']) && $_conf['jrPayment_plugin'] == $plug) {
            $dat[3]['title'] = $act;
        }
        elseif ($plug == 'paypal' && isset($_conf['jrPayment_show_paypal']) && $_conf['jrPayment_show_paypal'] == 'on') {
            $dat[3]['title'] = $act;
        }
        else {
            $dat[3]['title'] = $ina;
        }
        $dat[3]['class'] = 'center';
        $dat[4]['title'] = jrCore_page_button("c{$plug}", 'settings', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/plugin_config/{$plug}')");
        $dat[4]['class'] = 'p10';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button('referrer');
    jrCore_page_display();
}

//------------------------------
// plugin_config
//------------------------------
function view_jrPayment_plugin_config($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_admin_tabs('jrPayment');
    jrCore_page_include_admin_menu();
    jrCore_validate_location_url();
    if (!isset($_post['_1']) || !jrPayment_is_valid_plugin($_post['_1'])) {
        jrCore_notice_page('error', 'invalid plugin');
    }
    // Get config as provided by plugin
    $plug = $_post['_1'];
    $func = "jrPayment_plugin_{$plug}_config";
    if (!function_exists($func)) {
        require_once APP_DIR . "/modules/jrPayment/plugins/{$plug}.php";
    }
    if (!function_exists($func)) {
        jrCore_notice_page('error', 'no plugin config found');
    }
    $_mta = jrPayment_get_plugin_meta_data($plug);

    // Config form
    $button = jrCore_page_button('admin', "{$plug} admin", "window.open('{$_mta['admin']}')");
    jrCore_page_banner("{$_mta['title']} config", $button);

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/plugin_browser",
        'values'       => jrPayment_get_plugin_config($plug)
    );
    jrCore_form_create($_tmp);

    // Plugin
    $_tmp = array(
        'name'     => 'plugin',
        'type'     => 'hidden',
        'validate' => 'printable',
        'value'    => $plug
    );
    jrCore_form_field_create($_tmp);

    // Brings in config form fields for plugin
    $func($_post);
    jrCore_page_display();
}

//------------------------------
// plugin_activate
//------------------------------
function view_jrPayment_plugin_activate($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (jrPayment_is_valid_plugin($_post['_1'])) {
        jrCore_set_setting_value('jrPayment', 'plugin', $_post['_1']);
        jrCore_delete_config_cache();
    }
    else {
        jrCore_set_form_notice('error', 'unable to activate the plugin - please try again');
    }
    jrCore_location('referrer');
}

//------------------------------
// plugin_config_save
//------------------------------
function view_jrPayment_plugin_config_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    if (!isset($_post['plugin']) || !jrPayment_is_valid_plugin($_post['plugin'])) {
        jrCore_set_form_notice('error', 'invalid plugin');
        jrCore_form_result();
    }
    // Is our plugin doing any validation?
    $plug = $_post['plugin'];
    $func = "jrPayment_plugin_{$plug}_config_validate";
    if (!function_exists($func)) {
        require_once APP_DIR . "/modules/jrPayment/plugins/{$plug}.php";
    }
    if (function_exists($func)) {
        if (!$func($_post)) {
            jrCore_form_result();
        }
    }
    // Some things we don't save
    $_data = $_post;
    unset($_data['module'], $_data['module_url'], $_data['option'], $_data['uri'], $_data['_1'], $_data['_2']);

    $plg = jrCore_db_escape($plug);
    $_cf = jrCore_db_escape(json_encode($_data));
    $tbl = jrCore_db_table_name('jrPayment', 'plugin_config');
    $req = "INSERT INTO {$tbl} (config_plugin, config_time, config_content) VALUES ('{$plg}', UNIX_TIMESTAMP(), '{$_cf}') ON DUPLICATE KEY UPDATE config_time = UNIX_TIMESTAMP(), config_content = '{$_cf}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt == 0) {
        jrCore_set_form_notice('error', 'an error was encountered saving the config - please try again');
        jrCore_form_result();
    }
    jrCore_set_form_notice('success', 'The config settings were successfully saved');
    jrCore_form_result();
}

//------------------------------
// plugin_view
//------------------------------
function view_jrPayment_plugin_view($_post, $_user, $_conf)
{
    // http://site.com/payment/plugin_view/plugin/?
    $func = "jrPayment_plugin_view_{$_post['_1']}_{$_post['_2']}";
    if (!function_exists($func)) {
        require_once APP_DIR . "/modules/jrPayment/plugins/{$_post['_1']}.php";
    }
    if (function_exists($func)) {
        return $func($_post, $_user, $_conf);
    }
    return jrCore_notice_page('error', 'invalid plugin view');
}

//------------------------------
// success
//------------------------------
function view_jrPayment_success($_post, $_user, $_conf)
{
    // NOTE: Our cart doesn't actually "clear" until the webhook from the payment
    // processor comes through - but if we hit here it means we've just made payment
    // and we can "clear" the cart (even though it remains)
    if ($_cr = jrPayment_get_user_cart(false)) {
        // "1" marks this cart as COMPLETE
        jrPayment_set_cart_status($_cr['cart_id'], PAYMENT_CART_COMPLETE);
    }

    jrCore_trigger_event('jrPayment', 'payment_success_page', $_post);
    $_ln = jrUser_load_lang_strings();
    jrCore_page_notice('success', "<div class=\"p10 center\">{$_ln['jrPayment'][28]}<br><br>{$_ln['jrPayment'][30]}</div>", false);
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/purchases", $_ln['jrPayment'][29]);
    jrCore_page_display();
}

//----------------------------
// payout
//----------------------------
function view_jrPayment_payout($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrPayment');
    jrCore_page_banner('Profile Payout', '<small>&starf; includes tax<br>&star; includes shipping</small>');
    jrCore_get_form_notice();

    $dat             = array();
    $dat[1]['title'] = 'select';
    $dat[1]['width'] = '4%';
    $dat[2]['title'] = 'profile';
    $dat[2]['width'] = '21%';
    $dat[3]['title'] = 'payout email address';
    $dat[3]['width'] = '33%';
    $dat[4]['title'] = 'sales';
    $dat[4]['width'] = '12%';
    $dat[5]['title'] = 'system fee';
    $dat[5]['width'] = '12%';
    $dat[6]['title'] = 'payout';
    $dat[6]['width'] = '18%';
    jrCore_page_table_header($dat);

    // First up - get all the profiles we are going to payout and the
    // quota that their profile belongs to
    $old = ($_conf['jrPayment_payout_clears'] * 86400);
    $tbl = jrCore_db_table_name('jrPayment', 'register');

    $_pi = array();
    $req = "SELECT r_seller_profile_id AS pid FROM {$tbl} WHERE r_payed_out_time = 0 AND r_created < (UNIX_TIMESTAMP() - {$old}) AND r_seller_profile_id > 0 GROUP BY r_seller_profile_id";
    $_rt = jrCore_db_query($req, 'pid');
    if ($_rt && is_array($_rt)) {
        $_sc = array(
            'search'        => array(
                '_profile_id in ' . implode(',', array_keys($_rt))
            ),
            'return_keys'   => array('_profile_id', 'profile_quota_id'),
            'skip_triggers' => true,
            'privacy_check' => false,
            'limit'         => count($_rt)
        );
        $_sc = jrCore_db_search_items('jrProfile', $_sc);
        if ($_sc && is_array($_sc) && isset($_sc['_items'])) {
            foreach ($_sc['_items'] as $p) {
                $pid = (int) $p['_profile_id'];
                $qid = (int) $p['profile_quota_id'];
                if (!isset($_pi[$qid])) {
                    $_pi[$qid] = array();
                }
                $_pi[$qid][] = $pid;
            }
        }
    }

    $done = false;
    if (count($_pi) > 0) {

        // We have our profile and Quota info - get tax and shipping options from the quota
        $_id = array();
        $_us = array();
        $_ui = array();
        $_in = array();
        $_fe = array();
        $_qo = array();
        $_qi = array();
        foreach ($_pi as $quota_id => $_pids) {

            // Get payments for payout
            $tax = 'off';
            $shp = 'off';
            $_qt = jrProfile_get_quota($quota_id);
            // We are including BOTH shipping and tax in this payout
            if ($_qt['quota_jrPayment_include_tax'] == 'on' && $_qt['quota_jrPayment_include_shipping'] == 'on') {
                $req = "SELECT r_id, r_seller_profile_id, ((r_amount + r_shipping + r_tax) - r_refunded_amount) AS r_amount, ((r_amount + r_shipping + r_tax) - r_fee - r_refunded_amount) AS r_income, r_fee FROM {$tbl}
                         WHERE r_payed_out_time = 0 AND r_created < (UNIX_TIMESTAMP() - {$old}) AND r_seller_profile_id IN(" . implode(',', $_pids) . ')';
                $tax = 'on';
                $shp = 'on';
            }
            // We are including ONLY tax in this payout
            elseif ($_qt['quota_jrPayment_include_tax'] == 'on') {
                $req = "SELECT r_id, r_seller_profile_id, ((r_amount + r_tax) - r_refunded_amount) AS r_amount, ((r_amount + r_tax) - r_fee - r_refunded_amount) AS r_income, r_fee FROM {$tbl}
                         WHERE r_payed_out_time = 0 AND r_created < (UNIX_TIMESTAMP() - {$old}) AND r_seller_profile_id IN(" . implode(',', $_pids) . ')';
                $tax = 'on';
            }
            // We are including ONLY shipping in this payout
            elseif ($_qt['quota_jrPayment_include_shipping'] == 'on') {
                $req = "SELECT r_id, r_seller_profile_id, ((r_amount + r_shipping) - r_refunded_amount) AS r_amount, ((r_amount + r_shipping) - r_fee - r_refunded_amount) AS r_income, r_fee FROM {$tbl}
                         WHERE r_payed_out_time = 0 AND r_created < (UNIX_TIMESTAMP() - {$old}) AND r_seller_profile_id IN(" . implode(',', $_pids) . ')';
                $shp = 'on';
            }
            // We are not including Tax OR Shipping
            else {
                $req = "SELECT r_id, r_seller_profile_id, (r_amount - r_refunded_amount) AS r_amount, (r_amount - r_fee - r_refunded_amount) AS r_income, r_fee FROM {$tbl}
                         WHERE r_payed_out_time = 0 AND r_created < (UNIX_TIMESTAMP() - {$old}) AND r_seller_profile_id IN(" . implode(',', $_pids) . ')';
            }
            $_rt = jrCore_db_query($req, 'r_id');
            if ($_rt && is_array($_rt)) {
                foreach ($_rt as $rid => $_r) {
                    $pid = (int) $_r['r_seller_profile_id'];
                    if (!isset($_ui[$pid])) {
                        $_ui[$pid] = 0;
                        $_fe[$pid] = 0;
                    }
                    $_in[$pid] += $_r['r_amount'];
                    $_ui[$pid] += $_r['r_income'];
                    $_fe[$pid] += $_r['r_fee'];
                    $_qi[$pid] = $quota_id;
                    $_id[]     = $rid;
                }
            }
            $_qo[$quota_id] = array(
                'tax' => $tax,
                'shp' => $shp
            );
        }
        arsort($_ui, SORT_NUMERIC);

        // Save our payout key
        $md5 = md5(microtime());
        $_op = array(
            'pids' => $_pi,
            'opts' => $_qo
        );
        $opt = jrCore_db_escape(json_encode($_op));
        $tbl = jrCore_db_table_name('jrPayment', 'payout');
        // Note: we don't store payout_profile_id's here - that will be filled in below after saving
        $req = "INSERT INTO {$tbl} (payout_key, payout_time, payout_profile_ids, payout_ids, payout_refunds, payout_options) VALUE ('{$md5}', UNIX_TIMESTAMP(), '', '" . implode(',', $_id) . "', '', '{$opt}')";
        jrCore_db_query($req);

        // Form init
        $_tmp = array(
            'submit_value'     => 'payout selected profiles',
            'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
            'form_ajax_submit' => false
        );
        jrCore_form_create($_tmp);

        // Unique Payout Code
        $_tmp = array(
            'name'  => 'p_code',
            'type'  => 'hidden',
            'value' => $md5
        );
        jrCore_form_field_create($_tmp);

        if (count($_ui) > 0) {
            $_pr = array(
                'search'         => array(
                    '_item_id in ' . implode(',', array_keys($_ui))
                ),
                'return_keys'    => array('_profile_id', 'profile_url', 'profile_jrPayment_payout_email', 'profile_jrPayment_refund_adjust'),
                'skip_triggers'  => true,
                'privacy_check'  => false,
                'ignore_pending' => true,
                'limit'          => count($_ui)
            );
            $_pr = jrCore_db_search_items('jrProfile', $_pr);
            if ($_pr && is_array($_pr) && isset($_pr['_items'])) {
                foreach ($_pr['_items'] as $_u) {
                    $uid       = (int) $_u['_profile_id'];
                    $_us[$uid] = $_u;
                }
            }
            $_rf = array();
            $_ra = array();
            $url = jrCore_get_module_url('jrProfile');
            foreach ($_ui as $pid => $amount) {
                if (isset($_us[$pid]['profile_jrPayment_refund_adjust']) && $_us[$pid]['profile_jrPayment_refund_adjust'] > 0) {
                    // This profile's payout is going to be adjust since we had a refund on one of their items
                    $_rf[$pid] = $amount;
                    $_ra[$pid] = (int) $_us[$pid]['profile_jrPayment_refund_adjust'];
                    continue;
                }
                if ($amount < 0) {
                    continue;
                }
                $dat = array();
                // We have to have an email address to payout
                if (isset($_us[$pid]['profile_jrPayment_payout_email'])) {
                    if ($amount > 1000) {
                        $dat[1]['title'] = '<input name="profile_id[' . $pid . ']" type="checkbox" value="' . $amount . '" checked="checked">';
                    }
                    else {
                        $dat[1]['title'] = '<input name="profile_id[' . $pid . ']" type="checkbox" value="' . $amount . '">';
                    }
                    $dat[3]['title'] = $_us[$pid]['profile_jrPayment_payout_email'];
                    $dat[3]['class'] = 'center';
                }
                else {
                    $dat[1]['title'] = '<input name="profile_id[' . $pid . ']" type="checkbox" value="' . $amount . '" disabled="disabled">';
                    $dat[3]['title'] = 'no payout email address';
                    $dat[3]['class'] = 'error center';
                }
                $dat[1]['class'] = 'center';
                $dat[2]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$url}/settings/profile_id={$pid}\">@" . $_us[$pid]['profile_url'] . '</a>';
                $dat[2]['class'] = 'center';
                $dat[4]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_in[$pid]);
                $dat[4]['class'] = 'center';
                $dat[5]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_fe[$pid]);
                $dat[5]['class'] = 'center';
                $dat[6]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($amount);
                if (isset($_qi[$pid])) {
                    $qid = (int) $_qi[$pid];
                    if (isset($_qo[$qid])) {
                        if ($_qo[$qid]['tax'] == 'on' && $_qo[$qid]['shp'] == 'on') {
                            $dat[6]['title'] .= ' <sup><small>&starf;&star;</small></sup>';
                        }
                        elseif ($_qo[$qid]['tax'] == 'on') {
                            $dat[6]['title'] .= ' <sup><small>&starf;</small></sup>';
                        }
                        elseif ($_qo[$qid]['shp'] == 'on') {
                            $dat[6]['title'] .= ' <sup><small>&star;</small></sup>';
                        }
                    }
                }
                $dat[6]['class'] = 'center';
                jrCore_page_table_row($dat);
                $done = true;
            }

            if (count($_rf) > 0) {

                // Save refund info for this payout key
                $tbl = jrCore_db_table_name('jrPayment', 'payout');
                $req = "UPDATE {$tbl} SET payout_refunds = '" . json_encode($_ra) . "' WHERE payout_key = '{$md5}' LIMIT 1";
                jrCore_db_query($req);

                // We have profiles that need to have their payout adjusted
                foreach ($_rf as $pid => $amount) {
                    if ($amount < 0) {
                        continue;
                    }
                    $dat = array();
                    // We have to have an email address to payout
                    if (isset($_us[$pid]['profile_jrPayment_payout_email'])) {
                        if ($amount > 1000) {
                            $dat[1]['title'] = '<input name="profile_id[' . $pid . ']" type="checkbox" value="' . $amount . '" checked="checked">';
                        }
                        else {
                            $dat[1]['title'] = '<input name="profile_id[' . $pid . ']" type="checkbox" value="' . $amount . '">';
                        }
                        $dat[3]['title'] = $_us[$pid]['profile_jrPayment_payout_email'];
                        $dat[3]['class'] = 'center';
                    }
                    else {
                        $dat[1]['title'] = '<input name="profile_id[' . $pid . ']" type="checkbox" value="' . $amount . '" disabled="disabled">';
                        $dat[3]['title'] = 'no payout email address';
                        $dat[3]['class'] = 'error center';
                    }
                    $dat[1]['class'] = 'center';
                    $dat[2]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_us[$pid]['profile_url']}\">@" . $_us[$pid]['profile_url'] . '</a>';
                    $dat[2]['class'] = 'center';
                    $dat[4]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_in[$pid]);
                    $dat[4]['class'] = 'center';
                    $dat[5]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_fe[$pid]);
                    $dat[5]['class'] = 'center';

                    $adjust = $_us[$pid]['profile_jrPayment_refund_adjust'];
                    if ($amount > $adjust) {
                        $dat[6]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($amount - $adjust);
                    }
                    else {
                        // This profile will NOT be payed out
                        $dat[6]['title'] = jrPayment_get_currency_code() . '0';
                    }
                    if (isset($_qi[$pid])) {
                        $qid = (int) $_qi[$pid];
                        if (isset($_qo[$qid])) {
                            if ($_qo[$qid]['tax'] == 'on' && $_qo[$qid]['shp'] == 'on') {
                                $dat[6]['title'] .= ' <sup><small>&starf;&star;</small></sup>';
                            }
                            elseif ($_qo[$qid]['tax'] == 'on') {
                                $dat[6]['title'] .= ' <sup><small>&starf;</small></sup>';
                            }
                            elseif ($_qo[$qid]['shp'] == 'on') {
                                $dat[6]['title'] .= ' <sup><small>&star;</small></sup>';
                            }
                        }
                    }
                    $adjurl = "<a href=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/payments/profile_id={$pid}/refunded=1\" target=\"_blank\"><u>refunds</u></a>";
                    if ($amount > $adjust) {
                        $dat[6]['title'] .= '<br><small>-' . jrPayment_get_currency_code() . jrPayment_currency_format($adjust) . " for {$adjurl}</small>";
                    }
                    else {
                        // This profile will NOT be payed out
                        $dat[6]['title'] .= '<br><small>-' . jrPayment_get_currency_code() . jrPayment_currency_format($amount) . " for {$adjurl}</small>";
                    }
                    $dat[6]['class'] = 'center';
                    jrCore_page_table_row($dat);
                    $done = true;
                }
            }
        }

    }
    if (!$done) {
        $dat             = array();
        $dat[1]['title'] = 'There are no profiles with pending payouts';
        $dat[1]['class'] = 'center p10';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_display();
}

//--------------------------------------------------------------------
// payout_save
//--------------------------------------------------------------------
function view_jrPayment_payout_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // We must have at least 1 profile to payout
    if (!isset($_post['profile_id']) || !is_array($_post['profile_id'])) {
        jrCore_set_form_notice('error', 'You must choose at least one profile to payout');
        jrCore_location('referrer');
    }

    // We must have a valid payout code
    if (!isset($_post['p_code']) || !jrCore_checktype($_post['p_code'], 'md5')) {
        jrCore_set_form_notice('error', 'Invalid payout code received - please try again');
        jrCore_location('referrer');
    }

    $tbl = jrCore_db_table_name('jrPayment', 'payout');
    $req = "SELECT payout_ids FROM {$tbl} WHERE payout_key = '" . jrCore_db_escape($_post['p_code']) . "'";
    $_id = jrCore_db_query($req, 'SINGLE');
    if (!$_id || !is_array($_id)) {
        jrCore_set_form_notice('error', 'Invalid payout code received - no ids found in DB');
        jrCore_location('referrer');
    }

    // Now we have the complete list of register ID's that are in this payout process
    // Save posted PIDs to the DB
    $_pd = array();
    foreach ($_post['profile_id'] as $id => $amount) {
        $_pd[] = (int) $id;
    }
    $req = "UPDATE {$tbl} SET payout_profile_ids = '" . implode(',', $_pd) . "' WHERE payout_key = '" . jrCore_db_escape($_post['p_code']) . "'";
    jrCore_db_query($req);

    jrCore_form_delete_session();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrPayout');
    jrCore_page_banner('Payout Profiles');
    jrCore_get_form_notice();

    $note = "<div class=\"p10\" style=\"text-align:left\">
    <ul><li>You should be prompted to download the Payments CSV file - save to your computer.</li><br>
    <li> <a href=\"https://www.paypal.com\" target=\"_blank\"><u>Log in to PayPal</u></a> and go to Send Payment &raquo; Make a Mass Payment.</li>
    <li> Select the CSV file that was just download and upload it to PayPal in the Mass Pay form.</li>
    <li> Complete the Mass Pay process at PayPal.</li>
    <li> Click the &quot;Complete Payout&quot; button below when finished.</li><br>
    <li>You can redownload the CSV file by <a href=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/payout_csv/p_code={$_post['p_code']}\"><u>Clicking Here.</u></a></li></ul>
    <iframe src=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/payout_csv/p_code={$_post['p_code']}\" style=\"display:none;\"></iframe>";
    jrCore_set_form_notice('notice', $note, false);
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value'     => 'complete payout',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/payout",
        'form_ajax_submit' => false,
        'action'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/payout_complete"
    );
    jrCore_form_create($_tmp);

    // key
    $_tmp = array(
        'name'  => 'p_code',
        'type'  => 'hidden',
        'value' => $_post['p_code']
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// payout_csv
//------------------------------
function view_jrPayment_payout_csv($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['p_code']) || !jrCore_checktype($_post['p_code'], 'md5')) {
        jrCore_notice('error', 'invalid payout code received');
    }
    $tbl = jrCore_db_table_name('jrPayment', 'payout');
    $req = "SELECT * FROM {$tbl} WHERE payout_key = '" . jrCore_db_escape($_post['p_code']) . "'";
    $_id = jrCore_db_query($req, 'SINGLE');
    if (!$_id || !is_array($_id)) {
        jrCore_notice('error', 'invalid payout code received (2)');
    }

    // Get profiles we are paying out
    $_pp = array_flip(explode(',', $_id['payout_profile_ids']));

    // Get our payout options
    $_op = json_decode($_id['payout_options'], true);
    if (!isset($_op['pids']) || !is_array($_op['pids'])) {
        jrCore_notice('error', 'invalid payout options received');
    }

    $_pa = array();
    $tbl = jrCore_db_table_name('jrPayment', 'register');
    foreach ($_op['pids'] as $quota_id => $_pids) {

        if (!isset($_op['opts'][$quota_id])) {
            jrCore_notice('error', 'unable to retrieve quota options for quota_id ' . $quota_id);
        }

        $_opts = $_op['opts'][$quota_id];
        // We are including BOTH shipping and tax in this payout
        if ($_opts['tax'] == 'on' && $_opts['shp'] == 'on') {
            $req = "SELECT r_id, r_seller_profile_id, ((r_amount + r_shipping + r_tax) - r_fee - r_refunded_amount) AS r_amount FROM {$tbl}
                     WHERE r_seller_profile_id IN(" . implode(',', $_pids) . ') AND r_id IN(' . trim(trim($_id['payout_ids'], ',')) . ')';
        }
        // We are including ONLY tax in this payout
        elseif ($_opts['tax'] == 'on') {
            $req = "SELECT r_id, r_seller_profile_id, ((r_amount + r_tax) - r_fee - r_refunded_amount) AS r_amount FROM {$tbl}
                     WHERE r_seller_profile_id IN(" . implode(',', $_pids) . ') AND r_id IN(' . trim(trim($_id['payout_ids'], ',')) . ')';
        }
        // We are including ONLY shipping in this payout
        elseif ($_opts['shp'] == 'on') {
            $req = "SELECT r_id, r_seller_profile_id, ((r_amount + r_shipping) - r_fee - r_refunded_amount) AS r_amount FROM {$tbl}
                     WHERE r_seller_profile_id IN(" . implode(',', $_pids) . ') AND r_id IN(' . trim(trim($_id['payout_ids'], ',')) . ')';
        }
        // We are not including Tax OR Shipping
        else {
            $req = "SELECT r_id, r_seller_profile_id, (r_amount - r_fee - r_refunded_amount) AS r_amount FROM {$tbl}
                     WHERE r_seller_profile_id IN(" . implode(',', $_pids) . ') AND r_id IN(' . trim(trim($_id['payout_ids'], ',')) . ')';
        }
        $_rt = jrCore_db_query($req, 'r_id');
        if (!$_rt || !is_array($_rt)) {
            // Something is wrong...
            jrCore_notice('error', 'payout transactions listed in payout are not found in the DB');
        }
        $_pa = array();
        foreach ($_rt as $rid => $_r) {
            $pid = (int) $_r['r_seller_profile_id'];
            if (isset($_pp[$pid])) {
                // This is a transaction for a profile we are paying out
                if (!isset($_pa[$pid])) {
                    $_pa[$pid] = 0;
                }
                $_pa[$pid] += $_r['r_amount'];
            }
        }
    }
    if (count($_pa) === 0) {
        // Something is wrong...
        jrCore_notice('error', 'no profiles found with payout balances');
    }
    if (strlen($_id['payout_refunds']) > 0) {
        // We have some refunds to process
        if ($_rf = json_decode($_id['payout_refunds'], true)) {
            foreach ($_rf as $pid => $amount) {
                if (isset($_pa[$pid])) {
                    if ($amount > $_pa[$pid]) {
                        // This profile gets no payout
                        $_pa[$pid] = 0;
                    }
                    else {
                        $_pa[$pid] = ($_pa[$pid] - $amount);
                    }
                }
            }
        }
    }

    // Get payout email addresses
    $_pr = jrCore_db_get_multiple_items('jrProfile', array_keys($_pa), array('_profile_id', 'profile_jrPayment_payout_email'));
    if (!$_pr || !is_array($_pr)) {
        jrCore_notice('error', 'unable to retrieve profile info for payout profiles');
    }
    $_pi = array();
    foreach ($_pr as $_p) {
        $pid       = (int) $_p['_profile_id'];
        $_pi[$pid] = $_p['profile_jrPayment_payout_email'];
    }

    // Format is for PayPal MassPay - use Store Currency value
    $cfg = jrPayment_get_plugin_config('paypal');
    $cur = (isset($cfg['currency']) && strlen($cfg['currency']) > 0) ? $cfg['currency'] : 'USD';

    // Format...
    $out = '';
    foreach ($_pa as $pid => $amount) {
        $out .= $_pi[$pid] . ',' . jrPayment_currency_format($amount) . ",{$cur}\n";
    }

    // Looks good - send file
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Cache-Control: private");
    header("Pragma: no-cache");
    header("Content-type: application/csv");
    header("Content-Disposition: inline; filename=\"Payout_{$_post['p_code']}.csv\"");
    header("Content-length: " . strlen($out));
    ob_start();
    echo $out;
    ob_end_flush();
    exit;
}

//--------------------------------------------------------------------
// payout_complete
//--------------------------------------------------------------------
function view_jrPayment_payout_complete($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // We must have a valid payout code
    if (!isset($_post['p_code']) || !jrCore_checktype($_post['p_code'], 'md5')) {
        jrCore_set_form_notice('error', 'Invalid payout code received - please try again');
        jrCore_location('referrer');
    }

    $tbl = jrCore_db_table_name('jrPayment', 'payout');
    $req = "SELECT * FROM {$tbl} WHERE payout_key = '" . jrCore_db_escape($_post['p_code']) . "'";
    $_id = jrCore_db_query($req, 'SINGLE');
    if (!$_id || !is_array($_id)) {
        jrCore_set_form_notice('error', 'Invalid payout code received - no ids found in DB');
        jrCore_location('referrer');
    }

    $tbl = jrCore_db_table_name('jrPayment', 'register');
    $req = "UPDATE {$tbl} SET r_payed_out_time = UNIX_TIMESTAMP() WHERE r_id IN({$_id['payout_ids']})";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt > 0) {

        $_pd = explode(',', $_id['payout_profile_ids']);
        if ($_pd && is_array($_pd)) {

            // Get our payout options
            $_op = json_decode($_id['payout_options'], true);
            if (!isset($_op['pids']) || !is_array($_op['pids'])) {
                jrCore_set_form_notice('error', 'Invalid payout options received');
                jrCore_location('referrer');
            }

            $_am = array();
            $tbl = jrCore_db_table_name('jrPayment', 'register');
            foreach ($_op['pids'] as $quota_id => $_pids) {

                $_opts = $_op['opts'][$quota_id];
                // We are including BOTH shipping and tax in this payout
                if ($_opts['tax'] == 'on' && $_opts['shp'] == 'on') {
                    $req = "SELECT r_id, r_seller_profile_id, ((r_amount + r_shipping + r_tax) - r_fee - r_refunded_amount) AS r_amount FROM {$tbl}
                             WHERE r_seller_profile_id IN(" . implode(',', $_pids) . ') AND r_id IN(' . trim(trim($_id['payout_ids'], ',')) . ')';
                }
                // We are including ONLY tax in this payout
                elseif ($_opts['tax'] == 'on') {
                    $req = "SELECT r_id, r_seller_profile_id, ((r_amount + r_tax) - r_fee - r_refunded_amount) AS r_amount FROM {$tbl}
                             WHERE r_seller_profile_id IN(" . implode(',', $_pids) . ') AND r_id IN(' . trim(trim($_id['payout_ids'], ',')) . ')';
                }
                // We are including ONLY shipping in this payout
                elseif ($_opts['shp'] == 'on') {
                    $req = "SELECT r_id, r_seller_profile_id, ((r_amount + r_shipping) - r_fee - r_refunded_amount) AS r_amount FROM {$tbl}
                             WHERE r_seller_profile_id IN(" . implode(',', $_pids) . ') AND r_id IN(' . trim(trim($_id['payout_ids'], ',')) . ')';
                }
                // We are not including Tax OR Shipping
                else {
                    $req = "SELECT r_id, r_seller_profile_id, (r_amount - r_fee - r_refunded_amount) AS r_amount FROM {$tbl}
                             WHERE r_seller_profile_id IN(" . implode(',', $_pids) . ') AND r_id IN(' . trim(trim($_id['payout_ids'], ',')) . ')';
                }
                $_rt = jrCore_db_query($req, 'r_id');
                if (!$_rt || !is_array($_rt)) {
                    // Something is wrong...
                    jrCore_set_form_notice('error', 'payout transactions listed in payout are not found in the DB');
                    jrCore_location('referrer');
                }
                $_pa = array();
                foreach ($_rt as $rid => $_r) {
                    $pid = (int) $_r['r_seller_profile_id'];
                    if (!isset($_pa[$pid])) {
                        $_pa[$pid] = 0;
                    }
                    $_am[$pid] += $_r['r_amount'];
                }
            }

            if (strlen($_id['payout_refunds']) > 0) {
                // We have some refunds to process
                if ($_rf = json_decode($_id['payout_refunds'], true)) {
                    foreach ($_rf as $pid => $amount) {
                        if (isset($_am[$pid])) {
                            if ($amount > $_am[$pid]) {
                                // This profile gets no payout
                                $_am[$pid] = 0;
                                // We did not use up all the refund adjustment - decrement
                                jrCore_db_decrement_key('jrProfile', $pid, 'profile_jrPayment_refund_adjust', $_am[$pid], 0);
                            }
                            else {
                                $_am[$pid] = ($_am[$pid] - $amount);
                                // We used ALL the refund adjustment - delete the key
                                jrCore_db_delete_item_key('jrProfile', $pid, 'profile_jrPayment_refund_adjust');
                            }
                        }
                    }
                }
            }

            // Get profile info for these profile ids
            $_pr = array();
            $_tm = jrCore_db_get_multiple_items('jrProfile', $_pd, array('_profile_id', 'profile_jrPayment_payout_email'));
            if ($_tm && is_array($_tm)) {
                foreach ($_tm as $_p) {
                    $pid       = (int) $_p['_profile_id'];
                    $_pr[$pid] = $_p;
                }
            }

            // Notify the profiles they have been paid
            foreach ($_pd as $pid) {
                $_rp = array(
                    'payout_email'  => $_pr[$pid]['profile_jrPayment_payout_email'],
                    'payout_amount' => jrPayment_currency_format($_am[$pid])
                );
                list($sub, $msg) = jrCore_parse_email_templates('jrPayment', 'profile_payout', $_rp);
                jrCore_send_email($_pr[$pid]['profile_jrPayment_payout_email'], $sub, $msg);
                jrCore_logger('INF', "Payments: successfully payed out " . jrPayment_currency_format($_am[$pid]) . " to " . $_pr[$pid]['profile_jrPayment_payout_email']);
            }

            // Mark this payout code as done
            $tbl = jrCore_db_table_name('jrPayment', 'payout');
            $req = "UPDATE {$tbl} SET payout_completed = UNIX_TIMESTAMP() WHERE payout_key = '" . jrCore_db_escape($_post['p_code']) . "'";
            jrCore_db_query($req);

            jrCore_logger('INF', "Payments: marked {$cnt} transactions as payed out to profiles");

            jrCore_page_include_admin_menu();
            jrCore_page_admin_tabs('jrPayment');
            jrCore_page_banner('Payout Complete');
            jrCore_set_form_notice('success', 'The selected profiles payouts have been completed.');
            jrCore_get_form_notice();
            jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/payout", 'continue');
            jrCore_page_display();
        }
    }
    else {
        jrCore_form_delete_session();
        jrCore_set_form_notice('error', 'Invalid payout key - please try again');
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/payout");
    }
}

//------------------------------
// system_reset
//------------------------------
function view_jrPayment_system_reset($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrPayment');
    jrCore_page_banner('reset test data');

    // Form init
    $_tmp = array(
        'submit_value'  => 'reset selected options',
        'cancel'        => 'referrer',
        'submit_title'  => 'reset selected options?',
        'submit_prompt' => 'This will delete all test data for the selected options!',
    );
    jrCore_form_create($_tmp);

    $_opts = array(
        'reset_cart'     => array('reset cart data', 'delete all existing cart data', 'on'),
        'reset_register' => array('reset payment register', 'delete all existing register data', 'on'),
        'reset_payout'   => array('reset profile payouts', 'delete all existing profile payout data', 'on'),
        'reset_meta'     => array('reset user meta data', 'delete all existing user meta data', 'on')
    );
    $_opts = jrCore_trigger_event('jrPayment', 'system_reset_options', $_opts);

    foreach ($_opts as $name => $_inf) {
        $_tmp = array(
            'name'     => $name,
            'label'    => $_inf[0],
            'help'     => $_inf[1],
            'type'     => 'checkbox',
            'validate' => 'onoff',
            'default'  => $_inf[2],
            'required' => true
        );
        jrCore_form_field_create($_tmp);
    }
    jrCore_page_display();
}

//------------------------------
// system_reset_save
//------------------------------
function view_jrPayment_system_reset_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    if (isset($_post['reset_cart']) && $_post['reset_cart'] == 'on') {
        foreach (array('cart', 'cart_item') as $table) {
            $tbl = jrCore_db_table_name('jrPayment', $table);
            jrCore_db_query("TRUNCATE TABLE {$tbl}");
        }
    }

    if (isset($_post['reset_register']) && $_post['reset_register'] == 'on') {

        $tbl = jrCore_db_table_name('jrPayment', 'register');
        jrCore_db_query("TRUNCATE TABLE {$tbl}");
        jrCore_db_truncate_datastore('jrPayment');

        $tbl = jrCore_db_table_name('jrPayment', 'vault');
        jrCore_db_query("TRUNCATE TABLE {$tbl}");
        jrPayment_delete_all_vault_files();
    }

    if (isset($_post['reset_payout']) && $_post['reset_payout'] == 'on') {
        $tbl = jrCore_db_table_name('jrPayment', 'payout');
        jrCore_db_query("TRUNCATE TABLE {$tbl}");
    }

    if (isset($_post['reset_meta']) && $_post['reset_meta'] == 'on') {
        jrCore_db_delete_key_from_all_items('jrUser', 'user_stripe_customer_id');
    }

    jrCore_trigger_event('jrPayment', 'system_reset_save', $_post);

    jrCore_logger('INF', "Payments: the selected payment system options were successfully reset", $_post);
    jrCore_set_form_notice('success', 'The selected options were successfully reset');
    jrCore_location('referrer');
}

//------------------------------
// import
//------------------------------
function view_jrPayment_import($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrPayment');

    if (!isset($_mods['jrFoxyCart'])) {
        $show = false;
        jrCore_set_form_notice('error', 'The FoxyCart eCommerce module is not installed');
    }
    else {
        $show = true;
        jrCore_set_form_notice('success', "This tool will attempt to import existing FoxyCart payments in to the Payment Support module");
    }
    jrCore_page_banner('import FoxyCart payments');
    jrCore_get_form_notice();

    if ($show) {
        $_tmp = array(
            'submit_value'  => 'import payments',
            'cancel'        => 'referrer',
            'submit_title'  => 'Import Payments from FoxyCart?',
            'submit_prompt' => 'Please be patient while the payments are imported',
            'submit_modal'  => 'update',
            'modal_width'   => 600,
            'modal_height'  => 400,
            'modal_note'    => 'Please be patient while the payments are imported'
        );
        jrCore_form_create($_tmp);

        $_tmp = array(
            'name'     => 'item_payments',
            'label'    => 'import item payments',
            'help'     => 'If this option is checked, existing item payments will be imported into the Payment Support module',
            'type'     => 'checkbox',
            'validate' => 'onoff',
            'default'  => 'on',
            'required' => true
        );
        jrCore_form_field_create($_tmp);

        if (jrCore_module_is_active('jrSubscribe')) {
            $_tmp = array(
                'name'     => 'sub_payments',
                'label'    => 'import subscription payments',
                'help'     => 'If this option is checked, existing subscription payments will be imported into the Payment Support module',
                'type'     => 'checkbox',
                'validate' => 'onoff',
                'default'  => 'on',
                'required' => true
            );
            jrCore_form_field_create($_tmp);
        }

        $_tmp = array(
            'name'     => 'ledger_payments',
            'label'    => 'import manual transactions',
            'help'     => 'If this option is checked, existing transactions that were entered manually will be imported',
            'type'     => 'checkbox',
            'validate' => 'onoff',
            'default'  => 'on',
            'required' => true
        );
        jrCore_form_field_create($_tmp);
    }
    jrCore_page_display();
}

//------------------------------
// import_save
//------------------------------
function view_jrPayment_import_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Import Item Sales
    $cnt = 0;

    if (isset($_post['item_payments']) && $_post['item_payments'] == 'on') {
        $tb1 = jrCore_db_table_name('jrFoxyCart', 'sale');
        $tb2 = jrCore_db_table_name('jrFoxyCart', 'purchase');
        $req = "SELECT * FROM {$tb1} s LEFT JOIN {$tb2} p ON p.purchase_id = s.sale_purchase_id ORDER BY s.sale_id ASC";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt)) {
            jrCore_form_modal_notice('update', "checking " . jrCore_number_format(count($_rt)) . " payment transactions");
            foreach ($_rt as $k => $p) {
                if (isset($p['purchase_module'])) {

                    // Make sure we have not already imported this transaction
                    $tbl = jrCore_db_table_name('jrPayment', 'register');
                    $req = "SELECT * FROM {$tbl} WHERE r_gateway_id = '" . jrCore_db_escape($p['sale_txn_id']) . "' LIMIT 1";
                    $_ex = jrCore_db_query($req, 'SINGLE');
                    if ($_ex && is_array($_ex)) {
                        // Already imported
                        continue;
                    }

                    // Get the RAW transaction if we can
                    $_tx = jrCore_db_get_item_by_key('jrFoxyCart', 'txn_id', $p['sale_txn_id'], true);
                    $_tr = array(
                        'txn_id'       => $p['sale_txn_id'],
                        'txn_status'   => 'active',
                        'txn_date'     => $p['sale_time'],
                        'txn_type'     => 'payment',
                        'txn_plugin'   => (strpos($p['sale_txn_id'], '_')) ? 'stripe' : 'foxycart',
                        'txn_currency' => $_conf['jrFoxyCart_store_currency'],
                        'txn_total'    => jrPayment_price_to_cents($p['sale_gross']),
                        'txn_shipping' => jrPayment_price_to_cents($p['sale_shipping']),
                        'txn_user_id'  => $p['sale_buyer_user_id']
                    );
                    if (is_array($_tx)) {
                        $_tr['txn_raw'] = json_encode($_tx);
                    }
                    $_cr = array(
                        '_created'    => $p['sale_time'],
                        '_updated'    => $p['sale_time'],
                        '_profile_id' => 0,
                        '_user_id'    => 0
                    );
                    if ($tid = jrPayment_create_transaction($_tr, $_cr)) {
                        $_it = array();
                        if (isset($p['purchase_data']) && strlen($p['purchase_data']) > 0) {
                            $_it = json_decode($p['purchase_data'], true);
                        }
                        $_fl = array(
                            'r_txn_id'            => $tid,
                            'r_plugin'            => (strpos($p['sale_txn_id'], '_')) ? 'stripe' : 'foxycart',
                            'r_currency'          => $_conf['jrFoxyCart_store_currency'],
                            'r_gateway_id'        => $p['sale_txn_id'],
                            'r_created'           => $p['sale_time'],
                            'r_purchase_user_id'  => (int) $p['sale_buyer_user_id'],
                            'r_seller_profile_id' => (int) $p['sale_seller_profile_id'],
                            'r_module'            => $p['purchase_module'],
                            'r_item_id'           => $p['purchase_item_id'],
                            'r_field'             => $p['purchase_field'],
                            'r_quantity'          => 1,
                            'r_amount'            => jrPayment_price_to_cents($p['sale_gross']),
                            'r_shipping'          => jrPayment_price_to_cents($p['sale_shipping']),
                            'r_fee'               => 0,
                            'r_item_data'         => jrCore_db_escape(json_encode($_it)),
                            'r_note'              => '',
                            'r_tag'               => ''
                        );
                        $tbl = jrCore_db_table_name('jrPayment', 'register');
                        $req = "INSERT INTO {$tbl} (" . implode(',', array_keys($_fl)) . ") VALUES ('" . implode("','", $_fl) . "')";
                        $rid = jrCore_db_query($req, 'INSERT_ID');
                        if ($rid && $rid > 0) {
                            $cnt++;
                            if ($_fl['r_seller_profile_id'] > 0) {
                                // We've successfully sold a profile item - make sure it is added to the vault
                                if (!jrPayment_add_register_item_to_vault($_fl['r_module'], $_fl['r_item_id'], $_fl['r_field'], $_it)) {
                                    jrCore_logger('CRI', "Payments: unable to copy purchased media file to system vault: {$_it['_profile_id']}/{$_fl['r_module']}/{$_fl['r_item_id']}/{$_fl['r_field']}", $_it);
                                }
                                if ($p['sale_payed_out'] == 1) {
                                    $tbl = jrCore_db_table_name('jrPayment', 'register');
                                    $req = "UPDATE {$tbl} SET r_payed_out_time = '{$p['sale_time']}' WHERE r_id = {$rid}";
                                    jrCore_db_query($req);
                                }
                            }
                        }
                    }
                }
                if ($cnt > 0 && ($cnt % 50) === 0) {
                    jrCore_form_modal_notice('update', "imported transactions: {$cnt}");
                }
            }
        }
    }

    // Import Subscription Payments
    if (isset($_post['sub_payments']) && $_post['sub_payments'] == 'on') {
        if (jrCore_module_is_active('jrSubscribe')) {
            $tbl = jrCore_db_table_name('jrFoxyCart', 'purchase');
            $req = "SELECT * FROM {$tbl} WHERE purchase_module = 'jrFoxyCart' AND purchase_field = '' ORDER BY purchase_id ASC";
            $_rt = jrCore_db_query($req, 'NUMERIC');
            if ($_rt && is_array($_rt)) {

                $_ps = array();

                jrCore_form_modal_notice('update', "checking " . jrCore_number_format(count($_rt)) . " subscription payment transactions");
                foreach ($_rt as $k => $p) {

                    // Make sure we have not already imported this transaction
                    $tbl = jrCore_db_table_name('jrPayment', 'register');
                    $req = "SELECT * FROM {$tbl} WHERE r_gateway_id = '" . jrCore_db_escape($p['purchase_txn_id']) . "' LIMIT 1";
                    $_ex = jrCore_db_query($req, 'SINGLE');
                    if ($_ex && is_array($_ex)) {
                        // Already imported
                        continue;
                    }

                    // Get the RAW transaction if we can
                    if ($_tx = jrCore_db_get_item_by_key('jrFoxyCart', 'txn_id', $p['purchase_txn_id'], true)) {
                        $_tr = array(
                            'txn_id'       => $p['purchase_txn_id'],
                            'txn_status'   => 'active',
                            'txn_date'     => $p['purchase_created'],
                            'txn_type'     => 'subscription',
                            'txn_plugin'   => 'foxycart',
                            'txn_currency' => $_conf['jrFoxyCart_store_currency'],
                            'txn_total'    => jrPayment_price_to_cents($p['purchase_price']),
                            'txn_shipping' => 0,
                            'txn_user_id'  => $p['purchase_user_id'],
                            'txn_raw'      => json_encode($_tx)
                        );
                        $_cr = array(
                            '_created'    => $p['purchase_created'],
                            '_updated'    => $p['purchase_created'],
                            '_profile_id' => 0,
                            '_user_id'    => 0
                        );
                        if ($tid = jrPayment_create_transaction($_tr, $_cr)) {

                            if ($_dt = json_decode($p['purchase_data'], true)) {
                                $_pl = false;
                                $qid = (int) $_dt['product_quota_id'];
                                if ($qid > 0) {
                                    if (!isset($_ps[$qid])) {
                                        if ($_pl = jrCore_db_get_item_by_key('jrSubscribe', 'sub_import_quota_id', $qid, true)) {
                                            $_ps[$qid] = $_pl;
                                        }
                                        else {
                                            $_ps[$qid] = 1;
                                            $_pl       = false;
                                        }
                                    }
                                    else {
                                        if (is_array($_ps[$qid])) {
                                            $_pl = $_ps[$qid];
                                        }
                                    }
                                }
                                $sub = '';
                                parse_str(parse_url($_dt['sub_token_url'], PHP_URL_QUERY), $_vars);
                                if ($_vars && isset($_vars['sub_token'])) {
                                    $sub = $_vars['sub_token'];
                                }
                                $_rd = array();
                                if (is_array($_pl)) {
                                    $_rd = $_pl;
                                }
                                if ($_us = jrCore_db_get_item('jrUser', $p['purchase_user_id'])) {
                                    $_rd = array_merge($_rd, $_us);
                                }
                                $_fl = array(
                                    'r_txn_id'            => $tid,
                                    'r_plugin'            => 'foxycart',
                                    'r_currency'          => $_conf['jrFoxyCart_store_currency'],
                                    'r_gateway_id'        => $p['purchase_txn_id'],
                                    'r_created'           => $p['purchase_created'],
                                    'r_purchase_user_id'  => (int) $p['purchase_user_id'],
                                    'r_seller_profile_id' => 0,
                                    'r_module'            => 'jrSubscribe',
                                    'r_item_id'           => $_pl['_item_id'],
                                    'r_field'             => 'sub_file',
                                    'r_quantity'          => 1,
                                    'r_amount'            => jrPayment_price_to_cents($p['purchase_price']),
                                    'r_shipping'          => 0,
                                    'r_fee'               => 0,
                                    'r_item_data'         => jrCore_db_escape(json_encode($_rd)),
                                    'r_note'              => '',
                                    'r_tag'               => $sub
                                );
                                $tbl = jrCore_db_table_name('jrPayment', 'register');
                                $req = "INSERT INTO {$tbl} (" . implode(',', array_keys($_fl)) . ") VALUES ('" . implode("','", $_fl) . "')";
                                $rid = jrCore_db_query($req, 'INSERT_ID');
                                if ($rid && $rid > 0) {
                                    $cnt++;
                                }
                            }
                        }
                        if ($cnt > 0 && ($cnt % 50) === 0) {
                            jrCore_form_modal_notice('update', "imported subscription transactions: {$cnt}");
                        }
                    }
                }
            }
        }
    }

    // Import Ledger Payments
    if (isset($_post['ledger_payments']) && $_post['ledger_payments'] == 'on') {
        $tbl = jrCore_db_table_name('jrFoxyCart', 'ledger');
        $req = "SELECT * FROM {$tbl} ORDER BY record_id ASC";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt)) {

            jrCore_form_modal_notice('update', "checking " . jrCore_number_format(count($_rt)) . " manual ledger transactions");
            foreach ($_rt as $k => $p) {

                // Make sure we have not already imported this transaction
                $tbl = jrCore_db_table_name('jrPayment', 'register');
                $req = "SELECT * FROM {$tbl} WHERE r_gateway_id = '" . jrCore_db_escape($p['record_transaction_id']) . "' LIMIT 1";
                $_ex = jrCore_db_query($req, 'SINGLE');
                if ($_ex && is_array($_ex)) {
                    // Already imported
                    continue;
                }

                // If we got an email address, see if we can link it up to a user_id
                $_tx = array(
                    'txn_user_email' => $p['record_email'],
                    'txn_id'         => $p['record_transaction_id'],
                    'txn_total'      => jrPayment_price_to_cents($p['record_income']),
                    'txn_expense'    => jrPayment_price_to_cents($p['record_expense']),
                    'txn_date'       => $p['record_time'],
                    'txn_category'   => jrCore_str_to_lower($p['record_category']),
                    'txn_note'       => $p['record_details']
                );

                if (strpos($p['record_email'], '@')) {
                    if ($_us = jrCore_db_get_item_by_key('jrUser', 'user_email', $p['record_email'], true)) {
                        $_tx['txn_user_id'] = (int) $_us['_user_id'];
                    }
                }
                if (!isset($_tx['txn_user_id'])) {
                    $_tx['txn_user_id'] = 0;
                }
                $_tx['txn_tax']      = 0;
                $_tx['txn_shipping'] = 0;
                $_tx['txn_type']     = 'payment';
                $_tx['txn_currency'] = jrPayment_run_plugin_function('get_currency_code');

                $_cr = array(
                    '_profile_id' => 0,
                    '_user_id'    => 0,
                    '_created'    => $p['record_time'],
                    '_updated'    => $p['record_time']
                );

                if (!$tid = jrCore_db_create_item('jrPayment', $_tx, $_cr)) {
                    // We had an error creating the item in the datastore
                    continue;
                }

                $_tx['_item_id']      = $tid; // use transaction DS id
                $_tx['_profile_id']   = 0;    // No profile is seller
                $_tx['cart_amount']   = $_tx['txn_total'];
                $_tx['cart_expense']  = $_tx['txn_expense'];
                $_tx['cart_shipping'] = 0;
                $_tx['cart_quantity'] = 1;
                $_tx['cart_module']   = 'jrPayment';
                $_tx['cart_field']    = 'transaction';

                // Create register entry
                jrPayment_record_sale_in_register($tid, $_tx['txn_id'], $_tx['txn_user_id'], $_tx, $_tx['txn_category'], 0, 0, $p['record_time']);
                $cnt++;

                if ($cnt > 0 && ($cnt % 50) === 0) {
                    jrCore_form_modal_notice('update', "imported manual ledger transactions: {$cnt}");
                }
            }
        }
    }

    jrCore_form_modal_notice('complete', "successfully imported " . jrCore_number_format($cnt) . " valid purchase transactions from FoxyCart eCommerce module");
    jrCore_form_delete_session();
    jrCore_db_close();
    exit;
}

//-----------------------------------
// update_quantity_save
//-----------------------------------
function view_jrPayment_update_quantity_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        $_rs = array('error', 'invalid cart_entry_id - please try again');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['qty']) || !jrCore_checktype($_post['qty'], 'number_nn')) {
        $_rs = array('error', 'invalid cart quantity - please try again');
        jrCore_json_response($_rs);
    }
    // Get user's cart
    $_cr = jrPayment_get_user_cart();
    if (!$_cr || !is_array($_cr)) {
        $_rs = array('error', 'invalid user cart - please try again');
        jrCore_json_response($_rs);
    }
    if (!isset($_cr['_items']) || !is_array($_cr['_items'])) {
        $_rs = array('error', 'no items found in cart');
        jrCore_json_response($_rs);
    }

    $eid = (int) $_post['id'];
    if (!isset($_cr['_items'][$eid])) {
        $_rs = array('error', 'invalid cart_entry_id - please try again');
        jrCore_json_response($_rs);
    }

    if ($_post['qty'] > 0) {

        // This is the requested NEW quantity
        $_cr['_items'][$eid]['cart_new_quantity'] = (int) $_post['qty'];
        $_cr['_items'][$eid]                      = jrCore_trigger_event('jrPayment', 'cart_update_quantity', $_cr['_items'][$eid], $_cr, $_cr['_items'][$eid]['cart_module']);
        $qty                                      = (int) $_cr['_items'][$eid]['cart_new_quantity'];
        if ($qty > 0) {
            if (jrPayment_update_cart_item_quantity($_cr['cart_id'], $eid, $qty)) {
                // We have successfully updated the quantity - return correct price
                $shp = ($_cr['_items'][$eid]['cart_shipping'] / $_cr['_items'][$eid]['cart_quantity']);
                $itm = ($_cr['_items'][$eid]['cart_amount'] / $_cr['_items'][$eid]['cart_quantity']);
                $_rs = array(
                    'price'    => jrPayment_currency_format($qty * $itm),
                    'shipping' => jrPayment_currency_format($qty * $shp)
                );
                if ($qty != $_post['qty']) {
                    // We had to adjust the quantity
                    $_ln             = jrUser_load_lang_strings();
                    $_rs['adjusted'] = 1;
                    $_rs['title']    = $_ln['jrPayment'][51];
                    $_rs['text']     = str_replace('%1', $qty, $_ln['jrPayment'][52]);
                }
                jrCore_json_response($_rs);
            }
        }
        else {
            // Event listener tells us there are none left
            if (jrPayment_remove_item_from_cart($_cr['cart_id'], $eid)) {
                $_ln = jrUser_load_lang_strings();
                $_rs = array(
                    'delete'   => 1,
                    'adjusted' => 1,
                    'title'    => $_ln['jrPayment'][51],
                    'text'     => str_replace('%1', 0, $_ln['jrPayment'][52])
                );
                jrCore_json_response($_rs);
            }
        }
    }
    else {
        // We are removing this item from the cart
        if (jrPayment_remove_item_from_cart($_cr['cart_id'], $eid)) {
            $_rs = array('delete' => 1);
            jrCore_json_response($_rs);
        }
    }
    $_rs = array('error', 'an error was encountered updating the cart item');
    jrCore_json_response($_rs);
}

//------------------------------
// create_register_entry
//------------------------------
function view_jrPayment_create_register_entry($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_admin_tabs('jrPayment', 'payments');
    jrCore_page_include_admin_menu();
    jrCore_page_banner('Create Payment');
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value' => 'create payment',
        'cancel'       => 'referrer'
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'     => 'txn_user_email',
        'label'    => 'email address',
        'help'     => 'Enter the email address used in this transaction',
        'type'     => 'text',
        'validate' => 'email',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'txn_id',
        'label'    => 'transaction ID',
        'help'     => 'Enter the transaction ID for this transaction',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'txn_total',
        'label'    => 'income',
        'help'     => 'Enter any income associated with this transaction',
        'type'     => 'text',
        'validate' => 'price',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'txn_expense',
        'label'    => 'expense',
        'help'     => 'Enter any expense associated with this transaction',
        'type'     => 'text',
        'validate' => 'price',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'txn_date',
        'label'    => 'date',
        'help'     => 'Enter the date this transaction occured on',
        'type'     => 'datetime',
        'validate' => 'date',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $tbl  = jrCore_db_table_name('jrPayment', 'register');
    $req  = "SELECT r_tag FROM {$tbl} WHERE r_module = 'jrPayment' GROUP BY r_tag ORDER BY r_tag ASC";
    $_rt  = jrCore_db_query($req, 'r_tag', false, 'r_tag');
    $_tmp = array(
        'name'     => 'txn_category',
        'label'    => 'category',
        'help'     => 'Enter a category for this transaction',
        'type'     => 'select_and_text',
        'options'  => $_rt,
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'txn_note',
        'label'    => 'details',
        'help'     => 'Enter any additional info about this transaction you would like to save',
        'type'     => 'textarea',
        'validate' => 'printable',
        'required' => false
    );
    jrCore_form_field_create($_tmp);
    return jrCore_page_display(true);
}

//------------------------------
// create_register_entry_save
//------------------------------
function view_jrPayment_create_register_entry_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // We have to first create the DS transaction THEN create the register entry
    $_tx = jrCore_form_get_save_data('jrPayment', 'create_register_entry', $_post);

    // If we got an email address, see if we can link it up to a user_id
    if (strpos($_tx['txn_user_email'], '@')) {
        if ($_us = jrCore_db_get_item_by_key('jrUser', 'user_email', $_tx['txn_user_email'], true)) {
            $_tx['txn_user_id'] = (int) $_us['_user_id'];
        }
    }
    if (!isset($_tx['txn_user_id'])) {
        $_tx['txn_user_id'] = 0;
    }
    $_tx['txn_total']    = jrPayment_price_to_cents($_tx['txn_total']);
    $_tx['txn_expense']  = jrPayment_price_to_cents($_tx['txn_expense']);
    $_tx['txn_tax']      = 0;
    $_tx['txn_shipping'] = 0;
    $_tx['txn_type']     = 'payment';
    $_tx['txn_currency'] = jrPayment_run_plugin_function('get_currency_code');
    $_cr                 = array(
        '_created' => $_tx['txn_date'],
        '_updated' => $_tx['txn_date']
    );
    if (!$tid = jrCore_db_create_item('jrPayment', $_tx, $_cr)) {
        // We had an error creating the item in the datastore
        jrCore_set_form_notice('error', 'an error was encountered saving the payment to the datastore - please try again');
        jrCore_form_result();
    }

    $_tx['_item_id']      = $tid; // use transaction DS id
    $_tx['_profile_id']   = 0;    // No profile is seller
    $_tx['cart_amount']   = $_tx['txn_total'];
    $_tx['cart_expense']  = $_tx['txn_expense'];
    $_tx['cart_shipping'] = 0;
    $_tx['cart_quantity'] = 1;
    $_tx['cart_module']   = 'jrPayment';
    $_tx['cart_field']    = 'transaction';

    // Create register entry
    if (jrPayment_record_sale_in_register($tid, $_tx['txn_id'], $_tx['txn_user_id'], $_tx, $_tx['txn_category'], 0, 0, $_tx['txn_date'])) {
        jrCore_form_delete_session();
        jrCore_set_form_notice('success', "The new payment transaction was successfully created");
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/payments");
    }
    jrCore_set_form_notice('error', 'an error was encountered saving the payment to the register - please try again');
    jrCore_form_result();
}

//------------------------------
// modify_register_entry
//------------------------------
function view_jrPayment_modify_register_entry($_post, $_user, $_conf)
{
    jrUser_master_only();

    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid transaction id');
        jrCore_location('referrer');
    }

    // Get register entry
    $rid = (int) $_post['_1'];
    $tbl = jrCore_db_table_name('jrPayment', 'register');
    $req = "SELECT * FROM {$tbl} WHERE r_txn_id = {$rid} LIMIT 1";
    $_tr = jrCore_db_query($req, 'SINGLE');
    if (!$_tr || !is_array($_tr)) {
        jrCore_set_form_notice('error', 'invalid transaction id - not found');
        jrCore_location('referrer');
    }
    $_rt = jrCore_db_get_item('jrPayment', $_tr['r_txn_id'], true);

    jrCore_page_admin_tabs('jrPayment', 'payments');
    jrCore_page_include_admin_menu();
    jrCore_page_banner('Modify Payment');
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/txn_detail/{$_post['_1']}",
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'  => 'register_id',
        'type'  => 'hidden',
        'value' => $_tr['r_id']
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'  => 'item_id',
        'type'  => 'hidden',
        'value' => $rid
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'txn_user_email',
        'label'    => 'email address',
        'help'     => 'Enter the email address used in this transaction',
        'type'     => 'text',
        'validate' => 'email',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'txn_id',
        'label'    => 'transaction ID',
        'help'     => 'Enter the transaction ID for this transaction',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'txn_total',
        'label'    => 'income',
        'help'     => 'Enter any income associated with this transaction',
        'type'     => 'text',
        'validate' => 'price',
        'value'    => str_replace(',', '', jrPayment_currency_format($_rt['txn_total'])),
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'txn_expense',
        'label'    => 'expense',
        'help'     => 'Enter any expense associated with this transaction',
        'type'     => 'text',
        'validate' => 'price',
        'value'    => str_replace(',', '', jrPayment_currency_format($_rt['txn_expense'])),
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'txn_date',
        'label'    => 'date',
        'help'     => 'Enter the date this transaction occured on',
        'type'     => 'datetime',
        'validate' => 'date',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $tbl  = jrCore_db_table_name('jrPayment', 'register');
    $req  = "SELECT r_tag FROM {$tbl} WHERE r_module = 'jrPayment' GROUP BY r_tag ORDER BY r_tag ASC";
    $_rt  = jrCore_db_query($req, 'r_tag', false, 'r_tag');
    $_tmp = array(
        'name'     => 'txn_category',
        'label'    => 'category',
        'help'     => 'Enter a category for this transaction',
        'type'     => 'select_and_text',
        'options'  => $_rt,
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'txn_note',
        'label'    => 'details',
        'help'     => 'Enter any additional info about this transaction you would like to save',
        'type'     => 'textarea',
        'validate' => 'printable',
        'required' => false
    );
    jrCore_form_field_create($_tmp);
    return jrCore_page_display(true);
}

//------------------------------
// modify_register_entry_save
//------------------------------
function view_jrPayment_modify_register_entry_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // We have to first create the war transaction THEN create the register entry
    $_tx = jrCore_form_get_save_data('jrPayment', 'modify_register_entry', $_post);

    // If we got an email address, see if we can link it up to a user_id
    if (strpos($_tx['txn_user_email'], '@')) {
        if ($_us = jrCore_db_get_item_by_key('jrUser', 'user_email', $_tx['txn_user_email'], true)) {
            $_tx['txn_user_id'] = (int) $_us['_user_id'];
        }
    }
    if (!isset($_tx['txn_user_id'])) {
        $_tx['txn_user_id'] = 0;
    }
    $_tx['txn_total']    = jrPayment_price_to_cents($_tx['txn_total']);
    $_tx['txn_expense']  = jrPayment_price_to_cents($_tx['txn_expense']);
    $_tx['txn_tax']      = 0;
    $_tx['txn_shipping'] = 0;
    $_tx['txn_type']     = 'payment';
    $_tx['txn_currency'] = jrPayment_run_plugin_function('get_currency_code');

    jrCore_db_update_item('jrPayment', $_post['item_id'], $_tx);

    // Update values in transaction
    $rid = (int) $_post['register_id'];
    $uid = (int) $_tx['txn_user_id'];
    $cat = jrCore_db_escape($_tx['txn_category']);
    $dat = (int) $_post['txn_date'];
    $tbl = jrCore_db_table_name('jrPayment', 'register');
    $req = "UPDATE {$tbl} SET r_created = {$dat}, r_purchase_user_id = {$uid}, r_amount = {$_tx['txn_total']}, r_expense = {$_tx['txn_expense']}, r_gateway_id = '{$_tx['txn_id']}', r_tag = '{$cat}' WHERE r_id = {$rid}";
    jrCore_db_query($req);
    jrCore_form_delete_session();
    jrCore_set_form_notice('success', "The payment transaction was successfully updated");
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/txn_detail/{$_post['item_id']}");
}

//------------------------------
// delete_register_entry_save
//------------------------------
function view_jrPayment_delete_register_entry_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid transaction id');
        jrCore_location('referrer');
    }

    // Get register entry
    $rid = (int) $_post['_1'];
    $tbl = jrCore_db_table_name('jrPayment', 'register');
    $req = "SELECT * FROM {$tbl} WHERE r_txn_id = {$rid} LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid transaction id - not found');
        jrCore_location('referrer');
    }

    // Delete
    $req = "DELETE FROM {$tbl} WHERE r_id = {$_rt['r_id']} LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!$cnt || $cnt === 0) {
        jrCore_set_form_notice('error', 'error deleting register entry - please try again');
        jrCore_location('referrer');
    }
    jrCore_db_delete_item('jrPayment', $rid, false);

    jrCore_set_form_notice('success', 'The payment transaction was successfully deleted');
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/payments");
}
