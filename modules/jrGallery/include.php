<?php
/**
 * Jamroom Image Galleries module
 *
 * copyright 2017 The Jamroom Network
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
function jrGallery_meta()
{
    $_tmp = array(
        'name'        => 'Image Galleries',
        'url'         => 'gallery',
        'version'     => '1.9.2',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Add Image Gallery support to Profiles',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/280/image-galleries',
        'category'    => 'profiles',
        'requires'    => 'jrCore:6.1.0b2',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrGallery_init()
{
    // Embedded media module
    jrCore_register_event_listener('jrEmbed', 'embed_params', 'jrGallery_embed_params_listener');
    jrCore_register_event_listener('jrEmbed', 'embed_variables', 'jrGallery_embed_variables_listener');

    // We listen for the jrUrlScan 'url_found' trigger and if its a gallery url, add appropriate data to its array
    jrCore_register_event_listener('jrUrlScan', 'url_found', 'jrGallery_url_found_listener');

    // We have some small custom CSS for our page
    jrCore_register_module_feature('jrCore', 'css', 'jrGallery', 'jrGallery.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrGallery', 'jrGallery.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrGallery', 'jquery.pagesearch.js');

    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrGallery', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrGallery', 'update');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrGallery', 'detail');

    // Let the core Action System know we are adding gallery Support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrGallery', 'on');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrGallery', 'on');
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrGallery', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrGallery', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrGallery', 'update', 'item_action.tpl');

    // Payments
    jrCore_register_event_listener('jrPayment', 'payment_entry', 'jrGallery_payment_entry_listener');
    jrCore_register_event_listener('jrPayment', 'purchase_entry', 'jrGallery_purchase_entry_listener');
    jrCore_register_event_listener('jrPayment', 'cart_entry', 'jrGallery_cart_entry_listener');
    jrCore_register_event_listener('jrPayment', 'txn_detail_entry', 'jrGallery_txn_detail_entry_listener');
    jrCore_register_event_listener('jrPayment', 'vault_download', 'jrGallery_vault_download_listener');
    jrCore_register_event_listener('jrBundle', 'bundle_filename', 'jrGallery_bundle_filename_listener');

    // Sales support
    jrCore_register_event_listener('jrFoxyCart', 'add_price_field', 'jrGallery_add_price_field_listener');
    jrCore_register_event_listener('jrFoxyCart', 'my_items_row', 'jrGallery_my_items_row_listener');

    // Bundle Support (selling an entire album (gallery))
    jrCore_register_module_feature('jrFoxyCartBundle', 'visible_support', 'jrGallery', true);
    jrCore_register_event_listener('jrFoxyCartBundle', 'get_album_field', 'jrGallery_get_bundle_field_listener');
    jrCore_register_event_listener('jrFoxyCartBundle', 'add_bundle_price_field', 'jrGallery_add_bundle_price_field_listener');

    // We have fields that can be searched
    jrCore_register_module_feature('jrSearch', 'search_fields', 'jrGallery', 'gallery_image_title,gallery_image_name,gallery_caption', 24);

    // Fix up image titles in bundles
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrGallery_db_search_items_listener');
    jrCore_register_event_listener('jrCore', 'repair_module', 'jrGallery_repair_module_listener');

    // Make sure originals are not being downloaded
    jrCore_register_event_listener('jrCore', 'download_file', 'jrGallery_download_file_listener');

    // Watch for image views
    jrCore_register_event_listener('jrCore', 'module_view', 'jrGallery_module_view_listener');

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'jrGallery', 'profile_jrGallery_item_count', 38);

    // Custom Share this for gallery pages
    jrCore_register_event_listener('jrShareThis', 'get_item_info', 'jrGallery_get_item_info_listener');

    // Our widget
    jrCore_register_module_feature('jrSiteBuilder', 'widget', 'jrGallery', 'widget_gallery', 'Gallery Images');

    // Quick Share Tabs
    $_tm = array(
        'title' => 1,
        'icon'  => 'camera'
    );
    jrCore_register_module_feature('jrAction', 'quick_share', 'jrGallery', 'jrGallery_quick_share_gallery', $_tm);

    jrCore_register_module_feature('jrTips', 'tip', 'jrGallery', 'tip');

    // item buttons
    $_tmp = array(
        'title'  => 'image download button',
        'icon'   => 'download',
        'active' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrGallery', 'jrGallery_image_download_button', $_tmp);

    // When an action is shared via jrOneAll, we can provide the text of the shared item
    jrCore_register_event_listener('jrOneAll', 'network_share_text', 'jrGallery_network_share_text_listener');

    return true;
}

//------------------------------------
// QUICK SHARE
//------------------------------------

/**
 * Show Quick Share form
 * @param $_post array Posted info
 * @param $_user array Active User info
 * @param $_conf array Global Config
 * @return string
 */
