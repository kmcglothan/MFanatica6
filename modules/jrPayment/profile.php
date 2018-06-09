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
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//------------------------------
// Default
//------------------------------
function profile_view_jrPayment_default($_profile, $_post, $_user, $_conf)
{
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        return jrCore_location($_conf['jrCore_base_url'] . '/' . $_profile['profile_url']);
    }
    $url = jrCore_get_module_url('jrPayment');
    return jrCore_location($_conf['jrCore_base_url'] . '/' . $_profile['profile_url'] . '/' . $url . '/summary');
}

//------------------------------
// Payments CSV
//------------------------------
function profile_view_jrPayment_payments_csv($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrPayment');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }
    $tbl = jrCore_db_table_name('jrPayment', 'register');
    $req = "SELECT * FROM {$tbl} WHERE r_seller_profile_id = '{$_profile['_profile_id']}' AND r_hidden = 0 ORDER BY r_id DESC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (!$_rt || !is_array($_rt)) {
        jrCore_notice_page('error', 'no payments have been recorded');
    }

    $_cs = array();
    $_tm = array();
    foreach ($_rt[0] as $k => $v) {
        switch ($k) {
            case 'r_plugin':
            case 'r_field':
            case 'r_txn_id':
            case 'r_item_data':
            case 'r_payed_out_time':
            case 'r_seller_profile_id':
            case 'r_hidden':
            case 'r_note':
            case 'r_tag':
            case 'r_expense':
            case 'r_gateway_fee':
            case 'r_gateway_fee_checked':
                continue 2;
                break;
            default:
                $_tm[] = substr($k, 2);
                break;
        }
    }
    $_cs[] = implode(',', $_tm);
    foreach ($_rt as $_e) {
        $_tm = array();
        foreach ($_e as $k => $v) {
            switch ($k) {
                case 'r_plugin':
                case 'r_field':
                case 'r_txn_id':
                case 'r_item_data':
                case 'r_payed_out_time':
                case 'r_seller_profile_id':
                case 'r_hidden':
                case 'r_note':
                case 'r_tag':
                case 'r_expense':
                case 'r_gateway_fee':
                case 'r_gateway_fee_checked':
                    continue 2;
                    break;
                case 'r_created':
                    $_tm[] = jrCore_format_time($v);
                    break;
                case 'r_refunded_time':
                    if ($v > 0) {
                        $_tm[] = jrCore_format_time($v);
                    }
                    else {
                        $_tm[] = '';
                    }
                    break;
                case 'r_amount':
                case 'r_shipping':
                case 'r_fee':
                    $_tm[] = jrPayment_currency_format($v);
                    break;
                default:
                    $_tm[] = $v;
                    break;
            }
        }
        $_cs[] = implode(',', $_tm);
    }

    header('Content-type: text/csv');
    header("Content-Disposition: attachment; filename=\"Payments_Log_" . date('Ymd') . ".csv\"");
    echo implode("\n", $_cs) . "\n";
    exit;
}

