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

/**
 * Default View
 * @param $_profile array Profile Info
 * @param $_post array Post info
 * @param $_user array Current user info
 * @param $_conf array Global conf
 * @return null
 */
function profile_view_jrFoxyCart_default($_profile, $_post, $_user, $_conf)
{
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        return jrCore_location($_conf['jrCore_base_url'] . '/' . $_profile['profile_url']);
    }
    //redirect to earnings
    $murl = jrCore_get_module_url('jrFoxyCart');
    return jrCore_location($_conf['jrCore_base_url'] . '/' . $_profile['profile_url'] . '/' . $murl . '/earnings');
}

//------------------------------
// Register
//------------------------------
function profile_view_jrFoxyCart_register($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFoxyCart');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }
    $murl = jrCore_get_module_url('jrFoxyCart');
    $temp = jrCore_page_button('new', 'New Transaction', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$murl}/record_create')");
    jrCore_page_banner('transaction register', $temp);
    jrCore_get_form_notice();
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$murl}/register");

    $dat             = array();
    $dat[1]['title'] = 'date';
    $dat[1]['width'] = '15%';
    $dat[2]['title'] = 'email';
    $dat[2]['width'] = '35%';
    $dat[3]['title'] = 'income';
    $dat[3]['width'] = '10%';
    $dat[4]['title'] = 'expense';
    $dat[4]['width'] = '10%';
    $dat[5]['title'] = 'category';
    $dat[5]['width'] = '20%';
    $dat[6]['title'] = 'modify';
    $dat[6]['width'] = '5%';
    $dat[7]['title'] = 'delete';
    $dat[7]['width'] = '5%';
    $dat[8]['title'] = 'view';
    $dat[8]['width'] = '5%';
    jrCore_page_table_header($dat);

    $off = date_offset_get(new DateTime);
    $m1  = '';
    $m2  = '';
    $m3  = '';
    if (isset($_post['m']) && strlen($_post['m']) > 0) {
        $m1 = " AND FROM_UNIXTIME(a.sale_time + {$off},'%M') = '" . jrCore_db_escape($_post['m']) . "'";
        $m2 = " AND FROM_UNIXTIME(record_time + {$off},'%M') = '" . jrCore_db_escape($_post['m']) . "'";
        $m3 = " AND FROM_UNIXTIME(p.purchase_created + {$off},'%M') = '" . jrCore_db_escape($_post['m']) . "'";
    }
    $y1 = '';
    $y2 = '';
    $y3 = '';
    if (isset($_post['y']) && jrCore_checktype($_post['y'], 'number_nz')) {
        $y1 = " AND FROM_UNIXTIME(a.sale_time + {$off},'%Y') = '{$_post['y']}'";
        $y2 = " AND FROM_UNIXTIME(record_time + {$off},'%Y') = '{$_post['y']}'";
        $y3 = " AND FROM_UNIXTIME(p.purchase_created + {$off},'%Y') = '{$_post['y']}'";
    }

    // Get all sales + expenses
    $tb1 = jrCore_db_table_name('jrFoxyCart', 'sale');
    $tb2 = jrCore_db_table_name('jrUser', 'item_key');
    $tb3 = jrCore_db_table_name('jrFoxyCart', 'purchase');
    $tb4 = jrCore_db_table_name('jrFoxyCart', 'ledger');

    // If we are on the SYSTEM PROFILE ID, we want to show subscription payments
    // as well as any other "system level" income in this profile's ledger
    $add = '';
    $sr1 = '';
    $sr2 = '';
    $sr3 = '';
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $sr1 = " AND (a.sale_txn_id LIKE '%" . jrCore_db_escape($_post['search_string']) . "%' OR u.`value` LIKE '%" . jrCore_db_escape($_post['search_string']) . "%')";
        $sr2 = " AND record_email LIKE '%" . jrCore_db_escape($_post['search_string']) . "%'";
        $sr3 = " AND u.`value` LIKE '%" . jrCore_db_escape($_post['search_string']) . "%'";
    }
    if (isset($_conf['jrFoxyCart_system_profile_id']) && $_conf['jrFoxyCart_system_profile_id'] == $_profile['_profile_id']) {
        $add = " UNION ALL (SELECT '0' AS r_id, p.purchase_created AS r_time, u.`value` AS r_email, '0' AS r_bundle_id, p.purchase_price AS r_income, '0' AS r_expense, p.purchase_txn_id AS r_txn_id, 'subscriptions' AS r_category
                 FROM {$tb3} p
            LEFT JOIN {$tb2} u ON (u.`_item_id` = p.purchase_user_id AND u.`key` = 'user_email')
                 WHERE p.purchase_module = 'jrFoxyCart'{$sr3}{$m3}{$y3})";
    }
    $req = "(SELECT '0' AS r_id, a.sale_time AS r_time, u.`value` AS r_email, a.sale_bundle_id AS r_bundle_id, a.sale_gross AS r_income, a.sale_system_fee AS r_expense, a.sale_txn_id AS r_txn_id, p.purchase_field AS r_category
               FROM {$tb1} a
          LEFT JOIN {$tb2} u ON (u.`_item_id` = a.sale_buyer_user_id AND u.`key` = 'user_email')
          LEFT JOIN {$tb3} p ON p.purchase_id = a.sale_purchase_id
              WHERE a.sale_seller_profile_id = '{$_profile['_profile_id']}'{$sr1}{$m1}{$y1})
          UNION ALL
            (SELECT record_id AS r_id, record_time AS r_time, record_email as r_email, '0' AS r_bundle_id, record_income AS r_income, record_expense AS r_expense, record_transaction_id AS r_txn_id, record_category AS r_category
               FROM {$tb4}
              WHERE record_profile_id = '{$_profile['_profile_id']}'{$sr2}{$m2}{$y2}){$add}
           ORDER BY r_time DESC";
    $page = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $page = (int) $_post['p'];
    }
    $_rt = jrCore_db_paged_query($req, $page, 12, 'NUMERIC');
    if (is_array($_rt['_items'])) {

        foreach ($_rt['_items'] as $k => $_v) {
            $dat             = array();
            $dat[1]['title'] = jrCore_format_time($_v['r_time']);
            $dat[1]['class'] = 'center';
            if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
                $dat[2]['title'] = jrCore_hilight_string($_v['r_email'], $_post['search_string']);
            }
            else {
                $dat[2]['title'] = $_v['r_email'];
            }
            $dat[3]['title'] = '<strong>' . jrFoxyCart_currency_format($_v['r_income']) . '</strong>';
            $dat[3]['class'] = 'right';
            $dat[4]['title'] = jrFoxyCart_currency_format($_v['r_expense']);
            $dat[4]['class'] = 'right';
            if (isset($_v['r_bundle_id']) && $_v['r_bundle_id'] > 0) {
                $dat[5]['title'] = 'bundles';
            }
            else {
                $dat[5]['title'] = $_v['r_category'];
            }
            $dat[5]['class'] = 'center';
            if ($_v['r_id'] > 0) {
                $dat[6]['title'] = jrCore_page_button("m{$k}", "modify", "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$murl}/record_modify/id={$_v['r_id']}')");
                $dat[7]['title'] = jrCore_page_button("d{$k}", "delete", "if(confirm('Are you sure you want to delete this record?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$murl}/record_delete/id={$_v['r_id']}') }");
            }
            else {
                $dat[6]['title'] = jrCore_page_button("m{$k}", "modify", 'disabled');
                $dat[7]['title'] = jrCore_page_button("d{$k}", "delete", 'disabled');
            }
            $dat[8]['title'] = jrCore_page_button("t{$k}", 'view', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$murl}/txn_details/{$_v['r_txn_id']}')");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>There are no transactions in the register</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_title("Transaction Register - {$_user['profile_name']}");
    jrCore_page_table_footer();
    return jrCore_page_display(true);
}

