<?php
/**
 * Jamroom Admin Skin module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
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
 *
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function jrAdminSkin_meta()
{
    $_tmp = array(
        'name'        => 'Admin Skin',
        'url'         => 'admin_skin',
        'version'     => '1.2.14',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'User a different skin for the Site, Admin Control Panel and Profiles',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/546/admin-skin',
        'category'    => 'admin',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrAdminSkin_init()
{
    // Switch skins when running a view function
    jrCore_register_event_listener('jrCore', 'template_cache_reset', 'jrAdminSkin_template_cache_reset_listener'); //integrity check.

    jrCore_register_event_listener('jrCore', 'module_view', 'jrAdminSkin_module_view_listener'); //forms should show profile skin.
    jrCore_register_event_listener('jrCore', 'form_display', 'jrAdminSkin_form_display_listener'); //checkbox
    jrCore_register_event_listener('jrCore', 'run_view_function', 'jrAdminSkin_run_view_function_listener'); //save checkbox
    jrCore_register_event_listener('jrCore', 'form_field_create', 'jrAdminSkin_form_field_create_listener'); //uncheck checkbox
    jrCore_register_event_listener('jrCore', 'profile_template', 'jrAdminSkin_profile_template_listener'); //set profile skin

    // Our tips
    jrCore_register_module_feature('jrTips', 'tip', 'jrAdminSkin', 'tip');
    return true;
}

/**
 * Check for an Admin Skin View
 * @param $_data array trigger array
 * @param $_user array User info
 * @param $_conf array Global Config
 * @param $_args array Extra arguments from trigger
 * @param $event string Event name
 * @return array
 */
function jrAdminSkin_module_view_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_conf, $_post;

    // Don't add menu on views we can't support
    if (!jrCore_is_view_request() || jrCore_is_ajax_request() || (isset($_post['option']) && $_post['option'] == 'image')) {
        return $_data;
    }

    if (!isset($_conf['jrAdminSkin_admin_skin']{1})) {
        $_conf['jrAdminSkin_admin_skin'] = $_conf['jrCore_active_skin'];
    }
    if (!isset($_conf['jrAdminSkin_profile_skin']{1})) {
        $_conf['jrAdminSkin_profile_skin'] = $_conf['jrCore_active_skin'];
    }

    $current = $_post['module_url'];
    if (isset($_post['option'])) {
        $current = $_post['module_url'] . '/' . $_post['option'];
    }
    $show_skin = $_conf['jrCore_active_skin'];
    $_skins    = jrCore_get_skins();

    if (jrUser_is_admin()) {
        if (!isset($_post['option'])) {
            $_post['option'] = '';
        }
        $from = jrCore_is_profile_referrer();
        if (jrCore_checktype($from, 'url') && ($_post['option'] == 'create' || $_post['option'] == 'update')) {
            // Coming FROM a profile (create, etc.)
            $show_skin = $_conf['jrAdminSkin_profile_skin'];

            // jrMeta /update is an admin view, all other modules /update views are coming from the profile.
            if ($_post['module'] == 'jrMeta' && isset($_skins["{$_conf['jrAdminSkin_admin_skin']}"])) {
                $show_skin = $_conf['jrAdminSkin_admin_skin'];
            }
        }
        else {
            switch ($_post['option']) {
                case 'admin':
                case 'support':
                case 'browser':
                case 'dashboard':
                case 'dashboard_config':
                case 'skin_admin':
                case 'template_modify':
                case 'template_compare':
                case 'quota_transfer':
                case 'performance_history':
                case 'skin_menu_modify':
                case 'install_result':
                case 'release_system_update':
                    if (isset($_skins["{$_conf['jrAdminSkin_admin_skin']}"])) {
                        $show_skin = $_conf['jrAdminSkin_admin_skin'];
                    }
                    break;
                default:
                    // Some Core URLs have to be hard coded since they are not registered as a tool view
                    $url    = jrCore_get_module_url('jrCore');
                    $_admin = array(
                        "{$url}/log_debug"     => 1,
                        "{$url}/debug_log"     => 1,
                        "{$url}/php_error_log" => 1,
                        "{$url}/search"        => 1
                    );
                    $_tool  = jrCore_get_registered_module_features('jrCore', 'tool_view');
                    foreach ($_tool as $module => $_option) {
                        if (is_array($_option)) {
                            foreach ($_option as $o => $title) {
                                $m                     = jrCore_get_module_url($module);
                                $_admin[$m . '/' . $o] = 1;
                            }
                        }
                    }
                    $_tabs = jrCore_get_registered_module_features('jrCore', 'admin_tab');
                    foreach ($_tabs as $module => $_option) {
                        if (is_array($_option)) {
                            foreach ($_option as $o => $title) {
                                $m                     = jrCore_get_module_url($module);
                                $_admin[$m . '/' . $o] = 1;
                            }
                        }
                    }
                    if (isset($_admin[$current]) && isset($_skins["{$_conf['jrAdminSkin_admin_skin']}"])) {
                        $show_skin = $_conf['jrAdminSkin_admin_skin'];
                    }
                    break;
            }
        }
    }
    else {
        $show_skin = $_conf['jrAdminSkin_profile_skin'];
    }

    $_conf['jrAdminSkin_customer_facing_skin'] = $_conf['jrCore_active_skin'];
    if ($_conf['jrCore_active_skin'] !== $show_skin && is_file(APP_DIR . "/skins/{$show_skin}/include.php")) {
        $_conf['jrCore_active_skin'] = $show_skin;
        $func                        = "{$show_skin}_skin_init";
        if (!function_exists($func)) {
            require_once APP_DIR . "/skins/{$show_skin}/include.php";
            if (function_exists($func)) {
                $func();
            }
        }
        // DO NOT remove this $_conf - it is required for it work right
        $_conf = jrCore_load_skin_config($show_skin, $_conf);
    }
    return $_data;
}

