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
 * @package DataStore
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

// Constants
define('SKIP_TRIGGERS', true);
define('NO_CACHE', true);

/**
 * @ignore
 * jrCore_get_datastore_plugins
 * @return array
 */
function jrCore_get_datastore_plugins()
{
    return jrCore_get_system_plugins('datastore');
}

/**
 * Get the active datastore function
 * @param $module string DS module
 * @param $function string DS function to run
 * @return string
 */
function jrCore_get_active_datastore_function($module, $function)
{
    global $_conf;
    if (isset($_conf["jrCore_active_datastore_system_{$module}"]{1})) {
        if (function_exists($function)) {
            return '_' . $_conf["jrCore_active_datastore_system_{$module}"] . '_' . $function;
        }
    }
    if (isset($_conf['jrCore_active_datastore_system']{1})) {
        if (function_exists($function)) {
            return '_' . $_conf['jrCore_active_datastore_system'] . '_' . $function;
        }
    }
    return '_jrCore_' . $function;
}

/**
 * An array of modules that have a datastore enabled
 */
function jrCore_get_datastore_modules()
{
    global $_mods;
    $_out = array();
    foreach ($_mods as $module => $_inf) {
        if (isset($_inf['module_prefix']) && strlen($_inf['module_prefix']) > 0) {
            $_out[$module] = $_inf['module_prefix'];
        }
    }
    return $_out;
}

/**
 * Return TRUE if given module is a DS module
 * @param $module string module
 * @return bool
 */
function jrCore_is_datastore_module($module)
{
    global $_mods;
    return (isset($_mods[$module]['module_prefix']) && strlen($_mods[$module]['module_prefix']) > 0) ? true : false;
}

/**
 * Returns DataStore Prefix for a module
 * @param string $module Module to return prefix for
 * @return mixed
 */
function jrCore_db_get_prefix($module)
{
    global $_mods;
    if (isset($_mods[$module]['module_prefix']) && strlen($_mods[$module]['module_prefix']) > 0) {
        return $_mods[$module]['module_prefix'];
    }
    elseif ($_tmp = jrCore_get_flag('jrcore_db_create_datastore_prefixes')) {
        if (isset($_tmp[$module])) {
            return $_tmp[$module];
        }
    }
    return false;
}

/**
 * Get all index tables for a module
 * @param string $module
 * @return array|bool
 */
function jrCore_db_get_all_index_tables_for_module($module)
{
    global $_conf;
    if (isset($_conf['jrCore_index_keys']) && strlen($_conf['jrCore_index_keys']) > 0) {
        if ($_tm = explode(',', trim(trim($_conf['jrCore_index_keys'], ',')))) {
            $_tb = array();
            foreach ($_tm as $v) {
                if (strpos($v, "{$module}:") === 0) {
                    list(, $k) = explode(':', $v);
                    $k       = trim($k);
                    $_tb[$k] = $k;
                }
            }
            return $_tb;
        }
    }
    return false;
}

/**
 * Returns true if a key has an index table
 * @param string $module
 * @param string $key
 * @return bool
 */
function jrCore_db_key_has_index_table($module, $key)
{
    global $_conf;
    if ($key == '_item_id') {
        return false;
    }
    if (isset($_conf['jrCore_index_keys']) && strlen($_conf['jrCore_index_keys']) > 0 && strpos(' ' . $_conf['jrCore_index_keys'], ",{$module}:{$key},")) {
        return true;
    }
    return false;
}

/**
 * Get a table name for an index table
 * @param string $module
 * @param string $key
 * @param bool $key_name_only
 * @return bool|string
 */
function jrCore_db_get_index_table_name($module, $key, $key_name_only = false)
{
    // Name of table will be: jr_<module>_item_key_<key_name_without_prefix>
    // NOTE: Max length of table name is 64 characters
    if (!$pfx = jrCore_db_get_prefix($module)) {
        return false;
    }
    $name = $key;
    if (strpos($key, $pfx) === 0) {
        $name = substr($key, strlen($pfx) + 1);
    }
    if (strlen($name) > 54) {
        $name = substr($name, 0, 55);
    }
    if ($key_name_only) {
        return "item_key_{$name}";
    }
    return jrCore_db_table_name($module, "item_key_{$name}");
}

/**
 * Creates a new module DataStore
 * @param string $module Module to create DataStore for
 * @param string $prefix Key Prefix in DataStore
 * @return bool
 */
function jrCore_db_create_datastore($module, $prefix)
{
    if (strlen($prefix) === 0) {
        jrCore_logger('CRI', "Invalid datastore module_prefix for module: {$module}");
        return false;
    }
    $func = jrCore_get_active_datastore_function($module, 'db_create_datastore');
    if (function_exists($func)) {
        return $func($module, $prefix);
    }
    return true;
}

/**
 * Creates a new module DataStore
 * @param string $module Module to create DataStore for
 * @param string $prefix Key Prefix in DataStore
 * @return bool
 */
function _jrCore_db_create_datastore($module, $prefix)
{
    // Items
    $_tmp = array(
        "`_item_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY"
    );
    jrCore_db_verify_table($module, 'item', $_tmp, 'InnoDB');

    // Item
    $_tmp = array(
        "`_item_id` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "`_profile_id` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "`key` VARCHAR(128) NOT NULL DEFAULT ''",
        "`index` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0'",
        "`value` VARCHAR(512) NOT NULL DEFAULT ''",
        "PRIMARY KEY (`key`, `_item_id`, `index`)",
        "INDEX `_item_id` (`_item_id`)",
        "INDEX `_profile_id` (`_profile_id`)",
        "INDEX `value` (`value`(128))",
    );
    jrCore_db_verify_table($module, 'item_key', $_tmp, 'InnoDB');

    // Validate _profile_id
    jrCore_db_sync_datastore_profile_ids($module);

    // Make sure our DataStore prefix is stored with the module info
    $efx = jrCore_db_get_prefix($module);
    if (!$efx || $efx != $prefix || strlen($efx) === 0) {
        $tbl = jrCore_db_table_name('jrCore', 'module');
        $req = "UPDATE {$tbl} SET module_prefix = '" . jrCore_db_escape($prefix) . "' WHERE module_directory = '" . jrCore_db_escape($module) . "' LIMIT 1";
        jrCore_db_query($req, 'COUNT');
    }

    // Lastly, if this DS is being created in a jrCore_verify_module, and the
    // module has an install.php script, the prefix won't be available in $_mods
    // until cache is reset and the page reloaded, so put it in a tmp place
    if ($_tmp = jrCore_get_flag('jrcore_db_create_datastore_prefixes')) {
        $_tmp[$module] = $prefix;
    }
    else {
        $_tmp = array($module => $prefix);
    }
    jrCore_set_flag('jrcore_db_create_datastore_prefixes', $_tmp);

    // Let modules know we are creating/validating a DataStore
    $_args = array(
        'module' => $module,
        'prefix' => $prefix
    );
    jrCore_trigger_event('jrCore', 'db_create_datastore', array(), $_args);

    return true;
}

/**
 * Deletes an existing module datastore
 * @param string $module Module to delete DataStore for
 * @return bool
 */
function jrCore_db_delete_datastore($module)
{
    $func = jrCore_get_active_datastore_function($module, 'db_delete_datastore');
    if (function_exists($func)) {
        if ($func($module)) {
            jrCore_trigger_event('jrCore', 'db_delete_datastore', array('module' => $module));
            return true;
        }
    }
    return true;
}

/**
 * Deletes an existing module datastore
 * @param string $module Module to delete DataStore for
 * @return bool
 */
function _jrCore_db_delete_datastore($module)
{
    $tbl = jrCore_db_table_name($module, 'item');
    $req = "DROP TABLE IF EXISTS {$tbl}";
    jrCore_db_query($req);

    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "DROP TABLE IF EXISTS {$tbl}";
    jrCore_db_query($req);
    return true;
}

/**
 * Migrate profile_ids in datastore
 * @param $module string
 * @returns int
 */
function jrCore_db_sync_datastore_profile_ids($module)
{
    global $_mods;
    $cnt = 0;
    $num = jrCore_db_number_rows($module, 'item');
    if ($num > 0) {

        // Has this DS been setup properly?
        $_ti = jrCore_db_table_columns($module, 'item_key');
        if ($_ti && is_array($_ti) && isset($_ti['_profile_id'])) {

            // Fix up DS items where the _profile_id has not been set
            $tmp = false;
            $tbl = jrCore_db_table_name($module, 'item_key');
            while (true) {
                $req = "SELECT `_item_id`, `value` FROM {$tbl} WHERE `key` = '_profile_id' AND `value` > 0 AND (`_profile_id` = 0 OR `_profile_id` != `value`) LIMIT 1000";
                $_rt = jrCore_db_query($req, '_item_id', false, 'value');
                if ($_rt && is_array($_rt)) {
                    if ($num > 5000 && !$tmp) {
                        jrCore_form_modal_notice('update', "applying changes: " . $_mods[$module]['module_name'] . ' - this could take a bit - ' . jrCore_number_format($num) . ' items');
                        $tmp = true;
                    }
                    foreach ($_rt as $k => $v) {
                        if (is_numeric($v)) {
                            jrCore_db_query("UPDATE {$tbl} SET `_profile_id` = {$v} WHERE `_item_id` = {$k}");
                            $cnt++;
                        }
                    }
                    if ($tmp) {
                        jrCore_form_modal_notice('update', "applying changes: " . $_mods[$module]['module_name'] . ' - updated ' . jrCore_number_format($cnt) . ' datastore items');
                    }
                    if (count($_rt) < 1000) {
                        // No more items
                        break;
                    }
                }
                else {
                    break;
                }
            }
            if ($cnt > 0) {
                jrCore_logger('INF', "updated " . jrCore_number_format($cnt) . " {$module} datastore items with correct _profile_id");
            }
        }
    }
    return $cnt;
}

/**
 * Repair datastore items for a module
 * @param string $module module to repair
 * @return bool
 */
function jrCore_db_repair_datastore_items($module)
{
    // Find items that are valid items with keys that have _profile_id set to 0
    global $_mods;
    $cnt = 0;
    $num = jrCore_db_number_rows($module, 'item');
    if ($num > 0) {
        // Has this DS been setup properly?
        $_ti = jrCore_db_table_columns($module, 'item_key');
        if ($_ti && is_array($_ti) && isset($_ti['_profile_id'])) {

            $tbl = jrCore_db_table_name($module, 'item_key');
            while (true) {
                $req = "SELECT DISTINCT(`_item_id`) AS i FROM {$tbl} WHERE `_profile_id` = 0 AND `_item_id` IN(SELECT `_item_id` FROM {$tbl} WHERE `key` = '_profile_id' AND `value` > 0) LIMIT 1000";
                $_rt = jrCore_db_query($req, 'i', false, 'i');
                if ($_rt && is_array($_rt)) {
                    // We have items that have keys that have _profile_id set to 0 - get _profile_id for these items
                    $req = "SELECT `_item_id` AS i, `value` AS v FROM {$tbl} WHERE `key` = '_profile_id' AND `value` > 0 AND `_item_id` IN(" . implode(',', $_rt) . ')';
                    $_ii = jrCore_db_query($req, 'i', false, 'v');
                    if ($_ii && is_array($_ii)) {
                        jrCore_form_modal_notice('update', "applying updates: " . $_mods[$module]['module_name'] . ' - ' . jrCore_number_format(count($_rt)) . ' items with keys missing _profile_id');
                        foreach ($_rt as $k => $v) {
                            if (isset($_ii[$k])) {
                                jrCore_db_query("UPDATE {$tbl} SET `_profile_id` = " . $_ii[$k] . " WHERE `_item_id` = {$k}");
                                $cnt++;
                            }
                        }
                    }
                    if (count($_rt) < 1000) {
                        // No more items
                        break;
                    }
                }
                else {
                    // No items
                    break;
                }
            }
            if ($cnt > 0) {
                jrCore_logger('INF', "updated " . jrCore_number_format($cnt) . " {$module} datastore item keys with correct _profile_id");
            }

            // Do we have any left over?  If so these are likely bad DS items - left over keys
        }
    }
    return true;
}

/**
 * Run repair operations on a DataStore
 * @param string $module
 * @return bool
 */
function jrCore_db_repair_datastore($module)
{
    if (jrCore_is_datastore_module($module)) {
        $func = jrCore_get_active_datastore_function($module, 'db_repair_datastore');
        if (function_exists($func)) {
            return $func($module);
        }
    }
    return true;
}

/**
 * Run repair operations on a DataStore
 * @param string $module
 * @return bool
 */
function _jrCore_db_repair_datastore($module)
{
    // There are 2 indexes that are no longer needed in older DS's
    $_in = jrCore_db_get_table_indexes($module, 'item_key');
    if ($_in && is_array($_in) && count($_in) > 0) {
        $tbl = jrCore_db_table_name($module, 'item_key');
        foreach (array('index', 'key') as $idx) {
            if (isset($_in[$idx])) {
                $req = "DROP INDEX `{$idx}` ON {$tbl}";
                jrCore_db_query($req);
            }
        }
    }
    return true;
}

/**
 * Truncate and reset a module DataStore
 * @param $module string Module DataStore to truncate
 * @return bool
 */
function jrCore_db_truncate_datastore($module)
{
    $func = jrCore_get_active_datastore_function($module, 'db_truncate_datastore');
    if ($func($module)) {
        jrCore_trigger_event('jrCore', 'db_truncate_datastore', array('module' => $module));
        return true;
    }
    return false;
}

/**
 * Core DS Plugin
 * @param $module
 * @return bool
 */
function _jrCore_db_truncate_datastore($module)
{
    $tbl = jrCore_db_table_name($module, 'item');
    $req = "TRUNCATE TABLE {$tbl}";
    jrCore_db_query($req);
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "TRUNCATE TABLE {$tbl}";
    jrCore_db_query($req);
    return true;
}

/**
 * Get number of items in a module DataStore
 * @param $module string Module to get number of items for
 * @return int
 */
function jrCore_db_get_datastore_item_count($module)
{
    $func = jrCore_get_active_datastore_function($module, 'db_get_datastore_item_count');
    return $func($module);
}

/**
 * Core DS Plugin
 * @param $module
 * @return int
 */
function _jrCore_db_get_datastore_item_count($module)
{
    return jrCore_db_number_rows($module, 'item');
}

/**
 * Run a key "function" in matching values
 * @param $module string Module DataStore
 * @param $key string Key to match
 * @param $match string Value to match - '*' for all
 * @param $function string function to run on key values (sum, avg, min, max, std, count)
 * @param $group_by_value bool set to TRUE to group by value
 * @return mixed
 */
function jrCore_db_run_key_function($module, $key, $match, $function, $group_by_value = false)
{
    $func = jrCore_get_active_datastore_function($module, 'db_run_key_function');
    return $func($module, $key, $match, $function, $group_by_value);
}

/**
 * Core DS Plugin
 * @param $module
 * @param $key
 * @param $match
 * @param $function
 * @param $group_by_value
 * @return bool
 */
function _jrCore_db_run_key_function($module, $key, $match, $function, $group_by_value = false)
{
    switch (strtolower($function)) {
        case 'sum':
        case 'avg':
        case 'min':
        case 'max':
        case 'std':
            $fnc = strtoupper($function) . '(`value`)';
            break;
        case 'count':
            $fnc = 'COUNT(`_item_id`)';
            break;
        default:
            return false;
            break;
    }
    $tbl = jrCore_db_table_name($module, 'item_key');
    if ($match == '*') {
        if (jrCore_db_key_has_index_table($module, $key)) {
            $tbl = jrCore_db_get_index_table_name($module, $key);
            $req = "SELECT {$fnc} AS tc, `_item_id`, `value` FROM {$tbl}";
        }
        else {
            $req = "SELECT {$fnc} AS tc, `_item_id`, `value` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "'";
        }
    }
    elseif (strpos(' ' . $match, '%')) {
        if (jrCore_db_key_has_index_table($module, $key)) {
            $tbl = jrCore_db_get_index_table_name($module, $key);
            $req = "SELECT {$fnc} AS tc, `_item_id`, `value` FROM {$tbl} WHERE `value` LIKE '" . jrCore_db_escape($match) . "'";
        }
        else {
            $req = "SELECT {$fnc} AS tc, `_item_id`, `value` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "' AND `value` LIKE '" . jrCore_db_escape($match) . "'";
        }
    }
    elseif (strpos($match, '<') === 0) {
        if (jrCore_db_key_has_index_table($module, $key)) {
            $tbl = jrCore_db_get_index_table_name($module, $key);
            $req = "SELECT {$fnc} AS tc, `_item_id`, `value` FROM {$tbl} WHERE `value` " . jrCore_db_escape($match);
        }
        else {
            $req = "SELECT {$fnc} AS tc, `_item_id`, `value` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "' AND `value` " . jrCore_db_escape($match);
        }
    }
    elseif (strpos($match, '>') === 0) {
        if (jrCore_db_key_has_index_table($module, $key)) {
            $tbl = jrCore_db_get_index_table_name($module, $key);
            $req = "SELECT {$fnc} AS tc, `_item_id`, `value` FROM {$tbl} WHERE `value` " . jrCore_db_escape($match);
        }
        else {
            $req = "SELECT {$fnc} AS tc, `_item_id`, `value` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "' AND `value` " . jrCore_db_escape($match);
        }
    }
    elseif (stripos($match, 'IN') === 0) {
        if (jrCore_db_key_has_index_table($module, $key)) {
            $tbl = jrCore_db_get_index_table_name($module, $key);
            $req = "SELECT {$fnc} AS tc, `_item_id`, `value` FROM {$tbl} WHERE `value` " . jrCore_db_escape($match);
        }
        else {
            $req = "SELECT {$fnc} AS tc, `_item_id`, `value` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "' AND `value` " . jrCore_db_escape($match);
        }
    }
    else {
        if (jrCore_db_key_has_index_table($module, $key)) {
            $tbl = jrCore_db_get_index_table_name($module, $key);
            $req = "SELECT {$fnc} AS tc, `_item_id`, `value` FROM {$tbl} WHERE `value` = '" . jrCore_db_escape($match) . "'";
        }
        else {
            $req = "SELECT {$fnc} AS tc, `_item_id`, `value` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "' AND `value` = '" . jrCore_db_escape($match) . "'";
        }
    }

    if (!$group_by_value) {
        $_rt = jrCore_db_query($req, 'SINGLE');
        if ($_rt && is_array($_rt)) {
            return $_rt['tc'];
        }
    }
    else {
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt)) {
            return $_rt;
        }
    }
    return false;
}

/**
 * Set the special "display_order" keys for items in a DataStore
 * @param $module string Module DataStore to set values in
 * @param $_ids array Array of id => value entries
 * @return bool
 */
function jrCore_db_set_display_order($module, $_ids)
{
    if (!is_array($_ids)) {
        return false;
    }
    $func = jrCore_get_active_datastore_function($module, 'db_set_display_order');
    return $func($module, $_ids);
}

/**
 * Core DS Plugin
 * @param $module
 * @param $_ids
 * @return bool
 */
function _jrCore_db_set_display_order($module, $_ids)
{
    $_pi = array();
    $_rt = jrCore_db_get_multiple_items($module, array_keys($_ids), array('_item_id', '_profile_id'));
    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $k => $v) {
            $iid       = (int) $v['_item_id'];
            $_pi[$iid] = (int) $v['_profile_id'];
        }
    }
    $pfx = jrCore_db_get_prefix($module);
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "INSERT INTO {$tbl} (`_item_id`,`_profile_id`,`key`,`index`,`value`) VALUES ";
    foreach ($_ids as $iid => $ord) {
        $ord = (int) $ord;
        $iid = (int) $iid;
        $pid = (int) $_pi[$iid];
        $req .= "({$iid},{$pid},'{$pfx}_display_order',0,'{$ord}'),";
    }
    $req = substr($req, 0, strlen($req) - 1) . " ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
    jrCore_db_query($req, null, false, null, false);

    // If display_order is setup as a key index - rebuild
    if (jrCore_db_key_has_index_table($module, "{$pfx}_display_order")) {
        $tbl = jrCore_db_get_index_table_name($module, "{$pfx}_display_order");
        $req = "INSERT INTO {$tbl} (`_item_id`,`value`) VALUES ";
        foreach ($_ids as $iid => $ord) {
            $ord = (int) $ord;
            $iid = (int) $iid;
            $req .= "({$iid},{$ord}),";
        }
        $req = substr($req, 0, strlen($req) - 1) . " ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
        jrCore_db_query($req, null, false, null, false);
    }

    return true;
}

/**
 * Create a new key for all entries in a DataStore and set it to a default value
 * @param $module string Module DataStore to create new key in
 * @param $key string Key to create
 * @param $value mixed initial value
 * @return bool
 */
function jrCore_db_create_default_key($module, $key, $value)
{
    $func = jrCore_get_active_datastore_function($module, 'db_create_default_key');
    return $func($module, $key, $value);
}

/**
 * Core DS Plugin
 * @param $module
 * @param $key
 * @param $value
 * @return mixed
 */
function _jrCore_db_create_default_key($module, $key, $value)
{
    $tbl = jrCore_db_table_name($module, 'item_key');
    $key = jrCore_db_escape($key);
    $val = jrCore_db_escape($value);
    $req = "INSERT IGNORE INTO {$tbl} (`_item_id`,`_profile_id`,`key`,`value`) SELECT DISTINCT(`_item_id`),`_profile_id`,'{$key}','{$val}' FROM {$tbl} WHERE `key` = '_created' AND `_item_id` > 0";
    return jrCore_db_query($req, 'COUNT', false, null, false);
}

/**
 * Create a new key for all entries in a DataStore and set it to a default value
 * @param $module string Module DataStore to create new key in
 * @param $key string Key to create
 * @param $value mixed value to set keys to
 * @param $default mixed if a value is set set to $default, it will be changed to $value
 * @return bool
 */
function jrCore_db_update_default_key($module, $key, $value, $default)
{
    $func = jrCore_get_active_datastore_function($module, 'db_update_default_key');
    return $func($module, $key, $value, $default);
}

/**
 * Core DS Plugin
 * @param $module
 * @param $key
 * @param $value
 * @param $default
 * @return mixed
 */
function _jrCore_db_update_default_key($module, $key, $value, $default)
{
    $tbl = jrCore_db_table_name($module, 'item_key');
    $key = jrCore_db_escape($key);
    $val = jrCore_db_escape($value);
    $def = jrCore_db_escape($default);
    $req = "UPDATE {$tbl} SET `value` = '{$val}' WHERE `key` = '{$key}' AND (`value` IS NULL OR `value` = '' OR `value` = '{$def}')";
    return jrCore_db_query($req, 'COUNT');
}

/**
 * Increment a DataStore key for an Item ID or Array of Item IDs by a given value
 * @param $module string Module Name
 * @param $id mixed Unique Item ID OR Array of Item IDs
 * @param $key string Key to increment
 * @param $value number Integer/Float to increment by
 * @param $update bool set to TRUE to update updated timed
 * @param $cache_reset bool set to FALSE to prevent cache reset
 * @return mixed
 */
function jrCore_db_increment_key($module, $id, $key, $value, $update = false, $cache_reset = true)
{
    if (!is_numeric($value)) {
        return false;
    }
    if (!is_array($id)) {
        $id = array(intval($id));
    }
    else {
        foreach ($id as $k => $iid) {
            $id[$k] = (int) $iid;
        }
    }
    $_arg = array(
        'module'      => $module,
        'id'          => $id,
        'key'         => $key,
        'value'       => $value,
        'update'      => $update,
        'cache_reset' => $cache_reset
    );
    $_arg = jrCore_trigger_event('jrCore', 'db_increment_key', $_arg);
    $func = jrCore_get_active_datastore_function($module, 'db_increment_key');
    if ($func($_arg['module'], $_arg['id'], $_arg['key'], $_arg['value'], $_arg['update'])) {
        if ($cache_reset) {
            // Reset cache for these items
            $_ch = array();
            foreach ($id as $uid) {
                $_ch[] = array($module, "{$module}-{$uid}-0", true);
                $_ch[] = array($module, "{$module}-{$uid}-1", true);
                $_ch[] = array($module, "{$module}-{$uid}-0", false);
                $_ch[] = array($module, "{$module}-{$uid}-1", false);
            }
            jrCore_delete_multiple_cache_entries($_ch);
        }
        return true;
    }
    return false;
}

/**
 * Core DS Plugin
 * @param $module
 * @param $id
 * @param $key
 * @param $value
 * @param bool|false $update
 * @return bool
 */
