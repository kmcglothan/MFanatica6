<?php
/**
 * Jamroom Simple Custom Forms module
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
function jrCustomForm_meta()
{
    $_tmp = array(
        'name'        => 'Simple Custom Forms',
        'url'         => 'form',
        'version'     => '1.2.4',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Create simple forms that store responses and optionally email admin users',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2885/simple-custom-forms',
        'category'    => 'forms',
        'requires'    => 'jrCore:6.0.4',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrCustomForm_init()
{
    // Register our custom CSS
    jrCore_register_module_feature('jrCore', 'css', 'jrCustomForm', 'jrCustomForm.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCustomForm', true);

    jrCore_register_module_feature('jrCore', 'tool_view', 'jrCustomForm', 'browse', array('Form Browser', 'Browse existing Custom Forms'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrCustomForm', 'create', array('Create a Custom Form', 'Create a new Custom Form'));

    // Custom tabs
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrCustomForm', 'browse', 'Form Browser');

    // Our default master view
    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrCustomForm', 'browse');

    // Maintain proper form response counts
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrCustomForm_verify_module_listener');
    jrCore_register_event_listener('jrCore', 'db_delete_item', 'jrCustomForm_db_delete_item_listener');
    jrCore_register_event_listener('jrCore', 'form_validate_init', 'jrCustomForm_form_validate_init_listener');

    // notifications
    $_tmp = array(
        'label' => 'new form response',
        'help'  => 'When a new form response has been received, how do you want to be notified?',
        'group' => 'admin'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrCustomForm', 'form_response', $_tmp);

    // Site Builder widget
    jrCore_register_module_feature('jrSiteBuilder', 'widget', 'jrCustomForm', 'widget_form', 'Embedded Form');

    return true;
}

//------------------------------------
// EVENT LISTENERS
//------------------------------------

/**
 * Make sure we're not using an internal form name
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrCustomForm_form_validate_init_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (isset($_post['_uri']) && strpos($_post['_uri'], '/form_designer_save/') && isset($_post['new_name'])) {
        switch ($_post['new_name']) {
            case 'form_id':
            case 'form_created':
            case 'form_updated':
            case 'form_name':
            case 'form_title':
            case 'form_message':
            case 'form_unique':
            case 'form_login':
            case 'form_notify':
            case 'form_responses':
                jrCore_set_form_notice('error', "<b>{$_post['new_name']}</b> is used internally - please use a different New Field Name", false);
                jrCore_form_result();
                break;
        }
    }
    return $_data;
}

/**
 * Maintain form response counts
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrCustomForm_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    // get all forms
    $tbl = jrCore_db_table_name('jrCustomForm', 'form');
    $req = "SELECT form_id, form_name FROM {$tbl}";
    $_rt = jrCore_db_query($req, 'form_id', false, 'form_name');
    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $fid => $fname) {
            $res = (int) jrCore_db_run_key_function('jrCustomForm', 'form_name', $fname, 'count');
            if (!$res || !jrCore_checktype($res, 'number_nz')) {
                $res = 0;
            }
            $req = "UPDATE {$tbl} SET form_responses = '{$res}' WHERE form_id = '{$fid}'";
            $cnt = jrCore_db_query($req, 'COUNT');
            if (!$cnt || $cnt === 0) {
                jrCore_logger('MAJ', "unable to update form_responses for form: {$fname} ({$fid})", array('req' => $req, 'cnt' => $cnt));
            }
        }
    }

    // Some fields are no longer needed...
    jrCore_db_delete_key_from_all_items('jrCustomForm', 'form_created');
    jrCore_db_delete_key_from_all_items('jrCustomForm', 'form_user_id');
    jrCore_db_delete_key_from_all_items('jrCustomForm', 'form_user_name');
    jrCore_db_delete_key_from_all_items('jrCustomForm', 'form_profile_name');

    return $_data;
}

/**
 * Decrement response counts
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrCustomForm_db_delete_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['module']) && $_args['module'] == 'jrCustomForm' && isset($_data['form_name']) && strlen($_data['form_name']) > 0) {
        // Decrement counts
        $tbl = jrCore_db_table_name('jrCustomForm', 'form');
        $req = "UPDATE {$tbl} SET form_responses = (form_responses - 1) WHERE form_name = '" . jrCore_db_escape($_data['form_name']) . "' AND form_responses > 0";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (!$cnt || $cnt === 0) {
            jrCore_logger('MAJ', "unable to decrement form_responses count for form: {$_data['form_name']}");
        }
    }
    return $_data;
}

//------------------------------------
// WIDGETS
//------------------------------------

/**
 * Display CONFIG screen for Embedded Form
 * @param $_post array Post info
 * @param $_user array User array
 * @param $_conf array Global Config
 * @param $_wg array Widget info
 * @return bool
 */
