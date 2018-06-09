<?php
/**
 * Jamroom 5 Proxima Data module
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
 * jrProximaData_meta
 */
function jrProximaData_meta()
{
    $_tmp = array(
        'name'        => 'Proxima Data',
        'url'         => 'px_data',
        'version'     => '1.1.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Generalized DataStore API for creating, updating and deleting objects',
        'category'    => 'proxima',
        'url_change'  => false,
        'requires'    => 'jrProximaCore:2.0.0',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * jrProximaData_init
 */
function jrProximaData_init()
{
    // Tools
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrProximaData', 'collection_browser', array('Collection Browser', 'Browse, Create and Update Data Collections'));

    // Tabs
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrProximaData', 'collection_browser', 'Collections');

    // Our default view for admins
    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrProximaData', 'collection_browser');

    // We provide our own data browser
    jrCore_register_module_feature('jrCore', 'data_browser', 'jrProximaData', 'jrProximaData_data_browser');

    // Delete collections and data when an app is deleted
    jrCore_register_event_listener('jrProximaCore', 'app_deleted', 'jrProximaData_app_deleted_listener');

    // Delete data worker
    jrCore_register_queue_worker('jrProximaData', 'delete_data', 'jrProximaData_delete_data_worker', 0, 1, 14400);

    return true;
}

/**
 * px_config
 */
function jrProximaData_px_config()
{
    return array(
        'prefix' => 'p'
    );
}

//------------------------------------
// EVENT LISTENERS
//------------------------------------

/**
 * Data Cleanup when an app is deleted
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrProximaData_app_deleted_listener($_data, $_user, $_conf, $_args, $event)
{
    // App is being deleted
    if (isset($_data['app_id']) && jrCore_checktype($_data['app_id'], 'number_nz')) {

        // Delete Collections
        $tbl = jrCore_db_table_name('jrProximaData', 'collections');
        $req = "DELETE FROM {$tbl} WHERE c_app_id = '{$_data['app_id']}'";
        jrCore_db_query($req);

        // Deleting data could be a big job - queue
        jrCore_queue_create('jrProximaData', 'delete_data', $_data);

    }
    return $_data;
}

//------------------------------------
// QUEUE WORKER
//------------------------------------

/**
 * Delete DS entries when an App is deleted
 * @param array $_queue The queue entry the worker will receive
 * @return bool
 */
function jrProximaData_delete_data_worker($_queue)
{
    // We need to delete all DS entries for $_queue['app_id']
    $_rt = jrCore_db_get_multiple_items_by_key('jrProximaData', '_app_id', $_queue['app_id'], true);
    if ($_rt && is_array($_rt)) {
        $_rt = array_chunk($_rt, 500);
        foreach ($_rt as $_ids) {
            jrCore_db_delete_multiple_items('jrProximaData', $_ids);
        }
    }
    return true;
}

//------------------------------------
// METHOD FUNCTIONS
//------------------------------------

/**
 * jrProximaData POST Method functions
 * @param $_post array jrProximaCore post|get|put|delete URL vars
 * @param $_app array Proxima Application
 * @param $_cfg array jrProximaData module config
 * @param $_vars array Method parameters
 * @return string
 */
function jrProximaData_px_method_post($_post, $_app, $_cfg, $_vars)
{
    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        return jrProximaCore_http_response(400, 'collection name required');
    }
    if (!$_ccf = jrProximaData_get_collection_config($_app['app_id'], $_post['_1'])) {
        return jrProximaCore_http_response(400, 'invalid collection');
    }
    if (jrProximaData_collection_is_readonly($_ccf)) {
        return jrProximaCore_http_response(401, 'collection is readonly');
    }
    $_core = array(
        '_g' => $_post['_1']
    );
    // Check for global permissions for objects in this collection
    if ($acl = jrProximaData_get_global_permissions($_cfg)) {
        // We have global R or RW access
        $_core['_p0'] = $acl;
    }

    $_vars = jrProximaCore_clean_method_variables($_vars);
    $_vars = jrProximaCore_add_module_prefix('jrProximaData', $_vars);

    if ($iid = jrCore_db_create_item('jrProximaData', $_vars, $_core)) {
        jrProximaData_collection_item_count_increment($_app['app_id'], $_post['_1']);
        $_rs = array(
            '_id'      => $iid,
            '_app_id'  => $_app['app_id'],
            'location' => jrProximaCore_get_unique_item_url('jrProximaData', $iid, $_post['_1'])
        );
        return jrProximaCore_http_response(201, null, $_rs);
    }
    return jrProximaCore_http_response(500);
}

/**
 * jrProximaData GET Method functions
 * @param $_post array jrProximaCore post|get|put|delete URL vars
 * @param $_app array Proxima Application
 * @param $_cfg array jrProximaData module config
 * @param $_vars array Method parameters
 * @return string
 */
function jrProximaData_px_method_get($_post, $_app, $_cfg, $_vars)
{
    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        return jrProximaCore_http_response(400, 'collection name required');
    }
    if (!$_ccf = jrProximaData_get_collection_config($_app['app_id'], $_post['_1'])) {
        return jrProximaCore_http_response(404, 'collection not found');
    }

    switch ($_post['_2']) {

        // Search Items
        // api/data/collection/search
        case 'search':
            if ($_rt = jrProximaCore_search_items('jrProximaData', $_vars, $_post['_1'])) {
                foreach ($_rt['_items'] as $k => $v) {
                    $_rt['_items'][$k] = jrProximaCore_get_response_keys('jrProximaData', $v);
                }
                unset($_rt['_params']);
                return jrProximaCore_http_response(200, null, $_rt);
            }
            return jrProximaCore_http_response(404);
            break;

        // Get single item
        // api/data/collection/<id>
        default:
            if ($_rt = jrCore_db_get_item('jrProximaData', $_post['_2'], true)) {
                if ($_rt['_g'] != $_post['_1']) {
                    return jrProximaCore_http_response(404, 'collection mismatch');
                }
                if (jrProximaCore_get_client_access_level() != 'master' && isset($_ccf['c_perms']) && $_ccf['c_perms'] == 1) {
                    $uid = jrProximaCore_get_session_user_id();
                    $acc = jrProximaCore_get_user_access_level($uid, $_rt);
                    if ($acc != 'read' && $acc != 'write') {
                        return jrProximaCore_http_response(401, 'invalid item owner');
                    }
                }
                $_rt = jrProximaCore_get_response_keys('jrProximaData', $_rt);
                return jrProximaCore_http_response(200, null, $_rt);
            }
            return jrProximaCore_http_response(404);
            break;
    }
}

