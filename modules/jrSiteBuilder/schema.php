<?php
/**
 * Jamroom Site Builder module
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
 * @copyright 2015 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * db_schema
 */
function jrSiteBuilder_db_schema()
{
    // Menu
    $_tmp = array(
        "menu_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "menu_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "menu_parent_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "menu_order TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "menu_title VARCHAR(256) NOT NULL DEFAULT ''",
        "menu_url VARCHAR(256) NOT NULL DEFAULT ''",
        "menu_group VARCHAR(256) NOT NULL DEFAULT ''",
        "menu_onclick VARCHAR(2048) NOT NULL DEFAULT ''",
        "INDEX menu_parent_id (menu_parent_id)",
        "INDEX menu_order (menu_order)"
    );
    jrCore_db_verify_table('jrSiteBuilder', 'menu', $_tmp, 'MyISAM');

    // Page
    $_tmp = array(
        "page_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "page_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "page_enabled TINYINT(1) UNSIGNED NOT NULL DEFAULT '1'",
        "page_uri VARCHAR(256) NOT NULL DEFAULT ''",
        "page_title VARCHAR(256) NOT NULL DEFAULT ''",
        "page_groups VARCHAR(256) NOT NULL DEFAULT ''",
        "page_active VARCHAR(256) NOT NULL DEFAULT ''",
        "page_layout VARCHAR(256) NOT NULL DEFAULT ''",
        "page_settings TEXT NOT NULL",
        "page_head TEXT NOT NULL",
        "UNIQUE page_uri (page_uri)",
        "INDEX page_enabled (page_enabled)"
    );
    jrCore_db_verify_table('jrSiteBuilder', 'page', $_tmp, 'MyISAM');

    // Widget
    $_tmp = array(
        "widget_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "widget_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "widget_page_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "widget_location INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "widget_weight INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "widget_groups VARCHAR(256) NOT NULL DEFAULT ''",
        "widget_title VARCHAR(512) NOT NULL DEFAULT ''",
        "widget_module VARCHAR(128) NOT NULL DEFAULT ''",
        "widget_name VARCHAR(128) NOT NULL DEFAULT ''",
        "widget_data MEDIUMTEXT NOT NULL",
        "widget_unique VARCHAR(128) NOT NULL DEFAULT ''",
        "INDEX widget_page_id (widget_page_id)",
        "INDEX widget_name (widget_name)"
    );
    jrCore_db_verify_table('jrSiteBuilder', 'widget', $_tmp, 'MyISAM');

    // Template
    $_tmp = array(
        "template_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "template_module VARCHAR(50) NOT NULL DEFAULT ''",
        "template_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "template_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "template_name VARCHAR(64) NOT NULL DEFAULT ''",
        "template_body TEXT NOT NULL",
        "UNIQUE template_unique (template_module, template_name)"
    );
    jrCore_db_verify_table('jrSiteBuilder', 'template', $_tmp);

    return true;
}
