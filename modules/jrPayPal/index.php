<?php
/**
 * Jamroom PayPal Buy It Now module
 *
 * copyright 2017 The Jamroom Network
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

//------------------------------
// webhook
//------------------------------
function view_jrPayPal_webhook($_post, $_user, $_conf)
{
    // Validate incoming transaction
    $url = 'www.sandbox.paypal.com';
    if (isset($_conf['jrPayPal_live']) && $_conf['jrPayPal_live'] == 'on') {
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
        jrCore_logger('CRI', "jrPayPal: invalid notification received in webhook", $_post);
        header('HTTP/1.0 200 OK');
        exit;
    }
    jrCore_logger('INF', "jrPayPal: processing incoming transaction: {$_post['txn_id']}", $_post);

    // Validate business email
    if (!isset($_post['business']) || !jrCore_checktype($_post['business'], 'email')) {
        jrCore_logger('CRI', 'jrPayPal: invalid seller email in webhook', $_post);
        header('HTTP/1.0 200 OK');
        exit;
    }

    switch ($_post['txn_type']) {

        case 'web_accept':

            // Make sure seller email exists on system
            $_pf = jrCore_db_get_item_by_key('jrProfile', 'profile_paypal_email', $_post['business']);
            if (!$_pf || !is_array($_pf)) {
                jrCore_logger('CRI', "jrPayPal: invalid seller received in webhook - profile not found");
                header('HTTP/1.0 200 OK');
                exit;
            }

            // Validate our item
            list($uid, $mod, $iid) = explode('-', $_post['item_number']);

            // Make sure it is a good user
            $_us = jrCore_db_get_item('jrUser', $uid, true);
            if (!$_us || !is_array($_us)) {
                jrCore_logger('CRI', "jrPayPal: invalid user_id received in webhook: {$uid}");
                header('HTTP/1.0 200 OK');
                exit;
            }
            // Make sure it is a good item
            $_it = jrCore_db_get_item($mod, $iid);
            if (!$_it || !is_array($_it)) {
                jrCore_logger('CRI', "jrPayPal: invalid item_id received in webhook: {$iid}");
                header('HTTP/1.0 200 OK');
                exit;
            }

            if ($_post['payment_status'] != 'Completed') {
                list($sub, $msg) = jrCore_parse_email_templates('jrPayPal', 'purchase_pending', $_post);
                jrCore_send_email($_post['payer_email'], $sub, $msg);
                jrCore_logger('INF', "jrPayPal: notified {$_post['payer_email']} of pending eCheck");
                header('HTTP/1.0 200 OK');
                exit;
            }
            $pfx   = jrCore_db_get_prefix($mod);
            $_item = array(
                '_profile_id'    => $_it['_profile_id'],
                'profile_name'   => $_it['profile_name'],
                'profile_url'    => $_it['profile_url'],
                'item_title'     => $_it["{$pfx}_title"],
                'item_title_url' => $_it["{$pfx}_title_url"],
                'item_name'      => (isset($_it["{$pfx}_file_original_name"])) ? $_it["{$pfx}_file_original_name"] : $_it["{$pfx}_file_name"],
                'item_extension' => $_it["{$pfx}_file_extension"]
            );
            $_data = array(
                'paypal_number'  => $_post['item_number'],
                'paypal_user_id' => $uid,
                'paypal_module'  => $mod,
                'paypal_item_id' => $iid,
                'paypal_item'    => json_encode($_item)
            );
            $_core = array(
                '_user_id'    => $uid,
                '_profile_id' => $_us['_profile_id']
            );
            $aid   = jrCore_db_create_item('jrPayPal', $_data, $_core);
            if (!$aid) {
                jrCore_logger('CRI', "jrPayPal: unable to create paypal item for user: {$_us['user_name']}");
                header('HTTP/1.0 200 OK');
                exit;
            }
            $_rp = array(
                'paypal_downloads_url' => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/downloads"
            );
            list($sub, $msg) = jrCore_parse_email_templates('jrPayPal', 'purchase_success', $_rp);
            jrCore_send_email($_post['payer_email'], $sub, $msg);
            jrCore_logger('INF', "jrPayPal: successfully delivered {$mod} item: &quot;" . $_it["{$pfx}_title"] . "&quot; to user: {$_us['user_name']}");
            header('HTTP/1.0 200 OK');
            exit;
            break;

        default:

            if (isset($_post['payment_status']) && $_post['payment_status'] == 'Refunded') {

                // We have a refund - remove the item from the user
                list($uid, $mod, $iid) = explode('-', $_post['item_number']);
                $_it = jrCore_db_get_item_by_key('jrPayPal', 'paypal_number', $_post['item_number']);
                if ($_it) {
                    jrCore_db_delete_item('jrPayPal', $_it['_item_id']);
                }
                jrCore_logger('MIN', "jrPayPal: successfully deleted {$mod} item_id {$iid} from user_id {$uid} for refund received");
                header('HTTP/1.0 200 OK');
                exit;
            }
            break;
    }
    jrCore_logger('CRI', "jrPayPal: unable to determine transaction type in webhook");
    header('HTTP/1.0 200 OK');
    exit;
}

//------------------------------
// checkout
//------------------------------
function view_jrPayPal_checkout($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['_1']) || !jrCore_module_is_active($_post['_1'])) {
        jrCore_notice_page('error', 'Invalid module');
    }
    if (!isset($_post['_2']) || !jrCore_checktype($_post['_2'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid item_id');
    }
    $_it = jrCore_db_get_item($_post['_1'], $_post['_2']);
    if (!$_it || !is_array($_it)) {
        jrCore_notice_page('error', 'Invalid item');
    }

    // See if this user has already bought this item
    $_ex = jrCore_db_get_item_by_key('jrPayPal', 'paypal_number', "{$_user['_user_id']}-{$_post['_1']}-{$_post['_2']}");
    if ($_ex && is_array($_ex)) {
        $_ln = jrUser_load_lang_strings();
        jrCore_set_form_notice('success', $_ln['jrPayPal'][1]);
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/downloads");
    }
    if (!$url = jrPayPal_create_buy_now_url($_post['_1'], $_it)) {
        jrCore_notice_page('error', 'Unable to generate buy now URL');
    }
    jrCore_location($url);
}

//------------------------------
// downloads
//------------------------------
function view_jrPayPal_downloads($_post, $_user, $_conf)
{
    jrUser_session_require_login();

    if (jrUser_is_admin() && isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz')) {
        $_us = jrCore_db_get_item('jrUser', $_post['user_id']);
        if ($_us['user_name'] != $_user['user_name']) {
            jrCore_set_form_notice('notice', "You are viewing the downloads for the user <strong>{$_us['user_name']}</strong>", false);
        }
    }
    else {
        $_us = $_user;
    }

    jrUser_account_tabs('downloads');

    // Get language strings
    $_lang = jrUser_load_lang_strings();

    // List all items
    $button = jrCore_page_button('p', $_us['profile_name'], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_us['profile_url']}')");
    jrCore_page_banner(2, $button);
    jrCore_get_form_notice();

    // get all items that have been purchased
    $_sc = array(
        'search'         => array(
            "paypal_user_id = {$_us['_user_id']}"
        ),
        'order_by'       => array('_created' => 'numerical_desc'),
        'skip_triggers'  => true,
        'privacy_check'  => false,
        'ignore_pending' => true,
        'no_cache'       => true,
        'pagebreak'      => 12,
        'page'           => (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) ? (int) $_post['p'] : 1
    );
    $_rt = jrCore_db_search_items('jrPayPal', $_sc);
    if (isset($_rt['_items']) && is_array($_rt['_items'])) {

        $dat             = array();
        $dat[1]['title'] = '';
        $dat[1]['width'] = '2%';
        $dat[2]['title'] = $_lang['jrPayPal'][3];
        $dat[2]['width'] = '73%';
        $dat[3]['title'] = $_lang['jrPayPal'][4];
        $dat[3]['width'] = '20%';
        $dat[4]['title'] = $_lang['jrPayPal'][5];
        $dat[4]['width'] = '5%';
        jrCore_page_table_header($dat);

        foreach ($_rt['_items'] as $k => $_it) {
            $_pr             = json_decode($_it['paypal_item'], true);
            $url             = jrCore_get_module_url($_it['paypal_module']);
            $pfx             = jrCore_db_get_prefix($_it['paypal_module']);
            $dat             = array();
            $dat[1]['title'] = "<img src=\"{$_conf['jrCore_base_url']}/{$url}/image/{$pfx}_image/{$_it['paypal_item_id']}/small/crop=auto\" width=\"32\" height=\"32\" alt=\"{$_pr['item_title']}\">";
            $dat[2]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_pr['profile_url']}/{$url}/{$_it['paypal_item_id']}/{$_pr['item_title_url']}\">{$_pr['item_title']}</a><br><a href=\"{$_conf['jrCore_base_url']}/{$_pr['profile_url']}\">@{$_pr['profile_url']}</a>";
            $dat[3]['title'] = jrCore_format_time($_it['_created']);
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = jrCore_page_button("d{$k}", $_lang['jrPayPal'][5], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/download/{$_it['_item_id']}')");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = "<p>{$_lang['jrPayPal'][6]}</p>";
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_display();
}

//------------------------------
// download
//------------------------------
function view_jrPayPal_download($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    // Make sure this user bought this item
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice('Error', 'Invalid paypal id provided');
    }
    $_ex = jrCore_db_get_item('jrPayPal', $_post['_1'], true);
    if (!$_ex || !is_array($_ex)) {
        jrCore_notice('Error', 'It does not appear you have purchased this item');
    }
    $_pr = json_decode($_ex['paypal_item'], true);
    $pfx = jrCore_db_get_prefix($_ex['paypal_module']);
    $nam = "{$_ex['paypal_module']}_{$_ex['paypal_item_id']}_{$pfx}_file." . $_pr['item_extension'];
    jrCore_media_file_download($_pr['_profile_id'], $nam, $_pr['item_name']);
    session_write_close();
    exit();
}
