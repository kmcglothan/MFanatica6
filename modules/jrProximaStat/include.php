<?php
/**
 * Jamroom 5 Proxima Stats module
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
function jrProximaStat_meta()
{
    $_tmp = array(
        'name'        => 'Proxima Stats',
        'url'         => 'px_stat',
        'version'     => '1.2.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'System request and performance statistics',
        'category'    => 'proxima',
        'url_change'  => false,
        'requires'    => 'jrProximaCore:2.0.0,jrCore:6.0.0',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrProximaStat_init()
{
    // We provide a single stat key = "rt" (response time)
    jrCore_register_module_feature('jrProximaStat', 'stat_key', 'jrProximaStat', 'rt', 'Response Time');

    jrCore_register_event_listener('jrProximaCore', 'process_init', 'jrProximaStat_process_init_listener');
    jrCore_register_event_listener('jrCore', 'process_exit', 'jrProximaStat_process_exit_listener');
    jrCore_register_event_listener('jrCore', 'minute_maintenance', 'jrProximaStat_minute_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrProximaStat_verify_module_listener');

    jrCore_register_module_feature('jrCore', 'tool_view', 'jrProximaStat', 'view_graph', array('Stat Browser', 'View Statistical Graphs'));

    // Custom tabs
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrProximaStat', 'view_graph', 'Stat Browser');

    // Our default view for admins
    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrProximaStat', 'view_graph');

    jrCore_register_module_feature('jrCore', 'css', 'jrProximaStat', 'jrProximaStat.css');

    // Graph Support
    $_tmp = array(
        'title'    => 'API Response Time',
        'function' => 'jrProximaStat_graph_response_time'
    );
    jrCore_register_module_feature('jrGraph', 'graph_config', 'jrProximaStat', 'response_time', $_tmp);

    $_tmp = array(
        'title'    => 'API Access Counts',
        'function' => 'jrProximaStat_graph_request_count'
    );
    jrCore_register_module_feature('jrGraph', 'graph_config', 'jrProximaStat', 'request_count', $_tmp);
    return true;
}

/**
 * px_config
 */
function jrProximaStat_px_config()
{
    // Config items for ACL module
    return array(
        'prefix' => 'jrProximaStat'
    );
}

//------------------------------------
//  GRAPH CONFIG
//------------------------------------

/**
 * Request Count Graph plugin
 * @param $module string Module
 * @param $name string Name of Graph to create
 * @param $_args array Passed in Parameters
 * @return array
 */
function jrProximaStat_graph_request_count($module, $name, $_args)
{
    global $_mods;
    $_pr = array();
    $tag = '';
    if (isset($_args['mod'])) {
        $_pr['module'] = $_args['mod'];
        $tag           = " ({$_mods["{$_args['mod']}"]['module_name']})";
    }
    if (isset($_args['mtd'])) {
        $val = 0;
        switch (strtolower($_args['mtd'])) {
            case 'post':
                $val = 1;
                break;
            case 'get':
                $val = 2;
                break;
            case 'put':
                $val = 3;
                break;
            case 'delete':
                $val = 4;
                break;
        }
        if ($val > 0) {
            $_pr['method'] = $val;
            $tag .= ' (' . strtoupper($_args['mtd']) . ')';
        }
    }
    // Check for tick size
    if (!isset($_args['days']) || $_args['days'] == 1) {
        $_args['days'] = 1;
        $ts            = "[1, 'hour']";
        $fm            = '%m/%d %H:%M';
    }
    else {
        $ts = "[1, 'day']";
        $fm = '%m/%d';
    }
    unset($_args['module']);
    $_pr = array_merge($_args, $_pr);
    $_rt = jrProximaStat_get_stats('rt', $_pr);

    $_rs = array(
        '_sets' => array(
            0 => array(
                'label'       => "Number of API Requests{$tag}",
                'date_format' => $fm,
                'minTickSize' => $ts,
                'type'        => 'line',
                'pointRadius' => 3,
                '_data'       => array(),
            )
        )
    );

    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $v) {
            $yr = substr($v['stat_date'], 0, 4);
            $mn = substr($v['stat_date'], 4, 2);
            $dy = substr($v['stat_date'], 6, 2);
            $hr = 0;
            if (isset($_args['days']) && $_args['days'] == 1) {
                $hr = substr($v['stat_date'], 8, 2);
            }
            $tm = (string) gmmktime($hr, 0, 0, $mn, $dy, $yr) * 1000;
            if (!isset($_rs['_sets'][0]['_data']["{$tm}"])) {
                $_rs['_sets'][0]['_data']["{$tm}"] = 0;
            }
            $_rs['_sets'][0]['_data']["{$tm}"] += $v['stat_cnt'];
        }
    }
    return $_rs;
}

