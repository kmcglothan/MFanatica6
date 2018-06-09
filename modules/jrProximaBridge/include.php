<?php
/**
 * Jamroom Proxima Bridge module
 *
 * copyright 2017 The Jamroom Network
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
 * @copyright 2015 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function jrProximaBridge_meta()
{
    $_tmp = array(
        'name'        => 'Proxima Bridge',
        'url'         => 'px_bridge',
        'version'     => '1.1.5',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'REST API for non-Proxima DataStore modules',
        'category'    => 'proxima',
        'url_change'  => false,
        'requires'    => 'jrProximaCore:2.0.0',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrProximaBridge_init()
{
    return true;
}

/**
 * px_config
 */
function jrProximaBridge_px_config()
{
    // We return the config items that are for our module
    return array(
        'prefix' => 'bridge'
    );
}

/**
 * POST Method function
 * @param $_post array jrProximaCore post|get|put|delete URL vars
 * @param $_app array Proxima Application
 * @param $_cfg array jrProximaData module config
 * @param $_vars array Method parameters
 * @return string
 */
function jrProximaBridge_px_method_post($_post, $_app, $_cfg, $_vars)
{
    // api/bridge/audio
    global $_conf, $_urls;
    if (!isset($_vars['_1']) || !isset($_urls["{$_vars['_1']}"]) || !jrCore_module_is_active($_urls["{$_vars['_1']}"])) {
        // Module not provided
        // Module is not a valid module
        // Module is not active
        return jrProximaCore_http_response(404);
    }
    $mod = $_urls["{$_vars['_1']}"];
    // Must be enabled
    if (!jrProximaBridge_module_enabled($mod)) {
        return jrProximaCore_http_response(400, "module is not enabled in Proxima Bridge");
    }

    // Validate Profile Quota
    $_us = jrProximaCore_get_session_user_info();
    if (!$_us) {
        // Should not happen
        return jrProximaCore_http_response(401, 'invalid user session');
    }
    if (isset($_us["quota_{$mod}_allowed"]) && $_us["quota_{$mod}_allowed"] != 'on') {
        return jrProximaCore_http_response(401, 'user is not allowed access to this module');
    }

    // Create
    $_up = array();
    $pfx = jrCore_db_get_prefix($mod);
    foreach ($_vars as $k => $v) {
        if (strpos($k, "{$pfx}_") === 0) {
            $_up[$k] = $v;
        }
    }

    if (count($_up) === 0) {
        return jrProximaCore_http_response(400, "you must provide at least one {$pfx} key");
    }

    // Get our form fields for CREATE and make sure we get a value for our REQUIRED ones
    $_fl = jrCore_get_designer_form_fields($mod, 'create');
    if ($_fl && is_array($_fl)) {
        foreach ($_fl as $k => $_v) {
            if (isset($_v['required']) && $_v['required'] == '1' && !isset($_up[$k]) && !isset($_FILES[$k])) {
                return jrProximaCore_http_response(400, "missing required {$pfx} key: {$k}");
            }
        }
    }
    // Check for title and generate slug
    if (isset($_fl["{$pfx}_title"]) && !isset($_fl["{$pfx}_title_url"])) {
        $_fl["{$pfx}_title_url"] = jrCore_url_string($_fl["{$pfx}_title"]);
    }
    $_cr = array(
        '_user_id'    => jrProximaCore_get_session_user_id(),
        '_profile_id' => jrProximaCore_get_session_profile_id()
    );

    if ($uid = jrCore_db_create_item($mod, $_up, $_cr)) {

        // Save any files associated with this item
        if (isset($_FILES) && is_array($_FILES)) {
            $pid = jrProximaCore_get_session_profile_id();
            foreach ($_FILES as $fld => $_val) {
                if (jrCore_checktype($_val['size'], 'number_nz')) {
                    $ext = jrCore_file_extension($_val['name']);
                    $fnm = "{$mod}_{$uid}_{$fld}.{$ext}";
                    if (jrCore_rename_media_file($pid, $_val['tmp_name'], $fnm)) {
                        $_up["{$pfx}_{$fld}_name"] = $_val['name'];
                        $_up["{$pfx}_{$fld}_size"] = $_val['size'];
                        $_up["{$pfx}_{$fld}_type"] = jrCore_mime_type($_val['name']);
                        $_up["{$pfx}_{$fld}_ext"]  = $ext;
                    }
                }
            }
            if (count($_up) > 0) {
                jrCore_db_update_item($mod, $uid, $_up);
            }
        }

        $crl = jrCore_get_module_url('jrProximaCore');
        $url = str_replace('px_', '', jrCore_get_module_url('jrProximaBridge'));
        $_rs = array(
            '_id'      => $uid,
            'location' => "{$_conf['jrCore_base_url']}/{$crl}/{$url}/{$_vars['_1']}/{$uid}"
        );
        return jrProximaCore_http_response(201, 'item created', $_rs);
    }

    return jrProximaCore_http_response(500, "error creating new item in datastore");
}

