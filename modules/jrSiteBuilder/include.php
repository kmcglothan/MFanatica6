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

// make sure we are not being called directly
defined('APP_DIR') or exit();

 * meta
 */
function jrSiteBuilder_meta()
{
    $_tmp = array(
        'name'        => 'Site Builder',
        'url'         => 'sbcore',
        'version'     => '2.0.2',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Site Builder Core provides support for all Site Builder functions',
        'category'    => 'site',
        'requires'    => 'jrCore:6.0.0',
        'priority'    => 5,
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrSiteBuilder_init()
{
    // Events
    jrCore_register_module_feature('jrCore', 'javascript', 'jrSiteBuilder', 'jrSiteBuilder.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrSiteBuilder', 'jquery-ui-1.10.4.custom.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrSiteBuilder', 'jquery.mjs.nestedSortable.js');
    jrCore_register_module_feature('jrCore', 'css', 'jrSiteBuilder', 'jrSiteBuilder.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSiteBuilder', 'jquery.nouislider.css');

    // Site Builder Core provided widgets
    jrCore_register_module_feature('jrSiteBuilder', 'widget', 'jrSiteBuilder', 'widget_html', 'HTML Editor');
    jrCore_register_module_feature('jrSiteBuilder', 'widget', 'jrSiteBuilder', 'widget_code', 'Template Code');

    // Insert our "Edit this page" option for masters
    jrCore_register_event_listener('jrCore', 'module_view', 'jrSiteBuilder_module_view_listener');
    jrCore_register_event_listener('jrCore', 'index_template', 'jrSiteBuilder_view_template_listener');
    jrCore_register_event_listener('jrCore', 'skin_template', 'jrSiteBuilder_view_template_listener');
    jrCore_register_event_listener('jrCore', 'view_results', 'jrSiteBuilder_view_results_listener');
    jrCore_register_event_listener('jrCore', 'template_variables', 'jrSiteBuilder_template_variables_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrSiteBuilder_verify_module_listener');
    jrCore_register_event_listener('jrCore', 'verify_skin', 'jrSiteBuilder_verify_skin_listener');

    // Custom page display
    jrCore_register_event_listener('jrCore', 'profile_template', 'jrSiteBuilder_profile_template_listener');

    // For a 404 not found page, if it is a master and looks good let them create it
    jrCore_register_event_listener('jrCore', '404_not_found', 'jrSiteBuilder_404_not_found_listener');

    // widget specifies a template, but its not found
    jrCore_register_event_listener('jrCore', 'tpl_404', 'jrSiteBuilder_tpl_404_listener');

    // tools
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrSiteBuilder', 'browser', array('Page Browser', 'View and Delete existing Site Builder pages.'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrSiteBuilder', 'reset', array('Reset Site Builder', 'Delete all Menus, Panels, and Widgets from the system.'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrSiteBuilder', 'export', array('Export Backup', 'Export Pages, Widgets and Menus and to a Backup Package.'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrSiteBuilder', 'import', array('Import Backup', 'Import Pages, Widgets and Menus from an existing Backup Package.'));

    // Once a day we export the layout as a backup
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrSiteBuilder_daily_maintenance_listener');

    // clear the default menu array on clear caches
    jrCore_register_event_listener('jrCore', 'template_cache_reset', 'jrSiteBuilder_template_cache_reset_listener');

    return true;
}

//------------------------------------
// WIDGETS
//------------------------------------

/**
 * Display CONFIG screen for HTML Editor Widget
 * @param $_post array Post info
 * @param $_user array User array
 * @param $_conf array Global Config
 * @param $_wg array Widget info
 * @return bool
 */
function jrSiteBuilder_widget_html_config($_post, $_user, $_conf, $_wg)
{
    $html = jrCore_parse_template('widget_html_config.tpl', $_wg, 'jrSiteBuilder');
    jrCore_page_custom($html);
    return true;
}

/**
 * Get Widget results from posted Config data
 * @param $_post array Post info
 * @return array
 */
function jrSiteBuilder_widget_html_config_save($_post)
{
    // Make sure some smarty constructs are properly setup
    $_rp                                   = array(
        '&amp;&amp;' => '&&',
        '&gt;'       => '>',
        '&lt;'       => '<'
    );
    $_post['html_content_editor_contents'] = str_replace(array_keys($_rp), $_rp, $_post['html_content_editor_contents']);

    // We need to test this HTML and make sure it does not cause any Smarty errors
    if (jrSiteBuilder_template_code_contains_errors($_post['html_content_editor_contents'])) {
        jrCore_set_form_notice('error', 'There is a Smarty syntax error in your HTML - please fix and try again');
        jrCore_form_result();
    }
    return array('html_content' => trim($_post['html_content_editor_contents']));
}

/**
 * HTML Editor Widget DISPLAY
 * @param $_widget array Page Widget info
 * @return string
 */
function jrSiteBuilder_widget_html_display($_widget)
{
    $_widget['html_content'] = '<div class="item">' . smarty_modifier_jrCore_format_string($_widget['html_content'], 'allow_all_formatters', null, 'nl2br,hash_tags') . '</div>';
    return jrCore_parse_template($_widget['html_content'], $_widget, 'jrSiteBuilder');
}

/**
 * Display CONFIG screen for Template Code Widget
 * @param $_post array Post info
 * @param $_user array User array
 * @param $_conf array Global Config
 * @param $_wg array Widget info
 * @return bool
 */
function jrSiteBuilder_widget_code_config($_post, $_user, $_conf, $_wg)
{
    $_wg['code_content'] = htmlspecialchars($_wg['code_content'], ENT_NOQUOTES);
    $html                = jrCore_parse_template('widget_code_config.tpl', $_wg, 'jrSiteBuilder');
    jrCore_page_custom($html);
    return true;
}

/**
 * Get Widget results from posted Config data
 * @param $_post array Post info
 * @return array
 */
function jrSiteBuilder_widget_code_config_save($_post)
{
    // We need to test this HTML and make sure it does not cause any Smarty errors
    if (jrSiteBuilder_template_code_contains_errors($_post['code_content'])) {
        jrCore_set_form_notice('error', 'There is a Smarty syntax error in your Template Code - please fix and try again');
        jrCore_form_result();
    }
    return array('code_content' => $_post['code_content']);
}

/**
 * Template Code Widget
 * @param $_widget array Page Widget info
 * @return string
 */
function jrSiteBuilder_widget_code_display($_widget)
{
    global $_post;
    return jrCore_parse_template($_widget['code_content'], $_post, 'jrSiteBuilder');
}

//------------------------------------
// EVENT LISTENERS
//------------------------------------

/**
 * Profile Template - we listen for custom pages
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return mixed
 */
function jrSiteBuilder_profile_template_listener($_data, $_user, $_conf, $_args, $event)
{
    // See if a page reload has been requested by the Master
    if (jrUser_is_master() && isset($_COOKIE['sb-page-layout-reload'])) {
        $pid = (int) $_COOKIE['sb-page-layout-reload'];
        unset($_COOKIE['sb-page-layout-reload']);
        jrCore_delete_cookie('sb-page-layout-reload');
        jrCore_set_flag('sb-page-layout-reload', $pid);
        $_js = array("jrSiteBuilder_edit_page('{$pid}');");
        jrCore_create_page_element('javascript_ready_function', $_js);
    }

    $uri = jrSiteBuilder_get_page_uri();
    if ($uri == '/index') {
        jrCore_location($_conf['jrCore_base_url']);
    }
    if ($_pg = jrSiteBuilder_get_page_by_uri($uri)) {

        // Check page Groups
        if (!jrUser_is_master() && isset($_pg['page_groups']) && strlen($_pg['page_groups']) > 0 && $_pg['page_groups'] != 'all') {
            if (!jrCore_user_is_part_of_group($_pg['page_groups'])) {
                jrCore_page_not_found();
            }
        }

        if ($_pg && isset($_pg['page_active']) && $_pg['page_active'] == '1') {

            // We have a Site Builder page - show instead
            jrCore_set_flag('sb-active-page', $_pg);
            return jrSiteBuilder_get_page($_pg);

        }
    }
    elseif ($_pg = jrSiteBuilder_get_page_by_json_file($_conf['jrCore_active_skin'], $uri)) {
        // Check page Groups
        if (!jrUser_is_master() && isset($_pg['page_groups']) && strlen($_pg['page_groups']) > 0 && $_pg['page_groups'] != 'all') {
            if (!jrCore_user_is_part_of_group($_pg['page_groups'])) {
                jrCore_page_not_found();
            }
        }

        if ($_pg && isset($_pg['page_active']) && $_pg['page_active'] == '1') {

            // We have a Site Builder page - show instead
            jrCore_set_flag('sb-active-page', $_pg);
            return jrSiteBuilder_get_page($_pg);

        }
    }
    else {
        // Normal profile view - no site builder
        jrCore_set_flag('jrsitebuilder_hide_sitebuilder', 1);
    }
    return $_data;
}

/**
 * Create a new page on a 404
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrSiteBuilder_404_not_found_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (jrUser_is_master() && isset($_post['module_url']) && strlen($_post['module_url']) > 0 && (!isset($_post['module']) || strlen($_post['module']) === 0)) {

        // Let the Master CREATE this new page via Site Builder
        $_post['create_notice'] = '';

        $temp = jrCore_parse_template('page_create.tpl', $_post, 'jrSiteBuilder');
        header("Content-Type: text/html; charset=utf-8");
        header('Content-Length: ' . strlen($temp));
        echo $temp;
        exit;
    }
    return $_data;
}

/**
 * Don't add site builder to module views
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrSiteBuilder_module_view_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_mods, $_post;
    if (jrUser_is_master() && isset($_post['module']) && isset($_mods["{$_post['module']}"]) && isset($_post['option']) && strlen($_post['option']) > 0) {
        // This is a module VIEW - i.e. not a module index, we don't add SB to these pages
        jrCore_set_flag('jrsitebuilder_hide_sitebuilder', 1);
    }
    return $_data;
}

/**
 * See if we are enabled for a page
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return mixed
 */
function jrSiteBuilder_view_template_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    // See if we ARE over-riding this template and if we are, return a placeholder for $out so the template isn't parsed
    $page_uri = false;
    switch ($event) {
        case 'index_template':
            $page_uri = '/';
            break;
        case 'skin_template':
            $page_uri = $_data['_uri'];
            break;
    }
    if ($page_uri) {
        $_rt = jrSiteBuilder_get_page_by_uri($page_uri);
        if ($_rt && jrCore_checktype($_rt['page_id'], 'number_nz')) {
            jrCore_set_flag("jrSiteBuilder_view_template_{$page_uri}", $_rt);
            return 'Site Builder is going to show page_id ' . $_rt['page_id'] . ' here, so dont bother parsing the skin template.';
        }
        elseif ($_pg = jrSiteBuilder_get_page_by_json_file($_conf['jrCore_active_skin'], $page_uri)) {
            jrCore_set_flag("jrSiteBuilder_view_template_{$page_uri}", $_pg);
            return 'Site Builder is going to the json default file for this page ' . $page_uri . '.json here, so dont bother parsing the skin template.';
        }
    }

    // See if we are viewing a skin template
    if (jrUser_is_master()) {
        if ((!isset($_post['option']) || strlen($_post['option']) === 0) && isset($_post['module_url']) && strlen($_post['module_url']) > 0) {
            // OK - we either have a MODULE INDEX or a SKIN TEMPLATE - let the site
            // admin OVERRIDE these if they want to, but let them know about it
            if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$_post['module_url']}.tpl")) {
                // We have a skin template
                jrCore_set_flag('jrsitebuilder_show_override_notice', "{$_post['module_url']}.tpl");
            }
        }
    }
    return $_data;
}

