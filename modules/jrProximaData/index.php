<?php
/**
 * Jamroom 5 Proxima Data module
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
 * @copyright 2014 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrProximaData_collection_browser
 */
function view_jrProximaData_collection_browser($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrProximaData', 'collection_browser');

    // Get apps
    $_ap = jrProximaCore_get_apps();
    // Add in app jumper
    $jmp = '';
    $aid = 0;
    if ($_ap && is_array($_ap)) {
        if (isset($_post['app_id']) && jrCore_checktype($_post['app_id'], 'number_nz')) {
            $aid = (int) $_post['app_id'];
            $_SESSION['proxima_active_app_id'] = $aid;
        }
        else {
            if (isset($_SESSION['proxima_active_app_id'])) {
                $aid = (int) $_SESSION['proxima_active_app_id'];
            }
            else {
                $aid = array_keys($_ap);
                $aid = reset($aid);
            }
        }
        if (count($_ap) > 1) {
            $url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/collection_browser/app_id=";
            $jmp .= '<select name="active_app" class="form_select form_select_item_jumper" onchange="var v=this.options[this.selectedIndex].value;jrCore_window_location(\'' . $url . "'+ v)\">\n";
            foreach ($_ap as $i => $n) {
                if ($i == $aid) {
                    $jmp .= '<option value="' . $i . '" selected="selected"> ' . $n . "</option>\n";
                }
                else {
                    $jmp .= '<option value="' . $i . '"> ' . $n . "</option>\n";
                }
            }
            $jmp .= '</select>';
        }
        else {
            $jmp = reset($_ap);
        }
    }
    jrCore_page_banner('data collections', $jmp);
    if (!is_array($_ap)) {
        jrCore_notice_page('error', 'You must first create a Proxima App before you can create a collection');
    }
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $tbl = jrCore_db_table_name('jrProximaData', 'collections');
    $req = "SELECT * FROM {$tbl} WHERE c_app_id = '{$aid}' ORDER BY c_name DESC";
    $_rt = jrCore_db_paged_query($req, $_post['p'], 12);

    $dat = array();
    $dat[1]['title'] = 'collection name';
    $dat[1]['width'] = '40%';
    $dat[2]['title'] = 'item count';
    $dat[2]['width'] = '15%';
    $dat[3]['title'] = 'read only';
    $dat[3]['width'] = '15%';
    $dat[4]['title'] = 'default';
    $dat[4]['width'] = '15%';
    $dat[5]['title'] = 'modify';
    $dat[5]['width'] = '5%';
    $dat[6]['title'] = 'delete';
    $dat[6]['width'] = '5%';
    $dat[7]['title'] = 'browse';
    $dat[7]['width'] = '5%';
    jrCore_page_table_header($dat);

    if (isset($_rt['_items']) && is_array($_rt['_items'])) {
        foreach ($_rt['_items'] as $k => $_col) {
            $dat = array();
            $dat[1]['title'] = '&nbsp;<strong>' . $_col['c_name'] .'</strong>';
            $dat[2]['title'] = $_col['c_items'];
            $dat[2]['class'] = 'center';
            if ($_col['c_readonly'] == 'on') {
                $dat[3]['title'] = 'readonly';
            }
            else {
                $dat[3]['title'] = '-';
            }
            $dat[3]['class'] = 'center';
            switch ($_col['c_perms']) {
                case '0':
                    $dat[4]['title'] = 'none';
                    break;
                case '1':
                    $dat[4]['title'] = 'private';
                    break;
                case '2':
                    $dat[4]['title'] = 'global read';
                    break;
                case '3':
                    $dat[4]['title'] = 'global write';
                    break;
            }
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrCore_page_button("a{$k}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/collection_update/id={$_col['c_id']}')");
            $dat[6]['title'] = jrCore_page_button("d{$k}", 'delete', "if(confirm('Are you sure you want to delete this collection? This will delete ALL data in this collection!')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/collection_delete_save/id={$_col['c_id']}') }");
            if ($_col['c_items'] > 0) {
                $dat[7]['title'] = jrCore_page_button("b{$k}", 'browse', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard/browser/" . urlencode($_col['c_name']) . "')");
            }
            else {
                $dat[7]['title'] = jrCore_page_button("b{$k}", 'browse', 'disabled');
            }
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat = array();
        $dat[1]['title'] = '<p>No collections have been created yet for this app - create a new one below.</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    jrCore_page_section_header('create a new collection');
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value'     => 'create new collection',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // Collection ID
    $_tmp = array(
        'name'     => 'id',
        'type'     => 'hidden',
        'value'    => $aid,
        'validate' => 'number_nz',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Collection Name
    $_tmp = array(
        'name'     => 'c_name',
        'label'    => 'collection name',
        'help'     => 'Enter the name of this new collection - it is recommended this be short but descriptive.  Only letters, numbers and underscores are allowed.',
        'type'     => 'text',
        'validate' => 'core_string',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Default Perms
    $_opt = array(
        '1' => 'Owner read and write (default)',
        '2' => 'Global read only',
        '3' => 'Global read and write'
    );
    $_tmp = array(
        'name'     => 'c_perms',
        'label'    => 'default item permissions',
        'help'     => 'Select the default item permissions for items created in this collection.<br><br><b>Owner read and write:</b> this is the default permissions for all items created in a collection.  The client creating the item has access to read, update and delete the item.  No other client may access the item without it specifically being updated to allow access.<br><br><b>Global read only:</b> The client creating the object retains full access to the item, but the item will be globally readable by all clients.<br><br><b>Global read and write:</b> All clients can read and write to the item (effectively removing permissions).',
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'number_nz',
        'default'  => '1',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Collection Read Only
    $_tmp = array(
        'name'     => 'c_readonly',
        'label'    => 'read only',
        'help'     => 'If this option is checked, then items can only be created in this collection using the Master Key or manually via the Data Browser.',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'default'  => 'off',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

/**
 * jrProximaData_collection_save
 */
function view_jrProximaData_collection_browser_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid app_id');
        jrCore_form_result();
    }

    // Make sure this collection does not already exist
    if (jrProximaData_get_collection_by_name($_post['id'], $_post['c_name'])) {
        jrCore_set_form_notice('error', 'There is already a collection using that name - please enter a different name');
        jrCore_form_result();
    }

    if (!jrProximaData_create_collection($_post['id'], $_post['c_name'], $_post['c_perms'], $_post['c_readonly'])) {
        jrCore_set_form_notice('error', 'An error was encountered creating the collection - please try again');
    }
    else {
        jrCore_set_form_notice('success', 'The new collection was successfully created');
    }
    jrCore_form_result();
}

/**
 * jrProximaData_collection_update
 */
function view_jrProximaData_collection_update($_post, $_user, $_conf)
{
    jrUser_master_only();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid collection id');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrProximaData', 'collections');
    $req = "SELECT * FROM {$tbl} WHERE c_id = '{$_post['id']}' LIMIT 1";
    $_ap = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_ap)) {
        jrCore_set_form_notice('error', 'invalid collection id');
        jrCore_location('referrer');
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrProximaData', 'collection_browser');
    jrCore_page_banner('modify collection');

    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/collection_browser",
        'values'       => $_ap
    );
    jrCore_form_create($_tmp);

    // ID
    $_tmp = array(
        'name'  => 'id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_custom($_ap['c_name'], 'collection name');

    // Default Perms
    $_opt = array(
        '1' => 'Owner read and write (default)',
        '2' => 'Global read only',
        '3' => 'Global read and write'
    );
    $_tmp = array(
        'name'     => 'c_perms',
        'label'    => 'default item permissions',
        'help'     => 'Select the default item permissions for items created in this collection.<br><br><b>Owner read and write:</b> this is the default permissions for all items created in a collection.  The client creating the item has access to read, update and delete the item.  No other client may access the item without it specifically being updated to allow access.<br><br><b>Global read only:</b> The client creating the object retains full access to the item, but the item will be globally readable by all clients.<br><br><b>Global read and write:</b> All clients can read and write to the item (effectively removing permissions).',
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'number_nz',
        'default'  => '1',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Collection Read Only
    $_tmp = array(
        'name'     => 'c_readonly',
        'label'    => 'read only',
        'help'     => 'If this option is checked, then items can only be created in this collection using the Master Key or manually via the Data Browser.',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'default'  => 'off',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

/**
 * jrProximaData_collection_update_save
 */
function view_jrProximaData_collection_update_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid collection id');
        jrCore_form_result();
    }
    $tbl = jrCore_db_table_name('jrProximaData', 'collections');
    $req = "SELECT * FROM {$tbl} WHERE c_id = '{$_post['id']}' LIMIT 1";
    $_ap = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_ap)) {
        jrCore_set_form_notice('error', 'invalid collection id');
        jrCore_form_result();
    }

    // Make sure this collection does not already exist
    $prm = jrCore_db_escape($_post['c_perms']);
    $rdo = jrCore_db_escape($_post['c_readonly']);
    $req = "UPDATE {$tbl} SET c_perms = '{$prm}', c_readonly = '{$rdo}' WHERE c_id = '{$_post['id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_set_form_notice('success', 'The Collection has been successfully updated');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered updating the Collection - please try again');
    }
    jrCore_form_result();
}

/**
 * jrProximaData_collection_delete_save
 */
function view_jrProximaData_collection_delete_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid collection id');
        jrCore_location('referrer');
    }
    if (jrProximaData_delete_collection($_post['id'])) {
        jrCore_set_form_notice('success', 'The Collection has been successfully deleted');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered deleting the Collection - please try again');
    }
    jrCore_location('referrer');
}

