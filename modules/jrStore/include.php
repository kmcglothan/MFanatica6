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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function jrStore_meta()
{
    $_tmp = array(
        'name'        => 'Merchandise',
        'url'         => 'store',
        'version'     => '1.0.10',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Add Physical item sales ability to Profiles',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/291/merchandise-store',
        'category'    => 'profiles',
        'requires'    => 'jrFoxyCart',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrStore_init()
{
    // Core support (set "Access Allowed" to be 'off' by default for the quotas)
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrStore', 'off');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrStore', true);
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrStore', true);
    jrCore_register_module_feature('jrCore', 'item_order_support', 'jrStore', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrStore', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrStore', 'update', 'item_action.tpl');

    // We have some small custom CSS for our update page
    jrCore_register_module_feature('jrCore', 'css', 'jrStore', 'jrStore.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrStore', 'jrStore.js');

    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrStore', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrStore', 'update');

    // notifications
    $_tmp = array(
        'label'    => 30, // 30 = 'new sale (store)'
        'help'     => 32, // 'When you make a sale of merchandise, that needs to be shipped, how do you want to be notified?';
        'function' => 'notifications_jrStore'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrStore', 'new_sale', $_tmp);
    $_tmp = array(
        'label'    => 31, // 31 = 'new order message (store)'
        'help'     => 33, // 'When a customer of yours sends you a message on an order they have purchased from you, how do you want to be notified?';
        'function' => 'notifications_jrStore'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrStore', 'new_order_message', $_tmp);

    //listeners
    jrCore_register_event_listener('jrFoxyCart', 'add_price_field', 'jrStore_add_price_field_listener');
    jrCore_register_event_listener('jrFoxyCart', 'my_items_row', 'jrStore_my_items_row_listener');
    jrCore_register_event_listener('jrFoxyCart', 'my_earnings_row', 'jrStore_my_earnings_row_listener');
    jrCore_register_event_listener('jrFoxyCart', 'purchase_recorded', 'jrStore_purchase_recorded_listener');
    jrCore_register_event_listener('jrFoxyCart', 'adding_item_to_purchase_history', 'jrStore_adding_item_to_purchase_history_listener'); //adjust the product_qty
    jrCore_register_event_listener('jrFoxyCart', 'cart_url', 'jrStore_cart_url_listener');

    // Create our image gallery on item get
    jrCore_register_event_listener('jrCore', 'db_get_item', 'jrStore_db_get_item_listener');
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrStore_db_search_items_listener');

    jrCore_register_module_feature('jrOneAll', 'shared_network_support', 'jrStore', 'create');
    jrCore_register_module_feature('jrOneAll', 'shared_network_support', 'jrStore', 'update');

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'jrStore', 'profile_jrStore_item_count', 19);

    // Profile tabs for Profile owners
    jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrStore', 'default', 19); // 19 = 'Products'
    jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrStore', 'sales', 34); // 34 = 'Merchandise Sales'
    jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrStore', 'settings', 35); // 35 = 'Store Settings'

    // We have fields that can be searched
    jrCore_register_module_feature('jrSearch', 'search_fields', 'jrStore', 'product_title,product_body,product_category', 19);

    return true;
}

/**
 * Expand attached images into useful array for templates
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrStore_db_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_is_view_request() && $_args['module'] == 'jrStore') {
        foreach ($_data as $k => $v) {
            if (strpos(' ' . $k, 'product_image') && strpos($k, '_time')) {
                if (!isset($_data['_product_images'])) {
                    $_data['_product_images'] = array();
                }
                $_data['_product_images'][] = substr($k, 0, strpos($k, '_time'));
            }
        }
        $_data['product_image_count'] = (isset($_data['_product_images'])) ? count($_data['_product_images']) : 0;
    }
    return $_data;
}

/**
 * Expand attached images into useful array for templates
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrStore_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrStore' && isset($_data['_items']) && is_array($_data['_items'])) {
        foreach ($_data['_items'] as $ik => $_item) {
            foreach ($_item as $k => $v) {
                if (strpos(' ' . $k, 'product_image') && strpos($k, '_time')) {
                    if (!isset($_data['_items'][$ik]['_product_images'])) {
                        $_data['_items'][$ik]['_product_images']       = array();
                        $_data['_items'][$ik]['product_image_primary'] = substr($k, 0, strpos($k, '_time'));
                    }
                    $_data['_items'][$ik]['_product_images'][] = substr($k, 0, strpos($k, '_time'));
                }
            }
            $_data['_items'][$ik]['product_image_count'] = (isset($_data['_items'][$ik]['_product_images'])) ? count($_data['_items'][$ik]['_product_images']) : 0;
            if (!isset($_data['_items'][$ik]['product_image_primary'])) {
                $_data['_items'][$ik]['product_image_primary'] = 'product_image'; // will show default image
            }
        }
    }
    return $_data;
}

/**
 * Return product_id field for forms
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrStore_add_price_field_listener($_data, $_user, $_conf, $_args, $event)
{
    // View => File Field
    $_data["jrStore/create"] = 'product';
    $_data["jrStore/update"] = 'product';
    return $_data;
}

/**
 * Get information about an item for FoxyCart
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrStore_my_items_row_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['purchase_module']) && $_args['purchase_module'] == 'jrStore') {
        $url               = jrCore_get_module_url('jrStore');
        $_data[2]['title'] = $_args['product_title'];
        $_data[5]['title'] = '<a href="' . $_conf['jrCore_base_url'] . '/' . $url . '/purchases/' . $_args['purchase_txn_id'] . '/' . $_args['purchase_seller_profile_id'] . '/communication">' . jrCore_page_button("a{$_args['_item_id']}", 'Delivery Status', '') . '</a>';
        unset($_data[6]);
    }
    return $_data;
}

/**
 * display the sale info to the seller of the item for FoxyCart
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrStore_my_earnings_row_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['purchase_module']) && $_args['purchase_module'] == 'jrStore') {
        $txn_id = (int) $_args['sale_txn_id'];
        //get the status of this item:
        $_status = jrStore_get_status($txn_id, $_user['user_active_profile_id']);

        $murl              = jrCore_get_module_url('jrStore');
        $_data[1]['title'] = $_args['product_title'] . ' (Status: ' . $_status . ')';
        $_data[3]['title'] = '<a class="form_button p3" type="button" href="' . $_conf['jrCore_base_url'] . '/' . $_user['profile_url'] . '/' . $murl . '/sales/' . $txn_id . '">' . $txn_id . '</a>';

    }
    return $_data;
}

function jrStore_cc2country($cc)
{
    $_cc = strtolower($cc);

    //countries
    $_countries['ad'] = 'Andorra';
    $_countries['ae'] = 'United Arab Emirates';
    $_countries['af'] = 'Afghanistan';
    $_countries['ag'] = 'Antigua and Barbuda';
    $_countries['ai'] = 'Anguilla';
    $_countries['al'] = 'Albania';
    $_countries['am'] = 'Armenia';
    $_countries['an'] = 'Netherlands Antilles';
    $_countries['ao'] = 'Angola';
    $_countries['aq'] = 'Antarctica';
    $_countries['ar'] = 'Argentina';
    $_countries['as'] = 'American Samoa';
    $_countries['at'] = 'Austria';
    $_countries['au'] = 'Australia';
    $_countries['aw'] = 'Aruba';
    $_countries['az'] = 'Azerbaijan';
    $_countries['ba'] = 'Bosnia and Herzegovina';
    $_countries['bb'] = 'Barbados';
    $_countries['bd'] = 'Bangladesh';
    $_countries['be'] = 'Belgium';
    $_countries['bf'] = 'Burkina Faso';
    $_countries['bg'] = 'Bulgaria';
    $_countries['bh'] = 'Bahrain';
    $_countries['bi'] = 'Burundi';
    $_countries['bj'] = 'Benin';
    $_countries['bm'] = 'Bermuda';
    $_countries['bn'] = 'Brunei Darussalam';
    $_countries['bo'] = 'Bolivia';
    $_countries['br'] = 'Brazil';
    $_countries['bs'] = 'Bahamas';
    $_countries['bt'] = 'Bhutan';
    $_countries['bv'] = 'Bouvet Island';
    $_countries['bw'] = 'Botswana';
    $_countries['by'] = 'Belarus';
    $_countries['bz'] = 'Beliza';
    $_countries['ca'] = 'Canada';
    $_countries['cc'] = 'Cocos (Keeling) Islands';
    $_countries['cf'] = 'Central African Republic';
    $_countries['cg'] = 'Congo';
    $_countries['ch'] = 'Switzerland';
    $_countries['ci'] = 'Cote DIvoire (Ivory Coast)';
    $_countries['ck'] = 'Cook Islands';
    $_countries['cl'] = 'Chile';
    $_countries['cm'] = 'Cameroon';
    $_countries['cn'] = 'China';
    $_countries['co'] = 'Colombia';
    $_countries['cr'] = 'Costa Rica';
    $_countries['cs'] = 'Czechoslovakia (former)';
    $_countries['cu'] = 'Cuba';
    $_countries['cv'] = 'Cape Verde';
    $_countries['cx'] = 'Christmas Island';
    $_countries['cy'] = 'Cyprus';
    $_countries['cz'] = 'Czech Republic';
    $_countries['de'] = 'Germany';
    $_countries['dj'] = 'Djibouti';
    $_countries['dk'] = 'Denmark';
    $_countries['dm'] = 'Dominica';
    $_countries['do'] = 'Dominican Republic';
    $_countries['dz'] = 'Algeria';
    $_countries['ec'] = 'Ecuador';
    $_countries['ee'] = 'Estonia';
    $_countries['eg'] = 'Egypt';
    $_countries['eh'] = 'Western Samoa';
    $_countries['er'] = 'Eritrea';
    $_countries['es'] = 'Spain';
    $_countries['et'] = 'Ethiopia';
    $_countries['fi'] = 'Finland';
    $_countries['fj'] = 'Fiji';
    $_countries['fk'] = 'Falkland Islands (Malvinas)';
    $_countries['fm'] = 'Micronesia';
    $_countries['fo'] = 'Faroe Islands';
    $_countries['fr'] = 'France';
    $_countries['fx'] = 'France (metropolitan)';
    $_countries['ga'] = 'Gabon';
    $_countries['gb'] = 'Great Britain';
    $_countries['gd'] = 'Grenada';
    $_countries['ge'] = 'Georgia';
    $_countries['gf'] = 'French Guiana';
    $_countries['gh'] = 'Ghana';
    $_countries['gi'] = 'Gibraltar';
    $_countries['gl'] = 'Greenland';
    $_countries['gm'] = 'Gambia';
    $_countries['gn'] = 'Guinea';
    $_countries['gp'] = 'Guadeloupe';
    $_countries['gq'] = 'Equatorial Guinea';
    $_countries['gr'] = 'Greece';
    $_countries['gs'] = 'St. Georgia & Sandwich Islands';
    $_countries['gt'] = 'Guatemala';
    $_countries['gu'] = 'Guam';
    $_countries['gw'] = 'Guinea-Bissau';
    $_countries['gy'] = 'Guyana';
    $_countries['hk'] = 'Hong Kong';
    $_countries['hm'] = 'Heard and McDonald Islands';
    $_countries['hn'] = 'Honduras';
    $_countries['hr'] = 'Croatia (Hrvatska)';
    $_countries['ht'] = 'Haiti';
    $_countries['hu'] = 'Hungary';
    $_countries['id'] = 'Indonesia';
    $_countries['ie'] = 'Ireland';
    $_countries['il'] = 'Israel';
    $_countries['in'] = 'India';
    $_countries['io'] = 'British Indian Ocean Territory';
    $_countries['iq'] = 'Iraq';
    $_countries['ir'] = 'Iran';
    $_countries['is'] = 'Iceland';
    $_countries['it'] = 'Italy';
    $_countries['jm'] = 'Jamaica';
    $_countries['jo'] = 'Jordan';
    $_countries['jp'] = 'Japan';
    $_countries['ke'] = 'Kenya';
    $_countries['kg'] = 'Kyrgystan';
    $_countries['kh'] = 'Cambodia';
    $_countries['ki'] = 'Kiribati';
    $_countries['km'] = 'Comoros';
    $_countries['kn'] = 'Saint Kitts and Nevis';
    $_countries['kp'] = 'Korea (North)';
    $_countries['kr'] = 'Korea (South)';
    $_countries['kw'] = 'Kuwait';
    $_countries['ky'] = 'Cayman Islands';
    $_countries['kz'] = 'Kazakhstan';
    $_countries['la'] = 'Laos';
    $_countries['lb'] = 'Lebanon';
    $_countries['lc'] = 'Saint Lucia';
    $_countries['li'] = 'Liechtenstein';
    $_countries['lk'] = 'Sri Lanka';
    $_countries['lr'] = 'Liberia';
    $_countries['ls'] = 'Lesotho';
    $_countries['lt'] = 'Lithuania';
    $_countries['lu'] = 'Luxembourg';
    $_countries['lv'] = 'Latvia';
    $_countries['ly'] = 'Libya';
    $_countries['ma'] = 'Morocco';
    $_countries['mc'] = 'Monaco';
    $_countries['md'] = 'Moldova';
    $_countries['mg'] = 'Madagascar';
    $_countries['mh'] = 'Marshall Islands';
    $_countries['mk'] = 'Macedonia';
    $_countries['ml'] = 'Mali';
    $_countries['mm'] = 'Myanmar';
    $_countries['mn'] = 'Mongolia';
    $_countries['mo'] = 'Macau';
    $_countries['mp'] = 'Northern Mariana Islands';
    $_countries['mq'] = 'Martinique';
    $_countries['mr'] = 'Mauritania';
    $_countries['ms'] = 'Montserrat';
    $_countries['mt'] = 'Malta';
    $_countries['mu'] = 'Mauritius';
    $_countries['mv'] = 'Maldives';
    $_countries['mw'] = 'Malawi';
    $_countries['mx'] = 'Mexico';
    $_countries['my'] = 'Malaysia';
    $_countries['mz'] = 'Mozambique';
    $_countries['na'] = 'Namibia';
    $_countries['nc'] = 'New Caledonia';
    $_countries['ne'] = 'Niger';
    $_countries['nf'] = 'Norfolk Island';
    $_countries['ng'] = 'Nigeria';
    $_countries['ni'] = 'Nicaragua';
    $_countries['nl'] = 'Netherlands';
    $_countries['no'] = 'Norway';
    $_countries['np'] = 'Nepal';
    $_countries['nr'] = 'Nauru';
    $_countries['nt'] = 'Neutral Zone';
    $_countries['nu'] = 'Niue';
    $_countries['nz'] = 'New Zealand';
    $_countries['om'] = 'Oman';
    $_countries['pa'] = 'Panama';
    $_countries['pe'] = 'Peru';
    $_countries['pf'] = 'French Polynesia';
    $_countries['pg'] = 'Papua New Guinea';
    $_countries['ph'] = 'Phillipines';
    $_countries['pk'] = 'Pakistan';
    $_countries['pl'] = 'Poland';
    $_countries['pm'] = 'St. Pierre and Miquelon';
    $_countries['pn'] = 'Pitcairn';
    $_countries['pr'] = 'Puerto Rico';
    $_countries['pt'] = 'Portugal';
    $_countries['pw'] = 'Palau';
    $_countries['py'] = 'Paraguay';
    $_countries['qa'] = 'Qatar';
    $_countries['re'] = 'Reunion';
    $_countries['ro'] = 'Romania';
    $_countries['ru'] = 'Russian Federation';
    $_countries['rw'] = 'Rwanda';
    $_countries['sa'] = 'Saudia Arabia';
    $_countries['sb'] = 'Solomon Islands';
    $_countries['sc'] = 'Seychelles';
    $_countries['sd'] = 'Sudan';
    $_countries['se'] = 'Sweden';
    $_countries['sg'] = 'Singapore';
    $_countries['sh'] = 'St. Helena';
    $_countries['si'] = 'Slovenia';
    $_countries['sj'] = 'Svalbard and Jan Mayen Islands';
    $_countries['sk'] = 'Romania';
    $_countries['sl'] = 'Slovak Republic';
    $_countries['sm'] = 'Sierra Leone';
    $_countries['sn'] = 'Senegal';
    $_countries['so'] = 'Somalia';
    $_countries['sr'] = 'Suriname';
    $_countries['st'] = 'Sao Tome and Principe';
    $_countries['su'] = 'USSR (former)';
    $_countries['sv'] = 'El Salvador';
    $_countries['sy'] = 'Syria';
    $_countries['sz'] = 'Swaziland';
    $_countries['tc'] = 'Turks and Caicos Islands';
    $_countries['td'] = 'Chad';
    $_countries['tf'] = 'French Southern Territories';
    $_countries['tg'] = 'Togo';
    $_countries['th'] = 'Thailand';
    $_countries['tj'] = 'Tajikistan';
    $_countries['tk'] = 'Tokelau';
    $_countries['tm'] = 'Turkmenistan';
    $_countries['tn'] = 'Tunisia';
    $_countries['to'] = 'Tonga';
    $_countries['tp'] = 'East Timor';
    $_countries['tr'] = 'Turkey';
    $_countries['tt'] = 'Trinidad and Tobago';
    $_countries['tv'] = 'Tuvalu';
    $_countries['tw'] = 'Taiwan';
    $_countries['tz'] = 'Tanzania';
    $_countries['ua'] = 'Ukraine';
    $_countries['ug'] = 'Uganda';
    $_countries['uk'] = 'United Kingdom';
    $_countries['um'] = 'US Minor Outlying Islands';
    $_countries['us'] = 'United States';
    $_countries['uy'] = 'Uruguay';
    $_countries['uz'] = 'Uzbekistan';
    $_countries['va'] = 'Vatican City State';
    $_countries['vc'] = 'Saint Vincent and the Grenadines';
    $_countries['ve'] = 'Venezuela';
    $_countries['vg'] = 'Virgin Islands (British)';
    $_countries['vi'] = 'Virgin Islands (U.S.)';
    $_countries['vn'] = 'Viet Nam';
    $_countries['vu'] = 'Vanuata';
    $_countries['wf'] = 'Wallis and Futuna Islands';
    $_countries['ws'] = 'Samoa';
    $_countries['ye'] = 'Yemen';
    $_countries['yt'] = 'Mayotte';
    $_countries['yu'] = 'Yugoslavia';
    $_countries['za'] = 'South Africa';
    $_countries['zm'] = 'Zambia';
    $_countries['zr'] = 'Zaire';
    $_countries['zw'] = 'Zimbabwe';

    $country = $_countries[$_cc];
    if (strlen($country) > 2) {
        return $country;
    }
    else {
        return $cc;
    }

}

/**
 * output a list of comments for this order
 * @param $params
 * @param $smarty
 * @return bool|string
 */
function smarty_function_jrStore_list($params, $smarty)
{
    global $_conf;
    switch ($params['mode']) {
        case 'comments':
            $_post['txn_id']            = $params['txn_id'];
            $_post['seller_profile_id'] = $params['seller_profile_id'];
            if (!function_exists('view_jrStore_view_comments')) {
                include_once $_conf['jrCore_base_dir'] . '/modules/jrStore/index.php';
            }
            return view_jrStore_view_comments($_post, array(), array());
            break;
    }
    return false;
}

/**
 * Takes the transaction and user_id and returns whether this user is involved in this transaction
 * @param $txn_id
 * @param $user_id
 * @param $seller_profile_id
 * @return bool|string  the 'buyer' or the 'seller'
 */
function jrStore_is_buyer_seller($txn_id, $user_id, $seller_profile_id)
{
    $txn_id = (int) $txn_id;
    $tbl    = jrCore_db_table_name('jrFoxyCart', 'purchase');
    $req    = "SELECT * FROM {$tbl}
                WHERE purchase_txn_id = '{$txn_id}'
                  AND purchase_module = 'jrStore'
                  AND purchase_seller_profile_id = '{$seller_profile_id}'";
    $_rt    = jrCore_db_query($req, 'SINGLE');
    if (isset($_rt) && is_array($_rt) && $_rt['purchase_txn_id'] == $txn_id) {
        //we have a purchase
        if ($_rt['purchase_user_id'] == $user_id && $user_id > 0) {
            return 'buyer';
        }
        //
        $item = jrCore_db_get_item('jrStore', $_rt['purchase_item_id']);
        if (isset($item) && is_array($item)) {
            if ($item['_profile_id'] == $seller_profile_id) {
                return 'seller';
            }
        }
    }
    return false; //neither the buyer or the seller.
}

/**
 * get the buyer or the sellers user id.
 * @param $to
 * @param $txn_id
 * @param $seller_profile_id
 * @return bool
 */
function jrStore_get_user_id($to, $txn_id, $seller_profile_id)
{
    $who          = '';
    $purchase_tbl = jrCore_db_table_name('jrFoxyCart', 'purchase');
    $req          = "SELECT purchase_user_id, purchase_item_id
                       FROM {$purchase_tbl}
                      WHERE purchase_txn_id = '{$txn_id}'
                        AND purchase_module = 'jrStore'
                        AND purchase_seller_profile_id = '{$seller_profile_id}'";
    $_rt          = jrCore_db_query($req, 'SINGLE');
    switch ($to) {
        case 'buyer':
            $who = 'buyer';
            //return the buyers user_id
            if ($_rt['purchase_user_id'] > 0) {
                return $_rt['purchase_user_id'];
            }
            break;
        case 'seller':
            $who = 'seller';
            //return the sellers user_id
            $item = jrCore_db_get_item('jrStore', $_rt['purchase_item_id']);
            if (isset($item) && is_array($item)) {
                if ($item['_user_id'] > 0) {
                    return $item['_user_id'];
                }
            }
            break;
    }
    jrCore_logger('CRI', 'tried to get the ' . $who . 's user id to send a mail, but failed.  could not send mail.');
    return false;
}

/**
 * fired when foxycart sends a confirmed order in the background back to our system.
 * If there are store items in the order, send a message out to the purchasers.
 * the pre-set messge that the store owner has on thier profile.
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return mixed
 */
function jrStore_purchase_recorded_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrStore') {
        //we sold a shippable product. send a thank you note and message.
        $_seller = jrCore_db_get_item('jrProfile', $_data['_profile_id']);
        if (isset($_seller['profile_jrStore_first_message']) && strlen($_seller['profile_jrStore_first_message']) > 5) {
            //send it out as a note
            $comment_text = $_seller['profile_jrStore_first_message'];
            $txn_id       = $_args['_txn']['txn_id'];

            //check that it hasn't already been sent for this txn
            $comment_tbl = jrCore_db_table_name('jrStore', 'comment');
            $req         = "SELECT *
                      FROM {$comment_tbl}
                      WHERE comment_txn_id = {$txn_id}
                        AND comment_seller_profile_id = '{$_seller['_profile_id']}' ";
            $_ct         = jrCore_db_query($req, 'COUNT');
            if ($_ct == 0) {
                //send the buyer the store note.
                jrStore_record_comment($_seller['_profile_id'], $comment_text, $txn_id, $_seller);

                //send the seller their new_sale note
                $from_uid = jrStore_get_user_id('buyer', $txn_id, $_seller['_profile_id']);
                $_buyer   = jrCore_db_get_item('jrUser', $from_uid);

                $murl = jrCore_get_module_url('jrStore');
                $_rp  = array(
                    'system_name'      => $_conf['jrCore_system_name'],
                    '_seller'          => $_seller,
                    '_buyer'           => $_buyer,
                    'sale_details_url' => $_conf['jrCore_base_url'] . '/' . $_seller['profile_url'] . '/' . $murl . '/sales/' . $txn_id,
                    'txn_id'           => $txn_id
                );
                list($sub, $msg) = jrCore_parse_email_templates('jrStore', 'new_sale', $_rp);
                // NOTE: "0" is from_user_id - 0 is the "system user"
                jrUser_notify($_seller['_user_id'], 0, 'jrStore', 'new_sale', $sub, $msg);

            }
        }
    }
    return $_data;
}

