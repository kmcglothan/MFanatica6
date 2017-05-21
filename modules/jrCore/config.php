<?php
/**
 * Jamroom System Core module
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

/**
 * config
 */
function jrCore_config()
{
    // System Name
    $_tmp = array(
        'name'     => 'system_name',
        'default'  => $_SERVER['HTTP_HOST'],
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => 'on',
        'label'    => 'system name',
        'help'     => 'This is the name of your system.',
        'section'  => 'general',
        'order'    => 1
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Allowed Domains
    $_tmp = array(
        'name'     => 'allowed_domains',
        'default'  => '',
        'type'     => 'textarea',
        'validate' => 'false',
        'required' => 'off',
        'label'    => 'allowed domains',
        'help'     => 'All streams and downloads via media players are restricted to the local domain by default - you can enter additional domains (one per line) that you would like to allow access.<br><br><b>NOTE:</b> If you would like to allow streaming and downloading from any domain, enter <b>ALLOW_ALL_DOMAINS</b> as the value for this setting.',
        'section'  => 'general',
        'order'    => 2
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Log 404 Not Found
    $_tmp = array(
        'name'     => 'log_404',
        'default'  => 'off',
        'type'     => 'checkbox',
        'required' => 'on',
        'validate' => 'onoff',
        'label'    => 'log 404 not found',
        'help'     => 'Check this option to enable logging of 404 not found pages to the Activity Log.',
        'section'  => 'general',
        'order'    => 3
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Default Cache length
    $_tmp = array(
        'name'     => 'default_cache_seconds',
        'default'  => '300',
        'type'     => 'text',
        'required' => 'on',
        'min'      => 0,
        'max'      => 86400,
        'validate' => 'number_nn',
        'label'    => 'cache seconds',
        'help'     => 'Cached items will be cached for the number of seconds entered here. Set to "0" (zero) to disable template caching.<br><br><b>NOTE:</b> It is <b>highly recommended</b> that caching is enabled on a live system - disabling caching will have a significant performance impact.',
        'section'  => 'general',
        'order'    => 4
    );
    jrCore_register_setting('jrCore', $_tmp);

    $_opt = jrCore_get_cache_system_plugins();
    if ($_opt && is_array($_opt) && count($_opt) > 1) {
        $_tmp = array(
            'name'     => 'active_cache_system',
            'default'  => 'jrCore_mysql',
            'type'     => 'select',
            'options'  => $_opt,
            'validate' => 'core_string',
            'required' => 'on',
            'label'    => 'active cache system',
            'help'     => 'What Cache plugin should be the active cache system?',
            'section'  => 'general',
            'order'    => 5
        );
        jrCore_register_setting('jrCore', $_tmp);
    }
    else {
        // Active Cache System (hidden)
        $_tmp = array(
            'name'     => 'active_cache_system',
            'default'  => 'jrCore_mysql',
            'type'     => 'hidden',
            'required' => 'on',
            'validate' => 'not_empty',
            'label'    => 'active cache system',
            'help'     => 'this hidden field holds the name of the active caching sub system - do not modify by hand'
        );
        jrCore_register_setting('jrCore', $_tmp);
    }

    // Maintenance Mode
    $_tmp = array(
        'name'     => 'maintenance_mode',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'maintenance mode',
        'help'     => 'Enabling this option will place your system in Maintenance Mode.  While in maintenance mode, only admin users will be able to log into the site - all others will see the Maintenance Message.',
        'section'  => 'maintenance',
        'order'    => 20
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Maintenance Notice
    $_tmp = array(
        'name'     => 'maintenance_notice',
        'default'  => 'The System is currently undergoing system maintenance. We are working to get the system back online as soon as possible. Thank you for your patience.',
        'type'     => 'textarea',
        'validate' => 'false',
        'required' => 'off',
        'label'    => 'maintenance message',
        'help'     => 'If you have enabled &quot;Maintenance Mode&quot; you can enter a message that is shown on the login page.',
        'section'  => 'maintenance',
        'order'    => 21
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Server Time Zone
    $_tzs = array(
        'Kwajalein'              => 'GMT -12.00 - Kwajalein',
        'Pacific/Samoa'          => 'GMT -11:00 - Samoa',
        'Pacific/Honolulu'       => 'GMT -10:00 - Hawaii',
        'America/Anchorage'      => 'GMT -09:00 - Alaska',
        'America/Vancouver'      => 'GMT -08:00 - (PST) USA, Canada',
        'America/Denver'         => 'GMT -07:00 - (MST) USA, Canada',
        'America/Tegucigalpa'    => 'GMT -06:00 - (CST) USA, Canada, Mexico City',
        'America/New_York'       => 'GMT -05:00 - (EST) USA, Canada',
        'America/Caracas'        => 'GMT -04:30 - Caracas',
        'America/Halifax'        => 'GMT -04:00 - (AST) Canada',
        'America/St_Johns'       => 'GMT -03:30 - (NST) Newfoundland',
        'America/Sao_Paulo'      => 'GMT -03:00 - Brazil, Buenos Aires, Sao Paulo',
        'Atlantic/South_Georgia' => 'GMT -02:00 - South Georgia',
        'Atlantic/Azores'        => 'GMT -01:00 - Azores',
        'UTC'                    => 'UTC -00:00 - (UTC) Coordinated Universal Time',
        'Europe/Dublin'          => 'GMT -00:00 - (GMT) Lisbon, London, Dublin',
        'Europe/Belgrade'        => 'GMT +01:00 - Madrid, Paris, Belgrade',
        'Europe/Helsinki'        => 'GMT +02:00 - Cairo, Helsinki, South Africa',
        'Asia/Kuwait'            => 'GMT +03:00 - Baghdad, Moscow, St Petersburg',
        'Asia/Tehran'            => 'GMT +03:30 - Tehran',
        'Asia/Muscat'            => 'GMT +04:00 - Abu Dhabi, Tbilisi',
        'Asia/Kabul'             => 'GMT +04:30 - Kabul',
        'Asia/Yekaterinburg'     => 'GMT +05:00 - Karachi, Lahore, Tashkent',
        'Asia/Kolkata'           => 'GMT +05:30 - (IST) Bangalore, Mumbai, New Delhi',
        'Asia/Katmandu'          => 'GMT +05:45 - Katmandu',
        'Asia/Dhaka'             => 'GMT +06:00 - Dhaka',
        'Asia/Rangoon'           => 'GMT +06:30 - Yangon, Rangoon',
        'Asia/Krasnoyarsk'       => 'GMT +07:00 - Bangkok, Jakarta',
        'Asia/Brunei'            => 'GMT +08:00 - Beijing, Hong Kong, Singapore',
        'Asia/Seoul'             => 'GMT +09:00 - Osaka, Seoul, Tokyo',
        'Australia/Darwin'       => 'GMT +09:30 - Adelaide, Darwin',
        'Australia/Canberra'     => 'GMT +10:00 - Brisbane, Canberra, Vladivostok',
        'Asia/Magadan'           => 'GMT +11:00 - Sydney, Melboune',
        'Pacific/Fiji'           => 'GMT +12:00 - Auckland, Fiji',
        'Pacific/Tongatapu'      => 'GMT +13:00 - Tongatupu'
    );
    $_tmp = array(
        'name'     => 'system_timezone',
        'default'  => 'Europe/Dublin',
        'type'     => 'select',
        'options'  => $_tzs,
        'required' => 'on',
        'label'    => 'system time zone',
        'help'     => 'Setting the System Time Zone will adjust all of the output Date and Time stamps to the specified time zone, regardless of where your actual server may be physically located.  All Time Zones except UTC will be adjusted for Daylight Savings.',
        'section'  => 'date and time',
        'order'    => 10
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Hour Format
    $_hrs = array(
        '%I:%M:%S%p' => '12 hour format (with am/pm)',
        '%T'         => '24 hour format'
    );
    $_tmp = array(
        'name'     => 'hour_format',
        'default'  => '%I:%M:%S%p',
        'type'     => 'select',
        'options'  => $_hrs,
        'required' => 'on',
        'label'    => 'hour format',
        'help'     => 'This option allows you to define how Hours of the Day will be displayed.',
        'section'  => 'date and time',
        'order'    => 11
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Date Format
    $_edf = array(
        '%D'       => 'mm/dd/yy',
        '%d/%m/%y' => 'dd/mm/yy',
        '%d %b %Y' => 'dd mmm yyyy'
    );
    $_tmp = array(
        'name'     => 'date_format',
        'default'  => '%D',
        'type'     => 'select',
        'options'  => $_edf,
        'required' => 'on',
        'label'    => 'date format',
        'help'     => 'Select the Date Format to use for dates.',
        'section'  => 'date and time',
        'order'    => 12
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Pager Limit
    $_tmp = array(
        'name'     => 'pager_limit',
        'default'  => 'on',
        'type'     => 'checkbox',
        'required' => 'on',
        'validate' => 'onoff',
        'label'    => 'use optimized pager',
        'help'     => 'Checking this option will disable the Page Jumper on listing pages when the core determines the resulting SQL COUNT() query would slow down the system.<br><br>Leave this option enabled - disabling this on large systems can result in slow page generation.',
        'section'  => 'DataStore',
        'order'    => 31
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Pager Limit
    $_tmp = array(
        'name'     => 'optimize_order',
        'default'  => 'on',
        'type'     => 'checkbox',
        'required' => 'on',
        'validate' => 'onoff',
        'label'    => 'use optimized ordering',
        'help'     => 'Checking this option allows the core to determine if it can optimize the ordering of list items.<br><br>Leave this option enabled unless you feel the time-ordering of list items is incorrect (which can happen on imported items).',
        'section'  => 'DataStore',
        'order'    => 32
    );
    jrCore_register_setting('jrCore', $_tmp);

    $_tmp = array(
        'name'     => 'active_media_system',
        'default'  => 'jrCore_local',
        'type'     => 'select',
        'options'  => 'jrCore_get_media_system_plugins',
        'validate' => 'core_string',
        'required' => 'on',
        'label'    => 'active media system',
        'help'     => 'What Media system should be the active system for storing profile media items (images, audio, video, files, etc.)?',
        'section'  => 'media system',
        'order'    => 40
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Recycle Bin
    $_tmp = array(
        'name'     => 'recycle_bin',
        'default'  => 'on',
        'type'     => 'checkbox',
        'required' => 'on',
        'validate' => 'onoff',
        'label'    => 'enable recycle bin',
        'help'     => 'With this option checked when a module DataStore item (such as a Profile, a User, a Blog entry, etc.) is deleted it will be saved in the system Recycle Bin where it can be restored if needed.',
        'section'  => 'recycle bin',
        'order'    => 50
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Expired Items
    $_dys = array(
        '0'  => 'Never - Recycle Bin must be manually emptied',
        '1'  => '1 Day',
        '2'  => '2 Days',
        '3'  => '3 Days',
        '4'  => '4 Days',
        '5'  => '5 Days',
        '6'  => '6 Days',
        '7'  => '7 Days',
        '8'  => '8 Days',
        '9'  => '9 Days',
        '10' => '10 Days',
        '14' => '14 Days',
        '21' => '21 Days',
        '28' => '28 Days',
        '60' => '60 Days',
    );
    $_tmp = array(
        'name'     => 'recycle_bin_expire',
        'default'  => '3',
        'type'     => 'select',
        'options'  => $_dys,
        'valdiate' => 'number_nn',
        'required' => 'on',
        'label'    => 'delete after',
        'help'     => 'How many days after an item has been added to the Recycle Bin will it be permanently deleted?',
        'section'  => 'recycle bin',
        'order'    => 51
    );
    jrCore_register_setting('jrCore', $_tmp);

    //---------------------------------
    // Internal settings
    //---------------------------------

    // Active Skin (hidden)
    $_tmp = array(
        'name'     => 'active_skin',
        'default'  => 'jrElastic2',
        'type'     => 'hidden',
        'validate' => 'not_empty',
        'required' => 'on',
        'label'    => 'active skin',
        'help'     => 'this hidden field keeps track of the Active Skin.',
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Last Minute Run (hidden)
    $_tmp = array(
        'name'     => 'last_minute_maint_run',
        'default'  => '0',
        'type'     => 'hidden',
        'required' => 'on',
        'min'      => 1208010101,
        'max'      => 4012312323,
        'validate' => 'number_nn',
        'label'    => 'last minute maintenance run',
        'help'     => 'this hidden field keeps track of the last time minute maintenance was run - do not modify by hand.'
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Last Hourly Run (hidden)
    $_tmp = array(
        'name'     => 'last_hourly_maint_run',
        'default'  => '0',
        'type'     => 'hidden',
        'required' => 'on',
        'min'      => 2012080101,
        'max'      => 2099123123,
        'validate' => 'number_nn',
        'label'    => 'last hourly maintenance run',
        'help'     => 'this hidden field keeps track of the last time the hourly maintenance was run - do not modify by hand.'
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Last Daily Run (hidden)
    $_tmp = array(
        'name'     => 'last_daily_maint_run',
        'default'  => '0',
        'type'     => 'hidden',
        'required' => 'on',
        'min'      => 20120801,
        'max'      => 20991231,
        'validate' => 'number_nn',
        'label'    => 'last daily maintenance run',
        'help'     => 'this hidden field keeps track of the last time the daily maintenance was run - do not modify by hand.'
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Active DataStore (hidden)
    $_tmp = array(
        'name'     => 'active_datastore_system',
        'default'  => 'jrCore',
        'type'     => 'hidden',
        'required' => 'on',
        'validate' => 'not_empty',
        'label'    => 'active datastore system',
        'help'     => 'this hidden field holds the name of the active DataStore sub system - do not modify by hand'
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Queues Active (hidden)
    $_tmp = array(
        'name'     => 'queues_active',
        'default'  => 'on',
        'type'     => 'hidden',
        'required' => 'on',
        'validate' => 'onoff',
        'label'    => 'queues active',
        'help'     => 'this hidden field is used to activate and deactive the core queue system'
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Detail Feature Order (hidden)
    $_tmp = array(
        'name'     => 'detail_feature_order',
        'default'  => '',
        'type'     => 'hidden',
        'required' => 'on',
        'validate' => 'printable',
        'label'    => 'detail feature order',
        'help'     => 'this hidden field keeps track of the module detail feature order - do not modify or remove'
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Dashboard Config (hidden)
    $_tmp = array(
        'name'     => 'dashboard_config',
        'default'  => '',
        'type'     => 'hidden',
        'required' => 'on',
        'validate' => 'printable',
        'label'    => 'dashboard config',
        'help'     => 'this hidden field holds config options for the Dashboard - do not modify'
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Unique String
    $_tmp = array(
        'name'     => 'unique_string',
        'default'  => md5(microtime()),
        'type'     => 'hidden',
        'required' => 'on',
        'validate' => 'md5',
        'label'    => 'unique system string',
        'help'     => 'this hidden field holds a unique string value for use in hashing - do not modify'
    );
    jrCore_register_setting('jrCore', $_tmp);

    // Settings no longer used
    jrCore_delete_setting('jrCore', 'enable_precache');
    jrCore_delete_setting('jrCore', 'precache_workers');
    jrCore_delete_setting('jrCore', 'dls_adjust');

    return true;
}
