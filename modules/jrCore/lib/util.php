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
 * @package Utilities
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//-----------------------------------------
// FEATURE registration
//-----------------------------------------

/**
 * Register a feature for a module
 * @param string $module The Module that provides the feature
 * @param string $feature The Feature name
 * @param string $r_module The module that wants to use the feature
 * @param string $r_feature The unique name/key for the setting
 * @param mixed $_options optional parameters for the feature
 * @return bool
 */
function jrCore_register_module_feature($module, $feature, $r_module, $r_feature, $_options = true)
{
    if (!isset($GLOBALS['__JR_FLAGS']['jrcore_register_module_feature'])) {
        $GLOBALS['__JR_FLAGS']['jrcore_register_module_feature'] = array(
            $module => array(
                $feature => array(
                    $r_module => array()
                )
            )
        );
    }
    elseif (!isset($GLOBALS['__JR_FLAGS']['jrcore_register_module_feature'][$module])) {
        $GLOBALS['__JR_FLAGS']['jrcore_register_module_feature'][$module] = array(
            $feature => array(
                $r_module => array()
            )
        );
    }
    elseif (!isset($GLOBALS['__JR_FLAGS']['jrcore_register_module_feature'][$module][$feature])) {
        $GLOBALS['__JR_FLAGS']['jrcore_register_module_feature'][$module][$feature] = array(
            $r_module => array()
        );
    }
    elseif (!isset($GLOBALS['__JR_FLAGS']['jrcore_register_module_feature'][$module][$feature][$r_module])) {
        $GLOBALS['__JR_FLAGS']['jrcore_register_module_feature'][$module][$feature][$r_module] = array();
    }
    // See if we have a registered module feature function
    if (isset($GLOBALS['__JR_FLAGS']['jrcore_register_module_feature_function'][$module][$feature])) {
        $GLOBALS['__JR_FLAGS']['jrcore_register_module_feature'][$module][$feature][$r_module][$r_feature] = $GLOBALS['__JR_FLAGS']['jrcore_register_module_feature_function'][$module][$feature]($r_module, $r_feature, $_options);
    }
    else {
        $GLOBALS['__JR_FLAGS']['jrcore_register_module_feature'][$module][$feature][$r_module][$r_feature] = $_options;
    }
    return true;
}

/**
 * Returns an array of modules registered for a given feature
 * @param string $module The Module that provides the feature
 * @param string $feature The unique Feature name from the providing module
 * @return mixed
 */
function jrCore_get_registered_module_features($module, $feature)
{
    $_tmp = jrCore_get_flag('jrcore_register_module_feature');
    return (isset($_tmp[$module]) && isset($_tmp[$module][$feature])) ? $_tmp[$module][$feature] : false;
}

/**
 * Run a function when a module calls the jrCore_register_module_feature function
 * @param string $module The Module that provides the feature
 * @param string $feature The unique Feature name from the providing module
 * @param string $function Function to execute when jrCore_register_module_feature is called for this feature
 * @return bool
 */
function jrCore_register_module_feature_function($module, $feature, $function)
{
    if (!function_exists($function)) {
        return false;
    }
    $_tmp = jrCore_get_flag('jrcore_register_module_feature_function');
    if (!$_tmp) {
        $_tmp = array();
    }
    if (!isset($_tmp[$module])) {
        $_tmp[$module] = array();
    }
    $_tmp[$module][$feature] = $function;
    jrCore_set_flag('jrcore_register_module_feature_function', $_tmp);
    return true;
}

//-----------------------------------------
// COOKIE functions
//-----------------------------------------

/**
 * Set a persistent cookie
 * @param string $name Cookie Name
 * @param mixed $content Content (max ~4k)
 * @param int $expires Days to expire (default 10)
 * @return bool
 */
function jrCore_set_cookie($name, $content, $expires = 10)
{
    if (!jrCore_checktype($name, 'core_string')) {
        return false;
    }
    $content = json_encode($content);
    $expires = (intval($expires) * 86400);
    if (setcookie($name, $content, (time() + $expires), '/')) {
        $_COOKIE[$name] = $content;
        return true;
    }
    return false;
}

/**
 * Get value for persistent cookie if it exists
 * @param string $name Name of cookie to retrieve
 * @return bool|mixed
 */
function jrCore_get_cookie($name)
{
    if (isset($_COOKIE[$name])) {
        if (!$_val = json_decode($_COOKIE[$name], true)) {
            return $_COOKIE[$name];
        }
        return $_val;
    }
    return false;
}

/**
 * Delete a persistent cookie
 * @param string $name Name of cookie to delete
 * @return bool
 */
function jrCore_delete_cookie($name)
{
    setcookie($name, '', (time() - (365 * 86400)), '/');
    if (isset($_COOKIE[$name])) {
        unset($_COOKIE[$name]);
    }
    return true;
}

/**
 * Return an "option" image HTML for pass/fail
 * @param $state string pass|fail
 * @param $title string alt|title of img tag
 * @return string
 */
function jrCore_get_option_image($state, $title = null)
{
    global $_conf;
    if (is_null($title)) {
        $title = $state;
    }
    $url = jrCore_get_module_url('jrImage');
    return '<img class="option_img_' . $state . '" src="' . $_conf['jrCore_base_url'] . '/' . $url . '/img/module/jrCore/option_' . $state . '.png?_v=' . time() . '" width="12" height="12" alt="' . $title . '" title="' . $title . '">';
}

/**
 * Creates a configurable "module jumper" for use in ACP views
 * @param $name string Select field name
 * @param $selected string Value to be pre-selected
 * @param $onchange string JS code to execute when changed
 * @param null $_modules array limit display to only these modules
 * @return string
 */
function jrCore_get_module_jumper($name, $selected, $onchange, $_modules = null)
{
    global $_mods;
    $html = '<select name="' . $name . '" class="form_select form_select_item_jumper" onchange="' . $onchange . "\">\n";
    $_tmp = array();
    foreach ($_mods as $mod_dir => $_inf) {
        if ($_modules == null || is_array($_modules) && (isset($_modules[$mod_dir]) || in_array($mod_dir, $_modules))) {
            $cat = (isset($_inf['module_category'])) ? $_inf['module_category'] : 'custom';
            if (!isset($_tmp[$cat])) {
                $_tmp[$cat] = array();
            }
            $_tmp[$cat][$mod_dir] = $_inf['module_name'];
        }
    }
    ksort($_tmp, SORT_STRING);
    foreach ($_tmp as $cat => $_mds) {
        $html .= '<optgroup label="' . $cat . '">';
        natcasesort($_mds);
        foreach ($_mds as $dir => $name) {
            $murl = jrCore_get_module_url($dir);
            if ($dir == $selected) {
                $html .= '<option value="' . $murl . '" selected> ' . $name . "</option>\n";
            }
            else {
                $html .= '<option value="' . $murl . '"> ' . $name . "</option>\n";
            }
        }
        $html .= '</optgroup>';
    }
    $html .= '</select>';
    return $html;
}

/**
 * Return the detected URL to the system installation
 * @return string Returns full install URL
 */
function jrCore_get_base_url()
{
    $protocol = jrCore_get_server_protocol();
    $protocol = $protocol . '://';
    if (isset($_SERVER['HTTP_HOST'])) {
        return $protocol . rtrim($_SERVER['HTTP_HOST'], '/');
    }
    return $protocol . rtrim($_SERVER['SERVER_NAME'], '/');
}

/**
 * Returns server request protocol (http or https)
 */
function jrCore_get_server_protocol()
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
}

/**
 * Encode a string to be sent over a URL
 * @param string $string to Encode
 * @return string
 */
function jrCore_url_encode_string($string)
{
    return rawurlencode(htmlentities(rawurlencode(urlencode(base64_encode($string))), ENT_QUOTES));
}

/**
 * Decode a string encoded with jrCore_url_encode_string
 * @param string $string to Decode
 * @return string
 */
function jrCore_url_decode_string($string)
{
    return base64_decode(urldecode(rawurldecode(html_entity_decode(rawurldecode($string), ENT_QUOTES))));
}

/**
 * Convert a string to all lowercase
 * @param string $str String to lowercase
 * @return string
 */
function jrCore_str_to_lower($str)
{
    return (function_exists('mb_strtolower')) ? mb_strtolower($str, 'UTF-8') : strtolower($str);
}

/**
 * Convert a string to all uppercase
 * @param string $str String to uppercase
 * @return string
 */
function jrCore_str_to_upper($str)
{
    return (function_exists('mb_strtoupper')) ? mb_strtoupper($str, 'UTF-8') : strtoupper($str);
}

/**
 * Get the current URL
 * @return string Returns current URL (including mapped domains)
 */
function jrCore_get_current_url()
{
    global $_conf;
    $hst = (!empty($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : $_conf['jrCore_base_url'];
    $dir = (!empty($_SERVER['PHP_SELF']) && strpos($_SERVER['PHP_SELF'], 'router.php') && strpos($_SERVER['PHP_SELF'], '/modules/') !== 0) ? substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], '/modules/')) : '';
    $uri = (isset($_REQUEST['_uri'])) ? trim($_REQUEST['_uri']) : '';
    if (isset($uri{1}) && strpos($uri, '/') !== 0) {
        $uri = "/{$uri}";
    }
    $pfx = 'http://';
    if (!empty($_SERVER['HTTPS'])) {
        $pfx = 'https://';
    }
    return jrCore_trigger_event('jrCore', 'get_current_url', $pfx . rtrim($hst, '/') . $dir . $uri);
}

/**
 * Returns HTTP_REFERER if the URL is from the local site
 * @return string Returns URL to forward to on success
 */
function jrCore_get_local_referrer()
{
    global $_conf;
    $ref = $_conf['jrCore_base_url'];
    if (isset($_SERVER['HTTP_REFERER']) && basename($_SERVER['SCRIPT_FILENAME']) == 'router.php') {
        if (strpos($_SERVER['HTTP_REFERER'], $_conf['jrCore_base_url']) === 0) {
            // this is a local request
            $ref = $_SERVER['HTTP_REFERER'];
        }
        else {
            // Is this a mapped domain?
            if ($_dm = jrCore_get_flag('jrCustomDomain_requested_domain')) {
                if (strpos($_SERVER['HTTP_REFERER'], $_dm['map_domain']) === 0) {
                    // This is a mapped domain - we're good
                    $ref = $_SERVER['HTTP_REFERER'];
                }
            }
        }
    }
    return jrCore_trigger_event('jrCore', 'get_local_referrer', $ref);
}

/**
 * returns a URL if the referring URL is from the local site
 * @return string
 */
function jrCore_is_local_referrer()
{
    if (!isset($_SESSION['jruser_save_location'])) {
        $_SESSION['jruser_save_location'] = jrCore_get_local_referrer();
    }
    return $_SESSION['jruser_save_location'];
}

/**
 * Return TRUE if given URL is either the master URL or a valid URL as defined by a module
 * @param string $url
 * @return bool
 */
function jrCore_is_local_url($url)
{
    global $_conf;
    if (strpos($url, $_conf['jrCore_base_url']) === 0) {
        return true;
    }
    // If we don't match, see if it is an SSL/non-SSL issue
    $_rep = array('https://', 'http://');
    $tmp1 = str_replace($_rep, '', $url);
    $tmp2 = str_replace($_rep, '', $_conf['jrCore_base_url']);
    if (strpos($tmp1, $tmp2) === 0) {
        return true;
    }
    $_dat = array(
        'url'           => $url,
        'url_no_scheme' => $tmp1,
        'is_valid'      => 0
    );
    $_dat = jrCore_trigger_event('jrCore', 'is_local_url', $_dat);
    if ($_dat && is_array($_dat) && isset($_dat['is_valid']) && $_dat['is_valid'] == 1) {
        return true;
    }
    return false;
}

/**
 * Strip parameters from a URL
 * @param string $url URL to strip parameters from
 * @param array $_strip Array of parameter keys to strip
 * @return string
 */
function jrCore_strip_url_params($url, $_strip)
{
    if (is_array($_strip)) {
        foreach ($_strip as $strip) {
            if (stripos(' ' . $url, $strip)) {
                $url = preg_replace("%[?/]{$strip}=[^/]+%i", '', $url);
            }
        }
        return rtrim($url, '/');
    }
    return $url;
}

/**
 * Return TRUE if running in developer mode
 * @return bool
 */
