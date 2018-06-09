<?php
/**
 * Jamroom User Birthday module
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
 * @copyright 2012 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function jrBirthday_meta()
{
    $_tmp = array(
        'name'        => 'User Birthday',
        'url'         => 'birthday',
        'version'     => '1.1.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Creates Timeline entries for User Birthdays',
        'requires'    => 'jrCore:6.0.0',
        'license'     => 'mpl',
        'category'    => 'users'
    );
    return $_tmp;
}

/**
 * init
 */
function jrBirthday_init()
{
    // CSS
    jrCore_register_module_feature('jrCore', 'css', 'jrBirthday', 'jrBirthday.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrBirthday', 'jrBirthday.js');

    // Listeners
    jrCore_register_event_listener('jrCore', 'form_display', 'jrBirthday_form_display_listener');
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrBirthday_daily_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrBirthday_db_search_items_listener');
    jrCore_register_event_listener('jrCore', 'view_results', 'jrBirthday_view_results_listener');

    // Queue Worker
    jrCore_register_queue_worker('jrBirthday', 'check_for_birthdays', 'jrBirthday_check_for_birthdays_worker', 0, 1, 7200, LOW_PRIORITY_QUEUE);

    // Site Builder widget
    jrCore_register_module_feature('jrSiteBuilder', 'widget', 'jrBirthday', 'widget_birthdays', 'User Birthdays');

    // Action support
    jrCore_register_module_feature('jrCore', 'action_support', 'jrBirthday', 'create', 'item_action.tpl');

    return true;
}

//----------------------
// EVENT LISTENERS
//----------------------

/**
 * Insert birthdate field into user account form
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrBirthday_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrAction') {
        if (!$_rep = jrCore_get_flag('jrbirthday_modal_replace')) {
            $_rep = array();
        }
        foreach ($_data['_items'] as $k => $_item) {
            if ($_item['action_module'] == 'jrBirthday') {

                // We found a birthday wish - setup action_data
                $_data['_items'][$k]['action_data'] = jrCore_db_get_item('jrUser', $_item['action_item_id']);

                // Replacement code for display
                $code                               = jrCore_create_unique_string(8);
                $_rep["%%{$code}%%"]                = array('item' => $_data['_items'][$k]);
                $_data['_items'][$k]['action_text'] = "%%{$code}%%";
            }
        }
        jrCore_set_flag('jrbirthday_modal_replace', $_rep);
    }
    return $_data;
}

/**
 * Create Share birthday wish boxes
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrBirthday_view_results_listener($_data, $_user, $_conf, $_args, $event)
{
    // Do we have any replacements?
    if ($_rep = jrCore_get_flag('jrbirthday_modal_replace')) {

        foreach ($_rep as $code => $_item) {
            $_rp   = array(
                $code     => jrCore_parse_template('item_action.tpl', $_item, 'jrBirthday'),
                '</body>' => jrCore_parse_template('item_share_modal.tpl', $_item, 'jrBirthday') . '</body>'
            );
            $_data = str_replace(array_keys($_rp), $_rp, $_data);
        }

    }
    return $_data;
}

/**
 * Insert birthdate field into user account form
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrBirthday_form_display_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['form_view']) && $_data['form_view'] == 'jrUser/account') {
        // Add in birthdate field
        $_ln = jrUser_load_lang_strings();
        $_tm = array(
            'name'          => 'user_birthdate',
            'label'         => $_ln['jrBirthday'][1],
            'sublabel'      => $_ln['jrBirthday'][3],
            'help'          => $_ln['jrBirthday'][2],
            'type'          => 'date_birthday',
            'validate'      => 'date_birthday',
            'exclude_year'  => true,
            'required'      => false,
            'form_designer' => false
        );
        jrCore_form_field_create($_tm);
    }
    return $_data;
}

/**
 * Look for birthdays today and add them to the profile timeline
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrBirthday_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    // Setup our Queue Worker
    if (isset($_conf['jrBirthday_add_to_timeline']) && $_conf['jrBirthday_add_to_timeline'] == 'on') {
        jrCore_queue_create('jrBirthday', 'check_for_birthdays', array('date' => date('md')));
    }
    return $_data;
}

//----------------------
// QUEUE WORKER
//----------------------

/**
 * Create Timeline entries for Birthdays
 * @param array $_queue The queue entry the worker will receive
 * @return bool
 */
