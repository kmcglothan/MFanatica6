<?php
/**
 * Jamroom Payment Support module
 *
 * copyright 2018 Jamroom Team
 *
 * This Jamroom file is LICENSED SOFTWARE, and cannot be redistributed.
 *
 * This Source Code is subject to the terms of the Jamroom Network
 * Commercial License -  please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * Jamroom Team
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
function jrPayment_db_schema()
{
    // Transaction detail DS
    jrCore_db_create_datastore('jrPayment', 'txn');

    // Register
    $_tmp = array(
        "r_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "r_txn_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "r_plugin VARCHAR(32) NOT NULL DEFAULT ''",
        "r_currency CHAR(3) NOT NULL DEFAULT 'USD'",
        "r_gateway_id VARCHAR(64) NOT NULL DEFAULT ''",
        "r_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "r_purchase_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "r_seller_profile_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "r_payed_out_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "r_refunded_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "r_refunded_amount INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "r_module VARCHAR(64) NOT NULL DEFAULT ''",
        "r_item_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "r_field VARCHAR(64) NOT NULL DEFAULT ''",
        "r_quantity INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "r_amount INT(11) NOT NULL DEFAULT '0'",
        "r_expense INT(11) NOT NULL DEFAULT '0'",
        "r_shipping INT(11) NOT NULL DEFAULT '0'",
        "r_tax INT(11) NOT NULL DEFAULT '0'",
        "r_fee INT(11) NOT NULL DEFAULT '0'",
        "r_gateway_fee INT(11) NOT NULL DEFAULT '0'",
        "r_gateway_fee_checked TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "r_hidden TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "r_tag VARCHAR(128) NOT NULL DEFAULT ''",
        "r_item_data MEDIUMTEXT NOT NULL",
        "r_note TEXT NOT NULL",
        "INDEX r_txn_id (r_txn_id)",
        "INDEX r_plugin (r_plugin)",
        "INDEX r_gateway_id (r_gateway_id)",
        "INDEX r_created (r_created)",
        "INDEX r_purchase_user_id (r_purchase_user_id)",
        "INDEX r_seller_profile_id (r_seller_profile_id)",
        "INDEX r_payed_out_time (r_payed_out_time)",
        "INDEX r_refunded_time (r_refunded_time)",
        "INDEX r_gateway_fee_checked (r_gateway_fee_checked)",
        "INDEX r_hidden (r_hidden)",
        "INDEX r_tag (r_tag)"
    );
    jrCore_db_verify_table('jrPayment', 'register', $_tmp, 'InnoDB');

    // Plugin Config
    $_tmp = array(
        "config_plugin VARCHAR(64) NOT NULL DEFAULT ''",
        "config_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "config_content TEXT NOT NULL",
        "UNIQUE config_plugin (config_plugin)"
    );
    jrCore_db_verify_table('jrPayment', 'plugin_config', $_tmp, 'InnoDB');

    // Cart
    $_tmp = array(
        "cart_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "cart_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "cart_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "cart_session_id VARCHAR(40) NOT NULL DEFAULT ''",
        "cart_hash VARCHAR(32) NOT NULL DEFAULT ''",
        "cart_status TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "INDEX cart_user_id (cart_user_id)",
        "INDEX cart_session_id (cart_session_id)",
        "INDEX cart_updated (cart_updated)",
        "INDEX cart_status (cart_status)"
    );
    jrCore_db_verify_table('jrPayment', 'cart', $_tmp, 'InnoDB');

    // Cart Item
    $_tmp = array(
        "cart_entry_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "cart_entry_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "cart_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "cart_module VARCHAR(64) NOT NULL DEFAULT ''",
        "cart_item_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "cart_field VARCHAR(64) NOT NULL DEFAULT ''",
        "cart_quantity INT(11) UNSIGNED NOT NULL DEFAULT '1'",
        "cart_amount INT(11) NOT NULL DEFAULT '0'",
        "cart_shipping INT(11) NOT NULL DEFAULT '0'",
        "UNIQUE cart_item_unique (cart_id, cart_module, cart_item_id, cart_field)"
    );
    jrCore_db_verify_table('jrPayment', 'cart_item', $_tmp, 'InnoDB');

    // Payout
    $_tmp = array(
        "payout_key CHAR(32) CHARACTER SET latin1 NOT NULL DEFAULT ''",
        "payout_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "payout_profile_ids TEXT NOT NULL",
        "payout_ids MEDIUMTEXT NOT NULL",
        "payout_refunds TEXT NOT NULL",
        "payout_options TEXT NOT NULL",
        "payout_completed INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "UNIQUE payout_key (payout_key)"
    );
    jrCore_db_verify_table('jrPayment', 'payout', $_tmp, 'InnoDB');

    // Vault
    $_tmp = array(
        "vault_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "vault_module VARCHAR(128) NOT NULL DEFAULT ''",
        "vault_item_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "vault_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "vault_updated INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "vault_deleted INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "vault_size BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
        "vault_data LONGTEXT NOT NULL",
        "UNIQUE vault_unique (vault_module, vault_item_id)",
        "INDEX vault_item_id (vault_item_id)"
    );
    jrCore_db_verify_table('jrPayment', 'vault', $_tmp, 'InnoDB');

    return true;
}
