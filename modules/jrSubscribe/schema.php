<?php
/**
 * Jamroom Subscriptions module
 *
 * copyright 2018 The Jamroom Network
 *
 * This Jamroom file is LICENSED SOFTWARE, and cannot be redistributed.
 *
 * This Source Code is subject to the terms of the Jamroom Network
 * Commercial License -  please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
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
 * db_schema
 */
function jrSubscribe_db_schema()
{
    jrCore_db_create_datastore('jrSubscribe', 'sub');

    // Subscription
    $_tmp = array(
        "sub_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "sub_profile_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "sub_plan_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "sub_status VARCHAR(32) NOT NULL DEFAULT ''",
        "sub_plugin VARCHAR(32) NOT NULL DEFAULT ''",
        "sub_amount INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "sub_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "sub_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "sub_expires INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "sub_manual TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "sub_data VARCHAR(2048) NOT NULL DEFAULT ''",
        "sub_note TEXT NOT NULL",
        "UNIQUE sub_profile_id (sub_profile_id)",
        "INDEX sub_plan_id (sub_plan_id)",
        "INDEX sub_status (sub_status)",
        "INDEX sub_expires (sub_expires)"
    );
    jrCore_db_verify_table('jrSubscribe', 'subscription', $_tmp, 'InnoDB');
    return true;
}
