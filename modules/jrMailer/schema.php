<?php
/**
 * Jamroom Email Support module
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
 * @copyright 2003-2015 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * db_schema
 */
function jrMailer_db_schema()
{
    // Throttle
    $_tmp = array(
        "t_min INT(10) UNSIGNED NOT NULL DEFAULT '0'",
        "t_cnt SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0'",
        "UNIQUE t_min (t_min)"
    );
    jrCore_db_verify_table('jrMailer', 'throttle', $_tmp);

    // Campaign
    $_tmp = array(
        "c_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "c_module VARCHAR(64) NOT NULL DEFAULT ''",
        "c_unique VARCHAR(64) NOT NULL DEFAULT ''",
        "c_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "c_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "c_title VARCHAR(256) NOT NULL DEFAULT ''",
        "c_sent INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "c_unsub INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "c_bounce INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "c_message MEDIUMTEXT NOT NULL",
        "UNIQUE c_unique_idx (c_module, c_unique)",
        "INDEX c_unique (c_unique)"
    );
    jrCore_db_verify_table('jrMailer', 'campaign', $_tmp);

    // Open Tracking
    $_tmp = array(
        "t_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "t_cid INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "t_uid INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "t_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "t_ip VARCHAR(45) NOT NULL DEFAULT ''",
        "t_lat VARCHAR(32) NOT NULL DEFAULT ''",
        "t_long VARCHAR(32) NOT NULL DEFAULT ''",
        "t_country VARCHAR(64) NOT NULL DEFAULT ''",
        "t_region VARCHAR(64) NOT NULL DEFAULT ''",
        "t_city VARCHAR(64) NOT NULL DEFAULT ''",
        "t_unsub TINYINT(1) NOT NULL DEFAULT '0'",
        "t_agent VARCHAR(512) NOT NULL DEFAULT ''",
        "t_info VARCHAR(256) NOT NULL DEFAULT ''",
        "t_infou INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "UNIQUE t_id_unique (t_cid, t_uid)",
        "INDEX t_uid (t_uid)",
        "INDEX t_time (t_time)",
        "INDEX t_ip (t_ip)",
        "INDEX t_country (t_country)",
        "INDEX t_region (t_region)",
        "INDEX t_city (t_city)",
        "INDEX t_unsub (t_unsub)",
        "INDEX t_infou (t_infou)"
    );
    jrCore_db_verify_table('jrMailer', 'track', $_tmp);

    // URL Click Tracking
    $_tmp = array(
        "url_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "url_cid INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "url_uri VARCHAR(256) NOT NULL DEFAULT ''",
        "UNIQUE url_unique_idx (url_cid, url_uri)"
    );
    jrCore_db_verify_table('jrMailer', 'url', $_tmp);

    // URL Click Tracking
    $_tmp = array(
        "click_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "click_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "click_url_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "click_campaign_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "click_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "click_count INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "UNIQUE click_unique_idx (click_url_id, click_user_id)",
        "INDEX click_campaign_id (click_campaign_id)",
        "INDEX click_user_id (click_user_id)"
    );
    jrCore_db_verify_table('jrMailer', 'click', $_tmp);

    // Unsubscribe
    $_tmp = array(
        "u_uid INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "u_cid INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "u_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "UNIQUE u_uid (u_uid, u_cid)",
        "INDEX u_time (u_time)"
    );
    jrCore_db_verify_table('jrMailer', 'unsubscribe', $_tmp);

    return true;
}
