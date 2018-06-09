<?php
/**
 * Jamroom Payment Support module
 *
 * copyright 2018 Jamroom Team
 *
 * This Jamroom file is LICENSED SOFTWARE, and cannot be redistributed.
 *
 * This Source Code is subject to the terms of the Jamroom Network
 * Commercial License -  please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * Jamroom Team
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
function jrPayment_quota_config()
{
    // Payout Percent
    $_prc = array();
    foreach (range(0, 100) as $num) {
        $_prc[$num] = $num;
    }
    $_tmp = array(
        'name'     => 'payout_percent',
        'type'     => 'select',
        'validate' => 'number_nn',
        'label'    => 'Payout Percent',
        'help'     => "What percentage of each sale should be credited to the profile when a profile sells an item?",
        'default'  => '100',
        'options'  => $_prc,
        'section'  => 'payout options',
        'order'    => 10
    );
    jrProfile_register_quota_setting('jrPayment', $_tmp);

    // Include Tax
    $_tmp = array(
        'name'     => 'include_tax',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'label'    => 'Include Tax in Payout',
        'help'     => 'If checked any Tax collected as part of a purchase will be included in the Profile Payout so the profile can remit to their local taxing agency',
        'default'  => 'on',
        'section'  => 'payout options',
        'order'    => 11
    );
    jrProfile_register_quota_setting('jrPayment', $_tmp);

    // Include Shipping
    $_tmp = array(
        'name'     => 'include_shipping',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'label'    => 'Include Shipping in Payout',
        'help'     => 'If checked any Shipping collected as part of a purchase will be included in the Profile Payout.  If you are shipping on behalf of your profiles, uncheck this option',
        'default'  => 'on',
        'section'  => 'payout options',
        'order'    => 12
    );
    jrProfile_register_quota_setting('jrPayment', $_tmp);

    // Show Purchase Browser
    $_tmp = array(
        'name'     => 'show_purchases',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'label'    => 'Show Purchase Browser',
        'help'     => "If checked, users with profiles in this Quota will see the &quot;Purchases&quot; tab in their Account and Skin Menu",
        'default'  => 'on',
        'section'  => 'permissions',
        'order'    => 1
    );
    jrProfile_register_quota_setting('jrPayment', $_tmp);
    return true;
}
