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
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//------------------------------
// web hook (data feed URL)
//------------------------------
function view_jrFoxyCart_webhook($_post, $_user, $_conf)
{
    // Transaction DataFeed
    if (isset($_post['FoxyData']) && strlen($_post['FoxyData']) > 0) {
        $_xml = jrFoxyCart_decode_xml_transaction($_post['FoxyData']);
        if ($_xml && is_array($_xml)) {
            // Process the Transaction
            if (jrFoxyCart_process_transaction($_xml)) {
                echo 'foxy'; // We're good
                exit;
            }
        }
    }

    // Subscription DataFeed
    elseif (isset($_post['FoxySubscriptionData']) && strlen($_post['FoxySubscriptionData']) > 0) {

        $_xml = jrFoxyCart_decode_xml_subscription($_post['FoxySubscriptionData']);
        if ($_xml && is_array($_xml)) {
            jrCore_logger('INF', 'jrFoxyCart: processing daily subscription feed', $_xml);

            // Trigger subscription DataFeed
            $_xml = jrCore_trigger_event('jrFoxyCart', 'subscription_datafeed', $_xml);

            // process subscriptions that are expired
            if (isset($_xml['subscriptions']['subscription'])) {
                jrFoxyCart_process_expired_subscriptions($_xml['subscriptions']['subscription']);
            }
            // TO DO: other sections of subscription DataFeed
            echo 'foxy';
            exit;
        }
        else {
            jrCore_logger('CRI', 'jrFoxyCart: error decoding XML subscription information', $_xml);
        }
    }

    // Fall through - Error
    header('HTTP/1.1 400 Bad Request');
    jrCore_logger('CRI', 'jrFoxyCart: invalid or corrupted data received in POST', $_post);
    jrCore_notice('CRI', 'ERROR: Invalid or corrupt data received in POST');
    exit;
}

//------------------------------
// foxy sso (single sign on)
// this gets fired between "continue to checkout"
// and arriving at foxycart. nothing is displayed to screen
// its just a redirect to tag the checkout with the
// customers ID.
//------------------------------
function view_jrFoxyCart_sso($_post, $_user, $_conf)
{
    //see if force_login is on, check if they are logged in
    if (!jrUser_is_logged_in() && isset($_conf['jrFoxyCart_force_login']) && $_conf['jrFoxyCart_force_login'] == 'on') {
        // redirect to the login page first.
        $_ln = jrUser_load_lang_strings();
        jrCore_set_form_notice('success', $_ln['jrFoxyCart'][113], false);
        jrUser_save_location();
        $murl = jrCore_get_module_url('jrUser');
        jrCore_location("{$_conf['jrCore_base_url']}/{$murl}/login");
    }

    $fcsid       = $_post['fcsid']; // foxy cart session id
    $timestamp   = (time() + 3600);
    $customer_id = 0;

    // If our user is logged in, we need a valid FC user ID
    if (jrUser_is_logged_in()) {

        if (isset($_user['user_foxycart_customer_id']) && jrCore_checktype($_user['user_foxycart_customer_id'], 'number_nz')) {

            $customer_id = $_user['user_foxycart_customer_id'];

            //check that the user current email address matches the one foxycart has, if it doesn't then update foxycart to their new email address.
            $_rs = array(
                'api_action'  => 'customer_get',
                'customer_id' => $customer_id
            );
            $res = jrFoxyCart_api($_rs);
            $res = json_decode(json_encode($res), true);
            if (isset($res['customer_email']) && jrCore_checktype($res['customer_email'], 'email') && $res['customer_email'] != $_user['user_email']) {
                // update the users email.
                $_rs = array(
                    'api_action'     => 'customer_save',
                    'customer_id'    => $customer_id,
                    'customer_email' => $_user['user_email']
                );
                $res = jrFoxyCart_api($_rs);
                $res = json_decode(json_encode($res), true);
                if ((isset($res['result']) && $res['result'] == 'ERROR') || $res === false) {

                    // See if they already have an account on the new email
                    $_r2 = array(
                        'api_action'     => 'customer_get',
                        'customer_email' => $_user['user_email']
                    );
                    $re2 = jrFoxyCart_api($_r2);
                    $re2 = json_decode(json_encode($re2), true);
                    if ($re2 && is_array($re2) && isset($re2['customer_id'])) {
                        // Update
                        $_up = array(
                            'user_foxycart_customer_id' => $re2['customer_id']
                        );
                        jrCore_db_update_item('jrUser', $_user['_user_id'], $_up);
                        jrUser_session_sync($_user['_user_id']);
                        $customer_id = $re2['customer_id'];
                    }
                    else {
                        $debug = array(
                            '_rs'      => $_rs,
                            'response' => $res
                        );
                        jrCore_logger('CRI', 'jrFoxyCart_sso: error updating user email address', $debug);
                        jrCore_notice_page('error', 'An error was encountered checking out - we apologize for the inconvenience - please try again');
                    }
                }
            }

        }
        else {

            // We need to create an account
            $_rs = array(
                'api_action'        => 'customer_save',
                'customer_email'    => $_user['user_email'],
                'customer_password' => substr(md5(microtime()), 0, 8)
            );
            $res = jrFoxyCart_api($_rs);
            if ($res === false) {
                $debug = array(
                    'status'   => 'User is logged in and does not have a foxycart_id number.',
                    '_user'    => $_user,
                    '_rs'      => $_rs,
                    'response' => $res
                );
                jrCore_logger('CRI', 'jrFoxyCart: failed to create foxycart user account', $debug);
                jrCore_notice_page('error', 'An error was encountered checking out - we apologize for the inconvenience - please try again (2)');
            }
            // Update user account with ID
            $customer_id = (int) $res->customer_id;
            $_data       = array(
                'user_foxycart_customer_id' => $customer_id
            );
            jrCore_db_update_item('jrUser', $_user['_user_id'], $_data);
        }
    }

    //-----------------------------------------------------------------------------
    // See if we have a customer
    //-----------------------------------------------------------------------------
    $key = '';
    if ($customer_id === 0) {
        // We are not logged in.  We are going to create a special login token
        $key = md5(microtime());
        $_rs = array(
            'h:extra' => $key,
            'fcsid'   => $fcsid,
            'output'  => 'json'
        );
        $res = jrFoxyCart_cart($_rs);
        // The following if block will log any errors
        if ($res['custom_fields']['extra'] == $key) {
            //if the user_login_key got set, then continue with allowing auto login.
            if (!jrCore_set_cookie('new_account_key', $key)) {
                jrCore_logger('INF', 'jrFoxyCart: tried to set a cookie to autolog the user back in on return but failed.');
            }
            if (!jrCore_set_temp_value('jrFoxyCart', $key, 'new_account_key')) {
                jrCore_logger('INF', 'jrFoxyCart: tried to record the cookie to the DB to autolog the user back in on return but failed.');
            }
        }
    }
    else {

        // we have a customer id, so lets check it exists in our shop.
        $_rs = array(
            'api_action'  => 'customer_get',
            'customer_id' => $customer_id
        );
        $xml = jrFoxyCart_api($_rs);
        if ($xml->result == "ERROR") {
            // error, so try the email.
            $_rs = array(
                'api_action'     => 'customer_get',
                'customer_email' => $_user['user_email']
            );
            $xml = jrFoxyCart_api($_rs);
            if ($xml->result == "SUCCESS") {
                $customer_id = $xml->customer_id;
                // Update the customers id number. (this could occur if the admin has a test store, deletes it, then creates another store. )
                // the customer would be left with a customer_id from an old store.
                $_temp = array(
                    'user_foxycart_customer_id' => $customer_id
                );
                $_core = array(
                    '_user_id' => $_user['_user_id']
                );
                jrCore_db_update_item('jrUser', $_user['_user_id'], $_temp, $_core);
            }
            elseif (jrUser_is_logged_in() && isset($_user['user_foxycart_customer_id'])) {

                // they have a foxycart user id that is not found on the foxycart store.
                // create their account on foxycart and update their ID here before sending them to checkout.
                $_rs = array(
                    'api_action'        => 'customer_save',
                    'customer_email'    => $_user['user_email'],
                    'customer_password' => $_user['user_password']
                );
                $res = jrFoxyCart_api($_rs);
                if (!$res) {
                    $debug = array(
                        'status'   => 'User is logged in and has foxycart_id number, but is not found in foxycart store',
                        '_user'    => $_user,
                        '_rs'      => $_rs,
                        'response' => $res
                    );
                    jrCore_logger('CRI', 'jrFoxyCart: failed to retrieve user account for user from FoxyCart', $debug);
                }
                else {
                    $customer_id = (int) $res->customer_id;
                    $_data = array(
                        'user_foxycart_customer_id' => $customer_id
                    );
                    jrCore_db_update_item('jrUser', $_user['_user_id'], $_data);
                }
            }
        }
    }

    // docs http://docs.foxycart.com/v/1.0/sso#the_details
    $auth_token = sha1($customer_id . '|' . $timestamp . '|' . $_conf['jrFoxyCart_api_key']);

    // then redirect to foxycart
    $url = 'https://' . $_conf['jrFoxyCart_store_name_url'] . '.foxycart.com/checkout?fc_auth_token=' . $auth_token . '&fcsid=' . $fcsid . '&fc_customer_id=' . $customer_id . '&timestamp=' . $timestamp . '&h:extra=' . $key;
    jrCore_location($url);

}

//-----------------------------------
// return from purchase
//-----------------------------------
function view_jrFoxyCart_return($_post, $_user, $_conf)
{
    // User must log in
    $_ln = jrUser_load_lang_strings();
    if (!jrUser_is_logged_in()) {

        // See if we had an account created cookie
        $_us = false;
        if ($key = jrCore_get_cookie('new_account_key')) {
            jrCore_delete_cookie('new_account_key');
            // We have a new account key - should be in TMP and user DB
            if ($tmp = jrCore_get_temp_value('jrFoxyCart', $key)) {
                jrCore_delete_temp_value('jrFoxyCart', $key);
                // We have a valid new account key
                $_us = jrCore_db_get_item_by_key('jrUser', 'user_new_account_key', $key, false, true);
                if ($_us && is_array($_us)) {
                    jrCore_db_delete_item_key('jrUser', $_us['_user_id'], 'user_new_account_key');
                    // Log the user in to their account
                    global $_user;
                    $_SESSION = $_us;
                    $_user    = $_SESSION;
                }
            }
        }
        if (!$_us) {
            jrUser_save_location();
            jrCore_set_form_notice('notice', $_ln['jrFoxyCart'][123], false);
            $url = jrCore_get_module_url('jrUser');
            jrCore_location("{$_conf['jrCore_base_url']}/{$url}/login");
        }
    }

    // See if we just started a SUBSCRIPTION or purchased an ITEM
    if ($tmp = jrCore_get_cookie('sub_quota_id')) {

        jrCore_delete_cookie('sub_quota_id');
        // Make sure our incoming quota cookie MATCHES what our
        // account is otherwise it could be an invalid quota cookie
        // amd will cause account / login issues
        if ($tmp == $_user['profile_quota_id']) {

            // We have started a subscription - reload
            jrCore_delete_cache('jrFoxyCart', 'subscription_list');
            jrUser_session_sync($_user['_user_id']);
            jrCore_set_form_notice('success', $_ln['jrFoxyCart'][110]);
            jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/subscription_browser");
        }
    }

    // Fall through - we have made an item purchase
    jrCore_set_form_notice('success', $_ln['jrFoxyCart'][111]);
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/items");
}

