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

//------------------------------
// default
//------------------------------
function profile_view_jrLike_default($_profile, $_post, $_user, $_conf)
{
    // If we get a request for a Like Item Detail page (which does not exist)
    // let's redirect to the item that the like is on
    if (isset($_post['_1']) && jrCore_checktype($_post['_1'], 'number_nz')) {
        $lid = (int) $_post['_1'];
        $tbl = jrCore_db_table_name('jrLike', 'likes');
        $req = "SELECT like_module, like_item_id FROM {$tbl} WHERE like_id = {$lid} LIMIT 1";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if ($_rt && is_array($_rt)) {
            if ($_it = jrCore_db_get_item($_rt['like_module'], $_rt['like_item_id'])) {
                $url = jrCore_get_module_url($_rt['like_module']);
                $url = "{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$url}/{$_it['_item_id']}";
                $pfx = jrCore_db_get_prefix($_rt['like_module']);
                if (isset($_it["{$pfx}_title_url"])) {
                    $url .= '/' . $_it["{$pfx}_title_url"];
                }
                header('HTTP/1.1 301 Moved Permanently');
                jrCore_location($url);
            }
        }
    }
    return false;
}
