<?php
/**
 * Jamroom Groups module
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

//------------------------------
// group_members
//------------------------------
function profile_view_jrGroup_members($_profile, $_post, $_user, $_conf)
{
    if (!isset($_post['_2']) || !jrCore_checktype($_post['_2'], 'number_nz')) {
        jrCore_page_not_found();
    }
    $_gr = jrCore_db_get_item('jrGroup', $_post['_2'], false, true);
    if (!$_gr || !is_array($_gr) || $_gr['_profile_id'] != $_profile['_profile_id']) {
        jrCore_page_not_found();
    }
    $murl = jrCore_get_module_url('jrGroup');

    // Check privacy
    if (isset($_gr['group_private']) && $_gr['group_private'] == 'on') {
        if (!jrUser_is_logged_in()) {
            // Must be logged in
            jrUser_session_require_login();
        }
        elseif (!jrGroup_member_has_access($_gr)) {
            // Not a member
            jrCore_location("{$_conf['jrCore_base_url']}/{$murl}/private_notice/{$_gr['_item_id']}");
        }
    }

    $ttl = count($_gr['group_member']);
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $max_img = ($_conf['jrGroup_max_images'] > 0) ? $_conf['jrGroup_max_images'] : 16;
    $off     = (($_post['p'] - 1) * $max_img);
    $tpg     = ceil($ttl / $max_img);
    if (isset($_gr['group_member']) && is_array($_gr['group_member'])) {
        $_gr['group_member'] = array_slice($_gr['group_member'], $off, $max_img, true);
    }
    $_gr = array(
        'item' => $_gr,
        'info' => array(
            'page_base_url' => "{$_conf['jrCore_base_url']}/{$_gr['profile_url']}/{$murl}/members/{$_gr['_item_id']}/{$_gr['group_title_url']}",
            'prev_page'     => ($_post['p'] - 1),
            'this_page'     => $_post['p'],
            'next_page'     => (($_post['p'] + 1) <= $tpg) ? ($_post['p'] + 1) : 0,
            'total_pages'   => $tpg,
            'total_items'   => $ttl
        )
    );
    return jrCore_parse_template('item_members.tpl', $_gr, 'jrGroup');
}
