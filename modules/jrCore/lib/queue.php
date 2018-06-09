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
 * @package Queue Functions
 * @copyright 2016 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

// Constants
define('HIGH_PRIORITY_QUEUE', 1);
define('NORMAL_PRIORITY_QUEUE', 5);
define('LOW_PRIORITY_QUEUE', 9);

/**
 * Returns TRUE if the current process can become a queue worker
 * @return bool
 */
function jrCore_can_be_queue_worker()
{
    global $_conf;
    if (isset($_conf['jrCore_queue_worker_ratio']) && jrCore_checktype($_conf['jrCore_queue_worker_ratio'], 'number_nz') && $_conf['jrCore_queue_worker_ratio'] < 101) {
        // our jrCore_queue_worker_ratio defines the chance that any single
        // process can BECOME a queue worker.  Values are between 1 and 100
        if (mt_rand(1, 100) > $_conf['jrCore_queue_worker_ratio']) {
            return false;
        }
    }
    $wrk = false;
    // Did we create a queue entry during this process? If so, we can work it
    if (jrCore_get_flag('jrcore_queue_entry_created')) {
        $wrk = true;
    }
    // If we have APCu install on this server we can see how long it has been since the last check
    elseif (jrCore_local_cache_is_enabled()) {
        if (jrCore_set_local_cache_key('jrcore_can_be_queue_worker', 1, 5)) {
            $wrk = true;
        }
    }
    else {
        $wrk = true;
    }
    $_rt = jrCore_trigger_event('jrCore', 'check_if_queue_worker', array('can_be_worker' => $wrk));
    return $_rt['can_be_worker'];
}

/**
 * See if our Queues are Active
 * @return bool
 */
function jrCore_queues_are_active()
{
    $dir = jrCore_get_media_directory(0, FORCE_LOCAL);
    return (is_file("{$dir}/queues.disabled")) ? false : true;
}

/**
 * Set the state of the system queues
 * @param string $state on|off
 * @return bool
 */
function jrCore_set_system_queue_state($state)
{
    if ($state == 'off') {
        // We are stopping queues
        jrCore_write_media_file(0, 'queues.disabled', time());
    }
    else {
        jrCore_delete_media_file(0, 'queues.disabled');
    }
    return true;
}

/**
 * Returns an array of Queue names with Queue Entries
 * @param bool $skip_triggers
 * @return mixed
 */
function jrCore_get_ready_queues($skip_triggers = false)
{
    if (!$skip_triggers) {
        $_rt = array();
        $_rt = jrCore_trigger_event('jrCore', 'check_queues_ready', $_rt, $_rt);
        if ($_rt && is_array($_rt) && count($_rt) > 0) {
            // We've been handled by a listener
            return $_rt;
        }
    }
    // Get local queue info
    $tbl = jrCore_db_table_name('jrCore', 'queue_info');
    $req = "SELECT queue_name , queue_depth FROM {$tbl} WHERE queue_depth > 0";
    $_rt = jrCore_db_query($req, 'queue_name', false, 'queue_depth', false);
    if ($_rt && is_array($_rt)) {
        $_rt = array('ready_queues' => $_rt);
        if (!$skip_triggers) {
            $_rt = jrCore_trigger_event('jrCore', 'ready_queues', $_rt);
        }
        if (count($_rt['ready_queues']) > 0) {
            return $_rt['ready_queues'];
        }
    }
    return false;
}

/**
 * Get the number of active worker processes
 * @return int
 */
function jrCore_get_active_worker_count()
{
    // Let listeners give us the worker count
    $_rt = array('worker_count' => false);
    $_rt = jrCore_trigger_event('jrCore', 'queue_worker_count', $_rt);
    if ($_rt && is_array($_rt) && isset($_rt['worker_count']) && jrCore_checktype($_rt['worker_count'], 'number_nn')) {
        return intval($_rt['worker_count']);
    }
    $tbl = jrCore_db_table_name('jrCore', 'queue_info');
    $req = "SELECT SUM(queue_workers) AS qw FROM {$tbl}";
    $_rt = jrCore_db_query($req, 'SINGLE', false, null, false);
    return ($_rt && isset($_rt['qw'])) ? intval($_rt['qw']) : 0;
}

