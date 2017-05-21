<?php
/**
 * Jamroom Geo Location module
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
// database
//------------------------------
function view_jrGeo_database($_post, $_user, $_conf)
{
    // Master Only
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrGeo', 'database');

    jrCore_set_form_notice('success', 'Download the latest <b>GeoLite City</b> binary database gzip file from MaxMind:<br><br><a href="https://www.jamroom.net/r/geoip-city-database-download"><u>Click here to download the latest Free GeoLite City database file</u></a><br><br>Unzip the downloaded file and upload the <b>GeoLiteCity.dat</b> file found inside.<br><br><small>This product includes GeoLite data created by MaxMind, available from <a href="http://www.maxmind.com">http://www.maxmind.com</a>.</small>', false);

    jrCore_page_banner('Geo Location Database');
    jrCore_get_form_notice();

    $_files = jrCore_get_media_files(0, 'geoipcity*');
    if ($_files && is_array($_files) && isset($_files[0])) {
        jrCore_page_notice('notice', 'current file: geoipcity.dat, ' . jrCore_format_size($_files[0]['size']) . ', uploaded: ' . jrCore_format_time($_files[0]['time']));
    }

    // Form init
    $_tmp = array(
        'submit_value'     => 'save',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // City File
    $_tmp = array(
        'name'     => 'db_file',
        'label'    => 'GeoLite City File',
        'help'     => 'Select the downloaded GeoLite City Database file (GeoLiteCity.dat)',
        'text'     => 'Select GeoLiteCity.dat File',
        'type'     => 'file',
        'allowed'  => 'dat',
        'required' => true,
        'max'      => jrCore_get_max_allowed_upload(false)
    );
    jrCore_form_field_create($_tmp);

    // Make sure our config field is setup
    if (!isset($_conf['jrGeo_ip_file_time'])) {
        $_tmp = array(
            'name'     => 'ip_file_time',
            'default'  => '',
            'type'     => 'hidden',
            'required' => 'on',
            'validate' => 'number_nz',
            'label'    => 'IP file time',
            'help'     => 'this hidden field stores the time of the IP City DAT file uploaded - don\'t modify'
        );
        jrCore_register_setting('jrGeo', $_tmp);
    }
    jrCore_page_display();
}

//------------------------------
// database_save
//------------------------------
function view_jrGeo_database_save($_post, $_user, $_conf)
{
    // Masters only
    jrUser_master_only();
    jrCore_form_validate($_post);
    $_dat = jrCore_get_uploaded_media_files('jrGeo', 'db_file');
    if ($_dat && is_array($_dat) && isset($_dat[0]) && is_file($_dat[0])) {
        $tmp = time();
        if (!jrCore_write_media_file(0, "geoipcity-{$tmp}.dat", $_dat[0])) {
            jrCore_set_form_notice('error', 'Unable to save Geo Location data file - please try again');
            jrCore_form_result();
        }
        else {
            jrCore_set_setting_value('jrGeo', 'ip_file_time', $tmp);
            jrCore_delete_config_cache();
        }
        // Cleanup OLD entries
        $dir = jrCore_get_media_directory(0, FORCE_LOCAL);
        $_fl = glob("{$dir}/geoipcit*.dat");
        if ($_fl && is_array($_fl)) {
            foreach ($_fl as $file) {
                if (!strpos($file, "-{$tmp}")) {
                    unlink($file);
                }
            }
        }
    }
    jrCore_form_delete_session();
    jrCore_set_form_notice('success', 'The Geo Location Database file was successfully saved');
    jrCore_form_result();
}
