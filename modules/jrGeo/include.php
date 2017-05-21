<?php
/**
 * Jamroom Geo Location module
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
function jrGeo_meta()
{
    $_tmp = array(
        'name'        => 'Geo Location',
        'url'         => 'geo',
        'version'     => '1.1.1',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Adds Geo Location Lookup functionality to the system',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/863/geo-location',
        'license'     => 'mpl',
        'category'    => 'tools'
    );
    return $_tmp;
}

/**
 * init
 */
function jrGeo_init()
{
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrGeo', 'database', array('Update Geo Location Database', 'Upload a new or updated GeoLiteCity Database File'));
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrGeo', 'database', 'Geo Location Database');
    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrGeo', 'database');

    // Make sure we have the city dat file uploaded
    jrCore_register_event_listener('jrCore', 'system_check', 'jrGeo_system_check_listener');
    return true;
}

/**
 * Make sure City data file is uploaded
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrGeo_system_check_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_conf;
    // Check for GeoIP module
    $dat             = array();
    $dat[1]['title'] = 'GeoLite City Database';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = 'uploaded';
    $dat[2]['class'] = 'center';
    if (isset($_conf['jrGeo_ip_file_time']) && jrCore_checktype($_conf['jrGeo_ip_file_time'], 'number_nz')) {
        $dat[3]['title'] = $_args['pass'];
        $dat[4]['title'] = 'GeoLite City database is uploaded and active';
    }
    else {
        $url             = jrCore_get_module_url('jrGeo');
        $dat[3]['title'] = $_args['fail'];
        $dat[4]['title'] = "Required GeoLite City database is missing<br><a href=\"{$_conf['jrCore_base_url']}/{$url}/database\">Click here for directions on uploading it.</a>";
    }
    $dat[3]['class'] = 'center';
    jrCore_page_table_row($dat);
    return $_data;
}

/**
 * Get GeoIP lookup info for an IP address
 * @param $ip
 * @return array|bool
 */
function jrGeo_location($ip)
{
    global $_conf;
    if (!$ip || strlen($ip) < 7 || strlen($ip) > 15 || !jrCore_checktype($ip, 'ip_address')) {
        return false;
    }

    // Have we already seen this one in this process?
    $key = 'jrgeo_checked_ips';
    if ($_cc = jrCore_get_flag($key)) {
        if (isset($_cc[$ip])) {
            return $_cc[$ip];
        }
    }
    else {
        $_cc = array();
    }

    // Output FORMAT:
    // [country_code] => US
    // [country_name] => United States
    // [region] => WA
    // [city] => Ferndale
    // [postal_code] => 98248
    // [latitude] => 48.8645
    // [longitude] => -122.6307
    // [dma_code] => 819
    // [metro_code] => 819
    // [continent_code] => NA

    // Uploaded Database (default)
    $_rt = false;
    if (!isset($_conf['jrGeo_active']) || $_conf['jrGeo_active'] == 'local') {
        if (!isset($_conf['jrGeo_ip_file_time']) || !jrCore_checktype($_conf['jrGeo_ip_file_time'], 'number_nz')) {
            // Not setup yet
            return false;
        }
        $dir = jrCore_get_media_directory(0, FORCE_LOCAL);
        $fil = "{$dir}/geoipcity-{$_conf['jrGeo_ip_file_time']}.dat";
        if (!is_file($fil)) {
            // We don't have this one - could be a new DAT file
            // Cleanup any "old" DAT files that could be hanging around
            $_fl = glob("{$dir}/geoipcity-*");
            if ($_fl && is_array($_fl)) {
                foreach ($_fl as $file) {
                    unlink($file);
                }
            }
            jrCore_confirm_media_file_is_local(0, 'geoipcity.dat', $fil);
        }
        require_once APP_DIR . '/modules/jrGeo/contrib/geoip/geoipcity.php';
        $geo = geoip_open($fil, GEOIP_STANDARD);
        $_rt = (array) GeoIP_record_by_addr($geo, $ip);
        geoip_close($geo);
        if ($_rt) {
            unset($_rt['country_code3'], $_rt['area_code']);
        }
    }

    // MaxMind precision API
    elseif (jrGeo_api_is_configured()) {
        if (!$_rt = jrGeo_get_cached_ip_info($ip)) {
            $url = 'https://geoip.maxmind.com/geoip/v2.1/city/' . $ip;
            $_rs = jrCore_load_url($url, null, 'GET', 443, $_conf['jrGeo_user_id'], $_conf['jrGeo_license_key'], false, 6);
            if ($_rs && strlen(trim($_rs)) > 0) {
                $_rs = json_decode($_rs, true);
                if ($_rs && is_array($_rs)) {
                    $_rt = array(
                        'country_code'   => (isset($_rs['country']['iso_code'])) ? $_rs['country']['iso_code'] : '',
                        'country_name'   => (isset($_rs['country']['names']['en'])) ? $_rs['country']['names']['en'] : '',
                        'region'         => (isset($_rs['subdivisions'][0]['iso_code'])) ? $_rs['subdivisions'][0]['iso_code'] : '',
                        'city'           => (isset($_rs['city']['names']['en'])) ? $_rs['city']['names']['en'] : '',
                        'postal_code'    => (isset($_rs['postal']['code'])) ? $_rs['postal']['code'] : '',
                        'latitude'       => (isset($_rs['location']['latitude'])) ? $_rs['location']['latitude'] : '',
                        'longitude'      => (isset($_rs['location']['longitude'])) ? $_rs['location']['longitude'] : '',
                        'dma_code'       => (isset($_rs['location']['metro_code'])) ? $_rs['location']['metro_code'] : '',
                        'metro_code'     => (isset($_rs['location']['metro_code'])) ? $_rs['location']['metro_code'] : '',
                        'continent_code' => (isset($_rs['continent']['code'])) ? $_rs['continent']['code'] : ''
                    );
                    jrGeo_save_cached_ip_info($ip, $_rt);
                }
            }
        }
    }

    // in memory cache
    $_cc[$ip] = $_rt;
    jrCore_set_flag($key, $_cc);

    return (is_array($_rt)) ? $_rt : false;
}

