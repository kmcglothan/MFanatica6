<?php
/**
 * Jamroom Item Bundles module
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
 * meta
 */
function jrBundle_meta()
{
    $_tmp = array(
        'name'        => 'Item Bundles',
        'url'         => 'itembundle',
        'version'     => '1.0.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Package multiple items for sale into bundles so they can be priced separately',
        'category'    => 'ecommerce',
        'requires'    => 'jrPayment',
        'priority'    => 200,
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrBundle_init()
{
    global $_mods;
    if (isset($_mods['jrFoxyCartBundle'])) {
        jrCore_register_module_feature('jrCore', 'tool_view', 'jrBundle', 'import', array('Import Bundles', 'Import existing item bundles from the FoxyCart Bundle module'));
    }

    // Core module support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrBundle', 'on');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrBundle', 'on');
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrBundle', 'on');

    // event triggers
    jrCore_register_event_trigger('jrBundle', 'add_bundle_price_field', 'Fired when a form is viewed');
    jrCore_register_event_trigger('jrBundle', 'get_album_field', 'Fired in create Item Bundle view');
    jrCore_register_event_trigger('jrBundle', 'add_bundle_item', 'Fired when an item is added to a bundle');
    jrCore_register_event_trigger('jrBundle', 'delete_bundle_item', 'Fired when an item is deleted from a bundle');
    jrCore_register_event_trigger('jrBundle', 'bundle_filename', 'Fired when adding a file to a bundle ZIP file');

    // Expand bundle items
    jrCore_register_event_listener('jrCore', 'db_get_item', 'jrBundle_db_get_item_listener');
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrBundle_db_search_items_listener');
    jrCore_register_event_listener('jrCore', 'db_update_item', 'jrBundle_db_update_item_listener');
    jrCore_register_event_listener('jrCore', 'db_delete_item', 'jrBundle_db_delete_item_listener');
    jrCore_register_event_listener('jrCore', 'save_media_file', 'jrBundle_save_media_file_listener');

    // Add our bundle fields on form views that have a price field
    jrCore_register_event_listener('jrCore', 'form_display', 'jrBundle_form_display_listener');
    jrCore_register_event_listener('jrCore', 'form_result', 'jrBundle_form_result_listener');
    jrCore_register_event_listener('jrCore', 'download_file', 'jrBundle_download_file_listener');
    jrCore_register_event_listener('jrCore', 'stream_file', 'jrBundle_stream_file_listener');
    jrCore_register_event_listener('jrCore', 'view_results', 'jrBundle_view_results_listener');
    jrCore_register_event_listener('jrCore', 'exclude_item_index_buttons', 'jrBundle_exclude_item_index_buttons_listener');

    // hook into payments
    jrCore_register_event_listener('jrPayment', 'payment_entry', 'jrBundle_payment_entry_listener');
    jrCore_register_event_listener('jrPayment', 'purchase_entry', 'jrBundle_purchase_entry_listener');
    jrCore_register_event_listener('jrPayment', 'txn_detail_entry', 'jrBundle_txn_detail_entry_listener');
    jrCore_register_event_listener('jrPayment', 'vault_download', 'jrBundle_vault_download_listener');
    jrCore_register_event_listener('jrPayment', 'add_to_cart_onclick', 'jrBundle_add_to_cart_onclick_listener');

    // actions
    jrCore_register_module_feature('jrCore', 'action_support', 'jrBundle', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrBundle', 'update', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'item_order_support', 'jrBundle', 'on');

    // When an action is shared via jrOneAll, we can provide the text of the shared item
    jrCore_register_event_listener('jrOneAll', 'network_share_text', 'jrBundle_network_share_text_listener');

    // System resets
    jrCore_register_event_listener('jrDeveloper', 'reset_system', 'jrBundle_reset_system_listener');

    // Custom CSS and Javascript
    jrCore_register_module_feature('jrCore', 'javascript', 'jrBundle', 'jrBundle.js');
    jrCore_register_module_feature('jrCore', 'css', 'jrBundle', 'jrBundle.css');

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'jrBundle', 'profile_jrBundle_item_count', 1);

    // Add an item to a bundle
    $_tmp = array(
        'title'  => 'add to bundle button',
        'icon'   => 'bundle',
        'active' => 'on',
        'group'  => 'owner'
    );
    jrCore_register_module_feature('jrCore', 'item_list_button', 'jrBundle', 'jrBundle_item_bundle_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrBundle', 'jrBundle_item_bundle_button', $_tmp);

    // Download Free Bundle
    $_tmp = array(
        'title'  => 'download bundle',
        'icon'   => 'download',
        'active' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'item_list_button', 'jrBundle', 'jrBundle_download_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrBundle', 'jrBundle_download_button', $_tmp);

    // Add to cart for Bundles on Bundle pages (album pages, etc)
    $_tmp = array(
        'title'  => 'add to cart button',
        'icon'   => 'cart',
        'active' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'item_bundle_list_button', 'jrBundle', 'jrBundle_add_to_cart_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_bundle_detail_button', 'jrBundle', 'jrBundle_add_to_cart_button', $_tmp);

    // Bundle ZIP worker
    jrCore_register_queue_worker('jrBundle', 'create_bundle_zip', 'jrBundle_create_bundle_zip_worker', 0, 1, 300);

    return true;
}

//---------------------------------
// QUEUE WORKER
//---------------------------------

/**
 * Build downloadable ZIP file of bundle contents
 * @param array $_queue
 * @return bool
 */
function jrBundle_create_bundle_zip_worker($_queue)
{
    global $_conf;
    if (isset($_queue['bundle_id'])) {
        $bid = (int) $_queue['bundle_id'];
        $lck = "create_bundle_zip_{$bid}";
        if (jrCore_create_global_lock('jrBundle', $lck, 120)) {
            if ($_bundle = jrCore_db_get_item('jrBundle', $bid)) {
                if (isset($_bundle['bundle_items']) && is_array($_bundle['bundle_items'])) {
                    $add = '';
                    $_bl = json_decode($_bundle['bundle_list'], true);
                    $_fl = array();
                    foreach ($_bundle['bundle_items'] as $k => $i) {
                        $mod = $i['bundle_module'];
                        $iid = (int) $i['_item_id'];
                        $fld = $_bl[$mod][$iid][0];
                        jrPayment_add_register_item_to_vault($mod, $iid, $fld, $i);
                        $_fl[] = array($mod, $iid, $fld, $i);
                    }
                    if (isset($_bundle['bundle_deleted_list']) && strlen($_bundle['bundle_deleted_list']) > 0) {
                        // We have items in this bundle that have been deleted by the profile.
                        // Did the downloading user purchase BEFORE the items were deleted?
                        if ($_di = json_decode($_bundle['bundle_deleted_list'], true)) {
                            $_de = array();
                            foreach ($_di as $m => $_inf) {
                                foreach ($_inf as $did => $_dat) {
                                    $_de[] = "(del_module = '" . jrCore_db_escape($m) . "' AND del_item_id = " . intval($did) . ")";
                                }
                            }
                            if (count($_de) > 0) {
                                $tbl = jrCore_db_table_name('jrBundle', 'deleted');
                                $req = "SELECT * FROM {$tbl} WHERE " . implode(' OR ', $_de) . " AND del_time > " . intval($_queue['_register']['r_created']);
                                $_cn = jrCore_db_query($req, 'NUMERIC');
                                if ($_cn && is_array($_cn)) {
                                    // We have items that were deleted AFTER we purchased - include in ZIP
                                    // NOTE: We have to give this bundle a UNIQUE name since it is different
                                    foreach ($_cn as $k => $i) {
                                        $mod   = $i['del_module'];
                                        $iid   = (int) $i['del_item_id'];
                                        $fld   = $_di[$mod][$iid][0];
                                        $dat   = json_decode($i['del_data'], true);
                                        $_fl[] = array($mod, $iid, $fld, $dat);
                                    }
                                    $add = "_" . $_queue['_register']['r_purchase_user_id'];
                                }
                            }

                        }
                    }
                    if (count($_fl) > 0) {

                        // Build our ZIP file from the vault
                        $cdr = jrCore_get_module_cache_dir('jrBundle') . '/' . jrCore_create_unique_string(6);
                        if (is_dir($cdr)) {
                            jrCore_delete_dir_contents($cdr);
                        }
                        else {
                            mkdir($cdr, $_conf['jrCore_dir_perms'], true);
                        }
                        $pad = 2;
                        if (count($_fl) > 99) {
                            $pad = 3;
                        }
                        $_zp = array();
                        $dir = jrCore_get_media_directory('system');
                        $_ln = jrUser_load_lang_strings($_conf['jrUser_default_language']);
                        foreach ($_fl as $k => $_file) {
                            $mod = $_file[0];
                            $iid = $_file[1];
                            $fld = $_file[2];
                            $_it = $_file[3];
                            $nam = "jrPayment_vault_{$mod}_{$iid}_{$fld}." . $_it["{$fld}_extension"];
                            if (jrCore_confirm_media_file_is_local('system', $nam)) {
                                $idx = str_pad(($k + 1), $pad, '0', STR_PAD_LEFT);
                                $new = $nam;
                                $pfx = jrCore_db_get_prefix($mod);
                                if (isset($_it["{$pfx}_title_url"])) {
                                    $new = $_it["{$pfx}_title_url"];
                                }
                                elseif (isset($_it["{$pfx}_title"])) {
                                    $new = jrCore_url_string($_it["{$pfx}_title_url"]);
                                }
                                elseif (isset($_it["{$fld}_original_name"])) {
                                    $new = jrCore_str_to_lower($_it["{$fld}_original_name"]);
                                    $new = pathinfo($new, PATHINFO_FILENAME);
                                }
                                elseif (isset($_it["{$fld}_name"])) {
                                    $new = jrCore_str_to_lower($_it["{$fld}_name"]);
                                    $new = pathinfo($new, PATHINFO_FILENAME);
                                }
                                // Strip file extension and add in
                                $new = ((isset($_ln[$mod]['menu'])) ? $_ln[$mod]['menu'] : jrCore_get_module_url($mod)) . ' - ' . str_replace('_', ' ', $new) . '.' . $_it["{$fld}_extension"];
                                $new = "@{$_it['profile_url']} - {$new}";

                                // Let modules take over naming if needed
                                $_rt = jrCore_trigger_event('jrBundle', 'bundle_filename', array('filename' => $new), $_it, $mod);
                                if (isset($_rt['filename'])) {
                                    $new = $_rt['filename'];
                                }

                                copy("{$dir}/{$nam}", "{$cdr}/{$idx}_{$new}");
                                $_zp["{$idx} - {$new}"] = "{$cdr}/{$idx}_{$new}";
                            }
                        }
                        if (count($_zp) > 0) {
                            $zip = "{$cdr}/jrBundle_{$bid}{$add}.zip";
                            jrCore_create_zip_file($zip, $_zp);
                            if (is_file($zip)) {
                                jrCore_copy_media_file('system', $zip, basename($zip));
                            }
                            @unlink("{$cdr}.zip");
                        }
                        jrCore_delete_dir_contents($cdr);
                        rmdir($cdr);
                    }
                }
            }
            jrCore_delete_global_lock('jrBundle', $lck);
        }
        else {
            // Unable to get global lock - try again in 30 seconds
            return 30;
        }
    }
    else {
        jrCore_logger('CRI', "invalid bundle_id recieved in bundle_zip_worker", $_queue);
    }
    return true;
}

//---------------------------------
// BUNDLE ITEM BUTTONS
//---------------------------------

/**
 * Return "bundle" button for the item
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrBundle_item_bundle_button($module, $_item, $_args, $smarty, $test_only = false)
{
    // We can't have bundles of bundles
    if ($module == 'jrBundle' || !jrCore_module_is_active('jrPayment')) {
        return false;
    }
    if (!jrPayment_is_supported_payment_module($module)) {
        // This module is not supported by the payments system
        return false;
    }
    if ($test_only) {
        return true;
    }
    $_rp = array(
        'module' => $module,
        'item'   => $_item,
        'field'  => $_args['field']
    );
    return smarty_function_jrBundle_button($_rp, $smarty);
}

/**
 * Allow Downloads of bundles for free bundles and admin users
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return mixed
 */
function jrBundle_download_button($module, $_item, $_args, $smarty, $test_only = false)
{
    global $_conf;
    // We can't have bundles of bundles
    if ($module != 'jrBundle') {
        return false;
    }
    if ($test_only) {
        return true;
    }

    // Is this a paid bundle?
    if (isset($_item['bundle_item_price']) && $_item['bundle_item_price'] > 0) {
        // Only admins and profile owners can download a paid bundle
        if (!jrUser_can_edit_item($_item)) {
            return false;
        }
    }

    $url = jrCore_get_module_url('jrBundle');
    return array(
        'url'  => "{$_conf['jrCore_base_url']}/{$url}/download/{$_item['_item_id']}",
        'icon' => 'download',
        'alt'  => 51
    );
}

/**
 * Return "add to cart" button for a bundle
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrBundle_add_to_cart_button($module, $_item, $_args, $smarty, $test_only = false)
{
    // We will get the module, profile_id and bundle_name
    if ($module == 'jrBundle' || !jrCore_module_is_active('jrPayment')) {
        return false;
    }
    if (!jrPayment_is_supported_payment_module($module)) {
        // This module is not supported by the payments system
        return false;
    }
    if ($test_only) {
        return true;
    }
    $pid = (int) $_args['profile_id'];
    $_sp = array(
        'search'         => array(
            "_profile_id = {$pid}",
            "bundle_module = {$module}",
            "bundle_title = {$_args['bundle_name']}"
        ),
        'limit'          => 1,
        'ignore_pending' => true,
        'privacy_check'  => false
    );
    $_sp = jrCore_db_search_items('jrBundle', $_sp);
    if ($_sp && is_array($_sp) && isset($_sp['_items'])) {
        $_args['field'] = 'bundle';
        return jrPayment_item_cart_button('jrBundle', $_sp['_items'][0], $_args, $smarty, $test_only);
    }
    return false;
}

//---------------------------------
// EVENT LISTENERS
//---------------------------------

/**
 * Cleanup schema on system reset
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrBundle_reset_system_listener($_data, $_user, $_conf, $_args, $event)
{
    $tbl = jrCore_db_table_name('jrBundle', 'deleted');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");
    jrCore_db_query("OPTIMIZE TABLE {$tbl}");
    return $_data;
}

/**
 * Watch for add to cart buttons for bundle items
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return mixed
 */
function jrBundle_add_to_cart_onclick_listener($_data, $_user, $_conf, $_args, $event)
{
    // We don't work on our self
    if (isset($_data['module']) && $_data['module'] == 'jrBundle' && (!isset($_data['item']['bundle_item_price']) || $_data['item']['bundle_item_price'] <= 0)) {
        $_data['onclick'] = 'hide';
        return $_data;
    }
    // Make sure we're good
    if (isset($_data['module']) && $_data['module'] != 'jrBundle') {
        if (isset($_data['item']) && is_array($_data['item']) && !isset($_args['no_bundle'])) {
            // Does this item have a BUNDLE?
            $mod = $_data['module'];
            $fld = $_data['field'];
            $iid = (int) $_data['item']['_item_id'];
            if (isset($_data['item']["{$fld}_item_bundle"])) {
                // This item is included in a BUNDLE - replace with our custom add to cart JS
                jrCore_set_flag('add_bundle_display_box', 1);
                $_data['onclick'] = "jrBundle_display_bundles(this,'{$mod}-{$fld}-{$iid}')";
            }
        }
    }
    return $_data;
}

/**
 * Add bundle display box when needed
 * @param $_data mixed Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return mixed
 */
function jrBundle_view_results_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_get_flag('add_bundle_display_box') && strpos($_data, '</body>')) {
        $html = '<div class="bundle_drop_down bundle_box" style="display:none"></div></body>';
        jrCore_delete_flag('add_bundle_display_box');
        return preg_replace(',</body>,', $html, $_data, 1);
    }
    return $_data;
}