/**
 * See if we are enabled for module and skin index templates
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrSiteBuilder_template_variables_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['jr_template']) && $_data['jr_template'] == 'index.tpl') {
        // See if a page reload has been requested by the Master
        if (jrUser_is_master() && isset($_COOKIE['sb-page-layout-reload'])) {
            unset($_COOKIE['sb-page-layout-reload']);
            jrCore_delete_cookie('sb-page-layout-reload');
        }
    }
    return $_data;
}

/**
 * Insert "Edit This Page" option for a master admin
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return mixed
 */
function jrSiteBuilder_view_results_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post, $_mods;

    if (!jrCore_is_maintenance_mode($_conf, $_post) && !jrCore_get_flag('jrsitebuilder_hide_sitebuilder')) {

        $uri = jrSiteBuilder_get_page_uri();
        if ($uri == '/index') {
            jrCore_location($_conf['jrCore_base_url']);
        }
        if (strlen($uri) > 0) {

            // See if a page reload has been requested by the Master
            if (jrUser_is_master() && isset($_COOKIE['sb-page-layout-reload'])) {
                $pid = (int) $_COOKIE['sb-page-layout-reload'];
                unset($_COOKIE['sb-page-layout-reload']);
                jrCore_delete_cookie('sb-page-layout-reload');
                jrCore_set_flag('sb-page-layout-reload', $pid);
                $_js = array("jrSiteBuilder_edit_page('{$pid}');");
                jrCore_create_page_element('javascript_ready_function', $_js);
            }

            if ($_pg = jrCore_get_flag("jrSiteBuilder_view_template_{$uri}")) {
                if ($_pg && isset($_pg['page_active']) && $_pg['page_active'] == '1') {
                    // We have a Site Builder page - show it

                    // Check page Groups
                    if (!jrUser_is_master() && isset($_pg['page_groups']) && strlen($_pg['page_groups']) > 0 && $_pg['page_groups'] != 'all') {
                        if (!jrCore_user_is_part_of_group($_pg['page_groups'])) {
                            return $_data;
                        }
                    }

                    if (!jrCore_get_flag('sb-active-page')) {
                        $_data = jrSiteBuilder_get_page($_pg);
                    }
                }
            }
            else {
                // See if this is a site builder page
                if ($_pg = jrSiteBuilder_get_page_by_uri($uri)) {
                    if ($_pg && isset($_pg['page_active']) && $_pg['page_active'] == '1') {
                        // We have a Site Builder page - show it

                        // Check page Groups
                        if (!jrUser_is_master() && isset($_pg['page_groups']) && strlen($_pg['page_groups']) > 0 && $_pg['page_groups'] != 'all') {
                            if (!jrCore_user_is_part_of_group($_pg['page_groups'])) {
                                return jrCore_parse_template('404.tpl', array()); // if we return $_data here, the module default will show, this url has been claimed by sitebuilder, then deactivated for some users. respect that.
                            }
                        }

                        if (!jrCore_get_flag('sb-active-page')) {
                            $_data = jrSiteBuilder_get_page($_pg);
                        }
                    }
                }
                elseif ($_pg = jrSiteBuilder_get_page_by_json_file($_conf['jrCore_active_skin'], $uri)) {
                    // Check page Groups
                    if (!jrUser_is_master() && isset($_pg['page_groups']) && strlen($_pg['page_groups']) > 0 && $_pg['page_groups'] != 'all') {
                        if (!jrCore_user_is_part_of_group($_pg['page_groups'])) {
                            return jrCore_parse_template('404.tpl', array()); // if we return $_data here, the module default will show, this url has been claimed by sitebuilder, then deactivated for some users. respect that.
                        }
                    }

                    if (!jrCore_get_flag('sb-active-page')) {
                        $_data = jrSiteBuilder_get_page($_pg);
                    }
                }
            }
            if (!$_pg) {
                $_pg = array();
            }
        }
        else {
            if (!$_pg = jrCore_get_flag('sb-active-page')) {
                $_pg = array();
            }
        }

        // Viewing a module index
        if ($tpl = jrCore_get_flag('jrsitebuilder_show_override_notice')) {
            $_mt           = jrCore_skin_meta_data($_conf['jrCore_active_skin']);
            $_pg['notice'] = "This URL is currently mapped to the {$_mt['title']} skin &quot;{$tpl}&quot; template.\\n\\nClick OK to use a fresh Site Builder page instead. \\n\\nIf you want to restore the page to how it is now, just delete the Site Builder page you created \\nand the current page will return.\\n\\nClick OK to continue, or cancel to keep it how it is now. ";
        }
        elseif ($tpl = jrCore_get_flag('jrsitebuilder_show_override_notice_for_custom_page')) {
            $_mt           = jrCore_skin_meta_data($_conf['jrCore_active_skin']);
            $_pg['notice'] = "A Site Builder default layout is being proivded on this URL by the {$_mt['title']} skin.\\n\\nClick OK to import to Site Builder to begin alterations. \\n\\nIf you want to restore the page to how it is now, just delete the Site Builder page you created \\nand the current page will return.\\n\\nClick OK to continue, or cancel to keep it how it is now. ";
        }
        elseif (count($_pg) === 0) {
            if ($uri == '/') {
                // Viewing the SITE INDEX
                $_mt           = jrCore_skin_meta_data($_conf['jrCore_active_skin']);
                $_pg['notice'] = "This URL is currently mapped to the {$_mt['title']} skin &quot;index.tpl&quot; template.\\n\\nClick OK to use a fresh Site Builder page instead. \\n\\nIf you want to restore the page to how it is now, just delete the Site Builder page you created \\nand the current page will return.\\n\\nClick OK to continue, or cancel to keep it how it is now. ";
            }
            elseif (isset($_post['module']) && isset($_mods["{$_post['module']}"]) && !strpos($uri, '/')) {
                $module_name   = $_mods["{$_post['module']}"]['module_name'];
                $_pg['notice'] = "This URL is currently mapped to the {$module_name} module &quot;index.tpl&quot; template.\\n\\nClick OK to use a fresh Site Builder page instead. \\n\\nIf you want to restore the page to how it is now, just delete the Site Builder page you created \\nand the current page will return.\\n\\nClick OK to continue, or cancel to keep it how it is now. ";
            }
        }

        if (jrUser_is_master()) {
            $temp  = jrCore_parse_template('page_editor_include.tpl', $_pg, 'jrSiteBuilder');
            $_data = str_replace('</body>', "{$temp}\n</body>", $_data);
        }
    }
    return $_data;
}

