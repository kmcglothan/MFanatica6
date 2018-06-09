<?php
/**
 * Jamroom Proxima User module
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
 * config
 */
function jrProximaUser_config()
{
    $_tmp = array(
        'name'     => 'enabled',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'signups enabled',
        'help'     => 'Uncheck this option if you would like to disable user account creation',
        'order'    => 1
    );
    jrCore_register_setting('jrProximaUser', $_tmp);

    $_tmp = array(
        'name'     => 'sso',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'single user accounts',
        'help'     => 'If this option is checked, user accounts will apply to ALL configured apps (single sign on) - this allows a user account created in one app to be usable in another.  There is also a performance increase in using this option for all user API requests.',
        'order'    => 2
    );
    jrCore_register_setting('jrProximaUser', $_tmp);

    $_tmp = array(
        'name'     => 'include_profile',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'include profile info',
        'help'     => 'If this option is checked, GET requests to retrieve user information will also include the user\'s Profile information',
        'order'    => 3
    );
    jrCore_register_setting('jrProximaUser', $_tmp);

    // Extra Signup Fields
    $_opt = array(
        'none'  => 'No Extra Fields required',
        'user'  => 'Require unique &quot;user_name&quot; field',
        'email' => 'Require unique &quot;user_email&quot; field',
        'both'  => 'Require unique &quot;user_name&quot; and &quot;user_email&quot; fields'
    );
    $_tmp = array(
        'name'     => 'require_fields',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 'none',
        'validate' => 'not_empty',
        'required' => 'on',
        'label'    => 'extra signup fields',
        'help'     => 'Proxima User can require additional fields be submitted when a new user account is created - select the extra fields you would like to collect.',
        'order'    => 4
    );
    jrCore_register_setting('jrProximaUser', $_tmp);

    // Password Stretching
    $_opt = array();
    foreach (range(4, 12) as $k) {
        $_opt[$k] = $k;
    }
    $_tmp = array(
        'name'     => 'password_stretching',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 4,
        'validate' => 'number_nz',
        'required' => 'on',
        'label'    => 'password stretching',
        'help'     => 'Proxima User accounts uses a secure, bcrypt password hashing algorithm for storing user passwords.  These password hashes are &quot;stretched&quot; to ensure high computational complexity.<br><br><b>Higher Value:</b> - Slower hash generation speed but higher security.<br><b>Lower Value:</b> - Faster hash generation but lower hash strength.<br><br>This setting can be changed at any time - it will only affect new User Accounts created after the change is applied - existing accounts will continue to use the previous value.',
        'order'    => 5
    );
    jrCore_register_setting('jrProximaUser', $_tmp);

    return true;
}
