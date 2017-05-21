<?php
/**
 * Jamroom OneAll Social module
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
// login
//------------------------------
function view_jrOneAll_login($_post, $_user, $_conf)
{
    $_ln = jrUser_load_lang_strings();
    // Check for maintenance mode
    if (isset($_conf['jrCore_maintenance_mode']) && $_conf['jrCore_maintenance_mode'] == 'on') {
        jrCore_set_form_notice('notice', $_ln['jrCore'][35]);
    }

    // our page banner
    if (!isset($_post['_1'])) {
        $html = '';
        if (isset($_conf['jrUser_signup_on']) && $_conf['jrUser_signup_on'] == 'on') {
            $add = '';
            if (isset($_post['r']) && strlen($_post['r']) > 0) {
                $add = '/r=1';
                if ($_post['r'] != 1) {
                    if ($url = jrCore_get_memory_url($_post['r'])) {
                        $add = "/r={$_post['r']}";
                    }
                }
            }
            $html = jrCore_page_button('signup', $_ln['jrUser'][31], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/signup{$add}')");
        }
        $murl = jrCore_get_module_url('jrUser');
        $html .= jrCore_page_button('forgot', $_ln['jrUser'][41], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$murl}/forgot')");
        jrCore_page_banner($_ln['jrUser'][40], $html, false);
    }
    if (isset($_post['r']) && $_post['r'] == '1' && !isset($_SESSION['jrcore_form_notices'])) {
        jrCore_set_form_notice('error', $_ln['jrUser'][108]);
    }
    if (isset($_conf['jrOneAll_social_message']) && strlen($_conf['jrOneAll_social_message']) > 1) {
        jrCore_set_form_notice('notice', $_conf['jrOneAll_social_message'], false);
    }
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value'     => false,
        'cancel'           => false,
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // Required - do not remove
    $_tmp = array(
        'name'  => 'oneall_login',
        'type'  => 'hidden',
        'value' => 1
    );
    jrCore_form_field_create($_tmp);

    jrCore_get_form_notice();
    jrCore_page_display();
}

//------------------------------
// signup
//------------------------------
function view_jrOneAll_signup($_post, $_user, $_conf)
{
    // Make sure sign ups are turned on...
    $_ln = jrUser_load_lang_strings();
    if (!isset($_conf['jrUser_signup_on']) || $_conf['jrUser_signup_on'] != 'on') {
        jrCore_notice_page('error', $_ln['jrUser'][58]);
    }

    // Check for available signup quotas
    $_opt = jrProfile_get_signup_quotas();
    if (!$_opt || !is_array($_opt) || count($_opt) === 0) {
        if (jrUser_is_admin()) {
            jrCore_notice_page('error', 'Admin: There are currently NO QUOTAS that allow signups - please check the User Account Quota Config for quotas and allow signups!');
        }
        else {
            jrCore_notice_page('error', $_ln['jrUser'][58]);
        }
    }
    // our page banner
    if (!isset($_post['_1'])) {
        $tmp = jrCore_page_button('login', $_ln['jrUser'][3], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/login')");
        jrCore_page_banner($_ln['jrUser'][31], $tmp, false);
    }
    if (isset($_conf['jrOneAll_social_message']) && strlen($_conf['jrOneAll_social_message']) > 1) {
        jrCore_set_form_notice('notice', $_conf['jrOneAll_social_message'], false);
    }
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value'     => false,
        'cancel'           => false,
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // Required - do not remove
    $_tmp = array(
        'name'  => 'oneall_signup',
        'type'  => 'hidden',
        'value' => 1
    );
    jrCore_form_field_create($_tmp);

    if (isset($_post['r']) && strlen($_post['r']) > 0) {
        if ($url = jrCore_get_memory_url($_post['r'])) {
            // Referrer
            $_tmp = array(
                'name'  => 'r',
                'type'  => 'hidden',
                'value' => $_post['r']
            );
            jrCore_form_field_create($_tmp);
        }
    }

    jrCore_get_form_notice();
    jrCore_page_display();
}

//------------------------------
// connections
//------------------------------
function view_jrOneAll_connections($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_master_only();

    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }

    $tbl = jrCore_db_table_name('jrOneAll', 'link');
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $req = "SELECT * FROM {$tbl} WHERE `data` LIKE '%" . jrCore_db_escape($_post['search_string']) . "%' GROUP BY user_id ORDER BY updated DESC";
    }
    else {
        $req = "SELECT * FROM {$tbl} GROUP BY user_id ORDER BY updated DESC";
    }
    $_rt = jrCore_db_paged_query($req, $_post['p'], 18, 'user_id');

    $_nm = array();
    if ($_rt && isset($_rt['_items'])) {
        $_ky = array('_user_id', 'user_name', 'user_email', 'user_group', 'user_image_time');
        $_tm = jrCore_db_get_multiple_items('jrUser', array_keys($_rt['_items']), $_ky);
        if (isset($_tm) && is_array($_tm)) {
            foreach ($_tm as $_inf) {
                $_nm["{$_inf['_user_id']}"] = $_inf;
            }
        }
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrOneAll');
    jrCore_page_banner('connection browser');
    jrCore_get_form_notice();
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/connections");

    $dat             = array();
    $dat[1]['title'] = 'IMG';
    $dat[1]['width'] = '2%;';
    $dat[2]['title'] = 'user name';
    $dat[2]['width'] = '40%;';
    $dat[3]['title'] = 'IMG';
    $dat[3]['width'] = '2%;';
    $dat[4]['title'] = 'linked network';
    $dat[4]['width'] = '36%;';
    $dat[5]['title'] = 'date connected';
    $dat[5]['width'] = '15%;';
    $dat[6]['title'] = 'modify';
    $dat[6]['width'] = '5%;';
    $dat[7]['title'] = 'delete';
    $dat[7]['width'] = '5%;';
    jrCore_page_table_header($dat);

    if (count($_nm) > 0) {
        $i = 0;
        $u = jrCore_get_module_url('jrImage');
        foreach ($_nm as $user_id => $_inf) {
            $dat             = array();
            $_im             = array(
                'crop'   => 'auto',
                'width'  => 32,
                'height' => 32,
                'alt'    => 'img',
                'title'  => 'img',
                '_v'     => (isset($_inf['user_image_time']) && $_inf['user_image_time'] > 0) ? $_inf['user_image_time'] : false
            );
            $dat[1]['title'] = jrImage_get_image_src('jrUser', 'user_image', $user_id, 'xsmall', $_im);
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $_inf['user_name'] . '<br><small>' . $_inf['user_email'] . '</small>';
            $dat[3]['title'] = '<img src="' . $_conf['jrCore_base_url'] . '/' . $u . '/img/module/jrOneAll/' . $_rt['_items'][$user_id]['provider'] . '.png" width="32" height="32" alt="' . $_rt['_items'][$user_id]['provider'] . '">';
            $dat[4]['title'] = ucwords($_rt['_items'][$user_id]['provider']);
            if (isset($_rt['_items'][$user_id]['data'])) {
                $_us = json_decode($_rt['_items'][$user_id]['data'], true);
                if ($_us && is_array($_us) && isset($_us['response']['result']['data']['identity'])) {
                    $_tmp = $_us['response']['result']['data']['identity'];
                    $name = false;
                    if (isset($_tmp['displayName'])) {
                        $name = $_tmp['displayName'];
                    }
                    elseif (isset($_tmp['preferredUsername'])) {
                        $name = $_tmp['preferredUsername'];
                    }
                    if ($name) {
                        $purl = false;
                        if (isset($_tmp['profileUrl']) && strpos(' ' . $_tmp['profileUrl'], 'http')) {
                            $purl = $_tmp['profileUrl'];
                        }
                        elseif (isset($_tmp['id']) && strpos(' ' . $_tmp['id'], 'http')) {
                            $purl = $_tmp['id'];
                        }
                        if ($purl) {
                            $dat[4]['title'] .= '<br><small><a href="' . $purl . '" target="_blank">' . $name . '</a></small>';
                        }
                        else {
                            $dat[4]['title'] .= '<br><small>' . $name . '</small>';
                        }
                    }
                }
            }

            $dat[5]['title'] = jrCore_format_time($_rt['_items'][$user_id]['updated']);
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = jrCore_page_button("oneall-modify-{$i}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/connection_modify/user_id={$user_id}')");
            $dat[7]['title'] = jrCore_page_button("oneall-delete-{$i}", 'delete', "if (confirm('Are you sure you want to delete all social connections for this user?')){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/connection_delete_save/user_id={$user_id}') }");
            if (isset($_rt['_items'][$user_id]['error']) && strlen($_rt['_items'][$user_id]['error']) > 0) {
                $dat[6]['class'] = 'error';
                $dat[7]['class'] = 'error';
            }
            jrCore_page_table_row($dat);
            $i++;
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>There are no users that have created social connections</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_pager($_rt);
    jrCore_page_table_footer();
    jrCore_page_display();
}

//------------------------------
// connection_delete_save
//------------------------------
function view_jrOneAll_connection_delete_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_master_only();
    jrCore_validate_location_url();
    // Make sure we get a good user_id
    if (!isset($_post['user_id']) || !jrCore_checktype($_post['user_id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'You have provided an invalid user_id - please try again');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrOneAll', 'link');
    $req = "DELETE FROM {$tbl} WHERE user_id = '{$_post['user_id']}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    jrCore_set_form_notice('success', "successfully deleted {$cnt} social connections");
    jrCore_location('referrer');
}

//------------------------------
// connection_modify
//------------------------------
function view_jrOneAll_connection_modify($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrOneAll');

    // Make sure we get a good user_id
    if (!isset($_post['user_id']) || !jrCore_checktype($_post['user_id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'You have provided an invalid user_id - please try again');
        jrCore_location('referrer');
    }

    // Get this user's OneAll ID
    $tbl = jrCore_db_table_name('jrOneAll', 'link');
    $req = "SELECT * FROM {$tbl} WHERE user_id = '{$_post['user_id']}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'provider');
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'The user does not appear to be setup with any Social Connections');
        jrCore_location('referrer');
    }
    if (isset($_rt['error']) && strlen($_rt['error']) > 0) {
        jrCore_set_form_notice('error', "This connection is reporting the following error:<br>{$_rt['error']}", false);
    }

    // Get user's info
    $_us = jrCore_db_get_item('jrUser', $_post['user_id'], true);

    jrCore_page_banner("linked networks for: {$_us['user_name']}");
    jrCore_get_form_notice();

    // Go get this user's info from OneAll
    $tmp = reset($_rt);
    $_tm = jrOneAll_api_call("users/{$tmp['user_token']}.json");
    if (!$_tm || !is_array($_tm)) {
        jrCore_set_form_notice('error', 'An error was encountered retrieving the user from OneAll - please try again');
        jrCore_location('referrer');
    }

    // See if this user has any linked identities
    $dat             = array();
    $dat[1]['title'] = 'network';
    $dat[1]['width'] = '22%;';
    $dat[2]['title'] = 'network name';
    $dat[2]['width'] = '22%;';
    $dat[3]['title'] = 'network URL';
    $dat[3]['width'] = '51%;';
    $dat[4]['title'] = 'delete';
    $dat[4]['width'] = '5%;';
    jrCore_page_table_header($dat);

    $_np = array();
    if (isset($_tm['response']['result']['data']['user']['identities'])) {
        $_np = $_tm['response']['result']['data']['user']['identities'];
    }
    if (count($_np) > 0) {
        $url = jrCore_get_module_url('jrImage');
        foreach ($_np as $id => $_prv) {
            if (isset($_rt["{$_prv['provider']}"])) {
                $dat             = array();
                $dat[1]['title'] = '<img src="' . $_conf['jrCore_base_url'] . '/' . $url . '/img/module/jrOneAll/' . $_prv['provider'] . '.png" width="32" height="32" alt="' . $_prv['provider'] . '">&nbsp;&nbsp;' . ucwords($_prv['provider']);
                if (isset($_prv['photos'][0]['value'])) {
                    $dat[2]['title'] = '<img src="' . $_prv['photos'][0]['value'] . '" width="32" height="32" alt="' . $_prv['displayName'] . '">&nbsp;&nbsp;' . $_prv['displayName'];
                }
                else {
                    $dat[2]['title'] = $_prv['displayName'];
                }
                $dat[3]['title'] = "<a href=\"{$_prv['profileUrl']}\" target=\"_blank\">{$_prv['profileUrl']}</a>";
                $dat[4]['title'] = jrCore_page_button("d{$id}", 'delete', "if (confirm('Are you sure you want to delete this connection?')){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/connection_delete/token={$_prv['identity_token']}')}");
                jrCore_page_table_row($dat);
            }
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>This user is not currently linked with any social networks!</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button('referrer');
    jrCore_page_display();
}

//------------------------------
// connection_delete
//------------------------------
function view_jrOneAll_connection_delete($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();

    jrUser_master_only();
    // Make sure we get a good user_id
    if (!isset($_post['token']) || strlen($_post['token']) === 0) {
        jrCore_set_form_notice('error', 'You have provided an invalid token - please try again');
        jrCore_location('referrer');
    }

    // Delete the connection
    $tbl = jrCore_db_table_name('jrOneAll', 'link');
    $req = "DELETE FROM {$tbl} WHERE token = '" . jrCore_db_escape($_post['token']) . "' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_set_form_notice('success', 'The connection has been successfully deleted');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered deleting the connection from the system - please try again');
    }
    jrCore_location('referrer');
}

//------------------------------
// networks
//------------------------------
function view_jrOneAll_networks($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrOneAll');
    $_lang = jrUser_load_lang_strings();

    if (jrUser_is_admin() && isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz')) {
        $_us = jrCore_db_get_item('jrUser', $_post['user_id']);
        if ($_us['user_name'] != $_user['user_name']) {
            jrCore_set_form_notice('notice', "You are viewing the Shared Networks for the user <strong>{$_us['user_name']}</strong>", false);
        }
    }
    else {
        $_us = $_user;
    }

    if (jrUser_is_admin()) {
        jrUser_account_tabs('networks', $_us);
    }
    else {
        jrUser_account_tabs('networks');
    }

    // Only a user can setup their own networks - admins cannot do it
    if (jrUser_is_admin() && isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz') && $_post['user_id'] != $_user['_user_id']) {
        jrCore_set_form_notice('notice', 'Only the user can select and activate the networks they want to link to their account.');
        $button = jrCore_page_button('p', $_us['profile_name'], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_us['profile_url']}')");
        jrCore_page_banner(5, $button);
        jrCore_get_form_notice();
    }
    else {

        if (isset($_conf['jrOneAll_public_key']) && strlen($_conf['jrOneAll_public_key']) > 0) {

            // our page banner
            $button = jrCore_page_button('p', $_user['profile_name'], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_user['profile_url']}')");
            jrCore_page_banner(5, $button);
            jrCore_get_form_notice();

            // Add in our custom OneAll Login box
            $ocb = str_replace(array('http://', 'https://'), '', rtrim(trim($_conf['jrOneAll_domain']), '/'));
            $_js = array('source' => jrCore_get_server_protocol() . '://' . $ocb . '/socialize/library.js');
            jrCore_create_page_element('javascript_href', $_js);

            $tbl = jrCore_db_table_name('jrOneAll', 'link');
            $req = "SELECT provider, updated, user_token, shared, error FROM {$tbl} WHERE user_id = '" . intval($_user['_user_id']) . "'";
            $_rt = jrCore_db_query($req, 'NUMERIC');

            // Get our User Token
            $tkn = '';
            if (isset($_rt) && is_array($_rt) && isset($_rt[0])) {
                $tkn = $_rt[0]['user_token'];
            }

            $prv  = implode("','", explode(',', $_conf['jrOneAll_social_networks']));
            $url  = jrCore_get_module_url('jrOneAll');
            $htm  = '<div id="oneall_linked_network_box" style="float:left;"><div id="oneall_social_link"></div>
            <script type="text/javascript">
            oneall.api.plugins.social_link.build("oneall_social_link", {
                \'providers\' : [\'' . $prv . '\'],
                \'callback_uri\': \'' . $_conf['jrCore_base_url'] . '/' . $url . '/link_callback' . '\',
                \'user_token\': \'' . $tkn . '\'
            });
            </script></div>';
            $_tmp = array(
                'type'  => 'custom',
                'label' => 7,
                'html'  => $htm,
                'help'  => 8
            );
            jrCore_form_field_create($_tmp);

            // Next, we need to get an optionlist of those networks this user has linked to
            if (isset($_rt) && is_array($_rt) && isset($_rt[0])) {

                $_tmp = array(
                    'submit_value'     => $_lang['jrCore'][72],
                    'cancel'           => 'referrer',
                    'form_ajax_submit' => false
                );
                jrCore_form_create($_tmp);

                $_an = array();
                $_pr = array();
                foreach ($_rt as $_tok) {
                    $_an["{$_tok['provider']}"] = $_tok['provider'];
                    if (isset($_tok['shared']) && $_tok['shared'] == '1') {
                        $_pr[] = $_tok['provider'];
                    }
                }
                $_tmp = array(
                    'name'     => 'linked_networks',
                    'default'  => '',
                    'type'     => 'optionlist',
                    'options'  => $_an,
                    'value'    => implode(',', $_pr),
                    'label'    => 10,
                    'sublabel' => 13,
                    'help'     => 11,
                    'validate' => 'core_string'
                );
                jrCore_form_field_create($_tmp);
            }

            // See if this user is already linked and has encountered any errors from the provider
            if (isset($tkn) && strlen($tkn) > 0) {

                $found = false;
                foreach ($_rt as $_link) {
                    if (isset($_link['error']) && strlen($_link['error']) > 0) {
                        $found = true;
                    }
                }
                if ($found) {

                    jrCore_page_divider();
                    jrCore_set_form_notice('error', 'The following errors have been encountered sharing to your networks');
                    jrCore_get_form_notice();

                    $dat             = array();
                    $dat[1]['title'] = 'provider';
                    $dat[1]['width'] = '15%;';
                    $dat[2]['title'] = 'date';
                    $dat[2]['width'] = '20%;';
                    $dat[3]['title'] = 'message';
                    $dat[3]['width'] = '60%;';
                    $dat[5]['title'] = 'delete';
                    $dat[5]['width'] = '5%;';
                    jrCore_page_table_header($dat);

                    foreach ($_rt as $_link) {
                        if (!isset($_link['error']) || strlen($_link['error']) === 0) {
                            continue;
                        }
                        $dat             = array();
                        $dat[1]['title'] = $_link['provider'];
                        $dat[1]['class'] = 'center';
                        $dat[2]['title'] = jrCore_format_time($_link['updated']);
                        $dat[2]['class'] = 'center';
                        $dat[3]['title'] = $_link['error'];
                        $dat[5]['title'] = jrCore_page_button("d{$_link['provider']}", 'delete', " jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/provider_error_delete/provider={$_link['provider']}')");
                        jrCore_page_table_row($dat);
                    }
                    jrCore_page_table_footer();
                }
            }
        }
        else {
            if (jrUser_is_master()) {
                jrCore_set_form_notice('error', 'The One All module is not configured - click on the &quot;Global Config&quot; tab above to configure the One All module.');
            }
            else {
                jrCore_set_form_notice('error', 9);
            }
            // our page banner
            jrCore_page_banner(5, 6);
            jrCore_get_form_notice();
        }
    }
    jrCore_page_display();
}

//------------------------------
// networks_save
//------------------------------
function view_jrOneAll_networks_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrOneAll');

    // Go through and activate/deactivate our linked networks
    if (isset($_post['linked_networks']) && strlen($_post['linked_networks']) > 0) {

        $_upd = array();
        $_tmp = explode(',', $_post['linked_networks']);
        if (isset($_tmp) && is_array($_tmp)) {
            foreach ($_tmp as $network) {
                if (jrCore_checktype($network, 'core_string')) {
                    $_upd[] = $network;
                }
            }
        }
        if ($_upd) {
            // Enable selected
            $uid = intval($_user['_user_id']);
            $tbl = jrCore_db_table_name('jrOneAll', 'link');
            $req = "UPDATE {$tbl} SET `shared` = '1' WHERE `provider` IN ('" . implode("','", $_upd) . "') AND user_id = '{$uid}'";
            jrCore_db_query($req, 'COUNT');
            // Disable the rest
            $req = "UPDATE {$tbl} SET `shared` = '0' WHERE `provider` NOT IN ('" . implode("','", $_upd) . "') AND user_id = '{$uid}'";
            jrCore_db_query($req);
        }
    }
    jrCore_form_delete_session();
    jrCore_set_form_notice('success', 12);
    jrCore_form_result();
}

//------------------------------
// provider_error_delete
//------------------------------
function view_jrOneAll_provider_error_delete($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();

    if (!isset($_post['provider']) || !jrCore_checktype($_post['provider'], 'core_string')) {
        jrCore_set_form_notice('error', 'Invalid provider name received - please try again');
        jrCore_location('referrer');
    }
    $prv = jrCore_db_escape($_post['provider']);
    $tbl = jrCore_db_table_name('jrOneAll', 'link');
    $req = "UPDATE {$tbl} SET `error` = '' WHERE `provider` = '{$prv}' AND user_id = '" . intval($_user['_user_id']) . "' LIMIT 1";
    jrCore_db_query($req, 'COUNT');
    jrCore_location('referrer');
}

//------------------------------
// link_callback (linking to network)
//------------------------------
function view_jrOneAll_link_callback($_post, $_user, $_conf)
{
    // [oa_action] => social_link
    // [oa_social_login_token] => 1481c1d9-eab9-45fb-bda5-642db7337609
    // [connection_token] => 1481c1d9-eab9-45fb-bda5-642db7337609
    if (isset($_post['connection_token']{10})) {

        // Get JSON info about this connection token
        $_data = jrOneAll_api_call("connections/{$_post['connection_token']}.json");
        if (!$_data || !is_array($_data)) {
            jrCore_set_form_notice('error', 'Unable to retrieve user data from callback - please try again');
            jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/networks");
        }

        // Get provider and activate link
        if (!isset($_data['response']['result']['data']['user']['identity'])) {
            jrCore_set_form_notice('error', 'Unable to retrieve user identity from callback - please try again');
            jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/networks");
        }
        $_us = $_data['response']['result']['data']['user']['identity'];

        // Service provider
        if (!isset($_us['provider']) || strlen($_us['provider']) === 0) {
            jrCore_set_form_notice('error', 'Unable to retrieve identity provider - please try again');
            jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/networks");
        }
        $prv = jrCore_db_escape($_us['provider']);

        // Identity Token - uniquely identifies this user for this service
        if (!isset($_us['identity_token']) || strlen($_us['identity_token']) === 0) {
            jrCore_set_form_notice('error', 'Unable to retrieve identity token - please try again');
            jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/networks");
        }
        $tok = jrCore_db_escape($_us['identity_token']);

        // Identity of the User
        $utk = jrCore_db_escape($_data['response']['result']['data']['user']['user_token']);
        if (!$utk || strlen($utk) === 0) {
            jrCore_set_form_notice('error', 'Unable to retrieve user token - please try again');
            jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/networks");
        }

        // Create new link, or update existing link if it is already there
        $tbl = jrCore_db_table_name('jrOneAll', 'link');
        $req = "INSERT INTO {$tbl} (user_id,provider,updated,token,user_token,shared,data)
                VALUES('{$_user['_user_id']}','{$prv}',UNIX_TIMESTAMP(),'{$tok}','{$utk}',1,'" . jrCore_db_escape(json_encode($_data)) . "')
                ON DUPLICATE KEY UPDATE `token` = '{$tok}', `user_token` = '{$utk}', `updated` = UNIX_TIMESTAMP(), `shared` = 1";
        jrCore_db_query($req, 'COUNT');
        jrCore_set_form_notice('success', "You have successfully linked your account to {$_us['provider']}!");
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/networks");
    }

    jrCore_set_form_notice('error', 'Invalid identity token - please try again');
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/networks");
    return false;
}

//------------------------------
// callback (signup, login)
//------------------------------
function view_jrOneAll_callback($_post, $_user, $_conf)
{
    // [_uri] => /oneall/callback
    // [oa_action] => social_login
    // [oa_social_login_token] => c206c52e-2e2f-465e-b728-a52107d7f239
    // [connection_token] => c206c52e-2e2f-465e-b728-a52107d7f239
    // [module_url] => oneall
    // [module] => jrOneAll
    // [option] => callback

    if (isset($_post['connection_token']{10})) {

        // Get JSON info about this connection token
        $_data = jrOneAll_api_call("connections/{$_post['connection_token']}.json");
        $_bkup = $_data;

        // check for existing user
        if (isset($_data['response']['result']['data']['user']['user_token']) && (isset($_data['response']['result']['status']['flag']) && $_data['response']['result']['status']['flag'] == 'success')) {

            // Identity Token - uniquely identified this user -> provider link up
            $ctk = jrCore_db_escape($_data['response']['result']['data']['user']['identity']['identity_token']);

            // User Token - uniquely identifies this user across all providers
            $tkn = jrCore_db_escape($_data['response']['result']['data']['user']['user_token']);

            // Our user info comes in as the "identity" array
            $_us = $_data['response']['result']['data']['user']['identity'];
            $prv = jrCore_db_escape($_us['provider']);

            // Find this user link in our system
            $tbl = jrCore_db_table_name('jrOneAll', 'link');
            $req = "SELECT * FROM {$tbl} WHERE user_token = '{$tkn}'";
            $_rt = jrCore_db_query($req, 'NUMERIC');

            // See if this is linked to a valid user account - if not, we are
            // going to remove it and let a new user account be linked up
            if ($_rt && is_array($_rt)) {
                $tot = count($_rt);
                foreach ($_rt as $v) {
                    $_ua = jrCore_db_get_item('jrUser', $v['user_id'], true);
                    if (!$_us || !is_array($_ua)) {
                        // bad user_id (i.e. deleted) - fix it
                        $req = "DELETE FROM {$tbl} WHERE user_id = '{$v['user_id']}'";
                        jrCore_db_query($req);
                        $tot--;
                    }
                }
                if ($tot === 0) {
                    // Found no existing accounts to link up - delete and recreate
                    $_rt = false;
                }
            }

            // Now we don't want to create a new account for this user if the
            // User Token already exists in our database - so we need to find
            // out if we have to create a new entry for this User -> Provider
            // Link Up or if it already exists

            // New User Account
            if (!is_array($_rt) && !jrUser_is_logged_in()) {

                // Load up default lang strings
                $url = jrCore_get_module_url('jrUser');
                $_ln = jrUser_load_lang_strings();

                // First up - get email
                $email = '';
                if (isset($_us['emails']) && is_array($_us['emails'])) {
                    // Try to get a validated email
                    foreach ($_us['emails'] as $_eml) {
                        $email = $_eml['value'];
                        if (isset($_eml['is_verified']) && $_eml['is_verified'] == '1') {
                            // We found our first validate email
                            break;
                        }
                    }
                }

                // Check for email already existing on an account
                if (jrCore_checktype($email, 'email')) {
                    $_eu = jrCore_db_get_item_by_key('jrUser', 'user_email', $email);
                    if ($_eu && is_array($_eu)) {
                        // We already have a user using this email - redirect to login
                        jrCore_set_form_notice('error', $_ln['jrOneAll'][31], false);
                        jrCore_location("{$_conf['jrCore_base_url']}/{$url}/login");
                    }
                }
                // When requiring a social login we must get a valid email address
                elseif (isset($_conf['jrOneAll_require_social']) && $_conf['jrOneAll_require_social'] == 'on') {
                    jrCore_set_form_notice('error', $_ln['jrOneAll'][28], false);
                    jrCore_location("{$_conf['jrCore_base_url']}/{$url}/login");
                }

                // We have either a new user or an existing user using the social login for the first time
                // Check for an existing username - if we find it, and it has the same email address
                if (isset($_us['preferredUsername']) && strlen($_us['preferredUsername']) > 0) {
                    $user_name = $_us['preferredUsername'];
                }
                elseif (isset($_us['displayName']) && strlen($_us['displayName']) > 0) {
                    $user_name = $_us['displayName'];
                }
                elseif (isset($_us['name']['formatted']) && strlen($_us['name']['formatted']) > 0) {
                    $user_name = $_us['name']['formatted'];
                }
                elseif (isset($_us['profileUrl']) && strlen($_us['profileUrl']) > 0) {
                    $utmp      = explode('/', $_us['profileUrl']);
                    $user_name = end($utmp);
                }
                elseif (isset($_us['accounts'][0]['username']) && strlen($_us['accounts'][0]['username']) > 0) {
                    $user_name = $_us['accounts'][0]['username'];
                }
                elseif (strpos($email, '@')) {
                    list($user_name,) = explode('@', $email);
                }
                else {
                    // Hopefully we never get here
                    $user_name = mt_rand(11111, 99999);
                }
                if (!jrCore_checktype($user_name, 'printable')) {
                    $user_name = jrCore_url_string($user_name);
                }
                $_eu = jrCore_db_get_item_by_key('jrUser', 'user_name', $user_name);

                // Existing Local account with same name
                $new = false;
                if ($_eu && is_array($_eu)) {
                    // Looks like someone else already has used this name - set it to a
                    // different variation and notify them at the end
                    $user_name .= mt_rand(1111, 9999);
                    $new = true;
                }

                // Create new User Account
                $iter = jrCore_get_advanced_setting('jrUser', 'password_iterations', 12);
                $text = substr(md5(microtime()), 5, 8);
                require APP_DIR . '/modules/jrUser/contrib/phpass/PasswordHash.php';
                $hash = new PasswordHash($iter, false);
                $pass = $hash->HashPassword($text);

                $_data = array(
                    'user_name'          => $user_name,
                    'user_email'         => $email,
                    'user_password'      => $pass,
                    'user_temp_password' => 1,
                    'user_group'         => 'user',
                    'user_language'      => (isset($_post['user_language']{0})) ? $_post['user_language'] : $_conf['jrUser_default_language'],
                    'user_active'        => 1,
                    'user_validate'      => md5(microtime()),
                    'user_validated'     => 1,
                    'user_jrOneAll'      => 1,
                    'user_last_login'    => 'UNIX_TIMESTAMP()'
                );

                // If our email or user name are BANNED, we flag
                // this user account as needing admin validation
                $banned = false;
                if (jrCore_module_is_active('jrBanned')) {
                    if (jrCore_run_module_function('jrBanned_is_banned', 'name', $user_name)) {
                        $_data['user_active'] = 0;
                        $banned               = true;
                    }
                    elseif (jrCore_run_module_function('jrBanned_is_banned', 'email', $email)) {
                        $_data['user_active'] = 0;
                        $banned               = true;
                    }
                }

                $uid = jrCore_db_create_item('jrUser', $_data);
                if (!$uid || !jrCore_checktype($uid, 'number_nz')) {
                    jrCore_page_notice('error', 'An error was encountered creating your user account - please try again');
                }

                // update user account with correct _user_id value
                $_temp = array();
                $_core = array(
                    '_user_id' => $uid
                );
                // Update account just created with proper user_id...
                jrCore_db_update_item('jrUser', $uid, $_temp, $_core);

                // User account is created - send out trigger so any listening
                // modules can do their work for this new user
                // Profile will be created here
                $_data['_user_id'] = $uid;

                // Figure out quota_id
                if (isset($_COOKIE['signup_quota_id']) && jrCore_checktype($_COOKIE['signup_quota_id'], 'number_nz')) {
                    $_post['quota_id'] = (int) $_COOKIE['signup_quota_id'];
                    unset($_COOKIE['signup_quota_id']);
                }

                $_pi = jrCore_trigger_event('jrUser', 'signup_created', $_post, $_data);

                // Save provider linkup
                $req = "INSERT INTO {$tbl} (user_id,updated,provider,token,user_token,shared,data)
                        VALUES('{$uid}',UNIX_TIMESTAMP(),'{$prv}','{$ctk}','{$tkn}',0,'" . jrCore_db_escape(json_encode($_bkup)) . "')";
                jrCore_db_query($req, 'INSERT_ID');

                if (!$banned && isset($_pi['signup_method']) && $_pi['signup_method'] != 'admin') {
                    // Send them a welcome email if we allow password changes
                    if (!isset($_conf['jrOneAll_require_social']) || $_conf['jrOneAll_require_social'] != 'on') {
                        $_rp = array(
                            'system_name'      => $_conf['jrCore_system_name'],
                            'jamroom_url'      => $_conf['jrCore_base_url'],
                            'provider'         => $prv,
                            'user_name'        => $user_name,
                            'user_pass'        => $text,
                            'user_email'       => $email,
                            'user_account_url' => "{$_conf['jrCore_base_url']}/" . jrCore_get_module_url('jrUser') . "/account"
                        );
                        list($sub, $msg) = jrCore_parse_email_templates('jrOneAll', 'password', $_rp);
                        if (isset($email) && jrCore_checktype($email, 'email')) {
                            jrCore_logger('INF', "notifying new OneAll user of their password info: {$email}", $_rp);
                            jrCore_send_email($email, $sub, $msg);
                        }
                        elseif (jrCore_module_is_active('jrPrivateNote')) {
                            // Send user system PN
                            // Send it from the system user
                            jrPrivateNote_send_note($uid, 0, $sub, $msg);
                        }
                    }
                }

                // Get User account (now with profile info)
                $_usr = jrCore_db_get_item('jrUser', $uid, false, true);

                // Next - let's see if we have an image for this user
                if (isset($_us['photos']) && is_array($_us['photos'])) {
                    // The last picture in the set will be the largest
                    $_photo = end($_us['photos']);
                    if ($_photo && is_array($_photo) && isset($_photo['value']) && jrCore_checktype($_photo['value'], 'url')) {

                        // Download the file locally and use it as both our Account and Profile photo
                        $cdir = jrCore_get_module_cache_dir('jrOneAll');
                        $file = "{$cdir}/{$uid}_user_image";
                        if (jrCore_download_file($_photo['value'], $file)) {

                            // Is this a valid image?
                            $_im = getimagesize($file);
                            if ($_im && is_array($_im)) {
                                // Are we configured for minimum image width?
                                $minw = jrCore_get_advanced_setting('jrImage', 'minimum_width', 0);
                                if ($minw == 0 || $_im[0] >= $minw) {
                                    $_img = jrCore_save_media_file('jrUser', $file, $_usr['_profile_id'], $uid);
                                    if ($_img && is_array($_img)) {
                                        $_usr       = array_merge($_usr, $_img);
                                        $user_image = jrCore_get_media_file_path('jrUser', 'user_image', $_usr);
                                        if (is_file($user_image)) {
                                            $ext = jrCore_file_extension($user_image);
                                            $nam = "{$_usr['_profile_id']}_profile_image";
                                            if (jrCore_copy_media_file($_usr['_profile_id'], $user_image, $nam)) {
                                                $dir = dirname($user_image);
                                                jrCore_write_to_file("{$dir}/{$nam}.tmp", "profile_image.{$ext}");
                                                jrCore_save_media_file('jrProfile', "{$dir}/{$nam}", $_usr['_profile_id'], $_usr['_profile_id']);
                                                unlink("{$dir}/{$nam}");
                                                unlink("{$dir}/{$nam}.tmp");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // Check if we are doing ADMIN validation on sign ups in this quota - if so, notify admins
                if ($banned || (isset($_pi['signup_method']) && $_pi['signup_method'] == 'admin')) {
                    $_dt = array(
                        'user_active'    => 0,
                        'user_validated' => 0
                    );
                    jrCore_db_update_item('jrUser', $uid, $_dt);
                    $_data['signup_method'] = $_pi['signup_method'];
                    if (isset($_conf['jrUser_signup_notify']) && $_conf['jrUser_signup_notify'] == 'on') {
                        $_ad = jrUser_get_admin_user_ids();
                        if ($_ad && is_array($_ad)) {
                            $_data['system_name']     = $_conf['jrCore_system_name'];
                            $_data['ip_address']      = jrCore_get_ip();
                            $_data['new_profile_url'] = "{$_conf['jrCore_base_url']}/" . rawurldecode(jrCore_url_string($_data['user_name']));
                            jrCore_logger('INF', "notifying admin users of new OneAll signup", $_data);
                            list($sub, $msg) = jrCore_parse_email_templates('jrUser', 'notify_signup', $_data);
                            foreach ($_ad as $uid) {
                                jrUser_notify($uid, 0, 'jrUser', 'signup_notify', $sub, $msg);
                            }
                        }
                    }

                    // Show them success
                    jrCore_logger('INF', "{$_data['user_name']} has validated their account via {$prv} and is pending approval");
                    jrCore_notice_page('success', $_ln['jrUser'][105], $_conf['jrCore_base_url'], $_ln['jrOneAll'][32], false);
                    return true;
                }

                // Reload User account - start session
                global $_user;
                $_SESSION = $_usr;
                /** @noinspection PhpUnusedLocalVariableInspection */
                $_user = $_SESSION; // Leave this here!

                // let modules know signup is activated
                jrCore_trigger_event('jrUser', 'signup_activated', $_SESSION);

                // Startup session with user info
                $_SESSION = jrCore_trigger_event('jrUser', 'login_success', $_SESSION);

                // Show them success
                jrCore_logger('INF', "{$_data['user_name']} has validated their account via {$prv} and logged in");

                // Redirect them to their new account section so they can fill in
                // any missing information that we may not have received
                if (strlen($email) === 0) {
                    $_lng = jrUser_load_lang_strings();
                    jrCore_set_form_notice('error', $_lng['jrOneAll'][30], false);
                    jrCore_form_field_hilight('user_email');
                    if (is_numeric($_data['user_name'])) {
                        jrCore_form_field_hilight('user_name');
                    }
                }
                elseif ($new) {
                    $_lng = jrUser_load_lang_strings();
                    jrCore_set_form_notice('error', $_lng['jrOneAll'][26], false);
                    jrCore_form_field_hilight('user_name');
                }

                // If we are only using social login, no need to redirect to their account
                if (isset($_conf['jrOneAll_require_social']) && $_conf['jrOneAll_require_social'] == 'on') {
                    jrCore_location("{$_conf['jrCore_base_url']}/" . jrCore_url_string($_data['user_name']));
                }
                jrCore_set_form_notice('error', $_ln['jrOneAll'][29]);
                jrCore_form_field_hilight('user_passwd1');
                jrCore_form_field_hilight('user_passwd2');
                jrCore_location("{$_conf['jrCore_base_url']}/{$url}/account");
                return true;
            }

            // Existing User coming in on an existing or new Connection Token
            $_rt = reset($_rt);
            $req = "INSERT INTO {$tbl} (user_id,updated,provider,token,user_token,shared,data)
                    VALUES('{$_rt['user_id']}',UNIX_TIMESTAMP(),'{$prv}','{$ctk}','{$tkn}','0','" . jrCore_db_escape(json_encode($_bkup)) . "')
                    ON DUPLICATE KEY UPDATE `updated` = UNIX_TIMESTAMP(), `token` = '{$ctk}', `data` = VALUES(`data`)";
            jrCore_db_query($req);

            // See if this user is active
            $_us = jrCore_db_get_item('jrUser', $_rt['user_id'], false, true);
            if (!$_us || is_array($_us) && isset($_us['user_validated']) && $_us['user_validated'] == 0) {
                $_ln = jrUser_load_lang_strings();
                jrCore_notice_page('success', $_ln['jrUser'][105], $_conf['jrCore_base_url'], $_ln['jrOneAll'][32], false);
            }
            $_SESSION = $_us;
            jrCore_location("{$_conf['jrCore_base_url']}/{$_SESSION['profile_url']}");
            return true;

        }

        // If we fall through to here it means the JSON response from one all was invalid
        jrCore_logger('CRI', "invalid JSON response from OneAll", $_bkup);
        jrCore_notice_page('error', 'An error was encountered intializing your social connection - please try again shortly. (2)');
        return false;
    }

    jrCore_logger('CRI', "invalid token response from OneAll", $_post);
    jrCore_notice_page('error', 'Invalid Token received - please try again');
    return false;
}

