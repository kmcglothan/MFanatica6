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
 * @package Extras
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Returns TRUE if current view is a MAGIC view
 * @return bool
 */
function jrCore_is_magic_view()
{
    return (jrCore_get_flag('jrcore_is_magic_view')) ? true : false;
}

/**
 * Check if a user is part of a specific user group
 * @param $group mixed user group OR quota ID OR comma list of group/quota_id
 * @param $_usr array User information to check (default is viewing user)
 * @return bool
 */
function jrCore_user_is_part_of_group($group, $_usr = null)
{
    foreach (explode(',', $group) as $grp) {
        $grp = trim($grp);
        switch ($grp) {
            case 'all':
                return true;
                break;
            case 'master':
                if (jrUser_is_master()) {
                    return true;
                }
                break;
            case 'admin':
                if (jrUser_is_admin()) {
                    return true;
                }
                break;
            case 'power':
            case 'power_user':
                if (jrUser_is_power_user()) {
                    return true;
                }
                break;
            case 'multi':
            case 'multi_user':
                if (jrUser_is_multi_user()) {
                    return true;
                }
                break;
            case 'user':
                if (jrUser_is_logged_in()) {
                    return true;
                }
                break;
            case 'visitor':
                if (!jrUser_is_logged_in()) {
                    return true;
                }
                break;
            default:
                if (jrCore_checktype($grp, 'number_nz')) {
                    if (is_null($_usr)) {
                        $gid = jrUser_get_profile_home_key('profile_quota_id');
                        if ($gid && $gid == $grp) {
                            return true;
                        }
                    }
                    else {
                        if (isset($_usr['profile_quota_id']) && $_usr['profile_quota_id'] == $grp) {
                            return true;
                        }
                    }
                }
                break;
        }
    }
    return false;
}

/**
 * Save a URL to the user's breadcrumb stack
 * @param string $url URL to save to history
 * @return bool
 */
function jrCore_save_url_history($url = null)
{
    if (!isset($_SESSION['jrcore_url_history'])) {
        $_SESSION['jrcore_url_history'] = array();
    }
    $cur = jrCore_get_current_url();
    if (isset($_SESSION['jrcore_url_history'][$cur])) {
        // Keep it to a good size
        $num = count($_SESSION['jrcore_url_history']);
        if ($num > 30) {
            $_SESSION['jrcore_url_history'] = array_slice($_SESSION['jrcore_url_history'], -30);
        }
        // If we have already been set, we need to cut everything AFTER
        // this entry in our history (if any) so if we come in from a
        // different direction it can be set
        $found = false;
        foreach ($_SESSION['jrcore_url_history'] as $k => $v) {
            if (!$found) {
                if ($k == $cur) {
                    $found = true;
                }
            }
            else {
                unset($_SESSION['jrcore_url_history'][$k]);
            }
        }
        return (isset($_SESSION['jrcore_url_history'][$cur])) ? $_SESSION['jrcore_url_history'][$cur] : $cur;
    }
    if (is_null($url) || strlen($url) === 0) {
        $url = jrCore_get_local_referrer();
    }
    $_SESSION['jrcore_url_history'][$cur] = $url;
    return $url;
}

/**
 * Get the last URL from the user's breadcrumb stack
 * @param string $url URL to return if no history is set
 * @return string
 */
function jrCore_get_last_history_url($url = 'referrer')
{
    if (!isset($_SESSION['jrcore_url_history'])) {
        if ($url == 'referrer') {
            $url = jrCore_get_local_referrer();
        }
        return $url;
    }
    $cur = jrCore_get_current_url();
    $url = $_SESSION['jrcore_url_history'][$cur];
    return $url;
}

/**
 * Return all registered event listeners for a given event name
 * @param string $module Module that registered event
 * @param string $event Name of the event
 * @return mixed
 */
function jrCore_get_event_listeners($module, $event)
{
    $_tmp  = jrCore_get_flag('jrcore_event_listeners');
    $event = "{$module}_{$event}";
    if (!$_tmp || (!isset($_tmp[$event]) && !isset($_tmp[$event]) && !isset($_tmp['jrCore_all_events']))) {
        // No one is listening for this event
        return false;
    }
    return $_tmp[$event];
}

/**
 * Trigger a module event
 *
 * The jrCore_trigger_event is used by a module to tell the Core
 * that it is running an "action".  Other modules can listen for
 * this action, and can execute code in response to the action.
 *
 * @param string $module broadcasting module name
 * @param string $event Action that listening modules will receive
 * @param array $_data information passed to the listening event function to be modified
 * @param array $_args additional info pertaining to the event (non modifiable)
 * @param mixed $only_listener Set to a specific module (or array of modules) to broadcast to only those modules
 * @param bool $active_check set to FALSE to disable check for active module
 * @return mixed
 */
function jrCore_trigger_event($module, $event, $_data = null, $_args = null, $only_listener = false, $active_check = true)
{
    global $_conf, $_user;

    // Our event name
    $ename = "{$module}_{$event}";
    $mname = "{$module}_all_events";

    // See if we have any listeners for this event...
    // We do not use jrCore_get_event_listeners() here since we need access to ALL events
    $_tmp = jrCore_get_flag('jrcore_event_listeners');
    if (!$_tmp || (!isset($_tmp[$ename]) && !isset($_tmp[$mname]) && !isset($_tmp['jrCore_all_events']))) {
        // No one is listening for this event
        return $_data;
    }

    // See if we are sending to specific modules only
    if ($only_listener && is_string($only_listener)) {
        $only_listener = array($only_listener);
    }
    if ($only_listener) {
        $only_listener = array_flip($only_listener);
    }

    // Our data MUST come in as an array, or it will cause issues for
    // the listeners - check for it here
    if (is_null($_data) || $_data === false) {
        if (jrCore_is_developer_mode()) {
            jrCore_logger('DBG', "invalid _data array received for event: {$module}/{$event}", $_data);
        }
        $_data = array();
    }

    // Check for recursion - this prevents a module trigger from creating another
    // trigger call to the same module/action resulting in a recursive loop
    $key = md5("{$ename}_" . json_encode($_data));
    $tmp = jrCore_get_flag("jr_module_trigger_event_active_{$key}");
    if ($tmp) {
        // We have recursion...
        jrCore_logger('CRI', "recursive module trigger detected for {$module}/{$event}", array('event' => $event, 'module' => $module, '_data' => $_data, '_args' => $_args));
        jrCore_notice('Error', 'an internal trigger error has occurred - please try again');
    }
    jrCore_set_flag("jr_module_trigger_event_active_{$key}", 1);

    // Make sure module is part of $_args
    if (is_null($_args) || !is_array($_args) || $_args === false) {
        if (!is_null($_args) && $_args !== false) {
            jrCore_logger('DBG', "invalid _args array received for event: {$module}/{$event}", array('_args' => $_args));
        }
        $_args = array();
    }
    if (!isset($_args['module'])) {
        $_args['module'] = $module;
    }
    $_args['jrcore_unique_trigger_id'] = $key;

    // Set our active args
    jrCore_set_active_trigger_args($_args);

    // We can register 1 of 3 events:
    // a specific event from a specific module - i.e. 'jrUser','get_info_by_id' - $ename will be 'jrUser_get_info_by_id'
    // all events from a specific module - i.e. 'jrUser','all_events' - $ename will be 'jrUser_all_events'
    // all events for the whole system - i.e. 'all','all_events' - $ename will be 'all_all_events'

    // Start with specific event
    if (isset($_tmp[$ename]) && is_array($_tmp[$ename])) {
        foreach ($_tmp[$ename] as $func) {
            $lmod = substr($func, 0, strpos($func, '_'));
            if ($active_check && $lmod != $_conf['jrCore_active_skin'] && !jrCore_module_is_active($lmod)) {
                continue;
            }
            // See if we are only doing specific listeners
            if ($only_listener) {
                if (!isset($only_listener[$lmod])) {
                    continue;
                }
            }
            if (function_exists($func)) {
                $start = microtime();
                $_temp = $func($_data, $_user, $_conf, jrCore_get_flag("jrcore_active_trigger_args_{$key}"), $event);
                jrCore_record_triggered_event($module, $event, $lmod, $func, $start);
                if (!empty($_temp)) {
                    $_data = $_temp;
                }
            }
            else {
                jrCore_logger('CRI', "jrCore_trigger_event: defined event listener function does not exist: {$func}");
                jrCore_delete_flag("jr_module_trigger_event_active_{$key}");
                return $_data;
            }
        }
    }

    // all events for given module
    if (isset($_tmp[$mname]) && is_array($_tmp[$mname])) {
        foreach ($_tmp[$mname] as $func) {
            $lmod = substr($func, 0, strpos($func, '_'));
            if ($active_check && $lmod != $_conf['jrCore_active_skin'] && !jrCore_module_is_active($lmod)) {
                continue;
            }
            // See if we are only doing specific listeners
            if ($only_listener) {
                if (!isset($only_listener[$lmod])) {
                    continue;
                }
            }
            if (function_exists($func)) {
                $start = microtime();
                $_temp = $func($_data, $_user, $_conf, jrCore_get_flag("jrcore_active_trigger_args_{$key}"), $event);
                jrCore_record_triggered_event($module, $event, $lmod, $func, $start);
                if (!empty($_temp)) {
                    $_data = $_temp;
                }
            }
            else {
                jrCore_logger('CRI', "jrCore_trigger_event: defined event listener function does not exist: {$func}");
                jrCore_delete_flag("jr_module_trigger_event_active_{$key}");
                return $_data;
            }
        }
    }
    // all events
    if (isset($_tmp['jrCore_all_events']) && is_array($_tmp['jrCore_all_events'])) {
        foreach ($_tmp['jrCore_all_events'] as $func) {
            $lmod = substr($func, 0, strpos($func, '_'));
            if ($active_check && $lmod != $_conf['jrCore_active_skin'] && !jrCore_module_is_active($lmod)) {
                continue;
            }
            // See if we are only doing specific listeners
            if ($only_listener) {
                if (!isset($only_listener[$lmod])) {
                    continue;
                }
            }
            if (function_exists($func)) {
                $start = microtime();
                $_temp = $func($_data, $_user, $_conf, jrCore_get_flag("jrcore_active_trigger_args_{$key}"), $event);
                jrCore_record_triggered_event($module, $event, $lmod, $func, $start);
                if (!empty($_temp)) {
                    $_data = $_temp;
                }
            }
            else {
                jrCore_logger('CRI', "jrCore_trigger_event: defined event listener function does not exist: {$func}");
                jrCore_delete_flag("jr_module_trigger_event_active_{$key}");
                return $_data;
            }
        }
    }
    jrCore_delete_flag("jr_module_trigger_event_active_{$key}");
    return $_data;
}