//------------------------------
// Record Create
//------------------------------
function profile_view_jrFoxyCart_record_create($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFoxyCart');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }
    jrProfile_set_active_profile_tab('register');
    jrCore_page_banner('Create Transaction');
    jrCore_get_form_notice();

    // Form init
    $murl = jrCore_get_module_url('jrFoxyCart');
    $_tmp = array(
        'submit_value'     => 'save transaction',
        'action'           => "{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$murl}/record_create_save",
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$murl}/register",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'     => 'record_email',
        'label'    => 'email address',
        'help'     => 'Enter the email address used in this transaction',
        'type'     => 'text',
        'validate' => 'email',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'record_transaction_id',
        'label'    => 'transaction id',
        'help'     => 'Enter the transaction id for this transaction',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'record_income',
        'label'    => 'income',
        'help'     => 'Enter any income associated with this transaction',
        'type'     => 'text',
        'validate' => 'price',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'record_expense',
        'label'    => 'expense',
        'help'     => 'Enter any expense associated with this transaction',
        'type'     => 'text',
        'validate' => 'price',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $tbl  = jrCore_db_table_name('jrFoxyCart', 'ledger');
    $req  = "SELECT record_category FROM {$tbl} WHERE record_profile_id = '{$_profile['_profile_id']}' GROUP BY record_category ORDER BY record_category ASC";
    $_rt  = jrCore_db_query($req, 'record_category', false, 'record_category');
    $_tmp = array(
        'name'     => 'record_category',
        'label'    => 'category',
        'help'     => 'Enter a category for this expense',
        'type'     => 'select_and_text',
        'options'  => $_rt,
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'record_time',
        'label'    => 'date',
        'help'     => 'Enter the date this transaction occured on',
        'type'     => 'datetime',
        'validate' => 'date',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'record_details',
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
// Record Create Save
//------------------------------
function profile_view_jrFoxyCart_record_create_save($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFoxyCart');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }
    jrCore_form_validate($_post);

    $pid = (int) $_profile['_profile_id'];
    $txn = jrCore_db_escape($_post['record_transaction_id']);
    $eml = jrCore_db_escape($_post['record_email']);
    $inc = jrCore_db_escape($_post['record_income']);
    $exp = jrCore_db_escape($_post['record_expense']);
    $cat = jrCore_db_escape($_post['record_category']);
    $xtr = jrCore_db_escape($_post['record_details']);
    $tim = jrCore_db_escape($_post['record_time']);
    $tbl = jrCore_db_table_name('jrFoxyCart', 'ledger');
    $req = "INSERT INTO {$tbl} (record_profile_id, record_transaction_id, record_email, record_time, record_income, record_expense, record_category, record_details)
            VALUES ('{$pid}', '{$txn}', '{$eml}', '{$tim}', '{$inc}', '{$exp}', '{$cat}', '{$xtr}')";
    $rid = jrCore_db_query($req, 'INSERT_ID');
    if ($rid && jrCore_checktype($rid, 'number_nz')) {
        jrCore_form_delete_session();
        jrCore_set_form_notice('success', 'The new transaction was successfully saved');
        $murl = jrCore_get_module_url('jrFoxyCart');
        jrCore_location("{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$murl}/register");
    }
    jrCore_set_form_notice('error', 'An error was encountered saving the new transaction - please try again');
    jrCore_location('referrer');
}

//------------------------------
// Record Delete
//------------------------------
function profile_view_jrFoxyCart_record_delete($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrFoxyCart');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid record id');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrFoxyCart', 'ledger');
    $req = "SELECT * FROM {$tbl} WHERE record_id = '{$_post['id']}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Invalid record id (2)');
        jrCore_location('referrer');
    }
    $req = "DELETE FROM {$tbl} WHERE record_id = '{$_post['id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt == 1) {
        jrCore_logger('INF', "successfully deleted transaction record_id {$_post['id']}", $_rt);
        jrCore_set_form_notice('success', 'The record was successfully deleted');
    }
    else {
        jrCore_set_form_notice('error', "an error was encountered trying to delete the record - please try again");
    }
    jrCore_location('referrer');
}

//------------------------------
// Record Modify
//------------------------------
function profile_view_jrFoxyCart_record_modify($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFoxyCart');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid record id');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrFoxyCart', 'ledger');
    $req = "SELECT * FROM {$tbl} WHERE record_id = '{$_post['id']}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Invalid record id (2)');
        jrCore_location('referrer');
    }
    jrProfile_set_active_profile_tab('register');
    jrCore_page_banner('Modify Transaction');
    jrCore_get_form_notice();

    // Form init
    $murl = jrCore_get_module_url('jrFoxyCart');
    $_tmp = array(
        'submit_value'     => 'save transaction',
        'action'           => "{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$murl}/record_modify_save",
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$murl}/register",
        'form_ajax_submit' => false,
        'values'           => $_rt
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'  => 'id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'record_email',
        'label'    => 'email address',
        'help'     => 'Enter the email address used in this transaction',
        'type'     => 'text',
        'validate' => 'email',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'record_transaction_id',
        'label'    => 'transaction id',
        'help'     => 'Enter the transaction id for this transaction',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'record_income',
        'label'    => 'income',
        'help'     => 'Enter any income associated with this transaction',
        'type'     => 'text',
        'validate' => 'price',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'record_expense',
        'label'    => 'expense',
        'help'     => 'Enter any expense associated with this transaction',
        'type'     => 'text',
        'validate' => 'price',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $tbl  = jrCore_db_table_name('jrFoxyCart', 'ledger');
    $req  = "SELECT record_category FROM {$tbl} WHERE record_profile_id = '{$_profile['_profile_id']}' GROUP BY record_category ORDER BY record_category ASC";
    $_rt  = jrCore_db_query($req, 'record_category', false, 'record_category');
    $_tmp = array(
        'name'     => 'record_category',
        'label'    => 'category',
        'help'     => 'Enter a category for this expense',
        'type'     => 'select_and_text',
        'options'  => $_rt,
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'record_time',
        'label'    => 'date',
        'help'     => 'Enter the date this transaction occured on',
        'type'     => 'datetime',
        'validate' => 'date',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'record_details',
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
// Record Modify Save
//------------------------------
function profile_view_jrFoxyCart_record_modify_save($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFoxyCart');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }
    jrCore_form_validate($_post);

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid record id');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrFoxyCart', 'ledger');
    $req = "SELECT * FROM {$tbl} WHERE record_id = '{$_post['id']}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt) || $_rt['record_profile_id'] != $_profile['_profile_id']) {
        jrCore_set_form_notice('error', 'Invalid record id');
        jrCore_location('referrer');
    }

    $txn = jrCore_db_escape($_post['record_transaction_id']);
    $eml = jrCore_db_escape($_post['record_email']);
    $inc = jrCore_db_escape($_post['record_income']);
    $exp = jrCore_db_escape($_post['record_expense']);
    $cat = jrCore_db_escape($_post['record_category']);
    $xtr = jrCore_db_escape($_post['record_details']);
    $tim = jrCore_db_escape($_post['record_time']);
    $tbl = jrCore_db_table_name('jrFoxyCart', 'ledger');
    $req = "UPDATE {$tbl} SET record_transaction_id = '{$txn}', record_email = '{$eml}', record_time = '{$tim}', record_income = '{$inc}', record_expense = '{$exp}', record_category = '{$cat}', record_details = '{$xtr}'
             WHERE record_id = '{$_post['id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_form_delete_session();
        jrCore_set_form_notice('success', 'The transaction was successfully updated');
        $murl = jrCore_get_module_url('jrFoxyCart');
        jrCore_location("{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$murl}/register");
    }
    jrCore_set_form_notice('error', 'An error was encountered saving the transaction - please try again');
    jrCore_location('referrer');
}

