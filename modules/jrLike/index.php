<?php
/**
 * Jamroom Like It module
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
    $req = "SELECT like_user_id FROM {$tbl} WHERE like_item_id = '{$iid}' AND like_module = '{$mod}' AND like_action = '{$_post['t']}' GROUP BY like_user_id ORDER BY like_id DESC";
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
    $req = "SELECT like_item_id, like_module FROM {$tbl} WHERE like_user_id = '{$_user['_user_id']}' AND like_action != 'neutral' ORDER BY like_id DESC";
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
