<?php
/**
 * Jamroom Admin Hover Menu module
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

/**
 * meta
 */
function jrAdminMenu_meta()
{
    $_tmp = array(
        'name'        => 'Admin Hover Menu',
        'url'         => 'admin_menu',
        'version'     => '1.0.12',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Add a top hover menu for one click access to anywhere in the ACP',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/544/admin-hover-menu',
        'license'     => 'mpl',
        'category'    => 'admin'
    );
    return $_tmp;
}

/**
 * init
 */
function jrAdminMenu_init()
{
    // Show admin drop down
    jrCore_register_module_feature('jrCore', 'css', 'jrAdminMenu', 'jrAdminMenu.css');
    jrCore_register_event_listener('jrCore', 'view_results', 'jrAdminMenu_view_results_listener');
    return true;
}

/**
 * Display admin drop down menu when in pages
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrAdminMenu_view_results_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post, $_conf;
    if (!jrCore_is_mobile_device() && jrUser_is_master() && strpos($_data, '<body>')) {
        if (isset($_post['__ajax']) && $_post['__ajax'] == '1') {
            return $_data;
        }
        if (jrCore_get_flag('jrcore_page_meta_header_only')) {
            return $_data;
        }
        if (isset($_post['module_url']) && isset($_post['option']) && strlen($_post['option']) > 0) {
            switch ("{$_post['module_url']}/{$_post['option']}") {
                case 'user/login':
                case 'core/log_debug':
                    return $_data;
                    break;
                default:
                    if (strpos($_post['_uri'], '/license')) {
                        return $_data;
                    }
                    if (strpos($_post['_uri'], '/changelog/')) {
                        return $_data;
                    }
                    if (strpos($_post['_uri'], '/view_queue_id/')) {
                        return $_data;
                    }
                    break;
            }
        }
        $_rp = jrAdminMenu_get_menu();
        if (isset($_conf['jrAdminSkin_customer_facing_skin']) && strlen($_conf['jrAdminSkin_customer_facing_skin']) > 2) {
            $_rp['customer_facing_skin'] = $_conf['jrAdminSkin_customer_facing_skin'];
        }
        else {
            $_rp['customer_facing_skin'] = $_conf['jrCore_active_skin'];
        }
        $out = jrCore_parse_template('admin_menu.tpl', $_rp, 'jrAdminMenu');
        $out = preg_replace('!\s+!', ' ', $out);
        return preg_replace('/<body>/', "<body>{$out}", $_data, 1);

    }
    return $_data;
}

/**
 * prepare the admin menu nested <ul> list for the menus.
 * @return array
 */
function jrAdminMenu_get_menu()
{
    global $_mods;

    // show the menu
    $_replace = array(
        '_skins' => array()
    );

    //get the mods in order.
    $_tmp = array();
    foreach ($_mods as $mod_dir => $_inf) {
        if (jrCore_module_is_active($mod_dir)) {
            $_tmp["{$_inf['module_name']}"] = $mod_dir;
        }
    }
    ksort($_tmp);

    $_out = array();
    foreach ($_tmp as $mod_dir) {
        if (!isset($_mods[$mod_dir]['module_category'])) {
            $_mods[$mod_dir]['module_category'] = 'tools';
        }
        $cat = $_mods[$mod_dir]['module_category'];
        if (!isset($_out[$cat])) {
            $_out[$cat] = array();
        }
        $_out[$cat][$mod_dir]         = $_mods[$mod_dir];
        $_out[$cat][$mod_dir]['tabs'] = jrAdminMenu_get_tabs($mod_dir);
    }
    $_replace['_modules']['core'] = $_out['core'];
    unset($_out['core']);
    $_replace['_modules'] = $_replace['_modules'] + $_out;
    ksort($_replace['_modules']);
    unset($_out);

    // get the skins:
    $_tmp = jrCore_get_skins();
    foreach ($_tmp as $skin) {
        $_mta                      = jrCore_skin_meta_data($skin);
        $_replace['_skins'][$skin] = $_mta['title'];
    }
    return $_replace;
}

/**
 * returns a list of tabs that are associate with this module in the admin area.
 * @param $module
 * @return array
 */
