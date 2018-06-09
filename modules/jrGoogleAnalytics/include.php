<?php
/**
 * Jamroom Google Analytics module
 *
 * copyright 2017 The Jamroom Network
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
 * meta
 */
function jrGoogleAnalytics_meta()
{
    $_tmp = array(
        'name'        => 'Google Analytics',
        'url'         => 'googleanalytics',
        'version'     => '1.2.3',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Add Google Analytics to your site pages and profiles',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2923/google-analytics',
        'license'     => 'mpl',
        'category'    => 'site'
    );
    return $_tmp;
}

/**
 * init
 */
function jrGoogleAnalytics_init()
{
    global $_conf;
    // We listen for our various page display triggers
    jrCore_register_event_listener('jrCore', 'view_results', 'jrGoogleAnalytics_view_results_listener');

    // We provide a custom listener to record a GA hit
    jrCore_register_event_listener('jrGoogleAnalytics', 'record_hit', 'jrGoogleAnalytics_record_hit_listener');

    // Let other modules know we are going to record a hit
    jrCore_register_event_trigger('jrGoogleAnalytics', 'save_hit', 'Fired when a hit is being saved to the collection API (non JS)');

    // A/B Testing Tool
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrGoogleAnalytics', 'experiment_browse', array('A/B Testing', 'Create A/B Tests using Google Analytics Experiments'));
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrGoogleAnalytics', 'experiment_browse', 'A/B Testing');

    // Register our custom JS
    if (isset($_conf['jrGoogleAnalytics_enabled']) && $_conf['jrGoogleAnalytics_enabled'] == 'on') {
        if ($_conf['jrGoogleAnalytics_type'] == 'universal') {
            jrCore_register_module_feature('jrCore', 'javascript', 'jrGoogleAnalytics', 'jrGoogleAnalytics_universal.js');
        }
        else {
            jrCore_register_module_feature('jrCore', 'javascript', 'jrGoogleAnalytics', 'jrGoogleAnalytics.js');
        }
    }
    return true;
}

//-------------------------------
// EVENT LISTENERS
//-------------------------------

/**
 * Insert Google Analytics experiment code - A/B Testing
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGoogleAnalytics_view_results_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (jrGoogleAnalytics_can_insert_tracking()) {

        // Don't add analytics to non-session views
        $_tmp = jrCore_get_registered_module_features('jrUser', 'skip_session');
        if ($_tmp && is_array($_tmp)) {
            foreach ($_tmp as $mod => $_opts) {
                if (isset($_post['option']) && isset($_post['module']) && isset($_opts["{$_post['option']}"]) && ($mod == $_post['module'] || $_opts["{$_post['option']}"] == 'magic_view')) {
                    return $_data;
                }
            }
        }

        // See what we are doing...
        switch ($_conf['jrGoogleAnalytics_type']) {
            case 'display':
            case 'standard':
                break;
            case 'universal':
                // For universal analytics, we support the Measurement Protocol, which means
                // each user must have a Client ID we use across both JS and PHP
                if (jrUser_is_logged_in()) {
                    if (!isset($_SESSION['user_jrGoogleAnalytics_cid']{20})) {
                        $uuid = jrGoogleAnalytics_gen_uuid();
                        $_dat = array('user_jrGoogleAnalytics_cid' => $uuid);
                        jrCore_db_update_item('jrUser', $_SESSION['_user_id'], $_dat);
                        $_SESSION['user_jrGoogleAnalytics_cid'] = $uuid;
                        $_post['unique_cid']                    = $uuid;
                    }
                    else {
                        $_post['unique_cid'] = $_SESSION['user_jrGoogleAnalytics_cid'];
                    }
                }
                break;
            default:
                $_conf['jrGoogleAnalytics_type'] = 'standard';
                break;
        }
        $script = trim(jrCore_parse_template("{$_conf['jrGoogleAnalytics_type']}.tpl", $_post, 'jrGoogleAnalytics'));
        $script = str_replace(array('<script>', '</script>'), '', $script);
        $_data  = str_replace('</head>', "<script>\n{$script}\n</script>\n</head>", $_data);

        // Are experiments enabled and do we have a page match?
        if (isset($_conf['jrGoogleAnalytics_ab_enabled']) && $_conf['jrGoogleAnalytics_ab_enabled'] == 'on') {
            if (!isset($_post['_uri']) || strlen($_post['_uri']) === 0) {
                $uri = '/';
            }
            else {
                $uri = trim(trim($_post['_uri']), '/');
                if (strlen($uri) === 0) {
                    $uri = '/';
                }
            }
            if ($_url = jrGoogleAnalytics_get_experiment_url($uri)) {
                if ($_url['e_active'] == 'on') {
                    // We have an experiment - insert code
                    $ptemp = jrCore_parse_template('experiment.tpl', $_url, 'jrGoogleAnalytics');
                    if (isset($ptemp{10})) {
                        $_data = str_replace('<head>', "<head>\n{$ptemp}\n", $_data);
                    }
                }
            }
        }

    }
    return $_data;
}

/**
 * Record a hit using the GA Measurement Protocol
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGoogleAnalytics_record_hit_listener($_data, $_user, $_conf, $_args, $event)
{
    // NOTE: GA Measurement API requires Universal Analytics
    if (!isset($_data['type']) || strlen($_conf['jrGoogleAnalytics_account']) === 0 || $_conf['jrGoogleAnalytics_type'] != 'universal') {
        return $_data;
    }
    switch ($_data['type']) {
        case 'pageview':
        case 'event':
        case 'transaction':
        case 'item':
        case 'social':
        case 'exception':
        case 'timing':
        case 'appview':
            break;
        default:
            jrCore_logger('CRI', "jrGoogleAnalytics: unknown record_hit type: {$_data['type']}", $_data);
            return $_data;
            break;
    }

    if (jrUser_is_logged_in() && isset($_user['user_jrGoogleAnalytics_cid']{30})) {
        // User is logged in, but we are in a PHP action
        $uuid = $_user['user_jrGoogleAnalytics_cid'];
    }
    elseif (isset($_data['cid']{30})) {
        // Passed in by caller
        $uuid = $_data['cid'];
    }
    else {
        // required, and since we didn't get it - create it
        $uuid = jrGoogleAnalytics_gen_uuid();
    }
    $_rp = array(
        'v'   => '1',
        'tid' => $_conf['jrGoogleAnalytics_account'],
        'cid' => $uuid,
        't'   => $_data['type']
    );
    unset($_data['type'], $_data['user_jrGoogleAnalytics_cid']);
    foreach ($_data as $k => $v) {
        // These keys cannot just be whatever you want - make sure and see:
        // https://developers.google.com/analytics/devguides/collection/protocol/v1/devguide
        if (!isset($_rp[$k])) {
            $_rp[$k] = $v;
        }
    }
    // POST this to GA
    $_rp = jrCore_trigger_event('jrGoogleAnalytics', 'save_hit', $_rp);
    $str = array();
    foreach ($_rp as $k => $v) {
        $str[] = "{$k}=" . urlencode($v);
    }
    $str = implode('&', $str) . '&z=' . mt_rand(000000, 999999);
    jrCore_load_url("http://www.google-analytics.com/collect?{$str}", null, 'GET', 80, null, null, true, 10);
    return $_data;
}

//-------------------------------
// FUNCTIONS
//-------------------------------

/**
 * Return TRUE of we can insert tracking into the current page
 * @return bool
 */
