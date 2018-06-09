<?php
/**
 * Jamroom Photo Albums module
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
function jrPhotoAlbum_meta()
{
    $_tmp = array(
        'name'        => 'Photo Albums',
        'url'         => 'photoalbum',
        'version'     => '1.0.18',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Create photo albums from images in the Image Galleries or Flickr modules',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2928/photo-albums',
        'category'    => 'users',
        'priority'    => 50,
        'license'     => 'jcl',
        'requires'    => 'jrGallery'
    );
    return $_tmp;
}

/**
 * init
 */
function jrPhotoAlbum_init()
{
    // Core features
    $_tmp = array(
        'label' => 'Show Photo Albums',
        'help'  => 'If checked, Photo Albums created by Users will appear on their profile'
    );
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrPhotoAlbum', 'on', $_tmp);
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrPhotoAlbum', true);
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrPhotoAlbum', true);
    jrCore_register_module_feature('jrCore', 'item_order_support', 'jrPhotoAlbum', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrPhotoAlbum', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrPhotoAlbum', 'update', 'item_action.tpl');

    // Core item buttons
    $_tmp = array(
        'title'  => 'add to photo album button',
        'icon'   => 'camera',
        'active' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'item_list_button', 'jrPhotoAlbum', 'jrPhotoAlbum_item_photoalbum_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrPhotoAlbum', 'jrPhotoAlbum_item_photoalbum_button', $_tmp);

    // Custom JS and CSS
    jrCore_register_module_feature('jrCore', 'javascript', 'jrPhotoAlbum', 'jrPhotoAlbum.js');
    jrCore_register_module_feature('jrCore', 'css', 'jrPhotoAlbum', 'jrPhotoAlbum.css');

    // We don't want the core provided "create" button
    jrCore_register_event_listener('jrCore', 'exclude_item_index_buttons', 'jrPhotoAlbum_exclude_item_index_buttons_listener');

    // Expand photo album entries
    jrCore_register_event_listener('jrCore', 'db_get_item', 'jrPhotoAlbum_db_get_item_listener');
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrPhotoAlbum_db_search_items_listener');

    // Convert photo albums for non-logged in users on login...
    jrCore_register_event_listener('jrUser', 'login_success', 'jrPhotoAlbum_login_success_listener');

    return true;
}

//---------------------------------------------------------
// ITEM BUTTONS
//---------------------------------------------------------

