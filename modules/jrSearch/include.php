<?php
/**
 * Jamroom Search module
 *
 * copyright 2018 The Jamroom Network
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
function jrSearch_meta()
{
    $_tmp = array(
        'name'        => 'Search',
        'url'         => 'search',
        'version'     => '2.0.4',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Site Wide Search plus search system for registered modules',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/950/search',
        'category'    => 'listing',
        'requires'    => 'jrCore:5.3.0',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrSearch_init()
{
    jrCore_register_module_feature('jrCore', 'css', 'jrSearch', 'jrSearch.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrSearch', 'jrSearch.js');

    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrSearch_db_search_params_listener');

    // Maintain our full text index
    jrCore_register_event_listener('jrCore', 'db_create_item', 'jrSearch_db_create_item_listener');
    jrCore_register_event_listener('jrCore', 'db_update_item', 'jrSearch_db_update_item_listener');
    jrCore_register_event_listener('jrCore', 'db_delete_item', 'jrSearch_db_delete_item_listener');

    // System reset listener
    jrCore_register_event_listener('jrDeveloper', 'reset_system', 'jrSearch_reset_system_listener');

    // Our re-index tool
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrSearch', 'rebuild', array('Rebuild Indexes', 'Rebuild Search Indexes'));

    // Our index creator/worker
    jrCore_register_queue_worker('jrSearch', 'search_index', 'jrSearch_search_index_worker', 0, 4, 14400);

    // Site Builder widgets
    jrCore_register_module_feature('jrSiteBuilder', 'widget', 'jrSearch', 'widget_search', 'Site Search');

    // Checktype plugin
    jrCore_register_module_feature('jrCore', 'checktype', 'jrSearch', 'searchable_field');

    return true;
}

//------------------------
// WIDGETS
//------------------------

/**
 * Display CONFIG screen for Widget
 * @param $_post array Post info
 * @param $_user array User array
 * @param $_conf array Global Config
 * @param $_wg array Widget info
 * @return bool
 */
function jrSearch_widget_search_config($_post, $_user, $_conf, $_wg)
{
    global $_mods;

    // module
    $_opt = jrCore_get_datastore_modules();
    foreach ($_opt as $mod => $url) {
        if (!jrCore_module_is_active($mod)) {
            unset($_opt[$mod]);
            continue;
        }
        if (!jrSearch_is_excluded_module($mod)) {
            if (is_file(APP_DIR . "/modules/{$mod}/templates/item_list.tpl")) {
                $_opt[$mod] = $_mods[$mod]['module_name'];
            }
            else {
                unset($_opt[$mod]);
            }
        }
    }
    $_opt['_'] = '- Search All Modules -';
    natcasesort($_opt);

    $_tmp = array(
        'name'     => 'search_module',
        'label'    => 'Search Module',
        'help'     => 'Select the module whos items you want to search',
        'options'  => $_opt,
        'value'    => (isset($_wg['widget_data']['search_module'])) ? $_wg['widget_data']['search_module'] : '_',
        'type'     => 'select',
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    return true;
}

/**
 * Get Widget results from posted Config data
 * @param $_post array Post info
 * @return array
 */
function jrSearch_widget_search_config_save($_post)
{
    $_data = array(
        'search_module' => $_post['search_module']
    );
    return array('widget_data' => $_data);
}

/**
 * Widget DISPLAY
 * @param $_widget array Page Widget info
 * @return string
 */
function jrSearch_widget_search_display($_widget)
{
    return jrCore_parse_template('widget_search.tpl', $_widget, 'jrSearch');
}

//------------------------
// QUEUE WORKER
//------------------------

/**
 * Maintain a FULL TEXT index for a module
 * @param $_queue array Queue info
 * @return bool
 */
function jrSearch_search_index_worker($_queue)
{
    $mod = $_queue['module'];

    if (isset($_queue['action']) && $_queue['action'] == 'delete') {

        // We're _removing_ a module from the index
        jrSearch_delete_index_table_for_module($mod);

        $tbl = jrCore_db_table_name('jrSearch', 'fulltext');
        $req = "DELETE FROM {$tbl} WHERE s_module = '{$mod}'";
        jrCore_db_query($req);

    }
    else {

        // Has the module told us what fields to index?
        $_fl = jrSearch_get_module_index_fields($mod);
        if ($_fl) {

            $key = '_item_id';
            switch ($mod) {
                case 'jrProfile':
                    $key = '_profile_id';
                    break;
                case 'jrUser':
                    $key = '_user_id';
                    break;
            }

            // Get items and add to index
            $cnt = 0;
            $off = 0;
            $_ky = array($key);
            $_ky = array_merge(array_keys($_fl), $_ky);
            while (true) {

                $_rt = array(
                    'search'        => array(
                        "_item_id > {$off}"
                    ),
                    'skip_triggers' => true,
                    'privacy_check' => false,
                    'return_keys'   => $_ky,
                    'cache_seconds' => 0,
                    'limit'         => 500
                );
                $_rt = jrCore_db_search_items($mod, $_rt);
                if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
                    $_in = array();
                    $_md = false;
                    if (jrSearch_module_has_dedicated_index($mod)) {
                        $_md = array();
                    }
                    foreach ($_rt['_items'] as $_v) {

                        $off = (int) $_v[$key];
                        if ($_tm = jrSearch_get_insert_items($mod, $off, $_v, $_fl)) {
                            foreach ($_tm as $add) {
                                $_in[] = $add;
                            }
                        }
                        if (is_array($_md)) {
                            if ($_tm = jrSearch_get_insert_items($mod, $off, $_v, $_fl, false)) {
                                foreach ($_tm as $add) {
                                    $_md[] = $add;
                                }
                            }
                        }
                        $cnt++;

                    }
                    if (count($_in) > 0) {
                        jrSearch_insert_global_search_rows($_in);
                    }
                    if ($_md && count($_md) > 0) {
                        jrSearch_insert_module_search_rows($mod, $_md);
                    }
                    if ($cnt < 500) {
                        break;
                    }
                }
                else {
                    break;
                }
            }
        }
    }
    return true;
}

