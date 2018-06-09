<?php
/**
 * Jamroom SoundCloud module
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
function jrSoundCloud_meta()
{
    $_tmp = array(
        'name'        => 'SoundCloud',
        'url'         => 'soundcloud',
        'version'     => '1.2.6',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Import SoundCloud tracks into a Profile',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/290/soundcloud',
        'category'    => 'profiles',
        'requires'    => 'jrCore:6.0.4',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrSoundCloud_init()
{
    // Event listeners
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrSoundCloud_daily_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'repair_module', 'jrSoundCloud_repair_module_listener');

    // We can be added to play lists
    jrCore_register_event_listener('jrCore', 'media_playlist', 'jrSoundCloud_media_playlist_listener');

    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrSoundCloud', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrSoundCloud', 'update');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrSoundCloud', true);

    // jrSoundCloud module magic view
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrSoundCloud', 'soundcloud_player', 'view_jrSoundCloud_display_player');

    // Core support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrSoundCloud', 'off');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrSoundCloud', 'on');
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrSoundCloud', 'on');
    jrCore_register_module_feature('jrCore', 'item_order_support', 'jrSoundCloud', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrSoundCloud', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrSoundCloud', 'update', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrSoundCloud', 'search', 'item_action.tpl');

    // When an action is shared via jrOneAll, we can provide the text of the shared item
    jrCore_register_event_listener('jrOneAll', 'network_share_text', 'jrSoundCloud_network_share_text_listener');

    // add a row to the system check to make sure the API key has been set.
    jrCore_register_event_listener('jrCore', 'system_check', 'jrSoundCloud_system_check_listener');

    // We listen for the jrUrlScan 'url_found' trigger and if its a soundcloud url, add appropriate data to its array
    jrCore_register_event_listener('jrUrlScan', 'url_found', 'jrSoundCloud_url_found_listener');

    // We have fields that can be searched
    jrCore_register_module_feature('jrSearch', 'search_fields', 'jrSoundCloud', 'soundcloud_title', 60);

    // We want RSS feeds
    jrCore_register_module_feature('jrFeed', 'feed_support', 'jrSoundCloud', 'enabled');

    // Tool view
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrSoundCloud', 'integrity_check', array('Integrity Check', 'Checks the integrity of all uploaded SoundCloud tracks'));

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'jrSoundCloud', 'profile_jrSoundCloud_item_count', 60);

    // Check for SSL
    jrCore_register_event_listener('jrCore', 'db_get_item', 'jrSoundCloud_db_get_item_listener');
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrSoundCloud_db_search_items_listener');

    jrCore_register_module_feature('jrSiteBuilder', 'widget', 'jrSoundCloud', 'widget_soundcloud_player', 'SoundCloud Player');

    // We can be added to the Combined Audio module
    $_tmp = array(
        'alt'   => 12,
        'title' => 68
    );
    jrCore_register_module_feature('jrCombinedAudio', 'combined_support', 'jrSoundCloud', 'create', $_tmp);

    return true;
}

//------------------------------------
// WIDGETS
//------------------------------------

/**
 * Display CONFIG screen for selecting soundcloud track
 * @param $_post array Post info
 * @param $_user array User array
 * @param $_conf array Global Config
 * @return bool
 */
