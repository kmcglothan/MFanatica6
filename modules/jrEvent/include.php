<?php
/**
 * Jamroom Event Calendar module
 *
 * copyright 2017 The Jamroom Network
 *
 * This Jamroom file is LICENSED SOFTWARE, and cannot be redistributed.
 *
 * This Source Code is subject to the terms of the Jamroom Network
 * Commercial License -  please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
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
function jrEvent_meta()
{
    $_tmp = array(
        'name'        => 'Event Calendar',
        'url'         => 'event',
        'version'     => '1.3.6',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Add Calendar Events support to Profiles',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/276/event-calendar',
        'requires'    => 'jrCore:6.0.0',
        'category'    => 'profiles',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrEvent_init()
{
    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrEvent', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrEvent', 'update');

    // Core support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrEvent', 'off');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrEvent', 'on');
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrEvent', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrEvent', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrEvent', 'update', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrEvent', 'attend', 'item_action.tpl');

    // We have fields that can be searched
    jrCore_register_module_feature('jrSearch', 'search_fields', 'jrEvent', 'event_title', 23);

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'jrEvent', 'profile_jrEvent_item_count', 23);

    // Support for adding attendees
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrEvent_db_search_items_listener');
    jrCore_register_event_listener('jrCore', 'db_get_item', 'jrEvent_db_get_item_listener');

    // Recycle Bin
    jrCore_register_event_listener('jrCore', 'db_delete_item', 'jrEvent_db_delete_item_listener');
    jrCore_register_event_listener('jrCore', 'empty_recycle_bin', 'jrEvent_empty_recycle_bin_listener');
    jrCore_register_event_listener('jrCore', 'expire_recycle_bin', 'jrEvent_expire_recycle_bin_listener');
    jrCore_register_event_listener('jrCore', 'restore_recycle_bin_item', 'jrEvent_restore_recycle_bin_item_listener');

    // System Reset listener
    jrCore_register_event_listener('jrDeveloper', 'reset_system', 'jrEvent_reset_system_listener');

    // Verify listener
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrEvent_verify_module_listener');

    // When an action is shared via jrOneAll, we can provide the text of the shared item
    jrCore_register_event_listener('jrOneAll', 'network_share_text', 'jrEvent_network_share_text_listener');

    // We want RSS feeds
    jrCore_register_module_feature('jrFeed', 'feed_support', 'jrEvent', 'enabled');
    // RSS Format listener
    jrCore_register_event_listener('jrFeed', 'create_rss_feed', 'jrEvent_create_rss_feed_listener');

    // Support for past_events
    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrEvent_db_search_params_listener');

    // Notification support
    jrCore_register_event_listener('jrUser', 'hourly_notification', 'jrEvent_hourly_notification_listener');

    // Register our CSS
    jrCore_register_module_feature('jrCore', 'css', 'jrEvent', 'jrEvent.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrEvent', 'calendar.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrEvent', 'small_calendar.css');

    // Register our custom JS
    jrCore_register_module_feature('jrCore', 'javascript', 'jrEvent', 'jrEvent.js');

    // notifications
    $_tmp = array(
        'field' => 'quota_jrEvent_allowed_attending',
        'label' => 65, // 'Attending Event'
        'help'  => 66 // 'When an event you are attending is near, how do you want to be notified?'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrEvent', 'attending_event', $_tmp);

    // Profile tabs
    $_tmp = array(
        'label'  => 'List',
        'active' => 'on',
        'group'  => 'all'
    );
    jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrEvent', 'default', $_tmp);

    // Profile tabs
    $_tmp = array(
        'label' => 'Calendar',
        'group' => 'all'
    );
    jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrEvent', 'calendar', $_tmp);

    $_tmp = array(
        'title'  => 'attending event button',
        'active' => 'off'
    );
    jrCore_register_module_feature('jrCore', 'item_list_button', 'jrEvent', 'jrEvent_item_attend_button', $_tmp);

    $_tmp = array(
        'title'  => 'attending event button',
        'active' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrEvent', 'jrEvent_item_attend_button', $_tmp);

    $_tmp = array(
        'title'  => 'event calendar button',
        'active' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'item_list_button', 'jrEvent', 'jrEvent_item_calendar_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrEvent', 'jrEvent_item_calendar_button', $_tmp);

    // Site Builder widget
    jrCore_register_module_feature('jrSiteBuilder', 'widget', 'jrEvent', 'widget_calendar', 'Event Calendar');

    // If the tracer module is installed, we have an event for it
    jrCore_register_event_trigger('jrEvent', 'attending', 'Fired when a user clicks the attending button on an event');
    jrCore_register_module_feature('jrTrace', 'trace_event', 'jrEvent', 'attending', 'User clicks the attending button for an event');
    jrCore_register_event_listener('jrTrace', 'trace_saved', 'jrEvent_trace_saved_listener'); // add in the state 'attending' 'not_attending' to the trace.

    // Attending/attended events
    $_tmp = array(
        'group' => 'user',
        'label' => 147, // 'Events attending'
        'url'   => 'attending'
    );
    jrCore_register_module_feature('jrCore', 'skin_menu_item', 'jrEvent', 'attending', $_tmp);

    return true;
}

//----------------------
// EVENT LISTENERS
//----------------------

/**
 * Event notification listener
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrEvent_hourly_notification_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrUser' && jrCore_checktype($_conf['jrEvent_notification_time'], 'number_nz')) {
        // Get all events happening between now and notification_time away
        $min = time();
        $max = $min + ($_conf['jrEvent_notification_time'] * 3600);
        $_s  = array(
            "search"                       => array(
                "event_date >= {$min}",
                "event_date < {$max}"
            ),
            'exclude_jrUser_keys'          => true,
            'exclude_jrProfile_keys'       => true,
            'exclude_jrProfile_quota_keys' => true,
            'limit'                        => 10000
        );
        $_rt = jrCore_db_search_items('jrEvent', $_s);
        if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
            // Get all of these event attendees
            $_attendees = array();
            foreach ($_rt['_items'] as $rt) {
                if (isset($rt['event_attendee']) && is_array($rt['event_attendee']) && count($rt['event_attendee']) > 0) {
                    foreach ($rt['event_attendee'] as $_v) {
                        $_attendees[] = array_merge($_v, $rt, array('system_name' => $_conf['jrCore_system_name']));
                    }
                }
            }
            if (count($_attendees) > 0) {
                // Notify?
                $tbl = jrCore_db_table_name('jrEvent', 'attendee');
                foreach ($_attendees as $attendee) {
                    if ($attendee['attendee_notified'] != 1) {
                        // Yes - Set up notification
                        list($sub, $msg) = jrCore_parse_email_templates('jrEvent', 'attending_event', $attendee);
                        $_data[] = array(
                            'event'   => 'attending_event',
                            'module'  => 'jrEvent',
                            'user_id' => $attendee['attendee_user_id'],
                            'subject' => $sub,
                            'message' => $msg
                        );
                        $req     = "UPDATE {$tbl} SET `attendee_notified` = 1 WHERE `attendee_event_id` = '{$attendee['attendee_event_id']}' AND `attendee_user_id` = '{$attendee['attendee_user_id']}' LIMIT 1";
                        jrCore_db_query($req);
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * Verify module listener to add in the event_end_day field to the form designer
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrEvent_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    $_rt = jrCore_get_designer_form_fields('jrEvent', 'create');
    if (!isset($_rt['event_end_day'])) {
        $_tmp = array(
            'name'     => 'event_end_day',
            'label'    => 62,
            'help'     => 63,
            'type'     => 'datetime',
            'validate' => 'date',
            'active'   => false,
            'required' => false
        );
        jrCore_verify_designer_form_field('jrEvent', 'create', $_tmp);
    }
    $_rt = jrCore_get_designer_form_fields('jrEvent', 'update');
    if (!isset($_rt['event_end_day'])) {
        $_tmp = array(
            'name'     => 'event_end_day',
            'label'    => 62,
            'help'     => 63,
            'type'     => 'datetime',
            'validate' => 'date',
            'active'   => false,
            'required' => false
        );
        jrCore_verify_designer_form_field('jrEvent', 'update', $_tmp);
    }

    // Convert any event_date fields in yyyymmddhhmm format to epoch time
    $_s  = array(
        "search"                       => array("event_date >= 200000000000"),
        'return_keys'                  => array('_item_id', 'event_date'),
        'ignore_pending'               => true,
        'include_jrProfile_keys'       => true,
        'exclude_jrProfile_quota_keys' => true,
        'limit'                        => 10000

    );
    $_rt = jrCore_db_search_items('jrEvent', $_s);
    if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
        $_tmp = array();
        $cnt  = 0;
        foreach ($_rt['_items'] as $rt) {
            $_tmp["{$rt['_item_id']}"]["event_date"] = mktime(substr($rt['event_date'], 8, 2), substr($rt['event_date'], 10, 2), 0, substr($rt['event_date'], 4, 2), substr($rt['event_date'], 6, 2), substr($rt['event_date'], 0, 4));
            $cnt++;
        }
        if ($cnt && $cnt > 0) {
            jrCore_db_update_multiple_items('jrEvent', $_tmp);
            jrCore_logger('INF', "jrEvent: converted {$cnt} event dates in DataStore");
        }
    }

    // Delete event_banner_search field
    $_s  = array(
        "search"                       => array("event_banner_search LIKE %"),
        'return_keys'                  => array('_item_id'),
        'ignore_pending'               => true,
        'include_jrProfile_keys'       => true,
        'exclude_jrProfile_quota_keys' => true,
        'limit'                        => 10000

    );
    $_rt = jrCore_db_search_items('jrEvent', $_s);
    if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
        $cnt = 0;
        foreach ($_rt['_items'] as $rt) {
            jrCore_db_delete_item_key('jrEvent', $rt['_item_id'], 'event_banner_search');
            $cnt++;
        }
        if ($cnt && $cnt > 0) {
            jrCore_logger('INF', "jrEvent: removed {$cnt} unused keys from DataStore");
        }
    }

    // Populate the new attendee table with existing attendees
    $_sp = array(
        'search'      => array(
            'event_attendees like %{%'
        ),
        'return_keys' => array('_item_id', 'event_attendees'),
        'limit'       => 10000
    );
    $_rt = jrCore_db_search_items('jrEvent', $_sp);
    if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
        $_dl = array();
        $tbl = jrCore_db_table_name('jrEvent', 'attendee');
        foreach ($_rt['_items'] as $rt) {
            if (isset($rt['event_attendees']) && strlen($rt['event_attendees']) > 2) {
                $_ea = json_decode($rt['event_attendees'], true);
                if ($_ea && is_array($_ea)) {
                    // Get user accounts that still exist for these event attendees
                    $_us = array();
                    $_tm = jrCore_db_get_multiple_items('jrUser', array_keys($_ea), array('_user_id', 'user_name'));
                    if ($_tm && is_array($_tm)) {
                        foreach ($_tm as $_u) {
                            $_us["{$_u['_user_id']}"] = $_u['user_name'];
                        }
                    }
                    foreach ($_ea as $uid => $ignore) {
                        if (isset($_us[$uid])) {
                            $req = "INSERT IGNORE INTO {$tbl} (attendee_created,attendee_user_id,attendee_event_id,attendee_notified,attendee_active) VALUES (UNIX_TIMESTAMP(),'{$uid}','{$rt['_item_id']}',0,1)";
                            jrCore_db_query($req);
                        }
                    }
                }
            }
            $_dl[] = $rt['_item_id'];
        }
        // Remove the event_attendees keys for the items we have converted
        if (count($_dl) > 0) {
            jrCore_db_delete_key_from_multiple_items('jrEvent', $_dl, array('event_attendees'));
            jrCore_logger('INF', "converted " . count($_dl) . " Events to new attendee structure");
        }
    }
    return $_data;
}

/**
 * System Reset listener
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrEvent_reset_system_listener($_data, $_user, $_conf, $_args, $event)
{
    $tbl = jrCore_db_table_name('jrEvent', 'attendee');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");
    return $_data;
}

/**
 * Expire Recycle Bin Items
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrEvent_expire_recycle_bin_listener($_data, $_user, $_conf, $_args, $event)
{
    if (is_array($_data) && is_array($_data['_items']) && count($_data['_items']) > 0) {
        $_ev = array();
        $_id = array();
        foreach ($_data['_items'] as $k => $_it) {
            switch ($_it['module']) {
                case 'jrEvent':
                    $_ev[] = $_it['item_id'];
                    break;
                case 'jrUser':
                    $_id[] = $_it['item_id'];
                    break;
            }
        }
        $tbl = jrCore_db_table_name('jrEvent', 'attendee');
        if (count($_ev) > 0) {
            $req = "DELETE FROM {$tbl} WHERE attendee_event_id IN(" . implode(',', $_ev) . ')';
            jrCore_db_query($req);
        }
        if (count($_id) > 0) {
            $req = "DELETE FROM {$tbl} WHERE attendee_user_id IN(" . implode(',', $_id) . ')';
            jrCore_db_query($req);
        }
    }
    return $_data;
}

/**
 * Restore Recycle Bin Item
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrEvent_restore_recycle_bin_item_listener($_data, $_user, $_conf, $_args, $event)
{
    switch ($_args['module']) {

        case 'jrEvent':
            // An event is being restored - undelete attendees
            $tbl = jrCore_db_table_name('jrEvent', 'attendee');
            $req = "UPDATE {$tbl} SET attendee_active = '1' WHERE attendee_event_id = '" . intval($_args['item_id']) . "'";
            jrCore_db_query($req);
            break;

        case 'jrUser':
            // Restore this user's place in their events
            $tbl = jrCore_db_table_name('jrEvent', 'attendee');
            $req = "UPDATE {$tbl} SET attendee_active = '1' WHERE attendee_user_id = '" . intval($_args['item_id']) . "'";
            jrCore_db_query($req);
    }
    return $_data;
}

/**
 * Empty Recycle Bin
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrEvent_empty_recycle_bin_listener($_data, $_user, $_conf, $_args, $event)
{
    $tbl = jrCore_db_table_name('jrEvent', 'attendee');
    $req = "DELETE FROM {$tbl} WHERE attendee_active = '0'";
    jrCore_db_query($req);
    return $_data;
}

/**
 * Flag attendees as deleted when a event is deleted
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrEvent_db_delete_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['_item_id']) && jrCore_checktype($_args['_item_id'], 'number_nz')) {
        if ($_args['module'] == 'jrEvent') {
            // flag attendees of this event as deleted...
            $tbl = jrCore_db_table_name('jrEvent', 'attendee');
            $req = "UPDATE {$tbl} SET attendee_active = '0' WHERE `attendee_event_id` = {$_args['_item_id']}";
            jrCore_db_query($req);
        }
        elseif ($_args['module'] == 'jrUser') {
            // flag user as deleted...
            $tbl = jrCore_db_table_name('jrEvent', 'attendee');
            $req = "UPDATE {$tbl} SET attendee_active = '0' WHERE `attendee_user_id` = {$_data['_user_id']}";
            jrCore_db_query($req);
        }
    }
    return $_data;
}

/**
 * Add attendees to events
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrEvent_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if (is_array($_data) && isset($_data['_items']) && jrCore_is_view_request()) {

        // Expand Event Attendees
        if ($_args['module'] == 'jrEvent') {

            // Is table there
            if (!jrCore_db_table_exists('jrEvent', 'attendee')) {
                return $_data;
            }

            // Get all event attendees
            $_eid = array();
            foreach ($_data['_items'] as $_v) {
                $_eid[] = $_v['_item_id'];
            }
            $_uid = array();
            $eid  = implode(',', $_eid);
            $tbl  = jrCore_db_table_name('jrEvent', 'attendee');
            $req  = "SELECT * FROM {$tbl} WHERE `attendee_event_id` IN ({$eid}) AND `attendee_active` = '1'";
            $_rt  = jrCore_db_query($req, 'NUMERIC');
            if ($_rt && is_array($_rt) && count($_rt) > 0) {
                $_attendee = array();
                foreach ($_rt as $rt) {
                    $_attendee["{$rt['attendee_event_id']}"]["{$rt['attendee_user_id']}"] = $rt;
                    $_uid["{$rt['attendee_user_id']}"]                                    = $rt['attendee_user_id'];
                }
            }
            foreach ($_data['_items'] as $k => $_v) {
                if (isset($_attendee["{$_v['_item_id']}"])) {
                    $_data['_items'][$k]['event_attendee']       = $_attendee["{$_v['_item_id']}"];
                    $_data['_items'][$k]['event_attendee_count'] = count($_attendee["{$_v['_item_id']}"]);
                }
            }
            if (count($_uid) > 0) {
                $_rt = array(
                    'search'                       => array(
                        '_user_id in ' . implode(',', $_uid)
                    ),
                    'order_by'                     => array(
                        'user_name' => 'asc'
                    ),
                    'return_keys'                  => array('_user_id', 'user_name', '_profile_id', 'profile_name', 'profile_url', 'profile_quota_id'),
                    'ignore_pending'               => true,
                    'include_jrProfile_keys'       => true,
                    'exclude_jrProfile_quota_keys' => true,
                    'limit'                        => count($_uid)
                );
                $_rt = jrCore_db_search_items('jrUser', $_rt);
                if ($_rt && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
                    // Add attendees to each event
                    $_uid = array();
                    foreach ($_rt['_items'] as $_v) {
                        $_uid["{$_v['_user_id']}"] = $_v;
                    }
                    foreach ($_data['_items'] as $k => $_v) {
                        if (isset($_v['event_attendee']) && is_array($_v['event_attendee'])) {
                            foreach ($_data['_items'][$k]['event_attendee'] as $i => $_u) {
                                if (isset($_uid["{$_u['attendee_user_id']}"])) {
                                    $_data['_items'][$k]['event_attendee'][$i] = array_merge($_u, $_uid["{$_u['attendee_user_id']}"]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * Add attendees to event
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrEvent_db_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['module']) && $_args['module'] == 'jrEvent' && jrCore_is_view_request()) {

        // Is table there
        if (!jrCore_db_table_exists('jrEvent', 'attendee')) {
            return $_data;
        }
        // Add in attendee info
        $tbl = jrCore_db_table_name('jrEvent', 'attendee');
        $req = "SELECT * FROM {$tbl} WHERE `attendee_event_id` = {$_data['_item_id']} AND `attendee_active` = '1'";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt) && count($_rt) > 0) {
            $_data['event_attendee_count'] = 0;
            $_data['event_attendee']       = array();
            foreach ($_rt as $rt) {
                $uid                           = (int) $rt['attendee_user_id'];
                $_data['event_attendee'][$uid] = $rt;
                $_data['event_attendee_count']++;
            }
            if (count($_data['event_attendee']) > 0) {
                $_rt = array(
                    'search'                       => array(
                        '_user_id in ' . implode(',', array_keys($_data['event_attendee']))
                    ),
                    'return_keys'                  => array('_user_id', 'user_name', '_profile_id', 'profile_name', 'profile_url', 'profile_quota_id'),
                    'ignore_pending'               => true,
                    'include_jrProfile_keys'       => true,
                    'exclude_jrProfile_quota_keys' => true,
                    'limit'                        => $_data['event_attendee_count']
                );
                $_rt = jrCore_db_search_items('jrUser', $_rt);
                if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
                    // Add attendees to event
                    $_uid = array();
                    $_srt = array();
                    foreach ($_rt['_items'] as $_v) {
                        $uid        = (int) $_v['_user_id'];
                        $_uid[$uid] = $_v;
                        $_srt[$uid] = $_v['user_name'];
                    }
                    natcasesort($_srt);
                    foreach ($_srt as $uid => $name) {
                        $_data['event_attendee'][$uid] = array_merge($_data['event_attendee'][$uid], $_uid[$uid]);
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * Add share data to a jrOneAll network share
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return mixed
 */
