<?php
/**
 * Jamroom DB and System Backup module
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//------------------------------
// snapshots
//------------------------------
function view_jrBackup_snapshots($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrBackup');

    jrCore_page_banner('Hourly Backup Browser', jrCore_page_button("snapshot", 'create backup set now', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/snapshot')"));
    jrCore_get_form_notice();

    $dat             = array();
    $dat[1]['title'] = 'backup date and time';
    $dat[1]['width'] = '40%';
    $dat[2]['title'] = 'table count';
    $dat[2]['width'] = '25%';
    $dat[3]['title'] = 'size';
    $dat[3]['width'] = '25%';
    $dat[4]['title'] = 'restore options';
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = 'delete backup set';
    $dat[5]['width'] = '5%';
    jrCore_page_table_header($dat);

    $dir = jrBackup_get_snapshot_directory();
    $_fl = glob("{$dir}/*.gz");
    if ($_fl && is_array($_fl)) {
        $_fl = array_reverse($_fl);
        $_hr = array();
        foreach ($_fl as $file) {
            $tmp = substr(basename($file), 0, 12);
            if (!isset($_hr[$tmp])) {
                $_hr[$tmp] = array(
                    'year'   => substr($tmp, 0, 4),
                    'month'  => substr($tmp, 4, 2),
                    'day'    => substr($tmp, 6, 2),
                    'hour'   => substr($tmp, 8, 2),
                    'minute' => substr($tmp, 10, 2),
                    'count'  => 0,
                    'size'   => 0
                );
            }
            $_hr[$tmp]['count']++;
            $_hr[$tmp]['size'] += filesize($file);
        }
        if (count($_hr) > 0) {
            foreach ($_hr as $tmp => $_inf) {
                $dat             = array();
                $dat[1]['title'] = jrCore_format_time(mktime($_inf['hour'], $_inf['minute'], 0, $_inf['month'], $_inf['day'], $_inf['year']));
                $dat[1]['class'] = 'center';
                $dat[2]['title'] = jrCore_number_format($_inf['count']);
                $dat[2]['class'] = 'center';
                $dat[3]['title'] = jrCore_format_size($_inf['size']);
                $dat[3]['class'] = 'center';
                $dat[4]['title'] = jrCore_page_button("restore-{$tmp}", 'restore options', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/snapshot_restore/{$tmp}')");
                $dat[6]['title'] = jrCore_page_button("delete-{$tmp}", 'delete backup set', "if(confirm('Delete this database backup set?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/snapshot_delete_save/{$tmp}') }");
                jrCore_page_table_row($dat);
            }
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = 'No hourly database snapshots were found';
        $dat[1]['class'] = 'center p20';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_display();
}

//------------------------------
// snapshot
//------------------------------
function view_jrBackup_snapshot($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrBackup');

    jrCore_page_banner("Database Table Backup");

    // Form init
    $_tmp = array(
        'submit_value'  => 'backup tables',
        'cancel'        => 'referrer',
        'submit_prompt' => 'Make a backup of your database tables? Please be patient as this process could take some time depending on the size of your database.',
        'submit_modal'  => 'update',
        'modal_width'   => 600,
        'modal_height'  => 400,
        'modal_note'    => 'Please be patient while the Database Tables are exported',
        'modal_onclick' => "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/snapshots')"
    );
    jrCore_form_create($_tmp);

    // Backup Tables
    $_tmp = array(
        'name'     => 'backup_tables',
        'label'    => 'backup tables',
        'help'     => 'Check this box to backup your database tables.',
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// snapshot_save
//------------------------------
function view_jrBackup_snapshot_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    jrCore_logger('INF', 'manual database backup started');
    @ini_set('memory_limit', '512M');
    ini_set('max_execution_time', 7200); // 2 hours max

    // Backup Tables
    if (isset($_post['backup_tables']) && $_post['backup_tables'] == 'on') {
        jrCore_form_modal_notice('update', "backing up database tables...");
        jrBackup_snapshot_tables(true);
        jrCore_form_modal_notice('update', "deleting old snapshots...");
        jrBackup_delete_old_snapshots();
    }

    jrCore_form_delete_session();
    jrCore_logger('INF', 'manual database backup completed');
    jrCore_form_modal_notice('complete', 'The database backup successfully completed');
    jrCore_db_close();
    exit;
}

//------------------------------
// snapshot_delete_save
//------------------------------
function view_jrBackup_snapshot_delete_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['_1']) || strlen($_post['_1']) !== 12) {
        jrCore_set_form_notice('error', 'Invalid snapshot timestamp - please try again');
        jrCore_location('referrer');
    }
    if (jrBackup_delete_snapshot_set($_post['_1'])) {
        jrCore_set_form_notice('success', 'The backup set was sucessfully deleted');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered deleting the backup set - please try again');
    }
    jrCore_location('referrer');
}

//------------------------------
// snapshot_restore
//------------------------------
function view_jrBackup_snapshot_restore($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    if (!isset($_post['_1']) || strlen($_post['_1']) !== 12) {
        jrCore_set_form_notice('error', 'Invalid snapshot timestamp - please try again');
        jrCore_location('referrer');
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrBackup');

    $tmp  = jrCore_page_button('cancel', 'cancel', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/snapshots')");
    $tmp .= jrCore_page_button('all', 'restore all', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/snapshot_restore_table/{$_post['_1']}/all')");
    jrCore_page_banner('Backup Table Browser', $tmp);

    $dat             = array();
    $dat[1]['title'] = '<input type="checkbox" class="form_checkbox" onclick="$(\'.table_checkbox\').prop(\'checked\',$(this).prop(\'checked\'));">';
    $dat[1]['width'] = '1%';
    $dat[2]['title'] = '';
    $dat[2]['width'] = '1%';
    $dat[3]['title'] = 'module';
    $dat[3]['width'] = '40%';
    $dat[4]['title'] = 'table name';
    $dat[4]['width'] = '33%';
    $dat[5]['title'] = 'table size';
    $dat[5]['width'] = '20%';
    $dat[6]['title'] = 'restore';
    $dat[6]['width'] = '5%';
    jrCore_page_table_header($dat);

    $dir = jrBackup_get_snapshot_directory();
    $_fl = glob("{$dir}/{$_post['_1']}:*.gz");
    if ($_fl && is_array($_fl)) {

        $_new = array();
        foreach ($_mods as $mod => $_inf) {
            $new = strtolower($mod);
            $_new[$new] = $_inf;
        }

        $_tb = array();
        foreach ($_fl as $file) {
            list(, $tbl,) = explode(':', basename($file));
            $_tb[$tbl] = filesize($file);
        }
        if (count($_tb) > 0) {
            $num = 0;
            foreach ($_tb as $tbl => $size) {
                switch ($tbl) {
                    case 'jr_jrcore_cache':
                    case 'jr_jrcore_form_session':
                    case 'jr_jrcore_modal':
                    case 'jr_jruser_session':
                        continue 2;
                        break;
                    default:
                        list(, $mod, ) = explode('_', $tbl, 3);
                        if (!isset($_new[$mod])) {
                            continue 2;
                        }
                        break;
                }
                $dat             = array();
                $dat[1]['title'] = '<input type="checkbox" class="form_checkbox table_checkbox" name="' . $tbl . '">';

                // What module is this table from?
                list(, $mod, ) = explode('_', $tbl, 3);
                $dat[2]['title'] = jrCore_get_module_icon_html($_new[$mod]['module_directory'], 24);
                $dat[3]['title'] = $_new[$mod]['module_name'];
                $dat[4]['title'] = $tbl;
                $dat[4]['class'] = 'center';
                $dat[5]['title'] = jrCore_format_size($size);
                $dat[5]['class'] = 'center';
                $dat[6]['title'] = jrCore_page_button("restore-{$num}", 'restore table', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/snapshot_restore_table/{$_post['_1']}/{$tbl}')");
                jrCore_page_table_row($dat);
                $num++;
            }
            $sjs             = "var v = $('input:checkbox.table_checkbox:checked').map(function(){ return this.name; }).get().join(',')";
            $tmp             = jrCore_page_button("all-bottom", 'restore checked', "{$sjs};jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/snapshot_restore_table/{$_post['_1']}/' + v)");
            $dat             = array();
            $dat[1]['title'] = $tmp;
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = 'No tables found';
        $dat[1]['class'] = 'center p20';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_display();
}

//------------------------------
// snapshot_restore_table
//------------------------------
function view_jrBackup_snapshot_restore_table($_post, $_user, $_conf)
{
    jrUser_master_only();

    // Must get the timestamp we are restoring
    if (!isset($_post['_1']) || strlen($_post['_1']) !== 12) {
        jrCore_set_form_notice('error', 'Invalid snapshot timestamp - please try again');
        jrCore_location('referrer');
    }

    // Must get table option - can be:
    // "all" - all tables in backup set
    // $table_name - restore specific table
    // $table_name,$table_name[,...] - set of comma separated tables to restore
    if (!isset($_post['_2']) || strlen($_post['_2']) === 0) {
        jrCore_set_form_notice('error', 'Invalid table - please try again');
        jrCore_location('referrer');
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrBackup');
    jrCore_page_banner('Backup Restore');

    // Form init
    $_tmp = array(
        'submit_value'  => 'start restoral',
        'cancel'        => 'referrer',
        'submit_prompt' => 'Are you sure you want to restore the selected tables?',
        'submit_modal'  => 'update',
        'modal_width'   => 600,
        'modal_height'  => 400,
        'modal_note'    => 'Please be patient while the restoral runs',
        'modal_onclick' => "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/snapshots')"
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'  => 'snapshot',
        'type'  => 'hidden',
        'value' => $_post['_1']
    );
    jrCore_form_field_create($_tmp);

    if ($_post['_2'] == 'all') {
        // Restore All
        $_tmp = array(
            'name'     => 'restore_all_tables',
            'label'    => 'restore all tables',
            'help'     => 'Check this option to restore all tables in the backup set',
            'type'     => 'checkbox',
            'value'    => 'on',
            'validate' => 'onoff'
        );
        jrCore_form_field_create($_tmp);
    }
    else {
        // Restore Selected
        $_opt = array();
        foreach (explode(',', $_post['_2']) as $v) {
            $_opt[$v] = $v;
        }
        $_tmp = array(
            'name'    => 'tables',
            'label'   => 'restore selected tables',
            'help'    => 'Check this option to restore all tables in the backup set',
            'type'    => 'optionlist',
            'options' => $_opt,
            'value'   => $_opt
        );
        if (count($_opt) > 10) {
            $_tmp['layout']  = 'columns';
            $_tmp['columns'] = 2;
        }
        jrCore_form_field_create($_tmp);
    }
    jrCore_page_display();

}

//------------------------------
// snapshot_restore_table_save
//------------------------------
function view_jrBackup_snapshot_restore_table_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    jrCore_logger('INF', 'manual table restoral started');
    @ini_set('memory_limit', '512M');
    ini_set('max_execution_time', 82800); // 23 hours max

    // Backup Tables
    if (isset($_post['restore_all_tables']) && $_post['restore_all_tables'] == 'on') {

        // We are restoring ALL tables in this backup set
        jrCore_form_modal_notice('update', "restoring all tables in backup set...");
        $_db = array();
        $_fl = jrBackup_get_snapshot_set($_post['snapshot']);
        if ($_fl && is_array($_fl)) {
            foreach ($_fl as $file) {
                list(, $tbl,) = explode(':', basename($file));
                if (jrBackup_table_import($tbl, $file)) {
                    jrCore_form_modal_notice('update', "successfully restored table: {$tbl}");
                    $_db[] = $tbl;
                }
            }
        }
        jrCore_logger('INF', 'manual table restoral completed', $_db);

    }
    else {

        // Restoring specific tables
        $dir = jrBackup_get_snapshot_directory();
        foreach (explode(',', $_post['tables']) as $tbl) {
            $tbl = trim($tbl);
            $_fl = glob("{$dir}/{$_post['snapshot']}:{$tbl}:*.gz");
            if ($_fl && count($_fl) === 1 && is_file($_fl[0])) {
                if (jrBackup_table_import($tbl, $_fl[0])) {
                    jrCore_form_modal_notice('update', "successfully restored table: {$tbl}");
                }
            }
        }
        jrCore_logger('INF', 'manual table restoral completed', explode(',', $_post['tables']));

    }

    jrCore_form_delete_session();
    jrCore_form_modal_notice('complete', 'The system restore successfully completed');
    jrCore_db_close();
    exit;
}

//------------------------------
// backup
//------------------------------
function view_jrBackup_backup($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrBackup', 'backup');

    if (!jrBackup_is_s3_configured()) {
        jrCore_set_form_notice('error', 'Your AWS Settings are not configured properly to perform a system backup');
    }

    jrCore_page_banner("System Backup");

    // Form init
    $_tmp = array(
        'submit_value'  => 'backup system',
        'cancel'        => 'referrer',
        'submit_prompt' => 'Are you sure you want to backup the system now? Please be patient as this process could take some time depending on the size of your system.',
        'submit_modal'  => 'update',
        'modal_width'   => 600,
        'modal_height'  => 400,
        'modal_note'    => 'Please be patient while the System Backup runs'
    );
    jrCore_form_create($_tmp);

    // Backup Tables
    $_tmp = array(
        'name'     => 'backup_tables',
        'label'    => 'backup database',
        'help'     => 'Check this box to backup your database tables.',
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Backup Profiles
    $_tmp = array(
        'name'     => 'backup_profiles',
        'label'    => 'backup profiles',
        'help'     => 'Check this box to backup the media files for your profiles.',
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Backup Modules and Skins
    $_tmp = array(
        'name'     => 'backup_items',
        'label'    => 'backup modules and skins',
        'help'     => 'Check this box to backup your modules and skins directories',
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// backup_save
//------------------------------
function view_jrBackup_backup_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    jrCore_logger('INF', 'manual system backup started');
    @ini_set('memory_limit', '512M');
    ini_set('max_execution_time', 82800); // 23 hours max

    // Backup Tables
    if (isset($_post['backup_tables']) && $_post['backup_tables'] == 'on') {

        jrCore_form_modal_notice('update', "backing up system tables...");
        jrBackup_backup_tables(true);
    }

    // Backup Profiles
    if (isset($_post['backup_profiles']) && $_post['backup_profiles'] == 'on') {

        // Next do profiles
        jrCore_form_modal_notice('update', "backing up profile media...");
        $tbl = jrCore_db_table_name('jrProfile', 'item');
        $req = "SELECT `_item_id` FROM {$tbl}";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if (isset($_rt) && is_array($_rt)) {
            foreach ($_rt as $_id) {
                jrCore_form_modal_notice('update', "backing up media for profile_id: {$_id['_item_id']}");
                jrBackup_backup_profile_media($_id['_item_id']);
                jrCore_form_modal_notice('update', "successfully backed up media for profile_id: {$_id['_item_id']}");
            }
        }
    }

    // Backup Modules and Skins
    if (isset($_post['backup_items']) && $_post['backup_items'] == 'on') {

        // Next do profiles
        jrCore_form_modal_notice('update', "backing up modules and skins...");
        if (jrBackup_backup_modules_and_skins()) {
            jrCore_form_modal_notice('update', "successfully backed up modules and skins");
        }
    }

    jrCore_form_delete_session();
    jrCore_logger('INF', 'manual system backup completed');
    jrCore_form_modal_notice('complete', 'The system backup successfully completed');
    jrCore_db_close();
    exit;
}

//------------------------------
// restore
//------------------------------
function view_jrBackup_restore($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrBackup', 'restore');

    if (!jrBackup_is_s3_configured()) {
        jrCore_set_form_notice('error', 'Your AWS Settings are not configured properly to perform a system restore');
    }

    // Get dates we are backed up for
    require_once APP_DIR . "/modules/jrBackup/contrib/S3/S3.php";
    $pfx = 'modules.';
    S3::setAuth($_conf['jrBackup_access_key'], $_conf['jrBackup_secret_key']);
    $_dy = S3::getBucket($_conf['jrBackup_bucket'], $pfx);
    // [modules.20130925.tar] => Array
    //    (
    //        [name] => modules.20130925.zip
    //        [time] => 1380114746
    //        [size] => 14557968
    //        [hash] => cd95f900394ef5ab22b501cc30788262
    //    )
    $_dt = array();
    if (is_array($_dy)) {
        foreach ($_dy as $file => $_inf) {
            $date       = substr($file, 8, 8);
            $_dt[$date] = substr($date, 0, 4) . '/' . substr($date, 4, 2) . '/' . substr($date, 6, 2);
        }
        krsort($_dt, SORT_NUMERIC);
    }
    if (count($_dt) === 0) {
        jrCore_set_form_notice('error', 'No Backup files were found in the AWS S3 Bucket - check AWS settings');
    }
    jrCore_page_banner("System Restore");
    jrCore_get_form_notice();


    // Form init
    $_tmp = array(
        'submit_value'  => 'restore selected options',
        'cancel'        => 'referrer',
        'submit_prompt' => 'Restore the selected options? Your existing data will be overwritten with the backup - do not interrupt the process or your site could be left in an unusable state.',
        'submit_modal'  => 'update',
        'modal_width'   => 600,
        'modal_height'  => 400,
        'modal_note'    => 'Please be patient while the System Restore runs'
    );
    jrCore_form_create($_tmp);

    jrCore_page_section_header('database');

    // Restore Tables
    $_tmp = array(
        'name'     => 'restore_tables',
        'label'    => 'restore entire database',
        'help'     => 'Check this box to restore all your database tables from the specified backup.',
        'type'     => 'checkbox',
        'value'    => 'off',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Restore Specific Table
    $_tmp = array(
        'name'     => 'restore_table',
        'label'    => 'restore specific table',
        'help'     => 'If you just need to restore a single table, enter the table name here.',
        'type'     => 'text',
        'value'    => '',
        'validate' => 'core_string',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Restore Specific Date
    $_tmp = array(
        'name'     => 'restore_table_date',
        'label'    => 'restore date',
        'help'     => 'Select the Date of the table you wish to restore',
        'type'     => 'select',
        'options'  => $_dt,
        'validate' => 'number_nz',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_section_header('system files');

    // Restore Modules
    $_tmp = array(
        'name'     => 'restore_modules',
        'label'    => 'restore modules',
        'help'     => 'Check this box to restore your modules from the previous backup',
        'type'     => 'checkbox',
        'value'    => 'off',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Restore Skins
    $_tmp = array(
        'name'     => 'restore_skins',
        'label'    => 'restore skins',
        'help'     => 'Check this box to restore your skins from the previous backup',
        'type'     => 'checkbox',
        'value'    => 'off',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Restore Specific Date
    $_tmp = array(
        'name'     => 'restore_files_date',
        'label'    => 'restore date',
        'help'     => 'Select the Date for the Module and/or Skins you would like to restore',
        'type'     => 'select',
        'options'  => $_dt,
        'validate' => 'number_nz',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_section_header('profile media files');

    // Restore Media
    $_tmp = array(
        'name'     => 'restore_profiles',
        'label'    => 'restore all profiles',
        'help'     => 'Check this box to restore ALL media files for your profiles from the backup.<br><br><b>NOTE:</b>If this option is checked, any value entered in the <strong>restore profile IDs</strong> text field is ignored.',
        'type'     => 'checkbox',
        'value'    => 'off',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Restore Specific Profile ID
    $_tmp = array(
        'name'     => 'restore_profile_id',
        'label'    => 'restore profile IDs',
        'help'     => 'If you just need to restore media for a specific profile_id, or a group of profile_id\'s, enter the profile_id here, or multiple profile_id values separated by commas.<br><br><b>NOTE:</b> You can find the profile_id in the ACP -> Users -> Profile Browser',
        'type'     => 'text',
        'value'    => '',
        'validate' => 'not_empty',
        'required' => false
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// restore_save
//------------------------------
function view_jrBackup_restore_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    jrCore_logger('INF', 'manual system restore started');
    ini_set('max_execution_time', 82800); // 23 hours max

    // Restore Specific Table
    if (isset($_post['restore_table']) && strlen($_post['restore_table']) > 0) {

        jrCore_form_modal_notice('update', "restoring system table {$_post['restore_table']}");
        $cdr = jrCore_get_module_cache_dir('jrBackup');
        $tbl = trim($_post['restore_table']);
        $fil = "{$cdr}/{$tbl}";
        @unlink($fil);
        $dat = trim($_post['restore_table_date']);
        if (!jrBackup_copy_s3_to_file("{$tbl}.{$dat}.sql", $fil)) {
            jrCore_logger('CRI', "unable to restore DB backup file S3/{$_conf['jrBackup_bucket']}/{$tbl} from S3");
            jrCore_form_modal_notice('error', "unable to restore DB backup file {$tbl}");
        }
        else {
            // Restore it...
            if (jrBackup_table_import($tbl, $fil)) {
                jrCore_form_modal_notice('update', "successfully restored DB table: {$tbl}");
            }
            else {
                jrCore_form_modal_notice('error', "unable to restore DB table {$tbl} - check debug");
            }
        }
    }

    // Restore Tables
    elseif (isset($_post['restore_tables']) && $_post['restore_tables'] == 'on') {

        jrCore_form_modal_notice('update', "restoring all system tables...");
        $dat = trim($_post['restore_table_date']);
        $cnt = 0;

        // Get all tables for the date from S3
        jrCore_form_modal_notice('update', "restoring all DB tables - retrieving table info from S3...");
        require_once APP_DIR . "/modules/jrBackup/contrib/S3/S3.php";
        S3::setAuth($_conf['jrBackup_access_key'], $_conf['jrBackup_secret_key']);
        $_dy = S3::getBucket($_conf['jrBackup_bucket']);
        $_rt = array();
        if (is_array($_dy)) {
            foreach ($_dy as $file => $_inf) {
                if (strpos($_inf['name'], "{$dat}.sql")) {
                    $_rt[] = $_inf['name'];
                }
            }
        }
        if ($_rt && is_array($_rt)) {
            $cdr = jrCore_get_module_cache_dir('jrBackup');
            foreach ($_rt as $file) {
                list($tbl,) = explode('.', $file, 2);
                $fil = "{$cdr}/{$tbl}";
                @unlink($fil);
                if (!jrBackup_copy_s3_to_file($file, $fil)) {
                    jrCore_logger('CRI', "unable to restore DB backup file S3/{$_conf['jrBackup_bucket']}/{$tbl} from S3");
                    jrCore_form_modal_notice('error', "unable to restore DB backup file {$tbl}");
                }
                else {
                    // Restore it...
                    if (jrBackup_table_import($tbl, $fil)) {
                        jrCore_form_modal_notice('update', "successfully restored DB table: {$tbl}");
                        $cnt++;
                    }
                    else {
                        jrCore_form_modal_notice('error', "unable to restore DB table {$tbl} - check debug");
                    }
                }
            }
        }
    }

    // Restore Modules
    if (isset($_post['restore_modules']) && $_post['restore_modules'] == 'on') {

        jrCore_form_modal_notice('update', "restoring system modules...");
        $dat = trim($_post['restore_files_date']);
        $cdr = jrCore_get_module_cache_dir('jrBackup');
        $tmp = "{$cdr}/modules.{$dat}.tar";
        jrCore_form_modal_notice('update', "downloading modules.{$dat}.tar from S3 ...");
        if (!jrBackup_copy_s3_to_file("modules.{$dat}.tar", $tmp)) {
            jrCore_logger('CRI', "unable to copy module backup file S3/{$_conf['jrBackup_bucket']}/modules.{$dat}.tar from S3");
            jrCore_form_modal_notice('error', "unable to copy module backup file S3/{$_conf['jrBackup_bucket']}/modules.{$dat}.tar from S3");
        }
        else {
            jrCore_form_modal_notice('update', "download complete");
            if (is_dir("{$cdr}/modules")) {
                // Cleanup from previously
                jrCore_delete_dir_contents("{$cdr}/modules");
                rmdir("{$cdr}/modules");
            }
            jrCore_form_modal_notice('update', "extracting module files and renaming ...");
            if (mkdir("{$cdr}/modules", $_conf['jrCore_dir_perms'], true)) {
                if (jrCore_extract_tar_archive($tmp, "{$cdr}/modules")) {
                    // Move existing modules out of the way
                    if (rename(APP_DIR . '/modules', APP_DIR . '/modules.__backup')) {
                        // Move backup into place
                        if (rename("{$cdr}/modules", APP_DIR . '/modules')) {

                            // Delete backup
                            jrCore_delete_dir_contents(APP_DIR . '/modules.__backup', false);
                            rmdir(APP_DIR . '/modules.__backup');

                            // Cleanup tmp
                            jrCore_delete_dir_contents("{$cdr}/modules");
                            rmdir("{$cdr}/modules");
                            jrCore_form_modal_notice('update', "rename complete");
                        }
                        else {
                            // Move back
                            rename(APP_DIR . '/modules.__backup', APP_DIR . '/modules');
                            jrCore_form_modal_notice('error', "unable to restore modules - check directory permissions");
                        }
                    }
                }
            }
            unlink($tmp);
            if (function_exists('jrMarket_reset_opcode_caches')) {
                jrMarket_reset_opcode_caches();
            }
        }
    }

    // Restore Skins
    if (isset($_post['restore_skins']) && $_post['restore_skins'] == 'on') {

        jrCore_form_modal_notice('update', "restoring system skins...");
        $dat = trim($_post['restore_files_date']);
        $cdr = jrCore_get_module_cache_dir('jrBackup');
        $tmp = "{$cdr}/skins.{$dat}.tar";
        jrCore_form_modal_notice('update', "downloading skins.{$dat}.tar from S3 ...");
        if (!jrBackup_copy_s3_to_file("skins.{$dat}.tar", $tmp)) {
            jrCore_logger('CRI', "unable to copy skin backup file S3/{$_conf['jrBackup_bucket']}/skins.{$dat}.tar from S3");
            jrCore_form_modal_notice('error', "unable to copy skin backup file S3/{$_conf['jrBackup_bucket']}/skins.{$dat}.tar from S3");
        }
        else {
            jrCore_form_modal_notice('update', "download complete");
            if (is_dir("{$cdr}/skins")) {
                // Cleanup from previously
                jrCore_delete_dir_contents("{$cdr}/skins");
                rmdir("{$cdr}/skins");
            }
            jrCore_form_modal_notice('update', "extracting skin files and renaming ...");
            if (mkdir("{$cdr}/skins", $_conf['jrCore_dir_perms'], true)) {
                if (jrCore_extract_tar_archive($tmp, "{$cdr}/skins")) {
                    // Move existing skins out of the way
                    if (rename(APP_DIR . '/skins', APP_DIR . '/skins.__backup')) {
                        // Move backup into place
                        if (rename("{$cdr}/skins", APP_DIR . '/skins')) {

                            // Delete backup
                            jrCore_delete_dir_contents(APP_DIR . '/skins.__backup', false);
                            rmdir(APP_DIR . '/skins.__backup');

                            // Cleanup tmp
                            jrCore_delete_dir_contents("{$cdr}/skins");
                            rmdir("{$cdr}/skins");
                            jrCore_form_modal_notice('update', "rename complete");
                        }
                        else {
                            // Move back
                            rename(APP_DIR . '/skins.__backup', APP_DIR . '/skins');
                            jrCore_form_modal_notice('error', "unable to restore skins - check directory permissions");
                        }
                    }
                }
            }
            unlink($tmp);
            if (function_exists('jrMarket_reset_opcode_caches')) {
                jrMarket_reset_opcode_caches();
            }
        }
    }

    // Restore all Profiles
    if (isset($_post['restore_profiles']) && $_post['restore_profiles'] == 'on') {
        jrCore_form_modal_notice('update', "restoring profile media...");
        $tbl = jrCore_db_table_name('jrProfile', 'item');
        $req = "SELECT `_item_id` FROM {$tbl}";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if (isset($_rt) && is_array($_rt)) {
            foreach ($_rt as $_id) {
                jrCore_form_modal_notice('update', "restoring media for profile_id: {$_post['restore_profile_id']}...");
                $cnt = jrBackup_restore_profile_media($_id['_item_id']);
                jrCore_form_modal_notice('update', "restored {$cnt} media items for profile_id: {$_id['_item_id']}");
            }
        }
    }

    // Restore Profile_ID or multiple profile_id's
    elseif (isset($_post['restore_profile_id']) && strlen($_post['restore_profile_id']) > 0) {
        foreach (explode(',', $_post['restore_profile_id']) as $pid) {
            $pid = intval(trim($pid));
            if ($pid > 0) {
                jrCore_form_modal_notice('update', "restoring media for profile_id: {$pid}...");
                $cnt = jrBackup_restore_profile_media($pid);
                jrCore_form_modal_notice('update', "restored {$cnt} media items for profile_id: {$pid}");
            }
        }
    }

    jrCore_form_delete_session();
    jrCore_logger('INF', 'manual system restore completed');
    jrCore_form_modal_notice('complete', 'The system restore has successfully completed');
    jrCore_db_close();
    exit;
}