/**
 * Set the "active" trigger args
 * @param $_args array
 * @return bool
 */
function jrCore_set_active_trigger_args($_args)
{
    // Save start time for triggered event
    if (is_array($_args) && isset($_args['jrcore_unique_trigger_id'])) {
        return jrCore_set_flag("jrcore_active_trigger_args_{$_args['jrcore_unique_trigger_id']}", $_args);
    }
    return false;
}

/**
 * Record a triggered event to the event stack
 * @param string $trigger_module
 * @param string $event
 * @param string $listener_module
 * @param string $function
 * @param float $start_time
 * @return bool
 */
function jrCore_record_triggered_event($trigger_module, $event, $listener_module, $function, $start_time)
{
    // Are we saving triggered events?
    if (jrCore_get_flag('jrcore_save_triggered_events')) {

        // 0.81072800 1393853047
        $now = explode(' ', microtime());
        $now = $now[1] + $now[0];

        // Have we run before?
        $key = 'jrcore_triggered_events_start_time';
        $beg = jrCore_get_flag($key);
        if (!$beg) {
            jrCore_set_flag($key, $now);
            $elapsed = 0;
        }
        else {
            $elapsed = round(($now - $beg), 3);
        }
        $key = 'jrcore_triggered_events';
        if (!$_tm = jrCore_get_flag($key)) {
            $_tm = array();
        }
        $stt   = explode(' ', $start_time);
        $stt   = $stt[1] + $stt[0];
        $_tm[] = array(
            $trigger_module,
            $event,
            $listener_module,
            $function,
            'total: ' . $elapsed . ", function: " . round(($now - $stt), 3)
        );
        return jrCore_set_flag($key, $_tm);
    }
    return true;
}

/**
 * Get stack of triggered events
 * @return mixed
 */
function jrCore_get_triggered_events()
{
    if (jrCore_get_flag('jrcore_save_triggered_events')) {
        $key = 'jrcore_triggered_events';
        return jrCore_get_flag($key);
    }
    return 'error: jrCore_enable_triggered_events() not called - triggered events not saved';
}

/**
 * Set flag to enable saving of triggered events
 * @return mixed
 */
function jrCore_enable_triggered_events()
{
    return jrCore_set_flag('jrcore_save_triggered_events', 1);
}

/**
 * Set flag to disable triggered events
 * @return mixed
 */
function jrCore_disable_triggered_events()
{
    return jrCore_delete_flag('jrcore_save_triggered_events');
}

/**
 * Get custom form fields defined in the Form Designer for a view
 * @param string $module Module that has registered a designer form view
 * @param string $view View to get form fields for
 * @return mixed
 */
function jrCore_get_designer_form_fields($module, $view = null)
{
    $ckey = "jrcore_get_designer_form_fields_{$module}_{$view}";
    $_tmp = jrCore_get_flag($ckey);
    if ($_tmp) {
        if (is_array($_tmp) && count($_tmp) > 0) {
            return $_tmp;
        }
        return false;
    }
    $tbl = jrCore_db_table_name('jrCore', 'form');
    $mod = jrCore_db_escape($module);
    if (is_null($view) || strlen($view) === 0) {
        $req = "SELECT * FROM {$tbl} WHERE `module` = '{$mod}' ORDER by `order` ASC";
    }
    else {
        $opt = jrCore_db_escape($view);
        $req = "SELECT * FROM {$tbl} WHERE `module` = '{$mod}' AND `view` = '{$opt}' ORDER by `order` ASC";
    }
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (!isset($_rt) || !is_array($_rt) || count($_rt) === 0) {
        jrCore_set_flag($ckey, 1);
        return false;
    }
    $_out = array();
    foreach ($_rt as $_v) {
        $_out["{$_v['name']}"] = $_v;
    }
    jrCore_set_flag($ckey, $_out);
    return $_out;
}

/**
 * Verify a form field is setup in the Form Designer
 * @param string $module Module that has registered a designer form view
 * @param string $view View to get form fields for
 * @param array $_field Array of field information
 * @param bool $allow_hidden set to TRUE to allow a hidden field to be added
 * @return mixed Returns false on error, 1 on update and INSERT_ID on create
 */
function jrCore_verify_designer_form_field($module, $view, $_field, $allow_hidden = false)
{
    global $_user;
    if (!is_array($_field) || (isset($_field['form_designer']) && $_field['form_designer'] === false)) {
        return false;
    }
    // we MUST get a field name
    if (!isset($_field['name'])) {
        jrCore_logger('CRI', "field received without field name", array('module' => $module, 'view' => $view, '_field' => $_field));
        return false;
    }
    // We don't do hidden fields...
    if (!$allow_hidden && isset($_field['type']) && $_field['type'] == 'hidden') {
        return true;
    }
    // The "type" must be a valid form field
    $_fld = array();
    $_tmp = jrCore_get_registered_module_features('jrCore', 'form_field');
    foreach ($_tmp as $m => $_v) {
        foreach ($_v as $k => $v) {
            $_fld[$k] = $m;
        }
    }
    if (!isset($_fld["{$_field['type']}"])) {
        // Not a form field
        return true;
    }
    // Cleanup field options...
    if (isset($_field['options']) && is_array($_field['options'])) {
        $_field['options'] = json_encode($_field['options']);
    }

    // There are some fields that we do not override here
    unset($_field['module'], $_field['view'], $_field['created'], $_field['updated'], $_field['user']);
    // Create
    $_cm = jrCore_db_table_columns('jrCore', 'form');
    $tbl = jrCore_db_table_name('jrCore', 'form');
    $mod = jrCore_db_escape($module);
    $opt = jrCore_db_escape($view);
    $usr = (isset($_user['user_name']) && strlen($_user['user_name']) > 0) ? $_user['user_name'] : (isset($_user['user_email']) ? $_user['user_email'] : 'installer');
    $_tm = jrCore_get_designer_form_fields($module, $view);
    if (!isset($_tm["{$_field['name']}"])) {
        if (!isset($_field['locked'])) {
            $_field['locked'] = '1';
        }
        unset($_field['order']);
        $_cl = array();
        $_vl = array();
        foreach ($_field as $k => $v) {
            if (isset($_cm[$k])) {
                $_cl[] = "`{$k}`";
                switch ($k) {
                    case 'active':
                    case 'locked':
                    case 'required':
                        $_vl[] = intval($v);
                        break;
                    default:
                        $_vl[] = jrCore_db_escape($v);
                        break;
                }
            }
        }
        if (count($_cl) < 1) {
            return false;
        }
        // On insert we have to go in at the end of the form...
        $ord = 1;
        if (is_array($_tm)) {
            $ord = (count($_tm) + 1);
        }
        $req = "INSERT INTO {$tbl} (`module`,`view`,`created`,`updated`,`user`,`order`," . implode(',', $_cl) . ")
                VALUES ('{$mod}','{$opt}',UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),'{$usr}','{$ord}','" . implode("','", $_vl) . "')
                ON DUPLICATE KEY UPDATE `updated` = UNIX_TIMESTAMP()";
        $cnt = jrCore_db_query($req, 'COUNT');
        if ($cnt && $cnt === 1) {
            // If our 'jrcore_designer_create_custom_field' flag is set, it is
            // a field that is being created by the site admin.
            $crf = jrCore_get_flag('jrcore_designer_create_custom_field');
            if ($crf) {
                // NOTE: custom user lang keys are greater than 10000
                $tbl = jrCore_db_table_name('jrUser', 'language');
                $req = "SELECT (MAX(lang_key + 0) + 1) AS maxl FROM {$tbl} WHERE lang_module = '{$mod}'";
                $_nk = jrCore_db_query($req, 'SINGLE');
                $num = (isset($_nk['maxl'])) ? (int) $_nk['maxl'] : 1;
                if ($num < 10000) {
                    // This is our first custom entry for this module
                    $num = 10001;
                }
                $_done = array();
                $_todo = array(
                    'label'    => jrCore_db_escape("{$_field['name']} label *change this*"),
                    'sublabel' => '',
                    'help'     => jrCore_db_escape("{$_field['name']} help *change this*")
                );
                // Get support languages...
                $req = "SELECT lang_code, lang_ltr FROM {$tbl} GROUP BY lang_code";
                $_lc = jrCore_db_query($req, 'lang_code', false, 'lang_ltr');
                if ($_lc && is_array($_lc)) {
                    $req = "INSERT INTO {$tbl} (lang_module,lang_code,lang_charset,lang_ltr,lang_key,lang_text,lang_default) VALUES\n";
                    foreach ($_lc as $code => $ltr) {
                        foreach ($_todo as $str => $val) {
                            if (!isset($_done[$str])) {
                                $_done[$str] = $num++;
                            }
                            $req .= "('{$mod}','{$code}','utf-8','{$ltr}','" . $_done[$str] . "','{$val}','{$val}'),";
                        }
                    }
                }
                $req = substr($req, 0, strlen($req) - 1);
                $cnt = jrCore_db_query($req, 'COUNT');
                if (isset($cnt) && $cnt > 0) {
                    // Go back in and update our new form entry with the proper lang entries
                    $tbl = jrCore_db_table_name('jrCore', 'form');
                    $req = "UPDATE {$tbl} SET `label` = '" . intval($_done['label']) . "', `sublabel` = '" . intval($_done['sublabel']) . "', `help` = '" . intval($_done['help']) . "'
                             WHERE `module` = '{$mod}' AND `view` = '{$opt}' AND `name` = '" . jrCore_db_escape($_field['name']) . "'";
                    $cnt = jrCore_db_query($req, 'COUNT');
                }
            }
        }
        if (isset($cnt) && $cnt > 0) {
            // Reset designer form field key so it contains the new field
            $ckey = "jrcore_get_designer_form_fields_{$module}_{$view}";
            jrCore_delete_flag($ckey);
            return true;
        }
    }
    // Update
    else {
        // We can't change 'locked' status on update
        unset($_field['locked']);
        $req = "UPDATE {$tbl} SET `updated` = UNIX_TIMESTAMP(), `user` = '{$usr}', ";
        foreach ($_field as $k => $v) {
            if (isset($_cm[$k])) {
                switch ($k) {
                    case 'required':
                    case 'active':
                        if (isset($v) && jrCore_checktype($v, 'number_nn')) {
                            $req .= "`{$k}` = " . intval($v) . ',';
                        }
                        break;
                    case 'min':
                    case 'max':
                        if (isset($v) && jrCore_checktype($v, 'number_nz')) {
                            $req .= "`{$k}` = " . intval($v) . ',';
                        }
                        else {
                            $req .= "`{$k}` = 0,";
                        }
                        break;
                    default:
                        $req .= "`{$k}` = '" . jrCore_db_escape($_field[$k]) . "',";
                        break;
                }
            }
        }
        if (!isset($req) || !strpos($req, '=')) {
            return false;
        }
        $req = substr($req, 0, strlen($req) - 1) . " WHERE `module` = '{$mod}' AND `view` = '{$opt}' AND `name` = '" . jrCore_db_escape($_field['name']) . "' LIMIT 1";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (isset($cnt) && $cnt === 1) {
            return true;
        }
    }
    return false;
}