function jrEvent_network_share_text_listener($_data, $_user, $_conf, $_args, $event)
{
    // $_data:
    // [providers] => twitter
    // [user_token] => (token)
    // [user_id] => 1
    // [action_module] => jrEvent
    // [action_data] => (JSON array of data for item initiating action)
    $_data = json_decode($_data['action_data'], true);
    if (!isset($_data) || !is_array($_data)) {
        return false;
    }
    $_ln = jrUser_load_lang_strings($_data['user_language']);

    // We return an array:
    // 'text' => text to post (i.e. "tweet")
    // 'url'  => URL to media item,
    // 'name' => name if media item
    $url = jrCore_get_module_url('jrEvent');
    $txt = $_ln['jrEvent'][32];
    if ($_data['action_mode'] == 'update') {
        $txt = $_ln['jrEvent'][33];
    }
    $_out = array(
        'text' => "{$_conf['jrCore_base_url']}/{$_data['profile_url']} {$_data['profile_name']} {$txt}: \"{$_data['event_title']}\" {$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['_item_id']}/{$_data['event_title_url']}",
        'link' => array(
            'url'  => "{$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['_item_id']}/{$_data['event_title_url']}",
            'name' => $_data['event_title']
        )
    );
    // See if they included a picture with the event
    if (isset($_data['event_image_size']) && jrCore_checktype($_data['event_image_size'], 'number_nz')) {
        $_out['picture'] = array(
            'url' => "{$_conf['jrCore_base_url']}/{$url}/image/event_image/{$_data['_item_id']}/large"
        );
    }
    return $_out;
}

