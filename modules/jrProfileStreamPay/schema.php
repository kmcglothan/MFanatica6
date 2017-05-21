<?php
/**
 * Jamroom Stream Pay module
 *
 * copyright 2017 The Jamroom Network
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
 * @copyright 2015 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * db_schema
 */
function jrProfileStreamPay_db_schema()
{
    // Credit Log
    $_tmp = array(
        "log_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "log_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "log_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "log_user_ip VARCHAR(45) NOT NULL DEFAULT ''",
        "log_module VARCHAR(64) NOT NULL DEFAULT ''",
        "log_item_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "log_item_name VARCHAR(128) NOT NULL DEFAULT ''",
        "log_amount VARCHAR(16) NOT NULL DEFAULT ''"
    );
    jrCore_db_verify_table('jrProfileStreamPay', 'log', $_tmp, 'MyISAM');
    return true;
}
