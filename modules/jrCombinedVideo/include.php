<?php
/**
 * Jamroom 5 Video (Combined) module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0.  Please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
 *
 * Jamroom may use modules and skins that are licensed by third party
 * developers, and licensed under a different license  - please
 * reference the individual module or skin license that is included
 * with your installation.
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
function jrCombinedVideo_meta()
{
    $_tmp = array(
        'name'        => 'Video (Combined)',
        'url'         => 'video',
        'version'     => '1.0.13',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Combine video modules into a unified Videos section on Profiles',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2946/combined-video',
        'category'    => 'profiles',
        'requires'    => 'jrSeamless',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrCombinedVideo_init()
{
    // We can be turned on/off by Quota
    $_tmp = array(
        'label' => 'Combine Videos',
        'help'  => 'If checked, all videos on profiles in this Quota will be combined into a &quot;Videos&quot; section on the profile'
    );
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrCombinedVideo', 'on', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_order_support', 'jrCombinedVideo', 'on', 'jrCombinedVideo_get_order_items');

    // Check URLs
    jrCore_register_event_listener('jrCore', 'parse_url', 'jrCombinedVideo_parse_url_listener');
    jrCore_register_event_listener('jrCore', 'module_view', 'jrCombinedVideo_module_view_listener');
    jrCore_register_event_listener('jrCore', 'profile_template', 'jrCombinedVideo_profile_template_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrCombinedVideo_verify_module_listener');
    jrCore_register_event_listener('jrCore', 'view_results', 'jrCombinedVideo_view_results_listener');

    // Remove profile menu items
    jrCore_register_event_listener('jrProfile', 'profile_menu_items', 'jrCombinedVideo_profile_menu_items_listener');

    // Remove "Create" item index button - we have our own
    jrCore_register_event_listener('jrCore', 'exclude_item_index_buttons', 'jrCombinedVideo_exclude_item_index_buttons_listener');

    // Add in a 'combined video count' to profile db_get_item calls
    jrCore_register_event_listener('jrCore', 'db_get_item', 'jrCombinedVideo_db_get_item_listener');

    $_tmp = array(
        'title'  => 'create video button',
        'icon'   => 'plus',
        'active' => 'on',
        'group'  => 'owner'
    );
    jrCore_register_module_feature('jrCore', 'item_index_button', 'jrCombinedVideo', 'jrCombinedVideo_item_create_button', $_tmp);

    $_tmp = array(
        'title'  => 'create album button',
        'icon'   => 'star2',
        'active' => 'on',
        'group'  => 'owner'
    );
    jrCore_register_module_feature('jrCore', 'item_index_button', 'jrCombinedVideo', 'jrCombinedVideo_create_album_button', $_tmp);

    // Our javascript and CSS
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCombinedVideo', 'jrCombinedVideo.js');
    jrCore_register_module_feature('jrCore', 'css', 'jrCombinedVideo', 'jrCombinedVideo.css');

    return true;
}

//----------------------
// ITEM ORDER
//----------------------

/**
 * Get items for profile module index
 * @param $_post array Post array
 * @return mixed
 */
function jrCombinedVideo_get_order_items($_post)
{
    global $_user;
    // Get active modules
    $_tm = jrCore_get_registered_module_features('jrCombinedVideo', 'combined_support');
    if ($_tm && is_array($_tm)) {
        $_ot = array();
        $_it = array();
        foreach ($_tm as $mod => $_inf) {
            $nam = jrCore_get_module_url($mod);
            if ($pfx = jrCore_db_get_prefix($mod)) {
                $_sp = array(
                    'search'         => array(
                        "_profile_id = {$_user['user_active_profile_id']}"
                    ),
                    'return_keys'    => array('_item_id', "{$pfx}_display_order", "{$pfx}_title"),
                    'skip_triggers'  => true,
                    'ignore_pending' => true,
                    'privacy_check'  => false,
                    'limit'          => 500
                );
                $_sp = jrCore_db_search_items($mod, $_sp);
                if ($_sp && is_array($_sp) && isset($_sp['_items'])) {
                    foreach ($_sp['_items'] as $v) {
                        $iid       = "{$mod}-{$v['_item_id']}";
                        $_ot[$iid] = $v["{$pfx}_display_order"];
                        $_it[$iid] = array(
                            'data-id' => $iid,
                            'title'   => $v["{$pfx}_title"] . " ({$nam})"
                        );
                    }
                }
            }
        }
        if (count($_ot) > 0) {
            $_rt = array(
                '_items' => array()
            );
            asort($_ot, SORT_NUMERIC);
            foreach ($_ot as $k => $v) {
                $_rt['_items'][] = $_it[$k];
            }
            return $_rt;
        }
    }
    return false;
}

//----------------------
// ITEM BUTTONS
//----------------------