/**
 * Response Time Graph plugin
 * @param $module string Module
 * @param $name string Name of Graph to create
 * @param $_args array Passed in Parameters
 * @return array
 */
function jrProximaStat_graph_response_time($module, $name, $_args)
{
    global $_mods;
    $_pr = array();
    $tag = '';
    if (isset($_args['mod'])) {
        $_pr['module'] = $_args['mod'];
        $tag           = " ({$_mods["{$_args['mod']}"]['module_name']})";
    }
    if (isset($_args['mtd'])) {
        $val = 0;
        switch (strtolower($_args['mtd'])) {
            case 'post':
                $val = 1;
                break;
            case 'get':
                $val = 2;
                break;
            case 'put':
                $val = 3;
                break;
            case 'delete':
                $val = 4;
                break;
        }
        if ($val > 0) {
            $_pr['method'] = $val;
            $tag .= ' (' . strtoupper($_args['mtd']) . ')';
        }
    }
    // Check for tick size
    if (!isset($_args['days']) || $_args['days'] == 1) {
        $_args['days'] = 1;
        $ts            = "[1, 'hour']";
        $fm            = '%m/%d %H:%M';
    }
    else {
        $ts = "[1, 'day']";
        $fm = '%m/%d';
    }

    unset($_args['module']);
    $_pr = array_merge($_args, $_pr);

    $_rs = array(
        '_sets' => array(
            0 => array(
                'label'       => "Maximum Response Time (ms){$tag}",
                'date_format' => $fm,
                'xticks'      => 5,
                'minTickSize' => $ts,
                'type'        => 'line',
                'pointRadius' => 5,
                '_data'       => array(),
            ),
            1 => array(
                'label'       => "Average Response Time (ms){$tag}",
                'type'        => 'line',
                'pointRadius' => 4,
                '_data'       => array(),
            ),
            2 => array(
                'label'       => "Minimum Response Time (ms){$tag}",
                'type'        => 'line',
                'pointRadius' => 3,
                '_data'       => array(),
            ),
        )
    );

    $_rt = jrProximaStat_get_stats('rt', $_pr);
    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $v) {
            // [stat_date] => 2014051416
            // [stat_mtd] => 2
            // [stat_cnt] => 17130
            // [stat_min] => 0.000
            // [stat_avg] => 0.013
            // [stat_max] => 0.650
            $yr = substr($v['stat_date'], 0, 4);
            $mn = substr($v['stat_date'], 4, 2);
            $dy = substr($v['stat_date'], 6, 2);
            $hr = 0;
            if (isset($_args['days']) && $_args['days'] == 1) {
                $hr = substr($v['stat_date'], 8, 2);
            }
            $tm = (string) gmmktime($hr, 0, 0, $mn, $dy, $yr) * 1000;

            $v['stat_max'] *= 1000;
            $v['stat_avg'] *= 1000;
            $v['stat_min'] *= 1000;

            if (!isset($_rs['_sets'][0]['_data']["{$tm}"]) || $v['stat_max'] > $_rs['_sets'][0]['_data']["{$tm}"]) {
                $_rs['_sets'][0]['_data']["{$tm}"] = $v['stat_max'];
            }
            $_rs['_sets'][1]['_data']["{$tm}"] = $v['stat_avg'];
            if (!isset($_rs['_sets'][2]['_data']["{$tm}"]) || $v['stat_min'] < $_rs['_sets'][2]['_data']["{$tm}"]) {
                $_rs['_sets'][2]['_data']["{$tm}"] = $v['stat_min'];
            }
        }
    }
    return $_rs;
}

