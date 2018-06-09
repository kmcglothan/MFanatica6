<?php
/**
 * Jamroom Users module
 *
 * copyright 2018 The Jamroom Network
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
// ssl_check
//------------------------------
function view_jrUser_ssl_check($_post, $_user, $_conf)
{
    echo 'OK';
    jrCore_db_close();
    exit;
}

//------------------------------
// user_resend
//------------------------------
function view_jrUser_user_resend($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();
    if (!isset($_post['user_id']) || !jrCore_checktype($_post['user_id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid user_id - please try again');
        jrCore_location('referrer');
    }
    $_rt = jrCore_db_get_item('jrUser', $_post['user_id']);
    if (!is_array($_rt)) {
        jrCore_set_form_notice('error', 'Invalid user_id - data not found - please try again');
        jrCore_location('referrer');
    }
    if ($_rt['user_validated'] != '1' || $_rt['user_active'] != '1') {
        // Send User Account validation email
        $_rp = array(
            'system_name'    => $_conf['jrCore_system_name'],
            'activation_url' => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/activate/{$_rt['user_validate']}"
        );
        list($sub, $msg) = jrCore_parse_email_templates('jrUser', 'signup', $_rp);
        jrCore_send_email($_rt['user_email'], $sub, $msg);
    }
    jrCore_set_form_notice('success', 'The Signup Activation email has been resent to the User');
    jrCore_location('referrer');
}

//------------------------------
// user_activate
//------------------------------
function view_jrUser_user_activate($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();
    if (!isset($_post['user_id']) || !jrCore_checktype($_post['user_id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid user_id - please try again');
        jrCore_location('referrer');
    }
    $_rt = jrCore_db_get_item('jrUser', $_post['user_id'], false, true);
    if (!is_array($_rt)) {
        jrCore_set_form_notice('error', 'Invalid user_id - data not found - please try again');
        jrCore_location('referrer');
    }
    if ($_rt['user_validated'] != '1' || $_rt['user_active'] != '1') {
        // Activate this account
        $_data = array(
            'user_active'    => '1',
            'user_validated' => '1'
        );
        jrCore_db_update_item('jrUser', $_post['user_id'], $_data);

        // Send out trigger on successful account activation - only first time
        $_rt['user_active']    = '1';
        $_rt['user_validated'] = '1';
        jrCore_trigger_event('jrUser', 'signup_activated', $_rt);

        // Notify User their account is now active
        $_rt['system_name'] = $_conf['jrCore_system_name'];
        list($sub, $msg) = jrCore_parse_email_templates('jrUser', 'account_activated', $_rt);
        jrCore_send_email($_rt['user_email'], $sub, $msg);

        // Reset this user's cache
        jrUser_reset_cache($_post['user_id']);
    }

    jrCore_set_form_notice('success', 'The User Account has been successfully activated');
    if (isset($_SESSION['user_dashboard_referral_url'])) {
        $url = $_SESSION['user_dashboard_referral_url'];
        unset($_SESSION['user_dashboard_referral_url']);
        jrCore_location($url);
    }
    jrCore_location('referrer');
}

//------------------------------
// online_status
//------------------------------
function view_jrUser_online_status($_post, $_user, $_conf)
{
    // Make sure we get the correct incoming data
    if (!isset($_post['_2']) || strlen($_post['_2']) === 0) {
        if (jrUser_is_admin()) {
            return 'invalid unique_id';
        }
        return '&nbsp;';
    }
    if (!isset($_post['_3']) || !jrCore_checktype($_post['_3'], 'number_nz')) {
        if (jrUser_is_admin()) {
            return 'invalid seconds';
        }
        return '&nbsp;';
    }
    if (!isset($_post['_4']) || strlen($_post['_4']) === 0) {
        if (jrUser_is_admin()) {
            return 'invalid template';
        }
        return '';
    }
    foreach (explode(',', $_post['_2']) as $uid) {
        if (!jrCore_checktype($uid, 'number_nz')) {
            if (jrUser_is_admin()) {
                return 'invalid unique_id (2)';
            }
            return '&nbsp;';
        }
    }

    // Check for cache
    $ckey = md5(json_encode($_post));
    if (!$out = jrCore_is_cached('jrUser', $ckey)) {

        // First - get info about the specified users/profile
        switch ($_post['_1']) {

            // We've been asked to get online status for all users attached to a specific profile
            case 'profile':
                // We must get all users associated with the profile
                $tbl = jrCore_db_table_name('jrProfile', 'profile_link');
                $req = "SELECT user_id FROM {$tbl} WHERE profile_id = '" . intval($_post['_2']) . "'";
                $_us = jrCore_db_query($req, 'user_id');
                if ($_us && is_array($_us)) {
                    $_sr = array(
                        'search'                       => array(
                            "_user_id in " . implode(',', array_keys($_us))
                        ),
                        'include_jrProfile_keys'       => true,
                        'exclude_jrProfile_quota_keys' => true,
                        'ignore_pending'               => true,
                        'privacy_check'                => false
                    );
                    $_rt = jrCore_db_search_items('jrUser', $_sr);
                }
                else {
                    // No user accounts for this profile
                    return '&nbsp;';
                }
                break;

            case 'user':
                $_sr = array(
                    'search'         => array(
                        "_user_id IN {$_post['_2']}"
                    ),
                    'exclude_jrProfile_quota_keys',
                    'ignore_pending' => true,
                    'privacy_check'  => false
                );
                $_rt = jrCore_db_search_items('jrUser', $_sr);
                break;

            default:

                return 'invalid online type';
                break;
        }

        // Now see if these users are online
        if (!isset($_rt['_items']) || !is_array($_rt['_items'])) {
            return '';
        }

        $_us = array();
        foreach ($_rt['_items'] as $v) {
            $_us[] = $v['_user_id'];
        }

        // See if users are online
        $_st = jrUser_session_online_user_ids($_post['_3'], $_us);
        if ($_st && is_array($_st)) {
            foreach ($_rt['_items'] as $k => $v) {
                if (isset($_st["{$v['_user_id']}"])) {
                    $_rt['_items'][$k]['user_is_online'] = 1;
                }
                else {
                    $_rt['_items'][$k]['user_is_online'] = 0;
                }
            }
        }
        // See what we are doing for a template
        if ($_post['_4'] == 'default') {
            $tpl = 'online_status_row.tpl';
            $mod = 'jrUser';
        }
        elseif (jrCore_checktype($_post['_4'], 'md5')) {
            $cdr = jrCore_get_module_cache_dir($_conf['jrCore_active_skin']);
            $tpl = "{$cdr}/{$_post['_4']}.tpl";
            $mod = null;
        }
        else {
            $tpl = $_post['_4'];
            $mod = null;
        }
        $out = jrCore_parse_template($tpl, $_rt, $mod);

        // Save to cache
        jrCore_add_to_cache('jrUser', $ckey, $out, 15);
    }
    return $out;
}

//------------------------------
// delete_language
//------------------------------
function view_jrUser_delete_language($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrUser');
    jrCore_page_banner('delete user language');

    $_dl = jrUser_get_languages();
    $err = false;
    unset($_dl['en-US']);
    if (strlen(reset($_dl)) === 0) {
        jrCore_set_form_notice('error', 'There are no additional user languages install besides en-US.');
        $err = true;
    }
    else {
        jrCore_set_form_notice('notice', 'Deleting a language here will not remove it from any modules or skins that have defined it.<br>The next time an Integrity Check is run, these language strings could be reloaded.<br>You will need to remove the actual module or skin language files if they get reloaded.', false);
    }
    jrCore_get_form_notice();

    if (!$err) {
        // Form init
        $_tmp = array(
            'submit_value'     => 'delete language',
            'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
            'form_ajax_submit' => false,
            'submit_prompt'    => 'Are you sure you want to delete this language?'
        );
        jrCore_form_create($_tmp);

        // Delete Language
        $_tmp = array(
            'name'     => 'delete_lang',
            'label'    => 'language to delete',
            'help'     => 'Select the existing User Language you would like to clone to create the new User Language',
            'type'     => 'select',
            'options'  => $_dl,
            'required' => true
        );
        jrCore_form_field_create($_tmp);
    }
    else {
        jrCore_page_cancel_button('referrer');
    }
    jrCore_page_display();
}

//------------------------------
// delete_language_save
//------------------------------
function view_jrUser_delete_language_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    $_dl = jrUser_get_languages();
    if (!isset($_post['delete_lang']) || !isset($_dl["{$_post['delete_lang']}"]) || $_post['delete_lang'] == 'en-US') {
        jrCore_set_form_notice('error', 'Invalid language received - please try again');
    }
    $tbl = jrCore_db_table_name('jrUser', 'language');
    $req = "DELETE FROM {$tbl} WHERE lang_code = '{$_post['delete_lang']}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt > 0) {
        jrCore_logger('INF', "successfully deleted {$cnt} {$_post['delete_lang']} language strings");
    }
    jrCore_form_delete_session();
    jrCore_set_form_notice('success', 'The language strings were successfully deleted');
    jrCore_form_result();
}

//------------------------------
// reset_language
//------------------------------
function view_jrUser_reset_language($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrUser');
    jrCore_page_banner('reset language strings');

    jrCore_set_form_notice('notice', 'Resetting the Language Strings for a module or skin will delete and reload<br>all non form designer language strings for the module or skin.', false);
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value'     => 'reset language strings',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false,
        'submit_prompt'    => 'Are you sure you want to reset the language strings for the select module or skin?'
    );
    jrCore_form_create($_tmp);

    $_opt = array();
    foreach ($_mods as $mod => $_inf) {
        if (is_dir(APP_DIR . "/modules/{$mod}/lang")) {
            $_opt[$mod] = '(module) ' . $_mods[$mod]['module_name'];
        }
    }
    $_skns = jrCore_get_skins();
    foreach ($_skns as $skin) {
        $_mta        = jrCore_skin_meta_data($skin);
        $_opt[$skin] = '(skin) ' . ((isset($_mta['title'])) ? $_mta['title'] : $skin);
    }
    // Delete Language
    $_tmp = array(
        'name'     => 'lang_item',
        'label'    => 'module or skin',
        'help'     => 'Select the module or skin that you would like to reset the language strings for.',
        'type'     => 'select',
        'options'  => $_opt,
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// reset_language_save
//------------------------------
function view_jrUser_reset_language_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Remove any existing lang strings
    $tbl = jrCore_db_table_name('jrUser', 'language');
    $req = "DELETE FROM {$tbl} WHERE lang_module = '" . jrCore_db_escape($_post['lang_item']) . "' AND (lang_key = 'menu' || lang_key < 10000)";
    jrCore_db_query($req);

    // Next, reload all existing language strings for the module
    if (is_dir(APP_DIR . "/modules/{$_post['lang_item']}")) {
        jrUser_install_lang_strings('module', $_post['lang_item']);
    }
    else {
        jrUser_install_lang_strings('skin', $_post['lang_item']);
    }
    // Reset language entries in cache
    jrCore_delete_all_cache_entries('jrUser');
    jrCore_set_form_notice('success', 'The language strings were successfully reset');
    jrCore_form_delete_session();
    jrCore_location('referrer');
}

//------------------------------
// create_language
//------------------------------
function view_jrUser_create_language($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrUser');

    jrCore_set_form_notice('notice', 'Create or Update a User Language by cloning an existing language.');
    jrCore_page_banner('create or update a user language');

    // Form init
    $_tmp = array(
        'submit_value' => 'clone language strings',
        'cancel'       => 'referrer'
    );
    jrCore_form_create($_tmp);

    // Clone Language
    $_tmp = array(
        'name'     => 'new_lang_clone',
        'label'    => 'clone language',
        'help'     => 'Select the existing User Language you would like to clone - language strings that exist in the Cloned Language, but not in the New Language will be inserted.',
        'type'     => 'select',
        'options'  => 'jrUser_get_languages',
        'value'    => 'en-US',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // New Lang Code
    $_tmp = array(
        'name'     => 'new_lang_code',
        'label'    => 'language code',
        'sublabel' => '(click help for details)',
        'help'     => 'This should be the 2 digit ISO-639-1 Code for the language family, followed by a dash (-) and a 2 digit, uppercase local code - i.e. "en-US", "en-GB", etc. A list of 2 digit ISO-639-1 codes can be found in Wikipedia:<br><br><a href="http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"><u>List of ISO-639-1 Codes on Wikipedia</u></a>',
        'type'     => 'text',
        'required' => true,
        'min'      => 5,
        'max'      => 5,
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // New Lang Direction
    $_tmp = array(
        'name'     => 'new_lang_direction',
        'label'    => 'language text direction',
        'help'     => 'Does this new language flow from left to right (ltr) or from right to left (rtl)?',
        'type'     => 'select',
        'options'  => array('ltr' => 'Left to Right', 'rtl' => 'Right to Left'),
        'value'    => 'ltr',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// create_language_save
//------------------------------
function view_jrUser_create_language_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Validate new_lang_code
    list($one, $two) = explode('-', $_post['new_lang_code']);
    if ((!isset($one) || strlen($one) !== 2) || (!isset($two) || strlen($two) !== 2)) {
        jrCore_set_form_notice('error', 'invalid language code - should be in xx-XX format');
        jrCore_form_field_hilight('new_lang_code');
        jrCore_form_result();
    }
    // Make sure that code does not already exist in the DB
    $cod = jrCore_db_escape($_post['new_lang_code']);
    $ltr = jrCore_db_escape($_post['new_lang_direction']);
    $tbl = jrCore_db_table_name('jrUser', 'language');
    $cln = jrCore_db_escape($_post['new_lang_clone']);
    $req = "SELECT * FROM {$tbl} WHERE lang_code = '{$cod}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (is_array($_rt)) {

        // We already have entries for this lang code in the DB - let's get any NEW
        // lang entries that have not been setup yet and insert those.
        $req = "SELECT CONCAT_WS('-', lang_module, lang_key) AS lkey FROM {$tbl} WHERE lang_code = '{$cod}'";
        $_nl = jrCore_db_query($req, 'lkey', false, 'lkey');

        $req = "SELECT CONCAT_WS('-', lang_module, lang_key) AS lkey, lang_module, lang_code, lang_key, lang_text, lang_default FROM {$tbl} WHERE lang_code = '{$cln}'";
        $_el = jrCore_db_query($req, 'lkey');

        if (count($_nl) < count($_el)) {
            // We have a difference - figure out
            $_ins = array();
            foreach ($_el as $key => $_ls) {
                if (!isset($_nl[$key])) {
                    $_ins[] = "('" . jrCore_db_escape($_ls['lang_module']) . "','{$cod}','utf-8','{$ltr}','{$_ls['lang_key']}','" . jrCore_db_escape($_ls['lang_text']) . "','" . jrCore_db_escape($_ls['lang_default']) . "')";
                }
            }
            if (count($_ins) > 0) {
                $req = "INSERT INTO {$tbl} (lang_module,lang_code,lang_charset,lang_ltr,lang_key,lang_text,lang_default) VALUES " . implode(',', $_ins);
                $cnt = jrCore_db_query($req, 'COUNT');
                if ($cnt && $cnt > 0) {
                    jrCore_logger('INF', "Created {$cnt} new Lang Strings in User Language: {$_post['new_lang_code']}");
                    jrCore_set_form_notice('success', "The language was successfully updated with {$cnt} new language strings");
                    jrCore_form_delete_session();
                    jrCore_form_result('referrer');
                }
            }
        }
        if (!isset($cnt)) {
            jrCore_set_form_notice('success', 'There were no new language strings to create or update');
            jrCore_form_delete_session();
            jrCore_form_result('referrer');
        }
    }
    else {
        // copy every entry from the CLONE language into the TARGET language
        $req = "INSERT INTO {$tbl} (lang_module,lang_code,lang_charset,lang_ltr,lang_key,lang_text,lang_default) (SELECT lang_module,'{$cod}','utf-8','{$ltr}',lang_key,lang_text,lang_default FROM {$tbl} WHERE lang_code = '{$cln}')";
        jrCore_db_query($req, 'COUNT');
        jrCore_logger('INF', "Created new User Language: {$_post['new_lang_code']}");
        jrCore_set_form_notice('success', 'The new language has been successfully created');
    }
    // Redirect to edit..
    jrCore_form_delete_session();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/language/lang_code={$cod}");
}

//------------------------------
// create
//------------------------------
function view_jrUser_create($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrUser_load_lang_strings();

    // If this a master admin creating...
    if (jrUser_is_master()) {
        jrCore_page_include_admin_menu();
        jrCore_page_admin_tabs('jrUser');
    }
    else {
        jrCore_page_dashboard_tabs('online');
    }

    // our page banner
    jrCore_page_banner('create user account');

    // Form init
    $_tmp = array(
        'submit_value'     => 'create user',
        'cancel'           => 'referrer',
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // User Name
    $_tmp = array(
        'name'      => 'user_name',
        'label'     => 4,
        'help'      => 5,
        'type'      => 'text',
        'error_msg' => 6,
        'required'  => true,
        'validate'  => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // User Email
    $_tmp = array(
        'name'     => 'user_email',
        'label'    => 18,
        'help'     => 57,
        'type'     => 'text',
        'required' => true,
        'validate' => 'email'
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_section_header('password options');

    // Generate Password
    $_tmp = array(
        'name'     => 'user_password_create',
        'label'    => 'create password',
        'sublabel' => 'and send user an email',
        'help'     => 'If this option is checked, a random password will be generated for this new User Account if NO PASSWORD is entered into the password form field.  The new user will be sent an email with their password - the user can change their password when they login.',
        'type'     => 'checkbox',
        'value'    => 'on'
    );
    jrCore_form_field_create($_tmp);

    // Password #1
    $_tmp = array(
        'name'      => 'user_passwd1',
        'label'     => 7,
        'help'      => 8,
        'type'      => 'password',
        'error_msg' => 9,
        'required'  => false,
        'validate'  => 'not_empty'
    );
    jrCore_form_field_create($_tmp);

    // Password #2
    $_tmp = array(
        'name'      => 'user_passwd2',
        'label'     => 32,
        'help'      => 23,
        'type'      => 'password',
        'error_msg' => 9,
        'required'  => false,
        'validate'  => 'not_empty'
    );
    jrCore_form_field_create($_tmp);

    // Master Admin options
    if (jrUser_is_admin()) {

        jrCore_page_section_header('admin options');

        $_tmp = array(
            'name'     => 'create_profile',
            'label'    => 'create profile for user',
            'help'     => 'If this option is checked, a Profile will be created for this User Account.  If left unchecked, you will be able to select a Profile to link this User Account to.',
            'type'     => 'checkbox',
            'value'    => 'on',
            'validate' => 'onoff',
            'required' => true
        );
        jrCore_form_field_create($_tmp);

        if (jrUser_is_master()) {
            $_tmp = array(
                'name'     => 'user_group',
                'label'    => 'user group',
                'help'     => 'Select the user group this user should be part of:<br><br><b>Standard User:</b> a normal user account in your system - can modify items they have created only.<br><b>Profile Admin:</b> can modify users and profiles and items created by any user on the system. Has access to the Dashboard.<br><b>Master Admin:</b> full access to all system areas including the Admin Control Panel and Dashboard.',
                'type'     => 'select',
                'options'  => array('user' => 'Standard User', 'admin' => 'Profile Admin', 'master' => 'Master Admin'),
                'value'    => 'user',
                'validate' => 'core_string'
            );
            jrCore_form_field_create($_tmp);
        }
    }
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrUser_create_save($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_form_validate($_post);

    // Make sure they don't already exist
    $_rt = jrCore_db_get_item_by_key('jrUser', 'user_name', $_post['user_name']);
    if ($_rt && is_array($_rt)) {
        jrCore_set_form_notice('error', 33);
        jrCore_form_field_hilight('user_name');
        jrCore_form_result();
    }

    // Make sure they don't already exist
    $_rt = jrCore_db_get_item_by_key('jrUser', 'user_email', $_post['user_email']);
    if ($_rt && is_array($_rt)) {
        jrCore_set_form_notice('error', 34);
        jrCore_form_field_hilight('user_email');
        jrCore_form_result();
    }

    // Make sure user_name is not a banned word...
    if (jrCore_run_module_function('jrBanned_is_banned', 'name', $_post['user_name'])) {
        jrCore_set_form_notice('error', 55);
        jrCore_form_field_hilight('user_name');
        jrCore_form_result();
    }

    // Make sure the user_name is not being used by a profile
    if ($_post['create_profile'] == 'on') {

        $_rt = jrCore_db_get_item_by_key('jrProfile', 'profile_url', $_post['user_name']);
        if ($_rt && is_array($_rt)) {
            jrCore_set_form_notice('error', 33);
            jrCore_form_field_hilight('user_name');
            jrCore_form_result();
        }
        $_rt = jrCore_db_get_item_by_key('jrProfile', 'profile_name', $_post['user_name']);
        if ($_rt && is_array($_rt)) {
            jrCore_set_form_notice('error', 33);
            jrCore_form_field_hilight('user_name');
            jrCore_form_result();
        }

    }

    // Check for an active skin template with that name...
    if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$_post['user_name']}.tpl")) {
        jrCore_set_form_notice('error', 33);
        jrCore_form_field_hilight('user_name');
        jrCore_form_result();
    }
    // Create profile or not
    $cusr = false;
    if (jrUser_is_admin()) {
        $cusr = $_post['create_profile'];
    }

    // See if we are generating a password
    if (isset($_post['user_passwd1']) && strlen($_post['user_passwd1']) > 0 && isset($_post['user_passwd2']) && strlen($_post['user_passwd2']) > 0) {
        // Verify our passwords match
        if (!isset($_post['user_passwd1']) || strlen($_post['user_passwd1']) === 0 || !isset($_post['user_passwd2']) || strlen($_post['user_passwd2']) === 0) {
            jrCore_set_form_notice('error', 35);
            jrCore_form_field_hilight('user_passwd1');
            jrCore_form_field_hilight('user_passwd2');
            jrCore_form_result();
        }
        if (isset($_post['user_passwd1']) && isset($_post['user_passwd2']) && $_post['user_passwd1'] != $_post['user_passwd2']) {
            jrCore_set_form_notice('error', 35);
            jrCore_form_field_hilight('user_passwd1');
            jrCore_form_field_hilight('user_passwd2');
            jrCore_form_result();
        }
    }
    else {
        // Create and generate a password
        $_post['user_passwd1'] = substr(md5(microtime()), 8, 8);
    }
    $password = $_post['user_passwd1'];

    // Setup our default user values
    $pass = jrUser_get_password_hash($_post['user_passwd1']);
    $code = md5(microtime());
    unset($_post['user_passwd1'], $_post['user_passwd2']);

    // Create our user account
    $_data = array(
        'user_name'      => $_post['user_name'],
        'user_email'     => $_post['user_email'],
        'user_group'     => 'user',
        'user_password'  => $pass,
        'user_language'  => (isset($_post['user_language']{0})) ? $_post['user_language'] : $_conf['jrUser_default_language'],
        'user_active'    => 1,
        'user_validated' => 1,
        'user_validate'  => $code
    );
    // Check for master setting group
    if (jrUser_is_master()) {
        if (isset($_post['user_group']{0})) {
            $_data['user_group'] = $_post['user_group'];
        }
    }

    $uid = jrCore_db_create_item('jrUser', $_data);
    if (!isset($uid) || !jrCore_checktype($uid, 'number_nz')) {
        jrCore_set_form_notice('error', 36);
        jrCore_form_result();
    }
    $_data['_user_id'] = $uid;
    $_data             = jrCore_trigger_event('jrUser', 'signup_created', $_data, $_data);

    // User account is created - send out trigger so any listening
    // modules can do their work for this new user
    $_temp = array();
    $_core = array(
        '_user_id' => $uid
    );
    if ($cusr && $cusr == 'off') {
        $_core['_profile_id'] = 0;  // hack but needed..
    }
    // Update account just created with proper user_id...
    jrCore_db_update_item('jrUser', $uid, $_temp, $_core);

    // Send User Account email
    if (isset($_post['user_password_create']) && $_post['user_password_create'] == 'on') {
        $_rp = array(
            'system_name' => $_conf['jrCore_system_name'],
            'jamroom_url' => $_conf['jrCore_base_url'],
            'user_name'   => $_data['user_name'],
            'user_pass'   => $password,
            'user_email'  => $_data['user_email']
        );
        list($sub, $msg) = jrCore_parse_email_templates('jrUser', 'created', $_rp);
        jrCore_send_email($_data['user_email'], $sub, $msg);
        if ($cusr == 'off') {
            jrCore_set_form_notice('success', 'The account has been created and a welcome email sent.<br>You can now modify information about the Profile for this new User.', false);
        }
    }
    elseif ($cusr == 'off') {
        jrCore_set_form_notice('success', 'The User account has been successfully created.<br>You can now modify information about the Profile for this new User.', false);
    }

    // Our User Account is created...
    jrCore_logger('INF', "account created for {$_data['user_email']}");
    jrCore_form_delete_session();

    $purl = jrCore_get_module_url('jrProfile');
    if (jrUser_is_admin() && $cusr == 'off') {
        // Redirect to link up so User Account can be linked to profile...
        jrCore_set_form_notice('success', 'The User account has been successfully created.<br>Select the profile you would like to link to the User Account.<br><strong>The User will be unable to log in</strong> without a linked Profile!', false);
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$purl}/user_link/user_id={$uid}");
    }
    // Redirect to the Update Profile page so the admin can change anything needed
    $_usr = jrCore_db_get_item('jrUser', $uid, true); // OK
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$purl}/settings/profile_id={$_usr['_profile_id']}");
}

//------------------------------
// signup
//------------------------------
function view_jrUser_signup($_post, $_user, $_conf)
{
    if (isset($_post['_1']) && strlen($_post['_1']) > 0) {
        switch ($_post['_1']) {
            case 'widget':
                jrCore_page_set_no_header_or_footer();
                break;
            case 'modal':
                jrCore_page_set_meta_header_only();
                break;
        }
    }
    else {
        if (isset($_conf['jrUser_force_ssl']) && $_conf['jrUser_force_ssl'] == 'on' && strpos(jrCore_get_current_url(), 'http:') === 0) {
            $url = str_replace('http://', 'https://', jrCore_get_current_url());
            jrCore_location($url);
        }
    }

    // Make sure sign ups are turned on...
    if (!isset($_conf['jrUser_signup_on']) || $_conf['jrUser_signup_on'] != 'on') {
        jrCore_notice_page('error', 58);
    }

    // Check for available signup quotas
    $_opt = jrProfile_get_signup_quotas();
    if (!$_opt || !is_array($_opt) || count($_opt) === 0) {
        if (jrUser_is_admin()) {
            jrCore_notice_page('error', 'Admin: There are currently NO QUOTAS that allow signups - please check the User Account Quota Config for quotas and allow signups!');
        }
        else {
            jrCore_notice_page('error', 58);
        }
    }
    // our page banner
    $_ln = jrUser_load_lang_strings();
    if (!isset($_post['_1'])) {
        $tmp = jrCore_page_button('login', $_ln['jrUser'][3], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/login')");
        jrCore_page_banner(31, $tmp, false);
        if (isset($_conf['jrUser_signup_note']) && strlen($_conf['jrUser_signup_note']) > 2) {
            jrCore_set_form_notice('notice', $_conf['jrUser_signup_note'], false);
        }
        jrCore_get_form_notice();

        // Form init
        $_tmp = array(
            'submit_value'     => 45,
            'cancel'           => 'referrer',
            'form_ajax_submit' => false
        );
        $tok  = jrCore_form_create($_tmp);
    }
    elseif ($_post['_1'] == 'widget') {

        // Form init
        $_tmp = array(
            'submit_value'     => 45,
            'cancel'           => 'referrer',
            'form_ajax_submit' => false
        );
        $tok  = jrCore_form_create($_tmp);
    }
    else {
        // Form init
        $_tmp = array(
            'submit_value' => 45,
            'cancel'       => false
        );
        $tok  = jrCore_form_create($_tmp);
    }

    if (isset($_post['r']) && strlen($_post['r']) > 0) {
        if ($url = jrCore_get_memory_url($_post['r'])) {
            // Referrer
            $_tmp = array(
                'name'  => 'r',
                'type'  => 'hidden',
                'value' => $_post['r']
            );
            jrCore_form_field_create($_tmp);
        }
    }

    // User Name
    $_tmp = array(
        'name'           => 'user_name',
        'label'          => 4,
        'help'           => 5,
        'type'           => 'text',
        'validate'       => 'printable',
        'ban_check'      => false,
        'required'       => true,
        'autocapitalize' => 'off',
        'autocorrect'    => 'off',
        'min'            => 1
    );
    jrCore_form_field_create($_tmp);

    // User Email
    $_tmp = array(
        'name'           => 'user_email',
        'label'          => 18,
        'help'           => 19,
        'type'           => 'text',
        'validate'       => 'email',
        'ban_check'      => false,
        'autocapitalize' => 'off',
        'autocorrect'    => 'off',
        'required'       => true
    );
    jrCore_form_field_create($_tmp);

    // Password #1
    $_tmp = array(
        'name'      => 'user_passwd1',
        'label'     => 7,
        'help'      => 8,
        'type'      => 'password',
        'error_msg' => 9,
        'validate'  => 'not_empty',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // Password #2
    $_tmp = array(
        'name'       => 'user_passwd2',
        'label'      => 32,
        'help'       => 23,
        'type'       => 'password',
        'error_msg'  => 9,
        'validate'   => 'not_empty',
        'required'   => true,
        'onkeypress' => "if (event && event.keyCode == 13 && this.value.length > 0) { jrFormSubmit('#jrUser_signup','{$tok}','ajax'); }"
    );
    jrCore_form_field_create($_tmp);

    // Quota option
    $_tmp = array(
        'name'     => 'quota_id',
        'label'    => 59,
        'help'     => 60,
        'default'  => (isset($_post['quota_id']) && jrCore_checktype($_post['quota_id'], 'number_nz') && isset($_opt["{$_post['quota_id']}"])) ? $_post['quota_id'] : $_conf['jrProfile_default_quota_id'],
        'type'     => 'select',
        'options'  => 'jrProfile_get_signup_quotas',
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    // Spam Bot Check
    if (jrCore_db_number_rows('jrUser', 'item') > 0) {
        $_tmp = array(
            'name'      => 'user_is_human',
            'label'     => 90,
            'help'      => 91,
            'type'      => 'checkbox_spambot',
            'error_msg' => 92,
            'validate'  => 'onoff'
        );
        jrCore_form_field_create($_tmp);
    }
    jrCore_page_display();
}

//------------------------------
// signup_save
//------------------------------
function view_jrUser_signup_save($_post, $_user, $_conf)
{
    global $_urls;
    jrCore_form_validate($_post);

    $_post['user_name']  = ltrim(trim($_post['user_name'], '@'));
    $_post['user_email'] = trim($_post['user_email']);

    // Make sure we get a good user_name
    if (strpos(' ' . $_post['user_name'], '>') || strpos(' ' . $_post['user_name'], '<') || stripos(' ' . $_post['user_name'], 'script')) {
        jrCore_set_form_notice('error', 129);
        jrCore_form_field_hilight('user_name');
        jrCore_form_result();
    }

    // Check for "confusable" Unicode names
    if (!isset($_conf['jrUser_enable_spoofcheck']) || $_conf['jrUser_enable_spoofcheck'] == 1) {
        if (class_exists('Spoofchecker')) {
            if (filter_var($_post['user_name'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH) != $_post['user_name']) {
                $spoof = new Spoofchecker();
                if ($spoof->isSuspicious($_post['user_name'])) {
                    jrCore_set_form_notice('error', 129);
                    jrCore_form_field_hilight('user_name');
                    jrCore_form_result();
                }
            }
        }
    }

    // Make sure it is not in our list of reserved names
    if (jrUser_is_reserved_name($_post['user_name'])) {
        jrCore_set_form_notice('error', 33);
        jrCore_form_field_hilight('user_name');
        jrCore_form_result();
    }

    // Make sure they don't already exist (user_name)
    $_rt = jrCore_db_get_item_by_key('jrUser', 'user_name', $_post['user_name']);
    if (is_array($_rt)) {
        jrCore_set_form_notice('error', 33);
        jrCore_form_field_hilight('user_name');
        jrCore_form_result();
    }

    // Make sure they don't already exist (user_email)
    $_rt = jrCore_db_get_item_by_key('jrUser', 'user_email', $_post['user_email']);
    if (is_array($_rt)) {
        jrCore_set_form_notice('error', 34);
        jrCore_form_field_hilight('user_email');
        jrCore_form_result();
    }

    // Make sure the user_name is not being used by a profile
    $_rt = jrCore_db_get_item_by_key('jrProfile', 'profile_name', $_post['user_name']);
    if (is_array($_rt)) {
        jrCore_set_form_notice('error', 33);
        jrCore_form_field_hilight('user_name');
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item_by_key('jrProfile', 'profile_url', jrCore_url_string($_post['user_name']));
    if (is_array($_rt)) {
        jrCore_set_form_notice('error', 33);
        jrCore_form_field_hilight('user_name');
        jrCore_form_result();
    }

    // Make sure user_name is not a banned word...
    if (jrCore_run_module_function('jrBanned_is_banned', 'name', $_post['user_name'])) {
        jrCore_set_form_notice('error', 55);
        jrCore_form_field_hilight('user_name');
        jrCore_form_result();
    }

    // Make sure user_name is not a banned email...
    if (jrCore_run_module_function('jrBanned_is_banned', 'email', $_post['user_email'])) {
        jrCore_set_form_notice('error', 96);
        jrCore_form_field_hilight('user_email');
        jrCore_form_result();
    }

    // Check for an active skin template with that name...
    if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$_post['user_name']}.tpl") || is_dir(APP_DIR . "/skins/{$_post['user_name']}")) {
        jrCore_set_form_notice('error', 33);
        jrCore_form_field_hilight('user_name');
        jrCore_form_result();
    }

    // Make sure it is not a module URL
    if (isset($_urls["{$_post['user_name']}"]) || is_dir(APP_DIR . "/modules/{$_post['user_name']}")) {
        jrCore_set_form_notice('error', 33);
        jrCore_form_field_hilight('user_name');
        jrCore_form_result();
    }

    // Verify our passwords match
    if (!isset($_post['user_passwd1']) || strlen($_post['user_passwd1']) === 0 || !isset($_post['user_passwd2']) || strlen($_post['user_passwd2']) === 0) {
        jrCore_set_form_notice('error', 35);
        jrCore_form_field_hilight('user_passwd1');
        jrCore_form_field_hilight('user_passwd2');
        jrCore_form_result();
    }
    if (isset($_post['user_passwd1']) && isset($_post['user_passwd2']) && $_post['user_passwd1'] != $_post['user_passwd2']) {
        jrCore_set_form_notice('error', 35);
        jrCore_form_field_hilight('user_passwd1');
        jrCore_form_field_hilight('user_passwd2');
        jrCore_form_result();
    }

    // Make sure the quota they are signing up for is allowed
    if (!isset($_post['quota_id']) || !jrCore_checktype($_post['quota_id'], 'number_nz')) {
        jrCore_set_form_notice('error', 61);
        jrCore_form_result();
    }
    $_qt = jrProfile_get_quota($_post['quota_id']);
    if (!isset($_qt['quota_jrUser_allow_signups']) || $_qt['quota_jrUser_allow_signups'] != 'on') {
        jrCore_set_form_notice('error', 61);
        jrCore_form_result();
    }

    // Our data from our signup form
    $_data = jrCore_form_get_save_data('jrUser', 'signup', $_post);

    // Setup our default user values
    $pass = jrUser_get_password_hash($_post['user_passwd1']);
    $code = md5(microtime());

    // Some fields we don't save
    unset($_data['user_passwd1'], $_data['user_passwd2']);

    // Create our user account
    $_data['user_name']      = $_post['user_name'];
    $_data['user_email']     = $_post['user_email'];
    $_data['user_password']  = $pass;
    $_data['user_language']  = (isset($_post['user_language']{0})) ? $_post['user_language'] : $_conf['jrUser_default_language'];
    $_data['user_active']    = 0;
    $_data['user_validated'] = 0;
    $_data['user_validate']  = $code;

    // Trigger now that we have passed
    $_data = jrCore_trigger_event('jrUser', 'signup_validate', $_data);

    $uid = jrCore_db_create_item('jrUser', $_data);
    if (!$uid || !jrCore_checktype($uid, 'number_nz')) {
        jrCore_set_form_notice('error', 36);
        jrCore_form_result();
    }
    // Update our _user_id value
    // If this is the FIRST USER on the system, they are master
    $first = false;
    $_temp = array('user_group' => 'user');
    $_core = array('_user_id' => $uid);
    if ($uid < 10) {
        $cnt = jrCore_db_get_datastore_item_count('jrUser');
        if ($cnt < 2) {
            // For our first master user, we automatically activate their account
            $_temp = array(
                'user_group' => 'master'
            );
            // Let's also update the CORE with their email address
            jrCore_set_setting_value('jrMailer', 'from_email', $_post['user_email']);
            jrCore_delete_all_cache_entries('jrCore', 0);
            $first = true;
        }
    }
    jrCore_db_update_item('jrUser', $uid, $_temp, $_core);

    // If this user is signing up, and was previously suppressed, un-suppress
    $tbl = jrCore_db_table_name('jrUser', 'suppressed');
    $req = "DELETE FROM {$tbl} WHERE email_address = '" . jrCore_db_escape($_post['user_email']) . "'";
    jrCore_db_query($req);

    // User account is created - send out trigger so any listening
    // modules can do their work for this new user
    $_data['_user_id'] = $uid;
    $_post             = jrCore_trigger_event('jrUser', 'signup_created', $_post, $_data);

    if ($first) {

        // Our first account is our MASTER ADMIN account - validate instantly
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/activate/{$code}");

    }
    else {

        // See what type of signup method are are doing
        switch ($_qt['quota_jrUser_signup_method']) {

            case 'instant':

                // Instant Account validation
                jrCore_form_delete_session();
                $add = '';
                if (isset($_post['r']) && strlen($_post['r']) > 0) {
                    $add = '/r=1';
                    if ($_post['r'] != '1') {
                        if ($url = jrCore_get_memory_url($_post['r'])) {
                            $add = "/r={$_post['r']}";
                        }
                    }
                }
                jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/activate/{$code}{$add}");
                break;

            case 'admin':

                // Admin validation
                if (isset($_conf['jrUser_signup_notify']) && $_conf['jrUser_signup_notify'] == 'on') {
                    $_ad = jrUser_get_admin_user_ids();
                    if ($_ad && is_array($_ad)) {
                        $_rp                    = $_data;
                        $_rp['signup_method']   = 'admin';
                        $_rp['system_name']     = $_conf['jrCore_system_name'];
                        $_rp['ip_address']      = jrCore_get_ip();
                        $_rp['new_profile_url'] = "{$_conf['jrCore_base_url']}/" . jrCore_url_string($_post['user_name']);
                        list($sub, $msg) = jrCore_parse_email_templates('jrUser', 'notify_signup', $_rp);
                        jrUser_notify($_ad, 0, 'jrUser', 'signup_notify', $sub, $msg);
                    }
                }
                $_ln = jrUser_load_lang_strings();
                jrCore_form_delete_session();
                jrCore_notice_page('success', "<p>{$_ln['jrUser'][105]}</p>", $_conf['jrCore_base_url'], $_ln['jrCore'][87], false);
                break;

            default:

                $add = '';
                if (isset($_post['r']) && strlen($_post['r']) > 0) {
                    if ($url = jrCore_get_memory_url($_post['r'])) {
                        $add = "/r={$_post['r']}";
                    }
                }
                $url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/activate/{$code}{$add}";
                // Send User Account validation email - Admins are sent a notification
                // email AFTER the user has validated their account
                $_rp = array(
                    'system_name'    => $_conf['jrCore_system_name'],
                    'activation_url' => $url
                );
                list($sub, $msg) = jrCore_parse_email_templates('jrUser', 'signup', $_rp);
                jrCore_send_email($_post['user_email'], $sub, $msg);

                // Our User Account is created...
                $_ln = jrUser_load_lang_strings();
                jrCore_logger('INF', "{$_post['user_email']} has signed up and is pending validation");
                jrCore_form_delete_session();
                jrCore_notice_page('success', "<p>{$_ln['jrUser'][37]}</p>", $_conf['jrCore_base_url'], $_ln['jrCore'][87], false);
                break;
        }

    }
    return true;
}

//------------------------------
// activation_resend
//------------------------------
function view_jrUser_activation_resend($_post, $_user, $_conf)
{
    jrCore_validate_location_url();
    // Prevent abuse
    if (!isset($_SESSION['allow_activation_resend'])) {
        jrCore_notice_page('error', 89);
    }
    // Our user_id will come in as _1
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('error', 89);
    }
    // Get our user info
    $_rt = jrCore_db_get_item('jrUser', $_post['_1'], true); // OK
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_notice_page('error', 89);
    }
    // See if this user is already activated
    if (isset($_rt['user_validated']) && $_rt['user_validated'] != '0') {
        jrCore_notice_page('error', 56);
    }
    // Resend User Account validation email
    $_rp = array(
        'system_name'    => $_conf['jrCore_system_name'],
        'activation_url' => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/activate/{$_rt['user_validate']}"
    );
    list($sub, $msg) = jrCore_parse_email_templates('jrUser', 'signup', $_rp);
    jrCore_send_email($_rt['user_email'], $sub, $msg);

    // Our User Account is created...
    jrCore_logger('INF', "{$_rt['user_email']} resent account activation email");
    jrCore_form_delete_session();
    $_ln = jrUser_load_lang_strings();
    jrCore_notice_page('success', "<p>{$_ln['jrUser'][37]}</p>", $_conf['jrCore_base_url'], $_ln['jrCore'][87], false);
}

//------------------------------
// activate
//------------------------------
function view_jrUser_activate($_post, $_user, $_conf)
{
    global $_user;
    // Bring in user and language
    ignore_user_abort();
    jrUser_load_lang_strings();

    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'md5')) {
        jrCore_notice_page('error', 38, false, false, false);
    }

    $url = false;
    if (isset($_post['r']) && strlen($_post['r']) > 0) {
        if ($url = jrCore_get_memory_url($_post['r'])) {
            jrCore_delete_memory_url($_post['r']);
        }
    }

    // Make sure account has been created
    $_rt = jrCore_db_get_item_by_key('jrUser', 'user_validate', $_post['_1'], true, true);
    if (!$_rt || !is_array($_rt)) {
        jrCore_notice_page('error', 38, false, false, false);
    }
    // Make sure this account has not already been validated
    if ($_rt['user_validated'] != '0') {
        jrCore_set_form_notice('success', 56);
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/login");
    }

    // Update user account so it is active
    $_data = array(
        'user_last_login' => 'UNIX_TIMESTAMP()',
        'user_active'     => '1',
        'user_validated'  => '1'
    );
    jrCore_db_update_item('jrUser', $_rt['_user_id'], $_data);

    // Reload user data
    $_rt = jrUser_get_user_session_data($_rt['_user_id']);
    $_rt = jrCore_trigger_event('jrUser', 'signup_activated', $_rt);
    jrCore_logger('INF', "{$_rt['user_email']} has validated their account and logged in");

    // Reset this user's cache
    jrUser_reset_cache($_rt['_user_id']);

    // Startup session with user info
    $_SESSION = $_rt;
    $_user    = $_SESSION;
    unset($_rt);
    $_SESSION['is_logged_in'] = true;

    // Save home profile keys
    jrUser_save_profile_home_keys();

    // Login Success Trigger - other modules can add to our User Info
    $_user = jrCore_trigger_event('jrUser', 'login_success', $_user);

    if (jrUser_is_master() && jrCore_db_get_datastore_item_count('jrUser') === 1) {
        // This will only happen on the FIRST account in the system
        jrCore_notice_page('success', 39, "{$_conf['jrCore_base_url']}/core/system_check", 'Continue to System Check', false);
    }

    // If signup_page set in quota, redirect as appropriate
    if (!$url && isset($_user['quota_jrUser_signup_page'])) {
        $_ln = jrUser_load_lang_strings();
        $spg = strtolower(trim($_user['quota_jrUser_signup_page']));
        if ($spg == 'index') {
            jrCore_notice_page('success', 39, $_conf['jrCore_base_url'], $_ln['jrCore'][87], false);
        }
        elseif ($spg == 'profile') {
            jrCore_notice_page('success', 39, "{$_conf['jrCore_base_url']}/{$_user['profile_url']}", $_ln['jrCore'][87], false);
        }
        elseif (jrCore_checktype($spg, 'url')) {
            jrCore_notice_page('success', 39, $spg, $_ln['jrCore'][87], false);
        }
    }

    // Show them success
    if ($url && strpos($url, $_conf['jrCore_base_url']) === 0) {
        $_ln = jrUser_load_lang_strings();
        jrCore_notice_page('success', 39, $url, $_ln['jrCore'][87], false);
    }
    // Save from a signup?
    $url = jrCore_get_cookie('jruser_save_location');
    if ($url && strpos($url, $_conf['jrCore_base_url']) === 0) {
        $_ln = jrUser_load_lang_strings();
        jrCore_notice_page('success', 39, $url, $_ln['jrCore'][87], false);
    }
    else {
        jrCore_notice_page('success', 39, "{$_conf['jrCore_base_url']}/{$_user['profile_url']}", 54, false);
    }
    return true;
}

//------------------------------
// login
//------------------------------
function view_jrUser_login($_post, $_user, $_conf)
{
    if (isset($_post['_1']) && strlen($_post['_1']) > 0) {
        switch ($_post['_1']) {
            case 'widget':
                jrCore_page_set_no_header_or_footer();
                break;
            case 'modal':
                jrCore_page_set_meta_header_only();
                break;
        }
    }
    else {
        // If the user is already logged in, redirect to profile page
        if (jrUser_is_logged_in()) {
            $url = jrUser_get_profile_home_key('profile_url');
            jrCore_location("{$_conf['jrCore_base_url']}/{$url}");
        }
        if (isset($_conf['jrUser_force_ssl']) && $_conf['jrUser_force_ssl'] == 'on' && strpos(jrCore_get_current_url(), 'http:') === 0 && strpos($_conf['jrCore_base_url'], 'https:') === 0) {
            // We are running on an HTTPS site - move to HTTPS
            $url = str_replace('http://', 'https://', jrCore_get_current_url());
            jrCore_location($url);
        }
        // See if we are being asked to remember our location URL
        if (isset($_post['r']) && strlen($_post['r']) > 0) {
            if ($_post['r'] != 1 && $url = jrCore_get_memory_url($_post['r'])) {
                jrCore_set_cookie('jruser_save_location', $url, 1);
            }
            elseif ($url = jrCore_get_local_referrer()) {
                jrCore_set_cookie('jruser_save_location', $url, 1);
            }
        }
    }

    $_lang = jrUser_load_lang_strings();
    // Check for maintenance mode
    if (isset($_conf['jrCore_maintenance_mode']) && $_conf['jrCore_maintenance_mode'] == 'on') {
        jrCore_set_form_notice('notice', $_lang['jrCore'][35]);
    }

    // our page banner
    if (!isset($_post['_1'])) {
        $html = '';
        if (isset($_conf['jrUser_signup_on']) && $_conf['jrUser_signup_on'] == 'on') {
            $add = '';
            if (isset($_post['r']) && strlen($_post['r']) > 0) {
                $add = '/r=1';
                if ($_post['r'] != 1) {
                    if ($url = jrCore_get_memory_url($_post['r'])) {
                        $add = "/r={$_post['r']}";
                    }
                }
            }
            $html = jrCore_page_button('signup', $_lang['jrUser'][31], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/signup{$add}')");
        }
        $html .= jrCore_page_button('forgot', $_lang['jrUser'][41], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/forgot')");
        jrCore_page_banner(40, $html, false);
    }
    if (isset($_post['r']) && $_post['r'] == '1' && !isset($_SESSION['jrcore_form_notices'])) {
        jrCore_set_form_notice('error', $_lang['jrUser'][108]);
    }
    if (isset($_conf['jrUser_login_note']) && strlen($_conf['jrUser_login_note']) > 2) {
        jrCore_set_form_notice('notice', $_conf['jrUser_login_note'], false);
    }
    jrCore_get_form_notice();

    // Form init
    if (!isset($_post['_1'])) {
        $_tmp = array(
            'submit_value' => 3,
            'cancel'       => jrCore_is_local_referrer()
        );
        $tok  = jrCore_form_create($_tmp);
    }
    else {
        $_tmp = array(
            'submit_value' => 3,
            'cancel'       => false
        );
        $tok  = jrCore_form_create($_tmp);
    }

    // User Email OR User Name
    $_tmp = array(
        'name'           => 'user_email_or_name',
        'label'          => 1,
        'help'           => 125,
        'type'           => 'text',
        'validate'       => 'not_empty',
        'autocapitalize' => 'off',
        'autocorrect'    => 'off'
    );
    jrCore_form_field_create($_tmp);

    // Password
    $_tmp = array(
        'name'       => 'user_password',
        'label'      => 7,
        'help'       => 8,
        'type'       => 'password',
        'error_msg'  => 9,
        'validate'   => 'not_empty',
        'onkeypress' => "if (event && event.keyCode == 13 && this.value.length > 0) { jrFormSubmit('#jrUser_login','{$tok}','ajax'); }"
    );
    jrCore_form_field_create($_tmp);

    // Remember Me
    $_tmp = array(
        'name'     => 'user_remember',
        'label'    => 13,
        'help'     => 14,
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// login_save
//------------------------------
function view_jrUser_login_save($_post, $_user, $_conf)
{
    global $_user;
    jrCore_form_validate($_post);

    $uip = jrCore_get_ip();
    if (jrUser_get_password_attempts($uip, 60) > 10) {
        // We've had more than 10 login attempts from this IP
        jrCore_set_form_notice('error', 26);
        jrCore_form_result();
    }

    // Make sure user is valid
    $key1 = 'user_name';
    $key2 = 'user_email';
    if (strpos($_post['user_email_or_name'], '@')) {
        $key1 = 'user_email';
        $key2 = 'user_name';
    }
    $_rt = jrCore_db_get_item_by_key('jrUser', $key1, $_post['user_email_or_name'], false, true);
    if (!$_rt || !is_array($_rt)) {
        $_rt = jrCore_db_get_item_by_key('jrUser', $key2, $_post['user_email_or_name'], false, true);
        if (!$_rt || !is_array($_rt)) {
            jrCore_set_form_notice('error', 26);
            jrCore_form_result();
        }
    }

    // Validate password
    if (!jrUser_verify_password_hash($_post['user_password'], $_rt['user_password'])) {
        if (isset($_rt['user_group']) && $_rt['user_group'] != 'user') {
            jrCore_logger('MAJ', "invalid login attempt for {$_rt['user_group']} user account {$_rt['user_name']} - bad password");
        }
        jrCore_set_form_notice('error', 26);
        jrCore_form_result();
    }

    // Make sure account is validated
    if (!isset($_rt['user_validated']) || $_rt['user_validated'] != '1') {
        $_ln = jrUser_load_lang_strings();
        if (isset($_rt['quota_jrUser_signup_method']) && $_rt['quota_jrUser_signup_method'] == 'email') {
            // Give the user the ability to resend the activation email
            $_SESSION['allow_activation_resend'] = 1;
            $tmp                                 = jrCore_page_button('resend', $_ln['jrUser'][28], "jrCore_window_location('" . $_conf['jrCore_base_url'] . '/' . $_post['module_url'] . '/activation_resend/' . $_rt['_user_id'] . "')");
            jrCore_set_form_notice('error', $_ln['jrUser'][27] . '<br><br>' . $tmp, false);
        }
        else {
            jrCore_set_form_notice('error', $_ln['jrUser'][27]);
        }
        jrCore_form_result();
    }

    // Make sure account is active
    if (!isset($_rt['user_active']) || $_rt['user_active'] != '1') {
        jrCore_set_form_notice('error', 29);
        jrCore_form_result();
    }

    // Make sure we have a valid profile id
    if (!isset($_rt['_profile_id']) || !jrCore_checktype($_rt['_profile_id'], 'number_nz')) {
        jrCore_set_form_notice('error', 26);
        jrCore_form_result();
    }

    // Make sure the profile we are attached to is still valid
    if (!isset($_rt['profile_url'])) {
        // The profile we are linked to did not get added in - check if it exists
        $_pr = jrCore_db_get_item('jrProfile', $_rt['_profile_id']);
        if (!$_pr || !is_array($_pr)) {
            // The profile this user account was linked to no longer exists
            // See if this user is linked to another profile in the system
            $_lp = jrProfile_get_user_linked_profiles($_rt['_user_id']);
            if ($_lp && is_array($_lp)) {
                if (isset($_lp["{$_rt['_profile_id']}"])) {
                    // Cleanup
                    unset($_lp["{$_rt['_profile_id']}"]);
                    $tbl = jrCore_db_table_name('jrProfile', 'profile_link');
                    $req = "DELETE FROM {$tbl} WHERE user_id = '{$_rt['_user_id']}' AND profile_id = '{$_rt['_profile_id']}'";
                    jrCore_db_query($req);
                }
                if (count($_lp) > 0) {
                    // The first one that is
                    foreach ($_lp as $pid => $uid) {
                        $_pr = jrCore_db_get_item('jrProfile', $pid);
                        if ($_pr && is_array($_pr)) {
                            jrCore_db_update_item('jrUser', $_rt['_user_id'], array(), array('_profile_id' => $pid));
                            break;
                        }
                    }
                }
                else {
                    // Bad user account
                    jrCore_logger('CRI', 'User attempted login to account with no associated profile', $_rt);
                    jrCore_notice_page('error', 'Your Account is missing important information and will not function properly.<br>Please contact the site administrator for assistance on accessing your account.', $_conf['jrCore_base_url'], null, false);
                }
            }
        }
    }

    // See if this user is logging in for the first time on a new device
    if (isset($_rt['quota_jrUser_device_notice']) && $_rt['quota_jrUser_device_notice'] == 'on') {
        jrUser_notify_if_new_device($_rt['_user_id']);
    }

    // Startup Session and login
    if (!$_SESSION = jrUser_get_user_session_data($_rt['_user_id'], $_rt['_profile_id'])) {
        jrCore_set_form_notice('error', 10);
        jrCore_form_result();
    }
    if (!is_array($_SESSION)) {
        // Could not start a session
        jrCore_set_form_notice('error', 10);
        jrCore_form_result();
    }

    // Save home profile keys
    jrUser_save_profile_home_keys();

    // Maintenance login check
    if (jrCore_is_maintenance_mode($_conf, $_post)) {
        jrCore_set_form_notice('error', 30);
        jrCore_form_result();
    }

    // Remove any forgot password entries
    jrUser_delete_forgot_password_entries($_SESSION['_user_id']);

    // User has logged in - reset any failed password attempts on this IP
    jrUser_reset_password_attempts($uip);

    // Update last login time
    $_data = array(
        'user_last_login' => 'UNIX_TIMESTAMP()'
    );
    jrCore_db_update_item('jrUser', $_SESSION['_user_id'], $_data);
    $_SESSION['user_last_login'] = time();
    $_SESSION['is_logged_in']    = 'yes';

    // Bring in all profile and Quota info
    $_SESSION = jrUser_session_start();

    // Setup our "remember me" cookie if requested
    if (isset($_post['user_remember']) && $_post['user_remember'] === 'on') {
        jrUser_session_set_login_cookie($_SESSION['_user_id']);
    }
    else {
        jrUser_session_delete_login_cookie();
    }

    // Send out trigger on successful account activation
    $_user = $_SESSION;
    $_user = jrCore_trigger_event('jrUser', 'login_success', $_user);

    jrCore_logger('INF', "successful login by {$_post['user_email_or_name']}");
    jrCore_form_delete_session();

    // If login_page set in quota, redirect as appropriate
    if (isset($_user['quota_jrUser_login_page'])) {
        $login_page = strtolower(trim($_user['quota_jrUser_login_page']));
        if ($login_page == 'index') {
            jrCore_form_result($_conf['jrCore_base_url']);
        }
        elseif (jrCore_checktype($login_page, 'url')) {
            jrCore_form_result($login_page);
        }
    }

    // Redirect to Profile or Saved Location
    $url = jrUser_get_saved_location();
    if ($url && jrCore_checktype($url, 'url') && strpos($url, $_conf['jrCore_base_url']) === 0 && $url != $_conf['jrCore_base_url'] && $url != $_conf['jrCore_base_url'] . '/' && !strpos($url, '/signup')) {
        jrCore_form_result($url);
    }
    $url = "{$_conf['jrCore_base_url']}/{$_user['profile_url']}";
    jrCore_form_result($url);
}

//------------------------------
// logout
//------------------------------
function view_jrUser_logout($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_end_user_session($_user);
    jrCore_form_result($_conf['jrCore_base_url']);
}

//------------------------------
// forgot
//------------------------------
function view_jrUser_forgot($_post, $_user, $_conf)
{
    // Check for maintenance mode
    if (isset($_conf['jrCore_maintenance_mode']) && $_conf['jrCore_maintenance_mode'] == 'on') {
        $_lang = jrUser_load_lang_strings();
        jrCore_set_form_notice('notice', $_lang['jrCore'][35]);
    }

    // our page banner
    jrCore_page_banner(44, null, false);

    // Form init
    $_tmp = array(
        'submit_value'     => 46,
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/login",
        'form_ajax_submit' => false
    );
    $tok  = jrCore_form_create($_tmp);

    // User Email OR User Name
    $_tmp = array(
        'name'           => 'user_email',
        'label'          => 103,
        'help'           => 104,
        'type'           => 'text',
        'validate'       => 'not_empty',
        'autocapitalize' => 'off',
        'autocorrect'    => 'off',
        'onkeypress'     => "if (event && event.keyCode == 13 && this.value.length > 0) { jrFormSubmit('#jrUser_forgot','{$tok}','ajax'); }"
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// forgot_save
//------------------------------
function view_jrUser_forgot_save($_post, $_user, $_conf)
{
    jrCore_form_validate($_post);

    // Make sure user is valid
    if (jrCore_checktype($_post['user_email'], 'email')) {
        $_rt = jrCore_db_get_item_by_key('jrUser', 'user_email', $_post['user_email']);
    }
    else {
        $_rt = jrCore_db_get_item_by_key('jrUser', 'user_name', $_post['user_email']);
    }
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 48);
        jrCore_form_result();
    }

    // See if this user is still pending activation
    if (!isset($_rt['user_validated']) || $_rt['user_validated'] != '1') {

        $_ln = jrUser_load_lang_strings();
        if (isset($_rt['quota_jrUser_signup_method']) && $_rt['quota_jrUser_signup_method'] == 'email') {
            // Give the user the ability to resend the activation email
            $_SESSION['allow_activation_resend'] = 1;
            $tmp                                 = jrCore_page_button('resend', $_ln['jrUser'][28], "jrCore_window_location('" . $_conf['jrCore_base_url'] . '/' . $_post['module_url'] . '/activation_resend/' . $_rt['_user_id'] . "')");
            jrCore_notice_page('error', '<p>' . $_ln['jrUser'][27] . '<br><br>' . $tmp . '</p>', $_conf['jrCore_base_url'], $_ln['jrCore'][87], false);
        }
        jrCore_notice_page('error', $_ln['jrUser'][27], $_conf['jrCore_base_url'], $_ln['jrCore'][87], false);
    }

    // Okay - this user is requesting a password reset:
    // - set a temp "reset" key
    // - send email to the address with the proper key URL
    // - user comes to form and resets password
    // - reset ALL user sessions/cookies for this user
    // - Send user an email letting them know their password was changed

    // New Entry
    $key = md5(microtime());
    $tbl = jrCore_db_table_name('jrUser', 'forgot');
    $req = "INSERT INTO {$tbl} (forgot_user_id,forgot_time,forgot_key) VALUES ('{$_rt['_user_id']}',UNIX_TIMESTAMP(),'" . jrCore_db_escape($key) . "')";
    $uid = jrCore_db_query($req, 'INSERT_ID');
    if (!$uid || !jrCore_checktype($uid, 'number_nz')) {
        jrCore_set_form_notice('error', 36);
        jrCore_form_result();
    }

    // Send out password reset email
    $_rp = array(
        'system_name' => $_conf['jrCore_system_name'],
        'reset_url'   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/new_password/{$key}"
    );
    list($sub, $msg) = jrCore_parse_email_templates('jrUser', 'forgot', $_rp);
    jrCore_send_email($_rt['user_email'], $sub, $msg);

    // Our User Account is created...
    jrCore_logger('INF', "{$_rt['user_email']} has requested a password reset");

    jrCore_form_delete_session();
    $_ln = jrUser_load_lang_strings();
    jrCore_notice_page('success', "<p>{$_ln['jrUser'][49]}</p>", $_conf['jrCore_base_url'], $_ln['jrCore'][87], false);
}

//------------------------------
// new_password
//------------------------------
function view_jrUser_new_password($_post, $_user, $_conf)
{
    // Make sure our token is valid
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'md5')) {
        jrCore_notice_page('error', 52);
    }

    // Validate Token
    $tbl = jrCore_db_table_name('jrUser', 'forgot');
    $req = "SELECT * FROM {$tbl} WHERE forgot_key = '" . jrCore_db_escape($_post['_1']) . "' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        jrCore_notice_page('error', 52);
    }

    // Check for maintenance mode
    if (isset($_conf['jrCore_maintenance_mode']) && $_conf['jrCore_maintenance_mode'] == 'on') {
        $_lang = jrUser_load_lang_strings();
        jrCore_set_form_notice('notice', $_lang['jrCore'][35]);
    }

    // our page banner
    jrCore_page_banner(50);

    // Form init
    $_tmp = array(
        'submit_value' => 51,
        'cancel'       => false
    );
    $tok  = jrCore_form_create($_tmp);

    // Token
    $_tmp = array(
        'name'  => 'password_token',
        'type'  => 'hidden',
        'value' => $_post['_1']
    );
    jrCore_form_field_create($_tmp);

    // Password #1
    $_tmp = array(
        'name'      => 'user_passwd1',
        'label'     => 20,
        'help'      => 21,
        'type'      => 'password',
        'error_msg' => 9,
        'validate'  => 'not_empty'
    );
    jrCore_form_field_create($_tmp);

    // Password #2
    $_tmp = array(
        'name'       => 'user_passwd2',
        'label'      => 22,
        'help'       => 23,
        'type'       => 'password',
        'error_msg'  => 9,
        'validate'   => 'not_empty',
        'onkeypress' => "if (event && event.keyCode == 13 && this.value.length > 0) { jrFormSubmit('#jrUser_new_password','{$tok}','ajax'); }"
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// new_password_save
//------------------------------
function view_jrUser_new_password_save($_post, $_user, $_conf)
{
    jrCore_form_validate($_post);

    // Make sure our token is valid
    if (!isset($_post['password_token']) || !jrCore_checktype($_post['password_token'], 'md5')) {
        jrCore_set_form_notice('error', 52);
        jrCore_form_result();
    }

    // Validate Token
    $tkn = jrCore_db_escape($_post['password_token']);
    $tbl = jrCore_db_table_name('jrUser', 'forgot');
    $req = "SELECT * FROM {$tbl} WHERE forgot_key = '{$tkn}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 52);
        jrCore_form_result();
    }

    // Get user info
    $_us = jrCore_db_get_item('jrUser', $_rt['forgot_user_id'], true);
    if (!$_us || !is_array($_us)) {
        jrCore_set_form_notice('error', 52);
        jrCore_form_result();
    }

    // Make sure passwords match
    if (!isset($_post['user_passwd1']) || strlen($_post['user_passwd1']) === 0 || !isset($_post['user_passwd2']) || strlen($_post['user_passwd2']) === 0) {
        jrCore_set_form_notice('error', 35);
        jrCore_form_field_hilight('user_passwd1');
        jrCore_form_field_hilight('user_passwd2');
        jrCore_form_result();
    }
    if (isset($_post['user_passwd1']) && isset($_post['user_passwd2']) && $_post['user_passwd1'] != $_post['user_passwd2']) {
        jrCore_set_form_notice('error', 35);
        jrCore_form_field_hilight('user_passwd1');
        jrCore_form_field_hilight('user_passwd2');
        jrCore_form_result();
    }
    // Setup new password
    $pass = jrUser_get_password_hash($_post['user_passwd1']);

    // Update user with new password
    $_dt = array(
        'user_password'   => $pass,
        'user_last_login' => 'UNIX_TIMESTAMP()'
    );
    if (!jrCore_db_update_item('jrUser', $_rt['forgot_user_id'], $_dt)) {
        jrCore_set_form_notice('error', 36);
        jrCore_form_result();
    }
    // has user been validated yet
    if (!isset($_us['user_validated']) || $_us['user_validated'] != '1') {
        jrCore_set_form_notice('error', 27);
        jrCore_form_result();
    }

    // Remove all existing sessions
    jrUser_session_remove($_us['_user_id']);

    // Remove any "remember me" cookie
    $tbl = jrCore_db_table_name('jrUser', 'cookie');
    $req = "DELETE FROM {$tbl} WHERE cookie_user_id = '{$_us['_user_id']}'";
    jrCore_db_query($req);

    // Reset user cache
    jrUser_reset_cache($_us['_user_id']);

    // Cleanup forgot
    jrUser_delete_forgot_password_entries($_us['_user_id']);

    // User has logged in - reset any failed password attempts on this IP
    jrUser_reset_password_attempts(jrCore_get_ip());

    // Log user in if we are NOT in maintenance mode
    if (isset($_conf['jrCore_maintenance_mode']) && $_conf['jrCore_maintenance_mode'] == 'on') {
        // If we are NOT an admin...
        if ($_us['user_group'] != 'master' && $_us['user_group'] != 'admin') {
            jrCore_notice_page('error', 30);
        }
    }

    // Startup session with user info
    $_SESSION = jrUser_get_user_session_data($_us['_user_id']);
    $_SESSION = jrCore_trigger_event('jrUser', 'login_success', $_SESSION);

    // Set NEW remember me cookie if enabled
    if (isset($_conf['jrUser_autologin']) && intval($_conf['jrUser_autologin']) > 1) {
        jrUser_session_set_login_cookie($_us['_user_id']);
    }

    // See if this user is logging in for the first time on a new device
    if (isset($_SESSION['quota_jrUser_device_notice']) && $_SESSION['quota_jrUser_device_notice'] == 'on') {
        jrUser_notify_if_new_device($_us['_user_id']);
    }

    // Show them success
    jrCore_logger('INF', "@{$_SESSION['profile_url']} ({$_SESSION['user_email']}) has reset their password and logged in");

    // Redirect to Profile
    if (!empty($_SESSION['profile_url'])) {
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_SESSION['profile_url']}");
    }
    // Fall through - system index
    jrCore_form_result($_conf['jrCore_base_url']);
}

//------------------------------
// account
//------------------------------
function view_jrUser_account($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    $_lang = jrUser_load_lang_strings();

    // See if we are modifying a different account
    $frm = true;
    if (jrUser_is_admin() && (isset($_post['profile_id']) || isset($_post['user_id']))) {

        // If the user account we are modifying is attached to more than 1 profile,
        // we need to make sure we are using the profile_id as passed in
        if (isset($_post['profile_id']) && jrCore_checktype($_post['profile_id'], 'number_nz')) {

            // We also must get a valid user_id
            if (!isset($_post['user_id']) || !jrCore_checktype($_post['user_id'], 'number_nz')) {
                jrCore_notice_page('error', 'invalid user_id received - please try again');
            }
            $_pr   = jrCore_db_get_item('jrProfile', $_post['profile_id'], true, true);
            $_us   = jrCore_db_get_item('jrUser', $_post['user_id'], true, true);
            $_qt   = jrProfile_get_quota($_pr['profile_quota_id']);
            $_data = array_merge($_us, $_pr, $_qt);
            unset($_pr);

            // See if there is more than 1 user account associated with this profile
            $tbl = jrCore_db_table_name('jrProfile', 'profile_link');
            $req = "SELECT * FROM {$tbl} WHERE profile_id = '{$_post['profile_id']}'";
            $_xu = jrCore_db_query($req, 'user_id', false, 'profile_id');

        }
        elseif ($_post['user_id'] != $_user['_user_id']) {
            $_us = jrCore_db_get_item('jrUser', $_post['user_id'], false, true);
            if ($_pr = jrCore_db_get_item('jrProfile', $_us['_profile_id'], false, true)) {
                $_qt   = jrProfile_get_quota($_pr['profile_quota_id']);
                $_data = array_merge($_us, $_pr, $_qt);
            }
            $_data['_user_id'] = $_post['user_id'];
        }
        else {
            $_data = $_user;
        }

        jrUser_account_tabs('account', $_data);

        if ($_data && is_array($_data)) {
            if ($_data['user_name'] != $_user['user_name']) {
                jrCore_set_form_notice('notice', "You are viewing the user account information for the user <b>{$_data['user_name']}</b>", false);
            }
            if (isset($_data['user_blocked']) && $_data['user_blocked'] == '1') {
                jrCore_set_form_notice('error', "This User Account is <b>blocked</b> - the user cannot log in to the site", false);
            }
        }
        else {
            jrCore_page_notice('error', "Invalid id - no User Account data was found for user_id {$_post['user_id']}");
            $frm = false;
        }
        $button = jrCore_page_button('p', $_data['profile_name'], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_data['profile_url']}')");
        $uid    = $_data['_user_id'];
        $pid    = $_data['_profile_id'];
    }
    else {
        if (jrUser_is_admin()) {
            // If we are dropping in directly from another profile, we need to
            // make sure we are setup on our OWN profile
            $pid = jrUser_get_profile_home_key('_profile_id');
            if ($_user['user_active_profile_id'] != $pid) {
                $_pr = jrCore_db_get_item('jrProfile', $pid, false, true);
                $_qt = jrProfile_get_quota($_pr['profile_quota_id']);

                global $_user;
                $_user = array_merge($_user, $_pr, $_qt);
            }
        }
        jrUser_account_tabs('account', $_user);
        $button = jrCore_page_button('p', $_user['profile_name'], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_user['profile_url']}')");
        $uid    = $_user['_user_id'];
        $pid    = $_user['_profile_id'];
    }

    if ($frm) {
        // We're showing our form
        // See if this is a profile admin
        if (isset($_data)) {

            // User ID we are modifying
            $_tmp = array(
                'name'     => 'user_id',
                'type'     => 'hidden',
                'value'    => $uid,
                'validate' => 'number_nz'
            );
            jrCore_form_field_create($_tmp);

            // Profile ID we are modifying
            $_tmp = array(
                'name'     => 'profile_id',
                'type'     => 'hidden',
                'value'    => $pid,
                'validate' => 'number_nz'
            );
            jrCore_form_field_create($_tmp);
        }
        else {
            $_data               = jrCore_db_get_item('jrUser', $_user['_user_id'], true); // OK
            $_post['user_id']    = $_user['_user_id'];
            $_post['profile_id'] = $_user['_profile_id'];
        }

        // Make sure we set error if no email address - this can happen using the social login
        if (!jrCore_checktype($_user['user_email'], 'email') && !isset($_SESSION['show_email_exists_error'])) {
            jrCore_set_form_notice('error', 68);
            jrCore_form_field_hilight('user_email');
        }
        if (isset($_SESSION['show_email_exists_error'])) {
            unset($_SESSION['show_email_exists_error']);
        }

        // our page banner
        jrCore_page_banner(42, $button, false);
        jrCore_get_form_notice();

        // User Jumper
        if (jrUser_is_admin() && isset($_xu) && is_array($_xu) && count($_xu) > 1) {
            $_un = jrCore_db_get_multiple_items('jrUser', array_keys($_xu), array('_user_id', 'user_name'));
            if ($_un && is_array($_un) && count($_un) > 0) {
                $html = '<select name="selected_user_id" class="form_select" onchange="var a=this.options[this.selectedIndex].value;jrCore_window_location(\'' . $_conf['jrCore_base_url'] . '/' . $_post['module_url'] . '/account/profile_id=' . $_post['profile_id'] . '/user_id=\'+ a);">' . "\n";
                $ucnt = 0;
                foreach ($_un as $v) {
                    if (strlen($v['user_name']) > 0) {
                        if ($v['_user_id'] == $_post['user_id']) {
                            $html .= '<option value="' . $v['_user_id'] . '" selected> ' . $v['user_name'] . '</option>';
                        }
                        else {
                            $html .= '<option value="' . $v['_user_id'] . '"> ' . $v['user_name'] . '</option>';
                        }
                        $ucnt++;
                    }
                }
                $html .= '</select><br>&nbsp;<small>There are <strong>' . $ucnt . ' User Accounts</strong> associated with this profile.</small>';
                jrCore_page_custom($html, 'user account', 'select to make active');
            }
        }

        // Form init
        $_tmp = array(
            'submit_value'     => $_lang['jrCore'][72],
            'cancel'           => 'reset_form_referrer',
            'values'           => $_data,
            'form_ajax_submit' => false
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

        // User Avatar
        $_tmp = array(
            'name'         => 'user_image',
            'label'        => 53,
            'help'         => 93,
            'type'         => 'image',
            'size'         => 'medium',
            'image_delete' => true,
            'required'     => false
        );
        if (isset($_us) && is_array($_us)) {
            $_tmp['value'] = $_us;
        }
        jrCore_form_field_create($_tmp);

        // User Name
        $_tmp = array(
            'name'           => 'user_name',
            'label'          => 4,
            'help'           => 5,
            'type'           => 'text',
            'validate'       => 'printable',
            'autocapitalize' => 'off',
            'autocorrect'    => 'off',
            'required'       => true,
            'readonly'       => 'readonly',
            'onfocus'        => "this.removeAttribute('readonly');"
        );
        jrCore_form_field_create($_tmp);

        // User Email
        $_tmp = array(
            'name'           => 'user_email',
            'label'          => 18,
            'help'           => 57,
            'type'           => 'text',
            'validate'       => 'email',
            'autocapitalize' => 'off',
            'autocorrect'    => 'off',
            'required'       => true,
            'readonly'       => 'readonly',
            'onfocus'        => "this.removeAttribute('readonly');"
        );
        jrCore_form_field_create($_tmp);

        // Preferred Language
        $_lng = jrUser_get_languages();
        if ($_lng && is_array($_lng) && count($_lng) > 1) {
            $_tmp = array(
                'name'     => 'user_language',
                'label'    => 62,
                'help'     => 63,
                'type'     => 'select',
                'options'  => 'jrUser_get_languages',
                'required' => true
            );
            jrCore_form_field_create($_tmp);
        }

        // Password #1
        $_tmp = array(
            'name'      => 'user_passwd1',
            'label'     => 7,
            'help'      => 8,
            'type'      => 'password',
            'error_msg' => 9,
            'required'  => false,
            'validate'  => 'not_empty',
            'readonly'  => 'readonly',
            'onfocus'   => "this.removeAttribute('readonly');"
        );
        if (isset($_user['user_temp_password'])) {
            $_tmp['required'] = true;
        }
        jrCore_form_field_create($_tmp);

        // Password #2
        $_tmp = array(
            'name'      => 'user_passwd2',
            'label'     => 32,
            'help'      => 23,
            'type'      => 'password',
            'error_msg' => 9,
            'required'  => false,
            'validate'  => 'not_empty',
            'readonly'  => 'readonly',
            'onfocus'   => "this.removeAttribute('readonly');"
        );
        if (isset($_user['user_temp_password'])) {
            $_tmp['required'] = true;
        }
        jrCore_form_field_create($_tmp);

        $_tmp = jrCore_get_flag('jruser_register_setting');
        if ($_tmp) {
            foreach ($_tmp as $smod => $_entries) {
                // Make sure the viewing user has Quota access to this module
                if (isset($_user["quota_{$smod}_allowed"]) && $_user["quota_{$smod}_allowed"] != 'on') {
                    continue;
                }
                foreach ($_entries as $_field) {
                    // Language replacements...
                    if (isset($_field['label']) && jrCore_checktype($_field['label'], 'number_nz') && isset($_lang[$smod]["{$_field['label']}"])) {
                        $_field['label'] = $_lang[$smod]["{$_field['label']}"];
                    }
                    if (isset($_field['help']) && jrCore_checktype($_field['help'], 'number_nz') && isset($_lang[$smod]["{$_field['help']}"])) {
                        $_field['help'] = $_lang[$smod]["{$_field['help']}"];
                    }
                    if (isset($_field['error_msg']) && jrCore_checktype($_field['error_msg'], 'number_nz') && isset($_lang[$smod]["{$_field['error_msg']}"])) {
                        $_field['error_msg'] = $_lang[$smod]["{$_field['error_msg']}"];
                    }
                    jrCore_form_field_create($_field);
                }
            }
        }

        // Master Admin options
        if (jrUser_is_master()) {

            $_tmp = array(
                'name'          => 'user_group',
                'label'         => 'user group',
                'help'          => 'Select the user group this user should be part of:<br><br><b>Standard User:</b> a normal user account in your system - can modify items they have created only.<br><b>Profile Admin:</b> can modify users and profiles and items created by any user on the system. Has access to the Dashboard.<br><b>Master Admin:</b> full access to all system areas including the Admin Control Panel and Dashboard.',
                'type'          => 'select',
                'options'       => array('user' => 'Standard User', 'admin' => 'Profile Admin', 'master' => 'Master Admin'),
                'value'         => $_data['user_group'],
                'group'         => 'master',
                'validate'      => 'core_string',
                'form_designer' => false,
                'section'       => 'master admin options',
                'order'         => 250
            );
            // Don't let us change our own account
            if ($_data['_user_id'] == $_user['_user_id']) {
                $_tmp['disabled'] = true;
            }
            jrCore_form_field_create($_tmp);

            // See if this user is linked to more than 1 profile
            $_lp = jrProfile_get_user_linked_profiles($_data['_user_id']);
            if ($_lp && is_array($_lp) && count($_lp) > 1) {

                // looks like this user is linked to more than 1 profile
                $_sc = array(
                    'search'         => array(
                        '_item_id in ' . $_data['_profile_id'] . ',' . implode(',', array_keys($_lp))
                    ),
                    'return_keys'    => array('_profile_id', 'profile_name'),
                    'order_by'       => false,
                    'limit'          => (count($_lp) + 1),
                    'skip_triggers'  => true,
                    'ignore_pending' => true,
                    'privacy_check'  => false
                );
                $_tp = jrCore_db_search_items('jrProfile', $_sc);
                if ($_tp && is_array($_tp) && isset($_tp['_items'])) {
                    $_pr = array();
                    foreach ($_tp['_items'] as $_v) {
                        if ($_data['_profile_id'] == $_v['_profile_id']) {
                            $_pr["{$_v['_profile_id']}"] = "{$_v['profile_name']} (Home Profile)";
                        }
                        else {
                            $_pr["{$_v['_profile_id']}"] = $_v['profile_name'];
                        }
                    }
                    natcasesort($_pr);
                    $_tmp = array(
                        'name'          => 'user_linked_profiles',
                        'label'         => 'linked profiles',
                        'help'          => "This User Account is linked to the listed Profiles. Uncheck a profile to prevent this user from accessing it.",
                        'type'          => 'optionlist',
                        'options'       => $_pr,
                        'value'         => array_keys($_lp),
                        'group'         => 'master',
                        'form_designer' => false,
                        'order'         => 251
                    );
                    jrCore_form_field_create($_tmp);
                }
            }
        }
    }
    jrCore_page_display();
}

//------------------------------
// account_save
//------------------------------
function view_jrUser_account_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_form_validate($_post);

    // Double check email
    if (!isset($_post['user_email']) || !jrCore_checktype($_post['user_email'], 'email')) {
        jrCore_set_form_notice('error', 68);
        jrCore_form_field_hilight('user_email');
        jrCore_form_result();
    }

    // User name checks
    if (!jrUser_is_admin() && !empty($_post['user_name'])) {

        // Check for "confusable" Unicode names
        if (!isset($_conf['jrUser_enable_spoofcheck']) || $_conf['jrUser_enable_spoofcheck'] == 1) {
            if (class_exists('Spoofchecker')) {
                if (filter_var($_post['user_name'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH) != $_post['user_name']) {
                    $spoof = new Spoofchecker();
                    if ($spoof->isSuspicious($_post['user_name'])) {
                        jrCore_set_form_notice('error', 129);
                        jrCore_form_field_hilight('user_name');
                        jrCore_form_result();
                    }
                }
            }
        }

        // Make sure it is not in our list of reserved names
        if (!jrUser_is_admin() && !empty($_post['user_name']) && jrUser_is_reserved_name($_post['user_name'])) {
            jrCore_set_form_notice('error', 129);
            jrCore_form_field_hilight('user_name');
            jrCore_form_result();
        }
    }

    // Get posted data
    $rauth = false;
    $_data = jrCore_form_get_save_data('jrUser', 'account', $_post);

    // Check for changing passwords
    if ((isset($_post['user_passwd1']) && strlen($_post['user_passwd1']) > 0) || (isset($_post['user_passwd2']) && strlen($_post['user_passwd2']) > 0)) {
        if (isset($_post['user_passwd1']) && isset($_post['user_passwd2']) && $_post['user_passwd1'] != $_post['user_passwd2']) {
            jrCore_set_form_notice('error', 35);
            jrCore_form_field_hilight('user_passwd1');
            jrCore_form_field_hilight('user_passwd2');
            jrCore_form_result();
        }
        // Add in new password hash
        $pass                   = jrUser_get_password_hash($_post['user_passwd1']);
        $_data['user_password'] = $pass;
    }

    // Check for forced re-authentication
    if (isset($_conf['jrUser_authenticate']) && $_conf['jrUser_authenticate'] == 'on') {

        if (!isset($_post['user_id']) || !jrCore_checktype($_post['user_id'], 'number_nz')) {
            $_post['user_id'] = $_user['_user_id'];
        }

        if (jrUser_is_admin() && isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz') && $_post['user_id'] != $_user['_user_id']) {
            // Admin changing another account
            $rauth = false;
        }
        else {

            // Forced Password change
            // NOTE: This is set in the OneAll module to force the user to set a NEW password after account creation
            if (isset($_user['user_temp_password']) && !isset($_user['user_temp_token'])) {
                $rauth = false;
            }
            else {
                // Password if we are changing
                if (isset($pass)) {
                    $_data['user_temp_password'] = $pass;
                    unset($_data['user_password']);
                    $rauth = true;
                }

                // Email changing
                if ($_post['user_email'] != $_user['user_email']) {
                    $_data['user_temp_email'] = $_post['user_email'];
                    unset($_data['user_email']);
                    $rauth = true;
                }

                // If 2 factor auth is being turned OFF
                if (jrCore_module_is_active('jrTwoFactor') && isset($_user['user_twofactor_enabled']) && $_user['user_twofactor_enabled'] == 'on' && $_post['user_twofactor_enabled'] == 'off') {
                    $_data['user_temp_twofactor_enabled'] = $_post['user_twofactor_enabled'];
                    unset($_data['user_twofactor_enabled']);
                    $rauth = true;
                }

                if ($rauth) {
                    $_data['user_temp_token'] = $_post['jr_html_form_token'];
                }
            }

        }
    }

    // See if this is an admin modifying this user account
    $uid = $_user['_user_id'];
    if (jrUser_is_admin() && isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz')) {

        $uid = (int) $_post['user_id'];
        $_us = jrCore_db_get_item('jrUser', $uid);
        if (isset($_post['user_group']{0})) {
            $_data['user_group'] = $_post['user_group'];
        }
        // Check for changes in linked profiles
        if (isset($_post['user_linked_profiles']) && strlen($_post['user_linked_profiles']) > 0) {
            $pid = (int) $_post['profile_id'];
            $tbl = jrCore_db_table_name('jrProfile', 'profile_link');
            if ($_post['user_linked_profiles'] == '_') {
                // user_linked_profiles will be an underscore if ALL extra profiles are removed
                $req = "DELETE FROM {$tbl} WHERE user_id = '{$uid}' AND profile_id != '{$pid}'";
            }
            else {
                $req = "DELETE FROM {$tbl} WHERE user_id = '{$uid}' AND profile_id NOT IN({$pid},{$_post['user_linked_profiles']})";
            }
            jrCore_db_query($req);
        }
    }
    else {
        $_us = $_user;
    }
    // We don't save user_linked_profiles to the DS
    if (isset($_data['user_linked_profiles'])) {
        unset($_data['user_linked_profiles']);
    }

    // Check for changing user_email
    $_sc = array(
        'search'         => array(
            "user_email = {$_post['user_email']}",
            "_item_id != {$uid}"
        ),
        'return_count'   => true,
        'skip_triggers'  => true,
        'privacy_check'  => false,
        'ignore_missing' => true,
        'ignore_pending' => true
    );
    $cnt = jrCore_db_search_items('jrUser', $_sc);
    if ($cnt && $cnt > 0) {
        $_SESSION['show_email_exists_error'] = true;
        jrCore_set_form_notice('error', 96);
        jrCore_form_field_hilight('user_email');
        jrCore_form_result();
    }

    // Check for changing user_name
    $_sc = array(
        'search'         => array(
            "user_name = {$_post['user_name']}",
            "_item_id != {$uid}"
        ),
        'return_count'   => true,
        'skip_triggers'  => true,
        'privacy_check'  => false,
        'ignore_missing' => true,
        'ignore_pending' => true
    );
    $cnt = jrCore_db_search_items('jrUser', $_sc);
    if ($cnt && $cnt > 0) {
        jrCore_set_form_notice('error', 100);
        jrCore_form_field_hilight('user_name');
        jrCore_form_result();
    }

    // See if we got a language
    if (isset($_post['user_language'])) {
        $_data['user_language'] = $_post['user_language'];
    }

    // Save info
    unset($_data['user_passwd1'], $_data['user_passwd2']);
    jrCore_db_update_item('jrUser', $uid, $_data);

    // If we are changing email or password, send an email to the OLD email address
    // outlining that the email address has been changed on the account
    if (!jrUser_is_admin() && (!isset($_conf['jrUser_authenticate']) || $_conf['jrUser_authenticate'] != 'on')) {
        if (isset($_conf['jrUser_change_notice']) && $_conf['jrUser_change_notice'] == 'on') {
            $_rp = array(
                'system_name'     => $_conf['jrCore_system_name'],
                'password_change' => false,
                'email_change'    => false
            );
            // Is the user changing email?
            if ($_data['user_email'] != $_user['user_email']) {
                $_rp['email_change'] = true;
            }
            if (isset($pass)) {
                $_rp['password_change'] = true;
            }
            list($sub, $msg) = jrCore_parse_email_templates('jrUser', 'change', $_rp);
            jrCore_send_email($_user['user_email'], $sub, $msg);
        }
    }

    // Check for Photo upload
    $tempid = $_us['_profile_id'];
    $_image = jrCore_save_media_file('jrUser', 'user_image', $_us['_profile_id'], $uid);
    // If the user does NOT have a profile image yet, set the user image to be the profile image...
    if (!isset($_us['profile_image_size']) && isset($_image) && is_array($_image)) {
        $_us        = array_merge($_us, $_image);
        $user_image = jrCore_get_media_file_path('jrUser', 'user_image', $_us);
        if (is_file($user_image)) {
            $ext = jrCore_file_extension($user_image);
            $nam = "{$_us['_profile_id']}_profile_image";
            if (jrCore_copy_media_file($_us['_profile_id'], $user_image, $nam)) {
                $dir = dirname($user_image);
                jrCore_write_to_file("{$dir}/{$nam}.tmp", "profile_image.{$ext}");
                jrCore_save_media_file('jrProfile', "{$dir}/{$nam}", $_us['_profile_id'], $_us['_profile_id']);
                unlink("{$dir}/{$nam}");
                unlink("{$dir}/{$nam}.tmp");
            }
        }
        $_us['_profile_id'] = $tempid;
    }
    unset($tempid);

    // Remove temp password flag if set
    if (!$rauth && isset($_user['user_temp_password'])) {
        jrCore_db_delete_item_key('jrUser', $_user['_user_id'], 'user_temp_password');
        jrUser_delete_session_key('user_temp_password');
    }

    // Re-sync session
    if (isset($uid) && $uid == $_user['_user_id']) {
        jrUser_reset_cache($uid);
        jrUser_session_sync($uid);
    }

    // Reset caches
    $_ln = jrProfile_get_user_linked_profiles($_us['_user_id']);
    if ($_ln && is_array($_ln)) {
        foreach ($_ln as $pid => $uid) {
            jrProfile_reset_cache($pid);
        }
    }

    // If we are an ADMIN user modifying someone else...
    if (jrUser_is_admin() && isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz') && $_post['user_id'] != $_user['_user_id']) {

        // Send out account updated trigger
        jrCore_trigger_event('jrUser', 'user_updated', $_us, $_data);
        jrUser_set_session_sync_for_user_id($_post['user_id'], 'on');

        jrCore_set_form_notice('success', 'The user account has been successfully updated');
        // If this is an admin from the browser...
        if (isset($_post['from_browser']) && jrCore_checktype($_post['from_browser'], 'url')) {
            jrCore_form_result($_post['from_browser']);
        }
        jrCore_form_delete_session();
        jrCore_form_result();
    }

    // Fall through - we are modifying our own account
    if ($rauth) {
        jrCore_form_delete_session();
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/authenticate");
    }

    // Send out account updated trigger
    jrCore_trigger_event('jrUser', 'user_updated', $_us, $_data);

    // If we changed passwords, log out other sessions
    if (isset($pass)) {
        jrUser_session_remove_all_other_sessions($_user['_user_id'], session_id());
        $cid = jrUser_session_get_login_cookie_name();
        if (isset($_COOKIE[$cid])) {
            // This user had a remember me cookie - set new one
            jrUser_session_set_login_cookie($_user['_user_id']);
        }
    }

    // Remove any forgot password entries
    jrUser_delete_forgot_password_entries($_user['_user_id']);

    jrCore_set_form_notice('success', 43);
    jrCore_form_delete_session();
    jrCore_form_result('referrer');
}

//------------------------------
// authenticate
//------------------------------
function view_jrUser_authenticate($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_set_form_notice('error', 112);
    jrCore_page_banner(42);

    // Form init
    $_tmp = array(
        'submit_value'     => 113,
        'cancel'           => 'referrer',
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // Password
    $_tmp = array(
        'name'     => 'old_password',
        'label'    => 7,
        'help'     => 8,
        'type'     => 'password',
        'required' => true,
        'validate' => 'not_empty'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// authenticate_save
//------------------------------
function view_jrUser_authenticate_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    $_us = jrCore_db_get_item('jrUser', $_user['_user_id'], true, true);
    if (!$_us) {
        jrCore_notice_page('error', 36);
    }

    // Make sure our OLD password is correct
    if (!jrUser_verify_password_hash($_post['old_password'], $_us['user_password'])) {
        jrCore_set_form_notice('error', 26);
        jrCore_location('referrer');
    }

    // Update
    $_dt = array();
    if (isset($_us['user_temp_password'])) {
        $_dt['user_password'] = $_us['user_temp_password'];
    }
    if (isset($_us['user_temp_email'])) {
        $_dt['user_email'] = $_us['user_temp_email'];
    }
    if (isset($_us['user_temp_twofactor_enabled'])) {
        $_dt['user_twofactor_enabled'] = $_us['user_temp_twofactor_enabled'];
    }
    if (jrCore_db_update_item('jrUser', $_user['_user_id'], $_dt)) {

        // Send out account updated trigger
        jrCore_trigger_event('jrUser', 'user_updated', $_us, $_dt);

        // Send notification to OLD address if enabled
        if (isset($_conf['jrUser_change_notice']) && $_conf['jrUser_change_notice'] == 'on') {

            // They are changing email or password
            $_rp = array(
                'system_name'     => $_conf['jrCore_system_name'],
                'password_change' => false,
                'email_change'    => false
            );
            if (isset($_us['user_temp_password'])) {
                $_rp['password_change'] = true;
            }
            if (isset($_us['user_temp_email'])) {
                $_rp['email_change'] = true;
                $_rp['new_email']    = $_us['user_temp_email'];
            }
            list($sub, $msg) = jrCore_parse_email_templates('jrUser', 'change', $_rp);
            jrCore_send_email($_user['user_email'], $sub, $msg);
        }

        // Delete temp keys
        $_dl = array(
            'user_temp_password',
            'user_temp_email',
            'user_temp_twofactor_enabled',
            'user_temp_token'
        );
        jrCore_db_delete_multiple_item_keys('jrUser', $_user['_user_id'], $_dl);

        // Success
        jrUser_reset_cache($_user['_user_id']);
        jrUser_session_sync($_user['_user_id']);

        // Log out other sessions and auto login cookies
        jrUser_session_remove_all_other_sessions($_user['_user_id'], session_id());
        $cid = jrUser_session_get_login_cookie_name();
        if (isset($_COOKIE[$cid])) {
            // This user had a remember me cookie - set new one
            jrUser_session_set_login_cookie($_user['_user_id']);
        }

        // Remove forgot entries
        jrUser_delete_forgot_password_entries($_user['_user_id']);

        jrCore_set_form_notice('success', 43);
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/account");
    }
    jrCore_notice_page('error', 36);
}

//------------------------------
// notifications
//------------------------------
function view_jrUser_notifications($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    $_lang = jrUser_load_lang_strings();

    // See if we are modifying a different account
    if (jrUser_is_admin() && (isset($_post['profile_id']) || isset($_post['user_id']))) {

        // If the user account we are modifying is attached to more than 1 profile,
        // we need to make sure we are using the profile_id as passed in
        if (isset($_post['profile_id']) && jrCore_checktype($_post['profile_id'], 'number_nz') && $_post['profile_id'] != $_user['user_active_profile_id']) {
            // We also must get a valid user_id
            if (!isset($_post['user_id']) || !jrCore_checktype($_post['user_id'], 'number_nz')) {
                jrCore_notice_page('error', 'invalid user_id received - please try again');
            }
            $_pr   = jrCore_db_get_item('jrProfile', $_post['profile_id'], true, true);
            $_us   = jrCore_db_get_item('jrUser', $_post['user_id'], true, true);
            $_qt   = jrProfile_get_quota($_pr['profile_quota_id']);
            $_data = array_merge($_us, $_pr, $_qt);
            unset($_pr);
        }
        elseif ($_post['user_id'] != $_user['_user_id']) {
            $_us   = jrCore_db_get_item('jrUser', $_post['user_id'], true, true);
            $_pr   = jrCore_db_get_item('jrProfile', $_us['_profile_id'], true, true);
            $_qt   = jrProfile_get_quota($_pr['profile_quota_id']);
            $_data = array_merge($_us, $_pr, $_qt);
        }
        else {
            $_data = $_user;
        }

        if (!isset($_data) || !is_array($_data)) {
            jrUser_account_tabs('notifications');
            jrCore_notice_page('error', 'invalid id - please pass in a valid user_id');
        }
        jrUser_account_tabs('notifications', $_data);

        if ($_data['user_name'] != $_user['user_name']) {
            jrCore_set_form_notice('notice', "You are viewing the notification options for the user <strong>{$_data['user_name']}</strong>", false);
        }
        $button = jrCore_page_button('p', $_data['profile_name'], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_data['profile_url']}')");
        $uid    = $_post['user_id'];
        $pid    = $_post['profile_id'];
    }
    else {
        if (jrUser_is_admin()) {
            // If we are dropping in directly from another profile, we need to
            // make sure we are setup on our OWN profile
            $pid = jrUser_get_profile_home_key('_profile_id');
            if ($_user['user_active_profile_id'] != $pid) {
                $_pr = jrCore_db_get_item('jrProfile', $pid, false, true);
                $_qt = jrProfile_get_quota($_pr['profile_quota_id']);

                global $_user;
                $_user = array_merge($_user, $_pr, $_qt);
            }
        }
        jrUser_account_tabs('notifications', $_user);

        $button = jrCore_page_button('p', $_user['profile_name'], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_user['profile_url']}')");
        $uid    = $_user['_user_id'];
        $pid    = $_user['_profile_id'];
        $_data  = $_user;
    }

    // See if all notifications have been disabled
    $disabled = false;
    if (isset($_data['user_notifications_disabled']) && $_data['user_notifications_disabled'] == 'on') {
        jrCore_set_form_notice('error', 95);
        $disabled = true;
    }

    // our page banner
    jrCore_page_banner(64, $button);

    // Form init
    $_tmp = array(
        'submit_value'     => $_lang['jrCore'][72],
        'cancel'           => 'referrer',
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // See if this is a profile admin
    if (isset($_data)) {

        // User ID we are modifying
        $_tmp = array(
            'name'     => 'user_id',
            'type'     => 'hidden',
            'value'    => $uid,
            'validate' => 'number_nz'
        );
        jrCore_form_field_create($_tmp);

        // Profile ID we are modifying
        $_tmp = array(
            'name'     => 'profile_id',
            'type'     => 'hidden',
            'value'    => $pid,
            'validate' => 'number_nz'
        );
        jrCore_form_field_create($_tmp);
    }
    else {
        $_post['user_id']    = $_user['_user_id'];
        $_post['profile_id'] = $_user['_profile_id'];
    }

    // Disable all notifications
    $_tmp = array(
        'name'     => 'user_notifications_disabled',
        'type'     => 'checkbox',
        'label'    => 122,
        'help'     => 120,
        'validate' => 'onoff',
        'value'    => (isset($_data['user_notifications_disabled'])) ? $_data['user_notifications_disabled'] : 'off'
    );
    jrCore_form_field_create($_tmp);

    if (!isset($_data['user_notifications_disabled']) || $_data['user_notifications_disabled'] != 'on') {

        // Get our registered notification events
        $_htm = array();
        $_sel = array();
        $_tmp = jrCore_get_registered_module_features('jrUser', 'notification');

        if ($_tmp && is_array($_tmp)) {

            foreach ($_tmp as $module => $_events) {

                foreach ($_events as $name => $label) {

                    $nid        = "{$module}_{$name}";
                    $_htm[$nid] = '<div id="' . $nid . '" style="display:none">';
                    if (is_array($label)) {

                        // With our $label being an array we have some control over
                        // how this notification option will appear in the User Notifications
                        if (isset($label['function']) && function_exists($label['function'])) {
                            $func  = $label['function'];
                            $_args = array(
                                'module' => $module,
                                'event'  => $name,
                            );
                            if (!$func($_post, $_user, $_conf, $_args)) {
                                continue;
                            }
                        }
                        else {

                            // See if this module defines a custom field to check for showing
                            if (isset($label['field']) && strlen($label['field']) > 0) {
                                if (isset($_data["{$label['field']}"]) && $_data["{$label['field']}"] != 'on') {
                                    continue;
                                }
                            }

                            // Check user group
                            if (!isset($label['group'])) {
                                $label['group'] = 'user';
                            }
                            switch ($label['group']) {
                                case 'master':
                                    if (!jrUser_is_master()) {
                                        continue 2;
                                    }
                                    break;
                                case 'admin':
                                    if (!jrUser_is_admin()) {
                                        continue 2;
                                    }
                                    break;
                                case 'power':
                                    if (!jrUser_is_power_user()) {
                                        continue 2;
                                    }
                                    break;
                                case 'multi':
                                    if (!jrUser_is_multi_user()) {
                                        continue 2;
                                    }
                                    break;
                                case 'all':
                                    break;
                                default:
                                    if (!isset($label['field']) && isset($_data["quota_{$module}_allowed"]) && $_data["quota_{$module}_allowed"] != 'on') {
                                        continue 2;
                                    }
                                    break;
                            }
                        }
                    }
                    else {
                        // Make sure this user has Quota access
                        if (isset($_data["quota_{$module}_allowed"]) && $_data["quota_{$module}_allowed"] != 'on') {
                            continue;
                        }
                    }

                    $_opts = array(
                        'off'   => $_lang['jrUser'][65],
                        'email' => $_lang['jrUser'][66],
                        'note'  => $_lang['jrUser'][67]
                    );
                    if (!jrCore_module_is_active('jrPrivateNote') || (isset($_data["quota_jrPrivateNote_allowed"]) && $_data["quota_jrPrivateNote_allowed"] != 'on') || $name == 'note_received' || (is_array($label) && isset($label['email_only']) && $label['email_only'] === true)) {
                        unset($_opts['note']);
                    }

                    // If we have disabled all notifications, show that
                    if ($disabled) {
                        $_data["user_{$module}_{$name}_notifications"] = 'off';
                    }

                    $_tmp = array(
                        'name'          => "event_{$module}_{$name}",
                        'type'          => 'radio',
                        'options'       => $_opts,
                        'off_checked'   => '',
                        'email_checked' => '',
                        'note_checked'  => '',
                        '_options'      => $label
                    );
                    if (isset($_data["user_{$module}_{$name}_notifications"])) {
                        $opt                    = $_data["user_{$module}_{$name}_notifications"];
                        $_tmp["{$opt}_checked"] = 'checked="checked"';
                    }
                    else {
                        $_tmp['email_checked'] = 'checked="checked"';
                    }

                    if (is_array($label)) {
                        $_tmp['label'] = ((isset($_lang[$module]["{$label['label']}"])) ? $_lang[$module]["{$label['label']}"] : $label['label']);
                        if (!empty($label['help'])) {
                            $_tmp['help'] = ((isset($_lang[$module]["{$label['help']}"])) ? $_lang[$module]["{$label['help']}"] : $label['help']);
                        }
                    }
                    else {
                        $_tmp['label'] = ((isset($_lang[$module][$label])) ? $_lang[$module][$label] : $label);
                    }

                    if (!isset($_sel[$nid])) {
                        $_sel[$nid] = ucwords($_tmp['label']);
                    }

                    $_htm[$nid] .= jrCore_parse_template('notification_option.tpl', $_tmp, 'jrUser') . '</div>';

                }
            }

            $html = '';
            natcasesort($_sel);
            $active = false;
            $select = '<select id="user-notification-select" class="form_select" onchange="jrUser_notification_option($(this).val()); return false">';
            foreach ($_sel as $nid => $title) {
                if (!$active) {
                    $select .= '<option value="' . $nid . '" selected> ' . $title . "</option>\n";
                    $active = true;
                    $html   .= str_replace('style="display:none"', 'class="no-act"', $_htm[$nid]);
                }
                else {
                    $select .= '<option value="' . $nid . '"> ' . $title . "</option>\n";
                    $html   .= $_htm[$nid];
                }
            }
            $select .= '</select>';
            jrCore_page_section_header($select);
            jrCore_page_custom($html, '');
        }

    }
    jrCore_page_display();
}

//------------------------------
// notifications_save
//------------------------------
function view_jrUser_notifications_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_form_validate($_post);

    // See if this is an admin modifying this user account
    $uid = $_user['_user_id'];
    if (jrUser_is_admin() && isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz')) {
        $uid = (int) $_post['user_id'];
    }
    $_up = array();
    foreach ($_post as $k => $v) {
        if (strpos($k, 'event_') === 0) {
            $nam       = 'user_' . substr($k, 6) . '_notifications';
            $_up[$nam] = $v;
            if (isset($uid) && $uid == $_user['_user_id']) {
                $_SESSION[$nam] = $v;
            }
        }
    }

    if (isset($_post['user_notifications_disabled']) && $_post['user_notifications_disabled'] == 'on') {
        $_up['user_notifications_disabled'] = 'on';
    }
    else {
        jrCore_db_delete_item_key('jrUser', $uid, 'user_notifications_disabled');
        jrUser_delete_session_key('user_notifications_disabled');
    }
    if (count($_up) > 0) {
        jrCore_db_update_item('jrUser', $uid, $_up);
    }

    // Re-sync session
    if ($uid == $_user['_user_id']) {
        jrUser_reset_cache($uid);
        jrUser_session_sync($uid);
    }

    jrCore_form_delete_session();
    jrCore_set_form_notice('success', 43);
    jrCore_form_result('referrer');
}

//------------------------------
// online
//------------------------------
function view_jrUser_online($_post, $_user, $_conf)
{
    if (!jrUser_is_admin()) {
        jrUser_not_authorized();
    }
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrUser');

    // our page banner
    $btn = null;
    if (!isset($_conf['jrUser_bot_sessions']) || $_conf['jrUser_bot_sessions'] == 'on') {
        $url = jrCore_strip_url_params(jrCore_get_current_url(), array('show_bots'));
        if (isset($_post['show_bots'])) {
            $btn = jrCore_page_button('sb', 'Hide Bots', "jrCore_window_location('{$url}')");
        }
        else {
            $btn = jrCore_page_button('sb', 'Show Bots', "jrCore_window_location('{$url}/show_bots=1')");
        }
    }

    jrCore_page_banner('Users Online', $btn);
    jrCore_get_form_notice();

    jrUser_online_users($_post, $_user, $_conf);

    jrCore_page_display();
}

//------------------------------
// online
//------------------------------
function view_jrUser_session_remove_save($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();

    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid Session User ID');
        jrCore_form_result('referrer');
    }
    if ($_post['_1'] == $_user['_user_id']) {
        jrCore_set_form_notice('error', 'You can\'t remove yourself!');
        jrCore_form_result('referrer');
    }
    jrUser_session_remove($_post['_1']);

    // Remove remember me cookie
    $uid = (int) $_post['_1'];
    $tbl = jrCore_db_table_name('jrUser', 'cookie');
    $req = "SELECT cookie_id FROM {$tbl} WHERE cookie_user_id = {$uid}";
    $_rt = jrCore_db_query($req, 'cookie_id');
    if ($_rt && is_array($_rt)) {
        $req = "DELETE FROM {$tbl} WHERE cookie_id IN(" . implode(',', array_keys($_rt)) . ')';
        jrCore_db_query($req);
    }

    jrCore_form_result('referrer');
}

//------------------------------
// unsubscribe
//------------------------------
function view_jrUser_unsubscribe($_post, $_user, $_conf)
{
    $_ln = jrUser_load_lang_strings();

    // Are we getting JUST an email address to unsubscribe?
    if (!isset($_post['_1'])) {
        jrCore_notice_page('error', 'Invalid unique email ID<br><br>Please make sure you are entering the full URL from the unsubscribe link (1)', $_conf['jrCore_base_url'], $_ln['jrUser'][113], false);
    }
    elseif (!jrCore_checktype($_post['_1'], 'email') && !jrCore_checktype($_post['_1'], 'md5')) {
        jrCore_notice_page('error', 'Invalid unique email ID<br><br>Please make sure you are entering the full URL from the unsubscribe link (2)', $_conf['jrCore_base_url'], $_ln['jrUser'][113], false);
    }

    $_rt = false;
    if (jrCore_checktype($_post['_1'], 'email')) {

        if (!isset($_post['_2']) || !jrCore_checktype($_post['_2'], 'md5')) {
            jrCore_notice_page('error', 'Invalid unique email hash<br><br>Please make sure you are entering the full URL from the unsubscribe link (1)', $_conf['jrCore_base_url'], $_ln['jrUser'][113], false);
        }

        // Check incoming hash
        if ($_post['_2'] != md5($_conf['jrCore_unique_string'] . $_post['_1'])) {
            jrCore_notice_page('error', 'Invalid unique email hash<br><br>Please make sure you are entering the full URL from the unsubscribe link (2)', $_conf['jrCore_base_url'], $_ln['jrUser'][113], false);
        }

        // We're good - this request is valid

        // This unsubscribe request has come in for an email - let's see if there is account
        $_rt = jrCore_db_get_item_by_key('jrUser', 'user_email', $_post['_1'], true);
        if (!$_rt || !is_array($_rt)) {

            // No account - block sending to this email address any longer
            if (!jrUser_is_logged_in()) {
                jrUser_suppress_email_address($_post['_1']);
                jrCore_notice_page('success', $_ln['jrUser'][121], $_conf['jrCore_base_url'], $_ln['jrUser'][113]);
            }

            // Fall through - redirect to notifications
            jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/notifications/profile_id=" . jrUser_get_profile_home_key('_profile_id') . "/user_id={$_user['_user_id']}");

        }
    }

    // Fall through - this user has an ACCOUNT on the system
    if (!$_rt) {
        $_rt = jrCore_db_get_item_by_key('jrUser', 'user_validate', $_post['_1'], true);
    }
    if (!$_rt || !is_array($_rt)) {
        jrCore_notice_page('error', 'Invalid unique subscriber ID<br><br>Please make sure you are entering the full URL from the unsubscribe link (2)', $_conf['jrCore_base_url'], $_ln['jrUser'][113], false);
    }

    if (jrUser_is_logged_in()) {
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/notifications/profile_id={$_rt['_profile_id']}/user_id={$_rt['_user_id']}");
    }

    jrCore_page_banner(118, $_rt['user_email']);
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'cancel' => $_conf['jrCore_base_url']
    );
    jrCore_form_create($_tmp);

    jrCore_set_form_notice('notice', $_ln['jrUser'][121]);

    $_tmp = array(
        'name'     => 'block_notifications',
        'label'    => 119,
        'help'     => 120,
        'type'     => 'checkbox',
        'value'    => 'off',
        'validate' => 'onoff',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'  => 'user_validate',
        'type'  => 'hidden',
        'value' => $_post['_1']
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// unsubscribe_save
//------------------------------
function view_jrUser_unsubscribe_save($_post, $_user, $_conf)
{
    jrCore_form_validate($_post);

    if (!isset($_post['block_notifications']) || $_post['block_notifications'] != 'on') {
        jrCore_set_form_notice('error', 'Please confirm by checking the checkbox and submitting again. Thanks.');
        jrCore_form_result();
    }

    $_ln = jrUser_load_lang_strings();
    if (!isset($_post['user_validate']) || !jrCore_checktype($_post['user_validate'], 'md5')) {
        jrCore_set_form_notice('error', 'Invalid unique subscriber ID<br><br>Please make sure you are entering the full URL from the unsubscribe link (3)', false);
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item_by_key('jrUser', 'user_validate', $_post['user_validate'], true);
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Invalid unique subscriber ID<br><br>Please make sure you are entering the full URL from the unsubscribe link (4)', false);
        jrCore_form_result();
    }

    if (!isset($_rt['user_unsubscribe']) || !jrCore_checktype($_rt['user_unsubscribe'], 'md5')) {
        $md5  = md5(microtime() . mt_rand(0, 999999));
        $_tmp = array(
            'user_unsubscribe' => $md5
        );
        jrCore_db_update_item('jrUser', $_rt['_item_id'], $_tmp);
    }
    else {
        $md5 = $_rt['user_unsubscribe'];
    }

    $murl = jrCore_get_module_url('jrUser');
    // send out a confirm unsubscribe email link.
    $_rp = array(
        'opt_out_link' => $_conf['jrCore_base_url'] . '/' . $murl . '/unsubscribe_confirm/' . $md5
    );
    list($sub, $msg) = jrCore_parse_email_templates('jrUser', 'unsubscribe_confirm', $_rp);
    jrCore_send_email($_rt['user_email'], $sub, $msg);

    // Delete our cache
    jrCore_delete_all_cache_entries('jrUser', $_rt['_item_id']);
    jrCore_set_form_notice('success', "{$_ln['jrUser'][123]} <b>{$_rt['user_email']}</b><br> {$_ln['jrUser'][124]}", false);
    jrCore_form_result();
}

//------------------------------
// Un-subscribe confirm
//------------------------------
function view_jrUser_unsubscribe_confirm($_post, $_user, $_conf)
{
    $_ln = jrUser_load_lang_strings();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'md5')) {
        jrCore_notice_page('error', 'Invalid unique subscriber ID!<br><br>Please make sure you are entering the full URL from the unsubscribe link (5)', $_conf['jrCore_base_url'], $_ln['jrUser'][113], false);
    }
    $_rt = jrCore_db_get_item_by_key('jrUser', 'user_unsubscribe', $_post['_1'], true);
    if (!$_rt || !is_array($_rt)) {
        jrCore_notice_page('error', 'Invalid unique subscriber ID!<br><br>Please make sure you are entering the full URL from the unsubscribe link (6)', $_conf['jrCore_base_url'], $_ln['jrUser'][113], false);
    }

    if (jrUser_is_logged_in()) {
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/notifications/profile_id={$_rt['_profile_id']}/user_id={$_rt['_user_id']}");
    }

    $_tmp = array(
        'user_notifications_disabled' => 'on',
        'user_unsubscribe'            => ''
    );
    jrCore_db_update_item('jrUser', $_rt['_item_id'], $_tmp);
    jrCore_logger('MIN', "{$_rt['user_name']} ({$_rt['user_email']}) has confirmed unsubscribe from all notifications", null, false);

    // Delete our cache
    jrCore_delete_all_cache_entries('jrUser', $_rt['_item_id']);
    jrCore_notice_page('success', $_ln['jrUser'][94]);
}

//------------------------------
// Re-subscribe
//------------------------------
function view_jrUser_resubscribe($_post, $_user, $_conf)
{
    $_ln = jrUser_load_lang_strings();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'md5')) {
        jrCore_notice_page('error', 'Invalid unique subscriber ID!<br><br>Please make sure you are entering the full URL from the unsubscribe link (1)', $_conf['jrCore_base_url'], $_ln['jrUser'][113], false);
    }
    $_rt = jrCore_db_get_item_by_key('jrUser', 'user_validate', $_post['_1'], true);
    if (!$_rt || !is_array($_rt)) {
        jrCore_notice_page('error', 'Invalid unique subscriber ID!<br><br>Please make sure you are entering the full URL from the unsubscribe link (2)', $_conf['jrCore_base_url'], $_ln['jrUser'][113], false);
    }
    // delete special "user_notifications_disabled" flag
    jrCore_db_delete_item_key('jrUser', $_rt['_item_id'], 'user_notifications_disabled');
    jrUser_delete_session_key('user_notifications_disabled');
    jrCore_logger('MIN', "{$_rt['user_name']} ({$_rt['user_email']}) has re-subscribed to notifications", null, false);

    // Delete our cache
    jrCore_delete_all_cache_entries('jrUser', $_rt['_item_id']);
    jrCore_notice_page('success', $_ln['jrUser'][107], $_conf['jrCore_base_url'], $_ln['jrUser'][113]);
}

//------------------------------
// delete_save
//------------------------------
function view_jrUser_delete_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();

    // Make sure user is allowed to create profiles....
    if (!jrUser_is_admin()) {
        jrUser_not_authorized();
    }
    // Make sure we get a valid ID
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'invalid user id - please try again');
    }
    $_us = jrCore_db_get_item('jrUser', $_post['id'], true);
    if (!$_us || !is_array($_us)) {
        jrCore_notice_page('error', 'invalid user id - no data for user found');
    }
    if ($_us['_user_id'] == $_user['_user_id']) {
        jrCore_notice_page('error', 'you cannot delete your own account - contact the master admin');
    }
    // Cannot delete admin and master users..
    if (!jrUser_is_master()) {
        if ($_us['user_group'] == 'admin' || $_us['user_group'] == 'master') {
            jrCore_notice_page('error', 'only a Master Admin can delete admin or master accounts');
        }
    }
    jrCore_trigger_event('jrUser', 'delete_user', $_us);
    $uid = $_post['id'];

    // Delete from profile link
    if (isset($_conf['jrCore_recycle_bin']) && $_conf['jrCore_recycle_bin'] == 'off') {
        $tbl = jrCore_db_table_name('jrProfile', 'profile_link');
        $req = "DELETE FROM {$tbl} WHERE user_id = '{$uid}'";
        jrCore_db_query($req);
    }

    // Delete account
    jrCore_db_delete_item('jrUser', $uid);

    // Delete session
    jrUser_session_remove($uid);

    // Delete Caches
    jrCore_delete_all_cache_entries(null, $uid);

    // Redirect
    jrCore_set_form_notice('success', 'The User Account was successfully deleted.');
    if (isset($_SESSION['user_dashboard_referral_url'])) {
        $url = $_SESSION['user_dashboard_referral_url'];
        unset($_SESSION['user_dashboard_referral_url']);
        jrCore_location($url);
    }
    jrCore_location('referrer');
}

//------------------------------
// block_save
//------------------------------
function view_jrUser_block_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    if (!jrUser_is_admin()) {
        jrUser_not_authorized();
    }

    // Make sure we get a valid ID
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'invalid user id - please try again');
    }
    $_us = jrCore_db_get_item('jrUser', $_post['id'], true);
    if (!$_us || !is_array($_us)) {
        jrCore_notice_page('error', 'invalid user id - no data for user found');
    }
    if ($_us['_user_id'] == $_user['_user_id']) {
        jrCore_notice_page('error', 'you cannot block your own account - contact the master admin');
    }
    // Cannot delete admin and master users..
    if (!jrUser_is_master()) {
        if ($_us['user_group'] == 'admin' || $_us['user_group'] == 'master') {
            jrCore_notice_page('error', 'only a Master Admin can block admin or master accounts');
        }
    }
    $uid = (int) $_post['id'];
    if (!isset($_post['mode']) || ($_post['mode'] != 'b' && $_post['mode'] != 'u')) {
        jrCore_notice_page('error', 'invalid block mode - please try again');
    }

    // Block a User
    if ($_post['mode'] == 'b') {

        // When blocking a user we set BOTH user and profile inactive and remove any sessions
        jrCore_db_update_item('jrUser', $uid, array('user_active' => 0, 'user_blocked' => 1));

        // Delete existing session and login cookie
        jrUser_session_remove($uid);

        $tbl = jrCore_db_table_name('jrUser', 'cookie');
        $req = "DELETE FROM {$tbl} WHERE cookie_user_id = '{$uid}'";
        jrCore_db_query($req);

        // Next - we need to get ALL profiles this user is linked to abd block the profiles
        $_pr = jrProfile_get_user_linked_profiles($uid);
        if ($_pr && is_array($_pr)) {
            foreach ($_pr as $pid => $user_id) {
                jrCore_db_update_item('jrProfile', $pid, array('profile_active' => 0));
                jrProfile_reset_cache($pid);
            }
        }

    }
    else {

        // UN-blocking a user
        jrCore_db_update_item('jrUser', $uid, array('user_active' => 1));
        jrCore_db_delete_item_key('jrUser', $uid, 'user_blocked');

        // Next - we need to get ALL profiles this user is linked to abd block the profiles
        $_pr = jrProfile_get_user_linked_profiles($uid);
        if ($_pr && is_array($_pr)) {
            foreach ($_pr as $pid => $user_id) {
                jrCore_db_update_item('jrProfile', $pid, array('profile_active' => 1));
                jrProfile_reset_cache($pid);
            }
        }

    }

    jrCore_trigger_event('jrUser', 'block_user', $_post);
    jrCore_location('referrer');
}

//------------------------------
// whois
//------------------------------
function view_jrUser_whois($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_page_set_meta_header_only();

    $button = jrCore_page_button('close', 'close', 'self.close();');
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'ip_address')) {
        jrCore_page_banner('whois lookup', $button);
        jrCore_notice_page('error', 'Invalid IP Address');
    }

    jrCore_page_banner("whois lookup: {$_post['_1']}", $button);
    $_rt = jrUser_whois_lookup($_post['_1']);
    if (!$_rt || !is_array($_rt)) {
        jrCore_notice_page('error', 'Unable to run whois for IP Address');
    }

    // Is the Geo Module installed and working?
    if (jrCore_module_is_active('jrGeo')) {
        $_ip = jrGeo_location($_post['_1']);
        if ($_ip && is_array($_ip) && isset($_ip['latitude'])) {

            $dat             = array();
            $dat[1]['title'] = 'Geo Key';
            $dat[1]['width'] = '20%';
            $dat[2]['title'] = 'Geo Value';
            $dat[2]['width'] = '80%';
            jrCore_page_table_header($dat);

            foreach ($_ip as $k => $v) {
                if (strlen(trim($v)) > 0) {
                    $dat             = array();
                    $dat[1]['title'] = $k;
                    $dat[2]['title'] = $v;
                    jrCore_page_table_row($dat);
                }
            }
        }
    }

    if (count($_rt) > 0) {
        $dat             = array();
        $dat[1]['title'] = 'Whois Key';
        $dat[1]['width'] = '20%';
        $dat[2]['title'] = 'Whois Value';
        $dat[2]['width'] = '80%';
        jrCore_page_table_header($dat, null, true);

        foreach ($_rt as $k => $v) {
            $dat             = array();
            $dat[1]['title'] = $k;
            $dat[2]['title'] = $v;
            jrCore_page_table_row($dat);
        }
    }

    jrCore_page_table_footer();
    jrCore_page_close_button();
    jrCore_page_display();
}

//------------------------------
// export_language
//------------------------------
function view_jrUser_export_language($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrUser');

    $iframe = '';
    if (isset($_post['_2']) && strlen($_post['_2']) === 12) {
        $iframe = "<iframe src=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/export_language_download/{$_post['_1']}/{$_post['_2']}\" style=\"display:none;\"></iframe>";
    }
    $button = jrCore_page_button('uncheck', 'unselect all', "$('#jrUser_export_language').find('.form_checkbox').prop('checked', false)");
    jrCore_page_banner('export language strings', $iframe . $button);
    jrCore_get_form_notice();

    $tbl = jrCore_db_table_name('jrUser', 'language');
    $req = "SELECT lang_module FROM {$tbl} GROUP BY lang_module";
    $_ln = jrCore_db_query($req, 'lang_module', false, 'lang_module');

    // Get modules and skins
    $_mds = array();
    foreach ($_mods as $dir => $_inf) {
        if (isset($_ln[$dir])) {
            $_mds[$dir] = $_inf['module_name'];
        }
    }
    $_sks = jrCore_get_skins();
    $_skn = array();
    foreach ($_sks as $dir => $_inf) {
        $_tmp = jrCore_skin_meta_data($dir);
        if ($_tmp && isset($_ln[$dir])) {
            $_skn[$dir] = $_tmp['title'];
        }
    }

    // Form init
    $_tmp = array(
        'submit_value'     => 'export language strings',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // Export Language
    $_opt = jrUser_get_languages();
    $_tmp = array(
        'name'     => 'export_lang',
        'label'    => 'language to export',
        'help'     => 'Select the existing User Language you would like to export',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 'en-US',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Modules
    natcasesort($_mds);
    $_tmp = array(
        'name'     => 'export_modules',
        'label'    => 'Selected for Export',
        'help'     => 'Select the modules you would like to export language strings for',
        'type'     => 'optionlist',
        'options'  => $_mds,
        'default'  => implode(',', array_keys($_mds)),
        'layout'   => 'columns',
        'columns'  => 3,
        'required' => true,
        'section'  => 'module language strings'
    );
    jrCore_form_field_create($_tmp);

    // Skins
    natcasesort($_skn);
    $_tmp = array(
        'name'     => 'export_skins',
        'label'    => 'Selected for Export',
        'help'     => 'Select the skins you would like to export language strings for',
        'type'     => 'optionlist',
        'options'  => $_skn,
        'default'  => implode(',', array_keys($_skn)),
        'layout'   => 'columns',
        'columns'  => 3,
        'required' => true,
        'section'  => 'skin language strings'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// export_language_save
//------------------------------
function view_jrUser_export_language_save($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_form_validate($_post);

    $_td = array();
    if (isset($_post['export_modules']) && strlen($_post['export_modules']) > 0) {
        $_td = explode(',', $_post['export_modules']);
    }
    if (isset($_post['export_skins']) && strlen($_post['export_skins']) > 0) {
        $_td = array_merge($_td, explode(',', $_post['export_skins']));
    }
    $_td = array_flip($_td);

    $_tm = jrUser_load_lang_strings($_post['export_lang'], false, false);
    if ($_tm && is_array($_tm)) {
        $_sk = jrCore_get_skins();
        $tmp = jrCore_create_unique_string(12);
        $dir = jrCore_get_module_cache_dir('jrUser');
        $_fl = array();
        ksort($_tm);
        foreach ($_tm as $mod => $_strings) {
            if (isset($_td[$mod]) && (isset($_mods[$mod]) || isset($_sk[$mod]))) {
                $_fl[] = '';
                foreach ($_strings as $key => $val) {
                    // NOTE: custom lang strings have an id > 10,000
                    if (strlen($val) > 0 && $key < 10000) {
                        $_fl[] = "{$mod}:{$_tm['_settings']['code']}:{$_tm['_settings']['charset']}:{$_tm['_settings']['direction']}:{$key}:{$val}";
                    }
                }
            }
        }
        $dat = implode("\n", $_fl);
        jrCore_form_delete_session();
        jrCore_write_to_file("{$dir}/{$_post['export_lang']}_{$tmp}.lang", $dat);
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/export_language/{$_post['export_lang']}/{$tmp}");
    }
    else {
        jrCore_notice_page('error', "No language strings found for {$_post['export_lang']}");
    }
}

//------------------------------
// export_language_download
//------------------------------
function view_jrUser_export_language_download($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        jrCore_notice('error', 'invalid language');
    }
    if (!isset($_post['_2']) || strlen($_post['_2']) !== 12) {
        jrCore_notice('error', 'invalid unique download id');
    }
    $dir = jrCore_get_module_cache_dir('jrUser');
    if (is_file("{$dir}/{$_post['_1']}_{$_post['_2']}.lang")) {
        $tmp = file_get_contents("{$dir}/{$_post['_1']}_{$_post['_2']}.lang");
        $now = strftime('%Y%m%d%H%M');
        $nam = jrCore_url_string($_conf['jrCore_system_name']);
        header('Content-Length: ' . strlen($tmp));
        header("Content-type: application/binary");
        header("Content-Disposition: attachment; filename=\"{$nam}_{$now}_{$_post['_1']}.lang\"");
        echo $tmp;
    }
}

//------------------------------
// import_language
//------------------------------
function view_jrUser_import_language($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrUser');
    jrCore_page_banner('import language strings');
    jrCore_set_form_notice('success', 'You will have the option to select which module and skin language strings to import on the next page');
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value'     => 'import language',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // Lang File
    $_tmp = array(
        'name'     => 'lang_file',
        'label'    => 'Language File',
        'help'     => 'Select the Language File you would like to import',
        'text'     => 'Select .lang File',
        'type'     => 'file',
        'allowed'  => 'lang',
        'required' => true,
        'max'      => jrCore_get_max_allowed_upload(false)
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// import_language_save
//------------------------------
function view_jrUser_import_language_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    $_dat = jrCore_get_uploaded_media_files('jrUser', 'lang_file');
    if ($_dat && is_array($_dat) && isset($_dat[0]) && is_file($_dat[0])) {

        // Validate file
        $_tm = explode("\n", file_get_contents($_dat[0]));
        if (!$_tm || !is_array($_tm)) {
            jrCore_set_form_notice('error', 'Invalid language file - please upload a valid language file');
            jrCore_form_result();
        }

        // Save file off for options
        $dir = jrCore_get_module_cache_dir('jrUser');
        $tmp = jrCore_create_unique_string(12);
        $fil = "{$dir}/{$tmp}.lang";
        if (!rename($_dat[0], $fil)) {
            if (!copy($_dat[0], $fil)) {
                jrCore_set_form_notice('error', 'Unable to save uploaded language file to cache directory - check permissions');
                jrCore_form_result();
            }
            unlink($_dat[0]);
        }
        jrCore_form_delete_session();
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/import_language_options/{$tmp}");

    }
    else {
        jrCore_notice_page('error', "No language strings found for {$_post['export_lang']}");
    }
}

//------------------------------
// import_language_options
//------------------------------
function view_jrUser_import_language_options($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        jrCore_set_form_notice('error', "invalid unique file id - unable to import");
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/import_language");
    }

    // Get file
    $dir = jrCore_get_module_cache_dir('jrUser');
    $fil = "{$dir}/{$_post['_1']}.lang";
    if (!is_file($fil)) {
        jrCore_set_form_notice('error', "language file not found - unable to import");
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/import_language");
    }

    $_tm = explode("\n", file_get_contents($fil));
    if (!$_tm || !is_array($_tm)) {
        jrCore_set_form_notice('error', "invalid language file - unable to import");
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/import_language");
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrUser');

    $button = jrCore_page_button('uncheck', 'unselect all', "$('#jrUser_import_language_options').find('.form_checkbox').prop('checked', false)");
    jrCore_page_banner('import language options', $button);
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value'     => 'import selected options',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/import_language",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // Go through file and get installed modules
    $_sks = jrCore_get_skins();
    $_mds = array();
    $_skn = array();
    foreach ($_tm as $line) {
        $mod = substr($line, 0, strpos($line, ':'));
        if (isset($_mods[$mod]) && !isset($_mds[$mod])) {
            $_mds[$mod] = $_mods[$mod]['module_name'];
        }
        elseif (isset($_sks[$mod]) && !isset($_skn[$mod])) {
            $_tmp       = jrCore_skin_meta_data($mod);
            $_skn[$mod] = $_tmp['name'];
        }
    }

    $_tmp = array(
        'name'  => 'import_id',
        'type'  => 'hidden',
        'value' => $_post['_1']
    );
    jrCore_form_field_create($_tmp);

    // Module Options
    natcasesort($_mds);
    $_tmp = array(
        'name'     => 'import_modules',
        'label'    => 'Selected for Import',
        'help'     => 'Select the modules you would like to import language strings for',
        'type'     => 'optionlist',
        'options'  => $_mds,
        'default'  => implode(',', array_keys($_mds)),
        'layout'   => 'columns',
        'columns'  => 3,
        'required' => true,
        'section'  => 'module language strings'
    );
    jrCore_form_field_create($_tmp);

    // Skins
    if (count($_skn) > 0) {
        natcasesort($_skn);
        $_tmp = array(
            'name'     => 'import_skins',
            'label'    => 'Selected for Import',
            'help'     => 'Select the skins you would like to import language strings for',
            'type'     => 'optionlist',
            'options'  => $_skn,
            'default'  => implode(',', array_keys($_skn)),
            'layout'   => 'columns',
            'columns'  => 3,
            'required' => true,
            'section'  => 'skin language strings'
        );
        jrCore_form_field_create($_tmp);
    }
    jrCore_page_display();

}

//------------------------------
// import_language_options_save
//------------------------------
function view_jrUser_import_language_options_save($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_form_validate($_post);
    if (!isset($_post['import_id']) || strlen($_post['import_id']) !== 12) {
        jrCore_set_form_notice('error', "invalid unique file id - unable to import");
        jrCore_form_result();
    }
    // Get file
    $dir = jrCore_get_module_cache_dir('jrUser');
    $fil = "{$dir}/{$_post['import_id']}.lang";
    if (!is_file($fil)) {
        jrCore_set_form_notice('error', "language file not found - unable to import");
        jrCore_form_result();
    }

    $_tm = explode("\n", file_get_contents($fil));
    if (!$_tm || !is_array($_tm)) {
        jrCore_set_form_notice('error', "invalid language file - unable to import");
        jrCore_form_result();
    }

    $_td = array();
    if (isset($_post['import_modules']) && strlen($_post['import_modules']) > 0) {
        $_td = explode(',', $_post['import_modules']);
    }
    if (isset($_post['import_skins']) && strlen($_post['import_skins']) > 0) {
        $_td = array_merge($_td, explode(',', $_post['import_skins']));
    }
    $_td = array_flip($_td);

    $_skn = jrCore_get_skins();
    $done = 0;
    $_ex  = false;
    $tbl  = jrCore_db_table_name('jrUser', 'language');
    foreach ($_tm as $line) {
        list($mod, $lng, $chr, $dir, $idx, $txt) = explode(':', $line, 6);
        $idx = (int) $idx;
        if ($idx < 10000 && isset($_td[$mod]) && (isset($_mods[$mod]) || isset($_skn[$mod]))) {

            // Load up existing strings
            if (!$_ex) {
                $req = "SELECT * FROM {$tbl} WHERE lang_module = '{$mod}' AND lang_code = '{$lng}'";
                $_ex = jrCore_db_query($req, 'lang_key');
                if (!$_ex || !is_array($_ex)) {
                    $_ex = array();
                }
            }
            $txt = jrCore_db_escape(trim($txt));
            if (isset($_ex[$idx])) {
                $req = "UPDATE {$tbl} SET lang_text = '{$txt}' WHERE lang_id = '{$_ex[$idx]['lang_id']}'";
            }
            else {
                $lng = trim($lng);
                $chr = trim($chr);
                $dir = trim($dir);
                $idx = (int) $idx;
                $req = "INSERT INTO {$tbl} (lang_module, lang_code, lang_charset, lang_ltr, lang_key, lang_text, lang_default) VALUES ('{$mod}', '{$lng}', '{$chr}', '{$dir}', '{$idx}', '{$txt}', '{$txt}')";
            }
            jrCore_db_query($req);
            $done++;
        }
    }

    unlink($fil);
    jrCore_form_delete_session();
    jrCore_delete_all_cache_entries();
    jrCore_set_form_notice('success', jrCore_number_format($done) . " language strings were successfully imported");
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/import_language");

}