function _jrCore_db_increment_key($module, $id, $key, $value, $update = false)
{
    // Get profile_ids
    $_pi = array();
    $_rt = jrCore_db_get_multiple_items($module, $id, array('_profile_id'));
    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $k => $v) {
            if (is_numeric($v['_profile_id'])) {
                $uid       = (int) $v['_item_id'];
                $iid       = (int) $v['_profile_id'];
                $_pi[$uid] = $iid;
            }
        }
    }

    $_in = array();
    $key = jrCore_db_escape($key);
    foreach ($id as $uid) {
        $pid   = (isset($_pi[$uid])) ? $_pi[$uid] : 0;
        $_in[] = "({$uid},{$pid},'{$key}',0,'{$value}')";
        if ($update) {
            $_in[] = "({$uid},{$pid},'_updated',0,UNIX_TIMESTAMP())";
        }
    }
    $tbl = jrCore_db_table_name($module, 'item_key');
    if ($update) {
        $req = "INSERT INTO {$tbl} (`_item_id`,`_profile_id`,`key`,`index`,`value`) VALUES " . implode(',', $_in) . " ON DUPLICATE KEY UPDATE `value` = IF(`key` = '{$key}',`value` + {$value}, VALUES(`value`))";
    }
    else {
        $req = "INSERT INTO {$tbl} (`_item_id`,`_profile_id`,`key`,`index`,`value`) VALUES " . implode(',', $_in) . " ON DUPLICATE KEY UPDATE `value` = `value` + {$value}";
    }
    $cnt = jrCore_db_query($req, 'COUNT', false, null, false);
    if (!$cnt || $cnt < 1) {
        return false;
    }

    // Do we have a dedicated key index?
    if (jrCore_db_key_has_index_table($module, $key)) {
        $_in = array();
        foreach ($id as $uid) {
            $_in[] = "({$uid},'{$value}')";
        }
        $tbl = jrCore_db_get_index_table_name($module, $key);
        $req = "INSERT INTO {$tbl} (`_item_id`,`value`) VALUES " . implode(',', $_in) . " ON DUPLICATE KEY UPDATE `value` = `value` + {$value}";
        jrCore_db_query($req, 'COUNT', false, null, false);
    }

    return true;
}

/**
 * Decrement a DataStore key for an Item ID or Array of Item IDs by a given value
 * @param $module string Module Name
 * @param $id mixed Unique Item ID OR Array of Item IDs
 * @param $key string Key to decrement
 * @param $value number Integer/Float to decrement by
 * @param $min_value number Lowest Value allowed for Key (default 0)
 * @param $update bool set to TRUE to update updated timed
 * @param $cache_reset bool set to FALSE to prevent cache reset
 * @return mixed
 */
function jrCore_db_decrement_key($module, $id, $key, $value, $min_value = null, $update = false, $cache_reset = true)
{
    if (!is_numeric($value)) {
        return false;
    }
    if (is_null($min_value) || !is_numeric($min_value)) {
        $min_value = 0;
    }
    if (!is_array($id)) {
        $id = array(intval($id));
    }
    else {
        foreach ($id as $k => $iid) {
            $id[$k] = (int) $iid;
        }
    }
    $_arg = array(
        'module'      => $module,
        'id'          => $id,
        'key'         => $key,
        'value'       => $value,
        'min_value'   => $min_value,
        'update'      => $update,
        'cache_reset' => $cache_reset
    );
    $_arg = jrCore_trigger_event('jrCore', 'db_decrement_key', $_arg);
    $func = jrCore_get_active_datastore_function($module, 'db_decrement_key');
    if ($func($_arg['module'], $_arg['id'], $_arg['key'], $_arg['value'], $_arg['min_value'], $_arg['update'])) {
        if ($cache_reset) {
            // Reset cache for these items
            $_ch = array();
            foreach ($id as $uid) {
                $_ch[] = array($module, "{$module}-{$uid}-0", true);
                $_ch[] = array($module, "{$module}-{$uid}-1", true);
                $_ch[] = array($module, "{$module}-{$uid}-0", false);
                $_ch[] = array($module, "{$module}-{$uid}-1", false);
            }
            jrCore_delete_multiple_cache_entries($_ch);
        }
        return true;
    }
    return false;
}

/**
 * Core DS Plugin
 * @param $module
 * @param $id
 * @param $key
 * @param $value
 * @param null $min_value
 * @param bool|false $update
 * @return bool
 */
function _jrCore_db_decrement_key($module, $id, $key, $value, $min_value = null, $update = false)
{
    // Get profile_ids
    $_pi = array();
    $_rt = jrCore_db_get_multiple_items($module, $id, array('_profile_id'));
    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $k => $v) {
            if (is_numeric($v['_profile_id'])) {
                $uid       = (int) $v['_item_id'];
                $iid       = (int) $v['_profile_id'];
                $_pi[$uid] = $iid;
            }
        }
    }

    $_in = array();
    $key = jrCore_db_escape($key);
    foreach ($id as $uid) {
        $pid   = (isset($_pi[$uid])) ? $_pi[$uid] : 0;
        $_in[] = "({$uid},{$pid},'{$key}',0,0)";
        if ($update) {
            $_in[] = "({$uid},{$pid},'_updated',0,UNIX_TIMESTAMP())";
        }
    }
    $val = ($min_value + $value);
    $tbl = jrCore_db_table_name($module, 'item_key');
    if ($update) {
        $req = "INSERT INTO {$tbl} (`_item_id`,`_profile_id`,`key`,`index`,`value`) VALUES " . implode(',', $_in) . " ON DUPLICATE KEY UPDATE `value` = IF(`key` = '_updated', VALUES(`value`), IF(`value` >= {$val},`value` - {$value}, `value`))";
    }
    else {
        $req = "INSERT INTO {$tbl} (`_item_id`,`_profile_id`,`key`,`index`,`value`) VALUES " . implode(',', $_in) . " ON DUPLICATE KEY UPDATE `value` = IF(`value` >= {$val},`value` - {$value}, `value`)";
    }
    $cnt = jrCore_db_query($req, 'COUNT', false, null, false);
    if (!$cnt || $cnt < 1) {
        return false;
    }

    // Do we have a dedicated key index?
    if (jrCore_db_key_has_index_table($module, $key)) {
        $_in = array();
        foreach ($id as $uid) {
            $_in[] = "({$uid},'{$value}')";
        }
        $tbl = jrCore_db_get_index_table_name($module, $key);
        $req = "INSERT INTO {$tbl} (`_item_id`,`value`) VALUES " . implode(',', $_in) . " ON DUPLICATE KEY UPDATE `value` = IF(`value` >= {$val},`value` - {$value}, `value`)";
        jrCore_db_query($req, 'COUNT', false, null, false);
    }

    return true;
}

/**
 * Return an array of _item_id's that do NOT have a specified key set
 * @param $module string Module DataStore to search through
 * @param $key string Key Name that should not be set
 * @param $limit int Limit number of results to $limit
 * @return array|bool
 */
function jrCore_db_get_items_missing_key($module, $key, $limit = 0)
{
    $func = jrCore_get_active_datastore_function($module, 'db_get_items_missing_key');
    return $func($module, $key, $limit);
}

/**
 * Core DS Plugin
 * @param $module string
 * @param $key string
 * @param $limit int
 * @return array|bool
 */
function _jrCore_db_get_items_missing_key($module, $key, $limit)
{
    $tbl = jrCore_db_table_name($module, 'item_key');
    $lim = '';
    if ($limit > 0) {
        $lim = " LIMIT {$limit}";
    }
    $req = "SELECT `_item_id` FROM {$tbl} WHERE `_item_id` > 0 AND `_item_id` NOT IN(SELECT `_item_id` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "') GROUP BY `_item_id`{$lim}";
    $_rt = jrCore_db_query($req, '_item_id');
    if ($_rt && is_array($_rt)) {
        return array_keys($_rt);
    }
    return false;
}

/**
 * Check if a specific KEY name exists in a DataStore
 * @param $module string Module DataStore
 * @param $key string Key to check for
 * @return bool
 */
function jrCore_db_item_key_exists($module, $key)
{
    $func = jrCore_get_active_datastore_function($module, 'db_item_key_exists');
    return $func($module, $key);
}

/**
 * Core DS Plugin
 * @param $module
 * @param $key
 * @return bool
 */
function _jrCore_db_item_key_exists($module, $key)
{
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "SELECT `_item_id` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        return true;
    }
    return false;
}

/**
 * Get all unique Key Names from a module DS
 * @param $module string Module
 * @return array|bool
 */
function jrCore_db_get_unique_keys($module)
{
    $func = jrCore_get_active_datastore_function($module, 'db_get_unique_keys');
    return $func($module);
}

/**
 * Core DS Plugin
 * @param $module
 * @return array|bool
 */
function _jrCore_db_get_unique_keys($module)
{
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "SELECT `key` FROM {$tbl} GROUP BY `key` ORDER BY `key` ASC";
    $_rt = jrCore_db_query($req, 'key', false, 'key');
    if ($_rt && is_array($_rt)) {
        return array_keys($_rt);
    }
    return false;
}

/**
 * Get all values for a key
 * @param $module string Module DataStore
 * @param $key string Key to get value for
 * @return bool|mixed
 */
function jrCore_db_get_all_key_values($module, $key)
{
    $func = jrCore_get_active_datastore_function($module, 'db_get_all_key_values');
    return $func($module, $key);
}

/**
 * Core DS Plugin
 * @param $module
 * @param $key
 * @return array|bool
 */
function _jrCore_db_get_all_key_values($module, $key)
{
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "SELECT `_item_id` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "'";
    $_rt = jrCore_db_query($req, '_item_id', false, '_item_id');
    if ($_rt && is_array($_rt)) {
        switch ($module) {
            case 'jrUser':
                $iid = '_user_id';
                break;
            case 'jrProfile':
                $iid = '_profile_id';
                break;
            default:
                $iid = '_item_id';
                break;
        }
        // We pass through jrCore_db_get_multiple_items here on purpose -
        // it contains the logic to reconstruct all key values (i.e. over 512 bytes)
        $_rt = jrCore_db_get_multiple_items($module, $_rt, array($iid, $key));
        if ($_rt && is_array($_rt)) {
            $_tm = array();
            foreach ($_rt as $v) {
                $_tm["{$v[$iid]}"] = $v[$key];
            }
            return $_tm;
        }
    }
    return false;
}

/**
 * Deletes multiple keys from an item
 * @param string $module Module the DataStore belongs to
 * @param int $id Item ID
 * @param array $_keys Keys to delete
 * @param bool $core_check by default you cannot delete keys that begin with _
 * @param bool $cache_reset by default cache is reset
 * @return bool
 */
function jrCore_db_delete_multiple_item_keys($module, $id, $_keys, $core_check = true, $cache_reset = true)
{
    if (!is_array($_keys) || count($_keys) === 0) {
        return false;
    }
    $func = jrCore_get_active_datastore_function($module, 'db_delete_multiple_item_keys');
    if ($func($module, $id, $_keys, $core_check, $cache_reset)) {
        // reset cache for this item
        if ($cache_reset) {
            $_ch = array(
                array($module, "{$module}-{$id}-0", true),
                array($module, "{$module}-{$id}-1", true),
                array($module, "{$module}-{$id}-0", false),
                array($module, "{$module}-{$id}-1", false)
            );
            jrCore_delete_multiple_cache_entries($_ch);
        }
        return true;
    }
    return false;
}

/**
 * Core DS Plugin
 * @param $module
 * @param $id
 * @param $_keys
 * @param bool|true $core_check
 * @param bool|true $cache_reset
 * @return bool
 */
function _jrCore_db_delete_multiple_item_keys($module, $id, $_keys, $core_check = true, $cache_reset = true)
{
    foreach ($_keys as $k => $key) {
        // Some things we cannot remove
        if ($core_check && strpos($key, '_') === 0) {
            // internally used - cannot remove
            unset($_keys[$k]);
            continue;
        }
        $_keys[$k] = jrCore_db_escape($key);
    }
    // Delete keys
    if (count($_keys) > 0) {
        $uid   = intval($id);
        $_args = array('module' => $module, '_item_id' => $uid);
        $_keys = jrCore_trigger_event('jrCore', 'db_delete_keys', $_keys, $_args);
        if ($_keys && count($_keys) > 0) {
            $tbl = jrCore_db_table_name($module, 'item_key');
            $req = "DELETE FROM {$tbl} WHERE `_item_id` = {$uid} AND `key` IN('" . implode("','", $_keys) . "')";
            $cnt = jrCore_db_query($req, 'COUNT');
            if ($cnt && $cnt > 0) {

                // Check for key indexes
                $_rq = array();
                foreach ($_keys as $key) {
                    if (jrCore_db_key_has_index_table($module, $key)) {
                        $tbl   = jrCore_db_get_index_table_name($module, $key);
                        $_rq[] = "DELETE FROM {$tbl} WHERE `_item_id` = {$uid}";
                    }
                }
                if (count($_rq) > 0) {
                    jrCore_db_multi_select($_rq, false);
                }

                return true;
            }
        }
    }
    return false;
}

/**
 * Deletes a single key from an item
 * @param string $module Module the DataStore belongs to
 * @param int $id Item ID
 * @param string $key Key to delete
 * @param bool $core_check by default you cannot delete keys that begin with _
 * @param bool $cache_reset by default cache is reset
 * @return mixed INSERT_ID on success, false on error
 */
function jrCore_db_delete_item_key($module, $id, $key, $core_check = true, $cache_reset = true)
{
    $func = jrCore_get_active_datastore_function($module, 'db_delete_item_key');
    return $func($module, $id, $key, $core_check, $cache_reset);
}

/**
 * Core DS Plugin
 * @param $module
 * @param $id
 * @param $key
 * @param bool|true $core_check
 * @param bool|true $cache_reset
 * @return bool
 */
function _jrCore_db_delete_item_key($module, $id, $key, $core_check = true, $cache_reset = true)
{
    return jrCore_db_delete_multiple_item_keys($module, $id, array($key), $core_check, $cache_reset);
}

/**
 * Delete DataStore Key(s) from Multiple Items
 * @param string $module Module the DataStore belongs to
 * @param array $_ids IDs of items to delete keys from
 * @param mixed $key key name or array of key names
 * @param bool $cache_reset by default cache is reset
 * @return bool
 */
function jrCore_db_delete_key_from_multiple_items($module, $_ids, $key, $cache_reset = true)
{
    $func = jrCore_get_active_datastore_function($module, 'db_delete_key_from_multiple_items');
    if ($func($module, $_ids, $key)) {
        if ($cache_reset) {
            $_cch = array();
            foreach ($_ids as $k => $id) {
                $_cch[] = array($module, "{$module}-{$id}-0", true);
                $_cch[] = array($module, "{$module}-{$id}-1", true);
                $_cch[] = array($module, "{$module}-{$id}-0", false);
                $_cch[] = array($module, "{$module}-{$id}-1", false);
            }
            jrCore_delete_multiple_cache_entries($_cch);
        }
        return true;
    }
    return false;
}

/**
 * Core DS Plugin
 * @param $module
 * @param $_ids
 * @param $key
 * @return bool
 */
function _jrCore_db_delete_key_from_multiple_items($module, $_ids, $key)
{
    $_rq = array();
    if (is_array($key)) {
        $_ky = array();
        foreach ($key as $k) {
            $_ky[] = jrCore_db_escape($k);
            if (jrCore_db_key_has_index_table($module, $k)) {
                $tbl   = jrCore_db_get_index_table_name($module, $k);
                $_rq[] = "DELETE FROM {$tbl} WHERE `_item_id` IN(" . implode(',', $_ids) . ")";
            }
        }
        $tbl   = jrCore_db_table_name($module, 'item_key');
        $_rq[] = "DELETE FROM {$tbl} WHERE `key` IN('" . implode("','", $_ky) . "') AND `_item_id` IN(" . implode(',', $_ids) . ")";
    }
    else {
        $tbl   = jrCore_db_table_name($module, 'item_key');
        $_rq[] = "DELETE FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "' AND `_item_id` IN(" . implode(',', $_ids) . ")";
        if (jrCore_db_key_has_index_table($module, $key)) {
            $tbl   = jrCore_db_get_index_table_name($module, $key);
            $_rq[] = "DELETE FROM {$tbl} WHERE `_item_id` IN(" . implode(',', $_ids) . ")";
        }
    }
    jrCore_db_multi_select($_rq, false);
    return true;
}

/**
 * Delete DataStore Key(s) from All Items
 * @param string $module Module the DataStore belongs to
 * @param mixed $key key name or array of key names
 * @return bool
 */
function jrCore_db_delete_key_from_all_items($module, $key)
{
    $func = jrCore_get_active_datastore_function($module, 'db_delete_key_from_all_items');
    return $func($module, $key);
}

/**
 * Core DS Plugin
 * @param $module
 * @param $key
 * @return bool
 */
function _jrCore_db_delete_key_from_all_items($module, $key)
{
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "DELETE FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "'";
    $cnt = jrCore_db_query($req, 'COUNT');
    // If this key has a dedicated index table, truncate it
    if (jrCore_db_key_has_index_table($module, $key)) {
        $tbl = jrCore_db_get_index_table_name($module, $key);
        $req = "TRUNCATE TABLE {$tbl}";
        jrCore_db_query($req);
    }
    $_rp = array(
        'delete_count' => $cnt,
        'module'       => $module,
        'key'          => $key
    );
    jrCore_trigger_event('jrCore', 'db_delete_key_from_all_items', $_rp);
    return $cnt;
}

/**
 * Validates DataStore key names are allowed and correct
 * @param string $module Module the DataStore belongs to
 * @param array $_data Array of Key => Value pairs to check
 * @return mixed true on success, exits on error
 */
function jrCore_db_get_allowed_item_keys($module, $_data)
{
    if (!$_data || !is_array($_data) || count($_data) === 0) {
        return false;
    }
    $pfx = jrCore_db_get_prefix($module);
    $_rt = array();
    foreach ($_data as $k => $v) {
        if (strpos($k, '_') === 0) {
            jrCore_notice_page('CRI', "invalid key name: {$k} - key names cannot start with an underscore");
        }
        elseif (strpos($k, $pfx) !== 0) {
            jrCore_notice_page('CRI', "invalid key name: {$k} - key name must begin with module prefix: {$pfx}_");
        }
        $_rt[$k] = $v;
    }
    return $_rt;
}

/**
 * Create a new Unique Item ID for an item
 * @param $module string module
 * @param $count int number of IDs to create
 * @return mixed
 */
function jrCore_db_create_unique_item_id($module, $count = 1)
{
    $func = jrCore_get_active_datastore_function($module, 'db_create_unique_item_id');
    return $func($module, $count);
}

/**
 * Core DS Plugin
 * @param $module string
 * @param $count int
 * @return mixed
 */
function _jrCore_db_create_unique_item_id($module, $count = 1)
{
    // Get our unique item id
    $tbl = jrCore_db_table_name($module, 'item');
    if ($count > 1) {
        $ins = str_repeat('(0),', $count);
        $req = "INSERT INTO {$tbl} (`_item_id`) VALUES " . substr($ins, 0, strlen($ins) - 1);
    }
    else {
        $req = "INSERT INTO {$tbl} (`_item_id`) VALUES (0)";
    }
    return jrCore_db_query($req, 'INSERT_ID');
}

/**
 * Update module item count for a profile
 * @param $module string
 * @param $profile_id int
 * @return mixed
 */
function jrCore_db_update_profile_item_count($module, $profile_id)
{
    $func = jrCore_get_active_datastore_function($module, 'db_update_profile_item_count');
    return $func($module, $profile_id);
}

/**
 * Core DS Plugin
 * @param $module string
 * @param $profile_id int
 * @return mixed
 */
function _jrCore_db_update_profile_item_count($module, $profile_id)
{
    $pid = (int) $profile_id;
    $tbl = jrCore_db_table_name($module, 'item_key');
    $ptb = jrCore_db_table_name('jrProfile', 'item_key');
    $req = "UPDATE {$ptb} SET `value` = (SELECT COUNT(`_item_id`) FROM {$tbl} WHERE `key` = '_profile_id' AND `value` = '{$pid}') WHERE `key` = 'profile_{$module}_item_count' AND `_item_id` = '{$pid}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!$cnt || $cnt === 0) {
        // The first entry for a new module item
        $req = "INSERT INTO {$ptb} (`_item_id`,`_profile_id`,`key`,`index`,`value`)
                VALUES ({$pid},{$pid},'profile_{$module}_item_count',0,(SELECT COUNT(`_item_id`) FROM {$tbl} WHERE `key` = '_profile_id' AND `value` = '{$pid}'))
                ON DUPLICATE KEY UPDATE `value` = (`value` + 1)";
        jrCore_db_query($req, null, false, null, false);
    }
    return true;
}

/**
 * Update module item count for a user
 * @param $module string
 * @param $profile_id int
 * @param $user_id int
 * @return mixed
 */
function jrCore_db_update_user_item_count($module, $profile_id, $user_id)
{
    $func = jrCore_get_active_datastore_function($module, 'db_update_user_item_count');
    return $func($module, $profile_id, $user_id);
}

/**
 * Core DS Plugin
 * @param $module string
 * @param $profile_id int
 * @param $user_id int
 * @return mixed
 */
function _jrCore_db_update_user_item_count($module, $profile_id, $user_id)
{
    $uid = (int) $user_id;
    $pid = (int) $profile_id;
    $tbl = jrCore_db_table_name($module, 'item_key');
    $ptb = jrCore_db_table_name('jrUser', 'item_key');
    $req = "UPDATE {$ptb} SET `value` = (SELECT COUNT(`_item_id`) FROM {$tbl} WHERE `key` = '_user_id' AND `value` = '{$uid}') WHERE `key` = 'user_{$module}_item_count' AND `_item_id` = '{$uid}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!$cnt || $cnt === 0) {
        // The first entry for a new user module item counter
        $req = "INSERT INTO {$ptb} (`_item_id`,`_profile_id`,`key`,`index`,`value`)
                VALUES ('{$uid}',{$pid},'user_{$module}_item_count',0,(SELECT COUNT(`_item_id`) FROM {$tbl} WHERE `key` = '_user_id' AND `value` = '{$uid}'))
                ON DUPLICATE KEY UPDATE `value` = (`value` + 1)";
        jrCore_db_query($req, null, false, null, false);
    }
    return true;
}

/**
 * Creates a new item in a module datastore
 * @param string $module Module the DataStore belongs to
 * @param array $_data Array of Key => Value pairs for insertion
 * @param array $_core Array of Key => Value pairs for insertion - skips jrCore_db_get_allowed_item_keys()
 * @param bool $profile_count If set to true, profile_count will be incremented for given _profile_id
 * @param bool $skip_trigger bool Set to TRUE to skip sending out create_item trigger
 * @return mixed INSERT_ID on success, false on error
 */