/**
 * Return "photoalbum" button for the item
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrPhotoAlbum_item_photoalbum_button($module, $_item, $_args, $smarty, $test_only = false)
{
    // See if the requesting module supports photo albums
    if ($module != 'jrGallery' && $module != 'jrFlickr') {
        return false;
    }
    if ($test_only) {
        return true;
    }
    $_rp = array(
        'photoalbum_for' => $module,
        'item_id'        => $_item['_item_id']
    );
    return smarty_function_jrPhotoAlbum_button($_rp, $smarty);
}

//---------------------------------------------------------
// SMARTY FUNCTIONS
//---------------------------------------------------------

/**
 * shows an add to photo album button on gallery files for logged in users.
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrPhotoAlbum_button($params, $smarty)
{
    global $_conf;

    // check to see if this user is allowed to add stuff to photo albums
    if (jrUser_is_logged_in()) {
        $key = jrUser_get_profile_home_key('quota_jrPhotoAlbum_allowed');
        if (!$key || $key != 'on') {
            return '';
        }
    }
    else {
        // Are we requiring the user be logged in?
        if (isset($_conf['jrPhotoAlbum_require_login']) && $_conf['jrPhotoAlbum_require_login'] == 'on') {
            return '';
        }
    }

    $size = null;
    if (isset($params['size']) && jrCore_checktype($params['size'], 'number_nz')) {
        $size = (int) $params['size'];
    }

    $item_id = (int) $params['item_id'];
    if ($item_id > 0) {

        $_lang                  = jrUser_load_lang_strings();
        $_rep                   = array();
        $_rep['photoalbum_for'] = $params['photoalbum_for']; //jrGallery, jrFlickr
        $_rep['item_id']        = $item_id;
        $_rep['uniqid']         = 'a' . uniqid();
        $_rep['size']           = (isset($params['size']) && is_numeric($params['size'])) ? (int) $params['size'] : 32;
        $_rep['width']          = (isset($params['width']) && is_numeric($params['width'])) ? (int) $params['width'] : 32;
        $_rep['height']         = (isset($params['height']) && is_numeric($params['height'])) ? (int) $params['height'] : 32;
        $_rep['alt']            = (isset($params['alt'])) ? $params['alt'] : $_lang['jrPhotoAlbum'][9];
        $_rep['title']          = $_rep['alt'];
        $_rep['class']          = (isset($params['class'])) ? $params['class'] : 'create_img';

        if (isset($params['image']{0})) {
            $src               = "{$_conf['jrCore_base_url']}/skins/{$_conf['jrCore_active_skin']}/img/{$params['image']}";
            $_rep['icon_html'] = '<img src="' . $src . '" class="' . $_rep['class'] . '" alt="' . $_rep['alt'] . '" title="' . $_rep['alt'] . '" onclick="jrPhotoAlbum_select(\'' . intval($item_id) . '\',\'' . $_rep['photoalbum_for'] . '\',null)">';
        }
        else {
            if (!isset($params['icon'])) {
                $params['icon'] = 'camera';
            }
            $_rep['icon_html'] = "<a onclick=\"jrPhotoAlbum_select('" . intval($item_id) . "','" . $_rep['photoalbum_for'] . "',null)\" title=\"{$_rep['alt']}\">" . jrCore_get_sprite_html($params['icon'], $size) . '</a>';
        }

        $out = jrCore_parse_template("photoalbum_button.tpl", $_rep, 'jrPhotoAlbum');
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], $out);
            return '';
        }
        return $out;
    }
    return '';
}

/**
 * Shows a DELETE button to remove a photo from an existing photo album
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrPhotoAlbum_delete_button($params, $smarty)
{
    global $_conf;

    if (jrUser_is_logged_in()) {
        $key = jrUser_get_profile_home_key('quota_jrPhotoAlbum_allowed');
        if (!$key || $key != 'on') {
            return '';
        }
    }
    else {
        // Are we requiring the user be logged in?
        if (isset($_conf['jrPhotoAlbum_require_login']) && $_conf['jrPhotoAlbum_require_login'] == 'on') {
            return '';
        }
    }

    $size = null;
    if (isset($params['size']) && jrCore_checktype($params['size'], 'number_nz')) {
        $size = (int) $params['size'];
    }

    $item_id = (int) $params['item_id'];
    if ($item_id > 0) {

        $_lang                  = jrUser_load_lang_strings();
        $_rep                   = array();
        $_rep['photoalbum_for'] = $params['photoalbum_for']; //jrGallery, jrFlickr
        $_rep['item_id']        = $item_id;
        $_rep['uniqid']         = 'a' . uniqid();
        $_rep['size']           = (isset($params['size']) && is_numeric($params['size'])) ? (int) $params['size'] : 32;
        $_rep['width']          = (isset($params['width']) && is_numeric($params['width'])) ? (int) $params['width'] : 32;
        $_rep['height']         = (isset($params['height']) && is_numeric($params['height'])) ? (int) $params['height'] : 32;
        $_rep['alt']            = (isset($params['alt'])) ? $params['alt'] : $_lang['jrPhotoAlbum'][10];
        $_rep['title']          = $_rep['alt'];
        $_rep['class']          = (isset($params['class'])) ? $params['class'] : 'create_img';

        if (isset($params['image']{0})) {
            $src               = "{$_conf['jrCore_base_url']}/skins/{$_conf['jrCore_active_skin']}/img/{$params['image']}";
            $_rep['icon_html'] = '<img src="' . $src . '" class="' . $_rep['class'] . '" alt="' . $_rep['alt'] . '" title="' . $_rep['alt'] . '" onclick="jrPhotoAlbum_select(\'' . $item_id . '\',\'' . $_rep['photoalbum_for'] . '\',null)">';
        }
        else {
            if (!isset($params['icon'])) {
                $params['icon'] = 'trash';
            }
            $_rep['icon_html'] = "<a onclick=\"jrPhotoAlbum_remove('" . $params['dom_id'] . "','" . $item_id . "','" . $_rep['photoalbum_for'] . "','" . $params['photo_id'] . "')\" title=\"{$_rep['alt']}\">" . jrCore_get_sprite_html($params['icon'], $size) . '</a>';
        }

        $out = jrCore_parse_template("photoalbum_button.tpl", $_rep, 'jrPhotoAlbum');
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], $out);
            return '';
        }
        return $out;
    }
    return '';
}

//---------------------------------------------------------
// LISTENERS
//---------------------------------------------------------

/**
 * Exclude core provided "create" item button
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrPhotoAlbum_exclude_item_index_buttons_listener($_data, $_user, $_conf, $_args, $event)
{
    // Exclude core create button...
    $_data['jrCore_item_create_button'] = true;
    return $_data;
}

/**
 * Expands the photo album array out to a full list of photo items
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 *
 * @return array
 */
