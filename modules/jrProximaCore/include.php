<?php
/**
 * Jamroom Proxima Core module
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

/**
 * jrProximaCore_meta
 */
function jrProximaCore_meta()
{
    $_tmp = array(
        'name'        => 'Proxima Core',
        'url'         => 'api',
        'version'     => '2.0.2',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'App and Core support for all Proxima modules',
        'category'    => 'proxima',
        'url_change'  => false,
        'requires'    => 'jrCore:5.3.0',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * jrProximaCore_init
 */
function jrProximaCore_init()
{
    // Tools
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrProximaCore', 'app_browser', array('App Browser', 'Browse and Create Proxima Apps'));

    // Tabs
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrProximaCore', 'app_browser', 'App Browser');

    // Our event triggers
    jrCore_register_event_trigger('jrProximaCore', 'process_init', 'Fired after request validation and before method function');
    jrCore_register_event_trigger('jrProximaCore', 'run_module_method_function', 'Fired just before the module method function');
    jrCore_register_event_trigger('jrProximaCore', 'http_response', 'Fired just before sending JSON results');
    jrCore_register_event_trigger('jrProximaCore', 'get_method_variables', 'Fired when jrProximaCore parses the method variables');
    jrCore_register_event_trigger('jrProximaCore', 'search_items', 'Fired in jrProximaCore_search_items');
    jrCore_register_event_trigger('jrProximaCore', 'get_response_keys', 'Fired when sending key value responses on a GET request');
    jrCore_register_event_trigger('jrProximaCore', 'app_validated', 'Fired when all App validation has been completed successfuly');
    jrCore_register_event_trigger('jrProximaCore', 'app_created', 'Fired when a new App has been created');
    jrCore_register_event_trigger('jrProximaCore', 'app_deleted', 'Fired when an App has been deleted');

    // Our default view for admins
    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrProximaCore', 'app_browser');

    // Core provided Value Functions
    jrCore_register_module_feature('jrProximaCore', 'value_function', 'jrProximaCore_increment_value_function', 'increment');
    jrCore_register_module_feature('jrProximaCore', 'value_function', 'jrProximaCore_decrement_value_function', 'decrement');

    // Default core permission checking
    jrCore_register_event_listener('jrProximaCore', 'get_item', 'jrProximaCore_get_item_listener');

    // Block User Profiles by default
    jrCore_register_event_listener('jrProfile', 'profile_view', 'jrProximaCore_profile_view_listener');

    // Core events
    jrCore_register_event_listener('jrCore', 'parse_url', 'jrProximaCore_parse_url_listener');
    jrCore_register_event_listener('jrCore', 'db_create_item', 'jrProximaCore_db_create_item_listener');
    jrCore_register_event_listener('jrCore', 'db_update_item', 'jrProximaCore_db_update_item_listener');

    // Cron Events
    jrCore_register_module_feature('jrCloudCron', 'event', 'jrProximaCore_compile_stats', 'hourly');

    return true;
}

//------------------------------------
// EVENT LISTENERS
//------------------------------------

/**
 * Disable JR session on API view
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrProximaCore_parse_url_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_urls;
    if (isset($_data['option']) && isset($_urls["px_{$_data['option']}"])) {
        jrCore_register_module_feature('jrUser', 'skip_session', 'jrProximaCore', $_data['option']);
    }
    return $_data;
}

/**
 * Add Proxima Core keys to DB items
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrProximaCore_db_create_item_listener($_data, $_user, $_conf, $_args, $event)
{
    // $uid will NOT be set for jrProximaUser when creating a user account!
    $uid = jrProximaCore_get_session_user_id();
    if ($uid && $uid > 0) {

        foreach ($_data as $k => $v) {
            // make sure we do not have any VALUE functions on create
            if (strpos($v, '__') === 0) {
                unset($_data[$k]);
            }
        }

        $_data['_app_id']     = jrProximaCore_get_active_app_id();
        $_data['_user_id']    = $uid;
        $_data['_profile_id'] = jrProximaCore_get_session_profile_id();
        $_data["_p{$uid}"]    = 2; // 2 = read/write
    }
    return $_data;
}

/**
 * Run value functions on item updates
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrProximaCore_db_update_item_listener($_data, $_user, $_conf, $_args, $event)
{
    $uid = jrProximaCore_get_session_user_id();
    if ($uid && $uid > 0) {

        // Cleanup incoming data
        foreach ($_data as $k => $v) {
            // Remove hidden keys
            if (strpos($v, '_') === 0) {
                unset($_data[$k]);
            }
        }
    }
    return $_data;
}

/**
 * Block access to User Profiles
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrProximaCore_profile_view_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_conf['jrProximaCore_enable_profiles']) && $_conf['jrProximaCore_enable_profiles'] == 'off' && !jrUser_is_admin()) {
        jrCore_page_not_found();
    }
    return $_data;
}

/**
 * Check session user permissions on item access
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrProximaCore_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    // When new items are created we need to set the permissions
    // on the object so only the user has access (by default)
    if (jrProximaCore_get_client_access_level() != 'master' && !isset($_data['_p0'])) {
        if (!$uid = jrProximaCore_get_session_user_id()) {
            // No session = no access
            return jrProximaCore_http_response(401, 'invalid item owner (1)');
        }
        if ($_args['module'] == 'jrProximaUser') {
            if ($uid != $_data['_item_id']) {
                // They do not own this account
                return jrProximaCore_http_response(401, 'invalid item owner (2)');
            }
        }
        else {
            // Must have at LEAST read access:
            // keys are:
            // _p0 = global "user" access
            // _p[uid] = specific user_id access
            // flags are:
            // 1 = read access
            // 2 = read/write access
            if (!isset($_data["_p{$uid}"])) {
                return jrProximaCore_http_response(401, 'invalid item owner (2)');
            }
        }
    }
    return $_data;
}

//------------------------------------
// VALUE FUNCTIONS
//------------------------------------

/**
 * Increment a Given Key value
 * @param $module string Module
 * @param $item_id int Item ID
 * @param $key string key that the function is being run on
 * @param $_args array array of value function arguments being passed in
 * @param $_new array new keys being added/updated for the item,
 * @return mixed
 */
function jrProximaCore_increment_value_function($module, $item_id, $key, $_args, $_new)
{
    if (ctype_digit($_args[0])) {
        $val = intval($_args[0]);
    }
    else {
        $val = floatval($_args[0]);
    }
    jrCore_db_increment_key($module, $item_id, $key, $val);
    return false;
}

/**
 * Decrement a Given Key value
 * @param $module string Module
 * @param $item_id int Item ID
 * @param $key string key that the function is being run on
 * @param $_args array array of value function arguments being passed in
 * @param $_new array new keys being added/updated for the item,
 * @return mixed
 */
function jrProximaCore_decrement_value_function($module, $item_id, $key, $_args, $_new)
{
    if (ctype_digit($_args[0])) {
        $val = intval($_args[0]);
    }
    else {
        $val = floatval($_args[0]);
    }
    jrCore_db_decrement_key($module, $item_id, $key, $val);
    return false;
}

//------------------------------------
// FUNCTIONS
//------------------------------------

/**
 * Remove unused method variables
 * @param $_vars array incoming vars
 * @return mixed
 */
function jrProximaCore_clean_method_variables($_vars)
{
    unset($_vars['_uri'], $_vars['module'], $_vars['module_url'], $_vars['option'], $_vars['REQUEST_METHOD'], $_vars['_1'], $_vars['_2'], $_vars['_3'], $_vars['_4']);
    return jrCore_trigger_event('jrProximaCore', 'clean_method_variables', $_vars);
}

/**
 * Add Module prefix to data array
 * @param $module string module
 * @param $_vars array
 * @return array
 */
function jrProximaCore_add_module_prefix($module, $_vars)
{
    if (!is_array($_vars)) {
        return $_vars;
    }
    $pfx = jrCore_db_get_prefix($module);
    foreach ($_vars as $k => $v) {
        if (strpos($k, '_') !== 0 && strpos($k, "{$pfx}_") !== 0) {
            $_vars["{$pfx}_{$k}"] = $v;
            unset($_vars[$k]);
        }
    }
    return $_vars;
}

/**
 * Get Access Method for client request
 * @return string
 */
function jrProximaCore_get_access_method()
{
    return $GLOBALS['REQUEST_METHOD'];
}

/**
 * Generate a unique code of given length
 * @param $length int Length of code to generate
 * @return string
 */
function jrProximaCore_get_code($length)
{
    $chr = "abcdefghijkmnopqrstvwxyzABCDEFGHIJKLMNPQRSTVWXYZ123456789";
    $len = strlen($chr);
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= substr($chr, mt_rand(0, $len - 1), 1);
    }
    return $key;
}

/**
 * Get a jrProximaCore App from provided App Key
 * @param $key string App Key
 * @return mixed Array on success, bool false on app not found
 */
function jrProximaCore_get_app_by_key($key)
{
    if (!$_ap = jrCore_is_cached('jrProximaCore', $key, false, false)) {
        $key = jrCore_db_escape($key);
        $tbl = jrCore_db_table_name('jrProximaCore', 'app');
        $req = "SELECT * FROM {$tbl} WHERE (app_client_key = '{$key}' OR app_master_key = '{$key}') LIMIT 1";
        $_ap = jrCore_db_query($req, 'SINGLE');
        if (!is_array($_ap)) {
            return false;
        }
        jrCore_add_to_cache('jrProximaCore', $key, $_ap, 0, 0, false, false);
    }
    if ($key == $_ap['app_client_key']) {
        jrCore_set_flag('jrProximaCore_client_access', 'client');
    }
    else {
        global $_user;
        $_user['_user_id']    = 0;
        $_user['_profile_id'] = 0;
        jrCore_set_flag('jrProximaCore_client_access', 'master');
    }
    jrCore_set_flag('jrProximaCore_active_app_id', $_ap['app_id']);
    return $_ap;
}

/**
 * Return a list of apps by id => name
 */
function jrProximaCore_get_apps()
{
    $tbl = jrCore_db_table_name('jrProximaCore', 'app');
    $req = "SELECT app_id, app_name FROM {$tbl} ORDER BY app_name ASC";
    return jrCore_db_query($req, 'app_id', false, 'app_name');
}

/**
 * Get active current app_id
 * @return string client|master
 */
function jrProximaCore_get_active_app_id()
{
    return jrCore_get_flag('jrProximaCore_active_app_id');
}

/**
 * Get current client access level
 * @return string client|master
 */
function jrProximaCore_get_client_access_level()
{
    return jrCore_get_flag('jrProximaCore_client_access');
}

/**
 * Get Active Session Key
 * @return mixed
 */
function jrProximaCore_get_session_key()
{
    return (isset($GLOBALS['APP_SESSION_KEY'])) ? $GLOBALS['APP_SESSION_KEY'] : false;
}

/**
 * Get client credentials (i.e. app_key)
 */
function jrProximaCore_get_credentials()
{
    // Client Key
    $app = false;
    if (isset($_SERVER['PHP_AUTH_USER']{0})) {
        $app = $_SERVER['PHP_AUTH_USER'];
    }
    else {
        if (isset($_SERVER['HTTP_CLIENT_KEY']{0})) {
            $app = $_SERVER['HTTP_CLIENT_KEY'];
        }
    }
    if ($app) {
        $GLOBALS['APP_CLIENT_KEY'] = $app;
    }
    else {
        // no app - no access
        return false;
    }

    // Session Key
    $key = false;
    if (isset($_SERVER['PHP_AUTH_PW']{0})) {
        $key = $_SERVER['PHP_AUTH_PW'];
    }
    else {
        if (isset($_SERVER['HTTP_SESSION_KEY']{0})) {
            $key = $_SERVER['HTTP_SESSION_KEY'];
        }
    }
    // Validate
    if ($key) {
        $GLOBALS['APP_SESSION_KEY'] = $key;
        if ($app) {
            return array($app, $key);
        }
    }
    if ($app) {
        return array($app, '');
    }
    return false;
}

/**
 * get a Proxima module config
 * @param $module string Module to initialize
 * @return bool|array
 */
function jrProximaCore_get_module_config($module)
{
    $func = "{$module}_px_config";
    if (!function_exists($func)) {
        // We should exist here
        return false;
    }
    $_cfg = $func();
    if (isset($_cfg['prefix'])) {
        jrCore_set_flag('jrProximaCore_active_module_prefix', $_cfg['prefix']);
    }
    jrCore_set_flag('jrProximaCore_active_module', $module);
    return $_cfg;
}

/**
 * Get Active px Module
 * @return bool|mixed
 */
function jrProximaCore_get_active_module()
{
    return jrCore_get_flag('jrProximaCore_active_module');
}

/**
 * Get Active px Module URL
 * @return bool|mixed
 */
function jrProximaCore_get_active_module_url()
{
    global $_post;
    return (isset($_post['option']{0})) ? $_post['option'] : false;
}

/**
 * Get Active px Module data store prefix
 * @return bool|mixed
 */
function jrProximaCore_get_active_module_prefix()
{
    if ($pfx = jrCore_get_flag('jrProximaCore_active_module_prefix')) {
        return $pfx;
    }
    return false;
}

/**
 * Execute a jrProximaCore Module method function
 * @param string $module Module
 * @param string $method Method
 * @param array $_app Application data array variables
 * @param array $_cfg Application Config
 * @param array $_vars URL and POST/PUT variables
 * @return string
 */
function jrProximaCore_run_module_method_function($module, $method, $_app, $_cfg, $_vars)
{
    global $_post;
    // jrProximaData_method_get
    $func = "{$module}_px_method_{$method}";
    if (!function_exists($func)) {
        return jrProximaCore_http_response(404, 'module method function not found');
    }
    $_args = array(
        'module' => $module,
        'method' => $method,
        '_app'   => $_app,
        '_cfg'   => $_cfg
    );
    $_vars = jrCore_trigger_event('jrProximaCore', 'run_module_method_function', $_vars, $_args);
    if (is_array($_vars)) {
        unset($_vars['module'], $_vars['option'], $_vars['module_url']);
        return $func($_post, $_app, $_cfg, $_vars);
    }
    return $_vars;
}

/**
 * Sends an HTTP Response to a client
 * @param $code int HTTP Status Code
 * @param $note string Notice Text
 * @param $data mixed Payload
 * @return string
 */
function jrProximaCore_http_response($code, $note = null, $data = false)
{
    ob_start();
    switch ($code) {
        case 200:
            $text = 'OK';
            break;
        case 201:
            $text = 'Created';
            break;
        case 202:
            $text = 'Accepted';
            break;
        case 204:
            $text = 'No Content';
            break;
        case 206:
            $text = 'Partial Content';
            break;
        case 301:
            $text = 'Moved Permanently';
            break;
        case 302:
            $text = 'Found';
            break;
        case 304:
            $text = 'Not Modified';
            break;
        case 400:
            $text = 'Bad Request';
            break;
        case 401:
            $text = 'Unauthorized';
            break;
        case 403:
            $text = 'Forbidden';
            break;
        case 404:
            $text = 'Not Found';
            break;
        case 405:
            $text = 'Method Not Allowed';
            break;
        case 406:
            $text = 'Not Acceptable';
            break;
        case 410:
            $text = 'Gone';
            break;
        case 415:
            $text = 'Unsupported Media Type';
            break;
        case 429:
            $text = 'Too Many Requests';
            break;
        case 500:
            $text = 'Internal Server Error';
            break;
        case 501:
            $text = 'Not Implemented';
            break;
        case 503:
            $text = 'Service Unavailable';
            break;
        default:
            $code = 400;
            $text = 'Invalid Code';
            break;
    }
    header("HTTP/1.0 {$code} {$text}");

    // code = code
    // text = response text
    // note = note (optional)
    // data = data payload (optional)
    $_out = array(
        'code' => $code,
        'text' => $text
    );
    if (!is_null($note)) {
        $_out['note'] = $note;
    }
    if ($data) {
        $_out['data'] = $data;
    }
    $_out = jrCore_trigger_event('jrProximaCore', 'http_response', $_out);
    jrCore_json_response($_out, false, false);
    return ob_get_clean();
}

/**
 * Get POST/GET/PUT/DELETE variables
 */
function jrProximaCore_get_method_variables()
{
    global $_post;
    switch ($_SERVER['REQUEST_METHOD']) {

        case 'POST':
        case 'GET':
            $GLOBALS['REQUEST_METHOD'] = strtolower($_SERVER['REQUEST_METHOD']);
            // The core has already parsed these for us
            $_out = $_post;
            break;

        default:

            // [Host] => local.proximacore.com
            // [Accept] => */*
            // [Content-Length] => 1989
            // [Expect] => 100-continue
            // [Content-Type] => multipart/form-data; boundary=----------------------------b9b6159b9622

            // Check for PUT/DELETE
            if (!$_out = jrCore_get_flag('jrProximaCore_get_method_variables')) {
                $_tmp = file_get_contents('php://input');

                // On a PUT request, if the data is sent as FORM data, then
                // we need to parse it out of the request.  If it is a straight
                // up PUT request, then the entire BODY is the file, and it is
                // going to be replacing an existing file
                // http://stackoverflow.com/questions/12005790/how-to-receive-a-file-via-http-put-with-php

                if (strlen($_tmp) > 0 && strpos($_tmp, 'Content-Disposition')) {

                    // $_tmp - will be one entry for each field/file received
                    $_tmp = explode('form-data;', $_tmp);
                    $_out = array();
                    foreach ($_tmp as $k => $v) {
                        if ($k === 0) {
                            continue;
                        }
                        // $v will look like:
                        // name="putfoo"
                        // name="put_quick_curls"; filename="quick-curls.txt" ... (file contents)
                        $_v = explode('"', $v);
                        if (strpos($v, '; filename=')) {
                            $fld = trim($_v[1]);

                            $_FILES[$fld] = array(
                                'name' => $_v[3]
                            );

                            unset($_v[0], $_v[1], $_v[2], $_v[3]);
                            $_ct = explode("\n", $_v[4]);
                            list(, $ctt) = explode(':', $_ct[1]);

                            $_FILES[$fld]['type'] = trim($ctt);

                            $_ct = implode('"', $_v);
                            $_ct = explode("\n", $_ct);
                            $num = count($_ct);
                            unset($_ct[0], $_ct[1], $_ct[2], $_ct[--$num], $_ct[--$num]);

                            $tdr = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
                            if ($nam = tempnam($tdr, 'jr')) {
                                if (jrCore_write_to_file($nam, trim(implode("\n", $_ct)))) {
                                    $_FILES[$fld]['tmp_name'] = $nam;
                                    $_FILES[$fld]['size']     = filesize($nam);
                                    $_FILES[$fld]['error']    = 0;
                                }
                            }
                        }
                        else {
                            $_out["{$_v[1]}"] = explode("\r\n", trim($_v[2]));
                            $_out["{$_v[1]}"] = trim(reset($_out["{$_v[1]}"]));
                            if (strpos($_out["{$_v[1]}"], '-------------------') === 0) {
                                $_out["{$_v[1]}"] = '';
                            }
                        }
                    }
                    if (isset($_post) && is_array($_post)) {
                        $_out = $_post + $_out;
                    }
                }
                elseif (strlen($_tmp) > 0) {
                    parse_str(trim($_tmp), $_out);
                    if (isset($_post) && is_array($_post)) {
                        $_out = $_post + $_out;
                    }
                }
                else {
                    $_out = $_post;
                }
                jrCore_set_flag('jrProximaCore_get_method_variables', $_out);
            }
            $GLOBALS['REQUEST_METHOD'] = strtolower($_SERVER['REQUEST_METHOD']);
    }

    jrCore_trigger_event('jrProximaCore', 'get_method_variables', $_out);
    return $_out;
}

/**
 * Returns true if active session user_id owns item
 * @param $_item array Item Array
 * @param $_app array App Array
 * @return bool
 */
function jrProximaCore_is_item_owner($_item, $_app)
{
    return ($_item['_user_id'] == jrProximaCore_get_session_user_id()) ? true : false;
}

/**
 * Sets up Item keys for return
 * @param $module string Module
 * @param $_it array Item to remove keys from
 * @return mixed
 */
function jrProximaCore_get_response_keys($module, $_it)
{
    $pfx = jrCore_db_get_prefix($module);
    $_ar = array(
        'module' => $module,
        'prefix' => $pfx
    );
    $_it = jrCore_trigger_event('jrProximaCore', 'get_response_keys', $_it, $_ar);
    if ($_it && is_array($_it)) {
        foreach ($_it as $k => $v) {
            if (strpos($k, '_') === 0) {
                switch ($k) {
                    case '_item_id':
                        $_it['_id'] = (int) $v;
                        break;
                    case '_updated':
                    case '_created':
                        $_it[$k] = (int) $v;
                        continue 2;
                        break;
                    case '_files':
                        $_it[$k] = $v;
                        continue 2;
                        break;
                    case '_user_id':
                        if ($module == 'jrProximaUser') {
                            $_it['_id'] = (int) $v;
                        }
                        else {
                            $_it['_user_id'] = (int) $v;
                        }
                        continue 2;
                        break;
                }
                unset($_it[$k]);
            }
            else {
                $nk       = str_replace("{$pfx}_", '', $k);
                $_it[$nk] = $v;
                unset($_it[$k]);
            }
        }
        ksort($_it);
    }
    return $_it;
}

/**
 * Get access level for user to object
 * @param $uid int User ID
 * @param $_it array Object
 * @return bool|string
 */
function jrProximaCore_get_user_access_level($uid, $_it)
{
    // Master
    if (jrProximaCore_get_client_access_level() == 'master') {
        return 'write';
    }
    // Global Write Access
    elseif (isset($_it['_p0']) && $_it['p0'] == 2) {
        return 'write';
    }
    elseif (isset($_it["_p{$uid}"]) && $_it["_p{$uid}"] == 2) {
        return 'write';
    }
    elseif (isset($_it['_p0']) && $_it['p0'] == 1) {
        return 'read';
    }
    elseif (isset($_it["_p{$uid}"]) && $_it["_p{$uid}"] == 1) {
        return 'read';
    }
    else {
        // See if this user is part of an ACL group that has access to this item
        $tmp = jrCore_trigger_event('jrProximaCore', 'get_user_access_level', $_it);
        if ($tmp == 'read' || $tmp == 'write') {
            return $tmp;
        }
    }
    return false;
}

//---------------------------------
// DataStore wrappers
//---------------------------------

/**
 * Search a px module data store for matching items
 * @param $module string Proxima Module
 * @param $_data array Item array
 * @param $collection string restrict search to specified collection
 * @return mixed array on success, bool false on fail
 */
function jrProximaCore_search_items($module, $_data, $collection = null)
{
    global $_conf;
    // Item Search
    // search?search1=name%20eq%20brian&pagebreak=20&page=1
    $_sc           = array(
        'search'               => array(),
        'order_by'             => array('_item_id' => 'numerical_desc'),
        'skip_triggers'        => true,
        'ignore_pending'       => true,
        'privacy_check'        => false,
        'pxcore_search_active' => 1,
        'limit'                => 10
    );
    // Check for master versus client searches
    if (jrProximaCore_get_client_access_level() != 'master') {
        $uid             = jrProximaCore_get_session_user_id();
        $_sc['search'][] = "_p{$uid} > 0 || _p0 > 0";
    }
    $pfx = jrProximaCore_get_active_module_prefix();
    foreach ($_data as $k => $v) {
        if (strpos($k, 'search') === 0) {
            list($key, $opt, $val) = explode(' ', $v, 3);
            switch ($opt) {
                case 'gt':
                    $opt = '>';
                    break;
                case 'gte':
                    $opt = '>=';
                    break;
                case 'lt':
                    $opt = '<';
                    break;
                case 'lte':
                    $opt = '<=';
                    break;
                case 'eq':
                    $opt = '=';
                    break;
                case 'ne':
                    $opt = '!=';
                    break;
            }
            if (strpos($key, '_') === 0) {
                $_sc['search'][] = "{$key} {$opt} {$val}";
            }
            else {
                $_sc['search'][] = "{$pfx}_{$key} {$opt} {$val}";
            }
        }
    }
    // Add collection
    if (!is_null($collection)) {
        $_sc['search'][] = "_g = {$collection}";
    }
    else {
        $_sc['search'][] = "_app_id = " . jrProximaCore_get_active_app_id();
    }

    // What is our max result limit?
    $max = (isset($_conf['jrProximaCore_max_results'])) ? intval($_conf['jrProximaCore_max_results']) : 100;

    // Simplepagebreak
    if (isset($_data['simplepagebreak'])) {
        $_data['simplepagebreak'] = (int) $_data['simplepagebreak'];
        if ($_data['simplepagebreak'] > $max) {
            $_data['simplepagebreak'] = $max;
        }
        $_sc['simplepagebreak'] = $_data['simplepagebreak'];
        // Page
        if (isset($_data['page']) && jrCore_checktype($_data['page'], 'number_nz')) {
            $_sc['page'] = (int) $_data['page'];
        }
        else {
            $_sc['page'] = 1;
        }
    }
    // Pagebreak
    if (isset($_data['pagebreak'])) {
        $_data['pagebreak'] = (int) $_data['pagebreak'];
        if ($_data['pagebreak'] > $max) {
            $_data['pagebreak'] = $max;
        }
        $_sc['pagebreak'] = $_data['pagebreak'];
        // Page
        if (isset($_data['page']) && jrCore_checktype($_data['page'], 'number_nz')) {
            $_sc['page'] = (int) $_data['page'];
        }
        else {
            $_sc['page'] = 1;
        }
    }
    if (isset($_data['limit'])) {
        $_data['limit'] = (int) $_data['limit'];
        if ($_data['limit'] > $max) {
            $_data['limit'] = $max;
        }
        $_sc['limit'] = $_data['limit'];
    }
    // Order By
    if (isset($_data['order_by']) && strlen($_data['order_by']) > 0) {
        list($ob, $od) = explode(' ', $_data['order_by'], 2);
        $ob = trim($ob);
        if ($ob == '_id') {
            $ob = '_item_id';
        }
        $od = strtolower($od);
        switch ($od) {
            case 'asc':
            case 'desc':
            case 'numerical_asc':
            case 'numerical_desc':
            case 'random':
                break;
            default:
                return jrProximaCore_http_response(400, 'invalid order_by - must be one of: asc, desc, numerical_asc, numerical_desc, random');
                break;
        }
        $_sc['order_by'] = array($ob => $od);
    }
    $_rt = jrCore_db_search_items($module, $_sc);
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
        unset($_rt['info']['page_base_url']);
        $_rt = jrCore_trigger_event('jrProximaCore', 'search_items', $_rt);
        return $_rt;
    }
    return false;
}

