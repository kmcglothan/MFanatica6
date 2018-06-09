<?php
/**
 * Jamroom Proxima User module
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
 * jrProximaUser_meta
 */
function jrProximaUser_meta()
{
    $_tmp = array(
        'name'        => 'Proxima User',
        'url'         => 'px_user',
        'version'     => '1.2.3',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Proxima User - User Accounts and Session API',
        'category'    => 'proxima',
        'url_change'  => false,
        'requires'    => 'jrProximaCore:2.0.0',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * jrProximaUser_init
 */
function jrProximaUser_init()
{
    // Delete users when an app is deleted
    jrCore_register_event_listener('jrProximaCore', 'app_deleted', 'jrProximaUser_app_deleted_listener');
    jrCore_register_event_listener('jrProximaCore', 'get_response_keys', 'jrProximaUser_get_response_keys_listener');

    // Delete users worker
    jrCore_register_queue_worker('jrProximaUser', 'delete_data', 'jrProximaUser_delete_data_worker', 0, 1, 14400);
    return true;
}

/**
 * px_config
 */
function jrProximaUser_px_config()
{
    // define endpoints that do not require a session
    return array(
        'no_session' => array(
            'post' => array('/', '/login', '/forgot')
        )
    );
}

//------------------------------------
// EVENT LISTENERS
//------------------------------------

/**
 * User Cleanup when an app is deleted
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrProximaUser_app_deleted_listener($_data, $_user, $_conf, $_args, $event)
{
    // App is being deleted
    if (isset($_data['app_id']) && jrCore_checktype($_data['app_id'], 'number_nz')) {

        // Deleting users could be a big job - queue
        jrCore_queue_create('jrProximaUser', 'delete_data', $_data);

    }
    return $_data;
}

/**
 * Remove keys from response
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrProximaUser_get_response_keys_listener($_data, $_user, $_conf, $_args, $event)
{
    unset($_data['user_password'],
        $_data['user_group'],
        $_data['user_validate'],
        $_data['user_validated'],
        $_data['user_language'],
        $_data['user_active']);
    return $_data;
}

//------------------------------------
// QUEUE WORKER
//------------------------------------

/**
 * Delete DS entries when an App is deleted
 * @param array $_queue The queue entry the worker will receive
 * @return bool
 */
function jrProximaUser_delete_data_worker($_queue)
{
    // We need to delete all user DS entries for $_queue['app_id']
    $_rt = jrCore_db_get_multiple_items_by_key('jrUser', '_app_id', $_queue['app_id'], true);
    if ($_rt && is_array($_rt)) {
        $_rt = array_chunk($_rt, 500);
        foreach ($_rt as $_ids) {
            jrCore_db_delete_multiple_items('jrUser', $_ids);
        }
    }
    return true;
}

//------------------------------------
// METHODS
//------------------------------------

/**
 * POST Method function
 * @param $_post array jrProximaCore post|get|put|delete URL vars
 * @param $_app array Proxima Application
 * @param $_cfg array jrProximaUser module config
 * @param $_vars array Method parameters
 * @return string
 */
function jrProximaUser_px_method_post($_post, $_app, $_cfg, $_vars)
{
    global $_conf;

    // Create New Account: api/user
    if (!isset($_post['_1'])) {

        // Check for signups enable
        if (isset($_conf['jrProximaUser_enabled']) && $_conf['jrProximaUser_enabled'] == 'off') {
            return jrProximaCore_http_response(503, 'user signup is currently disabled');
        }

        // Check for required fields
        if (!isset($_vars['password'])) {
            return jrProximaCore_http_response(400, 'missing password value');
        }

        $pfx = jrCore_db_get_prefix('jrUser');
        if (!isset($_conf['jrProximaUser_require_fields'])) {
            $_conf['jrProximaUser_require_fields'] = 'both';
        }
        switch ($_conf['jrProximaUser_require_fields']) {

            // Both email and username
            case 'both':
                if (!isset($_vars['user_name'])) {
                    return jrProximaCore_http_response(400, 'missing required user_name value');
                }
                if (!isset($_vars['user_email'])) {
                    return jrProximaCore_http_response(400, 'missing required user_email value');
                }
                if (!jrCore_checktype($_vars['user_email'], 'email')) {
                    return jrProximaCore_http_response(400, 'provided user_email is not valid');
                }
                // Validate the values are unique
                $_sp = array(
                    'search'         => array(
                        "user_name = {$_vars['user_name']} || user_email = {$_vars['user_email']}"
                    ),
                    'return_keys'    => array('_user_id', 'user_name', 'user_email'),
                    'skip_triggers'  => true,
                    'privacy_check'  => false,
                    'ignore_pending' => true,
                    'limit'          => 1
                );
                $_rt = jrCore_db_search_items('jrUser', $_sp);
                if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
                    $bad = 'user_name';
                    if ($_rt['_items'][0]['user_email'] == $_vars['user_email']) {
                        $bad = 'user_email';
                    }
                    $_rs = array(
                        '_id'      => $_rt['_items'][0]['_user_id'],
                        'location' => jrProximaCore_get_unique_item_url('jrProximaUser', $_rt['_items'][0]['_user_id'])
                    );
                    return jrProximaCore_http_response(400, "account with {$bad} value already exists", $_rs);
                }
                break;

            // user only
            case 'user':
                if (!isset($_vars['user_name'])) {
                    return jrProximaCore_http_response(400, 'missing required user_name value');
                }
                // Validate value is unique
                $_rt = jrCore_db_get_item_by_key('jrUser', "{$pfx}_name", $_vars['user_name'], true);
                if (is_array($_rt)) {
                    $_rs = array(
                        '_id'      => $_rt['_item_id'],
                        'location' => jrProximaCore_get_unique_item_url('jrProximaUser', $_rt['_item_id'])
                    );
                    return jrProximaCore_http_response(400, "account using user_name value already exists", $_rs);
                }
                break;

            // email only
            case 'email':
                if (!isset($_vars['user_email'])) {
                    return jrProximaCore_http_response(400, 'missing required user_email value');
                }
                if (!jrCore_checktype($_vars['user_email'], 'email')) {
                    return jrProximaCore_http_response(400, 'provided user_email is not valid');
                }
                // Validate value is unique
                $_rt = jrCore_db_get_item_by_key('jrUser', "{$pfx}_email", $_vars['user_email'], true);
                if (is_array($_rt)) {
                    $_rs = array(
                        '_id'      => $_rt['_item_id'],
                        'location' => jrProximaCore_get_unique_item_url('jrProximaUser', $_rt['_item_id'])
                    );
                    return jrProximaCore_http_response(400, "account using user_email value already exists", $_rs);
                }
                break;
        }

        // Validate Quota (if provided)
        if (isset($_vars['quota_id']) && jrCore_checktype($_vars['quota_id'], 'number_nz')) {
            $_qt = jrProfile_get_quota($_vars['quota_id']);
            if (!$_qt || !is_array($_qt) || !isset($_qt['quota_jrUser_allow_signups']) || $_qt['quota_jrUser_allow_signups'] != 'on') {
                return jrProximaCore_http_response(400, "quota_id is not configured to allow signups");
            }
            unset($_qt);
            $qid = (int) $_vars['quota_id'];
        }
        else {
            $qid = (int) $_conf['jrProfile_default_quota_id'];
        }

        // Looks good - create account
        require APP_DIR . '/modules/jrUser/contrib/phpass/PasswordHash.php';
        $hash = new PasswordHash($_conf['jrProximaUser_password_stretching'], false);
        $pass = $hash->HashPassword($_vars['password']);
        $code = md5(microtime());

        // Create our user account
        $_data = array(
            'user_password'   => $pass,
            'user_language'   => $_conf['jrUser_default_language'],
            'user_active'     => 1,
            'user_validated'  => 1,
            'user_validate'   => $code,
            'user_last_login' => 'UNIX_TIMESTAMP()'
        );
        if (isset($_vars['user_name']) && strlen($_vars['user_name']) > 0) {
            $_data['user_name'] = $_vars['user_name'];
        }
        if (isset($_vars['user_email']) && strlen($_vars['user_email']) > 0) {
            $_data['user_email'] = $_vars['user_email'];
        }
        unset($_vars['user_name'], $_vars['user_email'], $_vars['password']);

        // Add additional posted KV
        $_vars = jrProximaCore_clean_method_variables($_vars);
        if (count($_vars) > 0) {
            foreach ($_vars as $k => $v) {
                if (strlen($k) > 0 && strpos($k, '_') !== 0 && !isset($_data["{$pfx}_{$k}"])) {
                    $_data["{$pfx}_{$k}"] = $v;
                }
            }
        }

        // All users created through the API get an APP_ID
        $_core = array(
            '_app_id' => jrProximaCore_get_active_app_id()
        );
        if ($uid = jrCore_db_create_item('jrUser', $_data, $_core, false)) {

            // User account is created - create profile
            $_prof = array(
                'profile_quota_id' => $qid,
                'profile_active'   => 1,
                'profile_private'  => 1
            );
            if (isset($_data['user_name'])) {
                $_prof['profile_name'] = $_data['user_name'];
                $_prof['profile_url']  = jrCore_url_string($_data['user_name']);
            }
            else {
                $_prof['profile_name'] = $uid;
                $_prof['profile_url']  = $uid;
            }
            $pid = jrCore_db_create_item('jrProfile', $_prof);

            // Update with new profile id
            $_temp = array();
            $_core = array(
                '_user_id'    => $uid,
                '_profile_id' => $pid
            );
            jrCore_db_update_item('jrProfile', $pid, $_temp, $_core);

            $_temp = array('user_group' => 'user');
            $_user = array(
                '_user_id'    => $uid,
                '_profile_id' => $pid
            );

            // Initiate our USER SESSION - LEAVE THIS HERE
            // This ensures any uploaded media are handled in the udpate_item listener
            $sid = jrProximaCore_create_user_session($_app['app_id'], $_user);
            jrCore_db_update_item('jrUser', $uid, $_temp, $_user);

            // Profile is created - add user_id -> profile_id into link table
            jrProfile_create_user_link($uid, $pid);

            // Make sure profile media directory is created
            jrCore_create_media_directory($pid);

            // Update the profile_count for the quota this profile just signed up
            jrProfile_increment_quota_profile_count($qid);

            // Send out created and activated events
            $_temp = array(
                '_user'      => $_data,
                '_profile'   => $_prof,
                'session_id' => $sid
            );
            jrCore_trigger_event('jrProximaUser', 'user_created', $_temp, $_core);

            // Start session
            $_rs = array(
                '_id'        => $uid,
                '_app_id'    => $_app['app_id'],
                'session_id' => $sid,
                'location'   => jrProximaCore_get_unique_item_url('jrProximaUser', $uid)
            );
            return jrProximaCore_http_response(201, 'user created and session started', $_rs);

        }
        // Fall through - something happened in the DB
        return jrProximaCore_http_response(500);
    }

    switch ($_post['_1']) {

        // login: api/user/login
        case 'login':

            // Must get password
            if (!isset($_vars['password']) || strlen($_vars['password']) === 0) {
                return jrProximaCore_http_response(400, 'must supply password');
            }

            // did we get an ID?
            $_us = false;
            if (isset($_vars['id'])) {
                if (!jrCore_checktype($_vars['id'], 'number_nz')) {
                    return jrProximaCore_http_response(400, 'must supply a valid id');
                }
                $_us = jrCore_db_get_item('jrUser', $_vars['id'], true);
            }
            // Did we get a user_name?
            elseif (isset($_vars['user_name'])) {
                $_us = jrCore_db_get_item_by_key('jrUser', 'user_name', $_vars['user_name'], true);
            }
            // Did we get a user_email?
            elseif (isset($_vars['user_email'])) {
                $_us = jrCore_db_get_item_by_key('jrUser', 'user_email', $_vars['user_email'], true);
            }
            if (!is_array($_us)) {
                return jrProximaCore_http_response(404, 'user not found');
            }
            // Are we enforcing by app_id?
            else {
                if (isset($_conf['jrProximaUser_sso']) && $_conf['jrProximaUser_sso'] == 'off') {
                    if (isset($_us['_app_id']) && $_us['_app_id'] != jrProximaCore_get_active_app_id()) {
                        return jrProximaCore_http_response(404, 'user not found');
                    }
                }
            }

            // Validate password
            require APP_DIR . '/modules/jrUser/contrib/phpass/PasswordHash.php';
            $hash = new PasswordHash($_conf['jrProximaUser_password_stretching'], false);
            if (!$hash->CheckPassword($_post['password'], $_us['user_password'])) {
                return jrProximaCore_http_response(400, 'invalid password');
            }

            // Update user_last_login
            jrCore_db_update_item('jrUser', $_us['_user_id'], array('user_last_login' => 'UNIX_TIMESTAMP()'), null, false, false);

            // Looks good - start session
            $_rs = array(
                '_id'        => $_us['_user_id'],
                '_app_id'    => $_app['app_id'],
                'session_id' => jrProximaCore_create_user_session($_app['app_id'], $_us)
            );
            return jrProximaCore_http_response(200, 'session started', $_rs);
            break;

        case 'forgot':

            // did we get an ID?
            $_us = false;
            if (isset($_vars['id'])) {
                if (!jrCore_checktype($_vars['id'], 'number_nz')) {
                    return jrProximaCore_http_response(400, 'must supply a valid id');
                }
                $_us = jrCore_db_get_item('jrUser', $_vars['id'], true);
            }
            // Did we get a user_name?
            elseif (isset($_vars['user_name'])) {
                $_us = jrCore_db_get_item_by_key('jrUser', 'user_name', $_vars['user_name'], true);
            }
            // Did we get a user_email?
            elseif (isset($_vars['user_email'])) {
                $_us = jrCore_db_get_item_by_key('jrUser', 'user_email', $_vars['user_email'], true);
            }
            if (!is_array($_us)) {
                return jrProximaCore_http_response(404, 'user not found');
            }

            // Must have a user_email to handle forgot password
            if (!isset($_us['user_email'])) {
                return jrProximaCore_http_response(400, 'user account is missing email address');
            }

            // New Entry
            $key = md5(microtime());
            $tbl = jrCore_db_table_name('jrUser', 'forgot');
            $req = "INSERT INTO {$tbl} (forgot_user_id,forgot_time,forgot_key) VALUES ('{$_us['_user_id']}',UNIX_TIMESTAMP(),'" . jrCore_db_escape($key) . "')";
            $uid = jrCore_db_query($req, 'INSERT_ID');
            if (!$uid || !jrCore_checktype($uid, 'number_nz')) {
                jrCore_logger('CRI', "Unable to save forgot password info for user_id {$_us['_user_id']}");
                return jrProximaCore_http_response(500, 'internal server error');
            }

            // Send out password reset email
            $url = jrCore_get_module_url('jrUser');
            $_rp = array(
                'system_name' => $_conf['jrCore_system_name'],
                'reset_url'   => "{$_conf['jrCore_base_url']}/{$url}/new_password/{$key}"
            );
            list($sub, $msg) = jrCore_parse_email_templates('jrUser', 'forgot', $_rp);
            jrCore_send_email($_us['user_email'], $sub, $msg);
            return jrProximaCore_http_response(200, 'reset password email successfully queued');
            break;

    }
    // Fall through - unknown option
    return jrProximaCore_http_response(400, 'invalid user method');
}

/**
 * GET Method function
 * @param $_post array jrProximaCore post|get|put|delete URL vars
 * @param $_app array Proxima Application
 * @param $_cfg array jrProximaUser module config
 * @param $_vars array Method parameters
 * @return string
 */
function jrProximaUser_px_method_get($_post, $_app, $_cfg, $_vars)
{
    global $_conf;
    if (!isset($_vars['_1']) || !jrCore_checktype($_vars['_1'], 'number_nz')) {
        return jrProximaCore_http_response(400, 'invalid user id');
    }
    // Get Item
    if (!$_rt = jrCore_db_get_item('jrUser', $_vars['_1'], true)) {
        return jrProximaCore_http_response(404);
    }
    // Make sure we have the right app
    if (isset($_conf['jrProximaUser_sso']) && $_conf['jrProximaUser_sso'] == 'off') {
        if (!isset($_rt['_app_id']) || $_rt['_app_id'] != jrProximaCore_get_active_app_id()) {
            // User exists, but we do NOT have SSO turned on - wrong app - not found
            return jrProximaCore_http_response(404);
        }
    }

    $uid = jrProximaCore_get_session_user_id();
    if (jrProximaCore_get_client_access_level() != 'master' && $uid != $_vars['_1']) {
        $acc = jrProximaCore_get_user_access_level($uid, $_rt);
        if ($acc != 'read' && $acc != 'write') {
            return jrProximaCore_http_response(401, 'invalid item owner');
        }
    }

    $pid = (int) $_rt['_profile_id'];
    $_rt = jrProximaCore_get_response_keys('jrUser', $_rt);

    // Are we adding in profile info?
    if (isset($_conf['jrProximaUser_include_profile']) && $_conf['jrProximaUser_include_profile'] == 'on') {
        $_pr = jrCore_db_get_item('jrProfile', $pid, true);
        if ($_pr && is_array($_pr)) {
            foreach ($_pr as $k => $v) {
                if (strpos($k, 'profile_') === 0) {
                    $_rt[$k] = $v;
                }
            }
        }
    }

    return jrProximaCore_http_response(200, null, $_rt);
}

/**
 * PUT Method function
 * @param $_post array jrProximaCore post|get|put|delete URL vars
 * @param $_app array Proxima Application
 * @param $_cfg array jrProximaUser module config
 * @param $_vars array Method parameters
 * @return string
 */
function jrProximaUser_px_method_put($_post, $_app, $_cfg, $_vars)
{
    global $_conf;
    switch ($_post['_1']) {

        // logout: api/user/logout
        case 'logout':

            $app_id = jrProximaCore_get_active_app_id();
            $_user  = array('_user_id' => jrProximaCore_get_session_user_id());
            jrProximaCore_delete_user_session($app_id, $_user);
            return jrProximaCore_http_response(200);
            break;

        default:

            // Params check
            if (!isset($_vars['_1']) || !jrCore_checktype($_vars['_1'], 'number_nz')) {
                return jrProximaCore_http_response(400, 'invalid user id');
            }
            // Get User
            if (!$_rt = jrCore_db_get_item('jrUser', $_vars['_1'], true)) {
                return jrProximaCore_http_response(404);
            }
            // Make sure we have the right app
            if (isset($_conf['jrProximaUser_sso']) && $_conf['jrProximaUser_sso'] == 'off') {
                if (!isset($_rt['_app_id']) || $_rt['_app_id'] != jrProximaCore_get_active_app_id()) {
                    return jrProximaCore_http_response(404);
                }
            }

            // Check ACL
            $uid = jrProximaCore_get_session_user_id();
            if (jrProximaCore_get_client_access_level() != 'master' && $uid != $_vars['_1']) {
                $acc = jrProximaCore_get_user_access_level($uid, $_rt);
                if ($acc != 'write') {
                    return jrProximaCore_http_response(401, 'invalid item owner');
                }
            }

            $_vars = jrProximaCore_clean_method_variables($_vars);
            $_vars = jrProximaCore_add_module_prefix('jrUser', $_vars);
            $_vars = jrProximaCore_run_value_functions('jrUser', $_rt['_item_id'], $_vars);

            jrCore_db_update_item('jrUser', $_rt['_item_id'], $_vars);
            return jrProximaCore_http_response(200);
            break;
    }
}

/**
 * DELETE Method function
 * @param $_post array jrProximaCore post|get|put|delete URL vars
 * @param $_app array Proxima Application
 * @param $_cfg array jrProximaUser module config
 * @param $_vars array Method parameters
 * @return string
 */
function jrProximaUser_px_method_delete($_post, $_app, $_cfg, $_vars)
{
    // Must get user_id
    if (!isset($_vars['_1']) || !jrCore_checktype($_vars['_1'], 'number_nz')) {
        return jrProximaCore_http_response(400, 'invalid user id');
    }
    // Only a user can delete themselves
    if (jrProximaCore_get_session_user_id() != $_post['_1']) {
        return jrProximaCore_http_response(401, 'invalid item owner');
    }

    // Get User
    if (!$_rt = jrCore_db_get_item('jrUser', $_vars['_1'])) {
        return jrProximaCore_http_response(404);
    }
    // Make sure we have the right app
    if (!isset($_rt['_app_id']) || $_rt['_app_id'] != jrProximaCore_get_active_app_id()) {
        return jrProximaCore_http_response(404);
    }

    // Delete User
    if (jrCore_db_delete_item('jrUser', $_vars['_1'])) {
        // Delete Profile
        if (jrProfile_delete_profile($_rt['_profile_id'], true, false)) {
            jrProfile_decrement_quota_profile_count($_rt['profile_quota_id']);
            return jrProximaCore_http_response(200);
        }
    }
    return jrProximaCore_http_response(500);
}
