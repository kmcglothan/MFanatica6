<?php
/**
 * Jamroom Proxima Bridge module
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
 * @copyright 2016 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Proxima Bridge unit tests
 */
function test_jrProximaBridge_bridge()
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

    jrUnitTest_init_test('POST User Item - not allowed');
    $_dt = array(
        'password' => 'abc123'
    );
    $_rs = $prx->Put("bridge/user", $_dt);
    if (!$_rs || !isset($_rs['code']) || $_rs['code'] != 400) {
        jrUnitTest_exit_with_error();
    }

    jrCore_set_setting_value('jrProximaUser', 'require_fields', $ini);
    jrCore_delete_config_cache();
}
