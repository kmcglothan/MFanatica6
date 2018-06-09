<?php
/**
 * Jamroom 5 MailChimp User Sync module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
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
// sync
//------------------------------
function view_jrMailChimp_sync($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrMailChimp');
    jrCore_page_banner('synchronize users');
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value'  => 'sync user accounts',
        'cancel'        => 'referrer',
        'submit_prompt' => 'Are you sure you want to synchronize user accounts?  Please be patient while the accounts are linked',
        'submit_modal'  => 'update',
        'modal_width'   => 600,
        'modal_height'  => 400,
        'modal_note'    => 'Please be patient while the Accounts are synced'
    );
    jrCore_form_create($_tmp);

    // Prune
    $_tmp = array(
        'name'     => 'prune',
        'label'    => 'remove unsubscribed',
        'help'     => 'If this option is checked, users that have unsubscribed from User Notifications will be removed from the linked MailChimp list.',
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff',
        'section'  => 'list options'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// sync_save
//------------------------------
function view_jrMailChimp_sync_save($_post, $_user, $_conf)
{
    @ini_set('memory_limit', '512M');
    jrUser_master_only();
    jrCore_form_validate($_post);
    jrCore_form_modal_notice('update', 'synchronizing user accounts with configured list');

    // Bring in class
    require_once APP_DIR . '/modules/jrMailChimp/contrib/mailchimp-api/MailChimp.php';
    $mcm = new \Drewm\MailChimp($_conf['jrMailChimp_api_key']);

    // Get all user accounts
    $dis = 0;
    $tot = 0;
    $uid = 0;
    while (true) {
        $_rt = array(
            'search'        => array(
                "_item_id > {$uid}"
            ),
            'skip_triggers' => true,
            'privacy_check' => false,
            'limit'         => 100
        );
        $_rt = jrCore_db_search_items('jrUser', $_rt);
        if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
            $_us = array();
            $_ds = array();
            foreach ($_rt['_items'] as $k => $_u) {
                if ((isset($_u['user_notifications_disabled']) && $_u['user_notifications_disabled'] == 'on') || (isset($_u['user_unsubscribed']) && $_u['user_unsubscribed'] == 'on') || (isset($_u['user_jrNewsLetter_newsletter_notifications']) && $_u['user_jrNewsLetter_newsletter_notifications'] == 'off')) {
                    // User has notifications disabled
                    $_ds[] = array('email' => array('email' => $_u['user_email']));
                }
                else {
                    if (jrCore_checktype($_u['user_email'], 'email')) {
                        $_us[$k] = array('email' => array('email' => $_u['user_email']));
                        if ($_mt = jrMailChimp_get_user_merge_vars($_u)) {
                            $_us[$k]['merge_vars'] = $_mt;
                        }
                    }
                    else {
                        jrCore_form_modal_notice('update', "user_id {$_u['_user_id']} has an invalid email address: {$_u['user_email']}");
                    }
                    $uid = $_u['_user_id'];
                }
            }
            if (count($_us) > 0) {
                $_tm = array(
                    'id'                => $_conf['jrMailChimp_list_id'],
                    'batch'             => $_us,
                    'double_optin'      => false,
                    'update_existing'   => true,
                    'replace_interests' => false,
                    'send_welcome'      => false
                );
                $res = $mcm->call('lists/batch-subscribe', $_tm);
                if (isset($res['status']) && $res['status'] == 'error') {
                    jrCore_logger('CRI', "error returned from MailChimp list/batch-subscribe", $res);
                    jrCore_form_modal_notice('error', 'An error was encountered communicating with MailChimp - check Activity Log');
                    jrCore_form_modal_notice('complete', 'Errors were encountered running list/batch-subscribe');
                    exit;
                }
                $tot += count($_us);
                jrCore_form_modal_notice('update', "synchronized {$tot} user accounts for subscription");
            }
            if (isset($_post['prune']) && $_post['prune'] == 'on' && count($_ds) > 0) {
                $_tm = array(
                    'id'            => $_conf['jrMailChimp_list_id'],
                    'batch'         => $_ds,
                    'delete_member' => true,
                    'send_goodbye'  => false,
                    'send_notify'   => false
                );
                $res = $mcm->call('lists/batch-unsubscribe', $_tm);
                if (isset($res['status']) && $res['status'] == 'error') {
                    jrCore_logger('CRI', "error returned from MailChimp list/batch-unsubscribe", $res);
                    jrCore_form_modal_notice('error', 'An error was encountered communicating with MailChimp - check Activity Log');
                    jrCore_form_modal_notice('complete', 'Errors were encountered running list/batch-unsubscribe');
                    exit;
                }
                $dis += count($_ds);
                jrCore_form_modal_notice('update', "unsubscribed {$dis} user accounts - notifications disabled");
            }
        }
        else {
            // No more accounts
            break;
        }
    }
    jrCore_form_delete_session();
    jrCore_form_modal_notice('complete', 'The User Accounts have been successfully sunchronized');
    exit;
}

//------------------------------
// list_fields
//------------------------------
function view_jrMailChimp_list_fields($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrMailChimp', 'list_fields');
    jrCore_page_banner('list fields and merge tags');
    jrCore_get_form_notice();

    // Get custom form fields for User Accounts module
    $_fl = jrCore_get_designer_form_fields('jrUser');
    if ($_fl && is_array($_fl)) {
        foreach ($_fl as $name => $_inf) {
            switch ($_inf['type']) {
                case 'password':
                case 'image':
                case 'file':
                case 'audio':
                case 'video':
                case 'checkbox_spambot':
                    unset($_fl[$name]);
                    break;
            }
        }
    }
    $_ln = jrUser_load_lang_strings();
    $_mt = jrMailChimp_get_merge_tags();

    $dat             = array();
    $dat[1]['title'] = 'field title';
    $dat[1]['width'] = '30%';
    $dat[2]['title'] = 'key name';
    $dat[2]['width'] = '30%';
    $dat[3]['title'] = 'linked merge tag';
    $dat[3]['width'] = '30%';
    $dat[4]['title'] = 'modify';
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = 'delete';
    $dat[5]['width'] = '5%';
    jrCore_page_table_header($dat);

    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $pagebreak = 12;
    if (isset($_COOKIE['jrcore_pager_rows']) && jrCore_checktype($_COOKIE['jrcore_pager_rows'], 'number_nz')) {
        $pagebreak = (int) $_COOKIE['jrcore_pager_rows'];
    }
    $_sc = array(
        'order_by'       => array(
            '_item_id' => 'numerical_desc'
        ),
        'skip_triggers'  => true,
        'privacy_check'  => false,
        'ignore_pending' => true,
        'page'           => (int) $_post['p'],
        'no_cache'       => true,
        'pagebreak'      => $pagebreak
    );

    $_rt = jrCore_db_search_items('jrMailChimp', $_sc);
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

        foreach ($_rt['_items'] as $k => $_tag) {
            $lbl             = $_fl["{$_tag['tag_key']}"]['label'];
            $dat             = array();
            $dat[1]['title'] = $_ln['jrUser'][$lbl];
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $_tag['tag_key'];
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $_tag['tag_merge'];
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = jrCore_page_button("d{$k}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/update/id={$_tag['_item_id']}')");
            $dat[5]['title'] = jrCore_page_button("m{$k}", 'delete', "if(confirm('Are you sure you want to delete this Merge Tag?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_save/id={$_tag['_item_id']}') }");
            jrCore_page_table_row($dat);
            unset($_fl["{$_tag['tag_key']}"], $_mt["{$_tag['tag_merge']}"]);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = "<p>no MailChimp Merge Tags have been created yet - create one below.</p>";
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    if (count($_fl) > 0) {
        $_tmp = array(
            'submit_value'     => 'create new merge tag',
            'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
            'form_ajax_submit' => false
        );
        jrCore_form_create($_tmp);

        // Field
        $_opt = array();
        foreach ($_fl as $name => $_inf) {
            if (strpos($name, 'user_') === 0 && $name != 'user_email') {
                if (isset($_ln['jrUser']["{$_inf['label']}"])) {
                    if (!strpos($_ln['jrUser']["{$_inf['label']}"], 'change this')) {
                        $_opt[$name] = ucfirst($_ln['jrUser']["{$_inf['label']}"]) . " ({$name})";
                    }
                    else {
                        $_opt[$name] = $name;
                    }
                }
                else {
                    $_opt[$name] = ucfirst($_inf['label']) . " ({$name})";
                }
            }
        }
        $_opt['_created'] = 'Account Created Date (_created)';
        natcasesort($_opt);
        $_tmp = array(
            'name'     => 'tag_key',
            'label'    => 'User Field',
            'help'     => "Select the existing User Field you want to link with a MailChimp Merge Tag",
            'type'     => 'select',
            'options'  => $_opt,
            'validate' => 'not_empty',
            'required' => true,
            'section'  => 'create a new merge tag'
        );
        jrCore_form_field_create($_tmp);

        // Tag
        $_tmp = array(
            'name'     => 'tag_merge',
            'label'    => 'Linked Merge Tag',
            'help'     => "Select the MailChimp Merge Tag.<br><br><b>Note:</b> These tags have been pre-created in the &quot;List fields and *|MERGE|* tags&quot; section in the List Settings for your list in the MailChimp control panel.  If you would like to add new Merge Tags, create them there first and they will be avialable for selecting here.",
            'type'     => 'select',
            'options'  => $_mt,
            'validate' => 'printable',
            'required' => true
        );
        jrCore_form_field_create($_tmp);
    }
    jrCore_page_display();
}

//------------------------------
// list_fields_save
//------------------------------
function view_jrMailChimp_list_fields_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Make sure it does not already exist
    $_ex = jrCore_db_get_item_by_key('jrMailChimp', 'tag_key', $_post['tag_key']);
    if ($_ex && is_array($_ex)) {
        jrCore_set_form_notice('error', 'The key you have selected is already mapped to a merge var - please select another');
        jrCore_form_result();
    }

    $_sv = jrCore_form_get_save_data('jrMailChimp', 'list_fields', $_post);
    $rid = jrCore_db_create_item('jrMailChimp', $_sv);
    if ($rid) {
        jrCore_form_delete_session();
        jrCore_set_form_notice('success', 'The new Merge Tag has been successfully created');
    }
    else {
        jrCore_set_form_notice('error', 'Unable to create new Merge Tag - please try again');
    }
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/list_fields");
}

//------------------------------
// update
//------------------------------
function view_jrMailChimp_update($_post, $_user, $_conf)
{
    jrUser_master_only();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid rule id - please try again');
        jrCore_location('referrer');
    }
    $_rt = jrCore_db_get_item('jrMailChimp', $_post['id'], true);
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid rule id - please try again');
        jrCore_location('referrer');
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrMailChimp', 'list_fields');
    jrCore_page_banner('Update MailChimp Merge Tag');
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value' => 'update rule',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/list_fields",
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // ID
    $_tmp = array(
        'name'  => 'id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    // Get custom form fields for User Accounts module
    $_fl = jrCore_get_designer_form_fields('jrUser');
    if ($_fl && is_array($_fl)) {
        foreach ($_fl as $name => $_inf) {
            switch ($_inf['type']) {
                case 'password':
                case 'image':
                case 'file':
                case 'audio':
                case 'video':
                case 'checkbox_spambot':
                    unset($_fl[$name]);
                    break;
            }
        }
    }
    $_ln = jrUser_load_lang_strings();

    // Field
    $_opt = array();
    foreach ($_fl as $name => $_inf) {
        if (strpos($name, 'user_') === 0 && $name != 'user_email') {
            if (isset($_ln['jrUser']["{$_inf['label']}"])) {
                if (!strpos($_ln['jrUser']["{$_inf['label']}"], 'change this')) {
                    $_opt[$name] = ucfirst($_ln['jrUser']["{$_inf['label']}"]) . " ({$name})";
                }
                else {
                    $_opt[$name] = $name;
                }
            }
            else {
                $_opt[$name] = ucfirst($_inf['label']) . " ({$name})";
            }
        }
    }
    $_opt['_created'] = 'Account Created Date (_created)';
    natcasesort($_opt);
    $_tmp = array(
        'name'     => 'tag_key',
        'label'    => 'User Field',
        'help'     => "Select the existing User Field you want to link with a MailChimp Merge Tag",
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'not_empty',
        'required' => true,
        'section'  => 'create a new merge tag'
    );
    jrCore_form_field_create($_tmp);

    // Tag
    $_tmp = array(
        'name'     => 'tag_merge',
        'label'    => 'Merge Tag',
        'help'     => 'Enter the MailChimp Merge Tag - this tag must already exist as part of the list',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrMailChimp_update_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid tag id - please try again');
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item('jrMailChimp', $_post['id'], true);
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid tag id - please try again');
        jrCore_form_result();
    }
    $_sv = jrCore_form_get_save_data('jrMailChimp', 'update', $_post);
    if (jrCore_db_update_item('jrMailChimp', $_post['id'], $_sv)) {
        jrCore_form_delete_session();
        jrCore_set_form_notice('success', 'The Merge Tag has been successfully updated');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered updating the Merge Tag - please try again');
    }
    jrCore_form_result();
}

//------------------------------
// delete
//------------------------------
function view_jrMailChimp_delete_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid tag id - please try again');
        jrCore_location('referrer');
    }
    if (jrCore_db_delete_item('jrMailChimp', $_post['id'])) {
        jrCore_set_form_notice('success', 'The Merge Tag has been successfully deleted');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered deleting the Merge Tag - please try again');
    }
    jrCore_location('referrer');
}
