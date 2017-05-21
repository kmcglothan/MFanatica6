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

//------------------------------
// create
//------------------------------
function view_jrGroupDiscuss_create($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['group_id']) || !jrCore_checktype($_post['group_id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid group_id - please try again');
    }
    $_gr = jrCore_db_get_item('jrGroup', $_post['group_id']);
    if (!$_gr || !is_array($_gr)) {
        jrCore_notice_page('error', 'Invalid group_id - please try again (2)');
    }
    if (!jrUser_is_admin() && !jrProfile_is_profile_owner($_gr['_profile_id']) && jrGroup_get_user_config('jrGroupDiscuss', 'allowed', $_gr, $_user['_user_id']) != 'on') {
        jrUser_not_authorized();
    }

    jrCore_page_banner(2);

    // Form init
    $_tmp = array(
        'submit_value' => 2,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'  => 'discuss_group_id',
        'type'  => 'hidden',
        'value' => (int) $_post['group_id']
    );
    jrCore_form_field_create($_tmp);

    // Discuss Title
    $_tmp = array(
        'name'     => 'discuss_title',
        'label'    => 3,
        'help'     => 4,
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Discuss Description
    $_tmp = array(
        'name'     => 'discuss_description',
        'label'    => 15,
        'help'     => 16,
        'type'     => 'editor',
        'validate' => 'allowed_html',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrGroupDiscuss_create_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_form_validate($_post);

    if (!isset($_post['discuss_group_id']) || !jrCore_checktype($_post['discuss_group_id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid group_id - please try again');
        jrCore_form_result();
    }
    $_gr = jrCore_db_get_item('jrGroup', $_post['discuss_group_id']);
    if (!$_gr || !is_array($_gr)) {
        jrCore_set_form_notice('error', 'Invalid group_id - please try again (2)');
        jrCore_form_result();
    }
    if (!jrUser_is_admin() && !jrProfile_is_profile_owner($_gr['_profile_id']) && jrGroup_get_user_config('jrGroupDiscuss', 'allowed', $_gr, $_user['_user_id']) != 'on') {
        jrCore_set_form_notice('error', 'you do not have permissions to create this item');
        jrCore_form_result();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('jrGroupDiscuss', 'create', $_post);

    // Add in our SEO URL names
    $_rt['discuss_title_url'] = jrCore_url_string($_rt['discuss_title']);

    // If an admin, set correct _profile_id
    $_core = null;
    if (jrUser_is_admin()) {
        $_core = array('_profile_id' => jrUser_get_profile_home_key('_profile_id'));
    }
    $pid = jrCore_db_create_item('jrGroupDiscuss', $_rt, $_core);
    if (!$pid) {
        jrCore_set_form_notice('error', 5);
        jrCore_form_result();
    }

    // Save any uploaded media files added in by our
    jrCore_save_all_media_files('jrGroupDiscuss', 'create', $_gr['_profile_id'], $pid);

    if (isset($_conf['jrGroupDiscuss_notify_all']) && $_conf['jrGroupDiscuss_notify_all'] == 'on') {
        // Notify All Group Members
        jrGroupDiscuss_notify_group_members($_rt, $_gr, $pid);
    }
    else {
        // Notify Group Watchers
        jrGroupDiscuss_notify_group_watchers($_rt, $_gr, $pid);
    }

    // Set user as a follower
    $tbl = jrCore_db_table_name('jrGroupDiscuss', 'follow');
    $req = "INSERT INTO {$tbl} (follow_id, follow_user_id, follow_created) VALUES ('{$pid}','{$_user['_user_id']}',UNIX_TIMESTAMP()) ON DUPLICATE KEY UPDATE follow_created = UNIX_TIMESTAMP()";
    jrCore_db_query($req);

    // Add to Actions...
    if (isset($_gr['group_private']) && $_gr['group_private'] != 'on') {
        $_rt['group_profile_url'] = $_gr['profile_url'];
        jrCore_run_module_function('jrAction_save', 'create', 'jrGroupDiscuss', $pid, $_rt, false, jrUser_get_profile_home_key('_profile_id'));
    }

    jrCore_form_delete_session();
    jrProfile_reset_cache($_gr['_profile_id']);
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_gr['profile_url']}/{$_post['module_url']}/{$pid}/{$_rt['discuss_title_url']}");
}

//------------------------------
// update
//------------------------------
function view_jrGroupDiscuss_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 6);
    }
    $_rt = jrCore_db_get_item('jrGroupDiscuss', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', 7);
    }
    $_gr = jrCore_db_get_item('jrGroup', $_rt['discuss_group_id']);
    if (!$_gr || !is_array($_gr)) {
        jrCore_notice_page('error', 'Invalid group_id - please try again (2)');
    }
    if (!jrUser_is_admin() && !jrProfile_is_profile_owner($_gr['_profile_id']) && !($_conf['jrGroupDiscuss_update_always'] == 'on' || ($_conf['jrGroupDiscuss_update_always'] == 'off' && $_rt['discuss_comment_count'] == 0))) {
        jrCore_notice_page('error', 'you do not have permissions to edit this item');
    }

    jrCore_page_banner(8);

    // Form init
    $_tmp = array(
        'submit_value' => 9,
        'cancel'       => jrCore_is_profile_referrer(),
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // Group ID
    $_tmp = array(
        'name'  => 'id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    // Title
    $_tmp = array(
        'name'     => 'discuss_title',
        'label'    => 3,
        'help'     => 4,
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Description
    $_tmp = array(
        'name'     => 'discuss_description',
        'label'    => 15,
        'help'     => 16,
        'type'     => 'editor',
        'validate' => 'allowed_html',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrGroupDiscuss_update_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_form_validate($_post);

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 6);
        jrCore_form_result('referrer');
    }
    // Get data
    $_rt = jrCore_db_get_item('jrGroupDiscuss', $_post['id']);
    if (!$_rt || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 7);
        jrCore_form_result();
    }
    $_gr = jrCore_db_get_item('jrGroup', $_rt['discuss_group_id']);
    if (!$_gr || !is_array($_gr)) {
        jrCore_set_form_notice('error', 'Invalid group_id - please try again (2)');
        jrCore_form_result();
    }
    if (!jrUser_is_admin() && !jrProfile_is_profile_owner($_gr['_profile_id']) && !($_conf['jrGroupDiscuss_update_always'] == 'on' || ($_conf['jrGroupDiscuss_update_always'] == 'off' && $_rt['discuss_comment_count'] == 0))) {
        jrCore_set_form_notice('error', 'you do not have permissions to edit this item');
        jrCore_form_result();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrGroupDiscuss', 'update', $_post);

    // Add in our SEO URL names
    $_sv['discuss_title_url'] = jrCore_url_string($_sv['discuss_title']);

    // Save all updated fields to the Data Store
    $_core = array('_updated' => $_rt['_updated']);
    jrCore_db_update_item('jrGroupDiscuss', $_post['id'], $_sv, $_core);

    // Save any uploaded media file
    jrCore_save_all_media_files('jrGroupDiscuss', 'update', $_user['user_active_profile_id'], $_post['id']);

    // Add to Actions...
    if ($_gr['group_private'] != 'on') {
        $_rt['group_profile_url'] = $_gr['profile_url'];
        jrCore_run_module_function('jrAction_save', 'update', 'jrGroupDiscuss', $_post['id'], $_rt, false, jrUser_get_profile_home_key('_profile_id'));
    }

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_rt['profile_url']}/{$_post['module_url']}/{$_post['id']}/{$_sv['discuss_title_url']}");
}

