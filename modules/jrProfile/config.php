<?php
/**
 * Jamroom Profiles module
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
function jrProfile_config()
{
    // Default Signup Quota
    $_tmp = array(
        'name'     => 'default_quota_id',
        'type'     => 'select',
        'options'  => 'jrProfile_get_quotas',
        'default'  => '1',
        'validate' => 'number_nz',
        'label'    => 'default profile quota',
        'help'     => 'What Profile Quota should be the default for new users when they signup?',
        'section'  => 'signup settings',
        'order'    => 1
    );
    jrCore_register_setting('jrProfile', $_tmp);

    // Quota Changes
    $_tmp = array(
        'name'     => 'change',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'label'    => 'allow quota changes',
        'help'     => 'If you have multiple Signup Quotas, check this option to give your users the ability to change Profile Quotas to any other free quota.',
        'section'  => 'signup settings',
        'order'    => 2
    );
    jrCore_register_setting('jrProfile', $_tmp);

    // Self Delete
    $_tmp = array(
        'name'     => 'allow_delete',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'label'    => 'allow profile deletion',
        'help'     => 'If this option is checked, a user can delete their own profile',
        'section'  => 'profile options',
        'order'    => 3
    );
    jrCore_register_setting('jrProfile', $_tmp);

    // Show Help
    $_tmp = array(
        'name'     => 'show_help',
        'type'     => 'checkbox',
        'default'  => 'on',
        'upgrade'  => 'off',
        'validate' => 'onoff',
        'label'    => 'show profile help',
        'help'     => 'If this option is checked, and a profile owner views a section on their profile that has no items, a message will show to help them create an item',
        'section'  => 'profile options',
        'order'    => 4
    );
    jrCore_register_setting('jrProfile', $_tmp);

    return true;
}
