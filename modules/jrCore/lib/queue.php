<?php
/**
 * Jamroom System Core module
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
 * @package Queue Functions
 * @copyright 2016 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * See if our Queues are Active
 * @return string
 */
function jrCore_queues_are_active()
{
    $tbl = jrCore_db_table_name('jrCore', 'setting');
    $req = "SELECT `value` FROM {$tbl} WHERE `module` = 'jrCore' AND `name` = 'queues_active'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && isset($_rt['value']) && $_rt['value'] == 'off') {
        return false;
    }
    return true;
}

/**
 * Returns an array of Queue names with Queue Entries
 */
function jrCore_get_ready_queues()
{
    // We are setup on the new queues
    $_rt = array();
    $_rt = jrCore_trigger_event('jrCore', 'check_queues_ready', $_rt, $_rt);
    if ($_rt && is_array($_rt) && count($_rt) > 0) {
        // We've been handled by a listener
        return $_rt;
    }
    $tbl = jrCore_db_table_name('jrCore', 'queue_info');
    $req = "SELECT queue_name , queue_depth FROM {$tbl} WHERE queue_depth > 0";
    $_rt = jrCore_db_query($req, 'queue_name', false, 'queue_depth', false, null, false);
    if ($_rt && is_array($_rt)) {
        return $_rt;
    }
    return false;
}

/**
 * Get the number of active worker processes
 * @return int
 */
function jrCore_get_active_worker_count()
{
    $tbl = jrCore_db_table_name('jrCore', 'queue_info');
    $req = "SELECT SUM(queue_workers) AS qw FROM {$tbl}";
    $_rt = jrCore_db_query($req, 'SINGLE', false, null, false, null, false);
    return ($_rt && isset($_rt['qw'])) ? intval($_rt['qw']) : 0;
}

/**
 * Get queue entry from a named Queue
 * @param string $module Module Name
 * @param string $name Queue Name
 * @param string $worker Unique Worker ID
 * @param string $system_id Unique System ID in a cluster system
 * @param int $timeout length of time worker is allowed to work queue
 * @return mixed
 */
function jrCore_queue_get($module, $name, $worker = null, $system_id = null, $timeout = null)
{
    if (is_null($worker) || strlen($worker) === 0) {
        $worker = getmypid();
    }
    $res = array(
        'queue_module' => $module,
        'queue_name'   => $name,
        'worker_id'    => $worker,
        'system_id'    => $system_id
    );
    $res = jrCore_trigger_event('jrCore', 'get_queue_entry', array(), $res);
    if ($res === false) {
        // Our listener returned false - exit
        return false;
    }
    elseif (is_array($res) && count($res) > 0) {
        // We've been handled by a listener
        return $res;
    }
    // See if we have a system_id
    $add = '';
    if (!is_null($system_id)) {
        $add = " AND queue_system_id = '" . jrCore_db_escape($system_id) . "'";
    }

    // See how long workers in this queue can work
    $exp = 3600;
    if (is_null($timeout)) {
        $_tmp = jrCore_get_flag('jrcore_register_queue_worker');
        if ($_tmp && is_array($_tmp)) {
            foreach ($_tmp as $priority => $_modules) {
                if (isset($_modules[$module][$name][3])) {
                    // We found our queue
                    $exp = intval($_modules[$module][$name][3]);
                    break;
                }
            }
        }
    }
    else {
        $exp = (int) $timeout;
    }

    $wrk = jrCore_db_escape($worker);
    $nam = jrCore_db_escape($name);
    $tbl = jrCore_db_table_name('jrCore', 'queue');
    $req = "UPDATE {$tbl} SET queue_worker = '{$wrk}', queue_started = UNIX_TIMESTAMP(), queue_expires = (UNIX_TIMESTAMP() + {$exp})
             WHERE queue_module = '{$module}' AND queue_name = '{$nam}' AND queue_started = 0 AND queue_sleep <= UNIX_TIMESTAMP(){$add}
             ORDER BY queue_count ASC, queue_created ASC LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT', false, null, false, null, false);
    if ($cnt && $cnt > 0) {

        // We grabbed a queue entry - get the details
        $req = "SELECT queue_id, queue_created, queue_module, queue_data, queue_count, queue_status FROM {$tbl}
                 WHERE queue_module = '{$module}' AND queue_worker = '{$wrk}' AND queue_name = '{$nam}'{$add} LIMIT 1";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if ($_rt && is_array($_rt)) {
            $_rt['queue_data']                    = json_decode($_rt['queue_data'], true);
            $_rt['queue_data']['queue_id']        = (int) $_rt['queue_id'];
            $_rt['queue_data']['queue_name']      = $name;
            $_rt['queue_data']['queue_count']     = (int) $_rt['queue_count'];
            $_rt['queue_data']['queue_status']    = $_rt['queue_status'];
            $_rt['queue_data']['queue_module']    = $module;
            $_rt['queue_data']['queue_system_id'] = $system_id;
            return $_rt;
        }
    }
    return false;
}

