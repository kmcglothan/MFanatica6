<?php
/**
 * Jamroom Banned Items module
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
function jrBanned_meta()
{
    $_tmp = array(
        'name'        => 'Banned Items',
        'url'         => 'banned',
        'version'     => '1.3.5',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Create, Update and Delete Banned names, words, email and IP addresses',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/1950/banned-items',
        'license'     => 'mpl',
        'requires'    => 'jrCore:6.1.6',
        'category'    => 'admin'
    );
    return $_tmp;
}

/**
 * init
 */
function jrBanned_init()
{
    // Check for banned IP addresses in process init
    jrCore_register_event_listener('jrCore', 'process_init', 'jrBanned_process_init_listener');
    jrCore_register_event_listener('jrCore', 'module_view', 'jrBanned_module_view_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrBanned_verify_module_listener');

    // Block banned word searches
    jrCore_register_event_listener('jrSearch', 'search_fields', 'jrBanned_search_fields_listener');

    // Tool to create, update and delete banned Items
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrBanned', 'browse', array('Banned Items', 'Create, Update and Delete Banned names, words, email and IP addresses'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrBanned', 'test', array('Test Item', 'Test a value to see if it passes or fails the existing banned items'));

    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrBanned', 'browse', 'Banned Items');
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrBanned', 'test', 'Test Items');

    // Our default master view
    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrBanned', 'browse');

    // System resets
    jrCore_register_event_listener('jrDeveloper', 'reset_system', 'jrBanned_reset_system_listener');

    return true;
}

//---------------------
// EVENT LISTENERS
//---------------------

/**
 * Cleanup schema on system reset
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrBanned_reset_system_listener($_data, $_user, $_conf, $_args, $event)
{
    $tbl = jrCore_db_table_name('jrBanned', 'banned');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");
    jrCore_db_query("OPTIMIZE TABLE {$tbl}");
    return $_data;
}

/**
 * Check for banned IPs
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrBanned_process_init_listener($_data, $_user, $_conf, $_args, $event)
{
    // Make sure this is NOT a banned IP
    if (jrCore_is_view_request()) {
        $ip = jrCore_get_ip();
        if (jrBanned_is_banned('ip', $ip)) {
            header('HTTP/1.0 403 Forbidden');
            jrCore_notice('error', 'You do not have permission to access this server');
            exit;
        }
    }
    return $_data;
}

/**
 * Check for banned words in form submissions
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrBanned_module_view_listener($_data, $_user, $_conf, $_args, $event)
{
    // Watch for form submissions
    if (!jrUser_is_admin() && isset($_data['module']) && $_data['module'] == 'jrCore' && isset($_data['option']) && $_data['option'] == 'form_validate' && jrCore_is_ajax_request()) {
        if (isset($_data['jr_html_form_token'])) {
            if ($_rt = jrCore_form_get_session($_data['jr_html_form_token'])) {
                // Scan for incoming text fields (text, textarea) and check for banned words
                if (isset($_rt['form_fields'])) {
                    foreach ($_rt['form_fields'] as $_field) {

                        if (isset($_field['ban_check'])) {
                            if ($_field['ban_check'] === false) {
                                // Purposefully disabled on this field
                                continue;
                            }
                        }

                        $key  = false;
                        $type = false;
                        switch ($_field['type']) {
                            case 'editor':
                                $key  = "{$_field['name']}_editor_contents";
                                $type = 'word';
                                break;
                            case 'select_and_text':
                                $key  = "{$_field['name']}_text";
                                $type = 'word';
                                break;
                            case 'text':
                                $key = $_field['name'];
                                if (strpos($key, '_name')) {
                                    $type = 'name';
                                }
                                elseif (strpos($key, '_email')) {
                                    $type = 'email';
                                }
                                else {
                                    $type = 'word';
                                }
                                break;
                            case 'textarea':
                                $key  = $_field['name'];
                                $type = 'word';
                                break;
                        }
                        if ($key && $type && isset($_data[$key]) && strlen($_data[$key]) > 0) {
                            if ($bad = jrBanned_is_banned($type, $_data[$key])) {
                                $_ln = jrUser_load_lang_strings();
                                jrCore_set_form_notice('error', "{$_ln['jrBanned'][1]}&quot;{$bad}&quot;");
                                jrCore_form_field_hilight($key);
                                jrCore_form_result();
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
 * Migrate from jrCore to jrBanned
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrBanned_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    // See if we have migrated
    if (jrCore_db_table_exists('jrCore', 'banned')) {
        // We have not migrated yet
        $tb1 = jrCore_db_table_name('jrCore', 'banned');
        $tb2 = jrCore_db_table_name('jrBanned', 'banned');
        $req = "INSERT INTO {$tb2} SELECT * FROM {$tb1}";
        jrCore_db_query($req);
        $req = "DROP TABLE {$tb1}";
        jrCore_db_query($req);
    }
    return $_data;
}

/**
 * Block banned word searches
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrBanned_search_fields_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (!jrUser_is_admin() && isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_tm = explode(' ', $_post['search_string']);
        if ($_tm && is_array($_tm)) {
            foreach ($_tm as $word) {
                if (jrBanned_is_banned('word', $word)) {
                    // This is a banned word - short circuit our match
                    return array(
                        'jrBanned' => array(
                            'no_match' => 1
                        )
                    );
                }
            }
        }
    }
    return $_data;
}

//---------------------
// FUNCTIONS
//---------------------

/**
 * Get current banned config for a given ban type
 * @param $type string
 * @return bool|mixed|string
 */
