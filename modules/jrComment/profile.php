<?php
/**
 * Jamroom Comments module
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
 * @copyright 2016 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//------------------------------
// default
//------------------------------
function profile_view_jrComment_default($_profile, $_post, $_user, $_conf)
{
    // Check for detail page access by non-admins on private comments
    if (!jrUser_is_admin()) {

        // Are we viewing a specific comment?
        if (isset($_post['_1']) && jrCore_checktype($_post['_1'], 'number_nz')) {

            // This is a comment detail page - check permissions
            if (!jrUser_is_logged_in() || !jrProfile_is_profile_owner($_profile['_profile_id'])) {

                // Get the comment and see what module it is for
                $_rt = jrCore_db_get_item('jrComment', $_post['_1'], true);
                if ($_rt && is_array($_rt)) {

                    // Module active?
                    if (!jrCore_module_is_active($_rt['comment_module'])) {
                        return jrCore_page_not_found();
                    }

                    // Are there private items for this module?
                    $_ids = jrCore_trigger_event('jrComment', 'private_item_ids', array(), $_rt, $_rt['comment_module']);
                    if ($_ids && is_array($_ids) && isset($_ids["{$_rt['comment_module']}"])) {

                        // We have private comments for this module - get comments attached to items
                        $_sp = array(
                            'search'              => array(
                                'comment_item_id in ' . implode(',', $_ids["{$_rt['comment_module']}"]),
                                "comment_module = {$_rt['comment_module']}"
                            ),
                            'return_item_id_only' => true,
                            'skip_triggers'       => true,
                            'privacy_check'       => false,
                            'ignore_pending'      => true,
                            'quota_check'         => false,
                            'order_by'            => false,
                            'limit'               => 1000000
                        );
                        $_sp = jrCore_db_search_items('jrComment', $_sp);
                        if ($_sp && is_array($_sp) && in_array($_post['_1'], $_sp)) {
                            // This is a private comment
                            return jrCore_page_not_found();
                        }
                    }
                }
                else {
                    return jrCore_page_not_found();
                }
            }
        }
        else {
            // Looks like we are doing a profile wide comment listing
            return jrCore_page_not_found();
        }

    }
    // Fallthrough - show item detail page
    return false;
}