//------------------------------
// Ledger
//------------------------------
function profile_view_jrFoxyCart_ledger($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFoxyCart');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }

    // Get our current monthly balance
    $off = date_offset_get(new DateTime);
    $tb1 = jrCore_db_table_name('jrFoxyCart', 'sale');
    $tb2 = jrCore_db_table_name('jrFoxyCart', 'ledger');
    $add = '';
    if (isset($_conf['jrFoxyCart_system_profile_id']) && $_conf['jrFoxyCart_system_profile_id'] == $_profile['_profile_id']) {
        $tb3 = jrCore_db_table_name('jrFoxyCart', 'purchase');
        $add = " UNION ALL (SELECT FROM_UNIXTIME(purchase_created + {$off},'%Y%M') AS rtime, FROM_UNIXTIME(purchase_created + {$off},'%Y%m') AS stime, SUM(purchase_price) AS income, '0' AS expense
                  FROM {$tb3} WHERE purchase_seller_profile_id = '0' AND purchase_module = 'jrFoxyCart' GROUP BY rtime)";
    }
    $req = "SELECT a.rtime, SUM(a.income) AS income, SUM(a.expense) AS expense FROM (
           (SELECT FROM_UNIXTIME(sale_time + {$off},'%Y%M') AS rtime, FROM_UNIXTIME(sale_time + {$off},'%Y%m') AS stime, SUM(sale_gross) AS income, SUM(sale_system_fee) AS expense
              FROM {$tb1} WHERE sale_seller_profile_id = '{$_profile['_profile_id']}' GROUP BY rtime)
         UNION ALL
           (SELECT FROM_UNIXTIME(record_time + {$off},'%Y%M') AS rtime, FROM_UNIXTIME(record_time + {$off},'%Y%m') AS stime, SUM(record_income) AS income, SUM(record_expense) AS expense
              FROM {$tb2} WHERE record_profile_id = '{$_profile['_profile_id']}' GROUP BY rtime){$add}
            ) a
            GROUP BY a.rtime ORDER BY a.stime DESC";
    $_rt = jrCore_db_query($req, 'rtime');

    $now = strftime('%Y%B');
    $inc = 0;
    $exp = 0;
    if (isset($_rt[$now]['income'])) {
        $inc = $_rt[$now]['income'];
    }
    if (isset($_rt[$now]['expense'])) {
        $exp = $_rt[$now]['expense'];
    }

    $murl = jrCore_get_module_url('jrFoxyCart');
    jrCore_page_banner("monthly summary - <a href=\"{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$murl}/register/y=" . strftime('%Y') . "/m=" . strftime('%B') . '">' . strftime('%B, %Y'));

    $dat             = array();
    $dat[1]['title'] = 'Income';
    $dat[1]['width'] = '33%';
    $dat[2]['title'] = 'Expense';
    $dat[2]['width'] = '34%';
    $dat[3]['title'] = 'Balance';
    $dat[3]['width'] = '33%';
    jrCore_page_table_header($dat);

    $dat             = array();
    $dat[1]['title'] = jrFoxyCart_currency_format($inc);
    $dat[1]['class'] = 'bignum bignum4';
    $dat[2]['title'] = jrFoxyCart_currency_format($exp);
    $dat[2]['class'] = 'bignum bignum1';
    $dat[3]['title'] = jrFoxyCart_currency_format($inc - $exp);
    $dat[3]['class'] = 'bignum bigsystem-inf';
    jrCore_page_table_row($dat);
    jrCore_page_table_footer();

    // Now go through previous months
    if (count($_rt) > 0) {
        $dat             = array();
        $dat[1]['title'] = 'Month';
        $dat[1]['width'] = '23%';
        $dat[2]['title'] = 'Income';
        $dat[2]['width'] = '23%';
        $dat[3]['title'] = 'Expense';
        $dat[3]['width'] = '23%';
        $dat[4]['title'] = 'Balance';
        $dat[4]['width'] = '23%';
        $dat[5]['title'] = 'Report';
        $dat[5]['width'] = '8%';
        jrCore_page_table_header($dat);

        foreach ($_rt as $_rec) {

            $y = substr($_rec['rtime'], 0, 4);
            $m = substr($_rec['rtime'], 4);

            $dat             = array();
            $dat[1]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$murl}/register/y={$y}/m={$m}\" style=\"text-decoration:underline\">{$m}, {$y}</a>";
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = jrFoxyCart_currency_format($_rec['income']);
            $dat[2]['class'] = 'right';
            $dat[3]['title'] = jrFoxyCart_currency_format($_rec['expense']);
            $dat[3]['class'] = 'right';
            $dat[4]['title'] = '<strong>' . jrFoxyCart_currency_format($_rec['income'] - $_rec['expense']) . '</strong>';
            $dat[4]['class'] = 'right';
            $dat[5]['title'] = jrCore_page_button("r{$m}", 'report', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$murl}/ledger_report/y={$y}/m={$m}')");
            jrCore_page_table_row($dat);

        }
        jrCore_page_table_footer();
    }
    jrCore_page_title("Monthly Summary - {$_user['profile_name']}");
    return jrCore_page_display(true);
}