//------------------------
// EVENT LISTENERS
//------------------------

/**
 * System Reset listener
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrSearch_reset_system_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_mods;
    $tbl = jrCore_db_table_name('jrSearch', 'fulltext');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");
    jrCore_db_query("OPTIMIZE TABLE {$tbl}");
    foreach ($_mods as $mod => $_inf) {
        if (jrCore_is_datastore_module($mod) && jrSearch_module_has_dedicated_index($mod)) {
            if (jrCore_db_table_exists('jrSearch', "fulltext_{$mod}")) {
                $tbl = jrCore_db_table_name('jrSearch', "fulltext_{$mod}");
                jrCore_db_query("TRUNCATE TABLE {$tbl}");
                jrCore_db_query("OPTIMIZE TABLE {$tbl}");
            }
        }
    }
    return $_data;
}

/**
 * Add Text fields to full text search
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrSearch_db_create_item_listener($_data, $_user, $_conf, $_args, $event)
{
    // Get our index fields
    $_fl = jrSearch_get_module_index_fields($_args['module']);
    if ($_fl) {
        $_in = jrSearch_get_insert_items($_args['module'], $_args['_item_id'], $_data, $_fl);
        if ($_in && is_array($_in)) {
            jrSearch_insert_global_search_rows($_in);

            // Does this module have it's own dedicated search index?
            if (jrSearch_module_has_dedicated_index($_args['module'])) {
                $_in = jrSearch_get_insert_items($_args['module'], $_args['_item_id'], $_data, $_fl, false);
                if ($_in && is_array($_in)) {
                    jrSearch_insert_module_search_rows($_args['module'], $_in);
                }
            }
        }
    }
    return $_data;
}

/**
 * Update Text fields for full text search
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrSearch_db_update_item_listener($_data, $_user, $_conf, $_args, $event)
{
    // Get our index fields
    $_fl = jrSearch_get_module_index_fields($_args['module']);
    if ($_fl) {

        // Are we missing any fields in our update?
        $update = false;
        foreach ($_fl as $field => $weight) {
            if (!isset($_data[$field])) {
                $update = true;
                break;
            }
        }
        // When we get an item UPDATE we only get the keys that are
        // changing - we need to get ALL the $_fl keys for this item
        // and merge in the changes
        if ($update) {
            if ($_ex = jrCore_db_get_item($_args['module'], $_args['_item_id'], true)) {
                foreach ($_fl as $field => $weight) {
                    if (!isset($_data[$field]) && isset($_ex[$field])) {
                        $_data[$field] = $_ex[$field];
                    }
                }
            }
        }

        // First - delete item from existing indexes
        jrSearch_delete_item_from_indexes($_args['module'], $_args['_item_id']);

        // Get new list of search words
        $_in = jrSearch_get_insert_items($_args['module'], $_args['_item_id'], $_data, $_fl);
        if ($_in && is_array($_in)) {


            jrSearch_insert_global_search_rows($_in);

            // Does this module have it's own dedicated search index?
            if (jrSearch_module_has_dedicated_index($_args['module'])) {
                $_in = jrSearch_get_insert_items($_args['module'], $_args['_item_id'], $_data, $_fl, false);
                if ($_in && is_array($_in)) {
                    jrSearch_insert_module_search_rows($_args['module'], $_in);
                }
            }
        }
    }
    return $_data;
}

/**
 * Delete an entry from the fulltext index
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrSearch_db_delete_item_listener($_data, $_user, $_conf, $_args, $event)
{
    jrSearch_delete_item_from_indexes($_args['module'], $_args['_item_id']);
    return $_data;
}

/**
 * Add support for custom list parameters
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrSearch_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_urls, $_post;
    if (isset($_post['module_url']) && isset($_post['ss']) && strlen($_post['ss']) > 0) {

        // See if this is from a profile or the site
        if (!isset($_urls["{$_post['module_url']}"])) {
            // Profile module index
            $pfx = jrCore_db_get_prefix($_urls["{$_post['option']}"]);
            $mod = $_urls["{$_post['option']}"];
        }
        else {
            // Module index
            $pfx = jrCore_db_get_prefix($_post['module']);
            $mod = $_post['module'];
        }
        if ($mod != $_data['module']) {
            // This is an embedded jrCore_list in our search - this is not for us
            return $_data;
        }
        if ($pfx) {

            $cache_key = json_encode($_post);
            if (!$_rt = jrCore_is_cached('jrSearch', $cache_key, false, false)) {

                // See we if have full text searching enabled
                $_rt = false;

                // See what the minimum word length is for using Full Text Searching (default for MySQL is 4)
                $len = 4;
                if (strlen($_post['ss']) < 5) {
                    $len = jrSearch_get_ft_min_word_length();
                }
                $use_like = true;
                if (strlen($_post['ss']) >= $len) {

                    $limit = 1000;
                    if (isset($_conf['jrSearch_match_limit']) && jrCore_checktype(intval($_conf['jrSearch_match_limit']), 'number_nz')) {
                        $limit = (int) $_conf['jrSearch_match_limit'];
                    }
                    $_rt = jrSearch_get_matching_ids_from_full_text_index($mod, $_post['ss'], 0, $limit);
                    if ($_rt && is_array($_rt)) {
                        $use_like = false;
                    }
                    else {
                        // We came out of our FULLTEXT search with no results - are we going to do a partial match?
                        if (isset($_conf['jrSearch_partial']) && $_conf['jrSearch_partial'] == 'on') {
                            $use_like = true;
                        }
                        else {
                            $use_like = false;
                        }
                    }

                }

                // Fall through - we either are too short or don't have full text enabled
                if ($use_like) {
                    $sst = jrCore_db_escape(str_replace('"', '', $_post['ss']));
                    if (jrSearch_module_has_dedicated_index($mod)) {
                        $tbl = jrCore_db_table_name('jrSearch', "fulltext_{$mod}");
                        $req = "SELECT `s_id` AS i FROM {$tbl} WHERE `s_text` LIKE '%{$sst}%'";
                    }
                    else {
                        $tbl = jrCore_db_table_name('jrSearch', 'fulltext');
                        $req = "SELECT `s_id` AS i FROM {$tbl} WHERE `s_text` LIKE '%{$sst}%' AND `s_module` = '{$mod}'";
                    }
                    $_rt = jrCore_db_query($req, 'i', false, 'i');
                }

                // Cache our results
                jrCore_add_to_cache('jrSearch', $cache_key, $_rt, 0, 0, false, false);

            }

            // Did we get our results based on a full text search?
            if ($_rt && is_array($_rt) && count($_rt) > 0) {

                $key = '_item_id';
                switch ($mod) {
                    case 'jrProfile':
                        $key = '_profile_id';
                        break;
                    case 'jrUser':
                        $key = '_user_id';
                        break;
                }

                // We are adding a search condition
                if (!isset($_data['search']) && !isset($_data['profile_id']) && !isset($_SESSION['jrSearch_search_params'])) {

                    if (!isset($_data['page']) || !jrCore_checktype($_data['page'], 'number_nz')) {
                        $_data['page'] = 1;
                    }

                    // We can process less data here...
                    $pbrk = false;
                    if (isset($_data['pagebreak']) && jrCore_checktype($_data['pagebreak'], 'number_nz')) {
                        $pbrk = (int) $_data['pagebreak'];
                    }
                    if (isset($_data['simplepagebreak']) && jrCore_checktype($_data['simplepagebreak'], 'number_nz')) {
                        $pbrk = (int) $_data['simplepagebreak'];
                    }

                    $_data['search']              = array();
                    $_data['use_total_row_count'] = count($_rt);

                    $rcnt = count($_rt);
                    if ($pbrk > 0 && $rcnt > $pbrk && $rcnt > 200 && (!isset($_conf['jrSearch_optimize']) || $_conf['jrSearch_optimize'] == 'on')) {
                        $_rt = array_slice($_rt, 0, (($_data['page'] * 3) * $pbrk));
                    }
                }
                $_data['search'][] = "{$key} in " . implode(',', $_rt);

                if (isset($_data['order_by'])) {
                    unset($_data['order_by']);
                }

                // Next - see if we have any additional fields that were passed in by function
                if (isset($_SESSION['jrSearch_search_params']) && is_array($_SESSION['jrSearch_search_params'])) {
                    foreach ($_SESSION['jrSearch_search_params'] as $cond) {
                        $_data['search'][] = $cond;
                    }
                    unset($_SESSION['jrSearch_search_params']);
                }
            }
            else {
                // No match - set empty result set
                $_data['result_set'] = array();
            }
        }

    }
    else {

        // Are we asking for any full_text searches?
        if (isset($_data['search']) && is_array($_data['search'])) {
            if ($_fl = jrSearch_get_module_index_fields($_args['module'])) {
                foreach ($_data['search'] as $k => $v) {
                    if (stripos($v, 'full_text')) {
                        list($field, , $ss) = explode(' ', $v, 3);
                        if (isset($_fl[$field])) {
                            $limit = 100;
                            $break = 10;
                            if (isset($_data['page']) && jrCore_checktype($_data['page'], 'number_nz')) {
                                $page = (int) $_data['page'];
                                if (isset($_data['simplepagebreak'])) {
                                    $break = (int) $_data['simplepagebreak'];
                                }
                                elseif (isset($_data['pagebreak'])) {
                                    $break = (int) $_data['pagebreak'];
                                }
                                $limit = ($page * $break);
                            }
                            if ($_ids = jrSearch_get_matching_ids_from_full_text_index($_args['module'], $ss, 0, $limit)) {
                                $_data['search'][$k] = '_item_id in ' . implode(',', $_ids);
                            }
                            else {
                                // We have no matches - set no match condition
                                $_data['search'] = array('_item_id < 0');
                            }
                        }
                    }
                }
            }
        }

    }
    return $_data;
}

//------------------------
// FUNCTIONS
//------------------------

/**
 * Get an array of matching item_ids from Search's FULLTEXT index
 * @param string $module
 * @param string $search_string
 * @param int $offset
 * @param int $limit
 * @return array|bool
 */