function jrBirthday_check_for_birthdays_worker($_queue)
{
    @ini_set('memory_limit', '512M');
    if (is_array($_queue)) {

        // See if we have any birthdays today
        $_ut = array(
            'search'        => array(
                'user_birthdate like ____' . $_queue['date']
            ),
            'skip_triggers' => true,
            'privacy_check' => false,
            'limit'         => 10000
        );
        $_ut = jrCore_db_search_items('jrUser', $_ut);
        if ($_ut && is_array($_ut) && isset($_ut['_items'])) {
            $_pids = array();
            $_uids = array();
            foreach ($_ut['_items'] as $_usr) {
                $_pids["{$_usr['_user_id']}"] = $_usr['_profile_id'];
                $_uids["{$_usr['_user_id']}"] = $_usr;
            }
            if (count($_pids) > 0) {
                $pids = implode(',', $_pids);
                $_pt = array(
                    "search" => array("_profile_id IN {$pids}"),
                    'privacy_check' => false,
                    'limit'         => 10000
                );
                $_pt = jrCore_db_search_items('jrProfile', $_pt);
                if ($_pt && is_array($_pt) && isset($_pt['_items'])) {
                    foreach ($_pt['_items'] as $_prf) {
                        if (!isset($_prf['quota_jrAction_allowed']) || $_prf['quota_jrAction_allowed'] != 'on') {
                            unset($_uids["{$_prf['_user_id']}"]);
                        }
                    }
                    foreach ($_uids as $_usr) {
                        $_usr['quota_jrAction_allowed'] = 'on';
                        jrCore_run_module_function('jrAction_save', 'create', 'jrBirthday', $_usr['_user_id'], $_usr, false, $_usr['_profile_id']);
                    }
                }
            }
        }
    }
    return true;
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
function jrBirthday_widget_birthdays_config($_post, $_user, $_conf, $_wg)
{
    $_opt = array(
        '0'  => "Today Only",
        '1'  => "1 day",
        '2'  => "2 days",
        '3'  => "3 days",
        '4'  => "4 days",
        '5'  => "5 days",
        '6'  => "6 days",
        '7'  => "1 week",
        '14' => "2 weeks",
        '21' => "3 weeks",
        '30' => "1 month"
    );
    // Widget Content
    $_tmp = array(
        'name'     => 'widget_look_ahead',
        'label'    => 'Look Ahead Days',
        'help'     => 'How many upcoming days should be checked for User Birthdays?  This will determine how many days the Birthday shows in the widget.<br><br>For example - if this is set to &quot;7&quot, any birthday happening in the next 7 days will be shown in the output.',
        'type'     => 'select',
        'options'  => $_opt,
        'value'    => (isset($_wg['widget_look_ahead'])) ? $_wg['widget_look_ahead'] : '1',
        'required' => true,
        'validate' => 'number_nn'
    );
    jrCore_form_field_create($_tmp);

    // Max results
    $_tmp = array(
        'name'     => 'widget_max_results',
        'label'    => 'Max Birthdays',
        'help'     => 'What is the maximum number of birthdays to show in the output?',
        'type'     => 'text',
        'value'    => (isset($_wg['widget_max_results'])) ? intval($_wg['widget_max_results']) : 10,
        'required' => true,
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    return true;
}

/**
 * Get Widget results from posted Config data
 * @param $_post array Post info
 * @return array
 */
function jrBirthday_widget_birthdays_config_save($_post)
{
    return array(
        'widget_look_ahead' => $_post['widget_look_ahead'],
        'widget_max_results' => (int) $_post['widget_max_results']
    );
}

/**
 * Widget DISPLAY
 * @param $_widget array Page Widget info
 * @return string
 */
function jrBirthday_widget_birthdays_display($_widget)
{
    $now = time();
    $end = ($now + ($_widget['widget_look_ahead'] * 86400));
    $_dt = array();
    while ($now <= $end) {
        $day   = strftime('%m%d', $now);
        $_dt[] = "____{$day}";
        $now += 86400;
    }
    $_rt = array(
        'search'                 => array(
            'user_birthdate like ' . implode(" || user_birthdate like ", $_dt)
        ),
        'include_jrProfile_keys' => true,
        'order_by'               => false,
        'limit'                  => 500
    );
    $_rt = jrCore_db_search_items('jrUser', $_rt);
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
        $tmp = strftime('%Y');
        $now = strftime('%m%d');
        foreach ($_rt['_items'] as $k => $v) {
            $day = substr($v['user_birthdate'], 4);
            if (strpos($v['user_birthdate'], '0000') === 0) {
                $_rt['_items'][$k]['user_birthdate'] = $tmp . $day;
            }
            if ($day == $now) {
                $_rt['_items'][$k]['user_birthdate_today'] = 1;
            }
            else {
                $_rt['_items'][$k]['user_birthdate_today'] = 0;
            }
            $_rt['_items'][$k]['user_birthdate_epoch'] = mktime(12, 0, 0, substr($v['user_birthdate'], 4, 2), substr($v['user_birthdate'], 6));
        }
        $_od = array();
        $_tm = array();
        foreach ($_rt['_items'] as $k => $v) {
            $_od["{$v['_user_id']}"] = $v['user_birthdate'];
            $_tm["{$v['_user_id']}"] = $v;
        }
        asort($_od, SORT_NUMERIC);
        $_nw = array();
        foreach ($_od as $k => $v) {
            $_nw[] = $_tm[$k];
        }
        unset($_tm, $_od);
        $num = (isset($_widget['widget_max_results'])) ? intval($_widget['widget_max_results']) : 10;
        $_nw = array_slice($_nw, 0, $num);
        return jrCore_parse_template('widget_birthdays.tpl', array('_items' => $_nw), 'jrBirthday');
    }
    return jrCore_parse_template('widget_birthdays.tpl', array(), 'jrBirthday');
}