//------------------------------
// Ledger Report
//------------------------------
function profile_view_jrFoxyCart_ledger_report($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFoxyCart');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }
    jrProfile_set_active_profile_tab('register');
    $off = date_offset_get(new DateTime);

    $y = strftime('%Y');
    if (isset($_post['y']) && strlen($_post['y']) === 4) {
        $y = (int) $_post['y'];
    }
    if (isset($_post['m']) && strlen($_post['m']) > 0) {
        $r1 = " AND FROM_UNIXTIME(sale_time + {$off},'%Y%M') = '{$y}" . jrCore_db_escape($_post['m']) . "'";
        $r2 = " AND FROM_UNIXTIME(record_time + {$off},'%Y%M') = '{$y}" . jrCore_db_escape($_post['m']) . "'";
        $r3 = " AND FROM_UNIXTIME(purchase_created + {$off},'%Y%M') = '{$y}" . jrCore_db_escape($_post['m']) . "'";
        $t  = htmlentities($_post['m']) . ", {$y}";
    }
    else {
        $r1 = " AND FROM_UNIXTIME(sale_time + {$off},'%Y') = '{$y}'";
        $r2 = " AND FROM_UNIXTIME(record_time + {$off},'%Y') = '{$y}'";
        $r3 = " AND FROM_UNIXTIME(purchase_created + {$off},'%Y') = '{$y}'";
        $t  = $y;
    }

    // Get our current monthly balance
    $tb1 = jrCore_db_table_name('jrFoxyCart', 'sale');
    $tb2 = jrCore_db_table_name('jrFoxyCart', 'ledger');
    $add = '';
    if (isset($_conf['jrFoxyCart_system_profile_id']) && $_conf['jrFoxyCart_system_profile_id'] == $_profile['_profile_id']) {
        $tb3 = jrCore_db_table_name('jrFoxyCart', 'purchase');
        $add = " UNION ALL (SELECT FROM_UNIXTIME(purchase_created + {$off},'%Y%M') AS rtime, SUM(purchase_price) AS income, '0' AS expense, 'subscriptions' as category
                FROM {$tb3} WHERE purchase_seller_profile_id = '0' AND purchase_module = 'jrFoxyCart'{$r3} GROUP BY purchase_field)";
    }
    $req = "SELECT SUM(a.income) AS income, SUM(a.expense) AS expense, a.category FROM (
           (SELECT FROM_UNIXTIME(sale_time + {$off},'%Y%M') AS rtime, SUM(sale_gross) AS income, SUM(sale_system_fee) AS expense, 'sales' AS category
              FROM {$tb1} WHERE sale_seller_profile_id = '{$_profile['_profile_id']}'{$r1} GROUP BY '0')
         UNION ALL
           (SELECT FROM_UNIXTIME(record_time + {$off},'%Y%M') AS rtime, SUM(record_income) AS income, SUM(record_expense) AS expense, record_category AS category
              FROM {$tb2} WHERE record_profile_id = '{$_profile['_profile_id']}'{$r2} GROUP BY record_category){$add}
            ) a
            GROUP BY a.category ORDER BY income DESC, expense DESC";
    $_rt = jrCore_db_query($req, 'category');
    if (!$_rt || !is_array($_rt)) {
        jrCore_notice_page('error', 'no records found for given time range');
    }
    $inc = 0;
    $exp = 0;
    foreach ($_rt as $v) {
        $inc += $v['income'];
        $exp += $v['expense'];
    }
    jrCore_page_banner("monthly report - {$t}");

    $dat             = array();
    $dat[1]['title'] = 'Income';
    $dat[1]['width'] = '33%';
    $dat[2]['title'] = 'Expense';
    $dat[2]['width'] = '34%';
    $dat[3]['title'] = 'Balance';
    $dat[3]['width'] = '33%';
    jrCore_page_table_header($dat);

    $dat             = array();
    $dat[1]['title'] = jrFoxyCart_currency_format($inc);
    $dat[1]['class'] = 'bignum bignum4';
    $dat[2]['title'] = jrFoxyCart_currency_format($exp);
    $dat[2]['class'] = 'bignum bignum1';
    $dat[3]['title'] = jrFoxyCart_currency_format($inc - $exp);
    $dat[3]['class'] = 'bignum bigsystem-inf';
    jrCore_page_table_row($dat);
    jrCore_page_table_footer();

    // Now go through categories
    $dat             = array();
    $dat[1]['title'] = 'Category';
    $dat[1]['width'] = '33%';
    $dat[2]['title'] = 'Income';
    $dat[2]['width'] = '22%';
    $dat[3]['title'] = 'Expense';
    $dat[3]['width'] = '23%';
    $dat[4]['title'] = 'Balance';
    $dat[4]['width'] = '22%';
    jrCore_page_table_header($dat);

    foreach ($_rt as $_rec) {
        $dat             = array();
        $dat[1]['title'] = '<h3>' . $_rec['category'] . '</h3>';
        $dat[1]['class'] = 'center';
        $dat[2]['title'] = jrFoxyCart_currency_format($_rec['income']);
        $dat[2]['class'] = 'right';
        $dat[3]['title'] = jrFoxyCart_currency_format($_rec['expense']);
        $dat[3]['class'] = 'right';
        $bal             = ($_rec['income'] - $_rec['expense']);
        if ($bal < 0) {
            $dat[4]['title'] = '<strong>' . jrFoxyCart_currency_format($bal) . '</strong>';
            $dat[4]['class'] = 'right error';
        }
        else {
            $dat[4]['title'] = '<strong>' . jrFoxyCart_currency_format($bal) . '</strong>';
            $dat[4]['class'] = 'right success';
        }
        jrCore_page_table_row($dat);

    }
    jrCore_page_table_footer();
    jrCore_page_title("Monthly Report for {$t} - {$_user['profile_name']}");
    return jrCore_page_display(true);
}

