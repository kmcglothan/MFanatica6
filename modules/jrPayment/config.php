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
 * config
 */
function jrPayment_config()
{
    global $_conf;
    // Active plugin
    $_tmp = array(
        'name'     => 'plugin',
        'type'     => 'select',
        'options'  => jrPayment_get_plugins(),
        'required' => 'on',
        'default'  => '',
        'validate' => 'core_string',
        'label'    => 'active payment processor',
        'sublabel' => 'configure in <a href="' . $_conf['jrCore_base_url'] . '/' . jrCore_get_module_url('jrPayment') . '/plugin_browser"><u>Plugin Config</u></a>',
        'help'     => 'Select the active payment processor plugin',
        'order'    => 1
    );
    jrCore_register_setting('jrPayment', $_tmp);

    // Payouts Clear
    $_prc = array(
        '0'  => 'immediately',
        '1'  => 'after 1 day',
        '2'  => 'after 2 days',
        '3'  => 'after 3 days',
        '4'  => 'after 4 days',
        '5'  => 'after 5 days',
        '6'  => 'after 6 days',
        '7'  => 'after 7 days',
        '10' => 'after 10 days',
        '14' => 'after 14 days',
        '21' => 'after 21 days',
        '28' => 'after 28 days',
        '30' => 'after 30 days',
        '60' => 'after 60 days',
        '90' => 'after 90 days'
    );
    $_tmp = array(
        'name'     => 'payout_clears',
        'type'     => 'select',
        'validate' => 'number_nn',
        'label'    => 'Profile Payouts Clear',
        'help'     => 'After receiving payment for an item, how many days does it take for the payment to &quot;clear&quot; and be available for payout to the seller profile?<br><br><b>NOTE:</b> To properly account for refunds it is recommended to set this option to a minimum of 30 days.',
        'default'  => '30',
        'options'  => $_prc,
        'order'    => 2
    );
    jrCore_register_setting('jrPayment', $_tmp);

    $_tmp = array(
        'name'     => 'cart_charge',
        'label'    => 'Cart Service Charge',
        'help'     => 'If you would like to add a set service charge <b>per cart</b>, enter it here in X.XX format - for example:<br><br>1.00 - 1 dollar service charge<br>0.35 - 35 cent service charge<br>etc.',
        'type'     => 'text',
        'default'  => '0.00',
        'validate' => 'price',
        'order'    => 3
    );
    jrCore_register_setting('jrPayment', $_tmp);

    $_tmp = array(
        'name'     => 'show_cart',
        'label'    => 'Show Cart in Menu',
        'help'     => 'If this option is checked, a &quot;Cart&quot; option will be added to the site menu for the user to view their cart.',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'order'    => 4
    );
    jrCore_register_setting('jrPayment', $_tmp);

    $_tmp = array(
        'name'     => 'show_clear',
        'label'    => 'Show Clear Cart option',
        'help'     => 'If this option is checked, a &quot;Clear Cart&quot; button will appear in the Cart to allow the entire cart to be cleared.',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'order'    => 5
    );
    jrCore_register_setting('jrPayment', $_tmp);

    $_tmp = array(
        'name'     => 'show_paypal',
        'label'    => 'Enable PayPal Checkout',
        'help'     => 'If this option is checked, and PayPal is NOT the active Payment Processor, users will be able to check out using PayPal in addition to the active Payment Processor.<br><br><b>NOTE:</b> PayPal must be properly configured in the Plugin Config section.  This feature only applies to the cart - subscription payments (if the Subscription module is active) always go through the active Payment Processor.',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'order'    => 6
    );
    jrCore_register_setting('jrPayment', $_tmp);

    return true;
}