/**
 * GET Method function
 * @param $_post array jrProximaCore post|get|put|delete URL vars
 * @param $_app array Proxima Application
 * @param $_cfg array jrProximaData module config
 * @param $_vars array Method parameters
 * @return string
 */
function jrProximaBridge_px_method_get($_post, $_app, $_cfg, $_vars)
{
    // api/bridge/audio
    global $_urls;
    if (!isset($_vars['_1']) || !isset($_urls["{$_vars['_1']}"]) || !jrCore_module_is_active($_urls["{$_vars['_1']}"])) {
        // Module not provided
        // Module is not a valid module
        // Module is not active
        return jrProximaCore_http_response(404);
    }
    $mod = $_urls["{$_vars['_1']}"];
    // Must be enabled
    if (!jrProximaBridge_module_enabled($mod)) {
        return jrProximaCore_http_response(400, "module is not enabled in Proxima Bridge");
    }

    switch ($_post['_2']) {

        // Search DataStore items
        // api/bridge/module/search
        case 'search':

            if ($_rt = jrProximaBridge_search_items($mod, $_vars)) {
                unset($_rt['_params']);
                if (isset($_rt['_items'])) {
                    foreach ($_rt['_items'] as $k => $v) {
                        $_rt['_items'][$k] = jrProximaBridge_add_download_keys($mod, $v);
                    }
                }
                return jrProximaCore_http_response(200, null, $_rt);
            }
            return jrProximaCore_http_response(404);
            break;

        // Get single item
        // api/bridge/module/<id>
        default:

            // Must have a valid ID
            if (!isset($_vars['_2']) || !jrCore_checktype($_vars['_2'], 'number_nz')) {
                return jrProximaCore_http_response(400, 'invalid _item_id');
            }

            // Get Item
            if (!$_rt = jrCore_db_get_item($mod, $_vars['_2'], true)) {
                return jrProximaCore_http_response(404);
            }

            $_rt        = jrProximaBridge_add_download_keys($mod, $_rt);
            $_rt['_id'] = (int) $_vars['_2'];
            return jrProximaCore_http_response(200, null, $_rt);
            break;
    }
}

/**
 * PUT Method function
 * @param $_post array jrProximaCore post|get|put|delete URL vars
 * @param $_app array Proxima Application
 * @param $_cfg array jrProximaData module config
 * @param $_vars array Method parameters
 * @return string
 */
function jrProximaBridge_px_method_put($_post, $_app, $_cfg, $_vars)
{
    // api/bridge/audio
    global $_urls;
    if (!isset($_vars['_1']) || !isset($_urls["{$_vars['_1']}"]) || !jrCore_module_is_active($_urls["{$_vars['_1']}"])) {
        // Module not provided
        // Module is not a valid module
        // Module is not active
        return jrProximaCore_http_response(404);
    }
    $mod = $_urls["{$_vars['_1']}"];
    // Must be enabled
    if (!jrProximaBridge_module_enabled($mod)) {
        return jrProximaCore_http_response(400, "module is not enabled in Proxima Bridge");
    }

    // Must have a valid ID
    if (!isset($_vars['_2']) || !jrCore_checktype($_vars['_2'], 'number_nz')) {
        return jrProximaCore_http_response(400, 'invalid _item_id');
    }

    // Get Item
    if (!$_rt = jrCore_db_get_item($mod, $_vars['_2'], true)) {
        return jrProximaCore_http_response(404);
    }

    if (!isset($_rt['_user_id']) || $_rt['_user_id'] != jrProximaCore_get_session_user_id()) {
        return jrProximaCore_http_response(401, 'invalid item owner');
    }

    // Validate Profile Quota
    $_us = jrProximaCore_get_session_user_info();
    if (!$_us) {
        // Should not happen
        return jrProximaCore_http_response(401, 'invalid user session');
    }
    if (isset($_us["quota_{$mod}_allowed"]) && $_us["quota_{$mod}_allowed"] != 'on') {
        return jrProximaCore_http_response(401, 'user is not allowed access to this module');
    }

    // Update
    $_up = array();
    $pfx = jrCore_db_get_prefix($mod);
    foreach ($_vars as $k => $v) {
        if (strpos($k, "{$pfx}_") === 0) {
            $_up[$k] = $v;
        }
    }

    // Save any files associated with this item
    if (isset($_FILES) && is_array($_FILES)) {
        $pid = jrProximaCore_get_session_profile_id();
        foreach ($_FILES as $fld => $_val) {
            if (jrCore_checktype($_val['size'], 'number_nz')) {
                $ext = jrCore_file_extension($_val['name']);
                $fnm = "{$mod}_{$_vars['_2']}_{$fld}.{$ext}";
                if (jrCore_rename_media_file($pid, $_val['tmp_name'], $fnm)) {
                    $_up["{$pfx}_{$fld}_name"] = $_val['name'];
                    $_up["{$pfx}_{$fld}_size"] = $_val['size'];
                    $_up["{$pfx}_{$fld}_type"] = jrCore_mime_type($_val['name']);
                    $_up["{$pfx}_{$fld}_ext"]  = $ext;
                }
            }
        }
    }

    // Get our form fields for UPDATE and make sure we get a value for our REQUIRED ones
    $_fl = jrCore_get_designer_form_fields($mod, 'update');
    if ($_fl && is_array($_fl)) {
        foreach ($_fl as $k => $_v) {
            if (isset($_v['required']) && $_v['required'] == '1' && !isset($_up[$k]) && !isset($_rt[$k]) && !isset($_FILES[$k])) {
                return jrProximaCore_http_response(400, "missing required {$pfx} key: {$k}");
            }
        }
    }

    if (count($_up) > 0) {
        if (jrCore_db_update_item($mod, $_vars['_2'], $_up)) {
            return jrProximaCore_http_response(200);
        }
        else {
            return jrProximaCore_http_response(500, "error updating item in datastore");
        }
    }
    return jrProximaCore_http_response(400, "no keys provided to update");
}