/**
 * Format RSS entries
 * @param $_data array incoming data array from view_jrFeed_default
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrEvent_create_rss_feed_listener($_data, $_user, $_conf, $_args, $event)
{
    // We override the $_data array with one specific to jrEvent
    if (isset($_args['module']) && $_args['module'] == 'jrEvent' && isset($_args['profile_url'])) {
        $_rt = array(
            'search'   => array(
                "event_date >= " . time(),
                "profile_url = {$_args['profile_url']}"
            ),
            "order_by" => array("event_date" => 'asc'),
            "limit"    => $_args['limit']
        );
        $_rt = jrCore_db_search_items('jrEvent', $_rt);
        if (isset($_rt['_items']) && is_array($_rt['_items'])) {
            foreach ($_rt['_items'] as $k => $rt) {
                $_rt['_items'][$k]['title']       = "@{$rt['profile_url']}";
                $_rt['_items'][$k]['link']        = "{$_conf['jrCore_base_url']}/{$rt['profile_url']}/{$_args['url']}/{$rt['_item_id']}/{$rt['event_title_url']}";
                $event_date                       = date("d F Y H:i", $rt['event_date']);
                $_rt['_items'][$k]['description'] = "{$event_date} - {$rt['event_location']}";
                if ($rt['event_description'] != '') {
                    $_rt['_items'][$k]['description'] .= " - &quot;{$rt['event_description']}&quot;";
                }
                $_rt['_items'][$k]['pubdate'] = strftime("%a, %d %b %Y %T %Z", $rt['_created']);
            }
        }
        return $_rt['_items'];
    }
    return $_data;
}

/**
 * Hide past events if configured to do so
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrEvent_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_conf;
    if (isset($_args['module']) && $_args['module'] == 'jrEvent') {
        // This is a jrCore_db_search_items() call for events.  If this is on the TIMELINE
        // then we need to return event data regardless - otherwise we need to check and
        // see if this site is showing past events.  Note the special "jrAction_list"
        if (isset($_conf['jrEvent_show_past']) && $_conf['jrEvent_show_past'] != 'on' && !isset($_data['jrAction_list'])) {
            // See if we are already being checked in a search param
            if (isset($_data['search']) && is_array($_data['search'])) {
                foreach ($_data['search'] as $v) {
                    if (strpos($v, 'event_date') === 0) {
                        // We are already being checked...
                        return $_data;
                    }
                }
            }
            else {
                $_data['search'] = array();
            }
            $add = 3600;
            if (jrCore_checktype($_conf['jrEvent_show_past'], 'number_nz')) {
                $add = ($_conf['jrEvent_show_past'] * 60);
            }
            $_data['search'][] = 'event_date >= ' . (time() - $add);
        }
    }
    return $_data;
}

/**
 * Save the trace
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrEvent_trace_saved_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['module']) && $_args['module'] == 'jrEvent') {
        if (isset($_data['attending_state']) && jrCore_checktype($_args['trace_id'], 'number_nz')) {
            $_sv = array(
                'trace_state' => $_data['attending_state']
            );
            jrCore_db_update_item('jrTrace', $_args['trace_id'], $_sv);
        }
    }
    return $_data;
}

//------------------------------------
// WIDGETS
//------------------------------------

/**
 * Display CONFIG screen for Widget
 * @param $_post array Post info
 * @param $_user array User array
 * @param $_conf array Global Config
 * @param $_wg array Widget info
 * @return bool
 */