/**
 * Set the order of fields in a Designer Form
 * @param string $module Module that has registered a designer form view
 * @param string $view View to get form fields for
 * @param string $field Field Name to set specific order for
 * @param int $order Order Value to Set for $field
 * @return bool
 */
function jrCore_set_form_designer_field_order($module, $view, $field = null, $order = 1)
{
    $_rt = jrCore_get_designer_form_fields($module, $view);
    if (!isset($_rt) || !is_array($_rt) || count($_rt) === 0) {
        // NO designer fields
        return true;
    }
    $tbl = jrCore_db_table_name('jrCore', 'form');
    $mod = jrCore_db_escape($module);
    $opt = jrCore_db_escape($view);
    $ord = 1;
    if (isset($field) && strlen($field) > 0) {
        $fld = jrCore_db_escape($field);
        $req = "UPDATE {$tbl} SET `order` = '" . intval($order) . "' WHERE `module` = '{$mod}' AND `view` = '{$opt}' AND `name` = '{$fld}' LIMIT 1";
        jrCore_db_query($req);
    }
    foreach ($_rt as $_field) {
        if (isset($_field['name']) && $_field['name'] != $field) {
            if ($ord == $order) {
                $ord++;
            }
            $fld = jrCore_db_escape($_field['name']);
            $req = "UPDATE {$tbl} SET `order` = '{$ord}' WHERE `module` = '{$mod}' AND `view` = '{$opt}' AND `name` = '{$fld}' LIMIT 1";
            jrCore_db_query($req);
            $ord++;
        }
    }
    return true;
}

/**
 * Delete an existing Designer Form field from the form table
 * @param string $module Module that has registered a designer form view
 * @param string $view View field belongs to
 * @param string $name Name of field to delete
 * @return bool
 */