/**
 * Save a new entry into a named Queue
 * @param string $module Module creating the Queue entry
 * @param string $name Queue Name
 * @param array $data Data to save to new Queue entry
 * @param int $sleep Initial "sleep" seconds
 * @param string $system_id Unique System ID in a cluster system
 * @param int $queue_max_entries Max number of entries allowed in a queue - set to 0 for unlimited
 * @return mixed
 */
function jrCore_queue_create($module, $name, $data, $sleep = 0, $system_id = null, $queue_max_entries = 0)
{
    if (!is_array($data)) {
        // Bad queue data
        return false;
    }
    if (!jrCore_checktype($name, 'core_string')) {
        // Bad queue name
        return false;
    }

    // See how long workers in this queue can work
    $tout = 3600;
    $_tmp = jrCore_get_flag('jrcore_register_queue_worker');
    if ($_tmp && is_array($_tmp)) {
        foreach ($_tmp as $priority => $_modules) {
            if (isset($_modules[$module][$name][3])) {
                // We found our queue - get timeout
                $tout = intval($_modules[$module][$name][3]);
            }
        }
    }
    $data['__queue_worker_timeout'] = $tout;

    $tbl = jrCore_db_table_name('jrCore', 'queue');
    $nam = $name;
    $mod = $module;
    $sid = (is_null($system_id)) ? '' : jrCore_db_escape($system_id);
    $uid = 0;
    if (isset($data['item_id']) && jrCore_checktype($data['item_id'], 'number_nz')) {
        $uid = (int) $data['item_id'];
    }
    $_rp  = array(
        'queue_module' => $module,
        'queue_name'   => $name,
        'sleep'        => $sleep,
        'system_id'    => $system_id,
        'queue_max'    => (int) $queue_max_entries
    );
    $data = jrCore_trigger_event('jrCore', 'create_queue_entry', $data, $_rp);
    if (!is_array($data)) {
        // Our new queue entry has been handled by a listener and it has returned the ID
        return $data;
    }
    if ($queue_max_entries > 0) {
        $req = "SELECT COUNT(queue_id) AS c FROM {$tbl} WHERE queue_name = '{$nam}' AND queue_module = '{$mod}'";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if ($_rt && is_array($_rt) && $_rt['c'] >= $queue_max_entries) {
            // We are at the queue entry limit
            return false;
        }
    }
    $sec = (int) $sleep;
    $dat = jrCore_db_escape(json_encode($data));
    $req = "INSERT INTO {$tbl} (queue_name, queue_created, queue_module, queue_item_id, queue_data, queue_started, queue_count, queue_sleep, queue_system_id) VALUES ('{$nam}', UNIX_TIMESTAMP(), '{$mod}', '{$uid}', '{$dat}', 0, 0, (UNIX_TIMESTAMP() + {$sec}), '{$sid}')";
    $iid = jrCore_db_query($req, 'INSERT_ID');
    if ($iid && $iid > 0) {

        // Increase Queue Depth
        $nam = jrCore_db_escape("{$mod}_{$nam}");
        $tbl = jrCore_db_table_name('jrCore', 'queue_info');
        $req = "INSERT INTO {$tbl} (queue_name, queue_workers, queue_depth) VALUES ('{$nam}', 0, 1) ON DUPLICATE KEY UPDATE queue_depth = (queue_depth + 1)";
        jrCore_db_query($req);

        $_args = array('queue_id' => $iid);
        jrCore_trigger_event('jrCore', 'queue_entry_created', $data, $_args);

        return $iid;
    }
    return false;
}