//-----------------------------------
// items (that have been purchased)
//-----------------------------------
function view_jrFoxyCart_items($_post, $_user, $_conf)
{
    // Must be logged in to create a new youtube file
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
        jrUser_account_tabs('items', $_us);
    }
    else {
        jrUser_account_tabs('items');
    }

    // Get language strings
    $_lang = jrUser_load_lang_strings();

    // List all items
    $button = jrCore_page_button('p', $_us['profile_name'], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_us['profile_url']}')");
    jrCore_page_banner(6, $button);
    jrCore_get_form_notice();

    // get all items that have been purchased
    $tbl = jrCore_db_table_name('jrFoxyCart', 'purchase');
    $req = "SELECT *, CONCAT(purchase_item_id, purchase_txn_id) AS gb FROM {$tbl} WHERE purchase_user_id = '{$_us['_user_id']}' AND purchase_module != 'jrFoxyCart' GROUP BY gb ORDER BY purchase_created desc";
    // find how many lines we are showing
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $_rt = jrCore_db_paged_query($req, $_post['p'], 12, 'NUMERIC');

    if (isset($_rt['_items']) && is_array($_rt['_items'])) {

        // Show items
        $dat             = array();
        if (jrUser_is_admin()) {
            $dat[1]['title'] = '<input type="checkbox" class="form_checkbox" onclick="$(\'.lc_checkbox\').prop(\'checked\',$(this).prop(\'checked\'));">';
            $dat[1]['width'] = '1%';
            $dat[2]['title'] = '';
            $dat[2]['width'] = '1%';
        }
        else {
            $dat[2]['title'] = '';
            $dat[2]['width'] = '2%';
        }
        $dat[3]['title'] = $_lang['jrFoxyCart'][8];
        $dat[3]['width'] = '53%';
        $dat[4]['title'] = $_lang['jrFoxyCart'][9];
        $dat[4]['width'] = '20%';
        $dat[5]['title'] = $_lang['jrFoxyCart'][10];
        $dat[5]['width'] = '20%';
        $dat[6]['title'] = '&nbsp;'; // $_lang['jrFoxyCart'][4]; = 'download' but this col is also used for other buttons.
        $dat[6]['width'] = '5%';
        jrCore_page_table_header($dat);

        $_all = array();
        $_pr  = array();
        foreach ($_rt['_items'] as $_p) {

            // Information about the item purchase
            $_d = json_decode($_p['purchase_data'], true);
            if (!isset($_d['profile_name'])) {
                if (!isset($_pr["{$_p['purchase_seller_profile_id']}"])) {
                    $_pr["{$_p['purchase_seller_profile_id']}"] = jrCore_db_get_item('jrProfile', $_p['purchase_seller_profile_id'], true);
                }
                if (is_array($_pr["{$_p['purchase_seller_profile_id']}"])) {
                    $_d = array_merge($_pr["{$_p['purchase_seller_profile_id']}"], $_d);
                }
            }

            // Default row
            $dat = array();
            if (jrUser_is_admin()) {
                $dat[1]['title'] = '<input type="checkbox" class="form_checkbox lc_checkbox" name="' . $_p['purchase_id'] . '">';
                $dat[1]['class'] = 'center';
            }
            // get each module to fill this row in for their item.
            $url = jrCore_get_module_url($_p['purchase_module']);
            $pfx = jrCore_db_get_prefix($_p['purchase_module']);
            if ($pfx && isset($_d["{$pfx}_title"])) {
                $_im = array(
                    'crop'  => 'auto',
                    'alt'   => $_d["{$pfx}_title"],
                    'title' => $_d["{$pfx}_title"],
                    'width' => 32,
                    '_v'    => (isset($_d["{$pfx}_image_time"]) && $_d["{$pfx}_image_time"] > 0) ? $_d["{$pfx}_image_time"] : $_p['purchase_created']
                );
                $dat[2]['title'] = jrImage_get_image_src($_p['purchase_module'], "{$pfx}_image", $_p['purchase_item_id'], 'xsmall', $_im);
                $dat[3]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_d['profile_url']}\">{$_d['profile_name']}</a> - <a href=\"{$_conf['jrCore_base_url']}/{$_d['profile_url']}/{$url}/{$_p['purchase_item_id']}/" . $_d["{$pfx}_title_url"] . "\">" . $_d["{$pfx}_title"] . '</a>';
                $dat[6]['title'] = jrCore_page_button("a{$_p['purchase_item_id']}", 'download', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/vault_download/{$pfx}_file/{$_p['purchase_item_id']}')");
            }
            else {
                $dat[2]['title'] = '';
                $dat[2]['class'] = 'center';
                $dat[3]['title'] = '';
                $dat[6]['title'] = '';
            }
            $dat[4]['title'] = jrCore_format_time($_p['purchase_created']);
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = $_p['purchase_txn_id'];
            $dat[5]['class'] = 'center';
            $dat[6]['class'] = 'center';

            // Since we know our module here, we can just trigger it's event listener directly
            // without having to load and go through all the other modules that might be listening
            $_d = array_merge($_d, $_p);

            // each module should over-ride their row settings with what they actually want to display here.
            $dat = jrCore_trigger_event('jrFoxyCart', 'my_items_row', $dat, $_d, $_p['purchase_module']);
            if ($dat && is_array($dat) && count($dat) > 0) {
                jrCore_page_table_row($dat);
                $_all[] = $_p['purchase_module'] . '|' . $_p['purchase_field'] . '|' . $_p['purchase_item_id'];
            }

        }
        if (jrUser_is_admin()) {
            $sjs             = "var v = $('input:checkbox.lc_checkbox:checked').map(function(){ return this.name; }).get().join(',')";
            $dat             = array();
            $dat[1]['title'] = jrCore_page_button("delete", 'delete checked', "if (confirm('Are you sure you want to delete all checked licenses?')){ {$sjs};jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/item_delete/id='+ v )}");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>' . $_lang['jrFoxyCart'][3] . '</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_display();
}

//--------------------------------------------------------------
// item_delete
//--------------------------------------------------------------
function view_jrFoxyCart_item_delete($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();
    $_id = false;
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        // See if we are getting multiple id's
        if (strpos($_post['id'], ',')) {
            $_id = explode(',', $_post['id']);
            foreach ($_id as $id) {
                if (!jrCore_checktype($id, 'number_nz')) {
                    jrCore_set_form_notice('error', 'Invalid license id - please try again');
                    jrCore_location('referrer');
                }
            }
        }
    }
    else {
        $_id = array($_post['id']);
    }
    if (!$_id || count($_id) === 0) {
        jrCore_set_form_notice('error', 'Invalid license id - please try again');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrFoxyCart', 'purchase');
    foreach ($_id as $num) {
        $req = "SELECT * FROM {$tbl} WHERE purchase_id = '{$num}' LIMIT 1";
        $_cd = jrCore_db_query($req, 'SINGLE');
        if (!isset($_cd) || !is_array($_cd)) {
            jrCore_set_form_notice('error', 'Invalid license id (2)');
            jrCore_location('referrer');
        }
        // Delete it
        $req = "DELETE FROM {$tbl} WHERE purchase_id = '{$num}' LIMIT 1";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (!isset($cnt) || $cnt !== 1) {
            jrCore_set_form_notice('error', 'Error deleting the license code in database - please try again');
            jrCore_location('referrer');
        }
        $_cd['purchase_data'] = json_decode($_cd['purchase_data'], true);
        jrCore_logger('INF', "succesfully deleted purchase: {$_cd['purchase_txn_id']}", $_cd);
    }
    jrCore_set_form_notice('success', "The license(s) were successfully deleted");
    jrCore_location('referrer');
}

//--------------------------------
// download_all
//--------------------------------
function view_jrFoxyCart_download_all($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_SESSION['JRFOXYCART_DOWNLOAD_ALL_SET']) || !is_array($_SESSION['JRFOXYCART_DOWNLOAD_ALL_SET'])) {
        jrCore_notice('CRI', 'Invalid download set - please make sure you are downloading from your My Items section');
    }
    jrCore_stream_zip('my_items_download.zip', $_SESSION['JRFOXYCART_DOWNLOAD_ALL_SET']);
}