/**
 * clear the default skin menu when skins change.
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrSiteBuilder_verify_skin_listener($_data, $_user, $_conf, $_args, $event)
{

    // on 'verify_skin' clear the default menu temp values
    jrCore_delete_temp_value('jrSiteBuilder', 'default_menu');
    return $_data;
}

/**
 * Fired when the integrity check runs.  Used to turn off jrPanels, jrWidget, jrMenu so jrSiteBuilder is main.
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrSiteBuilder_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_module_is_active('jrPanel') && jrCore_module_is_active('jrMenu') && jrCore_module_is_active('jrWidget')) {
        // menu
        $_sp = array(
            'order_by'      => array('widget_order' => 'numerical_asc'),
            'skip_triggers' => true,
            'limit'         => 5000
        );
        $_rt = jrCore_db_search_items('jrMenu', $_sp);
        if ($_rt && is_array($_rt['_items'])) {
            $tbl = jrCore_db_table_name('jrSiteBuilder', 'menu');
            // get the existing menus, skip if included.
            $req        = "SELECT menu_url FROM {$tbl}";
            $_menu_urls = jrCore_db_query($req, 'menu_url');

            foreach ($_rt['_items'] as $item) {
                if (in_array($item['menu_url'], array_keys($_menu_urls))) {
                    continue;
                }
                // add it to sitebuilder menus
                $req = "INSERT INTO {$tbl} (menu_id, menu_updated, menu_parent_id, menu_order, menu_title, menu_url, menu_group)
                        VALUES ('{$item['_item_id']}', UNIX_TIMESTAMP(), '{$item['menu_parent']}', '{$item['menu_weight']}', '" . jrCore_db_escape($item['menu_title']) . "', '" . jrCore_db_escape($item['menu_url']) . "', 'all')";
                jrCore_db_query($req);
            }
        }

        // panel
        $_existing_page_uri = array();
        $tbl                = jrCore_db_table_name('jrSiteBuilder', 'page');
        $req                = "SELECT * FROM {$tbl}";
        $_rt                = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt)) {
            foreach ($_rt as $row) {
                $menu_url                      = trim($row['page_uri'], '/');
                $_existing_page_uri[$menu_url] = $row['page_id'];
            }
        }
        if (function_exists('jrPanel_explode_sequence')) {
            $_sp = array(
                'skip_triggers' => true,
                'limit'         => 5000
            );
            $_rt = jrCore_db_search_items('jrPanel', $_sp);
            if ($_rt && is_array($_rt['_items'])) {
                foreach ($_rt['_items'] as $item) {
                    if (in_array($item['panel_name'], array_keys($_existing_page_uri))) {
                        continue;
                    }
                    $_rows = jrPanel_explode_sequence($item['panel_template']);
                    if (is_array($_rows) && !empty($_rows)) {
                        $r = array();
                        foreach ($_rows as $row) {
                            $pos1 = (isset($row[0]['span']) && $row[0]['span'] > 0) ? $row[0]['span'] : 0;
                            $pos2 = (isset($row[1]['span']) && $row[1]['span'] > 0) ? $row[1]['span'] : 0;
                            $pos3 = (isset($row[2]['span']) && $row[2]['span'] > 0) ? $row[2]['span'] : 0;
                            $r[]  = "{$pos1}-{$pos2}-{$pos3}";
                        }
                        $page_layout = implode(',', $r);

                        $ttl = str_replace(array('/', '-', '_'), ' ', $item['panel_name']);

                        // insert
                        $req = "INSERT INTO {$tbl} (page_enabled, page_uri, page_active, page_layout, page_groups, page_title, page_head, page_settings)
                                VALUES ('1', '/" . jrCore_db_escape($item['panel_name']) . "', '1', '{$page_layout}', 'all', '" . jrCore_db_escape($ttl) . "', '', '')
                                ON DUPLICATE KEY UPDATE page_enabled = page_enabled";
                        $page_id                                     = jrCore_db_query($req, 'INSERT_ID');
                        $_existing_page_uri["{$item['panel_name']}"] = $page_id;
                    }
                }
            }
        }

        // widgets
        $tbl = jrCore_db_table_name('jrSiteBuilder', 'widget');

        // get the existing menus, skip if included.
        $req = "SELECT widget_title, widget_page_id FROM {$tbl}";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt)) {
            foreach ($_rt as $row) {
                $_existing_widgets["{$row['widget_page_id']}"][] = $row['widget_title'];
            }
        }

        $mod = 'jrSiteBuilder';
        $_sp = array(
            'skip_triggers' => true,
            'limit'         => 5000
        );
        $_rt = jrCore_db_search_items('jrWidget', $_sp);

        if ($_rt && is_array($_rt['_items'])) {
            foreach ($_rt['_items'] as $item) {
                list($panel_name, $loc) = explode('-location-', $item['widget_location']);
                $loc    = $loc - 1;
                $weight = $item['widget_order'] * 10;
                if (!isset($_existing_page_uri) || strlen($_existing_page_uri[$panel_name]) == 0) {
                    continue;
                }
                $page_id = $_existing_page_uri[$panel_name];
                $ttl     = jrCore_db_escape($item['widget_name']);
                if (isset($_existing_widgets) && in_array($ttl, $_existing_widgets[$page_id])) {
                    continue;
                }

                $nam = '';
                $dat = '';
                switch ($item['widget_function']) {

                    case 'jrWidget_editor':
                        $nam = 'widget_html';
                        $_sv = array('html_content' => "{$item['widget_editor']}");
                        $dat = jrCore_db_escape(json_encode($_sv));
                        break;

                    case 'jrWidget_code':
                        $nam = 'widget_code';
                        $_sv = array('code_content' => "{$item['widget_code']}");
                        $dat = jrCore_db_escape(json_encode($_sv));
                        break;

                    case 'jrWidget_tab_container':
                        // the widget IS a tab container, so set the settings for where it is on the page, then move all widgets that are IN this tab container to the page
                        $_page      = jrSiteBuilder_get_page_by_id($page_id);
                        $_tmp       = (isset($_page['page_settings']) && strlen($_page['page_settings']) > 0) ? json_decode($_page['page_settings'], true) : array();
                        $_tmp[$loc] = array(
                            'ct_layout' => 'tab',
                            'ct_height' => '',
                        );
                        $pgtbl      = jrCore_db_table_name('jrSiteBuilder', 'page');
                        $req        = "UPDATE {$pgtbl} SET page_settings = '" . jrCore_db_escape(json_encode($_tmp)) . "' WHERE page_id = '{$page_id}' LIMIT 1";
                        jrCore_db_query($req);
                        // get all the widgets in this tab_container  'tab_container-location-9997'
                        $_sp = array(
                            'search'        => array(
                                "widget_location = tab_container-location-{$item['_item_id']}"
                            ),
                            'order_by'      => array('widget_order' => 'numerical_asc'),
                            'skip_triggers' => true,
                            'limit'         => 5000
                        );
                        $_wg = jrCore_db_search_items('jrWidget', $_sp);
                        if ($_wg && is_array($_wg['_items'])) {
                            foreach ($_wg['_items'] as $i) {
                                // insert into this page..
                                $ttl = jrCore_db_escape($i['widget_name']);
                                if (isset($_existing_widgets) && in_array($ttl, $_existing_widgets[$page_id])) {
                                    continue;
                                }
                                switch ($i['widget_function']) {
                                    case 'jrWidget_editor':
                                        $nam = 'widget_html';
                                        $_sv = array('html_content' => "{$i['widget_editor']}");
                                        $dat = jrCore_db_escape(json_encode($_sv));
                                        break;
                                    case 'jrWidget_code':
                                        $nam = 'widget_code';
                                        $_sv = array('code_content' => "{$i['widget_code']}");
                                        $dat = jrCore_db_escape(json_encode($_sv));
                                        break;
                                }
                                $unique_id = jrCore_create_unique_string(20);
                                $req       = "INSERT INTO {$tbl} (widget_updated, widget_page_id, widget_title, widget_module, widget_name, widget_data, widget_location, widget_weight, widget_groups, widget_unique)
                                              VALUES (UNIX_TIMESTAMP(), '{$page_id}', '{$ttl}', '{$mod}', '{$nam}', '{$dat}', '{$loc}', '{$weight}', 'all', '{$unique_id}')
                                              ON DUPLICATE KEY UPDATE widget_updated = UNIX_TIMESTAMP()";
                                jrCore_db_query($req);
                            }
                        }

                        continue 2;
                        break;
                }
                $unique_id = jrCore_create_unique_string(20);
                $req       = "INSERT INTO {$tbl} (widget_updated, widget_page_id, widget_title, widget_module, widget_name, widget_data, widget_location, widget_weight, widget_groups, widget_unique)
                              VALUES (UNIX_TIMESTAMP(), '{$page_id}', '{$ttl}', '{$mod}', '{$nam}', '{$dat}', '{$loc}', '{$weight}', 'all', '$unique_id')
                              ON DUPLICATE KEY UPDATE widget_updated = UNIX_TIMESTAMP()";
                jrCore_db_query($req);
            }
        }

        // test for {jrConstructionKit_rotator in any widget_html and move it to widget_code
        $tbl = jrCore_db_table_name('jrSiteBuilder', 'widget');
        $req = "SELECT widget_id, widget_name, widget_data FROM {$tbl} WHERE widget_data LIKE '%{jrConstructionKit_rotator%'";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt)) {
            foreach ($_rt as $row) {
                // rename {jrConstructionKit_rotator to {jrSiteBuilder_rotator
                $_data                 = json_decode($row['widget_data'], true);
                $_data['html_content'] = str_replace('jrConstructionKit_rotator', 'jrSiteBuilder_rotator', $_data['html_content']);
                $dat                   = jrCore_db_escape(json_encode($_data));
                $req                   = "UPDATE {$tbl} SET widget_name = 'widget_html', widget_data = '{$dat}' WHERE widget_id = '{$row['widget_id']}' LIMIT 1";
                jrCore_db_query($req);
            }
        }

        // now deactivate jrPanel, jrMenu and jrWidgets, and jrConstructionKit
        $_m = array('jrMenu', 'jrPanel', 'jrWidget', 'jrConstructionKit');
        foreach ($_m as $mod) {
            $tbl = jrCore_db_table_name('jrCore', 'module');
            $req = "UPDATE {$tbl} SET module_updated = UNIX_TIMESTAMP(), module_active = '0' WHERE module_directory = '{$mod}' LIMIT 1";
            jrCore_db_query($req);
            jrCore_logger('MIN', "jrSiteBuilder: (success) system import complete, disabling {$mod}  ");
        }
        jrCore_delete_all_cache_entries('jrCore', 0);
    }

    // correct for /index and rename it to /
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'page');
    $req = "SELECT page_id, page_uri FROM {$tbl} WHERE page_uri =  '/'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || $_rt == null) {
        // do the update
        $req = "SELECT page_id, page_uri FROM {$tbl} WHERE page_uri =  '/index'";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if ($_rt && is_array($_rt)) {
            $req = "UPDATE {$tbl} SET page_uri = '/' WHERE page_id =  '{$_rt['page_id']}'";
            jrCore_db_query($req);
        }

        $tbl = jrCore_db_table_name('jrSiteBuilder', 'menu');
        $req = "SELECT menu_id, menu_url FROM {$tbl} WHERE menu_url = 'index' ";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if ($_rt && is_array($_rt)) {
            $req = "UPDATE {$tbl} SET menu_url = '' WHERE menu_id =  '{$_rt['menu_id']}'";
            jrCore_db_query($req);
        }
    }

    //------------------------------------------
    // make sure each widget has a unique id
    //------------------------------------------
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'widget');
    $req = "SELECT * FROM {$tbl} WHERE widget_unique = ''";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (is_array($_rt)) {
        $i = 0;
        foreach ($_rt as $_w) {
            $unique = jrCore_create_unique_string(20);
            $req    = "UPDATE {$tbl} SET widget_unique = '{$unique}' WHERE widget_id = '{$_w['widget_id']}'";
            jrCore_db_query($req);
            $i++;
        }
        if ($i > 0) {
            jrCore_logger('INF', 'Site builder added ' . $i . ' unique ids to widgets.');
        }
    }

    return $_data;
}

/**
 * cleare the default menu items when the cache is cleared.
 * @param $_data array trigger array
 * @param $_user array User info
 * @param $_conf array Global Config
 * @param $_args array Extra arguments from trigger
 * @param $event string Event name
 * @return array
 */