function jrEvent_widget_calendar_config($_post, $_user, $_conf, $_wg)
{
    $_opt = array(
        'small' => 'Small',
        'large' => 'Large',
    );
    $_tmp = array(
        'name'     => 'calendar_size',
        'label'    => 'Calendar Size',
        'help'     => 'Select the module whos items you want to search',
        'options'  => $_opt,
        'value'    => $_wg['widget_data']['calendar_size'],
        'type'     => 'select',
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    return true;
}

/**
 * Get Widget results from posted Config data
 * @param $_post array Post info
 * @return array
 */
function jrEvent_widget_calendar_config_save($_post)
{
    $_data = array(
        'calendar_size' => $_post['calendar_size']
    );
    return array('widget_data' => $_data);
}

/**
 * Widget DISPLAY
 * @param $_widget array Page Widget info
 * @return string
 */
function jrEvent_widget_calendar_display($_widget)
{
    return jrCore_parse_template('widget_calendar.tpl', $_widget, 'jrEvent');
}

//----------------------
// EVENT ITEM BUTTONS
//----------------------

/**
 * Return calendar button for event list and detail pages
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrEvent_item_calendar_button($module, $_item, $_args, $smarty, $test_only = false)
{
    if ($module == 'jrEvent') {
        if ($test_only) {
            return true;
        }
        $_item['icon_size'] = jrCore_get_skin_icon_size();
        return trim(jrCore_parse_template('button_calendar.tpl', $_item, 'jrEvent'));
    }
    return false;
}

/**
 * Return "Attending" button for event detail page
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrEvent_item_attend_button($module, $_item, $_args, $smarty, $test_only = false)
{
    if ($module == 'jrEvent') {
        if ($test_only) {
            return true;
        }
        $_item['icon_size'] = jrCore_get_skin_icon_size();
        $params             = array(
            'item' => $_item
        );
        return smarty_function_jrEvent_attending_button($params, $smarty);
    }
    return false;
}

//----------------------
// SMARTY
//----------------------

/**
 * Creates a Attending/Attended button for logged in users on an event page
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrEvent_attending_button($params, $smarty)
{
    global $_user;

    // Are we logged in?
    if (!jrUser_is_logged_in()) {
        return '';
    }

    // we must get an item array
    if (!isset($params['item'])) {
        return jrCore_smarty_missing_error('item');
    }
    if (!is_array($params['item'])) {
        return jrCore_smarty_invalid_error('item');
    }

    // allowed by quota?
    if (!isset($params['item']['quota_jrEvent_allowed_attending']) || $params['item']['quota_jrEvent_allowed_attending'] == 'off') {
        return '';
    }

    $params['item']['user_id'] = $_user['_user_id'];

    // Happened?
    $params['item']['happened'] = false;
    if ($params['item']['event_date'] < time()) {
        $params['item']['happened'] = true;
    }

    // Attendee?
    $params['item']['attendee'] = false;
    $tbl                        = jrCore_db_table_name('jrEvent', 'attendee');
    $req                        = "SELECT * FROM {$tbl} WHERE `attendee_user_id` = '{$_user['_user_id']}' AND `attendee_event_id` = '{$params['item']['_item_id']}' LIMIT 1";
    if (jrCore_checktype(jrCore_db_query($req, 'COUNT'), 'number_nz')) {
        $params['item']['attendee'] = true;
    }

    // process and return
    $out = jrCore_parse_template('button_attend.tpl', $params['item'], 'jrEvent');
    $out = trim($out);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Creates a Calendar
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrEvent_calendar($params, $smarty)
{
    global $_post, $_conf;

    $month = (int) ($params['month']) ? $params['month'] : date('n');
    $year  = (int) ($params['year']) ? $params['year'] : date('Y');

    $month_start = mktime(0, 0, 0, date($month), 1, date($year));
    $month_end   = mktime(0, 0, 0, date($month) + 1, 1, date($year));

    //search for events for this month
    $_sp = array(
        'search' => array(
            "event_date >= $month_start",
            "event_date <= $month_end",
        ),
        'limit'  => 100
    );

    if (isset($params['search'])) {
        $_sp['search'][] = $params['search'];
    }

    if (isset($params['profile_id']) && jrCore_checktype($params['profile_id'], 'number_nz')) {
        $_sp['search'][] = "_profile_id = {$params['profile_id']}";
    }

    $_rt = jrCore_db_search_items('jrEvent', $_sp);
    //arrange them by day.
    $_events = array();
    if (is_array($_rt['_items'])) {
        foreach ($_rt['_items'] as $event) {
            $day             = date('j', $event['event_date']);
            $_events[$day][] = $event;
        }
    }

    $_rep = array(
        '_calendar' => jrEvent_create_month($month, $year),
        'month'     => $month,
        'year'      => $year,
        '_years'    => jrEvent_get_year_range(),
        '_events'   => $_events
    );
    if (isset($params['profile_id']) && jrCore_checktype($params['profile_id'], 'number_nz')) {
        $_rep['_profile']        = jrCore_db_get_item('jrProfile', $params['profile_id']);
        $murl                    = jrCore_get_module_url('jrEvent');
        $_rep['browse_base_url'] = $_conf['jrCore_base_url'] . '/' . $_post['module_url'] . '/' . $murl . '/calendar';
    }
    // process and return

    if (isset($params['template']) && $params['template'] != '' && $params['tpl_dir']) {
        //allow other modules to set the tpl_dir.
    }
    elseif (isset($params['template']) && $params['template'] != '') {
        $params['tpl_dir'] = $_conf['jrCore_active_skin'];
    }
    else {
        $params['template'] = "calendar.tpl";
        $params['tpl_dir']  = 'jrEvent';
    }

    $out = jrCore_parse_template($params['template'], $_rep, $params['tpl_dir']);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

//----------------------
// MISCELLANEOUS
//----------------------

/**
 * Get values for past event times
 * @return array
 */
function jrEvent_get_past_event_times()
{
    return array(
        'on'   => 'Show all events regardless of start time',
        'off'  => 'Do not show events that have passed their start time',
        '15'   => 'Show events for up to 15 minutes past start time',
        '30'   => 'Show events for up to 30 minutes past start time',
        '45'   => 'Show events for up to 45 minutes past start time',
        '60'   => 'Show events for up to 1 hour past start time',
        '120'  => 'Show events for up to 2 hours past start time',
        '180'  => 'Show events for up to 3 hours past start time',
        '240'  => 'Show events for up to 4 hours past start time',
        '300'  => 'Show events for up to 5 hours past start time',
        '360'  => 'Show events for up to 6 hours past start time',
        '480'  => 'Show events for up to 8 hours past start time',
        '960'  => 'Show events for up to 16 hours past start time',
        '1440' => 'Show events for up to 1 day past start time',
        '2880' => 'Show events for up to 2 days past start time',
        '4320' => 'Show events for up to 3 days past start time',
        '7200' => 'Show events for up to 5 days past start time'
    );
}

/**
 * Get presets for Recurring events select list
 * @return array
 */
function jrEvent_recurring_presets()
{
    $_ln = jrUser_load_lang_strings();
    return array(
        'no'    => $_ln['jrEvent'][68],
        'daily' => $_ln['jrEvent'][69],
        'sun'   => $_ln['jrEvent'][70],
        'mon'   => $_ln['jrEvent'][71],
        'tue'   => $_ln['jrEvent'][72],
        'wed'   => $_ln['jrEvent'][73],
        'thu'   => $_ln['jrEvent'][74],
        'fri'   => $_ln['jrEvent'][75],
        'sat'   => $_ln['jrEvent'][76],
        '1'     => $_ln['jrEvent'][77],
        '2'     => $_ln['jrEvent'][78],
        '3'     => $_ln['jrEvent'][79],
        '4'     => $_ln['jrEvent'][80],
        '5'     => $_ln['jrEvent'][81],
        '6'     => $_ln['jrEvent'][82],
        '7'     => $_ln['jrEvent'][83],
        '8'     => $_ln['jrEvent'][84],
        '9'     => $_ln['jrEvent'][85],
        '10'    => $_ln['jrEvent'][86],
        '11'    => $_ln['jrEvent'][87],
        '12'    => $_ln['jrEvent'][88],
        '13'    => $_ln['jrEvent'][89],
        '14'    => $_ln['jrEvent'][90],
        '15'    => $_ln['jrEvent'][91],
        '16'    => $_ln['jrEvent'][92],
        '17'    => $_ln['jrEvent'][93],
        '18'    => $_ln['jrEvent'][94],
        '19'    => $_ln['jrEvent'][95],
        '20'    => $_ln['jrEvent'][96],
        '21'    => $_ln['jrEvent'][97],
        '22'    => $_ln['jrEvent'][98],
        '23'    => $_ln['jrEvent'][99],
        '24'    => $_ln['jrEvent'][100],
        '25'    => $_ln['jrEvent'][101],
        '26'    => $_ln['jrEvent'][102],
        '27'    => $_ln['jrEvent'][103],
        '28'    => $_ln['jrEvent'][104],
        '29'    => $_ln['jrEvent'][105],
        '30'    => $_ln['jrEvent'][106],
        '31'    => $_ln['jrEvent'][107],
        '1_sun' => $_ln['jrEvent'][108],
        '1_mon' => $_ln['jrEvent'][109],
        '1_tue' => $_ln['jrEvent'][110],
        '1_wed' => $_ln['jrEvent'][111],
        '1_thu' => $_ln['jrEvent'][112],
        '1_fri' => $_ln['jrEvent'][113],
        '1_sat' => $_ln['jrEvent'][114],
        '2_sun' => $_ln['jrEvent'][115],
        '2_mon' => $_ln['jrEvent'][116],
        '2_tue' => $_ln['jrEvent'][117],
        '2_wed' => $_ln['jrEvent'][118],
        '2_thu' => $_ln['jrEvent'][119],
        '2_fri' => $_ln['jrEvent'][120],
        '2_sat' => $_ln['jrEvent'][121],
        '3_sun' => $_ln['jrEvent'][122],
        '3_mon' => $_ln['jrEvent'][123],
        '3_tue' => $_ln['jrEvent'][124],
        '3_wed' => $_ln['jrEvent'][125],
        '3_thu' => $_ln['jrEvent'][126],
        '3_fri' => $_ln['jrEvent'][127],
        '3_sat' => $_ln['jrEvent'][128],
        '4_sun' => $_ln['jrEvent'][129],
        '4_mon' => $_ln['jrEvent'][130],
        '4_tue' => $_ln['jrEvent'][131],
        '4_wed' => $_ln['jrEvent'][132],
        '4_thu' => $_ln['jrEvent'][133],
        '4_fri' => $_ln['jrEvent'][134],
        '4_sat' => $_ln['jrEvent'][135],
        '5_sun' => $_ln['jrEvent'][136],
        '5_mon' => $_ln['jrEvent'][137],
        '5_tue' => $_ln['jrEvent'][138],
        '5_wed' => $_ln['jrEvent'][139],
        '5_thu' => $_ln['jrEvent'][140],
        '5_fri' => $_ln['jrEvent'][141],
        '5_sat' => $_ln['jrEvent'][142]
    );
}

/**
 * Page jumper for Events
 * @return string
 */
function jrEvent_page_banner_item_jumper()
{
    global $_user, $_conf, $_post;
    $_rt = array(
        'search'         => array(
            "_profile_id = {$_user['user_active_profile_id']}",
        ),
        'order_by'       => array(
            'event_title' => 'ASC'
        ),
        'limit'          => 250,
        'skip_triggers'  => true,
        'privacy_check'  => false,
        'ignore_pending' => true
    );
    $_rt = jrCore_db_search_items('jrEvent', $_rt);

    $_lang = jrUser_load_lang_strings();
    $c_url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/create";
    $u_url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/update/id=";
    $htm   = '<select name="item_id" class="form_select form_select_item_jumper" onchange="var iid=this.options[this.selectedIndex].value;if(iid == \'create\'){self.location=\'' . $c_url . '\'} else {self.location=\'' . $u_url . '\'+ iid}">' . "\n";
    $htm .= '<option value="create"> ' . $_lang['jrCore'][50] . '</option>' . "\n";

    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
        $_opts = array();
        foreach ($_rt['_items'] as $_v) {
            $_opts["{$_v['_item_id']}"] = "{$_v['event_title']} " . jrCore_format_time($_v['event_date'], true);
        }
        foreach ($_opts as $item_id => $display) {
            if (isset($_post['id']) && $item_id == $_post['id']) {
                $htm .= '<option value="' . $item_id . '" selected="selected"> ' . $display . '</option>' . "\n";
            }
            else {
                $htm .= '<option value="' . $item_id . '"> ' . $display . '</option>' . "\n";
            }
        }
        unset($_rt, $_opts);
    }
    else {
        return '';
    }
    $htm .= '</select>';
    return $htm;
}

