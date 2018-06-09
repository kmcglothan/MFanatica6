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
 * @package Temp and Cache
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//-------------------------------------------
// Local Cache
//-------------------------------------------

/**
 * Check if local caching is enabled
 * @param bool $developer_check set to FALSE to not check for developer mode
 * @return bool
 */
function jrCore_local_cache_is_enabled($developer_check = true)
{
    if ($developer_check && jrCore_is_developer_mode()) {
        return false;
    }
    return function_exists('apcu_add');
}

/**
 * Add a key and value to the local cache
 * @param string $key
 * @param mixed $value
 * @param int $ttl
 * @return array|bool
 */
function jrCore_set_local_cache_key($key, $value,  $ttl = 0)
{
    global $_conf;
    if (jrCore_local_cache_is_enabled()) {
        $pfx = substr($_conf['jrCore_unique_string'], 0, 5);
        return apcu_add("{$pfx}:{$key}", $value, $ttl);
    }
    return false;
}

/**
 * Get a locally cached key value
 * @param string $key
 * @return mixed
 */
function jrCore_get_local_cache_key($key)
{
    global $_conf;
    if (jrCore_local_cache_is_enabled()) {
        $pfx = substr($_conf['jrCore_unique_string'], 0, 5);
        return apcu_fetch("{$pfx}:{$key}");
    }
    return false;
}

/**
 * Delete a locally cached key value
 * @param string $key
 * @return mixed
 */
function jrCore_delete_local_cache_key($key)
{
    global $_conf;
    if (jrCore_local_cache_is_enabled()) {
        $pfx = substr($_conf['jrCore_unique_string'], 0, 5);
        return apcu_delete("{$pfx}:{$key}");
    }
    return false;
}

/**
 * Reset the local cache
 * @return bool
 */
function jrCore_reset_local_cache()
{
    if (jrCore_local_cache_is_enabled()) {
        if ($_tmp = apcu_cache_info()) {
            if (isset($_tmp['cache_list']) && is_array($_tmp['cache_list'])) {
                foreach ($_tmp['cache_list'] as $c) {
                    apcu_delete($c['info']);
                }
                return true;
            }
        }
    }
    return false;
}

//-------------------------------------------
// Temp Value
//-------------------------------------------

/**
 * Temp: Save a Temp Value to the Temp DB
 * The jrCore_set_temp_value function will store a "value" for a "key"
 * for a given module.  It is guaranteed to NOT conflict with any other
 * module on the system (including the core).  This value can be retrieved
 * at a later point using jrCore_get_temp_value.
 *
 * @param string $module Module to store temp value for
 * @param string $key Key unique key for temp value
 * @param mixed $value Value to store - can be string or array
 * @return mixed Returns value on success, bool false on key does not exist
 */
function jrCore_set_temp_value($module, $key, $value)
{
    $tbl = jrCore_db_table_name('jrCore', 'tempvalue');
    $req = "INSERT INTO {$tbl} (temp_module,temp_key,temp_updated,temp_value)
            VALUES ('" . jrCore_db_escape($module) . "','" . jrCore_db_escape($key) . "',UNIX_TIMESTAMP(),'" . jrCore_db_escape(json_encode($value)) . "')
            ON DUPLICATE KEY UPDATE temp_updated = UNIX_TIMESTAMP(),temp_value = VALUES(temp_value)";
    $cnt = jrCore_db_query($req, 'COUNT', false, null, false);
    // 0 = no update
    // 1 = new row inserted
    // 2 = row updated
    if ($cnt && is_numeric($cnt)) {
        return true;
    }
    return false;
}

/**
 * Temp: Update a Temp Value in the Temp DB
 * The jrCore_update_temp_value function will return a "value" that has been
 * stored in the Temp Table, when given the KEY.  This is guaranteed to
 * be unique to the calling module.
 *
 * @param string $module Module that saved the temp value
 * @param string $key Key existing key to update
 * @param mixed $value New Value to store - can be string or array
 *
 * @return mixed Returns value (string or array) on success, bool false on key does not exist
 */