function jrSearch_get_matching_ids_from_full_text_index($module, $search_string, $offset = 0, $limit = 10000)
{
    global $_conf;

    // Are we doing natural language or boolean?
    // http://dev.mysql.com/doc/refman/5.1/en/fulltext-boolean.html
    // http://dev.mysql.com/doc/refman/5.1/en/fulltext-natural-language.html
    $nat = true;

    // Are we looking for an exact match (quotes)?
    if (strpos($search_string, '"') === 0 || strpos($search_string, "'") === 0) {

        $sst = jrCore_db_escape(str_replace(array('"', "'"), '', $search_string));
        if (jrSearch_module_has_dedicated_index($module)) {
            $tbl = jrCore_db_table_name('jrSearch', "fulltext_{$module}");
            $req = "SELECT `s_id` AS i, `s_mod` AS s FROM {$tbl} WHERE `s_text` LIKE '%{$sst}%'";
        }
        else {
            $tbl = jrCore_db_table_name('jrSearch', 'fulltext');
            $req = "SELECT `s_id` AS i, `s_mod` AS s FROM {$tbl} WHERE `s_module` = '" . jrCore_db_escape($module) . "' AND `s_text` LIKE '%{$sst}%'";
        }
        $_ss = jrCore_db_query($req, 'i', false, 's');
        if ($_ss && is_array($_ss)) {
            // These results are coming out un-ordered - order
            arsort($_ss, SORT_NUMERIC);
            $_ss = array_keys($_ss);
            return array_slice($_ss, $offset, $limit);
        }
        return false;
    }

    $_mt = explode(' ', $search_string);
    if (stripos($search_string, ' AND ') || stripos($search_string, ' OR ') || stripos($search_string, ' NOT ')) {
        // apple and pine not pineapple
        // apple and pine or banana
        // Clean boolean operators
        foreach ($_mt as $k => $word) {
            $word = strtolower($word);
            if ($word == 'and') {
                // With an AND we add + to the preceding AND next word in the search phrase
                $prv = ($k - 1);
                if (isset($_mt[$prv])) {
                    $_mt[$prv] = '+' . str_replace(array('+', '-'), '', $_mt[$prv]);
                }
                $nxt = ($k + 1);
                if (isset($_mt[$nxt])) {
                    $_mt[$nxt] = '+' . str_replace(array('+', '-'), '', $_mt[$nxt]);
                }
                unset($_mt[$k]);
            }
            elseif ($word == 'or') {
                // Or is the default, so we just remove our OR
                unset($_mt[$k]);
            }
            elseif ($word == 'not') {
                // With NOT with add a "-" sign to the next word in the search phrase
                $nxt = ($k + 1);
                if (isset($_mt[$nxt])) {
                    $_mt[$nxt] = '-' . str_replace(array('+', '-'), '', $_mt[$nxt]);
                }
                unset($_mt[$k]);
            }
        }
        $search_string = implode(' ', $_mt);
    }

    $_mt = explode(' ', $search_string);
    if ($_mt && is_array($_mt)) {
        foreach ($_mt as $str) {
            $char = substr($str, 0, 1);
            switch ($char) {
                case '+';
                case '-';
                case '~';
                    $nat = false;
                    break 2;
            }
            if (strpos($str, '*')) {
                $nat = false;
                break;
            }
        }
    }

    // Get search method
    if (!isset($_conf['jrSearch_method'])) {
        $_conf['jrSearch_method'] = 'both';
    }
    $smd = 'IN BOOLEAN MODE';
    switch ($_conf['jrSearch_method']) {
        case 'natural':
            $smd = 'IN NATURAL LANGUAGE MODE';
            break;
        default:
            if ($nat || count($_mt) > 4) {
                $smd = 'IN NATURAL LANGUAGE MODE';
            }
            break;
    }

    $sst = jrCore_db_escape($search_string);
    if (jrSearch_module_has_dedicated_index($module)) {
        $tbl = jrCore_db_table_name('jrSearch', "fulltext_{$module}");
        $req = "SELECT `s_id` AS i, (MATCH(`s_text`) AGAINST('{$sst}' {$smd}) * `s_mod`) AS s FROM {$tbl} WHERE MATCH(`s_text`) AGAINST('{$sst}' {$smd})";
    }
    else {
        $tbl = jrCore_db_table_name('jrSearch', 'fulltext');
        $req = "SELECT `s_id` AS i, (MATCH(`s_text`) AGAINST('{$sst}' {$smd}) * `s_mod`) AS s FROM {$tbl} WHERE MATCH(`s_text`) AGAINST('{$sst}' {$smd}) AND `s_module` = '" . jrCore_db_escape($module) . "'";
    }
    $_ss = jrCore_db_query($req, 'i', false, 's');
    if ($_ss && is_array($_ss)) {
        // These results are coming out un-ordered - order
        arsort($_ss, SORT_NUMERIC);
        $_ss = array_keys($_ss);
        return array_slice($_ss, $offset, $limit);
    }
    return false;
}

