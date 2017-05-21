<?php
/**
 * Jamroom Files module
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
function view_jrFile_create($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFile');
    jrProfile_check_disk_usage();

    // Get profile quota info
    $_qt = jrProfile_get_quota($_user['profile_quota_id']);
    if (!isset($_qt) || !is_array($_qt)) {
        jrCore_set_form_notice('error', 'Profile quota invalid');
        jrCore_form_result();
    }

    // Start our create form
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
    );
    $tmp = jrCore_page_banner_item_jumper('jrFile', 'file_title', $_sr, 'create', 'update');
    jrCore_page_banner(1, $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 2,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    // File Title
    $_tmp = array(
        'name'      => 'file_title',
        'label'     => 3,
        'help'      => 4,
        'type'      => 'text',
        'validate'  => 'printable',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // File
    $_tmp = array(
        'name'       => 'file_file',
        'label'      => 5,
        'help'       => 6,
        'text'       => 7,
        'type'       => 'file',
        'extensions' => $_qt['quota_jrFile_allowed_file_types'],
        'required'   => true
    );
    jrCore_form_field_create($_tmp);

    // File Image
    $_tmp = array(
        'name'     => 'file_image',
        'label'    => 19,
        'help'     => 20,
        'text'     => 21,
        'type'     => 'image',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrFile_create_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrFile');

    // Get profile quota info
    $_qt = jrProfile_get_quota($_user['profile_quota_id']);
    if (!isset($_qt) || !is_array($_qt)) {
        jrCore_set_form_notice('error', 'Profile quota invalid');
        jrCore_form_result();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('jrFile', 'create', $_post);

    // Add in our SEO URL names
    $_rt['file_title_url'] = jrCore_url_string($_rt['file_title']);

    // $fid will be the INSERT_ID (_item_id) of the created item
    $fid = jrCore_db_create_item('jrFile', $_rt);
    if (!$fid) {
        jrCore_set_form_notice('error', 'Unable to create dataStore item');
        jrCore_form_result();
    }
    // Save any uploaded media files added in by our
    jrCore_save_all_media_files('jrFile', 'create', $_user['user_active_profile_id'], $fid);

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'create', 'jrFile', $fid);

    jrCore_form_delete_session();
    jrProfile_reset_cache();

    // redirect to the list file page
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}");
}

//------------------------------
// update
//------------------------------
function view_jrFile_update($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFile');

    // Get profile quota info
    $_qt = jrProfile_get_quota($_user['profile_quota_id']);
    if (!isset($_qt) || !is_array($_qt)) {
        jrCore_set_form_notice('error', 'Profile quota invalid');
        jrCore_form_result();
    }

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid ID received');
    }
    $_rt = jrCore_db_get_item('jrFile', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', 'Invalid ID - unable to retrieve document from DataStore');
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Start output
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
    );
    $tmp = jrCore_page_banner_item_jumper('jrFile', 'file_title', $_sr, 'create', 'update');
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

    // File Title
    $_tmp = array(
        'name'      => 'file_title',
        'label'     => 3,
        'help'      => 4,
        'type'      => 'text',
        'validate'  => 'printable',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // File
    $_tmp = array(
        'name'       => 'file_file',
        'label'      => 5,
        'help'       => 6,
        'text'       => 7,
        'type'       => 'file',
        'value'      => $_rt,
        'extensions' => $_qt['quota_jrFile_allowed_file_types'],
        'required'   => false
    );
    jrCore_form_field_create($_tmp);

    // File Image
    $_tmp = array(
        'name'     => 'file_image',
        'label'    => 19,
        'help'     => 20,
        'text'     => 21,
        'type'     => 'image',
        'value'    => $_rt,
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrFile_update_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFile');

    // Validate all incoming posted data
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrFile');

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid ID received');
        jrCore_form_result();
    }

    // Get data
    $_rt = jrCore_db_get_item('jrFile', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 'Invalid ID - unable to retrieve document from DataStore');
        jrCore_form_result();
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrFile', 'update', $_post);

    // Add in our SEO URL names
    $_sv['file_title_url'] = jrCore_url_string($_sv['file_title']);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrFile', $_post['id'], $_sv);

    // Save any uploaded media file
    jrCore_save_all_media_files('jrFile', 'update', $_user['user_active_profile_id'], $_post['id']);

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'update', 'jrFile', $_post['id']);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    // redirect to the list file page
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}");
}

//------------------------------
// delete
//------------------------------
function view_jrFile_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrFile');

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid ID');
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item('jrFile', $_post['id']);

    // Make sure the calling user has permission to delete this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    // Delete item and any associated files
    jrCore_db_delete_item('jrFile', $_post['id']);
    jrProfile_reset_cache();
    jrCore_form_result('delete_referrer');
}
