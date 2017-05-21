<?php
/**
 * Jamroom Merchandise module
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
function jrStore_quota_config() {

//    // Payout Percent
//    $_prc = array();
//    foreach (range(0,100) as $num) {
//        $_prc[$num] = $num;
//    }
//    $_tmp = array(
//        'name'     => 'payout_percent',
//        'type'     => 'select',
//        'validate' => 'number_nn',
//        'label'    => 'Payout Percent',
//        'help'     => "What percentage of each sale should be credited to the profile when a profile sells an item?",
//        'default'  => '100',
//        'options'  => $_prc
//    );
//    jrProfile_register_quota_setting('jrStore',$_tmp);

// todo perhaps a later feature?
//    $_tmp = array(
//        'name'     => 'per_item_service_charge',
//        'type'     => 'text',
//        'validate' => 'price',
//        'label'    => 'Per Item Service Charge',
//        'help'     => "Does the system take any flat fee as a service charge for processing the cart sale. Charged to each item sold.",
//        'default'  => '0.00'
//    );
//    jrProfile_register_quota_setting('jrStore',$_tmp);
//
//    $_tmp = array(
//        'name'     => 'cart_service_charge',
//        'type'     => 'text',
//        'validate' => 'price',
//        'label'    => 'Cart Service Charge',
//        'help'     => "Does the system take any flat fee as a service charge for processing the cart sale. Divided equally between items in the cart.",
//        'default'  => '0.00'
//    );
//    jrProfile_register_quota_setting('jrStore',$_tmp);

    return true;
}