/**
 * Get the min word length for full text search in MySQL
 * @return int
 */
function jrSearch_get_ft_min_word_length()
{
    // get ft_min_word_len from MySQL
    $req = "SHOW VARIABLES LIKE 'ft_min_word_len'";
    $_ln = jrCore_db_query($req, 'SINGLE');
    if ($_ln && isset($_ln['Value'])) {
        return intval($_ln['Value']);
    }
    return 4;  // MySQL default is 4
}

/**
 * Insert Rows into the Global Full Text Search table
 * @param $_in array insert rows
 * @return bool
 */
function jrSearch_insert_global_search_rows($_in)
{
    // Insert into global search index
    $tbl = jrCore_db_table_name('jrSearch', 'fulltext');
    $req = "INSERT INTO {$tbl} (`s_module`, `s_id`, `s_mod`, `s_text`) VALUES " . implode(',', $_in) . " ON DUPLICATE KEY UPDATE `s_text` = VALUES(`s_text`)";
    jrCore_db_query($req, null, false, null, false, null, false);
    return true;
}

/**
 * Insert Rows into the Module Full Text Search table
 * @param $module string
 * @param $_in array insert rows
 * @return bool
 */
function jrSearch_insert_module_search_rows($module, $_in)
{
    // Insert into global search index
    $tbl = jrCore_db_table_name('jrSearch', "fulltext_{$module}");
    $req = "INSERT INTO {$tbl} (`s_id`, `s_mod`, `s_text`) VALUES " . implode(',', $_in) . " ON DUPLICATE KEY UPDATE `s_text` = VALUES(`s_text`)";
    jrCore_db_query($req, null, false, null, false, null, false);
    return true;
}

