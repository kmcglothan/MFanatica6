<?php
/**
 * Jamroom FAQ module
 *
 * copyright 2017 The Jamroom Network
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0.  Please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
 *
 * Jamroom may use modules and skins that are licensed by third party
 * developers, and licensed under a different license  - please
 * reference the individual module or skin license that is included
 * with your installation.
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
function view_jrFAQ_create($_post, $_user, $_conf)
{
    // Must be logged in to create a new faq
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFAQ');
    jrProfile_check_disk_usage();

    // Get language strings
    $_lang = jrUser_load_lang_strings();

    // Start our create form
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
    );
    $tmp = jrCore_page_banner_item_jumper('jrFAQ', 'faq_question', $_sr, 'create', 'update');
    jrCore_page_banner($_lang['jrFAQ'][2], $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 2,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    // FAQ Question
    $_tmp = array(
        'name'       => 'faq_question',
        'label'      => 3,
        'help'       => 4,
        'type'       => 'text',
        'validate'   => 'printable',
        'required'   => true,
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);

    // FAQ Category
    $_tmp = array(
        'name'     => 'faq_category',
        'label'    => 13,
        'sublabel' => 17,
        'help'     => 15,
        'type'     => 'select_and_text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // FAQ Answer
    $_tmp = array(
        'name'     => 'faq_answer',
        'label'    => 14,
        'help'     => 16,
        'type'     => 'editor',
        'validate' => 'allowed_html',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrFAQ_create_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrFAQ');

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('jrFAQ', 'create', $_post);

    // Add in our SEO URL names
    $_rt['faq_question_url'] = jrCore_url_string($_rt['faq_question']);
    $_rt['faq_category_url'] = jrCore_url_string($_rt['faq_category']);

    // $xid will be the INSERT_ID (_item_id) of the created item
    $xid = jrCore_db_create_item('jrFAQ', $_rt);
    if (!$xid) {
        jrCore_set_form_notice('error', 5);
        jrCore_form_result();
    }

    // Save any uploaded media files added in by our
    jrCore_save_all_media_files('jrFAQ', 'create', $_user['user_active_profile_id'], $xid);

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'create', 'jrFAQ', $xid);

    jrCore_form_delete_session();
    jrProfile_reset_cache($_user['user_active_profile_id'], 'jrFAQ');

    // redirect to the actual faq page, not the update page.
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}");
}

//------------------------------
// update
//------------------------------
function view_jrFAQ_update($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFAQ');

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 6);
    }
    $_rt = jrCore_db_get_item('jrFAQ', $_post['id']);
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
    $tmp = jrCore_page_banner_item_jumper('jrFAQ', 'faq_question', $_sr, 'create', 'update');
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

    // FAQ Question
    $_tmp = array(
        'name'     => 'faq_question',
        'label'    => 3,
        'help'     => 4,
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // FAQ Category
    $_tmp = array(
        'name'     => 'faq_category',
        'label'    => 13,
        'sublabel' => 17,
        'help'     => 15,
        'type'     => 'select_and_text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // FAQ Answer
    $_tmp = array(
        'name'     => 'faq_answer',
        'label'    => 14,
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
function view_jrFAQ_update_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();

    // Validate all incoming posted data
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrFAQ');

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 6);
        jrCore_form_result('referrer');
    }

    // Get data
    $_rt = jrCore_db_get_item('jrFAQ', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
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
    $_sv = jrCore_form_get_save_data('jrFAQ', 'update', $_post);

    // Add in our SEO URL names
    $_sv['faq_question_url'] = jrCore_url_string($_sv['faq_question']);
    $_sv['faq_category_url'] = jrCore_url_string($_sv['faq_category']);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrFAQ', $_post['id'], $_sv);

    // Save any uploaded media file
    jrCore_save_all_media_files('jrFAQ', 'update', $_user['user_active_profile_id'], $_post['id']);

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'update', 'jrFAQ', $_post['id']);

    jrCore_form_delete_session();
    jrProfile_reset_cache($_user['user_active_profile_id'], 'jrFAQ');
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}");
}

//------------------------------
// delete
//------------------------------
function view_jrFAQ_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFAQ');

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 6);
        jrCore_form_result('referrer');
    }
    $_rt = jrCore_db_get_item('jrFAQ', $_post['id']);

    // Make sure the calling user has permission to delete this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    // Delete item and any associated files
    jrCore_db_delete_item('jrFAQ', $_post['id']);
    jrProfile_reset_cache($_user['user_active_profile_id'], 'jrFAQ');
    jrCore_form_result('delete_referrer');
}

/**
 * Set display order for items on a profile
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 * @return bool
 */
function view_jrFAQ_item_display_order($_post, $_user, $_conf)
{
    jrUser_session_require_login();

    // Make sure this module has registered for item_order
    $_md = jrCore_get_registered_module_features('jrCore', 'item_order_support');
    if (!isset($_md["{$_post['module']}"])) {
        jrCore_notice_page('error', 'Invalid module - module is not registered for item_order support');
        return false;
    }

    // Get all items of this type
    $_sc = array(
        'search'         => array("_profile_id = {$_user['user_active_profile_id']}"),
        'return_keys'    => array('_item_id', 'faq_category', 'faq_question', 'faq_display_order'),
        'order_by'       => array("faq_display_order" => 'numerical_asc'),
        'skip_triggers'  => true,
        'privacy_check'  => false,
        'ignore_pending' => true,
        'limit'          => 500
    );
    $_rt = jrCore_db_search_items('jrFAQ', $_sc);
    if (!isset($_rt['_items']) || !is_array($_rt['_items'])) {
        jrCore_notice_page('notice', 'There are no items to set the order for!');
        return false;
    }

    $_ln = jrUser_load_lang_strings();
    $btn = jrCore_page_button('c', $_ln['jrCore'][87], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}')");
    jrCore_page_banner($_ln['jrCore'][83], $btn);

    $_cat = array();
    foreach ($_rt['_items'] as $_item) {
        $_cat[$_item['faq_category']][$_item['faq_display_order']] = $_item;
    }

    $tmp = '<ul class="item_sortable list nested_list">';
    // display each category
    foreach ($_cat as $cat => $_items) {
        $tmp .= "<li>" . $cat . '<ul class="item_sortable list ui-sortable">';
        foreach ($_items as $_item) {
            $tmp .= "<li data-id=\"{$_item['_item_id']}\">" . $_item['faq_question'] . "</li>\n";
        }
        $tmp .= "</ul></li>\n";
    }
    $tmp .= '</ul>';
    jrCore_page_custom($tmp, $_ln['jrCore'][83], $_ln['jrCore'][85]);

    $url = "{$_conf['jrCore_base_url']}/" . jrCore_get_module_url('jrCore') . "/item_display_order_update/m=jrFAQ/__ajax=1";
    $tmp = array('$(function() {
           $(\'.item_sortable\').sortable().bind(\'sortupdate\', function(event,ui) {
               var o = $(\'ul.item_sortable li\').map(function(){ return $(this).data("id"); }).get();
               $.post(\'' . $url . '\', { iid: o });
           });
       });');
    jrCore_create_page_element('javascript_footer_function', $tmp);
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}", $_ln['jrCore'][87]);
    return jrCore_page_display(true);
}
