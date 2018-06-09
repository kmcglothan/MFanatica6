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

/**
 * jrUser_config
 */
function jrUser_config()
{
    // Enable Signups
    $_tmp = array(
        'name'     => 'signup_on',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => 'User Signups',
        'help'     => 'Check this option to allow users to signup for your site.',
        'section'  => 'signup',
        'order'    => 1
    );
    jrCore_register_setting('jrUser', $_tmp);

    // Signup Notification
    $_tmp = array(
        'name'     => 'signup_notify',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => 'Signup Notification',
        'help'     => 'If this option is checked the system will notify Admins when a new User Account is created.',
        'section'  => 'signup',
        'order'    => 2
    );
    jrCore_register_setting('jrUser', $_tmp);

    // authenticate
    $_tmp = array(
        'name'     => 'authenticate',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => 'Re-Authenticate',
        'help'     => 'If this option is checked, when a user attempts to change their <strong>email address</strong> or <strong>password</strong> they will have to enter their existing password to continue.',
        'section'  => 'account',
        'order'    => 10
    );
    jrCore_register_setting('jrUser', $_tmp);

    // change notice
    $_tmp = array(
        'name'     => 'change_notice',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => 'change notification',
        'help'     => 'If this option is checked and a user changes their email address or password they will be sent a notification to their <strong>old</strong> email address letting them know that their account information has been changed.',
        'section'  => 'account',
        'order'    => 11
    );
    jrCore_register_setting('jrUser', $_tmp);

    // Default Language
    $_tmp = array(
        'name'     => 'default_language',
        'default'  => 'en-US',
        'type'     => 'select',
        'options'  => 'jrUser_get_languages',
        'required' => 'on',
        'label'    => 'default language',
        'help'     => 'The Default language is the language that is setup for new user accounts by default.',
        'section'  => 'account',
        'order'    => 12
    );
    jrCore_register_setting('jrUser', $_tmp);

    // Max Login Time
    $_tmp = array(
        'name'     => 'session_expire_min',
        'default'  => '360',
        'type'     => 'text',
        'validate' => 'number_nz',
        'required' => 'on',
        'min'      => 10,
        'max'      => 20160,
        'label'    => 'session expiration',
        'help'     => 'How many minutes of inactivity will cause a User session to be marked as expired?',
        'section'  => 'sessions',
        'order'    => 20
    );
    jrCore_register_setting('jrUser', $_tmp);

    // Auto Login
    $_als = array(
        '1'  => 'Every Login (auto login disabled)',
        '7'  => 'Every 7 days',
        '2'  => 'Every 14 days',
        '30' => 'Every 30 days',
        '60' => 'Every 60 days',
        '90' => 'Every 90 days',
        '3'  => 'Permanent (until user resets cookies)'
    );
    $_tmp = array(
        'name'     => 'autologin',
        'default'  => '2',
        'type'     => 'select',
        'options'  => $_als,
        'required' => 'on',
        'label'    => 'auto login reset',
        'help'     => 'How often should a user have to re-enter their login credentials? If the user does not visit the site for the number of days selected here, they will need to login again.',
        'section'  => 'sessions',
        'order'    => 21
    );
    jrCore_register_setting('jrUser', $_tmp);

    // change notice
    $_tmp = array(
        'name'     => 'bot_sessions',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'label'    => 'enable bot sessions',
        'help'     => 'If this option is checked, session tracking will be enable for Web Bots (such as Google bot, Bing bot, etc.). This allows the dashboard to display the number of bots online. Disable this to turn off bot sessions, which will use less system resources',
        'section'  => 'sessions',
        'order'    => 22
    );
    jrCore_register_setting('jrUser', $_tmp);

    $_opt = jrUser_get_session_system_plugins();
    if ($_opt && is_array($_opt) && count($_opt) > 1) {
        // Active Session System
        $_tmp = array(
            'name'     => 'active_session_system',
            'default'  => 'jrUser_mysql',
            'type'     => 'select',
            'options'  => $_opt,
            'validate' => 'core_string',
            'required' => 'on',
            'label'    => 'active session system',
            'help'     => 'What Session plugin should be the active session system?',
            'section'  => 'sessions',
            'order'    => 23
        );
        jrCore_register_setting('jrUser', $_tmp);
    }
    else {
        // Active Session System (hidden)
        $_tmp = array(
            'name'     => 'active_session_system',
            'default'  => 'jrUser_mysql',
            'type'     => 'hidden',
            'required' => 'on',
            'validate' => 'not_empty',
            'label'    => 'active session system',
            'help'     => 'This hidden field holds the name of the active session sub system - do not modify by hand'
        );
        jrCore_register_setting('jrUser', $_tmp);
    }

    // Enable SSL
    $_tmp = array(
        'name'     => 'force_ssl',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'label'    => 'Create SSL URLs',
        'sublabel' => 'SSL Certificate Required!',
        'help'     => 'Checking this option will cause local non-SSL URLs that are embedded in text items to be shown as an SSL url for logged in users',
        'section'  => 'site options',
        'order'    => 30
    );
    jrCore_register_setting('jrUser', $_tmp);

    // Site privacy options
    $_priv = array(
        '1' => 'Public (all pages visible)',
        '2' => 'Limited (site index and log in / signup only)',
        '3' => 'Private (no pages visible)'
    );
    $_tmp  = array(
        'name'     => 'site_privacy',
        'default'  => '1',
        'type'     => 'select',
        'options'  => $_priv,
        'required' => 'on',
        'label'    => 'Site Privacy',
        'help'     => 'Select which site pages visitors who are not logged in can see.<br><br><strong>NOTE:</strong> This setting only applies to users who are not logged in.',
        'section'  => 'site options',
        'order'    => 31
    );
    jrCore_register_setting('jrUser', $_tmp);

    // Login Note
    $_tmp = array(
        'name'     => 'login_note',
        'type'     => 'textarea',
        'validate' => 'allowed_html',
        'default'  => '',
        'required' => 'off',
        'label'    => 'login note',
        'sublabel' => 'HTML is allowed',
        'help'     => 'Enter a note that will be shown on the login page.',
        'section'  => 'notes',
        'order'    => 40
    );
    jrCore_register_setting('jrUser', $_tmp);

    // Signup Note
    $_tmp = array(
        'name'     => 'signup_note',
        'type'     => 'textarea',
        'validate' => 'allowed_html',
        'default'  => '',
        'required' => 'off',
        'label'    => 'signup note',
        'sublabel' => 'HTML is allowed',
        'help'     => 'Enter a note that will be shown on the signup page',
        'section'  => 'notes',
        'order'    => 41
    );
    jrCore_register_setting('jrUser', $_tmp);

    return true;
}

/**
 * Validate Config settings
 * @param $_post array Posted config values
 * @return mixed bool|array
 */
function jrUser_config_validate($_post)
{
    global $_conf;
    if (isset($_post['force_ssl']) && $_post['force_ssl'] == 'on') {
        // They are trying to enable SSL - see if SSL is actually enabled
        $ssl = str_replace('http:', 'https:', $_conf['jrCore_base_url']);
        $url = jrCore_get_module_url('jrUser');
        $tmp = jrCore_load_url("{$ssl}/{$url}/ssl_check", null, 'GET', 443, null, null, false, 5);
        if ($tmp != 'OK') {
            jrCore_set_form_notice('error', 'Your site does not appear to support SSL!<br>The &quot;Create SSL URLs&quot; option needs to be <b>unchecked</b>', false);
            return false;
        }
    }
    return $_post;
}
