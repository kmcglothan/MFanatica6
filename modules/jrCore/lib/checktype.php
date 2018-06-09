<?php
/**
 * Jamroom System Core module
 *
 * copyright 2018 The Jamroom Network
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0.  Please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
 *
 * Jamroom may use modules and skins that are licensed by third party
 * developers, and licensed under a different license  - please
 * reference the individual module or skin license that is included
 * with your installation.
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
 * @package CheckType
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Test a value to see if it is of a specific "type"
 *
 * The jrCore_checktype function is used for variable validation to ensure
 * a given variable is of the requested "type".  Valid types are:
 *
 * * <b>allowed_html</b> - Ensures value is a string and is allowed to contain HTML allowed in the profile's quota <br>
 * * <b>core_string</b> - the numbers 0-9, letters a-z and _ (underscore) <br>
 * * <b>date</b> - a valid date <br>
 * * <b>domain</b> - a valid domain name (no www) <br>
 * * <b>email</b> - a valid email address <br>
 * * <b>float</b> - a floating point number <br>
 * * <b>hex</b> - a hexadecimal value <br>
 * * <b>ip_address</b> - a valid IP Address <br>
 * * <b>is_true</b> - boolean TRUE or int or string "1" <br>
 * * <b>md5</b> - valid 32 character long MD5 hash <br>
 * * <b>multi_word</b> - a string with more than 1 word <br>
 * * <b>not_empty</b> - a string of any length <br>
 * * <b>number</b> - an integer <br>
 * * <b>number_nn</b> - an integer greater than or equal to zero (0) <br>
 * * <b>number_nz</b> - an integer greater than or equal to one (1) <br>
 * * <b>onoff</b> - either "on" or "off" <br>
 * * <b>price</b> - a price in D.CC format <br>
 * * <b>printable</b> - UTF-8 characters with HTML stripped <br>
 * * <b>sha1</b> - a 40 character SHA1 hash <br>
 * * <b>string</b> - a string that does not contain HTML or hidden characters <br>
 * * <b>url</b> - a valid URL <br>
 * * <b>url_name</b> - the numbers 0-9, letters a-z, _ and - (dash) or %<br>
 * * <b>user_name</b> - the numbers 0-9, letters a-z, _ (underscore), - (dash) and spaces <br>
 * * <b>file_name</b> - string that can contain a . (dot) <br>
 * * <b>yesno</b> - "yes" or "no" <br>
 * * <b>json</b> - check if a string is json or not<br>
 *
 * @param string $input String to test
 * @param string $type "Type" to test incoming string against.
 * @param bool $desc_only Returns checktype description ONLY if set to true
 * @param bool $type_only Returns checktype TYPE ONLY if set to true
 *
 * @return mixed
 */
function jrCore_checktype($input, $type, $desc_only = false, $type_only = false)
{
    // no validation as type is false - return OK
    if ($type === false) {
        return true;
    }

    $_lf = jrCore_get_flag('jrcore_checktype_cache');
    if (!$_lf) {
        $_ft = jrCore_get_registered_module_features('jrCore', 'checktype');
        // We will get an array back based on module => entries
        if (!$_ft || !is_array($_ft)) {
            echo "error: jrCore_checktype: unable to load checktype plugins";
            jrCore_db_close();
            exit;
        }
        foreach ($_ft as $module => $_entries) {
            foreach ($_entries as $name => $func) {
                $_lf[$name] = "{$module}_checktype_{$name}";
            }
        }
        if (!jrCore_get_flag('jrcore_in_module_init')) {
            jrCore_set_flag('jrcore_checktype_cache', $_lf);
        }
    }
    if (!isset($_lf[$type])) {
        echo "error: jrCore_checktype: invalid type: {$type}";
        jrCore_db_close();
        exit;
    }
    $func = $_lf[$type];
    // zero length is false
    if (!is_array($input) && strlen($input) === 0) {
        if ($desc_only) {
            return $func(false, true);
        }
        elseif ($type_only) {
            return $func(false, false, true);
        }
        return false;
    }
    if (!function_exists($func)) {
        jrCore_notice('Error', "invalid jrCore_checktype: {$type} - checktype function not found");
    }
    if ($desc_only) {
        $val = $func($input, true);
    }
    elseif ($type_only) {
        $val = $func($input, false, true);
    }
    else {
        $val = $func($input);
    }
    if ($val) {
        return $val;
    }
    return false;
}

