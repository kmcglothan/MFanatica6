<?php
/**
 * Jamroom PayPal Buy It Now module
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
 * quota_config
 */
function jrPayPal_quota_config()
{
    // Show My Downloads
    $_tmp = array(
        'name'     => 'show_downloads',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'label'    => 'Show Downloads',
        'help'     => "If checked, users with profiles in this Quota will see the &quot;Downloads&quot; tab in their User Account",
        'default'  => 'on',
        'section'  => 'permissions',
        'order'    => 5
    );
    jrProfile_register_quota_setting('jrPayPal', $_tmp);

    // Default Currency
    $_tmp = array(
        'name'     => 'default_currency',
        'type'     => 'select',
        'validate' => 'not_empty',
        'options'  => 'jrPayPal_get_currencies',
        'label'    => 'Default Currency',
        'help'     => "Select the default PayPal currency that will be used for sales to profiles in this quota",
        'default'  => 'USD',
        'section'  => 'currency',
        'order'    => 10
    );
    jrProfile_register_quota_setting('jrPayPal', $_tmp);

    // Allow Currency Selection
    $_tmp = array(
        'name'     => 'allow_change',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'label'    => 'Allow Currency Selection',
        'help'     => "If checked, users with profiles in this Quota will see a &quot;currency&quot; option in their profile settings where they can change the accepted currency to something different than the Quota default.",
        'default'  => 'off',
        'section'  => 'currency',
        'order'    => 15
    );
    jrProfile_register_quota_setting('jrPayPal', $_tmp);
    return true;
}