function jrGallery_quick_share_gallery($_post, $_user, $_conf)
{
    return jrCore_parse_template('item_action_quick_share.tpl', $_user, 'jrGallery');
}

/**
 * Quick Share save
 * @param $_post array Posted info
 * @param $_user array Active User info
 * @param $_conf array Global Config
 * @return string
 */
function jrGallery_quick_share_gallery_save($_post, $_user, $_conf)
{
    // Prevent core from handling this upload
    jrCore_disable_automatic_upload_handling();

    // Make sure we get a gallery title
    if (!isset($_post['gallery_title']) || strlen($_post['gallery_title']) === 0) {
        return "FIELD: gallery_title";
    }

    // Must get at least one image
    $_files = jrCore_get_uploaded_media_files('jrGallery', 'gallery_image');
    if (!$_files || !is_array($_files)) {
        $_ln = jrUser_load_lang_strings();
        return "ERROR: {$_ln['jrGallery'][6]}";
    }

    // Gallery data
    $_rt = array(
        'gallery_title'     => $_post['gallery_title'],
        'gallery_title_url' => jrCore_url_string($_post['gallery_title'])
    );

    foreach ($_files as $k => $file_name) {
        $_rt['gallery_order'] = ($k + 1);
        $aid                  = jrCore_db_create_item('jrGallery', $_rt);
        if (!$aid) {
            if ($error_message = jrCore_get_flag("max_jrGallery_items_reached")) {
                return "ERROR: {$error_message}";
            }
            else {
                // See if we can save any
                continue;
            }
        }
        $_tm = array(
            '_item_id'    => $aid,
            '_profile_id' => $_user['user_active_profile_id']
        );
        $_tm = array_merge($_rt, $_tm);
        jrCore_save_media_file('jrGallery', $file_name, $_user['user_active_profile_id'], $aid, 'gallery_image', $_tm);

        // Add our FIRST IMAGE to our actions...
        if (!isset($action_saved)) {
            // Add to Actions...
            jrCore_run_module_function('jrAction_save', 'create', 'jrGallery', $aid);
            $action_saved = true;
        }
    }
    jrCore_delete_upload_temp_directory($_post['upload_token']);
    return true;
}

//------------------------------------
// ITEM BUTTONS
//------------------------------------

/**
 * "image download" button
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return mixed
 */
function jrGallery_image_download_button($module, $_item, $_args, $smarty, $test_only = false)
{
    global $_conf;
    if ($module == 'jrGallery') {

        if ($test_only) {
            return true;
        }

        if (isset($_item['gallery_image_size']) && jrCore_checktype($_item['gallery_image_size'], 'number_nz')) {

            // We have a valid file - check for allowed downloads
            if (isset($_conf['jrGallery_download']) && $_conf['jrGallery_download'] == 'on') {

                $allow = false;
                if (jrUser_can_edit_item($_item)) {
                    // Admins and profile owners can always download
                    $allow = true;
                }
                // NOTE: If an gallery item has NO PRICE, but is part of a BUNDLE, and is
                // not marked "Bundle Only" AND we allow downloads, show download button
                elseif ((!isset($_item['gallery_image_item_price']) || strlen($_item['gallery_image_item_price']) === 0 || $_item['gallery_image_item_price'] == 0) && (!isset($_item['gallery_bundle_only']) || $_item['gallery_bundle_only'] != 'on')) {
                    $allow = true;
                }
                elseif (isset($_item['gallery_bundle_only']) && $_item['gallery_bundle_only'] == 'on') {
                    $allow = false;
                }
                // NOTE: gallery_image_item_price is already checked in core download magic view
                // We just need to check to see if this gallery image is part of a paid bundle
                elseif (isset($_item['gallery_image_item_bundle']) && strlen($_item['gallery_image_item_bundle']) > 0) {
                    $_id = array();
                    foreach (explode(',', $_item['gallery_image_item_bundle']) as $bid) {
                        $_id[] = (int) $bid;
                    }
                    $_bi = jrCore_db_get_multiple_items('jrFoxyCartBundle', $_id, array('bundle_item_price'));
                    if ($_bi && is_array($_bi)) {
                        $block = false;
                        foreach ($_bi as $_bun) {
                            if (isset($_bun['bundle_item_price']) && $_bun['bundle_item_price'] > 0) {
                                $block = true;
                                break;
                            }
                        }
                        if (!$block) {
                            $allow = true;
                        }
                    }
                }
                else {
                    $allow = true;
                }
                if ($allow) {
                    $ttl = (isset($_item['gallery_image_title'])) ? $_item['gallery_image_title'] : $_item['gallery_image_name'];
                    $ttl = jrGallery_url_name($ttl);
                    $url = jrCore_get_module_url('jrGallery');
                    return array(
                        'url'  => "{$_conf['jrCore_base_url']}/{$url}/download/gallery_image/{$_item['_item_id']}/{$ttl}",
                        'icon' => 'download',
                        'alt'  => 51
                    );
                }
            }
        }
    }
    return false;
}