/**
 * Format bundle entry in transaction detail
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrBundle_txn_detail_entry_listener($_data, $_user, $_conf, $_args, $event)
{
    $_data[1]['title'] = jrCore_get_module_icon_html('jrBundle', 48, 'payment-icon');
    // Do we have a bundle image?
    if (isset($_args['r_item_data']) && is_array($_args['r_item_data']) && isset($_args['r_item_data']['bundle_image_size']) && jrCore_checktype($_args['r_item_data']['bundle_image_size'], 'number_nz')) {
        // We have a bundle image
        $_im               = array(
            'crop'   => 'auto',
            'alt'    => $_args['r_item_data']['bundle_title'],
            'title'  => $_args['r_item_data']['bundle_title'],
            'width'  => 48,
            'height' => 48,
            '_v'     => $_args['r_item_data']['bundle_image_time']
        );
        $_data[1]['title'] = jrImage_get_image_src('jrBundle', "bundle_image", $_args['r_item_data']['_item_id'], 'small', $_im);
    }
    return $_data;
}

/**
 * Format bundle entry in payment entries
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrBundle_payment_entry_listener($_data, $_user, $_conf, $_args, $event)
{
    $_data[1]['title'] = jrCore_get_module_icon_html('jrBundle', 48, 'payment-icon');
    // Do we have a bundle image?
    if (isset($_args['r_item_data']) && is_array($_args['r_item_data']) && isset($_args['r_item_data']['bundle_image_size']) && jrCore_checktype($_args['r_item_data']['bundle_image_size'], 'number_nz')) {
        // We have a bundle image
        $_im               = array(
            'crop'   => 'auto',
            'alt'    => $_args['r_item_data']['bundle_title'],
            'title'  => $_args['r_item_data']['bundle_title'],
            'width'  => 48,
            'height' => 48,
            '_v'     => $_args['r_item_data']['bundle_image_time']
        );
        $_data[1]['title'] = jrImage_get_image_src('jrBundle', "bundle_image", $_args['r_item_data']['_item_id'], 'small', $_im);
    }
    return $_data;
}

/**
 * Format bundle purchase in user purchases
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrBundle_purchase_entry_listener($_data, $_user, $_conf, $_args, $event)
{
    $_data[1]['title'] = jrCore_get_module_icon_html('jrBundle', 48, 'payment-icon');
    // Do we have a bundle image?
    if (isset($_args['r_item_data']) && is_array($_args['r_item_data']) && isset($_args['r_item_data']['bundle_image_size']) && jrCore_checktype($_args['r_item_data']['bundle_image_size'], 'number_nz')) {
        // We have a bundle image
        $_im               = array(
            'crop'   => 'auto',
            'alt'    => $_args['r_item_data']['bundle_title'],
            'title'  => $_args['r_item_data']['bundle_title'],
            'width'  => 48,
            'height' => 48,
            '_v'     => $_args['r_item_data']['bundle_image_time']
        );
        $_data[1]['title'] = jrImage_get_image_src('jrBundle', "bundle_image", $_args['r_item_data']['_item_id'], 'small', $_im);
    }
    if (!$index = jrCore_get_flag('jrbundle_purchase_index')) {
        $index = 1;
    }
    $_ln = jrUser_load_lang_strings();
    jrCore_set_flag('jrbundle_purchase_index', ($index + 1));
    $_data[5]['title'] = jrCore_page_button("content-" . $index, $_ln['jrBundle'][51], "jrCore_window_location('{$_conf['jrCore_base_url']}/" . jrCore_get_module_url('jrBundle') . "/check_zip/id={$_args['r_id']}')");
    return $_data;
}

/**
 * Download ZIP file of bundle contents
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrBundle_vault_download_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrBundle') {
        // We have a download request for a bundle ZIP file
        $dir = jrCore_get_media_directory('system');

        // First - we have to check if this user purchased a bundle that
        // contains items that were deleted
        $zip = false;
        if (isset($_data['bundle_deleted_list']) && strlen($_data['bundle_deleted_list']) > 0) {
            // This bundle contains DELETED items - get them and see if any of
            // the items were deleted AFTER this user bought this bundle
            if ($_di = json_decode($_data['bundle_deleted_list'], true)) {
                $_de = array();
                foreach ($_di as $m => $_inf) {
                    foreach ($_inf as $did => $_dat) {
                        $_de[] = "(del_module = '" . jrCore_db_escape($m) . "' AND del_item_id = " . intval($did) . ")";
                    }
                }
                if (count($_de) > 0) {
                    $tbl = jrCore_db_table_name('jrBundle', 'deleted');
                    $req = "SELECT * FROM {$tbl} WHERE " . implode(' OR ', $_de) . " AND del_time > " . intval($_args['_register']['r_created']);
                    $_cn = jrCore_db_query($req, 'NUMERIC');
                    if ($_cn && is_array($_cn)) {
                        // We have items that were deleted AFTER we purchased - must have a unique ZIP file
                        $zip = "{$dir}/jrBundle_{$_data['_item_id']}_{$_user['_user_id']}.zip";
                    }
                }
            }
        }
        if (!$zip) {
            $zip = "{$dir}/jrBundle_{$_data['_item_id']}.zip";
        }
        if (!is_file($zip)) {

            // We've not been created yet - create queue so it gets created
            // and redirect user to let them know it is being built for them
            jrBundle_rebuild_bundle($_data['_item_id'], $_args['_register']);

            $murl = jrCore_get_module_url('jrBundle');
            jrCore_location("{$_conf['jrCore_base_url']}/{$murl}/zip_in_progress");

        }
        $_data['vault_file']  = basename($zip);
        $_data['vault_name']  = $_data['bundle_title_url'] . '.zip';
        $_data['_profile_id'] = 'system';
    }
    return $_data;
}

/**
 * Exclude Core create button from item index
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrBundle_exclude_item_index_buttons_listener($_data, $_user, $_conf, $_args, $event)
{
    // Exclude the core provided create button
    $_data['jrCore_item_create_button'] = true;
    return $_data;
}

/**
 * Prevent downloads of FREE Bundle Only files
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrBundle_download_file_listener($_data, $_user, $_conf, $_args, $event)
{
    // See if this item is marked as "bundle only"
    if (!jrUser_is_admin()) {
        $pfx = jrCore_db_get_prefix($_args['module']);
        if ($pfx && !jrUser_can_edit_item($_data) && isset($_data["{$pfx}_bundle_only"]) && $_data["{$pfx}_bundle_only"] == 'on' && (!isset($_data["{$pfx}_item_price"]) || $_data["{$pfx}_item_price"] == 0)) {
            jrCore_notice('Error', 'Downloads of Bundle Only Items are blocked');
        }
    }
    return $_data;
}

/**
 * Stream sample files for items that are bundle only
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrBundle_stream_file_listener($_data, $_user, $_conf, $_args, $event)
{
    // See if this item is marked as "bundle only"
    $pfx = jrCore_db_get_prefix($_args['module']);
    if ($pfx) {

        // Check for album price
        $fld = $_args['file_name'];
        if (isset($_data["{$pfx}_album_bundle_price"]) && strlen($_data["{$pfx}_album_bundle_price"]) > 0) {
            // Check for sample file
            $nam = "{$_args['module']}_{$_data['_item_id']}_{$fld}." . $_data["{$fld}_extension"];
            if (jrCore_media_file_exists($_data['_profile_id'], "{$nam}.sample." . $_data["{$fld}_extension"])) {
                $_data['stream_file'] = "{$nam}.sample." . $_data["{$fld}_extension"];
            }
        }

        // Check for inclusion in a paid bundle
        elseif (isset($_data["{$fld}_item_bundle"]) && strlen($_data["{$fld}_item_bundle"]) > 0) {
            $_id = array();
            foreach (explode(',', $_data["{$fld}_item_bundle"]) as $bid) {
                $_id[] = (int) $bid;
            }
            $_bi = jrCore_db_get_multiple_items('jrBundle', $_id, array('bundle_item_price'));
            if ($_bi && is_array($_bi)) {
                foreach ($_bi as $_bun) {
                    if (isset($_bun['bundle_item_price']) && $_bun['bundle_item_price'] > 0) {
                        // Check for sample file
                        $nam = "{$_args['module']}_{$_data['_item_id']}_{$fld}." . $_data["{$fld}_extension"];
                        if (jrCore_media_file_exists($_data['_profile_id'], "{$nam}.sample." . $_data["{$fld}_extension"])) {
                            $_data['stream_file'] = "{$nam}.sample." . $_data["{$fld}_extension"];
                        }
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * Add share data to a jrOneAll network share
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return mixed
 */