function jrCore_db_create_item($module, $_data, $_core = null, $profile_count = true, $skip_trigger = false)
{
    global $_user;

    // See if we are limiting the number of items that can be created by a profile in this quota
    if (!jrUser_is_admin()) {
        if (isset($_user["quota_{$module}_max_items"]) && $_user["quota_{$module}_max_items"] > 0) {
            if ($p_cnt = jrCore_db_get_item_key('jrProfile', $_user['user_active_profile_id'], "profile_{$module}_item_count")) {
                if ($p_cnt >= $_user["quota_{$module}_max_items"]) {
                    // We've hit the limit for this quota
                    $_ln = jrUser_load_lang_strings();
                    jrCore_set_flag("max_{$module}_items_reached", $_ln['jrCore'][70]);
                    return false;
                }
            }
        }
    }

    // Validate incoming data
    $_data = jrCore_db_get_allowed_item_keys($module, $_data);

    // Check for additional core fields being added in
    if ($_core && is_array($_core)) {
        foreach ($_core as $k => $v) {
            if (strpos($k, '_') === 0) {
                $_data[$k] = $_core[$k];
            }
        }
    }
    $_core = null;

    // Internal defaults
    $_check = array(
        '_created'    => 'UNIX_TIMESTAMP()',
        '_updated'    => 'UNIX_TIMESTAMP()',
        '_profile_id' => 0,
        '_user_id'    => 0
    );
    // If user is logged in, defaults to their account
    if (jrUser_is_logged_in()) {
        $_check['_profile_id'] = (isset($_user['user_active_profile_id'])) ? intval($_user['user_active_profile_id']) : jrUser_get_profile_home_key('_profile_id');
        $_check['_user_id']    = (int) $_user['_user_id'];
    }
    foreach ($_check as $k => $v) {
        // Any of our _check values can be removed by setting it to false
        if (isset($_data[$k]) && $_data[$k] === false) {
            unset($_data[$k]);
        }
        elseif (!isset($_data[$k])) {
            $_data[$k] = $_check[$k];
        }
    }

    // Our module DS prefix
    $pfx = jrCore_db_get_prefix($module);

    // Check for item_order_support
    $_pn = jrCore_get_registered_module_features('jrCore', 'item_order_support');
    if ($_pn && isset($_pn[$module]) && !isset($_data["{$pfx}_display_order"])) {
        // New entries at top
        $_data["{$pfx}_display_order"] = 0;
    }

    // Let listeners add/remove data or prevent item creation altogether
    if (!$skip_trigger) {
        $_args = array(
            'module' => $module
        );
        $_data = jrCore_trigger_event('jrCore', 'db_create_item_data', $_data, $_args);
        // Our listeners can tell us to NOT create the item
        if (isset($_data['db_create_item']) && $_data['db_create_item'] == false) {
            // We've been short circuited by a listener
            return false;
        }
    }

    // Generate unique ID for this item
    $iid = jrCore_db_create_unique_item_id($module, 1);
    if (!$iid) {
        return false;
    }

    // Trigger create event
    if (!$skip_trigger) {
        $_args['_item_id'] = $iid;
        $_data             = jrCore_trigger_event('jrCore', 'db_create_item', $_data, $_args);
    }

    // Check for Pending Support for this module
    // Items created by master/admin users bypass pending
    $eml = true;
    $lid = 0;
    $lmd = '';
    if (!isset($_data["{$pfx}_pending"])) {
        $_pn = jrCore_get_registered_module_features('jrCore', 'pending_support');
        if ($_pn && isset($_pn[$module])) {
            $_data["{$pfx}_pending"] = 0;

            // Pending support is on for this module - check quota setting:
            // 0 = immediately active
            // 1 = review needed on CREATE
            // 2 = review needed on CREATE and UPDATE
            if (!jrUser_is_admin() && isset($_user["quota_{$module}_pending"]) && intval($_user["quota_{$module}_pending"]) > 0) {
                $_data["{$pfx}_pending"] = 1;
            }

        }
    }
    else {

        // See if this item was set pending by a db_create_item event listener
        if (isset($_data["{$pfx}_pending"]) && $_data["{$pfx}_pending"] == 1) {

            jrCore_set_flag("jrcore_created_pending_item_{$module}_{$iid}", 1);

            // Check for actions that are linking to pending items
            // Important: This part must be BEFORE the active DS create call so we can remove the extra keys
            if (isset($_data['action_pending_linked_item_id']) && jrCore_checktype($_data['action_pending_linked_item_id'], 'number_nz')) {
                $lid = (int) $_data['action_pending_linked_item_id'];
                $lmd = jrCore_db_escape($_data['action_pending_linked_item_module']);
                unset($_data['action_pending_linked_item_id'], $_data['action_pending_linked_item_module']);
                $eml = false;
            }

        }
    }

    // Create item
    $func = jrCore_get_active_datastore_function($module, 'db_create_item');
    if ($func($module, $iid, $_data, $_core, $profile_count, $skip_trigger)) {

        // Add pending entry to Pending table...
        if (isset($_data["{$pfx}_pending"]) && $_data["{$pfx}_pending"] == 1) {
            $_pd                     = array(
                'module' => $module,
                'item'   => $_data,
                'user'   => $_user
            );
            $_pd['item']['_created'] = time();
            $_pd['item']['_updated'] = time();
            $dat                     = jrCore_db_escape(jrCore_strip_emoji(json_encode($_pd), false));
            $pnd                     = jrCore_db_table_name('jrCore', 'pending');
            $req                     = "INSERT INTO {$pnd} (pending_created,pending_module,pending_item_id,pending_linked_item_module,pending_linked_item_id,pending_data)
                                        VALUES (UNIX_TIMESTAMP(),'" . jrCore_db_escape($module) . "','{$iid}','{$lmd}','{$lid}','{$dat}')
                                        ON DUPLICATE KEY UPDATE pending_created = UNIX_TIMESTAMP(), pending_data = VALUES(pending_data)";
            jrCore_db_query($req);
            unset($_pd);

            // Notify admins of new pending item
            if ($eml && $lid == 0) {
                $_rt = jrUser_get_admin_user_ids();
                if ($_rt && is_array($_rt)) {
                    $_data['module'] = $module;
                    list($sub, $msg) = jrCore_parse_email_templates('jrCore', 'pending_item', $_data);
                    jrCore_db_notify_admins_of_pending_item($module, $_rt, $sub, $msg);
                }
            }
        }

        // Increment profile counts for this item
        if ($profile_count) {
            switch ($module) {

                // Some modules we do not store counts for
                case 'jrProfile':
                case 'jrUser':
                case 'jrCore':
                    break;

                default:
                    if (isset($_data['_profile_id'])) {
                        $pid = intval($_data['_profile_id']);
                        if ($pid > 0) {
                            jrCore_db_increment_key('jrProfile', $pid, "profile_{$module}_item_count", 1);
                        }
                    }
                    if (isset($_data['_user_id'])) {
                        $uid = intval($_data['_user_id']);
                        if ($uid > 0) {
                            jrCore_db_increment_key('jrUser', $uid, "user_{$module}_item_count", 1);
                        }
                    }
                    break;
            }
        }

        // Trigger create_item_exit event
        if (!$skip_trigger) {
            $_args = array(
                '_item_id' => $iid,
                'module'   => $module
            );
            jrCore_trigger_event('jrCore', 'db_create_item_exit', $_data, $_args);
        }
        return $iid;
    }
    return false;
}

/**
 * Core DS Plugin
 * @param $module string
 * @param $item_id int
 * @param $_data array
 * @param null $_core
 * @param bool|true $profile_count
 * @param bool|false $skip_trigger
 * @return bool
 */
function _jrCore_db_create_item($module, $item_id, $_data, $_core = null, $profile_count = true, $skip_trigger = false)
{
    // Get our unique item id
    $iid = (int) $item_id;
    $pfx = jrCore_db_get_prefix($module);
    $pid = (int) $_data['_profile_id'];

    // Check for item_order_support
    if (isset($_data["{$pfx}_display_order"]) && $_data["{$pfx}_display_order"] === 0) {

        // Any other items of this type need to have their order incremented by ONE
        // NOTE: This is done in 2 queries since you cannot UPDATE a table that you SELECT FROM
        // http://dev.mysql.com/doc/refman/5.6/en/update.html
        $tbl = jrCore_db_table_name($module, 'item_key');
        $req = "SELECT `_item_id` FROM {$tbl} WHERE `key` = '_profile_id' AND `value` = '{$pid}'";
        $_ei = jrCore_db_query($req, '_item_id', false, '_item_id');
        if ($_ei && is_array($_ei)) {
            $req = "UPDATE {$tbl} SET `value` = (`value` + 1) WHERE _item_id IN(" . implode(',', $_ei) . ") AND `key` = '{$pfx}_display_order'";
            jrCore_db_query($req);
        }

    }

    $_rq = array();
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "INSERT INTO {$tbl} (`_item_id`,`_profile_id`,`key`,`index`,`value`) VALUES ";
    foreach ($_data as $k => $v) {

        $val = false;
        if ($v === 'UNIX_TIMESTAMP()') {
            $req .= "({$iid},{$pid},'" . jrCore_db_escape($k) . "','0',UNIX_TIMESTAMP()),";
            $val = 'UNIX_TIMESTAMP()';
        }
        else {
            // If our value is longer than 508 bytes we split it up
            $v   = jrCore_strip_emoji($v);
            $len = strlen($v);
            if ($len > 508) {
                $_tm = array();
                while ($len) {
                    $_tm[] = mb_strcut($v, 0, 508, "UTF-8");
                    $v     = mb_strcut($v, 508, $len, "UTF-8");
                    $len   = strlen($v);
                }
                foreach ($_tm as $idx => $part) {
                    $req .= "({$iid},{$pid},'" . jrCore_db_escape($k) . "','" . ($idx + 1) . "','" . jrCore_db_escape($part) . "'),";
                }
            }
            else {
                if (is_numeric($v)) {
                    $val = $v;
                }
                else {
                    $val = jrCore_db_escape($v);
                }
                $req .= "({$iid},{$pid},'" . jrCore_db_escape($k) . "','0','" . $val . "'),";
            }
        }

        if ($val && jrCore_db_key_has_index_table($module, $k)) {
            $tbi = jrCore_db_get_index_table_name($module, $k);
            if ($val != 'UNIX_TIMESTAMP()') {
                $val = "'{$val}'";
            }
            $_rq[] = "INSERT INTO {$tbi} (`_item_id`,`value`) VALUES ({$iid},{$val})";
        }

    }
    $req = substr($req, 0, strlen($req) - 1);
    $cnt = jrCore_db_query($req, 'COUNT', false, null, false);
    if ($cnt && $cnt > 0) {
        if (count($_rq) > 0) {
            jrCore_db_multi_select($_rq, false);
        }
        return true;
    }
    return false;
}

/**
 * Create multiple items in a module datastore
 * @param string $module Module the DataStore belongs to
 * @param array $_data Array of Key => Value pairs for insertion
 * @param array $_core Array of Key => Value pairs for insertion - skips jrCore_db_get_allowed_item_keys()
 * @param bool $skip_trigger bool Set to TRUE to skip sending out create_item trigger
 * @return mixed array of INSERT_ID's on success, false on error
 */
function jrCore_db_create_multiple_items($module, $_data, $_core = null, $skip_trigger = false)
{
    global $_user;

    // Validate incoming data
    foreach ($_data as $k => $_dt) {
        if (!is_array($_dt)) {
            // bad data
            return false;
        }
        $_data[$k] = jrCore_db_get_allowed_item_keys($module, $_dt);
    }

    // Check for additional core fields being added in
    if (is_array($_core)) {
        foreach ($_core as $ck => $_cr) {
            foreach ($_cr as $k => $v) {
                if (strpos($k, '_') === 0) {
                    $_data[$ck][$k] = $v;
                }
            }
        }
        $_core = null;
    }

    // Internal defaults
    $_check = array(
        '_created'    => 'UNIX_TIMESTAMP()',
        '_updated'    => 'UNIX_TIMESTAMP()',
        '_profile_id' => 0,
        '_user_id'    => 0
    );
    // If user is logged in, defaults to their account
    if (jrUser_is_logged_in()) {
        $_check['_profile_id'] = (isset($_user['user_active_profile_id'])) ? intval($_user['user_active_profile_id']) : jrUser_get_profile_home_key('_profile_id');
        $_check['_user_id']    = (int) $_user['_user_id'];
    }
    foreach ($_data as $k => $_dt) {
        foreach ($_check as $ck => $v) {
            // Any of our _check values can be removed by setting it to false
            if (isset($_data[$k][$ck]) && $_data[$k][$ck] === false) {
                unset($_data[$k][$ck]);
            }
            elseif (!isset($_data[$k][$ck])) {
                $_data[$k][$ck] = $v;
            }
        }
    }

    // Let listeners add/remove data or prevent item creation altogether
    if (!$skip_trigger) {
        $_args = array(
            'module' => $module
        );
        foreach ($_data as $k => $_dt) {
            $_dt = jrCore_trigger_event('jrCore', 'db_create_item_data', $_dt, $_args);
            // Our listeners can tell us to NOT create the item
            if (isset($_dt['db_create_item']) && $_dt['db_create_item'] == false) {
                // We've been short circuited by a listener
                unset($_data[$k]);
                continue;
            }
        }
        if (count($_data) === 0) {
            // Nothing to create
            return false;
        }
    }

    // Generate unique ID for these items - note that we only get
    // the FIRST ID in the set - the rest are incremental from that
    $iid = jrCore_db_create_unique_item_id($module, count($_data));
    if (!$iid) {
        return false;
    }

    // Trigger create event
    if (!$skip_trigger) {
        $uid = $iid;
        foreach ($_data as $k => $_dt) {
            $_args     = array(
                '_item_id' => $uid,
                'module'   => $module
            );
            $_data[$k] = jrCore_trigger_event('jrCore', 'db_create_item', $_dt, $_args);
            $uid++;
        }
    }

    $func = jrCore_get_active_datastore_function($module, 'db_create_multiple_items');
    if ($cnt = $func($module, $iid, $_data, $_core, $skip_trigger)) {
        $_id = array();
        $uid = $iid;
        foreach ($_data as $k => $_dt) {
            // Trigger create_item_exit event
            if (!$skip_trigger) {
                $_args = array(
                    '_item_id' => $uid,
                    'module'   => $module
                );
                jrCore_trigger_event('jrCore', 'db_create_item_exit', $_dt, $_args);
            }
            $_id[] = $uid++;
        }
        return $_id;
    }
    return false;
}

/**
 * Core DS plugin
 * @param $module string
 * @param $iid int
 * @param $_data array
 * @param null $_core
 * @param bool|false $skip_trigger
 * @return array|bool
 */
function _jrCore_db_create_multiple_items($module, $iid, $_data, $_core = null, $skip_trigger = false)
{
    $_rq = array();
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "INSERT INTO {$tbl} (`_item_id`,`_profile_id`,`key`,`index`,`value`) VALUES ";
    $uid = $iid;
    foreach ($_data as $dk => $_dt) {
        $pid = (int) $_dt['_profile_id'];
        foreach ($_dt as $k => $v) {
            $val = false;
            if ($v === 'UNIX_TIMESTAMP()') {
                $req .= "({$uid},{$pid},'" . jrCore_db_escape($k) . "','0',UNIX_TIMESTAMP()),";
                $val = 'UNIX_TIMESTAMP()';
            }
            else {
                // If our value is longer than 508 bytes we split it up
                $len = strlen($v);
                $v   = jrCore_strip_emoji($v);
                if ($len > 508) {
                    $_tm = array();
                    while ($len) {
                        $_tm[] = mb_strcut($v, 0, 508, "UTF-8");
                        $v     = mb_strcut($v, 508, $len, "UTF-8");
                        $len   = strlen($v);
                    }
                    foreach ($_tm as $idx => $part) {
                        $req .= "({$uid},{$pid},'" . jrCore_db_escape($k) . "','" . ($idx + 1) . "','" . jrCore_db_escape($part) . "'),";
                    }
                }
                else {
                    if (is_numeric($v)) {
                        $val = $v;
                    }
                    else {
                        $val = jrCore_db_escape($v);
                    }
                    $req .= "({$uid},{$pid},'" . jrCore_db_escape($k) . "','0','" . jrCore_db_escape($v) . "'),";
                }
            }
            if ($val && jrCore_db_key_has_index_table($module, $k)) {
                $tbi = jrCore_db_get_index_table_name($module, $k);
                if ($val != 'UNIX_TIMESTAMP()') {
                    $val = "'{$val}'";
                }
                $_rq[] = "INSERT INTO {$tbi} (`_item_id`,`value`) VALUES ({$iid},{$val})";
            }
        }
        $uid++;
    }
    $req = substr($req, 0, strlen($req) - 1);
    $cnt = jrCore_db_query($req, 'COUNT', false, null, false);
    if ($cnt && $cnt > 0) {
        if (count($_rq) > 0) {
            jrCore_db_multi_select($_rq, false);
        }
        return $cnt;
    }
    return false;
}

/**
 * Gets all items from a module datastore matching a key and value
 * @param string $module Module the item belongs to
 * @param string $key Key name to match
 * @param mixed $value Value to find in matched key (can be array of key => values)
 * @param bool $item_id_array if set to TRUE returns array of id's
 * @param bool $skip_caching Set to true to force item reload (skip caching)
 * @return mixed array on success, bool false on failure
 */
function jrCore_db_get_multiple_items_by_key($module, $key, $value, $item_id_array = false, $skip_caching = false)
{
    $func = jrCore_get_active_datastore_function($module, 'db_get_multiple_items_by_key');
    return $func($module, $key, $value, $item_id_array, $skip_caching);
}

/**
 * Core DS Plugin
 * @param $module string
 * @param $key string
 * @param $value mixed
 * @param bool|false $item_id_array
 * @param bool $skip_caching Set to true to force item reload (skip caching)
 * @return array|bool|mixed
 */
function _jrCore_db_get_multiple_items_by_key($module, $key, $value, $item_id_array = false, $skip_caching = false)
{
    $idx = (jrCore_db_key_has_index_table($module, $key)) ? true : false;
    if (is_array($value)) {
        $esc = jrCore_db_escape($key);
        $_rq = array();
        foreach ($value as $k => $v) {
            if ($idx) {
                $_rq[] = "(`value` = '" . jrCore_db_escape($v) . "')";
            }
            else {
                if (is_numeric($k)) {
                    $_rq[] = "(`key` = '{$esc}' AND `value` = '" . jrCore_db_escape($v) . "')";
                }
                else {
                    $_rq[] = "(`key` = '" . jrCore_db_escape($k) . "' AND `value` = '" . jrCore_db_escape($v) . "')";
                }
            }
        }
        if (count($_rq) > 0) {
            if ($idx) {
                $tbl = jrCore_db_get_index_table_name($module, $key);
                $req = "SELECT `_item_id` FROM {$tbl} WHERE " . implode(' OR ', $_rq);
            }
            else {
                $tbl = jrCore_db_table_name($module, 'item_key');
                $req = "SELECT `_item_id` FROM {$tbl} WHERE " . implode(' OR ', $_rq);
            }
        }
        else {
            return false;
        }
    }
    else {
        if ($idx) {
            $tbl = jrCore_db_get_index_table_name($module, $key);
            $req = "SELECT `_item_id` FROM {$tbl} WHERE `value` = '" . jrCore_db_escape($value) . "'";
        }
        else {
            $tbl = jrCore_db_table_name($module, 'item_key');
            $req = "SELECT `_item_id` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "' AND `value` = '" . jrCore_db_escape($value) . "'";
        }
    }
    $_rt = jrCore_db_query($req, '_item_id');
    if (!$_rt || !is_array($_rt)) {
        return false;
    }
    if ($item_id_array) {
        return array_keys($_rt);
    }
    return jrCore_db_get_multiple_items($module, array_keys($_rt), null, $skip_caching);
}

/**
 * Gets a single item from a module datastore by key name and value
 * @param string $module Module the item belongs to
 * @param string $key Key name to find
 * @param mixed $value Value to find
 * @param bool $skip_trigger By default the db_get_item event trigger is sent out to allow additional modules to add data to the item.  Set to TRUE to just return the item from the item datastore.
 * @param bool $skip_caching Set to true to force item reload (skip caching)
 * @return mixed array on success, bool false on failure
 */
function jrCore_db_get_item_by_key($module, $key, $value, $skip_trigger = false, $skip_caching = false)
{
    $func = jrCore_get_active_datastore_function($module, 'db_get_item_by_key');
    return $func($module, $key, $value, $skip_trigger, $skip_caching);
}

/**
 * Core DS Plugin
 * @param $module
 * @param $key
 * @param $value
 * @param bool|false $skip_trigger
 * @param bool|false $skip_caching
 * @return bool|mixed
 */
function _jrCore_db_get_item_by_key($module, $key, $value, $skip_trigger = false, $skip_caching = false)
{
    if (!$_rt = jrCore_get_flag("jrCore_db_get_item_by_key_{$key}_{$value}")) {
        if (jrCore_db_key_has_index_table($module, $key)) {
            $tbl = jrCore_db_get_index_table_name($module, $key);
            $req = "SELECT `_item_id` FROM {$tbl} WHERE `value` = '" . jrCore_db_escape($value) . "'";
        }
        else {
            $tbl = jrCore_db_table_name($module, 'item_key');
            $req = "SELECT `_item_id` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "' AND `value` = '" . jrCore_db_escape($value) . "' LIMIT 1";
        }
        $_rt = jrCore_db_query($req, 'SINGLE');
        jrCore_set_flag("jrCore_db_get_item_by_key_{$key}_{$value}", $_rt);
    }
    if (!$_rt || !is_array($_rt)) {
        return false;
    }
    return jrCore_db_get_item($module, $_rt['_item_id'], $skip_trigger, $skip_caching);
}

/**
 * Gets an item from a module datastore
 * @param string $module Module the item belongs to
 * @param int $id Item ID to retrieve
 * @param bool $skip_trigger By default the db_get_item event trigger is sent out to allow additional modules to add data to the item.  Set to TRUE to just return the item from the item datastore.
 * @param bool $skip_caching Set to true to force item reload (skip caching)
 * @return mixed array on success, bool false on failure
 */
function jrCore_db_get_item($module, $id, $skip_trigger = false, $skip_caching = false)
{
    if (!is_numeric($id)) {
        return false;
    }

    // See if we are cached - this is a GLOBAL cache
    // since it will be the same for any viewing user
    $key = ($skip_trigger) ? 1 : 0;
    $key = "{$module}-{$id}-{$key}";
    if (!$skip_caching) {
        if ($_rt = jrCore_is_cached($module, $key)) {
            return $_rt;
        }
    }

    $func = jrCore_get_active_datastore_function($module, 'db_get_item');
    if ($_itm = $func($module, $id, $skip_trigger, $skip_caching)) {

        if (!$skip_trigger) {
            $_itm = jrCore_trigger_event('jrCore', 'db_get_item', $_itm, array('module' => $module));
            // Make sure listeners did not change our _item_id
            $_itm['_item_id'] = intval($id);
        }

        // Save to cache
        if (isset($_itm['_profile_id'])) {
            jrCore_set_flag('datastore_cache_profile_ids', array($_itm['_profile_id']));
            jrCore_add_to_cache($module, $key, $_itm, 0, $_itm['_profile_id']);
        }

        return $_itm;
    }
    return false;
}

/**
 * Core DS plugin
 * @param $module
 * @param $id
 * @param bool|false $skip_trigger
 * @param bool|false $skip_caching
 * @return array|bool|mixed
 */
function _jrCore_db_get_item($module, $id, $skip_trigger = false, $skip_caching = false)
{
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "SELECT `key` AS k,`index` AS x,`value` AS v FROM {$tbl} WHERE `_item_id` = " . intval($id);
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {

        // Construct item
        $_ot = array('_item_id' => intval($id));
        $_ix = array();
        foreach ($_rt as $k => $v) {
            if ($v['x'] > 0) {
                if (isset($_ot["{$v['k']}"]) && !is_array($_ot["{$v['k']}"])) {
                    // We already saw index 0 - move it to our array
                    $_ot["{$v['k']}"] = array($_ot["{$v['k']}"]);
                }
                $_ot["{$v['k']}"]["{$v['x']}"] = $v['v'];
                $_ix["{$v['k']}"]              = 1;
            }
            else {
                $_ot["{$v['k']}"] = $v['v'];
            }
            unset($_rt[$k]);
        }
        if (count($_ix) > 0) {
            // We have index keys that need to be sorted and re-assembled
            foreach ($_ix as $k => $i) {
                ksort($_ot[$k], SORT_NUMERIC);
                $_ot[$k] = implode('', $_ot[$k]);
                unset($_ix[$id]);
            }
        }
        // Make sure _item_id did not get changed
        $_ot['_item_id'] = intval($id);
        unset($_ix, $_rt);

        if ($skip_trigger !== true) {

            if ($skip_trigger === false) {
                $skip_trigger = '';
            }

            // $skip_trigger can be:
            // true  = only the item data will be included
            // false = event trigger is fired and will include User, Profile and Quota info
            switch ($module) {

                case 'jrProfile':
                    // We only add in Quota info (below)
                    break;

                // For Users we always add in their ACTIVE profile info
                case 'jrUser':
                    if (isset($_ot['_profile_id']) && $_ot['_profile_id'] > 0 && !$skip_trigger) {
                        $_tm = jrCore_db_get_item('jrProfile', $_ot['_profile_id'], true);
                        if ($_tm && is_array($_tm)) {
                            unset($_tm['_item_id']);
                            $_ot = $_ot + $_tm;
                        }
                    }
                    break;

                // Everything else gets BOTH User and Profile
                default:
                    if (isset($_ot['_user_id']) && $_ot['_user_id'] > 0 && !$skip_trigger) {
                        // Add in User Info
                        $_tm = jrCore_db_get_item('jrUser', $_ot['_user_id'], true);
                        if ($_tm && is_array($_tm)) {
                            // We do not return passwords
                            unset($_tm['_item_id'], $_tm['user_password'], $_tm['user_old_password']);
                            $_ot = $_ot + $_tm;
                        }
                    }
                    if (isset($_ot['_profile_id']) && $_ot['_profile_id'] > 0 && !$skip_trigger) {
                        // Add in Profile Info
                        $_tm = jrCore_db_get_item('jrProfile', $_ot['_profile_id'], true);
                        if ($_tm && is_array($_tm)) {
                            unset($_tm['_item_id']);
                            $_ot = $_ot + $_tm;
                        }
                    }
                    break;
            }

            // Add in Quota info to item
            if (isset($_ot['profile_quota_id']) && !$skip_trigger) {
                $_tm = jrProfile_get_quota($_ot['profile_quota_id']);
                if ($_tm && is_array($_tm)) {
                    unset($_tm['_item_id']);
                    $_ot = $_ot + $_tm;
                }
            }
            unset($_tm);

        }
        return $_ot;

    }
    return false;
}