function jrCore_is_developer_mode()
{
    global $_conf;
    return (isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on' && jrCore_module_is_active('jrDeveloper')) ? true : false;
}

/**
 * Return true if client has detached from process
 * @return mixed
 */
function jrCore_client_is_detached()
{
    return jrCore_get_flag('jrcore_client_is_detached');
}

/**
 * Set a new temp global flag
 * @param string $flag Unique flag string to set value for
 * @param mixed $value Value to store
 * @return bool
 */
function jrCore_set_flag($flag, $value)
{
    $GLOBALS['__JR_FLAGS'][$flag] = $value;
    return true;
}

/**
 * Retrieve a previously set temp global flag
 * @param mixed $flag String or Array to save to flag
 * @return mixed
 */
function jrCore_get_flag($flag)
{
    return (isset($GLOBALS['__JR_FLAGS'][$flag])) ? $GLOBALS['__JR_FLAGS'][$flag] : false;
}

/**
 * delete a previously set temp global flag
 * @param mixed $flag String or Array to delete
 * @return bool
 */
function jrCore_delete_flag($flag)
{
    if (isset($GLOBALS['__JR_FLAGS'][$flag])) {
        unset($GLOBALS['__JR_FLAGS'][$flag]);
        return true;
    }
    return false;
}

/**
 * Get value for an Advanced Setting (if set)
 * @param $module string Module setting advanced config item
 * @param $key string Unique Advanced Key Name
 * @param $default mixed Default value for key not set
 * @return mixed
 */
function jrCore_get_advanced_setting($module, $key, $default)
{
    global $_conf;
    return (isset($_conf["{$module}_{$key}"])) ? $_conf["{$module}_{$key}"] : $default;
}

/**
 * Parse REQUEST_URI into it's components
 * @param $uri string URI to parse - if empty uses $_SERVER['REQUEST_URI']
 * @param $cache bool set to FALSE to disable memory cache
 * @return array
 */
function jrCore_parse_url($uri = null, $cache = true)
{
    global $_urls;
    // Check for process cache
    if ($cache) {
        $tmp = jrCore_get_flag('jr_parse_url_complete');
        if ($tmp) {
            return $tmp;
        }
    }
    $_out = array();
    if (is_null($uri)) {
        $uri  = (isset($_REQUEST['_uri'])) ? $_REQUEST['_uri'] : '/';
        $curl = urldecode(str_replace(array('%2B', '%26'), array('+', '___AMP'), $_SERVER['REQUEST_URI']));
    }
    else {
        $curl = urldecode(str_replace(array('%2B', '%26'), array('+', '___AMP'), $uri));
    }

    // Get everything cleaned up and into $_post
    if ($uri != '/' && isset($uri{0})) {

        $_REQUEST['_uri'] = substr($curl, strpos($curl, '/' . $uri));
        // If we are NOT in an upload, make sure we strip out some URL params
        if (!strpos($_REQUEST['_uri'], '/upload_file/')) {
            if (strpos(' ' . $_REQUEST['_uri'], ";")) {
                $_REQUEST['_uri'] = substr($_REQUEST['_uri'], 0, strpos($_REQUEST['_uri'], ";"));
            }
            if (strpos(' ' . $_REQUEST['_uri'], '(')) {
                $_REQUEST['_uri'] = substr($_REQUEST['_uri'], 0, strpos($_REQUEST['_uri'], "("));
            }
        }

        // Break up our URL
        $_tmp = explode('/', str_replace(array('?', '&', '//', '///', '////', '/////'), '/', trim(urldecode($_REQUEST['_uri']), '/')));
        $ucnt = count($_tmp);

        if ($_tmp && is_array($_tmp)) {
            $idx = 0;
            // Page
            if (isset($_tmp[0]) && !strpos($_tmp[0], '=') && (!isset($_tmp[1]) || strpos($_tmp[1], '='))) {
                $_out['module_url'] = rawurlencode($_tmp[0]);
                $_out['module']     = (isset($_urls["{$_out['module_url']}"])) ? $_urls["{$_out['module_url']}"] : '';
                $idx                = 1;
            }
            // Module/View
            elseif (isset($_tmp[1]) && !strpos($_tmp[1], '=')) {
                $_out['module_url'] = rawurlencode($_tmp[0]);
                $_out['module']     = (isset($_urls["{$_out['module_url']}"])) ? $_urls["{$_out['module_url']}"] : '';
                $_out['option']     = rawurlencode($_tmp[1]);
                $idx                = 2;
            }
            // Handle any additional parameters
            if (isset($_tmp[$idx]) && strlen($_tmp[$idx]) > 0) {
                $vc = 1;
                for ($i = $idx; $i <= $ucnt; $i++) {
                    if (isset($_tmp[$i]{0})) {
                        if (strpos($_tmp[$i], '=')) {
                            list($key, $val) = explode('=', $_tmp[$i]);
                            if ($key == 'module' || $key == 'option') {
                                continue;
                            }
                            // Check for URL encoded array []'s
                            if (strpos($key, '%5B%5D')) {
                                $key          = substr($key, 0, strpos($key, '%5B%5D'));
                                $_out[$key][] = str_replace('___AMP', '&', trim($val));
                            }
                            elseif (strpos($key, '[]')) {
                                $key          = substr($key, 0, strpos($key, '[]'));
                                $_out[$key][] = str_replace('___AMP', '&', trim($val));
                            }
                            else {
                                $_out[$key] = str_replace('___AMP', '&', trim($val));
                            }
                        }
                        else {
                            // these are our "bare" parameters
                            $_out["_{$vc}"] = str_replace('___AMP', '&', trim($_tmp[$i]));
                            $vc++;
                        }
                    }
                    else {
                        break;
                    }
                }
            }
        }
    }
    else {
        $_REQUEST['_uri'] = '/';
    }
    // Lastly, check for an AJAX request
    $_SERVER['jr_is_ajax_request'] = 0;
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '__ajax')) {
        $_SERVER['jr_is_ajax_request'] = 1;
    }
    if (is_array($_out)) {
        if (!isset($_REQUEST) || !is_array($_REQUEST)) {
            $_REQUEST = array();
        }
        $_REQUEST = $_REQUEST + $_out;
    }
    // let other modules checkout $_post...
    $_REQUEST = jrCore_trigger_event('jrCore', 'parse_url', $_REQUEST);
    jrCore_set_flag('jr_parse_url_complete', $_REQUEST);
    return $_REQUEST;
}

/**
 * Redirect a browser to a URL
 * @param string $url URL to forward to
 * @param bool $process_exit set to FALSE to skip 'process_exit' event
 * @return null Function exits on completion
 */
function jrCore_location($url, $process_exit = true)
{
    global $_conf;
    if ($url == 'referrer') {
        $url = jrCore_get_local_referrer();
    }
    if (jrUser_is_logged_in() && isset($_conf['jrUser_force_ssl']) && $_conf['jrUser_force_ssl'] == 'on' && strpos($url, 'https:') !== 0) {
        // We have a non-SSL URL - if we are forcing logged in users to SSL, check host
        $hst = parse_url($url, PHP_URL_HOST);
        if (strpos($_conf['jrCore_base_url'], "//{$hst}/")) {
            // We are a local URL
            $url = str_replace('http://', 'https://', $url);
        }
    }
    if (isset($_SESSION)) {
        session_write_close();
    }
    $_tmp = jrCore_get_flag('jrcore_set_custom_header');
    if ($_tmp && is_array($_tmp)) {
        foreach ($_tmp as $header) {
            if (strpos($header, 'Location') !== 0) {
                header($header);
            }
        }
    }

    if (jrCore_is_ajax_request()) {
        // AJAX redirect
        $_out = json_encode(array('redirect' => $url));
    }
    else {
        header('Location: ' . trim($url));
        $_out = $url;
    }

    jrCore_process_exit_delete_profile_cache();
    jrCore_send_response_and_detach($_out);
    if ($process_exit) {
        jrCore_trigger_event('jrCore', 'process_exit', $_REQUEST);
        jrCore_trigger_event('jrCore', 'process_done', $_REQUEST);
    }
    jrCore_db_close();
    exit;
}

/**
 * Log an entry to the Activity Log
 * @param string $pri Priority - one of INF, MIN, MAJ, CRI
 * @param string $txt Text string to log
 * @param mixed $debug Additional debug information associated with the log entry
 * @param bool $include_user Include logging User Name in text
 * @param string $ip_address use IP Address instead of detected IP address
 * @return bool
 */
function jrCore_logger($pri, $txt, $debug = null, $include_user = true, $ip_address = null)
{
    if (jrCore_get_flag('jrCore_suppress_activity_log')) {
        // Activity Logging is suppressed in this process
        return true;
    }
    global $_user;
    $pri = strtolower($pri);
    $usr = '';
    if ($include_user && strpos($txt, '[') !== 0) {
        if ($include_user === true) {
            $tmp = jrCore_get_flag('jrcore_logger_system_user_active'); // Log as SYSTEM user in all queue workers
            if ($tmp) {
                $usr = '[system] ';
                $uip = (isset($_SERVER['SERVER_ADDR'])) ? $_SERVER['SERVER_ADDR'] : jrCore_get_ip();
            }
            else {
                $usr = (isset($_user['user_name']{0})) ? "[{$_user['user_name']}] " : ((isset($_user['user_email'])) ? "[{$_user['user_email']}] " : '');
                $uip = jrCore_get_ip();
            }
        }
        else {
            $usr = "[{$include_user}]";
            $uip = jrCore_get_ip();
        }
    }
    else {
        $uip = jrCore_get_ip();
    }
    if (!is_null($ip_address)) {
        $uip = $ip_address;
    }

    // trigger
    if (!jrCore_get_flag('jrcore_logger_trigger_active')) {

        // We must set this flag here, since if a LISTENER calls a jrCore_logger
        // call, we don't want to fall down a trigger/event spiral
        jrCore_set_flag('jrcore_logger_trigger_active', 1);
        $_data = array(
            'priority' => $pri,
            'message'  => $usr . $txt,
            'user_ip'  => $uip,
            'debug'    => $debug
        );
        $_data = jrCore_trigger_event('jrCore', 'log_message', $_data);
        jrCore_delete_flag('jrcore_logger_trigger_active');
        if (!is_array($_data)) {
            // We have been handled by a listener - return
            return true;
        }
    }

    $tbl = jrCore_db_table_name('jrCore', 'log');
    if (is_null($debug)) {
        $req = "INSERT INTO {$tbl} (log_created, log_priority, log_ip, log_text) VALUES (UNIX_TIMESTAMP(), '{$pri}', '" . jrCore_db_escape($uip) . "', '" . jrCore_db_escape($usr . $txt) . "')";
        jrCore_db_query($req, null, false, null, false, null, false);
    }
    else {
        if (is_array($debug) || is_object($debug)) {
            $debug = print_r($debug, true);
        }
        if (!mb_check_encoding($debug, 'UTF-8')) {
            // We have non UTF8 chars - remove
            $debug = mb_convert_encoding($debug, 'UTF-8', 'UTF-8');
        }
        $sav = jrCore_db_escape(jrCore_strip_emoji($debug, false));
        $mem = memory_get_usage(true);
        $uri = (isset($_REQUEST['_uri'])) ? jrCore_db_escape(substr($_REQUEST['_uri'], 0, 255)) : '/';

        $tb2 = jrCore_db_table_name('jrCore', 'log_debug');
        $_rq = array(
            "INSERT INTO {$tbl} (log_created, log_priority, log_ip, log_text) VALUES (UNIX_TIMESTAMP(), '{$pri}', '" . jrCore_db_escape($uip) . "', '" . jrCore_db_escape($usr . $txt) . "')",
            "INSERT INTO {$tb2} (log_log_id, log_url, log_memory, log_data) VALUES (LAST_INSERT_ID(), '{$uri}', '{$mem}', '{$sav}') ON DUPLICATE KEY UPDATE log_url = VALUES(log_url), log_memory = '{$mem}', log_data = VALUES(log_data)"
        );
        jrCore_db_multi_select($_rq, false, false);
    }
    return true;
}

/**
 * Purge the Activity log according to Purge Activity Logs global config
 * @return int
 */
function jrCore_purge_activity_logs()
{
    global $_conf;
    if (isset($_conf['jrCore_purge_log_days']) && jrCore_checktype($_conf['jrCore_purge_log_days'], 'number_nz')) {
        $dys = intval($_conf['jrCore_purge_log_days']);
        $tb1 = jrCore_db_table_name('jrCore', 'log');
        $tb2 = jrCore_db_table_name('jrCore', 'log_debug');
        $req = "DELETE {$tb1}, {$tb2} FROM {$tb1} LEFT JOIN {$tb2} ON ({$tb2}.log_log_id = {$tb1}.log_id) WHERE {$tb1}.log_created < (UNIX_TIMESTAMP() - ({$dys} * 86400))";
        return jrCore_db_query($req, 'COUNT', false, null, false);
    }
    return true;
}

/**
 * Replace emoji unicode characters with placeholders in a string
 * @param $string string
 * @param $replace bool set to TRUE to store emoji replacements
 * @return int
 */
function jrCore_strip_emoji($string, $replace = true)
{
    if (is_string($string)) {
        $pattern = '/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1FFFF}][\x{FE00}-\x{FEFF}]?/u';
        if (preg_match_all($pattern, $string, $_match)) {
            $_rp = array();
            foreach ($_match[0] as $e) {
                if (strlen($e) > 1) {
                    $_rp[$e] = $e;
                }
            }
            if (count($_rp) > 0) {
                if ($replace) {
                    $tbl = jrCore_db_table_name('jrCore', 'emoji');
                    $req = "SELECT * FROM {$tbl} WHERE emoji_value IN('" . implode("','", $_rp) . "')";
                    $_rt = jrCore_db_query($req, 'emoji_value', false, 'emoji_id', false, null, false);
                    foreach ($_rp as $k => $e) {
                        if (!$_rt || !isset($_rt[$k])) {
                            $req     = "INSERT INTO {$tbl} (emoji_value) VALUES ('{$e}')";
                            $eid     = jrCore_db_query($req, 'INSERT_ID', false, null, false, null, false);
                            $_rt[$k] = $eid;
                        }
                        else {
                            $eid = $_rt[$k];
                        }
                        if ($eid && $eid > 0) {
                            // Replace with placeholder in our string
                            $string = str_replace($e, "!!emoji!!{$eid}!!emoji!!", $string);
                        }
                    }
                }
                else {
                    foreach ($_rp as $k => $e) {
                        $string = str_replace($e, '', $string);
                    }
                }
            }
        }
        jrCore_delete_flag('jrCore_strip_emoji');
        return jrCore_strip_non_utf8($string);
    }
    return $string;
}

/**
 * Replace emoji placeholders in a string with actual emoji
 * @param $string string
 * @return mixed
 */
function jrCore_replace_emoji($string)
{
    if (is_string($string) && strpos(' ' . $string, '!!emoji!!')) {
        if (preg_match_all('/!!emoji!!([0-9]*)!!emoji!!/i', $string, $_match)) {
            $_id = array();
            foreach ($_match[0] as $e) {
                $_id[] = (int) str_ireplace('!!emoji!!', '', $e);
            }
            if (count($_id) > 0) {
                $tbl = jrCore_db_table_name('jrCore', 'emoji');
                $req = "SELECT CONCAT('!!emoji!!', emoji_id, '!!emoji!!') AS emoji_id, emoji_value FROM {$tbl} WHERE emoji_id IN(" . implode(',', $_id) . ")";
                $_rt = jrCore_db_query($req, 'emoji_id', false, 'emoji_value', false, null, false);
                if ($_rt && is_array($_rt)) {
                    $string = str_ireplace(array_keys($_rt), $_rt, $string);
                }
            }
        }
    }
    return $string;
}

/**
 * Return TRUE if a request is an XHR request
 * @return bool
 */
function jrCore_is_ajax_request()
{
    //[HTTP_X_REQUESTED_WITH] => XMLHttpRequest
    return ((isset($_SERVER['jr_is_ajax_request']) && $_SERVER['jr_is_ajax_request'] === 1) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) ? true : false;
}