//--------------------------------------------------------------
// Show previous subscription transactions for a user
//--------------------------------------------------------------
function view_jrFoxyCart_subscription_history($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (jrUser_is_master()) {
        jrCore_page_include_admin_menu();
        jrCore_page_admin_tabs('jrFoxyCart');
    }
    if (jrUser_is_logged_in()) {
        jrUser_account_tabs('subscription_browser');
    }

    if (!isset($_user['user_foxycart_customer_id']) || strlen($_user['user_foxycart_customer_id']) < 2) {
        jrCore_notice_page('error', 'There does not appear to be any subscription transactions for your account');
    }

    // Get history from FoxyCart
    $_rs = array(
        'api_action'         => 'subscription_list',
        'is_active_filter'   => '',
        'customer_id_filter' => $_user['user_foxycart_customer_id']
    );
    $xml = jrFoxyCart_api($_rs);
    $_ln = jrUser_load_lang_strings();

    jrCore_page_banner($_ln['jrFoxyCart'][93]);
    jrCore_get_form_notice();
    jrCore_page_section_header($_ln['jrFoxyCart'][93]); // Subscription History

    //header
    $dat             = array();
    $dat[1]['title'] = $_ln['jrFoxyCart'][94]; // Transaction Id
    $dat[1]['width'] = '23%';
    $dat[2]['title'] = $_ln['jrFoxyCart'][95]; // Start Date
    $dat[2]['width'] = '12%';
    $dat[3]['title'] = $_ln['jrFoxyCart'][96]; // Next Transaction
    $dat[3]['width'] = '12%';
    $dat[4]['title'] = $_ln['jrFoxyCart'][97]; // End Date
    $dat[4]['width'] = '12%';
    $dat[5]['title'] = $_ln['jrFoxyCart'][98]; // Past Due Amount
    $dat[5]['width'] = '12%';
    $dat[6]['title'] = $_ln['jrFoxyCart'][99]; // Product Name
    $dat[6]['width'] = '23%';
    $dat[7]['title'] = 'details'; // +/-
    $dat[7]['width'] = '6%';
    jrCore_page_table_header($dat);

    foreach ($xml->subscriptions->subscription as $_sub) {

        $dat             = array();
        $dat[1]['title'] = $_sub->original_transaction_id; // Transaction Id
        $dat[1]['class'] = "center";
        $dat[2]['title'] = $_sub->start_date; // Start Date
        $dat[2]['class'] = "center";
        $dat[3]['title'] = $_sub->next_transaction_date; // Next Transaction
        $dat[3]['class'] = "center";
        if ($_sub->end_date == '0000-00-00') {
            $dat[4]['title'] = '-';
        }
        else {
            $dat[4]['title'] = $_sub->end_date; // End Date
        }
        $dat[4]['class'] = "center";
        $dat[5]['title'] = $_sub->past_due_amount; // Past Due Amount
        $dat[5]['class'] = "center";
        $name            = '';
        //purchased subscriptions. (should only be 1 but it comes back in an array, so expanding it.)
        foreach ($_sub->transaction_template->transaction_details->transaction_detail as $_tr) {
            $name = $_tr->product_name . '<br>';
        }
        $dat[6]['title'] = $name; // Product Name
        $dat[6]['class'] = "center";
        $dat[7]['title'] = jrCore_page_button("h", '+/-', '$(\'#details' . $_sub->sub_token . '\').slideToggle();'); // Product Name
        $dat[7]['class'] = 'center';
        $class           = '';
        if (isset($_post['highlight']) && strlen($_post['highlight']) > 5 && $_post['highlight'] == $_sub->sub_token) {
            $class = 'highlight';
        }
        jrCore_page_table_row($dat, $class);

        //details row
        $dat      = array();
        $_replace = array(
            'subscription' => $_sub
        );

        // Get profiles we have access to
        $_sc = array(
            'search'         => array("_profile_id in {$_user['user_linked_profile_ids']}"),
            'group_by'       => "_item_id",
            'order_by'       => array(
                'profile_name' => 'ASC'
            ),
            'limit'          => 250,
            'skip_triggers'  => true,
            'privacy_check'  => false,
            'ignore_pending' => true
        );
        $_rt = jrCore_db_search_items('jrProfile', $_sc);
        if (is_array($_rt['_items'])) {
            $_replace['profiles']       = $_rt['_items'];
            $_replace['profiles_count'] = $_rt['info']['total_items'];
        }

        $dat[1]['title'] = jrCore_parse_template("subscription_details.tpl", $_replace, 'jrFoxyCart');
        $rowclass        = '" id="details' . $_sub->sub_token . '" style="display:none';
        jrCore_page_table_row($dat, $rowclass);

    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button('referrer');
    jrCore_page_display();
}

//--------------------------------------------------------------
// Show existing and available subscriptions
//--------------------------------------------------------------
function view_jrFoxyCart_subscription_browser($_post, $_user, $_conf)
{
    jrUser_session_require_login();

    // make sure we get a good profile_id
    if (!isset($_post['profile_id']) || !jrCore_checktype($_post['profile_id'], 'number_nz')) {
        $_post['profile_id'] = (int) $_SESSION['user_active_profile_id'];
    }
    if (!jrUser_is_admin()) {
        $tbl = jrCore_db_table_name('jrProfile', 'profile_link');
        $req = "SELECT * FROM {$tbl} WHERE user_id = '" . intval($_user['_user_id']) . "' AND profile_id = '{$_post['profile_id']}' LIMIT 1";
        $_pr = jrCore_db_query($req, 'SINGLE');
        if (!isset($_pr) || !is_array($_pr)) {
            jrUser_not_authorized();
        }
    }
    $_profile = jrCore_db_get_item('jrProfile', $_post['profile_id']);

    if (jrUser_is_admin()) {
        jrUser_account_tabs('subscription_browser', $_profile);
    }
    else {
        jrUser_account_tabs('subscription_browser');
    }

    // See if we are viewing for a different user...
    if (jrUser_is_admin() && isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz')) {
        $_us = jrCore_db_get_item('jrUser', $_post['user_id']);
    }
    else {
        $_us = $_user;
    }
    if (jrUser_is_admin() && $_us['user_name'] != $_user['user_name']) {
        jrCore_set_form_notice('notice', "You are viewing the subscription options for the user <strong>{$_us['user_name']}</strong>", false);
    }

    $_ln    = jrUser_load_lang_strings();
    $button = jrCore_page_button('p', $_us['profile_name'], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_us['profile_url']}')");
    jrCore_page_banner(34, $button);
    jrCore_get_form_notice();

    $dat             = array();
    $dat[1]['title'] = $_ln['jrFoxyCart'][85];
    $dat[1]['width'] = '20%;';
    $dat[1]['class'] = 'sprite_icon_hilighted';
    $dat[2]['title'] = $_ln['jrFoxyCart'][104];
    $dat[2]['width'] = '40%;';
    $dat[2]['class'] = 'sprite_icon_hilighted';
    $dat[3]['title'] = $_ln['jrFoxyCart'][118];
    $dat[3]['width'] = '10%;';
    $dat[3]['class'] = 'sprite_icon_hilighted';
    $dat[4]['title'] = $_ln['jrFoxyCart'][91];
    $dat[4]['width'] = '10%;';
    $dat[4]['class'] = 'sprite_icon_hilighted';
    $dat[5]['title'] = $_ln['jrFoxyCart'][89];
    $dat[5]['width'] = '10%;';
    $dat[5]['class'] = 'sprite_icon_hilighted';
    $dat[6]['title'] = $_ln['jrFoxyCart'][93];
    $dat[6]['width'] = '10%;';
    $dat[6]['class'] = 'sprite_icon_hilighted';
    jrCore_page_table_header($dat);

    $len = '-';
    $prc = '-';
    $tkn = false;
    $exp = false;
    $y   = $m = $d = false;

    // See if we are already on a paid plan
    if (isset($_us['quota_jrFoxyCart_subscription_allow']) && $_us['quota_jrFoxyCart_subscription_allow'] == 'on' && isset($_us['quota_jrFoxyCart_subscription_price']) && $_us['quota_jrFoxyCart_subscription_price'] > 0) {

        // Get our subscription token if this user already has a subscription
        if (isset($_us['user_foxycart_customer_id']) && strlen($_us['user_foxycart_customer_id']) > 0) {

            // This user is already on a subscription - get info
            $tkn = jrFoxyCart_get_subscription_token($_us['user_foxycart_customer_id']);

            // See if we have cancelled
            $tbl = jrCore_db_table_name('jrFoxyCart', 'sub_expire');
            $req = "SELECT * FROM {$tbl} WHERE sub_profile_id = '{$_us['_profile_id']}'";
            $_ex = jrCore_db_query($req, 'SINGLE');
            $exp = '';
            if (is_array($_ex)) {
                $y   = substr($_ex['sub_expire_date'], 0, 4);
                $m   = substr($_ex['sub_expire_date'], 4, 2);
                $d   = substr($_ex['sub_expire_date'], 6, 2);
                $exp = "<br><br>{$_ln['jrFoxyCart'][108]} {$y}-{$m}-{$d}";
            }
            else {
                // Active sub - get info from FoxyCart
                $_rs = array(
                    'api_action' => 'subscription_get',
                    'sub_token'  => $tkn
                );
                $res = jrFoxyCart_api($_rs);
                if ($res && isset($res->subscription->next_transaction_date)) {
                    $dat = (string) $res->subscription->next_transaction_date;
                    $exp = "<br><br>{$_ln['jrFoxyCart'][114]} {$dat}";
                }
            }
        }

        // Subscription Length
        if ($_us['quota_jrFoxyCart_subscription_length'] == '1m') {
            $len = $_ln['jrFoxyCart'][105];
        }
        elseif ($_us['quota_jrFoxyCart_subscription_length'] == '1y') {
            $len = $_ln['jrFoxyCart'][106];
        }
        else {
            $len = $_us['quota_jrFoxyCart_subscription_length'];
        }
        $prc = "{$_conf['jrFoxyCart_store_currency']} {$_us['quota_jrFoxyCart_subscription_price']}";
    }
    else {
        $_us['quota_jrFoxyCart_subscription_price'] = '';
        $_us['quota_jrFoxyCart_subscription_trial'] = 0;
    }

    $dat             = array();
    $dat[1]['title'] = "<p><h3>{$_us['quota_jrProfile_name']}</h3>{$exp}</p>";
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = '<p>' . nl2br($_us['quota_jrFoxyCart_subscription_desc']) . '</p>';
    $dat[3]['title'] = (isset($_us['quota_jrFoxyCart_subscription_trial']) && strlen($_us['quota_jrFoxyCart_subscription_trial']) > 1) ? $_us['quota_jrFoxyCart_subscription_trial'] : '';
    $dat[3]['class'] = 'center';
    $dat[4]['title'] = $len;
    $dat[4]['class'] = 'center';
    $dat[5]['title'] = $prc;
    $dat[5]['class'] = 'center';

    if (isset($_us['user_foxycart_customer_id']) && strlen($_us['user_foxycart_customer_id']) > 2) {
        // search to see if this user has ever purchased a subscription
        $tbl = jrCore_db_table_name('jrFoxyCart', 'purchase');
        $req = "SELECT * FROM {$tbl} WHERE purchase_module = 'jrFoxyCart' AND purchase_user_id = {$_us['_user_id']} ";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if (isset($_rt['purchase_id']) && $_rt['purchase_id'] > 0) {
            $dat[6]['title'] = jrCore_page_button('h', $_ln['jrFoxyCart'][93], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/subscription_history')");
        }
        else {
            $dat[6]['title'] = jrCore_page_button('h', $_ln['jrFoxyCart'][93], 'disabled');
        }
    }
    else {
        $dat[6]['title'] = jrCore_page_button("h", $_ln['jrFoxyCart'][93], 'disabled');
    }
    $dat[6]['class'] = 'center nowrap';
    jrCore_page_table_row($dat);

    // Show available PAID subscriptions
    $tbl = jrCore_db_table_name('jrProfile', 'quota_value');
    $req = "SELECT quota_id FROM {$tbl} WHERE `module` = 'jrFoxyCart' AND `name` = 'subscription_allow' AND `value` = 'on' AND quota_id != '{$_us['profile_quota_id']}'";
    $_rt = jrCore_db_query($req, 'quota_id', false, 'quota_id');

    if (isset($_rt) && is_array($_rt)) {

        // Get info about the subscriptions
        $_inf = array();
        $req  = "SELECT * FROM {$tbl} WHERE `quota_id` IN(" . implode(',', $_rt) . ") AND `module` IN('jrProfile','jrFoxyCart') AND `name` IN('name','subscription_trial','subscription_length','subscription_price','subscription_desc')";
        $_qt  = jrCore_db_query($req, 'NUMERIC');
        if (isset($_qt) && is_array($_qt)) {
            foreach ($_qt as $_qta) {
                $_inf["{$_qta['quota_id']}"]["{$_qta['name']}"] = $_qta['value'];
            }
        }

        $dat             = array();
        $dat[1]['title'] = $_ln['jrFoxyCart'][90];
        $dat[1]['width'] = '20%;';
        $dat[2]['title'] = $_ln['jrFoxyCart'][104];
        $dat[2]['width'] = '40%;';
        $dat[3]['title'] = $_ln['jrFoxyCart'][118];
        $dat[3]['width'] = '10%;';
        $dat[4]['title'] = $_ln['jrFoxyCart'][91];
        $dat[4]['width'] = '10%;';
        $dat[5]['title'] = $_ln['jrFoxyCart'][89];
        $dat[5]['width'] = '10%;';
        $dat[6]['title'] = $_ln['jrFoxyCart'][92];
        $dat[6]['width'] = '10%;';
        jrCore_page_table_row($dat, 'page_table_header');

        foreach ($_rt as $qid) {

            $dat             = array();
            $dat[1]['title'] = "<p><h3>{$_inf[$qid]['name']}</h3></p>";
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = (isset($_inf[$qid]['subscription_desc'])) ? '<p>' . nl2br($_inf[$qid]['subscription_desc']) . '</p>' : '';

            if (isset($_inf[$qid]['subscription_trial']) && strlen($_inf[$qid]['subscription_trial']) > 1) {
                $tag = '';
                $num = intval($_inf[$qid]['subscription_trial']);
                switch (substr($_inf[$qid]['subscription_trial'], -1)) {
                    case 'd':
                        $tag = $_ln['jrFoxyCart'][119];
                        break;
                    case 'w':
                        $tag = $_ln['jrFoxyCart'][120];
                        break;
                    case 'm':
                        $tag = $_ln['jrFoxyCart'][121];
                        break;
                    case 'y':
                        $tag = $_ln['jrFoxyCart'][122];
                        break;
                }
                $dat[3]['title'] = "{$num} {$tag}";
            }
            else {
                $dat[3]['title'] = '';
            }
            $dat[3]['class'] = 'center';

            if ($_inf[$qid]['subscription_length'] == '1m') {
                $dat[4]['title'] = $_ln['jrFoxyCart'][105];
            }
            elseif ($_inf[$qid]['subscription_length'] == '1y') {
                $dat[4]['title'] = $_ln['jrFoxyCart'][106];
            }
            else {
                $dat[4]['title'] = $_inf[$qid]['subscription_length'];
            }
            $dat[4]['class'] = 'center';

            $dat[5]['title'] = "{$_conf['jrFoxyCart_store_currency']} {$_inf[$qid]['subscription_price']}";
            $dat[5]['class'] = 'center';
            if (isset($_us['quota_jrFoxyCart_subscription_price']) && $_us['quota_jrFoxyCart_subscription_price'] > 0) {
                $dat[6]['title'] = jrCore_page_button("s{$qid}", $_ln['jrFoxyCart'][92], "if(confirm('{$_ln['jrFoxyCart'][115]}')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/subscription_modify/{$qid}') }");
            }
            else {
                // User is NOT on an existing subscription - link directly to start subscription
                $url             = jrFoxyCart_get_subscription_url($qid);
                $dat[6]['title'] = jrCore_page_button("s{$qid}", $_ln['jrFoxyCart'][92], "jrSetCookie('sub_quota_id'," . intval($qid) . ",1); jrCore_window_location('{$url}')");
            }
            $dat[6]['class'] = 'center nowrap';
            jrCore_page_table_row($dat);

        }
    }

    // Show available FREE subscriptions (to cancel current subscription)
    if (is_array($_rt) && count($_rt) > 0) {
        $req = "SELECT quota_id FROM {$tbl} WHERE `module` = 'jrUser' AND `name` = 'allow_signups' AND `value` = 'on' AND quota_id NOT IN({$_us['profile_quota_id']}," . implode(',', $_rt) . ")";
    }
    else {
        $req = "SELECT quota_id FROM {$tbl} WHERE `module` = 'jrUser' AND `name` = 'allow_signups' AND `value` = 'on' AND quota_id != '{$_us['profile_quota_id']}'";
    }
    $_fs = jrCore_db_query($req, 'quota_id', false, 'quota_id');
    if (is_array($_fs)) {

        // Get info about the subscriptions
        $_inf = array();
        $req  = "SELECT * FROM {$tbl} WHERE `quota_id` IN(" . implode(',', $_fs) . ") AND `name` IN('name', 'subscription_desc', 'subscription_allow')";
        $_qt  = jrCore_db_query($req, 'NUMERIC');
        if (isset($_qt) && is_array($_qt)) {
            foreach ($_qt as $_qta) {
                $_inf["{$_qta['quota_id']}"]["{$_qta['name']}"] = $_qta['value'];
            }
        }

        if (count($_fs) > 0) {
            foreach ($_fs as $qid) {
                $dat             = array();
                $dat[1]['title'] = "<p><h3>{$_inf[$qid]['name']}</h3></p>";
                if (isset($_ex['sub_new_quota_id']) && $_ex['sub_new_quota_id'] == $qid) {
                    $dat[1]['title'] .= "<br>{$_ln['jrFoxyCart'][109]} {$y}-{$m}-{$d}";
                }
                $dat[1]['class'] = 'center';
                $dat[2]['title'] = (isset($_inf[$qid]['subscription_desc'])) ? '<p>' . nl2br($_inf[$qid]['subscription_desc']) . '</p>' : '';
                $dat[3]['title'] = '-';
                $dat[3]['class'] = 'center';
                $dat[4]['title'] = '-';
                $dat[4]['class'] = 'center';
                $dat[5]['title'] = 'free';
                $dat[5]['class'] = 'center';
                if (isset($_ex['sub_new_quota_id']) && $_ex['sub_new_quota_id'] == $qid) {
                    $dat[6]['title'] = jrCore_page_button('c', $_ln['jrFoxyCart'][92], 'disabled');
                }
                elseif ($tkn) {
                    $dat[6]['title'] = jrCore_page_button('c', $_ln['jrFoxyCart'][92], "if(confirm('{$_ln['jrFoxyCart'][116]}')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/subscription_cancel/quota_id={$qid}/sub_token={$tkn}') }");
                }
                else {
                    $dat[6]['title'] = jrCore_page_button('c', $_ln['jrFoxyCart'][92], "if(confirm('{$_ln['jrFoxyCart'][117]}')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/subscription_cancel/quota_id={$qid}') }");
                }
                $dat[6]['class'] = 'center nowrap';
                jrCore_page_table_row($dat);
            }
        }
    }
    jrCore_page_table_footer();
    jrCore_page_display();
}

//--------------------------------------------------------------
// start a new subscription (prorated)
//--------------------------------------------------------------
function view_jrFoxyCart_subscription_modify($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    $_ln = jrUser_load_lang_strings();

    // At this screen, our user MUST have an existing sub
    if (!isset($_user['user_foxycart_customer_id'])) {
        jrCore_notice_page('error', 'You do not have a current subscription to modify');
    }

    // The quota_id we are moving to comes in as _1
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid quota_id received');
    }
    $_qt = jrProfile_get_quota($_post['_1']);
    if (!is_array($_qt)) {
        jrCore_notice_page('error', 'Invalid quota_id received (2)');
    }
    // Make sure it allows subscriptions
    if (!isset($_qt['quota_jrFoxyCart_subscription_allow']) || $_qt['quota_jrFoxyCart_subscription_allow'] != 'on') {
        jrCore_notice_page('error', 'The selected quota does not allow subscriptions');
    }

    // We need to:
    // 1) find out when the existing subscription expires
    // 2) find out the frequency of the existing subscription
    // 3) figure CREDIT amount that will be applied to new subscription
    // 4) start new subscription on THAT AMOUNT (this is the first payment)
    // 5) after subscription starts, modify subscription to regular price

    // If the user switches to a new quota BEFORE they have been charged for their existing quota, we don't want to credit them
    // See if we have received payment for current subscription yet
    $tkn = jrFoxyCart_get_subscription_token($_user['user_foxycart_customer_id']);

    // Get existing subscription info
    $_rs = array(
        'api_action' => 'subscription_get',
        'sub_token'  => $tkn
    );
    $res = jrFoxyCart_api($_rs);
    if (!$res || !isset($res->subscription->start_date)) {
        jrCore_notice_page('error', 'No existing subscription was found');
    }

    $qid = (int) $_post['_1'];
    $beg = (string) $res->subscription->start_date;
    $beg = strtotime(str_replace('-', '/', $beg) . ' 00:00:01');
    $end = (string) $res->subscription->next_transaction_date;
    $end = strtotime(str_replace('-', '/', $end) . ' 00:00:01');
    $tot = floor(($end - $beg) / 86400); // Total number of days in EXISTING subscription period
    $far = ceil(($end - time()) / 86400); // how many days from NOW until the end

    // Current sub FULL price
    $prc = (string) $res->subscription->transaction_template->transaction_details->transaction_detail->product_price;

    // $crd is our CREDIT - this is how much of the current sub price we get to apply towards the new quota
    $crd = jrFoxyCart_currency_format(round($prc * ($far / $tot), 2));

    // $prc - this is the full price of the new quota
    $prc = jrFoxyCart_currency_format($_qt['quota_jrFoxyCart_subscription_price']);

    // So lastly we need to figure out how FAR into the new quota our credit
    // will get us, and then we set the next_transaction_date to that date
    $mod = substr($_qt['quota_jrFoxyCart_subscription_length'], strlen($_qt['quota_jrFoxyCart_subscription_length']) - 1);
    $val = (int) str_replace($mod, '', $_qt['quota_jrFoxyCart_subscription_length']);
    $mul = 0;
    switch ($mod) {
        case 'd':
            $mul = 86400;
            break;
        case 'w':
            $mul = (86400 * 7);
            break;
        case 'm':
            $mul = (86400 * 30);
            break;
        case 'y':
            $mul = (86400 * 365);
            break;
        default:
            jrCore_notice_page('error', 'Unable to determine new subscription length!');
            break;
    }
    $sec = ($val * $mul); // Number of SECONDS in our NEW subscription
    $dif = ($sec * ($crd / $prc)); // Our CREDITED seconds - added to TODAY this gets us our next charge time
    $dif = (time() + $dif);
    if ($dif < time()) {
        // This should NOT happen - log our error message
        $_er = array(
            'quota_id subscribing to ($qid)' => $qid,
            'number of days in existing subscription period ($tot)' => $tot,
            'days LEFT in existing subscription period ($far)' => $far,
            'recurring price for EXISTING subscription ($prc)' => (string) $res->subscription->transaction_template->transaction_details->transaction_detail->product_price,
            'credited amount from current subscription ($crd)' => $crd,
            'recurring price for NEW subscription ($prc)' => $prc,
            'computed DIFF to get next_transaction_date ($dif)' => $dif
        );
        jrCore_logger('CRI', 'unable to properly compute next_transaction_date for new subscription', $_er);
        jrCore_notice_page('error', 'An error was encountered trying to move to the new subscription - please try again');
    }
    else {
        $dif = strftime('%Y-%m-%d', $dif);
    }

    // OK $crd contains the amount of CREDIT we are applying to this new Quota
    $tpl = "<?xml version='1.0' encoding='UTF-8'?>
    <transaction_template>
        <custom_fields>
            <custom_field>
                <custom_field_name>profile_id</custom_field_name>
                <custom_field_value>{$_user['user_active_profile_id']}</custom_field_value>
                <custom_field_is_hidden>1</custom_field_is_hidden>
            </custom_field>
            <custom_field>
                <custom_field_name>user_id</custom_field_name>
                <custom_field_value>{$_user['_user_id']}</custom_field_value>
                <custom_field_is_hidden>1</custom_field_is_hidden>
            </custom_field>
        </custom_fields>
        <discounts/>
        <transaction_details>
            <transaction_detail>
                <product_name>{$_qt['quota_jrProfile_name']}</product_name>
                <product_price>{$prc}</product_price>
                <product_quantity>1</product_quantity>
                <product_weight>0</product_weight>
                <product_code>subscription_purchase</product_code>
                <image></image>
                <url></url>
                <length>0</length>
                <width>0</width>
                <height>0</height>
                <shipto></shipto>
                <category_code>DEFAULT</category_code>
                <transaction_detail_options>
                    <transaction_detail_option>
                        <product_option_name>quota_id</product_option_name>
                        <product_option_value>{$qid}</product_option_value>
                        <price_mod>0</price_mod>
                        <weight_mod>0</weight_mod>
                    </transaction_detail_option>
                </transaction_detail_options>
            </transaction_detail>
        </transaction_details>
    </transaction_template>";

    // Submit new subscription
    $_rs = array(
        'api_action'            => 'subscription_modify',
        'next_transaction_date' => $dif,
        'is_active'             => 1,
        'frequency'             => $_qt['quota_jrFoxyCart_subscription_length'],
        'sub_token'             => $tkn,
        'transaction_template'  => $tpl
    );
    $xml = jrFoxyCart_api($_rs);
    if ($xml && isset($xml->result)) {
        $res = (string) $xml->result;
        if (strtolower($res) == 'success') {
            // We've moved to the new Quota - move now
            $_dt = array(
                'profile_quota_id' => $qid
            );
            jrCore_db_update_item('jrProfile', $_user['user_active_profile_id'], $_dt);
            jrCore_logger('INF', "successfully started new subscription to quota_id {$qid} for profile: {$_user['profile_name']}", $_rs);
            jrUser_session_sync($_user['_user_id']);
            jrCore_set_form_notice('success', $_ln['jrFoxyCart'][110]);
            jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/subscription_browser");

        }
    }
    jrCore_logger('CRI', "Unable to start new subscription for {$_user['user_name']} to quota_id {$qid}", $xml);
    jrCore_notice_page('error', 'An error was encountered trying to move to the new subscription - please try again');
}

/**
 * doing stuff with ajax like cancelling a subscription etc.
 * @param $_post
 * @param $_user
 * @param $_conf
 * @return string
 */
function view_jrFoxyCart_ajax($_post, $_user, $_conf)
{
    switch ($_post['mode']) {

        case 'subscription_cancel':

            jrUser_session_require_login();
            jrCore_validate_location_url();

            $sub_token = (string) $_post['sub_token'];
            if (strlen($sub_token) > 0) {

                // check to see if this user owns this subscription, or is the admin.
                if (isset($_user['user_foxycart_customer_id'])) {
                    //query foxycart API for this users subscriptions
                    $_rs = array(
                        'api_action'         => 'subscription_list',
                        'is_active_filter'   => '',
                        'customer_id_filter' => $_user['user_foxycart_customer_id']
                    );
                    $xml = jrFoxyCart_api($_rs);
                    if ($xml->result == "SUCCESS") {
                        //get sub_token this user controls.
                        $_tokens = array();
                        foreach ($xml->subscriptions->subscription as $_sub) {
                            $_tokens[] = (string) $_sub->sub_token;
                        }
                        if (count($_tokens) === 0 || !in_array($sub_token, $_tokens)) {
                            // not the owner or the admin, fail.
                            jrCore_logger('MAJ', 'jrFoxyCart_ajax: not the sub_token owner or the admin.');
                            $_response = array(
                                'error'     => 1,
                                'error_msg' => 'not the sub_token owner or the admin.'
                            );
                            jrCore_json_response($_response);
                        }
                    }
                    else {
                        // no success contacting foxycart API
                        jrCore_logger('MIN', 'jrFoxyCart_ajax: no success code when calling the foxycart API to cancel a transaction, try again later.');
                        $_response = array(
                            'error'     => 1,
                            'error_msg' => 'no success code when calling the foxycart API to cancel a transaction, try again later.'
                        );
                        jrCore_json_response($_response);
                    }
                }
                else {
                    //not the owner or the admin, fail.
                    jrCore_logger('MAJ', 'jrFoxyCart_ajax: tried to cancel a subscription, but user not the owner or an admin.');
                    $_response = array(
                        'error'     => false,
                        'error_msg' => 'failed: you are neither the admin or the owner of this subscription'

                    );
                    jrCore_json_response($_response);
                }

                //send the api call to cancel the subscription
                $_rs = array(
                    'api_action' => 'subscription_modify',
                    'is_active'  => '0',
                    'sub_token'  => $sub_token
                );
                $xml = jrFoxyCart_api($_rs);
                if ($xml->result == "SUCCESS") {

                    //so remove any profiles that were using it and return them to the default quota
                    $_sc = array(
                        'search'         => array("profile_sub_token = {$_post['sub_token']}"),
                        'order_by'       => array(
                            'profile_name' => 'ASC'
                        ),
                        'limit'          => 250,
                        'skip_triggers'  => true,
                        'privacy_check'  => false,
                        'ignore_pending' => true
                    );
                    $_rt = jrCore_db_search_items('jrProfile', $_sc);
                    if (is_array($_rt)) {
                        foreach ($_rt['_items'] as $_p) {
                            //delete the profile id and return it to default quota
                            $_data['quota_id'] = $_conf['jrProfile_default_quota_id'];
                            $_data             = jrCore_trigger_event('jrFoxyCart', 'return_to_default_quota', $_data, $_p);

                            $_prof = array(
                                'profile_quota_id'  => $_data['quota_id'],
                                'profile_sub_token' => ''
                            );
                            jrCore_db_update_item('jrProfile', $_p['_profile_id'], $_prof);
                            jrProfile_reset_cache($_p['profile_id']);
                            // Update Quota Counts for quotas if we are changing
                            if (isset($_data['quota_id']) && $_data['quota_id'] != $_p['profile_quota_id']) {
                                // Update counts in both Quotas
                                jrProfile_increment_quota_profile_count($_data['quota_id']);
                                jrProfile_decrement_quota_profile_count($_p['profile_quota_id']);
                            }
                        }
                    }
                }

                //based on the api call, return the json results
                $_response = array(
                    'success'     => true,
                    'success_msg' => 'your subscription has been cancelled.',
                    'xml'         => $xml
                );
                jrCore_json_response($_response);
            }
            break;

        case 'subscription_info':

            jrUser_session_require_login();
            $_tmp                = array();
            $_tmp['quota_id']    = $_user['profile_quota_id'];
            $_tmp['quota_name']  = $_user['quota_jrProfile_name'];
            $_tmp['sub_len']     = $_user['quota_jrFoxyCart_subscription_length'];
            $_tmp['quota_price'] = $_user['quota_jrFoxyCart_subscription_price'];
            jrCore_json_response($_tmp);
            break;

        case 'change_quota':

            jrUser_session_require_login();
            jrCore_validate_location_url();

            /**
             * allows a user to change quotas if they have multiple active subscriptions.
             */
            $sub_token = (string) $_post['sub_token'];
            $_rs       = array(
                'api_action' => 'subscription_get',
                'sub_token'  => $sub_token
            );
            $xml       = jrFoxyCart_api($_rs);
            if ($xml->result == "SUCCESS") {
                //check that this users customer id == the subscription customer id
                if ($xml->subscription->customer_id == $_user['user_foxycart_customer_id']) {
                    //get the quota_id from the subscription details:
                    $quota_id = false;
                    if ($xml->subscription->transaction_template->transaction_details->transaction_detail->transaction_detail_options->transaction_detail_option->product_option_name == 'quota_id' &&
                        isset($xml->subscription->transaction_template->transaction_details->transaction_detail->transaction_detail_options->transaction_detail_option->product_option_value)
                    ) {
                        $quota_id = $xml->subscription->transaction_template->transaction_details->transaction_detail->transaction_detail_options->transaction_detail_option->product_option_value;
                    }
                    else {
                        $_response = array(
                            'success'     => false,
                            'success_msg' => 'error: the quota_id could not be found in the foxycart subscription.'
                        );
                        jrCore_json_response($_response);
                    }

                    //change them to this quota.
                    $_profile                  = jrCore_db_get_item('jrProfile', $_user['_user_id']); //their existing quota id
                    $_data                     = array();
                    $_data['profile_quota_id'] = $quota_id;
                    jrCore_db_update_item('jrProfile', $_profile['_profile_id'], $_data);
                    // Update Quota Counts for quotas if we are changing
                    if (isset($quota_id) && $quota_id != $_profile['profile_quota_id']) {
                        // Update counts in both Quotas
                        jrProfile_increment_quota_profile_count($quota_id);
                        jrProfile_decrement_quota_profile_count($_profile['profile_quota_id']);
                    }
                    jrCore_form_delete_session();
                    jrProfile_reset_cache($_profile['profile_id']);
                    $_response = array(
                        'success'     => true,
                        'success_msg' => 'profile quota id changed to: ' . $quota_id
                    );

                }
                else {
                    $_response = array(
                        'success'     => false,
                        'success_msg' => 'error: the subscripiton customer_id does not match yours'
                    );
                }
            }
            else {
                $_response = array(
                    'success'     => false,
                    'success_msg' => $xml->messages->message
                );
            }
            jrCore_json_response($_response);
            break;
    }
    //failed
    $_response = array(
        'success'     => false,
        'success_msg' => 'ERROR: unrecognized mode, check your request.'
    );
    jrCore_json_response($_response);
}

//------------------------------
// vault_download
//------------------------------
function view_jrFoxyCart_vault_download($_post, $_user, $_conf)
{
    jrUser_session_require_login();

    // When a download request comes in, it will look like:
    // http://www.site.com/audio/vault_download/audio_file/5
    // so we have URL / module / option / _1 / _2
    if (!isset($_post['_2']) || !is_numeric($_post['_2'])) {
        jrCore_notice('CRI', 'Invalid media id provided');
    }
    // Make sure this is a DataStore module
    if (!jrCore_db_get_prefix($_post['module'])) {
        jrCore_notice('CRI', 'Invalid module - no datastore');
    }
    $_rt = jrCore_db_get_item($_post['module'], intval($_post['_2']));
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_notice('CRI', 'Invalid media id - no data found');
    }
    if (!isset($_rt["{$_post['_1']}_size"]) || $_rt["{$_post['_1']}_size"] < 1) {
        jrCore_notice('CRI', 'Invalid media id - no media item found');
    }
    // Make sure this user has purchased this vault file
    if (!jrUser_is_admin()) {
        $tbl = jrCore_db_table_name('jrFoxyCart', 'purchase');
        $req = "SELECT purchase_txn_id FROM {$tbl} WHERE purchase_user_id = '{$_user['_user_id']}' AND purchase_item_id = '{$_rt['_item_id']}' AND purchase_module = '" . jrCore_db_escape($_post['module']) . "' LIMIT 1";
        $_us = jrCore_db_query($req, 'SINGLE');
        if (!isset($_us) || !is_array($_us)) {
            jrCore_notice('Error', 'It does not appear you have purchased this file - exiting');
        }
    }

    // When we get a VAULT download, we're going to be sending the
    // user the ORIGINAL file - not the down sampled copy.
    if (isset($_rt["{$_post['_1']}_original_extension"])) {
        // jrVideo_38_video_file.mov.original.mov
        $ext = $_rt["{$_post['_1']}_original_extension"];
        $nam = "{$_post['module']}_{$_post['_2']}_{$_post['_1']}.{$ext}.original.{$ext}";
        $ttl = $_rt["{$_post['_1']}_original_name"];
    }
    else {
        // We don't have an "original" - i.e. no conversion was done
        $nam = "{$_post['module']}_{$_post['_2']}_{$_post['_1']}." . $_rt["{$_post['_1']}_extension"];
        $ttl = $_rt["{$_post['_1']}_name"];
    }
    if (!jrCore_media_file_exists($_rt['_profile_id'], $nam)) {
        jrCore_notice('CRI', 'Invalid media id - no file found');
    }

    // Increment our counter
    jrCore_counter($_post['module'], $_post['_2'], "{$_post['_1']}_vault_download");

    // "vault_download" event trigger
    $_args = array(
        'module'     => $_post['module'],
        'file_name'  => $_post['_2'],
        'vault_file' => $nam
    );
    jrCore_trigger_event('jrFoxyCart', 'vault_download', $_rt, $_args);

    // Download the file to the client
    jrCore_media_file_download($_rt['_profile_id'], $nam, $ttl);
    session_write_close();
    exit();
}