/**
 * used to write a comment to the sales communication timeline
 * @param $seller_profile_id
 * @param $comment_text
 * @param $txn_id
 * @param $_user
 * @return array
 */
function jrStore_record_comment($seller_profile_id, $comment_text, $txn_id, $_user)
{
    global $_conf;
    if (!jrCore_checktype($txn_id, 'number_nz')) {
        return array('error' => 'You\'re neither the buyer nor the seller, comment was not saved');
    }

    $_lang = jrUser_load_lang_strings();

    $user_is = jrStore_is_buyer_seller($txn_id, $_user['_user_id'], $seller_profile_id);
    if ($user_is == 'buyer' || $user_is == 'seller') {
        if (!isset($comment_text) || strlen($comment_text) === 0) {
            $_res = array('error' => $_lang['jrStore'][22]);
            return $_res;
        }
        if ($ban = jrCore_run_module_function('jrBanned_is_banned', 'word', $comment_text)) {
            $_res = array('error' => "{$_lang['jrCore'][67]} " . strip_tags($ban));
            return $_res;
        }

        $murl                   = jrCore_get_module_url('jrStore');
        $_tmp                   = array();
        $_tmp['comment_text']   = jrCore_db_escape(jrCore_strip_html(trim($comment_text)));
        $_tmp['comment_txn_id'] = $txn_id;
        $_tmp['comment_ip']     = jrCore_get_ip();

        $tbl = jrCore_db_table_name('jrStore', 'comment');
        $req = "INSERT INTO {$tbl} (comment_created, comment_user_id, comment_seller_profile_id, comment_txn_id, comment_text, comment_ip)
                VALUES (UNIX_TIMESTAMP(),'{$_user['_user_id']}','{$seller_profile_id}', '{$_tmp['comment_txn_id']}', '{$_tmp['comment_text']}', '{$_tmp['comment_ip']}') ";
        $aid = jrCore_db_query($req, 'INSERT_ID');
        if (!isset($aid)) {
            $_res = array('error' => $_lang['jrStore'][24]);
            return $_res;
        }
        // Next, notify users
        if ($user_is == 'buyer') {
            // send a note to the sellers
            $_owners   = jrProfile_get_owner_info($seller_profile_id);
            $direction = 'from buyer to seller';
            $from      = jrCore_db_get_item('jrUser', $_owners[0]['_user_id']);
            $reply_url = $_conf['jrCore_base_url'] . '/' . $from['profile_url'] . '/' . $murl . '/sales/' . $txn_id . '/communication';
        }
        else {
            // send a note to the buyer
            $_owners   = array(
                '_user_id' => jrStore_get_user_id('buyer', $txn_id, $seller_profile_id)
            );
            $direction = 'from seller to buyer';
            $reply_url = $_conf['jrCore_base_url'] . '/' . $murl . '/purchases/' . $txn_id . '/' . $seller_profile_id . '/communication';
        }
        $_rp = array(
            'system_name'       => $_conf['jrCore_system_name'],
            'comment_user_name' => $_user['user_name'],
            'comment_item_url'  => $reply_url,
            'comment_txn_id'    => $txn_id,
            'comment_text'      => $_tmp['comment_text']
        );
        list($sub, $msg) = jrCore_parse_email_templates('jrStore', 'new_order_message', $_rp);
        foreach ($_owners as $_o) {
            if ($_o['_user_id'] != $_user['_user_id']) {
                jrUser_notify($_o['_user_id'], 0, 'jrStore', 'new_order_message', $sub, $msg);
            }
        }
        jrCore_logger('INF', 'jrStore communication ' . $direction . ' on transaction ' . $txn_id);

        $_res = array('success' => $_lang['jrStore'][23]);
        return $_res;
    }
    jrCore_logger('MAJ', 'a user who was neither the buyer nor the seller tried to add a comment to a sale. blocked.');
    return array('error' => 'You\'re neither the buyer nor the seller, comment was not saved');
}