function jrBundle_network_share_text_listener($_data, $_user, $_conf, $_args, $event)
{
    // $_data:
    // [providers] => twitter
    // [user_token] => c6418e9a-b66e-4c6c-xxxx-cdea7e915d03
    // [user_id] => 1
    // [action_module] => jrBundle
    // [action_data] => (JSON array of data for item initiating action)
    $_data = json_decode($_data['action_data'], true);
    if (!isset($_data) || !is_array($_data)) {
        return false;
    }
    $_ln = jrUser_load_lang_strings($_data['user_language']);

    // We return an array:
    // 'text' => text to post (i.e. "tweet")
    // 'url'  => URL to media item,
    // 'name' => name if media item
    $url = jrCore_get_module_url('jrBundle');
    $txt = $_ln['jrBundle'][40];
    if ($_data['action_mode'] == 'update') {
        $txt = $_ln['jrBundle'][41];
    }
    $_out = array(
        'text' => "{$_conf['jrCore_base_url']}/{$_data['profile_url']} {$_data['profile_name']} {$txt}: \"{$_data['bundle_title']}\" {$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['_item_id']}/{$_data['bundle_title_url']}",
        'link' => array(
            'url'  => "{$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['_item_id']}/{$_data['bundle_title_url']}",
            'name' => $_data['bundle_title']
        )
    );
    // See if they included a picture with the song
    if (isset($_data['bundle_image_size']) && jrCore_checktype($_data['bundle_image_size'], 'number_nz')) {
        $_out['picture'] = array(
            'url' => "{$_conf['jrCore_base_url']}/{$url}/image/bundle_image/{$_data['_item_id']}/large"
        );
    }
    return $_out;
}

