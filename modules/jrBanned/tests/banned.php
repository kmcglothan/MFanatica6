<?php
/**
 * Jamroom Banned Items module
 *
 * copyright 2016 The Jamroom Network
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
 * banned items unit tests
 */
function test_jrBanned_banned()
{
    $_words = array(
        'multiple word' => 'in multiple word string',
        'öldü'          => 'part of a öldü string',
        'single'        => 'single',
        'zf'            => 'zf'
    );
    foreach ($_words as $word => $string) {
        jrUnitTest_init_test("banned word: {$word}");
        // Make sure it exists in the DB
        $tbl = jrCore_db_table_name('jrBanned', 'banned');
        $req = "INSERT INTO {$tbl} (ban_updated,ban_type,ban_value) VALUES (UNIX_TIMESTAMP(),'word','" . jrCore_db_escape($word) . "') ON DUPLICATE KEY UPDATE ban_updated = UNIX_TIMESTAMP()";
        jrCore_db_query($req);
        if (!jrCore_is_banned('word', $string)) {
            jrUnitTest_exit_with_error("not banned: {$string}");
        }
    }

    $_email = array(
        '@yahoo.com'         => 'brian@yahoo.com',
        'öldü'               => 'öldü@me.com',
        'brian@whatever.com' => 'brian@whatever.com'
    );
    foreach ($_email as $email => $string) {
        jrUnitTest_init_test("banned email: {$email}");
        // Make sure it exists in the DB
        $tbl = jrCore_db_table_name('jrBanned', 'banned');
        $req = "INSERT INTO {$tbl} (ban_updated,ban_type,ban_value) VALUES (UNIX_TIMESTAMP(),'email','" . jrCore_db_escape($email) . "') ON DUPLICATE KEY UPDATE ban_updated = UNIX_TIMESTAMP()";
        jrCore_db_query($req);
        if (!jrCore_is_banned('email', $string)) {
            jrUnitTest_exit_with_error("not banned: {$string}");
        }
    }
}