//------------------------------
// delete
//------------------------------
function view_jrGroupDiscuss_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGroupDiscuss');

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 6);
        jrCore_form_result('referrer');
    }
    $_rt  = jrCore_db_get_item('jrGroupDiscuss', $_post['id']);
    $_gt  = jrCore_db_get_item('jrGroup', $_rt['discuss_group_id']);
    $gurl = jrCore_get_module_url('jrGroup');

    // Make sure the calling user has permission to delete this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    // Delete item and any associated files
    jrCore_db_delete_item('jrGroupDiscuss', $_post['id']);
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_rt['profile_url']}/{$gurl}/{$_gt['_item_id']}/{$_gt['group_title_url']}");
}

//--------------------------------------------------------------
// watch or unwatch a discussion
//--------------------------------------------------------------
function view_jrGroupDiscuss_toggle_watch($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    // We must get a valid ID
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_json_response(array('error' => 'invalid discussion id'));
    }
    $_ln = jrUser_load_lang_strings();
    $did = (int) $_post['id'];
    $uid = (int) $_user['_user_id'];
    $tbl = jrCore_db_table_name('jrGroupDiscuss', 'follow');
    $req = "SELECT follow_id FROM {$tbl} WHERE follow_id = '{$did}' AND follow_user_id = '{$uid}' LIMIT 1";
    $fol = jrCore_db_query($req, 'SINGLE');
    if (!$fol) {
        $req = "INSERT INTO {$tbl} (follow_id, follow_user_id, follow_created) VALUES ('{$did}','{$uid}',UNIX_TIMESTAMP()) ON DUPLICATE KEY UPDATE follow_created = UNIX_TIMESTAMP()";
        $tag = $_ln['jrGroupDiscuss'][25];
        $fol = 'on';
    }
    else {
        $req = "DELETE FROM {$tbl} WHERE follow_id = '{$did}' AND follow_user_id = '{$uid}'";
        $tag = $_ln['jrGroupDiscuss'][26];
        $fol = 'off';
    }
    jrCore_db_query($req);
    jrCore_delete_all_cache_entries('jrGroupDiscuss', $_user['_user_id']);
    $_rp = array(
        'success'   => 1,
        'following' => $fol,
        'tag'       => $tag
    );
    jrCore_json_response($_rp);
}

//--------------------------------------------------------------
// watch or unwatch a group for new discussions added
//--------------------------------------------------------------
function view_jrGroupDiscuss_toggle_group_watch($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    // We must get a valid ID
    if (!isset($_post['group_id']) || !jrCore_checktype($_post['group_id'], 'number_nz')) {
        jrCore_json_response(array('error' => 'invalid group id'));
    }
    $_ln = jrUser_load_lang_strings();
    $gid = (int) $_post['group_id'];
    $uid = (int) $_user['_user_id'];
    $tbl = jrCore_db_table_name('jrGroupDiscuss', 'follow_group');
    $req = "SELECT follow_group_id FROM {$tbl} WHERE follow_group_id = '{$gid}' AND follow_user_id = '{$uid}' LIMIT 1";
    $fol = jrCore_db_query($req, 'SINGLE');
    if (!$fol) {
        $req = "INSERT INTO {$tbl} (follow_group_id, follow_user_id, follow_created) VALUES ('{$gid}','{$uid}',UNIX_TIMESTAMP()) ON DUPLICATE KEY UPDATE follow_created = UNIX_TIMESTAMP()";
        $tag = $_ln['jrGroupDiscuss'][30];
        $fol = 'on';
    }
    else {
        $req = "DELETE FROM {$tbl} WHERE follow_group_id = '{$gid}' AND follow_user_id = '{$uid}'";
        $tag = $_ln['jrGroupDiscuss'][31];
        $fol = 'off';
    }
    jrCore_db_query($req);
    jrCore_delete_all_cache_entries('jrGroupDiscuss', $_user['_user_id']);
    $_rp = array(
        'success'   => 1,
        'following' => $fol,
        'tag'       => $tag
    );
    jrCore_json_response($_rp);
}

