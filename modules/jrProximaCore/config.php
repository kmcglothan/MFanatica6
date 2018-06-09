<?php
/**
 * Jamroom Proxima Core module
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
function jrProximaCore_config()
{
    $_tmp = array(
        'name'     => 'require_ssl',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'require SSL for API',
        'sublabel' => 'SSL is required for security',
        'help'     => 'Proxima uses HTTP Basic Access Authentication for receiving the application key and user session key which sends both of these values from your clients in plain text.  Make sure when you are running live data through your site that you <strong>always use an SSL connection for any API requests.</strong> This will ensure the security of your user connections.',
        'order'    => 1
    );
    jrCore_register_setting('jrProximaCore', $_tmp);

    $_tmp = array(
        'name'     => 'enable_profiles',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Enable User Profiles',
        'help'     => 'By default User Profiles are enabled - if you would prefer to hide User Profiles on your site, uncheck this option.',
        'order'    => 10
    );
    jrCore_register_setting('jrProximaCore', $_tmp);

    $_tmp = array(
        'name'     => 'max_results',
        'type'     => 'text',
        'default'  => '100',
        'validate' => 'number_nz',
        'required' => 'on',
        'label'    => 'Max Search Results',
        'help'     => 'What is the maximum number of results that can be returned via an API search request?',
        'order'    => 20
    );
    jrCore_register_setting('jrProximaCore', $_tmp);

    $_tmp = array(
        'name'     => 'active_session_system',
        'type'     => 'select',
        'options'  => 'jrProximaCore_get_registered_session_systems',
        'default'  => 'jrProximaCore_mysql',
        'validate' => 'not_empty',
        'required' => 'on',
        'label'    => 'active session system',
        'help'     => 'Select the active session system for Proxima User sessions',
        'order'    => 30
    );
    jrCore_register_setting('jrProximaCore', $_tmp);

    // No longer used
    jrCore_delete_setting('jrProximaCore', 'active_cache_system');

    return true;
}

/**
 * Config Display
 * @param array $_post
 * @param array $_user
 * @param array $_conf
 */
function jrProximaCore_config_display($_post, $_user, $_conf)
{
    if (jrCore_get_server_protocol() != 'https') {
        jrCore_set_form_notice('error', 'Proxima uses HTTP Basic Access Authentication for client requests -<br><strong>all API requests should be made using an SSL connection!</strong><br>This will provide security for your user connections.', false);
    }
}