/**
 * DELETE Method function
 * @param $_post array jrProximaCore post|get|put|delete URL vars
 * @param $_app array Proxima Application
 * @param $_cfg array jrProximaData module config
 * @param $_vars array Method parameters
 * @return string
 */
function jrProximaBridge_px_method_delete($_post, $_app, $_cfg, $_vars)
{
    // api/bridge/audio
    global $_urls;
    if (!isset($_vars['_1']) || !isset($_urls["{$_vars['_1']}"]) || !jrCore_module_is_active($_urls["{$_vars['_1']}"])) {
        // Module not provided
        // Module is not a valid module
        // Module is not active
        return jrProximaCore_http_response(404);
    }
    $mod = $_urls["{$_vars['_1']}"];
    // Must be enabled
    if (!jrProximaBridge_module_enabled($mod)) {
        return jrProximaCore_http_response(400, "module is not enabled in Proxima Bridge");
    }

    // Must have a valid ID
    if (!isset($_vars['_2']) || !jrCore_checktype($_vars['_2'], 'number_nz')) {
        return jrProximaCore_http_response(400, 'invalid _item_id');
    }

    // Get Item
    if (!$_rt = jrCore_db_get_item($mod, $_vars['_2'], true)) {
        return jrProximaCore_http_response(404);
    }

    if (!isset($_rt['_user_id']) || $_rt['_user_id'] != jrProximaCore_get_session_user_id()) {
        return jrProximaCore_http_response(401, 'invalid item owner');
    }

    // Validate Profile Quota
    $_us = jrProximaCore_get_session_user_info();
    if (!$_us) {
        // Should not happen
        return jrProximaCore_http_response(401, 'invalid user session');
    }
    if (isset($_us["quota_{$mod}_allowed"]) && $_us["quota_{$mod}_allowed"] != 'on') {
        return jrProximaCore_http_response(401, 'user is not allowed access to this module');
    }

    if (jrCore_db_delete_item($mod, $_vars['_2'])) {
        return jrProximaCore_http_response(200);
    }
    return jrProximaCore_http_response(500);
}

/**
 * Search a module data store for matching items
 * @param $module string Proxima Module
 * @param $_data array Item array
 * @return mixed array on success, bool false on fail
 */