function jrCustomForm_widget_form_config($_post, $_user, $_conf, $_wg)
{
    // Get available forms
    $tbl  = jrCore_db_table_name('jrCustomForm', 'form');
    $req  = "SELECT form_name, form_title FROM {$tbl} ORDER BY form_title ASC";
    $_opt = jrCore_db_query($req, 'form_name', false, 'form_title');

    // Embed form
    $_tmp = array(
        'name'     => 'name',
        'label'    => 'Form to Embed',
        'help'     => 'Select the Form you would like to embed in this container location',
        'options'  => $_opt,
        'default'  => '',
        'type'     => 'select',
        'validate' => 'printable',
        'size'     => 8
    );
    jrCore_form_field_create($_tmp);
    return true;
}

/**
 * Get Widget results from posted Config data
 * @param $_post array Post info
 * @return array
 */
function jrCustomForm_widget_form_config_save($_post)
{
    return array('name' => $_post['name']);
}

/**
 * HTML Editor Widget DISPLAY
 * @param $_widget array Page Widget info
 * @return string
 */
function jrCustomForm_widget_form_display($_widget)
{
    return jrCore_parse_template('widget_embed_form.tpl', $_widget, 'jrCustomForm');
}

/**
 * Embed a Custom Form into a template
 * @param $params array Function params
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrCustomForm_embed_form($params, $smarty)
{
    global $_conf;
    if (!isset($params['name'])) {
        return jrCore_smarty_missing_error('name');
    }
    // Validate Form
    $tbl = jrCore_db_table_name('jrCustomForm', 'form');
    $req = "SELECT * FROM {$tbl} WHERE `form_name` = '" . jrCore_db_escape($params['name']) . "' LIMIT 1";
    $_fm = jrCore_db_query($req, 'SINGLE');
    if (!$_fm || !is_array($_fm)) {
        return jrCore_smarty_invalid_error('name');
    }
    if (isset($_fm['form_login']) && $_fm['form_login'] == 'on' && !jrUser_is_logged_in()) {
        $out = jrCore_parse_template('form_embed_login_notice.tpl', $_fm, 'jrCustomForm');
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], $out);
            return '';
        }
        return $out;
    }

    $_ln = jrUser_load_lang_strings();
    $btn = $_ln['jrCustomForm'][1];
    if (isset($params['submit_value']) && $params['submit_value'] != '1') {
        if (jrCore_checktype($params['submit_value'], 'number_nz')) {
            $btn = $_ln['jrCustomForm']["{$params['submit_value']}"];
        }
        else {
            $btn = $params['submit_value'];
        }
    }
    $dir = 'jrCustomForm';
    $tpl = 'form_embed.tpl';
    if (isset($params['template']) && strlen($params['template']) > 0) {
        $dir = $_conf['jrCore_active_skin'];
        $tpl = $params['template'];
    }

    // Form init (establish form session)
    $url = jrCore_get_module_url('jrCustomForm');
    $act = "{$_conf['jrCore_base_url']}/{$url}/{$params['name']}_save";
    $_fr = array(
        'module'      => 'jrCustomForm',
        'action'      => $act,
        'success_msg' => (isset($params['success'])) ? $params['success'] : $_ln['jrCustomForm'][2],
    );
    $tkn = jrCore_form_begin($params['name'], $act, $_fr);
    jrCore_form_create_session($tkn, $_fr);

    // Get fields
    $num = 0;
    $_fd = jrCore_get_designer_form_fields('jrCustomForm', $params['name']);
    if ($_fd && is_array($_fd)) {
        foreach ($_fd as $k => $_field) {
            if (!isset($_field['active']) || $_field['active'] != 1) {
                unset($_fd[$k]);
                continue;
            }
            $_field['form_designer'] = false;
            jrCore_form_field_create($_field, 'jrCustomForm');
            switch ($_field['type']) {
                case 'select':
                case 'radio':
                case 'multiple_select':
                case 'optionlist':
                    $_fd[$k]['options'] = json_decode($_field['options'], true);
                    break;
            }
            $num++;
        }
        // If the user is NOT logged on, make sure they are human
        if (!jrUser_is_logged_in()) {
            $_fd[$num] = array(
                'name'          => 'form_is_human',
                'label'         => $_ln['jrUser'][90],
                'help'          => $_ln['jrUser'][91],
                'type'          => 'checkbox_spambot',
                'error_msg'     => $_ln['jrUser'][92],
                'validate'      => 'onoff',
                'form_designer' => false
            );
            jrCore_form_field_create($_fd[$num]);
            if ($sb = jrCore_get_flag('jrcore_form_field_checkbox_spambot')) {
                $_fd[$num]['name']      = $sb;
                $_fd[$num]['tab_order'] = $num;
            }
        }
    }
    $_rp = array(
        'form'         => $_fm,
        'fields'       => $_fd,
        'params'       => $params,
        'form_token'   => $tkn,
        'submit_value' => $btn
    );
    $out = jrCore_parse_template($tpl, $_rp, $dir);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

//------------------------------------
// FUNCTIONS
//------------------------------------

/**
 * Get an email address from a form response
 * @param $_it array Response
 * @return bool|mixed
 */
