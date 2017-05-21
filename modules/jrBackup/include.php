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
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function jrBackup_meta()
{
    $_tmp = array(
        'name'        => 'DB and System Backup',
        'url'         => 'backup',
        'version'     => '1.4.6',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Daily system backup to Amazon S3 and Hourly database backups',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/1510/db-and-system-backup',
        'category'    => 'tools',
        'requires'    => 'jrCore:6.0.0',
        'license'     => 'mpl',
        'priority'    => 255, // LOW load priority (we want other listeners to run first)
    );
    return $_tmp;
}

/**
 * init
 */
function jrBackup_init()
{
    // Daily and Hourly Backups
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrBackup_daily_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'hourly_maintenance', 'jrBackup_hourly_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'system_check', 'jrBackup_system_check_listener');

    // Tools
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrBackup', 'snapshots', array('Hourly Backup Browser', 'Browse hourly backups of database tables that can be restored'));

    if (jrBackup_is_s3_configured()) {

        // We are setup for S3 - show backup and restore options
        jrCore_register_module_feature('jrCore', 'tool_view', 'jrBackup', 'backup', array('System Backup', 'Perform a manual System Backup'));
        jrCore_register_module_feature('jrCore', 'tool_view', 'jrBackup', 'restore', array('System Restore', 'Restore your System from the Last Backup'));

        // Custom tabs
        jrCore_register_module_feature('jrCore', 'admin_tab', 'jrBackup', 'backup', 'Backup System');
        jrCore_register_module_feature('jrCore', 'admin_tab', 'jrBackup', 'restore', 'Restore System');

    }

    // Backup worker
    jrCore_register_queue_worker('jrBackup', 'daily_backup', 'jrBackup_daily_backup_worker', 0, 1, 82800);
    jrCore_register_queue_worker('jrBackup', 'hourly_backup', 'jrBackup_hourly_backup_worker', 0, 1, 3540);
    return true;
}

//-------------------
// QUEUE WORKER
//-------------------

/**
 * Backup system
 * @param array $_queue The queue entry the worker will receive
 * @return bool
 */
function jrBackup_daily_backup_worker($_queue)
{
    global $_conf;
    if (jrBackup_is_s3_configured()) {

        jrCore_logger('INF', "daily backup to S3/{$_conf['jrBackup_bucket']} starting");
        ini_set('max_execution_time', 82800); // 23 hours max

        // First - backup tables
        $cnt = jrBackup_backup_tables();
        if ($cnt && $cnt > 0) {
            jrCore_logger('INF', "successfully backed up {$cnt} database tables to S3/{$_conf['jrBackup_bucket']}");
        }

        // Next do profiles
        $_sc = array(
            'skip_triggers'       => true,
            'ignore_pending'      => true,
            'privacy_check'       => false,
            'quota_check'         => false,
            'return_item_id_only' => true,
            'limit'               => 10000000
        );
        $_rt = jrCore_db_search_items('jrProfile', $_sc);
        if ($_rt && is_array($_rt)) {
            foreach ($_rt as $pid) {
                jrBackup_backup_profile_media($pid);
            }
            jrCore_logger('INF', "successfully backed up media for " . count($_rt) . " profiles to S3/{$_conf['jrBackup_bucket']}");
        }

        // Do Skins and Modules
        if (jrBackup_backup_modules_and_skins()) {
            jrCore_logger('INF', "successfully backed up modules and skins to S3/{$_conf['jrBackup_bucket']}");
        }

        // Cleanup any backup files we might have missed
        jrBackup_remove_old_files();

        jrCore_logger('INF', "daily backup to S3/{$_conf['jrBackup_bucket']} completed");
    }
    return true;
}

/**
 * Dump hourly database snapshots
 * @param array $_queue The queue entry the worker will receive
 * @return bool
 */