/**
 * Get cached IP info for an IP
 * @param string $ip
 * @return bool|mixed
 */
function jrGeo_get_cached_ip_info($ip)
{
    $tbl = jrCore_db_table_name('jrGeo', 'ip_cache');
    $req = "SELECT ip_time, ip_info FROM {$tbl} WHERE ip_address = '" . jrCore_db_escape($ip) . "' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        return json_decode($_rt['ip_info'], true);
    }
    return false;
}

/**
 * Save IP info to the IP cache
 * @param string $ip
 * @param array $_info
 * @return bool
 */
function jrGeo_save_cached_ip_info($ip, $_info)
{
    $tbl = jrCore_db_table_name('jrGeo', 'ip_cache');
    $ipa = jrCore_db_escape($ip);
    $inf = jrCore_db_escape(json_encode($_info));
    $req = "INSERT INTO {$tbl} (ip_address, ip_time, ip_info) VALUES ('{$ipa}', UNIX_TIMESTAMP(), '{$inf}')
            ON DUPLICATE KEY UPDATE ip_time = UNIX_TIMESTAMP(), ip_info = VALUES(ip_info)";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt > 0) {
        return true;
    }
    return false;
}

/**
 * Return TRUE if API is configured
 * @return bool
 */
function jrGeo_api_is_configured()
{
    global $_conf;
    if (isset($_conf['jrGeo_active']) && $_conf['jrGeo_active'] == 'api' && isset($_conf['jrGeo_user_id']) && strlen($_conf['jrGeo_user_id']) > 1 && isset($_conf['jrGeo_license_key']) && strlen($_conf['jrGeo_license_key']) > 1) {
        return true;
    }
    return false;
}

/**
 * Get distance between two coordinates
 * @param $latitude1 string
 * @param $longitude1 string
 * @param $latitude2 string
 * @param $longitude2 string
 * @return array
 */
function jrGeo_distance_between_points($latitude1, $longitude1, $latitude2, $longitude2)
{
    $theta = ($longitude1 - $longitude2);
    $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
    $miles = acos($miles);
    $miles = rad2deg($miles);
    $miles = ($miles * 60 * 1.1515);
    $_out  = array(
        'miles'      => round($miles, 2),
        'feet'       => round($miles * 5280, 2),
        'kilometers' => round($miles * 1.609344, 2),
        'meters'     => round(($miles * 1.609344) * 1000, 2)
    );
    return $_out;
}

/**
 * Distance
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrGeo_distance($params, $smarty)
{
    if (!isset($params['ip1'])) {
        return 'jrGeo_distance: ip1 param required';
    }
    if (!isset($params['ip2'])) {
        return 'jrGeo_distance: ip2 param required';
    }
    if ($_ip1 = jrGeo_location($params['ip1'])) {
        if ($_ip2 = jrGeo_location($params['ip2'])) {
            $_rt = jrGeo_distance_between_points($_ip1['latitude'], $_ip1['longitude'], $_ip2['latitude'], $_ip2['longitude']);
            if (isset($_rt) && is_array($_rt)) {
                // Check for template
                if (isset($params['template']{0})) {
                    if (strpos($params['template'], '.tpl')) {
                        $out = jrCore_parse_template($params['template'], $_rt);
                    }
                    else {
                        $_rp = array();
                        foreach ($_rt as $k => $v) {
                            $_rp["%{$k}%"] = $v;
                        }
                        $out = str_replace(array_keys($_rp), $_rp, $params['template']);
                        unset($_rp, $_rt);
                    }
                }
                else {
                    $out = $_rt['miles'];
                }
                if (!empty($params['assign'])) {
                    $smarty->assign($params['assign'], $out);
                    return '';
                }
                return $out;
            }
        }
    }
    return '';
}

/**
 * Location
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrGeo_location($params, $smarty)
{
    if (!isset($params['ip'])) {
        $params['ip'] = jrCore_get_ip();
    }
    $_rt = jrGeo_location($params['ip']);
    if (!$_rt) {
        return '';
    }

    // [country_code] => US
    // [country_code3] => USA
    // [country_name] => United States
    // [region] => WA
    // [city] => Bothell
    // [postal_code] => 98021
    // [latitude] => 47.7948
    // [longitude] => -122.2054
    // [area_code] => 425
    // [dma_code] => 819
    // [metro_code] => 819
    // [continent_code] => NA

    $out = '';
    // Check for template
    if (isset($params['template']{0})) {
        if (strpos($params['template'], '.tpl')) {
            $out = jrCore_parse_template($params['template'], $_rt);
        }
        else {
            $_rp = array();
            foreach ($_rt as $k => $v) {
                $_rp["%{$k}%"] = $v;
            }
            $out = str_replace(array_keys($_rp), $_rp, $params['template']);
            unset($_rp, $_rt);
        }
    }
    else {
        $_tm   = array();
        $_tm[] = (isset($_rt['city'])) ? $_rt['city'] : '';
        $_tm[] = (isset($_rt['region'])) ? $_rt['region'] : '';
        $_tm[] = (isset($_rt['country_name'])) ? $_rt['country_name'] : '';
        if (count($_tm) > 0) {
            $out = implode(', ', $_tm);
        }
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}
