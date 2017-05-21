<?php
/**
 * Jamroom Activity Log Watcher module
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
// browse (browse existing)
//------------------------------
function view_jrLogWatch_browse($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrLogWatch', 'browse');
    jrCore_page_banner('LogWatch rule browser');
    if (!isset($_conf['jrLogWatch_active']) || $_conf['jrLogWatch_active'] != 'on') {
        jrCore_set_form_notice('error', 'LogWatch Active is currently is set to <strong>off</strong> in Global Config<br>Rules will not be run against the Activity Log until enabled', false);
    }
    else {
        jrCore_set_form_notice('success', 'LogWatch is <strong>active</strong> and monitoring the Activity Log', false);
    }
    jrCore_get_form_notice();
    jrCore_page_search('search', jrCore_get_current_url());

    $page = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $page = (int) $_post['p'];
    }

    $tbl = jrCore_db_table_name('jrLogWatch', 'rule');
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $ss  = jrCore_db_escape($_post['search_string']);
        $req = "SELECT * FROM {$tbl} WHERE rule_name LIKE '%{$ss}%' ORDER BY rule_id DESC";
    }
    else {
        $req = "SELECT * FROM {$tbl} ORDER BY rule_id DESC";
    }
    $_rt = jrCore_db_paged_query($req, $page, 12, 'NUMERIC');

    $dat             = array();
    $dat[1]['title'] = 'name';
    $dat[1]['width'] = '25%';
    $dat[2]['title'] = 'match';
    $dat[2]['width'] = '35%';
    $dat[3]['title'] = 'action';
    $dat[3]['width'] = '25%';
    $dat[4]['title'] = 'active';
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = 'modify';
    $dat[5]['width'] = '5%';
    $dat[6]['title'] = 'delete';
    $dat[6]['width'] = '5%';
    jrCore_page_table_header($dat);

    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

        $pass = jrCore_get_option_image('pass');
        $fail = jrCore_get_option_image('fail');

        foreach ($_rt['_items'] as $k => $_rule) {
            $dat             = array();
            $dat[1]['title'] = $_rule['rule_name'];
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = '&quot;' . $_rule['rule_match'] . '&quot;';
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $_rule['rule_action'];
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = ($_rule['rule_active'] == 1) ? $pass : $fail;
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrCore_page_button("d{$k}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/update/id={$_rule['rule_id']}')");
            $dat[6]['title'] = jrCore_page_button("m{$k}", 'delete', "if(confirm('Are you sure you want to delete this rule?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_save/id={$_rule['rule_id']}') }");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = "<p>no log watch rulesets have been created yet - create one below.</p>";
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    // Form init
    $_tmp = array(
        'submit_value'     => 'create new log watch rule',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // Title
    $_tmp = array(
        'name'     => 'name',
        'label'    => 'rule name',
        'help'     => 'Enter a short, descriptive name for this new rule.',
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true,
        'section'  => 'create a new rule'
    );
    jrCore_form_field_create($_tmp);

    // Priority
    $_opt = array(
        'all' => 'ALL priorities (default)',
        'cri' => 'CRI (critical) only',
        'maj' => 'MAJ (major) only',
        'min' => 'MIN (minor) only',
        'inf' => 'INF (informational) only',
        'dbg' => 'DBG (debug) only'
    );
    $_tmp = array(
        'name'     => 'pri',
        'label'    => 'rule match priority',
        'help'     => 'Select the priorty of the Activity Log entry that this rule will match',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 'all',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Match
    $_tmp = array(
        'name'     => 'match',
        'label'    => 'rule match string',
        'sublabel' => 'for entries in the Activity Log',
        'help'     => 'Enter a match string - if this string appears in the Activity Log text, it will be considered a match and result in the Rule Action being triggered.<br><br><strong>NOTE:</strong> The Rule Match String is not case sensitive.<br><br><strong>TIP:</strong> You can enter a PHP regular expresssion for finer control over matches in the format /&lt;expression&gt;/',
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'action',
        'label'    => 'rule action',
        'sublabel' => 'expand help for details',
        'help'     => 'Select the Action to take when this Rule Match String is matched, using the following expressions:<br><br><strong>masters</strong> - enter &quot;masters&quot; (without the quotes) to email Master Admins.<br><strong>admins</strong> - enter &quot;admins&quot; (without the quotes) to email Profile Admins.<br><strong>email address</strong> - enter a specific email address you want to send to.<br><b>URL</b> - enter a valid URL and the activity log entry will be POSTED to it.',
        'type'     => 'text',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_opt = array(
        0     => 'notify on every rule match',
        300   => 'notify on rule match once every 5 minutes',
        900   => 'notify on rule match once every 15 minutes',
        1800  => 'notify on rule match once every 30 minutes',
        3600  => 'notify on rule match once every 60 minutes',
        14400 => 'notify on rule match once every 4 hours',
        28800 => 'notify on rule match once every 8 hours',
        86400 => 'notify on rule match once a day'
    );
    $_tmp = array(
        'name'     => 'delay',
        'label'    => 'rule delay',
        'help'     => 'How often do you want to be notified when this rule matches a new Activity Log entry?',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 3600,
        'validate' => 'number_nn',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Active
    $_tmp = array(
        'name'     => 'active',
        'label'    => 'rule active',
        'help'     => 'If checked this rule will be active',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'default'  => 'off',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// browse_save
//------------------------------
function view_jrLogWatch_browse_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    $nam = jrCore_db_escape($_post['name']);
    $mtc = jrCore_db_escape($_post['match']);
    $act = jrCore_db_escape($_post['action']);
    $dly = (int) $_post['delay'];
    $acn = ($_post['active'] == 'on') ? 1 : 0;
    $pri = jrCore_db_escape($_post['pri']);
    $tbl = jrCore_db_table_name('jrLogWatch', 'rule');
    $req = "INSERT INTO {$tbl} (rule_name, rule_pri, rule_match, rule_action, rule_delay, rule_active) VALUES ('{$nam}', '{$pri}', '{$mtc}', '{$act}', '{$dly}', '{$acn}')";
    $iid = jrCore_db_query($req, 'INSERT_ID');
    if (!$iid || !jrCore_checktype($iid, 'number_nz')) {
        jrCore_set_form_notice('error', 'An error was encountered adding the rule to the database - please try again');
        jrCore_form_result();
    }
    jrCore_form_delete_session();
    jrCore_set_form_notice('success', 'The new rule has been successfully created');
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse");
}

//------------------------------
// update
//------------------------------
function view_jrLogWatch_update($_post, $_user, $_conf)
{
    jrUser_master_only();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nn')) {
        jrCore_set_form_notice('error', 'invalid rule id - please try again');
        jrCore_location('referrer');
    }
    $rid = (int) $_post['id'];
    $tbl = jrCore_db_table_name('jrLogWatch', 'rule');
    $req = "SELECT * FROM {$tbl} WHERE rule_id = '{$rid}'";
    $_rt = jrCore_db_query($req, 'SINGLE');

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrLogWatch', 'browse');
    $button = jrCore_page_button('test', 'test on activity log', "if (confirm('Run a test match for the first 25 matches of this rule?  Please be patient - on systems with a large Activity Log this could take a little bit')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/test/id={$_post['id']}') }");
    jrCore_page_banner('Update LogWatch Rule', $button);
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value'     => 'update log watch rule',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // ID
    $_tmp = array(
        'name'  => 'id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    // Title
    $_tmp = array(
        'name'     => 'name',
        'label'    => 'rule name',
        'help'     => 'Enter a short, descriptive name for this rule.',
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true,
        'value'    => $_rt['rule_name']
    );
    jrCore_form_field_create($_tmp);

    // Priority
    $_opt = array(
        'all' => 'ALL priorities (default)',
        'cri' => 'CRI (critical) only',
        'maj' => 'MAJ (major) only',
        'min' => 'MIN (minor) only',
        'inf' => 'INF (informational) only',
        'dbg' => 'DBG (debug) only'
    );
    $_tmp = array(
        'name'     => 'pri',
        'label'    => 'rule match priority',
        'help'     => 'Select the priorty of the Activity Log entry that this rule will match',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 'all',
        'validate' => 'not_empty',
        'required' => true,
        'value'    => $_rt['rule_pri']
    );
    jrCore_form_field_create($_tmp);

    // Match
    $_tmp = array(
        'name'     => 'match',
        'label'    => 'rule match string',
        'sublabel' => 'for entries in the Activity Log',
        'help'     => 'Enter a match string - if this string appears in the Activity Log text, it will be considered a match and result in the Rule Action being triggered.<br><br><strong>NOTE:</strong> The Rule Match String is not case sensitive.<br><br><strong>TIP:</strong> You can enter a PHP regular expresssion for finer control over matches in the format /&lt;expression&gt;/',
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true,
        'value'    => $_rt['rule_match']
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'action',
        'label'    => 'rule action',
        'sublabel' => 'expand help for details',
        'help'     => 'Select the Action to take when this Rule Match String is matched, using the following expressions:<br><br><strong>masters</strong> - enter &quot;masters&quot; (without the quotes) to email Master Admins.<br><strong>admins</strong> - enter &quot;admins&quot; (without the quotes) to email Profile Admins.<br><strong>email address</strong> - enter a specific email address you want to send to.<br><b>URL</b> - enter a valid URL and the activity log entry will be POSTED to it.',
        'type'     => 'text',
        'required' => true,
        'value'    => $_rt['rule_action']
    );
    jrCore_form_field_create($_tmp);

    $_opt = array(
        0     => 'notify on every rule match',
        300   => 'notify on rule match once every 5 minutes',
        1800  => 'notify on rule match once every 30 minutes',
        3600  => 'notify on rule match once every 60 minutes',
        14400 => 'notify on rule match once every 4 hours',
        28800 => 'notify on rule match once every 8 hours',
        86400 => 'notify on rule match once a day'
    );
    $_tmp = array(
        'name'     => 'delay',
        'label'    => 'rule delay',
        'help'     => 'How often do you want to be notified when this rule matches a new Activity Log entry?',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 3600,
        'validate' => 'number_nn',
        'required' => true,
        'value'    => $_rt['rule_delay']
    );
    jrCore_form_field_create($_tmp);

    // Active
    $_tmp = array(
        'name'     => 'active',
        'label'    => 'rule active',
        'help'     => 'If checked this rule will be active',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => true,
        'value'    => ($_rt['rule_active'] == 1) ? 'on' : 'off'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrLogWatch_update_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nn')) {
        jrCore_set_form_notice('error', 'invalid rule id - please try again');
        jrCore_location('referrer');
    }
    $rid = (int) $_post['id'];
    $tbl = jrCore_db_table_name('jrLogWatch', 'rule');
    $req = "SELECT * FROM {$tbl} WHERE rule_id = '{$rid}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid rule id - please try again');
        jrCore_location('referrer');
    }

    $nam = jrCore_db_escape($_post['name']);
    $mtc = jrCore_db_escape($_post['match']);
    $act = jrCore_db_escape($_post['action']);
    $dly = (int) $_post['delay'];
    $acn = ($_post['active'] == 'on') ? 1 : 0;
    $pri = jrCore_db_escape($_post['pri']);
    $req = "UPDATE {$tbl} SET rule_name = '{$nam}', rule_pri = '{$pri}', rule_match = '{$mtc}', rule_action = '{$act}', rule_delay = '{$dly}', rule_active = '{$acn}' WHERE rule_id = '{$rid}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!$cnt || $cnt === 0) {
        jrCore_set_form_notice('error', 'Unable to update rule in database - please try again');
        jrCore_form_result();
    }
    jrCore_form_delete_session();
    jrCore_set_form_notice('success', 'The rule has been successfully updated');
    jrCore_location('referrer');
}

//------------------------------
// test
//------------------------------
function view_jrLogWatch_test($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nn')) {
        jrCore_set_form_notice('error', 'invalid rule id - please try again');
        jrCore_location('referrer');
    }
    $rid = (int) $_post['id'];
    $tbl = jrCore_db_table_name('jrLogWatch', 'rule');
    $req = "SELECT * FROM {$tbl} WHERE rule_id = '{$rid}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid rule id - please try again');
        jrCore_location('referrer');
    }

    $_rules = array($_rt);

    $ofs = 0;
    $_mr = array();
    $tbl = jrCore_db_table_name('jrCore', 'log');
    while (true) {
        if ($ofs > 0) {
            $req = "SELECT log_id AS i, log_priority AS p, log_created AS c, log_text AS t FROM {$tbl} WHERE log_id < {$ofs} ORDER BY log_id DESC LIMIT 100";
        }
        else {
            $req = "SELECT log_id AS i, log_priority AS p, log_created AS c, log_text AS t FROM {$tbl} ORDER BY log_id DESC LIMIT 100";
        }
        $_lg = jrCore_db_query($req, 'NUMERIC');
        if (is_array($_lg)) {
            foreach ($_lg as $_log) {
                $_tmp = jrLogWatch_match($_log['p'], $_log['t'], $_rules, true);
                if (is_array($_tmp)) {
                    $_mr[] = $_log;
                    if (count($_mr) > 24) {
                        break 2;
                    }
                }
                $ofs = $_log['i'];
            }
        }
        else {
            break;
        }
    }
    if (count($_mr) > 0) {

        jrCore_page_include_admin_menu();
        jrCore_page_admin_tabs('jrLogWatch', 'tools');
        jrCore_page_banner('Matches');
        jrCore_set_form_notice('success', "Showing up to 25 recent matches for &quot;" . $_rt['rule_name'] . "&quot rule", false);
        jrCore_get_form_notice();

        $dat             = array();
        $dat[1]['title'] = 'id';
        $dat[1]['width'] = '5%';
        $dat[2]['title'] = 'date';
        $dat[2]['width'] = '20%';
        $dat[3]['title'] = 'message';
        $dat[3]['width'] = '75%';
        jrCore_page_table_header($dat);
        foreach ($_mr as $_m) {
            $dat             = array();
            $dat[1]['title'] = $_m['i'];
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = jrCore_format_time($_m['c']);
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $_m['t'];
            $dat[3]['class'] = "log-{$_m['p']}";
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_footer();
        jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/update/id={$_post['id']}", 'continue');
    }
    else {
        jrCore_set_form_notice('error', 'There were no matches found for this rule in the current Activity Log');
        jrCore_location('referrer');
    }
    jrCore_page_display();
}

//------------------------------
// delete
//------------------------------
function view_jrLogWatch_delete_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nn')) {
        jrCore_set_form_notice('error', 'invalid rule id - please try again');
        jrCore_location('referrer');
    }
    $rid = (int) $_post['id'];
    $tbl = jrCore_db_table_name('jrLogWatch', 'rule');
    $req = "SELECT * FROM {$tbl} WHERE rule_id = '{$rid}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid rule id - please try again');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrLogWatch', 'rule');
    $req = "DELETE FROM {$tbl} WHERE rule_id = '{$rid}'";
    jrCore_db_query($req);
    jrCore_location('referrer');
}