//------------------------------
// Summary
//------------------------------
function profile_view_jrPayment_summary($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrPayment');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }
    jrCore_page_banner(40);

    $tax = 0;
    if (!empty($_user['quota_jrPayment_include_tax']) && $_user['quota_jrPayment_include_tax'] == 'on') {
        $tax = 'r_tax';
    }
    $shp = 0;
    if (!empty($_user['quota_jrPayment_include_shipping']) && $_user['quota_jrPayment_include_shipping'] == 'on') {
        $shp = 'r_shipping';
    }
    $_rt = false;
    $pid = (int) $_user['user_active_profile_id'];
    $off = date_offset_get(new DateTime);
    $tbl = jrCore_db_table_name('jrPayment', 'register');
    $req = "SELECT IF(r_payed_out_time > 0, 1, 0) AS p,
              FROM_UNIXTIME(r_created + {$off}, '%M %Y') AS v,
              FROM_UNIXTIME(r_created + {$off}, '%Y%m') AS c,
              SUM(((r_quantity * r_amount) + {$shp} + {$tax}) - r_refunded_amount) AS i,
              SUM(r_fee) AS f
            FROM {$tbl} WHERE r_seller_profile_id = '{$pid}' GROUP BY c, p ORDER BY c DESC";
    $_tm = jrCore_db_query($req, 'NUMERIC');
    if ($_tm && is_array($_tm)) {
        $_rt = array();
        foreach ($_tm as $_data) {
            $idx = $_data['c'];
            if (!isset($_rt[$idx])) {
                $_rt[$idx] = array(
                    'i' => 0,  // total
                    'p' => 0,  // pending
                    'c' => 0,  // cleared
                    'f' => 0   // system fee
                );
            }
            if ($_data['p'] == 1) {
                $_rt[$idx]['c'] += ($_data['i'] - $_data['f']);
            }
            else {
                $_rt[$idx]['p'] += ($_data['i'] - $_data['f']);
            }
            $_rt[$idx]['i'] += $_data['i'];
            $_rt[$idx]['f'] += $_data['f'];
            $_rt[$idx]['v'] = $_data['v'];
        }
    }
    unset($_tm);

    $_ln = jrUser_load_lang_strings();
    if ($_rt) {
        $dat             = array();
        $dat[1]['title'] = $_ln['jrPayment'][44];
        $dat[1]['class'] = 'center';
        jrCore_page_table_header($dat);

        $smarty          = new stdClass();
        $params          = array(
            'module' => 'jrPayment',
            'name'   => 'profile_monthly_report',
            'width'  => '100%',
            'height' => '300px'
        );
        $dat             = array();
        $dat[1]['title'] = smarty_function_jrGraph_embed($params, $smarty);
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
        jrCore_page_table_footer();
    }

    if ($_rt && is_array($_rt)) {
        if (isset($_conf['jrPayment_payout_clears']) && jrCore_checktype($_conf['jrPayment_payout_clears'], 'number_nz')) {
            jrCore_page_notice('success', str_replace('%1', "<b>{$_conf['jrPayment_payout_clears']}</b>", $_ln['jrPayment'][54]), false);
        }
    }

    $dat             = array();
    $dat[1]['title'] = $_ln['jrPayment'][45];
    $dat[1]['width'] = '19%';
    $dat[2]['title'] = $_ln['jrPayment'][46];
    $dat[2]['width'] = '20%';
    $dat[3]['title'] = $_ln['jrPayment'][48];
    $dat[3]['width'] = '20%';
    $dat[4]['title'] = $_ln['jrPayment'][47];
    $dat[4]['width'] = '20%';
    $dat[5]['title'] = $_ln['jrPayment'][49];
    $dat[5]['width'] = '20%';
    jrCore_page_table_header($dat);

    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $_m) {
            $dat             = array();
            $dat[1]['title'] = $_m['v'];
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_m['i']);
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_m['f']);
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_m['c']);
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_m['i'] - $_m['f']);
            $dat[5]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = $_ln['jrPayment'][50];
        $dat[1]['class'] = 'p10 center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button('referrer');
    return jrCore_page_display(true);
}