/**
 * Expands the bundle_list array out to a full item list
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrBundle_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_is_view_request() && isset($_args['module']) && $_args['module'] == 'jrBundle' && isset($_data['_items']) && is_array($_data['_items'])) {
        foreach ($_data['_items'] as $k => $v) {
            $total = 0;
            if (isset($v['bundle_list']) && strlen($v['bundle_list']) > 0) {
                $_tp  = array();
                $_pl  = array();
                $_si  = array();
                $_md  = array();
                $_px  = array();
                $list = json_decode($v['bundle_list'], true);
                if ($list && is_array($list)) {
                    $num = 0;
                    $_ln = jrUser_load_lang_strings();
                    foreach ($list as $module => $items) {

                        if (count($items) > 0) {
                            // See if this module provides a bundle template
                            if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$module}_item_bundle.tpl")) {
                                $_tp[$module] = APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$module}_item_bundle.tpl";
                            }
                            elseif (is_file(APP_DIR . "/modules/{$module}/templates/item_bundle.tpl")) {
                                $_tp[$module] = APP_DIR . "/modules/{$module}/templates/item_bundle.tpl";
                            }
                            $pfx = jrCore_db_get_prefix($module);
                            // Get info about these items for our template
                            $_sp = array(
                                'search' => array(
                                    "_item_id in " . implode(',', array_keys($items))
                                ),
                                'limit'  => count($items)
                            );
                            if (isset($_args['bundle_only']) && $_args['bundle_only'] === false) {
                                $_sp['bundle_only'] = false;
                            }
                            $_rt = jrCore_db_search_items($module, $_sp);
                            if ($_rt && is_array($_rt) && isset($_rt['_items']) && is_array($_rt['_items'])) {
                                // Place each entry in it's proper output order
                                foreach ($_rt['_items'] as $n => $_item) {
                                    if (isset($_item["{$pfx}_file_item_price"]) && $_item["{$pfx}_file_item_price"] > 0) {
                                        $total += $_item["{$pfx}_file_item_price"];
                                    }

                                    // Get menu title
                                    $mod_ttl = '';
                                    if (isset($_ln[$module]['menu'])) {
                                        $mod_ttl = $_ln[$module]['menu'] . ': ';
                                    }

                                    if (isset($_item["{$pfx}_bundle_title"])) {
                                        $item_ttl = $_item["{$pfx}_bundle_title"];
                                    }
                                    elseif (isset($_item["{$pfx}_title"])) {
                                        $item_ttl = $_item["{$pfx}_title"];
                                    }
                                    elseif (isset($_item["{$pfx}_name"])) {
                                        $item_ttl = $_item["{$pfx}_name"];
                                    }
                                    else {
                                        $item_ttl = '?';
                                    }

                                    // Get our stacked image module's and item id's
                                    $_si[]                    = $_item['_item_id'];
                                    $_md[]                    = $module;
                                    $_px[]                    = "{$pfx}_image";
                                    $ord                      = $items["{$_item['_item_id']}"][1];
                                    $_item['bundle_module']   = $module;
                                    $_item['item_title']      = $mod_ttl . $item_ttl;
                                    $url                      = jrCore_get_module_url($module);
                                    $_item['item_url']        = "{$_conf['jrCore_base_url']}/{$_item['profile_url']}/{$url}/{$_item['_item_id']}/" . jrCore_url_string($item_ttl);
                                    $_item['item_image_type'] = "{$pfx}_image";
                                    $_item['bundle_only']     = (isset($_item["{$pfx}_bundle_only"])) ? $_item["{$pfx}_bundle_only"] : 'off';

                                    if (!isset($_pl[$ord])) {
                                        $_pl[$ord] = $_item;
                                    }
                                    else {
                                        // Looks like we have items without an order set
                                        $ord       = (1 + $n);
                                        $_pl[$ord] = $_item;
                                    }
                                    $num++;
                                }
                            }
                        }
                    }
                }
                if (count($_pl) > 0) {
                    ksort($_pl, SORT_NUMERIC);
                    $_data['_items'][$k]['bundle_items']     = $_pl;
                    $_data['_items'][$k]['bundle_templates'] = $_tp;
                    if (isset($num) && $num != $_data['_items'][$k]['bundle_count']) {
                        // We've had a change in our item count - update
                        $_dt = array(
                            'bundle_count' => $num
                        );
                        jrCore_db_update_item('jrBundle', $_data['_items'][$k]['_item_id'], $_dt);
                    }
                }
                if (count($_si) > 0) {
                    $_si                                          = array_slice($_si, 0, 3, true);
                    $_data['_items'][$k]['stacked_image_module']  = implode(',', $_md);
                    $_data['_items'][$k]['stacked_image_type']    = implode(',', $_px);
                    $_data['_items'][$k]['stacked_image_item_id'] = implode(',', array_reverse($_si));
                    unset($_si, $_md, $_px);
                }
            }

            // Figure our bundle savings if items in this bundle have prices
            if (isset($v['bundle_item_price']) && $v['bundle_item_price'] > 0 && isset($total) && $total > 0) {
                if ($total > $v['bundle_item_price']) {
                    $_data['_items'][$k]['bundle_item_savings'] = ($total - $v['bundle_item_price']);
                }
            }
        }
    }
    return $_data;
}

/**
 * Expands the bundle_list array out to a full item list
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrBundle_db_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_is_view_request() && isset($_args['module']) && $_args['module'] == 'jrBundle' && !empty($_data['bundle_list']) && !jrCore_get_flag('jrbundle_skip_trigger')) {
        $_tp  = array();
        $_pl  = array();
        $_si  = array();
        $_px  = array();
        $list = json_decode($_data['bundle_list'], true);
        if ($list && is_array($list)) {
            $num = 0;
            foreach ($list as $module => $items) {

                if (count($items) > 0) {
                    // See if this module provides a bundle template
                    if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$module}_item_bundle.tpl")) {
                        $_tp[$module] = APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$module}_item_bundle.tpl";
                    }
                    elseif (is_file(APP_DIR . "/modules/{$module}/templates/item_bundle.tpl")) {
                        $_tp[$module] = APP_DIR . "/modules/{$module}/templates/item_bundle.tpl";
                    }
                    else {
                        // No template - skip
                        continue;
                    }
                    $pfx = jrCore_db_get_prefix($module);
                    // Get info about these items for our template
                    $_sp = array(
                        'search'      => array(
                            "_item_id in " . implode(',', array_keys($items))
                        ),
                        'bundle_only' => false,
                        'limit'       => count($items)
                    );
                    $_rt = jrCore_db_search_items($module, $_sp);
                    if ($_rt && is_array($_rt) && isset($_rt['_items']) && is_array($_rt['_items'])) {
                        // Place each entry in it's proper output order
                        foreach ($_rt['_items'] as $n => $_item) {
                            // Get our stacked image module's and item id's
                            if (!isset($_si[$module])) {
                                $_si[$module] = $_item['_item_id'];
                                $_px[$module] = "{$pfx}_image";
                            }
                            $ord                      = $items["{$_item['_item_id']}"][1];
                            $_item['bundle_module']   = $module;
                            $_item['item_title']      = $_item["{$pfx}_title"];
                            $url                      = jrCore_get_module_url($module);
                            $_item['item_url']        = "{$_conf['jrCore_base_url']}/{$_item['profile_url']}/{$url}/{$_item['_item_id']}/" . $_item["{$pfx}_title_url"];
                            $_item['item_image_type'] = "{$pfx}_image";
                            $_item['bundle_only']     = (isset($_item["{$pfx}_bundle_only"])) ? $_item["{$pfx}_bundle_only"] : 'off';
                            if (!isset($_pl[$ord])) {
                                $_pl[$ord] = $_item;
                            }
                            else {
                                // Looks like we have items without an order set
                                $ord       = (1 + $n);
                                $_pl[$ord] = $_item;
                            }
                            $num++;
                        }
                    }
                }
            }
        }
        if (count($_pl) > 0) {
            ksort($_pl, SORT_NUMERIC);
            $_data['bundle_items']     = $_pl;
            $_data['bundle_templates'] = $_tp;
            if (isset($num) && $num != $_data['bundle_count']) {
                // We've had a change in our item count - update
                $_dt = array(
                    'bundle_count' => $num
                );
                jrCore_db_update_item('jrBundle', $_data['_item_id'], $_dt);
            }
        }
        if (isset($_si) && is_array($_si)) {
            $_si                            = array_slice($_si, 0, 3, true);
            $_data['stacked_image_module']  = implode(',', array_keys($_si));
            $_data['stacked_image_type']    = implode(',', array_slice($_px, 0, 3));
            $_data['stacked_image_item_id'] = implode(',', array_reverse($_si));
        }
    }
    return $_data;
}

/**
 * Remove bundle ZIP files that contain an item when updated
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrBundle_save_media_file_listener($_data, $_user, $_conf, $_args, $event)
{
    // Are we getting an updated item that is going to have a NEW file?
    $_tm = jrCore_get_registered_module_features('jrFoxyCartBundle', 'visible_support');
    if (isset($_tm["{$_args['module']}"])) {
        // We are a supported module - see if this item is part of a bundle
        if ($_it = jrCore_db_get_item($_args['module'], $_args['unique_id'])) {
            $fld = $_args['file_name'];
            if (isset($_it["{$fld}_item_bundle"])) {
                // This item is included in bundles - delete so they are rebuilt
                foreach (explode(',', $_it["{$fld}_item_bundle"]) as $bid) {
                    jrBundle_delete_bundle_zip_files($bid);
                }
            }
        }
    }
    return $_data;
}

/**
 * Cleanup Bundles that include an item that is being deleted
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrBundle_db_update_item_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    // We need to watch for the "order" of a bundle being updated
    if (isset($_post['module']) && $_post['module'] != 'jrBundle' && isset($_post['option']) && $_post['option'] == 'order_update') {
        // Since we are always only ordering a SINGLE bundle, we only check for the FIRST item
        $key = 'jrbundle_updated_item_bundle_id';
        if (!jrCore_get_flag($key)) {
            // We have NOT got our bundle id yet - grab info on this item
            if ($_it = jrCore_db_get_item($_args['module'], $_args['_item_id'], true)) {
                // This item exists - delete bundle ZIPs so they are rebuilt
                foreach ($_it as $k => $v) {
                    if (strpos($k, '_item_bundle')) {
                        foreach (explode(',', $v) as $i) {
                            jrBundle_delete_bundle_zip_files($i);
                        }
                        return $_data;
                    }
                }
            }
            jrCore_set_flag($key, 1);
        }
    }
    return $_data;
}

/**
 * Cleanup Bundles that include an item that is being deleted
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrBundle_db_delete_item_listener($_data, $_user, $_conf, $_args, $event)
{
    // Find any bundles that contain this item
    if (isset($_args['module']) && strlen($_args['module']) > 0) {
        switch ($_args['module']) {

            case 'jrUser':
            case 'jrProfile':
                // We do nothing for these modules
                break;

            case 'jrBundle':
                // When a bundle is deleted we have to remove it from
                // the item_bundle field for the items contained in the bundle
                jrBundle_delete_bundle_id_from_items($_data);
                break;

            default:
                // has this module registered for bundle support?
                $_tm = jrCore_get_registered_module_features('jrFoxyCartBundle', 'visible_support');
                if ($_tm && isset($_tm["{$_args['module']}"])) {

                    // We need to round up bundles that have included this item and
                    // remove the item from the bundle.  If it is the LAST item in
                    // the bundle, we also delete the bundle.
                    $iid = (int) $_args['_item_id'];
                    $_rt = array(
                        'search'         => array(
                            'bundle_list like %"' . $_args['module'] . '"%',
                            'bundle_list like %"' . $iid . '"%'
                        ),
                        'return_keys'    => array('_item_id', 'bundle_list', 'bundle_vault_download_count', 'bundle_deleted_list'),
                        'limit'          => 500,
                        'skip_triggers'  => true,
                        'privacy_check'  => false,
                        'ignore_pending' => true
                    );
                    $_rt = jrCore_db_search_items('jrBundle', $_rt);
                    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

                        // We have bundles that include this module and MIGHT include this item
                        $_up = array();
                        $_dl = array();
                        foreach ($_rt['_items'] as $k => $_v) {
                            if (isset($_v['bundle_list']) && strlen($_v['bundle_list']) > 0) {
                                $_tm = json_decode($_v['bundle_list'], true);
                                if (isset($_tm["{$_args['module']}"][$iid])) {

                                    $bid = (int) $_v['_item_id'];
                                    // We've found a bundle that contains this item
                                    // If this bundle has been SOLD already, then it means
                                    // we have to record the time this item was deleted for use in
                                    // bundles of users that purchased it BEFORE it was deleted
                                    $_up[$bid] = array();
                                    if (isset($_v['bundle_vault_download_count']) && $_v['bundle_vault_download_count'] > 0) {
                                        // This bundle has vault downloads - items have sold
                                        // save as a deleted item in our bundle
                                        $_di = array();
                                        if (isset($_v['bundle_deleted_list']) && strlen($_v['bundle_deleted_list']) > 0) {
                                            $_di = json_decode($_v['bundle_deleted_list'], true);
                                        }
                                        // contains field, order
                                        $_di["{$_args['module']}"][$iid]  = $_tm["{$_args['module']}"][$iid];
                                        $_up[$bid]['bundle_deleted_list'] = json_encode($_di);
                                        unset($_di);

                                        // Add item info to deleted table
                                        if ($_pr = jrCore_db_get_item('jrProfile', $_data['_profile_id'])) {
                                            // Add in profile data if we have it - used in download file naming
                                            $dat = json_encode(array_merge($_data, $_pr));
                                        }
                                        else {
                                            $dat = json_encode($_data);
                                        }
                                        $dat = jrCore_db_escape($dat);
                                        $tbl = jrCore_db_table_name('jrBundle', 'deleted');
                                        $req = "INSERT IGNORE INTO {$tbl} (del_module, del_item_id, del_time, del_data) VALUES ('{$_args['module']}', '{$iid}', UNIX_TIMESTAMP(), '{$dat}')";
                                        jrCore_db_query($req);
                                    }

                                    // Remove it from the bundle list
                                    unset($_tm["{$_args['module']}"][$iid]);

                                    // See if this was the LAST item in this bundle - if so, delete module
                                    if (count($_tm["{$_args['module']}"]) === 0) {
                                        unset($_tm["{$_args['module']}"]);
                                    }
                                    if (count($_tm) === 0) {
                                        // We no longer have ANY items in this bundle
                                        $_dl[$bid] = $_v;
                                    }
                                    else {
                                        $_up[$bid]['bundle_list'] = json_encode($_tm);
                                    }
                                    jrBundle_delete_bundle_zip_files($bid);
                                }
                            }
                        }
                        if (count($_dl) > 0) {
                            // We have bundles we are deleting that have sold items - save
                            // bundle info so we can handle downloads, then remove item from DS
                            foreach ($_dl as $bid => $_d) {
                                $dat = jrCore_db_escape(json_encode($_d));
                                $tbl = jrCore_db_table_name('jrBundle', 'deleted');
                                $req = "INSERT IGNORE INTO {$tbl} (del_module, del_item_id, del_time, del_data) VALUES ('jrBundle', '{$bid}', UNIX_TIMESTAMP(), '{$dat}')";
                                jrCore_db_query($req);
                            }
                            jrCore_db_delete_multiple_items('jrBundle', array_keys($_dl));
                        }
                        if (count($_up) > 0) {
                            jrCore_db_update_multiple_items('jrBundle', $_up);
                        }
                    }
                }
                break;
        }
    }
    return $_data;
}

/**
 * Adds a "price" field to forms that have requested it
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrBundle_form_display_listener($_data, $_user, $_conf, $_args, $event)
{
    // See if this user's quota allows sales
    if (!jrCore_module_is_active('jrPayment')) {
        return $_data;
    }
    if (!isset($_user['quota_jrPayment_allowed']) || $_user['quota_jrPayment_allowed'] != 'on') {
        // Not active for this quota
        return $_data;
    }
    // First - see if this view is a Bundle Price View
    $_xtra = array('module' => 'jrBundle');
    $_form = jrCore_trigger_event('jrFoxyCartBundle', 'add_bundle_price_field', array(), $_xtra);
    if ($_form && is_array($_form) && isset($_form["{$_data['form_view']}"])) {
        $mod = $_data['form_params']['module'];
        $pfx = jrCore_db_get_prefix($mod);
        if ($pfx && strlen($pfx) > 0) {
            $_lang = jrUser_load_lang_strings();

            // We've been asked to include a price field in this form
            $field = $_form["{$_data['form_view']}"]['field'];

            // See if this is a CREATE or UPDATE - if an update, we need
            // to get the existing bundle price from the datastore
            $_frm = jrCore_form_get_session();
            $bid  = false;
            $val  = false;
            $show = true;
            if (isset($_frm['form_params']['values']) && is_array($_frm['form_params']['values'])) {
                // It's an update...
                if (isset($_frm['form_params']['values']["{$field}_item_bundle"]) && jrCore_checktype($_frm['form_params']['values']["{$field}_item_bundle"], 'number_nz')) {
                    $_bi = jrCore_db_get_item('jrBundle', $_frm['form_params']['values']["{$field}_item_bundle"]);
                    if ($_bi && is_array($_bi)) {
                        $val = $_bi['bundle_item_price'];
                        $bid = (int) $_bi['_item_id'];
                    }
                }
                else {
                    // Can we figure out what bundle we are showing based on the title?
                    if (isset($_frm['form_params']['values']["{$pfx}_title"]) && strlen($_frm['form_params']['values']["{$pfx}_title"]) > 0) {

                        // Check for an album
                        if (isset($_frm['form_params']['values']["{$pfx}_album"])) {
                            $show = true;
                        }
                        else {
                            $_bi = array(
                                'search'         => array(
                                    "bundle_title = " . $_frm['form_params']['values']["{$pfx}_title"],
                                    "_profile_id = " . $_frm['form_params']['values']['_profile_id'],
                                    "bundle_module = {$mod}"
                                ),
                                'return_keys'    => array('_item_id', 'bundle_item_price'),
                                'skip_triggers'  => true,
                                'ignore_pending' => true,
                                'privacy_check'  => false,
                                'limit'          => 10
                            );
                            $_bi = jrCore_db_search_items('jrBundle', $_bi);
                            if ($_bi && is_array($_bi) && isset($_bi['_items'])) {
                                $val = $_bi['_items'][0]['bundle_item_price'];
                                $bid = (int) $_bi['_items'][0]['_item_id'];
                            }
                            elseif (isset($_frm['form_params']['values']["{$field}_item_bundle"]) && strlen($_frm['form_params']['values']["{$field}_item_bundle"]) > 0) {
                                // Multiple bundles for this item - don't allow price change here
                                $show = explode(',', $_frm['form_params']['values']["{$field}_item_bundle"]);
                            }
                        }
                    }
                }
            }
            if ($show === true) {
                $_tmp = array(
                    'name'     => "bundle_item_price",
                    'type'     => 'text',
                    'default'  => '',
                    'validate' => 'price',
                    'label'    => $_lang['jrBundle'][22],
                    'sublabel' => $_lang['jrBundle'][24],
                    'help'     => $_lang['jrBundle'][23],
                    'value'    => $val
                );
                jrCore_form_field_create($_tmp);
                if ($bid) {
                    $_tmp = array(
                        'name'  => "bundle_item_id",
                        'type'  => 'hidden',
                        'value' => $bid
                    );
                    jrCore_form_field_create($_tmp);
                }
            }
            else {
                if ($show && is_array($show)) {
                    jrCore_page_custom($_lang['jrBundle'][54], $_lang['jrBundle'][22]);
                }
            }
        }
    }

    // Check for Bundle Only
    if (!$field = jrPayment_get_item_price_field($_data['form_params']['module'], $_data['form_view'])) {
        // Backwards support for foxycart module
        $_temp = jrCore_trigger_event('jrFoxyCart', 'add_price_field', $_data, null, $_data['form_params']['module']);
        if ($_temp && is_array($_temp) && isset($_temp["{$_data['form_view']}"]) && !strpos($_data['form_view'], 'album')) {
            $field = $_temp["{$_data['form_view']}"] . '_item_price';
        }
    }
    if ($field) {
        // Item Visible
        $_lng = jrUser_load_lang_strings();
        $prfx = jrCore_db_get_prefix($_data['form_params']['module']);
        $_tmp = array(
            'name'          => "{$prfx}_bundle_only",
            'label'         => $_lng['jrBundle'][37],
            'help'          => $_lng['jrBundle'][38],
            'type'          => 'checkbox',
            'default'       => 'off',
            'required'      => false,
            'form_designer' => false
        );
        jrCore_form_field_create($_tmp);
    }
    return $_data;
}

/**
 * Listens for album/bundle create view saves to create bundle
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrBundle_form_result_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;

    // See if we received a bundle price in our results
    if (isset($_post['bundle_item_price'])) {

        // Get title field from event
        $_xtra = array('module' => $_data['module']);
        $_form = jrCore_trigger_event('jrFoxyCartBundle', 'add_bundle_price_field', array(), $_xtra, $_data['module']);
        if (!$_form || !is_array($_form)) {
            return $_data;
        }
        $title = false;
        $field = false;
        foreach ($_form as $mod_view => $_info) {
            // [jrAudio/create_album] => array(audio_album,audio_file)
            list($mod, $view) = explode('/', $mod_view, 2);
            if ($mod == $_data['module'] && strpos($_data['option'], $view) === 0) {
                // Found our view
                $title = $_info['title'];
                $field = $_info['field'];
                break;
            }
        }
        if ($title && $field && isset($_data[$title])) {

            // We are creating or updating a bundle!
            $price = $_post['bundle_item_price'];

            // Do we have a bundle price coming in?
            if ($price > 0) {

                if (isset($_post['bundle_item_id']) && jrCore_checktype($_post['bundle_item_id'], 'number_nz')) {

                    // This is an update to an EXISTING BUNDLE
                    $bid = (int) $_post['bundle_item_id'];

                    // Make sure our title is good
                    $_rt = array(
                        'bundle_title'      => $_data[$title],
                        'bundle_title_url'  => jrCore_url_string($_data[$title]),
                        'bundle_item_price' => $price,
                        'bundle_name'       => jrCore_url_string($_data[$title]) . '.zip'
                    );
                    jrCore_db_update_item('jrBundle', $bid, $_rt);

                    // Delete any existing bundle ZIP file so it is re-created
                    jrBundle_delete_bundle_zip_files($bid);

                }
                else {

                    // Creating a NEW bundle
                    $_rt = array(
                        'bundle_title'      => $_data[$title],
                        'bundle_title_url'  => jrCore_url_string($_data[$title]),
                        'bundle_item_price' => $price,
                        'bundle_module'     => $_data['module'],
                        'bundle_field'      => $title,
                        'bundle_name'       => jrCore_url_string($_data[$title]) . '.zip',
                        'bundle_extension'  => 'zip'
                    );
                    $bid = jrCore_db_create_item('jrBundle', $_rt);

                    // We have our bundle - we now need to update each
                    // item that is in the bundle with our bundle ID
                    if ($bid && jrCore_checktype($bid, 'number_nz')) {

                        // We've got our bundle, next we need to get items that are
                        // part of this bundle and make sure they are updated with our ID
                        $_sp = array(
                            'search'         => array(
                                "_profile_id = {$_user['user_active_profile_id']}",
                                "{$title} = {$_data[$title]}"
                            ),
                            'skip_triggers'  => true,
                            'privacy_check'  => false,
                            'ignore_pending' => true,
                            'limit'          => 500
                        );
                        $_it = jrCore_db_search_items($_data['module'], $_sp);
                        if ($_it && is_array($_it) && isset($_it['_items'])) {

                            $_id = array();
                            $_up = array();
                            foreach ($_it['_items'] as $k => $_item) {

                                $iid = (int) $_item['_item_id'];

                                // Is this item already part of any other bundles?
                                $_bn = array();
                                if (isset($_item["{$field}_item_bundle"]) && strlen($_item["{$field}_item_bundle"]) > 0) {
                                    foreach (explode(',', $_item["{$field}_item_bundle"]) as $bnid) {
                                        $bnid = (int) $bnid;
                                        if ($bnid > 0) {
                                            $_bn[$bnid] = $bnid;
                                        }
                                    }
                                }
                                $ord = $k;
                                if (!isset($_bn[$bid])) {
                                    // This item is being added to the bundle - make sure it is added to the vault
                                    jrPayment_add_file_to_vault($_data['module'], $field, $_item);
                                    if ($pfx = jrCore_db_get_prefix($_data['module'])) {
                                        if (isset($_item["{$pfx}_display_order"])) {
                                            $ord = (int) $_item["{$pfx}_display_order"];
                                        }
                                    }
                                }
                                $_bn[$bid] = $bid;
                                $_up[$iid] = array("{$field}_item_bundle" => implode(',', $_bn));

                                // Add this item to our bundle_list
                                $_id[$iid] = array($field, $ord);

                            }
                            if (count($_up) > 0) {

                                // Update items to now include our new bundle ID
                                jrCore_db_update_multiple_items($_data['module'], $_up);

                                // Update our bundle with the bundle_list and info
                                $_rt = array(
                                    'bundle_title'      => $_data[$title],
                                    'bundle_title_url'  => jrCore_url_string($_data[$title]),
                                    'bundle_list'       => json_encode(array($_data['module'] => $_id)),
                                    'bundle_count'      => count($_id),
                                    'bundle_item_price' => $price
                                );
                                jrCore_db_update_item('jrBundle', $bid, $_rt);

                            }
                        }
                    }
                }

            }
            else {

                // See if we are UPDATING a bundle
                if (isset($_post['bundle_item_id']) && jrCore_checktype($_post['bundle_item_id'], 'number_nz')) {

                    $bid = (int) $_post['bundle_item_id'];

                    // Make sure our title is good
                    $_rt = array(
                        'bundle_title'     => $_data[$title],
                        'bundle_title_url' => jrCore_url_string($_data[$title])
                    );
                    jrCore_db_update_item('jrBundle', $bid, $_rt);

                    // Remove price
                    jrCore_db_delete_item_key('jrBundle', $bid, 'bundle_item_price');
                }
            }
        }
    }
    return $_data;
}

//---------------------------------
// FUNCTIONS
//---------------------------------

/**
 * Will delete a bundle_id from items contained in the bundle
 * @param array $_bundle
 * @return bool
 */
