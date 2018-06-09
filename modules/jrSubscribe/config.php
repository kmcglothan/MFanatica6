<?php
/**
 * Jamroom Subscriptions module
 *
 * copyright 2018 The Jamroom Network
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * config
 */
function jrSubscribe_config()
{
    $_opt = array(
        0 => 'Do not notify',
        1 => '1 day before subscription payment is due',
        2 => '2 days before subscription payment is due',
        3 => '3 days before subscription payment is due',
        4 => '4 days before subscription payment is due',
        5 => '5 days before subscription payment is due',
        6 => '6 days before subscription payment is due',
        7 => '7 days before subscription payment is due'
    );
    $_tmp = array(
        'name'     => 'upcoming_notify',
        'label'    => 'Notify of Payment',
        'help'     => 'Do you want subscription users to be notified by email of upcoming subscription payments?',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 0,
        'validate' => 'number_nn',
        'order'    => 1
    );
    jrCore_register_setting('jrSubscribe', $_tmp);

    $_opt = array(
        0 => 'Do not notify',
        1 => '1 day before subscription ends',
        2 => '2 days before subscription ends',
        3 => '3 days before subscription ends',
        4 => '4 days before subscription ends',
        5 => '5 days before subscription ends',
        6 => '6 days before subscription ends',
        7 => '7 days before subscription ends'
    );
    $_tmp = array(
        'name'     => 'cancel_notify',
        'label'    => 'Notify of Cancelation',
        'help'     => 'Do you want subscription users to be notified by email when their subscription is about to end?',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 0,
        'validate' => 'number_nn',
        'order'    => 2
    );
    jrCore_register_setting('jrSubscribe', $_tmp);

    $_tmp = array(
        'name'     => 'expire_notify',
        'label'    => 'Notify of Card Expiration',
        'help'     => 'Check this option to send users an email when their credit card is about to expire (for payment gateways that support it, such as FoxyCart and Stripe)',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'order'    => 3
    );
    jrCore_register_setting('jrSubscribe', $_tmp);
    return true;
}