//------------------------------
// Payments
//------------------------------
function profile_view_jrPayment_payments($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrPayment');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }
    $_ln = jrUser_load_lang_strings();

    // get all purchases for this user
    $tbl = jrCore_db_table_name('jrPayment', 'register');

    if (isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz')) {
        $uid = (int) $_post['user_id'];
        $req = "SELECT * FROM {$tbl} WHERE r_seller_profile_id = '{$_profile['_profile_id']}' AND r_hidden = 0 AND r_purchase_user_id = {$uid} ORDER BY r_id DESC";
    }
    elseif (isset($_post['item']) && strpos($_post['item'], ':')) {
        list($mod, $iid) = explode(':', $_post['item']);
        $iid = (int) $iid;
        $req = "SELECT * FROM {$tbl} WHERE r_seller_profile_id = '{$_profile['_profile_id']}' AND r_hidden = 0 AND r_module = '" . jrCore_db_escape($mod) . "' AND r_item_id = {$iid} ORDER BY r_id DESC";
    }
    else {
        $req = "SELECT * FROM {$tbl} WHERE r_seller_profile_id = '{$_profile['_profile_id']}' AND r_hidden = 0 ORDER BY r_id DESC";
    }
    $page = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $page = (int) $_post['p'];
    }
    $_rt = jrCore_db_paged_query($req, $page, 12, 'NUMERIC');

    $tmp = null;
    if ($_rt && is_array($_rt) && isset($_rt['_items']) && is_array($_rt['_items'])) {
        $mrl = jrCore_get_module_url('jrPayment');
        $tmp = jrCore_page_button('csv', 'download as CSV', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$mrl}/payments_csv')");
    }
    jrCore_page_banner(23, $tmp);
    if (isset($_conf['jrPayment_payout_clears']) && jrCore_checktype($_conf['jrPayment_payout_clears'], 'number_nz')) {
        jrCore_set_form_notice('success', 'Payments with amounts in <b>bold</b> have not been cleared for payout', false);
        jrCore_get_form_notice();
    }

    // Show items
    $dat[1]['title'] = '';
    $dat[1]['width'] = '2%';
    $dat[2]['title'] = $_ln['jrPayment'][7];   // item
    if (jrUser_is_admin()) {
        $dat[2]['width'] = '35%';
    }
    else {
        $dat[2]['width'] = '40%';
    }
    $dat[3]['title'] = $_ln['jrPayment'][19];  // purchaser
    $dat[3]['width'] = '18%';
    $dat[4]['title'] = $_ln['jrPayment'][16];  // date
    $dat[4]['width'] = '20%';
    $dat[5]['title'] = $_ln['jrPayment'][9];   // amount
    $dat[5]['width'] = '10%';
    $dat[6]['title'] = $_ln['jrPayment'][20];  // fee
    $dat[6]['width'] = '10%';
    if (jrUser_is_admin()) {
        $dat[7]['title'] = $_ln['jrPayment'][21];  // detail
        $dat[7]['width'] = '5%';
    }
    jrCore_page_table_header($dat);

    if ($_rt && is_array($_rt) && isset($_rt['_items']) && is_array($_rt['_items'])) {

        // Get user info for these items
        $_ui = array();
        $_us = array();
        foreach ($_rt['_items'] as $k => $_r) {
            $uid       = (int) $_r['r_purchase_user_id'];
            $_ui[$uid] = $uid;
        }
        if (count($_ui) > 0) {
            $_ui = array(
                'search'                       => array(
                    '_item_id in ' . implode(',', $_ui)
                ),
                'privacy_check'                => false,
                'ignore_pending'               => true,
                'include_jrProfile_keys'       => true,
                'exclude_jrProfile_quota_keys' => true,
                'limit'                        => count($_ui)
            );
            $_ui = jrCore_db_search_items('jrUser', $_ui);
            if ($_ui && is_array($_ui) && isset($_ui['_items'])) {
                foreach ($_ui['_items'] as $_u) {
                    $uid       = (int) $_u['_user_id'];
                    $_us[$uid] = $_u;
                }
            }
            unset($_ui);
        }

        $clear = false;
        if (isset($_conf['jrPayment_payout_clears']) && jrCore_checktype($_conf['jrPayment_payout_clears'], 'number_nz')) {
            $clear = (time() - ($_conf['jrPayment_payout_clears'] * 86400));
        }

        $url = jrCore_get_module_url('jrPayment');
        foreach ($_rt['_items'] as $k => $_r) {

            $_r['r_item_data'] = json_decode($_r['r_item_data'], true);

            $pfx             = jrCore_db_get_prefix($_r['r_module']);
            $_im             = array(
                'crop'   => 'auto',
                'alt'    => $_r['r_item_data']["{$pfx}_title"],
                'title'  => $_r['r_item_data']["{$pfx}_title"],
                'width'  => 48,
                'height' => 48,
                '_v'     => (isset($_r['r_item_data']["{$pfx}_image_time"]) && $_r['r_item_data']["{$pfx}_image_time"] > 0) ? $_r['r_item_data']["{$pfx}_image_time"] : $_r['r_created']
            );
            $dat             = array();
            $dat[1]['title'] = jrImage_get_image_src($_r['r_module'], "{$pfx}_image", $_r['r_item_id'], 'icon', $_im);
            $dat[2]['title'] = $_r['r_item_data']["{$pfx}_title"];
            if (isset($_us["{$_r['r_purchase_user_id']}"])) {
                $dat[3]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/" . $_us["{$_r['r_purchase_user_id']}"]['profile_url'] . "\">@" . $_us["{$_r['r_purchase_user_id']}"]['profile_url'] . '</a>';
            }
            else {
                $dat[3]['title'] = '-';
            }
            $dat[4]['title'] = jrCore_format_time($_r['r_created']);
            $dat[4]['class'] = 'center';

            if ($_r['r_refunded_time'] > 0) {
                $dat[5]['title'] = '<strike>' . jrPayment_get_currency_code() . jrPayment_currency_format($_r['r_amount']) . '</strike><br><b><small>' . $_ln['jrPayment'][34] . '</small></b>';
                $dat[6]['title'] = '<strike>' . jrPayment_get_currency_code() . jrPayment_currency_format($_r['r_fee']) . '</strike>';
            }
            else {
                if ($clear) {
                    if ($_r['r_created'] < $clear) {
                        $dat[5]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_r['r_amount']);
                    }
                    else {
                        $dat[5]['title'] = '<b>' . jrPayment_get_currency_code() . jrPayment_currency_format($_r['r_amount']) . '</b>';
                    }
                }
                else {
                    $dat[5]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_r['r_amount']);
                }
                $dat[6]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_r['r_fee']);
            }
            $dat[5]['class'] = 'center';
            $dat[6]['class'] = 'center';
            if (jrUser_is_admin()) {
                $dat[7]['title'] = jrCore_page_button("d{$k}", $_ln['jrPayment'][21], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/transaction/{$_r['r_txn_id']}')");
                $dat[7]['class'] = 'center';
            }

            // Trigger so individual modules can customize their row if needed
            $dat = jrCore_trigger_event('jrPayment', 'sale_entry', $dat, $_r, $_r['r_module']);
            if ($dat && is_array($dat) && count($dat) > 0) {
                jrCore_page_table_row($dat);
            }

        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = $_ln['jrPayment'][18];
        $dat[1]['class'] = 'center p20';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    return jrCore_page_display(true);
}

