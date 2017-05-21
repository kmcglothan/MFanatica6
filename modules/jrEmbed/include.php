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

/**
 * meta
 */
function jrEmbed_meta()
{
    $_tmp = array(
        'name'        => 'Editor Embedded Media',
        'url'         => 'embed',
        'version'     => '1.3.10',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Adds an embed button to the WYSIWYG editor for embedding items',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/194/editor-embedded-media',
        'license'     => 'mpl',
        'priority'    => 200, // LOW load priority (we want other listeners to run first)
        'category'    => 'forms'
    );
    return $_tmp;
}

/**
 * init
 */
function jrEmbed_init()
{
    // register our custom JS/CSS
    jrCore_register_module_feature('jrCore', 'javascript', 'jrEmbed', 'jrEmbed.js');
    jrCore_register_module_feature('jrCore', 'css', 'jrEmbed', 'jrEmbed.css');

    // Events
    jrCore_register_event_trigger('jrEmbed', 'embed_tabs', 'Fired when the available tabs are generated');
    jrCore_register_event_trigger('jrEmbed', 'embed_params', 'Fired with db_search_items parameters for the selected module');
    jrCore_register_event_trigger('jrEmbed', 'embed_variables', 'Fired with db_search_items results for the selected module');
    jrCore_register_event_trigger('jrEmbed', 'embed_html', 'Fired with item that is being embedded for custom output');

    // Core support
    $_tmp = array(
        'label' => 'Show in Editor',
        'help'  => 'If checked, the &quot;Embed Local Media&quot; button will show in the editor',
        'default' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrEmbed', 'on', $_tmp);

    // We have a custom editor button we provide
    jrCore_register_module_feature('jrCore', 'editor_button', 'jrEmbed', 'on');

    // Watch for [embed] strings inside text
    $_tmp = array(
        'wl'    => 'embed',
        'label' => 'Convert Embed Tags',
        'help'  => 'If active, Embed Tags (i.e. [jrEmbed module=&quot;jrVideo&quot; id=&quot;5&quot]) will be converted to the proper HTML to show the embedded item.'
    );
    jrCore_register_module_feature('jrCore', 'format_string', 'jrEmbed', 'jrEmbed_format_string_embed_tags', $_tmp);

    // Make sure there is a default module tab to load
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrEmbed_verify_module_listener');

    return true;
}

//---------------------------------------------------------
// FUNCTIONS
//---------------------------------------------------------

/**
 * Get Active Embed modules
 * @return array
 */
function jrEmbed_get_active_modules()
{
    global $_mods;
    $_out = false;
    $_fl  = glob(APP_DIR . "/modules/*/templates/{jrEmbed,tab_ajax}*", GLOB_BRACE);
    if ($_fl && is_array($_fl)) {
        $_out = array();
        foreach ($_fl as $file) {
            if (strpos($file, '-release')) {
                continue;
            }
            $file = str_replace(APP_DIR . '/modules/', '', $file);
            list($mod,) = explode('/', $file);
            if (jrCore_module_is_active($mod)) {
                $_out[$mod] = $_mods[$mod]['module_name'];
            }
        }
    }
    return $_out;
}

//---------------------------------------------------------
// SMARTY FUNCTIONS
//---------------------------------------------------------

/**
 * Registered core string formatter - Embed Tags
 * @param string $string String to format
 * @param int $quota_id Quota ID for Profile ID
 * @return string
 */
function jrEmbed_format_string_embed_tags($string, $quota_id = 0)
{
    // find any [jrEmbed mode="jrAudio" id="30"] and ask the module to replace it
    if (strpos(' ' . $string, '[jrEmbed')) {
        $pattern = '#\[jrEmbed(([\s|=].*?)*?)\]#i';
        $string  = preg_replace_callback($pattern, 'jrEmbed_replace_tag', $string);
    }
    return $string;
}

/**
 * allow modules to replace tags [jrEmbed mode="jrAudio" id="30"] with the actual HTML
 * @param string $html
 * @return string
 */
function smarty_modifier_jrEmbed_embed($html)
{
    return jrEmbed_format_string_embed_tags($html);
}

/**
 * Get params for a jrEmbed call in [jrEmbed] format
 * @param $matches array
 * @return string
 */
function jrEmbed_replace_tag($matches)
{
    global $_mods;

    $params = jrEmbed_get_param_array_from_string($matches[1]);
    $_data  = array();
    if (isset($params['module']) && isset($_mods["{$params['module']}"])) {

        // See if this module provides an embed function
        $_data['module'] = $params['module'];
        $_data           = jrCore_trigger_event('jrEmbed', 'embed_html', $_data, $params, $params['module']);
        if (!isset($_data['html'])) {
            $_data['html'] = jrEmbed_embed_default_html($params['module'], $params);
        }

        // Replace newlines with chars so we can fix it up on display
        if (isset($_data['html'])) {
            $_rep = array(
                "\n" => '',
                "\r" => '',
                "\t" => ''
            );
            return trim(preg_replace('!\s+!', ' ', str_replace(array_keys($_rep), $_rep, $_data['html'])));
        }
    }
    // Fall through - no module
    return '';
}

/**
 * Build parameter array from a smarty function call parameter string
 * @param $string string parameter string
 * @return array
 */
function jrEmbed_get_param_array_from_string($string)
{
    // [jrEmbed module="jrEmbed" mode="place" location="Corner Brook, NL, Canada" maptype="roadmap"]
    $string = trim($string);

    // Remove multiple spaces/tabs/newlines
    $string = preg_replace('!\s+!', ' ', $string);

    $params = array();
    foreach (explode(' ', $string) as $part) {
        if (strpos($part, '=')) {
            list($k, $v) = explode('=', $part, 2);
            $params[$k] = str_replace(array('"', "'", '\\'), '', $v);
        }
        elseif (isset($k)) {
            $params[$k] .= ' ' . str_replace(array('"', "'", '\\'), '', $part);
        }
    }
    return $params;
}

/**
 * Default embed function for DS enabled modules
 * @param $module string Module to get embed items for
 * @param $_params
 * @return bool|string
 */
function jrEmbed_embed_default_html($module, $_params)
{
    if (!jrCore_module_is_active($module) || !jrCore_db_get_prefix($module) || !is_file(APP_DIR . "/modules/{$module}/templates/item_embed.tpl")) {
        return false;
    }
    if (isset($_params['id']) && jrCore_checktype($_params['id'], 'number_nz')) {
        $_rt = array('item' => jrCore_db_get_item($module, $_params['id']));
    }
    elseif (isset($_params['profile_id']) && jrCore_checktype($_params['profile_id'], 'number_nz')) {
        $_rt = array(
            'search' => array(
                "_profile_id = {$_params['profile_id']}"
            ),
            'limit'  => 100
        );
        $_rt = jrCore_db_search_items($module, $_rt);
    }
    else {
        $_rt = array(
            'jrembed_html_function_is_active' => 1,
            '_params'                         => $_params
        );
        foreach ($_params as $k => $v) {
            // Search
            if (strpos($k, 'search') === 0 && strlen($v) > 0) {
                if (!isset($_rt['search'])) {
                    $_rt['search'] = array();
                }
                $_rt['search'][] = $v;
            }
            // Order by
            elseif (strpos($k, 'order_by') === 0) {
                if (!isset($_rt['order_by'])) {
                    $_rt['order_by'] = array();
                }
                list($fld, $dir) = explode(' ', $v);
                $fld                   = trim($fld);
                $_rt['order_by'][$fld] = trim($dir);
            }
            // Group By
            elseif ($k == 'group_by') {
                $_rt['group_by'] = trim($v);
            }
            // Limit
            elseif ($k == 'limit') {
                $_rt['limit'] = (int) $v;
            }
            if (isset($_rt['search'])) {
                $_rt = jrCore_db_search_items($module, $_rt);
            }
        }
    }
    if ($_rt && is_array($_rt)) {
        return jrCore_parse_template('item_embed.tpl', $_rt, $module);
    }
    return false;
}

//----------------------
// EVENT LISTENERS
//----------------------

/**
 * Make sure there is a default module tab set.
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrEmbed_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    if (!isset($_conf['jrEmbed_default_tab']) || strlen($_conf['jrEmbed_default_tab']) === 0) {
        $_md = jrEmbed_get_active_modules();
        if ($_md && is_array($_md)) {
            $_ky = array_flip($_md);
            $_ky = array_values($_ky);
            $def = $_ky[0];
            jrCore_set_setting_value('jrEmbed', 'default_tab', $def);
        }
    }
    return $_data;
}