function jrBackup_hourly_backup_worker($_queue)
{
    global $_conf;

    // 59 minutes max
    ini_set('max_execution_time', 3540);

    jrBackup_snapshot_tables();

    // Cleanup old entries - more than $_conf.jrBackup_hours
    $hrs = (int) $_conf['jrBackup_hours'];
    $now = time();
    while (true) {
        $old = strftime('%Y%m%d%H', ($now - ($hrs * 3600)));
        if (jrBackup_delete_snapshot_set($old)) {
            $hrs++;
        }
        else {
            break;
        }
    }

    // Delete any we missed
    jrBackup_delete_old_snapshots();

    return true;
}

//-------------------
// EVENT LISTENERS
//-------------------

/**
 * Check mysql and mysqldump binaries
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrBackup_system_check_listener($_data, $_user, $_conf, $_args, $event)
{
    $dat             = array();
    $dat[1]['title'] = 'Backup MySQL Tools';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = 'executable';
    $dat[2]['class'] = 'center';

    $dir = jrCore_get_module_cache_dir('jrBackup');
    $tmp = tempnam($dir, 'system_check_');

    // For backing up we need mysqldump and for restoring we use mysql
    $fnd = false;
    if ($mys = jrBackup_get_mysql_binary()) {
        ob_start();
        system("{$mys} -V >{$tmp} 2>&1", $ret);
        ob_end_clean();
        if (!is_file($tmp) || !strpos(file_get_contents($tmp), 'Ver')) {
            $dat[3]['title'] = $_args['fail'];
            $dat[4]['title'] = 'mysql binary is not executable';
        }
        else {
            $fnd = true;
        }
    }
    else {
        $dat[3]['title'] = $_args['fail'];
        $dat[4]['title'] = 'mysql binary is not executable';
    }
    if ($mys = jrBackup_get_mysqldump_binary()) {
        ob_start();
        system("{$mys} -V >{$tmp} 2>&1", $ret);
        ob_end_clean();
        if (!is_file($tmp) || !strpos(file_get_contents($tmp), 'Ver')) {
            $dat[3]['title'] = $_args['fail'];
            $dat[4]['title'] = 'mysqldump binary is not executable';
        }
        else {
            $fnd = true;
        }
    }
    else {
        $dat[3]['title'] = $_args['fail'];
        $dat[4]['title'] = 'mysqldump binary is not executable';
    }

    if ($fnd) {
        $dat[3]['title'] = $_args['pass'];
        $dat[4]['title'] = 'MySQL Tools configured correctly for backups';
    }
    $dat[3]['class'] = 'center';

    jrCore_page_table_row($dat);
    return $_data;
}

/**
 * Daily off site backups of Database, media files, modules and skins
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrBackup_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_conf['jrBackup_enabled']) && $_conf['jrBackup_enabled'] == 'on' && jrBackup_is_s3_configured()) {
        jrCore_queue_create('jrBackup', 'daily_backup', array('backup' => true), 900);
    }
    return $_data;
}

/**
 * Hourly Database backups
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrBackup_hourly_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_conf['jrBackup_hourly']) && $_conf['jrBackup_hourly'] == 'on') {
        jrCore_queue_create('jrBackup', 'hourly_backup', array('backup' => true), 600);
    }
    return $_data;
}

//-------------------
// FUNCTIONS
//-------------------

/**
 * Get full path of working mysql binary
 * @return bool
 */
function jrBackup_get_mysql_binary()
{
    global $_conf;
    $mysql = '/usr/bin/mysql';
    if (isset($_conf['jrBackup_mysql_binary'])) {
        $mysql = $_conf['jrBackup_mysql_binary'];
    }
    if (is_file($mysql) && !is_executable($mysql)) {
        // Try to set permissions if we can...
        @chmod($mysql, 0755);
    }
    if (is_executable($mysql)) {
        return $mysql;
    }
    return false;
}

/**
 * Get full path of working mysqldump binary
 * @return bool
 */
function jrBackup_get_mysqldump_binary()
{
    global $_conf;
    $mysqldump = '/usr/bin/mysqldump';
    if (isset($_conf['jrBackup_mysqldump_binary'])) {
        $mysqldump = $_conf['jrBackup_mysqldump_binary'];
    }
    if (is_file($mysqldump) && !is_executable($mysqldump)) {
        // Try to set permissions if we can...
        @chmod($mysqldump, 0755);
    }
    if (is_executable($mysqldump)) {
        return $mysqldump;
    }
    return false;
}

