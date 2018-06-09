<?php
/**
 * Jamroom 5 Proxima File module
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
 * @copyright 2014 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrProximaFile_meta
 */
function jrProximaFile_meta()
{
    $_tmp = array(
        'name'        => 'Proxima File',
        'url'         => 'px_file',
        'version'     => '1.1.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'File access API and upload support for Proxima modules',
        'category'    => 'proxima',
        'url_change'  => false,
        'requires'    => 'jrProximaCore:2.0.0',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * jrProximaFile_init
 */
function jrProximaFile_init()
{
    // Save uploaded FILES
    jrCore_register_event_listener('jrCore', 'db_create_item', 'jrProximaFile_db_create_item_listener');
    jrCore_register_event_listener('jrCore', 'db_update_item', 'jrProximaFile_db_update_item_listener');

    // Add URLs to GET and SEARCH responses
    jrCore_register_event_listener('jrProximaCore', 'get_response_keys', 'jrProximaFile_get_response_keys_listener');
    jrCore_register_event_listener('jrProximaCore', 'search_items', 'jrProximaFile_search_items_listener');

    // Delete attached files when keys are deleted
    // jrCore_register_event_listener('jrCore', 'db_delete_keys', 'jrProximaFile_db_delete_keys_listener');
    return true;
}

/**
 * jrProximaFile_px_config
 */
function jrProximaFile_px_config()
{
    // We return the config items that are for our module
    return array(
        'prefix' => 'pxf',
        'no_session' => array(
            'get' => array('/')
        )
    );
}

//------------------------------------
// EVENT LISTENERS
//------------------------------------

/**
 * Save uploaded files when items are created
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrProximaFile_db_create_item_listener($_data, $_user, $_conf, $_args, $event)
{
    // Save any files associated with this item
    $pid = jrProximaCore_get_session_profile_id();
    if ($pid && $pid > 0) {
        if (isset($_FILES) && is_array($_FILES)) {
            $pfx = jrCore_db_get_prefix($_args['module']);
            foreach ($_FILES as $fld => $_val) {
                if (jrCore_checktype($_val['size'], 'number_nz')) {
                    $ext = jrCore_file_extension($_val['name']);
                    $fnm = "{$_args['module']}_{$_args['_item_id']}_{$fld}.{$ext}";
                    if (jrCore_rename_media_file($pid, $_val['tmp_name'], $fnm)) {
                        $_data["{$pfx}_{$fld}_name"] = $_val['name'];
                        $_data["{$pfx}_{$fld}_size"] = $_val['size'];
                        $_data["{$pfx}_{$fld}_time"] = 'UNIX_TIMESTAMP()';
                        $_data["{$pfx}_{$fld}_type"] = jrCore_mime_type($_val['name']);
                        $_data["{$pfx}_{$fld}_ext"]  = $ext;
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * Save uploaded files when items are updated
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrProximaFile_db_update_item_listener($_data, $_user, $_conf, $_args, $event)
{
    // Save any files associated with this item
    $uid = jrProximaCore_get_session_user_id();
    if ($uid && $uid > 0) {
        if (isset($_FILES) && is_array($_FILES)) {
            $_rt = jrCore_db_get_item($_args['module'], $_args['_item_id'], true);
            if ($_rt && is_array($_rt)) {
                $pfx = jrCore_db_get_prefix($_args['module']);
                foreach ($_FILES as $fld => $_val) {
                    if (jrCore_checktype($_val['size'], 'number_nz')) {
                        $ext = jrCore_file_extension($_val['name']);
                        $fnm = "{$_args['module']}_{$_args['_item_id']}_{$fld}.{$ext}";
                        if (jrCore_rename_media_file($_rt['_profile_id'], $_val['tmp_name'], $fnm)) {
                            $_data["{$pfx}_{$fld}_name"] = basename($_val['name']);
                            $_data["{$pfx}_{$fld}_size"] = $_val['size'];
                            $_data["{$pfx}_{$fld}_time"] = 'UNIX_TIMESTAMP()';
                            $_data["{$pfx}_{$fld}_type"] = jrCore_mime_type($_val['name']);
                            $_data["{$pfx}_{$fld}_ext"]  = $ext;
                        }
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * Create File URLs on item GET
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrProximaFile_get_response_keys_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (is_array($_data)) {
        foreach ($_data as $k => $v) {
            // See if this we have a FILE - if we do, add in unique URL to retrieve the file
            $end = strpos($k, '_ext');
            if ($end && ($end + 4) === strlen($k)) {
                // We have a file field - i.e. avatar_ext
                $key = substr($k, 0, $end);
                $fld = substr($key, strlen($_args['prefix']) + 1);
                if (!isset($_data['_files'])) {
                    $_data['_files'] = array();
                }
                $_data['_files'][$fld] = array(
                    "ext"  => $v,
                    "name" => $_data["{$key}_name"],
                    "size" => $_data["{$key}_size"],
                    "time" => $_data["{$key}_time"],
                    "type" => $_data["{$key}_type"],
                    "url"  => "{$_conf['jrCore_base_url']}/px_file/download/{$_post['option']}/{$_data['_profile_id']}/{$_data['_item_id']}_{$key}.{$v}"
                );
                unset($_data["{$key}_ext"], $_data["{$key}_name"], $_data["{$key}_size"], $_data["{$key}_time"], $_data["{$key}_type"]);
            }
        }
    }
    return $_data;
}

/**
 * Create File URLs on item SEARCH
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrProximaFile_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (is_array($_data['_items'])) {
        foreach ($_data['_items'] as $k => $_item) {
            foreach ($_item as $nk => $v) {
                // See if this we have a FILE - if we do, add in unique URL to retrieve the file
                $end = strpos($nk, '_ext');
                if ($end && ($end + 4) === strlen($nk)) {
                    // We have a file field - i.e. avatar_ext
                    $fld = substr($nk, 0, $end);
                    $key = str_replace('_ext', '_url', $nk);
                    // /api/file/<stream|download>/<module>/<profile_id>/<id>/<field>.<extension>
                    $url                       = jrCore_get_module_url('jrProximaCore');
                    $_data['_items'][$k][$key] = "{$_conf['jrCore_base_url']}/{$url}/file/download/{$_post['option']}/{$_item['_profile_id']}/{$_item['_item_id']}_{$fld}.{$v}";
                }
            }
        }
    }
    return $_data;
}

/**
 * jrProximaFile GET Method functions
 * @param $_post array jrProximaCore post|get|put|delete URL vars
 * @param $_app array Proxima Application
 * @param $_cfg array jrProximaFile module config
 * @param $_vars array Method parameters
 * @return string
 */
function jrProximaFile_px_method_get($_post, $_app, $_cfg, $_vars)
{
    global $_urls;
    // Validate stream|download
    $func = null;
    switch ($_post['_1']) {
        case 'download':
            $func = 'jrCore_media_file_download';
            break;
        case 'stream':
            $func = 'jrCore_media_file_stream';
            break;
        default:
            return jrProximaCore_http_response(400, 'invalid file method - must be one of: stream, download');
            break;
    }
    // Validate Module is active
    if (!isset($_post['_2']) || !isset($_urls["px_{$_post['_2']}"])) {
        return jrProximaCore_http_response(404, 'invalid module');
    }
    $pxmod = $_urls["px_{$_post['_2']}"];
    if (!jrCore_module_is_active($pxmod)) {
        return jrProximaCore_http_response(404, 'invalid module');
    }
    if (!isset($_post['_3']) || !is_numeric($_post['_3'])) {
        return jrProximaCore_http_response(400, 'invalid profile id');
    }
    if (!isset($_post['_4']) || !strpos($_post['_4'], '.')) {
        return jrProximaCore_http_response(400, 'invalid file name');
    }
    $fnm = "{$pxmod}_{$_post['_4']}";
    if (!$func($_post['_3'], $fnm, $fnm)) {
        return jrProximaCore_http_response(400, "unable to {$_post['_1']} file");
    }
    exit;
}
