<?php
/**
 * Jamroom Like It module
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
function jrLike_db_schema()
{
    // Update any like_user_id values that are IP addresses
    // We are changing to INT for performance reasons
    if (jrCore_db_table_exists('jrLike', 'likes')) {
        $tbl = jrCore_db_table_name('jrLike', 'likes');
        $req = "UPDATE {$tbl} SET like_user_id = INET_ATON(like_user_id) WHERE like_user_id LIKE '%.%'";
        $cnt = jrCore_db_query($req, 'COUNT');
        if ($cnt > 0) {
            jrCore_logger('INF', "converted " . jrCore_number_format($cnt) . " like entries to correct IP address format");
        }
    }

    // Likes
    $_tmp = array(
        "like_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "like_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "like_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "like_item_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "like_module VARCHAR(64) NOT NULL DEFAULT ''",
        "like_action VARCHAR(8) NOT NULL DEFAULT ''",
        "UNIQUE like_unique (like_user_id, like_item_id, like_module)",
        "INDEX like_action (like_action)",
        "INDEX like_item_id (like_item_id)",
        "INDEX like_module (like_module)"
    );
    jrCore_db_verify_table('jrLike', 'likes', $_tmp, 'InnoDB');

    return true;
}
