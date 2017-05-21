<?php
/**
 * Jamroom Merchandise module
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
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//------------------------------
// default (view a product)
//------------------------------
function view_jrStore_default($_post, $_user, $_conf)
{
    // We will get our product_id and product_url on the URL - i.e.
    // http://site.com/store/1/this-is-the-page-title
    // so $_post['option'] will be set with our product_id (1)
    $out = '';
    if (isset($_post['option']) && jrCore_checktype($_post['option'], 'number_nz')) {
        $pid = (int) $_post['option'];
        $_rt = jrCore_db_get_item('jrStore', $pid);
        if (!isset($_rt) || !is_array($_rt)) {
            jrCore_page_not_found();
        }

        // See if we are cached (non-logged in users)
        $key = '';
        if (!jrUser_is_logged_in()) {
            $key = "product-view-cache-{$_post['_uri']}";
            if ($out = jrCore_is_cached('jrStore', $key)) {
                $out .= "\n<!--c-->";
                return $out;
            }
        }

        // Set title, parse and return
        jrCore_page_title("{$_rt['product_title']} - {$_rt['profile_name']}");

        // Parse template
        $out = jrCore_parse_template('header.tpl', $_post);

        $out .= jrCore_parse_template('item_detail.tpl', array('item' => $_rt), 'jrStore');

        $out .= jrCore_parse_template('footer.tpl', $_post);

        // Caching for non-logged in users
        if (!jrUser_is_logged_in()) {
            jrCore_add_to_cache('jrStore', $key, $out, false, $_rt['_profile_id']);
        }
    }
    return $out;
}

//------------------------------
// create
//------------------------------
function view_jrStore_create($_post, $_user, $_conf)
{

    // Must be logged in to create a product
    jrUser_session_require_login();
    jrUser_check_quota_access('jrStore');
    jrProfile_check_disk_usage();

    // Bring in language
    jrUser_load_lang_strings();

    // Start our create form
    jrCore_page_banner(1);

    // Form init
    $_tmp = array(
        'submit_value' => 2,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    // Product Title
    $_tmp = array(
        'name'     => 'product_title',
        'label'    => 3,
        'help'     => 4,
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Product Category
    $_tmp = array(
        'name'      => 'product_category',
        'label'     => 13,
        'help'      => 14,
        'type'      => 'select_and_text',
        'validate'  => 'not_empty',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // Product Body
    $_tmp = array(
        'name'      => 'product_body',
        'label'     => 5,
        'help'      => 6,
        'type'      => 'editor',
        'validate'  => 'allowed_html',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // Product Images
    $_tmp = array(
        'name'     => 'product_image',
        'label'    => 25,
        'help'     => 26,
        'text'     => 27,
        'type'     => 'image',
        'multiple' => true,
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Product Quantity
    $_tmp = array(
        'name'     => 'product_qty',
        'label'    => 42,
        'help'     => 43,
        'type'     => 'text',
        'validate' => 'number_nn',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Domestic Shipping Price
    $_tmp = array(
        'name'     => 'product_ship_domestic',
        'label'    => 46,
        'help'     => 47,
        'type'     => 'text',
        'validate' => 'price',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // International Shipping Price
    $_tmp = array(
        'name'     => 'product_ship_international',
        'label'    => 48,
        'help'     => 49,
        'type'     => 'text',
        'validate' => 'price',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrStore_create_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrStore');
    jrCore_form_validate($_post);

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('jrStore', 'create', $_post);

    // Next, we need to create the "slug" from the title and save it
    $_rt['product_title_url']    = jrCore_url_string($_rt['product_title']);
    $_rt['product_category_url'] = jrCore_url_string($_rt['product_category']);

    // $aid will be the INSERT_ID (_item_id) of the created item
    $aid = jrCore_db_create_item('jrStore', $_rt);
    if (!$aid) {
        jrCore_set_form_notice('error', 7);
        jrCore_form_result();
    }

    // Save uploaded media files
    jrCore_save_all_media_files('jrStore', 'create', $_user['user_active_profile_id'], $aid);

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'create', 'jrStore', $aid);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$aid}/{$_rt['product_title_url']}");
}

//------------------------------
// update
//------------------------------
function view_jrStore_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrStore');

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 9);
    }
    $_rt = jrCore_db_get_item('jrStore', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', 9);
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Start output
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
        'product_category = 1'
    );
    $tmp = jrCore_page_banner_item_jumper('jrStore', 'product_title', $_sr, 'create', 'update');
    jrCore_page_banner(10, $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 11,
        'cancel'       => jrCore_is_profile_referrer(),
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // id
    $_tmp = array(
        'name'     => 'id',
        'type'     => 'hidden',
        'value'    => $_post['id'],
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    // product Title
    $_tmp = array(
        'name'     => 'product_title',
        'label'    => 3,
        'help'     => 4,
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Product Category
    $_tmp = array(
        'name'      => 'product_category',
        'label'     => 13,
        'help'      => 14,
        'type'      => 'select_and_text',
        'validate'  => 'not_empty',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // Page Body
    $_tmp = array(
        'name'      => 'product_body',
        'label'     => 5,
        'help'      => 6,
        'type'      => 'editor',
        'validate'  => 'allowed_html',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // show any images we already have setup for this product
    if (count($_rt['_product_images']) > 0) {
        $htm = jrCore_parse_template('store_update.tpl', $_rt, 'jrStore');
        jrCore_page_custom($htm, 25);
    }

    // Product Images
    $_tmp = array(
        'name'     => 'product_image',
        'label'    => 25,
        'help'     => 26,
        'text'     => 27,
        'type'     => 'image',
        'value'    => false,
        'multiple' => true,
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Product Quantity
    $_tmp = array(
        'name'     => 'product_qty',
        'label'    => 42,
        'help'     => 43,
        'type'     => 'text',
        'validate' => 'number_nn',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Domestic Shipping Price
    $_tmp = array(
        'name'     => 'product_ship_domestic',
        'label'    => 46,
        'help'     => 47,
        'type'     => 'text',
        'validate' => 'price',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // International Shipping Price
    $_tmp = array(
        'name'     => 'product_ship_international',
        'label'    => 48,
        'help'     => 49,
        'type'     => 'text',
        'validate' => 'price',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrStore_update_save($_post, &$_user, &$_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrStore');

    // Validate all incoming posted data
    jrCore_form_validate($_post);

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 9);
        jrCore_form_result('referrer');
    }

    // Get data
    $_rt = jrCore_db_get_item('jrStore', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_notice_page('error', 9);
        jrCore_form_result('referrer');
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrStore', 'update', $_post);

    // Add in our SEO URL names
    $_sv['product_title_url']    = jrCore_url_string($_sv['product_title']);
    $_sv['product_category_url'] = jrCore_url_string($_sv['product_category']);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrStore', $_post['id'], $_sv);

    // Save any uploaded media files
    jrCore_save_all_media_files('jrStore', 'update', $_user['user_active_profile_id'], $_post['id'], $_rt);

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'update', 'jrStore', $_post['id']);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$_post['id']}/{$_rt['product_title_url']}");
}

//------------------------------
// delete_image
//------------------------------
function view_jrStore_delete_image($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();

    jrUser_check_quota_access('jrStore');
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 14);
        jrCore_form_result('referrer');
    }
    $_rt = jrCore_db_get_item('jrStore', $_post['id']);
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    // Delete the keys that correspond to the image we are removing
    $_ky = array(
        "{$_post['field']}_height",
        "{$_post['field']}_width",
        "{$_post['field']}_access",
        "{$_post['field']}_extension",
        "{$_post['field']}_type",
        "{$_post['field']}_size",
        "{$_post['field']}_name",
        "{$_post['field']}_time"
    );
    $tbl = jrCore_db_table_name('jrStore', 'item_key');
    $req = "DELETE FROM {$tbl} WHERE `_item_id` = '{$_post['id']}' AND `key` IN('" . implode("','", $_ky) . "')";
    jrCore_db_query($req);

    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/update/id={$_post['id']}");
}

//------------------------------
// delete
//------------------------------
function view_jrStore_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrStore');

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 9);
        jrCore_form_result('referrer');
    }
    $_rt = jrCore_db_get_item('jrStore', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_notice_page('error', 9);
        jrCore_form_result('referrer');
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    // Delete item and any associated files
    jrCore_db_delete_item('jrStore', $_post['id']);
    jrProfile_reset_cache();
    jrCore_form_result('delete_referrer');
}

//----------------------------------------------------------
// Delivery
// Items purchased on FoxyCart by me which are postable.
//---------------------------------------------------------
function view_jrStore_purchases($_post, $_user, $_conf)
{
    jrUser_session_require_login();

    $_replace = array();
    //see if there is a specific order that we are viewing.
    if (isset($_post['_1']) && is_numeric($_post['_1'])) {
        $txn_id = (int) $_post['_1'];
    }

    if (isset($txn_id) && $txn_id > 0) {
        if (!isset($_post['_2']) || !is_numeric($_post['_2'])) {
            jrCore_notice_page('error', 'Only the purchaser may view the transaction details.');
        }

        $seller_profile_id = (int) $_post['_2'];
        //check this sellers profile id is involved in this transaction
        $purchase_tbl     = jrCore_db_table_name('jrFoxyCart', 'purchase');
        $req              = "SELECT *
                               FROM {$purchase_tbl}
                              WHERE purchase_txn_id = '{$txn_id}'
                                AND purchase_seller_profile_id = '{$seller_profile_id}' ";
        $_purchased_items = jrCore_db_query($req, 'NUMERIC');
        if (!is_array($_purchased_items) || $_purchased_items[0]['purchase_user_id'] != $_user['_user_id']) {
            jrCore_notice_page('error', 'Only the purchaser may view the transaction details.');
        }
        //get the items purchased from this seller
        foreach ($_purchased_items as $k => $_pi) {
            $_replace['purchased_items'][$k]            = $_pi;
            $_replace['purchased_items'][$k]['details'] = jrCore_db_get_item('jrStore', $_pi['purchase_item_id']);
            $_d                                         = json_decode($_pi['purchase_data'], true);
            if (isset($_d['product_bundle_id']) && is_numeric($_d['product_bundle_id'])) {
                $_replace['purchased_items'][$k]['bundle'] = jrCore_db_get_item('jrFoxyCartBundle', $_d['product_bundle_id']);
            }
        }

        //get the txn details.
        $_sp = array(
            'search'        => array(
                "txn_id = $txn_id"
            ),
            'skip_triggers' => true
        );
        $_rt = jrCore_db_search_items('jrFoxyCart', $_sp);

        //get all the txn_*
        foreach ($_rt['_items'][0] as $k => $v) {
            $pfx = substr($k, 0, 3);
            if ($pfx == 'txn') {
                $_replace[$k] = $v;
            }
        }
        $_replace['seller'] = jrCore_db_get_item('jrProfile', $_purchased_items[0]['purchase_seller_profile_id']);
        unset($_replace['seller']['user_password']);
        //get the status.
        $_replace['status_status'] = jrStore_get_status($txn_id, $seller_profile_id);

        switch ($_post['_3']) {
            case 'communication':
                return jrCore_parse_template("purchase_communication.tpl", $_replace, 'jrStore');
                break;
            default: //details.
                return jrCore_parse_template("purchase_details.tpl", $_replace, 'jrStore');
                break;
        }

        //show just this transaction.
    }
    else {
        //show the list of transactions
        $purchase_tbl = jrCore_db_table_name('jrFoxyCart', 'purchase');
        $status_tbl   = jrCore_db_table_name('jrStore', 'status');
        $req          = "SELECT *,
                                  sum(purchase_qty) as item_count
                          FROM {$purchase_tbl} p
                     LEFT JOIN {$status_tbl} s  ON s.status_txn_id = p.purchase_txn_id AND s.status_seller_profile_id = p.purchase_seller_profile_id
                         WHERE purchase_user_id = '{$_user['_user_id']}'
                           AND purchase_module = 'jrStore'
                     GROUP BY purchase_txn_id desc , purchase_seller_profile_id ";
        $_rt          = jrCore_db_query($req, 'NUMERIC');

        $comment_tbl = jrCore_db_table_name('jrStore', 'comment');
        $_sellers    = array();
        $_txn        = array();
        foreach ($_rt as $k => $_transaction) {

            if (isset($_sellers[$_transaction['purchase_seller_user_id']])) {
                //got the seller earlier, use again.
                $_seller = $_sellers[$_transaction['purchase_seller_user_id']];
            }
            else {
                //get this seller
                $_seller                                            = jrCore_db_get_item('jrProfile', $_transaction['purchase_seller_profile_id']);
                $_sellers[$_transaction['purchase_seller_user_id']] = $_seller;
            }

            $_txn[$k] = $_transaction;
            $txn_id   = (int) $_transaction['purchase_txn_id'];

            //message count
            $req = "SELECT *,
                            COUNT(comment_id) as messages
                      FROM {$comment_tbl}
                     WHERE comment_txn_id = {$txn_id}
                       AND comment_seller_profile_id = '{$_seller['_profile_id']}'
                  GROUP BY comment_seller_profile_id ";
            $_rt = jrCore_db_query($req, 'SINGLE');

            $_txn[$k]['message_count'] = $_rt['messages'];

            //last message from
            $req = "SELECT comment_created, comment_user_id
                      FROM {$comment_tbl}
                      WHERE comment_txn_id = {$txn_id}
                        AND comment_seller_profile_id = '{$_seller['_profile_id']}'
                   ORDER BY comment_created DESC ";
            $_rt = jrCore_db_query($req, 'SINGLE');
            if ($_rt['comment_created'] > 0) {
                $_txn[$k]['message_last_time'] = $_rt['comment_created'];
            }
            else {
                $_txn[$k]['message_last_time'] = $_transaction['purchase_created'];
            }
            if ($_rt['comment_user_id'] > 0) {
                $u = $_rt['comment_user_id'];
            }
            else {
                $u = $_transaction['purchase_seller_user_id'];
            }

            $_txn[$k]['message_last_user'] = jrCore_db_get_item('jrUser', $u);
            $_txn[$k]['seller']            = $_seller;

            //sale value
            $sale_tbl               = jrCore_db_table_name('jrFoxyCart', 'sale');
            $req                    = "SELECT *
                                              FROM {$sale_tbl}
                                             WHERE sale_txn_id = {$txn_id}
                                               AND sale_seller_profile_id = '{$_seller['_profile_id']}' ";
            $_rt                    = jrCore_db_query($req, 'SINGLE');
            $_txn[$k]['sale_gross'] = jrFoxyCart_currency_format($_rt['sale_gross']);

        }
        $_replace['transactions'] = $_txn;
        return jrCore_parse_template("purchase_list.tpl", $_replace, 'jrStore');
    }

}

//------------------------------
// view_comments
//------------------------------
function view_jrStore_view_comments($_post, $_user, $_conf)
{
    jrUser_session_require_login();

    $txn_id            = (int) $_post['txn_id'];
    $seller_profile_id = (int) $_post['seller_profile_id'];

    //get all the comments from this seller/buyer
    $comment_tbl = jrCore_db_table_name('jrStore', 'comment');
    $req         = "SELECT *
              FROM {$comment_tbl}
             WHERE comment_txn_id = '{$txn_id}'
                AND comment_seller_profile_id = '{$seller_profile_id}'
          ORDER BY comment_created DESC ";
    $_rt         = jrCore_db_query($req, 'NUMERIC');

    $i         = 0;
    $_comments = array();
    if (is_array($_rt)) {
        $_all = array(); //holds the $_user array so we dont need to call it more than once each user
        foreach ($_rt as $row) {
            $_comments[$i] = $row;
            //store the users so they are only caled once
            if (isset($_all) && is_array($_all[$row['comment_user_id']])) {
                $_one = $_all[$row['comment_user_id']];
            }
            else {
                $_all[$row['comment_user_id']] = jrCore_db_get_item('jrUser', $row['comment_user_id']);
                $_one                          = $_all[$row['comment_user_id']];
            }
            $_comments[$i]['user'] = $_one;

            unset($_comments[$i]['user']['user_password']);
            $i++;
        }
    }
    $_replace = array(
        'comments' => $_comments
    );
    return jrCore_parse_template('comment_item.tpl', $_replace, 'jrStore');
}

//------------------------------
// Save comment to datastore
//------------------------------
function view_jrStore_comment_save($_post, &$_user, &$_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    $_res = jrStore_record_comment($_post['comment_seller_profile_id'], $_post['comment_text'], $_post['comment_txn_id'], $_user);
    jrCore_json_response($_res);
}

//--------------------------------
// update the transaction status
//--------------------------------
function view_jrStore_status_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();

    switch ($_post['status']) {
        case '':
        case 'onhold':
        case 'processing':
        case 'posted':
        case 'delivered':
        case 'canceled':
            if (isset($_post['txn_id']) && is_numeric($_post['txn_id'])) {
                $txn_id            = (int) $_post['txn_id'];
                $seller_profile_id = (int) $_post['seller_profile_id'];
                //update status
                $tbl = jrCore_db_table_name('jrStore', 'status');
                $req = "INSERT INTO {$tbl} (status_txn_id, status_status, status_seller_profile_id)
                            VALUES ('{$txn_id}','{$_post['status']}', '{$seller_profile_id}')
                            ON DUPLICATE KEY UPDATE status_status = '{$_post['status']}' ";
                $_ct = jrCore_db_query($req, 'COUNT');
                if ($_ct > 0) {
                    $_res = array('success' => 'status updated');
                    jrCore_logger('INF', 'jrStore trasaction ' . $txn_id . ' status updated to: ' . $_post['status']);

                    //send a status change notification email.
                    $comment_text = 'Order status changed to: ' . $_post['status'];
                    jrStore_record_comment($seller_profile_id, $comment_text, $txn_id, $_user);
                }
                else {
                    $_res = array('error' => 'failed to update the db');
                }
            }
            else {
                $_res = array('error' => 'txn_id not set');
            }
            break;
        default:
            $_res = array('error' => 'unknown status');
            break;
    }
    jrCore_json_response($_res);
}