/**
 * Get the directory we use for local snapshots
 * @return string
 */
function jrBackup_get_snapshot_directory()
{
    global $_conf;
    $dir = APP_DIR . '/data/media/1/0';
    if (!is_dir($dir)) {
        mkdir($dir, $_conf['jrCore_dir_perms'], true);
    }
    return $dir;
}

/**
 * Get snapshot files for a specific date
 * @param $date string date in YYYYMMDDHH format
 * @return array|bool
 */
function jrBackup_get_snapshot_set($date)
{
    $dir = jrBackup_get_snapshot_directory();
    $_fl = glob("{$dir}/{$date}:*.gz");
    if ($_fl && is_array($_fl)) {
        return $_fl;
    }
    return false;
}

/**
 * Delete a snapshot set by date
 * @param $date string date in YYYYMMDDHH format
 * @return bool|int
 */
function jrBackup_delete_snapshot_set($date)
{
    $dir = jrBackup_get_snapshot_directory();
    $_fl = glob("{$dir}/{$date}*.gz");
    if ($_fl && is_array($_fl)) {
        foreach ($_fl as $file) {
            unlink($file);
        }
        return count($_fl);
    }
    return false;
}

/**
 * Delete old snapshot files
 * @return int
 */
function jrBackup_delete_old_snapshots()
{
    global $_conf;
    // Cleanup old entries - more than $_conf.jrBackup_hours
    $num = 0;
    $dir = jrBackup_get_snapshot_directory();
    $_fl = glob("{$dir}/*.gz");
    if ($_fl && is_array($_fl)) {
        $hrs = (int) $_conf['jrBackup_hours'];
        $old = (time() - ($hrs * 3600));
        foreach ($_fl as $file) {
            if (filemtime($file) < $old) {
                unlink($file);
                $num++;
            }
        }
    }
    return $num;
}

/**
 * Hourly snapshot of tables
 * @param bool $modal
 * @return bool
 */
function jrBackup_snapshot_tables($modal = false)
{
    // We store in 1/0 as it is a protected directory
    $dir = jrBackup_get_snapshot_directory();

    // DB table snapshots
    $_rt = jrCore_db_query('SHOW TABLES', 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        $now = strftime('%Y%m%d%H%M');
        foreach ($_rt as $tbl) {

            // Some tables we do NOT backup
            $tbl = reset($tbl);
            switch ($tbl) {
                case 'jr_jrcore_log':
                case 'jr_jrcore_log_debug':
                case 'jr_jrcore_cache':
                case 'jr_jrcore_form_session':
                case 'jr_jrcore_modal':
                case 'jr_jrcore_play_key':
                case 'jr_jruser_session':
                    continue 2;
                    break;
            }

            $tmp = jrCore_create_unique_string(12);
            $fil = "{$dir}/{$now}:{$tbl}:${tmp}.sql";
            if ($modal) {
                jrCore_form_modal_notice('update', "exporting {$tbl}");
            }
            jrBackup_table_export($tbl, $fil, true);
        }
    }
    return true;
}

/**
 * Return true if S3 is configured for backups
 * @return bool
 */
function jrBackup_is_s3_configured()
{
    global $_conf;
    if (!isset($_conf['jrBackup_access_key']{0})) {
        return false;
    }
    elseif (!isset($_conf['jrBackup_secret_key']{0})) {
        return false;
    }
    elseif (!isset($_conf['jrBackup_bucket']{0})) {
        return false;
    }
    return true;
}

/**
 * Remove old backup files from S3
 * @return bool
 */