/**
 * Execute value functions on item
 * @param $module string Module
 * @param $item_id int Existing DS Item ID
 * @param $_it array Key => Values being updated
 * @return array
 */
function jrProximaCore_run_value_functions($module, $item_id, $_it)
{
    if (!is_array($_it)) {
        return $_it;
    }

    // See if we have loaded registered value functions
    $_vf = jrCore_get_flag('run_value_functions');
    if (!$_vf) {
        $_vf = array();
        $_ia = jrCore_get_registered_module_features('jrProximaCore', 'value_function');
        if ($_ia && is_array($_ia)) {
            foreach ($_ia as $func => $_inf) {
                foreach ($_inf as $name => $opts) {
                    $_vf["__{$name}"] = $func;
                }
            }
        }
        jrCore_set_flag('run_value_functions', $_vf);
    }

    $_dl = array();
    foreach ($_it as $k => $v) {
        if (strpos($v, '__') === 0) {

            // We have a VALUE function
            list($actn, $args) = explode('(', rtrim(trim($v), ')'));
            switch ($actn) {

                case '__delete_key':
                    // For performance reasons, deleting keys is done inline here
                    $_dl[] = $k;
                    unset($_it[$k]);
                    break;

                default:
                    if (isset($_vf[$actn])) {
                        $func = $_vf[$actn];
                        if (function_exists($func)) {
                            $args = explode(',', $args);
                            if ($args && is_array($args)) {
                                foreach ($args as $ak => $av) {
                                    $args[$ak] = trim($av);
                                }
                            }
                            if ($tmp = $func($module, $item_id, $k, $args, $_it)) {
                                // We successfully processed this key - update value
                                $_it[$k] = $tmp;
                            }
                            else {
                                // If our VALUE function returns false, it means REMOVE the key from the update array
                                unset($_it[$k]);
                            }
                        }
                        else {
                            return jrProximaCore_http_response(400, "registered value function for {$actn}: {$func} - does not exist");
                        }
                    }
                    else {
                        return jrProximaCore_http_response(400, "unregistered value function: {$actn}");
                    }
                    break;

            }
        }
    }
    if (count($_dl) > 0) {
        jrCore_db_delete_multiple_item_keys($module, $item_id, $_dl);
    }
    return $_it;
}

