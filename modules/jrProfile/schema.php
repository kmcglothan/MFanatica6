<?php
/**
 * Jamroom Profiles module
 *
 * copyright 2018 The Jamroom Network
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
 * jrProfile_db_schema
 */
function jrProfile_db_schema()
{
    // This module uses a Data Store - create it.  The Data store holds
    // all information (key value pairs) about profiles
    jrCore_db_create_datastore('jrProfile', 'profile');

    // Profile Quota
    $_tmp = array(
        "quota_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "quota_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "quota_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'"
    );
    jrCore_db_verify_table('jrProfile', 'quota', $_tmp, 'InnoDB');

    // Profile Quota Setting
    $_tmp = array(
        "`module` VARCHAR(64) NOT NULL DEFAULT ''",
        "`name` VARCHAR(64) NOT NULL DEFAULT ''",
        "`created` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "`default` VARCHAR(4096) NOT NULL DEFAULT ''",
        "`type` VARCHAR(32) NOT NULL DEFAULT 'text'",
        "`validate` VARCHAR(32) NOT NULL DEFAULT ''",
        "`required` VARCHAR(8) NOT NULL DEFAULT 'off'",
        "`min` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "`max` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "`options` VARCHAR(8192) NOT NULL DEFAULT ''",
        "`label` VARCHAR(64) NOT NULL DEFAULT ''",
        "`sublabel` VARCHAR(128) NOT NULL DEFAULT ''",
        "`help` VARCHAR(4096) NOT NULL DEFAULT ''",
        "`section` VARCHAR(64) NOT NULL DEFAULT ''",
        "`order` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1'",
        "PRIMARY KEY (`module`,`name`)",
        "INDEX `name` (`name`)",
        "INDEX `order` (`order`)"
    );
    jrCore_db_verify_table('jrProfile', 'quota_setting', $_tmp, 'InnoDB');

    // Profile Quota Value
    $_tmp = array(
        "`quota_id` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "`module` VARCHAR(64) NOT NULL DEFAULT ''",
        "`name` VARCHAR(64) NOT NULL DEFAULT ''",
        "`updated` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "`value` VARCHAR(8196) NOT NULL DEFAULT ''",
        "`user` VARCHAR(128) NOT NULL DEFAULT ''",
        "PRIMARY KEY (`quota_id`,`module`,`name`)"
    );
    jrCore_db_verify_table('jrProfile', 'quota_value', $_tmp, 'InnoDB');

    // Profile -> User ID Link
    $_tmp = array(
        "user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "profile_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "PRIMARY KEY (user_id, profile_id)",
        "INDEX profile_id (profile_id)"
    );
    jrCore_db_verify_table('jrProfile', 'profile_link', $_tmp, 'InnoDB');

    // Profile Pulse
    $_tmp = array(
        "pulse_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "pulse_profile_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "pulse_module VARCHAR(64) NOT NULL DEFAULT ''",
        "pulse_key VARCHAR(64) NOT NULL DEFAULT ''",
        "pulse_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "pulse_count INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "UNIQUE pulse_unique (pulse_profile_id, pulse_module, pulse_key)",
        "INDEX pulse_key (pulse_key)"
    );
    jrCore_db_verify_table('jrProfile', 'pulse', $_tmp, 'InnoDB');

    // Fix up bad "required" column
    $tbl = jrCore_db_table_name('jrProfile', 'quota_setting');
    $req = "ALTER TABLE {$tbl} CHANGE `required` `required` VARCHAR(8) NOT NULL DEFAULT 'off'";
    jrCore_db_query($req);

    // Cleanup profile_privacy from Form Designer
    jrCore_delete_designer_form_field('jrProfile', 'settings', 'profile_private');

    return true;
}
