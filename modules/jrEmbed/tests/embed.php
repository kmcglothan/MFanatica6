<?php
/**
 * Jamroom Editor Embedded Media module
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
 * Cache system unit tests
 */
function test_jrEmbed_embed()
{
    jrUnitTest_init_test('Parse params');
    $tmp = 'module="jrEmbed" mode="place" location="Corner Brook, NL, Canada" maptype="roadmap"';
    $_rs = jrEmbed_get_param_array_from_string($tmp);
    if (!is_array($_rs)) {
        jrUnitTest_exit_with_error('failed to create array');
    }
    if (!isset($_rs['module']) || $_rs['module'] != 'jrEmbed') {
        jrUnitTest_exit_with_error('bad parsed param: module');
    }
    if (!isset($_rs['mode']) || $_rs['mode'] != 'place') {
        jrUnitTest_exit_with_error('bad parsed param: mode');
    }
    if (!isset($_rs['location']) || $_rs['location'] != 'Corner Brook, NL, Canada') {
        jrUnitTest_exit_with_error('bad parsed param: location');
    }
    if (!isset($_rs['maptype']) || $_rs['maptype'] != 'roadmap') {
        jrUnitTest_exit_with_error('bad parsed param: maptype');
    }
    /* different string */
    $tmp = 'module="jrAudio" search="audio_genre = rock"';
    $_rs = jrEmbed_get_param_array_from_string($tmp);
    if (!is_array($_rs)) {
        jrUnitTest_exit_with_error('failed to create array (2)');
    }
    if (!isset($_rs['module']) || $_rs['module'] != 'jrAudio') {
        jrUnitTest_exit_with_error('bad parsed param: module (2)');
    }
    if (!isset($_rs['search']) || $_rs['search'] != 'audio_genre = rock') {
        jrUnitTest_exit_with_error('bad parsed param: search');
    }


}