/**
 * for the /user/notifications page - switch on the notification
 * @param $_post
 * @param $_user
 * @param $_conf
 * @param $_args
 * @return bool
 */
function notifications_jrStore($_post, $_user, $_conf, $_args)
{
    switch ($_args['event']) {
        case 'new_sale':
        case 'new_order_message':
            if ($_user['quota_jrStore_allowed']) {
                return true; //TRUE, show the radio buttons
            }
            break;
    }
    return false;
}

/**
 * get the status of a deliverable item sold via the jrStore system.
 * This is set by the seller of the item as they deliver the goods.
 * @param $txn_id
 * @param $seller_profile_id
 * @return mixed
 */
function jrStore_get_status($txn_id, $seller_profile_id)
{
    // get the status of this item:
    $tbl = jrCore_db_table_name('jrStore', 'status');
    $req = "SELECT * FROM {$tbl} WHERE status_txn_id = '$txn_id' AND status_seller_profile_id = '{$seller_profile_id}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        return $_rt['status_status'];
    }
    return false;
}

/**
 * fired when foxycart sends a confirmed order in the background back to our system.
 * If there are store items in the order, we need to adjust the product_qty down by the amount sold
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return mixed
 */
function jrStore_adding_item_to_purchase_history_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrStore') {
        // see if the product has a product_qty
        if (isset($_args['product_quantity']) && jrCore_checktype($_args['product_quantity'], 'number_nn') && jrCore_checktype($_args['item_id'], 'number_nz')) {
            $qty = $_args['product_quantity'];
            //subtract qty from available.
            $_item = jrCore_db_get_item('jrStore', $_args['item_id']);
            if (isset($_item['product_qty']) && jrCore_checktype($_item['product_qty'], 'number_nz') && jrCore_checktype($qty, 'number_nz')) {
                //subtract the qty
                $new_qty           = $_item['product_qty'] - $qty;
                $_u['product_qty'] = ($new_qty <= 0) ? 0 : $new_qty;
                jrCore_db_update_item('jrStore', $_args['item_id'], $_u);
            }
        }

    }
    return $_data;
}

