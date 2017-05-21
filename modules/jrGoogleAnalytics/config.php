<?php
/**
 * Jamroom Google Analytics module
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
function jrGoogleAnalytics_config()
{
    // Enabled
    $_tmp = array(
        'name'     => 'enabled',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'label'    => 'enable analytics',
        'help'     => 'If this option is checked, the Google Analytics tracking javascript will be inserted into pages on your site',
        'order'    => 1
    );
    jrCore_register_setting('jrGoogleAnalytics', $_tmp);

    // Enabled
    $_tmp = array(
        'name'     => 'ab_enabled',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'label'    => 'enable experiments',
        'help'     => 'If this option is checked, A/B testing support will be enabled for Google Analytics Experiments',
        'order'    => 2
    );
    jrCore_register_setting('jrGoogleAnalytics', $_tmp);

    // GA Account ID
    $_tmp = array(
        'name'     => 'account',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'account id',
        'help'     => 'Enter the Google Analytics account id you have setup for your site. eg: UV-12345678-2',
        'order'    => 3
    );
    jrCore_register_setting('jrGoogleAnalytics', $_tmp);

    // Analytics Type
    $_opt = array(
        'standard'  => 'Standard Analytics',
        'display'   => 'Display Network (Remarketing)',
        'universal' => 'Universal Analytics'
    );
    $_tmp = array(
        'name'     => 'type',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 'standard',
        'validate' => 'core_string',
        'label'    => 'enabled analytics',
        'help'     => 'Select the Analytics you would like to use:<br><br><strong>Standard Analytics</strong> - analytics code uses ga.js<br><strong>Display Network</strong> - analytics code uses dc.js (doubleclick)<br><strong>Universal Analytics</strong> - uses universal.js - do not enable unless you have migrated your account to Universal Analytics and have received confirmation that your account has been setup!',
        'order'    => 4
    );
    jrCore_register_setting('jrGoogleAnalytics', $_tmp);

    // GA Domain Name
    $_tmp = array(
        'name'     => 'domain',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'domain',
        'label'    => 'domain',
        'help'     => 'If you would like your analytics reporting to appear under a different domain than your installed domain, enter the domain here',
        'order'    => 5
    );
    jrCore_register_setting('jrGoogleAnalytics', $_tmp);

    // Exclude Admins
    $_tmp = array(
        'name'     => 'exclude_admins',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => 'exclude admins',
        'help'     => 'If this option is checked, then the Google Analytics tracking code will not be shown to Admin users (including Master admins)',
        'order'    => 6
    );
    jrCore_register_setting('jrGoogleAnalytics', $_tmp);

    // Delete old settings
    jrCore_delete_setting('jrGoogleAnalytics', 'enable_da');
    return true;
}

?>
