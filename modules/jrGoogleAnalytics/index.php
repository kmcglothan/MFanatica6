<?php
/**
 * Jamroom Google Analytics module
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
// experiment_browse
//------------------------------
function view_jrGoogleAnalytics_experiment_browse($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrGoogleAnalytics', 'experiment_browse');
    jrCore_page_banner('A/B Testing Browser');
    jrCore_get_form_notice();

    $dat             = array();
    $dat[1]['title'] = 'original URL';
    $dat[1]['width'] = '38%';
    $dat[2]['title'] = 'variant URL';
    $dat[2]['width'] = '38%';
    $dat[3]['title'] = 'active';
    $dat[3]['width'] = '5%';
    $dat[4]['title'] = 'modify';
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = 'delete';
    $dat[5]['width'] = '5%';
    jrCore_page_table_header($dat);

    $page = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $page = (int) $_post['p'];
    }

    $tbl = jrCore_db_table_name('jrGoogleAnalytics', 'experiment');
    $req = "SELECT * FROM {$tbl} ORDER BY e_created DESC";
    $_rt = jrCore_db_paged_query($req, $page, 12, 'NUMERIC', jrCore_db_number_rows('jrGoogleAnalytics', 'experiment'));

    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
        $pass = jrCore_get_option_image('pass');
        $fail = jrCore_get_option_image('fail');
        foreach ($_rt['_items'] as $k => $_u) {
            $dat             = array();
            if ($_u['e_urlone'] == '/') {
                $dat[1]['title'] = "<a href=\"{$_conf['jrCore_base_url']}\" target=\"_blank\">{$_u['e_urlone']}</a>";
            }
            else {
                $dat[1]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_u['e_urlone']}\" target=\"_blank\">/{$_u['e_urlone']}</a>";
            }
            $dat[2]['title'] = '/' . $_u['e_urltwo'];
            $dat[3]['title'] = ($_u['e_active'] == 'on') ? $pass : $fail;
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = jrCore_page_button("url-update-{$k}", "modify", "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/experiment_modify/{$_u['e_id']}')");
            $dat[5]['title'] = jrCore_page_button("url-delete-{$k}", "delete", "if(confirm('Are you sure you wanto to delete this experiment?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/experiment_delete_save/{$_u['e_id']}') }");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = 'No Experiments have been created';
        $dat[1]['class'] = 'center p10';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    // Form init
    $_tmp = array(
        'submit_value'     => 'create new experiment',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // Original URL
    $_tmp = array(
        'name'     => 'e_urlone',
        'label'    => 'Original URL',
        'help'     => 'Enter the Original URL that you want to run the A/B test on - the "A" URL.<br><br>This is the URL you will link to from another site.',
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true,
        'section'  => 'create a new experiment'
    );
    jrCore_form_field_create($_tmp);

    // Variant URL
    $_tmp = array(
        'name'     => 'e_urltwo',
        'label'    => 'Variant URL',
        'help'     => 'Enter the Variant URL that you want to run the A/B test on - the "B" URL',
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Experiment Key
    $_tmp = array(
        'name'     => 'e_key',
        'label'    => 'Experiment Key',
        'help'     => 'Enter the Google Analytics Experiment Key that was created for this experiment.<br><br><b>NOTE:</b> The Experiment Key <b>must</b> already have been created in your Google Analytics -> Experiments section AND be enabled for A/B testing to work.',
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// experiment_browse_save
//------------------------------
function view_jrGoogleAnalytics_experiment_browse_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    $one = jrCore_db_escape(jrGoogleAnalytics_get_uri($_post['e_urlone']));
    $two = jrCore_db_escape(jrGoogleAnalytics_get_uri($_post['e_urltwo']));
    $key = jrCore_db_escape($_post['e_key']);

    $tbl = jrCore_db_table_name('jrGoogleAnalytics', 'experiment');
    $req = "INSERT INTO {$tbl} (e_created, e_urlone, e_urltwo, e_active, e_key) VALUES (UNIX_TIMESTAMP(), '{$one}', '{$two}', 'off', '{$key}')
            ON DUPLICATE KEY UPDATE e_created = UNIX_TIMESTAMP(), e_urltwo = VALUES(e_urltwo), e_key = VALUES(e_key)";
    jrCore_db_query($req);

    jrCore_form_delete_session();
    jrCore_set_form_notice('success', 'The new experiment has been successfully created');
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/experiment_browse");
}

//------------------------------
// experiment_modify
//------------------------------
function view_jrGoogleAnalytics_experiment_modify($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrGoogleAnalytics', 'experiment_browse');

    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid experiment id');
        jrCore_location('referrer');
    }
    $eid = (int) $_post['_1'];
    $tbl = jrCore_db_table_name('jrGoogleAnalytics', 'experiment');
    $req = "SELECT * FROM {$tbl} WHERE e_id = '{$eid}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid experiment id - no data found');
        jrCore_location('referrer');
    }

    jrCore_page_banner('Update Experiment');
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value'     => 'save changes',
        'cancel'           => 'referrer',
        'form_ajax_submit' => false,
        'values'           => $_rt
    );
    jrCore_form_create($_tmp);

    // ID
    $_tmp = array(
        'name'  => 'e_id',
        'type'  => 'hidden',
        'value' => $eid
    );
    jrCore_form_field_create($_tmp);

    // Active
    $_tmp = array(
        'name'     => 'e_active',
        'label'    => 'Experiment is Active',
        'help'     => 'If this option is checked, this experiment is ACTIVE and will be processed on matched URL views',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Original URL
    $_tmp = array(
        'name'     => 'e_urlone',
        'label'    => 'Original URL',
        'help'     => 'Enter the Original URL that you want to run the A/B test on - the "A" URL',
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Variant URL
    $_tmp = array(
        'name'     => 'e_urltwo',
        'label'    => 'Variant URL',
        'help'     => 'Enter the Variant URL that you want to run the A/B test on - the "B" URL',
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Experiment Key
    $_tmp = array(
        'name'     => 'e_key',
        'label'    => 'Experiment Key',
        'help'     => 'Enter the Google Analytics Experiment Key that was created for this experiment.<br><br><b>NOTE:</b> The Experiment Key <b>must</b> already have been created in your Google Analytics -> Experiments section AND be enabled for A/B testing to work.',
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// experiment_modify_save
//------------------------------
function view_jrGoogleAnalytics_experiment_modify_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    $eid = (int) $_post['e_id'];
    $one = jrCore_db_escape(jrGoogleAnalytics_get_uri($_post['e_urlone']));
    $two = jrCore_db_escape(jrGoogleAnalytics_get_uri($_post['e_urltwo']));
    $act = jrCore_db_escape($_post['e_active']);
    $key = jrCore_db_escape($_post['e_key']);

    $tbl = jrCore_db_table_name('jrGoogleAnalytics', 'experiment');
    $req = "UPDATE {$tbl} SET e_created = UNIX_TIMESTAMP(), e_urlone = '{$one}', e_urltwo = '{$two}', e_active = '{$act}', e_key = '{$key}' WHERE e_id = '{$eid}'";
    jrCore_db_query($req, 'COUNT');

    jrCore_form_delete_session();
    jrCore_set_form_notice('success', 'The experiment was successfully updated');
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/experiment_browse");
}

//------------------------------
// experiment_delete_save
//------------------------------
function view_jrGoogleAnalytics_experiment_delete_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid experiment id');
        jrCore_location('referrer');
    }
    $eid = (int) $_post['_1'];
    $tbl = jrCore_db_table_name('jrGoogleAnalytics', 'experiment');
    $req = "DELETE FROM {$tbl} WHERE e_id = '{$eid}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt > 0) {
        jrCore_set_form_notice('success', 'The experiment has been successfully deleted');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered deleting the experiment - please try again');
    }
    jrCore_location('referrer');
}
