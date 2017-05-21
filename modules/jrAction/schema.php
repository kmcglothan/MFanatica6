<?php
/**
 * Jamroom Timeline module
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * db_schema
 */
function jrAction_db_schema()
{
    jrCore_db_create_datastore('jrAction', 'action');

    // Keep track of hash tags for trending
    $_tmp = array(
        "hash_profile_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "hash_text VARCHAR(32) NOT NULL DEFAULT ''",
        "hash_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "UNIQUE hash_unique (hash_profile_id, hash_text, hash_time)",
        "INDEX hash_text (hash_text)",
        "INDEX hash_time (hash_time)"
    );
    jrCore_db_verify_table('jrAction', 'hash', $_tmp, 'InnoDB');

    // Keep track of Shares
    $_tmp = array(
        "share_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "share_module VARCHAR(64) NOT NULL DEFAULT ''",
        "share_item_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "share_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "share_action_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "UNIQUE share_unique (share_user_id, share_module, share_item_id)",
        "INDEX share_module (share_module)",
        "INDEX share_item_id (share_item_id)"
    );
    jrCore_db_verify_table('jrAction', 'share', $_tmp, 'InnoDB');

    return true;
}