function jrSiteBuilder_template_cache_reset_listener($_data, $_user, $_conf, $_args, $event)
{

    jrCore_delete_temp_value('jrSiteBuilder', 'default_menu');
    return $_data;
}

//------------------------------------
// FUNCTIONS
//------------------------------------

/**
 * Check if a given bit of template code is valid
 * @param $code string Smarty Template code
 * @return bool
 */
function jrSiteBuilder_template_code_contains_errors($code)
{
    global $_conf;
    $url = jrCore_get_module_url('jrCore');
    $cdr = jrCore_get_module_cache_dir('jrCore');
    $nam = time() . ".tpl";
    jrCore_write_to_file("{$cdr}/{$nam}", $code);
    $out = jrCore_load_url("{$_conf['jrCore_base_url']}/{$url}/test_template/{$nam}");
    @unlink("{$cdr}/{$nam}");
    if ($out && strlen($out) > 1 && (strpos($out, 'error:') === 0 || stristr($out, 'fatal error'))) {
        return true;
    }
    return false;
}

/**
 * Get the title of a widget
 * @param $title mixed array or string
 * @return string
 */
function jrSiteBuilder_get_widget_title($title)
{
    return (is_array($title)) ? $title['title'] : $title;
}

/**
 * Construct the Page Builder URI for the current URL
 * @return string
 */