/**
 * Find if a request is for a VIEW (template, view, etc) and NOT an Ajax, IMG or CSS/JS request
 * @note use of $_REQUEST since $_post may not be built at time of function call
 * @return bool
 */
function jrCore_is_view_request()
{
    if ((isset($_REQUEST['_uri']) && (strpos($_REQUEST['_uri'], '_v=') || strpos($_REQUEST['_uri'], '/image/') || strpos($_REQUEST['_uri'], '/img/') || strpos($_REQUEST['_uri'], '/icon_css/')))) {
        return false;
    }
    return true;
}

/**
 * Return TRUE if current request is an image request
 * @note use of $_REQUEST since $_post may not be built at time of function call
 * @return bool
 */
function jrCore_is_image_request()
{
    if (isset($_REQUEST['_uri']) && (strpos($_REQUEST['_uri'], '/image/') || strpos($_REQUEST['_uri'], '/img/'))) {
        return true;
    }
    return false;
}

/**
 * Convert URLs in a string into click URLs
 * @param string $string Input string to parse
 * @return string
 */
function jrCore_string_to_url($string)
{
    // replace links with click able version
    if (strpos(' ' . $string, 'http')) {
        $string = preg_replace('`([^"\'])(http[s]?://[^.]+\.[@\p{L}\p{N}-,:\./\_\?\%\#\&\=\;\~\!\+\]\[]+)`iu', '\1<a href="\2" target="_blank" rel="nofollow">\2</a>', ' ' . $string);
        return substr($string, 1);
    }
    return $string;
}

/**
 * Download a file from a remote site by URL
 * @param string $remote_url Remote File URL
 * @param string $local_file Local file to save data to
 * @param int $max_download_time How many seconds to allow for file download before failing
 * @param int $port Remote Port to create socket connection to.
 * @param string $username HTTP Basic Authentication User Name
 * @param string $password HTTP Basic Authentication Password
 * @param string $agent Browser Agent to appear as
 * @param bool $log_error Set to FALSE to prevent Activity Log error if an error is encountered
 * @param bool $ssl_verify set to TRUE to force SSL verification
 * @return bool Returns true if file is downloaded, false on error
 */
function jrCore_download_file($remote_url, $local_file, $max_download_time = 120, $port = 80, $username = null, $password = null, $agent = null, $log_error = true, $ssl_verify = false)
{
    if (!jrCore_checktype($remote_url, 'url')) {
        jrCore_logger('CRI', "jrCore_download_file: invalid URL received: {$remote_url}");
    }
    set_time_limit(0);
    $_temp = jrCore_module_meta_data('jrCore');
    if (is_null($agent)) {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $agent = $_SERVER['HTTP_USER_AGENT'];
        }
        else {
            $agent = 'Jamroom v' . $_temp['version'];
        }
    }
    if ($port === 80 && strpos($remote_url, 'https:') === 0) {
        $port = 443;
    }
    if ($local = fopen($local_file, 'wb')) {
        $_opts = array(
            CURLOPT_USERAGENT      => $agent,
            CURLOPT_URL            => $remote_url, // File we are downloading
            CURLOPT_PORT           => (int) $port,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => intval($max_download_time),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FILE           => $local,
            CURLOPT_HEADERFUNCTION => 'jrCore_save_load_url_response_headers'
        );
        if (!$ssl_verify) {
            $_opts[CURLOPT_SSL_VERIFYHOST] = false;
            $_opts[CURLOPT_SSL_VERIFYPEER] = false;
        }
        // Check for HTTP Basic Authentication
        if (!is_null($username) && !is_null($password)) {
            $_opts[CURLOPT_USERPWD] = $username . ':' . $password;
        }
        $ch = curl_init();
        if (curl_setopt_array($ch, $_opts)) {
            curl_exec($ch);
            $err = curl_errno($ch);
            fclose($local);
            if (!isset($err) || $err === 0) {
                curl_close($ch);
                return true;
            }
            $errmsg = curl_error($ch);
            curl_close($ch);
            if ($log_error) {
                jrCore_logger('CRI', "jrCore_download_file: {$remote_url} returned error #{$err} ({$errmsg})");
            }
            return false;
        }
        fclose($local);
        curl_close($ch);
    }
    return false;
}

/**
 * Store response headers from a jrCore_load_url() call
 * @param $curl resource
 * @param $line string
 * @return bool
 */
function jrCore_save_load_url_response_headers($curl, $line)
{
    // Used to hold response headers
    $_tm = jrCore_get_flag('jrcore_load_url_response_headers');
    if (!$_tm) {
        $_tm = array();
    }
    if (strlen(trim($line)) > 0) {
        $_tm[] = trim($line);
        jrCore_set_flag('jrcore_load_url_response_headers', $_tm);
    }
    return strlen($line);
}

/**
 * Get last response headers from a jrCore_load_url() call
 * @return bool|mixed
 */
function jrCore_get_load_url_response_headers()
{
    if ($_tm = jrCore_get_flag('jrcore_load_url_response_headers')) {
        return $_tm;
    }
    return false;
}

/**
 * Get contents of a remote URL
 * @param string $url Url to load
 * @param mixed $_vars URI variables for URL
 * @param string $method URL method (POST or GET)
 * @param int $port Remote Port to create socket connection to.
 * @param string $username HTTP Basic Authentication User Name
 * @param string $password HTTP Basic Authentication Password
 * @param bool $log_error Set to false to prevent error logging on failed URL load
 * @param int $max_transfer_time Time in seconds to allow for data transfer
 * @param string $agent Browser Agent to appear as
 * @param bool $uploads set to TRUE to allow @file_uploads
 * @param bool $ssl_verify set to TRUE to verify SSL certificate
 * @return string Returns value of loaded URL, or false on failure
 */
function jrCore_load_url($url, $_vars = null, $method = 'GET', $port = 80, $username = null, $password = null, $log_error = true, $max_transfer_time = 30, $agent = null, $uploads = false, $ssl_verify = false)
{
    if (!jrCore_checktype($url, 'url')) {
        $_rs = array(
            '_vars'    => $_vars,
            'referrer' => jrCore_get_local_referrer()
        );
        jrCore_logger('CRI', "jrCore_load_url: invalid URL received: {$url}", $_rs);
        return false;
    }
    jrCore_delete_flag('jrcore_load_url_response_headers');
    // Our user agent
    if (is_null($agent)) {
        $_temp = jrCore_module_meta_data('jrCore');
        $agent = 'Jamroom v' . $_temp['version'];
    }
    if ($port === 80 && strpos($url, 'https:') === 0) {
        $port = 443;
    }
    $_opts = array(
        CURLOPT_POST           => false,
        CURLOPT_HEADER         => false,
        CURLOPT_USERAGENT      => $agent,
        CURLOPT_URL            => $url,
        CURLOPT_PORT           => (int) $port,
        CURLOPT_FRESH_CONNECT  => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FORBID_REUSE   => true,
        CURLOPT_TIMEOUT        => intval($max_transfer_time),
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_VERBOSE        => false,
        CURLOPT_FAILONERROR    => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 3,
        CURLOPT_HEADERFUNCTION => 'jrCore_save_load_url_response_headers'
    );
    if (!$ssl_verify) {
        $_opts[CURLOPT_SSL_VERIFYHOST] = false;
        $_opts[CURLOPT_SSL_VERIFYPEER] = false;
    }

    // Curl File Handling has changed in PHP 5.5.0
    if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
        if (!$uploads) {
            $_opts[CURLOPT_SAFE_UPLOAD] = true;
        }
        else {
            // Are we uploading any files?
            if (function_exists('curl_file_create')) {
                foreach ($_vars as $k => $v) {
                    if (strpos($v, '@') === 0) {
                        $file = substr($v, 1);
                        if (file_exists($file) && strpos(realpath($file), APP_DIR) === 0) {
                            $mime      = jrCore_mime_type($file);
                            $_vars[$k] = curl_file_create($file, $mime, basename($file));
                        }
                    }
                }
            }
        }
    }

    // Check for HTTP Basic Authentication
    if (!is_null($username) && !is_null($password)) {
        $_opts[CURLOPT_USERPWD] = $username . ':' . $password;
    }
    switch (strtoupper($method)) {
        case 'POST':
            $_opts[CURLOPT_POST] = true;
            if (!is_null($_vars) && is_array($_vars) && count($_vars) > 0) {
                $_opts[CURLOPT_POSTFIELDS] = $_vars;
            }
            elseif (!is_null($_vars) && strlen($_vars) > 0) {
                $_opts[CURLOPT_POSTFIELDS] = trim($_vars);
            }
            break;
        case 'GET':
            $_opts[CURLOPT_HTTPGET] = true;
            if (!is_null($_vars) && is_array($_vars) && count($_vars) > 0) {
                if (strpos($url, '?')) {
                    $_opts[CURLOPT_URL] = $url . '&' . http_build_query($_vars);
                }
                else {
                    $_opts[CURLOPT_URL] = $url . '?' . http_build_query($_vars);
                }
            }
            elseif (!is_null($_vars) && strlen($_vars) > 0) {
                if (strpos($url, '?')) {
                    $_opts[CURLOPT_URL] = $url . '&' . trim($_vars);
                }
                else {
                    $_opts[CURLOPT_URL] = $url . '?' . trim($_vars);
                }
            }
            break;
        case 'PUT':
            $_opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
            if (!is_null($_vars) && is_array($_vars) && count($_vars) > 0) {
                $_opts[CURLOPT_POSTFIELDS] = $_vars;
            }
            break;
        case 'DELETE':
            $_opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
            if (!is_null($_vars) && is_array($_vars) && count($_vars) > 0) {
                if (strpos($url, '?')) {
                    $_opts[CURLOPT_URL] = $url . '&' . http_build_query($_vars);
                }
                else {
                    $_opts[CURLOPT_URL] = $url . '?' . http_build_query($_vars);
                }
            }
            break;
    }
    $ch = curl_init();
    if (curl_setopt_array($ch, $_opts)) {
        $res = curl_exec($ch);
        $err = curl_errno($ch);
        if (!isset($err) || $err === 0) {
            curl_close($ch);
            return $res;
        }
        $errmsg = curl_error($ch);
        if ($log_error) {
            $rcd = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (!is_array($_vars)) {
                $_vars = array('_vars' => $_vars);
            }
            $_vars['url']        = $url;
            $_vars['error_code'] = $rcd;
            $_vars['error_text'] = $errmsg;
            if (strlen($url) > 128) {
                $url = substr($url, 0, 128) . '...';
            }
            jrCore_logger('CRI', "jrCore_load_url error: {$url}", $_vars);
        }
    }
    else {
        if ($log_error) {
            jrCore_logger('CRI', "jrCore_load_url: unable to load cURL options via curl_setopt_array");
        }
    }
    curl_close($ch);
    return false;
}

/**
 * Load multiple URLs in one call asynchronously
 * @param $_urls array URL info
 * @param $agent string Alternate User Agent
 * @param $sleep int Number of microseconds to sleep between each check of curl_multi_exec() to see if URLs have completed
 * @return mixed
 */
function jrCore_load_multiple_urls($_urls, $agent = null, $sleep = 10000)
{
    $_ch   = array();
    $_temp = jrCore_module_meta_data('jrCore');
    if (is_null($agent)) {
        $agent = 'Jamroom v' . $_temp['version'];
    }
    $crl = curl_multi_init();
    foreach ($_urls as $k => $_url) {
        if (!isset($_url['url']) || !jrCore_checktype($_url['url'], 'url')) {
            return false;
        }
        $transfer_timeout = (isset($_url['timeout']) && jrCore_checktype($_url['timeout'], 'number_nz')) ? intval($_url['timeout']) : 30;
        $connect_timeout  = (isset($_url['connect_timeout']) && jrCore_checktype($_url['connect_timeout'], 'number_nz')) ? intval($_url['connect_timeout']) : 10;
        if (isset($_url['port']) && jrCore_checktype($_url['port'], 'number_nz')) {
            $port = intval($_url['port']);
        }
        else {
            $port = (strpos($_url['url'], 'https:') === 0) ? 443 : 80;
        }
        $_opts = array(
            CURLOPT_POST           => false,
            CURLOPT_HEADER         => false,
            CURLOPT_USERAGENT      => $agent,
            CURLOPT_URL            => $_url['url'],
            CURLOPT_PORT           => $port,
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FORBID_REUSE   => true,
            CURLOPT_TIMEOUT        => $transfer_timeout,
            CURLOPT_CONNECTTIMEOUT => $connect_timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_VERBOSE        => false,
            CURLOPT_FAILONERROR    => false
        );
        if (!isset($_url['ssl_verify']) || $_url['ssl_verify'] == false) {
            $_opts[CURLOPT_SSL_VERIFYHOST] = false;
            $_opts[CURLOPT_SSL_VERIFYPEER] = false;
        }
        if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
            if (!isset($_url['uploads']) || $_urls['uploads'] === false) {
                $_opts[CURLOPT_SAFE_UPLOAD] = true;
            }
            else {
                // Are we uploading any files?
                if (function_exists('curl_file_create') && isset($_url['_data']) && is_array($_url['_data'])) {
                    foreach ($_url['_data'] as $fk => $fv) {
                        if (strpos($fv, '@') === 0) {
                            $file = substr($fv, 1);
                            if (file_exists($file)) {
                                $mime               = jrCore_mime_type($file);
                                $_url['_data'][$fk] = curl_file_create($file, $mime, basename($file));
                            }
                        }
                    }
                }
            }
        }
        // Check for HTTP Basic Authentication
        if (isset($_url['username']) && strlen($_url['username']) > 0 && isset($_url['password']) && strlen($_url['password']) > 0) {
            $_opts[CURLOPT_USERPWD] = $_url['username'] . ':' . $_url['password'];
        }
        if (isset($_url['method']) && $_url['method'] == 'POST') {
            $_opts[CURLOPT_POST] = true;
        }
        if (isset($_url['_data']) && is_array($_url['_data'])) {
            $_opts[CURLOPT_POSTFIELDS] = $_url['_data'];
        }
        elseif (isset($_url['_data']) && strlen($_url['_data']) > 0) {
            $_opts[CURLOPT_POSTFIELDS] = trim($_url['_data']);
        }
        $_ch[$k] = curl_init();
        curl_setopt_array($_ch[$k], $_opts);
        curl_multi_add_handle($crl, $_ch[$k]);
    }
    if (count($_ch) === 0) {
        return false;
    }

    $active = null;
    // Get content
    do {
        curl_multi_exec($crl, $active);
        usleep($sleep);
    } while ($active > 0);

    $res = array();
    foreach ($_ch as $k => $handle) {
        $res[$k] = curl_multi_getcontent($handle);
    }
    foreach ($_ch as $k => $handle) {
        curl_multi_remove_handle($crl, $handle);
    }
    curl_multi_close($crl);
    return $res;
}

