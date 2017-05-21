<?php
/**
 * Jamroom Editor Embedded Media module
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
// Tabs - show Tabs in Embed
//------------------------------
function view_jrEmbed_tabs($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    $_data = array(
        'tabs'           => array(),
        'ready_function' => ''
    );

    // Figure our default tab
    $def = false;
    if (isset($_post['default']) && $_post['default'] != 'undefined') {
        $def = $_post['default'];
    }
    elseif (isset($_conf['jrEmbed_default_tab']) && strlen($_conf['jrEmbed_default_tab']) > 0) {
        $def = $_conf['jrEmbed_default_tab'];
    }

    // Round up modules
    $_ln = jrUser_load_lang_strings();
    $_md = jrEmbed_get_active_modules();
    if ($_md && is_array($_md)) {
        foreach ($_md as $mod => $nam) {
            $val = 'on';
            if (!jrUser_is_admin()) {
                $val = jrUser_get_profile_home_key("quota_{$mod}_allowed");
            }
            if ($val == 'on') {
                if (!jrUser_is_admin() && !jrUser_is_power_user() && !jrUser_is_multi_user()) {
                    // This user only has their "home" profile - exclude tabs for
                    // modules that we have not created items for
                    $val = $_user["profile_{$mod}_item_count"];
                    if ((isset($_conf['jrEmbed_profile_only']) && $_conf['jrEmbed_profile_only'] == 'off') || ($val && $val > 0) || $mod == 'jrUpimg') {
                        $_data['tabs'][$mod] = array(
                            'module' => $mod,
                            'name'   => (isset($_ln[$mod]['menu'])) ? $_ln[$mod]['menu'] : jrCore_get_module_url($mod)
                        );
                    }

                }
                else {
                    $_data['tabs'][$mod] = array(
                        'module' => $mod,
                        'name'   => (isset($_ln[$mod]['menu'])) ? $_ln[$mod]['menu'] : jrCore_get_module_url($mod)
                    );
                }
            }
        }
    }
    if ($def) {
        $_data['default_module'] = $def;
        // check that the default tab is in the list of active tabs.
        if (!isset($_data['tabs'][$def])) {
            $_data['default_module'] = key($_data['tabs']);
        }
    }

    // Let other modules modify us if needed
    $_data = jrCore_trigger_event('jrEmbed', 'embed_tabs', $_data);

    jrCore_page_set_meta_header_only();
    $_tm = array("jrEmbed_load_module('{$def}', 1, '')");
    jrCore_create_page_element('javascript_ready_function', $_tm);

    $out = jrCore_parse_template('tabs.tpl', $_data, 'jrEmbed');
    jrCore_page_html($out);
    jrCore_page_display();
}

//------------------------------
// Load a module tab
//------------------------------
function view_jrEmbed_load_module($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['m']) || !jrCore_module_is_active($_post['m'])) {
        // Try to get one so we don't show an error
        if (isset($_conf['jrEmbed_default_tab']) && strlen($_conf['jrEmbed_default_tab']) > 0 && jrCore_module_is_active($_conf['jrEmbed_default_tab'])) {
            $_post['m'] = $_conf['jrEmbed_default_tab'];
        }
        else {
            $_md = jrEmbed_get_active_modules();
            if ($_md && is_array($_md)) {
                $_post['m'] = array_keys($_md);
                $_post['m'] = reset($_post['m']);
                if (!jrCore_module_is_active($_post['m'])) {
                    jrCore_notice('error', 'invalid module');
                }
            }
            else {
                jrCore_notice('error', 'invalid module');
            }
        }
    }

    if (jrCore_db_get_prefix($_post['m'])) {
        if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
            $_post['p'] = 1;
        }

        // Search params
        $_sp = array(
            'pagebreak'                    => 8,
            'page'                         => $_post['p'],
            'order_by'                     => array('_item_id' => 'desc'),
            'exclude_jrUser_keys'          => true,
            'exclude_jrProfile_quota_keys' => true
        );

        // search string
        if (isset($_post['ss']) && strlen($_post['ss']) > 0 && $_post['ss'] !== "false" && $_post['ss'] !== "undefined") {
            if (strpos($_post['ss'], ':')) {
                list($k, $v) = explode(':', $_post['ss']);
                $_sp['search'] = array(
                    "{$k} = {$v}"
                );
            }
            else {
                $pfx           = jrCore_db_get_prefix($_post['m']);
                $_sp['search'] = array(
                    "{$pfx}_% like %{$_post['ss']}%"
                );
            }
        }

        // See what we are showing
        if (!isset($_conf['jrEmbed_profile_only']) || $_conf['jrEmbed_profile_only'] == 'on') {
            if (!isset($_sp['search']) || !is_array($_sp['search'])) {
                $_sp['search'] = array();
            }
            $pid = $_user['user_active_profile_id'];
            if ($_tm = jrProfile_get_user_linked_profiles($_SESSION['_user_id'])) {
                $pid = "{$pid}," . implode(',', array_keys($_tm));
            }
            $_sp['search'][] = "_profile_id in {$pid}";
        }

        // Trigger event for module to add or change params
        $_sp = jrCore_trigger_event('jrEmbed', 'embed_params', $_sp, array(), $_post['m']);

        // Get items
        $_rt = jrCore_db_search_items($_post['m'], $_sp);
        if (!$_rt || !is_array($_rt)) {
            $_rt = array();
        }
        // Trigger event for modules
        $_rt['show_search'] = true;
        $_rt['show_pager']  = true;
        $_rt                = jrCore_trigger_event('jrEmbed', 'embed_variables', $_rt, array(), $_post['m']);
    }
    else {
        $_rt = array();
    }

    // Process
    $out = '';
    if ($_rt['show_search'] === true) {
        $out .= jrCore_parse_template('tab_search.tpl', $_rt, 'jrEmbed');
    }
    if (is_file(APP_DIR . "/modules/{$_post['m']}/templates/jrEmbed_item_list.tpl")) {
        $tpl = 'jrEmbed_item_list.tpl';
    }
    else {
        $tpl = 'tab_ajax_' . jrCore_get_module_url($_post['m']) . '.tpl';
    }

    $out .= jrCore_parse_template($tpl, $_rt, $_post['m']);

    if ($_rt['show_pager'] === true) {
        $out .= jrCore_parse_template('tab_pager.tpl', $_rt, 'jrEmbed');
    }
    return $out;
}
