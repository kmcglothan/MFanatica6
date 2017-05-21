<?php
/**
 * Jamroom 5 Parameter Injection module
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
// browse (browse existing)
//------------------------------
function view_jrListParams_browse($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrListParams', 'browse');
    jrCore_page_banner('parameter browser');
    jrCore_get_form_notice();

    $dat             = array();
    $dat[1]['title'] = 'module';
    $dat[1]['width'] = '25%';
    $dat[2]['title'] = 'view';
    $dat[2]['width'] = '25%';
    $dat[3]['title'] = 'parameters';
    $dat[3]['width'] = '30%';
    $dat[4]['title'] = 'group(s)';
    $dat[4]['width'] = '10%';
    $dat[5]['title'] = 'modify';
    $dat[5]['width'] = '5%';
    $dat[6]['title'] = 'delete';
    $dat[6]['width'] = '5%';
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
        'privacy_check'  => false,
        'ignore_pending' => true,
        'page'           => (int) $_post['p'],
        'no_cache'       => true,
        'pagebreak'      => $pagebreak
    );

    $_rt = jrCore_db_search_items('jrListParams', $_sc);
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

        foreach ($_rt['_items'] as $k => $_rule) {
            $dat             = array();
            $dat[1]['title'] = $_mods["{$_rule['list_module']}"]['module_name'];
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $_rule['list_view'];
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = str_replace("\n", '<br>', $_rule['list_params']);
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = implode('<br>', explode(',', $_rule['list_group']));
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrCore_page_button("d{$k}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/update/id={$_rule['_item_id']}')");
            $dat[6]['title'] = jrCore_page_button("m{$k}", 'delete', "if(confirm('Are you sure you want to delete this custom parameter?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_save/id={$_rule['_item_id']}') }");
            jrCore_page_table_row($dat);
        }
        if (count($_rt['_items']) > $pagebreak) {
            jrCore_page_table_pager($_rt);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = "<p>no custom list parameters have been created yet - create one below.</p>";
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    // Form init
    $_tmp = array(
        'submit_value'     => 'create new list parameter',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);
    jrCore_set_form_notice('notice', 'Injecting Parameters is only recommended for <strong>specific use cases</strong> - <br>incorrect settings could cause the selected list view to stop functioning', false);
    jrCore_get_form_notice();

    // Module
    $_opt = jrListParams_get_modules();
    $_tmp = array(
        'name'     => 'list_module',
        'label'    => 'list module',
        'help'     => 'Select the module you would like to add a custom list parameter to.',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => '0',
        'validate' => 'not_empty',
        'required' => true,
        'onchange' => "var v=this.options[this.selectedIndex].value; jrListParams_get_views(v);"
    );
    jrCore_form_field_create($_tmp);

    // View
    $_tmp = array(
        'name'     => 'list_view',
        'label'    => 'list view',
        'help'     => "Enter the view that this parameter will be active on - i.e.:<br><br>{$_conf['jrCore_base_url']}/list_module/<strong>list_view</strong>",
        'type'     => 'select',
        'options'  => array('0' => ' -- select module to see views'),
        'required' => true,
        'disabled' => 'disabled',
        'class'    => 'form_element_disabled'
    );
    jrCore_form_field_create($_tmp);

    // Group
    $_opt = array(
        'all'     => '(group) All Users (including logged out)',
        'master'  => '(group) Master Admins',
        'admin'   => '(group) Profile Admins',
        'power'   => '(group) Power Users',
        'user'    => '(group) Normal Users',
        'visitor' => '(group) Logged Out Users'
    );
    $_qta = jrProfile_get_quotas();
    if (isset($_qta) && is_array($_qta)) {
        foreach ($_qta as $qid => $qname) {
            $_opt[$qid] = "(quota) {$qname}";
        }
    }
    $_tmp = array(
        'name'     => 'list_group',
        'label'    => 'list groups',
        'sublabel' => 'more than 1 group allowed',
        'help'     => 'If you would like this parameter to only be active for specific user groups, select the group(s) here.',
        'type'     => 'select_multiple',
        'options'  => $_opt,
        'default'  => 'all',
        'required' => '1',
        'validate' => 'core_string'
    );
    jrCore_form_field_create($_tmp);

    // Params
    $_tmp = array(
        'name'     => 'list_params',
        'label'    => 'list parameters',
        'sublable' => '(one search parameter per line)',
        'help'     => 'Enter the additional search parameters, one per line, that will be added to the module/view search parameters. For example:<br><br><strong>audio_download != off</strong><br><br>Entries are in the same format you would use with a {jrCore_list} template function call.',
        'type'     => 'textarea',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// browse_save
//------------------------------
function view_jrListParams_browse_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Make sure we get a valid list_view
    if (!isset($_post['list_view']) || $_post['list_view'] == '0') {
        jrCore_set_form_notice('error', 'You must select a valid List View');
        jrCore_form_result();
    }

    // See if this view has already been created
    $_sc = array(
        'search'        => array(
            "list_module = {$_post['list_module']}",
            "list_view = {$_post['list_view']}"
        ),
        'skip_triggers' => true,
        'no_cache'      => true,
        'limit'         => 50
    );
    $_rt = jrCore_db_search_items('jrListParams', $_sc);
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
        // We've got previous entries for this module/view - we need to make
        // sure we are no adding ANOTHER view that covers the same group(s)
        foreach ($_rt['_items'] as $v) {
            if (isset($v['list_group']) && strlen($v['list_group']) > 0) {
                if ($v['list_group'] == $_post['list_group']) {
                    jrCore_set_form_notice('error', 'There is already a custom list parameter configured for the selected <strong>List Groups</strong>.<br>Please select different List Groups, or modify the existing List Parameter', false);
                    jrCore_form_result();
                }
            }
        }
    }

    $_sv = jrCore_form_get_save_data('jrListParams', 'browse', $_post);
    $rid = jrCore_db_create_item('jrListParams', $_sv);
    if ($rid) {
        jrCore_form_delete_session();
        jrCore_set_form_notice('success', 'The new search parameter has been successfully created');
    }
    else {
        jrCore_set_form_notice('error', 'Unable to create new search parameter - please try again');
    }
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse");
}

//------------------------------
// update
//------------------------------
function view_jrListParams_update($_post, $_user, $_conf)
{
    jrUser_master_only();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid parameter id - please try again');
        jrCore_location('referrer');
    }
    $_rt = jrCore_db_get_item('jrListParams', $_post['id'], true);
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid parameter id - please try again');
        jrCore_location('referrer');
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrListParams', 'browse');
    jrCore_page_banner('Update Custom Search Parameter');
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse",
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

    // Module
    $_opt = jrListParams_get_modules();
    unset($_opt['0']);
    $_tmp = array(
        'name'     => 'list_module',
        'label'    => 'list module',
        'help'     => 'Select the module you would like to add a custom list parameter to',
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'not_empty',
        'required' => true,
        'onchange' => "var v=this.options[this.selectedIndex].value; jrListParams_get_views(v);"
    );
    jrCore_form_field_create($_tmp);

    $_opts = jrListParams_get_views($_rt['list_module']);

    // View
    $_tmp = array(
        'name'     => 'list_view',
        'label'    => 'list view',
        'help'     => "Enter the view that this parameter will be active on - i.e.:<br><br>{$_conf['jrCore_base_url']}/list_module/<strong>list_view</strong>",
        'type'     => 'select',
        'options'  => $_opts,
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Group
    $_opt = array(
        'all'     => '(group) All Users (including logged out)',
        'master'  => '(group) Master Admins',
        'admin'   => '(group) Profile Admins',
        'power'   => '(group) Power Users',
        'user'    => '(group) Normal Users',
        'visitor' => '(group) Logged Out Users'
    );
    $_qta = jrProfile_get_quotas();
    if (isset($_qta) && is_array($_qta)) {
        foreach ($_qta as $qid => $qname) {
            $_opt[$qid] = "(quota) {$qname}";
        }
    }
    $_tmp = array(
        'name'     => 'list_group',
        'label'    => 'list groups',
        'sublabel' => 'more than 1 group allowed',
        'help'     => 'If you would like this parameter to only be active for specific user groups, select the group(s) here.',
        'type'     => 'select_multiple',
        'options'  => $_opt,
        'default'  => 'all',
        'validate' => 'core_string'
    );
    jrCore_form_field_create($_tmp);

    // Params
    $_tmp = array(
        'name'     => 'list_params',
        'label'    => 'list parameters',
        'sublable' => '(one search parameter per line)',
        'help'     => 'Enter the additional search parameters, one per line, that will be added to the module/view search parameters. For example:<br><br><strong>audio_download != off</strong><br><br>Entries are in the same format you would use with a {jrCore_list} template function call.',
        'type'     => 'textarea',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrListParams_update_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid parameter id - please try again');
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item('jrListParams', $_post['id'], true);
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid parameter id - please try again');
        jrCore_form_result();
    }
    $_sv = jrCore_form_get_save_data('jrListParams', 'update', $_post);
    if (jrCore_db_update_item('jrListParams', $_post['id'], $_sv)) {
        jrCore_form_delete_session();
        jrCore_delete_all_cache_entries('jrListParams');
        jrCore_set_form_notice('success', 'The custom parameter has been successfully updated');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered updating the parameter - please try again');
    }
    jrCore_form_result();
}

//------------------------------
// delete
//------------------------------
function view_jrListParams_delete_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid parameter id - please try again');
        jrCore_location('referrer');
    }
    if (jrCore_db_delete_item('jrListParams', $_post['id'])) {
        jrCore_set_form_notice('success', 'The custom parameter has been successfully deleted');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered deleting the parameter - please try again');
    }
    jrCore_location('referrer');
}

//------------------------------
// get_views
//------------------------------
function view_jrListParams_get_views($_post, $_user, $_conf)
{
    jrUser_master_only();
    // Our module comes in as _1
    if (!isset($_post['_1']) || !jrCore_module_is_active($_post['_1'])) {
        $_rp = array('error', 'invalid module');
        jrCore_json_response($_rp);
    }
    if (!is_file(APP_DIR . "/modules/{$_post['_1']}/index.php")) {
        $_rp = array('error', 'invalid module (2)');
        jrCore_json_response($_rp);
    }
    $_vw = jrListParams_get_views($_post['_1']);
    if ($_vw) {
        $_rp = array('success' => $_vw);
        jrCore_json_response($_rp);
    }
    $_rp = array('error' => 'no module views found');
    jrCore_json_response($_rp);
}