function jrBundle_delete_bundle_id_from_items($_bundle)
{
    // When a bundle is deleted we have to remove it from
    // the item_bundle field for the items contained in the bundle
    if (isset($_bundle['bundle_list']) && strlen($_bundle['bundle_list']) > 0) {
        $_it = json_decode($_bundle['bundle_list'], true);
        if ($_it && is_array($_it)) {
            $iid = (int) $_bundle['_item_id'];
            foreach ($_it as $mod => $_ids) {
                if ($_tm = jrCore_db_get_multiple_items($mod, array_keys($_ids))) {
                    // These items exist - remove from the item_bundle key
                    $_dl = array();
                    $_up = array();
                    $_cr = array();
                    $key = false;
                    foreach ($_tm as $i) {
                        $val = false;
                        $uid = (int) $i['_item_id'];
                        foreach ($i as $k => $v) {
                            if (strpos($k, '_item_bundle')) {
                                // We found our item bundle key
                                if (!$key) {
                                    $key = $k;
                                }
                                $val = explode(',', $v);
                                break;
                            }
                        }
                        if ($val) {
                            $val = array_flip($val);
                            unset($val[$iid], $val[0]);
                            if (count($val) > 0) {
                                // We still have existing bundles on this item - update
                                $_up[$uid] = array($key => implode(',', array_keys($val)));
                                $_cr[$uid] = array('_updated' => $i['_updated']);
                            }
                        }
                        if (!isset($_up[$uid])) {
                            // This was the only bundle for this item - remove key
                            $_dl[$uid] = $uid;
                        }
                    }
                    if (count($_up) > 0) {
                        jrCore_db_update_multiple_items($mod, $_up, $_cr);
                    }
                    if ($key && count($_dl) > 0) {
                        jrCore_db_delete_key_from_multiple_items($mod, $_dl, $key);
                    }
                }
            }
        }
    }
    return true;
}

