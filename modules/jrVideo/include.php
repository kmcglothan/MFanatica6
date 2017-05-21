<?php
/**
 * Jamroom Video module
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
function jrVideo_meta()
{
    $_tmp = array(
        'name'        => 'Video',
        'url'         => 'video',
        'version'     => '1.5.2',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Create and stream Video - transcodes for both desktop and mobile viewing',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/293/video',
        'category'    => 'profiles',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrVideo_init()
{
    global $_conf;

    // Event listeners
    // We listen for the "save_media_file" event - we will then add
    // in our video specific fields to the DataStore item
    jrCore_register_event_listener('jrCore', 'save_media_file', 'jrVideo_save_media_file_listener');

    // JS
    jrCore_register_module_feature('jrCore', 'javascript', 'jrVideo', 'jrVideo.js');

    // We listen for the jrUrlScan 'url_found' trigger and if its a video url, add appropriate data to its array
    jrCore_register_event_listener('jrUrlScan', 'url_found', 'jrVideo_url_found_listener');

    // We also provide support for the "video" form field type
    jrCore_register_module_feature('jrCore', 'form_field', 'jrVideo', 'video');

    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrVideo', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrVideo', 'update');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrVideo', 'create_album');

    // Core support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrVideo', 'on');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrVideo', 'on');
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrVideo', 'on');
    jrCore_register_module_feature('jrCore', 'item_order_support', 'jrVideo', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrVideo', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrVideo', 'update', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrVideo', 'create_album', 'item_action.tpl');

    // We provide 2 video player skins
    jrCore_register_module_feature('jrCore', 'media_player', 'jrVideo', 'jrVideo_player_dark', 'video');
    jrCore_register_module_feature('jrCore', 'media_player', 'jrVideo', 'jrVideo_blue_monday', 'video');
    jrCore_register_module_feature('jrCore', 'media_player', 'jrVideo', 'jrVideo_black_overlay_player', 'video');
    jrCore_register_module_feature('jrCore', 'media_player', 'jrVideo', 'jrVideo_gray_overlay_player', 'video');
    jrCore_register_module_feature('jrCore', 'media_player', 'jrVideo', 'jrVideo_solo_player', 'video');

    // We support video conversions
    $max = (isset($_conf['jrVideo_conversion_worker_count'])) ? intval($_conf['jrVideo_conversion_worker_count']) : 1;
    jrCore_register_queue_worker('jrVideo', 'video_conversions', 'jrVideo_convert_file', 0, $max, 14400);
    jrCore_register_queue_worker('jrVideo', 'create_video_sample', 'jrVideo_create_video_sample', 0, $max, 7200);

    // Block downloads of FLV and MP4 files
    jrCore_register_event_listener('jrCore', 'download_file', 'jrVideo_download_file_listener');
    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrVideo_db_search_params_listener');
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrVideo_db_search_items_listener');
    jrCore_register_event_listener('jrCore', 'db_get_item', 'jrVideo_db_get_item_listener');
    jrCore_register_event_listener('jrCore', 'stream_file', 'jrVideo_stream_file_listener');
    jrCore_register_event_listener('jrCore', 'media_player_params', 'jrVideo_media_player_params_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrVideo_verify_module_listener');

    // When an action is shared via jrOneAll, we can provide the text of the shared item
    jrCore_register_event_listener('jrOneAll', 'network_share_text', 'jrVideo_network_share_text_listener');

    // Bring in pricing if FoxyCart module is installed
    jrCore_register_event_listener('jrFoxyCart', 'add_price_field', 'jrVideo_add_price_field_listener');
    jrCore_register_event_listener('jrFoxyCart', 'adding_item_to_purchase_history', 'jrVideo_adding_item_to_purchase_history_listener');
    jrCore_register_event_listener('jrFoxyCart', 'my_earnings_row', 'jrVideo_my_earnings_row_listener');

    jrCore_register_module_feature('jrFoxyCartBundle', 'visible_support', 'jrVideo', true);
    jrCore_register_event_listener('jrFoxyCartBundle', 'get_album_field', 'jrVideo_get_album_field_listener');
    jrCore_register_event_listener('jrFoxyCartBundle', 'add_bundle_price_field', 'jrVideo_add_bundle_price_field_listener');

    // We can be hidden but included in bundles
    jrCore_register_module_feature('jrFoxyCartBundle', 'bundle_only_support', 'jrVideo', 'create');
    jrCore_register_module_feature('jrFoxyCartBundle', 'bundle_only_support', 'jrVideo', 'update');

    // We have fields that can be searched
    jrCore_register_module_feature('jrSearch', 'search_fields', 'jrVideo', 'video_title', 39);

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'jrVideo', 'profile_jrVideo_item_count', 39);

    // RSS Format
    jrCore_register_event_listener('jrFeed', 'create_rss_feed', 'jrVideo_create_rss_feed_listener');

    // Core item buttons
    $_tmp = array(
        'title'  => 'create album button',
        'icon'   => 'star2',
        'active' => 'on',
        'group'  => 'owner'
    );
    jrCore_register_module_feature('jrCore', 'item_index_button', 'jrVideo', 'jrVideo_create_album_button', $_tmp);

    $_tmp = array(
        'title'  => 'download video button',
        'icon'   => 'download',
        'active' => 'on',
        'group'  => 'user'
    );
    jrCore_register_module_feature('jrCore', 'item_list_button', 'jrVideo', 'jrVideo_item_download_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrVideo', 'jrVideo_item_download_button', $_tmp);

    jrCore_register_module_feature('jrSiteBuilder', 'widget', 'jrVideo', 'widget_video_player', 'Video Player');

    // We can be added to the Combined Video module
    $_tmp = array(
        'alt'   => 29,
        'title' => 63
    );
    jrCore_register_module_feature('jrCombinedVideo', 'combined_support', 'jrVideo', 'create', $_tmp);

    // Quick Share Tabs
    $_tm = array(
        'title' => 63,
        'icon'  => 'play'
    );
    jrCore_register_module_feature('jrAction', 'quick_share', 'jrVideo', 'jrVideo_quick_share_video', $_tm);

    jrCore_register_module_feature('jrTips', 'tip', 'jrVideo', 'tip');


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
function jrVideo_quick_share_video($_post, $_user, $_conf)
{
    return jrCore_parse_template('item_action_quick_share.tpl', $_user, 'jrVideo');
}

/**
 * Quick Share save
 * @param $_post array Posted info
 * @param $_user array Active User info
 * @param $_conf array Global Config
 * @return string
 */