//------------------------------------
// EVENT LISTENERS
//------------------------------------

/**
 * Get info about a gallery for ShareThis
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_repair_module_listener($_data, $_user, $_conf, $_args, $event)
{
    $_rt = jrCore_db_get_items_missing_key('jrGallery', 'gallery_order');
    if ($_rt && is_array($_rt)) {
        $_up = array();
        foreach ($_rt as $id) {
            $_up[$id] = array('gallery_order' => 100);
        }
        if (count($_up) > 0) {
            jrCore_db_update_multiple_items('jrGallery', $_up);
            jrCore_logger('INF', "updated " . count($_up) . " gallery images missing gallery_order key");
        }
    }
    return $_data;
}

/**
 * Get info about a gallery for ShareThis
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_module_view_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['module']) && $_data['module'] == 'jrGallery' && isset($_data['option']) && $_data['option'] == 'image' && isset($_data['_1']) && $_data['_1'] == 'gallery_image') {
        // We have a request for a gallery image - check ID and size
        if (isset($_data['_2']) && jrCore_checktype($_data['_2'], 'number_nz') && isset($_data['_3'])) {
            switch ($_data['_3']) {
                case '512':
                case '800':
                case '1280':
                case 'xxlarge':
                case 'xxxlarge':
                    // Do we exist?
                    if ($_rt = jrCore_db_get_item('jrGallery', intval($_data['_2']))) {
                        jrCore_counter('jrGallery', $_data['_2'], 'gallery_image_view_count');
                    }
                    break;
            }
        }
    }
    return $_data;
}

/**
 * Get info about a gallery for ShareThis
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_get_item_info_listener($_data, $_user, $_conf, $_args, $event)
{
    $_rt = array(
        'search'                       => array(
            "_profile_id = {$_args['_profile_id']}"
        ),
        'order_by'                     => array(
            'gallery_order' => 'numerical_asc'
        ),
        'exclude_jrUser_keys'          => true,
        'exclude_jrProfile_quota_keys' => true,
        'ignore_pending'               => true,
        'limit'                        => 1
    );
    $ttl = false;
    if (isset($_args['_1']) && strlen($_args['_1']) > 0) {
        $ttl             = true;
        $_rt['search'][] = "gallery_title_url = {$_args['_1']}";
    }
    $_rt = jrCore_db_search_items('jrGallery', $_rt);
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
        $_rt                     = $_rt['_items'][0];
        $_rt['create_short_url'] = false;
        if (!$ttl) {
            $_ln                  = jrUser_load_lang_strings();
            $_rt['gallery_title'] = $_ln['jrGallery']['menu'];
        }
        if (!isset($_rt['gallery_description'])) {
            $_rt['gallery_description'] = $_rt['gallery_title'];
        }
        return $_rt;
    }
    return $_data;
}

/**
 * Watch for original image downloads
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_download_file_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['module']) && $_args['module'] == 'jrGallery') {
        if (!isset($_conf['jrGallery_download']) || $_conf['jrGallery_download'] != 'on') {
            header('HTTP/1.0 403 Forbidden');
            header('Connection: close');
            jrCore_notice('Error', 'you do not have permission to download this file');
            exit;
        }
    }
    return $_data;
}

/**
 * Add in player code to the jrUrlScan array
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_url_found_listener($_data, $_user, $_conf, $_args, $event)
{
    // Is it a local gallery image url
    if (isset($_args['url']) && strpos($_args['url'], $_conf['jrCore_base_url']) === 0) {
        $_x = explode('/', substr($_args['url'], strlen($_conf['jrCore_base_url']) + 1));
        if ($_x && is_array($_x) && isset($_x[1]) && $_x[1] == jrCore_get_module_url('jrGallery')) {
            $idx = (int) $_args['i'];
            if (isset($_x[2]) && jrCore_checktype($_x[2], 'number_nz')) {
                $_item                             = jrCore_db_get_item('jrGallery', $_x[2], true);
                $_data['_items'][$idx]['title']    = jrGallery_get_gallery_image_title($_item);
                $_data['_items'][$idx]['load_url'] = "{$_conf['jrCore_base_url']}/{$_x[1]}/parse/urlscan_player/{$_x[2]}/__ajax=1";
                $_data['_items'][$idx]['url']      = $_args['url'];
            }
        }
    }
    return $_data;
}

/**
 * Replace titles with image names
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrGallery' && isset($_data['_items'])) {
        foreach ($_data['_items'] as $k => $v) {
            $title                                       = jrGallery_get_gallery_image_title($v);
            $_data['_items'][$k]['gallery_bundle_title'] = $title;
            $_data['_items'][$k]['gallery_alt_text']     = $title;
        }
    }
    return $_data;
}

/**
 * Format gallery entry in purchases
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGallery_payment_entry_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_mods;
    if (isset($_args['r_item_data']) && is_array($_args['r_item_data'])) {
        $image_url         = jrGallery_get_gallery_image_url($_args['r_item_data']);
        $image_ttl         = jrGallery_get_gallery_image_title($_args['r_item_data']);
        $_data[2]['title'] = '<a href="' . $_conf['jrCore_base_url'] . '/' . $_args['r_item_data']['profile_url'] . '/' . jrCore_get_module_url($_args['r_module']) . '">' . $_mods['jrGallery']['module_name'] . '</a> - <a href="' . $image_url . '">' . $image_ttl . '</a><br><a href="' . $_conf['jrCore_base_url'] . '/' . $_args['r_item_data']['profile_url'] . '"><small>@' . $_args['r_item_data']['profile_url'] . '</small></a>';
    }
    return $_data;
}

/**
 * Format gallery entry in USER purchases
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGallery_purchase_entry_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['r_item_data']) && is_array($_args['r_item_data'])) {
        $image_url         = jrGallery_get_gallery_image_url($_args['r_item_data']);
        $image_ttl         = jrGallery_get_gallery_image_title($_args['r_item_data']);
        $_data[2]['title'] = '<a href="' . $image_url . '">' . $image_ttl . '</a><br><a href="' . $_conf['jrCore_base_url'] . '/' . $_args['r_item_data']['profile_url'] . '"><small>@' . $_args['r_item_data']['profile_url'] . '</small></a>';
    }
    return $_data;
}

/**
 * Format gallery image in cart
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGallery_cart_entry_listener($_data, $_user, $_conf, $_args, $event)
{
    $_data[2]['title'] = jrGallery_get_gallery_image_title($_args) . '<br><small><a href="' . $_conf['jrCore_base_url'] . '/' . $_args['profile_url'] . '">@' . $_args['profile_url'] . '</a> &bull; gallery image</small>';
    return $_data;
}

/**
 * Format gallery image in transaction detail
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGallery_txn_detail_entry_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_mods;
    if (isset($_args['r_item_data']) && is_array($_args['r_item_data'])) {
        $image_url         = jrGallery_get_gallery_image_url($_args['r_item_data']);
        $image_ttl         = jrGallery_get_gallery_image_title($_args['r_item_data']);
        $_data[2]['title'] = '<a href="' . $_conf['jrCore_base_url'] . '/' . $_args['r_item_data']['profile_url'] . '/' . jrCore_get_module_url($_args['r_module']) . '">' . $_mods['jrGallery']['module_name'] . '</a> - <a href="' . $image_url . '">' . $image_ttl . '</a><br><a href="' . $_conf['jrCore_base_url'] . '/' . $_args['r_item_data']['profile_url'] . '"><small>@' . $_args['r_item_data']['profile_url'] . '</small></a>';
    }
    return $_data;
}

/**
 * Return correct filename for a gallery image
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrGallery_bundle_filename_listener($_data, $_user, $_conf, $_args, $event)
{
    $_ln               = jrUser_load_lang_strings($_conf['jrUser_default_language']);
    $_data['filename'] = '@' . $_args['profile_url'] . ' - ' . $_ln['jrGallery']['menu'] . ' - ' . jrCore_str_to_lower(jrGallery_get_gallery_image_title($_args)) . '.' . jrCore_str_to_lower($_args['gallery_image_extension']);
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
function jrGallery_vault_download_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrGallery') {
        $_data['vault_name'] = '@' . $_data['profile_url'] . ' - ' . jrCore_str_to_lower($_data['gallery_title'] . ' - ' . jrGallery_get_gallery_image_title($_data)) . '.' . jrCore_str_to_lower($_data['gallery_image_extension']);
    }
    return $_data;
}

/**
 * Return gallery file field that a price can be added to
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_add_price_field_listener($_data, $_user, $_conf, $_args, $event)
{
    // Module/View => File Field
    $_data['jrGallery/detail'] = 'gallery_image';
    return $_data;
}

/**
 * Return field for Bundle module
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_get_bundle_field_listener($_data, $_user, $_conf, $_args, $event)
{
    $_data['jrGallery'] = 'gallery_image';
    return $_data;
}

/**
 * Return gallery file bundle fields for forms
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_add_bundle_price_field_listener($_data, $_user, $_conf, $_args, $event)
{
    // Module/View => array(Bundle Title field, Bundle File field)
    $_data['jrGallery/create'] = array(
        'title' => 'gallery_title',
        'field' => 'gallery_image'
    );
    $_data['jrGallery/update'] = array(
        'title' => 'gallery_title',
        'field' => 'gallery_image'
    );
    return $_data;
}

/**
 * Add gallery image download row to My Items
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_my_items_row_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['purchase_module']) && $_args['purchase_module'] == 'jrGallery') {
        $url               = jrCore_get_module_url('jrGallery');
        $_data[2]['title'] = $_args['gallery_image_name'];
        $_data[5]['title'] = jrCore_page_button("a{$_args['_item_id']}", 'download', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/vault_download/gallery_image/{$_args['_item_id']}')");
    }
    return $_data;
}

/**
 * We change the pagebreak on an embed item list
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_embed_params_listener($_data, $_user, $_conf, $_args, $event)
{
    $_data['pagebreak'] = 14;
    return $_data;
}

/**
 * Add in image size selector when viewing a gallery list
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_embed_variables_listener($_data, $_user, $_conf, $_args, $event)
{
    $_data['image_sizes'] = array();
    $_tmp                 = jrImage_get_allowed_image_widths();
    foreach ($_tmp as $k => $v) {
        if (!is_numeric($k)) {
            $_data['image_sizes'][$v] = $k;
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
function jrGallery_network_share_text_listener($_data, $_user, $_conf, $_args, $event)
{
    // $_data:
    // [providers] => twitter
    // [user_token] => c64xxxxa-b66e-4c6c-xxxx-cdea7xxxxx03
    // [user_id] => 1
    // [action_module] => jrGallery
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
    $url = jrCore_get_module_url('jrGallery');
    $txt = $_ln['jrGallery'][23];
    if ($_data['action_mode'] == 'update') {
        $txt = $_ln['jrGallery'][39];
    }
    $_out = array(
        'text' => "{$_conf['jrCore_base_url']}/{$_data['profile_url']} {$_data['profile_name']} {$txt}: \"{$_data['gallery_title']}\" {$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['gallery_title_url']}/all",
        'link' => array(
            'url'  => "{$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['gallery_title_url']}/all",
            'name' => $_data['gallery_title']
        )
    );
    // See if they included a picture with the song
    if (isset($_data['gallery_image_size']) && jrCore_checktype($_data['gallery_image_size'], 'number_nz')) {
        $_out['picture'] = array(
            'url' => "{$_conf['jrCore_base_url']}/{$url}/image/gallery_image/{$_data['_item_id']}/large"
        );
    }
    return $_out;
}


//---------------------------------------------------------
// FUNCTIONS
//---------------------------------------------------------

/**
 * Create a clean URL from a Gallery Image name
 * @param $name string image name
 * @return string
 */