function jrPhotoAlbum_db_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_is_view_request() && isset($_args['module']) && $_args['module'] == 'jrPhotoAlbum' && isset($_data['photoalbum_list']) && strlen($_data['photoalbum_list']) > 0) {
        $_pl  = array();
        $list = json_decode($_data['photoalbum_list'], true);
        if (isset($list) && is_array($list)) {
            // Get all items for each module in 1 shot
            // Our entries are stored like:
            // module => array(
            // 1 => 0,
            // 5 => 1,
            // 7 => 2
            // i.e. item_id => photoalbum_order
            $num = 0;
            $upd = false;
            foreach ($list as $module => $items) {
                // Get info about these items for our template
                $_sp = array(
                    'search'         => array(
                        "_item_id in " . implode(',', array_keys($items))
                    ),
                    'ignore_pending' => true,
                    'quota_check'    => false,
                    'limit'          => count($items)
                );
                $_rt = jrCore_db_search_items($module, $_sp);
                if (isset($_rt) && is_array($_rt) && isset($_rt['_items']) && is_array($_rt['_items'])) {
                    // Place each entry in it's proper output order
                    $_fnd = array();
                    foreach ($_rt['_items'] as $n => $_item) {
                        $ord                        = $items["{$_item['_item_id']}"];
                        $_item['photoalbum_module'] = $module;
                        $_item['module']            = $module;
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
            }
            if ($upd) {
                if (count($list) > 0) {
                    $_dt = array(
                        'photoalbum_list' => json_encode($list)
                    );
                    jrCore_db_update_item('jrPhotoAlbum', $_data['_item_id'], $_dt);
                }
            }
        }
        if (isset($_pl) && is_array($_pl) && count($_pl) > 0) {
            ksort($_pl, SORT_NUMERIC);
            $_data['photoalbum_items'] = $_pl;
            if (isset($num) && $num != $_data['photoalbum_count']) {
                // We've had a change in our item count - update
                $_dt = array(
                    'photoalbum_count' => $num
                );
                jrCore_db_update_item('jrPhotoAlbum', $_data['_item_id'], $_dt);
            }
        }
    }
    return $_data;
}

/**
 * Expands the photo album array out to a full list of photo items
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 *
 * @return array
 */
function jrPhotoAlbum_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if (is_array($_data) && isset($_data['_items'])) {
        if ($_args['module'] == 'jrPhotoAlbum') {
            foreach ($_data['_items'] as $k => $_v) {
                $list = json_decode($_v['photoalbum_list'], true);
                if (isset($list['jrGallery']) && is_array($list['jrGallery'])) {
                    foreach ($list['jrGallery'] as $gallery_id => $order) {
                        $_data['_items'][$k]['photoalbum_photos'][$order] = $gallery_id;
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * Convert temp photo albums to "real" photo albums on user login
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrPhotoAlbum_login_success_listener($_data, $_user, $_conf, $_args, $event)
{
    // When a user logs in, we save any photo albums they created when they weren't logged in
    $_tmp = jrCore_get_cookie('photoalbum');
    if ($_tmp) {
        // We have a photo album from the user when they were not logged in
        foreach ($_tmp as $_photoalbum) {
            unset($_photoalbum['_item_id']);
            $_cr = array(
                '_profile_id' => $_user['_profile_id']
            );
            jrCore_db_create_item('jrPhotoAlbum', $_photoalbum, $_cr);
        }
        jrCore_delete_cookie('photoalbum');
    }
    return $_data;
}