/**
 * jrProximaData PUT Method functions
 * @param $_post array jrProximaCore post|get|put|delete URL vars
 * @param $_app array Proxima Application
 * @param $_cfg array jrProximaData module config
 * @param $_vars array Method parameters
 * @return string
 */
function jrProximaData_px_method_put($_post, $_app, $_cfg, $_vars)
{
    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        return jrProximaCore_http_response(400, 'collection name required');
    }
    if (!isset($_post['_2']) || !jrCore_checktype($_post['_2'], 'number_nz')) {
        return jrProximaCore_http_response(400, "invalid item id");
    }
    if (!$_ccf = jrProximaData_get_collection_config($_app['app_id'], $_post['_1'])) {
        return jrProximaCore_http_response(400, 'invalid collection');
    }

    // Read only collection?
    if (jrProximaData_collection_is_readonly($_ccf)) {
        return jrProximaCore_http_response(401, 'collection is read only');
    }

    // Check ACL
    if (jrProximaCore_get_client_access_level() != 'master' && isset($_ccf['c_perms']) && $_ccf['c_perms'] != 3) {
        // This collection is a PRIVATE or GLOBAL READ collection - that means only the
        // item owner can actually update the item UNLESS the item has been
        // specifically allowed by ACL - get item to check permissions
        if ($_it = jrCore_db_get_item('jrProximaData', $_post['_2'], true)) {
            $uid = jrProximaCore_get_session_user_id();
            $acc = jrProximaCore_get_user_access_level($uid, $_it);
            if ($acc != 'write') {
                return jrProximaCore_http_response(401, 'invalid item owner');
            }
        }
        else {
            return jrProximaCore_http_response(404);
        }
    }

    // Fall through - update
    $_vars = jrProximaCore_add_module_prefix('jrProximaData', $_vars);
    $_vars = jrProximaCore_run_value_functions('jrProximaData', $_post['_2'], $_vars);
    $_vars = jrProximaCore_clean_method_variables($_vars);

    if (jrCore_db_update_item('jrProximaData', $_post['_2'], $_vars)) {
        return jrProximaCore_http_response(200);
    }
    return jrProximaCore_http_response(500);
}