function jrGallery_url_name($name)
{
    // Note @ for "Detected an illegal character in input string"
    $str = @iconv('UTF-8', 'ASCII//TRANSLIT', substr(trim($name), 0, 128));
    $str = preg_replace("/[^a-zA-Z0-9\/\._| -]/", '', $str);
    $str = strtolower(trim($str, '-'));
    $str = trim(trim(preg_replace("/[\/_| -]+/", '-', $str)), '-');
    $str = preg_replace('/\\.[^.\\s]{3,4}$/', '', $str);
    if (strlen($str) === 0) {
        // We may have removed everything - rawurlencode
        $str = rawurlencode(jrCore_str_to_lower(str_replace(array('"', "'", ' ', '&', '@', '/', '[', ']', '(', ')'), '-', $name)));
    }
    return trim(preg_replace('/-+/', '-', $str), '-');
}

/**
 * Create a clean TITLE from a Gallery Image name
 * @param $name string image name
 * @return string
 */
function jrGallery_title_name($name)
{
    // Note @ for "Detected an illegal character in input string"
    $str = @iconv('UTF-8', 'ASCII//TRANSLIT', substr(trim($name), 0, 128));
    $str = preg_replace("/[^a-zA-Z0-9\/\._| -]/", '', $str);
    $str = strtolower(trim($str, '-'));
    $str = trim(trim(preg_replace("/[\/_| -]+/", ' ', $str)), ' ');
    $str = preg_replace('/\\.[^.\\s]{3,4}$/', '', $str);
    if (strlen($str) === 0) {
        // We may have removed everything - rawurlencode
        $str = rawurlencode(jrCore_str_to_lower(str_replace(array('"', "'", ' ', '&', '@', '/', '[', ']', '(', ')'), ' ', $name)));
    }
    return trim(preg_replace('/-+/', ' ', $str), ' ');
}

