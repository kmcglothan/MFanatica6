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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function jrProduct_meta()
{
    $_tmp = array(
        'name'        => 'Products',
        'url'         => 'product',
        'version'     => '1.1.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Supports sales of physical merchandise from a profile',
        'category'    => 'profiles',
        'requires'    => 'jrPayment',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrProduct_init()
{
    // Tools
    global $_mods;
    if (isset($_mods['jrStore'])) {
        jrCore_register_module_feature('jrCore', 'tool_view', 'jrProduct', 'import', array('Import Store Items', 'Import existing store items from the Merchandise module'));
    }

    // Core Features
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrProduct', 'off');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrProduct', true);
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrProduct', true);
    jrCore_register_module_feature('jrCore', 'item_order_support', 'jrProduct', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrProduct', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrProduct', 'update', 'item_action.tpl');

    // Profile tabs
    jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrProduct', 'default', 19); // 19 = 'products'
    jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrProduct', 'categories', 26); // 26 = 'product categories'

    // We have some JS and small custom CSS for our update page
    jrCore_register_module_feature('jrCore', 'javascript', 'jrProduct', 'jrProduct.js');

    // Let Payment module know we support shipping and item quantities
    jrCore_register_module_feature('jrPayment', 'shipping_support', 'jrProduct', true);
    jrCore_register_module_feature('jrPayment', 'quantity_support', 'jrProduct', true);

    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrProduct', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrProduct', 'update');

    // Listeners
    jrCore_register_event_listener('jrCore', 'db_get_item', 'jrProduct_db_get_item_listener');

    // Add item price field
    jrCore_register_event_listener('jrPayment', 'get_item_price_field', 'jrProduct_get_item_price_field_listener');
    jrCore_register_event_listener('jrPayment', 'cart_shipping', 'jrProduct_cart_shipping_listener');
    jrCore_register_event_listener('jrPayment', 'cart_update_quantity', 'jrProduct_cart_update_quantity_listener');
    jrCore_register_event_listener('jrPayment', 'purchase_item', 'jrProduct_purchase_item_listener');
    jrCore_register_event_listener('jrPayment', 'purchase_entry', 'jrProduct_purchase_entry_listener');

    // System reset listener
    jrCore_register_event_listener('jrDeveloper', 'reset_system', 'jrProduct_reset_system_listener');

    return true;
}

//---------------------
// EVENT LISTENERS
//---------------------

/**
 * Check that item is still available
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrProduct_cart_update_quantity_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['cart_new_quantity']) && $_data['cart_new_quantity'] > 0) {
        // Requesting a new quantity - are there enough left?
        if ($qty = jrCore_db_get_item_key('jrProduct', $_data['cart_item_id'], 'product_qty')) {
            if (jrCore_checktype($qty, 'number_nn') && $qty < $_data['cart_new_quantity']) {
                $_data['cart_new_quantity'] = $qty;
            }
        }
    }
    return $_data;
}

/**
 * Decrement number of available items
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrProduct_purchase_item_listener($_data, $_user, $_conf, $_args, $event)
{
    jrCore_db_decrement_key('jrProduct', $_data['cart_item_id'], 'product_qty', $_data['cart_quantity']);
    jrProfile_reset_cache($_data['_profile_id'], 'jrProduct');
    return $_data;
}

/**
 * Add contact button for buyer to contact seller
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrProduct_purchase_entry_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_conf;
    $_data[5]['title'] = '&nbsp;';
    if (jrCore_module_is_active('jrPrivateNote') && isset($_args['r_item_data']['_user_id'])) {
        $_ln               = jrUser_load_lang_strings();
        $url               = jrCore_get_module_url('jrPrivateNote');
        $uid               = (int) $_args['r_item_data']['_user_id'];
        $_data[5]['title'] = jrCore_page_button('contact', $_ln['jrProduct'][45], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/new/user_id={$uid}');");
    }
    return $_data;
}

/**
 * Get shipping for a product
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return int
 */
