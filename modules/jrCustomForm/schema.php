<?php
/**
 * Jamroom Simple Custom Forms module
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
 * @copyright 2012 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * db_schema
 */
function jrCustomForm_db_schema()
{
    jrCore_db_create_datastore('jrCustomForm', 'form');

    // Custom forms
    $_tmp = array(
        "form_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "form_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "form_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "form_name VARCHAR(64) NOT NULL DEFAULT ''",
        "form_title VARCHAR(128) NOT NULL DEFAULT ''",
        "form_message VARCHAR(4096) NOT NULL DEFAULT ''",
        "form_unique CHAR(3) NOT NULL DEFAULT 'off'",
        "form_login CHAR(3) NOT NULL DEFAULT 'off'",
        "form_notify VARCHAR(32) NOT NULL DEFAULT 'master_email'",
        "form_responses INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "INDEX form_updated (form_updated)",
        "INDEX form_name (form_name)"
    );
    jrCore_db_verify_table('jrCustomForm', 'form', $_tmp, 'InnoDB');

    return true;
}