function jrSiteBuilder_get_page_uri()
{
    global $_post;
    $uri = '/';
    if (isset($_post['module_url']) && strlen($_post['module_url']) > 0) {
        $uri .= "{$_post['module_url']}";
        if (isset($_post['option']) && strlen($_post['option']) > 0) {
            $uri .= "/{$_post['option']}";
            $num = 1;
            while (true) {
                if (isset($_post["_{$num}"])) {
                    $uri .= '/' . $_post["_{$num}"];
                    $num++;
                }
                else {
                    break;
                }
            }
        }
    }
    return $uri;
}

/**
 * Construct a good Menu URL from a given URL
 * @param $url string URL as given in form
 * @return string
 */
function jrSiteBuilder_get_menu_url($url)
{
    $url = trim(trim($url), '/');
    if (strpos($url, '/')) {
        $_ot = array();
        foreach (explode('/', $url) as $part) {
            $_ot[] = jrCore_url_string($part);
        }
        return implode('/', $_ot);
    }
    return jrCore_url_string($url);
}

/**
 * Get Site menu entries
 * @param bool $cache set to FALSE to bypass caching
 * @param bool|true $group_filter
 * @return array|mixed
 */
function jrSiteBuilder_get_menu_entries($cache = true, $group_filter = true)
{
    $ckey = 'sb-menu' . intval($group_filter);
    if (!$cache || !$_rp = jrCore_is_cached('jrSiteBuilder', $ckey)) {

        // Get existing menu entries
        $tbl = jrCore_db_table_name('jrSiteBuilder', 'menu');
        $req = "SELECT * FROM {$tbl} ORDER BY menu_order ASC";
        $_rt = jrCore_db_query($req, 'menu_id');

        $_rp = array(
            '_list'  => array(),
            '_items' => $_rt
        );
        if ($_rt && is_array($_rt)) {

            // First - get TOP level menu items setup
            foreach ($_rt as $mid => $_m) {
                // Remove any we are not allowed to view
                if (isset($_m['menu_group']) && $_m['menu_group'] != 'all' && !jrCore_user_is_part_of_group($_m['menu_group']) && $group_filter) {
                    unset($_rt[$mid]);
                    continue;
                }
                $pid = (int) $_m['menu_parent_id'];
                if ($pid === 0) {
                    $_rp['_list'][$mid] = $_m;
                    unset($_rt[$mid]);
                }
            }

            // Next get First Level menu items
            if (count($_rt) > 0) {
                $_mp = array();
                foreach ($_rt as $mid => $_m) {
                    $pid = (int) $_m['menu_parent_id'];
                    if (isset($_rp['_list'][$pid])) {
                        // We are a First Level menu item
                        if (!isset($_rp['_list'][$pid]['_children']) || !is_array($_rp['_list'][$pid]['_children'])) {
                            $_rp['_list'][$pid]['_children'] = array();
                        }
                        $_rp['_list'][$pid]['_children'][$_m['menu_id']] = $_m;
                        $_mp[$mid]                                       = $pid;
                        unset($_rt[$mid]);
                    }
                }
            }

            // Finally get Second Level options
            if (count($_rt) > 0) {
                foreach ($_rt as $mid => $_m) {
                    $pid = (int) $_m['menu_parent_id'];
                    if (isset($_mp[$pid])) {
                        $tid = $_mp[$pid];
                        if (isset($_rp['_list'][$tid]['_children'][$pid])) {
                            if (!isset($_rp['_list'][$tid]['_children'][$pid]['_children']) || !is_array($_rp['_list'][$tid]['_children'][$pid]['_children'])) {
                                $_rp['_list'][$tid]['_children'][$pid]['_children'] = array();
                            }
                            $ord                                                      = (int) $_m['menu_order'];
                            $_rp['_list'][$tid]['_children'][$pid]['_children'][$ord] = $_m;
                        }
                    }
                }
            }

        }
        jrCore_add_to_cache('jrSiteBuilder', $ckey, $_rp);
    }
    return $_rp;
}

/**
 * Get Data for a page based on URI
 * @param $uri string URI
 * @return mixed
 */
function jrSiteBuilder_get_page_by_uri($uri)
{
    // We need to remove any URL variables from the URI
    if (!$_rt = jrCore_get_flag('jrsitebuilder_get_page_by_uri')) {
        $tbl = jrCore_db_table_name('jrSiteBuilder', 'page');
        $req = "SELECT * FROM {$tbl} WHERE page_uri = '" . jrCore_db_escape($uri) . "' LIMIT 1";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if (!$_rt || !is_array($_rt)) {
            $_rt = 'not_found';
        }
        jrCore_set_flag('jrsitebuilder_get_page_by_uri', $_rt);
    }
    return ($_rt == 'not_found') ? false : $_rt;
}

/**
 * Get Data for a page based on filename
 * @param $skin string Skin
 * @param $uri string URI
 * @return mixed
 */
function jrSiteBuilder_get_page_by_json_file($skin, $uri)
{
    // $uri is like "/welcome" and corresponds to SkinName/sitebuilder/welcome.json
    $filename = trim($uri, '/') . '.json';
    if ($uri == '/') {
        $filename = 'index.json';
    }
    $path = APP_DIR . "/skins/{$skin}/sitebuilder/{$filename}";
    if (is_file($path)) {
        $contents = file_get_contents($path);
        if (jrCore_checktype($contents, 'json')) {
            $_cont = json_decode($contents, true);
            $_pg   = $_cont['_page'];

            // sort by weight http://stackoverflow.com/questions/2699086/sort-multi-dimensional-array-by-value
            usort($_cont['_widget'], function ($a, $b) {
                return $a['widget_weight'] - $b['widget_weight'];
            });

            $_pg['_widget'] = $_cont['_widget'];
            return $_pg;
        }
    }
    return false;
}

/**
 * Install a page from a sitebuilder JSON file
 * @param string $skin Skin
 * @param string $page JSON file
 * @return int|bool
 */