//------------------------------
// Tool: Transfer forum topic(s) to a group as a group discuss item
//------------------------------
function view_jrGroupDiscuss_transfer_topic_to_discuss($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrGroupDiscuss');
    jrCore_page_banner('Select and transfer forum topics to a group as group discussions');

    if (!jrCore_module_is_active('jrForum')) {
        jrCore_page_notice('error', 'Forum module not active');
        jrCore_page_display();
        exit;
    }
    if (!jrCore_module_is_active('jrGroup')) {
        jrCore_page_notice('error', 'Group module not active');
        jrCore_page_display();
        exit;
    }

    // Get all profile forums with topics
    $_s  = array(
        "search"         => array(
            "forum_title LIKE %",
            "forum_post_count >= 1"
        ),
        "order_by"       => array('profile_name' => 'asc'),
        "group_by"       => 'forum_profile_id',
        'quota_check'    => false,
        'privacy_check'  => false,
        'ignore_pending' => true,
        "return_keys"    => array('forum_profile_id'),
        "limit"          => jrCore_db_get_datastore_item_count('jrForum')
    );
    $_rt = jrCore_db_search_items('jrForum', $_s);
    if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
        // Select forum
        $_fopts = array('0' => '-');
        foreach ($_rt['_items'] as $rt) {
            $_fopts["{$rt['forum_profile_id']}"] = jrCore_db_get_item_key('jrProfile', $rt['forum_profile_id'], 'profile_name');
        }
        $_tmp = array(
            'name'     => 'profile_id',
            'label'    => 'select profile forum',
            'help'     => 'select the profile whose forum topics you want to transfer',
            'options'  => $_fopts,
            'value'    => $_post['pid'],
            'type'     => 'select',
            'validate' => 'printable',
            'required' => true,
            'onchange' => "var vp=this.options[this.selectedIndex].value; jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/transfer_topic_to_discuss/pid='+ vp)"
        );
        jrCore_form_field_create($_tmp);

        // Attached files not transferred warning
        $_forum_owner = jrCore_db_get_item('jrProfile', $_post['pid']);
        if (isset($_forum_owner['quota_jrForum_file_attachments']) && $_forum_owner['quota_jrForum_file_attachments'] == 'on') {
            jrCore_page_note("<strong>Note</strong> that any attachments on the selected topic posts will not be transferred.");
        }

        if (isset($_post['pid']) && jrCore_checktype($_post['pid'], 'number_nz')) {
            // Get all categories for this forum
            $tbl   = jrCore_db_table_name('jrForum', 'category');
            $req   = "SELECT * FROM {$tbl} WHERE `cat_profile_id` = {$_post['pid']} ORDER BY `cat_title` ASC";
            $_cats = jrCore_db_query($req, 'NUMERIC');
            if ($_cats && is_array($_cats) && count($_cats) > 0) {
                $_st    = false;
                $_copts = array('0' => '-');
                foreach ($_cats as $cat) {
                    $_copts["{$cat['cat_id']}"] = "{$cat['cat_title']}";
                }
                $_tmp = array(
                    'name'     => 'cat_id',
                    'label'    => 'select forum category',
                    'help'     => 'select the category whos topics you want to transfer',
                    'options'  => $_copts,
                    'value'    => $_post['cid'],
                    'type'     => 'select',
                    'validate' => 'printable',
                    'required' => true,
                    'onchange' => "var vc=this.options[this.selectedIndex].value; jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/transfer_topic_to_discuss/pid={$_post['pid']}/cid='+ vc)"
                );
                jrCore_form_field_create($_tmp);

                if (isset($_post['cid']) && jrCore_checktype($_post['cid'], 'number_nz')) {
                    // Form init
                    $_tmp = array(
                        'submit_value'  => 'Transfer',
                        'cancel'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
                        'submit_prompt' => 'Are you sure you want to transfer the selected forum topics?',
                        'submit_modal'  => 'update',
                        'modal_width'   => 800,
                        'modal_height'  => 400,
                        'modal_note'    => 'Please be patient whilst the selected forum topics are transferred'
                    );
                    jrCore_form_create($_tmp);

                    // Topic search for this profile forum category
                    $_st = array(
                        "search"         => array(
                            "forum_title LIKE %",
                            "forum_profile_id = {$_post['pid']}",
                            "forum_cat = {$_copts["{$_post['cid']}"]}"
                        ),
                        "order_by"       => array('forum_title' => 'asc'),
                        'quota_check'    => false,
                        'privacy_check'  => false,
                        'ignore_pending' => true,
                        "return_keys"    => array('_item_id', 'forum_title', 'forum_cat', 'forum_post_count', 'profile_name'),
                        "limit"          => jrCore_db_get_datastore_item_count('jrForum')
                    );
                }
            }
            else {
                // Form init
                $_tmp = array(
                    'submit_value'  => 'Transfer',
                    'cancel'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
                    'submit_prompt' => 'Are you sure you want to transfer the selected forum topics?',
                    'submit_modal'  => 'update',
                    'modal_width'   => 800,
                    'modal_height'  => 400,
                    'modal_note'    => 'Please be patient whilst the selected forum topics are transferred'
                );
                jrCore_form_create($_tmp);

                // Topic search for this profile forum
                $_st = array(
                    "search"         => array(
                        "forum_title LIKE %",
                        "forum_profile_id = {$_post['pid']}"
                    ),
                    "order_by"       => array('forum_title' => 'asc'),
                    'quota_check'    => false,
                    'privacy_check'  => false,
                    'ignore_pending' => true,
                    "return_keys"    => array('_item_id', 'forum_title', 'forum_cat', 'forum_post_count', 'profile_name'),
                    "limit"          => jrCore_db_get_datastore_item_count('jrForum')
                );
            }

            if ($_st && is_array($_st)) {
                // Get appropriate topics
                $_rt = jrCore_db_search_items('jrForum', $_st);
                if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
                    $_topts = array();
                    foreach ($_rt['_items'] as $rt) {
                        $_topts["{$rt['_item_id']}"] = "'{$rt['forum_title']}' by '{$rt['profile_name']}' ({$rt['forum_post_count']} posts)";
                    }
                    $_tmp = array(
                        'name'     => 'topic_ids',
                        'label'    => 'select forum topics',
                        'sublabel' => '(multi-select)',
                        'help'     => 'select the forum topic(s) you want to transfer',
                        'options'  => $_topts,
                        'type'     => 'select_multiple'
                    );
                    jrCore_form_field_create($_tmp);

                    // Select target group
                    $_s  = array(
                        "order_by" => array('group_title' => 'asc'),
                        "limit"    => jrCore_db_get_datastore_item_count('jrGroup')
                    );
                    $_rt = jrCore_db_search_items('jrGroup', $_s);
                    if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
                        $_gopts = array('0' => '-');
                        foreach ($_rt['_items'] as $rt) {
                            $_gopts["{$rt['_item_id']}"] = "{$rt['group_title']} [{$rt['profile_name']}]";
                        }
                        $_tmp = array(
                            'name'     => 'target_group_id',
                            'label'    => 'target group',
                            'help'     => 'select the target group for the transfer',
                            'options'  => $_gopts,
                            'type'     => 'select',
                            'validate' => 'printable'
                        );
                        jrCore_form_field_create($_tmp);

                        // Delete after transfer option
                        $_tmp = array(
                            'name'    => "delete_after_xfer",
                            'label'   => 'Delete After Transfer',
                            'help'    => 'If checked, after transfer the selected forum topics will be deleted',
                            'type'    => 'checkbox',
                            'default' => 'on'
                        );
                        jrCore_form_field_create($_tmp);

                        // Email owner
                        $_tmp = array(
                            'name'    => "email_owner",
                            'label'   => 'Email Owner',
                            'help'    => 'If checked, the owner of the forum topic will be emailed that the item has been transferred',
                            'type'    => 'checkbox',
                            'default' => 'off'
                        );
                        jrCore_form_field_create($_tmp);
                    }
                    else {
                        jrCore_page_notice('error', 'No target groups found');
                    }
                }
                else {
                    jrCore_page_notice('error', 'No forum topics found');
                }
            }
        }
    }
    else {
        jrCore_page_notice('error', 'No profile forums found');
    }
    jrCore_page_display();
}

