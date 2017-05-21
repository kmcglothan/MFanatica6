<?php
/**
 * Jamroom Merchandise module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
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
 * @author Michael Ussher <michael [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrStore_db_schema
 */
function jrStore_db_schema()
{
    // This module uses a Data Store - create it.  The Data store holds
    // all information (key value pairs) about the products.
    jrCore_db_create_datastore('jrStore', 'product');

    // Comments on Purchases
    $_tmp = array(
        "comment_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "comment_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "comment_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "comment_seller_profile_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "comment_txn_id  INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "comment_text TEXT NOT NULL",
        "comment_ip VARCHAR(45) NOT NULL DEFAULT ''",
        "comment_url VARCHAR(255) NOT NULL DEFAULT ''",
    );
    jrCore_db_verify_table('jrStore', 'comment', $_tmp);

    //purchase status.
    $_tmp = array(
        "status_txn_id INT(11) UNSIGNED NOT NULL DEFAULT '0' ",
        "status_seller_profile_id VARCHAR(40) NOT NULL DEFAULT ''",
        "status_status VARCHAR(40) NOT NULL DEFAULT ''",
        "UNIQUE status_unique (status_txn_id,status_seller_profile_id)",
        "INDEX status_seller_profile_id (status_seller_profile_id)"
    );
    jrCore_db_verify_table('jrStore', 'status', $_tmp);

    return true;
}
