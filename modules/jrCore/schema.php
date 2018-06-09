<?php
/**
 * Jamroom System Core module
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * db_schema
 */
function jrCore_db_schema()
{
    // Logs
    $_tmp = array(
        "log_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "log_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "log_priority CHAR(3) CHARACTER SET latin1 NOT NULL DEFAULT 'inf'",
        "log_ip VARCHAR(45) CHARACTER SET latin1 NOT NULL DEFAULT ''",
        "log_text VARCHAR(4096) NOT NULL DEFAULT ''",
        "INDEX log_created (log_created)",
        "INDEX log_priority (log_priority)",
        "INDEX log_ip (log_ip)"
    );
    jrCore_db_verify_table('jrCore', 'log', $_tmp, 'InnoDB');

    // Log Debug
    $_tmp = array(
        "log_log_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "log_url VARCHAR(255) NOT NULL DEFAULT ''",
        "log_memory BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
        "log_data MEDIUMTEXT NOT NULL",
        "UNIQUE log_log_id (log_log_id)"
    );
    jrCore_db_verify_table('jrCore', 'log_debug', $_tmp, 'InnoDB');
    jrCore_db_change_table_engine('jrCore', 'log_debug', 'InnoDB');

    // Settings
    $_tmp = array(
        "`module` VARCHAR(64) NOT NULL DEFAULT ''",
        "`name` VARCHAR(64) NOT NULL DEFAULT ''",
        "`created` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "`updated` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "`value` MEDIUMTEXT NOT NULL",
        "`default` MEDIUMTEXT NOT NULL",
        "`type` VARCHAR(32) NOT NULL DEFAULT 'text'",
        "`validate` VARCHAR(32) NOT NULL DEFAULT ''",
        "`required` VARCHAR(8) NOT NULL DEFAULT 'off'",
        "`min` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "`max` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "`options` MEDIUMTEXT NOT NULL",
        "`user` VARCHAR(128) NOT NULL DEFAULT ''",
        "`label` VARCHAR(64) NOT NULL DEFAULT ''",
        "`sublabel` VARCHAR(256) NOT NULL DEFAULT ''",
        "`help` VARCHAR(4096) NOT NULL DEFAULT ''",
        "`section` VARCHAR(64) NOT NULL DEFAULT ''",
        "`order` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1'",
        "PRIMARY KEY (`module`, `name`)",
        "INDEX `type` (`type`)",
        "INDEX `order` (`order`)"
    );
    jrCore_db_verify_table('jrCore', 'setting', $_tmp, 'InnoDB');

    // Modules
    $_tmp = array(
        "module_id MEDIUMINT(7) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "module_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "module_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "module_priority TINYINT(3) UNSIGNED NOT NULL DEFAULT '100'",
        "module_directory VARCHAR(64) NOT NULL DEFAULT ''",
        "module_url VARCHAR(64) NOT NULL DEFAULT ''",
        "module_version VARCHAR(12) NOT NULL DEFAULT ''",
        "module_name VARCHAR(255) NOT NULL DEFAULT ''",
        "module_prefix VARCHAR(32) NOT NULL DEFAULT ''",
        "module_description VARCHAR(1024) NOT NULL DEFAULT ''",
        "module_category VARCHAR(64) NOT NULL DEFAULT ''",
        "module_developer VARCHAR(128) NOT NULL DEFAULT ''",
        "module_active TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "module_locked TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "module_requires VARCHAR(512) NOT NULL DEFAULT ''",
        "module_system_id TINYINT(3) UNSIGNED NOT NULL DEFAULT '0'",
        "module_license VARCHAR(32) NOT NULL DEFAULT ''",
        "INDEX module_priority (module_priority)",
        "INDEX module_directory (module_directory)",
        "INDEX module_url (module_url)",
        "INDEX module_category (module_category)",
        "INDEX module_active (module_active)"
    );
    jrCore_db_verify_table('jrCore', 'module', $_tmp, 'InnoDB');

    // Skins
    $_tmp = array(
        "skin_directory VARCHAR(128) NOT NULL DEFAULT ''",
        "skin_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "skin_custom_css MEDIUMTEXT NOT NULL",
        "skin_custom_image MEDIUMTEXT NOT NULL",
        "skin_system_id TINYINT(3) UNSIGNED NOT NULL DEFAULT '0'",
        "skin_license VARCHAR(32) NOT NULL DEFAULT ''",
        "skin_cloned_from VARCHAR(128) NOT NULL DEFAULT ''",
        "PRIMARY KEY (skin_directory)"
    );
    jrCore_db_verify_table('jrCore', 'skin', $_tmp, 'InnoDB');

    // Forms
    $_tmp = array(
        "`module` VARCHAR(64) NOT NULL DEFAULT ''",
        "`view` VARCHAR(64) NOT NULL DEFAULT ''",
        "`name` VARCHAR(64) NOT NULL DEFAULT ''",
        "`active` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "`locked` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "`created` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "`updated` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "`default` VARCHAR(4096) NOT NULL DEFAULT ''",
        "`type` VARCHAR(32) NOT NULL DEFAULT 'text'",
        "`validate` VARCHAR(32) NOT NULL DEFAULT ''",
        "`required` TINYINT(1) NOT NULL DEFAULT '0'",
        "`min` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "`max` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "`options` VARCHAR(8192) NOT NULL DEFAULT ''",
        "`user` VARCHAR(128) NOT NULL DEFAULT ''",
        "`group` VARCHAR(128) NOT NULL DEFAULT 'user'",
        "`label` VARCHAR(64) NOT NULL DEFAULT ''",
        "`sublabel` VARCHAR(128) NOT NULL DEFAULT ''",
        "`help` VARCHAR(4096) NOT NULL DEFAULT ''",
        "`section` VARCHAR(64) NOT NULL DEFAULT ''",
        "`order` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1'",
        "PRIMARY KEY (`module`,`view`,`name`)",
        "INDEX `order` (`order`)"
    );
    jrCore_db_verify_table('jrCore', 'form', $_tmp, 'InnoDB');

    // Menu
    $_tmp = array(
        "menu_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "menu_module VARCHAR(64) NOT NULL DEFAULT 'jrCore'",
        "menu_unique VARCHAR(64) NOT NULL DEFAULT ''",
        "menu_active VARCHAR(3) NOT NULL DEFAULT 'off'",
        "menu_label VARCHAR(128) NOT NULL DEFAULT ''",
        "menu_category VARCHAR(64) NOT NULL DEFAULT 'user'",
        "menu_action VARCHAR(128) NOT NULL DEFAULT ''",
        "menu_groups VARCHAR(512) NOT NULL DEFAULT ''",
        "menu_order TINYINT(3) UNSIGNED NOT NULL DEFAULT '0'",
        "menu_function VARCHAR(128) NOT NULL DEFAULT ''",
        "menu_onclick VARCHAR(1024) NOT NULL DEFAULT ''",
        "menu_field VARCHAR(64) NOT NULL DEFAULT ''",
        "UNIQUE menu_unique (menu_module,menu_category,menu_action)",
        "INDEX menu_category (menu_category)",
        "INDEX menu_active (menu_active)",
        "INDEX menu_order (menu_order)"
    );
    jrCore_db_verify_table('jrCore', 'menu', $_tmp, 'InnoDB');

    // Cache
    $_tmp = array(
        "cache_key CHAR(32) CHARACTER SET latin1 NOT NULL PRIMARY KEY",
        "cache_expires INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "cache_module VARCHAR(64) NOT NULL DEFAULT ''",
        "cache_profile_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "cache_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "cache_item_id VARCHAR(8096) NOT NULL DEFAULT ''",
        "cache_encoded TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "cache_value LONGTEXT NOT NULL",
        "INDEX cache_expires (cache_expires)",
        "INDEX cache_module (cache_module)",
        "INDEX cache_profile_id (cache_profile_id)",
        "INDEX cache_user_id (cache_user_id)",
        "INDEX cache_item_id (cache_item_id(64))"
    );
    jrCore_db_verify_table('jrCore', 'cache', $_tmp, 'InnoDB');
    jrCore_db_change_table_engine('jrCore', 'cache', 'InnoDB');

    // TempValue
    $_tmp = array(
        "temp_module VARCHAR(64) NOT NULL DEFAULT 'jrCore'",
        "temp_key VARCHAR(64) NOT NULL DEFAULT ''",
        "temp_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "temp_value MEDIUMTEXT NOT NULL",
        "UNIQUE temp_unique (temp_module, temp_key)",
        "INDEX temp_key (temp_key)",
        "INDEX temp_updated (temp_updated)"
    );
    jrCore_db_verify_table('jrCore', 'tempvalue', $_tmp, 'InnoDB');
    jrCore_db_change_table_engine('jrCore', 'tempvalue', 'InnoDB');

    // Template
    $_tmp = array(
        "template_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "template_module VARCHAR(50) NOT NULL DEFAULT 'jrCore'",
        "template_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "template_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "template_user VARCHAR(128) NOT NULL DEFAULT ''",
        "template_active TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "template_name VARCHAR(64) NOT NULL DEFAULT ''",
        "template_type VARCHAR(64) NOT NULL DEFAULT ''",
        "template_body MEDIUMTEXT NOT NULL",
        "UNIQUE template_unique (template_module, template_name)",
        "INDEX template_active (template_active)"
    );
    jrCore_db_verify_table('jrCore', 'template', $_tmp, 'InnoDB');

    // Form Sessions
    $_tmp = array(
        "form_token VARCHAR(32) NOT NULL PRIMARY KEY",
        "form_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "form_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "form_rand INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "form_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "form_view VARCHAR(128) NOT NULL DEFAULT ''",
        "form_validated TINYINT(1) NOT NULL DEFAULT '0'",
        "form_params MEDIUMTEXT NOT NULL",
        "form_fields MEDIUMTEXT NOT NULL",
        "form_saved MEDIUMTEXT NOT NULL",
        "INDEX form_created (form_created)",
        "INDEX form_view (form_view)",
        "INDEX form_user_id (form_user_id)"
    );
    jrCore_db_verify_table('jrCore', 'form_session', $_tmp, 'InnoDB');
    jrCore_db_change_table_engine('jrCore', 'form_session', 'InnoDB');

    // We need to drop the OLD Primary key if it exists
    if (jrCore_db_table_exists('jrCore', 'count_ip')) {
        $tbl = jrCore_db_table_name('jrCore', 'count_ip');
        $req = "SHOW INDEX FROM {$tbl}";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt)) {
            foreach ($_rt as $_ix) {
                if (isset($_ix['Key_name']) && $_ix['Key_name'] == 'PRIMARY') {
                    $req = "ALTER TABLE {$tbl} DROP PRIMARY KEY";
                    jrCore_db_query($req);
                    break;
                }
            }
        }
        // Update counter IP addresses to be integers
        $req = "UPDATE {$tbl} SET count_ip = INET_ATON(count_ip) WHERE count_ip LIKE '%.%'";
        $cnt = jrCore_db_query($req, 'COUNT', false, null, false);
        if ($cnt > 0) {
            jrCore_logger('INF', "converted " . jrCore_number_format($cnt) . " counter entries to correct IP address format");
        }
    }

    // Counter
    $_tmp = array(
        "count_ip INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "count_uid INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "count_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "count_name VARCHAR(128) NOT NULL DEFAULT ''",
        "count_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "UNIQUE count_hit (count_ip, count_uid, count_user_id, count_name)"
    );
    jrCore_db_verify_table('jrCore', 'count_ip', $_tmp, 'InnoDB');

    // Modal
    $_tmp = array(
        "modal_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "modal_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "modal_key VARCHAR(32) NOT NULL DEFAULT ''",
        "modal_value VARCHAR(512) NOT NULL DEFAULT ''",
        "INDEX modal_key (modal_key)"
    );
    jrCore_db_verify_table('jrCore', 'modal', $_tmp, 'InnoDB');

    // Play Keys
    $_tmp = array(
        "key_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "key_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "key_code VARCHAR(16) NOT NULL DEFAULT ''",
        "UNIQUE key_code (key_code)"
    );
    jrCore_db_verify_table('jrCore', 'play_key', $_tmp, 'InnoDB');
    jrCore_db_change_table_engine('jrCore', 'play_key', 'InnoDB');

    // Pending Item
    $_tmp = array(
        "pending_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "pending_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "pending_module VARCHAR(64) NOT NULL DEFAULT ''",
        "pending_item_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "pending_linked_item_module VARCHAR(64) NOT NULL DEFAULT ''",
        "pending_linked_item_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "pending_data MEDIUMTEXT NOT NULL",
        "UNIQUE pending_unique (pending_module,pending_item_id)",
        "INDEX pending_item_id (pending_item_id)",
        "INDEX pending_linked_item_module (pending_linked_item_module)",
        "INDEX pending_linked_item_id (pending_linked_item_id)"
    );
    jrCore_db_verify_table('jrCore', 'pending', $_tmp, 'InnoDB');

    // Pending Reason
    $_tmp = array(
        "reason_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "reason_key VARCHAR(32) NOT NULL DEFAULT ''",
        "reason_text VARCHAR(1024) NOT NULL DEFAULT ''",
        "UNIQUE reason_key (reason_key)"
    );
    jrCore_db_verify_table('jrCore', 'pending_reason', $_tmp, 'InnoDB');

    // Have we migrated to the new queue structure yet?
    $_sv = false;
    $_tm = jrCore_db_table_columns('jrCore', 'queue');
    if ($_tm && isset($_tm['queue_data'])) {

        // Turn off queues while we work
        if (function_exists('jrCore_set_system_queue_state')) {
            jrCore_set_system_queue_state('off');
        }

        // We are still on the "old" version of the queue table - migrate queue entries
        $tbl = jrCore_db_table_name('jrCore', 'queue');
        $req = "SELECT queue_id, queue_module, queue_name, queue_data, queue_sleep, queue_system_id FROM {$tbl} WHERE queue_started = 0";
        $_sv = jrCore_db_query($req, 'queue_id');
        if ($_sv && is_array($_sv) && count($_sv) > 0) {
            // We have them - remove them
            $req = "DELETE FROM {$tbl} WHERE queue_id IN(" . implode(',', array_keys($_sv)) . ')';
            jrCore_db_query($req);
        }
    }

    // Queue Data
    $_tmp = array(
        "queue_id BIGINT(20) UNSIGNED NOT NULL PRIMARY KEY",
        "queue_item_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
        "queue_data LONGTEXT NOT NULL",
        "queue_status VARCHAR(255) NOT NULL DEFAULT ''",
        "queue_note VARCHAR(255) NOT NULL DEFAULT ''",
        "INDEX queue_item_id (queue_item_id)",
    );
    jrCore_db_verify_table('jrCore', 'queue_data', $_tmp, 'InnoDB');

    // Queue
    $_tmp = array(
        "queue_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "queue_name VARCHAR(64) NOT NULL DEFAULT ''",
        "queue_system_id VARCHAR(32) NOT NULL DEFAULT ''",
        "queue_created INT(10) UNSIGNED NOT NULL DEFAULT '0'",
        "queue_module VARCHAR(64) NOT NULL DEFAULT ''",
        "queue_worker VARCHAR(64) NOT NULL DEFAULT ''",
        "queue_started INT(10) UNSIGNED NOT NULL DEFAULT '0'",
        "queue_expires INT(10) UNSIGNED NOT NULL DEFAULT '0'",
        "queue_sleep INT(10) UNSIGNED NOT NULL DEFAULT '0'",
        "queue_count INT(10) UNSIGNED NOT NULL DEFAULT '0'",
        "INDEX queue_name (queue_name)",
        "INDEX queue_system_id (queue_system_id)",
        "INDEX queue_created (queue_created)",
        "INDEX queue_module (queue_module)",
        "INDEX queue_worker (queue_worker)",
        "INDEX queue_started (queue_started)",
        "INDEX queue_expires (queue_expires)",
        "INDEX queue_sleep (queue_sleep)"
    );
    jrCore_db_verify_table('jrCore', 'queue', $_tmp, 'InnoDB');
    jrCore_db_change_table_engine('jrCore', 'queue', 'InnoDB');

    // Old columns we no longer need
    if (function_exists('jrCore_db_delete_table_column')) {
        jrCore_db_delete_table_column('jrCore', 'queue', 'queue_item_id');
        jrCore_db_delete_table_column('jrCore', 'queue', 'queue_data');
        jrCore_db_delete_table_column('jrCore', 'queue', 'queue_status');
        jrCore_db_delete_table_column('jrCore', 'queue', 'queue_note');
    }

    // Queue Info
    $_tmp = array(
        "queue_name VARCHAR(128) NOT NULL DEFAULT ''",
        "queue_workers TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "queue_depth INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "UNIQUE queue_name (queue_name)",
        "INDEX queue_workers (queue_workers)",
        "INDEX queue_depth (queue_depth)"
    );
    jrCore_db_verify_table('jrCore', 'queue_info', $_tmp, 'InnoDB');
    jrCore_db_change_table_engine('jrCore', 'queue_info', 'InnoDB');

    // migrate existing queues to new queue entries
    if ($_sv && count($_sv) > 0) {
        foreach ($_sv as $q) {
            $_dt = json_decode($q['queue_data'], true);
            $slp = 0;
            if ($q['queue_sleep'] > time()) {
                $slp = ($q['queue_sleep'] - time());
            }
            jrCore_queue_create($q['queue_module'], $q['queue_name'], $_dt, $slp, $q['queue_system_id']);
        }
        jrCore_logger('INF', "successfully migrated " . jrCore_number_format(count($_sv)) . " queue entries to new format");
        unset($_sv);

        // Turn queues back on
        if (function_exists('jrCore_set_system_queue_state')) {
            jrCore_set_system_queue_state('on');
        }
    }

    // Performance
    $_tmp = array(
        "p_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "p_time INT(10) UNSIGNED NOT NULL DEFAULT '0'",
        "p_val VARCHAR(1024) NOT NULL DEFAULT ''",
        "p_provider VARCHAR(128) NOT NULL DEFAULT ''",
        "p_comment VARCHAR(512) NOT NULL DEFAULT ''",
        "p_price TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "p_rating TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "p_type VARCHAR(32) NOT NULL DEFAULT ''",
        "INDEX p_time (p_time)"
    );
    jrCore_db_verify_table('jrCore', 'performance', $_tmp, 'InnoDB');

    // Recycle Bin
    $_tmp = array(
        "r_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "r_group_id VARCHAR(64) NOT NULL DEFAULT '1'",
        "r_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "r_module VARCHAR(64) NOT NULL DEFAULT ''",
        "r_profile_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "r_item_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "r_title VARCHAR(255) NOT NULL DEFAULT ''",
        "r_data MEDIUMTEXT NOT NULL",
        "UNIQUE r_unique (r_module, r_item_id)",
        "INDEX r_group_id (r_group_id)",
        "INDEX r_profile_id (r_profile_id)",
        "INDEX r_title (r_title)",
        "INDEX r_time (r_time)"
    );
    jrCore_db_verify_table('jrCore', 'recycle', $_tmp, 'InnoDB');

    // Emoji
    $_tmp = array(
        "emoji_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "emoji_value VARBINARY(16) NOT NULL",
        "UNIQUE emoji_value (emoji_value)"
    );
    jrCore_db_verify_table('jrCore', 'emoji', $_tmp, 'InnoDB');

    // Stat Count
    $_tmp = array(
        "stat_module VARCHAR(64) CHARACTER SET latin1 NOT NULL DEFAULT ''",
        "stat_key VARCHAR(64) NOT NULL DEFAULT ''",
        "stat_index VARCHAR(64) NOT NULL DEFAULT ''",
        "stat_date BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
        "stat_value BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
        "UNIQUE stat_unique (stat_key, stat_module, stat_index, stat_date)",
        "INDEX stat_index (stat_index)",
        "INDEX stat_date (stat_date)"
    );
    jrCore_db_verify_table('jrCore', 'stat_count', $_tmp, 'InnoDB');

    // Stat Unique
    $_tmp = array(
        "stat_ip VARCHAR(45) CHARACTER SET latin1 NOT NULL DEFAULT ''",
        "stat_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "stat_key VARCHAR(192) NOT NULL DEFAULT ''",
        "stat_date BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
        "UNIQUE stat_unique (stat_ip, stat_user_id, stat_key, stat_date)"
    );
    jrCore_db_verify_table('jrCore', 'stat_unique', $_tmp, 'InnoDB');

    // Locks
    $_tmp = array(
        "lock_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "lock_unique VARCHAR(128) NOT NULL",
        "lock_expires INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "UNIQUE lock_unique (lock_unique)",
        "INDEX lock_expires (lock_expires)"
    );
    jrCore_db_verify_table('jrCore', 'global_lock', $_tmp, 'InnoDB');

    // Used for performance testing
    jrCore_db_create_datastore('jrCore', 'core');

    // Validate queue depth in queue info table
    if (function_exists('jrCore_validate_queue_info')) {
        jrCore_validate_queue_info();
    }

    return true;
}
