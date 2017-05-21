<?php
/**
 * Jamroom Playlist Ads module
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
function view_jrPlaylistAds_create($_post, $_user, $_conf)
{
    // Admin only
    jrUser_master_only();

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrPlaylistAds');
    jrCore_page_banner('Playlist Ads - Create');

    // Form init
    $_tmp = array(
        'submit_value' => 'create',
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    // Title
    $_tmp = array(
        'name'     => 'playlistads_title',
        'label'    => 'Ad Title',
        'help'     => 'Enter a title for this ad',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // File
    $_tmp = array(
        'name'       => 'playlistads_file',
        'label'      => 'Media File',
        'help'       => 'Select the audio (mp3) or video (flv/mp4) ad file',
        'text'       => 'select file',
        'type'       => 'file',
        'extensions' => 'mp3,flv,mp4',
        'required'   => true
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrPlaylistAds_create_save($_post, &$_user, &$_conf)
{
    // Admin only
    jrUser_master_only();

    // Validate
    jrCore_form_validate($_post);

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('jrPlaylistAds', 'create', $_post);

    // $fid will be the INSERT_ID (_item_id) of the created item
    $id = jrCore_db_create_item('jrPlaylistAds', $_rt);
    if (!$id) {
        jrCore_set_form_notice('error', 'Unable to create dataStore item');
        jrCore_form_result();
    }
    // Save any uploaded media files added in by our
    jrCore_save_all_media_files('jrPlaylistAds', 'create', $_user['user_active_profile_id'], $id);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    $murl = jrCore_get_module_url($_post['module']);
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$murl}/admin/tools");
}

//------------------------------
// select
//------------------------------
function view_jrPlaylistAds_select($_post, $_user, $_conf)
{
    // Admin only
    jrUser_master_only();

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrPlaylistAds');
    jrCore_page_banner('Playlist Ads - Select');

    $murl            = jrCore_get_module_url($_post['module']);
    $dat             = array();
    $dat[1]['title'] = 'Ad Title';
    $dat[1]['width'] = '40%;';
    $dat[2]['title'] = 'Type';
    $dat[2]['width'] = '40%;';
    $dat[3]['title'] = '';
    $dat[3]['width'] = '20%;';
    jrCore_page_table_header($dat);

    $_s  = array(
        "limit"                  => 100,
        "order_by"               => array(
            "playlistads_title" => 'asc'
        ),
        'exclude_jrUser_keys'    => true,
        'exclude_jrProfile_keys' => true
    );
    $_rt = jrCore_db_search_items('jrPlaylistAds', $_s);
    $_rt = $_rt['_items'];

    if (isset($_rt[0]) && is_array($_rt[0])) {
        foreach ($_rt as $rt) {
            $dat[1]['title'] = $rt['playlistads_title'];
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $rt['playlistads_file_extension'];
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = jrCore_page_button('update', 'Update', "location.href='{$_conf['jrCore_base_url']}/{$murl}/update/id={$rt['_item_id']}'");
            $dat[3]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat[1]['title'] = "No ads have been created";
        $dat[1]['class'] = 'center';
        $dat[2]['title'] = jrCore_page_button('back', 'Back', "location.href='{$_conf['jrCore_base_url']}/{$_post['option']}/admin/tools'");
        $dat[2]['class'] = 'center';
        $dat[3]['title'] = "";
        $dat[3]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// update
//------------------------------
function view_jrPlaylistAds_update($_post, $_user, $_conf)
{
    // Admin only
    jrUser_master_only();

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid item ID');
    }
    $_rt = jrCore_db_get_item('jrPlaylistAds', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', 'Something went wrong retrieving the datastore item');
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrPlaylistAds');
    $murl = jrCore_get_module_url($_post['module']);
    $tmp  = jrCore_page_button('delete', 'Delete this Ad', "location.href='{$_conf['jrCore_base_url']}/{$murl}/delete/id={$_post['id']}'");
    jrCore_page_banner('Playlist Ads - Update', $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 'update',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$murl}/select",
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

    // Title
    $_tmp = array(
        'name'     => 'playlistads_title',
        'label'    => 'Ad Title',
        'help'     => 'Enter a title for this ad',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // File
    $_tmp = array(
        'name'       => 'playlistads_file',
        'label'      => 'Media File',
        'help'       => 'Select the audio (mp3) or video (flv/mp4) ad file',
        'text'       => 'select file',
        'type'       => 'file',
        'extensions' => 'mp3,flv,mp4',
        'required'   => true
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrPlaylistAds_update_save($_post, &$_user, &$_conf)
{
    // Admin only
    jrUser_master_only();
    // Must be logged in

    // Validate all incoming posted data
    jrCore_form_validate($_post);

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid ID received');
        jrCore_form_result('referrer');
    }

    // Get data
    $_rt = jrCore_db_get_item('jrPlaylistAds', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 'Unable to create dataStore item');
        jrCore_form_result('referrer');
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrPlaylistAds', 'update', $_post);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrPlaylistAds', $_post['id'], $_sv);

    // Save any uploaded media file
    jrCore_save_all_media_files('jrPlaylistAds', 'update', $_user['user_active_profile_id'], $_post['id']);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    $murl = jrCore_get_module_url($_post['module']);
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$murl}/update/id={$_post['id']}");
}

//------------------------------
// delete
//------------------------------
function view_jrPlaylistAds_delete($_post, $_user, $_conf)
{
    // Admin only
    jrUser_master_only();

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid item ID');
        jrCore_form_result('referrer');
    }
    $_rt = jrCore_db_get_item('jrPlaylistAds', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_notice_page('error', 'Invalid item ID');
        jrCore_form_result('referrer');
    }
    // Delete item
    jrCore_db_delete_item('jrPlaylistAds', $_post['id']);
    $murl = jrCore_get_module_url($_post['module']);
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$murl}/admin/tools");
}