/**
 * Delete all bundle zip files for a given bundle id
 * @param int $id
 * @return bool
 */
function jrBundle_delete_bundle_zip_files($id)
{
    $iid = (int) $id;
    if ($_fl = jrCore_get_media_files('system', "jrBundle_{$iid}*")) {
        foreach ($_fl as $f) {
            jrCore_delete_media_file('system', basename($f['name']));
        }
    }
    return true;
}

/**
 * Rebuild or re-create a bundle ZIP file
 * @param int $bundle_id
 * @param array $_register Register entry
 * @return mixed
 */
function jrBundle_rebuild_bundle($bundle_id, $_register)
{
    $_queue = array(
        'bundle_id' => (int) $bundle_id,
        '_register' => $_register
    );
    return jrCore_queue_create('jrBundle', 'create_bundle_zip', $_queue);
}

/**
 * returns the info on the bundles this item is included in
 * @param $module
 * @param $item_id
 * @return array|bool
 */
function jrBundle_get_bundles($module, $item_id)
{
    $_item = jrCore_db_get_item($module, $item_id, true);
    foreach ($_item as $k => $v) {
        if (strpos($k, '_item_bundle')) {
            $_bids = explode(',', $v);
            if (is_array($_bids)) {
                foreach ($_bids as $bid) {
                    $_bundles[] = jrCore_db_get_item('jrBundle', $bid, true);
                }
            }
            break;
        }
    }
    return (isset($_bundles) && is_array($_bundles)) ? $_bundles : false;
}

