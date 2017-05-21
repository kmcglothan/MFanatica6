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
 * lib/util.php unit tests
 */
function test_jrCore_util()
{
    // What library file are we testing?
    jrUnitTest_add_coverage_file(APP_DIR . "/modules/jrCore/lib/util.php");

    //--------------------------
    // UTILS
    //--------------------------
    jrUnitTest_init_test('Strip non UTF8');
    $raw_utf8 = file_get_contents(APP_DIR . '/modules/jrCore/tests/util_utf8_test_file.txt');
    $stripped = jrCore_strip_emoji($raw_utf8);

    // Make sure we can store $stripped in the DB
    if (!jrCore_set_temp_value('jrCore', 'utf8_unit_test', $stripped)) {
        jrUnitTest_exit_with_error('error storing stripped utf8 in db');
    }

    // Replace emoji and compare
    $tmp = jrCore_get_temp_value('jrCore', 'utf8_unit_test');
    $tmp = jrCore_replace_emoji($tmp);
    if ($tmp !== $raw_utf8) {
        jrUnitTest_exit_with_error('value with replaced emoji does not match');
    }

    //--------------------------
    // PARSE_URL
    //--------------------------
    $_req = $_REQUEST;

    jrUnitTest_init_test('Parse URL (no URL)');
    $_tmp = jrCore_parse_url(null, false);
    if (!$_tmp || $_tmp['module'] != 'jrUnitTest' || $_tmp['option'] != 'run_save') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Parse URL (cached)');
    $_tmp = jrCore_parse_url();
    if (!$_tmp || $_tmp['module'] != 'jrUnitTest' || $_tmp['option'] != 'run_save') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Parse URL (normal URL)');
    $_REQUEST = array();
    $_tmp     = jrCore_parse_url('core/skin_admin/language/skin2/p=3', false);
    if (!$_tmp || $_tmp['module'] != 'jrCore' || $_tmp['option'] != 'skin_admin') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Parse URL (try to change module)');
    $_REQUEST = array();
    $_tmp     = jrCore_parse_url('core/skin_admin/language/module=jrUser', false);
    if (!$_tmp || $_tmp['module'] != 'jrCore' || $_tmp['option'] != 'skin_admin') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Parse URL (try to change option)');
    $_REQUEST = array();
    $_tmp     = jrCore_parse_url('core/skin_admin/language/option=admin', false);
    if (!$_tmp || $_tmp['module'] != 'jrCore' || $_tmp['option'] != 'skin_admin') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Parse URL (multiple slashes)');
    $_REQUEST = array();
    $_tmp     = jrCore_parse_url('core//skin_admin///language/skin2/p=3', false);
    if (!$_tmp || $_tmp['module'] != 'jrCore' || $_tmp['option'] != 'skin_admin') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Parse URL (leading slash)');
    $_REQUEST = array();
    $_tmp     = jrCore_parse_url('/core//skin_admin///language/p=3', false);
    if (!$_tmp || $_tmp['module'] != 'jrCore' || $_tmp['option'] != 'skin_admin') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Parse URL (single quote in URL)');
    $_REQUEST = array();
    $_tmp     = jrCore_parse_url("core/skin_admin/language/skin2');alert('1=jrElastic2", false);
    if (!$_tmp || $_tmp['module'] != 'jrCore' || $_tmp['option'] != 'skin_admin') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Parse URL (parens in URL)');
    $_REQUEST = array();
    $_tmp     = jrCore_parse_url("core/skin_admin/language/skin2);alert(1=jrElastic2", false);
    if (!$_tmp || $_tmp['module'] != 'jrCore' || $_tmp['option'] != 'skin_admin') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Parse URL (semi-colon in URL)');
    $_REQUEST = array();
    $_tmp     = jrCore_parse_url("core/skin_admin/language/skin2;alert(1=jrElastic2)", false);
    if (!$_tmp || $_tmp['module'] != 'jrCore' || $_tmp['option'] != 'skin_admin') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Parse URL (array in params)');
    $_REQUEST = array();
    $_tmp     = jrCore_parse_url("core/skin_admin/language/opt[]=1/opt[]=2", false);
    if (!$_tmp || $_tmp['module'] != 'jrCore' || $_tmp['option'] != 'skin_admin' || !is_array($_tmp['opt']) || count($_tmp['opt']) != 2) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Parse URL (urlencoded array in params)');
    $_REQUEST = array();
    $_tmp     = jrCore_parse_url("core/skin_admin/language/opt%5B%5D=1/opt%5B%5D=2", false);
    if (!$_tmp || $_tmp['module'] != 'jrCore' || $_tmp['option'] != 'skin_admin' || !is_array($_tmp['opt']) || count($_tmp['opt']) != 2) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Parse URL (no option)');
    $_REQUEST = array();
    $_tmp     = jrCore_parse_url('core', false);
    if (!$_tmp || $_tmp['module'] != 'jrCore' || isset($_tmp['option'])) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Parse URL (GET params)');
    $_REQUEST = array();
    $_tmp     = jrCore_parse_url('core/skin_admin?param1=yes&param2=no', false);
    if (!$_tmp || $_tmp['module'] != 'jrCore' || $_tmp['option'] != 'skin_admin' || !isset($_tmp['param1']) || $_tmp['param1'] != 'yes' || !isset($_tmp['param2']) || $_tmp['param2'] != 'no') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Parse URL (GET params 2)');
    $_REQUEST = array();
    $_tmp     = jrCore_parse_url('core/skin_admin/param1=yes/param2=no', false);
    if (!$_tmp || $_tmp['module'] != 'jrCore' || $_tmp['option'] != 'skin_admin' || !isset($_tmp['param1']) || $_tmp['param1'] != 'yes' || !isset($_tmp['param2']) || $_tmp['param2'] != 'no') {
        jrUnitTest_exit_with_error();
    }
    $_REQUEST = $_req;

    //--------------------------
    // COOKIE
    //--------------------------
    jrUnitTest_init_test('Set Cookie');
    if (!jrCore_set_cookie('unittest', 'test cookie')) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Get Cookie just set');
    $val = jrCore_get_cookie('unittest');
    if ($val != 'test cookie') {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Delete Cookie just set');
    if (!jrCore_delete_cookie('unittest')) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Get Cookie just deleted (should fail)');
    $val = jrCore_get_cookie('unittest');
    if ($val) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Set Cookie (invalid name)');
    if (jrCore_set_cookie('this%will?break', 'test cookie')) {
        jrUnitTest_exit_with_error();
    }

    //--------------------------
    // MODULE FEATURES
    //--------------------------
    jrUnitTest_init_test('Register Module Feature');
    if (!jrCore_register_module_feature('jrUnitTest', 'unit_test_feature', 'jrCore', 'unique_setting', array('this is an option'))) {
        jrUnitTest_exit_with_error();
    }

    jrUnitTest_init_test('Get Registered Module Features');
    $_tm = jrCore_get_registered_module_features('jrUnitTest', 'unit_test_feature');
    if (!$_tm || !is_array($_tm) || !isset($_tm['jrCore']) || !isset($_tm['jrCore']['unique_setting'])) {
        jrUnitTest_exit_with_error();
    }

    //--------------------------
    // URL TESTS
    //--------------------------

    // jrCore_get_current_url()
    $_urls = array(
        '/one=one'                         => '/one=one',
        '?one=one'                         => '?one=one',
        '/one=one/two=two'                 => '/one=one/two=two',
        '/one=one/two);alert(1=jrElastic2"' => '/one=one/two)'
    );
    foreach ($_urls as $url => $back) {
        jrUnitTest_init_test('Get Current URL: ' . $url);
        $tmp = jrUnitTest_load_function_by_url($url, 'jrCore_unit_test_get_current_url');
        if ($tmp != $back) {
            jrUnitTest_exit_with_error("failed: {$url}");
        }
    }

    // jrCore_url_string()
    $_urls = array(
        'test_one'          => 'test-one',
        'test-TWO'          => 'test-two',
        'will%it%break'     => 'willitbreak',
        'öldü'              => 'oldu',
        'test%25test'       => 'testtest',
        'test test'         => 'test-test',
        'ﬁ'                 => 'fi',
        'testing İ and ı'   => 'testing-i-and-i',
        'Do you like 合気道' => 'do-you-like-%E5%90%88%E6%B0%97%E9%81%93',
        '合気道-20'          => '%E5%90%88%E6%B0%97%E9%81%93-20'
    );
    foreach ($_urls as $url => $good) {
        jrUnitTest_init_test('URL String: ' . $url);
        $new = jrCore_url_string($url);
        if ($new != $good) {
            jrUnitTest_exit_with_error("failed: {$new}");
        }
    }

}

/**
 * Get current URL in a unit test
 * @return string
 */
function jrCore_unit_test_get_current_url()
{
    global $_conf;
    $tmp = jrCore_get_module_url('jrUnitTest');
    $url = jrCore_get_current_url();
    return str_replace("{$_conf['jrCore_base_url']}/{$tmp}/load_function_by_url", '', $url);
}
