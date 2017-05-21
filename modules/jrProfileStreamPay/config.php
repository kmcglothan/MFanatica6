<?php
/**
 * Jamroom Stream Pay module
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
 * @copyright 2015 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * config
 */
function jrProfileStreamPay_config()
{
    $_tmp = array(
        'name'     => 'active',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'label'    => 'Stream Payments Active',
        'help'     => 'If this option is checked, then Profiles in Quotas that are configured for Stream Payments will be credited when a configured media item is streamed',
        'order'    => 1
    );
    jrCore_register_setting('jrProfileStreamPay', $_tmp);

    $_opt = array(
        1 => 'every day',
        2 => 'every 2 days',
        3 => 'every 3 days',
        4 => 'every 4 days',
        5 => 'every 5 days',
        6 => 'every 6 days',
        7 => 'every 7 days',
       14 => 'every 14 days',
       30 => 'every 30 days',
       60 => 'every 60 days',
       90 => 'every 90 days',
      180 => 'every 180 days',
      365 => 'every 365 days',
        0 => 'never'
    );
    $_tmp = array(
        'name'     => 'reset',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 7,
        'label'    => 'Stream Count Reset',
        'help'     => 'When a viewer streams a media item, how much time must elapse before another play of the same item will be counted as a new play (and crediting the profile)?',
        'order'    => 2
    );
    jrCore_register_setting('jrProfileStreamPay', $_tmp);

    return true;
}
