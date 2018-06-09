<?php
/**
 * Jamroom Group Discussions module
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

//------------------------------
// discussions
//------------------------------
function profile_view_jrGroupDiscuss_discussions($_profile, $_post, $_user, $_conf)
{
    global $_post; // do not remove!
    if (!isset($_post['_2']) || !jrCore_checktype($_post['_2'], 'number_nz')) {
        jrCore_page_not_found();
        return false;
    }
    $_gr = jrCore_db_get_item('jrGroup', $_post['_2']);
    if (!$_gr || !is_array($_gr)) {
        jrCore_page_not_found();
        return false;
    }
    if ($_gr['_profile_id'] != $_profile['_profile_id']) {
        // Wrong profile
        jrCore_page_not_found();
        return false;
    }
    $_ln                = jrUser_load_lang_strings();
    $_profile['_group'] = $_gr;
    $_post['group_id']  = $_post['_2'];
    jrCore_page_title("{$_ln['jrGroupDiscuss'][1]} - {$_gr['group_title']} - {$_gr['profile_name']}");
    return jrCore_parse_template('item_index.tpl', $_profile, 'jrGroupDiscuss');
}

//------------------------------
// Redirect to new URL
//------------------------------
function profile_view_jrGroupDiscuss_default($_profile, $_post, $_user, $_conf)
{
    if (isset($_post['group_id'])) {
        $_gr = jrCore_db_get_item('jrGroup', $_post['group_id']);
        if (!$_gr || !is_array($_gr)) {
            jrCore_page_not_found();
            return false;
        }
        $url = jrCore_get_module_url('jrGroupDiscuss');
        header('HTTP/1.1 301 Moved Permanently');
        jrCore_location("{$_conf['jrCore_base_url']}/{$_gr['profile_url']}/{$url}/discussions/{$_gr['_item_id']}/{$_gr['group_title_url']}");
    }
    return false;
}
