<?php
/**
 * Jamroom 5 Proxima Memcache module
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
 * @copyright 2014 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function jrProximaCache_meta()
{
    $_tmp = array(
        'name'        => 'Proxima Memcache',
        'url'         => 'px_cache',
        'version'     => '1.1.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Memcache for Proxima sessions',
        'category'    => 'proxima',
        'url_change'  => false,
        'requires'    => 'jrProximaCore:2.0.0',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrProximaCache_init()
{
    // Event Listeners
    jrCore_register_event_listener('jrCore', 'system_check', 'jrProximaCache_system_check_listener');

    // Tools
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrProximaCache', 'server_browser', array('Memcache Servers', 'Browse, Create and Update active Memcache servers'));

    // Tabs
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrProximaCache', 'server_browser', 'Memcache Servers');

    // Our default master view
    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrProximaCache', 'server_browser');

    // register jrProximaCore cache plugin
    jrCore_register_module_feature('jrProximaCore', 'session_plugin', 'jrProximaCache', 'memcache', 'Memcache');
    return true;
}

/**
 * px_config
 */
function jrProximaCache_px_config()
{
    return array(
        'default' => true
    );
}

/**
 * Check for Memcache class
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return bool
 */
function jrProximaCache_system_check_listener($_data, $_user, $_conf, $_args, $event)
{
    $dat             = array();
    $dat[1]['title'] = 'Proxima Memcache';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = 'PHP Memcached support';
    $dat[2]['class'] = 'center';

    $pass = jrCore_get_option_image('pass');
    $fail = jrCore_get_option_image('fail');

    if (class_exists('Memcached')) {
        $dat[3]['title'] = $pass;
        $dat[4]['title'] = 'PHP Memcached support is enabled';
    }
    else {
        $dat[3]['title'] = $fail;
        $dat[4]['title'] = 'PHP Memcached support is NOT enabled';
    }
    $dat[3]['class'] = 'center';
    jrCore_page_table_row($dat);
    return true;
}

/**
 * Connect to Memcached server(s)
 * @return bool|Memcached
 */
function jrProximaCache_connect()
{
    global $_conf;
    if (class_exists('Memcached')) {
        $mc = new Memcached($_conf['jrProximaCache_cache_reload']);
        if (!count($mc->getServerList())) {

            $mc->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
            $mc->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
            $mc->setOption(Memcached::OPT_RECV_TIMEOUT, 1000);
            $mc->setOption(Memcached::OPT_SEND_TIMEOUT, 1000);
            $mc->setOption(Memcached::OPT_NO_BLOCK, true);
            $mc->setOption(Memcached::OPT_TCP_NODELAY, true);

            // Add our servers in
            $tbl = jrCore_db_table_name('jrProximaCache', 'server');
            $req = "SELECT * FROM {$tbl} WHERE server_active = 'on'";
            $_rt = jrCore_db_query($req, 'NUMERIC');
            if (is_array($_rt)) {
                $_sv = array();
                foreach ($_rt as $_server) {
                    $_sv[] = array($_server['server_host'], $_server['server_port']);
                }
                $mc->addServers($_sv);
            }
        }
        return $mc;
    }
    return false;
}

/**
 * Get unique Cache key for item
 * @param $module string Proxima Module name
 * @param $key string unique ID within module
 * @return string
 */
function jrProximaCache_get_cache_key($module, $key)
{
    global $_conf;
    $app_id = jrProximaCore_get_active_app_id();
    return md5("{$_conf['jrCore_base_url']}/{$module}/{$app_id}/{$key}");
}

//---------------------------------
// jrProximaCache Memcache cache
//---------------------------------

/**
 * Set a cached item in Memcache
 * @param $module string Module that set cache item
 * @param $key string Cache Key
 * @param $value mixed Value to save
 * @param $seconds int Cache Seconds
 * @return mixed
 */
function plugin_jrProximaCache_memcache_set_cache_item($module, $key, $value, $seconds = -1)
{
    global $_conf;
    $mcc = jrProximaCache_connect();
    if ($mcc) {
        if ($seconds == -1) {
            $seconds = (int) $_conf['jrProximaCache_cache_seconds'];
        }
        $key = jrProximaCache_get_cache_key($module, $key);
        $mcc->set($key, $value, $seconds);
        return $key;
    }
    return false;
}

/**
 * Get a cached item from Memcache
 * @param $module string Module that set cache item
 * @param $key string Cache Key
 * @return mixed
 */
function plugin_jrProximaCache_memcache_get_cache_item($module, $key)
{
    $mcc = jrProximaCache_connect();
    if ($mcc) {
        $key = jrProximaCache_get_cache_key($module, $key);
        return $mcc->get($key);
    }
    return false;
}

/**
 * Delete a cache key in Memcache
 * @param $module string Module that set cache item
 * @param $key string Cache Key
 * @return mixed
 */
function plugin_jrProximaCache_memcache_delete_cache_item($module, $key)
{
    $mcc = jrProximaCache_connect();
    if ($mcc) {
        $key = jrProximaCache_get_cache_key($module, $key);
        return $mcc->delete($key);
    }
    return false;
}

//---------------------------------
// jrProximaCache Memcache session
//---------------------------------

/**
 * jrProximaCache Session plugin - create new user session
 * @param $app_id int Application ID
 * @param $_user array User Info
 * @param $expires int Expiration time in seconds
 * @return mixed
 */
function plugin_jrProximaCache_memcache_create_user_session($app_id, $_user, $expires)
{
    $key = md5(uniqid() . $_user['_user_id']);
    $_rt = array(
        'uid' => intval($_user['_user_id']),
        'pid' => intval($_user['_profile_id'])
    );
    if (plugin_jrProximaCache_memcache_set_cache_item('jrProximaCache', $key, json_encode($_rt), $expires)) {
        return $key;
    }
    return false;
}

/**
 * jrProximaCache Session plugin - delete an existing session
 * @param $app_id int Application ID
 * @param $_user array User Info
 * @return mixed
 */
function plugin_jrProximaCache_memcache_delete_user_session($app_id, $_user)
{
    $key = jrProximaCore_get_session_key();
    if (plugin_jrProximaCache_memcache_delete_cache_item('jrProximaCache', $key)) {
        return true;
    }
    return false;
}

/**
 * jrProximaCache Session plugin - check for valid session
 * @param string $key Client Session Key
 * @return mixed
 */
function plugin_jrProximaCache_memcache_is_valid_user_session($key)
{
    if ($_rt = plugin_jrProximaCache_memcache_get_cache_item('jrProximaCache', $key)) {
        return json_decode($_rt, true);
    }
    return false;
}

/**
 * jrProximaCache Session plugin - cleanup old sessions
 * @return int - returns number of cleaned up sessions
 */
function proxima_jrProximaCache_memcache_cleanup()
{
    return 0;
}