/**
 * Delete a single item from the search indexes
 * @param string $module
 * @param int $item_id
 * @return bool
 */
function jrSearch_delete_item_from_indexes($module, $item_id)
{
    // Delete from global index
    $tbl = jrCore_db_table_name('jrSearch', 'fulltext');
    $req = "DELETE FROM {$tbl} WHERE `s_module` = '{$module}' AND `s_id` = '{$item_id}'";
    jrCore_db_query($req);

    // Check for dedicated module index
    if (jrSearch_module_has_dedicated_index($module)) {
        $tbl = jrCore_db_table_name('jrSearch', "fulltext_{$module}");
        $req = "DELETE FROM {$tbl} WHERE `s_id` = '{$item_id}'";
        jrCore_db_query($req);
    }
    return true;
}

/**
 * Returns TRUE if a module has a dedicated index table
 * @param string $module
 * @return bool
 */
function jrSearch_module_has_dedicated_index($module)
{
    global $_conf;
    if (!jrSearch_is_disabled_module($module) && isset($_conf['jrSearch_dedicated']) && strpos(' ,' . $_conf['jrSearch_dedicated'] . ',', ",{$module},")) {
        return true;
    }
    return false;
}

/**
 * Create a new dedicated full text index table for a module
 * @param string $module
 * @return bool
 */
function jrSearch_create_index_table_for_module($module)
{
    // Full text search
    $_tmp = array(
        "s_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "s_mod TINYINT(1) UNSIGNED NOT NULL DEFAULT '1'",
        "s_text TEXT NOT NULL",
        "UNIQUE s_unique (s_id, s_mod)",
        "FULLTEXT s_text (s_text)"
    );
    // NOTE: MySQL 5.6+ and MariaDB can use InnoDB
    $_db = jrCore_db_query("SHOW VARIABLES WHERE Variable_name = 'version'", 'SINGLE');
    if ($_db && is_array($_db) && isset($_db['Value'])) {
        $ver = $_db['Value'];
    }
    else {
        $msi = jrCore_db_connect();
        $ver = mysqli_get_server_info($msi);
    }
    if (strpos($ver, '-')) {
        list($ver,) = explode('-', $ver);
    }
    $engine = 'MyISAM';
    if (version_compare($ver, '5.6.4', '>=')) {
        // We should be able to use InnoDB here - double check
        $_ft = jrCore_db_query("SHOW VARIABLES LIKE '%nnodb_optimize_fulltext%'", 'SINGLE');
        if ($_ft && is_array($_ft)) {
            $engine = 'InnoDB';
        }
    }
    return jrCore_db_verify_table('jrSearch', "fulltext_{$module}", $_tmp, $engine);
}

/**
 * Delete a dedicated index table for a module
 * @param string $module
 * @return bool
 */
