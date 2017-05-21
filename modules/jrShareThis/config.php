<?php
/**
 * Jamroom ShareThis module
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
 * jrShareThis_config
 */
function jrShareThis_config()
{
    // Publisher ID
    $_tmp = array(
        'name'     => 'pub_key',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'publisher key',
        'help'     => 'This is your ShareThis Publisher Key found in your ShareThis dashboard',
        'order'    => 1
    );
    jrCore_register_setting('jrShareThis', $_tmp);

    // Style
    $_opt = array(
        'buttons_o' => 'Buttons Only',
        'buttons_h' => 'Buttons (horizontal counter)',
        'buttons_v' => 'Buttons (vertical counter)',
        'bar_left'  => 'Bar Left - does not work on SSL',
        'bar_right' => 'Bar Right - does not work on SSL'
    );
    $_tmp = array(
        'name'     => 'style',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 'buttons_o',
        'validate' => 'not_empty',
        'label'    => 'style',
        'help'     => 'Select the style that will be used for the ShareThis widget',
        'order'    => 2
    );
    jrCore_register_setting('jrShareThis', $_tmp);

    $_tmp = array(
        'name'     => 'copy_share',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'label'    => 'Enable Copy N Share',
        'help'     => 'If enabled, the Copy N Share option will be turned on, which adds a trackable extension to any URL that is copied from your site',
        'order'    => 3
    );
    jrCore_register_setting('jrShareThis', $_tmp);

    // Active Chicklets
    $_tmp = array(
        'name'     => 'chicklets',
        'type'     => 'optionlist',
        'options'  => 'jrShareThis_get_chicklets',
        'default'  => 'email,facebook,twitter,sharethis',
        'validate' => 'not_empty',
        'label'    => 'active services',
        'help'     => 'Select the services you would like to appear in the ShareThis section',
        'layout'   => 'columns',
        'columns'  => 2,
        'section'  => 'Service Config',
        'order'    => 5
    );
    jrCore_register_setting('jrShareThis', $_tmp);

    jrCore_delete_setting('jrShareThis', 'short_urls');
    return true;
}