function jrAdminMenu_get_tabs($module)
{
    global $_conf;
    $_lang = jrUser_load_lang_strings();

    // Get registered tool views
    $_tools = jrCore_get_registered_module_features('jrCore', 'tool_view');
    $_quota = jrCore_get_registered_module_features('jrCore', 'quota_support');

    // Our current module url
    $url = jrCore_get_module_url($module);

    // Our admin tabs for the top of the view
    $_tabs = array();
    if (is_file(APP_DIR . "/modules/{$module}/config.php")) {
        $_tabs['global'] = array(
            'label' => 'global config',
            'url'   => "{$_conf['jrCore_base_url']}/{$url}/admin/global"
        );
    }
    if (isset($_quota[$module]) || is_file(APP_DIR . "/modules/{$module}/quota.php")) {
        $_tabs['quota'] = array(
            'label' => 'quota config',
            'url'   => "{$_conf['jrCore_base_url']}/{$url}/admin/quota"
        );
    }
    if (isset($_tools[$module]) || jrCore_db_get_prefix($module)) {
        $_tabs['tools'] = array(
            'label' => 'tools',
            'url'   => "{$_conf['jrCore_base_url']}/{$url}/admin/tools",
            'tools' => jrAdminMenu_get_tools($module)
        );
    }
    if (isset($_lang[$module])) {
        $_tabs['language'] = array(
            'label' => 'language',
            'url'   => "{$_conf['jrCore_base_url']}/{$url}/admin/language"
        );
    }
    if (is_dir(APP_DIR . "/modules/{$module}/img")) {
        $_tabs['images'] = array(
            'label' => 'images',
            'url'   => "{$_conf['jrCore_base_url']}/{$url}/admin/images"
        );
    }
    if (is_dir(APP_DIR . "/modules/{$module}/templates")) {
        $_tabs['templates'] = array(
            'label' => 'templates',
            'url'   => "{$_conf['jrCore_base_url']}/{$url}/admin/templates"
        );
    }
    $_tabs['info'] = array(
        'label' => 'info',
        'url'   => "{$_conf['jrCore_base_url']}/{$url}/admin/info"
    );

    // Check for additional tabs registered by the module
    $_tmp = jrCore_get_registered_module_features('jrCore', 'admin_tab');
    $_tmp = (isset($_tmp[$module])) ? $_tmp[$module] : false;
    if ($_tmp && is_array($_tmp)) {
        $_tab = array();
        $murl = jrCore_get_module_url($module);
        foreach ($_tmp as $view => $label) {
            // There are some views we cannot set
            switch ($view) {
                case 'global':
                case 'quota':
                case 'tools':
                case 'language':
                case 'templates':
                case 'style':
                case 'images':
                case 'info':
                    continue;
                    break;
            }
            $_tab[$view] = array(
                'label' => $label,
                'url'   => "{$_conf['jrCore_base_url']}/{$murl}/{$view}"
            );
        }
        $_tabs = $_tabs + $_tab;
    }
    return $_tabs;
}

/**
 * returns a list of tools that this module has in the admin area.
 * @param $module
 * @return array|bool
 */
function jrAdminMenu_get_tools($module)
{
    global $_conf;

    $tools = false;
    $murl  = jrCore_get_module_url($module);
    // Get registered tool views
    $_tool = jrCore_get_registered_module_features('jrCore', 'tool_view');
    if ((!isset($_tool[$module]) || !isset($_tool[$module]['browser'])) && jrCore_db_get_prefix($module)) {
        if (!$tools) {
            $tools = array();
        }
        $tools[] = array(
            'label' => 'DataStore Browser',
            'url'   => "{$_conf['jrCore_base_url']}/{$murl}/dashboard/browser"
        );
    }
    if (isset($_tool[$module]) && is_array($_tool[$module])) {
        foreach ($_tool[$module] as $view => $_v) {
            if (strpos($view, 'http') === 0) {
                $tools[] = array(
                    'label' => $_v[0],
                    'url'   => $view
                );
            }
            else {
                $tools[] = array(
                    'label' => $_v[0],
                    'url'   => "{$_conf['jrCore_base_url']}/{$murl}/{$view}"
                );
            }
        }
    }

    return $tools;
}