//----------------------------
// choose profiles to payout
//----------------------------
function view_jrFoxyCart_payout($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrFoxyCart');
    jrCore_page_banner('Profile Payout');

    // Make sure we are setup
    if (!isset($_conf['jrFoxyCart_store_currency'])) {
        jrCore_notice_page('error', 'You have not configured a valid <b>Store Currency</b>  - click on the <b>Global Config</b> tab above and select a valid Store Currency value.', false);
    }
    jrCore_get_form_notice();

    // Get profiles with balances
    $_pr = array();
    $unq = false;
    $old = intval(time() - ($_conf['jrFoxyCart_payout_clears'] * 86400));
    $tbl = jrCore_db_table_name('jrFoxyCart', 'sale');
    $req = "SELECT *, SUM(sale_total_net) AS cleared FROM {$tbl} WHERE sale_time < {$old} AND sale_payed_out != '1' GROUP BY sale_seller_profile_id ORDER BY cleared DESC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (is_array($_rt)) {

        // We need to get the individual sales that match our results
        $req = "SELECT sale_id AS sid, sale_seller_profile_id AS pid, sale_total_net AS net FROM {$tbl} WHERE sale_time < {$old} AND sale_payed_out != '1'";
        $_tm = jrCore_db_query($req, 'sid');
        if (is_array($_tm)) {
            $unq = md5(microtime());
            $tbl = jrCore_db_table_name('jrFoxyCart', 'payout_history');
            $req = "INSERT INTO {$tbl} (p_code, p_time, p_data, p_pids) VALUES ('{$unq}', UNIX_TIMESTAMP(), '" . jrCore_db_escape(json_encode($_tm)) . "', '')";
            jrCore_db_query($req);
        }

        $_pr = array();
        foreach ($_rt as $_sale) {
            $pid       = (int) $_sale['sale_seller_profile_id'];
            $_pr[$pid] = $pid;
        }
        if (count($_pr) > 0) {
            $_tm = jrCore_db_get_multiple_items('jrProfile', $_pr);
            if (is_array($_tm)) {
                $_pr = array();
                foreach ($_tm as $_profile) {
                    $_pr["{$_profile['_profile_id']}"] = $_profile;
                }
            }
            unset($_tm);
        }
    }

    $dat             = array();
    $dat[1]['title'] = 'select';
    $dat[1]['width'] = '5%';
    $dat[2]['title'] = 'profile';
    $dat[2]['width'] = '45%';
    $dat[3]['title'] = 'payout email address';
    $dat[3]['width'] = '35%';
    $dat[4]['title'] = 'cleared amount';
    $dat[4]['width'] = '15%';
    jrCore_page_table_header($dat);

    if (is_array($_rt)) {

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
            'value' => $unq
        );
        jrCore_form_field_create($_tmp);

        $tot = 0;
        foreach ($_rt as $_s) {
            if ($_s['cleared'] > 0) {
                $pid             = (int) $_s['sale_seller_profile_id'];
                $_p              = $_pr[$pid];
                $dat             = array();
                $dat[1]['title'] = '&nbsp;'; // Cannot process payout without a valid email address
                $shw             = 'no payout address';
                if (isset($_p['profile_foxycart_payout_email']) && jrCore_checktype($_p['profile_foxycart_payout_email'], 'email')) {
                    $dat[1]['title'] = '<input name="profile_id[' . $_p['_profile_id'] . ']" type="checkbox" value="' . jrFoxyCart_currency_format($_s['cleared']) . '" checked="checked">';
                    $shw             = '<b>' . $_p['profile_foxycart_payout_email'] . '</b>';
                }

                $dat[1]['class'] = 'center';
                $dat[2]['title'] = "<a href\"{$_conf['jrCore_base_url']}/{$_p['profile_url']}\">{$_p['profile_name']}</a>";
                $dat[2]['class'] = 'center';
                $dat[3]['title'] = $shw;
                $dat[3]['class'] = 'center';
                $dat[4]['title'] = '<a href="' . $_conf['jrCore_base_url'] . '/' . $_p['profile_url'] . '/' . $_post['module_url'] . '/earnings">' . jrFoxyCart_currency_format($_s['cleared']) . '</a>';
                $dat[4]['class'] = 'center';
                jrCore_page_table_row($dat);
                $tot += $_s['cleared'];
            }
        }

        if ($tot > 0) {
            $dat             = array();
            $dat[1]['title'] = '<b>total payout amount</b>&nbsp;';
            $dat[1]['class'] = 'right" colspan="3';
            $dat[2]['title'] = jrFoxyCart_currency_format($tot);
            $dat[2]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_footer();
    }
    else {
        $dat             = array();
        $dat[1]['title'] = "<p>There are no profiles with pending payouts that have cleared</p>";
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
        jrCore_page_table_footer();
        jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    }
    jrCore_page_display();
}