/**
 * Get unique array of gallery titles for specific profile_id
 * @param $profile_id
 * @return mixed
 */
function jrGallery_get_gallery_titles($profile_id = 0)
{
    global $_user;
    if (!jrCore_checktype($profile_id, 'number_nz')) {
        $profile_id = $_user['user_active_profile_id'];
    }
    // Let's get other galleries this profile has created so we can allow the
    // image to be moved to a new gallery if they want
    $_gt = array(
        'search'      => array(
            "_profile_id = {$profile_id}"
        ),
        'return_keys' => array(
            'gallery_title'
        ),
        'group_by'    => 'gallery_title',
        'limit'       => 5000
    );
    $_gt = jrCore_db_search_items('jrGallery', $_gt);
    if ($_gt && is_array($_gt) && isset($_gt['_items'])) {
        $_og = array();
        foreach ($_gt['_items'] as $_itm) {
            $_og["{$_itm['gallery_title']}"] = $_itm['gallery_title'];
        }
        return $_og;
    }
    return false;
}

/**
 * Get a unique gallery image URL
 * @param $item array Gallery image data array
 * @return string
 */
function jrGallery_get_gallery_image_url($item)
{
    global $_conf;
    $mrl = jrCore_get_module_url('jrGallery');
    $url = "{$_conf['jrCore_base_url']}/{$item['profile_url']}/{$mrl}/{$item['_item_id']}/";
    if (isset($item['gallery_image_title_url']) && strlen($item['gallery_image_title_url']) > 0) {
        $add = $item['gallery_image_title_url'];
    }
    elseif (isset($item['gallery_caption']) && strlen($item['gallery_caption']) > 0) {
        $add = jrCore_url_string(substr(jrCore_strip_html($item['gallery_caption']), 0, 128));
    }
    else {
        $add = jrGallery_url_name($item['gallery_image_name']);
    }
    return $url . jrGallery_strip_image_extensions($add);
}