/**
 * jrProximaData DELETE Method functions
 * @param $_post array jrProximaCore post|get|put|delete URL vars
 * @param $_app array Proxima Application
 * @param $_cfg array jrProximaData module config
 * @param $_vars array Method parameters
 * @return string
 */
function jrProximaData_px_method_delete($_post, $_app, $_cfg, $_vars)
{
    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        return jrProximaCore_http_response(400, 'collection name required');
    }
    if (!isset($_post['_2']) || !jrCore_checktype($_post['_2'], 'number_nz')) {
        return jrProximaCore_http_response(400, 'invalid item id');
    }
    if (!$_ccf = jrProximaData_get_collection_config($_app['app_id'], $_post['_1'])) {
        return jrProximaCore_http_response(400, 'invalid collection');
    }
    if (jrProximaData_collection_is_readonly($_ccf)) {
        return jrProximaCore_http_response(401, 'collection is readonly');
    }

    // CHeck ACL
    if (jrProximaCore_get_client_access_level() != 'master' && isset($_ccf['c_perms']) && $_ccf['c_perms'] != 3) {
        // This collection is a PRIVATE or GLOBAL READ collection - that means only the
        // item owner can actually update the item UNLESS the item has been
        // specifically allowed by ACL - get item to check permissions
        if ($_it = jrCore_db_get_item('jrProximaData', $_post['_2'], true)) {
            $uid = jrProximaCore_get_session_user_id();
            $acc = jrProximaCore_get_user_access_level($uid, $_it);
            if ($acc != 'write') {
                return jrProximaCore_http_response(401, 'invalid item owner');
            }
        }
        else {
            return jrProximaCore_http_response(404);
        }
    }

    if (jrCore_db_delete_item('jrProximaData', $_post['_2'])) {
        jrProximaData_collection_item_count_decrement($_app['app_id'], $_post['_1']);
        return jrProximaCore_http_response(200);
    }
    return jrProximaCore_http_response(500);
}

//------------------------------------
// HELPER FUNCTIONS
//------------------------------------

/**
 * Create a new Collection for an App
 * @param $app_id int App ID to create collection for
 * @param $name string Name of Collection
 * @param $perms int 1|2|3 (default object permissions)
 * @param string $read_only Set to 'on' to create a Read Only collection
 * @return bool|mixed
 */
function jrProximaData_create_collection($app_id, $name, $perms, $read_only = 'off')
{
    if (!jrProximaData_get_collection_by_name($app_id, $name)) {
        $aid = (int) $app_id;
        $nam = jrCore_db_escape(trim($name));
        $prm = (int) $perms;
        $rdo = ($read_only == 'on') ? 'on' : 'off';
        $tbl = jrCore_db_table_name('jrProximaData', 'collections');
        $req = "INSERT INTO {$tbl} (c_app_id, c_name, c_items, c_perms, c_readonly) VALUES ('{$aid}', '{$nam}', '0', '{$prm}', '{$rdo}')";
        $cid = jrCore_db_query($req, 'INSERT_ID');
        if ($cid) {
            jrCore_delete_cache('jrProximaData', "{$app_id}:collections", false, false);
            return $cid;
        }
    }
    return false;
}

/**
 * Get a Data Collection by name
 * @param $app_id int App ID
 * @param $name string Collection Name
 * @return bool|mixed
 */
function jrProximaData_get_collection_by_name($app_id, $name)
{
    $aid = (int) $app_id;
    $nam = jrCore_db_escape(trim($name));
    $tbl = jrCore_db_table_name('jrProximaData', 'collections');
    $req = "SELECT * FROM {$tbl} WHERE c_name = '{$nam}' AND c_app_id = '{$aid}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        return $_rt;
    }
    return false;
}

