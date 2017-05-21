<?php
/**
 * Jamroom Group Discussions module
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

/**
 * verify
 */
function jrGroupDiscuss_verify()
{
    // Bad form designer entry
    $tbl = jrCore_db_table_name('jrCore', 'form');
    $req = "DELETE FROM {$tbl} WHERE `module` = 'jrGroupDiscuss' AND `view` IN('create','update') AND `name` IN('discuss_group_id','discuss_category')";
    jrCore_db_query($req);

    // Bad quota setting
    jrProfile_delete_quota_setting('jrGroupDiscuss', 'allowed');

    // GroupDiscuss item counts
    $_s  = array(
        "order_by"    => array('_created' => 'NUMERICAL_ASC'),
        "return_keys" => array('_item_id', 'group_jrGroupDiscuss_item_count'),
        "limit"       => 100000
    );
    $_rt = jrCore_db_search_items('jrGroup', $_s);
    if ($_rt['_items'] && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
        foreach ($_rt['_items'] as $rt) {
            if (!isset($rt['group_jrGroupDiscuss_item_count']) || !jrCore_checktype($rt['group_jrGroupDiscuss_item_count'], 'number_nz')) {
                $_s  = array(
                    "search"       => array("discuss_group_id = {$rt['_item_id']}"),
                    "return_count" => true
                );
                $gds = jrCore_db_search_items('jrGroupDiscuss', $_s);
                if (jrCore_checktype($gds, 'number_nz')) {
                    jrCore_db_update_item('jrGroup', $rt['_item_id'], array('group_jrGroupDiscuss_item_count' => $gds));
                }
            }
        }
    }

    return true;
}