/**
 * Load queued URLs for processing
 * @param array $_urls array of URLs to run
 * @param int $workers Number of simultaneous URLs to work
 * @param bool $ssl_verify set to TRUE to force SSL certificate validation
 * @return mixed
 */
function jrCore_load_queued_urls($_urls, $workers, $ssl_verify = false)
{
    $curl = curl_multi_init();
    $_opt = array(
        CURLOPT_POST           => false,
        CURLOPT_HEADER         => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 3,
        CURLOPT_VERBOSE        => false,
        CURLOPT_FAILONERROR    => false
    );
    if (!$ssl_verify) {
        $_opt[CURLOPT_SSL_VERIFYHOST] = false;
        $_opt[CURLOPT_SSL_VERIFYPEER] = false;
    }
    if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
        $_opt[CURLOPT_SAFE_UPLOAD] = true;
    }

    // start the first batch of requests
    for ($i = 0; $i < $workers; $i++) {
        if (isset($_urls[$i]) && jrCore_checktype($_urls[$i], 'url')) {
            $ch                = curl_init();
            $_opt[CURLOPT_URL] = $_urls[$i];
            curl_setopt_array($ch, $_opt);
            curl_multi_add_handle($curl, $ch);
        }
    }
    $_rs = array(
        'success' => array()
    );
    do {
        /** @noinspection PhpStatementHasEmptyBodyInspection */
        while (($exec = curl_multi_exec($curl, $running)) == CURLM_CALL_MULTI_PERFORM) ;
        if ($exec != CURLM_OK) {
            break;
        }
        // a request was just completed -- find out which one
        while ($done = curl_multi_info_read($curl)) {
            $_inf = curl_getinfo($done['handle']);
            $idx  = array_search($_inf['url'], $_urls);
            if ($_inf['http_code'] == 200) {

                $_rs['success'][$idx] = curl_multi_getcontent($done['handle']);
                // start a new request before removing the old one
                $i++;
                if (isset($_urls[$i])) {
                    $ch                = curl_init();
                    $_opt[CURLOPT_URL] = $_urls[$i];
                    curl_setopt_array($ch, $_opt);
                    curl_multi_add_handle($curl, $ch);
                }
                // remove the curl handle that just completed
                curl_multi_remove_handle($curl, $done['handle']);
            }
            else {
                // request failed
                if (!isset($_rs['failure'])) {
                    $_rs['failure'] = array();
                }
                $_rs['failure'][$idx] = curl_multi_getcontent($done['handle']);
            }
        }
    } while ($running);
    curl_multi_close($curl);
    return $_rs;
}

/**
 * Strip unsafe HTML tags from a string (recursive)
 * @param mixed $string String or Array to strip tags from
 * @param string $_allowed comma separated list of allowed HTML tags
 * @param bool $strip_embed set to TRUE to strip [jrEmbed] tags
 * @return mixed
 */
function jrCore_strip_html($string, $_allowed = null, $strip_embed = false)
{
    global $_conf;

    if (is_array($string)) {
        foreach ($string as $k => $v) {
            $string[$k] = jrCore_strip_html($v, $_allowed);
        }
    }
    else {

        if (strlen($string) === 0) {
            return $string;
        }

        if ($strip_embed && strpos(' ' . $string, '[jrEmbed')) {
            // We have embed codes in this string - strip
            $string = preg_replace('/\[jrEmbed[^\]]+\]/', '', $string);
        }

        if (!strpos(' ' . $string, '<')) {
            // No HTML in string
            return $string;
        }

        // Cleanup any malformed HTML
        $string = jrCore_clean_html($string);

        if (!is_null($_allowed)) {
            if (jrUser_is_master() && jrCore_get_flag('master_html_trusted')) {
                // It's the master and we've been requested to return trusted
                return $string;
            }
            elseif (jrUser_get_profile_home_key('quota_jrCore_allow_all_html') === 'on') {
                // This quota is fully trusted
                return $string;
            }
        }

        $_cs = array();
        // We have to do a quick check for icon_css (i.e. from an embedded item) - if we strip it
        // out then icon's may not show properly for any item that was relying on the CSS
        if (strpos($string, '/icon_css/')) {
            if (preg_match('%/icon_css/([0-9]*)%', $string, $_match)) {
                if ($_match && is_array($_match) && isset($_match[1])) {
                    $url = jrCore_get_module_url('jrCore');
                    foreach ($_match as $k => $v) {
                        if ($k > 0 && !isset($_cs[$v])) {
                            $_tm = array('source' => $_conf['jrCore_base_url'] . '/' . $url . '/icon_css/' . $v . '?_v=' . time());
                            jrCore_create_page_element('css_footer_href', $_tm);
                        }
                    }
                    unset($url, $_match);
                }
            }
        }

        // cleanup with HTML purifier
        require_once APP_DIR . '/modules/jrCore/contrib/htmlpurifier/HTMLPurifier.standalone.php';

        $allw       = '';
        $trgt       = false;
        $_xtr       = array();
        $trusted    = false;
        $safe_embed = false;
        if (!is_null($_allowed) && strlen($_allowed) > 0) {
            $_all = explode(',', $_allowed);
            $_att = array();
            $_tmp = array();
            if (is_array($_all)) {
                foreach ($_all as $k => $tag) {
                    $tag = trim($tag);
                    if (strlen($tag) > 0) {

                        // Get Tag Attributes
                        switch ($tag) {
                            // Setup some defaults for ease of use
                            case 'a':
                                $_att[] = 'a.name,a.href,a.target,a.rel,a.title,a.data-lightbox,a.title,a.style,a.id,a.class';
                                $trgt   = true;
                                break;
                            case 'img':
                                $_att[] = 'img.src,img.width,img.height,img.alt,img.style,img.class,img.title';
                                break;
                            case 'script':
                                $_att[]     = 'script.type';
                                $safe_embed = true;
                                break;
                            case 'iframe':
                                $_att[]     = 'iframe.src,iframe.width,iframe.height,iframe.name,iframe.align,iframe.frameborder,iframe.marginwidth,iframe.marginheight';
                                $safe_embed = true;
                                break;
                            case 'object':
                                $_att[]     = 'object.width,object.height,object.style,object.class';
                                $safe_embed = true;
                                break;
                            case 'param':
                                $_att[]     = 'param.name,param.value';
                                $safe_embed = true;
                                break;
                            case 'embed':
                                $_att[] = 'embed.src,embed.type,embed.allowscriptaccess,embed.allowfullscreen,embed.width,embed.height,embed.flashvars';
                                break;
                            case 'form':
                            case 'input':
                                $trusted = true;
                                break;
                            case 'table':
                                $_att[] = 'table.class,table.style,table.border,table.align,table.cellspacing,table.cellpadding';
                                $_xtr[] = 'tbody';
                                $_xtr[] = 'caption';
                                break;
                            case 'tr':
                                $_att[] = 'tr.class,tr.style,tr.align';
                                break;
                            case 'td':
                                $_att[] = 'td.class,td.style,td.align,td.valign,td.colspan,td.rowspan';
                                break;
                            case 'th':
                                $_att[] = 'th.class,th.style,th.align,th.valign,th.colspan,th.rowspan';
                                break;
                            case 'hr':
                                $_att[] = 'hr.id,hr.class,hr.style,hr.align,hr.color,hr.width,hr.noshade';
                                break;
                            case 'p':
                            case 'span':
                            case 'div':
                            case 'h1':
                            case 'h2':
                            case 'h3':
                            case 'h4':
                            case 'h5':
                            case 'h6':
                                $_att[] = "{$tag}.class,{$tag}.style,{$tag}.title,{$tag}.align";
                                break;
                            default:
                                // If the tag has a period in it - i.e. "iframe.src"
                                // we need to add tag to tags, and attribute to attributes
                                if (strpos($tag, '.')) {
                                    list($t,) = explode('.', $tag);
                                    $_att[] = $tag;
                                    $tag    = $t;
                                }
                                break;
                        }
                        $_all[$k]   = $tag;
                        $_tmp[$tag] = 1;
                    }
                }

                // Allow other modules to expand on our work
                $_arg = array(
                    'string' => $string
                );
                $_all = jrCore_trigger_event('jrCore', 'allowed_html_tags', $_all, $_arg);

                // Add in extras for proper table support
                if (count($_xtr) > 0) {
                    foreach ($_xtr as $xtra) {
                        $_all[] = $xtra;
                    }
                }

                // Finalize
                $allw = implode(',', $_all);
            }
            unset($_all);

            // now strip our tags
            if (!$trusted && strlen($allw) > 0) {

                // See: http://htmlpurifier.org/live/configdoc/plain.html
                $pc = HTMLPurifier_Config::createDefault();
                $pc->set('Core.NormalizeNewlines', false);
                $pc->set('Cache.SerializerPath', jrCore_get_module_cache_dir('jrCore'));
                $pc->set('HTML.AllowedElements', $allw);
                $pc->set('HTML.SafeEmbed', $safe_embed);
                $pc->set('HTML.MaxImgLength', null);
                $pc->set('CSS.Trusted', true);
                $pc->set('CSS.Proprietary', true);
                $pc->set('CSS.AllowTricky', true);
                $pc->set('CSS.MaxImgLength', null);
                $pc->set('Attr.EnableID', true);
                if (isset($_att) && is_array($_att) && count($_att) > 0) {
                    $pc->set('HTML.AllowedAttributes', implode(',', $_att));
                    unset($_att);
                    if ($trgt) {
                        $pc->set('Attr.AllowedFrameTargets', '_blank,_self,_parent,_top');
                    }
                }
                $pc = jrCore_trigger_event('jrCore', 'html_purifier', $pc);

                // Add support for HTML 5 Elements
                // See: https://github.com/kennberg/php-htmlpurfier-html5/blob/master/htmlpurifier_html5.php
                /** @noinspection PhpUndefinedMethodInspection */
                $pc->set('HTML.DefinitionID', 'html5-definitions');
                /** @noinspection PhpUndefinedMethodInspection */
                $pc->set('HTML.DefinitionRev', 1);

                /** @noinspection PhpUndefinedMethodInspection */
                if ($def = $pc->maybeGetRawHTMLDefinition()) {

                    /** @noinspection PhpUndefinedMethodInspection */
                    $def->addAttribute('a', 'data-lightbox', 'Text');

                    // Common
                    $_temp = array('section', 'nav', 'article', 'aside', 'header', 'footer', 'address');
                    foreach ($_temp as $ctag) {
                        if (isset($_tmp[$ctag])) {
                            /** @noinspection PhpUndefinedMethodInspection */
                            $def->addElement($ctag, 'Block', 'Flow', 'Common');
                        }
                    }

                    // Inline
                    $_temp = array('s', 'var', 'sub', 'sup', 'mark', 'wbr');
                    foreach ($_temp as $ctag) {
                        if (isset($_tmp[$ctag])) {
                            /** @noinspection PhpUndefinedMethodInspection */
                            $def->addElement($ctag, 'Inline', 'Inline', 'Common');
                        }
                    }

                    if (isset($_tmp['hgroup'])) {
                        /** @noinspection PhpUndefinedMethodInspection */
                        $def->addElement('hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common');
                    }

                    if (isset($_tmp['figure'])) {
                        /** @noinspection PhpUndefinedMethodInspection */
                        $def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
                        /** @noinspection PhpUndefinedMethodInspection */
                        $def->addElement('figcaption', 'Inline', 'Flow', 'Common');
                    }

                    if (isset($_tmp['video'])) {
                        /** @noinspection PhpUndefinedMethodInspection */
                        $def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
                            'src'      => 'URI',
                            'type'     => 'Text',
                            'width'    => 'Length',
                            'height'   => 'Length',
                            'poster'   => 'URI',
                            'preload'  => 'Enum#auto,metadata,none',
                            'controls' => 'Bool',
                        ));
                    }
                    if (isset($_tmp['source'])) {
                        /** @noinspection PhpUndefinedMethodInspection */
                        $def->addElement('source', 'Block', 'Flow', 'Common', array(
                            'src'  => 'URI',
                            'type' => 'Text',
                        ));
                    }

                    if (isset($_tmp['ins'])) {
                        /** @noinspection PhpUndefinedMethodInspection */
                        $def->addElement('ins', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));
                    }
                    if (isset($_tmp['del'])) {
                        /** @noinspection PhpUndefinedMethodInspection */
                        $def->addElement('del', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));
                    }

                    if (isset($_tmp['img'])) {
                        /** @noinspection PhpUndefinedMethodInspection */
                        $def->addAttribute('img', 'data-mce-src', 'Text');
                        /** @noinspection PhpUndefinedMethodInspection */
                        $def->addAttribute('img', 'data-mce-json', 'Text');
                    }

                    if (isset($_tmp['iframe'])) {
                        /** @noinspection PhpUndefinedMethodInspection */
                        $def->addAttribute('iframe', 'allowfullscreen', 'Bool');
                    }
                    if (isset($_tmp['table'])) {
                        /** @noinspection PhpUndefinedMethodInspection */
                        $def->addAttribute('table', 'height', 'Text');
                        /** @noinspection PhpUndefinedMethodInspection */
                        $def->addAttribute('td', 'border', 'Text');
                        /** @noinspection PhpUndefinedMethodInspection */
                        $def->addAttribute('th', 'border', 'Text');
                        /** @noinspection PhpUndefinedMethodInspection */
                        $def->addAttribute('tr', 'width', 'Text');
                        /** @noinspection PhpUndefinedMethodInspection */
                        $def->addAttribute('tr', 'height', 'Text');
                        /** @noinspection PhpUndefinedMethodInspection */
                        $def->addAttribute('tr', 'border', 'Text');
                    }
                }

                /** @noinspection PhpUndefinedFieldInspection */
                $pc->autoFinalize = true;
                $pf               = new HTMLPurifier($pc);

                $string = preg_replace("@<!-- pagebreak -->@", "#!-- pagebreak --#", $string); // allow pagebreak from TinyMCE editor
                $string = @$pf->purify($string);
                $string = preg_replace("@#!-- pagebreak --#@", "<!-- pagebreak -->", $string);
            }
        }
        else {
            // Strip everything
            // DO NOT rely on strip_tags alone - it will NOT strip out Javascript BETWEEN script tags.
            if (!$pf = jrCore_get_flag('jrCore_strip_html_config')) {
                $pc = HTMLPurifier_Config::createDefault();
                $pc->set('Core.NormalizeNewlines', false);
                $pc->set('Cache.SerializerPath', jrCore_get_module_cache_dir('jrCore'));
                $pc->set('HTML.AllowedElements', '');
                $pf = new HTMLPurifier($pc);
                jrCore_set_flag('jrCore_strip_html_config', $pf);
            }
            $string = @$pf->purify($string);
            $string = strip_tags($string);
        }
    }
    return $string;
}