function jrCore_delete_designer_form_field($module, $view, $name)
{
    $_rt = jrCore_get_designer_form_fields($module, $view);
    if (!isset($_rt) || !is_array($_rt) || count($_rt) === 0) {
        // NO designer fields
        return true;
    }
    $tbl = jrCore_db_table_name('jrCore', 'form');
    $mod = jrCore_db_escape($module);
    $opt = jrCore_db_escape($view);
    $fld = jrCore_db_escape($name);
    $req = "DELETE FROM {$tbl} WHERE `module` = '{$mod}' AND `view` = '{$opt}' AND `name` = '{$fld}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Check if system is in maintenance mode
 *
 * The jrCore_is_maintenance_mode function will redirect a non-logged in,
 * non-master user to the maintenance page.  Allows log ins from masters
 *
 * @param array $_conf Global Configuration array
 * @param array $_post jrCore_parse_url return
 * @return bool
 */
function jrCore_is_maintenance_mode($_conf, $_post)
{
    if (isset($_conf['jrCore_maintenance_mode']) && $_conf['jrCore_maintenance_mode'] == 'on') {
        if (!jrUser_is_admin()) {
            // We're in maintenance mode - check for allowed module views
            if (isset($_post['option'])) {
                switch ($_post['option']) {
                    case 'webhook':
                    case 'login':
                    case 'login_save':
                    case 'logout':
                    case 'form_validate':
                    case 'forgot':
                    case 'forgot_save':
                    case 'new_password':
                    case 'new_password_save':
                        return false;
                        break;
                    default:
                        // We need to check if this is a "no session" login - if so,
                        // we are going to return false
                        $_tmp = jrCore_get_registered_module_features('jrUser', 'skip_session');
                        if ($_tmp && is_array($_tmp)) {
                            foreach ($_tmp as $mod => $_opts) {
                                if (isset($_opts["{$_post['option']}"]) && ($mod == $_post['module'] || $_opts["{$_post['option']}"] == 'magic_view')) {
                                    return false;
                                }
                            }
                        }
                        break;
                }
            }
            jrUser_session_destroy();
            return true;
        }
    }
    return false;
}

/**
 * Get registered system plugins for given type
 * @param string $type Type of Plugin to get
 * @return mixed
 */
function jrCore_get_system_plugins($type)
{
    $_tmp = jrCore_get_flag('jr_register_system_plugin');
    if (!isset($_tmp[$type]) || !is_array($_tmp[$type]) || count($_tmp[$type]) === 0) {
        return false;
    }
    $_out = array();
    foreach ($_tmp[$type] as $module => $_mod) {
        foreach ($_mod as $plugin => $desc) {
            $_out["{$module}_{$plugin}"] = $desc;
        }
    }
    return $_out;
}

/**
 * Register a module function for an event trigger
 * @param string $module Module registering for event trigger
 * @param string $event Event name registering for
 * @param string $function Function to execute when event is triggered
 * @return bool
 */
function jrCore_register_event_listener($module, $event, $function)
{
    // We can register 1 of 3 events:
    // a specific event from a specific module - i.e. 'jrUser','get_info_by_id'
    // all events from a specific module - i.e. 'jrUser','all_events'
    // all events for the whole system - i.e. 'jrCore','all_events'
    if (!isset($GLOBALS['__JR_FLAGS']['jrcore_event_listeners'])) {
        $GLOBALS['__JR_FLAGS']['jrcore_event_listeners'] = array();
    }
    $ename = "{$module}_{$event}";
    if (!isset($GLOBALS['__JR_FLAGS']['jrcore_event_listeners'][$ename])) {
        $GLOBALS['__JR_FLAGS']['jrcore_event_listeners'][$ename] = array();
    }
    $GLOBALS['__JR_FLAGS']['jrcore_event_listeners'][$ename][] = $function;
    return true;
}

/**
 * Register an event trigger that modules can listen for
 * @param string $module Module registering the new event trigger
 * @param string $event Event name being registered
 * @param string $description Descriptive text used when jrDeveloper module is installed outlining what the event trigger is for
 * @return bool
 */
function jrCore_register_event_trigger($module, $event, $description)
{
    // We can register 1 of 3 events:
    // a specific event from a specific module - i.e. 'jrUser','get_info_by_id'
    // all events from a specific module - i.e. 'jrUser','all_events'
    // all events for the whole system - i.e. 'jrCore','all_events'
    $GLOBALS['__JR_FLAGS']['jrcore_event_triggers']["{$module}_{$event}"] = $description;
    return true;
}

/**
 * Register a Core System architecture plugin
 * @param string $module Module that provides the plugin capability
 * @param string $type one of "email", "cache" or "media"
 * @param string $plugin Plugin Name
 * @param string $description Plugin Description
 * @return bool
 */
function jrCore_register_system_plugin($module, $type, $plugin, $description)
{
    $_tmp = jrCore_get_flag('jr_register_system_plugin');
    if (!$_tmp) {
        $_tmp = array();
    }
    if (!isset($_tmp[$type])) {
        $_tmp[$type] = array();
    }
    if (!isset($_tmp[$type][$module])) {
        $_tmp[$type][$module] = array();
    }
    $_tmp[$type][$module][$plugin] = $description;
    jrCore_set_flag('jr_register_system_plugin', $_tmp);
    return true;
}

/**
 * Create a new entry in the settings table
 * @param string $module Module registering setting for
 * @param array $_field Array of setting information
 * @return bool
 */
function jrCore_register_setting($module, $_field)
{
    if (!is_array($_field) || !isset($_field['type'])) {
        jrCore_notice('Error', "jrCore_register_setting required field: type missing for setting");
    }
    // example $_field:
    // $_tmp = array(
    //     'name'     => 'from_email',
    //     'label'    => 'from email address',
    //     'default'  => '',
    //     'type'     => 'text',
    //     'validate' => 'email',
    //     'help'     => 'When the system sends an automated / system message, what email address should the email be sent from?',
    //     'section'  => 'general email settings'
    // );
    // Optional:
    //     'min'      => (int) (minimum allow numerical value - validated)
    //     'max'      => (int) (maximum allow numerical value - validated)
    //     'options'  => array() (array of key => value pairs for fields with "select" type

    // See if we have already been called for this module/key in this process
    $key = jrCore_db_escape($_field['name']);
    $_tm = jrCore_get_flag('jrcore_register_setting');
    if ($_tm && is_array($_tm) && isset($_tm["{$module}_{$key}"])) {
        return true;
    }
    if (!$_tm || !is_array($_tm)) {
        $_tm = array();
    }
    $_tm["{$module}_{$key}"] = 1;
    jrCore_set_flag('jrcore_register_setting', $_tm);
    if (!$_orig = jrCore_get_flag('jrCore_register_setting_fields')) {
        $_orig = array();
    }
    $_orig["{$_field['name']}"] = $_field;
    if (isset($_orig["{$_field['name']}"]['value'])) {
        unset($_orig["{$_field['name']}"]['value']);
    }
    jrCore_set_flag('jrCore_register_setting_fields', $_orig);

    // Some items are required for form fields
    $_ri = array_flip(array('name', 'default', 'validate', 'label', 'help'));
    switch ($_field['type']) {
        // we already internally validate hidden and select elements
        case 'hidden':
            unset($_ri['validate'], $_ri['label'], $_ri['help']);
            break;
        case 'radio':
        case 'select':
        case 'select_multiple':
        case 'optionlist':
            unset($_ri['validate']);
            // Handle field options for select statements if set
            if (isset($_field['options']) && is_array($_field['options'])) {
                // Can setup a default
                if (!isset($_field['default'])) {
                    $_field['default'] = array_keys($_field['options']);
                    $_field['default'] = reset($_field['default']);
                }
                $_field['options'] = json_encode($_field['options']);
            }
            elseif (isset($_field['options']) && !function_exists($_field['options'])) {
                // These select options are generated at display time by a function
                jrCore_notice('Error', "jrCore_register_setting option function defined for field: {$_field['name']} does not exist");
            }
            break;
    }
    foreach ($_ri as $k => $v) {
        if (!isset($_field[$k])) {
            jrCore_logger('CRI', "jrCore_register_setting required field: \"{$k}\" missing for: {$module}/{$_field['name']}");
            return false;
        }
    }
    // Make sure setting is properly updated
    return jrCore_update_setting($module, $_field);
}

/**
 * Verify a Global Setting is configured correctly in the settings table
 * @param string $module Module to create global setting for
 * @param array $_field Array of setting information
 * @return bool
 */
function jrCore_update_setting($module, $_field)
{
    global $_conf, $_user;
    $usr = (isset($_user['user_name']) && strlen($_user['user_name']) > 0) ? $_user['user_name'] : (isset($_user['user_email']) ? $_user['user_email'] : 'installer');
    $tbl = jrCore_db_table_name('jrCore', 'setting');
    $req = "SELECT * FROM {$tbl} WHERE `module` = '" . jrCore_db_escape($module) . "' AND `name` = '" . jrCore_db_escape($_field['name']) . "'";
    $_ex = jrCore_db_query($req, 'SINGLE');
    $_rt = jrCore_db_table_columns('jrCore', 'setting');

    // Create
    if (!$_ex || !is_array($_ex)) {
        $_cl = array();
        $_vl = array();

        // When creating a NEW entry in settings, our value is set to the default
        $_field['value'] = $_field['default'];
        $stt             = jrCore_get_flag('verify_module_state');
        if ($stt && $stt == 'upgraded' && isset($_field['upgrade'])) {
            // We've been given an UPGRADE flag - this tells us that if this module is being UPGRADED (i.e.
            // it is NOT a new install of the module) then we use the value of UPGRADE as the default
            $_field['value'] = $_field['upgrade'];
        }

        foreach ($_rt as $k => $v) {
            if (isset($_field[$k])) {
                $_cl[] = "`{$k}`";
                $_vl[] = jrCore_db_escape($_field[$k]);
            }
            // TEXT fields must have a default value
            elseif (strpos(' ' . $v['Type'], 'text')) {
                $_cl[] = "`{$k}`";
                $_vl[] = '';
            }
        }
        if (count($_cl) < 1) {
            return false;
        }
        $req = "INSERT INTO {$tbl} (`module`,`created`,`updated`,`user`," . implode(',', $_cl) . ") VALUES ('" . jrCore_db_escape($module) . "',UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),'" . jrCore_db_escape($usr) . "','" . implode("','", $_vl) . "')";
    }
    // Update
    else {
        $upd = false;
        $req = "UPDATE {$tbl} SET `updated` = UNIX_TIMESTAMP(), `user` = '" . jrCore_db_escape($usr) . "', ";
        foreach ($_rt as $k => $v) {
            if (isset($_field[$k])) {
                if ($_field[$k] != $_ex[$k]) {
                    $req .= "`{$k}` = '" . jrCore_db_escape($_field[$k]) . "',";
                    $upd = true;
                }
            }
            else {
                // Some fields can be unset
                switch ($k) {
                    case 'section':
                    case 'sublabel':
                        if (strlen($_ex[$k]) > 0) {
                            $req .= "`{$k}` = '',";
                            $upd = true;
                        }
                        break;
                }
            }
        }
        if (!$upd) {
            // Nothing has changed
            return false;
        }
        $req = substr($req, 0, strlen($req) - 1) . " WHERE module = '" . jrCore_db_escape($module) . "' AND `name` = '" . jrCore_db_escape($_field['name']) . "' LIMIT 1";
    }
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        // Make sure this is updated in process
        if (!isset($_conf["{$module}_{$_field['name']}"])) {
            $_conf["{$module}_{$_field['name']}"] = (isset($_field['value'])) ? $_field['value'] : $_field['default'];
        }
        if (isset($_cl)) {
            jrCore_logger('INF', "created global setting for {$module} module: {$_field['name']}");
        }
        jrCore_delete_config_cache();
        return true;
    }
    return false;
}

/**
 * Delete an existing global setting from the settings table
 * @param string $module Module Name
 * @param string $name Setting Name
 * @return bool
 */
function jrCore_delete_setting($module, $name)
{
    $tbl = jrCore_db_table_name('jrCore', 'setting');
    $req = "DELETE FROM {$tbl} WHERE `module` = '" . jrCore_db_escape($module) . "' AND `name` = '" . jrCore_db_escape($name) . "' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_delete_config_cache();
        jrCore_logger('INF', "global setting {$module}_{$name} was successfully deleted");
    }
    return true;
}

/**
 * Update a Global Config setting value
 * @param string $module Module that owns the setting
 * @param string $name Name of the setting
 * @param string $value New Value for setting
 * @return bool
 */
function jrCore_set_setting_value($module, $name, $value)
{
    global $_conf, $_user;
    $usr = (isset($_user['user_name']) && strlen($_user['user_name']) > 0) ? $_user['user_name'] : (isset($_user['user_email']) ? $_user['user_email'] : 'installer');
    $tbl = jrCore_db_table_name('jrCore', 'setting');
    $req = "UPDATE {$tbl} SET `updated` = UNIX_TIMESTAMP(), `value` = '" . jrCore_db_escape($value) . "', `user` = '" . jrCore_db_escape($usr) . "'
             WHERE `module` = '" . jrCore_db_escape($module) . "' AND `name` = '" . jrCore_db_escape($name) . "' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        $_conf["{$module}_{$name}"] = $value;
        return true;
    }
    return false;
}

/**
 * Check for and run maintenance workers if needed
 * @return bool
 */
function jrCore_maintenance_check()
{
    global $_post;
    // NOTE: Do not use a global lock here as that requires a
    // trip to the DB on every process to "check"
    // Make sure we are the only process to create the queue entry
    $now = gmstrftime('%y%m%d%H%M');
    $dir = jrCore_get_module_cache_dir('jrCore');
    $lck = "{$dir}/minute_maintenance_{$now}.lock";
    if (!is_file($lck)) {

        // No lock file - create minute maintenance queue entry
        touch($lck);
        $_queue = array(
            'minute' => $now,
            '_post'  => $_post
        );
        jrCore_queue_create('jrCore', 'minute_maintenance', $_queue, 0, null, 1);

        // Every 10 minutes we need to make sure our queues are in good order
        if (substr($now, 9, 1) == 1) {
            // NOTE: Queue checks are done OUTSIDE a queue entry so we ensure they can
            // run even if the queue_data gets out of whack
            jrCore_check_for_dead_queue_workers();
            jrCore_validate_queue_info();
            jrCore_validate_queue_data();
        }

    }
    return false;
}

/**
 * Minute Maintenance worker
 * @param array $_queue
 * @return bool
 */
