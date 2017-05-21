<?php
/**
 * Jamroom 5 System Tips module
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

/**
 * meta
 */
function jrTips_meta()
{
    $_tmp = array(
        'name'        => 'System Tips',
        'url'         => 'tips',
        'version'     => '2.0.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Provides hover tips functionality for all modules',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2868/system-tips',
        'license'     => 'mpl',
        'requires'    => 'jrCore:6.0.0',
        'category'    => 'core'
    );
    return $_tmp;
}

/**
 * init
 */
function jrTips_init()
{
    // Custom CSS and JS
    jrCore_register_module_feature('jrCore', 'css', 'jrTips', 'jrTips.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrTips', 'jrTips.js');

    // Add tip on/off checkbox to User Settings
    jrCore_register_event_listener('jrCore', 'form_display', 'jrTips_form_display_listener');

    // Show tips
    jrCore_register_event_listener('jrCore', 'module_view', 'jrTips_create_view_listener');
    jrCore_register_event_listener('jrCore', 'index_template', 'jrTips_create_view_listener');
    jrCore_register_event_listener('jrCore', 'skin_template', 'jrTips_create_view_listener');
    jrCore_register_event_listener('jrProfile', 'profile_view', 'jrTips_create_view_listener');

}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Adds a "show tips" field to the User Settings
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrTips_form_display_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrUser_is_admin()) {
        if (!jrCore_module_is_active('jrTips')) {
            return $_data;
        }
        if ($_data['form_view'] == 'jrUser/account') {
            $_lng = jrUser_load_lang_strings();
            $_tmp = array(
                'name'          => "user_jrTips_enabled",
                'type'          => 'checkbox',
                'default'       => 'on',
                'validate'      => 'onoff',
                'label'         => $_lng['jrTips'][6],
                'help'          => $_lng['jrTips'][7],
                'required'      => false,
                'form_designer' => false // no form designer or we can't turn it off
            );
            jrCore_form_field_create($_tmp);
        }
    }
    return $_data;
}

