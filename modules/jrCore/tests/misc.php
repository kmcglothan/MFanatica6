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
 * lib/misc.php Unit tests
 */
function test_jrCore_misc()
{
    // What library file are we testing?
    jrUnitTest_add_coverage_file(APP_DIR . "/modules/jrCore/lib/misc.php");

    $_ch = array(
        'http://test.com'                 => '<a href="http://test.com" target="_blank" rel="nofollow">http://test.com</a>',
        'http://test.com/'                => '<a href="http://test.com/" target="_blank" rel="nofollow">http://test.com/</a>',
        'https://test.com'                => '<a href="https://test.com" target="_blank" rel="nofollow">https://test.com</a>',
        'https://test.com/'               => '<a href="https://test.com/" target="_blank" rel="nofollow">https://test.com/</a>',
        'https://test.com?one=two'        => '<a href="https://test.com?one=two" target="_blank" rel="nofollow">https://test.com?one=two</a>',
        'https://test.com?one=two&tmp=1'  => '<a href="https://test.com?one=two&tmp=1" target="_blank" rel="nofollow">https://test.com?one=two&tmp=1</a>',
        'yes https://test.com'            => 'yes <a href="https://test.com" target="_blank" rel="nofollow">https://test.com</a>',
        'https://test.com yes'            => '<a href="https://test.com" target="_blank" rel="nofollow">https://test.com</a> yes',
        '<a href="https://test.com"> yes' => '<a href="https://test.com"> yes'
    );

    $num = 1;
    foreach ($_ch as $string => $result) {
        jrUnitTest_init_test('jrCore_string_to_url test ' . $num);
        $tmp = jrCore_string_to_url($string);
        if ($tmp != $result) {
            jrUnitTest_exit_with_error("incorrect URL result");
        }
        $num++;
    }

}
