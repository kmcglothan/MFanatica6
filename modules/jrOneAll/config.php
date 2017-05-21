<?php
/**
 * Jamroom OneAll Social module
 *
 * copyright 2017 The Jamroom Network
 *
 * This Jamroom file is LICENSED SOFTWARE, and cannot be redistributed.
 *
 * This Source Code is subject to the terms of the Jamroom Network
 * Commercial License -  please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
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
function jrOneAll_config()
{
    global $_conf;

    // OneAll Public Key
    $_tmp = array(
        'name'     => 'public_key',
        'default'  => '',
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => 'on',
        'label'    => 'public key',
        'help'     => 'Enter the Public Key from your OneAll Dashboard',
        'order'    => 1
    );
    jrCore_register_setting('jrOneAll', $_tmp);

    // OneAll Private Key
    $_tmp = array(
        'name'     => 'private_key',
        'default'  => '',
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => 'on',
        'label'    => 'private key',
        'help'     => 'Enter the Private Key from your OneAll Dashboard',
        'order'    => 2
    );
    jrCore_register_setting('jrOneAll', $_tmp);

    // OneAll Domain
    $_tmp = array(
        'name'     => 'domain',
        'default'  => '',
        'type'     => 'text',
        'validate' => 'url',
        'required' => 'on',
        'label'    => 'API endpoint',
        'help'     => 'Enter the API Domain from the OneAll Dashboard. Should be in this format: https://jr500.api.oneall.com/ ',
        'order'    => 3
    );
    jrCore_register_setting('jrOneAll', $_tmp);

    // Sharing API
    $_tmp = array(
        'name'     => 'advanced',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Advanced Sharing API',
        'help'     => 'Check this option to use the Advanced Sharing API, which is reserved for OneAll customers that are NOT on the OneAll free plan',
        'order'    => 4
    );
    jrCore_register_setting('jrOneAll', $_tmp);

    // Social Options Only
    $_tmp = array(
        'name'     => 'require_social',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Social Options Only',
        'help'     => "If this option is checked, then users will only be able to signup or login to your site using OneAll options.",
        'order'    => 5
    );
    jrCore_register_setting('jrOneAll', $_tmp);

    // Social Signup Message
    $_tmp = array(
        'name'     => 'social_message',
        'default'  => '',
        'type'     => 'textarea',
        'validate' => 'allowed_html',
        'required' => 'off',
        'label'    => 'Social Options Message',
        'sublabel' => 'HTML is allowed',
        'help'     => "If you have enabled the &quot;Social Options Only&quot; setting, you can enter an optional message here that will be displayed to the user on the Login and Signup screens",
        'order'    => 6
    );
    jrCore_register_setting('jrOneAll', $_tmp);

    // Login and Signup Networks
    $_tmp = array(
        'name'     => 'social_networks',
        'default'  => 'facebook,twitter',
        'type'     => 'optionlist',
        'options'  => jrOneAll_get_provider_options(),
        'required' => 'off',
        'validate' => 'core_string',
        'label'    => 'active networks',
        'sublabel' => 'for login and signup',
        'help'     => 'Select the Social Networks you would like to allow your users to login and signup with.<br><br><b>NOTE:</b> Any Social Network selected here MUST be configured properly in your OneAll Dashboard:<br><br><a href="https://app.oneall.com/">https://app.oneall.com</a>',
        'section'  => 'login and signup networks',
        'layout'   => 'columns',
        'columns'  => 3,
        'order'    => 5
    );
    jrCore_register_setting('jrOneAll', $_tmp);

    // Sharing Networks
    $_opt = jrOneAll_get_sharing_providers();
    $_tmp = array(
        'name'     => 'sharing_networks',
        'default'  => implode(',', array_keys($_opt)),
        'type'     => 'optionlist',
        'options'  => $_opt,
        'required' => 'off',
        'validate' => 'core_string',
        'label'    => 'active networks',
        'sublabel' => 'for sharing content',
        'help'     => 'Select the Social Networks you would like to allow your users to share with.<br><br><b>NOTE:</b> Any Social Network selected here MUST be configured properly in your OneAll Dashboard:<br><br><a href="https://app.oneall.com/">https://app.oneall.com</a>',
        'section'  => 'sharing networks',
        'layout'   => 'columns',
        'columns'  => 1,
        'order'    => 6
    );
    jrCore_register_setting('jrOneAll', $_tmp);

    // Login Bypass Key
    $_tmp = array(
        'name'     => 'bypass_key',
        'default'  => jrCore_create_unique_string(6),
        'type'     => 'hidden',
        'required' => 'on',
        'validate' => 'not_empty',
        'label'    => 'unique login string',
        'help'     => 'this hidden field holds a unique string value for use in the admin login bypass - do not modify'
    );
    jrCore_register_setting('jrOneAll', $_tmp);

    return true;
}

/**
 * Display admin login URL
 * @param $_post array
 * @param $_user array
 * @param $_conf array
 * @return bool
 */
function jrOneAll_config_display($_post, $_user, $_conf)
{
    if (isset($_conf['jrOneAll_require_social']) && $_conf['jrOneAll_require_social'] == 'on') {
        $murl = jrCore_get_module_url('jrUser');
        jrCore_set_form_notice('error', "Special Login URL for admin users: <a href=\"{$_conf['jrCore_base_url']}/{$murl}/login/admin={$_conf['jrOneAll_bypass_key']}\" target=\"_blank\"><u>{$_conf['jrCore_base_url']}/{$murl}/login/admin={$_conf['jrOneAll_bypass_key']}</u></a>", false);
    }
    return true;
}