/**
 * Check for interface tips
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrTips_create_view_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post, $_mods;

    // Only admin users see tips
    if (!jrUser_is_admin()) {
        return $_data;
    }

    // Don't add tips on views we can't add tips to
    if (!jrCore_is_view_request() || jrCore_is_ajax_request() || (isset($_post['option']) && $_post['option'] == 'image')) {
        return $_data;
    }

    if (!jrCore_module_is_active('jrTips')) {
        // We are not enabled globally
        return $_data;
    }
    if (isset($_user['user_jrTips_enabled']) && $_user['user_jrTips_enabled'] == 'off') {
        // We've been disabled by the user
        return $_data;
    }
    if (jrCore_is_mobile_device()) {
        // We don't show to mobile - too busy
        return $_data;
    }
    if (strpos($_SERVER['REQUEST_URI'], '__ajax') || strpos($_SERVER['REQUEST_URI'], '_v=') || jrCore_get_flag('jrTips_qtip_loaded')) {
        // already run or not active
        return $_data;
    }
    // We don't load on image views
    $url = jrCore_get_module_url('jrImage');
    if (strpos($_SERVER['REQUEST_URI'], "/{$url}/img/")) {
        return $_data;
    }

    // Check for registered tips
    $_tm = jrCore_get_registered_module_features('jrTips', 'tip');
    if (!$_tm) {
        // no registered tips
        return $_data;
    }
    jrCore_set_flag('jrTips_qtip_loaded', 1);

    $_ck = false;
    if (isset($_COOKIE['jrTips_hide']) && strlen($_COOKIE['jrTips_hide']) > 0) {
        $_ck = json_decode($_COOKIE['jrTips_hide'], true);
    }

    $_ln = jrUser_load_lang_strings();
    // See if we have modules that have registered a tip for this view
    $_tt     = array();
    $_sl     = array();
    $_md     = array();
    $num     = 0;
    $section = jrTips_which_section($event);
    foreach ($_tm as $mod => $view) {

        // Make sure we're not turned off purposefully for this module
        if ($_ck && isset($_ck[$mod])) {
            continue;
        }

        $_js = array();
        // Make sure we have tips...
        $func = "{$mod}_tips";
        if (!is_file(APP_DIR . "/modules/{$mod}/tips.php")) {
            continue;
        }
        if (!function_exists($func)) {
            require_once APP_DIR . "/modules/{$mod}/tips.php";
            if (!function_exists($func)) {
                continue;
            }
        }
        $_view = $func($_post, $_user, $_conf);

        // Multiple tip views...
        if ($_view && is_array($_view)) {
            foreach ($_view as $_inf) {

                if (!isset($_inf['view']) || strlen($_inf['view']) === 0) {
                    continue;
                }

                if ($_inf['view'] == 'VIEW_ACP_MODULES' && $section == 'ACP_MODULES') {
                    $match = true;
                }
                elseif ($_inf['view'] == 'VIEW_ACP_SKINS' && $section == 'ACP_SKINS') {
                    $match = true;
                }
                elseif ($_inf['view'] == 'VIEW_ACP_ALL' && ($section == 'ACP_MODULES' || $section == 'ACP_SKINS')) {
                    $match = true;
                }
                elseif ($_inf['view'] == 'VIEW_PROFILES' && $section == 'PROFILE') {
                    $match = true;
                }
                elseif ($_inf['view'] == 'VIEW_DASHBOARD' && $section == 'DASHBOARD') {
                    $match = true;
                }
                else {
                    // show on just one specific view
                    $_inf['view'] = trim($_inf['view'], '/'); // trim the trailing /
                    // View matching
                    if (strpos($_inf['view'], $_conf['jrCore_base_url']) === 0) {
                        $view = $_inf['view'];
                    }
                    else {
                        $view = "{$_conf['jrCore_base_url']}/{$_inf['view']}";
                    }
                    // Check for anchoring
                    $m_url = rtrim(trim(jrCore_get_current_url()), '/');
                    $match = false;
                    if (strpos($view, '$') && strrpos($view, '$') === (strlen($view) - 1)) {
                        $view = substr($view, 0, strlen($view) - 1);
                        // We must match exactly
                        if ($m_url == $view) {
                            $match = true;
                        }
                    }
                    else {
                        if (strpos($m_url, $view) === 0) {
                            $match = true;
                        }
                    }
                }

                if ($match) {
                    // We have a match - check group
                    if (!isset($_inf['group'])) {
                        $_inf['group'] = 'master';
                    }
                    switch ($_inf['group']) {
                        case 'master':
                            if (!jrUser_is_master()) {
                                continue 2;
                            }
                            break;
                        case 'admin':
                            if (!jrUser_is_admin()) {
                                continue 2;
                            }
                            break;
                        case 'power':
                            if (!jrUser_is_power_user()) {
                                continue 2;
                            }
                            break;
                        case 'multi':
                            if (!jrUser_is_multi_user()) {
                                continue 2;
                            }
                            break;
                        case 'visitor':
                            if (jrUser_is_logged_in()) {
                                continue 2;
                            }
                            break;
                        default:
                            if (!jrUser_is_logged_in()) {
                                continue 2;
                            }
                            break;
                    }
                    $_js[$num] = $_inf;
                    $_md[$num] = $mod;
                    $_sl[]     = (isset($_inf['selector'])) ? $_inf['selector'] : '#content';
                    $num++;
                }
            }
        }
        $cnt = count($_js);
        if ($cnt > 0) {
            foreach ($_js as $k => $_inf) {
                $sel = '#content';
                if (isset($_inf['selector'])) {
                    $sel = $_inf['selector'];
                }
                $pos = 'bottom right';
                $add = '';
                if (isset($_inf['position'])) {
                    $pos = $_inf['position'];
                    if ($pos == 'top center') {
                        $add = ", my: 'top center' ";
                    }
                }
                if (isset($_inf['my_position'])) {
                    $add = ", my: '{$_inf['my_position']}'";
                }
                $sty = '';
                if (isset($_inf['pointer']) && $_inf['pointer'] === false) {
                    $sty = ', style: { tip: false }';
                }

                $btn = '';
                if (isset($_inf['button_url']) && jrCore_checktype($_inf['button_url'], 'url')) {
                    $txt = ($_inf['button']) ? $_inf['button'] : $_ln['jrTips'][4]; // close or button text
                    $txt = '<div class="qtip-close" onclick="jrCore_window_location(&#39;' . $_inf['button_url'] . '&#39;);">' . $txt . '</div>';
                    $btn = "button: $('{$txt}'),";
                }

                // Extra elements (video, documentation, etc.)
                $_xl = array();

                // Check for Documentation
                if (isset($_inf['doc_url']) && jrCore_checktype($_inf['doc_url'], 'url')) {
                    $vtext = $_inf['doc_url'];
                    if (isset($_inf['doc_title'])) {
                        $vtext = $_inf['doc_title'];
                    }
                    $_xl['doc'] = "<a href=\"{$_inf['doc_url']}\" target=\"_blank\">{$vtext}</a>";
                }

                // Close
                if (isset($_inf['cookie']) && $_inf['cookie'] === false) {
                    $_xl['close'] = "<a onclick=\"jrTips_close_tip()\">{$_ln['jrTips'][4]}</a>";
                }
                else {
                    $_xl['close'] = "<a onclick=\"jrTips_close_tour('{$_md[$k]}', 0)\">{$_ln['jrTips'][4]}</a>";
                }

                foreach ($_xl as $xk => $x) {
                    $_xl[$xk] = "<li class=\"tour-extra tour-{$xk}\">{$x}</li>";
                }
                $_inf['text'] .= '<br><ul class="tour-list">' . implode(' ', $_xl) . '</ul>';
                if (jrUser_is_logged_in()) {
                    $_inf['text'] .= "<div class=\"tour-stop\"><a onclick=\"jrTips_stop_tour()\">" . jrCore_entity_string($_ln['jrTips'][5]) . "</a></div>";
                }

                switch ($sel) {
                    case 'window':
                    case 'document.body':
                        break;
                    default:
                        $sel = "'{$sel}'";
                }
                $_tt[] = "$({$sel}).qtip({ {$btn} content: { title: '" . addslashes($_inf['title']) . "', text: '" . addslashes($_inf['text']) . "' } $sty , show: { effect: function() { $(this).fadeIn(250); }, delay: " . $_conf['jrTips_delay'] . " }, position: { at: '{$pos}'{$add} }, hide: { delay: 200, fixed: true, effect: function() { $(this).fadeOut(250); }    } });";
            }
        }
    }
    if (count($_tt) > 0) {

        // Dependencies
        if (!jrCore_get_flag('jrTips_qtip_js_loaded')) {
            $_tmp = array('source' => "{$_conf['jrCore_base_url']}/modules/jrTips/contrib/qtips2/jquery.qtip.css?_v={$_mods['jrTips']['module_version']}");
            jrCore_create_page_element('css_footer_href', $_tmp);
            $_tmp = array('source' => "{$_conf['jrCore_base_url']}/modules/jrTips/contrib/qtips2/imagesloaded.pkg.min.js?_v={$_mods['jrTips']['module_version']}");
            jrCore_create_page_element('javascript_footer_href', $_tmp);
            $_tmp = array('source' => "{$_conf['jrCore_base_url']}/modules/jrTips/contrib/qtips2/jquery.qtip.min.js?_v={$_mods['jrTips']['module_version']}");
            jrCore_create_page_element('javascript_footer_href', $_tmp);
            jrCore_set_flag('jrTips_qtip_js_loaded', 1);
        }

        jrCore_create_page_element('javascript_ready_function', $_tt);
        jrCore_create_page_element('javascript_embed', array("var __tt = '" . implode(',', $_sl) . "'"));
    }
    return $_data;
}


//----------------------------
// Functions
//----------------------------

/**
 * returns ADMIN, DASHBOARD, PROFILE or false
 * ( Logic is taken from jrAdminSkin_module_view_listener() which tries to determine profile/admin/skin.  Not the best, but all we have right now. )
 * @param string $event
 * @return mixed
 */
