<?php
/**
 * Jamroom Users module
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
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * session unit tests
 */
function test_jrUser_session()
{
    global $_user;

    // What library file are we testing?
    jrUnitTest_add_coverage_file(APP_DIR . "/modules/jrUser/include.php");

    jrUnitTest_init_test('check non existing session');
    if (jrUser_session_is_valid_session('badsid')) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('get online user count');
    $cnt = jrUser_session_online_user_count();
    if (!$cnt || $cnt == 0) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('get online user ids');
    $uid = (int) $_user['_user_id'];
    $_id = jrUser_session_online_user_ids();
    if (!$_id || !is_array($_id) || !isset($_id[$uid])) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('get online user info');
    $uid = (int) $_user['_user_id'];
    $_in = jrUser_session_online_user_info();
    if (!$_in || !is_array($_in)) {
        jrUnitTest_exit_with_error();
    }
    $found = false;
    foreach ($_in as $_u) {
        if (isset($_u['session_user_id']) && $_u['session_user_id'] == $_user['_user_id']) {
            $found = true;
            break;
        }
    }
    if (!$found) {
        jrUnitTest_exit_with_error(2);
    }

}