function jrBackup_remove_old_files()
{
    global $_conf;
    @ini_set('memory_limit', '1024M');
    require_once APP_DIR . "/modules/jrBackup/contrib/S3/S3.php";
    S3::setAuth($_conf['jrBackup_access_key'], $_conf['jrBackup_secret_key']);
    $_fl = S3::getBucket($_conf['jrBackup_bucket']);
    $_rt = array();
    $old = (time() - (8 * 86400));
    if (is_array($_fl)) {
        foreach ($_fl as $file => $_inf) {
            // table.date.sql
            // modules.date.[zip|tar]
            // skins.date.[zip|tar]
            if (substr_count($_inf['name'], '.') === 2) {
                list(, $date,) = explode('.', $_inf['name'], 3);
                if (strlen($date) === 8 && strpos($date, '2') === 0) {
                    $y = substr($date, 0, 4);
                    $m = substr($date, 4, 2);
                    $d = substr($date, 6, 2);
                    $e = strtotime("{$m}/{$d}/{$y}");
                    if ($e && $e < $old) {
                        jrBackup_delete_s3_file($_inf['name']);
                        $_rt[] = $_inf['name'];
                    }
                }
            }
        }
        if (count($_rt) > 0) {
            jrCore_logger('INF', "DB backup succesfully deleted " . count($_rt) . " outdated backup files", $_rt);
        }
    }
    return true;
}

/**
 * Backup Tables to S3
 * @param $notice bool Set to TRUE to show modal notice
 * @return int Returns number of tables successfully backed up
 */
function jrBackup_backup_tables($notice = false)
{
    global $_conf;
    $dat = strftime('%Y%m%d');
    $old = strftime('%Y%m%d', (time() - (8 * 86400)));
    $cnt = 0;
    $_rt = jrCore_db_query('SHOW TABLES', 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $tbl) {
            $tbl = reset($tbl);
            $fil = jrBackup_table_export($tbl);
            if (is_file($fil) && filesize($fil) > 0) {
                if (!jrBackup_copy_file_to_s3($fil, "{$tbl}.{$dat}.sql")) {
                    // try one more time...
                    sleep(10);  // OK
                    if (!jrBackup_copy_file_to_s3($fil, "{$tbl}.{$dat}.sql")) {
                        jrCore_logger('CRI', "unable to copy daily DB backup file {$tbl} to S3/{$_conf['jrBackup_bucket']}");
                    }
                    else {
                        // Cleanup old one
                        jrBackup_delete_s3_file("{$tbl}.{$old}.sql");
                        if ($notice) {
                            jrCore_form_modal_notice('update', "successfully backed up table: {$tbl}");
                        }
                        $cnt++;
                    }
                }
                else {
                    // Cleanup old one
                    jrBackup_delete_s3_file("{$tbl}.{$old}.sql");
                    if ($notice) {
                        jrCore_form_modal_notice('update', "successfully backed up table: {$tbl}");
                    }
                    $cnt++;
                }
            }
            @unlink($fil);
        }
    }
    return $cnt;
}

/**
 * Dump a table to a backup file
 * @param $table string DB Table to backup
 * @param $output_file string File to save to
 * @param $compress bool set to TRUE for gzip compression
 * @return mixed
 */
function jrBackup_table_export($table, $output_file = null, $compress = false)
{
    global $_conf;
    $mysqldump = jrBackup_get_mysqldump_binary();
    if (!$mysqldump) {
        return false;
    }
    if (is_null($output_file)) {
        $cdr = jrCore_get_module_cache_dir('jrBackup');
        $tmp = jrCore_create_unique_string(8);
        $fil = "{$cdr}/{$tmp}_{$table}.sql";
    }
    else {
        if (strpos($output_file, APP_DIR) === 0) {
            $fil = $output_file;
        }
        else {
            // Not in Jamroom directory - not allowed
            return false;
        }
    }
    $add = '';
    if ($compress) {
        $fil = "{$fil}.gz";
        $add = ' | gzip';
    }
    ob_start();
    system("{$mysqldump} --user=" . escapeshellarg($_conf['jrCore_db_user']) . " --password=" . escapeshellarg($_conf['jrCore_db_pass']) . " --compact --lock-tables=false --single-transaction --extended-insert --add-drop-table --quick " . escapeshellarg($_conf['jrCore_db_name']) . " {$table}{$add} >{$fil}", $ret);
    ob_end_clean();
    if (is_file($fil)) {
        return $fil;
    }
    return false;
}

