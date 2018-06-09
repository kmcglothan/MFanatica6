<?php
/**
 * Jamroom Chained Select module
 *
 * copyright 2018 The Jamroom Network
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
 * @copyright 2013 Talldude Networks, LLC.
 * @author Paul Asher <paul [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//------------------------------
// Browse
//------------------------------
function view_jrChainedSelect_browse($_post, $_user, $_conf)
{
    // Must be logged in as admin
    jrUser_session_require_login();
    jrUser_master_only();

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrChainedSelect', 'browse');
    $btn = null;
    if (jrCore_db_number_rows('jrChainedSelect', 'item') > 0) {
        $btn = jrCore_page_button('eo', 'Export Old Sets', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/export_old');");
    }
    jrCore_page_banner('Chained Select sets', $btn);

    $tbl = jrCore_db_table_name('jrChainedSelect', 'sets');
    $req = "SELECT * FROM {$tbl} ORDER BY set_name ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');

    $dat             = array();
    $dat[1]['title'] = 'set name';
    $dat[1]['width'] = '40%;';
    $dat[2]['title'] = 'option levels';
    $dat[2]['width'] = '30%;';
    $dat[3]['title'] = 'options 1';
    $dat[3]['width'] = '5%;';
    $dat[4]['title'] = 'options 2';
    $dat[4]['width'] = '5%;';
    $dat[5]['title'] = 'options 3';
    $dat[5]['width'] = '5%;';
    $dat[6]['title'] = 'delete';
    $dat[6]['width'] = '5%;';
    $dat[7]['title'] = 'export';
    $dat[7]['width'] = '5%;';
    jrCore_page_table_header($dat);
    unset($dat);

    if (isset($_rt) && is_array($_rt)) {

        // Each Entry
        foreach ($_rt as $k => $_set) {

            $dat             = array();
            $dat[1]['title'] = '<strong>' . $_set['set_name'] . '</strong>';
            $dat[1]['class'] = 'center" style="text-transform:lowercase';
            $dat[2]['title'] = $_set['set_levels'];
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = jrCore_page_button("m{$k}0", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/update_options/{$_set['set_id']}/0')");
            $dat[4]['title'] = jrCore_page_button("m{$k}1", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/update_options/{$_set['set_id']}/1')");
            if (isset($_set['set_levels']) && $_set['set_levels'] == 3) {
                $dat[5]['title'] = jrCore_page_button("m{$k}2", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/update_options/{$_set['set_id']}/2')");
            }
            else {
                $dat[5]['title'] = jrCore_page_button("m{$k}2", 'modify', 'disabled');
            }
            $dat[6]['title'] = jrCore_page_button("d{$k}", 'delete', "if (confirm('Are you sure you want to delete this entire set?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_save/id={$_set['set_id']}'); }");
            $dat[7]['title'] = jrCore_page_button("e{$k}", 'export', "if (confirm('Are you sure you want to export this set to a CSV file?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/export/id={$_set['set_id']}'); }");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>no Chained Select sets have been created yet</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    jrCore_page_section_header('Create a new Set');

    // Form init
    $_tmp = array(
        'submit_value' => 'Create new Set',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools"
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'     => 'set_name',
        'label'    => 'New Set Name',
        'help'     => 'Enter a unique name for this new Chained Select Set - this name will be used in the Form Designer &quot;Options&quot; field.',
        'type'     => 'text',
        'validate' => 'core_string',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'set_levels',
        'label'    => 'Option Levels',
        'help'     => 'Select the number of &quot;Option Levels&quot; this chained select will contain',
        'type'     => 'select',
        'options'  => array(2 => '2 levels', 3 => '3 levels'),
        'default'  => 2,
        'validate' => 'number_nz',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'set_options_1',
        'label'    => 'First Level Options',
        'help'     => 'Enter the first level options for this new Set - one per line.  These are the first options that will be selectable from the select list.',
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
function view_jrChainedSelect_browse_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Make sure we don't already exist
    $nam = jrCore_db_escape(strtolower($_post['set_name']));
    $tbl = jrCore_db_table_name('jrChainedSelect', 'sets');
    $req = "SELECT * FROM {$tbl} WHERE set_name = '{$nam}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        jrCore_set_form_notice('error', 'There is already a set with the same name - please enter another');
        jrCore_form_field_hilight('set_name');
        jrCore_form_result();
    }

    // Create new one
    $_op = array();
    foreach (explode("\n", trim($_post['set_options_1'])) as $v) {
        if (strpos($v, '|')) {
            list($k, $v) = explode('|', $v);
            $k       = trim($k);
            $_op[$k] = trim($v);
        }
        else {
            $v       = trim($v);
            $_op[$v] = $v;
        }
    }
    $_op = jrCore_db_escape(json_encode($_op));
    $req = "INSERT INTO {$tbl} (set_name, set_levels, set_options_0, set_options_1, set_options_2) VALUES ('{$nam}', '{$_post['set_levels']}', '{$_op}', '', '')";
    $sid = jrCore_db_query($req, 'INSERT_ID');
    if ($sid && jrCore_checktype($sid, 'number_nz')) {
        jrCore_form_delete_session();
        jrCore_set_form_notice('success', 'The new Chained Field Set has been created');
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse");
    }
    jrCore_set_form_notice('error', 'An error was encountered creating the new Options Set - please try again');
    jrCore_form_result();
}

//------------------------------
// update_options
//------------------------------
function view_jrChainedSelect_update_options($_post, $_user, $_conf)
{
    // Must be logged in as admin
    jrUser_session_require_login();
    jrUser_master_only();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid set_id received - please try again');
        jrCore_form_result('referrer');
    }
    $_rt = jrChainedSelect_get_set($_post['_1']);
    if (!$_rt) {
        jrCore_set_form_notice('error', 'Invalid set_id received - please try again (2)');
        jrCore_form_result('referrer');
    }
    $sid = (int) $_post['_1'];
    if (!isset($_post['_2']) || !jrCore_checktype($_post['_2'], 'number_nn') || $_post['_2'] < 0 || $_post['_2'] > 2) {
        jrCore_set_form_notice('error', 'Invalid set level received - please try again');
        jrCore_form_result('referrer');
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrChainedSelect', 'browse');
    jrCore_page_banner("Update Options for &quot;{$_rt['set_name']}&quot;");

    // Form init
    $_tmp = array(
        'submit_value' => 'Save Changes',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse"
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'  => 'set_id',
        'type'  => 'hidden',
        'value' => $sid
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'  => 'set_level',
        'type'  => 'hidden',
        'value' => intval($_post['_2'])
    );
    jrCore_form_field_create($_tmp);

    // Prepare our options....
    if ($_post['_2'] == '0') {

        // Prep values
        $_opt = array();
        foreach (json_decode($_rt['set_options_0'], true) as $k => $v) {
            if ($k !== $v) {
                $_opt[] = "{$k}|{$v}";
            }
            else {
                $_opt[] = $v;
            }
        }

        $_tmp = array(
            'name'     => 'set_name',
            'label'    => 'Set Name',
            'help'     => 'Enter a unique name for this Chained Select Set - this name will be used in the Form Designer &quot;Options&quot; field.',
            'type'     => 'text',
            'validate' => 'core_string',
            'value'    => $_rt['set_name'],
            'required' => true
        );
        jrCore_form_field_create($_tmp);

        $_tmp = array(
            'name'     => 'set_levels',
            'label'    => 'Option Levels',
            'help'     => 'Select the number of &quot;Option Levels&quot; this chained select will contain',
            'type'     => 'select',
            'options'  => array(2 => '2 levels', 3 => '3 levels'),
            'value'    => $_rt['set_levels'],
            'validate' => 'number_nz',
            'required' => true
        );
        jrCore_form_field_create($_tmp);
        $val = implode("\n", $_opt);
    }
    else {
        $_opt = array();
        $_op1 = json_decode($_rt['set_options_0'], true);
        if (is_array($_op1)) {
            if ($_post['_2'] == 2) {
                $_op2 = json_decode($_rt['set_options_1'], true);
                if (is_array($_op2)) {
                    foreach ($_op1 as $k => $v) {
                        if (isset($_op2[$k])) {
                            foreach ($_op2[$k] as $k2 => $v2) {
                                $_opt["{$k}|{$k2}"] = "{$v} - {$v2}";
                            }
                        }
                    }
                }
            }
            else {
                $_opt = $_op1;
            }
        }

        $_tmp = array(
            'name'     => 'set_option_title',
            'label'    => "Option Label",
            'help'     => 'If you would like this set of options to have an alternate Form Label, enter the label here.  Leave this blank to have no label associated with this select form entry.',
            'type'     => 'text',
            'validate' => 'printable',
            'required' => false
        );
        jrCore_form_field_create($_tmp);

        $_tmp = array(
            'name'     => 'set_option_name',
            'label'    => "When Selected",
            'help'     => 'Select the option the option you want to edit the choices for.  When this option is selected, the new options shown below will be presented.',
            'type'     => 'select',
            'options'  => $_opt,
            'validate' => 'core_string',
            'required' => true,
            'onchange' => "var v=jrE(this.options[this.selectedIndex].value); jrChainedSelect_load('{$_rt['set_name']}', " . intval($_post['_2']) . ", v);",
            'size'     => 6
        );
        if (!isset($_post['_3']) || strlen($_post['_3']) === 0) {
            $_post['_3'] = array_keys($_opt);
            $_post['_3'] = reset($_post['_3']);
        }
        $_tmp['value'] = $_post['_3'];
        if (isset($_rt["set_options_{$_post['_2']}"]{1})) {
            $val = json_decode($_rt["set_options_{$_post['_2']}"], true);
            if (isset($val["{$_post['_3']}"])) {
                $val = implode("\n", $val["{$_post['_3']}"]);
            }
        }
        jrCore_form_field_create($_tmp);
    }

    $_tmp = array(
        'name'     => 'set_options',
        'label'    => 'Show Options',
        'sublabel' => '(one option per line)',
        'help'     => 'Enter the options that will be shown when the option above is selected.<br><br><strong>Note:</strong> enter one option per line.  If you want the saved value to be different than the displayed value, separate the key and value with a pipe sign - i.e.<br><br>saved_value|Displayed Value',
        'type'     => 'textarea',
        'validate' => 'not_empty',
        'required' => true
    );
    if (isset($val)) {
        $_tmp['value'] = $val;
    }
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// update_options_save
//------------------------------
function view_jrChainedSelect_update_options_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    $_rt = jrChainedSelect_get_set($_post['set_id']);
    if (!$_rt) {
        jrCore_set_form_notice('error', 'invalid set_id - please try again');
        jrCore_form_result();
    }
    $_op = array();
    foreach (explode("\n", trim($_post['set_options'])) as $v) {
        if (strpos($v, '|')) {
            list($k, $v) = explode('|', $v);
            $k       = trim($k);
            $_op[$k] = trim($v);
        }
        else {
            $v       = trim($v);
            $_op[$v] = $v;
        }
    }
    $num = (int) $_post['set_level'];
    $_nw = array();
    // Do we have existing options?
    if (isset($_rt["set_options_{$num}"]{1})) {
        $_nw = json_decode($_rt["set_options_{$num}"], true);
    }
    $tbl = jrCore_db_table_name('jrChainedSelect', 'sets');
    if ($num > 0) {
        $nam = trim($_post['set_option_name']);
        // If our name has a | in it, we are a 3rd level key
        if (strpos($nam, '|')) {
            list($nam, $key) = explode('|', $nam);
            $nam = trim($nam);
            $key = trim($key);
            if (!isset($_nw[$nam])) {
                $_nw[$nam] = array();
            }
            $_nw[$nam][$key] = $_op;
        }
        else {
            $_nw[$nam] = $_op;
        }
        $_nw = jrCore_db_escape(json_encode($_nw));
        $ttl = '';
        if (isset($_post['set_option_title']) && strlen($_post['set_option_title']) > 0) {
            $ttl = jrCore_db_escape($_post['set_option_title']);
        }
        $req = "UPDATE {$tbl} SET `set_options_{$num}` = '{$_nw}', `set_options_{$num}_title` = '{$ttl}' WHERE set_id = '{$_post['set_id']}' LIMIT 1";
    }
    else {
        $nam = jrCore_db_escape($_post['set_name']);
        $lvl = intval($_post['set_levels']);
        $_nw = jrCore_db_escape(json_encode($_op));
        $req = "UPDATE {$tbl} SET `set_name` = '{$nam}', `set_levels` = '{$lvl}', `set_options_0` = '{$_nw}' WHERE set_id = '{$_post['set_id']}' LIMIT 1";
    }
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_set_form_notice('success', 'The set options were successfully saved');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered updating the form options - please try again');
    }
    if ($num > 0) {
        jrCore_form_result();
    }
    else {
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse");
    }
}

//------------------------------
// delete_save
//------------------------------
function view_jrChainedSelect_delete_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid set_id received - please try again');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrChainedSelect', 'sets');
    $req = "DELETE FROM {$tbl} WHERE set_id = {$_post['id']} LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_set_form_notice('success', 'The option set was successfully deleted');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered deleting the set - please try again');
    }
    jrCore_location('referrer');
}

//------------------------------
// Export Old
//------------------------------
function view_jrChainedSelect_export_old($_post, $_user, $_conf)
{
    // Must be logged in as admin
    jrUser_session_require_login();
    jrUser_master_only();

    $num = jrCore_db_number_rows('jrChainedSelect', 'item');
    if ($num == 0) {
        jrCore_notice_page('error', 'There are no old chained select entries to show in the database');
    }

    $_sp = array(
        'search'        => array(
            'cs_name like %'
        ),
        'group_by'      => 'cs_name',
        'order_by'      => array('cs_name' => 'asc'),
        'skip_triggers' => true,
        'limit'         => 1000
    );
    $_sp = jrCore_db_search_items('jrChainedSelect', $_sp);
    if (!$_sp || !is_array($_sp) || !isset($_sp['_items'])) {
        jrCore_notice_page('error', 'There are no old chained select entries to show in the database (2)');
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrChainedSelect', 'tools');
    jrCore_page_banner('Export Old Sets');

    $dat             = array();
    $dat[1]['title'] = 'set name';
    $dat[1]['width'] = '90%;';
    $dat[2]['title'] = 'delete set';
    $dat[2]['width'] = '5%;';
    $dat[3]['title'] = 'export set';
    $dat[3]['width'] = '5%;';
    jrCore_page_table_header($dat);
    unset($dat);

    foreach ($_sp['_items'] as $k => $_set) {
        $dat             = array();
        $dat[1]['title'] = '<strong>' . $_set['value'] . '</strong>';
        $dat[1]['class'] = 'center" style="text-transform:lowercase';
        $dat[2]['title'] = jrCore_page_button("d{$k}", 'delete set', "if (confirm('Are you sure you want to delete this entire set?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_old_save/name={$_set['value']}'); }");
        $dat[3]['title'] = jrCore_page_button("e{$k}", 'export set', "if (confirm('Are you sure you want to export this set to a CSV file? Please be patient - the export could take a few minutes to process')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/export_old_save/name={$_set['value']}'); }");
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_pager($_sp);
    jrCore_page_table_footer();
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    jrCore_page_display();
}

//------------------------------
// delete old save
//------------------------------
function view_jrChainedSelect_delete_old_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_master_only();
    if (!isset($_post['name']) || strlen($_post['name']) === 0) {
        jrCore_set_form_notice('error', 'invalid set name - please try again');
        jrCore_location('referrer');
    }
    @ini_set('max_execution_time', 600); // 10 minutes max
    @ini_set('memory_limit', '1024M');
    $_sp = array(
        'search'              => array(
            "cs_name = {$_post['name']}"
        ),
        'return_item_id_only' => true,
        'limit'               => 100000
    );
    $_sp = jrCore_db_search_items('jrChainedSelect', $_sp);
    if ($_sp && is_array($_sp)) {
        jrCore_db_delete_multiple_items('jrChainedSelect', $_sp);
    }
    jrCore_set_form_notice('success', 'The set has been successfully deleted');
    jrCore_location('refferer');
}

//------------------------------
// Export Old Save
//------------------------------
function view_jrChainedSelect_export_old_save($_post, $_user, $_conf)
{
    global $_conf;
    // Must be logged in as admin
    jrUser_session_require_login();
    jrUser_master_only();

    if (!isset($_post['name']) || strlen($_post['name']) === 0) {
        jrCore_set_form_notice('error', 'invalid set name - please try again');
        jrCore_location('referrer');
    }

    @ini_set('max_execution_time', 600); // 10 minutes max
    @ini_set('memory_limit', '1024M');

    $_conf['jrCore_datastore_cutoff'] = 500000;
    $_sp                              = array(
        'search'         => array(
            "cs_name = {$_post['name']}"
        ),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'privacy_check'  => false,
        'limit'          => 250000
    );
    $_rt                              = jrCore_db_search_items('jrChainedSelect', $_sp);
    if (!$_rt || !is_array($_rt) || !isset($_rt['_items'])) {
        jrCore_set_form_notice('error', 'there were no entries found for the name - please try again');
        jrCore_location('referrer');
    }
    $out = '';
    $nam = $_post['name'];
    foreach ($_rt['_items'] as $k => $_itm) {
        foreach (range(0, 5) as $v) {
            if (isset($_itm["cs_{$nam}_{$v}"])) {
                if ($v > 0) {
                    $out .= ',';
                }
                $out .= "\"" . $_itm["cs_{$nam}_{$v}"] . "\"";
            }
        }
        $out .= "\n";
        unset($_rt['_items'][$k]);
    }
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=\"ChainedSelect_old_{$nam}.csv\"");
    echo $out;
}

//------------------------------
// Export
//------------------------------
function view_jrChainedSelect_export($_post, $_user, $_conf)
{
    // Must be logged in as admin
    jrUser_session_require_login();
    jrUser_master_only();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid set_id - please try again');
        jrCore_location('referrer');
    }
    $_rt = jrChainedSelect_get_set($_post['id']);
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid set_id - please try again (2)');
        jrCore_location('referrer');
    }

    // How many levels is this set setup for?
    $num = (int) ($_rt['set_levels'] - 1);
    if (!isset($_rt["set_options_{$num}"]{1})) {
        jrCore_set_form_notice('error', 'There are no set options to export');
        jrCore_location('referrer');
    }

    $_r0 = array();
    if (isset($_rt['set_options_0']{1})) {
        $_jo = json_decode($_rt['set_options_0'], true);
        if (is_array($_jo)) {
            foreach ($_jo as $k => $v) {
                if ($k != $v) {
                    $_r0[$k] = "\"{$k}|{$v}\"";
                }
                else {
                    $_r0[$k] = "\"{$k}\"";
                }
            }
        }
        asort($_r0);
    }

    $_r1 = array();
    if (isset($_rt['set_options_1']{1})) {
        $_jo = json_decode($_rt['set_options_1'], true);
        if (is_array($_jo)) {
            foreach ($_jo as $k => $v) {
                if (isset($_r0[$k])) {
                    foreach ($v as $k1 => $v1) {
                        if ($k1 != $v1) {
                            $val = "{$_r0[$k]},\"{$k1}|{$v1}\"";
                        }
                        else {
                            $val = "{$_r0[$k]},\"{$k1}\"";
                        }
                        $_r1[$val] = $val;
                    }
                }
            }
        }
    }

    if (isset($_rt['set_options_2']{1})) {
        $_jo = json_decode($_rt['set_options_2'], true);
        if (is_array($_jo)) {
            foreach ($_jo as $k => $v) {
                if (isset($_r0[$k])) {
                    foreach ($v as $k1 => $v1) {
                        $val = "{$_r0[$k]},\"{$k1}\"";
                        if (isset($_r1[$val])) {
                            foreach ($v1 as $k2 => $v2) {
                                if ($k2 != $v2) {
                                    $nv = "{$_r1[$val]},\"{$k2}|{$v2}\"";
                                }
                                else {
                                    $nv = "{$_r1[$val]},\"{$k2}\"";
                                }
                                $_r1[$nv] = $nv;
                            }
                            unset($_r1[$val]);
                        }
                    }
                }
            }
        }
    }

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=\"ChainedSelect_{$_rt['set_name']}.csv\"");
    asort($_r1);
    $out = implode("\n", $_r1);
    echo $out;
}

//------------------------------
// Import
//------------------------------
function view_jrChainedSelect_import($_post, $_user, $_conf)
{
    // Must be logged in as admin
    jrUser_session_require_login();
    jrUser_master_only();

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrChainedSelect');

    // Create form
    jrCore_set_form_notice('success', '<strong>Tip:</strong> If you enter the name of an existing Option Set,<br>the imported entries will be added to the existing set if they do not already exist.', false);
    jrCore_page_banner('Chained Select field import CSV file');

    // Form init
    $_tmp = array(
        'submit_value' => 'submit',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools"
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'     => 'set_name',
        'label'    => 'Set Name',
        'help'     => 'Enter a unique name for this new Chained Select Set - this name will be used in the Form Designer &quot;Options&quot; field.',
        'type'     => 'text',
        'validate' => 'core_string',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // File
    $_tmp = array(
        'name'       => 'cs_csv',
        'label'      => 'CSV File',
        'help'       => 'Select the CSV file to upload',
        'text'       => 'select',
        'type'       => 'file',
        'extensions' => 'csv',
        'required'   => false
    );
    jrCore_form_field_create($_tmp);

    $_library = glob("{$_conf['jrCore_base_dir']}/modules/jrChainedSelect/csv/*.csv");
    if (isset($_library) && is_array($_library)) {
        $_options = array(' - ' => ' - ');
        foreach ($_library as $library) {
            $library            = basename($library);
            $_options[$library] = $library;
        }
        if (count($_options) > 0) {
            $_tmp = array(
                'name'     => 'cs_lib_csv',
                'label'    => 'Library CSV File',
                'help'     => 'Select a CSV file from the library',
                'type'     => 'select',
                'options'  => $_options,
                'required' => false
            );
            jrCore_form_field_create($_tmp);
        }
    }
    jrCore_page_display();
}

//------------------------------
// Import Save
//------------------------------
function view_jrChainedSelect_import_save($_post, $_user, $_conf)
{
    // Must be logged in as admin
    jrUser_session_require_login();
    jrUser_master_only();

    @ini_set('max_execution_time', 7200); // 2 hours max
    @ini_set('memory_limit', '1024M');

    // Make sure we don't already exist
    $nam = jrCore_db_escape(strtolower(strtolower($_post['set_name'])));
    $tbl = jrCore_db_table_name('jrChainedSelect', 'sets');
    $req = "SELECT * FROM {$tbl} WHERE set_name = '{$nam}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        $_rs = array(
            0 => json_decode($_rt['set_options_0'], true),
            1 => json_decode($_rt['set_options_1'], true),
            2 => json_decode($_rt['set_options_2'], true)
        );
    }
    else {
        // Start fresh
        $_rs = array(
            0 => array(),
            1 => array(),
            2 => array()
        );
    }

    // Save CSV file
    jrCore_save_all_media_files('jrChainedSelect', 'import', 0, 1);

    // See if we have uploaded a CSV file (File name is 'jrChainedSelect_{id}_cs_csv . csv')
    $csv_file = jrCore_get_uploaded_media_files('jrChainedSelect', 'cs_csv');
    if (!is_file($csv_file[0])) {
        // Maybe not - perhaps a library file has been selected
        if (is_file(APP_DIR . "/modules/jrChainedSelect/csv/{$_post['cs_lib_csv']}")) {
            $csv_file = APP_DIR . "/modules/jrChainedSelect/csv/{$_post['cs_lib_csv']}";
        }
        else {
            jrCore_set_form_notice('error', "No CSV file uploaded or library file selected");
            jrCore_form_result();
        }
    }
    else {
        $csv_file = $csv_file[0];
    }
    $_csv = file($csv_file);
    if (!$_csv || !is_array($_csv)) {
        jrCore_set_form_notice('error', "No options found in the CSV file selected");
        jrCore_form_result();
    }

    $ky1 = null;
    $ky2 = null;
    foreach ($_csv as $line) {
        $_line = preg_split("/[\t,]/", str_replace('"', '', $line));
        if (isset($_line) && is_array($_line)) {
            foreach ($_line as $k => $v) {
                if ($k == 0) {
                    if (strpos($v, '|')) {
                        list($one, $two) = explode('|', $v);
                        $one          = trim($one);
                        $_rs[0][$one] = trim($two);
                        $ky1          = $one;
                    }
                    else {
                        $v          = trim($v);
                        $_rs[0][$v] = $v;
                        $ky1        = $v;
                    }
                }
                elseif ($k == 1) {
                    if (strpos($v, '|')) {
                        list($one, $two) = explode('|', $v);
                        $one                = trim($one);
                        $_rs[1][$ky1][$one] = trim($two);
                        $ky2                = $one;
                    }
                    else {
                        $v                = trim($v);
                        $_rs[1][$ky1][$v] = $v;
                        $ky2              = $v;
                    }
                }
                else {
                    if (strpos($v, '|')) {
                        list($one, $two) = explode('|', $v);
                        $one                      = trim($one);
                        $_rs[2][$ky1][$ky2][$one] = trim($two);
                    }
                    else {
                        $v                      = trim($v);
                        $_rs[2][$ky1][$ky2][$v] = $v;
                    }
                }
            }
        }
    }
    if (count($_rs[0]) === 0 || count($_rs[1]) === 0) {
        jrCore_set_form_notice('error', "No options found in the CSV file selected (2)");
        jrCore_form_result();
    }

    // Get things sorted
    natcasesort($_rs[0]);
    foreach ($_rs[1] as $k => $v) {
        natcasesort($_rs[1][$k]);
    }
    $lvl = 2;
    $_s0 = jrCore_db_escape(json_encode($_rs[0]));
    $_s1 = jrCore_db_escape(json_encode($_rs[1]));
    $_s2 = '';
    if (isset($_rs[2]) && count($_rs[2]) > 0) {
        foreach ($_rs[2] as $k => $v) {
            foreach ($v as $k1 => $v1) {
                natcasesort($_rs[2][$k][$k1]);
            }
        }
        $lvl = 3;
        $_s2 = jrCore_db_escape(json_encode($_rs[2]));
    }
    if ($_rt && is_array($_rt)) {
        $req = "UPDATE {$tbl} SET set_options_0 = '{$_s0}', set_options_1 = '{$_s1}', set_options_2 = '{$_s2}' WHERE set_name = '{$nam}' LIMIT 1";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (!$cnt || $cnt !== 1) {
            jrCore_set_form_notice('error', "Unable to update existing option set - please try again");
            jrCore_form_result();
        }
    }
    else {
        $req = "INSERT INTO {$tbl} (set_name, set_levels, set_options_0, set_options_1, set_options_2) VALUES ('{$nam}', '{$lvl}', '{$_s0}', '{$_s1}', '{$_s2}')";
        $sid = jrCore_db_query($req, 'INSERT_ID');
        if (!$sid) {
            jrCore_set_form_notice('error', "Unable to create new option set - please try again");
            jrCore_form_result();
        }
    }

    jrCore_delete_media_file(0, "jrChainedSelect_1_cs_csv.csv");
    jrCore_form_delete_session();
    jrProfile_reset_cache();
    $txt = "successfully imported " . count($_csv) . " options from option file: " . basename($csv_file);
    jrCore_logger('INF', $txt);
    jrCore_set_form_notice('success', $txt);
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse");
}

//------------------------------
// get
//------------------------------
function view_jrChainedSelect_get($_post, $_user, $_conf)
{
    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        $_rs = array('error' => 'invalid set name');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['_2']) || !jrCore_checktype($_post['_2'], 'number_nn') || $_post['_2'] > 2) {
        $_rs = array('error' => 'invalid set level');
        jrCore_json_response($_rs);
    }
    $set = jrCore_db_escape($_post['_1']);
    $lvl = (int) $_post['_2'];
    $tbl = jrCore_db_table_name('jrChainedSelect', 'sets');
    $req = "SELECT set_options_{$lvl} FROM {$tbl} WHERE set_name = '{$set}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && isset($_rt["set_options_{$lvl}"])) {
        $_opt = json_decode($_rt["set_options_{$lvl}"], true);
        if (isset($_post['_3']) && $_post['_3'] != '-') {
            if (strpos($_post['_3'], '|')) {
                list($key, $idx) = explode('|', $_post['_3']);
                $key = trim($key);
                $idx = trim($idx);
            }
            else {
                $key = $_post['_3'];
            }
            if (isset($_opt[$key])) {
                if (isset($idx)) {
                    $_tmp = (isset($_opt[$key][$idx])) ? $_opt[$key][$idx] : 'no_data';
                }
                else {
                    $_tmp = $_opt[$key];
                }

                if (!isset($_post['nd'])) {
                    $_out = array(
                        '-' => '-'
                    );
                    if ($_tmp && is_array($_tmp)) {
                        foreach ($_tmp as $k => $v) {
                            $_out[$k] = $v;
                        }
                    }
                    $_out = array(
                        'ok'    => 1,
                        'value' => $_out
                    );
                    jrCore_json_response($_out);
                }
                $_out = array(
                    'ok'    => 1,
                    'value' => implode("\n", $_tmp)
                );
                jrCore_json_response($_out);
            }
        }
        else {
            if (!isset($_post['nd'])) {
                $_out = array(
                    '-' => '-'
                );
                if ($_opt && is_array($_opt)) {
                    foreach ($_opt as $k => $v) {
                        $_out[$k] = $v;
                    }
                }
                $_out = array(
                    'ok'    => 1,
                    'value' => $_out
                );
                jrCore_json_response($_out);
            }
            $_out = array(
                'ok'    => 1,
                'value' => implode("\n", $_opt)
            );
            jrCore_json_response($_out);
        }
    }
    $_out = array('error' => 'no_data');
    jrCore_json_response($_out);
}