//------------------------------
// Payouts
//------------------------------
function profile_view_jrFoxyCart_payouts($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFoxyCart');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }
    jrCore_page_banner('payout');

    $dat             = array();
    $dat[1]['title'] = 'Total Paid Out';
    $dat[1]['width'] = '33%';
    $dat[2]['title'] = 'Cleared';
    $dat[2]['width'] = '34%';
    $dat[3]['title'] = 'Pending';
    $dat[3]['width'] = '33%';
    jrCore_page_table_header($dat);

    $dat             = array();
    $dat[1]['title'] = jrFoxyCart_get_paid_out_amount($_profile['_profile_id']);
    $dat[1]['class'] = 'bignum bigsystem-inf';
    $dat[2]['title'] = jrFoxyCart_get_cleared_balance($_profile['_profile_id']);
    $dat[2]['class'] = 'bignum bignum4';
    $dat[3]['title'] = jrFoxyCart_get_pending_balance($_profile['_profile_id']);
    $dat[3]['class'] = 'bignum bignum1';
    jrCore_page_table_row($dat);
    jrCore_page_table_footer();

    $dat             = array();
    $dat[1]['title'] = 'date';
    $dat[1]['width'] = '33%';
    $dat[2]['title'] = 'email';
    $dat[2]['width'] = '34%';
    $dat[3]['title'] = 'amount';
    $dat[3]['width'] = '33%';
    jrCore_page_table_header($dat);

    $tbl = jrCore_db_table_name('jrFoxyCart', 'payout');
    $req = "SELECT * FROM {$tbl} WHERE payout_profile_id = '{$_profile['_profile_id']}' ORDER BY payout_time DESC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (is_array($_rt)) {
        foreach ($_rt as $_pay) {
            $dat             = array();
            $dat[1]['title'] = jrCore_format_time($_pay['payout_time']);
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $_pay['payout_email'];
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $_pay['payout_amount'];
            $dat[3]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>There are no previous payouts to show for this profile</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    jrCore_page_title("Payout - {$_user['profile_name']}");
    return jrCore_page_display(true);
}

//------------------------------
// Earnings CSV
//------------------------------
function profile_view_jrFoxyCart_earnings_csv($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFoxyCart');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }
    // Get sales
    $tb1 = jrCore_db_table_name('jrFoxyCart', 'sale');
    $tb2 = jrCore_db_table_name('jrFoxyCart', 'purchase');
    $tb3 = jrCore_db_table_name('jrUser', 'item_key');
    $req = "SELECT *
              FROM {$tb1} s
         LEFT JOIN {$tb2} p ON s.sale_purchase_id = p.purchase_id
         LEFT JOIN {$tb3} u ON (u.`_item_id` = s.sale_buyer_user_id AND u.`key` = 'user_name')
             WHERE s.sale_seller_profile_id = '{$_user['user_active_profile_id']}'
             ORDER BY s.sale_time DESC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (!$_rt || !is_array($_rt)) {
        jrCore_notice_page('error', 'no sales have been recorded');
    }

    // We need to bring in Bundle info if any of these sales are bundles
    if (isset($_rt) && is_array($_rt)) {
        $_bi = array();
        foreach ($_rt as $_v) {
            if ($_v['sale_bundle_id'] > 0) {
                $_bi["{$_v['sale_bundle_id']}"] = (int) $_v['sale_bundle_id'];
            }
        }
        if (count($_bi) > 0) {
            $_sc = array(
                'search'                       => array(
                    '_item_id in ' . implode(',', $_bi),
                ),
                'return_keys'                  => array('_item_id', 'bundle_title'),
                'limit'                        => 200,
                'exclude_jrProfile_quota_keys' => true,
                'exclude_jrUser_keys'          => true,
                'ignore_pending'               => true,
                'privacy_check'                => false
            );
            $_tm = jrCore_db_search_items('jrFoxyCartBundle', $_sc);
            if (is_array($_tm['_items'])) {
                $_bi = array();
                foreach ($_tm['_items'] as $_bundle) {
                    $_bi["{$_bundle['_item_id']}"] = $_bundle;
                }
            }
        }
    }
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=\"Sales_Log_" . date('Ymd') . ".csv\"");
    $out = "sale_time,sale_gross,sale_system_fee,sale_total_net,sale_txn_id,purchase_module,purchase_item_id,purchase_item_name,purchase_user_id\n";
    foreach ($_rt as $_e) {
        $_temp  = json_decode($_e['purchase_data'], true);
        $prefix = jrCore_db_get_prefix($_e['purchase_module']);
        $title  = (isset($_temp["{$prefix}_title"])) ? $_temp["{$prefix}_title"] : '?';
        $out .= "{$_e['sale_time']},{$_e['sale_gross']},{$_e['sale_system_fee']},{$_e['sale_total_net']},{$_e['sale_txn_id']},{$_e['purchase_module']},{$_e['purchase_item_id']},{$title},{$_e['purchase_user_id']}\n";
    }
    echo $out;
    exit;
}