//------------------------------
// transfer_topic_save
//------------------------------
function view_jrGroupDiscuss_transfer_topic_to_discuss_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    // Do some checking
    if (!jrCore_checktype($_post['profile_id'], 'number_nz')) {
        jrCore_form_modal_notice('complete', "ERROR: Invalid profile id");
        exit;
    }
    if (!isset($_post['topic_ids']) || !is_array($_post['topic_ids']) || count($_post['topic_ids']) == 0) {
        jrCore_form_modal_notice('complete', "ERROR: No topic(s) selected");
        exit;
    }
    if (!jrCore_checktype($_post['target_group_id'], 'number_nz')) {
        jrCore_form_modal_notice('complete', "ERROR: Target group not selected");
        exit;
    }

    // Get target group members
    $_members = array();
    $tbl      = jrCore_db_table_name('jrGroup', 'member');
    $req      = "SELECT * FROM {$tbl} WHERE `member_group_id` = {$_post['target_group_id']}";
    $_rt      = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt) && count($_rt) > 0) {
        foreach ($_rt as $rt) {
            $_members["{$rt['member_user_id']}"] = true;
        }
    }

    // All good - Do the transfer(s)
    if ($_target_group = jrCore_db_get_item('jrGroup', $_post['target_group_id'])) {
        $cnt = 0;
        foreach ($_post['topic_ids'] as $tid) {
            $updated = '';
            $_s      = array(
                "search"         => array("forum_group_id = {$tid}"),
                "order_by"       => array("_item_id" => "asc"),
                'quota_check'    => false,
                'privacy_check'  => false,
                'ignore_pending' => true,
                "limit"          => 10000
            );
            $_rt     = jrCore_db_search_items('jrForum', $_s);
            if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
                $_dids = array(); // Collect item IDs for this topic in case we need to delete them
                foreach ($_rt['_items'] as $k => $rt) {
                    $_dids[] = $rt['_item_id'];
                    if ($k == 0) {
                        // Already transferred to this group?
                        if (jrCore_db_get_item_by_key('jrGroupDiscuss', 'discuss_xfer_key', "{$_post['target_group_id']}|{$rt['_item_id']}")) {
                            jrCore_form_modal_notice('update', "ERROR: Topic '{$rt['forum_title']}' already transferred to this group");
                            continue 2;
                        }
                        // Create the group discuss item
                        $_tmp  = array(
                            'discuss_title'         => $rt['forum_title'],
                            'discuss_title_url'     => jrCore_url_string($rt['forum_title']),
                            'discuss_group_id'      => $_post['target_group_id'],
                            'discuss_description'   => $rt['forum_text'],
                            'discuss_comment_count' => count($_rt['_items']) - 1,
                            'discuss_pending'       => 0,
                            'discuss_xfer_key'      => "{$_post['target_group_id']}|{$rt['_item_id']}"
                        );
                        $_core = array(
                            '_created'    => $rt['_created'],
                            '_updated'    => $rt['_created'],
                            '_profile_id' => $rt['_profile_id'],
                            '_user_id'    => $rt['_user_id']
                        );
                        if ($gdid = jrCore_db_create_item('jrGroupDiscuss', $_tmp, $_core)) {
                            // Check for target group membership
                            if (!isset($_members["{$rt['_user_id']}"])) {
                                $_members["{$rt['_user_id']}"] = true;
                                $tbl                           = jrCore_db_table_name('jrGroup', 'member');
                                $req                           = "INSERT INTO {$tbl} (member_created, member_user_id, member_group_id, member_status) VALUES (UNIX_TIMESTAMP(), '{$rt['_user_id']}', '{$_post['target_group_id']}', '1')";
                                jrCore_db_query($req);
                            }
                        }
                        else {
                            jrCore_form_modal_notice('update', "ERROR: Failed to create group discuss item for '{$rt['forum_title']}'");
                        }
                    }
                    elseif (isset($gdid) && jrCore_checktype($gdid, 'number_nz')) {
                        // Create the comments on the group discuss item
                        $_tmp  = array(
                            'comment_text'       => $rt['forum_text'],
                            'comment_profile_id' => $_rt['_items'][0]['_profile_id'],
                            'comment_module'     => 'jrGroupDiscuss',
                            'comment_item_title' => $_rt['_items'][0]['forum_title'],
                            'comment_item_id'    => $gdid,
                            'comment_pending'    => 0
                        );
                        $_core = array(
                            '_created'    => $rt['_created'],
                            '_updated'    => $rt['_created'],
                            '_profile_id' => $rt['_profile_id'],
                            '_user_id'    => $rt['_user_id']
                        );
                        if ($id = jrCore_db_create_item('jrComment', $_tmp, $_core)) {
                            // Check for target group membership
                            if (!isset($_members["{$rt['_user_id']}"])) {
                                $_members["{$rt['_user_id']}"] = true;
                                $tbl                           = jrCore_db_table_name('jrGroup', 'member');
                                $req                           = "INSERT INTO {$tbl} (member_created, member_user_id, member_group_id, member_status) VALUES (UNIX_TIMESTAMP(), '{$rt['_user_id']}', '{$_post['target_group_id']}', '1')";
                                jrCore_db_query($req);
                            }
                        }
                        else {
                            jrCore_form_modal_notice('update', "ERROR: Failed to create comment item for '{$rt['forum_title']}'");
                        }
                    }
                    $updated = $rt['_created'];
                }
                // Delete forum topic?
                if (isset($_post['delete_after_xfer']) && $_post['delete_after_xfer'] == 'on' && count($_dids) > 0) {
                    jrCore_db_delete_multiple_items('jrForum', $_dids);
                    // Next - we need to decrement the cat count
                    if (isset($_rt['_items'][0]['forum_cat_url']) && strlen($_rt['_items'][0]['forum_cat_url']) > 0) {
                        $url = jrCore_db_escape($_rt['_items'][0]['forum_cat_url']);
                        $tbl = jrCore_db_table_name('jrForum', 'category');
                        $req = "UPDATE {$tbl} SET cat_topic_count = (cat_topic_count - 1) WHERE cat_title_url = '{$url}' AND cat_profile_id = '{$_rt['_items'][0]['forum_profile_id']}' AND cat_topic_count > 0";
                        jrCore_db_query($req);
                        $_sp = array(
                            'search'         => array(
                                "forum_profile_id = {$_rt['_items'][0]['forum_profile_id']}",
                                "forum_post_count > 0",
                                "forum_cat_url = {$_rt['_items'][0]['forum_cat_url']}"
                            ),
                            'order_by'       => array(
                                'forum_updated' => 'numerical_desc'
                            ),
                            'quota_check'    => false,
                            'privacy_check'  => false,
                            'ignore_pending' => true,
                            'skip_triggers'  => true,
                            'limit'          => 1
                        );
                        $_lp = jrCore_db_search_items('jrForum', $_sp);
                        if ($_lp && is_array($_lp) && isset($_lp['_items']) && isset($_lp['_items'][0]['forum_updated_user_id'])) {
                            $uid = (int) $_lp['_items'][0]['forum_updated_user_id'];
                            $_us = jrCore_db_get_item('jrUser', $uid);
                            if ($_us && is_array($_us)) {
                                jrForum_set_category_last_user_info($_rt['_items'][0]['forum_profile_id'], $_rt['_items'][0]['forum_cat_url'], $_us);
                            }
                        }
                    }
                    // Delete any FOLLOWS for the topic
                    $tbl = jrCore_db_table_name('jrForum', 'follow_topic');
                    $req = "DELETE FROM {$tbl} WHERE follow_forum_id = '{$_rt['_items'][0]['forum_group_id']}'";
                    jrCore_db_query($req);
                    jrProfile_reset_cache($_rt['_items'][0]['forum_profile_id'], 'jrForum');
                }
                // Email owner
                if (isset($_post['email_owner']) && $_post['email_owner'] == 'on' && isset($gdid)) {
                    $_rep = array(
                        'source_module' => 'jrForum',
                        'target_module' => 'jrGroup',
                        'source_type'   => 'Forum Topic',
                        'target_type'   => 'Group',
                        'target_url'    => $_conf['jrCore_base_url'] . '/' . $_target_group['profile_url'] . '/' . jrCore_get_module_url('jrGroupDiscuss') . '/' . $gdid . '/' . $_rt['_items'][0]['forum_title_url'],
                        '_source'       => $_rt['_items'][0],
                        '_target'       => $_target_group,
                    );
                    list($sub, $msg) = jrCore_parse_email_templates('jrGroupDiscuss', 'transferred', $_rep);
                    jrCore_send_email(jrCore_db_get_item_key('jrUser', $_rt['_items'][0]['_user_id'], 'user_email'), $sub, $msg);
                    jrCore_form_modal_notice('update', "Forum Topic '{$_rt['_items'][0]['forum_title']}' transferred - Owner notified");
                }
                else {
                    jrCore_form_modal_notice('update', "Forum Topic '{$_rt['_items'][0]['forum_title']}' transferred");
                }
                $cnt++;
                jrCore_logger('INF', "Forum Topic '{$_rt['_items'][0]['forum_title']}' transferred to group '{$_target_group['group_title']}'");
            }
            else {
                jrCore_form_modal_notice('update', "ERROR: Unable to get data for forum_group_id '{$tid}'");
            }
            // Set discuss item _updated to that of last comment
            if (jrCore_checktype($updated, 'number_nn') && isset($gdid) && jrCore_checktype($gdid, 'number_nz') && isset($_conf['jrGroupDiscuss_recently_active']) && $_conf['jrGroupDiscuss_recently_active'] != 'off') {
                jrCore_db_update_item('jrGroupDiscuss', $gdid, array(), array('_updated' => $updated));
            }
        }
        jrCore_form_modal_notice('complete', "SUCCESS: {$cnt} forum topics transferred");
    }
    else {
        jrCore_form_modal_notice('complete', "ERROR: Unable to get target group data");
    }
}

