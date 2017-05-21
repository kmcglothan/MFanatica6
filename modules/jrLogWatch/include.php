<?php
/**
 * Jamroom Activity Log Watcher module
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
 * jrLogWatch_meta
 */
function jrLogWatch_meta()
{
    $_tmp = array(
        'name'        => 'Activity Log Watcher',
        'url'         => 'logwatch',
        'version'     => '1.2.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Setup notification events based on entries in the Activity Log',
        'category'    => 'tools',
        'license'     => 'mpl',
        'priority'    => 250
    );
    return $_tmp;
}

/**
 * jrLogWatch_init
 */
function jrLogWatch_init()
{
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrLogWatch', 'browse', array('rule browser', 'Browse, Create, Update, and Delete LogWatch rules'));
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrLogWatch', 'browse', 'LogWatch Rule Browser');

    // Listeners
    jrCore_register_event_listener('jrCore', 'log_message', 'jrLogWatch_log_message_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrLogWatch_verify_module_listener');

    // User Notifications
    $_tmp = array(
        'label' => 'Log Watch Matches',
        'help'  => 'When a match is made on a Log Watch rule that emails masters or admins, how do you want to be notified?',
        'group' => 'admin'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrLogWatch', 'notify', $_tmp);
    return true;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Simple key => replacements for output
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrLogWatch_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_conf['jrLogWatch_ruleset']) && strlen($_conf['jrLogWatch_ruleset']) > 1) {
        // We have not been converted
        $_rules = json_decode($_conf['jrLogWatch_ruleset'], true);
        if ($_rules && is_array($_rules)) {
            $_rl = array();
            foreach ($_rules as $_r) {
                $name   = jrCore_db_escape($_r['t']);
                $match  = jrCore_db_escape($_r['m']);
                $action = jrCore_db_escape($_r['a']);
                $active = ($_r['o'] == 'on') ? 1 : 0;
                $_rl[] = "('{$name}','{$match}','{$action}',3600,'{$active}')";
            }
            if (count($_rl) > 0) {
                $tbl = jrCore_db_table_name('jrLogWatch', 'rule');
                $req = "INSERT INTO {$tbl} (rule_name, rule_match, rule_action, rule_delay, rule_active) VALUES " . implode(',', $_rl);
                $cnt = jrCore_db_query($req, 'COUNT');
                if ($cnt && $cnt > 0) {
                    jrCore_logger('INF', "successfully converted {$cnt} log watch rules to new database format");
                }
            }
        }
        jrCore_delete_setting('jrLogWatch', 'ruleset');
    }
    return $_data;
}

/**
 * Watch Log messages
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrLogWatch_log_message_listener($_data, $_user, $_conf, $_args, $event)
{
    if (!isset($_conf['jrLogWatch_active']) || $_conf['jrLogWatch_active'] !== 'on') {
        // Whole thing is turned off
        return $_data;
    }
    if (!$_rs = jrCore_get_flag('jrLogWatch_rule_set')) {
        $tbl = jrCore_db_table_name('jrLogWatch', 'rule');
        $req = "SELECT * FROM {$tbl} WHERE rule_active = 1 AND (rule_delay = 0 OR (rule_notice < (UNIX_TIMESTAMP() - rule_delay)))";
        $_rs = jrCore_db_query($req, 'NUMERIC', false, null, false);
        if ($_rs && is_array($_rs)) {
            jrCore_set_flag('jrLogWatch_rule_set', $_rs);
        }
        else {
            jrCore_set_flag('jrLogWatch_rule_set', 'no_rules');
        }
    }
    if (is_array($_rs)) {
        jrLogWatch_match($_data['priority'], $_data['message'], $_rs);
    }
    return $_data;
}

//---------------------------------------------------------
// FUNCTIONS
//---------------------------------------------------------

/**
 * Create a new LogWatch rule
 * @param string $name display name for rule
 * @param string $match matching string or regular expression
 * @param int $priority activity log priority DBG|INF|MIN|MAJ|CRI
 * @param string $action action to perform
 * @param int $delay frequency of notification (seconds)
 * @param int $active 1|0 rule active/inactive
 * @param bool $unique set to TRUE to check if matching string or regular expression already exists on a rule
 * @return bool|int|mixed
 */
function jrLogWatch_create_rule($name, $match, $priority, $action, $delay = 3600, $active = 1, $unique = false)
{
    $mtc = jrCore_db_escape($match);
    $tbl = jrCore_db_table_name('jrLogWatch', 'rule');
    // Are we checking to be sure rule match is unique?
    if ($unique) {
        $req = "SELECT * FROM {$tbl} WHERE rule_match = '{$mtc}' LIMIT 1";
        $_ex = jrCore_db_query($req, 'SINGLE');
        if ($_ex && is_array($_ex)) {
            return 0;
        }
    }
    $nam = jrCore_db_escape($name);
    $acn = jrCore_db_escape($action);
    $pri = jrCore_db_escape(strtolower($priority));
    $act = ($active === 1) ? 1 : 0;
    $dly = (int) $delay;
    $req = "INSERT INTO {$tbl} (rule_name, rule_pri, rule_match, rule_action, rule_delay, rule_active) VALUES ('{$nam}', '{$pri}', '{$mtc}', '{$acn}', '{$dly}', '{$act}')";
    $rid = jrCore_db_query($req, 'INSERT_ID');
    if ($rid && $rid > 0) {
        return $rid;
    }
    return false;
}

