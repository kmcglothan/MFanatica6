<?php
/**
 * Jamroom Like It module
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

//----------------------------------
// get_like_users
//----------------------------------
function view_jrLike_get_like_users($_post, $_user, $_conf)
{
    jrCore_validate_location_url();
    if (!isset($_post['m']) || !jrCore_module_is_active($_post['m'])) {
        return 'error: invalid module';
    }
    if (!isset($_post['i']) || !jrCore_checktype($_post['i'], 'number_nz')) {
        return 'error: invalid item_id';
    }
    if (!isset($_post['t']) || ($_post['t'] != 'like' && $_post['t'] != 'dislike')) {
        return 'error: invalid type';
    }
    $mod = jrCore_db_escape($_post['m']);
    $iid = (int) $_post['i'];
    $tbl = jrCore_db_table_name('jrLike', 'likes');
    $req = "SELECT like_user_id FROM {$tbl} WHERE like_item_id = '{$iid}' AND like_module = '{$mod}' AND like_action = '{$_post['t']}' GROUP BY like_user_id ORDER BY like_created DESC";
    $_rt = jrCore_db_query($req, 'like_user_id', false, 'like_user_id');
    if ($_rt && is_array($_rt)) {
        $_ln = jrUser_load_lang_strings();
        $_sp = array(
            'search'                 => array(
                '_item_id IN ' . implode(',', $_rt)
            ),
            'include_jrProfile_keys' => true,
            'limit'                  => count($_rt)
        );
        $_rt = jrCore_db_search_items('jrUser', $_sp);
        if ($_post['t'] == 'like') {
            $_rt['_params']['action'] = $_ln['jrLike'][8];
        }
        else {
            $_rt['_params']['action'] = $_ln['jrLike'][9];
        }
        return jrCore_parse_template('likers.tpl', $_rt, 'jrLike');
    }
    return jrCore_parse_template('likers.tpl', array(), 'jrLike');
}

//------------------------------
// (dis)like an item
// $_post.module - the module being (dis)liked
// $_post._1     - the '_item_id' of the item being (dis)liked
// $_post._2     - the action (like or dislike)
//------------------------------
function jrLike_like_create($_post, $_user, $_conf)
{
    global $_mods;
    jrCore_validate_location_url();
    $_ln = jrUser_load_lang_strings();

    // Check the module being (dis)liked exists and is enabled
    if (!jrCore_module_is_active($_post['module'])) {
        return jrCore_json_response(array('error' => 'Module is inactive'));
    }

    // Check we got a valid item id
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        return jrCore_json_response(array('error' => 'Invalid item id'));
    }

    // Check we got a valid action
    if (!isset($_post['_2']) || ($_post['_2'] != 'like' && $_post['_2'] != 'dislike')) {
        return jrCore_json_response(array('error' => 'Invalid like action - must be one of like/dislike'));
    }

    // Make sure we are liking a valid item
    $_item = jrCore_db_get_item($_post['module'], $_post['_1']);
    if (!$_item || !is_array($_item)) {
        return jrCore_json_response(array('error' => 'Invalid item'));
    }

    if (jrUser_is_logged_in()) {
        // Check Quota
        $allowed  = jrUser_get_profile_home_key('quota_jrLike_allowed');
        $selflike = jrUser_get_profile_home_key('quota_jrLike_allow_self_likings');
        if ($allowed && $allowed == 'off') {
            // Is the user allowed to like their own items?
            if ($selflike && $selflike != 'on') {
                return jrCore_json_response(array('error' => $_ln['jrLike'][18]));
            }
        }
        // Are user allowed to self like?
        if (jrUser_can_edit_item($_item) && $selflike == 'off' && $_item['_user_id'] == $_user['_user_id']) {
            return jrCore_json_response(array('error' => $_ln['jrLike'][19]));
        }
        $uid = (int) $_user['_user_id'];
    }
    else {
        // Check visitor
        if (isset($_conf['jrLike_require_login']) && $_conf['jrLike_require_login'] == 'on') {
            return jrCore_json_response(array('error' => $_ln['jrLike'][20]));
        }
        $uid = jrCore_get_ip();
    }

    // Looking good - get some variables that we'll need
    $pfx = jrCore_db_get_prefix($_post['module']);

    // See if this user already has a like
    $tbl = jrCore_db_table_name('jrLike', 'likes');
    $req = "SELECT like_action FROM {$tbl} WHERE like_user_id = '{$uid}' AND like_item_id = '{$_post['_1']}' AND like_module = '" . jrCore_db_escape($_post['module']) . "' LIMIT 1";
    $_ex = jrCore_db_query($req, 'SINGLE');

    // See if the user is CHANGING their like
    $upv = $_post['_2'];
    if ($_ex && is_array($_ex) && isset($_ex['like_action'])) {
        if ($_ex['like_action'] == 'like' && $_post['_2'] == 'like') {
            $upv = 'neutral';
        }
        if ($_ex['like_action'] == 'dislike' && $_post['_2'] == 'dislike') {
            $upv = 'neutral';
        }
    }

    // Record like
    // Important: the extra jrCore_db_connect() call is here so we can get 0,1,2 back from the MySQL "COUNT" for the ON DUPLICATE KEY UPDATE
    $con = jrCore_db_connect(true, false);
    $req = "INSERT INTO {$tbl} (like_created, like_user_id, like_item_id, like_module, like_action)
            VALUES(UNIX_TIMESTAMP(), '{$uid}', '{$_post['_1']}', '" . jrCore_db_escape($_post['module']) . "', '{$upv}')
            ON DUPLICATE KEY UPDATE like_created = UNIX_TIMESTAMP(), like_action = '{$upv}'";
    $cnt = jrCore_db_query($req, 'COUNT', false, null, true, $con);
    jrCore_db_close();

    if (!is_numeric($cnt)) {

        // We encountered a DB Error
        return jrCore_json_response(array('error' => 'an error was encountered saving the like - please try again'));
    }

    elseif ($cnt === 2) {

        // Like already exists and the user has updated
        // Get the user's previous like or dislike and update accordingly
        if (isset($_ex['like_action'])) {
            $upd = false;
            $exv = false;
            if ($_ex['like_action'] == 'like') {
                if ($_post['_2'] == 'like') {
                    // User is changing their "like" to a "neutral"
                    $exv = 'like';     // Existing Value to DECREMENT
                }
                elseif ($_post['_2'] == 'dislike') {
                    // User is changing their "like" to a "dislike"
                    $exv = 'like';     // Existing Value
                    $upd = 'dislike';  // New Value
                }
            }
            elseif ($_ex['like_action'] == 'dislike') {
                if ($_post['_2'] == 'dislike') {
                    // User is changing their "dislike" to a "neutral"
                    $exv = 'dislike';
                }
                elseif ($_post['_2'] == 'like') {
                    // User is changing their "dislike" to a "like"
                    $exv = 'dislike';
                    $upd = 'like';
                }
            }
            elseif ($_ex['like_action'] == 'neutral') {
                $upd = $_post['_2'];
            }

            // We changed our like - maintain item counts
            $ppfx = jrCore_db_get_prefix('jrProfile');
            if ($upd) {
                jrCore_db_increment_key($_post['module'], $_post['_1'], "{$pfx}_{$upd}_count", 1);
                if (jrUser_get_profile_home_key('_profile_id') != $_item['_profile_id']) {
                    jrCore_db_increment_key('jrProfile', $_item['_profile_id'], "{$ppfx}_jrLike_{$upd}_home_item_count", 1);
                }
            }
            if ($exv) {
                jrCore_db_decrement_key($_post['module'], $_post['_1'], "{$pfx}_{$exv}_count", 1);
                if (jrUser_get_profile_home_key('_profile_id') != $_item['_profile_id']) {
                    jrCore_db_decrement_key('jrProfile', $_item['_profile_id'], "{$ppfx}_jrLike_{$exv}_home_item_count", 1);
                }
            }
        }
        // Fall through - we will come out at the bottom of this function for success
    }

    elseif ($cnt === 1) {

        // 1 = new row inserted - first time like or dislike
        jrCore_db_increment_key($_post['module'], $_post['_1'], "{$pfx}_{$_post['_2']}_count", 1);

        // Increment number of likes for profile
        if (jrUser_get_profile_home_key('_profile_id') != $_item['_profile_id']) {
            jrCore_db_increment_key('jrProfile', $_item['_profile_id'], "profile_jrLike_{$_post['_2']}_home_item_count", 1);
        }

        if (jrUser_is_logged_in()) {

            $ttl = false;
            $url = false;
            // Trigger event to get item Title and URL
            $_data = array(
                'item_url'   => '',
                'item_title' => ''
            );
            $_data = jrCore_trigger_event('jrLike', 'item_action_info', $_data, $_item, $_post['module']);
            if (isset($_data['item_title']) && strlen($_data['item_title']) > 0) {
                $ttl = $_data['item_title'];
            }
            if (isset($_data['item_url']) && strlen($_data['item_url']) > 0) {
                $url = $_data['item_url'];
            }
            if (!$ttl || !$url) {
                switch ($_post['module']) {
                    case 'jrProfile':
                        $ttl = $_item['profile_name'];
                        $url = "{$_conf['jrCore_base_url']}/{$_item['profile_url']}";
                        break;
                    case 'jrGuestbook':
                        $ttl = $_item['profile_name'] . ' - ' . $_ln['jrGuestbook']['menu'];
                        $url = "{$_conf['jrCore_base_url']}/{$_item['profile_url']}/{$_post['module_url']}";
                        break;
                    case 'jrUser':
                        $ttl = $_item['user_name'];
                        $url = "{$_conf['jrCore_base_url']}/{$_item['profile_url']}";
                        break;
                    case 'jrAction':
                        $ttl = '';
                        if (isset($_item["{$pfx}_text"])) {
                            $txt = strip_tags($_item["{$pfx}_text"]);
                            if (strlen($txt) > 60) {
                                $txt = substr($txt, 0, 60) . '...';
                            }
                            $ttl = "{$_ln['jrLike'][3]}: {$txt}";
                        }
                        elseif (isset($_item["{$pfx}_item"])) {
                            $ipfx = jrCore_db_get_prefix($_item["{$pfx}_module"]);
                            $ttl  = $_item["{$pfx}_item"]["{$ipfx}_title"];
                        }
                        $url = "{$_conf['jrCore_base_url']}/{$_item['profile_url']}/{$_post['module_url']}/{$_post['_1']}";
                        break;
                    case 'jrComment':
                        $ttl = strip_tags($_item["{$pfx}_text"]);
                        if (strlen($ttl) > 60) {
                            $ttl = substr($ttl, 0, 60) . '...';
                        }
                        $url = "{$_conf['jrCore_base_url']}/{$_item['profile_url']}/{$_post['module_url']}/{$_post['_1']}";
                        break;
                    case 'jrForum':
                        $furl = jrLike_get_forum_url($_item['forum_group_id']);
                        $url  = "{$_conf['jrCore_base_url']}/{$furl}";
                        $ttl  = $_item["{$pfx}_title"];
                        break;
                    default:
                        $ttl = $_item["{$pfx}_title"];
                        $url = "{$_conf['jrCore_base_url']}/{$_item['profile_url']}/{$_post['module_url']}/{$_post['_1']}/{$_item["{$pfx}_title_url"]}";
                        break;
                }
            }

            // Record Action
            if (isset($_conf['jrLike_allow_actions']) && $_conf['jrLike_allow_actions'] == 'on') {
                // Some modules we do NOT record a LIKE to the timeline for
                switch ($_post['module']) {
                    case 'jrFollower':
                        break;
                    default:
                        // We need to get to the inserted ID of what we just did
                        $tbl = jrCore_db_table_name('jrLike', 'likes');
                        $req = "SELECT * FROM {$tbl} WHERE like_user_id = '{$uid}' AND like_item_id = '{$_post['_1']}' AND like_module = '" . jrCore_db_escape($_post['module']) . "' LIMIT 1";
                        $_rt = jrCore_db_query($req, 'SINGLE');
                        if ($_rt && is_array($_rt)) {
                            $_rt['action_original_module']  = $_post['module'];
                            $_rt['action_original_item_id'] = (int) $_post['_1'];
                            jrCore_run_module_function('jrAction_save', 'like', 'jrLike', $_rt['like_id'], $_rt, false, jrUser_get_profile_home_key('_profile_id'), $_item['_profile_id']);
                        }
                }
            }

            // Notifications
            // They are not sent multiple notifications if the (dis)liker changes his/her mind and reverses the (dis)like
            // They are not sent a notification if its an item they own
            // They are not sent a notification if a non-logged in visitor has (dis)liked their item
            if (jrUser_get_profile_home_key('_profile_id') != $_item['_profile_id']) {
                // Notify
                $_owners = jrProfile_get_owner_info($_item['_profile_id']);
                if ($_owners && is_array($_owners)) {
                    $_info = array(
                        'system_name'    => $_conf['jrCore_system_name'],
                        'like_user_name' => $_user['user_name'],
                        'like_title'     => $ttl,
                        'like_url'       => $url,
                        'like_module'    => $_post['module'],
                        'like_action'    => $_post['_2']
                    );
                    list($sub, $msg) = jrCore_parse_email_templates('jrLike', 'new_like', $_info);
                    foreach ($_owners as $_o) {
                        if ($_o['_user_id'] != $_user['_user_id']) {
                            jrUser_notify($_o['_user_id'], 0, 'jrLike', 'new_like', $sub, $msg);
                        }
                    }
                }
            }
        }

        // Event trigger
        $_args = array(
            'like_item_id' => $_post['_1'],
            'like_action'  => $_post['_2'],
            'liked_item'   => $_item
        );
        jrCore_trigger_event('jrLike', 'item_liked', $_item, $_args);
    }

    // Fall through - success
    $imurl = "{$_conf['jrCore_base_url']}/" . jrCore_get_module_url('jrImage') . "/img/module/jrLike";
    if ($_post['_2'] == 'like') {
        // New like or going neutral from an existing like?
        if (isset($_ex['like_action']) && $_ex['like_action'] == 'like') {
            $l_src = "{$imurl}/like.png";
            $d_src = "{$imurl}/dislike.png";
            $l_ttl = jrCore_entity_string($_ln['jrLike'][4]);
            $d_ttl = jrCore_entity_string($_ln['jrLike'][5]);
        }
        else {
            $l_src = "{$imurl}/liked.png";
            $d_src = "{$imurl}/dislike_greyed.png";
            $l_ttl = jrCore_entity_string($_ln['jrLike'][6]);
            $d_ttl = jrCore_entity_string($_ln['jrLike'][5]);
        }
    }
    elseif ($_post['_2'] == 'dislike') {
        // New dislike or going neutral from an existing dislike?
        if (isset($_ex['like_action']) && $_ex['like_action'] == 'dislike') {
            $l_src = "{$imurl}/like.png";
            $d_src = "{$imurl}/dislike.png";
            $l_ttl = jrCore_entity_string($_ln['jrLike'][4]);
            $d_ttl = jrCore_entity_string($_ln['jrLike'][5]);
        }
        else {
            $l_src = "{$imurl}/like_greyed.png";
            $d_src = "{$imurl}/disliked.png";
            $l_ttl = jrCore_entity_string($_ln['jrLike'][4]);
            $d_ttl = jrCore_entity_string($_ln['jrLike'][7]);
        }
    }
    else {
        // Neutral
        $l_src = "{$imurl}/like.png";
        $d_src = "{$imurl}/dislike.png";
        $l_ttl = jrCore_entity_string($_ln['jrLike'][4]);
        $d_ttl = jrCore_entity_string($_ln['jrLike'][5]);
    }
    list($l_cnt, $d_cnt) = jrLike_get_like_counts($_post['module'], $_post['_1']);
    $_rs = array(
        'OK'    => 1,
        'l_src' => $l_src . "?s={$_conf['jrCore_active_skin']}&_v={$_mods['jrLike']['module_version']}",
        'l_ttl' => $l_ttl,
        'l_cnt' => $l_cnt,
        'd_src' => $d_src . "?s={$_conf['jrCore_active_skin']}&_v={$_mods['jrLike']['module_version']}",
        'd_ttl' => $d_ttl,
        'd_cnt' => $d_cnt
    );
    jrUser_reset_cache($_user['_user_id'], 'jrLike');
    jrProfile_reset_cache($_item['_profile_id']);
    return jrCore_json_response($_rs);
}

//------------------------------
// (dis)liked_items
//------------------------------
function view_jrLike_liked_items($_post, $_user, $_conf)
{
    // Must be logged
    jrUser_session_require_login();
    jrUser_check_quota_access('jrLike');
    $_ln = jrUser_load_lang_strings();

    // Banner
    jrCore_page_banner(10);

    // Get all items liked
    $tbl = jrCore_db_table_name('jrLike', 'likes');
    $req = "SELECT like_item_id, like_module FROM {$tbl} WHERE like_user_id = '{$_user['_user_id']}' AND like_action != 'neutral' ORDER BY like_created DESC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {

        // Sort likes into module groups
        $_likes = array();
        foreach ($_rt as $rt) {
            if (!isset($_likes["{$rt['like_module']}"])) {
                $_likes["{$rt['like_module']}"] = array();
            }
            $_likes["{$rt['like_module']}"][] = $rt['like_item_id'];
        }
        ksort($_likes);
        if (!isset($_post['_1']) || !jrCore_module_is_active($_post['_1'])) {
            $_post['_1'] = $_conf['jrLike_like_default'];
        }
        if (!isset($_likes["{$_post['_1']}"])) {
            $tmp         = array_keys($_likes);
            $_post['_1'] = reset($tmp);
        }
        // Tab Bar
        $_tabs = array();
        foreach ($_likes as $mod => $_v) {
            if (isset($_ln[$mod]['menu']{0})) {
                $md = ucfirst($_ln[$mod]['menu']);
            }
            else {
                $_mta = jrCore_module_meta_data($mod);
                $md   = $_mta['name'];
            }
            if (!isset($_tabs[$mod])) {
                $_tabs[$mod] = array();
            }
            $_tabs[$mod]['label'] = $md;
            $_tabs[$mod]['url']   = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/liked_items/{$mod}";
        }
        $_tabs["{$_post['_1']}"]['active'] = true;
        jrCore_page_tab_bar($_tabs);

        if (isset($_post['_1']) && jrCore_module_is_active($_post['_1'])) {
            // We are just showing likes for a specific module
            $tpl = 'item_list.tpl';
            if ($_post['_1'] == 'jrGallery') {
                $tpl = 'item_image_list.tpl';
            }
            if (isset($_likes["{$_post['_1']}"])) {
                $_s = array(
                    "search"    => array(
                        '_item_id in ' . implode(',', $_likes["{$_post['_1']}"])
                    ),
                    'page'      => (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) ? intval($_post['p']) : 1,
                    'pagebreak' => 12
                );
                if ($_post['_1'] == 'jrForum') {
                    $_s['quota_check'] = false;
                }
                $_rt  = jrCore_db_search_items($_post['_1'], $_s);
                $html = jrCore_parse_template($tpl, $_rt, $_post['_1']);
                $html .= jrCore_parse_template('list_pager.tpl', $_rt, 'jrCore');
                jrCore_page_custom($html);
            }
        }
    }
    else {
        jrCore_page_note($_ln['jrLike'][11]);
    }
    jrCore_page_display();
}

//------------------------------
// Rebuild Counts
//------------------------------
function view_jrLike_rebuild_counts($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrLike');
    jrCore_page_banner('Rebuild Like Counts');

    // Form init
    $_tmp = array(
        'submit_value'  => 'Rebuild Counts',
        'cancel'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'submit_prompt' => 'Are you sure you want to rebuild like counts?',
        'submit_modal'  => 'update',
        'modal_width'   => 600,
        'modal_height'  => 400,
        'modal_note'    => 'Please be patient while the like counts are rebuilt'
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'  => 'dummy',
        'value' => 'off',
        'type'  => 'hidden'
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_note("The <b>Rebuild Counts</b> tool rebuilds like and dislike counts based the on the existing Like database.<br>If you think that like and dislike variables may have be &quot;out of sync&quot; this tool may help.<br>Note: on large systems this could take some time to run - please be patient");

    jrCore_page_display();
}

//------------------------------
// Rebuild Counts Save
//------------------------------
function view_jrLike_rebuild_counts_save($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_form_validate($_post);
    @ini_set('max_execution_time', 86400); // 24 hours max
    @ini_set('memory_limit', '2048M');
    jrCore_logger('INF', 'rebuild of like counts started');

    // First up - get ALL datastore modules and reset ALL like and dislike keys
    jrCore_form_modal_notice('update', "deleting existing like and dislike counts...");
    $_mds = jrCore_get_datastore_modules();
    foreach ($_mds as $mod => $pfx) {
        $tbl = jrCore_db_table_name($mod, 'item_key');
        $req = "DELETE FROM {$tbl} WHERE `key` IN('{$pfx}_like_count','{$pfx}_dislike_count','{$pfx}_liked_by','{$pfx}_disliked_by')";
        $cnt = jrCore_db_query($req, 'COUNT');
        if ($cnt > 0) {
            jrCore_form_modal_notice('update', "deleted existing counts for: " . $_mods[$mod]['module_name']);
        }
    }

    // Get unique Modules in DB
    jrCore_form_modal_notice('update', "rebuilding like and dislike counts...");
    $tbl = jrCore_db_table_name('jrLike', 'likes');
    $req = "SELECT like_module FROM {$tbl} GROUP BY like_module ORDER BY like_module ASC";
    $_rt = jrCore_db_query($req, 'like_module', false, 'like_module');
    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $mod) {
            $req = "SELECT like_item_id, like_action, COUNT(like_id) AS cnt FROM {$tbl} WHERE like_module = '" . jrCore_db_escape($mod) . "' GROUP BY like_item_id, like_action";
            $_el = jrCore_db_query($req, 'NUMERIC');
            if ($_el && is_array($_el)) {
                $pfx = jrCore_db_get_prefix($mod);
                $_id = array();
                foreach ($_el as $_e) {
                    $_id[] = "({$_e['like_item_id']},'{$pfx}_{$_e['like_action']}_count',0,'{$_e['cnt']}')";
                }
                if (count($_id) > 0) {
                    $ktb = jrCore_db_table_name($mod, 'item_key');
                    $req = "INSERT INTO {$ktb} (`_item_id`,`key`,`index`,`value`) VALUES " . implode(',', $_id) . " ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
                    $cnt = jrCore_db_query($req, 'COUNT');
                    if ($cnt > 0) {
                        jrCore_form_modal_notice('update', "inserted " . number_format($cnt) . " updated like count entries in " . $_mods[$mod]['module_name']);
                    }
                }
            }
        }
    }

    jrCore_logger('INF', 'rebuild of like counts completed');
    jrCore_form_modal_notice('complete', 'Like datastore counts successfully rebuilt');
}