/**
 * Get multiple items by _item_id from a module datastore
 *
 * This function does NOT send out a trigger to add User/Profile information.  If you need
 * User and Profile information in the returned array of items, make sure and use jrCore_db_search_items
 * With an "in" search for your items ids - i.e. _item_id IN 1,5,7,9,12
 *
 * @param string $module Module the item belongs to
 * @param array $_ids array array of _item_id's to get
 * @param array $_keys Array of key names to get, default is all keys for each item
 * @param bool $skip_caching Set to true to force item reload (skip caching)
 * @return mixed array on success, bool false on failure
 */
function jrCore_db_get_multiple_items($module, $_ids, $_keys = null, $skip_caching = false)
{
    if (!$_ids || !is_array($_ids)) {
        return false;
    }
    // validate id's
    $_id = array();
    foreach ($_ids as $k => $id) {
        if (!jrCore_checktype($id, 'number_nz')) {
            unset($_ids[$k]);
            continue;
        }
        $_id[$id] = $id;
    }
    if (count($_id) === 0) {
        return false;
    }

    if (!$skip_caching) {
        $key = json_encode(func_get_args());
        if ($_rt = jrCore_is_cached($module, $key, false, false)) {
            return $_rt;
        }
    }

    $func = jrCore_get_active_datastore_function($module, 'db_get_multiple_items');
    if ($_tmp = $func($module, $_id, $_keys, $skip_caching)) {
        if (!$skip_caching) {
            $key = json_encode(func_get_args());
            jrCore_add_to_cache($module, $key, $_tmp, 0, 0, false, false);
        }
        return $_tmp;
    }
    return false;
}

/**
 * Core DS Plugin
 * @param string $module Module the item belongs to
 * @param array $_ids array array of _item_id's to get
 * @param array $_keys Array of key names to get, default is all keys for each item
 * @param bool $skip_caching Set to true to force item reload (skip caching)
 * @return mixed array on success, bool false on failure
 */
function _jrCore_db_get_multiple_items($module, $_ids, $_keys = null, $skip_caching = false)
{
    if (!$_ids || !is_array($_ids) || count($_ids) === 0) {
        return false;
    }
    $tbl = jrCore_db_table_name($module, 'item_key');
    if (is_array($_keys) && count($_keys) > 0) {
        $_ky = array();
        foreach ($_keys as $k) {
            if ($k == '_item_id') {
                // We handle _item_id down below...
            }
            else {
                $_ky[] = jrCore_db_escape($k);
            }
        }
    }
    $req = "SELECT `_item_id` AS i,`key` AS k,`index` AS x,`value` AS v FROM {$tbl} WHERE `_item_id` IN(" . implode(',', $_ids) . ")";
    if (isset($_ky) && count($_ky) > 0) {
        $req .= " AND `key` IN('" . implode("','", $_ky) . "')";
    }
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {

        $_nw = array();
        $_ix = array();
        foreach ($_rt as $k => $v) {
            if ($v['x'] > 0) {
                if (isset($_nw["{$v['i']}"]["{$v['k']}"]) && !is_array($_nw["{$v['i']}"]["{$v['k']}"])) {
                    // We already saw index 0 - move it to our array
                    $_nw["{$v['i']}"]["{$v['k']}"] = array($_nw["{$v['i']}"]["{$v['k']}"]);
                }
                $_nw["{$v['i']}"]["{$v['k']}"]["{$v['x']}"] = $v['v'];
                $_ix["{$v['i']}"]["{$v['k']}"]              = 1;
            }
            else {
                $_nw["{$v['i']}"]["{$v['k']}"] = $v['v'];
            }
            unset($_rt[$k]);
        }
        if (count($_ix) > 0) {
            // We have index keys that need to be sorted and re-assembled
            foreach ($_ix as $id => $keys) {
                foreach ($keys as $k => $i) {
                    ksort($_nw[$id][$k], SORT_NUMERIC);
                    $_nw[$id][$k] = implode('', $_nw[$id][$k]);
                }
                unset($_ix[$id]);
            }
        }
        unset($_ix);

        // Put things back into our incoming order
        $add_id = false;
        if ($module == 'jrUser') {
            $add_id = '_user_id';
        }
        elseif ($module == 'jrProfile') {
            $add_id = '_profile_id';
        }
        $i   = 0;
        $_rs = array();
        foreach ($_ids as $id) {
            if (isset($_nw[$id])) {
                $_rs[$i]             = $_nw[$id];
                $_rs[$i]['_item_id'] = $id;
                if ($add_id) {
                    $_rs[$i][$add_id] = $id;
                }
                $i++;
            }
        }
        unset($_nw, $_rt);
        return $_rs;
    }
    return false;
}

/**
 * Gets a single item attribute from a module datastore
 * @param string $module Module the item belongs to
 * @param int $id Item ID to retrieve
 * @param string $key Key value to return
 * @return mixed array on success, bool false on failure
 */
function jrCore_db_get_item_key($module, $id, $key)
{
    if (!jrCore_checktype($id, 'number_nz')) {
        return false;
    }
    $func = jrCore_get_active_datastore_function($module, 'db_get_item_key');
    return $func($module, $id, $key);
}

/**
 * Core DS Plugin
 * @param $module
 * @param $id
 * @param $key
 * @return bool|string
 */
function _jrCore_db_get_item_key($module, $id, $key)
{
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "SELECT `index` AS i, `value` AS v FROM {$tbl} WHERE `_item_id` = " . intval($id) . " AND `key` = '" . jrCore_db_escape($key) . "'";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        if (!isset($_rt[1])) {
            return $_rt[0]['v'];
        }
        $_ot = array();
        foreach ($_rt as $v) {
            if (isset($_ot["{$v['i']}"])) {
                $_ot["{$v['i']}"] .= $v['v'];
            }
            else {
                $_ot["{$v['i']}"] = $v['v'];
            }
        }
        ksort($_ot, SORT_NUMERIC);
        return implode('', $_ot);
    }
    return false;
}

/**
 * Updates multiple Item in a module datastore
 * @param string $module Module the DataStore belongs to
 * @param array $_data Array of Key => Value pairs for insertion
 * @param array $_core Array of Key => Value pairs for insertion - skips jrCore_db_get_allowed_item_keys()
 * @param bool $update set to FALSE to prevent _updated key from being set to UNIX_TIMESTAMP
 * @param bool $cache_reset set to FALSE to prevent cache reset
 * @param bool $exist_check set to prevent checking if item exists before updating
 * @return bool true on success, false on error
 */
function jrCore_db_update_multiple_items($module, $_data = null, $_core = null, $update = true, $cache_reset = true, $exist_check = true)
{
    global $_post, $_user;
    if (!$_data || is_null($_data) || !is_array($_data)) {
        return false;
    }

    $pfx = jrCore_db_get_prefix($module);
    foreach ($_data as $id => $_up) {

        // Must be valid ID
        if (!jrCore_checktype($id, 'number_nz')) {
            return false;
        }
        // Keys must come in as array
        if (!is_array($_up)) {
            $_data[$id] = array();
        }
        else {
            $_data[$id] = jrCore_db_get_allowed_item_keys($module, $_up);
        }

        // We're being updated
        if ($update) {
            $_data[$id]['_updated'] = 'UNIX_TIMESTAMP()';
        }

        // Check for additional core fields being overridden
        if (!is_null($_core) && isset($_core[$id]) && is_array($_core[$id])) {
            foreach ($_core[$id] as $k => $v) {
                if (strpos($k, '_') === 0) {
                    $_data[$id][$k] = $v;
                }
            }
        }

        // Check for Pending Support for this module
        // We must check for this function being called as part of another (usually save)
        // routine - we don't want to change the value if this is an update that is part of a create process
        // and we don't want to change it if the update is being done by a different module (rating, comment, etc.)
        if (!jrUser_is_admin() && isset($_post['module']) && $_post['module'] == $module && !jrCore_is_magic_view()) {
            if (!jrCore_get_flag("jrcore_created_pending_item_{$module}_{$id}")) {
                $_pnd = jrCore_get_registered_module_features('jrCore', 'pending_support');
                if ($_pnd && isset($_pnd[$module])) {
                    // Pending support is on for this module - check quota
                    // 0 = immediately active
                    // 1 = review needed on CREATE
                    // 2 = review needed on CREATE and UPDATE
                    if (isset($_user["quota_{$module}_pending"]) && $_user["quota_{$module}_pending"] == '2') {
                        $_data[$id]["{$pfx}_pending"] = 1;
                    }
                }
            }
        }
    }

    // Trigger update event
    $_li = array();
    $_lm = array();
    foreach ($_data as $id => $_v) {
        $_args      = array(
            '_item_id' => $id,
            'module'   => $module
        );
        $_data[$id] = jrCore_trigger_event('jrCore', 'db_update_item', $_v, $_args);

        // Check for actions that are linking to pending items
        $_li[$id] = 0;
        $_lm[$id] = '';
        if (isset($_v['action_pending_linked_item_id']) && jrCore_checktype($_v['action_pending_linked_item_id'], 'number_nz')) {
            $_li[$id] = (int) $_v['action_pending_linked_item_id'];
            $_lm[$id] = jrCore_db_escape($_v['action_pending_linked_item_module']);
            unset($_data[$id]['action_pending_linked_item_id']);
            unset($_data[$id]['action_pending_linked_item_module']);
        }
    }

    $func = jrCore_get_active_datastore_function($module, 'db_update_multiple_items');
    if ($func($module, $_data, $exist_check)) {

        // Check for pending
        $_rq = array();
        $pnd = jrCore_db_table_name('jrCore', 'pending');
        foreach ($_data as $id => $_vals) {
            if (!jrCore_get_flag("jrcore_created_pending_item_{$module}_{$id}")) {
                if (isset($_vals["{$pfx}_pending"]) && $_vals["{$pfx}_pending"] == '1') {
                    // Add pending entry to Pending table...
                    $_pd                     = array(
                        'module' => $module,
                        'item'   => $_vals,
                        'user'   => $_user
                    );
                    $_pd['item']['_updated'] = time();
                    $dat                     = jrCore_db_escape(jrCore_strip_emoji(json_encode($_pd), false));
                    $_rq[]                   = "(UNIX_TIMESTAMP(),'" . jrCore_db_escape($module) . "','{$id}','{$_lm[$id]}','{$_li[$id]}','{$dat}')";
                    unset($_pd);
                }
            }
        }
        if (count($_rq) > 0) {
            $req = "INSERT INTO {$pnd} (pending_created,pending_module,pending_item_id,pending_linked_item_module,pending_linked_item_id,pending_data) VALUES " . implode(',', $_rq) . "
                    ON DUPLICATE KEY UPDATE pending_created = UNIX_TIMESTAMP(), pending_data = VALUES(pending_data)";
            $cnt = jrCore_db_query($req, 'COUNT');
            if ($cnt && $cnt === 1) {
                // We INSERTED a new pending row - notify
                $_rt = jrUser_get_admin_user_ids();
                if ($_rt && is_array($_rt)) {
                    $_rp           = reset($_data);
                    $_rp['module'] = $module;
                    list($sub, $msg) = jrCore_parse_email_templates('jrCore', 'pending_item', $_rp);
                    jrCore_db_notify_admins_of_pending_item($module, $_rt, $sub, $msg);
                }
            }
        }
        if ($cache_reset) {
            $_ch = array();
            foreach ($_data as $id => $_vals) {
                $_ch[] = array($module, "{$module}-{$id}-0", true);
                $_ch[] = array($module, "{$module}-{$id}-1", true);
                $_ch[] = array($module, "{$module}-{$id}-0", false);
                $_ch[] = array($module, "{$module}-{$id}-1", false);
            }
            jrCore_delete_multiple_cache_entries($_ch);
        }
        return true;
    }
    return false;
}

/**
 * Core DS Plugin
 * @param string $module
 * @param array $_data
 * @param bool $exist_check
 * @return bool
 */
function _jrCore_db_update_multiple_items($module, $_data = null, $exist_check = true)
{
    // Get profile ids
    $_pi = array();
    if ($exist_check) {
        if (jrCore_db_key_has_index_table($module, '_profile_id')) {
            $tbl = jrCore_db_get_index_table_name($module, '_profile_id');
            $req = "SELECT `_item_id` AS i, `value` AS v FROM {$tbl} WHERE `_item_id` IN(" . implode(',', array_keys($_data)) . ')';
        }
        else {
            $tbl = jrCore_db_table_name($module, 'item_key');
            $req = "SELECT `_item_id` AS i, `value` AS v FROM {$tbl} WHERE `key` = '_profile_id' AND `_item_id` IN(" . implode(',', array_keys($_data)) . ')';
        }
        $_pi = jrCore_db_query($req, 'i', false, 'v');
        if (!$_pi) {
            // items do not exist
            return false;
        }
    }

    // Are we updating _profile_id ?
    $_up = array();
    foreach ($_data as $id => $_vals) {
        if ($exist_check && !isset($_pi[$id])) {
            // This item does not exist - skip
            unset($_data[$id]);
        }
        if (isset($_vals['_profile_id'])) {
            // We are setting the _profile_id for this item - make sure ALL keys
            // for this item have been updated to the correct _profile_id
            $_pi[$id] = (int) $_vals['_profile_id'];
            $_up[$id] = (int) $_vals['_profile_id'];
        }
    }
    if (count($_data) === 0) {
        // nothing to update
        return false;
    }

    // Update
    $_rq = array();
    $_mx = array();
    $_zo = array();
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "INSERT INTO {$tbl} (`_item_id`,`_profile_id`,`key`,`index`,`value`) VALUES ";
    foreach ($_data as $id => $_vals) {
        $_mx[$id] = array();
        $_zo[$id] = array();
        $pid      = (isset($_pi[$id])) ? intval($_pi[$id]) : 0;
        foreach ($_vals as $k => $v) {

            $val = false;
            if ($v === 'UNIX_TIMESTAMP()') {
                $req          .= "({$id},{$pid},'" . jrCore_db_escape($k) . "',0,UNIX_TIMESTAMP()),";
                $_mx[$id][$k] = '0';
                $val          = 'UNIX_TIMESTAMP()';
            }
            else {
                $v   = jrCore_strip_emoji($v);
                $len = strlen($v);
                // If our value is longer than 508 bytes we split it up
                if ($len > 508) {
                    $_tm = array();
                    while ($len) {
                        $_tm[] = mb_strcut($v, 0, 508, "UTF-8");
                        $v     = mb_strcut($v, 508, $len, "UTF-8");
                        $len   = strlen($v);
                    }
                    $idx = 0;
                    foreach ($_tm as $i => $part) {
                        $idx = ($i + 1);
                        $req .= "({$id},{$pid},'" . jrCore_db_escape($k) . "','{$idx}','" . jrCore_db_escape($part) . "'),";
                    }
                    $_mx[$id][$k] = $idx;
                    // We have to also delete any previous 0 index
                    $_zo[$id][] = $k;
                }
                else {
                    if (is_numeric($v)) {
                        $val = $v;
                    }
                    else {
                        $val = jrCore_db_escape($v);
                    }
                    $req          .= "({$id},{$pid},'" . jrCore_db_escape($k) . "',0,'" . $val . "'),";
                    $_mx[$id][$k] = '0';
                }
            }
            if ($val && jrCore_db_key_has_index_table($module, $k)) {
                $tbi = jrCore_db_get_index_table_name($module, $k);
                if ($val != 'UNIX_TIMESTAMP()') {
                    $val = "'{$val}'";
                }
                $_rq[] = "INSERT INTO {$tbi} (`_item_id`,`value`) VALUES ({$id},{$val}) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
            }
        }
    }
    $req = substr($req, 0, strlen($req) - 1) . " ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
    jrCore_db_query($req);
    if (count($_rq) > 0) {
        jrCore_db_multi_select($_rq, false);
    }

    // Cleanup
    $_tm = array();
    foreach ($_mx as $id => $_vals) {
        foreach ($_vals as $fld => $max) {
            if (jrCore_is_ds_index_needed($fld)) {
                $_tm[] = "(`_item_id` = {$id} AND `key` = '" . jrCore_db_escape($fld) . "' AND `index` > {$max})";
            }
        }
    }
    if (count($_zo) > 0) {
        foreach ($_zo as $id => $_vals) {
            foreach ($_vals as $fld) {
                if (jrCore_is_ds_index_needed($fld)) {
                    $_tm[] = "(`_item_id` = {$id} AND `key` = '" . jrCore_db_escape($fld) . "' AND `index` = 0)";
                }
            }
        }
    }
    if (count($_tm) > 0) {
        $req = "DELETE FROM {$tbl} WHERE " . implode(' OR ', $_tm);
        jrCore_db_query($req);
    }

    // Set _profile_id's for items that need changing
    if (count($_up) > 0) {
        foreach ($_up as $k => $v) {
            $req = "UPDATE {$tbl} SET `_profile_id` = '{$v}' WHERE `_item_id` = '{$k}'";
            jrCore_db_query($req);
        }
    }

    return true;
}

/**
 * Updates an Item in a module datastore
 * @param string $module Module the DataStore belongs to
 * @param int $id Unique ID to update
 * @param array $_data Array of Key => Value pairs for insertion
 * @param array $_core Array of Key => Value pairs for insertion - skips jrCore_db_get_allowed_item_keys()
 * @param bool $update set to FALSE to prevent _updated key from being set to UNIX_TIMESTAMP
 * @param bool $cache_reset set to FALSE to prevent cache reset
 * @param bool $exist_check set to prevent checking if item exists before updating
 * @return bool true on success, false on error
 */
function jrCore_db_update_item($module, $id, $_data = null, $_core = null, $update = true, $cache_reset = true, $exist_check = true)
{
    $func = jrCore_get_active_datastore_function($module, 'db_update_item');
    return $func($module, $id, $_data, $_core, $update, $cache_reset, $exist_check);
}

/**
 * Core DS plugin
 * @param string $module
 * @param int $id
 * @param null $_data
 * @param null $_core
 * @param bool $update
 * @param bool $cache_reset
 * @param bool $exist_check
 * @return bool
 */
function _jrCore_db_update_item($module, $id, $_data = null, $_core = null, $update = true, $cache_reset = true, $exist_check = true)
{
    $_dt = array(
        $id => $_data
    );
    $_cr = null;
    if (!is_null($_core)) {
        $_cr = array(
            $id => $_core
        );
    }
    return jrCore_db_update_multiple_items($module, $_dt, $_cr, $update, $cache_reset, $exist_check);
}

/**
 * Delete multiple items from a module DataStore
 * @param $module string Module DataStore belongs to
 * @param $_ids array Array of _item_id's to delete
 * @param bool $delete_media Set to false to NOT delete associated media files
 * @param mixed $profile_count If set to true, profile counts for the deleted items will be decremented
 * @param bool $cache_reset set to FALSE to prevent cache reset after deletion
 * @param bool $recycle_bin set to FALSE to prevent items being added to recycle bin
 * @return bool
 */
function jrCore_db_delete_multiple_items($module, $_ids, $delete_media = true, $profile_count = true, $cache_reset = true, $recycle_bin = true)
{
    global $_conf;
    if (!is_array($_ids) || count($_ids) === 0) {
        return false;
    }
    // validate id's
    foreach ($_ids as $id) {
        if (!jrCore_checktype($id, 'number_nz')) {
            return false;
        }
    }
    // Get all items so we can check for attached media
    $_it = jrCore_db_get_multiple_items($module, $_ids);
    if (!$_it || !is_array($_it)) {
        // no items matching
        return true;
    }

    $func = jrCore_get_active_datastore_function($module, 'db_delete_multiple_items');
    if ($func($module, $_ids, $delete_media, $profile_count)) {

        // Update display_order keys for any remaining items in this DS
        $pfx = jrCore_db_get_prefix($module);
        $_pn = jrCore_get_registered_module_features('jrCore', 'item_order_support');
        if ($_pn && isset($_pn[$module]) && isset($_it[0]) && is_array($_it[0]) && isset($_it[0]['_profile_id']) && $_it[0]['_profile_id'] > 0) {
            $pid = (int) $_it[0]['_profile_id'];
            $_ex = array(
                'search'              => array(
                    "_profile_id = {$pid}"
                ),
                'order_by'            => array(
                    "{$pfx}_display_order" => 'numerical_asc'
                ),
                'return_keys'         => array('_item_id'),
                'return_item_id_only' => true,
                'ignore_missing'      => true,
                'skip_triggers'       => true,
                'ignore_pending'      => true,
                'privacy_check'       => false,
                'quota_check'         => false,
                'limit'               => 1000
            );
            $_ex = jrCore_db_search_items($module, $_ex);
            if ($_ex && is_array($_ex)) {
                // This profile has other items in this DS - we need to set display_order to new values
                $_up = array();
                $ord = 0;
                foreach ($_ex as $i) {
                    $_up[$i] = $ord++;
                }
                jrCore_db_set_display_order($module, $_up);
                unset($_up);
            }
            unset($_ex);
        }

        // Is the Recycle Bin turned on?
        $_uc = array();
        $_pc = array();
        if ($recycle_bin && (!isset($_conf['jrCore_recycle_bin']) || $_conf['jrCore_recycle_bin'] != 'off')) {

            $_rb = array();
            $mod = jrCore_db_escape($module);
            foreach ($_it as $_item) {

                switch ($module) {
                    case 'jrProfile':
                        $iid = (int) $_item['_profile_id'];
                        $ttl = (isset($_item['profile_name'])) ? $_item['profile_name'] : 'unknown';
                        break;
                    case 'jrUser':
                        $iid = (int) $_item['_user_id'];
                        $ttl = (isset($_item['user_name'])) ? $_item['user_name'] : 'unknown';
                        break;
                    default:
                        $iid = (int) $_item['_item_id'];
                        $ttl = '?';
                        if (isset($_item["{$pfx}_title"])) {
                            $ttl = $_item["{$pfx}_title"];
                        }
                        elseif (isset($_item["{$pfx}_name"])) {
                            $ttl = $_item["{$pfx}_name"];
                        }
                        $uid = (int) $_item['_user_id'];
                        if (!isset($_uc[$uid])) {
                            $_uc[$uid] = 0;
                        }
                        $_uc[$uid]++;
                        $pid = (int) $_item['_profile_id'];
                        if (!isset($_pc[$pid])) {
                            $_pc[$pid] = 0;
                        }
                        $_pc[$pid]++;
                        break;
                }

                // When we first come into db_delete_multiple_items, it will be for the item
                // being removed (profile, audio, etc).  There are listeners that are
                // going to delete other items related to this item (i.e. ratings, comments,
                // etc.) - each of those will need to be part of our group id.
                $gid = jrCore_get_flag('jrCore_db_delete_item_group_id');
                if (!$gid || strlen($gid) < 2) {
                    // We are the FIRST item in our delete set
                    $gid = 1;
                    jrCore_set_flag('jrCore_db_delete_item_group_id', "{$module}:{$iid}");
                }

                // Handle this item's media
                if ($delete_media) {
                    $flag_key = "jrcore_db_delete_multiple_items_file_list_{$_item['_profile_id']}";
                    if (!$_fl = jrCore_get_flag($flag_key)) {
                        $_fl = jrCore_get_media_files($_item['_profile_id'], "{$module}_*");
                        if (!$_fl || !is_array($_fl)) {
                            $_fl = 'no_files';
                        }
                        jrCore_set_flag($flag_key, $_fl);
                    }
                    if ($_fl && is_array($_fl)) {
                        $_item['_delete_files'] = array();
                        foreach ($_fl as $_file) {
                            $name = basename($_file['name']);
                            if (strpos($name, "{$module}_{$iid}_") === 0) {
                                $_item['_delete_files'][] = $_file['name'];
                                $_item['rb_item_media']   = 1;
                            }
                        }
                        jrCore_queue_create('jrCore', 'db_delete_item_media', $_item);
                    }
                }

                // Store it's data in our recycle bin
                $_rb[] = "('{$gid}',UNIX_TIMESTAMP(),'{$mod}','" . intval($_item['_profile_id']) . "','{$iid}','" . jrCore_db_escape(mb_substr($ttl, 0, 254)) . "','" . jrCore_db_escape(json_encode($_item)) . "')";

                // Trigger event
                $_args = array(
                    '_item_id' => $iid,
                    'module'   => $module
                );
                jrCore_trigger_event('jrCore', 'db_delete_item', $_item, $_args);

                if ($gid == 1) {
                    jrCore_delete_flag('jrCore_db_delete_item_group_id');
                }

            }

            // Insert into Recycle Bin
            if (count($_rb) > 0) {
                $rbl = jrCore_db_table_name('jrCore', 'recycle');
                $req = "INSERT INTO {$rbl} (r_group_id,r_time,r_module,r_profile_id,r_item_id,r_title,r_data) VALUES " . implode(',', $_rb) . " ON DUPLICATE KEY UPDATE r_time = UNIX_TIMESTAMP(), r_title = VALUES(r_title), r_data = VALUES(r_data)";
                jrCore_db_query($req);
            }

        }
        else {
            // No Recycle Bin - take care of media
            if ($delete_media) {
                foreach ($_it as $_item) {

                    $_item['_delete_files'] = array();
                    foreach ($_item as $k => $v) {
                        if (strpos($k, '_extension')) {
                            $field                    = str_replace('_extension', '', $k);
                            $_item['_delete_files'][] = array($module, $field);
                        }
                    }
                    jrCore_queue_create('jrCore', 'db_delete_item_media', $_item);
                    // Trigger event
                    $_args = array(
                        '_item_id' => $_item['_item_id'],
                        'module'   => $module
                    );
                    jrCore_trigger_event('jrCore', 'db_delete_item', $_item, $_args);

                    $uid = (int) $_item['_user_id'];
                    if (!isset($_uc[$uid])) {
                        $_uc[$uid] = 0;
                    }
                    $_uc[$uid]++;
                    $pid = (int) $_item['_profile_id'];
                    if (!isset($_pc[$pid])) {
                        $_pc[$pid] = 0;
                    }
                    $_pc[$pid]++;
                }
            }
        }

        // Take care of profile counts
        if ($profile_count) {
            switch ($module) {

                // We do not maintain counts for some modules
                case 'jrProfile':
                case 'jrUser':
                case 'jrCore':
                    break;

                default:
                    // Profile Counts
                    if (count($_pc) > 0) {
                        foreach ($_pc as $pid => $cnt) {
                            jrCore_db_decrement_key('jrProfile', $pid, "profile_{$module}_item_count", $cnt, 0);
                        }
                    }
                    // User Counts
                    if (count($_uc) > 0) {
                        foreach ($_uc as $uid => $cnt) {
                            jrCore_db_decrement_key('jrUser', $uid, "user_{$module}_item_count", $cnt, 0);
                        }
                    }
                    break;
            }
        }

        // Remove from Cache and Pending
        $_pn = array();
        foreach ($_it as $_item) {
            switch ($module) {
                case 'jrProfile':
                    $iid = (int) $_item['_profile_id'];
                    break;
                case 'jrUser':
                    $iid = (int) $_item['_user_id'];
                    break;
                default:
                    $iid = (int) $_item['_item_id'];
                    break;
            }

            // reset caches
            if ($cache_reset) {
                // module, key, (user logged in)
                $_ch = array(
                    array($module, "{$module}-{$iid}-0", true),
                    array($module, "{$module}-{$iid}-1", true),
                    array($module, "{$module}-{$iid}-0", false),
                    array($module, "{$module}-{$iid}-1", false)
                );
                jrCore_delete_multiple_cache_entries($_ch);
            }

            // Remove from Pending
            if (isset($_item["{$pfx}_pending"]) && $_item["{$pfx}_pending"] >= 1) {
                $_pn[] = $iid;
            }
        }

        // Some of these items were pending - cleanup pending table
        if (count($_pn) > 0) {
            $tbl = jrCore_db_table_name('jrCore', 'pending');
            $req = "DELETE FROM {$tbl} WHERE (`pending_module` = '{$module}' AND `pending_item_id` IN(" . implode(',', $_pn) . ")) OR (`pending_linked_item_module` = '{$module}' AND `pending_linked_item_id` IN(" . implode(',', $_pn) . "))";
            jrCore_db_query($req);
        }
    }
    return true;
}