//------------------------------
// Tool: Transfer group discuss item(s) to a Discussion module category
//------------------------------
function view_jrGroupDiscuss_transfer_discuss_to_discussion($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrGroupDiscuss');
    jrCore_page_banner('Select and transfer group discuss items to a Discussion module category');

    if (!jrCore_module_is_active('jrDiscussion')) {
        jrCore_page_notice('error', 'Discussion module not active');
        jrCore_page_display();
        exit;
    }
    if (!jrCore_module_is_active('jrGroup')) {
        jrCore_page_notice('error', 'Group module not active');
        jrCore_page_display();
        exit;
    }
    if (!jrCore_module_is_active('jrGroupDiscuss')) {
        jrCore_page_notice('error', 'GroupDiscuss module not active');
        jrCore_page_display();
        exit;
    }

    if (isset($_post['gid']) && jrCore_checktype($_post['gid'], 'number_nz')) {
        // Form init
        $_tmp = array(
            'submit_value'  => 'Transfer',
            'cancel'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
            'submit_prompt' => 'Are you sure you want to transfer the selected group discuss items?',
            'submit_modal'  => 'update',
            'modal_width'   => 800,
            'modal_height'  => 400,
            'modal_note'    => 'Please be patient whilst the selected group discuss items are transferred'
        );
        jrCore_form_create($_tmp);
    }
    // Get all groups with discuss items
    $_s  = array(
        "search"         => array("group_jrGroupDiscuss_item_count > 0"),
        "order_by"       => array('group_title' => 'asc'),
        'privacy_check'  => false,
        'ignore_pending' => true,
        "return_keys"    => array('_item_id', 'group_title', 'group_jrGroupDiscuss_item_count', 'profile_name'),
        "limit"          => jrCore_db_get_datastore_item_count('jrGroup')
    );
    $_rt = jrCore_db_search_items('jrGroup', $_s);
    if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
        // Select group
        $_gopts = array('0' => '-');
        foreach ($_rt['_items'] as $rt) {
            $_gopts["{$rt['_item_id']}"] = "{$rt['group_title']} ({$rt['group_jrGroupDiscuss_item_count']}) [{$rt['profile_name']}]";
        }
        $_tmp = array(
            'name'     => 'group_id',
            'label'    => 'select group',
            'help'     => 'select the group whose discuss item(s) you want to transfer',
            'options'  => $_gopts,
            'value'    => $_post['gid'],
            'type'     => 'select',
            'validate' => 'printable',
            'required' => true,
            'onchange' => "var v=this.options[this.selectedIndex].value; jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/transfer_discuss_to_discussion/gid='+ v)"
        );
        jrCore_form_field_create($_tmp);
        if (isset($_post['gid']) && jrCore_checktype($_post['gid'], 'number_nz')) {
            // Get all discuss items for this group
            $_s  = array(
                "search"                       => array("discuss_group_id = {$_post['gid']}"),
                "order_by"                     => array('discuss_title' => 'asc'),
                'privacy_check'                => false,
                'ignore_pending'               => true,
                'exclude_jrUser_keys'          => true,
                'exclude_jrProfile_quota_keys' => true,
                "return_keys"                  => array('_item_id', 'discuss_group_id', 'discuss_comment_count', 'discuss_title', 'profile_name'),
                "limit"                        => jrCore_db_get_datastore_item_count('jrGroupDiscuss')
            );
            $_rt = jrCore_db_search_items('jrGroupDiscuss', $_s);
            if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
                // Select group discuss item(s)
                $_dopts = array();
                foreach ($_rt['_items'] as $rt) {
                    if (!isset($rt['discuss_comment_count'])) {
                        $rt['discuss_comment_count'] = 0;
                    }
                    $_dopts["{$rt['_item_id']}"] = "{$rt['discuss_title']} by '{$rt['profile_name']}' ({$rt['discuss_comment_count']} comments)";
                }
                $_tmp = array(
                    'name'     => 'discuss_ids',
                    'label'    => 'select discuss items',
                    'sublabel' => '(multi-select)',
                    'help'     => 'select the group discuss item(s) you want to transfer',
                    'options'  => $_dopts,
                    'type'     => 'select_multiple'
                );
                jrCore_form_field_create($_tmp);

                // Select target discussion category
                $_s  = array(
                    "search"                       => array("discussion_type = cat"),
                    "order_by"                     => array('discussion_title' => 'asc'),
                    'privacy_check'                => false,
                    'ignore_pending'               => true,
                    'exclude_jrUser_keys'          => true,
                    'exclude_jrProfile_quota_keys' => true,
                    "return_keys"                  => array('_item_id', 'discussion_type', 'discussion_title', 'profile_name'),
                    "limit"                        => jrCore_db_get_datastore_item_count('jrDiscussion')
                );
                $_rt = jrCore_db_search_items('jrDiscussion', $_s);
                if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
                    $_topts = array('0' => '-');
                    foreach ($_rt['_items'] as $rt) {
                        $_topts["{$rt['_item_id']}"] = "{$rt['discussion_title']} [{$rt['profile_name']}]";
                    }
                    $_tmp = array(
                        'name'     => 'discussion_id',
                        'label'    => 'target discussion category',
                        'help'     => 'select the target discussion category for the transfer',
                        'options'  => $_topts,
                        'type'     => 'select',
                        'validate' => 'printable'
                    );
                    jrCore_form_field_create($_tmp);
                    // Delete after transfer option
                    $_tmp = array(
                        'name'    => "delete_after_xfer",
                        'label'   => 'Delete After Transfer',
                        'help'    => 'If checked, after transfer the selected group discuss items will be deleted',
                        'type'    => 'checkbox',
                        'default' => 'on'
                    );
                    jrCore_form_field_create($_tmp);
                    // Email owner
                    $_tmp = array(
                        'name'    => "email_owner",
                        'label'   => 'Email Owner',
                        'help'    => 'If checked, the owner of the Group Discuss item will be emailed that the item has been moved',
                        'type'    => 'checkbox',
                        'default' => 'off'
                    );
                    jrCore_form_field_create($_tmp);
                }
                else {
                    jrCore_page_notice('error', 'No target discussion categories found');
                }
            }
            else {
                jrCore_page_notice('error', 'No group discussions found');
            }
        }
    }
    else {
        jrCore_page_notice('error', 'No groups with discussions found');
    }
    jrCore_page_display();
}

