<?php
/**
 * Jamroom Profiles module
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
// get_profile_users
//------------------------------
function view_jrProfile_get_profile_users($_post, $_user, $_conf)
{
    jrUser_admin_only();
    $_sc = array(
        'search'         => array(
            "user_name like {$_post['q']}%"
        ),
        'return_keys'    => array('_user_id', 'user_name'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'privacy_check'  => false,
        'limit'          => 12
    );
    $_rt = jrCore_db_search_items('jrUser', $_sc);
    $_sl = array();
    if ($_rt && is_array($_rt) && is_array($_rt['_items'])) {
        foreach ($_rt['_items'] as $_v) {
            $_sl["{$_v['_user_id']}"] = $_v['user_name'];
        }
    }
    return jrCore_live_search_results('profile_user_id', $_sl);
}

//------------------------------
// get_pulse_counts
//------------------------------
function view_jrProfile_get_pulse_counts($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        jrCore_json_response(array('error' => 'must be logged in'));
    }
    $pid = (int) jrUser_get_profile_home_key('_profile_id');
    $_rs = jrProfile_get_pulse_keys($pid);
    jrCore_json_response($_rs);
}

//------------------------------
// reset_pulse_count
//------------------------------
function view_jrProfile_reset_pulse_count($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        jrCore_json_response(array('error' => 'must be logged in'));
    }
    jrCore_validate_location_url();
    if (!isset($_post['key']) || strlen($_post['key']) === 0) {
        $_er = array('error' => 'invalid pulse key');
        jrCore_json_response($_er);
    }
    $pid = (int) jrUser_get_profile_home_key('_profile_id');
    list($mod, $key) = @explode('_', $_post['key'], 2);
    if (jrProfile_reset_pulse_key($pid, $mod, $key)) {
        $_rs = array('success' => 1);
        jrCore_json_response($_rs);
    }
    $_rs = array('error', 'unable to reset pulse key');
    jrCore_json_response($_rs);
}

//------------------------------
// list_profiles (power,multi)
//------------------------------
function view_jrProfile_list_profiles($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!jrUser_is_power_user() && !jrUser_is_multi_user()) {
        jrCore_location("{$_conf['jrCore_base_url']}/{$_user['profile_url']}");
    }

    // We're a power user or multi user and want to see the list of
    // profiles that we have access to - list them out here
    jrCore_page_banner(25);

    $tbl = jrCore_db_table_name('jrProfile', 'profile_link');
    $req = "SELECT profile_id FROM {$tbl} WHERE user_id = '" . intval($_user['_user_id']) . "'";
    $_rt = jrCore_db_query($req, 'profile_id');
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_notice_page('error', 'Unable to retrieve any profiles from the database - please try again');
    }

    $_rt = array(
        'search'        => array(
            '_profile_id in ' . implode(',', array_keys($_rt))
        ),
        'order_by'      => array(
            '_item_id' => 'asc'
        ),
        'return_keys'   => array('profile_url', '_profile_id', 'profile_name', 'profile_image_time'),
        'pagebreak'     => 20,
        'page'          => (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) ? intval($_post['p']) : 1,
        'privacy_check' => false
    );
    $_rt = jrCore_db_search_items('jrProfile', $_rt);
    if (!$_rt || !is_array($_rt['_items'])) {
        jrCore_notice_page('error', 'Unable to retrieve any profiles from the database - please try again');
    }

    $html = '<div class="profile_grid">';
    foreach ($_rt['_items'] as $_pr) {
        $_im  = array(
            'crop'  => 'auto',
            'alt'   => $_pr['profile_name'],
            'title' => $_pr['profile_name'],
            '_v'    => (isset($_pr['profile_image_time']) && $_pr['profile_image_time'] > 0) ? $_pr['profile_image_time'] : false
        );
        $isrc = jrImage_get_image_src('jrProfile', 'profile_image', $_pr['_profile_id'], 'medium', $_im);
        $html .= "<div class=\"item center\" style=\"float:left\"><a href=\"{$_conf['jrCore_base_url']}/{$_pr['profile_url']}\">{$isrc}</a><br><a href=\"{$_conf['jrCore_base_url']}/{$_pr['profile_url']}\">{$_pr['profile_name']}</a></div>";
    }
    $html .= '</div><div style="clear:both"></div>';

    jrCore_page_custom($html);

    jrCore_page_table_pager($_rt);

    jrCore_page_cancel_button('referrer');
    jrCore_page_display();
}

//------------------------------
// create
//------------------------------
function view_jrProfile_create($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    // Make sure user is allowed to create profiles....
    if (!jrUser_is_power_user()) {
        jrUser_not_authorized();
    }

    // If this a master admin creating...
    if (jrUser_is_master()) {
        jrCore_page_include_admin_menu();
        jrCore_page_admin_tabs('jrProfile');
    }

    // Show Quota picker - not that Power Users may/may not have access to
    // select a different Quota for the profiles created by them
    $_qut = jrProfile_get_quotas();
    if (!jrUser_is_admin()) {
        // We're a power user and may only have access to selected Quotas
        $key = jrUser_get_profile_home_key('quota_jrUser_power_user_quotas');
        if (strpos($key, ',')) {
            $_all = array();
            foreach (explode(',', $key) as $qid) {
                if (isset($_qut[$qid])) {
                    $_all[$qid] = $_qut[$qid];
                }
            }
            $_qut = $_all;
            unset($_all);
        }
        elseif (jrCore_checktype($key, 'number_nz') && isset($_qut[$key])) {
            $_qut = array($key => $_qut[$key]);
        }
        else {
            jrCore_notice_page('error', 'Unable to determine Power User Quota - please contact the system adminstrator');
        }
        // Show them how many profiles they can create
        if (isset($_user['quota_jrUser_power_user_max']) && $_user['quota_jrUser_power_user_max'] > 0) {

            // Let's see how many profiles they have created
            $num = jrProfile_get_user_linked_profiles($_user['_user_id']);
            $max = jrUser_get_profile_home_key('quota_jrUser_power_user_max');
            if ($num && is_array($num) && count($num) >= $max) {
                jrCore_notice_page('error', 37, 'referrer');
            }
            $_ln = jrUser_load_lang_strings();
            jrCore_set_form_notice('notice', "{$_ln['jrProfile'][28]} {$max}");
        }
    }

    // Show create new Profile Form
    jrCore_page_banner(7, false, false);

    // Form init
    $_tmp = array(
        'submit_value' => 8,
        'cancel'       => 'referrer'
    );
    jrCore_form_create($_tmp);

    // Profile Name
    $_tmp = array(
        'name'     => 'profile_name',
        'label'    => 9,
        'help'     => 10,
        'type'     => 'text',
        'required' => true,
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    if (isset($_qut) && is_array($_qut) && count($_qut) > 1) {
        $_tmp = array(
            'name'          => 'profile_quota_id',
            'label'         => 29,
            'help'          => 30,
            'type'          => 'select',
            'options'       => $_qut,
            'required'      => true,
            'validate'      => 'number_nz',
            'form_designer' => false // We do not allow the form designer to override this field
        );
        jrCore_form_field_create($_tmp);
    }
    else {
        $qval = array_keys($_qut);
        $qval = reset($qval);
        $_tmp = array(
            'name'          => 'profile_quota_id',
            'type'          => 'hidden',
            'value'         => $qval,
            'validate'      => 'number_nz',
            'form_designer' => false // We do not allow the form designer to override this field
        );
        jrCore_form_field_create($_tmp);
    }

    // Show User Picker...  (ADMINS ONLY)
    $_tmp = array(
        'name'          => 'profile_user_id',
        'group'         => 'admin',
        'label'         => 'profile owner',
        'help'          => 'What User Account should this profile be created for?  The User Account selected here will have admin capabilities for the Profile.',
        'type'          => 'live_search',
        'target'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/get_profile_users",
        'required'      => false,
        'validate'      => 'number_nz',
        'form_designer' => false // We do not allow the form designer to override this field
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrProfile_create_save($_post, $_user, $_conf)
{
    global $_urls;
    jrUser_session_require_login();
    // Make sure user is allowed to create profiles....
    if (!jrUser_is_power_user()) {
        jrUser_not_authorized();
    }
    jrCore_form_validate($_post);

    // Make sure the given profile name does not already exist
    $_rt = jrCore_db_get_item_by_key('jrProfile', 'profile_name', $_post['profile_name']);
    if (isset($_rt) && is_array($_rt)) {
        jrCore_set_form_notice('error', 19);
        jrCore_form_field_hilight('profile_name');
        jrCore_form_result();
    }

    // Make sure user_name is not a banned word...
    if (jrCore_run_module_function('jrBanned_is_banned', 'name', $_post['profile_name'])) {
        jrCore_set_form_notice('error', 20);
        jrCore_form_field_hilight('profile_name');
        jrCore_form_result();
    }

    // Check for an active skin template with that name...
    $_tl = glob(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/*.tpl");
    $unm = jrCore_url_string($_post['profile_name']);
    foreach ($_tl as $tname) {
        if (strpos($tname, "/{$unm}.tpl")) {
            jrCore_set_form_notice('error', 'There is an active skin page using that name - please try another');
            jrCore_form_field_hilight('profile_name');
            jrCore_form_result();
            break;
        }
    }

    // Make sure it is NOT a module URL
    if (isset($_urls["{$_post['profile_name']}"]) || isset($_urls[$unm])) {
        jrCore_set_form_notice('error', 'There is a module already using that name - please try another');
        jrCore_form_field_hilight('profile_name');
        jrCore_form_result();
    }

    // Make sure we get a good profile_user_id
    if (jrUser_is_admin()) {
        if (!isset($_post['profile_user_id']) || !jrCore_checktype($_post['profile_user_id'], 'number_nz')) {
            jrCore_set_form_notice('error', 'You have entered an invalid profile owner - please search and select a valid profile owner');
            jrCore_form_field_hilight('profile_user_id');
            jrCore_form_result();
        }
        $_vu = jrCore_db_get_item('jrUser', $_post['profile_user_id']);
        if (!is_array($_vu)) {
            jrCore_set_form_notice('error', 'You have entered an invalid profile owner - please search and select a valid profile owner');
            jrCore_form_field_hilight('profile_user_id');
            jrCore_form_result();
        }
    }

    // Validate posted Quota
    $_qut = jrProfile_get_quotas();
    if (!jrUser_is_admin()) {
        // We're a power user and may only have access to selected Quotas
        $key = jrUser_get_profile_home_key('quota_jrUser_power_user_quotas');
        if (strpos($key, ',')) {
            $_all = array();
            foreach (explode(',', $key) as $qid) {
                if (isset($_qut[$qid])) {
                    $_all[$qid] = $_qut[$qid];
                }
            }
            $_qut = $_all;
            unset($_all);
        }
        elseif (jrCore_checktype($key, 'number_nz') && isset($_qut[$key])) {
            $_qut = array($key => $_qut[$key]);
        }
        else {
            jrCore_set_form_notice('error', 'Unable to determine Power User Quota - please contact the system adminstrator');
            jrCore_form_field_hilight('profile_quota_id');
            jrCore_form_result();
        }

        // Let's see how many profiles they have created
        $num = jrProfile_get_user_linked_profiles($_user['_user_id']);
        $max = jrUser_get_profile_home_key('quota_jrUser_power_user_max');
        if ($num && is_array($num) && count($num) >= $max) {
            jrCore_set_form_notice('error', 37);
            jrCore_form_result();
        }
    }

    $qid = (int) $_post['profile_quota_id'];
    if (!isset($_qut[$qid])) {
        jrCore_set_form_notice('error', 31);
        jrCore_form_field_hilight('profile_quota_id');
        jrCore_form_result();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt                    = jrCore_form_get_save_data('jrProfile', 'create', $_post);
    $_rt['profile_url']     = jrCore_url_string($_post['profile_name']);
    $_rt['profile_active']  = 1;
    $_rt['profile_private'] = 1;

    // Create new Profile
    $pid = jrCore_db_create_item('jrProfile', $_rt);
    if (!$pid) {
        jrCore_set_form_notice('error', 18);
        jrCore_form_result();
    }

    // If this is NOT an admin user, setup profile link
    if (jrUser_is_admin()) {
        $uid = $_post['profile_user_id'];
    }
    else {
        $uid = $_user['_user_id'];
    }

    // Update with new profile id
    if (isset($uid)) {
        $_temp = array();
        $_core = array(
            '_user_id'    => $uid,
            '_profile_id' => $pid
        );
        jrCore_db_update_item('jrProfile', $pid, $_temp, $_core);
        jrProfile_create_user_link($uid, $pid);
    }

    // update the profile_count for the quota
    jrProfile_increment_quota_profile_count($qid);

    // Save any uploaded media files added in by our
    jrCore_save_all_media_files('jrProfile', 'create', $pid, $pid);

    jrCore_logger('INF', "created new profile: {$_post['profile_name']}");
    jrCore_form_delete_session();
    // Redirect to new Profile
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_rt['profile_url']}");
}

//------------------------------
// delete_save
//------------------------------
function view_jrProfile_delete_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();

    // Make sure we get a valid ID
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'invalid profile id - please try again');
    }
    $_pr = jrCore_db_get_item('jrProfile', $_post['id'], true);
    if (!isset($_pr) || !is_array($_pr)) {
        jrCore_notice_page('error', 'invalid profile id - no data for profile found');
    }

    // Make sure user is allowed to create profiles....
    if (!jrUser_is_admin()) {
        // Are we a power user?
        if (jrUser_is_power_user()) {
            if (!jrProfile_is_profile_owner($_post['id'])) {
                jrUser_not_authorized();
            }
        }
        // We are not a power user or admin - see if we are allowed to delete our profile
        elseif (isset($_conf['jrProfile_allow_delete']) && $_conf['jrProfile_allow_delete'] == 'on') {
            if (!jrProfile_is_profile_owner($_post['id'])) {
                jrUser_not_authorized();
            }
        }
        else {
            jrUser_not_authorized();
        }
    }

    // We need to get info about this profile first, and make sure any users are NOT admin users
    $_sp = array(
        'search'        => array(
            "_profile_id = {$_post['id']}"
        ),
        'return_keys'   => array(
            'user_name', 'user_group'
        ),
        'skip_triggers' => true,
        'no_cache'      => true,
        'limit'         => 1000
    );
    $_rt = jrCore_db_search_items('jrUser', $_sp);
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
        foreach ($_rt['_items'] as $_v) {
            if (isset($_v['user_group'])) {
                switch ($_v['user_group']) {
                    case 'master':
                    case 'admin':
                        $murl = jrCore_get_module_url('jrUser');
                        jrCore_notice_page('error', "You cannot delete a profile that belongs to an Admin or Master User!<br>You must change the &quot;<a href=\"{$_conf['jrCore_base_url']}/{$murl}/account/user_id={$_v['_user_id']}\">{$_v['user_name']}</a>&quot; User Account to the &quot;user&quot; group before you can delete this profile.", 'referrer', 'continue', false);
                        break;
                }
            }
        }
    }

    // Delete Profile
    jrProfile_delete_profile($_post['id']);

    // Delete caches for this profile
    jrCore_delete_profile_cache_entries($_post['id']);

    // If we just delete our OWN profile, and we are not associated with any other profiles, log out
    if (!jrUser_is_admin() && !jrUser_is_power_user()) {
        sleep(1);

        // We no longer exist!?
        if (!$_us = jrCore_db_get_item('jrUser', $_user['_user_id'], true, true)) {

            // Delete cache entries..
            jrUser_reset_cache($_user['_user_id']);

            // Send logout trigger
            jrCore_trigger_event('jrUser', 'logout', $_user);

            // Destroy session
            jrUser_session_destroy();

            // Redirect to front page
            jrCore_form_result($_conf['jrCore_base_url']);
        }
    }

    // Redirect
    $url = $_conf['jrCore_base_url'];
    $ref = jrCore_get_local_referrer();
    if (strpos($ref, '/browser') || strpos($ref, '/pending')) {
        jrCore_set_form_notice('success', 33);
        jrCore_location('referrer');
    }
    jrCore_notice_page('success', 33, $url, 'continue', false);
}

//------------------------------
// settings
//------------------------------
function view_jrProfile_settings($_post, $_user, $_conf)
{
    jrUser_session_require_login();

    // make sure we get a good profile_id
    if (isset($_post['id'])) {
        $_post['profile_id'] = (int) $_post['id'];
    }
    if (!isset($_post['profile_id']) || !jrCore_checktype($_post['profile_id'], 'number_nz')) {
        $_post['profile_id'] = jrUser_get_profile_home_key('_profile_id');
    }

    // We need to make sure the viewing user has access to this profile
    if (!jrProfile_is_profile_owner($_post['profile_id'])) {
        jrUser_not_authorized();
    }

    // See if we are switching active profiles
    if ((jrUser_is_admin() || jrUser_is_power_user() || jrUser_is_multi_user()) && isset($_post['profile_id']) && jrCore_checktype($_post['profile_id'], 'number_nz')) {
        $_profile = jrCore_db_get_item('jrProfile', $_post['profile_id'], true, true);
        $_quota   = jrProfile_get_quota($_profile['profile_quota_id']);
        $_profile = array_merge($_profile, $_quota);
    }
    else {
        $_profile = jrCore_db_get_item('jrProfile', $_user['_profile_id'], true, true);
        $_quota   = jrProfile_get_quota($_profile['profile_quota_id']);
        $_profile = array_merge($_profile, $_quota);
    }

    if (!$_profile || !is_array($_profile)) {
        jrCore_notice_page('error', 41);
    }

    $_lang = jrUser_load_lang_strings();

    // If this a master admin modifying...
    jrUser_account_tabs('settings', $_profile);

    $_ln = jrUser_load_lang_strings();

    if ($_profile['_profile_id'] != jrUser_get_profile_home_key('_profile_id')) {
        jrCore_set_form_notice('notice', "{$_ln['jrProfile'][35]} <strong>{$_profile['profile_name']}</strong>", false);
    }
    if (!isset($_profile['profile_active']) || $_profile['profile_active'] != '1') {
        if (!isset($_post['hl'])) {
            global $_post;
            $_post['hl'] = 'profile_active';
        }
        jrCore_set_form_notice('error', $_ln['jrProfile'][36], false);
    }

    // If we have a Power User, we can create additional profiles
    $create = null;
    if (jrUser_is_admin() || jrUser_is_power_user()) {
        $max = jrUser_get_profile_home_key('quota_jrUser_power_user_max');
        if (jrUser_is_admin() || (intval($max) > count(explode(',', $_user['user_linked_profile_ids'])))) {
            $create .= jrCore_page_button('profile_create', $_lang['jrProfile'][7], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/create')");
        }
    }
    $create .= jrCore_page_button('p', $_profile['profile_name'], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_profile['profile_url']}')");
    jrCore_page_banner(2, $create, false);

    // Form init
    $_tmp = array(
        'submit_value'     => $_lang['jrCore'][72],
        'cancel'           => 'referrer',
        'form_ajax_submit' => false,
        'values'           => $_profile
    );
    jrCore_form_create($_tmp);

    // If we modifying FROM the Profile Browser, we redirect there on save...
    $ref = jrCore_get_local_referrer();
    if (jrUser_is_admin() && strpos($ref, '/browser')) {
        $_tmp = array(
            'name'  => 'from_browser',
            'type'  => 'hidden',
            'value' => $ref
        );
        jrCore_form_field_create($_tmp);
    }

    if ((jrUser_is_admin() || jrUser_is_power_user() || jrUser_is_multi_user()) && isset($_post['profile_id']) && jrCore_checktype($_post['profile_id'], 'number_nz')) {
        $_tmp = array(
            'name'  => 'profile_id',
            'type'  => 'hidden',
            'value' => $_post['profile_id']
        );
        jrCore_form_field_create($_tmp);
    }

    // Profile Image
    $_img             = $_profile;
    $_img['_item_id'] = $_profile['_profile_id'];
    $_tmp             = array(
        'name'         => 'profile_image',
        'label'        => 6,
        'help'         => 23,
        'type'         => 'image',
        'size'         => 'medium',
        'max'          => (isset($_profile['quota_jrImage_max_image_size'])) ? intval($_profile['quota_jrImage_max_image_size']) : 2097152,
        'required'     => false,
        'image_delete' => true,
        'value'        => $_img
    );
    jrCore_form_field_create($_tmp);

    // Profile Name
    $_tmp = array(
        'name'      => 'profile_name',
        'label'     => 9,
        'help'      => 10,
        'type'      => 'text',
        'required'  => true,
        'min'       => 1,
        'validate'  => 'printable'
    );
    jrCore_form_field_create($_tmp);

    if (jrUser_is_admin() || (isset($_user['quota_jrProfile_url_changes']) && $_user['quota_jrProfile_url_changes'] == 'on')) {
        $_tmp = array(
            'name'          => 'profile_url',
            'label'         => 42,
            'sublabel'      => 43,
            'help'          => 44,
            'type'          => 'text',
            'required'      => false,
            'validate'      => 'url_name',
            'form_designer' => false
        );
        jrCore_form_field_create($_tmp);
    }

    // Profile Active
    if (jrUser_is_admin()) {
        $_tmp = array(
            'name'          => 'profile_active',
            'label'         => 'profile active',
            'help'          => 'If checked, this profile is active and will be viewable in the system to all users',
            'type'          => 'checkbox',
            'required'      => true,
            'validate'      => 'onoff',
            'value'         => (isset($_profile['profile_active']) && $_profile['profile_active'] == '1') ? 'on' : 'off',
            'form_designer' => false
        );
        jrCore_form_field_create($_tmp);
    }

    // See if we can change our Profile Privacy
    if (jrUser_is_admin() || (isset($_user['quota_jrProfile_privacy_changes']) && $_user['quota_jrProfile_privacy_changes'] == 'on')) {
        $_opt = jrProfile_get_privacy_options();
        $priv = 1;
        if (isset($_profile['profile_private']) && jrCore_checktype($_profile['profile_private'], 'number_nn')) {
            $priv = (int) $_profile['profile_private'];
        }
        elseif (isset($_profile['quota_jrProfile_default_privacy']) && jrCore_checktype($_profile['quota_jrProfile_default_privacy'], 'number_nn')) {
            $priv = (int) $_profile['quota_jrProfile_default_privacy'];
        }
        // Profile Privacy
        $_tmp = array(
            'name'          => 'profile_private',
            'label'         => 11,
            'help'          => 12,
            'type'          => 'select',
            'options'       => $_opt,
            'value'         => $priv,
            'required'      => true,
            'min'           => 0,
            'max'           => 2,
            'validate'      => 'number_nn',
            'form_designer' => false // We do not allow the form designer to override this field
        );
        jrCore_form_field_create($_tmp);
    }

    // Bio
    $_tmp = array(
        'name'      => 'profile_bio',
        'label'     => 21,
        'help'      => 22,
        'type'      => 'editor',
        'validate'  => 'allowed_html',
        'required'  => false
    );
    jrCore_form_field_create($_tmp);

    // Admin users can change a profile to any quota
    if (jrUser_is_admin()) {
        $_tmp = array(
            'name'          => 'profile_quota_id',
            'label'         => 29,
            'help'          => 30,
            'type'          => 'select',
            'options'       => 'jrProfile_get_quotas',
            'value'         => $_profile['profile_quota_id'],
            'required'      => true,
            'group'         => 'power',
            'validate'      => 'number_nz',
            'form_designer' => false // We do not allow the form designer to override this field
        );
        jrCore_form_field_create($_tmp);
    }

    // Power Users are limited to specific Quotas
    elseif (jrUser_is_power_user()) {

        // Power Users can change profile quota_id's of profiles that are NOT their home profile
        if (jrUser_get_profile_home_key('_profile_id') != $_profile['_profile_id']) {

            // Profile Quota (power users only)
            $_qot = jrProfile_get_settings_quotas();
            if ($_qot && is_array($_qot)) {
                if (count($_qot) > 1) {
                    $_tmp = array(
                        'name'          => 'profile_quota_id',
                        'label'         => 29,
                        'help'          => 30,
                        'type'          => 'select',
                        'options'       => $_qot,
                        'value'         => $_profile['profile_quota_id'],
                        'required'      => true,
                        'group'         => 'power',
                        'validate'      => 'number_nz',
                        'form_designer' => false // We do not allow the form designer to override this field
                    );
                }
                else {
                    $_tmp = array(
                        'name'          => 'profile_quota_id',
                        'type'          => 'hidden',
                        'value'         => $_profile['profile_quota_id'],
                        'form_designer' => false // We do not allow the form designer to override this field
                    );
                }
                jrCore_form_field_create($_tmp);
            }
        }
    }

    // If we allow multiple free signup quotas, let the user change quotas
    // But only if they are on a FREE quota - otherwise no change
    elseif (isset($_conf['jrProfile_change']) && $_conf['jrProfile_change'] == 'on' && isset($_user['quota_jrUser_allow_signups']) && $_user['quota_jrUser_allow_signups'] == 'on') {
        $_qot = jrProfile_get_signup_quotas();
        if ($_qot && count($_qot) > 1) {
            $_tmp = array(
                'name'          => 'profile_quota_id',
                'label'         => 29,
                'help'          => 30,
                'type'          => 'select',
                'options'       => $_qot,
                'value'         => $_profile['profile_quota_id'],
                'required'      => true,
                'validate'      => 'number_nz',
                'form_designer' => false // We do not allow the form designer to override this field
            );
            jrCore_form_field_create($_tmp);
        }
    }

    jrCore_page_display();
}

//------------------------------
// settings_save
//------------------------------
function view_jrProfile_settings_save($_post, $_user, $_conf)
{
    global $_mods, $_urls;

    jrUser_session_require_login();
    jrCore_form_validate($_post);
    jrUser_load_lang_strings();

    if (isset($_post['profile_id']) && jrCore_checktype($_post['profile_id'], 'number_nz')) {
        if (jrUser_is_admin()) {
            $_profile = jrCore_db_get_item('jrProfile', $_post['profile_id']);
        }
        elseif (jrUser_is_power_user() || jrUser_is_multi_user()) {
            // For a Power or Multi User we need to make sure they have
            // access to the profile they are trying to modify
            $_pr = jrProfile_get_user_linked_profiles($_user['_user_id']);
            if (!isset($_pr["{$_post['profile_id']}"])) {
                jrCore_set_form_notice('error', 'invalid profile_id - please try again');
                jrCore_form_result();
            }
            $_profile = jrCore_db_get_item('jrProfile', $_post['profile_id']);
        }
        else {
            $_profile = jrCore_db_get_item('jrProfile', $_user['user_active_profile_id']);
        }
    }
    else {
        $_profile = jrCore_db_get_item('jrProfile', $_user['user_active_profile_id']);
    }

    // Check that our submitted profile name does not already exist
    $_rt = jrCore_db_get_item_by_key('jrProfile', 'profile_name', $_post['profile_name']);
    if ($_rt && is_array($_rt) && $_profile['_profile_id'] != $_rt['_profile_id']) {
        jrCore_set_form_notice('error', 18);
        jrCore_form_field_hilight('profile_name');
        jrCore_form_result();
    }

    if (!jrUser_is_admin()) {
        unset($_post['profile_active']);
    }
    else {
        $_post['profile_active'] = ($_post['profile_active'] == 'on') ? 1 : 0;
        // If the admin is changing the profile active, change user_active as well
        $_us = jrProfile_get_owner_info($_profile['_profile_id']);
        if ($_us && is_array($_us)) {
            $_up = array('user_active' => $_post['profile_active']);
            foreach ($_us as $_u) {
                if ($_u['_user_id'] != $_user['_user_id']) {
                    // We want to set an user accounts that are ONLY associated with
                    // this profile to inactive as well (user_active = 0)
                    $_pr = jrProfile_get_user_linked_profiles($_u['_user_id']);
                    if (!$_pr || count($_pr) === 1) {
                        jrCore_db_update_item('jrUser', $_u['_user_id'], $_up);
                    }
                }
            }
        }
    }

    if (isset($_post['profile_quota_id']) && jrCore_checktype($_post['profile_quota_id'], 'number_nz')) {
        // Validate posted Quota
        $_qut = jrProfile_get_quotas();
        if (!jrUser_is_admin()) {

            if (jrUser_is_power_user()) {
                // We're a power user and may only have access to selected Quotas
                $key = jrUser_get_profile_home_key('quota_jrUser_power_user_quotas');
                if (strpos($key, ',')) {
                    $_all = array();
                    foreach (explode(',', $key) as $qid) {
                        if (isset($_qut[$qid])) {
                            $_all[$qid] = $_qut[$qid];
                        }
                    }
                    $_qut = $_all;
                    unset($_all);
                }
                elseif (jrCore_checktype($key, 'number_nz') && isset($_qut[$key])) {
                    $_qut = array($key => $_qut[$key]);
                }
                else {
                    jrCore_set_form_notice('error', 32);
                    jrCore_form_field_hilight('profile_quota_id');
                    jrCore_form_result();
                }
            }
            elseif (isset($_conf['jrProfile_change']) && $_conf['jrProfile_change'] == 'on') {
                // We can only change to a quota that allows signup
                $_qot = jrProfile_get_signup_quotas();
                if (!isset($_qot["{$_post['profile_quota_id']}"])) {
                    jrCore_set_form_notice('error', 31);
                    jrCore_form_field_hilight('profile_quota_id');
                    jrCore_form_result();
                }
            }
            else {
                // No change
                $_post['profile_quota_id'] = $_profile['profile_quota_id'];
            }
        }
        $qid = (int) $_post['profile_quota_id'];
        $hqi = (int) jrUser_get_profile_home_key('profile_quota_id');
        if (!isset($_qut[$qid]) && $qid !== $hqi) {
            jrCore_set_form_notice('error', 31);
            jrCore_form_field_hilight('profile_quota_id');
            jrCore_form_result();
        }
    }

    $_post['profile_private'] = (int) $_post['profile_private'];
    $_data                    = jrCore_form_get_save_data('jrProfile', 'settings', $_post);

    if (isset($_data['profile_name']) && strlen($_data['profile_name']) > 0) {
        // Custom profile url checking
        if ((jrUser_is_admin() || (isset($_user['quota_jrProfile_url_changes']) && $_user['quota_jrProfile_url_changes'] == 'on')) && isset($_data['profile_url']) && strlen($_data['profile_url']) > 0) {
            $profile_url = jrCore_url_string($_data['profile_url']);
            $highlight   = 'profile_url';
        }
        else {
            $profile_url = jrCore_url_string($_post['profile_name']);
            $highlight   = 'profile_name';
        }
        if ($profile_url != $_profile['profile_url']) {
            // Make sure the url isn't being used already
            if (jrCore_db_get_item_by_key('jrProfile', 'profile_url', $profile_url)) {
                jrCore_set_form_notice('error', 45);
                jrCore_form_field_hilight($highlight);
                jrCore_form_result();
            }
            // Check for an active skin template with that name...
            if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$profile_url}.tpl")) {
                jrCore_set_form_notice('error', 45);
                jrCore_form_field_hilight($highlight);
                jrCore_form_result();
            }
            // Make sure it is NOT a module URL
            if (isset($_mods[$profile_url]) || isset($_urls[$profile_url])) {
                jrCore_set_form_notice('error', 45);
                jrCore_form_field_hilight($highlight);
                jrCore_form_result();
            }
        }
        $_data['profile_url'] = $profile_url;
    }
    jrCore_db_update_item('jrProfile', $_profile['_profile_id'], $_data);

    // Update Quota Counts for quotas if we are changing
    if (isset($_post['profile_quota_id']) && $_post['profile_quota_id'] != $_profile['profile_quota_id']) {
        // Update counts in both Quotas
        jrProfile_increment_quota_profile_count($_post['profile_quota_id']);
        jrProfile_decrement_quota_profile_count($_profile['profile_quota_id']);
    }

    // Check for file upload
    $_image = jrCore_save_media_file('jrProfile', 'profile_image', $_profile['_profile_id'], $_profile['_profile_id']);

    // If the user does NOT have a user image, and we are uploading one to our home profile...
    if (!isset($_user['user_image_size']) && isset($_image) && is_array($_image) && $_profile['_profile_id'] == jrUser_get_profile_home_key('_profile_id')) {
        $_user             = array_merge($_user, $_image);
        $_user['_item_id'] = $_profile['_profile_id'];
        $profile_image     = jrCore_get_media_file_path('jrProfile', 'profile_image', $_user);
        if (is_file($profile_image)) {
            $ext = jrCore_file_extension($profile_image);
            $nam = "{$_user['_user_id']}_user_image";
            if (jrCore_copy_media_file($_profile['_profile_id'], $profile_image, $nam)) {
                $dir = dirname($profile_image);
                jrCore_write_to_file("{$dir}/{$nam}.tmp", "user_image.{$ext}");
                jrCore_save_media_file('jrUser', "{$dir}/{$nam}", $_profile['_profile_id'], $_user['_user_id']);
                unlink("{$dir}/{$nam}");
                unlink("{$dir}/{$nam}.tmp");
            }
        }
    }

    // If we have updated our OWN profile, then we need to update home URL
    if ($_profile['_profile_id'] == jrUser_get_profile_home_key('_profile_id')) {
        jrUser_save_profile_home_keys();
    }
    jrCore_form_delete_session();

    // If this is an admin from the browser...
    if (jrUser_is_admin() && isset($_post['from_browser']) && jrCore_checktype($_post['from_browser'], 'url')) {
        jrCore_form_result($_post['from_browser']);
    }
    jrProfile_reset_cache($_profile['_profile_id']);
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_data['profile_url']}");
}

//------------------------------
// user_link
//------------------------------
function view_jrProfile_user_link($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrProfile');

    jrCore_set_form_notice('notice', 'User Accounts can be linked to multiple profiles - each works the same as the User\'s home profile.');
    jrCore_page_banner('User Profile Link');

    $_tmp = array(
        'submit_value'     => 'link user to profile',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false
    );
    if (strpos(jrCore_get_local_referrer(), 'create')) {
        $_tmp['cancel'] = false;
    }
    jrCore_form_create($_tmp);

    // Select User
    if (isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz')) {
        $_us = jrCore_db_get_item('jrUser', $_post['user_id'], true);
        if (!$_us || !is_array($_us)) {
            jrCore_notice_page('error', 'Invalid User ID');
        }
        jrCore_page_custom("<strong>{$_us['user_name']}</strong>", 'user name');

        $_tmp = array(
            'name'     => 'link_user_id',
            'type'     => 'hidden',
            'validate' => 'number_nz',
            'value'    => $_post['user_id']
        );
        jrCore_form_field_create($_tmp);
    }
    else {
        $_tmp = array(
            'name'      => 'link_user_id',
            'label'     => 'user name',
            'type'      => 'live_search',
            'help'      => 'Select the User Account you want to link to an existing profile. The User Account can already be linked to an existing profile, and you can link a User Account to more than 1 profile.<br><br><b>NOTE:</b> Master and Admin User Accounts can already work with any profile in the system, so do not show up in this list.',
            'validate'  => 'not_empty',
            'required'  => true,
            'error_msg' => 'You have selected an invalid User Account - please try again',
            'target'    => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/user_link_get_user"
        );
        jrCore_form_field_create($_tmp);
    }

    // Select Profile
    $_tmp = array(
        'name'      => 'link_profile_id',
        'label'     => 'profile name',
        'type'      => 'live_search',
        'help'      => 'Select the Profile you want to link the User Account to.  The linked User Account will have full access to the profile as if it was their own.',
        'validate'  => 'not_empty',
        'required'  => true,
        'error_msg' => 'You have selected an invalid Profile - please try again',
        'target'    => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/user_link_get_profile"
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// user_link_save
//------------------------------
function view_jrProfile_user_link_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Our link_user_id and link_profile_id could come in as a STRING (user_name) or a NUMBER (_user_id)
    $uid = 0;
    $pid = 0;
    if (isset($_post['link_user_id']) && jrCore_checktype($_post['link_user_id'], 'number_nz')) {
        // We're good - they selected from the live search
        $uid = (int) $_post['link_user_id'];
    }
    else {
        $_tm = jrCore_db_get_item_by_key('jrUser', 'user_name', $_post['link_user_id'], true);
        if ($_tm && is_array($_tm)) {
            $uid = (int) $_tm['_user_id'];
        }
        else {
            jrCore_set_form_notice('error', 'invalid user name - please select a valid user name');
            jrCore_form_result();
        }
    }
    if (isset($_post['link_profile_id']) && jrCore_checktype($_post['link_profile_id'], 'number_nz')) {
        // We're good - they selected from the live search
        $pid = (int) $_post['link_profile_id'];
    }
    else {
        $_tm = jrCore_db_get_item_by_key('jrProfile', 'profile_name', $_post['link_profile_id'], true);
        if ($_tm && is_array($_tm)) {
            $pid = (int) $_tm['_profile_id'];
        }
        else {
            jrCore_set_form_notice('error', 'invalid profile name - please select a valid user account');
            jrCore_form_result();
        }
    }
    // [link_user_id] => 2
    // [link_profile_id] => 26
    $tbl = jrCore_db_table_name('jrProfile', 'profile_link');
    $req = "SELECT * FROM {$tbl} WHERE user_id = '{$uid}' AND profile_id = '{$pid}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {

        jrProfile_create_user_link($uid, $pid);

        // Make sure this user is not being linked to a profile for the first time...
        $_us = jrCore_db_get_item('jrUser', $uid);
        if (!isset($_us['_profile_id']) || !jrCore_checktype($_us['_profile_id'], 'number_nz')) {
            $_dt = array('user_name' => $_us['user_name']);
            $_cr = array('_profile_id' => $pid);
            jrCore_db_update_item('jrUser', $uid, $_dt, $_cr);
        }

        jrProfile_reset_cache($pid);
    }
    jrCore_form_delete_session();
    jrCore_set_form_notice('success', 'The User Account has been linked with the Profile');
    jrCore_form_result();
}

//------------------------------
// user_link_get_user
//------------------------------
function view_jrProfile_user_link_get_user($_post, $_user, $_conf)
{
    jrUser_master_only();
    $_sc = array(
        'search'         => array(
            "user_name like {$_post['q']}%"
        ),
        'return_keys'    => array('_user_id', 'user_name'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'privacy_check'  => false,
        'limit'          => 12
    );
    $_rt = jrCore_db_search_items('jrUser', $_sc);
    $_sl = array();
    if ($_rt && is_array($_rt) && is_array($_rt['_items'])) {
        foreach ($_rt['_items'] as $_v) {
            $_sl["{$_v['_user_id']}"] = $_v['user_name'];
        }
    }
    return jrCore_live_search_results('link_user_id', $_sl);
}

//------------------------------
// user_link_get_profile
//------------------------------
function view_jrProfile_user_link_get_profile($_post, $_user, $_conf)
{
    jrUser_master_only();
    $_sc = array(
        'search'         => array(
            "profile_name like {$_post['q']}%"
        ),
        'return_keys'    => array('_profile_id', 'profile_name'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'privacy_check'  => false,
        'limit'          => 12
    );
    $_rt = jrCore_db_search_items('jrProfile', $_sc);
    $_sl = array();
    if ($_rt && is_array($_rt) && is_array($_rt['_items'])) {
        foreach ($_rt['_items'] as $_v) {
            $_sl["{$_v['_profile_id']}"] = $_v['profile_name'];
        }
    }
    return jrCore_live_search_results('link_profile_id', $_sl);
}

//------------------------------
// quota_browser
//------------------------------
function view_jrProfile_quota_browser($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrProfile');
    jrCore_page_banner('Profile Quotas');

    // Start our output
    $dat             = array();
    $dat[1]['title'] = 'ID';
    $dat[1]['width'] = '5%';
    $dat[2]['title'] = 'name';
    $dat[2]['width'] = '60%';
    $dat[3]['title'] = 'profiles';
    $dat[3]['width'] = '5%';
    $dat[4]['title'] = 'signup';
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = 'note';
    $dat[5]['width'] = '5%';
    $dat[6]['title'] = 'rename';
    $dat[6]['width'] = '5%';
    $dat[7]['title'] = 'clone';
    $dat[7]['width'] = '5%';
    $dat[8]['title'] = 'transfer';
    $dat[8]['width'] = '5%';
    $dat[9]['title'] = 'delete';
    $dat[9]['width'] = '5%';
    jrCore_page_table_header($dat);

    // Get existing quotas
    $tbl = jrCore_db_table_name('jrProfile', 'quota_value');
    $req = "SELECT `quota_id`, `name`, `value` FROM {$tbl} WHERE `name` IN('name','allow_signups','admin_note','profile_count') ORDER BY `quota_id` ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    $_ft = array();
    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $_v) {
            $_ft["{$_v['quota_id']}"]["{$_v['name']}"] = $_v['value'];
        }
    }
    $pass  = jrCore_get_option_image('pass');
    $fail  = jrCore_get_option_image('fail');
    $murlu = jrCore_get_module_url('jrUser');

    foreach ($_ft as $qid => $_qt) {

        $num             = (isset($_ft[$qid]['profile_count'])) ? (int) $_ft[$qid]['profile_count'] : '0';
        $dat             = array();
        $dat[1]['title'] = $qid;
        $dat[1]['class'] = 'center';
        $dat[2]['title'] = $_qt['name'];
        if (isset($num) && $num > 0) {
            $dat[3]['title'] = jrCore_page_button("qb-c{$qid}", jrCore_number_format($num), "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard/browser/search_string=profile_quota_id:{$qid}')");
        }
        else {
            $dat[3]['title'] = '0';
        }
        $dat[3]['class'] = 'center';
        $dat[4]['title'] = (isset($_qt['allow_signups']) && $_qt['allow_signups'] == 'on') ? '<a href="' . $_conf['jrCore_base_url'] . '/' . $murlu . '/admin/quota/id=' . $qid . '">' . $pass . '</a>' : '<a href="' . $_conf['jrCore_base_url'] . '/' . $murlu . '/admin/quota/id=' . $qid . '">' . $fail . '</a>';
        $dat[4]['class'] = 'center';
        $dat[5]['title'] = (isset($_qt['admin_note']{0})) ? '<img src="' . $_conf['jrCore_base_url'] . '/modules/jrProfile/img/note.png" width="24" height="24" alt="' . jrCore_entity_string($_qt['admin_note']) . '" title="' . jrCore_entity_string($_qt['admin_note']) . '">' : '&nbsp;';
        $dat[5]['class'] = 'center';
        $dat[6]['title'] = jrCore_page_button("qb-r{$qid}", 'rename', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/quota/id={$qid}/hl=name')");
        $dat[7]['title'] = jrCore_page_button("qb-c{$qid}", 'clone', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/quota_clone/id={$qid}')");
        if ($num > 0) {
            $dat[8]['title'] = jrCore_page_button("qb-t{$qid}", 'transfer', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/quota_transfer/id={$qid}')");
            $dat[9]['title'] = jrCore_page_button("qb-d{$qid}", 'delete', 'disabled');
        }
        else {
            $dat[8]['title'] = jrCore_page_button("qb-t{$qid}", 'transfer', 'disabled');
            $dat[9]['title'] = jrCore_page_button("qb-d{$qid}", 'delete', " jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/quota_delete/id={$qid}')");
        }
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    // Form init
    $_tmp = array(
        'submit_value'     => 'create new quota',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // New Quota Name
    $_tmp = array(
        'name'      => 'new_quota_name',
        'label'     => 'new quota name',
        'help'      => 'To create a new Profile Quota, enter the name of the new quota you would like to create.',
        'type'      => 'text',
        'error_msg' => 'Please enter a valid quota name',
        'validate'  => 'printable'
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_display();
}

//------------------------------
// quota_browser_save
//------------------------------
function view_jrProfile_quota_browser_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    if (isset($_post['new_quota_name']) && strlen($_post['new_quota_name']) > 0) {
        $qid = jrProfile_create_quota($_post['new_quota_name']);
        if (isset($qid) && jrCore_checktype($qid, 'number_nz')) {
            jrCore_form_delete_session();
            jrCore_set_form_notice('success', 'The new Profile Quota was successfully created');
            jrCore_form_result('referrer');
        }
        jrCore_set_form_notice('error', 'An error was encountered creating the Profile Quota - please try again');
    }
    else {
        jrCore_set_form_notice('error', 'Please enter a valid Profile Quota name to create a new quota');
    }
    jrCore_form_result();
}

//------------------------------
// quota_clone
//------------------------------
function view_jrProfile_quota_clone($_post, $_user, $_conf)
{
    jrUser_master_only();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid quota_id - please try again');
        jrCore_form_result('referrer');
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrProfile');
    jrCore_page_banner('Clone to New Quota');

    // Form init
    $_tmp = array(
        'submit_value' => 'create new quota',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/quota_browser"
    );
    jrCore_form_create($_tmp);

    // Clone Quota ID
    $_tmp = array(
        'name'  => 'clone_id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    // New Quota Name
    $_tmp = array(
        'name'      => 'new_quota_name',
        'label'     => 'new quota name',
        'help'      => 'Enter the name of the new Profile Quota you want to create by cloning an existing quota.',
        'type'      => 'text',
        'error_msg' => 'Please enter a valid quota name',
        'validate'  => 'printable'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// quota_clone_save
//------------------------------
function view_jrProfile_quota_clone_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    if (isset($_post['new_quota_name']) && strlen($_post['new_quota_name']) > 0) {
        $qid = jrProfile_create_quota($_post['new_quota_name']);
        if (isset($qid) && jrCore_checktype($qid, 'number_nz')) {

            // Next - we need to get all settings for
            // the quota we are cloning FROM, and add them to our new quota
            $_qt = jrProfile_get_quota($_post['clone_id']);
            foreach ($_qt as $k => $v) {
                switch ($k) {
                    // There are some keys we do not copy over
                    case 'quota_jrProfile_name':
                    case 'quota_jrProfile_profile_count':
                        continue 2;
                        break;
                    default:
                        // [quota_jrAudio_allowed_audio_types]
                        list(, $module, $name) = explode('_', $k, 3);
                        jrProfile_set_quota_value($module, $qid, $name, $v);
                        break;
                }
            }
            jrCore_form_delete_session();
            jrCore_set_form_notice('success', "The new Profile Quota was successfully cloned from the {$_qt['quota_jrProfile_name']} quota");
            jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/quota_browser");
        }
        jrCore_set_form_notice('error', 'An error was encountered creating the Profile Quota - please try again');
    }
    else {
        jrCore_set_form_notice('error', 'Please enter a valid Profile Quota name to create a new quota');
    }
    jrCore_form_result();
}

//------------------------------
// quota_transfer
//------------------------------
function view_jrProfile_quota_transfer($_post, $_user, $_conf)
{
    jrUser_master_only();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid quota_id - please try again');
        jrCore_form_result('referrer');
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrProfile');
    jrCore_page_banner('Select Quota to Transfer to');

    // Form init
    $_tmp = array(
        'submit_value' => 'transfer profiles',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/quota_browser"
    );
    jrCore_form_create($_tmp);

    $_qt = jrProfile_get_quotas();
    unset($_qt["{$_post['id']}"]);

    // Clone Quota ID
    $_tmp = array(
        'name'  => 'transfer_id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    // New Quota Name
    $_tmp = array(
        'name'      => 'new_quota_id',
        'label'     => 'transfer to quota',
        'help'      => 'Select the Quota you want to transfer profiles to.',
        'type'      => 'select',
        'options'   => $_qt,
        'error_msg' => 'Please enter a valid quota name',
        'validate'  => 'printable'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// quota_transfer_save
//------------------------------
function view_jrProfile_quota_transfer_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Get affected profile id's
    $tid = intval($_post['transfer_id']);
    $nid = intval($_post['new_quota_id']);

    $_sc = array(
        'search'              => array(
            "profile_quota_id = {$tid}"
        ),
        'return_item_id_only' => true,
        'skip_triggers'       => true,
        'ignore_pending'      => true,
        'privacy_check'       => false,
        'limit'               => 10000000
    );
    $_rt = jrCore_db_search_items('jrProfile', $_sc);
    if ($_rt && is_array($_rt)) {

        $_up = array();
        foreach ($_rt as $pid) {
            $_up[$pid] = array('profile_quota_id' => $nid);
        }
        jrCore_db_update_multiple_items('jrProfile', $_up);

        // Update profile counts
        $cnt = count($_rt);

        // Set old quota to 0 - no more profiles in it
        jrProfile_set_quota_value('jrProfile', $tid, 'profile_count', 0);

        // Increment new quota profile count by amount we have transferred
        jrProfile_increment_quota_profile_count($nid, $cnt);

        // Reset all caches - this is WAY faster then resetting caches for each profile_id
        jrCore_delete_all_cache_entries();

        jrCore_form_delete_session();
        jrCore_set_form_notice('success', "Successfully transferred {$cnt} profiles to the new quota");
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/quota_browser");
    }
    jrCore_set_form_notice('error', 'An error was encountered transferring the profiles - please try again');
    jrCore_form_result();
}

//------------------------------
// quota_delete
//------------------------------
function view_jrProfile_quota_delete($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid quota_id - please try again');
        jrCore_form_result('referrer');
    }
    if (!jrProfile_delete_quota($_post['id'])) {
        jrCore_set_form_notice('error', 'An error was encountered deleting the quota - please try again');
    }
    jrCore_form_result('referrer');
}

//------------------------------
// quota_compare
//------------------------------
function view_jrProfile_quota_compare($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_admin_only();
    $btn = jrCore_page_button('tools', 'Tools', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools');");
    jrCore_page_banner('Quota Compare', $btn);

    $_adm = array();
    $_tmp = array();
    $_ina = array();
    foreach ($_mods as $mod_dir => $_inf) {
        if ($_inf['module_active'] == '1') {
            $_tmp["{$_inf['module_name']}"] = $mod_dir;
        }
        else {
            $_ina["{$_inf['module_name']}"] = $mod_dir;
        }
    }
    ksort($_tmp);
    ksort($_ina);
    $_tmp = array_merge($_tmp, $_ina);

    $_out = array();
    foreach ($_tmp as $mod_dir) {
        if (!isset($_mods[$mod_dir]['module_category'])) {
            $_mods[$mod_dir]['module_category'] = 'tools';
        }
        $cat = $_mods[$mod_dir]['module_category'];
        if (!isset($_out[$cat])) {
            $_out[$cat] = array();
        }
        $_out[$cat][$mod_dir] = $_mods[$mod_dir];
    }
    $_adm['_modules']['core'] = $_out['core'];
    unset($_out['core']);
    $_adm['_modules'] = $_adm['_modules'] + $_out;
    ksort($_adm['_modules']);
    unset($_out);

    $_q      = jrProfile_get_quotas();
    $_quotas = array();
    foreach ($_q as $id => $name) {
        $_quotas[$id]         = jrProfile_get_quota($id);
        $_quotas[$id]['name'] = $name;
    }

    $allowed = jrCore_get_option_image('pass');
    $blocked = jrCore_get_option_image('fail');

    $dat             = array();
    $dat[1]['title'] = '';
    $dat[1]['class'] = 'page_section_header';
    $i               = 3;
    foreach ($_quotas as $id => $_q) {
        $dat[$i]['title'] = $_q['name'];
        $dat[$i]['class'] = '" style="white-space: normal';
        $i++;
    }
    jrCore_page_table_header($dat);

    // Signup
    $dat             = array();
    $dat[1]['title'] = 'Allows Signup';
    $i               = 3;
    $murl            = jrCore_get_module_url('jrUser');
    foreach ($_quotas as $id => $_q) {
        $btn              = ($_q['quota_jrUser_allow_signups'] == 'on') ? $allowed : $blocked;
        $dat[$i]['title'] = '<a href="' . $_conf['jrCore_base_url'] . '/' . $murl . '/admin/quota/id=' . $id . '/hl[]=allow_signups/hl[]=id">' . $btn . '</a>';
        $dat[$i]['class'] = 'center';
        $i++;
    }
    jrCore_page_table_row($dat);

    // Go through installed modules
    foreach ($_adm['_modules'] as $cat => $_mod) {

        $header = false;
        foreach ($_mod as $mod => $_inf) {

            if (!jrCore_module_is_active($mod) || !isset($_q['quota_' . $mod . '_allowed'])) {
                continue;
            }
            // Do category header if we have not done it yet
            if (!$header) {
                // category
                $dat             = array();
                $dat[1]['title'] = $cat;
                $dat[1]['class'] = 'center';
                jrCore_page_table_header($dat, null, true);
                $header = true;
            }

            $dat             = array();
            $dat[1]['title'] = $_inf['module_name'];

            $i = 3;
            foreach ($_quotas as $id => $_q) {
                $btn              = ($_q['quota_' . $mod . '_allowed'] == 'on') ? $allowed : $blocked;
                $dat[$i]['title'] = '<a href="' . $_conf['jrCore_base_url'] . '/' . $_inf['module_url'] . '/admin/quota/id=' . $id . '/hl[]=allowed/hl[]=id">' . $btn . '</a>';
                $dat[$i]['class'] = 'center';
                $i++;
            }
            jrCore_page_table_row($dat);
        }
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button('referrer');
    jrCore_page_display();
}

//------------------------------
// disk_usage_report
//------------------------------
function view_jrProfile_disk_usage_report($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_admin_only();
    jrCore_page_set_meta_header_only();

    $button = jrCore_page_button('close', 'close', 'self.close();');
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_page_banner('disk usage report', $button);
        jrCore_notice_page('error', 'Invalid Profile ID');
    }

    $pid = (int) $_post['_1'];
    $_pr = jrCore_db_get_item('jrProfile', $pid, true);
    $nam = $_pr['profile_url'];
    if (strlen($_pr['profile_url']) > 50) {
        $nam = substr($_pr['profile_url'], 0, 47) . '...';
    }
    jrCore_page_banner("disk usage report: <span style=\"text-transform:none\">@{$nam}</span>", $button);
    if (!isset($_pr['profile_disk_usage']) || $_pr['profile_disk_usage'] == 0) {
        $usage = jrProfile_get_disk_usage($pid);
        if ($usage > 0) {
            $_up = array(
                'profile_disk_usage' => $usage
            );
            jrCore_db_update_item('jrProfile', $pid, $_up);
        }
    }

    $dat             = array();
    $dat[0]['title'] = 'icon';
    $dat[0]['width'] = '1%';
    $dat[1]['title'] = 'module';
    $dat[1]['width'] = '59%';
    $dat[2]['title'] = 'file count';
    $dat[2]['width'] = '20%';
    $dat[3]['title'] = 'total size';
    $dat[3]['width'] = '20%';
    jrCore_page_table_header($dat);

    // Get files
    $_fl = jrCore_get_media_files($pid);
    if ($_fl && is_array($_fl)) {
        $_tt = array('jrCore' => array(0,0));
        foreach ($_fl as $_file) {
            if (!strpos($_file['name'], '/rb_')) {
                $name = basename($_file['name']);
                list($mod,) = explode('_', $name, 2);
                if (!isset($_tt[$mod])) {
                    $_tt[$mod] = array(0,0);
                }
                $_tt[$mod][0] += $_file['size'];
                $_tt[$mod][1]++;
            }
            else {
                $_tt['jrCore'][0] += $_file['size'];
                $_tt['jrCore'][1]++;
            }
        }
        if (count($_tt) > 0) {
            arsort($_tt);
            foreach ($_tt as $mod => $_s) {
                if (isset($_mods[$mod]) && $_s[1] > 0) {
                    $dat             = array();
                    $dat[0]['title'] = jrCore_get_module_icon_html($mod, 32);
                    $dat[1]['title'] = $_mods[$mod]['module_name'];
                    $dat[2]['title'] = jrCore_number_format($_s[1]);
                    $dat[2]['class'] = 'center';
                    $dat[3]['title'] = jrCore_format_size($_s[0]);
                    $dat[3]['class'] = 'center';
                    jrCore_page_table_row($dat);
                }
            }
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = 'No files found for profile';
        $dat[1]['class'] = 'p10 center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_close_button();
    jrCore_page_display();
}