//------------------------------
// Earnings
//------------------------------
function profile_view_jrFoxyCart_earnings($_profile, $_post, $_user, $_conf)
{
    global $_mods;
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFoxyCart');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }
    $mrl = jrCore_get_module_url('jrFoxyCart');
    $_ln = jrUser_load_lang_strings();
    $tmp = jrCore_page_button('csv', 'download as CSV', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$mrl}/earnings_csv')");
    jrCore_page_banner(31, $tmp);
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$mrl}/earnings");

    $add = '';
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $str = jrCore_db_escape($_post['search_string']);
        $add = " AND (s.sale_txn_id LIKE '%{$str}%' OR p.purchase_data LIKE '%{$str}%')";
    }

    // Get sales
    $tb1 = jrCore_db_table_name('jrFoxyCart', 'sale');
    $tb2 = jrCore_db_table_name('jrFoxyCart', 'purchase');
    $req = "SELECT *
              FROM {$tb1} s
         LEFT JOIN {$tb2} p ON s.sale_purchase_id = p.purchase_id
             WHERE s.sale_seller_profile_id = '{$_user['user_active_profile_id']}'{$add}
             ORDER BY s.sale_time DESC";

    // find how many lines we are showing
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $_rt = jrCore_db_paged_query($req, $_post['p'], 12, 'NUMERIC');

    // We need to bring in Bundle info if any of these sales are bundles
    $_bi = array();
    if (isset($_rt['_items']) && is_array($_rt['_items'])) {
        $_bi = array();
        $_us = array();
        foreach ($_rt['_items'] as $_v) {
            if ($_v['sale_bundle_id'] > 0) {
                $_bi["{$_v['sale_bundle_id']}"] = (int) $_v['sale_bundle_id'];
            }
            if (isset($_v['sale_buyer_user_id']) && $_v['sale_buyer_user_id'] > 0) {
                $_us[] = (int) $_v['sale_buyer_user_id'];
            }
        }
        if (count($_bi) > 0) {
            $_sc = array(
                'search'                       => array(
                    '_item_id in ' . implode(',', $_bi),
                ),
                'limit'                        => count($_bi),
                'exclude_jrProfile_quota_keys' => true,
                'exclude_jrUser_keys'          => true,
                'ignore_pending'               => true,
                'privacy_check'                => false
            );
            $_tm = jrCore_db_search_items('jrFoxyCartBundle', $_sc);
            if (is_array($_tm['_items'])) {
                $_bi = array();
                foreach ($_tm['_items'] as $_bundle) {
                    $_bi["{$_bundle['_item_id']}"] = $_bundle;
                }
            }
        }
        if (count($_us) > 0) {
            $_sc = array(
                'search'                       => array(
                    '_item_id in ' . implode(',', $_us),
                ),
                'limit'                        => count($_us),
                'include_jrProfile_keys'       => true,
                'exclude_jrProfile_quota_keys' => true,
                'ignore_pending'               => true,
                'privacy_check'                => false
            );
            $_tm = jrCore_db_search_items('jrUser', $_sc);
            if (is_array($_tm['_items'])) {
                $_us = array();
                foreach ($_tm['_items'] as $_u) {
                    $_us["{$_u['_user_id']}"] = $_u;
                }
            }
        }
    }

    $dat              = array();
    $dat[1]['title']  = '';
    $dat[1]['width']  = '2%';
    $dat[2]['title']  = $_ln['jrFoxyCart'][8];
    $dat[2]['width']  = '32%';
    $dat[3]['title']  = 'profile';
    $dat[3]['width']  = '19%';
    $dat[4]['title']  = $_ln['jrFoxyCart'][70];
    $dat[4]['width']  = '12%';
    $dat[5]['title']  = 'gross';
    $dat[5]['width']  = '6%';
    $dat[6]['title']  = 'ship';
    $dat[6]['width']  = '6%';
    $dat[7]['title']  = 'fee';
    $dat[7]['width']  = '6%';
    $dat[8]['title']  = 'net';
    $dat[8]['width']  = '6%';
    $dat[9]['title']  = $_ln['jrFoxyCart'][112];
    $dat[9]['width']  = '5%';
    $dat[10]['title'] = 'receipt';
    $dat[10]['width'] = '5%';
    jrCore_page_table_header($dat);

    if (isset($_rt['_items']) && is_array($_rt['_items'])) {

        foreach ($_rt['_items'] as $k => $_s) {

            $ref = false;
            if (isset($_s['sale_refund_item']) && strlen($_s['sale_refund_item']) > 0) {
                $_pd = json_decode($_s['sale_refund_item'], true);
                unset($_s['sale_refund_item']);
                if (isset($_pd['module']) && (!isset($_s['purchase_module']) || strlen($_s['purchase_module']) === 0)) {
                    $_s['purchase_module'] = $_pd['module'];
                }
                $ref = true;
            }
            elseif (isset($_s['sale_bundle_id']) && $_s['sale_bundle_id'] > 0) {
                // This is a bundle
                $_pd                   = $_bi["{$_s['sale_bundle_id']}"];
                $_s['purchase_module'] = 'jrFoxyCartBundle';
            }
            else {
                $_pd = json_decode($_s['purchase_data'], true);
            }

            if ($_pd && is_array($_pd)) {
                $_s = array_merge($_s, $_pd);
            }
            $dat             = array();
            $dat[1]['title'] = '-';
            $dat[1]['class'] = 'center';

            if (isset($_s['purchase_module']) && strlen($_s['purchase_module']) > 0) {
                $url = jrCore_get_module_url($_s['purchase_module']);
                $pfx = jrCore_db_get_prefix($_s['purchase_module']);
                if ($pfx && isset($_pd["{$pfx}_title"])) {
                    if (isset($_s['sale_bundle_id']) && $_s['sale_bundle_id'] > 0) {
                        $dat[1]['title'] = jrCore_get_module_icon_html('jrFoxyCartBundle', 32);
                    }
                    else {
                        if (isset($_pd["{$pfx}_image_size"])) {
                            $dat[1]['title'] = "<img src=\"{$_conf['jrCore_base_url']}/{$url}/image/{$pfx}_image/{$_pd['_item_id']}/xsmall/crop=auto\" width=\"32\" alt=\"{$_pd["{$pfx}_title"]}\">";
                        }
                        else {
                            $dat[1]['title'] = jrCore_get_module_icon_html($_s['purchase_module'], 32);
                        }
                    }
                    if ($ref && isset($_s['sale_total_net']) && $_s['sale_total_net'] < 0) {
                        $dat[2]['title'] = "<b>REFUND:</b> <a href=\"{$_conf['jrCore_base_url']}/{$_pd['profile_url']}/{$url}/{$_pd['_item_id']}/" . $_pd["{$pfx}_title_url"] . "\">" . $_pd["{$pfx}_title"] . '</a>';
                    }
                    else {
                        if (isset($_pd['profile_url'])) {
                            $dat[2]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_pd['profile_url']}/{$url}/{$_pd['_item_id']}/" . $_pd["{$pfx}_title_url"] . "\">" . $_pd["{$pfx}_title"] . '</a>';
                        }
                        else {
                            $dat[2]['title'] = $_pd["{$pfx}_title"];
                        }
                    }
                }
                else {
                    if (isset($_mods["{$_s['purchase_module']}"])) {
                        $mtitle = $_mods["{$_s['purchase_module']}"]['module_name'];
                        $dat[1]['title'] = jrCore_get_module_icon_html($_s['purchase_module'], 32);
                        $dat[2]['title'] = $mtitle;
                    }
                    else {
                        $dat[1]['title'] = jrCore_get_module_icon_html('jrFoxyCartBundle', 32);
                        $dat[2]['title'] = '-';
                    }
                }
            }
            else {
                if (isset($_s['sale_total_net']) && $_s['sale_total_net'] < 0) {
                    if (isset($_s['sale_bundle_id'])) {
                        $dat[1]['title'] = jrCore_get_module_icon_html('jrFoxyCartBundle', 32);
                        $ttl             = jrCore_db_get_item_key('jrFoxyCartBundle', $_s['sale_bundle_id'], 'bundle_title');
                        $dat[2]['title'] = "<b>REFUND:</b> {$ttl}";
                    }
                    else {
                        $dat[2]['title'] = "<b>REFUND</b>";
                    }
                    if (isset($_s['sale_refunded']) && $_s['sale_refunded'] == '1') {
                        if (isset($_s['sale_refund_item']) && strlen($_s['sale_refund_item']) > 0) {
                            $dat[2]['title'] = "<b>REFUND:</b> {$_s['sale_refund_item']}";
                        }
                        else {
                            $dat[2]['title'] = "<b>REFUND</b>";
                        }
                    }
                }
                else {
                    $dat[2]['title'] = '-';
                }
            }

            $uid = (int) $_s['sale_buyer_user_id'];
            $dat[3]['title'] = (isset($_us[$uid])) ? "<a href=\"{$_conf['jrCore_base_url']}/{$_us[$uid]['profile_url']}\">{$_us[$uid]['user_name']}</a>" : '';
            $dat[4]['title'] = jrCore_format_time($_s['sale_time']);
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrFoxyCart_currency_format($_s['sale_gross']);
            $dat[5]['class'] = 'right';
            $dat[6]['title'] = jrFoxyCart_currency_format($_s['sale_shipping']);
            $dat[6]['class'] = 'right';
            $dat[7]['title'] = jrFoxyCart_currency_format($_s['sale_system_fee']);
            $dat[7]['class'] = 'right';
            if ($_s['sale_payed_out'] == '0') {
                $dat[8]['title'] = '<strong>' . jrFoxyCart_currency_format($_s['sale_total_net']) . '</strong>';
            }
            else {
                $dat[8]['title'] = jrFoxyCart_currency_format($_s['sale_total_net']);
            }
            $dat[8]['class']  = 'right';
            $dat[9]['title']  = jrCore_page_button("v{$k}", $_ln['jrFoxyCart'][112], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$mrl}/txn_details/{$_s['sale_txn_id']}')");
            $dat[10]['title'] = jrCore_page_button("r{$k}", $_ln['jrFoxyCart'][126], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$mrl}/send_receipt/id={$_s['sale_id']}')");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>No Sales have been reported</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_title("Sales Tracker - {$_user['profile_name']}");
    return jrCore_page_display(true);
}

//------------------------------
// Send Receipt
//------------------------------
function profile_view_jrFoxyCart_send_receipt($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFoxyCart');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }

    // Make sure this is a good sale
    $tb1 = jrCore_db_table_name('jrFoxyCart', 'sale');
    $tb2 = jrCore_db_table_name('jrFoxyCart', 'purchase');
    $req = "SELECT s.* FROM {$tb1} s
         LEFT JOIN {$tb2} p ON p.purchase_id = s.sale_purchase_id
             WHERE s.sale_id = '{$_post['id']}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_rt)) {
        jrCore_set_form_notice('error', 'Unable to load sale transaction from database - please try again');
        jrCore_location('referrer');
    }
    // Get user info
    $_us = jrCore_db_get_item('jrUser', $_rt['sale_buyer_user_id'], true);

    jrProfile_set_active_profile_tab('earnings');
    jrCore_page_banner('Send Receipt');
    jrCore_get_form_notice();

    // Form init
    $murl = jrCore_get_module_url('jrFoxyCart');
    $_tmp = array(
        'submit_value' => 'send receipt',
        'action'       => "{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$murl}/send_receipt_save",
        'cancel'       => 'referrer'
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'  => 'id',
        'type'  => 'hidden',
        'value' => intval($_post['id'])
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'receipt_email',
        'label'    => 'email address',
        'help'     => 'Enter the email address you would like to send the receipt to',
        'type'     => 'text',
        'validate' => 'email',
        'value'    => (isset($_us['user_email'])) ? $_us['user_email'] : '',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    return jrCore_page_display(true);
}

