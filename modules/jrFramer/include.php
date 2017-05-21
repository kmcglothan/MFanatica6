<?php
/**
 * Jamroom 5 iFrame Control module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
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
 * @copyright 2012 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrFramer_meta
 */
function jrFramer_meta()
{
    $_tmp = array(
        'name'        => 'iFrame Control',
        'url'         => 'framer',
        'version'     => '1.0.5',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Control the src value of iframes used on your site.',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2924/iframe-control',
        'license'     => 'mpl',
        'category'    => 'site'
    );
    return $_tmp;
}

/**
 * jrFramer_init
 */
function jrFramer_init()
{
    // We have some small custom CSS
    jrCore_register_module_feature('jrCore', 'css', 'jrFramer', 'jrFramer.css');

    // Tool to create and delete allowed domains
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrFramer', 'browse', array('Allowed Domains', 'Create and Delete allowed iframe src domains'));

    // Custom tabs
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrFramer', 'browse', 'Allowed Domains');

    // Add in iframe allowed tags to HTML Purifier if enabled
    jrCore_register_event_listener('jrCore', 'html_purifier', 'jrFramer_html_purifier_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrFramer_verify_module_listener');

    $_tmp = array(
        'wl'    => 'framer',
        'label' => 'Validate iframes',
        'help'  => 'If active, iframe src values will be checked to be sure the URL is an allowed domain'
    );
    jrCore_register_module_feature('jrCore', 'format_string', 'jrFramer', 'jrFramer_format_string_check_iframes', $_tmp);

    // Site Builder widget
    // jrCore_register_module_feature('jrSiteBuilder', 'widget', 'jrFramer', 'widget_iframe', 'Embedded iFrame');

    return true;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Remove old config option
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrFramer_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    jrCore_delete_setting('jrFramer', 'active');
    return $_data;
}

/**
 * Adds width/height keys to saved media info
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrFramer_html_purifier_listener($_data, $_user, $_conf, $_args, $event)
{
    /** @noinspection PhpUndefinedMethodInspection */
    $_data->set('HTML.SafeIframe', true);
    /** @noinspection PhpUndefinedMethodInspection */
    $_data->set('URI.SafeIframeRegexp', '%(.*)%');
    return $_data;
}

//----------------------
// STRING FORMATTER
//----------------------

/**
 * Registered core string formatter - Convert # tags
 * @param string $string String to format
 * @param int $quota_id Quota ID for Profile ID
 * @return string
 */
function jrFramer_format_string_check_iframes($string, $quota_id = 0)
{
    $key = md5($string);
    if (!strpos(' ' . $string, 'iframe>') || jrCore_get_flag($key) === 1) {
        return $string;
    }
    $out = '';
    $_rp = array(
        'www.' => '',
        '//'   => ''
    );
    $_tm = preg_split("/<iframe/i", $string, 0, PREG_SPLIT_NO_EMPTY);
    if ($_tm && is_array($_tm) && count($_tm) > 1) {
        $_al = jrFramer_get_allowed_domains();
        foreach ($_tm as $part) {
            if (stripos($part, 'iframe>')) {
                preg_match('/src="([^"]+)"/', $part, $match);
                // URL can look like:
                // http://domain
                // //:domain
                if (strpos($match[1], '//') === 0) {
                    $match = str_replace(array_keys($_rp), $_rp, $match[1]);
                    $match = explode('/', $match);
                    $match = reset($match);
                }
                else {
                    $match = str_replace(array_keys($_rp), $_rp, parse_url($match[1], PHP_URL_HOST));
                }
                if (strlen($match) > 2) {
                    if (!isset($_al[$match]) && !isset($_al["www.{$match}"])) {
                        // We are not allowed - remove
                        $_pt = preg_split("/iframe>/i", $part, 0, PREG_SPLIT_NO_EMPTY);
                        // This should get us just the internals of the iframe
                        $_ln = jrUser_load_lang_strings();
                        $out .= '<span class="iframe_error">' . $_ln['jrFramer'][1] . '</span>' . $_pt[1];
                    }
                    else {
                        $out .= '<iframe ' . $part;
                    }
                }
                else {
                    // Bad iframe source
                    $out .= strip_tags($part);
                }
            }
            else {
                $out .= $part;
            }
        }
        $string = $out;
    }
    return $string;
}

/**
 * Get all configured allowed domains
 * @return mixed
 */
function jrFramer_get_allowed_domains()
{
    $tbl = jrCore_db_table_name('jrFramer', 'domain');
    $req = "SELECT dm_id, dm_name FROM {$tbl}";
    return jrCore_db_query($req, 'dm_name', false, 'dm_id');
}