/**
 * Get unique Location URL for a px DataStore item
 * @param $module string Proxima Module
 * @param $id int Datastore ID
 * @param $collection string Optional collection name
 * @return string
 */
function jrProximaCore_get_unique_item_url($module, $id, $collection = null)
{
    global $_conf;
    $crl = jrCore_get_module_url('jrProximaCore');
    $url = str_replace('px_', '', jrCore_get_module_url($module));
    if (!is_null($collection)) {
        return "{$_conf['jrCore_base_url']}/{$crl}/{$url}/{$collection}/{$id}";
    }
    return "{$_conf['jrCore_base_url']}/{$crl}/{$url}/{$id}";
}

//---------------------------------
// session wrappers
//---------------------------------

/**
 * Get cache systems registered with the jrProximaCore
 */
function jrProximaCore_get_registered_session_systems()
{
    global $_mods;
    $_out = array(
        'jrProximaCore_mysql' => $_mods['jrProximaCore']['module_name'] . ': MySQL (default)'
    );
    if ($_tmp = jrCore_get_registered_module_features('jrProximaCore', 'session_plugin')) {
        foreach ($_tmp as $mod => $_opt) {
            foreach ($_opt as $name => $desc) {
                $_out["{$mod}_{$name}"] = $_mods[$mod]['module_name'] . ": {$desc}";
            }
        }
    }
    return $_out;
}