function jrCustomForm_get_email_from_response($_it)
{
    if (!is_array($_it)) {
        return false;
    }
    foreach ($_it as $k => $v) {
        if (strpos($k, 'email') && jrCore_checktype($v, 'email')) {
            return $v;
        }
    }
    return false;
}

/**
 * Format a response item
 * @param $_it array Item from the form DS
 * @return bool|string
 */
function jrCustomForm_format_response($_it)
{
    global $_conf;
    if (!is_array($_it)) {
        return false;
    }
    ksort($_it);
    $url = jrCore_get_module_url('jrCustomForm');
    $_tm = array();
    $_rp = array("\n", "\r", "\n\r");
    $_fl = false;

    // First - process for any FILES
    foreach ($_it as $k => $v) {
        if (strpos($k, '_extension')) {
            if (!is_array($_fl)) {
                $_fl = array();
            }
            $fld       = trim(str_replace('_extension', '', $k));
            $_fl[$fld] = $fld;
        }
    }
    foreach ($_it as $k => $v) {
        if ($k == '_created') {
            $_tm[] = "<span class=\"ds_browser_key form_browser_key\">received:</span> <span class=\"ds_browser_value form_browser_value\">" . jrCore_format_time($v) . "</span>";
            continue;
        }
        elseif ($k == 'form_user_ip') {
            $_tm[] = "<span class=\"ds_browser_key form_browser_key\">user_ip:</span> <span class=\"ds_browser_value form_browser_value\"><a href=\"https://dnsquery.org/ipwhois/" . $v . "\" target=\"_blank\"><u>{$v}</u></a></span>";
            continue;
        }
        if (strpos($k, 'form_') !== 0) {
            continue;
        }
        switch ($k) {
            case 'form_name':
                continue 2;
                break;
        }
        if ($_fl) {
            foreach ($_fl as $fld) {
                if (strpos($k, "{$fld}_") === 0) {
                    if (!strpos($k, '_name')) {
                        continue 2;
                    }
                    $v = "<a href=\"{$_conf['jrCore_base_url']}/{$url}/download/{$fld}/{$_it['_item_id']}\"><u>{$v}</u></a> (" . jrCore_format_size($_it["{$fld}_size"]) . ')';
                }
            }
        }
        $st = true;
        if (strpos($v, '<a href') === 0) {
            $st = false;
        }
        elseif (isset($v) && is_array($v)) {
            $v  = json_encode($v);
            $st = false;
        }
        elseif (is_numeric($v) && strlen($v) === 10) {
            $v  = jrCore_format_time($v);
            $st = false;
        }
        if ($st) {
            $v = strip_tags(str_replace($_rp, ' ', $v));
        }
        $_tm[] = "<span class=\"ds_browser_key form_browser_key\">" . substr($k, 5) . ":</span> <span class=\"ds_browser_value form_browser_value\">{$v}</span>";
    }
    return implode('<br>', $_tm);
}

