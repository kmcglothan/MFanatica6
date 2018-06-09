<?php
/**
 * Jamroom Products module
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
 * @copyright 2003 - 2017 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//------------------------------
// categories
//------------------------------
function profile_view_jrProduct_categories($_profile, $_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrProduct');
    $_ln = jrUser_load_lang_strings();

    // Banner
    $url = jrCore_get_module_url('jrProduct');
    $tmp = jrCore_page_button('create', $_ln['jrProduct'][20], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/create_category')");
    jrCore_page_banner($_ln['jrProduct'][26], $tmp);

    // Get all categories for items belonging to this profile
    $_it = false;
    $_sc = array(
        'search' => array(
            "_profile_id = {$_profile['_profile_id']}"
        ),
        'return_keys' => array('product_category_url'),
        'skip_triggers' => true,
        'ignore_pending' => true,
        'privacy_check'  => false,
        'limit'          => 50000
    );
    $_sc = jrCore_db_search_items('jrProduct', $_sc);
    if ($_sc && is_array($_sc) && isset($_sc['_items'])) {
        $_it = array();
        foreach ($_sc['_items'] as $c) {
            if (!isset($_it["{$c['product_category_url']}"])) {
                $_it["{$c['product_category_url']}"] = 0;
            }
            $_it["{$c['product_category_url']}"]++;
        }
    }

    $tbl = jrCore_db_table_name('jrProduct', 'category');
    $req = "SELECT * FROM {$tbl} WHERE cat_profile_id = '{$_profile['_profile_id']}' ORDER BY cat_title ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt) && count($_rt) > 0) {
        // Show all user categories
        foreach ($_rt as $k => $rt) {
            $count = (isset($_it["{$rt['cat_title_url']}"])) ? intval($_it["{$rt['cat_title_url']}"]) : 0;
            if ($count > 0) {
                $tmp = jrCore_page_button("count{$k}", $_ln['jrProduct'][43] . ': ' . $count, "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$url}/category/{$rt['cat_title_url']}')");
            }
            else {
                $tmp = jrCore_page_button("count{$k}", $_ln['jrProduct'][43] . ': 0', 'disabled');
            }
            $tmp .= jrCore_page_button("update{$k}", $_ln['jrProduct'][29], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/update_category/id={$rt['cat_id']}')");
            if ($count > 0) {
                $tmp .= jrCore_page_button("delete{$rt['cat_id']}", $_ln['jrProduct'][30], 'disabled');
            }
            else {
                $tmp .= jrCore_page_button("delete{$rt['cat_id']}", $_ln['jrProduct'][30], "jrCore_confirm('{$_ln['jrProduct'][31]}','',function(){jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/delete_category/id={$rt['cat_id']}')})");
            }
            jrCore_page_banner('&bull; ' . $rt['cat_title'], $tmp);
        }
    }
    else {
        jrCore_page_note($_ln['jrProduct'][27]);
    }
    jrCore_page_title($_ln['jrProduct'][26]);
    return jrCore_page_display(true);
}

//------------------------------
// default
//------------------------------
function profile_view_jrProduct_default($_profile, $_post, $_user, $_conf)
{
    if (isset($_post['option']) && $_post['option'] == 'category') {

        // We are viewing a specific category _2 is our cat
        if (!isset($_post['_2']) || strlen($_post['_2']) === 0) {
            jrCore_page_not_found();
        }
        if (!$_cat = jrProduct_get_category_by_url($_profile['_profile_id'], $_post['_2'])) {
            jrCore_page_not_found();
        }
        $_profile['_cat'] = $_cat;
        return jrCore_parse_template('item_category.tpl', $_profile, 'jrProduct');
    }
    return false;
}