/**
 * Get active session plugin
 * @return string
 */
function jrProximaCore_get_active_session_system()
{
    global $_conf;
    if (isset($_conf['jrProximaCore_active_session_system']{1})) {
        return $_conf['jrProximaCore_active_session_system'];
    }
    return 'jrProximaCore_mysql';
}

/**
 * Get Activer _user_id for current session
 * @return bool
 */
function jrProximaCore_get_session_user_id()
{
    return (isset($GLOBALS['APP_SESSION_USER_ID'])) ? intval($GLOBALS['APP_SESSION_USER_ID']) : false;
}

/**
 * Get User information for active session user
 * @return mixed
 */
function jrProximaCore_get_session_user_info()
{
    if ($uid = jrProximaCore_get_session_user_id()) {
        return jrCore_db_get_item('jrUser', $uid);
    }
    return false;
}

/**
 * Get _user_id for active session
 * @return bool
 */
function jrProximaCore_get_session_profile_id()
{
    return (isset($GLOBALS['APP_SESSION_PROFILE_ID'])) ? $GLOBALS['APP_SESSION_PROFILE_ID'] : 0;
}

/**
 * Create a new Proxima Session
 * @param $app_id int Application ID
 * @param $_user array User Account k=>v pairs
 * @param $expires int Expiration time in seconds (max 30 * 86400)
 * @return string
 */