function jrCore_minute_maintenance_worker($_queue)
{
    global $_conf;

    // Process MINUTE maintenance
    ini_set('max_execution_time', 55);
    jrCore_set_flag('jr_minute_maintenance_is_active', 1);

    // Check for HOURLY maintenance
    $clr = false;
    $now = gmstrftime('%Y%m%d%H');

    if (isset($_conf['jrCore_last_hourly_maint_run']) && $_conf['jrCore_last_hourly_maint_run'] < $now) {
        jrCore_set_setting_value('jrCore', 'last_hourly_maint_run', $now);
        $_tmp = array(
            'hour'  => $now,
            '_post' => $_queue['_post']
        );
        jrCore_queue_create('jrCore', 'hourly_maintenance', $_tmp, 15, null, 1);
        $clr = true;
    }

    // Check for DAILY maintenance
    $off = (time() + date_offset_get(new DateTime));
    $now = gmstrftime('%Y%m%d', $off);
    if (isset($_conf['jrCore_last_daily_maint_run']) && $_conf['jrCore_last_daily_maint_run'] < $now) {
        jrCore_set_setting_value('jrCore', 'last_daily_maint_run', $now);
        $_tmp = array(
            'day'   => $now,
            '_post' => $_queue['_post']
        );
        jrCore_queue_create('jrCore', 'daily_maintenance', $_tmp, 30, null, 1);
        $clr = true;
    }

    // Do we need to UPDATE our config cache?
    if ($clr) {
        jrCore_delete_config_cache();
    }

    // Delete expired global locks
    jrCore_delete_expired_global_locks();

    // Cache maintenance
    jrCore_cache_maintenance();

    jrCore_trigger_event('jrCore', 'minute_maintenance', $_queue);
    jrCore_delete_flag('jr_minute_maintenance_is_active');

    return true;
}

/**
 * Run HOURLY maintenance
 * @param array $_queue
 * @return bool
 */
function jrCore_hourly_maintenance_worker($_queue)
{
    ini_set('max_execution_time', 3550);

    jrCore_set_flag('jr_hourly_maintenance_is_active', 1);
    $now = explode(' ', microtime());
    $now = $now[1] + $now[0];

    // Run maintenance
    jrCore_form_session_maintenance();
    jrCore_media_play_key_maintenance();
    jrCore_purge_activity_logs();
    jrCore_delete_old_error_and_debug_logs();
    jrCore_delete_old_upload_directories();
    jrCore_delete_old_maintenance_lock_files();
    jrCore_delete_old_test_templates();

    // Maintain queue integrity
    jrCore_check_for_dead_queue_workers();
    jrCore_validate_queue_info();
    jrCore_validate_queue_data();

    // Cleanup hit counter ip table older than 24 hours
    $tbl = jrCore_db_table_name('jrCore', 'count_ip');
    $req = "DELETE FROM {$tbl} WHERE count_time < (UNIX_TIMESTAMP() - 86400)";
    jrCore_db_query($req, 'COUNT');

    // Delete old modal entries (over 12 hours)
    $tbl = jrCore_db_table_name('jrCore', 'modal');
    $req = "SELECT modal_id FROM {$tbl} WHERE modal_updated < (UNIX_TIMESTAMP() - 43200)";
    $_rt = jrCore_db_query($req, 'modal_id', false, 'modal_id');
    if ($_rt && is_array($_rt)) {
        $req = "DELETE FROM {$tbl} WHERE modal_id IN(" . implode(',', $_rt) . ')';
        jrCore_db_query($req);
    }

    jrCore_trigger_event('jrCore', 'hourly_maintenance', $_queue);

    $end = explode(' ', microtime());
    $end = $end[1] + $end[0];
    $end = round(($end - $now), 2);

    if ($end > 5) {
        jrCore_logger('INF', "hourly maintenance worker completed in {$end} seconds");
    }
    jrCore_delete_flag('jr_hourly_maintenance_is_active');
    return true;
}

/**
 * Run DAILY maintenance
 * @param array $_queue
 * @return bool
 */
function jrCore_daily_maintenance_worker($_queue)
{
    ini_set('max_execution_time', 86000);
    jrCore_set_flag('jr_daily_maintenance_is_active', 1);
    jrCore_logger('INF', 'daily maintenance worker started');

    jrCore_trigger_event('jrCore', 'daily_maintenance', $_queue);

    jrCore_logger('INF', 'daily maintenance worker completed');
    jrCore_delete_flag('jr_daily_maintenance_is_active');
    return true;
}

//--------------------------------------
// EMAIL wrapper functions
//--------------------------------------

/**
 * Parse subject and message email templates
 * @param string $module Module name
 * @param string $template Base email template name to parse
 * @param array $_replace Replacement Key => Value array
 * @return mixed
 */
function jrCore_parse_email_templates($module, $template, $_replace = null)
{
    $sub_file = APP_DIR . "/modules/{$module}/templates/email_{$template}_subject.tpl";
    if (!is_file($sub_file)) {
        return false;
    }
    $msg_file = APP_DIR . "/modules/{$module}/templates/email_{$template}_message.tpl";
    if (!is_file($msg_file)) {
        return false;
    }

    // 0 = subject
    // 1 = message
    ob_start();
    $_out = array(
        trim(jrCore_strip_emoji(html_entity_decode(jrCore_parse_template("email_{$template}_subject.tpl", $_replace, $module), ENT_QUOTES), false)),
        jrCore_parse_template("email_{$template}_message.tpl", $_replace, $module)
    );
    ob_end_clean();
    return $_out;
}

/**
 * @ignore
 * jrCore_get_email_system_plugins
 * @return array
 */
function jrCore_get_email_system_plugins()
{
    return jrCore_get_system_plugins('email');
}

/**
 * @ignore
 * jrCore_get_active_email_system
 * @return string
 */
function jrCore_get_active_email_system()
{
    // Find our active email system plugin
    global $_conf;
    if (isset($_conf['jrMailer_active_email_system']{1})) {
        // Make sure function exists...
        $func = "_{$_conf['jrMailer_active_email_system']}_send_email";
        if (function_exists($func)) {
            return $_conf['jrMailer_active_email_system'];
        }
    }
    return 'jrCore_debug';
}

/**
 * Send an email to single or multiple recipients
 * @param mixed $_add Email addresses (single address as a string, multiple addresses as an array) to send to
 * @param string $subject Message Subject
 * @param string $message Message Body
 * @param array $_options Email options
 * @param array $_user_data User info about EACH email address in $_add (email address index)
 * @return int
 */
function jrCore_send_email($_add, $subject, $message, $_options = null, $_user_data = null)
{
    global $_conf;
    // message and subject are required
    if (strlen($subject) === 0) {
        jrCore_logger('CRI', "jrCore_send_email: empty subject received - verify usage", func_get_args());
        return false;
    }
    $message = trim($message);
    if (strlen($message) === 0) {
        jrCore_logger('CRI', "jrCore_send_email: empty message received - verify usage", func_get_args());
        return false;
    }

    // our addresses must be an incoming array
    if (!is_array($_add)) {
        $_add = array($_add);
    }
    // Validate email addresses
    foreach ($_add as $k => $address) {
        if (!jrCore_checktype($address, 'email')) {
            unset($_add[$k]);
        }
    }
    // Make sure we still have at least 1 good email
    if (count($_add) === 0) {
        return false;
    }

    // Make sure we have our mail options
    if (is_null($_options) || !is_array($_options)) {
        $_options = array();
    }

    // Remove any emoji from our subject
    $_options['subject'] = jrCore_strip_emoji($subject, false);

    // figure our from email address
    if (!isset($_options['from']) || !jrCore_checktype($_options['from'], 'email')) {
        $_options['from'] = (isset($_conf['jrMailer_from_email'])) ? $_conf['jrMailer_from_email'] : $_SERVER['SERVER_ADMIN'];
    }

    // Make sure we have user info about each address
    if (is_null($_user_data) || !is_array($_user_data)) {
        $_us = array(
            'search'        => array(
                'user_email in ' . implode(',', $_add)
            ),
            'order_by'      => false,
            'skip_triggers' => true,
            'privacy_check' => false
        );
        $_us = jrCore_db_search_items('jrUser', $_us);
        if ($_us && is_array($_us) && isset($_us['_items'])) {
            $_user_data = array();
            foreach ($_us['_items'] as $k => $v) {
                $add              = $v['user_email'];
                $_user_data[$add] = $v;
            }
        }
    }

    // Trigger our addresses event
    $_add = jrCore_trigger_event('jrCore', 'email_addresses', $_add, $_user_data);
    if (!$_add || isset($_add['abort']) || count($_add) === 0) {
        // Our addresses were removed by a listener
        return 0;
    }

    // Are we sending as HTML?
    $html = false;
    if (isset($_options['send_as_html'])) {
        $html = ($_options['send_as_html'] === true) ? true : false;
    }
    elseif (isset($_conf['jrMailer_send_as_html']) && $_conf['jrMailer_send_as_html'] == 'on') {
        $html = true;
    }
    elseif (stripos($message, '<html') === 0 || stripos($message, 'DOCTYPE')) {
        $html = true;
    }

    // Are we overriding any setting?
    if (isset($_options['mailing_module']) && isset($_conf["{$_options['mailing_module']}_email_format"])) {
        $html = ($_conf["{$_options['mailing_module']}_email_format"] == 'html') ? true : false;
    }

    // Make sure any bbcode is converted to HTML
    $message = jrCore_format_string_bbcode($message);
    if ($_tm = jrCore_get_flag('jrcore_bbcode_replace_blocks')) {
        $message = str_replace(array_keys($_tm), $_tm, $message);
    }
    if (!$html) {
        $message = str_replace('<pre class="hljs php">', "\n[code]\n", $message);
        $message = str_replace('</pre></div>', "\n[/code]\n</div>", $message);
        $message = html_entity_decode(jrCore_strip_html($message), ENT_QUOTES);
    }

    // Create individual email message queues
    $murl = jrCore_get_module_url('jrUser');
    foreach ($_add as $address) {

        // Is this a User Account in the system?
        if (is_array($_user_data) && isset($_user_data[$address])) {
            // User has an account in the system
            $v                    = $_user_data[$address];
            $v['preferences_url'] = "{$_conf['jrCore_base_url']}/{$murl}/notifications/r=1";
            $v['unsubscribe_url'] = "{$_conf['jrCore_base_url']}/{$murl}/unsubscribe/{$v['user_validate']}";

        }
        else {
            // This is an email address OUTSIDE our system
            $v                    = array();
            $v['preferences_url'] = "{$_conf['jrCore_base_url']}/{$murl}/notifications/r=1";
            $v['unsubscribe_url'] = "{$_conf['jrCore_base_url']}/{$murl}/unsubscribe/{$address}/" . md5($_conf['jrCore_unique_string'] . $address);
        }

        // Are we sending an HTML email?
        if ($html) {

            // Our preferences footer
            $foot = jrCore_parse_template('email_preferences_html_footer.tpl', $v, 'jrUser');

            // Have we already been given an HTML header?  The NewsLetter module will come in
            // as a full HTML page so we don't want to add any HTML header
            if (stripos($message, '<html') === 0 || stripos($message, 'DOCTYPE')) {

                $_options['message'] = jrCore_replace_emoji($message);
                // Have we been told where to place the preferences footer?
                if (strpos($message, '%%EMAIL_PREFERENCES_FOOTER%%')) {
                    $_options['message'] = str_replace('%%EMAIL_PREFERENCES_FOOTER%%', $foot, $_options['message']);
                }
                elseif (strpos($_options['message'], '</body>')) {
                    // Place it before the closing body tag
                    $_options['message'] = str_replace('</body>', "{$foot}</body>", $_options['message']);
                }
                else {
                    // Place it at the end
                    $_options['message'] .= $foot;
                }
            }
            else {
                $v['email_message']     = nl2br(trim(jrCore_replace_emoji(jrCore_string_to_url($message))));
                $v['email_preferences'] = $foot;
                $_options['message']    = jrCore_parse_template('email_html_notification.tpl', $v, 'jrUser');
            }

        }
        else {

            // NON - html
            $_options['message'] = jrCore_parse_template('email_overall_header.tpl', $v, 'jrUser');
            $_options['message'] .= jrCore_strip_emoji(jrCore_replace_emoji($message), false) . "\n\n";
            $_options['message'] .= jrCore_parse_template('email_preferences_footer.tpl', $v, 'jrUser');

        }
        $_options['send_as_html'] = $html;

        // Is this a high priority send (default) or deferred?
        $queue = 'send_email';
        if (isset($_options['low_priority']) && $_options['low_priority'] == true) {
            $queue = 'send_email_low_priority';
        }
        // Create email queue entry
        $_queue = array(
            'address'    => array($address),
            '_options'   => $_options,
            '_user_data' => $v,
            'queue'      => $queue
        );
        $sleep  = 0;
        if (isset($_options['queue_sleep']) && jrCore_checktype($_options['queue_sleep'], 'number_nz')) {
            $sleep = (int) $_options['queue_sleep'];
        }
        jrCore_queue_create('jrCore', $queue, $_queue, $sleep);

    }
    return count($_add);
}