//---------------------------------------------------------
// calendar building functions
// adapted from apache licensed http://php-calendar.org/
//---------------------------------------------------------

/**
 * Creates a display for a particular month to be embedded in a full view
 * @param int $month
 * @param int $year
 * @return array
 */
function jrEvent_create_month($month, $year)
{
    $wim = jrEvent_weeks_in_month($month, $year);

    $month_table = array();
    for ($week_of_month = 1; $week_of_month <= $wim; $week_of_month++) {
        $month_table[] = jrEvent_create_week($week_of_month, $month, $year);
    }

    return $month_table;
}

/**
 * Creates a display for a particular week to be embedded in a month table
 * @param int $week_of_month
 * @param int $month
 * @param int $year
 * @return array
 */
function jrEvent_create_week($week_of_month, $month, $year)
{
    $start_day    = 1 + ($week_of_month - 1) * 7 - jrEvent_day_of_week($month, 1, $year);
    $week_of_year = (int) jrEvent_week_of_year($month, $start_day, $year);
    $week_html    = array();

    for ($day_of_week = 0; $day_of_week < 7; $day_of_week++) {
        $day                        = $start_day + $day_of_week;
        $week_html[$week_of_year][] = jrEvent_create_day($month, $day, $year);
    }

    return $week_html;
}

