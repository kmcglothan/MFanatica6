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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrFoxyCart_config
 */
function jrFoxyCart_config()
{
    // API Key
    $_tmp = array(
        'name'     => 'api_key',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'printable',
        'label'    => 'API Key',
        'help'     => 'This is your FoxyCart API Key - it is required to receive notifications from FoxyCart when an item is sold.  Your API Key can be found and generated in the FoxyCart control panel under <b>store</b> &raquo; <b>advanced</b> &raquo; <b>api key</b>.',
        'section'  => 'store config',
        'order'    => 1
    );
    jrCore_register_setting('jrFoxyCart', $_tmp);

    // Store Sub Domain
    $_tmp = array(
        'name'     => 'store_domain',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'url',
        'label'    => 'Store Sub Domain URL',
        'help'     => 'This is the Store URL for your FoxyCart store - this can be found in the FoxyCart Dashboard &quot;Currently Selected Store&quot section as <b>Domain</b>.',
        'section'  => 'store config',
        'order'    => 2
    );
    jrCore_register_setting('jrFoxyCart', $_tmp);

    // API Version
    $_api = array(
        '1.1' => '1.1',
        '2.0' => '2.0'
    );
    $_tmp = array(
        'name'     => 'api_version',
        'type'     => 'select',
        'options'  => $_api,
        'default'  => '1.1',
        'validate' => 'not_empty',
        'label'    => 'Store Version',
        'help'     => 'This is the version of API you are using in your FoxyCart account - make sure this is set to the SAME value as found in your FoxyCart Admin -> Store Version configuration option',
        'section'  => 'store config',
        'order'    => 3
    );
    jrCore_register_setting('jrFoxyCart', $_tmp);

    // Store Sub name url
    $_tmp = array(
        'name'     => 'store_name_url',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'url_name',
        'label'    => 'Store Sub Domain',
        'help'     => 'If your &quot;Store Sub Domain URL&quot; is <b>http://jr500.foxycart.com</b> then this value would be <b>jr500</b>. No spaces or non-url characters are allowed.',
        'section'  => 'store config',
        'order'    => 4
    );
    jrCore_register_setting('jrFoxyCart', $_tmp);

    // Store Payout Level
    $_tmp = array(
        'name'     => 'store_payout_level',
        'type'     => 'text',
        'validate' => 'price',
        'default'  => '10.00',
        'label'    => 'Payout Level',
        'help'     => 'If your members have sold greater than the amount entered here they will appear on the payout tool page and be included in the payout system.',
        'section'  => 'store config',
        'order'    => 5
    );
    jrCore_register_setting('jrFoxyCart', $_tmp);

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
        'label'    => 'Payment Clears',
        'help'     => 'After receiving payment for an item, how many days does it take for the payment to &quot;clear&quot; and be available for payout?',
        'default'  => '7',
        'options'  => $_prc,
        'section'  => 'store config',
        'order'    => 6
    );
    jrCore_register_setting('jrFoxyCart', $_tmp);

    // Store Profile
    $_tmp = array(
        'name'     => 'system_profile_id',
        'type'     => 'text',
        'validate' => 'number_nn',
        'default'  => '',
        'label'    => 'System Profile ID',
        'help'     => 'If you are selling subscriptions, enter the profile_id you would like to record subscription payment transactions to. Set to 0 to disable.',
        'section'  => 'store config',
        'order'    => 7
    );
    jrCore_register_setting('jrFoxyCart', $_tmp);

    $_cur = array(
        'ARS' => 'ARS - Argentine Peso',
        'AUD' => 'AUD - Australian Dollars',
        'BDT' => 'BDT - Bangladeshi Taka',
        'BOB' => 'BOB - Bolivian Boliviano',
        'BRL' => 'BRL - Brazilian Real',
        'GBP' => 'GBP - British Pounds Sterling',
        'BGN' => 'BGN - Bulgarian Lev',
        'KHR' => 'KHR - Cambodian Riel',
        'CAD' => 'CAD - Canadian Dollars',
        'CLP' => 'CLP - Chilean Peso',
        'COP' => 'COP - Colombia Peso',
        'CRC' => 'CRC - Costa Rican Colon',
        'HRK' => 'HRK - Croatia Kuna',
        'CZK' => 'CZK - Czech Koruny',
        'DKK' => 'DKK - Danish Kroner',
        'EGP' => 'EGP - Egyptian Pound',
        'EEK' => 'EEK - Estonia Kroon',
        'ETB' => 'ETB - Ethiopian Birr',
        'EUR' => 'EUR - Euros',
        'GTQ' => 'GTQ - Guatamala Quetzal',
        'HNL' => 'HNL - Honduras Lempira',
        'HKD' => 'HKD - Hong Kong Dollars',
        'HUF' => 'HUF - Hungarian Forints',
        'ISK' => 'ISK - Icelandic Króna',
        'INR' => 'INR - Indian Rupee',
        'IDR' => 'IDR - Indonesian Rupiah',
        'ILS' => 'ILS - Israeli Shekel',
        'JPY' => 'JPY - Japanese Yen',
        'KES' => 'KES - Kenyan Shilling',
        'KWD' => 'KWD - Kuwaiti Dinar',
        'LVL' => 'LVL - Latvian Lat',
        'LBP' => 'LBP - Lebanese Pound',
        'LTL' => 'LTL - Lithuanian Litas',
        'MYR' => 'MYR - Malaysian Ringgit',
        'MXN' => 'MXN - Mexican Pesos',
        'TWD' => 'TWD - New Taiwan Dollars',
        'NZD' => 'NZD - New Zealand Dollars',
        'NOK' => 'NOK - Norwegian Kroner',
        'PYG' => 'PYG - Paraguay Guarani',
        'PEN' => 'PEN - Peruvian New Sol',
        'PHP' => 'PHP - Philippine Pesos',
        'PLN' => 'PLN - Polish Zlotys',
        'RON' => 'RON - Romanian New Leu',
        'RUB' => 'RUB - Russian Ruble',
        'SGD' => 'SGD - Singapore Dollars',
        'ZAR' => 'ZAR - South African Rand',
        'KRW' => 'KRW - South Korean Won',
        'SEK' => 'SEK - Swedish Kronor',
        'CHF' => 'CHF - Swiss Francs',
        'THB' => 'THB - Thai Baht',
        'TRY' => 'TRY - Turkish Liras',
        'USD' => 'USD - U.S. Dollars',
        'UAH' => 'UAH - Ukranian Hryvna',
        'UYU' => 'UYU - Uruguayan Peso',
        'VEB' => 'VEB - Venezuelan Bolivar',
        'ZWD' => 'ZWD - Zimbabwe Dollar'
    );

    // Store Currency
    $_tmp = array(
        'name'     => 'store_currency',
        'type'     => 'select',
        'options'  => $_cur,
        'default'  => 'USD',
        'validate' => 'core_string',
        'label'    => 'store currency',
        'help'     => 'Select the currency you want to use on the site - this must be set the same as the Store Locale setting in your FoxyCart control panel.<br><br><strong>NOTE:</strong> The currency selected here must be supported by the Payment Processor you have select in your FoxyCart control panel - if you have questions about the status of a supported currency, contact FoxyCart for assistance.',
        'section'  => 'options',
        'order'    => 10
    );
    jrCore_register_setting('jrFoxyCart', $_tmp);

    // Use Popup Cart
    $_tmp = array(
        'name'     => 'add_to_cart_popup',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'default'  => 'on',
        'label'    => 'Add To Cart Popup',
        'help'     => 'If this option is checked, when a user adds an item to the cart it will popup their cart window. Uncheck this to have the cart item add indicator show instead.<br><br><b>NOTE:</b> This setting has no effect when using version 2+ of the API.',
        'section'  => 'options',
        'order'    => 11
    );
    jrCore_register_setting('jrFoxyCart', $_tmp);

    // Force login before checkout
    $_tmp = array(
        'name'     => 'force_login',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'default'  => 'off',
        'label'    => 'Force Login to Checkout',
        'help'     => 'If this option is checked, when a user who is not logged in clicks &quot;checkout&quot; in the shopping cart, they will need to create an account before proceeding. <br><br> If unchecked, their account will be created for them from the information they enter at checkout.',
        'section'  => 'options',
        'order'    => 12
    );
    jrCore_register_setting('jrFoxyCart', $_tmp);

    return true;
}
