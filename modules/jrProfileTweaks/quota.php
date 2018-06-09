<?php
/**
 * Jamroom Profile Tweaks module
 *
 * copyright 2018 The Jamroom Network
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
 * quota_config
 */
function jrProfileTweaks_quota_config()
{
    global $_conf;

    // Allow Background image
    $_tmp = array(
        'name'     => 'allow_logo',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'label'    => 'Allow Custom Logo',
        'help'     => 'Allow users in this quota to upload a custom site logo that will appear in place of the main site logo.',
        'default'  => 'off',
        'section'  => 'options',
        'order'    => 7
    );
    jrProfile_register_quota_setting('jrProfileTweaks', $_tmp);

    // Allow Background image
    $_tmp = array(
        'name'     => 'allow_background_image',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'label'    => 'Allow Background Image',
        'help'     => 'Allow users in this quota to change their profile background image?',
        'default'  => 'on',
        'section'  => 'options',
        'order'    => 10
    );
    jrProfile_register_quota_setting('jrProfileTweaks', $_tmp);

    // Index Page
    $_tmp = array(
        'name'     => 'allow_index_redirect',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'label'    => 'Allow Custom Index',
        'help'     => 'Allow users in this quota to change their profile index page?',
        'default'  => 'on',
        'section'  => 'options',
        'order'    => 20
    );
    jrProfile_register_quota_setting('jrProfileTweaks', $_tmp);

    // Default Index Page
    $_tmp = array(
        'name'     => 'default_index',
        'type'     => 'select',
        'options'  => 'jrProfileTweaks_get_index_modules',
        'validate' => 'not_empty',
        'label'    => 'Default Profile Index',
        'help'     => 'What module index should be used as the default Profile index page?',
        'default'  => 'jrAction',
        'section'  => 'options',
        'order'    => 30
    );
    jrProfile_register_quota_setting('jrProfileTweaks', $_tmp);

    // Custom Skin
    $_tmp = array(
        'name'     => 'allow_skin',
        'type'     => 'optionlist',
        'options'  => 'jrProfileTweaks_get_skins',
        'validate' => 'printable',
        'label'    => 'Allowed Profiles Skins',
        'help'     => 'Allow Users in this Quota to select a different skin for their profile.  If any skin is checked here, the Profile will be allowed to set it as their Profile Skin',
        'default'  => 'off',
        'section'  => 'design',
        'layout'   => 'columns',
        'columns'  => 2,
        'order'    => 40,
    );
    jrProfile_register_quota_setting('jrProfileTweaks', $_tmp);

    // Default Skin
    $_tmp = array(
        'name'     => 'default_skin',
        'type'     => 'select',
        'validate' => 'not_empty',
        'label'    => 'Default Profile Skin',
        'help'     => 'Which skin should be the default skin for profiles in this quota',
        'options'  => 'jrProfileTweaks_get_skins',
        'default'  => $_conf['jrCore_active_skin'],
        'section'  => 'design',
        'order'    => 41
    );
    jrProfile_register_quota_setting('jrProfileTweaks', $_tmp);

    $_tmp = array("$('[id^=allow_skin_]').click(function() { jrProfileTweaks_default_skin_options() }); jrProfileTweaks_default_skin_options();");

    jrCore_create_page_element('javascript_ready_function', $_tmp);

    return true;
}