/**
 * Restore a table from a backup file
 * @param $table string DB Table to restore to
 * @param $file string File to restore from
 * @return mixed
 */
function jrBackup_table_import($table, $file)
{
    global $_conf;
    $mysql = jrBackup_get_mysql_binary();
    if (!$mysql) {
        return false;
    }
    if (!is_file($file) || filesize($file) === 0) {
        return false;
    }
    ob_start();
    if (strpos($file, '.gz')) {
        // This file is gzipped - unzip first
        system("gunzip < {$file} | ${mysql}  --default-character-set=utf8 --database=" . escapeshellarg($_conf['jrCore_db_name']) . " --user=" . escapeshellarg($_conf['jrCore_db_user']) . " --password=" . escapeshellarg($_conf['jrCore_db_pass']), $ret);
    }
    else {
        system("${mysql} --default-character-set=utf8 --database=" . escapeshellarg($_conf['jrCore_db_name']) . " --user=" . escapeshellarg($_conf['jrCore_db_user']) . " --password=" . escapeshellarg($_conf['jrCore_db_pass']) . " < {$file}", $ret);
    }
    ob_end_clean();
    return true;
}

/**
 * Delete a remote S3 file
 * @param $remote_name
 * @return bool
 */
function jrBackup_delete_s3_file($remote_name)
{
    global $_conf;
    if (!jrBackup_is_s3_configured()) {
        jrCore_logger('CRI', "AWS is not configured properly - unable to backup profile");
        return false;
    }
    require_once APP_DIR . "/modules/jrBackup/contrib/S3/S3.php";
    S3::setAuth($_conf['jrBackup_access_key'], $_conf['jrBackup_secret_key']);
    return S3::deleteObject($_conf['jrBackup_bucket'], $remote_name);
}

/**
 * Copy a local file to S3 with remote name
 * @param $local_file string Local file to copy to S3
 * @param $remote_name string Name of file in S3 bucket
 * @return bool
 */
function jrBackup_copy_file_to_s3($local_file, $remote_name)
{
    global $_conf;
    if (!jrBackup_is_s3_configured()) {
        jrCore_logger('CRI', "AWS is not configured properly - unable to backup profile");
        return false;
    }
    require_once APP_DIR . "/modules/jrBackup/contrib/S3/S3.php";
    S3::setAuth($_conf['jrBackup_access_key'], $_conf['jrBackup_secret_key']);
    return S3::putObject(S3::inputFile($local_file, false), $_conf['jrBackup_bucket'], $remote_name, S3::ACL_PRIVATE, array(), array(), S3::STORAGE_CLASS_RRS);
}

/**
 * Backup modules and skins to S3
 * @return bool
 */
function jrBackup_backup_modules_and_skins()
{
    $dat = strftime('%Y%m%d');
    $old = strftime('%Y%m%d', (time() - (8 * 86400)));
    $cdr = jrCore_get_module_cache_dir('jrBackup');

    // First do modules
    $cwd = getcwd();
    $_fl = array();
    foreach (jrCore_get_directory_files(APP_DIR . '/modules') as $file => $ignore) {
        $_fl[] = str_replace(APP_DIR . '/modules/', '', $file);
    }
    chdir(APP_DIR . '/modules');
    jrCore_create_tar_archive("{$cdr}/modules.{$dat}.tar", $_fl);
    chdir($cwd);

    jrBackup_copy_file_to_s3("{$cdr}/modules.{$dat}.tar", "modules.{$dat}.tar");

    // Next do skins
    $_fl = array();
    foreach (jrCore_get_directory_files(APP_DIR . '/skins') as $file => $ignore) {
        $_fl[] = str_replace(APP_DIR . '/skins/', '', $file);
    }
    chdir(APP_DIR . '/skins');
    jrCore_create_tar_archive("{$cdr}/skins.{$dat}.tar", $_fl);
    chdir($cwd);

    jrBackup_copy_file_to_s3("{$cdr}/skins.{$dat}.tar", "skins.{$dat}.tar");

    // Delete old ones on S3
    jrBackup_delete_s3_file("modules.{$old}.tar");
    jrBackup_delete_s3_file("skins.{$old}.tar");

    // Delete local ones now that we've copied to S3
    jrCore_delete_dir_contents($cdr);

    return true;
}