/**
 * Reset all admin skin caches when caches are reset
 * @param $_data array trigger array
 * @param $_user array User info
 * @param $_conf array Global Config
 * @param $_args array Extra arguments from trigger
 * @param $event string Event name
 * @return array
 */
function jrAdminSkin_template_cache_reset_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_module_is_active('jrAdminSkin') && isset($_conf['jrAdminSkin_admin_skin']{1})) {
        if (isset($_conf['jrAdminSkin_customer_facing_skin'])) {
            $_data[] = $_conf['jrAdminSkin_customer_facing_skin'];
        }
        if (isset($_conf['jrAdminSkin_profile_skin'])) {
            $_data[] = $_conf['jrAdminSkin_profile_skin'];
        }
    }
    return $_data;
}

/**
 * Add admin skin checkbox to skin config
 * @param $_data array trigger array
 * @param $_user array User info
 * @param $_conf array Global Config
 * @param $_args array Extra arguments from trigger
 * @param $event string Event name
 * @return array
 */
function jrAdminSkin_form_display_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    // See if this module supports actions
    if (isset($_post['module']) && $_post['module'] == 'jrCore' && isset($_post['option']) && $_post['option'] == 'skin_admin' && jrCore_module_is_active('jrAdminSkin') && ((isset($_post['_1']) && ($_post['_1'] == 'info') || !isset($_post['_1']) || $_post['_1'] == ''))) {

        if (!isset($_post['skin'])) {
            $_post['skin'] = $_conf['jrCore_active_skin'];
        }

        // register setting admin_skin
        $tbl = jrCore_db_table_name('jrCore', 'setting');
        $req = "SELECT `name` FROM {$tbl} WHERE `module` = 'jrAdminSkin'";
        $_as = jrCore_db_query($req, 'name');

        if ($_as == false || (is_array($_as) && !in_array('admin_skin', array_keys($_as)))) {
            $_tmp = array(
                'name'     => 'admin_skin',
                'default'  => '',
                'type'     => 'hidden',
                'required' => 'off',
                'validate' => 'false',
            );
            jrCore_register_setting('jrAdminSkin', $_tmp);
        }

        // checked
        $act = 'off';
        if (isset($_conf['jrAdminSkin_admin_skin']) && $_conf['jrAdminSkin_admin_skin'] == $_post['skin']) {
            $act = 'on';
        }

        $_tmp = array(
            'name'     => 'skin_admin',
            'label'    => 'set as active admin skin',
            'help'     => "If you would like to use this skin for the admin area of your site, check this option and save.",
            'type'     => 'checkbox',
            'value'    => $act,
            'validate' => 'onoff'
        );
        jrCore_form_field_create($_tmp);

        // register setting profile skin
        if ($_as == false || (is_array($_as) && !in_array('profile_skin', array_keys($_as)))) {
            $_tmp = array(
                'name'     => 'profile_skin',
                'default'  => '',
                'type'     => 'hidden',
                'required' => 'off',
                'validate' => 'false',
            );
            jrCore_register_setting('jrAdminSkin', $_tmp);
        }

        // Active Admin Skin
        $act = 'off';
        if (isset($_conf['jrAdminSkin_profile_skin']) && $_conf['jrAdminSkin_profile_skin'] == $_post['skin']) {
            $act = 'on';
        }

        $_tmp = array(
            'name'     => 'skin_profile',
            'label'    => 'set as active profile skin',
            'help'     => "If you would like to use this skin for the profiles area of your site, check this option and save.",
            'type'     => 'checkbox',
            'value'    => $act,
            'validate' => 'onoff'
        );
        jrCore_form_field_create($_tmp);

    }
    return $_data;
}