function jrProximaCore_create_user_session($app_id, $_user, $expires = -1)
{
    $fnc = jrProximaCore_get_active_session_system();
    $fnc = "plugin_{$fnc}_create_user_session";
    if (function_exists($fnc)) {
        if ($expires == -1) {
            $expires = (7 * 86400);
        }
        if ($key = $fnc($app_id, $_user, $expires)) {
            $GLOBALS['APP_SESSION_USER_ID']    = $_user['_user_id'];
            $GLOBALS['APP_SESSION_PROFILE_ID'] = $_user['_profile_id'];
            return $key;
        }
    }
    return false;
}

/**
 * Delete a Proxima Session
 * @param $app_id int Application ID
 * @param $_user array User Account k=>v pairs
 * @return string
 */
function jrProximaCore_delete_user_session($app_id, $_user)
{
    $fnc = jrProximaCore_get_active_session_system();
    $fnc = "plugin_{$fnc}_delete_user_session";
    if (function_exists($fnc)) {
        $fnc($app_id, $_user);
        unset($GLOBALS['APP_SESSION_USER_ID']);
        unset($GLOBALS['APP_SESSION_PROFILE_ID']);
        return true;
    }
    return false;
}

/**
 * Check if a given user session key is valid
 * @param string $key Client Session Key
 * @return bool
 */