/**
 * Worker that processes the Core send_email Queue
 * @param $_queue array Queue entry
 * @return mixed
 */
function jrCore_send_email_queue_worker($_queue)
{
    global $_conf;

    if (!is_array($_queue)) {
        return true; // bad queue entry
    }

    // Email coming in via the low_priority queue can be throttled
    if (isset($_queue['queue']) && $_queue['queue'] == 'send_email_low_priority') {

        // Are we throttling?
        if (isset($_conf['jrMailer_throttle']) && jrCore_checktype($_conf['jrMailer_throttle'], 'number_nz')) {

            // See if we have already hit our max on this run
            $min = strftime('%y%m%d%H%M');
            if ($tmp = jrCore_get_flag('jrcore_send_email_throttled')) {
                if ($tmp == $min) {
                    // We have hit the max allowed to be sent in this minute - let the queue know we are throttling
                    return 'THROTTLED';
                }
            }

            // We're throttling, and need to make sure we only send X number per minute
            $max = (int) $_conf['jrMailer_throttle'] + 1;
            $tbl = jrCore_db_table_name('jrMailer', 'throttle');
            $_rq = array(
                "INSERT INTO {$tbl} (t_min, t_cnt) VALUES ('{$min}', 1) ON DUPLICATE KEY UPDATE t_cnt = IF(t_cnt < {$max}, (t_cnt + 1), t_cnt)",
                "SELECT t_cnt FROM {$tbl} WHERE t_min = '{$min}'"
            );
            $_rt = jrCore_db_multi_select($_rq, false, false);
            if ($_rt && is_array($_rt)) {
                if (isset($_rt[0][0]['t_cnt']) && $_rt[0][0]['t_cnt'] >= $max) {
                    // We've hit our maximum send for this minute
                    // Set the flag so we do not have to check again this minute
                    jrCore_set_flag('jrcore_send_email_throttled', $min);
                    return 'THROTTLED';
                }
            }
        }
    }

    // Get our active mailer sub system and send email
    $smtp = jrCore_get_active_email_system();
    $func = "_{$smtp}_send_email";
    if (function_exists($func)) {

        // Trigger email_prepare event
        $_queue  = jrCore_trigger_event('jrCore', 'email_prepare', $_queue);
        $numsent = $func($_queue['address'], array(), $_conf, $_queue['_options']);
        if ($numsent !== false) {
            if (is_numeric($numsent) && $numsent > 0) {
                $_queue['total_sent'] = (int) $numsent;
                jrCore_trigger_event('jrCore', 'email_sent', $_queue);
            }
            return true;
        }
        return false;
    }
    jrCore_logger('CRI', "active email system function: {$func} is not defined");
    return false;
}

/**
 * @ignore
 * Core provided Send Email function that logs all sent email to the debug log
 * @param $_email_to mixed address or array of addresses to
 * @param $_user array Current User info
 * @param $_conf array Global Config
 * @param $_email_info array Email options (subject, message, etc.)
 * @return int
 */
function _jrCore_debug_send_email($_email_to, $_user, $_conf, $_email_info)
{
    if (function_exists('jrMailer_prepare_email_message')) {
        foreach ($_email_to as $address) {
            $_email_info['message'] = jrMailer_prepare_email_message($address, $_email_info['message']);
            $_out                   = array(
                '_email_to'   => $address,
                '_email_info' => $_email_info
            );
            fdebug($_out); // OK
        }
    }
    else {
        $_out = array(
            '_email_to'   => $_email_to,
            '_email_info' => $_email_info
        );
        fdebug($_out); // OK
    }
    return count($_email_to);
}

/**
 * @ignore
 * Core provided Send Email function that logs all sent email to the Activity Log
 * @param $_email_to mixed address or array of addresses to
 * @param $_user array Current User info
 * @param $_conf array Global Config
 * @param $_email_info array Email options (subject, message, etc.)
 * @return int
 */
function _jrCore_activity_send_email($_email_to, $_user, $_conf, $_email_info)
{
    if (function_exists('jrMailer_prepare_email_message')) {
        foreach ($_email_to as $address) {
            $_email_info['message'] = jrMailer_prepare_email_message($address, $_email_info['message']);
            $_out                   = array(
                '_email_to'   => $address,
                '_email_info' => $_email_info
            );
            jrCore_logger('INF', "Email To {$address}: {$_email_info['subject']}", $_out);
        }
    }
    else {
        $_out = array(
            '_email_to'   => $_email_to,
            '_email_info' => $_email_info
        );
        jrCore_logger('INF', "Email To {$_email_to}: {$_email_info['subject']}", $_out);
    }
    return count($_email_to);
}

/**
 * Cleanup media files from items that have been emptied from the recycle bin
 * @param $_queue array Queue entry
 * @return bool
 */
function jrCore_empty_recycle_bin_files_worker($_queue)
{
    // Cleanup any attached media
    if (isset($_queue['_items']) && is_array($_queue['_items'])) {
        if (!$_pr = jrCore_get_flag('jrprofile_media_changes')) {
            $_pr = array();
        }
        foreach ($_queue['_items'] as $_item) {
            if (isset($_item['pid']) && $_item['pid'] > 0 && isset($_item['r_data']{1})) {
                $_tm = json_decode($_item['r_data'], true);
                if ($_tm && is_array($_tm) && isset($_tm['rb_item_media'])) {
                    if (!$_fl = jrCore_get_flag("jrCore_empty_recycle_bin_{$_item['pid']}")) {
                        $_fl = jrCore_get_media_files($_item['pid']);
                        jrCore_set_flag("jrCore_empty_recycle_bin_{$_item['pid']}", $_fl);
                    }
                    if ($_fl && is_array($_fl)) {
                        foreach ($_fl as $_file) {
                            $name = basename($_file['name']);
                            if (strpos($name, "rb_{$_item['module']}_{$_item['iid']}_") === 0) {
                                jrCore_delete_media_file($_item['pid'], $name);
                            }
                        }
                    }
                }
                $_pr["{$_item['pid']}"] = $_item['pid'];
            }
        }
        if (count($_pr) > 0) {
            jrCore_set_flag('jrprofile_media_changes', $_pr);
        }
    }
    return true;
}

/**
 * Delete or rename media files offline when items are removed
 * @param $_queue array Queue entry
 * @return bool
 */
function jrCore_db_delete_item_media_worker($_queue)
{
    if (isset($_queue['_delete_files']) && is_array($_queue['_delete_files'])) {
        foreach ($_queue['_delete_files'] as $k => $file) {
            if (isset($_queue['rb_item_media']) && $_queue['rb_item_media'] == 1) {
                // This file is going to the recycle bin - rename
                $file = basename($file);
                jrCore_rename_media_file($_queue['_profile_id'], $file, 'rb_' . $file);
            }
            else {
                // This file is being deleted
                jrCore_delete_item_media_file($file[0], $file[1], $_queue['_profile_id'], $_queue['_item_id'], false);
            }
        }
    }
    return true;
}