//------------------------------
// Customers
//------------------------------
function profile_view_jrPayment_customers($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrPayment');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }
    $_ln = jrUser_load_lang_strings();
    jrCore_page_banner(24);
    jrCore_page_search('search', jrCore_get_current_url());

    // get all purchases ranked by user
    $tbl = jrCore_db_table_name('jrPayment', 'register');
    // Do we have a search?
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_sc = array(
            'search'              => array(
                "profile_name like %{$_post['search_string']}%"
            ),
            'skip_triggers'       => true,
            'privacy_check'       => false,
            'limit'               => 1000,
            'return_item_id_only' => true
        );
        $_sc = jrCore_db_search_items('jrUser', $_sc);
        if ($_sc && is_array($_sc)) {
            $req = "SELECT MAX(r_created) AS last, COUNT(r_id) AS purchases, SUM(r_amount) AS total, r_purchase_user_id
                      FROM {$tbl} WHERE r_seller_profile_id = '{$_profile['_profile_id']}' AND r_hidden = 0 AND r_purchase_user_id IN(" . implode(',', $_sc) . ")
                     GROUP BY r_purchase_user_id ORDER BY total DESC";
        }
    }
    else {
        $req = "SELECT MAX(r_created) AS last, COUNT(r_id) AS purchases, SUM(r_amount) AS total, r_purchase_user_id
                  FROM {$tbl} WHERE r_seller_profile_id = '{$_profile['_profile_id']}' AND r_hidden = 0
                 GROUP BY r_purchase_user_id ORDER BY total DESC";
    }
    $page = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $page = (int) $_post['p'];
    }
    if (isset($req) && strlen($req) > 0) {
        $_rt = jrCore_db_paged_query($req, $page, 12, 'NUMERIC');
    }
    else {
        $_rt = false;
    }

    // Show customers
    $dat[1]['title'] = '';
    $dat[1]['width'] = '2%';
    $dat[2]['title'] = $_ln['jrPayment'][19];  // purchaser
    $dat[2]['width'] = '58%';
    $dat[3]['title'] = $_ln['jrPayment'][16];  // last date
    $dat[3]['width'] = '20%';
    $dat[4]['title'] = $_ln['jrPayment'][15];  // purchases
    $dat[4]['width'] = '10%';
    $dat[5]['title'] = $_ln['jrPayment'][9];   // total
    $dat[5]['width'] = '10%';
    jrCore_page_table_header($dat);

    if ($_rt && is_array($_rt) && isset($_rt['_items']) && is_array($_rt['_items'])) {

        // Get user info for these items
        $_ui = array();
        $_us = array();
        foreach ($_rt['_items'] as $k => $_r) {
            $uid       = (int) $_r['r_purchase_user_id'];
            $_ui[$uid] = $uid;
        }
        if (count($_ui) > 0) {
            $_ui = array(
                'search'                 => array(
                    '_item_id in ' . implode(',', $_ui)
                ),
                'privacy_check'          => false,
                'ignore_pending'         => true,
                'include_jrProfile_keys' => true,
                'limit'                  => count($_ui)
            );
            $_ui = jrCore_db_search_items('jrUser', $_ui);
            if ($_ui && is_array($_ui) && isset($_ui['_items'])) {
                foreach ($_ui['_items'] as $_u) {
                    $uid       = (int) $_u['_user_id'];
                    $_us[$uid] = $_u;
                }
            }
            unset($_ui);
        }
        $url = jrCore_get_module_url('jrPayment');
        foreach ($_rt['_items'] as $k => $_r) {

            $_r['r_item_data'] = json_decode($_r['r_item_data'], true);
            $_usr              = false;
            if (isset($_us["{$_r['r_purchase_user_id']}"])) {
                $_usr = $_us["{$_r['r_purchase_user_id']}"];
            }
            $_im             = array(
                'crop'   => 'auto',
                'alt'    => '',
                'title'  => '',
                'width'  => 48,
                'height' => 48,
                '_v'     => (isset($_usr['user_image_time']) && $_usr['user_image_time'] > 0) ? $_usr['user_image_time'] : $_r['r_created']
            );
            $dat             = array();
            $dat[1]['title'] = jrImage_get_image_src('jrUser', 'user_image', $_usr['_user_id'], 'icon', $_im);
            $dat[2]['title'] = $_usr['user_name'] . '<br><small><a href="' . $_conf['jrCore_base_url'] . '/' . $_usr['profile_url'] . '">@' . $_usr['profile_url'] . '</a></small>';
            $dat[3]['title'] = jrCore_format_time($_r['last']);
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = jrCore_page_button("purchases-{$k}", $_r['purchases'], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$url}/payments?user_id={$_usr['_user_id']}')");
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_r['total']);
            $dat[5]['class'] = 'center';

            // Trigger so individual modules can customize their row if needed
            $dat = jrCore_trigger_event('jrPayment', 'customer_entry', $dat, $_r, $_r['r_module']);
            if ($dat && is_array($dat) && count($dat) > 0) {
                jrCore_page_table_row($dat);
            }

        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat = array();
        if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
            $dat[1]['title'] = $_ln['jrPayment'][53];
        }
        else {
            $dat[1]['title'] = $_ln['jrPayment'][18];
        }
        $dat[1]['class'] = 'center p20';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    return jrCore_page_display(true);
}