function jrVideo_quick_share_video_save($_post, $_user, $_conf)
{
    // Prevent core from handling this upload
    jrCore_disable_automatic_upload_handling();

    if (!isset($_post['video_title']) || strlen($_post['video_title']) === 0) {
        return 'FIELD: video_title';
    }
    if (!jrCore_is_uploaded_media_file('jrVideo', 'video_file', $_user['user_active_profile_id'])) {
        $_ln = jrUser_load_lang_strings();
        return "ERROR: {$_ln['jrVideo'][60]}";
    }

    // Save video
    $_rt = array(
        'video_title'     => $_post['video_title'],
        'video_title_url' => jrCore_url_string($_post['video_title']),
        'video_album'     => 'Timeline',
        'video_album_url' => 'timeline'
    );

    // We don't want to show this video file in lists and on the site if
    // it is being converted - set our active flag to 0 if we're converting
    $_rt['video_active'] = 'on';
    if (isset($_user['quota_jrVideo_video_conversions']) && $_user['quota_jrVideo_video_conversions'] == 'on') {
        $_rt['video_active'] = 'off';
    }
    $vid = jrCore_db_create_item('jrVideo', $_rt);
    if (!$vid) {
        $_ln = jrUser_load_lang_strings();
        return "ERROR: {$_ln['jrVideo'][18]}";
    }

    // Save files
    $_rt['_item_id'] = $vid;
    jrCore_save_media_file('jrVideo', 'video_file', $_user['user_active_profile_id'], $vid);
    jrCore_delete_upload_temp_directory($_post['upload_token']);

    // Check for uploaded files and convert
    if (isset($_user['quota_jrVideo_video_conversions']) && $_user['quota_jrVideo_video_conversions'] == 'on') {

        $_queue = array(
            'file_name'     => 'video_file',
            'quota_id'      => $_user['profile_quota_id'],
            'profile_id'    => $_user['user_active_profile_id'],
            'item_id'       => $vid,
            'screenshot'    => 1,
            'sample'        => false,
            'sample_length' => $_conf['jrVideo_sample_length'],
            'max_workers'   => intval($_conf['jrVideo_conversion_worker_count'])
        );
        jrCore_queue_create('jrVideo', 'video_conversions', $_queue);
    }

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'create', 'jrVideo', $vid);

    return true;
}

//------------------------------------
// WIDGETS
//------------------------------------

/**
 * Display CONFIG screen for HTML Editor Widget
 * @param $_post array Post info
 * @param $_user array User array
 * @param $_conf array Global Config
 * @param $_wg array Widget info
 * @return bool
 */
