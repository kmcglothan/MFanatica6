<?php
/**
 * Jamroom Meta Tag Manager module
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
function jrMeta_config()
{
    // Meta Tag names
    $_tmp = array(
        'name'     => 'names',
        'type'     => 'textarea',
        'default'  => jrMeta_get_valid_names_for_config(),
        'validate' => 'not_empty',
        'label'    => 'Meta Tag Names',
        'help'     => 'You can add custom Meta Tag names to this list by placing tags one per line'
    );
    jrCore_register_setting('jrMeta', $_tmp);

    // Meta Tag names
    $_tmp = array(
        'name'     => 'names',
        'type'     => 'textarea',
        'default'  => implode("\n", jrMeta_get_valid_names()),
        'validate' => 'not_empty',
        'label'    => 'Meta Tag Names',
        'help'     => 'You can add custom Meta Tag names to this list by placing tags one per line'
    );
    jrCore_register_setting('jrMeta', $_tmp);

    // Rules
    $_tmp = array(
        'name'     => 'tagset',
        'default'  => '',
        'type'     => 'hidden',
        'required' => 'on',
        'validate' => 'printable',
        'label'    => 'meta tag set',
        'help'     => 'this hidden field holds the configured meta tags for the Meta Tags module'
    );
    jrCore_register_setting('jrMeta', $_tmp);

    jrCore_delete_setting('jrMeta', 'active');

    return true;
}
