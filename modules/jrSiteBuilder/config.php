<?php
/**
 * Jamroom Site Builder module
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
function jrSiteBuilder_config()
{
    // Rules
    $_tmp = array(
        'name'     => 'enabled',
        'default'  => 'on',
        'type'     => 'checkbox',
        'required' => 'on',
        'validate' => 'onoff',
        'label'    => 'show site builder button',
        'help'     => 'If this option is checked, then Master Admins will see a &quot;Site Builder&quot; button on pages that can be edited in Site Builder',
        'order'    => 1
    );
    jrCore_register_setting('jrSiteBuilder', $_tmp);

    // Daily auto backups of .json file
    $_tmp = array(
        'name'     => 'backup',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Daily Backup',
        'help'     => 'A daily export of Site Builder widget configurations will be performed allowing you to restore your widget settings to a specific day.',
        'order'    => 2
    );
    jrCore_register_setting('jrSiteBuilder', $_tmp);

    // Storage period
    $_cnt = array(
        0  => 'Forever',
        3  => '3 Days',
        10 => '10 Days',
        30 => '30 Days (Default)',
        45 => '45 Days',
        90 => '90 Days',
    );

    $_tmp = array(
        'name'     => 'backup_retain_days',
        'default'  => '30',
        'type'     => 'select',
        'options'  => $_cnt,
        'required' => 'on',
        'validate' => 'number_nn',
        'label'    => 'Backup Days',
        'help'     => 'How many days of backups would you like to keep - any auto-created backup files older than what is selected here will be removed during nightly maintenance.',
        'order'    => 3
    );
    jrCore_register_setting('jrSiteBuilder', $_tmp);

    return true;
}
