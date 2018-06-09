<?php
/**
 * Jamroom 5 MailChimp User Sync module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
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
function jrMailChimp_config()
{
    // API Key
    $_tmp = array(
        'name'     => 'active',
        'label'    => 'Active',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'help'     => 'Check this option to activate syncing user accounts with the MailChimp list configured below.',
        'order'    => 1
    );
    jrCore_register_setting('jrMailChimp', $_tmp);

    // API Key
    $_tmp = array(
        'name'     => 'api_key',
        'label'    => 'MailChimp API Key',
        'default'  => '',
        'type'     => 'text',
        'validate' => 'printable',
        'help'     => 'Enter your MailChimp API Key here - this can be found in your MailChimp control panel.',
        'order'    => 2
    );
    jrCore_register_setting('jrMailChimp', $_tmp);

    // List ID
    $_tmp = array(
        'name'     => 'list_id',
        'label'    => 'MailChimp List ID',
        'default'  => '',
        'type'     => 'text',
        'validate' => 'printable',
        'help'     => 'Enter the List ID of the MailChimp List that users will be added to.  This value can be found in the Settings -> List Names and Defaults section of your list.',
        'order'    => 3
    );
    jrCore_register_setting('jrMailChimp', $_tmp);

    return true;
}