/**
 * Get a unique gallery image Title
 * @param $item array Gallery image data array
 * @return string
 */
function jrGallery_get_gallery_image_title($item)
{
    if (isset($item['gallery_image_title']) && strlen($item['gallery_image_title']) > 0) {
        $title = $item['gallery_image_title'];
    }
    elseif (isset($item['gallery_image_name']) && strlen($item['gallery_image_name']) > 0) {
        $title = $item['gallery_image_name'];
    }
    elseif (isset($item['gallery_caption']) && strlen($item['gallery_caption']) > 0) {
        $title = jrCore_strip_html($item['gallery_caption']);
    }
    else {
        $title = $item['gallery_title'];
    }
    return jrGallery_strip_image_extensions($title);
}

/**
 * Strip image extensions from a string
 * @param string $string
 * @return mixed
 */
function jrGallery_strip_image_extensions($string)
{
    return str_ireplace(array('.jpg', '.jpeg', '.jpe', '.jfi', '.jfif', '.gif', '.png', 'jpg', 'jpeg', 'jpe', 'jfi', 'jfif', 'gif', 'png'), '', $string);
}

/**
 * Pending Gallery Images browser
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 * @return bool
 */
function jrGallery_dashboard_pending($_post, $_user, $_conf)
{
    // Get our pending items
    $tbl = jrCore_db_table_name('jrCore', 'pending');
    $req = "SELECT * FROM {$tbl} WHERE LENGTH(pending_linked_item_module) = 0 AND pending_module = '{$_post['m']}'";
    $_ex = false;
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_post['search_string'] = trim(urldecode($_post['search_string']));
        $str                    = jrCore_db_escape($_post['search_string']);
        $req                    .= " AND pending_data LIKE '%{$str}%'";
        $_ex                    = array('search_string' => $_post['search_string']);
    }
    $req .= ' ORDER BY pending_id ASC';

    // find how many lines we are showing
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $_rt = jrCore_db_paged_query($req, $_post['p'], 12, 'NUMERIC');

    // start our html output
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard/pending/m={$_post['m']}");

    $dat             = array();
    $dat[1]['title'] = '<input type="checkbox" class="form_checkbox" onclick="$(\'.pending_checkbox\').prop(\'checked\',$(this).prop(\'checked\'));">';
    $dat[1]['width'] = '1%;';
    $dat[2]['title'] = 'date';
    $dat[2]['width'] = '5%;';

    $dat[3]['title'] = 'image';
    $dat[3]['width'] = '5%;';

    $dat[4]['title'] = 'item';
    $dat[4]['width'] = '50%;';
    $dat[5]['title'] = 'profile';
    $dat[5]['width'] = '10%;';
    $dat[6]['title'] = 'approve';
    $dat[6]['width'] = '3%;';
    $dat[7]['title'] = 'reject';
    $dat[7]['width'] = '3%;';
    $dat[8]['title'] = 'delete';
    $dat[8]['width'] = '3%;';
    jrCore_page_table_header($dat);
    unset($dat);

    $url = jrCore_get_module_url('jrCore');
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

        foreach ($_rt['_items'] as $k => $_pend) {
            $_data           = json_decode($_pend['pending_data'], true);
            $murl            = jrCore_get_module_url($_pend['pending_module']);
            $mpfx            = jrCore_db_get_prefix($_pend['pending_module']);
            $dat             = array();
            $dat[1]['title'] = '<input type="checkbox" class="form_checkbox pending_checkbox" name="' . $_pend['pending_id'] . '">';
            $dat[2]['title'] = jrCore_format_time($_pend['pending_created']);
            $dat[2]['class'] = 'center';

            $_im             = array(
                'crop'   => 'auto',
                'width'  => 56,
                'height' => 56,
                'alt'    => 'img',
                'title'  => 'img',
                '_v'     => (isset($_data['user_image_time']) && $_data['user_image_time'] > 0) ? $_data['user_image_time'] : false
            );
            $dat[3]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$murl}/image/gallery_image/{$_pend['pending_item_id']}/1280\" data-lightbox=\"images\">" . jrImage_get_image_src('jrGallery', 'gallery_image', $_pend['pending_item_id'], 'icon', $_im) . '</a>';
            if (isset($_data['item']["{$mpfx}_title"]) && strlen($_data['item']["{$mpfx}_title"]) > 0) {
                $title = $_data['item']["{$mpfx}_title"];
            }
            else {
                $title = "{$_data['user']['profile_url']}/{$murl}/{$_pend['pending_item_id']}";
            }
            $dat[4]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$murl}/detail/id={$_pend['pending_item_id']}\" target=\"_blank\">{$title}</a>";
            if (isset($_data['item']["{$mpfx}_pending_reason"])) {
                $dat[4]['title'] .= '<br><small>' . $_data['item']["{$mpfx}_pending_reason"] . '</small>';
            }
            $dat[5]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_data['user']['profile_url']}\">@{$_data['user']['profile_name']}</a>";
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = jrCore_page_button("pending-approve-{$k}", 'approve', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/pending_item_approve/jrGallery/id={$_pend['pending_item_id']}')");
            $dat[7]['title'] = jrCore_page_button("pending-reject-{$k}", 'reject', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/pending_item_reject/jrGallery/id={$_pend['pending_item_id']}')");
            $dat[8]['title'] = jrCore_page_button("pending-delete-{$k}", 'delete', "if(confirm('Are you sure you want to delete this item? No notice will be sent.')){jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/pending_item_delete/jrGallery/id={$_pend['pending_item_id']}')}");
            jrCore_page_table_row($dat);
        }

        $sjs = "var v = $('input:checkbox.pending_checkbox:checked').map(function(){ return this.name; }).get().join(',')";
        $tmp = jrCore_page_button("all", 'approve checked', "{$sjs};jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/pending_item_approve/all/id=,'+ v)");
        $tmp .= '&nbsp;' . jrCore_page_button("delete", 'delete checked', "if (confirm('Are you sure you want to delete all checked items?')){ {$sjs};jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/pending_item_delete/all/id='+ v )}");

        $dat             = array();
        $dat[1]['title'] = $tmp;
        jrCore_page_table_row($dat);

        jrCore_page_table_pager($_rt, $_ex);
    }
    else {
        $dat = array();
        if (!empty($_post['search_string'])) {
            $dat[1]['title'] = '<p>There were no Pending Items found to match your search criteria</p>';
        }
        else {
            $dat[1]['title'] = '<p>There are no pending items to show</p>';
        }
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    return true;
}