/**
 * displays the day of the week and the following days of the week
 * @param int $month
 * @param int $day
 * @param int $year
 * @return array
 */
function jrEvent_create_day($month, $day, $year)
{
    $date_class = 'ecal-date';
    $rel        = 'this_month';
    if ($day <= 0) {
        $month--;
        if ($month < 1) {
            $month = 12;
            $year--;
        }
        $day += jrEvent_days_in_month($month, $year);
        $date_class .= ' ecal-shadow';
        $rel = 'last_month';
    }
    elseif ($day > jrEvent_days_in_month($month, $year)) {
        $day -= jrEvent_days_in_month($month, $year);
        $date_class .= ' ecal-shadow';
        $rel = 'next_month';
    }
    else {
        $currentday   = date('j');
        $currentmonth = date('n');
        $currentyear  = date('Y');

        // set whether the date is current date
        if ($currentyear == $year && $currentmonth == $month && $currentday == $day) {
            $date_class .= ' ecal-today';
        }
    }

    $html_day = array(
        'rel'   => $rel,
        'class' => $date_class,
        'day'   => $day
    );

    return $html_day;
}

/**
 * Returns the number of weeks in $month
 * @param int $month
 * @param int $year
 * @return float|int
 */
function jrEvent_weeks_in_month($month, $year)
{
    $days = jrEvent_days_in_month($month, $year);

    // days not in this month in the partial weeks
    $days_before_month = jrEvent_day_of_week($month, 1, $year);
    $days_after_month  = 6 - jrEvent_day_of_week($month, $days, $year);

    // add up the days in the month and the outliers in the partial weeks
    // divide by 7 for the weeks in the month
    return ($days_before_month + $days + $days_after_month) / 7;
}

