<?php
/**
 * Jamroom Files module
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
function jrFile_meta()
{
    $_tmp = array(
        'name'        => 'Files',
        'url'         => 'file',
        'version'     => '1.0.13',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Add File upload and download support to Profiles',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/278/files',
        'category'    => 'profiles',
        'requires'    => 'jrCore:6.0.4',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrFile_init()
{
    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrFile', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrFile', 'update');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrFile', true);

    // Integrate with payment system
    jrCore_register_event_listener('jrFoxyCart', 'add_price_field', 'jrFile_add_price_field_listener');
    jrCore_register_event_listener('jrFoxyCart', 'my_items_row', 'jrFile_my_items_row_listener');
    jrCore_register_event_listener('jrFoxyCart', 'adding_item_to_purchase_history', 'jrFile_adding_item_to_purchase_history_listener');
    jrCore_register_event_listener('jrFoxyCart', 'my_earnings_row', 'jrFile_my_earnings_row_listener');

    // We can be hidden but included in bundles
    jrCore_register_module_feature('jrFoxyCartBundle', 'visible_support', 'jrFile', true);
    jrCore_register_module_feature('jrFoxyCartBundle', 'bundle_only_support', 'jrFile', 'create');
    jrCore_register_module_feature('jrFoxyCartBundle', 'bundle_only_support', 'jrFile', 'update');

    // Core support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrFile', 'off');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrFile', 'on');
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrFile', 'on');
    jrCore_register_module_feature('jrCore', 'item_order_support', 'jrFile', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrFile', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrFile', 'update', 'item_action.tpl');

    // When an action is shared via jrOneAll, we can provide the text of the shared item
    jrCore_register_event_listener('jrOneAll', 'network_share_text', 'jrFile_network_share_text_listener');

    // We have fields that can be searched
    jrCore_register_module_feature('jrSearch', 'search_fields', 'jrFile', 'file_title', 22);

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'jrFile', 'profile_jrFile_item_count', 22);

    $_tmp = array(
        'title'  => 'download file button',
        'icon'   => 'download',
        'active' => 'on',
        'group'  => 'admin'
    );
    jrCore_register_module_feature('jrCore', 'item_list_button', 'jrFile', 'jrFile_item_download_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrFile', 'jrFile_item_download_button', $_tmp);

    jrCore_register_module_feature('jrTips', 'tip', 'jrFile', 'tip');

    return true;
}

//---------------------------------------------------------
// AUDIO ITEM BUTTONS
//---------------------------------------------------------

/**
 * Return "download" button for admin users
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrFile_item_download_button($module, $_item, $_args, $smarty, $test_only = false)
{
    global $_conf;
    if ($module == 'jrFile') {
        if ($test_only) {
            return true;
        }
        if (jrCore_checktype($_item['file_file_size'], 'number_nz')) {
            $url = jrCore_get_module_url('jrFile');
            $_rt = array(
                'url'  => "{$_conf['jrCore_base_url']}/{$url}/download/file_file/{$_item['_item_id']}/" . urlencode(strtolower($_item['file_file_original_name'])),
                'icon' => 'download'
            );
            return $_rt;
        }
    }
    return false;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Add share data to a jrOneAll network share
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return mixed
 */
function jrFile_network_share_text_listener($_data, $_user, $_conf, $_args, $event)
{
    // $_data:
    // [providers] => twitter
    // [user_token] => c6418e9a-b66e-4c6c-xxxx-cdea7e915d03
    // [user_id] => 1
    // [action_module] => jrFile
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
    $url = jrCore_get_module_url('jrFile');
    $txt = $_ln['jrFile'][1];
    if ($_data['action_mode'] == 'update') {
        $txt = $_ln['jrFile'][23];
    }
    $_out = array(
        'text' => "{$_conf['jrCore_base_url']}/{$_data['profile_url']} {$_data['profile_name']} {$txt}: \"{$_data['file_title']}\" {$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['_item_id']}/{$_data['file_title_url']}",
        'link' => array(
            'url'  => "{$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['_item_id']}/{$_data['file_title_url']}",
            'name' => $_data['file_title']
        )
    );
    // See if they included a picture with the song
    if (isset($_data['file_image_size']) && jrCore_checktype($_data['file_image_size'], 'number_nz')) {
        $_out['picture'] = array(
            'url' => "{$_conf['jrCore_base_url']}/{$url}/image/file_image/{$_data['_item_id']}/large"
        );
    }
    return $_out;
}