function jrBanned_get_banned_config($type)
{
    $key = "jrbanned_config_{$type}";
    if (!$_rt = jrCore_get_flag($key)) {
        if (!$_rt = jrCore_get_local_cache_key($key)) {
            if (!$_rt = jrCore_is_cached('jrBanned', $key, false, false)) {
                $tbl = jrCore_db_table_name('jrBanned', 'banned');
                $req = "SELECT ban_id AS i, ban_value AS v FROM {$tbl} WHERE ban_type = '" . jrCore_db_escape($type) . "'";
                $_rt = jrCore_db_query($req, 'i', false, 'v');
                if (!$_rt || !is_array($_rt)) {
                    $_rt = 'no_items';
                }
                jrCore_set_flag($key, $_rt);
                jrCore_set_local_cache_key($key, $_rt);
                jrCore_add_to_cache('jrBanned', $key, $_rt, 0, 0, false, false);
            }
            else {
                jrCore_set_flag($key, $_rt);
                jrCore_set_local_cache_key($key, $_rt);
            }
        }
        else {
            jrCore_set_flag($key, $_rt);
        }
    }
    if (!$_rt || !is_array($_rt) || count($_rt) === 0) {
        // No items of this type
        return false;
    }
    return $_rt;
}

/**
 * Test if a given value for a type is a banned item
 * @param string $type Type of Banned Item
 * @param string $value Value to check
 * @return bool
 */
function jrBanned_is_banned($type, $value = null)
{
    if (mb_strlen($value) > 1) {
        if (!$_rt = jrBanned_get_banned_config($type)) {
            // No items of this type
            return false;
        }
        $value = trim(strip_tags($value));
        switch ($type) {

            case 'ip':
                foreach ($_rt as $i => $v) {
                    if (strpos($value, $v) === 0) {
                        return $v;
                    }
                }
                break;

            case 'word':
            case 'name':
            case 'email':
                foreach ($_rt as $i => $v) {
                    if ($v == $value || preg_match('/\b' . preg_quote($v) . '\b/ui', " {$value} ")) {
                        return $v;
                    }
                }
                break;

            default:
                // Do we have other registered types?
                $_mf = jrCore_get_registered_module_features('jrBanned', 'banned_type');
                if ($_mf && is_array($_mf)) {
                    foreach ($_mf as $mod => $_inf) {
                        if (isset($_inf[$type]['function']) && function_exists($_inf[$type]['function'])) {
                            $func = $_inf[$type]['function'];
                            if ($tmp = $func($value, $_rt)) {
                                return $tmp;
                            }
                        }
                    }
                }
                break;
        }
    }
    return false;
}

/**
 * Check if a given type is a valid banned type
 * @param $type string
 * @return bool
 */
function jrBanned_is_valid_ban_type($type)
{
    $_tmp = jrBanned_get_banned_types();
    return (isset($_tmp[$type])) ? true : false;
}

/**
 * Get all banned types
 * @return array
 */
function jrBanned_get_banned_types()
{
    global $_mods;
    // Built ins
    $_opt = array(
        'ip'    => 'IP Address',
        'name'  => 'Profile or User Name',
        'email' => 'Email Address or Domain',
        'word'  => 'Forbidden Word'
    );
    $_mf  = jrCore_get_registered_module_features('jrBanned', 'banned_type');
    if ($_mf && is_array($_mf)) {
        foreach ($_mf as $mod => $_tmp) {
            foreach ($_tmp as $type => $_inf) {
                if (isset($_inf['function']) && function_exists($_inf['function'])) {
                    $_opt[$type] = $_inf['title'] . ' (' . $_mods[$mod]['module_name'] . ')';
                }
            }
        }
    }
    return $_opt;
}