//------------------------------
// Send Receipt Save
//------------------------------
function profile_view_jrFoxyCart_send_receipt_save($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFoxyCart');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }
    jrCore_form_validate($_post);

    // Make sure this is a good sale
    $tb1 = jrCore_db_table_name('jrFoxyCart', 'sale');
    $tb2 = jrCore_db_table_name('jrFoxyCart', 'purchase');
    $req = "SELECT s.*, p.* FROM {$tb1} s
         LEFT JOIN {$tb2} p ON p.purchase_id = s.sale_purchase_id
             WHERE s.sale_id = '{$_post['id']}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_rt)) {
        jrCore_set_form_notice('error', 'Unable to load sale transaction from database - please try again');
        jrCore_form_result();
    }

    // Get item purchase
    if (isset($_rt['sale_bundle_id']) && jrCore_checktype($_rt['sale_bundle_id'], 'number_nz')) {
        $_it = jrCore_db_get_item('jrFoxyCartBundle', $_rt['sale_bundle_id'], true);
        $pfx = jrCore_db_get_prefix('jrFoxyCartBundle');
    }
    else {
        $_it = jrCore_db_get_item($_rt['purchase_module'], $_rt['purchase_item_id'], true);
        $pfx = jrCore_db_get_prefix($_rt['purchase_module']);
    }
    if (isset($_it["{$pfx}_title"])) {
        $_rt['item_title'] = $_it["{$pfx}_title"];
    }

    // Send out email
    list($sub, $msg) = jrCore_parse_email_templates('jrFoxyCart', 'receipt', $_rt);
    jrCore_send_email($_post['receipt_email'], $sub, $msg);
    jrCore_form_delete_session();
    jrCore_set_form_notice('success', 'The receipt was successfully sent to the email address');
    jrCore_form_result();
}

//------------------------------
// Expenses
//------------------------------
function profile_view_jrFoxyCart_expenses($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFoxyCart');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }

}

