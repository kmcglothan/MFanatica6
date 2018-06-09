<?php
/**
 * Jamroom Simple Custom Forms module
 *
 * copyright 2018 The Jamroom Network
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//------------------------------
// default
//------------------------------
function view_jrCustomForm_default($_post, $_user, $_conf)
{
    if (empty($_post['option'])) {
        jrCore_notice_page('error', 'no form was specified - please specify the form to display');
    }

    // See if we are SAVING or DISPLAYING a form
    //------------------------------
    // FORM SAVE
    //------------------------------
    if (isset($_post['jr_html_form_token']{31}) && strpos($_post['option'], '_save')) {

        // We're handling a form submission.

        // Save data to datastore
        $nam = str_replace('_save', '', $_post['option']);

        // make sure this is a good form
        $tbl = jrCore_db_table_name('jrCustomForm', 'form');
        $req = "SELECT * FROM {$tbl} WHERE form_name = '" . jrCore_db_escape($nam) . "' LIMIT 1";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if (!$_rt || !is_array($_rt)) {
            if (jrCore_is_ajax_request()) {
                jrCore_set_form_notice('error', 'invalid form');
                jrCore_form_result();
            }
            else {
                jrCore_notice_page('error', 'Invalid form');
            }
        }
        if (isset($_rt['form_login']) && $_rt['form_login'] == 'on') {
            jrUser_session_require_login();
        }

        // re-validate server side to prevent bot submission
        $_fm                = jrCore_form_get_session($_post['jr_html_form_token']);
        $_fm['form_fields'] = jrCore_get_designer_form_fields('jrCustomForm', $nam);
        jrCore_set_flag("jrcore_form_get_session_{$_post['jr_html_form_token']}", $_fm);
        jrCore_form_validate($_post);

        // If this is a request from an EMBEDDED form, we have to handle
        if (jrCore_is_ajax_request()) {

            // We have to validate if we are human if NOT logged in
            if (!jrUser_is_logged_in()) {
                $pass = false;
                foreach ($_post as $k => $v) {
                    if (strpos($k, 'form_is_human') === 0) {
                        $pass = true;
                        break;
                    }
                }
                if (!$pass) {
                    $_ln = jrUser_load_lang_strings();
                    jrCore_set_form_notice('error', $_ln['jrUser'][92]);
                    jrCore_form_result();
                }
            }

            $_sv = array();
            foreach ($_fm['form_fields'] as $k => $v) {
                if (isset($_post[$k])) {
                    $_sv[$k] = $_post[$k];
                }
            }
        }
        else {
            $_sv = jrCore_form_get_save_data('jrCustomForm', $nam, $_post);
        }

        $_sv['form_name']    = $nam;
        $_sv['form_user_ip'] = jrCore_get_ip();

        // Check for banned items
        if (jrCore_run_module_function('jrBanned_is_banned', 'ip', $_sv['form_user_ip'])) {
            // This is a banned IP address
            jrCore_set_form_notice('error', 3);
            jrCore_form_result();
        }
        foreach ($_sv as $k => $v) {
            if (strpos($k, '_email')) {
                if (jrCore_run_module_function('jrBanned_is_banned', 'email', $v)) {
                    // This is a banned Email address
                    jrCore_set_form_notice('error', 3);
                    jrCore_form_result();
                }
            }
        }

        if (jrUser_is_logged_in()) {

            // Check for form unique
            if (isset($_rt['form_unique']) && $_rt['form_unique'] == 'on') {
                // Make sure they have not submitted before
                $_sp = array(
                    'search'        => array(
                        "_user_id = {$_user['_user_id']}",
                        "form_name = {$nam}"
                    ),
                    'return_count'  => true,
                    'skip_triggers' => true,
                    'privacy_check' => false // disable privacy check
                );
                $cnt = jrCore_db_search_items('jrCustomForm', $_sp);
                if ($cnt && $cnt > 0) {
                    if (jrCore_is_ajax_request()) {
                        jrCore_set_form_notice('error', 5);
                        jrCore_form_result();
                    }
                    else {
                        jrCore_notice_page('error', 5);
                    }
                }
            }
            $_cr = null;
        }
        else {
            // User is NOT logged in
            $_cr = array(
                '_user_id'    => 0,
                '_profile_id' => 0
            );
        }
        $fid = jrCore_db_create_item('jrCustomForm', $_sv, $_cr);
        if ($fid && jrCore_checktype($fid, 'number_nz')) {

            // Save any uploaded media files added in by our
            if (jrUser_is_logged_in()) {
                jrCore_save_all_media_files('jrCustomForm', $nam, $_user['user_active_profile_id'], $fid);
            }

            // Update response count
            $req = "UPDATE {$tbl} SET form_responses = (form_responses + 1) WHERE form_id = '{$_rt['form_id']}' LIMIT 1";
            jrCore_db_query($req);

            // Check for notifications
            switch ($_rt['form_notify']) {

                case 'master_email':
                case 'admin_email':
                    $_sp = array(
                        'search'        => array(
                            "user_group = master",
                        ),
                        'order_by'      => array(
                            'user_name' => 'desc'
                        ),
                        'limit'         => 100,
                        'return_keys'   => array('_user_id'),
                        'skip_triggers' => true,
                        'privacy_check' => false // disable privacy check
                    );
                    if ($_rt['form_notify'] == 'admin_email') {
                        $_sp['search'][0] = 'user_group IN master,admin';
                    }
                    $_us = jrCore_db_search_items('jrUser', $_sp);
                    if (isset($_us) && isset($_us['_items']) && is_array($_us['_items'])) {
                        foreach ($_sv as $k => $v) {
                            if ((strpos($k, '_created') || strpos($k, '_updated')) && is_numeric($v)) {
                                $_sv[$k] = jrCore_format_time($v);
                            }
                        }
                        $_rp = array(
                            'system_name'      => $_conf['jrCore_system_name'],
                            'form_browser_url' => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browser_item_update/id={$fid}",
                            'user_ip'          => $_sv['form_user_ip'],
                            '_save'            => $_sv
                        );
                        $_rp = array_merge($_rt, $_rp);
                        list($sub, $msg) = jrCore_parse_email_templates('jrCustomForm', 'form_received', $_rp);
                        foreach ($_us['_items'] as $_v) {
                            jrUser_notify($_v['_user_id'], 0, 'jrCustomForm', 'form_response', $sub, $msg);
                        }
                    }
                    break;
            }

            jrCore_form_delete_session();
            if (jrCore_is_ajax_request()) {
                jrCore_set_form_notice('success', 2);
                jrCore_form_result();
            }
            else {
                jrCore_notice_page('success', 2, $_conf['jrCore_base_url'], 4);
            }
        }
        else {
            if (jrCore_is_ajax_request()) {
                jrCore_set_form_notice('error', 3);
                jrCore_form_result();
            }
            else {
                jrCore_notice_page('error', 3);
            }
        }
    }

    //------------------------------
    // FORM DISPLAY
    //------------------------------
    else {

        // Check for form
        $tbl = jrCore_db_table_name('jrCustomForm', 'form');
        $req = "SELECT * FROM {$tbl} WHERE form_name = '" . jrCore_db_escape($_post['option']) . "' LIMIT 1";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if (!isset($_rt) || !is_array($_rt)) {
            jrCore_notice_page('error', 'Invalid form');
        }
        if (isset($_rt['form_login']) && $_rt['form_login'] == 'on') {
            jrUser_session_require_login();
        }
        if (jrUser_is_logged_in() && isset($_rt['form_unique']) && $_rt['form_unique'] == 'on') {
            // Make sure they have not submitted before
            $_sp = array(
                'search'        => array(
                    "form_user_id = {$_user['_user_id']}",
                    "form_name = {$_post['option']}"
                ),
                'return_count'  => true,
                'skip_triggers' => true,
                'privacy_check' => false // disable privacy check
            );
            $cnt = jrCore_db_search_items('jrCustomForm', $_sp);
            if (isset($cnt) && $cnt > 0) {
                jrCore_notice_page('error', 5);
            }
        }
        jrCore_register_module_feature('jrCore', 'designer_form', 'jrCustomForm', $_post['option']);
        $_lng = jrUser_load_lang_strings();

        jrCore_page_banner($_rt['form_title']);
        if (!empty($_rt['form_message'])) {
            jrCore_page_note($_rt['form_message']);
        }

        // Form init
        $_tmp = array(
            'submit_value'     => 1,
            'cancel'           => jrCore_is_profile_referrer(),
            'form_ajax_submit' => false
        );
        jrCore_form_create($_tmp);

        $_fields = jrCore_get_designer_form_fields('jrCustomForm', $_post['option']);
        if (!$_fields || !is_array($_fields)) {
            jrCore_notice_page('error', 'This form has not been setup yet');
        }
        foreach ($_fields as $_tmp) {
            // If we have any file based form types, user must be logged in
            switch ($_tmp['type']) {
                case 'file':
                case 'audio':
                case 'video':
                case 'image':
                    if (jrUser_is_logged_in()) {
                        jrCore_form_field_create($_tmp);
                    }
                    break;
                default:
                    jrCore_form_field_create($_tmp);
                    break;
            }
        }

        // See if we need to add a Spam Bot Checkbox
        $tmp = jrCore_get_flag('jrcore_form_field_checkbox_spambot');
        if (!$tmp && !jrUser_is_logged_in()) {
            // Spam Bot Check
            $_tmp = array(
                'name'          => 'form_is_human',
                'label'         => $_lng['jrUser'][90],
                'help'          => $_lng['jrUser'][91],
                'type'          => 'checkbox_spambot',
                'error_msg'     => $_lng['jrUser'][92],
                'validate'      => 'onoff',
                'form_designer' => false
            );
            jrCore_form_field_create($_tmp);
        }
        jrCore_page_display();
    }
}

//------------------------------
// browse
//------------------------------
function view_jrCustomForm_browse($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCustomForm', 'browse');

    $num = jrCore_db_number_rows('jrCustomForm', 'form');
    $tbl = jrCore_db_table_name('jrCustomForm', 'form');
    $req = "SELECT * FROM {$tbl} ORDER BY form_updated DESC";
    // find how many lines we are showing
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $_rt = jrCore_db_paged_query($req, $_post['p'], 12, 'NUMERIC', $num);

    $create = jrCore_page_button('form_create', 'create new form', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/create')");
    jrCore_page_banner('custom forms', $create);
    jrCore_get_form_notice();

    $dat             = array();
    $dat[1]['title'] = 'view form';
    $dat[1]['width'] = '5%;';
    $dat[2]['title'] = 'title';
    $dat[2]['width'] = '61%;';
    $dat[3]['title'] = 'responses';
    $dat[3]['width'] = '8%;';
    $dat[4]['title'] = 'login';
    $dat[4]['width'] = '8%;';
    $dat[5]['title'] = 'unique';
    $dat[5]['width'] = '8%;';
    $dat[6]['title'] = 'fields';
    $dat[6]['width'] = '5%;';
    $dat[7]['title'] = 'settings';
    $dat[7]['width'] = '5%;';
    $dat[8]['title'] = 'delete';
    $dat[8]['width'] = '5%;';
    jrCore_page_table_header($dat);

    if (isset($_rt['_items']) && is_array($_rt['_items'])) {
        foreach ($_rt['_items'] as $k => $_form) {
            $dat             = array();
            $dat[1]['title'] = jrCore_page_button("cf-viewform-{$k}", $_form['form_name'], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/{$_form['form_name']}')");
            $dat[1]['class'] = 'center form_name_button';
            $dat[2]['title'] = $_form['form_title'];
            if (isset($_form['form_responses']) && $_form['form_responses'] > 0) {
                $dat[3]['title'] = jrCore_page_button("c{$k}", jrCore_number_format($_form['form_responses']), "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/response_browser/id={$_form['form_id']}')");
            }
            else {
                $dat[3]['title'] = '0';
            }
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = $_form['form_login'];
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = $_form['form_unique'];
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = jrCore_page_button("cf-fields-{$k}", 'fields', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/form_designer/m=jrCustomForm/v={$_form['form_name']}')");
            $dat[7]['title'] = jrCore_page_button("cf-settings-{$k}", 'settings', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/update/id={$_form['form_id']}')");
            $dat[8]['title'] = jrCore_page_button("cf-delete-{$k}", 'delete', "if (confirm('Are you sure you want to delete this form?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_save/id={$_form['form_id']}')}");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = 'no custom forms have been craeated yet';
        $dat[1]['class'] = 'p20 center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    jrCore_page_display();
}

//------------------------------
// response_browser
//------------------------------
function view_jrCustomForm_response_browser($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCustomForm', 'browse');
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid form id');
        jrCore_location('referrer');
    }
    $fid = (int) $_post['id'];
    $tbl = jrCore_db_table_name('jrCustomForm', 'form');
    $req = "SELECT * FROM {$tbl} WHERE form_id = '{$fid}'";
    $_fm = jrCore_db_query($req, 'SINGLE');
    if (!$_fm || !is_array($_fm)) {
        jrCore_set_form_notice('error', 'invalid form id - data not found');
        jrCore_location('referrer');
    }

    $page = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $page = (int) $_post['p'];
    }

    $_sc = array(
        'search'                       => array(
            "form_name = {$_fm['form_name']}"
        ),
        'order_by'                     => array(
            '_item_id' => 'desc'
        ),
        'exclude_jrProfile_quota_keys' => true,
        'ignore_pending'               => true,
        'quota_check'                  => false,
        'no_cache'                     => true,
        'pagebreak'                    => 12,
        'page'                         => $page
    );
    $_sc = jrCore_db_search_items('jrCustomForm', $_sc);

    jrCore_page_banner('&quot;' . $_fm['form_title'] . '&quot; form responses');
    jrCore_get_form_notice();

    $dat             = array();
    $dat[1]['title'] = 'user';
    $dat[1]['width'] = '10%;';
    $dat[2]['title'] = 'response';
    $dat[2]['width'] = '85%;';
    $dat[3]['title'] = 'delete';
    $dat[3]['width'] = '5%;';
    jrCore_page_table_header($dat);

    if ($_sc && is_array($_sc) && isset($_sc['_items'])) {
        foreach ($_sc['_items'] as $k => $_r) {
            $dat = array();
            $_us = array();
            if (!isset($_r['_user_id']) || $_r['_user_id'] == 0) {
                if ($eml = jrCustomForm_get_email_from_response($_r)) {
                    $_r['user_email'] = $eml;
                }
            }
            $_im   = array(
                '_item'  => $_r,
                'crop'   => 'auto',
                'width'  => 96,
                'height' => 96,
                'alt'    => 'img',
                'title'  => 'img',
                '_v'     => (isset($_r['user_image_time']) && $_r['user_image_time'] > 0) ? $_r['user_image_time'] : false
            );
            $_us[] = jrImage_get_image_src('jrUser', 'user_image', $_r['_user_id'], 'icon', $_im);
            if (isset($_r['_user_id']) && jrCore_checktype($_r['_user_id'], 'number_nz')) {
                $_us[] = "<a href=\"{$_conf['jrCore_base_url']}/{$_r['profile_url']}\">@{$_r['profile_url']}</a>";
            }
            else {
                // Visitor...
                if (isset($_r['user_email'])) {
                    $_us[] = $_r['user_email'];
                }
            }
            $dat[1]['title'] = implode('<br>', $_us);
            $dat[1]['class'] = 'p10 center';
            $dat[2]['title'] = '<div class="ds_browser_item form_browser_item">' . jrCustomForm_format_response($_r) . '</div>';

            $_bt = array();
            if (isset($_r['user_email'])) {
                if (jrCore_module_is_active('jrTicket')) {
                    $turl  = jrCore_get_module_url('jrTicket');
                    $_bt[] = jrCore_page_button("response-response-{$k}", 'respond', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$turl}/create/email=" . urlencode($_r['user_email']) . "')");
                }
                else {
                    $_bt[] = "<a href=\"mailto:{$_r['user_email']}\" style=\"text-decoration:none\">" . jrCore_page_button("response-response-{$k}", 'respond', '') . '</a>';
                }
            }
            $_bt[]           = jrCore_page_button("response-delete-{$k}", 'remove', "if(confirm('delete this response?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/response_delete_save/id={$_r['_item_id']}') }");
            $dat[3]['title'] = implode('<br><br>', $_bt);
            $dat[3]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_sc);
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse");
    jrCore_page_display();
}

//------------------------------
// response_delete_save
//------------------------------
function view_jrCustomForm_response_delete_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid response id');
        jrCore_location('referrer');
    }
    $_rs = jrCore_db_get_item('jrCustomForm', $_post['id'], true);
    if (!$_rs || !is_array($_rs)) {
        jrCore_set_form_notice('error', 'invalid response id (not found)');
        jrCore_location('referrer');
    }
    if (jrCore_db_delete_item('jrCustomForm', $_post['id'])) {
        jrCore_set_form_notice('success', 'the response was successfully deleted');
    }
    else {
        jrCore_set_form_notice('error', 'an error was encountered deleting the form response');
    }
    jrCore_location('referrer');
}

//------------------------------
// create
//------------------------------
function view_jrCustomForm_create($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCustomForm', 'tools');

    jrCore_page_banner('create new custom form');

    // Form init
    $_tmp = array(
        'submit_value' => 'create form',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse"
    );
    jrCore_form_create($_tmp);

    // Form Name
    $_tmp = array(
        'name'     => 'form_name',
        'label'    => 'form name',
        'help'     => 'Enter a unique form name for this new form. It should be lowercase and consist of letters and underscores only.',
        'type'     => 'text',
        'validate' => 'core_string',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Form Title
    $_tmp = array(
        'name'     => 'form_title',
        'label'    => 'form title',
        'help'     => 'Enter a title for this form - it will be used in the form header as well as the page title.',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Form Message
    $_tmp = array(
        'name'     => 'form_message',
        'label'    => 'form message',
        'help'     => 'Enter an optional message for this form - it will appear above the form and can be used for form instructions, etc.',
        'type'     => 'textarea',
        'validate' => 'allowed_html',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Form Login
    $_tmp = array(
        'name'     => 'form_login',
        'label'    => 'form login',
        'help'     => 'Check this option to only allow logged in users to view this form.',
        'type'     => 'checkbox',
        'default'  => 'off',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Form Unique
    $_tmp = array(
        'name'     => 'form_unique',
        'label'    => 'form unique',
        'help'     => 'Check this option to ensure that a user can only fill out this form one time (only works if <b>form login</b> is checked too)',
        'type'     => 'checkbox',
        'default'  => 'off',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Form Notification
    $_sel = array(
        'master_email' => 'Send email to Master Admins',
        'admin_email'  => 'Send email to Admin Users',
        'none'         => 'Store in DataStore only'
    );
    $_tmp = array(
        'name'     => 'form_notify',
        'label'    => 'form notify',
        'help'     => 'Select the type of Notification you would like to be sent out on a successful form submission',
        'type'     => 'select',
        'default'  => 'master_email',
        'options'  => $_sel,
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrCustomForm_create_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Make sure this new form is unique
    $tbl = jrCore_db_table_name('jrCustomForm', 'form');
    $req = "SELECT * FROM {$tbl} WHERE form_name = '" . jrCore_db_escape($_post['form_name']) . "' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (isset($_rt) && is_array($_rt)) {
        jrCore_set_form_notice('error', 'There is already a form using that form name - please enter another');
        jrCore_form_field_hilight('form_name');
        jrCore_form_result();
    }
    $nam = jrCore_db_escape($_post['form_name']);
    $ttl = jrCore_db_escape($_post['form_title']);
    $msg = jrCore_db_escape($_post['form_message']);
    $req = "INSERT INTO {$tbl} (form_created,form_updated,form_name,form_title,form_message,form_unique,form_login)
            VALUES (UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),'{$nam}','{$ttl}','{$msg}','{$_post['form_unique']}','{$_post['form_login']}')";
    $fid = jrCore_db_query($req, 'INSERT_ID');
    if ($fid && jrCore_checktype($fid, 'number_nz')) {
        // Create our single default field
        $_field = array(
            'name'     => 'form_content',
            'type'     => 'textarea',
            'label'    => 'Content',
            'help'     => 'change this',
            'validate' => 'printable',
            'locked'   => 0,
            'required' => true
        );
        jrCore_verify_designer_form_field('jrCustomForm', $_post['form_name'], $_field);

        // Activate it or it won't show
        $tbl = jrCore_db_table_name('jrCore', 'form');
        $req = "UPDATE {$tbl} SET `active` = 1 WHERE `module` = 'jrCustomForm' AND `view` = '" . jrCore_db_escape($_post['form_name']) . "' AND `name` = 'form_content' LIMIT 1";
        jrCore_db_query($req);

        // Redirect to form designer
        jrCore_register_module_feature('jrCore', 'designer_form', 'jrCustomForm', $_post['form_name']);
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/form_designer/m=jrCustomForm/v=" . $_post['form_name']);
    }
    jrCore_set_form_notice('error', 'An error was encountered creating the new form - please try again');
    jrCore_form_result();
}

//------------------------------
// update
//------------------------------
function view_jrCustomForm_update($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCustomForm', 'tools');

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid form id');
        jrCore_form_result('referrer');
    }
    $tbl = jrCore_db_table_name('jrCustomForm', 'form');
    $req = "SELECT * FROM {$tbl} WHERE form_id = '{$_post['id']}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid form id');
        jrCore_form_result('referrer');
    }

    jrCore_page_banner('custom form settings');

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse",
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // Form ID
    $_tmp = array(
        'name'  => 'id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    // Form Name
    $_tmp = array(
        'name'     => 'form_name',
        'label'    => 'form name',
        'help'     => 'Enter a unique form name for this new form. It should be lowercase and consist of letters and underscores only.',
        'type'     => 'text',
        'validate' => 'core_string',
        'required' => true
    );
    if ($_rt['form_responses'] > 0) {
        $_tmp['readonly'] = 'readonly';
    }
    jrCore_form_field_create($_tmp);

    // Form Title
    $_tmp = array(
        'name'     => 'form_title',
        'label'    => 'form title',
        'help'     => 'Enter a title for this form - it will be used in the form header as well as the page title.',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Form Message
    $_tmp = array(
        'name'     => 'form_message',
        'label'    => 'form message',
        'help'     => 'Enter an optional message for this form - it will appear above the form and can be used for form instructions, etc.',
        'type'     => 'textarea',
        'validate' => 'allowed_html',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Form Login
    $_tmp = array(
        'name'     => 'form_login',
        'label'    => 'form login',
        'help'     => 'Check this option to only allow logged in users to view this form.',
        'type'     => 'checkbox',
        'default'  => 'off',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Form Unique
    $_tmp = array(
        'name'     => 'form_unique',
        'label'    => 'form unique',
        'help'     => 'Check this option to ensure that a user can only fill out this form one time (only works if <b>form login</b> is checked too)',
        'type'     => 'checkbox',
        'default'  => 'off',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Form Notification
    $_sel = array(
        'master_email' => 'Send email to Master Admins',
        'admin_email'  => 'Send email to Admin Users',
        'none'         => 'Save the Form Response only - no notification'
    );
    $_tmp = array(
        'name'     => 'form_notify',
        'label'    => 'form notify',
        'help'     => 'Select the type of Notification you would like to be sent out on a successful form submission',
        'type'     => 'select',
        'default'  => 'master_email',
        'options'  => $_sel,
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrCustomForm_update_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Update
    $tbl = jrCore_db_table_name('jrCustomForm', 'form');
    $nam = jrCore_db_escape($_post['form_name']);
    $ttl = jrCore_db_escape($_post['form_title']);
    $msg = jrCore_db_escape($_post['form_message']);
    $req = "UPDATE {$tbl} SET
              form_updated = UNIX_TIMESTAMP(),
              form_name    = '{$nam}',
              form_title   = '{$ttl}',
              form_message = '{$msg}',
              form_unique  = '{$_post['form_unique']}',
              form_login   = '{$_post['form_login']}',
              form_notify  = '{$_post['form_notify']}'
            WHERE form_id = '{$_post['id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {
        jrCore_set_form_notice('success', 'The changes were successfully saved');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered updating the form - please try again');
    }
    jrCore_form_result();
}

//------------------------------
// delete_save
//------------------------------
function view_jrCustomForm_delete_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid form id');
        jrCore_form_result('referrer');
    }
    $tbl = jrCore_db_table_name('jrCustomForm', 'form');
    $req = "SELECT * FROM {$tbl} WHERE form_id = '{$_post['id']}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid form id');
        jrCore_form_result('referrer');
    }
    // Delete it
    $req = "DELETE FROM {$tbl} WHERE form_id = '" . jrCore_db_escape($_post['id']) . "' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {

        // Delete any form designer fields for this
        $tbl = jrCore_db_table_name('jrCore', 'form');
        $req = "DELETE FROM {$tbl} WHERE `module` = 'jrCustomForm' AND `view` = '" . jrCore_db_escape($_rt['form_name']) . "'";
        jrCore_db_query($req);

        jrCore_set_form_notice('success', 'The form was successfully deleted');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered deleting the form - please try again');
    }
    jrCore_form_result('referrer');
}