/**
 * returns a lis of Foxycart country codes.
 * @return array
 */
function jrStore_foxycart_country_codes()
{
    $cc       = array();
    $cc['AF'] = "Afghanistan";
    $cc['AX'] = "Åland Islands";
    $cc['AL'] = "Albania";
    $cc['DZ'] = "Algeria";
    $cc['AS'] = "American Samoa";
    $cc['AD'] = "Andorra";
    $cc['AO'] = "Angola";
    $cc['AI'] = "Anguilla";
    $cc['AQ'] = "Antarctica";
    $cc['AG'] = "Antigua and Barbuda";
    $cc['AR'] = "Argentina";
    $cc['AM'] = "Armenia";
    $cc['AW'] = "Aruba";
    $cc['AU'] = "Australia";
    $cc['AT'] = "Austria";
    $cc['AZ'] = "Azerbaijan";
    $cc['BS'] = "Bahamas";
    $cc['BH'] = "Bahrain";
    $cc['BD'] = "Bangladesh";
    $cc['BB'] = "Barbados";
    $cc['BY'] = "Belarus";
    $cc['BE'] = "Belgium";
    $cc['BZ'] = "Belize";
    $cc['BJ'] = "Benin";
    $cc['BM'] = "Bermuda";
    $cc['BT'] = "Bhutan";
    $cc['BO'] = "Bolivia";
    $cc['BQ'] = "Bonaire, Sint Eustatius and Saba";
    $cc['BA'] = "Bosnia and Herzegovina";
    $cc['BW'] = "Botswana";
    $cc['BV'] = "Bouvet Island";
    $cc['BR'] = "Brazil";
    $cc['IO'] = "British Indian Ocean Territory";
    $cc['BN'] = "Brunei Darussalam";
    $cc['BG'] = "Bulgaria";
    $cc['BF'] = "Burkina Faso";
    $cc['BI'] = "Burundi";
    $cc['KH'] = "Cambodia";
    $cc['CM'] = "Cameroon";
    $cc['CA'] = "Canada";
    $cc['CV'] = "Cape Verde";
    $cc['CW'] = "Curaçao";
    $cc['KY'] = "Cayman Islands";
    $cc['CF'] = "Central African Republic";
    $cc['TD'] = "Chad";
    $cc['CL'] = "Chile";
    $cc['CN'] = "China";
    $cc['CX'] = "Christmas Island";
    $cc['CC'] = "Cocos (Keeling) Islands";
    $cc['CO'] = "Colombia";
    $cc['KM'] = "Comoros";
    $cc['CG'] = "Congo";
    $cc['CD'] = "Congo, the Democratic Republic of the";
    $cc['CK'] = "Cook Islands";
    $cc['CR'] = "Costa Rica";
    $cc['CI'] = "Cote DIvoire";
    $cc['HR'] = "Croatia";
    $cc['CU'] = "Cuba";
    $cc['CY'] = "Cyprus";
    $cc['CZ'] = "Czech Republic";
    $cc['DK'] = "Denmark";
    $cc['DJ'] = "Djibouti";
    $cc['DM'] = "Dominica";
    $cc['DO'] = "Dominican Republic";
    $cc['EC'] = "Ecuador";
    $cc['EG'] = "Egypt";
    $cc['SV'] = "El Salvador";
    $cc['SX'] = "Sint Maarten";
    $cc['GQ'] = "Equatorial Guinea";
    $cc['ER'] = "Eritrea";
    $cc['EE'] = "Estonia";
    $cc['ET'] = "Ethiopia";
    $cc['FK'] = "Falkland Islands (Malvinas)";
    $cc['FO'] = "Faroe Islands";
    $cc['FJ'] = "Fiji";
    $cc['FI'] = "Finland";
    $cc['FR'] = "France";
    $cc['GF'] = "French Guiana";
    $cc['PF'] = "French Polynesia";
    $cc['TF'] = "French Southern Territories";
    $cc['GA'] = "Gabon";
    $cc['GM'] = "Gambia";
    $cc['GE'] = "Georgia";
    $cc['DE'] = "Germany";
    $cc['GH'] = "Ghana";
    $cc['GI'] = "Gibraltar";
    $cc['GR'] = "Greece";
    $cc['GL'] = "Greenland";
    $cc['GD'] = "Grenada";
    $cc['GP'] = "Guadeloupe";
    $cc['GU'] = "Guam";
    $cc['GT'] = "Guatemala";
    $cc['GG'] = "Guernsey";
    $cc['GN'] = "Guinea";
    $cc['GW'] = "Guinea-bissau";
    $cc['GY'] = "Guyana";
    $cc['HT'] = "Haiti";
    $cc['HM'] = "Heard Island and McDonald Island";
    $cc['VA'] = "Holy See (Vatican City State)";
    $cc['HN'] = "Honduras";
    $cc['HK'] = "Hong Kong";
    $cc['HU'] = "Hungary";
    $cc['IS'] = "Iceland";
    $cc['IN'] = "India";
    $cc['ID'] = "Indonesia";
    $cc['IR'] = "Iran, Islamic Republic of";
    $cc['IQ'] = "Iraq";
    $cc['IE'] = "Ireland";
    $cc['IM'] = "Isle of Man";
    $cc['IL'] = "Israel";
    $cc['IT'] = "Italy";
    $cc['JM'] = "Jamaica";
    $cc['JP'] = "Japan";
    $cc['JE'] = "Jersey";
    $cc['JO'] = "Jordan";
    $cc['KZ'] = "Kazakhstan";
    $cc['KE'] = "Kenya";
    $cc['KI'] = "Kiribati";
    $cc['KP'] = "Korea, Democratic People's Republic of";
    $cc['KR'] = "Korea, Republic of";
    $cc['KW'] = "Kuwait";
    $cc['KG'] = "Kyrgyzstan";
    $cc['LA'] = "Lao People's Democratic Republic";
    $cc['LV'] = "Latvia";
    $cc['LB'] = "Lebanon";
    $cc['LS'] = "Lesotho";
    $cc['LR'] = "Liberia";
    $cc['LY'] = "Libya";
    $cc['LI'] = "Liechtenstein";
    $cc['LT'] = "Lithuania";
    $cc['LU'] = "Luxembourg";
    $cc['MO'] = "Macau Special Administrative Region of China";
    $cc['MK'] = "Macedonia, The Former Yugoslav Republic of";
    $cc['MG'] = "Madagascar";
    $cc['MW'] = "Malawi";
    $cc['MY'] = "Malaysia";
    $cc['MV'] = "Maldives";
    $cc['ML'] = "Mali";
    $cc['MT'] = "Malta";
    $cc['MH'] = "Marshall Islands";
    $cc['MQ'] = "Martinique";
    $cc['MR'] = "Mauritania";
    $cc['MU'] = "Mauritius";
    $cc['YT'] = "Mayotte";
    $cc['MX'] = "Mexico";
    $cc['FM'] = "Micronesia, Federated States of";
    $cc['MD'] = "Moldova, Republic of";
    $cc['MC'] = "Monaco";
    $cc['MN'] = "Mongolia";
    $cc['ME'] = "Montenegro";
    $cc['MS'] = "Montserrat";
    $cc['MA'] = "Morocco";
    $cc['MZ'] = "Mozambique";
    $cc['MM'] = "Myanmar";
    $cc['NA'] = "Namibia";
    $cc['NR'] = "Nauru";
    $cc['NP'] = "Nepal";
    $cc['NL'] = "Netherlands";
    $cc['NC'] = "New Caledonia";
    $cc['NZ'] = "New Zealand";
    $cc['NI'] = "Nicaragua";
    $cc['NE'] = "Niger";
    $cc['NG'] = "Nigeria";
    $cc['NU'] = "Niue";
    $cc['NF'] = "Norfolk Island";
    $cc['MP'] = "Northern Mariana Islands";
    $cc['NO'] = "Norway";
    $cc['OM'] = "Oman";
    $cc['PK'] = "Pakistan";
    $cc['PW'] = "Palau";
    $cc['PS'] = "Palestine, State of";
    $cc['PA'] = "Panama";
    $cc['PG'] = "Papua New Guinea";
    $cc['PY'] = "Paraguay";
    $cc['PE'] = "Peru";
    $cc['PH'] = "Philippines";
    $cc['PN'] = "Pitcairn";
    $cc['PL'] = "Poland";
    $cc['PT'] = "Portugal";
    $cc['PR'] = "Puerto Rico";
    $cc['QA'] = "Qatar";
    $cc['RE'] = "Réunion";
    $cc['RO'] = "Romania";
    $cc['RU'] = "Russian Federation";
    $cc['RW'] = "Rwanda";
    $cc['BL'] = "Saint Barthélemy";
    $cc['SH'] = "Saint Helena, Ascension and Tristan da Cunha";
    $cc['KN'] = "Saint Kitts and Nevis";
    $cc['LC'] = "Saint Lucia";
    $cc['MF'] = "Saint Martin";
    $cc['PM'] = "Saint Pierre and Miquelon";
    $cc['VC'] = "Saint Vincent and the Grenadines";
    $cc['WS'] = "Samoa";
    $cc['SM'] = "San Marino";
    $cc['SS'] = "South Sudan";
    $cc['ST'] = "Sao Tome and Principe";
    $cc['SA'] = "Saudi Arabia";
    $cc['SN'] = "Senegal";
    $cc['RS'] = "Serbia";
    $cc['SC'] = "Seychelles";
    $cc['SL'] = "Sierra Leone";
    $cc['SG'] = "Singapore";
    $cc['SK'] = "Slovakia";
    $cc['SI'] = "Slovenia";
    $cc['SB'] = "Solomon Islands";
    $cc['SO'] = "Somalia";
    $cc['ZA'] = "South Africa";
    $cc['GS'] = "South Georgia and the South Sandwich Islands";
    $cc['ES'] = "Spain";
    $cc['LK'] = "Sri Lanka";
    $cc['SD'] = "Sudan";
    $cc['SR'] = "Suriname";
    $cc['SJ'] = "Svalbard and Jan Mayen";
    $cc['SZ'] = "Swaziland";
    $cc['SE'] = "Sweden";
    $cc['CH'] = "Switzerland";
    $cc['SY'] = "Syrian Arab Republic";
    $cc['TW'] = "Taiwan";
    $cc['TJ'] = "Tajikistan";
    $cc['TZ'] = "Tanzania, United Republic of";
    $cc['TH'] = "Thailand";
    $cc['TL'] = "Timor-Leste";
    $cc['TG'] = "Togo";
    $cc['TK'] = "Tokelau";
    $cc['TO'] = "Tonga";
    $cc['TT'] = "Trinidad and Tobago";
    $cc['TN'] = "Tunisia";
    $cc['TR'] = "Turkey";
    $cc['TM'] = "Turkmenistan";
    $cc['TC'] = "Turks and Caicos Islands";
    $cc['TV'] = "Tuvalu";
    $cc['UG'] = "Uganda";
    $cc['UA'] = "Ukraine";
    $cc['AE'] = "United Arab Emirates";
    $cc['GB'] = "United Kingdom";
    $cc['US'] = "United States";
    $cc['UM'] = "United States Minor Outlying Islands";
    $cc['UY'] = "Uruguay";
    $cc['UZ'] = "Uzbekistan";
    $cc['VU'] = "Vanuatu";
    $cc['VE'] = "Venezuela";
    $cc['VN'] = "Vietnam";
    $cc['VG'] = "Virgin Islands, British";
    $cc['VI'] = "Virgin Islands, U.S.";
    $cc['WF'] = "Wallis and Futuna Islands";
    $cc['EH'] = "Western Sahara";
    $cc['YE'] = "Yemen";
    $cc['ZM'] = "Zambia";
    $cc['ZW'] = "Zimbabwe";
    return $cc;
}

/**
 * add shipping info to the add_to_cart button url.
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrStore_cart_url_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrStore') {
        //domestic
        if (isset($_args['item']['product_ship_domestic']) && $_args['item']['product_ship_domestic'] > 0) {
            $_data['domestic_shipping'] = $_args['item']['product_ship_domestic'];
        }
        else {
            $_data['domestic_shipping'] = '0.00';
        }
        //international
        if (isset($_args['item']['product_ship_international']) && $_args['item']['product_ship_international'] > 0) {
            $_data['international_shipping'] = $_args['item']['product_ship_international'];
        }
        else {
            $_data['international_shipping'] = '0.00';
        }
        //ships from
        $shipping_country = jrCore_db_get_item_key('jrProfile', $_args['item']['_profile_id'], 'profile_jrStore_store_country');
        if (isset($shipping_country) && (strlen($shipping_country) == 2)) {
            $_data['ships_from'] = $shipping_country;
        }
        else {
            $_data['ships_from'] = 'US';
        }
    }
    return $_data;
}
