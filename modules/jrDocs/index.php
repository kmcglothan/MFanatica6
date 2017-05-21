<?php
/**
 * Jamroom Documentation module
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
// create
//------------------------------
function view_jrDocs_create($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrDocs');

    // Start our create form
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
        "doc_section_type = header"
    );
    $tmp = jrCore_page_banner_item_jumper('jrDocs', 'doc_title', $_sr, 'create', 'update');
    jrCore_page_banner(1, $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 1,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    // Title
    $_tmp = array(
        'name'     => 'doc_title',
        'label'    => 2,
        'help'     => 3,
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Category
    $_tmp = array(
        'name'     => 'doc_category',
        'label'    => 4,
        'help'     => 5,
        'type'     => 'select_and_text',
        'validate' => 'printable',
        'required' => true
    );

    $url = jrCore_get_local_referrer();
    $_tm = explode('/', $url);
    if ($_tm && is_array($_tm) && isset($_tm[5])) {
        // Is this a valid category?
        $_sc = array(
            'search'         => array(
                "doc_category_url = {$_tm[5]}"
            ),
            'return_keys'    => array('doc_category'),
            'skip_triggers'  => true,
            'ignore_pending' => true,
            'quota_check'    => false,
            'limit'          => 1
        );
        $_sc = jrCore_db_search_items('jrDocs', $_sc);
        if ($_sc && is_array($_sc) && is_array($_sc['_items'])) {
            $_tmp['value'] = $_sc['_items'][0]['doc_category'];
        }
    }
    jrCore_form_field_create($_tmp);

    // Indent
    $_tmp = array(
        'name'     => 'doc_level',
        'label'    => 7,
        'help'     => 8,
        'type'     => 'select',
        'options'  => array(1 => 'Level 1', 2 => 'Level 2', 3 => 'Level 3'),
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Show Table of Contents (TOC)
    $_tmp = array(
        'name'     => 'doc_show_toc',
        'label'    => 9,
        'help'     => 10,
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Show Related
    if (isset($_conf['jrDocs_show_related']) && $_conf['jrDocs_show_related'] == 'on') {
        $_tmp = array(
            'name'          => 'doc_show_related',
            'label'         => 72,
            'help'          => 73,
            'type'          => 'checkbox',
            'default'       => 'on',
            'validate'      => 'onoff',
            'required'      => true,
            'form_designer' => false
        );
        jrCore_form_field_create($_tmp);
    }

    // Group
    $_tmp = array(
        'name'          => 'doc_group',
        'label'         => 65,
        'sublabel'      => 67,
        'help'          => 66,
        'type'          => 'select_multiple',
        'options'       => jrDocs_get_groups(),
        'default'       => 'all',
        'validate'      => 'core_string',
        'required'      => true,
        'form_designer' => false
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrDocs_create_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrDocs');

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('jrDocs', 'create', $_post);

    // Add in additional info for this new doc
    $_rt['doc_title_url']     = jrCore_url_string($_rt['doc_title']);
    $_rt['doc_category_url']  = jrCore_url_string($_rt['doc_category']);
    $_rt['doc_section_order'] = 0;
    $_rt['doc_section_type']  = 'header';
    $_rt['doc_show_toc']      = 'on';

    // Get current doc_order and add it to the bottom
    $_sc = array(
        'search'         => array(
            "doc_category_url = {$_rt['doc_category_url']}",
            "_profile_id = {$_user['user_active_profile_id']}"
        ),
        'order_by'       => array('doc_order' => 'desc'),
        'return_keys'    => array('doc_order'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'quota_check'    => false,
        'limit'          => 1
    );
    $_sc = jrCore_db_search_items('jrDocs', $_sc);
    if ($_sc && is_array($_sc) && is_array($_sc['_items'])) {
        $_rt['doc_order'] = intval($_sc['_items'][0]['doc_order']) + 1;
    }
    else {
        $_rt['doc_order'] = 5000;
    }

    // $fid will be the INSERT_ID (_item_id) of the created item
    $fid = jrCore_db_create_item('jrDocs', $_rt);
    if (!$fid) {
        jrCore_set_form_notice('error', 11);
        jrCore_form_result();
    }
    // Save any uploaded media files added in by our
    jrCore_save_all_media_files('jrDocs', 'create', $_user['user_active_profile_id'], $fid);

    // Update created doc with our doc_group_id, which brings all sections together
    $_sv = array('doc_group_id' => $fid);
    jrCore_db_update_item('jrDocs', $fid, $_sv);

    jrCore_form_delete_session();
    jrProfile_reset_cache();

    // redirect to document
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$_rt['doc_category_url']}/{$fid}/{$_rt['doc_title_url']}");
}

//------------------------------
// get_sections
//------------------------------
function view_jrDocs_get_sections($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrDocs');

    // We must get a valid item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice('error', 'invalid item id');
    }
    if (!isset($_post['profile_id']) || !jrCore_checktype($_post['profile_id'], 'number_nz')) {
        jrCore_notice('error', 'invalid profile id');
    }

    // Get our sections
    $_tmp = array(
        '_types' => array()
    );
    $_tpl = glob(APP_DIR . '/modules/jrDocs/templates/doc_section*.tpl');
    if (isset($_tpl) && is_array($_tpl)) {
        foreach ($_tpl as $file) {
            $type = basename($file);
            $type = substr($type, 12, strpos($type, '.tpl') - 12);
            switch ($type) {
                case 'header':
                case 'footer':
                    continue 2;
                    break;
            }
            $_tmp['_types'][$type] = str_replace('_', ' ', $type);
        }
    }
    $_tmp['item_id']    = (int) $_post['id'];
    $_tmp['profile_id'] = (int) $_post['profile_id'];
    if (isset($_post['order']) && jrCore_checktype($_post['order'], 'number_nn')) {
        $_tmp['doc_section_order'] = (int) $_post['order'];
    }
    return jrCore_parse_template('create_section.tpl', $_tmp, 'jrDocs');
}

//------------------------------
// create_section
//------------------------------
function view_jrDocs_create_section($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrDocs');

    // We must get a valid item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice('error', 'invalid item id');
    }
    // We must get a valid section type
    if (!isset($_post['_1'])) {
        jrCore_notice('error', 'invalid section type');
    }

    $fnc = "jrDocs_section_{$_post['_1']}";
    if (!function_exists($fnc)) {
        jrCore_notice('error', 'invalid section type');
    }
    jrCore_page_banner("new " . str_replace('_', ' ', $_post['_1']) . " section");

    // Form init
    $_tmp = array(
        'submit_value' => 12,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    // id
    $_tmp = array(
        'name'  => 'id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    // profile_id
    $_tmp = array(
        'name'  => 'profile_id',
        'type'  => 'hidden',
        'value' => $_post['profile_id']
    );
    jrCore_form_field_create($_tmp);

    // doc_section_order
    $_tmp = array(
        'name'  => 'doc_section_order',
        'type'  => 'hidden',
        'value' => $_post['order']
    );
    jrCore_form_field_create($_tmp);

    // Type
    $_tmp = array(
        'name'  => 'type',
        'type'  => 'hidden',
        'value' => $_post['_1']
    );
    jrCore_form_field_create($_tmp);

    // Run new section function
    $fnc($_post, $_user, $_conf, 'create');

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// create_section_save
//------------------------------
function view_jrDocs_create_section_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrDocs');

    // Get data
    $_rt = jrCore_db_get_item('jrDocs', $_post['id']);
    if (!$_rt || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 'Invalid ID - unable to retrieve document from DataStore');
        jrCore_form_result();
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    // Make sure this is a good type
    if (!is_file(APP_DIR . "/modules/jrDocs/templates/doc_section_{$_post['type']}.tpl")) {
        jrCore_set_form_notice('error', 'Invalid Document type');
        jrCore_form_result();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrDocs', 'create_section', $_post);

    // See if this type has defined a custom save function
    $fnc = "jrDocs_section_{$_post['type']}_save";
    if (function_exists($fnc)) {
        $_sv = $fnc($_post, $_user, $_conf, $_rt);
    }

    // if the doc_section_order is set, put this section just after that one, otherwise at the bottom.
    if (isset($_post['doc_section_order']) && jrCore_checktype($_post['doc_section_order'], 'number_nn')) {
        // Ok - we know where our new entry goes - shift all after
        // down 1 space so the new section slides into the right slot
        $_sp = array(
            'search'         => array(
                "doc_group_id = {$_post['id']}",
                "doc_section_order > {$_post['doc_section_order']}"
            ),
            'order_by'       => array(
                "doc_section_order" => "numerical_asc"
            ),
            'return_keys'    => array('_item_id', '_profile_id', 'doc_section_order'),
            'limit'          => 500,
            'skip_triggers'  => true,
            'privacy_check'  => false,
            'ignore_pending' => true
        );
        // TODO: This should be replaced by DS functions
        $_ed = jrCore_db_search_items('jrDocs', $_sp);
        if (isset($_ed['_items']) && is_array($_ed['_items'])) {
            $tbl = jrCore_db_table_name('jrDocs', 'item_key');
            $req = "INSERT INTO {$tbl} (`_item_id`,`_profile_id`,`key`,`value`) VALUES ";
            foreach ($_ed['_items'] as $_item) {
                $ord = (int) ($_item['doc_section_order'] + 1);
                $iid = (int) $_item['_item_id'];
                $pid = (int) $_item['_profile_id'];
                $req .= "({$iid},{$pid},'doc_section_order',{$ord}),";
            }
            $req = substr($req, 0, strlen($req) - 1) . " ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
            jrCore_db_query($req);
        }
        $ord = (int) $_post['doc_section_order'] + 1;
    }
    else {
        // No specific location requested so, - we need to make sure this document section is at the bottom
        // of the existing document
        $ord = 5000;
        $_sp = array(
            'search'         => array(
                "doc_group_id = {$_post['id']}"
            ),
            'order_by'       => array(
                "doc_section_order" => "numerical_desc"
            ),
            'return_keys'    => array('doc_section_order'),
            'limit'          => 1,
            'skip_triggers'  => true,
            'privacy_check'  => false,
            'ignore_pending' => true
        );
        $_ed = jrCore_db_search_items('jrDocs', $_sp);
        if (isset($_ed['_items']) && is_array($_ed['_items'])) {
            $ord = intval($_ed['_items'][0]['doc_section_order'] + 1);
        }
    }
    $_sv['doc_group_id']      = (int) $_post['id'];
    $_sv['doc_section_type']  = $_post['type'];
    $_sv['doc_section_order'] = $ord;
    $_sv['doc_title_url']     = jrCore_url_string($_sv['doc_title']);

    // See if our profile_id has changed...
    $_cr = null;
    $url = $_user['profile_url'];
    if (isset($_post['profile_id']) && jrCore_checktype($_post['profile_id'], 'number_nz') && $_post['profile_id'] != $_user['user_active_profile_id'] && jrProfile_is_profile_owner($_post['profile_id'])) {
        $_cr = array('_profile_id' => (int) $_post['profile_id']);
        $url = jrCore_db_get_item_key('jrProfile', $_post['profile_id'], 'profile_url');
    }

    // $fid will be the INSERT_ID (_item_id) of the created item
    $fid = jrCore_db_create_item('jrDocs', $_sv, $_cr);
    if (!$fid) {
        jrCore_set_form_notice('error', 'An error was encountered creating the new document - please try again');
        jrCore_form_result();
    }

    // Update the _updated time on our section header...
    jrCore_db_update_item('jrDocs', $_sv['doc_group_id'], array('doc_group_id' => $_sv['doc_group_id']));

    // Save any uploaded media file
    jrCore_save_all_media_files('jrDocs', 'create_section', $_user['user_active_profile_id'], $fid);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$url}/{$_post['module_url']}/{$_rt['doc_category_url']}/{$_post['id']}/{$_rt['doc_title_url']}");
}

//------------------------------
// section_update
//------------------------------
function view_jrDocs_section_update($_post, $_user, $_conf)
{
    // [_uri] => /documentation/create_section/code/id=1
    // [module_url] => documentation
    // [module] => jrDocs
    // [option] => create_section
    // [_1] => code
    // [id] => 1

    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrDocs');

    // We must get a valid item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice('error', 'invalid item id');
    }
    // Get data
    $_rt = jrCore_db_get_item('jrDocs', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 'Invalid ID - unable to retrieve document from DataStore');
        jrCore_form_result();
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    $_sel = array();
    $_tpl = glob(APP_DIR . '/modules/jrDocs/templates/doc_section*.tpl');
    if (isset($_tpl) && is_array($_tpl)) {
        foreach ($_tpl as $file) {
            $type = basename($file);
            $type = substr($type, 12, strpos($type, '.tpl') - 12);
            switch ($type) {
                case 'header':
                case 'footer':
                    continue 2;
                    break;
            }
            $_sel[$type] = str_replace('_', ' ', $type);
        }
    }

    $fnc = "jrDocs_section_{$_rt['doc_section_type']}";
    if (!function_exists($fnc)) {
        jrCore_notice('error', 'invalid section type');
    }
    jrCore_page_banner("update " . $_sel["{$_rt['doc_section_type']}"] . " section");

    // Form init
    $_tmp = array(
        'submit_value' => 13,
        'cancel'       => jrCore_is_profile_referrer(),
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // id
    $_tmp = array(
        'name'  => 'id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    if ($_rt && is_array($_rt)) {
        $_tmp = array(
            'name'     => 'change_section_type',
            'label'    => 46,
            'help'     => 50,
            'type'     => 'select',
            'options'  => $_sel,
            'value'    => $_rt['doc_section_type'],
            'onchange' => "var a=this.options[this.selectedIndex].value;jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/change_doc_type/id={$_rt['_item_id']}/type=' + a)"
        );
        jrCore_form_field_create($_tmp);
    }

    // Run update section function
    $fnc($_post, $_user, $_conf, $_rt);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// section_update_save
//------------------------------
function view_jrDocs_section_update_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrDocs');

    // Validate all incoming posted data
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrDocs');

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid ID received');
        jrCore_form_result();
    }

    // Get data
    $_rt = jrCore_db_get_item('jrDocs', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 'Invalid ID - unable to retrieve document from DataStore');
        jrCore_form_result();
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrDocs', 'section_update', $_post);
    // See if this type has defined a custom save function
    $fnc = "jrDocs_section_{$_rt['doc_section_type']}_save";
    if (function_exists($fnc)) {
        $_sv = $fnc($_post, $_user, $_conf, $_rt);
    }

    // Add in our SEO URL names
    $_sv['doc_title_url'] = jrCore_url_string($_sv['doc_title']);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrDocs', $_post['id'], $_sv);

    // Save any uploaded media file
    jrCore_save_all_media_files('jrDocs', 'section_update', $_rt['_profile_id'], $_post['id']);

    // Update the _updated time on our section header...
    jrCore_db_update_item('jrDocs', $_rt['doc_group_id'], array('doc_group_id' => $_rt['doc_group_id']));

    // Lastly, we need to get this sections parent info
    $_pr = jrCore_db_get_item('jrDocs', $_rt['doc_group_id']);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    // redirect to document
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_pr['profile_url']}/{$_post['module_url']}/{$_pr['doc_category_url']}/{$_pr['_item_id']}/{$_pr['doc_title_url']}");
}

//------------------------------
// update
//------------------------------
function view_jrDocs_update($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrDocs');

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid ID received');
    }

    // Get info about this item
    $_rt = jrCore_db_get_item('jrDocs', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', 'Invalid ID - unable to retrieve document from DataStore');
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Start output
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
        "doc_section_type = header"
    );
    $tmp = jrCore_page_banner_item_jumper('jrDocs', 'doc_title', $_sr, 'create', 'update');
    jrCore_page_banner(6, $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 13,
        'cancel'       => jrCore_is_profile_referrer(),
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

    // Title
    $_tmp = array(
        'name'     => 'doc_title',
        'label'    => 2,
        'help'     => 3,
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Category
    $_tmp = array(
        'name'     => 'doc_category',
        'label'    => 4,
        'help'     => 5,
        'type'     => 'select_and_text',
        'validate' => 'printable',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Indent
    $_tmp = array(
        'name'     => 'doc_level',
        'label'    => 7,
        'help'     => 8,
        'type'     => 'select',
        'options'  => array(1 => 'Level 1', 2 => 'Level 2', 3 => 'Level 3'),
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Show Table of Contents (TOC)
    $_tmp = array(
        'name'     => 'doc_show_toc',
        'label'    => 9,
        'help'     => 10,
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Show Related
    if (isset($_conf['jrDocs_show_related']) && $_conf['jrDocs_show_related'] == 'on') {
        $_tmp = array(
            'name'          => 'doc_show_related',
            'label'         => 72,
            'help'          => 73,
            'type'          => 'checkbox',
            'default'       => 'on',
            'validate'      => 'onoff',
            'required'      => true,
            'form_designer' => false
        );
        jrCore_form_field_create($_tmp);
    }

    // Group
    $_tmp = array(
        'name'          => 'doc_group',
        'label'         => 65,
        'sublabel'      => 67,
        'help'          => 66,
        'type'          => 'select_multiple',
        'options'       => jrDocs_get_groups(),
        'default'       => 'all',
        'validate'      => 'core_string',
        'required'      => true,
        'form_designer' => false
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrDocs_update_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrDocs');

    // Validate all incoming posted data
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrDocs');

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid ID received');
        jrCore_form_result();
    }

    // Get data
    $_rt = jrCore_db_get_item('jrDocs', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 'Invalid ID - unable to retrieve document from DataStore');
        jrCore_form_result();
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrDocs', 'update', $_post);

    // Add in our SEO URL names
    $_sv['doc_title_url']    = jrCore_url_string($_sv['doc_title']);
    $_sv['doc_category_url'] = jrCore_url_string($_sv['doc_category']);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrDocs', $_post['id'], $_sv);

    // Save any uploaded media file
    jrCore_save_all_media_files('jrDocs', 'update', $_user['user_active_profile_id'], $_post['id']);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    // redirect to document
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$_sv['doc_category_url']}/{$_post['id']}/{$_sv['doc_title_url']}");
}

//------------------------------
// section_delete
//------------------------------
function view_jrDocs_section_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrDocs');

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid ID');
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item('jrDocs', $_post['id']);

    // Make sure the calling user has permission to delete this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    jrCore_db_delete_item('jrDocs', $_post['id']);
    jrProfile_reset_cache();
    jrCore_form_result('delete_referrer');
}

//------------------------------
// delete
//------------------------------
function view_jrDocs_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrDocs');

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid ID');
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item('jrDocs', $_post['id']);

    // Make sure the calling user has permission to delete this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    // We need to delete all associated sections for this document
    $_ex = jrCore_db_get_multiple_items_by_key('jrDocs', 'doc_group_id', $_rt['doc_group_id']);
    if (isset($_ex) && is_array($_ex)) {
        $_id = array();
        foreach ($_ex as $_v) {
            $_id[] = (int) $_v['_item_id'];
        }
        jrCore_db_delete_multiple_items('jrDocs', $_id);
    }
    jrCore_db_delete_item('jrDocs', $_post['id']);
    jrProfile_reset_cache();

    // See if we deleted the last page in this category
    $_ex = jrCore_db_get_multiple_items_by_key('jrDocs', 'doc_category', $_rt['doc_category']);
    if (isset($_ex) && is_array($_ex)) {
        // Back to category
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_rt['profile_url']}/{$_post['module_url']}/{$_rt['doc_category_url']}");
    }
    else {
        // Return to the main category index
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_rt['profile_url']}/{$_post['module_url']}");
    }
}

//------------------------------
// section_parameter_delete
//------------------------------
function view_jrDocs_section_parameter_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrDocs');

    // Make sure we get a good parameter
    if (empty($_post['parameter'])) {
        jrCore_set_form_notice('error', 'Invalid Parameter');
        jrCore_form_result();
    }
    $_post['parameter'] = jrCore_url_decode_string($_post['parameter']);

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid ID');
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item('jrDocs', $_post['id']);

    // Make sure the calling user has permission to delete this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    $_rt['doc_parameters'] = json_decode($_rt['doc_parameters'], true);
    foreach ($_rt['doc_parameters'] as $k => $_v) {
        if ($_v['name'] == $_post['parameter']) {
            unset($_rt['doc_parameters'][$k]);
            break;
        }
    }
    $_sv = array(
        'doc_parameters' => json_encode($_rt['doc_parameters'])
    );
    jrCore_db_update_item('jrDocs', $_post['id'], $_sv);
    jrProfile_reset_cache($_rt['_profile_id'], 'jrDocs');
    jrCore_form_result('referrer');
}

//------------------------------
// change_doc_type
//------------------------------
function view_jrDocs_change_doc_type($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrDocs');

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid id - please try again');
        jrCore_location('referrer');
    }
    $_rt = jrCore_db_get_item('jrDocs', $_post['id']);
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid id - please try again (2)');
        jrCore_location('referrer');
    }
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    $_up = array(
        'doc_section_type' => $_post['type']
    );
    jrCore_db_update_item('jrDocs', $_post['id'], $_up);

    $type = 'unknown';
    $_tpl = glob(APP_DIR . '/modules/jrDocs/templates/doc_section*.tpl');
    if (isset($_tpl) && is_array($_tpl)) {
        foreach ($_tpl as $file) {
            $type = basename($file);
            $type = substr($type, 12, strpos($type, '.tpl') - 12);
            if ($type == $_post['type']) {
                break;
            }
        }
    }
    jrCore_set_form_notice('success', "The section type was successfully changed to &quot;{$type}&quot;");
    jrCore_location('referrer');
}

//------------------------------
// order_update
//------------------------------
function view_jrDocs_order_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['doc_order']) || !is_array($_post['doc_order'])) {
        return jrCore_json_response(array('error', 'invalid doc_order array received'));
    }

    // Get our audio files that are being re-ordered and make sure
    // the calling user has access to them
    if (!jrUser_is_admin()) {
        $_rt = jrCore_db_get_multiple_items('jrDocs', $_post['doc_order']);
        if (!isset($_rt) || !is_array($_rt)) {
            return jrCore_json_response(array('error', 'unable to retrieve documentation entries from database'));
        }
        foreach ($_rt as $_v) {
            if (!jrUser_can_edit_item($_v)) {
                return jrCore_json_response(array('error', 'permission denied'));
            }
        }
    }
    // Looks good - set album order
    $tbl = jrCore_db_table_name('jrDocs', 'item_key');
    $req = "INSERT INTO {$tbl} (`_item_id`,`key`,`index`,`value`) VALUES ";
    foreach ($_post['doc_order'] as $ord => $iid) {
        $ord = (int) $ord;
        $iid = (int) $iid;
        $req .= "('{$iid}','doc_section_order',0,'{$ord}'),";
    }
    $req = substr($req, 0, strlen($req) - 1) . " ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
    jrCore_db_query($req);
    jrProfile_reset_cache();
    return jrCore_json_response(array('success', 'doc_order successfully updated'));
}

//------------------------------
// category_order_update
//------------------------------
function view_jrDocs_category_order_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['doc_order']) || !is_array($_post['doc_order'])) {
        return jrCore_json_response(array('error', 'invalid doc_order array received'));
    }
    // Get our audio files that are being re-ordered and make sure
    // the calling user has access to them
    if (!jrUser_is_admin()) {
        $_rt = jrCore_db_get_multiple_items('jrDocs', $_post['doc_order']);
        if (!isset($_rt) || !is_array($_rt)) {
            return jrCore_json_response(array('error', 'unable to retrieve documentation entries from database'));
        }
        foreach ($_rt as $_v) {
            if (!jrUser_can_edit_item($_v)) {
                return jrCore_json_response(array('error', 'permission denied'));
            }
        }
    }
    // Looks good - set album order
    $tbl = jrCore_db_table_name('jrDocs', 'item_key');
    $req = "INSERT INTO {$tbl} (`_item_id`,`key`,`index`,`value`) VALUES ";
    foreach ($_post['doc_order'] as $ord => $iid) {
        $ord = (int) $ord + 1;
        $iid = (int) $iid;
        $req .= "('{$iid}','doc_order',0,'{$ord}'),";
    }
    $req = substr($req, 0, strlen($req) - 1) . " ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
    jrCore_db_query($req);
    jrProfile_reset_cache();
    return jrCore_json_response(array('success', 'doc_order successfully updated'));
}

//------------------------------
// chapter_order_update
//------------------------------
function view_jrDocs_chapter_order_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['chapter_order']) || !is_array($_post['chapter_order'])) {
        return jrCore_json_response(array('error', 'invalid chapter_order array received'));
    }
    // Get our chapters that are being re-ordered and make sure
    // the calling user has access to them
    if (!jrUser_is_admin()) {
        if (!jrProfile_is_profile_owner($_post['profile_id'])) {
            return jrCore_json_response(array('error', 'you are not an owner of this profile, so cant update the order.'));
        }
    }
    // Looks good - set chapter order
    $tbl = jrCore_db_table_name('jrDocs', 'chapter');
    $req = "INSERT INTO {$tbl} (`chapter_profile_id`,`chapter_category_url`,`chapter_order`) VALUES ";
    foreach ($_post['chapter_order'] as $ord => $category_url) {
        $ord = (int) $ord + 1;
        $req .= "('" . jrCore_db_escape($_post['profile_id']) . "','" . jrCore_db_escape($category_url) . "','{$ord}'),";
    }
    $req = substr($req, 0, strlen($req) - 1) . " ON DUPLICATE KEY UPDATE `chapter_order` = VALUES(`chapter_order`)";
    jrCore_db_query($req);
    jrProfile_reset_cache();
    return jrCore_json_response(array('success', 'chapter order successfully updated'));
}

//------------------------------
// get related (__ajax)
//------------------------------
function view_jrDocs_get_related($_post, $_user, $_conf)
{
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'string')) {
        return 'invalid tag';
    }

    $pid = false;
    if (isset($_post['profile_id']) && jrCore_checktype($_post['profile_id'], 'number_nz')) {
        $pid = $_post['profile_id'];
    }

    // get all the docs that are tagged with this tag.
    $_rt = array(
        'search'                       => array(
            "doc_tags like %{$_post['_1']}%",
            'doc_section_type = header',
            'doc_order > 0'
        ),
        'order_by'                     => array(
            'doc_order' => 'numerical_asc'
        ),
        'limit'                        => 30,
        'exclude_jrUser_keys'          => true,
        'exclude_jrProfile_quota_keys' => true,
        'ignore_pending'               => true,
        'quota_check'                  => false
    );
    $_rt = jrCore_db_search_items('jrDocs', $_rt);
    $pri = '';
    $out = '';
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
        $_rt['_items'] = jrDocs_get_docs_visible_to_viewer($_rt['_items'], $pid);
        if (count($_rt['_items']) > 0) {
            $murl = jrCore_get_module_url('jrDocs');
            foreach ($_rt['_items'] as $item) {

                $tmp = "<div class='doc_tag_link'>{$item['profile_name']} &raquo; {$item['doc_category']}  &raquo; <a href='{$_conf['jrCore_base_url']}/{$item['profile_url']}/{$murl}/{$item['doc_category_url']}/{$item['_item_id']}/{$item['doc_title_url']}' target='_blank'>{$item['doc_title']}</a><br></div>";

                if ($pid && $pid == $item['profile_id']) {
                    $pri .= $tmp;
                }
                else {
                    $out .= $tmp;
                }
            }
        }
    }
    $_ln = jrUser_load_lang_strings();
    return "<h3>{$_ln['jrDocs'][76]}:</h3><br>" . $pri . $out;
}
