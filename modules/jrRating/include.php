<?php
/**
 * Jamroom Item Ratings module
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
 * Module meta data
 * @return array
 */
function jrRating_meta()
{
    $_tmp = array(
        'name'        => 'Item Ratings',
        'url'         => 'rating',
        'version'     => '1.4.2',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Ratings for all datastore based items',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/287/item-ratings',
        'category'    => 'item features',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * Initialize module
 * @return bool
 */
function jrRating_init()
{
    // Pulse Key support
    jrCore_register_module_feature('jrProfile', 'pulse_key', 'jrRating', 'profile_jrRating_home_item_count', 'ratings');

    // Core support
    $_tmp = array(
        'label' => 'Allowed to Rate',
        'help'  => 'If checked, User Accounts associated with Profiles in this quota will be allowed to rate items.'
    );
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrRating', 'on', $_tmp);

    // notifications
    $_tmp = array(
        'label' => 12, // 12 = 'Item rated'
        'help'  => 13  // 13 = 'If one of your items is rated, would you like to be notified?'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrRating', 'new_rating', $_tmp);

    // Register our custom JS and CSS
    jrCore_register_module_feature('jrCore', 'javascript', 'jrRating', 'jrRating.js');
    jrCore_register_module_feature('jrCore', 'css', 'jrRating', 'jrRating.css');

    // Our rating module provides the "rate" magic view
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrRating', 'rate', 'jrRating_rate_item');

    // Add in overall rating average and number listeners
    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrRating_db_search_params_listener');
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrRating_db_search_items_listener');
    jrCore_register_event_listener('jrCore', 'db_delete_item', 'jrRating_db_delete_item_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrRating_verify_module_listener');

    // Register our rate_item event listener
    jrCore_register_event_trigger('jrRating', 'rate_item', 'Fired when a new rating is going to be saved');

    // Support for actions
    jrCore_register_module_feature('jrCore', 'action_support', 'jrRating', 'rate', 'item_action.tpl');

    // Verify DB queue worker
    jrCore_register_queue_worker('jrRating', 'verify_db', 'jrRating_verify_db_worker', 0, 1, 14400);

    return true;
}

//---------------------
// QUEUE WORKER
//---------------------

/**
 * Verify Rating Database
 * @param array $_queue The queue entry the worker will receive
 * @return bool
 */
function jrRating_verify_db_worker($_queue)
{
    ini_set('max_execution_time', 28800); // 8 hours max
    $max = 0;
    $cnt = 0;
    while (true) {
        $max++;
        $_rt = jrCore_db_get_items_missing_key('jrRating', 'rating_item_ckey', 2000);
        if ($_rt && is_array($_rt)) {
            $tot = count($_rt);
            $_id = array();
            foreach ($_rt as $k => $id) {
                $_id[] = $id;
                $cnt++;
                if (($cnt % 200) === 0 || ($k + 1) >= $tot) {
                    $_rg = jrCore_db_get_multiple_items('jrRating', $_id, null, true);
                    if ($_rg && is_array($_rg)) {
                        $_up = array();
                        foreach ($_rg as $r) {
                            $iid       = (int) $r['_item_id'];
                            $_up[$iid] = array(
                                'rating_item_ckey' => "{$r['rating_item_id']}:{$r['rating_index']}:{$r['rating_module']}"
                            );
                        }
                        if (count($_up) > 0) {
                            jrCore_db_update_multiple_items('jrRating', $_up, null, false, false);
                            $_id = array();
                        }
                    }
                }
            }
        }
        else {
            if ($cnt > 0) {
                jrCore_logger('INF', "added rating_item_ckey compound key to " . jrCore_number_format($cnt) . " ratings");
            }
            break;
        }
        if ($max > 1000) {
            // fail safe - break out
            jrCore_logger('CRI', "failsafe hit adding rating_item_ckey compound key to ratings");
            break;
        }
    }
    return true;
}

//---------------------
// EVENT LISTENERS
//---------------------

/**
 * Special Order By support
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrRating_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    // Bayesian Rating
    if (isset($_data['order_by']) && stripos(json_encode($_data['order_by']), 'bayesian_rating')) {
        // br = ( (avg_num_votes * avg_rating) + (this_num_votes * this_rating) ) / (avg_num_votes + this_num_votes)
        $pfx = jrCore_db_get_prefix($_args['module']);
        if ($pfx) {
            $tbl = jrCore_db_table_name($_args['module'], 'item_key');
            $req = "SELECT COUNT(`_item_id`) AS item_num, SUM(`value`) AS vote_num FROM {$tbl} WHERE `key` = '{$pfx}_rating_overall_count'";
            $_rt = jrCore_db_query($req, 'SINGLE');
            if ($_rt && is_array($_rt)) {

                $i_count = (int) $_rt['item_num'];
                $v_count = (int) $_rt['vote_num'];
                $a_votes = round(($v_count / $i_count), 2);

                $req = "SELECT AVG(`value`) AS avg_rating FROM {$tbl} WHERE `key` = '{$pfx}_rating_overall_average_count'";
                $_rt = jrCore_db_query($req, 'SINGLE');
                if ($_rt && is_array($_rt)) {

                    $average = $_rt['avg_rating'];

                    // Now we run the input query so any additional modifiers can be taken into account
                    $_sp = false;
                    if (isset($_data['search'])) {
                        $_sp = array(
                            'search'              => $_data['search'],
                            'skip_triggers'       => true,
                            'return_item_id_only' => true,
                            'limit'               => 10000
                        );
                        $_sp = jrCore_db_search_items($_args['module'], $_sp);
                    }

                    // Pagebreak / Limit
                    $add = ' LIMIT 10';
                    if (isset($_data['limit'])) {
                        $add = " LIMIT " . (int) $_data['limit'];
                    }
                    elseif (isset($_data['pagebreak'])) {
                        if (!isset($_data['page']) || !jrCore_checktype($_data['page'], 'number_nz')) {
                            $_data['page'] = 1;
                        }
                        $add = " LIMIT " . (($_data['page'] - 1) * $_data['page_break']) . ",{$_data['pagebreak']}";
                    }
                    $dir = 'DESC';
                    foreach ($_data['order_by'] as $k => $v) {
                        if ($k == 'bayesian_rating') {
                            switch (strtolower($v)) {
                                case 'asc':
                                case 'numerical_asc':
                                    $dir = 'ASC';
                                    break 2;
                            }
                        }
                    }
                    $req = "SELECT a.`_item_id`, ROUND(((({$a_votes} * {$average}) + (a.`value` * b.`value`)) / ({$a_votes} + a.`value`)), 2) AS br
                              FROM {$tbl} a
                         LEFT JOIN {$tbl} b ON (b.`_item_id` = a.`_item_id` AND b.`key` = '{$pfx}_rating_overall_average_count')
                             WHERE a.`key` = '{$pfx}_rating_overall_count'";
                    if (is_array($_sp)) {
                        $req .= " AND a.`_item_id` IN(" . implode(',', $_sp) . ")";
                    }
                    $req .= " ORDER BY br {$dir}{$add}";
                    $_rt = jrCore_db_query($req, '_item_id', false, 'br');
                    if ($_rt && is_array($_rt)) {
                        $_data['search'] = array(
                            "_item_id in " . implode(',', array_keys($_rt))
                        );
                        unset($_data['order_by']);
                        jrCore_set_flag('jrrating_bayesian_ratings', $_rt);
                    }
                }
            }
        }
    }

    // On a rating list check if we only return items for modules that are active
    if ($_args['module'] == 'jrRating' && isset($_conf['jrRating_check_modules']) && $_conf['jrRating_check_modules'] == 'on') {
        // See if we are already searching for a specific module...
        if (isset($_data['search'])) {
            foreach ($_data['search'] as $k => $v) {
                if (strpos(' ' . $v, 'rating_module')) {
                    // We are already specifying a rating_module - no need to add to it
                    return $_data;
                }
            }
        }
        else {
            $_data['search'] = array();
        }
        if ($_tmp = jrCore_get_datastore_modules()) {
            $_mod = array();
            foreach ($_tmp as $mod => $pfx) {
                if (jrCore_module_is_active($mod)) {
                    $_mod[] = $mod;
                }
            }
            if (count($_mod) > 0) {
                $_data['search'][] = 'rating_module in ' . implode(',', $_mod);
            }
        }
    }
    return $_data;
}

/**
 * Get item info when a rating list is requested
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrRating_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] != 'jrRating') {
        // See if we have bayesian ratings to add in...
        if ($_rt = jrCore_get_flag('jrrating_bayesian_ratings')) {
            $pfx = jrCore_db_get_prefix($_args['module']);
            foreach ($_data['_items'] as $k => $_item) {
                if (isset($_rt["{$_item['_item_id']}"])) {
                    $_data['_items'][$k]["{$pfx}_rating_bayesian_average"] = $_rt["{$_item['_item_id']}"];
                }
            }
            jrCore_delete_flag('jrrating_bayesian_ratings');
        }
        return $_data;
    }

    // Fall through - we are doing a rating list
    $_ids = array();
    $_lnk = array();
    foreach ($_data['_items'] as $k => $_item) {
        if (!isset($_item['rating_module'])) {
            continue;
        }
        if (!isset($_ids["{$_item['rating_module']}"])) {
            $_ids["{$_item['rating_module']}"] = array();
        }
        $_ids["{$_item['rating_module']}"][]                             = $_item['rating_item_id'];
        $_lnk["{$_item['rating_module']}"]["{$_item['rating_item_id']}"] = $k;
    }
    if (isset($_ids) && count($_ids) > 0) {
        foreach ($_ids as $mod => $_uniq) {
            // Fire off our query to get item, user and profile info
            $_sp = array(
                'search'                       => array(
                    '_item_id in ' . implode(',', $_uniq)
                ),
                'exclude_jrProfile_quota_keys' => true,
                'ignore_pending'               => true,
                'privacy_check'                => false // This was already run on the initial search
            );
            $_it = jrCore_db_search_items($mod, $_sp);
            if (isset($_it) && is_array($_it['_items'])) {
                foreach ($_it['_items'] as $v) {
                    switch ($mod) {
                        case 'jrProfile':
                            $iid = $_lnk[$mod]["{$v['_profile_id']}"];
                            break;
                        case 'jrUser':
                            $iid = $_lnk[$mod]["{$v['_user_id']}"];
                            break;
                        default:
                            $iid = $_lnk[$mod]["{$v['_item_id']}"];
                            break;
                    }
                    $_data['_items'][$iid]['rating_data'] = $v;
                }
                unset($_it);
            }
        }
    }
    unset($_ids, $_lnk);
    return $_data;
}

/**
 * Delete rating entries when DS items are deleted
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrRating_db_delete_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['_item_id']) && is_numeric($_args['_item_id']) && isset($_args['module']) && $_args['module'] != 'jrRating') {
        while (true) {
            // Do a thousand at a time - lower memory usage
            $_sp = array(
                "search"              => array(
                    "rating_module = {$_args['module']}",
                    "rating_item_id = {$_args['_item_id']}"
                ),
                'return_item_id_only' => true,
                'skip_triggers'       => true,
                'ignore_pending'      => true,
                'privacy_check'       => false,
                'limit'               => 1000
            );
            $_rt = jrCore_db_search_items('jrRating', $_sp);
            if ($_rt && is_array($_rt)) {
                // NOTE: Since rating entries have no media, we set the 3rd param to "false" - this
                // let's the delete function skip checking for associated item media.
                jrCore_db_delete_multiple_items('jrRating', $_rt, false);
                if (count($_rt) < 1000) {
                    // We got them all
                    break;
                }
            }
            else {
                break;
            }
        }
    }
    return $_data;
}

/**
 * Add rating compound key
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrRating_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    $num = jrCore_db_get_datastore_item_count('jrRating');
    if ($num > 0) {
        jrCore_queue_create('jrRating', 'verify_db', array('count' => $num), 0, null, 1);
    }
    return $_data;
}

//---------------------
// FUNCTIONS
//---------------------

/**
 * Return a list of available rating types
 * @return array
 */
function jrRating_get_types()
{
    return array(
        'star' => 'star',
        'html' => 'html'
    );
}

/**
 * Return DOM targets for rating results
 * @return array
 */
function jrRating_get_targets()
{
    return array(
        'alert' => 'alert',
        'div'   => 'div'
    );
}

/**
 * Get a list of modules that have an item_list.tpl file
 * @return array
 */
function jrRating_get_ratable_modules()
{
    global $_mods;
    $_out = array();
    foreach ($_mods as $k => $_v) {
        $item_list = APP_DIR . "/modules/{$k}/templates/item_list.tpl";
        if (jrCore_module_is_active($_v['module_directory']) && is_file($item_list)) {
            $_out[$k] = $k;
        }
    }
    return $_out;
}

//---------------------
// SMARTY
//---------------------

/**
 * Smarty function to return a rating form
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrRating_form($params, $smarty)
{
    global $_user, $_conf;

    // Is jrRating module enabled?
    if (!jrCore_module_is_active('jrRating')) {
        return '';
    }
    // Is rating target module enabled?
    if (!jrCore_module_is_active($params['module'])) {
        return '';
    }
    // Is it allowed in this quota?
    if (!jrProfile_is_allowed_by_quota('jrRating', $smarty)) {
        return '';
    }
    $params['module_url'] = jrCore_get_module_url($params['module']);

    // Check the incoming parameters
    if (!jrCore_checktype($params['item_id'], 'number_nz')) {
        return 'jrRating_form: item_id not set correctly';
    }
    if (!isset($params['index']) || !jrCore_checktype($params['index'], 'number_nn')) {
        $params['index'] = 1;
    }

    $_type = jrRating_get_types();
    if (!isset($params['type'])) {
        $params['type'] = 'star';
    }
    if (!isset($_type["{$params['type']}"])) {
        return 'jrRating_form: invalid rating type';
    }

    // Get item
    $_item = jrCore_db_get_item($params['module'], $params['item_id']);

    // Rate or Norate this item?
    if (jrUser_is_admin() && $_user['quota_jrLike_allow_self_likings'] == 'off' && $_item['_user_id'] != $_user['_user_id']) {
        $params['norate'] = 0;
    }
    elseif (!jrUser_is_logged_in() && isset($_conf['jrRating_require_login']) && $_conf['jrRating_require_login'] == 'on') {
        $params['norate'] = 1;
    }
    elseif (isset($params['norate']) || (isset($_user['quota_jrRating_allowed']) && $_user['quota_jrRating_allowed'] != 'on')) {
        $params['norate'] = 1;
    }
    elseif (jrUser_is_logged_in() && $_user['quota_jrRating_allow_self_ratings'] == 'off' && jrCore_checktype($_user['user_active_profile_id'], 'number_nz') && $_user['user_active_profile_id'] == $_item['_profile_id']) {
        $params['norate'] = 1;
    }
    else {
        $params['norate'] = 0;
    }

    $_target = jrRating_get_targets();
    if (!isset($params['target'])) {
        $params['target'] = 'div';
    }
    if (!isset($_target["{$params['target']}"])) {
        return 'jrRating_form: invalid rating target';
    }

    if (!isset($params['style'])) {
        $params['style'] = '';
    }

    if (!isset($params['class'])) {
        $params['class'] = '';
    }

    if (isset($params['values']) && strpos($params['values'], ';')) {
        $params['values'] = explode(';', $params['values']);
        if (count($params['values']) !== 6) {
            $params['values'] = array('-', '1', '2', '3', '4', '5');
        }
    }
    else {
        $params['values'] = array('-', '1', '2', '3', '4', '5');
    }

    // This is the WIDTH (in percent) of the STAR rating
    if (isset($params['current']) && $params['current'] >= 0 && $params['current'] <= 5) {
        $params['current'] = $params['current'] * 20;
    }
    else {
        $params['current'] = 0;
    }

    if (isset($params['template']{0}) && isset($params['tpl_dir']{0})) {
        //allow other modules to set the tpl_dir.
    }
    elseif (isset($params['template']{0})) {
        $params['tpl_dir'] = $_conf['jrCore_active_skin'];
    }
    else {
        $params['template'] = "{$params['type']}_rating_form.tpl";
        $params['tpl_dir']  = 'jrRating';
    }

    if (isset($params['include_raters']) && $params['include_raters'] != false) {
        // Get item raters
        $_s  = array(
            'search'   => array(
                "rating_item_ckey = {$params['item_id']}:{$params['index']}:{$params['module']}"
            ),
            'order_by' => array(
                '_item_id' => 'desc'
            ),
            'limit'    => 100
        );
        $_rt = jrCore_db_search_items('jrRating', $_s);
        if (isset($_rt) && is_array($_rt)) {
            $i = 0;
            foreach ($_rt['_items'] as $rt) {
                $params['raters'][$i]['rating_value'] = $rt['rating_value'];
                $params['raters'][$i]['_created']     = $rt['_created'];
                $params['raters'][$i]['_user_id']     = $rt['_user_id'];
                $params['raters'][$i]['user_name']    = $rt['user_name'];
                $params['raters'][$i]['_profile_id']  = $rt['_profile_id'];
                $params['raters'][$i]['profile_name'] = $rt['profile_name'];
                $params['raters'][$i]['profile_url']  = $rt['profile_url'];
                $i++;
            }
        }
    }

    $_tmp = array();
    foreach ($params as $k => $v) {
        $_tmp['jrRating'][$k] = $v;
    }
    $_tmp['jrRating']['html_id'] = 'r' . substr(md5(microtime()), 8, 8);

    // Call the appropriate template and return
    $out = jrCore_parse_template($params['template'], $_tmp, $params['tpl_dir']);
    if (isset($params['assign']) && $params['assign'] != '') {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}
