<?php
/**
 * Jamroom ShareThis module
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
 * @copyright 2015 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Handle a shortened URL request
 * @deprecated - functionality has been moved to URL Redirect module
 * @param $_post array $_POST info
 * @param $_user array active user info
 * @param $_conf array Global Config
 */
function view_jrShareThis_default($_post, $_user, $_conf)
{
    // URL like: http://site.com/s/ao5ydk03
    $tbl = jrCore_db_table_name('jrShareThis', 'url');
    $req = "SELECT * FROM {$tbl} WHERE url_key = '" . jrCore_db_escape($_post['option']) . "'";
    $_rt = jrCore_db_query($req, 'SINGLE', false, null, false, null, false, true);
    if ($_rt && is_array($_rt)) {
        $pfx = jrCore_db_get_prefix($_rt['url_module']);
        if ($pfx) {
            $_it = jrCore_db_get_item($_rt['url_module'], $_rt['url_item_id']);
            if ($_it && is_array($_it)) {
                $url = jrCore_get_module_url($_rt['url_module']);
                $ttl = '';
                if (isset($_it["{$pfx}_title_url"])) {
                    $ttl = '/' . $_it["{$pfx}_title_url"];
                }
                header('HTTP/1.1 301 Moved Permanently');
                jrCore_location("{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$url}/{$_it['_item_id']}{$ttl}");
            }
        }
    }
    // Fall through - does not belong to any item in the system
    jrCore_page_not_found();
}