function jrSearch_delete_index_table_for_module($module)
{
    $tbl = jrCore_db_table_name('jrSearch', "fulltext_{$module}");
    $req = "DROP TABLE IF EXISTS {$tbl}";
    jrCore_db_query($req);
    jrCore_logger('INF', "successfully removed dedicated search index for module: {$module}");
    return true;
}

/**
 * Some modules we don't want in search
 * @param $mod string Module
 * @return bool
 */
function jrSearch_is_excluded_module($mod)
{
    // Some are excluded on purpose...
    switch ($mod) {
        case 'jrRating':
        case 'jrSmiley':
        case 'jrSeamless':
            return true;
            break;
        case 'jrTags':
            return false;
            break;
    }
    // Others must have an item_list.tpl
    if (file_exists(APP_DIR . "/modules/{$mod}/templates/item_list.tpl")) {
        return false;
    }
    return true;
}

/**
 * Return TRUE if a module has been disabled from search in Global Config
 * @param string $mod module to check
 * @return bool
 */
function jrSearch_is_disabled_module($mod)
{
    global $_conf;
    if (isset($_conf['jrSearch_disabled']) && strlen($_conf['jrSearch_disabled']) > 1) {
        if (strpos(" ," . $_conf['jrSearch_disabled'] . ',', ",{$mod},")) {
            return true;
        }
    }
    return false;
}

/**
 * Get insert rows for MySQL full text search
 * @param $mod string Module
 * @param $item_id int Item ID
 * @param $_item array Item info
 * @param $_fields array fields to index
 * @param $global_index bool set to FALSE for module specific index
 * @return array|bool
 */
function jrSearch_get_insert_items($mod, $item_id, $_item, $_fields, $global_index = true)
{
    $_vl = array();
    foreach ($_fields as $fld => $weight) {
        if (isset($_item[$fld])) {
            if (!isset($_vl[$weight])) {
                $_vl[$weight] = '';
            }
            $_vl[$weight] .= $_item[$fld] . ' ';
        }
    }
    if (count($_vl) > 0) {
        $_in = array();
        foreach ($_vl as $w => $t) {
            $t = trim(preg_replace('!\s+!', ' ', str_replace(array("\n", "\r"), ' ', strip_tags($t))));
            if (strlen($t) > 0) {
                $t = jrCore_db_escape(jrCore_strip_emoji(mb_substr($t, 0, 16384), false));
                if ($global_index) {
                    $_in[] = "('{$mod}',{$item_id},'{$w}','{$t}')";
                }
                else {
                    $_in[] = "({$item_id},'{$w}','{$t}')";
                }
            }
        }
        if (count($_in) > 0) {
            return $_in;
        }
    }
    return false;
}

/**
 * Get the index fields for a given module
 * @param $module string Module to get fields for
 * @return array|bool
 */
function jrSearch_get_module_index_fields($module)
{
    global $_conf;

    // Our defaults
    $pfx = jrCore_db_get_prefix($module);
    $_fn = array(
        "{$pfx}_name"        => 3,
        "{$pfx}_title"       => 3,
        "{$pfx}_text"        => 1,
        "{$pfx}_desc"        => 1,
        "{$pfx}_description" => 1,
        "{$pfx}_caption"     => 1,
        "{$pfx}_tags"        => 2
    );

    // See if this module has registered any search fields
    $_tm = jrCore_get_registered_module_features('jrSearch', 'search_fields');
    if ($_tm && isset($_tm[$module]) && is_array($_tm[$module])) {
        $tmp = array_keys($_tm[$module]);
        $tmp = reset($tmp);
        $_fl = explode(',', $tmp);
        if ($_fl && is_array($_fl)) {
            foreach ($_fl as $fld) {
                if (strpos($fld, '_title') || strpos($fld, '_name')) {
                    $_fn[$fld] = 3;
                }
                elseif (strpos($fld, '_tags')) {
                    $_fn[$fld] = 2;
                }
                else {
                    $_fn[$fld] = 1;
                }
            }
        }
    }

    // Has the module told us what fields to index?
    $_tm = jrCore_get_registered_module_features('jrSearch', 'fulltext_search_fields');
    if ($_tm && isset($_tm[$module]) && is_array($_tm[$module])) {
        foreach ($_tm[$module] as $fld => $weight) {
            $_fn[$fld] = $weight;
        }
    }

    // See if there are any custom fields for our module
    $pfx = jrCore_db_get_prefix($module);
    if (strpos(' ' . $_conf['jrSearch_search_fields'], $pfx)) {
        foreach (explode("\n", $_conf['jrSearch_search_fields']) as $v) {
            $v = trim($v);
            if (strpos($v, "{$pfx}_") === 0) {
                if (strpos($v, ',')) {
                    list($fld, $weight) = explode(',', $v);
                    $fld       = trim($fld);
                    $_fn[$fld] = (int) $weight;
                }
                else {
                    if (strpos($v, '_title') || strpos($v, '_name')) {
                        $_fn[$v] = 3;
                    }
                    elseif (strpos($v, '_tags')) {
                        $_fn[$v] = 2;
                    }
                    else {
                        $_fn[$v] = 1;
                    }
                }
            }
        }
    }

    // Now see which ones exist
    $_fl = array();
    $_rt = jrCore_db_get_unique_keys($module);
    if ($_rt && is_array($_rt)) {
        $_rt = array_flip($_rt);
        foreach ($_fn as $fld => $weight) {
            if (isset($_rt[$fld]) && !isset($_fl[$fld])) {
                $_fl[$fld] = $weight;
            }
        }
    }

    return (count($_fl) > 0) ? $_fl : false;
}

