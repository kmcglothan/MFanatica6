<?php
/**
 * Jamroom Merchandise module
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
// Merchandise Sales
//------------------------------
function profile_view_jrStore_sales($_profile, $_post, $_user, $_conf)
{

    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        return false;
    }

    $_replace = array();
    //profile
    foreach ($_profile as $k => $v) {
        $_replace[$k] = $v;
    }
    //see if there is a specific order that we are viewing.
    if (isset($_post['_2']) && is_numeric($_post['_2'])) {
        $txn_id = (int) $_post['_2'];
    }

    if (isset($txn_id) && $txn_id > 0) {

        //only visible for the seller:
        if (isset($_profile['_profile_id']) && is_numeric($_profile['_profile_id'])) {
            $seller_profile_id = $_profile['_profile_id'];
            if ($seller_profile_id != $_user['user_active_profile_id']) {
                jrCore_notice_page('error', 'Only the seller may view the transaction details.');
            }
        }
        else {
            jrCore_notice_page('error', 'Only the seller may view the transaction details.');
        }

        //check this sellers profile id is involved in this transaction
        $purchase_tbl = jrCore_db_table_name('jrFoxyCart', 'purchase');
        $req = "SELECT *
                               FROM {$purchase_tbl}
                              WHERE purchase_txn_id = '{$txn_id}'
                                AND purchase_seller_profile_id = '{$seller_profile_id}' ";
        $_sold_items = jrCore_db_query($req, 'NUMERIC');
        if (!is_array($_sold_items)) {
            jrCore_notice_page('error', 'Only the seller may view the transaction details.');
        }
        //get the items purchased from this seller
        foreach ($_sold_items as $k => $_si) {
            $_replace['sold_items'][$k] = $_si;
            $_replace['sold_items'][$k]['details'] = jrCore_db_get_item('jrStore', $_si['purchase_item_id']);
        }

        //get the txn details.
        $_sp = array(
            'search'        => array(
                "txn_id = $txn_id"
            ),
            'skip_triggers' => true
        );
        $_rt = jrCore_db_search_items('jrFoxyCart', $_sp);

        //get all the txn_*
        foreach ($_rt['_items'][0] as $k => $v) {
            $pfx = substr($k, 0, 3);
            if ($pfx == 'txn') {
                $_replace[$k] = $v;
            }
        }

        $sale_tbl = jrCore_db_table_name('jrFoxyCart', 'sale');
        $req = "SELECT sum(sale_gross) as sale_gross,
                        sum(sale_shipping) as sale_shipping,
                        sum(sale_system_fee) as sale_system_fee
                  FROM {$sale_tbl}
                 WHERE sale_txn_id = '{$txn_id}'
                   AND sale_seller_profile_id = '{$seller_profile_id}'
              GROUP BY sale_txn_id ";
        $money_totals = jrCore_db_query($req, 'SINGLE');

        //over-ride for just this sellers stuff
        $_replace['txn_order_total'] = jrFoxyCart_currency_format($money_totals['sale_gross'] + $money_totals['sale_shipping']);
        $_replace['txn_product_total'] = jrFoxyCart_currency_format($money_totals['sale_gross']);
        $_replace['txn_shipping_total'] = jrFoxyCart_currency_format($money_totals['sale_shipping']);
        $_replace['txn_sale_system_fee'] = jrFoxyCart_currency_format($money_totals['sale_system_fee']);

        $_replace['buyer'] = jrCore_db_get_item('jrUser', $_sold_items[0]['purchase_user_id']);
        $_replace['seller'] = jrCore_db_get_item('jrProfile', $_sold_items[0]['purchase_seller_profile_id']);
        unset($_replace['buyer']['user_password']);
        unset($_replace['seller']['user_password']);

        //get the status.
        $_replace['status_status'] = jrStore_get_status($txn_id, $seller_profile_id);

        switch ($_post['_3']) {
            case 'communication':
                return jrCore_parse_template("sale_communication.tpl", $_replace, 'jrStore');
                break;
            default:
                return jrCore_parse_template("sale_details.tpl", $_replace, 'jrStore');
                break;
        }

        //show just this transaction.
    }
    else {
        //show the list of transactions
        $purchase_tbl = jrCore_db_table_name('jrFoxyCart', 'purchase');
        $status_tbl = jrCore_db_table_name('jrStore', 'status');
        $req = "SELECT *,
                                  sum(purchase_qty) as item_count
                          FROM {$purchase_tbl} p
                     LEFT JOIN {$status_tbl} s  ON s.status_txn_id = p.purchase_txn_id
                         WHERE purchase_seller_profile_id = '{$_user['user_active_profile_id']}'
                           AND purchase_module = 'jrStore'
                      GROUP BY purchase_txn_id, purchase_seller_profile_id
                      ORDER BY purchase_created DESC ";
        $_rt = jrCore_db_query($req, 'NUMERIC');

        $comment_tbl = jrCore_db_table_name('jrStore', 'comment');
        $_sellers = array();
        $_buyers = array();
        foreach ($_rt as $k => $_transaction) {

            //buyer
            if (isset($_sellers[$_transaction['purchase_seller_user_id']])) {
                //got the seller earlier, use again.
                $_seller = $_sellers[$_transaction['purchase_seller_user_id']];
            }
            else {
                //get this seller
                $_seller = jrCore_db_get_item('jrProfile', $_transaction['purchase_seller_profile_id']);
                $_sellers[$_transaction['purchase_seller_user_id']] = $_seller;
            }

            //buyer
            if (isset($_buyers[$_transaction['purchase_user_id']])) {
                //got the seller earlier, use again.
                $_buyer = $_buyers[$_transaction['purchase_user_id']];
            }
            else {
                //get this seller
                $_buyer = jrCore_db_get_item('jrUser', $_transaction['purchase_user_id']);
                $_buyers[$_transaction['purchase_user_id']] = $_buyer;
            }

            $_txn[$k] = $_transaction;
            $txn_id = (int) $_transaction['purchase_txn_id'];

            //message count
            $req = "SELECT *,
                            COUNT(comment_id) as messages
                      FROM {$comment_tbl}
                     WHERE comment_txn_id = {$txn_id}
                       AND comment_seller_profile_id = '{$_seller['_profile_id']}'
                  GROUP BY comment_seller_profile_id ";
            $_rt = jrCore_db_query($req, 'SINGLE');

            $_txn[$k]['message_count'] = $_rt['messages'];

            //last message from
            $req = "SELECT comment_created, comment_user_id
                      FROM {$comment_tbl}
                      WHERE comment_txn_id = {$txn_id}
                        AND comment_seller_profile_id = '{$_seller['_profile_id']}'
                  ORDER BY comment_created DESC ";
            $_rt = jrCore_db_query($req, 'SINGLE');
            if ($_rt['comment_created'] > 0) {
                $_txn[$k]['message_last_time'] = $_rt['comment_created'];
            }
            else {
                $_txn[$k]['message_last_time'] = $_transaction['purchase_created'];
            }
            if ($_rt['comment_user_id'] > 0) {
                $u = $_rt['comment_user_id'];
            }
            else {
                $u = $_transaction['purchase_seller_user_id'];
            }

            $_txn[$k]['message_last_user'] = jrCore_db_get_item('jrUser', $u);
            $_txn[$k]['seller'] = $_seller;
            $_txn[$k]['buyer'] = $_buyer;

            //sale value
            $sale_tbl = jrCore_db_table_name('jrFoxyCart', 'sale');
            $req = "SELECT *
                      FROM {$sale_tbl}
                     WHERE sale_txn_id = {$txn_id}
                       AND sale_seller_profile_id = '{$_seller['_profile_id']}' ";
            $_rt = jrCore_db_query($req, 'SINGLE');
            $_txn[$k]['sale_gross'] = jrFoxyCart_currency_format($_rt['sale_gross']);
            $_txn[$k]['sale_system_fee'] = jrFoxyCart_currency_format($_rt['sale_system_fee']);
            $_txn[$k]['sale_total_net'] = jrFoxyCart_currency_format($_rt['sale_total_net']);
            $_txn[$k]['sale_shipping'] = jrFoxyCart_currency_format($_rt['sale_shipping']);

            $txn_item = jrCore_db_get_item_by_key('jrFoxyCart', 'txn_id', $txn_id);
            $_txn[$k]['buyer_firstname'] = $txn_item['txn_customer_first_name'];
            $_txn[$k]['buyer_lastname'] = $txn_item['txn_customer_last_name'];

        }
        $_replace['transactions'] = $_txn;

        return jrCore_parse_template("sale_list.tpl", $_replace, 'jrStore');
    }

}

//------------------------------
// Settings
//------------------------------
function profile_view_jrStore_settings($_profile, $_post, $_user, $_conf)
{

    jrUser_session_require_login();
    jrUser_check_quota_access('jrStore');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }

    // Start our create form
    jrCore_page_banner('setup', null, false);

    $_ln = jrUser_load_lang_strings();
    $_rt = array(
        'first_message'    => (isset($_user['profile_jrStore_first_message'])) ? $_user['profile_jrStore_first_message'] : "Thanks for your purchase.\n\nThe next steps are... (what ever your next steps are that you want the customer to perform.)\n\nPlease check that the postal address is correct that we are sending to. If you want it sent somewhere else, please reply here.\n\nThe items will be sent from:\n\n1234 My Address\nSometown\nSome State\nCOUNTRY\n\nSo should take about _______________ Days to reach you if you live in the USA.\n\n(anything else you want your new customer to know/to do after they have purchased).",
        'store_details'    => (isset($_user['profile_jrStore_store_details'])) ? $_user['profile_jrStore_store_details'] : '',
        'store_country'    => (isset($_user['profile_jrStore_store_country'])) ? $_user['profile_jrStore_store_country'] : '',
        'owner_profile_id' => $_profile['_profile_id']
    );

    // Form init
    $_tmp = array(
        'submit_value' => 36,
        'cancel'       => false,
        'action'       => "{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/" . jrCore_get_module_url('jrStore') . "/settings_save",
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // First message
    $_tmp = array(
        'name'     => 'first_message',
        'type'     => 'textarea',
        'validate' => 'printable',
        'required' => 'on',
        'label'    => 38,
        'help'     => 39
    );
    jrCore_form_field_create($_tmp);

    // Store Details
    $_tmp = array(
        'name'     => 'store_details',
        'type'     => 'textarea',
        'validate' => 'printable',
        'required' => 'on',
        'label'    => 40,
        'help'     => 41
    );
    jrCore_form_field_create($_tmp);

    // Store Country
    $_tmp = array(
        'name'     => 'store_country',
        'type'     => 'select',
        'default'  => 'US',
        'options'  => 'jrStore_foxycart_country_codes',
        'validate' => 'not_empty',
        'required' => true,
        'label'    => 44,
        'help'     => 45
    );
    jrCore_form_field_create($_tmp);

    return jrCore_page_display(true);
}

//------------------------------
// settings_save
//------------------------------
function profile_view_jrStore_settings_save($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrStore');
    jrCore_form_validate($_post);

    $_data = array(
        'profile_jrStore_first_message' => $_post['first_message'],
        'profile_jrStore_store_details' => $_post['store_details'],
        'profile_jrStore_store_country' => $_post['store_country'],
    );

    jrCore_db_update_item('jrProfile', $_user['user_active_profile_id'], $_data);
    jrCore_set_form_notice('success', 37);
    jrCore_form_result();
}