/**
 * Return file field for forms
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrFile_add_price_field_listener($_data, $_user, $_conf, $_args, $event)
{
    // View => File Field
    $_data["jrFile/create"] = 'file_file';
    $_data["jrFile/update"] = 'file_file';
    return $_data;
}

/**
 * Get information about an item for FoxyCart
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrFile_my_items_row_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['purchase_module']) && $_args['purchase_module'] == 'jrFile') {
        $url = jrCore_get_module_url('jrFile');

        $_data[2]['title'] = $_args['file_title'];
        $_data[5]['title'] = jrCore_page_button("a{$_args['_item_id']}", 'download', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/vault_download/file_file/{$_args['_item_id']}')");
    }
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
function jrFile_my_earnings_row_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['purchase_module']) && $_args['purchase_module'] == 'jrFile') {
        $_data[1]['title'] = $_args['file_title'];
    }
    return $_data;
}

/**
 * fired when foxycart sends a confirmed order in the background back to our system.
 * If there are files in the order, those files need to be kept in the system vault
 * so they can be downloaded.  do that moving here.
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return mixed
 */
function jrFile_adding_item_to_purchase_history_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrFile') {
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
 * {jrFile_util}
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return array
 */
function smarty_function_jrFile_util($params, $smarty)
{
    global $_user;
    switch ($params['mode']) {
        case 'get_my_files':
            $_params = array(
                'search'   => array(
                    "_profile_id = {$_user['user_active_profile_id']}",
                ),
                "order_by" => array(
                    '_item_id' => 'DESC'
                ),
                "limit"    => 100
            );
            $_rt     = jrCore_db_search_items('jrFile', $_params);
            $items   = $_rt['_items'];
            if (!empty($params['assign'])) {
                $smarty->assign($params['assign'], $items);
                return '';
            }
            break;
    }
    return '';
}

/**
 * jrFile_download_button
 * Smarty function to create a download button
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrFile_download_button($params, $smarty)
{
    global $_conf;
    // We must get the item
    if (!isset($params['item']) || !is_array($params['item'])) {
        return 'jrFile_download_button: item parameter required';
    }
    // Enabled?
    if (!jrCore_module_is_active('jrFile')) {
        return '';
    }
    // Is it allowed in this quota?
    if (!jrProfile_is_allowed_by_quota('jrFile', $smarty)) {
        return '';
    }
    // We don't show downloads for files that are for sale, or part of a bundle
    if (!empty($params['item']['file_file_item_price']) || !empty($params['item']['file_file_item_bundle'])) {
        return '';
    }
    $cls = '';
    if (isset($params['class'])) {
        $cls = ' class="' . $params['class'] . '"';
    }
    $alt = '';
    $ttl = '';
    if (isset($params['alt'])) {
        $alt = ' alt="' . $params['alt'] . '"';
        $ttl = ' title="' . $params['alt'] . '"';
    }
    $url = jrCore_get_module_url('jrFile');
    $url = "{$_conf['jrCore_base_url']}/{$url}/download/file_file/{$params['item']['_item_id']}";
    if (isset($params['image']) && strlen($params['image']) > 0) {
        $src = "{$_conf['jrCore_base_url']}/skins/{$_conf['jrCore_active_skin']}/img/{$params['image']}";
        $out = '<a href="' . $url . '"><img src="' . $src . '"' . $cls . $alt . $ttl . '></a>';
    }
    else {
        if (!isset($params['icon'])) {
            $params['icon'] = 'download';
        }
        $out = "<a href=\"{$url}\"" . $alt . $ttl . '>' . jrCore_get_sprite_html($params['icon']) . '</a>';
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * jrFile_get_link
 * Smarty function to return a link to the file download
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrFile_get_link($params, $smarty)
{
    global $_conf;
    if (!jrCore_module_is_active('jrFile')) {
        return '';
    }
    // Check the incoming parameters
    if (!isset($params['item_id']) || !jrCore_checktype($params['item_id'], 'number_nz')) {
        return 'item_id not set correctly';
    }
    if (!isset($params['style']) || strlen($params['style']) === 0) {
        $params['style'] = '';
    }
    if (!isset($params['class']) || strlen($params['class']) === 0) {
        $params['class'] = '';
    }
    if (isset($params['template']) && $params['template'] != '') {
        $params['tpl_dir'] = $_conf['jrCore_active_skin'];
    }
    else {
        $params['template'] = "link.tpl";
        $params['tpl_dir']  = 'jrFile';
    }
    $_tmp = array();
    foreach ($params as $k => $v) {
        $_tmp['jrFile'][$k] = $v;
    }
    // Get file DS
    $_rt = jrCore_db_get_item('jrFile', $params['item_id']);
    foreach ($_rt as $k => $v) {
        $_tmp['jrFile'][$k] = $v;
    }
    $out = jrCore_parse_template($params['template'], $_tmp, $params['tpl_dir']);
    if (isset($params['assign']) && $params['assign'] != '') {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}