function jrVideo_widget_video_player_config($_post, $_user, $_conf, $_wg)
{
    // Widget Content
    $_tmp = array(
        'name'     => 'video_playlist',
        'type'     => 'hidden',
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // header
    $html = jrCore_parse_template('widget_config_header.tpl', $_wg, 'jrVideo');
    jrCore_page_custom($html);

    return true;
}

/**
 * Get Widget results from posted Config data
 * @param $_post array Post info
 * @return array
 */
function jrVideo_widget_video_player_config_save($_post)
{
    return array('video_playlist' => $_post['video_playlist']);
}

/**
 * HTML Editor Widget DISPLAY
 * @param $_widget array Page Widget info
 * @return string
 */
function jrVideo_widget_video_player_display($_widget)
{
    global $_conf;
    $_widget['module']  = 'jrVideo';
    $_widget['field']   = 'video_file';
    $_widget['search1'] = "_item_id in {$_widget['video_playlist']}";
    $skin               = $_conf['jrCore_active_skin'];
    if (isset($_conf["{$skin}_player_type"])) {
        if (jrCore_module_is_active('jrPlaylist') && strpos($_widget['video_playlist'], ',')) {
            $_widget['type'] = "jrPlaylist_" . $_conf["{$skin}_player_type"];
        }
        else {
            $_widget['type'] = "jrVideo_" . $_conf["{$skin}_player_type"];
        }
    }
    $smarty = new stdClass;
    return smarty_function_jrCore_media_player($_widget, $smarty);
}

//------------------------------------
// ITEM BUTTONS
//------------------------------------

/**
 * Return "create album" button for index
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrVideo_create_album_button($module, $_item, $_args, $smarty, $test_only = false)
{
    global $_conf;
    if ($module == 'jrVideo') {
        if ($test_only) {
            return true;
        }
        if (jrProfile_is_profile_owner($_args['profile_id'])) {
            $url = jrCore_get_module_url('jrVideo');
            $_rt = array(
                'url'  => "{$_conf['jrCore_base_url']}/{$url}/create_album",
                'icon' => 'star2',
                'alt'  => 45
            );
            return $_rt;
        }
    }
    return false;
}

/**
 * Return "download" button for the item
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrVideo_item_download_button($module, $_item, $_args, $smarty, $test_only = false)
{
    global $_conf;
    if ($module == 'jrVideo') {
        if ($test_only) {
            return true;
        }
        if (jrCore_checktype($_item['video_file_size'], 'number_nz')) {

            $allow = false;
            if (jrUser_can_edit_item($_item)) {
                // Admins and profile owners can always download
                $allow = true;
            }
            // Are we turned off?
            elseif (isset($_conf['jrVideo_block_download']) && $_conf['jrVideo_block_download'] == 'on') {
                return false;
            }
            // NOTE: If a video item has NO PRICE, but is part of a BUNDLE, and is
            // not marked "Bundle Only" AND we allow downloads, show download button
            elseif ((!isset($_item['video_file_item_price']) || strlen($_item['video_file_item_price']) === 0 || $_item['video_file_item_price'] == 0) && (!isset($_item['video_bundle_only']) || $_item['video_bundle_only'] != 'on')) {
                $allow = true;
            }
            elseif (isset($_item['video_bundle_only']) && $_item['video_bundle_only'] == 'on') {
                $allow = false;
            }
            // NOTE: video_file_item_price is already checked in core download magic view
            // We just need to check to see if this video item is part of a paid bundle
            elseif (isset($_item['video_file_item_bundle']) && strlen($_item['video_file_item_bundle']) > 0) {
                $_id = array();
                foreach (explode(',', $_item['video_file_item_bundle']) as $bid) {
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
                $url = jrCore_get_module_url('jrVideo');
                $_rt = array(
                    'url'  => "{$_conf['jrCore_base_url']}/{$url}/download/video_file/{$_item['_item_id']}/{$_item['video_title_url']}",
                    'icon' => 'download'
                );
                return $_rt;
            }
        }
    }
    return false;
}

//------------------------------------
// SMARTY FUNCTIONS
//------------------------------------

/**
 * {jrVideo_util}
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrVideo_util($params, $smarty)
{
    global $_user;
    switch ($params['mode']) {
        case 'get_my_videos':
            $_params = array(
                'search'   => array(
                    "_profile_id = {$_user['user_active_profile_id']}",
                ),
                "order_by" => array(
                    '_item_id' => 'DESC'
                ),
                "limit"    => 100
            );
            $_rt     = jrCore_db_search_items('jrVideo', $_params);
            if (!empty($params['assign'])) {
                $smarty->assign($params['assign'], $_rt['_items']);
                return '';
            }
            break;
    }
    return '';
}

//------------------------------------
// VIDEO EVENT LISTENERS
//------------------------------------

/**
 * Reset video player name
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrVideo_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    $tbl = jrCore_db_table_name('jrCore', 'setting');
    $req = "SELECT * FROM {$tbl} WHERE `name` = 'player_type' AND `value` LIKE '%midnight%'";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $_cfg) {
            jrCore_set_setting_value($_cfg['module'], 'player_type', 'player_dark');
        }
        jrCore_delete_config_cache();
    }

    // If the combined video module is installed, we need to change our URL
    if (jrCore_module_is_active('jrCombinedVideo')) {
        $tbl = jrCore_db_table_name('jrCore', 'module');
        $req = "UPDATE {$tbl} SET `module_url` = 'uploaded_video' WHERE `module_directory` = 'jrVideo' AND `module_url` = 'video' LIMIT 1";
        jrCore_db_query($req);
    }

    return $_data;
}

/**
 * Set video solution based on accessing device
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrVideo_media_player_params_listener($_data, $_user, $_conf, $_args, $event)
{
    // Desktops use Flash for quality
    if (!jrCore_is_mobile_device() && $_args['module'] == 'jrVideo') {
        $_data['solution'] = 'flash,html';
    }
    return $_data;
}

/**
 * Skip sample if we are NOT doing samples
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrVideo_stream_file_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if ($_args['module'] == 'jrVideo' && isset($_conf['jrVideo_sample_length']) && intval($_conf['jrVideo_sample_length']) === 0) {
        // No sample even if we have a file
        $_data['stream_file'] = "{$_post['module']}_{$_post['_2']}_{$_post['_1']}." . $_data["{$_post['_1']}_extension"];
    }
    return $_data;
}

/**
 * Block downloads of FLV and MP4 files if configured
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrVideo_download_file_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_conf['jrVideo_block_download']) && $_conf['jrVideo_block_download'] == 'on' && !jrUser_is_admin()) {
        // Check for extension
        switch ($_data["{$_args['file_name']}_extension"]) {
            case 'flv':
            case 'mp4':
                jrCore_notice_page('Error', $_data["{$_args['file_name']}_extension"] . " files are restricted to streaming only", 'referrer');
                break;
        }
    }
    return $_data;
}

/**
 * Format RSS entries
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrVideo_create_rss_feed_listener($_data, $_user, $_conf, $_args, $event)
{
    // We override the "description" and format it differently
    if (isset($_args['module']) && $_args['module'] == 'jrVideo') {
        $_lg = jrUser_load_lang_strings();
        $pfx = $_args['prefix'];
        foreach ($_data as $k => $_itm) {
            $_data[$k]['description'] = "{$_itm['profile_name']} {$_lg['jrVideo'][33]} - &quot;{$_itm["{$pfx}_title"]}&quot;";
        }
    }
    return $_data;
}

/**
 * Return video_album field for Bundle module
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrVideo_get_album_field_listener($_data, $_user, $_conf, $_args, $event)
{
    $_data['jrVideo'] = 'video_album';
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
function jrVideo_network_share_text_listener($_data, $_user, $_conf, $_args, $event)
{
    // $_data:
    // [providers] => twitter
    // [user_token] => c6418e9a-b66e-4c6c-xxxx-cdea7e915d03
    // [user_id] => 1
    // [action_module] => jrVideo
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
    $url = jrCore_get_module_url('jrVideo');
    $txt = $_ln['jrVideo'][33];
    if ($_data['action_mode'] == 'update') {
        $txt = $_ln['jrVideo'][51];
    }
    $_out = array(
        'text' => "{$_conf['jrCore_base_url']}/{$_data['profile_url']} {$_data['profile_name']} {$txt}: \"{$_data['video_title']}\" {$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['_item_id']}/{$_data['video_title_url']}",
        'link' => array(
            'url'  => "{$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['_item_id']}/{$_data['video_title_url']}",
            'name' => $_data['video_title']
        )
    );
    // See if they included a picture with the song
    if (isset($_data['video_image_size']) && jrCore_checktype($_data['video_image_size'], 'number_nz')) {
        $_out['picture'] = array(
            'url' => "{$_conf['jrCore_base_url']}/{$url}/image/video_image/{$_data['_item_id']}/large"
        );
    }
    return $_out;
}

/**
 * jrVideo_save_media_file_listener
 * Adds video file meta data to saved video files
 *
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 *
 * @return array
 */
function jrVideo_save_media_file_listener($_data, $_user, $_conf, $_args, $event)
{
    $_types = jrVideo_get_video_types();
    $field  = $_args['file_name'];
    $f_ext  = (isset($_data["{$field}_extension"])) ? $_data["{$field}_extension"] : false;
    if (!$f_ext) {
        return $_data;
    }
    if (isset($_data) && is_array($_data) && is_array($_types) && isset($_types[$f_ext])) {
        $_tmp = jrCore_get_media_file_metadata($_args['saved_file'], $_args['file_name']);
        if (isset($_tmp) && is_array($_tmp)) {
            // Init streaming preview seconds
            $_tmp["{$_args['file_name']}_preview"] = 0;
            $_data                                 = array_merge($_data, $_tmp);
        }
    }
    return $_data;
}

/**
 * Return video file field for forms
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrVideo_add_price_field_listener($_data, $_user, $_conf, $_args, $event)
{
    // View => File Field
    $_data["jrVideo/create"]       = 'video_file';
    $_data["jrVideo/update"]       = 'video_file';
    $_data['jrVideo/create_album'] = 'video_file';
    return $_data;
}

/**
 * Return video file bundle fields for forms
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrVideo_add_bundle_price_field_listener($_data, $_user, $_conf, $_args, $event)
{
    // Module/View => array(Bundle Title field, Bundle File field)
    $_data['jrVideo/create_album'] = array(
        'title' => 'video_album',
        'field' => 'video_file'
    );
    return $_data;
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
function jrVideo_my_earnings_row_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['purchase_module']) && $_args['purchase_module'] == 'jrVideo') {
        $_data[1]['title'] = $_args['video_title'];
    }
    return $_data;
}

/**
 * fired when foxycart sends a confirmed order in the background back to our system.
 * If there are video files in the order, those files need to be kept in the system vault
 * so they can be downloaded.  do that moving here.
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return mixed
 */
function jrVideo_adding_item_to_purchase_history_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrVideo') {
        // a file has been sold, copy it to our system vault.
        // Make sure file is copied over to system vault
        $nam = jrCore_get_media_file_path($_args['module'], $_args['product_field'], $_data);
        if (!isset($nam) || !is_file($nam)) {
            // BAD FILE!
            jrCore_logger('CRI', "transaction received with no valid media file: {$_args['txn']['txn_id']}");
            return $_data;
        }
        $dir = APP_DIR . '/data/media/vault';
        $fil = $dir . '/' . basename($nam);
        if (!is_file($fil)) {
            if (!copy($nam, $fil)) {
                jrCore_logger('CRI', "unable to copy sold media file to system vault: {$_args['txn']['txn_id']}");
                return $_data;
            }
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
function jrVideo_url_found_listener($_data, $_user, $_conf, $_args, $event)
{
    // Is it a local video url
    $uurl = jrCore_get_module_url('jrUrlScan');
    if (strpos($_args['url'], $_conf['jrCore_base_url']) === 0) {
        $_x = explode('/', substr($_args['url'], strlen($_conf['jrCore_base_url']) + 1));
        if ($_x && is_array($_x) && isset($_x[1]) && $_x[1] == jrCore_get_module_url('jrVideo') && jrCore_checktype($_x[2], 'number_nz')) {
            $_video = jrCore_db_get_item('jrVideo', $_x[2], true);
            if ($_video && is_array($_video) && isset($_video['video_active']) && $_video['video_active'] == 'on') {
                $_data['_items'][$_args['i']]['title']    = $_video['video_title'];
                $_data['_items'][$_args['i']]['load_url'] = "{$_conf['jrCore_base_url']}/{$uurl}/parse/urlscan_player/{$_x[2]}/0/jrVideo/__ajax=1";
                $_data['_items'][$_args['i']]['url']      = $_args['url'];
            }
        }
    }
    return $_data;
}

/**
 * Hide Video items that are video_active = off from everyone but admins and profile owners
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrVideo_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrVideo' && !jrUser_is_admin()) {
        if ($pid = jrProfile_is_profile_view()) {
            if (!jrUser_is_logged_in() || !jrProfile_is_profile_owner($pid)) {
                if (!isset($_data['search'])) {
                    $_data['search'] = array();
                }
                $_data['search'][] = 'video_active = on';
            }
        }
    }
    return $_data;
}

/**
 * Expand height / width if we have it
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrVideo_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_is_view_request() && $_args['module'] == 'jrVideo' && isset($_data['_items'])) {
        foreach ($_data['_items'] as $k => $_v) {
            if (isset($_v['video_file_resolution']) && strpos($_v['video_file_resolution'], 'x')) {
                list($w, $h) = explode('x', $_v['video_file_resolution'], 2);
                $_data['_items'][$k]['video_file_resolution_width']  = intval($w);
                $_data['_items'][$k]['video_file_resolution_height'] = intval($h);
            }
        }
    }
    return $_data;
}

/**
 * Expand height / width if we have it
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrVideo_db_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_is_view_request() && isset($_args['module']) && $_args['module'] == 'jrVideo') {
        if (isset($_data['video_file_resolution']) && strpos($_data['video_file_resolution'], 'x')) {
            list($w, $h) = explode('x', $_data['video_file_resolution'], 2);
            $_data['video_file_resolution_width']  = intval($w);
            $_data['video_file_resolution_height'] = intval($h);
        }
    }
    return $_data;
}

//---------------------------------------------------------
// VIDEO FORM FUNCTIONS
//---------------------------------------------------------

/**
 * Display a Video upload field in a form
 * @param $_field array form field info
 * @param null $_att array additional attributes
 * @return bool
 */
function jrVideo_form_field_video_display($_field, $_att = null)
{
    global $_user;
    // Get existing video if we have one - the "value" we get will
    // be the unique id for the video we are loading.
    $htm = '';
    if (isset($_field['value']) && is_array($_field['value'])) {
        $nam = $_field['name'];
        if (isset($_field['value']["{$nam}_size"]) && jrCore_checktype($_field['value']["{$nam}_size"], 'number_nz')) {
            $_key = array('original_name', 'type', 'size', 'time', 'extension', 'bitrate', 'smprate', 'length', 'resolution', 'access', 'preview');
            $_rep = array(
                'item' => array(
                    'field_name' => $nam
                )
            );
            $_fld = array('_item_id', 'video_title', 'video_album', 'video_album_url', 'video_genre', 'profile_url', 'user_name');
            foreach ($_fld as $fld) {
                if (isset($_field['value'][$fld])) {
                    $_rep['item'][$fld] = $_field['value'][$fld];
                }
            }
            //file attributes
            foreach ($_key as $v) {
                if (isset($_field['value']["{$nam}_{$v}"])) {
                    $_rep['item']["video_file_{$v}"] = $_field['value']["{$nam}_{$v}"];
                }
            }
            $htm = jrCore_parse_template('video_update.tpl', $_rep, 'jrVideo');
            // Next - we need to see if this is a MULTIPLE upload field - if it is, we need
            // need to show a video box for EACH video file stored for this item
            $i = 2;
            while (true) {
                if (isset($_field['value']["{$nam}_{$i}_size"]) && jrCore_checktype($_field['value']["{$nam}_{$i}_size"], 'number_nz')) {
                    $_rep = array(
                        'item' => array(
                            '_item_id'   => $_field['value']['_item_id'],
                            'field_name' => "{$nam}_{$i}"
                        )
                    );
                    foreach ($_key as $v) {
                        $_rep['item']["video_file_{$v}"] = $_field['value']["{$nam}_{$i}_{$v}"];
                    }
                    $htm .= jrCore_parse_template('video_update.tpl', $_rep, 'jrVideo');
                }
                else {
                    break;
                }
                $i++;
            }
        }
    }
    $_field['html']     = $htm;
    $_field['type']     = 'video';
    $_field['template'] = 'form_field_elements.tpl';

    // We have a file upload - we need to turn on the progress meter if enabled
    $_field['multiple'] = (isset($_field['multiple'])) ? $_field['multiple'] : false;
    if (isset($_field['is_form_designer_field']) && $_field['is_form_designer_field'] === true) {
        $_user['quota_jrVideo_allowed_video_types'] = 'flv';
    }
    $_field = jrCore_enable_meter_support($_field, $_user['quota_jrVideo_allowed_video_types'], jrCore_get_max_allowed_upload($_user['quota_jrCore_max_upload_size']), $_field['multiple']);

    // add to our page element
    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * Defines Form Designer field options
 * @return string
 */
function jrVideo_form_field_video_form_designer_options()
{
    return array(
        'disable_validation'  => true,
        'disable_default'     => true,
        'disable_options'     => true,
        'disable_min_and_max' => true
    );
}

/**
 * Additional form field HTML attributes that can be passed in via the form
 */
function jrVideo_form_field_video_attributes()
{
    return array('disabled', 'readonly', 'maxlength', 'onfocus', 'onblur', 'onselect', 'onkeypress');
}

/**
 * Check to be sure validation is on if field is required
 * @param $_field array Array of Field Parameters
 * @param $_post array Posted Data for checking
 * @return array
 */
function jrVideo_form_field_video_params($_field, $_post)
{
    if (!isset($_field['validate'])) {
        $_field['validate'] = 'not_empty';
    }
    if (!isset($_field['error_msg'])) {
        $_lang               = jrUser_load_lang_strings();
        $_field['error_msg'] = $_lang['jrVideo'][60];
    }
    return $_field;
}

/**
 * jrVideo_form_field_video_is_empty
 * Checks to see if we received data on our post in the form validator
 * @param $_field array Array of Field Parameters
 * @param $_post array Posted Data for checking
 * @return bool
 */
function jrVideo_form_field_video_is_empty($_field, $_post)
{
    global $_user;
    // Make sure we got a File..
    $tmp = jrCore_is_uploaded_media_file($_field['module'], $_field['name'], $_user['user_active_profile_id']);
    if (!$tmp) {
        return true;
    }
    // Okay looks good
    return false;
}

/**
 * jrVideo_form_field_video_validate
 * Verify we get an uploaded file if one is required in the form
 * @param $_field array Field Information
 * @param $_post array Parsed $_REQUEST
 * @param $e_msg string Error message for field if in error
 * @return mixed
 */
function jrVideo_form_field_video_validate($_field, $_post, $e_msg)
{
    global $_user;
    // Make sure we got a File..
    $tmp = jrCore_is_uploaded_media_file($_field['module'], $_field['name'], $_user['user_active_profile_id']);
    if (!$tmp) {
        jrCore_set_form_notice('error', $e_msg);
        return false;
    }
    // Okay looks good
    return $_post;
}

//---------------------------------------------------------
// VIDEO FUNCTIONS
//---------------------------------------------------------

/**
 * Get FFMpeg command line value including nice (priority)
 * @return string
 */
function jrVideo_get_ffmpeg_command()
{
    global $_conf;
    $ffmpeg = jrCore_check_ffmpeg_install();
    if ($ffmpeg) {
        $nice = 12;
        if (isset($_conf['jrVideo_conversion_priority']) && jrCore_checktype($_conf['jrVideo_conversion_priority'], 'number_nz')) {
            $nice = (int) $_conf['jrVideo_conversion_priority'];
        }
        return "nice -n {$nice} {$ffmpeg}";
    }
    return false;
}

/**
 * Get FFMpeg command line THREADS value
 * @return string
 */
function jrVideo_get_ffmpeg_thread()
{
    global $_conf;
    $threads = 1;
    if (isset($_conf['jrVideo_conversion_priority']) && $_conf['jrVideo_conversion_priority'] == '1') {
        $threads = 0;
    }
    return $threads;
}

/**
 * Create a "sample" of an existing video file
 * @param $profile_id integer Unique Profile ID that video file belongs to
 * @param $video_id integer Unique Video ID to create sample for
 * @param $_video array Video information array
 * @param $extension string FLV|M4V
 * @param $sample_length integer Length of sample (in seconds)
 * @return string
 */
function jrVideo_create_sample($profile_id, $video_id, $_video, $extension, $sample_length = 60)
{
    // Requested sample length (in seconds)
    $sample_length = (int) $sample_length;
    if ($sample_length === 0) {
        // Nothing to do - return
        return false;
    }

    // Make sure this video item exists
    if (!isset($_video) || !is_array($_video)) {
        return false;
    }

    $field = 'video_file'; // Desktop FLV preview
    if (isset($extension) && $extension == 'm4v') {
        $field = 'video_file_mobile'; // Mobile M4V preview
    }

    // Make sure input MP3 file exists
    $input_file = jrCore_get_media_file_path('jrVideo', $field, $_video);
    if (!jrCore_media_file_exists($profile_id, $input_file)) {
        return false;
    }

    $ffmpeg = jrVideo_get_ffmpeg_command();

    // Our command to create our sample file
    // "q" for quarter of a sine wave
    // "h" for half a sine wave
    // "t" for linear (‘triangular’) slope
    // "l" for logarithmic
    // "p" for inverted parabola

    // See how long of a sample has been requested - we want to try and snip
    // a section out from the middle of the song for our sample.
    list($h, $m, $s) = explode(':', $_video['video_file_length']);
    $total = (($h * 60) * 60) + ($m * 60) + $s;

    // If our song is shorter than our requested sample length
    if ($total < $sample_length) {
        $sample_length = round($total / 2);
    }

    $t = "00:01:00.0";
    if ($sample_length < 60) {
        $t = "00:00:{$sample_length}.0";
    }

    // Error file
    $dir = jrCore_get_module_cache_dir('jrVideo');
    $tmp = tempnam($dir, 'ffmpeg_errors_');

    // Convert file
    ob_start();
    system("{$ffmpeg} -i \"{$input_file}\" -threads " . jrVideo_get_ffmpeg_thread() . " -t {$t} -vcodec copy -acodec copy \"{$input_file}.sample.{$extension}\" >/dev/null 2>{$tmp}", $ret);
    ob_end_clean();

    // See if we had errors
    if (is_file($tmp) && filesize($tmp) > 0) {
        $tmp = file_get_contents($tmp);
        if (stristr($tmp, 'FAIL')) {
            jrCore_logger('MAJ', "errors encountered creating {$extension} video sample", $tmp);
        }
    }
    @unlink($tmp);
    return true;
}

/**
 * Create a video "sample" for videos that are for sale
 * @param array $_queue The queue entry the worker will receive
 * @return bool
 */
function jrVideo_create_video_sample($_queue)
{
    if (!isset($_queue) || !is_array($_queue)) {
        return false;
    }

    // Our queue entry will contain the item ID and the Quota ID
    // for the item being submitted for conversion
    $_qt = jrProfile_get_quota($_queue['quota_id']);
    if (!isset($_qt) || !is_array($_qt)) {
        jrCore_logger('CRI', "invalid quota_id received in queue entry: {$_queue['quota_id']}");
        return true; // Bad queue entry - remove it
    }

    // Get the item
    $_it = jrCore_db_get_item('jrVideo', $_queue['item_id']);
    if (!isset($_it) || !is_array($_it)) {
        jrCore_logger('CRI', "invalid item_id received in queue entry: {$_queue['item_id']}");
        return true; // Bad queue entry - remove it
    }

    // Make sure the file we are converting exists
    $input_file = jrCore_get_media_file_path('jrVideo', $_queue['file_name'], $_it);
    if (!is_file($input_file)) {
        jrCore_logger('CRI', "invalid item_id received in queue entry: {$_queue['item_id']} - unable to open input file: {$_queue['file_name']} for reading", $_queue);
        return true; // Bad queue entry - remove it
    }
    // Create our FLV Sample
    if ($_queue['sample']) {
        if (jrVideo_create_sample($_queue['profile_id'], $_queue['item_id'], $_it, 'flv', $_queue['sample_length'])) {
            // Delete any existing sample
            jrCore_delete_media_file($_queue['profile_id'], "{$input_file}.sample.flv");
        }
    }
    else {
        // We're not creating a sample - make sure any old one is removed
        jrCore_delete_media_file($_queue['profile_id'], "{$input_file}.sample.flv");
    }

    // Create our M4V (mobile) Sample
    $input_file = preg_replace("/\\.[^.\\s]{3,4}$/", "", $input_file) . '_mobile.m4v';
    if ($_queue['sample']) {
        if (!jrVideo_create_sample($_queue['profile_id'], $_queue['item_id'], $_it, 'm4v', $_queue['sample_length'])) {
            // Delete any existing sample
            jrCore_delete_media_file($_queue['profile_id'], "{$input_file}.sample.flv");
        }
    }
    else {
        // We're not creating a sample - make sure any old one is removed
        jrCore_delete_media_file($_queue['profile_id'], "{$input_file}.sample.m4v");
    }
    return true;
}

/**
 * Convert a Video file to the proper formats for streaming,
 * or to an FLV file if not using the Jamroom Network.
 * @param array $_queue The queue entry the worker will receive
 * @return bool
 */
function jrVideo_convert_file($_queue)
{
    if (!isset($_queue) || !is_array($_queue)) {
        return false;
    }

    // Our queue entry will contain the item ID and the Quota ID
    // for the item being submitted for conversion
    $_qt = jrProfile_get_quota($_queue['quota_id']);
    if (!is_array($_qt)) {
        jrCore_logger('CRI', "invalid quota_id received in queue entry: {$_queue['quota_id']}");
        return true; // Bad queue entry - remove it
    }

    // Make sure profile is valid
    $_pr = jrCore_db_get_item('jrProfile', $_queue['profile_id'], true);
    if (!is_array($_pr)) {
        jrCore_logger('CRI', "invalid profile_id received in queue entry: {$_queue['profile_id']}");
        return true; // Bad queue entry - remove it
    }

    // Get the item
    $_it = jrCore_db_get_item('jrVideo', $_queue['item_id']);
    if (!is_array($_it)) {
        jrCore_logger('CRI', "invalid item_id received in queue entry: {$_queue['item_id']}");
        return true; // Bad queue entry - remove it
    }

    // Make sure the file we are converting exists
    $input_file = jrCore_get_media_file_path('jrVideo', $_queue['file_name'], $_it);
    $input_save = $input_file;
    $input_name = preg_replace("/\\.[^.\\s]{3,4}$/", "", basename($input_file));
    if (!jrCore_media_file_exists($_queue['profile_id'], $input_file)) {
        jrCore_logger('CRI', "invalid item_id received in queue entry: {$_queue['item_id']} - unable to open input file: {$_queue['file_name']} for reading", $input_file);
        return true;
    }

    // Confirm media file is a "local" file
    // If $input_file is on a remote FS (S3) then it will be copied locally
    // $input_file = $input_orig if we are NOT doing re-conversions
    $input_file = jrCore_confirm_media_file_is_local($_queue['profile_id'], basename($input_file));
    if (!$input_file) {
        jrCore_logger('CRI', "unable to confirm local video file for conversion");
        return true;
    }

    // Log start time
    $start = explode(' ', microtime());
    $start = $start[1] + $start[0];

    // See what type of file we are converting and if we support it
    $ext = jrCore_file_extension($input_file);
    if (!is_file(APP_DIR . "/modules/jrVideo/plugins/{$ext}.php")) {
        // We don't support this format
        jrCore_logger('CRI', "invalid file type received for conversion: {$ext} - type is not supported");
        return true; // Bad queue entry - remove it
    }
    require_once APP_DIR . "/modules/jrVideo/plugins/{$ext}.php";

    // First - setup an error file we will use to watch for errors
    $cdr = jrCore_get_module_cache_dir('jrVideo');
    $err = tempnam($cdr, 'conversion');

    //---------------------------
    // SAVE ORIGINAL
    //---------------------------
    if (!jrCore_copy_media_file($_queue['profile_id'], $input_file, basename($input_file) . '.original.' . $ext)) {
        jrCore_logger('CRI', "unable to save original video file for: {$_queue['file_name']}/{$_queue['item_id']}");
        return true;
    }

    //---------------------------
    // DECODE
    //---------------------------
    $func = "jrVideo_{$ext}_decode";
    if (function_exists($func)) {

        $tmp = $func($input_file, $_queue, $err);
        // If we encounter an error, the plugin will return false.  The
        // plugin is responsible for logging and error checking
        if (!$tmp) {
            unlink($err);
            return 1; // Count as a try
        }
        // If we have decoded, we use the OUTPUT of the decode step
        // as the new INPUT to the encode step
        if ($tmp != $input_file) {
            $input_file = $tmp;
        }
    }

    //---------------------------
    // ENCODE - FLV
    //---------------------------
    $func = "jrVideo_flv_encode";
    require_once APP_DIR . "/modules/jrVideo/plugins/flv.php";
    if (function_exists($func) && isset($_it["{$_queue['file_name']}_extension"]) && $_it["{$_queue['file_name']}_extension"] != 'flv') {

        // If we are NOT an FLV already
        $tmp = $func($input_file, $_queue, $err);

        // If we encounter an error, the plugin will return false.  The
        // plugin is responsible for logging and error checking
        if (!$tmp) {
            unlink($err);
            return true;
        }
        if (!is_file($tmp) || filesize($tmp) < 200) {
            jrCore_logger('CRI', "unable to convert FLV video file for: {$_queue['file_name']}/{$_queue['item_id']}", file_get_contents($err));
            return true;
        }

        // This is now our CONVERTED FLV - rename and move into place
        $input_size = filesize($tmp);
        if (!jrCore_write_media_file($_queue['profile_id'], "{$input_name}.flv", $tmp)) {
            jrCore_logger('CRI', "unable to create converted FLV video file for: {$_queue['file_name']}/{$_queue['item_id']}");
            return true;
        }
        unlink($tmp);

    }
    else {

        // We are ALREADY an FLV file
        $input_size = filesize($input_file);
        $input_save = false;

    }

    // New DS entries for FLV file and Original
    $_data = array(
        "{$_queue['file_name']}_name"               => basename($input_file),
        "{$_queue['file_name']}_time"               => time(),
        "{$_queue['file_name']}_size"               => $input_size,
        "{$_queue['file_name']}_type"               => 'video/mpeg',
        "{$_queue['file_name']}_extension"          => 'flv',
        "{$_queue['file_name']}_original_name"      => $_it["{$_queue['file_name']}_name"],
        "{$_queue['file_name']}_original_time"      => $_it["{$_queue['file_name']}_time"],
        "{$_queue['file_name']}_original_size"      => $_it["{$_queue['file_name']}_size"],
        "{$_queue['file_name']}_original_type"      => $_it["{$_queue['file_name']}_type"],
        "{$_queue['file_name']}_original_extension" => $_it["{$_queue['file_name']}_extension"]
    );

    //---------------------------
    // FLV - SAMPLE
    //---------------------------
    if ($_queue['sample'] && $_queue['sample_length'] > 0) {
        jrVideo_create_sample($_queue['profile_id'], $_queue['item_id'], array_merge($_it, $_data), 'flv', $_queue['sample_length']);
    }
    else {
        // We're not creating a sample - make sure any old one is removed
        jrCore_delete_media_file($_queue['profile_id'], "{$input_file}.sample.flv");
    }

    //---------------------------
    // ENCODE - MP4
    //---------------------------
    $func = "jrVideo_m4v_encode";
    require_once APP_DIR . "/modules/jrVideo/plugins/m4v.php";
    if (function_exists($func) && jrCore_file_extension($input_file) !== 'm4v') {

        // See if we converted or are using our uploaded file
        $tmp = $func($input_file, $_queue, $err);

        // If we encounter an error, the plugin will return false.  The
        // plugin is responsible for logging and error checking
        if (!$tmp) {
            unlink($err);
            return true;
        }
        if (!is_file($tmp) || filesize($tmp) < 200) {
            jrCore_logger('CRI', "unable to convert M4V video file for: {$_queue['file_name']}/{$_queue['item_id']}", file_get_contents($err));
            return true;
        }

        // Save off mobile version
        if (!jrCore_write_media_file($_queue['profile_id'], "{$input_name}_mobile.m4v", $tmp)) {
            jrCore_logger('CRI', "unable to create converted mobile M4V video file for: {$_queue['file_name']}/{$_queue['item_id']}");
            return true;
        }

        // Additional DS entries
        $_data["{$_queue['file_name']}_mobile_name"]      = basename("{$input_name}_mobile.m4v");
        $_data["{$_queue['file_name']}_mobile_time"]      = time();
        $_data["{$_queue['file_name']}_mobile_size"]      = filesize($tmp);
        $_data["{$_queue['file_name']}_mobile_type"]      = 'video/mp4';
        $_data["{$_queue['file_name']}_mobile_extension"] = 'm4v';

        unlink($tmp);
    }

    //---------------------------
    // M4V - SAMPLE
    //---------------------------
    if ($_queue['sample'] && $_queue['sample_length'] > 0) {
        jrVideo_create_sample($_queue['profile_id'], $_queue['item_id'], array_merge($_it, $_data), 'm4v', $_queue['sample_length']);
    }
    else {
        // We're not creating a sample - make sure any old one is removed
        jrCore_delete_media_file($_queue['profile_id'], "{$input_file}.sample.m4v");
    }

    // Finally, let's grab an image from this video to use as the video image
    // "screenshot" will be equal to 1 on CREATE only
    if (isset($_queue['screenshot']) && $_queue['screenshot'] == '1') {
        $shot = jrVideo_get_screenshot($input_file);
        if ($shot && is_file($shot)) {
            // We got our screen shot - Make sure it is copied to the media dir
            $nam = "jrVideo_{$_queue['item_id']}_video_image.jpg";
            if (!jrCore_write_media_file($_queue['profile_id'], $nam, $shot)) {
                jrCore_logger('CRI', "unable to copy video screenshot: {$nam} to profile directory");
            }
            $_tm = getimagesize($shot);

            // Image DS entries
            $_data['video_image_name']      = $nam;
            $_data['video_image_size']      = filesize($shot);
            $_data['video_image_type']      = 'image/jpeg';
            $_data['video_image_time']      = time();
            $_data['video_image_width']     = $_tm[0];
            $_data['video_image_height']    = $_tm[1];
            $_data['video_image_extension'] = 'jpg';
            @unlink($shot);
        }
    }

    // Activate new video
    $_data['video_active'] = 'on';
    jrCore_db_update_item('jrVideo', $_queue['item_id'], $_data);

    // Original is saved - cleanup
    if ($input_save) {
        jrCore_delete_media_file($_queue['profile_id'], $input_save);
    }

    $finish = explode(' ', microtime());
    $finish = $finish[1] + $finish[0];
    $total  = round(($finish - $start), 2);
    jrCore_logger('INF', "converted {$_queue['profile_id']}/{$_queue['item_id']}/{$_queue['file_name']} from {$ext} in {$total} seconds");
    jrProfile_reset_cache($_queue['profile_id']);

    // We're done - returning true tells the core to delete the queue entry
    unlink($err);
    return true;
}

/**
 * Grab a still image from a video file
 * @param $file string Video File to grab image from
 * @return mixed Returns full path to JPG image on success, bool false on failure
 */

function jrVideo_get_screenshot($file)
{
    $ffmpeg = jrVideo_get_ffmpeg_command();
    $s_time = '00:00:03';
    $_meta  = jrCore_get_media_file_metadata($file, 'tmp');
    if (isset($_meta['tmp_length']) && strpos($_meta['tmp_length'], ':')) {
        // Figure out half
        list($hrs, $mns, $scs) = explode(':', $_meta['tmp_length']);
        $scs = ((($hrs * 60) * 60) + ($mns * 60) + $scs);
        $scs = round($scs / 2);
        if ($scs > 59) {
            $scs = 59;
        }
        elseif ($scs < 10) {
            $scs = "0{$scs}";
        }
        $s_time = "00:00:{$scs}";
    }
    $cdr = jrCore_get_module_cache_dir('jrVideo');
    $err = tempnam($cdr, 'imagegrab');
    ob_start();
    system("{$ffmpeg} -i \"{$file}\" -threads " . jrVideo_get_ffmpeg_thread() . " -ss {$s_time} -an -r 1 -vframes 1 -y {$file}%d.jpg >/dev/null 2>{$err}", $ret);
    ob_end_clean();
    // Make sure we got our file
    if (is_file("{$file}1.jpg")) {
        return "{$file}1.jpg";
    }
    return false;
}

/**
 * Get valid video types installed in the system
 * @return mixed
 */
function jrVideo_get_video_types()
{
    $_tmp = glob(APP_DIR . '/modules/jrVideo/plugins/*.php');
    if (!isset($_tmp) || !is_array($_tmp)) {
        return false;
    }
    $_out = array();
    foreach ($_tmp as $file) {
        $name        = str_replace('.php', '', basename($file));
        $_out[$name] = $name;
    }
    return $_out;
}
