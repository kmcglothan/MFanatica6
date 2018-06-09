<?php
/**
 * Jamroom Item Ratings module
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
function jrRating_config()
{
    // Require Login
    $_tmp = array(
        'name'     => 'require_login',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => 'Require Login',
        'help'     => 'If checked, users must be logged in to rate items',
        'order'    => 2
    );
    jrCore_register_setting('jrRating', $_tmp);

    $_tmp = array(
        'name'     => 'allow_actions',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'label'    => 'Add to Timeline',
        'help'     => 'If checked, when logged in Users rate an item, an entry will be added to their Timeline',
        'order'    => 3
    );
    jrCore_register_setting('jrRating', $_tmp);

    $_tmp = array(
        'name'     => 're-rate_timeout',
        'type'     => 'text',
        'default'  => '0',
        'validate' => 'number_nn',
        'label'    => 'Rating Lock Timer',
        'help'     => 'Enter the timeout, in seconds, after which users will no longer be able to change their rating for an item.<br><br><b>Note:</b> Setting this to 0 (zero) will disable the Rating Lock Timer.',
        'order'    => 4
    );
    jrCore_register_setting('jrRating', $_tmp);

    // Check Modules
    $_tmp = array(
        'name'     => 'check_modules',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'only active modules',
        'help'     => 'Check this option to add an additional check that ensures Ratings only appear for modules that are currently Active. For large and active systems it is recommended to leave this option unchecked as it adds query overhead that can slow down listings.',
        'order'    => 5
    );
    jrCore_register_setting('jrRating', $_tmp);

    // We used to offer this by module - removed
    jrCore_delete_setting('jrRating', 're-rate_modules');
    jrCore_delete_setting('jrRating', 'default_type');

    return true;
}