/**
 * Core DS plugin
 * @param $module
 * @param $_ids
 * @param bool|true $delete_media
 * @param bool|true $profile_count
 * @return mixed
 */
function _jrCore_db_delete_multiple_items($module, $_ids, $delete_media = true, $profile_count = true)
{
    // Delete items
    $tbl = jrCore_db_table_name($module, 'item');
    $req = "DELETE FROM {$tbl} WHERE `_item_id` IN(" . implode(',', $_ids) . ")";
    jrCore_db_query($req);

    // Delete keys
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "DELETE FROM {$tbl} WHERE `_item_id` IN(" . implode(',', $_ids) . ")";
    jrCore_db_query($req);

    if ($_tb = jrCore_db_get_all_index_tables_for_module($module)) {
        $_rq = array();
        foreach ($_tb as $key) {
            $tbl   = jrCore_db_get_index_table_name($module, $key);
            $_rq[] = "DELETE FROM {$tbl} WHERE `_item_id` IN(" . implode(',', $_ids) . ")";
        }
        if (count($_rq) > 0) {
            jrCore_db_multi_select($_rq, false);
        }
    }
    return true;
}

/**
 * Deletes an Item in the module DataStore
 * By default this function will also delete any media files that are associated with the item id!
 * @param string $module Module the DataStore belongs to
 * @param int $id Item ID to delete
 * @param bool $delete_media Set to false to NOT delete associated media files
 * @param mixed $profile_count If set to true, profile_count will be decremented by 1 for given _profile_id.  If set to an integer, it will be used as the profile_id for the counts
 * @return bool
 */
function jrCore_db_delete_item($module, $id, $delete_media = true, $profile_count = true)
{
    $func = jrCore_get_active_datastore_function($module, 'db_delete_item');
    return $func($module, $id, $delete_media, $profile_count);
}

/**
 * Core DS plugin
 * @ignore
 * @param $module
 * @param $id
 * @param bool|true $delete_media
 * @param bool|true $profile_count
 * @return bool
 */
function _jrCore_db_delete_item($module, $id, $delete_media = true, $profile_count = true)
{
    $id = array($id);
    return jrCore_db_delete_multiple_items($module, $id, $delete_media, $profile_count);
}

/**
 * Search a module DataStore and return matching items
 *
 * $_params is an array that contains all the function parameters - i.e.:
 *
 * <code>
 * $_params = array(
 *     'search' => array(
 *         'user_name = brian',
 *         'user_height > 72'
 *     ),
 *     'order_by' => array(
 *         'user_name' => 'asc',
 *         'user_height' => 'desc'
 *     ),
 *     'group_by' => '_user_id',
 *     'return_keys' => array(
 *         'user_email',
 *         'username'
 *      ),
 *     'return_count' => true|false,
 *     'limit' => 50
 * );
 *
 * wildcard searches use a % in the key name:
 * 'search' => array(
 *     'user_% = brian',
 *     '% like brian%'
 * );
 * </code>
 *
 * "no_cache" - by default search results are cached - this will disable caching if set to true
 *
 * "cache_seconds" - set length of time result set is cached
 *
 * "return_keys" - only return the matching keys
 *
 * "return_count" - If the "return_count" parameter is set to TRUE, then only the COUNT of matching
 * entries will be returned.
 *
 * "privacy_check" - by default only items that are viewable to the calling user will be returned -
 * set "privacy_check" to FALSE to disable privacy settings checking.
 *
 * "ignore_pending" - by default only items that are NOT pending are shown - set ignore_pending to
 * TRUE to skip the pending item check
 *
 * "exclude_(module)_keys" - some modules (such as jrUser and jrProfile) add extra keys into the returned
 * results - you can skip adding these extra keys in by disable the module(s) you do not want keys for.
 *
 * "skip_triggers" - don't run the db_search_params or db_search_items event triggers
 *
 * Valid Search conditions are:
 * <code>
 *  =           - "equals"
 *  !=          - "not equals"
 *  >           - greater than
 *  >=          - greater than or equal to
 *  <           - less than
 *  <=          - less than or equal to
 *  between     - between and including a low,high value - i.e. "profile_latitude between 1.50,1.60
 *  not_between - not between anf including low,high value - i.e. "profile_latitude not_between 1.50,1.60
 *  like        - wildcard text search - i.e. "user_name like %ob%" would find "robert" and "bob". % is wildcard character.
 *  not_like    - wildcard text negated search - same format as "like"
 *  in          - "in list" of values - i.e. "user_name in brian,douglas,paul,michael" would find all 4 matches
 *  not_in      - negated "in least" search - same format as "in"
 *  regexp      - MySQL regular expression match
 * </code>
 * @param string $module Module the DataStore belongs to
 * @param array $_params Search Parameters
 * @return mixed Array on success, Bool on error
 */
function jrCore_db_search_items($module, $_params)
{
    if (!$_params || !is_array($_params) || count($_params) === 0) {
        return false;
    }
    $func = jrCore_get_active_datastore_function($module, 'db_search_items');
    return $func($module, $_params);
}

/**
 * jrCore DS plugin - db_search_items
 * @param $module string module
 * @param $_params array params
 * @return array|bool|mixed
 */