/**
 * Delete an entry from a named Queue
 * @param int $id Queue ID to delete
 * @param string $system_id Unique System ID in a cluster system
 * @return bool
 */
function jrCore_queue_delete($id, $system_id = null)
{
    $res = array(
        'queue_id'  => $id,
        'system_id' => $system_id
    );
    $res = jrCore_trigger_event('jrCore', 'delete_queue_entry', $res, $res);
    if (!is_array($res)) {
        // Our queue entry has been deleted by a listener
        return true;
    }
    $qid = (int) $id;
    $tbl = jrCore_db_table_name('jrCore', 'queue');
    $req = "DELETE FROM {$tbl} WHERE queue_id = '{$qid}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Release an entry back into a named Queue
 * @param int $id Queue ID to release
 * @param int $sleep Number of seconds to "sleep" queue entry
 * @param string $system_id Unique System ID in a cluster system
 * @return bool
 */
function jrCore_queue_release($id, $sleep = 0, $system_id = null)
{
    $res = array(
        'queue_id'  => $id,
        'sleep'     => (int) $sleep,
        'system_id' => $system_id
    );
    $res = jrCore_trigger_event('jrCore', 'release_queue_entry', $res, $res);
    if (!is_array($res)) {
        // We've been released by a listener
        return true;
    }
    $tbl = jrCore_db_table_name('jrCore', 'queue');
    $req = "UPDATE {$tbl} SET queue_worker = '', queue_started = '0', queue_count = (queue_count + 1), queue_sleep = (UNIX_TIMESTAMP() + " . intval($sleep) . ") WHERE queue_id = '" . intval($id) . "'";
    if (!is_null($system_id)) {
        $req .= " AND queue_system_id = '" . jrCore_db_escape($system_id) . "'";
    }
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Set or Extend Sleep period for a Queue Entry
 * @param int $id Queue ID to release
 * @param int $sleep Number of seconds to "sleep" queue entry
 * @param string $system_id Unique System ID in a cluster system
 * @return bool
 */
function jrCore_queue_sleep($id, $sleep, $system_id = null)
{
    $res = array(
        'queue_id'  => $id,
        'sleep'     => (int) $sleep,
        'system_id' => $system_id
    );
    $res = jrCore_trigger_event('jrCore', 'sleep_queue_entry', $res, $res);
    if (!is_array($res)) {
        // We've been released by a listener
        return true;
    }
    $tbl = jrCore_db_table_name('jrCore', 'queue');
    $req = "UPDATE {$tbl} SET queue_sleep = (UNIX_TIMESTAMP() + " . intval($sleep) . ") WHERE queue_id = '" . intval($id) . "'";
    if (!is_null($system_id)) {
        $req .= " AND queue_system_id = '" . jrCore_db_escape($system_id) . "'";
    }
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Set Status on an individual Queue Entry
 * @param $queue_id int Queue ID to set status for
 * @param $status string Queue Status (max 256 chars)
 * @param string $note string Queue Note (max 256 chars)
 * @param string $system_id Unique System ID in a cluster system
 * @return bool
 */
function jrCore_queue_set_status($queue_id, $status, $note = '', $system_id = null)
{
    $res = array(
        'queue_id'  => $queue_id,
        'status'    => $status,
        'note'      => $note,
        'system_id' => $system_id
    );
    $res = jrCore_trigger_event('jrCore', 'set_queue_status', $res, $res);
    if (!is_array($res)) {
        // We've been handled by a listener
        return true;
    }
    $tbl = jrCore_db_table_name('jrCore', 'queue');
    $req = "UPDATE {$tbl} SET queue_status = '" . jrCore_db_escape(substr($status, 0, 255)) . "', queue_note = '" . jrCore_db_escape(substr($status, 0, 255)) . "' WHERE queue_id = '" . intval($queue_id) . "'";
    if (!is_null($system_id)) {
        $req .= " AND queue_system_id = '" . jrCore_db_escape($system_id) . "'";
    }
    $req .= ' LIMIT 1';
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Get number of active workers for a given Queue
 * @param string $module Queue module
 * @param string $queue Name of Queue to get active worker count for
 * @param int $timeout Length of time (in seconds) that a queue worker can run before timing out
 * @param int $max_workers Maximum number of simultaneous workers for the queue
 * @param string $system_id Unique System ID in a cluster system
 * @return int
 */
function jrCore_queue_worker_count($module, $queue, $timeout = 3600, $max_workers = 1, $system_id = null)
{
    if (!jrCore_checktype($timeout, 'number_nz')) {
        $timeout = 3600;
    }
    if (!jrCore_checktype($max_workers, 'number_nz')) {
        $max_workers = 1;
    }
    $res = array(
        'queue_name'  => $queue,
        'timeout'     => $timeout,
        'max_workers' => $max_workers,
        'system_id'   => $system_id
    );
    $res = jrCore_trigger_event('jrCore', 'get_queue_worker_count', $res, $res);
    if (!is_array($res)) {
        // We've been handled by a listener
        return intval($res);
    }

    // Is this worker able to work on a queue?
    $nam = jrCore_db_escape("{$module}_{$queue}");
    $con = jrCore_db_connect(true, false);
    $tbl = jrCore_db_table_name('jrCore', 'queue_info');
    $req = "INSERT INTO {$tbl} (queue_name, queue_workers, queue_depth) VALUES ('{$nam}', 1, 0) ON DUPLICATE KEY UPDATE queue_workers = IF((queue_workers < {$max_workers}), (queue_workers + 1), queue_workers)";
    $cnt = jrCore_db_query($req, 'COUNT', false, null, false, $con, false, true);
    if (!$cnt || $cnt === 0) {
        // We could NOT grab a queue slot - we are not a worker
        return $max_workers;
    }
    // We ARE a worker - return 0 so we fall under the max allowed workers and enter the worker loop
    return 0;
}

/**
 * Release a queue worker slot
 * @param string $module Module Name
 * @param string $queue Queue Name
 * @param int $decrement set to an int > 0 to decrement queue_depth in info table
 * @return mixed
 */
function jrCore_queue_release_worker_slot($module, $queue, $decrement = null)
{
    $nam = jrCore_db_escape("{$module}_{$queue}");
    $tbl = jrCore_db_table_name('jrCore', 'queue_info');
    if (!is_null($decrement)) {
        $dec = (int) $decrement;
        $req = "UPDATE {$tbl} SET queue_workers = (queue_workers - 1), queue_depth = (queue_depth - {$dec}) WHERE queue_name = '{$nam}' AND queue_workers > 0 AND queue_depth >= {$dec}";
    }
    else {
        $req = "UPDATE {$tbl} SET queue_workers = (queue_workers - 1) WHERE queue_name = '{$nam}' AND queue_workers > 0";
    }
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!$cnt || $cnt === 0) {
        return false;
    }
    return true;
}

/**
 * Check a queue for stuck workers that need to be reset
 * @param string $queue Queue Name
 * @param string $system_id System ID
 * @param int $timeout timeout in seconds
 * @return bool
 */
function jrCore_queue_cleanup($queue, $system_id, $timeout)
{
    $nam = jrCore_db_escape($queue);
    $tbl = jrCore_db_table_name('jrCore', 'queue');
    $req = "SELECT queue_id AS qid, queue_count AS cnt, (UNIX_TIMESTAMP() - queue_started) AS rs FROM {$tbl} WHERE queue_name = '{$nam}' AND queue_started > 0";
    if (!is_null($system_id)) {
        $req .= " AND queue_system_id = '" . jrCore_db_escape($system_id) . "'";
    }
    $_rt = jrCore_db_query($req, 'qid');
    if ($_rt && is_array($_rt) && count($_rt) > 0) {
        $_id = array();
        foreach ($_rt as $k => $_q) {
            if ($_q['rs'] > $timeout) {
                // If this is the 3RD time this queue has become stuck, we have a problem
                if ($_q['cnt'] > 2) {
                    jrCore_logger('MAJ', "deleted {$queue} queue entry running for {$_q['rs']} seconds - 3rd time failing");
                    jrCore_queue_delete($k);
                }
                else {
                    $_id[] = $k;
                }
            }
        }
        unset($_rt);
        // Return to Queue?
        if (count($_id) > 0) {
            $req = "UPDATE {$tbl} SET queue_started = 0, queue_worker = '', queue_count = (queue_count + 1), queue_sleep = 0 WHERE queue_id IN(" . implode(',', $_id) . ")";
            jrCore_db_query($req);
        }
    }
    return true;
}

/**
 * Reset expired queue workers
 * @return mixed
 */
function jrCore_reset_expired_workers()
{
    $tbl = jrCore_db_table_name('jrCore', 'queue');
    $req = "UPDATE {$tbl} SET queue_worker = '', queue_started = 0, queue_count = (queue_count + 1), queue_sleep = (UNIX_TIMESTAMP() + 1) WHERE queue_expires < UNIX_TIMESTAMP()";
    return jrCore_db_query($req, 'COUNT', false, null, false, null, false);
}

/**
 * Delete a Queue Entry for a module/item_id
 * @param string $module Module that created the queue entry
 * @param integer $item_id Unique Item ID to delete queue entries for
 * @param string $system_id Unique System ID in a cluster system
 * @return mixed
 */
function jrCore_queue_delete_by_item_id($module, $item_id, $system_id = null)
{
    $res = array(
        'module'    => $module,
        'item_id'   => $item_id,
        'system_id' => $system_id
    );
    $res = jrCore_trigger_event('jrCore', 'delete_queue_by_item_id', $res, $res);
    if (!is_array($res)) {
        // We've been handled by a listener
        return $res;
    }

    $tbl = jrCore_db_table_name('jrCore', 'queue');
    $req = "DELETE FROM {$tbl} WHERE queue_module = '" . jrCore_db_escape($module) . "' AND queue_item_id = '" . intval($item_id) . "'";
    if (!is_null($system_id)) {
        $req .= " AND queue_system_id = '" . jrCore_db_escape($system_id) . "'";
    }
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt > 0) {
        return true;
    }
    return false;
}

/**
 * Return information about all active queues and workers
 * @param string $system_id Unique System ID in a cluster system
 * @param bool $trigger set to false to skip sending get_queue_info event
 * @return array|bool
 */
function jrCore_get_active_queue_info($system_id = null, $trigger = true)
{
    if ($trigger) {
        $res = array('system_id' => $system_id);
        $tmp = jrCore_trigger_event('jrCore', 'get_queue_info', $res, $res);
        if (is_array($tmp) && $tmp !== $res) {
            // We've been handled by a listener
            return $res;
        }
    }

    // Worker Count
    $_rs = array();
    $tbl = jrCore_db_table_name('jrCore', 'queue');
    $req = "SELECT COUNT(queue_worker) as qworkers, CONCAT_WS('~', queue_module, queue_name) AS q_nam FROM {$tbl} WHERE queue_started > 0 ";
    if (!is_null($system_id)) {
        $req .= " AND queue_system_id = '" . jrCore_db_escape($system_id) . "' ";
    }
    $req .= "GROUP BY q_nam";
    $_qw = jrCore_db_query($req, 'q_nam', false, 'qworkers');

    // Queue Counts
    $req = "SELECT COUNT(queue_id) as qcount, MIN(queue_created) AS q_min, CONCAT_WS('~', queue_module, queue_name) AS q_nam FROM {$tbl} ";
    if (!is_null($system_id)) {
        $req .= "WHERE queue_system_id = '" . jrCore_db_escape($system_id) . "' ";
    }
    $req .= "GROUP BY q_nam";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $_q) {
            list($mod, $nam) = explode('~', $_q['q_nam']);
            if (!isset($_rs[$mod])) {
                $_rs[$mod] = array();
            }
            $_rs[$mod][$nam] = array(
                'count'   => $_q['qcount'],
                'latency' => $_q['q_min']
            );
            if (isset($_qw["{$_q['q_nam']}"])) {
                $_rs[$mod][$nam]['workers'] = (int) $_qw["{$_q['q_nam']}"];
            }
        }
    }
    if (count($_rs) > 0) {
        return $_rs;
    }
    return false;
}