/**
 * Returns number of days in month
 * @param int $month
 * @param int $year
 * @return false|string
 */
function jrEvent_days_in_month($month, $year)
{
    return date('t', mktime(0, 0, 0, $month, 1, $year));
}

/**
 * return the week number corresponding to the $day.
 * @param int $month
 * @param int $day
 * @param int $year
 * @return float|int
 */
function jrEvent_week_of_year($month, $day, $year)
{
    $timestamp = mktime(0, 0, 0, $month, $day, $year);

    // week_start = 1 uses ISO 8601 and contains the Jan 4th,
    //   Most other places the first week contains Jan 1st
    //   There are a few outliers that start weeks on Monday and use
    //   Jan 1st for the first week. We'll ignore them for now.
    if (jrEvent_day_of_week_start() == 1) {
        $year_contains = 4;
        // if the week is in December and contains Jan 4th, it's a week
        // from next year
        if ($month == 12 && $day - 24 >= $year_contains) {
            $year++;
            $month = 1;
            $day -= 31;
        }
    }
    else {
        $year_contains = 1;
    }

    // $day is the first day of the week relative to the current month,
    // so it can be negative. If it's in the previous year, we want to use
    // that negative value, unless the week is also in the previous year,
    // then we want to switch to using that year.
    if ($day < 1 && $month == 1 && $day > $year_contains - 7) {
        $day_of_year = $day - 1;
    }
    else {
        $day_of_year = date('z', $timestamp);
        $year        = date('Y', $timestamp);
    }

    /* Days in the week before Jan 1. */
    $days_before_year = jrEvent_day_of_week(1, $year_contains, $year);

    // Days left in the week
    $days_left = 8 - jrEvent_day_of_week_ts($timestamp) - $year_contains;

    /* find the number of weeks by adding the days in the week before
     * the start of the year, days up to $day, and the days left in
     * this week, then divide by 7 */
    return ($days_before_year + $day_of_year + $days_left) / 7;
}

/**
 * returns the number of days in the week taking into account whether we start on sunday or monday
 * @param int $month
 * @param int $day
 * @param int $year
 * @return int
 */
function jrEvent_day_of_week($month, $day, $year)
{
    return jrEvent_day_of_week_ts(mktime(0, 0, 0, $month, $day, $year));
}

/**
 * returns the number of days in the week taking into account whether we start on sunday or monday
 * @param int $timestamp
 * @return int
 */