//---------------------------------------------------------
// SMARTY
//---------------------------------------------------------

/**
 * Get an image edit key for the aviary editor
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrGallery_get_image_edit_key($params, $smarty)
{
    if (!isset($params['item_id'])) {
        return jrCore_smarty_missing_error('item_id');
    }
    if (!jrCore_checktype($params['item_id'], 'number_nz')) {
        return jrCore_smarty_invalid_error('item_id');
    }
    $key = mt_rand(0, 1000000000);
    jrCore_set_temp_value('jrGallery', "image_edit_key_{$key}", $params['item_id']);

    // cleanup old keys
    if (jrCore_db_table_exists('jrCore', 'tempvalue')) {
        $tbl = jrCore_db_table_name('jrCore', 'tempvalue');
        $req = "DELETE FROM {$tbl} WHERE temp_module = 'jrGallery' AND temp_updated < " . (time() - 600) . " AND temp_key LIKE 'image_edit_key_%'";
        jrCore_db_query($req);
    }

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $key);
        return '';
    }
    return $key;
}

/**
 * Get a unique gallery image URL
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrGallery_get_gallery_image_url($params, $smarty)
{
    if (isset($params['item']) || !is_array($params['item'])) {
        jrCore_smarty_missing_error('item');
    }
    $out = jrGallery_get_gallery_image_url($params['item']);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Get a unique gallery image Title
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrGallery_get_gallery_image_title($params, $smarty)
{
    if (isset($params['item']) || !is_array($params['item'])) {
        jrCore_smarty_missing_error('item');
    }
    $out = jrGallery_get_gallery_image_title($params['item']);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Show Download Image button
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrGallery_download_button($params, $smarty)
{
    global $_conf;
    if (!isset($params['item']) || !is_array($params['item'])) {
        return jrCore_smarty_missing_error('item');
    }
    $out = '';
    $_it = $params['item'];
    if (isset($_it['gallery_image_size']) && jrCore_checktype($_it['gallery_image_size'], 'number_nz')) {

        // We have a valid file - check for allowed downloads
        if (isset($_conf['jrGallery_download']) && $_conf['jrGallery_download'] == 'on') {

            $allow = false;
            if (jrUser_can_edit_item($_it)) {
                // Admins and profile owners can always download
                $allow = true;
            }
            // NOTE: If an gallery item has NO PRICE, but is part of a BUNDLE, and is
            // not marked "Bundle Only" AND we allow downloads, show download button
            elseif ((!isset($_it['gallery_image_item_price']) || strlen($_it['gallery_image_item_price']) === 0 || $_it['gallery_image_item_price'] == 0) && (!isset($_it['gallery_bundle_only']) || $_it['gallery_bundle_only'] != 'on')) {
                $allow = true;
            }
            elseif (isset($_it['gallery_bundle_only']) && $_it['gallery_bundle_only'] == 'on') {
                $allow = false;
            }
            // NOTE: gallery_image_item_price is already checked in core download magic view
            // We just need to check to see if this gallery image is part of a paid bundle
            elseif (isset($_it['gallery_image_item_bundle']) && strlen($_it['gallery_image_item_bundle']) > 0) {
                $_id = array();
                foreach (explode(',', $_it['gallery_image_item_bundle']) as $bid) {
                    $_id[] = (int) $bid;
                }
                $_bi = jrCore_db_get_multiple_items('jrFoxyCartBundle', $_id, array('bundle_item_price'));
                if ($_bi && is_array($_bi)) {
                    $block = false;
                    foreach ($_bi as $_bun) {
                        if (isset($_bun['bundle_item_price']) && $_bun['bundle_item_price'] > 0) {
                            $block = true;
                            break;
                        }
                    }
                    if (!$block) {
                        $allow = true;
                    }
                }
            }
            else {
                $allow = true;
            }
            if ($allow) {
                $url = jrCore_get_module_url('jrGallery');
                $ttl = (isset($_it['gallery_image_title'])) ? $_it['gallery_image_title'] : $_it['gallery_image_name'];
                $ttl = jrGallery_url_name($ttl);
                $out = "<a href=\"{$_conf['jrCore_base_url']}/{$url}/download/gallery_image/{$_it['_item_id']}/{$ttl}\">" . jrCore_get_icon_html('download') . '</a>';
            }
        }
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

//------------------------------------
// WIDGETS
//------------------------------------

/**
 * Display CONFIG screen for Gallery Images
 * @param $_post array Post info
 * @param $_user array User array
 * @param $_conf array Global Config
 * @param $_wg array Widget info
 * @return bool
 */
