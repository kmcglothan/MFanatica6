<?php
/**
 * Jamroom Products module
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

//------------------------------
// create
//------------------------------
function view_jrProduct_create($_post, $_user, $_conf)
{
    // Must be logged in to create a product
    jrUser_session_require_login();
    jrUser_check_quota_access('jrProduct');
    jrProfile_check_disk_usage();

    // Make sure at least one category has been created
    if (!$_cats = jrProduct_get_profile_categories()) {
        jrCore_set_form_notice('error', 18);
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/create_category");
    }

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
    jrCore_page_divider();
    $_tmp = array(
        'name'     => 'product_category_id',
        'label'    => 5,
        'help'     => 6,
        'type'     => 'select',
        'options'  => array('0' => '-') + $_cats,
        'onchange' => "var v=this.options[this.selectedIndex].value; jrProduct_show_cat_fields(v, 0)",
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Holders for cat fields
    for ($i = 1; $i <= 5; $i++) {
        jrCore_page_link_cell('<div id="cat_fields_label_' . $i . '"></div>', '<div id="cat_fields_detail_' . $i . '"></div>');
    }
    jrCore_page_divider();

    // Product Body
    $_tmp = array(
        'name'     => 'product_description',
        'label'    => 7,
        'help'     => 8,
        'type'     => 'editor',
        'validate' => 'allowed_html',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Product Images
    $_tmp = array(
        'name'     => 'product_image',
        'label'    => 9,
        'help'     => 10,
        'text'     => 11,
        'type'     => 'image',
        'required' => false,
        'multiple' => 10
    );
    jrCore_form_field_create($_tmp);

    // Product Quantity
    $_tmp = array(
        'name'     => 'product_qty',
        'label'    => 12,
        'help'     => 13,
        'type'     => 'text',
        'validate' => 'number_nn',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Shipping Price
    $_tmp = array(
        'name'     => 'product_item_shipping',
        'label'    => 14,
        'help'     => 15,
        'type'     => 'text',
        'validate' => 'price',
        'required' => false
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrProduct_create_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrProduct');
    jrCore_form_validate($_post);

    // Get our posted data
    $_rt = array();
    $pfx = jrCore_db_get_prefix('jrProduct');
    foreach ($_post as $k => $v) {
        if (strpos($k, $pfx) === 0) {
            $_rt[$k] = $v;
        }
    }

    // We must have a category
    if (!jrCore_checktype($_rt['product_category_id'], 'number_nz')) {
        jrCore_set_form_notice('error', 46);
        jrCore_form_result('referrer');
    }

    // Next, we need to create the "urls"
    $_rt['product_title_url']    = jrCore_url_string($_rt['product_title']);
    $_cats                       = jrProduct_get_profile_categories();
    $_rt['product_category']     = $_cats["{$_rt['product_category_id']}"];
    $_rt['product_category_url'] = jrCore_url_string($_rt['product_category']);

    // $aid will be the INSERT_ID (_item_id) of the created item
    $aid = jrCore_db_create_item('jrProduct', $_rt);
    if (!$aid) {
        jrCore_set_form_notice('error', 'An error was encountered saving the product - please try again.');
        jrCore_form_result();
    }

    // Save uploaded media files
    jrCore_save_all_media_files('jrProduct', 'create', $_user['user_active_profile_id'], $aid);

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'create', 'jrProduct', $aid);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$aid}/{$_rt['product_title_url']}");
}

//------------------------------
// update
//------------------------------
function view_jrProduct_update($_post, $_user, $_conf)
{
    // Must be logged in to update a product
    jrUser_session_require_login();
    jrUser_check_quota_access('jrProduct');
    jrProfile_check_disk_usage();

    // Make sure at least one category has been created
    if (!$_cats = jrProduct_get_profile_categories()) {
        jrCore_set_form_notice('error', 18);
    }

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid product_id - please try again');
    }
    $_rt = jrCore_db_get_item('jrProduct', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', 'An error was encountered retrieving the product - please try again.');
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Start update form
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
        'product_category = 1'
    );
    $tmp = jrCore_page_banner_item_jumper('jrProduct', 'product_title', $_sr, 'create', 'update');
    jrCore_page_banner(16, $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 17,
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
    jrCore_page_divider();
    $_tmp = array(
        'name'     => 'product_category_id',
        'label'    => 5,
        'help'     => 6,
        'type'     => 'select',
        'options'  => array('0' => '-') + $_cats,
        'onchange' => "var v=this.options[this.selectedIndex].value; jrProduct_show_cat_fields(v, 0)",
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Holders for cat fields
    for ($i = 1; $i <= 5; $i++) {
        jrCore_page_link_cell('<div id="cat_fields_label_' . $i . '"></div>', '<div id="cat_fields_detail_' . $i . '"></div>');
    }
    jrCore_page_html('<script type="text/javascript">jrProduct_show_cat_fields(' . $_rt['product_category_id'] . ', ' . $_post['id'] . ')</script>');
    jrCore_page_divider();

    // Product Body
    $_tmp = array(
        'name'     => 'product_description',
        'label'    => 7,
        'help'     => 8,
        'type'     => 'editor',
        'validate' => 'allowed_html',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Product Images
    $_tmp = array(
        'name'         => 'product_image',
        'label'        => 9,
        'help'         => 10,
        'text'         => 11,
        'type'         => 'image',
        'required'     => false,
        'multiple'     => 10,
        'image_delete' => true
    );
    jrCore_form_field_create($_tmp);

    // Product Quantity
    $_tmp = array(
        'name'     => 'product_qty',
        'label'    => 12,
        'help'     => 13,
        'type'     => 'text',
        'validate' => 'number_nn',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Shipping Price
    $_tmp = array(
        'name'     => 'product_item_shipping',
        'label'    => 14,
        'help'     => 15,
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
function view_jrProduct_update_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrProduct');

    // Validate all incoming posted data
    jrCore_form_validate($_post);

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid product_id - please try again');
        jrCore_form_result('referrer');
    }

    // Get data
    $_rt = jrCore_db_get_item('jrProduct', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 'An error was encountered retrieving the product - please try again.');
        jrCore_form_result('referrer');
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data
    $_sv = array();
    $pfx = jrCore_db_get_prefix('jrProduct');
    foreach ($_post as $k => $v) {
        if (strpos($k, $pfx) === 0) {
            $_sv["{$k}"] = $v;
        }
    }

    // We must have a category
    if (!jrCore_checktype($_sv['product_category_id'], 'number_nz')) {
        jrCore_set_form_notice('error', 46);
        jrCore_form_result('referrer');
    }

    // Next, we need to create the "urls"
    $_sv['product_title_url']    = jrCore_url_string($_sv['product_title']);
    $_cats                       = jrProduct_get_profile_categories();
    $_sv['product_category']     = $_cats["{$_sv['product_category_id']}"];
    $_sv['product_category_url'] = jrCore_url_string($_sv['product_category']);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrProduct', $_post['id'], $_sv);

    // Save any uploaded media files
    jrCore_save_all_media_files('jrProduct', 'update', $_user['user_active_profile_id'], $_post['id'], $_rt);

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'update', 'jrProduct', $_post['id']);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$_post['id']}/{$_rt['product_title_url']}");
}

//------------------------------
// delete
//------------------------------
function view_jrProduct_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrProduct');

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid product_id - please try again');
        jrCore_form_result('referrer');
    }
    $_rt = jrCore_db_get_item('jrProduct', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 'An error was encountered retrieving the product - please try again.');
        jrCore_form_result('referrer');
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    // Delete item and any associated files
    jrCore_db_delete_item('jrProduct', $_post['id']);
    jrProfile_reset_cache();
    jrCore_form_result('delete_referrer');
}

//------------------------------
// create category
//------------------------------
function view_jrProduct_create_category($_post, $_user, $_conf)
{
    // Must be logged in to create a category
    jrUser_session_require_login();
    jrUser_check_quota_access('jrProduct');

    // Start our create form
    jrCore_page_banner(24);

    // Form init
    $_tmp = array(
        'submit_value' => 20,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    // Product Title
    $_tmp = array(
        'name'     => 'cat_title',
        'label'    => 21,
        'help'     => 22,
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// create_category_save
//------------------------------
function view_jrProduct_create_category_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrProduct');
    jrCore_form_validate($_post);

    // Next, we need to create the "slug" from the title
    $_post['cat_title_url'] = jrCore_url_string($_post['cat_title']);

    // Make sure the category title is unique to this user
    $tbl = jrCore_db_table_name('jrProduct', 'category');
    $req = "SELECT * FROM {$tbl} WHERE cat_profile_id = '{$_user['user_active_profile_id']}' AND cat_title_url = '{$_post['cat_title_url']}'";
    if (jrCore_checktype(jrCore_db_query($req, 'NUM_ROWS'), 'number_nz')) {
        jrCore_set_form_notice('error', 23);
        jrCore_form_result('referrer');
    }

    // All good - Add category to the table and go to the category update form
    $_post['cat_title'] = jrCore_db_escape($_post['cat_title']);
    $cat_field          = base64_encode(json_encode(array()));
    $req                = "INSERT INTO {$tbl} (cat_created, cat_updated, cat_profile_id, cat_title, cat_title_url, cat_field) VALUES (UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), {$_user['user_active_profile_id']}, '{$_post['cat_title']}', '{$_post['cat_title_url']}', '{$cat_field}')";
    if (!$cid = jrCore_db_query($req, 'INSERT_ID')) {
        jrCore_set_form_notice('error', 'An error was encountered saving the category - please try again.');
        jrCore_form_result('referrer');
    }
    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/update_category/id={$cid}");
}

//------------------------------
// update category
//------------------------------
function view_jrProduct_update_category($_post, $_user, $_conf)
{
    // Must be logged in to update a product
    jrUser_session_require_login();
    jrUser_check_quota_access('jrProduct');
    $_ln = jrUser_load_lang_strings();

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid category id - please try again');
    }

    // Get the category
    $tbl = jrCore_db_table_name('jrProduct', 'category');
    $req = "SELECT * FROM {$tbl} WHERE cat_id = '{$_post['id']}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        jrCore_notice_page('error', 'An error was encountered retrieving the category - please try again.');
    }
    if ($_cat_field = json_decode(base64_decode($_rt['cat_field']), true)) {
        $_rt = array_merge($_rt, $_cat_field);
    }

    // Start our update form
    jrCore_page_banner(25);

    // Form init
    $_tmp = array(
        'submit_value' => 17,
        'cancel'       => jrCore_is_profile_referrer(),
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // id
    $_tmp = array(
        'name'     => 'cat_id',
        'type'     => 'hidden',
        'value'    => $_post['id'],
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    // Original Title
    $_tmp = array(
        'name'     => 'cat_orig_title_url',
        'type'     => 'hidden',
        'value'    => $_rt['cat_title_url'],
        'validate' => 'not_empty'
    );
    jrCore_form_field_create($_tmp);

    // Title
    $_tmp = array(
        'name'     => 'cat_title',
        'label'    => 21,
        'help'     => 22,
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Product options
    $_popts = array(
        'none'     => '-',
        'text'     => 'Text',
        'textarea' => 'Text Area',
        'select'   => 'Select'
    );
    for ($i = 1; $i <= 5; $i++) {
        $_tmp = array(
            'section'  => "{$_ln['jrProduct'][40]} {$i}",
            'name'     => "cat_field_type_{$i}",
            'label'    => 34,
            'help'     => 35,
            'type'     => 'select',
            'options'  => $_popts,
            'required' => true
        );
        jrCore_form_field_create($_tmp);

        $_tmp = array(
            'section'  => "{$_ln['jrProduct'][40]} {$i}",
            'name'     => "cat_field_label_{$i}",
            'label'    => 36,
            'help'     => 37,
            'type'     => 'text',
            'required' => false
        );
        jrCore_form_field_create($_tmp);

        $_tmp = array(
            'section'  => "{$_ln['jrProduct'][40]} {$i}",
            'name'     => "cat_field_option_{$i}",
            'label'    => 38,
            'help'     => 39,
            'type'     => 'textarea',
            'required' => false
        );
        jrCore_form_field_create($_tmp);
    }

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// update_category_save
//------------------------------
function view_jrProduct_update_category_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrProduct');
    jrCore_form_validate($_post);

    // We should get an id
    if (!isset($_post['cat_id']) || !jrCore_checktype($_post['cat_id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid category id - please try again');
    }

    // Next, we need to create the "slug" from the title
    $_post['cat_title_url'] = jrCore_url_string($_post['cat_title']);

    // Make sure the category title is unique to this user
    $tbl = jrCore_db_table_name('jrProduct', 'category');
    if ($_post['cat_title_url'] != $_post['cat_orig_title_url']) {
        $req = "SELECT * FROM {$tbl} WHERE cat_profile_id = '{$_user['user_active_profile_id']}' AND cat_title_url = '{$_post['cat_title_url']}'";
        if (jrCore_checktype(jrCore_db_query($req, 'NUM_ROWS'), 'number_nz')) {
            jrCore_set_form_notice('error', 23);
            jrCore_form_result('referrer');
        }
    }

    // Build the fields array
    $_cat_field = array();
    foreach ($_post as $k => $v) {
        if (strpos($k, 'cat_field_') === 0) {
            $_cat_field["{$k}"] = $v;
        }
    }
    $cat_field = base64_encode(json_encode($_cat_field));

    // Update the table
    $cat_title = jrCore_db_escape($_post['cat_title']);
    $req       = "UPDATE {$tbl} SET `cat_updated` = UNIX_TIMESTAMP(), `cat_title` = '{$cat_title}', `cat_title_url` = '{$_post['cat_title_url']}', `cat_field` = '{$cat_field}' WHERE `cat_id` = {$_post['cat_id']} LIMIT 1";
    if (jrCore_db_query($req, 'COUNT') != 1) {
        jrCore_notice_page('error', 'An error was encountered saving the category - please try again.');
    }

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/categories");
}

//------------------------------
// delete category
//------------------------------
function view_jrProduct_delete_category($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrProduct');

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid category_id - please try again');
        jrCore_form_result('referrer');
    }

    // Get the category
    $tbl = jrCore_db_table_name('jrProduct', 'category');
    $req = "SELECT * FROM {$tbl} WHERE cat_id = '{$_post['id']}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        jrCore_notice_page('error', 'An error was encountered retrieving the category - please try again.');
    }

    // Make sure the calling user has permission to edit this item
    if (!jrProfile_is_profile_owner($_rt['cat_profile_id'])) {
        jrUser_not_authorized();
    }

    // Delete category
    $req = "DELETE FROM {$tbl} WHERE cat_id = '{$_post['id']}' LIMIT 1";
    if (jrCore_db_query($req, 'COUNT') != 1) {
        jrCore_notice_page('error', 'An error was encountered deleting the category - please try again.');
    }

    jrProfile_reset_cache();
    jrCore_form_result('delete_referrer');
}

//------------------------------
// get_cat_fields
// $_post._1 - category ID
// $_post._2 - item ID or 0
//------------------------------
function view_jrProduct_get_cat_fields($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrProduct');
    $pfx = jrCore_db_get_prefix('jrProduct');

    // Get item
    $_pt = array();
    if (jrCore_checktype($_post['_2'], 'number_nz')) {
        $_pt = jrCore_db_get_item('jrProduct', $_post['_2']);
    }

    // Get category field info
    $_fields = array();
    if (jrCore_checktype($_post['_1'], 'number_nz')) {
        $tbl = jrCore_db_table_name('jrProduct', 'category');
        $req = "SELECT * FROM {$tbl} WHERE cat_id = '{$_post['_1']}' LIMIT 1";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if ($_rt && is_array($_rt)) {
            $_fields = json_decode(base64_decode($_rt['cat_field']), true);
        }
    }
    $_out = array();
    for ($i = 1; $i <= 5; $i++) {
        $key = "{$pfx}_cat_field_{$i}";
        if (isset($_fields["cat_field_type_{$i}"]) && $_fields["cat_field_type_{$i}"] == 'text') {
            if (isset($_pt["{$key}"])) {
                $value = $_pt["{$key}"];
            }
            else {
                $value = '';
                $_x    = explode(PHP_EOL, $_fields["cat_field_option_{$i}"]);
                if (isset($_x[0])) {
                    $_x[0] = trim($_x[0]);
                    if (strlen($_x[0]) > 0) {
                        $value = $_x[0];
                    }
                }
            }
            $_out["{$i}"]['label']  = "{$_fields["cat_field_label_{$i}"]}";
            $_out["{$i}"]['detail'] = '<input type="text" class="form_text" name="' . $key . '" value="' . $value . '">';
        }
        elseif (isset($_fields["cat_field_type_{$i}"]) && $_fields["cat_field_type_{$i}"] == 'textarea') {
            if (isset($_pt["{$key}"])) {
                $value = $_pt["{$key}"];
            }
            else {
                $value = '';
                if (strlen($_fields["cat_field_option_{$i}"]) > 0) {
                    $value = $_fields["cat_field_option_{$i}"];
                }
            }
            $_out["{$i}"]['label']  = "{$_fields["cat_field_label_{$i}"]}";
            $_out["{$i}"]['detail'] = '<textarea class="form_textarea" name="' . $key . '">' . $value . '</textarea>';
        }
        elseif (isset($_fields["cat_field_type_{$i}"]) && $_fields["cat_field_type_{$i}"] == 'select') {
            $_out["{$i}"]['label']  = "{$_fields["cat_field_label_{$i}"]}";
            $_out["{$i}"]['detail'] = '<select class="form_select" name="' . $key . '">';
            foreach (explode(PHP_EOL, $_fields["cat_field_option_{$i}"]) as $opt) {
                $topt = trim($opt);
                if (isset($_pt["{$key}"]) && $_pt["{$key}"] == $topt) {
                    $_out["{$i}"]['detail'] .= "<option value=\"{$topt}\" selected>{$topt}</option>";
                }
                else {
                    $_out["{$i}"]['detail'] .= "<option value=\"{$topt}\">{$topt}</option>";
                }
            }
            $_out["{$i}"]['detail'] .= "</select><br>";
        }
        else {
            $_out["{$i}"]['label']  = '';
            $_out["{$i}"]['detail'] = '';
        }
    }
    $_rp = array('success' => $_out);
    jrCore_json_response($_rp, true, false);
}

//------------------------------
// import
//------------------------------
function view_jrProduct_import($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrProduct');

    $show = false;
    if (!isset($_mods['jrStore'])) {
        jrCore_set_form_notice('error', 'The Store module is not installed');
    }
    else {
        if (jrCore_db_get_datastore_item_count('jrStore') === 0) {
            jrCore_set_form_notice('error', "There are no Store items to import");
        }
        else {
            $show = true;
            jrCore_set_form_notice('success', "This tool will import existing Store items in to the Product module");
            $_msg = array();
            if (jrCore_db_get_datastore_item_count('jrStore') > 0) {
                $_msg[] = 'Existing items in the Product Module will be deleted!';
                $_msg[] = 'Existing items in the Store module will be unaffected.';
            }
            $_msg[] = 'After running this tool make sure and <b>disable</b> the Store module!';
            jrCore_set_form_notice('error', implode('<br>', $_msg), false);
        }
    }
    jrCore_page_banner('import Store items');
    jrCore_get_form_notice();

    if ($show) {
        $_tmp = array(
            'submit_value'  => 'import store items',
            'cancel'        => 'referrer',
            'submit_prompt' => 'Import Items from the Store Module?'
        );
        jrCore_form_create($_tmp);

        $_tmp = array(
            'name'     => 'create_product_items',
            'label'    => 'import store items',
            'help'     => 'If this option is checked, store items will be copied from the Store module into the Product module',
            'type'     => 'checkbox',
            'validate' => 'onoff',
            'default'  => 'off',
            'required' => true
        );
        jrCore_form_field_create($_tmp);
    }
    jrCore_page_display();
}

//------------------------------
// import_save
//------------------------------
function view_jrProduct_import_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    $cnt = 0;
    // Import Store Items
    if (isset($_post['create_product_items']) && $_post['create_product_items'] == 'on') {

        // Truncate all Product tables
        jrCore_db_truncate_datastore('jrProduct');
        $tbl = jrCore_db_table_name('jrProduct', 'category');
        $req = "TRUNCATE TABLE {$tbl}";
        jrCore_db_query($req);

        // Copy DataStore
        $tb1 = jrCore_db_table_name('jrProduct', 'item');
        $tb2 = jrCore_db_table_name('jrStore', 'item');
        $req = "INSERT INTO {$tb1} SELECT * FROM {$tb2}";
        $cnt = jrCore_db_query($req, 'COUNT');
        $tb1 = jrCore_db_table_name('jrProduct', 'item_key');
        $tb2 = jrCore_db_table_name('jrStore', 'item_key');
        $req = "INSERT INTO {$tb1} (`_item_id`,`_profile_id`,`key`,`index`,`value`) SELECT `_item_id`,`_profile_id`,`key`,`index`,`value` FROM {$tb2}";
        jrCore_db_query($req);
        $req = "UPDATE {$tb1} SET `key` = 'product_description' WHERE `key` = 'product_body'";
        jrCore_db_query($req);
        $req = "UPDATE {$tb1} SET `key` = 'product_ship_shipping' WHERE `key` = 'product_item_domestic'";
        jrCore_db_query($req);

        // Build categories
        $_s = array(
            'skip_triggers' => true,
            'limit'         => jrCore_db_get_datastore_item_count('jrProduct')
        );
        $_rt = jrCore_db_search_items('jrProduct', $_s);
        if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
            $_cats      = array();
            $_cat_field = array();
            for ($i = 1;$i <= 5;$i++) {
                $_cat_field["cat_field_type_{$i}"]   = '-';
                $_cat_field["cat_field_label_{$i}"]  = '';
                $_cat_field["cat_field_option_{$i}"] = '';
            }
            $cat_field = base64_encode(json_encode($_cat_field));
            foreach ($_rt['_items'] as $rt) {
                $rt['product_category']     = (isset($rt['product_category']) && strlen($rt['product_category']) > 0) ? $rt['product_category'] : 'miscellaneous';
                $rt['product_category_url'] = jrCore_url_string($rt['product_category']);
                if (!jrCore_checktype($_cats["{$rt['_profile_id']}"]["{$rt['product_category']}"], 'number_nz')) {
                    $req = "INSERT INTO {$tbl} (`cat_created`, `cat_updated`, `cat_profile_id`, `cat_title`, `cat_title_url`, `cat_field`) VALUES ('{$rt['_created']}', '{$rt['_created']}', '{$rt['_profile_id']}', '{$rt['product_category']}', '{$rt['product_category_url']}', '{$cat_field}')";
                    if (jrCore_checktype($id = jrCore_db_query($req, 'INSERT_ID'), 'number_nz')) {
                        $_cats["{$rt['_profile_id']}"]["{$rt['product_category']}"] = $id;
                    }
                    else {
                        jrCore_set_form_notice('error', "Failed to create category");
                        jrCore_form_result();
                    }
                }
            }
            // Add category IDs to DS
            $_upd = array();
            $_cnt = array();
            foreach ($_rt['_items'] as $rt) {
                $_upd["{$rt['_item_id']}"]['product_category_id']  = $_cats["{$rt['_profile_id']}"]["{$rt['product_category']}"];
                $_upd["{$rt['_item_id']}"]['product_category']     = (isset($rt['product_category']) && strlen($rt['product_category']) > 0) ? $rt['product_category'] : 'miscellaneous';
                $_upd["{$rt['_item_id']}"]['product_category_url'] = jrCore_url_string($_upd["{$rt['_item_id']}"]['product_category']);
                if (isset($_cnt["{$rt['_profile_id']}"]['profile_jrProduct_item_count'])) {
                    $_cnt["{$rt['_profile_id']}"]['profile_jrProduct_item_count']++;
                }
                else {
                    $_cnt["{$rt['_profile_id']}"]['profile_jrProduct_item_count'] = 1;
                }
            }
            if (!jrCore_db_update_multiple_items('jrProduct', $_upd)) {
                jrCore_set_form_notice('error', "Failed to update products with category IDs");
                jrCore_form_result();
            }
            // Update profile counts
            if (!jrCore_db_update_multiple_items('jrProfile', $_cnt)) {
                jrCore_set_form_notice('error', "Failed to update profile counts");
                jrCore_form_result();
            }
            // Copy any image files
            foreach ($_rt['_items'] as $rt) {
                foreach ($rt as $k => $v) {
                    if (strpos($k, 'product_image') === 0 && strpos($k, 'size')) {
                        list( , , $idx) = explode('_', $k);
                        if (jrCore_checktype($idx,'number_nz')) {
                            $idx = "_{$idx}";
                        }
                        else {
                            $idx = '';
                        }
                        $pdir = jrCore_get_media_directory($rt['_profile_id']);
                        if (jrCore_checktype($v, 'number_nz') && is_file("{$pdir}/jrStore_{$rt['_item_id']}_product_image{$idx}.{$rt["product_image{$idx}_extension"]}")) {
                            if (!copy("{$pdir}/jrStore_{$rt['_item_id']}_product_image{$idx}.{$rt["product_image{$idx}_extension"]}", "{$pdir}/jrProduct_{$rt['_item_id']}_product_image{$idx}.{$rt["product_image{$idx}_extension"]}")) {
                                jrCore_set_form_notice('error', "Failed to copy a Store image");
                                jrCore_form_result();
                            }
                        }
                        else {
                            unlink("{$pdir}/jrStore_{$rt['_item_id']}_product_image{$idx}.{$rt["product_image{$idx}_extension"]}");
                            $_tmp = array();
                            foreach ($rt as $dk => $ignore) {
                                if (strpos($dk, "product_image{$idx}") === 0) {
                                    $_tmp[] = $dk;
                                }
                            }
                            jrCore_db_delete_multiple_item_keys('jrProduct', $rt['_item_id'], $_tmp);
                        }
                    }
                }
            }
        }
        else {
            jrCore_set_form_notice('error', "No Product items imported (This shouldn't happen)");
            jrCore_form_result();
        }
        jrCore_set_form_notice('success', "Successfully imported " . jrCore_number_format($cnt) . " items from the Store module");
        jrCore_form_result();
    }
    else {
        jrCore_set_form_notice('error', "Import Store Items not checked");
        jrCore_form_result();
    }
}