/**
 * Scan an Activity Log for rule matches and perform actions
 * @param $priority string Activity Log Message priority
 * @param $message string Activity Log Message
 * @param $_rules array Rule Set
 * @param $test bool Set to true for Test Mode (just record actions that would have happened)
 * @return mixed
 */
function jrLogWatch_match($priority, $message, $_rules, $test = false)
{
    $_upd = array();
    $_out = array();
    foreach ($_rules as $_r) {

        if ($_r['rule_pri'] != 'all' && $_r['rule_pri'] != $priority) {
            // We don't match the priority
            continue;
        }

        $notify = false;
        $match  = false;
        if (strpos($_r['rule_match'], '/') === 0 || strpos($_r['rule_match'], ',') === 0) {
            // We have a regular expression...
            if (preg_match($_r['rule_match'], $message) === 1) {
                $match = true;
            }
        }
        elseif (stripos(' '. $message, $_r['rule_match'])) {
            $match = true;
        }

        if ($match) {

            // See if we are in test mode
            if ($test) {
                $_out[] = array(
                    'matched'  => $_r['rule_id'],
                    'priority' => $priority,
                    'message'  => $message
                );
                continue;
            }

            if (jrCore_checktype($_r['rule_action'], 'url')) {
                $_data = array(
                    'priority' => $priority,
                    'message'  => $message
                );
                jrCore_load_url($_r['rule_action'], $_data, 'POST');
                $notify = true;
            }
            elseif (jrCore_checktype($_r['rule_action'], 'email')) {
                $_rp = array(
                    'priority'        => $priority,
                    'message'         => $message,
                    'rule_name'       => $_r['rule_name'],
                    'rule_expression' => $_r['rule_match'],
                    'rule_action'     => $_r['rule_action']
                );
                list($sub, $msg) = jrCore_parse_email_templates('jrLogWatch', 'matched', $_rp);
                jrCore_send_email($_r['rule_action'], $sub, $msg);
                $notify = true;
            }
            elseif ($_r['rule_action'] == 'masters') {
                $_sp = array(
                    'search'         => array(
                        "user_group = master",
                    ),
                    'limit'          => 100,
                    'return_keys'    => array('_user_id', 'user_email'),
                    'skip_triggers'  => true,
                    'privacy_check'  => false,
                    'ignore_pending' => true
                );
                $_rt = jrCore_db_search_items('jrUser', $_sp);
                if ($_rt && is_array($_rt['_items'])) {
                    $_rp = array(
                        'priority'        => $priority,
                        'message'         => $message,
                        'rule_name'       => $_r['rule_name'],
                        'rule_expression' => $_r['rule_match'],
                        'rule_action'     => $_r['rule_action']
                    );
                    list($sub, $msg) = jrCore_parse_email_templates('jrLogWatch', 'matched', $_rp);
                    foreach ($_rt['_items'] as $_v) {
                        jrUser_notify($_v['_user_id'], 0, 'jrLogWatch', 'notify', $sub, $msg);
                    }
                }
                $notify = true;
            }
            elseif ($_r['rule_action'] == 'admins') {
                $_sp = array(
                    'search'         => array(
                        "user_group in master,admin",
                    ),
                    'limit'          => 100,
                    'return_keys'    => array('_user_id', 'user_email'),
                    'skip_triggers'  => true,
                    'privacy_check'  => false,
                    'ignore_pending' => true
                );
                $_rt = jrCore_db_search_items('jrUser', $_sp);
                if ($_rt && is_array($_rt['_items'])) {
                    $_rp = array(
                        'priority'        => $priority,
                        'message'         => $message,
                        'rule_name'       => $_r['rule_name'],
                        'rule_expression' => $_r['rule_match'],
                        'rule_action'     => $_r['rule_action']
                    );
                    list($sub, $msg) = jrCore_parse_email_templates('jrLogWatch', 'matched', $_rp);
                    foreach ($_rt['_items'] as $_v) {
                        jrUser_notify($_v['_user_id'], 0, 'jrLogWatch', 'notify', $sub, $msg);
                    }
                }
                $notify = true;
            }
            else {
                $_data = array(
                    'rule'     => $_r,
                    'prioirty' => $priority,
                    'message'  => $message
                );
                jrCore_logger('CRI', "LogWatch: invalid rule action for {$_r['rule_name']} rule - verify config", $_data);
            }
            if ($notify && $_r['rule_delay'] > 0) {
                $_upd[] = $_r['rule_id'];
            }
        }
    }
    if (count($_upd) > 0) {
        // Update notice time
        $tbl = jrCore_db_table_name('jrLogWatch', 'rule');
        $req = "UPDATE {$tbl} SET rule_notice = UNIX_TIMESTAMP() WHERE rule_id IN(" . implode(',', $_upd) .')';
        jrCore_db_query($req, null, false, null, false);
    }
    if ($test) {
        if (count($_out)  > 0) {
            return $_out;
        }
        return false;
    }
    return true;
}

