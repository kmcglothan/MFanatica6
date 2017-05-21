<?php
/**
 * Jamroom 5 Parameter Injection module
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
 * jrListParams_meta
 */
function jrListParams_meta()
{
    $_tmp = array(
        'name'        => 'Parameter Injection',
        'url'         => 'clp',
        'version'     => '1.0.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Inject Custom List Parameters into specified module views',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2944/parameter-injection',
        'category'    => 'listing',
        'license'     => 'mpl',
        'priority'    => 252
    );
    return $_tmp;
}

/**
 * jrListParams_init
 */
function jrListParams_init()
{
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrListParams', 'browse', array('parameter browser', 'Browse, Create, Update, and Delete custom list parameters'));
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrListParams', 'browse', 'Parameter Browser');
    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrListParams', 'browse');

    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrListParams_db_search_params_listener');

    jrCore_register_module_feature('jrCore', 'javascript', 'jrListParams', 'jrListParams.js');
    return true;
}

//--------------------------
// EVENT LISTENERS
//--------------------------

/**
 * Add support for custom list parameters
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrListParams_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    // Get any custom rules for this module
    global $_post;
    if (isset($_post['module']) && jrCore_module_is_active($_post['module']) && $_post['module'] != 'jrListParams') {

        // Check for match
        $_sc = array(
            'search'        => array(
                "list_module = {$_post['module']}"
            ),
            'skip_triggers' => true,
            'limit'         => 1,
        );
        if (isset($_post['option']) && strlen($_post['option']) > 0) {
            $_sc['search'][] = "list_view = {$_post['option']}";
        }
        $_rt = jrCore_db_search_items('jrListParams', $_sc);
        if ($_rt && is_array($_rt) && isset($_rt['_items']) && strlen($_rt['_items'][0]['list_params']) > 0) {

            // Check for group
            if (isset($_rt['_items'][0]['list_group']{1}) && $_rt['_items'][0]['list_group'] != 'all') {
                $_gr = explode(',', $_rt['_items'][0]['list_group']);
                if ($_gr && is_array($_gr)) {
                    foreach ($_gr as $group) {
                        // TODO: switch to new permission function!
                        switch ($group) {
                            case 'master':
                                if (!jrUser_is_master()) {
                                    return $_data;
                                }
                                break;
                            case 'admin':
                                if (!jrUser_is_admin()) {
                                    return $_data;
                                }
                                break;
                            case 'power':
                                if (!jrUser_is_power_user()) {
                                    return $_data;
                                }
                                break;
                            case 'user':
                                if (!jrUser_is_logged_in()) {
                                    return $_data;
                                }
                            case 'visitor':
                                if (jrUser_is_logged_in()) {
                                    return $_data;
                                }
                            default:
                                // Check for Quota ID - must be logged in
                                if (!jrUser_is_logged_in()) {
                                    return $_data;
                                }
                                if (!isset($ok)) {
                                    $ok = 'no';
                                }
                                if (jrCore_checktype($group, 'number_nz') && isset($_user['profile_quota_id']) && $_user['profile_quota_id'] == $group) {
                                    $ok = true;
                                }
                                break;
                        }
                    }
                    if (isset($ok) && $ok == 'no') {
                        return $_data;
                    }
                }
            }
            // Add in custom search fields
            if (!isset($_data['search']) || !is_array($_data['search'])) {
                $_data['search'] = array();
            }
            foreach (explode("\n", $_rt['_items'][0]['list_params']) as $prm) {
                $_data['search'][] = trim($prm);
            }
        }
    }
    return $_data;
}

//--------------------------
// FUNCTIONS
//--------------------------

/**
 * Return a list of DS modules
 * @return array
 */
function jrListParams_get_modules()
{
    global $_mods;
    $_out = array();
    foreach ($_mods as $dir => $_inf) {
        if (((isset($_inf['module_prefix']) && strlen($_inf['module_prefix']) > 0) || $dir == 'jrCore') && is_file(APP_DIR ."/modules/{$dir}/index.php")) {
            if ($dir == 'jrListParams') {
                continue;
            }
            $_tmp = jrListParams_get_views($dir);
            if ($_tmp) {
                $_out[$dir] = $_inf['module_name'];
            }
        }
    }
    natcasesort($_out);
    $_out = array_merge(array('0' => ' -- select module'), $_out);
    return $_out;
}

/**
 * Get valid views for a specific module
 * @param $module
 * @return array|bool
 */
function jrListParams_get_views($module)
{
    if (!is_file(APP_DIR . "/modules/{$module}/index.php")) {
        return false;
    }
    $_tm = file(APP_DIR . "/modules/{$module}/index.php");
    $_vw = array();
    $str = false;
    if ($_tm && is_array($_tm)) {
        foreach ($_tm as $line) {
            if (strpos($line, 'function ') === 0) {
                $str = $line;
            }
            elseif ($str) {
                if (strpos($line, 'jrCore_db_search_items')) {
                    $func       = substr($str, 0, strpos($str, '('));
                    $func       = trim(str_replace(array("view_{$module}_", 'function '), '', $func));
                    $_vw[$func] = $func;
                    $str        = false;
                }
            }
        }

        // Check for magic views
        $_mv = jrCore_get_registered_module_features('jrCore', 'magic_view');
        if (is_array($_mv)) {
            foreach ($_mv as $m => $_e) {
                foreach ($_e as $v => $_d) {
                    if ($m == 'jrCore') {
                        switch ($v) {
                            case 'stream':
                            case 'download':
                            case 'browser':
                            case 'item_display_order':
                                break;
                            default:
                                continue 2;
                                break;
                        }
                    }
                    $_vw[$v] = "(magic view): {$v}";
                }
            }
        }
        if (count($_vw) > 0) {
            return $_vw;
        }
    }
    return false;
}