/**
 * Recursively run stripslashes() on a string or array
 * @param mixed $data data mixed data to strip slashes from
 * @return mixed
 */
function jrCore_stripslashes($data)
{
    if (isset($data) && is_array($data)) {
        foreach ($data as $k => $v) {
            $data[$k] = jrCore_stripslashes($v);
        }
        return $data;
    }
    return stripslashes($data);
}

/**
 * Get IP Address of a viewer
 * @NOTE: DO NOT USE fdebug() in this function!
 * @return string Returns IP Address.
 */
function jrCore_get_ip()
{
    $tmp = jrCore_get_flag('jrcore_get_ip');
    if ($tmp) {
        return $tmp;
    }
    // See if we are running in Demo mode (all 1's)
    if ((!isset($_SERVER['REMOTE_ADDR']) || empty($_SERVER['REMOTE_ADDR'])) || $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR']) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $real_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $real_ip = $_SERVER['HTTP_X_FORWARDED'];
        }
        elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $real_ip = $_SERVER['HTTP_FORWARDED_FOR'];
        }
        elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $real_ip = $_SERVER['HTTP_FORWARDED'];
        }
        elseif (isset($_SERVER['HTTP_VIA'])) {
            $real_ip = $_SERVER['HTTP_VIA'];
        }
        elseif (isset($_SERVER['HTTP_X_COMING_FROM'])) {
            $real_ip = $_SERVER['HTTP_X_COMING_FROM'];
        }
        elseif (isset($_SERVER['HTTP_COMING_FROM'])) {
            $real_ip = $_SERVER['HTTP_COMING_FROM'];
        }
        elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $real_ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else {
            $real_ip = $_SERVER['REMOTE_ADDR'];
        }
    }
    else {
        $real_ip = $_SERVER['REMOTE_ADDR'];
    }
    if (!isset($real_ip{5})) {
        $real_ip = '0.0.0.0';
    }
    jrCore_set_flag('jrcore_get_ip', $real_ip);
    return $real_ip;
}

/**
 * Get substring of a string based on position and separator
 * @param string $string Input string to get field from
 * @param string $field numerical position in string to return, or "END" to return last word of string. If $field is negative, then counting begins from the end of the string backwards.
 * @param string $sep Field separator for string
 * @return mixed Returns field from string, or false on error
 */
function jrCore_string_field($string, $field, $sep = ' ')
{
    if ($sep == ' ') {
        // first - convert tabs to spaces
        $string = str_replace("\t", ' ', $string);
    }
    // see if they want the LAST field
    if ($field == 'NF' || $field == 'END') {
        $out = explode($sep, $string);
        return end($out);
    }
    // numerical (positive int)
    elseif ($field > 0) {
        $i = 1;
        foreach (@explode($sep, $string) as $v) {
            if (strlen($v) >= 1) {
                $_out[$i] = trim($v);
                if (isset($_out[$field])) {
                    return $_out[$field];
                }
                $i++;
            }
        }
    }
    // negative (backwards from end of string)
    else {
        $field = str_replace('-', '', $field);
        $i     = 1;
        foreach (@array_reverse(explode($sep, $string)) as $v) {
            if (strlen($v) >= 1) {
                $_out[$i] = trim($v);
                if (isset($_out[$field])) {
                    return $_out[$field];
                }
                $i++;
            }
        }
    }
    return false;
}

/**
 * Display debug information on screen
 * @return bool Returns True/False on Success/Fail.
 */
function debug()
{
    $out = jrCore_get_debug_output(func_get_args());
    if (php_sapi_name() == "cli") {
        echo $out;
    }
    else {
        $out = str_replace("\n", '<br>', jrCore_entity_string($out));
        echo '<style>.jrCore_debug { font-family: Monaco, "Courier New", Courier, monospace; font-size: 11px; font-weight: normal; text-transform: none; text-align: left; }</style><pre class="jrCore_debug">' . $out . '</pre>';
    }
    return true;
}

/**
 * Tell fdebug() to only log for specified IP address
 * @param $ip string IP Address
 * @return bool
 */
function fdebug_only_ip($ip)
{
    return jrCore_set_flag('fdebug_only_ip', $ip);
}

/**
 * Don't log fdebug() entries when the URI is an image
 * @return bool
 */
function fdebug_ignore_images()
{
    return jrCore_set_flag('fdebug_ignore_images', 1);
}

/**
 * Don't log fdebug() entries when the URI is an AJAX URI
 * @return bool
 */
function fdebug_ignore_ajax()
{
    return jrCore_set_flag('fdebug_ignore_ajax', 1);
}

/**
 * Log debug info to the data/logs/debug_log
 * @return bool
 */
function fdebug()
{
    $uip = jrCore_get_ip();
    if ($oip = jrCore_get_flag('fdebug_only_ip')) {
        if ($uip != $oip) {
            return true;
        }
    }

    // Check if we are ignoring images
    if (jrCore_get_flag('fdebug_ignore_images') && strpos($_SERVER['REQUEST_URI'], '/image/')) {
        return true;
    }

    // Check if we are ignoring AJAX
    if (jrCore_get_flag('fdebug_ignore_ajax') && strpos($_SERVER['REQUEST_URI'], '__ajax=1')) {
        return true;
    }

    $out = jrCore_get_debug_output(func_get_args());
    $tmp = fopen(APP_DIR . "/data/logs/debug_log", 'ab');
    if ($tmp) {
        flock($tmp, LOCK_EX);
        fwrite($tmp, $out);
        flock($tmp, LOCK_UN);
        fclose($tmp);
    }
    return true;
}

/**
 * Get output to use in debug() or fdebug()
 * @param array $_args
 * @return string
 */
function jrCore_get_debug_output($_args)
{
    global $_user;
    if (!is_array($_args)) {
        return '||';
    }
    $uip = jrCore_get_ip();

    // 0.81072800 1393853047
    $now = explode(' ', microtime());
    $msc = $now[0];
    $now = $now[1] + $now[0];

    // Have we run before?
    $beg = jrCore_get_flag('jrcore_process_start_time');
    $dif = round(($now - $beg), 3);
    $mem = jrCore_format_size(memory_get_usage(true));
    $fmt = date('c');
    $out = '';
    $usr = (isset($_user['user_name']{1})) ? "-(user: {$_user['user_name']})" : '';
    $url = jrCore_get_current_url();
    foreach ($_args as $arg) {
        $out .= PHP_EOL . "({$fmt}.{$msc} : {$dif})-(mem: {$mem})-(pid: " . getmypid() . ")-(ip: {$uip}){$usr}-(url: {$url})" . PHP_EOL;
        if (is_array($arg) || is_object($arg)) {
            $out .= print_r($arg, true);
        }
        else {
            $out .= "|{$arg}|" . PHP_EOL;
        }
    }
    return $out;
}

/**
 * Create a directory if needed and verify Core permissions
 * @param string $dir
 * @param bool $recursive
 * @return bool
 */
function jrCore_create_directory($dir, $recursive = false)
{
    global $_conf;
    if (!is_dir($dir)) {
        if (!@mkdir($dir, $_conf['jrCore_dir_perms'], $recursive)) {
            return false;
        }
    }
    else {
        @chmod($dir, $_conf['jrCore_dir_perms']);
    }
    return true;
}

/**
 * Recursively copy one directory to another
 * @param string $source Source Directory (directory to copy to)
 * @param string $destination Destination directory (directory to copy from)
 * @param array $_replace of K/V pairs for replacement within copied files
 * @return bool
 */
function jrCore_copy_dir_recursive($source, $destination, $_replace = null)
{
    global $_conf;
    if (!is_dir($source)) {
        return false;
    }
    if (!is_dir($destination)) {
        jrCore_create_directory($destination, true);
    }
    $f = opendir($source);
    if ($f) {
        while ($file = readdir($f)) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir("{$source}/{$file}")) {
                jrCore_copy_dir_recursive("{$source}/{$file}", "{$destination}/{$file}", $_replace);
            }
            else {
                $rep = false;
                switch (jrCore_file_extension($file)) {
                    case 'tpl':
                    case 'css':
                    case 'php':
                    case 'cfg':
                    case 'js':
                    case 'htm':
                    case 'html':
                    case 'xml':
                        $rep = true;
                        break;
                }
                if (!$rep || is_null($_replace) || !is_array($_replace)) {
                    // Straight Copy (possibly to new name)
                    $fnm = $file;
                    if (is_array($_replace)) {
                        $fnm = strtr($file, $_replace);
                    }
                    if (copy("{$source}/{$file}", "{$destination}/{$fnm}")) {
                        chmod("{$destination}/{$fnm}", $_conf['jrCore_file_perms']);
                    }
                }
                else {
                    // Key => Value replacements in destination
                    $tmp = file_get_contents("{$source}/{$file}");
                    $fnm = $file;
                    if (is_array($_replace)) {
                        $tmp = strtr($tmp, $_replace);
                        $fnm = strtr($file, $_replace);
                    }
                    jrCore_write_to_file("{$destination}/{$fnm}", $tmp);
                    unset($tmp);
                }
            }
        }
        closedir($f);
    }
    return true;
}

/**
 * Delete all content from a directory, including sub directories, optionally aged
 * @param string $dir Directory to remove all files and sub directories inside.
 * @param bool $cache_check default directory must be in cache directory
 * @param int $safe_seconds Files/Directories younger than "safe_seconds" will be ignore
 * @return bool
 */
function jrCore_delete_dir_contents($dir, $cache_check = true, $safe_seconds = 0)
{
    if (!is_dir($dir)) {
        return false;
    }
    if ($cache_check && strpos($dir, APP_DIR . '/data/cache') !== 0) {
        jrCore_logger('CRI', "jrCore_delete_dir_contents: invalid directory: " . str_replace(APP_DIR . '/', '', $dir) . " - must be a valid directory within the data/cache directory");
        return false;
    }
    // We never delete anything outside the APP_DIR
    if (strpos($dir, APP_DIR) !== 0) {
        jrCore_logger('CRI', "jrCore_delete_dir_contents: attempt to delete directory outside of APP_DIR: {$dir}");
        return false;
    }
    // We do not allow relative dirs...
    if (strpos(' ' . $dir, '..')) {
        jrCore_logger('CRI', "jrCore_delete_dir_contents: attempt to delete directory using relative path: {$dir}");
        return false;
    }
    $secs = false;
    if (intval($safe_seconds) > 0) {
        $secs = (time() - intval($safe_seconds));
    }

    // and now do our deletion
    $cnt = 0;
    if ($h = opendir($dir)) {
        while (($file = readdir($h)) !== false) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir("{$dir}/{$file}") && !is_link("{$dir}/{$file}")) {
                $cnt += jrCore_delete_dir_contents("{$dir}/{$file}", $cache_check, $safe_seconds);
                if (@rmdir("{$dir}/{$file}")) {
                    // Directory was empty so could be removed
                    $cnt++;
                }
            }
            else {
                if ($secs) {
                    $_tmp = stat("{$dir}/{$file}");
                    if (isset($_tmp['mtime']) && $_tmp['mtime'] < $secs) {
                        unlink("{$dir}/{$file}");  // OK
                        $cnt++;
                    }
                }
                else {
                    unlink("{$dir}/{$file}");  // OK
                    $cnt++;
                }
            }
        }
        closedir($h);
    }
    return $cnt;
}

/**
 * Recursively get all files and directories within a directory
 * @param string $dir Directory to get files for
 * @param bool $finish flag for final dir check
 * @return mixed
 */
function jrCore_get_directory_files($dir, $finish = true)
{
    $_out = false;
    $dir  = rtrim(trim($dir), '/');
    if ($h = opendir($dir)) {
        $_out = array();
        while (false !== ($file = readdir($h))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_link($dir . '/' . $file)) {
                $_out[] = "{$dir}/{$file}";
            }
            elseif (is_dir($dir . '/' . $file)) {
                $_tmp = jrCore_get_directory_files("{$dir}/{$file}", false);
                if (isset($_tmp) && is_array($_tmp)) {
                    $_out = array_merge($_out, $_tmp);
                }
            }
            else {
                $_out[] = "{$dir}/{$file}";
            }
        }
        closedir($h);
    }
    if ($finish && isset($_out) && is_array($_out)) {
        foreach ($_out as $k => $full_file) {
            $_out[$full_file] = str_replace("{$dir}/", '', $full_file);
            unset($_out[$k]);
        }
    }
    return $_out;
}

/**
 * Convert a string to a File System safe string
 * @param string $string String to return URL encoded
 * @return string
 */
function jrCore_file_string($string)
{
    return rawurlencode($string);
}

/**
 * Convert a string to a URL Safe string - used to generate slugs
 * @param string $string String to convert URLs in
 * @return string
 */