function jrProximaCore_is_valid_user_session($key)
{
    if ($key == 'no_session') {
        return false;
    }
    $fnc = jrProximaCore_get_active_session_system();
    $fnc = "plugin_{$fnc}_is_valid_user_session";
    if (function_exists($fnc)) {
        $_rt = $fnc($key);
        if ($_rt && is_array($_rt) && is_numeric($_rt['uid'])) {
            $GLOBALS['APP_SESSION_USER_ID']    = $_rt['uid'];
            $GLOBALS['APP_SESSION_PROFILE_ID'] = $_rt['pid'];
            return true;
        }
    }
    return false;
}

/**
 * Set the active _user_id and _profile_id for an active API user
 * @return bool
 */
function jrProximaCore_set_active_user_and_profile_ids()
{
    global $_user;
    if (isset($_user) && is_array($_user)) {
        $_user['_user_id']    = (isset($GLOBALS['APP_SESSION_USER_ID']) && is_numeric($GLOBALS['APP_SESSION_USER_ID'])) ? $GLOBALS['APP_SESSION_USER_ID'] : '';
        $_user['_profile_id'] = (isset($GLOBALS['APP_SESSION_PROFILE_ID']) && is_numeric($GLOBALS['APP_SESSION_PROFILE_ID'])) ? $GLOBALS['APP_SESSION_PROFILE_ID'] : '';
    }
    return true;
}