function jrProximaBridge_search_items($module, $_data)
{
    global $_conf;
    // Item Search
    // search?search1=name%20eq%20brian&pagebreak=20&page=1
    $_sc = array(
        'search'         => array(),
        'order_by'       => array('_item_id' => 'numerical_desc'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'privacy_check'  => false,
        'limit'          => 10
    );
    foreach ($_data as $k => $v) {
        if (strpos($k, 'search') === 0) {
            if (strpos($v, '||') > 0) {
                // OR search
                $_e     = explode('||', $v);
                $_parts = array();
                foreach ($_e as $parts) {
                    list($key, $opt, $val) = explode(' ', $parts);
                    switch ($opt) {
                        case 'gt':
                            $opt = '>';
                            break;
                        case 'gte':
                            $opt = '>=';
                            break;
                        case 'lt':
                            $opt = '<';
                            break;
                        case 'lte':
                            $opt = '<=';
                            break;
                        case 'eq':
                            $opt = '=';
                            break;
                        case 'ne':
                            $opt = '!=';
                            break;
                    }
                    $_parts[] = "{$key} {$opt} {$val}";
                }
                $search = implode('||', $_parts);
            }
            else {
                list($key, $opt, $val) = explode(' ', $v);
                switch ($opt) {
                    case 'gt':
                        $opt = '>';
                        break;
                    case 'gte':
                        $opt = '>=';
                        break;
                    case 'lt':
                        $opt = '<';
                        break;
                    case 'lte':
                        $opt = '<=';
                        break;
                    case 'eq':
                        $opt = '=';
                        break;
                    case 'ne':
                        $opt = '!=';
                        break;
                }
                $search = "{$key} {$opt} {$val}";
            }

            $_sc['search'][] = $search;
        }
    }
    // Group by
    if (isset($_data['group_by']) && strlen($_data['group_by']) > 0 && $_sc['group_by'] != 'false') {
        $_sc['group_by'] = $_data['group_by'];
    }
    // Order By
    if (isset($_data['order_by']) && strlen($_data['order_by']) > 0 && isset($_data['order_dir']) && strlen($_data['order_dir']) > 0) {
        switch (strtolower($_data['order_dir'])) {
            case 'asc':
            case 'desc':
            case 'numerical_asc':
            case 'numerical_desc':
            case 'random':
                break;
            default:
                $_data['order_dir'] = 'desc';
                break;
        }
        $_sc['order_by'] = array($_data['order_by'] => $_data['order_dir']);
    }

    // What is our max result limit?
    $max = (isset($_conf['jrProximaCore_max_results'])) ? intval($_conf['jrProximaCore_max_results']) : 100;

    // Simplepagebreak
    if (isset($_data['simplepagebreak'])) {
        if ($_data['simplepagebreak'] > $max) {
            $_data['simplepagebreak'] = $max;
        }
        $_sc['simplepagebreak'] = $_data['simplepagebreak'];
        // Page
        if (isset($_data['page']) && jrCore_checktype($_data['page'], 'number_nz')) {
            $_sc['page'] = (int) $_data['page'];
        }
        else {
            $_sc['page'] = 1;
        }
    }

    // Pagebreak
    if (isset($_data['pagebreak'])) {
        if ($_data['pagebreak'] > $max) {
            $_data['pagebreak'] = $max;
        }
        $_sc['pagebreak'] = $_data['pagebreak'];
        // Page
        if (isset($_data['page']) && jrCore_checktype($_data['page'], 'number_nz')) {
            $_sc['page'] = (int) $_data['page'];
        }
        else {
            $_sc['page'] = 1;
        }
    }

    // Limit
    if (isset($_data['limit']) && jrCore_checktype($_data['limit'], 'number_nz')) {
        if ($_data['limit'] > $max) {
            $_data['limit'] = $max;
        }
        $_sc['limit'] = $_data['limit'];
    }

    $_rt = jrCore_db_search_items($module, $_sc);
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
        unset($_rt['info']['page_base_url']);
        return $_rt;
    }
    return false;
}

/**
 * Add Download keys for bridged items with files
 * @param $module string Module
 * @param $_item array Item Info
 * @return array
 */
function jrProximaBridge_add_download_keys($module, $_item)
{
    global $_conf;
    if (!is_array($_item)) {
        return $_item;
    }
    // See if we have a file or image field
    $_dn = array();
    $url = jrCore_get_module_url($module);
    foreach ($_item as $k => $v) {
        if ((strpos($k, 'image_size') || strpos($k, 'file_size')) && !isset($_dn[$k])) {
            // We have an image field
            $fld                      = str_replace('_size', '', $k);
            $_item["{$fld}_download"] = "{$_conf['jrCore_base_url']}/{$url}/download/{$fld}/{$_item['_item_id']}";
            $_dn[$k]                  = 1;
        }
    }
    return $_item;
}

/**
 * Get active JR modules for config
 * @return array
 */
function jrProximaBridge_get_active_modules()
{
    global $_mods;
    $_md = array();
    foreach ($_mods as $mod => $inf) {
        // Some are purposefully disabled
        switch ($mod) {
            case 'jrCore':
            case 'jrUser':
                break;
            default:
                if (jrCore_module_is_active($mod) && jrCore_db_get_prefix($mod) && strpos($mod, 'jrProx') !== 0) {
                    // Modules must have an item_index
                    if (is_file(APP_DIR . "/modules/{$mod}/templates/item_index.tpl")) {
                        $_md[$mod] = $inf['module_name'];
                    }
                }
                break;
        }
    }
    natcasesort($_md);
    return $_md;
}

/**
 * Return TRUE if a bridge module is enabled
 * @param $mod string module to check
 * @return bool
 */
function jrProximaBridge_module_enabled($mod)
{
    global $_conf;
    if (strstr(',' . $_conf['jrProximaBridge_active_modules'] . ',', ",{$mod},")) {
        return true;
    }
    return false;
}