function jrCore_url_string($string)
{
    // Are we already a url encoded string?
    if (strpos(' ' . $string, '%') && !strpos(' ' . rawurldecode($string), '%')) {
        // We are already rawurlencoded
        return $string;
    }
    setlocale(LC_ALL, 'en_US.UTF8');
    $str = str_replace(array('İ', 'ı'), 'i', urldecode($string));
    // @ for "Detected an illegal character in input string"
    $str = @iconv('UTF-8', 'ASCII//TRANSLIT', substr(trim($str), 0, 128));
    $str = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $str);
    $str = strtolower(trim($str, '-'));
    $str = trim(trim(preg_replace("/[\/| -]+/", '-', $str)), '-');
    if (strlen($str) === 0) {
        // We may have removed everything - rawurlencode
        $str = rawurlencode(jrCore_str_to_lower(str_replace(array('"', "'", ' ', '&', '@', '/', '[', ']', '(', ')'), '-', $string)));
    }
    return trim(preg_replace('/[-+_]/', '-', $str), '-');
}

/**
 * Write data to a file with file locking
 * @param string $file File to write to
 * @param string $data Data to write to file
 * @param string $mode Mode - can be "overwrite" or "append"
 * @return bool
 */
function jrCore_write_to_file($file, $data = null, $mode = 'overwrite')
{
    global $_conf;
    if (is_null($data)) {
        return false;
    }
    ignore_user_abort(true);
    if ($mode == 'overwrite') {
        $f = fopen($file, 'wb');
    }
    else {
        $f = fopen($file, 'ab');
    }
    if (!$f || !is_resource($f)) {
        return false;
    }
    flock($f, LOCK_EX);
    $ret = fwrite($f, $data);
    flock($f, LOCK_UN);
    fclose($f);
    ignore_user_abort(false);
    if (!$ret) {
        return false;
    }
    if (!isset($_conf['jrCore_file_perms'])) {
        $_conf['jrCore_file_perms'] = 0644;
    }
    chmod($file, $_conf['jrCore_file_perms']);
    return true;
}

/**
 * Copy a file in chunks to use less memory
 * @param string $source_path
 * @param string $target_path
 * @param int $chunk_size
 * @return bool
 */
function jrCore_chunked_copy($source_path, $target_path, $chunk_size = 16)
{
    $ck = (intval($chunk_size) * 1048576);
    if (!$fs = fopen($source_path, 'rb')) {
        return false;
    }
    if (!$ft = fopen($target_path, 'w')) {
        return false;
    }
    while (!feof($fs)) {
        fwrite($ft, fread($fs, $ck));
    }
    fclose($fs);
    fclose($ft);
    return true;
}

/**
 * Get file extension from a file
 * Returns file extension in lower case!
 * @param string $file file string file name to return extension for
 * @return string
 */
function jrCore_file_extension($file)
{
    if (strpos($file, '.')) {
        $_tmp = explode('.', trim($file));
        if ($_tmp && is_array($_tmp)) {
            $ext = array_pop($_tmp);
            if (strpos($ext, '?')) {
                list($ext,) = explode('?', $ext, 2);
            }
            return jrCore_str_to_lower(trim($ext));
        }
    }
    return false;
}

/**
 * Get file extension for a given Mime-Type
 * This function relies on the /etc/mime.types file being readable by the web user
 * File extension is returned lower case
 * @param string $type Mime-Type
 * @return bool|string Returns extension if mime-type found, false if not found/known
 */
function jrCore_file_extension_from_mime_type($type)
{
    $ext = false;
    // Some quick ones...
    switch ($type) {
        case 'image/jpeg':
            $ext = 'jpg';
            break;
        case 'image/png':
            $ext = 'png';
            break;
        case 'image/gif':
            $ext = 'gif';
            break;
    }
    if (!$ext) {
        if (strpos($type, '/') && is_readable('/etc/mime.types')) {
            $_mim = jrCore_get_flag('jrcore_loaded_mime_types');
            if (!$_mim) {
                $_mim = file('/etc/mime.types');
                jrCore_set_flag('jrcore_loaded_mime_types', $_mim);
            }
            foreach ($_mim as $line) {
                if (strpos($line, $type) === 0) {
                    $ext = trim(jrCore_string_field($line, 'NF'));
                    if (isset($ext) && $ext != $type) {
                        switch ($ext) {
                            case 'jpe':
                            case 'jpeg':
                            case 'jfif':
                                $ext = 'jpg';
                                break;
                        }
                        break;
                    }
                }
            }
        }
    }
    if (!$ext) {
        // Go to our built in mime list
        $_ms = array_flip(jrCore_get_mime_list());
        if (isset($_ms[$type])) {
            return $_ms[$type];
        }
    }
    return $ext;
}

/**
 * Return an array of file extensions => mime types
 *
 * @return array
 */
function jrCore_get_mime_list()
{
    $_mimes = array(
        'txt'  => 'text/plain',
        'htm'  => 'text/html',
        'html' => 'text/html',
        'php'  => 'text/html',
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'xml'  => 'application/xml',
        'swf'  => 'application/x-shockwave-flash',

        // images
        'png'  => 'image/png',
        'jpe'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'jfif' => 'image/jpeg',
        'gif'  => 'image/gif',
        'bmp'  => 'image/bmp',
        'ico'  => 'image/x-icon',
        'tiff' => 'image/tiff',
        'tif'  => 'image/tiff',
        'svg'  => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archives
        'zip'  => 'application/zip',
        'rar'  => 'application/x-rar-compressed',
        'exe'  => 'application/x-msdownload',
        'msi'  => 'application/x-msdownload',
        'cab'  => 'application/vnd.ms-cab-compressed',

        // audio
        'mp3'  => 'audio/mpeg',
        'm4a'  => 'audio/mp4',
        'wma'  => 'audio/x-ms-wma',
        'ogg'  => 'audio/ogg',
        'flac' => 'audio/x-flac',
        'wav'  => 'audio/wav',
        'aac'  => 'application/aac',

        // video
        'flv'  => 'video/x-flv',
        'f4v'  => 'video/x-flv',
        'qt'   => 'video/quicktime',
        'mov'  => 'video/quicktime',
        'ogv'  => 'video/ogg',
        'm4v'  => 'video/x-m4v',
        'mpg'  => 'video/mpeg',
        'mp4'  => 'video/mp4',
        'avi'  => 'video/avi',
        '3gp'  => 'video/3gpp',
        '3g2'  => 'video/3gpp2',
        'wmv'  => 'video/x-ms-wmv',

        // adobe
        'pdf'  => 'application/pdf',
        'psd'  => 'image/vnd.adobe.photoshop',
        'ai'   => 'application/postscript',
        'eps'  => 'application/postscript',
        'ps'   => 'application/postscript',

        // ms office
        'doc'  => 'application/msword',
        'rtf'  => 'application/rtf',
        'xls'  => 'application/vnd.ms-excel',
        'ppt'  => 'application/vnd.ms-powerpoint',

        // open office
        'odt'  => 'application/vnd.oasis.opendocument.text',
        'ods'  => 'application/vnd.oasis.opendocument.spreadsheet'
    );
    return $_mimes;
}

/**
 * Get Mime-Type for a file
 *
 * @param string $file File Name
 * @return string
 */
function jrCore_mime_type($file)
{
    // Go on file extension - fastest
    $_ms = jrCore_get_mime_list();
    $ext = jrCore_file_extension($file);
    if (isset($ext) && strlen($ext) > 0 && isset($_ms[$ext])) {
        return $_ms[$ext];
    }

    // mime_content_type is deprecated
    if (is_file($file)) {
        if (function_exists('mime_content_type')) {
            return mime_content_type($file);
        }
        elseif (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME);
            $mime  = @finfo_file($finfo, $file);
            @finfo_close($finfo);
            if (isset($mime{0})) {
                if (isset($mime) && strpos($mime, '; ')) {
                    $mime = substr($mime, 0, strpos($mime, '; '));
                }
                return $mime;
            }
        }

        // Last check - see if we can do a system call to get the extension
        if (function_exists('system')) {
            ob_start();
            $mime = @system("file -bi {$file}");
            ob_end_clean();
            if (isset($mime{0})) {
                return $mime;
            }
        }
    }

    // Default
    return 'application/binary';
}

/**
 * Format an integer to "readable" size
 * @param int Number to format
 * @return string Returns formatted number
 */
function jrCore_format_size($number)
{
    // make sure we get a number
    if (!is_numeric($number)) {
        return false;
    }
    $kb = 1024;
    $mb = 1024 * $kb;
    $gb = 1024 * $mb;
    $tb = 1024 * $gb;

    // if it's less than a kb we just return
    // the size, otherwise we keep going until
    // the size is in the appropriate measurement range.
    if ($number < $kb) {
        return $number . 'B';
    }
    elseif ($number < $mb) {
        return round($number / $kb) . 'KB';
    }
    elseif ($number < $gb) {
        return round($number / $mb, 1) . 'MB';
    }
    elseif ($number < $tb) {
        return round($number / $gb, 2) . 'GB';
    }
    return round($number / $tb, 2) . 'TB';
}

/**
 * Format an integer of seconds to a "readable" timestamp
 * @param int $length Time number to format
 * @return string Returns formatted time
 */
function jrCore_format_seconds($length = 0)
{
    $length = round($length); // no decimals

    $numh = (int) ($length / 3600);
    $hour = str_pad($numh, 2, '0', STR_PAD_LEFT);
    $mins = str_pad(floor(($length - ($numh * 3600)) / 60), 2, '0', STR_PAD_LEFT);
    $secs = str_pad(($length % 60), 2, '0', STR_PAD_LEFT);
    $time = "{$hour}:{$mins}:{$secs}";
    return $time;
}

/**
 * Check if a given timestamp is in DLS time or not
 * @param int $timestamp
 * @param bool $adjust set to FALSE to not adjust for DLS
 * @return int
 */
function jrCore_get_timezone_dls_offset($timestamp, $adjust)
{
    global $_conf;
    if ($_conf['jrCore_system_timezone'] != 'UTC') {
        $tz = new DateTimeZone($_conf['jrCore_system_timezone']);
        $_t = $tz->getTransitions($timestamp, $timestamp);
        if ($_t && is_array($_t) && isset($_t[0]) && isset($_t[0]['offset'])) {
            // Are we adjust for DLS?
            if (!$adjust && $_t[0]['isdst'] != 1) {
                $add = 0;
                switch ($_conf['jrCore_system_timezone']) {
                    case 'America/Caracas':
                    case 'America/St_Johns':
                    case 'Asia/Tehran':
                    case 'Asia/Kabul':
                    case 'Asia/Kolkata':
                    case 'Asia/Rangoon':
                    case 'Australia/Darwin':
                        $add = 1800;
                        break;
                    case 'Asia/Katmandu':
                        $add = 2700;
                        break;
                }
                $_t[0]['offset'] += $add;
            }
            return $_t[0]['offset'];
        }
    }
    return 0;
}

/**
 * Formats an Epoch Time Stamp to the format specified by the system
 * @param int $timestamp Epoch Time Stamp to format
 * @param bool $date_only Set to true to just return DATE portion (instead of DATE TIME)
 * @param string $format Date Format for Display
 * @param bool $adjust set to FALSE to not adjust for DLS
 * @return string
 */
function jrCore_format_time($timestamp, $date_only = false, $format = null, $adjust = true)
{
    global $_user, $_conf;
    if (!jrCore_checktype($timestamp, 'number_nz')) {
        return '';
    }
    if ($date_only) {
        if (is_null($format)) {
            $format = $_conf['jrCore_date_format'];
        }
    }
    else {
        if (is_null($format)) {
            $format = "{$_conf['jrCore_date_format']} {$_conf['jrCore_hour_format']}";
        }
    }
    $dlsoffset = jrCore_get_timezone_dls_offset($timestamp, $adjust);
    $timestamp = ($timestamp + $dlsoffset);
    if ($format == 'relative') {

        $_lang = jrUser_load_lang_strings();

        $time = (time() + $dlsoffset);

        // Seconds
        $diff = ($time - $timestamp);
        if ($diff < 60) {
            return sprintf($diff > 1 ? "%s {$_lang['jrCore'][51]}" : $_lang['jrCore'][52], $diff);
        }

        // Minutes
        $diff = floor($diff / 60);
        if ($diff < 60) {
            return sprintf($diff > 1 ? "%s {$_lang['jrCore'][53]}" : $_lang['jrCore'][54], $diff);
        }

        // Hours
        $diff = floor($diff / 60);
        if ($diff < 24) {
            return sprintf($diff > 1 ? "%s {$_lang['jrCore'][55]}" : $_lang['jrCore'][56], $diff);
        }

        // Days
        $diff = round($diff / 24);
        if ($diff < 7) {
            return sprintf($diff > 1 ? "%s {$_lang['jrCore'][57]}" : $_lang['jrCore'][58], $diff);
        }

        // Months
        if ($diff < 30) {
            $diff = round($diff / 7);
            return sprintf($diff > 1 ? "%s {$_lang['jrCore'][59]}" : $_lang['jrCore'][60], $diff);
        }

        // Years
        $diff = round($diff / 30);
        if ($diff < 12) {
            return sprintf($diff > 1 ? "%s {$_lang['jrCore'][61]}" : $_lang['jrCore'][62], $diff);
        }
        $diff = date('Y', $time) - date('Y', $timestamp);
        return sprintf($diff > 1 ? "%s {$_lang['jrCore'][63]}" : $_lang['jrCore'][64], $diff);
    }
    $lang = (isset($_conf['jrUser_default_language'])) ? $_conf['jrUser_default_language'] : 'en_US';
    if (jrUser_is_logged_in() && isset($_user['user_language']{1})) {
        $lang = str_replace('-', '_', $_user['user_language']);
    }
    setlocale(LC_TIME, $lang . '.UTF-8');
    return gmstrftime($format, $timestamp);
}

/**
 * Get all installed locales on server
 * @return array|bool
 */
function jrCore_get_installed_locales()
{
    $_lc = false;
    ob_start();
    @system('locale -a', $ret);
    $out = ob_get_contents();
    ob_end_clean();
    if ($out && strlen($out) > 5) {
        $_lc = array();
        foreach (explode("\n", $out) as $l) {
            if (stripos($l, 'UTF')) {
                $k       = str_ireplace(array('.utf8', '.UTF-8'), '', str_replace('_', '-', $l));
                $_lc[$k] = $l;
            }
        }
    }
    return $_lc;
}