/**
 * Test Queue System Worker process
 * @param $_queue array Queue entry
 * @return bool
 */
function jrCore_test_queue_system_worker($_queue)
{
    usleep(50000);
    return true;
}

/**
 * Test if a given value for a type is a banned item
 * @DEPRECATED - use jrBanned_is_banned via jrCore_run_module_function()
 * @param string $type Type of Banned Item
 * @param string $value Value to check
 * @return bool
 */
function jrCore_is_banned($type, $value = null)
{
    if (jrCore_module_is_active('jrBanned')) {
        return jrBanned_is_banned($type, $value);
    }
    return false;
}

//---------------------------------------------------------
// Stats Functions
//---------------------------------------------------------

/**
 * Create a stat entry
 * @param string $module Module dir
 * @param string $key unique key
 * @param string $index index for key
 * @param int $date date in YYYYMMDD[HHMMSS] format
 * @param int $user_id If 0 will global $_user and check for _user_id
 * @param string $ip_address IP Address - if set to FALSE will use jrCore_get_ip()
 * @param bool $unique set to FALSE to not check for unique "hit"
 * @param int $count amount to set or increment by
 * @return bool
 */
function jrCore_create_stat_entry($module, $key, $index, $date = 0, $user_id = 0, $ip_address = null, $unique = true, $count = 1)
{
    global $_user;
    if ($date === 0 || strlen($date) < 8) {
        $date = strftime('%Y%m%d');
    }
    $uip = (is_null($ip_address)) ? jrCore_get_ip() : $ip_address;
    if ($user_id === 0) {
        $uid = (jrUser_is_logged_in()) ? intval($_user['_user_id']) : 0;
    }
    else {
        $uid = (int) $user_id;
    }
    if ($unique && !jrCore_is_unique_stat_hit($module, $key, $index, $date, $uip, $uid)) {
        return false;
    }
    $mod = jrCore_db_escape($module);
    $key = jrCore_db_escape($key);
    $act = jrCore_db_escape($index);
    $dat = intval($date);
    $cnt = intval($count);
    $tbl = jrCore_db_table_name('jrCore', 'stat_count');
    $req = "INSERT INTO {$tbl} (stat_module, stat_key, stat_index, stat_date, stat_value) VALUES ('{$mod}', '{$key}', '{$act}', {$dat}, {$cnt}) ON DUPLICATE KEY UPDATE stat_value = (stat_value + {$cnt})";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt > 0) {
        return true;
    }
    return false;
}

/**
 * Return TRUE if the stat count is a unique count for the viewer
 * @param string $module module
 * @param string $key key name
 * @param string $index index for key
 * @param int $date date in YYYYMMDD[HHMMSS] format
 * @param string $user_ip IP Address
 * @param int $user_id User ID
 * @return bool
 */
function jrCore_is_unique_stat_hit($module, $key, $index, $date, $user_ip, $user_id)
{
    $uip = jrCore_db_escape($user_ip);
    $uid = intval($user_id);
    $key = jrCore_db_escape($module . $key . $index);
    $dat = jrCore_db_escape($date);
    $tbl = jrCore_db_table_name('jrCore', 'stat_unique');
    $req = "INSERT IGNORE INTO {$tbl} (stat_ip, stat_user_id, stat_key, stat_date) VALUES ('{$uip}', {$uid}, '{$key}', '{$dat}')";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt > 0) {
        return true;
    }
    return false;
}

/**
 * Get a value for a stat over a date range
 * @param string $module module
 * @param mixed $key key name (or array of key names)
 * @param mixed $index index for key (or array of indexes)
 * @param int $start_date
 * @param int $end_date
 * @return int
 */
function jrCore_get_stat_value($module, $key, $index, $start_date, $end_date)
{
    if (is_array($key)) {
        foreach ($key as $k => $a) {
            $key[$k] = jrCore_db_escape($a);
        }
        $key = " AND stat_key IN('" . implode("','", $key) . "')";
    }
    else {
        $key = " AND stat_key = '" . jrCore_db_escape($key) . "'";
    }

    $act = '';
    if ($index) {
        if (is_array($index)) {
            foreach ($index as $k => $a) {
                $index[$k] = jrCore_db_escape($a);
            }
            $act = " AND stat_index IN('" . implode("','", $index) . "')";
        }
        else {
            $act = " AND stat_index = '" . jrCore_db_escape($index) . "'";
        }
    }

    $beg = (int) $start_date;
    $end = (int) $end_date;
    if ($beg === $end) {
        $dat = " AND stat_date = {$beg}";
    }
    else {
        $dat = " AND stat_date >= {$beg} AND stat_date <= {$end}";
    }

    $mod = jrCore_db_escape($module);
    $tbl = jrCore_db_table_name('jrCore', 'stat_count');
    $req = "SELECT SUM(stat_value) AS val FROM {$tbl} WHERE stat_module = '{$mod}'{$key}{$act}{$dat}";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        return intval($_rt['val']);
    }
    return 0;
}

/**
 * Get a set of values over a date range
 * @param string $module module
 * @param mixed $key key name (or array of key names)
 * @param mixed $index index for key (or array of indexes)
 * @param string $start_date
 * @param string $end_date
 * @return int
 */
function jrCore_get_all_stat_values($module, $key, $index, $start_date, $end_date)
{
    if (is_array($key)) {
        foreach ($key as $k => $a) {
            $key[$k] = jrCore_db_escape($a);
        }
        $key = " AND stat_key IN('" . implode("','", $key) . "')";
    }
    else {
        $key = " AND stat_key = '" . jrCore_db_escape($key) . "'";
    }

    $act = '';
    if ($index) {
        if (is_array($index)) {
            foreach ($index as $k => $a) {
                $index[$k] = jrCore_db_escape($a);
            }
            $act = " AND stat_index IN('" . implode("','", $index) . "')";
        }
        else {
            $act = " AND stat_index = '" . jrCore_db_escape($index) . "'";
        }
    }

    $beg = (int) $start_date;
    $end = (int) $end_date;
    if ($beg === $end) {
        $dat = " AND stat_date = {$beg}";
    }
    else {
        $dat = " AND stat_date >= {$beg} AND stat_date <= {$end}";
    }

    $mod = jrCore_db_escape($module);
    $tbl = jrCore_db_table_name('jrCore', 'stat_count');
    $req = "SELECT * FROM {$tbl} WHERE stat_module = '{$mod}'{$key}{$act}{$dat} ORDER BY stat_date DESC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        return $_rt;
    }
    return false;
}

/**
 * Get stat values for use as graph data
 * @param string $module module
 * @param mixed $key key name (or array of key names)
 * @param mixed $index index for key (or array of indexes)
 * @param int $start_date in YYYYMMDD[HH] format
 * @param int $end_date in YYYYMMDD[HH] format
 * @param string $type one of daily|hourly
 * @param bool $combined set to FALSE to return sets for each key
 * @return array|bool
 */
function jrCore_get_graph_stat_values($module, $key, $index, $start_date = 0, $end_date = 0, $type = 'daily', $combined = true)
{
    $fmt = '%Y%m%d';
    if ($type == 'hourly') {
        $fmt = '%Y%m%d%H';
    }
    if ($start_date == 0) {
        $start_date = strftime($fmt, (time() - (60 * 86400)));
    }
    if ($end_date == 0) {
        $end_date = strftime($fmt);
    }
    if (is_array($key)) {
        foreach ($key as $k => $a) {
            $key[$k] = jrCore_db_escape($a);
        }
        $key = " AND stat_key IN('" . implode("','", $key) . "')";
    }
    else {
        $key = " AND stat_key = '" . jrCore_db_escape($key) . "'";
    }

    $_mp = array();
    $act = '';
    if ($index) {
        if (is_array($index)) {
            foreach ($index as $k => $a) {
                $index[$k] = jrCore_db_escape($a);
                $_mp[$a]   = $k;
            }
            $act = " AND stat_index IN('" . implode("','", $index) . "')";
        }
        else {
            $act         = " AND stat_index = '" . jrCore_db_escape($index) . "'";
            $_mp[$index] = 0;
        }
    }

    $beg = (int) $start_date;
    $end = (int) $end_date;
    if ($beg === $end) {
        $dat = " AND stat_date = {$beg}";
    }
    else {
        $dat = " AND stat_date >= {$beg} AND stat_date <= {$end}";
    }

    $mod = jrCore_db_escape($module);
    $tbl = jrCore_db_table_name('jrCore', 'stat_count');
    $req = "SELECT stat_date AS d, stat_index AS a, stat_value AS v FROM {$tbl} WHERE stat_module = '{$mod}'{$key}{$act}{$dat} ORDER BY stat_date ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        $_rs = array();
        foreach ($_rt as $k => $v) {
            $yr = substr($v['d'], 0, 4);
            $mn = substr($v['d'], 4, 2);
            $dy = substr($v['d'], 6, 2);
            $hr = 0;
            if ($type == 'hourly') {
                $hr = substr($v['d'], 8, 2);
            }
            $tm = (string) mktime($hr, 0, 0, $mn, $dy, $yr);
            if ($combined) {
                $ix = 0;
            }
            else {
                $ix = $_mp["{$v['a']}"];
            }
            if (!isset($_rs[$ix])) {
                $_rs[$ix] = array();
            }
            if (!isset($_rs[$ix][$tm])) {
                $_rs[$ix][$tm] = 0;
            }
            $_rs[$ix][$tm] += $v['v'];
        }
        if ($combined) {
            return $_rs[0];
        }
        return $_rs;
    }
    return false;
}

