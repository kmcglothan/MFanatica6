<?php
/**
 * Jamroom Group Mailer module
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
// Compose
//------------------------------
function view_jrGroupMailer_compose($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGroupMailer');
    $_lang = jrUser_load_lang_strings();

    // Setup group_id url post
    $gid = (isset($_post['gid']) && jrCore_checktype($_post['gid'], 'number_nz')) ? "/gid={$_post['gid']}" : '';

    // See if we have a draft
    $_s = array(
        "search"        => array(
            "groupmailer_draft = 1",
            "_user_id = {$_user['_user_id']}"
        ),
        "skip_triggers" => true,
        "limit"         => 1
    );
    $_rt = jrCore_db_search_items('jrGroupMailer', $_s);
    if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
        $_post['draft'] = $_rt['_items'][0]['_item_id'];
    }

    $did = 0;
    $_vl = false;
    $val = 'Save Draft';
    if (isset($_post['draft']) && jrCore_checktype($_post['draft'], 'number_nz')) {
        $did = (int) $_post['draft'];
        $_vl = jrCore_db_get_item('jrGroupMailer', $_post['draft'], true, true);
        $val = 'Save Changes';
    }

    $tmp = '<img id="save_indicator" style="display:none;" src="' . $_conf['jrCore_base_url'] . '/skins/' . $_conf['jrCore_active_skin'] . '/img/submit.gif" width="24" height="24" alt="working...">&nbsp;' . jrCore_page_button('groupmailer_save', $val, "jrGroupMailer_save('{$gid}');");

    if (!isset($_post['tid'])) {
        $tmp .= jrCore_page_button('tpl', $_lang['jrGroupMailer'][6], "jrGroupMailer_check_template()");
    }

    // Templates button
    $tmp .= jrCore_page_button('btpl', $_lang['jrGroupMailer'][7], "location.href='{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_browser'");

    jrCore_page_banner(8, $tmp);
    jrCore_get_form_notice();

    // Form init
    $lurl = jrCore_get_local_referrer();
    if (!strpos($lurl, '/compose')) {
        jrCore_create_memory_url('compose_cancel', $lurl);
    }
    if (!$curl = jrCore_get_memory_url('compose_cancel')) {
        $curl = 'referrer';
    }
    $_tmp = array(
        'submit_value'     => 9,
        'cancel'           => $curl,
        'submit_prompt'    => 10,
        'form_ajax_submit' => false,
        'values'           => $_vl
    );
    jrCore_form_create($_tmp);

    // Show templates
    $_rt = jrGroupMailer_get_templates();
    if ($_rt && is_array($_rt)) {
        $_sel    = array();
        $_sel[0] = '-';
        foreach ($_rt as $tid => $ttl) {
            $_sel[$tid] = $ttl;
        }
        $_tmp = array(
            'name'     => 'groupmailer_template',
            'label'    => 11,
            'sublabel' => 12,
            'type'     => 'select',
            'options'  => $_sel,
            'default'  => (isset($_post['tid']) && jrCore_checktype($_post['tid'], 'number_nz')) ? intval($_post['tid']) : 0,
            'validate' => 'not_empty',
            'required' => true,
            'onchange' => "var nid=$(this).val(); if(nid > 0) { self.location='{$_conf['jrCore_base_url']}/{$_post['module_url']}/compose{$gid}/tid='+ nid } else { jrGroupMailer_compose_new() }"
        );
        jrCore_form_field_create($_tmp);
        jrCore_page_divider();
    }

    // Are we loading a template?
    $val = (isset($_vl['groupmailer_message'])) ? $_vl['groupmailer_message'] : '';
    $ttl = '';
    if (isset($_post['tid']) && jrCore_checktype($_post['tid'], 'number_nz')) {
        $_tp = jrGroupMailer_get_template($_post['tid']);
        $val = $_tp['t_template'];
        $ttl = $_tp['t_title'];
    }

    // Add a link to the group as default if a new message
    if (strlen(strip_tags($val)) == 0) {
        $_gt  = jrCore_db_get_item('jrGroup', $_post['gid']);
        $murl = jrCore_get_module_url('jrGroup');
        $val  = "<br><br><a href=\"{$_conf['jrCore_system_url']}/{$_gt['profile_url']}/{$murl}/{$_post['gid']}/{$_gt['group_title_url']}\">{$_gt['group_title']}</a>";
    }

    // Hidden - Template Title
    $_tmp = array(
        'name'  => 'template_title',
        'type'  => 'hidden',
        'value' => $ttl
    );
    jrCore_form_field_create($_tmp);

    // Hidden - Draft ID
    $_tmp = array(
        'name'  => 'groupmailer_id',
        'type'  => 'hidden',
        'value' => $did
    );
    jrCore_form_field_create($_tmp);

    // Email subject
    $_tmp = array(
        'name'     => 'groupmailer_title',
        'label'    => 13,
        'help'     => 15,
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Email message
    $_tmp = array(
        'name'       => 'groupmailer_message',
        'label'      => 16,
        'help'       => 17,
        'type'       => 'editor',
        'value'      => $val,
        'validate'   => 'allowed_html',
        'required'   => true
    );
    jrCore_form_field_create($_tmp);

    // Select recipients
    $_rec = array(
        0 => "{$_lang['jrGroupMailer'][18]} {$_user['user_email']}"
    );
    $_s = array(
        "order_by" => array('group_title' => 'asc'),
        "return_keys" => array('_item_id', 'group_title', 'profile_name', 'group_member_count'),
        "limit"    => 5000
    );
    if (!jrUser_is_admin()) {
        $_s['search'] = array('_profile_id = ' . jrUser_get_profile_home_key('_profile_id'));
    }
    $_gt = jrCore_db_search_items('jrGroup', $_s);
    if ($_gt && $_gt['_items'] && count($_gt['_items']) > 0) {
        foreach ($_gt['_items'] as $gt) {
            if (isset($gt['group_member_count']) && jrCore_checktype($gt['group_member_count'], 'number_nz')) {
                $gmc = $gt['group_member_count']; // Group member count (decrement if this user is a member as he won't be getting the email anyway)

                if (jrUser_is_admin()) {
                    $_rec["{$gt['_item_id']}"] = "{$_lang['jrGroupMailer'][50]} {$gt['group_title']} ({$_lang['jrGroupMailer'][52]} {$gt['profile_name']}, {$_lang['jrGroupMailer'][51]} {$gt['group_member_count']})";
                }
                elseif ($gt['group_member_count'] > 1) {
                    $gmc = $gt['group_member_count'] -1; // Exclude self from group member count
                    $_rec["{$gt['_item_id']}"] = "{$_lang['jrGroupMailer'][50]} {$gt['group_title']} ({$_lang['jrGroupMailer'][51]} {$gmc})";
                }
            }
        }
    }

    // Get Group names
    $_tmp = array(
        'name'     => 'groupmailer_group',
        'label'    => 19,
        'help'     => 20,
        'type'     => 'select',
        'options'  => $_rec,
        'value'    => (isset($_post['gid'])) ? $_post['gid'] : 0,
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $html = jrCore_parse_template('save_as_template.tpl', $_post, 'jrGroupMailer');
    jrCore_page_custom($html);
    jrCore_page_display();
}

//------------------------------
// Save
//------------------------------
function view_jrGroupMailer_compose_save($_post, $_user, $_conf)
{
    // Must be logged in as admin
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGroupMailer');
    jrCore_form_validate($_post);
    $_lang = jrUser_load_lang_strings();

    // Make sure the calling user has permission to send emails to this group
    if (jrCore_checktype($_post['groupmailer_group'], 'number_nz')) {
        if (!jrUser_can_edit_item(jrCore_db_get_item('jrGroup', $_post['groupmailer_group']))) {
            jrUser_not_authorized();
        }
    }

    // Save email to DS
    $_tmp = array(
        'groupmailer_sent'    => 'UNIX_TIMESTAMP()',
        'groupmailer_title'   => $_post['groupmailer_title'],
        'groupmailer_message' => $_post['groupmailer_message'],
        'groupmailer_group'   => $_post['groupmailer_group']
    );

    // Is this the group owner sending a test to themselves?
    if ($_post['groupmailer_group'] == '0') {

        $_tmp['groupmailer_draft'] = 1;

        // This is a TEST mailing of existing newsletter - do not create NEW - update existing
        if (isset($_post['groupmailer_id']) && jrCore_checktype($_post['groupmailer_id'], 'number_nz')) {
            $nid = (int) $_post['groupmailer_id'];
            if (!jrCore_db_update_item('jrGroupMailer', $nid, $_tmp)) {
                jrCore_set_form_notice('error', 'an error was encountered updating the email in the DataStore');
                jrCore_location('referrer');
            }
        }
        // This is a TEST send - create if the first time
        else {
            if (!$nid = jrCore_db_create_item('jrGroupMailer', $_tmp)) {
                jrCore_set_form_notice('error', 'an error was encountered creating the email in the DataStore');
                jrCore_location('referrer');
            }
        }

        // Send email to group owner
        // If we are currently UN SUBSCRIBED - make sure we're re-subscribed
        if (isset($_user['user_jrGroupMailer_email_notifications']) && $_user['user_jrGroupMailer_email_notifications'] == 'off') {
            jrCore_db_update_item('jrUser', $_user['_user_id'], array('user_jrGroupMailer_email_notifications' => 'email'));
        }

        jrUser_notify($_user['_user_id'], 0, 'jrGroupMailer', 'email', $_post['groupmailer_title'], $_post['groupmailer_message']);
        jrCore_form_delete_session();
        jrCore_set_form_notice('success', "{$_lang['jrGroupMailer'][21]} {$_user['user_email']}");
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/compose/draft={$nid}");

    }

    // FALL THROUGH - sending for real!

    // Auto save as template if configured
    if (isset($_user['quota_jrGroupMailer_auto_tpl']) && $_user['quota_jrGroupMailer_auto_tpl'] == 'on') {
        $template_title = $_post['groupmailer_title'] . ' (' . strftime('%d %b %Y %I:%M:%S%p') . ')';
        jrGroupMailer_save_template($template_title, $_post['groupmailer_message']);
    }

    // Create new email ID
    if (isset($_post['groupmailer_id']) && jrCore_checktype($_post['groupmailer_id'], 'number_nz')) {
        $nid = $_post['groupmailer_id'];
    }
    else {
        if (!$nid = jrCore_db_create_item('jrGroupMailer', $_tmp)) {
            jrCore_set_form_notice('error', 'an error was encountered creating the email in the DataStore');
            jrCore_form_result();
        }
    }

    // Our email
    $_post['groupmailer_id'] = $nid;

    // It's no longer a draft - remove draft key
    jrCore_db_delete_item_key('jrGroupMailer', $nid, 'groupmailer_draft');

    // Submit to prep queue
    $_post['groupmailer_sender'] = $_user['_user_id']; // Pass user id to the worker so that he doesn't get any emails
    if (isset($_grp['_user_id']) &&  $_grp['_user_id'] != $_user['_user_id']) {
        $_post['groupmailer_owner'] = false;
    }
    jrCore_queue_create('jrGroupMailer', 'prep_email', $_post);

    // redirect back
    jrCore_set_form_notice('success', $_lang['jrGroupMailer'][22], false);
    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_location("referrer");
}

//------------------------------
// Save Template
//------------------------------
function view_jrGroupMailer_save_template($_post, $_user, $_conf)
{
    jrUser_check_quota_access('jrGroupMailer');
    jrCore_validate_location_url();
    $_lang = jrUser_load_lang_strings();

    if ($_post['template_title'] == '') {
        jrCore_json_response(array('error' => 'No template title entered'));
    }
    if (jrGroupMailer_template_exists($_post['template_title'])) {
        jrCore_json_response(array('error' => 'A template with that title already exists'));
    }
    $tid = jrGroupMailer_save_template($_post['template_title'], $_post['groupmailer_message_editor_contents']);
    if ($tid && $tid > 0) {
        $_rp = array('success' => $_lang['jrGroupMailer'][23], 'tid' => $tid);
    }
    else {
        $_rp = array('error', 'Error saving template - please try again');
    }
    jrCore_json_response($_rp);
}

//------------------------------
// Save Draft
//------------------------------
function view_jrGroupMailer_save_draft($_post, $_user, $_conf)
{
    jrUser_check_quota_access('jrGroupMailer');
    jrCore_validate_location_url();
    $_lang = jrUser_load_lang_strings();

    $_tmp                         = array();
    $_tmp['groupmailer_sent']       = time();
    $_tmp['groupmailer_title']      = $_post['groupmailer_title'];
    $_tmp['groupmailer_draft']      = 1;
    $_tmp['groupmailer_message']    = $_post['groupmailer_message_editor_contents'];
    $_tmp['groupmailer_group']      = (isset($_post['groupmailer_group']) && is_array($_post['groupmailer_group'])) ? implode(',', $_post['groupmailer_group']) : '';
    $_tmp['groupmailer_recipients'] = 0;

    if (isset($_post['groupmailer_id']) && jrCore_checktype($_post['groupmailer_id'], 'number_nz')) {
        // Update DS newsletter
        if (!jrCore_db_update_item('jrGroupMailer', $_post['groupmailer_id'], $_tmp)) {
            jrCore_json_response(array('error' => 'an error was encountered saving the email - please try again'));
        }
        $id = (int) $_post['groupmailer_id'];
    }
    else {
        // Save newsletter to DS
        $id = jrCore_db_create_item('jrGroupMailer', $_tmp);
        if (!jrCore_checktype($id, 'number_nz')) {
            jrCore_json_response(array('error' => 'an error was encountered creating the email - please try again'));
        }
    }
    jrCore_json_response(array('success' => $_lang['jrGroupMailer'][24], 'draft_id' => $id));
}

//------------------------------
// Edit Email Template
//------------------------------
function view_jrGroupMailer_edit_email_template($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGroupMailer');
    $_lang = jrUser_load_lang_strings();

    $button = jrCore_page_button('new', $_lang['jrGroupMailer'][25], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/compose/tid={$_post['id']}')");
    jrCore_page_banner('Edit Email Template', $button);

    $_rt = jrGroupMailer_get_template($_post['id']);

    // Form init
    $_tmp = array(
        'submit_value' => 26,
        'cancel'       => "referrer",
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // Hidden - template id
    $_tmp = array(
        'name'  => 'template_id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    // Template title
    $_tmp = array(
        'name'     => 't_title',
        'label'    => 27,
        'help'     => 28,
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Template content
    $_tmp = array(
        'name'       => 't_template',
        'label'      => 29,
        'help'       => 30,
        'type'       => 'editor',
        'validate'   => 'allowed_html',
        'required'   => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// Edit Email Template Save
//------------------------------
function view_jrGroupMailer_edit_email_template_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGroupMailer');
    jrCore_form_validate($_post);
    $_lang = jrUser_load_lang_strings();

    $tid                 = (int) $_post['template_id'];
    $ttl                 = jrCore_db_escape($_post['t_title']);
    $_post['t_template'] = jrCore_db_escape($_post['t_template']);
    $tbl                 = jrCore_db_table_name('jrGroupMailer', 'template');
    $req                 = "UPDATE {$tbl} SET t_time = UNIX_TIMESTAMP(), t_title = '{$ttl}', t_template = '{$_post['t_template']}' WHERE t_id = '{$tid}' AND `t_user_id` = '{$_user['_user_id']}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_set_form_notice('success', $_lang['jrGroupMailer'][31]);
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered saving the template update - please try again');
    }
    jrCore_form_result();
}

//------------------------------
// Templates
//------------------------------
function view_jrGroupMailer_template_browser($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGroupMailer');
    $_lang = jrUser_load_lang_strings();

    jrCore_page_banner(32);
    jrCore_get_form_notice();

    $p = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $p = (int) $_post['p'];
    }
    $tbl = jrCore_db_table_name('jrGroupMailer', 'template');
    $req = "SELECT * FROM {$tbl} WHERE `t_user_id` = '{$_user['_user_id']}' ORDER BY t_time DESC";
    $_rt = jrCore_db_paged_query($req, $p, 12, 'NUMERIC');

    $dat             = array();
    $dat[1]['title'] = $_lang['jrGroupMailer'][33];
    $dat[1]['width'] = '60%';
    $dat[2]['title'] = $_lang['jrGroupMailer'][34];
    $dat[2]['width'] = '20%';
    $dat[3]['title'] = $_lang['jrGroupMailer'][35];
    $dat[3]['width'] = '10%';
    $dat[4]['title'] = $_lang['jrGroupMailer'][36];
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = $_lang['jrGroupMailer'][37];
    $dat[5]['width'] = '5%';
    jrCore_page_table_header($dat);

    if ($_rt && is_array($_rt) && is_array($_rt['_items'])) {

        foreach ($_rt['_items'] as $k => $_l) {

            $dat             = array();
            $dat[1]['title'] = $_l['t_title'];
            $dat[2]['title'] = jrCore_format_time($_l['t_time']);
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = jrCore_format_size(strlen($_l['t_template']));
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = jrCore_page_button("r{$k}", $_lang['jrGroupMailer'][36], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/edit_email_template/id={$_l['t_id']}')");
            $dat[5]['title'] = jrCore_page_button("d{$k}", $_lang['jrGroupMailer'][37], "if(confirm('{$_lang['jrGroupMailer'][38]}')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_delete_save/id={$_l['t_id']}') }");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = $_lang['jrGroupMailer'][39];
        $dat[1]['class'] = 'p10 center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button("referrer");
    jrCore_page_display();
}

//------------------------------
// Template Delete Save
//------------------------------
function view_jrGroupMailer_template_delete_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGroupMailer');
    jrCore_validate_location_url();
    $_lang = jrUser_load_lang_strings();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid template id');
        jrCore_location('referrer');
    }
    if (jrGroupMailer_delete_template($_post['id'])) {
        jrCore_set_form_notice('success', $_lang['jrGroupMailer'][40]);
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered deleting the template - please try again');
    }
    jrCore_location('referrer');
}

//------------------------------
// Browse
//------------------------------
function view_jrGroupMailer_browse($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGroupMailer');
    $_lang = jrUser_load_lang_strings();

    $button = jrCore_page_button('create', $_lang['jrGroupMailer'][41], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/compose')");
    jrCore_page_banner(42, $button);
    jrCore_get_form_notice();

    $_sc = array(
        'order_by'       => array(
            'letter_sent' => 'numerical_desc'
        ),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'privacy_check'  => false,
        'no_cache'       => true,
        'pagebreak'      => 12,
        'page'           => (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) ? (int) $_post['p'] : 1
    );
    $_rt = jrCore_db_search_items('jrGroupMailer', $_sc);

    $dat             = array();
    $dat[1]['title'] = $_lang['jrGroupMailer'][43];
    $dat[1]['width'] = '57%';
    $dat[2]['title'] = $_lang['jrGroupMailer'][44];
    $dat[2]['width'] = '25%';
    $dat[3]['title'] = $_lang['jrGroupMailer'][45];
    $dat[3]['width'] = '8%';
    $dat[4]['title'] = $_lang['jrGroupMailer'][36];
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = $_lang['jrGroupMailer'][37];
    $dat[5]['width'] = '5%';
    jrCore_page_table_header($dat);

    if ($_rt && is_array($_rt) && is_array($_rt['_items'])) {
        $url = jrCore_get_module_url('jrMailer');
        foreach ($_rt['_items'] as $k => $_l) {
            $dat = array();
            $dat[1]['title'] = $_l['groupmailer_title'];
            if (isset($_l['groupmailer_draft']) && $_l['groupmailer_draft'] == 1) {
                $dat[1]['class'] = 'error';
                $dat[2]['title'] = 'UNSENT';
                $dat[2]['class'] = 'center error';
                $dat[3]['title'] = '0';
                $dat[3]['class'] = 'center error';
                $dat[4]['title'] = jrCore_page_button("r{$k}", $_lang['jrGroupMailer'][36], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/compose/draft={$_l['_item_id']}')");
                $dat[4]['class'] = 'center error';
                $dat[5]['class'] = 'error';
            }
            else {
                $dat[2]['title'] = 'UNSENT';
                $dat[2]['title'] = jrCore_format_time($_l['_updated']);
                $dat[2]['class'] = 'center';
                $dat[3]['title'] = '';
                $dat[3]['class'] = 'center';
                $dat[4]['title'] = '';
            }
            $dat[5]['title'] = jrCore_page_button("d{$k}", $_lang['jrGroupMailer'][37], "if(confirm({$_lang['jrGroupMailer'][46]})) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_save/id={$_l['_item_id']}') }");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = $_lang['jrGroupMailer'][47];
        $dat[1]['class'] = 'p10 center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button("referrer");
    jrCore_page_display();
}

//------------------------------
// Delete Save
//------------------------------
function view_jrGroupMailer_delete_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGroupMailer');
    jrCore_validate_location_url();
    $_lang = jrUser_load_lang_strings();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid email id');
        jrCore_location('referrer');
    }
    $_nl = jrCore_db_get_item('jrGroupMailer', $_post['id']);
    if (!$_nl || !is_array($_nl)) {
        jrCore_set_form_notice('error', 'invalid email id - data not found');
        jrCore_location('referrer');
    }
    if (jrCore_db_delete_item('jrGroupMailer', $_post['id'])) {
        jrCore_set_form_notice('success', $_lang['jrGroupMailer'][48]);
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered deleting the email - please try again');
    }
    jrCore_location('referrer');
}

//------------------------------
// Save Template Update
//------------------------------
function view_jrGroupMailer_save_template_update($_post, $_user, $_conf)
{
    jrUser_check_quota_access('jrGroupMailer');
    jrCore_validate_location_url();
    $_lang = jrUser_load_lang_strings();

    $ttl = jrCore_db_escape($_post['template_title']);
    $tbl = jrCore_db_table_name('jrGroupMailer', 'template');
    $req = "SELECT * FROM {$tbl} WHERE t_title = '{$ttl}' ";
    $_ex = jrCore_db_query($req, 'SINGLE');
    if ($_ex && is_array($_ex)) {
        $msg = jrCore_db_escape($_post['groupmailer_message_editor_contents']);
        $req = "UPDATE {$tbl} SET t_time = UNIX_TIMESTAMP(), t_template = '{$msg}' WHERE t_title = '{$ttl}' AND `t_user_id` = '{$_user['_user_id']}'";
        jrCore_db_query($req, 'COUNT');
        $_rp = array('success' => $_lang['jrGroupMailer'][49]);
    }
    else {
        $_rp = array('error', 'Error saving template - please try again');
    }
    jrCore_json_response($_rp);
}
