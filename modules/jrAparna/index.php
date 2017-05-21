<?php
/**
 * Jamroom 5 Aparna module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
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
// clone_module
//------------------------------
function view_jrAparna_clone($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrAparna');

    // Make sure the module directory is writable by the web user
    jrCore_page_banner('Create Module', 'Create a new customizable DataStore module');
    if (!is_writable(APP_DIR . '/modules')) {
        jrCore_page_notice('error', 'The modules directory is not writable by the web user - unable to create custom modules');
        jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    }
    elseif ($_post['module_url'] != strrev('anrapa')) {
        jrCore_page_notice('error', 'Use the <strong>Aparna</strong> module to create new modules!');
        jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    }
    else {

        jrCore_page_notice('success', "After creating a new module, the module will appear in the &quot;custom&quot; category in the ACP<br>and be customizable using the Form Designer and Template Editor", false);

        // Form init
        $_tmp = array(
            'submit_value'  => 'Create New Module',
            'cancel'        => 'referrer',
            'submit_prompt' => 'Are you sure you want to create a new module?',
        );
        jrCore_form_create($_tmp);

        $_tmp = array(
            'name'     => 'module_name',
            'label'    => 'Module Name',
            'help'     => "Enter a Name for this new Module - only numbers, letters, underscores, dashes and spaces are allowed.",
            'type'     => 'text',
            'value'    => '',
            'min'      => 3,
            'validate' => 'user_name'
        );
        jrCore_form_field_create($_tmp);

        $_tmp = array(
            'name'     => 'module_desc',
            'label'    => 'Module Description',
            'help'     => "Enter a brief description for this module that will appear in the Info tab of the module.",
            'type'     => 'text',
            'value'    => '',
            'validate' => 'printable'
        );
        jrCore_form_field_create($_tmp);

        $_tmp = array(
            'name'     => 'module_icon',
            'label'    => 'Module Icon',
            'sublabel' => '256 x 256 pixel PNG',
            'help'     => "Upload a 256x256 pixel icon if you would like to use a different icon than the default icon",
            'type'     => 'file',
            'text'     => 'Select Icon',
            'required' => false,
            'allowed'  => 'png',
            'max'      => 102400
        );
        jrCore_form_field_create($_tmp);
    }
    jrCore_page_display();
}

//------------------------------
// clone_module_save
//------------------------------
function view_jrAparna_clone_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    $pfx = 'xx';
    if (isset($_conf['jrDeveloper_developer_prefix']) && strlen($_conf['jrDeveloper_developer_prefix']) > 0) {
        $pfx = trim($_conf['jrDeveloper_developer_prefix']);
    }
    $_rc = array(' ', '_', '-');
    $dir = $pfx . trim(str_replace($_rc, '', ucwords($_post['module_name'])));

    // Make sure we don't already exist
    if (is_dir(APP_DIR . "/modules/{$dir}") || is_link(APP_DIR . "/modules/{$dir}")) {
        jrCore_set_form_notice('error', 'A module by that name already exists - please try another');
        jrCore_form_result();
    }
    // Clone Aparna
    $_rp = array(
        'xx_description_xx' => addslashes($_post['module_desc']),
        'jrAparna'          => $dir,
        'Aparna'            => $_post['module_name'],
        'aparna'            => strtolower(trim(str_replace($_rc, '', $_post['module_name'])))
    );
    $res = jrCore_copy_dir_recursive(APP_DIR . "/modules/jrAparna/clone_files", APP_DIR . "/modules/{$dir}", $_rp);
    if (!$res) {
        jrCore_set_form_notice('error', "An error was encountered trying to copy the module directory - check Error Log");
    }
    else {

        // Copy icon over
        $_up = jrCore_get_uploaded_meter_files($_post['upload_token']);
        if ($_up && is_array($_up)) {
            foreach ($_up as $_info) {
                rename($_info['tmp_name'], APP_DIR . "/modules/{$dir}/icon.png");
            }
        }

        // Install new module
        jrCore_validate_module_schema($dir);
        jrCore_verify_module($dir);

        $tbl = jrCore_db_table_name('jrCore', 'module');
        $req = "UPDATE {$tbl} SET module_category = 'custom' WHERE module_directory = '" . jrCore_db_escape($dir) . "'";
        jrCore_db_query($req);

        jrCore_form_delete_session();
        jrCore_delete_config_cache();
        $murl = jrCore_get_module_url($dir);
        jrCore_location("{$_conf['jrCore_base_url']}/{$murl}/admin/info");
    }
    jrCore_form_result();
}
