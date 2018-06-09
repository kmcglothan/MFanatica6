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

//------------------------------
// rate_item (Magic View)
//------------------------------
function jrRating_rate_item($_post, $_user, $_conf)
{
    // URL is like /module_url/rate/item_id/value/index
    jrCore_validate_location_url();

    // See if we are requiring login
    $_ln = jrUser_load_lang_strings();
    if (isset($_conf['jrRating_require_login']) && $_conf['jrRating_require_login'] == 'on' && !jrUser_is_logged_in()) {
        jrCore_set_form_notice('error', $_ln['jrRating'][18]);
        return json_encode(array('error' => 'login'));
    }
    // Check quota
    $allowed = jrUser_get_profile_home_key('quota_jrRating_allowed');
    if (jrUser_is_logged_in() && (!$allowed || $allowed != 'on')) {
        return json_encode(array('error' => 'User not allowed to rate based on quota'));
    }
    // Check the module being rated exists and is enabled
    if (!jrCore_module_is_active('jrRating')) {
        return json_encode(array('error' => 'Module being rated not enabled or invalid'));
    }
    // Check item_id
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        return json_encode(array('error' => 'Invalid item_id'));
    }
    // Check rating value
    if (!isset($_post['_2']) || !jrCore_checktype($_post['_2'], 'number_nz') || $_post['_2'] < 1 || $_post['_2'] > 5) {
        return json_encode(array('error' => 'Invalid rating value'));
    }
    // Make sure rated item is valid
    $_it = jrCore_db_get_item($_post['module'], $_post['_1'], false, true);
    if (!$_it || !is_array($_it)) {
        return json_encode(array('error' => 'Item being rated does not exist'));
    }
    // Check the index
    if (!jrCore_checktype($_post['_3'], 'number_nn')) {
        $_post['_3'] = 1;
    }

    // See if the user has already rated this item
    $pfx = jrCore_db_get_prefix($_post['module']);
    $uip = jrCore_get_ip();
    $_sp = array(
        'search'         => array(
            "rating_item_ckey = {$_post['_1']}:{$_post['_3']}:{$_post['module']}"
        ),
        'skip_triggers'  => true,
        'privacy_check'  => false,
        'return_keys'    => array('_item_id', '_created', 'rating_value'),
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 1
    );

    // If user is logged in, search by user_id - logged out, ip
    if (jrUser_is_logged_in()) {
        $_sp['search'][] = "_user_id = {$_user['_user_id']}";
    }
    else {
        $_sp['search'][] = "rating_ip = {$uip}";
    }

    $_rt = jrCore_db_search_items('jrRating', $_sp);
    $_rt = ($_rt && is_array($_rt) && isset($_rt['_items'][0])) ? $_rt['_items'][0] : false;
    $url = false;

    $_dt = array(
        'module' => $_post['module'],
        'rating' => $_post['_2'],
        '_item'  => $_it
    );
    $_dt = jrCore_trigger_event('jrRating', 'rate_item', $_dt, $_rt);
    if (isset($_dt['error']) && strlen($_dt['error']) > 0) {
        // We encountered an error in a listener
        return json_encode(array('error' => $_dt['error']));
    }

    if ($_rt && is_array($_rt)) {
        // User has already rated this item - see if we are locked
        if (isset($_conf['jrRating_re-rate_timeout']) && jrCore_checktype($_conf['jrRating_re-rate_timeout'], 'number_nz')) {
            // Looks like we are blocking re-rating after X number of seconds
            if (intval($_rt['_created']) < (time() - $_conf['jrRating_re-rate_timeout'])) {
                // Cannot re-rate at this time
                return json_encode(array('error' => 'You cannot re-rate this item'));
            }
        }
        jrCore_db_update_item('jrRating', $_rt['_item_id'], array('rating_value' => $_post['_2']));

        // Does the rated item have an image?
        if (!isset($_it["{$pfx}_image_size"])) {
            jrCore_db_delete_item_key('jrRating', $_rt['_item_id'], "{$pfx}_image_size");
        }
    }
    else {
        // See what we are using for a title
        switch ($_post['module']) {
            case 'jrProfile':
                $ttl = $_it['profile_name'];
                $url = "{$_conf['jrCore_base_url']}/{$_it['profile_url']}";
                break;
            case 'jrUser':
                $ttl = $_it['user_name'];
                $url = "{$_conf['jrCore_base_url']}/{$_it['profile_url']}";
                break;
            case 'jrAction':
                $ttl = '';
                if (isset($_it["{$pfx}_text"])) {
                    $txt = strip_tags($_it["{$pfx}_text"]);
                    if (strlen($txt) > 60) {
                        $txt = substr($txt, 0, 60) . '...';
                    }
                    $ttl = "{$_ln['jrRating'][15]}: {$txt}";
                }
                elseif (isset($_it["{$pfx}_item"])) {
                    $ipfx = jrCore_db_get_prefix($_it["{$pfx}_module"]);
                    $ttl  = $_it["{$pfx}_item"]["{$ipfx}_title"];
                }
                $url = "{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$_post['module_url']}/{$_post['_1']}";
                break;
            case 'jrComment':
                $ttl = strip_tags($_it["{$pfx}_text"]);
                if (strlen($ttl) > 60) {
                    $ttl = substr($ttl, 0, 60) . '...';
                }
                $url = "{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$_post['module_url']}/{$_post['_1']}";
                break;
            default:
                $ttl = $_it["{$pfx}_title"];
                $url = "{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$_post['module_url']}/{$_post['_1']}";
                break;
        }
        // Create new rating
        $_info  = array(
            'rating_ip'          => $uip,
            'rating_item_ckey'   => "{$_post['_1']}:{$_post['_3']}:{$_post['module']}",
            'rating_module'      => $_post['module'],
            'rating_item_id'     => $_post['_1'],
            'rating_value'       => $_post['_2'],
            'rating_index'       => $_post['_3'],
            'rating_profile_id'  => $_it['_profile_id'],
            'rating_profile_url' => $_it['profile_url'],
            'rating_title'       => $ttl
        );
        $_info2 = $_info;

        // Does the rated item have an image?
        if (isset($_it["{$pfx}_image_size"]) && jrCore_checktype($_it["{$pfx}_image_size"], 'number_nz')) {
            $_info['rating_image_size'] = $_it["{$pfx}_image_size"];
        }

        // Ratings are always saved to the rated item profile id
        if (jrUser_is_logged_in()) {
            $_core = array(
                '_profile_id' => jrUser_get_profile_home_key('_profile_id')
            );
        }
        else {
            $_core = array(
                '_user_id'    => 0,
                '_profile_id' => 0
            );
        }
        $rid = jrCore_db_create_item('jrRating', $_info, $_core);
        if (!jrCore_checktype($rid, 'number_nz')) {
            return json_encode(array('error' => 'System error when rating the item'));
        }

        // Add to Actions...
        if (jrUser_is_logged_in() && $_conf['jrRating_allow_actions'] == 'on') {
            $_rt['action_original_module']  = $_post['module'];
            $_rt['action_original_item_id'] = (int) $_post['_1'];
            $_rt['quota_jrAction_allowed']  = (isset($_user['quota_jrAction_allowed'])) ? $_user['quota_jrAction_allowed'] : false;
            jrCore_run_module_function('jrAction_save', 'rate', 'jrRating', $rid, $_rt, false, $_core['_profile_id'], $_it['_profile_id']);
        }

    }

    $key = "{$pfx}_rating_{$_post['_3']}";
    if (!isset($_it["{$key}_count"])) {
        // First time this item has been rated
        $_data = array(
            "{$key}_1"                            => 0,
            "{$key}_2"                            => 0,
            "{$key}_3"                            => 0,
            "{$key}_4"                            => 0,
            "{$key}_5"                            => 0,
            "{$key}_{$_post['_2']}"               => 1,
            "{$key}_average_count"                => $_post['_2'],
            "{$key}_count"                        => 1,
            "{$pfx}_rating_overall_average_count" => $_post['_2'],
            "{$pfx}_rating_overall_count"         => 1
        );
        if (!jrCore_db_update_item($_post['module'], $_it['_item_id'], $_data)) {
            return json_encode(array('error' => 'a system error was encountered saving the rating - please try again'));
        }
    }
    else {
        // We already have ratings in the DB
        // Increment the number of ratings for our selected value
        jrCore_db_increment_key($_post['module'], $_it['_item_id'], "{$key}_{$_post['_2']}", 1);

        // If this is a NEW rating from a NEW user, we increment our rating counters
        if (!$_rt) {
            // Fresh rating - increment overall counter as well
            jrCore_db_increment_key($_post['module'], $_it['_item_id'], "{$key}_count", 1);
            jrCore_db_increment_key($_post['module'], $_it['_item_id'], "{$pfx}_rating_overall_count", 1);
        }
        else {
            // Decrement the rating count for our OLD rating
            jrCore_db_decrement_key($_post['module'], $_it['_item_id'], "{$key}_{$_rt['rating_value']}", 1);
        }

        // Overall average for specific index
        $tbl = jrCore_db_table_name($_post['module'], 'item_key');
        // profile_rating_1_1
        $len = (strlen($pfx) + 11);
        $req = "UPDATE {$tbl} SET `value` = ( SELECT val FROM (
                    SELECT ROUND(SUM(SUBSTR(`key`, {$len}) * `value`) / SUM(`value`), 2) AS val FROM {$tbl} WHERE `_item_id` = '{$_it['_item_id']}' AND `key` IN('{$key}_1','{$key}_2','{$key}_3','{$key}_4','{$key}_5')
                ) AS v ) WHERE `_item_id` = '{$_it['_item_id']}' AND `key` = '{$key}_average_count' LIMIT 1";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (!$cnt || $cnt !== 1) {
            return json_encode(array('error' => 'a system error was encountered saving the rating - please try again'));
        }

        // Overall average for entire item
        $req = "UPDATE {$tbl} SET `value` = ( SELECT val FROM (
                    SELECT ROUND(SUM(SUBSTR(`key`, {$len}) * `value`) / SUM(`value`), 2) AS val FROM {$tbl} WHERE `_item_id` = '{$_it['_item_id']}' AND `key` LIKE '{$pfx}_rating_%' AND `key` NOT LIKE '%_count'
                ) AS v ) WHERE `_item_id` = '{$_it['_item_id']}' AND `key` = '{$pfx}_rating_overall_average_count' LIMIT 1";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (!$cnt || $cnt !== 1) {
            return json_encode(array('error' => 'a system error was encountered saving the rating - please try again'));
        }
        $_data = jrCore_db_get_item($_post['module'], $_it['_item_id'], true, true);
    }

    // Increment number of ratings for profile - as long as we are not rating our own item
    if (jrUser_get_profile_home_key('_profile_id') != $_it['_profile_id']) {
        jrCore_db_increment_key('jrProfile', $_it['_profile_id'], 'profile_jrRating_home_item_count', 1);
    }

    // Next, check on user notifications - we're going to notify users
    // if this is a rating on an item they have created - but only
    // if the rating is not BY us on our own profile
    if (isset($_info2) && jrUser_get_profile_home_key('_profile_id') != $_it['_profile_id']) {
        $_owners = jrProfile_get_owner_info($_it['_profile_id']);
        if (isset($_owners) && is_array($_owners)) {
            $_info2['system_name']      = $_conf['jrCore_system_name'];
            $_info2['rating_user_name'] = (isset($_user['user_name'])) ? $_user['user_name'] : $_ln['jrRating'][14];
            $_info2['rating_url']       = $url;
            list($sub, $msg) = jrCore_parse_email_templates('jrRating', 'new_rating', $_info2);
            foreach ($_owners as $_o) {
                // NOTE: "0" is from_user_id - 0 is the "system user"
                if ($_o['_user_id'] != $_user['_user_id']) {
                    jrUser_notify($_o['_user_id'], 0, 'jrRating', 'new_rating', $sub, $msg);
                }
            }
        }
    }

    // Success
    jrProfile_reset_cache($_it['_profile_id']);
    return json_encode(array('OK' => 1, 'rating_average' => $_data["{$key}_average_count"], 'rating_count' => $_data["{$key}_count"]));
}