/**
 * Get config for a Data Collection
 * @param $app_id int Application ID
 * @param $collection string Collection Name
 * @return mixed
 */
function jrProximaData_get_collection_config($app_id, $collection)
{
    $key = "{$app_id}:collections";
    if (!$_cl = jrCore_is_cached('jrProximaData', $key, false, false)) {
        $tbl = jrCore_db_table_name('jrProximaData', 'collections');
        $req = "SELECT * FROM {$tbl} WHERE c_app_id = '{$app_id}'";
        $_cl = jrCore_db_query($req, 'c_name');
        jrCore_add_to_cache('jrProximaData', $key, $_cl, 0, 0, false, false);
        if (!is_array($_cl)) {
            return false;
        }
    }
    if (isset($_cl[$collection])) {
        return $_cl[$collection];
    }
    return false;
}

/**
 * Check if a collection is readonly
 * @param $_cfg array Collection config
 * @return bool
 */
function jrProximaData_collection_is_readonly($_cfg)
{
    if (jrProximaCore_get_client_access_level() != 'master') {
        if ($_cfg['c_readonly'] == 'on') {
            // Readonly - no write access
            return true;
        }
    }
    return false;
}

/**
 * Check for default global permissions
 * @param $_cfg array Collection config
 * @return bool
 */
function jrProximaData_get_global_permissions($_cfg)
{
    // 2 = global read
    // 3 = global read and write
    if (isset($_cfg['c_perms']) && $_cfg['c_perms'] > 1) {
        // we have more collection permissions than object permissions
        // at the object level: 1 = read, 2 = read/write
        return intval($_cfg['c_perms'] - 1);
    }
    return false;
}

/**
 * Increment a collection item counter
 * @param $app_id int Application ID
 * @param $collection string Collection Name
 * @return mixed
 */
function jrProximaData_collection_item_count_increment($app_id, $collection)
{
    $tbl = jrCore_db_table_name('jrProximaData', 'collections');
    $req = "UPDATE {$tbl} SET c_items = (c_items + 1) WHERE c_app_id = '{$app_id}' AND c_name = '" . jrCore_db_escape($collection) . "'";
    return jrCore_db_query($req, 'COUNT');
}

/**
 * Decrement a collection item counter
 * @param $app_id int Application ID
 * @param $collection string Collection Name
 * @return mixed
 */
function jrProximaData_collection_item_count_decrement($app_id, $collection)
{
    $tbl = jrCore_db_table_name('jrProximaData', 'collections');
    $req = "UPDATE {$tbl} SET c_items = (c_items - 1) WHERE c_items > 0 AND c_app_id = '{$app_id}' AND c_name = '" . jrCore_db_escape($collection) . "'";
    return jrCore_db_query($req, 'COUNT');
}

/**
 * Delete a collection and its data
 * @param $id int Collection ID
 * @return bool
 */
function jrProximaData_delete_collection($id)
{
    $tbl = jrCore_db_table_name('jrProximaData', 'collections');
    $req = "SELECT * FROM {$tbl} WHERE c_id = '{$id}' LIMIT 1";
    $_ap = jrCore_db_query($req, 'SINGLE');
    if (!$_ap || !is_array($_ap)) {
        return false;
    }
    $req = "DELETE FROM {$tbl} WHERE c_id = '{$id}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        // Delete objects from the DataStore
        $_dt = jrCore_db_get_multiple_items_by_key('jrProximaData', '_g', $_ap['c_name'], true);
        jrCore_db_delete_multiple_items('jrProximaData', $_dt, false, false);
        return true;
    }
    return false;
}

/**
 * Custom Data Store browser tool
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 * @return bool
 */