//--------------------------------------------------------------------
// show the profiles that are included in the CSV for post-processing
//--------------------------------------------------------------------
function view_jrFoxyCart_payout_save($_post, &$_user, &$_conf)
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

    // Update our unique payout entry with select profiles
    $tbl = jrCore_db_table_name('jrFoxyCart', 'payout_history');
    $req = "UPDATE {$tbl} SET p_pids = '" . jrCore_db_escape(json_encode($_post['profile_id'])) . "' WHERE p_code = '{$_post['p_code']}'";
    jrCore_db_query($req);

    jrCore_form_delete_session();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrFoxyCart');
    jrCore_page_banner('Send Mass Payment');
    jrCore_get_form_notice();

    $note = "<div style=\"text-align:left\">
    <ul><li>You should be prompted to download the Mass Pay CSV file - save to your computer.</li>
    <li><a href=\"https://www.paypal.com\" target=\"_blank\"><u>Log in to PayPal</u></a> and go to Send Payment &raquo; Make a Mass Payment.</li>
    <li>Select the CSV file that was just download and upload it to PayPal in the Mass Pay form.</li>
    <li>Complete the Mass Pay process at PayPal.</li>
    <li>Click the &quot;Continue&quot; button below when finished.</li>
    <li>You can redownload the CSV file by <a href=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/csv/p_code={$_post['p_code']}\"><u>Clicking Here.</u></a></li></ul>
    <iframe src=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/csv/p_code={$_post['p_code']}\" style=\"display:none;\"></iframe></div>";
    jrCore_set_form_notice('notice', $note, false);
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value'     => 'continue',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/payout",
        'form_ajax_submit' => false,
        'action'           => 'payout_complete'
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

