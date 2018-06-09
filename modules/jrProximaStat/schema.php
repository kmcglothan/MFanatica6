<?php
/**
 * Jamroom 5 Proxima Stats module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
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
function jrProximaStat_db_schema()
{
    // Stat Insert
    $_tmp = array(
        "s_sid BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
        "s_ins INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "s_mod VARCHAR(32) NOT NULL DEFAULT ''",
        "s_key VARCHAR(8) NOT NULL DEFAULT ''",
        "s_mtd TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "s_val FLOAT(6,3) NOT NULL",
        "INDEX s_ins (s_ins)"
    );
    jrCore_db_verify_table('jrProximaStat', 'stat_insert', $_tmp, 'MyISAM');

    // Stat History
    $_tmp = array(
        "stat_date VARCHAR(16) NOT NULL DEFAULT ''",
        "stat_mod VARCHAR(32) NOT NULL DEFAULT ''",
        "stat_key VARCHAR(8) NOT NULL DEFAULT ''",
        "stat_mtd TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "stat_cnt INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "stat_min FLOAT(6,3) NOT NULL",
        "stat_avg FLOAT(6,3) NOT NULL",
        "stat_max FLOAT(6,3) NOT NULL",
        "UNIQUE stat_unique (stat_date, stat_mod, stat_key, stat_mtd)"
    );
    jrCore_db_verify_table('jrProximaStat', 'stat_history', $_tmp, 'MyISAM');
}
