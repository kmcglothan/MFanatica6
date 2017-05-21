<?php
/**
 * Jamroom Playlists module
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
function jrPlaylist_meta()
{
    $_tmp = array(
        'name'        => 'Playlists',
        'url'         => 'playlist',
        'version'     => '1.1.4',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Users can create playlists from Audio, Video and SoundCloud media (with modules installed)',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/285/playlists',
        'category'    => 'users',
        'priority'    => 50,
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrPlaylist_init()
{
    // Expand playlist entries
    jrCore_register_event_listener('jrCore', 'db_get_item', 'jrPlaylist_db_get_item_listener');

    // We listen for the jrUrlScan 'url_found' trigger and if its a playlist url, add appropriate data to its array
    jrCore_register_event_listener('jrUrlScan', 'url_found', 'jrPlaylist_url_found_listener');

    // Convert play lists for non-logged in users on login...
    jrCore_register_event_listener('jrUser', 'login_success', 'jrPlaylist_login_success_listener');

    // Provide playlist for media player calls
    jrCore_register_event_listener('jrCore', 'media_playlist', 'jrPlaylist_media_playlist_listener');

    // Core media player support
    jrCore_register_module_feature('jrCore', 'media_player', 'jrPlaylist', 'jrPlaylist_blue_monday', 'mixed');
    jrCore_register_module_feature('jrCore', 'media_player', 'jrPlaylist', 'jrPlaylist_player_dark', 'mixed');
    jrCore_register_module_feature('jrCore', 'media_player', 'jrPlaylist', 'jrPlaylist_gray_overlay_player', 'mixed');
    jrCore_register_module_feature('jrCore', 'media_player', 'jrPlaylist', 'jrPlaylist_black_overlay_player', 'mixed');
    jrCore_register_module_feature('jrCore', 'media_player', 'jrPlaylist', 'jrPlaylist_solo_player', 'mixed');

    // Custom JS and CSS
    jrCore_register_module_feature('jrCore', 'javascript', 'jrPlaylist', 'jrPlaylist.js');
    jrCore_register_module_feature('jrCore', 'css', 'jrPlaylist', 'jrPlaylist.css');

    // Form Designer
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrPlaylist', 'update');

    // Core features
    $_tmp = array(
        'label' => 'Show Playlists',
        'help'  => 'If checked, Playlists created by Users will appear on their profile'
    );
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrPlaylist', 'on', $_tmp);
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrPlaylist', true);
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrPlaylist', true);
    jrCore_register_module_feature('jrCore', 'item_order_support', 'jrPlaylist', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrPlaylist', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrPlaylist', 'update', 'item_action.tpl');

    // When an action is shared via jrOneAll, we can provide the text of the shared item
    jrCore_register_event_listener('jrOneAll', 'network_share_text', 'jrPlaylist_network_share_text_listener');

    // We have fields that can be searched
    jrCore_register_module_feature('jrSearch', 'search_fields', 'jrPlaylist', 'playlist_title', 21);

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'jrPlaylist', 'profile_jrPlaylist_item_count', 21);

    // Core item buttons
    $_tmp = array(
        'title'  => 'add to playlist button',
        'icon'   => 'music',
        'active' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'item_list_button', 'jrPlaylist', 'jrPlaylist_item_playlist_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrPlaylist', 'jrPlaylist_item_playlist_button', $_tmp);

    // We don't want the core provided "create" button on our item_detail.tpl - we handle it ourselves
    jrCore_register_event_listener('jrCore', 'exclude_item_index_buttons', 'jrPlaylist_exclude_item_index_buttons_listener');

    // Site Builder widget
    // jrCore_register_module_feature('jrSiteBuilder', 'widget', 'jrPlaylist', 'widget_playlist', 'Featured Playlist');

    return true;
}

//---------------------------------------------------------
// ITEM BUTTONS
//---------------------------------------------------------

/**
 * Return "playlist" button for the item
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrPlaylist_item_playlist_button($module, $_item, $_args, $smarty, $test_only = false)
{
    // See if the requesting module supports play lists
    if (!is_file(APP_DIR . "/modules/{$module}/templates/item_playlist.tpl")) {
        return false;
    }
    if ($test_only) {
        return true;
    }
    $_rp = array(
        'playlist_for' => $module,
        'item_id'      => $_item['_item_id']
    );
    return smarty_function_jrPlaylist_button($_rp, $smarty);
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Add in player code to the jrUrlScan array
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrPlaylist_url_found_listener($_data, $_user, $_conf, $_args, $event)
{
    // Is it a local playlist url
    $uurl = jrCore_get_module_url('jrUrlScan');
    if (strpos($_args['url'], $_conf['jrCore_base_url']) === 0) {
        $_x = explode('/', substr($_args['url'], strlen($_conf['jrCore_base_url']) + 1));
        if ($_x && is_array($_x) && isset($_x[1]) && $_x[1] == jrCore_get_module_url('jrPlaylist') && jrCore_checktype($_x[2], 'number_nz')) {
            $title = jrCore_db_get_item_key('jrPlaylist', $_x[2], 'playlist_title');
            if ($title != '') {
                $_data['_items'][$_args['i']]['title']    = $title;
                $_data['_items'][$_args['i']]['load_url'] = "{$_conf['jrCore_base_url']}/{$uurl}/parse/urlscan_player/{$_x[2]}/0/jrPlaylist/__ajax=1";
                $_data['_items'][$_args['i']]['url']      = $_args['url'];
            }
        }
    }
    return $_data;
}

/**
 * Exclude core provided "create" item button from item_detail.tpl
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrPlaylist_exclude_item_index_buttons_listener($_data, $_user, $_conf, $_args, $event)
{
    // Exclude core delete button...
    $_data['jrCore_item_create_button'] = true;
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
function jrPlaylist_network_share_text_listener($_data, $_user, $_conf, $_args, $event)
{
    // $_data:
    // [providers] => twitter
    // [user_token] => c6418e9a-b66e-4c6c-xxxx-cdea7e915d03
    // [user_id] => 1
    // [action_module] => jrPlaylist
    // [action_data] => (JSON array of data for item initiating action)
    $_data = json_decode($_data['action_data'], true);
    if (!isset($_data) || !is_array($_data)) {
        return $_data;
    }
    $_ln = jrUser_load_lang_strings($_data['user_language']);

    // We return an array:
    // 'text' => text to post (i.e. "tweet")
    // 'url'  => URL to media item,
    // 'name' => name if media item
    $url = jrCore_get_module_url('jrPlaylist');
    $txt = $_ln['jrPlaylist'][1];
    if ($_data['action_mode'] == 'update') {
        $txt = $_ln['jrPlaylist'][20];
    }
    $_out = array(
        'text' => "{$_conf['jrCore_base_url']}/{$_data['profile_url']} {$_data['profile_name']} {$txt}: \"{$_data['playlist_title']}\" {$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['_item_id']}/{$_data['playlist_title_url']}",
        'link' => array(
            'url'  => "{$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['_item_id']}/{$_data['playlist_title_url']}",
            'name' => $_data['playlist_title']
        )
    );
    return $_out;
}

/**
 * Return the items in playlist for a media player
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrPlaylist_media_playlist_listener($_data, $_user, $_conf, $_args, $event)
{
    // We only care about jrPlaylist calls
    if ($_args['module'] != 'jrPlaylist') {
        return $_data;
    }

    // Note that "playlist_items" is created via the db_get_items listener
    // down below and will be expanded for us in this listener.
    if (isset($_data[0]['playlist_items']) && is_array($_data[0]['playlist_items'])) {
        foreach ($_data[0]['playlist_items'] as $k => $_item) {
            $_data[0]['playlist_items'][$k]['module'] = $_item['playlist_module'];
        }
        return $_data[0]['playlist_items'];
    }
    else {
        $_out = array();
        // We need to get our playlist items and return them
        $_tmp = json_decode($_data['playlist_list'], true);
        if (isset($_tmp) && is_array($_tmp)) {
            foreach ($_tmp as $mod => $_items) {
                $_sp = array(
                    'search'                       => array(
                        "_item_id in " . implode(',', array_keys($_items))
                    ),
                    'exclude_jrProfile_quota_keys' => true,
                    'ignore_pending'               => true,
                    'limit'                        => 100
                );
                $_rt = jrCore_db_search_items($mod, $_sp);
                if (isset($_rt['_items']) && is_array($_rt['_items'])) {
                    foreach ($_rt['_items'] as $_item) {
                        $_item['module'] = $mod;
                        $_out[]          = $_item;
                    }
                }
            }
            if (isset($_out) && is_array($_out) && count($_out) > 0) {
                return $_out;
            }
        }
    }
    return $_data;
}

/**
 * Convert temp playlists to "real" playlists on user login
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrPlaylist_login_success_listener($_data, $_user, $_conf, $_args, $event)
{
    // When a user logs in, we save any playlists they created when they weren't logged in
    $_tmp = jrCore_get_cookie('playlist');
    if ($_tmp) {
        // We have a playlist from the user when they were not logged in
        foreach ($_tmp as $_playlist) {
            unset($_playlist['_item_id']);
            $_cr = array(
                '_profile_id' => $_user['_profile_id']
            );
            jrCore_db_create_item('jrPlaylist', $_playlist, $_cr);
        }
        jrCore_delete_cookie('playlist');
    }
    return $_data;
}

/**
 * Expands the playlist array out to a full playlist items
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 *
 * @return array
 */
