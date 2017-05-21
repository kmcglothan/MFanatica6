<?php
/**
 * Jamroom Site Builder module
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
 * @copyright 2014 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//------------------------------------
// check_reload
//------------------------------------
function view_jrSiteBuilder_check_reload($_post, $_user, $_conf)
{
    jrUser_master_only();
    $_rp = array('reload' => '0');
    if (isset($_SESSION['sb-reload'])) {
        $_rp['reload'] = '1';
        unset($_SESSION['sb-reload']);
    }
    jrCore_json_response($_rp);
}

//------------------------------------
// view_widget
//------------------------------------
function view_jrSiteBuilder_view_widget($_post, $_user, $_conf)
{
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_page_notice('error', 'invalid widget id');
    }
    $out = '';
    $tb1 = jrCore_db_table_name('jrSiteBuilder', 'widget');
    $tb2 = jrCore_db_table_name('jrSiteBuilder', 'page');
    $req = "SELECT * FROM {$tb1} w LEFT JOIN {$tb2} p ON p.page_id = w.widget_page_id WHERE w.widget_id = '{$_post['id']}' LIMIT 1";
    $_wg = jrCore_db_query($req, 'SINGLE');
    if (!$_wg || !is_array($_wg)) {
        jrCore_page_notice('error', 'invalid widget id (2)');
    }
    $_cf = (isset($_wg['page_settings']) && strlen($_wg['page_settings']) > 0) ? json_decode($_wg['page_settings'], true) : array();
    $fnc = "{$_wg['widget_module']}_{$_wg['widget_name']}_display";
    if (function_exists($fnc)) {
        if (isset($_wg['widget_data']) && strlen($_wg['widget_data']) > 1) {
            $_wc = (isset($_cf["{$_wg['widget_location']}"])) ? $_cf["{$_wg['widget_location']}"] : null;
            $_dt = json_decode($_wg['widget_data'], true);
            $out = $fnc($_dt, $_wg, $_wc);
        }
    }
    return $out;
}

//------------------------------------
// widget_order_update
//------------------------------------
function view_jrSiteBuilder_widget_order_update($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    list($page_id, $location) = explode('-location-', $_post['_1']);
    $page_id = (int) str_replace('l', '', $page_id);

    if (!jrCore_checktype($page_id, 'number_nz') || !jrCore_checktype($location, 'number_nn')) {
        jrCore_json_response(array('error' => 'unable to save widget order. page id or location not numeric.'));
    }

    if (!isset($_post['widget_order']) || !is_array($_post['widget_order'])) {
        jrCore_json_response(array('error' => 'no widget order received'));
    }

    // [widget_order] => Array (
    // [0] => "17"
    // [1] => "22"
    // [2] => "44"
    // [3] => "26"
    // [4] => "23"
    // [5] => "20"

    $_up = array();
    $i   = 1;
    // save widgets with a weight, lighter float to top.
    foreach ($_post['widget_order'] as $k => $widget_id) {
        if (jrCore_checktype($widget_id, 'number_nz')) {
            $_up[$i] = $widget_id;
            $i++;
        }
    }
    if (count($_up) > 0) {
        $tbl = jrCore_db_table_name('jrSiteBuilder', 'widget');
        // weight
        $req = "UPDATE {$tbl} SET widget_weight = CASE widget_id\n";
        foreach ($_up as $weight => $wid) {
            $w = $weight * 10;
            $req .= "WHEN {$wid} THEN '{$w}'\n";
        }
        $req .= " ELSE widget_weight END, \n";
        // location
        $req .= " widget_location = CASE widget_id\n";
        foreach ($_up as $wid) {
            $req .= "WHEN {$wid} THEN '{$location}'\n";
        }
        $req .= "ELSE widget_location END
                WHERE widget_id IN (" . implode(',', $_up) . ")";

        jrCore_db_query($req);
        jrCore_delete_all_cache_entries('jrSiteBuilder');
    }
    jrCore_json_response(array('OK' => 1));
}

//------------------------------------
// menu_order_update
//------------------------------------
function view_jrSiteBuilder_menu_order_update($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    // [list[32]] => null
    // [list[34]] => 32
    $ord = 0;
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'menu');
    $req = "UPDATE {$tbl} SET menu_parent_id = CASE menu_id\n";
    $re2 = '';
    foreach ($_post as $k => $v) {
        if (strpos($k, 'list[') === 0) {
            $id = intval(substr($k, 5));
            if ($v == 'null') {
                $v = 0;
            }
            $req .= "WHEN {$id} THEN " . intval($v) . "\n";
            $re2 .= "WHEN {$id} THEN " . $ord++ . "\n";
        }
    }
    $req .= "ELSE menu_parent_id END, menu_order = CASE menu_id\n{$re2}ELSE menu_order END";
    jrCore_db_query($req);
    jrCore_delete_all_cache_entries();
    jrCore_json_response(array('OK' => 1));
}

//------------------------------------
// panel_order_update
//------------------------------------
function view_jrSiteBuilder_panel_order_update($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    if (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {

        $_pg = jrSiteBuilder_get_page_by_id($_post['id']);
        if ($_pg && is_array($_pg)) {
            // [panel_order] => Array (
            // [0] => 0
            // [1] => 2
            // [2] => 1
            $_tm = explode(',', $_pg['page_layout']);
            if ($_tm && is_array($_tm)) {
                $_up = array();
                foreach ($_post['panel_order'] as $k => $v) {
                    $_up[$k] = $_tm[$v];
                }
                $ord = jrCore_db_escape(implode(',', $_up));
                $tbl = jrCore_db_table_name('jrSiteBuilder', 'page');
                $req = "UPDATE {$tbl} SET page_layout = '{$ord}' WHERE page_id = '{$_post['id']}' LIMIT 1";
                jrCore_db_query($req);
                jrCore_delete_all_cache_entries('jrSiteBuilder');
            }
        }

    }
    jrCore_json_response(array('OK' => 1));
}

//------------------------------------
// create_menu_option_save
//------------------------------------
function view_jrSiteBuilder_create_menu_option_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    jrCore_page_set_no_header_or_footer();

    $ttl = jrCore_db_escape($_post['t']);
    if (strpos($_post['t'], '/')) {
        $url = str_replace('~', '/', $_post['t']);
    }
    elseif (strpos($_post['t'], 'www.') === 0) {
        $url = "http://{$_post['t']}";
    }
    else {
        $url = jrCore_url_string($_post['t']);
    }
    $url = jrCore_db_escape($url);
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'menu');
    $req = "SELECT MAX(menu_order) AS m FROM {$tbl}";
    $_rt = jrCore_db_query($req, 'SINGLE');
    $ord = 0;
    if ($_rt && is_array($_rt)) {
        $ord = intval($_rt['m']) + 1;
    }
    $req = "INSERT INTO {$tbl} (menu_updated, menu_parent_id, menu_order, menu_title, menu_url, menu_group) VALUES (UNIX_TIMESTAMP(), 0, '{$ord}', '{$ttl}', '{$url}', 'all')";
    $mid = jrCore_db_query($req, 'INSERT_ID');
    if ($mid && jrCore_checktype($mid, 'number_nz')) {
        jrCore_form_delete_session();
        jrCore_delete_all_cache_entries();
        jrCore_json_response(
            array(
                'id'  => $mid,
                'ttl' => $_post['t']
            )
        );
    }
    jrCore_json_response(array('error' => 'Unable to create new menu entry in database - please try again'));
}

//------------------------------------
// delete_menu_entry_save
//------------------------------------
function view_jrSiteBuilder_delete_menu_entry_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {
        $mid = (int) $_post['id'];
        $_rt = jrSiteBuilder_get_menu_entry_by_id($mid);
        if (!$_rt || !is_array($_rt)) {
            jrCore_json_response(array('error' => 'invalid menu id'));
        }

        $tbl = jrCore_db_table_name('jrSiteBuilder', 'menu');
        $req = "DELETE FROM {$tbl} WHERE menu_id = '{$mid}' OR menu_parent_id = '{$mid}'";
        jrCore_db_query($req);
        jrCore_delete_all_cache_entries();
        jrCore_json_response(array('ok' => 1));
    }
    jrCore_json_response(array('error' => 'invalid menu id'));
}

//------------------------------------
// modify_menu_options
//------------------------------------
function view_jrSiteBuilder_modify_menu_options($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_set_no_header_or_footer();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_page_notice('error', "invalid menu id - please select a menu item");
        return jrCore_page_display(true);
    }

    $_mn = jrSiteBuilder_get_menu_entry_by_id($_post['id']);
    if (!$_mn || !is_array($_mn)) {
        jrCore_page_notice('error', "invalid menu id - please select a menu item (2)");
        return jrCore_page_display(true);
    }
    if (strlen($_mn['menu_group']) === 0) {
        $_mn['menu_group'] = 'all';
    }

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => false,
        'values'       => $_mn
    );
    jrCore_form_create($_tmp);

    // Menu ID
    $_tmp = array(
        'name'  => 'menu_id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    // Title
    $_tmp = array(
        'name'     => 'menu_title',
        'label'    => 'Title',
        'help'     => 'This is the title for the menu item',
        'type'     => 'text',
        'validate' => 'printable',
        'order'    => 1,
        'required' => true,
        'onkeyup'  => 'jrSiteBuilder_modify_title_sync()'
    );
    jrCore_form_field_create($_tmp);

    // URL
    $_tmp = array(
        'name'     => 'menu_url',
        'label'    => 'URL',
        'help'     => 'This is the URL that will be linked to the menu entry',
        'type'     => 'text',
//        'validate' => 'printable',
        'order'    => 2,
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Field Group
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
        'name'     => 'menu_group',
        'label'    => 'menu groups',
        'sublabel' => 'more than 1 group allowed',
        'help'     => 'If you would like this menu item to only be visible to Users in specific Profile Quotas, Profile Admins or Master Admins, select the group(s) here. Use ctrl+click to select multiple.',
        'type'     => 'select_multiple',
        'options'  => $_opt,
        'default'  => 'user',
        'order'    => 3,
        'validate' => 'core_string',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // ONCLICK
    $_tmp = array(
        'name'     => 'menu_onclick',
        'label'    => 'OnClick Code',
        'help'     => 'If you would like to add custom Javascript to the menu onclick handler, enter it here',
        'type'     => 'textarea',
        'validate' => 'printable',
        'order'    => 4,
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    return jrCore_page_display(true);
}

//------------------------------------
// modify_menu_options_save
//------------------------------------
function view_jrSiteBuilder_modify_menu_options_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    if (isset($_post['menu_url']) && strlen($_post['menu_url']) > 0) {
        // Make sure it is either a relative URL OR a full URL
        if (!jrCore_checktype($_post['menu_url'], 'url') && preg_match('/[^A-Za-z0-9_%-#&\/]/', $_post['menu_url']) > 0) {
            jrCore_set_form_notice('error', 'The URL entered is <b>invalid</b> - it must consist of<br> - a single forward slash (to indicate the site index)<br>-a valid full URL (i.e. http://www.example.com)<br>- numbers, letters, forward slashes and dashes only (i.e. audio, audio/newest-files)', false);
            jrCore_form_field_hilight('menu_url');
            jrCore_form_result();
        }
    }

    $mid = (int) $_post['menu_id'];
    $_mn = jrSiteBuilder_get_menu_entry_by_id($mid);

    $ttl      = jrCore_db_escape($_post['menu_title']);
    $menu_url = trim($_post['menu_url'], '/');
    if (strpos($menu_url, '/') !== false) {
        $url = $menu_url;
    }
    elseif (stripos($menu_url, 'www.') === 0) {
        $url = "http://{$menu_url}";
    }
    else {
        $url = $menu_url;
    }
    $url = jrCore_db_escape(trim($url));
    $grp = jrCore_db_escape($_post['menu_group']);
    $onc = jrCore_db_escape($_post['menu_onclick']);

    $tbl = jrCore_db_table_name('jrSiteBuilder', 'menu');
    $req = "UPDATE {$tbl} SET menu_updated = UNIX_TIMESTAMP(), menu_title = '{$ttl}', menu_url = '{$url}', menu_group = '{$grp}', menu_onclick = '{$onc}' WHERE menu_id = '{$mid}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_delete_cache('jrSiteBuilder', 'sb-menu');
        $_rp = array(
            'on_close' => 'jrSiteBuilder_modify_menu_saved'
        );
        // See if we actually changed...
        $changed = false;
        foreach (array('menu_title', 'menu_url', 'menu_group', 'menu_onclick') as $opt) {
            if (!isset($_mn[$opt]) || $_mn[$opt] != $_post[$opt]) {
                $changed = true;
                break;
            }
        }
        if ($changed) {
            $_rp['show_changed'] = '1';
        }
        jrCore_form_delete_session();
        jrCore_delete_all_cache_entries();
        jrCore_json_response($_rp);
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered updating the menu item - please try again');
    }
    jrCore_form_result();
}

//------------------------------------
// modify_menu
//------------------------------------
function view_jrSiteBuilder_modify_menu($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_set_no_header_or_footer();

    // Get existing menu entries
    $_rp = jrSiteBuilder_get_menu_entries(false, false);
    if (!is_array($_rp['_list']) || count($_rp['_list']) == 0) {
        // get the skin provided menu if it exists and save it  as the menu
        $_default = jrSiteBuilder_skin_default_menu_items();
        if (is_array($_default) && count($_default) > 0) {
            $tbl = jrCore_db_table_name('jrSiteBuilder', 'menu');
            jrCore_db_query("TRUNCATE TABLE {$tbl}");

            // top items
            $i = 0;
            foreach ($_default as $_m) {
                // add it to sitebuilder menus
                $url    = jrCore_db_escape($_m['menu_url']);
                $ttl    = jrCore_db_escape($_m['menu_title']);
                $ord    = (isset($_m['menu_order'])) ? $_m['menu_order'] : $i;
                $req    = "INSERT INTO {$tbl} (menu_updated, menu_parent_id, menu_order, menu_title, menu_url, menu_group) VALUES (UNIX_TIMESTAMP(), 0, '{$ord}', '{$ttl}', '{$url}', 'all')";
                $l0_url = jrCore_db_query($req, 'INSERT_ID');
                $i++;
                if (is_array($_m['_children'])) {
                    foreach ($_m['_children'] as $_m1) {
                        // first level.
                        $url    = jrCore_db_escape($_m1['menu_url']);
                        $ttl    = jrCore_db_escape($_m1['menu_title']);
                        $ord    = (isset($_m1['menu_order'])) ? $_m1['menu_order'] : $i;
                        $req    = "INSERT INTO {$tbl} (menu_updated, menu_parent_id, menu_order, menu_title, menu_url, menu_group) VALUES (UNIX_TIMESTAMP(), {$l0_url}, '{$ord}', '{$ttl}', '{$url}', 'all')";
                        $l1_url = jrCore_db_query($req, 'INSERT_ID');
                        $i++;
                        if (is_array($_m1['_children'])) {
                            foreach ($_m1['_children'] as $_m2) {
                                // second level.
                                $url = jrCore_db_escape($_m2['menu_url']);
                                $ttl = jrCore_db_escape($_m2['menu_title']);
                                $ord = (isset($_m2['menu_order'])) ? $_m2['menu_order'] : $i;
                                $req = "INSERT INTO {$tbl} (menu_updated, menu_parent_id, menu_order, menu_title, menu_url, menu_group) VALUES (UNIX_TIMESTAMP(), {$l1_url}, '{$ord}', '{$ttl}', '{$url}', 'all')";
                                jrCore_db_query($req);
                                $i++;
                            }
                        }
                    }
                }
            }
        }
        // retry the get function to get the new entries
        $_rp = jrSiteBuilder_get_menu_entries(false, false);
    }

    $html = jrCore_parse_template('menu_modify.tpl', $_rp, 'jrSiteBuilder');
    jrCore_page_custom($html);

    return jrCore_page_display(true);
}

//------------------------------------
// modify_menu_item_save
//------------------------------------
function view_jrSiteBuilder_modify_menu_item_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    $mid = (int) $_post['id'];
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'menu');
    $req = "UPDATE {$tbl} SET menu_title = '" . jrCore_db_escape($_post['t']) . "' WHERE menu_id = '{$mid}' LIMIT 1";
    jrCore_db_query($req);
    usleep(50000);
    jrCore_form_delete_session();
    jrCore_delete_all_cache_entries();
    jrCore_delete_cache('jrSiteBuilder', 'sb-menu');
    jrCore_json_response(array('OK' => 1));
}

//------------------------------------
// create_menu_item_save
//------------------------------------
function view_jrSiteBuilder_create_menu_item_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nn')) {
        jrCore_json_response(array('error' => 'invalid menu parent id'));
    }
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'menu');
    $pid = (int) $_post['p'];

    // Get parent
    if ($pid > 0) {
        $_pr = jrSiteBuilder_get_menu_entry_by_id($pid);
        if (!$_pr || !is_array($_pr)) {
            jrCore_json_response(array('error' => 'invalid menu parent id'));
        }
        $url = "{$_pr['menu_url']}/" . jrCore_url_string($_post['t']);
    }
    else {
        $url = jrCore_url_string($_post['t']);
    }

    // Make sure there is NOT already a page using this URL
    $_ex = jrSiteBuilder_get_menu_entry_by_uri($url);
    if ($_ex && is_array($_ex)) {
        jrCore_json_response(array('error' => 'There is already a menu entry using that title - please enter another'));
    }

    // Figure our order
    $ord = 0;
    $req = "SELECT MAX(menu_order) AS m FROM {$tbl} WHERE menu_parent_id = '{$pid}'";
    $_mx = jrCore_db_query($req, 'SINGLE');
    if ($_mx && is_array($_mx)) {
        $ord = (int) ($_mx['m'] + 1);
    }

    $ttl = jrCore_db_escape($_post['t']);
    $req = "INSERT INTO {$tbl} (menu_updated, menu_parent_id, menu_order, menu_title, menu_url) VALUES (UNIX_TIMESTAMP(), '{$pid}', '{$ord}', '{$ttl}', '{$url}')";
    $iid = jrCore_db_query($req, 'INSERT_ID');
    sleep(1);
    jrCore_form_delete_session();
    jrCore_delete_all_cache_entries();
    jrCore_json_response(array('id' => $iid));
}

//------------------------------------
// create_page_save
//------------------------------------
function view_jrSiteBuilder_create_page_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    $uri = trim($_post['page_url'], '/');
    $uri = jrCore_strip_url_params($uri, array('p'));
    $uri = str_replace(trim($_conf['jrCore_base_url'], '/'), '', $uri);
    if (strlen($uri) === 0) {
        $uri = '/';
    }

    // Make sure this URI does not exist
    $_pg = jrSiteBuilder_get_page_by_uri($uri);
    if (!$_pg) {
        if ($pid = jrSiteBuilder_install_page_from_json($_conf['jrCore_active_skin'], $uri)) {
            jrCore_json_response(array('pid' => $pid));
        }
    }

    if ($_pg && is_array($_pg)) {
        // already exists
        jrCore_json_response(array('error' => 'page already exists'));
    }

    // Create the Page
    $_rp = array(
        '-' => ' ',
        '_' => ' ',
        '/' => ' '
    );
    $ttl = jrCore_db_escape(str_replace(array_keys($_rp), $_rp, trim($uri, '/')));

    $tbl = jrCore_db_table_name('jrSiteBuilder', 'page');
    $req = "INSERT INTO {$tbl} (page_updated, page_uri, page_title, page_groups, page_active, page_layout, page_settings, page_head)
            VALUES (UNIX_TIMESTAMP(), '" . jrCore_db_escape($uri) . "', '{$ttl}', 'all', '1', '4-4-4', '', '')";
    $pid = jrCore_db_query($req, 'INSERT_ID');
    if (!$pid || !jrCore_checktype($pid, 'number_nz')) {
        jrCore_json_response(array('error' => 'unable to create page in database - please try again'));
    }

    jrCore_logger('INF', "new page {$uri} successfully created");
    jrCore_json_response(array('pid' => $pid));
}

//------------------------------------
// modify_page_settings
//------------------------------------
function view_jrSiteBuilder_modify_page_settings($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_set_no_header_or_footer();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_page_notice('error', 'invalid page_id');
        return jrCore_page_display(true);
    }

    // Get Page info
    $_pg = jrSiteBuilder_get_page_by_id($_post['id']);
    if (!isset($_pg['page_groups']) || strlen($_pg['page_groups']) === 0) {
        $_pg['page_groups'] = 'all';
    }

    $button = jrCore_page_button('l', 'page layout', "jrSiteBuilder_modify_page_layout('{$_post['id']}')");
    $button .= jrCore_page_button('c', 'close', 'jrSiteBuilder_modal_close()');
    jrCore_page_banner('page settings', $button);

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => false,
        'values'       => $_pg
    );
    jrCore_form_create($_tmp);

    // Page ID
    $_tmp = array(
        'name'  => 'page_id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'page_title',
        'label'    => 'page title',
        'help'     => 'Enter a title for this page',
        'type'     => 'text',
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'page_uri',
        'label'    => 'page URL',
        'help'     => 'Enter the url that this page is found at - it must begin with a / (forward slash).',
        'type'     => 'text',
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // Display Groups
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
        'name'     => 'page_groups',
        'label'    => 'display groups',
        'sublabel' => 'more than 1 group allowed',
        'help'     => 'If you would like this page to only be accessible to Users in specific Profile Quotas, Profile Admins or Master Admins, select the group(s) here. Use ctrl+click to select multiple.',
        'type'     => 'select_multiple',
        'options'  => $_opt,
        'default'  => 'all',
        'validate' => 'core_string'
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'page_head',
        'label'    => 'Page Head HTML',
        'help'     => 'If you have special code that needs to go in the &lt;head&gt;&lt/head&gt; section of this page, put that code in here.',
        'type'     => 'textarea',
        'validate' => 'allowed_html',
        'required' => false,
    );
    jrCore_form_field_create($_tmp);

    // add a way to export this page in .json format for including in the skin
    if (jrCore_is_developer_mode()) {
        $iurl  = jrCore_get_module_url('jrImage');
        $title = ($_pg['page_uri'] == '/') ? '/index' : $_pg['page_uri'];
        $html  = jrCore_page_button('jsonexport', "Save Page to skins/{$_conf['jrCore_active_skin']}/sitebuilder{$title}.json", "jrSiteBuilder_save_page_as_json('{$_post['id']}')") . '&nbsp;<img id="sb-json-spinner" src="' . $_conf['jrCore_base_url'] . '/' . $iurl . '/img/skin/' . $_conf['jrCore_active_skin'] . '/form_spinner.gif" width="24" height="24" alt="working..." style="display:none;vertical-align:middle"><div id="sb-json-message" class="page_notice" style="display:none"><!-- success message loads here --></div>';
        $_tmp  = array(
            'module' => 'jrSiteBuilder',
            'name'   => 'jrSiteBuilder_export_page',
            'label'  => 'Save Page Config',
            'help'   => "The current page will be saved as a JSON file in skins/{$_conf['jrCore_active_skin']}/sitebuilder where it can be imported on another site",
            'html'   => $html
        );
        jrCore_form_field_custom_display($_tmp);
    }
    return jrCore_page_display(true);
}

//------------------------------------
// modify_page_settings_save
//------------------------------------
function view_jrSiteBuilder_modify_page_settings_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    $_pg = jrSiteBuilder_get_page_by_id($_post['page_id']);
    if (!$_pg || !is_array($_pg)) {
        jrCore_set_form_notice('error', 'invalid page_id');
        jrCore_form_result();
    }

    $uri = $_post['page_uri'];
    if (strpos($uri, '/') !== 0) {
        jrCore_set_form_notice('error', 'URI must begin with a / and contain only numbers letters - and _ no spaces.');
        jrCore_form_result();
    }

    // Make sure this URI does not exist
    if ($_pg['page_uri'] != $uri) {
        $_pg = jrSiteBuilder_get_page_by_uri($uri);
        if ($_pg && is_array($_pg)) {
            // already exist
            jrCore_set_form_notice('error', 'Page by that name already exists, choose another url');
            jrCore_form_result();
        }
    }

    // [page_id] => 3
    // [page_groups] => master,admin

    $ttl = jrCore_db_escape($_post['page_title']);
    $grp = jrCore_db_escape($_post['page_groups']);
    $hed = jrCore_db_escape($_post['page_head']);
    $uri = jrCore_db_escape($uri);
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'page');
    $req = "UPDATE {$tbl} SET page_updated = UNIX_TIMESTAMP(), page_title = '{$ttl}', page_uri = '{$uri}', page_groups = '{$grp}', page_head = '{$hed}' WHERE page_id = '{$_post['page_id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_form_delete_session();
        jrCore_delete_all_cache_entries('jrSiteBuilder');
        $_rp = array(
            'on_close' => 'jrSiteBuilder_close_page_modal'
        );
        if ($_pg['page_title'] != $_post['page_title'] || $_pg['page_groups'] != $_post['page_groups']) {
            $_SESSION['sb-reload'] = 1;
            $_rp['show_changed']   = 1;
        }
        if ($_pg['page_uri'] != $uri) {
            jrCore_form_result("{$_conf['jrCore_base_url']}{$uri}");
        }
        jrCore_json_response($_rp);
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered saving the page settings - please try again');
    }
    jrCore_form_result("{$_conf['jrCore_base_url']}{$uri}");
}

//------------------------------------
// modify_page
//------------------------------------
function view_jrSiteBuilder_modify_page($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {
        $_pg = jrSiteBuilder_get_page_by_id($_post['id']);
        if (!$_pg || !is_array($_pg)) {
            jrCore_notice('error', 'invalid page_id');
        }

        // Get existing layout
        $_pg['_existing_layout'] = array();
        foreach (explode(',', $_pg['page_layout']) as $k => $row) {
            $_pg['_existing_layout'][$k] = array();
            $_tm                         = explode('-', $row);
            if ($_tm && is_array($_tm)) {
                foreach ($_tm as $col => $num) {
                    $_pg['_existing_layout'][$k][$col]['width'] = $num;
                }
            }
        }
        $_pg['page_row_count'] = count($_pg['_existing_layout']);
        return jrCore_parse_template('page_layout.tpl', $_pg, 'jrSiteBuilder');
    }
    jrCore_notice('error', 'invalid item_id');
    return false;
}

//------------------------------------
// modify_page_save
//------------------------------------
function view_jrSiteBuilder_modify_page_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (isset($_post['new-layout']) && is_array($_post['new-layout'])) {
        $out = jrCore_db_escape(implode(',', $_post['new-layout']));
        $tbl = jrCore_db_table_name('jrSiteBuilder', 'page');
        $req = "UPDATE {$tbl} SET page_layout = '{$out}' WHERE page_id = '{$_post['id']}' LIMIT 1";
        jrCore_db_query($req);
        $_rp = array(
            'on_close'     => 'jrSiteBuilder_close_page_modal',
            'show_changed' => 1
        );
        jrCore_delete_all_cache_entries('jrSiteBuilder');
        jrCore_json_response($_rp);
    }
    $_SESSION['sb-reload'] = 1;
    jrCore_delete_all_cache_entries('jrSiteBuilder');
    jrCore_json_response(array('OK' => 1));
}

//------------------------------------
// delete_page_save
//------------------------------------
function view_jrSiteBuilder_delete_page_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {

        // Remove Page
        $tbl = jrCore_db_table_name('jrSiteBuilder', 'page');
        $req = "DELETE FROM {$tbl} WHERE page_id = '{$_post['id']}' LIMIT 1";
        jrCore_db_query($req);

        // Remove Widgets
        $tbl = jrCore_db_table_name('jrSiteBuilder', 'widget');
        $req = "DELETE FROM {$tbl} WHERE widget_page_id = '{$_post['id']}'";
        jrCore_db_query($req);
        jrCore_delete_all_cache_entries('jrSiteBuilder');
        jrCore_json_response(array('OK' => 1));
    }
    jrCore_json_response(array('error' => 'invalid page_id - please try again'));
}

//------------------------------------
// modify_container
//------------------------------------
function view_jrSiteBuilder_modify_container($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_set_no_header_or_footer();
    if (!isset($_post['html_id']) || strlen($_post['html_id']) === 0) {
        jrCore_notice('error', 'invalid container html id');
    }

    // [html_id] => 1-location-0
    // [html_id] => 1-location-4
    list($pid, , $loc) = explode('-', $_post['html_id']);
    $_pg = jrSiteBuilder_get_page_by_id($pid);
    if (!$_pg || !is_array($_pg)) {
        jrCore_notice('error', 'invalid container html id');
    }

    $button = jrCore_page_button('c', 'close', 'jrSiteBuilder_modal_close()');
    jrCore_page_banner('modify container settings', $button);

    // Get our Config
    $_tmp = false;
    if (isset($_pg['page_settings']) && strlen($_pg['page_settings']) > 0) {
        $_tmp = json_decode($_pg['page_settings'], true);
    }
    if (isset($_tmp[$loc]['ct_style']{1})) {
        $_tmp[$loc]['ct_style'] = str_replace(' !important', '', $_tmp[$loc]['ct_style']);
    }

    // Form init
    $_tmp = array(
        'name'         => 'jrSiteBuilder_modify_container_form',
        'submit_value' => 'save changes',
        'cancel'       => false,
        'values'       => (isset($_tmp[$loc])) ? $_tmp[$loc] : array()
    );
    jrCore_form_create($_tmp);

    // Page ID
    $_tmp = array(
        'name'  => 'page_id',
        'type'  => 'hidden',
        'value' => $pid
    );
    jrCore_form_field_create($_tmp);

    // Location
    $_tmp = array(
        'name'  => 'location',
        'type'  => 'hidden',
        'value' => $loc
    );
    jrCore_form_field_create($_tmp);

    $_opt = array(
        'stack' => 'Stacked - widget output is stacked vertically inside the container',
        'tab'   => 'Tabbed - each widget is located on its own tab inside the container'
    );

    $_tmp = array(
        'name'     => 'ct_layout',
        'label'    => 'container layout',
        'help'     => 'Select the style of layout you would like for the widgets in this container:<br><br><b>Stacked:</b> Widget output will be &quot;stacked&quot; on top of each other.<br><br><b>Tabbed:</b> Each widget will have its own tab in the container.',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 'stack',
        'validate' => 'core_string',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'       => 'ct_height',
        'label'      => 'container height',
        'sublabel'   => '(in pixels)',
        'help'       => 'Enter the height (in pixels) for this container - leave empty or set to 0 (zero) to have the container automatically adjust itself to the height of the content.',
        'type'       => 'text',
        'default'    => '',
        'validate'   => 'number_nz',
        'required'   => false,
        'onkeypress' => 'if (event && event.keyCode == 13) return false;'
    );
    jrCore_form_field_create($_tmp);

// Plannded feature thats yet to be built
//    $_tmp = array(
//        'name'     => 'ct_unique',
//        'label'    => 'Container Id',
//        'help'     => 'Use this unique id to show this containers widgets on multiple pages.',
//        'type'     => 'text',
//        'default'  => '',
//        'validate' => 'core_string',
//        'required' => false
//    );
//    jrCore_form_field_create($_tmp);

    return jrCore_page_display(true);
}

//------------------------------------
// modify_container_save
//------------------------------------
function view_jrSiteBuilder_modify_container_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    $_pg = jrSiteBuilder_get_page_by_id($_post['page_id']);
    if (!$_pg || !is_array($_pg)) {
        jrCore_set_form_notice('error', 'invalid page_id');
        jrCore_form_result();
    }
    $_tmp = array();
    if (isset($_pg['page_settings']) && strlen($_pg['page_settings']) > 0) {
        $_tmp = json_decode($_pg['page_settings'], true);
    }

    // Cleanup Container Style
    if (isset($_post['ct_style']) && strlen($_post['ct_style']) > 0) {
        $_st = array();
        foreach (explode("\n", $_post['ct_style']) as $v) {
            $v = trim($v);
            if (strlen($v) > 0) {
                $v     = str_replace(array(';', 'important', '!important'), '', $v);
                $_st[] = "{$v} !important;";
            }
        }
        if (count($_st) > 0) {
            $_post['ct_style'] = implode("\n", $_st);
        }
        else {
            $_post['ct_style'] = '';
        }
    }

    $loc        = (int) $_post['location'];
    $_tmp[$loc] = array();
    foreach ($_post as $k => $v) {
        if (strpos($k, 'ct_') === 0) {
            $_tmp[$loc][$k] = $v;
        }
    }
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'page');
    $req = "UPDATE {$tbl} SET page_settings = '" . jrCore_db_escape(json_encode($_tmp)) . "' WHERE page_id = '{$_post['page_id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!$cnt || $cnt !== 1) {
        jrCore_set_form_notice('error', 'unable to save container settings - please try again');
        jrCore_form_result();
    }
    jrCore_form_delete_session();
    jrCore_delete_all_cache_entries('jrSiteBuilder');
    $_rp = array(
        'on_close' => 'jrSiteBuilder_close_container_modal'
    );
    if (json_encode($_tmp) != $_pg['page_settings']) {
        $_rp['location'] = $loc;
    }
    jrCore_json_response($_rp);
}

//------------------------------------
// modify_widget
//------------------------------------
function view_jrSiteBuilder_modify_widget($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_set_no_header_or_footer();

    $html = '';
    list(, $wid) = explode('-', $_post['html_id']);
    if (!isset($wid) || !jrCore_checktype($wid, 'number_nn')) {
        jrCore_json_response(array('error' => 'invalid widget html_id - please try again'));
    }
    else {

        // Get all registered Widgets
        $_tm = jrCore_get_registered_module_features('jrSiteBuilder', 'widget');

        $_rp = array(
            '_widgets'            => array(),
            'widget_form_content' => '',
        );
        // Get our unique Widget by it's unique HTML ID
        $_rp['_widget'] = jrSiteBuilder_get_widget_by_id($wid);
        if ($_rp['_widget'] && is_array($_rp['_widget'])) {
            $wid = $_rp['_widget']['widget_id'];
        }

        // Next, get all registered Widgets
        if ($_tm && is_array($_tm)) {
            // [jrSiteBuilder] => Array (
            //     [widget_html] => HTML Editor
            //     [widget_code] => Template Code
            // )
            $i = 0;
            foreach ($_tm as $mod => $_widgets) {
                foreach ($_widgets as $name => $title) {

                    // Check for widget features
                    if (is_array($title)) {
                        $ttl = $title['title'];
                        // Check for Requires
                        if (isset($title['requires']) && strlen($title['requires']) > 0) {
                            // This widget requires other modules - check for those
                            foreach (explode(',', $title['requires']) as $mdl) {
                                if (!jrCore_module_is_active(trim($mdl))) {
                                    // Requirement not met
                                    continue 2;
                                }
                            }
                        }
                    }
                    else {
                        // We only have a Title for this one
                        $ttl = $title;
                    }

                    $_rp['_widgets'][$ttl] = array(
                        'widget_id' => $wid,
                        'module'    => $mod,
                        'name'      => $name,
                        'title'     => jrCore_entity_string($ttl),
                        'icon'      => "{$_conf['jrCore_base_url']}/modules/{$mod}/icon.png"
                    );
                    if (isset($_rp['_widget']['widget_id'])) {
                        if ($_rp['_widget']['widget_module'] == $mod && $_rp['_widget']['widget_name'] == $name) {
                            $_rp['_widgets'][$ttl]['active'] = '1';
                        }
                    }
                    elseif ($mod == 'jrSiteBuilder' && $name == 'widget_html') {
                        $_rp['_widgets'][$ttl]['active'] = '1';
                    }
                    $i++;
                }
            }
            ksort($_rp['_widgets']);
        }
        $html = jrCore_parse_template('widget_modify.tpl', $_rp, 'jrSiteBuilder');

    }
    jrCore_page_custom($html);
    jrCore_page_display();
}

//------------------------------------
// modify_widget_settings
//------------------------------------
function view_jrSiteBuilder_modify_widget_settings($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_set_no_header_or_footer();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_page_notice('error', 'invalid widget_id');
        return jrCore_page_display(true);
    }

    // We got unique HTML id
    $_wg = jrSiteBuilder_get_widget_by_id($_post['id']);
    if (!isset($_wg['widget_groups']) || strlen($_wg['widget_groups']) === 0) {
        $_wg['widget_groups'] = 'all';
    }

    $button = jrCore_page_button('s', 'widget content', 'jrSiteBuilder_close_widget_settings()');
    $button .= jrCore_page_button('c', 'close', 'jrSiteBuilder_modal_close()');
    jrCore_page_banner('widget settings', $button);

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'values'       => $_wg
    );
    jrCore_form_create($_tmp);

    // Widget ID
    $_tmp = array(
        'name'  => 'widget_id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    // Display Groups
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
        'name'     => 'widget_groups',
        'label'    => 'display groups',
        'sublabel' => 'more than 1 group allowed',
        'help'     => 'If you would like this widget to only be visible to Users in specific Profile Quotas, Profile Admins or Master Admins, select the group(s) here. Use ctrl+click to select multiple.',
        'type'     => 'select_multiple',
        'options'  => $_opt,
        'default'  => 'all',
        'validate' => 'core_string'
    );
    jrCore_form_field_create($_tmp);

    return jrCore_page_display(true);
}

//------------------------------------
// modify_widget_settings_save
//------------------------------------
function view_jrSiteBuilder_modify_widget_settings_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    $_wg = jrSiteBuilder_get_widget_by_id($_post['widget_id']);

    // [widget_id] => 38
    // [widget_groups] => master,admin

    $grp = jrCore_db_escape($_post['widget_groups']);
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'widget');
    $req = "UPDATE {$tbl} SET widget_updated = UNIX_TIMESTAMP(), widget_groups = '{$grp}' WHERE widget_id = '{$_post['widget_id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_form_delete_session();
        jrCore_delete_all_cache_entries('jrSiteBuilder');
        $_rp = array(
            'on_close' => 'jrSiteBuilder_close_widget_settings'
        );
        if ($_wg['widget_groups'] != $_post['widget_groups']) {
            $_rp['widget_id'] = (int) $_post['widget_id'];
        }
        jrCore_json_response($_rp);
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered saving the widget settings - please try again');
    }
    jrCore_form_result();
}

//------------------------------------
// modify_widget_form
//------------------------------------
function view_jrSiteBuilder_modify_widget_form($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_set_no_header_or_footer();

    list(, $wid) = explode('-', $_post['html_id']);
    if (!isset($wid) || !jrCore_checktype($wid, 'number_nz')) {
        jrCore_page_notice('error', 'jrSiteBuilder_modify_widget_form: invalid widget html_id - please try again');
        return jrCore_page_display(true);
    }

    // We got unique HTML id
    $_wg = jrSiteBuilder_get_widget_by_id($wid);

    // Get all registered Widgets
    $_tm = jrCore_get_registered_module_features('jrSiteBuilder', 'widget');

    $mod = (isset($_post['m'])) ? $_post['m'] : $_wg['widget_module'];
    $nam = (isset($_post['n'])) ? $_post['n'] : $_wg['widget_name'];
    $fnc = "{$mod}_{$nam}_config";
    if (!isset($_tm[$mod][$nam])) {
        jrCore_page_notice('error', 'widget module not active');
        return jrCore_page_display(true);
    }
    elseif (!function_exists($fnc)) {
        jrCore_page_notice('error', 'widget function not registered');
        return jrCore_page_display(true);
    }

    // Combine widget and widget_data
    if ($_wg && is_array($_wg)) {
        if (isset($_wg['widget_data']) && is_array($_wg['widget_data'])) {
            $_wd = $_wg['widget_data'];
            unset($_wg['widget_data']);
            $_wg = array_merge($_wg, $_wd);
            unset($_wd);
        }
    }

    $button = jrCore_page_button('s', 'widget settings', "jrSiteBuilder_modify_widget_settings('{$wid}')");
    $button .= jrCore_page_button('c', 'close', 'jrSiteBuilder_modal_close()');
    jrCore_page_banner('widget content', $button);

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => false,
        'values'       => $_wg
    );
    jrCore_form_create($_tmp);

    // Widget ID
    $wid = 0;
    if ($_wg && is_array($_wg)) {
        $wid = (int) $_wg['widget_id'];
    }
    $_tmp = array(
        'name'  => 'widget_id',
        'type'  => 'hidden',
        'value' => $wid
    );
    jrCore_form_field_create($_tmp);

    // Widget Module
    $_tmp = array(
        'name'  => 'widget_page_id',
        'type'  => 'hidden',
        'value' => (int) $_wg['widget_page_id']
    );
    jrCore_form_field_create($_tmp);

    // Widget Module
    $_tmp = array(
        'name'  => 'widget_module',
        'type'  => 'hidden',
        'value' => $mod
    );
    jrCore_form_field_create($_tmp);

    // Widget Name
    $_tmp = array(
        'name'  => 'widget_name',
        'type'  => 'hidden',
        'value' => $nam
    );
    jrCore_form_field_create($_tmp);

    // Title
    $_tmp = array(
        'name'          => 'widget_title',
        'label'         => 'Widget Title',
        'help'          => 'Enter the TITLE for this widget - if entered, it will be shown ABOVE the Widget Content.',
        'type'          => 'text',
        'validate'      => 'allowed_html',
        'order'         => 1,
        'form_designer' => false,
        'required'      => false,
        'onkeypress'    => 'if (event && event.keyCode == 13) return false;'
    );
    jrCore_form_field_create($_tmp);

    // Run widget config function
    $fnc($_post, $_user, $_conf, $_wg);

    return jrCore_page_display(true);
}

//------------------------------------
// modify_widget_form_save
//------------------------------------
function view_jrSiteBuilder_modify_widget_form_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['widget_id']) || !jrCore_checktype($_post['widget_id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid widget id');
        jrCore_form_result();
    }
    $_wg = jrSiteBuilder_get_widget_by_id($_post['widget_id']);

    // [widget_id] => 0
    // [widget_order] => 0:2
    // [widget_module] => jrSiteBuilder
    // [widget_name] => widget_html
    // [widget_title] => test
    // [widget_data] => test

    $fnc = "{$_post['widget_module']}_{$_post['widget_name']}_config_save";
    if (function_exists($fnc)) {
        $_sv = $fnc($_post);
        // We need to test the title and make sure it does not cause any Smarty errors
        if (jrSiteBuilder_template_code_contains_errors($_post['widget_title'])) {
            jrCore_set_form_notice('error', 'There is a Smarty syntax error in your TITLE - please fix and try again');
            jrCore_form_result();
        }

        $wid = 0;
        if ($_sv && is_array($_sv)) {
            $wid = $_post['widget_id'];
            $dat = jrCore_db_escape(json_encode($_sv));
            $ttl = jrCore_db_escape($_post['widget_title']);
            $mod = jrCore_db_escape($_post['widget_module']);
            $nam = jrCore_db_escape($_post['widget_name']);
            $tbl = jrCore_db_table_name('jrSiteBuilder', 'widget');
            if (jrCore_checktype($_post['widget_id'], 'number_nz')) {
                $req = "UPDATE {$tbl} SET widget_updated = UNIX_TIMESTAMP(), widget_title = '{$ttl}', widget_module = '{$mod}', widget_name = '{$nam}', widget_data = '{$dat}' WHERE widget_id = '{$wid}' LIMIT 1";
                $cnt = jrCore_db_query($req, 'COUNT');
                if (!$cnt || $cnt !== 1) {
                    jrCore_set_form_notice('error', 'error saving widget changes to database - please try again');
                    jrCore_form_result();
                }
            }
            else {
                $req = "INSERT INTO {$tbl} (widget_updated, widget_title, widget_module, widget_name, widget_data) VALUES (UNIX_TIMESTAMP(), '{$ttl}', '{$mod}', '{$nam}', '{$dat}')";
                $wid = jrCore_db_query($req, 'COUNT');
                if (!$wid || !jrCore_checktype($wid, 'number_nz')) {
                    jrCore_set_form_notice('error', 'error creating new widget in database - please try again');
                    jrCore_form_result();
                }
            }
        }

        jrCore_form_delete_session();

        // Get all registered Widgets
        $name = '&nbsp;';
        $_tm  = jrCore_get_registered_module_features('jrSiteBuilder', 'widget');
        if (isset($_tm["{$_post['widget_module']}"]["{$_post['widget_name']}"])) {
            if (is_array($_tm["{$_post['widget_module']}"]["{$_post['widget_name']}"])) {
                $name = $_tm["{$_post['widget_module']}"]["{$_post['widget_name']}"]['title'];
            }
            else {
                $name = $_tm["{$_post['widget_module']}"]["{$_post['widget_name']}"];
            }
            $name = ($_wg['widget_location'] + 1) . '.' . $_wg['widget_weight'] . " {$name}";
        }

        // If we are changing widgets, we need to refresh on Site Builder close
        $_rp = array(
            'on_close'  => 'jrSiteBuilder_close_widget_modal',
            'title'     => (strlen($_post['widget_title']) > 0) ? jrCore_parse_template($_post['widget_title'], $_post, 'jrSiteBuilder') : '',
            'widget_id' => $wid,
            'name'      => $name
        );
        if (!isset($_wg['widget_module']) || "{$_wg['widget_module']}-{$_wg['widget_name']}" != "{$_post['widget_module']}-{$_post['widget_name']}") {
            $_SESSION['sb-reload'] = 1;
            $_rp['widget_id']      = (int) $_post['widget_id'];
        }
        elseif ($_wg['widget_title'] != $_post['widget_title']) {
            $_SESSION['sb-reload'] = 1;
            $_rp['widget_id']      = (int) $_post['widget_id'];
        }
        elseif (json_encode($_wg['widget_data']) != json_encode($_sv)) {
            $_rp['widget_id'] = (int) $_post['widget_id'];
        }
        jrCore_delete_all_cache_entries('jrSiteBuilder');
        jrCore_json_response($_rp);
    }
    jrCore_set_form_notice('error', 'invalid widget - config_save function missing');
    jrCore_form_result();
}

//------------------------------------
// delete_widget_save
//------------------------------------
function view_jrSiteBuilder_delete_widget_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    // ID will look like: "widget_id-18"
    list(, $wid) = explode('-', $_post['html_id']);
    if (!isset($wid) || !jrCore_checktype($wid, 'number_nz')) {
        jrCore_json_response(array('error' => 'invalid widget_id - please try again'));
    }

    if (isset($wid) && jrCore_checktype($wid, 'number_nz')) {
        $_wg = jrSiteBuilder_get_widget_by_id($wid);

        if ($_wg && is_array($_wg)) {
            // Remove Widget
            $tbl = jrCore_db_table_name('jrSiteBuilder', 'widget');
            $req = "DELETE FROM {$tbl} WHERE widget_id = '{$wid}' LIMIT 1";
            jrCore_db_query($req);

            jrCore_delete_all_cache_entries('jrSiteBuilder');
            jrCore_json_response(array('OK' => 1));
        }
    }
    jrCore_json_response(array('error' => 'invalid widget_id - please try again'));
}

//------------------------------------
// Add a new Widget (ajax)
//------------------------------------
function view_jrSiteBuilder_widget_create($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    // page
    if (!isset($_post['page_id']) || !jrCore_checktype($_post['page_id'], 'number_nz')) {
        jrCore_json_response(array('error' => 'invalid page_id - please try again'));
    }
    // count
    if (!isset($_post['ct']) || !jrCore_checktype($_post['ct'], 'number_nn')) {
        jrCore_json_response(array('error' => 'count of existing widgets not set - please try again'));
    }
    // location
    if (!isset($_post['location']) || !jrCore_checktype($_post['location'], 'number_nn')) {
        jrCore_json_response(array('error' => 'location not set - please try again'));
    }

    // Create
    $pid       = (int) $_post['page_id'];
    $loc       = (int) $_post['location'];
    $mod       = 'jrSiteBuilder';
    $nam       = 'widget_html';
    $weight    = $_post['ct'] + 1;
    $tbl       = jrCore_db_table_name('jrSiteBuilder', 'widget');
    $unique_id = jrCore_create_unique_string(20);
    $req       = "INSERT INTO {$tbl} (widget_updated, widget_page_id, widget_location, widget_weight, widget_title, widget_module, widget_name, widget_data, widget_unique) VALUES (UNIX_TIMESTAMP(), '{$pid}', '{$loc}', '{$weight}', '', '{$mod}', '{$nam}', '', '{$unique_id}')";
    $wid       = jrCore_db_query($req, 'INSERT_ID');
    if (!$wid || !jrCore_checktype($wid, 'number_nz')) {
        jrCore_set_form_notice('error', 'error creating new widget in database - please try again');
        jrCore_form_result();
    }

    // keeping the same .tpl structure as other locations.
    $_rw = jrCore_get_registered_module_features('jrSiteBuilder', 'widget');
    $_rp = array(
        '_widget' => array(
            'widget_id'    => $wid,
            'widget_title' => ($loc + 1) . '. ' . $_rw[$mod][$nam],
            'no_title'     => 1
        )
    );

    $out = jrCore_parse_template('widget_create.tpl', $_rp, 'jrSiteBuilder');
    $_rs = array(
        'OK'          => 1,
        'widget_html' => $out,
        'widget_id'   => $wid
    );
    jrCore_json_response($_rs, true, false);
}

//------------------------------------
// Clone an existing Widget (ajax)
//------------------------------------
function view_jrSiteBuilder_widget_clone($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['widget_id']) || !jrCore_checktype($_post['widget_id'], 'number_nz')) {
        jrCore_json_response(array('error' => 'invalid widget_id - please try again'));
    }

    // get the widget
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'widget');
    $req = "SELECT * FROM {$tbl} WHERE widget_id = '{$_post['widget_id']}' LIMIT 1";
    $_wg = jrCore_db_query($req, 'SINGLE');
    if (!$_wg || !is_array($_wg)) {
        jrCore_json_response(array('error' => 'widget with that id not found - please try again'));
    }

    // Create
    $weight = (int) $_wg['widget_weight'] + 1;
    $req    = "INSERT INTO {$tbl} (widget_updated, widget_page_id, widget_title, widget_location, widget_weight, widget_module, widget_name, widget_data, widget_groups) (
                   SELECT widget_updated, widget_page_id, widget_title, widget_location, {$weight}, widget_module, widget_name, widget_data, widget_groups FROM {$tbl} WHERE widget_id = '{$_post['widget_id']}'
               )";
    $wid    = jrCore_db_query($req, 'INSERT_ID');
    if (!$wid || !jrCore_checktype($wid, 'number_nz')) {
        jrCore_json_response(array('error' => 'error creating new widget in database - please try again'));
    }

    $_rw = jrCore_get_registered_module_features('jrSiteBuilder', 'widget');
    if (isset($_wg['widget_title']) && strlen($_wg['widget_title']) > 0) {
        $nam = $_wg['widget_title'];
        $ttl = 0;
    }
    else {
        $nam = (is_array($_rw["{$_wg['widget_module']}"]["{$_wg['widget_name']}"])) ? $_rw["{$_wg['widget_module']}"]["{$_wg['widget_name']}"]['title'] : $_rw["{$_wg['widget_module']}"]["{$_wg['widget_name']}"];
        $ttl = 1;
    }
    $_rp = array(
        '_widget' => array(
            'widget_id'    => $wid,
            'widget_title' => $nam,
            'no_title'     => $ttl
        )
    );

    $out  = jrCore_parse_template('widget_create.tpl', $_rp, 'jrSiteBuilder');
    $_res = array(
        'OK'          => 1,
        'widget_html' => $out,
        'widget_id'   => $wid,
        'page_id'     => $_wg['widget_page_id']
    );
    jrCore_json_response($_res, true, false);
}

//------------------------------------
// default template code to customize. (ajax)
//------------------------------------
function view_jrSiteBuilder_default_tpl($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['m']) || !jrCore_module_is_active($_post['m'])) {
        jrCore_json_response(array('OK' => 0, 'code' => 'Please select a module from the List Module field before trying to load default code'));
    }
    // get the template
    if (is_file(APP_DIR . "/modules/{$_post['m']}/templates/item_list.tpl")) {
        $tpl = file_get_contents(APP_DIR . "/modules/{$_post['m']}/templates/item_list.tpl");
        $_rs = array(
            'OK'   => 1,
            'code' => $tpl
        );
        jrCore_json_response($_rs, true, false);
    }
    jrCore_json_response(array('OK' => 0, 'code' => 'unable to open default item_list.tpl file'));
}

//------------------------------
// page browser
//------------------------------
function view_jrSiteBuilder_browser($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrSiteBuilder');
    jrCore_page_banner('Page Browser');
    jrCore_get_form_notice();
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browser");

    // Start our output
    $dat             = array();
    $dat[1]['title'] = 'URL';
    $dat[2]['title'] = 'page title';
    $dat[3]['title'] = 'widgets';
    $dat[3]['width'] = '5%';
    $dat[4]['title'] = 'layout';
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = 'delete';
    $dat[5]['width'] = '5%';
    jrCore_page_table_header($dat);

    $page = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $page = (int) $_post['p'];
    }

    $tbl = jrCore_db_table_name('jrSiteBuilder', 'page');
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $sst = jrCore_db_escape($_post['search_string']);
        $req = "SELECT * FROM {$tbl} WHERE (page_uri LIKE '%{$sst}%' OR page_title LIKE '%{$sst}%')";
    }
    else {
        $req = "SELECT * FROM {$tbl}";
    }
    $_rt = jrCore_db_paged_query($req, $page, 12, 'NUMERIC');

    $tbl  = jrCore_db_table_name('jrSiteBuilder', 'widget');
    $req  = "SELECT widget_page_id, count(widget_page_id) AS ct FROM {$tbl} GROUP BY widget_page_id";
    $_wct = jrCore_db_query($req, 'widget_page_id');

    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
        foreach ($_rt['_items'] as $_p) {
            $dat             = array();
            $dat[1]['title'] = '<a href="' . $_conf['jrCore_base_url'] . $_p['page_uri'] . '" target="_blank">' . $_p['page_uri'] . '</a>';
            $dat[2]['title'] = '<a href="' . $_conf['jrCore_base_url'] . $_p['page_uri'] . '" target="_blank">' . $_p['page_title'] . '</a>';
            $dat[3]['title'] = (isset($_wct[$_p['page_id']])) ? $_wct[$_p['page_id']]['ct'] : 0;
            $dat[3]['class'] = 'center';
            $_rep            = array(
                'layout' => jrSiteBuilder_explode_sequence($_p['page_layout'])
            );
            $layout          = jrCore_parse_template('page_layout_icon.tpl', $_rep, 'jrSiteBuilder');
            $dat[4]['title'] = $layout;
            $dat[5]['title'] = jrCore_page_button("d{$_p['page_id']}", 'delete', "if(confirm('Are you sure you want to delete this page?')){ jrSiteBuilder_browse_delete_page('{$_p['page_id']}') }");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat = array();
        if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
            $dat[1]['title'] = '<p>No Site Builder Pages matched your search criteria</p>';
        }
        else {
            $dat[1]['title'] = '<p>No Site Builder Pages have been created</p>';
        }
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    jrCore_page_display();
}

//------------------------------
// template_builder
//------------------------------
function view_jrSiteBuilder_template_builder($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    $_rep = array();
    if (isset($_post['_1']) && !jrCore_module_is_active($_post['_1'])) {
        jrCore_notice_page('error', 'that module is not active');
    }

    // get the exitsting .tpl configurations.

    $tbl        = jrCore_db_table_name('jrSiteBuilder', 'template');
    $req        = "SELECT * FROM {$tbl} WHERE template_module = '{$_post['_1']}' AND template_name NOT LIKE 'preview.tpl'";
    $_templates = jrCore_db_query($req, 'NUMERIC');

    if (is_array($_templates)) {
        $_rep['_templates'] = $_templates;
        if (isset($_post['tpl'])) {
            foreach ($_templates as $_t) {
                if ($_post['tpl'] == $_t['template_name']) {
                    $_rep['tpl'] = $_t['template_body'];
                    continue;
                }
            }
        }
    }
    if (is_file(APP_DIR . "/modules/{$_post['_1']}/templates/item_grid.tpl")) {
        $_rep['_templates'][] = array(
            'template_name' => 'item_grid.tpl'
        );
    }

    if (!isset($_rep['tpl'])) {
        if (!isset($_post['tpl'])) {
            $_post['tpl'] = 'item_list.tpl';
        }
        $tpl = APP_DIR . "/modules/{$_post['_1']}/templates/{$_post['tpl']}";
        if (!is_file($tpl)) {
            jrCore_notice_page('error', 'that module does not have the requested file');
        }
        $_rep['tpl'] = trim(file_get_contents($tpl));
    }

    $_rep['mod'] = $_post['_1'];

    // module
    $_opt = jrCore_get_datastore_modules();
    foreach ($_opt as $mod => $url) {
        if (!jrCore_module_is_active($mod)) {
            unset($_opt[$mod]);
            continue;
        }
        switch ($mod) {
            // Some modules we don't support or they support themselves
            case 'jrSeamless':
            case 'jrSmiley':
                unset($_opt[$mod]);
                break;

            default:
                if (is_file(APP_DIR . "/modules/{$mod}/templates/item_list.tpl")) {
                    $_opt[$mod] = $_mods[$mod]['module_name'];
                }
                else {
                    unset($_opt[$mod]);
                }
        }
    }
    natcasesort($_opt);
    $_rep['_modules'] = $_opt;

    return jrCore_parse_template('template_builder.tpl', $_rep, 'jrSiteBuilder');
}

//------------------------------
// preview_template
//------------------------------
function view_jrSiteBuilder_preview_template($_post, $_user, $_conf)
{
    jrUser_master_only();
    $_rep = array();
    if (isset($_post['_1']) && jrCore_module_is_active($_post['_1'])) {
        $_rep['mod'] = $_post['_1'];
        $_sp         = array(
            'limit' => 5
        );
        $_rt         = jrCore_db_search_items($_post['_1'], $_sp);
        if (isset($_rt['_items'])) {
            $_rep['_items'] = $_rt['_items'];

            $tbl        = jrCore_db_table_name('jrSiteBuilder', 'template');
            $req        = "SELECT * FROM {$tbl} WHERE template_module = '{$_post['_1']}'";
            $_templates = jrCore_db_query($req, 'NUMERIC');

            if (is_array($_templates)) {
                $_rep['_templates'] = $_templates;
                if (isset($_post['tpl'])) {
                    foreach ($_templates as $_t) {
                        if ($_post['tpl'] == $_t['template_name']) {
                            $_rep['tpl'] = $_t['template_body'];
                            continue;
                        }
                    }
                }
            }

            if (is_file(APP_DIR . "/modules/{$_post['_1']}/templates/item_grid.tpl")) {
                $_rep['_templates'][] = array(
                    'template_name' => 'item_grid.tpl'
                );
            }

            if (!isset($_rep['tpl'])) {
                if (!isset($_post['tpl'])) {
                    $_post['tpl'] = 'item_list.tpl';
                }
                $tpl = APP_DIR . "/modules/{$_post['_1']}/templates/{$_post['tpl']}";
                if (!is_file($tpl)) {
                    jrCore_notice_page('error', 'that module does not have the requested file');
                }
                $_rep['tpl'] = trim(file_get_contents($tpl));
            }
        }
    }

    // We need to test this template and make sure it does not cause any Smarty errors
    $cdr = jrCore_get_module_cache_dir('jrCore');
    $nam = time() . ".tpl";
    jrCore_write_to_file("{$cdr}/{$nam}", $_rep['tpl']);
    $url = jrCore_get_module_url('jrCore');
    $out = jrCore_load_url("{$_conf['jrCore_base_url']}/{$url}/test_template/{$nam}");
    if (isset($out) && strlen($out) > 1 && (strpos($out, 'error:') === 0 || stristr($out, 'fatal error'))) {
        unlink("{$cdr}/{$nam}");
        return '<div class="page_notice warning">There is an error in your template, it can\'t be displayed.</div>';
    }
    unlink("{$cdr}/{$nam}");

    return jrCore_parse_template('preview_template.tpl', $_rep, 'jrSiteBuilder');
}

//------------------------------
// save_template
//------------------------------
function view_jrSiteBuilder_save_template($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!jrCore_module_is_active($_post['mod'])) {
        $_res = array(
            'success' => 0,
            'msg'     => 'That module is not currently active.',
        );
        jrCore_json_response($_res);
    }

    // trim .tpl
    if (substr($_post['filename'], -4, 4) == '.tpl') {
        $_post['filename'] = substr_replace($_post['filename'], '', -4, 4);
    }

    $template_name   = jrCore_db_escape((jrCore_url_string(trim($_post['filename'])))) . '.tpl';
    $template_body   = jrCore_db_escape($_post['html']);
    $template_module = $_post['mod'];

    $tbl = jrCore_db_table_name('jrSiteBuilder', 'template');
    $req = "INSERT INTO {$tbl} (template_created, template_updated, template_module, template_name, template_body)
            VALUES (UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),'{$template_module}','{$template_name}','$template_body')
            ON DUPLICATE KEY UPDATE template_updated = UNIX_TIMESTAMP(), template_module = '{$template_module}', template_name = '{$template_name}', template_body = '{$template_body}'";
    if (jrCore_db_query($req)) {
        $_res = array(
            'success' => 1,
            'msg'     => 'template was successfully updated',
        );
    }
    else {
        $_res = array(
            'success' => 0,
            'msg'     => 'there was an error saving to the database, please try again.',
        );
    }

    jrCore_json_response($_res);
}

//------------------------------
// Reset
//------------------------------
function view_jrSiteBuilder_reset($_post, $_user, $_conf)
{
    // Must be logged in as admin
    jrUser_master_only();

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrSiteBuilder');

    // Create form
    jrCore_page_banner('Reset Site Builder');

    jrCore_page_notice('warning', 'Warning: All Site Builder Pages, Widgets,Ttemplates, and Menu content will be deleted!');

    // Form init
    $_tmp = array(
        'submit_value' => 'Reset Site Builder',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
    );
    jrCore_form_create($_tmp);

    jrCore_page_display();
}

//------------------------------
// Reset Save
//------------------------------
function view_jrSiteBuilder_reset_save($_post, &$_user, &$_conf)
{
    // Must be logged in as admin
    jrUser_master_only();

    // Truncate tables
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'menu');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'page');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'template');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'widget');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");

    jrCore_set_form_notice('success', 'Pages, Widgets, Menus and Templates have been reset.');
    jrCore_delete_all_cache_entries();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
}

//------------------------------
// Export current state to .json file.
//------------------------------
function view_jrSiteBuilder_export($_post, $_user, $_conf)
{
    jrUser_master_only();

    // Get all ZIPs in media dir
    $mdir = jrCore_get_media_directory(0);
    $_mds = glob("{$mdir}/sb_*.json");
    $_mds = array_reverse($_mds);

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrSiteBuilder');

    $btn = false;
    if ($_mds && is_array($_mds) && count($_mds) > 0) {
        $btn = jrCore_page_button('del', 'delete all backup files', "if(confirm('Are you sure you want to delete all the existing backup files that have been created?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_all_export') }");
    }
    jrCore_page_banner('Backup Packages', $btn);

    // Start our output
    $dat             = array();
    $dat[1]['title'] = 'file';
    $dat[2]['title'] = 'size';
    $dat[2]['width'] = '10%';
    $dat[3]['title'] = 'created';
    $dat[3]['width'] = '20%';
    $dat[4]['title'] = 'download';
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = 'delete';
    $dat[5]['width'] = '5%';
    jrCore_page_table_header($dat);

    // Show existing module packages that can be downloaded
    if (isset($_mds) && is_array($_mds) && count($_mds) > 0) {
        foreach ($_mds as $k => $file) {
            $nam             = basename($file);
            $dat             = array();
            $dat[1]['title'] = $nam;
            $dat[2]['title'] = jrCore_format_size(filesize($file));
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = jrCore_format_time(filemtime($file), true);
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = jrCore_page_button("d{$k}", 'download', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/download_package/{$nam}')");
            $dat[5]['title'] = jrCore_page_button("r{$k}", 'delete', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_package/{$nam}')");
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>No Backup Packages are available for download</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    jrCore_page_banner('Export Pages');
    jrCore_get_form_notice();

    // Start our output
    $dat             = array();
    $dat[1]['title'] = '<input type="checkbox" class="form_checkbox" onclick="$(\'.nw_checkbox\').prop(\'checked\',$(this).prop(\'checked\'));">';
    $dat[1]['width'] = '2%';
    $dat[2]['title'] = 'URL';
    $dat[3]['title'] = 'page title';
    $dat[4]['title'] = 'widgets';
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = 'layout';
    $dat[5]['width'] = '5%';

    jrCore_page_table_header($dat);

    $tbl = jrCore_db_table_name('jrSiteBuilder', 'page');
    $req = "SELECT * FROM {$tbl}";
    $_rt = jrCore_db_query($req, 'NUMERIC');

    $tbl  = jrCore_db_table_name('jrSiteBuilder', 'widget');
    $req  = "SELECT widget_page_id, count(widget_page_id) AS ct FROM {$tbl} GROUP BY widget_page_id";
    $_wct = jrCore_db_query($req, 'widget_page_id');

    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $_p) {
            $dat             = array();
            $dat[1]['title'] = '<input type="checkbox" class="form_checkbox nw_checkbox" name="page_id[]" value="' . $_p['page_id'] . '">';
            $dat[2]['title'] = '<a href="' . $_conf['jrCore_base_url'] . $_p['page_uri'] . '" target="_blank">' . $_p['page_uri'] . '</a>';
            $dat[3]['title'] = '<a href="' . $_conf['jrCore_base_url'] . $_p['page_uri'] . '" target="_blank">' . $_p['page_title'] . '</a>';
            $dat[4]['title'] = (isset($_p['page_id']) && isset($_wct["{$_p['page_id']}"])) ? $_wct["{$_p['page_id']}"]['ct'] : '';
            $dat[4]['class'] = 'center';
            $_rep            = array(
                'layout' => jrSiteBuilder_explode_sequence($_p['page_layout'])
            );
            $layout          = jrCore_parse_template('page_layout_icon.tpl', $_rep, 'jrSiteBuilder');
            $dat[5]['title'] = $layout;
            jrCore_page_table_row($dat);
        }
    }

    jrCore_page_table_footer();

    // Form init
    $_tmp = array(
        'submit_value' => 'Export Checked',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
    );
    jrCore_form_create($_tmp);

    jrCore_page_display();
}

//------------------------------
// Export Save
//------------------------------
function view_jrSiteBuilder_export_save($_post, $_user, $_conf)
{
    // Must be logged in as a panel admin
    jrUser_master_only();

    if (!isset($_post['page_id']) || !is_array($_post['page_id'])) {
        jrCore_set_form_notice('error', 'No pages selected. Please select more than 1 page to export.');
        jrCore_location('referrer');
    }

    $mdir = jrCore_get_media_directory(0);
    $ver  = strtolower(date('j-M-Y_G:i'));
    if (jrCore_media_file_exists(0, "/sb_{$ver}.json")) {
        jrCore_delete_media_file(0, "{$mdir}/sb_{$ver}.json");
    }

    $_wanted = jrSiteBuilder_export($_post['page_id']);
    $_json   = json_encode($_wanted);
    jrCore_write_to_file("{$mdir}/sb_{$ver}.json", $_json);
    jrCore_set_form_notice('success', 'Export successful');

    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/export");
}

//------------------------------
// Import
//------------------------------
function view_jrSiteBuilder_import($_post, $_user, $_conf)
{
    // Must be logged in as admin
    jrUser_master_only();

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrSiteBuilder');

    jrCore_page_banner('Import Backup Package');

    // Form init
    $_tmp = array(
        'submit_value' => 'Import Backup Package',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
    );
    jrCore_form_create($_tmp);

    // Copy any skin packages into media directory
    $_skn = jrCore_get_skins();
    if (is_array($_skn)) {
        foreach ($_skn as $skin) {
            $_pkg = glob(APP_DIR . "/skins/{$skin}/panels/site_builder_package*.json");
            if (isset($_pkg) && is_array($_pkg) && count($_pkg) > 0) {
                foreach ($_pkg as $k => $file) {
                    // copy
                    $nam = basename($file);
                    jrCore_copy_media_file(0, $file, $nam);
                }
            }
        }
    }

    // Start our output
    $dat             = array();
    $dat[1]['title'] = 'file';
    $dat[2]['title'] = 'size';
    $dat[2]['width'] = '10%';
    $dat[3]['title'] = 'created';
    $dat[3]['width'] = '20%';
    $dat[4]['title'] = 'Import';
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = 'Delete';
    $dat[5]['width'] = '5%';
    jrCore_page_table_header($dat);

    // Get all ZIPs in media dir
    $mdir = jrCore_get_media_directory(0);
    $_pkg = glob("{$mdir}/sb_*.json");

    // Show existing module packages that can be downloaded
    if (isset($_pkg) && is_array($_pkg) && count($_pkg) > 0) {
        foreach ($_pkg as $k => $file) {
            $nam             = basename($file);
            $dat             = array();
            $dat[1]['title'] = $nam;
            $dat[2]['title'] = jrCore_format_size(filesize($file));
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = jrCore_format_time(filemtime($file), true);
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = jrCore_page_button("d{$k}", 'import', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/package_import/{$nam}')");
            $dat[5]['title'] = jrCore_page_button("r{$k}", 'delete', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_package/{$nam}')");
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>No Backup Packages are avialable for Import</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    // Upload Package form
    jrCore_page_banner('Upload Backup Package');

    // File
    $_tmp = array(
        'name'       => 'page_json',
        'label'      => 'Backup Package File',
        'help'       => 'Select the Backup Package .json file to upload. Exported Site Builder packages have a name like: sb_24-jul-2015.json',
        'text'       => 'upload package',
        'type'       => 'file',
        'extensions' => 'json',
        'required'   => false,
        'multiple'   => false
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// Import Save
//------------------------------
function view_jrSiteBuilder_import_save($_post, &$_user, &$_conf)
{
    // Must be logged in as admin
    jrUser_master_only();

    // See if we have uploaded a .json file
    $_files    = jrCore_get_uploaded_media_files('jrSiteBuilder', 'page_json');
    $json_file = $_files[0];

    // save to disk on original name.
    if (is_file($json_file)) {
        $ver = strtolower(date('j-M-Y_G:i'));
        jrCore_copy_media_file(0, $json_file, "sb_up_{$ver}.json");
        jrCore_delete_media_file(0, $json_file);
        jrCore_form_delete_session();
        jrProfile_reset_cache();
    }
    else {
        jrCore_set_form_notice('error', 'Uploaded file not found.');
    }

    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/import");
}

//------------------------------
// download_package
//------------------------------
function view_jrSiteBuilder_download_package($_post, $_user, $_conf)
{
    jrUser_master_only();
    $mdir = jrCore_get_media_directory(0);
    if (!isset($_post['_1']) || !jrCore_media_file_exists(0, $_post['_1'])) {
        jrCore_set_form_notice('error', 'Invalid json file');
        jrCore_location('referrer');
    }
    jrCore_send_download_file("{$mdir}/{$_post['_1']}");
    session_write_close();
    exit();
}

//------------------------------
// delete_package
//------------------------------
function view_jrSiteBuilder_delete_package($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['_1']) || !jrCore_media_file_exists(0, $_post['_1'])) {
        jrCore_set_form_notice('error', 'Invalid ZIP file');
        jrCore_location('referrer');
    }
    jrCore_delete_media_file(0, $_post['_1']);
    jrCore_location('referrer');
}

/*
 * delete all the .json files exported so far
 */