function jrEvent_day_of_week_ts($timestamp)
{
    $days = date('w', $timestamp);
    return ($days + 7 - jrEvent_day_of_week_start()) % 7;
}

/**
 * Get number for first day of week
 * @return int
 */
function jrEvent_day_of_week_start()
{
    global $_conf;
    return (jrCore_checktype($_conf['jrEvent_calendar_start_day'], 'number_nn')) ? $_conf['jrEvent_calendar_start_day'] : 0;
}

/**
 * Returns an array of the range of site event years
 * @return array
 */
function jrEvent_get_year_range()
{
    $_s  = array(
        "order_by"      => array("event_date" => "numerical_asc"),
        "limit"         => 1,
        "skip_triggers" => true
    );
    $_xt = jrCore_db_search_items('jrEvent', $_s);
    if ($_xt && is_array($_xt['_items'][0]) && jrCore_checktype($_xt['_items'][0]['event_date'], 'number_nn')) {
        $fey = date('Y', $_xt['_items'][0]['event_date']);
        $_s  = array(
            "order_by"      => array("event_date" => "numerical_desc"),
            "limit"         => 1,
            "skip_triggers" => true
        );
        $_xt = jrCore_db_search_items('jrEvent', $_s);
        if ($_xt && is_array($_xt['_items'][0]) && jrCore_checktype($_xt['_items'][0]['event_date'], 'number_nn')) {
            $ley = date('Y', $_xt['_items'][0]['event_date']);
        }
        else {
            $ley = $fey;
        }
        $_years = array();
        for ($i = $fey; $i <= $ley; $i++) {
            $_years["{$i}"] = $i;
        }
        return $_years;
    }
    else {
        return array(date('Y') => date('Y'));
    }
}

/**
 * Create a list of dates based on passed in parameters
 * @param array $_rt
 * @return array
 */
function jrEvent_get_event_dates($_rt)
{
    // Build an array of all the event dates
    $_event_dates = array();
    if (!isset($_rt['event_recurring']) || $_rt['event_recurring'] == 'no') {

        // Not a recurring event
        $_event_dates[] = $_rt['event_date'];

    }

    elseif (isset($_rt['event_recurring']) && is_numeric(substr($_rt['event_recurring'], 0, 1)) && substr($_rt['event_recurring'], 2, 3) != '') {

        // Recurring on a specific 'nth' day of the month
        $index = substr($_rt['event_recurring'], 0, 1);
        $day   = substr($_rt['event_recurring'], 2, 3);
        if ($index == 1) {
            $index = 'first';
        }
        elseif ($index == 2) {
            $index = 'second';
        }
        elseif ($index == 3) {
            $index = 'third';
        }
        elseif ($index == 4) {
            $index = 'fourth';
        }
        elseif ($index == 5) {
            $index = 'last';
        }
        if ($day == 'sun') {
            $day = 'Sunday';
        }
        elseif ($day == 'mon') {
            $day = 'Monday';
        }
        elseif ($day == 'tue') {
            $day = 'Tuesday';
        }
        elseif ($day == 'wed') {
            $day = 'Wednesday';
        }
        elseif ($day == 'thu') {
            $day = 'Thursday';
        }
        elseif ($day == 'fri') {
            $day = 'Friday';
        }
        elseif ($day == 'sat') {
            $day = 'Saturday';
        }
        $year  = date('Y', $_rt['event_date']);
        $start = date('Y', $_rt['event_date']) . date('m', $_rt['event_date']);
        $end   = date('Y', $_rt['event_end_date']) . date('m', $_rt['event_end_date']);
        while ($start <= $end) {
            $month = substr($start, 4, 2);
            if ($month == '01') {
                $month = 'January';
            }
            elseif ($month == '02') {
                $month = 'February';
            }
            elseif ($month == '03') {
                $month = 'March';
            }
            elseif ($month == '04') {
                $month = 'April';
            }
            elseif ($month == '05') {
                $month = 'May';
            }
            elseif ($month == '06') {
                $month = 'June';
            }
            elseif ($month == '07') {
                $month = 'July';
            }
            elseif ($month == '08') {
                $month = 'August';
            }
            elseif ($month == '09') {
                $month = 'September';
            }
            elseif ($month == '10') {
                $month = 'October';
            }
            elseif ($month == '11') {
                $month = 'November';
            }
            elseif ($month == '12') {
                $month = 'December';
            }
            $time           = (date('G', $_rt['event_date']) * 3600) + (date('i', $_rt['event_date']) * 60);
            $_event_dates[] = strtotime("{$index} {$day} of {$month} {$year}") + $time;
            $start++;
            if (substr($start, 4, 2) == '13') {
                $year++;
                $start = $year . '01';
            }
        }
    }
    else {
        $tzn = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $time              = (date('G', $_rt['event_date']) * 3600) + (date('i', $_rt['event_date']) * 60);
        $_rt['event_date'] = mktime(0, 0, 0, date("n", $_rt['event_date']), date("j", $_rt['event_date']), date("Y", $_rt['event_date']));

        while ($_rt['event_date'] <= $_rt['event_end_date']) {
            if (isset($_rt['event_recurring']) && $_rt['event_recurring'] == 'daily') {

                // Daily recurring events
                $_event_dates[] = $_rt['event_date'] + $time;
                // Fail safe - max 2 years worth of daily recurring events
                if (count($_event_dates) >= 730) {
                    break;
                }
            }
            elseif (isset($_rt['event_recurring']) && $_rt['event_recurring'] == strtolower(substr(date('l', $_rt['event_date']), 0, 3))) {

                // Weekly recurring events on specified day
                $_event_dates[] = $_rt['event_date'] + $time;
                // Fail safe - max 5 years worth of weekly recurring events
                if (count($_event_dates) >= 260) {
                    break;
                }
            }
            elseif (isset($_rt['event_recurring']) && is_numeric($_rt['event_recurring']) && $_rt['event_recurring'] == date('j', $_rt['event_date'])) {

                // Recurring event on specific day of month
                $_event_dates[] = $_rt['event_date'] + $time;
                // Fail safe - max 10 years worth of monthly recurring events
                if (count($_event_dates) >= 120) {
                    break;
                }
            }
            $_rt['event_date'] += 86400;
        }
        date_default_timezone_set($tzn);
    }
    return $_event_dates;
}