//------------------------------
// system_feed
//------------------------------
function view_jrOneAll_system_feed($_post, $_user, $_conf)
{
    jrUser_master_only();

    $_lang = jrUser_load_lang_strings();

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrOneAll');

    if (isset($_conf['jrOneAll_public_key']) && strlen($_conf['jrOneAll_public_key']) > 0) {

        // our page banner
        jrCore_page_banner('System Feed');
        jrCore_get_form_notice();
        jrCore_page_note("This is a special account for the entire site allowing you to<br>link all member activity to a single social network account.<br>Each member can still link their own account to their own social networks individually.");

        // Add in our custom OneAll Login box
        $ocb = str_replace(array('http://', 'https://'), '', rtrim(trim($_conf['jrOneAll_domain']), '/'));
        $_js = array('source' => jrCore_get_server_protocol() . '://' . $ocb . '/socialize/library.js');
        jrCore_create_page_element('javascript_href', $_js);

        $tbl = jrCore_db_table_name('jrOneAll', 'link');
        $req = "SELECT provider, updated, user_token, shared, error FROM {$tbl} WHERE user_id = '0'";
        $_rt = jrCore_db_query($req, 'NUMERIC');

        // Get our User Token
        $tkn = '';
        if (isset($_rt) && is_array($_rt) && isset($_rt[0])) {
            $tkn = $_rt[0]['user_token'];
        }

        $prv  = implode("','", explode(',', $_conf['jrOneAll_social_networks']));
        $url  = jrCore_get_module_url('jrOneAll');
        $htm  = '<div id="oneall_linked_network_box" style="float:left;"><div id="oneall_social_link"></div>
        <script type="text/javascript">
            oneall.api.plugins.social_link.build("oneall_social_link", {
                \'providers\' : [\'' . $prv . '\'],
                \'callback_uri\': \'' . $_conf['jrCore_base_url'] . '/' . $url . '/system_link_callback' . '\',
                \'user_token\': \'' . $tkn . '\'
            });
        </script></div>';
        $_tmp = array(
            'type'     => 'custom',
            'label'    => $_lang['jrOneAll'][7],
            'sublabel' => $_lang['jrOneAll'][13],
            'html'     => $htm,
            'help'     => $_lang['jrOneAll'][8]
        );
        jrCore_form_field_create($_tmp);

        // Next, we need to get an optionlist of those networks this user has linked to
        if ($_rt && is_array($_rt) && isset($_rt[0])) {

            $_tmp = array(
                'submit_value'     => $_lang['jrCore'][72],
                'cancel'           => 'referrer',
                'form_ajax_submit' => false
            );
            jrCore_form_create($_tmp);

            $_an = array();
            $_pr = array();
            foreach ($_rt as $_tok) {
                $_an["{$_tok['provider']}"] = $_tok['provider'];
                if (isset($_tok['shared']) && $_tok['shared'] == '1') {
                    $_pr[] = $_tok['provider'];
                }
            }
            $_tmp = array(
                'name'     => 'linked_networks',
                'default'  => '',
                'type'     => 'optionlist',
                'options'  => $_an,
                'value'    => implode(',', $_pr),
                'label'    => 10,
                'sublabel' => 13,
                'help'     => 11,
                'validate' => 'core_string'
            );
            jrCore_form_field_create($_tmp);
        }

        // See if this user is already linked and has encountered any errors from the provider
        if (isset($tkn) && strlen($tkn) > 0) {

            $found = false;
            foreach ($_rt as $_link) {
                if (isset($_link['error']) && strlen($_link['error']) > 0) {
                    $found = true;
                }
            }
            if ($found) {

                jrCore_page_divider();
                jrCore_set_form_notice('error', 'The following errors have been encountered sharing to your networks');
                jrCore_get_form_notice();

                $dat             = array();
                $dat[1]['title'] = 'provider';
                $dat[1]['width'] = '15%;';
                $dat[2]['title'] = 'date';
                $dat[2]['width'] = '20%;';
                $dat[3]['title'] = 'message';
                $dat[3]['width'] = '60%;';
                $dat[5]['title'] = 'delete';
                $dat[5]['width'] = '5%;';
                jrCore_page_table_header($dat);

                foreach ($_rt as $_link) {
                    if (!isset($_link['error']) || strlen($_link['error']) === 0) {
                        continue;
                    }
                    $dat             = array();
                    $dat[1]['title'] = $_link['provider'];
                    $dat[1]['class'] = 'center';
                    $dat[2]['title'] = jrCore_format_time($_link['updated']);
                    $dat[2]['class'] = 'center';
                    $dat[3]['title'] = $_link['error'];
                    $dat[5]['title'] = jrCore_page_button("d{$_link['provider']}", 'delete', " jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/provider_error_delete/provider={$_link['provider']}')");
                    jrCore_page_table_row($dat);
                }
                jrCore_page_table_footer();
            }
        }
    }
    else {

        jrCore_set_form_notice('error', 'The One All module is not configured - click on the &quot;Global Config&quot; tab above to configure the One All module.');
        jrCore_page_banner(5, 6);
        jrCore_get_form_notice();
    }
    jrCore_page_display();
}