//--------------------------------------------------------------------
// complete the payout by decrementing profile balances
//--------------------------------------------------------------------
function view_jrFoxyCart_payout_complete($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    if (!isset($_post['p_code']) || !jrCore_checktype($_post['p_code'], 'md5')) {
        jrCore_set_form_notice('error', 'invalid payout code');
        jrCore_location('referrer');
    }
    // Get payout info for this payout code
    $tbl = jrCore_db_table_name('jrFoxyCart', 'payout_history');
    $req = "SELECT * FROM {$tbl} WHERE p_code = '{$_post['p_code']}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid payout code - not found in DB');
        jrCore_location('referrer');
    }

    // Get profile info for profiles we paid out
    $_pid = json_decode($_rt['p_pids'], true);
    if (is_array($_pid)) {

        // We have to:
        // Mark each sale as payed out
        // update the payout table with the amount payed out
        $_sl = json_decode($_rt['p_data'], true);
        $tbl = jrCore_db_table_name('jrFoxyCart', 'sale');
        $req = "UPDATE {$tbl} SET sale_payed_out = '1' WHERE sale_id IN(" . implode(',', array_keys($_sl)) . ")";
        $cnt = jrCore_db_query($req, 'COUNT');
        jrCore_logger('INF', "Updated {$cnt} sales records as payed out per profile payout");

        // Okay - we know the profiles that have been selected to be paid, and we have the
        // complete sale transaction list that was generated for this payout - compute
        $_pi = array();
        foreach ($_pid as $pid => $balance) {
            $_pi[] = $pid;
        }
        $_tm = jrCore_db_get_multiple_items('jrProfile', $_pi);
        if (is_array($_tm)) {
            $_pi = array();
            foreach ($_tm as $_pr) {
                $_pi["{$_pr['_profile_id']}"] = $_pr;
            }
        }

        // Next, record net payout amounts into payout table
        $tbl = jrCore_db_table_name('jrFoxyCart', 'payout');
        $req = "INSERT INTO {$tbl} (payout_profile_id,payout_time,payout_currency,payout_amount, payout_email) VALUES ";
        $_ad = array();
        foreach ($_pid as $pid => $balance) {
            $_ad[] = "('{$pid}',UNIX_TIMESTAMP(),'{$_conf['jrFoxyCart_store_currency']}','{$balance}','{$_pi[$pid]['profile_foxycart_payout_email']}')";
        }
        $req .= implode(',', $_ad);
        $cnt = jrCore_db_query($req, 'COUNT');
        if ($cnt && $cnt > 0) {

            // Notify the profiles they have been paid
            foreach ($_pid as $pid => $balance) {
                $_rp = array(
                    'payout_email'  => $_pi[$pid]['profile_foxycart_payout_email'],
                    'payout_amount' => $balance
                );
                list($sub, $msg) = jrCore_parse_email_templates('jrFoxyCart', 'profile_paid', $_rp);
                jrCore_send_email($_pi[$pid]['profile_foxycart_payout_email'], $sub, $msg);
                jrCore_logger('INF', "successfully payed out {$balance} to " . $_pi[$pid]['profile_foxycart_payout_email']);
            }

            // Mark this payout code as done
            $tbl = jrCore_db_table_name('jrFoxyCart', 'payout_history');
            $req = "UPDATE {$tbl} SET p_done = '1' WHERE p_code = '{$_post['p_code']}'";
            jrCore_db_query($req);

            jrCore_page_include_admin_menu();
            jrCore_page_admin_tabs('jrFoxyCart');
            jrCore_page_banner('Payout Complete');
            jrCore_set_form_notice('success', 'The profile balances have been adjusted by the amounts in the payout.');
            jrCore_get_form_notice();
            jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/payout", 'continue');
            jrCore_page_display();
        }
    }
    else {
        // Fall through - bad data
        jrCore_form_delete_session();
        jrCore_set_form_notice('error', 'Invalid payout code - please try again');
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/payout");
    }
}

//------------------------------
// generate the CSV file.
//------------------------------
function view_jrFoxyCart_csv($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['p_code']) || !jrCore_checktype($_post['p_code'], 'md5')) {
        jrCore_logger('CRI', 'invalid payout code received in CSV!');
        exit;
    }
    // Get payout info for this payout code
    $tbl = jrCore_db_table_name('jrFoxyCart', 'payout_history');
    $req = "SELECT * FROM {$tbl} WHERE p_code = '{$_post['p_code']}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_rt)) {
        jrCore_logger('CRI', 'invalid payout code received in CSV - entry not found in DB');
        exit;
    }
    $_pid = json_decode($_rt['p_pids'], true);
    if (is_array($_pid)) {
        // Okay - we know the profiles that have been selected to be paid, and we have the
        // complete sale transaction list that was generated for this payout - compute
        $_pi = array();
        foreach ($_pid as $pid => $balance) {
            $_pi[] = $pid;
        }
        $_tm = jrCore_db_get_multiple_items('jrProfile', $_pi);
        if (is_array($_tm)) {
            $_pi = array();
            foreach ($_tm as $_pr) {
                $_pi["{$_pr['_profile_id']}"] = $_pr;
            }
        }
        $out = '';
        foreach ($_pid as $pid => $balance) {

            // Make sure we are not over our limit marks
            switch ($_conf['jrFoxyCart_store_currency']) {
                case 'USD':
                    $limit = 10000;
                    break;
                case 'GBP':
                    $limit = 5550;
                    break;
                case 'EUR':
                    $limit = 8000;
                    break;
                case 'JPY':
                    $limit = 1000000;
                    break;
                case 'AUD':
                case 'CAD':
                    $limit = 12500;
                    break;
                case 'CHF':
                    $limit = 13000;
                    break;
                case 'CZK':
                    $limit = 240000;
                    break;
                case 'DKK':
                    $limit = 60000;
                    break;
                case 'HUF':
                    $limit = 2000000;
                    break;
                case 'HKD':
                    $limit = 80000;
                    break;
                case 'NOK':
                    $limit = 70000;
                    break;
                case 'NZD':
                    $limit = 15000;
                    break;
                case 'PLN':
                    $limit = 32000;
                    break;
                case 'SEK':
                    $limit = 80000;
                    break;
                case 'SGD':
                    $limit = 16000;
                    break;
                default:
                    $_conf['jrFoxyCart_store_currency'] = 'USD';
                    $limit                              = 10000;
                    break;
            }
            if (isset($balance) && $balance > $limit) {
                jrCore_logger('CRI', "view_jrFoxyCart_csv() profile_id {$pid} has a profile_balance larger than allowed ({$limit}): {$balance}");
                continue;
            }
            $out .= "{$_pi[$pid]['profile_foxycart_payout_email']}," . jrFoxyCart_currency_format($balance) . ",{$_conf['jrFoxyCart_store_currency']}\n";
        }
        // Looks good - let's send out our MASS PAY file
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Cache-Control: private");
        header("Pragma: no-cache");
        header("Content-type: application/csv");
        header("Content-Disposition: inline; filename=\"MassPay_Payout_{$_post['p_code']}.csv\"");
        header("Content-length: " . strlen($out));
        ob_start();
        echo $out;
        ob_end_flush();
        exit;
    }
    else {
        jrCore_logger('CRI', "Invalid payout key: {$_post['key']} - no results found");
        jrCore_notice('error', 'The key returned no results.  Generate a CSV first.');
    }
}