function jrProximaData_data_browser($_post, $_user, $_conf)
{
    // our collection will come in as $_post['_2']
    if (isset($_post['_2']) && strlen($_post['_2']) > 0) {
        $_SESSION['jrproximadata_active_collection'] = $_post['_2'];
    }
    $col = (isset($_SESSION['jrproximadata_active_collection'])) ? $_SESSION['jrproximadata_active_collection'] : '';
    // get our items
    $_pr = array(
        'search'         => array(
            "_g = {$col}"
        ),
        'pagebreak'      => (isset($_COOKIE['jrcore_pager_rows']) && jrCore_checktype($_COOKIE['jrcore_pager_rows'], 'number_nz')) ? (int) $_COOKIE['jrcore_pager_rows'] : 12,
        'page'           => 1,
        'order_by'       => array(
            '_item_id' => 'desc'
        ),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true,
        'privacy_check'  => false
    );
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $_pr['page'] = (int) $_post['p'];
    }
    // See we have a search condition
    $_ex = false;
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_ex = array('search_string' => $_post['search_string']);
        // Check for passing in a specific key name for search
        if (strpos($_post['search_string'], ':')) {
            list($sf, $ss) = explode(':', $_post['search_string'], 2);
            $_post['search_string'] = $ss;
            if (is_numeric($ss)) {
                $_pr['search'][] = "{$sf} = {$ss}";
            }
            else {
                $_pr['search'][] = "{$sf} like {$ss}%";
            }
        }
        else {
            $_pr['search'][] = "% like %{$_post['search_string']}%";
        }
    }
    $_us = jrCore_db_search_items('jrProximaData', $_pr);

    // Start our output
    $dat             = array();
    $dat[1]['title'] = 'id';
    $dat[1]['width'] = '5%';
    $dat[2]['title'] = 'info';
    $dat[2]['width'] = '78%';
    $dat[3]['title'] = 'modify';
    $dat[3]['width'] = '2%';
    jrCore_page_table_header($dat);

    if (isset($_us['_items']) && is_array($_us['_items'])) {
        foreach ($_us['_items'] as $_itm) {
            $dat             = array();
            $iid             = $_itm['_item_id'];
            $pfx             = jrCore_db_get_prefix('jrProximaData');
            $dat[1]['title'] = $iid;
            $dat[1]['class'] = 'center';
            $_tm             = array();
            ksort($_itm);
            $_rep = array("\n", "\r", "\n\r");
            foreach ($_itm as $k => $v) {
                switch ($k) {
                    case '_user_id':
                    case '_profile_id':
                    case '_item_id':
                    case '_app_id':
                        break;
                    case '_g':
                        $_tm[] = "<span class=\"ds_browser_key\">collection:</span> <span class=\"ds_browser_value\">{$v}</span>";
                        break;
                    default:
                        if (isset($v) && is_array($v)) {
                            $v = json_encode($v);
                        }
                        if (is_numeric($v) && strlen($v) === 10) {
                            $v = jrCore_format_time($v);
                        }
                        if (strlen($v) > 80) {
                            $v = substr($v, 0, 80) . '...';
                        }
                        if (isset($_post['search_string'])) {
                            // See if we are searching a specific field
                            if (isset($sf)) {
                                if ($k == $sf) {
                                    $v = jrCore_hilight_string($v, str_replace('%', '', $_post['search_string']));
                                }
                            }
                            else {
                                $v = jrCore_hilight_string($v, str_replace('%', '', $_post['search_string']));
                            }
                        }
                        $v     = strip_tags(str_replace($_rep, ' ', $v));
                        $_tm[] = "<span class=\"ds_browser_key\">" . str_replace("{$pfx}_", '', $k) . ":</span> <span class=\"ds_browser_value\">{$v}</span>";
                        break;
                }
            }
            $dat[3]['title'] = implode('<br>', $_tm);
            $_att            = array(
                'style' => 'width:70px;'
            );

            $url             = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browser_item_update/id={$iid}";
            $dat[4]['title'] = '';
            $dat[4]['title'] .= jrCore_page_button("m{$iid}", 'modify', "jrCore_window_location('{$url}')", $_att) . '<br><br>';
            $dat[4]['title'] .= jrCore_page_button("d{$iid}", 'delete', "if (confirm('Are you sure you want to delete this item? The item will be PERMANENTLY deleted!')){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/browser_item_delete/id={$iid}')}", $_att);
            $dat[4]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_us, $_ex);
    }
    else {
        $dat = array();
        if (isset($_post['search_string'])) {
            $dat[1]['title'] = '<p>No Results found for your Search Criteria.</p>';
        }
        else {
            $dat[1]['title'] = '<p>No Items found in DataStore!</p>';
        }
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    return true;
}