/**
 * Save a URL to a "stack" of URLs by name
 * avoid using as it is not cross-domain compatible
 * @param string $tag Text Tag for memory URL
 * @param string $url URL to remember
 * @return bool
 */
function jrCore_create_memory_url($tag, $url = 'referrer')
{
    if (!$url || strlen($url) === 0 || $url === 'referrer') {
        $url = jrCore_get_local_referrer();
    }
    if (!isset($_SESSION['jrcore_memory_urls'])) {
        $_SESSION['jrcore_memory_urls'] = array();
    }
    $_SESSION['jrcore_memory_urls'][$tag] = $url;
    return true;
}

/**
 * Get a URL from the memory stack by name
 * @param string $tag Text Tag for memory URL
 * @param string $url URL to return if not set
 * @return string
 */
function jrCore_get_memory_url($tag, $url = 'referrer')
{
    if ($tag == '1') {
        if ($url = jrUser_get_saved_location()) {
            return $url;
        }
    }
    return (isset($_SESSION['jrcore_memory_urls'][$tag])) ? $_SESSION['jrcore_memory_urls'][$tag] : $url;
}

/**
 * Delete a URL from the memory stack by name
 * @param string $tag Text Tag for memory URL
 * @return string
 */
function jrCore_delete_memory_url($tag)
{
    unset($_SESSION['jrcore_memory_urls'][$tag]);
    return true;
}

/**
 * Get max allowed upload size as defined by PHP and the user's Quota
 * @param int $quota_max Max as set in Profile Quota
 * @return int Returns Max Upload in bytes
 */
function jrCore_get_max_allowed_upload($quota_max = 0)
{
    // figure max upload form size
    $php_pmax = (int) ini_get('post_max_size');
    $php_umax = (int) ini_get('upload_max_filesize');
    $val      = ($php_pmax > $php_umax) ? $php_umax : $php_pmax;

    // For handling large file uploads we must use the following logic to arrive at our
    // max allowed upload size: Use 1/2 memory_limit, and if $val is smaller use that
    $php_mmax = ceil(intval(str_replace('M', '', ini_get('memory_limit'))) / 2);
    $val      = ($php_mmax > $val) ? $val : $php_mmax;
    $val      = ($val * 1048576);

    // Check if we are getting a quota restricted level
    if (is_numeric($quota_max) && $quota_max > 0 && $quota_max < $val) {
        $val = $quota_max;
    }
    return $val;
}

/**
 * Array of upload sizes to be used in a Select field
 * @return array Array of upload sizes
 */
function jrCore_get_upload_sizes()
{
    $s_max = (int) jrCore_get_max_allowed_upload(false);
    $_qmem = array();
    $_memr = array(1, 2, 4, 8, 16, 24, 32, 48, 64, 72, 96, 100, 128, 160, 200, 256, 300, 350, 384, 400, 500, 512, 600, 640, 700, 768, 800, 896, 1000, 1024, 1536, 2048, 3072, 4096);
    foreach ($_memr as $m) {
        $v = $m * 1048576;
        if ($v < $s_max) {
            $_qmem[$v] = jrCore_format_size($v);
        }
    }
    $_qmem[$s_max] = jrCore_format_size($s_max) . " - max allowed";
    return $_qmem;
}

/**
 * returns a URL if the referring URL is the user's own profile,
 * @param string $default URL to return if referrer is NOT from user's profile
 * @return string
 */
function jrCore_is_profile_referrer($default = 'referrer')
{
    global $_conf, $_user;
    jrCore_delete_flag('jrcore_is_profile_referrer');
    if (isset($_SERVER['HTTP_REFERER']) && !strpos(' ' . $_SERVER['HTTP_REFERER'], $_conf['jrCore_base_url'])) {
        // Is this a mapped domain?
        return jrCore_get_local_referrer();
    }
    elseif (jrUser_is_logged_in()) {
        if ($url = jrUser_get_saved_url_location($_user['_user_id'])) {
            jrCore_set_flag('jrcore_is_profile_referrer', 1);
            return $url;
        }
    }
    $url = jrCore_get_local_referrer();
    if (isset($_user['profile_url']) && strpos($url, "{$_conf['jrCore_base_url']}/{$_user['profile_url']}") === 0) {
        return $url;
    }
    return $default;
}

/**
 * Set a custom Send Header
 * @param string $header Header to set
 * @return bool
 */
function jrCore_set_custom_header($header)
{
    $_tmp = jrCore_get_flag('jrcore_set_custom_header');
    if (!$_tmp) {
        $_tmp = array();
    }
    $_tmp[] = $header;
    jrCore_set_flag('jrcore_set_custom_header', $_tmp);
    return true;
}

/**
 * Strips a string of all non UTF8 characters
 * @param string $string String to strip UTF8 characters from
 * @return mixed
 */
function jrCore_strip_non_utf8($string)
{
    // strip 2 byte sequences, as well as characters above U+10000
    $string = iconv('UTF-8', 'UTF-8//IGNORE', $string);

    $string = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]+' .
        '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})' .
        '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S', '', $string);

    // strip long 3 byte sequences and UTF-16 surrogates
    return preg_replace('/\xE0[\x80-\x9F][\x80-\xBF]|\xED[\xA0-\xBF][\x80-\xBF]/S', '', $string);
}

/**
 * Format a number based on Locale information
 * @param $number number to convert
 * @param $dec int Number of decimal places
 * @return string
 */
function jrCore_number_format($number, $dec = 0)
{
    if ($dec > 0) {
        $_tmp = localeconv();
        $dsep = (strlen($_tmp['decimal_point']) > 0) ? $_tmp['decimal_point'] : '.';
        $tsep = (strlen($_tmp['thousands_sep']) > 0) ? $_tmp['thousands_sep'] : ',';
        return number_format($number, $dec, $dsep, $tsep);
    }
    return number_format($number);
}

/**
 * Format currency to locale
 * @param $amount string Monetary amount
 * @param null $format string format
 * @return string
 */
function jrCore_money_format($amount, $format = null)
{
    if (is_null($format)) {
        $format = '%n';
    }
    return money_format($format, $amount);
}

/**
 * Get Server Operating System
 * @return string
 */
function jrCore_get_server_os()
{
    $sos = @php_uname();
    // Are we 64 or 32 bit?
    $bit = 32;
    if (stripos($sos, 'x86_64')) {
        $bit = 64;
    }
    // macOS | Mac OS X
    if (stristr(PHP_OS, 'darwin')) {
        ob_start();
        @system('sw_vers -productVersion', $ret);
        $out = ob_get_clean();
        if ($out && strpos($out, '.')) {
            list(, $maj,) = explode('.', $out);
            if ($maj <= 11) {
                $sos = 'Mac OS X ' . $out;
            }
            else {
                $sos = 'macOS ' . $out;
            }
        }
    }
    else {
        ob_start();
        @system('lsb_release -a', $ret);
        $out = ob_get_clean();
        if ($out && stripos($out, 'description')) {
            $out = explode("\n", $out);
            if ($out && is_array($out)) {
                foreach ($out as $line) {
                    $line = trim($line);
                    if (stripos($line, 'description') === 0) {
                        list(, $sos) = explode(':', $line);
                        $sos = trim($sos);
                        break;
                    }
                }
            }
        }
    }
    return $sos . ' ' . $bit . 'bit';
}

/**
 * Returns RAM usage on server
 * @return mixed
 */
function jrCore_get_system_memory()
{
    if (!function_exists('system')) {
        return false;
    }
    $key = "jrcore_get_system_memory";
    $out = jrCore_is_cached('jrCore', $key);
    if ($out) {
        if ($out == 'bad') {
            return false;
        }
        return json_decode($out, true);
    }

    // See what system we are on
    ob_start();
    $_out = array();
    // Mac OS X
    if (stristr(PHP_OS, 'darwin')) {

        ob_start();
        @system('vm_stat', $ret);
        $out = ob_get_clean();
        if ($ret != 0 || strlen($out) === 0) {
            jrCore_add_to_cache('jrCore', $key, 'bad', 30);
            return false;
        }
        $_tmp = explode("\n", $out);
        if (!isset($_tmp) || !is_array($_tmp)) {
            jrCore_add_to_cache('jrCore', $key, 'bad', 30);
            return false;
        }
        $used = 0;
        $free = 0;
        $inac = 0;
        $spec = 0;
        foreach ($_tmp as $line) {
            $line = rtrim(trim($line), '.');
            if (strpos($line, 'free')) {
                $free += (intval(jrCore_string_field($line, 'END')) * 4096);
            }
            elseif (strpos($line, 'inactive') || strpos($line, 'purgeable')) {
                $inac += (int) jrCore_string_field($line, 'END') * 4096;
            }
            elseif (strpos($line, 'speculative')) {
                $spec += (int) jrCore_string_field($line, 'END') * 4096;
            }
            elseif (strpos($line, 'wired') || strpos($line, 'active')) {
                $used += (int) jrCore_string_field($line, 'END') * 4096;
            }
        }
        $_out['memory_used'] = $used;
        $_out['memory_free'] = ($free + $spec + $inac);

        // Get total Memory
        ob_start();
        @system('sysctl hw.memsize', $ret);
        $out = ob_get_clean();
        if ($out && strlen($out) > 0) {
            // hw.memsize: 8589934592
            $_out['memory_total'] = intval(jrCore_string_field($out, 'END'));
        }
        if (!isset($_out['memory_total'])) {
            $_out['memory_total'] = ($_out['memory_used'] + $_out['memory_free']);
        }
    }
    else {
        //              total       used       free     shared    buffers     cached
        // Mem:       1033748     997196      36552          0     402108      83156
        // -/+ buffers/cache:     511932     521816
        // Swap:      1379872          0    1379872

        // Ubuntu 14.04
        //            total       used       free     shared    buffers     cached
        // Mem:       1015460     989508     25952    85816     138996      123060

        // Ubuntu 16.04
        //            total       used       free     shared    buff/cache  available
        // Mem:       1015188     230556     19044    44908     765588      701684

        @system('free', $ret);
        $out = ob_get_contents();
        ob_end_clean();
        if ($ret != 0 || strlen($out) === 0) {
            jrCore_add_to_cache('jrCore', $key, 'bad', 30);
            return false;
        }
        $_tmp = explode("\n", $out);
        if (!$_tmp || !is_array($_tmp)) {
            jrCore_add_to_cache('jrCore', $key, 'bad', 30);
            return false;
        }
        $_one                 = explode(' ', preg_replace("/[ ]+/", ' ', $_tmp[1]));
        $_out['memory_total'] = strval($_one[1] * 1024);
        if (stripos($_tmp[0], 'available')) {
            $_out['memory_free'] = strval(($_one[3] + $_one[5]) * 1024);
        }
        else {
            $_out['memory_free'] = strval(($_one[3] + $_one[5] + $_one[6]) * 1024);
        }
        $_out['memory_used'] = strval($_out['memory_total'] - $_out['memory_free']);
    }

    // Common
    if (jrCore_checktype($_out['memory_total'], 'number_nz')) {
        $_out['percent_used'] = round(($_out['memory_total'] - $_out['memory_free']) / $_out['memory_total'], 2) * 100;
    }
    else {
        $_out['percent_used'] = 0;
    }
    if ($_out['percent_used'] > 95) {
        $_out['class'] = 'bigsystem-cri';
    }
    elseif ($_out['percent_used'] > 90) {
        $_out['class'] = 'bigsystem-maj';
    }
    elseif ($_out['percent_used'] > 85) {
        $_out['class'] = 'bigsystem-min';
    }
    else {
        $_out['class'] = 'bigsystem-inf';
    }
    jrCore_add_to_cache('jrCore', $key, json_encode($_out), 30);
    return $_out;
}

/**
 * Get information about server disk usage
 * @return array
 */
function jrCore_get_disk_usage()
{
    $key = "jrcore_get_disk_usage";
    $out = jrCore_is_cached('jrCore', $key);
    if ($out) {
        return json_decode($out, true);
    }
    clearstatcache();
    $ts                   = disk_total_space(APP_DIR);
    $fs                   = disk_free_space(APP_DIR);
    $_out                 = array();
    $_out['disk_total']   = $ts;
    $_out['disk_free']    = $fs;
    $_out['disk_used']    = ($ts - $fs);
    $_out['percent_used'] = round(($ts - $fs) / $ts, 2) * 100;
    if ($_out['percent_used'] > 95) {
        $_out['class'] = 'bigsystem-cri';
    }
    elseif ($_out['percent_used'] > 90) {
        $_out['class'] = 'bigsystem-maj';
    }
    elseif ($_out['percent_used'] > 85) {
        $_out['class'] = 'bigsystem-min';
    }
    else {
        $_out['class'] = 'bigsystem-inf';
    }
    jrCore_add_to_cache('jrCore', $key, json_encode($_out), 300);
    return $_out;
}

/**
 * Returns load (run queue) information for server
 * @param int Number of processors to determine system load
 * @return mixed
 */