/**
 * Get queue entry from a named Queue
 * @param string $module Module Name
 * @param string $queue_name Queue Name
 * @param string $worker Unique Worker ID
 * @param string $system_id Unique System ID in a cluster system
 * @param int $timeout length of time worker is allowed to work queue
 * @param int $max_workers max number of system workers for this queue
 * @return mixed
 */
function jrCore_queue_get($module, $queue_name, $worker = null, $system_id = null, $timeout = null, $max_workers = null)
{
    if (is_null($worker) || strlen($worker) === 0) {
        $worker = getmypid();
    }
    $res = array(
        'queue_module' => $module,
        'queue_name'   => $queue_name,
        'worker_id'    => $worker,
        'system_id'    => $system_id,
        'timeout'      => (is_null($timeout)) ? 0 : intval($timeout),
        'max_workers'  => (is_null($max_workers)) ? 0 : intval($max_workers)
    );
    $res = jrCore_trigger_event('jrCore', 'get_queue_entry', $res, $res);
    if (isset($res['queue_id'])) {
        // Our listener returned our queue ID - return
        return $res;
    }
    elseif (isset($res['exit'])) {
        // We've been told to exit the queue loop
        return false;
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
                if (isset($_modules[$module][$queue_name][3])) {
                    // We found our queue
                    $exp = intval($_modules[$module][$queue_name][3]);
                    break;
                }
            }
        }
    }
    else {
        $exp = (int) $timeout;
    }

    $wrk = jrCore_db_escape($worker);
    $nam = jrCore_db_escape($queue_name);
    $tb1 = jrCore_db_table_name('jrCore', 'queue');
    $tb2 = jrCore_db_table_name('jrCore', 'queue_data');
    $_rq = array(
        "SET @QID := (SELECT queue_id FROM {$tb1} WHERE queue_module = '{$module}' AND queue_name = '{$nam}' AND queue_started = 0 AND queue_sleep <= UNIX_TIMESTAMP(){$add} ORDER BY queue_id ASC LIMIT 1 FOR UPDATE)",
        "UPDATE {$tb1} SET queue_worker = '{$wrk}', queue_started = UNIX_TIMESTAMP(), queue_expires = (UNIX_TIMESTAMP() + {$exp}) WHERE queue_id = @QID",
        "SELECT q.*, d.queue_item_id, d.queue_data, d.queue_status, d.queue_note FROM {$tb1} q LEFT JOIN {$tb2} d ON (d.queue_id = q.queue_id) WHERE q.queue_id = @QID"
    );
    $_rs = jrCore_db_multi_select($_rq, false);
    if ($_rs && is_array($_rs) && isset($_rs[0][0])) {
        $_rs = $_rs[0][0];
        if (!isset($_rs['queue_data']{2})) {
            // This is a bad queue entry - remove it and log it
            if (isset($_rs['queue_id']) && jrCore_checktype($_rs['queue_id'], 'number_nz')) {
                jrCore_queue_delete($_rs['queue_id'], $system_id);
                jrCore_logger('CRI', "deleted queue entry in {$module}/{$queue_name} missing queue_data", $_rs);
                return false;
            }
        }
        $_qd = json_decode($_rs['queue_data'], true);
        unset($_rs['queue_data']);
        $_rs['queue_data'] = array_merge($_qd, $_rs);
        return $_rs;
    }
    return false;
}

/**
 * Save a new entry into a named Queue
 * @param string $module Module creating the Queue entry
 * @param string $queue_name Queue Name
 * @param array $data Data to save to new Queue entry
 * @param int $sleep Initial "sleep" seconds
 * @param string $system_id Unique System ID in a cluster system
 * @param int $queue_max_entries Max number of entries allowed in a queue - set to 0 for unlimited
 * @return mixed
 */