function jrGallery_widget_gallery_config($_post, $_user, $_conf, $_wg)
{
    // Widget Content
    $_tmp = array(
        'name'     => 'gallery_list',
        'type'     => 'hidden',
        'validate' => 'printable',
        'value'    => $_wg['gallery_data']['gallery_list']
    );
    jrCore_form_field_create($_tmp);

    // Images per row
    $_opts = array(
        12 => "1 image per row",
        6  => "2 images per row",
        4  => "3 images per row",
        3  => "4 images per row",
        2  => "6 images per row",
        1  => "12 images per row",

    );
    $_tmp  = array(
        'name'     => 'gallery_cols',
        'type'     => 'select',
        'options'  => $_opts,
        'default'  => 3,
        'validate' => 'number_nz',
        'label'    => 'images per row',
        'help'     => 'How many images would you like to show in each row of the output?',
        'value'    => (isset($_wg['gallery_data']['gallery_cols'])) ? $_wg['gallery_data']['gallery_cols'] : 3
    );
    jrCore_form_field_create($_tmp);

    // header
    $html = jrCore_parse_template('widget_config_header.tpl', $_wg, 'jrGallery');
    jrCore_page_custom($html);

    return true;
}

/**
 * Get Widget results from posted Config data
 * @param $_post array Post info
 * @return array
 */
function jrGallery_widget_gallery_config_save($_post)
{
    $_sv = array(
        'gallery_list' => $_post['gallery_list'],
        'gallery_cols' => $_post['gallery_cols'],
    );
    return array('gallery_data' => $_sv);
}

/**
 * Widget DISPLAY
 * @param $_widget array Widget info
 * @return string
 */
function jrGallery_widget_gallery_display($_widget)
{
    $_rt = array();
    $_dt = $_widget['gallery_data'];
    if (isset($_dt['gallery_list'])) {
        $_sp = array(
            'search'                       => array(
                "_item_id in {$_dt['gallery_list']}"
            ),
            'exclude_jrProfile_quota_keys' => true,
            'limit'                        => substr_count($_dt['gallery_list'], ',') + 1
        );
        $_rt = jrCore_db_search_items('jrGallery', $_sp);
    }
    switch ($_dt['gallery_cols']) {
        case '12':
        case '6':
        case '4':
        case '3':
        case '2':
        case '1':
            $_rt['gallery_cols'] = $_dt['gallery_cols'];
            break;
        default:
            $_rt['gallery_cols'] = '4';
            break;
    }
    return jrCore_parse_template('widget_gallery_display.tpl', $_rt, 'jrGallery');
}