/**
 * Get our index text from an item and fields
 * @param $_item array Item Array
 * @param $_fields array fields to get text for
 * @return string
 */
function jrSearch_get_index_text_from_item($_item, $_fields)
{
    $txt = '';
    foreach ($_fields as $fld) {
        if (strpos($fld, '_title') || strpos($fld, '_name')) {
            continue;
        }
        elseif (isset($_item[$fld])) {
            if (strpos($fld, '_tags')) {
                $txt .= trim(str_replace(',', ' ', $_item[$fld])) . ' ';
            }
            else {
                $txt .= strip_tags($_item[$fld]) . ' ';
            }
        }
    }
    return rtrim($txt);
}

/**
 * Get our index TITLE from an item and fields
 * @param $_item array Item Array
 * @return string
 */
function jrSearch_get_index_title_from_item($_item)
{
    foreach ($_item as $fld => $val) {
        if (strpos($fld, '_title')) {
            return $val;
        }
        elseif (strpos($fld, '_name')) {
            return $val;
        }
    }
    return false;
}

/**
 * Get DataStore modules we can enable FULL TEXT searching on
 * @return mixed
 */
function jrSearch_get_search_modules()
{
    global $_mods;
    $_ot = array();
    foreach ($_mods as $mod => $_inf) {
        if (jrCore_is_datastore_module($mod) && !jrSearch_is_excluded_module($mod) && is_file(APP_DIR . "/modules/{$mod}/templates/item_list.tpl")) {
            $_ot[$mod] = $_inf['module_name'];
        }
    }
    if (count($_ot) > 0) {
        natcasesort($_ot);
        return $_ot;
    }
    return false;
}

//------------------------
// CHECKTYPE
//------------------------