/**
 * Register a Queue worker process
 *
 * Registering a Queue worker process tells the core that
 * when a process completes, if there is queue work to be done,
 * the process should hang around and work the queue before exiting
 *
 * @param string $module Module registering the queue worker
 * @param string $queue_name Name of the Queue to read from
 * @param string $function Function to execute when a queue entry is found
 * @param int $count The number of queue entries to process before exiting (set to 0 for unlimited)
 * @param int $max_workers - maximum number of worker processes allowed to work queue at one time
 * @param int $timeout - how long can a worker work a single queue entry before it is considered "hung"?
 * @param int $priority - worker priority from 1 to 99
 * @return bool
 */
function jrCore_register_queue_worker($module, $queue_name, $function, $count = 1, $max_workers = 1, $timeout = 3600, $priority = 5)
{
    $_tmp = jrCore_get_flag('jrcore_register_queue_worker');
    if (!$_tmp) {
        $_tmp = array();
    }
    if (!isset($_tmp[$priority])) {
        $_tmp[$priority] = array();
    }
    if (!isset($_tmp[$priority][$module])) {
        $_tmp[$priority][$module] = array();
    }
    if (!jrCore_checktype($max_workers, 'number_nz')) {
        $max_workers = 1;
    }
    if (!jrCore_checktype($timeout, 'number_nz')) {
        $timeout = 3600;
    }
    $_tmp[$priority][$module][$queue_name] = array($function, intval($count), intval($max_workers), intval($timeout));
    ksort($_tmp, SORT_NUMERIC);
    jrCore_set_flag('jrcore_register_queue_worker', $_tmp);
    return true;
}

