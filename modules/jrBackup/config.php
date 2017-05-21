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
 * config
 */
function jrBackup_config()
{
    // Enabled
    $_tmp = array(
        'name'     => 'enabled',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'default'  => 'off',
        'label'    => 'daily backup to S3',
        'help'     => 'If enabled, daily backups to Amazon S3 will occur during the daily maintenance cycle.',
        'section'  => 'daily offsite backups',
        'order'    => 1
    );
    jrCore_register_setting('jrBackup', $_tmp);

    // AWS Access Key
    $_tmp = array(
        'name'     => 'access_key',
        'type'     => 'text',
        'validate' => 'not_empty',
        'default'  => '',
        'label'    => 'AWS access key',
        'help'     => 'Enter your Amazon Web Services (AWS) Access Key.<br><br>Create this key in the &quot;Access Keys&quot; section of the Your Security Credentials Page:<br><br><a href="https://console.aws.amazon.com/iam/home#security_credential" target="_blank">https://console.aws.amazon.com/iam/home#security_credential</a>',
        'section'  => 'daily offsite backups',
        'order'    => 2
    );
    jrCore_register_setting('jrBackup', $_tmp);

    // AWS Secret Key
    $_tmp = array(
        'name'     => 'secret_key',
        'type'     => 'text',
        'validate' => 'not_empty',
        'default'  => '',
        'label'    => 'AWS secret key',
        'help'     => 'Enter your Amazon Web Services (AWS) Secret Key.<br><br>Create this key in the &quot;Access Keys&quot; section of the Your Security Credentials Page:<br><br><a href="https://console.aws.amazon.com/iam/home#security_credential" target="_blank">https://console.aws.amazon.com/iam/home#security_credential</a>',
        'section'  => 'daily offsite backups',
        'order'    => 3
    );
    jrCore_register_setting('jrBackup', $_tmp);

    // AWS S3 Bucket
    $_tmp = array(
        'name'     => 'bucket',
        'type'     => 'text',
        'validate' => 'not_empty',
        'default'  => '',
        'label'    => 'S3 bucket name',
        'help'     => 'Enter your Amazon Web Services (AWS) S3 Bucket Name.<br><br>This S3 Bucket must already exist in your AWS account:<br><br><a href="https://console.aws.amazon.com/s3/home" target="_blank">https://console.aws.amazon.com/s3/home</a>',
        'section'  => 'daily offsite backups',
        'order'    => 4
    );
    jrCore_register_setting('jrBackup', $_tmp);

    // Hourly Snapshots
    $_tmp = array(
        'name'     => 'hourly',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'default'  => 'off',
        'label'    => 'enable hourly backups',
        'help'     => 'If enabled, hourly snapshots of each database table will be stored locally.  Individual tables can then be restored using the Tools -> Hourly Backup Browser',
        'section'  => 'hourly database backups',
        'order'    => 10
    );
    jrCore_register_setting('jrBackup', $_tmp);

    // Hours
    $_tmp = array(
        'name'     => 'hours',
        'type'     => 'text',
        'validate' => 'number_nz',
        'default'  => 8,
        'min'      => 1,
        'max'      => 240,
        'label'    => 'hours to keep',
        'help'     => 'How many hours of hourly backups should be kept inthe Hourly Backup Browser?',
        'section'  => 'hourly database backups',
        'order'    => 11
    );
    jrCore_register_setting('jrBackup', $_tmp);

    return true;
}

/**
 * Make sure the S3 credentials given actually work
 * @param $_post
 * @return bool
 */
function jrBackup_config_validate($_post)
{
    global $_conf;

    if (isset($_post['bucket']) && strpos(' ' . $_post['bucket'], '/')) {
        jrCore_set_form_notice('error', "Invalid S3 bucket name - bucket name cannot be a directory");
        return false;
    }

    // Save existing values to change back at end
    if (isset($_post['access_key'])) {
        $a = $_conf['jrBackup_access_key'];
        $s = $_conf['jrBackup_secret_key'];
        $b = $_conf['jrBackup_bucket'];

        // Set to new conf values so we can test
        $_conf['jrBackup_access_key'] = $_post['access_key'];
        $_conf['jrBackup_secret_key'] = $_post['secret_key'];
        $_conf['jrBackup_bucket']     = $_post['bucket'];

        // Create temp file and try to store it
        $cdr = jrCore_get_module_cache_dir('jrBackup');
        $fil = "{$cdr}/it_works.txt";
        jrCore_write_to_file($fil, 'it works - you can delete this file');
        if (!jrBackup_copy_file_to_s3("{$cdr}/it_works.txt", 'it_works.txt')) {
            jrCore_set_form_notice('error', "Invalid AWS setting - unable to store test file to S3/{$_post['bucket']}");
            return false;
        }

        // Change back so the core updates values
        $_conf['jrBackup_access_key'] = $a;
        $_conf['jrBackup_secret_key'] = $s;
        $_conf['jrBackup_bucket']     = $b;
    }
    return $_post;
}