function _jrCore_db_search_items($module, $_params)
{
    global $_user, $_conf;
    $_params['module'] = $module;
    // Backup copy of original params
    $_backup = $_params;

    // Other modules can provide supported parameters for searching - send
    // our trigger so those events can be added in.
    if (!isset($_params['skip_triggers']) || $_params['skip_triggers'] === false) {

        $_params = jrCore_trigger_event('jrCore', 'db_search_params', $_params, array('module' => $module));

        // Did our listener return a full result set?
        if (isset($_params['full_result_set'])) {
            return $_params['full_result_set'];
        }

        // See if a listener switched modules on us
        $_change = jrCore_get_flag('jrcore_active_trigger_args');
        if (isset($_change['module']) && $_change['module'] != $module) {
            $module            = $_change['module'];
            $_params['module'] = $module;
        }
        unset($_change);

    }

    // See if we are cached
    $cky = json_encode($_params);

    // Send out Cache Check event
    $_params = jrCore_trigger_event('jrCore', 'db_search_cache_check', $_params, array('module' => $module, 'cache_key' => $cky));

    // Check for cache
    if ((!isset($_params['no_cache']) || $_params['no_cache'] === false) && $tmp = jrCore_is_cached($module, $cky)) {
        if (!isset($_params['skip_triggers']) || $_params['skip_triggers'] === false) {
            $tmp = jrCore_trigger_event('jrCore', 'db_search_results', $tmp, $_params);
        }
        return $tmp;
    }

    // Allow a listener to provide the actual result set
    if (!isset($_params['result_set'])) {

        // We allow searching on both USER and PROFILE keys for all modules - check for those here
        if (isset($_params['search']) && is_array($_params['search'])) {
            switch ($module) {
                case 'jrProfile':
                    $_ck = array(
                        'user' => 'jrUser'
                    );
                    break;
                case 'jrUser':
                    $_ck = array(
                        'profile' => 'jrProfile'
                    );
                    break;
                default:
                    $_ck = array(
                        'user'    => 'jrUser',
                        'profile' => 'jrProfile'
                    );
                    break;
            }
            foreach ($_ck as $pfx => $mod) {
                foreach ($_params['search'] as $k => $cond) {
                    $_c = array();
                    if (strpos($cond, '||')) {
                        $tbl = jrCore_db_table_name($mod, 'item_key');
                        foreach (explode('||', $cond) as $part) {
                            if (strpos(trim($part), "{$pfx}_") === 0) {
                                if ($_sc = jrCore_db_check_for_supported_operator($part)) {
                                    // There are keys in this condition we need to handle
                                    if (strpos(' ' . $_sc[0], '%')) {
                                        $_c[] = "`key` LIKE '" . jrCore_db_escape($_sc[0]) . "' AND `value` {$_sc[1]} {$_sc[2]}";
                                    }
                                    elseif ($_sc[1] == 'between') {
                                        $_c[] = "`key` = '" . jrCore_db_escape($_sc[0]) . "' AND `value` BETWEEN {$_sc[2]} AND {$_sc[3]}";
                                    }
                                    elseif ($_sc[1] == 'not_between') {
                                        $_c[] = "`key` = '" . jrCore_db_escape($_sc[0]) . "' AND `value` NOT BETWEEN {$_sc[2]} AND {$_sc[3]}";
                                    }
                                    else {
                                        $_c[] = "`key` = '" . jrCore_db_escape($_sc[0]) . "' AND `value` {$_sc[1]} {$_sc[2]}";
                                    }
                                    unset($_params['search'][$k]);
                                }
                                else {
                                    jrCore_logger('MAJ', 'invalid OR search operator in jrCore_db_search_items parameters', array($module, $_params));
                                    return false;
                                }
                            }
                        }
                        if (count($_c) > 0) {
                            $_params['search'][] = "_{$pfx}_id IN (SELECT `_item_id` FROM {$tbl} WHERE (" . implode(' OR ', $_c) . '))';
                        }
                    }
                    // Check for "user_" and "profile_" key searches which are allowed when searching any DS
                    elseif (strpos(trim($cond), "{$pfx}_") === 0) {
                        $tbl = jrCore_db_table_name($mod, 'item_key');
                        if ($_sc = jrCore_db_check_for_supported_operator($cond)) {
                            $_params['search'][] = "_{$pfx}_id IN (SELECT `_item_id` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($_sc[0]) . "' AND `value` {$_sc[1]} {$_sc[2]})";
                            unset($_params['search'][$k]);
                        }
                        else {
                            jrCore_logger('MAJ', 'invalid OR search operator in jrCore_db_search_items parameters', array($module, $_params));
                            return false;
                        }
                    }
                }
                unset($_c);
            }
        }

        $uik = true;  // Use index key - we will set this FALSE for specific _profile_id queries
        $prf = '';
        $dob = '_created';
        $_ob = array();
        $_sc = array();
        $_ky = array();
        $_eq = array();
        $_ne = array();
        $ino = false;
        $sgb = false;
        $_so = false;
        if (isset($_params['search']) && count($_params['search']) > 0) {

            // Pre check for OR search conditions
            if (strpos(json_encode($_params['search']), '||')) {
                $_so = array();
                foreach ($_params['search'] as $k => $v) {
                    if (strpos($v, '||')) {
                        foreach (explode('||', $v) as $cond) {
                            if (!$tmp = jrCore_db_check_for_supported_operator($cond)) {
                                jrCore_logger('MAJ', 'invalid OR search operator in jrCore_db_search_items parameters', array($module, $_params));
                                return false;
                            }
                            if (strpos(' ' . $tmp[0], '%')) {
                                // Wildcard key
                                $tmp[1] = 'LIKE';
                            }
                            $_so[$k][] = $tmp;
                        }
                        if (count($_so) > 0) {
                            $_params['search'][$k] = "{$k} OR COND";
                        }
                    }
                }
            }

            // We need to be sure that != and not_in search conditions come
            // last in the search array, since if we have an = or in search
            // we may be able to exclude the != or not_in search entirely
            $_lk = array();
            $_hk = array();
            foreach ($_params['search'] as $k => $v) {
                $v = trim($v);
                if (isset($_dc[$v])) {
                    // Already seen this one...
                    unset($_params['search'][$k]);
                    continue;
                }
                $_dc[$v] = 1;
                @list($key, $opt,) = explode(' ', $v, 3);
                if (!isset($opt) || strlen($opt) === 0) {
                    // Bad Search
                    jrCore_logger('MAJ', 'invalid search criteria in jrCore_db_search_items parameters', array($module, $_params));
                    return false;
                }
                switch (jrCore_str_to_lower($opt)) {
                    case '!=':
                    case 'not_in':
                        if (trim($key) == '_profile_id') {
                            $_hk[] = $v;
                        }
                        else {
                            $_lk[] = $v;
                        }
                        break;
                    default:
                        $_lk[] = $v;
                        break;
                }
            }
            if (count($_hk) > 0) {
                $_params['search'] = array_merge($_lk, $_hk);
            }
            unset($_lk, $_hk);

            // Search prep
            $_dc = array();
            foreach ($_params['search'] as $k => $v) {
                @list($key, $opt, $val) = explode(' ', $v, 3);
                $key = jrCore_str_to_lower($key);
                if (!strpos(' ' . $key, '%')) {
                    $_ky[$key] = 1;
                }
                if (strpos($val, '(SELECT ') === 0) {
                    // We have a sub query as our match condition
                    // If this is a sub query for _profile_id we can skip a join
                    if (strpos($v, '_profile_id ') === 0) {
                        // We are looking for specific profile id's
                        $prf .= ' AND a.`_profile_id` ' . substr($v, 12) . ' ';
                        $uik = false;
                    }
                    else {
                        switch (jrCore_str_to_lower($opt)) {
                            case 'not_in':
                                $_sc[] = array($key, 'NOT IN', $val, 'no_quotes');
                                break;
                            case 'not_like':
                                $_sc[] = array($key, 'NOT LIKE', $val, 'no_quotes');
                                break;
                            default:
                                $_sc[] = array($key, $opt, $val, 'no_quotes');
                                break;
                        }
                    }
                    continue;
                }
                // Check for OR conditions (||)
                elseif ($opt == 'OR' && $val == 'COND') {
                    // We have an OR condition as our match condition
                    $_sc[] = array($key, $opt, $val, 'parens');
                    continue;
                }

                switch (jrCore_str_to_lower($opt)) {
                    case '>':
                    case '>=':
                    case '<':
                    case '<=':
                        if (strpos($val, '.')) {
                            $_sc[] = array($key, $opt, floatval($val), 'no_quotes');
                        }
                        else {
                            $_sc[] = array($key, $opt, intval($val), 'no_quotes');
                        }
                        break;

                    // Not Equal To
                    case '!=':
                        // With a NOT EQUAL operator on non _item_id, we also need to include items where the key may be MISSING or NULL
                        if ($key == '_item_id' || $key == '_profile_id' || $key == '_user_id') {
                            if ($key == '_profile_id') {
                                $val = (int) $val;
                                // Did we already get an = search for a specific profile_id?  If so then
                                // we can skip this != search condition
                                if (isset($_eq[$key]) && isset($_eq[$key][$val])) {
                                    // We have BOTH and equal AND a not equal - invalid condition
                                    if (isset($_params['fdebug']) && $_params['fdebug'] == true) {
                                        fdebug(array('jrCore_db_search_items error' => 'invalid search condition - _profile_id search values exclude each other', '_params' => $_params, '_backup' => $_backup)); // OK
                                    }
                                    return false;
                                }
                                // Note: we handle _profile_id searches separately
                                $_ne[$key][$val] = $val;
                            }
                            else {
                                $_sc[] = array($key, $opt, intval($val), 'no_quotes');
                            }
                        }
                        elseif (jrCore_db_key_found_on_all_items($key) || isset($_params['ignore_missing']) && $_params['ignore_missing'] == true) {
                            $_sc[] = array($key, $opt, $val);
                        }
                        else {
                            $tbl   = jrCore_db_table_name($module, 'item_key');
                            $vrq   = "(SELECT yy.`_item_id` FROM {$tbl} yy LEFT JOIN {$tbl} zz ON (zz.`_item_id` = yy.`_item_id` AND zz.`key` = '" . jrCore_db_escape($key) . "') WHERE yy.`key` = '_created' AND (zz.`value` != '" . jrCore_db_escape($val) . "' OR zz.`value` IS NULL))";
                            $_sc[] = array('_item_id', 'IN', $vrq, 'no_quotes', $key);
                            unset($_ky[$key]);
                        }
                        break;

                    // Equal To
                    case '=':
                        if (ctype_digit($val)) {
                            if ($key == '_profile_id') {
                                // NOTE: we do not add _profile_id searches to our $_sc (search conditions) array since they are handled separately
                                $val             = (int) $val;
                                $_eq[$key][$val] = $val;
                            }
                            else {
                                $_sc[] = array($key, $opt, $val);
                            }
                        }
                        else {
                            $_sc[] = array($key, $opt, jrCore_db_escape($val));
                        }
                        break;

                    // Between | Not_Between
                    case 'between':
                    case 'not_between':
                        if (strpos($val, ',')) {
                            list($vl1, $vl2) = explode(',', $val);
                            $vl1 = trim($vl1);
                            $vl2 = trim($vl2);
                            if (is_numeric($vl1) && is_numeric($vl2)) {
                                if (strpos(' ' . $vl1, '.')) {
                                    $vl1 = floatval($vl1);
                                }
                                else {
                                    $vl1 = intval($vl1);
                                }
                                if (strpos(' ' . $vl2, '.')) {
                                    $vl2 = floatval($vl2);
                                }
                                else {
                                    $vl2 = intval($vl2);
                                }
                                if ($vl2 < $vl1) {
                                    $val = "{$vl2},{$vl1}";
                                }
                                else {
                                    $val = "{$vl1},{$vl2}";
                                }
                                $_sc[] = array($key, jrCore_str_to_lower($opt), $val);
                            }
                        }
                        else {
                            jrCore_logger('MAJ', "invalid {$opt} search condition in jrCore_db_search_items search: {$opt}", array($module, $_params));
                            return false;
                        }
                        break;

                    // Like
                    case 'like':
                        if (strpos($val, '\%')) {
                            // We are looking explicitly for a percent sign
                            $_ps = explode('\%', $val);
                            if ($_ps && is_array($_ps)) {
                                $_pt = array();
                                foreach ($_ps as $pprt) {
                                    $_pt[] = jrCore_db_escape($pprt);
                                }
                                $_sc[] = array($key, strtoupper($opt), implode('\%', $_pt));
                                unset($_pt);
                            }
                            else {
                                $_sc[] = array($key, strtoupper($opt), jrCore_db_escape($val));
                            }
                            unset($_ps);
                        }
                        else {
                            $_sc[] = array($key, strtoupper($opt), jrCore_db_escape($val));
                        }
                        // If we do NOT get a group_by parameter, and we are doing a
                        // wildcard search on the KEY, we have to group by _item_id
                        if (!isset($_params['group_by']) && strpos(' ' . $key, '%')) {
                            $ino = '_item_id';
                        }
                        break;

                    // Not_Like
                    case 'not_like':
                        if (strpos($val, '\%')) {
                            // We are looking explicitly for a percent sign
                            $_ps = explode('\%', $val);
                            if ($_ps && is_array($_ps)) {
                                $_pt = array();
                                foreach ($_ps as $pprt) {
                                    $_pt[] = jrCore_db_escape($pprt);
                                }
                                $val = implode('\%', $_pt);
                                unset($_pt);
                            }
                        }
                        $tbl = jrCore_db_table_name($module, 'item_key');
                        if (isset($_params['ignore_missing']) && $_params['ignore_missing'] == true) {
                            if (jrCore_db_key_has_index_table($module, $key)) {
                                $vrq = "(SELECT yy.`_item_id` FROM " . jrCore_db_get_index_table_name($module, $key) . " yy WHERE yy.`value` NOT LIKE '" . jrCore_db_escape($val) . "')";
                            }
                            else {
                                $vrq = "(SELECT yy.`_item_id` FROM {$tbl} yy WHERE yy.key = '" . jrCore_db_escape($key) . "' AND yy.`value` NOT LIKE '" . jrCore_db_escape($val) . "')";
                            }
                        }
                        else {
                            $vrq = "(SELECT yy.`_item_id` FROM {$tbl} yy LEFT JOIN {$tbl} zz ON (zz.`_item_id` = yy.`_item_id` AND zz.`key` = '" . jrCore_db_escape($key) . "') WHERE yy.`key` = '_created' AND (zz.`value` NOT LIKE '" . jrCore_db_escape($val) . "' OR zz.`value` IS NULL))";
                        }
                        $_sc[] = array('_item_id', 'IN', $vrq, 'no_quotes', $key);
                        unset($_ky[$key]);
                        break;

                    case 'regexp':
                        $_sc[] = array($key, strtoupper($opt), jrCore_db_escape($val));
                        break;

                    case 'in':
                        $_vl = array();
                        foreach (explode(',', $val) as $iv) {
                            if (ctype_digit($iv)) {
                                if ($key == '_item_id' || $key == '_profile_id' || $key == '_user_id') {
                                    $_vl[] = intval($iv);
                                }
                                else {
                                    // Don't (int) here - strips leading zeros
                                    $_vl[] = "'{$iv}'";
                                }
                            }
                            else {
                                $_vl[] = "'" . jrCore_db_escape($iv) . "'";
                            }
                        }
                        if ($key == '_item_id' || $key == '_profile_id' || $key == '_user_id') {
                            if ($key == '_profile_id') {
                                if (!isset($_eq[$key])) {
                                    $_eq[$key] = array();
                                }
                                foreach ($_vl as $vpid) {
                                    $_eq[$key][$vpid] = $vpid;
                                }
                            }
                            else {
                                if (count($_vl) > 1) {
                                    $val   = "(" . implode(',', $_vl) . ')';
                                    $_sc[] = array($key, 'IN', $val, 'no_quotes');
                                }
                                else {
                                    $_sc[] = array($key, '=', reset($_vl), 'no_quotes');
                                }
                            }
                        }
                        else {
                            $val   = "(" . implode(',', $_vl) . ')';
                            $_sc[] = array($key, 'IN', $val, 'no_quotes');
                        }

                        // By default if we do NOT get an ORDER BY clause on an IN, order by FIELD unless specifically set NOT to
                        if (!isset($_params['order_by']) && !isset($_params['return_item_id_only'])) {
                            // If our key is _item_id, we can skip the GROUP BY
                            if ($key == '_item_id') {
                                $sgb = true;
                            }
                            $ino = $key;
                            if (isset($_do)) {
                                $_do = array_merge($_do, $_vl);
                            }
                            else {
                                $_do = $_vl;
                            }
                        }
                        unset($_vl);

                        break;

                    case 'not_in':
                        // If we are excluding specific _profile_id's then we can see if we have already included them
                        if ($key == '_profile_id' && isset($_eq[$key])) {
                            $_pval = explode(',', $val);
                            foreach ($_pval as $ik => $iv) {
                                $iv = intval($iv);
                                if (isset($_eq['_profile_id'][$iv])) {
                                    // We have both an EQUAL and a NOT EQUAL for this profile_id - exclude
                                    unset($_eq['_profile_id'][$iv]);
                                    unset($_pval[$ik]);
                                }
                            }
                            // If we come out with equals left, we can exclude all the not_in's left since we would never match them
                            if (count($_eq['_profile_id']) > 0) {
                                if (isset($_params['fdebug']) && $_params['fdebug'] == true) {
                                    fdebug(array('jrCore_db_search_items error' => "removing search condition {$k} due to equals exclusion", '_params' => $_params, '_backup' => $_backup)); // OK
                                }
                                unset($_params['search'][$k]);
                                unset($_pval);
                                continue 2;
                            }
                            if (isset($_do)) {
                                $_do = $_eq['_profile_id'];
                            }
                            // We have some not_in profile_id's left - restore $val
                            if (count($_pval) > 0) {
                                $val = implode(',', $_pval);
                                unset($_pval);
                            }
                            else {
                                // We have no not-in conditions left - unset and continue
                                unset($_params['search'][$k]);
                                unset($_pval);
                                continue 2;
                            }
                        }

                        $_vl = array();
                        foreach (explode(',', $val) as $iv) {
                            switch ($key) {
                                case '_item_id':
                                case '_user_id':
                                case '_profile_id':
                                case '_created':
                                case '_updated':
                                    $_vl[] = intval($iv);
                                    break;
                                default:
                                    if (ctype_digit($iv)) {
                                        // Don't int here - strips leading zeros
                                        $_vl[] = "'{$iv}'";
                                    }
                                    else {
                                        $_vl[] = "'" . jrCore_db_escape($iv) . "'";
                                    }
                                    break;
                            }
                        }
                        $val = '(' . implode(',', $_vl) . ')';
                        // ALL items have a _item_id/_profile_id/_user_id so no need to do the extra join here
                        if (jrCore_db_key_found_on_all_items($key) || (isset($_params['ignore_missing']) && $_params['ignore_missing'] == true)) {
                            // If we have a _profile_id NOT IN, and we are running our privacy check down below,
                            // we can skip creating another JOIN condition and just add to the existing profile_id check
                            if ($key == '_profile_id') {
                                if (!isset($_ne[$key])) {
                                    $_ne[$key] = array();
                                }
                                foreach ($_vl as $vpid) {
                                    $_ne[$key][$vpid] = $vpid;
                                }
                            }
                            else {
                                // NOTE: We use "no_quotes" here since the values in $_vl have been quoted as needed
                                if (count($_vl) == 1) {
                                    $_sc[] = array($key, '!=', reset($_vl), 'no_quotes');
                                }
                                else {
                                    $_sc[] = array($key, 'NOT IN', $val, 'no_quotes');
                                }
                            }
                        }
                        else {
                            $tbl   = jrCore_db_table_name($module, 'item_key');
                            $vrq   = "(SELECT yy.`_item_id` FROM {$tbl} yy LEFT JOIN {$tbl} zz ON (zz.`_item_id` = yy.`_item_id` AND zz.`key` = '" . jrCore_db_escape($key) . "') WHERE yy.`key` = '_created' AND (zz.`value` NOT IN{$val} OR zz.`value` IS NULL))";
                            $_sc[] = array('_item_id', 'IN', $vrq, 'no_quotes', $key);
                            unset($_ky[$key]);
                        }
                        unset($_vl);
                        break;

                    default:
                        jrCore_logger('MAJ', "invalid search operator in jrCore_db_search_items search: {$opt}", array($module, $_params));
                        return false;
                        break;
                }
            }
            unset($_dc);
        }

        // Module prefix
        $pfx = jrCore_db_get_prefix($module);

        // Check for Pending Support
        $_pn = jrCore_get_registered_module_features('jrCore', 'pending_support');
        if (!jrUser_is_admin() && isset($_pn) && isset($_pn[$module]) && !isset($_params['ignore_pending'])) {
            // If this is a profile owner viewing their own profile we show them everything
            if (!isset($_params['profile_id']) || !jrProfile_is_profile_owner($_params['profile_id'])) {
                // Pending support is on for this module - check status
                // 0 = immediately active
                // 1 = review needed
                // Let's see if anything is pending
                $_pq = jrCore_get_flag('jrcore_db_search_items_pending_modules');
                if (!$_pq) {
                    // This is here to avoid the extra search condition unless it is needed
                    $ptb = jrCore_db_table_name('jrCore', 'pending');
                    $prq = "SELECT pending_module FROM {$ptb} GROUP BY pending_module";
                    $_pq = jrCore_db_query($prq, 'pending_module', false, 'pending_module');
                    jrCore_set_flag('jrcore_db_search_items_pending_modules', $_pq);
                }
                if ($_pq && is_array($_pq) && isset($_pq[$module])) {
                    $_sc[]                 = array("{$pfx}_pending", '=', '0');
                    $_ky["{$pfx}_pending"] = 1;
                }
            }
        }

        // in order to properly ORDER BY, we must be including the key we are
        // ordering by in our JOIN - thus, if the user specifies an ORDER BY on
        // a key that they did not search on, then we must add in an IS NOT NULL
        // condition for the order by key
        $custom_order_by = array();
        if (isset($_params['order_by']) && $_params['order_by'] !== false) {
            if (is_array($_params['order_by'])) {

                // Check for some special orders
                if (count($_params['order_by']) === 1) {
                    if (isset($_params['order_by']["{$pfx}_display_order"])) {
                        // Sort by display order, _item_id desc default
                        $_params['order_by']['_item_id'] = 'numerical_desc';
                    }
                    elseif ($module == 'jrProfile' && isset($_params['order_by']['_profile_id'])) {
                        // Order by _item_id
                        $_params['order_by'] = array('_item_id' => $_params['order_by']['_profile_id']);
                    }
                    elseif ($module == 'jrUser' && isset($_params['order_by']['_user_id'])) {
                        // Order by _item_id
                        $_params['order_by'] = array('_item_id' => $_params['order_by']['_user_id']);
                    }
                }

                foreach ($_params['order_by'] as $k => $v) {
                    // if ($k == 0 && $k != '_item_id') {
                    if ($k != '_item_id') {
                        $dob = $k;
                    }
                    // Check for random order - no need to join
                    if (!isset($_ky[$k]) && $k != '_item_id' && $v != 'random') {

                        // See if we have existing search queries.  This sub query is needed
                        // since if there are NO search conditions, then the table will NOT
                        // have been joined in order to do the order - but if we join on a
                        // not exists condition (!=, not_in, not_like) we have to include items
                        // that do NOT have the DS key set
                        switch ($k) {

                            // We know these keys exist on all items
                            case '_created':
                            case '_updated':
                            case '_user_id':
                            case '_profile_id':
                                $_sc[] = array($k, '>', 0);
                                break;

                            // Any other key may NOT exist on all items
                            default:
                                if (jrCore_db_key_found_on_all_items($k) || isset($_params['ignore_missing']) && $_params['ignore_missing'] == true) {
                                    // This key is found on all items OR we've been specifically told to ignore any items that are missing this key
                                    $vrq = "`key` = '" . jrCore_db_escape($k) . "'";
                                }
                                else {
                                    // NOTE: Do not use an index table here since an index table
                                    // only contains entries for items that HAVE the key
                                    $tbl                 = jrCore_db_table_name($module, 'item_key');
                                    $vrq                 = "((a.`key` = '" . jrCore_db_escape($k) . "') OR (a.`key` = '_created' AND a.`_item_id` NOT IN(SELECT `_item_id` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($k) . "')))";
                                    $custom_order_by[$k] = 1;
                                }
                                $_sc[] = array($k, 'CUSTOM', $vrq);
                                break;
                        }
                        $_ky[$k] = 1;
                    }
                }
            }
            else {
                // We have a bad order_by
                if (jrCore_is_developer_mode()) {
                    jrCore_logger('MAJ', "invalid order_by in jrCore_db_search_items - value must be an array", array($module, $_params));
                }
            }
        }

        // Lastly - if we get a group_by parameter, we have to make sure the field
        // that is being grouped on is joined to the query so it can be grouped
        $_gb = array();
        if (isset($_params['group_by']) && strlen($_params['group_by']) > 0 && strpos($_params['group_by'], '_item_id') !== 0) {
            // Check for special UNIQUE in our group_by
            if (strpos($_params['group_by'], ' UNIQUE')) {
                list($gfd,) = explode(' ', $_params['group_by']);
                $gfd = trim($gfd);
                $gtb = jrCore_db_table_name($module, 'item_key');
                $grq = "SELECT MAX(`_item_id`) AS iid FROM {$gtb} WHERE `key` = '" . jrCore_db_escape($gfd) . "' GROUP BY `value`";
                $_gr = jrCore_db_query($grq, 'iid', false, 'iid');
                if ($_gr && is_array($_gr)) {
                    $_sc[] = array('_item_id', 'IN', '(' . implode(',', $_gr) . ')', 'no_quotes');
                }
                unset($_params['group_by']);
            }
            else {
                foreach (explode(',', $_params['group_by']) as $k => $gby) {
                    $gby = trim($gby);
                    if (!isset($_ky[$gby])) {
                        $_sc[] = array($gby, 'IS NOT', 'NULL');
                    }
                    $_gb[$gby] = $k;
                }
            }
        }
        // Make sure we got something
        if (!isset($_sc) || !is_array($_sc) || count($_sc) === 0) {
            $_sc[] = array('_item_id', '>', 0);
        }

        // To try and avoiding creating temp tables, we need to make sure if we have
        // an ORDER BY clause, the table that is being ordered on needs to be the
        // first table in the query
        // https://dev.mysql.com/doc/refman/5.0/en/order-by-optimization.html
        if (isset($_params['order_by']) && is_array($_params['order_by'])) {
            $o_key = array_keys($_params['order_by']);
            $o_key = reset($o_key);
            $_stmp = array();
            $found = false;
            foreach ($_sc as $k => $v) {
                if (!$found && $v[0] == $o_key) {
                    $_stmp[0] = $v;
                    $found    = true;
                }
                else {
                    $t_key         = ($k + 1);
                    $_stmp[$t_key] = $v;
                }
            }
            ksort($_stmp, SORT_NUMERIC);
            $_sc = array_values($_stmp);
            unset($_stmp, $o_key, $found, $t_key);
        }

        // Build query and get data
        $idx = true;
        $_al = range('a', 'z');

        // Allow modules to tell us what key names do NOT need an "index check" - i.e.
        // if a key is always going to contain a short value (i.e. a number, etc.)
        // then there is no need for the index < 2 check to be added on the JOIN
        // condition.  This can save a temporary table creation
        $_ii = array();
        $_ag = array(
            'module'    => $module,
            'params'    => $_params,
            'cache_key' => $cky
        );
        $_ii = jrCore_trigger_event('jrCore', 'db_search_simple_keys', $_ii, $_ag);

        $req = '';       // Main data Query
        $_jc = array();  // saves key values we matched in our JOIN condition so we can skip them in the WHERE condition
        $_di = array();

        foreach ($_sc as $k => $v) {

            // Save for our "order by" below - we must be searching on a column to order by it
            $als            = $_al[$k];
            $_ob["{$v[0]}"] = $als;

            // Does this key have a dedicated index column?
            $kdx = false;
            $tba = jrCore_db_table_name($module, 'item_key');
            if ($uik && jrCore_db_key_has_index_table($module, $v[0])) {
                $kdx     = true;
                $tba     = jrCore_db_get_index_table_name($module, $v[0]);
                $_di[$k] = $v[0];
            }

            if ($k == 0) {
                if (is_array($_so)) {
                    // With an OR condition we have to group on the item_id or we can
                    // get multiple results for the same key
                    $req .= "SELECT DISTINCT(a.`_item_id`) AS _item_id FROM {$tba} a\n";
                    $idx = false;
                }
                else {
                    $req .= "SELECT a.`_item_id` AS _item_id FROM {$tba} a\n";
                }
            }
            // wildcard
            elseif (strpos(' ' . $v[0], '%') || $v[1] == 'OR') {
                if (!$idx) {
                    // We're already doing a DISTINCT so no need for index requirement
                    $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id`)\n";
                }
                else {
                    if (is_array($_ii) && isset($_ii["{$v[0]}"]) || !jrCore_is_ds_index_needed($v[0])) {
                        $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id`)\n";
                    }
                    else {
                        if ($kdx) {
                            $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id`)\n";
                        }
                        else {
                            $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`index` < 2)\n";
                        }
                    }
                }
            }
            elseif ($v[0] !== '_item_id') {
                if (!$idx) {
                    // We're already doing a DISTINCT so no need for index requirement
                    if (isset($custom_order_by["{$v[0]}"])) {
                        if ($kdx) {
                            $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id`)\n";
                        }
                        else {
                            $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`key` = '{$v[0]}')\n";
                        }
                    }
                    else {
                        if (isset($v[3]) && $v[3] == 'no_quotes' || $v[2] == 'NULL') {
                            if ($kdx) {
                                $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`value` {$v[1]} {$v[2]})\n";
                            }
                            else {
                                $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`key` = '{$v[0]}' AND {$als}.`value` {$v[1]} {$v[2]})\n";
                            }
                        }
                        elseif ($v[1] == 'between') {
                            list($v1, $v2) = explode(',', $v[2]);
                            if ($kdx) {
                                $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`value` BETWEEN {$v1} AND {$v2})\n";
                            }
                            else {
                                $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`key` = '{$v[0]}' AND {$als}.`value` BETWEEN {$v1} AND {$v2})\n";
                            }
                        }
                        elseif ($v[1] == 'not_between') {
                            list($v1, $v2) = explode(',', $v[2]);
                            if ($kdx) {
                                $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`value` NOT BETWEEN {$v1} AND {$v2})\n";
                            }
                            else {
                                $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`key` = '{$v[0]}' AND {$als}.`value` NOT BETWEEN {$v1} AND {$v2})\n";
                            }
                        }
                        else {
                            if ($kdx) {
                                $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`value` {$v[1]} '{$v[2]}')\n";
                            }
                            else {
                                $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`key` = '{$v[0]}' AND {$als}.`value` {$v[1]} '{$v[2]}')\n";
                            }
                        }
                    }
                }
                else {
                    switch ($v[0]) {
                        case '_item_id':
                        case '_user_id':
                        case '_created':
                        case '_updated':
                            // No index needed on keys we know cannot be longer than 512
                            if ($kdx) {
                                $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id`)\n";
                            }
                            else {
                                $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`key` = '{$v[0]}')\n";
                            }
                            break;
                        default:
                            if (is_array($_ii) && isset($_ii["{$v[0]}"]) || !jrCore_is_ds_index_needed($v[0])) {
                                if (isset($custom_order_by["{$v[0]}"])) {
                                    if ($kdx) {
                                        $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id`)\n";
                                    }
                                    else {
                                        $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`key` = '{$v[0]}')\n";
                                    }
                                }
                                else {
                                    if (isset($v[3]) && $v[3] == 'no_quotes' || $v[2] == 'NULL') {
                                        if ($kdx) {
                                            $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`value` {$v[1]} {$v[2]})\n";
                                        }
                                        else {
                                            $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`key` = '{$v[0]}' AND {$als}.`value` {$v[1]} {$v[2]})\n";
                                        }
                                    }
                                    elseif ($v[1] == 'between') {
                                        list($v1, $v2) = explode(',', $v[2]);
                                        if ($kdx) {
                                            $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`value` BETWEEN {$v1} AND {$v2})\n";
                                        }
                                        else {
                                            $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`key` = '{$v[0]}' AND {$als}.`value` BETWEEN {$v1} AND {$v2})\n";
                                        }
                                    }
                                    elseif ($v[1] == 'not_between') {
                                        list($v1, $v2) = explode(',', $v[2]);
                                        if ($kdx) {
                                            $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`value` NOT BETWEEN {$v1} AND {$v2})\n";
                                        }
                                        else {
                                            $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`key` = '{$v[0]}' AND {$als}.`value` NOT BETWEEN {$v1} AND {$v2})\n";
                                        }
                                    }
                                    elseif ($v[1] == 'CUSTOM') {
                                        // [0] => forum_updated
                                        // [1] => CUSTOM
                                        // [2] => a.`key` = 'forum_updated'
                                        if ($kdx) {
                                            $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id`)\n";
                                        }
                                        else {
                                            $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.{$v[2]})\n";
                                        }
                                    }
                                    else {
                                        if ($kdx) {
                                            $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`value` {$v[1]} '{$v[2]}')\n";
                                        }
                                        else {
                                            $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`key` = '{$v[0]}' AND {$als}.`value` {$v[1]} '{$v[2]}')\n";
                                        }
                                    }
                                    $_jc["{$v[0]}"] = $v;
                                }
                            }
                            else {
                                if ($kdx) {
                                    $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id`)\n";
                                }
                                else {
                                    $req .= "JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`key` = '{$v[0]}' AND {$als}.`index` < 2)\n";
                                }
                            }
                            break;
                    }
                }
            }
            // See if this is our group_by column
            if (isset($_gb["{$v[0]}"])) {
                if (!isset($group_by)) {
                    $group_by = " GROUP BY {$als}.`value`";
                }
                else {
                    $group_by .= ",{$als}.`value`";
                }
            }
        }

        // Privacy Check - non admin users
        // 0 = Private
        // 1 = Global
        // 2 = Shared
        // 3 = Shared but Visible in Search
        $add = '';
        $aeq = false; // if $aeq is TRUE, we "add our equals" SQL to the query
        if (!jrUser_is_admin() && (!isset($_params['privacy_check']) || $_params['privacy_check'] !== false)) {

            // Get profiles that are NOT public and are allowed to change their profile privacy
            $_pp = jrCore_db_get_private_profiles();

            // Do we have any non-public profiles?
            if (is_array($_pp)) {

                // We have SOME private profiles
                // $_pp is in the format profile_id => profile_private
                $npp = count($_pp);
                if ($npp > 0) {

                    if (!jrUser_is_logged_in()) {

                        // If we are searching for a specific _profile_id we can check those here
                        // and see if any of them are PRIVATE profiles - if so we exclude those
                        if (isset($_eq['_profile_id']) && is_array($_eq['_profile_id']) && count($_eq['_profile_id']) > 0) {

                            // If we received a NOT EQUALS _profile_id, remove those here
                            if (isset($_ne['_profile_id']) && is_array($_ne['_profile_id'])) {
                                $_eq['_profile_id'] = jrCore_create_combined_equal_array($_eq['_profile_id'], $_ne['_profile_id']);
                                unset($_ne['_profile_id']);
                            }
                            if (count($_eq['_profile_id']) === 0) {
                                // We have no _profile_id's left over that this user can view - exit
                                if (isset($_params['fdebug']) && $_params['fdebug'] == true) {
                                    fdebug(array('jrCore_db_search_items error' => "privacy check excluded _profile_ids resulted in no profile_ids left in _eq", '_params' => $_params, '_backup' => $_backup)); // OK
                                }
                                return false;
                            }

                            foreach ($_eq['_profile_id'] as $k => $epid) {
                                if (isset($_pp[$epid])) {
                                    // This profile is a PRIVATE profile - exclude
                                    unset($_eq['_profile_id'][$k]);
                                }
                            }
                            if (count($_eq['_profile_id']) === 0) {
                                // We have no _profile_id's left over that this user can view - exit
                                if (isset($_params['fdebug']) && $_params['fdebug'] == true) {
                                    fdebug(array('jrCore_db_search_items error' => "privacy check excluded all _profile_id matches", '_params' => $_params, '_backup' => $_backup)); // OK
                                }
                                return false;
                            }

                            if (isset($_di[0])) {
                                if (jrCore_db_key_has_index_table($module, '_profile_id')) {
                                    $tbl = jrCore_db_get_index_table_name($module, '_profile_id');
                                    $add = "AND a.`_item_id` IN(SELECT `_item_id` FROM {$tbl} WHERE `value` IN(" . implode(',', $_eq['_profile_id']) . "))\n";
                                }
                                else {
                                    $tbl = jrCore_db_table_name($module, 'item_key');
                                    $add = "AND a.`_item_id` IN(SELECT `_item_id` FROM {$tbl} WHERE `key` = '_created' AND `_profile_id` IN(" . implode(',', $_eq['_profile_id']) . "))\n";
                                }
                            }
                            else {
                                $add = "AND a.`_profile_id` IN(" . implode(',', $_eq['_profile_id']) . ")\n";
                            }
                            unset($_eq['_profile_id']);
                        }
                        else {

                            // We did not get a profile_id EQUALS - if we got a NOT EQUALS
                            // let's add those into $_pp so we can exclude the extra query
                            if (isset($_ne['_profile_id']) && is_array($_ne['_profile_id'])) {
                                foreach ($_ne['_profile_id'] as $npid) {
                                    $_pp[$npid] = $npid;
                                }
                                unset($_ne['_profile_id']);
                            }

                            // Users that are not logged in only see global profiles
                            if (isset($_di[0])) {
                                if (jrCore_db_key_has_index_table($module, '_profile_id')) {
                                    $tbl = jrCore_db_get_index_table_name($module, '_profile_id');
                                    $add = "AND a.`_item_id` NOT IN(SELECT `_item_id` FROM {$tbl} WHERE `value` IN(" . implode(',', array_keys($_pp)) . "))\n";
                                }
                                else {
                                    $tbl = jrCore_db_table_name($module, 'item_key');
                                    $add = "AND a.`_item_id` NOT IN(SELECT `_item_id` FROM {$tbl} WHERE `key` = '_created' AND `_profile_id` IN(" . implode(',', array_keys($_pp)) . "))\n";
                                }
                            }
                            else {
                                $add .= "AND a.`_profile_id` NOT IN(" . implode(',', array_keys($_pp)) . ")\n";
                            }
                        }

                    }
                    else {

                        // Users that are logged in see:
                        // global profiles
                        // their own profiles
                        // any profiles they follow
                        // (jrProfile list only) any profiles with profile_private set to "3"
                        $_pr = array();
                        $hid = (int) jrUser_get_profile_home_key('_profile_id');
                        if ($hid > 0) {
                            $_pr[] = $hid;
                        }
                        if (isset($_user['user_active_profile_id']) && jrCore_checktype($_user['user_active_profile_id'], 'number_nz') && $_user['user_active_profile_id'] != $hid) {
                            $_pr[] = (int) $_user['user_active_profile_id'];
                        }
                        if (jrCore_module_is_active('jrFollower')) {
                            // If we are logged in, we can see GLOBAL profiles as well as profiles we are followers of
                            if ($_ff = jrFollower_get_profiles_followed($_user['_user_id'])) {
                                $_pr = array_merge($_ff, $_pr);
                                unset($_ff);
                            }
                        }
                        // Power/Multi users can always see the profiles they manage
                        // $_tm will be an array of profile_id => user_id entries
                        $_tm = jrProfile_get_user_linked_profiles($_user['_user_id']);
                        if ($_tm && is_array($_tm)) {
                            foreach ($_tm as $lpid => $luid) {
                                if (!$_ne || !isset($_ne[$lpid])) {
                                    $_pr[] = $lpid;
                                }
                            }
                            unset($_tm);
                        }
                        if (count($_pr) > 0) {

                            if ($module == 'jrProfile') {
                                // This is a jrProfile list - any profile's with profile_private set to 3 must be removed
                                foreach ($_pp as $pid => $ppi) {
                                    if ($ppi == 3) {
                                        unset($_pp[$pid]);
                                    }
                                }
                            }

                            // Check profiles we have access to
                            foreach ($_pr as $pid) {
                                if (isset($_pp[$pid])) {
                                    // We have access to this profile - remove from private list
                                    unset($_pp[$pid]);
                                }
                            }
                            if (count($_pp) > 0) {

                                // We still have profiles in the private list that we cannot see - if these
                                // profile_id's are in our EQUAL array, remove them
                                if (isset($_eq['_profile_id'])) {
                                    foreach ($_eq['_profile_id'] as $k => $pid) {
                                        if (isset($_pp[$pid])) {
                                            unset($_eq['_profile_id'][$k]);
                                        }
                                    }
                                    // If we received a NOT EQUALS _profile_id, remove those here
                                    if (isset($_ne['_profile_id']) && is_array($_ne['_profile_id'])) {
                                        $_eq['_profile_id'] = jrCore_create_combined_equal_array($_eq['_profile_id'], $_ne['_profile_id']);
                                        unset($_ne['_profile_id']);
                                    }
                                    if (count($_eq['_profile_id']) === 0) {
                                        // We have no _profile_id's left over that this user can view - exit
                                        if (isset($_params['fdebug']) && $_params['fdebug'] == true) {
                                            fdebug(array('jrCore_db_search_items error' => "privacy check excluded all _profile_id matches (2)", '_params' => $_params, '_backup' => $_backup)); // OK
                                        }
                                        return false;
                                    }

                                    if (isset($_di[0])) {
                                        if (jrCore_db_key_has_index_table($module, '_profile_id')) {
                                            $tbl = jrCore_db_get_index_table_name($module, '_profile_id');
                                            $add = "AND a.`_item_id` IN(SELECT `_item_id` FROM {$tbl} WHERE `value` IN(" . implode(',', $_eq['_profile_id']) . "))\n";
                                        }
                                        else {
                                            $tbl = jrCore_db_table_name($module, 'item_key');
                                            $add = "AND a.`_item_id` IN(SELECT `_item_id` FROM {$tbl} WHERE `key` = '_created' AND `_profile_id` IN(" . implode(',', $_eq['_profile_id']) . "))\n";
                                        }
                                    }
                                    else {
                                        $add = "AND a.`_profile_id` IN(" . implode(',', $_eq['_profile_id']) . ")\n";
                                    }
                                    unset($_eq['_profile_id']);
                                }
                                else {
                                    // Add any NOT EQUALS profiles_id's into our privacy check
                                    if (isset($_ne['_profile_id']) && is_array($_ne['_profile_id'])) {
                                        foreach ($_ne['_profile_id'] as $npid) {
                                            $_pp[$npid] = 1;
                                        }
                                        unset($_ne['_profile_id']);
                                    }
                                    // Make sure we exclude those in our privacy list
                                    if (isset($_di[0])) {
                                        if (jrCore_db_key_has_index_table($module, '_profile_id')) {
                                            $tbl = jrCore_db_get_index_table_name($module, '_profile_id');
                                            $add = "AND a.`_item_id` NOT IN(SELECT `_item_id` FROM {$tbl} WHERE `value` IN(" . implode(',', array_keys($_pp)) . "))\n";
                                        }
                                        else {
                                            $tbl = jrCore_db_table_name($module, 'item_key');
                                            $add = "AND a.`_item_id` NOT IN(SELECT `_item_id` FROM {$tbl} WHERE `key` = '_created' AND `_profile_id` IN(" . implode(',', array_keys($_pp)) . "))\n";
                                        }
                                    }
                                    else {
                                        $add .= " AND a.`_profile_id` NOT IN(" . implode(',', array_keys($_pp)) . ")\n";
                                    }
                                }
                            }
                            else {
                                // User has access to all profile id's in $_pp
                                $aeq = true;
                            }
                        }
                        else {
                            // User belongs to no profiles - should not get here
                            $aeq = true;
                        }
                    }
                }
                else {
                    // There are no private profiles on the system (should not get here)
                    $aeq = true;
                }
            }
            else {
                // There are no private profiles on the system
                $aeq = true;
            }
        }
        else {
            // Admin user - bypass privacy checking
            $aeq = true;
        }

        if ($aeq && isset($_eq['_profile_id']) && is_array($_eq['_profile_id']) && count($_eq['_profile_id']) > 0) {

            // If we have been given BOTH equals and NOT equals for profiles, we want to get rid
            // of the NOT equals - go through the equals and remove any that are NOT equals
            if (isset($_ne['_profile_id']) && is_array($_ne['_profile_id']) && count($_ne['_profile_id']) > 0) {
                $_eq['_profile_id'] = jrCore_create_combined_equal_array($_eq['_profile_id'], $_ne['_profile_id']);
                if (count($_eq['_profile_id']) === 0) {
                    // We've removed all profile id's - short circuit
                    if (isset($_params['fdebug']) && $_params['fdebug'] == true) {
                        fdebug(array('jrCore_db_search_items error' => "both include and exclude _profile_id params resulted in no profile_ids", '_params' => $_params, '_backup' => $_backup)); // OK
                    }
                    return false;
                }
                unset($_ne['_profile_id']);  // We no longer need to add the NOT EQUALS
            }

            if (isset($_di[0])) {
                if (jrCore_db_key_has_index_table($module, '_profile_id')) {
                    $tbl = jrCore_db_get_index_table_name($module, '_profile_id');
                    $add = "AND a.`_item_id` IN(SELECT `_item_id` FROM {$tbl} WHERE `value` IN(" . implode(',', $_eq['_profile_id']) . "))\n";
                }
                else {
                    $tbl = jrCore_db_table_name($module, 'item_key');
                    $add = "AND a.`_item_id` IN(SELECT `_item_id` FROM {$tbl} WHERE `key` = '_created' AND `_profile_id` IN(" . implode(',', $_eq['_profile_id']) . "))\n";
                }
            }
            else {
                $add = "AND a.`_profile_id` IN(" . implode(',', $_eq['_profile_id']) . ")\n";
            }
        }

        // We're excluding specific profile_id's from our search
        if (isset($_ne['_profile_id']) && is_array($_ne['_profile_id']) && count($_ne['_profile_id']) > 0) {
            if (isset($_di[0])) {
                if (jrCore_db_key_has_index_table($module, '_profile_id')) {
                    $tbl = jrCore_db_get_index_table_name($module, '_profile_id');
                    $add = "AND a.`_item_id` NOT IN(SELECT `_item_id` FROM {$tbl} WHERE `value` IN(" . implode(',', $_ne['_profile_id']) . "))\n";
                }
                else {
                    $tbl = jrCore_db_table_name($module, 'item_key');
                    $add = "AND a.`_item_id` NOT IN(SELECT `_item_id` FROM {$tbl} WHERE `key` = '_created' AND `_profile_id` IN(" . implode(',', $_ne['_profile_id']) . "))\n";
                }
            }
            else {
                $add = "AND a.`_profile_id` NOT IN(" . implode(',', $_ne['_profile_id']) . ")\n";
            }
        }

        $_sc = array_values($_sc);
        $req .= 'WHERE ';
        foreach ($_sc as $k => $v) {

            if ($k > 0 && isset($_jc["{$v[0]}"])) {
                continue;
            }

            if ($k == 0) {
                if ($v[0] == '_item_id') {
                    if ($v[2] == 'NULL' || (isset($v[3]) && $v[3] == 'no_quotes')) {
                        if ($v[1] == '>' && $v[2] == '0') {
                            $req .= "a.`key` = '{$dob}'\n";
                        }
                        else {
                            $req .= "(a.`key` = '{$dob}' AND a.`_item_id` {$v[1]} {$v[2]})\n";
                        }
                    }
                    else {
                        if ($v[1] == 'between') {
                            list($v1, $v2) = explode(',', $v[2]);
                            $req .= "(a.`key` = '{$dob}' AND a.`_item_id` BETWEEN {$v1} AND {$v2})\n";
                        }
                        elseif ($v[1] == 'not_between') {
                            list($v1, $v2) = explode(',', $v[2]);
                            $req .= "(a.`key` = '{$dob}' AND a.`_item_id` NOT BETWEEN {$v1} AND {$v2})\n";
                        }
                        else {
                            $req .= "(a.`key` = '{$dob}' AND a.`_item_id` {$v[1]} '{$v[2]}')\n";
                        }
                    }
                }
                elseif ($v[1] == 'CUSTOM') {
                    if (isset($_di[$k])) {
                        $req .= "1 = 1\n";
                    }
                    else {
                        if (strpos($v[2], '`key`') === 0) {
                            $req .= "{$_al[$k]}.{$v[2]}\n";
                        }
                        else {
                            $req .= "{$v[2]}\n";
                        }
                    }
                }
                elseif ($v[1] == 'IS OR IS NOT') {
                    $req .= "a.`key` = '{$v[0]}'\n";
                }
                elseif (isset($v[3]) && $v[3] == 'parens' && isset($_so["{$v[0]}"])) {
                    $_bd = array();
                    // ((a.key = 'something' AND value = 1) OR (a.key = 'other' AND value = 2))
                    $req .= '(';
                    foreach ($_so["{$v[0]}"] as $_part) {
                        if ($_part[0] == '_item_id') {
                            if ($_part[1] == 'between') {
                                $req .= "(a.`key` = '_created' AND a.`_item_id` BETWEEN {$_part[2]} AND {$_part[3]})\n";
                            }
                            elseif ($_part[1] == 'not_between') {
                                $req .= "(a.`key` = '_created' AND a.`_item_id` NOT BETWEEN {$_part[2]} AND {$_part[3]})\n";
                            }
                            else {
                                $_bd[] = "(a.`key` = '_created' AND a.`_item_id` {$_part[1]} {$_part[2]})";
                            }
                        }
                        elseif ($_part[1] == 'LIKE') {
                            $_bd[] = "(a.`key` LIKE '{$_part[0]}' AND a.`value` {$_part[1]} {$_part[2]})";
                        }
                        else {
                            if ($_part[1] == 'between') {
                                $_bd[] = "(a.`key` = '{$_part[0]}' AND a.`value` BETWEEN {$_part[2]} AND {$_part[3]})\n";
                            }
                            elseif ($_part[1] == 'not_between') {
                                $_bd[] = "(a.`key` = '{$_part[0]}' AND a.`value` NOT BETWEEN {$_part[2]} AND {$_part[3]})\n";
                            }
                            else {
                                if (isset($_di[$k])) {
                                    $_bd[] = "(a.`value` {$_part[1]} {$_part[2]})";
                                }
                                else {
                                    $_bd[] = "(a.`key` = '{$_part[0]}' AND a.`value` {$_part[1]} {$_part[2]})";
                                }
                            }
                        }
                    }
                    $req .= implode(' OR ', $_bd) . ")\n";
                }
                elseif ($v[0] == "{$pfx}_visible") {
                    $req .= "a.`key` = '{$v[0]}' AND (a.`value` IS NULL OR a.`value` != 'off')\n";
                }
                // wildcard (all keys)
                elseif ($v[0] == '%') {
                    if (isset($v[3]) && $v[3] == 'no_quotes') {
                        $req .= "a.`value` {$v[1]} {$v[2]}\n";
                    }
                    else {
                        if ($v[1] == 'between') {
                            list($v1, $v2) = explode(',', $v[2]);
                            $req .= "a.`value` BETWEEN {$v1} AND {$v2}\n";
                        }
                        elseif ($v[1] == 'not_between') {
                            list($v1, $v2) = explode(',', $v[2]);
                            $req .= "a.`value` NOT BETWEEN {$v1} AND {$v2}\n";
                        }
                        else {
                            $req .= "a.`value` {$v[1]} '{$v[2]}'\n";
                        }
                    }
                }
                // wildcard match on key
                elseif (strpos(' ' . $v[0], '%')) {
                    if (isset($v[3]) && $v[3] == 'no_quotes') {
                        $req .= "a.`key` LIKE '{$v[0]}' AND a.`value` {$v[1]} {$v[2]}\n";
                    }
                    else {
                        if ($v[1] == 'between') {
                            list($v1, $v2) = explode(',', $v[2]);
                            $req .= "a.`key` LIKE '{$v[0]}' AND a.`value` BETWEEN {$v1} AND {$v2}\n";
                        }
                        elseif ($v[1] == 'not_between') {
                            list($v1, $v2) = explode(',', $v[2]);
                            $req .= "a.`key` LIKE '{$v[0]}' AND a.`value` NOT BETWEEN {$v1} AND {$v2}\n";
                        }
                        else {
                            $req .= "a.`key` LIKE '{$v[0]}' AND a.`value` {$v[1]} '{$v[2]}'\n";
                        }
                    }
                }
                // IN / NOT IN (no quotes) or NULL
                elseif ($v[2] == 'NULL' || (isset($v[3]) && $v[3] == 'no_quotes')) {
                    if (isset($_di[$k])) {
                        $req .= "a.`value` {$v[1]} {$v[2]}\n";
                    }
                    else {
                        $req .= "a.`key` = '{$v[0]}' AND a.`value` {$v[1]} {$v[2]}\n";
                    }
                }
                else {
                    if ($v[1] == 'between') {
                        list($v1, $v2) = explode(',', $v[2]);
                        if (isset($_di[$k])) {
                            $req .= "a.`value` BETWEEN {$v1} AND {$v2}\n";
                        }
                        else {
                            $req .= "a.`key` = '{$v[0]}' AND a.`value` BETWEEN {$v1} AND {$v2}\n";
                        }
                    }
                    elseif ($v[1] == 'not_between') {
                        list($v1, $v2) = explode(',', $v[2]);
                        if (isset($_di[$k])) {
                            $req .= "a.`value` NOT BETWEEN {$v1} AND {$v2}\n";
                        }
                        else {
                            $req .= "a.`key` = '{$v[0]}' AND a.`value` NOT BETWEEN {$v1} AND {$v2}\n";
                        }
                    }
                    else {
                        if (isset($_di[$k])) {
                            $req .= "a.`value` {$v[1]} '{$v[2]}'\n";
                        }
                        else {
                            $req .= "a.`key` = '{$v[0]}' AND a.`value` {$v[1]} '{$v[2]}'\n";
                        }
                    }
                }
            }

            // keys beyond the first key...
            elseif ($v[1] !== 'CUSTOM') {
                // If we are searching by _item_id we always use "a" for our prefix
                if ($v[0] == '_item_id') {
                    if ($v[2] == 'NULL' || (isset($v[3]) && $v[3] == 'no_quotes')) {
                        $req .= "AND a.`_item_id` {$v[1]} {$v[2]}\n";
                    }
                    else {
                        if ($v[1] == 'between') {
                            list($v1, $v2) = explode(',', $v[2]);
                            $req .= "AND a.`_item_id` BETWEEN {$v1} AND {$v2}\n";
                        }
                        elseif ($v[1] == 'not_between') {
                            list($v1, $v2) = explode(',', $v[2]);
                            $req .= "AND a.`_item_id` NOT BETWEEN {$v1} AND {$v2}\n";
                        }
                        else {
                            $req .= "AND a.`_item_id` {$v[1]} '{$v[2]}'\n";
                        }
                    }
                }
                else {
                    $als = $_ob["{$v[0]}"];
                    // Special is or is not condition
                    // (e.`value` IS NOT NULL OR e.`value` IS NULL)
                    // This allows an ORDER_BY on a column that may not be set in all DS entries
                    if ($v[1] == 'IS OR IS NOT') {
                        $req .= "AND ({$als}.`value` > '' OR {$als}.`value` IS NULL)\n";
                    }
                    elseif (isset($v[3]) && $v[3] == 'parens' && isset($_so["{$v[0]}"])) {
                        $_bd = array();
                        // ((a.key = 'something' AND value = 1) OR (a.key = 'other' AND value = 2))
                        $req .= 'AND (';
                        foreach ($_so["{$v[0]}"] as $_part) {
                            if ($_part[0] == '_item_id') {
                                $_bd[] = "(a.`_item_id` {$_part[1]} {$_part[2]})";
                            }
                            elseif ($_part[1] == 'LIKE') {
                                $_bd[] = "({$als}.`key` LIKE '{$_part[0]}' AND {$als}.`value` {$_part[1]} {$_part[2]})";
                            }
                            else {
                                if ($_part[1] == 'between') {
                                    $_bd[] = "({$als}.`key` = '{$_part[0]}' AND {$als}.`value` BETWEEN {$_part[2]} AND {$_part[3]})\n";
                                }
                                elseif ($_part[1] == 'not_between') {
                                    $_bd[] = "({$als}.`key` = '{$_part[0]}' AND {$als}.`value` NOT BETWEEN {$_part[2]} AND {$_part[3]})\n";
                                }
                                else {
                                    $_bd[] = "({$als}.`key` = '{$_part[0]}' AND {$als}.`value` {$_part[1]} {$_part[2]})";
                                }
                            }
                        }
                        $req .= implode(' OR ', $_bd) . ")\n";
                    }
                    // wildcard (all keys)
                    elseif ($v[0] == '%') {
                        if ($v[1] == 'between') {
                            list($v1, $v2) = explode(',', $v[2]);
                            $req .= "AND {$als}.`value` BETWEEN {$v1} AND {$v2}\n";
                        }
                        elseif ($v[1] == 'not_between') {
                            list($v1, $v2) = explode(',', $v[2]);
                            $req .= "AND {$als}.`value` NOT BETWEEN {$v1} AND {$v2}\n";
                        }
                        else {
                            $req .= "AND {$als}.`value` {$v[1]} '{$v[2]}'\n";
                        }
                    }
                    // wildcard match on key
                    elseif (strpos(' ' . $v[0], '%')) {
                        if ($v[1] == 'between') {
                            list($v1, $v2) = explode(',', $v[2]);
                            $req .= "AND {$als}.`key` LIKE '{$v[0]}' AND {$als}.`value` BETWEEN {$v1} AND {$v2}\n";
                        }
                        elseif ($v[1] == 'not_between') {
                            list($v1, $v2) = explode(',', $v[2]);
                            $req .= "AND {$als}.`key` LIKE '{$v[0]}' AND {$als}.`value` NOT BETWEEN {$v1} AND {$v2}\n";
                        }
                        else {
                            $req .= "AND {$als}.`key` LIKE '{$v[0]}' AND {$als}.`value` {$v[1]} '{$v[2]}'\n";
                        }
                    }
                    elseif ($v[2] == 'NULL' || (isset($v[3]) && $v[3] == 'no_quotes')) {
                        if (strpos($v[2], '(SELECT ') !== 0) {
                            $req .= "AND {$als}.`value` {$v[1]} {$v[2]}\n";
                        }
                    }
                    else {
                        if ($v[1] == 'between') {
                            list($v1, $v2) = explode(',', $v[2]);
                            $req .= "AND {$als}.`value` BETWEEN {$v1} AND {$v2}\n";
                        }
                        elseif ($v[1] == 'not_between') {
                            list($v1, $v2) = explode(',', $v[2]);
                            $req .= "AND {$als}.`value` NOT BETWEEN {$v1} AND {$v2}\n";
                        }
                        else {
                            $req .= "AND {$als}.`value` {$v[1]} '{$v[2]}'\n";
                        }
                    }
                }
            }
        }

        // Bring in privacy additions if set...
        if (isset($add{1})) {
            $req .= $add;
        }

        // Bring in profile_id search
        if (strlen($prf) > 0) {
            $req .= $prf;
        }

        // For our counting query
        $re2 = $req;

        // Group by
        if (isset($group_by) && strlen($group_by) > 0) {
            $req .= $group_by . ' ';
            $re2 .= $group_by . ' ';
        }

        elseif (!strpos($req, 'RAND()')) {
            // Default - group by item_id
            if (!$sgb && $ino && $ino == '_item_id') {
                if (isset($_params['pagebreak'])) {
                    $req .= "GROUP BY a._item_id ";
                }
                $re2 .= "GROUP BY a._item_id ";
            }
        }

        // Some items are not needed in our counting query
        if (!isset($_params['return_count']) || $_params['return_count'] === false) {

            // Order by
            if (isset($_params['order_by']) && is_array($_params['order_by']) && count($_params['order_by']) > 0) {
                $_ov = array();
                $oby = "ORDER BY ";

                foreach ($_params['order_by'] as $k => $v) {
                    $v = strtoupper($v);
                    switch ($v) {

                        case 'RAND':
                        case 'RANDOM':
                            if (isset($_params['limit']) && intval($_params['limit']) === 1) {
                                $req .= "AND a.`_item_id` >= FLOOR(1 + @rnd * (SELECT MAX(_item_id) FROM " . jrCore_db_table_name($module, 'item') . ")) ";
                                $oby = false;
                            }
                            else {
                                $_ov[] = 'RAND()';
                            }
                            // With random ordering we ignore all other orders...
                            continue 2;
                            break;

                        case 'ASC':
                        case 'DESC':
                            if (!isset($_ob[$k]) && $k != '_item_id') {
                                if (isset($_params['fdebug']) && $_params['fdebug'] == true) {
                                    fdebug(array('jrCore_db_search_items error' => "you must include the {$k} field in your search criteria to order_by it", '_params' => $_params, '_backup' => $_backup)); // OK
                                }
                                jrCore_logger('MAJ', 'invalid order_by criteria in jrCore_db_search_items parameters', array("error: you must include the {$k} field in your search criteria to order_by it", $module, $_params, $_backup));
                                return false;
                            }
                            // If we are ordering by _item_id, we do not order by value...
                            if (count($custom_order_by) > 0 && $k != '_item_id' && $k != '_created') {
                                // Check for index tables
                                $fld = true;
                                if (count($_di) > 0) {
                                    foreach ($_di as $itk => $itv) {
                                        if ($itv == $k) {
                                            $fld = false;
                                            break;
                                        }
                                    }
                                }
                                if ($fld) {
                                    if ($v == 'ASC') {
                                        $_ov[] = "FIELD(" . $_ob[$k] . ".`key`, '_created', '{$k}') ASC, " . $_ob[$k] . ".`value` {$v}";
                                    }
                                    else {
                                        $_ov[] = "FIELD(" . $_ob[$k] . ".`key`, '{$k}', '_created') ASC, " . $_ob[$k] . ".`value` {$v}";
                                    }
                                }
                                else {
                                    $_ov[] = " " . $_ob[$k] . ".`value` {$v}";
                                }
                            }
                            elseif ($k == '_item_id') {
                                $_ov[] = "a.`_item_id` {$v}";
                            }
                            else {
                                switch ($k) {
                                    case '_user_id':
                                    case '_profile_id':
                                        $_ov[] = "a.`_item_id` {$v}";
                                        break;
                                    case '_created':
                                        if (isset($_conf['jrCore_optimized_order']) && $_conf['jrCore_optimized_order'] == 'on') {
                                            $_ov[] = "a.`_item_id` {$v}";
                                        }
                                        else {
                                            $_ov[] = '(' . $_ob[$k] . ".`value` + 0) {$v}";
                                        }
                                        break;
                                    case '_updated':
                                        $_ov[] = '(' . $_ob[$k] . ".`value` + 0) {$v}";
                                        break;
                                    default:
                                        $_ov[] = $_ob[$k] . ".`value` {$v}";
                                        break;
                                }
                            }
                            break;

                        case 'NUMERICAL_ASC':
                            if (!isset($_ob[$k]) && $k != '_item_id') {
                                if (isset($_params['fdebug']) && $_params['fdebug'] == true) {
                                    fdebug(array('jrCore_db_search_items error' => "you must include the {$k} field in your search criteria to order_by it (2)", '_params' => $_params, '_backup' => $_backup)); // OK
                                }
                                jrCore_logger('MAJ', 'invalid order_by criteria in jrCore_db_search_items parameters', array("error: you must include the {$k} field in your search criteria to order_by it", $module, $_params, $_backup));
                                return false;
                            }
                            if (count($custom_order_by) > 0 && $k != '_item_id' && $k != '_created') {
                                // Check for index tables
                                $fld = true;
                                if (count($_di) > 0) {
                                    foreach ($_di as $itk => $itv) {
                                        if ($itv == $k) {
                                            $fld = false;
                                            break;
                                        }
                                    }
                                }
                                if ($fld) {
                                    $_ov[] = "FIELD(" . $_ob[$k] . ".`key`, '_created', '{$k}') ASC, (" . $_ob[$k] . ".`value` + 0) ASC";
                                }
                                else {
                                    $_ov[] = "(" . $_ob[$k] . ".`value` + 0) ASC";
                                }
                            }
                            else {
                                switch ($k) {
                                    case '_item_id':
                                    case '_user_id':
                                    case '_profile_id':
                                        $_ov[] = "a.`_item_id` ASC";
                                        break;
                                    case '_created':
                                        if (isset($_conf['jrCore_optimized_order']) && $_conf['jrCore_optimized_order'] == 'on') {
                                            $_ov[] = "a.`_item_id` ASC";
                                        }
                                        else {
                                            $_ov[] = '(' . $_ob[$k] . ".`value` + 0) ASC";
                                        }
                                        break;
                                    default:
                                        $_ov[] = '(' . $_ob[$k] . ".`value` + 0) ASC";
                                        break;
                                }
                            }
                            break;

                        case 'NUMERICAL_DESC':
                            if (!isset($_ob[$k]) && $k != '_item_id') {
                                if (isset($_params['fdebug']) && $_params['fdebug'] == true) {
                                    fdebug(array('jrCore_db_search_items error' => "invalid order_by criteria", '_params' => $_params, '_backup' => $_backup)); // OK
                                }
                                jrCore_logger('MAJ', 'invalid order_by criteria in jrCore_db_search_items parameters', array("error: you must include the {$k} field in your search criteria to order_by it", $module, $_params, $_backup));
                                return false;
                            }
                            if (count($custom_order_by) > 0 && $k != '_item_id' && $k != '_created') {
                                // Check for index tables
                                $fld = true;
                                if (count($_di) > 0) {
                                    foreach ($_di as $itk => $itv) {
                                        if ($itv == $k) {
                                            $fld = false;
                                            break;
                                        }
                                    }
                                }
                                if ($fld) {
                                    $_ov[] = "FIELD(" . $_ob[$k] . ".`key`, '{$k}', '_created') ASC, (" . $_ob[$k] . ".`value` + 0) DESC";
                                }
                                else {
                                    $_ov[] = "(" . $_ob[$k] . ".`value` + 0) DESC";
                                }
                            }
                            else {
                                switch ($k) {
                                    case '_item_id':
                                    case '_user_id':
                                    case '_profile_id':
                                        $_ov[] = "a.`_item_id` DESC";
                                        break;
                                    case '_created':
                                        if (isset($_conf['jrCore_optimized_order']) && $_conf['jrCore_optimized_order'] == 'on') {
                                            $_ov[] = "a.`_item_id` DESC";
                                        }
                                        else {
                                            $_ov[] = '(' . $_ob[$k] . ".`value` + 0) DESC";
                                        }
                                        break;
                                    default:
                                        $_ov[] = '(' . $_ob[$k] . ".`value` + 0) DESC";
                                        break;
                                }
                            }
                            break;

                        default:

                            if (isset($_params['fdebug']) && $_params['fdebug'] == true) {
                                fdebug(array('jrCore_db_search_items error' => "invalid order direction: {$v} received for {$k} - must be one of: ASC, DESC, NUMERICAL_ASC, NUMERICAL_DESC, RANDOM", '_params' => $_params, '_backup' => $_backup)); // OK
                            }
                            jrCore_logger('MAJ', 'invalid order_by criteria in jrCore_db_search_items parameters', array("invalid order direction: {$v} received for {$k} - must be one of: ASC, DESC, NUMERICAL_ASC, NUMERICAL_DESC, RANDOM", $module, $_params, $_backup));
                            return false;
                            break;
                    }
                }
                if (isset($oby) && strlen($oby) > 0) {
                    $req .= $oby . implode(', ', $_ov) . ' ';
                }
            }

            // If we get a LIST of items, we (by default) order by that list unless we get a different order by
            elseif ($ino && isset($_do)) {
                // No need to order if we're only getting 1 result from the DS
                if (!isset($_params['limit']) || $_params['limit'] > 1) {
                    $field = false;
                    if ($ino == '_item_id' || $ino == '_profile_id' || $ino == '_user_id') {
                        $field = "a.`_item_id`";
                    }
                    elseif (isset($_ob[$ino])) {
                        $field = $_ob[$ino] . ".`value`";
                    }
                    if ($field) {
                        if (isset($_params['limit'])) {
                            $req .= "ORDER BY FIELD({$field}," . implode(',', array_slice($_do, 0, $_params['limit'], true)) . ",a.`_item_id`) ";
                        }
                        else {
                            $req .= "ORDER BY FIELD({$field}," . implode(',', $_do) . ") ";
                        }
                    }
                }
                unset($_do);
            }
        }

        // Start our result set.  When doing a search an array with 2 keys is returned:
        // "_items" - contains the actual search results numerically indexed
        // "info" - contains meta information about the result set
        $_rs = array(
            '_items' => false,
            'info'   => array()
        );

        if (isset($_conf['jrCore_pager_limit']) && $_conf['jrCore_pager_limit'] == 'on' && isset($_params['pagebreak']) && jrCore_checktype($_params['pagebreak'], 'number_nz')) {
            $nr = jrCore_db_number_rows($module, 'item');
            $mr = (isset($_params['simplepagebreak_cutoff'])) ? (int) $_params['simplepagebreak_cutoff'] : (isset($_conf['jrCore_simplepagebreak_cutoff'])) ? (int) $_conf['jrCore_simplepagebreak_cutoff'] : 50000;
            if ($nr > $mr) {
                $_params['simplepagebreak'] = (int) $_params['pagebreak'];
                unset($_params['pagebreak']);
            }
        }

        // Limit
        $slow_count = false;
        if (isset($_params['limit']) && !isset($_params['pagebreak']) && !isset($_params['simplepagebreak'])) {
            if (!jrCore_checktype($_params['limit'], 'number_nz')) {
                return "error: invalid limit value - must be a number greater than 0";
            }
            $req                  .= "\nLIMIT " . intval($_params['limit']) . ' ';
            $_rs['info']['limit'] = intval($_params['limit']);
        }

        // Simple Pagebreak - no COUNT
        elseif ((!isset($_params['return_count']) || $_params['return_count'] === false) && isset($_params['simplepagebreak']) && jrCore_checktype($_params['simplepagebreak'], 'number_nz')) {

            // Check for good page num
            if (!isset($_params['page']) || !jrCore_checktype($_params['page'], 'number_nz')) {
                $_params['page'] = 1;
            }
            $req                            .= "\nLIMIT " . intval(($_params['page'] - 1) * $_params['simplepagebreak']) . ",{$_params['simplepagebreak']}";
            $_rs['info']['next_page']       = intval($_params['page'] + 1);
            $_rs['info']['pagebreak']       = (int) $_params['simplepagebreak'];
            $_rs['info']['simplepagebreak'] = (int) $_params['simplepagebreak'];
            $_rs['info']['page']            = (int) $_params['page'];
            $_rs['info']['this_page']       = (int) $_params['page'];
            $_rs['info']['prev_page']       = ($_params['page'] > 1) ? intval($_params['page'] - 1) : 0;
            $_rs['info']['page_base_url']   = htmlspecialchars(jrCore_strip_url_params(jrCore_get_current_url(), array('p')));
            if (isset($_params['use_total_row_count']) && $_params['use_total_row_count'] > 0) {
                $_rs['info']['total_items'] = (int) $_params['use_total_row_count'];
            }
        }

        // Pagebreak
        elseif ((!isset($_params['return_count']) || $_params['return_count'] === false) && isset($_params['pagebreak']) && jrCore_checktype($_params['pagebreak'], 'number_nz')) {

            // Check for good page num
            if (!isset($_params['page']) || !jrCore_checktype($_params['page'], 'number_nz')) {
                $_params['page'] = 1;
            }

            // We can be told to use the TOTAL ROW COUNT of the entire table OR passed a number
            if (isset($_params['use_total_row_count']) && strlen($_params['use_total_row_count']) > 0) {
                if (is_numeric($_params['use_total_row_count'])) {
                    $_ct = array(
                        'tc' => (int) $_params['use_total_row_count']
                    );
                }
                else {
                    $_ct = array(
                        'tc' => jrCore_db_get_datastore_item_count($module)
                    );
                }
            }
            else {

                if (is_array($_so)) {
                    $re2 = str_replace('SELECT DISTINCT(a.`_item_id`) AS _item_id', 'SELECT COUNT(DISTINCT(a.`_item_id`)) AS tc', $re2);
                }
                else {
                    $re2 = str_replace('SELECT a.`_item_id` AS _item_id', 'SELECT COUNT(a.`_item_id`) AS tc', $re2);
                }

                $beg = explode(' ', microtime());
                $beg = $beg[1] + $beg[0];

                if (strpos($req, 'GROUP BY')) {
                    $_ct = array(
                        'tc' => jrCore_db_query($re2, 'NUM_ROWS', false, null, false)
                    );
                }
                else {
                    $_ct = jrCore_db_query($re2, 'SINGLE', false, null, false);
                }

                $end = explode(' ', microtime());
                $end = $end[1] + $end[0];
                $end = round(($end - $beg), 3);

                // Query Trigger
                $_qp = array(
                    'query'      => $re2,
                    'query_time' => $end,
                    'count'      => (isset($_ct['tc'])) ? intval($_ct['tc']) : 0,
                    'cache_key'  => $cky
                );
                jrCore_trigger_event('jrCore', 'db_search_count_query', $_params, $_qp);

                if (isset($_params['slow_query_time']) && $_params['slow_query_time'] > 0 && $end >= $_params['slow_query_time']) {
                    $slow_count = $end;
                }
                elseif ($end > 0.24 && isset($_conf['jrDeveloper_slow_queries']) && $_conf['jrDeveloper_slow_queries'] > 0 && $end > $_conf['jrDeveloper_slow_queries']) {
                    $slow_count = $end;
                }

            }

            if (is_array($_ct) && isset($_ct['tc']) && $_ct['tc'] > 0) {

                // Check if we also have a limit - this is going to limit the total
                // result set to a specific size, but still allow pagination
                if (isset($_params['limit'])) {
                    // We need to see WHERE we are in the requested set
                    $_rs['info']['total_items'] = (isset($_ct['tc']) && jrCore_checktype($_ct['tc'], 'number_nz')) ? intval($_ct['tc']) : 0;
                    if ($_rs['info']['total_items'] > $_params['limit']) {
                        $_rs['info']['total_items'] = $_params['limit'];
                    }
                    // Find out how many we are returning on this query...
                    $pnum = $_params['pagebreak'];
                    if (($_params['page'] * $_params['pagebreak']) > $_params['limit']) {
                        $pnum = (int) ($_params['limit'] % $_params['pagebreak']);
                        // See if the request range is completely outside the last page
                        if ($_params['pagebreak'] < $_params['limit'] && $_params['page'] > ceil($_params['limit'] / $_params['pagebreak'])) {
                            // invalid set
                            // Check for fdebug logging
                            if (isset($_params['fdebug']) && $_params['fdebug'] == true) {
                                fdebug(array('jrCore_db_search_items error' => "no items returned from COUNT query", '_params' => $_params, '_backup' => $_backup, 're2' => $re2)); // OK
                            }
                            return false;
                        }
                    }
                    $req .= "\nLIMIT " . intval(($_params['page'] - 1) * $_params['pagebreak']) . ",{$pnum}";
                }
                else {
                    $_rs['info']['total_items'] = (isset($_ct['tc']) && jrCore_checktype($_ct['tc'], 'number_nz')) ? intval($_ct['tc']) : 0;
                    $req                        .= "\nLIMIT " . intval(($_params['page'] - 1) * $_params['pagebreak']) . ",{$_params['pagebreak']}";
                }
                $_rs['info']['total_pages']   = (int) ceil($_rs['info']['total_items'] / $_params['pagebreak']);
                $_rs['info']['next_page']     = ($_rs['info']['total_pages'] > $_params['page']) ? intval($_params['page'] + 1) : 0;
                $_rs['info']['pagebreak']     = (int) $_params['pagebreak'];
                $_rs['info']['page']          = (int) $_params['page'];
                $_rs['info']['this_page']     = $_params['page'];
                $_rs['info']['prev_page']     = ($_params['page'] > 1) ? intval($_params['page'] - 1) : 0;
                $_rs['info']['page_base_url'] = htmlspecialchars(jrCore_strip_url_params(jrCore_get_current_url(), array('p')));
            }
            else {
                // No items
                // Check for fdebug logging
                if (isset($_params['fdebug']) && $_params['fdebug'] == true) {
                    fdebug(array('jrCore_db_search_items error' => "no items returned from COUNT query (2)", '_params' => $_params, '_backup' => $_backup, 're2' => $re2)); // OK
                }
                return false;
            }
        }
        else {
            // Default limit of 10
            $req .= "\nLIMIT 10";
        }

        $beg = explode(' ', microtime());
        $beg = $beg[1] + $beg[0];

        $_rt = jrCore_db_query($req, 'NUMERIC', false, null, false);

        $end = explode(' ', microtime());
        $end = $end[1] + $end[0];
        $end = round(($end - $beg), 3);

        // Check for fdebug logging
        if (isset($_params['fdebug']) && $_params['fdebug'] == true) {
            if (isset($_ct)) {
                fdebug($_backup, $_params, "(PAGINATION QUERY): {$re2}", $req, "Query Time: {$end}", $_rs, $_rt); // OK
            }
            else {
                fdebug($_backup, $_params, $req, "Query Time: {$end}", $_rs, $_rt); // OK
            }
        }

        // Slow Query logging
        $slow_time = 0;
        if (isset($_params['slow_query_time']) && $_params['slow_query_time'] > 0) {
            $slow_time = $_params['slow_query_time'];
        }
        elseif (isset($_conf['jrDeveloper_slow_queries']) && $_conf['jrDeveloper_slow_queries'] > 0) {
            $slow_time = $_conf['jrDeveloper_slow_queries'];
        }
        if ($slow_count || ($slow_time > 0 && $end >= $slow_time)) {
            global $_post;
            $_rq = array(
                'process'    => (jrCore_client_is_detached()) ? 'worker (background)' : 'client',
                '_post'      => $_post,
                '_params'    => $_params,
                'query_time' => $end,
                'query'      => $req
            );
            $tag = '';
            if ($slow_count && isset($_ct)) {
                $_rq['pagination_query_time'] = $slow_count;
                $_rq['pagination_query']      = $re2;
                $tag                          = ' pagination';
            }
            // Show whichever is longer
            $pri = (jrCore_client_is_detached()) ? 'MIN' : 'MAJ';
            $tim = ($slow_count && $slow_count > $end) ? $slow_count : $end;
            jrCore_logger($pri, "slow jrCore_db_search_items{$tag} query: {$tim} seconds", $_rq);
        }

        // Query Trigger
        $_qp = array(
            'query'      => $req,
            'query_time' => $end,
            'results'    => $_rt,
            'cache_key'  => $cky
        );
        jrCore_trigger_event('jrCore', 'db_search_items_query', $_params, $_qp);

    }
    else {
        $_rt = $_params['result_set'];
        if (isset($_params['fdebug']) && $_params['fdebug'] == true) {
            fdebug(array('jrCore_db_search_items error' => "result_set was provided by event listener - no query was run", '_params' => $_params, '_backup' => $_backup)); // OK
        }
    }

    if ($_rt && is_array($_rt)) {

        // See if we are only providing a count...
        // No need for triggers here
        if (isset($_params['return_count']) && $_params['return_count'] !== false) {
            return count($_rt);
        }

        $_id = array();
        foreach ($_rt as $v) {
            $_id[] = $v['_item_id'];
        }

        // We can ask to just get the item_id's for our own use.
        // No need for triggers here
        if (isset($_params['return_item_id_only']) && $_params['return_item_id_only'] === true) {
            return $_id;
        }

        $_ky = null;
        if (isset($_params['return_keys']) && is_array($_params['return_keys']) && count($_params['return_keys']) > 0) {
            $_params['return_keys'][] = '_item_id';    // Always include _item_id
            $_params['return_keys'][] = '_user_id';    // We must include _user_id or jrUser search items trigger does not know the user to include
            $_params['return_keys'][] = '_profile_id'; // We must include _profile_id or jrProfile search items trigger does not know the profile to include
            $_ky                      = $_params['return_keys'];
        }
        $_rs['_items'] = jrCore_db_get_multiple_items($module, $_id, $_ky, true);
        if ($_rs['_items'] && is_array($_rs['_items'])) {

            // Add in some meta data
            if (!isset($_rs['info']['total_items'])) {
                $_rs['info']['total_items'] = count($_rs['_items']);
            }

            // If we are using the SIMPLE pagebreak setup, if we have LESS
            // items than our pagebreak, we have NO next page
            if (isset($_params['simplepagebreak']) && $_params['simplepagebreak'] > $_rs['info']['total_items']) {
                $_rs['info']['next_page'] = 0;
            }

            // Trigger search event
            if (!isset($_params['skip_triggers']) || $_params['skip_triggers'] === false) {
                $_params['cache_key'] = $cky;
                $_rs                  = jrCore_trigger_event('jrCore', 'db_search_items', $_rs, $_params);
            }

            $_ci = array();
            foreach ($_rs['_items'] as $v) {
                if (isset($v['_profile_id'])) {
                    $_ci["{$v['_profile_id']}"] = $v['_profile_id'];
                }
            }
            $pid = 0;
            if (count($_ci) === 1) {
                $pid = reset($_ci);
            }
            jrCore_set_flag('datastore_cache_profile_ids', $_ci);
            unset($_ci);

            // Check for return keys
            if ($_ky) {
                $_ky = array_flip($_ky);
                foreach ($_rs['_items'] as $k => $v) {
                    foreach ($v as $ky => $kv) {
                        if (!isset($_ky[$ky])) {
                            unset($_rs['_items'][$k][$ky]);
                        }
                    }
                }
            }
            $_rs['_params']               = $_backup;
            $_rs['_params']['module']     = $module;
            $_rs['_params']['module_url'] = jrCore_get_module_url($module);
            if (!isset($_params['cache_seconds'])) {
                jrCore_add_to_cache($module, $cky, $_rs, 0, $pid);
            }
            elseif (jrCore_checktype($_params['cache_seconds'], 'number_nz')) {
                jrCore_add_to_cache($module, $cky, $_rs, $_params['cache_seconds'], $pid);
            }

            if (!isset($_params['skip_triggers']) || $_params['skip_triggers'] === false) {
                $_rs = jrCore_trigger_event('jrCore', 'db_search_results', $_rs, $_params);
            }
            unset($_params);
            return $_rs;
        }
    }
    if (!isset($_params['skip_triggers']) || $_params['skip_triggers'] === false) {
        jrCore_trigger_event('jrCore', 'db_search_results', array('no_results' => true), $_params);
    }
    return false;
}