//---------------------------------------------------------
// Counter functions
//---------------------------------------------------------

/**
 * Get count value for a given module/id/name
 * @param string $module Module to check unique hit for
 * @param string $name Type of count to store
 * @param string $uid Unique Identifier that identifies this entry in count table
 * @param string $pid Profile_id to limit results that belong to a single profile
 * @return int
 */
function jrCore_get_count($module, $name, $uid = null, $pid = null)
{
    // counts for a specific item_id
    if (is_numeric($uid) && isset($name{0}) && $pid == null) {
        if ($cnt = jrCore_db_get_item_key($module, $uid, "{$name}_count")) {
            return intval($cnt);
        }
    }
    // Get ALL counts for a profile_id
    elseif (isset($pid) && jrCore_checktype($pid, 'number_nz')) {
        $key = "{$name}_count";
        $_sp = array(
            'search'        => array(
                "_profile_id = {$pid}"
            ),
            'return_keys'   => array($key),
            'skip_triggers' => true,
            'privacy_check' => false,
            'limit'         => 1000000
        );
        $_rt = jrCore_db_search_items($module, $_sp);
        if (isset($_rt) && is_array($_rt['_items'])) {
            $tcount = 0;
            foreach ($_rt['_items'] as $_item) {
                if (isset($_item[$key]) && ($_item[$key] > 0)) {
                    $tcount += (int) $_item[$key];
                }
            }
            return intval($tcount);
        }
    }
    else {
        return intval(jrCore_db_run_key_function($module, $name, '*', 'sum'));
    }
    return 0;
}

/**
 * Count a hit for a module item with user ip tracking
 * @param string $module Module to check unique hit for
 * @param string $iid Unique Item ID
 * @param string $name Name of DS key for counter
 * @param int $amount Amount to increment counter by
 * @param bool $unique Check IP Address if true
 * @return bool
 */
function jrCore_counter($module, $iid, $name, $amount = 1, $unique = true)
{
    // Our steps:
    // - check IP status of hitting user
    // - if user passes IP check, increment counter
    $iid = intval($iid);
    $nam = (strpos($name, '_count')) ? $name : "{$name}_count";
    if (!$unique || jrCore_counter_is_unique_viewer($module, $iid, $nam)) {
        return jrCore_db_increment_key($module, $iid, $nam, intval($amount));
    }
    return true;
}

/**
 * Check that viewer is a making a unique count request
 * @param string $module Module to check unique hit for
 * @param string $iid Unique Identifier that identifies this entry in count table
 * @param string $name Type of count to store
 * @param int $timeframe Timeframe (in seconds) that if elapsed, will count as new "hit" - max 86400
 * @return bool
 */
function jrCore_counter_is_unique_viewer($module, $iid, $name, $timeframe = 86400)
{
    global $_user;
    if (!$uip = sprintf('%u', ip2long(jrCore_get_ip()))) {
        $uip = 0;
    }
    $iid = (int) $iid;
    $uid = (isset($_user['_user_id'])) ? (int) $_user['_user_id'] : 0;
    $nam = (strpos($name, '_count')) ? $name : "{$name}_count";
    $typ = jrCore_db_escape($nam);
    $tbl = jrCore_db_table_name('jrCore', 'count_ip');
    jrCore_db_close();
    $con = jrCore_db_connect(true, false);
    $req = "INSERT INTO {$tbl} (count_ip, count_uid, count_user_id, count_name, count_time)
            VALUES ({$uip}, {$iid}, {$uid}, '{$typ}', UNIX_TIMESTAMP())
            ON DUPLICATE KEY UPDATE count_time = IF(count_time < (UNIX_TIMESTAMP() - {$timeframe}), UNIX_TIMESTAMP(), count_time)";
    $cnt = jrCore_db_query($req, 'COUNT', false, null, false, $con);
    jrCore_db_close();
    jrCore_db_connect();
    if ($cnt && ($cnt === 1 || $cnt === 2)) {
        // 0 = record exists and hit is UNDER $timeframe - no count
        // 1 = new row inserted - user has not been counted
        // 2 = it has been over the $timeframe - updated - recount
        return true;
    }
    return false;
}

/**
 * Return string with bbcode [code] stripped out
 * @param string $str string to remove BBCode [code] from
 * @return bool
 */
function jrCore_strip_bb_code($str)
{
    return preg_replace("/\[code\](.+?)\[\/code\]/is", '', $str);
}

//---------------------------------------------------------
// MOBILE functions
//---------------------------------------------------------

/**
 * Return true if viewing browser is a mobile device
 * @return bool
 */
function jrCore_is_mobile_device()
{
    $tmp = jrCore_get_flag('jrcore_is_mobile_device');
    if (!$tmp) {
        if (!class_exists('Mobile_Detect')) {
            require_once APP_DIR . '/modules/jrCore/contrib/mobile_detect/Mobile_Detect.php';
        }
        $d = new Mobile_Detect();
        if ($d->isMobile() && !$d->isTablet()) {
            $tmp = 'yes';
            $ret = true;
        }
        else {
            $tmp = 'no';
            $ret = false;
        }
        jrCore_set_flag('jrcore_is_mobile_device', $tmp);
        return $ret;
    }
    return ($tmp == 'yes') ? true : false;
}

/**
 * Return true if viewing browser is a tablet device
 * @return bool
 */
function jrCore_is_tablet_device()
{
    $tmp = jrCore_get_flag('jrcore_is_tablet_device');
    if (!$tmp) {
        if (!class_exists('Mobile_Detect')) {
            require_once APP_DIR . '/modules/jrCore/contrib/mobile_detect/Mobile_Detect.php';
        }
        $d = new Mobile_Detect();
        if ($d->isTablet() || $d->isMobile()) {
            $tmp = 'yes';
            $ret = true;
        }
        else {
            $tmp = 'no';
            $ret = false;
        }
        jrCore_set_flag('jrcore_is_tablet_device', $tmp);
        return $ret;
    }
    return ($tmp == 'yes') ? true : false;
}

/**
 * Check to be sure sure a tool is installed and working
 * @param string $tool tool provided by jrSystemTools
 * @param string $module if set, module/tools will also be checked
 * @return bool
 */
function jrCore_get_tool_path($tool, $module = null)
{
    global $_conf;
    if (isset($_conf["jrSystemTools_{$tool}_binary"])) {
        $tool = $_conf["jrSystemTools_{$tool}_binary"];
    }
    elseif (!is_null($module) && isset($_conf["{$module}_{$tool}_binary"]) && is_file($_conf["{$module}_{$tool}_binary"])) {
        $tool = $_conf["{$module}_{$tool}_binary"];
    }
    elseif (!is_null($module) && is_file(APP_DIR . "/modules/{$module}/tools/{$tool}")) {
        $tool = APP_DIR . "/modules/{$module}/tools/{$tool}";
    }
    elseif (is_file(APP_DIR . "/modules/jrSystemTools/tools/{$tool}")) {
        $tool = APP_DIR . "/modules/jrSystemTools/tools/{$tool}";
    }
    if (is_file($tool)) {
        if (!is_executable($tool)) {
            // Try to set permissions if we can...
            @chmod($tool, 0755);
        }
        return $tool;
    }
    return false;
}

/**
 * Add a new Key and Value to the data/config/config.php file
 * @param string $key
 * @param mixed $value
 * @return bool
 */
function jrCore_add_key_to_config($key, $value)
{
    $file = APP_DIR . '/data/config/config.php';
    if ($_tmp = file($file)) {
        $key = trim($key);
        switch ($key) {
            // Some keys we cannot add
            case 'jrCore_db_host':
            case 'jrCore_db_port':
            case 'jrCore_db_name':
            case 'jrCore_db_user':
            case 'jrCore_db_pass':
                return false;
                break;
        }
        if (strlen($key) > 3) {
            $_new = array();
            foreach ($_tmp as $line) {
                if (!strpos($line, "'" . $key . "'")) {
                    $_new[] = trim($line);
                }
            }
            $val = trim($value);
            if ($val === 'true' || $val === 'false') {
                $_new[] = "\$_conf['{$key}'] = {$val};";
            }
            else {
                switch ($key) {
                    case 'jrCore_dir_perms':
                    case 'jrCore_file_perms':
                        $_new[] = "\$_conf['{$key}'] = {$val};";
                        break;
                    default:
                        $_new[] = "\$_conf['{$key}'] = '{$val}';";
                        break;
                }
            }
            if (jrCore_write_to_file("{$file}.tmp.php", implode("\n", $_new) . "\n")) {
                if (rename("{$file}.tmp.php", $file)) {
                    return true;
                }
                unlink("{$file}.tmp.php");
            }
        }
    }
    return false;
}

/**
 * Delete an existing key from the data/config/config.php file
 * @param string $key
 * @return bool
 */
function jrCore_delete_key_from_config($key)
{
    $file = APP_DIR . '/data/config/config.php';
    if ($_tmp = file($file)) {
        $key = trim($key);
        switch ($key) {
            // Some keys we cannot delete
            case 'jrCore_db_host':
            case 'jrCore_db_port':
            case 'jrCore_db_name':
            case 'jrCore_db_user':
            case 'jrCore_db_pass':
                return false;
                break;
        }
        if (strlen($key) > 3) {
            $_new = array();
            foreach ($_tmp as $line) {
                if (!strpos($line, "'" . $key . "'")) {
                    $_new[] = trim($line);
                }
            }
            if (count($_new) > 0) {
                if (jrCore_write_to_file("{$file}.tmp.php", implode("\n", $_new) . "\n")) {
                    if (rename("{$file}.tmp.php", $file)) {
                        return true;
                    }
                    unlink("{$file}.tmp.php");
                }
            }
        }
    }
    return false;
}
