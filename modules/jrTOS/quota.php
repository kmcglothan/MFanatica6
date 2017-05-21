<?php
/**
 * Jamroom Terms of Service module
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
 * quota_config
 */
function jrTOS_quota_config()
{
    // show_tos
    $_tmp = array(
        'name'     => 'show_tos',
        'default'  => '0',
        'type'     => 'select',
        'options'  => 'jrTOS_get_tos_pages',
        'required' => 'on',
        'label'    => 'display terms of service',
        'help'     => 'Select the Terms of Service pages you want to display to Users with profiles in this quota on login. The user will be required to agree to the displayed Terms of Service before continuing.<br><br>To create new Terms of Service Pages, simply create a new Page in the Page Creator module, and include the keyword &quot;terms&quot; in the title of the new Page.',
        'validate' => 'core_string',
        'order'    => 1
    );
    jrProfile_register_quota_setting('jrTOS', $_tmp);
    return true;
}