function jrGoogleAnalytics_can_insert_tracking()
{
    global $_conf;
    if (isset($_conf['jrGoogleAnalytics_exclude_admins']) && $_conf['jrGoogleAnalytics_exclude_admins'] == 'on' && jrUser_is_admin()) {
        return false;
    }
    if (jrCore_is_view_request() && isset($_conf['jrGoogleAnalytics_enabled']) && $_conf['jrGoogleAnalytics_enabled'] == 'on' && strlen($_conf['jrGoogleAnalytics_account']) > 0) {
        return true;
    }
    return false;
}

/**
 * Get our URI from a given URL
 * @param $url string
 * @return mixed|string
 */
function jrGoogleAnalytics_get_uri($url)
{
    global $_conf;
    $url = trim(trim($url), '/');
    if (strpos($url, $_conf['jrCore_base_url']) === 0) {
        $url = str_replace($_conf['jrCore_base_url'], '', $url);
    }
    if (strpos($url, '?')) {
        $url = substr($url, 0, strpos($url, '?'));
    }
    $url = trim(trim($url), '/');
    if (strlen($url) === 0) {
        $url = '/';
    }
    return $url;
}

/**
 * Get an experiment URL for a URI
 * @param $url string URL to get forward URL for
 * @return mixed
 */
function jrGoogleAnalytics_get_experiment_url($url)
{
    // Special handling of root URL
    $tbl = jrCore_db_table_name('jrGoogleAnalytics', 'experiment');
    if ($url == '/') {
        $req = "SELECT * FROM {$tbl} WHERE e_urlone = '/' LIMIT 1";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if ($_rt && is_array($_rt)) {
            return $_rt;
        }
        return false;
    }

    // When a URI comes in, we match from the MOST specific to the LEAST specific
    // if there are rules with wildcards in them
    if (strpos($url, '?')) {
        $url = substr($url, 0, strpos($url, '?'));
    }
    $_ur = explode('/', trim($url));
    if ($_ur && is_array($_ur)) {
        $_pc = array();
        foreach ($_ur as $k => $v) {
            $add = true;
            if (strpos($v, '?')) {
                // We are into the URL params
                list($v,) = explode('?', $v, 2);
                $add = false;
            }
            if (strlen($v) > 0 && $v != '/') {
                $idx = ($k - 1);
                if (isset($_pc[$idx])) {
                    $_pc[$k] = jrCore_db_escape(substr($_pc[$idx], 0, strlen($_pc[$idx]) - 2) . "/${v}");
                }
                else {
                    $_pc[$k] = jrCore_db_escape("/${v}");
                }
                if ($add) {
                    $_pc[$k] .= '/*';
                }
            }
        }
        if (count($_pc) > 0) {
            $_pc[] = jrCore_db_escape($url);
            $req   = "SELECT * FROM {$tbl} WHERE e_urlone IN('" . implode("','", $_pc) . "') ORDER BY LENGTH(e_urlone) DESC LIMIT 1";
            $_rt   = jrCore_db_query($req, 'SINGLE');
            if ($_rt && is_array($_rt)) {
                return $_rt;
            }
        }
    }
    return false;
}

/**
 * Generate a v4 UUID
 * @see http://www.php.net/manual/en/function.uniqid.php#94959
 * @return string
 */
function jrGoogleAnalytics_gen_uuid()
{
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),

        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,

        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}
