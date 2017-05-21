<?php
/**
 * Jamroom Developer Tools module
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
function jrDeveloper_config()
{
    // Developer Mode
    $_tmp = array(
        'name'     => 'developer_mode',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'run in developer mode',
        'help'     => 'Enabling the &quot;Run in Developer Mode&quot; option will change the information displayed in the &quot;Info&quot; tab for a module to include information about Module Triggers and Listeners, as well as force all template and cache items to be built on every access.<br><br><b>Warning:</b> Enabling Developer Mode will make your system run much slower than normal!',
        'section'  => 'general settings',
        'order'    => 1
    );
    jrCore_register_setting('jrDeveloper', $_tmp);

    // Template Debug
    $_tmp = array(
        'name'     => 'template_debug',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'template name in source',
        'help'     => 'Enabling the &quot;Template Name in Source&quot; option will add an html comment with the name of the .tpl file START and END to help locate which html code comes from which template.',
        'section'  => 'general settings',
        'order'    => 2
    );
    jrCore_register_setting('jrDeveloper', $_tmp);

    // Query Debug
    $_tmp = array(
        'name'     => 'query_debug',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'URL detail in Queries',
        'help'     => 'Enabling the &quot;URL detail in Queries&quot; option will add a comment to SQL queries that includes the user and URL information - this can help track down issues with long queries.  Leave this option <b>disabled</b> unless it is needed.',
        'section'  => 'general settings',
        'order'    => 3
    );
    jrCore_register_setting('jrDeveloper', $_tmp);

    // Log Slow Queries
    $_opt = array(
        0     => 'Disabled',
        '.25' => '.25 Second',
        '.5'  => '.5 Second',
        1     => '1 Second'
    );
    foreach (range(2, 30) as $s) {
        $_opt[$s] = "{$s} Seconds";
    }
    $_tmp = array(
        'name'     => 'slow_queries',
        'default'  => 0,
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'number_nn',
        'required' => 'on',
        'label'    => 'slow query log',
        'help'     => 'Enable this option so jrCore_db_search_items queries that take longer than the configured seconds will be logged to the Activity Log',
        'section'  => 'general settings',
        'order'    => 4
    );
    jrCore_register_setting('jrDeveloper', $_tmp);

    // Developer Name
    $_tmp = array(
        'name'     => 'developer_name',
        'default'  => '',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => 'off',
        'label'    => 'developer name',
        'help'     => 'This is your Developer Name as registered on Jamroom.net.  It will be used when generating Licenses for modules and skins',
        'section'  => 'developer settings',
        'order'    => 5
    );
    jrCore_register_setting('jrDeveloper', $_tmp);

    // Developer Prefix
    $_tmp = array(
        'name'     => 'developer_prefix',
        'default'  => '',
        'type'     => 'text',
        'validate' => 'core_string',
        'required' => 'off',
        'label'    => 'developer prefix',
        'help'     => 'This is your Developer Prefix as registered on Jamroom.net.  Your developer prefix is your unique module and skin directory name &quot;prefix&quot; that only you will use for your modules and skins.<br><br><b>Example:</b> All modules and skins created by The Jamroom Network begin with &quot;jr&quot;.<br><br><b>NOTE:</b> You can set this value to &quot;xx&quot; to enable local module and skin packaging.',
        'section'  => 'developer settings',
        'order'    => 6
    );
    jrCore_register_setting('jrDeveloper', $_tmp);

    jrCore_delete_setting('jrDeveloper', 'loader_mode');
    return true;
}

/**
 * Make sure the Marketplace module is configured
 * @param $_post array
 * @param $_user array
 * @param $_conf array
 * @return bool
 */
function jrDeveloper_config_display($_post, $_user, $_conf)
{
    // Let's make sure this is a valid prefix
    if (!jrCore_module_is_active('jrMarket')) {
        jrCore_set_form_notice('error', 'The Marketplace module is not installed or active and is required!<br>Make sure you have installed and activated the Marketplace module', false);
    }
    return true;
}

/**
 * Validate Config settings
 * @param $_post array Posted config values
 * @return mixed bool|array
 */
function jrDeveloper_config_validate($_post)
{
    if (isset($_post['developer_prefix']) && strlen($_post['developer_prefix']) > 0 && $_post['developer_prefix'] != 'xx') {
        // Let's make sure this is a valid prefix
        if (!jrCore_module_is_active('jrMarket')) {
            jrCore_set_form_notice('error', 'The Marketplace module is not installed or active and is required!<br>Make sure you have installed and activated the Marketplace module', false);
            return false;
        }
        $_mkt = jrMarket_get_active_release_system();
        // Must have valid marketplace system id
        if (!isset($_mkt['system_code']) || !jrCore_checktype($_mkt['system_code'], 'md5')) {
            jrCore_set_form_notice('error', 'You must properly configure the Marketplace System<br>with the correct Marketplace Email and Marketplace System ID in Marketplace -> Tools -> Marketplace Systems', false);
            return false;
        }
        // Validate prefix
        $_si = array(
            'email'  => $_mkt['system_email'],
            'sysid'  => $_mkt['system_code'],
            'prefix' => $_post['developer_prefix']
        );
        $_rs = jrCore_load_url("{$_mkt['system_url']}/networkmarket/validate_prefix", $_si, 'POST');
        if (isset($_rs) && strpos($_rs, '{') === 0) {
            $_rs = json_decode($_rs, true);
            if (isset($_rs['error'])) {
                jrCore_set_form_notice('error', $_rs['error'], false);
                if (isset($_post["{$_rs['field']}"])) {
                    jrCore_form_field_hilight($_rs['field']);
                }
                return false;
            }
        }
        else {
            jrCore_set_form_notice('error', 'Unable to validate your developer identity with the Marketplace - please try again');
            return false;
        }
    }
    return $_post;
}
