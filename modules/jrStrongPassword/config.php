<?php
/**
 * Jamroom Strong Password module
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
 * jrStrongPassword_config
 */
function jrStrongPassword_config()
{
    // Active
    $_tmp = array(
        'name'     => 'active',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'label'    => 'active',
        'help'     => 'Check this option to activate the Password policy',
        'order'    => 1
    );
    jrCore_register_setting('jrStrongPassword', $_tmp);

    // Minimum Password Length
    $_tmp = array(
        'name'     => 'length',
        'type'     => 'text',
        'default'  => '8',
        'validate' => 'number_nz',
        'label'    => 'minimum length',
        'help'     => 'Enter the minimum password length that will be accepted',
        'section'  => 'password policy',
        'min'      => 4,
        'max'      => 256,
        'order'    => 2
    );
    jrCore_register_setting('jrStrongPassword', $_tmp);

    // Lowercase
    $_tmp = array(
        'name'     => 'lowercase',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => 'require lowercase letter',
        'help'     => 'Check this option to require at least 1 <strong>lowercase</strong> letter in a password.',
        'section'  => 'password policy',
        'order'    => 3
    );
    jrCore_register_setting('jrStrongPassword', $_tmp);

    // Uppercase
    $_tmp = array(
        'name'     => 'uppercase',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => 'require uppercase letter',
        'help'     => 'Check this option to require at least 1 <strong>uppercase</strong> letter in a password.',
        'section'  => 'password policy',
        'order'    => 4
    );
    jrCore_register_setting('jrStrongPassword', $_tmp);

    // Symbol
    $_tmp = array(
        'name'     => 'symbol',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => 'require a symbol',
        'help'     => 'Check this option to require at least 1 <strong>symbol</strong> in a password.',
        'section'  => 'password policy',
        'order'    => 5
    );
    jrCore_register_setting('jrStrongPassword', $_tmp);

    // Number
    $_tmp = array(
        'name'     => 'number',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => 'require a number',
        'help'     => 'Check this option to require at least 1 <strong>number</strong> in a password.',
        'section'  => 'password policy',
        'order'    => 6
    );
    jrCore_register_setting('jrStrongPassword', $_tmp);
    return true;
}
