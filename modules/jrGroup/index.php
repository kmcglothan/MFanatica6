<?php
/**
 * Jamroom Groups module
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
// create
//------------------------------
function view_jrGroup_create($_post, $_user, $_conf)
{
    // Must be logged in to create a new group
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGroup');

    // Start our create form
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
    );
    $tmp = jrCore_page_banner_item_jumper('jrGroup', 'group_title', $_sr, 'create', 'update');
    jrCore_page_banner(2, $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 2,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    // Group Title
    $_tmp = array(
        'name'     => 'group_title',
        'label'    => 3,
        'help'     => 4,
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Group Description
    $_tmp = array(
        'name'     => 'group_description',
        'label'    => 14,
        'help'     => 15,
        'type'     => 'editor',
        'validate' => 'allowed_html',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Group Image
    $_tmp = array(
        'name'     => 'group_image',
        'label'    => 22,
        'help'     => 23,
        'text'     => 24,
        'type'     => 'image',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Group Applicants
    $_tmp = array(
        'name'          => 'group_applicants',
        'label'         => 18,
        'help'          => 19,
        'type'          => 'checkbox',
        'validate'      => 'onoff',
        'default'       => 'on',
        'form_designer' => false,
        'required'      => true
    );
    jrCore_form_field_create($_tmp);

    // Group Comment Wall
    if (jrCore_module_is_active('jrComment') || jrCore_module_is_active('jrDisqus')) {
        $_tmp = array(
            'name'          => 'group_wall',
            'label'         => 67,
            'help'          => 68,
            'type'          => 'checkbox',
            'validate'      => 'onoff',
            'default'       => 'on',
            'form_designer' => false,
            'required'      => true
        );
        jrCore_form_field_create($_tmp);
    }

    // Group Private
    $_tmp = array(
        'name'          => 'group_private',
        'label'         => 20,
        'help'          => 21,
        'type'          => 'checkbox',
        'validate'      => 'onoff',
        'default'       => 'off',
        'form_designer' => false,
        'required'      => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrGroup_create_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrGroup');

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('jrGroup', 'create', $_post);

    // Add in our SEO URL names
    $_rt['group_title_url'] = jrCore_url_string($_rt['group_title']);

    // $xid will be the INSERT_ID (_item_id) of the created item
    $xid = jrCore_db_create_item('jrGroup', $_rt);
    if (!$xid) {
        jrCore_set_form_notice('error', 5);
        jrCore_form_result();
    }

    // Save any uploaded media files added in by our module
    jrCore_save_all_media_files('jrGroup', 'create', $_user['user_active_profile_id'], $xid);

    // Add profile owner(s) to the member to table
    $_owners = jrProfile_get_owner_info($_user['user_active_profile_id']);
    if ($_owners && is_array($_owners) && count($_owners) > 0) {
        $tbl = jrCore_db_table_name('jrGroup', 'member');
        $req = "INSERT INTO {$tbl} (member_created, member_user_id, member_group_id, member_status) VALUES";
        foreach ($_owners as $owner) {
            $req .= " (UNIX_TIMESTAMP(), '{$owner['_user_id']}', '{$xid}', '1'),";
        }
        $req = substr($req, 0, -1);
        if ($cnt = jrCore_db_query($req, 'COUNT')) {
            jrCore_db_update_item('jrGroup', $xid, array('group_member_count' => $cnt));
        }
    }

    // Add to Actions
    jrCore_run_module_function('jrAction_save', 'create', 'jrGroup', $xid, $_rt);

    jrCore_form_delete_session();
    jrProfile_reset_cache();

    // redirect to the actual group page, not the update page.
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$xid}/{$_rt['group_title_url']}");
}

//------------------------------
// update
//------------------------------
function view_jrGroup_update($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGroup');

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 6);
    }
    $_rt = jrCore_db_get_item('jrGroup', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', 7);
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Start output
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
    );
    $tmp = jrCore_page_banner_item_jumper('jrGroup', 'group_title', $_sr, 'create', 'update');
    jrCore_page_banner(8, $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 9,
        'cancel'       => jrCore_is_profile_referrer(),
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // id
    $_tmp = array(
        'name'     => 'id',
        'type'     => 'hidden',
        'value'    => $_post['id'],
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    // Group Title
    $_tmp = array(
        'name'     => 'group_title',
        'label'    => 3,
        'help'     => 4,
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Group Description
    $_tmp = array(
        'name'     => 'group_description',
        'label'    => 14,
        'help'     => 15,
        'type'     => 'editor',
        'validate' => 'allowed_html',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Group Image
    $_tmp = array(
        'name'     => 'group_image',
        'label'    => 22,
        'help'     => 23,
        'text'     => 24,
        'type'     => 'image',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Group Allow Applicants
    $_tmp = array(
        'name'          => 'group_applicants',
        'label'         => 18,
        'help'          => 19,
        'type'          => 'checkbox',
        'validate'      => 'onoff',
        'default'       => 'on',
        'form_designer' => false,
        'required'      => true
    );
    jrCore_form_field_create($_tmp);

    // Group Comment Wall
    if (jrCore_module_is_active('jrComment') || jrCore_module_is_active('jrDisqus')) {
        $_tmp = array(
            'name'          => 'group_wall',
            'label'         => 67,
            'help'          => 68,
            'type'          => 'checkbox',
            'validate'      => 'onoff',
            'default'       => 'on',
            'form_designer' => false,
            'required'      => true
        );
        jrCore_form_field_create($_tmp);
    }

    // Group Private
    $_tmp = array(
        'name'          => 'group_private',
        'label'         => 20,
        'help'          => 21,
        'type'          => 'checkbox',
        'validate'      => 'onoff',
        'default'       => 'off',
        'form_designer' => false,
        'required'      => true
    );
    jrCore_form_field_create($_tmp);

    // Group Featured
    $_tmp = array(
        'name'     => 'group_featured',
        'label'    => 16,
        'help'     => 17,
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'default'  => 'off',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrGroup_update_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();

    // Validate all incoming posted data
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrGroup');

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 6);
        jrCore_form_result('referrer');
    }

    // Get data
    $_rt = jrCore_db_get_item('jrGroup', $_post['id']);
    if (!is_array($_rt)) {
        // Item does not exist....
        jrCore_notice_page('error', 7);
        jrCore_form_result('referrer');
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrGroup', 'update', $_post);

    // Add in our SEO URL names
    $_sv['group_title_url'] = jrCore_url_string($_sv['group_title']);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrGroup', $_post['id'], $_sv);

    // Save any uploaded media file
    jrCore_save_all_media_files('jrGroup', 'update', $_user['user_active_profile_id'], $_post['id']);

    // Add to Actions
    jrCore_run_module_function('jrAction_save', 'update', 'jrGroup', $_post['id'], $_rt);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$_post['id']}/{$_sv['group_title_url']}");
}

//------------------------------
// delete
//------------------------------
function view_jrGroup_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGroup');

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 6);
        jrCore_form_result('referrer');
    }
    $_rt = jrCore_db_get_item('jrGroup', $_post['id']);

    // Make sure the calling user has permission to delete this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    // Delete item and any associated files
    jrCore_db_delete_item('jrGroup', $_post['id']);
    jrProfile_reset_cache();
    jrCore_form_result('delete_referrer');
}

//------------------------------
// button
//------------------------------
function view_jrGroup_button($_post, $_user, $_conf)
{
    // [_1] => join (the action to take)
    // [_2] => 178 (the group id)

    jrUser_session_require_login();
    jrCore_validate_location_url();

    // Check that we get good IDs
    if (!isset($_post['_2']) || !jrCore_checktype($_post['_2'], 'number_nz')) {
        $_rs = array('error' => 'invalid group_id - please try again');
        jrCore_json_response($_rs);
    }

    // Validate Group
    $_gr = jrCore_db_get_item('jrGroup', $_post['_2']);
    if (!$_gr || !is_array($_gr)) {
        $_rs = array('error' => 'invalid group - unable to find group data - please try again');
        jrCore_json_response($_rs);
    }
    $gid = (int) $_post['_2'];
    $_ln = jrUser_load_lang_strings();

    // Join a Group
    if (isset($_post['_1']) && $_post['_1'] == 'join') {

        // Make sure it allows applications
        if (!isset($_gr['group_applicants']) || $_gr['group_applicants'] != 'on') {
            $_rs = array('error' => 'This group is not open for new members - sorry!');
            jrCore_json_response($_rs);
        }

        // Make sure they are not already a member
        if (isset($_gr['group_member']["{$_user['_user_id']}"])) {
            if ($_gr['group_member']["{$_user['_user_id']}"]['member_status'] == 0) {
                // Pending
                $_rs = array('error' => $_ln['jrGroup'][75]);
            }
            else {
                $_rs = array('error' => $_ln['jrGroup'][74]);
            }
            jrCore_json_response($_rs);
        }

        // Looks good - add user
        $sts = 1; // active
        $cls = 'cancel';
        $tag = $_ln['jrGroup'][27];
        $prm = $_ln['jrGroup'][29];
        if (isset($_gr['group_private']) && $_gr['group_private'] == 'on') {
            $sts = 0; // pending
            $cls = 'pending';
            $tag = $_ln['jrGroup'][28];
            $prm = $_ln['jrGroup'][30];
        }
        $tbl = jrCore_db_table_name('jrGroup', 'member');
        $req = "INSERT INTO {$tbl} (member_created, member_user_id, member_group_id, member_status) VALUES (UNIX_TIMESTAMP(), '{$_user['_user_id']}', '{$gid}', '{$sts}')";
        if (jrCore_db_query($req, 'COUNT')) {
            // Send action email to group owner
            if ($sts == 0) { // pending
                // Get group owners
                $_owners = jrProfile_get_owner_info($_gr['_profile_id']);
                if ($_owners && is_array($_owners)) {
                    $_rep = array(
                        '_applicant' => $_user,
                        '_group'     => $_gr
                    );
                    list($sub, $msg) = jrCore_parse_email_templates('jrGroup', 'application', $_rep);
                    foreach ($_owners as $_o) {
                        if ($_o['_user_id'] != $_user['_user_id']) {
                            jrUser_notify($_o['_user_id'], $_user['_user_id'], 'jrGroup', 'pending_application', $sub, $msg);
                        }
                    }
                }
            }
            else {
                // member is not pending - increment group_member_count
                jrCore_db_increment_key('jrGroup', $gid, 'group_member_count', 1);
            }
            jrProfile_reset_cache($_gr['_profile_id'], 'jrGroup');

            // Success
            $_rs = array(
                'class'  => "group_{$cls}",
                'value'  => jrCore_entity_string($tag),
                'prompt' => jrCore_entity_string($prm),
                'action' => 'cancel'
            );
            jrCore_json_response($_rs);
        }
        else {
            $_rs = array('error' => 'An error was encountered submitting your group application - please try again');
            jrCore_json_response($_rs);
        }
    }
    else {

        // Leave a group or Cancel a pending application
        if (isset($_gr['group_member']["{$_user['_user_id']}"])) {

            // Remove the user
            $tbl = jrCore_db_table_name('jrGroup', 'member');
            $req = "DELETE FROM {$tbl} WHERE `member_group_id` = '{$gid}' AND `member_user_id` = '{$_user['_user_id']}' LIMIT 1";
            if (jrCore_db_query($req, 'COUNT')) {

                // decrement group_member_count
                jrCore_db_decrement_key('jrGroup', $gid, 'group_member_count', 1);

                // Send action email to group owners
                $_owners = jrProfile_get_owner_info($_gr['_profile_id']);
                if ($_owners && is_array($_owners)) {
                    $_rep = array(
                        '_applicant' => $_user,
                        '_group'     => $_gr
                    );
                    list($sub, $msg) = jrCore_parse_email_templates('jrGroup', 'leaving', $_rep);
                    foreach ($_owners as $_o) {
                        if ($_o['_user_id'] != $_user['_user_id']) {
                            jrUser_notify($_o['_user_id'], $_user['_user_id'], 'jrGroup', 'user_leaving', $sub, $msg);
                        }
                    }
                }
            }
            else {
                $_rs = array('error' => 'An error was encountered removing your group application - please try again');
                jrCore_json_response($_rs);
            }
        }
        jrProfile_reset_cache($_gr['_profile_id'], 'jrGroup');

        // Success
        $_rs = array(
            'class'  => 'group_join',
            'value'  => jrCore_entity_string($_ln['jrGroup'][26]),
            'prompt' => jrCore_entity_string($_ln['jrGroup'][31]),
            'action' => 'join'
        );
        jrCore_json_response($_rs);
    }
}

//------------------------------
// user_config
//------------------------------
function view_jrGroup_user_config($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['group_id']) || !jrCore_checktype($_post['group_id'], 'number_nz')) {
        jrCore_notice_page('error', 'invalid group_id - please try again');
    }
    if (!isset($_post['user_id']) || !jrCore_checktype($_post['user_id'], 'number_nz')) {
        jrCore_notice_page('error', 'invalid user_id - please try again');
    }
    $_gr = jrCore_db_get_item('jrGroup', $_post['group_id']);
    if (!$_gr || !is_array($_gr)) {
        jrCore_notice_page('error', 'invalid group_id - please try again (2)');
    }
    if (!jrUser_can_edit_item($_gr)) {
        jrUser_not_authorized();
    }
    if (!isset($_gr['group_member']["{$_post['user_id']}"])) {
        jrCore_notice_page('error', 'invalid user_id - please try again (2)');
    }
    $_cf = $_gr['group_member']["{$_post['user_id']}"];
    $_us = jrCore_db_get_item('jrUser', $_post['user_id']);
    $_ln = jrUser_load_lang_strings();

    $btn = false;
    switch ($_us['user_group']) {
        case 'master':
        case 'admin':
            break;
        default:
            $_pr = jrProfile_get_user_linked_profiles($_us['_user_id']);
            if (!isset($_pr["{$_gr['_profile_id']}"])) {
                $btn = jrCore_page_button('delete', $_ln['jrGroup'][60], "if(confirm('{$_ln['jrGroup'][61]}')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_user_save/group_id={$_post['group_id']}/user_id={$_us['_user_id']}') }");
            }
            break;
    }
    jrCore_page_banner("{$_ln['jrGroup'][62]} &quot;{$_us['user_name']}&quot;", $btn);

    if (isset($_cf['member_status']) && $_cf['member_status'] == 0) { // pending
        jrCore_set_form_notice('error', 59, false);
        jrCore_get_form_notice();
    }

    // Form init
    $_tmp = array(
        'submit_value' => $_ln['jrCore'][72],
        'cancel'       => jrCore_is_profile_referrer(),
        'values'       => $_cf
    );
    jrCore_form_create($_tmp);

    // group_id
    $_tmp = array(
        'name'     => 'group_id',
        'type'     => 'hidden',
        'value'    => $_post['group_id'],
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    // user_id
    $_tmp = array(
        'name'     => 'user_id',
        'type'     => 'hidden',
        'value'    => $_post['user_id'],
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    $_arg = array('crop' => 'auto');
    $html = jrImage_get_image_src('jrUser', 'user_image', $_post['user_id'], 'small', $_arg) . " &nbsp; {$_us['user_name']} (<a href=\"{$_conf['jrCore_base_url']}/{$_us['profile_url']}\">@{$_us['profile_name']}</a>)";
    jrCore_page_custom($html, 'image');

    // User Status
    $temp = (isset($_cf['member_status']) && $_cf['member_status'] == 0) ? 'off' : 'on';
    $_tmp = array(
        'name'     => 'status',
        'label'    => 63,
        'help'     => 64,
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'value'    => $temp,
        'default'  => 'off',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Send out trigger so other group modules can add in Config options
    // NOTE: config options must begin with MODULE_config_
    $_us['user_id'] = $_post['user_id'];
    $_us            = array_merge($_gr['group_member']["{$_us['user_id']}"], $_us);
    jrCore_trigger_event('jrGroup', 'user_config', $_us, $_gr);
    jrCore_page_display();
}

//------------------------------
// user_config_save
//------------------------------
function view_jrGroup_user_config_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['group_id']) || !jrCore_checktype($_post['group_id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid group_id - please try again');
        jrCore_form_result();
    }
    if (!isset($_post['user_id']) || !jrCore_checktype($_post['user_id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid user_id - please try again');
        jrCore_form_result();
    }
    $_gr = jrCore_db_get_item('jrGroup', $_post['group_id']);
    if (!$_gr || !is_array($_gr)) {
        jrCore_set_form_notice('error', 'invalid group_id - please try again (2)');
        jrCore_form_result();
    }
    if (!jrUser_can_edit_item($_gr)) {
        jrUser_not_authorized();
    }
    $_us = jrCore_db_get_item('jrUser', $_post['user_id']);
    if (!$_us || !is_array($_us)) {
        jrCore_set_form_notice('error', 'invalid user_id - please try again (2)');
        jrCore_form_result();
    }
    if (!isset($_gr['group_member']["{$_post['user_id']}"])) {
        jrCore_set_form_notice('error', 'invalid user_id - please try again (3)');
        jrCore_form_result();
    }
    $sts = $_gr['group_member']["{$_post['user_id']}"]['member_status'];

    // Round up other config options
    $_up = array();
    $_mf = jrCore_get_registered_module_features('jrGroup', 'group_support');
    if ($_mf && is_array($_mf)) {
        foreach ($_mf as $mod => $ignore) {
            foreach ($_post as $k => $v) {
                if (strpos($k, "{$mod}_config_") === 0) {
                    $_up[$k] = $v;
                }
            }
        }
    }
    $member_more = '';
    if ($_up && is_array($_up) && count($_up) > 0) {
        $member_more = json_encode($_up);
    }

    // Update member table
    $member_status = (isset($_post['status']) && $_post['status'] == 'on') ? 1 : 0; // active : pending
    $tbl           = jrCore_db_table_name('jrGroup', 'member');
    $req           = "UPDATE {$tbl} SET `member_status` = '{$member_status}', `member_more` = '{$member_more}'  WHERE `member_user_id` = '{$_post['user_id']}' AND `member_group_id` = '{$_post['group_id']}' LIMIT 1";
    jrCore_db_query($req);

    // Notify the User that their application was accepted if they
    // were previously pending and are now ACTIVE
    if ($sts == 0 && $_post['status'] == 'on') {
        $_rep = array(
            '_applicant' => $_us,
            '_group'     => $_gr
        );
        list($sub, $msg) = jrCore_parse_email_templates('jrGroup', 'accepted', $_rep);
        jrUser_notify($_post['user_id'], 0, 'jrGroup', 'accepted_application', $sub, $msg);
    }

    jrGroup_validate_group_member_count($_post['group_id']);
    jrCore_form_delete_session();
    jrProfile_reset_cache($_gr['_profile_id'], 'jrGroup');

    // Redirect to Group Index
    jrCore_location("{$_conf['jrCore_base_url']}/{$_gr['profile_url']}/{$_post['module_url']}/{$_gr['_item_id']}/{$_gr['group_title_url']}");
}

//------------------------------
// delete_user_save
//------------------------------
function view_jrGroup_delete_user_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    if (!isset($_post['group_id']) || !jrCore_checktype($_post['group_id'], 'number_nz')) {
        jrCore_notice_page('error', 'invalid group_id - please try again');
    }
    if (!isset($_post['user_id']) || !jrCore_checktype($_post['user_id'], 'number_nz')) {
        jrCore_notice_page('error', 'invalid user_id - please try again');
    }
    $_gr = jrCore_db_get_item('jrGroup', $_post['group_id']);
    if (!$_gr || !is_array($_gr)) {
        jrCore_notice_page('error', 'invalid group_id - please try again (2)');
    }
    if (!jrUser_can_edit_item($_gr)) {
        jrUser_not_authorized();
    }
    if (isset($_gr['group_member']["{$_post['user_id']}"])) {
        // Remove the user
        $tbl = jrCore_db_table_name('jrGroup', 'member');
        $req = "DELETE FROM {$tbl} WHERE `member_group_id` = '{$_post['group_id']}' AND `member_user_id` = '{$_post['user_id']}' LIMIT 1";
        jrCore_db_query($req, 'COUNT');
        jrGroup_validate_group_member_count($_post['group_id']);
        jrProfile_reset_cache($_gr['_profile_id'], 'jrGroup');
    }
    jrCore_location("{$_conf['jrCore_base_url']}/{$_gr['profile_url']}/{$_post['module_url']}/{$_post['group_id']}/{$_gr['group_title_url']}");
}

//------------------------------
// my_groups
//------------------------------
function view_jrGroup_my_groups($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_page_banner(66);

    // Get groups followed
    $tbl = jrCore_db_table_name('jrGroup', 'member');
    $req = "SELECT * FROM {$tbl} WHERE `member_user_id` = '{$_user['_user_id']}'";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt) && count($_rt) > 0) {
        $_gid = array();
        foreach ($_rt as $rt) {
            $_gid["{$rt['member_group_id']}"] = $rt['member_group_id'];
        }
        $gid = implode(',', $_gid);
        $_s  = array(
            "search"    => array("_item_id IN {$gid}"),
            'pagebreak' => 24,
            'page'      => 1
        );
        if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
            $_s['page'] = (int) $_post['p'];
        }
        $_rt = jrCore_db_search_items('jrGroup', $_s);
        if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
            $html = jrCore_parse_template('my_groups.tpl', $_rt, 'jrGroup');
            $html .= jrCore_parse_template('list_pager.tpl', $_rt, 'jrCore');
            jrCore_page_custom($html);
        }
    }
    else {
        $_ln = jrUser_load_lang_strings();
        jrCore_page_note($_ln['jrGroup'][36]);
    }
    jrCore_page_display();
}

//------------------------------
// private_notice
//------------------------------
function view_jrGroup_private_notice($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    $_rt = jrCore_db_get_item('jrGroup', $_post['_1']);
    if (!$_rt || !is_array($_rt)) {
        jrCore_page_not_found();
    }

    $_ln = jrUser_load_lang_strings();

    // Applicants not allowed?
    if (!isset($_rt['group_applicants']) || $_rt['group_applicants'] == 'off') {
        jrCore_set_form_notice('error', '<strong>&quot;' . $_rt['group_title'] . "&quot;</strong> {$_ln['jrGroup'][73]}", false);
    }

    // See if the viewing user is already a member
    elseif (isset($_rt['group_member']["{$_user['_user_id']}"])) {
        // See if they are pending
        if (isset($_rt['group_member']["{$_user['_user_id']}"]['member_status']) && $_rt['group_member']["{$_user['_user_id']}"]['member_status'] == 1) {
            $text = $_ln['jrGroup'][74];
        }
        else {
            $text = $_ln['jrGroup'][75];
        }
        $btn = jrCore_page_button('continue', $_ln['jrCore'][87], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_rt['profile_url']}/{$_post['module_url']}')");
        jrCore_set_form_notice('notice', "{$text}<br><br>" . $btn, false);
    }

    else {
        $btn = jrCore_page_button('join', $_ln['jrGroup'][76], "jrGroupButton('join', '{$_post['_1']}', 'group_join')");
        $btn .= '&nbsp;' . jrCore_page_button('cancel', 'Cancel', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_rt['profile_url']}/{$_post['module_url']}');");
        jrCore_set_form_notice('error', '<strong>&quot;' . $_rt['group_title'] . "&quot;</strong> {$_ln['jrGroup'][73]}<br><br>{$_ln['jrGroup'][77]}<br><br>" . $btn, false);
    }
    jrCore_get_form_notice();
    jrCore_page_display();
}

//------------------------------
// Tool: Transfer group discussion(s) to another group
//------------------------------
function view_jrGroup_transfer_discuss($_post, $_user, $_conf)
{
    jrUser_master_only();
    @ini_set('max_execution_time', 120);
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrGroup');
    jrCore_page_banner('Transfer Group Discussions');

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

    // Get all groups
    $_rt = array(
        'exclude_jrUser_keys'          => true,
        'exclude_jrProfile_quota_keys' => true,
        'return_keys'                  => array('_item_id', 'group_title', 'group_jrGroupDiscuss_item_count', 'profile_name'),
        'order_by'                     => array('group_title' => 'asc'),
        'privacy_check'                => false,
        'quota_check'                  => false,
        'ignore_pending'               => true,
        'limit'                        => jrCore_db_get_datastore_item_count('jrGroup')
    );
    $_rt = jrCore_db_search_items('jrGroup', $_rt);
    if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {

        // Select groups
        $_gopts1 = array('0' => '-');
        $_gopts2 = array('0' => '-');
        foreach ($_rt['_items'] as $rt) {
            if (isset($_post['gid']) && jrCore_checktype($_post['gid'], 'number_nz') && $_post['gid'] != $rt['_item_id']) {
                $_gopts1["{$rt['_item_id']}"] = "{$rt['group_title']} [{$rt['profile_name']}]";
            }
            if (isset($rt['group_jrGroupDiscuss_item_count']) && jrCore_checktype($rt['group_jrGroupDiscuss_item_count'], 'number_nz')) {
                $_gopts2["{$rt['_item_id']}"] = "{$rt['group_title']} ({$rt['group_jrGroupDiscuss_item_count']}) [{$rt['profile_name']}]";
            }
        }
        $_tmp = array(
            'name'     => 'group_id',
            'label'    => 'select group',
            'help'     => 'select the group whose discuss items you want to transfer',
            'options'  => $_gopts2,
            'value'    => $_post['gid'],
            'type'     => 'select',
            'validate' => 'printable',
            'required' => true,
            'onchange' => "var v=this.options[this.selectedIndex].value; jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/transfer_discuss/gid='+ v)"
        );
        jrCore_form_field_create($_tmp);

        if (isset($_post['gid']) && jrCore_checktype($_post['gid'], 'number_nz')) {
            // Get all discuss items for this group
            $_rt = array(
                'search'         => array(
                    "discuss_group_id = {$_post['gid']}"
                ),
                'skip_triggers'  => true,
                'return_keys'    => array('_item_id', 'discuss_title'),
                'order_by'       => array('discuss_title' => 'asc'),
                'privacy_check'  => false,
                'quota_check'    => false,
                'ignore_pending' => true,
                'limit'          => jrCore_db_get_datastore_item_count('jrGroupDiscuss')
            );
            $_rt = jrCore_db_search_items('jrGroupDiscuss', $_rt);
            if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {

                // Select group discuss item(s)
                $_dopts = array();
                foreach ($_rt['_items'] as $rt) {
                    $_dopts["{$rt['_item_id']}"] = $rt['discuss_title'];
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

                jrCore_page_note("Note that any users who have created or commented on selected discuss items above will be made (pending) members of the target group selected below");

                // Select target group
                $_tmp = array(
                    'name'     => 'target_group_id',
                    'label'    => 'target group',
                    'help'     => 'select the target group for the transfer',
                    'options'  => $_gopts1,
                    'type'     => 'select',
                    'validate' => 'printable'
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
// transfer_discuss_save
//------------------------------
function view_jrGroup_transfer_discuss_save($_post, $_user, $_conf)
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
        foreach ($_post['discuss_ids'] as $did) {
            if ($_rt = jrCore_db_get_item('jrGroupDiscuss', $did)) {
                $updated = '';
                $_tmp    = array(
                    'discuss_group_id' => $_post['target_group_id']
                );
                $_core   = array(
                    '_updated' => $_rt['_created']
                );
                if (jrCore_db_update_item('jrGroupDiscuss', $did, $_tmp, $_core)) {
                    // Increment/Decrement group counts
                    jrCore_db_increment_key('jrGroup', $_post['target_group_id'], 'group_jrGroupDiscuss_item_count', 1);
                    jrCore_db_decrement_key('jrGroup', $_rt['discuss_group_id'], 'group_jrGroupDiscuss_item_count', 1);
                    // Check for target group membership
                    if (!isset($_members["{$_rt['_user_id']}"])) {
                        $_members["{$_rt['_user_id']}"] = true;
                        $req                            = "INSERT INTO {$tbl} (member_created, member_user_id, member_group_id, member_status) VALUES (UNIX_TIMESTAMP(), '{$_rt['_user_id']}', '{$_post['target_group_id']}', '1')";
                        jrCore_db_query($req);
                    }
                    // Check any commentees for target group membership
                    $_s  = array(
                        "search"      => array(
                            "comment_module = jrGroupDiscuss",
                            "comment_item_id = {$did}"
                        ),
                        "order_by"    => array('_item_id' => 'asc'),
                        "return_keys" => array('_user_id', '_created'),
                        "limit"       => 10000
                    );
                    $_ct = jrCore_db_search_items('jrComment', $_s);
                    if ($_ct && is_array($_ct['_items']) && count($_ct['_items']) > 0) {
                        foreach ($_ct['_items'] as $ct) {
                            $updated = $ct['_created'];
                            if (!isset($_members["{$ct['_user_id']}"])) {
                                $_members["{$ct['_user_id']}"] = true;
                                $req                           = "INSERT INTO {$tbl} (member_created, member_user_id, member_group_id, member_status) VALUES (UNIX_TIMESTAMP(), '{$ct['_user_id']}', '{$_post['target_group_id']}', '1')";
                                jrCore_db_query($req);
                            }
                        }
                    }
                    // Email owner
                    if (isset($_post['email_owner']) && $_post['email_owner'] == 'on') {
                        $_rep = array(
                            'type'    => 'Group Discussion',
                            'module'  => 'jrGroupDiscuss',
                            '_source' => $_rt,
                            '_target' => $_target_group,
                        );
                        list($sub, $msg) = jrCore_parse_email_templates('jrGroup', 'transferred', $_rep);
                        jrCore_send_email(jrCore_db_get_item_key('jrUser', $_rt['_user_id'], 'user_email'), $sub, $msg);
                        jrCore_form_modal_notice('update', "Group Discuss item '{$_rt['discuss_title']}' transferred - Owner notified");
                    }
                    else {
                        jrCore_form_modal_notice('update', "Group Discuss item '{$_rt['discuss_title']}' transferred");
                    }
                    // Set discuss item _updated to that of last comment
                    if (jrCore_checktype($updated, 'number_nn') && isset($_conf['jrGroupDiscuss_recently_active']) && $_conf['jrGroupDiscuss_recently_active'] != 'off') {
                        jrCore_db_update_item('jrGroupDiscuss', $did, array(), array('_updated' => $updated));
                    }
                    $cnt++;
                    jrCore_logger('INF', "Group Discuss item '{$_rt['discuss_title']}' transferred to group '{$_target_group['group_title']}'");
                }
                else {
                    jrCore_form_modal_notice('update', "Error: Unable to update group discuss item ID:'{$did}'");
                }
            }
            else {
                jrCore_form_modal_notice('update', "Error: Unable to get group discuss item data for ID:'{$did}'");
            }
        }
        jrCore_form_modal_notice('complete', "Success: {$cnt} Group Discuss items transferred");
    }
    else {
        jrCore_form_modal_notice('complete', "ERROR: Unable to get target group data");
    }
}

//------------------------------
// Tool: Transfer group page(s) to another group
//------------------------------
function view_jrGroup_transfer_page($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrGroup');
    jrCore_page_banner('Select and transfer group page items to another Group');

    if (!jrCore_module_is_active('jrGroupPage')) {
        jrCore_page_notice('error', 'GroupPage module not active');
        jrCore_page_display();
        exit;
    }

    if (isset($_post['gid']) && jrCore_checktype($_post['gid'], 'number_nz')) {
        // Form init
        $_tmp = array(
            'submit_value'  => 'Transfer',
            'cancel'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
            'submit_prompt' => 'Are you sure you want to transfer the selected group page items?',
            'submit_modal'  => 'update',
            'modal_width'   => 800,
            'modal_height'  => 400,
            'modal_note'    => 'Please be patient whilst the selected group page items are transferred'
        );
        jrCore_form_create($_tmp);
    }

    // Get all groups
    $_s  = array(
        "order_by"    => array('group_title' => 'asc'),
        "return_keys" => array('_item_id', 'group_title', 'group_jrGroupPage_item_count', 'profile_name'),
        "limit"       => jrCore_db_get_datastore_item_count('jrGroup')
    );
    $_rt = jrCore_db_search_items('jrGroup', $_s);
    if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
        // Select groups
        $_gopts1 = array('0' => '-');
        $_gopts2 = array('0' => '-');
        foreach ($_rt['_items'] as $rt) {
            if (isset($_post['gid']) && jrCore_checktype($_post['gid'], 'number_nz') && $_post['gid'] != $rt['_item_id']) {
                $_gopts1["{$rt['_item_id']}"] = "{$rt['group_title']} [{$rt['profile_name']}]";
            }
            if (isset($rt['group_jrGroupPage_item_count']) && jrCore_checktype($rt['group_jrGroupPage_item_count'], 'number_nz')) {
                $_gopts2["{$rt['_item_id']}"] = "{$rt['group_title']} ({$rt['group_jrGroupPage_item_count']}) [{$rt['profile_name']}]";
            }
        }
        $_tmp = array(
            'name'     => 'group_id',
            'label'    => 'select group',
            'help'     => 'select the group whose page items you want to transfer',
            'options'  => $_gopts2,
            'value'    => $_post['gid'],
            'type'     => 'select',
            'validate' => 'printable',
            'required' => true,
            'onchange' => "var v=this.options[this.selectedIndex].value; jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/transfer_page/gid='+ v)"
        );
        jrCore_form_field_create($_tmp);

        if (isset($_post['gid']) && jrCore_checktype($_post['gid'], 'number_nz')) {
            // Get all page items for this group
            $_s  = array(
                "search"   => array("npage_group_id = {$_post['gid']}"),
                "order_by" => array('npage_title' => 'asc'),
                "limit"    => jrCore_db_get_datastore_item_count('jrGroupPage')
            );
            $_rt = jrCore_db_search_items('jrGroupPage', $_s);
            if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
                // Select group page item(s)
                $_popts = array();
                foreach ($_rt['_items'] as $rt) {
                    $_popts["{$rt['_item_id']}"] = "{$rt['npage_title']} ({$rt['profile_name']})";
                }
                $_tmp = array(
                    'name'     => 'npage_ids',
                    'label'    => 'select page items',
                    'sublabel' => '(multi-select)',
                    'help'     => 'select the group page item(s) you want to transfer',
                    'options'  => $_popts,
                    'type'     => 'select_multiple'
                );
                jrCore_form_field_create($_tmp);

                jrCore_page_note("Note that any users who have created or commented on selected page items above will be made (pending) members of the target group selected below");

                // Select target group
                $_tmp = array(
                    'name'     => 'target_group_id',
                    'label'    => 'target group',
                    'help'     => 'select the target group for the transfer',
                    'options'  => $_gopts1,
                    'type'     => 'select',
                    'validate' => 'printable'
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
                jrCore_page_notice('error', 'No group pages found');
            }
        }
    }
    else {
        jrCore_page_notice('error', 'No groups with pages found');
    }
    jrCore_page_display();
}

//------------------------------
// transfer_page_save
//------------------------------
function view_jrGroup_transfer_page_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Do some checking
    if (!jrCore_checktype($_post['group_id'], 'number_nz')) {
        jrCore_form_modal_notice('complete', "ERROR: Invalid group id");
        exit;
    }
    if (!isset($_post['npage_ids']) || !is_array($_post['npage_ids']) || count($_post['npage_ids']) == 0) {
        jrCore_form_modal_notice('complete', "ERROR: No page item(s) selected");
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
        foreach ($_post['npage_ids'] as $npid) {
            if ($_rt = jrCore_db_get_item('jrGroupPage', $npid)) {
                $_tmp = array(
                    'npage_group_id' => $_post['target_group_id']
                );
                if (jrCore_db_update_item('jrGroupPage', $npid, $_tmp)) {
                    // Increment/Decrement group counts
                    jrCore_db_increment_key('jrGroup', $_post['target_group_id'], 'group_jrGroupPage_item_count', 1);
                    jrCore_db_decrement_key('jrGroup', $_rt['npage_group_id'], 'group_jrGroupPage_item_count', 1);
                    // Check for target group membership
                    if (!isset($_members["{$_rt['_user_id']}"])) {
                        $_members["{$_rt['_user_id']}"] = true;
                        $req                            = "INSERT INTO {$tbl} (member_created, member_user_id, member_group_id, member_status) VALUES (UNIX_TIMESTAMP(), '{$_rt['_user_id']}', '{$_post['target_group_id']}', '1')";
                        jrCore_db_query($req);
                    }
                    // Check any commentees for target group membership
                    $_s  = array(
                        "search"      => array(
                            "comment_module = jrGroupPage",
                            "comment_item_id = {$npid}"
                        ),
                        "return_keys" => array('_user_id'),
                        "limit"       => 10000
                    );
                    $_ct = jrCore_db_search_items('jrComment', $_s);
                    if ($_ct && is_array($_ct['_items']) && count($_ct['_items']) > 0) {
                        foreach ($_ct['_items'] as $ct) {
                            if (!isset($_members["{$ct['_user_id']}"])) {
                                $_members["{$ct['_user_id']}"] = true;
                                $req                           = "INSERT INTO {$tbl} (member_created, member_user_id, member_group_id, member_status) VALUES (UNIX_TIMESTAMP(), '{$ct['_user_id']}', '{$_post['target_group_id']}', '1')";
                                jrCore_db_query($req);
                            }
                        }
                    }
                    // Email owner
                    if (isset($_post['email_owner']) && $_post['email_owner'] == 'on') {
                        $_rep = array(
                            'type'    => 'Group Page',
                            'module'  => 'jrGroupPage',
                            '_source' => $_rt,
                            '_target' => $_target_group,
                        );
                        list($sub, $msg) = jrCore_parse_email_templates('jrGroup', 'transferred', $_rep);
                        jrCore_send_email(jrCore_db_get_item_key('jrUser', $_rt['_user_id'], 'user_email'), $sub, $msg);
                        jrCore_form_modal_notice('update', "Group Page item '{$_rt['npage_title']}' transferred - Owner notified");
                    }
                    else {
                        jrCore_form_modal_notice('update', "Group Page item '{$_rt['npage_title']}' transferred");
                    }
                    $cnt++;
                    jrCore_logger('INF', "Group Page item '{$_rt['npage_title']}' transferred to group '{$_target_group['group_title']}'");
                }
                else {
                    jrCore_form_modal_notice('update', "Error: Unable to update group page item ID:'{$npid}'");
                }
            }
            else {
                jrCore_form_modal_notice('update', "Error: Unable to get group page item data for ID:'{$npid}'");
            }
        }
        jrCore_form_modal_notice('complete', "Success: {$cnt} Group Page items transferred");
    }
    else {
        jrCore_form_modal_notice('complete', "ERROR: Unable to get target group data");
    }
}