//------------------------------
// transfer_discuss_to_discussion_save
//------------------------------
function view_jrGroupDiscuss_transfer_discuss_to_discussion_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Do some checking
    if (!jrCore_checktype($_post['group_id'], 'number_nz')) {
        jrCore_form_modal_notice('complete', "ERROR: Invalid group id");
        exit;
    }
    if (!isset($_post['discuss_ids']) || !is_array($_post['discuss_ids']) || count($_post['discuss_ids']) == 0) {
        jrCore_form_modal_notice('complete', "ERROR: No discuss item(s) selected");
        exit;
    }
    if (!jrCore_checktype($_post['discussion_id'], 'number_nz')) {
        jrCore_form_modal_notice('complete', "ERROR: Target discussion category not selected");
        exit;
    }

    // All good - Do the transfer(s)
    if ($_target_discussion = jrCore_db_get_item('jrDiscussion', $_post['discussion_id'])) {
        $_gids = array();
        $cnt   = 0;
        foreach ($_post['discuss_ids'] as $did) {
            if ($_rt = jrCore_db_get_item('jrGroupDiscuss', $did)) {
                // Already transferred to this discussion category?
                if (jrCore_db_get_item_by_key('jrDiscussion', 'discussion_xfer_key', "{$_target_discussion['_item_id']}|{$did}")) {
                    jrCore_form_modal_notice('update', "ERROR: Topic '{$_rt['discuss_title']}' already transferred to this discussion category");
                    continue;
                }
                if ($_pt = jrCore_db_get_item_by_key('jrProfile', 'profile_url', $_rt['original_profile_url'])) {
                    // Do the transfer - Create new discussion topic
                    $_tmp  = array(
                        'discussion_title'               => $_rt['discuss_title'],
                        'discussion_title_url'           => $_rt['discuss_title_url'],
                        'discussion_text'                => $_rt['discuss_description'],
                        'discussion_type'                => 'topic',
                        'discussion_pending'             => 0,
                        'discussion_cat_id'              => $_target_discussion['_item_id'],
                        'discussion_cat_title'           => $_target_discussion['discussion_title'],
                        'discussion_cat_title_url'       => $_target_discussion['discussion_title_url'],
                        'discussion_profile_id'          => $_target_discussion['_profile_id'],
                        'discussion_creator_profile_id'  => $_pt['_profile_id'],
                        'discussion_creator_profile_url' => $_pt['profile_url'],
                        'discussion_display_order'       => 0,
                        'discussion_xfer_key'            => "{$_target_discussion['_item_id']}|{$did}"
                    );
                    $_core = array(
                        '_profile_id' => $_target_discussion['_profile_id'],
                        '_user_id'    => $_target_discussion['_user_id'],
                        '_created'    => $_rt['_created'],
                        '_updated'    => $_rt['_updated']
                    );
                    if ($id = jrCore_db_create_item('jrDiscussion', $_tmp, $_core, true, true)) {
                        // Add to array for possible discuss item deletion
                        $_gids[] = $did;
                        // Increment topic count
                        jrCore_db_increment_key('jrDiscussion', $_target_discussion['_item_id'], 'discussion_cat_topic_count', 1);
                        // Copy comments over
                        $_s  = array(
                            "search"         => array(
                                "comment_module = jrGroupDiscuss",
                                "comment_item_id = {$did}"
                            ),
                            'privacy_check'  => false,
                            'ignore_pending' => true,
                            "limit"          => 10000
                        );
                        $_ct = jrCore_db_search_items('jrComment', $_s);
                        if ($_ct && is_array($_ct['_items']) && count($_ct['_items']) > 0) {
                            $_tmp                     = array();
                            $_core                    = array();
                            $i                        = 0;
                            $last_comment_profile_id  = 0;
                            $last_comment_updated     = 0;
                            $last_comment_profile_url = false;
                            foreach ($_ct['_items'] as $ct) {
                                $_t = array();
                                $_c = array();
                                foreach ($ct as $k => $v) {
                                    if (substr($k, 0, 7) == 'comment') {
                                        $_t["{$k}"] = $v;
                                    }
                                    elseif (substr($k, 0, 1) == '_') {
                                        $_c["{$k}"] = $v;
                                    }
                                }
                                $_t['comment_module']     = 'jrDiscussion';
                                $_t['comment_item_id']    = $id;
                                $_t['comment_profile_id'] = $_target_discussion['_profile_id'];
                                $_tmp[$i]                 = $_t;
                                $_core[$i]                = $_c;
                                $last_comment_profile_id  = $ct['_profile_id'];
                                $last_comment_profile_url = $ct['profile_url'];
                                $last_comment_updated     = $ct['_updated'];
                                $i++;
                            }
                            $ccnt = count(jrCore_db_create_multiple_items('jrComment', $_tmp, $_core));
                            jrCore_db_increment_key('jrDiscussion', $id, 'discussion_comment_count', $ccnt);

                            if ($last_comment_profile_id > 0) {
                                $_t = array(
                                    'discussion_last_comment_profile_id'  => $last_comment_profile_id,
                                    'discussion_last_comment_profile_url' => $last_comment_profile_url
                                );
                                $_c = array(
                                    '_updated' => $last_comment_updated
                                );
                                jrCore_db_update_item('jrDiscussion', $id, $_t, $_c);
                            }
                        }
                        // Email owner
                        if (isset($_post['email_owner']) && $_post['email_owner'] == 'on') {
                            $_rep = array(
                                'source_module' => 'jrGroupDiscuss',
                                'target_module' => 'jrDiscussion',
                                'source_type'   => 'Group Discuss item',
                                'target_type'   => 'Discussion',
                                'target_url'    => $_conf['jrCore_base_url'] . '/' . $_target_discussion['profile_url'] . '/' . jrCore_get_module_url('jrDiscussion') . '/' . $id . '/' . $_rt['discuss_title_url'],
                                '_source'       => $_rt,
                                '_target'       => $_target_discussion
                            );
                            list($sub, $msg) = jrCore_parse_email_templates('jrGroupDiscuss', 'transferred', $_rep);
                            jrCore_send_email(jrCore_db_get_item_key('jrUser', $_pt['_user_id'], 'user_email'), $sub, $msg);
                            jrCore_form_modal_notice('update', "Group Discuss item '{$_rt['discuss_title']}' transferred - Owner notified");
                        }
                        else {
                            jrCore_form_modal_notice('update', "Group Discuss item '{$_rt['discuss_title']}' transferred");
                        }
                        $cnt++;
                        jrCore_logger('INF', "Group Discuss item '{$_rt['discuss_title']}' transferred to Discussion '{$_target_discussion['discussion_title']}'");
                    }
                    else {
                        jrCore_form_modal_notice('update', "Error: Discussion topic '{$_rt['discuss_title']}' not created");
                    }
                }
                else {
                    jrCore_form_modal_notice('update', "Error: Unable to get data for '{$_rt['original_profile_url']}'");
                }
            }
            else {
                jrCore_form_modal_notice('update', "Error: Unable to get group discuss item data for ID:'{$did}'");
            }
        }
        // Are we deleting the discuss items?
        if (isset($_post['delete_after_xfer']) && $_post['delete_after_xfer'] == 'on' && count($_gids) > 0) {
            jrCore_db_delete_multiple_items('jrGroupDiscuss', $_gids);
            jrCore_form_modal_notice('update', count($_gids) . ' group discuss items deleted');
        }
        jrCore_form_modal_notice('complete', "SUCCESS: {$cnt} group discuss items transferred");
    }
    else {
        jrCore_form_modal_notice('complete', "ERROR: Unable to get target discussion data");
    }
}