function jrTips_which_section($event)
{
    global $_post;
    if (isset($_post['option'])) {
        $current = $_post['module_url'] . '/' . $_post['option'];
        switch ($_post['option']) {
            case 'admin':
            case 'support':
            case 'browser':
            case 'template_modify':
            case 'template_compare':
            case 'quota_transfer':
            case 'performance_history':
            case 'skin_menu_modify':
            case 'install_result':
            case 'release_system_update':
                return 'ACP_MODULES';
                break;
            case 'skin_admin':
                return 'ACP_SKINS';
                break;
            case 'dashboard':
            case 'dashboard_config':
            case 'debug_log':
            case 'php_error_log':
                return 'DASHBOARD';
                break;
            default:
                // Some Core URLs have to be hard coded since they are not registered as a tool view
                $url    = jrCore_get_module_url('jrCore');
                $_admin = array(
                    "{$url}/log_debug" => 1,
                    "{$url}/search"    => 1
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
                if (isset($_admin[$current])) {
                    if (isset($_post['skin']) || $_post['option'] == 'skin_admin') {
                        return 'ACP_SKINS';
                    }
                    else {
                        return 'ACP_MODULES';

                    }
                }
                break;
        }
    }
    if (jrProfile_is_profile_view()) {
        return 'PROFILE';
    }
    if ($event == 'profile_view') {
        return 'PROFILE';
    }
    return false;

}
