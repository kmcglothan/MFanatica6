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
 * meta
 */
function jrPayPal_meta()
{
    $_tmp = array(
        'name'        => 'PayPal Buy It Now',
        'url'         => 'paypal',
        'version'     => '1.1.4',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Profile sales of audio, video and file items via a PayPal Buy It Now button',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/1987/paypal-buy-it-now',
        'category'    => 'ecommerce',
        'requires'    => 'jrCore:6.0.4',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrPayPal_init()
{
    // Core Quota support
    $_tmp = array(
        'label' => 'Enable Buy Now',
        'help'  => 'If checked, users in this quota will have a price field in their audio, video and file forms that will let them set a price for the item.',
    );
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrPayPal', 'on', $_tmp);

    // Register our CSS
    jrCore_register_module_feature('jrCore', 'css', 'jrPayPal', 'jrPayPal.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrPayPal', true);

    // Add PayPal email address to Profile + price to item forms
    jrCore_register_event_listener('jrCore', 'form_display', 'jrPayPal_form_display_listener');

    // Our "My Downloads" tab
    $_tmp = array(
        'label' => 'downloads',
        'field' => 'quota_jrPayPal_show_downloads'
    );
    jrCore_register_module_feature('jrUser', 'account_tab', 'jrPayPal', 'downloads', $_tmp);

    // We have an event trigger
    jrCore_register_event_trigger('jrPayPal', 'add_paypal_price_field', 'Fired in forms so module can have price field added');

    // Core item buttons
    $_tmp = array(
        'title'  => 'buy now button',
        'icon'   => 'cart',
        'active' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'item_list_button', 'jrPayPal', 'jrPayPal_item_cart_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrPayPal', 'jrPayPal_item_cart_button', $_tmp);

    return true;
}

//---------------------------------------------------------
// BUY NOW BUTTON
//---------------------------------------------------------

/**
 * Return "buy it now" button for the item
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrPayPal_item_cart_button($module, $_item, $_args, $smarty, $test_only = false)
{
    if ($test_only) {
        switch ($module) {
            case 'jrAudio':
            case 'jrVideo':
            case 'jrFile':
                return true;
                break;
            default:
                // See if this module has registered for PayPal support
                $_tm = array();
                $_ex = array(
                    'module' => $module
                );
                $_tm = jrCore_trigger_event('jrPayPal', 'add_paypal_price_field', $_tm, $_ex);
                if (count($_tm) > 0) {
                    return true;
                }
                break;
        }
        return false;
    }
    if (!isset($_args['field']) || strlen($_args['field']) === 0) {
        return false;
    }
    $_args['module'] = $module;
    $_args['item']   = $_item;
    return smarty_function_jrPayPal_buy_now_button($_args, $smarty);
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Adds a "price" field to forms that have requested it
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrPayPal_form_display_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    // See if this is the Import Audio Tool
    if (jrUser_is_master() && $_data['form_view'] == 'jrAudio/import') {
        $pfx  = jrCore_db_get_prefix($_data['form_params']['module']);
        $_lng = jrUser_load_lang_strings();
        $_tmp = array(
            'name'          => "{$pfx}_file_item_price",
            'type'          => 'text',
            'default'       => '',
            'validate'      => 'price',
            'min'           => '0.01',
            'label'         => $_lng['jrPayPal'][9],
            'help'          => $_lng['jrPayPal'][10],
            'required'      => false,
            'form_designer' => false // no form designer or we can't turn it off
        );
        jrCore_form_field_create($_tmp);
        return $_data;
    }

    // See if this user's quota allows sales
    if (!isset($_user['quota_jrPayPal_allowed']) || $_user['quota_jrPayPal_allowed'] != 'on') {
        // Not active for this quota
        return $_data;
    }
    // If the FoxyCart module is active for this quota - exit
    if (jrCore_module_is_active('jrFoxyCart') && isset($_user['quota_jrFoxyCart_active']) && $_user['quota_jrFoxyCart_active'] == 'on') {
        return $_data;
    }
    switch ($_post['module']) {

        case 'jrProfile':
            if ($_data['form_view'] == 'jrProfile/settings') {
                $_lng = jrUser_load_lang_strings();
                $_tmp = array(
                    'name'          => "profile_paypal_email",
                    'type'          => 'text',
                    'default'       => '',
                    'validate'      => 'email',
                    'label'         => $_lng['jrPayPal'][7],
                    'help'          => $_lng['jrPayPal'][8],
                    'required'      => false,
                    'form_designer' => false // no form designer or we can't turn it off
                );
                jrCore_form_field_create($_tmp);

                // Change from default currency?
                if (isset($_user['quota_jrPayPal_allow_change']) && $_user['quota_jrPayPal_allow_change'] == 'on') {
                    $_tmp = array(
                        'name'          => "profile_paypal_currency",
                        'type'          => 'select',
                        'options'       => jrPayPal_get_currencies(),
                        'default'       => $_user['quota_jrPayPal_default_currency'],
                        'validate'      => 'not_empty',
                        'label'         => $_lng['jrPayPal'][11],
                        'help'          => $_lng['jrPayPal'][12],
                        'required'      => false,
                        'form_designer' => false // no form designer or we can't turn it off
                    );
                    jrCore_form_field_create($_tmp);
                }
            }
            break;

        case 'jrAudio':
        case 'jrVideo':
        case 'jrFile':
            list(, $view) = explode('/', $_data['form_view']);
            if ($view == 'create' || $view == 'update' || $view == 'create_album' || $view == 'update_album') {
                $pfx = jrCore_db_get_prefix($_data['form_params']['module']);
                if (isset($_user['profile_paypal_email']) && jrCore_checktype($_user['profile_paypal_email'], 'email') && isset($pfx) && strlen($pfx) > 0) {
                    $_lng = jrUser_load_lang_strings();
                    $_tmp = array(
                        'name'          => "{$pfx}_file_item_price",
                        'type'          => 'text',
                        'default'       => '',
                        'validate'      => 'price',
                        'min'           => '0.01',
                        'label'         => $_lng['jrPayPal'][9],
                        'help'          => $_lng['jrPayPal'][10],
                        'required'      => false,
                        'form_designer' => false // no form designer or we can't turn it off
                    );
                    jrCore_form_field_create($_tmp);
                }
            }
            break;

        default:

            // Check for other modules
            $_ex = array(
                'module' => $_post['module']
            );
            $_tm = jrCore_trigger_event('jrPayPal', 'add_paypal_price_field', $_data, $_ex);
            $pfx = jrCore_db_get_prefix($_post['module']);
            if (isset($_user['profile_paypal_email']) && jrCore_checktype($_user['profile_paypal_email'], 'email') && $pfx && isset($_tm["{$_data['form_view']}"])) {
                $_lng = jrUser_load_lang_strings();
                $_tmp = array(
                    'name'          => "{$pfx}_file_item_price",
                    'type'          => 'text',
                    'default'       => '',
                    'validate'      => 'price',
                    'min'           => '0.01',
                    'label'         => $_lng['jrPayPal'][9],
                    'help'          => $_lng['jrPayPal'][10],
                    'required'      => false,
                    'form_designer' => false // no form designer or we can't turn it off
                );
                jrCore_form_field_create($_tmp);
            }
            break;

    }
    return $_data;
}

//---------------------------------------------------------
// FUNCTIONS
//---------------------------------------------------------

/**
 * Get supported PayPal currencies
 * @return array
 */
function jrPayPal_get_currencies()
{
    $_tmp = array(
        'AUD' => '(AUD) Australian Dollar',
        'BRL' => '(BRL) Brazilian Real',
        'CAD' => '(CAD) Canadian Dollar',
        'CZK' => '(CZK) Czech Koruna',
        'DKK' => '(DKK) Danish Krone',
        'EUR' => '(EUR) Euro',
        'HKD' => '(HKD) Hong Kong Dollar',
        'ILS' => '(ILS) Israeli New Sheqel',
        'MXN' => '(MXN) Mexican Peso',
        'NOK' => '(NOK) Norwegian Krone',
        'NZD' => '(NZD) New Zealand Dollar',
        'PHP' => '(PHP) Philippine Peso',
        'PLN' => '(PLN) Polish Zloty',
        'GBP' => '(GBP) Pound Sterling',
        'RUB' => '(RUB) Russian Ruble',
        'SGD' => '(SGD) Singapore Dollar',
        'SEK' => '(SEK) Swedish Krona',
        'CHF' => '(CHF) Swiss Franc',
        'THB' => '(THB) Thai Baht',
        'USD' => '(USD) U.S. Dollar'
    );
    natcasesort($_tmp);
    return $_tmp;
}

/**
 * Creates a Buy Now URL for the given item
 * @param $module string Module
 * @param $_item array Item info
 * @return bool|string
 */
function jrPayPal_create_buy_now_url($module, $_item)
{
    // NOTE: User will always be logged in here
    global $_user, $_conf;
    $pfx = jrCore_db_get_prefix($module);
    if (!isset($_item["{$pfx}_file_item_price"]) || !jrCore_checktype($_item["{$pfx}_file_item_price"], 'price')) {
        return false;
    }
    $url = jrCore_get_module_url('jrPayPal');
    $iid = "{$_user['_user_id']}-{$module}-{$_item['_item_id']}";
    $cur = 'USD';
    if (isset($_item['profile_paypal_currency']{1}) && $_item['quota_jrPayPal_allow_change'] == 'on') {
        $cur = $_item['profile_paypal_currency'];
    }
    elseif (isset($_item['quota_jrPayPal_default_currency']{1})) {
        $cur = $_item['quota_jrPayPal_default_currency'];
    }
    $_pr = array(
        'business'      => urlencode($_item['profile_paypal_email']),
        'item_name'     => urlencode($_item["{$pfx}_title"]),
        'item_number'   => $iid,
        'amount'        => urlencode($_item["{$pfx}_file_item_price"]),
        'currency_code' => $cur,
        'return'        => urlencode("{$_conf['jrCore_base_url']}/{$url}/downloads"),
        'notify_url'    => urlencode("{$_conf['jrCore_base_url']}/{$url}/webhook"),
        'no_note'       => '0'
    );
    $url = 'www.sandbox.paypal.com';
    if (isset($_conf['jrPayPal_live']) && $_conf['jrPayPal_live'] == 'on') {
        $url = 'www.paypal.com';
    }
    $url = "https://{$url}/cgi-bin/webscr?cmd=_xclick";
    foreach ($_pr as $k => $v) {
        $url .= "&{$k}={$v}";
    }
    return $url;
}

/**
 * Creates a buy now button for an item
 * @param $params
 * @param $smarty
 * @return string
 */
function smarty_function_jrPayPal_buy_now_button($params, $smarty)
{
    global $_conf;
    if (!isset($params['module']) || !jrCore_module_is_active($params['module'])) {
        return '';
    }
    if (!isset($params['item']) || !is_array($params['item'])) {
        return 'invalid item';
    }
    if (!isset($params['item']['profile_paypal_email']) || !jrCore_checktype($params['item']['profile_paypal_email'], 'email')) {
        return '';
    }
    $pfx = jrCore_db_get_prefix($params['module']);
    if (!isset($params['item']["{$pfx}_file_item_price"]) || !jrCore_checktype($params['item']["{$pfx}_file_item_price"], 'price')) {
        return '';
    }
    if (!isset($params['item']['quota_jrPayPal_allowed']) || $params['item']['quota_jrPayPal_allowed'] != 'on') {
        return '';
    }

    // See if our skin has registered an icon size
    $size = 32;
    $_tmp = jrCore_get_registered_module_features('jrCore', 'icon_size');
    if (isset($_tmp["{$_conf['jrCore_active_skin']}"])) {
        $size = array_keys($_tmp["{$_conf['jrCore_active_skin']}"]);
        $size = (int) reset($size);
    }

    $iid = "{$params['module']}-{$params['item']['_item_id']}";
    $url = jrCore_get_module_url('jrPayPal');
    $url = "{$_conf['jrCore_base_url']}/{$url}/checkout/{$params['module']}/{$params['item']['_item_id']}";

    $_tmp = jrCore_get_registered_module_features('jrCore', 'icon_color');
    if (isset($_tmp["{$_conf['jrCore_active_skin']}"])) {
        $color = array_keys($_tmp["{$_conf['jrCore_active_skin']}"]);
        $color = reset($color);
    }
    else {
        $color = 'black';
    }
    $_pr = array(
        'module' => 'jrPayPal',
        'image'  => "paypal_{$color}.png",
        'width'  => (isset($params['width'])) ? (int) $params['width'] : ceil($size * 2.5),
        'height' => (isset($params['height'])) ? (int) $params['height'] : ($size - ceil($size / 8)),
        'alt'    => 'buy now'
    );

    $tmp = new stdClass();
    $img = smarty_function_jrCore_image($_pr, $tmp);
    $out = '<div class="sprite_icon paypal_buy_now_section">';
    $out .= '<a id="' . $iid . '" href="' . $url . '"><span class="add_to_cart_price paypal_buy_now_price">' . $params['item']["{$pfx}_file_item_price"] . '</span>';
    $out .= $img . '</a>';
    $out .= '</div>';

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}