//------------------------------
// Tool: Transfer group discuss item(s) to a Forum module topic
//------------------------------
function view_jrGroupDiscuss_transfer_discuss_to_forum($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrGroupDiscuss');
    jrCore_page_banner('Select and transfer group discuss items to a Forum module category');

    if (!jrCore_module_is_active('jrForum')) {
        jrCore_page_notice('error', 'Forum module not active');
        jrCore_page_display();
        exit;
    }
    if (!jrCore_module_is_active('jrGroup')) {
        jrCore_page_notice('error', 'Group module not active');
        jrCore_page_display();
        exit;
    }
    if (!jrCore_module_is_active('jrGroupDiscuss')) {
        jrCore_page_notice('error', 'GroupDiscuss module not active');
        jrCore_page_display();
        exit;
    }

    if (isset($_post['gid']) && jrCore_checktype($_post['gid'], 'number_nz')) {
        // Form init
        $_tmp = array(
            'submit_value'  => 'Transfer',
            'cancel'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
            'submit_prompt' => 'Are you sure you want to transfer the selected group discuss items?',
            'submit_modal'  => 'update',
            'modal_width'   => 800,
            'modal_height'  => 400,
            'modal_note'    => 'Please be patient whilst the selected group discuss items are transferred'
        );
        jrCore_form_create($_tmp);
    }
    // Get all groups with discuss items
    $_s  = array(
        "search"      => array("group_jrGroupDiscuss_item_count > 0"),
        "order_by"    => array('group_title' => 'asc'),
        "return_keys" => array('_item_id', 'group_title', 'group_jrGroupDiscuss_item_count', 'profile_name'),
        "limit"       => jrCore_db_get_datastore_item_count('jrGroup')
    );
    $_rt = jrCore_db_search_items('jrGroup', $_s);
    if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
        // Select group
        $_gopts = array('0' => '-');
        foreach ($_rt['_items'] as $rt) {
            $_gopts["{$rt['_item_id']}"] = "{$rt['group_title']} ({$rt['group_jrGroupDiscuss_item_count']}) [{$rt['profile_name']}]";
        }
        $_tmp = array(
            'name'     => 'group_id',
            'label'    => 'select group',
            'help'     => 'select the group whose discuss items you want to transfer',
            'options'  => $_gopts,
            'value'    => $_post['gid'],
            'type'     => 'select',
            'validate' => 'printable',
            'required' => true,
            'onchange' => "var v=this.options[this.selectedIndex].value; jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/transfer_discuss_to_forum/gid='+ v)"
        );
        jrCore_form_field_create($_tmp);
        if (isset($_post['gid']) && jrCore_checktype($_post['gid'], 'number_nz')) {
            // Get all discuss items for this group
            $_s  = array(
                "search"   => array("discuss_group_id = {$_post['gid']}"),
                "order_by" => array('discuss_title' => 'asc'),
                "limit"    => jrCore_db_get_datastore_item_count('jrGroupDiscuss')
            );
            $_rt = jrCore_db_search_items('jrGroupDiscuss', $_s);
            if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
                // Select group discuss item(s)
                $_dopts = array();
                foreach ($_rt['_items'] as $rt) {
                    $_dopts["{$rt['_item_id']}"] = "{$rt['discuss_title']} by '{$rt['profile_name']}' ({$rt['discuss_comment_count']} comments)";
                }
                $_tmp = array(
                    'name'     => 'discuss_ids',
                    'label'    => 'select discuss items',
                    'sublabel' => '(multi-select)',
                    'help'     => 'select the group discuss item(s) you want to transfer',
                    'options'  => $_dopts,
                    'type'     => 'select_multiple'
                );
                jrCore_form_field_create($_tmp);
                // Select target forum category
                $tbl = jrCore_db_table_name('jrForum', 'category');
                $req = "SELECT * FROM {$tbl} ORDER BY cat_order ASC";
                $_ct = jrCore_db_query($req, 'NUMERIC');
                if ($_ct && is_array($_ct) && count($_ct) > 0) {
                    $_copts = array('0' => '-');
                    foreach ($_ct as $ct) {
                        $pn                        = jrCore_db_get_item_key('jrProfile', $ct['cat_profile_id'], 'profile_name');
                        $_copts["{$ct['cat_id']}"] = "{$ct['cat_title']} [{$pn}]";
                    }
                    $_tmp = array(
                        'name'     => 'cat_id',
                        'label'    => 'target forum category',
                        'help'     => 'select the target forum category for the transfer',
                        'options'  => $_copts,
                        'type'     => 'select',
                        'validate' => 'printable'
                    );
                    jrCore_form_field_create($_tmp);
                    // Delete after transfer option
                    $_tmp = array(
                        'name'    => "delete_after_xfer",
                        'label'   => 'Delete After Transfer',
                        'help'    => 'If checked, after transfer the selected group discuss items will be deleted',
                        'type'    => 'checkbox',
                        'default' => 'on'
                    );
                    jrCore_form_field_create($_tmp);
                    // Email owner
                    $_tmp = array(
                        'name'    => "email_owner",
                        'label'   => 'Email Owner',
                        'help'    => 'If checked, the owner of the Group Discuss item will be emailed that the item has been moved',
                        'type'    => 'checkbox',
                        'default' => 'off'
                    );
                    jrCore_form_field_create($_tmp);
                }
                else {
                    jrCore_page_notice('error', 'No target forum categories found');
                }
            }
            else {
                jrCore_page_notice('error', 'No group discussions found');
            }
        }
    }
    else {
        jrCore_page_notice('error', 'No groups found');
    }
    jrCore_page_display();
}

