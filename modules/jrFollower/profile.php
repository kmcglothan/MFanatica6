<?php
/**
 * Jamroom Followers module
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
// Profiles Followed
//------------------------------
function profile_view_jrFollower_profiles_followed($_profile, $_post, $_user, $_conf)
{
    // Check if only logged in users
    if (isset($_conf['jrFollower_logged_in_only']) && $_conf['jrFollower_logged_in_only'] == 'on') {
        jrUser_session_require_login();
    }

    $out = '';
    $tbl = jrCore_db_table_name('jrProfile', 'profile_link');
    $req = "SELECT `user_id` FROM {$tbl} WHERE profile_id = '{$_profile['_profile_id']}'";
    $_ut = jrCore_db_query($req, 'user_id');
    if ($_ut && is_array($_ut) && count($_ut) > 0) {
        // Get all who users following info
        $_ut = array(
            'search'                       => array(
                '_user_id in ' . implode(',', array_keys($_ut)),
                'follow_active = 1'
            ),
            'order_by'                     => array('_item_id' => 'desc'),
            'exclude_jrProfile_quota_keys' => true,
            'limit'                        => jrCore_db_get_datastore_item_count('jrFollower')
        );
        $_ut = jrCore_db_search_items('jrFollower', $_ut);
        if ($_ut && is_array($_ut) && isset($_ut['_items'])) {
            // Get all profiles being followed
            $_fpid = array();
            foreach ($_ut['_items'] as $ut) {
                $_fpid["{$ut['follow_profile_id']}"] = $ut['follow_profile_id'];
            }
            $_pt = jrCore_db_get_multiple_items('jrProfile', $_fpid);
            $_pr = array();
            if ($_pt && is_array($_pt) && count($_pt) > 0) {
                foreach ($_pt as $pt) {
                    $_pr["{$pt['_profile_id']}"] = $pt;
                }
            }
            // Build the replace array
            $_rep = array();
            foreach ($_ut['_items'] as $ut) {
                if (!isset($_rep["{$ut['follow_profile_id']}"])) {
                    $_rep["{$ut['follow_profile_id']}"] = $_pr["{$ut['follow_profile_id']}"];
                }
                $_rep["{$ut['follow_profile_id']}"]['_followers']["{$ut['_user_id']}"] = $ut;
            }
            $pagebreak = $_conf['jrFollower_pagebreak'];
            if (jrCore_checktype($pagebreak, 'number_nz')) {
                if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
                    $_post['p'] = 1;
                }
                $offset      = ($_post['p'] - 1) * $pagebreak;
                $total_items = count($_rep);
                $_rep        = array_slice($_rep, $offset, $pagebreak);
                $_rep        = array(
                    '_items' => $_rep,
                    'info'   => array(
                        'total_items'   => $total_items,
                        'prev_page'     => $_post['p'] - 1,
                        'next_page'     => ($_post['p'] < ceil($total_items / $pagebreak)) ? $_post['p'] + 1 : 0,
                        'this_page'     => $_post['p'],
                        'page_base_url' => (strpos($_post['_uri'], '/p=')) ? substr($_post['_uri'], 0, strpos($_post['_uri'], '/p=')) : $_post['_uri'],
                        'total_pages'   => ceil($total_items / $pagebreak)
                    )
                );
            }
            else {
                $_rep = array('_items' => $_rep);
            }
            $_profile = array_merge($_profile, $_rep);
            // Parse templates and out
            $out = jrCore_parse_template('profiles_followed.tpl', $_profile, 'jrFollower');
            if (jrCore_checktype($pagebreak, 'number_nz')) {
                $out .= jrCore_parse_template('list_pager.tpl', $_profile, 'jrCore');
            }
        }
    }
    return $out;
}