function jrCore_queue_create($module, $queue_name, $data, $sleep = 0, $system_id = null, $queue_max_entries = 0)
{
    if (!is_array($data)) {
        // Bad queue data
        return false;
    }
    if (!jrCore_checktype($queue_name, 'core_string')) {
        // Bad queue name
        return false;
    }

    // See how long workers in this queue can work
    $tout = 3600;
    $_tmp = jrCore_get_flag('jrcore_register_queue_worker');
    if ($_tmp && is_array($_tmp)) {
        foreach ($_tmp as $priority => $_modules) {
            if (isset($_modules[$module][$queue_name][3])) {
                // We found our queue - get timeout
                $tout = intval($_modules[$module][$queue_name][3]);
            }
        }
    }
    $data['__queue_worker_timeout'] = $tout;

    $nam = $queue_name;
    $mod = $module;
    $sid = (is_null($system_id)) ? '' : jrCore_db_escape($system_id);
    $uid = 0;
    if (isset($data['item_id']) && jrCore_checktype($data['item_id'], 'number_nz')) {
        $uid = (int) $data['item_id'];
    }
    $_rp  = array(
        'queue_module' => $module,
        'queue_name'   => $queue_name,
        'sleep'        => $sleep,
        'system_id'    => $system_id,
        'queue_max'    => (int) $queue_max_entries
    );
    $data = jrCore_trigger_event('jrCore', 'create_queue_entry', $data, $_rp);
    if (isset($data['queue_id']) && jrCore_checktype($data['queue_id'], 'number_nz')) {
        // Our new queue entry has been handled by a listener
        return $data['queue_id'];
    }

    $lck = false;
    if ($queue_max_entries > 0) {
        $max = (int) $queue_max_entries;
        $tbl = jrCore_db_table_name('jrCore', 'queue_info');
        $req = "INSERT INTO {$tbl} (queue_name, queue_workers, queue_depth) VALUES ('{$mod}_{$nam}', 0, 1) ON DUPLICATE KEY UPDATE queue_depth = IF((queue_depth < {$max}), (queue_depth + 1), queue_depth)";
        $cnt = jrCore_db_query($req, 'COUNT', false, null, false);
        if (!$cnt || $cnt !== 2) {
            // We cannot create this queue...
            return false;
        }
        // Fall through - we are NOT over our max queue depth
        // NOTE: We set $lck here so we do not increment a second time below
        $lck = true;
    }

    $sec = (int) $sleep;
    $dat = jrCore_db_escape(json_encode($data));

    $tb1 = jrCore_db_table_name('jrCore', 'queue');
    $tb2 = jrCore_db_table_name('jrCore', 'queue_data');
    $tb3 = jrCore_db_table_name('jrCore', 'queue_info');
    $_rq = array(
        "INSERT INTO {$tb1} (queue_name, queue_created, queue_module, queue_started, queue_count, queue_sleep, queue_system_id) VALUES ('{$nam}', UNIX_TIMESTAMP(), '{$mod}', 0, 0, (UNIX_TIMESTAMP() + {$sec}), '{$sid}')",
        "INSERT INTO {$tb2} (queue_id, queue_item_id, queue_data) VALUES (LAST_INSERT_ID(), {$uid}, '{$dat}')"
    );
    if (!$lck) {
        $_rq[] = "INSERT INTO {$tb3} (queue_name, queue_workers, queue_depth) VALUES ('{$mod}_{$nam}', 0, 1) ON DUPLICATE KEY UPDATE queue_depth = (queue_depth + 1)";
    }
    $_rq[] = "SELECT LAST_INSERT_ID() AS iid";
    $_rt   = jrCore_db_multi_select($_rq, false);
    if ($_rt && is_array($_rt)) {
        $iid = (int) $_rt[0][0]['iid'];
        if ($iid && $iid > 0) {
            $_args = array('queue_id' => $iid);
            jrCore_trigger_event('jrCore', 'queue_entry_created', $data, $_args);
            jrCore_set_flag('jrcore_queue_entry_created', $iid);
            return $iid;
        }
    }
    // If we incremented queue_depth, we must decrement since we failed...
    if ($lck) {
        $tbl = jrCore_db_table_name('jrCore', 'queue_info');
        $req = "UPDATE {$tbl} SET queue_depth = (queue_depth - 1) WHERE queue_name = '{$mod}_{$nam}'";
        jrCore_db_query($req);
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
    $tb1 = jrCore_db_table_name('jrCore', 'queue');
    $tb2 = jrCore_db_table_name('jrCore', 'queue_data');
    $_rq = array(
        "DELETE FROM {$tb1} WHERE queue_id = {$qid}",
        "DELETE FROM {$tb2} WHERE queue_id = {$qid}"
    );
    jrCore_db_multi_select($_rq, false);
    return true;
}

/**
 * Release an entry back into a named Queue
 * @param int $id Queue ID to release
 * @param int $sleep Number of seconds to "sleep" queue entry
 * @param string $system_id Unique System ID in a cluster system
 * @param bool $increment set to FALSE to prevent queue_count increment
 * @return bool
 */
function jrCore_queue_release($id, $sleep = 0, $system_id = null, $increment = true)
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
    $inc = 1;
    if (!$increment) {
        $inc = 0;
    }
    $qid = (int) $id;
    $tbl = jrCore_db_table_name('jrCore', 'queue');
    $req = "UPDATE {$tbl} SET queue_worker = '', queue_started = 0, queue_expires = 0, queue_count = (queue_count + {$inc}), queue_sleep = (UNIX_TIMESTAMP() + " . intval($sleep) . ") WHERE queue_id = {$qid}";
    if (!is_null($system_id)) {
        $req .= " AND queue_system_id = '" . jrCore_db_escape($system_id) . "'";
    }
    $cnt = jrCore_db_query($req, 'COUNT', false, null, false);
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
    $qid = (int) $id;
    $tbl = jrCore_db_table_name('jrCore', 'queue');
    $req = "UPDATE {$tbl} SET queue_sleep = (UNIX_TIMESTAMP() + " . intval($sleep) . ") WHERE queue_id = {$qid}";
    if (!is_null($system_id)) {
        $req .= " AND queue_system_id = '" . jrCore_db_escape($system_id) . "'";
    }
    $cnt = jrCore_db_query($req, 'COUNT', false, null, false);
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
    $qid = (int) $queue_id;
    $tbl = jrCore_db_table_name('jrCore', 'queue_data');
    $req = "UPDATE {$tbl} SET queue_status = '" . jrCore_db_escape(substr($status, 0, 255)) . "', queue_note = '" . jrCore_db_escape(substr($status, 0, 255)) . "' WHERE queue_id = {$qid}";
    if (!is_null($system_id)) {
        $req .= " AND queue_system_id = '" . jrCore_db_escape($system_id) . "'";
    }
    $req .= ' LIMIT 1';
    $cnt = jrCore_db_query($req, 'COUNT', false, null, false);
    if ($cnt && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Return TRUE if a worker can work a queue
 * @param string $module Queue module
 * @param string $queue_name Name of Queue to get active worker count for
 * @param int $timeout Length of time (in seconds) that a queue worker can run before timing out
 * @param int $max_workers Maximum number of simultaneous workers for the queue
 * @param string $system_id Unique System ID in a cluster system
 * @return bool
 */
function jrCore_queue_worker_can_work($module, $queue_name, $timeout = 3600, $max_workers = 1, $system_id = null)
{
    if (!jrCore_checktype($timeout, 'number_nz')) {
        $timeout = 3600;
    }
    if (!jrCore_checktype($max_workers, 'number_nz')) {
        $max_workers = 1;
    }
    $res = array(
        'queue_name'  => $queue_name,
        'timeout'     => $timeout,
        'max_workers' => $max_workers,
        'system_id'   => $system_id
    );
    $res = jrCore_trigger_event('jrCore', 'queue_worker_can_work', $res, $res);
    if (isset($res['worker_can_work'])) {
        // We've been handled by a listener
        return $res['worker_can_work'];
    }

    // Is this worker able to work on a queue?
    $nam = jrCore_db_escape("{$module}_{$queue_name}");
    $tbl = jrCore_db_table_name('jrCore', 'queue_info');
    $req = "INSERT INTO {$tbl} (queue_name, queue_workers, queue_depth) VALUES ('{$nam}', 1, 0) ON DUPLICATE KEY UPDATE queue_workers = IF((queue_workers < {$max_workers}), (queue_workers + 1), queue_workers)";
    $cnt = jrCore_db_query($req, 'COUNT', false, null, false);
    if ($cnt && $cnt === 2) {
        // We are a worker!
        return getmypid();
    }
    return false;
}

/**
 * Release a queue worker slot
 * @param string $module Module Name
 * @param string $queue_name Queue Name
 * @param int $decrement set to an int > 0 to decrement queue_depth in info table
 * @return mixed
 */
function jrCore_queue_release_worker_slot($module, $queue_name, $decrement = null)
{
    $res = array(
        'module'     => $module,
        'queue_name' => $queue_name,
        'decrement'  => $decrement
    );
    $res = jrCore_trigger_event('jrCore', 'release_queue_worker_slot', $res);
    if (isset($res['worker_slot_released'])) {
        // We've been handled by a listener
        return true;
    }
    $nam = jrCore_db_escape("{$module}_{$queue_name}");
    $tbl = jrCore_db_table_name('jrCore', 'queue_info');
    if (!is_null($decrement)) {
        $dec = (int) $decrement;
        $req = "UPDATE {$tbl} SET queue_workers = (queue_workers - 1), queue_depth = (queue_depth - {$dec}) WHERE queue_name = '{$nam}' AND queue_workers > 0 AND queue_depth >= {$dec}";
    }
    else {
        $req = "UPDATE {$tbl} SET queue_workers = (queue_workers - 1) WHERE queue_name = '{$nam}' AND queue_workers > 0";
    }
    jrCore_db_query($req, null, false, null, false);
    return true;
}

/**
 * Decrement the queue depth of a queue
 * @param string $module
 * @param string $queue_name
 * @param int $decrement
 * @return bool
 */
function jrCore_queue_decrement_queue_depth($module, $queue_name, $decrement = 1)
{
    $res = array(
        'module'     => $module,
        'queue_name' => $queue_name,
        'decrement'  => $decrement
    );
    $res = jrCore_trigger_event('jrCore', 'decrement_queue_depth', $res);
    if (isset($res['queue_depth_decremented'])) {
        // We've been handled by a listener
        return true;
    }
    $nam = jrCore_db_escape("{$module}_{$queue_name}");
    $tbl = jrCore_db_table_name('jrCore', 'queue_info');
    $dec = (int) $decrement;
    $req = "UPDATE {$tbl} SET queue_depth = (queue_depth - {$dec}) WHERE queue_name = '{$nam}' AND queue_depth >= {$dec}";
    jrCore_db_query($req, null, false, null, false);
    return true;
}

/**
 * Check a queue for stuck workers that need to be reset
 * @param string $queue_name Queue Name
 * @param string $system_id System ID
 * @param int $timeout timeout in seconds
 * @return bool
 */
function jrCore_queue_cleanup($queue_name, $system_id, $timeout)
{
    $nam = jrCore_db_escape($queue_name);
    $tbl = jrCore_db_table_name('jrCore', 'queue');
    $req = "SELECT queue_id AS qid, queue_count AS cnt, (UNIX_TIMESTAMP() - queue_started) AS rs FROM {$tbl} WHERE queue_name = '{$nam}' AND queue_started > 0";
    if (!is_null($system_id)) {
        $req .= " AND queue_system_id = '" . jrCore_db_escape($system_id) . "'";
    }
    $_rt = jrCore_db_query($req, 'qid', false, null, false);
    if ($_rt && is_array($_rt) && count($_rt) > 0) {
        $_id = array();
        foreach ($_rt as $k => $_q) {
            if ($_q['rs'] > $timeout) {
                // If this is the 3RD time this queue has become stuck, we have a problem
                if ($_q['cnt'] > 2) {
                    jrCore_logger('MAJ', "deleted {$queue_name} queue entry running for {$_q['rs']} seconds - 3rd time failing");
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
            jrCore_db_query($req, null, false, null, false);
        }
    }
    return true;
}

/**
 * Reset expired queue workers
 * @deprecated
 * @return mixed
 */
function jrCore_reset_expired_workers()
{
    return true;
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

    $iid = (int) $item_id;
    $tb1 = jrCore_db_table_name('jrCore', 'queue_data');
    $tb2 = jrCore_db_table_name('jrCore', 'queue');
    $req = "DELETE {$tb1}, {$tb2} FROM {$tb1} JOIN {$tb2} ON ({$tb2}.queue_id = {$tb1}.queue_id) WHERE {$tb1}.queue_item_id = {$iid}";
    if (!is_null($system_id)) {
        $req .= " AND {$tb2}.queue_system_id = '" . jrCore_db_escape($system_id) . "'";
    }
    $cnt = jrCore_db_query($req, 'COUNT', false, null, false);
    if ($cnt && $cnt > 0) {
        return $cnt;
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
        if ($tmp && is_array($tmp) && $tmp !== $res) {
            // We've been handled by a listener
            return $tmp;
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
    $_qw = jrCore_db_query($req, 'q_nam', false, 'qworkers', false);

    // Queue Counts
    $req = "SELECT COUNT(queue_id) as qcount, MIN(queue_created) AS q_min, CONCAT_WS('~', queue_module, queue_name) AS q_nam FROM {$tbl} ";
    if (!is_null($system_id)) {
        $req .= "WHERE queue_system_id = '" . jrCore_db_escape($system_id) . "' ";
    }
    $req .= "GROUP BY q_nam";
    $_rt = jrCore_db_query($req, 'NUMERIC', false, null, false);
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
 * @param int $num_to_work The number of queue entries to process before exiting (set to 0 for unlimited)
 * @param int $max_workers - maximum number of worker processes allowed to work queue at one time
 * @param int $timeout - how long can a worker work a single queue entry before it is considered "hung"?
 * @param int $priority - worker priority from 1 to 99
 * @return bool
 */
function jrCore_register_queue_worker($module, $queue_name, $function, $num_to_work = 1, $max_workers = 1, $timeout = 3600, $priority = 5)
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
    $_tmp[$priority][$module][$queue_name] = array($function, intval($num_to_work), intval($max_workers), intval($timeout));
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
    $key = 'jrcore_validate_queue_info';
    if (!jrCore_get_flag($key)) {
        $tbl = jrCore_db_table_name('jrCore', 'queue_info');
        $req = "SELECT queue_name FROM {$tbl}";
        $_rt = jrCore_db_query($req, 'queue_name', false, 'queue_name', false);
        if ($_rt && is_array($_rt)) {
            $tb1 = jrCore_db_table_name('jrCore', 'queue_info');
            $tb2 = jrCore_db_table_name('jrCore', 'queue');
            foreach ($_rt as $qname) {
                list($mod, $nam) = explode('_', $qname, 2);
                $mod = trim($mod);
                $nam = trim($nam);
                $req = "INSERT INTO {$tb1} (queue_name, queue_workers, queue_depth) VALUES ('{$qname}',
                        (SELECT COUNT(queue_id) FROM {$tb2} WHERE queue_module = '{$mod}' AND queue_name = '{$nam}' AND queue_worker != ''),
                        (SELECT COUNT(queue_id) FROM {$tb2} WHERE queue_module = '{$mod}' AND queue_name = '{$nam}')
                    ) ON DUPLICATE KEY UPDATE queue_workers = VALUES(queue_workers), queue_depth = VALUES(queue_depth)";
                jrCore_db_query($req, null, false, null, false);
            }
        }
        jrCore_set_flag($key, 1);
    }
    return true;
}

/**
 * Validate that entries in queue_data have a corresponding entry in queue
 * @return mixed
 */
function jrCore_validate_queue_data()
{
    $cnt = 0;
    $key = 'jrcore_validate_queue_data';
    if (!jrCore_get_flag($key)) {
        $tb1 = jrCore_db_table_name('jrCore', 'queue');
        $tb2 = jrCore_db_table_name('jrCore', 'queue_data');
        $req = "DELETE FROM {$tb2} WHERE queue_id NOT IN (SELECT queue_id FROM {$tb1})";
        $cnt = jrCore_db_query($req, 'COUNT');
        jrCore_set_flag($key, 1);
    }
    return $cnt;
}

/**
 * Check for dead worker processes
 * @return bool
 */
function jrCore_check_for_dead_queue_workers()
{
    // We are going to check for any queue worker that has been working for longer than 10 minutes:
    // - if queue_expires has passed, we will kill the worker
    // - if the process is no longer running then we will reset the queue entry
    $tbl = jrCore_db_table_name('jrCore', 'queue');
    $req = "SELECT *, UNIX_TIMESTAMP() AS qtime FROM {$tbl} WHERE queue_started > 0 AND queue_started < (UNIX_TIMESTAMP() - 300) ORDER BY queue_id ASC LIMIT 30";
    $_rt = jrCore_db_query($req, 'NUMERIC', false, null, false);
    if ($_rt && is_array($_rt)) {
        $_rt = jrCore_trigger_event('jrCore', 'check_for_dead_queue_workers', array('_workers' => $_rt));
        if ($_rt && is_array($_rt) && isset($_rt['_workers']) && is_array($_rt['_workers'])) {
            foreach ($_rt['_workers'] as $_w) {
                if (jrCore_checktype($_w['queue_worker'], 'number_nz')) {

                    // We are a local process
                    $msg = false;
                    if ($_w['qtime'] >= $_w['queue_expires']) {
                        // This process has EXPIRED - kill it
                        if (function_exists('posix_getpgid') && function_exists('posix_kill')) {
                            if (posix_getpgid($_w['queue_worker'])) {
                                // Process is still running - kill it if we can
                                posix_kill($_w['queue_worker'], SIGTERM);
                                if ($err = @posix_get_last_error()) {
                                    if ($err > 0) {
                                        jrCore_logger('MAJ', "error restarting queue worker process id {$_w['queue_worker']}: " . posix_strerror($err));
                                    }
                                }
                            }
                        }
                        $msg = 'exceeded queue work time';
                    }
                    else {
                        // Is the worker process still running?
                        if (function_exists('posix_getpgid')) {
                            if (!posix_getpgid($_w['queue_worker'])) {
                                // This process is no longer running - release
                                $msg = 'process no longer running';
                            }
                        }
                    }
                    if ($msg) {
                        // Get associated queue data
                        $tbl = jrCore_db_table_name('jrCore', 'queue_data');
                        $req = "SELECT queue_data FROM {$tbl} WHERE queue_id = {$_w['queue_id']} LIMIT 1";
                        $_qd = jrCore_db_query($req, 'SINGLE');
                        if ($_qd && is_array($_qd)) {
                            $_qd = json_decode($_qd['queue_data'], true);
                        }
                        jrCore_queue_release($_w['queue_id']);
                        jrCore_queue_release_worker_slot($_w['queue_module'], $_w['queue_name'], 1);
                        jrCore_logger('MIN', "reset queue_id {$_w['queue_id']} for worker id {$_w['queue_worker']} - {$msg}", array('info' => $_w, 'data' => $_qd));
                    }

                }
            }
        }
    }
    return true;
}
