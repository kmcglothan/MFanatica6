<?php
/**
 * Jamroom FoxyCart eCommerce module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
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
function jrFoxyCart_quota_config()
{
    global $_conf;

    // Show My Files
    $_tmp = array(
        'name'     => 'show_my_files',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'label'    => 'Show My Files',
        'help'     => "If checked, users with profiles in this Quota will see the &quot;My Files&quot; tab in their Account, and the Skin Menu (if enabled)",
        'default'  => 'on',
        'section'  => 'permissions',
        'order'    => 5
    );
    jrProfile_register_quota_setting('jrFoxyCart', $_tmp);

    // Show Subscription Browser
    $_tmp = array(
        'name'     => 'show_sb',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'label'    => 'Show Subscription Browser',
        'help'     => "If checked, users with profiles in this Quota will see the &quot;Subscriptions&quot; tab in their Account",
        'default'  => 'on',
        'section'  => 'permissions',
        'order'    => 6
    );
    jrProfile_register_quota_setting('jrFoxyCart', $_tmp);

    // Allow Sales
    $_tmp = array(
        'name'     => 'active',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'label'    => 'Allow Sales',
        'help'     => "If checked, profiles in this quota will be allowed to sell items from their profile.",
        'default'  => 'off',
        'section'  => 'permissions',
        'order'    => 7
    );
    jrProfile_register_quota_setting('jrFoxyCart', $_tmp);

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
        'section'  => 'permissions',
        'order'    => 8
    );
    jrProfile_register_quota_setting('jrFoxyCart', $_tmp);

    // Allow Subscriptions
    $_tmp = array(
        'name'     => 'subscription_allow',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'label'    => 'Allow Subscriptions',
        'help'     => "If checked, users will be able to purchase a subscription to this quota.",
        'default'  => 'off',
        'section'  => 'subscription',
        'order'    => 10
    );
    jrProfile_register_quota_setting('jrFoxyCart', $_tmp);

    // Subscription description
    $_tmp = array(
        'name'     => 'subscription_desc',
        'type'     => 'textarea',
        'validate' => 'allowed_html',
        'label'    => 'Subscription Info',
        'help'     => "Enter information about this subscription - it will be shown to the user in the subscription browser",
        'default'  => '',
        'section'  => 'subscription',
        'order'    => 10
    );
    jrProfile_register_quota_setting('jrFoxyCart', $_tmp);

    // Subscription price
    $_tmp = array(
        'name'     => 'subscription_price',
        'type'     => 'text',
        'validate' => 'price',
        'label'    => 'Subscription Price',
        'help'     => "What is the Price for subscribing to this quota?  This is the amount that will be charged whenever the subscription is renewed.",
        'default'  => '0.00',
        'section'  => 'subscription',
        'order'    => 10
    );
    jrProfile_register_quota_setting('jrFoxyCart', $_tmp);

    // Subscription length
    $_len = array(
        '0'   => 'No Trial Period',
        '1d'  => '1 Day',
        '2d'  => '2 Days',
        '3d'  => '3 Days',
        '4d'  => '4 Days',
        '5d'  => '5 Days',
        '6d'  => '6 Days',
        '1w'  => '1 Week',
        '2w'  => '2 Weeks',
        '3w'  => '3 Weeks',
        '1m'  => '1 Month',
        '2m'  => '2 Months',
        '3m'  => '3 Months',
        '4m'  => '4 Months',
        '5m'  => '5 Months',
        '6m'  => '6 Months',
        '7m'  => '7 Months',
        '8m'  => '8 Months',
        '9m'  => '9 Months',
        '10m' => '10 Months',
        '11m' => '11 Months',
        '1y'  => '1 Year'
    );
    $_tmp = array(
        'name'    => 'subscription_trial',
        'type'    => 'select',
        'label'   => 'Trial Length',
        'help'    => "If you would like to offer a Trial Period for this subscription, select it here",
        'default' => '0',
        'options' => $_len,
        'section' => 'subscription',
        'order'   => 10
    );
    jrProfile_register_quota_setting('jrFoxyCart', $_tmp);

    // Subscription length
    $_len = array(
        '1d'  => '1 Day',
        '2d'  => '2 Days',
        '3d'  => '3 Days',
        '4d'  => '4 Days',
        '5d'  => '5 Days',
        '6d'  => '6 Days',
        '1w'  => '1 Week',
        '2w'  => '2 Weeks',
        '3w'  => '3 Weeks',
        '1m'  => '1 Month',
        '2m'  => '2 Months',
        '3m'  => '3 Months',
        '4m'  => '4 Months',
        '5m'  => '5 Months',
        '6m'  => '6 Months',
        '7m'  => '7 Months',
        '8m'  => '8 Months',
        '9m'  => '9 Months',
        '10m' => '10 Months',
        '11m' => '11 Months',
        '1y'  => '1 Year',
        '2y'  => '2 Years',
        '3y'  => '3 Years',
        '4y'  => '4 Years',
        '5y'  => '5 Years'
    );
    $_tmp = array(
        'name'    => 'subscription_length',
        'type'    => 'select',
        'label'   => 'Subscription Length',
        'help'    => "What is the length of the Subscription period?",
        'default' => '1m',
        'options' => $_len,
        'section' => 'subscription',
        'order'   => 11
    );
    jrProfile_register_quota_setting('jrFoxyCart', $_tmp);

    // Subscription expiration Quota
    $_tmp = array(
        'name'     => 'expire_quota',
        'type'     => 'select',
        'options'  => 'jrProfile_get_quotas',
        'validate' => 'number_nn',
        'label'    => 'Expiration Quota',
        'help'     => 'When a subscription to this Quota expires, what Quota should the profile be moved to?',
        'default'  => $_conf['jrProfile_default_quota_id'],
        'section'  => 'subscription',
        'order'    => 12
    );
    jrProfile_register_quota_setting('jrFoxyCart', $_tmp);

    // Subscription expiration reminder
    /*
    $_tmp = array(
        'name'     => 'expire_notify_days',
        'type'     => 'text',
        'validate' => 'number_nn',
        'label'    => 'Expiration Notification',
        'help'     => "How many days <strong>before</strong> a subscription is set to expire should the user be notified of the pending expiration?  Set to 0 (zero) to disable.",
        'default'  => '0',
        'section'  => 'subscription',
        'order'    => 13
    );
    jrProfile_register_quota_setting('jrFoxyCart', $_tmp);
    */

    return true;
}
