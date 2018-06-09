<?php
/**
 * Jamroom System Core module
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
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// Set start time flag for process
$now = explode(' ', microtime());
$now = $now[1] + $now[0];
$GLOBALS['__JR_FLAGS']['jrcore_process_start_time'] = $now;

// Define our base dir - DO NOT USE __DIR__ here
define('APP_DIR', dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME']))));

// prep our PHP environment
ini_set('always_populate_raw_post_data', -1);
ini_set('session.auto_start', 0);
ini_set('session.use_trans_sid', 0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', APP_DIR . '/data/logs/error_log');
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));

// Disable bad ideas
// deprecated @ 5.3.0, not in 5.4.x so @ here
@ini_set('magic_quotes_runtime', 0);
if (@ini_get('register_globals')) {
    foreach ($_REQUEST as $k => $v) {
        unset($$k);
    }
    if (isset($_FILES) && is_array($_FILES)) {
        foreach ($_FILES as $k => $v) {
            unset($$k);
        }
    }
}

// Bring in the core
require APP_DIR . '/modules/jrCore/include.php';

// Cleanup $_REQUEST
// deprecated @ 5.3.0, not in 5.4.x so @ here
if (@ini_get('magic_quotes_gpc') == 1) {
    $_REQUEST = jrCore_stripslashes($_REQUEST);
    $_COOKIE  = jrCore_stripslashes($_COOKIE);
}

$out   = false;
$_mods = array();
$_conf = array();
$_urls = array();

// Init Core - $_mods, $_conf and $_urls will be set after
// All module functions in include.php files will be active
jrCore_init();

// Parse out URL
$_post = jrCore_parse_url();

// Start User session
$_user = jrUser_session_start();

//------------------------------------
// Maintenance Mode Check
//------------------------------------
if (jrCore_is_maintenance_mode($_conf, $_post)) {
    $out = jrCore_parse_template('maintenance.tpl', $_post, 'jrCore');
}

//------------------------------------
// System Index
//------------------------------------
elseif (!isset($_post['module']{0}) && !isset($_post['option']{0}) && !isset($_post['module_url']{0})) {

    // Check cache
    $out = jrCore_trigger_event('jrCore', 'index_template', $_post);
    if (!$out || is_array($out) || strlen($out) === 0) {
        $key = "{$_conf['jrCore_active_skin']}-index-" . json_encode($_post);
        if (!$out = jrCore_is_cached('jrCore', $key)) {
            $out = jrCore_parse_template('index.tpl', $_post);
            jrCore_add_to_cache('jrCore', $key, $out);
        }
    }
    unset($_SESSION['jruser_save_location']);
}

//------------------------------------
// Skin Template Override
//------------------------------------
elseif (isset($_post['module']{0}) && isset($_post['option']{0}) && is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/override_{$_post['module']}_{$_post['option']}.tpl")) {

    // This is a SKIN template call
    if (jrCore_module_is_active($_post['module'])) {
        $_st = array(
            'template_path' => APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/override_{$_post['module']}_{$_post['option']}.tpl",
            'template_name' => "override_{$_post['module']}_{$_post['option']}.tpl"
        );
        $out = jrCore_trigger_event('jrCore', 'skin_template', $_post, $_st);
        if (!$out || is_array($out) || strlen($out) === 0) {
            $out = jrCore_parse_template("override_{$_post['module']}_{$_post['option']}.tpl", $_post);
        }
    }
    else {
        jrCore_page_not_found();
    }
}

else {

    //------------------------------------
    // Module controller
    //------------------------------------
    if (isset($_post['module']{0}) && isset($_mods["{$_post['module']}"])) {

        // If we are NOT the admin user, and a request comes in
        // for an inactive module, show error
        if (!jrCore_module_is_active($_post['module']) && !jrUser_is_master()) {
            jrCore_page_not_found();
        }

        // Trigger our module view
        $out = jrCore_trigger_event('jrCore', 'module_view', $_post);
        if (!$out || is_array($out) || strlen($out) === 0) {
            $out = false;

            // Our order of precedence is:
            // - Template Override @ Skin/override_[module]_[option].tpl
            // - EXACT MATCH on view_[module]_[option]
            // - MAGIC VIEW on view_magic_[option]
            // - Template @ Skin/[module]_[option].tpl
            // - Default view view_[module]_default
            // - Not Found (404.tpl)

            // Check for module controlled View (exact match)
            if (isset($_post['option']{0}) && is_file(APP_DIR . "/modules/{$_post['module']}/index.php")) {

                $func = "view_{$_post['module']}_{$_post['option']}";
                if (!function_exists($func)) {
                    ob_start();
                    require_once APP_DIR . "/modules/{$_post['module']}/index.php";
                    ob_end_clean();
                }
                // If it exists, run it - otherwise fall through for other handlers
                if (function_exists($func)) {
                    $out = jrCore_run_module_view_function($func);
                }
            }

            // Check for registered Magic View function
            if (isset($_post['option']{0}) && !$out) {

                $_vw = jrCore_get_registered_module_features('jrCore', 'magic_view');
                if ($_vw && is_array($_vw)) {
                    foreach ($_vw as $m => $_e) {
                        if (isset($_e["{$_post['option']}"])) {
                            $func = $_e["{$_post['option']}"];
                            // Bring in magic view module's view functions
                            if (!function_exists($func)) {
                                ob_start();
                                require_once APP_DIR . "/modules/{$m}/index.php";
                                ob_end_clean();
                            }
                            if (function_exists($func)) {
                                jrCore_set_flag('jrcore_is_magic_view', 1);
                                $out = jrCore_run_module_view_function($func);
                            }
                            else {
                                // log error and show 404
                                jrCore_logger('CRI', "magic view function: {$func} registered in module: {$m} does not exist!");
                                jrCore_page_not_found();
                            }
                            break;
                        }
                    }
                }
            }

            // Module must be active
            if (jrCore_module_is_active($_post['module']) || (isset($_post['_1']) && $_post['_1'] == 'info')) {

                // Skin Template
                if (isset($_post['option']{0}) && !$out) {
                    // Check for Skin Template specific to this module/view
                    if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$_post['module']}_{$_post['option']}.tpl")) {
                        $out = jrCore_parse_template("{$_post['module']}_{$_post['option']}.tpl", $_post);
                        unset($_SESSION['jruser_save_location']);
                    }
                }

                // Module Default View
                if (!$out) {
                    $func = "view_{$_post['module']}_default";
                    if (!function_exists($func)) {
                        ob_start();
                        require_once APP_DIR . "/modules/{$_post['module']}/index.php";
                        ob_end_clean();
                    }
                    jrCore_page_title($_post['module_url']);
                    if (function_exists($func)) {
                        // default view function
                        $out = jrCore_run_module_view_function($func);
                    }
                    elseif (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$_post['module']}_index.tpl")) {
                        // default module index (note skin override in parse_template)
                        $out = jrCore_parse_template('index.tpl', $_post, $_post['module']);
                    }
                    elseif (is_file(APP_DIR . "/modules/{$_post['module']}/templates/index.tpl")) {
                        // default module index (note skin override in parse_template)
                        $out = jrCore_parse_template('index.tpl', $_post, $_post['module']);
                    }
                    else {
                        // page/module/option not found
                        jrCore_page_not_found();
                    }
                }
            }
            else {
                jrCore_page_not_found();
            }
        }
    }

    //------------------------------------
    // Template / Profile
    //------------------------------------
    if (!$out || strlen($out) === 0) {

        // Fall through after modules have had their chance means we are loading a skin or profile index.
        // Note that skin templates always take precedence.
        if (isset($_post['module_url']{0}) && is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$_post['module_url']}.tpl")) {
            // This is a SKIN template call
            $_st = array(
                'template_path' => APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$_post['module_url']}.tpl",
                'template_name' => "{$_post['module_url']}.tpl",
            );
            $out = jrCore_trigger_event('jrCore', 'skin_template', $_post, $_st);
            if (!$out || is_array($out) || strlen($out) === 0) {
                $key = "{$_conf['jrCore_active_skin']}-" . json_encode($_post);
                if (!$out = jrCore_is_cached('jrCore', $key)) {
                    $out = jrCore_parse_template("{$_post['module_url']}.tpl", $_post);
                    jrCore_add_to_cache('jrCore', $key, $out);
                }
            }
            unset($_SESSION['jruser_save_location']);
        }
        // Profile...
        else {
            $out = jrCore_trigger_event('jrCore', 'profile_template', $_post);
            if (!$out || is_array($out) || strlen($out) === 0) {
                // This is a profile call - load profile info and display
                $out = jrProfile_show_profile($_post, $_user, $_conf);
            }
        }
    }
}

// Send view_results trigger
$out = jrCore_trigger_event('jrCore', 'view_results', $out);

// Full page cache
if (!jrUser_is_logged_in() && isset($_conf['jrCore_full_page']) && $_conf['jrCore_full_page'] == 'on') {
    if (!isset($_post['module']) || !isset($_mods["{$_post['module']}"])) {
        jrCore_add_to_cache('jrCore', jrCore_get_full_page_cache_key(), $out, 0, 0, false, true);
    }
}

// Send output
jrCore_send_response_and_detach($out);

// NOTE: We have detached from client at this point
// Process Exit trigger
jrCore_trigger_event('jrCore', 'process_exit', $_post);

// NOTE: internal use only - do not attach "regular" event listeners to this event!
jrCore_trigger_event('jrCore', 'process_done', $_post);
?>