//---------------------------------
// jrProximaCore MySQL plugins
//---------------------------------

/**
 * jrProximaCore MySQL Session plugin - create new user session
 * @param $app_id int Application ID
 * @param $_user array User Account info
 * @param $expires int Expiration time in seconds
 * @return mixed
 */
function plugin_jrProximaCore_mysql_create_user_session($app_id, $_user, $expires)
{
    $exp = (time() + $expires);
    $key = md5(microtime() . jrCore_get_ip());
    $tbl = jrCore_db_table_name('jrProximaCore', 'session');
    $uid = (int) $_user['_user_id'];
    $pid = (int) $_user['_profile_id'];
    $req = "INSERT INTO {$tbl} (session_key, session_app_id, session_user_id, session_profile_id, session_expires) VALUES ('{$key}', '{$app_id}', '{$uid}', '{$pid}', '{$exp}') ON DUPLICATE KEY UPDATE session_key = '{$key}', session_expires = '{$exp}'";
    if (jrCore_db_query($req)) {
        return $key;
    }
    return false;
}

/**
 * jrProximaCore MySQL Session plugin - delete existing user session
 * @param $app_id int Application ID
 * @param $_user array User Account info
 * @return mixed
 */
function plugin_jrProximaCore_mysql_delete_user_session($app_id, $_user)
{
    global $_conf;
    $tbl = jrCore_db_table_name('jrProximaCore', 'session');
    $uid = (int) $_user['_user_id'];
    if (isset($_conf['jrProximaUser_sso']) && $_conf['jrProximaUser_sso'] == 'off') {
        $req = "DELETE FROM {$tbl} WHERE session_app_id = '{$app_id}' AND session_user_id = '{$uid}'";
    }
    else {
        $req = "DELETE FROM {$tbl} WHERE session_user_id = '{$uid}'";
    }
    jrCore_db_query($req);
    return true;
}

/**
 * jrProximaCore MySQL Session plugin - check for valid session
 * @param string $key Client Session Key
 * @return mixed
 */
function plugin_jrProximaCore_mysql_is_valid_user_session($key)
{
    $tbl = jrCore_db_table_name('jrProximaCore', 'session');
    $req = "SELECT session_user_id AS uid, session_profile_id AS pid FROM {$tbl} WHERE session_key = '" . jrCore_db_escape($key) . "' AND session_expires > " . time() . " LIMIT 1";
    $_ps = jrCore_db_query($req, 'SINGLE');
    if (is_array($_ps)) {
        return $_ps;
    }
    return false;
}

/**
 * jrProximaCore MySQL Session plugin - cleanup old sessions
 * @return int - returns number of cleaned up sessions
 */
function proxima_jrProximaCore_mysql_cleanup()
{
    $tbl = jrCore_db_table_name('jrProximaCore', 'session');
    $req = "DELETE FROM {$tbl} WHERE session_expires < " . time();
    return jrCore_db_query($req, 'COUNT');
}
