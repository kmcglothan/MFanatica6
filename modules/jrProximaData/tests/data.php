<?php
/**
 * Jamroom 5 Proxima Data module
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
 * Proxima Data unit tests
 */
function test_jrProximaData_data()
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

    // Create user and get active session
    jrCore_set_setting_value('jrProximaUser', 'require_fields', 'none');
    jrCore_delete_config_cache();

    jrUnitTest_init_test('POST Create User - ID only');
    $_dt = array(
        'password' => 'abc123'
    );
    $_rs = $prx->Post('user', $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 201) {
        jrUnitTest_exit_with_error();
    }
    $oid = $_rs['data']['_id'];

    // Create a new Proxima Data collection
    $wcl = 'w' . mt_rand(0, 999999);
    jrUnitTest_init_test("Create new collection: {$wcl}");
    $cid = jrProximaData_create_collection($_ap['app_id'], $wcl, 1, 'off');
    if (!$cid || !jrCore_checktype($cid, 'number_nz')) {
        jrUnitTest_exit_with_error();
    }

    $str = jrCore_create_unique_string(1000);
    $_dt = array(
        'image1'  => '@' . realpath(APP_DIR . "/modules/jrProximaData/tests/test.png"),
        'image2'  => '@' . realpath(APP_DIR . "/modules/jrProximaData/tests/test.png"),
        'newkey1' => 1,
        'newkey2' => 'this is a text string',
        'newkey3' => $str,
        1         => 'the number one as a key'
    );

    jrUnitTest_init_test('POST Create Item - no collection');
    $_rs = $prx->Post("data", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 400) {
        jrUnitTest_exit_with_error();
    }

    // Insert item
    jrUnitTest_init_test('POST Create Item');
    $_rs = $prx->Post("data/{$wcl}", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 201) {
        jrUnitTest_exit_with_error();
    }
    $did = (int) $_rs['data']['_id'];

    jrUnitTest_init_test('PUT Item - __increment value function');
    $_dt = array(
        'newkey1' => '__increment(1)'
    );
    $_rs = $prx->Put("data/{$wcl}/{$did}", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('GET Item - with new value for newkey1');
    $_rs = $prx->Get("data/{$wcl}/{$did}");
    if (!isset($_rs['data']['newkey1']) || $_rs['data']['newkey1'] != 2) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('PUT Item - __increment value function');
    $_dt = array(
        'newkey1' => '__increment(10)'
    );
    $_rs = $prx->Put("data/{$wcl}/{$did}", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('GET Item - with new value for newkey1');
    $_rs = $prx->Get("data/{$wcl}/{$did}");
    if (!isset($_rs['data']['newkey1']) || $_rs['data']['newkey1'] != 12) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('PUT Item - __decrement value function');
    $_dt = array(
        'newkey1' => '__decrement(1)'
    );
    $_rs = $prx->Put("data/{$wcl}/{$did}", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }
    jrUnitTest_init_test('GET Item - with new value for newkey1');
    $_rs = $prx->Get("data/{$wcl}/{$did}");
    if (!isset($_rs['data']['newkey1']) || $_rs['data']['newkey1'] != 11) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('PUT Item - __delete_key value function');
    $_dt = array(
        'newkey1' => '__delete_key()'
    );
    $_rs = $prx->Put("data/{$wcl}/{$did}", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('GET Item - with deleted newkey1');
    $_rs = $prx->Get("data/{$wcl}/{$did}");
    if (isset($_rs['data']['newkey1'])) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('DELETE Delete Item');
    $_rs = $prx->Delete("data/{$wcl}/{$did}");
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    // Delete our collection
    jrUnitTest_init_test("Delete collection: {$wcl}");
    jrProximaData_delete_collection($cid);

    // Create a new Proxima READ ONLY Data collection
    $wcl = 'w' . mt_rand(0, 999999);
    jrUnitTest_init_test("Create new read only collection: {$wcl}");
    $cid = jrProximaData_create_collection($_ap['app_id'], $wcl, 1, 'on');
    if (!$cid || !jrCore_checktype($cid, 'number_nz')) {
        jrUnitTest_exit_with_error();
    }

    // Insert item into READ ONLY
    jrUnitTest_init_test('POST Create Item - read only collection');
    $_rs = $prx->Post("data/{$wcl}", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 401) {
        jrUnitTest_exit_with_error();
    }

    // Delete our read only collection
    jrUnitTest_init_test("Delete read only collection: {$wcl}");
    jrProximaData_delete_collection($cid);

    // Create a new Proxima GLOBAL READ Data collection
    // 1 = Owner read and write (default)
    // 2 = Global read only
    // 3 = Global read and write
    $wcl = 'w' . mt_rand(0, 999999);
    jrUnitTest_init_test("Create new global read collection: {$wcl}");
    $cid = jrProximaData_create_collection($_ap['app_id'], $wcl, 2, 'off');
    if (!$cid || !jrCore_checktype($cid, 'number_nz')) {
        jrUnitTest_exit_with_error();
    }

    // Insert item into GLOBAL READ collection
    jrUnitTest_init_test('POST Create Item - global read collection');
    $_rs = $prx->Post("data/{$wcl}", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 201) {
        jrUnitTest_exit_with_error();
    }
    $iid = (int) $_rs['data']['_id'];

    // We have created the item - create 2nd user for access
    jrUnitTest_init_test('POST Create User - ID only');
    $_dt = array(
        'password' => 'abc123'
    );
    $_rs = $prx->Post('user', $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 201) {
        jrUnitTest_exit_with_error();
    }

    // Get item - should be readable
    jrUnitTest_init_test('GET Item - global read collection');
    $_rs = $prx->Get("data/{$wcl}/{$iid}");
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    // Update item - should be read only
    jrUnitTest_init_test('PUT Item - global read collection');
    $_dt = array(
        'newkey3' => 'this should not work'
    );
    $_rs = $prx->Put("data/{$wcl}/{$iid}", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 401) {
        jrUnitTest_exit_with_error();
    }

    // Log out user
    jrUnitTest_init_test('PUT User - logout');
    $_rs = $prx->Put("user/logout");
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    // Log in original user
    jrUnitTest_init_test('POST User - login with password');
    $_dt = array(
        'id'      => $oid,
        'password'=> 'abc123'
    );
    $_rs = $prx->Post("user/login", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    // Update item - should be read only
    jrUnitTest_init_test('PUT Item - global read collection');
    $_dt = array(
        'newkey5' => 'this SHOULD work'
    );
    $_rs = $prx->Put("data/{$wcl}/{$iid}", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }

    // TEST SEARCH
    // Create items to test search
    jrUnitTest_init_test('POST Create 100 Items - search test');
    $i = 0;
    while ($i < 100) {
        $_dt = array(
            'newkey1' => $i,
            'newkey2' => 'this is a text string',
            'newkey3' => ($i % 10),
            $i         => "the number ${i} as a key"
        );
        $_rs = $prx->Post("data/{$wcl}", $_dt);
        if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 201) {
            jrUnitTest_exit_with_error();
        }
        $i++;
    }

    // Search Items
    jrUnitTest_init_test('GET Search Items - single item');
    $_dt = array(
        'search' => 'newkey5 like %SHOULD%'
    );
    $_rs = $prx->Get("data/{$wcl}/search", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }
    if (!isset($_rs['data']['_items'][0]['_id']) || $_rs['data']['_items'][0]['_id'] != $iid) {
        jrUnitTest_exit_with_error();
    }
    if (!isset($_rs['data']['_items'][0]['newkey5'])) {
        jrUnitTest_exit_with_error();
    }
    if (count($_rs['data']['_items']) > 1) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('GET Search Items - 10 items ordered');
    $_dt = array(
        'search'   => 'newkey3 eq 5',
        'order_by' => '_id asc'
    );
    $_rs = $prx->Get("data/{$wcl}/search", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 200) {
        jrUnitTest_exit_with_error();
    }
    if (count($_rs['data']['_items']) != 10) {
        jrUnitTest_exit_with_error('wrong item count');
    }
    if ($_rs['data']['_items'][0]['_id'] > $_rs['data']['_items'][9]['_id']) {
        jrUnitTest_exit_with_error('invalid order_by');
    }

    // Delete our global read collection
    jrUnitTest_init_test("Delete global read collection: {$wcl}");
    jrProximaData_delete_collection($cid);

    jrCore_set_setting_value('jrProximaUser', 'require_fields', $ini);
    jrCore_delete_config_cache();
}