/**
 * Catch image URLs going to /video/image
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrCombinedVideo_module_view_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['module']) && $_data['module'] == 'jrCombinedVideo' && isset($_data['option']) && ($_data['option'] == 'image' || $_data['option'] == 'stream' || $_data['option'] == 'download')) {
        // Redirect to proper URL
        $url = jrCore_get_module_url('jrVideo');
        $url = str_replace("/{$_data['module_url']}/", "/{$url}/", jrCore_get_current_url());
        header('HTTP/1.1 301 Moved Permanently');
        jrCore_location($url);
    }
    return $_data;
}

/**
 * Return "create" button for a combined video
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return string
 */
function jrCombinedVideo_item_create_button($module, $_item, $_args, $smarty, $test_only = false)
{
    if ($module == 'jrCombinedVideo') {
        if ($test_only) {
            return true;
        }
        $_args['module'] = $module;
        return smarty_function_jrCombinedVideo_create_button($_args, $smarty);
    }
    return '';
}

/**
 * Return "create album" button for index
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrCombinedVideo_create_album_button($module, $_item, $_args, $smarty, $test_only = false)
{
    global $_conf;
    if ($module == 'jrCombinedVideo' && jrCore_module_is_active('jrVideo')) {
        if ($test_only) {
            return true;
        }
        if (jrProfile_is_profile_owner($_args['profile_id'])) {
            $url = jrCore_get_module_url('jrVideo');
            $_rt = array(
                'url'  => "{$_conf['jrCore_base_url']}/{$url}/create_album",
                'icon' => 'star2',
                'alt'  => 3
            );
            return $_rt;
        }
    }
    return false;
}

//----------------------
// EVENT LISTENERS
//----------------------

/**
 * Make sure video module URL is updated
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrCombinedVideo_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    // We take over the "video" URL
    $tbl = jrCore_db_table_name('jrCore', 'module');
    $req = "UPDATE {$tbl} SET `module_url` = 'uploaded_video' WHERE `module_directory` = 'jrVideo' AND `module_url` = 'video' LIMIT 1";
    jrCore_db_query($req);
    return $_data;
}

/**
 * Add in a 'combined video count' to profile db_get_item calls
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrCombinedVideo_db_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['quota_jrCombinedVideo_allowed']) && $_data['quota_jrCombinedVideo_allowed'] == 'on') {
        $cnt = 0;
        $_tm = jrCore_get_registered_module_features('jrCombinedVideo', 'combined_support');
        if ($_tm && is_array($_tm)) {
            foreach ($_tm as $mod => $_inf) {
                $cnt += (isset($_data["profile_{$mod}_item_count"])) ? intval($_data["profile_{$mod}_item_count"]) : 0;
            }
        }
        $_data['profile_jrCombinedVideo_item_count'] = $cnt;
    }
    return $_data;
}

/**
 * Catch "old" URLs going to /video
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrCombinedVideo_profile_template_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post, $_urls;
    if (isset($_post['option']) && isset($_urls["{$_post['option']}"]) && $_urls["{$_post['option']}"] == 'jrCombinedVideo') {
        // See if an video item is being requested
        if (isset($_post['_1']) && (jrCore_checktype($_post['_1'], 'number_nz') || $_post['_1'] == 'albums')) {
            $url = jrCore_get_module_url('jrVideo');
            header('HTTP/1.1 301 Moved Permanently');
            $add = '';
            if (isset($_post['_2']) && strlen($_post['_2']) > 0) {
                $add = "/{$_post['_2']}";
            }
            jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/{$url}/{$_post['_1']}{$add}");
        }
        // Add our CREATE button HTML to this page
        jrCore_set_flag('jrcombinedvideo_add_button_html', 1);
    }
    return $_data;
}

/**
 * Add CREATE button HTML to bottom of page
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrCombinedVideo_view_results_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_get_flag('jrcombinedvideo_add_button_html')) {
        $_data = str_replace('</body>', '<div id="create_video_dropdown" class="overlay item block_content create_video_box" style="display:none"></div></body>', $_data);
    }
    return $_data;
}

/**
 * Remove the "Create new item" core provided item index button
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrCombinedVideo_exclude_item_index_buttons_listener($_data, $_user, $_conf, $_args, $event)
{
    $_data['jrCore_item_create_button'] = true;
    return $_data;
}

/**
 * Remove Profile menu entries for video modules
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrCombinedVideo_profile_menu_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['quota_jrCombinedVideo_allowed']) && $_data['quota_jrCombinedVideo_allowed'] == 'on') {
        // Get video modules that have requested support
        $_tm = jrCore_get_registered_module_features('jrCombinedVideo', 'combined_support');
        if ($_tm && is_array($_tm)) {
            $show = false;
            foreach ($_tm as $mod => $_inf) {
                if (isset($_data['_items'][$mod])) {
                    unset($_data['_items'][$mod]);
                }
                if (isset($_data["profile_{$mod}_item_count"]) && $_data["profile_{$mod}_item_count"] > 0) {
                    $show = true;
                }
            }
            // Next - are we showing OUR video menu button?  If we have NO video items
            // then we only want to show the menu option to admins and profile owners
            if (!jrProfile_is_profile_owner($_data['_profile_id']) && !$show) {
                // We have no video items - hide button
                unset($_data['_items']['jrCombinedVideo']);
            }
        }
    }
    return $_data;
}

/**
 * Make sure our URL "video" is for us
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrCombinedVideo_parse_url_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['module']) && $_data['module'] == 'jrVideo' && isset($_data['module_url']) && $_data['module_url'] == jrCore_get_module_url('jrCombinedVideo')) {
        // The  Video Support module is using the same URL we are - change it
        $tbl = jrCore_db_table_name('jrCore', 'module');
        $req = "UPDATE {$tbl} SET `module_url` = 'uploaded_video' WHERE `module_directory` = 'jrVideo' LIMIT 1";
        jrCore_db_query($req);
        jrCore_delete_all_cache_entries();
        $_data['module']     = 'jrCombinedVideo';
        $_data['module_url'] = jrCore_get_module_url('jrCombinedVideo');
    }
    return $_data;
}

/**
 * Shows "create video" button for site owners
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrCombinedVideo_create_button($params, $smarty)
{
    global $_conf, $_user;
    if (!jrProfile_is_profile_view()) {
        return '';
    }
    // Make sure viewer has owner access to item
    if (!jrProfile_is_profile_owner($params['profile_id'])) {
        return '';
    }
    $siz = null;
    if (isset($params['size']) && jrCore_checktype($params['size'], 'number_nz') && $params['size'] < 65) {
        $siz = (int) $params['size'];
    }
    $icn = 'plus';
    if (isset($params['icon'])) {
        $icn = $params['icon'];
    }
    $_ln = jrUser_load_lang_strings();
    $alt = $_ln['jrCombinedVideo'][2];

    // If we only have ONE module, link directly to create form
    $out = '';
    $fnd = 0;
    $_tm = jrCore_get_registered_module_features('jrCombinedVideo', 'combined_support');
    if ($_tm && is_array($_tm)) {
        foreach ($_tm as $mod => $_inf) {
            if (!isset($_user["quota_{$mod}_allowed"]) || $_user["quota_{$mod}_allowed"] != 'on') {
                unset($_tm[$mod]);
            }
            else {
                $fnd++;
            }
        }
    }
    if ($fnd > 0) {
        if ($fnd === 1) {
            foreach ($_tm as $mod => $_inf) {
                foreach ($_inf as $view => $_args) {
                    $url = jrCore_get_module_url($mod);
                    $url = "{$_conf['jrCore_base_url']}/{$url}/{$view}";
                    $title = $_ln[$mod][$_args['title']];
                    $out = '<a id="create_video_button" href="' . $url . '" title="' . jrCore_entity_string($title) . '">' . jrCore_get_sprite_html($icn, $siz) . '</a>';
                    break 2;
                }
            }
        }
        else {
            $out = '<a id="create_video_button" onclick="jrCombinedVideo_create_video();" title="' . jrCore_entity_string($alt) . '">' . jrCore_get_sprite_html($icn, $siz) . '</a>';
        }
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Combined Video search form
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrCombinedVideo_search_form($params, $smarty)
{
    global $_conf;
    if (!jrCore_module_is_active('jrSearch')) {
        return '';
    }
    if (!isset($params['page']) || !jrCore_checktype($params['page'], 'number_nz')) {
        $params['page'] = 1;
    }
    if (!isset($params['pagebreak']) || !jrCore_checktype($params['pagebreak'], 'number_nz')) {
        $params['pagebreak'] = (isset($_conf['jrSearch_index_limit'])) ? intval($_conf['jrSearch_index_limit']) : 4;
    }
    $_tm = jrCore_get_registered_module_features('jrCombinedVideo', 'combined_support');
    $_md = array();
    if ($_tm && is_array($_tm)) {
        foreach ($_tm as $mod => $_inf) {
            $_md[] = $mod;
        }
    }
    $params['modules'] = (count($_md) > 0) ? implode(',', $_md) : 'all';
    $out = jrCore_parse_template('index_search.tpl', $params, 'jrCombinedVideo');
    if (isset($params['assign']) && strlen($params['assign']) > 0) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Get active video modules for Seamless
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrCombinedVideo_get_active_modules($params, $smarty)
{
    $out = '';
    $_tm = jrCore_get_registered_module_features('jrCombinedVideo', 'combined_support');
    if ($_tm && is_array($_tm)) {
        $out = implode(',', array_keys($_tm));
    }
    if (isset($params['assign']) && strlen($params['assign']) > 0) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