//------------------------------
// Customers
//------------------------------
function profile_view_jrFoxyCart_customers($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFoxyCart');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }
    jrCore_page_banner('Customers');
    $mrl = jrCore_get_module_url('jrFoxyCart');
    jrCore_page_search('search email', "{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$mrl}/customers");

    // See what we are sorting on
    $sf = 'c_total';
    if (isset($_post['order_by'])) {
        switch ($_post['order_by']) {
            case 'c_total';
            case 't_purchases':
            case 't_last':
                $sf = $_post['order_by'];
                break;
        }
    }

    // See how we are sorting
    $od = 'DESC';
    $oo = 'ASC';
    if (isset($_post['order_dir'])) {
        switch (strtoupper($_post['order_dir'])) {
            case 'ASC':
                $od = 'ASC';
                $oo = 'DESC';
                break;
            default:
                $od = 'DESC';
                $oo = 'ASC';
                break;
        }
    }

    // Get sales
    // Check for search
    $add = '';
    if (isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz')) {
        $add = " AND s.sale_buyer_user_id = '{$_post['user_id']}'";
    }
    $tb1 = jrCore_db_table_name('jrFoxyCart', 'sale');
    $req = "SELECT *, SUM(s.sale_gross) AS c_total, COUNT(s.sale_id) AS t_purchases, MAX(s.sale_time) AS t_last
              FROM {$tb1} s
             WHERE s.sale_seller_profile_id = '{$_user['user_active_profile_id']}'{$add}
             GROUP BY s.sale_buyer_user_id
             ORDER BY {$sf} {$od}";

    // If we have a search we need to bring in the user info
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $tb2 = jrCore_db_table_name('jrUser', 'item_key');
        $req = "SELECT s.*, SUM(s.sale_gross) AS c_total, COUNT(s.sale_id) AS t_purchases, MAX(s.sale_time) AS t_last
                  FROM {$tb1} s
             LEFT JOIN {$tb2} u ON (u.`_item_id` = s.sale_buyer_user_id AND u.`key` = 'user_email')
                 WHERE s.sale_seller_profile_id = '{$_user['user_active_profile_id']}'
                   AND u.`value` LIKE '%" . jrCore_db_escape($_post['search_string']) . "%'
                 GROUP BY s.sale_buyer_user_id
                 ORDER BY {$sf} {$od}";
    }

    // find how many lines we are showing
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $_rt = jrCore_db_paged_query($req, $_post['p'], 12, 'NUMERIC');

    $url             = "{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$mrl}/customers";
    $dat             = array();
    $dat[1]['title'] = '';
    $dat[1]['width'] = '2%';
    $dat[2]['title'] = 'customer';
    $dat[2]['width'] = '31%';
    $dat[3]['title'] = 'email';
    $dat[3]['width'] = '31%';
    $dat[4]['title'] = '<a href="' . $url . '/order_by=t_last/order_dir=' . $oo . '"><u>last</u></a>';
    $dat[4]['width'] = '12%';
    $dat[5]['title'] = '<a href="' . $url . '/order_by=t_purchases/order_dir=' . $oo . '"><u>purchases</u></a>';
    $dat[5]['width'] = '12%';
    $dat[6]['title'] = '<a href="' . $url . '/order_by=c_total/order_dir=' . $oo . '"><u>total</u></a>';
    $dat[6]['width'] = '12%';
    jrCore_page_table_header($dat);

    if (isset($_rt['_items']) && is_array($_rt['_items'])) {

        $_ids = array();
        foreach ($_rt['_items'] as $_s) {
            $_ids[] = (int) $_s['sale_buyer_user_id'];
        }
        $_sc = array(
            'search'                       => array(
                "_item_id in " . implode(',', $_ids)
            ),
            'include_jrProfile_keys'       => true,
            'exclude_jrProfile_quota_keys' => true,
            'ignore_pending'               => true,
            'privacy_check'                => false,
            'limit'                        => count($_ids)
        );
        if (isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz')) {
            $_sc['search'] = array("_item_id = {$_post['user_id']}");
        }
        $_us = jrCore_db_search_items('jrUser', $_sc);
        if ($_us && is_array($_us['_items'])) {
            $_ids = array();
            foreach ($_us['_items'] as $_u) {
                $_ids["{$_u['_user_id']}"] = $_u;
            }
        }
        foreach ($_rt['_items'] as $_s) {
            $uid             = (int) $_s['sale_buyer_user_id'];
            $dat[1]['title'] = jrImage_get_image_src('jrUser', 'user_image', $uid, 'xsmall', array('crop' => 'auto', 'width' => 32));
            $dat[2]['title'] = $_ids[$uid]['user_name'] . '<br><a href="' . $_conf['jrCore_base_url'] . '/' . $_ids[$uid]['profile_url'] . '">@' . $_ids[$uid]['profile_url'] . '</a>';
            $dat[3]['title'] = $_ids[$uid]['user_email'];
            $dat[4]['title'] = jrCore_format_time($_s['t_last']);
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = $_s['t_purchases'];
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = '<strong>' . jrFoxyCart_currency_format($_s['c_total']) . '</strong>';
            $dat[6]['class'] = 'right';
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>No Sales have been reported</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_title("Customers - {$_user['profile_name']}");
    return jrCore_page_display(true);
}

//------------------------------
// Products
//------------------------------
function profile_view_jrFoxyCart_products($_profile, $_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFoxyCart');
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        jrUser_not_authorized();
    }
    jrCore_page_banner('Products');

    // See what we are sorting on
    $sf = 't_total';
    if (isset($_post['order_by'])) {
        switch ($_post['order_by']) {
            case 't_total';
            case 't_count':
            case 't_last':
                $sf = $_post['order_by'];
                break;
        }
    }

    // See how we are sorting
    $od = 'DESC';
    $oo = 'ASC';
    if (isset($_post['order_dir'])) {
        switch (strtoupper($_post['order_dir'])) {
            case 'ASC':
                $od = 'ASC';
                $oo = 'DESC';
                break;
            default:
                $od = 'DESC';
                $oo = 'ASC';
                break;
        }
    }

    // Get sales
    $tb1 = jrCore_db_table_name('jrFoxyCart', 'sale');
    $tb2 = jrCore_db_table_name('jrFoxyCart', 'purchase');
    $req = "SELECT *, CONCAT_WS('|', p.purchase_module, p.purchase_field, p.purchase_item_id, s.sale_bundle_id) AS i_unique,
                   MAX(s.sale_time) AS t_last, COUNT(s.sale_id) AS t_count, SUM(s.sale_gross) AS t_total
              FROM {$tb1} s
         LEFT JOIN {$tb2} p ON s.sale_purchase_id = p.purchase_id
             WHERE s.sale_seller_profile_id = '{$_user['user_active_profile_id']}'
             GROUP BY i_unique
             ORDER BY {$sf} {$od}";
    // find how many lines we are showing
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $_rt = jrCore_db_paged_query($req, $_post['p'], 12, 'NUMERIC');

    // We need to bring in Bundle info if any of these sales are bundles
    $_bi = array();
    if (isset($_rt['_items']) && is_array($_rt['_items'])) {
        $_bi = array();
        foreach ($_rt['_items'] as $_v) {
            if ($_v['sale_bundle_id'] > 0) {
                $_bi["{$_v['sale_bundle_id']}"] = (int) $_v['sale_bundle_id'];
            }
        }
        if (count($_bi) > 0) {
            $_sc = array(
                'search'                       => array(
                    '_item_id in ' . implode(',', $_bi),
                ),
                'limit'                        => 12,
                'exclude_jrProfile_quota_keys' => true,
                'exclude_jrUser_keys'          => true,
                'ignore_pending'               => true,
                'privacy_check'                => false
            );
            $_tm = jrCore_db_search_items('jrFoxyCartBundle', $_sc);
            if (is_array($_tm['_items'])) {
                $_bi = array();
                foreach ($_tm['_items'] as $_bundle) {
                    $_bi["{$_bundle['_item_id']}"] = $_bundle;
                }
            }
        }
    }

    $mrl             = jrCore_get_module_url('jrFoxyCart');
    $url             = "{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$mrl}/products";
    $dat             = array();
    $dat[1]['title'] = '';
    $dat[1]['width'] = '2%';
    $dat[2]['title'] = 'product';
    $dat[2]['width'] = '58%';
    $dat[3]['title'] = '<a href="' . $url . '/order_by=t_last/order_dir=' . $oo . '"><u>last_sale</u></a>';
    $dat[3]['width'] = '20%';
    $dat[4]['title'] = '<a href="' . $url . '/order_by=t_count/order_dir=' . $oo . '"><u>count</u></a>';
    $dat[4]['width'] = '10%';
    $dat[5]['title'] = '<a href="' . $url . '/order_by=t_total/order_dir=' . $oo . '"><u>total</u></a>';
    $dat[5]['width'] = '10%';
    jrCore_page_table_header($dat);

    if (isset($_rt['_items']) && is_array($_rt['_items'])) {

        $ffx = jrCore_db_get_prefix('jrFoxyCartBundle');
        foreach ($_rt['_items'] as $_s) {

            // Bundle vs. Item
            if (isset($_s['purchase_item_id'])) {
                $pfx = jrCore_db_get_prefix($_s['purchase_module']);
                $url = jrCore_get_module_url($_s['purchase_module']);
                $_dt = json_decode($_s['purchase_data'], true);
                $iid = (int) $_s['purchase_item_id'];
                if (!strpos($_s['purchase_field'], '_image') && !strpos($_s['purchase_field'], '_file')) {
                    $dat[1]['title'] = jrImage_get_image_src($_s['purchase_module'], "{$_s['purchase_field']}_image", $iid, 'xsmall', array('crop' => 'auto', 'width' => 32));
                }
                else {
                    $dat[1]['title'] = jrImage_get_image_src($_s['purchase_module'], str_replace('_file', '_image', $_s['purchase_field']), $iid, 'xsmall', array('crop' => 'auto', 'width' => 32));
                }
                $dat[2]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_dt['profile_url']}/{$url}/{$iid}/" . $_dt["{$pfx}_title_url"] . "\">" . $_dt["{$pfx}_title"] . '</a>';
            }
            else {
                $_dt             = $_bi["{$_s['sale_bundle_id']}"];
                $dat[1]['title'] = jrCore_get_module_icon_html('jrFoxyCartBundle', 32);
                $dat[2]['title'] = $_dt["{$ffx}_title"];
            }
            $dat[3]['title'] = jrCore_format_time($_s['t_last']);
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = $_s['t_count'];
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = '<strong>' . jrFoxyCart_currency_format($_s['t_total']) . '</strong>';
            $dat[5]['class'] = 'right';
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>No Sales have been reported</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_title("Products - {$_user['profile_name']}");
    return jrCore_page_display(true);
}

//------------------------------
// My insights
//------------------------------
function profile_view_jrFoxyCart_insights($_profile, $_post, $_user, $_conf)
{
    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        return false;
    }
    $_profile['buyer_user_id'] = (isset($_post['_2']) && jrCore_checktype($_post['_2'], 'number_nz')) ? intval($_post['_2']) : false;
    jrCore_page_title("Sales Insights - {$_user['profile_name']}");
    return jrCore_parse_template("my_insights.tpl", $_profile, 'jrFoxyCart');
}

//------------------------------
// My product performance
//------------------------------
function profile_view_jrFoxyCart_performance($_profile, $_post, $_user, $_conf)
{

    if (!jrProfile_is_profile_owner($_profile['_profile_id'])) {
        return false;
    }

    $_replace = array();
    //product code
    if (jrCore_checktype($_post['_2'], 'string')) {
        $_replace['product_code'] = $_post['_2'];
    }
    //profile
    foreach ($_profile as $k => $v) {
        $_replace[$k] = $v;
    }
    jrCore_page_title("Product Performance - {$_user['profile_name']}");
    return jrCore_parse_template("my_performance.tpl", $_replace, 'jrFoxyCart');

}