function jrCore_update_temp_value($module, $key, $value)
{
    $mod = jrCore_db_escape($module);
    $key = jrCore_db_escape($key);
    $val = jrCore_db_escape(json_encode($value));
    $tbl = jrCore_db_table_name('jrCore', 'tempvalue');
    $req = "UPDATE {$tbl} SET temp_updated = UNIX_TIMESTAMP(),temp_value = '{$val}' WHERE temp_module = '{$mod}' AND temp_key = '{$key}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT', false, null, false);
    if ($cnt && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Temp: Get a Temp Value from the Temp DB
 * The jrCore_get_temp_value function will return a "value" that has been
 * stored in the Temp Table, when given the KEY.  This is guaranteed to
 * be unique to the calling module.
 *
 * @param string $module Module that saved the temp value
 * @param string $key Key to retrieve
 *
 * @return mixed Returns value (string or array) on success, bool false on key does not exist
 */
function jrCore_get_temp_value($module, $key)
{
    $tbl = jrCore_db_table_name('jrCore', 'tempvalue');
    $req = "SELECT temp_value FROM {$tbl} WHERE temp_module = '" . jrCore_db_escape($module) . "' AND temp_key = '" . jrCore_db_escape($key) . "' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE', false, null, false);
    if ($_rt && is_array($_rt) && !empty($_rt['temp_value'])) {
        return json_decode($_rt['temp_value'], true);
    }
    return false;
}

/**
 * Temp: Delete an Existing Temp Value in the Temp DB
 * The jrCore_delete_temp_value function will delete a temp value that was
 * previously set by the jrCore_set_temp_value function.
 *
 * @param string $module Module that saved the temp value
 * @param string $key Key to delete
 * @return bool returns true if key is deleted, false if key is not found
 */
function jrCore_delete_temp_value($module, $key)
{
    $tbl = jrCore_db_table_name('jrCore', 'tempvalue');
    $req = "DELETE FROM {$tbl} WHERE temp_module = '" . jrCore_db_escape($module) . "' AND temp_key = '" . jrCore_db_escape($key) . "' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT', false, null, false);
    if ($cnt && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Temp: Cleanup old Temp Values in the Temp DB
 *
 * @param string $module Module that saved the temp value
 * @param int $safe_seconds number of seconds where of an entry is GREATER than it will be removed
 * @return int Returns number of Temp entries deleted
 */
function jrCore_clean_temp($module, $safe_seconds)
{
    if (jrCore_checktype($safe_seconds, 'number_nn')) {
        $tbl = jrCore_db_table_name('jrCore', 'tempvalue');
        $req = "DELETE FROM {$tbl} WHERE temp_module = '" . jrCore_db_escape($module) . "' AND temp_updated <= (UNIX_TIMESTAMP() - {$safe_seconds})";
        return jrCore_db_query($req, 'COUNT', false, null, false);
    }
    return false;
}

//-------------------------------------------
// Cache System
//-------------------------------------------

/**
 * Reset sprite caches
 * @return bool
 */
function jrCore_reset_sprite_cache()
{
    global $_conf;
    $dir = jrCore_get_module_cache_dir($_conf['jrCore_active_skin']);
    $_fl = glob("{$dir}/*sprite*", GLOB_NOSORT);
    if ($_fl && is_array($_fl)) {
        foreach ($_fl as $file) {
            unlink($file);  // OK
        }
    }
    $dir = jrCore_get_media_directory(0, FORCE_LOCAL);
    $_fl = glob("{$dir}/*sprite*", GLOB_NOSORT);
    if ($_fl && is_array($_fl)) {
        foreach ($_fl as $file) {
            unlink($file);  // OK
        }
    }
    return true;
}

/**
 * Reset template, CSS and JS cached files
 */
function jrCore_reset_template_cache()
{
    // When resetting template caches we delete:
    // - ALL JS and CSS files
    // - ALL file and string templates
    $_tmp = glob(APP_DIR . "/data/cache/*/{*.css,*.js,*.tpl*,*.string.php}", GLOB_BRACE | GLOB_NOSORT);
    if ($_tmp && is_array($_tmp)) {
        foreach ($_tmp as $file) {
            $file = realpath($file);
            if (strpos($file, APP_DIR . '/data/cache/') === 0) {
                unlink($file);  // OK
            }
        }
    }
    return true;
}

/**
 * Check if caching is enabled
 * @return bool
 */
function jrCore_caching_is_enabled()
{
    global $_conf;
    // Check to see if we are enabled
    if (isset($_conf['jrCore_default_cache_seconds']) && intval($_conf['jrCore_default_cache_seconds']) === 0) {
        return false;
    }
    // Check for developer mode
    if (jrCore_is_developer_mode()) {
        return false;
    }
    return true;
}

/**
 * Get configured cache plugins
 * @return array
 */
function jrCore_get_cache_system_plugins()
{
    return jrCore_get_system_plugins('cache');
}

/**
 * @ignore
 * jrCore_get_active_cache_system
 * @return string
 */
function jrCore_get_active_cache_system()
{
    global $_conf;
    if (isset($_conf['jrCore_active_cache_system']{1})) {
        // Make sure it is valid...
        $func = "_{$_conf['jrCore_active_cache_system']}_is_cached";
        if (function_exists($func)) {
            return $_conf['jrCore_active_cache_system'];
        }
    }
    return 'jrCore_mysql';
}

/**
 * Deletes the core Module and Config cache
 * @return bool
 */
function jrCore_delete_config_cache()
{
    // NOTE: Core config and settings is always stored in MySQL
    $key = jrCore_get_flag('jrcore_config_and_modules_key');
    jrCore_trigger_event('jrCore', 'delete_config_cache', array('jrcore_config_and_modules_key' => $key));
    return _jrCore_mysql_delete_cache('jrCore', $key);
}

/**
 * Delete all cache entries
 * @param $module string Optionally delete all cache entries for a specific module
 * @param $user_id int Optionally delete all cache entries for specific User ID
 * @return bool
 */
function jrCore_delete_all_cache_entries($module = null, $user_id = null)
{
    // Settings are ALWAYS cached - regardless of cache setting
    $temp = jrCore_get_active_cache_system();
    if (is_null($module) && is_null($user_id)) {
        jrCore_delete_config_cache();
    }
    if (!jrCore_caching_is_enabled()) {
        return true;
    }
    $func = "_{$temp}_delete_all_cache_entries";
    if (function_exists($func)) {
        jrCore_delete_all_cache_flags();
        return $func($module, $user_id);
    }
    jrCore_logger('CRI', "active cache system function: {$func} is not defined");
    return false;
}

/**
 * Delete all cache entries for a specific profile
 * @param $profile_id int Optionally delete all cache entries for specific User ID
 * @param $module string Optionally delete all cache entries for a specific module
 * @return bool
 */
function jrCore_delete_profile_cache_entries($profile_id = null, $module = null)
{
    global $_mods;
    if (!jrCore_caching_is_enabled()) {
        return true;
    }
    $pky = 'jrcore_process_exit_delete_profile_cache';
    $sky = 'jrcore_process_exit_delete_profile_skin_cache';
    if (!$_tm = jrCore_get_flag($pky)) {
        $_tm = array();
    }
    if (!$_sk = jrCore_get_flag($sky)) {
        $_sk = array();
    }
    $pid = (int) $profile_id;
    if (!isset($_tm[$pid])) {
        $_tm[$pid] = (is_null($module) || !isset($_mods[$module])) ? 0 : $module;
        $_sk[$pid] = $pid;
    }
    elseif ($_tm[$pid] != 0 && is_null($module)) {
        $_tm[$pid] = 0;
    }
    jrCore_set_flag($pky, $_tm);
    jrCore_set_flag($sky, $_sk);
    return true;
}

/**
 * Delete profile cache on exit
 * @return mixed
 */
function jrCore_process_exit_delete_profile_cache()
{
    if (!jrCore_caching_is_enabled()) {
        return true;
    }
    $key = 'jrcore_process_exit_delete_profile_cache';
    if ($_tm = jrCore_get_flag($key)) {
        $temp = jrCore_get_active_cache_system();
        $func = "_{$temp}_process_exit_delete_profile_cache";
        if (function_exists($func)) {
            if ($func($_tm)) {
                jrCore_delete_flag($key);
                return true;
            }
            return false;
        }
        jrCore_logger('CRI', "active cache system function: {$func} is not defined");
        return false;
    }
    return true;
}

/**
 * Delete cached skin templates that include expired profile id's
 * @return mixed
 */
function jrCore_process_exit_delete_profile_skin_cache()
{
    if (!jrCore_caching_is_enabled()) {
        return true;
    }
    $key = 'jrcore_process_exit_delete_profile_skin_cache';
    if ($_tm = jrCore_get_flag($key)) {
        $temp = jrCore_get_active_cache_system();
        $func = "_{$temp}_process_exit_delete_profile_skin_cache";
        if (function_exists($func)) {
            if ($func($_tm)) {
                jrCore_delete_flag($key);
                return true;
            }
            return false;
        }
        jrCore_logger('CRI', "active cache system function: {$func} is not defined");
        return false;
    }
    return true;
}

/**
 * Cache: Delete cache for a given key
 * @param string $module Module to save cache for
 * @param string $key Key to save cache for
 * @param bool $add_user By default each cache entry is for a specific User ID - set to false to override
 * @param bool $add_skin Add active skin name to unique cache key
 * @return bool
 */
function jrCore_delete_cache($module, $key, $add_user = true, $add_skin = true)
{
    if (!jrCore_caching_is_enabled()) {
        return true;
    }
    $temp = jrCore_get_active_cache_system();
    $func = "_{$temp}_delete_cache";
    if (function_exists($func)) {
        $key = jrCore_get_cache_key($module, $key, $add_user, $add_skin);
        jrCore_delete_cache_flag($key);
        return $func($module, $key);
    }
    jrCore_logger('CRI', "active cache system function: {$func} is not defined");
    return false;
}

/**
 * Cache: Delete multiple cache entries in 1 shot
 * @param $_items array consisting of 'module' (0), 'key' (1) and 'add_user' (2) params
 * @return bool
 */
function jrCore_delete_multiple_cache_entries($_items)
{
    if (!jrCore_caching_is_enabled()) {
        return true;
    }
    if (!is_array($_items) || count($_items) === 0) {
        // Nothing to delete
        return false;
    }
    $temp = jrCore_get_active_cache_system();
    $func = "_{$temp}_delete_multiple_cache_entries";
    if (function_exists($func)) {
        foreach ($_items as $k => $i) {
            if (!isset($i[2])) {
                $i[2] = true;
            }
            if (!isset($i[3])) {
                $i[3] = true;
            }
            // 0 = module, 1 = key, 2 = $add_user, 3 = $add_skin
            $_items[$k][1] = jrCore_get_cache_key($i[0], $i[1], $i[2], $i[3]);
            jrCore_delete_cache_flag($_items[$k][1]);
        }
        return $func($_items);
    }
    jrCore_logger('CRI', "active cache system function: {$func} is not defined");
    return false;
}

/**
 * Cache: Check if a given key is cached
 * @param string $module Module to save cache for
 * @param string $key Key to save cache for
 * @param bool $add_user By default each cache entry is for a specific User ID - set to false to override
 * @param bool $add_skin Add active skin name to unique cache key
 * @return mixed returns string on success, bool false on not cached
 */
function jrCore_is_cached($module, $key, $add_user = true, $add_skin = true)
{
    if (!jrCore_caching_is_enabled()) {
        return false;
    }
    $temp = jrCore_get_active_cache_system();
    $func = "_{$temp}_is_cached";
    if (function_exists($func)) {
        $key = jrCore_get_cache_key($module, $key, $add_user, $add_skin);
        if (!$out = jrCore_get_cache_flag($key)) {
            $out = $func($module, $key, $add_user);
            jrCore_set_cache_flag($key, jrCore_replace_emoji($out));
        }
        return $out;
    }
    jrCore_logger('CRI', "active cache system function: {$func} is not defined");
    return false;
}

/**
 * Cache: Cache a string for a given key
 * @param string $module Module doing the caching
 * @param string $key Unique key for cache item
 * @param mixed $value Value to cache
 * @param int $expire_seconds How long key will be cached for (in seconds)
 * @param int $profile_id Profile ID cache item belongs to
 * @param bool $add_user By default each cache entry is for a specific User ID - set to false to override
 * @param bool $add_skin Add active skin to unique cache key
 * @return mixed returns string on success, bool false on not cached
 */
function jrCore_add_to_cache($module, $key, $value, $expire_seconds = 0, $profile_id = 0, $add_user = true, $add_skin = true)
{
    if (!jrCore_caching_is_enabled()) {
        return true;
    }
    $temp = jrCore_get_active_cache_system();
    $func = "_{$temp}_add_to_cache";
    if (function_exists($func)) {
        $uniq = null;
        if ($_ci = jrCore_get_flag('datastore_cache_profile_ids')) {
            if (count($_ci) < 50) {
                $uniq = implode(',', $_ci);
            }
            unset($_ci);
        }
        $key = jrCore_get_cache_key($module, $key, $add_user, $add_skin);
        return $func($module, $key, jrCore_strip_emoji($value), $expire_seconds, $profile_id, $add_user, $uniq);
    }
    jrCore_logger('CRI', "active cache system function: {$func} is not defined");
    return false;
}

/**
 * Generate an Internal MD5 Cache key from params
 * @param $module string Module cache key is being created for
 * @param $key string Key to create new key from
 * @param $add_user bool make key unique for calling user
 * @param $add_skin bool make key unique for active skin
 * @return string
 */
function jrCore_get_cache_key($module, $key, $add_user = true, $add_skin = true)
{
    global $_conf;
    $key = trim($key);
    // See if we are adding unique User items to the key
    if ($add_user) {

        // Get unique user id
        $uid = 0;
        $lng = 'en-US';
        $dvc = 'desktop';
        if (isset($_SESSION['_user_id'])) {
            $uid = (int) $_SESSION['_user_id'];
            $lng = (isset($_SESSION['user_language'])) ? $_SESSION['user_language'] : 'en-US';
        }
        if (jrCore_is_mobile_device()) {
            $dvc = 'mobile';
        }
        elseif (jrCore_is_tablet_device()) {
            $dvc = 'tablet';
        }
        if ($uid > 0 && !isset($_SESSION['jrdtype']{2})) {
            $_SESSION['jrdtype'] = $dvc;
        }
        $key = "{$module}-{$lng}-{$key}-{$uid}-{$dvc}";
    }
    if ($add_skin) {
        return md5(jrCore_get_server_protocol() . "-{$_conf['jrCore_base_url']}-{$_conf['jrCore_active_skin']}-" . $key);
    }
    return md5(jrCore_get_server_protocol() . "-{$_conf['jrCore_base_url']}" . $key);
}

/**
 * Get unique key for a FULL PAGE cache request
 * @return string
 */
function jrCore_get_full_page_cache_key()
{
    global $_conf;
    $key = $_conf['jrCore_active_skin'];
    $key .= (isset($_REQUEST['_uri']{0})) ? $_REQUEST['_uri'] : '/';
    if (jrCore_is_mobile_device()) {
        $key .= 'm';
    }
    elseif (jrCore_is_tablet_device()) {
        $key .= 't';
    }
    return "{$key}_full_page";
}

/**
 * Perform maintenance on the cache system
 * @return bool
 */
function jrCore_cache_maintenance()
{
    if (!jrCore_caching_is_enabled()) {
        return true;
    }
    $temp = jrCore_get_active_cache_system();
    $func = "_{$temp}_cache_maintenance";
    if (function_exists($func)) {
        return $func();
    }
    jrCore_logger('CRI', "active cache system function: {$func} is not defined");
    return false;
}

/**
 * Set a new temp global cache flag
 * @param string $flag Unique flag string to set value for
 * @param mixed $value Value to store
 * @return bool
 */
function jrCore_set_cache_flag($flag, $value)
{
    $GLOBALS['__JR_CACHE'][$flag] = $value;
    return true;
}

/**
 * Retrieve a previously set temp global cache flag
 * @param mixed $flag String or Array to save to flag
 * @return mixed
 */
function jrCore_get_cache_flag($flag)
{
    return (isset($GLOBALS['__JR_CACHE'][$flag])) ? $GLOBALS['__JR_CACHE'][$flag] : false;
}

/**
 * delete a previously set temp global cache flag
 * @param mixed $flag String or Array to delete
 * @return bool
 */
function jrCore_delete_cache_flag($flag)
{
    if (isset($GLOBALS['__JR_CACHE'][$flag])) {
        unset($GLOBALS['__JR_CACHE'][$flag]);
        return true;
    }
    return false;
}

/**
 * Delete all set global cache flags
 * @return bool
 */
function jrCore_delete_all_cache_flags()
{
    unset($GLOBALS['__JR_CACHE']);
    return true;
}

//-------------------------------------------
// MySQL Cache Plugins
//-------------------------------------------

/**
 * Internal jrCore_delete_all_cache_entries() plugin
 * @ignore
 * @param $module string Optionally delete all cache entries for specific module
 * @param $user_id int Optionally delete all cache entries for a specific user_id
 * @return bool
 */
function _jrCore_mysql_delete_all_cache_entries($module = null, $user_id = null)
{
    $tbl = jrCore_db_table_name('jrCore', 'cache');
    if (is_null($module) && is_null($user_id)) {

        // Truncate table
        $req = "TRUNCATE TABLE {$tbl}";
        jrCore_db_query($req);

        // Make sure cache_key column is correct
        $req = "ALTER TABLE {$tbl} CHANGE `cache_key` `cache_key` CHAR(32) CHARACTER SET latin1 NOT NULL";
        jrCore_db_query($req);

    }
    else {
        $req = "DELETE FROM {$tbl} ";
        if (!is_null($module)) {
            $req .= "WHERE cache_module = '" . jrCore_db_escape($module) . "'";
            if (!is_null($user_id)) {
                $req .= " AND cache_user_id = '" . intval($user_id) . "'";
            }
        }
        else {
            $req .= "WHERE cache_user_id = '" . intval($user_id) . "'";
        }
    }
    jrCore_db_query($req);
    return true;
}

/**
 * Core MySQL cache function to reset profile cache
 * @param $_profile_ids array Array of profile_id => module entries to delete
 * @ignore
 * @return bool
 */
function _jrCore_mysql_process_exit_delete_profile_cache($_profile_ids)
{
    $tbl = jrCore_db_table_name('jrCore', 'cache');
    $_in = array();
    foreach ($_profile_ids as $pid => $mod) {
        if ($mod != 0) {
            // This is a profile reset for a specific module
            $_in[] = "(cache_profile_id = {$pid} AND cache_module = '{$mod}')";
        }
        else {
            $_in[] = "(cache_profile_id = {$pid})";
        }
    }
    if (count($_in) > 0) {
        $req = "DELETE FROM {$tbl} WHERE " . implode(' OR ', $_in);
        jrCore_db_query($req);
    }
    return true;
}

/**
 * Core MySQL cache function to delete skin cache for cleared profile_ids
 * @param $_profile_ids array Array of profile_id => module entries to delete
 * @ignore
 * @return bool
 */
function _jrCore_mysql_process_exit_delete_profile_skin_cache($_profile_ids)
{
    $tbl = jrCore_db_table_name('jrCore', 'cache');
    $_in = array();
    foreach ($_profile_ids as $pid => $mod) {
        $_in[] = "(cache_item_id LIKE '%,{$pid},%')";
    }
    if (count($_in) > 0) {
        $req = "DELETE FROM {$tbl} WHERE " . implode(' OR ', $_in);
        jrCore_db_query($req);
    }
    return true;
}

/**
 * Cache: Delete cache for a given key
 * @param string $module Module to save cache for
 * @param string $key Key to save cache for
 * @return bool
 */
function _jrCore_mysql_delete_cache($module, $key)
{
    $tbl = jrCore_db_table_name('jrCore', 'cache');
    $req = "DELETE FROM {$tbl} WHERE cache_key = '{$key}'";
    jrCore_db_query($req);
    return true;
}

/**
 * Cache: Delete cache for multiple module/keys
 * @param array $_items Cache Items to delete
 * @return bool
 */
function _jrCore_mysql_delete_multiple_cache_entries($_items)
{
    if (is_array($_items)) {
        $_ky = array();
        foreach ($_items as $v) {
            $_ky[] = "cache_key = '" . $v[1] . "'";
        }
        $tbl = jrCore_db_table_name('jrCore', 'cache');
        $req = "DELETE FROM {$tbl} WHERE " . implode(' OR ', $_ky);
        jrCore_db_query($req);
        return true;
    }
    return false;
}

/**
 * Cache: Check if a given key is cached
 * @param string $module Module to save cache for
 * @param string $key Key to save cache for
 * @param bool $add_user By default each cache entry is for a specific User ID - set to false to override
 * @return mixed returns string on success, bool false on not cached
 */
function _jrCore_mysql_is_cached($module, $key, $add_user = true)
{
    $tbl = jrCore_db_table_name('jrCore', 'cache');
    $req = "SELECT UNIX_TIMESTAMP() AS db_time, cache_expires, cache_encoded, cache_value FROM {$tbl} WHERE cache_key = '{$key}'";
    $_rt = jrCore_db_query($req, 'SINGLE', false, null, false, null, false, true);
    if ($_rt && isset($_rt['cache_value'])) {
        // See if we have expired...
        if ($_rt['cache_expires'] < $_rt['db_time']) {
            // Update so we avoid a stampede
            // and return false so we rebuild in this process
            $req = "UPDATE {$tbl} SET cache_expires = (cache_expires + 30) WHERE cache_key = '{$key}'";
            jrCore_db_query($req);
            return false;
        }
        switch ($_rt['cache_encoded']) {
            case '1':
                // Array
                $_rt['cache_value'] = json_decode($_rt['cache_value'], true);
                break;
            case '2':
                // Object
                $_rt['cache_value'] = json_decode($_rt['cache_value']);
                break;
        }
        return $_rt['cache_value'];
    }
    return false;
}

/**
 * Delete expired cache entries from the cache table
 */
function _jrCore_mysql_cache_maintenance()
{
    // Delete expired cache entries
    @ini_set('memory_limit', '256M');
    $tbl = jrCore_db_table_name('jrCore', 'cache');

    // On slower disk based systems this query can be a bit slower
    // but we are in process_exit so no need to complain about it
    $_rt = jrCore_db_query("SHOW VARIABLES LIKE '%long_query_time%'", 'SINGLE');
    $val = 10;
    if ($_rt && isset($_rt['Value']) && strlen($_rt['Value']) > 0) {
        $val = round($_rt['Value']);
    }
    // Delete all expired cache entries
    jrCore_db_query("SET SESSION long_query_time = 100");
    $_dl = jrCore_db_query("SELECT cache_key AS k, '1' AS n FROM {$tbl} WHERE cache_expires < UNIX_TIMESTAMP()", 'k', false, 'n');
    if ($_dl && is_array($_dl)) {
        jrCore_db_query("DELETE LOW_PRIORITY FROM {$tbl} WHERE cache_key IN('" . implode("','", array_keys($_dl)) . "')");
    }
    jrCore_db_query("SET SESSION long_query_time = {$val}");
    return true;
}

/**
 * Cache: Cache a string for a given key
 * @param string $module Module doing the caching
 * @param string $key Unique key for cache item
 * @param mixed $value Value to cache
 * @param int $expire_seconds How long key will be cached for (in seconds)
 * @param int $profile_id Profile ID cache item belongs to
 * @param bool $add_user By default each cache entry is for a specific User ID - set to false to override
 * @param string $unique Unique Module-Item_IDs (set in DataStore)
 * @return mixed returns string on success, bool false on not cached
 */
function _jrCore_mysql_add_to_cache($module, $key, $value, $expire_seconds = 0, $profile_id = 0, $add_user = true, $unique = null)
{
    global $_post, $_conf;
    if (!$expire_seconds || $expire_seconds === 0) {
        $expire_seconds = $_conf['jrCore_default_cache_seconds'];
    }
    $expire_seconds = intval($expire_seconds);
    if ($expire_seconds === 0) {
        return true;
    }

    // Check if we are encoding this in the DB
    $enc = 0;
    if (is_array($value)) {
        $value = json_encode($value);
        $enc   = 1;
    }
    elseif (is_object($value)) {
        $value = json_encode($value);
        $enc   = 2;
    }

    $pid = 0;
    if ($profile_id == 0) {
        if ($tmp = jrCore_get_flag('jrprofile_view_is_active')) {
            $pid = $tmp;
        }
        elseif (isset($_post['_profile_id'])) {
            $pid = (int) $_post['_profile_id'];
        }
    }
    else {
        $pid = (int) $profile_id;
    }

    $unq = '';
    if ($pid > 0 && !is_null($unique) && $unique != $pid) {
        $unq = jrCore_db_escape(",{$unique},");
    }

    $uid = (isset($_SESSION['_user_id'])) ? (int) $_SESSION['_user_id'] : 0;
    $val = jrCore_db_escape($value);
    $tbl = jrCore_db_table_name('jrCore', 'cache');
    $req = "INSERT INTO {$tbl} (cache_key,cache_expires,cache_module,cache_profile_id,cache_user_id,cache_item_id,cache_encoded,cache_value)
            VALUES ('{$key}',(UNIX_TIMESTAMP() + {$expire_seconds}),'{$module}','{$pid}','{$uid}','{$unq}','{$enc}','{$val}')
            ON DUPLICATE KEY UPDATE cache_expires = VALUES(cache_expires), cache_encoded = '{$enc}', cache_value = VALUES(cache_value), cache_item_id = VALUES(cache_item_id)";
    $cnt = jrCore_db_query($req, 'COUNT', false, null, false);
    if (!$cnt || $cnt < 1 || $cnt > 2) {
        return false;
    }
    return true;
}