//-------------------------------------------------------
// showing txn details
//-------------------------------------------------------
function view_jrFoxyCart_txn_details($_post, $_user, $_conf)
{
    jrUser_session_require_login();

    if (jrUser_is_master()) {
        jrCore_page_include_admin_menu();
        jrCore_page_admin_tabs('jrFoxyCart');
    }

    // See if we got a transaction
    if (isset($_post['_1']) && strlen($_post['_1']) > 0) {

        $txn_id = $_post['_1'];

        // check this user is at least the owner of a profile associated with this sale...
        if (!jrUser_is_admin()) {
            $_li = jrProfile_get_user_linked_profiles($_user['_user_id']);
            $tbl = jrCore_db_table_name('jrFoxyCart', 'purchase');
            $req = "SELECT purchase_seller_profile_id FROM {$tbl} WHERE purchase_txn_id = '" . jrCore_db_escape($txn_id) . "' AND purchase_seller_profile_id IN(" . implode(',', array_keys($_li)) . ")";
            $_rt = jrCore_db_query($req, 'NUMERIC');
            if (!$_rt || !is_array($_rt)) {
                jrUser_not_authorized();
            }
        }
    }

    // Get language strings
    $_lang = jrUser_load_lang_strings();

    // No passed in Transaction ID
    if (!isset($txn_id) || $txn_id === 0) {

        jrCore_page_banner($_lang['jrFoxyCart'][29]);
        jrCore_get_form_notice();
        jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/txn_details");

        //show the links to the latest ones:
        $tb1 = jrCore_db_table_name('jrFoxyCart', 'sale');
        $tb2 = jrCore_db_table_name('jrUser', 'item_key');

        if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {

            // See if we have any matching transactions...
            $_sp = array(
                'search'        => array(
                    "txn_% like %{$_post['search_string']}%"
                ),
                'skip_triggers' => true,
                'privacy_check' => false,
                'limit'         => 1000
            );
            $_mi = jrCore_db_search_items('jrFoxyCart', $_sp);
            $sst = jrCore_db_escape($_post['search_string']);
            $req = "SELECT *, SUM(s.sale_gross) AS amt, COUNT(s.sale_id) as item_count
                      FROM {$tb1} s
                 LEFT JOIN {$tb2} u ON (u.`_item_id` = s.sale_buyer_user_id AND u.`key` = 'user_name') ";
            if ($_mi && is_array($_mi) && isset($_mi['_items'])) {
                $_id = array();
                $_nt = array();
                foreach ($_mi['_items'] as $k => $v) {
                    $_id[]                 = $v['txn_id'];
                    $_nt["{$v['txn_id']}"] = $v;
                }
                $req .= "WHERE (s.sale_txn_id LIKE '%{$sst}%' OR u.`value` LIKE '%{$sst}%' OR s.sale_txn_id IN('" . implode("','", $_id) . "')) ";
            }
            else {
                $req .= "WHERE (s.sale_txn_id LIKE '%{$sst}%' OR u.`value` LIKE '%{$sst}%') ";
            }
            if (!jrUser_is_admin()) {
                $req .= "AND s.sale_seller_profile_id = '{$_user['user_active_profile_id']}' ";
            }
            $req .= "GROUP BY s.sale_txn_id ORDER BY s.sale_time DESC";
        }
        else {
            $req = "SELECT *, SUM(s.sale_gross) AS amt, COUNT(s.sale_id) as item_count
                      FROM {$tb1} s
                 LEFT JOIN {$tb2} u ON (u.`_item_id` = s.sale_buyer_user_id AND u.`key` = 'user_name') ";
            // If this is NOT an admin user, limit to sales from their profile
            if (!jrUser_is_admin()) {
                $req .= "WHERE s.sale_seller_profile_id = '{$_user['user_active_profile_id']}' ";
            }
            $req .= "GROUP BY s.sale_txn_id ORDER BY s.sale_time DESC";
        }
        if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
            $_post['p'] = 1;
        }
        $_rt = jrCore_db_paged_query($req, $_post['p'], 12, 'NUMERIC');

        $dat             = array();
        $dat[1]['title'] = '&nbsp;';
        $dat[1]['width'] = '2%';
        $dat[2]['title'] = 'buyer';
        $dat[2]['width'] = '33%';
        $dat[3]['title'] = 'date of sale';
        $dat[3]['width'] = '20%';
        $dat[4]['title'] = 'items';
        $dat[4]['width'] = '10%';
        $dat[5]['title'] = 'amount';
        $dat[5]['width'] = '10%';
        $dat[6]['title'] = 'ID';
        $dat[6]['width'] = '20%';
        $dat[7]['title'] = 'details';
        $dat[7]['width'] = '5%';
        jrCore_page_table_header($dat);

        if ($_rt && is_array($_rt)) {
            foreach ($_rt['_items'] as $k => $_row) {
                $dat             = array();
                $_im             = array(
                    'crop'  => 'auto',
                    'alt'   => $_row['value'],
                    'title' => $_row['value']
                );
                $dat[1]['title'] = jrImage_get_image_src('jrUser', 'user_image', $_row['sale_buyer_user_id'], 'xxsmall', $_im);
                $dat[2]['title'] = $_row['value'];
                $dat[3]['title'] = jrCore_format_time($_row['sale_time']);
                $dat[3]['class'] = 'center';
                $dat[4]['title'] = $_row['item_count'];
                $dat[4]['class'] = 'center';
                $dat[5]['title'] = jrFoxyCart_currency_format($_row['amt']);
                $dat[5]['class'] = 'center';
                if (isset($_nt["{$_row['sale_txn_id']}"])) {
                    $_sh = array();
                    foreach ($_nt["{$_row['sale_txn_id']}"] as $rk => $rv) {
                        if (strpos($rk, 'txn_') === 0) {
                            if ($rk == 'txn_items') {
                                foreach (json_decode($rv, true) as $itk => $itv) {
                                    foreach ($itv as $sk => $sv) {
                                        if (strpos($sk, 'subscr') !== 0) {
                                            $_sh[] = "(item " . ($itk + 1) . ") {$sk}: {$sv}";
                                        }
                                    }
                                }
                            }
                            else {
                                $_sh[] = "{$rk}: {$rv}";
                            }
                        }
                    }
                    $dat[6]['title'] = '<a href="' . $_conf['jrCore_base_url'] . '/' . $_post['module_url'] . '/txn_details/' . $_row['sale_txn_id'] . '" title="' . implode('&#10;', $_sh) . '">' . $_row['sale_txn_id'] . '</a>';
                }
                else {
                    $dat[6]['title'] = '<a href="' . $_conf['jrCore_base_url'] . '/' . $_post['module_url'] . '/txn_details/' . $_row['sale_txn_id'] . '">' . $_row['sale_txn_id'] . '</a>';
                }
                $dat[6]['class'] = 'center';
                $dat[7]['title'] = jrCore_page_button("t{$k}", 'details', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/txn_details/{$_row['sale_txn_id']}')");
                jrCore_page_table_row($dat);
            }
            jrCore_page_table_pager($_rt);
        }
        else {
            $dat             = array();
            $dat[1]['title'] = '<p>There are no transactions to show</p>';
            $dat[1]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_footer();
        jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    }
    else {

        jrCore_page_banner($_lang['jrFoxyCart'][27] . $txn_id);
        jrCore_page_section_header('Transaction');

        $_ex = jrCore_db_get_item_by_key('jrFoxyCart', 'txn_id', $txn_id);
        if (is_array($_ex)) {

            $_tx = array();
            if (isset($_ex['txn_items']) && strlen($_ex['txn_items']) > 0) {
                $_tm = json_decode($_ex['txn_items'], true);
                foreach ($_tm as $_ti) {
                    $_tx["{$_ti['product_code']}"] = $_ti['product_price'];
                }
                unset($_ex['txn_items'], $_tm);
            }

            // Expand transaction
            $dat             = array();
            $dat[1]['title'] = 'key';
            $dat[1]['width'] = '20%';
            $dat[2]['title'] = 'value';
            $dat[2]['width'] = '80%';
            jrCore_page_table_header($dat);

            $tid = reset($_ex);
            $_or = array('txn_id' => $tid);
            array_shift($_ex);
            ksort($_ex);
            $_or = array_merge($_or, $_ex);

            foreach ($_or as $k => $v) {
                if (strlen($v) === 0 || strpos($k, 'txn_') !== 0 || strpos(' ' . $k, 'txn_custom_')) {
                    continue;
                }
                if (!jrUser_is_admin() && (strpos(' ' . $k, 'email') || strpos(' ' . $k, 'address') || strpos(' ' . $k, 'customer_ip'))) {
                    continue;
                }
                $dat[1]['title'] = $k;
                if ($k == 'txn_date' && jrCore_checktype($v, 'number_nz')) {
                    $dat[2]['title'] = jrCore_format_time($v);
                }
                else {
                    $dat[2]['title'] = $v;
                }
                jrCore_page_table_row($dat);
            }
            jrCore_page_table_footer();

            // show the purchases table
            jrCore_page_section_header('Purchases');

            $dat             = array();
            $dat[1]['title'] = 'seller';
            $dat[2]['title'] = 'buyer';
            $dat[3]['title'] = 'Product Code';
            $dat[4]['title'] = 'qty';
            $dat[5]['title'] = 'product name';
            $dat[6]['title'] = 'product price';
            jrCore_page_table_header($dat);

            $tbl = jrCore_db_table_name('jrFoxyCart', 'purchase');
            $req = "SELECT * FROM {$tbl} WHERE purchase_txn_id = '{$txn_id}'";
            $_rt = jrCore_db_query($req, 'NUMERIC');

            $_us = array();
            if (is_array($_rt)) {
                $_us = jrCore_db_get_item('jrUser', $_rt[0]['purchase_user_id'], true);
            }
            if ($_rt && is_array($_rt)) {
                foreach ($_rt as $_item) {

                    $iid = (int) $_item['purchase_item_id'];
                    $cod = "{$_item['purchase_module']}-{$iid}";
                    $pfx = jrCore_db_get_prefix($_item['purchase_module']);
                    $_it = jrCore_db_get_item($_item['purchase_module'], $iid);

                    $purl            = "{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$_post['module_url']}";
                    $dat[1]['title'] = '<a href="' . $purl . '/customers">' . $_it['profile_name'] . '</a>';
                    $dat[2]['title'] = '<a href="' . $purl . '/customers/user_id=' . intval($_item['purchase_user_id']) . '">' . $_us['user_name'] . '</a>';
                    $dat[2]['class'] = 'center';
                    $dat[3]['title'] = "{$_item['purchase_module']}-{$iid}";
                    $dat[3]['class'] = 'center';
                    $dat[4]['title'] = $_item['purchase_qty'];
                    $dat[4]['class'] = 'center';
                    $dat[5]['title'] = '<a href="' . $purl . '/products">' . $_it["{$pfx}_title"] . '</a>';
                    $dat[5]['class'] = 'center';
                    $dat[6]['title'] = jrFoxyCart_currency_format($_tx[$cod]);
                    $dat[6]['class'] = 'right';
                    jrCore_page_table_row($dat);
                }
            }
            jrCore_page_table_footer();
        }
        jrCore_page_cancel_button('referrer');
    }
    jrCore_page_display();
}

//-------------------------------------------------------
// redirect to the transaction details display page.
//-------------------------------------------------------
function view_jrFoxyCart_txn_details_save($_post, &$_user, &$_conf)
{
    $murl = jrCore_get_module_url('jrFoxyCart');
    if (isset($_post['txn_id']) && is_numeric($_post['txn_id'])) {
        // redirect to the transaction id.
        jrCore_location($_conf['jrCore_base_url'] . '/' . $murl . '/txn_details/' . $_post['txn_id']);
    }
    jrCore_location($_conf['jrCore_base_url'] . '/' . $murl . '/txn_details');
}

//---------------------------
// query
//---------------------------
function view_jrFoxyCart_query($_post, $_user, $_conf)
{
    if (!isset($_post['_profile_id']) || !jrCore_checktype($_post['_profile_id'], 'number_nn')) {
        $_er = array('error', 'Invalid _profile_id');
        jrCore_json_response($_er);
    }

    $pid = (int) $_post['_profile_id'];
    if ($pid == 0 && jrUser_is_master()) {
        //get details for every thing
        $profile_url = '';
    }
    else {
        //check the user owns the profile
        if (!jrProfile_is_profile_owner($pid)) {
            $_er = array('error', 'you do not have permissions to perform that action');
            jrCore_json_response($_er);
        }
        $_profile    = jrCore_db_get_item('jrProfile', $pid, true);
        $profile_url = $_profile['profile_url'] . '/';
    }

    if (isset($_post['buyer']) && strlen($_post['buyer']) > 0) {
        $_bid = jrCore_db_get_item_by_key('jrUser', 'user_name', $_post['buyer']);
        if (is_array($_bid)) {
            $bid = (int) $_bid['_user_id'];
        }
        else {
            echo '<div class="item error">No User found to match your Filter</div>';
            exit;
        }
    }
    $_purchases_counts = array();
    $_sales_values     = array();
    $_unique_products  = array();

    $purchase_tbl = jrCore_db_table_name('jrFoxyCart', 'purchase');
    $req          = "SELECT * FROM {$purchase_tbl} WHERE 1";
    if (isset($pid) && $pid > 0) {
        $req .= " AND purchase_seller_profile_id = {$pid}";
    }
    if (isset($bid) && $bid > 0) {
        $req .= " AND purchase_user_id = {$bid}";
    }

    $req .= " ORDER BY purchase_created  DESC ";
    $_rt = jrCore_db_query($req, 'NUMERIC');

    // purchases
    $_purchases = array();
    foreach ($_rt as $k => $_p) {
        $_purchases[$k]                     = $_p;
        $_tmp                               = json_decode($_p['purchase_data'], true);
        $pfx                                = jrCore_db_get_prefix($_p['purchase_module']);
        $product_name                       = $_tmp[$pfx . '_title'];
        $_purchases[$k]['purchase_name']    = $product_name;
        $_purchases[$k]['purchase_data']    = $_tmp;
        $_purchases[$k]['purchase_seller']  = jrCore_db_get_item('jrProfile', $_p['purchase_seller_profile_id'], true);
        $_purchases[$k]['purchase_buyer']   = jrCore_db_get_item('jrUser', $_p['purchase_user_id'], true);
        $_purchases[$k]['purchase_type']    = jrCore_db_get_prefix($_p['purchase_module']);
        $_purchases[$k]['purchase_details'] = jrCore_db_get_prefix($_p['purchase_module']);
        $purchase_date                      = date('Y-m-d', $_p['purchase_created']); //2012-05-19

        // stupid javascript Date.UTC() making 0 = jan and 1 = feb.......
        $Date = new DateTime();
        $Date->setTimestamp($_p['purchase_created']);
        $str = $Date->format('Y') . ', ';
        $str .= $Date->format('n') - 1 . ', ';
        $str .= $Date->format('j');
        $utc_purchase_date = $str;

        $_purchases[$k]['purchase_date'] = $purchase_date;
        $_purchases_counts[$utc_purchase_date]++;

        // unique_products
        $product_code                            = $_p['purchase_module'] . '-' . $_p['purchase_item_id'];
        $_unique_products[$product_code]['code'] = $product_code;
        $_unique_products[$product_code]['name'] = $product_name;
        $_unique_products[$product_code]['qty'] += $_p['purchase_qty'];

    }

    //get the sales values
    $sale_tbl = jrCore_db_table_name('jrFoxyCart', 'sale');
    $req      = "SELECT * FROM {$sale_tbl} WHERE 1 ";
    if (isset($pid) && $pid > 0) {
        $req .= " AND sale_seller_profile_id = '{$pid}' ";
    }
    if (isset($bid) && $bid > 0) {
        $req .= " AND sale_buyer_user_id = '{$bid}' ";
    }
    $req .= " ORDER BY sale_time DESC ";
    $_rts = jrCore_db_query($req, 'NUMERIC');

    // sales
    $_sales             = array();
    $_lifetime_spending = array();
    $_lifetime_sales    = array();
    foreach ($_rts as $k => $_s) {

        $_sales[$k]                = $_s;
        $_sales[$k]['sale_seller'] = jrCore_db_get_item('jrProfile', $_s['sale_seller_profile_id'], true);
        $_sales[$k]['sale_buyer']  = jrCore_db_get_item('jrUser', $_s['sale_buyer_user_id'], true);
        $_sales[$k]['sale_date']   = date('Y-m-d', $_s['sale_time']);

        //stupid javascript Date.UTC() making 0 = jan and 1 = feb.......
        $Date = new DateTime();
        $Date->setTimestamp($_s['sale_time']);
        $str = $Date->format('Y') . ', ';
        $str .= $Date->format('n') - 1 . ', ';
        $str .= $Date->format('j');
        $utc_sale_date = $str;

        $_sales_values[$utc_sale_date] += $_s['sale_gross'];

        // lifetime_spending
        $_lifetime_spending[$_s['sale_buyer_user_id']]['_buyer'] = $_sales[$k]['sale_buyer'];
        $_lifetime_spending[$_s['sale_buyer_user_id']]['lifetime_spending'] += $_s['sale_gross'];

        // lifetime_earnings
        $_lifetime_sales[$_s['sale_seller_profile_id']]['_seller'] = $_sales[$k]['sale_seller'];
        $_lifetime_sales[$_s['sale_seller_profile_id']]['lifetime_sales'] += $_s['sale_gross'];
    }

    // get the average user purchase value.
    $_avg_value = array();
    foreach ($_sales_values as $k => $v) {
        if (isset($_purchases_counts[$k]) && $_purchases_counts[$k] > 0) {
            $ct = $_purchases_counts[$k];
        }
        else {
            $ct = 1;
        }
        $_avg_value[$k] = $v / $ct;
    }

    //out to the templates
    $_replace = array(
        'purchases'        => $_purchases,
        'purchases_counts' => $_purchases_counts,
        'sales'            => $_sales,
        'sales_values'     => $_sales_values,
        'avg_value'        => $_avg_value,
        'buyers'           => $_lifetime_spending,
        'sellers'          => $_lifetime_sales,
        'unique_products'  => $_unique_products,
        'purl'             => $profile_url
    );
    return jrCore_parse_template("query.tpl", $_replace, 'jrFoxyCart');
}

//---------------------------
// Remote Templates
//---------------------------
function view_jrFoxyCart_remote_templates($_post, $_user, $_conf)
{
    $_replace = array();
    return jrCore_parse_template("rt_checkout.tpl", $_replace, 'jrFoxyCart');
}

//---------------------------
// Remote Templates
//---------------------------
function view_jrFoxyCart_remote_templates_20($_post, $_user, $_conf)
{
    $file = APP_DIR ."/modules/jrFoxyCart/templates/rt_checkout-2.0.twig";
    readfile($file);
    exit();
}

//---------------------------
// Subscription Cancel
//---------------------------
function view_jrFoxyCart_subscription_cancel($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();

    $_ln = jrUser_load_lang_strings();
    // If we get a subscription token, it means the user is currently
    // on a PAID subscription, and is moving to a FREE subscription
    if (isset($_post['sub_token']) && strlen($_post['sub_token']) > 0) {

        // We have a subscription token - moving from paid to free
        $sub_token = (string) $_post['sub_token'];

        // check to see if this user owns this subscription, or is the admin.
        if (!jrUser_is_admin() && isset($_user['user_foxycart_customer_id'])) {
            // query API for this users subscriptions
            $_rs = array(
                'api_action'         => 'subscription_list',
                'is_active_filter'   => '',
                'customer_id_filter' => $_user['user_foxycart_customer_id']
            );
            $xml = jrFoxyCart_api($_rs);
            if ($xml->result == "SUCCESS") {
                //get sub_token this user controls.
                $_tokens = array();
                foreach ($xml->subscriptions->subscription as $_sub) {
                    $_tokens[] = (string) $_sub->sub_token;
                }
                if (count($_tokens) == 0 || !in_array($sub_token, $_tokens)) {
                    jrCore_set_form_notice('error', 'Unable to find active subscription token - please try again');
                    jrCore_location('referrer');
                }
            }
            else {
                // no success contacting foxycart API
                jrCore_set_form_notice('error', 'An error was encountered contacting the subscription service - please try again shortly.');
                jrCore_location('referrer');
            }
        }
        else {
            // not the owner or the admin, fail.
            jrCore_set_form_notice('error', 'Unable to find subscription customer_id - please re-login and try again');
            jrCore_location('referrer');
        }

        // send the api call to cancel the subscription
        $_rs = array(
            'api_action' => 'subscription_modify',
            'is_active'  => '0',
            'sub_token'  => $sub_token
        );
        $xml = jrFoxyCart_api($_rs);
        if ($xml && $xml->result == "SUCCESS") {

            // Set expiration and quota
            // NOTE: profile_quota_id is NOT changed here on purpose - the profile will
            // be moved to the new quota_id after their subscription runs out
            $qid = (int) $_conf['jrProfile_default_quota_id'];
            if (isset($_post['quota_id']) && jrCore_checktype($_post['quota_id'], 'number_nz')) {
                // They have selected the quota they want to move to - make sure it can be signed up to
                $_qt = jrProfile_get_quota($_post['quota_id']);
                if (!is_array($_qt)) {
                    jrCore_set_form_notice('error', 'The Subscription you have selected to move to is not a valid subscription - please try again');
                    jrCore_location('referrer');
                }
                if (!isset($_qt['quota_jrUser_allow_signups']) || $_qt['quota_jrUser_allow_signups'] != 'on') {
                    jrCore_set_form_notice('error', 'The Subscription you have selected to move to cannot be subscribed to - please try again');
                    jrCore_location('referrer');
                }
                $qid = (int) $_post['quota_id'];
            }

            // Set up our expires
            list($y, $m, $d) = explode('-', $xml->subscription->next_transaction_date);
            $dat = (int) "{$y}{$m}{$d}";
            $tbl = jrCore_db_table_name('jrFoxyCart', 'sub_expire');
            $req = "INSERT INTO {$tbl} (sub_expire_date, sub_profile_id, sub_new_quota_id) VALUES ('{$dat}', '{$_user['user_active_profile_id']}', '{$qid}')";
            $cnt = jrCore_db_query($req, 'COUNT');
            if (!$cnt || $cnt != '1') {
                jrCore_set_form_notice('error', 'Unable to create subscription expiration date - please try again');
                jrCore_location('referrer');
            }

            jrCore_logger('INF', "created new subscription expiration entry of {$dat} for profile_id {$_user['user_active_profile_id']} - from quota_id {$_user['profile_quota_id']} to quota_id {$qid}");
            jrCore_set_form_notice('success', $_ln['jrFoxyCart'][107] . "{$y}-{$m}-{$d}");
            jrCore_location('referrer');
        }
        jrCore_set_form_notice('error', 'An error was encountered contacting the subscription service - please try again shortly.');
        jrCore_location('referrer');
    }

    // We are on a FREE subscription, moving to another FREE subscription
    // Verify incoming quota allows sign ups
    if (isset($_post['quota_id']) && jrCore_checktype($_post['quota_id'], 'number_nz')) {
        $_qt = jrProfile_get_quota($_post['quota_id']);
        if (!is_array($_qt)) {
            jrCore_set_form_notice('error', 'The Subscription you have selected to move to is not a valid subscription - please try again');
            jrCore_location('referrer');
        }
        if (!isset($_qt['quota_jrUser_allow_signups']) || $_qt['quota_jrUser_allow_signups'] != 'on') {
            jrCore_set_form_notice('error', 'The Subscription you have selected to move to cannot be subscribed to - please try again');
            jrCore_location('referrer');
        }
        $_up = array(
            'profile_quota_id' => (int) $_post['quota_id']
        );
        if (jrCore_db_update_item('jrProfile', $_user['user_active_profile_id'], $_up)) {
            // Update counts in both Quotas
            jrProfile_increment_quota_profile_count($_up['profile_quota_id']);
            jrProfile_decrement_quota_profile_count($_user['profile_quota_id']);
            jrCore_delete_all_cache_entries(null, $_user['_user_id']);
            jrProfile_reset_cache($_user['user_active_profile_id']);
            jrCore_set_form_notice('success', $_ln['jrFoxyCart'][110]);
            jrCore_location('referrer');
        }
    }
    jrCore_set_form_notice('error', 'An error was encountered updating the subscription - please try again');
    jrCore_location('referrer');
}