function view_jrSiteBuilder_delete_all_export($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    $mdir = jrCore_get_media_directory(0);
    $_fl  = glob("{$mdir}/sb_*.json");
    if ($_fl && is_array($_fl)) {
        foreach ($_fl as $file) {
            jrCore_delete_media_file(0, $file);
        }
    }
    jrCore_location('referrer');
}

//------------------------------
// package_import
//------------------------------
function view_jrSiteBuilder_package_import($_post, $_user, $_conf)
{
    if (!isset($_post['_1']) || !jrCore_media_file_exists(0, $_post['_1'])) {
        jrCore_set_form_notice('error', 'Invalid package file');
        jrCore_location('referrer');
    }

    $_json = jrCore_read_media_file(0, $_post['_1']);

    if (!jrCore_checktype($_json, 'json')) {
        jrCore_set_form_notice('error', 'json code not valid.');
        jrCore_location('referrer');
    }

    $_data = json_decode($_json, 'true');

    if (!is_array($_data['pages']) || empty($_data['pages'])) {
        jrCore_set_form_notice('error', 'no data found in the file.');
        jrCore_location('referrer');
    }

    // show whats in the file here
    $dat             = array();
    $dat[1]['title'] = '<input type="checkbox" class="form_checkbox" onclick="$(\'.nw_checkbox\').prop(\'checked\',$(this).prop(\'checked\'));">';
    $dat[1]['width'] = '5%';
    $dat[2]['title'] = 'URL';
    $dat[3]['title'] = 'page title';
    $dat[4]['title'] = 'widgets';
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = 'layout';
    $dat[5]['width'] = '5%';

    jrCore_page_table_header($dat);

    foreach ($_data['pages'] as $_p) {
        $dat             = array();
        $dat[1]['title'] = '<input type="checkbox" class="form_checkbox nw_checkbox" name="page_id[]" value="' . $_p['_page']['page_id'] . '">';
        $dat[2]['title'] = '<a href="' . $_conf['jrCore_base_url'] . $_p['_page']['page_uri'] . '" target="_blank">' . $_p['_page']['page_uri'] . '</a>';
        $dat[3]['title'] = '<a href="' . $_conf['jrCore_base_url'] . $_p['_page']['page_uri'] . '" target="_blank">' . $_p['_page']['page_title'] . '</a>';
        $dat[4]['title'] = count($_p['_widget']);
        $dat[4]['class'] = 'center';
        $_rep            = array(
            'layout' => jrSiteBuilder_explode_sequence($_p['_page']['page_layout'])
        );
        $layout          = jrCore_parse_template('page_layout_icon.tpl', $_rep, 'jrSiteBuilder');
        $dat[5]['title'] = $layout;
        jrCore_page_table_row($dat);
    }

    jrCore_page_table_footer();

    // Form init
    $url  = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browser";
    $_tmp = array(
        'submit_value'  => 'Import Checked',
        'cancel'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/import",
        'submit_modal'  => 'update',
        'modal_width'   => 600,
        'modal_height'  => 400,
        'modal_note'    => 'Importing',
        'modal_close'   => 'Close',
        'modal_onclick' => "jrCore_window_location('{$url}')"
    );
    jrCore_form_create($_tmp);

    // File
    $_tmp = array(
        'name'  => 'json_file',
        'type'  => 'hidden',
        'value' => $_post['_1']
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_display();
}

//------------------------------
// package_import_save
//------------------------------
function view_jrSiteBuilder_package_import_save($_post, $_user, $_conf)
{
    $_json = jrCore_read_media_file(0, $_post['json_file']);
    if (!jrCore_checktype($_json, 'json')) {
        jrCore_set_form_notice('error', 'Could not locate the .json file.');
        jrCore_location('referrer');
    }

    if (jrCore_checktype($_json, 'json')) {
        $_data = json_decode($_json, true);
    }
    if (!isset($_data['pages']) || !is_array($_data['pages'])) {
        jrCore_form_delete_session();
        jrCore_form_modal_notice('error', "error: The file did not contain an array of pages, menus and widgets.");
        jrCore_form_modal_notice('complete', 'No data found in the file');
        exit;
    }

    jrCore_logger('INF', "importing Page, Widget, Menu layout from option file: " . basename($_post['json_file']) . " beginning");

    $tblm = jrCore_db_table_name('jrSiteBuilder', 'menu');
    $tblp = jrCore_db_table_name('jrSiteBuilder', 'page');
    $tblw = jrCore_db_table_name('jrSiteBuilder', 'widget');

    // existing pages
    $req        = "SELECT * FROM {$tblp}";
    $_existingp = jrCore_db_query($req, 'page_uri');
    // existing menu
    $req        = "SELECT * FROM {$tblm}";
    $_existingm = jrCore_db_query($req, 'menu_url');
    // existing widget
    $req        = "SELECT * FROM {$tblw}";
    $_existingw = jrCore_db_query($req, 'widget_unique');

    $_orphan = array();

    foreach ($_data['pages'] as $page) {
        // page
        if (in_array($page['_page']['page_uri'], array_keys($_existingp))) {
            // update
            jrCore_form_modal_notice('update', "updating page: " . $page['_page']['page_uri']);
            $req = "UPDATE {$tblp} SET
                      page_updated  = '" . jrCore_db_escape($page['_page']['page_updated']) . "',
                      page_enabled  = '" . jrCore_db_escape($page['_page']['page_enabled']) . "',
                      page_uri      = '" . jrCore_db_escape($page['_page']['page_uri']) . "',
                      page_title    = '" . jrCore_db_escape($page['_page']['page_title']) . "',
                      page_groups   = '" . jrCore_db_escape($page['_page']['page_groups']) . "',
                      page_active   = '" . jrCore_db_escape($page['_page']['page_active']) . "',
                      page_layout   = '" . jrCore_db_escape($page['_page']['page_layout']) . "',
                      page_settings = '" . jrCore_db_escape($page['_page']['page_settings']) . "',
                      page_head     = '" . jrCore_db_escape($page['_page']['page_head']) . "'
                     WHERE page_uri = '{$page['_page']['page_uri']}'";
            jrCore_db_query($req);
        }
        else {
            // insert
            jrCore_form_modal_notice('update', "importing page: " . $page['_page']['page_uri']);
            $req = "INSERT INTO {$tblp} (page_updated, page_enabled, page_uri, page_title, page_groups, page_active, page_layout, page_settings, page_head)
                    VALUES ('" . jrCore_db_escape($page['_page']['page_updated']) . "', '" . jrCore_db_escape($page['_page']['page_enabled']) . "', '" . jrCore_db_escape($page['_page']['page_uri']) . "', '" . jrCore_db_escape($page['_page']['page_title']) . "', '" . jrCore_db_escape($page['_page']['page_groups']) . "', '" . jrCore_db_escape($page['_page']['page_active']) . "', '" . jrCore_db_escape($page['_page']['page_layout']) . "', '" . jrCore_db_escape($page['_page']['page_settings']) . "', '" . jrCore_db_escape($page['_page']['page_head']) . "')";
            jrCore_db_query($req);
        }

        // menu
        if (isset($page['_menu']) && is_array($page['_menu'])) {
            foreach ($page['_menu'] as $_m) {
                if (in_array($_m['menu_url'], array_keys($_existingm))) {
                    // update
                    jrCore_form_modal_notice('update', "updating menu: " . $_m['menu_url']);
                    $req = "UPDATE {$tblm} SET
                              menu_updated = '" . jrCore_db_escape($_m['menu_updated']) . "',
                              menu_order   = '" . jrCore_db_escape($_m['menu_order']) . "',
                              menu_title   = '" . jrCore_db_escape($_m['menu_title']) . "',
                              menu_url 	   = '" . jrCore_db_escape($_m['menu_url']) . "',
                              menu_group   = '" . jrCore_db_escape($_m['menu_group']) . "',
                              menu_onclick = '" . jrCore_db_escape($_m['menu_onclick']) . "'
                            WHERE menu_url = '{$_m['menu_url']}'";
                    jrCore_db_query($req);
                }
                else {
                    // insert
                    jrCore_form_modal_notice('update', "importing menu: " . $_m['menu_url']);
                    $req = "INSERT INTO {$tblm} (menu_updated, menu_parent_id, menu_order, menu_title, menu_url, menu_group, menu_onclick)
                            VALUES ('" . jrCore_db_escape($_m['menu_updated']) . "', '0', '" . jrCore_db_escape($_m['menu_order']) . "', '" . jrCore_db_escape($_m['menu_title']) . "', '" . jrCore_db_escape($_m['menu_url']) . "', '" . jrCore_db_escape($_m['menu_group']) . "', '" . jrCore_db_escape($_m['menu_onclick']) . "')";
                    $iid = jrCore_db_query($req, 'INSERT_ID');

                    if ($iid && isset($_m['menu_parent_url']) && $_m['menu_parent_url'] != "0") {
                        $_orphan[$_m['menu_parent_url']][$_m['menu_id']] = $iid;
                    }
                }
            }
        }

    }

    // orphans (sub menu items that need thier parent menu_id set )
    if (!empty($_orphan)) {
        $req     = "SELECT menu_id, menu_url FROM {$tblm}";
        $_parent = jrCore_db_query($req, 'menu_url');
        foreach ($_orphan as $parent_menu_url => $_m0) {
            if (is_array($_m0)) {
                foreach ($_m0 as $menu_id) {
                    // update the item with the parent menu_id
                    $req = "UPDATE {$tblm} SET menu_parent_id  =  '{$_parent[$parent_menu_url]['menu_id']}' WHERE menu_id = '{$menu_id}'";
                    jrCore_db_query($req);
                }
            }
        }
    }

    // get the new page url setup
    $req    = "SELECT * FROM {$tblp}";
    $_pages = jrCore_db_query($req, 'page_uri');

    if (is_array($_pages)) {
        foreach ($_data['pages'] as $page) {
            // widgets
            if (isset($page['_widget']) && is_array($page['_widget'])) {
                foreach ($page['_widget'] as $_w) {
                    if (isset($_w['widget_unique']) && strlen($_w['widget_unique']) > 10 && in_array($_w['widget_unique'], array_keys($_existingw))) {
                        // update
                        jrCore_form_modal_notice('update', "updating widget: " . $_w['widget_title']);
                        $req = "UPDATE {$tblw} SET
                                  widget_updated  = '" . jrCore_db_escape($_w['widget_updated']) . "',
                                  widget_page_id  = '{$_pages[$page['_page']['page_uri']]['page_id']}',
                                  widget_location = '" . jrCore_db_escape($_w['widget_location']) . "',
                                  widget_weight   = '" . jrCore_db_escape($_w['widget_weight']) . "',
                                  widget_groups   = '" . jrCore_db_escape($_w['widget_groups']) . "',
                                  widget_title    = '" . jrCore_db_escape($_w['widget_title']) . "',
                                  widget_module   = '" . jrCore_db_escape($_w['widget_module']) . "',
                                  widget_name     = '" . jrCore_db_escape($_w['widget_name']) . "',
                                  widget_data     = '" . jrCore_db_escape($_w['widget_data']) . "'
                                WHERE widget_unique = '{$_w['widget_unique']}'";
                        jrCore_db_query($req);
                    }
                    else {
                        // insert
                        jrCore_form_modal_notice('update', "importing widget: " . $_w['widget_title']);
                        $req = "INSERT INTO {$tblw} (widget_updated, widget_page_id, widget_location, widget_weight, widget_groups, widget_title, widget_module, widget_name, widget_data, widget_unique)
                                VALUES ('" . jrCore_db_escape($_w['widget_updated']) . "', '{$_pages[$page['_page']['page_uri']]['page_id']}', '" . jrCore_db_escape($_w['widget_location']) . "', '" . jrCore_db_escape($_w['widget_weight']) . "', '" . jrCore_db_escape($_w['widget_groups']) . "', '" . jrCore_db_escape($_w['widget_title']) . "', '" . jrCore_db_escape($_w['widget_module']) . "', '" . jrCore_db_escape($_w['widget_name']) . "', '" . jrCore_db_escape($_w['widget_data']) . "', '" . jrCore_db_escape($_w['widget_unique']) . "')";
                        jrCore_db_query($req);
                    }
                }
            }

        }
    }

    jrCore_form_delete_session();
    jrCore_delete_all_cache_entries();
    jrCore_logger('INF', "Site Builder package imported successfully: " . basename($_post['json_file']));
    jrCore_form_modal_notice('complete', "Pages, Widgets and Menus import complete.");
    exit;
}

//---------------------------------------------------------------
// json_package_page (to package page.json files for skins)
//---------------------------------------------------------------
function view_jrSiteBuilder_json_package_page($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!jrCore_is_developer_mode()) {
        jrCore_notice_page('error', 'This tool can only be run when the site is in developer mode');
    }

    if (!jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid page_id - page_id is not numeric');
    }

    $_wanted = jrSiteBuilder_export(array($_post['_1']));
    if ($_wanted && is_array($_wanted)) {

        $_page = array_shift($_wanted['pages']);
        if ($_page['_page']['page_uri'] == "/") {
            $filename = 'index';
        }
        else {
            $filename = trim($_page['_page']['page_uri'], '/');
        }

        if (!$filename || strlen($filename) == 0) {
            jrCore_notice_page('error', 'The page URL could not be identified');
        }

        // set page id to zero (they don't exist in the database)
        $_page['_page']['page_id'] = 0;
        // set widget ids to zero (they don't exist in the database)
        if (is_array($_page['_widget'])) {
            foreach ($_page['_widget'] as $k => $_w) {
                $_page['_widget'][$k]['widget_id']      = 0;
                $_page['_widget'][$k]['widget_page_id'] = 0;
            }
        }

        $sbdir = APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/sitebuilder";
        if (!is_dir($sbdir)) {
            mkdir($sbdir);
            chmod($sbdir, $_conf['jrCore_dir_perms']);
        }
        if (!is_dir($sbdir) || !is_writable($sbdir)) {
            jrCore_notice_page('error', "Unable to create or write to the sitebuilder directory: skins/{$_conf['jrCore_active_skin']}/sitebuilder");
        }

        $jfile = "{$sbdir}/{$filename}.json";
        $_json = json_encode($_page);
        if (jrCore_write_to_file($jfile, $_json)) {
            jrCore_json_response(array('msg' => "Page Config successfully saved as: skins/{$_conf['jrCore_active_skin']}/sitebuilder/{$filename}.json"));
        }

        if (strlen($filename) > 1) {
            header("Content-type: application/json");
            header('Content-Disposition: attachment; filename="' . $filename . '.json"');
            header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 8640000));
            echo $_json;
        }
    }
    jrCore_json_response(array('error' => 'error exporting page - please try again'));
}