/**
 * Check if a given search operator is valid
 * @param $search string Search Condition
 * @return array
 */
function jrCore_db_check_for_supported_operator($search)
{
    $cd = false;
    @list($key, $opt, $val) = explode(' ', trim($search), 3);
    switch (jrCore_str_to_lower($opt)) {
        case '>':
        case '>=':
        case '<':
        case '<=':
            if (strpos($val, '.')) {
                $cd = array($key, $opt, floatval($val));
            }
            else {
                $cd = array($key, $opt, intval($val));
            }
            break;
        case '!=':
        case '=':
        case 'like':
        case 'regexp':
            $cd = array($key, $opt, "'" . jrCore_db_escape($val) . "'");
            break;
        case 'not_like':
            $cd = array($key, 'not like', "'" . jrCore_db_escape($val) . "'");
            break;
        case 'in':
            $_vl = array();
            foreach (explode(',', $val) as $iv) {
                if (ctype_digit($iv)) {
                    $_vl[] = (int) $iv;
                }
                else {
                    $_vl[] = "'" . jrCore_db_escape($iv) . "'";
                }
            }
            $val = "(" . implode(',', $_vl) . ") ";
            $cd  = array($key, 'IN', $val);
            break;
        case 'not_in':
            $_vl = array();
            foreach (explode(',', $val) as $iv) {
                if (ctype_digit($iv)) {
                    $_vl[] = (int) $iv;
                }
                else {
                    $_vl[] = "'" . jrCore_db_escape($iv) . "'";
                }
            }
            $val = "(" . implode(',', $_vl) . ") ";
            $cd  = array($key, 'NOT IN', $val);
            break;
        case 'between':
        case 'not_between':
            if (strpos($val, ',')) {
                list($vl1, $vl2) = explode(',', $val);
                $vl1 = trim($vl1);
                $vl2 = trim($vl2);
                if (is_numeric($vl1) && is_numeric($vl2)) {
                    if (strpos(' ' . $vl1, '.')) {
                        $vl1 = floatval($vl1);
                    }
                    else {
                        $vl1 = intval($vl1);
                    }
                    if (strpos(' ' . $vl2, '.')) {
                        $vl2 = floatval($vl2);
                    }
                    else {
                        $vl2 = intval($vl2);
                    }
                    if ($vl2 < $vl1) {
                        $cd = array($key, jrCore_str_to_lower($opt), $vl2, $vl1);
                    }
                    else {
                        $cd = array($key, jrCore_str_to_lower($opt), $vl1, $vl2);
                    }
                }
            }
            break;
    }
    return $cd;
}

