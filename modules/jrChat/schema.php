<?php
/**
 * Jamroom Simple Chat module
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
function jrChat_db_schema()
{
    // Datastore - for media
    jrCore_db_create_datastore('jrChat', 'chat');

    $_tmp = array(
        "room_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "room_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "room_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "room_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "room_title VARCHAR(64) NOT NULL DEFAULT ''",
        "room_private TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "room_public TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "INDEX room_user_id (room_user_id)",
        "INDEX room_title (room_title)",
        "INDEX room_public (room_public)"
    );
    jrCore_db_verify_table('jrChat', 'room', $_tmp, 'InnoDB');

    $_tmp = array(
        "slot_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "slot_room_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "slot_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "slot_last_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "slot_blocked TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "UNIQUE slot_id_unique (slot_room_id, slot_user_id)",
        "INDEX slot_user_id (slot_user_id)",
        "INDEX slot_blocked (slot_blocked)"
    );
    jrCore_db_verify_table('jrChat', 'slot', $_tmp, 'InnoDB');

    $_tmp = array(
        "msg_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "msg_room_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "msg_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "msg_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "msg_content TEXT NOT NULL",
        "INDEX msg_room_id (msg_room_id)",
        "INDEX msg_user_id (msg_user_id)"
    );
    jrCore_db_verify_table('jrChat', 'message', $_tmp, 'InnoDB');

    $_tmp = array(
        "t_room_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "t_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "t_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "UNIQUE t_unique_idx (t_room_id, t_user_id)"
    );
    jrCore_db_verify_table('jrChat', 'typing', $_tmp, 'InnoDB');

    $_tmp = array(
        "msg_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "msg_room_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "msg_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "msg_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "msg_content TEXT NOT NULL",
        "INDEX msg_room_id (msg_room_id)",
        "INDEX msg_user_id (msg_user_id)"
    );
    jrCore_db_verify_table('jrChat', 'archive', $_tmp, 'InnoDB');
    return true;
}