function jrSiteBuilder_install_page_from_json($skin, $page)
{
    $_pg = jrSiteBuilder_get_page_by_json_file($skin, $page);
    if (!$_pg || !is_array($_pg)) {
        return false;
    }
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'page');
    $uri = jrCore_db_escape($page);
    $ttl = jrCore_db_escape($_pg['page_title']);
    $grp = jrCore_db_escape($_pg['page_groups']);
    $act = (isset($_pg['page_active']) && $_pg['page_active'] == '1') ? 1 : 0;
    $lay = jrCore_db_escape($_pg['page_layout']);
    $cfg = jrCore_db_escape($_pg['page_settings']);
    $mta = jrCore_db_escape($_pg['page_head']);
    $req = "INSERT INTO {$tbl} (page_updated, page_uri, page_title, page_groups, page_active, page_layout, page_settings, page_head)
            VALUES (UNIX_TIMESTAMP(), '{$uri}', '{$ttl}', '{$grp}', '{$act}', '{$lay}', '{$cfg}', '{$mta}')";
    $pid = jrCore_db_query($req, 'INSERT_ID');
    if (!$pid || !jrCore_checktype($pid, 'number_nz')) {
        return false;
    }
    // add in the widgets
    if (isset($_pg['_widget']) && is_array($_pg['_widget'])) {
        $tbl = jrCore_db_table_name('jrSiteBuilder', 'widget');
        foreach ($_pg['_widget'] as $_w) {
            // Create widget
            $ttl = jrCore_db_escape($_w['widget_title']);
            $mod = jrCore_db_escape($_w['widget_module']);
            $nam = jrCore_db_escape($_w['widget_name']);
            $dta = jrCore_db_escape($_w['widget_data']);
            $loc = jrCore_db_escape($_w['widget_location']);
            $wgt = jrCore_db_escape($_w['widget_weight']);
            $grp = jrCore_db_escape($_w['widget_groups']);
            $unq = jrCore_db_escape($_w['widget_unique']);
            $req = "INSERT INTO {$tbl} (widget_updated, widget_page_id, widget_title, widget_module, widget_name, widget_data, widget_location, widget_weight, widget_groups, widget_unique)
                    VALUES (UNIX_TIMESTAMP(), '{$pid}', '{$ttl}', '{$mod}', '{$nam}', '{$dta}', '{$loc}', '{$wgt}', '{$grp}', '{$unq}') ON DUPLICATE KEY UPDATE widget_updated = UNIX_TIMESTAMP()";
            $wid = jrCore_db_query($req, 'INSERT_ID');
            if (!$wid || !jrCore_checktype($wid, 'number_nz')) {
                return false;
            }
        }
    }
    jrCore_logger('INF', "successfully installed new {$skin} Site Builder page: {$page}");

    // Reset caches
    jrCore_delete_all_cache_entries();
    return $pid;
}

/**
 * Get Data for a page based on Page ID
 * @param $id int Page ID
 * @return mixed
 */
function jrSiteBuilder_get_page_by_id($id)
{
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'page');
    $req = "SELECT * FROM {$tbl} WHERE page_id = '" . intval($id) . "' LIMIT 1";
    return jrCore_db_query($req, 'SINGLE');
}

/**
 * Get Data for a menu based on URI
 * @param $uri string URI
 * @return mixed
 */
function jrSiteBuilder_get_menu_entry_by_uri($uri)
{
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'menu');
    $req = "SELECT * FROM {$tbl} WHERE menu_url = '" . jrCore_db_escape($uri) . "' LIMIT 1";
    return jrCore_db_query($req, 'SINGLE');
}

/**
 * Get Data for a menu entry based on Menu ID
 * @param $id int Menu ID
 * @return mixed
 */
function jrSiteBuilder_get_menu_entry_by_id($id)
{
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'menu');
    $req = "SELECT * FROM {$tbl} WHERE menu_id = '" . intval($id) . "' LIMIT 1";
    return jrCore_db_query($req, 'SINGLE');
}

/**
 * Get Data for a widget by ID
 * @param $id int widget ID
 * @return mixed
 */
function jrSiteBuilder_get_widget_by_id($id)
{
    $tbl = jrCore_db_table_name('jrSiteBuilder', 'widget');
    $req = "SELECT * FROM {$tbl} WHERE widget_id = '" . intval($id) . "' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        if (isset($_rt['widget_data']) && strpos($_rt['widget_data'], '{') === 0) {
            $_rt['widget_data'] = json_decode($_rt['widget_data'], true);
        }
        return $_rt;
    }
    return false;
}

/**
 * Get Data for a widget by ID
 * @param $html_id int Page ID
 * @return mixed
 */
function jrSiteBuilder_get_widget_by_html_id($html_id)
{
    // ID will look like: "widget_id-18"
    list(, $wid) = explode('-', $html_id);
    if (!isset($wid) || !jrCore_checktype($wid, 'number_nz')) {
        return false;
    }

    $tbl = jrCore_db_table_name('jrSiteBuilder', 'widget');
    $req = "SELECT * FROM {$tbl} WHERE widget_id = '{$wid}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        if (isset($_rt['widget_data']) && strpos($_rt['widget_data'], '{') === 0) {
            $_rt['widget_data'] = json_decode($_rt['widget_data'], true);
        }
        return $_rt;
    }
    return false;
}

/**
 * Create a Site Builder Page
 * @param $_page array Page info
 * @return string
 */