function jrProduct_cart_shipping_listener($_data, $_user, $_conf, $_args, $event)
{
    $shipping = 0;
    if (isset($_args['product_item_shipping']) && $_args['product_item_shipping'] > 0) {
        $shipping = jrPayment_price_to_cents($_args['product_item_shipping']);
    }
    return $shipping;
}

/**
 * Get item price field for product form
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return string
 */
function jrProduct_get_item_price_field_listener($_data, $_user, $_conf, $_args, $event)
{
    switch ($_args['option']) {
        case 'jrProduct/create':
        case 'jrProduct/update':
            return 'product_item_price';
            break;
    }
    return false;
}

/**
 * System Reset listener
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrProduct_reset_system_listener($_data, $_user, $_conf, $_args, $event)
{
    $tbl = jrCore_db_table_name('jrProduct', 'category');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");
    jrCore_db_query("OPTIMIZE TABLE {$tbl}");
    return $_data;
}

/**
 * Expand item images and category into useful array for templates
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrProduct_db_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_is_view_request() && $_args['module'] == 'jrProduct') {
        foreach ($_data as $k => $v) {
            if (strpos(' ' . $k, 'product_image') && strpos($k, '_time')) {
                if (!isset($_data['_product_images'])) {
                    $_data['_product_images'] = array();
                }
                $_data['_product_images'][] = substr($k, 0, strpos($k, '_time'));
            }
            elseif ($k == 'product_category_id' && jrCore_checktype($v, 'number_nz')) {
                $tbl = jrCore_db_table_name('jrProduct', 'category');
                $req = "SELECT * FROM {$tbl} WHERE cat_id = '{$v}' LIMIT 1";
                $_rt = jrCore_db_query($req, 'SINGLE');
                if ($_rt && is_array($_rt)) {
                    $_fields = json_decode(base64_decode($_rt['cat_field']), true);
                    $murl    = jrCore_db_get_prefix('jrProduct');
                    for ($i = 1; $i <= 5; $i++) {
                        if (isset($_fields["cat_field_type_{$i}"]) && $_fields["cat_field_type_{$i}"] != 'none') {
                            $_data['_product_cat_fields'][$i]['label'] = $_fields["cat_field_label_{$i}"];
                            $_data['_product_cat_fields'][$i]['type']  = $_fields["cat_field_type_{$i}"];
                            $_data['_product_cat_fields'][$i]['value'] = $_data["{$murl}_cat_field_{$i}"];
                        }
                    }
                }
            }
        }
        $_data['product_image_count'] = (isset($_data['_product_images'])) ? count($_data['_product_images']) : 0;
    }
    return $_data;
}

//---------------------
// FUNCTIONS
//---------------------

/**
 * Get profile product categories
 * @return mixed
 */
function jrProduct_get_profile_categories()
{
    global $_user;
    $tbl = jrCore_db_table_name('jrProduct', 'category');
    $req = "SELECT * FROM {$tbl} WHERE cat_profile_id = '{$_user['user_active_profile_id']}'";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt) && count($_rt) > 0) {
        $_out = array();
        foreach ($_rt as $rt) {
            $_out["{$rt['cat_id']}"] = $rt['cat_title'];
        }
        return $_out;
    }
    return false;
}

/**
 * Get a category by profile_id and category URL
 * @param int $profile_id
 * @param string $url
 * @return mixed
 */
function jrProduct_get_category_by_url($profile_id, $url)
{
    $pid = (int) $profile_id;
    $url = jrCore_db_escape($url);
    $tbl = jrCore_db_table_name('jrProduct', 'category');
    $req = "SELECT * FROM {$tbl} WHERE cat_title_url = '{$url}' AND cat_profile_id = {$pid} LIMIT 1";
    return jrCore_db_query($req, 'SINGLE');
}
