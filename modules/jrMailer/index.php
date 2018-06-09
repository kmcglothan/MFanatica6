<?php
/**
 * Jamroom Email Support module
 *
 * copyright 2018 The Jamroom Network
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0.  Please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
 *
 * Jamroom may use modules and skins that are licensed by third party
 * developers, and licensed under a different license  - please
 * reference the individual module or skin license that is included
 * with your installation.
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
// Image (beacon)
//------------------------------
function view_jrMailer_i($_post, $_user, $_conf)
{
    ignore_user_abort(true);
    // _1 = campaign ID
    // _2 = user ID
    if (isset($_post['_1']) && jrCore_checktype($_post['_1'], 'number_nz') && isset($_post['_2']) && jrCore_checktype($_post['_2'], 'number_nz')) {

        // Watch for proxy loading of images
        if (jrMailer_is_real_user_agent()) {
            $ins = true;
            $uid = (int) $_post['_2'];
            if (jrUser_is_logged_in() && $_user['_user_id'] != $uid) {
                // Spoof attempt - do not record
                $ins = false;
            }
            if ($ins) {
                $cid = (int) $_post['_1'];  // campaign ID
                $uip = jrCore_get_ip();     // IP Address
                $agn = isset($_SERVER['HTTP_USER_AGENT']) ? jrCore_db_escape($_SERVER['HTTP_USER_AGENT']) : '';
                $tbl = jrCore_db_table_name('jrMailer', 'track');
                $req = "INSERT INTO {$tbl} (t_cid, t_uid, t_time, t_ip, t_agent)
                        VALUES ({$cid}, {$uid}, UNIX_TIMESTAMP(), '{$uip}', '{$agn}')
                        ON DUPLICATE KEY UPDATE t_lat = '', t_ip = '{$uip}', t_agent = VALUES(t_agent)";
                jrCore_db_query($req);
            }
        }
    }
    // Send out image
    header("Content-type: image/gif");
    header('Content-Disposition: inline; filename="link.gif"');
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 8640000));
    echo file_get_contents(APP_DIR . '/modules/jrMailer/img/1.gif');
    exit();
}

//------------------------------
// Link
//------------------------------
function view_jrMailer_link($_post, $_user, $_conf)
{
    // http://local.jamroom.net/mailer/link/1/2
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_page_not_found();
    }
    if (!isset($_post['_2']) || !jrCore_checktype($_post['_2'], 'number_nz')) {
        jrCore_page_not_found();
    }
    $cid = (int) $_post['_1'];  // ID of URL clicked
    $uid = (int) $_post['_2'];  // User ID
    if ($_ur = jrMailer_get_campaign_url_by_id($cid)) {
        jrMailer_record_campaign_click($uid, $_ur['url_cid'], $cid);
        jrMailer_track_campaign_view($uid, $_ur['url_cid']);
        jrCore_location($_ur['url_uri']);
    }
    jrCore_location($_conf['jrCore_base_url']);
}

//------------------------------
// Browse
//------------------------------
function view_jrMailer_browse($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_session_require_login();
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrMailer', 'browse');
    jrCore_page_banner('Stats Browser');

    $p = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $p = (int) $_post['p'];
    }
    $tbl = jrCore_db_table_name('jrMailer', 'campaign');
    $req = "SELECT * FROM {$tbl} ORDER BY c_created DESC";
    $_rt = jrCore_db_paged_query($req, $p, 12, 'NUMERIC');

    $dat             = array();
    $dat[1]['title'] = 'subject';
    $dat[1]['width'] = '60%';
    $dat[2]['title'] = 'sent';
    $dat[2]['width'] = '20%';
    $dat[3]['title'] = 'recipients';
    $dat[3]['width'] = '10%';
    $dat[4]['title'] = 'stats';
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = 'delete';
    $dat[5]['width'] = '5%';
    jrCore_page_table_header($dat);

    if ($_rt && is_array($_rt) && is_array($_rt['_items'])) {

        foreach ($_rt['_items'] as $k => $_c) {
            $dat             = array();
            $dat[1]['title'] = $_mods["{$_c['c_module']}"]['module_name'] . ' - ' . $_c['c_title'];
            $dat[2]['title'] = jrCore_format_time($_c['c_created']);
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = jrCore_number_format($_c['c_sent']);
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = jrCore_page_button("r{$k}", 'view stats', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/campaign_result/{$_c['c_id']}')");
            $dat[5]['title'] = jrCore_page_button("d{$k}", 'delete', "if(confirm('Delete these stats?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/campaign_delete_save/{$_c['c_id']}') }");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>There are no mailer stats to show</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    jrCore_page_display();
}

//------------------------------
// campaign_delete_save
//------------------------------
function view_jrMailer_campaign_delete_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('error', 'invalid campaign id');
    }
    $cid = (int) $_post['_1'];
    if (jrMailer_delete_campaign($cid)) {
        jrCore_set_form_notice('success', 'The Campaign has been deleted');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered deleting the campaign - please try again');
    }
    jrCore_location('referrer');
}

//------------------------------
// campaign_result
//------------------------------
function view_jrMailer_campaign_result($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_session_require_login();
    jrUser_master_only();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('error', 'invalid campaign id');
    }
    $cid = (int) $_post['_1'];

    $url = jrCore_get_local_referrer();
    if (!strpos($url, '/campaign_result/')) {
        jrCore_create_memory_url('campaign_cancel', $url);
    }

    $_cp = jrMailer_get_campaign_results(array($cid));
    if (!$_cp || !is_array($_cp) || !isset($_cp[$cid]) || !is_array($_cp[$cid])) {
        jrCore_notice_page('error', 'invalid campaign id (2)');
    }
    $_cp = $_cp[$cid];

    $_inc = array('source' => "https://www.gstatic.com/charts/loader.js");
    jrCore_create_page_element('javascript_href', $_inc);

    $_inc = array('source' => "https://www.google.com/jsapi");
    jrCore_create_page_element('javascript_href', $_inc);

    $_inc = array('jrMailer_cp_init()');
    jrCore_create_page_element('javascript_ready_function', $_inc);

    if (isset($_cp['countries'])) {
        arsort($_cp['countries']);
    }
    $_cp['results']['c_percent'] = 0;
    if (isset($_cp['results']['total']) && $_cp['results']['total'] > 0) {
        $_cp['results']['c_percent'] = floor(($_cp['results']['total'] / $_cp['campaign']['c_sent']) * 100);
        if ($_cp['results']['c_percent'] > 100) {
            $_cp['results']['c_percent'] = 100;
        }
    }
    $mod = $_cp['campaign']['c_module'];
    jrCore_page_title("{$_mods[$mod]['module_name']}: {$_cp['campaign']['c_title']} - Campaign");

    // Create all campaigns Jumper
    $_camps = jrMailer_get_all_campaigns();
    $cm_url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/campaign_result/";
    $button = '<select name="campaign_id" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $cm_url . "'+ $(this).val())\">\n";
    foreach ($_camps as $cpid => $_inf) {
        if ($cpid == $_post['_1']) {
            $button .= '<option value="' . $cpid . '" selected="selected"> ' . $_inf['c_title'] . ' (' . jrCore_format_time($_inf['c_created']) . ")</option>\n";
        }
        else {
            $button .= '<option value="' . $cpid . '"> ' . $_inf['c_title'] . ' (' . jrCore_format_time($_inf['c_created']) . ")</option>\n";
        }
    }
    $button .= '</select>';

    $button .= jrCore_page_button('refresh', 'refresh', 'location.reload()');

    if ($mod == 'jrNewsLetter') {
        $url = jrCore_get_module_url('jrNewsLetter');
        jrCore_page_banner("<a href=\"{$_conf['jrCore_base_url']}/{$url}/browse\"><u>{$_mods[$mod]['module_name']}</u></a> - {$_cp['campaign']['c_title']}", $button);
    }
    else {
        jrCore_page_banner("{$_mods[$mod]['module_name']}: {$_cp['campaign']['c_title']}", $button);
    }
    jrCore_get_form_notice();

    $dat             = array();
    $dat[1]['title'] = 'total sent';
    $dat[1]['width'] = '16.6%';
    $dat[2]['title'] = 'unique opens';
    $dat[2]['width'] = '16.6%';
    $dat[3]['title'] = 'open percent';
    $dat[3]['width'] = '16.6%';
    $dat[4]['title'] = 'clicked URLs';
    $dat[4]['width'] = '16.6%';
    $dat[5]['title'] = 'unsubscribes';
    $dat[5]['width'] = '16.6%';
    $dat[6]['title'] = 'bounces';
    $dat[6]['width'] = '16.6%';

    // Let the module insert or change our header
    $dat = jrCore_trigger_event('jrMailer', 'campaign_result_header', $dat, $_cp, $mod);
    jrCore_page_table_header($dat);

    $dat             = array();
    $dat[1]['title'] = (isset($_cp['campaign']['c_sent'])) ? jrCore_number_format($_cp['campaign']['c_sent']) : 0;
    $dat[1]['class'] = 'bignum bignum4 nocursor';
    $dat[2]['title'] = (isset($_cp['results']['total'])) ? jrCore_number_format($_cp['results']['total']) : 0;
    $dat[2]['class'] = 'bignum bignum3 nocursor';
    $dat[3]['title'] = (isset($_cp['results']['c_percent'])) ? $_cp['results']['c_percent'] . '%' : '0%';
    if ($_cp['results']['c_percent'] > 25) {
        $dat[3]['class'] = 'bignum bignum3 nocursor';
    }
    elseif ($_cp['results']['c_percent'] > 10) {
        $dat[3]['class'] = 'bignum bignum2 nocursor';
    }
    else {
        $dat[3]['class'] = 'bignum bignum1 nocursor';
    }
    $dat[4]['title'] = (isset($_cp['results']['clicks'])) ? jrCore_number_format($_cp['results']['clicks']) : 0;
    $dat[4]['class'] = 'bignum bignum3 nocursor';
    $dat[5]['title'] = (isset($_cp['campaign']['c_unsub'])) ? jrCore_number_format($_cp['campaign']['c_unsub']) : 0;
    if ($_cp['campaign']['c_unsub'] > 0) {
        $dat[5]['class'] = 'bignum bignum1 nocursor';
    }
    else {
        $dat[5]['class'] = 'bignum bignum3 nocursor';
    }
    if (jrCore_module_is_active('jrMailGun')) {
        $dat[6]['title'] = (isset($_cp['campaign']['c_bounce'])) ? "<a href=\"{$_conf['jrCore_base_url']}/" . jrCore_get_module_url('jrMailGun') . '/browse_bounces"><u>' . jrCore_number_format($_cp['campaign']['c_bounce']) . '</u></a>' : 0;
    }
    else {
        $dat[6]['title'] = (isset($_cp['campaign']['c_bounce'])) ? jrCore_number_format($_cp['campaign']['c_bounce']) : 0;
    }
    if ($_cp['campaign']['c_bounce'] > 0) {
        $dat[6]['class'] = 'bignum bignum1 nocursor';
    }
    else {
        $dat[6]['class'] = 'bignum bignum3 nocursor';
    }

    // Let the module insert or change our row
    $dat = jrCore_trigger_event('jrMailer', 'campaign_result_row', $dat, $_cp, $mod);
    jrCore_page_table_row($dat);

    jrCore_page_table_footer();

    $_cp['module'] = jrCore_module_meta_data($_cp['campaign']['c_module']);

    if (!isset($_post['_2'])) {
        $_post['_2'] = 'view';
    }
    jrMailer_campaign_tabs($_post['_2']);

    switch ($_post['_2']) {
        case 'map':
            if (isset($_cp['countries']) && is_array($_cp['countries'])) {
                $_new = array();
                foreach ($_cp['countries'] as $country => $count) {
                    $new_country = jrMailer_get_country_name_for_map($country);
                    $_new[$new_country] = $count;
                }
                $_cp['countries'] = $_new;
                unset($_new);
            }
            $html = jrCore_parse_template('campaign_user_map.tpl', $_cp, 'jrMailer');
            break;
        case 'unsub':
            $_cp['unsub'] = jrMailer_get_unsubscribed_users($cid);
            if (!is_array($_cp['unsub']) || count($_cp['unsub']) === 0) {
                unset($_cp['unsub']);
            }
            $html = jrCore_parse_template('campaign_user_unsubscribe.tpl', $_cp, 'jrMailer');
            break;
        case 'urls':
            $html = jrCore_parse_template('campaign_urls.tpl', $_cp, 'jrMailer');
            break;
        default:
            $html = jrCore_parse_template('campaign_views.tpl', $_cp, 'jrMailer');
            break;
    }
    jrCore_page_custom($html);
    $url = jrCore_get_memory_url('campaign_cancel');
    if ($url) {
        jrCore_page_cancel_button($url);
    }
    jrCore_page_display();
}

//------------------------------
// user_report
//------------------------------
function view_jrMailer_user_report($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_master_only();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        echo "ERROR: invalid user id";
        exit;
    }
    // Get info about this user
    $_usr = jrMailer_get_user_report($_post['_1'], $_post['_2']);
    $html = jrCore_parse_template('user_report.tpl', $_usr, 'jrMailer');
    echo $html;
    exit;
}

//------------------------------
// test email
//------------------------------
function view_jrMailer_test_email($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrMailer', 'test_email');
    jrCore_page_banner('Send a Test Email');

    // Form init
    $_tmp = array(
        'submit_value'     => 'send test email',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // Email Address
    $_tmp = array(
        'name'     => 'email',
        'label'    => 'email address',
        'help'     => 'Enter a valid email address you would like to send a test message to',
        'type'     => 'text',
        'validate' => 'email',
        'default'  => $_user['user_email'],
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Email Subject
    $_tmp = array(
        'name'     => 'subject',
        'label'    => 'email subject',
        'help'     => 'Enter a subject for the test email',
        'type'     => 'text',
        'validate' => 'not_empty',
        'default'  => 'this is a test email subject',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Email Message
    $_tmp = array(
        'name'     => 'message',
        'label'    => 'email message',
        'help'     => 'Enter a message for the test email',
        'type'     => 'textarea',
        'validate' => 'not_empty',
        'default'  => 'this is the test email message',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// test email_save
//------------------------------
function view_jrMailer_test_email_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_master_only();
    jrCore_form_validate($_post);
    if (jrCore_send_email($_post['email'], $_post['subject'], $_post['message'])) {
        sleep(1);
        jrCore_set_form_notice('success', 'The test email was successfully sent');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered sending the test email - check activity log');
    }
    jrCore_form_result();
}