function jrSiteBuilder_get_page($_page)
{
    global $_post, $_conf;
    if (!isset($_post['_uri']) || strlen($_post['_uri']) === 0) {
        $_post['_uri'] = '/';
    }
    $key = $_post['_uri'];
    if (!$out = jrCore_is_cached('jrSiteBuilder', $key)) {

        // Get all registered Widgets
        $_rw = jrCore_get_registered_module_features('jrSiteBuilder', 'widget');

        $out = '';
        $_ct = 0;
        // 3-6-3,4-4-4
        $_tm = explode(',', $_page['page_layout']);
        if ($_tm && is_array($_tm)) {

            // array(
            //     0 => '3-6-3',
            //     1 => '4-4-4'
            // )
            $_ly = array();
            $_cf = (isset($_page['page_settings']) && strlen($_page['page_settings']) > 0) ? json_decode($_page['page_settings'], true) : array();

            // Get all widgets for this page
            if ($_page['page_id'] == 0 && isset($_page['_widget']) && is_array($_page['_widget'])) {
                // its a skin provided page we've fallen through to, get it from the file.
                $_wg = $_page['_widget'];

                if (!isset($_post['module_url'])) {
                    // We are the index page, so set index so its not empty
                    jrCore_set_flag('jrsitebuilder_show_override_notice_for_custom_page', "/");
                }
                else {
                    // we're loading the page from a file, if the admin wants to customize it, they need to import to the db
                    jrCore_set_flag('jrsitebuilder_show_override_notice_for_custom_page', $_post['module_url']);
                }

            }
            else {
                $tbl = jrCore_db_table_name('jrSiteBuilder', 'widget');
                $req = "SELECT * FROM {$tbl} WHERE widget_page_id = '{$_page['page_id']}' ORDER BY widget_location ASC, widget_weight ASC";
                $_wg = jrCore_db_query($req, 'NUMERIC');
            }

            if (is_array($_wg)) {
                foreach ($_wg as $k => $w) {

                    // check the module is active, if not show activate module message here
                    if (jrCore_module_is_active($w['widget_module'])) {

                        // Check widget group
                        if (isset($w['widget_groups']) && strlen($w['widget_groups']) > 0 && $w['widget_groups'] != 'all') {
                            if (!jrUser_is_master() && !jrCore_user_is_part_of_group($w['widget_groups'])) {
                                // This user does not have access to any configured group
                                unset($_wg[$k]);
                                continue;
                            }
                        }

                        // Make sure we have a good function
                        $fnc = "{$w['widget_module']}_{$w['widget_name']}_display";
                        if (function_exists($fnc)) {
                            $out = '';
                            if (isset($w['widget_data']) && strlen($w['widget_data']) > 1) {
                                $_wc = (isset($_cf["{$w['widget_location']}"])) ? $_cf["{$w['widget_location']}"] : null;
                                $_dt = json_decode($w['widget_data'], true);
                                if (isset($_post['repair']) && $_post['repair'] == 'true') {
                                    $out = 'REPAIR MODE';
                                }
                                else {
                                    $out = $fnc($_dt, $w, $_wc);
                                    // allow functions in the titles so skins can have "View More" links.
                                    $w['widget_title'] = jrCore_parse_template($w['widget_title'], $w, 'jrSiteBuilder');
                                }
                            }
                            $_ct++;
                            $w['widget_module_title'] = (is_array($_rw["{$w['widget_module']}"]["{$w['widget_name']}"])) ? $_rw["{$w['widget_module']}"]["{$w['widget_name']}"]['title'] : $_rw["{$w['widget_module']}"]["{$w['widget_name']}"];
                        }
                        else {
                            $out = "unknown function: {$fnc}";
                        }
                    }
                    else {
                        $out = "Module inactive";
                        if (jrUser_is_admin()) {
                            $mkurl = jrCore_get_module_url('jrMarket');
                            $out   = "<div class=\"page_notice error\"> Either activate this module or delete this section. <a href=\"{$_conf['jrCore_base_url']}/{$mkurl}/browse/module?search_string={$w['widget_module']}\">Install {$w['widget_module']} here</a></div>";
                        }
                    }
                    $loc           = (int) $w['widget_location'];
                    $_ly[$loc][$k] = $w;
                    if (strlen($out) > 0) {
                        $_ly[$loc][$k]['content'] = '<div class="widget-item widget-item-' . $w['widget_name'] . '">' . $out . '</div>';
                    }
                    else {
                        $_ly[$loc][$k]['content'] = '';
                    }
                    $_ly[$loc][$k]['widget_display_number'] = ($loc + 1);
                }
            }

            $_rp = array(
                '_page' => $_page,
                '_rows' => array()
            );

            // Get the column structure on the page
            foreach ($_tm as $row => $_cols) {
                $_rp['_rows'][$row] = array(
                    '_cols' => array()
                );
                foreach (explode('-', $_cols) as $k => $col) {
                    $_rp['_rows'][$row]['_cols'][$k] = array(
                        'width' => $col
                    );
                }
            }

            $_rp['layout']              = $_ly;
            $_rp['config']              = $_cf;
            $_rp['_registered_widgets'] = jrCore_get_registered_module_features('jrSiteBuilder', 'widget');
            $out                        = jrCore_parse_template('page_row_column.tpl', $_rp, 'jrSiteBuilder');
        }
        $_rp                       = array(
            '_page'         => $_page,
            'page_content'  => $out,
            '_widget_count' => $_ct
        );
        $_rp                       = jrCore_trigger_event('jrSiteBuilder', 'page_content', $_rp, $_page);
        $_rp['show_widget_notice'] = true;
        if ($pid = jrCore_get_flag('sb-page-layout-reload')) {
            $_rp['show_widget_notice'] = false;
        }
        $out = jrCore_parse_template('page_container.tpl', $_rp, 'jrSiteBuilder');

        // page head html
        if (!isset($_post['repair']) || $_post['repair'] != 'true') {
            if (isset($_page['page_head']) && strlen($_page['page_head']) > 0) {
                $out = str_replace('</head>', $_page['page_head'] . '</head>', $out);
            }
        }

        jrCore_add_to_cache('jrSiteBuilder', $key, $out);
    }
    return $out;
}

/**
 * Get {jrSiteBuilder_default_menu} menu items in nested array order
 * @return array|bool
 */
function jrSiteBuilder_skin_default_menu_items()
{
    $_default = jrCore_get_temp_value('jrSiteBuilder', 'default_menu');
    $_list    = array();
    if ($_default && is_array($_default)) {
        // sort by menu_order if it exists
        uasort($_default, function ($a, $b) {
            return $a['menu_order'] - $b['menu_order'];
        });

        foreach ($_default as $k => $_m) {
            if (!isset($_m['menu_parent_url'])) {
                $_list[$_m['menu_url']] = $_m;
                unset($_default[$k]);
            }
        }
        if (count($_default) > 0) {
            foreach ($_default as $k => $_m) {
                // first level.
                if ($_list[$_m['menu_parent_url']]) {
                    $_list[$_m['menu_parent_url']]['_children'][$_m['menu_url']] = $_m;
                    unset($_default[$k]);
                }
            }
        }
        if (count($_default) > 0) {
            foreach ($_default as $k => $_m) {
                // second level.
                foreach ($_list as $url => $_values) {
                    if (is_array($_values['_children'])) {
                        if (in_array($_m['menu_parent_url'], array_keys($_values['_children']))) {
                            $_list[$url]['_children'][$_m['menu_parent_url']]['_children'][$k] = $_m;
                            unset($_default[$k]);
                        }
                    }
                }
            }
        }
    }
    if (count($_list) > 0) {
        return $_list;
    }
    return false;
}

//------------------------------------
// SMARTY
//------------------------------------

/**
 * add a default menu item to the menu
 * @param $params array parameters
 * @param $smarty object current Smarty object
 * @return string
 */
function smarty_function_jrSiteBuilder_default_menu($params, $smarty)
{
    if (!isset($params['title'])) {
        return jrCore_smarty_missing_error('title');
    }
    if (!isset($params['url'])) {
        return jrCore_smarty_missing_error('url');
    }
    $_menu = jrCore_get_temp_value('jrSiteBuilder', 'default_menu');

    $_new = array(
        'menu_title' => $params['title'],
        'menu_url'   => $params['url']
    );

    if (isset($params['parent'])) {
        $_new['menu_parent_url'] = $params['parent'];
    }
    if (isset($params['weight'])) {
        $_new['menu_order'] = (int) $params['weight'];
    }

    $_menu[$params['url']] = $_new;

    // need to save a copy of this to populate the jr_jrsitebuilder_menu table if the MENU EDITOR button is clicked.
    jrCore_set_temp_value('jrSiteBuilder', 'default_menu', $_menu);
    return '';
}

/**
 * Show the Site Menu (Desktop)
 * @param $params array parameters
 * @param $smarty object current Smarty object
 * @return string
 */
