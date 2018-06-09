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
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * lib/cache.php unit tests
 */
function test_jrCore_cache()
{
    global $_conf;

    // What library file are we testing?
    jrUnitTest_add_coverage_file(APP_DIR . "/modules/jrCore/lib/cache.php");

    // Make sure we enable caching here for our test
    $dev = false;
    if (jrCore_is_developer_mode()) {
        $_conf['jrDeveloper_developer_mode'] = 'off';
        $dev                                 = true;
    }

    $reset = false;
    if (isset($_conf['jrCore_default_cache_seconds']) && $_conf['jrCore_default_cache_seconds'] == '0') {
        $_conf['jrCore_default_cache_seconds'] = 10;
        $reset                                 = true;
    }

    $text = 'This is some text to be cached';
    $ckey = md5(microtime());

    jrUnitTest_init_test('Set a TEMP Value');
    if (!jrCore_set_temp_value('jrUnitTest', $ckey, $text)) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Get a TEMP Value');
    if (!$tmp = jrCore_get_temp_value('jrUnitTest', $ckey)) {
        jrUnitTest_exit_with_error();
    }
    elseif ($tmp != $text) {
        jrUnitTest_exit_with_error(2);
    }

    jrUnitTest_init_test('Update a TEMP Value (does not exist)');
    if (jrCore_update_temp_value('jrUnitTest', $ckey . 1, "{$text} modified")) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Update a TEMP Value');
    if (!jrCore_update_temp_value('jrUnitTest', $ckey, "{$text} modified")) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Get Updated TEMP Value');
    if (!$tmp = jrCore_get_temp_value('jrUnitTest', $ckey)) {
        jrUnitTest_exit_with_error();
    }
    elseif ($tmp != "{$text} modified") {
        jrUnitTest_exit_with_error(2);
    }

    jrUnitTest_init_test('Delete TEMP Value (does not exist)');
    if (jrCore_delete_temp_value('jrUnitTest', $ckey . 1)) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Delete TEMP Value');
    if (!jrCore_delete_temp_value('jrUnitTest', $ckey)) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Get Deleted TEMP Value');
    if ($tmp = jrCore_get_temp_value('jrUnitTest', $ckey)) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Set a TEMP Value');
    if (!jrCore_set_temp_value('jrUnitTest', $ckey, $text)) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Clean All TEMP Values');
    $tmp = jrCore_clean_temp('jrUnitTest', 0);
    if ($tmp != 1) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Clean All TEMP Values (bad seconds)');
    if (jrCore_clean_temp('jrUnitTest', 'bad')) {
        jrUnitTest_exit_with_error();
    }

    // Save a TEXT item
    jrUnitTest_init_test('Save text to cache');
    if (!jrCore_add_to_cache('jrCore', $ckey, $text)) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Retrieve text from cache');
    if (!$tmp = jrCore_is_cached('jrCore', $ckey)) {
        jrUnitTest_exit_with_error();
    }
    elseif ($tmp != $text) {
        jrUnitTest_exit_with_error(2);
    }

    jrUnitTest_init_test('Delete text from cache');
    jrCore_delete_cache('jrCore', $ckey);

    jrUnitTest_init_test('Retrieve text from cache (should not exist)');
    if ($tmp = jrCore_is_cached('jrCore', $ckey)) {
        jrUnitTest_exit_with_error();
    }

    // Save a NUMBER
    jrUnitTest_init_test('Save number to cache');
    if (!jrCore_add_to_cache('jrCore', $ckey, 500)) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Retrieve number from cache');
    if (!$tmp = jrCore_is_cached('jrCore', $ckey)) {
        jrUnitTest_exit_with_error();
    }
    elseif ($tmp != 500) {
        jrUnitTest_exit_with_error(2);
    }

    jrUnitTest_init_test('Delete number from cache');
    jrCore_delete_cache('jrCore', $ckey);

    jrUnitTest_init_test('Retrieve number from cache (should not exist)');
    if ($tmp = jrCore_is_cached('jrCore', $ckey)) {
        jrUnitTest_exit_with_error();
    }

    // Save an array
    jrUnitTest_init_test('Save array to cache');
    $_arr = array('key1' => 'value1', 'key2' => 'value2');
    if (!jrCore_add_to_cache('jrCore', $ckey, $_arr)) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Retrieve array from cache');
    if (!$tmp = jrCore_is_cached('jrCore', $ckey)) {
        jrUnitTest_exit_with_error();
    }
    elseif (!is_array($tmp) || !isset($tmp['key1'])) {
        jrUnitTest_exit_with_error(2);
    }

    jrUnitTest_init_test('Delete array from cache');
    jrCore_delete_cache('jrCore', $ckey);

    jrUnitTest_init_test('Retrieve array from cache (should not exist)');
    if ($tmp = jrCore_is_cached('jrCore', $ckey)) {
        jrUnitTest_exit_with_error();
    }

    // Reset caches
    jrCore_delete_all_cache_entries();

    // Multiple Test
    $num = 100;
    jrUnitTest_init_test("Create {$num} cache entries");
    for ($i = 0; $i < $num; $i++) {
        jrCore_add_to_cache('jrCore', "jrCore-{$i}-1", "test {$i}");
    }

    jrUnitTest_init_test("Get {$num} cache entries");
    for ($i = 0; $i < $num; $i++) {
        if (!jrCore_is_cached('jrCore', "jrCore-${i}-1")) {
            jrUnitTest_exit_with_error("{$i} is NOT cached");
        }
    }

    jrUnitTest_init_test("Delete {$num} cache entries");
    $_it = array();
    for ($i = 0; $i < $num; $i++) {
        $_it[] = array('jrCore', "jrCore-{$i}-1", true);
    }
    jrCore_delete_multiple_cache_entries($_it);

    jrUnitTest_init_test('Get cache entries (should not exist)');
    for ($i = 0; $i < $num; $i++) {
        if (jrCore_is_cached('jrCore', "jrCore-${i}-1")) {
            jrUnitTest_exit_with_error();
        }
    }

    jrUnitTest_init_test('Delete core and config cache');
    jrCore_delete_config_cache();
    jrCore_delete_all_cache_entries();

    if ($dev) {
        $_conf['jrDeveloper_developer_mode'] = 'on';
    }
    if ($reset) {
        $_conf['jrCore_default_cache_seconds'] = 0;
    }

}