/**
 * Copy remote S3 file to local file
 * @param $remote_name string Name of file in S3 bucket
 * @param $local_file string Local file to copy to S3
 * @return bool
 */
function jrBackup_copy_s3_to_file($remote_name, $local_file)
{
    global $_conf;
    if (!jrBackup_is_s3_configured()) {
        jrCore_logger('CRI', "AWS is not configured properly - unable to copy file");
        return false;
    }
    require_once APP_DIR . "/modules/jrBackup/contrib/S3/S3.php";
    S3::setAuth($_conf['jrBackup_access_key'], $_conf['jrBackup_secret_key']);
    return S3::getObject($_conf['jrBackup_bucket'], $remote_name, $local_file);
}

/**
 * Backup a Profile to S3
 * @param $profile_id integer Profile_ID to backup
 * @return mixed Returns number of media files saved to S3
 */
function jrBackup_backup_profile_media($profile_id)
{
    if (!jrCore_checktype($profile_id, 'number_nz')) {
        return false;
    }
    $cnt = 0;
    $cdr = jrCore_get_media_directory($profile_id, FORCE_LOCAL);
    $_fl = jrCore_get_directory_files($cdr);
    if (isset($_fl) && is_array($_fl)) {
        // Next - grab existing backup info
        $_s3 = jrBackup_get_profile_bucket_files($profile_id);
        foreach ($_fl as $file => $fname) {
            // See if we need to upload this file
            $rname = str_replace(APP_DIR . '/data/', '', $file);
            if (!is_array($_s3) || !isset($_s3[$rname]) || $_s3[$rname]['size'] != filesize($file)) {
                jrBackup_copy_file_to_s3($file, $rname);
            }
            $cnt++;
        }
    }
    return $cnt;
}

/**
 * Get a list of files in an S3 bucket for a profile_id
 * @param $profile_id
 * @return mixed
 */
function jrBackup_get_profile_bucket_files($profile_id)
{
    global $_conf;
    if (!jrBackup_is_s3_configured()) {
        jrCore_logger('CRI', "AWS is not configured properly - unable to list files for profile");
        return false;
    }
    require_once APP_DIR . "/modules/jrBackup/contrib/S3/S3.php";
    $cdr = jrCore_get_media_directory($profile_id, FORCE_LOCAL);
    $pfx = str_replace(APP_DIR . '/data/', '', $cdr);
    S3::setAuth($_conf['jrBackup_access_key'], $_conf['jrBackup_secret_key']);
    return S3::getBucket($_conf['jrBackup_bucket'], $pfx . '/');
}

/**
 * Restore Profile media from S3
 * @param $profile_id integer Profile_ID to backup
 * @return mixed Returns number of media files restore from S3
 */
function jrBackup_restore_profile_media($profile_id)
{
    if (!jrCore_checktype($profile_id, 'number_nz')) {
        return false;
    }
    if (!isset($_conf['jrCore_file_perms'])) {
        $_conf['jrCore_file_perms'] = 0644;
    }
    $cnt = 0;
    // get list of files in backup S3 bucket
    $_s3 = jrBackup_get_profile_bucket_files($profile_id);
    if (isset($_s3) && is_array($_s3)) {
        // [media/1/1/jrYouTube_22_youtube_file.pdf] => Array
        //    [name] => media/1/1/jrYouTube_22_youtube_file.pdf
        //    [time] => 1375462634
        //    [size] => 88876
        //    [hash] => 3144362f61b17a7417a962748b08cbe6
        foreach ($_s3 as $_inf) {
            $lname = APP_DIR . "/data/{$_inf['name']}";
            if (!is_file($lname) || filesize($lname) != $_inf['size']) {
                jrBackup_copy_s3_to_file($_inf['name'], $lname);
                chmod($lname, $_conf['jrCore_file_perms']);
            }
            $cnt++;
        }
    }
    return $cnt;
}