/**
 * jrCore_checktype_allowed_html
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_allowed_html($input, $desc_only = false, $type_only = false)
{
    global $_user;
    if ($desc_only) {
        $tmp = jrCore_get_local_referrer();
        if (!strpos($tmp, 'designer') && isset($_user) && isset($_user['quota_jrCore_allowed_tags']) && strlen($_user['quota_jrCore_allowed_tags']) > 0) {
            $_ln = jrUser_load_lang_strings();
            return $_ln['jrCore'][104] . $_user['quota_jrCore_allowed_tags'];
        }
        else {
            return 105; // printable characters and HTML allowed by Quota
        }
    }
    if ($type_only) {
        return 'string';
    }
    // If we are a Master Admin, we allow all tags...
    if (jrUser_is_master()) {
        return true;
    }

    // ignore sections in [code] blocks
    $input = jrCore_strip_bb_code($input);

    // Strip non-allowed HTML from string and compare to original
    $quota_id = (isset($_user['quota_jrCore_allowed_tags'])) ? $_user['quota_jrCore_allowed_tags'] : '';
    if (trim($input) != trim(jrCore_strip_html($input, $quota_id))) {
        // Tags were found in $input that were stripped
        return false;
    }
    return true;
}

/**
 * jrCore_checktype_core_string
 * @ignore
 * See: http://dev.mysql.com/doc/refman/5.0/en/identifiers.html
 * we do not allow $
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_core_string($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 106; // the numbers 0-9, letters a-z and _ (underscore)
    }
    if ($type_only) {
        return 'string';
    }
    if (preg_match('/[^A-Za-z0-9_]/', $input) > 0) {
        return false;
    }
    return true;
}

/**
 * jrCore_checktype_user_name
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_user_name($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 107; // the numbers 0-9, letters a-z, _ (underscore), - (dash) and spaces
    }
    if ($type_only) {
        return 'string';
    }
    if (@preg_match('/[^A-Za-z0-9_ -]/', $input) > 0) {
        return false;
    }
    return true;
}

/**
 * jrCore_checktype_url_name
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_url_name($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 108; // the numbers 0-9, letters a-z, _ and - (dash) or %
    }
    if ($type_only) {
        return 'string';
    }
    if (preg_match('/[^A-Za-z0-9_%-]/', $input) > 0) {
        return false;
    }
    return true;
}

/**
 * jrCore_checktype_file_name
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_file_name($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 109; // the numbers 0-9, letters a-z, _ and - (dash) and . (dot)
    }
    if ($type_only) {
        return 'string';
    }

    if (preg_match('/^[A-Za-z0-9._-]+$/', $input) > 0) {
        return true;
    }
    return false;
}

/**
 * jrCore_checktype_is_true
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_is_true($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 110; // boolean or string TRUE or int or string 1
    }
    if ($type_only) {
        return 'string';
    }
    if (isset($input) && ($input === 1 || $input === '1' || $input === true || $input === 'true')) {
        return true;
    }
    return false;
}

/**
 * jrCore_checktype_not_empty
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_not_empty($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 111; // a string
    }
    if ($type_only) {
        return 'string';
    }
    if (isset($input) && strlen($input) > 0) {
        return true;
    }
    return false;
}

/**
 * jrCore_checktype_domain
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_domain($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 112; // a valid domain name (no www)
    }
    if ($type_only) {
        return 'string';
    }
    // Make sure we get a good domain.tld
    if (strpos(' ' . $input, '.') && !strpos(' ' . $input, '/')) {
        $input = jrCore_str_to_lower($input);
        if (filter_var("http://{$input}", FILTER_VALIDATE_URL)) {
            return true;
        }
    }
    return false;
}

/**
 * jrCore_checktype_printable
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_printable($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 113; // numbers, letters, spaces or punctuation and contain no HTML
    }
    if ($type_only) {
        return 'string';
    }

    // ignore sections in [code] blocks
    $input = jrCore_strip_bb_code($input);
    $input = jrCore_strip_emoji($input);

    // Strip all non-utf8-printable chars
    $tmp = jrCore_strip_html(jrCore_strip_non_utf8($input));
    if ($tmp == $input) {
        // Our input matches the stripped version
        return true;
    }
    return false;
}

/**
 * jrCore_checktype_date
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_date($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 114; // a valid date
    }
    if ($type_only) {
        return 'date';
    }
    $tmp = strtotime($input);
    if (!$tmp) {
        return false;
    }
    return true;
}

/**
 * jrCore_checktype_date_birthday
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_date_birthday($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 115; // a valid date in the format YYYYMMDD
    }
    if ($type_only) {
        return 'date';
    }
    if (strlen($input) === 8 && is_numeric($input)) {
        return true;
    }
    return false;
}

/**
 * jrCore_checktype_email
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_email($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 116; // a valid email address
    }
    if ($type_only) {
        return 'string';
    }
    // Prevent name@whatever..com
    if (preg_match('/\.\.[A-Z]{2,8}$/i', $input)) {
        return false;
    }
    if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
        return true;
    }
    if (preg_match("/^[A-Z0-9._+%-]+@[A-Z0-9][A-Z0-9.-]{0,61}\.[A-Z]{2,6}$/i", $input)) {
        return true;
    }
    return false;
}

/**
 * jrCore_checktype_float
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_float($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 117; // a floating point number
    }
    if ($type_only) {
        return 'float';
    }
    $ftest = '+' . (float) $input;
    if ($ftest != '+' . $input) {
        return false;
    }
    return true;
}

/**
 * array
 * @deprecated
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_array($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return "a PHP array";
    }
    if ($type_only) {
        return 'array';
    }
    if (!is_array($input)) {
        return false;
    }
    return true;
}

/**
 * jrCore_checktype_hex
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_hex($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 118; // a hexadecimal value
    }
    if ($type_only) {
        return 'string';
    }
    if (!ctype_xdigit($input)) {
        return false;
    }
    return true;
}

/**
 * Check if input is an IP Address
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_ip_address($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 119; // a valid IP Address
    }
    if ($type_only) {
        return 'string';
    }
    if (filter_var($input, FILTER_VALIDATE_IP)) {
        return true;
    }
    return false;
}

/**
 * Check if input is a PRIVATE IP Address
 * @ignore
 * @link http://stackoverflow.com/questions/13818064/check-if-an-ip-address-is-private
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_private_ip_address($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 120; // a reserved (private) IP Address
    }
    if ($type_only) {
        return 'string';
    }
    $_pri    = array(
        '10.0.0.0|10.255.255.255',     // single class A network
        '172.16.0.0|172.31.255.255',   // 16 contiguous class B network
        '192.168.0.0|192.168.255.255', // 256 contiguous class C network
        '169.254.0.0|169.254.255.255', // Link-local address also referred to as Automatic Private IP Addressing
        '127.0.0.0|127.255.255.255'    // localhost
    );
    $long_ip = ip2long($input);
    if ($long_ip != -1) {
        foreach ($_pri AS $p) {
            list ($start, $end) = explode('|', $p);
            if ($long_ip >= ip2long($start) && $long_ip <= ip2long($end)) {
                // private
                return true;
            }
        }
    }
    return false;
}

/**
 * jrCore_checktype_md5
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_md5($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 121; // a 32 character long MD5 hash
    }
    if ($type_only) {
        return 'string';
    }
    if (preg_match('/[^a-f0-9]/', $input) || strlen($input) != 32) {
        return false;
    }
    return true;
}

/**
 * jrCore_checktype_multi_word
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_multi_word($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 122; // a string with more than 1 word
    }
    if ($type_only) {
        return 'string';
    }
    if (str_word_count($input) > 1) {
        return true;
    }
    return false;
}

/**
 * jrCore_checktype_number_nz
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_number_nz($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 123; // a whole number greater than 0 (zero)
    }
    if ($type_only) {
        return 'number';
    }
    if ((is_int($input) || ctype_digit($input)) && $input > 0) {
        return true;
    }
    return false;
}

/**
 * jrCore_checktype_number_nn
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_number_nn($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 124; // a whole number greater than or equal to 0 (zero)
    }
    if ($type_only) {
        return 'number';
    }
    if ((is_int($input) || ctype_digit($input)) && $input >= 0) {
        return true;
    }
    return false;
}

/**
 * jrCore_checktype_number
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_number($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 125; // a whole number (unsigned)
    }
    if ($type_only) {
        return 'number';
    }
    if (is_int($input) || ctype_digit($input)) {
        return true;
    }
    return false;
}

/**
 * jrCore_checktype_signed
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_signed($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 126; // a signed (+, -) whole number
    }
    if ($type_only) {
        return 'number';
    }
    $input = str_replace(array('+', '-'), '', $input);
    if (is_int($input) || ctype_digit($input)) {
        return true;
    }
    return false;
}

/**
 * jrCore_checktype_onoff
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_onoff($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 127; // either on or off (checkbox)
    }
    if ($type_only) {
        return 'string';
    }
    if ($input != 'on' && $input != 'off') {
        return false;
    }
    return true;
}

/**
 * jrCore_checktype_price
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_price($input, $desc_only = false, $type_only = false)
{
    global $_conf;
    if ($desc_only) {
        return 128; // a price in D.CC format
    }
    if ($type_only) {
        return 'float';
    }
    // See of we have the FoxyCart module installed
    if (jrCore_module_is_active('jrFoxyCart') && isset($_conf['jrFoxyCart_store_currency']) && $_conf['jrFoxyCart_store_currency'] == 'JPY') {
        if (!jrCore_checktype_number_nn($input)) {
            return false;
        }
        return true;
    }
    // first make sure it is a valid float val
    if ($input != strval(floatval($input))) {
        return false;
    }
    // next, check the length of the "cents" part
    list($dollars, $cents) = explode('.', $input);
    if (strlen($dollars) === 0 && strlen($cents) === 2 && strlen($input) === 3) {
        // In .CC format - OK
        return true;
    }
    elseif (strlen($dollars) === 0 || strlen($cents) !== 2) {
        return false;
    }
    return true;
}

/**
 * jrCore_checktype_sha1
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_sha1($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 129; // a 40 character SHA1 hash
    }
    if ($type_only) {
        return 'string';
    }
    if (preg_match('/[^a-f0-9]/', $input) || strlen($input) != 40) {
        return false;
    }
    return true;
}

/**
 * jrCore_checktype_string
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_string($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 130; // a string that does not contain HTML or hidden characters
    }
    if ($type_only) {
        return 'string';
    }
    if (ctype_cntrl($input) || strip_tags($input) != $input) {
        return false;
    }
    return true;
}

/**
 * jrCore_checktype_url
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_url($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 131; // a valid URL
    }
    if ($type_only) {
        return 'string';
    }
    if ((strpos($input, 'http://') === 0 || strpos($input, 'https://') === 0) && filter_var($input, FILTER_VALIDATE_URL)) {
        return true;
    }
    return false;
}

/**
 * jrCore_checktype_yesno
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_yesno($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 132; // either yes or no
    }
    if ($type_only) {
        return 'string';
    }
    if ($input != 'yes' && $input != 'no') {
        return false;
    }
    return true;
}

/**
 * jrCore_checktype_json
 * @ignore
 * @param string $input Input to check
 * @param bool $desc_only Set to true to return description of checktype only
 * @param bool $type_only Set to true to return type of checktype only
 * @return bool
 */
function jrCore_checktype_json($input, $desc_only = false, $type_only = false)
{
    if ($desc_only) {
        return 133; // is JSON encoded
    }
    if ($type_only) {
        return 'string';
    }
    if (strpos($input, '{') !== 0 && strpos($input, '[') !== 0) {
        return false;
    }
    json_decode($input);
    return (json_last_error() == JSON_ERROR_NONE);
}