//------------------------------------
// EVENT LISTENERS
//------------------------------------

/**
 * Verify Module
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrProximaStat_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    jrCore_delete_setting('jrProximaStat', 'stat_method');
    jrCore_delete_setting('jrProximaStat', 'run_chance');
    return $_data;
}

/**
 * Start Statistics collector
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrProximaStat_process_init_listener($_data, $_user, $_conf, $_args, $event)
{
    $_tm = array(
        'rt' => array(
            'init' => microtime()
        )
    );
    $_tm = jrCore_trigger_event('jrProximaStat', 'stats_init', $_tm);
    jrCore_set_flag('jrProximaStat_events', $_tm);
    return $_data;
}

/**
 * Process Collected Statistics (every minute)
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrProximaStat_minute_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    // Process inserted stats
    jrProximaStat_process_stats();
    return $_data;
}

/**
 * Save Statistics + Process Collected Statistics (Near Real-Time)
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrProximaStat_process_exit_listener($_data, $_user, $_conf, $_args, $event)
{
    // Save our process response time
    if ($_tm = jrCore_get_flag('jrProximaStat_events')) {

        // Let listening modules save stats
        $_tm = jrCore_trigger_event('jrProximaStat', 'stats_save', $_tm);

        // If our "rt" (response time) key is still here, save it as default
        if (isset($_tm['rt'])) {
            $beg = explode(' ', $_tm['rt']['init']);
            $beg = $beg[1] + $beg[0];
            $end = explode(' ', microtime());
            $end = $end[1] + $end[0];
            $end = round(($end - $beg), 2);

            // Save it
            $mod = jrCore_db_escape(jrProximaCore_get_active_module());
            $val = 0;
            switch (jrProximaCore_get_access_method()) {
                case 'post':
                    $val = 1;
                    break;
                case 'get':
                    $val = 2;
                    break;
                case 'put':
                    $val = 3;
                    break;
                case 'delete':
                    $val = 4;
                    break;
            }

            $tbl = jrCore_db_table_name('jrProximaStat', 'stat_insert');
            $req = "INSERT DELAYED INTO {$tbl} (s_ins,s_mod,s_key,s_mtd,s_val) VALUES (UNIX_TIMESTAMP(),'{$mod}','rt',{$val},'{$end}')";
            jrCore_db_query($req);
        }
    }
    return $_data;
}

//------------------------------------
// FUNCTIONS
//------------------------------------

/**
 * Get collected stats
 * @param string $key Stat Key
 * @param array $_args function parameters
 * @return bool
 */
function jrProximaStat_get_stats($key, $_args = null)
{
    $fnc = jrProximaStat_get_active_stat_collector();
    if (function_exists($fnc)) {
        return $fnc($key, $_args);
    }
    return false;
}

/**
 * Process collected stats
 * @return bool
 */
function jrProximaStat_process_stats()
{
    $fnc = jrProximaStat_get_active_stat_processor();
    if (function_exists($fnc)) {
        return $fnc();
    }
    return false;
}

/**
 * Get active stat collection system
 * @return string
 */
function jrProximaStat_get_active_stat_collector()
{
    return 'jrProximaStat_mysql_get_stats';
}

/**
 * Get active stat processing system
 * @return string
 */
function jrProximaStat_get_active_stat_processor()
{
    return 'jrProximaStat_mysql_process_stats';
}

//------------------------------------
// MySQL STAT FUNCTIONS
//------------------------------------

/**
 * Get Stat rows for processing from MySQL
 * @param $key string Stat Key to get
 * @param $_args array Parameters
 * @return mixed
 */