function jrPlaylist_db_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_conf;
    if (jrCore_is_view_request() && isset($_args['module']) && $_args['module'] == 'jrPlaylist' && isset($_data['playlist_list']) && strlen($_data['playlist_list']) > 0) {
        $_tp  = array();
        $_pl  = array();
        $list = json_decode($_data['playlist_list'], true);
        if (isset($list) && is_array($list)) {
            // Get all items for each module in 1 shot
            // Our entries are stored like:
            // module => array(
            // 1 => 0,
            // 5 => 1,
            // 7 => 2
            // i.e. item_id => playlist_order
            $num = 0;
            $upd = false;
            foreach ($list as $module => $items) {

                // See if this module provides a playlist template
                if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$module}_item_playlist.tpl")) {
                    $_tp[$module] = APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$module}_item_playlist.tpl";
                }
                elseif (is_file(APP_DIR . "/modules/{$module}/templates/item_playlist.tpl")) {
                    $_tp[$module] = APP_DIR . "/modules/{$module}/templates/item_playlist.tpl";
                }
                else {
                    // No template - skip
                    continue;
                }

                // Get info about these items for our template
                $_sp = array(
                    'search'                       => array(
                        "_item_id in " . implode(',', array_keys($items))
                    ),
                    'ignore_pending'               => true,
                    'quota_check'                  => false,
                    'limit'                        => count($items)
                );
                $_rt = jrCore_db_search_items($module, $_sp);
                if (isset($_rt) && is_array($_rt) && isset($_rt['_items']) && is_array($_rt['_items'])) {
                    // Place each entry in it's proper output order
                    $_fnd = array();
                    foreach ($_rt['_items'] as $n => $_item) {
                        $ord                      = $items["{$_item['_item_id']}"];
                        $_item['playlist_module'] = $module;
                        $_item['module']          = $module;
                        if (!isset($_pl[$ord])) {
                            $_pl[$ord] = $_item;
                        }
                        else {
                            // Looks like we have items without an order set
                            $ord       = (1000 + $n);
                            $_pl[$ord] = $_item;
                        }
                        $num++;
                        $_fnd[$_item['_item_id']] = $ord;
                    }
                    if (count($_fnd) != count($items)) {
                        // Some items have been removed - cleanup
                        $list[$module] = $_fnd;
                        $upd           = true;
                    }
                }
                else {
                    // No items - ba entries, remove
                    unset($list[$module]);
                    $upd = true;
                }
            }
            if ($upd) {
                if (count($list) > 0) {
                    $_dt = array(
                        'playlist_list' => json_encode($list)
                    );
                    jrCore_db_update_item('jrPlaylist', $_data['_item_id'], $_dt);
                }
                else {
                    jrCore_db_delete_item('jrPlaylist', $_data['_item_id']);
                    jrCore_delete_all_cache_entries('jrPlaylist', $_user['_user_id']);
                    jrCore_delete_all_cache_entries('jrProfile', $_user['_user_id']);
                    $_data['playlist_count'] = 0;
                    return $_data;
                }
            }
        }
        if (isset($_pl) && is_array($_pl) && count($_pl) > 0) {
            ksort($_pl, SORT_NUMERIC);
            $_data['playlist_items']     = $_pl;
            $_data['playlist_templates'] = $_tp;
            if (isset($num) && $num != $_data['playlist_count']) {
                // We've had a change in our item count - update
                $_dt = array(
                    'playlist_count' => $num
                );
                jrCore_db_update_item('jrPlaylist', $_data['_item_id'], $_dt);
            }
        }
    }
    return $_data;
}