//------------------------------
// browser_item_update
//------------------------------
function view_jrProximaData_browser_item_update($_post, $_user, $_conf)
{
    jrUser_admin_only();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid item id');
        jrCore_form_result('referrer');
    }
    $_rt = jrCore_db_get_item($_post['module'], $_post['id'], true);
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Unable to retrieve item from DataStore - please try again');
    }

    // See if we are an admin or master user...
    $url = jrCore_get_local_referrer();
    if (jrUser_is_master() && !strpos($url, 'dashboard')) {
        jrCore_page_include_admin_menu();
        jrCore_page_admin_tabs($_post['module']);
    }
    else {
        jrCore_page_dashboard_tabs('browser');
    }
    if (!strpos($url, 'browser_item_update')) {
        $_SESSION['jrproximadata_browser_cancel_url'] = $url;
    }

    jrCore_page_banner('modify item values', "{$_rt['_g']}/{$_post['id']}");
    jrCore_set_form_notice('success', 'You can delete a key by emptying the value and saving the changes', false);
    jrCore_get_form_notice();
    // Go through each field and show it on a form
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => (isset($_SESSION['jrproximadata_browser_cancel_url'])) ? $_SESSION['jrproximadata_browser_cancel_url'] : 'referrer'
    );
    jrCore_form_create($_tmp);

    // Item ID
    $_tmp = array(
        'name'     => 'id',
        'type'     => 'hidden',
        'value'    => $_rt['_item_id'],
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    $pfx = jrCore_db_get_prefix($_post['module']);
    ksort($_rt);
    foreach ($_rt as $k => $v) {
        switch ($k) {

            case '_app_id':
            case '_item_id':
            case '_user_id':
            case '_profile_id':
                break;

            case '_g':
                $_tmp = array(
                    'name'  => "ds_key_{$k}",
                    'label' => '<span style="text-transform:lowercase">collection</span>',
                    'type'  => 'text',
                    'value' => $v
                );
                jrCore_form_field_create($_tmp);
                break;

            default:
                if (strpos($v, '{') === 0) {
                    // JSON - skin
                    continue;
                }
                // New Form Field
                if (strlen($v) > 128 || strpos(' ' . $v, "\n")) {
                    $_tmp = array(
                        'name'  => "ds_key_{$k}",
                        'label' => '<span style="text-transform:lowercase">' . str_replace("{$pfx}_", '', $k) . '</span>',
                        'type'  => 'textarea',
                        'value' => $v
                    );
                }
                else {
                    $_tmp = array(
                        'name'  => "ds_key_{$k}",
                        'label' => '<span style="text-transform:lowercase">' . str_replace("{$pfx}_", '', $k) . '</span>',
                        'type'  => 'text',
                        'value' => $v
                    );
                }
                jrCore_form_field_create($_tmp);
                break;
        }
    }

    // New Field...
    $err = '';
    if (isset($_SESSION['jr_form_field_highlight']['ds_browser_new_key'])) {
        unset($_SESSION['jr_form_field_highlight']['ds_browser_new_key']);
        $err = ' field-hilight';
    }
    $text = '<input type="text" class="form_text' . $err . '" id="ds_browser_new_key" name="ds_browser_new_key" value="">';
    $html = '<input type="text" class="form_text" id="ds_browser_new_value" name="ds_browser_new_value" value="">';
    $_tmp = array(
        'type'     => 'page_link_cell',
        'label'    => $text,
        'url'      => $html,
        'module'   => 'jrCore',
        'template' => 'page_link_cell.tpl'
    );
    jrCore_create_page_element('page', $_tmp);
    jrCore_page_display();
}

