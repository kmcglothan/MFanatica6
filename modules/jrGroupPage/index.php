<?php
/**
 * Jamroom Group Pages module
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
function view_jrGroupPage_create($_post, $_user, $_conf)
{
    jrUser_session_require_login();

    if (!isset($_post['group_id']) || !jrCore_checktype($_post['group_id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid group_id - please try again');
    }
    $_gr = jrCore_db_get_item('jrGroup', $_post['group_id']);
    if (!$_gr || !is_array($_gr)) {
        jrCore_notice_page('error', 'Invalid group_id - please try again (2)');
    }
    if (!jrUser_is_admin() && !jrProfile_is_profile_owner($_gr['_profile_id']) && jrGroup_get_user_config('jrGroupPage', 'allowed', $_gr, $_user['_user_id']) != 'on') {
        jrUser_not_authorized();
    }

    // Start our create form
    jrCore_page_banner(2);

    // Form init
    $_tmp = array(
        'submit_value' => 2,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'  => 'npage_group_id',
        'type'  => 'hidden',
        'value' => (int) $_post['group_id']
    );
    jrCore_form_field_create($_tmp);

    // Title
    $_tmp = array(
        'name'      => 'npage_title',
        'label'     => 3,
        'help'      => 4,
        'type'      => 'text',
        'validate'  => 'printable',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // Body
    $_tmp = array(
        'name'      => 'npage_body',
        'label'     => 15,
        'help'      => 16,
        'type'      => 'editor',
        'validate'  => 'allowed_html',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrGroupPage_create_save($_post, &$_user, &$_conf)
{
    jrUser_session_require_login();
    jrCore_form_validate($_post);

    if (!isset($_post['npage_group_id']) || !jrCore_checktype($_post['npage_group_id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid group_id - please try again');
        jrCore_form_result();
    }
    $_gr = jrCore_db_get_item('jrGroup', $_post['npage_group_id']);
    if (!$_gr || !is_array($_gr)) {
        jrCore_set_form_notice('error', 'Invalid group_id - please try again (2)');
        jrCore_form_result();
    }
    if (!jrUser_is_admin() && !jrProfile_is_profile_owner($_gr['_profile_id']) && jrGroup_get_user_config('jrGroupPage', 'allowed', $_gr, $_user['_user_id']) != 'on') {
        jrCore_set_form_notice('error', 'you do not have permissions to create this item');
        jrCore_form_result();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('jrGroupPage', 'create', $_post);

    // Add in our SEO URL names
    $_rt['npage_title_url'] = jrCore_url_string($_rt['npage_title']);

    // If an admin, set correct _profile_id
    $_core = null;
    if (jrUser_is_admin()) {
        $_core = array('_profile_id' => jrUser_get_profile_home_key('_profile_id'));
    }

    $pid = jrCore_db_create_item('jrGroupPage', $_rt, $_core);
    if (!$pid) {
        jrCore_set_form_notice('error', 5);
        jrCore_form_result();
    }

    // Save any uploaded media files added in by our
    // TODO: See if this works
    jrCore_save_all_media_files('jrGroupPage', 'create', $_gr['_profile_id'], $pid);

    // Add to Actions...
    if ($_gr['group_private'] != 'on') {
        $_rt['group_profile_url'] = $_gr['profile_url'];
        jrCore_run_module_function('jrAction_save', 'create', 'jrGroupPage', $pid, $_rt);
    }
    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_gr['profile_url']}/{$_post['module_url']}/{$pid}/{$_rt['npage_title_url']}");
}

//------------------------------
// update
//------------------------------
function view_jrGroupPage_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 6);
    }
    $_rt = jrCore_db_get_item('jrGroupPage', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', 7);
    }
    $_gr = jrCore_db_get_item('jrGroup', $_rt['npage_group_id']);
    if (!$_gr || !is_array($_gr)) {
        jrCore_notice_page('error', 'Invalid group_id - please try again (2)');
    }
    if (!jrUser_is_admin() && !jrProfile_is_profile_owner($_gr['_profile_id']) && jrGroup_get_user_config('jrGroupPage', 'allowed', $_gr, $_user['_user_id']) != 'on') {
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
        'name'      => 'npage_title',
        'label'     => 3,
        'help'      => 4,
        'type'      => 'text',
        'validate'  => 'printable',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // Body
    $_tmp = array(
        'name'      => 'npage_body',
        'label'     => 15,
        'help'      => 16,
        'type'      => 'editor',
        'validate'  => 'allowed_html',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrGroupPage_update_save($_post, &$_user, &$_conf)
{
    jrUser_session_require_login();
    jrCore_form_validate($_post);

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 6);
        jrCore_form_result('referrer');
    }
    // Get data
    $_rt = jrCore_db_get_item('jrGroupPage', $_post['id']);
    if (!$_rt || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 7);
        jrCore_form_result();
    }
    $_gr = jrCore_db_get_item('jrGroup', $_rt['npage_group_id']);
    if (!$_gr || !is_array($_gr)) {
        jrCore_set_form_notice('error', 'Invalid group_id - please try again (2)');
        jrCore_form_result();
    }
    if (!jrUser_is_admin() && !jrProfile_is_profile_owner($_gr['_profile_id']) && jrGroup_get_user_config('jrGroupPage', 'allowed', $_gr, $_user['_user_id']) != 'on') {
        jrCore_set_form_notice('error', 'you do not have permissions to create this item');
        jrCore_form_result();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrGroupPage', 'update', $_post);

    // Add in our SEO URL names
    $_sv['npage_title_url'] = jrCore_url_string($_sv['npage_title']);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrGroupPage', $_post['id'], $_sv);

    // Save any uploaded media file
    jrCore_save_all_media_files('jrGroupPage', 'update', $_gr['_profile_id'], $_post['id']);

    // Add to Actions...
    if ($_gr['group_private'] != 'on') {
        $_rt['group_profile_url'] = $_gr['profile_url'];
        jrCore_run_module_function('jrAction_save', 'update', 'jrGroupPage', $_post['id'], $_rt);
    }
    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_gr['profile_url']}/{$_post['module_url']}/{$_post['id']}/{$_sv['npage_title_url']}");
}

//------------------------------
// delete
//------------------------------
function view_jrGroupPage_delete($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 6);
        jrCore_form_result('referrer');
    }
    $_rt = jrCore_db_get_item('jrGroupPage', $_post['id']);
    if (!$_rt || !is_array($_rt)) {
        jrCore_notice_page('error', 7);
    }
    $_gr = jrCore_db_get_item('jrGroup', $_rt['npage_group_id']);
    if (!$_gr || !is_array($_gr)) {
        jrCore_notice_page('error', 'Invalid group_id - please try again (2)');
    }
    if (!jrUser_is_admin() && !jrProfile_is_profile_owner($_gr['_profile_id']) && jrGroup_get_user_config('jrGroupPage', 'allowed', $_gr, $_user['_user_id']) != 'on') {
        jrCore_notice_page('error', 'you do not have permissions to create this item');
    }
    jrCore_db_delete_item('jrGroupPage', $_post['id']);
    jrProfile_reset_cache();
    $murl = jrCore_get_module_url('jrGroup');
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_gr['profile_url']}/{$murl}/{$_gr['_item_id']}/{$_gr['group_title_url']}");
}

//------------------------------
// group_config
//------------------------------
function view_jrGroupPage_group_config($_post, $_user, $_conf)
{
    jrUser_master_only();

    // Get all 'orphaned' group pages
    $_s = array(
        'search'                       => array(
            'npage_group_id = 0'
        ),
        'order_by'                     => array(
            'npage_title' => 'asc'
        ),
        'return_keys'                  => array('_item_id', 'npage_title', 'profile_name'),
        'exclude_jrUser_keys'          => true,
        'exclude_jrProfile_quota_keys' => true,
        'nocache'                      => true,
        'ignore_pending'               => true,
        'privacy_check'                => false,
        'limit'                        => jrCore_db_get_datastore_item_count('jrGroupPage')
    );
    $_pt = jrCore_db_search_items('jrGroupPage', $_s);
    if ($_pt && is_array($_pt['_items']) && count($_pt['_items']) > 0) {
        // Form init
        $_tmp = array(
            'submit_value'     => 'assign page(s) to selected group',
            'cancel'           => jrCore_is_profile_referrer(),
            'submit_prompt'    => "Are you sure you want assign the selected page(s) to the group? NOTE that if you have selected 'Stand Alone Page' the process is irreversable.",
            'form_ajax_submit' => false
        );
        jrCore_form_create($_tmp);

        $_opt = array();
        foreach ($_pt['_items'] as $pt) {
            $_opt["{$pt['_item_id']}"] = "{$pt['npage_title']} ({$pt['profile_name']})";
        }
        $_tmp = array(
            'name'     => 'page_id',
            'label'    => 'page',
            'sublabel' => '(Use the cntrl key to select multiple pages)',
            'help'     => 'Select the page(s) you would like to assign to a new Profile Group.  The page creator profile is shown in the parenthesis.',
            'type'     => 'select_multiple',
            'options'  => $_opt,
            'required' => true
        );
        jrCore_form_field_create($_tmp);

        // Get all groups
        $_s = array(
            'order_by'                     => array(
                'group_title' => 'asc'
            ),
            'return_keys'                  => array('_item_id', 'group_title', 'profile_name'),
            'exclude_jrUser_keys'          => true,
            'exclude_jrProfile_quota_keys' => true,
            'nocache'                      => true,
            'ignore_pending'               => true,
            'privacy_check'                => false,
            'limit'                        => jrCore_db_get_datastore_item_count('jrGroup')
        );
        $_gt = jrCore_db_search_items('jrGroup', $_s);
        if ($_gt && is_array($_gt['_items']) && count($_gt['_items']) > 0) {
            $_opt2 = array();
            foreach ($_gt['_items'] as $gt) {
                $_opt2["{$gt['_item_id']}"] = "{$gt['group_title']} ({$pt['profile_name']})";
            }
            if (jrCore_module_is_active('jrPage')) {
                $_opt2 = array(0 => 'Stand Alone Page') + $_opt2;
            }
            $_tmp = array(
                'name'     => 'group_id',
                'label'    => 'group',
                'help'     => 'Select the Profile Group you would like to assign the Page to',
                'type'     => 'select',
                'options'  => $_opt2,
                'required' => true
            );
            jrCore_form_field_create($_tmp);
        }
        else {
            jrCore_page_note("No target groups found");
        }
    }
    else {
        jrCore_page_note("No orphaned group pages found");
    }
    jrCore_page_display();
}

//------------------------------
// group_config_save
//------------------------------
function view_jrGroupPage_group_config_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Do some checking
    if (!jrCore_checktype($_post['group_id'], 'number_nn')) {
        jrCore_set_form_notice('error', 'No Target Group selected');
        jrCore_form_result();
    }
    if (!isset($_post['page_id']) || !is_array($_post['page_id']) || count($_post['page_id']) == 0) {
        jrCore_set_form_notice('error', 'No Group Page(s) selected');
        jrCore_form_result();
    }

    if (jrCore_checktype($_post['group_id'], 'number_nz')) {
        // Get target group members
        $_members = array();
        $tbl      = jrCore_db_table_name('jrGroup', 'member');
        $req      = "SELECT * FROM {$tbl} WHERE `member_group_id` = {$_post['group_id']}";
        $_rt      = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt) && count($_rt) > 0) {
            foreach ($_rt as $rt) {
                $_members["{$rt['member_user_id']}"] = true;
            }
        }

        // Assign page(s) to target group
        foreach ($_post['page_id'] as $pid) {
            if (jrCore_db_update_item('jrGroupPage', $pid, array('npage_group_id' => $_post['group_id']))) {
                jrCore_db_increment_key('jrGroup', $_post['group_id'], 'group_jrGroupPage_item_count', 1);
                // Get GroupPage user
                $uid = jrCore_db_get_item_key('jrGroupPage', $pid, '_user_id');
                // Check for target group membership
                if (!isset($_members["{$uid}"])) {
                    $_members["{$uid}"] = true;
                    $req                = "INSERT INTO {$tbl} (member_created, member_user_id, member_group_id, member_status) VALUES (UNIX_TIMESTAMP(), '{$uid}', '{$_post['group_id']}', '1')";
                    jrCore_db_query($req);
                }
                // Check any commentees for target group membership
                $_s  = array(
                    "search"      => array(
                        "comment_module = jrGroupPage",
                        "comment_item_id = {$pid}"
                    ),
                    "return_keys" => array('_user_id'),
                    "limit"       => 10000
                );
                $_ct = jrCore_db_search_items('jrComment', $_s);
                if ($_ct && is_array($_ct['_items']) && count($_ct['_items']) > 0) {
                    foreach ($_ct['_items'] as $ct) {
                        if (!isset($_members["{$ct['_user_id']}"])) {
                            $_members["{$ct['_user_id']}"] = true;
                            $req                           = "INSERT INTO {$tbl} (member_created, member_user_id, member_group_id, member_status) VALUES (UNIX_TIMESTAMP(), '{$ct['_user_id']}', '{$_post['group_id']}', '1')";
                            jrCore_db_query($req);
                        }
                    }
                }
            }
        }
        jrCore_set_form_notice('success', 'The Group Pages were successfully assigned');
    }
    else {
        // Copy Group Pages (and their comments) to jrPage
        foreach ($_post['page_id'] as $pid) {
            $_gp  = jrCore_db_get_item('jrGroupPage', $pid);
            if (!jrCore_db_get_item_by_key('jrPage', 'page_title', $_gp['npage_title'])) {
                // Copy GroupPage item to a Page item
                $_tmp = array(
                    'page_title'     => $_gp['npage_title'],
                    'page_title_url' => jrCore_url_string($_gp['npage_title']),
                    'page_location'  => 1,
                    'page_body'      => $_gp['npage_body'],
                    'page_header'    => 'on',
                    'page_feature'   => 'on'
                );
                $_core = array(
                    '_created'    => $_gp['_created'],
                    '_updated'    => $_gp['_updated'],
                    '_profile_id' => $_gp['_profile_id'],
                    '_user_id'    => $_gp['_user_id'],
                );
                $id = jrCore_db_create_item('jrPage', $_tmp, $_core);
                if (jrCore_checktype($id, 'number_nz')) {
                    // Update any comments to the new Page item
                    $_s = array(
                        "search" => array(
                            "comment_module = jrGroupPage",
                            "comment_item_id = {$pid}"
                        ),
                        'exclude_jrUser_keys'          => true,
                        'exclude_jrProfile_quota_keys' => true,
                        "limit"                        => 25000
                    );
                    $_rt = jrCore_db_search_items('jrComment', $_s);
                    if (is_array($_rt['_items'])) {
                        $_up = array(
                            'comment_module'  => 'jrPage',
                            'comment_item_id' => $id
                        );
                        $_ups = array();
                        foreach ($_rt['_items'] as $rt) {
                            $_ups["{$rt['_item_id']}"] = $_up;
                        }
                        jrCore_db_update_multiple_items('jrComment', $_ups);
                    }
                    // Delete the GroupPage item
                    jrCore_db_delete_item('jrGroupPage', $pid);
                }
            }
        }
        jrCore_set_form_notice('success', 'Stand Alone Page(s) successfully created');
    }
    jrCore_form_result();
}