//---------------------------------------------------------
// SMARTY FUNCTIONS
//---------------------------------------------------------

/**
 * shows an add to playlist button on audio files for logged in users.
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrPlaylist_button($params, $smarty)
{
    global $_conf;
    // check to see if this user is allowed to add stuff to playlists
    $key = jrUser_get_profile_home_key('quota_jrPlaylist_allowed');
    if ($_conf['jrPlaylist_require_login'] == 'on') {
        if (!$key || $key != 'on') {
            return '';
        }
    }

    $item_id = (int) $params['item_id'];
    if (isset($item_id) && $item_id > 0) {

        $_lang                = jrUser_load_lang_strings();
        $_rep                 = array();
        $_rep['playlist_for'] = $params['playlist_for']; //jrSoundCloud jrAudio etc.
        $_rep['item_id']      = $item_id;
        $_rep['uniqid']       = 'a' . uniqid();
        $_rep['width']        = (isset($params['width']) && is_numeric($params['width'])) ? (int) $params['width'] : 32;
        $_rep['height']       = (isset($params['height']) && is_numeric($params['height'])) ? (int) $params['height'] : 32;
        $_rep['alt']          = (isset($params['alt'])) ? $params['alt'] : $_lang['jrPlaylist'][2];
        $_rep['title']        = $_rep['alt'];
        $_rep['class']        = (isset($params['class'])) ? $params['class'] : 'create_img';

        if (isset($params['image']{0})) {
            $src               = "{$_conf['jrCore_base_url']}/skins/{$_conf['jrCore_active_skin']}/img/{$params['image']}";
            $_rep['icon_html'] = '<img src="' . $src . '" class="' . $_rep['class'] . '" alt="' . $_rep['alt'] . '" title="' . $_rep['alt'] . '" onclick="jrPlaylist_select(\'' . intval($item_id) . '\',\'' . $params['playlist_for'] . '\',null)">';
        }
        else {
            if (!isset($params['icon'])) {
                $params['icon'] = 'music';
            }
            $_rep['icon_html'] = "<a onclick=\"jrPlaylist_select('" . intval($item_id) . "','" . $params['playlist_for'] . "',null)\" title=\"{$_rep['alt']}\">" . jrCore_get_sprite_html($params['icon']) . '</a>';
        }

        $out = jrCore_parse_template("playlist_button.tpl", $_rep, 'jrPlaylist');
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], $out);
            return '';
        }
        return $out;
    }
    return '';
}

/**
 * shows remove from playlist button on audio files for logged in users.
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrPlaylist_remove_button($params, $smarty)
{
    global $_mods, $_conf, $_post;
    if (!isset($params['id']) || strlen($params['id']) === 0) {
        return 'jrPlaylist_remove_button: dom id required';
    }
    if (!isset($params['module']) || !isset($_mods["{$params['module']}"])) {
        return 'jrPlaylist_remove_button: module required';
    }
    if (!isset($params['item_id']) || !jrCore_checktype($params['item_id'], 'number_nz')) {
        return 'jrPlaylist_remove_button: item_id required';
    }
    if (jrUser_is_logged_in()) {
        if (!isset($params['playlist_id']) || !jrCore_checktype($params['playlist_id'], 'number_nz')) {
            return 'jrPlaylist_remove_button: playlist_id required';
        }
        $pid = (int) $params['playlist_id'];
    }
    else {
        if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
            return 'jrPlaylist_remove_button: playlist_id required';
        }
        $pid = trim($_post['_1']);
    }
    $_lang = jrUser_load_lang_strings();
    $alt   = addslashes($_lang['jrPlaylist'][3]);
    if (isset($params['confirm']) && strlen($params['confirm']) > 0) {
        $onc = "if(confirm('" . addslashes($_lang['jrPlaylist'][4]) . "')){jrPlaylist_remove('{$params['id']}','{$pid}','{$params['module']}',{$params['item_id']});}";
    }
    else {
        $onc = "jrPlaylist_remove('{$params['id']}','{$pid}','{$params['module']}',{$params['item_id']});";
    }

    if (isset($params['image']{0})) {
        // Check for custom button image
        $src = "{$_conf['jrCore_base_url']}/skins/{$_conf['jrCore_active_skin']}/img/{$params['image']}";
        $out = "<img src=\"{$src}\" alt=\"{$alt}\" title=\"{$alt}\" style=\"cursor:pointer\" onclick=\"" . $onc . "\">";
    }
    else {
        if (!isset($params['icon'])) {
            $params['icon'] = 'close';
        }
        $out = "<a onclick=\"{$onc}\" title=\"{$alt}\">" . jrCore_get_sprite_html($params['icon']) . '</a>';
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * {jrPlaylist_util}
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrPlaylist_util($params, $smarty)
{
    global $_user;
    switch ($params['mode']) {
        case 'get_my_playlists':
            $_sp   = array(
                'search'   => array(
                    "_profile_id = {$_user['user_active_profile_id']}",
                ),
                "order_by" => array(
                    '_item_id' => 'DESC'
                ),
                "limit"    => 100
            );
            $_rt   = jrCore_db_search_items('jrPlaylist', $_sp);
            $items = $_rt['_items'];
            if (!empty($params['assign'])) {
                $smarty->assign($params['assign'], $items);
                return '';
            }
            break;
        /*
        * embed_playlist takes the playlist id and shows the embed_playlist.tpl
        * {jrPlaylist_util mode="embed_playlist" playlist_id="1"} or {jrPlaylist_util mode="embed_playlist" playlist_id="1" template="something.tpl"}
        */
        case 'embed_playlist':
            $_rt         = array();
            $_item_id    = (int) $params['playlist_id'];
            $_rt['item'] = jrCore_db_get_item('jrPlaylist', $_item_id);
            if (isset($params['template'])) {
                return jrCore_parse_template($params['template'], $_rt);
            }
            else {
                return jrCore_parse_template('item_embed.tpl', $_rt, 'jrPlaylist');
            }
            break;
    }
    return '';
}

/**
 * needs to be used in a couple of locations, so built a function.
 * @param $playlist_id
 * @param $mod
 * @param $item_id
 */
function _inject_into_playlist($playlist_id, $mod, $item_id)
{
    //add this song to this playlist
    $_rt           = jrCore_db_get_item('jrPlaylist', $playlist_id);
    $playlist_list = json_decode($_rt['playlist_list'], true);
    $cnt           = 0;
    if (!isset($playlist_list[$mod][$item_id])) {
        // New entry into playlist
        $sum = 0;
        foreach ($playlist_list as $entries) {
            $sum += count($entries);
        }
        if (!isset($playlist_list[$mod])) {
            $playlist_list[$mod] = array();
        }
        $playlist_list[$mod][$item_id] = $sum;
        $cnt                           = 1;
    }
    //create the new playlist
    $_sv                       = array();
    $_sv['playlist_title']     = $_rt['playlist_title'];
    $_sv['playlist_title_url'] = jrCore_url_string($_rt['playlist_title']);
    $_sv['playlist_list']      = json_encode($playlist_list);
    $_sv['playlist_count']     = (int) ($_rt['playlist_count'] + $cnt);
    jrCore_db_update_item('jrPlaylist', $playlist_id, $_sv);
}