function jrProximaStat_mysql_get_stats($key, $_args)
{
    $tbl = jrCore_db_table_name('jrProximaStat', 'stat_history');
    $col = 'stat_date, stat_mtd, stat_cnt, stat_min, stat_avg, stat_max';
    if (isset($_args['days']) && jrCore_checktype($_args['days'], 'number_nz')) {
        if ($_args['days'] > 1) {
            $col = 'LEFT(stat_date, 8) AS stat_date, stat_mtd, SUM(stat_cnt) AS stat_cnt, MIN(stat_min) AS stat_min, ROUND(AVG(stat_avg), 2) AS stat_avg, MAX(stat_max) AS stat_max';
        }
    }
    $req = "SELECT {$col} FROM {$tbl} WHERE stat_key = '" . jrCore_db_escape($key) . "'";

    // Specific module
    if (isset($_args['module'])) {
        $req .= " AND stat_mod = '" . jrCore_db_escape($_args['module']) . "'";
    }

    // Specific method
    $gby = '';
    if (isset($_args['method'])) {
        $req .= " AND stat_mtd = " . intval($_args['method']) . "";
        $gby = ', stat_mtd';
    }

    // Date range
    if (isset($_args['days']) && jrCore_checktype($_args['days'], 'number_nz')) {
        $old = (time() - ($_args['days'] * 86400));
        if ($_args['days'] > 1) {
            $req .= " AND stat_date > " . strftime('%Y%m%d%H', $old) . " GROUP BY stat_date{$gby}";
        }
        else {
            $req .= " AND stat_date > " . strftime('%Y%m%d%H', $old);
        }
    }
    $req .= " ORDER BY stat_date ASC";
    return jrCore_db_query($req, 'NUMERIC');
}

/**
 * Process Statistics (from insert to history)
 */
function jrProximaStat_mysql_process_stats()
{
    $fnc = jrProximaStat_get_active_stat_collector();
    if (!function_exists($fnc)) {
        jrCore_logger('CRI', "active stat collector function does not exist: {$fnc}");
        return false;
    }
    // See if we are being run by cron
    if (jrCore_module_is_active('jrCloudCron')) {
        return true;
    }

    // Process Stats - we use the play_key table for our lock
    $key = 'PXS' . gmstrftime('%y%m%d%H');
    $tbl = jrCore_db_table_name('jrCore', 'play_key');
    $req = "INSERT IGNORE INTO {$tbl} (key_time, key_code) VALUES (UNIX_TIMESTAMP(), '{$key}')";
    if (jrCore_db_query($req, 'INSERT_ID') > 0) {

        $tbi = jrCore_db_table_name('jrProximaStat', 'stat_insert');
        $tbh = jrCore_db_table_name('jrProximaStat', 'stat_history');
        $now = (time() - 10);
        $req = "INSERT LOW_PRIORITY INTO {$tbh} (stat_date, stat_mod, stat_key, stat_mtd, stat_cnt, stat_min, stat_avg, stat_max)
                        SELECT FROM_UNIXTIME(s_ins,'%Y%m%d%H'), s_mod, s_key, s_mtd, COUNT(s_sid), MIN(s_val), ROUND(AVG(s_val),3), MAX(s_val)
                        FROM {$tbi} WHERE s_ins < {$now} GROUP BY s_ins, s_mod, s_key, s_mtd ORDER BY s_ins ASC
                    ON DUPLICATE KEY UPDATE
                        stat_cnt = (stat_cnt + VALUES(stat_cnt)),
                        stat_min = IF(VALUES(stat_min) < stat_min, VALUES(stat_min), stat_min),
                        stat_avg = (((stat_avg * stat_cnt) + (VALUES(stat_avg) * VALUES(stat_cnt))) / (stat_cnt + VALUES(stat_cnt))),
                        stat_max = IF(VALUES(stat_max) > stat_max, VALUES(stat_max), stat_max)";
        $cnt = jrCore_db_query($req, 'COUNT');
        if ($cnt > 0) {
            $req = "DELETE FROM {$tbi} WHERE s_ins < {$now}";
            jrCore_db_query($req);
        }

        // Unlock
        $tbl = jrCore_db_table_name('jrCore', 'play_key');
        $req = "DELETE FROM {$tbl} WHERE key_code = '{$key}'";
        jrCore_db_query($req);

    }
    return true;
}