/**
 * jrCore_checktype_searchable_field
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrSearch_checktype_searchable_field($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return "a valid date datastore key";
    }
    if ($type_only) {
        return 'string';
    }
    $each = explode("\n", str_replace("\r", "", $input));
    if (is_array($each)) {
        $_ds_mods = jrCore_get_datastore_modules();
        foreach ($each as $field) {
            if (strlen($field) == 0) {
                continue;
            }
            list($pfx,) = explode('_', $field, 2);
            if (strlen($pfx) > 0) {
                if (in_array($pfx, $_ds_mods)) {
                    continue;
                }
                return false;
            }
        }
    }
    return true;
}

//------------------------
// SMARTY FUNCTIONS
//------------------------

/**
 * Build a search form
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrSearch_form($params, $smarty)
{
    global $_conf;

    // In: module="ModuleName" or module="all" for a global search (default: all)
    // In: page (default:1)
    // In: pagebreak (default:10)
    // In: template (default: html_search_form.tpl)
    // In: class (optional)
    // In: style (optional)
    // In: assign (optional)

    if (!jrCore_module_is_active('jrSearch')) {
        return '';
    }

    // Check the incoming parameters
    if (empty($params['module'])) {
        $params['module'] = 'all';
    }
    if (!isset($params['page']) || !jrCore_checktype($params['page'], 'number_nz')) {
        $params['page'] = 1;
    }

    if (!isset($params['pagebreak']) || !jrCore_checktype($params['pagebreak'], 'number_nz')) {
        $params['pagebreak'] = (isset($_conf['jrSearch_index_limit'])) ? intval($_conf['jrSearch_index_limit']) : 4;
    }

    if (!isset($params['value'])) {
        $_lang           = jrUser_load_lang_strings();
        $params['value'] = $_lang['jrSearch'][1];
    }

    if (empty($params['style'])) {
        $params['style'] = '';
    }

    if (empty($params['class'])) {
        $params['class'] = '';
    }

    if (!empty($params['template'])) {
        $params['tpl_dir'] = $_conf['jrCore_active_skin'];
    }
    else {
        $params['template'] = 'html_search_form.tpl';
        $params['tpl_dir']  = 'jrSearch';
    }
    if (!isset($params['method'])) {
        $params['method'] = 'get';
    }
    $_tmp = array();
    foreach ($params as $k => $v) {
        $_tmp['jrSearch'][$k] = $v;
    }

    // Call the appropriate template and return
    $out = jrCore_parse_template($params['template'], $_tmp, $params['tpl_dir']);
    if (isset($params['assign']) && strlen($params['assign']) > 0) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * jrSearch_recent
 * Show most recent searches
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrSearch_recent($params, $smarty)
{
    global $_conf;
    if (!jrCore_module_is_active('jrSearch')) {
        return '';
    }
    // Check the incoming parameters
    $_s = array();
    if (isset($params['user_id']) && jrCore_checktype($params['user_id'], 'number_nz')) {
        $_s[] = "_user_id = {$params['user_id']}";
    }
    if (isset($params['module']) && $params['module'] != '') {
        $_s[] = "search_module = {$params['module']}";
    }
    if (!isset($params['limit']) || jrCore_checktype($params['limit'], 'number_nz') || $params['limit'] > 100) {
        $params['limit'] = 5;
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
        $params['template'] = "search_recent.tpl";
        $params['tpl_dir']  = 'jrSearch';
    }

    $_tmp = array();
    foreach ($params as $k => $v) {
        $_tmp['jrSearch'][$k] = $v;
    }

    // Get most recent
    $_s  = array(
        'search'        => $_s,
        'order_by'      => array("_created" => "desc"),
        'return_keys'   => array('search_module', 'search_string'),
        'skip_triggers' => true,
        'limit'         => $params['limit']
    );
    $_rt = jrCore_db_search_items('jrSearch', $_s);
    if ($_rt && is_array($_rt) && is_array($_rt['_items'])) {
        foreach ($_rt['_items'] as $k => $rt) {
            $_tmp['jrSearchRecent'][$k]['module'] = $rt['search_module'];
            $_tmp['jrSearchRecent'][$k]['string'] = jrCore_entity_string($rt['search_string']);
        }
    }

    $out = jrCore_parse_template($params['template'], $_tmp, $params['tpl_dir']);
    if (isset($params['assign']) && $params['assign'] != '') {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Show most popular searches
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrSearch_popular($params, $smarty)
{
    global $_conf;
    if (!jrCore_module_is_active('jrSearch')) {
        return '';
    }
    // Check the incoming parameters
    if (!isset($params['limit']) || !jrCore_checktype($params['limit'], 'number_nz') || $params['limit'] > 100) {
        $params['limit'] = 5;
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
        $params['template'] = "search_popular.tpl";
        $params['tpl_dir']  = 'jrSearch';
    }
    $_tmp = array();
    foreach ($params as $k => $v) {
        $_tmp['jrSearch'][$k] = $v;
    }

    // Get most popular
    $_rt = jrCore_db_run_key_function('jrSearch', 'search_string', '%', 'COUNT', true);
    if ($_rt && is_array($_rt)) {
        $_id = array();
        $_ct = array();
        foreach ($_rt as $k => $_item) {
            $_id["{$_item['value']}"] = (int) $_item['_item_id'];
            $_ct["{$_item['value']}"] = (int) $_item['cnt'];
        }
        $_it = jrCore_db_get_multiple_items('jrSearch', $_id);
        if ($_it && is_array($_it)) {
            $_tmp['jrSearchPopular'] = array();
            foreach ($_it as $k => $_item) {
                $_tmp['jrSearchPopular'][$k]['string'] = jrCore_entity_string($_item['search_string']);
                $_tmp['jrSearchPopular'][$k]['count']  = (int) $_ct["{$_item['search_string']}"];
                $_tmp['jrSearchPopular'][$k]['module'] = $_item['search_module'];
            }
        }
    }

    $out = jrCore_parse_template($params['template'], $_tmp, $params['tpl_dir']);
    if (isset($params['assign']) && strlen($params['assign']) > 0) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Show search area for a module index
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrSearch_module_form($params, $smarty)
{
    global $_urls, $_post;
    if (!isset($params['module'])) {
        if (isset($_post['module']) && jrCore_module_is_active($_post['module'])) {
            $params['module'] = $_post['module'];
        }
        elseif (isset($_urls["{$_post['option']}"])) {
            $params['module'] = $_urls["{$_post['option']}"];
        }
        if (!isset($params['module'])) {
            jrCore_smarty_missing_error('module');
        }
    }
    if (jrProfile_is_profile_view()) {
        if ($_pr = jrCore_get_flag('jrprofile_active_profile_data')) {
            if (!isset($_pr["profile_{$params['module']}_item_count"]) || $_pr["profile_{$params['module']}_item_count"] === 0) {
                // No items = no searching
                return '';
            }
        }
    }
    if (!isset($params['template'])) {
        $params['template'] = 'search_module_form.tpl';
    }
    if (!isset($params['fields']) || strlen($params['fields']) === 0) {
        $params['fields'] = 'all';
    }

    // We can get additional params for our jrCore_db_search_items
    $_args = array();
    foreach ($params as $k => $v) {
        // Search
        if (strpos($k, 'search') === 0 && $k != 'search_url') {
            $_args[] = $v;
        }
    }
    if (count($_args) > 0) {
        $_SESSION['jrSearch_search_params'] = $_args;
    }

    // See if we are on a SITE module index (index.tpl) OR
    // on a profile module index (item_index.tpl)
    // and a specific search URL has not been passed in.
    if (isset($_post['module_url']) && !isset($params['search_url'])) {
        if (isset($_post['module_url']) && !isset($_urls["{$_post['module_url']}"])) {
            // We're on a profile...
            $params['search_url'] = "{$_post['module_url']}/{$_post['option']}";
        }
        else {
            $params['search_url'] = $_post['module_url'];
            // See if we have additional options
            $_check = array('option', '_1', '_2');
            foreach ($_check as $chk) {
                if (isset($_post[$chk]) && strlen($_post[$chk]) > 0) {
                    $params['search_url'] .= "/{$_post[$chk]}";
                }
            }
        }
    }
    $out = jrCore_parse_template($params['template'], $params, 'jrSearch');
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}
