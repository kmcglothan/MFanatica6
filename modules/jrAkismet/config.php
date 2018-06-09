<?php
/**
 * Jamroom Spam Blocker module
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
 * config
 */
function jrAkismet_config()
{
    // Akismet enabled
    $_tmp = array(
        'name'     => 'enabled',
        'default'  => 'off',
        'type'     => 'checkbox',
        'required' => 'on',
        'validate' => 'onoff',
        'label'    => 'Akismet API Enabled',
        'help'     => 'If you would like to use the Akismet Blog Spam service for detecting Spam, enable this option and enter your Akismet API Key below',
        'section'  => 'Akismet Spam API',
        'order'    => 10
    );
    jrCore_register_setting('jrAkismet', $_tmp);
    // API Key
    $_tmp = array(
        'name'     => 'api_key',
        'default'  => '',
        'type'     => 'text',
        'required' => 'on',
        'validate' => 'not_empty',
        'label'    => 'Akismet API Key',
        'help'     => 'This is the Akismet API Key you received when you signed up for your Akismet account.<br><br>Signup for a free Akismet account at <a href="https://akismet.com">https://akismet.com</a>.',
        'section'  => 'Akismet Spam API',
        'order'    => 11
    );
    jrCore_register_setting('jrAkismet', $_tmp);

    // Probation
    $_opt = array(
        0     => 'Disabled',
        '.25' => '6 Hours',
        '.5'  => '12 Hours',
        1     => '1 Day',
        2     => '2 Days',
        3     => '3 Days',
        4     => '4 Days',
        5     => '5 Days',
        6     => '6 Days',
        7     => '7 Days',
        14    => '14 Days',
        28    => '28 Days'
    );
    $_tmp = array(
        'name'     => 'probation',
        'default'  => '0',
        'type'     => 'select',
        'options'  => $_opt,
        'required' => 'on',
        'validate' => 'number',
        'label'    => 'New User Probation',
        'help'     => 'To help prevent spam, new Users can be placed in <strong>Probation</strong> for a period of time during which their text submissions to the site will be analyzed for potential Spam.',
        'section'  => 'general settings',
        'order'    => 1
    );
    jrCore_register_setting('jrAkismet', $_tmp);

    $_tmp = array(
        'name'     => 'block_html',
        'default'  => 'on',
        'type'     => 'checkbox',
        'required' => 'on',
        'validate' => 'onoff',
        'label'    => 'Strip All HTML',
        'help'     => 'For User accounts that are in the probation period, you can strip all HTML from their text - this can help prevent spammers from posting URLs and HTML to your site.',
        'section'  => 'general settings',
        'order'    => 2
    );
    jrCore_register_setting('jrAkismet', $_tmp);

    // Report URLs
    $_opt = array(
        'ignore' => 'Do Nothing',
        'report' => 'Notify Admin Users',
        'active' => 'Set User Profile to Inactive and Notify Admin Users'
    );
    $_tmp = array(
        'name'     => 'report_urls',
        'default'  => 'ignore',
        'type'     => 'select',
        'options'  => $_opt,
        'required' => 'on',
        'validate' => 'not_empty',
        'label'    => 'Offsite URL Action',
        'help'     => 'If User in probation submits an offsite URL, what action would you like to take?  The URL will be stripped from the text if this option is set to <strong>Notify Admin Users</strong> or <strong>Set User Profile to Inactive and Notify Admin User</strong>.',
        'section'  => 'general settings',
        'order'    => 3
    );
    jrCore_register_setting('jrAkismet', $_tmp);
    return true;
}
