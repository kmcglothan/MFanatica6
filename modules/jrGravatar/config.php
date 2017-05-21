<?php
/**
 * Jamroom Gravatar Images module
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
function jrGravatar_config()
{
    // Default Signup Quota
    $_tmp = array(
        'name'     => 'enabled',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => 'enable gravatar images',
        'help'     => 'If this option is checked and the user has not uploaded a custom user image, the URL for a Gravatar image will be used in it\'s place.',
        'order'    => 1
    );
    jrCore_register_setting('jrGravatar', $_tmp);

    // User for Profile
    $_tmp = array(
        'name'     => 'enable_profile',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => 'use as profile image',
        'help'     => 'If this option is checked and the profile has not uploaded a custom profile image, the URL for the user\'s Gravatar image will be used in it\'s place.',
        'order'    => 2
    );
    jrCore_register_setting('jrGravatar', $_tmp);

    // Local Cache
    $_tmp = array(
        'name'     => 'local_cache',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'label'    => 'use local cache',
        'help'     => 'If this option is checked, the gravatar images for Users and Profiles will be cached and served from the lcoal web server',
        'order'    => 3
    );
    jrCore_register_setting('jrGravatar', $_tmp);

    // Default image
    $_opt = array(
        'default'   => 'Default',
        'mm'        => 'Mystery Man',
        'identicon' => 'Identicon',
        'monsterid' => 'MonsterID',
        'wavatar'   => 'Wavatar',
        'retro'     => 'Retro',
        'blank'     => 'Blank'
    );
    $_tmp = array(
        'name'     => 'default_image',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 'default',
        'validate' => 'not_empty',
        'label'    => 'default image style',
        'help'     => 'If the user has not uploaded a Gravatar image, what style would you like to use for the default image?<br><br>View details here: <a href="https://secure.gravatar.com/site/implement/images/#default-image">https://secure.gravatar.com/site/implement/images/#default-image</a>',
        'order'    => 4
    );
    jrCore_register_setting('jrGravatar', $_tmp);

    // Rating
    $_opt = array(
        'g'  => 'Rated G',
        'pg' => 'Rated PG',
        'r'  => 'Rated R',
        'x'  => 'Rated X',
    );
    $_tmp = array(
        'name'     => 'rating',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 'g',
        'validate' => 'not_empty',
        'label'    => 'image rating',
        'help'     => 'Select the image rating you would like to have viewable on the site.<br><br>See <a href="https://secure.gravatar.com/site/implement/images/#rating">https://secure.gravatar.com/site/implement/images/#rating</a> for more information.',
        'order'    => 5
    );
    jrCore_register_setting('jrGravatar', $_tmp);

    return true;
}
