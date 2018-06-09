<?php
/**
 * Jamroom Proxima Core module
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
 * @copyright 2014 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrProximaCore_app_browser
 */
function view_jrProximaCore_app_browser($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrProximaCore', 'app_browser');
    jrCore_page_banner('app browser');

    $tbl = jrCore_db_table_name('jrProximaCore', 'app');
    $req = "SELECT * FROM {$tbl} ORDER BY app_name ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');

    if (!$_rt || !is_array($_rt)) {
        jrCore_page_note('<strong>Welcome to Proxima!</strong><br><br>To get started let\'s create a new <strong>application</strong> - every request to Proxima belongs to an application.<br>Each application will have a unique set of <strong>access keys</strong> that are used to validate requests into the system.<br><br>You can have as many different apps configured as you would like - Proxima will keep everything separated.<br><br>Enter an <strong>app name</strong> into the highlighted field below.');
        jrCore_form_field_hilight('app_name');
    }

    $dat             = array();
    $dat[1]['title'] = 'app name';
    $dat[1]['width'] = '45%';
    $dat[2]['title'] = 'client key';
    $dat[2]['width'] = '20%';
    $dat[3]['title'] = 'master key';
    $dat[3]['width'] = '20%';
    $dat[4]['title'] = 'active';
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = 'modify';
    $dat[5]['width'] = '5%';
    $dat[6]['title'] = 'delete';
    $dat[6]['width'] = '5%';
    jrCore_page_table_header($dat);

    $pass = jrCore_get_option_image('pass');
    $fail = jrCore_get_option_image('fail');

    if (is_array($_rt)) {
        foreach ($_rt as $k => $_app) {
            $dat             = array();
            $dat[1]['title'] = '<h3>' . $_app['app_name'] . '</h3>';
            $dat[2]['title'] = $_app['app_client_key'];
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $_app['app_master_key'];
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = ($_app['app_active'] == 'on') ? $pass : $fail;
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrCore_page_button("a{$k}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/app_update/id={$_app['app_id']}')");
            $dat[6]['title'] = jrCore_page_button("d{$k}", 'delete', "if(confirm('Are you sure you want to delete this app? This will delete ALL data for this app, and cannot be undone!')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/app_delete_save/id={$_app['app_id']}') }");
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>No applications have been created yet - create a new one below.</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    jrCore_page_section_header('create a new application');
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value'     => 'create new app',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // APP Name
    $_tmp = array(
        'name'     => 'app_name',
        'label'    => 'app name',
        'help'     => 'Enter a name for this new app.  This can be changed at any time without affecting existing data stored for the application.',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

/**
 * jrProximaCore_app_save
 */
function view_jrProximaCore_app_browser_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Make sure this app does not already exist
    $app = jrCore_db_escape($_post['app_name']);
    $tbl = jrCore_db_table_name('jrProximaCore', 'app');
    $req = "SELECT app_id FROM {$tbl} WHERE app_name = '{$app}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (is_array($_rt)) {
        jrCore_set_form_notice('error', 'There is already an app using that name - please enter a different name');
        jrCore_form_result();
    }

    $cky = jrCore_db_escape(jrProximaCore_get_code(16));
    $mky = jrCore_db_escape(jrProximaCore_get_code(16));
    $req = "INSERT INTO {$tbl} (app_name, app_created, app_updated, app_client_key, app_master_key, app_active) VALUES ('{$app}', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), '{$cky}', '{$mky}', 'on')";
    $aid = jrCore_db_query($req, 'INSERT_ID');
    if ($aid) {
        $req = "SELECT * FROM {$tbl} WHERE app_id = '{$aid}' LIMIT 1";
        $_rt = jrCore_db_query($req, 'SINGLE');
        jrCore_trigger_event('jrProximaCore', 'app_created', $_rt);
        jrCore_set_form_notice('success', 'The new app was successfully created');
        jrCore_form_delete_session();
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered creating the app - please try again');
    }
    jrCore_form_result();
}

/**
 * jrProximaCore_app_update
 */
function view_jrProximaCore_app_update($_post, $_user, $_conf)
{
    jrUser_master_only();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid app id');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrProximaCore', 'app');
    $req = "SELECT * FROM {$tbl} WHERE app_id = '{$_post['id']}' LIMIT 1";
    $_ap = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_ap)) {
        jrCore_set_form_notice('error', 'invalid app id');
        jrCore_location('referrer');
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrProximaCore', 'app_browser');
    jrCore_page_banner('modify app');
    jrCore_get_form_notice();
    jrCore_page_note('Changing either the <strong>Client Key</strong> or <strong>Master Key</strong> will require<br>an update to clients using the existing key value, or they will no longer function.');

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/app_browser",
        'values'       => $_ap
    );
    jrCore_form_create($_tmp);

    // App ID
    $_tmp = array(
        'name'  => 'id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    // App Name
    $_tmp = array(
        'name'     => 'app_name',
        'label'    => 'app name',
        'help'     => 'Enter the name of this app.  This value is used for reference in the Control Panel, and does not impact client requests if changed.',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Client Key
    $_tmp = array(
        'name'     => 'app_client_key',
        'label'    => 'client key',
        'help'     => 'This is the unique Client Key for this App.<br><br><strong>Warning:</strong> Changing this value will require an update to any client using the existing value, or they will no longer function.',
        'type'     => 'text',
        'validate' => 'core_string',
        'style'    => 'font-family:monospace',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Master Key
    $_tmp = array(
        'name'     => 'app_master_key',
        'label'    => 'master key',
        'help'     => 'This is the unique Master Key for this App.<br><br><strong>Warning:</strong> Changing this value will require an update to any client using the existing value, or they will no longer function.',
        'type'     => 'text',
        'validate' => 'core_string',
        'style'    => 'font-family:monospace',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Active
    $_tmp = array(
        'name'     => 'app_active',
        'label'    => 'active',
        'help'     => 'You can <strong>de-activate</strong> this app by unchecking this option.  This will make it so any API requests using this app\'s keys will be denied. No data will be removed.',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'default'  => 'on',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

/**
 * jrProximaCore_app_update_save
 */
function view_jrProximaCore_app_update_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid app id');
        jrCore_form_result();
    }
    $tbl = jrCore_db_table_name('jrProximaCore', 'app');
    $req = "SELECT * FROM {$tbl} WHERE app_id = '{$_post['id']}' LIMIT 1";
    $_ap = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_ap)) {
        jrCore_set_form_notice('error', 'invalid app id');
        jrCore_form_result();
    }
    $nam = jrCore_db_escape($_post['app_name']);
    $cky = jrCore_db_escape($_post['app_client_key']);
    $mky = jrCore_db_escape($_post['app_master_key']);
    $act = jrCore_db_escape($_post['app_active']);
    $req = "UPDATE {$tbl} SET app_name = '{$nam}', app_client_key = '{$cky}', app_master_key = '{$mky}', app_active = '{$act}' WHERE app_id = '{$_post['id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        // Reset cache
        jrCore_delete_cache('jrProximaCore', $mky, false, false);
        jrCore_set_form_notice('success', 'The Application has been successfully updated');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered updating the Application - please try again');
    }
    jrCore_form_result();
}

