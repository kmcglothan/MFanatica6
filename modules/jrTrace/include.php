<?php
/**
 * Jamroom 5 Event Tracer module
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function jrTrace_meta()
{
    $_tmp = array(
        'name'        => 'Event Tracer',
        'url'         => 'tracer',
        'version'     => '1.0.2',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Save registered event &quot;Traces&quot; to the Trace DataStore for listing',
        'category'    => 'listing',
        'license'     => 'mpl',
        'priority'    => 251, // LOW load priority (we want other listeners to run first)
        'locked'      => false,
        'activate'    => false
    );
    return $_tmp;
}

/**
 * init
 */
function jrTrace_init()
{
    global $_conf;
    // We don't need to run on specific views
    if (isset($_SERVER['REQUEST_URI']) && !strpos(' ' . $_SERVER['REQUEST_URI'], '/image/') && !strpos(' ' . $_SERVER['REQUEST_URI'], '/icon_css/')) {
        if (isset($_conf['jrTrace_active_tracers']) && strlen($_conf['jrTrace_active_tracers']) > 0) {
            // Register our listeners
            foreach (explode(',', $_conf['jrTrace_active_tracers']) as $event) {
                list($mod, $evt) = explode('_', trim($event), 2);
                jrCore_register_event_listener($mod, $evt, 'jrTrace_events_listener');
            }
        }
    }

    // Bring in additional data when listing
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrTrace_db_search_items_listener');

    // We provide triggers for our trace saving
    jrCore_register_event_trigger('jrTrace', 'trace_saved', 'Fired when a Trace is saved to the Trace DataStore');

    // Keep trace history clean
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrTrace_daily_maintenance_listener');

    return true;
}

/**
 * Listen for Traced Events
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrTrace_events_listener($_data, $_user, $_conf, $_args, $event)
{
    // See if we are active on this events
    if (isset($_conf['jrTrace_active_tracers']) && strpos(",{$_conf['jrTrace_active_tracers']},", $event)) {
        // We want to save off SOME of the data here as $_data can be HUGE
        $_save = array(
            'trace_module' => $_args['module'],
            'trace_event'  => $event,
            'trace_data'   => array()
        );
        if (isset($_data['_item_id'])) {
            $_save['trace_item_id'] = (int) $_data['_item_id'];
        }
        foreach ($_data as $k => $v) {
            switch ($k) {
                case 'profile_id':
                case 'profile_name':
                case 'profile_url':
                case 'profile_quota_id':
                case 'user_name':
                case 'user_group':
                case 'user_language':
                    $_save['trace_data'][$k] = $v;
                    break;
                default:
                    if (strpos(' ' . $k, '_title')) {
                        $_save['trace_data'][$k] = $v;
                    }
                    break;
            }
        }
        $_save['trace_data'] = json_encode($_save['trace_data']);
        $tid                 = jrCore_db_create_item('jrTrace', $_save, null, false);

        // Trigger our traced event
        $_args = array(
            'trace_id' => $tid,
            'module'   => $_args['module'],
            'trace'    => $_save
        );
        jrCore_trigger_event('jrTrace', 'trace_saved', $_data, $_args);
    }
    return $_data;
}

/**
 * Expand trace_data field back to an Array
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrTrace_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrTrace' && is_array($_data['_items'])) {
        foreach ($_data['_items'] as $k => $_v) {
            if (isset($_v['trace_data']) && strpos($_v['trace_data'], '{') === 0) {
                $_data['_items'][$k]['trace_data'] = json_decode($_v['trace_data'], true);
            }
        }
    }
    return $_data;
}

/**
 * Get all registered event tracers
 * @return array
 */
function jrTrace_get_event_tracers()
{
    $_out = array();
    $_tmp = jrCore_get_registered_module_features('jrTrace', 'trace_event');
    foreach ($_tmp as $mod => $_traces) {
        foreach ($_traces as $name => $desc) {
            $_out["{$mod}_{$name}"] = "{$mod}_{$name} - {$desc}";
        }
    }
    ksort($_out);
    return $_out;
}

/**
 * Keep the trace DataStore clean
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrTrace_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_db_get_datastore_item_count('jrTrace') > 0) {
        $dif = (time() - ($_conf['jrTrace_trace_history'] * 86400));
        $_sc = array(
            'search'              => array(
                "_created < {$dif}"
            ),
            'return_item_id_only' => true,
            'skip_triggers'       => true,
            'privacy_check'       => false,
            'ignore_pending'      => true,
            'limit'               => 1000
        );
        $cnt = 0;
        while (true) {
            // Cleanup in batches of 1,000
            $_rt = jrCore_db_search_items('jrTrace', $_sc);
            if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
                jrCore_db_delete_multiple_items('jrTrace', $_rt, false, false);
                $cnt += count($_rt);
            }
            else {
                break;
            }
        }
        if ($cnt > 0) {
            jrCore_logger('INF', "deleted " . jrCore_number_format($cnt) . " trace items older than {$_conf['jrTrace_trace_history']} days");
        }
    }
    return $_data;
}
