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
 * Download a file uploaded to a Proxima DS module
 * @param $_post array
 * @param $_user array
 * @param $_conf array
 * @return bool|string
 */
function view_jrProximaFile_download($_post, $_user, $_conf)
{
    global $_urls;
    if (!isset($_post['_1']) || !isset($_urls["px_{$_post['_1']}"])) {
        echo jrProximaCore_http_response(404, 'invalid module');
        exit;
    }
    $pxmod = $_urls["px_{$_post['_1']}"];
    if (!jrCore_module_is_active($pxmod)) {
        return jrProximaCore_http_response(404, 'invalid module');
    }
    if (!isset($_post['_2']) || !is_numeric($_post['_2'])) {
        return jrProximaCore_http_response(400, 'invalid profile id');
    }
    if (!isset($_post['_3']) || !strpos($_post['_3'], '.')) {
        return jrProximaCore_http_response(400, 'invalid file name');
    }
    list($item_id, $prefix, $name) = explode('_', $_post['_3'], 3);
    $_rt = jrCore_db_get_item('jrProximaData', $item_id, true);
    if (!isset($_rt['_g0']) || $_rt['_g0'] == 0) {
        // No global access to this item
        return jrProximaCore_http_response(401);
    }
    list($name, $ext) = explode('.', $name, 2);
    $send = (isset($_rt["{$prefix}_{$name}_name"])) ? $_rt["{$prefix}_{$name}_name"] : "{$name}.{$ext}";
    $name = "{$pxmod}_{$item_id}_{$name}.{$ext}";
    if (!jrCore_media_file_download($_post['_2'], $name, $send)) {
        return jrProximaCore_http_response(400, "unable to download file");
    }
    return true;
}

/**
 * Stream a file uploaded to a Proxima DS module
 * @param $_post array
 * @param $_user array
 * @param $_conf array
 * @return bool|string
 */
function view_jrProximaFile_stream($_post, $_user, $_conf)
{
    global $_urls;
    if (!isset($_post['_1']) || !isset($_urls["px_{$_post['_1']}"])) {
        echo jrProximaCore_http_response(404, 'invalid module');
        exit;
    }
    $pxmod = $_urls["px_{$_post['_1']}"];
    if (!jrCore_module_is_active($pxmod)) {
        return jrProximaCore_http_response(404, 'invalid module');
    }
    if (!isset($_post['_2']) || !is_numeric($_post['_2'])) {
        return jrProximaCore_http_response(400, 'invalid profile id');
    }
    if (!isset($_post['_3']) || !strpos($_post['_3'], '.')) {
        return jrProximaCore_http_response(400, 'invalid file name');
    }
    list($item_id, $prefix, $name) = explode('_', $_post['_3'], 3);
    $_rt = jrCore_db_get_item('jrProximaData', $item_id, true);
    if (!isset($_rt['_g0']) || $_rt['_g0'] == 0) {
        // No global access to this item
        return jrProximaCore_http_response(401);
    }
    list($name, $ext) = explode('.', $name, 2);
    $send = (isset($_rt["{$prefix}_{$name}_name"])) ? $_rt["{$prefix}_{$name}_name"] : "{$name}.{$ext}";
    $name = "{$pxmod}_{$item_id}_{$name}.{$ext}";
    if (!jrCore_media_file_stream($_post['_2'], $name, $send)) {
        return jrProximaCore_http_response(400, "unable to stream file");
    }
    return true;
}