function jrCore_get_system_load($proc_num = 1)
{
    if (!function_exists('system')) {
        return false;
    }
    $key = "jrcore_get_system_load";
    $out = jrCore_is_cached('jrCore', $key);
    if ($out) {
        if ($out == 'bad') {
            return false;
        }
        return json_decode($out, true);
    }
    // go do our system() call and get our uptime
    ob_start();
    @system('uptime', $ret);
    $out = ob_get_contents();
    ob_end_clean();
    if ($ret != 0) {
        // looks we failed on getting our system load - return false
        jrCore_add_to_cache('jrCore', $key, 'bad', 30);
        return false;
    }
    if (!jrCore_checktype($proc_num, 'number_nz')) {
        $proc_num = 1;
    }
    // parse it for our needs
    // 17:45:22  up 95 days,  8:29,  3 users,  load average: 0.04, 0.01, 0.00
    //  2:41am  an 29 Tage 16:10,  1 Benutzer,  Durchschnittslast: 0,27, 0,25, 0,25
    $_cpu              = explode(" ", $out);
    $num1              = count($_cpu) - 1;
    $num2              = $num1 - 1;
    $num3              = $num2 - 1;
    $load[15]['level'] = trim($_cpu[$num1]);
    if (!is_numeric($load[15]['level'])) {
        jrCore_add_to_cache('jrCore', $key, 'bad', 30);
        return (false);
    }
    $load[5]['level'] = trim(str_replace(',', '', $_cpu[$num2]));
    $load[1]['level'] = trim(str_replace(',', '', $_cpu[$num3]));
    foreach (array(1, 5, 15) as $ll) {
        $level = number_format(round(($load[$ll]['level'] / $proc_num), 2), 2);
        if ($level > 4) {
            $load[$ll]['class'] = 'bigsystem-cri';
        }
        elseif ($level > 3) {
            $load[$ll]['class'] = 'bigsystem-maj';
        }
        elseif ($level > 2) {
            $load[$ll]['class'] = 'bigsystem-min';
        }
        else {
            $load[$ll]['class'] = 'bigsystem-inf';
        }
        $load[$ll]['level'] = $level;
    }
    jrCore_add_to_cache('jrCore', $key, json_encode($load), 300);
    return $load;
}

/**
 * Return information about Server Processors
 * @return mixed returns Array with CPU information
 */
function jrCore_get_proc_info()
{
    $key = "jrcore_get_proc_info";
    $out = jrCore_is_cached('jrCore', $key);
    if ($out) {
        return json_decode($out, true);
    }
    $_cpu = array();
    // lscpu
    if (function_exists('is_executable') && @is_executable('/usr/bin/lscpu')) {
        ob_start();
        @system('/usr/bin/lscpu');
        $out = ob_get_contents();
        ob_end_clean();
        $i                 = 1;
        $ncp               = 0;
        $_cpu[$i]['mhz']   = '';
        $_cpu[$i]['cache'] = '';
        $_cpu[$i]['model'] = '';
        // CPU(s):                2
        foreach (explode("\n", $out) as $line) {
            // Number of procs
            if (strpos($line, 'CPU(') === 0) {
                $ncp = (int) jrCore_string_field($line, 'NF');
            }
            elseif (strpos($line, 'CPU MHz') === 0) {
                $_cpu[$i]['mhz'] = round((jrCore_string_field($line, 'NF') / 1000), 2) . " GHz";
            }
            elseif (strpos($line, 'L2') === 0) {
                $_cpu[$i]['cache'] = trim(jrCore_string_field($line, 'NF'));
            }
        }
        if (jrCore_checktype($ncp, 'number_nz') && $ncp <= 32) {
            while ($i < $ncp) {
                $i++;
                $_cpu[$i] = $_cpu[1];
            }
        }
    }

    // proc file system
    elseif (@is_readable('/proc/cpuinfo')) {
        $_tmp = @file("/proc/cpuinfo");
        if (!is_array($_tmp)) {
            return 'unknown CPU';
        }
        $i = 0;
        foreach ($_tmp as $_v) {
            // get our processor
            if (stristr($_v, 'model name') || strstr($_v, 'altivec')) {
                $i++;
                $_cpu[$i]['model'] = trim(substr($_v, strpos($_v, ':') + 1));
            }
            elseif (stristr($_v, 'cpu MHz') || strstr($_v, 'clock')) {
                $_cpu[$i]['mhz'] = round(trim(substr($_v, strpos($_v, ':') + 1))) . " MHz";
            }
            elseif (stristr($_v, 'cache size') || strstr($_v, 'L2 cache')) {
                $_cpu[$i]['cache'] = trim(substr($_v, strpos($_v, ':') + 1));
            }
        }
    }
    // no proc file system - check for sysctl
    elseif (function_exists('is_executable') && @is_executable('/usr/sbin/sysctl')) {
        ob_start();
        @system('/usr/sbin/sysctl -a hw');
        $out = ob_get_contents();
        ob_end_clean();
        $i                 = 1;
        $ncp               = 0;
        $_cpu[$i]['mhz']   = '';
        $_cpu[$i]['cache'] = '';
        $_cpu[$i]['model'] = '';
        foreach (explode("\n", $out) as $line) {

            // Number of procs
            if (strstr($line, 'ncpu') && $ncp === 0) {
                $ncp = (int) jrCore_string_field($line, 'NF');
            }
            // Mac OS X CPU Model
            elseif (strstr($line, 'hw.model')) {
                $tmp               = explode('=', $line);
                $_cpu[$i]['model'] = trim($tmp[1]);
            }
            elseif (strstr($line, 'hw.cpufrequency:')) {
                $tmp = explode(' ', $line);
                $tmp = (int) end($tmp);
                if (is_numeric($tmp)) {
                    $_cpu[$i]['mhz'] = round(((($tmp / 1000) / 1000) / 1000), 2) . " GHz";
                }
            }
            elseif (strstr($line, 'hw.l2cachesize:')) {
                $tmp = explode(' ', $line);
                $tmp = (int) end($tmp);
                if (is_numeric($tmp)) {
                    $_cpu[$i]['cache'] = round($tmp / 1024) . " Kb";
                }
            }
        }
        if (jrCore_checktype($ncp, 'number_nz') && $ncp <= 32) {
            while ($i < $ncp) {
                $i++;
                $_cpu[$i] = $_cpu[1];
            }
        }
    }
    else {
        return 'unknown CPU';
    }
    jrCore_add_to_cache('jrCore', $key, json_encode($_cpu), 10800);
    return $_cpu;
}

/**
 * Extract a TAR archive
 * @param string $archive Archive name to extra
 * @param string $target Target directory to extract to
 * @return bool
 */
function jrCore_extract_tar_archive($archive, $target)
{
    // we need to include our TAR.php archive
    if (!class_exists('Archive_Tar')) {
        ini_set('include_path', APP_DIR . "/modules/jrCore/contrib/pear");
        require_once APP_DIR . "/modules/jrCore/contrib/pear/Tar.php";
    }
    $tar = new Archive_Tar($archive);
    $tar->setErrorHandling(PEAR_ERROR_PRINT);
    $tar->extract($target);
    ini_restore('include_path');
    return true;
}

/**
 * Create a TAR archive
 * @param string $archive Archive name to extra
 * @param array $_files Array of files to add to TAR archive
 * @return bool
 */
function jrCore_create_tar_archive($archive, $_files)
{
    global $_conf;
    if (!class_exists('Archive_Tar')) {
        ini_set('include_path', APP_DIR . "/modules/jrCore/contrib/pear");
        require_once APP_DIR . "/modules/jrCore/contrib/pear/Tar.php";
    }
    $tar = new Archive_Tar($archive);
    $tar->setErrorHandling(PEAR_ERROR_PRINT);
    $tar->create($_files);
    chmod($archive, $_conf['jrCore_file_perms']);
    return true;
}

/**
 * Create a new ZIP file from an array of files
 * @param $file string Full path of ZIP file to create
 * @param $_files array Array of files to add to zip file
 * @return bool
 */
function jrCore_create_zip_file($file, $_files)
{
    @ini_set('memory_limit', '1024M');
    require_once APP_DIR . "/modules/jrCore/contrib/zip/Zip.php";
    $zip = new Zip();
    $zip->setZipFile($file);
    $cnt = 0;
    foreach ($_files as $filename => $filepath) {
        if (is_file($filepath)) {
            if ($filename === $cnt) {
                $filename = str_replace(APP_DIR . '/', '', $filepath);
            }
            $zip->addFile(file_get_contents($filepath), $filename, filemtime($filepath));
        }
        $cnt++;
    }
    $zip->finalize();
    return true;
}

/**
 * ZIP files in a given array and "stream" the resulting ZIP to the browser
 * @param string $name Name of ZIP file to send
 * @param array $_files Array of files to send
 * @return bool
 */
function jrCore_stream_zip($name, $_files)
{
    jrCore_db_close();
    @ini_set('memory_limit', '1024M');
    $tmp = jrCore_get_module_cache_dir('jrCore');
    // Send out our ZIP stream
    require_once APP_DIR . "/modules/jrCore/contrib/zip/ZipStream.php";
    try {
        $zip = new ZipStream($name);
    }
    catch (Exception $e) {
        return false;
    }
    $cnt = 0;
    foreach ($_files as $filename => $filepath) {
        if (is_file($filepath)) {
            $f = fopen($filepath, 'rb');
            if ($filename === $cnt) {
                $filename = basename($filepath);
            }
            $zip->addLargeFile($f, $filename, $tmp);
            fclose($f);
            $cnt++;
        }
    }
    $zip->finalize();
    return true;
}

/**
 * Send a file to a browser that causes a "Save..." dialog
 * @param $file string File to send
 * @return bool
 */
function jrCore_send_download_file($file)
{
    jrCore_db_close();
    if (!is_file($file) || strpos($file, APP_DIR) !== 0 || strpos($file, '..')) {
        return false;
    }
    // Send headers to initiate download prompt
    $size = filesize($file);
    header('Content-Length: ' . $size);
    header('Connection: close');
    header('Content-Type: application/octet-stream');
    header('Content-Transfer-Encoding: binary');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');

    $handle = fopen($file, 'rb');
    if (!$handle) {
        jrCore_logger('CRI', "jrCore_send_download_file: unable to create file handle for download: {$file}");
        return false;
    }
    $bytes_sent = 0;
    while ($bytes_sent < $size) {
        fseek($handle, $bytes_sent);
        // Read 1 megabyte at a time...
        $buffer     = fread($handle, 1048576);
        $bytes_sent += strlen($buffer);
        echo $buffer;
        flush();
        unset($buffer);
        // Also - check that we have not sent out more data then the allowed size
        if ($bytes_sent >= $size) {
            fclose($handle);
            return true;
        }
    }
    fclose($handle);
    return true;
}

/**
 * Split array into equal number of arrays
 * @param $array array to split
 * @param $size int Size of array
 * @return array
 */
function jrCore_array_split($array, $size)
{
    $listlen = count($array);
    $partlen = floor($listlen / $size);
    $partrem = $listlen % $size;
    $split   = array();
    $mark    = 0;
    for ($px = 0; $px < $size; $px++) {
        $incr       = ($px < $partrem) ? $partlen + 1 : $partlen;
        $split[$px] = array_slice($array, $mark, $incr);
        $mark       += $incr;
    }
    return $split;
}

/**
 * Get skins in categories to show in the ACP
 * @return array
 */
function jrCore_get_acp_skins()
{
    // Expand our skins
    $_rt = jrCore_get_skins();
    $_sk = array();
    $_st = array();
    $_rp = array();
    foreach ($_rt as $skin_dir) {
        $func = "{$skin_dir}_skin_meta";
        if (!function_exists($func)) {
            require_once APP_DIR . "/skins/{$skin_dir}/include.php";
        }
        if (function_exists($func)) {
            $_mt            = $func();
            $_sk[$skin_dir] = $_mt;
            $cat            = 'general';
            if (isset($_mt['category'])) {
                foreach (explode(',', $_mt['category']) as $c) {
                    $c = trim(strtolower($c));
                    if (!isset($_st[$c])) {
                        $_st[$c] = array();
                    }
                    $_st[$c][$skin_dir] = $_sk[$skin_dir]['title'];
                }
            }
            else {
                if (!isset($_st[$cat])) {
                    $_st[$cat] = array();
                }
                $_st[$cat][$skin_dir] = $_sk[$skin_dir]['title'];
            }
        }
    }
    // We need to go through each module and get it's default page
    foreach ($_st as $cat => $_skins) {
        natcasesort($_skins);
        foreach ($_skins as $skin_dir => $title) {
            if (!isset($_rp[$cat])) {
                $_rp[$cat] = array();
            }
            $_rp[$cat][$skin_dir] = $_sk[$skin_dir];
            if (is_file(APP_DIR . "/skins/{$skin_dir}/config.php")) {
                $_rp[$cat][$skin_dir]['skin_index_page'] = 'global';
            }
            else {
                // info
                $_rp[$cat][$skin_dir]['skin_index_page'] = 'info';
            }
        }
    }
    return $_rp;
}

/**
 * Escape a string to sit between single quotes
 * @param string $str
 * @return mixed
 */
function jrCore_escape_single_quote_string($str)
{
    return addcslashes($str, "'\\");
}

/**
 * Create a new Global Lock
 * @param string $module
 * @param string $key
 * @param int $expire_seconds
 * @return bool
 */
function jrCore_create_global_lock($module, $key, $expire_seconds)
{
    $unq = jrCore_db_escape("{$module}/{$key}");
    $exp = (int) $expire_seconds;
    $tbl = jrCore_db_table_name('jrCore', 'global_lock');
    $req = "INSERT IGNORE INTO {$tbl} (lock_unique, lock_expires) VALUES ('{$unq}', (UNIX_TIMESTAMP() + {$exp}))";
    $lid = jrCore_db_query($req, 'INSERT_ID');
    return ($lid && $lid > 0) ? true : false;
}

/**
 * Delete a global lock
 * @param string $module
 * @param string $key
 * @return bool
 */
function jrCore_delete_global_lock($module, $key)
{
    $unq = jrCore_db_escape("{$module}/{$key}");
    $tbl = jrCore_db_table_name('jrCore', 'global_lock');
    $req = "DELETE FROM {$tbl} WHERE lock_unique = '{$unq}'";
    jrCore_db_query($req);
    return true;
}

/**
 * Delete expired global locks
 * @param bool $log_message
 * @return int
 */
function jrCore_delete_expired_global_locks($log_message = true)
{
    $tbl = jrCore_db_table_name('jrCore', 'global_lock');
    $req = "SELECT * FROM {$tbl} WHERE lock_expires < UNIX_TIMESTAMP()";
    $_rt = jrCore_db_query($req, 'lock_id', false, 'lock_unique');
    if ($_rt && is_array($_rt)) {
        $req = "DELETE FROM {$tbl} WHERE lock_id IN(" . implode(',', array_keys($_rt)) . ')';
        jrCore_db_query($req);
        $cnt = count($_rt);
        if ($log_message) {
            jrCore_logger('MAJ', "deleted {$cnt} expired global locks that had not been removed", $_rt);
        }
        return $cnt;
    }
    return 0;
}