/**
 * jrProximaCore_app_delete_save
 */
function view_jrProximaCore_app_delete_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid app id');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrProximaCore', 'app');
    $req = "SELECT * FROM {$tbl} WHERE app_id = '{$_post['id']}' LIMIT 1";
    $_ap = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_ap)) {
        jrCore_set_form_notice('error', 'invalid app id');
        jrCore_location('referrer');
    }
    $req = "DELETE FROM {$tbl} WHERE app_id = '{$_post['id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        // Trigger event for modules to cleanup
        jrCore_trigger_event('jrProximaCore', 'app_deleted', $_ap);
        jrCore_set_form_notice('success', 'The Application has been successfully deleted');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered deleting the Application - please try again');
    }
    jrCore_location('referrer');
}

/**
 * jrProximaCore_default
 * Routing front end for all Proxima Modules!
 */
function view_jrProximaCore_default($_post, $_user, $_conf)
{
    global $_urls;
    $_SERVER['jr_is_ajax_request'] = 1;  // enables JSON errors from the Core

    // Require SSL
    if ($_conf['jrProximaCore_require_ssl'] == 'on' && strpos($_conf['jrCore_base_url'], 'https') !== 0) {
        return jrProximaCore_http_response(403, 'protocol forbidden');
    }

    // Handles all POST/GET/PUT/DELETE calls
    $_vars = jrProximaCore_get_method_variables();

    // api/<option>
    // api/data = api/px_data = jrProximaData
    if (!isset($_urls["px_{$_vars['option']}"])) {
        return jrProximaCore_http_response(404, 'invalid module');
    }
    // jrProximaData
    $pxmod = $_urls["px_{$_vars['option']}"];

    // Process Steps:
    // 1) Validate Module
    // 2) Validate Module Method function
    // 3) Validate Client Key
    // 4) Validate App Configuration
    // 5) Validate Client Session
    // 6) Trigger for Param Methods
    // 7) Execute Method function

    // Validate Module is active
    if (!jrCore_module_is_active($pxmod)) {
        return jrProximaCore_http_response(404, 'invalid module');
    }

    // Validate Module function
    if (!$_cfg = jrProximaCore_get_module_config($pxmod)) {
        return jrProximaCore_http_response(404, 'invalid module config');
    }

    // Validate Client Key
    // 0 => App ID
    // 1 => Session Key
    $_keys = jrProximaCore_get_credentials();
    if (!$_keys || !isset($_keys[0])) {
        return jrProximaCore_http_response(401, 'invalid application key');
    }

    // Validate App Configuration
    if (!$_app = jrProximaCore_get_app_by_key($_keys[0])) {
        return jrProximaCore_http_response(404, 'application not found');
    }

    // Is the App active?
    if ($_app['app_active'] == 'off') {
        return jrProximaCore_http_response(403, 'application is not active');
    }

    // We're past init errors - trigger init event
    jrCore_trigger_event('jrProximaCore', 'process_init', $_vars);

    // Get our Access Method - i.e. POST/GET/PUT/DELETE
    $httpm = jrProximaCore_get_access_method();

    // Validate Client Session (if not master)
    if (jrProximaCore_get_client_access_level() != 'master') {
        $op = (isset($_post['_1'])) ? "/{$_post['_1']}" : '/';
        $rs = true;
        if (isset($_cfg['no_session']) && isset($_cfg['no_session'][$httpm])) {
            // Looks like this module has some methods that
            // do not require a session - see if we have one
            if (in_array($op, $_cfg['no_session'][$httpm])) {
                // No session required
                $rs = false;
            }
        }
        if ($rs) {
            $key = jrProximaCore_get_session_key();
            if (!jrProximaCore_is_valid_user_session($key)) {
                return jrProximaCore_http_response(401, 'invalid user session');
            }
        }
    }

    // Set active ID's - NOTE: with Master key both with will be 0 (zero)
    jrProximaCore_set_active_user_and_profile_ids();

    // Run Method function
    return jrProximaCore_run_module_method_function($pxmod, $httpm, $_app, $_cfg, $_vars);
}
