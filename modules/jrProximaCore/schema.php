<?php
/**
 * Jamroom Proxima Core module
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
 * @copyright 2014 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * db_schema
 */
function jrProximaCore_db_schema()
{
    // App
    $_tmp = array(
        "app_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "app_name VARCHAR(128) NOT NULL DEFAULT ''",
        "app_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "app_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "app_client_key VARCHAR(32) NOT NULL DEFAULT ''",
        "app_master_key VARCHAR(32) NOT NULL DEFAULT ''",
        "app_active CHAR(3) NOT NULL DEFAULT 'on'",
        "INDEX app_client_key (app_client_key)",
        "INDEX app_master_key (app_master_key)",
    );
    jrCore_db_verify_table('jrProximaCore', 'app', $_tmp);

    // Session
    $_tmp = array(
        "session_key VARCHAR(32) NOT NULL PRIMARY KEY",
        "session_app_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "session_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "session_profile_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "session_expires INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "UNIQUE session_unique (session_app_id, session_user_id)"
    );
    jrCore_db_verify_table('jrProximaCore', 'session', $_tmp);
    return true;
}
