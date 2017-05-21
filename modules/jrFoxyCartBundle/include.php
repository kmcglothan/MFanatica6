<?php
/**
 * Jamroom FoxyCart Bundles module
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

/**
 * meta
 */
function jrFoxyCartBundle_meta()
{
    $_tmp = array(
        'name'        => 'FoxyCart Bundles',
        'url'         => 'bundle',
        'version'     => '1.3.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Package multiple items for sale into bundles so they can be priced separately',
        'category'    => 'ecommerce',
        'requires'    => 'jrFoxyCart',
        'priority'    => 81,
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrFoxyCartBundle_init()
{
    // event triggers
    jrCore_register_event_trigger('jrFoxyCartBundle', 'add_bundle_price_field', 'Fired when a form is viewed');
    jrCore_register_event_trigger('jrFoxyCartBundle', 'get_album_field', 'Fired in create Item Bundle view');
    jrCore_register_event_trigger('jrFoxyCartBundle', 'add_bundle_item', 'Fired when an item is added to a bundle');
    jrCore_register_event_trigger('jrFoxyCartBundle', 'delete_bundle_item', 'Fired when an item is deleted from a bundle');

    // Expand bundle items
    jrCore_register_event_listener('jrCore', 'db_get_item', 'jrFoxyCartBundle_db_get_item_listener');
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrFoxyCartBundle_db_search_items_listener');
    jrCore_register_event_listener('jrCore', 'db_delete_item', 'jrFoxyCartBundle_db_delete_item_listener');

    // Add our bundle fields on form views that have a price field
    jrCore_register_event_listener('jrCore', 'form_display', 'jrFoxyCartBundle_form_display_listener');
    jrCore_register_event_listener('jrCore', 'form_result', 'jrFoxyCartBundle_form_result_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrFoxyCartBundle_verify_module_listener');
    jrCore_register_event_listener('jrCore', 'download_file', 'jrFoxyCartBundle_download_file_listener');
    jrCore_register_event_listener('jrCore', 'stream_file', 'jrFoxyCartBundle_stream_file_listener');
    jrCore_register_event_listener('jrCore', 'exclude_item_index_buttons', 'jrFoxyCartBundle_exclude_item_index_buttons_listener');

    // When we get a purchase of a bundle we will handle adding the individual items
    jrCore_register_event_listener('jrFoxyCart', 'adding_item_to_purchase_history', 'jrFoxyCartBundle_adding_item_to_purchase_history_listener');
    jrCore_register_event_listener('jrFoxyCart', 'my_earnings_row', 'jrFoxyCartBundle_my_earnings_row_listener');

    // actions
    jrCore_register_module_feature('jrCore', 'action_support', 'jrFoxyCartBundle', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrFoxyCartBundle', 'update', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'item_order_support', 'jrFoxyCartBundle', 'on');

    // When an action is shared via jrOneAll, we can provide the text of the shared item
    jrCore_register_event_listener('jrOneAll', 'network_share_text', 'jrFoxyCartBundle_network_share_text_listener');

    // Custom CSS and Javascript
    jrCore_register_module_feature('jrCore', 'javascript', 'jrFoxyCartBundle', 'jrFoxyCartBundle.js');
    jrCore_register_module_feature('jrCore', 'css', 'jrFoxyCartBundle', 'jrFoxyCartBundle.css');

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'jrFoxyCartBundle', 'profile_jrFoxyCartBundle_item_count', 1);

    // Core item buttons
    $_tmp = array(
        'title'  => 'add to bundle button',
        'icon'   => 'bundle',
        'active' => 'on',
        'group'  => 'owner'
    );
    jrCore_register_module_feature('jrCore', 'item_list_button', 'jrFoxyCartBundle', 'jrFoxyCartBundle_item_bundle_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrFoxyCartBundle', 'jrFoxyCartBundle_item_bundle_button', $_tmp);

    return true;
}

//---------------------------------------------------------
// BUNDLE ITEM BUTTONS
//---------------------------------------------------------

/**
 * Return "bundle" button for the item
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrFoxyCartBundle_item_bundle_button($module, $_item, $_args, $smarty, $test_only = false)
{
    // We can't have bundles of bundles
    if ($module == 'jrFoxyCartBundle' || !jrCore_module_is_active('jrFoxyCart')) {
        return false;
    }
    // Module must be asking for bundle support
    $_tm = jrCore_get_registered_module_features('jrFoxyCartBundle', 'visible_support');
    if (!isset($_tm[$module])) {
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
    return smarty_function_jrFoxyCartBundle_button($_rp, $smarty);
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Exclude Core create button from item index
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrFoxyCartBundle_exclude_item_index_buttons_listener($_data, $_user, $_conf, $_args, $event)
{
    // Exclude the core provided create button
    $_data['jrCore_item_create_button'] = true;
    return $_data;
}

/**
 * Prevent downloads of FREE Bundle Only files
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrFoxyCartBundle_download_file_listener($_data, $_user, $_conf, $_args, $event)
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
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrFoxyCartBundle_stream_file_listener($_data, $_user, $_conf, $_args, $event)
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
            $_bi = jrCore_db_get_multiple_items('jrFoxyCartBundle', $_id, array('bundle_item_price'));
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
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return mixed
 */
function jrFoxyCartBundle_network_share_text_listener($_data, $_user, $_conf, $_args, $event)
{
    // $_data:
    // [providers] => twitter
    // [user_token] => c6418e9a-b66e-4c6c-xxxx-cdea7e915d03
    // [user_id] => 1
    // [action_module] => jrFoxyCartBundle
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
    $url = jrCore_get_module_url('jrFoxyCartBundle');
    $txt = $_ln['jrFoxyCartBundle'][40];
    if ($_data['action_mode'] == 'update') {
        $txt = $_ln['jrFoxyCartBundle'][41];
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
function jrFoxyCartBundle_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_is_view_request() && isset($_args['module']) && $_args['module'] == 'jrFoxyCartBundle' && isset($_data['_items']) && is_array($_data['_items'])) {
        foreach ($_data['_items'] as $k => $v) {
            $total = 0;
            if (isset($v['bundle_list']) && strlen($v['bundle_list']) > 0) {
                $_tp  = array();
                $_pl  = array();
                $_si  = array();
                $_md  = array();
                $_px  = array();
                $list = json_decode($v['bundle_list'], true);
                if (isset($list) && is_array($list)) {
                    // Get all items for each module in 1 shot
                    // Our entries are stored like:
                    // module => array(
                    // 1 => 0,
                    // 5 => 1,
                    // 7 => 2
                    // i.e. item_id => bundle_order
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
                            if (isset($_rt) && is_array($_rt) && isset($_rt['_items']) && is_array($_rt['_items'])) {
                                // Place each entry in it's proper output order
                                foreach ($_rt['_items'] as $n => $_item) {
                                    if (isset($_item["{$pfx}_file_item_price"]) && $_item["{$pfx}_file_item_price"] > 0) {
                                        $total += $_item["{$pfx}_file_item_price"];
                                    }
                                    // Get our stacked image module's and item id's
                                    $_si[]                    = $_item['_item_id'];
                                    $_md[]                    = $module;
                                    $_px[]                    = "{$pfx}_image";
                                    $ord                      = $items["{$_item['_item_id']}"];
                                    $_item['bundle_module']   = $module;
                                    $_item['item_title']      = $_item["{$pfx}_title"];
                                    $url                      = jrCore_get_module_url($module);
                                    $_item['item_url']        = "{$_conf['jrCore_base_url']}/{$_item['profile_url']}/{$url}/{$_item['_item_id']}/" . $_item["{$pfx}_title_url"];
                                    $_item['item_image_type'] = "{$pfx}_image";

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
                if (isset($_pl) && is_array($_pl) && count($_pl) > 0) {
                    ksort($_pl, SORT_NUMERIC);
                    $_data['_items'][$k]['bundle_items']     = $_pl;
                    $_data['_items'][$k]['bundle_templates'] = $_tp;
                    if (isset($num) && $num != $_data['_items'][$k]['bundle_count']) {
                        // We've had a change in our item count - update
                        $_dt = array(
                            'bundle_count' => $num
                        );
                        jrCore_db_update_item('jrFoxyCartBundle', $_data['_items'][$k]['_item_id'], $_dt);
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
function jrFoxyCartBundle_db_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_is_view_request() && isset($_args['module']) && $_args['module'] == 'jrFoxyCartBundle' && !empty($_data['bundle_list'])) {
        $_tp  = array();
        $_pl  = array();
        $_si  = array();
        $_px  = array();
        $list = json_decode($_data['bundle_list'], true);
        if (isset($list) && is_array($list)) {
            // Get all items for each module in 1 shot
            // Our entries are stored like:
            // module => array(
            // 1 => 0,
            // 5 => 1,
            // 7 => 2
            // i.e. item_id => bundle_order
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
                    if (isset($_rt) && is_array($_rt) && isset($_rt['_items']) && is_array($_rt['_items'])) {
                        // Place each entry in it's proper output order
                        foreach ($_rt['_items'] as $n => $_item) {
                            // Get our stacked image module's and item id's
                            if (!isset($_si[$module])) {
                                $_si[$module] = $_item['_item_id'];
                                $_px[$module] = "{$pfx}_image";
                            }
                            $ord                      = $items["{$_item['_item_id']}"];
                            $_item['bundle_module']   = $module;
                            $_item['item_title']      = $_item["{$pfx}_title"];
                            $url                      = jrCore_get_module_url($module);
                            $_item['item_url']        = "{$_conf['jrCore_base_url']}/{$_item['profile_url']}/{$url}/{$_item['_item_id']}/" . $_item["{$pfx}_title_url"];
                            $_item['item_image_type'] = "{$pfx}_image";
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
        if (isset($_pl) && is_array($_pl) && count($_pl) > 0) {
            ksort($_pl, SORT_NUMERIC);
            $_data['bundle_items']     = $_pl;
            $_data['bundle_templates'] = $_tp;
            if (isset($num) && $num != $_data['bundle_count']) {
                // We've had a change in our item count - update
                $_dt = array(
                    'bundle_count' => $num
                );
                jrCore_db_update_item('jrFoxyCartBundle', $_data['_item_id'], $_dt);
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
 * Cleanup Bundles that include an item that is being deleted
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrFoxyCartBundle_db_delete_item_listener($_data, $_user, $_conf, $_args, $event)
{
    // Find any bundles that contain this item
    if (isset($_args['module']) && strlen($_args['module']) > 0) {
        switch ($_args['module']) {
            case 'jrUser':
            case 'jrProfile':
            case 'jrFoxyCartBundle':
                // We do nothing for these modules
                break;

            default:
                // has this module registered for bundle support?
                $_tm = jrCore_get_registered_module_features('jrFoxyCartBundle', 'visible_support');
                if (isset($_tm["{$_args['module']}"])) {

                    // We need to round up bundles that have included this item and
                    // remove the item from the bundle.  If it is the LAST item in
                    // the bundle, we also delete the bundle.
                    $iid = (int) $_args['_item_id'];
                    $_rt = array(
                        'search'         => array(
                            "bundle_list like %{$_args['module']}%"
                        ),
                        'return_keys'    => array('_item_id', 'bundle_list'),
                        'limit'          => 500,
                        'skip_triggers'  => true,
                        'privacy_check'  => false,
                        'ignore_pending' => true
                    );
                    $_rt = jrCore_db_search_items('jrFoxyCartBundle', $_rt);
                    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
                        $_up = array();
                        $_dl = array();
                        foreach ($_rt['_items'] as $k => $_v) {
                            if (isset($_v['bundle_list']) && strlen($_v['bundle_list']) > 0) {
                                $_tm = json_decode($_v['bundle_list'], true);
                                if (isset($_tm["{$_args['module']}"][$iid])) {
                                    unset($_tm["{$_args['module']}"][$iid]);
                                    // See if this was the LAST item in this bundle - if so, delete
                                    if (count($_tm["{$_args['module']}"]) === 0) {
                                        unset($_tm["{$_args['module']}"]);
                                    }
                                    if (count($_tm) === 0) {
                                        $_dl[] = $_v['_item_id'];
                                    }
                                    else {
                                        $_up["{$_v['_item_id']}"] = array('bundle_list' => json_encode($_tm));
                                    }
                                }
                            }
                        }
                        if (count($_dl) > 0) {
                            jrCore_db_delete_multiple_items('jrFoxyCartBundle', $_dl);
                        }
                        if (count($_up) > 0) {
                            jrCore_db_update_multiple_items('jrFoxyCartBundle', $_up);
                        }
                    }
                }
                break;
        }
    }
    return $_data;
}

/**
 * Removes an existing skin menu item for bundles
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrFoxyCartBundle_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    jrCore_delete_skin_menu_item('jrFoxyCartBundle', 'view_bundles');
    jrProfile_delete_quota_setting('jrFoxyCartBundle', 'allow_restrict');
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
function jrFoxyCartBundle_form_result_listener($_data, $_user, $_conf, $_args, $event)
{
    // See if we received a bundle price in our results
    if (strpos(json_encode($_data), '_bundle_price')) {

        // Get title field from event
        $_xtra = array('module' => $_data['module']);
        $_form = jrCore_trigger_event('jrFoxyCartBundle', 'add_bundle_price_field', array(), $_xtra, $_data['module']);
        if (!isset($_form) || !is_array($_form)) {
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
            $price = false;
            foreach ($_data as $k => $v) {
                // We need to get the title and price so we can
                // create the proper bundle
                if (strpos($k, '_bundle_price')) {
                    $price = $v;
                }
            }
            if ($price) {

                // See if we are UPDATING or CREATING a bundle
                $_sc = array(
                    'search'         => array(
                        "_profile_id = {$_user['user_active_profile_id']}",
                        "bundle_module = {$_data['module']}",
                        "bundle_title = {$_data[$title]}"
                    ),
                    'skip_triggers'  => true,
                    'ignore_pending' => true,
                    'privacy_check'  => false,
                    'limit'          => 1
                );
                $_ex = jrCore_db_search_items('jrFoxyCartBundle', $_sc);
                if (!$_ex || !is_array($_ex)) {
                    $_rt = array(
                        'bundle_title'      => $_data[$title],
                        'bundle_title_url'  => jrCore_url_string($_data[$title]),
                        'bundle_item_price' => $price,
                        'bundle_module'     => $_data['module'],
                        'bundle_field'      => $title
                    );
                    $bid = jrCore_db_create_item('jrFoxyCartBundle', $_rt);
                    if ($bid && jrCore_checktype($bid, 'number_nz')) {
                        // We created our bundle - we now need to update each
                        // item that was in the album with our bundle ID
                        $_sp = array(
                            'search'         => array(
                                "_profile_id = {$_user['user_active_profile_id']}",
                                "{$title} = {$_data[$title]}"
                            ),
                            'return_keys'    => array('_item_id', "{$field}_item_bundle", "{$title}_bundle_price"),
                            'skip_triggers'  => true,
                            'privacy_check'  => false,
                            'ignore_pending' => true,
                            'limit'          => 500
                        );
                        $_it = jrCore_db_search_items($_data['module'], $_sp);
                        if ($_it && is_array($_it) && isset($_it['_items'])) {
                            $_ids = array();
                            foreach ($_it['_items'] as $k => $_item) {
                                $iid        = (int) $_item['_item_id'];
                                $_ids[$iid] = (isset($_item["{$field}_item_bundle"])) ? "{$_item["{$field}_item_bundle"]},{$bid}" : $bid;
                                // Delete the price field from the item - not needed
                                if (isset($_item["{$title}_bundle_price"])) {
                                    jrCore_db_delete_item_key($_data['module'], $iid, "{$title}_bundle_price");
                                }
                            }
                            if (count($_ids) > 0) {

                                $_up = array();
                                foreach ($_ids as $id => $bundles) {
                                    $_up[$id] = array("{$field}_item_bundle" => $bundles);
                                }
                                jrCore_db_update_multiple_items($_data['module'], $_up);

                                // Update our bundle with the bundle_list
                                $_rt = array(
                                    'bundle_list'  => json_encode(array($_data['module'] => $_ids)),
                                    'bundle_count' => count($_ids)
                                );
                                jrCore_db_update_item('jrFoxyCartBundle', $bid, $_rt);

                            }
                        }
                    }
                }
                else {
                    // Updating an existing bundle with new bundle price
                    $_rt = array('bundle_item_price' => $price);
                    jrCore_db_update_item('jrFoxyCartBundle', $_ex['_items'][0]['_item_id'], $_rt);
                }
            }
            else {
                // See if we are UPDATING a bundle
                $_sc = array(
                    'search'         => array(
                        "_profile_id = {$_user['user_active_profile_id']}",
                        "bundle_module = {$_data['module']}",
                        "bundle_title = {$_data[$title]}"
                    ),
                    'skip_triggers'  => true,
                    'ignore_pending' => true,
                    'privacy_check'  => false,
                    'limit'          => 1
                );
                $_ex = jrCore_db_search_items('jrFoxyCartBundle', $_sc);
                if ($_ex && is_array($_ex) && isset($_ex['_items'])) {
                    jrCore_db_delete_item_key('jrFoxyCartBundle', $_ex['_items'][0]['_item_id'], 'bundle_item_price');
                }
            }
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
function jrFoxyCartBundle_form_display_listener($_data, $_user, $_conf, $_args, $event)
{
    // See if this user's quota allows sales
    if (!jrCore_module_is_active('jrFoxyCart')) {
        return $_data;
    }
    if (!isset($_user['quota_jrFoxyCart_active']) || $_user['quota_jrFoxyCart_active'] != 'on') {
        // Not active for this quota
        return $_data;
    }
    // First - see if this view is a Bundle Price View
    $_xtra = array('module' => 'jrFoxyCartBundle');
    $_form = jrCore_trigger_event('jrFoxyCartBundle', 'add_bundle_price_field', array(), $_xtra);
    if (isset($_form) && is_array($_form) && isset($_form["{$_data['form_view']}"])) {
        $mod = $_data['form_params']['module'];
        $pfx = jrCore_db_get_prefix($mod);
        if (isset($pfx) && strlen($pfx) > 0) {
            $_lang = jrUser_load_lang_strings();
            // We've been asked to include a price field in this form
            $name = $_form["{$_data['form_view']}"]['title'];

            // See if this is a CREATE or UPDATE - if an update, we need
            // to get the existing bundle price from the datastore
            $_frm = jrCore_form_get_session();
            $val  = false;
            if (isset($_frm['form_params']['values']) && is_array($_frm['form_params']['values'])) {
                // It's an update...
                $ttl = $_form["{$_data['form_view']}"]['field'];
                if (isset($_frm['form_params']['values']["{$ttl}_item_bundle"])) {
                    $_bi = jrCore_db_get_item('jrFoxyCartBundle', $_frm['form_params']['values']["{$ttl}_item_bundle"]);
                    if ($_bi && is_array($_bi)) {
                        $val = $_bi['bundle_item_price'];
                    }
                }
            }
            $_tmp = array(
                'name'     => "{$name}_bundle_price",
                'type'     => 'text',
                'default'  => '',
                'validate' => 'price',
                'label'    => $_lang['jrFoxyCartBundle'][22],
                'sublabel' => $_lang['jrFoxyCartBundle'][24],
                'help'     => $_lang['jrFoxyCartBundle'][23],
                'value'    => $val
            );
            jrCore_form_field_create($_tmp);
        }
        return $_data;
    }

    // Check for visible flag
    if (jrCore_module_is_active('jrFoxyCart') && isset($_user['quota_jrFoxyCartBundle_show_bundle_only']) && $_user['quota_jrFoxyCartBundle_show_bundle_only'] == 'on') {
        list($mod, $view) = explode('/', $_data['form_view']);
        $_pn = jrCore_get_registered_module_features('jrFoxyCartBundle', 'bundle_only_support');
        if ($_pn && isset($_pn[$mod][$view])) {
            // Item Visible
            $_lng = jrUser_load_lang_strings();
            $prfx = jrCore_db_get_prefix($mod);
            $_tmp = array(
                'name'          => "{$prfx}_bundle_only",
                'label'         => $_lng['jrFoxyCartBundle'][37],
                'help'          => $_lng['jrFoxyCartBundle'][38],
                'type'          => 'checkbox',
                'default'       => 'off',
                'required'      => false,
                'form_designer' => false
            );
            jrCore_form_field_create($_tmp);
        }
    }
    return $_data;
}

/**
 * Adds items to users My Items when a bundle is sold
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrFoxyCartBundle_adding_item_to_purchase_history_listener($_data, $_user, $_conf, $_args, $event)
{
    // $_data will be the bundle that was sold
    if (isset($_data) && is_array($_data) && $_args['module'] == 'jrFoxyCartBundle') {

        // We need to go get the items associated with this bundle
        $_xtra = array('module' => 'jrFoxyCart');
        $_form = jrCore_trigger_event('jrFoxyCart', 'add_price_field', array(), $_xtra);
        if (isset($_form) && is_array($_form)) {
            $_temp = array();
            $bid   = (int) $_data['_item_id'];
            foreach ($_form as $view => $field) {
                list($module,) = explode('/', $view);
                if (!isset($_temp[$module])) {
                    $_rt = array(
                        'search'              => array(
                            "{$field}_item_bundle = {$bid} || {$field}_item_bundle like {$bid},% || {$field}_item_bundle like %,{$bid} || {$field}_item_bundle like %,{$bid},%"
                        ),
                        'return_item_id_only' => true,
                        'skip_triggers'       => true,
                        'limit'               => 10000
                    );
                    $_rt = jrCore_db_search_items($module, $_rt);
                    if ($_rt && is_array($_rt)) {
                        $_it = jrCore_db_get_multiple_items($module, $_rt);
                        if ($_it && is_array($_it)) {
                            foreach ($_it as $_item) {
                                // See which field is our bundle field
                                $fld = false;
                                foreach ($_item as $k => $v) {
                                    if (strpos($k, '_item_bundle')) {
                                        $fld = str_replace('_item_bundle', '', $k);
                                        break;
                                    }
                                }
                                if ($fld) {

                                    $_item['product_bundle_id'] = $_args['item_id'];
                                    $mid                        = jrFoxyCart_add_to_my_items($_args['user_id'], $module, $fld, $_item, $_args['_txn']);
                                    if (!isset($mid) || !jrCore_checktype($mid, 'number_nz')) {
                                        jrCore_logger('CRI', "unable to add item_id {$module}/{$_item['_item_id']} to user_id {$_args['user_id']} my items");
                                    }

                                    // increment the sales count for the sellers profile
                                    jrCore_counter('jrProfile', $_item['_profile_id'], 'profile_jrFoxyCart_sales');

                                    // Increment sold count for item
                                    $pfx = jrCore_db_get_prefix($module);
                                    jrCore_db_increment_key($module, $_item['_item_id'], $pfx . '_sale_count', 1);

                                }
                                else {
                                    jrCore_logger('CRI', "unable to find purchase bundle field for item_id {$module}/{$_item['_item_id']}");
                                }
                            }
                        }
                    }
                    $_temp[$module] = 1;
                }
            }
        }
    }
    return $_data;
}

//---------------------------------------------------------
// SMARTY FUNCTIONS
//---------------------------------------------------------

/**
 * shows remove from bundle button on bundle items for bundle owners
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrFoxyCartBundle_remove_button($params, $smarty)
{
    global $_mods, $_conf;
    if (!isset($params['module']) || !isset($_mods["{$params['module']}"])) {
        return 'jrFoxyCartBundle_remove_button: module required';
    }
    if (!isset($params['bundle_id']) || !jrCore_checktype($params['bundle_id'], 'number_nz')) {
        return 'jrFoxyCartBundle_remove_button: bundle_id required';
    }
    if (!isset($params['item']) || !is_array($params['item'])) {
        return 'jrFoxyCartBundle_remove_button: item array required';
    }
    // Make sure viewer has owner access to item
    if (!jrUser_can_edit_item($params['item'])) {
        return '';
    }
    $_ln = jrUser_load_lang_strings();
    $alt = addslashes($_ln['jrFoxyCartBundle'][31]);
    $bid = (int) $params['bundle_id'];
    if (isset($params['confirm']) && strlen($params['confirm']) > 0) {
        $onc = "if(confirm('" . addslashes($_ln['jrFoxyCartBundle'][32]) . "')){jrFoxyCartBundle_remove({$bid},'{$params['module']}',{$params['item']['_item_id']});}";
    }
    else {
        $onc = "jrFoxyCartBundle_remove({$bid},'{$params['module']}',{$params['item']['_item_id']});";
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
 * List Items in a FoxyCart bundle
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrFoxyCartBundle_list_items($params, $smarty)
{
    if (!isset($params['item']) || !is_array($params['item'])) {
        return 'jrFoxyCartBundle_list_items: invalid item array';
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
        $_xtra = array('module' => 'jrFoxyCart');
        $_form = jrCore_trigger_event('jrFoxyCart', 'add_price_field', array(), $_xtra);
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
 * Get a FoxyCartBundle album
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrFoxyCartBundle_get_album($params, $smarty)
{
    if (!jrCore_module_is_active('jrFoxyCartBundle')) {
        return '';
    }
    if (!isset($params['module']{0})) {
        return 'jrFoxyCartBundle_get_album: module parameter required';
    }
    if (!isset($params['profile_id']) || strlen($params['profile_id']) === 0) {
        return 'jrFoxyCartBundle_get_album: profile_id parameter required';
    }
    if (!isset($params['name']) || strlen($params['name']) === 0) {
        return 'jrFoxyCartBundle_get_album: name parameter required';
    }
    if (!isset($params['assign']) || strlen($params['assign']) === 0) {
        return 'jrFoxyCartBundle_get_album: assign parameter required';
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
    $_rt = jrCore_db_search_items('jrFoxyCartBundle', $_sp);
    if (isset($_rt) && is_array($_rt['_items'])) {
        $smarty->assign($params['assign'], $_rt['_items'][0]);
    }
    else {
        $smarty->assign($params['assign'], false);
    }
    return '';
}

/**
 * shows an add to bundle button on audio files for logged in users.
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrFoxyCartBundle_button($params, $smarty)
{
    global $_conf;
    if (!jrCore_module_is_active('jrFoxyCartBundle') || $params['item']['quota_jrFoxyCart_active'] == 'off' || !jrProfile_is_profile_view()) {
        return '';
    }
    if (!isset($params['module']) || !jrCore_module_is_active($params['module'])) {
        return 'jrFoxyCartBundle_button: module parameter required';
    }
    if (!isset($params['item']) || !is_array($params['item'])) {
        return 'jrFoxyCartBundle_button: item array required';
    }
    if (!isset($params['field']) || strlen($params['field']) === 0) {
        return 'jrFoxyCartBundle_button: field param required';
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
        'alt'     => (isset($params['alt'])) ? $params['alt'] : $_lang['jrFoxyCartBundle'][25],
        'title'   => $params['alt'],
        'class'   => (isset($params['class'])) ? $params['class'] : 'create_img'
    );
    if (isset($params['image'])) {
        // Check for custom button image
        $src               = "{$_conf['jrCore_base_url']}/skins/{$_conf['jrCore_active_skin']}/img/{$params['image']}";
        $_rep['icon_html'] = '<img src="' . $src . '" class="' . $_rep['class'] . '" alt="' . $_rep['alt'] . '" title="' . $_rep['alt'] . '" onclick="jrFoxyCartBundle_select(\'' . intval($params['item']['_item_id']) . '\',\'' . $params['field'] . '\',\'' . $params['module'] . '\',null)">';
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
        $_rep['icon_html'] = "<a onclick=\"jrFoxyCartBundle_select('" . intval($params['item']['_item_id']) . "','" . $params['field'] . "','" . $params['module'] . "',null)\" title=\"{$_rep['alt']}\">" . jrCore_get_sprite_html($params['icon']) . '</a>';
    }
    $out = jrCore_parse_template('bundle_button.tpl', $_rep, 'jrFoxyCartBundle');
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * display the sale info to the seller of the item for FoxyCart
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrFoxyCartBundle_my_earnings_row_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['sale_bundle_id']) && is_numeric($_args['sale_bundle_id']) && $_args['sale_bundle_id'] > 0) {
        $murl              = jrCore_get_module_url('jrFoxyCartBundle');
        $item              = jrCore_db_get_item('jrFoxyCartBundle', $_args['sale_bundle_id']);
        $_data[1]['title'] = ($item['bundle_title']) ? '<a href="' . $_conf['jrCore_base_url'] . '/' . $item['profile_url'] . '/' . $murl . '/' . $_args['sale_bundle_id'] . '/' . $item['bundle_title_url'] . '">[bundle] ' . $item['bundle_title'] . '</a>' : 'jrFoxyCartBundle-' . $_args['sale_bundle_id'];
    }
    return $_data;
}

/**
 * returns the info on the bundles this item is included in
 * @param $module
 * @param $item_id
 * @return array|bool
 */
function jrFoxyCartBundle_get_bundles($module, $item_id)
{
    $_item = jrCore_db_get_item($module, $item_id, true);
    foreach ($_item as $k => $v) {
        if (strpos($k, '_item_bundle')) {
            $_bids = explode(',', $v);
            if (is_array($_bids)) {
                foreach ($_bids as $bid) {
                    $_bundles[] = jrCore_db_get_item('jrFoxyCartBundle', $bid, true);
                }
            }
            break;
        }
    }
    return (isset($_bundles) && is_array($_bundles)) ? $_bundles : false;
}
