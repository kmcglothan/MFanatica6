<?php
/**
 * Jamroom Beta Launch Page module
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
// signup_save
//------------------------------
function view_jrLaunch_signup_save($_post, &$_user, &$_conf)
{
    // users are not logged in here
    jrCore_validate_location_url();

    $_ln = jrUser_load_lang_strings();
    if (!isset($_post['launch_email_address']) || !jrCore_checktype($_post['launch_email_address'], 'email')) {
        $_res = array('error' => $_ln['jrLaunch'][3]);
        jrCore_json_response($_res);
    }
    $_rt = jrCore_db_get_item_by_key('jrLaunch', 'launch_email_address', $_post['launch_email_address']);
    if ($_rt && is_array($_rt)) {
        $_res = array('success' => $_ln['jrLaunch'][6]);
        jrCore_json_response($_res);
    }
    else {
        $uid = jrCore_db_create_item('jrLaunch', array('launch_email_address' => trim($_post['launch_email_address'])));
        if ($uid) {
            $_res = array('success' => $_ln['jrLaunch'][4]);
        }
        else {
            $_res = array('error' => $_ln['jrLaunch'][5]);
        }
        jrCore_json_response($_res);
    }
}

//------------------------------
// send_emails
//------------------------------
function view_jrLaunch_send_emails($_post, $_user, $_conf)
{
    jrUser_master_only();
    $cnt = jrCore_db_get_datastore_item_count('jrLaunch');
    if (jrCore_checktype($cnt, 'number_nz')) {

        jrCore_page_banner('Send Launch Email', "send to {$cnt} beta launch signups");

        // Form init
        $_tmp = array(
            'submit_value'  => 'Send',
            'cancel'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
            'submit_prompt' => 'Are you sure you want to send this email to the collected launch recipients?',
            'submit_modal'  => 'update',
            'modal_width'   => 600,
            'modal_height'  => 400,
            'modal_note'    => 'Please be patient while the emails are sent'
        );
        jrCore_form_create($_tmp);

        // Email Subject
        $_tmp = array(
            'name'     => 'email_subject',
            'label'    => 'Email Subject',
            'help'     => 'Enter the email subject',
            'type'     => 'text',
            'validate' => 'not_empty',
            'required' => true
        );
        jrCore_form_field_create($_tmp);

        // Email Body
        $_tmp = array(
            'name'     => 'email_body',
            'label'    => 'Email Body',
            'help'     => 'Enter the email body',
            'type'     => 'editor',
            'validate' => 'not_empty',
            'required' => true
        );
        jrCore_form_field_create($_tmp);
    }
    else {
        jrCore_page_banner("No launch emails have been collected as yet");
    }
    jrCore_page_display();
}

//------------------------------
// send_emails
//------------------------------
function view_jrLaunch_send_emails_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();

    if (isset($_post['email_body_editor_contents'])) {
        $_post['email_body'] = $_post['email_body_editor_contents'];
    }
    // Get all collected emails
    $tbl = jrCore_db_table_name('jrLaunch', 'item_key');
    $req = "SELECT `value` AS email FROM {$tbl} WHERE `key` = 'launch_email_address'";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        $_op = array('low_priority' => true);
        if (strip_tags($_post['email_body']) != $_post['email_body']) {
            // We have HTML in our message
            $_op = array('send_as_html' => true);
        }
        $sent = 0;
        $cnt  = count($_rt);
        $_tm  = array();
        foreach ($_rt as $rt) {
            $_tm[] = $rt['email'];
            $sent++;
            if ($sent >= 50 || $sent > $cnt) {
                jrCore_send_email($_tm, $_post['email_subject'], $_post['email_body'], $_op);
                jrCore_form_modal_notice('update', "{$sent} launch emails sent");
                $_tm = array();
                usleep(100000);
            }
        }
        jrCore_form_modal_notice('complete', "{$sent} launch emails successfully queued");
        jrCore_logger('INF', "{$sent} launch emails successfully queued");
        jrCore_form_delete_session();
        jrCore_set_form_notice('success', "Launch email sent to {$sent} recipients");
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/send_emails");
    }
    else {
        jrCore_form_modal_notice('complete', "No launch emails sent");
        jrCore_form_delete_session();
        jrCore_set_form_notice('error', "No launch emails found");
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/send_emails");
    }
}
