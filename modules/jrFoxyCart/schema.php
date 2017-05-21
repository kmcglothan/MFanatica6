<?php
/**
 * Jamroom FoxyCart eCommerce module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrFoxyCart_db_schema
 */
function jrFoxyCart_db_schema()
{

    // This module uses a Data Store - create it.  The Data store holds
    // all information (key value pairs) about the FoxyCart transactions
    jrCore_db_create_datastore('jrFoxyCart', 'txn');

    // My Purchases
    $_tmp = array(
        "purchase_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "purchase_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "purchase_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "purchase_profile_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "purchase_seller_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "purchase_seller_profile_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "purchase_module VARCHAR(64) NOT NULL DEFAULT ''",
        "purchase_field VARCHAR(64) NOT NULL DEFAULT ''",
        "purchase_item_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "purchase_bundle_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "purchase_txn_id VARCHAR(32) NOT NULL DEFAULT ''",
        "purchase_price VARCHAR(12) NOT NULL DEFAULT ''",
        "purchase_qty INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "purchase_data TEXT NOT NULL",
        "INDEX purchase_user_id (purchase_user_id)",
        "INDEX purchase_seller_profile_id (purchase_seller_profile_id)",
        "INDEX purchase_module (purchase_module)",
        "INDEX purchase_txn_id (purchase_txn_id)"
    );
    jrCore_db_verify_table('jrFoxyCart', 'purchase', $_tmp);

    // SALES (of individual items)
    $_tmp = array(
        "sale_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "sale_purchase_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "sale_bundle_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "sale_buyer_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "sale_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "sale_seller_profile_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "sale_gross VARCHAR(12) NOT NULL DEFAULT ''",
        "sale_shipping VARCHAR(12) NOT NULL DEFAULT ''",
        "sale_system_fee VARCHAR(12) NOT NULL DEFAULT ''",
        "sale_total_net VARCHAR(12) NOT NULL DEFAULT ''",
        "sale_txn_id VARCHAR(60) NOT NULL DEFAULT ''",
        "sale_payed_out TINYINT(1) NOT NULL DEFAULT '0'",
        "sale_refunded TINYINT(1) NOT NULL DEFAULT '0'",
        "sale_refund_item MEDIUMTEXT NOT NULL",
        "INDEX sale_purchase_id (sale_purchase_id)",
        "INDEX sale_buyer_user_id (sale_buyer_user_id)",
        "INDEX sale_seller_profile_id (sale_seller_profile_id)",
        "INDEX sale_txn_id (sale_txn_id)",
        "INDEX sale_payed_out (sale_payed_out)"
    );
    jrCore_db_verify_table('jrFoxyCart', 'sale', $_tmp);

    // PAYOUT (past payouts to profiles)
    $_tmp = array(
        "payout_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "payout_profile_id INT(10) UNSIGNED NOT NULL DEFAULT '0'",
        "payout_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "payout_currency VARCHAR(5) NOT NULL DEFAULT ''",
        "payout_amount VARCHAR(12) NOT NULL DEFAULT ''",
        "payout_email VARCHAR(255) NOT NULL DEFAULT ''"
    );
    jrCore_db_verify_table('jrFoxyCart', 'payout', $_tmp);

    // Ledger
    $_tmp = array(
        "record_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "record_profile_id INT(10) UNSIGNED NOT NULL DEFAULT '0'",
        "record_transaction_id VARCHAR(64) NOT NULL DEFAULT ''",
        "record_email VARCHAR(128) NOT NULL DEFAULT ''",
        "record_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "record_income VARCHAR(12) NOT NULL DEFAULT ''",
        "record_expense VARCHAR(12) NOT NULL DEFAULT ''",
        "record_category VARCHAR(128) NOT NULL DEFAULT ''",
        "record_details VARCHAR(8192) NOT NULL DEFAULT ''",
        "INDEX record_profile_id (record_profile_id)",
        "INDEX record_category (record_category)"
    );
    jrCore_db_verify_table('jrFoxyCart', 'ledger', $_tmp);

    // Payout History
    $_tmp = array(
        "p_code VARCHAR(32) NOT NULL DEFAULT ''",
        "p_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "p_data MEDIUMTEXT NOT NULL",
        "p_pids MEDIUMTEXT NOT NULL",
        "p_done TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "UNIQUE p_code (p_code)"
    );
    jrCore_db_verify_table('jrFoxyCart', 'payout_history', $_tmp);

    // Subscription Expire
    $_tmp = array(
        "sub_expire_date VARCHAR(8) NOT NULL DEFAULT ''",
        "sub_profile_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "sub_new_quota_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "INDEX sub_expire_date (sub_expire_date)",
        "INDEX sub_profile_id (sub_profile_id)"
    );
    jrCore_db_verify_table('jrFoxyCart', 'sub_expire', $_tmp);

    return true;
}
