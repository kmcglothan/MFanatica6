<?php
/**
 * Jamroom Beta Launch Page module
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
 * jrLaunch_config
 */
function jrLaunch_config()
{
    // Launch Active
    $_tmp = array(
        'name'     => 'launch_active',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'label'    => 'Launch Page Active',
        'help'     => 'Check this option to active the Launch Page for non-logged in users',
        'order'    => 1
    );
    jrCore_register_setting('jrLaunch',$_tmp);

    // Launch Title
    $_tmp = array(
        'name'     => 'launch_title',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'printable',
        'label'    => 'Title',
        'help'     => 'Enter the title for your Beta Launch page',
        'order'    => 2
    );
    jrCore_register_setting('jrLaunch',$_tmp);

    // Launch Consumer Secret
    $_tmp = array(
        'name'     => 'launch_description',
        'type'     => 'textarea',
        'default'  => '',
        'validate' => 'printable',
        'label'    => 'Description',
        'help'     => 'Enter a description for your Beta Launch page',
        'order'    => 3
    );
    jrCore_register_setting('jrLaunch',$_tmp);

    // Launch send rate is no longer used
    jrCore_delete_setting('jrLaunch', 'launch_send_rate');

    return true;
}
