<?php
/**
 * Jamroom Invitations module
 *
 * copyright 2017 The Jamroom Network
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
// browse
//------------------------------
function view_jrInvite_browse($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_session_require_login();
    jrUser_check_quota_access('jrInvite');

    $_lang = jrUser_load_lang_strings();
    $tmp   = jrCore_page_button('new_invite', $_lang['jrInvite'][21], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/create')");
    jrCore_page_banner(13, $tmp);

    $_tabs = array(
        'sending'  => array(
            'label'  => $_lang['jrInvite'][7],
            'url'    => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse/sending",
            'active' => ((!isset($_post['_1']) || $_post['_1'] == 'sending') ? true : false)
        ),
        'received' => array(
            'label'  => $_lang['jrInvite'][6],
            'url'    => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse/received",
            'active' => ((isset($_post['_1']) && $_post['_1'] == 'received') ? true : false)
        ),
    );
    jrCore_page_tab_bar($_tabs);

    if (isset($_post['_1']) && $_post['_1'] == 'received') {
        // Received
        $tbl = jrCore_db_table_name('jrInvite', 'invitee');
        $req = "SELECT * FROM {$tbl} WHERE `invitee_user_id` = '{$_user['_user_id']}' AND `invitee_active` = 1";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt) && count($_rt) > 0) {
            $_ids = array();
            foreach ($_rt as $rt) {
                $_ids["{$rt['invitee_invite_id']}"] = $rt['invitee_invite_id'];
            }
            if (!isset($_post['p'])) {
                $_post['p'] = 1;
            }
            $_s  = array(
                "search"    => array(
                    '_item_id IN ' . implode(',', $_ids)
                ),
                "order_by"  => array(
                    "_updated" => "numerical_desc"
                ),
                'page'      => $_post['p'],
                'pagebreak' => 12,
            );
            $_it = jrCore_db_search_items('jrInvite', $_s);
            if ($_it && is_array($_it['_items']) && count($_it['_items']) > 0) {
                $dat             = array();
                $dat[1]['title'] = $_lang['jrInvite'][5];
                $dat[1]['width'] = '15%';
                $dat[2]['title'] = $_lang['jrInvite'][52];
                $dat[2]['width'] = '15%';
                $dat[3]['title'] = $_lang['jrInvite'][16];
                $dat[3]['width'] = '15%';
                $dat[4]['title'] = $_lang['jrInvite'][24];
                $dat[4]['width'] = '50%';
                $dat[5]['title'] = '';
                $dat[5]['width'] = '5%';
                jrCore_page_table_header($dat);
                foreach ($_it['_items'] as $k => $it) {
                    $dat             = array();
                    $dat[1]['title'] = jrCore_format_time($it['invite_invitee']["{$_user['_user_id']}"]['invitee_created']);
                    $dat[1]['class'] = 'center';
                    $dat[2]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$it['profile_url']}\">{$it['user_name']}</a>";
                    $dat[2]['class'] = 'center';
                    $dat[3]['title'] = (isset($_lang["{$it['invite_module']}"]['menu'])) ? ucwords($_lang["{$it['invite_module']}"]['menu']) : ucwords($_mods["{$it['invite_module']}"]['module_name']);
                    $dat[3]['class'] = 'center';
                    $pfx             = jrCore_db_get_prefix($it['invite_module']);
                    $_xt             = jrCore_db_get_item($it['invite_module'], $it['invite_item_id']);
                    if ($it['invite_module'] == 'jrProfile') {
                        $title = '@' . $_xt['profile_url'];
                    }
                    else {
                        $title = $_xt["{$pfx}_title"];
                    }
                    $dat[4]['title'] = "{$title}<br>{$it['invite_text']}";
                    $dat[4]['class'] = 'center';
                    $dat[5]['title'] = jrCore_page_button("v{$k}", $_lang['jrInvite'][53], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/view/{$it['_item_id']}/{$_user['_user_id']}')");
                    jrCore_page_table_row($dat);
                }
                jrCore_page_table_footer();
                jrCore_page_table_pager($_it);
            }
        }
        else {
            jrCore_page_note($_lang['jrInvite'][3]);
        }
    }
    else {
        // Sent
        // Get all user invites
        if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
            $_post['p'] = 1;
        }
        $_s  = array(
            "search"    => array(
                "_profile_id = " . jrUser_get_profile_home_key('_profile_id')
            ),
            "order_by"  => array(
                "_updated" => "numerical_desc"
            ),
            'page'      => $_post['p'],
            'pagebreak' => 12,
            'no_cache'  => true
        );
        $_rt = jrCore_db_search_items('jrInvite', $_s);
        if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {

            $dat             = array();
            $dat[1]['title'] = $_lang['jrInvite'][22];
            $dat[1]['width'] = '15%';
            $dat[2]['title'] = $_lang['jrInvite'][16];
            $dat[2]['width'] = '15%';
            $dat[3]['title'] = $_lang['jrInvite'][18];
            $dat[3]['width'] = '30%';
            $dat[4]['title'] = $_lang['jrInvite'][42];
            $dat[4]['width'] = '25%';
            $dat[5]['title'] = $_lang['jrInvite'][30];
            $dat[5]['width'] = '5%';
            $dat[6]['title'] = $_lang['jrCore'][37];
            $dat[6]['width'] = '5%';
            $dat[7]['title'] = $_lang['jrCore'][38];
            $dat[7]['width'] = '5%';
            jrCore_page_table_header($dat);

            foreach ($_rt['_items'] as $rt) {
                $pfx = jrCore_db_get_prefix($rt['invite_module']);
                $_it = jrCore_db_get_item($rt['invite_module'], $rt['invite_item_id']);
                if ($rt['invite_module'] == 'jrProfile') {
                    $title = $_it["profile_name"];
                    $url   = "{$_conf['jrCore_base_url']}/{$_it['profile_url']}";
                }
                else {
                    $title = $_it["{$pfx}_title"];
                    $murl  = jrCore_get_module_url($rt['invite_module']);
                    $url   = "{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$murl}/{$_it['_item_id']}/{$_it["{$pfx}_title_url"]}";
                }
                $dat             = array();
                $dat[1]['title'] = jrCore_format_time($rt['_created']);
                $dat[1]['class'] = 'center';
                $dat[2]['title'] = (isset($_lang["{$rt['invite_module']}"]['menu'])) ? ucwords($_lang["{$rt['invite_module']}"]['menu']) : ucwords($_mods["{$rt['invite_module']}"]['module_name']);
                $dat[2]['class'] = 'center';
                $dat[3]['title'] = "<a href=\"{$url}\">{$title}</a>";
                $dat[3]['class'] = 'center';
                $dat[4]['title'] = '';
                if (isset($rt['invite_invitee']) && is_array($rt['invite_invitee'])) {
                    $all = true;
                    $_iv = array();
                    $cnt = count($rt['invite_invitee']);
                    if ($cnt > 0) {
                        foreach ($rt['invite_invitee'] as $k => $_v) {
                            $_iv[$k] = "<a href=\"{$_conf['jrCore_base_url']}/{$_v['profile_url']}\">{$_v['user_name']}</a>";
                            if (!isset($_v['invitee_viewed']) || $_v['invitee_viewed'] != 0) {
                                $_iv[$k] .= " [{$_lang['jrInvite'][51]}]";
                            }
                            if ($k > 19 && $all) {
                                $_iv[$k] .= '<br><a onclick="$(\'#vai\').slideToggle()">' . $_lang['jrInvite'][55] . '</a><div id="vai" style="display:none">';
                                $all = false;
                            }
                        }
                        $dat[4]['title'] = implode('<br>', $_iv);
                        if (!$all) {
                            $dat[4]['title'] .= '</div>';
                        }
                    }
                }
                $dat[5]['title'] = jrCore_page_button('invite', $_lang['jrInvite'][30], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/invite_users/id={$rt['_item_id']}')");
                $dat[6]['title'] = '';
                if (!($rt['invite_invitee'] && is_array($rt['invite_invitee']) && count($rt['invite_invitee']) > 0)) {
                    $dat[6]['title'] = jrCore_page_button('update', $_lang['jrInvite'][25], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/update/id={$rt['_item_id']}')");
                }
                $dat[7]['title'] = jrCore_page_button('delete', $_lang['jrInvite'][26], "if(confirm('{$_lang['jrInvite'][27]}')){jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete/id={$rt['_item_id']}')}");
                jrCore_page_table_row($dat);
            }
            jrCore_page_table_footer();
            jrCore_page_table_pager($_rt);
        }
        else {
            jrCore_page_note($_lang['jrInvite'][20]);
        }
    }
    jrCore_page_display();
}

//------------------------------
// invite_users
//------------------------------
function view_jrInvite_invite_users($_post, $_user, $_conf)
{
    // Do security
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrInvite');

    $_lang = jrUser_load_lang_strings();
    $tmp   = jrCore_page_button('new_invite', $_lang['jrInvite'][21], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/create')");
    jrCore_page_banner(30, $tmp);

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid ID - please pass in a valid invite ID.');
    }
    $_rt = jrCore_db_get_item('jrInvite', $_post['id']);
    if ($_rt && is_array($_rt)) {

        // Make sure the calling user has permission to edit this item
        if (!jrUser_can_edit_item($_rt)) {
            jrUser_not_authorized();
        }

        // See if this user has any followers
        $_ut = array();
        if ($_user['quota_jrInvite_invitee_type'] != 2) {
            if (jrUser_is_admin()) {
                // Admin - Get all users
                $_sp = array(
                    "order_by"      => array(
                        "user_name" => "ASC"
                    ),
                    'skip_triggers' => true,
                    'return_keys'   => array('_user_id', 'user_name'),
                    'limit'         => 10000
                );
                $_sp = jrCore_db_search_items('jrUser', $_sp);
                if ($_sp && is_array($_sp) && isset($_sp['_items'])) {
                    foreach ($_sp['_items'] as $_u) {
                        if ($_u['_user_id'] != $_user['_user_id']) {
                            $_ut[] = array(
                                '_user_id'  => $_u['_user_id'],
                                'user_name' => $_u['user_name']
                            );
                        }
                    }
                }
                unset($_sp);
            }
            else {
                if (jrCore_module_is_active('jrFollower')) {
                    $_ft = jrFollower_get_users_following($_user['_profile_id']);
                    if ($_ft && is_array($_ft) && count($_ft) > 0) {
                        foreach ($_ft as $user_id => $user_name) {
                            $_ut[] = array(
                                '_user_id'  => $user_id,
                                'user_name' => $user_name
                            );
                        }
                    }
                }
            }
            if (count($_ut) === 0) {
                jrCore_notice_page('error', $_lang['jrInvite'][31], 'referrer');
            }
        }

        // Show invitation info
        $pfx = jrCore_db_get_prefix($_rt['invite_module']);
        $_it = jrCore_db_get_item($_rt['invite_module'], $_rt['invite_item_id']);
        if ($_rt['invite_module'] == 'jrProfile') {
            $title = $_it["profile_name"];
        }
        else {
            $title = $_it["{$pfx}_title"];
        }
        jrCore_page_note($_lang['jrInvite'][10] . ': ' . $title);

        // Form init
        $_tmp = array(
            'submit_value'  => $_lang['jrInvite'][30],
            'cancel'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse",
            'submit_prompt' => $_lang['jrInvite'][36],
            'submit_modal'  => 'update',
            'modal_width'   => 600,
            'modal_height'  => 400,
            'modal_note'    => $_lang['jrInvite'][37]
        );
        jrCore_form_create($_tmp);

        // id
        $_tmp = array(
            'name'     => 'id',
            'type'     => 'hidden',
            'value'    => $_post['id'],
            'validate' => 'number_nz'
        );
        jrCore_form_field_create($_tmp);

        // module
        $_tmp = array(
            'name'     => 'invite_module',
            'type'     => 'hidden',
            'value'    => $_rt['invite_module'],
            'validate' => 'printable'
        );
        jrCore_form_field_create($_tmp);

        // Invite Followers Only
        if ($_user['quota_jrInvite_invitee_type'] != 2) {

            $_opts = array();
            foreach ($_ut as $ut) {
                if (isset($_rt['invite_invitee']["{$ut['_user_id']}"])) {
                    if ($_rt['invite_invitee']["{$ut['_user_id']}"]['invitee_viewed'] == 1) {
                        $_opts["{$ut['_user_id']}"] = "{$ut['user_name']} &nbsp; [{$_lang['jrInvite'][54]}, {$_lang['jrInvite'][51]}]";
                    }
                    else {
                        $_opts["{$ut['_user_id']}"] = "{$ut['user_name']} &nbsp; [{$_lang['jrInvite'][54]}]";
                    }
                }
                else {
                    $_opts["{$ut['_user_id']}"] = $ut['user_name'];
                }
            }
            if (count($_opts) > 0) {
                asort($_opts);
                $_tmp = array(
                    'name'     => 'invite_users',
                    'label'    => 32,
                    'sublabel' => 34,
                    'help'     => 33,
                    'type'     => 'select_multiple',
                    'options'  => $_opts
                );
                jrCore_form_field_create($_tmp);
            }
        }

        // Invite By Email
        if ($_user['quota_jrInvite_invitee_type'] != 1) {
            $_tmp = array(
                'name'     => 'invite_emails',
                'label'    => 4,
                'sublabel' => 48,
                'help'     => 49,
                'type'     => 'textarea'
            );
            jrCore_form_field_create($_tmp);
        }
    }
    else {
        jrCore_notice_page('error', 'The invitation entry was not found in the datastore - please try again.');
    }
    jrCore_page_display();
}

//------------------------------
// invite_users_save
//------------------------------
function view_jrInvite_invite_users_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrInvite');

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_form_modal_notice('complete', "ERROR: Invalid IDs - please pass in a valid invite and user IDs.");
        exit;
    }

    // Make sure we get a good module
    $_lang = jrUser_load_lang_strings();
    if (!isset($_post['invite_module']) || !jrCore_module_is_active($_post['invite_module'])) {
        jrCore_form_modal_notice('complete', "ERROR: {$_lang['jrInvite'][35]}");
        exit;
    }

    // Get invitation info
    $_rt = jrCore_db_get_item('jrInvite', $_post['id']);
    $_it = jrCore_db_get_item($_rt['invite_module'], $_rt['invite_item_id']);
    if ($_rt['invite_module'] == 'jrProfile') {
        $title = $_it["profile_name"];
    }
    else {
        $pfx   = jrCore_db_get_prefix($_rt['invite_module']);
        $title = $_it["{$pfx}_title"];
    }
    $_replace = array(
        'system_name' => $_conf['jrCore_system_name'],
        'inviter'     => $_rt['user_name'],
        'invite_text' => jrCore_strip_html($_rt['invite_text']),
        'item_title'  => jrCore_strip_html($title)
    );
    if ($_rt && $_it && is_array($_rt) && is_array($_it)) {
        // Set counters
        $fctr = 0;
        $ectr = 0;
        // Invite followers?
        if ($_user['quota_jrInvite_invitee_type'] != 2) {
            // Any selected?
            if (isset($_post['invite_users']) && is_array($_post['invite_users']) && count($_post['invite_users']) > 0) {
                // If !admin get all followers
                if (!jrUser_is_admin()) {
                    if (!$_followers = jrFollower_get_users_following($_user['_profile_id'])) {
                        jrCore_form_modal_notice('complete', "ERROR: {$_lang['jrInvite'][44]}");
                        exit;
                    }
                }
                // Get all users to be invited
                $iu  = implode(',', $_post['invite_users']);
                $_s  = array(
                    "search"        => array(
                        "_user_id IN {$iu}"
                    ),
                    'return_keys'   => array('_user_id', 'user_name', 'user_email'),
                    'skip_triggers' => true,
                    "limit"         => count($_post['invite_users'])
                );
                $_ut = jrCore_db_search_items('jrUser', $_s);
                if ($_ut && is_array($_ut['_items']) && count($_ut['_items']) > 0) {
                    // Invite them
                    $tbl = jrCore_db_table_name('jrInvite', 'invitee');
                    foreach ($_ut['_items'] as $ut) {
                        if (jrUser_is_admin() || isset($_followers["{$ut['_user_id']}"])) {

                            // Add to invitee table
                            $req = "INSERT IGNORE INTO {$tbl} (invitee_created,invitee_user_id,invitee_invite_id,invitee_viewed,invitee_active) VALUES (UNIX_TIMESTAMP(),'{$ut['_user_id']}','{$_post['id']}',0,1)";
                            jrCore_db_query($req);

                            // Send email
                            $_replace['invitee'] = $ut['user_name'];
                            $_replace['url']     = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/view/{$_rt['_item_id']}/{$ut['_user_id']}/" . jrCore_url_string($_replace['item_title']);
                            list($sub, $msg) = jrCore_parse_email_templates('jrInvite', 'invite', $_replace);
                            jrUser_notify($ut['_user_id'], $_user['_user_id'], 'jrInvite', 'new_invitation', $sub, $msg);
                            jrCore_form_modal_notice('update', "{$_lang['jrInvite'][38]} {$ut['user_name']}");
                            $fctr++;

                        }
                    }
                }
            }
        }

        // Invite emails?
        if ($_user['quota_jrInvite_invitee_type'] != 1) {
            // Any entered?
            if (isset($_post['invite_emails']) && strlen($_post['invite_emails']) > 0) {
                $_emails = preg_split('/\r\n|[\r\n]/', $_post['invite_emails']);
                if (is_array($_emails)) {
                    // Email them
                    if ($_rt['invite_module'] == 'jrProfile') {
                        $title = $_it["profile_name"];
                        $url   = "{$_conf['jrCore_base_url']}/{$_it["profile_url"]}";
                    }
                    else {
                        $pfx   = jrCore_db_get_prefix($_rt['invite_module']);
                        $title = $_it["{$pfx}_title"];
                        $murl  = jrCore_get_module_url($_rt['invite_module']);
                        $url   = "{$_conf['jrCore_base_url']}/{$_it["profile_url"]}/{$murl}/{$_it['_item_id']}/{$_it["{$pfx}_title_url"]}";
                    }
                    foreach ($_emails as $email) {
                        $email = trim($email);
                        if (jrCore_checktype($email, 'email')) {
                            $_replace = array(
                                'system_name' => $_conf['jrCore_system_name'],
                                'invitee'     => substr($email, 0, strpos($email, '@')),
                                'inviter'     => $_rt['user_name'],
                                'invite_text' => $_rt['invite_text'],
                                'item_title'  => $title,
                                'url'         => $url
                            );
                            list($sub, $msg) = jrCore_parse_email_templates('jrInvite', 'invite', $_replace);
                            jrCore_send_email($email, $sub, $msg, array('send_as_html' => true, 'low_priority' => true));
                            jrCore_form_modal_notice('update', "{$_lang['jrInvite'][38]} {$email}");
                            $ectr++;
                        }
                    }
                }
            }
        }

        if ($_user['quota_jrInvite_invitee_type'] == 1) {
            jrCore_form_modal_notice('complete', "{$fctr} {$_lang['jrInvite'][43]}");
        }
        elseif ($_user['quota_jrInvite_invitee_type'] == 2) {
            jrCore_form_modal_notice('complete', "{$ectr} {$_lang['jrInvite'][45]}");
        }
        elseif ($_user['quota_jrInvite_invitee_type'] == 3) {
            jrCore_form_modal_notice('complete', "{$fctr} {$_lang['jrInvite'][43]}&nbsp;&nbsp;&nbsp;&nbsp;{$ectr} {$_lang['jrInvite'][45]}");
        }
        jrCore_form_delete_session();
        exit;
    }
    jrCore_form_modal_notice('complete', "ERROR: The invitation entry was not found in the datastore - please try again.");
    exit;
}

//------------------------------
// view
//------------------------------
function view_jrInvite_view($_post, $_user, $_conf)
{
    // $_post['_1'] is the invite ID
    // $_post['_2'] is the invited user ID

    jrUser_session_require_login();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid IDs - please pass in a valid invite and user IDs.');
    }
    // Get the invite
    $_rt = jrCore_db_get_item('jrInvite', $_post['_1']);
    if (!$_rt || !is_array($_rt)) {
        jrCore_notice_page('error', 'The invitation entry was not found in the datastore - please try again.');
    }
    // Check this this user is the inviter or an invitee
    $tbl = jrCore_db_table_name('jrInvite', 'invitee');
    $req = "SELECT * FROM {$tbl} WHERE `invitee_invite_id` = '{$_post['_1']}' AND `invitee_user_id` = '{$_user['_user_id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($_rt['_user_id'] == $_user['_user_id'] || jrCore_checktype($cnt, 'number_nz')) {

        // Get the item invited to
        $_it = jrCore_db_get_item($_rt['invite_module'], $_rt['invite_item_id']);
        if ($_it && is_array($_it)) {

            // Log this invite as 'viewed'
            $tbl = jrCore_db_table_name('jrInvite', 'invitee');
            $req = "UPDATE {$tbl} SET `invitee_viewed` = 1 WHERE `invitee_invite_id` = '{$_post['_1']}' AND `invitee_user_id` = '{$_user['_user_id']}' LIMIT 1";
            jrCore_db_query($req);

            if ($_rt['invite_module'] == 'jrProfile') {
                // Redirect to Profile
                jrCore_location("{$_conf['jrCore_base_url']}/{$_it['profile_url']}");
            }

            // This is an item - redirect to item detail page
            $ttl = '';
            $url = jrCore_get_module_url($_rt['invite_module']);
            $pfx = jrCore_db_get_prefix($_rt['invite_module']);
            if ($pfx && isset($_it["{$pfx}_title"])) {
                $ttl = '/' . jrCore_url_string($_it["{$pfx}_title"]);
            }
            jrCore_location("{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$url}/{$_it['_item_id']}{$ttl}");

        }
        else {
            jrCore_notice_page('error', 'The item invited to was not found in the datastore - please try again.');
        }
    }
    else {
        jrCore_notice_page('error', 40);
    }
    jrCore_page_display();
}

//------------------------------
// create
//------------------------------
function view_jrInvite_create($_post, $_user, $_conf)
{
    // Do security
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrInvite');

    // Get language strings
    $_lang = jrUser_load_lang_strings();

    jrCore_page_banner(2);

    // Form init
    $_tmp = array(
        'submit_value' => 2,
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse"
    );
    jrCore_form_create($_tmp);

    // Invite Module
    $_ds           = jrCore_get_datastore_modules();
    $_user_modules = array(
        '-'         => '-',
        'jrProfile' => $_lang['jrInvite'][50]
    );
    foreach ($_ds as $k => $v) {
        if (jrCore_module_is_active($k) && isset($_user["quota_{$k}_allowed"]) && $_user["quota_{$k}_allowed"] == 'on' && is_file(APP_DIR . "/modules/{$k}/templates/item_detail.tpl") && isset($_user["profile_{$k}_item_count"]) && jrCore_checktype($_user["profile_{$k}_item_count"], 'number_nz')) {
            if ($k != 'jrAction' && $k != 'jrInvite' && $k != 'jrComment') {
                $_user_modules[$k] = (isset($_lang[$k]['menu'])) ? ucwords($_lang[$k]['menu']) : ucwords($v);
            }
        }
    }
    natcasesort($_user_modules);
    $_tmp = array(
        'name'     => 'invite_module',
        'label'    => 16,
        'help'     => 17,
        'type'     => 'select',
        'options'  => $_user_modules,
        'validate' => 'printable',
        'required' => true,
        'onchange' => "var v=$(this).val(); if (v != '-') { jrInvite_load(v); $('#invite_item_id').removeClass('form_element_disabled').removeAttr('disabled') } else { $('#invite_item_id').addClass('form_element_disabled').attr('disabled','disabled') }"
    );
    jrCore_form_field_create($_tmp);

    // Invite Item
    $_tmp = array(
        'name'     => 'invite_item_id',
        'label'    => 18,
        'help'     => 19,
        'type'     => 'select',
        'required' => true,
        'class'    => 'form_element_disabled',
        'disabled' => 'disabled'

    );
    jrCore_form_field_create($_tmp);

    // Invite Text
    $_tmp = array(
        'name'     => 'invite_text',
        'label'    => 14,
        'help'     => 15,
        'type'     => 'textarea',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrInvite_create_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrInvite');

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('jrInvite', 'create', $_post);

    // Check that a valid module is selected
    if (!jrCore_module_is_active($_rt['invite_module'])) {
        jrCore_set_form_notice('error', 35);
        jrCore_form_result();
    }

    // $xid will be the INSERT_ID (_item_id) of the created item
    $xid = jrCore_db_create_item('jrInvite', $_rt);
    if (!$xid) {
        jrCore_set_form_notice('error', 'An error was encountered creating the invitation - please try again.');
        jrCore_form_result();
    }

    // Save any uploaded media files added in by our
    jrCore_save_all_media_files('jrInvite', 'create', $_user['user_active_profile_id'], $xid);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse");
}

//------------------------------
// update
//------------------------------
function view_jrInvite_update($_post, $_user, $_conf)
{
    // Do security
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrInvite');

    // Get language strings
    $_lang = jrUser_load_lang_strings();

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid IDs - please pass in a valid invite and user IDs.');
    }
    $_rt = jrCore_db_get_item('jrInvite', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', 'The invitation entry was not found in the datastore - please try again.');
    }
    if ($_rt['invite_invitee'] && is_array($_rt['invite_invitee']) && count($_rt['invite_invitee']) > 0) {
        jrCore_notice_page('error', 'Invitation already sent - Cannot update');
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Start output
    jrCore_page_banner(8);

    // Get invitees
    if (isset($_rt['invite_invitees']{0})) {
        $_invitees = json_decode($_rt['invite_invitees'], true);
        if (isset($_invitees) && is_array($_invitees) && count($_invitees) > 0) {
            jrCore_page_notice('warning', $_lang['jrInvite'][28]);
        }
    }

    // Form init
    $_tmp = array(
        'submit_value' => 9,
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse",
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // id
    $_tmp = array(
        'name'     => 'id',
        'type'     => 'hidden',
        'value'    => $_post['id'],
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    // Invite Module
    $_ds           = jrCore_get_datastore_modules();
    $_user_modules = array(
        '-'         => '-',
        'jrProfile' => $_lang['jrInvite'][50]
    );
    foreach ($_ds as $k => $v) {
        if (jrCore_module_is_active($k) && isset($_user["quota_{$k}_allowed"]) && $_user["quota_{$k}_allowed"] == 'on' && is_file(APP_DIR . "/modules/{$k}/templates/item_detail.tpl") && isset($_user["profile_{$k}_item_count"]) && $_user["profile_{$k}_item_count"] > 0) {
            if ($k != 'jrAction' && $k != 'jrInvite') {
                $_user_modules[$k] = (isset($_lang[$k]['menu'])) ? ucwords($_lang[$k]['menu']) : ucwords($v);
            }
        }
    }
    natcasesort($_user_modules);
    $_tmp = array(
        'name'     => 'invite_module',
        'label'    => 16,
        'help'     => 17,
        'type'     => 'select',
        'options'  => $_user_modules,
        'validate' => 'printable',
        'required' => true,
        'onchange' => "var v=jrE(this.options[this.selectedIndex].value); jrInvite_load(v);",
    );
    jrCore_form_field_create($_tmp);

    // Invite Item
    if ($_rt['invite_module'] == 'jrProfile') {
        $_opts = array(
            $_user['user_active_profile_id'] => $_user['profile_name']
        );
    }
    else {
        $pfx   = jrCore_db_get_prefix($_rt['invite_module']);
        $_s    = array(
            "search"        => array(
                "_profile_id = {$_user['user_active_profile_id']}"
            ),
            "order_by"      => array(
                "{$pfx}_title" => "ASC"
            ),
            "skip_triggers" => true
        );
        $_it   = jrCore_db_search_items($_rt['invite_module'], $_s);
        $_opts = array();
        if (isset($_it['_items']) && is_array($_it['_items'])) {
            foreach ($_it['_items'] as $it) {
                $_opts["{$it['_item_id']}"] = $it["{$pfx}_title"];
            }
        }
    }
    $_tmp = array(
        'name'     => 'invite_item_id',
        'label'    => 18,
        'help'     => 19,
        'type'     => 'select',
        'options'  => $_opts,
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Invite Text
    $_tmp = array(
        'name'     => 'invite_text',
        'label'    => 14,
        'help'     => 15,
        'type'     => 'textarea',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrInvite_update_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();

    // Validate all incoming posted data
    jrCore_form_validate($_post);

    // Do security
    jrUser_check_quota_access('jrInvite');

    // Check that a valid module is selected
    if (!jrCore_module_is_active($_post['invite_module'])) {
        jrCore_set_form_notice('error', 35);
        jrCore_form_result();
    }

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid ID - please pass in a valid invite ID.');
        jrCore_form_result('referrer');
    }

    // Get data
    $_rt = jrCore_db_get_item('jrInvite', $_post['id']);
    if (!is_array($_rt)) {
        // Item does not exist....
        jrCore_notice_page('error', 'The invitation entry was not found in the datastore - please try again.');
        jrCore_form_result('referrer');
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrInvite', 'update', $_post);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrInvite', $_post['id'], $_sv);

    // Save any uploaded media file
    jrCore_save_all_media_files('jrInvite', 'update', $_user['user_active_profile_id'], $_post['id']);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    // redirect to the invite browse view
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse");
}

//------------------------------
// delete
//------------------------------
function view_jrInvite_delete($_post, $_user, $_conf)
{
    // Do security
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrInvite');

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid ID - please pass in a valid invite ID.');
        jrCore_form_result('referrer');
    }
    $_rt = jrCore_db_get_item('jrInvite', $_post['id']);

    // Make sure the calling user has permission to delete this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    // Delete item and any associated files
    jrCore_db_delete_item('jrInvite', $_post['id']);
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse");
}

//------------------------------
// load
//------------------------------
function view_jrInvite_load($_post, $_user, $_conf)
{
    // Do security
    jrUser_session_require_login();
    jrUser_check_quota_access('jrInvite');

    if (!isset($_post['_1']) || !jrCore_module_is_active($_post['_1'])) {
        $_rs = array('error' => 'invalid module name');
        jrCore_json_response($_rs);
    }
    if ($_post['_1'] == 'jrProfile') {
        $_out = array(
            'ok'    => 1,
            'value' => array($_user['user_active_profile_id'] => $_user['profile_name'])
        );
        jrCore_json_response($_out);
    }
    else {
        $pfx = jrCore_db_get_prefix($_post['_1']);
        $_s  = array(
            "search"        => array(
                "_profile_id = {$_user['user_active_profile_id']}"
            ),
            "order_by"      => array(
                "{$pfx}_title" => 'ASC'
            ),
            "skip_triggers" => true,
            "limit"         => 25000
        );
        $_rt = jrCore_db_search_items($_post['_1'], $_s);
        if (is_array($_rt['_items'])) {
            $_out = array();
            foreach ($_rt['_items'] as $rt) {
                $_out["{$rt['_item_id']}"] = $rt["{$pfx}_title"];
            }
            $_out = array(
                'ok'    => 1,
                'value' => $_out
            );
            jrCore_json_response($_out);
        }
        $_out = array('error' => 'no_data');
        jrCore_json_response($_out);
    }
}