/**
 * Return TRUE if key is a key that is found on all items in the DS
 * @param string $key
 * @return bool
 */
function jrCore_db_key_found_on_all_items($key)
{
    switch ($key) {
        case '_item_id':
        case '_user_id':
        case '_profile_id':
        case '_created':
        case '_updated':
            return true;
            break;
        default:
            if (strpos($key, '_title')) {
                return true;
            }
            if (strpos($key, '_count')) {
                return true;
            }
            if (strpos($key, '_order')) {
                return true;
            }
            if (strpos($key, '_pending')) {
                return true;
            }
            break;
    }
    return false;
}

/**
 * Some key searches do NOT need an index offset check
 * @param $key string DS key name
 * @return bool
 */
function jrCore_is_ds_index_needed($key)
{
    switch ($key) {
        case 'user_active':
        case 'user_birthdate':
        case 'user_email':
        case 'user_group':
        case 'user_last_login':
        case 'user_language':
        case 'user_name':
        case 'user_validate':
        case 'user_validated':
        case 'profile_active':
        case 'profile_disk_usage':
        case 'profile_location':
        case 'profile_name':
        case 'profile_private':
        case 'profile_quota_id':
        case 'profile_url':
            return false;
            break;
    }
    $key = ' ' . $key;
    if (strpos($key, 'category')) {
        return false;
    }
    elseif (strpos($key, '_image_')) {
        return false;
    }
    elseif (strpos($key, 'module')) {
        return false;
    }
    elseif (strpos($key, 'pending')) {
        return false;
    }
    elseif (strpos($key, 'notification')) {
        return false;
    }
    elseif (strpos($key, 'date')) {
        return false;
    }
    elseif (strpos($key, 'rating')) {
        return false;
    }
    elseif (strpos($key, 'type')) {
        return false;
    }
    elseif (strpos($key, 'profile_id')) {
        return false;
    }
    elseif (strpos($key, 'user_id')) {
        return false;
    }
    elseif (strpos($key, 'item_id')) {
        return false;
    }
    elseif (strpos($key, 'group_id')) {
        return false;
    }
    elseif (strpos($key, '_email')) {
        return false;
    }
    elseif (strpos($key, '_genre')) {
        return false;
    }
    elseif (strpos($key, '_album')) {
        return false;
    }
    elseif (strpos($key, '_active')) {
        return false;
    }
    elseif (strpos($key, '_file_size')) {
        return false;
    }
    elseif (strpos($key, 'latitude')) {
        return false;
    }
    elseif (strpos($key, 'longitude')) {
        return false;
    }
    elseif (strpos($key, 'ckey')) {
        return false;
    }
    elseif (strpos($key, 'pkey')) {
        return false;
    }
    elseif (strpos($key, '_approve')) {
        return false;
    }
    elseif (strpos($key, '_enabled')) {
        return false;
    }
    elseif (strlen($key) > 3) {
        if (strrpos($key, '_count', -6)) {
            return false;
        }
        elseif (strrpos($key, '_name', -5)) {
            return false;
        }
        elseif (strrpos($key, '_title', -6)) {
            return false;
        }
        elseif (strrpos($key, '_url', -4)) {
            return false;
        }
        elseif (strrpos($key, '_active', -7)) {
            return false;
        }
    }
    return true;
}

/**
 * Get Private Profiles
 * @return mixed|string
 */
function jrCore_db_get_private_profiles()
{
    // Get profiles that are NOT public and are allowed to change their profile privacy
    $key = 'db_get_private_profiles';
    if (!$_pp = jrCore_get_flag($key)) {
        if (!$_pp = jrCore_is_cached('jrCore', $key, false, false)) {
            $tbl = jrCore_db_table_name('jrProfile', 'item_key');
            $_pp = jrCore_db_query("SELECT `_item_id` AS i, `value` AS v FROM {$tbl} WHERE `key` = 'profile_private' AND `value` != '1'", 'i', false, 'v');
            if (!$_pp || !is_array($_pp)) {
                $_pp = 'no_results';
            }
            // We come out of this with an array of profile_id => profile_private mapping
            jrCore_add_to_cache('jrCore', $key, $_pp, 0, 0, false, false);
        }
        jrCore_set_flag($key, $_pp);
    }
    return $_pp;
}

/**
 * Take 2 arrays and return only values in first array that do not exist in the second
 * @param array $_include
 * @param array $_exclude
 * @return array
 */
function jrCore_create_combined_equal_array($_include, $_exclude)
{
    foreach ($_exclude as $i) {
        if (isset($_include[$i])) {
            unset($_include[$i]);
        }
    }
    return $_include;
}

/**
 * Delete the Private Profile cache
 * @return bool
 */
function jrCore_db_delete_private_profile_cache()
{
    $key = 'db_get_private_profiles';
    jrCore_delete_flag($key);
    jrCore_delete_cache('jrCore', $key, false, false);
    return true;
}

/**
 * Notify admin users of pending item (wrapper)
 * @param $module string Module
 * @param $_admins array Admin User_id's
 * @param $subject string
 * @param $message string
 * @return bool
 */
function jrCore_db_notify_admins_of_pending_item($module, $_admins, $subject, $message)
{
    $key = 'jrcore_db_pending_notified';
    if (!$_tm = jrCore_get_flag($key)) {
        $_tm = array();
    }
    if (isset($_tm[$module])) {
        // We already notified in this process for this module - not again
        return true;
    }
    foreach ($_admins as $uid) {
        if ($uid > 0) {
            jrUser_notify($uid, 0, 'jrCore', 'pending_item', $subject, $message);
        }
    }
    $_tm[$module] = 1;
    jrCore_set_flag($key, $_tm);
    return true;
}
