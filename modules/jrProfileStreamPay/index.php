<?php
/**
 * Jamroom Stream Pay module
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
 * @copyright 2015 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//------------------------------
// credit_log
//------------------------------
function view_jrProfileStreamPay_credit_log($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrProfileStreamPay', 'credit_log');

    // construct our query
    $tbl = jrCore_db_table_name('jrProfileStreamPay', 'log');
    $req = "SELECT * FROM {$tbl} ";
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $str  = jrCore_db_escape($_post['search_string']);
        $req .= "WHERE (log_item_name LIKE '%{$str}%' OR log_user_ip LIKE '%{$str}%' OR log_module LIKE '%{$str}%') ";
        $num = null;
        $_ex = array('search_string' => $_post['search_string']);
        $add = '/search_string=' . urlencode($_post['search_string']);
    }
    else {
        $num = jrCore_db_number_rows('jrProfileStreamPay', 'log');
        $_ex = false;
        $add = '';
    }
    $req .= 'ORDER BY log_id DESC';

    // find how many lines we are showing
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $_rt = jrCore_db_paged_query($req, $_post['p'], 12, 'NUMERIC', $num);

    // start our html output
    jrCore_page_banner('credit log');
    jrCore_get_form_notice();
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/credit_log{$add}");

    $dat = array();
    $dat[1]['title'] = 'ID';
    $dat[1]['width'] = '5%;';
    $dat[2]['title'] = 'date';
    $dat[2]['width'] = '15%;';
    $dat[3]['title'] = 'User';
    $dat[3]['width'] = '10%;';
    $dat[4]['title'] = 'IP';
    $dat[4]['width'] = '10%;';
    $dat[5]['title'] = 'streamed media';
    $dat[5]['width'] = '50%;';
    $dat[6]['title'] = 'amount';
    $dat[6]['width'] = '10%;';
    jrCore_page_table_header($dat);
    unset($dat);

    if (isset($_rt['_items']) && is_array($_rt['_items'])) {

        // Round up user info
        $_id = array();
        $_us = array();
        foreach ($_rt['_items'] as $k => $_log) {
            if ($_log['log_user_id'] > 0) {
                $_id[] = (int) $_log['log_user_id'];
            }
            if (count($_id) > 0) {
                $_tm = jrCore_db_get_multiple_items('jrUser', $_id);
                if ($_tm && is_array($_tm)) {
                    foreach ($_tm as $v) {
                        $_us["{$v['_user_id']}"] = $v;
                    }
                }
                unset($_tm);
            }
        }

        // LOG LINE
        foreach ($_rt['_items'] as $k => $_log) {

            $dat = array();
            $dat[1]['title'] = $_log['log_id'];
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = jrCore_format_time($_log['log_created']);
            $dat[2]['class'] = 'center nowrap';
            if ($_log['log_user_id'] > 0) {
                if (isset($_us["{$_log['log_user_id']}"])) {
                    $dat[3]['title'] = $_us["{$_log['log_user_id']}"]['user_name'];
                }
                else {
                    $dat[3]['title'] = $_log['log_user_id'] . ' (user not found)';
                }
            }
            else {
                $dat[3]['title'] = 'visitor';
            }
            $dat[3]['class'] = 'center nowrap';
            $dat[4]['title'] = $_log['log_user_ip'];
            $dat[4]['class'] = 'center nowrap';
            if (isset($_post['search_string']{0})) {
                $dat[5]['title'] = jrCore_hilight_string($_log['log_item_name'], $_post['search_string']);
            }
            else {
                $dat[5]['title'] = $_log['log_item_name'];
            }
            if (strpos($_log['log_amount'], '.')) {
                $pos = strpos($_log['log_amount'], '.') + 1;
                $dat[6]['title'] = jrCore_number_format($_log['log_amount'], strlen(substr($_log['log_amount'], $pos)));
            }
            else {
                $dat[6]['title'] = $_log['log_amount'];
            }
            $dat[6]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt, $_ex);
    }
    else {
        $dat = array();
        if (!empty($_post['search_string'])) {
            $dat[1]['title'] = '<p>There were no Credit Logs found to match your search criteria</p>';
        }
        else {
            $dat[1]['title'] = '<p>There does not appear to be any Credit Log entries</p>';
        }
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    jrCore_page_display();
}