//------------------------------
// system_feed_save
//------------------------------
function view_jrOneAll_system_feed_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Go through and activate/deactivate our linked networks
    if (isset($_post['linked_networks']) && strlen($_post['linked_networks']) > 0) {

        $_upd = array();
        $_tmp = explode(',', $_post['linked_networks']);
        if (isset($_tmp) && is_array($_tmp)) {
            foreach ($_tmp as $network) {
                if (jrCore_checktype($network, 'core_string')) {
                    $_upd[] = $network;
                }
            }
        }
        if ($_upd) {
            // Enable selected
            $uid = 0;
            $tbl = jrCore_db_table_name('jrOneAll', 'link');
            $req = "UPDATE {$tbl} SET `shared` = '1' WHERE `provider` IN ('" . implode("','", $_upd) . "') AND user_id = '{$uid}'";
            jrCore_db_query($req, 'COUNT');
            // Disable the rest
            $req = "UPDATE {$tbl} SET `shared` = '0' WHERE `provider` NOT IN ('" . implode("','", $_upd) . "') AND user_id = '{$uid}'";
            jrCore_db_query($req);
        }
    }
    jrCore_form_delete_session();
    jrCore_set_form_notice('success', 12);
    jrCore_form_result();
}

//------------------------------
// system_link_callback (linking to network)
//------------------------------
function view_jrOneAll_system_link_callback($_post, $_user, $_conf)
{
    // [oa_action] => social_link
    // [oa_social_login_token] => 1481c1d9-eab9-45fb-bda5-642db7337609
    // [connection_token] => 1481c1d9-eab9-45fb-bda5-642db7337609
    if (isset($_post['connection_token']{10})) {

        // Get JSON info about this connection token
        $_data = jrOneAll_api_call("connections/{$_post['connection_token']}.json");
        if (!$_data || !is_array($_data)) {
            jrCore_set_form_notice('error', 'Unable to retrieve user data from callback - please try again');
            jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/system_feed");
        }

        // Get provider and activate link
        $_us = $_data['response']['result']['data']['user']['identity'];
        if (!$_us || !is_array($_us)) {
            jrCore_set_form_notice('error', 'Unable to retrieve user identity from callback - please try again (2)');
            jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/system_feed");
        }

        // Service provider
        if (!isset($_us['provider']) || strlen($_us['provider']) === 0) {
            jrCore_set_form_notice('error', 'Unable to retrieve identity provider - please try again');
            jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/system_feed");
        }
        $prv = jrCore_db_escape($_us['provider']);

        // Identity Token - uniquely identifies this user for this service
        if (!isset($_us['identity_token']) || strlen($_us['identity_token']) === 0) {
            jrCore_set_form_notice('error', 'Unable to retrieve identity token - please try again');
            jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/system_feed");
        }
        $tok = jrCore_db_escape($_us['identity_token']);

        // Identity of the User
        $utk = jrCore_db_escape($_data['response']['result']['data']['user']['user_token']);
        if (!$utk || strlen($utk) === 0) {
            jrCore_set_form_notice('error', 'Unable to retrieve user token - please try again');
            jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/system_feed");
        }

        // Create new link, or update existing link if it is already there
        $tbl = jrCore_db_table_name('jrOneAll', 'link');
        $req = "INSERT INTO {$tbl} (user_id,provider,updated,token,user_token,shared,data)
                VALUES('0','{$prv}',UNIX_TIMESTAMP(),'{$tok}','{$utk}',1,'" . jrCore_db_escape(json_encode($_data)) . "')
                ON DUPLICATE KEY UPDATE `token` = '{$tok}', `user_token` = '{$utk}', `updated` = UNIX_TIMESTAMP(), `shared` = 1";
        jrCore_db_query($req, 'COUNT');
        jrCore_set_form_notice('success', "You have successfully linked your account to {$_us['provider']}!");
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/system_feed");
    }

    jrCore_set_form_notice('error', 'Invalid identity token - please try again');
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/system_feed");
    return false;
}