//---------------------- -------
// browser_item_update_save
//---------------------- -------
function view_jrProximaData_browser_item_update_save($_post, $_user, $_conf)
{
    jrUser_admin_only();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid item id');
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item($_post['module'], $_post['id'], true);
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Unable to retrieve item from DataStore - please try again');
        jrCore_form_result();
    }
    $_upd = array();
    $_cor = array();
    foreach ($_post as $k => $v) {
        if (strpos($k, 'ds_key_') === 0) {
            $k = substr($k, 7);
            if (isset($_rt[$k]) && ($_rt[$k] != $v || strlen($v) === 0)) {
                // See if we are removing fields....
                if (strlen($v) === 0) {
                    // Remove field
                    jrCore_db_delete_item_key($_post['module'], $_post['id'], $k);
                }
                elseif (strpos($k, '_') === 0) {
                    // Updating core key
                    $_cor[$k] = $v;
                }
                else {
                    $_upd[$k] = $v;
                }
            }
        }
    }

    // Check for new Value..
    if (isset($_post['ds_browser_new_key']{0})) {
        // Make sure it begins with our DS prefix
        $pfx = jrCore_db_get_prefix($_post['module']);
        if (!jrCore_checktype($_post['ds_browser_new_key'], 'core_string')) {
            $err = jrCore_checktype_core_string(null, true);
            jrCore_set_form_notice('error', "Invalid new key name - must contain {$err} only");
            jrCore_form_field_hilight('ds_browser_new_key');
            jrCore_form_result();
        }
        $_upd["{$pfx}_{$_post['ds_browser_new_key']}"] = $_post['ds_browser_new_value'];
    }

    if (isset($_upd) && count($_upd) > 0) {
        if (!jrCore_db_update_item($_post['module'], $_post['id'], $_upd, $_cor)) {
            jrCore_set_form_notice('error', 'An error was encountered saving the updates to the item - please try again');
            jrCore_form_result();
        }
    }
    jrCore_set_form_notice('success', 'The changes were successfully saved');
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browser_item_update/id={$_post['id']}");
}