//---------------------------------
// SMARTY FUNCTIONS
//---------------------------------

/**
 * shows remove from bundle button on bundle items for bundle owners
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrBundle_remove_button($params, $smarty)
{
    global $_mods, $_conf;
    if (!isset($params['module']) || !isset($_mods["{$params['module']}"])) {
        return 'jrBundle_remove_button: module required';
    }
    if (!isset($params['bundle_id']) || !jrCore_checktype($params['bundle_id'], 'number_nz')) {
        return 'jrBundle_remove_button: bundle_id required';
    }
    if (!isset($params['item']) || !is_array($params['item'])) {
        return 'jrBundle_remove_button: item array required';
    }
    // Make sure viewer has owner access to item
    if (!jrUser_can_edit_item($params['item'])) {
        return '';
    }
    $_ln = jrUser_load_lang_strings();
    $alt = addslashes($_ln['jrBundle'][31]);
    $bid = (int) $params['bundle_id'];
    if (isset($params['confirm']) && strlen($params['confirm']) > 0) {
        $onc = "if(confirm('" . addslashes($_ln['jrBundle'][32]) . "')){jrBundle_remove({$bid},'{$params['module']}',{$params['item']['_item_id']});}";
    }
    else {
        $onc = "jrBundle_remove({$bid},'{$params['module']}',{$params['item']['_item_id']});";
    }

    if (isset($params['image']{1})) {
        $src = "{$_conf['jrCore_base_url']}/skins/{$_conf['jrCore_active_skin']}/img/{$params['image']}";
        $out = "<img src=\"{$src}\" alt=\"{$alt}\" title=\"{$alt}\" style=\"cursor:pointer\" onclick=\"" . $onc . "\">";
    }
    else {
        if (!isset($params['icon'])) {
            $params['icon'] = 'close';
        }
        $out = "<a onclick=\"{$onc}\" title=\"{$alt}\">" . jrCore_get_sprite_html($params['icon']) . '</a>';
    }

    // Check for custom button image
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * List Items in a bundle
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrBundle_list_items($params, $smarty)
{
    if (!isset($params['item']) || !is_array($params['item'])) {
        return 'jrBundle_list_items: invalid item array';
    }
    $out = '';
    // If this item has a bundle_module field then we know the module to search
    if (isset($params['item']['bundle_module'])) {
        if (!jrCore_module_is_active($params['item']['bundle_module'])) {
            return '';
        }
        $_sp = array(
            'search'   => array(
                "_profile_id = {$params['item']['_profile_id']}",
                "{$params['item']['bundle_field']} = {$params['item']['bundle_title']}"
            ),
            'order_by' => array(
                '_created' => 'ASC'
            ),
            'limit'    => 250
        );
        $_it = jrCore_db_search_items($params['item']['bundle_module'], $_sp);
        if ($_it && is_array($_it) && isset($_it['_items'])) {
            $out = jrCore_parse_template('item_bundle.tpl', $_it, $params['item']['bundle_module']);
        }
    }
    else {
        // We need to go get the items associated with this bundle
        $_xtra = array('module' => 'jrPayment');
        $_form = jrCore_trigger_event('jrPayment', 'add_price_field', array(), $_xtra);
        if ($_form && is_array($_form)) {
            $_temp = array();
            $bid   = (int) $params['item']['_item_id'];
            foreach ($_form as $view => $field) {
                list($module,) = explode('/', $view);
                if (!isset($_temp[$module])) {
                    $_rt = array(
                        'search'              => array(
                            "{$field}_item_bundle = {$bid} || {$field}_item_bundle like {$bid},% || {$field}_item_bundle like %,{$bid} || {$field}_item_bundle like %,{$bid},%"
                        ),
                        'return_item_id_only' => true,
                        'skip_triggers'       => true,
                        'limit'               => 1000
                    );
                    $_rt = jrCore_db_search_items($module, $_rt);
                    if ($_rt && is_array($_rt)) {
                        $_sp = array(
                            'search'   => array(
                                '_item_id in ' . implode(',', $_rt)
                            ),
                            'order_by' => array(
                                '_created' => 'asc'
                            )
                        );
                        $_it = jrCore_db_search_items($module, $_sp);
                        if ($_it && is_array($_it) && isset($_it['_items'])) {
                            $out .= jrCore_parse_template('item_bundle.tpl', $_it, $module);
                        }
                    }
                    $_temp[$module] = 1;
                }
            }
        }
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Get a Bundle album
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrBundle_get_album($params, $smarty)
{
    if (!jrCore_module_is_active('jrBundle')) {
        return '';
    }
    if (!isset($params['module']{0})) {
        return 'jrBundle_get_album: module parameter required';
    }
    if (!isset($params['profile_id']) || strlen($params['profile_id']) === 0) {
        return 'jrBundle_get_album: profile_id parameter required';
    }
    if (!isset($params['name']) || strlen($params['name']) === 0) {
        return 'jrBundle_get_album: name parameter required';
    }
    if (!isset($params['assign']) || strlen($params['assign']) === 0) {
        return 'jrBundle_get_album: assign parameter required';
    }
    $_sp = array(
        'search'         => array(
            "_profile_id = {$params['profile_id']}",
            "bundle_module = {$params['module']}",
            "bundle_title = {$params['name']}"
        ),
        'limit'          => 1,
        'ignore_pending' => true,
        'privacy_check'  => false
    );
    $_rt = jrCore_db_search_items('jrBundle', $_sp);
    if (isset($_rt) && is_array($_rt['_items'])) {
        $smarty->assign($params['assign'], $_rt['_items'][0]);
    }
    else {
        $smarty->assign($params['assign'], false);
    }
    return '';
}

/**
 * shows an add to bundle button for logged in users.
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrBundle_button($params, $smarty)
{
    global $_conf;
    if (!jrCore_module_is_active('jrBundle') || $params['item']['quota_jrPayment_active'] == 'off' || !jrProfile_is_profile_view()) {
        return '';
    }
    if (!isset($params['module']) || !jrCore_module_is_active($params['module'])) {
        return 'jrBundle_button: module parameter required';
    }
    if (!isset($params['item']) || !is_array($params['item'])) {
        return 'jrBundle_button: item array required';
    }
    if (!isset($params['field']) || strlen($params['field']) === 0) {
        return 'jrBundle_button: field param required';
    }
    // Make sure viewer has owner access to item
    if (!jrUser_can_edit_item($params['item'])) {
        return '';
    }
    $_lang = jrUser_load_lang_strings();
    $_rep  = array(
        'module'  => $params['module'],
        'field'   => $params['field'],
        'item_id' => intval($params['item']['_item_id']),
        'uniqid'  => 'a' . uniqid(),
        'width'   => (isset($params['width']) && is_numeric($params['width'])) ? (int) $params['width'] : 32,
        'height'  => (isset($params['height']) && is_numeric($params['height'])) ? (int) $params['height'] : 32,
        'alt'     => (isset($params['alt'])) ? $params['alt'] : $_lang['jrBundle'][25],
        'title'   => $params['alt'],
        'class'   => (isset($params['class'])) ? $params['class'] : 'create_img'
    );
    if (isset($params['image'])) {
        // Check for custom button image
        $src               = "{$_conf['jrCore_base_url']}/skins/{$_conf['jrCore_active_skin']}/img/{$params['image']}";
        $_rep['icon_html'] = '<img src="' . $src . '" class="' . $_rep['class'] . '" alt="' . $_rep['alt'] . '" title="' . $_rep['alt'] . '" onclick="jrBundle_select(\'' . intval($params['item']['_item_id']) . '\',\'' . $params['field'] . '\',\'' . $params['module'] . '\',null)">';
    }
    else {
        if (!isset($params['icon'])) {
            $params['icon'] = 'bundle';
        }
        // See if we are visible - if NOT, hilight
        $pfx = jrCore_db_get_prefix($params['module']);
        if (isset($params['item']["{$pfx}_bundle_only"]) && $params['item']["{$pfx}_bundle_only"] == 'on') {
            $params['icon'] = "{$params['icon']}-hilighted";
        }
        $_rep['icon_html'] = "<a onclick=\"jrBundle_select('" . intval($params['item']['_item_id']) . "','" . $params['field'] . "','" . $params['module'] . "',null)\" title=\"{$_rep['alt']}\">" . jrCore_get_sprite_html($params['icon']) . '</a>';
    }
    $out = jrCore_parse_template('bundle_button.tpl', $_rep, 'jrBundle');
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