function smarty_function_jrSiteBuilder_menu($params, $smarty)
{
    global $_conf;
    $_rp = jrSiteBuilder_get_menu_entries(true);
    if (!$_rp['_list'] || count($_rp['_list']) == 0) {
        // get the default instead
        $_rp['_list'] = jrSiteBuilder_skin_default_menu_items();
    }
    $tpl = 'menu.tpl';
    $mod = 'jrSiteBuilder';
    if (isset($params['template']) && strlen($params['template']) > 0) {
        $tpl = $params['template'];
        $mod = $_conf['jrCore_active_skin'];
    }
    $out = jrCore_parse_template($tpl, $_rp, $mod);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Show the Site Menu (Mobile)
 * @param $params array parameters
 * @param $smarty object current Smarty object
 * @return string
 */
function smarty_function_jrSiteBuilder_mobile_menu($params, $smarty)
{
    global $_conf;
    $_rp = jrSiteBuilder_get_menu_entries(true);
    $tpl = 'menu_mobile.tpl';
    $mod = 'jrSiteBuilder';
    if (isset($params['template']) && strlen($params['template']) > 0) {
        $tpl = $params['template'];
        $mod = $_conf['jrCore_active_skin'];
    }
    $out = jrCore_parse_template($tpl, $_rp, $mod);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * initialize tiny mce editor manager.
 * @param $params array parameters
 * @param $smarty object current Smarty object
 * @return string
 */
function smarty_function_jrSiteBuilder_tinymce_init($params, $smarty)
{
    global $_mods, $_user, $_conf;

    // Initialize fields
    $_rp['theme'] = 'modern';
    $allowed_tags = explode(',', $_user['quota_jrCore_allowed_tags']);
    foreach ($allowed_tags as $tag) {
        $_rp[$tag] = true;
    }

    // See what modules are providing
    $_tm = jrCore_get_registered_module_features('jrCore', 'editor_button');
    if ($_tm && is_array($_tm)) {
        foreach ($_tm as $mod => $_items) {
            $tag       = strtolower($mod);
            $_rp[$tag] = false;
            if (jrCore_module_is_active($mod)) {
                if (!isset($_rp['_sources'])) {
                    $_rp['_sources'] = array();
                }
                $_rp['_sources'][] = "{$_conf['jrCore_base_url']}/modules/{$mod}/tinymce/plugin.min.js?v=" . $_mods[$mod]['module_version'];
                $_rp[$tag]         = true;
            }
        }
    }
    return jrCore_parse_template('form_editor.tpl', $_rp, 'jrSiteBuilder');
}

/**
 * pick up on the template 404 and show a custom page so the widgets don't exit().
 * @param array $_data
 * @param array $_user
 * @param array $_conf
 * @param array $_args
 * @param string $event
 * @return mixed
 */
function jrSiteBuilder_tpl_404_listener($_data, $_user, $_conf, $_args, $event)
{
    // Module needs to be active
    if (!jrProfile_is_profile_view() && !jrCore_is_ajax_request()) {
        $_data['file'] = $_conf['jrCore_base_dir'] . '/modules/jrSiteBuilder/templates/404.tpl';
    }
    return $_data;
}

/**
 * Smarty rotator function
 * (from the construction kit. here to allow for imports from old system.)
 * @param $params
 * @param $smarty
 * @return string
 */
function smarty_function_jrSiteBuilder_rotator($params, $smarty)
{

    // default template
    if (!isset($params['template'])) {
        $params['template'] = 'rotator_default_row.tpl';
        $params['tpl_dir']  = 'jrSiteBuilder';
    }
    // default order_by
    if (!isset($params['order_by'])) {
        $params['order_by'] = '_created RAND';
    }

    // only active profiles
    $params['search9'] = "profile_active = 1";

    // require image
    if (!isset($params['require_image'])) {
        $pfx                     = jrCore_db_get_prefix($params['module']);
        $params['require_image'] = $pfx . "_image";
    }

    $out  = smarty_function_jrCore_list($params, $smarty);
    $_rep = array(
        'unique_id'  => jrCore_create_unique_string(6),
        'row_output' => $out,
    );
    return jrCore_parse_template('rotator.tpl', $_rep, 'jrSiteBuilder');
}

/**
 * explode 2-8-2,12-0-0 into its row array.
 * @param string $seq
 * @return array
 */
function jrSiteBuilder_explode_sequence($seq)
{
    $rows  = explode(',', $seq);
    $i     = 0;
    $t     = 0;
    $l     = 1;
    $_rows = array();
    foreach ($rows as $row) {
        $layout = explode('-', $row);
        foreach ($layout as $col) {
            if ($col == 0) {
                continue;
            }

            $_rows[$i][] = array(
                'span'     => $col,
                'location' => $l,
            );
            $l++;

            $t += $col;
            if ($t % 12 == 0) {
                $i++;
            }

        }
    }
    return $_rows;
}

/**
 * Takes an array of page ids and returns the sitebuilder export file data in an array.
 * @param $_page_ids
 * @return array|bool
 */
function jrSiteBuilder_export($_page_ids)
{
    if (!is_array($_page_ids)) {
        return false;
    }
    $pid  = implode(',', $_page_ids);
    $tblm = jrCore_db_table_name('jrSiteBuilder', 'menu');
    $tblp = jrCore_db_table_name('jrSiteBuilder', 'page');
    $tblw = jrCore_db_table_name('jrSiteBuilder', 'widget');

    $req    = "SELECT * FROM {$tblp} WHERE page_id IN ($pid)";
    $_pages = jrCore_db_query($req, 'NUMERIC');
    if (!$_pages || !is_array($_pages)) {
        return false;
    }
    $_data = array();

    // parents
    $req     = "SELECT * FROM {$tblm}";
    $_parent = jrCore_db_query($req, 'menu_id');

    foreach ($_pages as $_p) {
        $_data['pages'][$_p['page_id']]['_page'] = $_p;
        // widgets
        $req      = "SELECT * FROM {$tblw} WHERE widget_page_id  = '{$_p['page_id']}'";
        $_widgets = jrCore_db_query($req, 'NUMERIC');
        if (is_array($_widgets)) {
            foreach ($_widgets as $_w) {
                $_data['pages'][$_p['page_id']]['_widget'][$_w['widget_id']]                    = $_w;
                $_data['pages'][$_p['page_id']]['_widget'][$_w['widget_id']]['widget_page_uri'] = $_p['page_uri'];
            }
        }
        // menu
        $req   = "SELECT * FROM {$tblm} WHERE concat('/',menu_url) = '" . jrCore_db_escape($_p['page_uri']) . "'";
        $_menu = jrCore_db_query($req, 'menu_id');

        if (is_array($_menu)) {
            foreach ($_menu as $_m) {
                $_data['pages'][$_p['page_id']]['_menu'][$_m['menu_id']]                    = $_m;
                $_data['pages'][$_p['page_id']]['_menu'][$_m['menu_id']]['menu_parent_url'] = ($_m['menu_parent_id'] > 0) ? $_parent[$_m['menu_parent_id']]['menu_url'] : '0';
            }
        }
    }

    return $_data;
}

/**
 * jrSiteBuilder_daily_maintenance_listener
 * <code>
 * Uses the EXPORT tool to add a package called sb_backup_1421982941.json where the number is a timestamp.
 * Old auto-created EXPORT packages are deleted.
 * </code>
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array $_data
 */
function jrSiteBuilder_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    //-----------------------------------
    // Daily Export
    //-----------------------------------
    // check if the global backups are on
    if (isset($_conf['jrSiteBuilder_backup']) && $_conf['jrSiteBuilder_backup'] == "on") {

        $tbl = jrCore_db_table_name('jrSiteBuilder', 'page');
        $req = "SELECT page_id FROM {$tbl}";
        $_rt = jrCore_db_query($req, 'page_id', false, 'page_id');

        if (!is_array($_rt)) {
            // No Site Builder content, return.
            return $_data;
        }

        // EXPORT todays backup
        $_wanted = jrSiteBuilder_export($_rt);
        $_json   = json_encode($_wanted);
        $mdir    = jrCore_get_media_directory(0, FORCE_LOCAL);
        $tim     = time();
        $cutoff  = strtotime("-{$_conf['jrSiteBuilder_backup_retain_days']} days", $tim);
        if (!jrCore_write_to_file("{$mdir}/sb_backup_{$tim}.json", $_json)) {
            jrCore_logger('CRI', "failed to write Site Builder backup file");
        }

        if (isset($_conf['jrSiteBuilder_backup_retain_days']) && $_conf['jrSiteBuilder_backup_retain_days'] > 0) {
            // clear out any older than wanted backups
            $_pkg = glob("{$mdir}/sb_backup_*.json");
            if (isset($_pkg) && is_array($_pkg) && count($_pkg) > 0) {
                $cnt = 0;
                foreach ($_pkg as $json_file) {
                    $nam   = basename($json_file);
                    $stamp = substr($nam, 10, 10);
                    if (ctype_digit($stamp) && $stamp > 0) {
                        if ($stamp < $cutoff) {
                            // too old, delete it.
                            jrCore_delete_media_file(0, $json_file);
                            $cnt++;
                        }
                    }
                }
                if ($cnt > 0) {
                    jrCore_logger('INF', "removed {$cnt} expired Site Builder backup files");
                }
            }
        }
    }
    return $_data;
}