//------------------------------
// transfer_discuss_to_forum_save
//------------------------------
function view_jrGroupDiscuss_transfer_discuss_to_forum_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Do some checking
    if (!jrCore_checktype($_post['group_id'], 'number_nz')) {
        jrCore_form_modal_notice('complete', "ERROR: Invalid group id");
        exit;
    }
    if (!isset($_post['discuss_ids']) || !is_array($_post['discuss_ids']) || count($_post['discuss_ids']) == 0) {
        jrCore_form_modal_notice('complete', "ERROR: No discuss item(s) selected");
        exit;
    }
    if (!jrCore_checktype($_post['cat_id'], 'number_nz')) {
        jrCore_form_modal_notice('complete', "ERROR: Target forum category not selected");
        exit;
    }
    // All good - Do the transfer(s)
    $tbl = jrCore_db_table_name('jrForum', 'category');
    $req = "SELECT * FROM {$tbl} WHERE cat_id = '{$_post['cat_id']}' LIMIT 1";
    $_ct = jrCore_db_query($req, 'SINGLE');
    if (is_array($_ct) && strlen($_ct['cat_title']) > 0) {
        $_gids = array();
        $tcnt  = 0;
        foreach ($_post['discuss_ids'] as $did) {
            if ($_rt = jrCore_db_get_item('jrGroupDiscuss', $did, true)) {
                // Already transferred to this forum category?
                if (jrCore_db_get_item_by_key('jrForum', 'forum_xfer_key', "{$_ct['cat_profile_id']}|{$_ct['cat_title']}|{$did}")) {
                    jrCore_form_modal_notice('update', "ERROR: Topic '{$_rt['discuss_title']}' already transferred to this forum category");
                    continue;
                }
                // Do the transfer - Create new forum topic
                $_tmp  = array(
                    'forum_cat'             => $_ct['cat_title'],
                    'forum_cat_url'         => $_ct['cat_title_url'],
                    'forum_group_id'        => '', // For now
                    'forum_pinned'          => 'off',
                    'forum_post_count'      => 1,
                    'forum_profile_id'      => $_ct['cat_profile_id'],
                    'forum_text'            => $_rt['discuss_description'],
                    'forum_title'           => $_rt['discuss_title'],
                    'forum_title_url'       => $_rt['discuss_title_url'],
                    'forum_updated'         => $_rt['_updated'],
                    'forum_updated_user_id' => $_rt['_user_id'],
                    'forum_xfer_key'        => "{$_ct['cat_profile_id']}|{$_ct['cat_title']}|{$did}"
                );
                $_core = array(
                    '_profile_id' => $_rt['_profile_id'],
                    '_user_id'    => $_rt['_user_id'],
                    '_created'    => $_rt['_created'],
                    '_updated'    => $_rt['_updated']
                );
                if ($id = jrCore_db_create_item('jrForum', $_tmp, $_core, true, true)) {
                    // Set forum group id
                    jrCore_db_update_item('jrForum', $id, array('forum_group_id' => $id));
                    // Increment category topic count
                    $tcnt++;
                    // Add to array for possible discuss item deletion
                    $_gids[] = $did;
                    // Copy comments over
                    $_s  = array(
                        "search" => array(
                            "comment_module = jrGroupDiscuss",
                            "comment_item_id = {$did}"
                        ),
                        "limit"  => 10000
                    );
                    $_mt = jrCore_db_search_items('jrComment', $_s);
                    if ($_mt && is_array($_mt['_items']) && count($_mt['_items']) > 0) {
                        $_last = false;
                        $_tmp  = array();
                        $_core = array();
                        $i     = 0;
                        foreach ($_mt['_items'] as $mt) {
                            $_tmp["{$i}"]['forum_group_id']   = $id;
                            $_tmp["{$i}"]['forum_profile_id'] = $_ct['cat_profile_id'];
                            $_tmp["{$i}"]['forum_text']       = $mt['comment_text'];
                            $_core["{$i}"]['_created']        = $mt['_created'];
                            $_core["{$i}"]['_updated']        = $mt['_created'];
                            $_core["{$i}"]['_profile_id']     = $mt['_profile_id'];
                            $_core["{$i}"]['_user_id']        = $mt['_user_id'];
                            $_last                            = $mt;
                            $i++;
                        }
                        $fcnt = count(jrCore_db_create_multiple_items('jrForum', $_tmp, $_core));
                        jrCore_db_increment_key('jrForum', $id, 'forum_post_count', $fcnt);
                        $udata   = jrCore_db_escape(json_encode(jrCore_db_get_item('jrUser', $_last['_user_id'])));
                        $updated = intval($_last['_updated']);
                        jrCore_db_query("UPDATE {$tbl} SET cat_updated = '{$updated}', cat_update_user = '{$udata}' WHERE cat_id = '{$_ct['cat_id']}' LIMIT 1");
                    }
                    // Email owner
                    if (isset($_post['email_owner']) && $_post['email_owner'] == 'on') {
                        $_rep = array(
                            'source_module' => 'jrGroupDiscuss',
                            'target_module' => 'jrForum',
                            'source_type'   => 'Group Discuss item',
                            'target_type'   => 'Forum Topic',
                            'target_url'    => $_conf['jrCore_base_url'] . '/' . jrCore_db_get_item_key('jrProfile', $_ct['cat_profile_id'], 'profile_url') . '/' . jrCore_get_module_url('jrForum') . '/' . $_ct['cat_title_url'] . '/' . $id . '/' . $_rt['discuss_title_url'],
                            '_source'       => $_rt,
                            '_target'       => jrCore_db_get_item('jrForum', $id)
                        );
                        list($sub, $msg) = jrCore_parse_email_templates('jrGroupDiscuss', 'transferred', $_rep);
                        jrCore_send_email(jrCore_db_get_item_key('jrUser', jrCore_db_get_item_key('jrProfile', $_ct['cat_profile_id'], '_user_id'), 'user_email'), $sub, $msg);
                        jrCore_form_modal_notice('update', "Group Discuss item '{$_rt['discuss_title']}' transferred - Owner notified");
                    }
                    else {
                        jrCore_form_modal_notice('update', "Group Discuss item '{$_rt['discuss_title']}' transferred");
                    }
                }
                else {
                    jrCore_form_modal_notice('update', "Error: Forum Category '{$_rt['discuss_title']}' not created");
                }
            }
            else {
                jrCore_form_modal_notice('update', "Error: Unable to get group discuss item data for ID:'{$did}'");
            }
        }
        // Increment category topic count
        if (jrCore_checktype($tcnt, 'number_nz')) {
            $ctc = intval($_ct['cat_topic_count'] + $tcnt);
            jrCore_db_query("UPDATE {$tbl} SET cat_topic_count = '{$ctc}' WHERE cat_id = '{$_ct['cat_id']}' LIMIT 1");
        }
        // Are we deleting the discuss items?
        if (isset($_post['delete_after_xfer']) && $_post['delete_after_xfer'] == 'on' && count($_gids) > 0) {
            jrCore_db_delete_multiple_items('jrGroupDiscuss', $_gids);
            jrCore_form_modal_notice('update', count($_gids) . ' group discuss items deleted');
        }
        jrCore_form_modal_notice('complete', "SUCCESS: {$tcnt} group discuss items transferred");
    }
    else {
        jrCore_form_modal_notice('complete', "ERROR: Unable to get target category data");
    }
}