function jrSoundCloud_widget_soundcloud_player_config($_post, $_user, $_conf, $_wg)
{

    // Widget Content
    $_tmp = array(
        'name'     => 'soundcloud_id',
        'type'     => 'hidden',
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // header
    $html = jrCore_parse_template('widget_config_header.tpl', $_wg, 'jrSoundCloud');
    jrCore_page_custom($html);

    return true;
}

/**
 * Get Widget results from posted Config data
 * @param $_post array Post info
 * @return array
 */
function jrSoundCloud_widget_soundcloud_player_config_save($_post)
{
    return array('soundcloud_id' => $_post['soundcloud_id']);
}

/**
 * Soundcloud widget DISPLAY
 * @param $_widget array Page Widget info
 * @return string
 */
function jrSoundCloud_widget_soundcloud_player_display($_widget)
{
    $_widget['auto_play'] = false;
    $_widget['item_id']   = (int) $_widget['soundcloud_id'];
    $smarty               = new stdClass;
    return smarty_function_jrSoundCloud_embed($_widget, $smarty);
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Fix bad count values for items
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrSoundCloud_repair_module_listener($_data, $_user, $_conf, $_args, $event)
{
    $_sp = array(
        'search'         => array(
            'soundcloud_file_stream_count_count like %'
        ),
        'return_keys'    => array('_item_id', 'soundcloud_file_stream_count_count'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'privacy_check'  => true,
        'limit'          => 10000
    );
    $_sp = jrCore_db_search_items('jrSoundCloud', $_sp);
    if ($_sp && is_array($_sp) && isset($_sp['_items'])) {
        foreach ($_sp as $k => $_item) {
            jrCore_db_increment_key('jrSoundCloud', $_item['_item_id'], 'soundcloud_stream_count', $_item['soundcloud_file_stream_count_count']);
        }
        jrCore_db_delete_key_from_all_items('jrSoundCloud', 'soundcloud_file_stream_count_count');
        jrCore_logger('INF', 'fixed ' . count($_sp['_items']) . ' invalid soundcloud stream count values');
    }
    return $_data;
}

/**
 * Daily maintenance
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrSoundCloud_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_checktype($_conf['jrSoundCloud_daily_maintenance'], 'number_nz')) {
        // Get maintenance counter
        $tmp = jrCore_get_temp_value('jrSoundCloud', 'maintenance_count');
        if (!$tmp || !jrCore_checktype($tmp, 'number_nn')) {
            jrCore_set_temp_value('jrSoundCloud', 'maintenance_count', 0);
            $tmp = 0;
        }
        // Get soundclouds to check
        $iid = 0;
        $num = (isset($_conf['jrSoundCloud_daily_maintenance']) && jrCore_checktype($_conf['jrSoundCloud_daily_maintenance'], 'number_nz')) ? (int) $_conf['jrSoundCloud_daily_maintenance'] : 100;
        $_sp = array(
            "search"         => array(
                "_item_id > {$tmp}"
            ),
            "order_by"       => array(
                "_item_id" => "numerical_asc"
            ),
            'privacy_check'  => false,
            'ignore_pending' => true,
            'limit'          => $num
        );
        $_rt = jrCore_db_search_items('jrSoundCloud', $_sp);
        if ($_rt && is_array($_rt['_items']) && isset($_rt['_items'][0]) && is_array($_rt['_items'][0])) {
            // We have some checking to do
            $ctr = 0;
            $del = 0;
            foreach ($_rt['_items'] as $rt) {
                $_xt = jrSoundCloud_get_data($rt['soundcloud_id']);
                if (!isset($_xt) || !is_array($_xt)) {
                    // Not looking good for this item
                    jrCore_db_delete_item('jrSoundCloud', $rt['_item_id']);
                    jrCore_logger('MAJ', "Removed invalid SoundCloud item - '{$rt['soundcloud_title']}' owned by '{$rt['profile_name']}'");
                    $del++;
                }
                $iid = $rt['_item_id'];
                $ctr++;
            }
            // Log the counts
            jrCore_logger('INF', "jrSoundCloud daily maintenance - {$ctr} items checked, {$del} deleted");

            // Save where we are up to for next time
            if (count($_rt['_items']) < $_conf['jrSoundCloud_daily_maintenance']) {
                // Start over
                $iid = 0;
            }
        }
        jrCore_update_temp_value('jrSoundCloud', 'maintenance_count', $iid);
    }
    return $_data;
}

/**
 * Convert non-SSL to SSL URLs if needed
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrSoundCloud_db_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_is_view_request() && $_args['module'] == 'jrSoundCloud' && jrCore_get_server_protocol() == 'https') {
        // Make sure the artwork url is over SSL
        if (isset($_data['soundcloud_artwork_url']{1}) && strpos($_data['soundcloud_artwork_url'], 'http://') === 0) {
            $_data['soundcloud_artwork_url'] = str_replace('http://', 'https://', $_data['soundcloud_artwork_url']);
        }
    }
    return $_data;
}

/**
 * Convert non-SSL to SSL URLs if needed
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrSoundCloud_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_is_view_request() && $_args['module'] == 'jrSoundCloud' && jrCore_get_server_protocol() == 'https' && is_array($_data['_items'])) {
        // Make sure the artwork url is over SSL
        foreach ($_data['_items'] as $k => $v) {
            if (isset($v['soundcloud_artwork_url']{1}) && strpos($v['soundcloud_artwork_url'], 'http://') === 0) {
                $_data['_items'][$k]['soundcloud_artwork_url'] = str_replace('http://', 'https://', $v['soundcloud_artwork_url']);
            }
        }
    }
    return $_data;
}

/**
 * Add in player code to the jrUrlScan array
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrSoundCloud_url_found_listener($_data, $_user, $_conf, $_args, $event)
{
    $murl = jrCore_get_module_url('jrSoundCloud');
    $uurl = jrCore_get_module_url('jrUrlScan');
    // Is it a local soundcloud url
    if (strpos($_args['url'], $_conf['jrCore_base_url']) === 0) {
        $_x = explode('/', substr($_args['url'], strlen($_conf['jrCore_base_url']) + 1));
        if ($_x && is_array($_x) && isset($_x[1]) && $_x[1] == $murl && jrCore_checktype($_x[2], 'number_nz')) {
            $title = jrCore_db_get_item_key('jrSoundCloud', $_x[2], 'soundcloud_title');
            if ($title != '') {
                $_data['_items'][$_args['i']]['title']    = $title;
                $_data['_items'][$_args['i']]['load_url'] = "{$_conf['jrCore_base_url']}/{$uurl}/parse/urlscan_player/{$_x[2]}/0/jrSoundCloud/__ajax=1";
                $_data['_items'][$_args['i']]['url']      = $_args['url'];
            }
        }
    }
    // Is it a SoundCloud URL?
    elseif (stristr($_args['url'], 'soundcloud')) {
        if ($soundcloud_id = jrSoundCloud_extract_id($_args['url'])) {
            if ($_soundcloud_data = jrSoundCloud_get_data($soundcloud_id)) {
                // Yep - Its a good soundcloud
                $_data['_items'][$_args['i']]['title']    = $_soundcloud_data['title'];
                $_data['_items'][$_args['i']]['load_url'] = "{$_conf['jrCore_base_url']}/{$uurl}/parse/urlscan_player/0/{$soundcloud_id}/jrSoundCloud/__ajax=1";
                $_data['_items'][$_args['i']]['url']      = $_args['url'];
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
 * @return array
 */
function jrSoundCloud_network_share_text_listener($_data, $_user, $_conf, $_args, $event)
{
    // $_data:
    // [providers] => twitter
    // [user_token] => c6418e9a-b66e-4c6c-xxxx-cdea7e915d03
    // [user_id] => 1
    // [action_module] => jrSoundCloud
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
    $url = jrCore_get_module_url('jrSoundCloud');
    $txt = $_ln['jrSoundCloud'][36];
    if ($_data['action_mode'] == 'update') {
        $txt = $_ln['jrSoundCloud'][46];
    }
    $_out = array(
        'text' => "{$_conf['jrCore_base_url']}/{$_data['profile_url']} {$_data['profile_name']} {$txt}: \"{$_data['soundcloud_title']}\" {$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['_item_id']}/{$_data['soundcloud_title_url']}",
        'link' => array(
            'url'  => "{$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['_item_id']}/{$_data['soundcloud_title_url']}",
            'name' => $_data['soundcloud_title']
        )
    );
    // See if they included a picture with the song
    if (isset($_data['soundcloud_image_size']) && jrCore_checktype($_data['soundcloud_image_size'], 'number_nz')) {
        $_out['picture'] = array(
            'url' => "{$_conf['jrCore_base_url']}/{$url}/image/soundcloud_image/{$_data['_item_id']}/large"
        );
    }
    return $_out;
}

//---------------------------------------------------------
// FUNCTIONS
//---------------------------------------------------------

/**
 * Extract a SoundCloud ID from a string
 * @param $string String (i.e. URL)
 * @return bool|int|string
 */
function jrSoundCloud_extract_id($string)
{
    global $_conf;
    $sid = 0;
    if (is_numeric($string) && strlen($string) >= 6 && strlen($string) <= 9) {
        return $string;
    }
    elseif (jrCore_checktype($string, 'url')) {

        $url = "http://api.soundcloud.com/resolve.json?client_id={$_conf['jrSoundCloud_client_id']}&url={$string}";
        $res = jrCore_load_url($url);
        // {"status":"302 - Found","location":"http://api.soundcloud.com/tracks/140162408.json?client_id=409edf537be4adcb29710e61b360e2d1"}
        if (!$res || strlen($res) < 10) {
            return false;
        }
        $_sc = json_decode($res, true);
        if (isset($_sc['id']) && is_numeric($_sc['id']) && strlen($_sc['id']) >= 6 && strlen($_sc['id']) <= 9) {
            $sid = $_sc['id'];
        }
        elseif (isset($_sc['location']{1})) {
            $_tm = parse_url($_sc['location']);
            $_tm = explode('/', $_tm['path']);
            $_tm = end($_tm);
            $sid = (int) str_replace('.json', '', $_tm);
        }
    }
    elseif ($x0 = strpos($string, 'api.soundcloud.com/tracks/')) {
        if ($x1 = strpos(substr($string, $x0), '"')) {
            $sid = substr($string, $x0 + 26, $x1 - 26);
        }
    }
    else {
        $x1 = strpos($string, 'api.soundcloud.com%2Ftracks%2F') + 30;
        if ($x1) {
            $scid1 = substr($string, $x1, 6);
            $scid2 = substr($string, $x1, 7);
            $scid3 = substr($string, $x1, 8);
            $scid4 = substr($string, $x1, 9);
            if (is_numeric($scid4)) {
                $sid = $scid4;
            }
            elseif (is_numeric($scid3)) {
                $sid = $scid3;
            }
            elseif (is_numeric($scid2)) {
                $sid = $scid2;
            }
            elseif (is_numeric($scid1)) {
                $sid = $scid1;
            }
        }
    }
    if (jrCore_checktype($sid, 'number_nz')) {
        return $sid;
    }
    return false;
}

/**
 * get SoundCloud track data
 */
function jrSoundCloud_get_data($soundcloud_id)
{
    global $_conf;
    $url = "http://api.soundcloud.com/tracks/{$soundcloud_id}.json?client_id={$_conf['jrSoundCloud_client_id']}";
    $res = jrCore_load_url($url);
    if (!$res || strlen($res) < 50) {
        $res = @file_get_contents($url);
        if (!$res || strlen($res) < 50) {
            return false;
        }
    }
    $_sc = json_decode($res, true);
    if ($_sc && is_array($_sc)) {
        if (isset($_sc['id']) && is_numeric($_sc['id']) && strlen($_sc['id']) >= 6 && strlen($_sc['id']) <= 9) {
            return $_sc;
        }
    }
    return false;
}

/**
 * Get an embeddable SoundCloud player
 * @param $id int DataStore ID
 * @param bool $auto_play bool true/false for auto play
 * @param bool $show_artwork bool true/false show artwork
 * @param string $width string width of embedded frame
 * @param int $height height of embedded frame
 * @return bool|string
 */
function jrSoundCloud_get_player($id, $auto_play = false, $show_artwork = true, $width = '100%', $height = 166)
{
    if (substr($id, 0, 2) == 'sc') {
        $_rt = array();
        $sid = (int) substr($id, 2);
    }
    else {
        $_rt = jrCore_db_get_item('jrSoundCloud', $id);
        if (!$_rt || !is_array($_rt)) {
            return false;
        }
        $sid = (int) $_rt['soundcloud_id'];
    }
    if (!jrCore_checktype($sid, 'number_nz')) {
        return false;
    }
    $_rt['width']        = $width;
    $_rt['height']       = $height;
    $_rt['auto_play']    = $auto_play;
    $_rt['show_artwork'] = $show_artwork;
    return jrCore_parse_template('soundcloud_embed.tpl', $_rt, 'jrSoundCloud');
}

/**
 * Sets up media stream URLs for play lists
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrSoundCloud_media_playlist_listener($_data, $_user, $_conf, $_args, $event)
{
    foreach ($_data as $k => $_item) {
        if (isset($_item['module']) && $_item['module'] == 'jrSoundCloud') {
            $_data[$k]['media_playlist_url'] = "https://api.soundcloud.com/tracks/{$_item['soundcloud_id']}/stream?client_id={$_conf['jrSoundCloud_client_id']}";
            $_data[$k]['media_playlist_ext'] = 'mp3';
            $_data[$k]['media_playlist_img'] = $_item['soundcloud_artwork_url'];
        }
    }
    return $_data;
}

/**
 * Embed a SoundCloud track player
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrSoundCloud_embed($params, $smarty)
{
    /**
     * In: item_id: required
     * In: width: optional - default 100%
     * In: height: optional - default 166
     * In: autoplay: optional - default FALSE
     * In: show_artwork: optional - default TRUE
     * In: assign: optional
     * Out: embed code
     */
    if (!jrCore_checktype($params['item_id'], 'number_nz')) {
        return 'jrSoundCloud_embed: item_id parameter required';
    }
    $_rt = jrCore_db_get_item('jrSoundCloud', $params['item_id']);
    if (!$_rt || !is_array($_rt)) {
        return '';
    }
    if (!isset($params['width'])) {
        $params['width'] = '100%';
    }
    $_rt['width'] = $params['width'];
    if (!isset($params['height']) || !jrCore_checktype($params['height'], 'number_nz')) {
        $params['height'] = 166;
    }
    $_rt['height'] = $params['height'];
    if (!isset($params['auto_play'])) {
        $params['auto_play'] = 'false';
    }
    if ($params['auto_play'] == 'on') {
        $params['auto_play'] = 'true';
    }
    elseif ($params['auto_play'] == 'off') {
        $params['auto_play'] = 'false';
    }
    $_rt['auto_play'] = $params['auto_play'];
    if (!isset($params['show_artwork'])) {
        $params['show_artwork'] = 'TRUE';
    }
    $_rt['show_artwork'] = strtolower($params['show_artwork']);

    // get player
    $out = jrCore_parse_template('soundcloud_embed.tpl', $_rt, 'jrSoundCloud');
    if ($out{2}) {
        // Increment stream counter
        jrCore_counter('jrSoundCloud', $params['item_id'], 'soundcloud_stream');
        if (isset($params['assign']) && $params['assign'] != '') {
            $smarty->assign($params['assign'], $out);
            return '';
        }
    }
    return $out;
}

/**
 * Embed a SoundCloud button player
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrSoundCloud_player($params, $smarty)
{
    global $_conf;
    // Check the incoming parameters
    if (isset($params['template']) && strlen($params['template']) > 0) {
        $params['tpl_dir'] = $_conf['jrCore_active_skin'];
    }
    else {
        $params['template'] = "jrSoundCloud_button.tpl";
        $params['tpl_dir']  = 'jrSoundCloud';
    }
    $_tmp = array();
    foreach ($params as $k => $v) {
        $_tmp['jrSoundCloud'][$k] = $v;
    }
    // Check for image
    $_tmp['image'] = "{$_conf['jrCore_base_url']}/modules/jrSoundCloud/img/button_player";
    if (isset($params['image']) && strlen($params['image']) > 0) {
        $_tmp['image'] = "{$_conf['jrCore_base_url']}/skins/{$_conf['jrCore_active_skin']}/img/{$params['image']}";
    }
    // Call the appropriate template and return
    $out = jrCore_parse_template($params['template'], $_tmp, $params['tpl_dir']);
    if (isset($params['assign']) && $params['assign'] != '') {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Add some items to the System Check
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return bool
 */
function jrSoundCloud_system_check_listener($_data, $_user, $_conf, $_args, $event)
{
    $dat             = array();
    $dat[1]['title'] = 'SoundCloud';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = 'API Settings';
    $dat[2]['class'] = 'center';
    $murl = jrCore_get_module_url('jrSoundCloud');
    if (!isset($_conf['jrSoundCloud_client_id']) || strlen($_conf['jrSoundCloud_client_id']) < 2) {
        $dat[3]['title'] = $_args['fail'];
        $dat[4]['title'] = "Client ID must be set, <a href='{$_conf['jrCore_base_url']}/{$murl}/admin/global/hl[]=client_id/hl[]=client_secret' style='text-decoration: underline' target='_blank'>click here</a>";
    }
    elseif (!isset($_conf['jrSoundCloud_client_secret']) || strlen($_conf['jrSoundCloud_client_secret']) < 2) {
        $dat[3]['title'] = $_args['fail'];
        $dat[4]['title'] = "Client Secret must be set, <a href='{$_conf['jrCore_base_url']}/{$murl}/admin/global/hl[]=client_id/hl[]=client_secret' style='text-decoration: underline' target='_blank'>click here</a>";
    }
    else {
        $dat[3]['title'] = $_args['pass'];
        $dat[4]['title'] = 'SoundCloud API Settings are configured';
    }
    $dat[3]['class'] = 'center';
    jrCore_page_table_row($dat);
    return true;
}

/**
 * Return readable seconds from soundcloud microseconds
 * @param $duration
 * @return string
 */
function jrSoundCloud_duration_to_readable($duration)
{
    $duration = floor($duration / 1000);
    $hours    = floor($duration / 3600);
    if (strlen($hours) == 1) {
        $hours = '0' . $hours;
    }
    $minutes = floor(($duration - ($hours * 3600)) / 60);
    if (strlen($minutes) == 1) {
        $minutes = '0' . $minutes;
    }
    $seconds = floor(($duration - ($hours * 3600) - ($minutes * 60)));
    if (strlen($seconds) == 1) {
        $seconds = '0' . $seconds;
    }
    return $hours . ':' . $minutes . ':' . $seconds;
}