/**
 * save admin skin if the checkbox is checked.
 * @param $_data array trigger array
 * @param $_user array User info
 * @param $_conf array Global Config
 * @param $_args array Extra arguments from trigger
 * @param $event string Event name
 * @return array
 */
function jrAdminSkin_run_view_function_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['view'] == 'skin_admin_save') {
        $flag = false;
        if (isset($_data['skin_admin']) && $_data['skin_admin'] == 'on' && jrCore_module_is_active('jrAdminSkin')) {
            jrCore_set_setting_value('jrAdminSkin', 'admin_skin', $_data['skin']);
            $flag = true;
        }

        if (isset($_data['skin_profile']) && $_data['skin_profile'] == 'on' && jrCore_module_is_active('jrAdminSkin')) {
            jrCore_set_setting_value('jrAdminSkin', 'profile_skin', $_data['skin']);
            $flag = true;
        }

        if ($flag) {
            jrCore_delete_all_cache_entries();
        }
    }
    return $_data;
}

/**
 * unset "Set As Active Skin" checkbox if the admin skin is different from the front end skin.
 * @param $_data array trigger array
 * @param $_user array User info
 * @param $_conf array Global Config
 * @param $_args array Extra arguments from trigger
 * @param $event string Event name
 * @return array
 */
function jrAdminSkin_form_field_create_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;

    // skin_active
    if (isset($_data['name']) && $_data['name'] == 'skin_active') {
        if (isset($_post['skin']) && $_conf['jrAdminSkin_customer_facing_skin'] == $_post['skin']) {
            $_data['value'] = "on";
        }
        else {
            $_data['value'] = "off";
        }
    }

    // skin_delete (hide the checkbox for delete if this skin is active.)
    if (isset($_data['name']) && $_data['name'] == 'skin_delete') {
        if (isset($_post['skin']) && $_conf['jrAdminSkin_customer_facing_skin'] == $_post['skin']) {
            $_data['type']  = 'hidden';
            $_data['value'] = 'off';
        }
    }
    return $_data;
}

/**
 * its a profile view, set the profile skin.
 * @param $_data array trigger array
 * @param $_user array User info
 * @param $_conf array Global Config
 * @param $_args array Extra arguments from trigger
 * @param $event string Event name
 * @return array
 */
function jrAdminSkin_profile_template_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_conf;
    // check that this profile url belongs to a live profile, otherwise, its probably a Site Builder url /somewhere/lower-level
    if (isset($_data['module_url'])) {
        $_pr = jrCore_db_get_item_by_key('jrProfile', 'profile_url', $_data['module_url']);
        if (!$_pr || !is_array($_pr)) {
            return $_data;
        }
        if (isset($_conf['jrAdminSkin_profile_skin']) && $_conf['jrAdminSkin_profile_skin'] != $_conf['jrCore_active_skin'] && jrCore_module_is_active('jrAdminSkin')) {
            $_skins = jrCore_get_skins();
            if (in_array($_conf['jrCore_active_skin'], $_skins) && (in_array($_conf['jrAdminSkin_profile_skin'], $_skins))) {
                $_conf['jrCore_active_skin'] = $_conf['jrAdminSkin_profile_skin'];
                $func                        = "{$_conf['jrCore_active_skin']}_skin_init";
                if (!function_exists($func)) {
                    require_once APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/include.php";
                    if (function_exists($func)) {
                        $func();
                    }
                }
            }
        }
    }
    return $_data;
}
