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
 * @copyright 2012 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Proxima User unit tests
 */
function test_jrProximaUser_user()
{
    global $_conf;

    // Bring in PHP Client
    jrUnitTest_init_test('Include PHP Client');
    if (!is_file(APP_DIR . "/clients/php/Proxima.php")) {
        jrUnitTest_exit_with_error();
    }
    require_once APP_DIR . "/clients/php/Proxima.php";

    // Get APP INFO - first configured app
    jrUnitTest_init_test('Get Active App for testing');
    $tbl = jrCore_db_table_name('jrProximaCore', 'app');
    $req = "SELECT * FROM {$tbl} WHERE app_active = 'on' ORDER BY app_id ASC LIMIT 1";
    $_ap = jrCore_db_query($req, 'SINGLE');
    if (!$_ap || !is_array($_ap)) {
        jrUnitTest_exit_with_error();
    }

    $ini = $_conf['jrProximaUser_require_fields'];
    $prx = new Proxima($_ap['app_client_key']);
    $prx->SetProximaUrl("{$_conf['jrCore_base_url']}/api");
    $prx->DeleteSessionID();

    // Create a user - should fail without password
    jrUnitTest_init_test('POST Create User - missing password');
    $_dt = array();
    $_rs = $prx->Post('user', $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 400) {
        jrUnitTest_exit_with_error();
    }

    // Create a user (id only) - should succeed
    jrCore_set_setting_value('jrProximaUser', 'require_fields', 'none');
    jrCore_delete_config_cache();
    jrUnitTest_init_test('POST Create User - ID only');
    $_dt = array(
        'password'  => 'abc123',
        'avatar'    => '@' . APP_DIR . '/modules/jrProximaUser/tests/test.png',
        'UPPERCASE' => 'uppercase'
    );
    $_rs = $prx->Post('user', $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 201) {
        jrUnitTest_exit_with_error();
    }
    $cid = $_rs['data']['_id'];

    // Get user data - check for avatar
    jrUnitTest_init_test('GET User - should include avatar');
    $_rs = $prx->Get("user/{$cid}");
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }
    if (!isset($_rs['data']['_files']['avatar'])) {
        jrUnitTest_exit_with_error('missing avatar');
    }
    if (!isset($_rs['data']['UPPERCASE'])) {
        jrUnitTest_exit_with_error('missing uppercase');
    }
    // Make sure we don't have any bad keys in our output
    $_keys = array('_1', '_2', '_3', '_4', '_5', '_uri', 'user_module', 'user_option', 'user__1', 'user__uri', 'password', 'user_password');
    foreach ($_keys as $k) {
        if (isset($_rs['data'][$k])) {
            jrUnitTest_exit_with_error("found bad key: {$k}");
        }
    }

    jrUnitTest_init_test('DELETE User - valid session (1)');
    $_rs = $prx->Delete("user/{$cid}");
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    // Create a user missing username
    jrCore_set_setting_value('jrProximaUser', 'require_fields', 'user');
    jrCore_delete_config_cache();
    jrUnitTest_init_test('POST Create User - missing user_name');
    $_dt = array(
        'password' => 'abc123'
    );
    $_rs = $prx->Post('user', $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 400) {
        jrUnitTest_exit_with_error();
    }

    // Create a user (username only) - should succeed
    $uid = 'test' . mt_rand(0, 999999);
    jrUnitTest_init_test('POST Create User - user_name only');
    $_dt = array(
        'user_name' => $uid,
        'password'  => 'abc123'
    );
    $_rs = $prx->Post('user', $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 201) {
        jrUnitTest_exit_with_error();
    }
    $cid = $_rs['data']['_id'];

    // Create a user (username only) - should fail - already exists
    jrUnitTest_init_test('POST Create User - user_name already exists');
    $_dt = array(
        'user_name' => $uid,
        'password'  => 'abc123'
    );
    $_rs = $prx->Post('user', $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 400) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('DELETE User - valid session (2)');
    $_rs = $prx->Delete("user/{$cid}");
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    // Create a user - email
    jrCore_set_setting_value('jrProximaUser', 'require_fields', 'email');
    jrCore_delete_config_cache();
    jrUnitTest_init_test('POST Create User - missing user_email');
    $_dt = array(
        'password' => 'abc123'
    );
    $_rs = $prx->Post('user', $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 400) {
        jrUnitTest_exit_with_error();
    }

    // Create a user (email only) - should succeed
    $uid = 'test' . mt_rand(0, 999999) . '@jamroom.net';
    jrUnitTest_init_test('POST Create User - user_email only');
    $_dt = array(
        'user_email' => $uid,
        'password'   => 'abc123'
    );
    $_rs = $prx->Post('user', $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 201) {
        jrUnitTest_exit_with_error();
    }
    $cid = $_rs['data']['_id'];

    // Create a user (username only) - should fail - already exists
    jrUnitTest_init_test('POST Create User - user_email already exists');
    $_dt = array(
        'user_email' => $uid,
        'password'   => 'abc123'
    );
    $_rs = $prx->Post('user', $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 400) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('DELETE User - valid session (3)');
    $_rs = $prx->Delete("user/{$cid}");
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    // Create a user - both email and username
    jrCore_set_setting_value('jrProximaUser', 'require_fields', 'both');
    jrCore_delete_config_cache();
    $uid = 'test' . mt_rand(0, 999999);
    $eml = "{$uid}@jamroom.net";

    jrUnitTest_init_test('POST Create User - missing both user_email and user_name');
    $_dt = array(
        'password' => 'abc123'
    );
    $_rs = $prx->Post('user', $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 400) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('POST Create User - missing user_email');
    $_dt = array(
        'user_name' => $uid,
        'password'  => 'abc123'
    );
    $_rs = $prx->Post('user', $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 400) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('POST Create User - missing user_name');
    $_dt = array(
        'user_email' => $eml,
        'password'   => 'abc123'
    );
    $_rs = $prx->Post('user', $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 400) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('POST Create User - both user_email and user_name');
    $_dt = array(
        'user_name'  => $uid,
        'user_email' => $eml,
        'password'   => 'abc123',
        'avatar'     => '@' . APP_DIR . '/modules/jrProximaUser/tests/test.png'
    );
    $_rs = $prx->Post('user', $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 201) {
        jrUnitTest_exit_with_error();
    }
    $cid = (int) $_rs['data']['_id'];

    jrUnitTest_init_test('POST Create User - user_name already exists');
    $_dt = array(
        'user_name'  => $uid,
        'user_email' => str_replace('test', 'tst', $eml),
        'password'   => 'abc123'
    );
    $_rs = $prx->Post('user', $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 400) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('POST Create User - user_email already exists');
    $_dt = array(
        'user_name'  => str_replace('test', 'tst', $uid),
        'user_email' => $eml,
        'password'   => 'abc123'
    );
    $_rs = $prx->Post('user', $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 400) {
        jrUnitTest_exit_with_error();
    }

    $sid = $prx->GetSessionID();
    $prx->SetSessionID('');
    jrUnitTest_init_test('GET User - invalid session');
    $_rs = $prx->Get("user/{$cid}");
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 401) {
        jrUnitTest_exit_with_error();
    }

    $prx->SetSessionID($sid);
    jrUnitTest_init_test('GET User - valid session');
    $_rs = $prx->Get("user/{$cid}");
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }
    if (!isset($_rs['data']['_files']['avatar']) || !is_array($_rs['data']['_files']['avatar'])) {
        jrUnitTest_exit_with_error('missing avatar');
    }

    // Add second image
    $_dt = array(
        'avatar2' => '@' . APP_DIR . '/modules/jrProximaUser/tests/test.png'
    );
    jrUnitTest_init_test('PUT User - add second image');
    $_rs = $prx->Put("user/{$cid}", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('GET User - valid session');
    $_rs = $prx->Get("user/{$cid}");
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }
    if (!isset($_rs['data']['_files']['avatar2']) || !is_array($_rs['data']['_files']['avatar2'])) {
        jrUnitTest_exit_with_error('missing avatar2');
    }

    jrUnitTest_init_test('PUT User - logout');
    $_rs = $prx->Put("user/logout");
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('GET User - session is no longer valid');
    $_rs = $prx->Get("user/{$cid}");
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 401) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('POST User - login with missing password');
    $_dt = array(
        'id' => $cid
    );
    $_rs = $prx->Post("user/login", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 400) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('POST User - login with invalid password');
    $_dt = array(
        'id'      => $cid,
        'password'=> 'abc1234'
    );
    $_rs = $prx->Post("user/login", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 400) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('POST User - login with password');
    $_dt = array(
        'id'      => $cid,
        'password'=> 'abc123'
    );
    $_rs = $prx->Post("user/login", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }
    $sid = $_rs['data']['session_id'];

    $prx->SetSessionID('');
    jrUnitTest_init_test('PUT User - invalid session');
    $_dt = array(
        'newkey' => 1
    );
    $_rs = $prx->Put("user/{$cid}", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 401) {
        jrUnitTest_exit_with_error();
    }

    $prx->SetSessionID($sid);
    jrUnitTest_init_test('PUT User - valid session');
    $str = jrCore_create_unique_string(1000);
    $_dt = array(
        'newkey1' => 1,
        'newkey2' => 'this is a text string',
        'newkey3' => $str,
        1         => 'the number one as a key'
    );
    $_rs = $prx->Put("user/{$cid}", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('GET User - valid session with new key');
    $_rs = $prx->Get("user/{$cid}");
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }
    if (!isset($_rs['data']['newkey1']) || $_rs['data']['newkey1'] != 1) {
        jrUnitTest_exit_with_error('newkey1');
    }
    if (!isset($_rs['data']['newkey2'])) {
        jrUnitTest_exit_with_error('newkey2');
    }
    if (!isset($_rs['data']['newkey3']) || strlen($_rs['data']['newkey3']) !== 1000 || $_rs['data']['newkey3'] != $str) {
        jrUnitTest_exit_with_error('newkey3');
    }

    jrUnitTest_init_test('PUT User - __increment value function');
    $_dt = array(
        'newkey1' => '__increment(1)'
    );
    $_rs = $prx->Put("user/{$cid}", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('GET User - valid session with new value for newkey1');
    $_rs = $prx->Get("user/{$cid}");
    if (!isset($_rs['data']['newkey1']) || $_rs['data']['newkey1'] != 2) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('PUT User - __decrement value function');
    $_dt = array(
        'newkey1' => '__decrement(1)'
    );
    $_rs = $prx->Put("user/{$cid}", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('GET User - valid session with new value for newkey1');
    $_rs = $prx->Get("user/{$cid}");
    if (!isset($_rs['data']['newkey1']) || $_rs['data']['newkey1'] != 1) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('PUT User - __delete_key value function');
    $_dt = array(
        'newkey1' => '__delete_key()'
    );
    $_rs = $prx->Put("user/{$cid}", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('GET User - valid session missing newkey1');
    $_rs = $prx->Get("user/{$cid}");
    if (isset($_rs['data']['newkey1'])) {
        jrUnitTest_exit_with_error();
    }

    $prx->SetAppKey('bad');
    jrUnitTest_init_test('GET User - wrong app_id');
    $_rs = $prx->Get("user/{$cid}");
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 404) {
        jrUnitTest_exit_with_error();
    }
    $prx->SetAppKey($_ap['app_client_key']);

    jrUnitTest_init_test('POST User - forgot login');
    $_dt = array(
        'id' => $cid
    );
    $_rs = $prx->Post("user/forgot", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    $prx->SetSessionID('');
    jrUnitTest_init_test('DELETE User - invalid session');
    $_rs = $prx->Delete("user/{$cid}");
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 401) {
        jrUnitTest_exit_with_error();
    }

    $prx->SetSessionID($sid);
    jrUnitTest_init_test('DELETE User - valid session (4)');
    $_rs = $prx->Delete("user/{$cid}");
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('GET User - user has been deleted');
    $_rs = $prx->Get("user/{$cid}");
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 404) {
        jrUnitTest_exit_with_error();
    }

    jrCore_set_setting_value('jrProximaUser', 'require_fields', $ini);
    jrCore_delete_config_cache();
}
