<?php
/**
 * Jamroom System Core module
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
 * lib/datastore.php unit tests
 */
function test_jrCore_datastore()
{
    global $_user;
    $private_profile_id = 10000001;

    // What library files are we testing?
    jrUnitTest_add_coverage_file(APP_DIR . "/modules/jrCore/lib/datastore.php");

    // Make sure user running test does NOT have a private profile
    jrUnitTest_init_test('Privacy Check');
    if (isset($_user['profile_private']) && $_user['profile_private'] != 1) {
        jrUnitTest_exit_with_error('your profile is private - DS unit tests will fail');
    }

    // Get all Datastore modules
    jrUnitTest_init_test('Get All Datastore Modules');
    $_md = jrCore_get_datastore_modules();
    if (!$_md || !is_array($_md)) {
        jrUnitTest_exit_with_error();
    }

    // Check is_datastore_module
    jrUnitTest_init_test('Check if DataStore module');
    if (!jrCore_is_datastore_module('jrUnitTest')) {
        jrUnitTest_exit_with_error();
    }
    jrUnitTest_init_test('Check if not DataStore module');
    if (jrCore_is_datastore_module('jrNotValidModule')) {
        jrUnitTest_exit_with_error();
    }
    jrUnitTest_init_test('Get DataStore plugins');
    jrCore_get_datastore_plugins();

    // Test invalid DS prefix
    jrUnitTest_init_test('Create Datastore - NO PREFIX');
    if (jrCore_db_create_datastore('jrUnitTest', '')) {
        jrUnitTest_exit_with_error();
    }

    // Test creation of datastore
    jrUnitTest_init_test('Create Datastore');
    $tbl = jrCore_db_table_name('jrUnitTest', 'item');
    $req = "DROP TABLE IF EXISTS {$tbl}";
    jrCore_db_query($req);

    $tbl = jrCore_db_table_name('jrUnitTest', 'item_key');
    $req = "DROP TABLE IF EXISTS {$tbl}";
    jrCore_db_query($req);
    if (!jrCore_db_create_datastore('jrUnitTest', 'ut')) {
        jrUnitTest_exit_with_error();
    }

    // Test truncating Datastore
    jrUnitTest_init_test('Truncate Datastore');
    if (!jrCore_db_truncate_datastore('jrUnitTest')) {
        jrUnitTest_exit_with_error();
    }

    // Insert
    jrUnitTest_init_test('Load items into Datastore');
    $_ids = range(1, 20);
    $_lnk = array();
    $rpid = jrUser_get_profile_home_key('_profile_id');
    foreach ($_ids as $num) {
        $mod = ($num % 2);
        $ttl = "Object {$num} Title";
        if ($num == 17) {
            $ttl = "Object {$num} % Title";
        }
        $_dt = array(
            'ut_num'    => $num,
            'ut_title'  => $ttl,
            'ut_title2' => "Object {$num} Title2",
            'ut_string' => "Object {$num} String",
            'ut_number' => intval("{$num}0"),
            'ut_float'  => floatval("{$num}.{$num}"),
            'ut_set'    => $mod
        );
        if ($mod == 1) {
            $_dt['ut_one'] = 1;
        }
        if ($num == 2) {
            $_dt['ut_exists'] = 1;
        }
        if ($num == 3) {
            $_dt['ut_exists'] = 2;
        }
        if ($num == 4) {
            $_dt['ut_increment_key'] = 10;
        }
        if ($num == 5) {
            $_dt['ut_decrement_key'] = 10;
        }
        if ($num > 4 && $num < 10) {
            $_lnk[$num]                 = jrCore_create_unique_string(2200);
            $_dt['ut_has_key']          = 'yes';
            $_dt['ut_delete_key_one']   = 'delete me';
            $_dt['ut_delete_key_two']   = 'delete me';
            $_dt['ut_delete_key_three'] = 'delete me';
            $_dt['ut_long_key']         = $_lnk[$num];
        }
        $_cr = array(
            '_created'    => (time() - $num),
            '_profile_id' => $rpid
        );
        if ($num > 9) {
            $_cr['_profile_id'] = $private_profile_id;
        }
        $uid = jrCore_db_create_item('jrUnitTest', $_dt, $_cr, false);
        if (!$uid) {
            jrUnitTest_exit_with_error();
        }
    }

    // Get number of items in datastore
    jrUnitTest_init_test('Get DataStore Item Count');
    $num = jrCore_db_get_datastore_item_count('jrUnitTest');
    if (!$num || $num !== 20) {
        jrUnitTest_exit_with_error();
    }

    // Update multiple items
    jrUnitTest_init_test('Update Multiple Items (bad data array)');
    $_dt = array();
    foreach ($_ids as $num) {
        $key = $num;
        if ($num == 5) {
            $key = 'bad';
        }
        $_dt[$key] = array(
            'ut_update_key1' => $num,
            'ut_pending'     => 1
        );
    }
    if (jrCore_db_update_multiple_items('jrUnitTest', $_dt)) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Update Multiple Items (bad item_id)');
    if (jrCore_db_update_multiple_items('jrUnitTest', 'bad value')) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Update Multiple Items');
    $_dt = array();
    foreach ($_ids as $num) {
        $_dt[$num] = array(
            'ut_update_key1' => $num,
            'ut_pending'     => 1
        );
    }
    if (!jrCore_db_update_multiple_items('jrUnitTest', $_dt)) {
        jrUnitTest_exit_with_error();
    }

    $tbl = jrCore_db_table_name('jrCore', 'pending');
    $req = "DELETE FROM {$tbl} WHERE pending_module = 'jrUnitTest'";
    jrCore_db_query($req);

    // Run Key functions
    jrUnitTest_init_test('Run Key Function for ut_exists: SUM');
    $sum = jrCore_db_run_key_function('jrUnitTest', 'ut_exists', '*', 'sum');
    if (!$sum || $sum != 3) {
        jrUnitTest_exit_with_error("returned: {$sum}");
    }

    jrUnitTest_init_test('Run Key Function for %: SUM');
    $sum = jrCore_db_run_key_function('jrUnitTest', 'ut_exists', '%', 'sum');
    if (!$sum || $sum != 3) {
        jrUnitTest_exit_with_error("returned: {$sum}");
    }

    jrUnitTest_init_test('Run Key Function for =: SUM');
    $sum = jrCore_db_run_key_function('jrUnitTest', 'ut_exists', '2', 'sum');
    if (!$sum || $sum != 2) {
        jrUnitTest_exit_with_error("returned: {$sum}");
    }

    jrUnitTest_init_test('Run Key Function for ut_exists: AVG');
    $sum = jrCore_db_run_key_function('jrUnitTest', 'ut_exists', '*', 'avg');
    if (!$sum || $sum != 1.5) {
        jrUnitTest_exit_with_error("returned: {$sum}");
    }

    jrUnitTest_init_test('Run Key Function for ut_exists: MIN');
    $sum = jrCore_db_run_key_function('jrUnitTest', 'ut_exists', '*', 'min');
    if (!$sum || $sum != 1) {
        jrUnitTest_exit_with_error("returned: {$sum}");
    }

    jrUnitTest_init_test('Run Key Function for ut_exists: MAX');
    $sum = jrCore_db_run_key_function('jrUnitTest', 'ut_exists', '*', 'max');
    if (!$sum || $sum != 2) {
        jrUnitTest_exit_with_error("returned: {$sum}");
    }

    jrUnitTest_init_test('Run Key Function for ut_exists: STD');
    $sum = jrCore_db_run_key_function('jrUnitTest', 'ut_exists', '*', 'std');
    if (!$sum || $sum != 0.5) {
        jrUnitTest_exit_with_error("returned: {$sum}");
    }

    jrUnitTest_init_test('Run Key Function for ut_num: COUNT');
    $sum = jrCore_db_run_key_function('jrUnitTest', 'ut_num', '*', 'COUNT');
    if (!$sum || $sum != 20) {
        jrUnitTest_exit_with_error("returned: {$sum}");
    }

    jrUnitTest_init_test('Run Key Function for ut_num: UNKNOWN');
    $sum = jrCore_db_run_key_function('jrUnitTest', 'ut_num', '*', 'UNKNOWN');
    if ($sum) {
        jrUnitTest_exit_with_error("returned: {$sum}");
    }

    jrUnitTest_init_test('Set Display Order');
    if (!jrCore_db_set_display_order('jrUnitTest', array_flip($_ids))) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Create Default Key');
    if (!jrCore_db_create_default_key('jrUnitTest', 'ut_default_key', 'this is the default')) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Update Default Key');
    $num = jrCore_db_update_default_key('jrUnitTest', 'ut_default_key', 'this is the NEW default', 'this is the default');
    if (!$num || $num !== 20) {
        jrUnitTest_exit_with_error("returned: {$num}");
    }

    jrUnitTest_init_test('Increment Key');
    jrCore_db_increment_key('jrUnitTest', 4, 'ut_increment_key', 1, true);
    $_rt = jrCore_db_get_item('jrUnitTest', 4, true, true);
    if (!$_rt || !is_array($_rt) || !isset($_rt['ut_increment_key']) || $_rt['ut_increment_key'] != 11) {
        jrUnitTest_exit_with_error("no increment: {$_rt['ut_increment_key']}");
    }

    jrUnitTest_init_test('Increment Key (array)');
    jrCore_db_increment_key('jrUnitTest', array(4), 'ut_increment_key', 1, true);
    $_rt = jrCore_db_get_item('jrUnitTest', 4, true, true);
    if (!$_rt || !is_array($_rt) || !isset($_rt['ut_increment_key']) || $_rt['ut_increment_key'] != 12) {
        jrUnitTest_exit_with_error("no increment: {$_rt['ut_increment_key']}");
    }

    jrUnitTest_init_test('Increment Key (bad value)');
    /** @noinspection PhpParamsInspection */
    if (jrCore_db_decrement_key('jrUnitTest', 4, 'ut_increment_key', 'bad value', 0, true)) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Decrement Key');
    jrCore_db_decrement_key('jrUnitTest', 5, 'ut_decrement_key', 1, 0, true);
    $_rt = jrCore_db_get_item('jrUnitTest', 5, true, true);
    if (!$_rt || !is_array($_rt) || !isset($_rt['ut_decrement_key']) || $_rt['ut_decrement_key'] != 9) {
        jrUnitTest_exit_with_error("no decrement: {$_rt['ut_decrement_key']}");
    }

    jrUnitTest_init_test('Decrement Key (array)');
    jrCore_db_decrement_key('jrUnitTest', array(5), 'ut_decrement_key', 1, 'bad value', true);
    $_rt = jrCore_db_get_item('jrUnitTest', 5, true, true);
    if (!$_rt || !is_array($_rt) || !isset($_rt['ut_decrement_key']) || $_rt['ut_decrement_key'] != 8) {
        jrUnitTest_exit_with_error("no decrement: {$_rt['ut_decrement_key']}");
    }

    jrUnitTest_init_test('Decrement Key (bad value)');
    /** @noinspection PhpParamsInspection */
    if (jrCore_db_decrement_key('jrUnitTest', 5, 'ut_decrement_key', 'bad value', 0, true)) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Get Items missing a Key');
    $_rt = jrCore_db_get_items_missing_key('jrUnitTest', 'ut_has_key');
    if (!$_rt || !is_array($_rt) || count($_rt) !== 15) {
        jrUnitTest_exit_with_error("returned keys: " . count($_rt));
    }

    jrUnitTest_init_test('Get Items missing a Key (no results)');
    $_rt = jrCore_db_get_items_missing_key('jrUnitTest', 'ut_num');
    if ($_rt) {
        jrUnitTest_exit_with_error("returned keys: " . count($_rt));
    }

    jrUnitTest_init_test('Check if Key Exists');
    if (!jrCore_db_item_key_exists('jrUnitTest', 'ut_num')) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Check if Key Exists (does not)');
    if (jrCore_db_item_key_exists('jrUnitTest', 'ut_num_does_not_exist')) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Get all values for a Key');
    $_rt = jrCore_db_get_all_key_values('jrUnitTest', 'ut_has_key');
    if (!$_rt || count($_rt) !== 5) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Get all values for a Key (no match)');
    $_rt = jrCore_db_get_all_key_values('jrUnitTest', 'ut_does_not_have_key');
    if ($_rt || is_array($_rt)) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Get single key for an Item');
    $val = jrCore_db_get_item_key('jrUnitTest', 9, 'ut_has_key');
    if (!$val || $val != 'yes') {
        jrUnitTest_exit_with_error("val: {$val}");
    }

    jrUnitTest_init_test('Get single key for an Item (long key)');
    $val = jrCore_db_get_item_key('jrUnitTest', 9, 'ut_long_key');
    if (!$val || strlen($val) != 2200 || $val != $_lnk[9]) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Get single key for an Item (invalid id)');
    $val = jrCore_db_get_item_key('jrUnitTest', 0, 'ut_has_key');
    if ($val || $val == 'yes') {
        jrUnitTest_exit_with_error("val: {$val}");
    }

    jrUnitTest_init_test('Search Items (invalid params)');
    $_rt = jrCore_db_search_items('jrUnitTest', array());
    if ($_rt || is_array($_rt)) {
        jrUnitTest_exit_with_error();
    }

    // profile_id
    jrUnitTest_init_test('Retrieve 1 item from DS (profile_id equal to)');
    $_sc = array(
        'search'        => array(
            "_profile_id = {$rpid}"
        ),
        'skip_triggers' => true,
        'limit'         => 1
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != '1') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Retrieve 1 item from DS (profile_id in)');
    $_sc = array(
        'search'        => array(
            "_profile_id in {$rpid},10002,10003,10004"
        ),
        'skip_triggers' => true,
        'limit'         => 1
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != '1') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Retrieve 1 item from DS (profile_id not_in)');
    $_sc = array(
        'search'        => array(
            "_profile_id not_in 10001,10002,10003,10004"
        ),
        'skip_triggers' => true,
        'limit'         => 1
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != '1') {
        jrUnitTest_exit_with_error();
    }

    // Logged out profile check
    // Setup $private_profile_id as PRIVATE
    $tbl = jrCore_db_table_name('jrProfile', 'item_key');
    $req = "INSERT IGNORE INTO {$tbl} (`_item_id`,`_profile_id`,`key`,`index`,`value`) VALUES ('{$private_profile_id}', '{$private_profile_id}', 'profile_private', 0, '0')";
    jrCore_db_query($req);
    jrCore_db_delete_private_profile_cache();

    jrUnitTest_set_user_logged_out();

    // profile_id
    jrUnitTest_init_test('Retrieve 1 item from DS (profile_id equal to) - logged out');
    $_sc = array(
        'search'        => array(
            "_profile_id = {$rpid}"
        ),
        'skip_triggers' => true,
        'limit'         => 20
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != 1 || count($_rt['_items']) !== 9) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Retrieve no items from DS (private profile) - logged out');
    $_sc = array(
        'search'        => array(
            "_profile_id = {$private_profile_id}"
        ),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if ($_rt && isset($_rt['_items']) && is_array($_rt['_items'])) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Retrieve all items from DS (private profile) - logged out');
    $_sc = array(
        'search'        => array(
            "_profile_id > 0"
        ),
        'skip_triggers' => true,
        'limit'         => 20
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || count($_rt['_items']) !== 9) {
        jrUnitTest_exit_with_error();
    }

    // Cleanup private profile
    $tbl = jrCore_db_table_name('jrProfile', 'item_key');
    $req = "DELETE FROM {$tbl} WHERE `_item_id` = '{$private_profile_id}'";
    jrCore_db_query($req);
    jrCore_db_delete_private_profile_cache();

    // non-admin
    jrUnitTest_set_user_logged_in();
    jrUnitTest_set_user_group('user');
    jrUnitTest_init_test('Retrieve 1 item from DS (title asc)');
    $_sc = array(
        'order_by'      => array('ut_title' => 'asc'),
        'skip_triggers' => true,
        'limit'         => 1
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != 1) {
        jrUnitTest_exit_with_error();
    }
    jrUnitTest_reset_user_group();

    jrUnitTest_set_user_logged_out();
    jrUnitTest_init_test('Retrieve 1 item from DS (title desc, logged out)');
    $_sc = array(
        'order_by'      => array('ut_title' => 'desc'),
        'skip_triggers' => true,
        'limit'         => 1
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != 9) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Retrieve 5 items from DS (title asc, logged out)');
    $_sc = array(
        'search'        => array('ut_has_key = yes'),
        'order_by'      => array('ut_title' => 'asc'),
        'skip_triggers' => true,
        'limit'         => 5
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || count($_rt['_items']) !== 5) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_set_user_logged_in();

    jrUnitTest_init_test('Retrieve 1 item from DS (number asc)');
    $_sc = array(
        'order_by'      => array('ut_number' => 'numerical_asc'),
        'skip_triggers' => true,
        'limit'         => 1
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != 1) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Retrieve 1 item from DS (number desc)');
    $_sc = array(
        'order_by'      => array('ut_number' => 'numerical_desc'),
        'skip_triggers' => true,
        'limit'         => 1
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != 20) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Retrieve 5 items from DS (number asc)');
    $_sc = array(
        'order_by'      => array('ut_number' => 'numerical_asc'),
        'skip_triggers' => true,
        'limit'         => 5
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != 1 || !isset($_rt['_items'][4]) || $_rt['_items'][4]['_item_id'] != 5) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Retrieve 5 items from DS (number desc)');
    $_sc = array(
        'order_by'      => array('ut_number' => 'numerical_desc'),
        'skip_triggers' => true,
        'limit'         => 5
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != 20 || !isset($_rt['_items'][4]) || $_rt['_items'][4]['_item_id'] != 16) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Retrieve 5 items from DS (float asc)');
    $_sc = array(
        'order_by'      => array('ut_float' => 'numerical_asc'),
        'skip_triggers' => true,
        'limit'         => 5
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != 1 || !isset($_rt['_items'][4]) || $_rt['_items'][4]['_item_id'] != 5) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Retrieve 5 items from DS (float desc)');
    $_sc = array(
        'order_by'      => array('ut_float' => 'numerical_desc'),
        'skip_triggers' => true,
        'limit'         => 5
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != 20 || !isset($_rt['_items'][4]) || $_rt['_items'][4]['_item_id'] != 16) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Retrieve 9 items from DS (_item_id desc)');
    $_sc = array(
        'order_by'      => array('_item_id' => 'desc'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != 20 || !isset($_rt['_items'][8]) || $_rt['_items'][8]['_item_id'] != 12) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Retrieve 9 items from DS (_created desc)');
    $_sc = array(
        'order_by'      => array('_created' => 'desc'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items']) || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != 1 || !isset($_rt['_items'][8]) || $_rt['_items'][8]['_item_id'] != 9) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Group by Set title asc (2 sets)');
    $_sc = array(
        'order_by'      => array('ut_title' => 'asc'),
        'group_by'      => 'ut_set',
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) != 2) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Group by Set title desc (2 sets)');
    $_sc = array(
        'order_by'      => array('ut_title' => 'desc'),
        'group_by'      => 'ut_set',
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) != 2) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search Title wildcard key EQUALS (1 item)');
    $_sc = array(
        'search'        => array('% = Object 5 Title'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || $_rt['_items'][0]['_item_id'] != '5' || count($_rt['_items']) > 1) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search Title partial wildcard key EQUALS (1 item)');
    $_sc = array(
        'search'        => array('ut_% = Object 5 Title'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || $_rt['_items'][0]['_item_id'] != '5' || count($_rt['_items']) > 1) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search Title EQUALS (1 item)');
    $_sc = array(
        'search'        => array('ut_title = Object 5 Title'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || $_rt['_items'][0]['_item_id'] != '5' || count($_rt['_items']) > 1) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search Title EQUALS (duplicate search)');
    $_sc = array(
        'search'        => array(
            'ut_title = Object 5 Title',
            'ut_title = Object 5 Title'
        ),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || $_rt['_items'][0]['_item_id'] != '5' || count($_rt['_items']) > 1) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search Title EQUALS (missing value)');
    $_sc = array(
        'search'        => array(
            'ut_title ='
        ),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if ($_rt) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search Title EQUALS (invalid operator)');
    $_sc = array(
        'search'        => array(
            'ut_title BAD true'
        ),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if ($_rt) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search Title NOT EQUALS (8 items)');
    $_sc = array(
        'search'        => array('ut_title != Object 5 Title'),
        'skip_triggers' => true,
        'limit'         => 50
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 19) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search Title LIKE (1 item)');
    $_sc = array(
        'search'        => array('ut_title like %Object 5 T%'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || $_rt['_items'][0]['_item_id'] != '5' || count($_rt['_items']) > 1) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search Title LIKE with % (1 item)');
    $_sc = array(
        'search'        => array('ut_title like %\%%'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || $_rt['_items'][0]['_item_id'] != '17' || count($_rt['_items']) > 1) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search Title NOT LIKE (19 items)');
    $_sc = array(
        'search'        => array('ut_title not_like %Object 5 T%'),
        'skip_triggers' => true,
        'limit'         => 50
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 19) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search Title NOT LIKE with % (19 items)');
    $_sc = array(
        'search'        => array('ut_title not_like %\%%'),
        'skip_triggers' => true,
        'limit'         => 50
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 19) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search _item_id IN (3 items)');
    $_sc = array(
        'search'        => array('_item_id in 1,5,9'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search _item_id IN order_by _item_id (3 items)');
    $_sc = array(
        'search'        => array('_item_id in 1,5,9'),
        'order_by'      => array('_item_id' => 'asc'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != '1' || $_rt['_items'][2]['_item_id'] != '9') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search _item_id IN order_by _created (3 items)');
    $_sc = array(
        'search'        => array('_item_id in 1,5,9'),
        'order_by'      => array('_item_id' => 'asc'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != '1' || $_rt['_items'][2]['_item_id'] != '9') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search _item_id NOT IN (6 items)');
    $_sc = array(
        'search'        => array('_item_id not_in 1,5,9'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 9) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search _item_id OR EQUALS (3 items)');
    $_sc = array(
        'search'        => array('_item_id = 1 || _item_id = 5 || _item_id = 9'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search _item_id OR WITH IN (3 items)');
    $_sc = array(
        'search'        => array('_item_id = 1 || _item_id in 5,9'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search _item_id OR GREATER THAN (9 items)');
    $_sc = array(
        'search'        => array('_item_id = 1 || _item_id > 5 '),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 9) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search ut_delete_key% OR ut_has_key');
    $_sc = array(
        'search'        => array('ut_delete_key% LIKE %elete% || ut_increment_key > 0'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 6) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search ut_delete_key% OR _item_id');
    $_sc = array(
        'search'        => array('ut_delete_key% LIKE %elete% || _item_id = 3'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 6) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search title with pagebreak 3, page 2 (3 items)');
    $_sc = array(
        'search'        => array('ut_title like %Object%'),
        'order_by'      => array('ut_number' => 'numerical_asc'),
        'pagebreak'     => 3,
        'page'          => 2,
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3 || $_rt['_items'][0]['_item_id'] != '4') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search title with simplepagebreak 3, page 2 (3 items)');
    $_sc = array(
        'search'          => array('ut_title like %Object%'),
        'order_by'        => array('ut_number' => 'numerical_asc'),
        'simplepagebreak' => 3,
        'page'            => 2,
        'skip_triggers'   => true,
        'limit'           => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3 || $_rt['_items'][0]['_item_id'] != '4') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search title with simplepagebreak 3 (no page number) (3 items)');
    $_sc = array(
        'search'          => array('ut_title like %Object%'),
        'order_by'        => array('ut_number' => 'numerical_asc'),
        'simplepagebreak' => 3,
        'skip_triggers'   => true,
        'limit'           => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3 || $_rt['_items'][0]['_item_id'] != '1') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search title with pagebreak 3 (invalid match - no items)');
    $_sc = array(
        'search'        => array('ut_title like %NO MATCH Object%'),
        'order_by'      => array('ut_number' => 'numerical_asc'),
        'pagebreak'     => 3,
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if ($_rt || is_array($_rt)) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search title with limit 3 (3 items)');
    $_sc = array(
        'search'        => array('ut_title like %Object%'),
        'order_by'      => array('ut_number' => 'numerical_asc'),
        'skip_triggers' => true,
        'limit'         => 3
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3 || $_rt['_items'][0]['_item_id'] != '1') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search title with limit 3 (3 items, slow query)');
    $_sc = array(
        'search'          => array('ut_title like %Object%'),
        'order_by'        => array('ut_number' => 'numerical_asc'),
        'skip_triggers'   => true,
        'slow_query_time' => '.00',
        'limit'           => 3
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3 || $_rt['_items'][0]['_item_id'] != '1') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search title with limit 3 (3 items, specific keys)');
    $_sc = array(
        'search'        => array('ut_title like %Object%'),
        'order_by'      => array('ut_number' => 'numerical_asc'),
        'return_keys'   => array('ut_title'),
        'skip_triggers' => true,
        'limit'         => 3
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3 || isset($_rt['_items'][0]['ut_num'])) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Search title with limit 3 (3 items, item id only)');
    $_sc = array(
        'search'              => array('ut_title like %Object%'),
        'order_by'            => array('ut_number' => 'numerical_asc'),
        'skip_triggers'       => true,
        'return_item_id_only' => true,
        'limit'               => 3
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt) || count($_rt) !== 3) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('number LESS THAN (3 items)');
    $_sc = array(
        'search'        => array('ut_number < 40'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3 || $_rt['_items'][2]['_item_id'] != 3) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('number LESS THAN OR EQUAL TO (3 items)');
    $_sc = array(
        'search'        => array('ut_number <= 30'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3 || $_rt['_items'][2]['_item_id'] != 3) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('number LESS THAN OR EQUAL TO FLOAT (3 items)');
    $_sc = array(
        'search'        => array('ut_number <= 30.0', '_profile_id != 689'),
        'skip_triggers' => true,
        'cache_seconds' => 2
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3 || $_rt['_items'][2]['_item_id'] != 3) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('number GREATER THAN (3 items)');
    $_sc = array(
        'search'        => array('ut_number > 60'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 10 || $_rt['_items'][2]['_item_id'] != 9) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('number GREATER THAN OR EQUAL TO (3 items)');
    $_sc = array(
        'search'        => array('ut_number >= 70'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 10 || $_rt['_items'][2]['_item_id'] != 9) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('title REGEXP (3 items)');
    $_sc = array(
        'search'        => array('ut_title regexp Object [1-3]'),
        'skip_triggers' => true,
        'limit'         => 20
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 14) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('TITLE wildcard (3 items)');
    $_sc = array(
        'search'        => array(
            '_item_id > 0 || _created > 0',
            'ut_titl% regexp Object [1-3]'
        ),
        'skip_triggers' => true,
        'limit'         => 20
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 14) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('order by title RANDOM (pass 1)');
    $_sc = array(
        'order_by'       => array('ut_title' => 'random'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 10) {
        jrUnitTest_exit_with_error();
    }
    $pass_one = json_encode($_rt['_items']);

    jrUnitTest_init_test('order by title RANDOM (pass 2)');
    $_sc = array(
        'order_by'       => array('ut_title' => 'random'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 10) {
        jrUnitTest_exit_with_error();
    }
    $pass_two = json_encode($_rt['_items']);
    if ($pass_one == $pass_two) {
        jrUnitTest_exit_with_error('non-random results');
    }
    unset($pass_one, $pass_two);

    jrUnitTest_init_test('key DOES NOT EXIST - NOT EQUAL (4 items)');
    $_sc = array(
        'search'        => array('ut_one != 1'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 10) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('key DOES NOT EXIST - NOT EQUAL (9 items)');
    $_sc = array(
        'search'        => array('ut_non_existing != 1', 'ut_non_existing2 != 2'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 10) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('key DOES NOT EXIST - NOT LIKE (4 items)');
    $_sc = array(
        'search'        => array('ut_one not_like %1%'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 10) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('key DOES NOT EXIST - NOT LIKE (9 items)');
    $_sc = array(
        'search'        => array('ut_non_existing not_like %1%'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 10) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('key DOES NOT EXIST - NOT IN (4 items)');
    $_sc = array(
        'search'        => array('ut_one not_in 1'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 10) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('key DOES NOT EXIST - NOT IN (9 items)');
    $_sc = array(
        'search'        => array('ut_non_existing not_in 1'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 10) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('key OR CONDITION with pagebreak (page 2)');
    $_sc = array(
        'search'        => array('ut_title like %Object% || ut_string like %Object%'),
        'pagebreak'     => 3,
        'page'          => 2,
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || count($_rt['_items']) !== 3 || $_rt['_items'][0]['_item_id'] != '4') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('ORDER BY key that does not exist in all entries WITHOUT SEARCH');
    $_sc = array(
        'order_by'       => array('ut_exists' => 'numerical_desc'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'limit'          => 6
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || count($_rt['_items']) !== 6 || $_rt['_items'][0]['_item_id'] != '3') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('ORDER BY RAND with GROUP BY (pass 1)');
    $_sc = array(
        'order_by'       => array('ut_title' => 'random'),
        'group_by'       => 'ut_number',
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 5
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || count($_rt['_items']) !== 5) {
        jrUnitTest_exit_with_error();
    }
    $one = $_rt['_items'][0]['ut_title'];
    $two = $_rt['_items'][4]['ut_title'];

    jrUnitTest_init_test('ORDER BY RAND with GROUP BY (pass 2)');
    $_sc = array(
        'order_by'       => array('ut_title' => 'random'),
        'group_by'       => 'ut_number',
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 5
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || count($_rt['_items']) !== 5) {
        jrUnitTest_exit_with_error();
    }
    if ($_rt['_items'][0]['ut_title'] == $one && $_rt['_items'][4]['ut_title'] == $two) {
        jrUnitTest_exit_with_error('results not random');
    }

    jrUnitTest_init_test('GROUP BY with UNIQUE');
    $_sc = array(
        'order_by'       => array('_item_id' => 'asc'),
        'group_by'       => 'ut_one UNIQUE',
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 1
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != 19) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('key OR condition on USER keys only');
    $_sc = array(
        'search'         => array(
            "ut_num = 1 || ut_num = 2",
            "user_email = {$_user['user_email']} || user_name like %{$_user['user_name']}%"
        ),
        'order_by'       => array('_item_id' => 'asc'),
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 10
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || count($_rt['_items']) !== 2) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('key OR condition on PROFILE keys only');
    $pnm = jrUser_get_profile_home_key('profile_name');
    $pnu = jrUser_get_profile_home_key('profile_url');
    $_sc = array(
        'search'         => array(
            "ut_num = 2 || ut_num = 5",
            "profile_name = {$pnm} || profile_url = {$pnu}",
        ),
        'order_by'       => array('_item_id' => 'asc'),
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 10
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || count($_rt['_items']) !== 2) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('key OR condition on USER and PROFILE keys');
    $_sc = array(
        'search'         => array(
            "ut_num = 1 || ut_num = 2",
            "profile_active = 1",
            "user_email = {$_user['user_email']} || user_name like %{$_user['user_name']}%"
        ),
        'order_by'       => array('_item_id' => 'asc'),
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 10
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || count($_rt['_items']) !== 2) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('ORDER BY key not in all items NUMERICAL_DESC (ut_exists)');
    $_sc = array(
        'order_by'       => array('ut_exists' => 'numerical_desc'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 20
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][1]) || $_rt['_items'][1]['ut_exists'] != '1') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('ORDER BY key not in all items NUMERICAL_ASC (ut_exists)');
    $_sc = array(
        'order_by'       => array('ut_exists' => 'numerical_asc'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 20
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][19]) || $_rt['_items'][19]['ut_exists'] != '2') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('ORDER BY _item_id us IN clause order - IDS specified');
    $_sc = array(
        'search'         => array(
            '_item_id in 1,9,7,5,3'
        ),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 3
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][2]) || $_rt['_items'][2]['_item_id'] != '7') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('ORDER BY _item_id using DESC - IDS specified');
    $_sc = array(
        'search'         => array(
            '_item_id in 1,9,7,5,3'
        ),
        'order_by'       => array('_item_id' => 'desc'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 10
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][4]) || $_rt['_items'][4]['_item_id'] != '1') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('ORDER BY _item_id using ASC - IDS specified');
    $_sc = array(
        'search'         => array(
            '_item_id in 1,9,7,5,3'
        ),
        'order_by'       => array('_item_id' => 'asc'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 10
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][4]) || $_rt['_items'][4]['_item_id'] != '9') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Both equal and NOT equal');
    $_sc = array(
        'search'         => array(
            '_item_id = 1',
            '_item_id != 1',
        ),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 1
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if ($_rt || is_array($_rt) || isset($_rt['_items'])) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Both equal and NOT IN');
    $_sc = array(
        'search'         => array(
            '_item_id = 1',
            '_item_id not_in 1,2',
        ),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 1
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if ($_rt || is_array($_rt) || isset($_rt['_items'])) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Delete multiple keys from an Item');
    jrCore_db_delete_multiple_item_keys('jrUnitTest', 5, array('ut_delete_key_one', 'ut_delete_key_two', '_remove'));
    $_rt = jrCore_db_get_item('jrUnitTest', 5, true, true);
    if (isset($_rt['ut_delete_key_one']) || isset($_rt['ut_delete_key_two'])) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Delete multiple keys from an Item (invalid array)');
    if (jrCore_db_delete_multiple_item_keys('jrUnitTest', 5, array())) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Delete multiple keys from an Item (invalid array contents)');
    if (jrCore_db_delete_multiple_item_keys('jrUnitTest', 5, array('_removed'))) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Delete single Key from Item');
    jrCore_db_delete_item_key('jrUnitTest', 6, 'ut_delete_key_one');
    $_rt = jrCore_db_get_item('jrUnitTest', 6, true, true);
    if (isset($_rt['ut_delete_key_one'])) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Delete single Key from Multiple Items');
    jrCore_db_delete_key_from_multiple_items('jrUnitTest', array(7, 8, 9), 'ut_delete_key_one');
    foreach (array(7, 8, 9) as $id) {
        $_rt = jrCore_db_get_item('jrUnitTest', $id, true, true);
        if (isset($_rt['ut_delete_key_one'])) {
            jrUnitTest_exit_with_error();
        }
    }

    jrUnitTest_init_test('Delete Multiple Key from Multiple Items');
    jrCore_db_delete_key_from_multiple_items('jrUnitTest', array(7, 8, 9), array('ut_delete_key_two', 'ut_delete_key_three'));
    foreach (array(7, 8, 9) as $id) {
        $_rt = jrCore_db_get_item('jrUnitTest', $id, true, true);
        if (isset($_rt['ut_delete_key_two']) || isset($_rt['ut_delete_key_three'])) {
            jrUnitTest_exit_with_error();
        }
    }

    jrUnitTest_init_test('Delete Key from All Items');
    jrCore_db_delete_key_from_all_items('jrUnitTest', 'ut_num');
    $_ky = jrCore_db_get_all_key_values('jrUnitTest', 'ut_num');
    if ($_ky) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Get Allowed Keys');
    if (jrCore_db_get_allowed_item_keys('jrUnitTest', array())) {
        jrUnitTest_exit_with_error();
    }

    $pid = jrUser_get_profile_home_key('_profile_id');
    jrUnitTest_init_test('Update Profile Item Count');
    if (!jrCore_db_update_profile_item_count('jrUnitTest', $pid)) {
        jrUnitTest_exit_with_error();
    }
    // Did it update correctly?
    $val = jrCore_db_get_item_key('jrProfile', $pid, 'profile_jrUnitTest_item_count');
    if (!$val || $val != 9) {
        jrUnitTest_exit_with_error('incorrect profile count key');
    }
    jrCore_db_delete_item_key('jrProfile', $pid, 'profile_jrUnitTest_item_count', false, false);

    $uid = (int) $_user['_user_id'];
    jrUnitTest_init_test('Update User Item Count');
    if (!jrCore_db_update_user_item_count('jrUnitTest', $pid, $uid)) {
        jrUnitTest_exit_with_error();
    }
    // Did it update correctly?
    $val = jrCore_db_get_item_key('jrUser', $uid, 'user_jrUnitTest_item_count');
    if (!$val || $val != 20) {
        jrUnitTest_exit_with_error('incorrect user count key');
    }
    jrCore_db_delete_item_key('jrUser', $uid, 'user_jrUnitTest_item_count', false, false);

    // Truncating Datastore
    jrUnitTest_init_test('Truncate Datastore');
    if (!jrCore_db_truncate_datastore('jrUnitTest')) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Create Multiple Items');
    $_dt = array();
    $_cr = array();
    foreach (range(1, 9) as $num) {
        $_dt[] = array(
            'ut_num'    => $num,
            'ut_title'  => "Object {$num} Title",
            'ut_title2' => "Object {$num} Title2",
            'ut_string' => "Object {$num} String",
            'ut_number' => intval("{$num}0"),
            'ut_float'  => floatval("{$num}.{$num}"),
            'ut_set'    => ($num % 2)
        );
        $_cr[] = array(
            '_created' => (time() - $num)
        );
    }
    $_id = jrCore_db_create_multiple_items('jrUnitTest', $_dt, $_cr, true);
    if (!$_id || !is_array($_id) || count($_id) !== 9) {
        jrUnitTest_exit_with_error();
    }

    // Delete multiple items
    jrUnitTest_init_test('Delete Multiple Items (bad item_id array)');
    if (jrCore_db_delete_multiple_items('jrUnitTest', 'bad value', false, false)) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Delete Multiple Items');
    if (!jrCore_db_delete_multiple_items('jrUnitTest', $_id)) {
        jrUnitTest_exit_with_error();
    }

}