//---------------------------------------------------------------
// reset the menu to the skins default menu
//---------------------------------------------------------------
function view_jrSiteBuilder_reset_menu($_post, $_user, $_conf)
{
    jrUser_master_only();

    // Empty everything
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'menu');
    if (jrCore_db_query("TRUNCATE TABLE {$tbl}")) {
        $_resp = array(
            'msg' => "successfully cleared the menu"
        );
        jrCore_json_response($_resp);

    }

    jrCore_json_response(array('error' => 'error exporting page - please try again'));
}

//------------------------------------
// jrSiteBuilder_menu_code
//------------------------------------
function view_jrSiteBuilder_menu_code($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_set_no_header_or_footer();

    $tbl = jrCore_db_table_name('jrSiteBuilder', 'menu');
    // get the existing menus, skip if included.
    $req   = "SELECT * FROM {$tbl} order by menu_order asc";
    $_menu = jrCore_db_query($req, 'menu_id');

    $_rp = array('_menu' => array());
    foreach ($_menu as $menu_id => $row) {
        $_rp['_menu'][$menu_id]               = $row;
        $_rp['_menu'][$menu_id]['parent_url'] = $_menu[$row['menu_parent_id']]['menu_url'];
    }

    $out = jrCore_parse_template('menu_code.tpl', $_rp, 'jrSiteBuilder');
    $out = jrCore_format_string_bbcode($out);

    // Handle BBCode [code] blocks
    if ($_ctemp = jrCore_get_flag('jrcore_bbcode_replace_blocks')) {
        $_ctemp = array_reverse($_ctemp);
        $out    = str_replace(array_keys($_ctemp), $_ctemp, $out);
        jrCore_delete_flag('jrcore_bbcode_replace_blocks');
    }

    jrCore_page_custom($out);

    return jrCore_page_display(true);
}
