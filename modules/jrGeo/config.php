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

/**
 * config
 */
function jrGeo_config()
{
    // Active
    $_opt = array(
        'local' => 'Uploaded Geo Location Database (default)',
        'api'   => 'MaxMind GeoIP Precision API (requires MaxMind account)'
    );
    $_tmp = array(
        'name'     => 'active',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 'local',
        'validate' => 'not_empty',
        'label'    => 'Active Service',
        'help'     => 'Select the GeoIP service that should be the active service for IP Address requests.',
        'order'    => 1
    );
    jrCore_register_setting('jrGeo', $_tmp);

    // User ID
    $_tmp = array(
        'name'     => 'user_id',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'MaxMind User ID',
        'help'     => 'This is your MaxMind User ID found in the &quot;My License Key&quot; section of your MaxMind Account',
        'order'    => 2
    );
    jrCore_register_setting('jrGeo', $_tmp);

    // License Key
    $_tmp = array(
        'name'     => 'license_key',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'MaxMind License Key',
        'help'     => 'This is your MaxMind License Key found in the &quot;My License Key&quot; section of your MaxMind Account',
        'order'    => 3
    );
    jrCore_register_setting('jrGeo', $_tmp);

    return true;
}