/**
 * Remove a previously registered Queue Worker
 * @param $module string Module that registered Queue Worker
 * @param $queue_name string Queue Name to remove
 * @return bool
 */
function jrCore_remove_queue_worker($module, $queue_name)
{
    $_tmp = jrCore_get_flag('jrcore_register_queue_worker');
    if ($_tmp && is_array($_tmp)) {
        foreach ($_tmp as $priority => $_mods) {
            foreach ($_mods as $mod => $name) {
                if ($mod == $module && $queue_name == $name) {
                    unset($_tmp[$priority][$mod][$name]);
                    jrCore_set_flag('jrcore_register_queue_worker', $_tmp);
                    return true;
                }
            }
        }
    }
    return true;
}

/**
 * Validate information in queue_info table based on actual data in queues
 * @return bool
 */
function jrCore_validate_queue_info()
{
    // Validate queue depth in queue info table
    $tbl = jrCore_db_table_name('jrCore', 'queue_info');
    $req = "SELECT queue_name FROM {$tbl}";
    $_rt = jrCore_db_query($req, 'queue_name', false, 'queue_name');
    if ($_rt && is_array($_rt)) {
        $tb1 = jrCore_db_table_name('jrCore', 'queue_info');
        $tb2 = jrCore_db_table_name('jrCore', 'queue');
        foreach ($_rt as $qname) {
            list($mod, $nam) = explode('_', $qname, 2);
            $mod = trim($mod);
            $nam = trim($nam);
            $req = "INSERT INTO {$tb1} (queue_name, queue_workers, queue_depth) VALUES ('{$qname}',
                        (SELECT COUNT(queue_id) FROM {$tb2} WHERE queue_module = '{$mod}' AND queue_name = '{$nam}' AND LENGTH(queue_worker) > 0),
                        (SELECT COUNT(queue_id) FROM {$tb2} WHERE queue_module = '{$mod}' AND queue_name = '{$nam}')
                    ) ON DUPLICATE KEY UPDATE queue_workers = VALUES(queue_workers), queue_depth = VALUES(queue_depth)";
            jrCore_db_query($req);
        }
    }
    return true;
}

/**
 * Check for dead worker processes
 * @return bool
 */
function jrCore_check_for_dead_queue_workers()
{
    if (function_exists('posix_getpgid')) {
        $tbl = jrCore_db_table_name('jrCore', 'queue');
        $req = "SELECT queue_id, queue_module, queue_name, queue_worker FROM {$tbl} WHERE queue_started > 0";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt)) {
            foreach ($_rt as $_w) {
                if (jrCore_checktype($_w['queue_worker'], 'number_nz')) {
                    // We are a local process - see if we are still running
                    if (!posix_getpgid($_w['queue_worker'])) {
                        // No longer running and did not clean up - release back to queue
                        jrCore_queue_release($_w['queue_id']);
                        jrCore_queue_release_worker_slot($_w['queue_module'], $_w['queue_name']);
                    }
                }
            }
        }
    }
    return true;
}