//------------------------------
// Products
//------------------------------
function profile_view_jrPayment_products($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrPayment');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }
    $_ln = jrUser_load_lang_strings();
    jrCore_page_banner(25);
    jrCore_page_search('search', jrCore_get_current_url());

    // get all purchases ranked by user
    $tbl  = jrCore_db_table_name('jrPayment', 'register');
    $req  = "SELECT MAX(r_created) AS last, COUNT(r_id) AS purchases, SUM(r_amount) AS total, r_module, r_item_id, r_item_data
               FROM {$tbl} WHERE r_seller_profile_id = '{$_profile['_profile_id']}' AND r_hidden = 0
              GROUP BY r_module, r_item_id ORDER BY total DESC";
    $page = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $page = (int) $_post['p'];
    }
    $_rt = jrCore_db_paged_query($req, $page, 12, 'NUMERIC');

    // Show customers
    $dat[1]['title'] = '';
    $dat[1]['width'] = '2%';
    $dat[2]['title'] = $_ln['jrPayment'][7];   // item
    $dat[2]['width'] = '58%';
    $dat[3]['title'] = $_ln['jrPayment'][16];  // last date
    $dat[3]['width'] = '20%';
    $dat[4]['title'] = $_ln['jrPayment'][15];  // purchases
    $dat[4]['width'] = '10%';
    $dat[5]['title'] = $_ln['jrPayment'][9];   // total
    $dat[5]['width'] = '10%';
    jrCore_page_table_header($dat);

    if ($_rt && is_array($_rt) && isset($_rt['_items']) && is_array($_rt['_items'])) {

        $url = jrCore_get_module_url('jrPayment');
        foreach ($_rt['_items'] as $k => $_r) {

            $dat               = array();
            $_r['r_item_data'] = json_decode($_r['r_item_data'], true);
            $pfx               = jrCore_db_get_prefix($_r['r_module']);
            if (isset($_r['r_item_data']["{$pfx}_image_time"]) && $_r['r_item_data']["{$pfx}_image_time"] > 0) {
                $_im             = array(
                    'crop'   => 'auto',
                    'alt'    => '',
                    'title'  => '',
                    'width'  => 48,
                    'height' => 48,
                    '_v'     => $_r['r_item_data']["{$pfx}_image_time"]
                );
                $dat[1]['title'] = jrImage_get_image_src($_r['r_module'], "{$pfx}_image", $_r['r_item_id'], 'icon', $_im);
            }
            else {
                $dat[1]['title'] = jrCore_get_module_icon_html($_r['r_module'], 48, 'payment-icon');
            }
            $dat[2]['title'] = $_r['r_item_data']["{$pfx}_title"];
            $dat[3]['title'] = jrCore_format_time($_r['last']);
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = jrCore_page_button("purchases-{$k}", $_r['purchases'], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$url}/payments?item={$_r['r_module']}:{$_r['r_item_id']}')");
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_r['total']);
            $dat[5]['class'] = 'center';

            // Trigger so individual modules can customize their row if needed
            $dat = jrCore_trigger_event('jrPayment', 'product_entry', $dat, $_r, $_r['r_module']);
            if ($dat && is_array($dat) && count($dat) > 0) {
                jrCore_page_table_row($dat);
            }

        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = $_ln['jrPayment'][18];
        $dat[1]['class'] = 'center p20';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    return jrCore_page_display(true);
}
